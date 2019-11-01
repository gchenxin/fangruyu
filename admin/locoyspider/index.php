<?php
//接口API测试

//系统核心配置文件
require_once('../../include/common.inc.php');

//引入配置文件
if($service != "system" && $service != "member"){
	require_once(HUONIAOINC."/config/".$service.".inc.php");
}

$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);

//声明以下均为接口类
$handler = true;

//拼接请求参数
$param = array();
foreach ($_GET as $key => $value) {
	if($key != "service" && $key != "action" && $key != "callback" && $key != "_"){
		$param[$key] = $value;
	}
}
foreach ($_POST as $key => $value) {
	if($key != "service" && $key != "action" && $key != "callback" && $key != "_"){
		$param[$key] = $value;
	}
}

//获取接口数据
$handels = new handlers($service, $action);
$return = $handels->getHandle($param);

if($pageInfo == 1 && $return['state'] == 100){
	$return = $return['info']['pageInfo'];
}


echo "$";
printInfo($return['info'], "|--");
echo "$";

function printInfo($arr, $pre){
    foreach ($arr as $key => $value) {
        echo "[id=".$value['id']."][typename=".$pre.$value['typename']."]"."<br />\r\n";
        if(!empty($value['lower'])){
            printInfo($value['lower'], "&nbsp;&nbsp;".$pre);
        }
    }
}
