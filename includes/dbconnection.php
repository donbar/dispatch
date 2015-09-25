<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

function getdbconnection(){
	$dbName = $_SERVER["DOCUMENT_ROOT"] . "\dispatch.accdb";

	if (!file_exists($dbName)) {
		print "could not find database file";
	    die;
	}
	$db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)}; DBQ=$dbName; Uid=; Pwd=;");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $db;
}

function getVehicleStatus($event_id, $truck_id){


	$db = getdbconnection();

	$query = "
		select top 1 * from ( 
			SELECT top 1 reported_time as pretty_time,*
			FROM incident 
				RIGHT JOIN (event_vehicle LEFT JOIN incident_vehicle ON event_vehicle.vehicle_id = incident_vehicle.vehicle_id) ON incident.ID = incident_vehicle.incident_id
			WHERE (((event_vehicle.event_id)=".$event_id . ") and (incident.event_id = " . $event_id . ")
				AND ((event_vehicle.vehicle_id)=" . $truck_id . ") 
				AND ((incident_vehicle.tm_available) Is Null))
				and override = 1
				order by override desc, incident.ID desc
			union
			SELECT top 1 reported_time as pretty_time,*
			FROM incident 
				RIGHT JOIN (event_vehicle LEFT JOIN incident_vehicle ON event_vehicle.vehicle_id = incident_vehicle.vehicle_id) ON incident.ID = incident_vehicle.incident_id
			WHERE (((event_vehicle.event_id)=".$event_id . ") and (incident.event_id = " . $event_id . ")
				AND ((event_vehicle.vehicle_id)=" . $truck_id . ") 
				AND ((incident_vehicle.tm_available) Is Null))
				and override = 0
				order by override desc, incident.ID asc)
			order by override desc		
	";
	$result = $db->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC); 
	if ($row{'tm_acknowledge'} && $row{'tm_onscene'} && $row{'tm_clear'} && $row{'tm_offcourse'}){
		$status_id[0] = 1;  # off course
		$status_id[2] = $row['tm_offcourse'];
	}elseif ($row{'tm_acknowledge'} && $row{'tm_onscene'} && $row{'tm_clear'}){
		$status_id[0] = 2;  # in transit FROM
		$status_id[2] = $row['tm_clear'];
	}elseif ($row{'tm_acknowledge'} && $row{'tm_onscene'} ){
		$status_id[0] = 3;  # on scene
		$status_id[2] = $row['tm_onscene'];
	}elseif ($row{'tm_acknowledge'}){
		$status_id[0] = 4;  # in transit TO
		$status_id[2] = $row['tm_acknowledge'];
	}elseif ($row{'track_config_input'}){
		$status_id[0] = 5;
		$status_id[2] = $row['pretty_time'];

	}else{
		$status_id[0] = 0;
	}
	$status_id[1] = $row['track_config_input'];
	$status_id[3] = $row['orientation'];
	$status_id[4] = $row['location'];
	return $status_id;
}

function getTruckOnline($event_id, $truck_id){
	$db = getdbconnection();
	$query = "select datediff('s',last_checkin, now()) as lastseen from event_vehicle where event_id = " . $event_id . " and vehicle_id = " . $truck_id;
	$result = $db->query($query);
	$row = $result->fetch(PDO::FETCH_ASSOC); 
	return $row['lastseen'];
}

?>