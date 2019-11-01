<?php
/**
 * 微信付款到零钱文件
 *
 * @version        $Id: wxpayTransfer.php $v1.0 2018-07-16 下午16:05:18 $
 * @package        HuoNiao.Payment
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

if(!defined('HUONIAOINC')) exit('Request Error!');


/**
 * 类
 */
class wxpayTransfer {

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
    $this->wxpayRefund();
  }

  function wxpayRefund(){

    // 加载支付方式操作函数
    loadPlug("payment");
    $payment = get_payment("wxpay");

    $this->appId = $payment['APPID'];
    $this->mch_id = $payment['MCHID'];
    $this->key = $payment['KEY'];
  }

  function transfer($order = array()){
    $appId = $this->appId;
    $mch_id = $this->mch_id;
    $key = $this->key;

    $date = GetMkTime(time());      //时间戳
    $nonce_str = genSecret(16, 2);  //随机数
    $out_refund_no = date(YmdHis);  //订单号
    $re_user_openid = $order['account'];   //用户openid
    $re_user_name = $order['realname'];  //收款用户真实姓名
    $amount = 100 * $order['amount'];  //付款金额，单位为分
    $desc = $order['remark'];  //企业付款描述信息

    $refund = array(
      'amount' => $amount,                   //金额
      'check_name' => 'FORCE_CHECK',         //校验用户姓名选项    NO_CHECK：不校验真实姓名    FORCE_CHECK：强校验真实姓名
      'desc' => $desc,                       //企业付款描述信息
      'mch_appid' => $appId,                 //应用ID，固定
      'mchid' => $mch_id,                    //商户号，固定
      'nonce_str' => $nonce_str,             //随机字符串
      'openid' => $re_user_openid,           //用户openid
      'partner_trade_no' => $out_refund_no,  //商户订单号
      're_user_name' => $re_user_name,       //收款用户姓名
      'spbill_create_ip' => getIP(),         //Ip地址
    );

    $stringA = "";
    foreach ($refund as $k => $v) {
        $stringA = $stringA.$k."=".$v."&";
    }

    $stringSignTemp = $stringA."key=".$key; //注：key为商户平台设置的密钥key
    $sign = strtoupper(MD5($stringSignTemp)); //注：MD5签名方式
    $refund['sign'] = $sign;

    $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";;//微信退款地址，post请求
    $xml = arrayToXml($refund);

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_HEADER,1);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);//证书检查
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);//证书检查
    curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
    curl_setopt($ch,CURLOPT_SSLCERT,HUONIAOROOT.'/api/payment/wxpay/cert/apiclient_cert.pem');
    curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
    curl_setopt($ch,CURLOPT_SSLKEY,HUONIAOROOT.'/api/payment/wxpay/cert/apiclient_key.pem');
    curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
    curl_setopt($ch,CURLOPT_CAINFO,HUONIAOROOT.'/api/payment/wxpay/cert/rootca.pem');
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

    $data = curl_exec($ch);

    //返回来的是xml格式需要转换成数组再提取值，用来做更新
    if($data){

      $data = strstr($data, "<xml");
      $p = xml_parser_create();
      $parse = xml_parse_into_struct($p, $data, $vals, $title);
      xml_parser_free($p);

      foreach ($title as $k => $value) {
        $k = strtoupper($k);
        $res = $vals[$value[0]]['value'];
        $$k = strtoupper($res);
      }

      if($RETURN_CODE == "SUCCESS" && $RESULT_CODE == "SUCCESS"){
        return array("state" => 100, "order_id" => $PAYMENT_NO, "pay_date" => $PAYMENT_TIME);
      }else{
        return array("state" => 101, "info" => $ERR_CODE_DES, "code" => $ERR_CODE);
      }

      echo "return_code: " . $RETURN_CODE."<br />";
      echo "return_msg: " . $RETURN_MSG."<br />";
      echo "result_code: " . $RESULT_CODE."<br />";
      echo "err_code: " . $ERR_CODE."<br />";
      echo "err_code_des: " . $ERR_CODE_DES."<br />";
      echo "payment_no: " . $PAYMENT_NO."<br />";    //微信订单号
      echo "payment_time: " . $PAYMENT_TIME."<br />";    //微信支付成功时间


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

//调用方式
// $wxpayTransfer = new wxpayTransfer();
// $return = $wxpayTransfer->transfer();
