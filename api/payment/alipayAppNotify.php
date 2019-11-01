<?php
//服务器异步通知页面路径

require_once(dirname(__FILE__)."/../../include/common.inc.php");

//引入配置文件
require_once(dirname(__FILE__)."/alipay/alipay.php");
$payRequest = new alipay();

if($payRequest->respondApp()){
	echo "success";
}else{
	echo "fail";
}
