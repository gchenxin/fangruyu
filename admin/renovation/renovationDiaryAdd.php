<?php
/**
 * 添加施工案例
 *
 * @version        $Id: renovationDiaryAdd.php 2014-3-7 下午14:19:19 $
 * @package        HuoNiao.Renovation
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/renovation";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "renovationDiaryAdd.html";

$tab = "renovation_diary";
$dopost = $dopost ? $dopost : "save";        //操作类型 save添加 edit修改
if($dopost == "edit"){
	$pagetitle = "修改施工案例";
	checkPurview("renovationDiaryEdit");
}else{
	$pagetitle = "添加施工案例";
	checkPurview("renovationDiaryAdd");
}

if(empty($designer)) $designer = 0;
if(empty($communityid)) $communityid = 0;
if(empty($case)) $case = 0;
if(empty($style)) $style = 0;
if(empty($units)) $units = 0;
if(empty($comstyle)) $comstyle = 0;
if(empty($began)) $began = 0;
if(empty($weight)) $weight = 1;
if(empty($state)) $state = 0;
if(empty($click)) $click = 0;

if(!empty($communityid)){
	$community = "";
}

if($_POST['submit'] == "提交"){

	if($token == "") die('token传递失败！');
	//二次验证
	if(empty($title)){
		echo '{"state": 200, "info": "请输入案例名称！"}';
		exit();
	}

	if($designer == 0 && trim($designername) == ''){
		echo '{"state": 200, "info": "请选择设计师"}';
		exit();
	}
	if($designer == 0){
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `name` = '".$designername."'");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			echo '{"state": 200, "info": "设计师不存在，请在联想列表中选择"}';
			exit();
		}
		$designer = $userResult[0]['id'];
	}else{
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `id` = ".$designer);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			echo '{"state": 200, "info": "设计师不存在，请在联想列表中选择"}';
			exit();
		}
	}

}

if($dopost == "save" && $submit == "提交"){
	//保存到表
	$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`title`, `type`, `style`, `units`, `comstyle`, `litpic`, `area`, `unitspic`, `btype`, `price`, `designer`, `case`, `communityid`, `began`, `end`, `visit`, `pics`, `weight`, `click`, `state`, `pubdate`) VALUES ('$title', '$type', '$style', '$units', '$comstyle', '$litpic', '$area', '$unitspic', '$btype', '$price', '$designer', '$case', '$communityid', '".GetMkTime($began)."', '$end', '$visit', '$imglist', '$weight', '$click', '$state', '".GetMkTime(time())."')");
	$aid = $dsql->dsqlOper($archives, "lastid");

	if(is_numeric($aid)){
		adminLog("添加施工案例", $title);

		$param = array(
			"service"     => "renovation",
			"template"    => "case-detail",
			"id"          => $aid
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
		$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `title` = '$title', `type` = '$type', `style` = '$style', `units` = '$units', `comstyle` = '$comstyle', `litpic` = '$litpic', `area` = '$area', `unitspic` = '$unitspic', `btype` = '$btype', `price` = '$price', `designer` = '$designer', `case` = '$case', `communityid` = '$communityid', `began` = '".GetMkTime($began)."', `end` = '$end', `visit` = '$visit', `pics` = '$imglist', `weight` = '$weight', `click` = '$click', `state` = '$state' WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "update");

		if($results == "ok"){
			adminLog("修改施工案例", $title);

			$param = array(
				"service"     => "renovation",
				"template"    => "case-detail",
				"id"          => $id
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

			$title     = $results[0]['title'];
			$type      = $results[0]['type'];
			$style     = $results[0]['style'];
			$units     = $results[0]['units'];
			$comstyle  = $results[0]['comstyle'];
			$litpic    = $results[0]['litpic'];
			$area      = $results[0]['area'];
			$unitspic  = $results[0]['unitspic'];
			$btype     = $results[0]['btype'];
			$price     = $results[0]['price'];
			$designer  = $results[0]['designer'];
			$case      = $results[0]['case'];
			$communityid = $results[0]['communityid'];
			$began     = $results[0]['began'];
			$end       = $results[0]['end'];
			$visit     = $results[0]['visit'];
			$pics      = $results[0]['pics'];
			$weight    = $results[0]['weight'];
			$click     = $results[0]['click'];
			$state     = $results[0]['state'];

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

	//js
	$jsFile = array(
		'ui/jquery.dragsort-0.5.1.min.js',
		'ui/bootstrap-datetimepicker.min.js',
		'publicUpload.js',
		'admin/renovation/renovationDiaryAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('pagetitle', $pagetitle);
	require_once(HUONIAOINC."/config/renovation.inc.php");
	global $customUpload;
	if($customUpload == 1){
		global $custom_thumbSize;
		global $custom_thumbType;
		global $custom_atlasSize;
		global $custom_atlasType;
		$huoniaoTag->assign('thumbSize', $custom_thumbSize);
		$huoniaoTag->assign('thumbType', "*.".str_replace("|", ";*.", $custom_thumbType));
		$huoniaoTag->assign('atlasSize', $custom_atlasSize);
		$huoniaoTag->assign('atlasType', "*.".str_replace("|", ";*.", $custom_atlasType));
	}
	$huoniaoTag->assign('mapCity', $cfg_mapCity);
	$huoniaoTag->assign('id', $id);

	$huoniaoTag->assign('title', $title);

	//显示状态
	$huoniaoTag->assign('typeopt', array('0', '1'));
	$huoniaoTag->assign('typenames',array('家装','公装'));
	$huoniaoTag->assign('type', $type == "" ? 0 : $type);


	$huoniaoTag->assign('litpic', $litpic);
	$huoniaoTag->assign('area', $area);
	$huoniaoTag->assign('unitspic', $unitspic);

	//商业装修
	$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_type` WHERE `parentid` = 3 ORDER BY `weight` ASC");
	$results = $dsql->dsqlOper($archives, "results");
	$comstylelist = array();
	$comstyleval  = array();
	foreach($results as $value){
		array_push($comstylelist, $value['typename']);
		array_push($comstyleval, $value['id']);
	}
	$huoniaoTag->assign('comstylelist', $comstylelist);
	$huoniaoTag->assign('comstyleval', $comstyleval);
	$huoniaoTag->assign('comstyle', $comstyle);

	//装修风格
	$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_type` WHERE `parentid` = 4 ORDER BY `weight` ASC");
	$results = $dsql->dsqlOper($archives, "results");
	$stylelist = array();
	$styleval  = array();
	foreach($results as $value){
		array_push($stylelist, $value['typename']);
		array_push($styleval, $value['id']);
	}
	$huoniaoTag->assign('stylelist', $stylelist);
	$huoniaoTag->assign('styleval', $styleval);
	$huoniaoTag->assign('style', $style);

	//户型
	$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_type` WHERE `parentid` = 8 ORDER BY `weight` ASC");
	$results = $dsql->dsqlOper($archives, "results");
	$unitslist = array();
	$unitsval  = array();
	foreach($results as $value){
		array_push($unitslist, $value['typename']);
		array_push($unitsval, $value['id']);
	}
	$huoniaoTag->assign('unitslist', $unitslist);
	$huoniaoTag->assign('unitsval', $unitsval);
	$huoniaoTag->assign('units', $units);

	//类型
	$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_type` WHERE `parentid` = 7 ORDER BY `weight` ASC");
	$results = $dsql->dsqlOper($archives, "results");
	$btypeopt = $btypenames = array();
	foreach($results as $value){
		array_push($btypeopt, $value['id']);
		array_push($btypenames, $value['typename']);
	}
	$huoniaoTag->assign('btypeopt', $btypeopt);
	$huoniaoTag->assign('btypenames', $btypenames);
	$huoniaoTag->assign('btype', $btype == "" ? $btypeopt[0] : $btype);

	$huoniaoTag->assign('price', $price);

	//设计师
	$huoniaoTag->assign('designer', $designer);
	$userSql = $dsql->SetQuery("SELECT `name` FROM `#@__renovation_team` WHERE `id` = ". $designer);
	$username = $dsql->getTypeName($userSql);
	$huoniaoTag->assign('designername', $username[0]['name']);

	//设计方案
	$huoniaoTag->assign('case', $case);
	if(!empty($case)){
		$caseSql = $dsql->SetQuery("SELECT `title` FROM `#@__renovation_case` WHERE `id` = ". $case);
		$casename = $dsql->getTypeName($caseSql);
		if($casename){
			$huoniaoTag->assign('caseName', '<span data-id="'.$case.'">'.$casename[0]['title'].'<a href="javascript:;">×</a></span>');
		}
	}

	$huoniaoTag->assign('addrListArr', json_encode($dsql->getTypeList($adminCityIds, "site_area")));

	//小区
	$huoniaoTag->assign('communityid', $communityid);

	if(!empty($communityid)){
		$communityidSql = $dsql->SetQuery("SELECT `title` FROM `#@__renovation_community` WHERE `id` = ". $communityid);
		$communityidname = $dsql->getTypeName($communityidSql);
		if($casename){
			$community = $communityidname[0]['title'];
		}
	}

	$huoniaoTag->assign('communityName', !empty($community) ? '<span>'.$community.'<a href="javascript:;">×</a></span>' : "");
	$huoniaoTag->assign('began', $began ? date("Y-m-d", $began) : "");
	$huoniaoTag->assign('end', $end);

	$huoniaoTag->assign('imglist', json_encode(!empty($pics) ? explode("||", $pics) : array()));

	//参观
	$huoniaoTag->assign('visitopt', array('0', '1'));
	$huoniaoTag->assign('visitnames',array('不接受','接受'));
	$huoniaoTag->assign('visit', $visit == "" ? 0 : $visit);

	$huoniaoTag->assign('click', $click == "" ? "1" : $click);
	$huoniaoTag->assign('weight', $weight == "" ? "1" : $weight);

	//显示状态
	$huoniaoTag->assign('stateopt', array('0', '1', '2'));
	$huoniaoTag->assign('statenames',array('待审核','已审核','审核拒绝'));
	$huoniaoTag->assign('state', $state == "" ? 1 : $state);
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/renovation";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
