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
$whoami_query = "select vehicle_name from vehicle where id = " . $vehicle_id;
$whoami_result = $db->query($whoami_query);
$vehicle_name = $whoami_result->fetch(PDO::FETCH_ASSOC);


/* Query to determine which track this is */
$track_query = "
		select track_name
		    from track inner join event on event.track_id = track.id
		    where event.id = " . $event_id;

$result = $db->query($track_query);
$track = $result->fetch(PDO::FETCH_ASSOC); 
$track_name = $track['track_name'];

$message_count_query = "
SELECT count(*) as counter
FROM message_vehicle INNER JOIN message ON message_vehicle.message_id = message.ID
WHERE (((message.[event_id])=" . $event_id . ") 
AND (([vehicle_id])=" . $vehicle_id . "))
";
$message_count_result = $db->query($message_count_query);
$message_count = $message_count_result->fetch(PDO::FETCH_ASSOC);


$message_query = "
SELECT top 1 message.id as mvid,*
FROM message_vehicle INNER JOIN message ON message_vehicle.message_id = message.ID
WHERE (((message.[event_id])=" . $event_id . ") 
AND (([vehicle_id])=" . $vehicle_id . "))
order by message.id;
";
$message_result = $db->query($message_query);
$message_row = $message_result->fetch(PDO::FETCH_ASSOC);

if($message_count['counter'] > 1){
	$counter = "1 of " . $message_count['counter'] . ":";
}else{
	$counter = "";
}

if($message_count['counter'] > 0){
	print "<input type='hidden' name='message_id'>";
	print "<span class='marquee'><marquee bgcolor='#ffffff' color='#000000' loop='-1' scrollamount='10' width='80%' height='25px'>" . $counter
		 . "[<b>" .date_format(date_create($message_row{'reported_time'}),'h:i:s') . "</b>] ".$message_row['message_text'] . "</marquee>
		<input type='button' class='okmarqueebutton' id='okmarqueebutton' value='OK' 
			onclick='document.truck.message_id.value=".$message_row['mvid'].";document.truck.actionbtn.value=99;document.truck.submit();'></span>";

		print "<audio src='includes/New_Messages.mp3' preload='auto' autoplay/>";			
}
$db->connection = null;
?>
