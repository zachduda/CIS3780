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

echo "<h1 class='mt-4 px-5 text-center mx-auto'>Add Lot</h1>";
echo "<br>";

$pend_del = 0; // for visual use only

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$skip_create = true;
	if($_POST["submit"] == "Delete") {
		$id = "";
		if(!empty($_POST["LotID"])) {
				$id = clean($_POST["LotID"]);
		}
		if(empty($_POST["confirm"]) || clean($_POST["confirm"]) !== $id) {
			$info = "<b>Are you sure?</b> Click <b>Delete</b> again for Lot <b>#".$id."</b> to remove this lot.";
			$pend_del = $id; // visual only
		} else if(clean($_POST["confirm"]) == $id) {
			if(empty($id)) {
				$error = "Missing ID to delete";
			}
			$sql_del = "DELETE FROM Lot WHERE LotID = ".$id.";";
			$res_del = $conn->query($sql_del);
			$success = "Lot ID <b>#".$id. "</b> has been deleted.";
		}
	}
	if($_POST["submit"] == "Reset the Table") {
		if(empty($_POST["reset-confirm"])) {
			$info = "Click <b>Reset Table</b> again to confirm you want to reset all tables.";
			$reset_confirm = true;
		} else {
			$sql_place = "DELETE FROM Lot;ALTER TABLE Lot AUTO_INCREMENT = 1;INSERT INTO Lot (Description, CategoryID, WinningBid, WinningBidder, Delivered) VALUES ('Default Lot', 1, 237.48, 1, 1);";
			$res_place = $conn->query($sql_place);
			$success = "The table has been reset to its default values.";
			$res_place->closeCursor();
		}
	}

	if($_POST["submit"] == "Add Lot") {
		if(empty($_SESSION["csrf"])) {
			$error = "CSRF token missing from session. Try adding your Lot again.";
		} if(empty($_POST["csrf"])) {
			$error = "CSRF token not sent with form. Try adding your Lot again.";
		} else if(!hash_equals(clean($_POST["csrf"]), $_SESSION["csrf"])) {
			$error = "CSRF token mismatch. Lot wasn't added as it may be a duplicate. Try it again!";
		} else {
			$desc = clean($_POST["description"]);
			$catid = intval(clean($_POST["categoryid"]));
			$wb = floatval(clean($_POST["winningbid"]));
			$wbb = intval(clean($_POST["winningbidder"]));
			$delivered = boolval(clean($_POST["delivered"]));
			if(empty($desc)) {
				$error = "Please provide a Description for the new Lot.";
			} else if(empty($catid)) {
				$error = "Please provide a CategoryID for the new Lot.";
			} else if(empty($wb)) {
				$error = "Please provide a Winning Bid (ie: $100.25)";
			} else if(empty($wbb)) {
				$error = "Please provide a Winning Bidder (ID #)";
			} else if(strlen($desc) > 75) {
				$error = "Description (" . $desc . ") exceeds 75 characters.";
			} else if ($catid <= 0) {
				$error = "CategoryID must be a number and bigger than 0.";
			} else if ($wb < 0.01) {
				$error = "Winning Bid must be greater than 1¢";
			} else if ($wbb <= 0) {
				$error = "Winning Bidder must be a number and not 0.";
			} else {
				$sql_in = "INSERT INTO Lot (Description, CategoryID, WinningBid, WinningBidder, Delivered) VALUES (:desc, :catid, :wb, :wbb, :delivered)";
				$sth_in = $conn->prepare($sql_in);
				$sth_in->bindParam(":desc", $desc, PDO::PARAM_STR, 75);
				$sth_in->bindParam(":catid", $catid, PDO::PARAM_INT);
				$sth_in->bindParam(":wb", $wb, PDO::PARAM_STR, 4);
				$sth_in->bindParam(":wbb", $wbb, PDO::PARAM_INT);
				$sth_in->bindParam(":delivered", $delivered, PDO::PARAM_BOOL);
				$sth_in->execute();
				$success = "Added Lot to Table: <b>" . $cn . "</b>";
			}
		}
	}
}

$csrf = genCSRF();
$_SESSION["csrf"] = $csrf;

if(!$skip_create) {
	$sql_create = "CREATE TABLE IF NOT EXISTS `Lot` (
	  `LotID` int NOT NULL AUTO_INCREMENT,
	  `Description` varchar(75) NOT NULL,
	  `CategoryID` int NOT NULL,
	  `WinningBid` decimal(10,2) NOT NULL,
	  `WinningBidder` int NOT NULL,
	  `Delivered` tinyint(1) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`LotID`),
	  KEY `CategoryID` (`CategoryID`),
	  KEY `WinningBidder` (`WinningBidder`)
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

$sql_get = "SELECT * FROM `Lot`";
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
		if($pend_del == $set["LotID"]) {
			$html .= '<input name="LotID" value="'.$set["LotID"].'" type="hidden"><input type="hidden" name="confirm" value="'.$set["LotID"].'"><input name="submit" type="submit" class="btn btn-danger font-weight-bold text-center m-0 p-0 mx-auto" style="height:100%;width:100%" value="Delete"></input>';
		} else {
			$html .= '<input name="LotID" value="'.$set["LotID"].'" type="hidden"><input name="submit" type="submit" class="btn btn-warning text-center m-0 p-0 mx-auto" style="height:100%;width:100%" value="Delete"></input>';
		}
		$html .= '</form>';
		$html .= '<a href="edit_lot.php?id='.$set["LotID"].'" class="mt-2 btn btn-secondary text-center m-0 p-0 mx-auto" style="height:100%;width:100%">Edit</a>';
		$html .= '</td></tr></tbody>';
	}
	echo $html.'</table>';
	echo "<br>";
} else { ?>

<div class="alert alert-secondary m-4 p-4 mx-auto <?php if(empty($error) && empty($info) && empty($success)) { ?>animated fadeInUp faster<?php }?>" style="max-width:600px;" role="alert">
  <h4 class="alert-heading">No Lots in Database</h4>
  <p>All lots have been removed from the database table.</p>
  <hr>
  <p class="mb-0">Click "Add Lot" below to insert more records into the table, or "Reset Table" to reinsert the default records.</p>
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
		<button type="button" class="mx-3 btn btn-success px-5" onclick="document.getElementById('buttons').style.display = 'none';document.getElementById('add').style.display = null;">Add Lot</button>
	</div>
</div>
<div id="add" class="container animated fadeIn fast mx-auto px-2" style="max-width:600px;display:none;">
	<div class="mx-auto">
		<button type="button" class="btn btn-dark px-5" onclick="document.getElementById('buttons').style.display = 'flex';document.getElementById('add').style.display = 'none';">Back</button>
	</div>
	<br>
	<h3>Add a Lot</h3>
	<p></p>
	<form class="row g-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
	  <div class="col-md-6">
	    <label for="description" class="form-label">Lot Description</label>
	    <input type="text" class="form-control" name="description" maxlength="75">
	  </div>
	  <div class="col-md-6">
	    <label for="categoryid" class="form-label">Category ID</label>
	    <input type="number" class="form-control" name="categoryid" min="1" max="999">
	  </div>
	  <div class="col-md-4">
			<label for="winningbid" class="form-label">Winning Bid</label>
			<div class="input-group">
			  <span class="input-group-text">$</span>
			  <div class="form-floating">
			    <input type="text" class="form-control" name="winningbid" id="winningbid" placeholder="100.00">
			    <label for="winningbid">Amount</label>
			  </div>
			</div>
	  </div>
	  <div class="col-md-4">
	    <label for="winningbidder" class="form-label">Winning Bidder (ID #)</label>
	    <input type="number" class="form-control" min="100" max="999" name="winningbidder">
	  </div>
	  <div class="col-12">
	    <div class="form-check">
	      <input class="form-check-input" type="checkbox" name="delivered" value="1">
	      <label class="form-check-label" for="delivered">
	        Was this delivered?
	      </label>
	    </div>
	  </div>
	  <div class="col-12">
			<input type="hidden" name="csrf" value="<?php echo $csrf;?>">
	    <input type="submit" name="submit" value="Add Lot" class="btn btn-success"></input>
	  </div>
	</form>
</div>
<?php echo $js; ?>
</body>
</html>
