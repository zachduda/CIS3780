<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
require_once("_sql.php");

$error = "";
$success = "";
$info = "";
$reset_confirm = false;

echo $headers;

echo "<h1 class='mt-4 px-5 text-center mx-auto'>Add bidders</h1>";

$skip_create = false;
echo "<br>";

$pend_del = 0; // for visual use only

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$skip_create = true;

	if($_POST["submit"] == "Delete") {
		$id = clean($_POST["id"]);
		if(empty($_POST["confirm"]) || clean($_POST["confirm"]) !== $id) {
			$info = "<b>Are you sure?</b> Click <b>Delete</b> again for bidder <b>#".$id."</b> to remove this bidder.";
			$pend_del = $id; // visual only
		} else if(clean($_POST["confirm"]) == $id) {
			if(empty($id)) {
				$error = "Missing ID to delete";
			}
			$sql_del = "DELETE FROM Bidder WHERE BidderID = ".$id.";";
			$res_del = $conn->query($sql_del);
			$success = "Bidder ID <b>#".$id. "</b> has been deleted.";
		}
	}
	if($_POST["submit"] == "Reset the Table") {
		if(empty($_POST["reset-confirm"])) {
			$info = "Click <b>Reset Table</b> again to confirm you want to reset all tables.";
			$reset_confirm = true;
		} else {
			$sql_place = "DELETE FROM Bidder;ALTER TABLE Bidder AUTO_INCREMENT = 1;INSERT INTO Bidder (Name, Address, CellNumber, HomeNumber, Email, Paid) VALUES ('Mr/Ms.Bidder', '123 Bidder Lane', '1234567890', '2345678901', 'bidder@money.com', 1);";
			$res_place = $conn->query($sql_place);
			$success = "The table has been reset to its default values.";
			$res_place->closeCursor();
		}
	}

	if($_POST["submit"] == "Add bidder") {
		if(empty($_SESSION["csrf"])) {
			$error = "CSRF token missing from session. Try adding your bidder again.";
		} if(empty($_POST["csrf"])) {
			$error = "CSRF token not sent with form. Try adding your bidder again.";
		} else if(!hash_equals(clean($_POST["csrf"]), $_SESSION["csrf"])) {
			$error = "CSRF token mismatch. bidder wasn't added as it may be a duplicate. Try it again!";
		} else {
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
				$sql_in = "INSERT INTO Bidder (Name, Address, CellNumber, Email, HomeNumber, Paid) VALUES (:nm, :ad, :cn, :hn, :em, :pd)";
				$sth_in = $conn->prepare($sql_in);
				$sth_in->bindParam(":nm", $nm, PDO::PARAM_STR, 75);
				$sth_in->bindParam(":ad", $ad, PDO::PARAM_STR, 75);
				$sth_in->bindParam(":cn", $cn, PDO::PARAM_STR, 10);
				$sth_in->bindParam(":hn", $hn, PDO::PARAM_STR, 10);
				$sth_in->bindParam(":em", $em, PDO::PARAM_STR, 200);
				$sth_in->bindParam(":pd", $pd, PDO::PARAM_BOOL);
				$sth_in->execute();
				$success = "Added bidder to Table: <b>" . $nm . "</b>";
			}
		}
	}
}

$csrf = genCSRF();
$_SESSION["csrf"] = $csrf;

if(!$skip_create) {
	 $sql_create = "CREATE TABLE IF NOT EXISTS `Bidder` (
        `BidderID` int(11) unsigned NOT NULL DEFAULT (FLOOR(100+RAND()*(999-100))),
        `Name` varchar(75) NOT NULL default '',
        `Address` varchar(75) NOT NULL default '',
        `CellNumber` varchar(10) NOT NULL default '',
        `HomeNumber` varchar(10) NOT NULL default '',
        `Email` varchar(200) NOT NULL default '',
        `Paid` tinyint(1) NOT NULL default 0,
        PRIMARY KEY  (`BidderID`)
    )";

	if(!$conn->query($sql_create)){
		$error = "Table creation failed: (" . $conn->errno . ") " . $dbConnection->error;
	}
}

// $sql_get = "INSERT INTO customers (firstname, lastame, address, state, zip) VALUES ('Zach', 'Duda', '10 King Street', 'NC', '28607');";
// $res = $conn->query($sql_get);
// print_r($res);

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

$sql_get = "SELECT * FROM `bidder`";
$result = $conn->query($sql_get);

if ($result->rowCount() > 0) {
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$resultset[]=$row;
	}
	$html = "<table class='container table table-dark table-hover table-striped table-bordered border-secondary'><thead><tr><th class='row'>".implode('</th><th class="col">',array_keys($resultset[0]))."</th><th class='col'>modify</th></tr></thead>";
	foreach($resultset as $set){
		$html .= "<tbody><tr><td class='row p-4 text-center'>".implode('</td><td>',$set).'</td><td class="p-0 m-0 pt-1"><form method="POST" class="p-0 m-0 mx-auto text-center" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">';
		if($pend_del == $set["BidderID"]) {
			$html .= '<input name="id" value="'.$set["BidderID"].'" type="hidden"><input type="hidden" name="confirm" value="'.$set["BidderID"].'"><input name="submit" type="submit" class="btn btn-danger font-weight-bold text-center m-0 p-0 mx-auto" style="height:100%;width:100%" value="Delete"></input>';
		} else {
			$html .= '<input name="id" value="'.$set["BidderID"].'" type="hidden"><input name="submit" type="submit" class="btn btn-warning text-center m-0 p-0 mx-auto" style="height:100%;width:100%" value="Delete"></input>';
		}
		$html .= '</form>';
		$html .= '<a href="edit_bidders.php?id='.$set["BidderID"].'" class="mt-2 btn btn-secondary text-center m-0 p-0 mx-auto" style="height:100%;width:100%">Edit</a>';
		$html .= '</td></tr></tbody>';
	}
	echo $html.'</table>';
	echo "<br>";
} else { ?>

<div class="alert alert-secondary m-4 p-4 mx-auto <?php if(empty($error) && empty($info) && empty($success)) { ?>animated fadeInUp faster<?php }?>" style="max-width:600px;" role="alert">
  <h4 class="alert-heading">No Bidders in Database</h4>
  <p>All bidders have been removed from the database table.</p>
  <hr>
  <p class="mb-0">Click "Add Bidder" below to insert more records into the table, or "Reset Table" to reinsert the default records.</p>
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
		<button type="button" class="mx-3 btn btn-success px-5" onclick="document.getElementById('buttons').style.display = 'none';document.getElementById('add').style.display = null;">Add bidder</button>
	</div>
</div>
<div id="add" class="container animated fadeIn fast mx-auto px-2" style="max-width:600px;display:none;">
	<div class="mx-auto">
		<button type="button" class="btn btn-dark px-5" onclick="document.getElementById('buttons').style.display = 'flex';document.getElementById('add').style.display = 'none';">Back</button>
	</div>
	<br>
	<h3>Add a Bidder</h3>
	<p>Insert the details of the bidders below. </p>
	<form class="row g-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<div class="col-md-4">
			<label for="name" class="form-label">Name</label>
			<input type="text" class="form-control" maxlength="75" name="name" required>
		</div>
		<div class="col-md-6">
			<label for="address" class="form-label">Address</label>
			<input type="text" class="form-control" maxlength="75" name="address" required>
		</div>
		<div class="col-md-3">
			<label for="cellnumber" class="form-label">Cell Number</label>
			<input type="number" class="form-control" min="0000000000" max="9999999999" name="cellnumber" required>
		</div>
		<div class="col-md-3">
			<label for="homenumber" class="form-label">Home Number</label>
			<input type="number" class="form-control" min="0000000000" max="9999999999" name="homenumber" required>
		</div>
		<div class="col-md-8">
			<label for="email" class="form-label">Email</label>
			<input type="text" class="form-control" maxlength="200" name="email" required>
		</div>
		<div class="col-md-2">
			<label for="paid" class="form-check-label">Paid</label>
			<input type="checkbox" class="form-check-input" name="email">
		</div>

		<input type="hidden" name="csrf" value="<?php echo $csrf;?>">
		<div class="col-12">
			<input type="submit" name="submit" class="mt-2 btn btn-success font-weight-bold py-2 px-5 float-end" value="Add bidder">
		</div>
	</form>
</div>
<?php echo $js; ?>
</body>
</html>
