<?php

$servername = "cis3870-mysql.mysql.database.azure.com";
$username = "dudazr_fc";
$pwd = "71584179c955c33c14941f61";
$dbname = "dudazr_db";

try {
    $conn = new PDO("mysql:host=".$servername.";dbname=dudazr_db", $username, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}

$sql = "INSERT INTO recipe (rid, title, servingqty, servingtype";
$sql .= ", timeprepqty, timecookqty, cat, picture) VALUES (";
$sql .= ":rid, :title, :card, :servingqty, :servingtype,";
$sql .= ":timeprepqty, :timecookqty, :cat, :picture);";
$sth = $conn->prepare($sql);
$sth -> bindParam(":rid", $rid, PDO::PARAM_INT);
$sth -> bindParam(":title", $title, PDO::PARAM_STR, 75);
echo "<br>SQL statement was ".$sql;
$conn->query($sql);
echo "New record created successfully.";
die;

?>
