<?php
  // add &admin=1 to URL to go into permadelete mode to delete test events
?>
<!DOCTYPE html>
<html>


<?php
include "includes/dispatch.css";
include "includes/dbconnection.php";
include "includes/version.php";
$version = version();
$db = getdbconnection();

?>
<SCRIPT SRC="includes/CalendarPopup.js"></SCRIPT>
	<script>
	function changelogin(){
		thevar = document.getElementById('eventvehicle').value;
		results = thevar.split("/");
		document.truck.event_id.value= results[0];
		document.truck.vehicle_id.value= results[1];
		document.truck.submit();
	}delete

	</script>

<?php
print "<body>";
if($_POST['deleteme'] > 0){
	$sql = "delete from event where ID = " . $_POST['deleteme'];
	$result = $db->query($sql);
}
if($_POST['deletemehard'] > 0){
	$sql = "delete * from incident_vehicle where id in(
			select incident_vehicle.id 
			from incident_vehicle 
				inner join incident on incident_vehicle.incident_id = incident.id
				where incident.event_id = " . $_POST['deletemehard'] . ")";
	$result = $db->query($sql);
	$sql = "delete * from incident where event_id = ". $_POST['deletemehard'];			
	$result = $db->query($sql);
	$sql = "delete from event where ID = " . $_POST['deletemehard'];
	$result = $db->query($sql);
}
if($_POST['post_it'] == '1' && $_POST['begindate'] > '' && $_POST['enddate'] > ''){
	$oktosave = 0;
	$counter = 0;
	$vehicles = $_POST['veh_counter'];
	while ($counter < $vehicles){
		$counter++;
		$var = 'veh'.$counter;
		if($_POST[$var] > 0){
			$oktosave = 1;
		}
	}

	if($oktosave == 1){
		$region = $_POST['region'];
		$track = $_POST['track'];
		$begin = $_POST['begindate'];
		$end = $_POST['enddate'];
		$vehicles = $_POST['veh_counter'];
		$counter = 0;
		$insert_query = "insert into event (region_id, track_id, begin_date, end_date) 
					values (".$region.",".$track.",'".$begin."','".$end."')";
		$result = $db->query($insert_query);	

		$id_query = "SELECT @@Identity as new_id";
		$id_result = $db->query($id_query);
		$id_row = $id_result->fetch(PDO::FETCH_ASSOC);

		while ($counter < $vehicles){
			$counter++;
			$var = 'veh'.$counter;
			if($_POST[$var] > 0){
				/* insert into event_vehicle */
				$insert_query = "insert into event_vehicle (event_id, vehicle_id, last_checkin) 
							values (".$id_row[new_id].",".$_POST[$var].",now())";
				$result = $db->query($insert_query);	
			}
		}
	}else{
		print "<script>window.alert('Please select at least one vehicle to save data');</script>";
	}
}


print "<link href='includes/CalendarControl.css' rel='stylesheet' type='text/css'>
		<script src='includes/CalendarControl.js' language='javascript'></script>";
print "<form name='event' method='post'>";
print "<input type='hidden' name='post_it' value='0'>";
print "<input type='hidden' name='deleteme' value='0'>";
print "<input type='hidden' name='deletemehard' value='0'>";


print "<span class='welcome'><center>Welcome to Safety Dispatch v".$version." - Event Management</center></span>";
print "<span class='eventmenu'><input id='menu' type='button' class='overridebuttonactive' value='Menu' onclick='document.location=\"index.php\"'></span>";


print "<br><br>";

$event_query = "
	SELECT top 10 event.id AS evid, 
	Format([begin_date],'yyyy.mm.dd') as pretty_start,
	Format([end_date],'yyyy.mm.dd') as pretty_end,
	(select count(*) from incident where incident.event_id = event.ID) as counter,
	*
	FROM (track INNER JOIN event ON track.ID = event.track_id) INNER JOIN region ON event.region_id = region.ID
	WHERE (((Now())<dateadd('d',90,[end_date])))
	order by begin_date;
";
$event_result = $db->query($event_query);

print "<center>";
print "<span class='largetext'>Upcoming Events (up to 10 shown)</span><br>";
print "<table id='events' border=0 >
		<tr>
		<th>Region</th> 
		<th>Track</th>
		<th>Begin</th>
		<th>End</th>
		<th></th>
		</tr>";
while ($event_row = $event_result->fetch(PDO::FETCH_ASSOC)) {
print "<tr>
		<td title='ID:".$event_row['evid'] . "'>".$event_row['region_name']."</td> 
		<td>".$event_row['track_name']."</td>
		<td>".$event_row['pretty_start']."</td>
		<td>".$event_row['pretty_end']."</td>
		<td>";
		if($event_row['counter'] ==0){
			print "<a href='#' onclick='document.event.deleteme.value=".$event_row['evid']."; document.event.submit();'><img src='images/delete.png'></a></td>";
		}else{
			if($_GET['admin'] == 1){
				print "<a href='#' onclick='document.event.deletemehard.value=".$event_row['evid']."; document.event.submit();'>PERMADELETE</a>";
			}	
			print "</td>";
		}
		print "</tr>";

}
print "</table></span>";
print "<br><br>";
print "<table border=0>";
print "<tr><td colspan=2 align='center'><span class='largetext'>Add new event:</span></td></tr>";
print "<tr><td align='right' width='50%'><span class='selecttext'>Region:</span></td>";

print "<td><select id='region' name='region'>";
$region_query = "select * from region";
$region_result = $db->query($region_query);
while ($region_row = $region_result->fetch(PDO::FETCH_ASSOC)) {
	print "<option value='".$region_row['ID']."'>".$region_row['region_name']."</option>";
}
print "</select></td></tr>";

print "<tr><td align='right' width='50%'><span class='selecttext'>Track:</span></td>";

print "<td><select id='track' name='track'>";
$track_query = "select * from track";
$track_result = $db->query($track_query);
while ($track_row = $track_result->fetch(PDO::FETCH_ASSOC)) {
	print "<option value='".$track_row['ID']."'>".$track_row['track_name']."</option>";
}
print "</select></td></tr>";

print "<tr><td align='right' width='50%'><span class='selecttext'>Begin Date:</span></td>";
print "<td width='50%'><span class='normal'><input type='text' name='begindate' id='begindate' maxlength=8 size=8 value='' onfocus='showCalendarControl(this);'></td>";

print "<tr><td align='right' width='50%'><span class='selecttext'>End Date:</span></td>";
print "<td width='50%'><span class='normal'><input type='text' name='enddate' id='enddate' maxlength=8 size=8 value='' onfocus='showCalendarControl(this);'></td>";

print "<tr valign='top'><td align='right' width='50%'><span class='selecttext'>Available Vehicles:</span></td>";

print "<td>";
$vehicle_query = "select * from vehicle";
$vehicle_result = $db->query($vehicle_query);

while ($vehicle_row = $vehicle_result->fetch(PDO::FETCH_ASSOC)) {
	$veh_counter++;
	print "<input type='checkbox' name='veh".$veh_counter."' id='veh".$veh_counter."' value='".$vehicle_row['ID']."'><span class='selecttext'>".$vehicle_row['vehicle_name']."</span><br>";
}
print "<input type='hidden' name='veh_counter' value=".$veh_counter.">";
print "</td></tr>";
print "<tr><td colspan=2 align='center'>
		<input id='savedata' type='button' class='overridebuttonactive' value='Save Event' onclick='document.event.post_it.value=1;document.event.submit();'>
		</td></tr>";
print "</center>";




print "</form>";

$db->connection = null;
?>

</body>
</html>