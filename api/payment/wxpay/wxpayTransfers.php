<?php
/**
 * 企业付款到零钱
 *
 * @version        $Id: wxpayTransfers.php $v1.0 2019-7-9 下午17:24:16 $
 * @package        HuoNiao.Payment
 * @copyright      Copyright (c) 2013 - 2019, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

if(!defined('HUONIAOINC')) exit('Request Error!');


/**
 * 类
 */
class wxpayTransfers {

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
    public $wxpayrsaPublicKey;

    function __construct(){
        $this->wxpayTransfers();
    }

    function wxpayTransfers(){

        // 加载支付方式操作函数
        loadPlug("payment");
        $payment = get_payment("wxpay");

        $this->appId = $payment['APPID'];
        $this->mch_id = $payment['MCHID'];
        $this->key = $payment['KEY'];

        $this->app_appId = $payment['APP_APPID'];
        $this->app_mch_id = $payment['APP_MCHID'];
        $this->app_key = $payment['APP_KEY'];
    }

    function transfers($order, $app = false){
        if($app){
            $appId = $this->app_appId;
            $mch_id = $this->app_mch_id;
            $key = $this->app_key;
        }else{
            $appId = $this->appId;
            $mch_id = $this->mch_id;
            $key = $this->key;
        }

        global $cfg_shortname;

        // 随机数
        $nonce_str = genSecret(16, 2);

        // ----------订单信息
        // 商户订单号
        $out_trade_no = $order['ordernum'];
        // 绑定微信的openid
        $openid = $order['openid'];
        // 姓名
        $name = $order['name'];
        // 提现金额
        $amount = $order['amount'] * 100;
        // 备注
        $desc = "余额提现";

        $transfers = array(
            'amount' => $amount,                  //金额
            'check_name' => 'FORCE_CHECK',        //NO_CHECK：不校验真实姓名  FORCE_CHECK：强校验真实姓名
            'desc' => $desc,                      //企业付款备注
            'mch_appid' => $appId,                //应用ID，固定
            'mchid' => $mch_id,                   //商户号，固定
            'nonce_str' => $nonce_str,            //随机字符串
            'openid' => $openid,                  //用户openid
            'partner_trade_no' => $out_trade_no,  //商户内部唯一退款单号
            're_user_name' => $name,              //收款用户姓名
            'spbill_create_ip' => GetIP()         //IP地址
        );

        $stringA = "";
        foreach ($transfers as $k => $v) {
            $stringA = $stringA.$k."=".$v."&";
        }

        $stringSignTemp = $stringA."key=".$key; //注：key为商户平台设置的密钥key

        $sign = strtoupper(MD5($stringSignTemp)); //注：MD5签名方式

        $transfers['sign'] = $sign;

        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";  //企业付款到零钱，请求Url
        $xml = arrayToXml($transfers);

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);//证书检查
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
        curl_setopt($ch,CURLOPT_SSLCERT,dirname(__FILE__).'/cert'. ($app ? '/app' : '') .'/apiclient_cert.pem');
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
        curl_setopt($ch,CURLOPT_SSLKEY,dirname(__FILE__).'/cert'. ($app ? '/app' : '') .'/apiclient_key.pem');
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
        curl_setopt($ch,CURLOPT_CAINFO,dirname(__FILE__).'/cert'. ($app ? '/app' : '') .'/rootca.pem');
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);

        //返回来的是xml格式需要转换成数组再提取值，用来做更新
        if($data){

            curl_close($ch);
            $data = strstr($data, "<xml");

            $r = false;
            $errcode = "";

            $p = xml_parser_create();
            $parse = xml_parse_into_struct($p, $data, $vals, $title);
            xml_parser_free($p);

            foreach ($title as $k => $value) {
                $k = strtoupper($k);
                $res = $vals[$value[0]]['value'];
                $$k = strtoupper($res);
            }

            // 请求结果
            if($RETURN_CODE == "SUCCESS"){

                // 业务结果 提现申请接收成功
                if($RESULT_CODE == "SUCCESS"){
                    return array("state" => 100, "date" => GetMkTime($PAYMENT_TIME), "payment_no" => $PAYMENT_NO);
                }else{

                    if($ERR_CODE == 'NAME_MISMATCH'){
                        $ERR_CODE_DES = '实名认证姓名与提现的微信实名信息不一致！';
                    }elseif($ERR_CODE == 'NOTENOUGH'){
                        $ERR_CODE_DES = '系统账户余额不足！';
                    }

                    return array("state" => 200, "info" => "提现失败，错误信息：" . $ERR_CODE_DES);
                }

            }else{
                return array("state" => 200, "info" => "$RETURN_MSG");
            }
        }else{
            $error = curl_error($ch);
            curl_close($ch);
            return array("state" => 200, "info" => "curl出错，错误代码：$error");
        }

    }

}

function arrayToXml($arr){
    $xml = "<xml>";
    foreach ($arr as $key=>$val){
        if(is_array($val)){
            $xml.="<".$key.">".arrayToXml($val)."</".$key.">";
        }else{
            $xml.="<".$key.">".$val."</".$key.">";
        }
    }
    $xml.="</xml>";
    return $xml ;
}
