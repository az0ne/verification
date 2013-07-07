<?php
include ('config.php'); 
include ('Valite.php');
include ('Updata.php');
include ('my_helper.php');

$src = $_GET["src"];
$key = $_GET["key"];
//$src = "0.jpg";
//$key = "RUFB";
$valite = new Valite();
$valite->setImage($src);
$block = $valite->gao();  //$block是个数组，范围(1~n)
$cnt = count($block); 
if ($cnt != 4) {
	echo "can't distinguish! num is " . $cnt;
}
else {
	$key = strtoupper($key);
	if (check($key) == true) {
		for ($i = 1; $i <= 4; $i++) {
			saveMark($block[$i]['n'], $block[$i]['m'], $block[$i]['percent'],
					 $block[$i]['data'], $block[$i]['pixelNum'], $key[$i - 1]);
		}
		//Updata();
		echo "OK";
	}	
	else echo "NO";
}

function check($key) {
	for ($i = 0; $i < 4; $i++)
		if (!('A' <= $key[$i] && $key[$i] <= 'Z'))
			return false;
	$res = sendPost('1128', '111', $key);
	//$res = iconv('GB2312', 'UTF-8', $res);
	//echo strlen($res);
	//echo $res . "\n";
	if(strstr($res,'您输入的验证码错误'))
		return false;
	else return true;
}

function sendPost($user, $password, $yzm) {
	$cookie_file = dirname(__FILE__) . '/cookie.txt'; 
	$jw_cookie = file_get_contents($cookie_file);
	//echo $jw_cookie . "\n";
	$res=post('http://202.114.74.198/servlet/Login',
			  $jw_cookie,
			  'id='.$user.'&pwd='.$password.'&yzm='.$yzm.'&who=student&submit=%25C8%25B7%2B%25B6%25A8',''
			  ,0);
	//$res = get('http://202.114.74.198/stu/newstu_index.jsp',$jw_cookie);
	//echo $res;
    return $res;
}

function getKey($block, $wordData) {
	$word = 'A';
	$value = 1.0;
	while($row = mysql_fetch_array($wordData)) {
		$key = $row['key'];
  		$percent = $row['percent'];
  		$dif = abs($percent - $block['percent']);
  		if ($dif < $value) {
  			$value = $dif;
  			$word = $key;
  		}
  	}
  	$res = array($word, $value);
  	return $res;
}

function saveMark($n, $m, $percent, $data, $pixelNum, $word) {
	//echo $n . " " . $m . " " . $percent . " " . $data . " " . $word . "\n";
	$sql="INSERT INTO data (word, n, m, data, percent, pixel_num)
		 VALUES ('$word', '$n', '$m',  '$data', '$percent', $pixelNum)";
	
	if (!mysql_query($sql)) {
  		die('Error: ' . mysql_error());
  	}
}
mysql_close($conn);
?>
