<?php
include "includes/dbconnection.php";
$db = getdbconnection();

$event_id = $_REQUEST['event_id'];
$vehicle_id = $_REQUEST['vehicle_id'];

$message_query = "
select vehicle_name, lat, lon, icon from (vehicle inner join event_vehicle on event_vehicle.vehicle_id = vehicle.ID) 
	where event_id = " . $event_id . " and vehicle_id = " . $vehicle_id;
$message_result = $db->query($message_query);
$message_row = $message_result->fetch(PDO::FETCH_ASSOC);
$prim_lat = $message_row['lat'];
$prim_lon = $message_row['lon'];
$prim_vehicle_name = $message_row['vehicle_name'];
$prim_icon = $message_row['icon'];

?>

<!DOCTYPE xhtml PUBLIC "-//W3C//DTD XHTML 4.01//EN">
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<script language="javascript" src="js/jquery-1.3.2.js" type="text/javascript"></script>
<script type="text/javascript"
  src="http://maps.google.com/maps/api/js?sensor=true"></script>
<style type="text/css">
  *, html { margin:0; padding:0 }
  div#map_canvas { width:100%; height:100%; }
</style>
<script type="text/javascript">

  $(document).ready(function() {// When the Dom is ready
  });


  setInterval(function() {
          updatealltrucks();
            }, 1000); 

  function updatealltrucks(){
      var cntr = document.getElementById('counter').value;
      for (var i =1; i < cntr; i++){
        lat = '';
        lon = '';
        var truck = 'truck'+i;

        if (document.getElementById(truck) != null){

          var mark = 'marker'+i;
          var latvar = 'lat'+i;
          var lonvar = 'lon'+i;
          var lastseen = 'timer'+i;
          //var lastseentmr = document.getElementById(lastseen).value;
          var vehid = document.getElementById(truck).value;
          var lat = document.getElementById(latvar).value;
          var lon = document.getElementById(lonvar).value;

          if(lat != '' && lon != '' ){
            position = new google.maps.LatLng(lat, lon);
            eval('marker'+i).setPosition(position);
            if (vehid == <?php print $vehicle_id ?>){
                map.setCenter(position);
            }
          }
        }
      }
  }


  function initialise() {
    var latlng = new google.maps.LatLng(<?php print $prim_lat . "," . $prim_lon ?>);
    var myOptions = {
      zoom: 4,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: true,
      mapTypeControl: true,
      scaleControl: true,
      zoomControl: true
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    map.setZoom(17);

    <?php $var = 'marker'.$vehicle_id ?>
     <?php print $var ?> = new google.maps.Marker({
	    map: map,
	    position: latlng,
	    title: '<?php print $prim_vehicle_name ?>',
	    icon: '<?php print $prim_icon ?>'
    });
    <?php print $var?>.setAnimation(google.maps.Animation.BOUNCE);

<?php
  	$message_query = "
  	select vehicle.ID as vehid, vehicle_name, lat, lon, icon from (vehicle inner join event_vehicle on event_vehicle.vehicle_id = vehicle.ID) 
  		where event_id = " . $event_id; /* . " and vehicle_id <> " . $vehicle_id; */
  	$message_result = $db->query($message_query);
    $counter = 0;
  	while ($message_row = $message_result->fetch(PDO::FETCH_ASSOC)){
      $counter++;
    	$lat = $message_row['lat'];
    	$lon = $message_row['lon'];
    	$vehicle_name = $message_row['vehicle_name'];
    	$icon = $message_row['icon'];
      $vehid = $message_row['vehid'];

      $marker = 'marker'.$counter;
      if ($vehid > 0 && $vehid <> $vehicle_id && $lat && $lon){
      	print "
      	var latlng = new google.maps.LatLng(".$lat . "," . $lon . ");
          ".$marker." = new google.maps.Marker({
      	    map: map,
      	    position: latlng,
      	    title: '$vehicle_name',
      	    icon: '$icon'
          });
      	";
      }
    }
?>
  }

  function updateLocation(event,vehicle) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtGPS").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "mapajax.php?event_id=<?php print $event_id ?>", true);
        xmlhttp.send();
        randomrefresh = Math.floor((Math.random() * 5) + 3) * 1000;
        setTimeout(updateLocation, randomrefresh );
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

</script>
</head>
<body onload='showStatus(<?php print $event_id ?>);updateLocation(<?php print $event_id ?>);'>

<?php
print "<form name='dispatch' method='post' action='map.php'>";
print "<input type='hidden' name='event_id' value='".$event_id."'>";
print "<input type='hidden' name='vehicle_id' value='".$vehicle_id."'>";
print "<input type='hidden' name='postit' value=0>";
?>

<span class='history'><input id='history' type='button' class='overridebuttoninactive' value='Dispatch' onclick='document.dispatch.action="dispatchmanager.php";document.dispatch.submit();'>
</span>

  <span id='txtStatus'></span>
  <div id="map_canvas"></div>
  <script>
  initialise();
  </script>
  <span id='txtGPS'></span>
</form>
</body>
</html>

<?php
$db->connection = null;
?>