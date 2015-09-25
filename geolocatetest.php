<!DOCTYPE xhtml PUBLIC "-//W3C//DTD XHTML 4.01//EN">
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>Where Am I?</title>
<script type="text/javascript"
  src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="geometa.js"></script>
<style type="text/css">
  *, html { margin:0; padding:0 }
  div#map_canvas { width:100%; height:100%; }

</style>
<script type="text/javascript">
  var map;
  function initialise() {
    var latlng = new google.maps.LatLng(-25.363882,131.044922);
    var myOptions = {
      zoom: 4,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.TERRAIN,
      disableDefaultUI: true
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    prepareGeolocation();
    doGeolocation();
  }




</script>
</head>
<body >
  <div id="map_canvas"></div>
  <div id="info" class="lightbox">Detecting your location...</div>
  <script>
  initialise();
  </script>
</body>
</html>
