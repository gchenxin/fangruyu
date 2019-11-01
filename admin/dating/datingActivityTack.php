<?php
/**
 * 交友会员活动报名管理
 *
 * @version        $Id: datingActivityTack.php 2014-7-21 上午12:14:21 $
 * @package        HuoNiao.Dating
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("datingActivityTack");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$tab = "dating_activity_take";
$templates = "datingActivityTack.html";
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
	'admin/dating/datingActivityTack.js'
);
$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

$pagetitle = "交友会员活动报名管理";

//列表
if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	$where = "";

    $where2 = " AND `cityid` in (0,$adminCityIds)";

    if ($adminCity){
        $where2 = " AND `cityid` = $adminCity";
    }

    $userid = array();
    $activityid = array();

	if($sKeyword != ""){
		$userSql = $dsql->SetQuery("SELECT mm.`id` FROM `#@__member` m LEFT JOIN `#@__dating_member` mm ON mm.`userid` = m.`id` WHERE (m.`username` like '%$sKeyword%' OR m.`nickname` like '%$sKeyword%' OR m.`email` like '%$sKeyword%') AND mm.`id` > 0");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			foreach($userResult as $key => $user){
				array_push($userid, $user['id']);
			}
		}else{
            $where2 .= " AND `title` like '%$sKeyword%'";
        }
	}

    $activitySql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_activity` WHERE 1=1".$where2);
    $activityResult = $dsql->dsqlOper($activitySql, "results");
    if($activityResult){
        foreach($activityResult as $key => $activity){
            array_push($activityid, $activity['id']);
        }
    }else{
        echo '{"state": 101, "info": ' . json_encode("暂无相关信息") . ', "pageInfo": {"totalPage": 0, "totalCount": 0, "totalGray": 0, "totalAudit": 0, "totalRefuse": 0}}';
        die;
    }


    if(!empty($userid) && !empty($activityid)){
        $where .= " AND (`uid` in (".join(",", $userid).") AND `aid` in (".join(",", $activityid)."))";
    }elseif(!empty($userid)){
        $where .= " AND `uid` in (".join(",", $userid).")";
    }elseif(!empty($activityid)){
        $where .= " AND `aid` in (".join(",", $activityid).")";
    }else{
        $where .= " AND 1 = 2";
    }

	$where .= " order by `id` desc";

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `uid`, `aid`, `pubdate` FROM `#@__".$tab."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["aid"] = $value["aid"];

			//会员信息
			$userSql = $dsql->SetQuery("SELECT m.`nickname`, mm.`userid` FROM `#@__member` m LEFT JOIN `#@__dating_member` mm ON mm.`userid` = m.`id` WHERE mm.`id` = ". $value["uid"]);
			$userResult = $dsql->getTypeName($userSql);
			$list[$key]["username"] = $userResult[0]['nickname'];
			$list[$key]["uid"]      = $userResult[0]["userid"];

			//活动信息
			$activitySql = $dsql->SetQuery("SELECT `title` FROM `#@__dating_activity` WHERE `id` = ". $value["aid"]);
			$activityResult = $dsql->getTypeName($activitySql);
			$list[$key]["activity"] = $activityResult[0]['title'];

			$list[$key]["pubdate"] = date("Y-m-d H:i:s", $value["pubdate"]);

			$param = array(
				"service"     => "dating",
				"template"    => "activity",
				"id"          => $value['aid']
			);
			$list[$key]['url'] = getUrlPath($param);
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "datingActivityTack": '.json_encode($list).'}';
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
			adminLog("删除交友相亲活动报名", $id);
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
	}
	die;

}

//验证模板文件
if(file_exists($tpl."/".$templates)){
	$huoniaoTag->assign('pagetitle', $pagetitle);
    $huoniaoTag->assign('cityList', json_encode($adminCityArr));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
