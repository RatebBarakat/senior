@extends('layouts.admin')

@section('title')
    edit | {{$location->name}}
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/sb-admin-2.css') }}">
<link rel="stylesheet" href="{{asset('css/admin/admin.css')}}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@1.13.1/dist/Control.Geocoder.css" />
<style>
    #mapid { height: 500px; }
    input{
        max-width: 200px;
    }
    .form-group{
        display: flex;
        gap: 10px
    }
</style>
@endsection

@section('content')
    
<form id="location-form" >
    <div id="mapid" class="my-3" style="height: 500px;"></div>
    <div class="form-group mt-3">
        <input class="form-control" type="text" value="{{$location->name}}" id="city" name="name" placeholder="Location Name">
        <button class="btn btn-primary" type="submit">update Location</button>
    </div>
    <button class="btn btn-primary ml-3" onclick="$('#deletModal').modal('show')" type="submit">delete Location</button>
    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
</form>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder@1.13.1/dist/Control.Geocoder.js"></script>
<script src="https://unpkg.com/esri-leaflet-geocoder@2.3.0/dist/esri-leaflet-geocoder.js"></script>
<script>
    var mymap = L.map('mapid');
    var lat =  {!! $location->latitude !!};
    var long = {!! $location->longitude !!};
    var id = {!! $location->id !!};
// Initialize the OpenStreetMap using Leaflet.js library
mymap.setView([lat, long], 11);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
  maxZoom: 18,
}).addTo(mymap);



L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(mymap);
    var marker = L.marker([lat, long]).addTo(mymap);
    mymap.on('click', function(e) {
        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(mymap);
        }
        $('#latitude').val(e.latlng.lat);
        $('#longitude').val(e.latlng.lng);
        // alert("long:"+e.latlng.lng+"lat:"+e.latlng.lat);
    });
    L.Control.geocoder({
        defaultMarkGeocode: false
    }).on('markgeocode', function(e) {
        var bbox = e.geocode.bbox;
        var poly = L.polygon([
            bbox.getSouthEast(),
            bbox.getNorthEast(),
            bbox.getNorthWest(),
            bbox.getSouthWest()
        ]);
        mymap.fitBounds(poly.getBounds());
        if (marker) {
            marker.setLatLng(e.geocode.center);
        } else {
            marker = L.marker(e.geocode.center).addTo(mymap);
        }
        // Set the latitude and longitude values of the form inputs
        $('#latitude').val(e.geocode.center.lat);
        $('#longitude').val(e.geocode.center.lng);
    });
    
</script>
<script>
    $('#location-form').submit(function(event) {
event.preventDefault();
var token = '{{ csrf_token() }}'; // Retrieve the CSRF token
$.ajax({
    url: `{{ route("admin.location.update", ":id") }}`.replace(':id', id),
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': token // Set the CSRF token in the headers
    },
    data: {
        'city': $('#city').val(),
        'latitude': $('#latitude').val(),
        'longitude': $('#longitude').val(),
    },
    success: function(response) {
        // Handle success response
        console.log(response);
        alert('Location updated successfully!');
        $('#search-input').val('');
        $('#latitude').val('');
        $('#longitude').val('');
        marker.remove();
        marker = null;
        window.location.href = "/admin/location"
    },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error(errorThrown);
            alert(jqXHR.responseText);
        }
});
});
</script>
@endpush
