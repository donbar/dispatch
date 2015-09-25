<?php

include "includes/dispatch.css";
include "includes/dbconnection.php";


$event_id = $_REQUEST['event'];
if ($event_id == ""){
	$event_id = 1;
}
$vehicle_id = $_REQUEST['vehicle'];
if ($vehicle_id == ""){
	$vehicle_id = 1;
}

$db = getdbconnection();
/* Query to determine which trucks this is */
$whoami_query = "select vehicle_name, soundfile from vehicle where id = " . $vehicle_id;
$whoami_result = $db->query($whoami_query);
$vehicle_name = $whoami_result->fetch(PDO::FETCH_ASSOC);
$vehicle_sound = $vehicle_name['soundfile'];


/* Query to determine which track this is */
$track_query = "
		select track_name
		    from track inner join event on event.track_id = track.id
		    where event.id = " . $event_id;

$result = $db->query($track_query);
$track = $result->fetch(PDO::FETCH_ASSOC); 
$track_name = $track['track_name'];

$update_query = "
	update event_vehicle set last_checkin = now() where event_id = " . $event_id . " and vehicle_id = " . $vehicle_id;
$update_result = $db->query($update_query);	

$incident_count_query = "
SELECT count(*) as counter
FROM incident_vehicle INNER JOIN incident ON incident_vehicle.incident_id = incident.ID
WHERE (((incident.[event_id])=" . $event_id . ") 
AND (([vehicle_id])=" . $vehicle_id . "))
and isnull(tm_available)
";
$incident_count_result = $db->query($incident_count_query);
$incident_count_row = $incident_count_result->fetch(PDO::FETCH_ASSOC);
$incident_count = $incident_count_row['counter'];

$vehicle_query = "
select top 1 * 
from (select top 1 incident_vehicle.id as ivid,*
FROM incident_vehicle INNER JOIN incident ON incident_vehicle.incident_id = incident.ID
WHERE (((incident.[event_id])=" . $event_id . ") 
AND (([vehicle_id])=" . $vehicle_id . "))
and isnull(tm_available)
and override = 1
order by override desc, reported_time desc
union 
select top 1 incident_vehicle.id as ivid,*
FROM incident_vehicle INNER JOIN incident ON incident_vehicle.incident_id = incident.ID
WHERE (((incident.[event_id])=" . $event_id . ") 
AND (([vehicle_id])=" . $vehicle_id . "))
and isnull(tm_available)
and override = 0
order by override desc, incident_id asc)
order by override desc
";
$vehicle_result = $db->query($vehicle_query);
$vehicle_row = $vehicle_result->fetch(PDO::FETCH_ASSOC);


$background = '#000000';
if ($vehicle_row{'fire'}){
	$background = 'red';
}

$counter = ($incident_count > 1 ? $incident_count-1 . " more incidents queued" : "");
print "<span class='loggedinas'>Logged in as: ".$vehicle_name['vehicle_name']. "<br><span class='largetext'>" . $counter . "</span></span>";
print "<span class='clock'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
print "<span class='clock'> $track_name " . date('h:i:s A') . "</span>";

print "<span id='truck' class='truck' style='background-color:$background; vertical-align: top'>";
print "<table cellpadding=2 cellspacing=2 width='85%' style='margin: 0px auto;' border=0 >";

$allclear = 0;
if ($vehicle_row{'track_config_input'}){
	/* This is where we'll display the actual message */
	if(strlen($vehicle_row{'track_config_input'}) > 3){
		$width = 140;
	}else{
		$width = 240;
	}
	print " <tr valign='middle'><td align='center' valign='top' width=50%><span style='vertical-align: middle; font-size: ".$width."px; color: #ffffff'>".$vehicle_row{'track_config_input'} . "</span></td>
			<td align='center' width=50%><span class='evenlargetext'>" . $vehicle_row{'orientation'}."<br>" .$vehicle_row{'location'};
		if ($vehicle_row{'rollover'}){
			$conditions .= "<span class='rollover'>ROLLOVER</span>";
		}
		if ($vehicle_row{'debris'}){
			if (strlen($conditions) > 0){ $conditions .= "<span class='rollover'> / </span>";}
			$conditions .= "<span class='rollover'>DEBRIS</span>";
		}
		if ($vehicle_row{'impact'}){
			if (strlen($conditions) > 0){ $conditions .= "<span class='rollover'> / </span>";}
			$conditions .= "<span class='rollover'>IMPACT</span>";
		}
		print "<br>".$conditions;

		print "</span></td></tr>";
	

}else{
	/* there is nothing to display */
	print "<tr><td>&nbsp;</td></tr>";
	$allclear = 1;
}	
print "</table>";
$playsound = 0;

if ($allclear == 0){
	$ack1 = 'truckoverridebuttonactive';
	$ack2 = 'truckoverridebuttonactive';
	$ack3 = 'truckoverridebuttonactive';
	$ack4 = 'truckoverridebuttonactive';
	if ($vehicle_row{'tm_acknowledge'}){
		$ack1 = 'truckoverridebuttoninactive';
	}else{
		$playsound = 1;
	}
	if ($vehicle_row{'tm_onscene'}){
		$ack2 = 'truckoverridebuttoninactive';
	}
	if ($vehicle_row{'tm_clear'}){
		$ack3 = 'truckoverridebuttoninactive';
	}
	if ($vehicle_row{'tm_offcourse'}){
		$ack4 = 'truckoverridebuttoninactive';
	}

	print "<span class='largetext'><span class='truckbuttons'>";
	print "<input type='hidden' name='incident_vehicle_id' value=".$vehicle_row{'ivid'}.">";
	print "<input type='button' class='$ack1' id='truck1' value='Acknowledge' onclick='document.truck.actionbtn.value=1;document.truck.submit();'>&nbsp;&nbsp;&nbsp;&nbsp;";
	print "<input type='button' class='$ack2' id='truck2' value='On Scene' onclick='document.truck.actionbtn.value=2;document.truck.submit();'>&nbsp;&nbsp;&nbsp;&nbsp;";
	print "<input type='button' class='$ack3' id='truck3' value='Clear of Scene' onclick='document.truck.actionbtn.value=3;document.truck.submit();'>&nbsp;&nbsp;&nbsp;&nbsp;";
	print "<input type='button' class='$ack4' id='truck4' value='Off Course' onclick='document.truck.actionbtn.value=4;document.truck.submit();'>&nbsp;&nbsp;&nbsp;&nbsp;";
	print "<input type='button' class='truckoverridebuttonactive' id='truck5' value='Available' onclick='document.truck.actionbtn.value=5;document.truck.submit();'>";
	print "</span></span>";

	if($playsound == 1){
		print "
			<audio src='includes/" . $vehicle_sound . "' preload='auto' autoplay/>";
	}

}
$db->connection = null;
?>
