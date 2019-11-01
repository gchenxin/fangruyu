<?php
/**
 * 分销商管理
 *
 * @version        $Id: fenxiaoUser.php 2014-11-15 上午10:03:17 $
 * @package        HuoNiao.Member
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("fenxiaoUser");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/member";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "fenxiaoUser.html";

if($action == "getList"){

	$where = "";

	if($pid){
		// $where .= " AND m.`from_uid` = $pid";
	}

	//城市管理员，只能管理管辖城市的会员
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
			if($adminAreaIDs){
				$where .= " AND m.`addr` in ($adminAreaIDs)";
			}else{
				$where .= " AND 1 = 2";
			}
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
			$where .= " AND m.`addr` in ($cityAreaIDs)";
		}else{
			$where .= " 3 = 4";
		}
	}

	if($sKeyword){
		if((int)$sKeyword){
			$uid = " || m.`id` = ".(int)$sKeyword;
		}else{
			$uid = "";
		}
		$where .= " AND (m.`username` LIKE '%$sKeyword%' || m.`nickname` LIKE '%$sKeyword%' || m.`email` LIKE '%$sKeyword%' || m.`phone` LIKE '%$sKeyword%'".$uid.")";
	}

	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;


	$list = array();

	//待审核
	$archives = $dsql->SetQuery("SELECT COUNT(f.`id`) total FROM `#@__member_fenxiao_user` f LEFT JOIN `#@__member` m  ON m.`id` = f.`uid` WHERE f.`state` = 0".$where);
	$result = $dsql->dsqlOper($archives, "results");
	$totalGray = $result[0]['total'];
	// 已审核
	$archives = $dsql->SetQuery("SELECT COUNT(f.`id`) total FROM `#@__member_fenxiao_user` f LEFT JOIN `#@__member` m  ON m.`id` = f.`uid` WHERE f.`state` = 1".$where);
	$result = $dsql->dsqlOper($archives, "results");
	$totalAudit = $result[0]['total'];
	// 审核拒绝
	$archives = $dsql->SetQuery("SELECT COUNT(f.`id`) total FROM `#@__member_fenxiao_user` f LEFT JOIN `#@__member` m  ON m.`id` = f.`uid` WHERE f.`state` = 2".$where);
	$result = $dsql->dsqlOper($archives, "results");
	$totalRefuse = $result[0]['total'];

	
	if($state != ""){
		$where .= " AND f.`state` = $state";
	}
	$archives = $dsql->SetQuery("SELECT COUNT(f.`id`) total FROM `#@__member_fenxiao_user` f LEFT JOIN `#@__member` m  ON m.`id` = f.`uid` WHERE 1 = 1".$where);
	//总条数
	$result = $dsql->dsqlOper($archives, "results");
	$totalCount = $result[0]['total'];
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);

	$atpage = $pagestep*($page-1);
	$limit = " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT f.`id`, f.`uid`, m.`mtype`, m.`username`, m.`nickname`, m.`realname`, m.`from_uid`, m2.`username` recuser, m2.`mtype` from_mtype, f.`state`, f.`pubdate`, f.`phone` FROM `#@__member_fenxiao_user` f LEFT JOIN `#@__member` m  ON m.`id` = f.`uid` LEFT JOIN `#@__member` m2  ON m2.`id` = m.`from_uid` WHERE 1 = 1".$where);
	$order = " ORDER BY f.`id` DESC";
	$results  = $dsql->dsqlOper($archives.$order.$limit, "results");

	if($results){
		// print_r($results);die;
		foreach ($results as $key => $value) {
			$list[$key]['id'] = $value['id'];
			$list[$key]['userid'] = $value['uid'];
			$list[$key]['mtype'] = $value['mtype'];
			$list[$key]['username'] = $value['username'];
			$list[$key]['nickname'] = $value['nickname'];
			$list[$key]['realname'] = $value['realname'];
			$list[$key]['state'] = (int)$value['state'];
			$list[$key]['pubdate'] = $value['pubdate'];
			$list[$key]['phone'] = $value['phone'];
			$list[$key]['from_uid'] = (int)$value['from_uid'];
			$list[$key]['from_mtype'] = (int)$value['from_mtype'];
			$list[$key]['recuser'] = $value['recuser'];

			$sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__member_fenxiao` WHERE `uid` = ".$value['uid']);
			$res = $dsql->dsqlOper($sql, "results");
			$list[$key]['amount'] = $res[0]['total'] ? $res[0]['total'] : '0.00';

			$sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member` WHERE `from_uid` = ".$value['uid']);
			$res = $dsql->dsqlOper($sql, "results");
			$list[$key]['child'] = $res[0]['total'] ? (int)$res[0]['total'] : 0;

		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}, "fenxiaoUser": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}, "info": '.json_encode("暂无相关信息").'}';
		}
	}else{
		echo '{"state": 101, "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "info": '.json_encode("暂无相关信息").'}';
	}
	die;

}

if($action == 'updateState'){
	if (!testPurview("fenxiaoUser")) {
	    die('{"state": 200, "info": ' . json_encode("对不起，您无权使用此功能！") . '}');
	};
	$each = explode(",", $id);
	$error = array();

	global $handler;
	$handler = true;

	if ($id != "") {

	    foreach ($each as $val) {

	        //更新记录状态
	        $archives = $dsql->SetQuery("UPDATE `#@__member_fenxiao_user` SET `state` = $arcrank WHERE `id` = " . $val);
	        $results = $dsql->dsqlOper($archives, "update");
	        if ($results != "ok") {
	            $error[] = $val;
	        }

	    }
	    if (!empty($error)) {
	        echo '{"state": 200, "info": ' . json_encode($error) . '}';
	    } else {
	        adminLog("更新分销商状态", $id . "=>" . $arcrank);
	        echo '{"state": 100, "info": ' . json_encode("修改成功！") . '}';
	    }
	}
	die;
}elseif($action == 'del'){
	if(empty($id)) die('{"state": 200, "info": ' . json_encode("您没有选择任何信息！") . '}');
	if (!testPurview("fenxiaoUser")) {
	    die('{"state": 200, "info": ' . json_encode("对不起，您无权使用此功能！") . '}');
	};
	$sql = $dsql->SetQuery("DELETE FROM `#@__member_fenxiao_user` WHERE `id` IN (".$id.")");
	$res = $dsql->dsqlOper($sql, "update");
	if($res == 'ok'){
		die('{"state": 100, "info": ' . json_encode("操作成功") . '}');
	}else{
		die('{"state": 200, "info": ' . json_encode("操作失败，请重试！") . '}');
	}
}elseif($action == "add"){
	if(empty($id)) die('{"state": 200, "info": ' . json_encode("请输入用户id") . '}');
	$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_fenxiao_user` WHERE `uid` = $id");
	$res = $dsql->dsqlOper($sql, "results");
	if($res){
		die('{"state": 200, "info": ' . json_encode("用户已经是分销商") . '}');
	}
	$sql = $dsql->SetQuery("SELECT `id`, `mtype`, `state`, `phone` FROM `#@__member` WHERE `id` = $id");
	$res = $dsql->dsqlOper($sql, "results");
	if($res){
		if($res[0]['mtype'] != 1 && $res[0]['mtype'] != 2) die('{"state": 200, "info": ' . json_encode("用户所在组无法进行此操作") . '}');
		if($res[0]['state'] != 1) die('{"state": 200, "info": ' . json_encode("用户状态异常") . '}');
		$pubdate = time();
		$phone = $res[0]['phone'];
		$sql = $dsql->SetQuery("INSERT INTO `#@__member_fenxiao_user` (`uid`, `phone`, `state`, `pubdate`) VALUES ($id, '$phone', 1, $pubdate)");
		$res = $dsql->dsqlOper($sql, "lastid");
		if($res && is_numeric($res)){
			die('{"state": 100, "info": ' . json_encode("添加成功") . '}');
		}else{
			die('{"state": 200, "info": ' . json_encode("添加失败，请重试") . '}');
		}
	}else{
		die('{"state": 200, "info": ' . json_encode("用户不存在") . '}');
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
		'ui/bootstrap.min.js',
		'ui/chosen.jquery.min.js',
		'ui/jquery-ui-selectable.js',
		'admin/member/fenxiaoUser.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
	$huoniaoTag->assign('cityArr', $userLogin->getAdminCity());

	$huoniaoTag->assign('notice', $notice);
	$huoniaoTag->assign('pid', (int)$pid);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/member";  //设置编译目录
	$huoniaoTag->display($templates);

}else{
	echo $templates."模板文件未找到！";
}
