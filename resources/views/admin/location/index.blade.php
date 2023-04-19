@extends('layouts.admin')

@section('title')
locations
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
    #locations_filter{
        max-width: 200px;
    }
</style>
@endsection

@section('content')

    <select name="" id="locations_filter" class="custom-select mb-3">
        <option value="">move to </option>
        @foreach ($locations as $location)
            <option value="{{$location['id']}}">{{$location['name']}}</option>
        @endforeach
    </select>

    <div id="mapid" style="height: 500px;"></div>
    <a href="{{route('admin.location.create')}}" class="btn btn-primary m-3">create new location</a>    
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder@1.13.1/dist/Control.Geocoder.js"></script>
<script src="https://unpkg.com/esri-leaflet-geocoder@2.3.0/dist/esri-leaflet-geocoder.js"></script>
<script>
    var locations = {!! json_encode($locations) !!};
    var mymap = L.map('mapid');
    var firstLocation = locations[0];
    if (firstLocation) {
        mymap.setView([firstLocation.latitude, firstLocation.longitude], 11);  
        var marker = L.marker([firstLocation.latitude, firstLocation.longitude]).addTo(mymap);
    }else{
        mymap.setView([33.882957310697, 35.494079589844], 11);  
    }

    // Add the OpenStreetMap tile layer to the map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(mymap);

    locations.forEach((location) => {
        const marker = L.marker([location.latitude, location.longitude]).addTo(mymap);
        const editUrl = "{{ route('admin.location.edit', ':id') }}".replace(':id', location.id);
        marker.bindPopup(`<a href="${editUrl}">${location.name}</a>`).openPopup();
    });

    var mySelect = document.getElementById('locations_filter');
    mySelect.onchange = (event) => {
        var value = event.target.value;
        const record = locations.find(item => item.id == value);
        if (value != "") {
            if (record) {
                mymap.setView([record.latitude, record.longitude], 11);              
            } else {
                alert("location not found")
            }
        }
    }


</script>


@endpush


