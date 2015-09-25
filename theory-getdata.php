<?php
/* This is a test file for cross-domain AJAX/JSON */
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");


if ($_GET['type'] == 'client'){
    $clients =
        array(
            array(
                    "clientID" => "1",
                    "clientName" => "Don",
                    ),
            array(
                    "clientID" => "2",
                    "clientName" => "Roger",
                    )            
            );

             echo $_GET['callback'] . '(' . json_encode($clients) . ')';
}


if ($_GET['type'] == 'matter' && $_GET['client'] == 1){
    $matters =
        array(
            array(
                    "matterID" => "1",
                    "matterName" => "MatDon2",
                    )
            );

             echo $_GET['callback'] . '(' . json_encode($matters) . ')';
}
if ($_GET['type'] == 'matter' && $_GET['client'] == 2){
    $matters =
        array(
            array(
                    "matterID" => "1",
                    "matterName" => "MatRoger2",
                    )
            );

             echo $_GET['callback'] . '(' . json_encode($matters) . ')';
}

cors();
?>
