<?php
/**
 * 添加分类信息
 *
 * @version        $Id: infoAdd.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.Info
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/huangye";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "huangyeAdd.html";

$action     = "huangye";
$pagetitle  = "发布黄页";
$dopost     = $dopost ? $dopost : "save";        //操作类型 save添加 edit修改

if($dopost == "edit"){
	checkPurview("editHuangye");
}else{
	checkPurview("huangyeAdd");
}

if($dopost != ""){
	$pubdate = GetMkTime(time());	//发布时间
	$rec     = (int)$rec;
	$fire    = (int)$fire;
	$top     = (int)$top;

	//对字符进行处理
	$title       = cn_substrR($title,60);
	$color       = cn_substrR($color,6);

	//获取当前管理员
	$adminid = $userLogin->getUserID();

}

if(empty($click)) $click = mt_rand(50, 200);

//有效期-下拉菜单
$huoniaoTag->assign('validList', array(0 => '长期有效', 3 => '三天后过期', 7 => '一周后过期', 30 => '一个月后过期', 90 => '三个月后过期'));
// $huoniaoTag->assign('valid', 1);

//阅读权限-下拉菜单
$huoniaoTag->assign('arcrankList', array(0 => '等待审核', 1 => '审核通过', 2 => '审核拒绝', 3 => '取消显示'));
$huoniaoTag->assign('arcrank', 1);  //阅读权限默认审核通过

if($dopost == "edit"){

	$pagetitle = "修改信息";

	if($submit == "提交"){
		if($token == "") die('token传递失败！');
		//表单二次验证
		if($typeid == ''){
			echo '{"state": 200, "info": "请选择黄页分类"}';
			exit();
		}

		if(trim($title) == ''){
			echo '{"state": 200, "info": "标题不能为空"}';
			exit();
		}

		if(trim($litpic) == ''){
			echo '{"state": 200, "info": "请上传logo"}';
			exit();
		}

		if(trim($addr) == ''){
			echo '{"state": 200, "info": "请选择所属地区"}';
			exit();
		}

		if(trim($lnglat) == ''){
			echo '{"state": 200, "info": "请标注联系地址坐标！"}';
			exit();
		}

		/*if(trim($person) == ''){
			echo '{"state": 200, "info": "请输入联系人信息"}';
			exit();
		}*/

		if(trim($tel) == ''){
			echo '{"state": 200, "info": "请输入联系电话"}';
			exit();
		}

		/*if(trim($project) == ''){
			echo '{"state": 200, "info": "请填写主营项目"}';
			exit();
		}
*/
		if(!trim($valid) == ''){
			$valid = GetMkTime($valid);
		}else{
			$valid = 0;
		}

		// 自定义导航
		/*if(empty($detail)){
			echo '{"state": 200, "info": "请至少填写一项导航"}';
		}*/


		$ip = GetIP();
		$ipAddr = getIpAddr($ip);

		$teladdr = getTelAddr($tel);

		//坐标
		if(!empty($lnglat)){
			$lnglat = explode(",", $lnglat);
			$longitude = $lnglat[0];
			$latitude  = $lnglat[1];
		}

		if($userid != 0){
			$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` = ".$userid);
			$userResult = $dsql->dsqlOper($userSql, "results");
			if(!$userResult){
				echo '{"state": 200, "info": "会员不存在，请重新选择"}';
				exit();
			}
		}

		$rz1 = empty($rz1) ? 0 : $rz1;
		$rz2 = empty($rz2) ? 0 : $rz2;
		$rz3 = empty($rz3) ? 0 : $rz3;
		$rz4 = empty($rz4) ? 0 : $rz4;

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__".$action."list` SET `cityid` = '$cityid', `typeid` = ".$typeid.", `title` = '".$title."', `color` = '".$color."', `peise` = '".$peise."', `litpic` = '".$litpic."', `weight` = ".$weight.", `valid` = '".$valid."', `addr` = ".$addr.", `address` = '".$address."', `longitude` = '".$longitude."', `latitude` = '".$latitude."', `project` = '".$project."', `pics` = '$imglist', `person` = '".$person."', `tel` = '".$tel."', `teladdr` = '".$teladdr."', `qq` = '".$qq."', `email` = '".$email."', `weixin` = '".$weixin."', `weixinQr` = '".$weixinQr."', `click` = ".$click.", `arcrank` = ".$arcrank.", `userid` = '$userid', `rec` = '$rec', `fire` = '$fire', `top` = '$top', `rz1` = '$rz1', `rz2` = '$rz2', `rz3` = '$rz3', `rz4` = '$rz4' WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "update");

		if($results != "ok"){
			echo '{"state": 200, "info": "主表保存失败！"}';
			exit();
		}

		// 更新导航内容
		// 先删除
		$delSql = $dsql->SetQuery("DELETE FROM `#@__huangyenav` WHERE `aid` = ".$id);
		$delRet = $dsql->dsqlOper($delSql, "update");
		if($delRet == "ok"){
			if(!empty($detail)){
				$naveData = explode(";;;;;;", $detail);

				foreach ($naveData as $k1 => $value1) {
					$item = explode(",,,,,,", $value1);
					foreach ($item as $k2 => $value2) {
						$field = explode("::::::", $value2);
						${$field[0]} = $field[1];
					}
					//保存自定义导航
					$archives = $dsql->SetQuery("INSERT INTO `#@__".$action."nav` (`aid`, `nav`, `body`, `mbody`, `show`, `weight`) VALUES (".$id.", '".$nav."', '".$body."', '".$mbody."', '".$show."', '".$weight."')");
					$fid = $dsql->dsqlOper($archives, "lastid");
				}
			}
		}

		adminLog("修改黄页信息", $title);

		$param = array(
			"service"     => "huangye",
			"template"    => "detail",
			"id"          => $id
		);
		$url = getUrlPath($param);

		echo '{"state": 100, "url": "'.$url.'"}';die;


	}else{
		if(!empty($id)){

			//主表信息
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$action."list` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "results");

			if(!empty($results)){

				$typeid  = $results[0]['typeid'];
				$title   = $results[0]['title'];
				$color   = $results[0]['color'];
				$peise   = $results[0]['peise'];
				$litpic  = $results[0]['litpic'];
				$weight  = $results[0]['weight'];
				$valid   = empty($results[0]['valid']) ? "" : date("Y-m-d",$results[0]['valid']);
				$addr    = $results[0]['addr'];
				$address = $results[0]['address'];
				$lnglat  = $results[0]['longitude'].",".$results[0]['latitude'];
				$body    = $results[0]['body'];
				$mbody   = $results[0]['mbody'];
				$person  = $results[0]['person'];
				$tel     = $results[0]['tel'];
				$qq      = $results[0]['qq'];
				$click   = $results[0]['click'];
				$arcrank = $results[0]['arcrank'];
				$rec     = $results[0]['rec'];
				$fire    = $results[0]['fire'];
				$top     = $results[0]['top'];
				$weixin  = $results[0]['weixin'];
				$weixinQr= $results[0]['weixinQr'];
				$email   = $results[0]['email'];
				$project = $results[0]['project'];
				$userid  = $results[0]['userid'];
				$pics    = $results[0]['pics'];
				$rz1     = $results[0]['rz1'];
				$rz2     = $results[0]['rz2'];
				$rz3     = $results[0]['rz3'];
				$rz4     = $results[0]['rz4'];
                $cityid  = $results[0]['cityid'];

				if($userid){
					$userSql  = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ".$userid);
					$userRet  = $dsql->getTypeName($userSql);
					$username = $userRet[0]['username'];
				}else{
					$username = "";
				}

				// 获取导航内容
				$navSql  = $dsql->SetQuery("SELECT *  FROM `#@__huangyenav` WHERE `aid` = ".$id." ORDER BY `weight` ASC");
				$navList = $dsql->dsqlOper($navSql, "results");
				if(!$navList){
					$navList = array();
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
}elseif($dopost == "" || $dopost == "save"){
	$dopost = "save";

	//表单提交
	if($submit == "提交"){
		if($token == "") die('token传递失败！');
		//表单二次验证
		if($typeid == ''){
			echo '{"state": 200, "info": "请选择黄页分类"}';
			exit();
		}

		if(trim($title) == ''){
			echo '{"state": 200, "info": "标题不能为空"}';
			exit();
		}

		if(trim($litpic) == ''){
			echo '{"state": 200, "info": "请上传logo"}';
			exit();
		}

		if(trim($addr) == ''){
			echo '{"state": 200, "info": "请选择所属地区"}';
			exit();
		}

		if(trim($lnglat) == ''){
			echo '{"state": 200, "info": "请标注联系地址坐标！"}';
			exit();
		}

		/*if(trim($person) == ''){
			echo '{"state": 200, "info": "请输入联系人信息"}';
			exit();
		}*/

		if(trim($tel) == ''){
			echo '{"state": 200, "info": "请输入联系电话"}';
			exit();
		}

		/*if(trim($project) == ''){
			echo '{"state": 200, "info": "请填写主营项目"}';
			exit();
		}*/

		$valid = empty($valid) ? 0 : GetMkTime($valid);  //有效期

		// 自定义导航
		/*if(empty($detail)){
			echo '{"state": 200, "info": "请至少填写一项导航"}';
		}*/

		$ip = GetIP();
		$ipAddr = getIpAddr($ip);

		$teladdr = getTelAddr($tel);

		//坐标
		if(!empty($lnglat)){
			$lnglat = explode(",", $lnglat);
			$longitude = $lnglat[0];
			$latitude  = $lnglat[1];
		}

		if($userid != 0){
			$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` = ".$userid);
			$userResult = $dsql->dsqlOper($userSql, "results");
			if(!$userResult){
				echo '{"state": 200, "info": "会员不存在，请重新选择"}';
				exit();
			}
		}

		$rz1 = empty($rz1) ? 0 : $rz1;
		$rz2 = empty($rz2) ? 0 : $rz2;
		$rz3 = empty($rz3) ? 0 : $rz3;
		$rz4 = empty($rz4) ? 0 : $rz4;

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__".$action."list` (`cityid`, `typeid`, `title`, `color`, `peise`, `litpic`, `weight`, `valid`, `addr`, `address`, `longitude`, `latitude`, `project`, `pics`, `person`, `tel`, `teladdr`, `qq`, `email`, `weixin`, `weixinQr`, `click`, `arcrank`, `ip`, `ipaddr`, `pubdate`, `userid`, `admin`, `rec`, `fire`, `top`, `rz1`, `rz2`, `rz3`, `rz4`) VALUES ('".$cityid."', ".$typeid.", '".$title."', '".$color."', '".$peise."', '".$litpic."', ".$weight.", ".$valid.", '".$addr."', '".$address."', '$longitude', '$latitude', '".$project."', '$imglist', '".$person."', '".$tel."', '".$teladdr."', '".$qq."', '".$email."', '".$weixin."', '".$weixinQr."', ".$click.", ".$arcrank.", '".$ip."', '".$ipAddr."', ".GetMkTime(time()).", ".$userid.", ".$adminid.", '$rec', '$fire', '$top', '$rz1', '$rz2', '$rz3', '$rz4')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){
			if(!empty($detail)){
				$naveData = explode(";;;;;;", $detail);

				foreach ($naveData as $k1 => $value1) {
					$item = explode(",,,,,,", $value1);
					foreach ($item as $k2 => $value2) {
						$field = explode("::::::", $value2);
						${$field[0]} = $field[1];
					}
					//保存自定义导航
					$archives = $dsql->SetQuery("INSERT INTO `#@__".$action."nav` (`aid`, `nav`, `body`, `mbody`, `show`, `weight`) VALUES (".$aid.", '".$nav."', '".$body."', '".$mbody."', '".$show."', '".$weight."')");
					$fid = $dsql->dsqlOper($archives, "lastid");
				}
			}
		}

		adminLog("发布黄页信息", $title);

		$param = array(
			"service"     => "huangye",
			"template"    => "detail",
			"id"          => $aid
		);
		$url = getUrlPath($param);

		echo '{"state": 100, "url": "'.$url.'"}';die;

	}
}

//验证模板文件
if(file_exists($tpl."/".$templates)){
	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/jquery.colorPicker.js',
		'ui/jquery.dragsort-0.5.1.min.js',
		'ui/bootstrap-datetimepicker.min.js',
		'publicUpload.js',
        'publicAddr.js',
		'admin/huangye/huangyeAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	require_once(HUONIAOINC."/config/".$action.".inc.php");
	global $customUpload;
	if($customUpload == 1){
		global $custom_atlasSize;
		global $custom_atlasType;
		$huoniaoTag->assign('atlasSize', $custom_atlasSize);
		$huoniaoTag->assign('atlasType', "*.".str_replace("|", ";*.", $custom_atlasType));
	}

	$huoniaoTag->assign('action', $action);
	$huoniaoTag->assign('pagetitle', $pagetitle);
	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('id', $id);
	$huoniaoTag->assign('typeid', empty($typeid) ? "''" : $typeid);
	$huoniaoTag->assign('title', $title);
	$huoniaoTag->assign('peise', empty($peise) ? 1 : $peise);
	$huoniaoTag->assign('litpic', $litpic);
	$huoniaoTag->assign('color', $color);
	$huoniaoTag->assign('addr', empty($addr) ? "''" : $addr);
    $huoniaoTag->assign('cityid', $cityid);
	$huoniaoTag->assign('mapCity', $cfg_mapCity);
	$huoniaoTag->assign('address', $address);
	$huoniaoTag->assign('lnglat', $lnglat == "," ? "" : $lnglat);
	$huoniaoTag->assign('click', $click);
	$huoniaoTag->assign('weight', $weight == "" ? "50" : $weight);
	$huoniaoTag->assign('person', $person);
	$huoniaoTag->assign('tel', $tel);
	$huoniaoTag->assign('qq', $qq);
	$huoniaoTag->assign('weixin', $weixin);
	$huoniaoTag->assign('weixinQr', $weixinQr);
	$huoniaoTag->assign('email', $email);
	$huoniaoTag->assign('project', $project);
	$huoniaoTag->assign('userid', empty($userid) ? 0 : $userid);
	$huoniaoTag->assign('username', $username);
	$huoniaoTag->assign('navList', $navList);
	$huoniaoTag->assign('valid', $valid);
	$huoniaoTag->assign('arcrank', $arcrank == "" ? 1 : $arcrank);
	$huoniaoTag->assign('imglist', empty($imglist) ? "''" : $imglist);
	$huoniaoTag->assign('typeListArr', json_encode($dsql->getTypeList(0, $action."type")));
	$huoniaoTag->assign('addrListArr', json_encode($dsql->getTypeList(0, $action."addr")));
	$huoniaoTag->assign('imglist', json_encode(!empty($pics) ? explode(",", $pics) : array()));

	$huoniaoTag->assign('rec', $rec);
	$huoniaoTag->assign('fire', $fire);
	$huoniaoTag->assign('top', $top);
	$huoniaoTag->assign('rz1', $rz1);
	$huoniaoTag->assign('rz2', $rz2);
	$huoniaoTag->assign('rz3', $rz3);
	$huoniaoTag->assign('rz4', $rz4);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/huangye";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
