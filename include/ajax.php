<?php
//接口API测试

//系统核心配置文件
require_once('../include/common.inc.php');

if(empty($service)) return;

//引入配置文件
if($service != "system" && $service != "member"){
	require_once(HUONIAOINC."/config/".$service.".inc.php");
}

if(GetCookie('siteCityInfo')){
	$siteCityInfo = checkSiteCity();
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

// foreach ($_REQUEST as $key => $value) {
// 	if($key != "service" && $key != "action" && $key != "callback" && $key != "_"){
// 		$param[$key] = $value;
// 	}
// }

//获取服务器时间
if($action == "getSysTime"){

	$now      = GetMkTime(time());
	$today    = GetMkTime(date("Y-m-d"));
	$nextHour = GetMkTime(date("Y-m-d H", $now + 3600).":00:00");
	$nextDay  = GetMkTime(date("Y-m-d", strtotime("+1 day")));
	$return = array(
		"now"      => $now,
		"today"    => $today,
		"nextHour" => $nextHour,
		"nextDay"  => $nextDay
	);


//获取登录用户信息
}elseif($action == "getMemberID"){

	die($userLogin->getMemberID());


//微信登录
}elseif($action == "checkWxlogin"){

	if($state){

		//查询临时表
		$sql = $dsql->SetQuery("SELECT `uid`, `sameConn` FROM `#@__site_wxlogin` WHERE `state` = '$state'");
		$ret = $dsql->dsqlOper($sql, "results");

		//查询登录用户信息
		if($ret){
			$uid = $ret[0]['uid'];
			$sameConn = $ret[0]['sameConn'];
			// 此微信号已被绑定到其他用户
			if($sameConn){
				$RenrenCrypt = new RenrenCrypt();
				$sameConn = base64_encode($RenrenCrypt->php_encrypt($sameConn));

				if($callback){
					echo $callback."(".json_encode(array("state" => 102, "info" => "此微信号已被绑定到其他用户", "sameConn" => $sameConn)).")";
				}else{
					echo json_encode(array("state" => 102, "info" => "此微信号已被绑定到其他用户", "uid" => $sameConnUid));
				}
				die;
			}

			if($uid){
				$sql = $dsql->SetQuery("SELECT `id`, `username`, `password` FROM `#@__member` WHERE `state` = 1 AND `id` = $uid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){

					$data = $uid . "&" . $ret[0]['password'];

					//登录
					global $cfg_cookiePath;
					global $cfg_onlinetime;
					$RenrenCrypt = new RenrenCrypt();
					$userid = base64_encode($RenrenCrypt->php_encrypt($data));
					PutCookie("login_user", $userid, $cfg_onlinetime * 60 * 60, $cfg_cookiePath);

					//论坛同步
					global $cfg_bbsState;
					global $cfg_bbsType;
					if($cfg_bbsState == 1 && $cfg_bbsType != ""){

						$username = $ret[0]['username'];
						$password = substr($state, 0, 20);

						$data = array();
						$data['username'] = $username;
						$data['uPwd']     = $password;
						$userLogin->bbsSync($data, "synlogin");
					}

					$sql = $dsql->SetQuery("DELETE FROM `#@__site_wxlogin` WHERE `state` = '$state'");
					$dsql->dsqlOper($sql, "update");

					//登录成功
					if($callback){
						echo $callback."(".json_encode(array("state" => 100, "info" => $langData['siteConfig'][21][0])).")";
					}else{
						echo json_encode(array("state" => 100, "info" => $langData['siteConfig'][21][0]));
					}

				}else{
					//会员状态验证错误，登录失败！
					if($callback){
						echo $callback."(".json_encode(array("state" => 101, "info" => $langData['siteConfig'][21][1])).")";
					}else{
						echo json_encode(array("state" => 101, "info" => $langData['siteConfig'][21][1]));
					}
				}
			}else{
				//等待扫描
				if($callback){
					echo $callback."(".json_encode(array("state" => 101, "info" => $langData['siteConfig'][21][2])).")";
				}else{
					echo json_encode(array("state" => 101, "info" => $langData['siteConfig'][21][2]));
				}
			}
		}else{
			//登录失败，请重试！
			if($callback){
				echo $callback."(".json_encode(array("state" => 101, "info" => $langData['siteConfig'][21][3])).")";
			}else{
				echo json_encode(array("state" => 101, "info" => $langData['siteConfig'][21][3]));
			}
		}

	}else{
		//请求错误，请重试！
		if($callback){
			echo $callback."(".json_encode(array("state" => 101, "info" => $langData['siteConfig'][21][4])).")";
		}else{
			echo json_encode(array("state" => 101, "info" => $langData['siteConfig'][21][4]));
		}
	}
	die;


//首次加载Geetest极验验证
}elseif($action == "geetest"){

	global $handler;
	$handler = false;
	$GtSdk = new geetestlib($cfg_geetest_id, $cfg_geetest_key);
	$userid = $userLogin->getMemberID();
	$status = $GtSdk->pre_process($userid);
	$_SESSION['gtserver'] = $status;
	$_SESSION['user_id'] = $userid;
	echo $GtSdk->get_response_str();die;


}else{
	// 前台异步请求时方便获取分站子区域
	if(empty($template) && strstr($_SERVER['HTTP_REFERER'], ".php") === false){
		$template = "page";
	}

	if($template == 'complain'){
		$action = 'complain';
	}

	//获取接口数据
	$handels = new handlers($service, $action);
	$return = $handels->getHandle($param);

	if($pageInfo == 1 && $return['state'] == 100){
		$return = $return['info']['pageInfo'];
	}

}

//输出到浏览器
if($callback){
	if(isset($param['dataType'])){
		if($param['dataType'] == 'html'){
			echo $return['info'];
			return;
		}
	}
	echo $callback."(".json_encode($return, JSON_UNESCAPED_UNICODE).")";
}else{
	if(isset($param['dataType'])){
		if($param['dataType'] == 'html'){
			echo $return['info'];
			return;
		}
	}
	echo json_encode($return, JSON_UNESCAPED_UNICODE);
}
