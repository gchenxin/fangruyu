<?php
/**
 * 用户管理
 *
 * @version        $Id: memberList.php 2014-11-15 上午10:03:17 $
 * @package        HuoNiao.Member
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("memberList");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/member";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$db = "member";

//城市管理员，只能管理管辖城市的会员
$adminAreaIDs = '';
if($userType == 3){
    $sql = $dsql->SetQuery("SELECT `mgroupid` FROM `#@__member` WHERE `id` = " . $userLogin->getUserID());
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        $adminCityID = $ret[0]['mgroupid'];

        global $data;
        $data = '';
        $adminAreaData = $dsql->getTypeList($adminCityID, 'site_area');
        $adminAreaIDArr = parent_foreach($adminAreaData, 'id');
        $adminAreaIDs = join(',', $adminAreaIDArr);
    }
}

//css
$cssFile = array(
  'ui/jquery.chosen.css',
  'admin/chosen.min.css'
);
$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));

if($dopost == "Add"){

	$templates = "memberAdd.html";

	//js
	$jsFile = array(
		'ui/bootstrap-datetimepicker.min.js',
		'ui/jquery.dragsort-0.5.1.min.js',
		'publicUpload.js',
		'publicAddr.js',
		'ui/chosen.jquery.min.js',
		'admin/member/memberAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

}elseif($dopost == "Edit"){

	$templates = "memberEdit.html";

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/bootstrap-datetimepicker.min.js',
		'ui/jquery.dragsort-0.5.1.min.js',
		'publicUpload.js',
		'publicAddr.js',
		'ui/chosen.jquery.min.js',
		'admin/member/memberEdit.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

}else{

	$templates = "memberList.html";

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/bootstrap-datetimepicker.min.js',
		'ui/jquery-ui-selectable.js',
		'ui/chosen.jquery.min.js',
		'admin/member/memberList.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

}


if($submit == "提交"){
	if($token == "") die('token传递失败！');

	//二次验证新增会员
	if($dopost == "Add"){

		//验证用户名
		if(empty($username)){
			die('{"state": 200, "info": "请输入用户名"}');
		}
		preg_match("/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]{1,15}$/iu", $username, $matchUsername);
		if(!$matchUsername){
			die('{"state": 200, "info": "用户名格式有误"}');
		}
		if(!checkMember($username)){
			die('{"state": 200, "info": "用户名已存在"}');
		}

		//验证密码
		if(empty($password)){
			die('{"state": 200, "info": "请输入密码"}');
		}
		preg_match('/^.{5,}$/', $password, $matchPassword);
		if(!$matchPassword){
			die('{"state": 200, "info": "密码格式有误"}');
		}

		//真实姓名
		if(empty($nickname)){
			die('{"state": 200, "info": "请输入真实姓名"}');
		}

		//邮箱
		if(!empty($email)){
			preg_match('/\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/', $email, $matchEmail);
			if(!$matchEmail){
				die('{"state": 200, "info": "邮箱格式有误"}');
			}

			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$db."` WHERE `email` = '$email'");
			$return = $dsql->dsqlOper($archives, "results");
			if($return){
				die('{"state": 200, "info": "此邮箱已被注册"}');
			}
		}

		//手机
		if(!empty($phone)){

			// preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $phone, $matchPhone);
			// if(!$matchPhone){
			// 	die('{"state": 200, "info": "手机格式有误"}');
			// }

			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$db."` WHERE `phone` = '$phone'");
			$return = $dsql->dsqlOper($archives, "results");
			if($return){
				die('{"state": 200, "info": "此手机号已被注册"}');
			}

		}

		if($mtype == 2){
			if(empty($company)){
				die('{"state": 200, "info": "请输入公司名称"}');
			}
		}

	//二次验证修改会员
	}elseif($dopost == "Edit"){
		//验证密码
		if(!empty($password)){
			preg_match('/^.{5,}$/', $password, $matchPassword);
			if(!$matchPassword){
				die('{"state": 200, "info": "密码格式有误"}');
			}
		}

		//打折卡号
		if(empty($discount)){
			//die('{"state": 200, "info": "请输入会员打折卡号"}');
		}

		//真实姓名
		if(empty($nickname)){
			die('{"state": 200, "info": "请输入真实姓名"}');
		}

		//邮箱
		if(!empty($email)){
			preg_match('/\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/', $email, $matchEmail);
			if(!$matchEmail){
				die('{"state": 200, "info": "邮箱格式有误"}');
			}

			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$db."` WHERE `email` = '$email' AND `id` != ".$id);
			$return = $dsql->dsqlOper($archives, "results");
			if($return){
				die('{"state": 200, "info": "此邮箱已被注册"}');
			}
		}

		//手机
		if(!empty($phone)){

			// preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $phone, $matchPhone);
			// if(!$matchPhone){
			// 	die('{"state": 200, "info": "手机格式有误"}');
			// }

			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$db."` WHERE `phone` = '$phone' AND `id` != ".$id);
			$return = $dsql->dsqlOper($archives, "results");
			if($return){
				die('{"state": 200, "info": "此手机号已被注册"}');
			}

		}

		//QQ
		if(empty($qq)){
			//die('{"state": 200, "info": "请输入QQ号码"}');
		}
		preg_match('/[1-9]*[1-9][0-9]*/', $qq, $matchQQ);
		if(!$matchQQ){
			//die('{"state": 200, "info": "QQ号码格式有误"}');
		}

        $freeze = (float)$freeze;

		//头像
		if(empty($photo)){
			//die('{"state": 200, "info": "请上传头像"}');
		}

		if($mtype == 2){
			if(empty($company)){
				die('{"state": 200, "info": "请输入公司名称"}');
			}
			if(empty($addr)){
				//die('{"state": 200, "info": "请选择公司所在区域"}');
			}
			if(empty($address)){
				//die('{"state": 200, "info": "请输入详细地址"}');
			}
		}

	}

}


//删除会员
if($dopost == "del"){
	if(!testPurview("memberDel")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	$each = explode(",", $id);
	$error = array();
	$title = array();
	if($id != ""){
		foreach($each as $val){

			//城市管理员
			if($userType == 3){
				if($adminAreaIDs){
					$archives = $dsql->SetQuery("SELECT * FROM `#@__".$db."` WHERE `addr` in ($adminAreaIDs) AND `id` = ".$val);
					$res = $dsql->dsqlOper($archives, "results");
					array_push($title, $res[0]['username']);

					$archives = $dsql->SetQuery("DELETE FROM `#@__".$db."` WHERE `addr` in ($adminAreaIDs) AND `id` = ".$val);
					$results = $dsql->dsqlOper($archives, "update");
					if($results != "ok"){
						$error[] = $val;
					}
				}
			}else{
				$archives = $dsql->SetQuery("SELECT * FROM `#@__".$db."` WHERE `id` = ".$val);
				$res = $dsql->dsqlOper($archives, "results");
				array_push($title, $res[0]['username']);

				$archives = $dsql->SetQuery("DELETE FROM `#@__".$db."` WHERE `id` = ".$val);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					$error[] = $val;
				}
			}

			if($res){
				//删除头像、营业执照
				delPicFile($res[0]['photo'], "delPhoto", "siteConfig");
				delPicFile($res[0]['license'], "delCard", "siteConfig");

				$archives = $dsql->SetQuery("DELETE FROM `#@__".$db."_money` WHERE `userid` = ".$val);
				$dsql->dsqlOper($archives, "update");

				$archives = $dsql->SetQuery("DELETE FROM `#@__".$db."_point` WHERE `userid` = ".$val);
				$dsql->dsqlOper($archives, "update");

				//同步删除论坛会员
				// $userLogin->bbsSync($res[0]['username'], "delete");
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("删除会员", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
	}
	die;

//更新状态
}else if($action == "updateState"){
	if(!testPurview("memberEdit")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	$each = explode(",", $id);
	$error = array();
	if($id != ""){
		foreach($each as $val){

			//验证权限
			if($userType == 3){
				$archives = $dsql->SetQuery("UPDATE `#@__".$db."` SET `state` = $arcrank WHERE `addr` in ($adminAreaIDs) AND `id` = ".$val);
			}else{
				$archives = $dsql->SetQuery("UPDATE `#@__".$db."` SET `state` = $arcrank WHERE `id` = ".$val);
			}
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}

		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("更新会员状态", $id."=>".$arcrank);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
	}
	die;

//获取会员列表
}else if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	//管理员
	$where = " AND `mgroupid` = 0";

	//城市管理员
	if($userType == 3){
		if($adminAreaIDs){
			$where .= " AND `addr` in ($adminAreaIDs)";
		}else{
			$where .= " AND 1 = 2";
		}
	}

	//城市
	if($cityid){
		global $data;
		$data = '';
		$cityAreaData = $dsql->getTypeList($cityid, 'site_area');
		$cityAreaIDArr = parent_foreach($cityAreaData, 'id');
		$cityAreaIDs = join(',', $cityAreaIDArr);
		if($cityAreaIDs){
			$where .= " AND `addr` in ($cityAreaIDs)";
		}else{
			$where .= " 3 = 4";
		}
	}

	if($sKeyword != ""){
		$where .= " AND (`username` like '%$sKeyword%' OR `discount` like '%$sKeyword%' OR `nickname` like '%$sKeyword%' OR `realname` like '%$sKeyword%' OR `idcard` like '%$sKeyword%' OR `email` like '%$sKeyword%' OR `phone` like '%$sKeyword%' OR `regip` like '%$sKeyword%' OR `company` like '%$sKeyword%')";
	}

	if($mtype != ""){
		$where .= " AND `mtype` = ".$mtype;
	}

	if((!empty($level) || $level == 0) && $level != ""){
		$where .= " AND `level` = ".$level;
	}

	if($start != ""){
		$where .= " AND `regtime` >= ". GetMkTime($start);
	}

	if($end != ""){
		$where .= " AND `regtime` <= ". GetMkTime($end . " 23:59:59");
	}

	if($pend !== ""){
		if($pend == 1){
			$pending = " AND `certifyState` = 3";
		}elseif($pend == 2){
			$pending = " AND `licenseState` = 3";
		}else{
			$pending = " AND (`certifyState` = 3 OR `licenseState` = 3)";
		}
	}


	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$db."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);
	//未审核
	$totalGray = $dsql->dsqlOper($archives." AND `state` = 0".$pending.$where, "totalCount");
	//正常
	$normal = $dsql->dsqlOper($archives." AND `state` = 1".$pending.$where, "totalCount");
	//锁定
	$lock = $dsql->dsqlOper($archives." AND `state` = 2".$pending.$where, "totalCount");
	//全部待办事项
	$totalPend = $dsql->dsqlOper($archives." AND (`certifyState` = 3 OR `licenseState` = 3)".$where, "totalCount");
	//个人实名待认证
	$pendPerson = $dsql->dsqlOper($archives." AND `certifyState` = 3".$where, "totalCount");
	//公司认证
	$pendCompany = $dsql->dsqlOper($archives." AND `licenseState` = 3".$where, "totalCount");

	if($state != ""){
		$where .= " AND `state` = $state";

		if($state == 0){
			$totalPage = ceil($totalGray/$pagestep);
		}elseif($state == 1){
			$totalPage = ceil($normal/$pagestep);
		}elseif($state == 2){
			$totalPage = ceil($lock/$pagestep);
		}
	}

	if(!empty($pending)){
		$where .= $pending;
		if($pend == 1){
			$totalPage = ceil($pendPerson/$pagestep);
		}elseif($pend == 2){
			$totalPage = ceil($pendCompany/$pagestep);
		}else{
			$totalPage = ceil($totalPend/$pagestep);
		}
	}

	$where .= " order by `id` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `mtype`, `username`, `level`, `expired`, `discount`, `nickname`, `realname`, `certifyState`, `email`, `emailCheck`, `phone`, `phoneCheck`, `company`, `licenseState`, `photo`, `sex`, `money`, `promotion`, `point`, `regtime`, `regip`, `lastlogintime`, `lastloginip`, `state`, `idcard`, `addr`, `address`, `freeze` FROM `#@__".$db."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	$list = array();

	if($results && is_array($results)){
		foreach ($results as $key=>$value) {
			$list[$key]["id"]         = $value["id"];
			$list[$key]["mtype"]      = $value["mtype"];
			$list[$key]["username"]   = $value["username"];

			$level = "";
			$sql = $dsql->SetQuery("SELECT `name` FROM `#@__member_level` WHERE `id` = " . $value['level']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$level = $ret[0]['name'];
			}
			$list[$key]["level"] = $level;
			$list[$key]["expired"]    = $value["expired"];

			$list[$key]["discount"]   = $value["discount"];
			$list[$key]["nickname"]   = $value["nickname"];
			$list[$key]["realname"]   = $value["realname"];
			$list[$key]["idcard"]     = $value["idcard"];
			$list[$key]["certifyState"] = $value["certifyState"];
			$list[$key]["email"]      = $value["email"];
			$list[$key]["emailCheck"] = $value["emailCheck"];
			$list[$key]["phone"]      = $value["phone"];
			$list[$key]["phoneCheck"] = $value["phoneCheck"];
			$list[$key]["company"]    = $value["company"];
			$list[$key]["licenseState"] = $value["licenseState"];
			$list[$key]["photo"]      = $value["photo"];
			$list[$key]["sex"]        = $value["sex"] ? "男" : "女";
			$list[$key]["money"]      = $value["money"];
			$list[$key]["promotion"]  = $value["promotion"];
			$list[$key]["point"]      = $value["point"];
			$list[$key]["regtime"]    = date("Y-m-d H:i:s", $value["regtime"]);
			$list[$key]["regip"]      = $value["regip"];
			$list[$key]["lastlogintime"]= empty($value["lastlogintime"]) ? "还未登录" : date("Y-m-d H:i:s", $value["lastlogintime"]);
			$list[$key]["lastloginip"]= $value["lastloginip"];
			$list[$key]["state"]      = $value["state"];
			$list[$key]["addr"]       = $value["addr"];
			$list[$key]["address"]    = $value["address"];

			$addrname = $value['addr'];
			if($addrname){
				$addrname = getPublicParentInfo(array('tab' => 'site_area', 'id' => $addrname, 'type' => 'typename', 'split' => ' '));
			}
			$list[$key]["addrname"] = $addrname;
		}
		if(count($list) > 0){
			if($do != "export"){
				echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "normal": '.$normal.', "lock": '.$lock.', "totalPend": '.$totalPend.', "pendPerson": '.$pendPerson.', "pendCompany": '.$pendCompany.'}, "memberList": '.json_encode($list).'}';
			}
		}else{
			if($do != "export"){
				echo '{"state": 101, "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "normal": '.$normal.', "lock": '.$lock.', "totalPend": '.$totalPend.', "pendPerson": '.$pendPerson.', "pendCompany": '.$pendCompany.'}, "info": '.json_encode("暂无相关信息").'}';
			}
		}
	}else{
		if($do != "export"){
			echo '{"state": 101, "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "normal": '.$normal.', "lock": '.$lock.', "totalPend": '.$totalPend.', "pendPerson": '.$pendPerson.', "pendCompany": '.$pendCompany.'}, "info": '.json_encode("暂无相关信息").'}';
		}
	}

	if($do == "export"){

        $tit = array();
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', 'UID'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '用户名'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '用户类型'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '用户等级'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '性别'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '用户昵称'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '真实姓名'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '身份证号'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '实名认证'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '邮箱'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '邮箱认证'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '手机'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '手机认证'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', 'qq'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '出生日期'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '公司名称'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '所在区域'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '联系地址'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '余额'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '积分'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '冻结金额'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '保障金'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '注册时间'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '注册来源'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '注册IP'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '注册IP地址'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '登录次数'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '最后登录时间'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '最后登录IP'));
        array_push($tit, iconv('utf-8', 'gb2312//IGNORE', '最后登录IP地址'));

        $folder = HUONIAOROOT . "/uploads/siteConfig/file/";
        $filePath = $folder."会员数据.csv";
        MkdirAll($folder);
        $file = fopen($filePath, "w");

        //表头
        fputcsv($file, $tit);

        foreach($list as $data){
	      $arr = array();
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['id']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['username']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['mtype'] == 1 ? "个人" : "企业"));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['level']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['sex']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['nickname']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['realname']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', "\t".$data['idcard']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['certifyState'] ? "是" : "否"));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['emial']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['emailCheck'] ? "是" : "否"));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['phone']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['phoneCheck'] ? "是" : "否"));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['qq']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['birthday']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['company']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['addrname']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['address']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['money']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['point']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['freeze']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['promotion']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['regtime']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['from']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['regip']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['regipaddr']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['logincount']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['lastlogintime']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['lastloginip']));
	      array_push($arr, iconv('utf-8', 'gb2312//IGNORE', $data['lastloginipaddr']));

          //写入文件
          fputcsv($file, $arr);
	    }

        header("Content-type:application/octet-stream");
        header("Content-Disposition:attachment;filename = 会员数据.csv");
        header("Accept-ranges:bytes");
        header("Accept-length:".filesize($filePath));
        readfile($filePath);

    }

	die;

//新增
}elseif($dopost == "Add"){
	if(!testPurview("memberAdd")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};

	$pagetitle = "新增会员";

	//表单提交
	if($submit == "提交"){

		$passwd = $userLogin->_getSaltedHash($password);
		$regtime  = GetMkTime(time());
		$regip    = GetIP();

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__".$db."` (`mtype`, `username`, `password`, `nickname`, `email`, `emailCheck`, `phone`, `phoneCheck`, `company`, `addr`, `regtime`, `regip`, `state`, `purviews`) VALUES ('$mtype', '$username', '$passwd', '$nickname', '$email', '1', '$phone', '1', '$company', '$addr', '$regtime', '$regip', '1', '')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if($aid){

			//论坛同步
			$data = array();
			$data['username'] = $username;
			$data['password'] = $password;
			$data['email']    = $email;
			$userLogin->bbsSync($data, "register");

			adminLog("新增会员", $username);
			echo '{"state": 100, "info": '.json_encode("添加成功！").'}';
		}else{
			echo $return;
		}
		die;
	}

	//会员类型
	$huoniaoTag->assign('mtype', array('1', '2'));
	$huoniaoTag->assign('mtypeNames',array('个人','企业'));
	$huoniaoTag->assign('mtypeChecked', 1);

//修改
}elseif($dopost == "Edit"){
	if(!testPurview("memberEdit")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};

	$pagetitle = "修改会员";

	//表单提交
	if($submit == "提交"){
		$archives = $dsql->SetQuery("SELECT * FROM `#@__".$db."` WHERE `id` = ".$id);
		$res = $dsql->dsqlOper($archives, "results");

		//城市管理员验证权限
		if($userType == 3){
			if($adminAreaIDArr){
				if(!in_array($res[0]['addr'], $adminAreaIDArr)){
					die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
				}
			}else{
				die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
			}
		}

		$username = $res[0]['username'];
		$emai     = $res[0]['email'];
		$certifyState_ = $res[0]['certifyState'];
		$licenseState_ = $res[0]['licenseState'];
		$expired_ = $res[0]['expired'];

		$birthday = $birthday ? GetMkTime($birthday) : 0;
		$expired = $expired ? GetMkTime($expired) : 0;
		$passArr = "";

		if(!empty($password)){
			$passwd = $userLogin->_getSaltedHash($password);
			$passArr .= ", `password` = '$passwd'";
		}

		if(!empty($paypwd)){
			$paypwd = $userLogin->_getSaltedHash($paypwd);
			$passArr .= ", `paypwd` = '$paypwd', `paypwdCheck` = 1";
		}

		//如果到期时间有变动，清除已提醒记录
		if($expired != $expired_){
			$expired_notify = ", `expired_notify_day` = 0, `expired_notify_week` = 0, `expired_notify_month` = 0";
		}

		$archives = $dsql->SetQuery("UPDATE `#@__".$db."` SET `mtype` = '$mtype'".$passArr.", `level` = '$level', `expired` = '$expired', `nickname` = '$nickname', `email` = '$email', `emailCheck` = '$emailCheck', `areaCode` = '$areaCode', `phone` = '$phone', `phoneCheck` = '$phoneCheck', `freeze` = '$freeze', `qq` = '$qq', `photo` = '$photo', `sex` = '$sex', `birthday` = '$birthday', `company` = '$company', `realname` = '$realname', `idcard` = '$idcard', `idcardFront` = '$idcardFront', `idcardBack` = '$idcardBack', `certifyState` = '$certifyState', `certifyInfo` = '$certifyInfo', `addr` = '$addr', `address` = '$address', `license` = '$license', `licenseState` = '$licenseState', `licenseInfo` = '$licenseInfo', `state` = '$state', `stateinfo` = '$stateinfo'".$expired_notify." WHERE `id` = ".$id);
		$update = $dsql->dsqlOper($archives, "update");

		if($update == "ok"){
			//同步论坛
			$data = array("username" => $username);
			if(!empty($password)){
				$data['newpw'] = $password;
			}
			if($email != $emai){
				$data['email'] = $email;
			}
			if(!empty($password) || $email != $emai){
				$userLogin->bbsSync($data, "edit");
			}

			//会员中心认证页面链接
			if($mtype == 2){
				$param = array(
					"service"  => "member",
					"template" => "security",
					"doget"    => "shCertify"
				);
			}else{
				$param = array(
					"service"  => "member",
					"type"     => "user",
					"template" => "security",
					"doget"    => "shCertify"
				);
			}

			//会员通知 - 实名认证
			if($certifyState_ != $certifyState){

                //自定义配置
        		$config = array(
        			"email" => $email,
        			"username" => $username,
        			"info" => $certifyState == 1 ? '' : $certifyInfo,
        			"fields" => array(
        				'keyword1' => '认证详情',
        				'keyword2' => '认证结果'
        			)
        		);

				//实名认证通过
				if($certifyState == 1){
					updateMemberNotice($id, "会员-实名认证审核通过", $param, $config);
				}

				//实名认证未通过
				if($certifyState == 2){
					updateMemberNotice($id, "会员-实名认证审核失败", $param, $config);
				}

			}

			//会员通知 - 营业执照认证
			if($licenseState_ != $licenseState){

                //自定义配置
        		$config = array(
        			"email" => $email,
        			"username" => $username,
        			"info" => $licenseState == 1 ? '' : $licenseInfo,
        			"fields" => array(
        				'keyword1' => '认证详情',
        				'keyword2' => '认证结果'
        			)
        		);

				//营业执照认证通过
				if($licenseState == 1){
					updateMemberNotice($id, "会员-营业执照审核通过", $param, $config);
				}

				//营业执照认证未通过
				if($licenseState == 2){
					updateMemberNotice($id, "会员-营业执照审核失败", $param, $config);
				}
			}

			adminLog("修改会员", $id." => ".$username);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}else{
			echo $update;
		}
		die;

	}else{
		if(!empty($id)){

			//主表信息
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$db."` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "results");

			if(!empty($results)){

				//城市管理员验证权限
				if($userType == 3){
					if($adminAreaIDArr){
						if(!in_array($results[0]['addr'], $adminAreaIDArr)){
							ShowMsg('您无权修改此会员信息！', "javascript:;");
							die;
						}
					}else{
						ShowMsg('您无权修改此会员信息！', "javascript:;");
						die;
					}
				}

				global $cfg_photoSize;
				global $cfg_photoType;
				$huoniaoTag->assign('photoSize', $cfg_photoSize);
				$huoniaoTag->assign('photoType', "*.".str_replace("|", ";*.", $cfg_photoType));

				$huoniaoTag->assign('addrListArr', $dsql->getTypeList(0, "site_area", false));

				//登录设备信息
				$sourceclient = unserialize($results[0]['sourceclient']);
				$huoniaoTag->assign('sourceclient', !empty($sourceclient) ? $sourceclient : array());

				//会员类型
				$huoniaoTag->assign('mtype', array('1', '2'));
				$huoniaoTag->assign('mtypeNames',array('个人','企业'));
				$huoniaoTag->assign('mtypeChecked', $results[0]['mtype']);

				$huoniaoTag->assign('id', $results[0]['id']);
				$huoniaoTag->assign('username', $results[0]['username']);

				$huoniaoTag->assign('recid', $results[0]['recid']);
				$sql = $dsql->SetQuery("SELECT * FROM `#@__".$db."` WHERE `id` = ".$results[0]['recid']);
				$memberInfo = $dsql->dsqlOper($sql, "results");
				if($memberInfo){
					$huoniaoTag->assign('recname', $memberInfo[0]['username']);
				}

				$huoniaoTag->assign('money', $results[0]['money']);
				$huoniaoTag->assign('freeze', $results[0]['freeze']);
				$huoniaoTag->assign('point', $results[0]['point']);
				$huoniaoTag->assign('promotion', $results[0]['promotion']);

				$huoniaoTag->assign('level', $results[0]['level']);

				$levelName = "普通会员";
				$sql = $dsql->SetQuery("SELECT `name` FROM `#@__member_level` WHERE `id` = " . $results[0]['level']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$levelName = $ret[0]['name'];
				}
				$huoniaoTag->assign('levelName', $levelName);

				$huoniaoTag->assign('expired', $results[0]['expired']);
				$huoniaoTag->assign('discount', $results[0]['discount']);
				$huoniaoTag->assign('nickname', $results[0]['nickname']);
				$huoniaoTag->assign('email', $results[0]['email']);
				$huoniaoTag->assign('emailCheck', $results[0]['emailCheck']);
				$huoniaoTag->assign('areaCode', $results[0]['areaCode']);
				$huoniaoTag->assign('phone', $results[0]['phone']);
				$huoniaoTag->assign('phoneCheck', $results[0]['phoneCheck']);
				$huoniaoTag->assign('qq', $results[0]['qq']);
				$huoniaoTag->assign('photo', $results[0]['photo']);

				$huoniaoTag->assign('sex', array('1', '0'));
				$huoniaoTag->assign('sexNames',array('男','女'));
				$huoniaoTag->assign('sexChecked', $results[0]['sex']);

				$huoniaoTag->assign('birthday', !empty($results[0]['birthday']) ? date("Y-m-d", $results[0]['birthday']) : "");
				$huoniaoTag->assign('company', $results[0]['company']);
				$huoniaoTag->assign('addr', $results[0]['addr']);

				//区域
				global $data;
				$data = "";
				$addrArr = getParentArr("site_area", $results[0]['addr']);
				if($addrArr){
					$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
					$huoniaoTag->assign('addrName', join(" > ", $addrArr));
				}else{
					$huoniaoTag->assign('addrName', "选择区域");
				}

				$huoniaoTag->assign('address', $results[0]['address']);


				$huoniaoTag->assign('realname', $results[0]['realname']);
				$huoniaoTag->assign('idcard', empty($results[0]['idcard']) ? "" : $results[0]['idcard']);
				$huoniaoTag->assign('idcardFront', $results[0]['idcardFront']);
				$huoniaoTag->assign('idcardBack', $results[0]['idcardBack']);
				$huoniaoTag->assign('certifyInfo', $results[0]['certifyInfo']);
				$huoniaoTag->assign('certifyState', array('0', '3', '1', '2'));
				$huoniaoTag->assign('certifyStateNames',array('未认证','等待认证','已认证','认证失败'));
				$huoniaoTag->assign('certifyStateChecked', $results[0]['certifyState']);


				$huoniaoTag->assign('license', $results[0]['license']);
				$huoniaoTag->assign('licenseState', array('0', '3', '1', '2'));
				$huoniaoTag->assign('licenseStateNames',array('未认证','等待认证','已认证','认证失败'));
				$huoniaoTag->assign('licenseStateChecked', $results[0]['licenseState']);

				$huoniaoTag->assign('licenseInfo', $results[0]['licenseInfo']);
				$huoniaoTag->assign('regtime', !empty($results[0]['regtime']) ? date("Y-m-d H:i:s", $results[0]['regtime']) : "");
				$huoniaoTag->assign('regip', $results[0]['regip']);

				$huoniaoTag->assign('recid', $results[0]['recid']);

				$onlineState = "离线";
				$online = $results[0]['online'];
				if($online > 0){
					global $cfg_onlinetime;
					$now = GetMkTime(time());
					if($now - $online < $cfg_onlinetime * 3600){
						$onlineState = "在线";
					}
				}
				$huoniaoTag->assign('online', $onlineState);

				$huoniaoTag->assign('state', array('0', '1', '2'));
				$huoniaoTag->assign('stateNames',array('未审核','正常','审核拒绝'));
				$huoniaoTag->assign('stateChecked', $results[0]['state']);

				$huoniaoTag->assign('stateinfo', $results[0]['stateinfo']);
				$huoniaoTag->assign('from', !empty($results[0]['from']) ? $results[0]['from'] : '本站');
				$huoniaoTag->assign('logincount', $results[0]['logincount']);
				$huoniaoTag->assign('lastlogintime', !empty($results[0]['lastlogintime']) ? date("Y-m-d H:i:s", $results[0]['lastlogintime']) : "");
				$huoniaoTag->assign('lastloginip', $results[0]['lastloginip']);



			}else{
				ShowMsg('要修改的信息不存在或已删除！', "-1");
				die;
			}

		}else{
			ShowMsg('要修改的信息参数传递失败，请联系管理员！', "-1");
			die;
		}

	}

//获取帐户日志
}elseif($dopost == "amountList"){

    if($type == "money"){
        if(!testPurview("moneyMember")){
            die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
        };
    }else{
        if(!testPurview("jfMember")){
            die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
        };
    }

	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;
	$where = "";

	if(empty($userid) || empty($type)){
		echo '{"state": 101, "info": '.json_encode("格式错误！").'}';
		die;
	}

	//城市管理员
	if($userType == 3){
		if($adminAreaIDs){
			$where .= " AND m.`addr` in ($adminAreaIDs)";
		}else{
			$where .= " AND 1 = 2";
		}
	}

	//会员
	$where .= " AND a.`userid` = ".$userid;

	$archives = $dsql->SetQuery("SELECT a.`id`, a.`type`, a.`amount`, a.`info`, a.`date` FROM `#@__".$db."_".$type."` a LEFT JOIN `#@__member` m ON m.`id` = a.`userid` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);

	$where .= " order by a.`id` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery($archives.$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"]     = $value["id"];
			$list[$key]["type"]   = $value["type"];
			$list[$key]["amount"] = $value["amount"];
			$list[$key]["info"]   = $value["info"];
			$list[$key]["date"]   = date("Y-m-d H:i:s", $value["date"]);
		}
		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "memberList": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "info": '.json_encode("暂无相关信息").'}';
		}
	}else{
		echo '{"state": 101, "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "info": '.json_encode("暂无相关信息").'}';
	}
	die;

//增加帐户操作记录
}elseif($dopost == "operaAmount"){

    if($action == 'money'){
        if(!testPurview("editMoneyMember")){
    		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
    	};
    }else{
        if(!testPurview("editjfMember")){
    		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
    	};
    }

	if(empty($action) || empty($userid) || $type === "" || empty($amount) || empty($info)){
		die('{"state": 200, "info": '.json_encode("请输入完整！").'}');
	}
	$date = GetMkTime(time());

	//验证权限
	if($userType == 3){
		if($adminAreaIDs){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `addr` in ($adminAreaIDs) AND `id` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
			}
		}else{
			die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
		}
	}

	//保存到主表
	$archives = $dsql->SetQuery("INSERT INTO `#@__".$db."_".$action."` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '$type', '$amount', '$info', '$date')");
	$aid = $dsql->dsqlOper($archives, "lastid");

	//更新帐户
	$oper = "+";
	if($type == 0){
		$oper = "-";
	}
	if($action == "money"){
		$archives = $dsql->SetQuery("UPDATE `#@__".$db."` SET `money` = `money` ".$oper." ".$amount." WHERE `id` = ".$userid);
	}else{
		$archives = $dsql->SetQuery("UPDATE `#@__".$db."` SET `point` = `point` ".$oper." ".$amount." WHERE `id` = ".$userid);
	}
	$dsql->dsqlOper($archives, "update");

	$title = $action == "money" ? "余额" : "积分";
	if($aid){

		//查询帐户信息
		$archives = $dsql->SetQuery("SELECT `username`, `mtype`, `money`, `freeze`, `point` FROM `#@__".$db."` WHERE `id` = ".$userid);
		$results = $dsql->dsqlOper($archives, "results");

		//用户名
		$username = $results[0]['username'];
		$mtype    = $results[0]['mtype'];
		$money    = $results[0]['money'];
		$freeze   = $results[0]['freeze'];
		$point    = $results[0]['point'];


		//会员中心交易记录页面链接
		if($action == "money"){
			if($mtype == 2){
				$param = array(
					"service"  => "member",
					"type"     => "user",
					"template" => "record"
				);
			}else{
				$param = array(
					"service"  => "member",
					"type"     => "user",
					"template" => "record"
				);
			}

		//会员中心积分记录页面链接
		}else{
			if($mtype == 2){
				$param = array(
					"service"  => "member",
					"type"     => "user",
					"template" => "point"
				);
			}else{
				$param = array(
					"service"  => "member",
					"type"     => "user",
					"template" => "point"
				);
			}
		}

		//余额
		if($action == "money"){

            //自定义配置
            $config = array(
                "username" => $username,
                "amount" => $oper.$amount,
                "money" => $money,
                "date" => date("Y-m-d H:i:s", $date),
                "info" => $info,
                "fields" => array(
                    'keyword1' => '变动时间',
                    'keyword2' => '变动金额',
                    'keyword3' => '帐户余额'
                )
            );

			updateMemberNotice($userid, "会员-帐户资金变动提醒", $param, $config);

		//积分
		}else{

            //自定义配置
            $config = array(
                "username" => $username,
                "amount" => $oper.$amount,
                "point" => $point,
                "date" => date("Y-m-d H:i:s", $date),
                "info" => $info,
                "fields" => array(
                    'keyword1' => '变动时间',
                    'keyword2' => '变动积分',
                    'keyword3' => '积分余额'
                )
            );

			updateMemberNotice($userid, "会员-积分变动通知", $param, $config);
		}

		adminLog("新增会员帐户".$title."操作记录", $type."=>".$amount."=>".$info);
		echo '{"state": 100, "info": '.json_encode("操作成功！").', "money": '.$results[0]['money'].', "freeze": '.$results[0]['freeze'].', "point": '.$results[0]['point'].'}';
	}else{
		die('{"state": 200, "info": '.json_encode("操作失败！").'}');
	}
	die;

//删除操作记录
}elseif($dopost == "delAmount"){

    if($action == 'money'){
        if(!testPurview("delMoneyMember")){
    		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
    	};
    }else{
        if(!testPurview("deljfMember")){
    		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
    	};
    }

	$title = $type == "money" ? "余额" : "积分";

	//验证权限
	if($userType == 3){
		if($adminAreaIDs){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `addr` in ($adminAreaIDs) AND `id` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
			}
		}else{
			die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
		}
	}

	if($action != ""){
		$archives = $dsql->SetQuery("DELETE FROM `#@__".$db."_".$type."` WHERE `userid` = ".$userid);
		$results = $dsql->dsqlOper($archives, "update");
		if(!$results){
			echo '{"state": 200, "info": "删除失败"}';
		}else{
			adminLog("清空".$title."操作记录", $id);
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
	}else{
		$each = explode(",", $id);
		$error = array();
		if($id != ""){
			foreach($each as $val){
				$archives = $dsql->SetQuery("DELETE FROM `#@__".$db."_".$type."` WHERE `id` = ".$val);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					$error[] = $val;
				}
			}
			if(!empty($error)){
				echo '{"state": 200, "info": '.json_encode($error).'}';
			}else{
				adminLog("删除".$title."操作记录", $id);
				echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
			}
		}
	}
	die;

}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	$sql = $dsql->SetQuery("SELECT `id`, `name` FROM `#@__member_level`");
	$results = $dsql->dsqlOper($sql, "results");
	$levelList = array();
	if($results){
		$levelList = $results;
	}
	$huoniaoTag->assign('levelList', $levelList);

	$huoniaoTag->assign('notice', $notice);
	$huoniaoTag->assign('cityArr', $userLogin->getAdminCity());
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/member";  //设置编译目录
	$huoniaoTag->display($templates);

}else{
	echo $templates."模板文件未找到！";
}
