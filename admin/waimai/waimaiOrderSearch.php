<?php
/**
 * 订单管理
 *
 * @version        $Id: order.php 2017-5-25 上午10:16:21 $
 * @package        HuoNiao.Order
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', ".." );
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/waimai";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$dbname = "waimai_order";
$templates = "waimaiOrderSearch.html";

// 搜索结果 或导出
if($action == "search" || $action == "export"){

    $where = "";

    $where = " AND s.`cityid` in ($adminCityIds)";
    if ($cityid){
        $where = " AND s.`cityid` = $cityid";
    }

    //订单编号
    if(!empty($ordernum)){
      $where .= " AND (o.`ordernum` like '%$ordernum%' OR o.`ordernumstore` like '%$ordernum%')";
    }

    //店铺名称
    if(!empty($shopname)){
      $where .= " AND s.`shopname` like '%$shopname%'";
    }

    //店铺ID
    if(!empty($shopid)){
      $where .= " AND o.`sid` = $shopid";
    }

    //姓名
    if(!empty($person)){
      $where .= " AND o.`person` LIKE '%$person%'";
    }

    //顾客ID
    if(!empty($personId)){
      $checkMore = true;
      if(is_numeric($personId)){
        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` = $personId");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
          $where .= " AND o.`uid` = $personId";
          $checkMore = false;
        }
      }

      if($checkMore){
        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` LIKE '%$personId%' || `phone` LIKE '%$personId%' || `email` LIKE '%$personId%' || `nickname` LIKE '%$personId%'");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
          $uids = arr_foreach($ret);
          $where .= " AND o.`uid` IN (".join(",", $uids).")";
        }else{
          $where .= " AND 1 = 2";
        }
      }
    }

    //电话
    if(!empty($tel)){
      $where .= " AND o.`tel` like '%$tel%'";
    }

    //收货地址
    if(!empty($address)){
      $where .= " AND o.`address` like '%$address%'";
    }

    //下单时间
    if(!empty($paydate)){
      $start = $paydate[0];
      $end = $paydate[1];

      $where1 = "";
      if(!empty($start)){
        $start = GetMkTime($start);
        $where1 = "o.`pubdate` >= $start";
      }
      if(!empty($end)){
        $end = GetMkTime($end);
        $where1 = $where1 == "" ? "o.`pubdate` <= $end" : ($where1." AND "."o.`pubdate` <= $end");
      }

      if($where1 != ""){
        $where .= " AND (".$where1.")";
      }
    }

    //配送员
    if(!empty($peisongid)){
      $where .= " AND o.`peisongid` = $peisongid";
    }

    //支付方式
    if(!empty($paytype)){
        if($paytype == 'online'){
            $where .= " AND (o.`paytype` = 'alipay' || `paytype` = 'wxpay')";
        }else{
            $where .= " AND O.`paytype` = '$paytype'";
        }
    }

    //订单金额
    if(!empty($amount)){
        $min = $amount[0];
        $max = $amount[1];

        $min = $min ? (int)$min : 0;
        $max = $max ? (int)$max : 0;

        $where1 = "";

        if(!empty($min)){
          $where1 = "o.`amount` >= $min";
        }
        if(!empty($max)){
          $where1 = $where1 == "" ? "o.`amount` <= $max" : ($where1." AND "."o.`amount` <= $max");
        }

        if($where1 != ""){
          $where .= " AND (".$where1.")";
        }

    }

    //订单状态
    if($state != ""){
        $where .= " AND o.`state` = '$state'";
    }

    //完成时间
    if($comtime && $state != 1){
        $time = $comtime * 60;
        $where .= " AND (o.`state` = 1 && (o.`okdate` - o.`paydate` <= $time))";
    }



    $list = array();
    $pageSize = $action == "export" ? 99999999999 : 15;

    $sql = $dsql->SetQuery("SELECT o.`id`, o.`uid`, o.`sid`, o.`ordernum`, o.`ordernumstore`, o.`state`, o.`food`, o.`person`, o.`tel`, o.`address`, o.`paytype`, o.`preset`, o.`note`, o.`pubdate`, o.`okdate`, o.`amount`, o.`peisongid`, o.`peisongidlog`, o.`failed`, s.`shopname` FROM `#@__$dbname` o LEFT JOIN `#@__waimai_shop` s ON s.`id` = o.`sid` WHERE 1 = 1".$where." ORDER BY o.`id` DESC");
    // echo $sql;

    //总条数
    $totalCount = $dsql->dsqlOper($sql, "totalCount");

    if($totalCount == 0){

      $huoniaoTag->assign("list", $list);

    }else{

      //总分页数
      $totalPage = ceil($totalCount/$pageSize);

      $p = (int)$p == 0 ? 1 : (int)$p;
      $atpage = $pageSize * ($p - 1);
      $results = $dsql->dsqlOper($sql." LIMIT $atpage, $pageSize", "results");

      foreach ($results as $key => $value) {
        $list[$key]['id']         = $value['id'];
        $list[$key]['uid']        = $value['uid'];

        //用户名
        $userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $value["uid"]);
        $username = $dsql->dsqlOper($userSql, "results");
        if(count($username) > 0){
            $list[$key]["username"] = $username[0]['username'];
        }else{
            $list[$key]["username"] = "未知";
        }

        $list[$key]['sid']           = $value['sid'];
        $list[$key]['shopname']      = $value['shopname'];
        $list[$key]['ordernum']      = $value['ordernum'];
        $list[$key]['ordernumstore'] = $value['ordernumstore'];
        $list[$key]['state']         = $value['state'];
        $list[$key]['food']          = unserialize($value['food']);
        $list[$key]['person']        = $value['person'];
        $list[$key]['tel']           = $value['tel'];
        $list[$key]['address']       = $value['address'];
        // $list[$key]['paytype']       = $value['paytype'] == "wxpay" ? "微信支付" : ($value['paytype'] == "alipay" ? "支付宝" : $value['paytype']);
        $list[$key]['paytype']      = $value['paytype'] == "wxpay" ? "微信支付" : ($value['paytype'] == "alipay" ? "支付宝" : ($value['paytype'] == "money" ? "余额支付" : ($value['paytype'] == "delivery" ? "货到付款" : $value['paytype']) ) );
        $list[$key]['preset']        = unserialize($value['preset']);
        $list[$key]['note']          = $value['note'];
        $list[$key]['pubdate']       = $value['pubdate'];
        $list[$key]['okdate']        = $value['okdate'];
        $list[$key]['amount']        = $value['amount'];
        $list[$key]['peisongid']     = $value['peisongid'];
        $list[$key]['peisongidlog']  = $value['peisongidlog'] ? substr($value['peisongidlog'], 0, -4) : "";
        $list[$key]['failed']        = $value['failed'];

        $sql = $dsql->SetQuery("SELECT `name`, `phone` FROM `#@__waimai_courier` WHERE `id` = ".$value['peisongid']);
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $list[$key]['peisongname'] = $ret[0]['name'];
            $list[$key]['peisongtel'] = $ret[0]['phone'];
        }
      }

      $huoniaoTag->assign("state", $state);
      $huoniaoTag->assign("list", $list);

      $pagelist = new pagelist(array(
        "list_rows"   => $pageSize,
        "total_pages" => $totalPage,
        "total_rows"  => $totalCount,
        "now_page"    => $p
      ));
      $huoniaoTag->assign("pagelist", $pagelist->show());

    }

    // 导出表格
    if($action == "export"){

      if(empty($list)){
        echo '{"state": 200, "info": "暂无数据！"}';
        die;
      }


      include HUONIAOROOT.'/include/class/PHPExcel/PHPExcel.php';
      include HUONIAOROOT.'/include/class/PHPExcel/PHPExcel/Writer/Excel2007.php';
      //或者include 'PHPExcel/Writer/Excel5.php'; 用于输出.xls 的
      // 创建一个excel
      $objPHPExcel = new PHPExcel();

      // Set document properties
      $objPHPExcel->getProperties()->setCreator("Phpmarker")->setLastModifiedBy("Phpmarker")->setTitle("Phpmarker")->setSubject("Phpmarker")->setDescription("Phpmarker")->setKeywords("Phpmarker")->setCategory("Phpmarker");

      $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A1', '订单编号')
      ->setCellValue('B1', '店铺')
      ->setCellValue('C1', '顾客ID')
      ->setCellValue('D1', '姓名')
      ->setCellValue('E1', '电话')
      ->setCellValue('F1', '配送地址')
      ->setCellValue('G1', '详情')
      ->setCellValue('H1', '备注')
      ->setCellValue('I1', '总价')
      ->setCellValue('J1', '配送员')
      ->setCellValue('K1', '下单时间')
      ->setCellValue('L1', '完成时间')
      ->setCellValue('M1', '付款方式');


      // 表名
      $tabname = "订单表";
      $objPHPExcel->getActiveSheet()->setTitle($tabname);

      // 将活动表索引设置为第一个表，因此Excel将作为第一个表打开此表
      $objPHPExcel->setActiveSheetIndex(0);
      // 所有单元格默认高度
      $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
      // 冻结窗口
      $objPHPExcel->getActiveSheet()->freezePane('A2');

      // 从第二行开始
      $row = 2;

      foreach($list as $data){

        // 详情
        $foods = array();
        if(isset($data['food'])){
          foreach($data['food'] as $food){
            array_push($foods, $food['title']."【".$food['count']."】");
          }
        }

        $objPHPExcel->getActiveSheet()->setCellValue("A".$row, $data['ordernumstore']);
        $objPHPExcel->getActiveSheet()->setCellValue("B".$row, $data['shopname']);
        $objPHPExcel->getActiveSheet()->setCellValue("C".$row, $data['username']);
        $objPHPExcel->getActiveSheet()->setCellValue("D".$row, $data['person']);
        $objPHPExcel->getActiveSheet()->setCellValue("E".$row, $data['tel']);
        $objPHPExcel->getActiveSheet()->setCellValue("F".$row, $data['address']);
        $objPHPExcel->getActiveSheet()->setCellValue("G".$row, join(",", $foods));
        $objPHPExcel->getActiveSheet()->setCellValue("H".$row, $data['note']);
        $objPHPExcel->getActiveSheet()->setCellValue("I".$row, $data['amount']);
        $objPHPExcel->getActiveSheet()->setCellValue("J".$row, $data['peisongname']);
        $objPHPExcel->getActiveSheet()->setCellValue("K".$row, date("Y-m-d H:i:s", $data['pubdate']));
        $objPHPExcel->getActiveSheet()->setCellValue("L".$row, $data['okdate'] ? date("Y-m-d H:i:s", $data['okdate']) : "");
        $objPHPExcel->getActiveSheet()->setCellValue("M".$row, $data['paytype']);

        $row++ ;
      }
      $objActSheet = $objPHPExcel->getActiveSheet();

      // 设置CELL填充颜色
      /*
      $cell_fill = array(
        'A1',
        'B1',
        'C1',
        'D1',
        'E1',
      );
      foreach($cell_fill as $cell_fill_val){
       $cellstyle = $objActSheet->getStyle($cell_fill_val);
       // background
       // $cellstyle->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('fafa00');
       // set align
       $cellstyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
       // 字体
       $cellstyle->getFont()->setSize(12)->setBold(false);
       // border
       $cellstyle->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)->getColor()->setARGB('f60');
       $cellstyle->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)->getColor()->setARGB('FFFF0000');
       $cellstyle->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)->getColor()->setARGB('FFFF0000');
       $cellstyle->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)->getColor()->setARGB('FFFF0000');
      }
      */

      // 行高
      // $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

      // 列宽
      $objPHPExcel->getActiveSheet()->getColumnDimension()->setAutoSize(true);
      /*$objActSheet->getColumnDimension('A')->setWidth(18.5);
      $objActSheet->getColumnDimension('B')->setWidth(23.5);
      $objActSheet->getColumnDimension('C')->setWidth(12);
      $objActSheet->getColumnDimension('D')->setWidth(12);
      $objActSheet->getColumnDimension('E')->setWidth(12);*/

      $filename = date("YmdHis").time().".csv";
      $dir = "/uploads/waimai/export";
      $pathStr  = HUONIAOROOT.$dir;
      if ( !file_exists( $pathStr ) ) {
          if ( !mkdir( $pathStr , 0777 , true ) ) {
            echo '{"state": 200, "info": "导出失败！"}';
            return false;
          }
      }
      $saveurl = $pathStr."/".$filename;
      $downloadurl = $cfg_secureAccess.$cfg_basehost.$dir."/".$filename;
      $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
      $objWriter->save($saveurl);
      echo '{"state": 100, "info": "'.$downloadurl.'"}';
      die;

    }



// 搜索表单
}else{

    // 店铺列表
    $shop = array();
    $sql = $dsql->SetQuery("SELECT s.`id`, s.`shopname` FROM `#@__waimai_shop` s WHERE 1 = 1");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        $shop = $ret;
    }
    $huoniaoTag->assign('shop', $shop);

    //配送员
    $courier = array();
    $sql = $dsql->SetQuery("SELECT `id`, `name` FROM `#@__waimai_courier` WHERE `state` = 1 ORDER BY `id` DESC");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        foreach ($ret as $key => $value) {
            array_push($courier, array(
                "id" => $value['id'],
                "name" => $value['name']
            ));
        }
    }
    $huoniaoTag->assign("courier", $courier);


    // 完成时间 分钟
    for($i = 1; $i <=60; $i++){
        $comtime[] = $i;
    }
    $huoniaoTag->assign("comtime", $comtime);

}

$huoniaoTag->assign("action", $action);

$huoniaoTag->assign('city', $adminCityArr);

//验证模板文件
if(file_exists($tpl."/".$templates)){

  //css
	$cssFile = array(
		'admin/jquery-ui.css',
		'admin/styles.css',
		'admin/ace-fonts.min.css',
		'admin/select.css',
		'admin/ace.min.css',
		'admin/animate.css',
		'admin/font-awesome.min.css',
		'admin/simple-line-icons.css',
    'ui/jquery.chosen.css',
    'admin/chosen.min.css',
		'admin/font.css',
		// 'admin/app.css'
	);
	$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));

    //配送员
    $courier = array();
    $sql = $dsql->SetQuery("SELECT `id`, `name` FROM `#@__waimai_courier` WHERE `state` = 1 ORDER BY `id` DESC");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        foreach ($ret as $key => $value) {
            array_push($courier, array(
                "id" => $value['id'],
                "name" => $value['name']
            ));
        }
    }
    $huoniaoTag->assign("courier", $courier);

    $personId = empty($personId) ? 0 : $personId;
    $huoniaoTag->assign("personId", $personId);

    //js
	$jsFile = array(
		'ui/bootstrap.min.js',
    'ui/jquery-ui.min.js',
    'ui/jquery.form.js',
		'ui/chosen.jquery.min.js',
    'ui/jquery-ui-timepicker-addon.js',
		'admin/waimai/waimaiOrderSearch.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
