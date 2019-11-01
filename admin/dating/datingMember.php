<?php
/**
 * 交友会员管理
 *
 * @version        $Id: datingMember.php 2014-7-20 上午11:24:18 $
 * @package        HuoNiao.Dating
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("datingMember");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$tab = "dating_member";

//css
$cssFile = array(
    'ui/jquery.chosen.css',
    'admin/chosen.min.css'
);
$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));
if($dopost != ""){
	$templates = "datingMemberAdd.html";

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/bootstrap-datetimepicker.min.js',
        'ui/chosen.jquery.min.js',
		'ui/jquery.dragsort-0.5.1.min.js',
		'publicUpload.js',
		'publicAddr.js',
		'admin/dating/datingMemberAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}else{
	$templates = "datingMember.html";

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/jquery-ui-selectable.js',
        'ui/chosen.jquery.min.js',
		'admin/dating/datingMember.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}

$pagetitle = "交友会员管理";

//审核状态-下拉菜单
$huoniaoTag->assign('arcrankList', array(0 => '等待审核', 1 => '审核通过', 2 => '审核拒绝'));
$huoniaoTag->assign('arcrank', 1);  //阅读权限默认审核通过

//显示状态-下拉菜单
$huoniaoTag->assign('stateList', array(0 => '不显示', 1 => '正常显示'));
$huoniaoTag->assign('state', 1);  //显示状态

//交友开关-下拉菜单
$huoniaoTag->assign('dateswitchList', array(0 => '关闭', 1 => '开启'));
$huoniaoTag->assign('dateswitch', 1);  //显示状态



if($submit == "提交"){
	if($token == "") die('token传递失败！');
	$pubdate     = GetMkTime(time());       //发布时间
	if(!empty($property)) $property = join(",", $property);

	//二次验证
	if($userid == 0 && trim($user) == ''){
		echo '{"state": 200, "info": "请选择所属会员"}';
		exit();
	}else{
		if($userid == 0){
			$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` = '".$user."'");
			$userResult = $dsql->dsqlOper($userSql, "results");
			if(!$userResult){
				echo '{"state": 200, "info": "会员不存在，请重新选择"}';
				exit();
			}
			$userid = $userResult[0]['id'];
		}else{
			$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` = ".$userid);
			$userResult = $dsql->dsqlOper($userSql, "results");
			if(!$userResult){
				echo '{"state": 200, "info": "会员不存在，请在联想列表中选择"}';
				exit();
			}
		}
	}

	//检测是否已经注册
	if($dopost == "Add"){

		$type = (int)$type;

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = '".$userid."' AND `type` = $type");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			echo '{"state": 200, "info": "此会员已经注册过次类型用户！"}';
			exit();
		}

	}else{

		$type = (int)$type;

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = '".$userid."' AND `type` = $type AND `id` != ". $id);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			echo '{"state": 200, "info": "此会员已经注册过次类型用户！"}';
			exit();
		}

	}

	if($birthday){
		$birthdayInfo = birthdayInfo($birthday);
		$zodiac = $birthdayInfo['sx'];
		$constellation = $birthdayInfo['xz'];
		
		$birthday = GetMkTime($birthday);
	}else{
		$zodiac = $constellation = '';
	}

	if($lnglat){
		$arr = explode(",", $lnglat);
		$lng = $arr[0];
		$lat = $arr[1];
	}else{
		$lng = $lat = '';
	}

	if($language){
		$language = join(",", $language);
	}
}

//列表
if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	$uType = (int)$uType;

    $where = " AND `cityid` in (0,$adminCityIds)";

    if ($adminCity){
        $where = " AND `cityid` = $adminCity";
    }

	if($sKeyword != ""){
		$where .= " AND `nickname` LIKE '%$sKeyword%'";
	}

	$where .= " AND `type` = $uType";

	//性别
	if($uType == 0 && $sType != ""){
		$where .= " AND `sex` = $sType";
	}


	$where .= " order by `id` desc";

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `userid`, `jointime`, `nickname`, `sex`, `photo`, `company`, `state`, `type` FROM `#@__".$tab."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["userid"] = $value["userid"];
			$list[$key]["username"] = $value['nickname'];
			$list[$key]["photo"] = $value['photo'];
			$list[$key]["state"] = $value['state'];
			$list[$key]["sex"] = $value['sex'] == 0 ? "女" : "男";

			//会员信息
			$userSql = $dsql->SetQuery("SELECT `nickname`, `email`, `photo`, `phone` FROM `#@__member` WHERE `id` = ". $value["userid"]);
			$userResult = $dsql->getTypeName($userSql);
			if($userResult){
				$list[$key]["email"] = $userResult[0]['email'];
				$list[$key]["phone"] = $userResult[0]['phone'];
			}else{
				$list[$key]["email"] = '';
				$list[$key]["phone"] = '';
				$list[$key]["del"] = 1;
			}

			//交友目的
			// $itemSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $value["purpose"]);
			// $itemResult = $dsql->getTypeName($itemSql);
			// $list[$key]["purpose"] = $itemResult[0]['typename'];

			if($value['company']){
				$sql = $dsql->SetQuery("SELECT `id`, `nickname` FROM `#@__dating_member` WHERE `id` = ".$value['company']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$parent = array(
						"id" => $ret[0]['company'],
						"nickname" => $ret[0]['nickname']
					);
					$list[$key]["company"] = $parent;
				}
			}else{
				$list[$key]['company'] = 0;
			}

			$list[$key]["pubdate"] = date("Y-m-d H:i:s", $value["jointime"]);

			$param = array(
				"service"     => "dating",
				"template"    => "u",
				"id"          => $value['id']
			);
			if($value['type'] == 1){
				$param['template'] = 'hn_detail';
			}elseif($value['type'] == 2){
				$param['template'] = 'store_detail';
			}
			$list[$key]['url'] = getUrlPath($param);
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "datingMember": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").'}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").'}';
	}
	die;

//新增
}elseif($dopost == "Add"){

	$pagetitle = "新增交友会员";

	//表单提交
	if($submit == "提交"){

		$entrust = 0;

		$jointime = GetMkTime(time());
		$photo = $litpic;
		$cityid = (int)$cityid;
		$fromage = (int)$fromage;
		$toage = (int)$toage;
		$addrid_locktime = (int)$addrid_locktime;
		$marriage = (int)$marriage;
		$child = (int)$child;
		$height = (int)$height;
		$height_locktime = (int)$height_locktime;
		$bodytype = (int)$bodytype;
		$housetag = (int)$housetag;
		$workstatus = (int)$workstatus;
		$income = (int)$income;
		$income_locktime = (int)$income_locktime;
		$education = (int)$education;
		$education_locktime = (int)$education_locktime;
		$smoke = (int)$smoke;
		$drink = (int)$drink;
		$workandrest = (int)$workandrest;
		$cartag = (int)$cartag;
		$dfheight = (int)$dfheight;
		$dtheight = (int)$dtheight;
		$addr = (int)$addr;
		$dmarriage = (int)$dmarriage;
		$dhousetag = (int)$dhousetag;
		$deducation = (int)$deducation;
		$dincome = (int)$dincome;
		$nation = (int)$nation;
		$bodyweight = (int)$bodyweight;
		$looks = (int)$looks;
		$religion = (int)$religion;
		$major = (int)$major;
		$duties = (int)$duties;
		$nature = (int)$nature;
		$industry = (int)$industry;
		$familyrank = (int)$familyrank;
		$parentstatus = (int)$parentstatus;
		$fatherwork = (int)$fatherwork;
		$motherwork = (int)$motherwork;
		$parenteconomy = (int)$parenteconomy;
		$parentinsurance = (int)$parentinsurance;
		$marriagetime = (int)$marriagetime;
		$datetype = (int)$datetype;
		$othervalue = (int)$othervalue;
		$weddingtype = (int)$weddingtype;
		$livetogeparent = (int)$livetogeparent;
		$givebaby = (int)$givebaby;
		$cooking = (int)$cooking;
		$housework = (int)$housework;
		$level = (int)$level;
		$expired = (int)$expired;
		$dfeducation = (int)$dfeducation;
		$dteducation = (int)$dteducation;
		$dfincome = (int)$dfincome;
		$dtincome = (int)$dtincome;
		$dchild = (int)$dchild;
		$my_video_state = (int)$my_video_state;
		$addrid = (int)$addrid;
		$dfage = (int)$dfage;
		$dtage = (int)$dtage;
		$daddr = (int)$daddr;
		$sex = (int)$sex;
		$hometown = (int)$hometown;
		$household = (int)$household;
		$dateswitch = (int)$dateswitch;
		$my_voice_state = (int)$my_voice_state;
		$my_video_state = (int)$my_video_state;
		$case = (int)$case;
		$year = (int)$year;
		if(empty($birthday)){
			$birthday = 0;
		}else{
			$birthday = GetMkTime($birthday);
		}

		$numid = 0;
		$sex_lock = 0;
		$birthday_lock = 0;
		$phoneCheck = 0;
		$like = 0;
		$money = 0;
		$key_buy = 0;
		$lead = 0;
		$leadExpired = 0;
		$visit_circle_date = 0;
		$activedate = $jointime;


		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`cityid`, `type`, `entrust`, `userid`, `fromage`, `toage`, `tags`, `addrid`, `addrid_locktime`, `sign`, `marriage`, `child`, `height`, `height_locktime`, `bodytype`, `housetag`, `workstatus`, `income`, `income_locktime`, `education`, `education_locktime`, `smoke`, `drink`, `workandrest`, `cartag`, `dfage`, `dtage`, `dfheight`, `dtheight`, `daddr`, `dmarriage`, `dhousetag`, `deducation`, `dincome`, `jointime`, `numid`, `nickname`, `sex`, `sex_lock`, `birthday`, `birthday_lock`, `interests`, `hometown`, `household`, `nation`, `zodiac`, `constellation`, `bloodtype`, `bodyweight`, `looks`, `religion`, `school`, `major`, `duties`, `nature`, `industry`, `language`, `familyrank`, `parentstatus`, `fatherwork`, `motherwork`, `parenteconomy`, `parentinsurance`, `marriagetime`, `datetype`, `othervalue`, `weddingtype`, `livetogeparent`, `givebaby`, `cooking`, `housework`, `profile`, `lng`, `lat`, `phone`, `phoneCheck`, `qq`, `wechat`, `tel`, `bus`, `dfeducation`, `dteducation`, `dfincome`, `dtincome`, `dchild`, `dateswitch`, `my_voice`, `my_voice_state`, `my_video`, `my_video_state`, `photo`, `cover`, `level`, `expired`, `state`, `case`, `year`, `company`, `honor`, `advice`, `address`, `like`, `activedate`, `money`, `key`, `key_buy`, `lead`, `leadExpired`, `dayinit`) VALUES ('$cityid', '$type', '$entrust', '$userid', '$fromage', '$toage', '$tags', '$addrid', '$addrid_locktime', '$sign', '$marriage', '$child', '$height', '$height_locktime', '$bodytype', '$housetag', '$workstatus', '$income', '$income_locktime', '$education', '$education_locktime', '$smoke', '$drink', '$workandrest', '$cartag', '$dfage', '$dtage', '$dfheight', '$dtheight', '$daddr', '$dmarriage', '$dhousetag', '$deducation', '$dincome', '$jointime', '$numid', '$nickname', '$sex', '$sex_lock', '$birthday', '$birthday_lock', '$interests', '$hometown', '$household', '$nation', '$zodiac', '$constellation', '$bloodtype', '$bodyweight', '$looks', '$religion', '$school', '$major', '$duties', '$nature', '$industry', '$language', '$familyrank', '$parentstatus', '$fatherwork', '$motherwork', '$parenteconomy', '$parentinsurance', '$marriagetime', '$datetype', '$othervalue', '$weddingtype', '$livetogeparent', '$givebaby', '$cooking', '$housework', '$profile', '$lng', '$lat', '$phone', '$phoneCheck', '$qq', '$wechat', '$tel', '$bus', '$dfeducation', '$dteducation', '$dfincome', '$dtincome', '$dchild', '$dateswitch', '$my_voice', '$my_voice_state', '$my_video', '$my_video_state', '$photo', '$cover', '$level', '$expired', '$state', '$case', '$year', '$company', '$honor', '$advice', '$address', '$like', '$activedate', '$money', '$key', '$key_buy', '$lead', '$leadExpired', '$dayinit')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){

			if($phone){
				$sql = $dsql->SetQuery("SELECT m.`id`, m.`phone` FROM `#@__member` m WHERE m.`id` = $userid");
				$res = $dsql->dsqlOper($sql, "results");
				if($res){
					$id_ = $res[0]['id'];
					$phone_ = $res[0]['phone'];
					if($phone_ != $phone){
						$sql = $dsql->SetQuery("UPDATE `#@__member` SET `phone` = '$phone', `phoneCheck` = 0 WHERE `id` = $id_");
						$dsql->dsqlOper($sql, "update");
					}
				}
			}

			$numid = (int)date('y');
			if($aid < 1000){
				$numid .= substr(time(), -4);
			}
			$numid .= $aid;
			$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `numid` = '$numid' WHERE `id` = $aid");
			$dsql->dsqlOper($sql, "update");

			if($type == "0"){
				$tit = "新增交友用户";
			}elseif($type == "1"){
				$tit = "新增交友红娘";
			}elseif($type == "2"){
				$tit = "新增交友门店";
			}
			adminLog($tit, $userid."=>".$user);

			$param = array(
				"service"     => "dating",
				"template"    => "u",
				"id"          => $aid
			);
			$url = getUrlPath($param);
			echo '{"state": 100, "url": "'.$url.'"}';
		}else{
			echo $aid;
		}
		die;
	}

	$type = (int)$type;
	$huoniaoTag->assign('type', $type);

//修改
}elseif($dopost == "edit"){

	$pagetitle = "修改交友会员信息";

	if($id == "") die('要修改的信息ID传递失败！');
	if($submit == "提交"){

		$photo = $litpic;
		$cityid = (int)$cityid;
		$fromage = (int)$fromage;
		$toage = (int)$toage;
		$addrid_locktime = (int)$addrid_locktime;
		$marriage = (int)$marriage;
		$child = (int)$child;
		$height = (int)$height;
		$height_locktime = (int)$height_locktime;
		$bodytype = (int)$bodytype;
		$housetag = (int)$housetag;
		$workstatus = (int)$workstatus;
		$income = (int)$income;
		$income_locktime = (int)$income_locktime;
		$education = (int)$education;
		$education_locktime = (int)$education_locktime;
		$smoke = (int)$smoke;
		$drink = (int)$drink;
		$workandrest = (int)$workandrest;
		$cartag = (int)$cartag;
		$dfheight = (int)$dfheight;
		$dtheight = (int)$dtheight;
		$addr = (int)$addr;
		$dmarriage = (int)$dmarriage;
		$dhousetag = (int)$dhousetag;
		$deducation = (int)$deducation;
		$dincome = (int)$dincome;
		$nation = (int)$nation;
		$bodyweight = (int)$bodyweight;
		$looks = (int)$looks;
		$religion = (int)$religion;
		$major = (int)$major;
		$duties = (int)$duties;
		$nature = (int)$nature;
		$industry = (int)$industry;
		$familyrank = (int)$familyrank;
		$parentstatus = (int)$parentstatus;
		$fatherwork = (int)$fatherwork;
		$motherwork = (int)$motherwork;
		$parenteconomy = (int)$parenteconomy;
		$parentinsurance = (int)$parentinsurance;
		$marriagetime = (int)$marriagetime;
		$datetype = (int)$datetype;
		$othervalue = (int)$othervalue;
		$weddingtype = (int)$weddingtype;
		$livetogeparent = (int)$livetogeparent;
		$givebaby = (int)$givebaby;
		$cooking = (int)$cooking;
		$housework = (int)$housework;
		$level = (int)$level;
		$expired = (int)$expired;
		$dfeducation = (int)$dfeducation;
		$dteducation = (int)$dteducation;
		$dfincome = (int)$dfincome;
		$dtincome = (int)$dtincome;
		$dchild = (int)$dchild;
		$my_video_state = (int)$my_video_state;
		$addrid = (int)$addrid;
		$dfage = (int)$dfage;
		$dtage = (int)$dtage;
		$daddr = (int)$daddr;
		$sex = (int)$sex;
		$hometown = (int)$hometown;
		$household = (int)$household;
		$dateswitch = (int)$dateswitch;
		$my_voice_state = (int)$my_voice_state;
		$my_video_state = (int)$my_video_state;
		$case = (int)$case;
		$year = (int)$year;
		if(empty($birthday)){
			$birthday = 0;
		}else{
			$birthday = GetMkTime($birthday);
		}

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `cityid` = '$cityid', `userid`='$userid', `photo` = '$photo', `fromage`='$fromage', `toage`='$toage', `tags` = '$tags', `addrid`='$addrid', `marriage`='$marriage', `child`='$child', `height`='$height', `bodytype`='$bodytype', `housetag`='$housetag', `workstatus`='$workstatus', `income`='$income', `education`='$education', `smoke`='$smoke', `drink`='$drink', `workandrest`='$workandrest', `cartag`='$cartag', `dfage`='$dfage', `dtage`='$dtage', `dfheight`='$dfheight', `dtheight`='$dtheight', `daddr`='$daddr', `dmarriage`='$dmarriage', `dhousetag`='$dhousetag', `deducation`='$deducation', `dincome`='$dincome', `nickname` = '$nickname', `sex` = '$sex', `birthday` = '$birthday', `interests` = '$interests', `hometown`='$hometown', `household`='$household', `nation`='$nation', `zodiac`='$zodiac', `constellation`='$constellation', `bloodtype`='$bloodtype', `bodyweight`='$bodyweight', `looks`='$looks', `religion`='$religion', `school`='$school', `major`='$major', `duties`='$duties', `nature`='$nature', `industry`='$industry', `language`='$language', `familyrank`='$familyrank', `parentstatus`='$parentstatus', `fatherwork`='$fatherwork', `motherwork`='$motherwork', `parenteconomy`='$parenteconomy', `parentinsurance`='$parentinsurance', `marriagetime`='$marriagetime', `datetype`='$datetype', `othervalue`='$othervalue', `weddingtype`='$weddingtype', `livetogeparent`='$livetogeparent', `givebaby`='$givebaby', `cooking`='$cooking', `housework`='$housework', `profile`='$profile', `lng`='$lng', `lat`='$lat', `qq`='$qq', `wechat`='$wechat', `tel`='$tel', `bus`='$bus', `dfeducation`='$dfeducation', `dteducation`='$dteducation', `dfincome`='$dfincome', `dtincome`='$dtincome', `dchild`='$dchild', `dateswitch`='$dateswitch', `my_voice_state`='$my_voice_state', `my_video_state`='$my_video_state', `level`='$level', `expired`='$expired', `state`='$state', `case`='$case', `year`='$year',`company`='$company', `honor`='$honor', `advice`='$advice', `address`='$address' WHERE `id` = ".$id);


		$return = $dsql->dsqlOper($archives, "update");

		if($return == "ok"){
			adminLog("修改交友用户", $userid."=>".$user);
			$sql = $dsql->SetQuery("SELECT m.`id`, m.`phone` FROM `#@__dating_member` d LEFT JOIN `#@__member` m ON m.`id` = d.`userid` WHERE d.`id` = $id");
			$res = $dsql->dsqlOper($sql, "results");
			if($res){
				$id_ = $res[0]['id'];
				$phone_ = $res[0]['phone'];
				if($phone_ != $phone){
					$sql = $dsql->SetQuery("UPDATE `#@__member` SET `phone` = '$phone', `phoneCheck` = 0 WHERE `id` = $id_");
					$dsql->dsqlOper($sql, "update");
				}
			}

			$param = array(
				"service"     => "dating",
				"template"    => "u",
				"id"          => $id
			);
			$url = getUrlPath($param);
			echo '{"state": 100, "url": "'.$url.'"}';
		}else{
			// echo $archives;die;
			echo $return;
		}
		die;

	}else{
		if(!empty($id)){

			//主表信息
			$archives = $dsql->SetQuery("SELECT d.*, m.`phone` mphone FROM `#@__".$tab."` d LEFT JOIN `#@__member` m ON m.`id` = d.`userid` WHERE d.`id` = ".$id);
			$results = $dsql->dsqlOper($archives, "results");

			if(!empty($results)){

				foreach ($results[0] as $key => $value) {
					if($key == "my_voice" && $value){
						$my_voice_turl = getFilePath($value);
						$huoniaoTag->assign('my_voice_turl', $my_voice_turl);
					}
					if($key == "company" && $value){
						$sql = $dsql->SetQuery("SELECT `nickname` FROM `#@__dating_member` WHERE `id` = $value");
						$ret = $dsql->dsqlOper($sql, "results");
						if($ret){
							$companyName = $ret[0]['nickname'];
							$huoniaoTag->assign('companyName', $companyName);
						}else{
							$value = 0;
						}
					}
					${$key} = $value;
					$huoniaoTag->assign($key, $value);
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

//删除会员
}elseif($dopost == "del"){
	$each = explode(",", $id);
	$error = array();
	if($id != ""){
		foreach($each as $val){
			$archives = $dsql->SetQuery("SELECT `userid`, `type`, `photo`, `cover` FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$userid = $results[0]['userid'];
				$type = $results[0]['type'];
				$photo = $results[0]['photo'];
				$cover = $results[0]['cover'];

				if($photo){
					delPicFile($photo, "delAtlas", "dating");
				}
				if($cover){
					delPicFile($cover, "delAtlas", "dating");
				}

				$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."` WHERE `id` = ".$val);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					$error[] = $val;
				}

				//删除私信
				$archives = $dsql->SetQuery("DELETE FROM `#@__dating_review` WHERE `ufrom` = ".$val." OR `uto` = ".$val);
				$dsql->dsqlOper($archives, "update");

				//删除相册分类
				$archives = $dsql->SetQuery("DELETE FROM `#@__dating_album_type` WHERE `uid` = ".$val);
				$dsql->dsqlOper($archives, "update");

				//删除相册
				$archives = $dsql->SetQuery("SELECT `id`, `path` FROM `#@__dating_album` WHERE `uid` = ".$val);
				$results = $dsql->dsqlOper($archives, "results");
				if($results){
					foreach($each as $info){
						delPicFile($info['path'], "delAtlas", "dating");

						//删除评论
						$archives = $dsql->SetQuery("DELETE FROM `#@__dating_album_review` WHERE `aid` = ".$info['id']);
						$dsql->dsqlOper($archives, "update");
					}

					$archives = $dsql->SetQuery("DELETE FROM `#@__dating_album` WHERE `uid` = ".$val);
					$dsql->dsqlOper($archives, "update");
				}

				//删除故事
				$archives = $dsql->SetQuery("SELECT `litpic`, `pics` FROM `#@__dating_story` WHERE `fid` = ".$val);
				$results = $dsql->dsqlOper($archives, "results");
				if($results){
					foreach($each as $info){
						delPicFile($info['litpic'], "delThumb", "dating");
						delPicFile($info['pics'], "delAtlas", "dating");
					}

					$archives = $dsql->SetQuery("DELETE FROM `#@__dating_story` WHERE `fid` = ".$val);
					$dsql->dsqlOper($archives, "update");
				}

				//删除人气
				$archives = $dsql->SetQuery("DELETE FROM `#@__dating_visit` WHERE `ufrom` = ".$val." OR `uto` = ".$val);
				$dsql->dsqlOper($archives, "update");

				//删除活动报名
				$archives = $dsql->SetQuery("DELETE FROM `#@__dating_activity_take` WHERE `uid` = ".$val);
				$dsql->dsqlOper($archives, "update");

				//删除提交申请
				$archives = $dsql->SetQuery("DELETE FROM `#@__dating_apply` WHERE `ufrom` = ".$val);
				$dsql->dsqlOper($archives, "update");

				if($type == 0){
					//删除动态
					$archives = $dsql->SetQuery("SELECT `file`, `type` FROM `#@__dating_circle` WHERE `uid` = ".$val);
					$results  = $dsql->dsqlOper($archives, "results");
					if($results){
						foreach ($results as $k => $v) {
							if($v['file']){
								$file = explode(",", $file);
								foreach ($file as $f) {
									if($v['type'] == 1){
										delPicFile($photo, "delAtlas", "dating");
									}elseif($v['type'] == 2){
										delPicFile($photo, "delVideo", "dating");
									}
								}
							}
						}
					}
					$archives = $dsql->SetQuery("DELETE FROM `#@__dating_circle` WHERE `uid` = ".$val);
					$dsql->dsqlOper($archives, "update");

					//删除送礼物
					$archives = $dsql->SetQuery("DELETE FROM `#@__dating_gift_put` WHERE `uid` = ".$val);
					$dsql->dsqlOper($archives, "update");

					//删除牵线
					$archives = $dsql->SetQuery("DELETE FROM `#@__dating_lead` WHERE `ufrom` = ".$val." OR `uto` = ".$val);
					$dsql->dsqlOper($archives, "update");

					//删除订单

				}elseif($type == 1){
					$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `company` = 0 WHERE `company` = ".$val);
					$dsql->dsqlOper($sql, "update");
				}

			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("删除交友会员", $id);
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
	}
	die;

//获取交友兴趣
}elseif($dopost == "getinterests"){
	echo json_encode($dsql->getTypeList(0, "dating_skill"));
	die;

//获取交友标签
}elseif($dopost == "gettags"){
	echo json_encode($dsql->getTypeList(0, "dating_tags"));
	die;

//获取技能
}elseif($dopost == "getgrasp" || $dopost == "getlearn"){
	echo json_encode($dsql->getTypeList(0, "dating_skill"));
	die;
// 匹配用户
}elseif($dopost == "checkUser"){
	$key = $_POST['key'];
	if($type && $key){
		$sql = $dsql->SetQuery("SELECT `id`, `nickname` username FROM `#@__dating_member` WHERE `type` = '$type' AND (`nickname` LIKE '%$key%' || `qq` LIKE '%$key%' || `wechat` LIKE '%$key%')");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			echo json_encode($ret);
			die;
		}else{
			die;
		}
	}
}elseif($dopost == "updateState"){
	$id = (int)$id;
	$state = (int)$_POST['state'];

	$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `state` = $state WHERE `id` = $id");
	$res = $dsql->dsqlOper($sql, "update");
	if($res == "ok"){
		echo '{"state": 100, "info": '.json_encode('操作成功').'}';
	}else{
		echo '{"state": 200, "info": '.json_encode('操作失败').'}';
	}
	die;
}


//验证模板文件
if(file_exists($tpl."/".$templates)){
	include(HUONIAOINC."/config/dating.inc.php");


	$huoniaoTag->registerPlugin("function", 'getItem', 'getItem');

	$huoniaoTag->assign('pagetitle', $pagetitle);
	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('mapCity', $cfg_mapCity);

	$huoniaoTag->assign('tagsLength', (int)$tagsLength);
	$huoniaoTag->assign('graspLength', (int)$graspLength);
	$huoniaoTag->assign('learnLength', (int)$learnLength);
	$huoniaoTag->assign('interestsLength', (int)$graspLength);


	if($dopost == "edit"){
		$huoniaoTag->assign('id', $id);
		$huoniaoTag->assign('userid', $userid);

		//会员
		$userSql = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `id` = ". $userid);
		$userRet = $dsql->dsqlOper($userSql, "results");
		$huoniaoTag->assign('sysUser', $userRet[0]);

		// $tagsSelected = "";
		// if(!empty($tags)){
		// 	$tags = explode(",", $tags);
		// 	foreach($tags as $val){
		// 		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_tags` WHERE `id` = $val");
		// 		$results = $dsql->dsqlOper($archives, "results");
		// 		$name = $results ? $results[0]['typename'] : "";
		// 		$tagsSelected .= '<span data-id="'.$val.'">'.$name.'<a href="javascript:;">×</a></span>';
		// 	}
		// }
		// $huoniaoTag->assign('tagsSelected', $tagsSelected);

		$interestsSelected = "";
		if(!empty($interests)){
			$interests = explode(",", $interests);
			foreach($interests as $val){
				$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_skill` WHERE `id` = $val");
				$results = $dsql->dsqlOper($archives, "results");
				$name = $results ? $results[0]['typename'] : "";
				$interestsSelected .= '<span data-id="'.$val.'">'.$name.'<a href="javascript:;">×</a></span>';
			}
		}
		$huoniaoTag->assign('interestsSelected', $interestsSelected);

		// $huoniaoTag->assign('grasp', $grasp);

	}



	// //属性
	// $huoniaoTag->assign('propertyVal',array('r'));
	// $huoniaoTag->assign('propertyList',array('推荐'));
	// $huoniaoTag->assign('property', !empty($property) ? explode(",", $property) : "");

	// 语言
	$language_sel = empty($language) ? array() : explode(",", $language);
	$languageval = $languagelist = array();
	$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_item` WHERE `parentid` = 273 ORDER BY `weight` ASC");
	$results = $dsql->dsqlOper($archives, "results");
	if($results){
		foreach ($results as $key => $value) {
			$languageval[] = $value['id'];
			$languagelist[] = $value['typename'];
		}
	}
	$huoniaoTag->assign('languageval', $languageval);
	$huoniaoTag->assign('languagelist', $languagelist);
	$huoniaoTag->assign('language_sel', $language_sel);



    $huoniaoTag->assign('cityList', json_encode($adminCityArr));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}


function getItem($param = array()){
	extract($param);
	if(empty($id)){
		if(empty($from) || empty($to)){
			return "";
		}
	}
	global $dsql;
	$def = empty($def) ? "请选择" : $def;
	$html = array();
	$html[] = '<option value="0">'.$def.'</option>';
	if(empty($id)){
		for($i = $from; $i <= $to; $i++){
			$selected = $sel == $i ? ' selected="selected"' : '';
			$html[] = '<option value="'.$i.'"'.$selected.'>'.$i.$txt.'</option>';
		}
	}else{
		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_item` WHERE `parentid` = $id ORDER BY `weight` ASC");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				$selected = $sel == $value['id'] ? ' selected="selected"' : '';
				$html[] = '<option value="'.$value['id'].'"'.$selected.'>'.$value['typename'].'</option>';
			}
		}
	}

	return join("", $html);
}