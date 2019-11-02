<?php
/**
 * 管理招聘职位
 *
 * @version        $Id: jobPost.php 2014-3-17 上午11:09:15 $
 * @package        HuoNiao.Job
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("jobPost");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/job";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "jobPost.html";

$tab = "job_post";

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

    $where = " AND `cityid` in (0,$adminCityIds)";

    if ($adminCity){
        $where = " AND `cityid` = $adminCity";
    }

    if($sKeyword != ""){
		$where .= " AND `title` like '%".$sKeyword."%'";

		$comSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__job_company` WHERE `title` like '%$sKeyword%'");
		$comResult = $dsql->dsqlOper($comSql, "results");
		if($comResult){
			$comid = array();
			foreach($comResult as $key => $com){
				array_push($comid, $com['id']);
			}
			if(!empty($comid)){
				$where .= " OR `company` in (".join(",", $comid).")";
			}
		}

		//模糊匹配简历用户
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__job_resume` WHERE `name` like '%$sKeyword%'");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			$userid = array();
			foreach($userResult as $key => $user){
				$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__job_bole` WHERE `userid` = ".$user['id']);
				$userResult = $dsql->dsqlOper($userSql, "results");
				if($userResult){
					array_push($userid, $userResult[0]['id']);
				}
			}
			if(!empty($userid)){
				$where .= " OR `bole` in (".join(",", $userid).")";
			}
		}
	}

	if($sType != ""){
		if($dsql->getTypeList($sType, "job_type")){
			$lower = arr_foreach($dsql->getTypeList($sType, "job_type"));
			$lower = $sType.",".join(',',$lower);
		}else{
			$lower = $sType;
		}
		$where .= " AND `type` in ($lower)";
	}

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);
	//待审核
	$state0 = $dsql->dsqlOper($archives." AND `state` = 0 AND `valid` >= ".time().$where, "totalCount");
	//已审核
	$state1 = $dsql->dsqlOper($archives." AND `state` = 1 AND `valid` >= ".time().$where, "totalCount");
	//拒绝审核
	$state2 = $dsql->dsqlOper($archives." AND `state` = 2 AND `valid` >= ".time().$where, "totalCount");
	//已过期
	$state3 = $dsql->dsqlOper($archives." AND `valid` < ".time().$where, "totalCount");

	if($state != ""){

		if($state == 3){
			$where .= " AND `valid` < ".time();
		}else{
			$where .= " AND `state` = $state AND `valid` >= ".time();
		}

		if($state == 0){
			$totalPage = ceil($state0/$pagestep);
		}elseif($state == 1){
			$totalPage = ceil($state1/$pagestep);
		}elseif($state == 2){
			$totalPage = ceil($state2/$pagestep);
		}elseif($state == 3){
			$totalPage = ceil($state3/$pagestep);
		}
	}

	$where .= " order by `weight` desc, `pubdate` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `title`, `type`, `company`, `bole`, `state`, `valid`, `pubdate` FROM `#@__".$tab."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["title"] = $value["title"];

			$list[$key]["typeid"] = $value["type"];
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__job_type` WHERE `id` = ". $value['type']);
			$typename = $dsql->getTypeName($typeSql);
			$list[$key]["type"] = $typename[0]["typename"];

			$list[$key]["companyid"] = $value["company"];
			$comSql = $dsql->SetQuery("SELECT `title` FROM `#@__job_company` WHERE `id` = ". $value['company']);
			$comname = $dsql->getTypeName($comSql);
			$list[$key]["company"] = $comname[0]["title"];

			$list[$key]["bole"] = $value["bole"];
			if($value['bole'] != 0){
				$comSql = $dsql->SetQuery("SELECT `userid` FROM `#@__job_bole` WHERE `id` = ". $value['bole']);
				$comname = $dsql->getTypeName($comSql);
				$userid = $comname[0]['userid'];

				$comSql = $dsql->SetQuery("SELECT `name` FROM `#@__job_resume` WHERE `id` = ". $userid);
				$comname = $dsql->getTypeName($comSql);
				$userid = $comname[0]['userid'];
				$list[$key]["boleName"] = $comname[0]["name"];
			}

			$list[$key]["state"] = $value["state"];
			$list[$key]["valid"] = $value["valid"];
			$list[$key]["pubdate"] = date('Y-m-d H:i:s', $value["pubdate"]);

			$param = array(
				"service"  => "job",
				"template" => "company",
				"id"       => $value['company']
			);
			$list[$key]["companyurl"] = getUrlPath($param);

			$param = array(
				"service"  => "job",
				"template" => "zhaopin",
				"param"    => "jtype=".$value['type']
			);
			$list[$key]["typeurl"] = getUrlPath($param);

			$param = array(
				"service"  => "job",
				"template" => "job",
				"id"       => $value['id']
			);
			$list[$key]["url"] = getUrlPath($param);

			//验证是否过期
			// if($value['state'] != 3 && GetMkTime(time()) > $value['valid']){
			// 	$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `state` = 3 WHERE `id` = ".$value['id']);
			// 	$dsql->dsqlOper($sql, "update");

			// 	$list[$key]["state"] = "3";

			// 	$state3++;
			// 	if($value['state'] == 0) {
			// 		$state0--;
			// 	}elseif($value['state'] == 1) {
			// 		$state1--;
			// 	}elseif($value['state'] == 2) {
			// 		$state2--;
			// 	}
			// }
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "state0": '.$state0.', "state1": '.$state1.', "state2": '.$state2.', "state3": '.$state3.', "time": '.time().'}, "jobPost": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "state0": '.$state0.', "state1": '.$state1.', "state2": '.$state2.', "state3": '.$state3.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "state0": '.$state0.', "state1": '.$state1.', "state2": '.$state2.', "state3": '.$state3.'}}';
	}
	die;

//删除
}elseif($dopost == "del"){
	if(!testPurview("jobPostDel")){
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

			//删除表
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}else{
				// 清除缓存
				checkCache("job_post_list", $val);
				clearCache("job_post_detail", $val);
				clearCache("job_post_total", "key");
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("删除招聘职位信息", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
		die;

	}
	die;

//更新状态
}elseif($dopost == "updateState"){
	if(!testPurview("jobPostEdit")){
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
				clearCache("job_post_detail", $val);
				// 取消审核
				if($state != 1 && $state_ == 1){
					checkCache("job_post_list", $val);
					clearCache("job_post_total", "key");
				}elseif($state == 1 && $state_ != 1){
					updateCache("job_post_list", 300);
					clearCache("job_post_total", "key");
				}
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("更新招聘职位状态", $id."=>".$state);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
	}
	die;

//根据公司ID获取伯乐
}elseif($dopost == "getBoleList"){
	if($sid){
		if($id != ""){
			$archives = $dsql->SetQuery("SELECT `bole` FROM `#@__job_post` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$id = $results[0]['bole'];
			}
		}

		$archives = $dsql->SetQuery("SELECT `id`, `userid` FROM `#@__job_bole` WHERE `cid` = ".$sid);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$cList = array();
			foreach($results as $key => $val){

				$archives = $dsql->SetQuery("SELECT `name` FROM `#@__job_resume` WHERE `id` = ".$val['userid']);
				$results = $dsql->dsqlOper($archives, "results");
				if($results){
					$selected = "";
					if(!empty($id) && $val['id'] == $id){
						$selected = " selected";
					}
					array_push($cList, '<option value="'.$val['id'].'"'.$selected.'>'.$results[0]['name'].'</option>');
				}
			}
			if(!empty($cList)){
				echo '{"state": 100, "info": "获取成功！", "list": '.json_encode('<option value="">请选择</option>'.join("", $cList)).'}';
			}else{
				echo '{"state": 200, "info": "获取失败！"}';
			}
		}else{
			echo '{"state": 200, "info": "获取失败！"}';
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
		'admin/job/jobPost.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

    $huoniaoTag->assign('cityList', json_encode($adminCityArr));
	$huoniaoTag->assign('typeListArr', json_encode($dsql->getTypeList(0, "job_type")));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/job";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
