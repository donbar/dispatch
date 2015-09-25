<!DOCTYPE html>
<html>


<?php
include "includes/dispatch.css";
include "includes/dbconnection.php";
include "includes/version.php";
$version = version();
$db = getdbconnection();

?>
	<script>
	function changelogin(){
		thevar = document.getElementById('eventvehicle').value;
		results = thevar.split("/");
		document.truck.event_id.value= results[0];
		document.truck.vehicle_id.value= results[1];
		document.truck.submit();
	}
	</script>

<?php
print "<body>";
print "<form name='truck' method='post' action='truckmanager.php'>";
print "<input type='hidden' name='event_id'>";
print "<input type='hidden' name='vehicle_id'><br>";

$event_query = "
	SELECT event.id AS evid, vehicle.id AS vid, *
	FROM track INNER JOIN (event INNER JOIN (vehicle INNER JOIN event_vehicle ON vehicle.ID = event_vehicle.vehicle_id) ON event.ID = event_vehicle.event_id) ON track.ID = event.track_id
	WHERE (((Now()) Between [begin_date] And dateadd('d',1,[end_date])))
	order by track.track_name, vehicle_name;
";
$event_result = $db->query($event_query);

print "<span class='welcome'><center>Welcome to Safety Dispatch v".$version." - Truck Login</center></span><br>";
print "<span class='eventmenu'><input id='menu' type='button' class='overridebuttonactive' value='Menu' onclick='document.location=\"index.php\"'></span>";
print "<br><br>";
print "<center>";
print "<span class='selecttext'>Event and Truck? </span><select name='eventvehicle' id='eventvehicle' class='largetext' onchange='changelogin();'>";
print "<option name='eventvehicle' value=0>Select One</option>";
while ($event_row = $event_result->fetch(PDO::FETCH_ASSOC)) {
	print "<option name='eventvehicle' value='".$event_row['evid']."/".$event_row['vid']."'>".$event_row['track_name'] . " - " . $event_row['vehicle_name']."</option>";
}
print "</select>";
print "</center>";




print "</form>";

$db->connection = null;
?>

</body>
</html>