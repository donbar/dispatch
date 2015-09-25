<!DOCTYPE html>
<html>
<head>
<style>
html, body { 
    margin: 0;
    padding: 0; 
    border: 0;
    background-color: #ffffff;
    font-family: arial;}
a:link{
	font-family: arial;
	color: #000000;
	text-decoration: none;
	font-size: 24px;	
}
a:visited{
	font-family: arial;
	color: #000000;
	text-decoration: none;
	font-size: 24px;
}
a:hover{
	font-family: arial;
	color: #000000;
	text-decoration: none;
	font-size: 24px;
}
a:active{
	font-family: arial;
	color: #000000;
	text-decoration: none;
	font-size: 24px;	
}    
span.welcome, td{
		font-size: 24px;
		font-family: arial;
		font-weight: bold;
		color: #000000;
		position: absolute;
		top: 65px;
		margin: auto;
		width: 100%;
}
span.menu, td{
		position: absolute;
		top: 150px;
		margin: auto;
		width: 100%;
}
span.logo, td{
		position: absolute;
		top: 120px;
		right: 0px;
}
span.nasalogo, td{
		position: absolute;
		top: 140px;
		left: 0px;
}
</style>
</head>
<?php
//include "includes/dispatch.css";
include "includes/dbconnection.php";
include "includes/version.php";
$version = version();
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
print "<body bgcolor='#ffffff'>";
print "<span class='welcome'><center>Welcome to Safety Dispatch v".$version."</center></span>";
print "<span class='nasalogo'><img src='/images/nasalogo.jpg'></span>";
print "<span class='logo'><img src='/images/logo.jpg'></span>";

print "<form name='truck' method='post' action='truckmanager.php'>";
print "<input type='hidden' name='event_id'>";
print "<input type='hidden' name='vehicle_id'>";

$event_query = "
	SELECT event.id AS evid, vehicle.id AS vid, *
	FROM track INNER JOIN (event INNER JOIN (vehicle INNER JOIN event_vehicle ON vehicle.ID = event_vehicle.vehicle_id) ON event.ID = event_vehicle.event_id) ON track.ID = event.track_id
	WHERE (((Now()) Between [begin_date] And dateadd('d',1,[end_date])))
	order by vehicle_name;
";
$event_result = $db->query($event_query);


print "<span class='menu'><center>";
print "<a href='truck.php'>Truck Login</a>";
print "<br><br><br>";
print "<a href='pace.php'>Pace Login</a>";
print "<br><br><br>";
print "<a href='dispatch.php'>Dispatch Login</a>";
print "<br><br><br>";
print "<a href='base.php'>Base Login</a>";
print "<br><br><br><br><hr><br>";
print "<a href='event.php'>Event Maintenance</a>";
print "<br><br><br>";
print "<a href='reports.php'>Event Reports</a>";
print "</center></span>";




print "</form>";

$db->connection = null;
?>

</body>
</html>