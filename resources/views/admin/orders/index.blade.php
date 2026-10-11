<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Orders</h2>
            <a href="{{ route('admin.orders.export', request()->only('start_date', 'end_date')) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Export Excel</a>
        </div>
    </x-slot>
    <div class="py-8"><div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col gap-3 rounded-lg bg-white p-4 shadow sm:flex-row sm:items-end">
            <div><label for="start_date" class="block text-sm font-medium">Start date</label><input id="start_date" type="date" name="start_date" value="{{ $validated['start_date'] ?? '' }}" class="mt-1 rounded-md border-gray-300 text-sm"></div>
            <div><label for="end_date" class="block text-sm font-medium">End date</label><input id="end_date" type="date" name="end_date" value="{{ $validated['end_date'] ?? '' }}" class="mt-1 rounded-md border-gray-300 text-sm"></div>
            <button class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white">Filter</button><a href="{{ route('admin.orders.index') }}" class="rounded-md border px-4 py-2 text-center text-sm">Clear</a>
        </form>
        @if ($errors->any())<div class="rounded-md bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <section class="overflow-hidden rounded-lg bg-white shadow"><div class="overflow-x-auto">
            <table class="min-w-full divide-y text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500"><tr><th class="px-6 py-3">Order</th><th class="px-6 py-3">Customer</th><th class="px-6 py-3">Email</th><th class="px-6 py-3">Total</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Created</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($orders as $order)
                        <tr><td class="px-6 py-4 font-medium">{{ $order->order_number }}</td><td class="px-6 py-4">{{ $order->customer_name }}</td><td class="px-6 py-4">{{ $order->customer_email }}</td><td class="px-6 py-4">{{ number_format((float) $order->total_amount, 2) }}</td><td class="px-6 py-4">{{ ucfirst($order->status) }}</td><td class="px-6 py-4">{{ $order->created_at->format('Y-m-d H:i') }}</td></tr>
                    @empty<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">No orders found for this date range.</td></tr>@endforelse
                </tbody>
            </table>
        </div><div class="p-4">{{ $orders->links() }}</div></section>
    </div></div>
</x-app-layout>
