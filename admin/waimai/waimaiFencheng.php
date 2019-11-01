<?php
/**
 * 店铺管理 店铺列表
 *
 * @version        $Id: list.php 2017-4-25 上午10:16:21 $
 * @package        HuoNiao.Shop
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', ".." );
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/waimai";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$dbname = "waimai_shop";
$templates = "waimaiFencheng.html";


// 快速编辑
if($action == "fastedit"){
  if(empty($type) || $type == "id" || empty($id) || $val == "") echo '{"state": 200, "info": "参数错误！"}';

  if($type != "shopname" && $type != "sort") echo '{"state": 200, "info": "操作错误！"}';

  if($type == "shopname"){
    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__$dbname` WHERE `shopname` = '$val' AND `id` != '$id'");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
      die('{"state": 200, "info": "店铺名称已经存在！"}');
    }
  }

  $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `$type` = '$val' WHERE `id` = $id");
  // echo $sql;die;
  $ret = $dsql->dsqlOper($sql, "update");
  if($ret == "ok"){
    die('{"state": 100, "info": "修改成功！"}');
  }else{
    die('{"state": 200, "info": "修改失败！"}');
  }
}

$where = " AND `cityid` in (0,$adminCityIds)";

if ($cityid) {
    $where = " AND `cityid` = $cityid";
    $huoniaoTag->assign('cityid', $cityid);
}

//店铺名称
if(!empty($shopname)){
  $where .= " AND s.`shopname` like ('%$shopname%')";
}

//店铺分类
if(!empty($typeid)){
  $reg = "(^$typeid$|^$typeid,|,$typeid,|,$typeid)";
  $where .= " AND s.`typeid` REGEXP '".$reg."' ";
}

//店铺分类
if(!empty($typename)){
  if(is_numeric($typename) && empty($typeid)){
    $reg = "(^$typename$|^$typename,|,$typename,|,$typename)";
    $where .= " AND s.`typeid` REGEXP '".$reg."' ";
  }else{
    $where .= " AND t.`title` like '%$typename%'";
  }
}

$pageSize = 30;

$sql = $dsql->SetQuery("SELECT s.`cityid`, s.`id`, s.`shopname`, s.`fencheng_foodprice`, s.`fencheng_delivery`, s.`fencheng_dabao`, s.`fencheng_addservice`, s.`fencheng_discount`, s.`fencheng_promotion`, s.`fencheng_firstdiscount`, s.`fencheng_offline`, s.`fencheng_quan` FROM `#@__$dbname` s LEFT JOIN `#@__waimai_shop_type` t ON t.`id` in (s.`typeid`) WHERE 1 = 1".$where." ORDER BY s.`sort` DESC, `id` DESC");

//echo $sql;die;
//总条数
$totalCount = $dsql->dsqlOper($sql, "totalCount");
//总分页数
$totalPage = ceil($totalCount/$pageSize);

$p = (int)$p == 0 ? 1 : (int)$p;
$atpage = $pageSize * ($p - 1);
$results = $dsql->dsqlOper($sql." LIMIT $atpage, $pageSize", "results");

$list = array();
foreach ($results as $key => $value) {
  $list[$key]['id']         = $value['id'];
  $list[$key]['shopname']   = $value['shopname'];
  $list[$key]['fencheng_foodprice']   = $value['fencheng_foodprice'];
  $list[$key]['fencheng_delivery']   = $value['fencheng_delivery'];
  $list[$key]['fencheng_dabao']   = $value['fencheng_dabao'];
  $list[$key]['fencheng_addservice']   = $value['fencheng_addservice'];
  $list[$key]['fencheng_discount']   = $value['fencheng_discount'];
  $list[$key]['fencheng_promotion']   = $value['fencheng_promotion'];
  $list[$key]['fencheng_firstdiscount']   = $value['fencheng_firstdiscount'];
  $list[$key]['fencheng_offline']   = $value['fencheng_offline'];
  $list[$key]['fencheng_quan']   = $value['fencheng_quan'];
  $cityname = getSiteCityName($value['cityid']);
  $list[$key]['cityname'] = $cityname;

  // 分类名
  $typeArr = array();
  $typeids = explode(",", $value['typeid']);
  foreach ($typeids as $k => $val) {
    if($val){
      $typeSql = $dsql->SetQuery("SELECT `title` FROM `#@__waimai_shop_type` WHERE `id` = ". $val);
      $type = $dsql->getTypeName($typeSql);
      array_push($typeArr, $type[0]['title']);
    }
  }
  $list[$key]['typename'] = join(" > ", $typeArr);

  $param = array(
      "service"  => "waimai",
      "template" => "shop",
      "id"       => $value['id']
  );
  $list[$key]['url'] = getUrlPath($param);
}

$huoniaoTag->assign("shopname", $shopname);
$huoniaoTag->assign("typename", $typename);

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
		'admin/jquery-ui.css',
		'admin/styles.css',
        'ui/jquery.chosen.css',
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
		'admin/waimai/waimaiFencheng.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
