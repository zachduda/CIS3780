<?php

require_once("_sql.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!$conn) {
    echo "Unable to connect to database";
    exit;
}

$sql_in = file_get_contents("_sql_struct.sql");
$sth_in = $conn->prepare($sql_in);
if($sth_in->execute()) {
    echo "Done";
}

?>
