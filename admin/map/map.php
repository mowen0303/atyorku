<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>York U Map</title>
    <style>
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #map {
            height: 100%;
        }
        /* Optional: Makes the sample page fill the window. */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<div id="map"></div>
<script>
    //
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 18,
            center: {lat: 43.773613, lng: -79.505424},
            mapTypeId: 'roadmap'
        });

        // Draw a polygon surrounding the library
        var buildingShapeCoordinates = [
            {lat: 43.773944, lng: -79.506611},
            {lat: 43.773777, lng: -79.506524},
            {lat: 43.773797, lng: -79.506437},
            {lat: 43.773621, lng: -79.506359},
            {lat: 43.773586, lng: -79.506458},
            {lat: 43.773419, lng: -79.506380},
            {lat: 43.773578, lng: -79.505653},
            {lat: 43.773753, lng: -79.505738},
            {lat: 43.773773, lng: -79.505653},
            {lat: 43.773953, lng: -79.505728},
            {lat: 43.773934, lng: -79.505812},
            {lat: 43.774112, lng: -79.505885},
            {lat: 43.773944, lng: -79.506611}
        ];
        var buildingPath = new google.maps.Polygon({
            path: buildingShapeCoordinates,
            strokeColor: "#FFFFFF",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#FF0000",
            fillOpacity: 0.4
        });
        buildingPath.setMap(map);

        // Create a marker on the library
        var buildingPositionCoordinate = {lat: 43.773747, lng: -79.50603};
        var buildingMarker = new google.maps.Marker({
            map: map,
            draggable: false,
            animation: google.maps.Animation.DROP,
            position: buildingPositionCoordinate
        })
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3XHZU3J8P2WA_3b17rzHkWku6H4QcJis&callback=initMap">
</script>
</body>
</html>