<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <style>
      *{
        margin: 0;
        padding: 0;

      }
      #map{
        height: 500px;
        width: 100%;
      }
    </style>
  </head>
  <body>
    <div id = "map"></div>

<script>
  function initMap(){
    var location = {lat: 38.246639, lng: 21.734573};
    var map = new google.maps.Map(document.getElementById("map"),{
      zoom: 4,
      center: location,
    });
    var marker = new google.maps.Marker({
      position: location,
      map: map
    })
  }
</script>
//api key not working??
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFULxldhGcNoE8qyjlNERlk0rfQnjguhM&callback=initMap"
type="text/javascript"></script>


  </body>
</html>
