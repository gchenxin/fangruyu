<?php
/**
 * 财付通在线支付主文件
 *
 * @version        $Id: tenpay.php $v1.0 2014-3-12 下午22:03:18 $
 * @package        HuoNiao.Payment
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

if(!defined('HUONIAOINC')) exit('Request Error!');

/* 基本信息 */
if(isset($set_modules) && $set_modules == TRUE){

    $i = isset($payment) ? count($payment) : 0;

    /* 代码 */
    $payment[$i]['pay_code'] = "tenpay";

	/* 名称 */
    $payment[$i]['pay_name'] = "财付通在线支付";

    /* 版本号 */
    $payment[$i]['version']  = '1.0.0';

    /* 描述 */
    $payment[$i]['pay_desc'] = '腾讯旗下在线支付平台，通过国家权威安全认证，支持各大银行网上支付，免支付手续费。';

    /* 作者 */
	$payment[$i]['author']   = '酷曼软件';

    /* 网址 */
    $payment[$i]['website']  = 'http://www.kumanyun.com';

    /* 配置信息 */
    $payment[$i]['config'] = array(
		array('title' => '商户号',   'name' => 'partner', 'type' => 'text'),
		array('title' => '商户密钥',  'name' => 'key',     'type' => 'text'),
		array('title' => '接口方式',  'name' => 'type',    'type' => 'select', 'options' => array('1' => '即时到帐', '2' => '中介担保', '3' => '后台选择'))
    );

    return;
}

/**
 * 类
 */
class tenpay {

	/**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */

    function __construct(){
        $this->tenpay();
    }

    function tenpay(){}

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment){
		global $cfg_soft_lang;

		// 加载支付方式操作函数
		loadPlug("payment");

        $bank = $order['bank'] != "" ? $order['bank'] : "DEFAULT";
		$paramUrl = "&module=".$order['service']."&sn=".$order['order_sn'];

        $parameter = array(
			'bank_type'        => $bank,                          //银行类型，默认为财付通
			'fee_type'         => 1,                              //币种
			'service_version'  => '1.0',                          //接口版本号
			'sign_key_index'   => 1,                              //密钥序号
			'trans_type'       => 1,                              //交易类型
			'transport_fee'    => 0,                              //物流费用
			'sign_type'        => 'MD5',                          //签名方式，默认为MD5，可选RSA
			'subject'          => $order['order_sn'],             //商品名称，（中介交易时必填）
			'body'             => $order['subject']."：".$order['order_sn'],             //商品内容
			'input_charset'    => $cfg_soft_lang,                 //字符集
			'notify_url'       => notify_url("tenpay", $paramUrl),           //服务器异步通知页面路径
			'return_url'       => return_url("tenpay", $paramUrl),           //页面跳转同步通知页面路径
			'out_trade_no'     => $order['order_sn'],             //商户订单号
			'partner'          => $payment['partner'],            //商户号
			'spbill_create_ip' => GetIP(),                        //客户端IP
			'time_start'       => date('YmdHis'),                 //订单生成时间
			'total_fee'        => $order['order_amount'] * 100,   //总金额
			'trade_mode'       => $payment['type']                //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
        );

        ksort($parameter);
        reset($parameter);

        $param = array();
        $sign  = '';

        foreach ($parameter as $key => $val){
            $param[$key] = $val;
            $sign  .= "$key=$val&";
        }

        $sign  = $sign . "key=" . $payment['key'];

		return $this->create_html($param, md5($sign));
    }

	/**
	 * 生成跳转表单
	 */
	function create_html($param, $sign){
		global $cfg_soft_lang;
        $html = <<<eot
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=$cfg_soft_lang" />
</head>
<body onload="javascript:document.pay_form.submit();">
  <form id="pay_form" name="pay_form" action="https://gw.tenpay.com/gateway/pay.htm" method="POST">

eot;
        foreach ($param as $key => $value) {
            $html .= "    <input type=\"hidden\" name=\"{$key}\" value=\"{$value}\" />\n";
        }
		$html .= "    <input type=\"hidden\" name=\"sign\" value=\"{$sign}\" />\n";
        $html .= <<<eot
    <input type="submit" type="hidden" style="display:none;">
  </form>
</body>
</html>
eot;
		return $html;
    }

    /**
     * 响应操作
     */
    function respond(){

        // 加载支付方式操作函数
        loadPlug("payment");
        $payment  = get_payment("tenpay");

        /* GET */
		foreach($_GET as $k => $v) {
			$_GET[$k] = $v;
		}
		/* POST */
		foreach($_POST as $k => $v) {
			$_GET[$k] = $v;
		}

        $order_sn = $_GET['out_trade_no'];

        /* 检查支付的金额是否相符 */
        if (!check_money($order_sn, $_GET['total_fee']/100)){
           return false;
       }

        /* 检查数字签名是否正确 */
        ksort($_GET);
        reset($_GET);

        $sign = '';
        foreach ($_GET AS $key=>$val){
            if ($key != 'sign' && $key != 'code' && $key != 'module' && $key != 'sn' && $key != ''){
                $sign .= "$key=$val&";
            }
        }

        $sign  = $sign . "key=" . $payment['key'];

        if (strtoupper(md5($sign)) != $_GET['sign']){
            return false;
        }

		//支付成功
        if ($_GET['trade_state'] == '0'){
            order_paid($order_sn);
            return true;

        }else{
            return false;
        }
    }

}
