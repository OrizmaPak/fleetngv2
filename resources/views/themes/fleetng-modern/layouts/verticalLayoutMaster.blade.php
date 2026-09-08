<body class="fleetng-modern vertical-layout vertical-menu-modern {{ $configData['showMenu'] ? '2-columns' : '1-column' }} {{ $configData['bodyClass'] }} {{ $configData['footerType'] }}"
      data-menu="vertical-menu-modern"
      data-col="{{ $configData['showMenu'] ? '2-columns' : '1-column' }}"
      data-framework="laravel"
      data-asset-path="{{ asset('/') }}">
    <a class="fleetng-skip-link" href="#fleetng-main">Skip to main content</a>

    @if($configData['showMenu'])
        @include('panels.sidebar')
    @endif

    @include('panels.navbar')

    <main id="fleetng-main" class="app-content content {{ $configData['pageClass'] }}" tabindex="-1">
        <div class="content-overlay"></div>
        <div class="content-wrapper {{ $configData['layoutWidth'] === 'boxed' ? 'container' : '' }}">
            @if($configData['pageHeader'])
                @include('panels.breadcrumb')
            @endif
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </main>

    <div class="sidenav-overlay" aria-hidden="true"></div>
    @include('panels.footer')
    @include('panels.scripts')
</body>
</html>
