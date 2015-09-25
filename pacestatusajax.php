<!DOCTYPE html>
<html>
<body>
<?php
include "includes/dispatch.css";
include "includes/dbconnection.php";

$event_id = $_REQUEST['event_id'];

$db = getdbconnection();


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
	$pace_status = 'ON COURSE';
}else{
	$pace_status = 'Off course';
}

print $pace_status;

$db->connection = null;
?>
</body>
</html>