@extends('layouts.admin')
@section('title')
    create location
@endsection
@section('content')
    <div id="map"></div>

    <form method="POST" action="{{ route('admin.location.store') }}">
        @csrf
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <button type="submit">Save location</button>
    </form>

@endsection

@push('js')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBKi8xmBU6DEkxnsrzq8z1M_AZSXasaqMA"></script>
    <script>
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 8,
            center: {lat: 37.7749, lng: -122.4194}
        });

        var marker = new google.maps.Marker({
            position: {lat: 37.7749, lng: -122.4194},
            map: map,
            draggable: true
        });

        google.maps.event.addListener(marker, 'dragend', function(event) {
            document.getElementById('latitude').value = event.latLng.lat();
            document.getElementById('longitude').value = event.latLng.lng();
        });
    </script>
@endpush
