<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-bold text-purple-950">Customer shop</h2>
                <p class="mt-1 text-sm text-gray-600">Welcome, {{ auth()->user()->name }}.</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-[#8eafb1]">
        <div class="grid w-full gap-6 px-4 py-8 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <section class="w-full bg-[#8eafb1] px-1 sm:px-0">
                <div class="mb-5 px-1"><h3 class="text-sm font-medium text-gray-900">Available products</h3></div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($products as $product)
                        <article class="flex flex-col rounded-xl border border-gray-200 bg-white p-3 shadow-md transition hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0"><h4 class="truncate text-base font-medium text-purple-950">{{ $product->name }}</h4><p class="mt-1 text-xs text-gray-600">SKU: {{ $product->sku }}</p></div>
                                    <span class="whitespace-nowrap rounded-full bg-[#a98d3d] px-2.5 py-1 text-sm font-bold text-white">{{ number_format((float) $product->price, 2) }}</span>
                                </div>
                                <p class="mt-3 text-xs text-[#99813c]"><span class="font-medium">{{ $product->stock }}</span> available</p>
                            </div>
                            <form method="POST" action="{{ route('customer.orders.store') }}" class="order-form mt-2" data-product-name="{{ $product->name }}" data-unit-price="{{ $product->price }}">
                                @csrf
                                <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">
                                <input type="hidden" name="customer_email" value="{{ auth()->user()->email }}">
                                <input type="hidden" name="items[0][product_id]" value="{{ $product->id }}">
                                <div class="flex items-end gap-2">
                                <label class="block min-w-0 flex-1 text-xs font-medium text-gray-800">Quantity
                                    <div class="mt-0.5 flex h-9 overflow-hidden rounded-full border border-[#9eb9bb] bg-[#e6eeee]">
                                        <button type="button" class="quantity-step px-3 text-lg text-gray-700 hover:bg-[#d5e2e2]" data-step="-1" aria-label="Decrease quantity">−</button>
                                        <input type="number" name="items[0][quantity]" min="1" max="{{ min(10000, $product->stock) }}" value="1" required class="order-quantity w-full border-0 bg-transparent p-0 text-center text-sm focus:ring-0">
                                        <button type="button" class="quantity-step px-3 text-lg text-gray-700 hover:bg-[#d5e2e2]" data-step="1" aria-label="Increase quantity">+</button>
                                    </div>
                                </label>
                                <button class="h-[52px] shrink-0 rounded-xl bg-purple-900 px-3 text-xs font-semibold text-white hover:bg-purple-800">Place order</button>
                                </div>
                                <p class="mt-1 text-right text-xs text-[#99813c]">Subtotal: <span class="order-subtotal font-medium text-gray-900">{{ number_format((float) $product->price, 2) }}</span></p>
                            </form>
                        </article>
                    @empty
                        <p class="text-sm text-gray-500">There are no products in stock right now.</p>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b px-6 py-4"><h3 class="font-semibold">Your recent orders</h3></div>
                <div class="overflow-x-auto p-6">
                    @if ($orders->isEmpty())
                        <p class="text-sm text-gray-500">You have not placed any orders yet.</p>
                    @else
                        <table class="min-w-full text-left text-sm">
                            <thead class="text-gray-500"><tr><th class="pb-3 pr-4">Order</th><th class="pb-3 pr-4">Items</th><th class="pb-3 pr-4">Total</th><th class="pb-3 pr-4">Status</th><th class="pb-3">Placed</th></tr></thead>
                            <tbody class="divide-y">
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="py-3 pr-4 font-medium">{{ $order->order_number }}</td>
                                        <td class="py-3 pr-4">{{ $order->orderItems->map(fn ($item) => ($item->product?->name ?? 'Deleted product').' × '.$item->quantity)->join(', ') }}</td>
                                        <td class="py-3 pr-4">{{ number_format((float) $order->total_amount, 2) }}</td>
                                        <td class="py-3 pr-4">{{ ucfirst($order->status) }}</td>
                                        <td class="py-3">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </section>
        </div>
    </div>
<script>
    document.querySelectorAll('.order-form').forEach((form) => {
        const quantity = form.querySelector('.order-quantity');
        const subtotal = form.querySelector('.order-subtotal');
        const unitPrice = Number(form.dataset.unitPrice);

        form.querySelectorAll('.quantity-step').forEach((button) => {
            button.addEventListener('click', () => {
                const min = Number(quantity.min) || 1;
                const max = Number(quantity.max) || 10000;
                quantity.value = Math.min(max, Math.max(min, Number(quantity.value || min) + Number(button.dataset.step)));
                quantity.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });

        quantity.addEventListener('input', () => {
            const count = Math.max(0, Number(quantity.value) || 0);
            subtotal.textContent = (unitPrice * count).toFixed(2);
        });

        form.addEventListener('submit', (event) => {
            const count = Number(quantity.value);
            const total = (unitPrice * count).toFixed(2);
            const message = `Confirm order?\n\nProduct: ${form.dataset.productName}\nQuantity: ${count}\nTotal: ${total}`;

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
</script>
</x-app-layout>
