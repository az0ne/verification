<?php
function get($url,$request_cookie='',$type=1){
	$ch=curl_init($url);
	curl_setopt($ch,CURLOPT_HEADER,$type==1?true:false);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_COOKIE,$request_cookie);
	curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.83 Safari/537.1');
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
	$response=$type==1?get_response(curl_exec($ch)):curl_exec($ch);
	curl_close($ch);
	return $response;
}

function post($url,$request_cookie='',$post_data='',$referer='',$type=1){
	$ch=curl_init($url);
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch,CURLOPT_COOKIE,$request_cookie);
	curl_setopt($ch,CURLOPT_REFERER,$referer);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
	$response=$type==1?get_response(curl_exec($ch)):curl_exec($ch);
	curl_close($ch);
	return $response;
}

function get_response($data){
	list($header,$body)=explode("\r\n\r\n",$data);
	preg_match("/set\-cookie:([^\r\n]*)/i",$header,$matches);
	if($matches!=NULL)
		return array('body'=>$body,'cookie'=>$matches[1]);
	return array('body'=>$data,'cookie'=>'');
}

function guolv(&$e){
	if(is_array($e)){
		foreach ($e as &$v)
			if(!is_array($v))
				$v=htmlentities($v,ENT_QUOTES,'UTF-8');
			else
				guolv($v);
	}else{
		$e=htmlentities($e,ENT_QUOTES,'UTF-8');
	}
}

function success($data=''){
	if($data==''){
		return '{"msg":"success"}';
	}else{
		return json_encode(array('msg'=>'success','data'=>$data));
	}
}

function error($data=''){
	if($data==''){
		return '{"msg":"error"}';
	}else{
		return json_encode(array('msg'=>'error','data'=>$data));
	}
}

function num2str($num){
	$arr=array('1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','7'=>'日');
	return $arr[$num];
}

function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
if(is_array($multi_array)){
foreach ($multi_array as $row_array){
if(is_array($row_array)){
$key_array[] = $row_array[$sort_key];
}else{
return false;
}
}
}else{
return false;
}
array_multisort($key_array,$sort,$multi_array);
return $multi_array;
}