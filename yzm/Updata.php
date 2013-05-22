<?php
include ('config.php'); 
Updata();
function Updata() {
	
	$cnt = array();
	$sum = array();
	$pixel = array();
	$n = array();
	$m = array();
	$data = array();
	$percent = array();

	
	for ($j = 0, $i = 'A'; $j < 26; $j++, $i++) {
		$cnt[$i] = 0;
		$sum[$i] = 0;
		$pixel[$i] = 100000;
		$n[$i] = 0;
		$m[$i] = 0;
		$data[$i] = '0';
		$percent[$i] = 0;
		Update_updataWord($i, $n[$i], $m[$i], $percent[$i], $pixel[$i], $data[$i]);
	}
	
	//while($row = mysql_fetch_array($wordData)) {
		//$word = $row['word'];
		//echo "$word" . "\n";
  		//if ($row['pixel_num'] < $value[$word]) {
  			//$n[$word] = $row['n'];
  			//$m[$word] = $row['m'];
  			//$data[$word] = $row['data'];
  			//$value[$word] = $row['pixel_num'];
  			//$percent[$word] = $row['percent'];
  		//}
	//}
	for ($j = 0, $i = 'A'; $j < 26; $j++, $i++) {
		Update_saveMark($i, $n[$i], $m[$i], $data[$i], $percent[$i], $pixel[$i]);
	}
}

function Update_updataWord($w, & $n, & $m, & $percent, & $pixel, & $data) {
	$nNum = array();
	$mNum = array();
	$pixelNum = array();
	for ($i = 0; $i <= 1000; $i++) {
		$nNum[$i] = 0;
  		$mNum[$i] = 0;
  		$pixelNum[$i] = 0;
	}
		
	$wordData = Update_getWordData($w);
	while($row = mysql_fetch_array($wordData)){
  		$nNum[$row['n']]++;
  		$mNum[$row['m']]++;
  		$pixelNum[$row['pixel_num']]++;
	}
	$tmp = 0;
	foreach ($nNum as $key=>$age) {
    	if ($age > $tmp) {
    		$tmp = $age;
    		$n = $key;
    	}
	}
	$tmp = 0;
	foreach ($mNum as $key=>$age) {
    	if ($age > $tmp){
    		$tmp = $age;
    		$m = $key;
    	}
	}
	$tmp = 0;
	foreach ($pixelNum as $key=>$age) {
    	if ($age > $tmp){
    		$pixel = $key;
    		$tmp = $age;
    	}
	}
	
	$flag = 0;
	$wordData = Update_getWordData($w);
	while($row = mysql_fetch_array($wordData)){
  		if ($n == $row['n'] && $m == $row['m'] && $pixel == $row['pixel_num']) {
  			$percent = $row['percent'];
  			$data = $row['data'];
  			$flag = 1;
  			break;
  		}
	}
	
	if ($flag == 0) {
		echo $n . " " . $m . " " . $pixel . " ". $w. "\n";
		//echo "error in Updata.php, can't find";
	}
}
function Update_getWordData($w) {
	$result = mysql_query("SELECT * FROM data 
						   WHERE word ='$w'");
	return $result;
}

function Update_saveMark($word, $n, $m, $data, $percent, $pixelNum) {
	//$word = "'" . $word;
	//$percent = "'" . $percent . "'";
	$sql="UPDATE word_info 
 		  SET percent = '$percent', n = '$n', m = '$m', data = '$data', pixel_num = '$pixelNum' 
		  WHERE word = '$word'";
	if (!mysql_query($sql)) {
  		die('Error: ' . mysql_error());
  	}
}
?>
