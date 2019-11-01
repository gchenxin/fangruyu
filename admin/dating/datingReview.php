<?php
/**
 * 交友会员私信管理
 *
 * @version        $Id: datingReview.php 2014-7-21 上午12:09:15 $
 * @package        HuoNiao.Dating
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("datingReview");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$tab = "dating_review_list";
$templates = "datingReview.html";

//js
$jsFile = array(
	'ui/bootstrap.min.js',
	'ui/jquery-ui-selectable.js',
	'admin/dating/datingReview.js'
);
$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

$pagetitle = "交友会员私信管理";

//列表
if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	$where = "";

	if($sKeyword != ""){
		$userSql = $dsql->SetQuery("SELECT d.`id` FROM `#@__member` m LEFT JOIN `#@__dating_member` d ON d.`userid` = m.`id` WHERE (m.`username` like '%$sKeyword%' OR m.`email` like '%$sKeyword%')");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			$userid = array();
			foreach($userResult as $key => $user){
				array_push($userid, $user['id']);
			}
			if(!empty($userid)){
				$where .= " AND (`content` like '%".$sKeyword."%' OR (`from` in (".join(",", $userid).") OR `to` in (".join(",", $userid).")))";
			}else{
				$where .= " AND `content` like '%".$sKeyword."%'";
			}
		}else{
			$where .= " AND `content` like '%".$sKeyword."%'";
		}
	}

	$where .= " order by `id` desc";

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `from`, `to`, `content`, `isread`, `pubdate` FROM `#@__".$tab."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];

			//会员信息
			$userSql = $dsql->SetQuery("SELECT m.`username`, m.`id` FROM `#@__member` m LEFT JOIN `#@__dating_member` d ON d.`userid` = m.`id` WHERE d.`id` = ". $value["from"]);
			$userResult = $dsql->getTypeName($userSql);
			$list[$key]["from"] = $userResult[0]["id"];
			$list[$key]["fromname"] = $userResult[0]['username'];

			$userSql = $dsql->SetQuery("SELECT m.`username`, m.`id` FROM `#@__member` m LEFT JOIN `#@__dating_member` d ON d.`userid` = m.`id` WHERE d.`id` = ". $value["to"]);
			$userResult = $dsql->getTypeName($userSql);
			$list[$key]["to"] = $userResult[0]["id"];
			$list[$key]["toname"] = $userResult[0]['username'];

			$list[$key]["content"] = $value["content"];
			$list[$key]["isread"] = $value["isread"];
			$list[$key]["pubdate"] = date("Y-m-d H:i:s", $value["pubdate"]);
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "datingReview": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").'}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").'}';
	}
	die;

//删除
}elseif($dopost == "del"){
	$each = explode(",", $id);
	$error = array();
	if($id != ""){
		foreach($each as $val){
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("删除交友会员私信", $id);
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
	}
	die;

}

//验证模板文件
if(file_exists($tpl."/".$templates)){
	$huoniaoTag->assign('pagetitle', $pagetitle);
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
