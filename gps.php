<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


include "includes/dbconnection.php";
$db = getdbconnection();

?>
<!DOCTYPE xhtml PUBLIC "-//W3C//DTD XHTML 4.01//EN">
<html>
<head>

<script>
var lat =  document.getElementById("lat");
var lon =  document.getElementById("lon");

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    }else{
        alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
	document.getElementById("lat").value = position.coords.latitude;
	document.getElementById("lon").value = position.coords.longitude;
    if ((position.coords.latitude != document.getElementById("oldlat").value
            || 
        position.coords.longitude != document.getElementById("oldlon").value)
        && position.coords.latitude != '' && position.coords.longitude != ''){
        // if position hasn't changed, don't do an update
	       window.setTimeout(function(){document.gps.submit()},1000);
    }else{
        window.setTimeout(function(){document.gps.submit()},10000);
    }
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            x.innerHTML = "User denied the request for Geolocation."
            break;
        case error.POSITION_UNAVAILABLE:
            x.innerHTML = "Location information is unavailable."
            break;
        case error.TIMEOUT:
            x.innerHTML = "The request to get user location timed out."
            break;
        case error.UNKNOWN_ERROR:
            x.innerHTML = "An unknown error occurred."
            break;
    }
}
</script>
<?php

$vehicle_id = $_REQUEST['vehicle_id'];
$event_id = $_REQUEST['event_id'];
$lat = $_REQUEST['lat'];
$lon = $_REQUEST['lon'];

if($lat <> '' && $lon <> ''){
	$update_query = "update event_vehicle set lat = " . $lat . ", lon = " . $lon . " where event_id = " . $event_id . " and vehicle_id = " . $vehicle_id;
	$update_result = $db->query($update_query);	
}

?>


<body onload="getLocation();">
	<form name='gps' method='get' action='#'>
		<input type='text' name='event_id' value="<?php print $event_id ?>">
		<input type='text' name='vehicle_id' value="<?php print $vehicle_id ?>">
		<input type='text' id='lat' name='lat'>
		<input type='text' id='lon' name='lon'>
        <input type='text' id='oldlat' name='oldlat' value="<?php print $lat ?>">
        <input type='text' id='oldlon' name='oldlon' value="<?php print $lon ?>">

	</form>
</body>
</html>

<?php
$db->connection = null;
?>
