<?php
/**
 * 结算设置
 *
 * @version        $Id: settlement.php 2015-8-4 下午15:09:11 $
 * @package        HuoNiao.Member
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("settlement");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/member";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "settlement.html";
$dir       = "../../templates/member"; //当前目录

if(!empty($_POST)){
	if($token == "") die('token传递失败！');

	$cfg_rewardFee = (float)$rewardFee;
	$cfg_tuanFee = (float)$tuanFee;
	$cfg_travelFee = (float)$travelFee;
	$cfg_shopFee = (float)$shopFee;
	$cfg_huodongFee = (float)$huodongFee;
	$cfg_liveFee = (float)$liveFee;
	$cfg_homemakingFee  = (float)$homemakingFee;
	$cfg_educationFee  = (float)$educationFee;

	$cfg_withdrawPlatform = $withdrawPlatform ? serialize($withdrawPlatform) : array();

	adminLog("修改结算设置");


	$configFile = "<"."?php\r\n";
	$configFile .= "\$cfg_fabuAmount = '".$cfg_fabuAmount."';\r\n";
	$configFile .= "\$cfg_fabuFreeCount = '".$cfg_fabuFreeCount."';\r\n";
	$configFile .= "\$cfg_rewardFee = ".$cfg_rewardFee.";\r\n";
	$configFile .= "\$cfg_tuanFee = ".$cfg_tuanFee.";\r\n";
	$configFile .= "\$cfg_travelFee = ".$cfg_travelFee.";\r\n";
	$configFile .= "\$cfg_shopFee = ".$cfg_shopFee.";\r\n";
	$configFile .= "\$cfg_huodongFee = ".$cfg_huodongFee.";\r\n";
	$configFile .= "\$cfg_liveFee = ".$cfg_liveFee.";\r\n";
	$configFile .= "\$cfg_homemakingFee = ".$cfg_homemakingFee.";\r\n";
	$configFile .= "\$cfg_educationFee = ".$cfg_educationFee.";\r\n";
	$configFile .= "\$cfg_minWithdraw = '".$minWithdraw."';\r\n";
	$configFile .= "\$cfg_maxWithdraw = '".$maxWithdraw."';\r\n";
	$configFile .= "\$cfg_withdrawFee = '".$withdrawFee."';\r\n";
	$configFile .= "\$cfg_maxCountWithdraw = '".$maxCountWithdraw."';\r\n";
	$configFile .= "\$cfg_maxAmountWithdraw = '".$maxAmountWithdraw."';\r\n";
	$configFile .= "\$cfg_withdrawCycle = '".$withdrawCycle."';\r\n";
	$configFile .= "\$cfg_withdrawCycleWeek = '".$withdrawCycleWeek."';\r\n";
	$configFile .= "\$cfg_withdrawCycleDay = '".$withdrawCycleDay."';\r\n";
	$configFile .= "\$cfg_withdrawPlatform = '".$cfg_withdrawPlatform."';\r\n";
	$configFile .= "\$cfg_withdrawCheckType = '".$withdrawCheckType."';\r\n";
	$configFile .= "\$cfg_withdrawNote = '".$withdrawNote."';\r\n";
	$configFile .= "?".">";

	$configIncFile = HUONIAOINC.'/config/settlement.inc.php';
	$fp = fopen($configIncFile, "w") or die('{"state": 200, "info": '.json_encode("写入文件 $configIncFile 失败，请检查权限！").'}');
	fwrite($fp, $configFile);
	fclose($fp);

	die('{"state": 100, "info": '.json_encode("配置成功！").'}');
	exit;

}

//配置参数
require_once(HUONIAOINC.'/config/settlement.inc.php');

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'admin/member/settlement.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('rewardFee', (float)$cfg_rewardFee);
	$huoniaoTag->assign('tuanFee', (float)$cfg_tuanFee);
	$huoniaoTag->assign('travelFee', (float)$cfg_travelFee);
	$huoniaoTag->assign('shopFee', (float)$cfg_shopFee);
	$huoniaoTag->assign('huodongFee', (float)$cfg_huodongFee);
	$huoniaoTag->assign('liveFee', (float)$cfg_liveFee);
	$huoniaoTag->assign('homemakingFee', (float)$cfg_homemakingFee);
	$huoniaoTag->assign('educationFee', (float)$cfg_educationFee);

	$huoniaoTag->assign('minWithdraw', $cfg_minWithdraw ? $cfg_minWithdraw : 1);
	$huoniaoTag->assign('maxWithdraw', (int)$cfg_maxWithdraw);
	$huoniaoTag->assign('withdrawFee', (int)$cfg_withdrawFee);
	$huoniaoTag->assign('maxCountWithdraw', (int)$cfg_maxCountWithdraw);
	$huoniaoTag->assign('maxAmountWithdraw', (int)$cfg_maxAmountWithdraw);
	$huoniaoTag->assign('withdrawCycle', (int)$cfg_withdrawCycle);
	$huoniaoTag->assign('withdrawCycleWeek', (int)$cfg_withdrawCycleWeek);
	$huoniaoTag->assign('withdrawCycleDay', $cfg_withdrawCycleDay ? $cfg_withdrawCycleDay : 28);
	$huoniaoTag->assign('withdrawPlatform', $cfg_withdrawPlatform ? unserialize($cfg_withdrawPlatform) : array('weixin', 'alipay', 'bank'));
	$huoniaoTag->assign('withdrawCheckType', (int)$cfg_withdrawCheckType);
	$huoniaoTag->assign('withdrawNote', $cfg_withdrawNote);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/settlement";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
