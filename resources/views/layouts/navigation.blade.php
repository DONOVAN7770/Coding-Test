<nav x-data="{ open: false }" class="border-b border-gray-100 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            @if (Auth::user()->role === 'admin')
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="text-lg font-bold text-indigo-700">Shop Admin</a>
                    <div class="hidden items-center gap-6 sm:flex">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Admin Dashboard</x-nav-link>
                        <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">Products</x-nav-link>
                        <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">Orders</x-nav-link>
                    </div>
                </div>
            @else
                <div></div>
            @endif
            <div class="hidden items-center gap-4 sm:flex">
                <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ Auth::user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="text-sm text-gray-600 hover:text-gray-900">Log out</button>
                </form>
            </div>
            <button @click="open = !open" class="inline-flex items-center rounded-md p-2 text-gray-500 sm:hidden" aria-label="Toggle account menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <div x-show="open" class="space-y-1 border-t border-gray-100 px-4 py-3 sm:hidden">
        @if (Auth::user()->role === 'admin')
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Admin Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">Products</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">Orders</x-responsive-nav-link>
        @endif
        <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="block w-full px-4 py-2 text-left text-sm text-gray-700">Log out</button>
        </form>
    </div>
</nav>
