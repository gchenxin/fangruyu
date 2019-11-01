<?php
/**
 * 交友会员照片评论管理
 *
 * @version        $Id: datingAlbumCommon.php 2014-7-23 下午22:14:21 $
 * @package        HuoNiao.Dating
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("datingAlbum");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
if(empty($aid)) die('数据传递失败！');

$tab = "dating_album_review";
$templates = "datingAlbumCommon.html";

//js
$jsFile = array(
	'ui/bootstrap.min.js',
	'ui/jquery-ui-selectable.js',
	'admin/dating/datingAlbumCommon.js'
);
$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

$pagetitle = "交友会员照片评论管理";

//列表
if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;
	
	$where = "";
	if($sKeyword != ""){
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE (`username` like '%$sKeyword%' OR `email` like '%$sKeyword%')");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			$userid = array();
			foreach($userResult as $key => $user){
				array_push($userid, $user['id']);
			}
			if(!empty($userid)){
				$where .= " AND (`content` like '%".$sKeyword."%' OR `uid` in (".join(",", $userid)."))";
			}
		}else{
			$where .= " AND `content` like '%".$sKeyword."%'";
		}
	}
	
	$where .= " order by `id` desc";
	
	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `type` = 0 AND `aid` = ".$aid);
	
	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);
	
	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `uid`, `type`, `content`, `pubdate` FROM `#@__".$tab."` WHERE `type` = 0 AND `aid` = ".$aid.$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["uid"] = $value["uid"];
			
			//会员信息
			$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $value["uid"]);
			$userResult = $dsql->getTypeName($userSql);
			$list[$key]["username"] = $userResult[0]['username'];			
			
			$list[$key]["type"] = $value["type"];
			$list[$key]["content"] = $value["content"];
			$list[$key]["pubdate"] = date("Y-m-d H:i:s", $value["pubdate"]);
		}
		
		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "datingAlbumCommon": '.json_encode($list).'}';
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
			adminLog("删除交友会员照片评论", $id);
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
	}
	die;

}

//验证模板文件
if(file_exists($tpl."/".$templates)){
	
	require_once(HUONIAOINC."/config/dating.inc.php");
	$huoniaoTag->assign('pagetitle', $pagetitle);
	$huoniaoTag->assign('aid', $aid);
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}