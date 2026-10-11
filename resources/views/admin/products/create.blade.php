<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Add product</h2></x-slot>
    <div class="py-8"><div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8"><div class="rounded-lg bg-white p-6 shadow">
        <form method="POST" action="{{ route('admin.products.store') }}" class="grid gap-5">
            @csrf
            @include('admin.products.form', ['product' => null])
            <div class="flex gap-3"><button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Create product</button><a href="{{ route('admin.products.index') }}" class="rounded-md border px-4 py-2 text-sm">Cancel</a></div>
        </form>
    </div></div></div>
</x-app-layout>
