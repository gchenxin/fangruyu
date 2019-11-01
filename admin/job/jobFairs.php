<?php
/**
 * 管理招聘会
 *
 * @version        $Id: jobFairs.php 2015-3-17 上午10:41:10 $
 * @package        HuoNiao.Job
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("jobFairs");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/job";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$tab = "job_fairs";
//css
$cssFile = array(
    'ui/jquery.chosen.css',
    'admin/chosen.min.css'
);
$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));
if($dopost != ""){
	$templates = "jobFairsAdd.html";

	//js
	$jsFile = array(
		'ui/bootstrap-datetimepicker.min.js',
        'ui/chosen.jquery.min.js',
		'admin/job/jobFairsAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}else{
	$templates = "jobFairs.html";

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/jquery-ui-selectable.js',
        'ui/chosen.jquery.min.js',
		'admin/job/jobFairs.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}

if($submit == "提交"){
	if($token == "") die('token传递失败！');
	$pubdate     = GetMkTime(time());       //发布时间

	//二次验证
	if(empty($fname) && empty($fid)){
		echo '{"state": 101, "info": "请输入会场名称！"}';
		exit();
	}

	if(empty($fid)){
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__job_fairs_center` WHERE `title` = '".$fname."'");
		$result = $dsql->dsqlOper($userSql, "results");
		if(count($result) > 0){
			echo '{"state": 101, "info": "会场不存在，请重新输入"}';
			exit();
		}else{
			$fid = $result[0]['id'];
		}
	}

	if(empty($title)){
		echo '{"state": 101, "info": "请输入招聘会名称！"}';
		exit();
	}

	if(empty($date)){
		echo '{"state": 101, "info": "请选择举办时间！"}';
		exit();
	}

	$date = GetMkTime($date);

	if(empty($began)){
		echo '{"state": 101, "info": "请选择开始时间！"}';
		exit();
	}

	if(empty($end)){
		echo '{"state": 101, "info": "请选择结束时间！"}';
		exit();
	}

}

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	$where = "";

    $where2 = " AND `cityid` in (0,$adminCityIds)";

    if ($cityid){
        $where2 = " AND `cityid` = $cityid";
    }

    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__job_fairs_center` WHERE 1=1".$where2);
    $result = $dsql->dsqlOper($sql, "results");
    if($result){
        $fid = array();
        foreach($result as $key => $fairs){
            array_push($fid, $fairs['id']);
        }
        $where .= " AND `fid` in (".join(",", $fid).")";
    }else{
        echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": 0, "totalCount": 0, "totalGray": 0, "totalAudit": 0, "totalRefuse": 0}}';die;
    }
    if($sKeyword != ""){
		$where .= " AND `title` like '%$sKeyword%'";

		//模糊匹配会场
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__job_fairs_center` WHERE `title` like '%$sKeyword%'".$where2);
		$result = $dsql->dsqlOper($sql, "results");
		if($result){
			$fid = array();
			foreach($result as $key => $fairs){
				array_push($fid, $fairs['id']);
			}
			if(!empty($fid)){
				$where .= " OR `fid` in (".join(",", $fid).")";
			}
		}
	}

	if($sAddr != ""){
		if($dsql->getTypeList($sAddr, "jobaddr")){
			$lower = arr_foreach($dsql->getTypeList($sAddr, "jobaddr"));
			$lower = $sAddr.",".join(',',$lower);
		}else{
			$lower = $sAddr;
		}

		//模糊匹配会场
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__job_fairs_center` WHERE `addr` in ($lower)");
		$result = $dsql->dsqlOper($sql, "results");
		if($result){
			$fid = array();
			foreach($result as $key => $fairs){
				array_push($fid, $fairs['id']);
			}
			if(!empty($fid)){
				$where .= " AND `fid` in (".join(",", $fid).")";
			}
		}else{
			$where .= " AND 1 = 2";
		}

	}

	if(!empty($date)){
		$date = GetMkTime($date);
		$where .= " AND `began` = ".$date;
	}

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);

	$where .= " order by `id` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `fid`, `title`, `date`, `began`, `end`, `click`, `pubdate` FROM `#@__".$tab."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];

			$list[$key]["fid"] = $value["fid"];
			$sql = $dsql->SetQuery("SELECT `title`, `addr` FROM `#@__job_fairs_center` WHERE `id` = ". $value['fid']);
			$fname = $dsql->dsqlOper($sql, "results");
			if($fname){
				$list[$key]["fname"] = $fname[0]["title"];
			}else{
				$list[$key]["fname"] = "无";
			}

			$list[$key]["addrid"] = $fname[0]["addr"];
			$addrSql = $dsql->SetQuery("SELECT `typename` FROM `#@__jobaddr` WHERE `id` = ". $fname[0]["addr"]);
			$addrname = $dsql->dsqlOper($addrSql, "results");
			if($addrname){
				$list[$key]["addr"] = $addrname[0]["typename"];
			}else{
				$list[$key]["addr"] = "无";
			}


			$list[$key]["title"] = $value["title"];
			$list[$key]["date"] = date("Y-m-d", $value["date"]);
			$list[$key]["began"] = $value["began"];
			$list[$key]["end"]    = $value["end"];
			$list[$key]["click"] = $value["click"];

			$list[$key]["pubdate"] = date('Y-m-d H:i:s', $value["pubdate"]);

			$param = array(
				"service"  => "job",
				"template" => "zhaopinhui",
				"param"    => "center=".$value['fid']
			);
			$list[$key]["furl"] = getUrlPath($param);

			$param = array(
				"service"  => "job",
				"template" => "zhaopinhui",
				"id"       => $value['id']
			);
			$list[$key]["url"] = getUrlPath($param);
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "jobFairs": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}}';
	}
	die;

//新增
}elseif($dopost == "Add"){
	if(!testPurview("jobFairsAdd")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	$pagetitle = "新增招聘会";

	//表单提交
	if($submit == "提交"){

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`fid`, `title`, `date`, `began`, `end`, `click`, `note`, `pubdate`) VALUES ('$fid', '$title', '$date', '$began', '$end', '$click', '$note', '".GetMkTime(time())."')");
		$id = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($id)){

			$param = array(
				"service"  => "job",
				"template" => "zhaopinhui",
				"id"       => $id
			);
			$url = getUrlPath($param);

			// 清除缓存
			clearCache("job_fairs_list", "key");
			clearCache("job_fairs_total", "key");

			adminLog("新增招聘会", $title);
			echo '{"state": 100, "url": "'.$url.'", "info": '.json_encode("添加成功！").'}';
		}else{
			echo $id;
		}
		die;
	}

//修改
}elseif($dopost == "edit"){
	if(!testPurview("jobFairsEdit")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	$pagetitle = "修改招聘会信息";

	if($id == "") die('要修改的信息ID传递失败！');
	if($submit == "提交"){

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `fid` = '$fid', `title` = '$title', `date` = '$date', `began` = '$began', `end` = '$end', `click` = '$click', `note` = '$note' WHERE `id` = ".$id);
		$return = $dsql->dsqlOper($archives, "update");

		if($return == "ok"){

			$param = array(
				"service"  => "job",
				"template" => "zhaopinhui",
				"id"       => $id
			);
			$url = getUrlPath($param);

			// 清除缓存
			checkCache("job_fairs_list", $id);
			clearCache("job_fairs_total", "key");
			clearCache("job_fairs_detail", $id);

			adminLog("修改招聘会信息", $title);
			echo '{"state": 100, "info": '.json_encode("修改成功！").', "url": "'.$url.'"}';
		}else{
			echo $return;
		}
		die;

	}else{
		if(!empty($id)){

			//主表信息
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "results");

			if(!empty($results)){

				$fid   = $results[0]['fid'];
				$sql   = $dsql->SetQuery("SELECT `title` FROM `#@__job_fairs_center` WHERE `id` = ". $results[0]['fid']);
				$fname = $dsql->getTypeName($sql);
				$fname = $fname[0]["title"];

				$title = $results[0]['title'];
				$date  = date("Y-m-d", $results[0]['date']);
				$began = $results[0]['began'];
				$end   = $results[0]['end'];
				$click = $results[0]['click'];
				$note  = $results[0]['note'];

			}else{
				ShowMsg('要修改的信息不存在或已删除！', "-1");
				die;
			}

		}else{
			ShowMsg('要修改的信息参数传递失败，请联系管理员！', "-1");
			die;
		}
	}

//删除
}elseif($dopost == "del"){
	if(!testPurview("jobFairsDel")){
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
				checkCache("job_fairs_list", $val);
				clearCache("job_fairs_detail", $val);
			}
		}
		// 清除缓存
		clearCache("job_fairs_total", "key");

		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("删除招聘会", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
		die;

	}
	die;

//根据关键词查询会场
}elseif($dopost == "checkFairs"){
	$result = "";
	$key = $_POST['key'];
	if(!empty($key)){
        if($userType == 0)
            $where = "";
        if($userType == 3)
            $where = " AND `addr` in ($adminAreaIDs)";
		$sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__job_fairs_center` WHERE `title` like '%$key%'".$where." LIMIT 0, 10");
		$result = $dsql->dsqlOper($sql, "results");
		if($result){
			echo json_encode($result);
		}else{
			echo 200;
		}
	}
	die;

}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('pagetitle', $pagetitle);

	if($dopost != ""){
		$huoniaoTag->assign('id', $id);
		$huoniaoTag->assign('fid', $fid);
		$huoniaoTag->assign('fname', $fname);
		$huoniaoTag->assign('title', $title);
		$huoniaoTag->assign('date', $date);
		$huoniaoTag->assign('began', $began);
		$huoniaoTag->assign('end', $end);
		$huoniaoTag->assign('click', $click);
		$huoniaoTag->assign('note', $note);
	}

    $huoniaoTag->assign('cityArr', $userLogin->getAdminCity());
	$huoniaoTag->assign('addrListArr', json_encode($dsql->getTypeList(0, "jobaddr")));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/job";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
