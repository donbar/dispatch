<?php
	include "includes/dbconnection.php";
$db = getdbconnection();

$event_id = $_REQUEST['event_id'];
?>

<!DOCTYPE xhtml PUBLIC "-//W3C//DTD XHTML 4.01//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<script type="text/javascript"
  src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
</script>
</head>

<?php
	$message_query = "
	select vehicle.ID as vehid, vehicle_name, lat, lon, icon from (vehicle inner join event_vehicle on event_vehicle.vehicle_id = vehicle.ID) 
		where event_id = " . $event_id;
	$message_result = $db->query($message_query);
	$counter = 0;
	while($message_row = $message_result->fetch(PDO::FETCH_ASSOC)){
		$counter++;

		$lastseen = getTruckOnline($event_id, $message_row['vehid']);

		$lat = $message_row['lat'];
		$lon = $message_row['lon'];
		$icon = $message_row['icon'];
		$vehid = $message_row['vehid'];

		#print $marker."setPosition(new google.maps.LatLng(".$lat . "," . $lon . "))";
		$truckname = 'truck'.$counter;
		$latname = 'lat'.$counter;
		$lonname = 'lon'.$counter;
		$timername = 'timer'.$counter;
		print "
		<input type='hidden' name='".$truckname."' id='".$truckname."' value='".$vehid."'>
		<input type='hidden' name='".$latname."' id='".$latname ."' value='".$lat."'>
		<input type='hidden' name='".$lonname."' id='".$lonname ."' value='".$lon."'>
		<input type='hidden' name='".$timername."' id='".$timername ."' value='".$lastseen."'>
		";
	}

?>
<input type='hidden' name='counter' id='counter' value='<?php print $counter ?>'>

</html>


<?php
$db->connection = null;
?>