<?php
$error = "";
$success = "";
$info = "";
$reset_confirm = false;

require_once("_sql.php");
echo $headers;

echo "<h1 class='mt-4 px-5 text-center mx-auto'>Manage Lots</h1>";
echo "<br>";

$li = 0;
if(empty($_GET["id"])) {
	$error = "No record ID # provided in the URL params.";
} else {
	$li = intval(clean($_GET["id"]));
	if($li == 0) {
		$error = "Invalid Lot ID. It must be a number.";
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$des = clean($_POST["description"]);
	$ci = clean($_POST["categoryid"]);
	$wb = clean($_POST["winningbid"]);
	$wbr = intval(clean($_POST["winningbidder"]));
	$del = boolval(clean($_POST["delivered"]));
	if(empty($des) || empty($ci) || empty($wb) || empty($wbr)) {
        $error = "Please provide a LotID, Description, CategoryID, WinningBid, Winning Bidder, and Delivered for the new Lot.";
    } else if (strlen($des) > 255) {
        $error = "Description (" . $des . ") exceeds 255 characters.";
    } else if(!intval($ci)) {
        $error = "Category (" . $ci . ") must be a number.";
    } else if (!intval($wb) && intval($wb) >= 0.01) {
        $error = "WinningBid (" . $wb . ") must be a price (ie: $100.25)";
    } else if($wbr <= 0) {
        $error = "WinningBidder (" . $wbr . ") must be an ID # (ie: 2)";
    } else {
        $sql_in = "UPDATE `Lot` SET `Description` = :des, `CategoryID` = :ci, `WinningBid` = :wb, `WinningBidder` = :wbr, `Delivered` = :del";
        $sth_in = $conn->prepare($sql_in);
        $sth_in->bindParam(":des", $des, PDO::PARAM_STR, 75);
        $sth_in->bindParam(":ci", $ci, PDO::PARAM_INT);
        $sth_in->bindParam(":wb", $wb, PDO::PARAM_STR, 4);
        $sth_in->bindParam(":wbr", $wbr, PDO::PARAM_INT);
        $sth_in->bindParam(":del", $del, PDO::PARAM_BOOL);
        $sth_in->execute();
        $success = "Added Lot to Table: <b>" . $li . " " . $des . "</b>";
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

if(!empty($li)) {
	$sql_get = "SELECT * FROM `Lot` WHERE LotID = ".$li." LIMIT 1";
	$result = $conn->query($sql_get);

	if ($result->rowCount() > 0) {
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$resultset[]=$row;
		}
		foreach($resultset as $set) { ?>
		<div class="container mx-auto p-2" style="max-width:800px;">
				<form class="row g-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$_REQUEST["id"]; ?>">
				  <div class="col-md-6">
				    <label for="contactname" class="form-label">Description</label>
				    <input type="text" class="form-control" name="description" maxlength="255" value="<?php echo $set["Description"];?>">
				  </div>
				  <div class="col-12">
				    <label for="contactemail" class="form-label">Category ID</label>
				    <input type="int" class="form-control" name="categoryid" maxlength="75" value="<?php echo $set["CategoryID"];?>">
				  </div>
					<div class="col-12">
						<label for="contacttitle" class="form-label">Winning Bid</label>
						<input type="decimal" class="form-control" name="winningbid" maxlength="75" value="<?php echo $set["WinningBid"];?>">
					</div>
				  <div class="col-12">
				    <label for="address" class="form-label">Winning Bidder</label>
				    <input type="int" class="form-control" name="winningbidder" maxlength="75" value="<?php echo $set["WinningBidder"];?>">
				  </div>
				  <div class="col-12">
				    <div class="form-check">
				      <input class="form-check-input" type="checkbox" name="delivered" value="1" <?php if(boolval($set["Delivered"])) { echo "checked";}?>>
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
		<a href="lot.php" class="mx-3 btn btn-secondary px-5">Back to Lot</a>
	</div>
</div>
<?php echo $js; ?>
</body>
</html>
