var polygon;
var polygonCoordinate = [];
var map;
var mapCenter;
var editable = true;
var mapListener;
var infoWindow;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 17,
        center: {
            lat: 43.773,
            lng: -79.502
        },
        mapTypeId: 'satellite'
    });
    map.setTilt(0);

    mapListener = map.addListener('click', function (e) {
        clickHandler(e)
    });
    map.addListener('drag', function () {
        //console.log(1)
    });

    polygon = new google.maps.Polygon({
        paths: polygonCoordinate,
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 1,
        fillColor: '#FF0000',
        fillOpacity: 0.3,
        editable: editable,
        draggable: false,
    });

    polygon.addListener('dragend', function () {
        //console.log('Vertex removed from inner path.');
    });

    // polygon.addListener('click', function () {
    //     editable = !editable;
    //     polygon.setEditable(editable);
    //     if (editable) {
    //         mapListener = map.addListener('click', function (e) {
    //             clickHandler(e)
    //         });
    //     } else {
    //         //结束编辑
    //         google.maps.event.removeListener(mapListener);
    //     }
    //
    // });

    polygon.addListener('click', showArrays);
}

function showArrays(event) {
        // Since this polygon has only one path, we can call getPath() to return the
        // MVCArray of LatLngs.
        var vertices = this.getPath();
        var contentString = event.latLng.lat() + ',' + event.latLng.lng() + '|';
        // Iterate over the vertices.
        for (var i =0; i < vertices.getLength(); i++) {
          var xy = vertices.getAt(i);
          contentString += xy.lat() + ',' + xy.lng() + ';';
        }
        // Replace the info window's content and position.
        $("#ta").val(contentString)
        //infoWindow.setContent(contentString);
      }

function drawPolygon(polygon) {
    polygon.setMap(map);
}

function removePloygon() {
    polygonCoordinate = [];
    $("#ta").val("");
    polygon.setMap(null);
}

function clickHandler(e) {
    polygonCoordinate.push(e.latLng.toJSON());
    polygon.setPaths(polygonCoordinate);
    //listener
    addEditListener();
    // polygon.setMap(null);
    drawPolygon(polygon);
}

function addEditListener() {
    polygon.getPaths().forEach(function (path, index) {
        google.maps.event.addListener(path, 'insert_at', function () {
            //console.log("insert_at");
        });
        google.maps.event.addListener(path, 'remove_at', function () {
            //console.log("remove_at");
        });
        google.maps.event.addListener(path, 'set_at', function () {
            //console.log("set_at");
            //console.log(polygonCoordinate);
            var coordinateArr = polygon.getPaths().getArray()[0].getArray();
            polygonCoordinate = [];
            coordinateArr.forEach(function (element) {
                polygonCoordinate.push(element.toJSON());
            })
            //console.log(polygonCoordinate);
            polygon.setPaths(polygonCoordinate);
            addEditListener();
            drawPolygon(polygon);
        });
    });
}
