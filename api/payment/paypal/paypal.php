<?php
/**
 * paypal在线支付主文件
 *
 * @version        $Id: paypal.php $v1.0 2014-8-14 下午18:30:11 $
 * @package        HuoNiao.Payment
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

//测试地址
// define('API_ENDPOINT', 'https://api-3t.sandbox.paypal.com/nvp');
// define('PAYPAL_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr&cmd=_express-checkout&token=');
//正式地址
define('API_ENDPOINT', 'https://api-3t.paypal.com/nvp');
define('PAYPAL_URL', 'https://www.paypal.com/cgi-bin/webscr&cmd=_express-checkout&token=');
define('USE_PROXY', FALSE);
define('PROXY_HOST', '127.0.0.1');
define('PROXY_PORT', '808');

$version=VERSION;

if(!defined('HUONIAOINC')) exit('Request Error!');

/* 基本信息 */
if(isset($set_modules) && $set_modules == TRUE){

    $i = isset($payment) ? count($payment) : 0;

    /* 代码 */
    $payment[$i]['pay_code'] = "paypal";

	/* 名称 */
    $payment[$i]['pay_name'] = "paypal在线支付";

    /* 版本号 */
    $payment[$i]['version']  = '1.0.0';

    /* 描述 */
    $payment[$i]['pay_desc'] = 'PayPal（www.paypal.com） 是在线付款解决方案的全球领导者，在全世界有超过七千一百六十万个帐户用户。PayPal 可在 56 个市场以 7 种货币（加元、欧元、英镑、美元、日元、澳元、港元）使用。';

    /* 作者 */
    $payment[$i]['author']   = '酷曼软件';

    /* 网址 */
    $payment[$i]['website']  = 'http://www.kumanyun.com';

    /* 配置信息 */
    $payment[$i]['config'] = array(
		array('title' => 'API 用户名',   'name' => 'account',   'type' => 'text'),
		array('title' => 'API 密码',     'name' => 'password',  'type' => 'text'),
		array('title' => '签名',         'name' => 'sign',      'type' => 'text'),
		array('title' => '支付货币',     'name' => 'type',      'type' => 'select', 'options' => array('AUD' => '澳元 - AUD', 'CAD' => '加元 - CAD', 'EUR' => '欧元 - EUR', 'GBP' => '英镑 - GBP', 'JPY' => '日元 - JPY', 'USD' => '美元 - USD', 'HKD' => '港元 - HKD'))
    );

    return;
}

/**
 * 类
 */
class paypal {

	/**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */

    function __construct(){
        $this->paypal();
    }

    function paypal(){}

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment){

		// 加载支付方式操作函数
		loadPlug("payment");

        global $cfg_secureAccess;
        global $cfg_basehost;

        $token = '';
        $url              = $cfg_secureAccess.$cfg_basehost;
        $paymentAmount    = $order['order_amount'];
        $currencyCodeType = $payment['type'];
        $paymentType      = 'Sale';
        $subject          = $order['subject'];
        $order_sn         = $order['order_sn'];

        $_SESSION['paypal_username']  = $payment['account'];
        $_SESSION['paypal_password']  = $payment['password'];
        $_SESSION['paypal_signature'] = $payment['sign'];

        $paramUrl = '&currencyCodeType='.$currencyCodeType.'&paymentType='.$paymentType.'&paymentAmount='.$paymentAmount.'&module='.$order['service'].'&sn='.$order_sn;
        $returnURL = urlencode(return_url("paypal", $paramUrl));

        //取消付款返回链接
        $cancelURL = urlencode($url."/?k=paypal");

        $nvpstr = "&Amt=".$paymentAmount
                  ."&PAYMENTACTION=".$paymentType
                  ."&ReturnUrl=".$returnURL
                  ."&CANCELURL=".$cancelURL
                  ."&CURRENCYCODE=".$currencyCodeType
                  ."&DESC=".$subject."：".$order_sn
                  ."&ButtonSource=HUONIAO";

        $resArray  = $this->hash_call("SetExpressCheckout", $nvpstr);

        $_SESSION['reshash'] = $resArray;
        if(isset($resArray["ACK"])){
            $ack = strtoupper($resArray["ACK"]);
        }

        if (isset($resArray["TOKEN"])){
            $token = urldecode($resArray["TOKEN"]);
        }
        $payPalURL = PAYPAL_URL.$token;

        // $returnHtml = '<meta http-equiv="refresh" content="1;url='.$payPalURL. '">正在跳转至paypal安全支付页面！';
        //$button = '<div style="text-align:center"><input type="button" onclick="window.open(\''.$payPalURL. '\')" value="立即付款"/></div>';
        // return $returnHtml;
        header("location:".$payPalURL);
        die;
    }

    /**
     * 响应操作
     */
    function respond(){

        // 加载支付方式操作函数
        loadPlug("payment");

        $payment  = get_payment("paypal");

        $order_sn = $_REQUEST['sn'];
        $token    = urlencode($_REQUEST['token']);
        $nvpstr   = "&TOKEN=".$token;
        $resArray = $this->hash_call("GetExpressCheckoutDetails", $nvpstr);
        $_SESSION['reshash'] = $resArray;
        $ack = strtoupper($resArray["ACK"]);

        if($ack=="SUCCESS"){
            $_SESSION['token']    = $_REQUEST['token'];
            $_SESSION['payer_id'] = $_REQUEST['PayerID'];

            $_SESSION['paymentAmount'] = $_REQUEST['paymentAmount'];
            $_SESSION['currCodeType']  = $_REQUEST['currencyCodeType'];
            $_SESSION['paymentType']   = $_REQUEST['paymentType'];

            $resArray = $_SESSION['reshash'];
            $token    = urlencode( $_SESSION['token']);

            $paymentAmount = urlencode($_SESSION['paymentAmount']);
            $paymentType   = urlencode($_SESSION['paymentType']);
            $currCodeType  = urlencode($_SESSION['currCodeType']);
            $payerID       = urlencode($_SESSION['payer_id']);
            $serverName    = urlencode($_SERVER['SERVER_NAME']);

            $nvpstr   = '&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currCodeType.'&IPADDRESS='.$serverName ;
            $resArray = $this->hash_call("DoExpressCheckoutPayment", $nvpstr);
            $ack = strtoupper($resArray["ACK"]);

            if($ack == "SUCCESS"){
                /* 改变订单状态 */
                order_paid($order_sn, 2);
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    function hash_call($methodName, $nvpStr){
        $version = '53.0';
        $API_UserName = $_SESSION['paypal_username'];
        $API_Password = $_SESSION['paypal_password'];
        $API_Signature = $_SESSION['paypal_signature'];
        $nvp_Header;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,API_ENDPOINT);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);

        if(USE_PROXY){
            curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT);
        }

        $nvpreq = "METHOD=".urlencode($methodName)."&VERSION=".urlencode($version)."&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature).$nvpStr;

        curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

        $response = curl_exec($ch);

        $nvpResArray = $this->deformatNVP($response);
        $nvpReqArray = $this->deformatNVP($nvpreq);
        $_SESSION['nvpReqArray'] = $nvpReqArray;

        if (curl_errno($ch)){
            $_SESSION['curl_error_no'] = curl_errno($ch) ;
            $_SESSION['curl_error_msg'] = curl_error($ch);
        }else{
            curl_close($ch);
        }

        return $nvpResArray;
    }


    function deformatNVP($nvpstr){

        $intial = 0;
        $nvpArray = array();

        while(strlen($nvpstr)){
            $keypos = strpos($nvpstr,'=');
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
            $keyval = substr($nvpstr,$intial,$keypos);
            $valval = substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
            $nvpArray[urldecode($keyval)] = urldecode( $valval);
            $nvpstr = substr($nvpstr,$valuepos+1,strlen($nvpstr));
        }

        return $nvpArray;
    }

}
