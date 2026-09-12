@if($errors->any())
    <div class="alert alert-danger fleetng-validation-summary" role="alert" tabindex="-1">
        <strong>Please check the submitted details.</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach
        </ul>
    </div>
@endif
