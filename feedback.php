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

    if ($level == 0) {
        //We graduate the user to level 1
        $insertQuery = "INSERT INTO `session_levels`(`session_id`, `phoneNumber`,`level`) VALUES('" . $sessionId . "','" . $phoneNumber . "', 1)";
        $db->query($insertQuery);

        //We show the user the home menu
        $response = "CON Welcome to Event Feedback. \n";
        $response .= "1. Give feedback \n";
        $response .= "2. Register Event \n";
        $response .= "3. My Registered Events \n";
    } else if ($level == 1) {
        //If user sends back nothing, we resend home menu
        if ($userResponse == "") {
            //We show the user the home menu
            $response = "CON Please pick an option to proceed. \n";
            $response .= "1. Give feedback \n";
            $response .= "2. Register Event \n";
            $response .= "3. My Registered Events \n";
        } else {
            $newLevel = 2;
            switch ($userResponse) {
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
    } else if ($level == 2) {

        //fetch the event using the event name entered
        //Check the level

        $eventAvail = 0;
        $sql = "SELECT * FROM `events` WHERE LOWER(`name`) LIKE LOWER('%" . $userResponse . "%')";
        $eventQuery = $db->query($sql);
        $eventDetails = $eventQuery->fetch_assoc();

        if ($result = $eventDetails) {
            $eventAvail = 1;
        }
        //used to know which option the user chose
        $choice = $textArray[0];
        $newLevel = 3;


        switch ($choice) {
            //give feedback
            case "1":
                //check if event exists
                if ($eventAvail == 1) {
                    //event exists, start questions

                    $response = "CON Give your answer in the form of ratings (1 - 10) where: \n 1   ==> Bad \n 10 ==> Good \n";
                    $response .= "1. Accept \n";
                    $response .= "2. Decline \n";

                } else {
                    $response = "END That event does not exist. Please make sure you have the correct name then try again. \n";
                }

                break;

            //register event
            case "2":
                //check if name has been taken
                if ($eventAvail == 1) {
                    //event name taken, start questions
                    $response = "CON That event name has already been taken. Please try another name: \n";
                    $newLevel = 2;
                } else {
                    //confirm registration of event name
                    $response = "CON The event name " . $userResponse . " is available. Create an event with this name? \n";
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
                            while (mysqli_fetch_assoc($feedbackQuery)) {
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

    } else if ($level == 3) {
        //used to know which option the user chose
        $choice = $textArray[0];
        $newLevel = 4;

        switch ($choice) {
            //save user response
            case 1:
                if ($textArray[2] == 1) {
                    $sql = "SELECT * FROM `events` WHERE LOWER(`name`) LIKE LOWER('%" . $textArray[1] . "%')";
                    $eventQuery = $db->query($sql);
                    $eventDetails = $eventQuery->fetch_assoc();

                    $sql = "SELECT * FROM `smsresponse` WHERE `from`='" . $eventDetails['phonenumber'] . "'";
                    $smsQuery = $db->query($sql);
                    $smsResponse = $smsQuery->fetch_assoc();

                    //$questions = "How was is it? # Was it enjoyable?# Did you have fun? # Will you come back?";
                    $questions = $smsResponse['text'];
                    //we use array filter to remove indices whose values are empty
                    $questionsArray = array_filter(explode('#', $questions));
                    $responseArray = array_filter(explode('*', $text));
                    if (count($questionsArray) < 1) {
                        $response = "END " . "We don't have questions for you today!";
                    } else {
                        $currentQuestion = null;
                        for ($i = 0; $i < count($questionsArray); $i++) {
                            if (isset($responseArray[$i + 1])) {
                                //question has been answered
                            } else {
                                //question not answered
                                $currentQuestion = $i;
                                break;

                            }
                        }
                        if (is_null($currentQuestion)) {
                            //if the current question is null, they've answered everything
                            $parts = $textArray;
                            unset($parts[0]);
                            unset($parts[1]);
                            unset($parts[2]);
                            $eventResponse = implode('*', $parts);

                            $eventName = $textArray[1];
                            $insertQuery = "INSERT INTO `event_feedback`(`event_name`, `phoneNumber`,`response`) VALUES('" . $eventName . "','" . $eventDetails['phonenumber'] . "', '".$eventResponse."')";
                            $db->query($insertQuery);

                            $response = "END " . "You have answered everything";
                        } else {
                            //show that question now!
                            $response = "CON " . $questionsArray[$currentQuestion];
                        }
                    }
                }else{
                    $response = "END " . "Thank you for using Event Feedback";
                }

                break;


            //create event
            case "2":
                if ($userResponse == 1) {
                    //save event to database
                    $eventName = $textArray[1];
                    $insertQuery = "INSERT INTO `events`(`name`, `phoneNumber`,`status`) VALUES('" . $eventName . "','" . $phoneNumber . "', 'ACTIVE')";
                    $db->query($insertQuery);
                    $response = "END Event created successfully. We will contact you shortly with instructions on how to add the questions. \n";
                    $message = "Send a message to 20414 with the questions  starting with 125 separated with #. e.g 125 How would you rate the event#How would you rate the food#";

                    // Create a new instance of our awesome gateway class
                    $gateway = new AfricasTalkingGateway($username_at, $apikey);

                    // Any gateway error will be captured by our custom Exception class below, 
                    // so wrap the call in a try-catch block

                    try {
                        // That's it, hit send and we'll take care of the rest. 
                        $gateway->sendMessage($phoneNumber, $message);

                    } catch (AfricasTalkingGatewayException $e) {
                        echo "Encountered an error while sending: " . $e->getMessage();
                    }
                } elseif ($userResponse == 2) {
                    $response = "END Event creation cancelled. \n";
                } else {
                    $response = "END Event creation failed. Invalid input. \n";
                }
                break;
        }
    }


    //Print the response onto the page so that the ussd API/gateway can read it
    header("Content-type: text/plain");
    echo $response;
}//end of code