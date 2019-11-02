<?php
/**
 * 微信扫码支付主文件
 *
 * @version        $Id: wxpay.php $v1.0 2015-12-10 下午23:35:11 $
 * @package        HuoNiao.Payment
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

if(!defined('HUONIAOINC')) exit('Request Error!');

/* 基本信息 */
if(isset($set_modules) && $set_modules == TRUE){

    $i = isset($payment) ? count($payment) : 0;

    /* 代码 */
    $payment[$i]['pay_code'] = "wxpay";

	/* 名称 */
    $payment[$i]['pay_name'] = "微信扫码支付";

    /* 版本号 */
    $payment[$i]['version']  = '1.0.0';

    /* 描述 */
    $payment[$i]['pay_desc'] = '用户使用微信“扫一扫”扫描二维码后，引导用户完成支付。';

    /* 作者 */
    $payment[$i]['author']   = '酷曼软件';

    /* 网址 */
    $payment[$i]['website']  = 'http://www.kumanyun.com';

    /* 配置信息 */
    $payment[$i]['config'] = array(
        array('title' => '网页支付',  'type' => 'split'),
		array('title' => 'APPID',     'name' => 'APPID',      'type' => 'text'),
		array('title' => '商户号',     'name' => 'MCHID',     'type' => 'text'),
		array('title' => 'KEY',       'name' => 'KEY',        'type' => 'text'),
		array('title' => 'APPSECRET', 'name' => 'APPSECRET',  'type' => 'text'),
        array('title' => 'APP支付',    'type' => 'split'),
		array('title' => 'APPID',     'name' => 'APP_APPID',     'type' => 'text'),
		array('title' => '商户号',     'name' => 'APP_MCHID',     'type' => 'text'),
		array('title' => 'KEY',       'name' => 'APP_KEY',       'type' => 'text'),
		array('title' => 'APPSECRET', 'name' => 'APP_APPSECRET', 'type' => 'text')
    );

    return;
}

/**
 * 类
 */
class wxpay {

	/**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */

    function __construct(){
        $this->wxpay();
    }

    function wxpay(){}

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment){

        // 加载支付方式操作函数
        loadPlug("payment");

        global $app;  //是否为客户端app支付
        global $huoniaoTag;
        global $cfg_basehost;
        global $cfg_webname;
        global $cfg_shortname;
        global $cfg_staticPath;
        global $cfg_soft_lang;
        global $cfg_secureAccess;
        global $dsql;
        global $userLogin;
        $cfg_basehost_ = $cfg_secureAccess.$cfg_basehost;
        $notify_url = $cfg_basehost_.'/api/payment/wxpayNotify.php';

        global $currency_rate;
        $order_amount = (sprintf("%.2f", $order['order_amount'] / $currency_rate)) * 100;

        require_once "WxPay.Api.php";

        //小程序
        $isWxMiniprogram = GetCookie('isWxMiniprogram');

        if($app){
            define('APPID', $payment['APP_APPID']);
            define('MCHID', $payment['APP_MCHID']);
            define('KEY', $payment['APP_KEY']);
            define('APPSECRET', $payment['APP_APPSECRET']);

        }elseif($isWxMiniprogram){
            global $cfg_miniProgramAppid;
            global $cfg_miniProgramAppsecret;
            define('APPID', $cfg_miniProgramAppid);
            define('APPSECRET', $cfg_miniProgramAppsecret);
            define('MCHID', $payment['MCHID']);
            define('KEY', $payment['KEY']);

        }else{
            define('APPID', $payment['APPID']);
            define('APPSECRET', $payment['APPSECRET']);
            define('MCHID', $payment['MCHID']);
            define('KEY', $payment['KEY']);
        }

        //客户端APP支付
        if($app){

            $input = new WxPayUnifiedOrder();
            $input->SetBody($order['subject']."：".$order['order_sn']);
            $input->SetAttach("huoniaoCMS");
            $input->SetOut_trade_no($order['order_sn']);
            $input->SetTotal_fee($order_amount);
            $input->SetTime_start(date("YmdHis"));
//            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("huoniaoCMS");
            $input->SetNotify_url($cfg_basehost_.'/api/payment/wxpayAppNotify.php');
            $input->SetTrade_type("APP");
            $wxorder = WxPayApi::unifiedOrder($input);
            if($wxorder['return_code'] == "FAIL"){
                die("APP支付错误：" . $wxorder['return_msg']);
            }

            $param["appid"]     =  $wxorder["appid"];
            $param["partnerid"]    =  $wxorder["mch_id"];
            $param["noncestr"] =  $wxorder["nonce_str"];
            $param["package"]   =  "Sign=WXPay";
            $param["prepayid"] =  $wxorder["prepay_id"];
            $param["timestamp"] =  time();
            ksort($param);

            $paramStr = "";
            foreach ($param as $key => $val){
                $paramStr .= $key."=".$val."&";
            }
            $param["sign"] = strtoupper(md5($paramStr."key=".$payment['APP_KEY']));

            //对数据重新拼装
            $orderInfo = array(
                "appId" => $param['appid'],
                "partnerId" => $param['partnerid'],
                "nonceStr"  => $param['noncestr'],
                "package"   => $param['package'],
                "prepayId"  => $param['prepayid'],
                "timeStamp" => $param['timestamp'],
                "sign"      => $param['sign']
            );

            //配置页面信息
            $tpl = HUONIAOROOT."/templates/member/touch/";
            $templates = "public-app-pay.html";
            if(file_exists($tpl.$templates)){
                $huoniaoTag->template_dir = $tpl;
                $huoniaoTag->assign('cfg_basehost', $cfg_basehost_);
                $huoniaoTag->assign('cfg_staticPath', $cfg_staticPath);
                $huoniaoTag->assign('appCall', "wechatPay");
                $huoniaoTag->assign('service', $order['service']);
                $huoniaoTag->assign('ordernum', $order['ordernum']);
                $huoniaoTag->assign('orderInfo', json_encode($orderInfo));
                $huoniaoTag->display($templates);
            }

            die;
        }




        //无线支付
        if(isMobile()){

          //公众号支付
          if(isWeixin()){

            //根据支付订单号查询商品订单号
            // global $dsql;
            // $sql = $dsql->SetQuery("SELECT `body` FROM `#@__pay_log` WHERE `ordernum` = '".$order['order_sn']."'");
            // $ret = $dsql->dsqlOper($sql, "results");
            // if($ret){

              // $RenrenCrypt = new RenrenCrypt();
              // $encodeid = base64_encode($RenrenCrypt->php_encrypt($ret[0]['body']));

              if($order['service'] == "member"){
                  $param = array(
                    "service"  => $order['service'],
                    "type"     => "user",
                    "template" => "record"
                  );
              }else{
                  $param = array(
                    "service"  => $order['service'],
                    "template" => "payreturn",
                    "ordernum" => $order['order_sn']
                  );
              }
              $returnUrl = getUrlPath($param);

              $_SESSION['wxPayReturnUrl'] = $returnUrl;
            //}else{
              //$returnUrl = $cfg_basehost_;
            //}

            require_once "WxPay.JsApiPay.php";

            //①、获取用户openid
            $tools = new JsApiPay();
            if($isWxMiniprogram){
                $userid = $userLogin->getMemberID();

                $openId = '';
                $conn = '';
                $sql = $dsql->SetQuery("SELECT `wechat_mini_openid`, `wechat_conn` FROM `#@__member` WHERE `id` = $userid");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $openId = $ret[0]['wechat_mini_openid'];
                    $conn = $ret[0]['wechat_conn'];
                }

                if(!$openId){

                    //读取unionid
                    $sql = $dsql->SetQuery("SELECT `id`, `openid`, `unionid` FROM `#@__site_wxmini_unionid` WHERE `conn` = '$conn'");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $_unionid = $ret[0]['id'];
                        $miniProgram_openid = $ret[0]['openid'];
                        $miniProgram_unionid = $ret[0]['unionid'];

                        $sql = $dsql->SetQuery("UPDATE `#@__member` SET `wechat_mini_session` = '$miniProgram_unionid', `wechat_mini_openid` = '$miniProgram_openid' WHERE `id` = $userid");
                        $dsql->dsqlOper($sql, "update");
                        $openId = $miniProgram_openid;

                        //用完后删除记录
                        $sql = $dsql->SetQuery("DELETE FROM `#@__site_wxmini_unionid` WHERE `id` = $_unionid");
                        $dsql->dsqlOper($sql, "update");

                    }else{
                        $param = array(
                            'service' => 'member',
                            'type' => 'user',
                            'template' => 'connect'
                        );
                        $url = getUrlPath($param);
                        die("<script>alert('请先在我的会员中心=>安全中心=>社交账号关联绑定中，绑定微信快捷登录，然后再支付！');location.href='".$url."';</script>");
                    }

                }

            }else{
                $openId = $tools->GetOpenid();
            }

            //②、统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody($order['subject']."：".$order['order_sn']);
            $input->SetAttach("huoniaoCMS");
            $input->SetOut_trade_no($order['order_sn']);
            $input->SetTotal_fee($order_amount);
            $input->SetTime_start(date("YmdHis"));
//            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("huoniaoCMS");
            $input->SetNotify_url($notify_url);
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            if($order['return_code'] == "FAIL"){
                die("公众号支付错误：" . $order['return_msg']);
            }
            $jsApiParameters = $tools->GetJsApiParameters($order);

            //配置页面信息
            $tpl = HUONIAOROOT."/templates/siteConfig/";
            $templates = "wxpayTouch.html";
            if(file_exists($tpl.$templates)){
                global $huoniaoTag;
                global $cfg_staticPath;
                $huoniaoTag->template_dir = $tpl;
                $huoniaoTag->assign('cfg_basehost', $cfg_basehost_);
                $huoniaoTag->assign('cfg_staticPath', $cfg_staticPath);
                $huoniaoTag->assign('ordernum', $order['order_sn']);
                $huoniaoTag->assign('returnUrl', $returnUrl);
                $huoniaoTag->assign('jsApiParameters', $jsApiParameters);
                $huoniaoTag->display($templates);
            }


          //H5支付
          }else{

            $out_trade_no = $order['order_sn'];//平台内部订单号
            $nonce_str = MD5($out_trade_no);//随机字符串
            $body = $order['subject'];//内容
            $total_fee = $order_amount; //金额
            $spbill_create_ip = GetIP(); //IP
            $notify_url = $notify_url; //回调地址
            $trade_type = 'MWEB';//交易类型 具体看API 里面有详细介绍
            $scene_info ='{"h5_info":{"type":"Wap","wap_url":"'.$cfg_basehost_.'","wap_name":"'.$cfg_shortname.'"}}';//场景信息 必要参数
            $signA ="appid=".APPID."&body=$body&mch_id=".MCHID."&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
            $strSignTmp = $signA."&key=".KEY; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML  是否正确
            $sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写
            $post_data = "<xml>
                           <appid><![CDATA[".APPID."]]></appid>
                           <body><![CDATA[$body]]></body>
                           <mch_id><![CDATA[".MCHID."]]></mch_id>
                           <nonce_str><![CDATA[$nonce_str]]></nonce_str>
                           <notify_url><![CDATA[$notify_url]]></notify_url>
                           <out_trade_no><![CDATA[$out_trade_no]]></out_trade_no>
                           <scene_info><![CDATA[$scene_info]]></scene_info>
                           <spbill_create_ip><![CDATA[$spbill_create_ip]]></spbill_create_ip>
                           <total_fee><![CDATA[$total_fee]]></total_fee>
                           <trade_type><![CDATA[$trade_type]]></trade_type>
                           <sign><![CDATA[$sign]]></sign>
                       </xml>";//拼接成XML 格式
            $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HEADER,0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $dataxml = curl_exec($ch);
            if ($dataxml === FALSE){
    			    echo 'cURL Error:'.curl_error($ch);
      			}
            curl_close($ch);
            $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组

            //成功
            if($objectxml['return_code'] == 'SUCCESS' && $objectxml['result_code'] == 'SUCCESS'){
              header("Location:" . $objectxml['mweb_url']);
            }else{
              die('H5支付错误：' . $objectxml['return_code'] . " => " . $objectxml['return_msg']);
            }
            die;

          }


        //PC端支付
        }else{


            //=======【curl代理设置】===================================
            /**
             * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
             * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
             * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
             * @var unknown_type
             */
            define('CURL_PROXY_HOST', "0.0.0.0");
            define('CURL_PROXY_PORT', 0);

            //=======【上报信息配置】===================================
            /**
             * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
             * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
             * 开启错误上报。
             * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
             * @var int
             */
            define('REPORT_LEVENL', 1);

            require_once "WxPay.NativePay.php";


            //组合付款参数，并生成付款URL
            $notify = new NativePay();
            $input = new WxPayUnifiedOrder();
            $input->SetBody($order['subject']."：".$order['order_sn']);
            $input->SetOut_trade_no($order['order_sn']);
            $input->SetTotal_fee($order_amount);
            $input->SetTime_start(date("YmdHis"));
//            $input->SetTime_expire(date("YmdHis", time() + 600));

            $input->SetNotify_url($notify_url);
            $input->SetTrade_type("NATIVE");
            $input->SetProduct_id($order['subject']);
            $result = $notify->GetPayUrl($input);
            if($result['return_code'] == "FAIL"){
                die("PC支付错误：" . $result['return_msg']);
            }
            $url = $result["code_url"];


            //配置页面信息
            $tpl = HUONIAOROOT."/templates/siteConfig/";
            $templates = "wxpay.html";
            if(file_exists($tpl.$templates)){
                global $huoniaoTag;
                global $cfg_staticPath;
                $huoniaoTag->template_dir = $tpl;
                $huoniaoTag->assign('cfg_staticPath', $cfg_staticPath);
                $huoniaoTag->assign('url', $url);
                $huoniaoTag->assign('order', $order);
                $huoniaoTag->display($templates);
            }else{
                echo '<img src="/include/qrcode.php?data='.urlencode($url).'" style="width:150px;height:150px;"/>';
            }

        }


    }


}
