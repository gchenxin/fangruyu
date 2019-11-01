<?php
/**
 * 店铺管理 店铺分类
 *
 * @version        $Id: type.php 2017-4-25 上午10:16:21 $
 * @package        HuoNiao.Shop
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "../" );
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/waimai";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "waimaiType.html";

$dbname = "waimai_shop_type";

//删除店铺分类
if($action == "delete"){
    if(!empty($id)){
        $sql = $dsql->SetQuery("SELECT `icon` FROM `#@__$dbname` WHERE `id` = $id");
        $res = $dsql->dsqlOper($sql, "results");
        if(!$res){
            echo '{"state": 200, "info": "分类不存在！"}';
            exit();
        }
        if($res[0]['icon']){
            delPicFile($res[0]['icon'], "deladvthumb", "waimai");
        }

        $sql = $dsql->SetQuery("DELETE FROM `#@__$dbname` WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            echo '{"state": 100, "info": "删除成功！"}';
    		exit();
        }else{
            echo '{"state": 200, "info": "删除失败！"}';
    		exit();
        }

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
		exit();
    }
}


//验证模板文件
if(file_exists($tpl."/".$templates)){


    //css
	$cssFile = array(
		'admin/jquery-ui.css',
		'admin/styles.css',
		'admin/chosen.min.css',
		'admin/ace-fonts.min.css',
		'admin/select.css',
		'admin/ace.min.css',
		'admin/animate.css',
		'admin/font-awesome.min.css',
		'admin/simple-line-icons.css',
		'admin/font.css',
		// 'admin/app.css'
	);
	$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));

    //js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'admin/waimai/waimaiType.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));


    $list = array();
    $sql = $dsql->SetQuery("SELECT * FROM `#@__$dbname` ORDER BY `sort` DESC, `id` DESC");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        foreach ($ret as $key => $value) {
            $list[$key]['id'] = $value['id'];
            $list[$key]['title'] = $value['title'];
            $list[$key]['sort'] = $value['sort'];
        }
    }

    $huoniaoTag->assign('sid', $sid);
    $huoniaoTag->assign('shopname', $shopname);
    $huoniaoTag->assign('list', $list);

	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
