<?php
/**
 * 管理黄页信息
 *
 * @version        $Id: huangyeList.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.Info
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("huangyeList");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/huangye";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "huangyeList.html";

$action = "huangye";

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

  $where = " AND `cityid` in (0,$adminCityIds)";

  if ($cityid){
      $where = " AND `cityid` = $cityid";
  }

	if($sKeyword != ""){
		$where .= " AND `title` like '%$sKeyword%'";
	}

	if($sType != ""){
		if($dsql->getTypeList($sType, $action."type")){
			global $arr_data;
			$arr_data = array();
			$lower = arr_foreach($dsql->getTypeList($sType, $action."type"));
			$lower = $sType.",".join(',',$lower);
		}else{
			$lower = $sType;
		}
		$where .= " AND `typeid` in ($lower)";
	}

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$action."list` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);
	//待审核
	$totalGray = $dsql->dsqlOper($archives." AND `arcrank` = 0".$where, "totalCount");
	//已审核
	$totalAudit = $dsql->dsqlOper($archives." AND `arcrank` = 1".$where, "totalCount");
	//拒绝审核
	$totalRefuse = $dsql->dsqlOper($archives." AND `arcrank` = 2".$where, "totalCount");
	//取消显示
	$totalNoshow = $dsql->dsqlOper($archives." AND `arcrank` = 3".$where, "totalCount");
	//已过期
	$now = GetMkTime(time());
	$totalValid = $dsql->dsqlOper($archives." AND (`valid` < ".$now." AND `valid` <> 0)".$where, "totalCount");

	// $archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$action."list` WHERE ((`pubdate` + `valid` * 86400) < ".$now." AND `valid` <> 0)");
	// $valid = $dsql->dsqlOper($archives, "totalCount");

	if($state != "" && $state != 4){
		$where .= " AND `arcrank` = $state";

		if($state == 0){
			$totalPage = ceil($totalGray/$pagestep);
		}elseif($state == 1){
			$totalPage = ceil($totalAudit/$pagestep);
		}elseif($state == 2){
			$totalPage = ceil($totalRefuse/$pagestep);
		}elseif($state == 3){
			$totalPage = ceil($totalNoshow/$pagestep);
		}
	}

	//筛选已经过期的信息
	if($state == 4){

		$now = GetMkTime(time());
		$where .= " AND (`valid` < ".$now." AND `valid` <> 0)";

		$totalPage = ceil($totalValid/$pagestep);

	}

	$where .= " order by `pubdate` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `title`, `color`, `typeid`, `addr`, `weight`, `userid`, `ip`, `ipaddr`, `arcrank`, `pubdate`, `valid` FROM `#@__".$action."list` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["title"] = $value["title"];
			$list[$key]["color"] = $value["color"];
			$list[$key]["typeid"] = $value["typeid"];
			$list[$key]["addrid"] = $value["addr"];

			//分类
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__".$action."type` WHERE `id` = ". $value["typeid"]);
			$typename = $dsql->getTypeName($typeSql);
			$list[$key]["type"] = $typename[0]['typename'];

			$list[$key]["sort"] = $value["weight"];

			$state = "";
			switch($value["arcrank"]){
				case "0":
					$state = "等待审核";
					break;
				case "1":
					$state = "审核通过";
					break;
				case "2":
					$state = "审核拒绝";
					break;
				case "3":
					$state = "取消显示";
					break;
			}

			$list[$key]["state"] = $state;

			$list[$key]["ip"] = $value["ip"];
			$list[$key]["ipaddr"] = $value["ipaddr"];
			$list[$key]['isvalid'] = ($value['valid'] != 0 && $value['valid'] < $now) ? 1 : 0;

			$list[$key]["date"] = date('Y-m-d H:i:s', $value["pubdate"]);

			$param = array(
				"service"     => "huangye",
				"template"    => "detail",
				"id"          => $value['id']
			);
			$list[$key]['url'] = getUrlPath($param);
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.', "totalNoshow": '.$totalNoshow.', "valid": '.$totalValid.'}, "articleList": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.', "totalNoshow": '.$totalNoshow.', "valid": '.$totalValid.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.', "totalNoshow": '.$totalNoshow.', "valid": '.$totalValid.'}}';
	}
	die;

//删除
}elseif($dopost == "del"){
	if(!testPurview("delHuangye")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	if($id != ""){
		$each = explode(",", $id);
		$error = array();
		$title = array();
		foreach($each as $val){

			//删除评论
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$action."common` WHERE `aid` = ".$val);
			$dsql->dsqlOper($archives, "update");

			//删除自定义导航正文中图片
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$action."nav` WHERE `aid` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach ($results as $key => $value) {
					if(!empty($value['body'])){
						delEditorPic($value['body'], $action);
					}
					if(!empty($value['mbody'])){
						delEditorPic($value['mbody'], $action);
					}
				}
			}

			$archives = $dsql->SetQuery("SELECT `title`,`litpic`,`pics` FROM `#@__".$action."list` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");

			array_push($title, $results[0]['title']);

			//删除缩略图
			delPicFile($results[0]['litpic'], "delThumb", $action);
			//删除图集
			if(!empty($results[0]['pics'])){
				$atlasPic = $results[0]['pics'];
				delPicFile($atlasPic, "delAtlas", $action);
			}

			//删除自定义导航
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$action."nav` WHERE `aid` = ".$val);
			$dsql->dsqlOper($archives, "update");

			//删除主表
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$action."list` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("删除黄页信息", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
		die;
	}
	die;

//删除所有待审核信息
}elseif($dopost == "delAllGray"){
	if(!testPurview("delHuangye")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};

	$archives = $dsql->SetQuery("SELECT * FROM `#@__".$action."list` WHERE `arcrank` = 0");
	$results = $dsql->dsqlOper($archives, "results");
	if($results){
		foreach ($results as $key => $value) {

			//删除评论
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$action."common` WHERE `aid` = ".$value['id']);
			$dsql->dsqlOper($archives, "update");

			//删除自定义导航正文中图片
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$action."nav` WHERE `aid` = ".$value['id']);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach ($results as $key => $value) {
					if(!empty($value['body'])){
						delEditorPic($value['body'], $action);
					}
					if(!empty($value['mbody'])){
						delEditorPic($value['mbody'], $action);
					}
				}
			}

			//删除缩略图
			delPicFile($value['litpic'], "delThumb", $action);
			//删除图集
			if(!empty($results[0]['pics'])){
				$atlasPic = $results[0]['pics'];
				delPicFile($atlasPic, "delAtlas", $action);
			}

			//删除自定义导航
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$action."nav` WHERE `aid` = ".$value['id']);
			$dsql->dsqlOper($archives, "update");

			//删除表
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$action."list` WHERE `id` = ".$value['id']);
			$dsql->dsqlOper($archives, "update");

		}

	}
	die;



//更新状态
}elseif($dopost == "updateState"){
	if(!testPurview("editInfo")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	$each = explode(",", $id);
	$error = array();
	if($id != ""){
		foreach($each as $val){
			$archives = $dsql->SetQuery("UPDATE `#@__".$action."list` SET `arcrank` = ".$arcrank." WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("更新分类信息状态", $id."=>".$arcrank);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
	}
	die;

//更新时间
}elseif($dopost == "updateTime"){
	if(!testPurview("editHuangye")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	$each = explode(",", $id);
	$error = array();
	if($id != ""){
		foreach($each as $val){
			$archives = $dsql->SetQuery("UPDATE `#@__".$action."list` SET `pubdate` = ".GetMkTime(time())." WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("更新分类信息时间", $id);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
	}
	die;

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
		'ui/jquery-ui-selectable.js',
        'ui/chosen.jquery.min.js',
		'admin/huangye/huangyeList.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

    $huoniaoTag->assign('cityArr', $userLogin->getAdminCity());
	$huoniaoTag->assign('typeListArr', json_encode($dsql->getTypeList(0, $action."type")));
	$huoniaoTag->assign('addrListArr', json_encode($dsql->getTypeList(0, $action."addr")));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/huangye";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
