<?php
/**
 * 二维码付款
 *
 * @version        $Id: arPay.php 2018-08-15 下午17:33:26 $
 * @package        HuoNiao.Libraries
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

//系统核心配置文件
require_once('../include/common.inc.php');

//如果在微信APP或者支付宝APP
if(isWeixin() || isAlipay()){

  $paycode = '';
  if(isWeixin()){
    $paycode = 'wxpay';
  }
  if(isAlipay()){
    $paycode = 'alipay';
  }

  $paymentFile = HUONIAOROOT . "/api/payment/$paycode/$paycode.php";
  if(file_exists($paymentFile)){
    require_once($paymentFile);
    $archives = $dsql->SetQuery("SELECT `pay_config` FROM `#@__site_payment` WHERE `pay_code` = '$paycode' AND `state` = 1");
    $payment = $dsql->dsqlOper($archives, "results");
    if($payment){
      $pay_config = unserialize($payment[0]['pay_config']);
      $paymentArr = array();

      //验证配置
      foreach ($pay_config as $key => $value) {
        if (!empty($value['value'])) {
          $paymentArr[$value['name']] = $value['value'];
        }
      }

      if (!empty($paymentArr)){

        //更新支付方式
        $sql = $dsql->SetQuery("UPDATE `#@__pay_log` SET `paytype` = '$paycode' WHERE `ordernum` = '".$_REQUEST['ordernum']."'");
        $dsql->dsqlOper($sql, "update");

        $pay = new $paycode();
        echo $pay->get_code($_REQUEST, $paymentArr);
      }else{
        showErrHtml('配置错误，请联系管理员000！');
      }
    }else{
      showErrHtml('支付方式不存在，001！');
    }
  }else{
    showErrHtml('支付方式不存在，002！');
  }

}else{

  //注册函数
  $contorllerFile = HUONIAOROOT.'/api/handlers/siteConfig.controller.php';
  if(file_exists($contorllerFile)){
    require_once($contorllerFile);
    $huoniaoTag->registerPlugin("block", "siteConfig", "siteConfig");
  }

  if($cfg_remoteStatic){
		$huoniaoTag->assign('cfg_staticPath', $cfg_remoteStatic . '/static/');  //静态资源路径
	}else{
		$huoniaoTag->assign('cfg_staticPath', $cfg_staticPath);  //静态资源路径
	}

  $huoniaoTag->assign('backUrl', $cfg_secureAccess . $cfg_basehost);
  $huoniaoTag->assign('title', '支付');
  $huoniaoTag->assign('channelName', str_replace('$city', $siteCityName, stripslashes($cfg_webname)));
  $huoniaoTag->assign('service', $_REQUEST['service']);
  $huoniaoTag->assign('totalAmount', $_REQUEST['order_amount']);
  $huoniaoTag->assign('ordernum', $_REQUEST['ordernum']);

  //查询订单信息
  if($_REQUEST['service'] == 'member'){
    $sql = $dsql->SetQuery("SELECT `body`, `amount` FROM `#@__pay_log` WHERE `ordernum` = '".$_REQUEST['ordernum']."'");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
      $amount = $ret[0]['amount'];
      if($amount != $_REQUEST['order_amount']){
        showErrHtml('支付金额与订单不符，请重新扫码支付！');
      }

      $body = unserialize($ret[0]['body']);
      $paramsArr = array();
      array_push($paramsArr, '<input type="hidden" name="amount" id="amount" value="'.$_REQUEST['order_amount'].'" />');
      foreach ($body as $key => $value) {
        $key = $key == 'type' ? 'ordertype' : $key;
        array_push($paramsArr, '<input type="hidden" name="'.$key.'" id="'.$key.'" value="'.$value.'" />');
        if($key == 'balance' && $value){
          array_push($paramsArr, '<input type="hidden" name="useBalance" id="useBalance" value="1" />');
        }
      }
      $huoniaoTag->assign('paramsHtml', join("\r", $paramsArr));
    }else{
      showErrHtml('订单超时，请重新扫码支付！');
    }

  }


  // $huoniaoTag->assign('paramsHtml', '<input type="hidden" name="ordertype" id="ordertype" value="upgrade" />');
  $huoniaoTag->assign('userinfo', array('money' => 0, 'point' => 0));
  $huoniaoTag->display(HUONIAOROOT . '/templates/member/touch/public-pay.html');
}


function showErrHtml($str){
  global $cfg_staticPath;
  global $cfg_staticVersion;
  echo <<<eot
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>支付错误</title>
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0">
<script src="{$cfg_staticPath}js/core/touchScale.js?v={$cfg_staticVersion}"></script>
<link rel="stylesheet" type="text/css" href="{$cfg_staticPath}css/core/touchBase.css?v={$cfg_staticVersion}">
<link rel="stylesheet" type="text/css" href="{$cfg_basehost}/templates/member/touch/css/public-pay.css?v={$cfg_staticVersion}">
<script type="text/javascript" src="{$cfg_staticPath}js/core/zepto.min.js"></script>
<style>html {overflow: hidden;} body {overflow: hidden; font-size: .3rem; padding: 1rem 0; text-align: center;}</style>
</head>
<body>
<div style="padding: 0 .5rem;">{$str}</div>
</body>
</html>
eot;
  die;
}
