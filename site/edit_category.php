<?php
$error = "";
$success = "";
$info = "";
$reset_confirm = false;

require_once("_sql.php");
echo $headers;

echo "<h1 class='mt-4 px-5 text-center mx-auto'>Manage Categories</h1>";
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
	$desc = clean($_POST["description"]);
	if(empty($desc)) {
		$error = "Please provide a Description for the new Category.";
	} else if(strlen($desc) > 75) {
		$error = "Description (" . $desc . ") exceeds 75 characters.";
	} else {
			$sql_in = "UPDATE `Category` SET `Description` = :desc WHERE `CategoryID` = :id";
			$sth_in = $conn->prepare($sql_in);
			$sth_in->bindParam(":desc", $desc, PDO::PARAM_STR, 75);
      $sth_in->bindParam(":id", $id, PDO::PARAM_INT);
			$sth_in->execute();
			$success = "Description changed for <b>Category #".$id. "</b>";
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
	$sql_get = "SELECT * FROM `Category` WHERE CategoryID = ".$id." LIMIT 1";
	$result = $conn->query($sql_get);

	if ($result->rowCount() > 0) {
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$resultset[]=$row;
		}
		foreach($resultset as $set) { ?>
		<div class="container mx-auto p-2" style="max-width:800px;">
				<form class="row g-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $_REQUEST["id"];?>">
				  <div class="col-md-6">
				    <label for="businessname" class="form-label">Category Description</label>
				    <input type="text" class="form-control" name="description" maxlength="75" value="<?php echo $set["Description"];?>">
				  </div>
				  <div class="col-12">
						<input type="hidden" name="csrf" value="<?php echo $csrf;?>">
				    <input type="submit" name="submit" value="Add Category" class="btn btn-success"></input>
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
		<a href="category.php" class="mx-3 btn btn-secondary px-5">Back to Categories</a>
	</div>
</div>
<?php echo $js; ?>
</body>
</html>
