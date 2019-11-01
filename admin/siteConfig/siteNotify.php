<?php
/**
 * 消息通知配置
 *
 * @version        $Id: siteNotify.php 2017-3-7 下午14:12:20 $
 * @package        HuoNiao.Config
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("siteNotify");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/siteConfig";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "siteNotify.html";

$action = "site_notify";

//列表
if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	if($sKeyword != ""){
		$where .= " AND `title` like '%$sKeyword%'";
	}

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$action."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);

	$where .= " order by `id` asc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `title`, `email_state`, `sms_state`, `wechat_state`, `site_state`, `app_state`, `state`, `system` FROM `#@__".$action."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["title"] = $value["title"];
			$list[$key]["email_state"] = $value["email_state"];
			$list[$key]["sms_state"] = $value["sms_state"];
			$list[$key]["wechat_state"] = $value["wechat_state"];
			$list[$key]["site_state"] = $value["site_state"];
			$list[$key]["app_state"] = $value["app_state"];
			$list[$key]["state"] = $value["state"];
			$list[$key]["system"] = $value["system"];
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "list": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}}';
	}
	die;

//更新通知方式
}elseif($dopost == "updateMode"){

	if($id && $type){

		$sql = $dsql->SetQuery("UPDATE `#@__".$action."` SET `".$type."` = $val WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}else{
			echo '{"state": 200, "info": '.json_encode("修改失败！").'}';
		}

	}else{
		echo '{"state": 200, "info": '.json_encode("信息传递失败！").'}';
	}
	die;

//更新状态
}elseif($dopost == "updateState"){

	if($id){

		$sql = $dsql->SetQuery("UPDATE `#@__".$action."` SET `state` = $val WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}else{
			echo '{"state": 200, "info": '.json_encode("修改失败！").'}';
		}

	}else{
		echo '{"state": 200, "info": '.json_encode("信息传递失败！").'}';
	}
	die;

//删除
}elseif($dopost == "del"){
	$archives = $dsql->SetQuery("DELETE FROM `#@__".$action."` WHERE `system` = 2 AND `id` = ".$id);
	$results = $dsql->dsqlOper($archives, "update");
	if($results != "ok"){
		echo '{"state": 200, "info": '.json_encode($error).'}';
	}else{
		adminLog("删除消息通知模板", $id);
		echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
	}
	die;
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'admin/siteConfig/siteNotify.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/siteConfig";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
