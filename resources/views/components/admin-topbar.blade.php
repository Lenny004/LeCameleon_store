<header class="admin-topbar">
    <div class="admin-topbar__start">
        <button type="button" class="admin-topbar__menu-toggle" @click="toggle()" aria-label="Toggle sidebar">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div class="admin-topbar__heading">
            <h1 class="admin-topbar__title">@yield('page-title', 'Dashboard')</h1>
            @hasSection('page-subtitle')
                <p class="admin-topbar__subtitle">@yield('page-subtitle')</p>
            @endif
        </div>
    </div>

    <div class="admin-topbar__actions">
        @include('components.theme-toggle')
        @auth
            <span class="admin-topbar__user text-small text-muted">{{ auth()->user()->name ?? auth()->user()->email }}</span>
            @if (Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn--ghost btn--sm">Logout</button>
                </form>
            @endif
        @endauth
    </div>
</header>
