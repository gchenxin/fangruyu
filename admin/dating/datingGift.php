<?php
/**
 * 礼物
 *
 * @version        $Id: datingGift.php 2015-7-18 上午1:03:21 $
 * @package        HuoNiao.dating
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("datingGift");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$tab = "dating_gift";

if($dopost != ""){
	$templates = "datingGiftAdd.html";

	//js
	$jsFile = array(
		'ui/jquery.dragsort-0.5.1.min.js',
		'publicUpload.js',
		'admin/dating/datingGiftAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}else{
	$templates = "datingGift.html";

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/jquery-ui-selectable.js',
		'admin/dating/datingGift.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}

if($submit == "提交"){
	if($token == "") die('token传递失败！');
	$typeid  = (int)$typeid;
	$title   = $title;
	$litpic  = $litpic;
	$weight   = (int)$weight;
	$price   = (int)$price;
	$pubdate = GetMkTime(time()); //发布时间

	// if(empty($typeid)){
	// 	echo '{"state": 200, "info": "请选择所属分类"}';
	// 	exit();
	// }

	if(empty($title)){
		echo '{"state": 200, "info": "请输入礼物名称"}';
		exit();
	}

	if(empty($litpic)){
		echo '{"state": 200, "info": "请上传图片"}';
		exit();
	}

}

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	$where = "";

	if($sKeyword != ""){
		$where .= " AND `title` like '%$sKeyword%'";
	}

	// if($sType != ""){
	// 	$where .= " AND `typeid` = '$sType'";
	// }

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);

	$where .= " order by `weight` DESC, `date` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `title`, `litpic`, `price`, `weight`, `date` FROM `#@__".$tab."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["title"] = $value["title"];
			$list[$key]["litpic"] = $value["litpic"];
			$list[$key]["price"] = $value["price"];
			$list[$key]["weight"] = $value["weight"];

			$list[$key]["date"] = date('Y-m-d H:i:s', $value["date"]);
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}, "datingGift": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.'}}';
	}
	die;

//新增
}elseif($dopost == "Add"){
	checkPurview("datingGiftAdd");

	$pagetitle = "添加新礼物";

	//表单提交
	if($submit == "提交"){

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`title`, `litpic`, `price`, `weight`, `date`) VALUES ('$title', '$litpic', '$price', '$weight', '$pubdate')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if($aid){
			adminLog("添加交友礼物", $title);
			echo '{"state": 100, "id": '.$aid.', "info": '.json_encode("添加成功！").'}';
		}else{
			echo $return;
		}
		die;
	}

//修改
}elseif($dopost == "edit"){
	checkPurview("datingGiftEdit");

	$pagetitle = "修改礼物";

	if($id == "") die('要修改的信息ID传递失败！');
	if($submit == "提交"){

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `title` = '$title', `litpic` = '$litpic', `weight` = '$weight', `price` = '$price' WHERE `id` = ".$id);
		$return = $dsql->dsqlOper($archives, "update");

		if($return == "ok"){
			adminLog("修改交友礼物", $title);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
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

				$title  = $results[0]['title'];
				$litpic = $results[0]['litpic'];
				$weight = $results[0]['weight'];
				$price  = $results[0]['price'];

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
	if(!testPurview("datingGiftDel")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	if($id != ""){

		$each = explode(",", $id);
		$error = array();
		$title = array();
		foreach($each as $val){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");

			//删除缩略图
			array_push($title, $results[0]['title']);
			delPicFile($results[0]['litpic'], "delCard", "siteConfig");

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
			adminLog("删除交友礼物", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
		die;

	}
	die;

//修改应用分类
}elseif($dopost == "updateType"){
	$data = str_replace("\\", '', $_POST['data']);
	if($data == "") die;
	$json = json_decode($data);

	$json = objtoarr($json);
	foreach($json as $key => $val){
		if($val['id'] != ""){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."_type` WHERE `id` = ".$val['id']);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$where = array();
				if($results[0]['weight'] != $val['weight']){
					$where[] = '`weight` = '.$val['weight'];
				}
				if($results[0]['typename'] != $val['val']){
					$where[] = '`typename` = "'.$val['val'].'"';
				}
				if(!empty($where)){
					$archives = $dsql->SetQuery("UPDATE `#@__".$tab."_type` SET ".join(",", $where)." WHERE `id` = ".$val['id']);
					$dsql->dsqlOper($archives, "update");
				}
			}
		}else{
			if(!empty($val['val'])){
				$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."_type` (`typename`, `weight`) VALUES ('".$val['val']."', ".$val['weight'].")");
				$dsql->dsqlOper($archives, "update");
			}
		}
	}
	echo '{"state": 100, "info": '.json_encode("操作成功！").'}';die;

//删除分类
}elseif($dopost == "delType"){
	if(!empty($id)){

		//删除分类下的数据
		$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `typeid` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				delPicFile($value['litpic'], "delCard", "siteConfig");
				delPicFile($value['big'], "delCard", "siteConfig");
			}
		}

		$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."` WHERE `typeid` = ".$id);
		$dsql->dsqlOper($archives, "update");

		$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."_type` WHERE `id` = ".$id);
		$dsql->dsqlOper($archives, "update");
	}
	die;

}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	$huoniaoTag->assign('thumbSize', $cfg_thumbSize);
	$huoniaoTag->assign('thumbType', "*.".str_replace("|", ";*.", $cfg_thumbType));
	$huoniaoTag->assign('pagetitle', $pagetitle);
	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('typeid', (int)$typeid);

	$huoniaoTag->assign('id', (int)$id);
	$huoniaoTag->assign('title', $title);
	$huoniaoTag->assign('litpic', $litpic);
	$huoniaoTag->assign('weight', (int)$weight);
	$huoniaoTag->assign('price', (int)$price);

	$huoniaoTag->assign('typeListArr', json_encode($dsql->getTypeList(0, $tab."_type")));

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
