<?php
/**
 * 第三方登录整合接口
 *
 * @version        $Id: login.php $v1.0 2015-2-14 下午17:54:18 $
 * @package        HuoNiao.Login
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

$type = $_GET['type'];
$type = empty($type) ? "qq" : $type;
$login = array();
$set_modules = true;

require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(dirname(__FILE__)."/../api/login/$type/$type.php");

//$referer = parse_url($_SERVER['HTTP_REFERER']);
//$_SESSION['redirectUrl'] = $referer['host'] == $cfg_basehost ? $_SERVER['HTTP_REFERER'] : $cfg_secureAccess.$cfg_basehost;

$dsql = new dsql($dbo);
$typeName = "Login".$type;
$loginConfig = new $typeName();
$data = array();

$callback = $login[0]['callback'];

//主要配合微信扫码登录成功后不让窗口关闭
if($notclose){
	$callback .= "&notclose=1";
}

//主要配合微信扫码登录成功后不让窗口关闭
if($qr){
	$callback .= "&qr=".$qr;
}

$data['callback'] = $callback;

$uid = $userLogin->getMemberID();
if($uid != -1){
	$data['loginUserid'] = $uid;
}

$archives = $dsql->SetQuery("SELECT * FROM `#@__site_loginconnect` WHERE `state` = 1 AND `code` = '$type'");
$loginData = $dsql->dsqlOper($archives, "results");
if($loginData){
	$config = unserialize($loginData[0]['config']);
	foreach ($config as $key => $value) {
		$data[$value['name']] = $value['value'];
	}

	//登录
	if($action == ""){
		$furl = $_GET['furl'];
		if($furl){
			global $cfg_cookiePath;
			PutCookie("furl", $furl, 15, $cfg_cookiePath);
		}
		echo $loginConfig->login($data);

	//登录成功
	}elseif($action == "back"){

		echo $loginConfig->back($data, $_GET);

	//APP登录
	}elseif($action == "appback"){

		echo $loginConfig->appback($data, $_GET);

	}

}else{
	die("接口不存在！");
}
