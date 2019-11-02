<?php
/**
 * 管理活动
 *
 * @version        $Id: huodongList.php 2016-12-24 下午13:57:10 $
 * @package        HuoNiao.Huodong
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("huodongList");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/huodong";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "huodongList.html";

$tab = "huodong_list";

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

    $where = " AND `cityid` in (0,$adminCityIds)";

    if ($adminCity) {
        $where = " AND `cityid` = $adminCity";
    }

    $where .= "AND `waitpay` = 0";

    if($sKeyword != ""){
		$where .= " AND (`title` like '%$sKeyword%' OR `ip` like '%$sKeyword%')";
	}
	if($sType != ""){
		if($dsql->getTypeList($sType, "huodong_type")){
			$lower = arr_foreach($dsql->getTypeList($sType, "huodong_type"));
			$lower = $sType.",".join(',',$lower);
		}else{
			$lower = $sType;
		}
		$where .= " AND `typeid` in ($lower)";
	}
	if($sAddr != ""){
		if($dsql->getTypeList($sAddr, "site_area")){
			$lower = arr_foreach($dsql->getTypeList($sAddr, "site_area"));
			$lower = $sAddr.",".join(',',$lower);
		}else{
			$lower = $sAddr;
		}
		$where .= " AND `addrid` in ($lower)";
	}

	if($property !== ""){
		if($property == 0){
			$where .= " AND `feetype` = 0";
		}elseif($property == 1){
			$where .= " AND `feetype` = 1";
		}
	}

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);
	//待审核
	$totalGray = $dsql->dsqlOper($archives." AND `state` = 0".$where, "totalCount");
	//已审核
	$totalAudit = $dsql->dsqlOper($archives." AND `state` = 1".$where, "totalCount");
	//拒绝审核
	$totalRefuse = $dsql->dsqlOper($archives." AND `state` = 2".$where, "totalCount");

	if($state != ""){
		$where .= " AND `state` = $state";

		if($state == 0){
			$totalPage = ceil($totalGray/$pagestep);
		}elseif($state == 1){
			$totalPage = ceil($totalAudit/$pagestep);
		}elseif($state == 2){
			$totalPage = ceil($totalRefuse/$pagestep);
		}
	}

	$where .= " order by `id` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `cityid`, `typeid`, `uid`, `title`, `began`, `end`, `addrid`, `feetype`, `pubdate`, `ip`, `state` FROM `#@__".$tab."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];

			//分类
			$list[$key]["typeid"] = $value["typeid"];
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__huodong_type` WHERE `id` = ". $value["typeid"]);
			$typename = $dsql->getTypeName($typeSql);
			$list[$key]["typename"] = $typename[0]['typename'];

			//会员
			$list[$key]["uid"] = $value["uid"];
			$username = "无";
			if($value['uid'] != 0){
				$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $value['uid']);
				$username = $dsql->getTypeName($userSql);
				$username = $username[0]['username'];
			}
			$list[$key]["username"] = $username;

			$list[$key]["title"] = $value["title"];
			$list[$key]["began"] = date("Y-m-d H:i", $value["began"]);
			$list[$key]["end"]   = date("Y-m-d H:i", $value["end"]);

			//区域
			$list[$key]["addrid"] = $value["addrid"];
			$addrSql = $dsql->SetQuery("SELECT `typename` FROM `#@__site_area` WHERE `id` = ". $value["addrid"]);
			$addrname = $dsql->getTypeName($addrSql);
			$list[$key]["addrname"] = $addrname[0]['typename'];

            $cityname = getSiteCityName($value['cityid']);
            $list[$key]['cityname'] = $cityname;

			$list[$key]["feetype"] = $value["feetype"];
			$list[$key]["pubdate"] = date('Y-m-d H:i:s', $value["pubdate"]);
			$list[$key]["ip"]      = $value["ip"];
			$list[$key]["state"]   = $value["state"];

			//回复数量
			$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__public_comment` WHERE `pid` =0 AND `type` = 'huodong-detail' AND `aid` = ".$value['id']);
			$ret = $dsql->dsqlOper($sql, "results");
			$replyCount = $ret[0]['t'];
			$list[$key]['reply'] = $replyCount;

			//报名数量
			$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__huodong_reg` WHERE `hid` = ".$value['id']);
			$ret = $dsql->dsqlOper($sql, "results");
			$regCount = $ret[0]['t'];
			$list[$key]['reg'] = $regCount;

			//收入总和
			if($value['feetype']){
				$amount = 0;
				$sql = $dsql->SetQuery("SELECT SUM((SELECT f.`price` FROM `#@__huodong_fee` f WHERE f.`id` = r.`fid`)) amount FROM `#@__huodong_reg` r WHERE r.`hid` = ".$value['id']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$amount = $ret[0]['amount'];
				}
				$list[$key]['amount'] = $amount;
			}

			$param = array(
				"service"  => "huodong",
				"template" => "detail",
				"id"       => $value['id']
			);
			$list[$key]["url"] = getUrlPath($param);

		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}, "huodongList": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}}';
	}
	die;

//删除
}elseif($dopost == "del"){
	if(!testPurview("huodongDel")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	if($id != ""){

		$each = explode(",", $id);
		$error = array();
		$title = array();
		foreach($each as $val){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");

			array_push($title, $results[0]['title']);

			//删除内容图片
			$body = $results[0]['body'];
			if(!empty($body)){
				delEditorPic($body, "huodong");
			}

			//删除费用
			$archives = $dsql->SetQuery("DELETE FROM `#@__huodong_fee` WHERE `hid` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");

			//删除活动
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("删除活动信息", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
		die;

	}
	die;

//更新状态
}elseif($dopost == "updateState"){
	if(!testPurview("huodongEdit")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	$each = explode(",", $id);
	$error = array();
	if($id != ""){
		foreach($each as $val){
			$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `state` = ".$state." WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("更新活动状态", $id."=>".$state);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
	}
	die;

}
//css
$cssFile = array(
    'ui/jquery.chosen.css',
    'admin/chosen.min.css'
);
$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));
//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/jquery.colorPicker.js',
		'ui/jquery-ui-selectable.js',
        'ui/chosen.jquery.min.js',
		'admin/huodong/huodongList.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('notice', $notice);

	$huoniaoTag->assign('typeListArr', json_encode($dsql->getTypeList(0, "huodong_type")));
	$huoniaoTag->assign('addrListArr', json_encode(array()));
    $huoniaoTag->assign('cityList', json_encode($adminCityArr));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/huodong";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
