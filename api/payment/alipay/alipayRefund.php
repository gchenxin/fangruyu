<?php
/**
 * 支付宝退款主文件
 *
 * @version        $Id: alipay.php $v1.0 2014-3-12 下午17:19:21 $
 * @package        HuoNiao.Payment
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

if(!defined('HUONIAOINC')) exit('Request Error!');



/* 基本信息 */
if(isset($set_modules) && $set_modules == TRUE){

    $i = isset($payment) ? count($payment) : 0;

    /* 代码 */
    $payment[$i]['pay_code'] = "alipay";

	/* 名称 */
    $payment[$i]['pay_name'] = "支付宝退款";

    /* 版本号 */
    $payment[$i]['version']  = '1.0.0';

    /* 描述 */
    $payment[$i]['pay_desc'] = '国内先进的网上支付平台。三种支付接口：担保交易，即时到账，双接口。在线即可开通，零预付，免年费，单笔阶梯费率，无流量限制。';

    /* 作者 */
    $payment[$i]['author']   = '火鸟软件';

    /* 网址 */
    $payment[$i]['website']  = 'http://www.huoniao.co';

    /* 配置信息 */
    $payment[$i]['config'] = array(
        'title' => '网页支付',
		'title' => '支付宝帐户'
    );

    return;
}

/**
 * 类
 */
class alipayRefund {

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

    function refund($order){

        require_once ("aop/AopClient.php");
        require_once ("aop/request/AlipayTradeRefundRequest.php");

        $appId = $this->appId;
        $rsaPrivateKey = $this->rsaPrivateKey;
        $alipayrsaPublicKey = $this->alipayrsaPublicKey;

        // ----------订单信息
        // 交易号
        $trade_no = "";
        // 商户订单号
        $out_trade_no = $order['ordernum'];
        // 退款金额
        $refund_amount = $order['amount'];

        // 标志一次退款请求 格式为：退款日期（8位）+流水号（3～24位）。不可重复，且退款日期必须是当天日期。流水号可以接受数字或英文字符，建议使用数字，但不可接受“000”
        $out_request_no = date(YmdHis).$out_trade_no;

        $aop = new AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $appId;
        $aop->rsaPrivateKey = $rsaPrivateKey;
        $aop->alipayrsaPublicKey = $alipayrsaPublicKey;
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new AlipayTradeRefundRequest ();
        $request->setBizContent("{" .
        "\"out_trade_no\":\"" . $out_trade_no . "\"," .
        "\"trade_no\":\"". $trade_no . "\"," .
        "\"refund_amount\":" . $refund_amount . "," .
        "\"refund_reason\":\"正常退款\"," .
        "\"out_request_no\":\"" . $out_request_no . "\"," .
        "\"operator_id\":\"\"," .
        "\"store_id\":\"\"," .
        "\"terminal_id\":\"\"" .
        "  }");

        $result = $aop->execute ( $request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        $sub_code = $result->$responseNode->sub_code;
        if(!empty($resultCode) && $resultCode == 10000){
            return array("state" => 100, "date" => $result->$responseNode->gmt_refund_pay, "trade_no" => $out_request_no);
        } else {
            return array("state" => 200, "code" => "$resultCode - $sub_code 订单号：$out_trade_no 金额：$refund_amount");
        }


    }

}
