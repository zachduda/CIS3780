<html>
  <head>
    <title>Select Project</title>
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
		  <h1 class="<?php if($anm) { ?>animated fadeInDown<?php } ?>">Select a Project</h1>
		  <p class="<?php if($anm) { ?>animated fadeInUp slow<?php } ?>">Please select 1 of the 3 tasks.</p>
	  </div>
	  <br>
		<div class="d-flex grid justify-content-center gap-5">
			  <div class="p-2">
				<h3>Task 1</h3>
				<p style="opacity:0.7";>Checks if an input was provided</p>
				<a href="1.php" class="animated flipInX py-3 px-5 btn btn-warning"><b>Name Checker</b></a>
			  </div>
			  <div class="p-2">
				<h3>Task 2</h3>
			  	<p style="opacity:0.7";>Checks for a valid email and a confirmation</p>
				<a href="2.php" class="animated flipInX py-3 px-5 btn btn-warning"><b>Email Checker</b></a>
			  </div>
			  <div class="p-2">
				<h3>Task 3</h3>
			  	<p style="opacity:0.7";>Checks if a password meets critera</p>
				<a href="3.php" class="animated flipInX py-3 px-5 btn btn-warning"><b>Password Validator</b></a>
			  </div>
			  <div class="p-2">
				<h3>Submission</h3>
			  	<p style="opacity:0.7";>Submitted to ASULearn for PHP Skill</p>
				<a href="result.php" class="animated flipInX py-3 px-5 btn btn-warning"><b>PHP Skill</b></a>
			  </div>
		</div>
    </div>
  </body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js" async defer crossorigin="anonymous"></script>
</html>