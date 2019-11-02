<?php
/**
 * 会员等级特权设置
 *
 * @version        $Id: memberLevelAuth.php 2017-07-24 下午15:56:28 $
 * @package        HuoNiao.Member
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("memberLevelAuth");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/member";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

//表名
$tab = "member_level";

//模板名
$templates = "memberLevelAuth.html";

//js
$jsFile = array(
	'admin/member/memberLevelAuth.js'
);
$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));


//保存
if(!empty($_POST)){
	$data = str_replace("\\", '', $_POST['data']);
	if($data != ""){
		$json = json_decode($data);
		$json = objtoarr($json);

		$dataArr = $dataArr_ = array();
		for($i = 0; $i < count($json); $i++){

				foreach ($json[$i] as $key => $value) {
					if(!$dataArr[$value['id']]){
						$dataArr[$value['id']] = array();
					}
					if(!$dataArr_[$value['id']]){
						$dataArr_[$value['id']] = array();
					}
					$dataArr[$value['id']][$value['module']] = $value['id'] && $value['module'] != 'livetime' ? (int)$value['amount'] : sprintf("%.2f", $value['amount']);
					$dataArr_[$value['id']][$value['module']] = (int)$value['count'];
				}

		}

		if($dataArr){

			$configFile = "<"."?php\r\n";
			$configFile .= "\$cfg_fabuAmount = '".serialize($dataArr[0])."';\r\n";
			$configFile .= "\$cfg_fabuFreeCount = '".serialize($dataArr_[0])."';\r\n";
			$configFile .= "\$cfg_rewardFee = ".(float)$cfg_rewardFee.";\r\n";
			$configFile .= "\$cfg_tuanFee = ".(float)$cfg_tuanFee.";\r\n";
			$configFile .= "\$cfg_travelFee = ".(float)$cfg_travelFee.";\r\n";
			$configFile .= "\$cfg_shopFee = ".(float)$cfg_shopFee.";\r\n";
			$configFile .= "\$cfg_huodongFee = ".(float)$cfg_huodongFee.";\r\n";
			$configFile .= "\$cfg_liveFee = ".(float)$cfg_liveFee.";\r\n";
			$configFile .= "\$cfg_homemakingFee = ".(float)$cfg_homemakingFee.";\r\n";
			$configFile .= "\$cfg_educationFee = ".(float)$cfg_educationFee.";\r\n";
			$configFile .= "\$cfg_minWithdraw = '".$cfg_minWithdraw."';\r\n";
			$configFile .= "\$cfg_maxWithdraw = '".$cfg_maxWithdraw."';\r\n";
			$configFile .= "\$cfg_withdrawFee = '".$cfg_withdrawFee."';\r\n";
			$configFile .= "\$cfg_maxCountWithdraw = '".$cfg_maxCountWithdraw."';\r\n";
			$configFile .= "\$cfg_maxAmountWithdraw = '".$cfg_maxAmountWithdraw."';\r\n";
			$configFile .= "\$cfg_withdrawCycle = '".$cfg_withdrawCycle."';\r\n";
			$configFile .= "\$cfg_withdrawCycleWeek = '".$cfg_withdrawCycleWeek."';\r\n";
			$configFile .= "\$cfg_withdrawCycleDay = '".$cfg_withdrawCycleDay."';\r\n";
			$configFile .= "\$cfg_withdrawPlatform = '".$cfg_withdrawPlatform."';\r\n";
			$configFile .= "\$cfg_withdrawCheckType = '".$cfg_withdrawCheckType."';\r\n";
			$configFile .= "\$cfg_withdrawNote = '".$cfg_withdrawNote."';\r\n";
			$configFile .= "?".">";

			$configIncFile = HUONIAOINC.'/config/settlement.inc.php';
			$fp = fopen($configIncFile, "w") or die('{"state": 200, "info": '.json_encode("写入文件 $configIncFile 失败，请检查权限！").'}');
			fwrite($fp, $configFile);
			fclose($fp);

			foreach ($dataArr as $key => $value) {
				$privilege = serialize($value);
				$sql = $dsql->SetQuery("UPDATE `#@__member_level` SET `privilege` = '$privilege' WHERE `id` = $key");
				$dsql->dsqlOper($sql, "update");
			}
			echo '{"state": 100, "info": "保存成功！"}';
		}else{
			echo '{"state": 200, "info": "表单为空，保存失败！"}';
		}
	}
	die;
}


//验证模板文件
if(file_exists($tpl."/".$templates)){

		$sql = $dsql->SetQuery("SELECT `id`, `name`, `privilege` FROM `#@__".$tab."`");
		$results = $dsql->dsqlOper($sql, "results");
		$levelList = array();
		if($results){
			foreach ($results as $key => $value) {
				$privilegeArr = empty($value['privilege']) ? array() : unserialize($value['privilege']);
				$levelList[$key]['id']   = $value['id'];
				$levelList[$key]['name'] = $value['name'];
				$levelList[$key]['privilege'] = $privilegeArr;
			}
		}
		$huoniaoTag->assign('levelList', $levelList);

		//配置参数
		require_once(HUONIAOINC.'/config/settlement.inc.php');
		$fabuAmount = $cfg_fabuAmount ? unserialize($cfg_fabuAmount) : array();
		$huoniaoTag->assign('fabuAmount', $fabuAmount);
		$fabuFreeCount = $cfg_fabuFreeCount ? unserialize($cfg_fabuFreeCount) : array();
		$huoniaoTag->assign('fabuFreeCount', $fabuFreeCount);

		$sql = $dsql->SetQuery("SELECT `title`, `subject`, `name` FROM `#@__site_module` WHERE ( `state` = 0 or `state` = -1) AND `type` = 0 ORDER BY `weight`, `id`");
		$result = $dsql->dsqlOper($sql, "results");
		if($result){
			foreach ($result as $key => $value) {
				if(!empty($value['name'])){
					$moduleArr[] = array(
						"name" => $value['name'],
						"title" => $value['subject'] ? $value['subject'] : $value['subject']
					);
				}
			}
		}
		$huoniaoTag->assign('moduleArr', $moduleArr);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/member";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
