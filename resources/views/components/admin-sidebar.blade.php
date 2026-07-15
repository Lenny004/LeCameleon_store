@php
    $navItems = [
        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route' => 'admin.products.index', 'label' => 'Products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['route' => 'admin.inventory.index', 'label' => 'Inventory', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
        ['route' => 'admin.orders.index', 'label' => 'Orders', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ['route' => 'admin.return-requests.index', 'label' => 'Returns', 'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6'],
        ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197'],
        ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z'],
        ['route' => 'admin.brands.index', 'label' => 'Brands', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z'],
        ['route' => 'admin.coupons.index', 'label' => 'Coupons', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
        ['route' => 'admin.reviews.index', 'label' => 'Reviews', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
        ['route' => 'admin.analytics.index', 'label' => 'Analytics', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ['route' => 'admin.activity.index', 'label' => 'Activity', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['route' => 'admin.settings.index', 'label' => 'Settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
    ];
@endphp

<aside class="admin-sidebar" :class="{ 'admin-sidebar--open': open }">
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="admin-sidebar__brand">
        Le <span>Cameleon</span>
    </a>

    <nav class="admin-sidebar__nav" aria-label="Admin navigation">
        <div class="admin-sidebar__section">
            <p class="admin-sidebar__label">Main</p>
            @foreach (array_slice($navItems, 0, 6) as $item)
                @if (Route::has($item['route']))
                    <a href="{{ route($item['route']) }}" class="admin-sidebar__link {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'admin-sidebar__link--active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"/></svg>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </div>
        <div class="admin-sidebar__section">
            <p class="admin-sidebar__label">Catalog</p>
            @foreach (array_slice($navItems, 6, 4) as $item)
                @if (Route::has($item['route']))
                    <a href="{{ route($item['route']) }}" class="admin-sidebar__link {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'admin-sidebar__link--active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"/></svg>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </div>
        <div class="admin-sidebar__section">
            <p class="admin-sidebar__label">System</p>
            @foreach (array_slice($navItems, 10) as $item)
                @if (Route::has($item['route']))
                    <a href="{{ route($item['route']) }}" class="admin-sidebar__link {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'admin-sidebar__link--active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"/></svg>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </nav>

    <div class="admin-sidebar__footer">
        @if (Route::has('home'))
            <a href="{{ route('home') }}" class="admin-sidebar__link">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Store
            </a>
        @endif
    </div>
</aside>
