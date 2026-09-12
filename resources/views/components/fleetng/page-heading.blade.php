@props(['description' => null])
<header class="fleetng-page-heading">
    <div>
        <h1>{{ $slot }}</h1>
        @if($description)<p>{{ $description }}</p>@endif
    </div>
    @isset($actions)<div class="fleetng-page-actions">{{ $actions }}</div>@endisset
</header>
