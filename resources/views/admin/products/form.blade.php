<div>
    <label for="name" class="block text-sm font-medium text-gray-700">Product name</label>
    <input id="name" name="name" value="{{ old('name', $product?->name) }}" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
    <input id="sku" name="sku" value="{{ old('sku', $product?->sku) }}" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('sku') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
        <input id="price" name="price" type="number" step="0.01" min="0.01" value="{{ old('price', $product?->price) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
        <input id="stock" name="stock" type="number" step="1" min="0" value="{{ old('stock', $product?->stock ?? 0) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('stock') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
