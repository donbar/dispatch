<?php
include "includes/dispatch.css";
include "includes/dbconnection.php";

$event_id = $_REQUEST['event_id'];

$db = getdbconnection();

$track_query = "
		select track_name
		    from track inner join event on event.track_id = track.id
		    where event.id = " . $event_id;

$result = $db->query($track_query);
$track = $result->fetch(PDO::FETCH_ASSOC); 
$track_name = $track['track_name'];

/* Query to determine which trucks are not available */
$vehicle_query = "
	SELECT event_vehicle.vehicle_id, vehicle_name, last_checkin
	FROM vehicle INNER JOIN event_vehicle ON vehicle.ID = event_vehicle.vehicle_id
	WHERE (((event_vehicle.[event_id])=" . $event_id . ") AND ((event_vehicle.[vehicle_id])>0))
	ORDER BY vehicle_name;
";
$vehicle_result = $db->query($vehicle_query);
print "<center>";
print "<br><br><br>";
print "<table cellpadding=20 border=0 width='100%'>";
$vehicle_count = 0;
$playsound = 0;
while ($vehicle_row = $vehicle_result->fetch(PDO::FETCH_ASSOC)) {

	$incident_count_query = "
	SELECT count(*) as counter
	FROM incident_vehicle INNER JOIN incident ON incident_vehicle.incident_id = incident.ID
	WHERE (((incident.[event_id])=" . $event_id . ") 
	AND (([vehicle_id])=" . $vehicle_row['vehicle_id'] . "))
	and isnull(tm_available)
	";
	$incident_count_result = $db->query($incident_count_query);
	$incident_count_row = $incident_count_result->fetch(PDO::FETCH_ASSOC);
	$incident_count = $incident_count_row['counter'];

	$vehicle_count++;
	$status_id = '';

	$status_id = getVehicleStatus($event_id, $vehicle_row['vehicle_id']);
	$lastseen = getTruckOnline($event_id, $vehicle_row['vehicle_id']);

	$timeifavail = $status_id[2];
	if ($status_id[0] == 1){
		$status = "<span style='color:#9AFE2E'>Off Course ";
	}elseif ($status_id[0] == 2 ){
		$status = "<span style='color:blue'>In Transit FROM ";
	}elseif ($status_id[0] == 3 ){
		$status = "<span style='color:red'>On Scene</span>";
	}elseif ($status_id[0] == 4){
		$status = "<span style='color:yellow'>In Transit TO </span>";		
	}elseif ($status_id[0] == 5){
		$status = "<span style='color:orange'>NOT ACKNOWLEDGED</span>";	
		$playsound = 1;	
	}else{
		$status = "<span style='color:green'>Available</span>";		
		$timeifavail = -1;
	}
	if ($lastseen > 3600){
		# if offline over an hour, don't show how long
		$status = "<span style='color:white'>OFFLINE</span>";
	}elseif ($lastseen > 60){
		# if offline over a minute and under an hour, show how long
		$status = "<span style='color:white'>OFFLINE ('.number_format(($lastseen/60),2,'.',',').' min)</span>";
	}


	if ($timeifavail > -1){
		$thetime= $status_id[2];
		$elapsed = secondsToTime(time() - strtotime($thetime));
		// date_format(date_create($elapsed),'h:i:s')
		$timerifavail = " (" . $elapsed .")";
		/* substr($thetime,11,8) */
	}else{
		$timerifavail = '';
	}

	if ($incident_count > 1){
		$queue_query = "
			select * from ( 
				SELECT reported_time as pretty_time,*
				FROM incident 
					RIGHT JOIN (event_vehicle LEFT JOIN incident_vehicle ON event_vehicle.vehicle_id = incident_vehicle.vehicle_id) ON incident.ID = incident_vehicle.incident_id
				WHERE (((event_vehicle.event_id)=".$event_id . ") and (incident.event_id = " . $event_id . ")
					AND ((event_vehicle.vehicle_id)=" . $vehicle_row['vehicle_id'] . ") 
					AND ((incident_vehicle.tm_available) Is Null))
					and override = 1
				union
				SELECT reported_time as pretty_time,*
				FROM incident 
					RIGHT JOIN (event_vehicle LEFT JOIN incident_vehicle ON event_vehicle.vehicle_id = incident_vehicle.vehicle_id) ON incident.ID = incident_vehicle.incident_id
				WHERE (((event_vehicle.event_id)=".$event_id . ") and (incident.event_id = " . $event_id . ")
					AND ((event_vehicle.vehicle_id)=" . $vehicle_row['vehicle_id'] . ") 
					AND ((incident_vehicle.tm_available) Is Null))
					and override = 0)
				order by override desc, incident.ID asc";
		$queue_result = $db->query($queue_query);
		$counter = 0;
		$title = '';
		while($queue_row = $queue_result->fetch(PDO::FETCH_ASSOC)){
			$counter++;
			if ($counter>1){
				$title = $title . $counter.") Turn " . $queue_row['track_config_input'] . chr(13).chr(10);
			}
		}
		$counter = '<a href="#" onclick="return false" title="'.$title.'">['.($incident_count -1) .']</a>';
	}else{
		$counter = '';
	}

	if ($status_id[3] && $status_id[4]){
		$details = $status_id[3] . "/" . $status_id[4];		
	}else{
		$details = $status_id[3] . $status_id[4];	
	}		
	print "<tr><td align='right' width='30%'><span  style='font-size:34px; color:white'>".$vehicle_row{'vehicle_name'} . "</span></td><td style='font-size:34px'>" . $status."</td>
	<td><span class='evenlargetext'>" .$status_id[1]. " " . $details . " " . $timerifavail . " ".$counter . "</span></td></tr>";
}
if ($vehicle_count < 3){
	for ($t=0; $t < (3-$vehicle_count); $t++){
		print "<tr><td align='right'>&nbsp;</td><td>&nbsp;</td><td></tr>";
	}
}
if ($vehicle_count == 0){
	print "Unable to find vehicles for this event (error code: $event_id)";
}else{

	$pace_query = "
	SELECT status
	FROM event_pace
	WHERE event_id = " . $event_id;

	$pace_result = $db->query($pace_query);
	$pace_row = $pace_result->fetch(PDO::FETCH_ASSOC);
	$pace_status = $pace_row['status'];

	if ($pace_status != 0 && $pace_status != 1){
		$pace_status = 'Not checked in';
	}elseif ($pace_status == '1'){
		$pace_status = "; color:red'>ON COURSE</span>";
	}else{
		$pace_status = "; color:green'>Off course</span>";
	}
	print "<tr><td align='right'><span  style='font-size:34px; color: white;'>Pace</span></td><td><span style='font-size:34px" . $pace_status . "</td></tr>";
}

print "</table>";
print "</span>";
print "</center>";
print "<span class='clock'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
print "<span class='clock'> $track_name " . date('h:i:s A') . "</span>";

if($playsound == 1){
	print "
		<audio src='includes/alert.mp3' preload='auto' autoplay/>";
}


function secondsToTime($s)
{
    $h = floor($s / 3600);
    $s -= $h * 3600;
    $m = floor($s / 60);
    $s -= $m * 60;
    return sprintf('%02d', $m).'m:'.sprintf('%02d', $s)."s";
}

$db->connection = null;
?>
</body>
</html>