<?php
/**
 * 添加
 *
 * @version        $Id: schoolAdd.php Sat Jan 11 14:05:38 CST 2020 $
 * @package        HuoNiao.House
 * @copyright      Copyright (c) 2020 -.
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/house";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "schoolAdd.html";

$tab = "house_school";
$dopost = $dopost ? $dopost : "save";        //操作类型 save添加 edit修改
if($dopost == "edit"){
	$pagetitle = "修改校区";
	checkPurview("schoolEdit");
}else{
	$pagetitle = "添加校区";
	checkPurview("schoolAdd");
}

if(empty($title)) $title = '';
if(empty($addrid)) $addrid = 0;
if(empty($state)) $state = 0;
if(empty($addr)) $addr = '';
if(empty($lnglat)) $lnglat = '';
if(empty($litpic)) $litpic = '';
if(empty($imglist)) $imglist = '';
if(empty($intro)) $intro = '';
if(empty($flag)) $flag = '';
if(empty($mbody)) $mbody = '';
if(empty($submit)) $submit = '';
if(empty($weight)) $weight = 1;
if(empty($tel)) $tel = 1;

if($_POST['submit'] == "提交"){
	if($token == "") die('token传递失败！');
	//二次验证
	if(empty($title)){
		echo '{"state": 200, "info": "请输入校区名称！"}';
		exit();
	}
	
	if(empty($addrid) || empty($addr)){
		echo '{"state": 200, "info": "请输入校区地址！"}';
		exit();
	}
	
	//坐标
	if(!empty($lnglat)){
		$lnglat = explode(",", $lnglat);
		$longitude = $lnglat[0];
		$latitude  = $lnglat[1];
	}
	if(empty($longitude) || empty($latitude)){
		echo '{"state": 200, "info": "请选择坐标！"}';
		exit();
	}
	
}

if($dopost == "save" && $submit == "提交"){
	
	//保存到表
	$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`cityid`, `title`, `addrid`, `address`, `intro`, `mbody`, `litpic`, `longitude`, `latitude`,`flag`, `weight`, `date`, `state`, `tel`) 
		VALUES 
		('$cityid', '$title', '$addrid', '$addr','$intro', '$mbody', '$litpic','$longitude', '$latitude', '$flag', '$weight', '".date("Y-m-d H:i:s")."','$state', '$tel')");
	$aid = $dsql->dsqlOper($archives, "lastid");

	//保存图集表
	if($imglist != ""){
		$picList = explode(",",$imglist);
		foreach($picList as $k => $v){
			$picInfo = explode("|", $v);
			$pics = $dsql->SetQuery("INSERT INTO `#@__house_pic` (`type`, `aid`, `picPath`, `picInfo`) VALUES ('houseschool', '$aid', '$picInfo[0]', '$picInfo[1]')");
			$dsql->dsqlOper($pics, "update");
		}
	}

	if($aid){

		if($state == 1){
			updateCache("house_school", 300);
			clearCache("house_school_list", "key");
			clearCache("house_school_total", "key");
		}

		adminLog("添加校区信息", $title);
		$param = array(
			"service"  => "house",
			"template" => "school-detail",
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
		$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `cityid` = '$cityid', `title` = '$title', `addrid` = '$addrid', `address` = '$addr', `litpic` = '$litpic', `intro` = '$intro', `mbody` = '$mbody', `weight` = '$weight', `state` = '$state', `longitude` = '$longitude', `latitude` = '$latitude', `tel` = '$tel', `flag`='" . join(',', $flag) . "' WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "update");

		//先删除文档所属图集
		$archives = $dsql->SetQuery("DELETE FROM `#@__house_pic` WHERE `type` = 'houseschool' AND `aid` = ".$id);
		$dsql->dsqlOper($archives, "update");

		//保存图集表
		if($imglist != ""){
			$picList = explode(",", $imglist);
			foreach($picList as $k => $v){
				$picInfo = explode("|", $v);
				$pics = $dsql->SetQuery("INSERT INTO `#@__house_pic` (`type`, `aid`, `picPath`, `picInfo`) VALUES ('houseschool', '$id', '$picInfo[0]', '$picInfo[1]')");
				$dsql->dsqlOper($pics, "update");
			}
		}

		if($results == "ok"){

			// 清除缓存
			clearCache("house_school_detail", $id);
			checkCache("house_school_list", $id);
			if(($state != 1 && $state_ == 1)|| ($state == 1 && $state_ != 1)){
				clearCache("house_school_total", "key");
			}

			adminLog("修改校区信息", $title);
			$param = array(
				"service"  => "house",
				"template" => "school-detail",
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

			//图表信息
			$archives = $dsql->SetQuery("SELECT * FROM `#@__house_pic` WHERE `type` = 'houseschool' AND `aid` = ".$id." ORDER BY `id` ASC");
			$results = $dsql->dsqlOper($archives, "results");

			if(!empty($results)){
				$imglist = array();
				foreach($results as $key => $value){
					$imglist[$key]["path"] = $value["picPath"];
					$imglist[$key]["info"] = $value["picInfo"];
				}
				$imglist = json_encode($imglist);
			}else{
				$imglist = "''";
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

	//js
	$jsFile = array(
		'ui/jquery.dragsort-0.5.1.min.js',
		'publicUpload.js',
		'publicAddr.js',
		'admin/house/schoolAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('dopost', $dopost);
	require_once(HUONIAOINC."/config/house.inc.php");
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
	$huoniaoTag->assign('id', $id);

	$huoniaoTag->assign('title', $title);
	
	$huoniaoTag->assign('address', $address);
	//区域
	$huoniaoTag->assign('addrListArr', json_encode($dsql->getTypeList(0, "houseaddr")));
	$huoniaoTag->assign('addrid', $addrid == "" ? 0 : $addrid);
    $huoniaoTag->assign('cityid', $cityid == "" ? 0 : $cityid);

	$huoniaoTag->assign('litpic', $litpic);

	$huoniaoTag->assign('intro', $intro);
	$huoniaoTag->assign('mbody', $mbody);
	//显示状态
	$huoniaoTag->assign('stateopt', array('0', '1', '2'));
	$huoniaoTag->assign('statenames',array('待审核','已审核','审核拒绝'));
	$huoniaoTag->assign('state', $state == "" ? 1 : $state);

	$huoniaoTag->assign('weight', $weight);
	$huoniaoTag->assign('tel', $tel);
	$huoniaoTag->assign('lnglat', $longitude.','.$latitude);


	//特色
	$sql = $dsql->SetQuery("select id,typename from #@__houseitem where parentid=(select id from #@__houseitem where typename='校区特色') order by weight");
	$tagList = $dsql->dsqlOper($sql, 'results');
	$huoniaoTag->assign('flaglist', array_column($tagList, 'typename'));
	$huoniaoTag->assign('flagval', array_column($tagList, 'id'));
	$huoniaoTag->assign('flag', explode(",", $flag));

	$huoniaoTag->assign('imglist', empty($imglist) ? "''" : $imglist);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/house";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
