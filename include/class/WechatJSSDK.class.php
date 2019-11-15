<?php
class WechatJSSDK {
  public $appId;
  public $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    global $cfg_secureAccess;

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = $cfg_secureAccess;
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage;
  }

  public function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  public function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    //$data = json_decode(@file_get_contents(HUONIAOROOT."/wechat_jsapi_ticket.json?v=1"));
    //if (!$data || $data->expire_time < time()) {
	if(empty($_COOKIE['gzh_jsTicket'])){
      $accessToken = $this->getAccessToken();
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
		setcookie('gzh_jsTicket',$ticket, time() + 7000);
        $fp = @fopen(HUONIAOROOT."/wechat_jsapi_ticket.json", "w");
        @fwrite($fp, json_encode($data));
		@fclose($fp);
      }
    } else {
		//$ticket = $data->jsapi_ticket;
		$ticket = $_COOKIE['gzh_jsTicket'];
    }

    return $ticket;
  }

  public function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    //$data = json_decode(@file_get_contents(HUONIAOROOT."/wechat_access_token.json?v=1"));
    //if (!$data || $data->expire_time < time()) {
	if(empty($_COOKIE['gzh_webAccessToken'])){
	  $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
	  if ($access_token) {
		setcookie('gzh_webAccessToken',$access_token, time()+7000);
        $fp = @fopen(HUONIAOROOT."/wechat_access_token.json", "w");
        @fwrite($fp, json_encode($data));
		@fclose($fp);
    }
  } else {
	  $access_token = $_COOKIE['gzh_webAccessToken'];
      //$access_token = $data->access_token;
    }
    return $access_token;
  }

  public function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }

  
}
