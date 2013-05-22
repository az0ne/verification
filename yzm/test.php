<?php

include ('Valite.php');

$valite = new Valite();
$valite->setImage('ceshi.jpeg');
$ert = $valite->gao();
//$ert = "1234";
print_r($ert);
echo '<br><img src="abc.jpeg"><br>';

?>