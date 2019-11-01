<?php
/**
 * 外卖统计
 *
 * @version        $Id: waimaiStatistics.php 2017-6-18 上午12:27:19 $
 * @package        HuoNiao.Waimai
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', ".." );
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/waimai";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$templates = "waimaiStatistics.html";
$action = empty($action) ? "chartrevenue" : $action;


//查询所有店铺
$shopArr = array();
$where2 = " AND cityid in ($adminCityIds)";
if ($cityid){
    $where2 = " AND cityid = $cityid";
}
$sql = $dsql->SetQuery("SELECT `id`, `shopname` FROM `#@__waimai_shop` WHERE 1=1".$where2." ORDER BY `id`");
$ret = $dsql->dsqlOper($sql, "results");
if($ret){
    $shopArr = $ret;
}
$huoniaoTag->assign("shop_id", $shop_id);
$huoniaoTag->assign("shopArr", $shopArr);

$huoniaoTag->assign('cityid', (int)$cityid);

//查询指定城市信息
$cityname = "";
if(!empty($cityid)){
    $cityname = getSiteCityName($cityid);
}
$huoniaoTag->assign("cityname", $cityname);

//查询指定店铺信息
$shopname = "";
if(!empty($shop_id)){
    $sql = $dsql->SetQuery("SELECT `shopname` FROM `#@__waimai_shop` WHERE `id` = $shop_id");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        $shopname = $ret[0]['shopname'];
    }
}
$huoniaoTag->assign("shopname", $shopname);


//查询所有配送员
$courierArr = array();
$where2 = " AND cityid in ($adminCityIds)";
if ($cityid){
    $where2 = " AND cityid = $cityid";
}
$sql = $dsql->SetQuery("SELECT `id`, `name` FROM `#@__waimai_courier` WHERE 1=1".$where2." ORDER BY `id`");
$ret = $dsql->dsqlOper($sql, "results");
if($ret){
    $courierArr = $ret;
}
$huoniaoTag->assign("courier_id", $courier_id);
$huoniaoTag->assign("courierArr", $courierArr);

//查询指定配送员信息
$couriername = "";
if(!empty($courier_id)){
    $sql = $dsql->SetQuery("SELECT `name` FROM `#@__waimai_courier` WHERE `id` = $courier_id");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        $couriername = $ret[0]['name'];
    }
}
$huoniaoTag->assign("couriername", $couriername);


//最近一月时间
// $nowDate = $endDate ? $endDate : ($action == "chartordertime" || $action == "chartcourier" ? date("Y-m-d") . " 23:59:59" : date("Y-m-d"));
// $lastMonthDate = $beginDate ? $beginDate : ($action == "chartordertime" || $action == "chartcourier" ? date("Y-m-d", strtotime("-31 day")) . " 00:00:00" : date("Y-m-d", strtotime("-31 day")));



$timeArr = array();
$dataArr = $priceArr = array();

if($endDate){
    $nowDate = $endDate;
}else{
    if($action == "chartordertime" || $action == "chartcourier"){
        $nowDate = date("Y-m-d") . " 23:59:59";
    }else{
        $nowDate = date("Y-m-d");
    }
}

if($beginDate){
    $lastMonthDate = $beginDate;
}else{
    if($action == "chartordertime" || $action == "chartcourier"){
        $lastMonthDate = date("Y-m-d", strtotime("-31 day")) . " 00:00:00";
    }else{
        $lastMonthDate = date("Y-m-d", strtotime("-31 day"));
    }
}





$huoniaoTag->assign("nowDate", $nowDate);
$huoniaoTag->assign("lastMonthDate", $lastMonthDate);

$begintime = strtotime($lastMonthDate);
$endtime = strtotime($nowDate);
for($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
    array_push($timeArr, date("m-d", $start));
}
$huoniaoTag->assign("timeArr", json_encode($timeArr));

$failedArr = array();

if($dopost == "getresults"){

    //外卖营业额统计
    if($action == "chartrevenue"){

        for($start = $endtime; $start >= $begintime; $start -= 24 * 3600) {

            $time1 = $start;
            $time2 = $start + 86400;

            $where = "";

            $where2 = " AND cityid in ($adminCityIds)";
            if ($cityid){
                $where2 = " AND cityid = $cityid";
            }
            $shopid = array();
            $shopSql = $dsql->SetQuery("SELECT `id` FROM `#@__waimai_shop` WHERE 1=1".$where2);
            $shopResult = $dsql->dsqlOper($shopSql, "results");
            if($shopResult){
                foreach($shopResult as $key => $loupan){
                    array_push($shopid, $loupan['id']);
                }
                $where = " AND `sid` in (".join(",", $shopid).")";
            }

            if($shop_id){
                $where = " AND `sid` = $shop_id";
            }

            //总交易额
            $sql = $dsql->SetQuery("SELECT `food`, `priceinfo` FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $foodTotal = $peisongTotal = $dabaoTotal = $addserviceTotal = $discountTotal = $promotionTotal = $firstdiscountTotal = $zjye = $youhuiquanTotal = 0;
                foreach ($ret as $key => $value) {
                    $food = unserialize($value['food']);
                    $priceinfo = empty($value['priceinfo']) ? array() : unserialize($value['priceinfo']);

                    //计算单个订单的商品原价
                    foreach ($food as $k_ => $v_) {
                        $foodTotal += sprintf("%.2f", $v_['price'] * $v_['count']);
                    }

                    //费用详情
                    if($priceinfo){
                        foreach ($priceinfo as $k_ => $v_) {
                            if($v_['type'] == "peisong"){
                                $peisongTotal += sprintf("%.2f", $v_['amount']);
                            }
                            if($v_['type'] == "dabao"){
                                $dabaoTotal += sprintf("%.2f", $v_['amount']);
                            }
                            if($v_['type'] == "fuwu"){
                                $addserviceTotal += sprintf("%.2f", $v_['amount']);
                            }
                            if($v_['type'] == "youhui"){
                                $discountTotal += sprintf("%.2f", $v_['amount']);
                            }
                            if($v_['type'] == "manjian"){
                                $promotionTotal += sprintf("%.2f", $v_['amount']);
                            }
                            if($v_['type'] == "shoudan"){
                                $firstdiscountTotal += sprintf("%.2f", $v_['amount']);
                            }
                            if($v_['type'] == "quan"){
                                $youhuiquanTotal += -sprintf("%.2f", $v_['amount']);
                            }
                        }
                    }

                }

                //计算总交易额
                $zjye = $foodTotal - $discountTotal - $promotionTotal - $firstdiscountTotal + $dabaoTotal + $peisongTotal + $addserviceTotal - $youhuiquanTotal;
            
            }else{
                $zjye = 0;
            }

            $total = array(array("total" => $zjye));

            //货到付款
            $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `paytype` = 'delivery' AND `state` = 1 AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $delivery = $dsql->dsqlOper($sql, "results");

            //余额支付
            $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `paytype` = 'money' AND `state` = 1 AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $money = $dsql->dsqlOper($sql, "results");

            //在线支付
            $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE (`paytype` = 'wxpay' OR `paytype` = 'alipay' OR `paytype` = 'unionpay') AND `state` = 1 AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $online = $dsql->dsqlOper($sql, "results");

            // 总交易额
            // $total = array(array("total" => $delivery[0]['total'] + $money[0]['total'] + $online[0]['total']));

            //其他费用统计
            $dabao = $peisong = $fuwu = $shoudan = $youhuiquan = 0;
            $sql = $dsql->SetQuery("SELECT `priceinfo` FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                foreach ($ret as $key => $value) {
                    $priceinfo = empty($value['priceinfo']) ? array() : unserialize($value['priceinfo']);
                    if($priceinfo){
                        foreach ($priceinfo as $k => $v) {
                            if($v['type'] == "dabao"){
                                $dabao += $v['amount'];
                            }
                            if($v['type'] == "peisong"){
                                $peisong += $v['amount'];
                            }
                            if($v['type'] == "fuwu"){
                                $fuwu += $v['amount'];
                            }
                            if($v['type'] == "shoudan"){
                                $shoudan += $v['amount'];
                            }
                            if($v['type'] == "quan"){
                                $youhuiquan += -$v['amount'];
                            }
                        }
                    }
                }
            }

            array_push($dataArr, array(
                "date"     => date("Y-m-d", $start),
                "total"    => sprintf("%.2f", $total[0]['total']),
                "delivery" => sprintf("%.2f", $delivery[0]['total']),
                "money"    => sprintf("%.2f", $money[0]['total']),
                "online"   => sprintf("%.2f", $online[0]['total']),
                "dabao"    => sprintf("%.2f", $dabao),
                "peisong"    => sprintf("%.2f", $peisong),
                "fuwu"    => sprintf("%.2f", $fuwu),
                "shoudan"    => sprintf("%.2f", $shoudan),
                "youhuiquan"    => sprintf("%.2f", $youhuiquan),
            ));

            array_push($priceArr, sprintf("%.2f", $total[0]['total']));

        }


        //导出
        if($do == "export"){

            $tablename = (empty($cityname) ? "全部店铺" : $cityname) . "营业额统计";
            $tablename = (empty($shopname) ? "全部店铺" : $shopname) . "营业额统计";
            $tablename = iconv("UTF-8", "GB2312//IGNORE", $tablename);

            include HUONIAOROOT.'/include/class/PHPExcel/PHPExcel.php';
            include HUONIAOROOT.'/include/class/PHPExcel/PHPExcel/Writer/Excel2007.php';
            //或者include 'PHPExcel/Writer/Excel5.php'; 用于输出.xls 的
            // 创建一个excel
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Phpmarker")->setLastModifiedBy("Phpmarker")->setTitle("Phpmarker")->setSubject("Phpmarker")->setDescription("Phpmarker")->setKeywords("Phpmarker")->setCategory("Phpmarker");

            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '时间')
            ->setCellValue('B1', '总营业额')
            ->setCellValue('C1', '货到付款')
            ->setCellValue('D1', '余额支付')
            ->setCellValue('E1', '在线支付')
            ->setCellValue('F1', '餐盒费')
            ->setCellValue('G1', '配送费')
            ->setCellValue('H1', '增值服务费统计')
            ->setCellValue('I1', '首单立减总金额')
            ->setCellValue('J1', '使用优惠券总额');


            // 表名
            $tabname = "外卖营业额统计";
            $objPHPExcel->getActiveSheet()->setTitle($tabname);

            // 将活动表索引设置为第一个表，因此Excel将作为第一个表打开此表
            $objPHPExcel->setActiveSheetIndex(0);
            // 所有单元格默认高度
            $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
            // 冻结窗口
            $objPHPExcel->getActiveSheet()->freezePane('A2');

            // 从第二行开始
            $row = 2;

            $total = $delivery = $money = $online = $dabao = $peisong = $fuwu = $shoudan = $youhuiquan = 0;
            foreach($dataArr as $data){
              $objPHPExcel->getActiveSheet()->setCellValue("A".$row, $data['date']);
              $objPHPExcel->getActiveSheet()->setCellValue("B".$row, $data['total']);
              $objPHPExcel->getActiveSheet()->setCellValue("C".$row, $data['delivery']);
              $objPHPExcel->getActiveSheet()->setCellValue("D".$row, $data['money']);
              $objPHPExcel->getActiveSheet()->setCellValue("E".$row, $data['online']);
              $objPHPExcel->getActiveSheet()->setCellValue("F".$row, $data['dabao']);
              $objPHPExcel->getActiveSheet()->setCellValue("G".$row, $data['peisong']);
              $objPHPExcel->getActiveSheet()->setCellValue("H".$row, $data['fuwu']);
              $objPHPExcel->getActiveSheet()->setCellValue("I".$row, $data['shoudan']);
              $objPHPExcel->getActiveSheet()->setCellValue("J".$row, $data['youhuiquan']);
              $row++;

              $total += $data['total'];
              $delivery += $data['delivery'];
              $money += $data['money'];
              $online += $data['online'];
              $dabao += $data['dabao'];
              $peisong += $data['peisong'];
              $fuwu += $data['fuwu'];
              $shoudan += $data['shoudan'];
              $youhuiquan += $data['youhuiquan'];
            }

            $objPHPExcel->getActiveSheet()->setCellValue("A".$row, "总计");
            $objPHPExcel->getActiveSheet()->setCellValue("B".$row, $total);
            $objPHPExcel->getActiveSheet()->setCellValue("C".$row, $delivery);
            $objPHPExcel->getActiveSheet()->setCellValue("D".$row, $money);
            $objPHPExcel->getActiveSheet()->setCellValue("E".$row, $online);
            $objPHPExcel->getActiveSheet()->setCellValue("F".$row, $dabao);
            $objPHPExcel->getActiveSheet()->setCellValue("G".$row, $peisong);
            $objPHPExcel->getActiveSheet()->setCellValue("H".$row, $fuwu);
            $objPHPExcel->getActiveSheet()->setCellValue("I".$row, $shoudan);
            $objPHPExcel->getActiveSheet()->setCellValue("J".$row, $youhuiquan);

            $objActSheet = $objPHPExcel->getActiveSheet();

            // 列宽
            $objPHPExcel->getActiveSheet()->getColumnDimension()->setAutoSize(true);

            $filename = $tablename."__".$lastMonthDate."__".$nowDate.".csv";
            ob_end_clean();//清除缓冲区,避免乱码
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $objWriter->save('php://output');
            die;

        }

    }


    //订单按天统计
    elseif($action == "chartorder"){

        for($start = $endtime; $start >= $begintime; $start -= 24 * 3600) {

            $time1 = $start;
            $time2 = $start + 86400;

            $where = "";

            $where2 = " AND cityid in ($adminCityIds)";
            if ($cityid){
                $where2 = " AND cityid = $cityid";
            }
            $shopid = array();
            $shopSql = $dsql->SetQuery("SELECT `id` FROM `#@__waimai_shop` WHERE 1=1".$where2);
            $shopResult = $dsql->dsqlOper($shopSql, "results");
            if($shopResult){
                foreach($shopResult as $key => $loupan){
                    array_push($shopid, $loupan['id']);
                }
                $where = " AND `sid` in (".join(",", $shopid).")";
            }

            if($shop_id){
                $where = " AND `sid` = $shop_id";
            }

            //成功订单数
            $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $success = $dsql->dsqlOper($sql, "results");

            //货到付款成功订单数
            $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE `paytype` = 'delivery' AND `state` = 1 AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $delivery = $dsql->dsqlOper($sql, "results");

            //余额支付
            $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE `paytype` = 'money' AND `state` = 1 AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $money = $dsql->dsqlOper($sql, "results");

            //在线支付
            $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE (`paytype` = 'wxpay' OR `paytype` = 'alipay' OR `paytype` = 'unionpay') AND `state` = 1 AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $online = $dsql->dsqlOper($sql, "results");

            array_push($dataArr, array(
                "date"     => date("Y-m-d", $start),
                "success"    => $success[0]['total'],
                "delivery" => $delivery[0]['total'],
                "money"    => $money[0]['total'],
                "online"   => $online[0]['total']
            ));

            array_push($priceArr, sprintf("%.2f", $success[0]['total']));

        }


        //导出
        if($do == "export"){

            $tablename = (empty($cityname) ? "全部店铺" : $cityname) . "订单统计";
            $tablename = (empty($shopname) ? "全部店铺" : $shopname) . "订单统计";
            $tablename = iconv("UTF-8", "GB2312//IGNORE", $tablename);

            include HUONIAOROOT.'/include/class/PHPExcel/PHPExcel.php';
            include HUONIAOROOT.'/include/class/PHPExcel/PHPExcel/Writer/Excel2007.php';
            //或者include 'PHPExcel/Writer/Excel5.php'; 用于输出.xls 的
            // 创建一个excel
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Phpmarker")->setLastModifiedBy("Phpmarker")->setTitle("Phpmarker")->setSubject("Phpmarker")->setDescription("Phpmarker")->setKeywords("Phpmarker")->setCategory("Phpmarker");

            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '时间')
            ->setCellValue('B1', '成功订单数')
            ->setCellValue('C1', '货到付款成功订单数')
            ->setCellValue('D1', '余额付款成功订单数')
            ->setCellValue('E1', '在线支付成功订单数');


            // 表名
            $tabname = "订单统计";
            $objPHPExcel->getActiveSheet()->setTitle($tabname);

            // 将活动表索引设置为第一个表，因此Excel将作为第一个表打开此表
            $objPHPExcel->setActiveSheetIndex(0);
            // 所有单元格默认高度
            $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
            // 冻结窗口
            $objPHPExcel->getActiveSheet()->freezePane('A2');

            // 从第二行开始
            $row = 2;

            $success = $delivery = $money = $online = 0;
            foreach($dataArr as $data){
              $objPHPExcel->getActiveSheet()->setCellValue("A".$row, $data['date']);
              $objPHPExcel->getActiveSheet()->setCellValue("B".$row, $data['success']);
              $objPHPExcel->getActiveSheet()->setCellValue("C".$row, $data['delivery']);
              $objPHPExcel->getActiveSheet()->setCellValue("D".$row, $data['money']);
              $objPHPExcel->getActiveSheet()->setCellValue("E".$row, $data['online']);
              $row++;

              $success += $data['total'];
              $delivery += $data['delivery'];
              $money += $data['money'];
              $online += $data['online'];
            }

            $objPHPExcel->getActiveSheet()->setCellValue("A".$row, "总计");
            $objPHPExcel->getActiveSheet()->setCellValue("B".$row, $success);
            $objPHPExcel->getActiveSheet()->setCellValue("C".$row, $delivery);
            $objPHPExcel->getActiveSheet()->setCellValue("D".$row, $money);
            $objPHPExcel->getActiveSheet()->setCellValue("E".$row, $online);

            $objActSheet = $objPHPExcel->getActiveSheet();

            // 列宽
            $objPHPExcel->getActiveSheet()->getColumnDimension()->setAutoSize(true);

            $filename = $tablename."__".$lastMonthDate."__".$nowDate.".csv";
            ob_end_clean();//清除缓冲区,避免乱码
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $objWriter->save('php://output');
            die;

        }

    }


    //订单按时间段统计
    elseif($action == "chartordertime"){


        $where = "";

        $where2 = " AND cityid in ($adminCityIds)";
        if ($cityid){
            $where2 = " AND cityid = $cityid";
        }
        $shopid = array();
        $shopSql = $dsql->SetQuery("SELECT `id` FROM `#@__waimai_shop` WHERE 1=1".$where2);
        $shopResult = $dsql->dsqlOper($shopSql, "results");
        if($shopResult){
            foreach($shopResult as $key => $loupan){
                array_push($shopid, $loupan['id']);
            }
            $where = " AND `sid` in (".join(",", $shopid).")";
        }

        if($shop_id){
            $where = " AND `sid` = $shop_id";
        }

        //成功订单数
        $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $begintime AND `pubdate` <= $endtime" . $where);
        $success = $dsql->dsqlOper($sql, "results");

        //货到付款成功订单数
        $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE `paytype` = 'delivery' AND `state` = 1 AND `pubdate` >= $begintime AND `pubdate` <= $endtime" . $where);
        $delivery = $dsql->dsqlOper($sql, "results");

        //余额支付
        $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE `paytype` = 'money' AND `state` = 1 AND `pubdate` >= $begintime AND `pubdate` <= $endtime" . $where);
        $money = $dsql->dsqlOper($sql, "results");

        //在线支付
        $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE (`paytype` = 'wxpay' OR `paytype` = 'alipay' OR `paytype` = 'unionpay') AND `state` = 1 AND `pubdate` >= $begintime AND `pubdate` <= $endtime" . $where);
        $online = $dsql->dsqlOper($sql, "results");

        array_push($dataArr, array(
            "success"    => $success[0]['total'],
            "delivery" => $delivery[0]['total'],
            "money"    => $money[0]['total'],
            "online"   => $online[0]['total']
        ));

    }


    //配送员统计
    elseif($action == "chartcourier"){

        $peisongid = array();
        if($courier_id){
            $peisongid = array($courier_id);
        }else{
            foreach ($courierArr as $key => $value) {
                array_push($peisongid, $value['id']);
            }
        }
        $peisongid = join(",", $peisongid);
        $where = " AND `peisongid` in ($peisongid)";

        

        $etime = strtotime(date("Y-m-d", $endtime));
        $btime = strtotime(date("Y-m-d", $begintime));
        for($start = $etime; $start >= $btime; $start -= 24 * 3600) {

            $time1 = $start;
            $time2 = $start + 86400;

            //成功订单数
            $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $success = $dsql->dsqlOper($sql, "results");

            //失败订单数
            $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE (`state` = 6 OR `state` = 7) AND `pubdate` >= $time1 AND `pubdate` < $time2" . $where);
            $failed = $dsql->dsqlOper($sql, "results");

            array_push($priceArr, $success[0]['total']);
            array_push($failedArr, $failed[0]['total']);

        }

        foreach ($courierArr as $key => $value) {
            if(($courier_id && $value['id'] == $courier_id) || !$courier_id){

                $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $begintime AND `pubdate` <= $endtime AND `peisongid` = " . $value['id']);
                $totalSuccess = $dsql->dsqlOper($sql, "results");

                $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE (`state` = 6 OR `state` = 7) AND `pubdate` >= $begintime AND `pubdate` <= $endtime AND `peisongid` = " . $value['id']);
                $totalFailed = $dsql->dsqlOper($sql, "results");

                $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $begintime AND `pubdate` <= $endtime AND `peisongid` = " . $value['id']);
                $success = $dsql->dsqlOper($sql, "results");

                $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `paytype` = 'delivery' AND `pubdate` >= $begintime AND `pubdate` <= $endtime AND `peisongid` = " . $value['id']);
                $delivery = $dsql->dsqlOper($sql, "results");

                $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `paytype` = 'money' AND `pubdate` >= $begintime AND `pubdate` <= $endtime AND `peisongid` = " . $value['id']);
                $money = $dsql->dsqlOper($sql, "results");

                $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND (`paytype` = 'wxpay' OR `paytype` = 'alipay') AND `pubdate` >= $begintime AND `pubdate` <= $endtime AND `peisongid` = " . $value['id']);
                $online = $dsql->dsqlOper($sql, "results");

                $peisong = $fuwu = 0;
                $sql = $dsql->SetQuery("SELECT `priceinfo` FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $begintime AND `pubdate` <= $endtime AND `peisongid` = " . $value['id']);
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    foreach ($ret as $k => $v) {
                        $priceinfo = empty($v['priceinfo']) ? array() : unserialize($v['priceinfo']);
                        if($priceinfo){
                            foreach ($priceinfo as $k_ => $v_) {
                                if($v_['type'] == "peisong"){
                                    $peisong += $v_['amount'];
                                }
                                if($v_['type'] == "fuwu"){
                                    $fuwu += $v_['amount'];
                                }
                            }
                        }
                    }
                }

                array_push($dataArr, array(
                    "name"     => $value['name'],
                    "totalSuccess"    => (int)$totalSuccess[0]['total'],
                    "totalFailed" => (int)$totalFailed[0]['total'],
                    "success"    => sprintf("%.2f", $success[0]['total']),
                    "delivery"   => sprintf("%.2f", $delivery[0]['total']),
                    "money"   => sprintf("%.2f", $money[0]['total']),
                    "online"   => sprintf("%.2f", $online[0]['total']),
                    "peisong"   => sprintf("%.2f", $peisong),
                    "fuwu"   => sprintf("%.2f", $fuwu)
                ));


            }
        }



        //导出
        if($do == "export"){

            $tablename = (empty($cityname) ? "全部" : $cityname) . "配送员统计";
            $tablename = (empty($couriername) ? "全部" : $couriername) . "配送员统计";
            $tablename = iconv("UTF-8", "GB2312//IGNORE", $tablename);

            include HUONIAOROOT.'/include/class/PHPExcel/PHPExcel.php';
            include HUONIAOROOT.'/include/class/PHPExcel/PHPExcel/Writer/Excel2007.php';
            //或者include 'PHPExcel/Writer/Excel5.php'; 用于输出.xls 的
            // 创建一个excel
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Phpmarker")->setLastModifiedBy("Phpmarker")->setTitle("Phpmarker")->setSubject("Phpmarker")->setDescription("Phpmarker")->setKeywords("Phpmarker")->setCategory("Phpmarker");

            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '配送员')
            ->setCellValue('B1', '配送成功')
            ->setCellValue('C1', '配送失败')
            ->setCellValue('D1', '配送费')
            ->setCellValue('E1', '增值服务费')
            ->setCellValue('F1', '配送成功总金额')
            ->setCellValue('G1', '货到付款总金额')
            ->setCellValue('H1', '余额付款总金额')
            ->setCellValue('I1', '在线支付总金额');


            // 表名
            $tabname = "配送员统计";
            $objPHPExcel->getActiveSheet()->setTitle($tabname);

            // 将活动表索引设置为第一个表，因此Excel将作为第一个表打开此表
            $objPHPExcel->setActiveSheetIndex(0);
            // 所有单元格默认高度
            $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
            // 冻结窗口
            $objPHPExcel->getActiveSheet()->freezePane('A2');

            // 从第二行开始
            $row = 2;

            $totalSuccess = $totalFailed = $peisong = $fuwu = $success = $delivery = $money = $online = 0;
            foreach($dataArr as $data){
              $objPHPExcel->getActiveSheet()->setCellValue("A".$row, $data['name']);
              $objPHPExcel->getActiveSheet()->setCellValue("B".$row, $data['totalSuccess']);
              $objPHPExcel->getActiveSheet()->setCellValue("C".$row, $data['totalFailed']);
              $objPHPExcel->getActiveSheet()->setCellValue("D".$row, $data['peisong']);
              $objPHPExcel->getActiveSheet()->setCellValue("E".$row, $data['fuwu']);
              $objPHPExcel->getActiveSheet()->setCellValue("F".$row, $data['success']);
              $objPHPExcel->getActiveSheet()->setCellValue("G".$row, $data['delivery']);
              $objPHPExcel->getActiveSheet()->setCellValue("H".$row, $data['money']);
              $objPHPExcel->getActiveSheet()->setCellValue("I".$row, $data['online']);
              $row++;

              $totalSuccess += $data['totalSuccess'];
              $totalFailed += $data['totalFailed'];
              $peisong += $data['peisong'];
              $fuwu += $data['fuwu'];
              $success += $data['success'];
              $delivery += $data['delivery'];
              $money += $data['money'];
              $online += $data['online'];
            }

            $objPHPExcel->getActiveSheet()->setCellValue("A".$row, "总计");
            $objPHPExcel->getActiveSheet()->setCellValue("B".$row, $totalSuccess);
            $objPHPExcel->getActiveSheet()->setCellValue("C".$row, $totalFailed);
            $objPHPExcel->getActiveSheet()->setCellValue("D".$row, $peisong);
            $objPHPExcel->getActiveSheet()->setCellValue("E".$row, $fuwu);
            $objPHPExcel->getActiveSheet()->setCellValue("F".$row, $success);
            $objPHPExcel->getActiveSheet()->setCellValue("G".$row, $delivery);
            $objPHPExcel->getActiveSheet()->setCellValue("H".$row, $money);
            $objPHPExcel->getActiveSheet()->setCellValue("I".$row, $online);

            $objActSheet = $objPHPExcel->getActiveSheet();

            // 列宽
            $objPHPExcel->getActiveSheet()->getColumnDimension()->setAutoSize(true);

            $filename = $tablename."__".$lastMonthDate."__".$nowDate.".csv";
            ob_end_clean();//清除缓冲区,避免乱码
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $objWriter->save('php://output');

            die;

        }

    }


    //财务结算
    elseif($action == "financenew"){

        $endtime = $endtime + 86399;
        
        foreach ($shopArr as $key => $value) {

            $sql = $dsql->SetQuery("SELECT o.`id`, o.`food`, o.`priceinfo`, o.`usequan`, s.`fencheng_foodprice`, s.`fencheng_delivery`, s.`fencheng_dabao`, s.`fencheng_addservice`, s.`fencheng_discount`, s.`fencheng_promotion`, s.`fencheng_firstdiscount`, s.`fencheng_quan` FROM `#@__waimai_order` o LEFT JOIN `#@__waimai_shop` s ON s.`id` = o.`sid` WHERE o.`state` = 1 AND o.`pubdate` >= $begintime AND o.`pubdate` <= $endtime AND o.`sid` = " . $value['id']);
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){

                $foodTotalPrice = $turnover = $platform = $business = $delivery = $money = $online = $peisongTotalPrice = $dabaoTotalPrice = $addserviceTotalPrice = $discountTotalPrice = $promotionTotalPrice = $firstdiscountTotalPrice = $youhuiquanTotalPrice = 0;

                foreach ($ret as $k => $v) {

                    $foodTotal = $peisongTotal = $dabaoTotal = $addserviceTotal = $discountTotal = $promotionTotal = $firstdiscountTotal = $youhuiquanTotal = 0;

                    $food = unserialize($v['food']);
                    $priceinfo = unserialize($v['priceinfo']);
                    $fencheng_foodprice = (int)$v['fencheng_foodprice'];   //商品原价分成
                    $fencheng_delivery = (int)$v['fencheng_delivery'];     //配送费分成
                    $fencheng_dabao = (int)$v['fencheng_dabao'];           //打包分成
                    $fencheng_addservice = (int)$v['fencheng_addservice']; //增值服务费分成
                    $fencheng_discount = (int)$v['fencheng_discount'];     //折扣分摊
                    $fencheng_promotion = (int)$v['fencheng_promotion'];   //满减分摊
                    $fencheng_firstdiscount = (int)$v['fencheng_firstdiscount'];  //首单减免分摊
                    $fencheng_quan = (int)$v['fencheng_quan'];  //优惠券分摊


                    // 优惠券
                    $usequan = (int)$v['usequan'];
                    $quanBili = 100;
                    if($usequan){
                        $quanSql = $dsql->SetQuery("SELECT `bear` FROM `#@__waimai_quanlist` WHERE `id` = $usequan");
                        $quanRet = $dsql->dsqlOper($quanSql, "results");
                        if($quanRet){
                            $bear = $quanRet[0]['bear'];
                            // 平台和店铺分担
                            if(!$bear){
                                $quanBili = $fencheng_quan;
                            }
                        }
                    }

                    //计算单个订单的商品原价
                    foreach ($food as $k_ => $v_) {
                        $foodTotal += $v_['price'] * $v_['count'];
                        $foodTotalPrice += $v_['price'] * $v_['count'];
                    }

                    //费用详情
                    if($priceinfo){
                        foreach ($priceinfo as $k_ => $v_) {
                            if($v_['type'] == "peisong"){
                                $peisongTotal += $v_['amount'];
                                $peisongTotalPrice += $v_['amount'];
                            }
                            if($v_['type'] == "dabao"){
                                $dabaoTotal += $v_['amount'];
                                $dabaoTotalPrice += $v_['amount'];
                            }
                            if($v_['type'] == "fuwu"){
                                $addserviceTotal += $v_['amount'];
                                $addserviceTotalPrice += $v_['amount'];
                            }
                            if($v_['type'] == "youhui"){
                                $discountTotal += $v_['amount'];
                                $discountTotalPrice += $v_['amount'];
                            }
                            if($v_['type'] == "manjian"){
                                $promotionTotal += $v_['amount'];
                                $promotionTotalPrice += $v_['amount'];
                            }
                            if($v_['type'] == "shoudan"){
                                $firstdiscountTotal += $v_['amount'];
                                $firstdiscountTotalPrice += $v_['amount'];
                            }
                            if($v_['type'] == "quan"){
                                $youhuiquanTotal += -$v_['amount'];
                                $youhuiquanTotalPrice += -$v_['amount'];
                            }
                        }
                    }

                    //计算总交易额
                    $zjye = $foodTotal - $discountTotal - $promotionTotal - $firstdiscountTotal + $dabaoTotal + $peisongTotal + $addserviceTotal - $youhuiquanTotal;
                    // $zjye = sprintf("%.2f", $foodTotal - $discountTotal - $promotionTotal - $firstdiscountTotal + $dabaoTotal + $peisongTotal + $addserviceTotal - $youhuiquanTotal);
                    $turnover += $zjye;

                    //计算平台应得金额
                    // $ptyd = sprintf("%.2f", $foodTotal * $fencheng_foodprice / 100 - $discountTotal * $fencheng_discount / 100 - $promotionTotal * $fencheng_promotion / 100 - $firstdiscountTotal * $fencheng_firstdiscount / 100 + $dabaoTotal * $fencheng_dabao / 100 + $peisongTotal * $fencheng_delivery / 100 + $addserviceTotal * $fencheng_addservice / 100 - $youhuiquanTotal * $quanBili / 100);

                    $ptyd = $foodTotal * $fencheng_foodprice / 100 - $discountTotal * $fencheng_discount / 100 - $promotionTotal * $fencheng_promotion / 100 - $firstdiscountTotal * $fencheng_firstdiscount / 100 + $dabaoTotal * $fencheng_dabao / 100 + $peisongTotal * $fencheng_delivery / 100 + $addserviceTotal * $fencheng_addservice / 100 - $youhuiquanTotal * $quanBili / 100;
                    $platform += $ptyd;

                    //商家应得
                    $business += $zjye - $ptyd;

                }

                //计算货到付款交易额
                $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `paytype` = 'delivery' AND `pubdate` >= $begintime AND `pubdate` <= $endtime AND `sid` = " . $value['id']);
                $ret = $dsql->dsqlOper($sql, "results");
                $delivery += $ret[0]['total'];

                //计算余额付款交易额
                $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `paytype` = 'money' AND `pubdate` >= $begintime AND `pubdate` <= $endtime AND `sid` = " . $value['id']);
                $ret = $dsql->dsqlOper($sql, "results");
                $money += $ret[0]['total'];

                //计算在线付款交易额
                $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND (`paytype` = 'wxpay' OR `paytype` = 'alipay') AND `pubdate` >= $begintime AND `pubdate` <= $endtime AND `sid` = " . $value['id']);
                $ret = $dsql->dsqlOper($sql, "results");
                $online += $ret[0]['total'];


                array_push($dataArr, array(
                    "shopname" => $value['shopname'],
                    "turnover" => $turnover,
                    "platform" => $platform,
                    "business" => $business,
                    "delivery" => $delivery,
                    "money"    => $money,
                    "online"   => $online,
                    "foodTotalPrice" => $foodTotalPrice,
                    "peisongTotalPrice" => $peisongTotalPrice,
                    "dabaoTotalPrice" => $dabaoTotalPrice,
                    "addserviceTotalPrice" => $addserviceTotalPrice,
                    "discountTotalPrice" => $discountTotalPrice,
                    "promotionTotalPrice" => $promotionTotalPrice,
                    "firstdiscountTotalPrice" => $firstdiscountTotalPrice,
                    "youhuiquanTotalPrice" => $youhuiquanTotalPrice
                ));


            }

        }


        //导出
        if($do == "export"){

            $title = iconv("UTF-8", "GB2312//IGNORE", "财务结算");

            include HUONIAOROOT.'/include/class/PHPExcel/PHPExcel.php';
            include HUONIAOROOT.'/include/class/PHPExcel/PHPExcel/Writer/Excel2007.php';
            //或者include 'PHPExcel/Writer/Excel5.php'; 用于输出.xls 的
            // 创建一个excel
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Phpmarker")->setLastModifiedBy("Phpmarker")->setTitle("Phpmarker")->setSubject("Phpmarker")->setDescription("Phpmarker")->setKeywords("Phpmarker")->setCategory("Phpmarker");

            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '店铺名')
            ->setCellValue('B1', '商家应得金额')
            ->setCellValue('C1', '平台应得金额')
            ->setCellValue('D1', '总交易额')
            ->setCellValue('E1', '货到付款交易额')
            ->setCellValue('F1', '余额付款交易额')
            ->setCellValue('G1', '在线支付交易额')
            ->setCellValue('H1', '商品原价总额')
            ->setCellValue('I1', '配送费总额')
            ->setCellValue('J1', '打包费总额')
            ->setCellValue('K1', '增值服务费总额')
            ->setCellValue('L1', '折扣优惠总额')
            ->setCellValue('M1', '满减优惠')
            ->setCellValue('N1', '优惠券使用总额')
            ->setCellValue('O1', '首次下单减免总额');


            // 表名
            $tabname = "财务结算";
            $objPHPExcel->getActiveSheet()->setTitle($tabname);

            // 将活动表索引设置为第一个表，因此Excel将作为第一个表打开此表
            $objPHPExcel->setActiveSheetIndex(0);
            // 所有单元格默认高度
            $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
            // 冻结窗口
            $objPHPExcel->getActiveSheet()->freezePane('A2');

            // 从第二行开始
            $row = 2;

            $business = $platform = $turnover = $delivery = $money = $online = $foodTotalPrice = $peisongTotalPrice = $dabaoTotalPrice = $addserviceTotalPrice = $discountTotalPrice = $promotionTotalPrice = $firstdiscountTotalPrice = $youhuiquanTotalPrice = 0;

            foreach($dataArr as $data){
              $objPHPExcel->getActiveSheet()->setCellValue("A".$row, $data['shopname']);
              $objPHPExcel->getActiveSheet()->setCellValue("B".$row, sprintf("%.2f", $data['business']));
              $objPHPExcel->getActiveSheet()->setCellValue("C".$row, sprintf("%.2f", $data['platform']));
              $objPHPExcel->getActiveSheet()->setCellValue("D".$row, sprintf("%.2f", $data['turnover']));
              $objPHPExcel->getActiveSheet()->setCellValue("E".$row, sprintf("%.2f", $data['delivery']));
              $objPHPExcel->getActiveSheet()->setCellValue("F".$row, sprintf("%.2f", $data['money']));
              $objPHPExcel->getActiveSheet()->setCellValue("G".$row, sprintf("%.2f", $data['online']));
              $objPHPExcel->getActiveSheet()->setCellValue("H".$row, sprintf("%.2f", $data['foodTotalPrice']));
              $objPHPExcel->getActiveSheet()->setCellValue("I".$row, sprintf("%.2f", $data['peisongTotalPrice']));
              $objPHPExcel->getActiveSheet()->setCellValue("J".$row, sprintf("%.2f", $data['dabaoTotalPrice']));
              $objPHPExcel->getActiveSheet()->setCellValue("K".$row, sprintf("%.2f", $data['addserviceTotalPrice']));
              $objPHPExcel->getActiveSheet()->setCellValue("L".$row, sprintf("%.2f", $data['discountTotalPrice']));
              $objPHPExcel->getActiveSheet()->setCellValue("M".$row, sprintf("%.2f", $data['promotionTotalPrice']));
              $objPHPExcel->getActiveSheet()->setCellValue("N".$row, sprintf("%.2f", $data['youhuiquanTotalPrice']));
              $objPHPExcel->getActiveSheet()->setCellValue("O".$row, sprintf("%.2f", $data['firstdiscountTotalPrice']));
              $row++;

              $business += $data['business'];
              $platform += $data['platform'];
              $turnover += $data['turnover'];
              $delivery += $data['delivery'];
              $money += $data['money'];
              $online += $data['online'];
              $foodTotalPrice += $data['foodTotalPrice'];
              $peisongTotalPrice += $data['peisongTotalPrice'];
              $dabaoTotalPrice += $data['dabaoTotalPrice'];
              $addserviceTotalPrice += $data['addserviceTotalPrice'];
              $discountTotalPrice += $data['discountTotalPrice'];
              $promotionTotalPrice += $data['promotionTotalPrice'];
              $firstdiscountTotalPrice += $data['firstdiscountTotalPrice'];
              $youhuiquanTotalPrice += $data['youhuiquanTotalPrice'];

            }

            $objPHPExcel->getActiveSheet()->setCellValue("A".$row, "总计");
            $objPHPExcel->getActiveSheet()->setCellValue("B".$row, sprintf("%.2f", $business));
            $objPHPExcel->getActiveSheet()->setCellValue("C".$row, sprintf("%.2f", $platform));
            $objPHPExcel->getActiveSheet()->setCellValue("D".$row, sprintf("%.2f", $turnover));
            $objPHPExcel->getActiveSheet()->setCellValue("E".$row, sprintf("%.2f", $delivery));
            $objPHPExcel->getActiveSheet()->setCellValue("F".$row, sprintf("%.2f", $money));
            $objPHPExcel->getActiveSheet()->setCellValue("G".$row, sprintf("%.2f", $online));
            $objPHPExcel->getActiveSheet()->setCellValue("H".$row, sprintf("%.2f", $foodTotalPrice));
            $objPHPExcel->getActiveSheet()->setCellValue("I".$row, sprintf("%.2f", $peisongTotalPrice));
            $objPHPExcel->getActiveSheet()->setCellValue("J".$row, sprintf("%.2f", $dabaoTotalPrice));
            $objPHPExcel->getActiveSheet()->setCellValue("K".$row, sprintf("%.2f", $addserviceTotalPrice));
            $objPHPExcel->getActiveSheet()->setCellValue("L".$row, sprintf("%.2f", $discountTotalPrice));
            $objPHPExcel->getActiveSheet()->setCellValue("M".$row, sprintf("%.2f", $promotionTotalPrice));
            $objPHPExcel->getActiveSheet()->setCellValue("N".$row, sprintf("%.2f", $youhuiquanTotalPrice));
            $objPHPExcel->getActiveSheet()->setCellValue("O".$row, sprintf("%.2f", $firstdiscountTotalPrice));

            $objActSheet = $objPHPExcel->getActiveSheet();

            // 列宽
            $objPHPExcel->getActiveSheet()->getColumnDimension()->setAutoSize(true);

            $filename = $title."__".$lastMonthDate."__".$nowDate.".csv";
            ob_end_clean();//清除缓冲区,避免乱码
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $objWriter->save('php://output');

            die;

        }

    }

}


$huoniaoTag->assign("dataArr", $dataArr);

$priceArr = array_reverse($priceArr);
$huoniaoTag->assign("priceArr", str_replace('"', '', json_encode($priceArr)));

$failedArr = array_reverse($failedArr);
$huoniaoTag->assign("failedArr", str_replace('"', '', json_encode($failedArr)));

$huoniaoTag->assign('cityList', json_encode($adminCityArr));


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
		'ui/jquery-ui.min.js',
		'ui/jquery.form.js',
		'ui/chosen.jquery.min.js',
		'ui/jquery-ui-i18n.min.js',
		'ui/jquery-ui-timepicker-addon.js',
		'ui/highcharts.js',
		'admin/waimai/waimaiStatistics.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
    $huoniaoTag->assign('action', $action);
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
