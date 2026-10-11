<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $orders = $request->user()
            ->orders()
            ->with('orderItems.product')
            ->latest()
            ->limit(10)
            ->get();

        return view('customer.shop', compact('products', 'orders'));
    }
}
