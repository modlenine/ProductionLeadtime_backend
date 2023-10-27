<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    /* กำหนดความสูงและความกว้างของแผนที่ */
    #map {
      height: 400px;
      width: 100%;
    }
  </style>
</head>
<body>
  <h1>ค้นหาสถานที่ต้นทางและปลายทาง</h1>
  <div>
    <label for="originInput">สถานที่ต้นทาง:</label>
    <input type="text" id="originInput" placeholder="ป้อนสถานที่ต้นทาง">
  </div>
  <div>
    <label for="destinationInput">สถานที่ปลายทาง:</label>
    <input type="text" id="destinationInput" placeholder="ป้อนสถานที่ปลายทาง">
  </div>
  <button onclick="calculateRoute()">คำนวณเส้นทาง</button>
  <div id="map"></div>
  <p id="distance"></p>

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3A9Mc08SyCJjtWFLFijSITvvx0UmdmFU&libraries=places"></script>
  <script>
    var map;
    var directionsService;
    var directionsDisplay;
    var originMarker;
    var destinationMarker;

    function initMap() {
      // ตำแหน่งเริ่มต้นของแผนที่ (Bangkok, Thailand)
      var initialLocation = { lat: 13.7563, lng: 100.5018 };

      // สร้างแผนที่
      map = new google.maps.Map(document.getElementById('map'), {
        center: initialLocation,
        zoom: 10
      });

      // สร้าง DirectionsService และ DirectionsRenderer
      directionsService = new google.maps.DirectionsService();
      directionsDisplay = new google.maps.DirectionsRenderer();
      directionsDisplay.setMap(map);

      // เพิ่ม event listener บนแผนที่
      map.addListener('click', function(event) {
        var clickedLocation = event.latLng;
        if (!document.getElementById('originInput').value) {
          document.getElementById('originInput').value = clickedLocation.lat() + ', ' + clickedLocation.lng();
            // สร้างหมุดสีเขียวที่จุดต้นทาง
            if (originMarker) {
            originMarker.setMap(null);
            }
            originMarker = new google.maps.Marker({
                position: clickedLocation,
                map: map,
                title: 'จุดต้นทาง',
                icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
            });
        } else if (!document.getElementById('destinationInput').value) {
          document.getElementById('destinationInput').value = clickedLocation.lat() + ', ' + clickedLocation.lng();
            // สร้างหมุดสีแดงที่จุดปลายทาง
            if (destinationMarker) {
            destinationMarker.setMap(null);
            }
            destinationMarker = new google.maps.Marker({
                position: clickedLocation,
                map: map,
                title: 'จุดปลายทาง',
                icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
            });
        }
      });

        // ค้นหาสถานที่ต้นทาง
        var originAutocomplete = new google.maps.places.Autocomplete(
            document.getElementById('originInput'));
            originAutocomplete.addListener('place_changed', function() {
            var place = originAutocomplete.getPlace();
            if (!place.geometry) {
            window.alert("ไม่พบข้อมูลสถานที่: '" + place.name + "'");
            return;
            }
        });

        // ค้นหาสถานที่ปลายทาง
        var destinationAutocomplete = new google.maps.places.Autocomplete(
            document.getElementById('destinationInput'));
            destinationAutocomplete.addListener('place_changed', function() {
            var place = destinationAutocomplete.getPlace();
            if (!place.geometry) {
            window.alert("ไม่พบข้อมูลสถานที่: '" + place.name + "'");
            return;
            }
        });
    }

    function calculateRoute() {
        destinationMarker.setMap(null);
        originMarker.setMap(null);
      var originInput = document.getElementById('originInput').value;
      var destinationInput = document.getElementById('destinationInput').value;

      // คำนวณเส้นทาง
      var request = {
        origin: originInput,
        destination: destinationInput,
        travelMode: 'DRIVING'
      };

      directionsService.route(request, function(result, status) {
        if (status == 'OK') {
          directionsDisplay.setDirections(result);

          var distance = result.routes[0].legs[0].distance.text;
          document.getElementById('distance').innerText = 'ระยะทาง: ' + distance;
        } else {
          console.error('เกิดข้อผิดพลาดในการคำนวณเส้นทาง: ' + status);
        }
      });
    }

    // เรียกใช้งานฟังก์ชัน initMap เมื่อโหลดแผนที่
    window.onload = initMap;
  </script>
</body>
</html>