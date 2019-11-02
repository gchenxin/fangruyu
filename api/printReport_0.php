<?php

if(empty($cmd)){
    die('{"data":"OK"}');
}

//验证签名
$signCode = strtoupper(md5($customPrintKey . $time));
if($sign != $signCode){
    die('签名验证失败！');
}

//终端状态推送接口
if($cmd == "print_status"){

	require_once dirname(__FILE__)."/payment/log.php";
  //初始化日志
  $logHandler= new CLogFileHandler('print.log');
  $log = Log::Init($logHandler, 15);
  $data = array();
  foreach ($_REQUEST as $key => $value) {
    array_push($data, $key . "=" . $value);
  }
  Log::DEBUG("query:" . join("&", $data) . "\r\n");


    $online = (int)$online;

    if($installWaimai){
      //查询数据库中的打印机
      $sql = $dsql->SetQuery("SELECT `id`, `print_config` FROM `#@__waimai_shop` WHERE `print_config` != ''");
      $ret = $dsql->dsqlOper($sql, "results");
      if($ret){
          foreach ($ret as $key => $value) {
              $shopid       = $value['id'];
              $print_config = unserialize($value['print_config']);
              $mcode        = $print_config[0]['mcode'];

              //如果两个终端号相同，则更新打印机状态
              if($mcode == $machine_code){
                  $sql = $dsql->SetQuery("UPDATE `#@__waimai_shop` SET `print_state` = $online WHERE `id` = $shopid");
                  $dsql->dsqlOper($sql, "update");
              }
          }
        }
    }

    if($installBusiness){
      //查询数据库中的打印机
      $sql = $dsql->SetQuery("SELECT `id`, `print_config` FROM `#@__business_list` WHERE `print_config` != ''");
      $ret = $dsql->dsqlOper($sql, "results");
      if($ret){
          foreach ($ret as $key => $value) {
              $shopid       = $value['id'];
              $print_config = unserialize($value['print_config']);
              $mcode        = $print_config[0]['mcode'];

              //如果两个终端号相同，则更新打印机状态
              if($mcode == $machine_code){
                  $sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `print_state` = $online WHERE `id` = $shopid");
                  $dsql->dsqlOper($sql, "update");
              }
          }
        }
    }
}

$acceptTypeed = empty($customAcceptType) ? "finish" : "getorder";


//接单拒单推送接口
if($cmd == $acceptTypeed){

	require_once dirname(__FILE__)."/payment/log.php";
  //初始化日志
  $logHandler= new CLogFileHandler('finish.log');
  $log = Log::Init($logHandler, 15);
  $data = array();
  foreach ($_REQUEST as $key => $value) {
    array_push($data, $key . "=" . $value);
  }
  Log::DEBUG("query:" . join("&", $data) . "\r\n");

    $state = (int)$state;

    if(!empty($dataid) && $state == 1){

        $time = GetMkTime(time());



        // 外卖
        if($installWaimai){

          //消息通知
          $sql = $dsql->SetQuery("SELECT o.`id`, o.`uid`, o.`ordernumstore`, o.`pubdate`, o.`food`, o.`amount`, s.`shopname` FROM `#@__waimai_order` o LEFT JOIN `#@__waimai_shop` s ON s.`id` = o.`sid` WHERE `print_dataid` = '$dataid'");
          $ret = $dsql->dsqlOper($sql, "results");
          if($ret){
            $data = $ret[0];

            $id            = $data['id'];
            $uid           = $data['uid'];
            $ordernumstore = $data['ordernumstore'];
            $pubdate       = $data['pubdate'];
            $food          = unserialize($data['food']);
            $amount        = $data['amount'];
            $shopname      = $data['shopname'];

            $foods = array();
            foreach ($food as $key => $value) {
                array_push($foods, $value['title'] . " " . $value['count'] . "份");
            }

            $param = array(
                "service"  => "member",
                "type"     => "user",
                "template" => "orderdetail",
                "module"   => "waimai",
                "id"       => $id
            );

            //自定义配置
            $config = array(
                "ordernum" => $shopname.$ordernumstore,
                "orderdate" => date("Y-m-d H:i:s", $pubdate),
                "orderinfo" => join(" ", $foods),
                "orderprice" => $amount,
                "fields" => array(
                    'keyword1' => '订单编号',
                    'keyword2' => '下单时间',
                    'keyword3' => '订单详情',
                    'keyword4' => '订单金额'
                )
            );

            updateMemberNotice($uid, "会员-订单确认提醒", $param, $config);

            $sql = $dsql->SetQuery("UPDATE `#@__waimai_order` SET `state` = 3, `confirmdate` = '$time' WHERE `state` = 2 AND `print_dataid` = '$dataid'");
            $dsql->dsqlOper($sql, "update");
          }

        }

        // 商家
        if($installBusiness){
          // $sql = $dsql->SetQuery("UPDATE `#@__business_diancan_order` SET `` WHERE `print_dataid` = '$dataid'");
          // $ret = $dsql->dsqlOper($sql, "results");
          // if($ret){

          // }
        }

    }

}
