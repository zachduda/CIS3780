<?php
  $servername = "cis3870-mysql.mysql.database.azure.com";
  $username = "dudazr_fc";
  $pwd = "71584179c955c33c14941f61";
  $dbname = "dudazr_db";

  try {
    $conn = new PDO("mysql:host=".$servername.";dbname=dudazr_db", $username, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
?>
