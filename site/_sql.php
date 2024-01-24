<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_PARSE | E_RECOVERABLE_ERROR);

  session_start();

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

function clean($str) {
	 return rtrim(htmlspecialchars($str, ENT_QUOTES, 'UTF-8', true));
}

function genCSRF() {
	$length = 8;
	return bin2hex(random_bytes($length));
}

$js = "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js' async defer crossorigin='anonymous'></script>";

$headers = <<<EOF
  <html data-bs-theme="dark">
    <head>
      <title>Donor System</title>
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta charset="utf-8">
  	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  	<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.compat.css" rel="stylesheet" crossorigin="anonymous">
	   <link href="/styles.css" rel="stylesheet" crossorigin="anonymous">
      <!-- optional fonts -->
      <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
      <style>* {font-family: 'Roboto', sans-serif;}</style>
    </head>
<body class="bg-dark text-white">
  <nav class="navbar navbar-dark bg-dark navbar-expand-lg bg-body-tertiary no-print">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">Donor System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="/">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="/biddingsheet.php">Bidding Sheet</a>
        </li>
		<li class="nav-item">
          <a class="nav-link" aria-current="page" href="/donations.php">List Donations</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Manage Records
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/donors.php">Donors</a></li>
            <li><a class="dropdown-item" href="/items.php">Items</a></li>
            <li><a class="dropdown-item" href="/bidders.php">Bidders</a></li>
            <li><a class="dropdown-item" href="/lot.php">Lots</a></li>
            <li><a class="dropdown-item" href="/category.php">Category</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
EOF;

?>
