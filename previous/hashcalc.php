<?php 
$t = 0.05; // 50 milliseconds 

if(!empty($_REQUEST["t"])) {
	$t = intval($_REQUEST["t"]);
}

$cost = 3;
do {
    $cost++;
    $start = microtime(true);
    password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
    $end = microtime(true);
} while (($end - $start) < $t);

echo "Appropriate Cost Found: " . $cost;
exit;
?>