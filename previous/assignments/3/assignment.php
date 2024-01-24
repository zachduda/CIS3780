<?php
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = "";
$success = "";
$info = "";
$reset_confirm = false;

session_start(); ?>
<html>
  <head>
    <title>Assignment #3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.compat.css" rel="stylesheet" crossorigin="anonymous">

    <!-- optional fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <style>* {font-family: 'Roboto', sans-serif;}</style>

	<!-- favicon stuff -->
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	
  </head>
<body style="color:white;background-color:#50607a;font-size:18px;">
<?php
echo "<h1 class='mt-4 px-5 text-center mx-auto'>PHP & SQL Assignment</h1>";

require_once("../../fc_creds.php");

function clean($str) {
	return rtrim(htmlspecialchars($str, ENT_QUOTES, 'UTF-8', true));
}

function genCSRF() {
	$length = 8;
	return bin2hex(random_bytes($length));
}

$skip_create = false;
echo "<br>";

$pend_del = 0; // for visual use only

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$skip_create = true;
	
	if($_POST["submit"] == "Delete") {
		$id = clean($_POST["id"]);
		if(empty($_POST["confirm"]) || clean($_POST["confirm"]) !== $id) {
			$info = "<b>Are you sure?</b> Click <b>Delete</b> again for Customer <b>#".$id."</b> to remove this customer.";
			$pend_del = $id; // visual only
		} else if(clean($_POST["confirm"]) == $id) {
			if(empty($id)) {
				$error = "Missing ID to delete";
			}
			$sql_del = "DELETE FROM customers WHERE id = ".$id.";";
			$res_del = $conn->query($sql_del);
			$success = "Customer ID <b>#".$id. "</b> has been deleted.";
		}
	}
	if($_POST["submit"] == "Reset the Table") {
		if(empty($_POST["reset-confirm"])) {
			$info = "Click <b>Reset Table</b> again to confirm you want to reset all tables.";
			$reset_confirm = true;
		} else {
			$sql_place = "DELETE FROM customers;ALTER TABLE customers AUTO_INCREMENT = 1;INSERT INTO customers (firstname, lastname, address, state, zip) VALUES ('Zach', 'Duda', '37 King Street', 'NC', '28607');INSERT INTO customers (firstname, lastname, address, state, zip) VALUES ('Bob', 'Ross', '1200 N Minnetrista Pkwy', 'IN', '47303');INSERT INTO customers (firstname, lastname, address, state, zip) VALUES ('Little', 'Debbie', '2 Sugar Cir', 'TN', '37315');INSERT INTO customers (firstname, lastname, address, state, zip) VALUES ('Ronald', 'McDonald', '10 N Carpenter St', 'IL', '60607');INSERT INTO customers (firstname, lastname, address, state, zip) VALUES ('Tim', 'Apple', '1 Infinite Loop', 'CA', '95014');";
			$res_place = $conn->query($sql_place);
			$success = "The table has been reset to its default values.";
			$res_place->closeCursor();
		}
	}
	
	if($_POST["submit"] == "Add Customer") {
		if(empty($_SESSION["csrf"])) {
			$error = "CSRF token missing from session. Try adding your customer again.";
		} if(empty($_POST["csrf"])) {
			$error = "CSRF token not sent with form. Try adding your customer again.";
		} else if(!hash_equals(clean($_POST["csrf"]), $_SESSION["csrf"])) {
			$error = "CSRF token mismatch. Customer wasn't added as it may be a duplicate. Try it again!";
		} else {
			$fn = clean($_POST["firstname"]);
			$ln = clean($_POST["lastname"]);
			$ad = clean($_POST["address"]);
			$st = clean($_POST["state"]);
			$zp = clean($_POST["zipcode"]);
			if(empty($fn) || empty($ln) || empty($ad) || empty($st) || empty($zp)) {
				$error = "Please provide a First Name, Last Name, Address, State & Zip Code for the new Customer.";
			} else if(strlen($fn) > 15) {
				$error = "First Name (" . $fn . ") exceeds 15 characters.";
			} else if (strlen($ln) > 30) {
				$error = "Last Name (" . $ln . ") exceeds 30 characters.";
			} else if (strlen($ad) > 255) {
				$error = "Address (" . $ad . ") exceeds 255 characters.";
			} else if (strlen($st) !== 2) {
				$error = "State (" . $st . ") must be a 2 letter state abbrv. (ie: NC)";
			} else if(strlen($zp) !== 5) {
				$error = "Zip Code (" . $zp . ") must be 5 numbers. (ie: 28607)";
			} else {
				$sql_in = "INSERT INTO customers (firstname, lastname, address, state, zip) VALUES (:fn, :ln, :ad, :st, :zp)";
				$sth_in = $conn->prepare($sql_in);
				$sth_in->bindParam(":fn", $fn, PDO::PARAM_STR, 15);
				$sth_in->bindParam(":ln", $ln, PDO::PARAM_STR, 30);
				$sth_in->bindParam(":ad", $ad, PDO::PARAM_STR, 255);
				$sth_in->bindParam(":st", $st, PDO::PARAM_STR, 2);
				$sth_in->bindParam(":zp", $zp, PDO::PARAM_STR, 5);
				$sth_in->execute(); 
				$success = "Added Customer to Table: <b>" . $fn . " " . $ln . "</b>";
			}
		}
	}
}

$csrf = genCSRF();
$_SESSION["csrf"] = $csrf;

if(!$skip_create) {
	$sql_create = "CREATE TABLE IF NOT EXISTS `customers` (
		`id` int(11) unsigned NOT NULL auto_increment,
		`firstname` varchar(15) NOT NULL default '',
		`lastame` varchar(30) NOT NULL default '',
		`address` varchar(255) NOT NULL default '',
		`state` CHAR(2) CHARACTER SET ascii NOT NULL default '',
		`zip` varchar(5) NOT NULL default '',
		PRIMARY KEY  (`id`)
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

$sql_get = "SELECT * FROM `customers`";
$result = $conn->query($sql_get);

if ($result->rowCount() > 0) {
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$resultset[]=$row;
	}
	$html = "<table class='container table table-dark table-hover table-striped table-bordered border-secondary'><thead><tr><th class='row'>".implode('</th><th class="col">',array_keys($resultset[0]))."</th><th class='col'>modify</th></tr></thead>";
	foreach($resultset as $set){
		$html .= "<tbody><tr><td class='row p-4 text-center'>".implode('</td><td>',$set).'</td><td class="p-0 m-0 pt-1"><form method="POST" class="p-0 m-0 mx-auto text-center" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">';
		if($pend_del == $set["id"]) {
			$html .= '<input name="id" value="'.$set["id"].'" type="hidden"><input type="hidden" name="confirm" value="'.$set["id"].'"><input name="submit" type="submit" class="btn btn-danger font-weight-bold text-center m-0 p-0 mx-auto" style="height:100%;width:100%" value="Delete"></input>';
		} else {
			$html .= '<input name="id" value="'.$set["id"].'" type="hidden"><input name="submit" type="submit" class="btn btn-warning text-center m-0 p-0 mx-auto" style="height:100%;width:100%" value="Delete"></input>';	
		}
		$html .= '</form>';
		$html .= '<a href="edit.php?id='.$set["id"].'" class="mt-2 btn btn-secondary text-center m-0 p-0 mx-auto" style="height:100%;width:100%">Edit</a>';
		$html .= '</td></tr></tbody>';
	}
	echo $html.'</table>';
	echo "<br>";
} else { ?>

<div class="alert alert-secondary m-4 p-4 mx-auto <?php if(empty($error) && empty($info) && empty($success)) { ?>animated fadeInUp faster<?php }?>" style="max-width:600px;" role="alert">
  <h4 class="alert-heading">No Customers in Database</h4>
  <p>All customers have been removed from the database table.</p>
  <hr>
  <p class="mb-0">Click "Add Customer" below to insert more records into the table, or "Reset Table" to reinsert the default records.</p>
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
		<button type="button" class="mx-3 btn btn-success px-5" onclick="document.getElementById('buttons').style.display = 'none';document.getElementById('add').style.display = null;">Add Customer</button>
	</div>
</div>
<div id="add" class="container animated fadeIn fast mx-auto px-2" style="max-width:600px;display:none;">
	<div class="mx-auto">
		<button type="button" class="btn btn-dark px-5" onclick="document.getElementById('buttons').style.display = 'flex';document.getElementById('add').style.display = 'none';">Back</button>
	</div>
	<br>
	<h3>Add a Customer</h3>
	<p>Insert the details of the customer below. Use a 2 letter state and 5 digit Zip Code.</p>
	<form class="row g-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<div class="col-md-4">
			<label for="firstname" class="form-label">First Name</label>
			<input type="text" class="form-control" maxlength="15" name="firstname" required>
		</div>
		<div class="col-md-4">
			<label for="lastname" class="form-label">Last Name</label>
			<input type="text" class="form-control" maxlength="30" name="lastname" required>
		</div>
		<div class="col-md-6">
			<label for="address" class="form-label">Address</label>
			<input type="text" class="form-control" maxlength="255" name="address" required>
		</div>
		<div class="col-md-3">
			<label for="state" class="form-label">State</label>
			<select class="form-select" name="state" required>
				<option value="" selected>--</option>
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
		<div class="col-md-3">
			<label for="zipcode" class="form-label">Zip Code</label>
			<input type="number" class="form-control" min="10000" max="99999" name="zipcode" required>
		</div>
		<input type="hidden" name="csrf" value="<?php echo $csrf;?>">
		<div class="col-12">
			<input type="submit" name="submit" class="mt-2 btn btn-success font-weight-bold py-2 px-5 float-end" value="Add Customer">
		</div>
	</form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js" async defer crossorigin="anonymous"></script>
</body>
</html>