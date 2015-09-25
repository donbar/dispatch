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
		thevar = document.getElementById('event').value;
		document.truck.event_id.value= thevar;
		document.truck.submit();
	}
	</script>

<?php
print "<body>";
print "<form name='truck' method='post' action='dispatchmanager.php'>";
print "<input type='hidden' name='event_id'>";

$event_query = "
SELECT event.id AS evid, *
FROM track INNER JOIN event ON track.ID = event.track_id
WHERE (((Now()) Between [begin_date] And dateadd('d',1,[end_date])))
order by track.track_name;

";
$event_result = $db->query($event_query);

print "<span class='welcome'><center>Welcome to Safety Dispatch v".$version."</center></span><br>";
print "<span class='eventmenu'><input id='menu' type='button' class='overridebuttonactive' value='Menu' onclick='document.location=\"index.php\"'></span>";
print "<br><br>";
print "<center>";
print "<span class='selecttext'>Event? </span><select name='event' id='event' class='largetext'>";
print "<option value=0>Select One</option>";
while ($event_row = $event_result->fetch(PDO::FETCH_ASSOC)) {
	print "<option value='".$event_row['evid']."'>".$event_row['track_name']."</option>";
}
print "</select><br><br>";
print "<span class='selecttext'>Password:</span><input type='password' name='password' style='font-size:24px;' size=10 maxlength=10>";
print "<br><br><input type='button' value='   Login   ' class='truckoverridebuttonactive' onclick='changelogin();'>";
print "</center>";
print "<input type='hidden' name='pwdexists' value=1>";




print "</form>";

$db->connection = null;
?>

</body>
</html>