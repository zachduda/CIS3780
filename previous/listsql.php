<html>
<table>
<?php

require_once("creds.php");

$sql = "SELECT rid, title FROM recipe;";
$sth = $conn->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
do {
	echo "<tr>";
	echo "<td align='center'>";
	echo $result["rid"];
	echo "</td>";
	echo "<td>";
	echo $result["title"];
	echo "</td>";
	echo "<td>";
	echo "<form method='POST' action='delete_recipe.php'>";
	echo "<input type='submit'>Delete</input>";
	echo "</td>";
	echo "<td>";
	echo "<form method='POST' action='updaterecipe.php'>";
	echo "<input type='submit'>Update</input>";
	echo "</td>";
	echo "</form>";
	echo "</tr>";
} while ($result = $sth->fetch(PDO::FETCH_ASSOC));

?>
</table>
</html>
