@php
    $configData = Helper::applClasses();
@endphp
<div class="main-menu menu-fixed {{ $configData['theme'] === 'dark' ? 'menu-dark' : 'menu-light' }} menu-accordion menu-shadow"
    data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav justify-content-center flex-row">
            <li class="nav-item">
                <a class="navbar-brand mt-1" href="{{ route('dashboard-analytics') }}">
                    <span class="brand-logo">
                        <img src="{{ asset('images/logo/fleetng-logo.svg') }}" alt="FleetNG Logo" width="70"
                            style="max-width:100%;">
                    </span>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc"
                        data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content" style="margin-top:30px;">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            {{-- Foreach menu item starts --}}
            @if (isset($menuData[0]))
                @foreach ($menuData[0]->menu as $menu)
                    @if (isset($menu->navheader))
                        @if (in_array(session('user_role'), $menu->role))
                            @if (Auth::user()->is_payment_user)
                                @if ($menu->payment_user)
                                    <li class="navigation-header">
                                        <span>{{ __('locale.' . $menu->navheader) }}</span>
                                        <i data-feather="more-horizontal"></i>
                                    </li>
                                @endif
                            @else
                                <li class="navigation-header">
                                    <span>{{ __('locale.' . $menu->navheader) }}</span>
                                    <i data-feather="more-horizontal"></i>
                                </li>
                            @endif
                        @endif
                    @else
                        {{-- Add Custom Class with nav-item --}}
                        @php
                            $custom_classes = '';
                            if (isset($menu->classlist)) {
                                $custom_classes = $menu->classlist;
                            }
                            $custom_id = '';
                            if (isset($menu->id)) {
                                $custom_id = $menu->id;
                            }
                        @endphp
                        @if (in_array(session('user_role'), $menu->role))
                            @if (Auth::user()->is_payment_user)
                                @if ($menu->payment_user)
                                    <li class="nav-item {{ Route::current()->uri() === $menu->url ? 'active' : '' }} {{ $custom_classes }}"
                                        id="{{ $custom_id }}">
                                        <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0)' }}"
                                            class="d-flex align-items-center"
                                            target="{{ isset($menu->newTab) ? '_blank' : '_self' }}">
                                            <i data-feather="{{ $menu->icon }}"></i>
                                            <span
                                                class="menu-title text-truncate">{{ __('locale.' . $menu->name) }}</span>
                                            @if (isset($menu->badge))
                                                <?php $badgeClasses = 'badge badge-pill badge-light-primary ml-auto mr-1'; ?>
                                                <span
                                                    class="{{ isset($menu->badgeClass) ? $menu->badgeClass : $badgeClasses }} ">{{ $menu->badge }}</span>
                                            @endif
                                        </a>
                                        @if (isset($menu->submenu))
                                            @include('panels/submenu', ['menu' => $menu->submenu])
                                        @endif
                                    </li>
                                @endif
                            @else
                                <li class="nav-item {{ Route::current()->uri() === $menu->url ? 'active' : '' }} {{ $custom_classes }}"
                                    id="{{ $custom_id }}">
                                    <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0)' }}"
                                        class="d-flex align-items-center"
                                        target="{{ isset($menu->newTab) ? '_blank' : '_self' }}">
                                        <i data-feather="{{ $menu->icon }}"></i>
                                        <span class="menu-title text-truncate">{{ __('locale.' . $menu->name) }}</span>
                                        @if (isset($menu->badge))
                                            <?php $badgeClasses = 'badge badge-pill badge-light-primary ml-auto mr-1'; ?>
                                            <span
                                                class="{{ isset($menu->badgeClass) ? $menu->badgeClass : $badgeClasses }} ">{{ $menu->badge }}</span>
                                        @endif
                                    </a>
                                    @if (isset($menu->submenu))
                                        @include('panels/submenu', ['menu' => $menu->submenu])
                                    @endif
                                </li>
                            @endif
                        @endif
                    @endif
                @endforeach
            @endif
            {{-- Foreach menu item ends --}}
        </ul>
    </div>
</div>
<!-- END: Main Menu-->