<?php

include ('my_helper.php');
sendPost('1128', '111', 'GBDC');	
function sendPost($user, $password, $yzm) {
	$cookie_file = dirname(__FILE__) . '/cookie.txt'; 
	$jw_cookie = file_get_contents($cookie_file);
	echo $jw_cookie . "\n";
	$res=post('http://202.114.74.198/servlet/Login',
			  $jw_cookie,
			  'id='.$user.'&pwd='.$password.'&yzm='.$yzm.'&who=student&submit=%25C8%25B7%2B%25B6%25A8',''
			  ,0);
	//$res = get('http://202.114.74.198/stu/newstu_index.jsp',$jw_cookie);
	echo $res;
    return $res;
}

?>