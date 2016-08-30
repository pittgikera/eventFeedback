<?php
//theUssdDb.php
//Connection Credentials
$servername = 'localhost';
$username = 'root';
$password = "";
$database = "feedback";
$dbport = 3306;
$username_at   = "Marciechiri";
$apikey     = "2c77f0fa3d92a4c746f270c6414d9ba80bfc7b332c499311c584d66020a2cf6f";

// Create connection
$db = new mysqli($servername, $username, $password, $database, $dbport);
// Check connection
if ($db->connect_error) {
    header('Content-type: text/plain');
    //log error to file/db $e-getMessage()
    die("END An error was encountered. Please try again later");
}
//echo "Connected successfully (".$db->host_info.")";