<?php
include "includes/dbconnection.php";
$db = getdbconnection();

$event_id = $_REQUEST['event_id'];
$vehicle_id = $_REQUEST['vehicle_id'];

$message_query = "
select vehicle_name, lat, lon, icon from (vehicle inner join event_vehicle on event_vehicle.vehicle_id = vehicle.ID) 
	where event_id = " . $event_id . " and vehicle_id = " . $vehicle_id;
$message_result = $db->query($message_query);
$message_row = $message_result->fetch(PDO::FETCH_ASSOC);
$prim_lat = $message_row['lat'];
$prim_lon = $message_row['lon'];
$prim_vehicle_name = $message_row['vehicle_name'];
$prim_icon = $message_row['icon'];

?>

<!DOCTYPE xhtml PUBLIC "-//W3C//DTD XHTML 4.01//EN">
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<script type="text/javascript"
  src="http://maps.google.com/maps/api/js?sensor=true"></script>
<style type="text/css">
  *, html { margin:0; padding:0 }
  div#map_canvas { width:100%; height:100%; }
</style>
<script type="text/javascript">
  var map;
  function initialise() {
    var latlng = new google.maps.LatLng(<?php print $prim_lat . "," . $prim_lon ?>);
    var myOptions = {
      zoom: 4,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.TERRAIN,
      disableDefaultUI: true
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    map.setZoom(17);

    var marker = new google.maps.Marker({
	    map: map,
	    position: latlng,
	    title: '<?php print $prim_vehicle_name ?>',
	    icon: '<?php print $prim_icon ?>'
    });
    marker.setAnimation(google.maps.Animation.BOUNCE);

<?php
	$message_query = "
	select vehicle_name, lat, lon, icon from (vehicle inner join event_vehicle on event_vehicle.vehicle_id = vehicle.ID) 
		where event_id = " . $event_id . " and vehicle_id <> " . $vehicle_id;
	$message_result = $db->query($message_query);
	$message_row = $message_result->fetch(PDO::FETCH_ASSOC);
	$lat = $message_row['lat'];
	$lon = $message_row['lon'];
	$vehicle_name = $message_row['vehicle_name'];
	$icon = $message_row['icon'];

	print "
	var latlng = new google.maps.LatLng(".$lat . "," . $lon . ");
    var marker = new google.maps.Marker({
	    map: map,
	    position: latlng,
	    title: '$vehicle_name',
	    icon: '$icon'
    });
	";
?>
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

<?php
$db->connection = null;
?>