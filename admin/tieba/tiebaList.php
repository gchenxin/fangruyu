<?php
/**
 * 管理贴吧帖子
 *
 * @version        $Id: tiebaList.php 2016-11-18 下午16:48:12 $
 * @package        HuoNiao.Tieba
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("tiebaList");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/tieba";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "tiebaList.html";

$tab = "tieba_list";

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

    $where = " AND `cityid` in (0,$adminCityIds)";

    if ($adminCity) {
        $where = " AND `cityid` = $adminCity";
    }

	if($sKeyword != ""){
		$where .= " AND (`title` like '%$sKeyword%' OR `ip` like '%$sKeyword%')";
	}
	if($sType != ""){
		if($dsql->getTypeList($sType, "tieba_type")){
			$lower = arr_foreach($dsql->getTypeList($sType, "tieba_type"));
			$lower = $sType.",".join(',',$lower);
		}else{
			$lower = $sType;
		}
		$where .= " AND `typeid` in ($lower)";
	}

	if($property !== ""){
		if($property == 'jinghua'){
			$where .= " AND `jinghua` = 1";
		}elseif($property == "top"){
			$where .= " AND `top` = 1";
		}elseif($property == "bold"){
			$where .= " AND `bold` = 1";
		}elseif($property == "isreply"){
			$where .= " AND `isreply` = 0";
		}
	}

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `waitpay` = 0");

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

	$where .= " order by `weight` desc, `id` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `cityid`, `typeid`, `uid`, `title`, `pubdate`, `ip`, `color`, `bold`, `click`, `state`, `isreply`, `jinghua`, `top` FROM `#@__".$tab."` WHERE `waitpay` = 0".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["title"] = $value["title"];
			$list[$key]["typeid"] = $value["typeid"];

            $cityname = getSiteCityName($value['cityid']);
            $list[$key]['cityname'] = $cityname;

			//地区
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__tieba_type` WHERE `id` = ". $value["typeid"]);
			$typename = $dsql->getTypeName($typeSql);
			$list[$key]["typename"] = $typename[0]['typename'];

			$list[$key]["uid"] = $value["uid"];

			$username = "无";
			if($value['uid'] != 0){
				$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $value['uid']);
				$username = $dsql->getTypeName($userSql);
				$username = $username[0]['username'];
			}
			$list[$key]["username"] = $username;

			$list[$key]["pubdate"] = date('Y-m-d H:i:s', $value["pubdate"]);
			$list[$key]["ip"]      = $value["ip"];
			$list[$key]["color"]   = $value["color"];
			$list[$key]["bold"]    = $value["bold"];
			$list[$key]["click"]   = $value["click"];
			$list[$key]["state"]   = $value["state"];
			$list[$key]["isreply"] = $value["isreply"];
			$list[$key]["jinghua"] = $value["jinghua"];
			$list[$key]["top"]     = $value["top"];

			//回复数量
			$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__public_comment` WHERE `type` = 'tieba-detail' AND `aid` = ".$value['id']);
			$ret = $dsql->dsqlOper($sql, "results");
			$replyCount = $ret[0]['t'];
			$list[$key]['reply'] = $replyCount;

			$param = array(
				"service"  => "tieba",
				"template" => "detail",
				"id"       => $value['id']
			);
			$list[$key]["url"] = getUrlPath($param);

			// 打赏
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_reward` WHERE `module` = 'tieba' AND `aid` = ".$value["id"]." AND `state` = 1");
			//总条数
			$totalCount_ = $dsql->dsqlOper($archives, "totalCount");
			if($totalCount_){
				$archives = $dsql->SetQuery("SELECT SUM(`amount`) totalAmount FROM `#@__member_reward` WHERE `module` = 'tieba' AND `aid` = ".$value["id"]." AND `state` = 1");
				$ret = $dsql->dsqlOper($archives, "results");
				$totalAmount = $ret[0]['totalAmount'];
			}else{
				$totalAmount = 0;
			}
			$list[$key]['reward'] = array("count" => $totalCount_, "amount" => $totalAmount);

		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}, "tiebaList": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}}';
	}
	die;

//获取信息详情
}elseif($dopost == "getDetail"){

	if($id != ""){
		$archives = $dsql->SetQuery("SELECT `typeid`, `title`, `color`, `click`, `weight`, `bold`, `content`, `state`, `isreply`, `jinghua`, `top` FROM `#@__".$tab."` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");
		echo json_encode($results);
	}
	die;

//编辑
}elseif($dopost == "updateDetail"){

	if(!testPurview("tiebaEdit")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};

	if($id == "") die('要修改的信息ID传递失败！');

	//表单二次验证
	if($typeid == ''){
		echo '{"state": 101, "info": '.json_encode("请选择文章分类！").'}';
		exit();
	}

	$sql = $dsql->SetQuery("SELECT `state` FROM `#@__".$tab."` WHERE `id` = ".$id);
	$res = $dsql->dsqlOper($sql, "results");
	$state_ = $res[0]['state'];

	$weight  = (int)$weight;
	$click   = (int)$click;
	$isreply = (int)$isreply;
	$bold    = (int)$bold;
	$jinghua = (int)$jinghua;
	$top     = (int)$top;
	$state   = (int)$state;

	$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `typeid` = $typeid, `weight` = '$weight', `click` = '$click', `isreply` = '$isreply', `bold` = '$bold', `jinghua` = '$jinghua', `top` = '$top', `state` = '$state', `color` = '$color', `content` = '$content' WHERE `id` = ".$id);
	$results = $dsql->dsqlOper($archives, "update");
	if($results != "ok"){
		echo $results;
	}else{

		// 清除缓存
		checkCache("tieba_list", $id);
		clearCache("tieba_detail", $id);
		if(($state != 1 && $state_ == 1)|| ($state == 1 && $state_ != 1)){
			clearCache("tieba_total", "key");
			if($state == 1){
				clearCache("tieba_list", "key");
			}
		}

		adminLog("编辑贴吧帖子信息", $id);
		echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
	}
	die;

//删除
}elseif($dopost == "del"){
	if(!testPurview("tiebaDel")){
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
				delEditorPic($body, "tieba");
			}

			//删除评论
			$archives = $dsql->SetQuery("DELETE FROM `#@__tieba_reply` WHERE `tid` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");

			//删除贴吧帖子
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}else{
				// 清除缓存
				checkCache("tieba_list", $val);
				clearCache("tieba_detail", $val);
				clearCache("tieba_total", "key");
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("删除贴吧帖子信息", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
		die;

	}
	die;

//更新状态
}elseif($dopost == "updateState"){
	if(!testPurview("tiebaEdit")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	$each = explode(",", $id);
	$error = array();
	if($id != ""){
		foreach($each as $val){
			$sql = $dsql->SetQuery("SELECT `state` FROM `#@__".$tab."` WHERE `id` = ".$val);
			$res = $dsql->dsqlOper($sql, "results");
			if(!$res) continue;
			$state_ = $res[0]['state'];

			$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `state` = ".$state." WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}else{
				// 清除缓存
				clearCache("tieba_detail", $val);
				// 取消审核
				if($state != 1 && $state_ == 1){
					checkCache("tieba_list", $val);
					clearCache("tieba_total", "key");
				}elseif($state == 1 && $state_ != 1){
					updateCache("tieba_list", 300);
					clearCache("tieba_total", "key");
				}
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("更新贴吧帖子状态", $id."=>".$state);
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
		'admin/tieba/tiebaList.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('notice', $notice);

	$huoniaoTag->assign('typeListArr', json_encode($dsql->getTypeList(0, "tieba_type")));
    $huoniaoTag->assign('cityList', json_encode($adminCityArr));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/tieba";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
