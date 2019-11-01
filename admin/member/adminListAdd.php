<?php
/**
 * 添加管理员
 *
 * @version        $Id: adminListAdd.php 2014-1-1 上午0:10:16 $
 * @package        HuoNiao.Member
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("adminListAdd");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/member";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "adminListAdd.html";

$tab = "member";
$dopost = $dopost == "" ? "add" : $dopost;        //操作类型 save添加 edit修改

if($submit == "提交"){
	if($token == "") die('token传递失败！');
}

$mtype = (int)$mtype;

//新增
if($dopost == "add"){

	//表单提交
	if($submit == "提交"){

		//表单二次验证
		if(trim($username) == ''){
			echo '{"state": 200, "info": "请输入用户名！"}';
			exit();
		}
		if(trim($password) == ''){
			echo '{"state": 200, "info": "请输入密码！"}';
			exit();
		}
		if(trim($nickname) == ''){
			echo '{"state": 200, "info": "请输入真实姓名！"}';
			exit();
		}
		if(!$mtype){
			if(trim($mgroupid) == ''){
				echo '{"state": 200, "info": "请选择所属管理组！"}';
				exit();
			}
		}else{
			if(trim($mcityid) == ''){
				echo '{"state": 200, "info": "请选择管辖城市！"}';
				exit();
			}

			$adminCityIdsArr = explode(',', $adminCityIds);
			if(!in_array($mcityid, $adminCityIdsArr)){
				echo '{"state": 200, "info": "选择的城市不在授权范围"}';
				exit();
			}
		}

		if($userType == 3 && !$mtype){
			echo '{"state": 200, "info": "城市管理员不可以添加系统管理员！"}';
			exit();
		}

		$purviews = isset($purviews) ? join(',', $purviews) : '';

		$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `username` = '$username'");
		$return = $dsql->dsqlOper($archives, "results");
		if($return){
			echo '{"state": 200, "info": "此用户名已被占用，请重新填写！"}';
			exit();
		}

		$password = $userLogin->_getSaltedHash($password);

		//保存到主表
		$mgroupid = $mtype ? $mcityid : $mgroupid;
		$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`mtype`, `username`, `password`, `nickname`, `mgroupid`, `state`, `regtime`, `regip`, `purviews`) VALUES ('$mtype', '$username', '$password', '$nickname', $mgroupid, $state, ".GetMkTime(time()).", '".GetIP()."', '$purviews')");
		$return = $dsql->dsqlOper($archives, "update");

		if($return == "ok"){
			adminLog("新增管理员", $username);
			echo '{"state": 100, "info": '.json_encode("添加成功！").'}';
		}else{
			echo $return;
		}
		die;
	}

//修改
}elseif($dopost == "edit"){

	if($id == "") die('要修改的信息ID传递失败！');
	if($submit == "提交"){

		//表单二次验证
		if(trim($username) == ''){
			echo '{"state": 200, "info": "请输入用户名！"}';
			exit();
		}
		if(trim($nickname) == ''){
			echo '{"state": 200, "info": "请输入真实姓名！"}';
			exit();
		}
		if(!$mtype){
			if(trim($mgroupid) == ''){
				echo '{"state": 200, "info": "请选择所属管理组！"}';
				exit();
			}
		}else{
			if(trim($mcityid) == ''){
				echo '{"state": 200, "info": "请选择管辖城市！"}';
				exit();
			}

			$adminCityIdsArr = explode(',', $adminCityIds);
			if(!in_array($mcityid, $adminCityIdsArr)){
				echo '{"state": 200, "info": "选择的城市不在授权范围"}';
				exit();
			}
		}

		if($userType == 3 && !$mtype){
			echo '{"state": 200, "info": "城市管理员不可以添加系统管理员！"}';
			exit();
		}

		$purviews = isset($purviews) ? join(',', $purviews) : '';

		$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `username` = '$username' AND `id` != $id");
		$return = $dsql->dsqlOper($archives, "results");
		if($return){
			echo '{"state": 200, "info": "此用户名已被占用，请重新填写！"}';
			exit();
		}

		//保存到主表
		$mgroupid = $mtype ? $mcityid : $mgroupid;
		if($password == ""){
			$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `mtype` = '$mtype', `username` = '$username', `nickname` = '$nickname', `mgroupid` = $mgroupid, `state` = $state, `purviews` = '$purviews' WHERE `id` = ".$id);
		}else{
			$password = $userLogin->_getSaltedHash($password);
			$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `mtype` = '$mtype', `username` = '$username', `password` = '$password', `nickname` = '$nickname', `mgroupid` = $mgroupid, `state` = $state, `purviews` = '$purviews' WHERE `id` = ".$id);
		}
		$return = $dsql->dsqlOper($archives, "update");

		if($return == "ok"){
			adminLog("修改管理员", $username);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}else{
			echo $return;
		}
		die;

	}else{
		if(!empty($id)){

			//主表信息
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "results");

			if(!empty($results)){

				//城市分站管理员权限验证
				if($userType == 3 && $userLogin->getUserID() != $id){
					ShowMsg('权限验证失败！', "javascript:;");
					die;
				}

				$username     = $results[0]['username'];
				$nickname     = $results[0]['nickname'];
				$mtype        = $results[0]['mtype'];
				$mgroupid     = $results[0]['mgroupid'];
				$state        = $results[0]['state'];
				$purviews     = $results[0]['purviews'];

			}else{
				ShowMsg('要修改的信息不存在或已删除！', "-1");
				die;
			}

		}else{
			ShowMsg('要修改的信息参数传递失败，请联系管理员！', "-1");
			die;
		}
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
		'ui/chosen.jquery.min.js',
		'admin/member/adminListAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	//会员类型
	if($userType != 3){
		$huoniaoTag->assign('mtypeArr', array('0', '3'));
		$huoniaoTag->assign('mtypeNames',array('系统管理员','城市管理员'));
	}else{
		$huoniaoTag->assign('mtypeArr', array('3'));
		$huoniaoTag->assign('mtypeNames',array('城市管理员'));
	}
	$huoniaoTag->assign('mtype', $mtype ? $mtype : ($userType == 3 ? 3 : 0));

	$huoniaoTag->assign('pagetitle', $pagetitle);
	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('id', $id);
	$huoniaoTag->assign('username', $username);
	$huoniaoTag->assign('nickname', $nickname);

	$archives = $dsql->SetQuery("SELECT `id`, `groupname` FROM `#@__admingroup`");
	$results = $dsql->dsqlOper($archives, "results");

	$huoniaoTag->assign('groupList', $results);
	$huoniaoTag->assign('mgroupid', empty($mgroupid) ? "''" : $mgroupid);

	//状态-单选
	$huoniaoTag->assign('stateList', array('0', '1'));
	$huoniaoTag->assign('stateName',array('正常','锁定'));
	$huoniaoTag->assign('state', $state == "" ? 0 : $state);

	//分站城市
	$huoniaoTag->assign('cityArr', $userLogin->getAdminCity());

	$huoniaoTag->assign('purviews', $purviews ? explode(',', $purviews) : array());

	//城市管理员权限集合
	$huoniaoTag->assign('cityPurviews', $userLogin->getAdminPermissions());

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/member";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
