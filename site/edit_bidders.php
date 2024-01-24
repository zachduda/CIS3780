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
  $nm = clean($_POST["name"]);
  $ad = clean($_POST["address"]);
  $cn = clean($_POST["cellnumber"]);
  $hn = clean($_POST["homenumber"]);
  $em = clean($_POST["email"]);
  $pd = boolval(clean($_POST["paid"]));
  if(empty($nm) || empty($ad) || empty($cn) || empty($hn) || empty($em)) {
    $error = "Please provide a Name, Address, Cell and Home Number, Email, and whether or not they paid for bidders.";
  } else if(strlen($nm) > 75) {
    $error = "Name (" . $nm . ") exceeds 75 characters.";
  } else if (strlen($ad) > 75) {
    $error = "Address (" . $ad . ") exceeds 75 characters.";
  } else if (strlen($cn) > 10) {
    $error = "Cell Number (" . $cn . ") exceeds 10 characters.";
  } else if (strlen($hn) > 10) {
    $error = "Home Number (" . $hn . ") exceeds 10 characters.";
  } else if (strlen($em) > 200) {
    $error = "Email (" . $em . ") exceeds 200 characters.";
  } else {
    $sql_in = "UPDATE `Bidder` SET `Name` = :na, `Address` = :ad, `CellNumber` = :cn, `HomeNumber` = :hn, `Email` = :em, `Paid` = :paid WHERE `BidderID` = :id";
    $sth_in = $conn->prepare($sql_in);
    $sth_in->bindParam(":id", $nm, PDO::PARAM_STR, 75);
    $sth_in->bindParam(":ad", $ad, PDO::PARAM_STR, 75);
    $sth_in->bindParam(":cn", $cn, PDO::PARAM_STR, 10);
    $sth_in->bindParam(":hn", $hn, PDO::PARAM_STR, 10);
    $sth_in->bindParam(":em", $em, PDO::PARAM_STR, 200);
    $sth_in->bindParam(":pd", $pd, PDO::PARAM_BOOL);
    $sth_in->execute();
    $success = "Bidder changed for id: <b>" . $nm . "</b>";
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
