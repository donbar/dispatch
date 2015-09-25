<!DOCTYPE html>
<html>

<?php
$event_id = $_REQUEST['event_id'];

$vehicle_id = $_REQUEST['vehicle_id'];

include "includes/dispatch.css";
include "includes/dbconnection.php";
$db = getdbconnection();
?>

<head>
<script language="javascript" src="js/jquery-1.3.2.js" type="text/javascript"></script>
<script>

$(document).ready(function() {// When the Dom is ready
$('.warningbox').hide();
});

function flipoff(objectid, objectnum){

	fullobj = objectid + objectnum;
	elmt = document.getElementById(fullobj);
	checkbox = fullobj+'val';
	if (elmt.className == 'overridebuttoninactive'){
		//document.getElementById(fullobj).className="overridebuttonactive";
		//document.getElementById(checkbox).value = 1;
	}else{
		document.getElementById(fullobj).className="overridebuttoninactive";
		document.getElementById(checkbox).value = 0;
	}
}

function showStatus(event,vehicle) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtStatus").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "truckstatusajax.php?event=<?php print $event_id ?>&vehicle=<?php print $vehicle_id ?>", true);
        xmlhttp.send();
        randomrefresh = Math.floor((Math.random() * 4) + 2) * 1000;
        setTimeout(showStatus, randomrefresh );
}

function showMessage(event,vehicle) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtMessage").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "truckmessageajax.php?event=<?php print $event_id ?>&vehicle=<?php print $vehicle_id ?>", true);
        xmlhttp.send();
        randomrefresh = 10 * 1000;
        setTimeout(showMessage, randomrefresh );
}


</script>
</head>

<?php

/* If we are coming in from a POST, let's save the data quickly */

if ($_POST['actionbtn'] > 0){

	$field = "";
	if ($_REQUEST['actionbtn']	== 1){
		$update_status_query = "update incident_vehicle set tm_acknowledge = now() where id = ". $_REQUEST{'incident_vehicle_id'};
		$insert_veh_result = $db->query($update_status_query);
	}
	if ($_REQUEST['actionbtn']	== 2){
		$update_status_query = "update incident_vehicle set tm_onscene = now() where id = ". $_REQUEST{'incident_vehicle_id'};
		$insert_veh_result = $db->query($update_status_query);

		$update_status_query = "update incident_vehicle set tm_acknowledge = now() where id = ". $_REQUEST{'incident_vehicle_id'} . 
								" and tm_acknowledge is null";
		$insert_veh_result = $db->query($update_status_query);
	}	
	if ($_REQUEST['actionbtn']	== 3){
		$update_status_query = "update incident_vehicle set tm_clear = now() where id = ". $_REQUEST{'incident_vehicle_id'};
		$insert_veh_result = $db->query($update_status_query);

		$update_status_query = "update incident_vehicle set tm_acknowledge = now() where id = ". $_REQUEST{'incident_vehicle_id'} . 
								" and tm_acknowledge is null";
		$insert_veh_result = $db->query($update_status_query);

		$update_status_query = "update incident_vehicle set tm_onscene = now() where id = ". $_REQUEST{'incident_vehicle_id'} . 
								" and tm_onscene is null";
		$insert_veh_result = $db->query($update_status_query);
	}	

	if ($_REQUEST['actionbtn']	== 4){
		$update_status_query = "update incident_vehicle set tm_offcourse = now() where id = ". $_REQUEST{'incident_vehicle_id'};
		$insert_veh_result = $db->query($update_status_query);

		$update_status_query = "update incident_vehicle set tm_acknowledge = now() where id = ". $_REQUEST{'incident_vehicle_id'} . 
								" and tm_acknowledge is null";
		$insert_veh_result = $db->query($update_status_query);

		$update_status_query = "update incident_vehicle set tm_onscene = now() where id = ". $_REQUEST{'incident_vehicle_id'} . 
								" and tm_onscene is null";
		$insert_veh_result = $db->query($update_status_query);

		$update_status_query = "update incident_vehicle set tm_clear = now() where id = ". $_REQUEST{'incident_vehicle_id'} . 
								" and tm_clear is null";
		$insert_veh_result = $db->query($update_status_query);		
	}		

	if ($_REQUEST['actionbtn']	== 5){
		$update_status_query = "update incident_vehicle set tm_available = now() where id = ". $_REQUEST{'incident_vehicle_id'};
		$insert_veh_result = $db->query($update_status_query);

		$update_status_query = "update incident_vehicle set tm_acknowledge = now() where id = ". $_REQUEST{'incident_vehicle_id'} . 
								" and tm_acknowledge is null";
		$insert_veh_result = $db->query($update_status_query);

		$update_status_query = "update incident_vehicle set tm_onscene = now() where id = ". $_REQUEST{'incident_vehicle_id'} . 
								" and tm_onscene is null";
		$insert_veh_result = $db->query($update_status_query);		

		$update_status_query = "update incident_vehicle set tm_clear = now() where id = ". $_REQUEST{'incident_vehicle_id'} . 
								" and tm_clear is null";
		$insert_veh_result = $db->query($update_status_query);	

		$update_status_query = "update incident_vehicle set tm_offcourse = now() where id = ". $_REQUEST{'incident_vehicle_id'} . 
								" and tm_offcourse is null";
		$insert_veh_result = $db->query($update_status_query);
	}
	if ($_REQUEST['actionbtn']	== 99){
		/* clear the message being displayed */
		$update_status_query = "delete from message_vehicle where message_id = ". $_REQUEST{'message_id'} . " and vehicle_id = " . $vehicle_id;
		$insert_veh_result = $db->query($update_status_query);

		$cleanitquery = "delete from message where  not exists (select count(*) from message_vehicle where message_id = ". $_REQUEST{'message_id'} . ")";
		$insert_veh_result = $db->query($cleanitquery);
	}		


}


print "<body onload='document.truck.postit.value= 0;showStatus(".$event_id.",".$vehicle_id.");showMessage(".$event_id.",".$vehicle_id.")'>";
print "<form name='truck' method='post'>";
print "<input type='hidden' name='actionbtn' value=0>";
print "<input type='hidden' name='event_id' value='".$event_id."'>";
print "<input type='hidden' name='vehicle_id' value='".$vehicle_id."'>";

print "<input type='hidden' name='postit' value=0>";

print "<span id='txtStatus'></span>";
print "<span id='txtMessage'></span>";

print "</form>";

print "
 <div id='gps' style='display:block; visibility:hidden'>
  <iframe src='gps.php?event_id=".$event_id."&vehicle_id=".$vehicle_id."' height='250px' width='250px' ></iframe>
</div>
";


$db->connection = null;
?>

</body>
</html>