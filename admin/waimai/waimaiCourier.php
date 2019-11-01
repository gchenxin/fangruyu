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
$tpl = dirname(__FILE__)."/../templates/waimai";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$dbname = "waimai_courier";
$templates = "waimaiCourier.html";


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
}


$where = " WHERE c.`cityid` in ($adminCityIds)";

if ($cityid) {
    $where = " WHERE c.`cityid` = $cityid";
    $huoniaoTag->assign('cityid', $cityid);
}

$pageSize = 15;

$sql = $dsql->SetQuery("SELECT c.`id`, c.`name`, c.`username`, c.`phone`, c.`age`, c.`sex`, c.`photo`, c.`lng`, c.`lat`, c.`quit`, (SELECT count(`id`) FROM `#@__waimai_order` WHERE `peisongid` = c.`id`) as total, (SELECT count(`id`) FROM `#@__waimai_order` WHERE `peisongid` = c.`id` AND `state` = 1) as ok, (SELECT count(`id`) FROM `#@__waimai_order` WHERE `peisongid` = c.`id` AND (`state` = 6 OR `state` = 7)) as failed, c.`state`  FROM `#@__$dbname` c ".$where." ORDER BY c.`quit` ASC, c.`id` ASC");

//总条数
$totalCount = $dsql->dsqlOper($sql, "totalCount");
//总分页数
$totalPage = ceil($totalCount/$pageSize);

$p = (int)$p == 0 ? 1 : (int)$p;
$atpage = $pageSize * ($p - 1);
$results = $dsql->dsqlOper($sql." LIMIT $atpage, $pageSize", "results");

$list = array();
foreach ($results as $key => $value) {
  $list[$key]['id']       = $value['id'];
  $list[$key]['name']     = $value['name'];
  $list[$key]['username'] = $value['username'];
  $list[$key]['phone']    = $value['phone'];
  $list[$key]['age']      = $value['age'];
  $list[$key]['sex']      = $value['sex'];
  $list[$key]['photo']    = $value['photo'];
  $list[$key]['lng']      = $value['lng'];
  $list[$key]['lat']      = $value['lat'];
  $list[$key]['total']    = $value['total'];
  $list[$key]['ok']       = $value['ok'];
  $list[$key]['failed']   = $value['failed'];
  $list[$key]['state']    = $value['state'];
  $list[$key]['quit']    = $value['quit'];
}
$huoniaoTag->assign("list", $list);

$pagelist = new pagelist(array(
  "list_rows"   => $pageSize,
  "total_pages" => $totalPage,
  "total_rows"  => $totalCount,
  "now_page"    => $p
));
$huoniaoTag->assign("pagelist", $pagelist->show());

$huoniaoTag->assign('city', $adminCityArr);

//验证模板文件
if(file_exists($tpl."/".$templates)){

    //css
	$cssFile = array(
        'ui/jquery.chosen.css',
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
        'ui/chosen.jquery.min.js',
		'admin/waimai/waimaiCourier.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
