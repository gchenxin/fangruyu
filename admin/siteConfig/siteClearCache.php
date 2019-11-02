<?php
/**
 * 清除页面缓存
 *
 * @version        $Id: siteClearCache.php 2014-3-19 上午10:23:13 $
 * @package        HuoNiao.Config
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("siteClearCache");
$dsql = new dsql($dbo);
$tpl  = dirname(__FILE__)."/../templates/siteConfig";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "siteClearCache.html";

if($action == "do"){

    if(empty($module)){
        $module = array();
    }
    //模板缓存
	if(count($module) > 0){
		foreach($module as $m){
			$admin = $type ? 1 : 0;
			clear_all_files($m, $admin);
		}
	}

	//生成新的静态资源版本号为当前时间
    if($static) {
        $m_file = HUONIAODATA . "/admin/staticVersion.txt";
        $fp = fopen($m_file, "w");
        fwrite($fp, time());
        fclose($fp);

        $module[] = 'static';
    }


    //内存缓存
    if($memory == 'redis' && $HN_memory->enable){
        $HN_memory->clear();
        $module[] = 'redis';
    }

	adminLog("清除页面缓存", join(",", $module));
	ShowMsg("页面缓存已经清除成功。", "siteClearCache.php");
	die;
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'admin/siteConfig/siteClearCache.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('action', $action);

	if($HN_memory->enable){
        $huoniaoTag->assign('redis', 1);
    }

	$huoniaoTag->assign('moduleList', getModuleList());
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/siteConfig";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
