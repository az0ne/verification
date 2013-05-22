<?php
include("config.php"); 
include ('Valite.php');

$valite = new Valite();
//$valite->setImage($_GET["src"]);
$valite->setImage("0.jpg");
$block = $valite->gao();  //$block是个数组，范围(1~n)
//$valite->Draw();
$cnt = count($block); 
$res = "";

for ($i = 1; $i <= $cnt; $i++) {
	$wordData = getWordInfo();
	$key = getKey($block[$i], $wordData);
	$res .= $key[0];
	//DrawWrod($block[$i]['data'], $block[$i]['n'], $block[$i]['m']);
}
echo $res;


//===========================function======================================
function getWordInfo() {
	$result = mysql_query("SELECT * FROM word_info");
	return $result;
}

function dealData($block, $n, $m) {
	$data = array();
	//
	for ($i = 0; $i < $n; $i++) {
		$data[$i] = array();
		for ($j = 0; $j < $m; $j++) {
			$data[$i][$j] = $block[$i * $m + $j];
			//echo strlen($block) . ' ' . $n * $m . "\n";
		}
	}
	return $data;
}

function getSameValue($blockA, $blockB) {
	$na = $blockA['n'];
	$nb = $blockB['n'];
	$ma = $blockA['m'];
	$mb = $blockB['m'];
	$dataA = dealData($blockA['data'], $na, $ma);
	$dataB = dealData($blockB['data'], $nb, $mb);
	$sameValue = -1000;
	$sumA = 0; 
	$sumB = 0;
	for ($i = 0; $i < $na; $i++) 
		for ($j = 0; $j < $ma; $j++)
			if ($dataA[$i][$j] == 1)
				$sumA++;
	for ($i = 0; $i < $nb; $i++) 
		for ($j = 0; $j < $mb; $j++)
			if ($dataB[$i][$j] == 1)
				$sumB++;
	for ($si = 0; $si < $na; $si++)
		for ($sj = 0; $sj < $ma; $sj++) {
			$res = 0;
			for ($i = 0; $i < $nb; $i++){
				$ax = $si + $i;	
				if ($ax >= $na) break;
				for ($j = 0; $j < $mb; $j++) {
					$ay = $sj + $j;
					if ($ay >= $ma) break;
					if ($dataA[$ax][$ay] == $dataB[$i][$j] && $dataA[$ax][$ay] == 1) {
						$res++;
					}
				}
			}
			$sameValue = max($sameValue, $res - ($sumA + $sumB - $res * 2) * 2);
		}
	return $sameValue;
}

function getKey($block, $wordData) {
	$word = 'A';
	$value = -1000;
	while($row = mysql_fetch_array($wordData)) {
		$dbWord = $row['word'];
		if ($row['n'] == '0') continue;
  		$sameValue = getSameValue($block, $row);
  		$sameValue = max(getSameValue($row, $block), $sameValue);
  		if ($value < $sameValue) {
  			$value = $sameValue;
  			$word = $dbWord;
  		}
  		//echo $dbWord . "\n";
  		//if ($dbWord == 'U') {
  		//	echo "=========================\n";
  		//DrawWrod($block['data'], $block['n'], $block['m']);
  		//	echo $sameValue . "\n";
  		//	DrawWrod($row['data'], $row['n'], $row['m']);
  		//}
		//if ($dbWord == 'E') {
			//DrawWrod($block['data'], $block['n'], $block['m']);
		//	echo $sameValue . "\n";
  		//	DrawWrod($row['data'], $row['n'], $row['m']);
  		//}
  	}
  	//echo $value . "\n";
  	$res = array($word, $value);
  	return $res;
}

function DrawWrod($data, $n, $m) {
	//$file = fopen("test.txt","a");
	$res = "";
	echo "\n";
	for ($i = 0; $i < $n; $i++) {
		for ($j = 0; $j < $m; $j++)
			echo $data[$i * $m + $j];
			//$res .= $data[$i * $m + $j];
		echo "\n";
		//$res .= "\n";		
	}
	//fwrite($file,$res);
	//fclose($file);
}
mysql_close($conn);
?>
