<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$error = "";
$success = "";
$info = "";
$reset_confirm = false;

session_start(); ?>
<html>
  <head>
    <title>Assignment #3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.compat.css" rel="stylesheet" crossorigin="anonymous">

    <!-- optional fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <style>* {font-family: 'Roboto', sans-serif;}</style>

	<!-- favicon stuff -->
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	
  </head>
<body style="color:white;background-color:#50607a;font-size:18px;">
<?php
echo "<h1 class='mt-4 px-5 text-center mx-auto'>PHP & SQL Assignment</h1>";

require_once("../../fc_creds.php");

function clean($str) {
	return rtrim(htmlspecialchars($str, ENT_QUOTES, 'UTF-8', true));
}

echo "<br>";

$id = 0;
if(empty($_GET["id"])) {
	$error = "No record ID # provided in the URL params.";
} else {
	$id = intval(clean($_GET["id"]));
	if($id == 0) {
		$error = "Invalid record ID. It must be a number.";
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$id = intval(clean($_POST["id"]));
	$fn = clean($_POST["firstname"]);
	$ln = clean($_POST["lastname"]);
	$ad = clean($_POST["address"]);
	$st = clean($_POST["state"]);
	$zp = clean($_POST["zipcode"]);
	
	$sql_in = "UPDATE `customers` SET `firstname` = :fn, `lastname` = :ln, `address` = :ad, `state` = :st, `zip` = :zp WHERE `id` = :id";
	$sth_in = $conn->prepare($sql_in);
	$sth_in->bindParam(":fn", $fn, PDO::PARAM_STR, 15);
	$sth_in->bindParam(":ln", $ln, PDO::PARAM_STR, 30);
	$sth_in->bindParam(":ad", $ad, PDO::PARAM_STR, 255);
	$sth_in->bindParam(":st", $st, PDO::PARAM_STR, 2);
	$sth_in->bindParam(":zp", $zp, PDO::PARAM_STR, 5);
	$sth_in->bindParam(":id", $id, PDO::PARAM_INT);
	$sth_in->execute(); 
	$success = "Record Changed for <b>" . $fn . " " . $ln . "</b>";
}

if(!empty($error)) {
?>
	<div class="animated fadeInUp faster alert alert-danger mx-auto" style="max-width: 800px" role="alert">
		<b>Oops!</b> <?php echo $error; ?>
	</div>
<?php 
} else if(!empty($success)) {
?>
	<div class="animated fadeInUp faster alert alert-success mx-auto" style="max-width: 800px" role="alert">
		<b>Success:</b> <?php echo $success; ?>
	</div>
<?php 
} else if(!empty($info)) {
?>
	<div class="animated fadeInUp faster alert alert-primary mx-auto" style="max-width: 800px" role="alert">
		<?php echo $info; ?>
	</div>
<?php 
} else {
	echo "<br><br><br>";
}

if(!empty($id)) {
	$sql_get = "SELECT * FROM `customers` WHERE id = ".$id." LIMIT 1";
	$result = $conn->query($sql_get);

	if ($result->rowCount() > 0) {
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$resultset[]=$row;
		}
		foreach($resultset as $set) { ?>
		<div class="container mx-auto p-2" style="max-width:800px;">
			<form class="row g-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $id;?>">
				<div class="col-md-4">
					<label for="firstname" class="form-label">First Name</label>
					<input type="text" class="form-control" maxlength="15" name="firstname" value="<?php echo $set["firstname"];?>" required>
				</div>
				<div class="col-md-4">
					<label for="lastname" class="form-label">Last Name</label>
					<input type="text" class="form-control" maxlength="30" name="lastname" value="<?php echo $set["lastname"];?>" required>
				</div>
				<div class="col-md-6">
					<label for="address" class="form-label">Address</label>
					<input type="text" class="form-control" maxlength="255" name="address" value="<?php echo $set["address"];?>" required>
				</div>
				<div class="col-md-3">
					<label for="state" class="form-label">State</label>
					<select class="form-select" name="state" required>
						<option value="AL"<?php if($set["state"] == "AL") { echo " selected";}?>>AL</option>
						<option value="AK"<?php if($set["state"] == "AK") { echo " selected";}?>>AK</option>
						<option value="AR"<?php if($set["state"] == "AR") { echo " selected";}?>>AR</option>
						<option value="AZ"<?php if($set["state"] == "AZ") { echo " selected";}?>>AZ</option>
						<option value="CA"<?php if($set["state"] == "CA") { echo " selected";}?>>CA</option>
						<option value="CO"<?php if($set["state"] == "CO") { echo " selected";}?>>CO</option>
						<option value="CT"<?php if($set["state"] == "CT") { echo " selected";}?>>CT</option>
						<option value="DC"<?php if($set["state"] == "DC") { echo " selected";}?>>DC</option>
						<option value="DE"<?php if($set["state"] == "DE") { echo " selected";}?>>DE</option>
						<option value="FL"<?php if($set["state"] == "FL") { echo " selected";}?>>FL</option>
						<option value="GA"<?php if($set["state"] == "GA") { echo " selected";}?>>GA</option>
						<option value="HI"<?php if($set["state"] == "HI") { echo " selected";}?>>HI</option>
						<option value="IA"<?php if($set["state"] == "IA") { echo " selected";}?>>IA</option>
						<option value="ID"<?php if($set["state"] == "ID") { echo " selected";}?>>ID</option>
						<option value="IL"<?php if($set["state"] == "IL") { echo " selected";}?>>IL</option>
						<option value="IN"<?php if($set["state"] == "IN") { echo " selected";}?>>IN</option>
						<option value="KS"<?php if($set["state"] == "KS") { echo " selected";}?>>KS</option>
						<option value="KY"<?php if($set["state"] == "KY") { echo " selected";}?>>KY</option>
						<option value="LA"<?php if($set["state"] == "LA") { echo " selected";}?>>LA</option>
						<option value="MA"<?php if($set["state"] == "MA") { echo " selected";}?>>MA</option>
						<option value="MD"<?php if($set["state"] == "MD") { echo " selected";}?>>MD</option>
						<option value="ME"<?php if($set["state"] == "ME") { echo " selected";}?>>ME</option>
						<option value="MI"<?php if($set["state"] == "MI") { echo " selected";}?>>MI</option>
						<option value="MN"<?php if($set["state"] == "MN") { echo " selected";}?>>MN</option>
						<option value="MO"<?php if($set["state"] == "MO") { echo " selected";}?>>MO</option>
						<option value="MS"<?php if($set["state"] == "MS") { echo " selected";}?>>MS</option>
						<option value="MT"<?php if($set["state"] == "MT") { echo " selected";}?>>MT</option>
						<option value="NC"<?php if($set["state"] == "NC") { echo " selected";}?>>NC</option>
						<option value="NE"<?php if($set["state"] == "NE") { echo " selected";}?>>NE</option>
						<option value="NH"<?php if($set["state"] == "NH") { echo " selected";}?>>NH</option>
						<option value="NJ"<?php if($set["state"] == "NJ") { echo " selected";}?>>NJ</option>
						<option value="NM"<?php if($set["state"] == "NM") { echo " selected";}?>>NM</option>
						<option value="NV"<?php if($set["state"] == "NV") { echo " selected";}?>>NV</option>
						<option value="NY"<?php if($set["state"] == "NY") { echo " selected";}?>>NY</option>
						<option value="ND"<?php if($set["state"] == "ND") { echo " selected";}?>>ND</option>
						<option value="OH"<?php if($set["state"] == "OH") { echo " selected";}?>>OH</option>
						<option value="OK"<?php if($set["state"] == "OK") { echo " selected";}?>>OK</option>
						<option value="OR"<?php if($set["state"] == "OR") { echo " selected";}?>>OR</option>
						<option value="PA"<?php if($set["state"] == "PA") { echo " selected";}?>>PA</option>
						<option value="RI"<?php if($set["state"] == "RI") { echo " selected";}?>>RI</option>
						<option value="SC"<?php if($set["state"] == "SC") { echo " selected";}?>>SC</option>
						<option value="SD"<?php if($set["state"] == "SD") { echo " selected";}?>>SD</option>
						<option value="TN"<?php if($set["state"] == "TN") { echo " selected";}?>>TN</option>
						<option value="TX"<?php if($set["state"] == "TX") { echo " selected";}?>>TX</option>
						<option value="UT"<?php if($set["state"] == "UT") { echo " selected";}?>>UT</option>
						<option value="VT"<?php if($set["state"] == "VT") { echo " selected";}?>>VT</option>
						<option value="VA"<?php if($set["state"] == "VA") { echo " selected";}?>>VA</option>
						<option value="WA"<?php if($set["state"] == "WA") { echo " selected";}?>>WA</option>
						<option value="WI"<?php if($set["state"] == "WI") { echo " selected";}?>>WI</option>
						<option value="WV"<?php if($set["state"] == "WV") { echo " selected";}?>>WV</option>
						<option value="WY"<?php if($set["state"] == "WY") { echo " selected";}?>>WY</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="zipcode" class="form-label">Zip Code</label>
					<input type="number" class="form-control" min="10000" max="99999" name="zipcode" value="<?php echo $set["zip"];?>" required>
				</div>
				<input type="hidden" name="id" value="<?php echo $id;?>">
				<div class="col-12">
					<button type="submit" name="submit" class="mt-2 btn btn-primary font-weight-bold py-2 px-5 float-end">Edit Record</button>
				</div>
			</form>
		</div>
<?php }
} else { ?>

<div class="alert alert-secondary m-4 p-4 mx-auto <?php if(empty($error) && empty($info) && empty($success)) { ?>animated fadeInUp faster<?php }?>" style="max-width:600px;" role="alert">
  <h4 class="alert-heading">No Record Found</h4>
  <p>That record has been moved or was since deleted.</p>
  <hr>
  <p class="mb-0">Click "Back to Tables" and try reselecting your record.</p>
</div>

<?php }} ?>
<br>
<div id="buttons" class="<?php if(empty($error) && empty($info) && empty($success)) { ?>animated fadeInUp fast <?php } ?>mx-auto text-center" style="display:flex;">
	<div class="mx-auto">
		<a href="assignment.php" class="mx-3 btn btn-dark px-5">Back to Tables</a>
	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js" async defer crossorigin="anonymous"></script>
</body>
</html>