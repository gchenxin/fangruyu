<?php
/**
 * 积分设置
 *
 * @version        $Id: pointsConfig.php 2015-8-4 下午15:09:11 $
 * @package        HuoNiao.Member
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("pointsConfig");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/member";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "pointsConfig.html";
$dir       = "../../templates/member"; //当前目录

if(!empty($_POST)){
	if($token == "") die('token传递失败！');

	$cfg_pointState = (int)$pointState;
	$cfg_pointName  = $pointName;
	$cfg_pointRatio = (float)$pointRatio;
	$cfg_pointFee   = (float)$pointFee;
	$cfg_pointRegGiving   = (float)$pointRegGiving;
	$cfg_pointRegGivingRec   = (int)$pointRegGivingRec;
	$cfg_pointPension   = (int)$pointPension;

	$cfg_returnPointState = (int)$returnPointState;
	$cfg_returnPoint_tuan = (int)$returnPoint_tuan;
	$cfg_returnPoint_shop = (int)$returnPoint_shop;
	$cfg_returnPoint_info = (int)$returnPoint_info;
	$cfg_returnPoint_waimai = (int)$returnPoint_waimai;
	$cfg_returnPoint_homemaking = (int)$returnPoint_homemaking;
	$cfg_returnPoint_education = (int)$returnPoint_education;
	$cfg_returnPoint_travel = (int)$returnPoint_travel;

	adminLog("修改积分设置");


	//站点信息文件内容
	$configFile = "<"."?php\r\n";
	$configFile .= "\$cfg_pointState = '".$cfg_pointState."';\r\n";
	$configFile .= "\$cfg_pointName = '".$cfg_pointName."';\r\n";
	$configFile .= "\$cfg_pointRatio = ".$cfg_pointRatio.";\r\n";
	$configFile .= "\$cfg_pointFee = ".$cfg_pointFee.";\r\n";
	$configFile .= "\$cfg_pointRegGiving = ".$cfg_pointRegGiving.";\r\n";
	$configFile .= "\$cfg_pointRegGivingRec = ".$cfg_pointRegGivingRec.";\r\n";
	$configFile .= "\$cfg_pointPension = ".$cfg_pointPension.";\r\n";
	$configFile .= "\$cfg_returnPointState = ".$cfg_returnPointState.";\r\n";
	$configFile .= "\$cfg_returnPoint_tuan = ".$cfg_returnPoint_tuan.";\r\n";
	$configFile .= "\$cfg_returnPoint_shop = ".$cfg_returnPoint_shop.";\r\n";
	$configFile .= "\$cfg_returnPoint_info = ".$cfg_returnPoint_info.";\r\n";
	$configFile .= "\$cfg_returnPoint_waimai = ".$cfg_returnPoint_waimai.";\r\n";
	$configFile .= "\$cfg_returnPoint_homemaking = ".$cfg_returnPoint_homemaking.";\r\n";
	$configFile .= "\$cfg_returnPoint_travel = ".$cfg_returnPoint_travel.";\r\n";
	$configFile .= "\$cfg_returnPoint_education = ".$cfg_returnPoint_education.";\r\n";
	$configFile .= "?".">";

	$configIncFile = HUONIAOINC.'/config/pointsConfig.inc.php';
	$fp = fopen($configIncFile, "w") or die('{"state": 200, "info": '.json_encode("写入文件 $configIncFile 失败，请检查权限！").'}');
	fwrite($fp, $configFile);
	fclose($fp);

	die('{"state": 100, "info": '.json_encode("配置成功！").'}');
	exit;

}

//配置参数
require_once(HUONIAOINC.'/config/pointsConfig.inc.php');

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'admin/member/pointsConfig.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	//签到状态
	$huoniaoTag->assign('pointState', array('0', '1'));
	$huoniaoTag->assign('pointStateNames',array('关闭','开启'));
	$huoniaoTag->assign('pointStateChecked', $cfg_pointState);

	$huoniaoTag->assign('pointName', $cfg_pointName);
	$huoniaoTag->assign('pointRatio', $cfg_pointRatio);
	$huoniaoTag->assign('pointFee', $cfg_pointFee);
	$huoniaoTag->assign('pointRegGiving', $cfg_pointRegGiving);
	$huoniaoTag->assign('pointRegGivingRec', (int)$cfg_pointRegGivingRec);
	$huoniaoTag->assign('pointPension', (int)$cfg_pointPension);


	//积分返现
	$huoniaoTag->assign('returnPointState', array('0', '1'));
	$huoniaoTag->assign('returnPointStateNames',array('关闭','开启'));
	$huoniaoTag->assign('returnPointStateChecked', (int)$cfg_returnPointState);

	$huoniaoTag->assign('returnPoint_tuan', $cfg_returnPoint_tuan);
	$huoniaoTag->assign('returnPoint_shop', $cfg_returnPoint_shop);
	$huoniaoTag->assign('returnPoint_info', $cfg_returnPoint_info);
	$huoniaoTag->assign('returnPoint_waimai', $cfg_returnPoint_waimai);
	$huoniaoTag->assign('returnPoint_homemaking', $cfg_returnPoint_homemaking);
	$huoniaoTag->assign('returnPoint_travel', $cfg_returnPoint_travel);
	$huoniaoTag->assign('returnPoint_education', $cfg_returnPoint_education);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/pointsConfig";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
