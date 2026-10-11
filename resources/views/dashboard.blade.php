<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Admin Dashboard</h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.products.index') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Manage products</a>
                <a href="{{ route('admin.orders.index') }}" class="rounded-md border px-4 py-2 text-sm font-semibold text-gray-700">View orders</a>
            </div>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <section class="rounded-lg bg-white p-6 shadow"><p class="text-sm text-gray-500">Total sales revenue</p><p class="mt-2 text-3xl font-bold">{{ number_format((float) $totalRevenue, 2) }}</p></section>
                <section class="rounded-lg bg-white p-6 shadow"><p class="text-sm text-gray-500">Orders today</p><p class="mt-2 text-3xl font-bold">{{ $todayOrders }}</p></section>
                <section class="rounded-lg bg-white p-6 shadow"><p class="text-sm text-gray-500">Low stock products</p><p class="mt-2 text-3xl font-bold {{ $lowStockProducts->isNotEmpty() ? 'text-red-600' : '' }}">{{ $lowStockProducts->count() }}</p><p class="mt-1 text-xs text-gray-500">Fewer than 5 units in stock</p></section>
            </div>
            <section class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b px-6 py-4"><h3 class="font-semibold">Low stock alert</h3></div>
                <div class="overflow-x-auto p-6">
                    @if ($lowStockProducts->isEmpty()) <p class="text-sm text-gray-500">No low-stock products.</p>
                    @else
                        <table class="min-w-full text-left text-sm"><thead class="text-gray-500"><tr><th class="pb-3 pr-4">Product</th><th class="pb-3 pr-4">SKU</th><th class="pb-3">Stock</th></tr></thead><tbody class="divide-y">
                            @foreach ($lowStockProducts as $product)<tr><td class="py-3 pr-4">{{ $product->name }}</td><td class="py-3 pr-4">{{ $product->sku }}</td><td class="py-3 font-semibold text-red-600">{{ $product->stock }}</td></tr>@endforeach
                        </tbody></table>
                    @endif
                </div>
            </section>
            <section class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b px-6 py-4"><h3 class="font-semibold">Recent orders</h3></div>
                <div class="overflow-x-auto p-6">
                    @if ($recentOrders->isEmpty()) <p class="text-sm text-gray-500">No orders submitted yet.</p>
                    @else
                        <table class="min-w-full text-left text-sm"><thead class="text-gray-500"><tr><th class="pb-3 pr-4">Order</th><th class="pb-3 pr-4">Customer</th><th class="pb-3 pr-4">Total</th><th class="pb-3 pr-4">Status</th><th class="pb-3">Created</th></tr></thead><tbody class="divide-y">
                            @foreach ($recentOrders as $order)<tr><td class="py-3 pr-4">{{ $order->order_number }}</td><td class="py-3 pr-4">{{ $order->customer_name }}</td><td class="py-3 pr-4">{{ number_format((float) $order->total_amount, 2) }}</td><td class="py-3 pr-4">{{ ucfirst($order->status) }}</td><td class="py-3">{{ $order->created_at->format('Y-m-d H:i') }}</td></tr>@endforeach
                        </tbody></table>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
