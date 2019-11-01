<?php
/**
 * 交友婚恋课堂
 *
 * @version        $Id: datingSchool.php 2014-7-24 上午09:41:25 $
 * @package        HuoNiao.Dating
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("datingSchool");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$tab = "dating_school";
//css
$cssFile = array(
    'ui/jquery.chosen.css',
    'admin/chosen.min.css'
);
$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));
if($dopost != ""){
	$templates = "datingSchoolAdd.html";

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
        // 'ui/bootstrap-datetimepicker.min.js',
        'ui/jquery.dragsort-0.5.1.min.js',
        'ui/chosen.jquery.min.js',
		'publicUpload.js',
		'admin/dating/datingSchoolAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}else{
	$templates = "datingSchool.html";
	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/jquery-ui-selectable.js',
        'ui/chosen.jquery.min.js',
		'admin/dating/datingSchool.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}

if($submit == "提交"){
	if($token == "") die('token传递失败！');
	$pubdate = GetMkTime(time());       //发布时间

    //表单二次验证
    if(empty($cityid)){
        echo '{"state": 200, "info": "请选择城市"}';
        exit();
    }

    $adminCityIdsArr = explode(',', $adminCityIds);
    if(!in_array($cityid, $adminCityIdsArr)){
        echo '{"state": 200, "info": "要发布的城市不在授权范围"}';
        exit();
    }

	if(trim($title) == ""){
		echo '{"state": 200, "info": "请输入活动标题"}';
		exit();
	}

    if(empty($typeid)){
        echo '{"state": 200, "info": "请选择分类"}';
        exit();
    }

	if(trim($litpic) == ""){
		echo '{"state": 200, "info": "请上传缩略图"}';
		exit();
	}

	if(trim($content) == ""){
		echo '{"state": 200, "info": "请输入信息详情"}';
		exit();
	}

	$click = (int)$click;
	$weight = (int)$weight;
	$arcrank = (int)$arcrank;
}

//阅读权限-下拉菜单
$huoniaoTag->assign('arcrankList', array(0 => '等待审核', 1 => '审核通过', 2 => '审核拒绝'));
$huoniaoTag->assign('arcrank', 1);  //阅读权限默认审核通过

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

    $where = " AND `cityid` in (0,$adminCityIds)";

    if ($adminCity) {
        $where = " AND `cityid` = $adminCity";
    }

    if($sKeyword != ""){
		$where .= " AND (`title` like '%".$sKeyword."%')";
	}

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);

	$where .= " order by `id` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `cityid`, `typeid`, `title`, `litpic`, `pics`, `pubdate`, `arcrank` FROM `#@__".$tab."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["title"] = $value["title"];
			$list[$key]["litpic"] = $value["litpic"];
			$list[$key]["pics"] = $value["pics"];
			$list[$key]["pubdate"] = date('Y-m-d H:i:s', $value["pubdate"]);
            $cityname = getSiteCityName($value['cityid']);
            $list[$key]['cityname'] = $cityname;

            $list[$key]["click"] = $value["click"];
            $list[$key]["weigth"] = $value["weigth"];
            $list[$key]["typeid"] = $value["typeid"];
            $list[$key]["arcrank"] = $value["arcrank"];

            $typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__" . $tab . "type` WHERE `id` = " . $value["typeid"]);
            $typename = $dsql->getTypeName($typeSql);

            $list[$key]["type"] = $typename[0]['typename'];

			$param = array(
				"service"     => "dating",
				"template"    => "activity",
				"id"          => $value['id']
			);
			$list[$key]['url'] = getUrlPath($param);
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "datingSchool": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}}';
	}
	die;

//新增
}elseif($dopost == "add"){

	$pagetitle = "新增交友婚恋课堂";

	//表单提交
	if($submit == "提交"){

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`cityid`, `typeid`, `title`, `description`, `litpic`, `pics`, `content`, `pubdate`, `click`, `weight`, `arcrank`) VALUES ('$cityid', '$typeid', '$title', '$description', '$litpic', '$imglist', '$content', '$pubdate', '$click', '$weight', '$arcrank')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){
			adminLog("新增交友相亲活动", $title);

			$param = array(
				"service"     => "dating",
				"template"    => "activity",
				"id"          => $aid
			);
			$url = getUrlPath($param);
			echo '{"state": 100, "url": "'.$url.'"}';
		}else{
			echo $return;
		}
		die;
	}

//修改
}elseif($dopost == "edit"){

	$pagetitle = "修改交友相亲活动信息";

	if($id == "") die('要修改的信息ID传递失败！');
	if($submit == "提交"){

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `cityid` = '$cityid', `typeid` = '$typeid', `title` = '$title', `description` = '$description', `litpic` = '$litpic', `pics` = '$imglist', `content` = '$content', `click` = '$click', `weight` = '$weight', `arcrank` = '$arcrank' WHERE `id` = ".$id);
		$return = $dsql->dsqlOper($archives, "update");

		if($return == "ok"){
			adminLog("修改交友相亲活动信息", $title);

			$param = array(
				"service"     => "dating",
				"template"    => "activity",
				"id"          => $id
			);
			$url = getUrlPath($param);
			echo '{"state": 100, "url": "'.$url.'"}';
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

				$title   = $results[0]['title'];
				$litpic  = $results[0]['litpic'];
				$pics    = $results[0]['pics'];
				$content = $results[0]['content'];
				$cityid  = $results[0]['cityid'];
				$typeid  = $results[0]['typeid'];
				$click   = $results[0]['click'];
				$weight  = $results[0]['weight'];
				$arcrank = $results[0]['arcrank'];
				$description  = $results[0]['description'];

				global $data;
				$data = "";
				$typename = getParentArr($tab."type", $results[0]['typeid']);
				$typename = join(" > ", array_reverse(parent_foreach($typename, "typename")));

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
	if($id != ""){

		$each = explode(",", $id);
		$error = array();
		$title = array();
		foreach($each as $val){

			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");

			array_push($title, $results[0]['title']);

			//删除缩略图
			delPicFile($results[0]['litpic'], "delThumb", "dating");

			$body = $results[0]['content'];
			if(!empty($body)){
				delEditorPic($body, "dating");
			}

			//删除报名
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."_take` WHERE `aid` = ".$val);
			$dsql->dsqlOper($archives, "update");

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
			adminLog("删除交友相亲活动", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
		die;

	}
	die;

//更新状态
}elseif($dopost == "updateState"){

	$each = explode(",", $id);
	$error = array();

	global $handler;
	$handler = true;

	if($id != ""){
		foreach($each as $val){

			//更新记录状态
			$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `arcrank` = $arcrank WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("更新婚恋课堂信息状态", $id."=>".$arcrank);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
	}
	die;
}

//验证模板文件
if(file_exists($tpl."/".$templates)){
	$huoniaoTag->assign('pagetitle', $pagetitle);
	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('mapCity', $cfg_mapCity);
	$huoniaoTag->assign('id', $id);

	$huoniaoTag->assign('title', $title);
	$huoniaoTag->assign('description', $description);
	$huoniaoTag->assign('litpic', $litpic);
	$huoniaoTag->assign('content', $content);
    $huoniaoTag->assign('click', (int)$click);
    $huoniaoTag->assign('weight', (int)$weight);
    $huoniaoTag->assign('cityid', (int)$cityid);
    $huoniaoTag->assign('typeid', (int)$typeid);
	$huoniaoTag->assign('typename', empty($typename) ? "选择分类" : $typename);
    $huoniaoTag->assign('arcrank', (int)$arcrank);

    $huoniaoTag->assign('cityList', json_encode($adminCityArr));
	$huoniaoTag->assign('typeListArr', json_encode($dsql->getTypeList(0, $tab."type")));
	$huoniaoTag->assign('imglist', json_encode(!empty($pics) ? explode(",", $pics) : array()));

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
