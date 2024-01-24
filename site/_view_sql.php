<?php
require_once("_sql.php");
$sql = "SHOW TABLES FROM dudazr_db";
$result = mysqli_query($sql);
if (!$result) {
echo "DB Error, could not list tables\n";
echo 'MySQL Error: ' . mysqli_error();
exit;
}
while ($row = mysqli_fetch_row($result)) {
echo "Table: {$row[0]}\n";
}
mysqli_free_result($result);
?>
