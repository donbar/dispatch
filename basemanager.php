<!DOCTYPE html>
<html>

<?php
$event_id = $_REQUEST['event_id'];

include "includes/dispatch.css";
include "includes/dbconnection.php";
$db = getdbconnection();

?>


<head>
<script language="javascript" src="js/jquery-1.3.2.js" type="text/javascript"></script>
<script>

$(document).ready(function() {// When the Dom is ready
});

function showStatus(event) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtStatus").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "basemanagerajax.php?event_id=<?php print $event_id ?>", true);
        xmlhttp.send();
        randomrefresh = Math.floor((Math.random() * 5) + 3) * 1000;
        setTimeout(showStatus, randomrefresh );
}


</script>
</head>

<?php

/* If we are coming in from a POST, let's save the data quickly */

if ($_POST['actionbtn'] > 0){



}


print "<body onload='document.truck.postit.value= 0;showStatus(".$event_id.");'>";
print "<form name='truck' method='post'>";
print "<input type='hidden' name='actionbtn' value=0>";
print "<input type='hidden' name='event_id' value='".$event_id."'>";

print "<input type='hidden' name='postit' value=0>";
print "<span class='welcome'>Safety Dispatch - Base Station</span>";
print "<span id='txtStatus'></span>";

print "</form>";


$db->connection = null;
?>

</body>
</html>