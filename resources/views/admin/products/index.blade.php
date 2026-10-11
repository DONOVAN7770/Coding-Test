<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Products</h2>
            <a href="{{ route('admin.products.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Add product</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))<div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>@endif
            @if ($errors->has('file'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-semibold">Import failed. No products were saved.</p>
                    <ul class="mt-2 list-inside list-disc">@foreach ($errors->get('file') as $message)<li>{{ $message }}</li>@endforeach</ul>
                </div>
            @endif
            <section class="rounded-lg bg-white p-6 shadow">
                <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                    @csrf
                    <div class="flex-1">
                        <input id="file" name="file" type="file" accept=".xlsx,.csv" required class="mt-1 block w-full rounded-md border border-gray-600 text-sm shadow-sm">
                    </div>
                    <button class="rounded-md border px-4 py-2 text-sm font-semibold text-gray-700">Import file</button>
                </form>
            </section>
            <section class="overflow-hidden rounded-lg bg-white shadow">
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col gap-3 border-b p-4 sm:flex-row">
                    <label for="search" class="sr-only">Search products</label>
                    <input id="search" name="search" value="{{ $search }}" placeholder="Search by name or SKU" class="w-full rounded-md border-gray-300 text-sm shadow-sm sm:max-w-md">
                    <button class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white">Search</button>
                    @if ($search !== '')<a href="{{ route('admin.products.index') }}" class="rounded-md border px-4 py-2 text-center text-sm">Clear</a>@endif
                </form>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500"><tr><th class="px-6 py-3">Product</th><th class="px-6 py-3">SKU</th><th class="px-6 py-3">Price</th><th class="px-6 py-3">Stock</th><th class="px-6 py-3 text-right">Actions</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($products as $product)
                                <tr>
                                    <td class="px-6 py-4 font-medium">{{ $product->name }}</td><td class="px-6 py-4">{{ $product->sku }}</td>
                                    <td class="px-6 py-4">{{ number_format((float) $product->price, 2) }}</td>
                                    <td class="px-6 py-4 {{ $product->stock < 5 ? 'font-semibold text-red-600' : '' }}">{{ $product->stock }}</td>
                                    <td class="px-6 py-4 text-right"><div class="flex justify-end gap-3">
                                        <a class="font-medium text-indigo-600" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Move this product to the recycle bin?')">
                                            @csrf @method('DELETE')<button class="font-medium text-red-600">Delete</button>
                                        </form>
                                    </div></td>
                                </tr>
                            @empty<tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">No products found.</td></tr>@endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $products->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
