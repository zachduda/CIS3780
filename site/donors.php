<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
require_once("_sql.php");

$error = "";
$success = "";
$info = "";
$reset_confirm = false;
$skip_create = false;

echo $headers;

echo "<h1 class='mt-4 px-5 text-center mx-auto'>Add Donors</h1>";
echo "<br>";

$pend_del = 0; // for visual use only

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$skip_create = true;
	if($_POST["submit"] == "Delete") {
		$id = "";
		if(!empty($_POST["DonorID"])) {
				$id = clean($_POST["DonorID"]);
		}
		if(empty($_POST["confirm"]) || clean($_POST["confirm"]) !== $id) {
			$info = "<b>Are you sure?</b> Click <b>Delete</b> again for Donor <b>#".$id."</b> to remove this donor.";
			$pend_del = $id; // visual only
		} else if(clean($_POST["confirm"]) == $id) {
			if(empty($id)) {
				$error = "Missing ID to delete";
			}
			$sql_del = "DELETE FROM Donor WHERE DonorID = ".$id.";";
			$res_del = $conn->query($sql_del);
			$success = "Donor ID <b>#".$id. "</b> has been deleted.";
		}
	}
	if($_POST["submit"] == "Reset the Table") {
		if(empty($_POST["reset-confirm"])) {
			$info = "Click <b>Reset Table</b> again to confirm you want to reset all tables.";
			$reset_confirm = true;
		} else {
			$sql_place = "DELETE FROM Donor;ALTER TABLE Donor AUTO_INCREMENT = 1;INSERT INTO Donor (BusinessName, ContactName, ContactEmail, ContactTitle, Address, City, State, ZipCode, TaxReceipt) VALUES ('Default Donors Inc.', 'Mr/Ms. Good Donor', 'donors@defaultdonors.org', 'CEO', '123 Genorosity Lane', 'Money', 'NC', '28607', '1');";
			$res_place = $conn->query($sql_place);
			$success = "The table has been reset to its default values.";
			$res_place->closeCursor();
		}
	}

	if($_POST["submit"] == "Add Donor") {
		if(empty($_SESSION["csrf"])) {
			$error = "CSRF token missing from session. Try adding your customer again.";
		} if(empty($_POST["csrf"])) {
			$error = "CSRF token not sent with form. Try adding your customer again.";
		} else if(!hash_equals(clean($_POST["csrf"]), $_SESSION["csrf"])) {
			$error = "CSRF token mismatch. Customer wasn't added as it may be a duplicate. Try it again!";
		} else {
			$bn = clean($_POST["businessname"]);
			$cn = clean($_POST["contactname"]);
			$ce = clean($_POST["contactemail"]);
			$ct = clean($_POST["contacttitle"]);
			$ad = clean($_POST["address"]);
			$cy = clean($_POST["city"]);
			$st = clean($_POST["state"]);
			$zc = intval(clean($_POST["zipcode"]));
			$tax = boolval(clean($_POST["tax"]));
			if(empty($bn) || empty($cn) || empty($ce) || empty($ad) || empty($cy)
		|| empty($st) || empty($zc)) {
				$error = "Please provide a Business Name, Contact Name, Contact Email, Contact Title, Address, City, State, Zip Code and Tax status for the new Donor.";
			} else if(strlen($bn) > 75) {
				$error = "Buisness Name (" . $bn . ") exceeds 75 characters.";
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
				$sql_in = "INSERT INTO Donor (BusinessName, ContactName, ContactEmail, ContactTitle, Address, City, State, ZipCode, TaxReceipt) VALUES (:bn, :cn, :ce, :ct, :ad, :cy, :st, :zc, :tax)";
				$sth_in = $conn->prepare($sql_in);
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
				$success = "Added Customer to Table: <b>" . $cn . "</b>";
			}
		}
	}
}

$csrf = genCSRF();
$_SESSION["csrf"] = $csrf;

if(!$skip_create) {
	$sql_create = "CREATE TABLE IF NOT EXISTS `Donor` (
		`DonorID` int NOT NULL AUTO_INCREMENT,
		`BusinessName` varchar(75) NOT NULL,
		`ContactName` varchar(75) NOT NULL,
		`ContactEmail` varchar(200) NOT NULL,
		`ContactTitle` varchar(75) NOT NULL,
		`Address` varchar(75) NOT NULL,
		`City` varchar(30) NOT NULL,
		`State` varchar(2) NOT NULL,
		`ZipCode` varchar(5) NOT NULL,
		`TaxReceipt` tinyint(1) NOT NULL DEFAULT '1',
		PRIMARY KEY (`DonorID`)
	)";

	if(!$conn->query($sql_create)){
		$error = "Table creation failed: (" . $conn->errno . ") " . $dbConnection->error;
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

$sql_get = "SELECT * FROM `Donor`";
$result = $conn->query($sql_get);

if ($result->rowCount() > 0) {
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$resultset[]=$row;
	}
	$html = "<table class='container table table-dark table-hover table-striped table-bordered border-secondary'>";
	$html .= "<thead><tr>";
	$html .= "<th class='row'>".implode('</th><th class="col">',array_keys($resultset[0]))."</th>";
	$html .= "<th class='col'>Modify</th></tr></thead>";
	foreach($resultset as $set){
		$html .= "<tbody><tr><td class='row p-4 text-center'>".implode('</td><td>',$set);"</td>";
		$html .= '<td class="p-0 m-0 pt-1"><form method="POST" class="p-0 m-0 mx-auto text-center" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">';
		if($pend_del == $set["DonorID"]) {
			$html .= '<input name="DonorID" value="'.$set["DonorID"].'" type="hidden"><input type="hidden" name="confirm" value="'.$set["DonorID"].'"><input name="submit" type="submit" class="btn btn-danger font-weight-bold text-center m-0 p-0 mx-auto" style="height:100%;width:100%" value="Delete"></input>';
		} else {
			$html .= '<input name="DonorID" value="'.$set["DonorID"].'" type="hidden"><input name="submit" type="submit" class="btn btn-warning text-center m-0 p-0 mx-auto" style="height:100%;width:100%" value="Delete"></input>';
		}
		$html .= '</form>';
		$html .= '<a href="edit_donors.php?id='.$set["DonorID"].'" class="mt-2 btn btn-secondary text-center m-0 p-0 mx-auto" style="height:100%;width:100%">Edit</a>';
		$html .= '</td></tr></tbody>';
	}
	echo $html.'</table>';
	echo "<br>";
} else { ?>

<div class="alert alert-secondary m-4 p-4 mx-auto <?php if(empty($error) && empty($info) && empty($success)) { ?>animated fadeInUp faster<?php }?>" style="max-width:600px;" role="alert">
  <h4 class="alert-heading">No Donors in Database</h4>
  <p>All donors have been removed from the database table.</p>
  <hr>
  <p class="mb-0">Click "Add Donor" below to insert more records into the table, or "Reset Table" to reinsert the default records.</p>
</div>

<?php } ?>
<br>
<div id="buttons" class="<?php if(empty($error) && empty($info) && empty($success)) { ?>animated fadeInUp fast <?php } ?>mx-auto text-center" style="display:flex;">
	<div class="mx-auto">
		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
			<?php if($reset_confirm) { ?>
					<input type="hidden" name="reset-confirm" value="1">
					<input type="submit" name="submit" class="mx-3 btn btn-danger font-weight-bold px-5" value="Reset the Table">
			<?php } else { ?>
					<input type="submit" name="submit" class="mx-3 btn btn-warning px-5" value="Reset the Table">
			<?php } ?>
		</form>
	</div>
	<div class="mx-auto">
		<button type="button" class="mx-3 btn btn-success px-5" onclick="document.getElementById('buttons').style.display = 'none';document.getElementById('add').style.display = null;">Add Donor</button>
	</div>
</div>
<div id="add" class="container animated fadeIn fast mx-auto px-2" style="max-width:600px;display:none;">
	<div class="mx-auto">
		<button type="button" class="btn btn-dark px-5" onclick="document.getElementById('buttons').style.display = 'flex';document.getElementById('add').style.display = 'none';">Back</button>
	</div>
	<br>
	<h3>Add a Donor</h3>
	<p>Insert the details of the customer below. Use a 2 letter state and 5 digit Zip Code.</p>
	<form class="row g-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
	  <div class="col-md-6">
	    <label for="businessname" class="form-label">Business Name</label>
	    <input type="text" class="form-control" name="businessname" maxlength="75">
	  </div>
	  <div class="col-md-6">
	    <label for="contactname" class="form-label">Contact Name</label>
	    <input type="text" class="form-control" name="contactname" maxlength="75">
	  </div>
	  <div class="col-12">
	    <label for="contactemail" class="form-label">Contact Email</label>
	    <input type="email" class="form-control" name="contactemail" placeholder="donor@email.com" maxlength="200">
	  </div>
		<div class="col-12">
			<label for="contacttitle" class="form-label">Contact Title</label>
			<input type="text" class="form-control" name="contacttitle" placeholder="CEO" maxlength="75">
		</div>
	  <div class="col-12">
	    <label for="address" class="form-label">Address</label>
	    <input type="text" class="form-control" name="address" placeholder="245 Genorosity Lane" maxlength="75">
	  </div>
	  <div class="col-md-6">
	    <label for="city" class="form-label">City</label>
	    <input type="text" class="form-control" name="city" maxlength="30">
	  </div>
	  <div class="col-md-4">
	    <label for="state" class="form-label">State</label>
	    <select name="state" class="form-select" maxlength="2">
					<option value="" selected>Choose...</option>
					<option value="AL">AL</option>
					<option value="AK">AK</option>
					<option value="AR">AR</option>
					<option value="AZ">AZ</option>
					<option value="CA">CA</option>
					<option value="CO">CO</option>
					<option value="CT">CT</option>
					<option value="DC">DC</option>
					<option value="DE">DE</option>
					<option value="FL">FL</option>
					<option value="GA">GA</option>
					<option value="HI">HI</option>
					<option value="IA">IA</option>
					<option value="ID">ID</option>
					<option value="IL">IL</option>
					<option value="IN">IN</option>
					<option value="KS">KS</option>
					<option value="KY">KY</option>
					<option value="LA">LA</option>
					<option value="MA">MA</option>
					<option value="MD">MD</option>
					<option value="ME">ME</option>
					<option value="MI">MI</option>
					<option value="MN">MN</option>
					<option value="MO">MO</option>
					<option value="MS">MS</option>
					<option value="MT">MT</option>
					<option value="NC">NC</option>
					<option value="NE">NE</option>
					<option value="NH">NH</option>
					<option value="NJ">NJ</option>
					<option value="NM">NM</option>
					<option value="NV">NV</option>
					<option value="NY">NY</option>
					<option value="ND">ND</option>
					<option value="OH">OH</option>
					<option value="OK">OK</option>
					<option value="OR">OR</option>
					<option value="PA">PA</option>
					<option value="RI">RI</option>
					<option value="SC">SC</option>
					<option value="SD">SD</option>
					<option value="TN">TN</option>
					<option value="TX">TX</option>
					<option value="UT">UT</option>
					<option value="VT">VT</option>
					<option value="VA">VA</option>
					<option value="WA">WA</option>
					<option value="WI">WI</option>
					<option value="WV">WV</option>
					<option value="WY">WY</option>
	    </select>
	  </div>
	  <div class="col-md-2">
	    <label for="zipcode" class="form-label">Zip Code</label>
	    <input type="text" class="form-control" maxlength="5" name="zipcode">
	  </div>
	  <div class="col-12">
	    <div class="form-check">
	      <input class="form-check-input" type="checkbox" name="tax" value="1">
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
<?php echo $js; ?>
</body>
</html>
