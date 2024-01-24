<?php
  // Establish database connection to SQL, passes $conn
  require_once("_sql.php");
  echo $headers;
?>
<div class="pt-20 p-5 mb-4 rounded-3">
      <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Donation System</h1>
        <br><br>
        <?php if($conn) { ?>
        <span class="badge rounded-pill text-bg-success py-2 px-5">Database Online</span>
        <?php } else { ?>
          <span class="badge rounded-pill text-bg-danger py-2 px-5">Database Offline</span>
        <?php } ?>
        <br><br>
        <p class="col-md-8 fs-4">Easily create, edit, and manage a complex donation system in your browser.</p>
        <a href="/donors.php" class="btn btn-primary btn-lg">View Donors</a>
      </div>
    </div>
      <?php echo $js; ?>
  </body>
</html>
