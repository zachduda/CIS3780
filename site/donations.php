<?php
require_once("_sql.php");

$error = "";
$success = "";
$info = "";
echo $headers;

echo "<h1 class='mt-4 px-5 text-center mx-auto'>Donation List</h1>";
echo "<br>";

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

$iid = 0;
$sql_get = "SELECT * FROM `Item`";
if(!empty($_REQUEST["id"])) {
	$iid = intval(clean($_REQUEST["id"]));
	$sql_get .= " WHERE ItemId = " . $iid . " LIMIT 1";
}
$result = $conn->query($sql_get);

if ($result->rowCount() > 0) {
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$resultset[]=$row;
	}
	if(!empty($iid)) { 
		foreach($resultset as $set) {?>
			<div class="text-white p-5">
				<h2 class="font-bold text-danger"><?php echo $set["Description"]; ?></h2>
				<p class="p-4">Reatil Value: $<?php echo $set["RetailValue"]; ?></p>
				<p class="p-4">Donated By Donor #<?php echo $set["DonorID"]; ?></p>
				<p class="p-4">From Lot #<?php echo $set["LotID"]; ?></p>
			</div>
	<?php 
		}
	} else {
	$html = "<table class='container table table-dark table-hover table-striped table-bordered border-secondary'><thead><tr><th class='row'>".implode('</th><th class="col">',array_keys($resultset[0]))."</th><th class='col'>modify</th></tr></thead>";
	foreach($resultset as $set){
		$html .= "<tbody><tr><td class='row p-4 text-center'>".implode('</td><td>',$set).'</td><td class="p-0 m-0 pt-1">';
		$html .= '<a href="donations.php?id='.$set["ItemID"].'" class="mt-2 btn btn-primary text-center m-0 p-0 mx-auto" style="height:100%;width:100%">Print Receipt</a>';
		$html .= '</td></tr></tbody>';
	}
	echo $html.'</table>';
	echo "<br>";
	} 
} else { ?>

<div class="alert alert-secondary m-4 p-4 mx-auto <?php if(empty($error) && empty($info) && empty($success)) { ?>animated fadeInUp faster<?php }?>" style="max-width:600px;" role="alert">
  <h4 class="alert-heading">No Items in Database</h4>
  <p>All Items have been removed from the database table.</p>
  <hr>
  <p class="mb-0">Click "Add Items" below to insert more records into the table, or "Reset Table" to reinsert the default records.</p>
</div>

<?php } ?>
</div>
<?php echo $js; ?>
</body>
</html>
