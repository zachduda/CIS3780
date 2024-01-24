<?php
$valid = false;
$error = "";

function clean($str) {
		return trim(htmlentities(stripslashes($str)));
}

if (empty($error) && $_SERVER["REQUEST_METHOD"] == "POST") {
	$rid = clean($_POST['rid']);

	if(!is_numeric($rid)) {
		$error = "Recipe ID was not a number";
	}

	if($rid > 2147483647) {
		$error = "Recipe ID integear value is too high";
	}

	$title = clean($_POST['title']);
	if(empty($title)) {
		$error = "Title cannot be left blank";
	}
	
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recipe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

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
    <div class="container" style="padding:30px;">
		<?php if($valid) { ?>
			<div class="container mx-auto text-center">
				<h1>Submit Recipe</h1>
			</div>
			<p>You entered:</p>
			<div class="container p-5">
				<p>
					<?php print_r($_POST); ?>
				</p>
				<br>
				<hr>
				<div class="p-2">
					<button type="submit" class="animated flipInX py-2 px-5 btn btn-dark" onclick="history.back()"><b>Back</b></button>
				</div>
			</div>
		<?php } else { ?>
		<div class="container mx-auto text-center">
		    <h1>Input Recipe</h1>
		</div>
		  <form method="POST" class="container" action="recipe.php" style="max-width: 500px;">
			<div class="p-2">
				<label for="title" class="form-label">Title:</label>
				<input class="form-control" type="text" name="title" value="<?php echo $title;?>">
			</div>
			<div class="p-2">
				<label for="rid" class="form-label">Recipe ID:</label>
				<input class="form-control" type="text" name="rid" value="<?php echo $rid;?>">
			</div>
			<div class="p-2">
				<label for="card" class="form-label">Card:</label>
				<input class="form-control" type="text" name="card" value="<?php echo $card;?>">
			</div>
			<div class="p-2">
				<label for="serves" class="form-label">Serves (qty)</label>
				<input class="form-control" type="text" name="serves" value="<?php echo $serves;?>">
			</div>
			<div class="p-2">
				<label for="prep" class="form-label">Prep Time (min):</label>
				<input class="form-control" type="password" name="prep" value="<?php echo $prep;?>">
			</div>
			<div class="p-2">
				<label for="cook" class="form-label">Cook Time (min):</label>
				<input class="form-control" type="password" name="cook" value="<?php echo $cook;?>">
			</div>
			<?php if(!empty($error)) { ?>
			<div class="p-2" style="color:salmon;">
				<strong>Error:</strong>
				<p><?php echo $error; ?></p>
			</div>
			<?php } ?>
			<div class="p-2">
			  <button type="submit" class="animated flipInX py-2 px-5 btn btn-dark"><b>Send</b></button>
			</div>
		  </form>
		<?php } ?>
	</div>
</body>
</html>
