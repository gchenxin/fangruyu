<?php
/**
 * 管理交友会员相册
 *
 * @version        $Id: datingAlbum.php 2014-7-22 下午16:41:22 $
 * @package        HuoNiao.Dating
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("datingAlbum");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/dating";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
if(empty($userid)) die('会员ID传递失败！');


$tab = "dating_album";

$archives = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = 0 AND `id` = ".$userid);
$results = $dsql->dsqlOper($archives, "results");
$huoniaoTag->assign('check', ($results ? 1 : 0));

if($dopost != ""){
	$templates = "datingAlbumAdd.html";

	//js
	$jsFile = array(
    'ui/jquery.dragsort-0.5.1.min.js',
		'publicUpload.js',
		'admin/dating/datingAlbumAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}else{
	$templates = "datingAlbum.html";

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/jquery-ui-selectable.js',
		'admin/dating/datingAlbum.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}

if($submit == "提交"){
	if($token == "") die('token传递失败！');
	$pubdate = GetMkTime(time());       //发布时间

	//二次验证
	// if(trim($typeid) == ""){
	// 	echo '{"state": 200, "info": "请选择相册分类"}';
	// 	exit();
	// }

	if(trim($imglist) == ""){
		echo '{"state": 200, "info": "请选择要上传的照片"}';
		exit();
	}
}

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	$where = "";

	if($sType != ""){
		$where .= " AND `atype` = ".$sType;
	}

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `type` = 0 AND `uid` = ".$userid);

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
	}

	$where .= " order by `id` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `atype`, `path`, `note`, `state`, `pubdate`, `zan` FROM `#@__".$tab."` WHERE `uid` = ".$userid.$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["atype"] = $value["atype"];

			$typeSql = $dsql->SetQuery("SELECT `title` FROM `#@__dating_album_type` WHERE `id` = ". $value['atype']);
			$typename = $dsql->getTypeName($typeSql);
			$list[$key]["typename"] = $typename[0]['title'];

			$list[$key]["path"] = $value["path"];
			$list[$key]["note"] = $value["note"];
			$list[$key]["state"] = $value["state"];
			$list[$key]["zan"] = $value["zan"];
			$list[$key]["date"] = date('Y-m-d H:i:s', $value["pubdate"]);
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}, "datingAlbum": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}}';
	}
	die;

//新增
}elseif($dopost == "add"){

	$pagetitle = "新增交友会员照片";

	//表单提交
	if($submit == "提交"){

		//保存到主表
		$path = explode(",", $imglist);
		foreach($path as $pic){
			$picInfo = explode("|", $pic);
			$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`uid`, `atype`, `path`, `note`, `state`, `pubdate`) VALUES ('$userid', '$typeid', '".$picInfo[0]."', '".$picInfo[1]."', '1', '$pubdate')");
			$dsql->dsqlOper($archives, "update");
		}

		adminLog("新增交友会员照片", $userid."=>".$pics);
		echo '{"state": 100, "info": '.json_encode("添加成功！").'}';
		die;
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

			//删除缩略图
			array_push($title, $results[0]['id']);
			delPicFile($results[0]['path'], "delAtlas", "dating");

			//删除评论
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."_review` WHERE `aid` = ".$val);
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
			adminLog("删除交友会员相册图片", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
		die;

	}
	die;

//编辑描述
}elseif($dopost == "editNote"){
	if(!empty($id)){
		$note = cn_substrR($note,200);
		$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `note` = '".$note."' WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "update");
		if($results != "ok"){
			echo '{"state": 200, "info": '.json_encode('修改失败！').'}';
		}else{
			adminLog("修改交友会员相册图片描述", $id."=>".$note);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
	}
	die;

//更新状态
}elseif($dopost == "updateState"){
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
			adminLog("更新交友会员相册图片状态", $id."=>".$state);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
	}
	die;

//更新分类
}elseif($dopost == "updateType"){
	$name = $_POST['name'];
	if(!empty($name)){

		//更新
		if($id != ""){
			$archives = $dsql->SetQuery("UPDATE `#@__".$tab."_type` SET `title` = '".$name."' WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				echo '{"state": 200, "info": '.json_encode("更新失败！").'}';
			}else{
				adminLog("更新交友会员相册分类", $id."=>".$name);
				echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
			}

		//新增
		}else{
			$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."_type` (`uid`, `title`) VALUES (".$userid.", '".$name."')");
			$results = $dsql->dsqlOper($archives, "lastid");
			if(!$results){
				echo '{"state": 200, "info": '.json_encode("新增失败！").'}';
			}else{
				adminLog("新增交友会员相册分类", $name);
				echo '{"state": 100, "id": '.$results.', "info": '.json_encode("新增成功！").'}';
			}
		}

	}else{
		echo '{"state": 200, "info": '.json_encode("分类名称不得为空！").'}';
	}
	die;

//删除分类
}elseif($dopost == "delType"){
	if(!empty($id)){
		$archives = $dsql->SetQuery("SELECT `id`, `path` FROM `#@__".$tab."` WHERE `atype` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach($results as $key => $val){
				//删除照片
				delPicFile($val['path'], "delAtlas", "dating");

				//删除评论
				$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."_review` WHERE `aid` = ".$val['id']);
				$dsql->dsqlOper($archives, "update");

				//删除表
				$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."` WHERE `id` = ".$val['id']);
				$dsql->dsqlOper($archives, "update");
			}
		}

		$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."_type` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "update");

		adminLog("删除交友会员相册分类", $id);
		echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
	}
	die;

//移动相册
}elseif($dopost == "move"){

	if(!empty($id) && !empty($mid)){

		$each = explode(",", $id);
		foreach($each as $val){
			$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `atype` = ".$mid." WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
		}

		adminLog("移动交友会员相册图片", $id."=>".$mid);
		echo '{"state": 100, "info": '.json_encode("修改成功！").'}';

	}else{
		echo '{"state": 200, "info": '.json_encode("参数传递失败！").'}';
	}
	die;

}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	require_once(HUONIAOINC."/config/dating.inc.php");
	global $customUpload;
	if($customUpload == 1){
		global $custom_atlasSize;
		global $custom_atlasType;
		$huoniaoTag->assign('atlasSize', $custom_atlasSize);
		$huoniaoTag->assign('atlasType', "*.".str_replace("|", ";*.", $custom_atlasType));
	}
	$huoniaoTag->assign('dopost', $dopost);

	//会员信息
	$huoniaoTag->assign('userid', $userid);

	$huoniaoTag->assign('typeList', getTypeList($userid));

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/dating";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}

function getTypeList($id){
	global $dsql;
	$archives = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__dating_album_type` WHERE `uid` = ".$id);
	$results = $dsql->dsqlOper($archives, "results");
	return $results ? $results : array();
}
