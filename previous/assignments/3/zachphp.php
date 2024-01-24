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
  <body>
    <div>
	  <div>
		  <h1>PHP Skill</h1>
	  </div>
	  <br>
	  <?php if(!empty($err)) { ?>
	   <div style="color:salmon;">
			<?php echo $err; ?>
	  </div>
	  <?php } else if(!empty($info)) { ?>
	   <div style="color:blue;">
			<?php echo $info; ?>
	  </div>
	  <?php } ?>
	  <br>
      <!-- Method="GET" means that values are sent via the URL (can be bookmarked) -->
      <!-- Method="POST" means that values are sent via HTTP headers -->
      <form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
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
