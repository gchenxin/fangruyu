<?php
/**
 * 配送员管理
 *
 * @version        $Id: waimaiCourier.php 2017-5-26 上午10:46:21 $
 * @package        HuoNiao.Courier
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', ".." );
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/shop";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$dbname = "shop_quan";
$templates = "shopQuan.html";

//删除店铺
if($action == "delete"){
    if(!empty($id)){

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
}elseif ($action == "checkshop"){//查询商品
    if(!empty($keywords)){
        //这里需不需查询当前城市ID商铺的再去查询商品
        $sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__shop_product` WHERE `title` like '%$keywords%'" .$where. "LIMIT 0, 10");
        $result = $dsql->dsqlOper($sql, "results");
        if ($result) {
            echo json_encode($result);
        }
    }
    die;
}



$where = "";

$pageSize = 15;

$sql = $dsql->SetQuery("SELECT * FROM `#@__$dbname` ORDER BY `id` ASC");

//总条数
$totalCount = $dsql->dsqlOper($sql, "totalCount");
//总分页数
$totalPage = ceil($totalCount/$pageSize);

$p = (int)$p == 0 ? 1 : (int)$p;
$atpage = $pageSize * ($p - 1);
$results = $dsql->dsqlOper($sql." LIMIT $atpage, $pageSize", "results");

$list = array();
foreach ($results as $key => $value) {
  foreach ($value as $k => $v) {
      if($k == "btime" || $k == "etime"){
      $v = empty($v) ? "" : date("Y-m-d H:i:s", $v);
    }
    $list[$key][$k] = $v;
  }
    $shopids = $value['product'];
    
    $sql = $dsql->SetQuery("SELECT `title` FROM `#@__shop_product` WHERE `id` = '$shopids'");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
      $list[$key]['shop'] = $ret[0]['title'];
    }else{
      $list[$key]['shop'] = '无';
    }
}
$huoniaoTag->assign("list", $list);

$pagelist = new pagelist(array(
  "list_rows"   => $pageSize,
  "total_pages" => $totalPage,
  "total_rows"  => $totalCount,
  "now_page"    => $p
));
$huoniaoTag->assign("pagelist", $pagelist->show());



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
		'admin/shop/shopQuan.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
