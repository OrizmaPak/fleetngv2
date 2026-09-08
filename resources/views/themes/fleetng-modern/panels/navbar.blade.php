@php
    $currentUser = Auth::user();
    $initials = '';
    foreach (array_slice(array_filter(explode(' ', trim($currentUser->full_name ?? 'FleetNG User'))), 0, 2) as $namePart) {
        $initials .= strtoupper(substr($namePart, 0, 1));
    }
@endphp
<nav class="header-navbar navbar navbar-expand-lg align-items-center" aria-label="Utility navigation">
    <div class="navbar-container d-flex align-items-center content">
        <button type="button" class="fleetng-icon-button menu-toggle d-xl-none" aria-label="Open navigation">
            <i data-feather="menu"></i>
        </button>

        <div class="fleetng-global-search" data-search-url="{{ route('ui-search') }}">
            <label class="sr-only" for="fleetng-global-search-input">Search drivers, clients and trips</label>
            <i data-feather="search" aria-hidden="true"></i>
            <input id="fleetng-global-search-input" type="search" autocomplete="off" placeholder="Search drivers, clients and trips" aria-controls="fleetng-search-results" aria-expanded="false">
            <kbd>Ctrl K</kbd>
            <div id="fleetng-search-results" class="fleetng-search-results" role="listbox" hidden></div>
        </div>

        <div class="fleetng-navbar-actions ml-auto">
            <button type="button" class="fleetng-icon-button fleetng-theme-toggle" aria-label="Switch colour theme" title="Switch colour theme">
                <i class="theme-icon-light" data-feather="sun"></i>
                <i class="theme-icon-dark" data-feather="moon"></i>
            </button>

            <div class="dropdown dropdown-user">
                <button class="fleetng-user-trigger dropdown-toggle" type="button" id="fleetng-user-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="fleetng-user-avatar">{{ $initials ?: 'FU' }}</span>
                    <span class="fleetng-user-copy">
                        <strong>{{ $currentUser->full_name ?? 'FleetNG User' }}</strong>
                        <small>Online</small>
                    </span>
                    <i data-feather="chevron-down"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="fleetng-user-menu">
                    <a class="dropdown-item" href="{{ session('user_role') == 1 ? route('superadmin-page-account-settings') : route('page-account-settings') }}">
                        <i data-feather="settings"></i> Account settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                        <i data-feather="log-out"></i> Sign out
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
