<?php
if(!empty($_POST)){
require_once('AfricasTalkingGateway.php');
require_once('_con.php');

 $from = $_POST['from'];
 $to = $_POST['to'];
 $text = $_POST['text'];
 $date = $_POST['date'];
 $id = $_POST['id'];
 $linkId = $_POST['linkId'];
 
 $textArray = explode('*', $text);
 $userResponse = trim(end($textArray));
 
 

$level = 0;
$sql = "select `level` from `session_levels` where `session_id`='" . $sessionId . "'";
$levelQuery = $db->query($sql);
if($result = $levelQuery->fetch_assoc()) {
  $level = $result['level'];
}
 
 $insertQuery = "INSERT INTO `smsresponse`(`from`, `to`, `text`, `linkId`, `date`, `id`) 
 VALUES ('$from','$to','$text','$date','$id','$linkId')";
     $db->query($insertQuery);
}
?>
	 
	 