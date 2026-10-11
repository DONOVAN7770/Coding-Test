<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class OrderSubmissionController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $userId = $request->user()?->id;

        $order = DB::transaction(function () use ($validated, $userId): Order {
            $items = collect($validated['items'])->keyBy('product_id');
            $products = Product::query()
                ->whereIn('id', $items->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $items->count()) {
                throw ValidationException::withMessages([
                    'items' => 'One or more products do not exist or are no longer available.',
                ]);
            }

            foreach ($items as $productId => $item) {
                if ($products[$productId]->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "There is not enough stock for product SKU {$products[$productId]->sku}.",
                    ]);
                }
            }

            $totalInCents = 0;
            foreach ($items as $productId => $item) {
                $totalInCents += (float) $products[$productId]->price * $item['quantity'];
            }

            $order = Order::create([
                'order_number' => 'ORD-'.Str::upper(Str::random(8)),
                'user_id' => $userId,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'total_amount' => number_format($totalAmount, 2, '.', ''),
                'status' => 'pending',
            ]);

            foreach ($items as $productId => $item) {
                $product = $products[$productId];
                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
                $product->decrement('stock', $item['quantity']);
            }

            return $order->load('orderItems.product');
        });

        if (! $request->expectsJson()) {
            return redirect()->route('customer.shop')
                ->with('status', 'Order '.$order->order_number.' was placed successfully.');
        }

        return response()->json([
            'message' => 'Order submitted successfully.',
            'data' => $order,
        ], 201);
    }
}
