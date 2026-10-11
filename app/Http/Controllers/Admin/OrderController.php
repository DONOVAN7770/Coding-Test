<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => $request->filled('start_date')
                ? ['nullable', 'date', 'after_or_equal:start_date']
                : ['nullable', 'date'],
        ]);

        $orders = Order::query()
            ->when($validated['start_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($validated['end_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'validated'));
    }
}
