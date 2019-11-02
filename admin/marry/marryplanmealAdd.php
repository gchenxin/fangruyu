<?php
/**
 * 添加婚嫁套餐
 *
 * @version        $Id: marryplanmealAdd.php 2019-03-14 上午10:21:14 $
 * @package        HuoNiao.marry
 * @copyright      Copyright (c) 2013 - 2019, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/marry";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "marryplanmealAdd.html";

$typeid = $typeid ? $typeid : 0;

if($typeid == 0){
	$filter = 2;
}elseif($typeid == 1){
	$filter = 4;
}elseif($typeid == 2){
	$filter = 5;
}elseif($typeid == 3){
	$filter = 7;
}elseif($typeid == 4){
	$filter = 8;
}elseif($typeid == 5){
	$filter = 9;
}elseif($typeid == 6){
	$filter = 10;
}

$tab = "marry_planmeal";
$dopost = $dopost ? $dopost : "save";        //操作类型 save添加 edit修改
if($dopost == "edit"){
	if($typeid == 0){
		$pagetitle = "修改婚嫁套餐";
	}elseif($typeid == 1){
		$pagetitle = "修改婚纱摄影套餐";
	}elseif($typeid == 2){
		$pagetitle = "修改摄影跟拍套餐";
	}elseif($typeid == 3){
		$pagetitle = "修改珠宝首饰套餐";
	}elseif($typeid == 4){
		$pagetitle = "修改摄像跟拍套餐";
	}elseif($typeid == 5){
		$pagetitle = "修改新娘跟妆套餐";
	}elseif($typeid == 6){
		$pagetitle = "修改婚纱礼服套餐";
	}
	
	checkPurview("marryplanmealEdit" . $typeid);
}else{
	if($typeid == 0){
		$pagetitle = "添加婚嫁套餐";
	}elseif($typeid == 1){
		$pagetitle = "添加婚纱摄影套餐";
	}elseif($typeid == 2){
		$pagetitle = "添加摄影跟拍套餐";
	}elseif($typeid == 3){
		$pagetitle = "添加珠宝首饰套餐";
	}elseif($typeid == 4){
		$pagetitle = "添加摄像跟拍套餐";
	}elseif($typeid == 5){
		$pagetitle = "添加新娘跟妆套餐";
	}elseif($typeid == 6){
		$pagetitle = "添加婚纱礼服套餐";
	}
	checkPurview("marryplanmealAdd" . $typeid);
}
if(empty($comid)) $comid = 0;
if(empty($userid)) $userid = 0;
if(empty($weight)) $weight = 1;
if(empty($state)) $state = 0;
if(empty($click)) $click = mt_rand(50, 200);
$joindate = GetMkTime(time());
$pubdate  = GetMkTime(time());
if(!empty($tag)) $tag = join('|', $tag);

if($_POST['submit'] == "提交"){

	if($token == "") die('token传递失败！');

	if($comid == 0 && trim($comid) == ''){
		echo '{"state": 200, "info": "请选择婚嫁公司"}';
		exit();
	}
	if($comid == 0){
		$comSql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `title` = '".$zjcom."'");
		$comResult = $dsql->dsqlOper($comSql, "results");
		if(!$comResult){
			echo '{"state": 200, "info": "婚嫁公司不存在，请在联想列表中选择"}';
			exit();
		}
		$comid = $comResult[0]['id'];
	}else{
		$comSql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `id` = ".$comid);
		$comResult = $dsql->dsqlOper($comSql, "results");
		if(!$comResult){
			echo '{"state": 200, "info": "婚嫁公司不存在，请在联想列表中选择"}';
			exit();
		}
	}

	//检测是否已经注册
	if($dopost == "save"){

		/* $userSql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_hotelfield` WHERE `title` = '".$title."'");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			echo '{"state": 200, "info": "此会员已经加入其它婚嫁公司，不可以重复添加！"}';
			exit();
		} */

	}else{

		/* $userSql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_hotelfield` WHERE `title` = '".$title."' AND `id` != ". $id);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			echo '{"state": 200, "info": "此会员已经加入其它婚嫁公司，不可以重复添加！"}';
			exit();
		} */

	}

}

if($dopost == "save" && $submit == "提交"){
	//保存到表
	$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`title`, `company`, `pics`, `tag`, `type`, `price`, `click`, `pubdate`, `weight`, `state`) VALUES ('$title', '$comid', '$pics', '$tag', '$typeid', '$price', '$click', '$pubdate', '$weight', '$state')");
	$aid = $dsql->dsqlOper($archives, "lastid");

	if($aid){
		adminLog($pagetitle, $userid);
		if($state == 1){
			updateCache("marry_planmeal_list", 300);
			clearCache("marry_planmeal_total", 'key');
		}
		$param = array(
			"service"  => "marry",
			"template" => "planmeal-detail",
			"id"       => $aid
		);
		$url = getUrlPath($param);

		echo '{"state": 100, "url": "'.$url.'"}';
	}else{
		echo '{"state": 200, "info": '.json_encode("保存到数据库失败！").'}';
	}
	die;
}elseif($dopost == "edit"){

	if($submit == "提交"){
		//保存到表
		$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `title` = '$title', `company` = '$comid', `pics` = '$pics', `price` = '$price', `tag` = '$tag', `type` = '$typeid', `click` = '$click', `weight` = '$weight', `state` = '$state' WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "update");

		if($results == "ok"){
			adminLog($pagetitle, $id);

			checkCache("marry_planmeal_list", $id);
			clearCache("marry_planmeal_detail", $id);
			clearCache("marry_planmeal_total", 'key');

			$param = array(
				"service"  => "marry",
				"template" => "planmeal-detail",
				"id"       => $id
			);
			$url = getUrlPath($param);

			echo '{"state": 100, "info": '.json_encode('修改成功！').', "url": "'.$url.'"}';
		}else{
			echo '{"state": 200, "info": '.json_encode('修改失败！').'}';
		}
		die;
	}

	if(!empty($id)){

		//主表信息
		$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");

		if(!empty($results)){

			foreach ($results[0] as $key => $value) {
				${$key} = $value;
			}

		}else{
			ShowMsg('要修改的信息不存在或已删除！', "-1");
			die;
		}

	}else{
		ShowMsg('要修改的信息参数传递失败，请联系管理员！', "-1");
		die;
	}

}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//css
    $cssFile = array(
        'ui/jquery.chosen.css',
        'admin/chosen.min.css'
    );
    $huoniaoTag->assign('cssFile', includeFile('css', $cssFile));

	//js
	$jsFile = array(
		'ui/jquery.dragsort-0.5.1.min.js',
		'ui/bootstrap-datetimepicker.min.js',
		'ui/chosen.jquery.min.js',
		'publicUpload.js',
		'publicAddr.js',
		'admin/marry/marryplanmealAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('dopost', $dopost);
	require_once(HUONIAOINC."/config/marry.inc.php");
	global $customUpload;
	if($customUpload == 1){
		global $custom_thumbSize;
		global $custom_thumbType;
		$huoniaoTag->assign('thumbSize', $custom_thumbSize);
		$huoniaoTag->assign('thumbType', "*.".str_replace("|", ";*.", $custom_thumbType));
	}
	$huoniaoTag->assign('atlasMax', $custom_marryplanmeal_atlasMax ? $custom_marryplanmeal_atlasMax : 9);

	if($id != ""){
		$huoniaoTag->assign('id', $id);

		$huoniaoTag->assign('comid', $company);
		$comSql = $dsql->SetQuery("SELECT `title` FROM `#@__marry_store` WHERE `id` = ". $company);
		$comname = $dsql->getTypeName($comSql);
		$huoniaoTag->assign('zjcom', $comname[0]['title']);
		
	}
	$huoniaoTag->assign('title', $title);
	$huoniaoTag->assign('typeid', $typeid);
	$huoniaoTag->assign('weight', $weight == "" || $weight == 0 ? "1" : $weight);
	$huoniaoTag->assign('click', $click);
	$huoniaoTag->assign('price', $price);
	$huoniaoTag->assign('pics', $pics ? json_encode(explode(",", $pics)) : "[]");

	$huoniaoTag->assign('tag', $tag);
	$huoniaoTag->assign('tagSel', $tag ? explode("|", $tag) : array());
	//特色标签
	$tagArr = $custommarryTag ? explode("|", $custommarryTag) : array();
	$huoniaoTag->assign('tagArr', $tagArr);

	//显示状态
	$huoniaoTag->assign('stateopt', array('0', '1', '2'));
	$huoniaoTag->assign('statenames',array('待审核','已审核','审核拒绝'));
	$huoniaoTag->assign('state', $state == "" ? 1 : $state);

	$huoniaoTag->assign('filter', $filter);

	$huoniaoTag->assign('cityList', json_encode($adminCityArr));

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/marry";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
