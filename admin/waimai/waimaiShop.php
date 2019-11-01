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
$templates = "waimaiShop.html";


//更新店铺状态
if($action == "updateStatus"){
    if(!empty($id)){

        $val = (int)$val;

        $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `status` = $val WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            echo '{"state": 100, "info": "更新成功！"}';
    		exit();
        }else{
            echo '{"state": 200, "info": "更新失败！"}';
    		exit();
        }

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
		exit();
    }
}


//更新微信下单状态
if($action == "updateValid"){
    if(!empty($id)){

        $val = (int)$val;
        if($id == 'all'){
          // 关闭前记住当前状态
          if($val == 0){
            $sql = $dsql->SetQuery("UPDATE `#@__$dbname` s1, `#@__$dbname` s2 SET s2.`ordervalid_before` = s1.`ordervalid`, s2.`ordervalid` = $val, s2.`closeorder` = '$des' WHERE s1.`id` = s2.`id`");
          // 开启时恢复之前状态
          }else{
            $sql = $dsql->SetQuery("UPDATE `#@__$dbname` s1, `#@__$dbname` s2 SET s2.`ordervalid` = s1.`ordervalid_before` WHERE s1.`id` = s2.`id`");
          }
        }else{
          $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `ordervalid` = $val, `closeorder` = '$des' WHERE `id` = $id");
        }
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            echo '{"state": 100, "info": "更新成功！"}';
    		exit();
        }else{
            echo '{"state": 200, "info": "更新失败！"}';
    		exit();
        }

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
		exit();
    }
}


// 快速编辑
if($action == "fastedit"){
  if(empty($type) || $type == "id" || empty($id) || $val == "") echo '{"state": 200, "info": "参数错误！"}';

  if($type != "shopname" && $type != "sort" && $type != "salesman" && $type != "salesdate") echo '{"state": 200, "info": "操作错误！"}';

  if($type == "shopname"){
    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__$dbname` WHERE `shopname` = '$val' AND `id` != '$id'");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
      die('{"state": 200, "info": "店铺名称已经存在！"}');
    }
  }elseif($type == "salesdate"){
    $val = GetMkTime($val);
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


//删除店铺-移入回收站
if($action == "delete"){
    if(!empty($id)){

        $sql = $dsql->SetQuery("SELECT `shopname` FROM `#@__$dbname` WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "results");
        if(!$ret){
          echo '{"state": 100, "info": "店铺不存在！"}';
          exit();
        }
        $shopname = $ret[0]['shopname'];

        $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `del` = 1 WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "update");

        if($ret == "ok"){
          /*// 商品分类
          $sql = $dsql->SetQuery("UPDATE `#@__waimai_list_type` SET `del` = 1 WHERE `sid` = $id");
          $ret = $dsql->dsqlOper($sql, "update");

          //删除商品
          $sql = $dsql->SetQuery("UPDATE `#@__waimai_list` SET `del` = 1 WHERE `sid` = $id");
          $ret = $dsql->dsqlOper($sql, "update");*/

          echo '{"state": 100, "info": "删除成功！"}';


          adminLog("删除店铺-移入回收站", $id."-".$shopname);
          exit();

        }else{
          echo '{"state": 200, "info": "删除失败！"}';
          exit();
        }

        /*$sql = $dsql->SetQuery("DELETE FROM `#@__$dbname` WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){

            //删除商品分类
            $sql = $dsql->SetQuery("DELETE FROM `#@__waimai_list_type` WHERE `sid` = $id");
            $dsql->dsqlOper($sql, "update");

            //删除商品
            $sql = $dsql->SetQuery("DELETE FROM `#@__waimai_list` WHERE `sid` = $id");
            $dsql->dsqlOper($sql, "update");

            echo '{"state": 100, "info": "删除成功！"}';
        exit();
        }else{
            echo '{"state": 200, "info": "删除失败！"}';
        exit();
        }*/

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
    exit();
    }
}

//彻底删除店铺
if($action == "destory"){
    if(!empty($id)){

        $sql = $dsql->SetQuery("SELECT `shopname` FROM `#@__$dbname` WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "results");
        if(!$ret){
            echo '{"state": 100, "info": "店铺不存在！"}';
            exit();
        }
        $shopname = $ret[0]['shopname'];

        $sql = $dsql->SetQuery("DELETE FROM `#@__$dbname` WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "update");

        if($ret == "ok"){

            echo '{"state": 100, "info": "删除成功！"}';
            adminLog("删除店铺-彻底删除", $id."-".$shopname);
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

//从回收站恢复店铺
if($action == "recycleback"){
    if(!empty($id)){

        $sql = $dsql->SetQuery("SELECT `shopname` FROM `#@__$dbname` WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "results");
        if(!$ret){
          echo '{"state": 100, "info": "店铺不存在！"}';
          exit();
        }
        $shopname = $ret[0]['shopname'];

        $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `del` = 0 WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "update");

        if($ret == "ok"){

          echo '{"state": 100, "info": "恢复成功！"}';

          adminLog("删从回收站恢复店铺", $id."-".$shopname);
          exit();

        }else{
          echo '{"state": 200, "info": "恢复失败！"}';
          exit();
        }

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
    exit();
    }
}


$del = empty($del) ? 0 : 1;
$huoniaoTag->assign('isdel', $del);
$where = " AND s.`del` = $del";

//$where .= " AND cityid in ($adminCityIds)";
if ($cityid){
    $where .= " AND cityid = $cityid";
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

//分站城市
//$where .= " AND s.`cityid` in ($adminCityIds)";
if ($cityid) {
    $where .= " AND s.`cityid` = $cityid";
}

//联系电话
if(!empty($phone)){
  $where .= " AND s.`phone` like ('%$phone%')";
}

//联系地址
if(!empty($address)){
  $where .= " AND s.`address` like ('%$address%')";
}

//业务员
if(!empty($salesman)){
  $where .= " AND s.`salesman` like ('%$salesman%')";
}

$pageSize = 30;

$reg = "(^s.`typeid`$|^s.`typeid`,|,s.`typeid`,|,s.`typeid`)";
$sql = $dsql->SetQuery("SELECT s.`id`, s.`shopname`, s.`sort`, s.`typeid`, s.`phone`, s.`address`, s.`status`, s.`ordervalid`, s.`print_state`, s.`bind_print`, s.`print_config`, s.`salesman`, s.`salesdate`, s.`cityid` FROM `#@__$dbname` s LEFT JOIN `#@__waimai_shop_type` t ON t.`id` in (s.`typeid`) WHERE 1 = 1".$where." ORDER BY s.`sort` DESC, `id` DESC");
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
  $list[$key]['sort']       = $value['sort'];
  $list[$key]['typeid']     = $value['typeid'];
  $list[$key]['phone']      = $value['phone'];
  $list[$key]['address']    = $value['address'];
  $cityname = getSiteCityName($value['cityid']);
  $list[$key]['cityname'] = $cityname;
  $list[$key]['status']     = $value['status'];
  $list[$key]['ordervalid'] = $value['ordervalid'];
  $list[$key]['print_state'] = $value['print_state'];
  $list[$key]['bind_print'] = $value['bind_print'];
  $list[$key]['salesman']   = $value['salesman'];
  $list[$key]['salesdate']  = empty($value['salesdate']) ? '' : date("Y-m-d", $value['salesdate']);
  $list[$key]['print_config'] = unserialize($value['print_config']);



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
$huoniaoTag->assign("phone", $phone);
$huoniaoTag->assign("address", $address);
$huoniaoTag->assign("salesman", $salesman);
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
		'admin/waimai/waimaiShop.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
