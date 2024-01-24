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

echo "<h1 class='mt-4 px-5 text-center mx-auto'>Add Items</h1>";



$skip_create = false;
echo "<br>";

$pend_del = 0; // for visual use only

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$skip_create = true;

	if($_POST["submit"] == "Delete") {
		$itemid = clean($_POST["itemid"]);
		if(empty($_POST["confirm"]) || clean($_POST["confirm"]) !== $itemid) {
			$info = "<b>Are you sure?</b> Click <b>Delete</b> again for Item <b>#".$itemid."</b> to remove this Item.";
			$pend_del = $id; // visual only
		} else if(clean($_POST["confirm"]) == $itemid) {
			if(empty($itemid)) {
				$error = "Missing ID to delete";
			}
			$sql_del = "DELETE FROM Items WHERE itemid = ".$itemid.";";
			$res_del = $conn->query($sql_del);
			$success = "Item ID <b>#".$itemid. "</b> has been deleted.";
		}
	}
	if($_POST["submit"] == "Reset the Table") {
		if(empty($_POST["reset-confirm"])) {
			$info = "Click <b>Reset Table</b> again to confirm you want to reset all tables.";
			$reset_confirm = true;
		} else {
			$sql_place = "DELETE FROM Items;ALTER TABLE Item AUTO_INCREMENT = 1";
			$res_place = $conn->query($sql_place);
			$success = "The table has been reset to its default values.";
			$res_place->closeCursor();
		}
	}

	if($_POST["submit"] == "Add Item") {
		if(empty($_SESSION["csrf"])) {
			$error = "CSRF token missing from session. Try adding your item again.";
		} if(empty($_POST["csrf"])) {
			$error = "CSRF token not sent with form. Try adding your item again.";
		} else if(!hash_equals(clean($_POST["csrf"]), $_SESSION["csrf"])) {
			$error = "CSRF token mismatch. Your item wasn't added as it may be a duplicate. Try it again!";
		} else {
			$iid = clean($_POST["ItemID"]);
			$des = clean($_POST["Description"]);
			$rv = clean($_POST["RetailValue"]);
			$di = clean($_POST["DonorID"]);
			$li = clean($_POST["LotID"]);
			if(empty($iid) || empty($des) || empty($rv) || empty($di) || empty($li)) {
				$error = "Please provide a ItemID, Description of Item, Retail Value, Donor ID & Lot ID for the new Item.";
			} else if(!intval($iid)) {
				$error = "ItemID (" . $iid . ") must be a number.";
			} else if (strlen($des) >255) {
				$error = "Description (" . $des . ") exceeds 255 characters.";
			} else if (!intval($rv)) {
				$error = "RetailValue (" . $rv . ") must be in decimal format.";
			} else if (!intval($di) < 0) {
				$error = "DonorID (" . $di . ") needs to be greater than 0.";
			} else if(!intval($li)) {
				$error = "LotID (" . $li . ") must be a number.";
			} else {
				$sql_in = "INSERT INTO Item (ItemID, Description, RetailValue, DonorID, LotID) VALUES (:iid, :des, :rv, :di, :li)";
				$sth_in = $conn->prepare($sql_in);
				$sth_in->bindParam(":iid", $iid, PDO::PARAM_STR);
				$sth_in->bindParam(":des", $des, PDO::PARAM_STR, 255);
				$sth_in->bindParam(":rv", $rv, PDO::PARAM_STR, 20);
				$sth_in->bindParam(":di", $di, PDO::PARAM_INT);
				$sth_in->bindParam(":li", $li, PDO::PARAM_STR);
				$sth_in->execute();
				$success = "Your item was added to Table: <b>" . $iid . " " . $des . "</b>";
			}
		}
	}
}

$csrf = genCSRF();
$_SESSION["csrf"] = $csrf;

if(!$skip_create) {
	$sql_create = "CREATE TABLE IF NOT EXISTS `Item` (
		`ItemID` int NOT NULL,
		`Description` varchar(75) NOT NULL,
		`RetailValue` decimal(10,2) NOT NULL,
		`DonorID` int NOT NULL,
		`LotID` int NOT NULL,
		PRIMARY KEY (`ItemID`),
		KEY `DonorID` (`DonorID`),
		KEY `LotID` (`LotID`)
	  )";

	if(!$conn->query($sql_create)){
		$error = "Table creation failed: (" . $conn->errno . ") " . $dbConnection->error;
	}
}

// $sql_get = "INSERT INTO items (firstname, lastame, address, state, zip) VALUES ('Zach', 'Duda', '10 King Street', 'NC', '28607');";
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

$sql_get = "SELECT * FROM `Item`";
$result = $conn->query($sql_get);

if ($result->rowCount() > 0) {
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$resultset[]=$row;
	}
	$html = "<table class='container table table-dark table-hover table-striped table-bordered border-secondary'><thead><tr><th class='row'>".implode('</th><th class="col">',array_keys($resultset[0]))."</th><th class='col'>modify</th></tr></thead>";
	foreach($resultset as $set){
		$html .= "<tbody><tr><td class='row p-4 text-center'>".implode('</td><td>',$set).'</td><td class="p-0 m-0 pt-1"><form method="POST" class="p-0 m-0 mx-auto text-center" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">';
		if($pend_del == $set["ItemID"]) {
			$html .= '<input name="iid" value="'.$set["ItemID"].'" type="hidden"><input type="hidden" name="confirm" value="'.$set["itemid"].'"><input name="submit" type="submit" class="btn btn-danger font-weight-bold text-center m-0 p-0 mx-auto" style="height:100%;width:100%" value="Delete"></input>';
		} else {
			$html .= '<input name="iid" value="'.$set["ItemID"].'" type="hidden"><input name="submit" type="submit" class="btn btn-warning text-center m-0 p-0 mx-auto" style="height:100%;width:100%" value="Delete"></input>';
		}
		$html .= '</form>';
		$html .= '<a href="edit_items.php?iid='.$set["ItemID"].'" class="mt-2 btn btn-secondary text-center m-0 p-0 mx-auto" style="height:100%;width:100%">Edit</a>';
		$html .= '</td></tr></tbody>';
	}
	echo $html.'</table>';
	echo "<br>";
} else { ?>

<div class="alert alert-secondary m-4 p-4 mx-auto <?php if(empty($error) && empty($info) && empty($success)) { ?>animated fadeInUp faster<?php }?>" style="max-width:600px;" role="alert">
  <h4 class="alert-heading">No Items in Database</h4>
  <p>All Items have been removed from the database table.</p>
  <hr>
  <p class="mb-0">Click "Add Items" below to insert more records into the table, or "Reset Table" to reinsert the default records.</p>
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
		<button type="button" class="mx-3 btn btn-success px-5" onclick="document.getElementById('buttons').style.display = 'none';document.getElementById('add').style.display = null;">Add Item</button>
	</div>
</div>
<div id="add" class="container animated fadeIn fast mx-auto px-2" style="max-width:600px;display:none;">
	<div class="mx-auto">
		<button type="button" class="btn btn-dark px-5" onclick="document.getElementById('buttons').style.display = 'flex';document.getElementById('add').style.display = 'none';">Back</button>
	</div>
	<br>
	<h3>Add a Item</h3>
	<p>Insert the details of the Item below.</p>
	<form class="row g-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<div class="col-md-4">
			<label for="ItemID" class="form-label">Item Id</label>
			<input type="text" class="form-control" maxlength="15" name="ItemID" required>
		</div>
		<div class="col-md-4">
			<label for="description" class="form-label">Description of Item</label>
			<input type="text" class="form-control" maxlength="30" name="Description" required>
		</div>
		<div class="col-md-6">
			<label for="RetailValue" class="form-label">Retail Value</label>
			<input type="text" class="form-control" maxlength="255" name="RetailValue" required>
		</div>
		<div class="col-md-3">
			<label for="DonorID" class="form-label">Donor Id</label>
			<input type="number" class="form-control" min="1" max="99999" name="DonorID" required>
		</div>
		<div class="col-md-4">
			<label for="DonorID" class="form-label">Lot Id</label>
			<input type="number" class="form-control" min="1" max="99999" name="LotID" required>
		</div>
		<input type="hidden" name="csrf" value="<?php echo $csrf;?>">
		<div class="col-12">
			<input type="submit" name="submit" class="mt-2 btn btn-success font-weight-bold py-2 px-5 float-end" value="Add Item">
		</div>
	</form>
</div>
<?php echo $js; ?>
</body>
</html>
