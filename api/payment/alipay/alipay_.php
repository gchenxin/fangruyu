<?php
/**
 * 支付宝转账到支付宝账户主文件
 *
 * @version        $Id: alipayTransfer.php $v1.0 2018-07-16 下午16:01:28 $
 * @package        HuoNiao.Payment
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

if(!defined('HUONIAOINC')) exit('Request Error!');

/**
 * 类
 */
class alipayTransfer {

	/**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */

  public $appId;
  public $rsaPrivateKey;
  public $alipayrsaPublicKey;

  function __construct(){
    $this->alipayRefund();
  }

  function alipayRefund(){

    // 加载支付方式操作函数
    loadPlug("payment");
    $payment = get_payment("alipay");

    $this->appId = $payment['appid'];
    $this->rsaPrivateKey = $payment['appPrivate'];
    $this->alipayrsaPublicKey = $payment['alipayPublic'];
  }

  function transfer($order = array()){

    require_once ("aop/AopClient.php");
    require_once ("aop/request/AlipayFundTransToaccountTransferRequest.php");

    // ----------订单信息
    $out_trade_no = date(YmdHis);         //商户转账唯一订单号
    $payee_account = $order['account'];  //收款方账户    支付宝账号对应的支付宝唯一用户号。以2088开头的16位纯数字组成。
    $amount = $order['amount'];                      //转账金额  金额必须大于等于0.1元。
    $payee_real_name = $order['realname'];          //收款方真实姓名
    $remark = $order['remark'];                    //转账备注

    $aop = new AopClient ();
    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
    $aop->appId = $this->appId;
    $aop->rsaPrivateKey = $this->rsaPrivateKey;
    $aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
    $aop->apiVersion = '1.0';
    $aop->signType = 'RSA2';
    $aop->postCharset='UTF-8';
    $aop->format='json';
    $request = new AlipayFundTransToaccountTransferRequest ();
    $request->setBizContent("{" .
    "\"out_biz_no\":\"" . $out_trade_no . "\"," .
    "\"payee_type\":\"ALIPAY_USERID\"," .
    "\"payee_account\":" . $payee_account . "," .
    "\"amount\":\"".$amount."\"," .
    "\"payee_real_name\":\"" . $payee_real_name . "\"," .
    "\"remark\":\"".$remark."\"" .
    "  }");
    // print_r($request);
    // die;
    $result = $aop->execute ( $request);

    $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
    $resultCode = $result->$responseNode->code;
    if(!empty($resultCode)&&$resultCode == 10000){
      return array("state" => 100, "order_id" => $result->$responseNode->order_id, "pay_date" => $result->$responseNode->pay_date);
      echo "成功<br />";
      echo "支付宝订单号：".$result->$responseNode->order_id."<br />";
      echo "支付成功时间：".$result->$responseNode->pay_date."<br />";
    } else {
      return array("state" => 101, "info" => $result->$responseNode->sub_msg, "code" => $result->$responseNode->sub_code);
      echo "失败<br />";
      echo "原因：".$result->$responseNode->sub_msg."<br />";
    }
  }
}

//调用方式
// $alipayTransfer = new alipayTransfer();
// $return = $alipayTransfer->transfer();
