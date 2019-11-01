<?php
/**
 * 支付宝转账主文件
 *
 * @version        $Id: alipayTransfers.php $v1.0 2019-7-10 上午11:18:19 $
 * @package        HuoNiao.Payment
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

if(!defined('HUONIAOINC')) exit('Request Error!');


/**
 * 类
 */
class alipayTransfers {

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
        $this->alipayTransfers();
    }

    function alipayTransfers(){

        // 加载支付方式操作函数
        loadPlug("payment");
        $payment = get_payment("alipay");

        $this->appId = $payment['appid'];
        $this->rsaPrivateKey = $payment['appPrivate'];
        $this->alipayrsaPublicKey = $payment['alipayPublic'];

    }

    function transfers($order){

        require_once ("aop/AopClient.php");
        require_once ("aop/request/AlipayFundTransToaccountTransferRequest.php");

        $appId = $this->appId;
        $rsaPrivateKey = $this->rsaPrivateKey;
        $alipayrsaPublicKey = $this->alipayrsaPublicKey;

        // ----------订单信息
        // 商户订单号
        $out_trade_no = $order['ordernum'];
        // 账户
        $account = $order['account'];
        // 姓名
        $name = $order['name'];
        // 提现金额
        $amount = $order['amount'];
        // 备注
        $desc = "余额提现";

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
        $request = new AlipayFundTransToaccountTransferRequest ();
        $request->setBizContent("{" .
        "\"out_biz_no\":\"" . $out_trade_no . "\"," .
        "\"payee_type\":\"ALIPAY_LOGONID\"," .
        "\"payee_account\":\"" . $account . "\"," .
        "\"amount\":\"" . $amount . "\"," .
        "\"payee_real_name\":\"" . $name . "\"," .
        "\"remark\":\"" . $desc . "\"" .
        "  }");

        $result = $aop->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        $sub_code = $result->$responseNode->sub_code;
        $sub_msg = $result->$responseNode->sub_msg;
        if(!empty($resultCode) && $resultCode == 10000){
            return array("state" => 100, "date" => GetMkTime($result->$responseNode->pay_date), "payment_no" => $result->$responseNode->order_id);
        } else {
            return array("state" => 200, "info" => $resultCode . ":" . $sub_msg);
        }


    }

}
