<?php
/**
 * 管理商家入驻订单
 *
 * @version        $Id: businessOrder.php 2017-08-08 上午11:35:20 $
 * @package        HuoNiao.Business
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("businessList");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/business";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "businessOrder.html";

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

    //城市管理员
    $where = " AND l.`cityid` in ($adminCityIds)";

    if ($cityid) {
        $where = " AND l.`cityid` = $adminCity";
    }

	if($sKeyword != ""){

		$sidArr = array();
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` like '%$sKeyword%' OR `nickname` like '%$sKeyword%' OR `phone` like '%$sKeyword%' OR `company` like '%$sKeyword%'");
		$results = $dsql->dsqlOper($userSql, "results");
		foreach ($results as $key => $value) {
			$sidArr[$key] = $value['id'];
		}

		$where_ = "";
		if(!empty($sidArr)){
			$where_ .= " AND (`title` like '%$sKeyword%' OR `company` like '%$sKeyword%' OR `phone` like '%$sKeyword%' OR `address` like '%$sKeyword%' OR `tel` like '%$sKeyword%' OR `uid` in (".join(",",$sidArr)."))";
		}else{
			$where_ .= " AND (`title` like '%$sKeyword%' OR `company` like '%$sKeyword%' OR `phone` like '%$sKeyword%' OR `address` like '%$sKeyword%' OR `tel` like '%$sKeyword%')";
		}

		$bids = array();
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE 1 = 1".$where_);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			foreach ($ret as $key => $value) {
				$bids[$key] = $value['id'];
			}
		}

		if($bids){
			$where .= " AND (o.`bid` in (".join(",", $bids).") OR o.`ordernum` like '%$sKeyword%')";
		}else{
			$where .= " AND o.`ordernum` like '%$sKeyword%'";
		}

	}

	if($start != ""){
		$where .= " AND o.`date` >= ". GetMkTime($start);
	}

	if($end != ""){
		$where .= " AND o.`date` <= ". GetMkTime($end . " 23:59:59");
	}

	$archives = $dsql->SetQuery("SELECT o.`id` FROM `#@__business_order` o LEFT JOIN `#@__business_list` l ON l.`id` = o.`bid` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);

	$where .= " ORDER BY `id` DESC";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT o.* FROM `#@__business_order` o LEFT JOIN `#@__business_list` l ON l.`id` = o.`bid` WHERE o.`paytype` != 'none'".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		$i = 0;
		foreach ($results as $key=>$value) {
			$list[$i]["id"] = $value["id"];
			$list[$i]["bid"] = $value["bid"];

			$title = $company = "";
			$sql = $dsql->SetQuery("SELECT `title`, `company` FROM `#@__business_list` WHERE `id` = ".$value['bid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$title = $ret[0]['title'];
				$company = $ret[0]['company'];
			}
			$list[$i]['title'] = $title;
			$list[$i]['company'] = $company;

			$list[$i]["ordernum"] = $value['ordernum'];
			$list[$i]["totalprice"] = $value["totalprice"];
			$list[$i]["offer"] = $value["offer"];
			$list[$i]["balance"] = $value['balance'];
			$list[$i]["paytype"] = $value['paytype'];
			$list[$i]["amount"] = $value['amount'];
			$list[$i]["date"] = date("Y-m-d H:i:s", $value["date"]);

			//查询开通的模块
			$modules = array();
			$sql = $dsql->SetQuery("SELECT `module`, `unitprice`, `count`, `expired` FROM `#@__business_order_module` WHERE `oid` = " . $value['id']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				foreach ($ret as $k => $v) {
					array_push($modules, array(
						"name" => getModuleTitle(array("name" => $v["module"])),
						"unitprice" => $v["unitprice"],
						"count" => $v["count"],
						"expired" => date("Y-m-d H:i:s", $v["expired"])
					));
				}
			}
			$list[$i]["modules"] = $modules;

			$i++;
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
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/bootstrap-datetimepicker.min.js',
		'admin/business/businessOrder.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/business";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
