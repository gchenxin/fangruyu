<?php
/**
 * 交友会员等级列表
 *
 * @version        $Id: datingLevelList.php 2017-07-21 下午19:28:16 $
 * @package        HuoNiao.dating
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("datingLevelList");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

//表名
$tab = "dating_level";

//模板名
$templates = "datingLevelList.html";

//js
$jsFile = array(
	'ui/jquery.ajaxFileUpload.js',
	'admin/dating/datingLevelList.js'
);
$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));


$sql = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `id` = 1");
$ret = $dsql->dsqlOper($sql, "results");
if(!$ret){
	$privilege = serialize(array());
	$sql = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`name`, `cost`, `month`, `privilege`, `icon`, `tag`, `def`) VALUES ('普通用户', '0', '0', '$privilege', '', '', '1')");
	$dsql->dsqlOper($sql, "lastid");
}

//删除等级
if($dopost == "del"){
	if($token == "") die('token传递失败！');

	if($id != ""){
		$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `def` = 0 AND `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");

		if(!empty($results)){

			$name = $results[0]['name'];
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				echo '{"state": 200, "info": '.json_encode('删除失败，请重试！').'}';
				die;
			}

			//将删除的等级下的会员等级归0
			$sql = $dsql->SetQuery("UPDATE `#@__dating_dating` SET `level` = 0, `expired` = 0 WHERE `level` = $id");
			$dsql->dsqlOper($sql, "update");

			adminLog("删除交友会员等级", $name);
			echo '{"state": 100, "info": '.json_encode('删除成功！').'}';
			die;
		}else{
			echo '{"state": 200, "info": '.json_encode('要删除的交友会员等级不存在或已删除！').'}';
			die;
		}
	}
	die;

//修改名称
}else if($dopost == "updateLevel"){
	if($token == "") die('token传递失败！');

	if($id != ""){
		$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");

		if(!empty($results)){

			if($name == "") die('{"state": 101, "info": '.json_encode('参数错误').'}');
			if($results[0][$name] != $val){

				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `".$name."` = '$val' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");

			}else{
				//分类没有变化
				echo '{"state": 101, "info": '.json_encode('无变化！').'}';
				die;
			}

			if($results != "ok"){
				echo '{"state": 101, "info": '.json_encode('修改失败，请重试！').'}';
				exit();
			}else{
				adminLog("修改交友会员等级", $name);
				echo '{"state": 100, "info": '.json_encode('修改成功！').'}';
				exit();
			}

		}else{
			echo '{"state": 101, "info": '.json_encode('要修改的信息不存在或已删除！').'}';
			die;
		}
	}
	die;

//新增管理组
}else if($dopost == "update"){
	if($token == "") die('token传递失败！');
	$data = str_replace("\\", '', $_POST['data']);
	if($data != ""){
		$json = json_decode($data);
		$json = objtoarr($json);

		for($i = 0; $i < count($json); $i++){
			$id = $json[$i]["id"];
			$name  = $json[$i]["name"];
			$cost  = (float)$json[$i]["cost"];
			$month = (int)$json[$i]["month"];
			$icon  = $json[$i]["icon"];
			$tag   = $json[$i]["tag"];

			//如果ID为空则向数据库插入下级分类
			if($id == "" || $id == 0){
				$arr = serialize(array());
				$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`name`, `cost`, `month`, `privilege`, `icon`, `tag`, `def`) VALUES ('$name', '$cost', '$month', '$arr', '$icon', '$tag', '0')");
				$dsql->dsqlOper($archives, "update");
				adminLog("添加会员等级", $name);
			}
			//其它为数据库已存在的需要验证名称是否有改动，如果有改动则UPDATE
			else{
				$archives = $dsql->SetQuery("SELECT `name`, `icon` FROM `#@__".$tab."` WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "results");
				if(!empty($results)){
					//验证分类名
					if($results[0]["name"] != $name || $results[0]["cost"] != $cost || $results[0]["month"] != $month || $results[0]["icon"] != $icon || $results[0]["tag"] != $tag){
						$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `name` = '$name', `cost` = '$cost', `month` = '$month', `icon` = '$icon', `tag` = '$tag' WHERE `id` = ".$id);
						$results = $dsql->dsqlOper($archives, "update");
					}
					adminLog("修改会员等级", $name);
				}
			}
		}
		echo '{"state": 100, "info": "保存成功！"}';
	}
	die;
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	$sql = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `def` = 0");
	$results = $dsql->dsqlOper($sql, "results");
	$levelList = array();
	if($results){
		foreach ($results as $key => $value) {
			if($value['icon'] != ''){
				$value['iconturl'] = getFilePath($value['icon']);
			}else{
				$value['iconturl'] = '';
			}
			$levelList[$key] = $value;
		}
		
	}
	$huoniaoTag->assign('levelList', $levelList);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
