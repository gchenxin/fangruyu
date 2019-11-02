<?php
/**
 * 分销设置
 *
 * @version        $Id: fenxiaoConfig.php 2015-8-4 下午15:09:11 $
 * @package        HuoNiao.Member
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("fenxiaoConfig");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/member";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "fenxiaoConfig.html";
$dir       = "../../templates/member"; //当前目录

if(!empty($_POST)){
	if($token == "") die('token传递失败！');

	if(empty($fenxiaoName)){
		die('{"state": 200, "info": '.json_encode("请填写属性名称").'}');
	}
	$cfg_fenxiaoName = $fenxiaoName;
	$cfg_fenxiaoState = (int)$fenxiaoState;
	$cfg_fenxiaoAmount = (int)$fenxiaoAmount;
	$fenxiaoLevel = $fenxiaoLevel ? $fenxiaoLevel : array();
	$cfg_fenxiaoNote = $fenxiaoNote;
	$cfg_fenxiaoLevel = array();
	if($fenxiaoLevel){
		foreach ($fenxiaoLevel['name'] as $key => $value) {
			$cfg_fenxiaoLevel[] = array(
				"name" => $value,
				"fee" => $fenxiaoLevel['fee'][$key]
			);	
		}
	}
	$config = $_POST['config'];
	if($config){
		foreach ($config as $key => $value) {
			$sql = "";
			switch($value){
				case 'shop':
					$sql = "UPDATE `#@__shop_product` SET `fx_reward` = '0'";
					break;
				case 'tuan':
					$sql = "UPDATE `#@__tuanlist` SET `fx_reward` = '0'";
					break;
				case 'info':
					$sql = "UPDATE `#@__infolist` SET `fx_reward` = '0'";
					break;
				case 'waimai':
					$sql = "UPDATE `#@__waimai_list` SET `fx_reward` = '0'";
					break;
			}
			if($sql){
				$sql = $dsql->SetQuery($sql);
				$dsql->dsqlOper($sql, "update");
			}

		}
	}

	adminLog("修改分销设置");

	//站点信息文件内容
	$configFile = "<"."?php\r\n";
	$configFile .= "\$cfg_fenxiaoName = '".$cfg_fenxiaoName."';\r\n";
	$configFile .= "\$cfg_fenxiaoState = ".$cfg_fenxiaoState.";\r\n";
	$configFile .= "\$cfg_fenxiaoAmount = '".$cfg_fenxiaoAmount."';\r\n";
	// $configFile .= "\$cfg_fenxiaoFee = '".serialize($cfg_fenxiaoFee)."';\r\n";
	$configFile .= "\$cfg_fenxiaoLevel = '".serialize($cfg_fenxiaoLevel)."';\r\n";
	$configFile .= "\$cfg_fenxiaoNote = '".$cfg_fenxiaoNote."';\r\n";
	$configFile .= "?".">";

	$configIncFile = HUONIAOINC.'/config/fenxiaoConfig.inc.php';
	$fp = fopen($configIncFile, "w") or die('{"state": 200, "info": '.json_encode("写入文件 $configIncFile 失败，请检查权限！").'}');
	fwrite($fp, $configFile);
	fclose($fp);

	die('{"state": 100, "info": '.json_encode("配置成功！").'}');
	exit;

}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'admin/member/fenxiaoConfig.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('fenxiaoName', $cfg_fenxiaoName);
	$huoniaoTag->assign('fenxiaoNote', $cfg_fenxiaoNote);
	//状态
	$huoniaoTag->assign('fenxiaoState', array('0', '1'));
	$huoniaoTag->assign('fenxiaoStateNames',array('关闭','开启'));
	$huoniaoTag->assign('fenxiaoStateChecked', (int)$cfg_fenxiaoState);

	$huoniaoTag->assign('fenxiaoAmount', (int)$cfg_fenxiaoAmount);
	$huoniaoTag->assign('fenxiaoLevel', $cfg_fenxiaoLevel ? unserialize($cfg_fenxiaoLevel) : array());

	$configval = array();
	$configlist = array();
	foreach ($installModuleArr as $key => $value) {
		if($value == 'shop' || $value == 'tuan' || $value == 'waimai'){
			$configval[] = $value;
			$configlist[] = $installModuleTitleArr[$value];
		}
	}
	// print_r($configval);die;
	$huoniaoTag->assign('configlist', $configlist);
	$huoniaoTag->assign('configval', $configval);
	$huoniaoTag->assign('config', explode(",", $config));

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/fenxiaoConfig";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
