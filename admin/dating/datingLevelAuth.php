<?php
/**
 * 会员等级特权设置
 *
 * @version        $Id: datingLevelAuth.php 2017-07-24 下午15:56:28 $
 * @package        HuoNiao.dating
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("datingLevelAuth");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

//表名
$tab = "dating_level";

//模板名
$templates = "datingLevelAuth.html";

//js
$jsFile = array(
	'admin/dating/datingLevelAuth.js'
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

				foreach ($json[$i] as $key => $value) {
					if(!$dataArr[$value['id']]){
						$dataArr[$value['id']] = array();
					}
					$count = $value['count'];
					if($value['module'] != 'key' && $key > 0) $count = 0;
					$dataArr[$value['id']][$value['module']] = $value['id'] ? (int)$count : sprintf("%.2f", $count);
				}

		}

		if($dataArr){

			foreach ($dataArr as $key => $value) {
				$privilege = serialize($value);
				$sql = $dsql->SetQuery("UPDATE `#@__dating_level` SET `privilege` = '$privilege' WHERE `id` = $key");
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

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
