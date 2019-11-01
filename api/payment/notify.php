<?php
//服务器异步通知页面路径

require_once(dirname(__FILE__)."/../../include/common.inc.php");

$code = !empty($_REQUEST['code']) ? trim($_REQUEST['code']) : '';

if(empty($code)) die('PayCode Request Error!');

//引入配置文件
require_once(dirname(__FILE__)."/$code/$code.php");
$payRequest = new $code();

if($payRequest->respond()){
	echo "success";
}else{
	echo "fail";
}