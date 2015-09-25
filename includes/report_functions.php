<?php
function median($session, $event_id, $track_id, $vehicle_id, $field1, $field2){

$db = getdbconnection();
if ($event_id == -1){
	$holdingtable = "
		insert into report_data (session_id, ack)
		select * from
		(SELECT ".$session.",datediff('s',".$field1.",".$field2.") as ack 
			from ((incident_vehicle 
			INNER JOIN incident ON incident_vehicle.incident_id = incident.ID) 
			INNER JOIN event ON incident.event_id = event.ID) 
			INNER JOIN vehicle ON incident_vehicle.vehicle_id = vehicle.ID 
			where event.track_id = " . $track_id . "
			and vehicle.ID = " . $vehicle_id . "
			and ".$field1." <> " . $field2 . ")
		";
}else{
	$holdingtable = "
		insert into report_data (session_id, ack)
		select * from
		(SELECT ".$session.",datediff('s',".$field1.",".$field2.") as ack 
			FROM (incident_vehicle INNER JOIN incident ON incident_vehicle.incident_id = incident.ID) 
			INNER JOIN vehicle ON incident_vehicle.vehicle_id = vehicle.ID where event_id = " . $event_id . "
			and vehicle.ID = " . $vehicle_id . "
			and ".$field1." <> " . $field2 . ")
		";
}

	$holding_result = $db->query($holdingtable);

$removeoutliers = "delete report_data from 
					(select top 1 * from report_data where ack = (select max(ack) from report_data where session_id = " . $session . ")
					and session_id = " . $session . ")";
$holding_result = $db->query($removeoutliers);

$removeoutliers = "delete report_data from 
					(select top 1 * from report_data where ack = (select min(ack) from report_data where session_id = " . $session . ")
					and session_id = " . $session . ")";
$holding_result = $db->query($removeoutliers);

$maxsql = "select max(ack) as maxnum from (select top 50 percent ack from report_data where session_id = " . $session . "  order by ack)";

$minsql = "select min(ack) as minnum from (select top 50 percent ack from report_data where session_id = " . $session . " order by ack desc)";





$max_result = $db->query($maxsql);
$max_row = $max_result->fetch(PDO::FETCH_ASSOC);

$min_result = $db->query($minsql);
$min_row = $min_result->fetch(PDO::FETCH_ASSOC);
$median = ($max_row['maxnum'] + $min_row['minnum']) / 2.00;
$median = gmdate("i:s", $median);

$holdingtable = "delete from report_data where session_id =  " . $session;
$holding_result = $db->query($holdingtable);
$db->connection = null;


return $median;
}

function average($session, $event_id, $track_id, $vehicle_id, $field1, $field2){

$db = getdbconnection();

if ($event_id == -1){
	$holdingtable = "
		insert into report_data (session_id, ack)
		select * from
			(SELECT ".$session.",datediff('s',".$field1.",".$field2.") as ack 
			from ((incident_vehicle 
			INNER JOIN incident ON incident_vehicle.incident_id = incident.ID) 
			INNER JOIN event ON incident.event_id = event.ID) 
			INNER JOIN vehicle ON incident_vehicle.vehicle_id = vehicle.ID
			where event.track_id = " . $track_id . "
			and vehicle.ID = " . $vehicle_id . "
			and ".$field1." <> " . $field2 . ")
		";
}else{
	$holdingtable = "
		insert into report_data (session_id, ack)
		select * from
		(SELECT ".$session.",datediff('s',".$field1.",".$field2.") as ack 
			FROM (incident_vehicle INNER JOIN incident ON incident_vehicle.incident_id = incident.ID) 
			INNER JOIN vehicle ON incident_vehicle.vehicle_id = vehicle.ID where event_id = " . $event_id . "
			and vehicle.ID = " . $vehicle_id . "
			and ".$field1." <> " . $field2 . ")
		";
}	
$holding_result = $db->query($holdingtable);

$removeoutliers = "delete report_data from 
					(select top 1 * from report_data where ack = (select max(ack) from report_data where session_id = " . $session . ")
					and session_id = " . $session . ")";
$holding_result = $db->query($removeoutliers);

$removeoutliers = "delete report_data from 
					(select top 1 * from report_data where ack = (select min(ack) from report_data where session_id = " . $session . ")
					and session_id = " . $session . ")";
$holding_result = $db->query($removeoutliers);

$avgsql = "select avg(ack) as avgnum from report_data where session_id = " . $session;


$avg_result = $db->query($avgsql);
$avg_row = $avg_result->fetch(PDO::FETCH_ASSOC);

$average = gmdate("i:s", $avg_row['avgnum']);

$holdingtable = "delete from report_data where session_id =  " . $session;
$holding_result = $db->query($holdingtable);
$db->connection = null;


return $average;
}
?>