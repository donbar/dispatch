<!DOCTYPE html>

<?php
$event_id = $_REQUEST['event_id'];
?>

<html>
<head>
<script language="javascript" src="js/jquery-1.3.2.js" type="text/javascript"></script>
<script>

$(document).ready(function() {// When the Dom is ready
$('.warningbox').hide();
});

function showStatus(event) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtStatus").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "dispatchstatusajax.php?event_id=<?php print $event_id ?>", true);
        xmlhttp.send();
        randomrefresh = Math.floor((Math.random() * 3) + 1) * 1000;
        setTimeout(showStatus, randomrefresh)
}

</script>
</head>

<?php

$event_id = $_REQUEST['event_id'];
if ($event_id == ""){
	$event_id = 1;
}

include "includes/dispatch.css";
include "includes/dbconnection.php";
$db = getdbconnection();


print "<body onload='document.dispatch.postit.value= 0;showStatus(".$event_id.")'>";
print "<form name='dispatch' method='post' action='dispatchmanager.php'>";
print "<input type='hidden' name='event_id' value='".$event_id."'>";
print "<input type='hidden' name='postit' value=0>";

print "<span id='txtStatus'></span>";

print "<div class='warningbox' id='warningbox'>Please select at least one vehicle</div>";
print "<hr>";

print "<span class='history'><input id='history' type='button' class='overridebuttoninactive' value='Dispatch' onclick='document.dispatch.submit();'>
		<br><input id='refresh' type='button' class='overridebuttoninactive' value='Refresh Report&nbsp;' onclick='location.reload();'></span>";
print "</form>";

$history_sql = "
SELECT incident.id as incident_id,
datediff('s',reported_time,tm_clear) as elapsed,
datediff('s',reported_time,tm_acknowledge) as diffacknowledged,
datediff('s',reported_time,tm_onscene) as diffonscene,
datediff('s',reported_time,tm_clear) as diffclear,
datediff('s',reported_time,tm_offcourse) as diffoffcourse,
datediff('s',reported_time,tm_available) as diffavailable,
*
FROM (incident_vehicle INNER JOIN incident ON incident_vehicle.incident_id = incident.ID) INNER JOIN vehicle ON incident_vehicle.vehicle_id = vehicle.ID
WHERE incident.event_id = " . $event_id . "
order by reported_time desc, vehicle_name;
";
$history_result = $db->query($history_sql);


print "
	<table id='history' border=1>
	<tr>
	<th title='Override Used'>Ovrd</th>
	<th>Reported Time</th>
	<th>Turn</th>
	<th>Orientation</th>
	<th>Location</th>
	<th>Fire/Roll</th>
	<th>Vehicle</th>
	<th>Ackldg</th>
	<th>On-Scene</th>
	<th>Clear</th>
	<th>Off Course</th>
	<th>Available</th>
	<th>Time</td></th>
	</tr>";

$background = 'norm';
$rowevent = '0';
while ($history_row = $history_result->fetch(PDO::FETCH_ASSOC)) {
	$fireroll = '';

	if($history_row['fire'] == 1){
		$fireroll = "Fire";
	}
	if($history_row['rollover'] == 1){
		$fireroll = ($fireroll == "") ? "Rollover" : $fireroll . " / Rollover";
	}	
	

	if($history_row['impact'] == 1){
		$fireroll = ($fireroll == "") ? "Impact" : $fireroll . " / Impact";
	}		

	if($history_row['debris'] == 1){
		$fireroll = ($fireroll == "") ? "Debris" : $fireroll . " / Debris";
	}		


	if ($rowevent <> $history_row['incident_id']){
		$rowevent = $history_row['incident_id'];
		($background == 'norm' ? $background = 'alt' : $background = 'norm');
	}
	if ($history_row['tm_available'] == ''){
		$background = 'activeincident';
	}
	$elapsed = gmdate("i:s", $history_row['elapsed']);
	$total_elapsed = gmdate("H:i:s", $history_row['elapsed']);

	$ack_elapsed = gmdate("i:s", $history_row['diffacknowledged']);
	$onscene_elapsed = gmdate("i:s", $history_row['diffonscene']);
	$clear_elapsed = gmdate("i:s", $history_row['diffclear']);
	$offcourse_elapsed = gmdate("i:s", $history_row['diffoffcourse']);
	$avail_elapsed = gmdate("i:s", $history_row['diffavailable']);
	$override = ($history_row['override'] == 1 ? 'override' : '');
	$overridenote = ($history_row['override'] == 1 ? 'X' : '');
	$overridetip = ($history_row['override'] == 1 ? 'This incident was overridden' : '');

$tm_acknowledge = ($history_row{'tm_acknowledge'} =='' ? '' : date_format(date_create($history_row{'tm_acknowledge'}),'h:i:s'));
$tm_onscene = ($history_row{'tm_onscene'} =='' ? '' : date_format(date_create($history_row{'tm_onscene'}),'h:i:s'));
$tm_clear = ($history_row{'tm_clear'} =='' ? '' : date_format(date_create($history_row{'tm_clear'}),'h:i:s'));
$tm_offcourse = ($history_row{'tm_offcourse'} =='' ? '' : date_format(date_create($history_row{'tm_offcourse'}),'h:i:s'));
$tm_available = ($history_row{'tm_available'} =='' ? '' : date_format(date_create($history_row{'tm_available'}),'h:i:s'));

	print "	
	<tr class='$background'>
	<td  title='$overridetip'>$overridenote</td>
	<td  title='".$history_row['reported_time']."'>".date_format(date_create($history_row['reported_time']),'l m/d/y h:i:s')."</td>
	<td >".$history_row['track_config_input']."</td>
	<td >".$history_row['orientation']."</td>
	<td >".$history_row['location']."</td>
	<td >".$fireroll."</td>
	<td >".$history_row['vehicle_name']."</td>
	<td  title='Time to Acknowledge ".$ack_elapsed."'>".$tm_acknowledge."</td>
	<td  title='Time to Scene ".$onscene_elapsed."'>".$tm_onscene."</td>
	<td  title='Time to Clear ".$clear_elapsed."'>". $tm_clear."</td>
	<td  title='Time to Off Course ".$clear_elapsed."'>".$tm_offcourse."</td>
	<td  title='Time to Available ".$avail_elapsed."'>".$tm_available."</td>
	<td  title='Time to Clear ".$total_elapsed."'>".$elapsed."</td>
	</tr>";
}
print "</table>";

$db->connection = null;
?>

</body>
</html>