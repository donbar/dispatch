<html>
<head>
<meta http-equiv="refresh" content="2">
<style>
body{
	background-color: #c0c0c0;
	font-family: arial;
}
span.clock {
    position: absolute;
    right: 100px;
    top: 10px;
}
</style>
</head>
<body>


<?php

/* include "includes/dbconnection.php"; */

print "<table cellpadding=2>";

/* Query to determine which trucks are not available */
$vehicle_query = "
	SELECT event_vehicle.vehicle_id, vehicle_name
	FROM vehicle INNER JOIN event_vehicle ON vehicle.ID = event_vehicle.vehicle_id
	WHERE (((event_vehicle.[event_id])=1) AND ((event_vehicle.[vehicle_id])>0))
	ORDER BY vehicle_name;
";
$vehicle_result = $db->query($vehicle_query);

while ($vehicle_row = $vehicle_result->fetch(PDO::FETCH_ASSOC)) {

	$query = "
		SELECT *
		FROM track_config 
			RIGHT JOIN (incident RIGHT JOIN (event_vehicle LEFT JOIN incident_vehicle ON event_vehicle.vehicle_id = incident_vehicle.vehicle_id) 
					ON incident.ID = incident_vehicle.incident_id) 
					ON track_config.ID = incident.track_config_id
		WHERE (((event_vehicle.event_id)=1) 
			AND ((event_vehicle.vehicle_id)=" . $vehicle_row['vehicle_id'] . ") 
			AND ((incident_vehicle.override)=0) 
			AND ((incident_vehicle.tm_available) Is Null))
	";
	$result = $db->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC); 
	if ($row{'tm_acknowledge'} && $row{'tm_onscene'} && $row{'tm_clear'}){
		$status = "<span style='color:blue'>In Transit FROM Scene";
	}elseif ($row{'tm_acknowledge'} && $row{'tm_onscene'} ){
		$status = "<span style='color:red'>On Scene</span>";
	}elseif ($row{'tm_acknowledge'}){
		$status = "<span style='color:yellow'>In Transit TO Scene</span>";		
	}elseif ($row{'turn_name'}){
		$status = "<span style='color:orange'>NOT ACKNOWLEDGED</span>";		
	}else{
		$status = "<span style='color:green'>Available</span>";		
	}
	print "<tr><td align='right'>".$vehicle_row{'vehicle_name'} . "</td><td>" . $status."</td><td>" .$row{'turn_name'} . "</tr>";
}
print "</table>";
print "<span class='clock'> " . date('h:i:s A e') . "</span>";
print "<hr>";
$db->connection = null;
?>

</body>
</html>