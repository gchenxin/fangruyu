<?php
//服务器异步通知页面路径

require_once(dirname(__FILE__)."/../../include/common.inc.php");

// 加载支付方式操作函数
loadPlug("payment");
require_once dirname(__FILE__)."/log.php";

//初始化日志
//$logHandler= new CLogFileHandler(date('Y-m-d').'.log');
//$log = Log::Init($logHandler, 15);

$postXml = $GLOBALS["HTTP_RAW_POST_DATA"];	//接受通知参数；
if(!$postXml){
  $postXml = file_get_contents("php://input");
}
if (empty($postXml)){
    echo "FAIL";
    exit;
}

// Log::DEBUG("APP_notify_xml:" . $postXml);

$postObj = simplexml_load_string($postXml, 'SimpleXMLElement', LIBXML_NOCDATA);

if($postObj->return_code == 'FAIL'){
	echo "FAIL";
	exit;
}

$data = array(
	"appid"				=>	$postObj->appid,
	"mch_id"			=>	$postObj->mch_id,
	"nonce_str"			=>	$postObj->nonce_str,
	"result_code"		=>	$postObj->result_code,
	"openid"			=>	$postObj->openid,
	"trade_type"		=>	$postObj->trade_type,
	"bank_type"			=>	$postObj->bank_type,
	"total_fee"			=>	$postObj->total_fee,
	"cash_fee"			=>	$postObj->cash_fee,
	"transaction_id"	=>	$postObj->transaction_id,
	"out_trade_no"		=>	$postObj->out_trade_no,
	"time_end"			=>	$postObj->time_end
);
ksort($param);

/* 检查支付的金额是否相符 */
if (!check_money($postObj->out_trade_no, $postObj->total_fee * 100)){
   //return false;
}

$paramStr = "";
foreach ($data as $key => $val){
	$paramStr .= $key."=".$val."&";
}
$sign = strtoupper(md5($paramStr."key=".$payment['APP_KEY']));

//Log::DEBUG("APP_notify:" . json_encode($postObj) . "\r\n");
//Log::DEBUG("APP_notify_sign:" . $sign . "\r\n");
//Log::DEBUG("APP_notify_sign1:" . $postObj->sign . "\r\n");

if($sign = $postObj->sign) {

	//更新订单状态
	order_paid($postObj->out_trade_no, $postObj->transaction_id);

	$reply = array(
		"return_code"   =>  "SUCCESS",
		"return_msg"    =>  "OK"
	);
	$reply = array2XML($reply);
	echo $reply;

	exit;

}else{
	echo "FAIL";
	exit;
}




function array2XML($data){
	$xmlString = "";
	if (is_array($data) && !empty($data)) {
		$xmlString .= "<xml>";
		foreach ($data as $tag => $node)
		{
			$xmlString .= "<".$tag."><![CDATA[".$node."]]></".$tag.">";
		}
		$xmlString .= "</xml>";
	}
	return $xmlString;
}
