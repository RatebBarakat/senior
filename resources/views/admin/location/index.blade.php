<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>OpenStreetMap Example</title>
    <link rel="stylesheet" href="{{ asset('css/sb-admin-2.css') }}">
    <link rel="stylesheet" href="{{asset('css/admin/admin.css')}}">
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
</head>
<body>
    
    <a href="/admin" class="btn btn-primary mt-3 ml-3">back to dashboard</a>

    <div id="mapid" class="m-3" style="height: 500px;"></div>

    <a href="{{route('admin.location.create')}}" class="btn btn-primary m-3">create new location</a>

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

        // // Loop through each location and add a marker to the map
        // for (var i = 0; i < locations.length; i++) {
        //     var location = locations[i];
        //     var marker = L.marker([location.latitude, location.longitude]).addTo(mymap);
        // }
    </script>
    


