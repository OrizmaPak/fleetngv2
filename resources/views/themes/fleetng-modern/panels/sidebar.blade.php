@php
    $role = (string) session('user_role');
    $currentUser = Auth::user();
    $dashboardUrl = $role === '1' ? route('super-dashboard-analytics') : route('dashboard-analytics');
    if ($currentUser && $currentUser->is_payment_user) $dashboardUrl = route('trip-list');
@endphp
<aside class="main-menu menu-fixed menu-light" aria-label="Primary navigation">
    <div class="fleetng-sidebar-head">
        <a class="navbar-brand" href="{{ $dashboardUrl }}" aria-label="FleetNG dashboard">
            <img src="{{ asset('images/logo/fleetng-logo.svg') }}" alt="FleetNG">
            <span>FLEETNG</span>
        </a>
        <button type="button" class="fleetng-icon-button fleetng-nav-collapse" aria-label="Collapse navigation" aria-expanded="true">
            <i data-feather="chevrons-left"></i>
        </button>
    </div>

    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation">
            @foreach($menuData[0]->menu ?? [] as $menu)
                @continue(!in_array($role, $menu->role ?? [], true))
                @continue($currentUser && $currentUser->is_payment_user && empty($menu->payment_user))

                @if(isset($menu->navheader))
                    <li class="navigation-header"><span>{{ __('locale.' . $menu->navheader) }}</span></li>
                @else
                    @php
                        $menuUrl = isset($menu->url) && $menu->url !== '' ? $menu->url : null;
                        $active = $menuUrl && request()->is(ltrim($menuUrl, '/') . '*');
                    @endphp
                    <li class="nav-item {{ $active ? 'active' : '' }} {{ $menu->classlist ?? '' }}" id="{{ $menu->id ?? '' }}">
                        <a href="{{ $menuUrl ? url($menuUrl) : 'javascript:void(0)' }}"
                           class="d-flex align-items-center"
                           @if(!$menuUrl) aria-expanded="false" @endif>
                            <i data-feather="{{ $menu->icon ?? 'circle' }}"></i>
                            <span class="menu-title">{{ __('locale.' . $menu->name) }}</span>
                            @if(isset($menu->badge))<span class="badge badge-light-primary ml-auto">{{ $menu->badge }}</span>@endif
                            @if(isset($menu->submenu))<i class="fleetng-submenu-chevron" data-feather="chevron-down"></i>@endif
                        </a>
                        @if(isset($menu->submenu))
                            @include('panels.submenu', ['menu' => $menu->submenu])
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

    <div class="fleetng-sidebar-foot">
        <span class="fleetng-status-dot" aria-hidden="true"></span>
        <span class="fleetng-sidebar-foot-copy">Secure operations portal</span>
    </div>
</aside>
