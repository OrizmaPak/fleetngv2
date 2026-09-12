<div class="row">
  <div class="col-md-6 form-group">
    <label for="latitude">Latitude</label>
    <input class="form-control" id="latitude" name="latitude" type="number" step="any" min="-90" max="90" required value="{{ old('latitude', $location_details['latitude'] ?? '') }}">
    @error('latitude')<span class="text-danger">{{ $message }}</span>@enderror
  </div>
  <div class="col-md-6 form-group">
    <label for="longitude">Longitude</label>
    <input class="form-control" id="longitude" name="longitude" type="number" step="any" min="-180" max="180" required value="{{ old('longitude', $location_details['longitude'] ?? '') }}">
    @error('longitude')<span class="text-danger">{{ $message }}</span>@enderror
  </div>
</div>
