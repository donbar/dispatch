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

function flip(objectid, objectnum){

	fullobj = objectid + objectnum;
	elmt = document.getElementById(fullobj);
	checkbox = fullobj+'val';
	if (elmt.className == 'overridebuttoninactive'){
		document.getElementById(fullobj).className="overridebuttonactive";
		document.getElementById(checkbox).value = 1;
	}else{
		document.getElementById(fullobj).className="overridebuttoninactive";
		document.getElementById(checkbox).value = 0;
	}

	/* This is a special check for override.  If doing override, check to see if the vehicle is checked too */
	if (objectid == 'ovrbtn' && document.getElementById(checkbox).value == 1){
		checkfield = 'veh'+objectnum;
		checkfieldbox = 'veh'+objectnum+'val';
		if (document.getElementById(checkfield).className == 'overridebuttoninactive'){
			document.getElementById(checkfield).className="overridebuttonactive";
			document.getElementById(checkfieldbox).value = 1;
		}
	}

}

function flipall(objectid){


	fullobj = objectid + '1';
	elmt = document.getElementById(fullobj);
	checkbox = fullobj+'val';
	if (elmt.className == 'overridebuttoninactive'){
		document.getElementById(fullobj).className="overridebuttonactive";
		document.getElementById(checkbox).value = 1;
		/* now go flip the others on */

		var elems = document.querySelectorAll('[id^=ovrbtn]');
		for (var i = 0, length = elems.length; i < length; i++) {
			if (elems[i].id.slice(-3) == 'val'){
   		 		checkfieldbox = elems[i].id;
   		 		checkfield = elems[i].id.substring(0,elems[i].id.length - 3);
   		 		if (checkfield != 'veh-1'){
   		 			document.getElementById(checkfieldbox).value = 1;
   		 			document.getElementById(checkfield).className="overridebuttonactive";
   		 		}
   		 	}
   		 }
		var elems = document.querySelectorAll('[id^=veh]');
		for (var i = 0, length = elems.length; i < length; i++) {
			if (elems[i].id.slice(-3) == 'val'){
   		 		checkfieldbox = elems[i].id;
   		 		checkfield = elems[i].id.substring(0,elems[i].id.length - 3);
   		 		if (checkfield != 'veh-1'){
   		 			document.getElementById(checkfieldbox).value = 1;
   		 			document.getElementById(checkfield).className="overridebuttonactive";
   		 		}
   		 	}
   		 }

	}else{
		document.getElementById(fullobj).className="overridebuttoninactive";
		document.getElementById(checkbox).value = 0;
		/* now go flip the others off */
		var elems = document.querySelectorAll('[id^=ovrbtn]');
		for (var i = 0, length = elems.length; i < length; i++) {
			if (elems[i].id.slice(-3) == 'val'){
   		 		checkfieldbox = elems[i].id;
   		 		checkfield = elems[i].id.substring(0,elems[i].id.length - 3);
   		 		if (checkfield != 'veh-1'){
   		 			document.getElementById(checkfieldbox).value = 0;
   		 			document.getElementById(checkfield).className="overridebuttoninactive";
   		 		}
   		 	}
   		 }
		var elems = document.querySelectorAll('[id^=veh]');
		for (var i = 0, length = elems.length; i < length; i++) {
			if (elems[i].id.slice(-3) == 'val'){
   		 		checkfieldbox = elems[i].id;
   		 		checkfield = elems[i].id.substring(0,elems[i].id.length - 3);
   		 		if (checkfield != 'veh-1'){
   		 			document.getElementById(checkfieldbox).value = 0;
   		 			document.getElementById(checkfield).className="overridebuttoninactive";
   		 		}
   		 	}
   		 }


	}

	/* This is a special check for override.  If doing override, check to see if the vehicle is checked too */
	if (objectid == 'ovrbtn' && document.getElementById(checkbox).value == 1){
		checkfield = 'veh'+objectnum;
		checkfieldbox = 'veh'+objectnum+'val';
		if (document.getElementById(checkfield).className == 'overridebuttoninactive'){
			document.getElementById(checkfield).className="overridebuttonactive";
			document.getElementById(checkfieldbox).value = 1;
		}
	}

}



function radioflip(objectid, objectnum, objectcount){
	fullobj = objectid + objectnum;
	elmt = document.getElementById(fullobj);
	checkbox = fullobj+'val';
	if (elmt.className == 'overridebuttoninactive'){
		document.getElementById(fullobj).className="overridebuttonactive";
		document.getElementById(checkbox).value = 1;
		i = 0;
		while (i < objectcount) {
		    i++;
			if (i != objectnum){
				changeobj = objectid+i;
				changecheck = changeobj + 'val';
		    	document.getElementById(changeobj).className="overridebuttoninactive";
				document.getElementById(changecheck).value = 0;
			}
		}
	}else{
		document.getElementById(fullobj).className="overridebuttoninactive";
		document.getElementById(checkbox).value = 0;
	}
}

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

function showPace(event) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtPace").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "pacestatusajax.php?event_id=<?php print $event_id ?>", true);
        xmlhttp.send();
        randomrefresh = Math.floor((Math.random() * 3) + 1) * 1000;
        setTimeout(showPace, randomrefresh)
}

function validate(){
	if (!document.getElementById('track_config').value && document.getElementById('clear1val').value != 1 &&
			document.getElementById('message').value == ''){
		document.getElementById('warningbox').innerHTML = "Which turn?";
		$(".warningbox").fadeIn(1500);
		exit;
	}

	i = 0;
	isvalid = 0;
	clsElements = document.querySelectorAll(".vehicleclass");
	for (var i=0, max=clsElements.length; i < max; i++) {
	     // Do something with the element here
	     if (clsElements[i].value != '' && clsElements[i].value == '1'){
	     	isvalid = 1;
	     }
	}
	if (isvalid == 0){
		document.getElementById('warningbox').innerHTML = "Select at least one vehicle";
		$(".warningbox").fadeIn(1500);
	}else{
		document.dispatch.postit.value=1;
		document.dispatch.submit();
	}
}


</script>
</head>

<?php


include "includes/dispatch.css";
include "includes/dbconnection.php";
$db = getdbconnection();

if (isset($_POST['pwdexists'])){
	if($_POST['password'] <> 'nasa1'){
		print "
		<script>
		window.location = 'index.php';
		</script>";
	}
}

/* If we are coming in from a POST, let's save the data quickly */
if(isset($_POST['postit'])){
	if ($_POST['postit'] == 1){
		$region_track_query = "SELECT region_track.id as region_track_id
					FROM event INNER JOIN region_track ON (event.track_id = region_track.track_id) AND (event.region_id = region_track.region_id)
					where event.id = " . $event_id;
		$region_track_result = $db->query($region_track_query);
		$region_track_row = $region_track_result->fetch(PDO::FETCH_ASSOC);

		$orientation = '';
		$location = '';

		if ($_POST['orient1val'] == '1'){
			$orientation = 'Left';
		}
		if ($_POST['orient2val'] == '1'){
			$orientation = 'Center';
		}
		if ($_POST['orient3val'] == '1'){
			$orientation = 'Right';
		}
		if ($_POST['loc1val'] == '1'){
			$location = 'Entrance';
		}
		if ($_POST['loc2val'] == '1'){
			$location = 'Apex';
		}
		if ($_POST['loc3val'] == '1'){
			$location = 'Exit';
		}
		$fire = ($_POST['fire1val'] == 1) ? $_POST['fire1val'] : 0;
		$roll = ($_POST['roll1val'] ==1) ? $_POST['roll1val'] : 0;
		$impact = ($_POST['impact1val'] ==1) ? $_POST['impact1val'] : 0;
		$debris = ($_POST['debris1val'] ==1) ? $_POST['debris1val'] : 0;
		$clear = ($_POST['clear1val'] ==1) ? $_POST['clear1val'] : 0;
		$message = $_POST['message'];

		if ($clear == "" && $_REQUEST['track_config'] <> ""){
			# dispatching one or more trucks
			$insert_query = "
			INSERT INTO incident (reported_time, event_id, region_track_id, track_config_input, orientation, location, fire, rollover, impact, debris)
			values  (now(), " .$event_id . ",". $region_track_row['region_track_id'].",'" . $_REQUEST['track_config'] . "','" . $orientation . "','" . $location . "'," .
					$fire . "," . $roll . ",". $impact . "," . $debris . ")";
			$insert_result = $db->query($insert_query);

			$id_query = "SELECT @@Identity as new_id";
			$id_result = $db->query($id_query);
			$id_row = $id_result->fetch(PDO::FETCH_ASSOC);

			foreach($_POST as $key=>$value){
				if (substr($key,0,3) == 'veh' && $value == 1){
					$vehicle_id = substr($key,3,1);
					$overridevar = "ovrbtn".$vehicle_id."val";
					$override = $_POST[$overridevar];
					if ($override == ''){
						$override = 0;
					}

					if ($override == 1){
						/* we have an override, reset any open incidents */
						$update_query = "update incident_vehicle 
										set tm_acknowledge = null,
										tm_onscene = null,
										tm_clear = null
										where tm_available is null and vehicle_id = " . $vehicle_id ;
						$update_result = $db->query($update_query);
					}

					if($vehicle_id == '-'){ # pace car here
						# do nothing right now
					}else{
						$insert_veh_query = "insert into incident_vehicle (incident_id, vehicle_id, override) values (" . $id_row['new_id'] . "," . $vehicle_id . ",".$override.")";
						$insert_veh_result = $db->query($insert_veh_query);
					}
		    	}
			}
		}
		if ($clear == "" && $message <> ""){
			#sending a message to one or more trucks
			$message = str_replace("'","''",$_REQUEST['message']);
			$insert_query = "
			INSERT INTO message (reported_time, event_id, region_track_id, message_text)
			values  (now(), " .$event_id . ",". $region_track_row['region_track_id'].",'" . $message . "')";
			$insert_result = $db->query($insert_query);

			$id_query = "SELECT @@Identity as new_id";
			$id_result = $db->query($id_query);
			$id_row = $id_result->fetch(PDO::FETCH_ASSOC);

			foreach($_POST as $key=>$value){
				if (substr($key,0,3) == 'veh' && $value == 1){
					$vehicle_id = substr($key,3,1);
					if($vehicle_id == '-'){ # pace car here
						$vehicle_id = -1;
					}
					$insert_veh_query = "insert into message_vehicle (message_id, vehicle_id) values (" . $id_row['new_id'] . "," . $vehicle_id . ")";
					$insert_veh_result = $db->query($insert_veh_query);
		    	}
			}		
		}
		if ($clear <> ""){
			/* this is a clear button */
			foreach($_POST as $key=>$value){
				if (substr($key,0,3) == 'veh' && $value == 1){
					$vehicle_id = substr($key,3,1);
						/* we have an override, populate any other open incidents for this vehicle */
						$update_query = "update incident_vehicle 
										set override = 1,
										tm_acknowledge = IIF(isnull(tm_acknowledge),now(),tm_acknowledge),
										tm_onscene = IIF(isnull(tm_onscene),now(),tm_onscene),
										tm_clear = IIF(isnull(tm_clear),now(),tm_clear),
										tm_offcourse = IIF(isnull(tm_offcourse),now(),tm_offcourse),
										tm_available = IIF(isnull(tm_available),now(),tm_available)
										where vehicle_id = " . $vehicle_id;
						$update_result = $db->query($update_query);
				}
			}
		}
	}
}

print "<body onload='document.dispatch.postit.value= 0;showStatus(".$event_id.");'>";
print "<form name='dispatch' method='post'>";
print "<input type='hidden' name='event_id' value='".$event_id."'>";
print "<input type='hidden' name='vehicle_id' >";
print "<input type='hidden' name='postit' value=0>";

print "<span id='txtStatus'></span>";

print "<div class='warningbox' id='warningbox'>Please select at least one vehicle</div>";

print "<span class='history' align='right'><input id='history' type='button' class='overridebuttoninactive' value='History'  onclick='document.dispatch.action=\"history.php\"; document.dispatch.submit();'>";
print "<center><input type='button' class='overridebuttoninactive' name='reset' id='reset' value='        Reset Buttons        ' onclick='document.dispatch.submit();'></center>";
print "</span>";
print "<hr>";



print "<span class='largetext'>";
print "<table cellpadding=5 width='95%' border=0>";
print "<tr valign='bottom'>
		<td width='30'>&nbsp;</td>
		<td colspan=6>
		<input type='hidden' name='impact1val' id='impact1val'>
		<input type='button' class='overridebuttoninactive' name='impact1' id='impact1' value='IMPACT' onclick='flip(\"impact\",1)'>
		&nbsp;

		<input type='hidden' name='debris1val' id='debris1val'>
		<input type='button' class='overridebuttoninactive' name='debris1' id='debris1' value='DEBRIS' onclick='flip(\"debris\",1)'>
		&nbsp;

		<input type='hidden' name='roll1val' id='roll1val'>
		<input type='button' class='overridebuttoninactive' name='roll1' id='roll1' value='ROLLOVER' onclick='flip(\"roll\",1)'>
		&nbsp;

		<input type='hidden' name='fire1val' id='fire1val'>
		<input type='button' class='overridebuttoninactive' name='fire1' id='fire1' value='&nbsp;&nbsp;&nbsp;FIRE&nbsp;&nbsp;&nbsp;' onclick='flip(\"fire\",1)'>
		<br><br>
		</td>
		</tr>";

print "<tr valign='bottom'>
		<td width='30'>&nbsp;</td>
		<td width='30'>
		<input type='hidden' name='clear1val' id='clear1val'>
		<input type='button' class='overridebuttoninactive' name='clear1' id='clear1' value='Clear' onclick='flip(\"clear\",1)'><br>
		<span class='largetext'>Vehicle</span></td>
		<td width='7%'>
		<input type='hidden' name='all1val' id='all1val'>
		<input type='button' class='overridebuttoninactive' name='all1' id='all1' value='All' onclick='flipall(\"all\")'><br>
		<span class='largetext'>Priority</span></td>


		<td width='22%'><span class='largetext'>Turn</span></td>
		<td width='15%'><span class='largetext'>Orientation</span></td>
		<td width='15%'><span class='largetext'>Location</span></td>
		<td>&nbsp;</td>
		</tr>";
print "<tr valign='top'><td>&nbsp;</td>";

print "<td>";

$vehicle_query = "
	SELECT vehicle.id, vehicle.vehicle_name
	FROM vehicle INNER JOIN event_vehicle ON vehicle.ID = event_vehicle.vehicle_id
	where event_id = " . $event_id . "
	order by vehicle_name
	";

$vehicle_result = $db->query($vehicle_query);
while ($vehicle_row = $vehicle_result->fetch(PDO::FETCH_ASSOC)) {
	$lastseen = getTruckOnline($event_id, $vehicle_row['id']);
	if ($lastseen < 61){
		print "<input type='hidden' name='veh". $vehicle_row['id'] . "val' id='veh" . $vehicle_row['id'] . "val' class='vehicleclass'>";
		print "<input type='button' class='overridebuttoninactive' id='veh". $vehicle_row['id']."' value='".$vehicle_row['vehicle_name']."' onclick='flip(\"veh\",".$vehicle_row['id'].")'>". "<br><br>";
	}
}
print "<input type='hidden' name='veh-1val' id='veh-1val' class='vehicleclass'>";
print "<input type='button' class='overridebuttoninactive' id='veh-1' value='Pace' onclick='flip(\"veh\",-1)'>". "<br><br>";


/*
$lastseen = getTruckOnline($event_id, $vehicle_row['vehicle_id']);
*/
print "</td>";
print "<td>";

$vehicle_result = $db->query($vehicle_query);
$vehicle_counter = 0;
while ($vehicle_row = $vehicle_result->fetch(PDO::FETCH_ASSOC)) {
	$vehicle_counter++;
	$lastseen = getTruckOnline($event_id, $vehicle_row['id']);
	if ($lastseen < 61){
		print "<input type='hidden' name='ovrbtn" . $vehicle_row['id'] . "val' id='ovrbtn" . $vehicle_row['id'] . "val'>";
		print "<input type='button' class='overridebuttoninactive' id='ovrbtn". $vehicle_row['id']."' value='Override' onclick='flip(\"ovrbtn\",".$vehicle_row['id'].")'>". "<br><br>";
	}
}	
print "<input type='hidden' name='vehicle_counter' value = " . $vehicle_counter .">";
print "</td><td>";

print "<input type='text' maxlength=5 id='track_config' name='track_config' autofocus='autofocus' onfocus='$(\".warningbox\").fadeOut(150);'>";

print "</td>";
print "<td>";
print "<input type='button' class='overridebuttoninactive' id='orient1' value='Left' onclick='radioflip(\"orient\",1,3)'>". "<br><br>";
print "<input type='button' class='overridebuttoninactive' id='orient2' value='Center' onclick='radioflip(\"orient\",2,3)'>". "<br><br>";
print "<input type='button' class='overridebuttoninactive' id='orient3' value='Right' onclick='radioflip(\"orient\",3,3)'>". "<br><br>";
print "<input type='hidden' name='orient1val' id='orient1val'>";
print "<input type='hidden' name='orient2val' id='orient2val'>";
print "<input type='hidden' name='orient3val' id='orient3val'>";



print "</td>";
print "<td>";

print "<input type='button' class='overridebuttoninactive' id='loc1' value='Entrance' onclick='radioflip(\"loc\",1,3)'>". "<br><br>";
print "<input type='button' class='overridebuttoninactive' id='loc2' value='Apex' onclick='radioflip(\"loc\",2,3)'>". "<br><br>";
print "<input type='button' class='overridebuttoninactive' id='loc3' value='Exit' onclick='radioflip(\"loc\",3,3)'>". "<br><br>";
print "<input type='hidden' name='loc1val' id='loc1val'>";
print "<input type='hidden' name='loc2val' id='loc2val'>";
print "<input type='hidden' name='loc3val' id='loc3val'>";


print "</td>";


print "</td><td><input id='dispatch' type='button' value='Dispatch!' onclick='validate();'></td>";
print "</tr>";
print "<tr valign='middle'><td>&nbsp;</td><td colspan=5 valign='middle'>
		<span class='largetext'>Message</span> <textarea cols=120 rows=3 maxlength=250 id='message' name='message'></textarea>
		</td>
		<td><input id='sendmessage' class='overridebuttoninactive' type='button' value='Send Message' onclick='validate();'></td>
		</tr>
		";
print "</table>";
print "</span>";
print "</form>";

$db->connection = null;
?>

</body>
</html>