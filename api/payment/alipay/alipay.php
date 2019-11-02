<?php
/**
 * 支付宝在线支付主文件
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
    $payment[$i]['pay_name'] = "支付宝在线支付";

    /* 版本号 */
    $payment[$i]['version']  = '1.0.0';

    /* 描述 */
    $payment[$i]['pay_desc'] = '国内先进的网上支付平台。三种支付接口：担保交易，即时到账，双接口。在线即可开通，零预付，免年费，单笔阶梯费率，无流量限制。';

    /* 作者 */
    $payment[$i]['author']   = '酷曼软件';

    /* 网址 */
    $payment[$i]['website']  = 'http://www.kumanyun.com';

    /* 配置信息 */
    $payment[$i]['config'] = array(
        array('title' => '网页支付',           'type' => 'split'),
		array('title' => '支付宝帐户',         'name' => 'account', 'type' => 'text'),
        array('title' => '合作伙伴身份（PID）', 'name' => 'partner', 'type' => 'text'),
		array('title' => 'MD5密钥',            'name' => 'key',     'type' => 'text'),
		array('title' => '接口方式',           'name' => 'type',    'type' => 'select', 'options' => array('0' => '使用标准双接口', '1' => '使用担保交易接口', '2' => '使用即时到帐交易接口')),
        array('title' => 'APP支付',            'type' => 'split'),
		array('title' => '开放平台应用APPID',  'name' => 'appid',         'type' => 'text'),
		array('title' => '支付宝公钥',         'name' => 'alipayPublic',  'type' => 'textarea'),
		array('title' => '商户应用私钥',       'name' => 'appPrivate',    'type' => 'textarea'),
    );

    return;
}

/**
 * 类
 */
class alipay {

	/**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */

    function __construct(){
        $this->alipay();
    }

    function alipay(){}

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment){

        global $app;  //是否为客户端app支付
        global $huoniaoTag;
        global $cfg_secureAccess;
        global $cfg_basehost;
        global $cfg_staticPath;
        global $cfg_soft_lang;
        global $currency_rate;

		// 加载支付方式操作函数
		loadPlug("payment");

        $real_method = $payment['type'];
        switch ($real_method){
			//使用标准双接口
            case '0':
                $service = 'trade_create_by_buyer';
                break;
			//使用担保交易接口
            case '1':
                $service = 'create_partner_trade_by_buyer';
                break;
			//使用即时到帐交易接口
            case '2':
                $service = 'create_direct_pay_by_user';
                break;
        }

        //无线支付
        if(isMobile()){
            $service = 'alipay.wap.create.direct.pay.by.user';
        }

        $paramUrl = "&module=".$order['service']."&sn=".$order['order_sn'];

        $order_amount = sprintf("%.2f", $order['order_amount'] / $currency_rate);

        $parameter = array(
            'service'           => $service,                   //接口方式
            'partner'           => $payment['partner'],        //合作者身份ID
            '_input_charset'    => $cfg_soft_lang,             //字符类型
            'notify_url'        => notify_url("alipay", $paramUrl),       //服务器异步通知页面路径
            'return_url'        => return_url("alipay", $paramUrl),       //页面跳转同步通知页面路径
            'subject'           => $order['subject']."：".$order['order_sn'],          //订单名称
            'out_trade_no'      => $order['order_sn'],         //商户订单号
            'price'             => $order_amount,     //付款金额
            'quantity'          => 1,                          //商品数量
            'payment_type'      => 1,                          //支付类型
            'logistics_type'    => 'EXPRESS',                  //物流类型 必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
            'logistics_fee'     => 0,                          //物流费用
            'logistics_payment' => 'SELLER_PAY',               //物流支付方式 必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
            //'seller_email'      => $payment['account'],         //卖家支付宝帐户
        );


        //客户端APP支付
        if($app){

            $rsaPrivateKey = $payment['appPrivate'];

            //订单信息
            $content = array();
            $content['subject'] = $order['subject']."：".$order['order_sn'];
            $content['out_trade_no'] = $order['order_sn'];
            $content['total_amount'] = $order['order_amount'];
            $content['product_code'] = 'QUICK_MSECURITY_PAY';
            $biz_content = json_encode($content);

            //公共参数
            $param = array();
            $param['app_id'] = $payment['appid'];
            $param['method'] = 'alipay.trade.app.pay';
            $param['format'] = 'json';
            $param['charset'] = $cfg_soft_lang;
            $param['sign_type'] = 'RSA2';
            $param['timestamp'] = date("Y-m-d H:i:s");
            $param['version'] = '1.0';
            $param['notify_url'] = $cfg_secureAccess.$cfg_basehost.'/api/payment/alipayAppNotify.php';
            $param['biz_content'] = $biz_content;
            ksort($param);

            $paramStr = "";
            $paramStr_ = "";
            foreach ($param as $key => $val){
                $paramStr .= $key."=".$val."&";   //生成sign不需要encode
                $paramStr_ .= $key."=".urlencode($val)."&";   //最终输出需要encode
            }

            $paramStr = substr($paramStr, 0, -1);
            $paramStr_ = substr($paramStr_, 0, -1);

            //获取sign
            $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
				wordwrap($rsaPrivateKey, 64, "\n", true) .
				"\n-----END RSA PRIVATE KEY-----";
            openssl_sign($paramStr, $sign, $res, OPENSSL_ALGO_SHA256);
            $sign = urlencode(base64_encode($sign));


            //配置页面信息
            $tpl = HUONIAOROOT."/templates/member/touch/";
            $templates = "public-app-pay.html";
            if(file_exists($tpl.$templates)){
                $huoniaoTag->template_dir = $tpl;
                $huoniaoTag->assign('cfg_basehost', $cfg_secureAccess.$cfg_basehost);
                $huoniaoTag->assign('cfg_staticPath', $cfg_staticPath);
                $huoniaoTag->assign('appCall', "aliPay");
                $huoniaoTag->assign('service', $order['service']);
                $huoniaoTag->assign('ordernum', $order['ordernum']);
                $huoniaoTag->assign('orderInfo', $paramStr_."&sign=".$sign);
                $huoniaoTag->display($templates);
            }

            die;
        }




        //网银支付
        if(!empty($order['bank'])){
            $parameter['paymethod']   = "bankPay";
            $parameter['defaultbank'] = $order['bank'];
        }

        //无线支付
        if(isMobile()){
            $parameter['seller_id'] = $payment['partner'];
            $parameter['total_fee'] = $order_amount;
            $parameter['app_pay']   = 'Y';
        }else{
            $parameter['seller_email'] = $payment['account'];
        }


        ksort($parameter);
        reset($parameter);

        $param = array();
        $sign  = '';

        foreach ($parameter as $key => $val){
            $param[$key] = $val;
            $sign  .= "$key=$val&";
        }

        $sign  = substr($sign, 0, -1). $payment['key'];
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
  <form id="pay_form" name="pay_form" action="https://mapi.alipay.com/gateway.do" method="get">

eot;
        foreach ($param as $key => $value) {
            $html .= "    <input type=\"hidden\" name=\"{$key}\" value=\"{$value}\" />\n";
        }
		$html .= "    <input type=\"hidden\" name=\"sign\" value=\"{$sign}\" />\n";
		$html .= "    <input type=\"hidden\" name=\"sign_type\" value=\"MD5\" />\n";
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

        $payment  = get_payment("alipay");

		/* GET */
		foreach($_GET as $k => $v) {
			$_GET[$k] = $v;
		}
		/* POST */
		foreach($_POST as $k => $v) {
			$_GET[$k] = $v;
		}

        $order_sn     = $_GET['out_trade_no'];
        $trade_no     = $_GET['trade_no'];

        /* 检查支付的金额是否相符 */
        if (!check_money($order_sn, $_GET['total_fee'])){
           return false;
       }

        /* 检查数字签名是否正确 */
        ksort($_GET);
        reset($_GET);

        $sign = '';
        foreach ($_GET AS $key=>$val){
            if ($key != 'sign' && $key != 'sign_type' && $key != 'code' && $key != 'module' && $key != 'sn' && $key != ''){
                $sign .= "$key=$val&";
            }
        }

        $sign = substr($sign, 0, -1) . $payment['key'];

        if (md5($sign) != $_GET['sign']){
            return false;
        }

		//买家付款，等待卖家发货
        if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS'){
            /* 改变订单状态 */
            order_paid($order_sn, $trade_no);

            return true;

		//交易完成
        }elseif ($_GET['trade_status'] == 'TRADE_FINISHED'){
            /* 改变订单状态 */
            order_paid($order_sn, $trade_no);

            return true;

		//支付成功
        }elseif ($_GET['trade_status'] == 'TRADE_SUCCESS'){
            /* 改变订单状态 */
            order_paid($order_sn, $trade_no);

            return true;

        }else{
            return false;
        }
    }

    /**
     * 响应操作
     */
    function respondApp(){

        // 加载支付方式操作函数
        loadPlug("payment");

        $payment  = get_payment("alipay");

		/* GET */
		foreach($_GET as $k => $v) {
			$_GET[$k] = $v;
		}
		/* POST */
		foreach($_POST as $k => $v) {
			$_GET[$k] = $v;
		}

        //订单号
        $order_sn = $_GET['out_trade_no'];

        //签名
        $sign = base64_decode($_GET['sign']);

        /* 检查支付的金额是否相符 */
        if (!check_money($order_sn, $_GET['total_amount'])){
           return false;
        }

        /* 检查数字签名是否正确 */
        ksort($_GET);
        reset($_GET);

        $paramSignStr = '';
        foreach ($_GET AS $key=>$val){
           if ($key != 'sign' && $key != 'sign_type'){
               $paramSignStr .= "$key=".urldecode($val)."&";
           }
        }
        $paramSignStr = substr($paramSignStr, 0, -1);

        $pubKey = $payment['alipayPublic'];

         //获取sign
         $res = "-----BEGIN PUBLIC KEY-----\n" .
				wordwrap($pubKey, 64, "\n", true) .
				"\n-----END PUBLIC KEY-----";
         $verify = (bool)openssl_verify($paramSignStr, $sign, $res, OPENSSL_ALGO_SHA256);

         //验证签名
         if(!$verify){
            return false;
         }


       //买家付款，等待卖家发货
       if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS'){
           /* 改变订单状态 */
           order_paid($order_sn);

           return true;

       //交易完成
       }elseif ($_GET['trade_status'] == 'TRADE_FINISHED'){
           /* 改变订单状态 */
           order_paid($order_sn);

           return true;

       //支付成功
       }elseif ($_GET['trade_status'] == 'TRADE_SUCCESS'){
           /* 改变订单状态 */
           order_paid($order_sn);

           return true;

       }else{
           return false;
       }



    }

}
