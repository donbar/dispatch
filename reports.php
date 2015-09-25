<script language="javascript" src="js/jquery-1.3.2.js" type="text/javascript"></script>

<?php
include "includes/reports.css";
include "includes/dbconnection.php";
include "includes/version.php";
include "includes/report_functions.php";
require_once dirname(__FILE__) . '/classes/PHPExcel.php';

$db = getdbconnection();
$version = version();


$track_query = "
	SELECT ID, track_name
	FROM track 
	order by track.track_name
";
$track_result = $db->query($track_query);


print "

	<script>

	function checkform(){
		if (document.reports.event){
			document.reports.event.value = 0;
		}
		document.reports.submit();
	}
	</script>

";

print "<form name='reports' method='post' action='reports.php'>";
print "<span class='welcome'><center>Welcome to Safety Dispatch v".$version." - Reporting Center</center></span><br>";
print "<span class='eventmenu'><input id='menu' type='button' class='overridebuttonactive' value='Menu' onclick='document.location=\"index.php\"'></span>";
print "<span class='excelmenu'><div id='excel' name='excel'></div></span>";
print "<br><br>";
print "<span class='selecttext'>Track: </span><select name='track' id='track' class='largetext' onchange='checkform();'>";
print "<option name='track' value=0 SELECTED>Select an Option</option>";

while ($track_row = $track_result->fetch(PDO::FETCH_ASSOC)) {
	if ($_POST['track'] == $track_row['ID']){
		$selected = ' SELECTED';
		$track_name = $track_row['track_name'];
	}else{
		$selected = '';
	}
	print "<option name='track' value='".$track_row['ID']."'" . $selected . ">".$track_row['track_name']."</option>";
}
print "</select>";
print "<br><br>";

if (isset($_POST['track']) && $_POST['track'] > 0){
	$event_query = "
		SELECT ID, format(begin_date,'mm/dd/yy') as begin_date_prty, format(end_date,'mm/dd/yy') as end_date_prty
		FROM event 
		where track_id = " . $_POST['track'] . "
		order by begin_date
	";
	$event_result = $db->query($event_query);

	print "<span class='selecttext'>Event: </span><select name='event' id='event' class='largetext' onchange='document.reports.submit();'>";
	print "<option name='event' value=0 SELECTED>Select an Option</option>";
	if ($_POST['event'] == -1){
		$selected = ' SELECTED';
		$event_name = 'All';
	}else{
		$selected = '';
	}
	print "<option name='event' value=-1 ". $selected . ">All</option>";

	while ($event_row = $event_result->fetch(PDO::FETCH_ASSOC)) {
		if ($_POST['event'] == $event_row['ID']){
			$selected = ' SELECTED';
			$event_name = $event_row['begin_date_prty']." - " . $event_row["end_date_prty"];
		}else{
			$selected = '';
		}
		print "<option name='event' value='".$event_row['ID']."'" . $selected . ">".$event_row['begin_date_prty']." - " . $event_row["end_date_prty"] . "</option>";
	}
	print "</select>";
	print "<br><br>";

	if (isset($_POST['event']) && $_POST['event'] <> 0){
		$track_id = $_POST['track'];
		$event_id = $_POST['event'];

		if ($event_id == -1){
			$totalcalls = "select count(*) as callcnt
							from incident left join event on incident.event_id = event.ID
							where event.track_id = " . $track_id;
		}else{
			$totalcalls = "select count(*) as callcnt
							from incident left join event on incident.event_id = event.ID
							where event.id = " . $event_id;
		}
		$call_result = $db->query($totalcalls);
		$call_row = $call_result->fetch(PDO::FETCH_ASSOC);
		$calltotal = $call_row['callcnt'];

	
		if ($event_id == -1){
			$totalcalls = "select count(*) as debriscnt
							from incident left join event on incident.event_id = event.ID
							where event.track_id = " . $track_id . " and debris = 1";
		}else{
			$totalcalls = "select count(*) as debriscnt
							from incident left join event on incident.event_id = event.ID
							where event.id = " . $event_id . " and debris = 1";
		}
		$call_result = $db->query($totalcalls);
		$call_row = $call_result->fetch(PDO::FETCH_ASSOC);
		$totaldebris = $call_row['debriscnt'];


		if ($event_id == -1){
			$totalcalls = "select count(*) as impactcnt
							from incident left join event on incident.event_id = event.ID
							where event.track_id = " . $track_id . " and impact = 1";
		}else{
			$totalcalls = "select count(*) as impactcnt
							from incident left join event on incident.event_id = event.ID
							where event.id = " . $event_id . " and impact = 1";
		}
		$call_result = $db->query($totalcalls);
		$call_row = $call_result->fetch(PDO::FETCH_ASSOC);
		$totalimpact = $call_row['impactcnt'];
		
		if ($event_id == -1){
			$totalcalls = "select count(*) as rollcnt
							from incident left join event on incident.event_id = event.ID
							where event.track_id = " . $track_id . " and rollover = 1";
		}else{
			$totalcalls = "select count(*) as rollcnt
							from incident left join event on incident.event_id = event.ID
							where event.id = " . $event_id . " and rollover = 1";
		}
		$call_result = $db->query($totalcalls);
		$call_row = $call_result->fetch(PDO::FETCH_ASSOC);
		$totalrollover = $call_row['rollcnt'];


		if ($event_id == -1){
			$totalcalls = "select count(*) as firecnt
							from incident left join event on incident.event_id = event.ID
							where event.track_id = " . $track_id . " and fire = 1";
		}else{
			$totalcalls = "select count(*) as firecnt
							from incident left join event on incident.event_id = event.ID
							where event.id = " . $event_id . " and fire = 1";
		}
		$call_result = $db->query($totalcalls);
		$call_row = $call_result->fetch(PDO::FETCH_ASSOC);
		$totalfires = $call_row['firecnt'];




		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Safety Dispatch")
									 ->setLastModifiedBy("Safety Dispatch")
									 ->setTitle("Safety Dispatch Report")
									 ->setSubject("Safety Dispatch Report")
									 ->setDescription("Safety Dispatch Report for testing")
									 ->setKeywords("safety dispatch")
									 ->setCategory("Safety Dispatch Report");

		$stylebold = array(
		        'font' => array(
		            'bold' => true,
		        )
		    );							 
		$styleboldunderline = array(
		        'font' => array(
		            'bold' => true,
		            'underline' => true,
		        )
		    );

		$objPHPExcel->getActiveSheet()->setCellValue('A1',"Safety Dispatch Report executed " .date('m/d/y H:i:s'));
		$objPHPExcel->getActiveSheet()->getStyle("A1")->applyFromArray($styleboldunderline);	

		$objPHPExcel->getActiveSheet()->setCellValue('A2',"Track:");
		$objPHPExcel->getActiveSheet()->getStyle("A2")->applyFromArray($stylebold);		
		$objPHPExcel->getActiveSheet()->setCellValue('B2',$track_name);


		$objPHPExcel->getActiveSheet()->setCellValue('E2',"Event:");
		$objPHPExcel->getActiveSheet()->getStyle("E2")->applyFromArray($stylebold);		
		$objPHPExcel->getActiveSheet()->setCellValue('F2',$event_name);

		$objPHPExcel->getActiveSheet()->setCellValue('A4',"# Calls:");
		$objPHPExcel->getActiveSheet()->getStyle("A4")->applyFromArray($stylebold);		
		$objPHPExcel->getActiveSheet()->setCellValue('B4',$calltotal);

		$objPHPExcel->getActiveSheet()->setCellValue('D4',"# Debris:");
		$objPHPExcel->getActiveSheet()->getStyle("D4")->applyFromArray($stylebold);				
		$objPHPExcel->getActiveSheet()->setCellValue('E4',$totaldebris);

		$objPHPExcel->getActiveSheet()->setCellValue('G4',"# Impact:");
		$objPHPExcel->getActiveSheet()->getStyle("G4")->applyFromArray($stylebold);		
		$objPHPExcel->getActiveSheet()->setCellValue('H4',$totalimpact);

		$objPHPExcel->getActiveSheet()->setCellValue('D5',"# Rollover:");
		$objPHPExcel->getActiveSheet()->getStyle("D5")->applyFromArray($stylebold);		
		$objPHPExcel->getActiveSheet()->setCellValue('E5',$totalrollover);

		$objPHPExcel->getActiveSheet()->setCellValue('G5',"# Fire:");
		$objPHPExcel->getActiveSheet()->getStyle("G5")->applyFromArray($stylebold);		
		$objPHPExcel->getActiveSheet()->setCellValue('H5',$totalfires);


		print "<table border=1 width='100%'>";
		print "<tr><td align='center'># Calls</td>
		<td align='center'># Debris</td>
		<td align='center'># Impact</td>
		<td align='center'># Rollover</td>
		<td align='center'># Fire</td></tr>";
		print "<tr>";
		print "<td align='center'>". $calltotal . "</td>";
		print "<td align='center'>". $totaldebris . "</td>";
		print "<td align='center'>". $totalimpact . "</td>";
		print "<td align='center'>". $totalrollover . "</td>";
		print "<td align='center'>". $totalfires . "</td>";
		print "</tr></table>";
		print "<br><br>";


		if ($event_id == -1){
			$truck_query = "
					SELECT distinct vehicle.ID, vehicle.vehicle_name
					FROM track INNER JOIN (event INNER JOIN (vehicle INNER JOIN event_vehicle ON vehicle.ID = event_vehicle.vehicle_id) ON event.ID = event_vehicle.event_id) ON track.ID = event.track_id
					WHERE track.ID = " . $track_id . "
					order by vehicle_name;
				";
		}else{
			$truck_query = "
					SELECT distinct vehicle.ID, vehicle.vehicle_name
					FROM track INNER JOIN (event INNER JOIN (vehicle INNER JOIN event_vehicle ON vehicle.ID = event_vehicle.vehicle_id) ON event.ID = event_vehicle.event_id) ON track.ID = event.track_id
					WHERE event.ID = " . $event_id . "
					order by vehicle_name;
				";
		}	
		$truck_result = $db->query($truck_query);
		$session = rand(1,5000000);

		print "<table width='100%' border=1>";
		print "<tr style='font-weight: bold;'><td>&nbsp;</td><td align='center'>Acknowledge</td><td align='center'>On Scene</td><td align='center'>Clear Scene</td><td align='center'>Off Course</td><td align='center'>Available</td></tr>";
		print "<tr style='font-weight: bold;'><td>Truck</td><td align='center'>Median / Avg</td><td align='center'>Median / Avg</td><td align='center'>Median / Avg</td><td align='center'>Median / Avg</td><td align='center'>Median / Avg</td></tr>";


	
		$style = array(
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        )
		    );
		$objPHPExcel->getActiveSheet()->setCellValue('B7',"Acknowledge");
		$objPHPExcel->getActiveSheet()->mergeCells('B7:C7');
		$objPHPExcel->getActiveSheet()->setCellValue('D7',"On Scene");
		$objPHPExcel->getActiveSheet()->mergeCells('D7:E7');
		$objPHPExcel->getActiveSheet()->setCellValue('F7',"Clear Scene");
		$objPHPExcel->getActiveSheet()->mergeCells('F7:G7');
		$objPHPExcel->getActiveSheet()->setCellValue('H7',"Off Course");
		$objPHPExcel->getActiveSheet()->mergeCells('H7:I7');
		$objPHPExcel->getActiveSheet()->setCellValue('J7',"Available");
		$objPHPExcel->getActiveSheet()->mergeCells('J7:K7');
		$objPHPExcel->getActiveSheet()->getStyle("B7:K7")->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->getStyle("B7:K7")->applyFromArray($stylebold);

		$objPHPExcel->getActiveSheet()->setCellValue('A8',"Truck");
		$objPHPExcel->getActiveSheet()->setCellValue('B8',"Median");
		$objPHPExcel->getActiveSheet()->setCellValue('C8',"Average");

		$objPHPExcel->getActiveSheet()->setCellValue('D8',"Median");
		$objPHPExcel->getActiveSheet()->setCellValue('E8',"Average");

		$objPHPExcel->getActiveSheet()->setCellValue('F8',"Median");
		$objPHPExcel->getActiveSheet()->setCellValue('G8',"Average");

		$objPHPExcel->getActiveSheet()->setCellValue('H8',"Median");
		$objPHPExcel->getActiveSheet()->setCellValue('I8',"Average");

		$objPHPExcel->getActiveSheet()->setCellValue('J8',"Median");
		$objPHPExcel->getActiveSheet()->setCellValue('K8',"Average");
		$objPHPExcel->getActiveSheet()->getStyle("A8:K8")->applyFromArray($stylebold);

		$row = 8;

		while ($truck_row = $truck_result->fetch(PDO::FETCH_ASSOC)) {

			$vehicle_id = $truck_row['ID'];
			$medianack = median($session, $event_id, $track_id, $vehicle_id, 'reported_time','tm_acknowledge');
			$averageack = average($session, $event_id, $track_id, $vehicle_id,'reported_time','tm_acknowledge');
			$medianon = median($session, $event_id, $track_id, $vehicle_id, 'tm_acknowledge','tm_onscene');
			$averageon = average($session, $event_id, $track_id, $vehicle_id, 'tm_acknowledge','tm_onscene');
			$medianclear = median($session, $event_id, $track_id, $vehicle_id,'tm_onscene','tm_clear');
			$averageclear = average($session, $event_id, $track_id, $vehicle_id,'tm_onscene','tm_clear');
			$medianoff = median($session, $event_id, $track_id, $vehicle_id,'tm_clear','tm_offcourse');
			$averageoff = average($session, $event_id, $track_id, $vehicle_id,'tm_clear','tm_offcourse');
			$medianavail = median($session, $event_id, $track_id, $vehicle_id,'tm_offcourse','tm_available');
			$averageavail = average($session, $event_id, $track_id, $vehicle_id,'tm_offcourse','tm_available');
		
			
			print "<td>" . $truck_row['vehicle_name'] . "</td>";
			print "<td align='center'>" . $medianack . " / " . $averageack . "</td>";
			print "<td align='center'>" . $medianon . " / " . $averageon . "</td>";
			print "<td align='center'>" . $medianclear . " / " . $averageclear . "</td>";
			print "<td align='center'>" . $medianoff . " / " . $averageoff . "</td>";
			print "<td align='center'>" . $medianavail . " / " . $averageavail . "</td>";				
			print "</tr>";

			$row++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$truck_row['vehicle_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$medianack);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$averageack);

			$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,$medianon);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$averageon);

			$objPHPExcel->getActiveSheet()->setCellValue('F'.$row,$medianclear);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$row,$averageclear);

			$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,$medianoff);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$row,$averageoff);

			$objPHPExcel->getActiveSheet()->setCellValue('J'.$row,$medianavail);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$row,$averageavail);			
		}
		print "</table>";
		
		if ($event_id == -1){
			$corners = "
				SELECT Count(*) as cnt, track_config_input as turn
								FROM incident
					inner join event on incident.event_id = event.id
								where track_id = ".$track_id . "
								group by track_config_input
								order by 1 desc";
		}else{

			$corners = "
			SELECT Count(*) as cnt, track_config_input as turn
				FROM incident
				where event_id = " . $event_id . "
				group by track_config_input
				order by 1 desc";
		}
		$corner_result = $db->query($corners);
		print "<br><br>";
		print "<table border=1>";
		print "<tr><td>Turn #</td><td>Count of Incidents</td></tr>";

		$row = $row + 2;

		$style = array(
		        'font' => array(
		            'bold' => true,
		        )
		    );		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "Turn #");
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,"Count of Incidents");
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':B'.$row)->applyFromArray($style);			


		$style = array(
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		        )
		    );

		while ($corner_row = $corner_result->fetch(PDO::FETCH_ASSOC)) {
			print "<tr><td>".$corner_row['turn']."</td><td align='right'>".$corner_row['cnt']."</td></tr>";

			$row++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $corner_row['turn']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$corner_row['cnt']);	
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->applyFromArray($style);	


		}
		print "</table>";


		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('temp/'.$session.'.xlsx'); 
		print "
		<script>
		document.getElementById('excel').innerHTML = ".chr(34)."<a href='temp/".$session.".xlsx' style='color:red'>Click here for Excel!</a>".chr(34).";
		</script>
		";



	} # end of if event exists
} # end of if track exists
?>
</body>
</html>
