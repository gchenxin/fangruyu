<?php
//微信支付服务器异步通知页面路径
require_once(dirname(__FILE__)."/../../include/common.inc.php");

//获取配置信息
$archives = $dsql->SetQuery("SELECT `pay_config` FROM `#@__site_payment` WHERE `pay_code` = 'wxpay' AND `state` = 1");
$payment   = $dsql->dsqlOper($archives, "results");
if(!$payment) die("支付方式不存在！");

$pay_config = unserialize($payment[0]['pay_config']);
$paymentArr = array();

//验证配置
foreach ($pay_config as $key => $value) {
	if(!empty($value['value'])){
		$paymentArr[$value['name']] = $value['value'];
	}
}

// 加载支付方式操作函数
loadPlug("payment");

define('APPID', $paymentArr['APPID']);
define('MCHID', $paymentArr['MCHID']);
define('KEY', $paymentArr['KEY']);

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

require_once dirname(__FILE__)."/wxpay/WxPay.Api.php";
require_once dirname(__FILE__)."/wxpay/WxPay.Notify.php";
require_once dirname(__FILE__)."/log.php";

//初始化日志
$logHandler= new CLogFileHandler(date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify{
  //查询订单
  public function Queryorder($transaction_id){
      $input = new WxPayOrderQuery();
      $input->SetTransaction_id($transaction_id);
      $result = WxPayApi::orderQuery($input);
      
      Log::DEBUG("query:" . json_encode($result));
      if(array_key_exists("return_code", $result)
          && array_key_exists("result_code", $result)
          && $result["return_code"] == "SUCCESS"
          && $result["result_code"] == "SUCCESS")
      {
        return true;
      }
      return false;
  }
  
  //重写回调处理函数
  public function NotifyProcess($data, &$msg){
      Log::DEBUG("call back:" . json_encode($data));
      $notfiyOutput = array();
      
      if(!array_key_exists("transaction_id", $data)){
          $msg = "输入参数不正确";
          return false;
      }
      //查询订单，判断订单真实性
      if(!$this->Queryorder($data["transaction_id"])){
          $msg = "订单查询失败";
          return false;
      }


      //验证通过，更新订单状态
      $orderid = $data["out_trade_no"];
      order_paid($orderid, $data["transaction_id"]);
      
      return true;
  }
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);