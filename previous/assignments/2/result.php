<?php
$banner;
$fn;
$ln;
$pic;
$err;
$info;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$banner = $_POST["banner"];
	$fn = $_POST["fn"];
	$ln = $_POST["ln"];
	$pic = $_POST["pic"];

	if(empty($_POST["banner"])) {
		$err = "You didn't provide your <b>Banner ID</b>";
	} else if(empty($_POST["fn"])) {
		$err = "You didn't provide your <b>First Name</b>";
	} else if(empty($_POST["ln"])) {
		$err = "You didn't provide your <b>Last Name</b>";
	} else if(empty($_POST["pic"])) {
		$err = "You didn't provide your <b>Picture URL</b>";
	} else {
		$fnlen = strlen($fn);
		$tlen = strlen($fn.$ln);
		$bint = intval($banner);
		$blen = strlen($bint);
		
		$pic_val = filter_var($pic, FILTER_VALIDATE_URL);

		if($fnlen > 15 || $tlen >= 30 || $blen !== 9 || !$bint || !$pic_val) {
			$err = "Please resolve the following: <ul>";
			if($fnlen > 15) {
				$err .= "<li><b>First Name</b> cannot exceed 15 characters.</li>";
			}
			if(!$tlen > 30) {
				$err .= "<li>Your <b>Full Name</b> (First & Last) cannot exceed 30 characters.</li>";
			}
			if(!$bint) {
				$err .= "<li>Your <b>Banner ID</b> must be a number.</li>";
			} else if($blen !== 9) {
				$err .= "<li>Your <b>Banner ID</b> is not 9 characters.</li>";
			}
			
			if(!$pic_val) {
				$err .= "<li><b>Picture</b> must be a URL.</li>";
			}
			
			$err .= "</ul>";
		}

		$info = "Inputs are all valid!";
	}
}

$anm = true;
if(!empty($info) || !empty($err)) { $anm = false; }
?>
<html>

  <head>
    <title>Assignment #2</title>
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
		  <h1 class="<?php if($anm) { ?>animated fadeInDown<?php } ?>">Assignment #2</h1>
		  <p class="<?php if($anm) { ?>animated fadeInUp slow<?php } ?>">Fill out the form below.</p>
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
			<label for="banner" class="form-label">Banner ID</label>
			<input class="form-control" type="number" name="banner" value="<?php echo $banner;?>">
		</div>
		<div class="p-2">
			<label for="fn" class="form-label">First Name</label>
			<input class="form-control" type="text" name="fn" value="<?php echo $fn;?>">
		</div>
		<div class="p-2">
			<label for="ln" class="form-label">Last Name</label>
			<input class="form-control" type="text" name="ln" value="<?php echo $ln;?>">
		</div>
		<div class="p-2">
			<label for="pic" class="form-label">Picture URL</label>
			<input class="form-control" type="text" name="pic" value="<?php echo $pic;?>">
		</div>
		<div class="p-2">
			<p style="opacity:0.5;">Banner ID should be 9 digits, names shouldn't exceed 30 characters total, and the picture should be a valid URL.</p>
		</div>
        <div class="p-2">
          <button type="submit" class="animated flipInX py-2 px-5 btn btn-primary"><b>Send</b></button>
        </div>
      </form>
    </div>
  </body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js" async defer crossorigin="anonymous"></script>
</html>