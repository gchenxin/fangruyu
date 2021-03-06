<?php

include_once '../aliyun-php-sdk-core/Config.php';
include 'Push/Request/V20160801/QueryDeviceStatRequest.php';
use \Push\Request\V20160801 as Push;

$accessKeyId = "your accessKeyId";
$accessKeySecret = "your accessKeySecret";
$appKey = "your appKey";

$iClientProfile = DefaultProfile::getProfile("cn-hangzhou", $accessKeyId, $accessKeySecret);
$client = new DefaultAcsClient($iClientProfile);
$request = new Push\QueryDeviceStatRequest();

$request->setAppKey($appKey);
$request->setQueryType("TOTAL");//NEW: 新增设备查询, TOTAL: 留存设备查询
$request->setDeviceType("ALL");//iOS,ANDROID,ALL
//查询近七天的数据
$startTime =  gmdate('Y-m-d\TH:i:s\Z', strtotime('-7 day'));
$endTime = gmdate('Y-m-d\TH:i:s\Z', time());
$request->setStartTime($startTime);
$request->setEndTime($endTime);

$response = $client->getAcsResponse($request);
print_r("\r\n");
print_r($response);

?>
