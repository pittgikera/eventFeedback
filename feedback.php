<?php

if (!empty($_POST)) {
    require_once('_con.php');
    require_once('AfricasTalkingGateway.php');

    //receiving the POST from AT
    $sessionId = $_POST['sessionId'];
    $serviceCode = $_POST['serviceCode'];
    $phoneNumber = $_POST['phoneNumber'];
    $text = $_POST['text'];

    //Explode to get the value of the latest interaction think 1*1
    $textArray = explode('*', $text);
    $userResponse = trim(end($textArray));

    //Check the level
    $level = 0;
    $sql = "SELECT `level` FROM `session_levels` WHERE `session_id`='" . $sessionId . "'";
    $levelQuery = $db->query($sql);


    if ($result = $levelQuery->fetch_assoc()) {
        $level = $result['level'];
    }

//check if user is not in db
    $firstQuery = "SELECT * FROM users WHERE `phonenumber` LIKE '%" . $phoneNumber . "%' LIMIT 1";
    $firstResult = $db->query($firstQuery);
    $userAvail = $firstResult->fetch_assoc();
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //Print the response onto the page so that the ussd API/gateway can read it
    header("Content-type: text/plain");
    echo $response;
}//end of code