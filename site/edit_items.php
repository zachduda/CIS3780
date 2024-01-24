<?php
$error = "";
$success = "";
$info = "";
$reset_confirm = false;

require_once("_sql.php");
echo $headers;

echo "<h1 class='mt-4 px-5 text-center mx-auto'>Manage Items</h1>";
echo "<br>";

$iid = 0;
if(empty($_GET["iid"])) {
	$error = "No record ID # provided in the URL params.";
} else {
	$iid = intval(clean($_GET["iid"]));
	if($iid == 0) {
		$error = "Invalid Item ID. It must be a number.";
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$iid = clean($_POST["ItemID"]);
	$des = clean($_POST["Description"]);
	$rv = clean($_POST["RetailValue"]);
	$di = clean($_POST["DonorID"]);
	$li = clean($_POST["LotID"]);
	if(empty($iid) || empty($des) || empty($rv) || empty($di) || empty($li)) {
        $error = "Please provide a LotID, Description, CategoryID, WinningBid, Winning Bidder, and Delivered for the new Lot.";
    } else if(intval($iid)) {
        $error = "ItemID (" . $iid . ") must be a number.";
    } else if (strlen($des) > 255) {
        $error = "Description (" . $des . ") exceeds 255 characters.";
    } else if (decimalval($rv)) {
        $error = "RetailValue (" . $rv . ") must be a number.";
    } else if (intval($di)) {
        $error = "DonorID (" . $di . ") must be a number)";
    } else if(intval($li)) {
        $error = "LotID (" . $li . ") must be a number. (ie: 28607)";
    } else {
        $sql_in = "UPDATE INTO Item (ItemID, Description, RetailValue, DonorID, LotID) VALUES (:iid, :des, :rv, :di, :li)";
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

if(!empty($iid)) {
	$sql_get = "SELECT * FROM `Item` WHERE ItemID = ".$iid." LIMIT 1";
	$result = $conn->query($sql_get);

	if ($result->rowCount() > 0) {
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$resultset[]=$row;
		}
		foreach($resultset as $set) { ?>
		<div class="container mx-auto p-2" style="max-width:800px;">
				<form class="row g-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
				  <div class="col-md-6">
				    <label for="businessname" class="form-label">Item ID</label>
				    <input type="int" class="form-control" name="ItemID" maxlength="75" value="<?php echo $set["ItemID"];?>">
				  </div>
				  <div class="col-md-6">
				    <label for="contactname" class="form-label">Description</label>
				    <input type="text" class="form-control" name="description" maxlength="255" value="<?php echo $set["Description"];?>">
				  </div>
				  <div class="col-12">
				    <label for="contactemail" class="form-label">Retail Value</label>
				    <input type="int" class="form-control" name="RetailValue" maxlength="75" value="<?php echo $set["RetailValue"];?>">
				  </div>
					<div class="col-12">
						<label for="contacttitle" class="form-label">Donor ID</label>
						<input type="decimal" class="form-control" name="DonorID" maxlength="75" value="<?php echo $set["DonorID"];?>">
					</div>
				  <div class="col-12">
				    <label for="address" class="form-label">Lot ID</label>
				    <input type="int" class="form-control" name="LotID" maxlength="75" value="<?php echo $set["LotID"];?>">
				  </div>
				      </label>
				  </div>
				  <div class="col-12">
						<input type="hidden" name="csrf" value="<?php echo $csrf;?>">
				    <input type="submit" name="submit" value="Add Item" class="btn btn-success"></input>
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
		<a href="items.php" class="mx-3 btn btn-dark px-5">Back to Item</a>
	</div>
</div>
<?php echo $js; ?>
</body>
</html>
