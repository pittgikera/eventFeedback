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

    if ($level == 0){
        //We graduate the user to level 1
        $insertQuery = "INSERT INTO `session_levels`(`session_id`, `phoneNumber`,`level`) VALUES('" . $sessionId . "','" . $phoneNumber . "', 1)";
        $db->query($insertQuery);

        //We show the user the home menu
        $response = "CON Welcome to Event Feedback. \n";
        $response .= "1. Give feedback \n";
        $response .= "2. Register Event \n";
        $response .= "3. My Registered Events \n";
    }else if ($level == 1){
        //If user sends back nothing, we resend home menu
        if ($userResponse == "") {
            //We show the user the home menu
            $response = "CON Please pick an option to proceed. \n";
            $response .= "1. Give feedback \n";
            $response .= "2. Register Event \n";
            $response .= "3. My Registered Events \n";
        } else {
            $newLevel = 2;
            switch ($userResponse){
                //user wants to give feedback to an event
                //user wants to register an event
                case "1":
                case "2":
                    $response = "CON Enter the name of the event:";
                    break;

                //user wants to view their registered events
                case "3":
                    $sql = "SELECT * FROM `events` WHERE `phonenumber`='" . $phoneNumber . "'";
                    $eventQuery = $db->query($sql);

                    if (!$eventQuery) {
                        $response = "END You do not have any events registered with us. \n";
                        break;
                    } else {
                        $response = "CON Please select an event to view its details: \n";
                        $count = 1;
                        while ($row = mysqli_fetch_assoc($eventQuery)) {
                            $response .= $count . ". " . $row['name'] . " \n";
                            $count++;
                        }
                    }
                    break;

                //invalid option sent
                default:
                    //We show the user the home menu
                    $response = "CON Please pick a valid option to proceed. \n";
                    $response .= "1. Give feedback \n";
                    $response .= "2. Register Event \n";
                    $response .= "3. My Registered Events \n";

                    $newLevel = 1;
                    break;
            }

            //We graduate the user to the next level
            $levelUpdate = "UPDATE `session_levels` SET `level`= '" . $newLevel . "'
                        WHERE `session_id`='" . $sessionId . "'";
            $db->query($levelUpdate);
        }
    }else if ($level == 2){

        //fetch the event using the event name entered
        //Check the level

        $eventAvail = 0;
        $sql = "SELECT * FROM `events` WHERE LOWER(`name`) LIKE LOWER('%" . $userResponse . "%')";
        $eventQuery = $db->query($sql);


        if ($result = $eventQuery->fetch_assoc()) {
            $eventAvail = 1;
        }
        //used to know which option the user chose
        $choice = $textArray[0];
        $newLevel = 3;


        switch ($choice) {
            //give feedback
            case "1":
                //check if event exists
                if ($eventAvail == 1){
                    //event exists, start questions

                    $sql = "SELECT * FROM `event_questions` WHERE `event_name`='" . $userResponse . "'";
                    $eventQuery = $db->query($sql);


                    $response = "CON You will give feedback  \n";
                    $response .= "1. Very Good \n";
                    $response .= "2. Good \n";
                    $response .= "3. Neutral \n";
                    $response .= "4. Bad \n";
                    $response .= "5. Very Bad \n";
                }else{
                    $response = "END That event does not exist. Please make sure you have the correct name then try again. \n";
                }

                break;

            //register event
            case "2":
                //check if name has been taken
                if ($eventAvail == 1){
                    //event name taken, start questions
                    $response = "CON That event name has already been taken. Please try another name: \n";
                    $newLevel = 2;
                }else{
                    //confirm registration of event name
                    $response = "CON The event name ". $userResponse ." is available. Create an event with this name? \n";
                    $response .= "1. Accept \n";
                    $response .= "2. Decline \n";
                }

                break;

            //my events
            case "3":
                //display the chosen event's details
                $sql = "SELECT * FROM `events` WHERE `phonenumber`='" . $phoneNumber . "'";
                $eventQuery = $db->query($sql);
                if (!$eventQuery) {
                    $response = "END You do not have an event registered with us. \n";
                    break;
                } else {
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($eventQuery)) {
                        if ($count == $userResponse) {
                            //get number of feedback response
                            $feedSql = "SELECT * FROM `event_feedback` WHERE `event_name`='" . $row['name'] . "'";
                            $feedbackQuery = $db->query($feedSql);
                            $feedbackCount = 0;
                            while (mysqli_fetch_assoc($feedbackQuery) ){
                                $feedbackCount++;
                            }

                            $response = "END Event Name: " . $row['name'] . " \n Number of responses: " . $feedbackCount;
                            break;
                        }
                        $count++;
                    }
                }

                break;

            default:
                $response = "END Invalid option selected. \n";
                $newLevel = 2;
                break;
        }

        //We graduate the user to the next level
        $levelUpdate = "UPDATE `session_levels` SET `level`= '" . $newLevel . "'
                        WHERE `session_id`='" . $sessionId . "'";
        $db->query($levelUpdate);
        
    }else if ($level == 3){
        //used to know which option the user chose
        $choice = $textArray[0];
        $newLevel = 4;

        switch ($choice) {
            //give feedback
            case "2":
                if ($userResponse == 1) {
                    //save event to database
                    $eventName = $textArray[1];
                    $insertQuery = "INSERT INTO `events`(`name`, `phoneNumber`,`status`) VALUES('" . $eventName . "','" . $phoneNumber . "', 'ACTIVE')";
                    $db->query($insertQuery);
                    $response = "END Event created successfully. We will contact you shortly with instructions on how to add
                 the questions. \n";
                }elseif ($userResponse == 2){
                    $response = "END Event creation cancelled. \n";
                }else{
                    $response = "END Event creation failed. Invalid input. \n";
                }
                break;
        }
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //Print the response onto the page so that the ussd API/gateway can read it
    header("Content-type: text/plain");
    echo $response;
}//end of code