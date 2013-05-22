<?php
include ('config.php'); 
//Updata();
$ok = $_GET["success"];
//$ok = 1;
$result = mysql_query("SELECT * FROM success");
$sum = 0;
$success = 0;
while($row = mysql_fetch_array($result)) {
	$sum = $row['sum'];
	$success = $row['success'];
}
$sum++;
if ($ok == 1) {
	$success++;
}

$sql="UPDATE success 
 	  SET success = '$success', sum = '$sum'
	  WHERE sid = '1'";
if (!mysql_query($sql)) {
  	die('Error: ' . mysql_error());
}

?>
