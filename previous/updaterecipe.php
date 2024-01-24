<?php
if(!empty($_POST)) {
	$formempty = false;
	$validform = true;
	$rid = trim(htmlentities(stripslashes($_POST['rid'])));

	if($_POST["submit"] == "Update") {
			$update = trim(htmlentities(stripslashes($_POST["submit"])));
			$title = trim(htmlentities(stripslashes($_POST["title"])));
			if(empty($rid)) {
					$validform = false;
					$riderrormessage .= "Value must be entered for recipe ID";
			} else if(!is_numeric($rid)) {
					$validform = false;
					$riderrormessage .= "RID must be a number";
			}
			die;
	}

// UPDATE recipe SET rid = :rid, title = :title WHERE rid = :rid;
// $sth = $conn->prepare($sql);
// $sth->bindParam(":rid", $rid, PDO:PARAM_INT);
// $sth->bindParam(":title", $title, PDO:PARAM_STR, 75);
// echo "<br>SQL STMT WAS:" . $sql;

	require_once("fc_creds.php");
	$sql = "SELECT rid, title FROM recipe;";
	// missing parts
	$sth -> bindParam(':rid', $rid, $PDO:PARAM_INT);
	$sth->execute();
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	if($sth->rowcount() > 0) {
		// return results
		echo "<p>You are updating recipe # " . $result["rid"] . "</p>";
		echo "<p>The title is " . $result["title"];
	} else {
		$validform = false;
		$riderrormessage = "Invalid record number";
		echo $riderrormessage;
		die;
	}
} else {
	$formempty = true;
	$validform = false;
	$rid = "";
}
$riderrormessage="";
$titleerrormessage="";

if ($formempty == false) {
	if(empty($rid)) {
		$validform = false;
	} elseif (!is_numeric($rid)) {
		$validform = false;
		$riderrormessage = "A number must be provided";
	}
}
?>
