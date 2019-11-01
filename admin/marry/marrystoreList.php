<?php
/**
 * 管理店铺
 *
 * @version        $Id: storeList.php 2019-04-1 上午11:28:16 $
 * @package        HuoNiao.marry
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("storeList");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/marry";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "storeList.html";

$tab = "marry_store";

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

    $where = " AND `cityid` in (0,$adminCityIds)";

    if ($adminCity) {
        $where = " AND `cityid` = $adminCity";
    }

	if($sKeyword != ""){
		$where .= " AND `title` like '%$sKeyword%'";
	}

	if(!empty($mType)){
		$where .= " AND FIND_IN_SET('".$mType."', `bind_module`)";
	}

	if($sType){
		if($dsql->getTypeList($sType, "marry_type")){
			$lower = arr_foreach($dsql->getTypeList($sType, "marry_type"));
			$lower = $sType.",".join(',',$lower);
		}else{
			$lower = $sType;
		}
		$where .= " AND `typeid` in ($lower)";
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

	$where .= " order by `pubdate` desc, `weight` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `cityid`, `title`, `userid`, `tel`, `address`, `state`, `typeid`, `weight`, `pubdate`, `bind_module` FROM `#@__".$tab."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["title"] = $value["title"];

			$list[$key]["userid"] = $value["userid"];
			if($value["userid"] == 0){
				$list[$key]["username"] = $value["username"];
			}else{
				$userSql = $dsql->SetQuery("SELECT `id`, `username` FROM `#@__member` WHERE `id` = ". $value['userid']);
				$username = $dsql->getTypeName($userSql);
				$list[$key]["username"] = $username[0]["username"];
			}

			$list[$key]["tel"] = $value["tel"];
			$list[$key]["address"] = $value["address"];
			$list[$key]["state"] = $value["state"];
			
			$list[$key]["weight"] = $value["weight"];
			$list[$key]["pubdate"] = date('Y-m-d H:i:s', $value["pubdate"]);

            $cityname = getSiteCityName($value['cityid']);
			$list[$key]['cityname'] = $cityname;
			
			

			

			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__marry_type` WHERE `id` = ". $value["typeid"]);
			$res = $dsql->dsqlOper($sql, "results");
			if(!empty($res[0]['typename'])){
				// $list[$key]["typename"] = $res[0]["typename"];
				$typename = $res[0]["typename"];
			}else{
				// $list[$key]["typename"] = '';
				$typename = '';
			}

			include_once HUONIAOROOT."/api/handlers/marry.class.php";
			$marry = new marry();
			$bind_moduleArr  = array();
			$bind_moduleArr_ = $value['bind_module'] ? explode(',', $value['bind_module']) : array();
			if($bind_moduleArr_){
				foreach($bind_moduleArr_ as $k => $row){
					$bind_modulename = $marry->gettypename("module_type", $row);
					array_push($bind_moduleArr, $bind_modulename);
				}
			}
			$list[$key]["typename"]  = !empty($bind_moduleArr) ? (!empty($typename) ? $typename . '--' : '' ) . join(",", $bind_moduleArr) : $typename;

			$param = array(
				"service"     => "marry",
				"template"    => "store-detail",
				"id"          => $value['id']
			);
			$list[$key]['url'] = getUrlPath($param);
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}, "storeList": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}}';
	}
	die;

//删除
}elseif($dopost == "del"){
	if(!testPurview("marrystoreDel")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	if($id != ""){

		$each = explode(",", $id);
		$error = array();
		$title = array();
		foreach($each as $val){

			$archives = $dsql->SetQuery("SELECT `pics`, `video`, `note` FROM `#@__marry_store` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");

			delPicFile($results[0]['pics'], "delAtlas", "marry");
			delPicFile($results[0]['video'], "delVideo", "marry");
			//删除内容图片
			$body = $results[0]['note'];
			if(!empty($body)){
				delEditorPic($body, "marry");
			}

			//删除表
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			checkMarryStoreCache($id);
			adminLog("删除婚嫁店铺信息", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
		die;

	}
	die;

//更新状态
}elseif($dopost == "updateState"){
	if(!testPurview("marrystoreEdit")){
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
			checkMarryStoreCache($id);
			adminLog("更新婚嫁店铺状态", $id."=>".$state);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
	}
	die;

}

// 检查缓存
function checkMarryStoreCache($id){
	checkCache("marry_store_list", $id);
	clearCache("marry_store_total", 'key');
	clearCache("marry_store_detail", $id);
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
		'admin/marry/storeList.js'
	);

	include_once HUONIAOROOT."/api/handlers/marry.class.php";
	$marry = new marry();
	$moduleList = $marry->module_type(); // 新闻类型
	$huoniaoTag->assign('moduleListArr', json_encode($moduleList));
	
	$huoniaoTag->assign('notice', $notice);
	$huoniaoTag->assign('typeListArr', json_encode($dsql->getTypeList(0, "marry_type")));
    $huoniaoTag->assign('cityList', json_encode($adminCityArr));
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/marry";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
