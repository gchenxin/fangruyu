<?php
/**
 * 会员等级费用设置
 *
 * @version        $Id: memberLevelCost.php 2017-07-24 上午11:05:13 $
 * @package        HuoNiao.Member
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("memberLevelCost");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

//表名
$tab = "dating_level";

//模板名
$templates = "datingLevelCost.html";

//js
$jsFile = array(
	'admin/dating/datingLevelCost.js'
);
$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));


//保存
if(!empty($_POST)){
	$data = str_replace("\\", '', $_POST['data']);
	if($data != ""){
		$json = json_decode($data);
		$json = objtoarr($json);

		$dataArr = array();
		for($i = 0; $i < count($json); $i++){
			$day = (int)$json[$i]["day"];
			$daytype = $json[$i]["daytype"];

			if(is_array($json[$i]["price"])){
				foreach ($json[$i]["price"] as $key => $value) {
					if(!$dataArr[$value['id']]){
						$dataArr[$value['id']] = array();
					}
					array_push($dataArr[$value['id']], array(
						"day" => $day,
						"daytype" => $daytype,
						"price" => sprintf("%.2f", $value['price'])
					));
				}
			}
		}

		if($dataArr){
			foreach ($dataArr as $key => $value) {
				$cost = serialize($value);
				$sql = $dsql->SetQuery("UPDATE `#@__dating_level` SET `cost` = '$cost' WHERE `id` = $key");
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

		$sql = $dsql->SetQuery("SELECT `id`, `name`, `cost` FROM `#@__".$tab."`");
		$results = $dsql->dsqlOper($sql, "results");
		$levelList = array();
		if($results){
			foreach ($results as $key => $value) {
				$costArr = empty($value['cost']) ? array() : unserialize($value['cost']);
				$levelList[$key]['id']   = $value['id'];
				$levelList[$key]['name'] = $value['name'];
				$levelList[$key]['cost'] = $costArr;
			}
		}
		$huoniaoTag->assign('levelList', $levelList);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
