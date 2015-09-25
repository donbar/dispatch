<!DOCTYPE html>
<html>

<?php
$event_id = $_REQUEST['event_id'];

$vehicle_id = -1;

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

function showStatus(event) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtStatus").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "dispatchstatusajax.php?event_id=<?php print $event_id ?>", true);
        xmlhttp.send();
        randomrefresh = Math.floor((Math.random() * 5) + 3) * 1000;
        setTimeout(showStatus, randomrefresh );
}

function showMessage(event,vehicle) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtMessage").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "truckmessageajax.php?event=<?php print $event_id ?>&vehicle=-1", true);
        xmlhttp.send();
        randomrefresh = 30 * 1000;
        setTimeout(showMessage, randomrefresh );
}


</script>
</head>

<?php

/* If we are coming in from a POST, let's save the data quickly */

if ($_POST['initial'] > 0){
	$check_status = "select count(*) as cnt from event_pace where event_id = " . $event_id;
	$check_result = $db->query($check_status);
	$check_row = $check_result->fetch(PDO::FETCH_ASSOC);
	if ($check_row['cnt'] > 0){
		/* do nothing */
	}else{
		$update_status_query = "insert into event_pace (event_id, status) values (".$event_id.",0)";
		$insert_veh_result = $db->query($update_status_query);
	}
}

if ($_POST['actionbtn'] != ''){

	$field = "";
	if ($_REQUEST['actionbtn']	== 1){  /* on course */
		$update_status_query = "update event_pace set status = 1 where event_id = ". $event_id;
		$insert_veh_result = $db->query($update_status_query);
	}else{								/* off course */
		$update_status_query = "update event_pace set status = 0 where event_id = ". $event_id;
		$insert_veh_result = $db->query($update_status_query);
	}		
	if ($_REQUEST['actionbtn']	== 99){
		/* clear the message being displayed */
		$update_status_query = "delete from message_vehicle where message_id = ". $_REQUEST{'message_id'} . " and vehicle_id = -1";
		$insert_veh_result = $db->query($update_status_query);

		$cleanitquery = "delete from message where  not exists (select count(*) from message_vehicle where message_id = ". $_REQUEST{'message_id'} . ")";
		$insert_veh_result = $db->query($cleanitquery);
	}		


}

$oncourse = 'truckoverridebuttonactive';
$offcourse = 'truckoverridebuttoninactive';

print "<body onload='document.truck.postit.value= 0;showStatus(".$event_id."); showMessage(".$event_id.",".$vehicle_id.")'>";
print "<form name='truck' method='post'>";
print "<span id='txtStatus'></span>";
print "<input type='hidden' name='actionbtn' value='".$_REQUEST['actionbtn']."'>";
print "<input type='hidden' name='event_id' value='".$event_id."'>";

print "<input type='hidden' name='postit' value=0>";

if ($_REQUEST['actionbtn'] == '1'){
	$offcourse = 'truckoverridebuttoninactive';
	$oncourse = 'truckoverridebuttonactive';
	$message = "You are ON course";
}else{
	$offcourse = 'truckoverridebuttonactive';
	$oncourse = 'truckoverridebuttoninactive';
	$message = "You are OFF course";
}

print "<span class='largetext'><span class='truckbuttons'><center>";
print "<input type='button' class='$offcourse' id='offcourse' value='OFF Course' onclick='document.truck.actionbtn.value=0;document.truck.submit();'>&nbsp;&nbsp;&nbsp;&nbsp;";
print "<input type='hidden' name='pace' value=-1>";
print "<input type='button' class='$oncourse' id='oncourse' value='ON Course' onclick='document.truck.actionbtn.value=1;document.truck.submit();'><br>";
print $message;
print "</center>";

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