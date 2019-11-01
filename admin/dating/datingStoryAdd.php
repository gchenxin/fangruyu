<?php
/**
 * 新增交友成功故事
 *
 * @version        $Id: datingStoryAdd.php 2014-7-22 上午9:48:11 $
 * @package        HuoNiao.Dating
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("datingStory");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "datingStoryAdd.html";

$tab = "dating_story";
$dopost = $dopost ? $dopost : "save";        //操作类型 save添加 edit修改
if($dopost == "edit"){
	$pagetitle = "修改交友成功故事";
}else{
	$pagetitle = "添加交友成功故事";
}

if(empty($fid)) $fid = 0;
if(empty($tid)) $tid = 0;
if(empty($state)) $state = 0;

if($_POST['submit'] == "提交"){

	$tags = isset($tags) ? ",".join(',',$tags)."," : '';

	if($token == "") die('token传递失败！');
	//二次验证
	if($fid == 0 && trim($fidname) == ''){
		echo '{"state": 200, "info": "请选择申请人"}';
		exit();
	}
	if($fid == 0){
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` = '".$fidname."'");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			echo '{"state": 200, "info": "会员不存在，请重新选择"}';
			exit();
		}
		$fid = $userResult[0]['id'];
	}else{
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` = ".$fid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			echo '{"state": 200, "info": "会员不存在，请在联想列表中选择"}';
			exit();
		}
	}

	if($tid != 0 && trim($tidname) != ''){
		//echo '{"state": 200, "info": "请选择爱人"}';
		//exit();

		if($tid == 0){
			$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` = '".$tidname."'");
			$userResult = $dsql->dsqlOper($userSql, "results");
			if(!$userResult){
				echo '{"state": 200, "info": "会员不存在，请重新选择"}';
				exit();
			}
			$tid = $userResult[0]['id'];
		}else{
			$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` = ".$tid);
			$userResult = $dsql->dsqlOper($userSql, "results");
			if(!$userResult){
				echo '{"state": 200, "info": "会员不存在，请在联想列表中选择"}';
				exit();
			}
		}
	}

	//检测是否已经注册
	if($dopost == "save"){

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_story` WHERE (`fid` = ".$fid." OR `fid` = ".$tid." OR `tid` = ".$fid." OR (`tid` = ".$tid." AND `tid` <> 0))");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			echo '{"state": 200, "info": "此会员已经开通成功故事！"}';
			exit();
		}

	}else{

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_story` WHERE (`fid` = '".$fid."' OR `fid` = '".$tid."' OR `tid` = '".$fid."' OR (`tid` = '".$tid."' AND `tid` <> 0)) AND `id` != ". $id);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			echo '{"state": 200, "info": "此会员已经开通成功故事！"}';
			exit();
		}

	}

	if(empty($litpic)){
		echo '{"state": 200, "info": "请上传合影照片！"}';
		exit();
	}

	if(empty($kdate)){
		echo '{"state": 200, "info": "请选择确定关系时间！"}';
		exit();
	}

	if(empty($title)){
		echo '{"state": 200, "info": "请输入故事主题！"}';
		exit();
	}

	if(empty($content)){
		echo '{"state": 200, "info": "请输入故事内容！"}';
		exit();
	}

}

if($dopost == "save" && $submit == "提交"){
	//保存到表
	$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`fid`, `tid`, `litpic`, `process`, `kdate`, `tags`, `title`, `content`, `pics`, `state`, `pubdate`) VALUES ('$fid', '$tid', '$litpic', '$process', '".GetMkTime($kdate)."', '$tags', '$title', '$content', '$imglist', '$state', '".GetMkTime(time())."')");
	$aid = $dsql->dsqlOper($archives, "lastid");

	if($aid){
		adminLog("添加交友成功故事", $title);

		$param = array(
			"service"     => "dating",
			"template"    => "story",
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
		$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `fid` = '$fid', `tid` = '$tid', `litpic` = '$litpic', `process` = '$process', `kdate` = '".GetMkTime($kdate)."', `tags` = '$tags', `title` = '$title', `content` = '$content', `pics` = '$imglist', `state` = '$state' WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "update");

		if($results == "ok"){
			adminLog("修改交友成功故事", $title);

			$param = array(
				"service"     => "dating",
				"template"    => "story",
				"id"          => $id
			);
			$url = getUrlPath($param);
			echo '{"state": 100, "url": "'.$url.'"}';
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

			$fid      = $results[0]['fid'];
			$tid      = $results[0]['tid'];
			$litpic   = $results[0]['litpic'];
			$process  = $results[0]['process'];
			$kdate    = $results[0]['kdate'];
			$tags     = $results[0]['tags'];
			$title    = $results[0]['title'];
			$content  = $results[0]['content'];
			$pics     = $results[0]['pics'];
			$state    = $results[0]['state'];

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
		'ui/bootstrap-datetimepicker.min.js',
    'ui/jquery.dragsort-0.5.1.min.js',
		'publicUpload.js',
		'admin/dating/datingStoryAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('pagetitle', $pagetitle);
	require_once(HUONIAOINC."/config/dating.inc.php");
	global $customUpload;
	if($customUpload == 1){
		global $custom_thumbSize;
		global $custom_thumbType;
		$huoniaoTag->assign('thumbSize', $custom_thumbSize);
		$huoniaoTag->assign('thumbType', "*.".str_replace("|", ";*.", $custom_thumbType));
		$huoniaoTag->assign('atlasSize', $custom_atlasSize);
		$huoniaoTag->assign('atlasType', "*.".str_replace("|", ";*.", $custom_atlasType));
	}

	if($dopost == "edit"){
		$huoniaoTag->assign('id', $id);

		$huoniaoTag->assign('fid', $fid);
		$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $fid);
		$username = $dsql->getTypeName($userSql);
		$huoniaoTag->assign('fidname', $username[0]['username']);

		$huoniaoTag->assign('tid', $tid);
		$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $tid);
		$username = $dsql->getTypeName($userSql);
		$huoniaoTag->assign('tidname', $username[0]['username']);
	}

	$huoniaoTag->assign('litpic', $litpic);

	$huoniaoTag->assign('processopt', array('0', '1', '2'));
	$huoniaoTag->assign('processnames',array('约会中','恋爱中','结婚啦'));
	$huoniaoTag->assign('process', $process == "" ? 0 : $process);

	$huoniaoTag->assign('kdate', $kdate == "" ? "" : date("Y-m-d", $kdate));


	//标签
	$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_item` WHERE `parentid` = 107 ORDER BY `weight` ASC");
	$results = $dsql->dsqlOper($archives, "results");
	$list = $val = array();
	foreach($results as $value){
		$list[] = $value['typename'];
		$val[] = $value['id'];
	}
	$huoniaoTag->assign('tagsList', $list);
	$huoniaoTag->assign('tagsValue', $val);
	$huoniaoTag->assign('tags', $tags == "" ? 0 : explode(",", $tags));


	$huoniaoTag->assign('title', $title);
	$huoniaoTag->assign('content', $content);
	$huoniaoTag->assign('imglist', json_encode(!empty($pics) ? explode(",", $pics) : array()));

	//显示状态
	$huoniaoTag->assign('stateopt', array('0', '1', '2'));
	$huoniaoTag->assign('statenames',array('待审核','已审核','审核拒绝'));
	$huoniaoTag->assign('state', $state == "" ? 1 : $state);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
