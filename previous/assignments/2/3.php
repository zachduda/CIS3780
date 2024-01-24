<?php
$cpwd;
$pwd;
$err;
$info;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if(empty($_POST["password"])) {
		$err = "You didn't provide a password.";
	} else {
		$pwd = $_POST["password"];
		$cpwd = $_POST["cpassword"];
		$uprg = preg_match('@[A-Z]@', $pwd);
		$lwrg = preg_match('@[a-z]@', $pwd);
		$numrg = preg_match('@[0-9]@', $pwd);
		$spc = preg_match('@[^\w]@', $pwd);
		$len = strlen($pwd);

		if(!$uprg || !$lwrg || !$numrg || !$spc || $len < 8 || $cpwd !== $pwd) {
			$err = "Your password needs: <ul>";
			if(!$uprg) {
				$err .= "<li>A uppercase character</li>";
			}
			if(!$lwrg) {
				$err .= "<li>A lowercase character</li>";
			}
			if(!$numrg) {
				$err .= "<li>A number</li>";
			}
			if(!$spc) {
				$err .= "<li>A special character (ie: !)</li>";
			}
			if($len < 8) {
				$err .= "<li>At least 8 characters</li>";
			}
			if($cpwd !== $pwd) {
				$err .= "<li>Your passwords must match</li>";
			}
			$err .= "</ul>";
		}
		$info = "Password Passed. Congrats!";
	}
}

$anm = true;
if(!empty($info) || !empty($err)) { $anm = false; }
?>
<html>

  <head>
    <title>Password</title>
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
	  <div class="container mx-auto text-center">
		  <h1 class="<?php if($anm) { ?>animated fadeInDown<?php } ?>">Password Checker</h1>
		  <p class="<?php if($anm) { ?>animated fadeInUp slow<?php } ?>">Please input a password below.</p>
	  </div>
	  <br>
	  <?php if(!empty($err)) { ?>
	   <div class="animated fadeInUp faster alert alert-danger mx-auto" style="max-width: 450px" role="alert">
			<?php echo $err; ?>
	  </div>
	  <?php } else if(!empty($info)) { ?>
	   <div class="animated fadeInUp faster alert alert-success mx-auto" style="max-width: 450px" role="alert">
			<?php echo $info; ?>
	  </div>
	  <?php } ?>
	  <br>
      <!-- Method="GET" means that values are sent via the URL (can be bookmarked) -->
      <!-- Method="POST" means that values are sent via HTTP headers -->
      <form method="POST" class="container <?php if($anm) { ?>animated fadeIn delay-1s<?php }?>" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" style="max-width: 500px;">
		<div class="p-2">
			<label for="password" class="form-label">Password</label>
			<input class="form-control" type="password" name="password" value="<?php echo $pwd;?>">
		</div>
		<div class="p-2">
			<label for="password" class="form-label">Confirm Password</label>
			<input class="form-control" type="password" name="cpassword" value="<?php echo $cpwd;?>">
		</div>
		<div class="p-2">
			<p style="opacity:0.5;">Password should be at least 8 characters, contain a special character, include a number, and uses both lower and uppercase letters.</p>
		</div>
        <div class="p-2">
          <button type="submit" class="animated flipInX py-2 px-5 btn btn-primary"><b>Send</b></button>
        </div>
      </form>
    </div>
  </body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js" async defer crossorigin="anonymous"></script>
</html>