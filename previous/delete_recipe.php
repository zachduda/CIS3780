<?php

  if(!empty($_POST)) {
      $formempty = false;
      $validform = true;
      $rid = trim(htmlentities(stripslashes($_POST["rid"]))); ?>
      Are you sure you want to delete reicpe number <?php echo $rid; ?>?
      <a href="delete_recipe.php?rid=<?php echo $rid; ?>&answer=yes">Yes</a>
      <a href="delete_recipe.php?rid=<?php echo $rid; ?>&answer=no">No</a>
<?php
  } else if(empty($_GET)){
      $rid = trim(htmlentities(stripslashes($_POST["rid"])));
      $answer = trim(htmlentities(stripslashes($_POST["answer"])));
      if($answer == "yes") {
          require_once("creds.php");
          $sql = "DELETE FROM Recipe WHERE rid = :rid;";
          $sth = $conn->prepare($sql);
          $sth->bindParam(':rid', $rid, PDO::PARAM_INT);
          $sth->execute();
          echo "Deleted";
      } else {
          header("Location: listsql.php");
      }
  }
die;
?>
