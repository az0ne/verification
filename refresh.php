<?php
	include ('my_helper.php');
	$res=get('http://202.114.74.198/GenImg?t='.rand(),'',1);
	header('Content-type:image/jpeg');
//	ob_clean();
//	echo $res['body'];
//	echo $res['cookie']. n;
	
	$cookie_file = dirname(__FILE__) . '\cookie.txt'; 
	$file = fopen($cookie_file, "w");
	fwrite($file, $res['cookie']);
	fclose($file);
	
	$jpg_file = dirname(__FILE__) . '\0.jpg'; 
	$file = fopen($jpg_file, "w");
	fwrite($file, $res['body']);
	fclose($file);
	echo $jpg_file . "\n";
?>
