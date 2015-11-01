<!DOCTYPE html>
<html>

<?php
$event_id = $_REQUEST['event_id'];

$vehicle_id = $_REQUEST['vehicle_id'];

$track_id = $_REQUEST['track_id'];

include "includes/dispatch.css";
include "includes/dbconnection.php";
$db = getdbconnection();
?>

<head>
<script language="javascript" src="js/jquery-1.3.2.js" type="text/javascript"></script>

<script>
var wavingyellowTimeout;
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



function getGlobalCommand() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					eval(xmlhttp.responseText);
                }else if((xmlhttp.status == 0 || xmlhttp.status == 404) && xmlhttp.readyState == 4){
                    // lost connection with server, clear screen and show oopsies div
                    hideDoubleYellowFlag();
                    hideSafetyFlag();
                    hideGreenFlag();
                    hideRestartFlag();
                    hideRedFlag();
                    hideWhiteFlag();
                    hideBlackFlag();
                    hideStandingYellowFlag();
                    hideDebrisFlag();
                    hidewavingYellow();
                    hideCheckeredFlag();
                }
            }
            var url = 'https://trackflag.nasasafety.com/server/statusajax.php?track_id=<?php print $track_id ?>';
            xmlhttp.open("GET", url, true);
            xmlhttp.send();

        randomrefresh = 2 * 1000;
        setTimeout(getGlobalCommand, randomrefresh );
}

function checkforGlobalCommand() {

            if ( document.getElementById("runscript").innerHTML != "" ) {
                    eval(document.getElementById("runscript").innerHTML);
                    document.getElementById("runscript").innerHTML = '';
            }
       randomrefresh = 500;
       setTimeout(checkforGlobalCommand, randomrefresh );
}


</script>
</head>

<?php

/* If we are coming in from a POST, let's save the data quickly */


if ($_REQUEST['actionbtn']	== -100){
	/* mark as away */
	$update_status_query = "update event_vehicle set notavailable = 1 where vehicle_id = ". $vehicle_id . " and event_id = " . $event_id;
	$insert_veh_result = $db->query($update_status_query);
}

if ($_REQUEST['actionbtn']	== -101){
	/* mark as available again */
	$update_status_query = "update event_vehicle set notavailable = 0 where vehicle_id = ". $vehicle_id . " and event_id = " . $event_id;
	$insert_veh_result = $db->query($update_status_query);
}
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


print "<body onload='getGlobalCommand(); checkforGlobalCommand(); document.truck.postit.value= 0;showStatus(".$event_id.",".$vehicle_id.");showMessage(".$event_id.",".$vehicle_id.")'; >";
print "<form name='truck' method='post'>";
print "<div id='runscript' style='display: none'></div>
  		<div id='runscriptlocal' style='display: none'></div>";

print "<input type='hidden' name='actionbtn' value=0>";
print "<input type='hidden' name='event_id' value='".$event_id."'>";
print "<input type='hidden' name='vehicle_id' value='".$vehicle_id."'>";
print "<input type='hidden' name='track_id' value='".$track_id."'>";
print "<input type='hidden' name='postit' value=0>";

print "<span id='global' class='globalflags'>";
print "	<div id='globalflags'>
		          <canvas id='redFlag'></canvas><br>
		          <canvas id='greenFlag'></canvas><br>
		          <canvas id='whiteFlag'></canvas><br>
		          <canvas id='blackFlag'></canvas><br>
		          <canvas id='safetyFlag'></canvas><br>
		          <canvas id='restartFlag'></canvas><br>
		          <canvas id='doubleyellowFlag'></canvas>
		          <canvas id='checkeredFlag' class='checkered' style='display:none'></canvas>
		         </div>
		    	";
print "</span>";
print "<span id='txtStatus'></span>";
print "<span id='txtMessage'></span>";
print "<span id='local' class='localflags'>
		  <div id='localflags'>
          <canvas id='standingYellow' style='display:none'></canvas><br>
          <canvas id='debrisFlag' style='display:none'></canvas><br>
          <canvas id='wavingYellow' style='display:none'></canvas>
          </div>
          </span>";

print "</form>";

print "
 <div id='gps' style='display:block; visibility:hidden'>
  <iframe src='gps.php?event_id=".$event_id."&vehicle_id=".$vehicle_id."' height='250px' width='250px' ></iframe>
</div>
";


$db->connection = null;
?>

<script>
function hideRedFlag(){
    document.getElementById("redFlag").style.display = "none";
}

function showRedFlag(scale){
    document.getElementById("redFlag").style.display = "inline-block";

    var canvas = document.getElementById('redFlag');
    var context = canvas.getContext('2d');

    var clientHeight = 50;
    var clientWidth = 100;

    // resize the canvas
    canvas.height = clientHeight;
    canvas.width = clientWidth;

    context.beginPath();
    context.rect(0, 0, clientWidth, clientHeight);
    context.fillStyle = 'red';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'black';
    context.stroke();


    var textString = 'RED';
    var fontsize = (clientWidth/textString.length);
    var divisor = '1.'+textString.length.toString();
    var textheight = (clientHeight / divisor);

    var ctx = canvas.getContext("2d");
    ctx.font=fontsize.toString()+"px Verdana";
    context.fillStyle = 'white';
    textWidth = ctx.measureText(textString ).width;
    //ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), textheight);
}


function hideDoubleYellowFlag(){
    document.getElementById("doubleyellowFlag").style.display = "none";
}

function showDoubleYellowFlag(scale){

    document.getElementById("doubleyellowFlag").style.display = "inline-block";

    var canvas = document.getElementById('doubleyellowFlag');
    var context = canvas.getContext('2d');

    var clientHeight = 50;
    var clientWidth = 100;

    // resize the canvas
    canvas.height = clientHeight;
    canvas.width = clientWidth;

    context.beginPath();
    context.rect(0, 0, clientWidth, clientHeight);
    context.fillStyle = 'yellow';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'black';
    context.stroke();


    var textString = 'YELLOW';
    var fontsize = (clientWidth/textString.length);
    var divisor = '1.'+textString.length.toString();
    var textheight = (clientHeight / divisor);

    var ctx = canvas.getContext("2d");
    ctx.font=fontsize.toString()+"px Verdana";
    context.fillStyle = 'black';
    textWidth = ctx.measureText(textString ).width;
    //ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), textheight);
}



function hideGreenFlag(){
    document.getElementById("greenFlag").style.display = "none";
}

function showGreenFlag(scale){
    document.getElementById("greenFlag").style.display = "inline-block";

    var canvas = document.getElementById('greenFlag');
    var context = canvas.getContext('2d');

    var clientHeight = 50;
    var clientWidth = 100;

    // resize the canvas
    canvas.height = clientHeight;
    canvas.width = clientWidth;

    context.beginPath();
    context.rect(0, 0, clientWidth, clientHeight);
    context.fillStyle = 'green';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'black';
    context.stroke();


    var textString = 'GREEN';
    var fontsize = (clientWidth/textString.length);
    var divisor = '1.'+textString.length.toString();
    var textheight = (clientHeight / divisor);

    var ctx = canvas.getContext("2d");
    ctx.font=fontsize.toString()+"px Verdana";
    context.fillStyle = 'white';
    textWidth = ctx.measureText(textString ).width;
    //ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), textheight);
}

function hideWhiteFlag(){
    document.getElementById("whiteFlag").style.display = "none";
}

function showWhiteFlag(scale){
    document.getElementById("whiteFlag").style.display = "inline-block";

    var canvas = document.getElementById('whiteFlag');
    var context = canvas.getContext('2d');

    var clientHeight = 50;
    var clientWidth = 100;

    // resize the canvas
    canvas.height = clientHeight;
    canvas.width = clientWidth;

    context.beginPath();
    context.rect(0, 0, clientWidth, clientHeight);
    context.fillStyle = 'white';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'black';
    context.stroke();

    var textString = 'WHITE';
    var fontsize = (clientWidth/textString.length);
    var divisor = '1.'+textString.length.toString();
    var textheight = (clientHeight / divisor);

    var ctx = canvas.getContext("2d");
    ctx.font=fontsize.toString()+"px Verdana";
    context.fillStyle = 'black';
    textWidth = ctx.measureText(textString ).width;
    //ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), textheight);

}

function hideBlackFlag(){
    document.getElementById("blackFlag").style.display = "none";
}

function showBlackFlag(scale){
    document.getElementById("blackFlag").style.display = "inline-block";

    var canvas = document.getElementById('blackFlag');
    var context = canvas.getContext('2d');

    var clientHeight = 50;
    var clientWidth = 100;

    // resize the canvas
    canvas.height = clientHeight;
    canvas.width = clientWidth;

    context.beginPath();
    context.rect(0, 0, clientWidth, clientHeight);
    context.fillStyle = 'black';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'white';
    context.stroke();


    var textString = 'BLACK';
    var fontsize = (clientWidth/textString.length);
    var divisor = '1.'+textString.length.toString();
    var textheight = (clientHeight / divisor);

    var ctx = canvas.getContext("2d");
    ctx.font=fontsize.toString()+"px Verdana";
    context.fillStyle = 'white';
    textWidth = ctx.measureText(textString ).width;
    //ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), textheight);

}

function hideSafetyFlag(){
    document.getElementById("safetyFlag").style.display = "none";
}

function showSafetyFlag(scale){
    document.getElementById("safetyFlag").style.display = "inline-block";

    var canvas = document.getElementById('safetyFlag');
    var context = canvas.getContext('2d');
    var clientHeight = 50;
    var clientWidth = 100;

    // resize the canvas
    canvas.height = clientHeight;
    canvas.width = clientWidth;

    context.beginPath();
    context.rect(0, 0, clientWidth, clientHeight);
    context.fillStyle = 'white';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'black';
    context.stroke();

    var c=document.getElementById("safetyFlag");
    var ctx=c.getContext("2d");
    ctx.fillStyle = 'red';
    ctx.lineWidth = 1;
    ctx.strokeStyle = 'red';
    var barwidth = clientWidth * .05;
    ctx.fillRect(clientWidth/2 - (barwidth/2),0,barwidth,clientHeight);

    // Red Cross for Safety flag
    var c=document.getElementById("safetyFlag");
    var ctx=c.getContext("2d");
    ctx.fillStyle = 'red';
    ctx.lineWidth = 1;
    ctx.strokeStyle = 'red';
    ctx.fillRect(0,clientHeight/2 - (barwidth/2),clientWidth,barwidth);


    var textString = 'SAFETY';
    var fontsize = (clientWidth/textString.length);
    var divisor = '1.'+textString.length.toString();
    var textheight = (clientHeight / divisor);

    var ctx = canvas.getContext("2d");
    ctx.font= fontsize.toString()+"px Verdana";
    context.fillStyle = 'black';
    textWidth = ctx.measureText(textString ).width;
    //ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), textheight);
}


function hideRestartFlag(){
    document.getElementById("restartFlag").style.display = "none";
}

function showRestartFlag(scale){
    document.getElementById("restartFlag").style.display = "inline-block";

    var canvas = document.getElementById('restartFlag');
    var context = canvas.getContext('2d');

    var clientHeight = 50;
    var clientWidth = 100;

    // resize the canvas
    canvas.height = clientHeight;
    canvas.width = clientWidth;

    context.beginPath();
    context.rect(0, 0, clientWidth/2, clientHeight);
    context.fillStyle = 'red';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'black';
    context.stroke();

    context.beginPath();
    context.rect(clientWidth/2, 0, clientWidth/2, clientHeight);
    context.fillStyle = 'yellow';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'black';
    context.stroke();

    var textString = 'RESTART';
    var fontsize = (clientWidth/textString.length);
    var divisor = '1.'+textString.length.toString();
    var textheight = (clientHeight / divisor);

    var ctx = canvas.getContext("2d");
    ctx.font=fontsize.toString()+"px Verdana";
    context.fillStyle = 'black';
    textWidth = ctx.measureText(textString ).width;
    //ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), textheight);

}

// Local Flags
function hideStandingYellowFlag(){
    document.getElementById("standingYellow").style.display = "none";
}

function showStandingYellowFlag(turn, scale){
    document.getElementById("standingYellow").style.display = "inline-block";

    var canvas = document.getElementById('standingYellow');
    var context = canvas.getContext('2d');

    var clientHeight = 100;
    var clientWidth = 100;

    // resize the canvas
    canvas.height = clientHeight;
    canvas.width = clientWidth;

    context.beginPath();
    context.rect(0, 0, clientWidth, clientHeight);
    context.fillStyle = 'yellow';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'black';
    context.stroke();


    if ( turn ) {
            var artificiallength = turn.length + 7;
            var divisor = '1.'+artificiallength.toString();
            var divisor = 3.0;            
            var textheight = (clientHeight / divisor);
            var textString = turn.trim();
            var ctx = canvas.getContext("2d");
            ctx.font= textheight.toString()+"px Verdana";
            context.fillStyle = 'black';
            textWidth = ctx.measureText(textString ).width;
            ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), (clientHeight) - (textheight/2));
    }


}

function hideCheckeredFlag(){
    document.getElementById("checkeredFlag").style.display = "none";
}

function showCheckeredFlag(scale){
    document.getElementById("checkeredFlag").style.display = "inline-block";

    var canvas = document.getElementById('checkeredFlag');
    var context = canvas.getContext('2d');

    var clientHeight = 50;
    var clientWidth = 100;

    // resize the canvas
    canvas.height = clientHeight;
    canvas.width = clientWidth;

    canvas.style = 'checkered';
}

function hideDebrisFlag(scale){
    document.getElementById("debrisFlag").style.display = "none";

}

function showDebrisFlag(turn, scale){
    var canvas = document.getElementById('debrisFlag');
    var context = canvas.getContext('2d');

    document.getElementById("debrisFlag").style.display = "inline-block";

    var canvas = document.getElementById('debrisFlag');
    var context = canvas.getContext('2d');
    var clientHeight = 100;
    var clientWidth = 100;


    // resize the canvas
    canvas.height = clientHeight;
    canvas.width = clientWidth;

    var barwidth = clientWidth / 10;

    context.beginPath();
    context.rect(0, 0, clientWidth, clientHeight);
    context.fillStyle = 'yellow';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'red';
    context.stroke();

    var c=document.getElementById("debrisFlag");
    var ctx=c.getContext("2d");
    ctx.fillStyle = 'red';
    ctx.lineWidth = 1;
    ctx.strokeStyle = 'red';
    ctx.fillRect(barwidth,0,barwidth,clientHeight);

    var ctx=c.getContext("2d");
    ctx.fillStyle = 'red';
    ctx.lineWidth = 1;
    ctx.strokeStyle = 'red';
    ctx.fillRect(barwidth * 3,0,barwidth,clientHeight);

    var ctx=c.getContext("2d");
    ctx.fillStyle = 'red';
    ctx.lineWidth = 1;
    ctx.strokeStyle = 'red';
    ctx.fillRect(barwidth * 5,0,barwidth,clientHeight);

    var ctx=c.getContext("2d");
    ctx.fillStyle = 'red';
    ctx.lineWidth = 1;
    ctx.strokeStyle = 'red';
    ctx.fillRect(barwidth * 7,0,barwidth,clientHeight);

    var ctx=c.getContext("2d");
    ctx.fillStyle = 'red';
    ctx.lineWidth = 1;
    ctx.strokeStyle = 'red';
    ctx.fillRect(barwidth * 9,0,barwidth,clientHeight);


    if ( turn ) {

            var artificiallength = turn.length + 7;
            var divisor = '1.'+artificiallength.toString();
            var divisor = 3.0;            
            var textheight = (clientHeight / divisor);
            var textString = turn.trim();
            var ctx = c.getContext("2d");
            ctx.font= textheight.toString()+"px Verdana";
            context.fillStyle = 'black';
            textWidth = ctx.measureText(textString ).width;
            ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), (clientHeight) - (textheight/2));
    }

}

function hidewavingYellow(scale){
    document.getElementById("wavingYellow").style.display = "none";
    var canvas = document.getElementById('wavingYellow');
    ctx = canvas.getContext('2d');
    var clientHeight = 50;
    var clientWidth = 100;
    ctx.clearRect(0, 0, clientWidth, clientHeight);
    clearTimeout(wavingyellowTimeout);
}

function showwavingYellow(turn, scale){
    if (document.getElementById("wavingYellow").style.display == "none"){
        document.getElementById("wavingYellow").style.display = "inline-block";
        blinkwavingYellow(turn, scale, 1);
    }
}

function blinkwavingYellow(turn, scale, even){
        if (even == 1)
            {
               even = 0;
                var canvas = document.getElementById('wavingYellow');
                var context = canvas.getContext('2d');

                    var clientHeight = 100;
  					  var clientWidth = 100;

                // resize the canvas
                canvas.height = clientHeight;
                canvas.width = clientWidth;

                var fillcolor;
                var othercolor;
                fillcolor = 'yellow';
                othercolor = '#c0c0c0';


                context.beginPath();
                context.clearRect(0, 0, clientWidth/2, clientHeight);
                context.rect(0, 0, clientWidth/2, clientHeight);
                context.fillStyle = fillcolor;
                context.fill();
                context.lineWidth = 1;
                context.strokeStyle = 'black';
                context.stroke();

                context.beginPath();
                context.clearRect(clientWidth/2, 0, clientWidth/2, clientHeight);
                context.rect(clientWidth/2, 0, clientWidth/2, clientHeight);
                context.fillStyle = othercolor;
                context.fill();
                context.lineWidth = 1;
                context.strokeStyle = 'black';
                context.stroke();    

                if ( turn ) {
                        var artificiallength = turn.length + 7;
                        var divisor = '1.'+artificiallength.toString();
                        var divisor = 3.0;            
                        var textheight = (clientHeight / divisor);
                        var textString = turn.trim();
                        var ctx = canvas.getContext("2d");
                        ctx.font= textheight.toString()+"px Verdana";
                        context.fillStyle = 'black';
                        textWidth = ctx.measureText(textString ).width;
                        ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), (clientHeight) - (textheight/2));
                }            

            } else{
                even = 1;
                var canvas = document.getElementById('wavingYellow');
                var context = canvas.getContext('2d');//
			    var clientHeight = 100;
			    var clientWidth = 100;

                // resize the canvas
                canvas.height = clientHeight;
                canvas.width = clientWidth;

                var fillcolor;
                var othercolor;
                fillcolor = '#c0c0c0';
                othercolor = 'yellow';


                context.beginPath();
                context.clearRect(0, 0, clientWidth/2, clientHeight);
                context.rect(0, 0, clientWidth/2, clientHeight);
                context.fillStyle = fillcolor;
                context.fill();
                context.lineWidth = 1;
                context.strokeStyle = 'black';
                context.stroke();

                context.beginPath();
                context.clearRect(clientWidth/2, 0, clientWidth/2, clientHeight);
                context.rect(clientWidth/2, 0, clientWidth/2, clientHeight);
                context.fillStyle = othercolor;
                context.fill();
                context.lineWidth = 1;
                context.strokeStyle = 'black';
                context.stroke();
                if ( turn ) {
                        var artificiallength = turn.length + 7;
                        var divisor = '1.'+artificiallength.toString();
                        var divisor = 3.0;            
                        var textheight = (clientHeight / divisor);
                        var textString = turn.trim();
                        var ctx = canvas.getContext("2d");
                        ctx.font= textheight.toString()+"px Verdana";
                        context.fillStyle = 'black';
                        textWidth = ctx.measureText(textString ).width;
                        ctx.fillText(textString , (clientWidth/2) - (textWidth / 2), (clientHeight) - (textheight/2));
                }                 
            }
            
randomrefresh = 500;
wavingyellowTimeout = setTimeout(blinkwavingYellow, randomrefresh, turn, scale, even);    
        
}

</script>
</body>
</html>