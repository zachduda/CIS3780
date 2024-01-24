<?php
$error = "";
$success = "";
$info = "";
$reset_confirm = false;

require_once("_sql.php");
echo $headers;

echo "<h1 class='mt-4 px-5 text-center mx-auto'>Manage Donors</h1>";
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
	$bn = clean($_POST["businessname"]);
	$cn = clean($_POST["contactname"]);
	$ce = clean($_POST["contactemail"]);
	$ct = clean($_POST["contacttitle"]);
	$ad = clean($_POST["address"]);
	$cy = clean($_POST["city"]);
	$st = clean($_POST["state"]);
	$zc = clean($_POST["zipcode"]);
	$tax = false;
	if(isset($_POST["tax"])) {
		$tax = boolval(clean($_POST["tax"]));
	}
	if(empty($bn) || empty($cn) || empty($ce) || empty($ad) || empty($cy)
|| empty($st) || empty($zc)) {
		$error = "Please provide a Business Name, Contact Name, Contact Email, Contact Title, Address, City, State, Zip Code and Tax status for the new Donor.";
	} else if(strlen($bn) > 75) {
		$error = "Business Name (" . $bn . ") exceeds 75 characters.";
	} else if (strlen($cn) > 75) {
		$error = "Contact Name (" . $cn . ") exceeds 75 characters.";
	} else if (strlen($ce) > 200) {
		$error = "Contact Email (" . $ce . ") exceeds 200 characters.";
	} else if (strlen($ct) > 75) {
		$error = "Contact Title (" . $ct . ") exceeds 75 characters.";
	} else if (strlen($ad) > 75) {
		$error = "Address (" . $ad . ") exceeds 75 characters.";
	} else if (strlen($cy) > 30) {
		$error = "City (" . $cy . ") exceeds 30 characters.";
	} else if (strlen($st) !== 2) {
		$error = "State (" . $st . ") must be a 2 letter state abbrv. (ie: NC)";
	} else if(strlen($zc) !== 5) {
		$error = "Zip Code (" . $zc . ") must be 5 numbers. (ie: 28607)";
	} else {
		$sql_in = "UPDATE `Donor` SET `BusinessName` = :bn, `ContactName` = :cn, `ContactEmail` = :ce, `ContactTitle` = :ct, `Address` = :ad, `City` = :cy, `State` = :st, `ZipCode` = :zc, `TaxReceipt` = :tax WHERE `DonorID` = :id";
		$sth_in = $conn->prepare($sql_in);
		$sth_in->bindParam(":id", $id, PDO::PARAM_INT);
		$sth_in->bindParam(":bn", $bn, PDO::PARAM_STR, 75);
		$sth_in->bindParam(":cn", $cn, PDO::PARAM_STR, 75);
		$sth_in->bindParam(":ce", $ce, PDO::PARAM_STR, 200);
		$sth_in->bindParam(":ct", $ct, PDO::PARAM_STR, 75);
		$sth_in->bindParam(":ad", $ad, PDO::PARAM_STR, 75);
		$sth_in->bindParam(":cy", $cy, PDO::PARAM_STR, 30);
		$sth_in->bindParam(":st", $st, PDO::PARAM_STR, 2);
		$sth_in->bindParam(":zc", $zc, PDO::PARAM_STR, 5);
		$sth_in->bindParam(":tax", $tax, PDO::PARAM_BOOL);
		$sth_in->execute();
		$success = "Record Changed for <b>" . $cn . "</b>";
	}
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
	$sql_get = "SELECT * FROM `Donor` WHERE DonorID = ".$id." LIMIT 1";
	$result = $conn->query($sql_get);

	if ($result->rowCount() > 0) {
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$resultset[]=$row;
		}
		foreach($resultset as $set) { ?>
		<div class="container mx-auto p-2" style="max-width:800px;">
				<form class="row g-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $_REQUEST["id"];?>">
				  <div class="col-md-6">
				    <label for="businessname" class="form-label">Buisness Name</label>
				    <input type="text" class="form-control" name="businessname" maxlength="75" value="<?php echo $set["BusinessName"];?>">
				  </div>
				  <div class="col-md-6">
				    <label for="contactname" class="form-label">Contact Name</label>
				    <input type="text" class="form-control" name="contactname" maxlength="75" value="<?php echo $set["ContactName"];?>">
				  </div>
				  <div class="col-12">
				    <label for="contactemail" class="form-label">Contact Email</label>
				    <input type="email" class="form-control" name="contactemail" placeholder="donor@email.com" maxlength="200" value="<?php echo $set["ContactEmail"];?>">
				  </div>
					<div class="col-12">
						<label for="contacttitle" class="form-label">Contact Title</label>
						<input type="text" class="form-control" name="contacttitle" placeholder="CEO" maxlength="75" value="<?php echo $set["ContactTitle"];?>">
					</div>
				  <div class="col-12">
				    <label for="address" class="form-label">Address</label>
				    <input type="text" class="form-control" name="address" placeholder="245 Genorosity Lane" maxlength="75" value="<?php echo $set["Address"];?>">
				  </div>
				  <div class="col-md-6">
				    <label for="city" class="form-label">City</label>
				    <input type="text" class="form-control" name="city" maxlength="30" value="<?php echo $set["City"];?>">
				  </div>
				  <div class="col-md-4">
				    <label for="state" class="form-label">State</label>
				    <select name="state" class="form-select" maxlength="2">
								<option value="AL" <?php if($set["State"] == "AL") { echo " selected";}?>>AL</option>
								<option value="AK" <?php if($set["State"] == "AK") { echo " selected";}?>>AK</option>
								<option value="AR" <?php if($set["State"] == "AR") { echo " selected";}?>>AR</option>
								<option value="AZ" <?php if($set["State"] == "AZ") { echo " selected";}?>>AZ</option>
								<option value="CA" <?php if($set["State"] == "CA") { echo " selected";}?>>CA</option>
								<option value="CO" <?php if($set["State"] == "CO") { echo " selected";}?>>CO</option>
								<option value="CT" <?php if($set["State"] == "CT") { echo " selected";}?>>CT</option>
								<option value="DC" <?php if($set["State"] == "DC") { echo " selected";}?>>DC</option>
								<option value="DE" <?php if($set["State"] == "DE") { echo " selected";}?>>DE</option>
								<option value="FL" <?php if($set["State"] == "FL") { echo " selected";}?>>FL</option>
								<option value="GA" <?php if($set["State"] == "GA") { echo " selected";}?>>GA</option>
								<option value="HI" <?php if($set["State"] == "HI") { echo " selected";}?>>HI</option>
								<option value="IA" <?php if($set["State"] == "IA") { echo " selected";}?>>IA</option>
								<option value="ID" <?php if($set["State"] == "ID") { echo " selected";}?>>ID</option>
								<option value="IL" <?php if($set["State"] == "IL") { echo " selected";}?>>IL</option>
								<option value="IN" <?php if($set["State"] == "IN") { echo " selected";}?>>IN</option>
								<option value="KS" <?php if($set["State"] == "KS") { echo " selected";}?>>KS</option>
								<option value="KY" <?php if($set["State"] == "KY") { echo " selected";}?>>KY</option>
								<option value="LA" <?php if($set["State"] == "LA") { echo " selected";}?>>LA</option>
								<option value="MA" <?php if($set["State"] == "MA") { echo " selected";}?>>MA</option>
								<option value="MD" <?php if($set["State"] == "MD") { echo " selected";}?>>MD</option>
								<option value="ME" <?php if($set["State"] == "ME") { echo " selected";}?>>ME</option>
								<option value="MI" <?php if($set["State"] == "MI") { echo " selected";}?>>MI</option>
								<option value="MN" <?php if($set["State"] == "MN") { echo " selected";}?>>MN</option>
								<option value="MO" <?php if($set["State"] == "MO") { echo " selected";}?>>MO</option>
								<option value="MS" <?php if($set["State"] == "MS") { echo " selected";}?>>MS</option>
								<option value="MT" <?php if($set["State"] == "MT") { echo " selected";}?>>MT</option>
								<option value="NC" <?php if($set["State"] == "NC") { echo " selected";}?>>NC</option>
								<option value="NE" <?php if($set["State"] == "NE") { echo " selected";}?>>NE</option>
								<option value="NH" <?php if($set["State"] == "NH") { echo " selected";}?>>NH</option>
								<option value="NJ" <?php if($set["State"] == "NJ") { echo " selected";}?>>NJ</option>
								<option value="NM" <?php if($set["State"] == "NM") { echo " selected";}?>>NM</option>
								<option value="NV" <?php if($set["State"] == "NV") { echo " selected";}?>>NV</option>
								<option value="NY" <?php if($set["State"] == "NY") { echo " selected";}?>>NY</option>
								<option value="ND" <?php if($set["State"] == "ND") { echo " selected";}?>>ND</option>
								<option value="OH" <?php if($set["State"] == "OH") { echo " selected";}?>>OH</option>
								<option value="OK" <?php if($set["State"] == "OK") { echo " selected";}?>>OK</option>
								<option value="OR" <?php if($set["State"] == "OR") { echo " selected";}?>>OR</option>
								<option value="PA" <?php if($set["State"] == "PA") { echo " selected";}?>>PA</option>
								<option value="RI" <?php if($set["State"] == "RI") { echo " selected";}?>>RI</option>
								<option value="SC" <?php if($set["State"] == "SC") { echo " selected";}?>>SC</option>
								<option value="SD" <?php if($set["State"] == "SD") { echo " selected";}?>>SD</option>
								<option value="TN" <?php if($set["State"] == "TN") { echo " selected";}?>>TN</option>
								<option value="TX" <?php if($set["State"] == "TX") { echo " selected";}?>>TX</option>
								<option value="UT" <?php if($set["State"] == "UT") { echo " selected";}?>>UT</option>
								<option value="VT" <?php if($set["State"] == "VT") { echo " selected";}?>>VT</option>
								<option value="VA" <?php if($set["State"] == "VA") { echo " selected";}?>>VA</option>
								<option value="WA" <?php if($set["State"] == "WA") { echo " selected";}?>>WA</option>
								<option value="WI" <?php if($set["State"] == "WI") { echo " selected";}?>>WI</option>
								<option value="WV" <?php if($set["State"] == "WV") { echo " selected";}?>>WV</option>
								<option value="WY" <?php if($set["State"] == "WY") { echo " selected";}?>>WY</option>
				    </select>
				  </div>
				  <div class="col-md-2">
				    <label for="zipcode" class="form-label">Zip Code</label>
				    <input type="number" class="form-control" min="0" max="99999" name="zipcode" value="<?php echo $set["ZipCode"];?>">
				  </div>
				  <div class="col-12">
				    <div class="form-check">
				      <input class="form-check-input" type="checkbox" name="tax" value="1" <?php if(boolval($set["TaxReceipt"])) { echo "checked";}?>>
				      <label class="form-check-label" for="tax">
				        Will this be taxed?
				      </label>
				    </div>
				  </div>
				  <div class="col-12">
						<input type="hidden" name="csrf" value="<?php echo $csrf;?>">
				    <input type="submit" name="submit" value="Add Donor" class="btn btn-success"></input>
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
		<a href="donors.php" class="mx-3 btn btn-dark px-5">Back to Donors</a>
	</div>
</div>
<?php echo $js; ?>
</body>
</html>
