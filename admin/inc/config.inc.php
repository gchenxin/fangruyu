<?php
/**
 * 后台管理配置文件
 *
 * @version        $Id: config.inc.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.Administrator
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
require_once(HUONIAOADMIN.'/../include/common.inc.php');
require_once(HUONIAOINC.'/class/userLogin.class.php');
$huoniaoTag->caching         = FALSE;                             //是否使用缓存，后台不需要开启
$huoniaoTag->compile_dir     = HUONIAOROOT."/templates_c/admin";  //设置编译目录
$huoniaoTag->template_dir = dirname(__FILE__)."/templates";       //设置后台模板目录
$userLogin = new userLogin($dbo);

//获取当前地址
$Nowurl = $s_scriptName = '';
$path = array();

$Nowurl = GetCurUrl();
$Nowurls = explode('/', $Nowurl);
for($i = 2; $i < count($Nowurls); $i++){
	array_push($path, $Nowurls[$i]);
}

$s_scriptName = join("/", $path);

//数据库还原操作不进行登录验证
if($action != 'dorevert'){

	//检验用户登录状态
	if($userLogin->getUserID()==-1 && $action != 'filecheck' && $action != 'sync' && $action != 'syncDatabase'){
	    header("location:".HUONIAOADMIN."/login.php?gotopage=".urlencode($s_scriptName));
	    exit();
	}

	$userLogin->keepUser();

	$huoniaoTag->assign("adminPath", HUONIAOADMIN."/");
	//css
	$huoniaoTag->assign('cssFile', includeFile('css'));

	//管理员类型
	$userType = $userLogin->getUserType();
	$huoniaoTag->assign('userType', $userType);

	//可操作的管理员、城市，多个以,分隔
	$adminIds = $userLogin->getAdminIds();
	$adminIds = empty($adminIds) ? 0 : $adminIds;
	$adminCityIds = $userLogin->getAdminCityIds();
	$adminCityIds = empty($adminCityIds) ? 0 : $adminCityIds;

	//当前登录管理员可以操作的管理员，array
	$adminListArr = $userLogin->getAdminList();
	$adminListArr = empty($adminListArr) ? array() : $adminListArr;

	//当前登录管理员可以操作的城市，array
	$adminCityArr = $userLogin->getAdminCity();
	$adminCityArr = empty($adminCityArr) ? array() : $adminCityArr;

}


$cfg_basehost_ = $cfg_basehost;
if(substr($cfg_basehost, 0, 4) == 'www.') {
    $cfg_basehost_ = substr($cfg_basehost, 4);
}

$huoniaoTag->assign('cfg_basehost_', $cfg_basehost_);
$huoniaoTag->assign('cfg_cookiePre', $cfg_cookiePre);
