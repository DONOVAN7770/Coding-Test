<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalRevenue = Order::where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $todayOrders = Order::whereDate('created_at', today())
            ->count();

        $lowStockProducts = Product::where('stock', '<', 5)
            ->orderBy('stock')
            ->get();

        $recentOrders = Order::latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalRevenue',
            'todayOrders',
            'lowStockProducts',
            'recentOrders'
        ));
    }
}