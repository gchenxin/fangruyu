<?php

/**
 * huoniaoTag模板标签函数插件-会员中心
 *
 * @param $params array 参数集
 * @return arra
 */

function member($params, $content = "", &$smarty = array(), &$repeat = array()){
	$service = "member";
	extract ($params);
	if(empty($action)) return '';

	global $huoniaoTag;
	global $dsql;
	global $userLogin;
	global $cfg_secureAccess;
	global $cfg_basehost;
	global $template;
	global $cfg_errLoginCount;
	global $cfg_loginLock;
	global $langData;

	//输出明天和一周后的时间
	$huoniaoTag->assign('tomorrowDate', AddDay(time(), 1));
	$huoniaoTag->assign('addWeekDate', AddDay(time(), 7));

	$loginCount = $cfg_errLoginCount;   //登录错误次数限制
	$loginTimes = $cfg_loginLock;       //登录错误次数太多后需要等待的时间（单位：分钟）


    $userinfo = $userLogin->getMemberInfo();
    $userid = $userLogin->getMemberID();

    if($userinfo['userType'] == 2) {
        $sql = $dsql->SetQuery("SELECT `id`, `type`, `bind_module`, `expired` FROM `#@__business_list` WHERE `uid` = " . $userid);
        $res = $dsql->dsqlOper($sql, "results");
        if ($res) {
        	$website = $res[0]['id'];
            $bind_module = $res[0]['bind_module'] ? explode(',', $res[0]['bind_module']) : array();
            if($action != 'join_renew' && $res[0]['expired'] && $res[0]['expired'] < time()){

                //判断访问类型
                global $tpl;
                if(strstr($tpl, "company")){
                    $param = array(
                        'service' => 'member',
                        'template' => 'join_renew'
                    );
                    $jump = getUrlPath($param);

                    header("location:" . getUrlPath($param));
                    die;
                }
            }
        } else {
        	// 更新会员类型为个人
        	$sql = $dsql->SetQuery("UPDATE `#@__member` SET `mtype` = 1 WHERE `id` = " . $userid);
        	$dsql->dsqlOper($sql, "update");

            $param = array(
                "service" => "member",
                "type" => "user",
            );
            header("location:" . getUrlPath($param));
            die;
        }

        $showModule = checkShowModule($bind_module, 'manage');
        $huoniaoTag->assign('showModuleConfig', $showModule);

        $totalComment = 0;
        // foreach ($showModule as $key => $value) {
        // 	$type = "";
        // 	$sql = "";
        // 	if(isset($value['sid']) && $value['sid']){
        // 		$sid = $value['sid'];
	       //  	if($key == "shop" || $key == "tuan" || $key == "waimai"){
	       //  		$type == $key."_store";
		    		// $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `type` = '$type' AND `aid` = $sid AND DATE_FORMAT(FROM_UNIXTIME(`dtime`), '%Y-%m-%d') = curdate()");
	       //  	}
	       //  }
	       //  if($sql){
	       //  	$totalComment += $dsql->dsqlOper($sql, "totalCount");
	       //  }
        // }
        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `type` = 'business' AND `aid` = $website AND DATE_FORMAT(FROM_UNIXTIME(`dtime`), '%Y-%m-%d') = curdate()");
        $totalComment += $dsql->dsqlOper($sql, "totalCount");

        $huoniaoTag->assign('totalComment', $totalComment);

    }



	// 移动端新版登陆注册页
	if(strpos($action, "login_touch_popup") !== false || $action == "connect"){
		global $cfg_seccodestatus;
		global $cfg_regstatus;
		global $cfg_regclosemessage;
		global $cfg_seccodetype;
		global $cfg_regtype;
		global $cfg_regfields;
		if($cfg_regstatus == 0){
			// die($cfg_regclosemessage);
		}

        if(strpos($action, "login_touch_popup") !== false){
            $outer = GetCookie("outer");
            if($outer){
                $file = HUONIAOROOT."/api/private/".$outer."/".$outer.".class.php";
                if(is_file($file)){
                    $outerObj = new $outer();
                    if(method_exists($outerObj, "listenLogin")){
                        $outerObj->listenLogin($url, true);
                    }
                }
            }
        }

		$huoniaoTag->assign('cfg_regstatus', $cfg_regstatus);
		$huoniaoTag->assign('cfg_regclosemessage', $cfg_regclosemessage);

		$seccodestatus = explode(",", $cfg_seccodestatus);
		$regCode = "";
		if(in_array("reg", $seccodestatus)){
			$regCode = 1;
		}
		$huoniaoTag->assign('regCode', $regCode);

		$huoniaoTag->assign('cfg_seccodetype', $cfg_seccodetype);
		$regtypeArr = explode(",",$cfg_regtype);
		 //用来判断表单是否显示
		 if(!empty($regtypeArr)){
		 	$type = $regtypeArr[0]==1 ? 1 : ($regtypeArr[0] == 2 ? 3 : 2);
			$huoniaoTag->assign('regable', $type);
		 }
		 $huoniaoTag->assign('regtypeArr', $regtypeArr);
		 //会员注册字段
		 $fieldsArr = explode(",",$cfg_regfields);
		 $huoniaoTag->assign('fieldsArr', $fieldsArr);



		global $cfg_secqaastatus;
		$secqaastatus = explode(",", $cfg_secqaastatus);
		$regQa = "";
		if(in_array("reg", $secqaastatus)){
			$regQa = 1;
		}
		$huoniaoTag->assign('regQa', $regQa);

		//随机选择一条问题
		$archives = $dsql->SetQuery("SELECT * FROM `#@__safeqa` ORDER BY RAND() LIMIT 1");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$huoniaoTag->assign('question', $results[0]['id']);
			$huoniaoTag->assign('regQuestion', $results[0]['question']);
		}


        //支付宝APP登录参数
        $alipay_app_login = array();

        $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $is_app = preg_match("/huoniao/", $useragent) ? 1 : 0;
        if($is_app){
            $sql = $dsql->SetQuery("SELECT `code`, `config` FROM `#@__site_loginconnect` WHERE `state` = 1");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                foreach ($ret as $key => $value) {
                    if($value['code'] == 'alipay'){
                        $config = unserialize($value['config']);

                        $configArr = array();
                        foreach ($config as $k => $v) {
                            $configArr[$v['name']] = $v['value'];
                        }

                        if($configArr['appPrivate']){
                            $rsaPrivateKey = $configArr['appPrivate'];

                            //公共参数
                            $alipay_app_login['apiname'] = 'com.alipay.account.auth';
                            $alipay_app_login['method'] = 'alipay.open.auth.sdk.code.get';
                            $alipay_app_login['app_id'] = $configArr['appid'];
                            $alipay_app_login['app_name'] = 'mc';
                            $alipay_app_login['biz_type'] = 'openservice';
                            $alipay_app_login['pid'] = $configArr['partner'];
                            $alipay_app_login['product_id'] = 'APP_FAST_LOGIN';
                            $alipay_app_login['scope'] = 'kuaijie';
                            $alipay_app_login['target_id'] = create_ordernum();
                            $alipay_app_login['auth_type'] = 'AUTHACCOUNT';   //AUTHACCOUNT代表授权；LOGIN代表登录
                            $alipay_app_login['sign_type'] = 'RSA2';
                            ksort($alipay_app_login);

                            $paramStr = "";
                            $paramStr_ = "";
                            foreach ($alipay_app_login as $key => $val){
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

                            $alipay_app_login['sign'] = $sign;

                        }

                    }
                }
            }
        }
        $huoniaoTag->assign('alipay_app_login', json_encode($alipay_app_login));

		return;

	// 移动端信息发布及入驻商家页面
	}elseif($action == "enter" || strpos($action, "fabuJoin_touch_popup") !== false || $action == "join_renew" || $action == "join_upgrade"){

		$userid = $userLogin->getMemberID();
		if($userid == -1){

		    global $cfg_staticPath;
		    global $cfg_staticVersion;
            $url = $cfg_secureAccess . $cfg_basehost;

            if(isApp()) {
                $html = <<<eot
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0">
	<title></title>
	<link rel="stylesheet" type="text/css" href="{$cfg_staticPath}css/core/touchBase.css?v=$cfg_staticVersion">
	<script src="{$cfg_staticPath}js/core/touchScale.js?v=$cfg_staticVersion"></script>
	<script src="{$cfg_staticPath}js/core/zepto.min.js?v=$cfg_staticVersion"></script>
	<style>
	    html, body {height: 100%; background: rgba(0, 0, 0, .2);}
	    .popup {position: fixed; width: 4.7rem; left: 50%; top: 50%; margin: -2.75rem 0 0 -2.35rem; padding: .5rem 0; background: #fff; border-radius: 4px; text-align: center;}
	    .popup h3 {font-size: .3rem;}
	    .popup img {width: 1rem; height: 1rem; display: block; margin: .3rem auto;}
	    .popup a {width: 2.45rem; height: .58rem; display: block; margin: 0 auto; line-height: .58rem; color: #fff; font-size: .26rem; border-bottom: 5px solid #fcb0a4; border-radius: 4px; background-color: rgb(248, 63, 33);}
    </style>
	<script>
	    window.open("{$url}/login.html");
    </script>
</head>
<body>
    <div class="popup">
        <h3>您还未登录，请先登录</h3>
        <img src="{$cfg_staticPath}images/login_tip_icon.png" />
        <a href="{$url}/login.html">立即登录</a>
    </div>
</body>
</html>
eot;
                echo $html;
//                header("location:" . $url . '/login.html');
                die;
            }else {
                header("location:" . $url . '/login.html');
                die;
            }
		}

		// 判断是否已入驻
		$sql = $dsql->SetQuery("SELECT * FROM `#@__business_list` WHERE `uid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			if($ret[0]['state'] != 4){
				$huoniaoTag->assign("has_joinBusiness", 1);

				if($action == "enter"){
					$param = array(
						"service" => "member",
					);
					header("location:".getUrlPath($param));
					die;
				}
			}

			if($action == "join_renew" || $action == "join_upgrade"){
				if($ret[0]['state'] == 4){
					$url = $cfg_secureAccess.$cfg_basehost;
					header("location:".$url);
					die;
				}
				if($action == "join_upgrade" && $ret[0]['type'] == 2){
					$param = array(
						"service" => "member",
						"template" => "join_renew",
					);
					$url = getUrlPath($param);
					header("location:".$url);
					die;
				}
			}

			global $data;
			$data = "";
			$typeArr = getParentArr("business_type", $ret[0]['typeid']);
			$typeArr = array_reverse(parent_foreach($typeArr, "typename"));
			$ret[0]['typeArr'] = join(" ", $typeArr);
			$huoniaoTag->assign("detail", $ret[0]);


		}else{
			if($action == "join_renew" || $action == "join_upgrade"){
				$param = array(
					"service" => "member",
					"type" => "user",
					"template" => "enter",
				);
				header("location:".getUrlPath($param)."#join");
				die;
			}
		}

		$busiHandlers = new handlers("business", "config");
		$busiConfig = $busiHandlers->getHandle();

		$busiConfig = is_array($busiConfig) ? $busiConfig['info'] : array();

		$allModuleArr = array();
		$sql = $dsql->SetQuery("SELECT `name`, `title`, `subject` FROM `#@__site_module` WHERE `state` = 0 AND `type` = 0 AND `parentid` != 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			foreach ($ret as $key => $value) {
		    	$allModuleArr[$value['name']] = $value['subject'] ? $value['subject'] : $value['title'];
		  	}
		}
		$allStoreArr = array(
			"info" => $langData['siteConfig'][34][41],//二手店铺
			"shop" => $langData['siteConfig'][34][12],//商城店铺
			"tuan" => $langData['siteConfig'][34][13],//团购店铺
			"waimai" => $langData['siteConfig'][34][14],//外卖店铺
			"house" => $langData['siteConfig'][34][26],//房产中介
			"job" => $langData['siteConfig'][34][15],//招聘企业
			"dating" => $langData['siteConfig'][34][16],//婚恋门店
			"renovation" => $langData['siteConfig'][34][27],//装修公司
			"huangye" => $langData['siteConfig'][34][17],//黄页店铺
			"huodong" => $langData['siteConfig'][34][18],//活动店铺
			"car" => $langData['siteConfig'][34][19],//汽车店铺
			"homemaking" => $langData['homemaking'][10][29],//家政公司
			"marry" => $langData['marry'][5][0],//婚假公司
			"travel" => $langData['travel'][12][12],//旅游公司
			"education" => $langData['education'][7][4],//教育公司
			"pension" => $langData['pension'][0][0],//养老机构

		);
		$autyTitle = array(
			"basic" => $langData['siteConfig'][34][20],//店铺基础信息
			"thumb" => $langData['siteConfig'][34][21],//相册/视频展示
			"custom" => $langData['siteConfig'][34][22],//自定义单页/背景音乐
			"newMedia" => $langData['siteConfig'][34][23],//720全景/互动直播
			"website" => $langData['siteConfig'][34][24],//企业官网
			"miniprogram" => $langData['siteConfig'][34][25],//店铺独立小程序
		);

		$joinAuth = $busiConfig['joinAuth'];
		foreach ($joinAuth as $key => $value) {
			if($key == "module" || $key == "store"){
				foreach ($value as $k => $v) {
					if($key == "module"){
						$title = $allModuleArr[$k];
					}else{
						$title = $allStoreArr[$k];
					}
					$joinAuth[$key][$k]['title'] = $title;
					$joinAuth[$key][$k]['type1'] = !isset($v['perm']) || array_search("1", $v['perm']) === false ? 0 : 1;
					$joinAuth[$key][$k]['type2'] = !isset($v['perm']) || array_search("2", $v['perm']) === false ? 0 : 1;
				}
			}else{
				$title = $autyTitle[$key];
				$joinAuth[$key]['title'] = $title;
				$joinAuth[$key]['type1'] = !isset($value['perm']) || array_search("1", $value['perm']) === false ? 0 : 1;
				$joinAuth[$key]['type2'] = !isset($value['perm']) || array_search("2", $value['perm']) === false ? 0 : 1;
			}
		}
		$busiConfig['joinAuth'] = $joinAuth;
		$huoniaoTag->assign('busiConfig', $busiConfig);


	//登录页面
	}elseif($action == "login" || $action == "login_popup"){

		$url = $furl ? urldecode($furl) : $_SERVER['HTTP_REFERER'];
		if(strstr($url, "logout.html") || strstr($url, "login.html") || empty($url)){
			$url = $cfg_secureAccess.$cfg_basehost;
		}

		if(strstr($url, "security.html")){
			$param = array("service" => "member",	"type" => "user");
			$url = getUrlPath($param);     //个人会员域名
		}

		//检验用户登录状态
		if($userLogin->getMemberID() > -1){

			$urlArr = parse_url($url);
			$host = $urlArr['host'];

			if($_SERVER['HTTP_HOST'] != $host){
				//header('location:'.$cfg_secureAccess.$host.'/index.php?service=member&template=ssoUserRedirect&site='.$host.'&furl='.$url);
				//die;
			}

			if($action == "login"){
				header('location:'.$url);
			}

			$huoniaoTag->assign('isLogin', 1);
		}

		global $cfg_seccodestatus;
		$seccodestatus = explode(",", $cfg_seccodestatus);
		$loginCode = "";
		if(in_array("login", $seccodestatus)){
			$loginCode = 1;
		}
		$huoniaoTag->assign('loginCode', $loginCode);
		$huoniaoTag->assign("redirectUrl", htmlspecialchars($url));
		$huoniaoTag->assign('site', htmlspecialchars($site));

		$_SESSION['loginRedirect'] = $url;

        $outer = GetCookie("outer");
        if($outer){
            $file = HUONIAOROOT."/api/private/".$outer."/".$outer.".class.php";
            if(is_file($file)){
                $outerObj = new $outer();
                if(method_exists($outerObj, "listenLogin")){
                    $outerObj->listenLogin($url);
                }
            }
        }

		//支付宝APP登录参数
		$alipay_app_login = array();

		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$is_app = preg_match("/huoniao/", $useragent) ? 1 : 0;
		if($is_app){
			$sql = $dsql->SetQuery("SELECT `code`, `config` FROM `#@__site_loginconnect` WHERE `state` = 1");
	    $ret = $dsql->dsqlOper($sql, "results");
	    if($ret){
	      foreach ($ret as $key => $value) {
					if($value['code'] == 'alipay'){
		        $config = unserialize($value['config']);

		        $configArr = array();
		        foreach ($config as $k => $v) {
		          $configArr[$v['name']] = $v['value'];
		        }

						if($configArr['appPrivate']){
							$rsaPrivateKey = $configArr['appPrivate'];

							//公共参数
							$alipay_app_login['apiname'] = 'com.alipay.account.auth';
							$alipay_app_login['method'] = 'alipay.open.auth.sdk.code.get';
							$alipay_app_login['app_id'] = $configArr['appid'];
							$alipay_app_login['app_name'] = 'mc';
							$alipay_app_login['biz_type'] = 'openservice';
							$alipay_app_login['pid'] = $configArr['partner'];
							$alipay_app_login['product_id'] = 'APP_FAST_LOGIN';
							$alipay_app_login['scope'] = 'kuaijie';
							$alipay_app_login['target_id'] = create_ordernum();
							$alipay_app_login['auth_type'] = 'AUTHACCOUNT';   //AUTHACCOUNT代表授权；LOGIN代表登录
							$alipay_app_login['sign_type'] = 'RSA2';
							ksort($alipay_app_login);

							$paramStr = "";
							$paramStr_ = "";
							foreach ($alipay_app_login as $key => $val){
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

							$alipay_app_login['sign'] = $sign;

						}

					}
				}
			}
		}
		$huoniaoTag->assign('alipay_app_login', json_encode($alipay_app_login));



		return;


	//单点登录页面
	}elseif($action == "sso"){

		//单点登录、退出
		if($do == "sso"){

			$userinfo = array();
			$userid = $_GET['userid'];
			if($userid){
				$RenrenCrypt = new RenrenCrypt();
				$uid = $RenrenCrypt->php_decrypt(base64_decode($userid));
                $member = new member();
				$uinfo = $member->detail($uid, true);

				$userinfo['uid']      = $userid;
				$userinfo['userid']   = $uinfo['userid'];
				$userinfo['userType'] = $uinfo['userType'];
				$userinfo['username'] = $uinfo['username'];
				$userinfo['nickname'] = $uinfo['nickname'];
				$userinfo['photo']    = $uinfo['photo'];
				$userinfo['message']  = $uinfo['message'];
				$userinfo['userid_encode']  = $uinfo['userid_encode'];

				//根据会员类型不同，返回不同的域名
				global $userDomain;
				global $busiDomain;
				$domain = $userDomain;
				if($uinfo['userType'] == 2){
					$domain = $busiDomain;
				}
				$userinfo['userDomain'] = $domain;

				$userinfo = json_encode($userinfo);
			}
			$huoniaoTag->assign('do', $do);
			$huoniaoTag->assign('userArr', $userinfo ? $userinfo : '');

		}else{

			//获取主站用户信息
			$userid = "";
			$mid = $userLogin->getMemberID();
			if($mid > -1){
				$RenrenCrypt = new RenrenCrypt();
				$userid = base64_encode($RenrenCrypt->php_encrypt($mid));
			}

			$huoniaoTag->assign('site', $site);
			$huoniaoTag->assign('userid', $userid);

		}
		return;

	}elseif($action == "ssoUserRedirect"){
		$huoniaoTag->assign('site', $site);
		$huoniaoTag->assign('furl', $furl);


	//单点登录页面，会员绑定独立域名专用
	}elseif($action == "ssoUser"){

		//单点登录、退出
		if($do == "sso"){

			$userinfo = "";
            $userid = $_GET['userid'];
			if($userid){
				$RenrenCrypt = new RenrenCrypt();
				$uid = $RenrenCrypt->php_decrypt(base64_decode($userid));
                $member = new member();
                $uinfo = $member->detail($uid, true);

//				$userinfo['uid']      = $userid;
				$userinfo['uid']      = $uinfo['userid_encode'];
				$userinfo['userid']   = $uinfo['userid'];
				$userinfo['userType'] = $uinfo['userType'];
				$userinfo['username'] = $uinfo['username'];
				$userinfo['nickname'] = $uinfo['nickname'];
				$userinfo['photo']    = $uinfo['photo'];
				$userinfo['message']  = $uinfo['message'];

				//根据会员类型不同，返回不同的域名
				global $userDomain;
				global $busiDomain;
				$domain = $userDomain;
				if($uinfo['userType'] == 2){
					$domain = $busiDomain;
				}
				$userinfo['userDomain'] = $domain;

				$userinfo = json_encode($userinfo);
			}
			$huoniaoTag->assign('do', $do);
			$huoniaoTag->assign('userArr', $userinfo);

		}else{

			//获取主站用户信息
			$userid = "";
			$mid = $userLogin->getMemberID();
			if($mid > -1){
				$RenrenCrypt = new RenrenCrypt();
				$userid = base64_encode($RenrenCrypt->php_encrypt($mid));
			}

			$huoniaoTag->assign('site', $site);
			$huoniaoTag->assign('userid', $userid);

		}
		return;


	//判断登录
	}elseif($action == "loginCheck"){

		//判断是否提交
		if(empty($_REQUEST)) {
			header('location:'.$cfg_secureAccess.$cfg_basehost);
			die();
		}

		//检验用户登录状态
		if($userLogin->getMemberID() > -1){

			echo '<span style="display:none;">1001</span>';
			die;

		}else{

			//判断验证码
			global $cfg_seccodestatus;
			$seccodestatus = explode(",", $cfg_seccodestatus);
			if(in_array("login", $seccodestatus)){
				if(strtolower($vericode) != $_SESSION['huoniao_vdimg_value']){
					echo "202|" . $langData['siteConfig'][21][222];  //验证码输入错误，请重试！
					die;
				}
			}

			$ip = GetIP();
			$ipaddr = getIpAddr($ip);
			$archives = $dsql->SetQuery("SELECT * FROM `#@__failedlogin` WHERE `ip` = '$ip'");
			$results = $dsql->dsqlOper($archives, "results");

			//登录前验证
			if($results){
				$count = $results[0]['count'];
				$timedifference = GetMkTime(time()) - $results[0]['date'];
				if($timedifference/60 < $loginTimes && $count >= $loginCount && $loginCount > 0 && $loginTimes > 0){
					echo '201|' . str_replace('1', ceil($loginTimes-$timedifference/60), $langData['siteConfig'][21][223]);  //您错误的次数太多，请1分钟后重试！
					die;
				}
			}
			$res = $userLogin->memberLogin($username, $password);
			//success
			if($res == 1){
				$userid = $userLogin->getMemberID();

				//记录当前设备s
				$sql = $dsql->SetQuery("SELECT `sourceclient` FROM `#@__member`  WHERE `id` = '$userid'");
				$client = $dsql->dsqlOper($sql, "results");
				if($client[0]['sourceclient']){
					$sourceclientAll = unserialize($client[0]['sourceclient']);
				}

				$sourceArr = array(
					"title" => $deviceTitle,
					"type"  => $deviceType,
					"serial" => $deviceSerial,
					"pudate" => time()
				);

				$sourceclients = array();
				if(!empty($deviceTitle) && !empty($deviceSerial) && !empty($deviceType)){
					if(!empty($sourceclientAll)){
						$sourceclients = $sourceclientAll;
						//$foundTitle  = array_search($deviceTitle, array_column($sourceclientAll, 'title'));
						$foundSerial = array_search($deviceSerial, array_column($sourceclientAll, 'serial'));
						//$foundType   = array_search($deviceType, array_column($sourceclientAll, 'type'));
						if($foundSerial){
							//如果已有，更新时间，以Serial为准
							$sourceclients[$foundSerial]['pudate'] = time();
						}else{
							array_push($sourceclients, $sourceArr);
						}
					}else{
						$sourceclients[] = $sourceArr;
					}
					$sourceclients = serialize($sourceclients);

					$where_ = "`sourceclient` = '$sourceclients',";
				}
				//记录当前设备e

				$archives = $dsql->SetQuery("UPDATE `#@__member` SET  $where_ `logincount` = `logincount` + 1, `lastlogintime` = ".GetMkTime(time()).", `lastloginip` = '".$ip."', `lastloginipaddr` = '".$ipaddr."' WHERE `id` = ".$userid);
				$dsql->dsqlOper($archives, "update");

				//保存到主表
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_login` (`userid`, `logintime`, `loginip`, `ipaddr`) VALUES ('$userid', '".GetMkTime(time())."', '$ip', '$ipaddr')");
				$dsql->dsqlOper($archives, "update");

				$userinfoArr = array();
				$userinfo = $userLogin->getMemberInfo();
				foreach ($userinfo as $key => $value) {
					array_push($userinfoArr, '"'.$key.'": "'.$value.'"');
				}
				$userinfoStr = '<script>var userinfo = {'.join(', ', $userinfoArr).'};window.sessionStorage.setItem("userid",true)</script>';
				echo '<span style="display:none;">'.$userinfoStr.'100</span>';
				die;

			//error
			}else if($res == -1 || $res == -2){

				//如果有记录则错误次数加1
				if($results){
					//计算最后一次错误是否是在$loginTimes分钟之前，如果是则重置错误次数
					if($timedifference/60 > $loginTimes){
						$count = 1;
					}else{
						$count = $results[0]['count'] >= $loginCount ? 0 : $results[0]['count'];
						$count++;
					}
					$archives = $dsql->SetQuery("UPDATE `#@__failedlogin` SET `count` = ".$count.", `date` = ".GetMkTime(time())." WHERE `ip` = '".$ip."'");
					$results = $dsql->dsqlOper($archives, "update");

				//没有记录则新增一条
				}else{
					$count = 1;
					$archives = $dsql->SetQuery("INSERT INTO `#@__failedlogin` (`ip`, `count`, `date`) VALUES ('$ip', $count, ".GetMkTime(time()).")");
					$results = $dsql->dsqlOper($archives, "update");
				}

				echo '201|' . $langData['siteConfig'][21][224];  //用户名或密码错误，请重试！
				die;

			}
		}
		return;

	//退出登录
	}elseif($action == "logout"){

		$userLogin->exitMember();
		$url = $_SERVER['HTTP_REFERER'];
		if(strstr($url, "logout.html") || strstr($url, "fpwd.html") || strstr($url, "register.html") || empty($url)){
			$url = $cfg_secureAccess.$cfg_basehost;
		}

		//判断是否开启论坛同步，如果开启则显示退出过程，如果没有开启，程序自动跳走
		global $cfg_bbsState;
		global $cfg_bbsType;
		if($cfg_bbsState == 1 && $cfg_bbsType != ""){
			$huoniaoTag->assign("redirectUrl", $url);
		}else{
            PutCookie("logout_time", time(), 60);
			header('location:'.$url);
			die;
		}
		return;

	//注册页面
	}elseif($action == "register"){
		//检验用户登录状态
		if($userLogin->getMemberID() > -1){
			global $cfg_basehost;
			$url = $cfg_secureAccess.$cfg_basehost;
			header('location:'.$url);
			die;
		}

		global $cfg_seccodestatus;
		global $cfg_regstatus;
		global $cfg_regclosemessage;
		global $cfg_seccodetype;
		global $cfg_regtype;
		global $cfg_regfields;
		if($cfg_regstatus == 0){
			die($cfg_regclosemessage);
		}

		$seccodestatus = explode(",", $cfg_seccodestatus);
		$regCode = "";
		if(in_array("reg", $seccodestatus)){
			$regCode = 1;
		}
		$huoniaoTag->assign('regCode', $regCode);

		$huoniaoTag->assign('cfg_seccodetype', $cfg_seccodetype);
		$regtypeArr = explode(",",$cfg_regtype);
		 //用来判断表单是否显示
		 if(!empty($regtypeArr)){
		 	$type = $regtypeArr[0]==1 ? 1 : ($regtypeArr[0] == 2 ? 3 : 2);
			$huoniaoTag->assign('regable', $type);
		 }
		 $huoniaoTag->assign('regtypeArr', $regtypeArr);
		 //会员注册字段
		 $fieldsArr = explode(",",$cfg_regfields);
		 $huoniaoTag->assign('fieldsArr', $fieldsArr);



		global $cfg_secqaastatus;
		$secqaastatus = explode(",", $cfg_secqaastatus);
		$regQa = "";
		if(in_array("reg", $secqaastatus)){
			$regQa = 1;
		}
		$huoniaoTag->assign('regQa', $regQa);

		//随机选择一条问题
		$archives = $dsql->SetQuery("SELECT * FROM `#@__safeqa` ORDER BY RAND() LIMIT 1");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$huoniaoTag->assign('question', $results[0]['id']);
			$huoniaoTag->assign('regQuestion', $results[0]['question']);
		}

		return;

	//找回密码
	}elseif($action == "fpwd"){

		$type = empty($type) ? "phone" : $type;
		$type = $type != "phone" && $type != "email" ? "phone" : $type;
		$huoniaoTag->assign("fptype", $type);

	//判断注册
	}elseif($action == "registerCheck"){

		global $cfg_regstatus;
		global $cfg_regclosemessage;
		if($cfg_regstatus == 0){
			die('200|'.$cfg_regclosemessage);
		}

		//验证用户名
		if(empty($username)){
			die('201|' . $langData['siteConfig'][21][225]);  //请输入用户名！
		}
		preg_match("/^[a-zA-Z]{1}[0-9a-zA-Z_]{4,15}$/iu", $username, $matchUsername);
		if(!$matchUsername){
			die('201|' . $langData['siteConfig'][21][226]);  //用户名格式有误！<br />英文字母、数字、下划线以内的5-20个字！<br />并且只能以字母开头！
		}
		if(!checkMember($username)){
			die('201|' . $langData['siteConfig'][21][227]); //用户名已存在！
		}

		//验证密码
		if(empty($password)){
			die('202|' . $langData['siteConfig'][20][164]);  //请输入密码
		}
		preg_match('/^.{5,}$/', $password, $matchPassword);
		if(!$matchPassword){
			die('202|' . $langData['siteConfig'][21][103]);  //密码长度最少为5位！
		}

		//真实姓名
		if(empty($nickname)){
			die('203|' . $langData['siteConfig'][20][248]);  //请输入真实姓名
		}
		preg_match('/^[a-z\/ ]{2,20}$/iu', $nickname, $matchNickname);
		preg_match('/^[\x{4e00}-\x{9fa5} ]{2,20}$/iu', $nickname, $matchNickname1);
		if(!$matchNickname && !$matchNickname1){
			die('203|' . $langData['siteConfig'][21][228]);  //真实姓名格式有误！<br />中文、英文字母、空格、反斜线(/)以内的2-20个字！<br />如：刘德华、刘 德华、Last/Frist Middle
		}

		//邮箱
		if(empty($email)){
			die('204|' . $langData['siteConfig'][21][36]);  //请输入邮箱地址！
		}
		preg_match('/\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/', $email, $matchEmail);
		if(!$matchEmail){
			die('204|' . $langData['siteConfig'][21][229]);  //邮箱格式有误！
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `email` = '$email'");
		$return = $dsql->dsqlOper($archives, "results");
		if($return){
			die('204|' . $langData['siteConfig'][21][230]);  //此邮箱已被注册！
		}

		//手机
		if(empty($phone)){
			die('205|' . $langData['siteConfig'][20][239]);  //请输入手机号
		}
		preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $phone, $matchPhone);
		if(!$matchPhone){
			die('205|' . $langData['siteConfig'][21][98]);  //手机号码格式错误
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `phone` = '$phone'");
		$return = $dsql->dsqlOper($archives, "results");
		if($return){
			die('205|' . $langData['siteConfig'][21][231]); //此手机号已被注册！
		}

		if($mtype == 2){
			if(empty($company)){
				die('206|' . $langData['siteConfig'][21][232]);  //请输入公司名称
			}
		}

		//判断安全问题
		global $cfg_secqaastatus;
		$secqaastatus = explode(",", $cfg_secqaastatus);
		if(in_array("reg", $secqaastatus)){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__safeqa` WHERE `id` = $question AND `answer` = '".$answer."'");
			$results = $dsql->dsqlOper($archives, "results");
			if(!$results){
				die('207|' . $langData['siteConfig'][21][233]);  //安全问题输入错误，请重试！
			}
		}

		//判断验证码
		global $cfg_seccodestatus;
		$seccodestatus = explode(",", $cfg_seccodestatus);
		if(in_array("reg", $seccodestatus)){
			if(strtolower($vericode) != $_SESSION['huoniao_vdimg_value']){
				die('208|' . $langData['siteConfig'][21][222]);  //验证码输入错误，请重试！
			}
		}

		$passwd   = $userLogin->_getSaltedHash($password);
		$regtime  = GetMkTime(time());
		$regip    = GetIP();
		$regipaddr = getIpAddr($regip);

		$archives = $dsql->SetQuery("SELECT `regtime` FROM `#@__member` WHERE `regip` = '$regip' AND `state` = 1 ORDER BY `id` DESC LIMIT 0, 1");
		$return = $dsql->dsqlOper($archives, "results");
		if($return){
			global $cfg_regtime;
			if(round(($regtime - $return[0]['regtime'])/60) < $cfg_regtime){
				die('200|' . str_replace('1', $cfg_regtime, $langData['siteConfig'][21][234]));  //本站限制每次注册间隔时间为1分钟，请稍后再注册。
			}
		}

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `email`, `emailCheck`, `phone`, `phoneCheck`, `company`, `regtime`, `regip`, `regipaddr`, `state`) VALUES ('$mtype', '$username', '$passwd', '$nickname', '$email', '0', '$phone', '0', '$company', '$regtime', '$regip', '$regipaddr', '0')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if($aid){

			//论坛同步
			$data['username'] = $username;
			$data['password'] = $password;
			$data['email']    = $email;
			$userLogin->bbsSync($data, "register");

			//自动登录
			$ureg = $userLogin->memberLogin($username, $password);

			$RenrenCrypt = new RenrenCrypt();
			$userid = base64_encode($RenrenCrypt->php_encrypt($aid));

			//注册验证
			global $cfg_regverify;

			//不验证
			if($cfg_regverify == 0){
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `state` = 1 WHERE `id` = '$aid'");
				$dsql->dsqlOper($archives, "update");

				// die('100|'.$cfg_secureAccess.$cfg_basehost.'/registerSuccess.html');
				die('100|'.$cfg_secureAccess.$cfg_basehost);

			//邮箱验证
			}elseif($cfg_regverify == 1){
				die('100|'.$cfg_secureAccess.$cfg_basehost.'/registerVerifyEmail.html?userid='.$userid);

			//手机验证
			}elseif($cfg_regverify == 2){
				die('100|'.$cfg_secureAccess.$cfg_basehost.'/registerVerifyPhone.html?userid='.$userid);

			}

		}else{
			die('200|' . $langData['siteConfig'][21][235]);  //注册失败，请稍候重试！
		}
		return;


	//判断注册用户名、邮件、手机
	}elseif($action == "registerCheck_v1"){

		$mtype = !empty($mtype) ? $mtype : 1;
		$regtime  = GetMkTime(time());
		$regip    = GetIP();
		$regipaddr = getIpAddr($regip);

		$archives = $dsql->SetQuery("SELECT `regtime` FROM `#@__member` WHERE `regip` = '$regip' AND `state` = 1 ORDER BY `id` DESC LIMIT 0, 1");
		$return = $dsql->dsqlOper($archives, "results");
		if($return){
			global $cfg_regtime;
			if(round(($regtime - $return[0]['regtime'])/60) < $cfg_regtime){
				die('200|' . str_replace('1', $cfg_regtime, $langData['siteConfig'][21][234]));  //本站限制每次注册间隔时间为1分钟，请稍后再注册。
			}
		}

		//验证密码
		if(empty($password)){
			die('202|' . $langData['siteConfig'][20][164]);  //请输入密码
		}
		preg_match('/^.{5,}$/', $password, $matchPassword);
		if(!$matchPassword){
			die('202|' . $langData['siteConfig'][21][103]);  //密码长度最少为5位！
		}

		$passwd    = $userLogin->_getSaltedHash($password);

		//记录当前设备s
		$sourceArr = array(
			"title" => $deviceTitle,
			"type"  => $deviceType,
			"serial" => $deviceSerial,
			"pudate" => time()
		);
		if(!empty($deviceTitle) && !empty($deviceSerial) && !empty($deviceType)){
			$sourceclient[] = $sourceArr;
			$sourceclient   = serialize($sourceclient);
		}
		//记录当前设备e
		//用户名
		if($rtype == 1){
			global $cfg_regstatus;
			global $cfg_regclosemessage;
			if($cfg_regstatus == 0){
				die('200|'.$cfg_regclosemessage);
			}

			//验证用户名
			if(empty($account)){
				die('201|' . $langData['siteConfig'][21][225]);  //请输入用户名！
			}
			preg_match("/^[a-zA-Z]{1}[0-9a-zA-Z_]{4,15}$/iu", $account, $matchUsername);
			if(!$matchUsername){
				die('201|' . $langData['siteConfig'][21][226]);  //用户名格式有误！<br />英文字母、数字、下划线以内的5-20个字！<br />并且只能以字母开头！
			}
			if(!checkMember($account)){
				die('201|' . $langData['siteConfig'][21][227]); //用户名已存在！
			}

			//真实姓名
			if(isset($nickname)){
				if(empty($nickname)){
					die('203|' . $langData['siteConfig'][20][248]);  //请输入真实姓名
				}
				preg_match('/^[a-z\/ ]{2,20}$/iu', $nickname, $matchNickname);
				preg_match('/^[\x{4e00}-\x{9fa5} ]{2,20}$/iu', $nickname, $matchNickname1);
				if(!$matchNickname && !$matchNickname1){
					die('203|' . $langData['siteConfig'][21][228]);  //真实姓名格式有误！<br />中文、英文字母、空格、反斜线(/)以内的2-20个字！<br />如：刘德华、刘 德华、Last/Frist Middle
				}
			}else{
				$nickname = $account;
			}

			//邮箱
			if(isset($email)){
				if(empty($email)){
					die('204|' . $langData['siteConfig'][21][36]);  //请输入邮箱地址！
				}
				preg_match('/\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/', $email, $matchEmail);
				if(!$matchEmail){
					die('204|' . $langData['siteConfig'][21][229]);  //邮箱格式有误！
				}
				$archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `email` = '$email'");
				$return = $dsql->dsqlOper($archives, "results");
				if($return){
					die('204|' . $langData['siteConfig'][21][230]);  //此邮箱已被注册！
				}
			}

			//手机
			if(isset($phone)){
				if(empty($phone)){
					die('205|' . $langData['siteConfig'][20][239]);  //请输入手机号
				}
				preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $phone, $matchPhone);
				if(!$matchPhone){
					die('205|' . $langData['siteConfig'][21][98]);  //手机号码格式错误
				}

				$archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `phone` = '$phone'");
				$return = $dsql->dsqlOper($archives, "results");
				if($return){
					die('205|' . $langData['siteConfig'][21][231]); //此手机号已被注册！
				}
			}

			//判断验证码
			global $cfg_seccodetype;
			if(!empty($cfg_seccodetype)){
				if(strtolower($vericode) != $_SESSION['huoniao_vdimg_value']){
					die('208|' . $langData['siteConfig'][21][222]);  //验证码输入错误，请重试！
				}
			}

			$regtime  = GetMkTime(time());
			$regip    = GetIP();
			$regipaddr = getIpAddr($regip);

			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `email`, `emailCheck`, `phone`, `phoneCheck`, `company`, `regtime`, `regip`, `regipaddr`, `state`, `sourceclient`) VALUES ('$mtype', '$account', '$passwd', '$nickname', '$email', '0', '$phone', '0', '$company', '$regtime', '$regip', '$regipaddr', '1', '$sourceclient')");
			$aid = $dsql->dsqlOper($archives, "lastid");

			if($aid){

                $userLogin->registGiving($aid);

				//论坛同步
				$data['username'] = $account;
				$data['password'] = $password;
				$data['email']    = $email;
				$userLogin->bbsSync($data, "register");

				//自动登录
				$ureg = $userLogin->memberLogin($account, $password);

				$RenrenCrypt = new RenrenCrypt();
				$userid = base64_encode($RenrenCrypt->php_encrypt($aid));

				//注册验证
				global $cfg_regverify;

				//不验证
				if($cfg_regverify == 0){

					$archives = $dsql->SetQuery("UPDATE `#@__member` SET `state` = 1 WHERE `id` = '$aid'");
					$dsql->dsqlOper($archives, "update");

					$return = '100|'.$cfg_secureAccess.$cfg_basehost;
//                    if($liveurl = GetCookie('live_share_url')){
//                        $return = $return . '|' . $liveurl;
//                    }
					// die('100|'.$cfg_secureAccess.$cfg_basehost.'/registerSuccess.html');
                    die($return);

				//邮箱验证
				}elseif($cfg_regverify == 1){
				    $return = '100|'.$cfg_secureAccess.$cfg_basehost.'/registerVerifyEmail.html?userid='.$userid;
//                    if($liveurl = GetCookie('live_share_url')){
//                        $return = $return . '|' . $liveurl;
//                    }
					die($return);

				//手机验证
				}elseif($cfg_regverify == 2){
                    $return = '100|'.$cfg_secureAccess.$cfg_basehost.'/registerVerifyPhone.html?userid='.$userid;
//                    if($liveurl = GetCookie('live_share_url')){
//                        $return = $return . '|' . $liveurl;
//                    }
                    die($return);
				}

			}else{
				die('200|' . $langData['siteConfig'][21][235]);  //注册失败，请稍候重试！
			}
			return;
		}

		//邮箱
		if($rtype == 2){

			if(empty($account)) die('201|' . $langData['siteConfig'][21][36]);  //请输入邮箱地址！
			if(empty($vcode)) die('201|' . $langData['siteConfig'][21][236]);  //请输入邮箱验证码！
			if(empty($password)) die('201|' . $langData['siteConfig'][21][237]);  //请输入登录密码！

			preg_match('/\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/', $account, $matchEmail);
			if(!$matchEmail){
				die('204|' . $langData['siteConfig'][21][229]);  //邮箱格式有误！
			}

			$archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `email` = '$account'");
			$return = $dsql->dsqlOper($archives, "results");
			if($return){
				die('204|' . $langData['siteConfig'][21][230]);  //此邮箱已被注册！
			}


			//验证输入的验证码
			$archives = $dsql->SetQuery("SELECT `id`, `pubdate` FROM `#@__site_messagelog` WHERE `type` = 'email' AND `lei` = 'signup' AND `user` = '$account' AND `code` = '$vcode'");
			$results  = $dsql->dsqlOper($archives, "results");
			if(!$results){
				die('204|' . $langData['siteConfig'][21][222]);  //验证码输入错误，请重试！
			}else{

				//24小时有效期
				if(round(($regtime - $results[0]['pubdate'])/3600) > 24) die('204|' . $langData['siteConfig'][21][33]);  //验证码已过期，请重新获取！

				//验证通过删除发送的验证码
				$archives = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'email' AND `lei` = 'signup' AND `user` = '$account' AND `code` = '$vcode'");
				$dsql->dsqlOper($archives, "update");
			}


			//保存到主表
			$nickname = preg_replace('/([0-9a-zA-Z]{3})(.*?)@(.*?)/is',"$1***@$3", $account);
			$archives = $dsql->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `email`, `emailCheck`, `regtime`, `regip`, `regipaddr`, `state`, `sourceclient`) VALUES ('$mtype', '$account', '$passwd', '$nickname', '$account', '1', '$regtime', '$regip', '$regipaddr', '1', '$sourceclient')");
			$aid = $dsql->dsqlOper($archives, "lastid");

			if($aid){

				$userLogin->registGiving($aid);

				//论坛同步
				$accountData = explode("@", $account);
				$data['username'] = $accountData[0];
				$data['password'] = $password;
				$data['email']    = $account;
				$userLogin->bbsSync($data, "register");

				//自动登录
				$ureg = $userLogin->memberLogin($account, $password);
				die('100|'.$cfg_secureAccess.$cfg_basehost);


			}else{
				die('200|' . $langData['siteConfig'][21][235]);  //注册失败，请稍候重试！
			}


		}


		//手机
		if($rtype == 3){

			if(empty($areaCode)) die('201|' . $langData['siteConfig'][21][238]);  //请输入区域码！
			if(empty($account)) die('201|' . $langData['siteConfig'][20][239]);  //请输入手机号
			if(empty($vcode)) die('201|' . $langData['siteConfig'][20][28]);  //请输入短信验证码
			if(empty($password)) die('201|' . $langData['siteConfig'][21][237]);  //请输入登录密码！

			$areaCode = (int)$areaCode;

			$archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
					$international = $results[0]['international'];
					if(!$international){
						$areaCode = "";
					}
			}else{
					return array("state" => 200, "info" => $langData['siteConfig'][33][3]);//短信平台未配置，发送失败！
			}

			/*preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $account, $matchPhone);
			if(!$matchPhone){
				die('205|手机格式有误');
			}*/

			$phone_ishave = false;
			$archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `phone` = '$account'");
			$return = $dsql->dsqlOper($archives, "results");
			if($return){
				$phone_ishave = true;
				// 第三方登陆绑定手机号进来时，此处忽略手机号是否已注册的验证
				if(empty($bindMobile) || $code == "email"){
					die('205|' . $langData['siteConfig'][21][231]); //此手机号已被注册！
				}
			}

			//验证输入的验证码
			$archives = $dsql->SetQuery("SELECT `id`, `pubdate` FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'signup' AND `user` = '".$areaCode.$account."' AND `code` = '$vcode'");
			$results  = $dsql->dsqlOper($archives, "results");
			if(!$results){
				die('205|' . $langData['siteConfig'][21][222]);  //验证码输入错误，请重试！
			}else{

				//5分钟有效期
				if($regtime - $results[0]['pubdate'] > 300) die('205|' . $langData['siteConfig'][21][33]);  //验证码已过期，请重新获取！

				//验证通过删除发送的验证码
				$archives = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'signup' AND `user` = '".$areaCode.$account."' AND `code` = '$vcode'");
				$dsql->dsqlOper($archives, "update");
			}

			// 通过邮箱注册或第三方登陆进来
			if($bindMobile){
				$RenrenCrypt = new RenrenCrypt();
				$uid = $RenrenCrypt->php_decrypt(base64_decode($bindMobile));
				// 查询用户
				// 邮箱
				if($code == "email"){
					$archives = $dsql->SetQuery("SELECT `username`, `email`, `paypwd`, `sourceclient` FROM `#@__member` WHERE `id` = $uid");
					$results  = $dsql->dsqlOper($archives, "results");
					if($results){
						// 手机号已存在，验证该手机号账号是否已绑定邮箱
						if($phone_ishave){
							die('205|' . $langData['siteConfig'][21][231]); //此手机号已被注册！
							// $sql = $dsql->SetQuery("SELECT `id`, `password` FROM `#@__member` WHERE `phone` = '$account' AND `email` = ''");
							// $ret = $dsql->dsqlOper($sql, "results");
							// // 未绑定邮箱
							// if($ret){
							// 	$sql = $dsql->SetQuery("UPDATE `#@__member` SET `email` WHERE `id` = ".$ret[0]['id']);
							// 	$ret = $dsql->dsqlOper($sql, "update");
							// 	if($ret == "ok"){
							// 		// 删除当前邮箱账号重新登陆
							// 		$sql = $dsql->SetQuery("DELETE FROM `#@__member` WHERE `id` = $uid");
							// 		$ret = $dsql->dsqlOper($sql, "update");

							// 		global $cfg_cookiePath;
							// 		global $cfg_onlinetime;
							// 		$data = $ret[0]['id'].'&'.$ret[0]['password'];
							// 		$RenrenCrypt = new RenrenCrypt();
							// 		$userid = base64_encode($RenrenCrypt->php_encrypt($data));
							// 		PutCookie($userLogin->keepMemberID, $userid, $cfg_onlinetime * 60 * 60, $cfg_cookiePath);

							// 		PutCookie("connect_uid", "");
							// 		die('100|'.$cfg_secureAccess.$cfg_basehost);
							// 	}else{
							// 		die('200|' . $langData['siteConfig'][21][239]);  //绑定手机号失败，请重试！
							// 	}
							// }else{
							// 	die('205|' . $langData['siteConfig'][21][231]); //此手机号已被注册！
							// }
						}else{
							$username = $resulst[0]['username'];
							$password = $resulst[0]['paypwd'];
							$email    = $resulst[0]['email'];
							//记录当前设备s
							if($resulst[0]['sourceclient']){
								$sourceclientAll = unserialize($resulst[0]['sourceclient']);
							}
							$sourceclients = array();
							if(!empty($deviceTitle) && !empty($deviceSerial) && !empty($deviceType)){
								if(!empty($sourceclientAll)){
									$sourceclients = $sourceclientAll;
									//$foundTitle  = array_search($deviceTitle, array_column($sourceclientAll, 'title'));
									$foundSerial = array_search($deviceSerial, array_column($sourceclientAll, 'serial'));
									//$foundType   = array_search($deviceType, array_column($sourceclientAll, 'type'));
									if($foundSerial){
										//如果已有，更新时间，以Serial为准
										$sourceclients[$foundSerial]['pudate'] = time();
									}else{
										array_push($sourceclients, $sourceArr);
									}
								}else{
									$sourceclients[] = $sourceArr;
								}
								$sourceclients = serialize($sourceclients);

								$where_ = "`sourceclient` = '$sourceclients',";
							}
							//记录当前设备e
						}
					}else{
						die('200|' . $langData['siteConfig'][21][239]);  //绑定手机号失败，请重试！
					}

					$sql = $dsql->SetQuery("UPDATE `#@__member` SET $where_ `areaCode` = '$areaCode', `phone` = '$account', `phoneCheck` = '1', `state` = 1, `paypwd` = '' WHERE `id` = $uid");

				// 第三方登陆
				}else{

					$code_field = $code == "wechat" ? "`wechat_conn`, `wechat_openid`" : "`".$code."_conn`";
					$archives = $dsql->SetQuery("SELECT `username`, `photo`, `nickname`, ".$code_field." FROM `#@__member` WHERE `id` = $uid");
					$results  = $dsql->dsqlOper($archives, "results");
					if($results){
						// 手机号已存在，验证该手机号账号是否已绑定第三方账号
						if($phone_ishave){
							$sql = $dsql->SetQuery("SELECT `id`, `password`, `sourceclient`, `photo`, `nickname`, `".$code."_conn` FROM `#@__member` WHERE `phone` = '$account' AND `".$code."_conn` = ''");
							$ret = $dsql->dsqlOper($sql, "results");
							// 未绑定第三方账号
							if($ret){
								// 更新已存在账号第三方绑定信息
								if($code == "wechat"){
									$up_field = "`wechat_conn` = '".$results[0]['wechat_conn']."', `wechat_openid` = '".$results[0]['wechat_openid']."'";
								}else{
									$up_field = "`".$code."_conn` = '".$results[0][$code."_conn"]."'";
								}
								if(empty($ret[0]['photo'])){
									$up_field .= ", `photo` = '".$results[0]['photo']."'";
								}
								if(empty($ret[0]['nickname'])){
									$up_field .= ", `nickname` = '".$results[0]['nickname']."'";
								}
								//记录当前设备s
								if($ret[0]['sourceclient']){
									$sourceclientAll = unserialize($ret[0]['sourceclient']);
								}
								$sourceclients = array();
								if(!empty($deviceTitle) && !empty($deviceSerial) && !empty($deviceType)){
									if(!empty($sourceclientAll)){
										$sourceclients = $sourceclientAll;
										//$foundTitle  = array_search($deviceTitle, array_column($sourceclientAll, 'title'));
										$foundSerial = array_search($deviceSerial, array_column($sourceclientAll, 'serial'));
										//$foundType   = array_search($deviceType, array_column($sourceclientAll, 'type'));
										if($foundSerial){
											//如果已有，更新时间，以Serial为准
											$sourceclients[$foundSerial]['pudate'] = time();
										}else{
											array_push($sourceclients, $sourceArr);
										}
									}else{
										$sourceclients[] = $sourceArr;
									}
									$sourceclients = serialize($sourceclients);
									$up_field .= ", `sourceclient` = '".$sourceclients."'";
								}
								//记录当前设备e
								$sql = $dsql->SetQuery("UPDATE `#@__member` SET ".$up_field." WHERE `id` = ".$ret[0]['id']);

								$res = $dsql->dsqlOper($sql, "update");
								if($res == "ok"){
									// 删除当前第三方账号，登陆已存在账号
									$sql = $dsql->SetQuery("DELETE FROM `#@__member` WHERE `id` = $uid");
									$dsql->dsqlOper($sql, "update");

									// 查询微信临时表-pc端同步登陆
									$sql = $dsql->SetQuery("UPDATE `#@__site_wxlogin` SET `uid` = ".$ret[0]['id']." WHERE `uid` = $uid");
									$dsql->dsqlOper($sql, "update");

									$userLogin->keepUserID = $userLogin->keepMemberID;
									$userLogin->userID = $ret[0]['id'];
									$userLogin->userPASSWORD = $ret[0]['password'];
									$userLogin->keepUser();

									PutCookie("connect_uid", "");
									die('100|'.$cfg_secureAccess.$cfg_basehost);
								}else{
									die('200|' . $langData['siteConfig'][21][239]);  //绑定手机号失败，请重试！
								}
							// 已绑定第三方账号
							}else{
								die('205|'.$langData['siteConfig'][33][38]); //该手机号码已注册并绑定了第三方账号，如需将手机号绑定此第三方账号，请先用手机登陆，然后在安全中心进行解绑，然后再绑定此第三方账号！
							}
						}else{
							$username = $resulst[0]['username'];
							$password = "";
							$chcode = strtolower(create_check_code(8));
						}
					}else{
						die('200|' . $langData['siteConfig'][21][239]);  //绑定手机号失败，请重试！
					}

					$sql = $dsql->SetQuery("UPDATE `#@__member` SET `areaCode` = '$areaCode', `phone` = '$account', `phoneCheck` = '1', `state` = 1 WHERE `id` = $uid");

				}

				$ret = $dsql->dsqlOper($sql, "update");
				if($ret == "ok"){

					// 用户登陆后再清除
					// PutCookie("connect_uid", "");




					if($code == "email"){

						//论坛同步
						$accountData = explode("@", $email);
						$data['username'] = $accountData[0];
						$data['password'] = $password;
						$data['email']    = $account;
						$userLogin->bbsSync($data, "register");

					}else{

						//如果是微信扫码登录，需要更新临时登录日志
						if($state){
							$archives = $dql->SetQuery("UPDATE `#@__site_wxlogin` SET `uid` = '$uid' WHERE `state` = '$state'");
							$results = $dql->dsqlOper($sql, "update");
						}

						//论坛同步
						global $cfg_bbsState;
						global $cfg_bbsType;
						if($cfg_bbsState == 1 && $cfg_bbsType != ""){
							$data['username'] = $username;
							$data['password'] = $password;
							$data['email']    = $chcode."@qq.com";
							$userLogin->bbsSync($data, "register");
						}

					}

					$aid = $uid;

				}else{
					die('200|' . $langData['siteConfig'][21][239]);  //绑定手机号失败，请重试！
				}

			}else{

				//保存到主表
				$nickname = preg_replace('/(1[34578]{1}[0-9])[0-9]{4}([0-9]{4})/is',"$1****$2", $account);
				$archives = $dsql->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `areaCode`, `phone`, `phoneCheck`, `regtime`, `regip`, `regipaddr`, `state`, `purviews`, `sourceclient`) VALUES ('$mtype', '$account', '$passwd', '$nickname', '$areaCode', '$account', '1', '$regtime', '$regip', '$regipaddr', '1', '', '$sourceclient')");
				$aid = $dsql->dsqlOper($archives, "lastid");

			}


			if(is_numeric($aid)){

				if(!$bindMobile){
					$userLogin->registGiving($aid);
				}

				//论坛同步
				global $cfg_bbsState;
				global $cfg_bbsType;
				if($cfg_bbsState == 1 && $cfg_bbsType != ""){
					$chcode = strtolower(create_check_code(8));
					$data['username'] = $account;
					$data['password'] = $passwd;
					$data['email']    = $chcode."@qq.com";
					$userLogin->bbsSync($data, "register");
				}

				//自动登录
				$ureg = $userLogin->memberLogin($account, $password);
				die('100|'.$cfg_secureAccess.$cfg_basehost);


			}else{
				die('200|' . $langData['siteConfig'][21][235]);  //注册失败，请稍候重试！
			}

		}
		return;


	//注册成功，不需要验证
	}elseif($action == "registerSuccess"){

		$memberId = $userLogin->getMemberID();
		if($memberId > -1){

			$archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `id` = '$memberId'");
			$return = $dsql->dsqlOper($archives, "results");
			if($return){

				$huoniaoTag->assign('username', $return[0]['username']);
				$huoniaoTag->assign('email', $return[0]['email']);
				$huoniaoTag->assign('phone', $return[0]['phone']);

			}else{
				die('会员不存在！');
			}

		}else{
			$furl = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
		}
		return;

	//注册成功，邮箱验证
	}elseif($action == "registerVerifyEmail"){

		$RenrenCrypt = new RenrenCrypt();
		$uid = $RenrenCrypt->php_decrypt(base64_decode($userid));

		if(!empty($userid)){

			$archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `id` = '$uid'");
			$return = $dsql->dsqlOper($archives, "results");
			if($return){

				$username   = $return[0]['username'];
				$email      = $return[0]['email'];
				$state      = $return[0]['state'];

				global $cfg_webname;
				global $cfg_basehost;

				if(empty($return[0]['sendEmail'])){
					if($state == 0){

						//获取邮件内容
						$cArr = getInfoTempContent("mail", '会员-帐号激活-发送邮件', array("email" => $email, "userid" => $userid));
						$title = $cArr['title'];
						$content = $cArr['content'];

						if($title == "" && $content == ""){
							// showMsg("邮件通知功能未开启，邮件发送失败！", "login.html?furl=".$furl);
						}

						sendmail($email, $title, $content);

						$now = GetMkTime(time());
						$archives = $dsql->SetQuery("UPDATE `#@__member` SET `sendEmail` = ".$now." WHERE `id` = '$uid'");
						$dsql->dsqlOper($archives, "update");

					}else{
						$furl = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
						showMsg($langData['siteConfig'][21][240], "login.html?furl=".$furl);  //您已完成邮箱验证，请登录！
						die;
					}
				}

				$huoniaoTag->assign('email', $email);

			}else{
				die('会员不存在！');
			}

		}else{
			$furl = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
		}
		return;

	//邮箱验证
	}elseif($action == "memberVerifyEmail"){

		$RenrenCrypt = new RenrenCrypt();
		$uid = $RenrenCrypt->php_decrypt(base64_decode($userid));

		if(!empty($userid)){

			$archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `id` = '$uid'");
			$return = $dsql->dsqlOper($archives, "results");
			if($return){

				$username   = $return[0]['username'];
				$email      = $return[0]['email'];
				$state      = $return[0]['state'];
				$sendEmail  = $return[0]['sendEmail'];

				if($state != 0){
					$furl = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
					showMsg($langData['siteConfig'][21][240], "login.html?furl=".$furl);  //您已完成邮箱验证，请登录！
					die;
				}

				$regtime  = GetMkTime(time());
				if(round(($regtime - $sendEmail)/3600) > 24){

					$archives = $dsql->SetQuery("DELETE FROM `#@__member` WHERE `id` = ".$uid);
					$dsql->dsqlOper($archives, "update");

					showMsg($langData['siteConfig'][21][241], "register.html");  //您的邮件验证已超过24小时的有效时间，请重新注册！
					die;
				}

				global $cfg_webname;
				global $cfg_basehost;

				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `state` = 1, `emailCheck` = 1 WHERE `id` = '$uid'");
				$dsql->dsqlOper($archives, "update");

				$huoniaoTag->assign('username', $username);
				$huoniaoTag->assign('email', $email);

				global $cfg_cookiePath;
				global $cfg_onlinetime;
				PutCookie("login_user", $userid, $cfg_onlinetime * 60 * 60, $cfg_cookiePath);

			}else{
				die('会员不存在！');
			}

		}else{
			$furl = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
		}
		return;

	//获取登录用户信息
	}elseif($action == "getUserInfo"){

		$userinfo = array();
		if($userLogin->getMemberID() > -1){
			$userinfo = $userLogin->getMemberInfo();
		}
		if($userinfo){
			if($callback){
				echo $callback.'('.json_encode($userinfo).')';
			}else{
				echo json_encode($userinfo);
			}
		}
		die;

	//站内消息
	}elseif($action == "message"){

        if(isApp() && $userid == -1) {
            global $cfg_staticPath;
            global $cfg_staticVersion;
            $url = $cfg_secureAccess . $cfg_basehost;

            $html = <<<eot
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0">
	<title></title>
	<link rel="stylesheet" type="text/css" href="{$cfg_staticPath}css/core/touchBase.css?v=$cfg_staticVersion">
	<script src="{$cfg_staticPath}js/core/touchScale.js?v=$cfg_staticVersion"></script>
	<script src="{$cfg_staticPath}js/core/zepto.min.js?v=$cfg_staticVersion"></script>
	<style>
	    html, body {height: 100%; background: rgba(0, 0, 0, .2);}
	    .popup {position: fixed; width: 4.7rem; left: 50%; top: 50%; margin: -2.75rem 0 0 -2.35rem; padding: .5rem 0; background: #fff; border-radius: 4px; text-align: center;}
	    .popup h3 {font-size: .3rem;}
	    .popup img {width: 1rem; height: 1rem; display: block; margin: .3rem auto;}
	    .popup a {width: 2.45rem; height: .58rem; display: block; margin: 0 auto; line-height: .58rem; color: #fff; font-size: .26rem; border-bottom: 5px solid #fcb0a4; border-radius: 4px; background-color: rgb(248, 63, 33);}
    </style>
	<script>
	    window.open("{$url}/login.html");
    </script>
</head>
<body>
    <div class="popup">
        <h3>您还未登录，请先登录</h3>
        <img src="{$cfg_staticPath}images/login_tip_icon.png" />
        <a href="{$url}/login.html">立即登录</a>
    </div>
</body>
</html>
eot;
            echo $html;
            die;
        }

		$userLogin->checkUserIsLogin();

		$page = empty($page) ? 1 : $page;
		$huoniaoTag->assign('atpage', $page);
		$huoniaoTag->assign('state', $state);

	//站内消息详细信息
	}elseif($action == "message_detail"){

		$userLogin->checkUserIsLogin();
		$userid = $userLogin->getMemberID();

		$id = (int)$id;
		if(empty($id)){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
			die;
		}

		$sql = $dsql->SetQuery("SELECT log.`state`, l.`title`, l.`body`, l.`urlParam`, l.`date` FROM `#@__member_letter_log` log LEFT JOIN `#@__member_letter` l ON l.`id` = log.`lid` WHERE l.`type` = 0 AND log.`id` = $id AND log.`uid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$data = $ret[0];

			//更新状态
			if($data['state'] == 0){
				$sql = $dsql->SetQuery("UPDATE `#@__member_letter_log` SET `state` = 1 WHERE `id` = $id");
				$dsql->dsqlOper($sql, "update");
			}

			//跳转
			if(!empty($data['urlParam'])){
				$param = unserialize($data['urlParam']);

				//APP端重定向到个人中心 by gz 20181018
				if($param['service'] == 'member' && !$param['type'] && isApp()){
				    if(
				        strstr($param['template'], 'withdraw') ||
                        strstr($param['template'], 'security') ||
                        strstr($param['template'], 'point') ||
                        strstr($param['template'], 'upgrade') ||
                        (strstr($param['template'], 'manage') && ( strstr($param['action'], 'article') || strstr($param['action'], 'info') )) ||
                        strstr($param['template'], 'record')
                    ){
                        $param['type'] = 'user';
                    }

                    if(
                        $param['template'] == 'config' && $param['action'] == 'business'
                    ){
                        $param['type'] = 'user';
                        $param['template'] = 'business';
                        $param['action'] = 'config';
                    }
                }

				//外卖订单详情只允许移动端访问
				if($param['service'] == 'member' && $param['template'] == 'orderdetail' && $param['action'] == 'waimai' && !isMobile()){
					$data['body'] = '请在手机端查看订单！';
				}else{
					header("location:".getUrlPath($param));
				}
			}

			$huoniaoTag->assign('title', $data['title']);
			$huoniaoTag->assign('body', $data['body']);
			$huoniaoTag->assign('date', date("Y-m-d H:i:s", $data['date']));

		}else{
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
			die;
		}
		return;



	}
    //房产经纪人
    elseif(stripos($action, "config-house") !== false){

		$userLogin->checkUserIsLogin();
		$userid = $userLogin->getMemberID();
		$jjr = 0;

		$sql = $dsql->SetQuery("SELECT * FROM `#@__house_zjuser` WHERE `userid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$jjr = 1;

			$contorllerFile = dirname(__FILE__).'/house.controller.php';
			if(file_exists($contorllerFile)){
				//声明以下均为接口类
				$handler = true;
				require_once($contorllerFile);

				$param = array(
					"action" => "broker-detail",
					"id"     => $ret[0]['id'],
					"u"      => 1
				);
				house($param);
			}
		}
		$huoniaoTag->assign("jjr", $jjr);

        $zjcom = 0;
        $sql = $dsql->SetQuery("SELECT * FROM `#@__house_zjcom` WHERE `userid` = $userid");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $zjcom = 1;
        }
        $huoniaoTag->assign("zjcom", $zjcom);


	//管理发布的信息
}elseif($action == "fabusuccess" || $action == "car_entrust" || $action == "car_receive_broker" || $action == "car-broker" || $action == "manage" || $action == "fabu"|| $action == "order" || $action == "team" || $action == "teamAdd" || $action == "albums" || $action == "albumsAdd" || $action == "case" || $action == "caseAdd" || $action == "booking" || $action == "post" || $action == "collections" || $action == "invitation" || $action == "resume" || $action == "house-broker" || $action == "statistics" || $action == "statistics" || $action == "house_receive_broker" || $action == "house_entrust" || $action == "education-order"){

		$userLogin->checkUserIsLogin();
		$userid = $userLogin->getMemberID();
		$userinfo = $userLogin->getMemberInfo();

		//发布前验证实名认证
		if($action == 'fabu'){
			global $cfg_memberVerified;
            global $cfg_memberVerifiedInfo;

			if($cfg_memberVerified && $userinfo['userType'] == 1 && !$userinfo['certifyState']){
				$param = array(
					"service"  => "member",
					"type"     => "user",
					"template" => "security",
					"doget"    => isMobile() ? "shCertify" : "chCertify"
				);
				$certifyUrl = getUrlPath($param);
                $cfg_memberVerifiedInfo = $cfg_memberVerifiedInfo ? $cfg_memberVerifiedInfo : $langData['siteConfig'][33][49];//请先进行实名认证！
				die('<meta charset="UTF-8"><script type="text/javascript">alert("'.$cfg_memberVerifiedInfo.'");location.href="'.$certifyUrl.'";</script>');
			}

            // 手机认证
            global $cfg_memberBindPhone;
            global $cfg_memberBindPhoneInfo;
            if($cfg_memberBindPhone && (!$userinfo['phone'] || !$userinfo['phoneCheck'])){
                $param = array(
                    "service"  => "member",
                    "type"     => "user",
                    "template" => "security",
                    "doget"    => "chphone"
                );
                $certifyUrl = getUrlPath($param);
                $cfg_memberBindPhoneInfo = $cfg_memberBindPhoneInfo ? $cfg_memberBindPhoneInfo : $langData['siteConfig'][33][53];//请先进行手机认证！
                die('<meta charset="UTF-8"><script type="text/javascript">alert("'.$cfg_memberBindPhoneInfo.'");location.href="'.$certifyUrl.'";</script>');
            }

			//资讯发布前验证是否入驻自媒体
			if($module=='article'){
                $obj = new article();
                $check = $obj->selfmedia_verify($userLogin->getMemberID(), "", "check", $vdata);
                if($check != "ok"){
                    $param = array(
                        "service" => "member",
                        "type"=>"user",
                        "template" => "config-selfmedia"
                    );
                    $selfmediaurl = getUrlPath($param);
                    $selfmediadInfo = $langData['siteConfig'][33][50];//请先入驻自媒体！
                    $selfmediadInfo = "您还没有入驻自媒体";
                    die('<meta charset="UTF-8"><script type="text/javascript">alert("'.$selfmediadInfo.'");location.href="'.$selfmediaurl.'";</script>');
                    //header("location:".$url);die;
                }
                $file = HUONIAOINC."/plugins/5/getInfo.php";
                if(is_file($file)){
                    $huoniaoTag->assign('reprintUrl', "/include/plugins/5/getInfo.php");
                }

                $huoniaoTag->assign('ac_id', $vdata['id']);
                $huoniaoTag->assign('ac_name', $vdata['ac_name']);
                $huoniaoTag->assign('ac_type', $vdata['type']);
			}
			//家政
			if($module=='homemaking'){
				$sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__homemaking_store` WHERE `state` = 1 AND `userid` = $userid");
				$res = $dsql->dsqlOper($sql, "results");
				if(empty($res[0]['id'])){
					$param = array(
						"service"  => "member",
						"template" => "config-homemaking"
					);
					$homemakingurl = getUrlPath($param);
					$homemakingInfo = $langData['homemaking'][8][43];//请先入驻家政公司！
					die('<meta charset="UTF-8"><script type="text/javascript">alert("'.$homemakingInfo.'");location.href="'.$homemakingurl.'";</script>');
					//header("location:".$url);die;
				}
                $huoniaoTag->assign('homemaking_store_title', $res[0]['title']);
			}
		}

		$huoniaoTag->assign('module', $module);

		$page = empty($page) ? 1 : $page;
		$huoniaoTag->assign('atpage', $page);
		$huoniaoTag->assign('state', $state);
		$huoniaoTag->assign('type', $type);
		$huoniaoTag->assign('do', $do);
		$huoniaoTag->assign('id', (int)$id);
		$huoniaoTag->assign('typeid', (int)$typeid);
		$huoniaoTag->assign('userid', (int)$userid);

		if($action == "fabu"){

			//只有升级的会员或企业会员才有权限访问 by 20170726
			if($userinfo['level'] == 0 && $userinfo['userType'] == 1){
				// $param = array(
				// 	"service"  => "member",
				// 	"type"     => "user",
				// 	"template" => "upgrade"
				// );
				// $url = getUrlPath($param);
				// header("location:" . $url);
				// die;
			}


			//获取图片配置参数
			require(HUONIAOINC."/config/".$module.".inc.php");

			if($customUpload == 1){
				$huoniaoTag->assign('thumbSize', $custom_thumbSize);
				$huoniaoTag->assign('thumbType', "*.".str_replace("|", ";*.", $custom_thumbType));
				$huoniaoTag->assign('atlasSize', $custom_atlasSize);
				$huoniaoTag->assign('atlasType', "*.".str_replace("|", ";*.", $custom_atlasType));
			}

			//房产单独配置
			if($module == "house"){
				if($type == "sale"){
					$customAtlasMax = $custom_houseSale_atlasMax;
				}elseif($type == "zu"){
					$customAtlasMax = $custom_houseZu_atlasMax;
				}elseif($type == "xzl"){
					$customAtlasMax = $custom_houseXzl_atlasMax;

                    // 配套设施
                    $peitaoCfg = array(
                        0 => array("type" => "ict", "name" => $langData['siteConfig'][34][28]),//员工餐厅
                        1 => array("type" => "ift", "name" => $langData['siteConfig'][34][29]),//扶梯
                        2 => array("type" => "ibg", "name" => $langData['siteConfig'][34][30]),//办公家具
                        3 => array("type" => "ign", "name" => $langData['siteConfig'][34][31]),//集中供暖
                        4 => array("type" => "ikt", "name" => $langData['siteConfig'][34][32]),//中央空调
                        5 => array("type" => "ielectric", "name" => $langData['siteConfig'][34][33]),//电
                        6 => array("type" => "iht", "name" => $langData['siteConfig'][34][34]),//货梯
                        7 => array("type" => "iketi", "name" => $langData['siteConfig'][34][35]),//客梯
                        8 => array("type" => "itel", "name" => $langData['siteConfig'][3][1]),//电话
                        9 => array("type" => "ikd", "name" => $langData['siteConfig'][34][36]),//宽带
                        10 => array("type" => "itv", "name" => $langData['siteConfig'][34][37]),//有线电视
                        11 => array("type" => "iwater", "name" => $langData['siteConfig'][34][38]),//水
                        12 => array("type" => "ijk", "name" => $langData['siteConfig'][32][86]),//监控
                        13 => array("type" => "ipark", "name" => $langData['siteConfig'][31][7]),//车位
                    );
                    $huoniaoTag->assign('peitaoCfg', $peitaoCfg);

				}elseif($type == "sp"){
					$customAtlasMax = $custom_houseSp_atlasMax;
				}elseif($type == "cf"){
					$customAtlasMax = $custom_houseCf_atlasMax;
                }elseif($type == "cw"){
                    $customAtlasMax = $custom_houseCw_atlasMax;
				}
                $customAtlasMax = $customAtlasMax == "" ? 9 : $customAtlasMax;

                //判断是否经纪人
                if($do != "edit"){
                    $sql = $dsql->SetQuery("SELECT `id`, `meal` FROM `#@__house_zjuser` WHERE `userid` = $userid");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $config = $ret[0]['meal'] ? unserialize($ret[0]['meal']) : array();
                        $house = new house();
                        $check = $house->checkZjuserMeal($config);
                        if($check['state'] == 200){
                            $huoniaoTag->assign('zjuserMealInfo', $check['info']);
                        }
                    }
				}

			//汽车
			}elseif($module == "car"){
				$customAtlasMax = $custom_car_atlasMax ? $custom_car_atlasMax : 9;

			//家政
			}elseif($module == "homemaking"){
				$customAtlasMax = $custom_homemaking_atlasMax ? $custom_homemaking_atlasMax : 9;

				$sql = $dsql->SetQuery("SELECT `flag` FROM `#@__homemaking_list` WHERE `id` = '$id'");
				$res = $dsql->dsqlOper($sql, "results");

				$homemakingHandlers = new handlers("homemaking", "config");
				$homemakingConfig   = $homemakingHandlers->getHandle();
				$homemakingConfig   = $homemakingConfig['info'];

				$homemakingTag_all = $homemakingConfig['homemakingFlag'];
				$homemakingTag_all_ = array();

				if(!empty($res[0]['flag'])){
					$homemakingTag_ = explode('|', $res[0]['flag']);
				}else{
					$homemakingTag_ = array();
				}
				foreach ($homemakingTag_all as $v) {
					$homemakingTag_all_[] = array(
						'name' => $v,
						'icon' => 'b_sertag_' . GetPinyin($v) . '.png',
						'active' => in_array($v, $homemakingTag_) ? 1 : 0
					);
				}

				$huoniaoTag->assign('homemakingTag_state', $homemakingTag_all_);

			//婚嫁
			}elseif($module == "marry"){
				if($type == "field"){//婚宴场地
					$customAtlasMax = $custom_marryhotelfield_atlasMax ? $custom_marryhotelfield_atlasMax : 9;
				}elseif($type == "rental"){//婚车
					$customAtlasMax = $custom_marryweddingcar_atlasMax ? $custom_marryweddingcar_atlasMax : 9;
				}elseif($type == "case"){//案例
					$customAtlasMax = $custom_marryplancase_atlasMax ? $custom_marryplancase_atlasMax : 9;
				}elseif($type == "meal"){//套餐
					$customAtlasMax = $custom_marryplanmeal_atlasMax ? $custom_marryplanmeal_atlasMax : 9;

					$sql = $dsql->SetQuery("SELECT `tag` FROM `#@__marry_planmeal` WHERE `id` = '$id'");
					$res = $dsql->dsqlOper($sql, "results");

					$marryHandlers = new handlers("marry", "config");
					$marryConfig   = $marryHandlers->getHandle();
					$marryConfig   = $marryConfig['info'];

					$marryTag_all  = $marryConfig['marryTag'];
					$marryTag_all_ = array();

					if(!empty($res[0]['tag'])){
						$marryTag_ = explode('|', $res[0]['tag']);
					}else{
						$marryTag_ = array();
					}
					foreach ($marryTag_all as $v) {
						$marryTag_all_[] = array(
							'name' => $v,
							'icon' => 'b_sertag_' . GetPinyin($v) . '.png',
							'active' => in_array($v, $marryTag_) ? 1 : 0
						);
					}

					$huoniaoTag->assign('marryTag_state', $marryTag_all_);
				}

			//旅游
			}elseif($module == "travel"){
				if($type == "hotel"){//酒店
					$customAtlasMax = $custom_travelhotel_atlasMax ? $custom_travelhotel_atlasMax : 9;

					//酒店分类
					$travelHandlers = new handlers($module, "travelhotel_type");
					$travelConfig   = $travelHandlers->getHandle();
					if($travelConfig['state'] == 100){
						$module_type = $travelConfig['info'];
						$huoniaoTag->assign('module_type', $module_type);
					}

					//窗户分类
					$travelHandlers = new handlers($module, "iswindow_type");
					$travelConfig   = $travelHandlers->getHandle();
					if($travelConfig['state'] == 100){
						$iswindow_type = $travelConfig['info'];
						$huoniaoTag->assign('iswindow_type', $iswindow_type);
					}

					//房间类型
					$travelHandlers = new handlers($module, "room_type");
					$travelConfig   = $travelHandlers->getHandle();
					if($travelConfig['state'] == 100){
						$room_type = $travelConfig['info'];
						$huoniaoTag->assign('room_type', $room_type);
					}

					//早餐分类
					$travelHandlers = new handlers($module, "breakfast_type");
					$travelConfig   = $travelHandlers->getHandle();
					if($travelConfig['state'] == 100){
						$breakfast_type = $travelConfig['info'];
						$huoniaoTag->assign('breakfast_type', $breakfast_type);
					}

				}elseif($type == "ticket"){//景点门票
					$customAtlasMax = $custom_travelticket_atlasMax ? $custom_travelticket_atlasMax : 9;
				}elseif($type == "rentcar"){//旅游租车
					$customAtlasMax = $custom_travelrentcar_atlasMax ? $custom_travelrentcar_atlasMax : 9;
				}elseif($type == "visa"){//旅游签证
					$customAtlasMax = $custom_travelvisa_atlasMax ? $custom_travelvisa_atlasMax : 9;
				}elseif($type == "agency"){//周边游
					$customAtlasMax = $custom_travelagency_atlasMax ? $custom_travelagency_atlasMax : 9;

					//周边游分类
					$travelHandlers = new handlers($module, "travelagency_type");
					$travelConfig   = $travelHandlers->getHandle();
					if($travelConfig['state'] == 100){
						$travelagency_type = $travelConfig['info'];
						$huoniaoTag->assign('travelagency_type', $travelagency_type);
					}
				}elseif($type == "strategy"){//旅游攻略
					$customAtlasMax = $custom_travelstrategy_atlasMax ? $custom_travelstrategy_atlasMax : 9;
				}

			//教育
			}elseif($module == "education"){
				//是否有教师
				if($type == "courses"){
					$customAtlasMax = $custom_educationcourses_atlasMax ? $custom_educationcourses_atlasMax : 9;

					$isteacher = 0;
					$sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__education_store` WHERE `userid` = $userid");
					$res = $dsql->dsqlOper($sql, "results");
					if(!empty($res[0]['id'])){
						$storeid = $res[0]['id'];
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__education_teacher` WHERE `company` = " . $res[0]['id']);
						$res = $dsql->dsqlOper($sql, "results");
						if(!empty($res[0]['id'])){
							$isteacher = 1;
						}
					}
					$huoniaoTag->assign('isteacher', (int)$isteacher);
					$huoniaoTag->assign('storeid', (int)$storeid);

					$sql = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__education_item` WHERE `parentid` = '3'");
					$res = $dsql->dsqlOper($sql, "results");
					$huoniaoTag->assign('itemall', $res ? json_encode($res) : '[]');

				}

			}elseif($module == "shop"){
				if($type == 'branch'){
					$sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__shop_branch_store` WHERE `userid` = $userid");
					$res = $dsql->dsqlOper($sql, "results");
					if($res){
						$branchStoreId = $res[0]['id'];
					}
				}
			}elseif($module == "pension"){
				if($type=='albums'){
					$storeid = 0;
					$sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__pension_store` WHERE `userid` = $userid");
					$res = $dsql->dsqlOper($sql, "results");
					if(!empty($res[0]['id'])){
						$storeid = 1;
					}
				}
			}
			$huoniaoTag->assign('storeid', (int)$storeid);
			$huoniaoTag->assign('atlasMax', (int)$customAtlasMax);
			$huoniaoTag->assign('softSize', (int)$custom_softSize);
			$huoniaoTag->assign('branchStoreId', (int)$branchStoreId);

			global $cfg_videoSize;
			$huoniaoTag->assign('videoSize', (int)$cfg_videoSize);



			$contorllerFile = dirname(__FILE__).'/'.$module.'.controller.php';
			if(file_exists($contorllerFile)){

				//声明以下均为接口类
				$handler = true;
				require_once($contorllerFile);

				if($do == "edit"){
					global $do;
					global $oper;
					$do = "edit";
					$oper = "user";
					$param = array(
                        "realServer" => "member",
						"action" => "detail",
						"type"   => $type,
						"id"     => $id
					);
					$module($param);
				}

				if($module == "marry"){
					$param = array(
						"action" => "fabu",
						"type"   => $type,
						"id"     => $id
					);
					$module($param);
				}

				if($module == "shop" && $type == 'branch'){
					$param = array(
						"action" => "fabu",
						"type"   => $type,
						"id"     => $id ? $id : $branchStoreId
					);
					$module($param);
				}

				if($module == "travel"){
					$param = array(
						"action" => "fabu",
						"type"   => $type,
						"id"     => $id
					);
					$module($param);
				}

				if($module == "education"){
					if($type == 'tutor' || $type == 'setup'){
						$id = 1;
						if($type == 'setup'){
							$type = 'tutor';
						}
					}
					$param = array(
						"action" => "fabu",
						"type"   => $type,
						"id"     => $id
					);
					$module($param);
				}

				if($module == "pension"){
					$param = array(
						"action" => "fabu",
						"type"   => $type,
						"id"     => $id
					);
					$module($param);
				}

				if($module == "homemaking" && empty($type)){
					$param = array(
						"action" => "fabu",
						"id"     => $id
					);
					$module($param);
				}elseif($module == "homemaking" && $type=='nanny'){
					$param = array(
						"action" => "nannydetail",
						"id"     => $id
					);
					$module($param);
				}

				if($module == "car"){
					$param = array(
						"action" => "fabu",
						"id"     => $id
					);
					$module($param);
				}

				if($module == "info"){
					$param = array(
						"action" => "fabu",
						"typeid" => $typeid
					);
					$module($param);
				}

				if($module == "tuan"){
					$param = array(
						"action" => "fabu",
						"id"     => $id
					);
					$module($param);
				}

				if($module == "shop"){
					$param = array(
						"action" => "fabu",
						"typeid" => $typeid
					);
					$module($param);
				}

				if($module == "build"){
					$param = array(
						"action" => "fabu"
					);
					$module($param);
				}

				if($module == "furniture"){
					$param = array(
						"action" => "fabu"
					);
					$module($param);
				}

				if($module == "home"){
					$param = array(
						"action" => "fabu"
					);
					$module($param);
				}

				if($module == "waimai"){
					$param = array(
						"action" => "fabu",
						"id" => $id
					);
					$module($param);
				}

				if($module == "website"){
					$param = array(
						"action" => "fabu",
						"act" => $type,
						"id" => $id
					);
					$module($param);
				}

				if($module == "business" && $type != 'custom_menu'){
					$param = array(
						"action" => "fabu",
						"act" => $type,
						"id" => $id
					);
					$module($param);
				}

				if($module == "vote"){
					$param = array(
						"action" => "fabu",
						"id" => $id
					);
					$module($param);
				}

				if($module=="live"){
                    if(!empty($id)){
                        global $userLogin;
                        global $dsql;
                        global $cfg_secureAccess;
                        global $cfg_basehost;
                        //$sql = $dsql->SetQuery("SELECT `title`,`click`,`litpic`,`ftime`,`typeid`,`catid`,`flow`,`way`,`pushurl` FROM `huoniao_livelist` where id =(SELECT max(`id`) i FROM `#@__livelist` where user='$userid')");
                        $sql = $dsql->SetQuery("SELECT `id`,`title`,`click`,`litpic`,`startmoney`,`endmoney`,`password`,`ftime`,`typeid`,`catid`,`flow`,`way`,`pushurl`,`note`,`menu`,`pulltype`,`pullurl_pc`,`pullurl_touch`,`lng`,`lat` FROM `huoniao_livelist` where id='$id'");
                        $res = $dsql->dsqlOper($sql, "results");
                        if($res){

                            $litpicS   = $res[0]['litpic'];
                            $litpic    = !empty($res[0]['litpic']) ? (strpos($res[0]['litpic'],'images') ? $cfg_secureAccess . $cfg_basehost . $res[0]['litpic'] : getFilePath($res[0]['litpic'])) : $cfg_secureAccess . $cfg_basehost . '/static/images/404.jpg';

                            $typeid        = $res[0]['typeid'];
                            $title         = empty($res[0]['title'])   ? $langData['siteConfig'][34][39] : $res[0]['title'];//无标题
                            $ftime         = !empty($res[0]['ftime']) ? date("Y-m-d H:i:s",$res[0]['ftime']) : date("Y-m-d H:i:s",time());
                            $password      = $res[0]['password'];
                            $way           = $res[0]['way'];
                            $catid         = $res[0]['catid'];
                            $startmoney    = $res[0]['startmoney'];
                            $endmoney      = $res[0]['endmoney'];
                            $flow          = $res[0]['flow'];
                            $note          = $res[0]['note'];
                            $menu          = $res[0]['menu'];
                            $pulltype      = $res[0]['pulltype'];
                            $pullurl_pc    = $res[0]['pullurl_pc'];
							$pullurl_touch = $res[0]['pullurl_touch'];
							$lnglat = '';
							if(!empty($res[0]['lng']) && !empty($res[0]['lat'])){
								$lnglat    = $res[0]['lng'] . ',' . $res[0]['lat'];
							}

                            $huoniaoTag->assign('lnglat', $lnglat);
                            $huoniaoTag->assign('catid', $catid);
                            $huoniaoTag->assign('typeid', $typeid);
                            $huoniaoTag->assign('flow', $flow);
                            $huoniaoTag->assign('way', $way);
                            $huoniaoTag->assign('ftime', $ftime);
                            $huoniaoTag->assign('title', $title);
                            $huoniaoTag->assign('litpic', $litpic);
                            $huoniaoTag->assign('password', $password);
                            $huoniaoTag->assign('startmoney', $startmoney);
                            $huoniaoTag->assign('endmoney', $endmoney);
                            $huoniaoTag->assign('flow', $flow);
                            $huoniaoTag->assign('litpicS', $litpicS);
                            $huoniaoTag->assign('note', $note);
                            $huoniaoTag->assign('menuArr', $menu ? unserialize($menu) : array());
                            $huoniaoTag->assign('pulltype', $pulltype);
                            $huoniaoTag->assign('pushurl', $pushurl);
                            $huoniaoTag->assign('pullurl_pc', $pullurl_pc);
                            $huoniaoTag->assign('pullurl_touch', $pullurl_touch);
                        }
                    }
                    $urlparam = array(
                        "service"     => "member",
                        "type"         => "user",
                        "template"    => "livedetail"
                    );
                    $url  = getUrlPath($urlparam);
                    $huoniaoTag->assign('url', $url);
                }


				if($type == 'custom_menu'){
                    $sql = $dsql->SetQuery("SELECT * FROM `#@__business_menu` WHERE `uid` = $userid ORDER BY `weight`, `id`");
                    $res = $dsql->dsqlOper($sql, "results");

                    $huoniaoTag->assign('menuList', $res);
                }

			}

		//团队
		}elseif($action == "teamAdd"){

			if(!empty($id)){
				$param = array(
					"id"     => $id,
					"action" => "designer-detail"
				);
				$module($param);
			}

		//效果图
		}elseif($action == "albumsAdd"){

			if(!empty($id)){
				$param = array(
					"id"     => $id,
					"action" => "albums-detail"
				);
				$module($param);
			}

			require(HUONIAOINC."/config/renovation.inc.php");

			if($customUpload == 1){
				$huoniaoTag->assign('thumbSize', $custom_thumbSize);
				$huoniaoTag->assign('thumbType', "*.".str_replace("|", ";*.", $custom_thumbType));
				$huoniaoTag->assign('atlasSize', $custom_atlasSize);
				$huoniaoTag->assign('atlasType', "*.".str_replace("|", ";*.", $custom_atlasType));
			}

			$huoniaoTag->assign('atlasMax', (int)$custom_case_atlasMax);

			$param = array("action" => "getDesignerByEnter");
			$module($param);

		//案例
		}elseif($action == "caseAdd"){

			if(!empty($id)){
				$param = array(
					"id"     => $id,
					"action" => "case-detail"
				);
				$module($param);
			}

			require(HUONIAOINC."/config/renovation.inc.php");

			if($customUpload == 1){
				$huoniaoTag->assign('thumbSize', $custom_thumbSize);
				$huoniaoTag->assign('thumbType', "*.".str_replace("|", ";*.", $custom_thumbType));
				$huoniaoTag->assign('atlasSize', $custom_atlasSize);
				$huoniaoTag->assign('atlasType', "*.".str_replace("|", ";*.", $custom_atlasType));
			}

			$huoniaoTag->assign('atlasMax', (int)$custom_diary_atlasMax);

			$param = array("action" => "getDesignerByEnter");
			$module($param);

		//职位
		}elseif($action == "post" && ($do == "add" || $do == "edit")){

			$module = "job";

			if(!empty($id)){

				global $oper;
				$oper = "user";

				$param = array(
					"id"     => $id,
					"action" => "job"
				);
				$module($param);
			}else{
				$userLogin->checkUserIsLogin();
				$userid = $userLogin->getMemberID();
				$sql = $dsql->SetQuery("SELECT `contact`, `email` FROM `#@__job_company` WHERE `userid` = $userid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$huoniaoTag->assign('job_tel', $ret[0]['contact']);
					$huoniaoTag->assign('job_email', $ret[0]['email']);
				}
			}

		//房产经纪人/中介公司收到的入驻申请/收到的房源委托
		}elseif($action == "house-broker" || $action == "house_receive_broker" || $action == "house_entrust"){

			$comid = 0;
			$userid = $userLogin->getMemberID();
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__house_zjcom` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$comid = $ret[0]['id'];
			}
            $huoniaoTag->assign('comid', $comid);
			$huoniaoTag->assign('id', (int)$id);

		// 统计
		}elseif($action == "statistics"){

			if($module == "vote"){
				$param = array(
					"action" => "detail",
					"id" => $id
				);
				$module($param);
			}

		//汽车顾问 入驻申请 委托卖车
		}elseif($action == "car_entrust" || $action == "car-broker" || $action == "car_receive_broker"){
			$comid = 0;
			$userid = $userLogin->getMemberID();
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__car_store` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$comid = $ret[0]['id'];
			}
            $huoniaoTag->assign('comid', $comid);
			$huoniaoTag->assign('id', (int)$id);
		}

		// 黄页
		if($module == "huangye"){
			$userid = $userLogin->getMemberID();

			$typeHandels = new handlers($module, "type");
			$typeConfig  = $typeHandels->getHandle(array("son" => 1));
			if($typeConfig && $typeConfig['state'] == 100){
				$typeList = $typeConfig['info'];
			}else{
				$typeList = array();
			}
			$huoniaoTag->assign('typeList', $typeList);

			$sql = $dsql->SetQuery("SELECT * FROM `#@__huangyelist` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			// print_r($ret);
			if($ret){
				$id = $ret[0]['id'];
				foreach ($ret[0] as $key => $value) {
					if($key == "pics"){
						$value = !empty($value) ? explode(",", $value) : array();
					}
					$huoniaoTag->assign('detail_'.$key, $value);
				}
				$lnglat  = $ret[0]['longitude'].",".$ret[0]['latitude'];
				$huoniaoTag->assign('detail_lnglat', $lnglat == "," ? "" : $lnglat);

				// 获取导航内容
				$navSql  = $dsql->SetQuery("SELECT *  FROM `#@__huangyenav` WHERE `aid` = ".$id." ORDER BY `weight` ASC");
				$navRet = $dsql->dsqlOper($navSql, "results");
				if(!$navRet){
					$navList = array();
				}else{
					$navList = $navRet;
				}

				$huoniaoTag->assign('navList', $navList);
			}else{
				$id = 0;
			}
			$huoniaoTag->assign('id', $id);
		}

		//家政 当前会员是否为派单人员
		global $installModuleArr;
		if($action == "order"){
			if(in_array('homemaking', $installModuleArr)){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__homemaking_personal` WHERE `userid` = $userid");
				$ret = $dsql->dsqlOper($sql, "results");
				$huoniaoTag->assign('personalId', $ret[0]['id'] ? 1 : 0);
			}
		}

		if($action == "order" && in_array('shop', $installModuleArr)){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_branch_store` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			$huoniaoTag->assign('branchStorelId', $ret[0]['id'] ? $ret[0]['id'] : 0);
		}



		//发布成功
		if($action == "fabusuccess"){
			require(HUONIAOINC."/config/refreshTop.inc.php");
			if(in_array($module, $installModuleArr)){
				if($module == "info"){
					$titleBlodlDay   = $cfg_info_titleBlodlDay;
					$titleBlodlPrice = $cfg_info_titleBlodlPrice;
					$titleRedDay     = $cfg_info_titleRedDay;
					$titleRedPrice   = $cfg_info_titleRedPrice;

					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__infolist` WHERE `id` = $id");
				}
				$ret = $dsql->dsqlOper($sql, "results");
				if(empty($ret)){
					$param = array(
						"service"  => "member",
						"type"     => "user",
						"action"   => "manage",
						"template" => $module
					);
					$url = getUrlPath($param);
					header("location:" . $url);
					die;
				}

				$huoniaoTag->assign('titleBlodlDay', $titleBlodlDay);
				$huoniaoTag->assign('titleBlodlPrice', $titleBlodlPrice);
				$huoniaoTag->assign('titleRedDay', $titleRedDay);
				$huoniaoTag->assign('titleRedPrice', $titleRedPrice);
			}
		}

		// 企业会员没有入驻此模块
		if($userinfo['userType'] == 2 && $module && $module != 'waimai' && $module != 'paotui'){

			if(!verifyModuleAuth(array("module" => $module))){


				$param = array(
					"service"  => "member",
					"template" => "module"
				);
				if(isMobile()){
					$param['template'] = 'appmanage';
				}
				$url = getUrlPath($param);

				$furl = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

				header("location:" . $url . "?furl=" . $furl);
				die;
			}
		}

		return;

	//商铺配置
	}elseif($action == "config"){

		$userLogin->checkUserIsLogin();
		$huoniaoTag->assign('module', $module);

        if($module == "business" && isMobile()){
            $url_ = GetCurUrl();
            $url = getUrlPath(array("service" => "member", "type" => "user", "template" => "business-config"));
            if(!strstr($url, $url_)){
                header("location:".$url);
                die;
            }
        }


		$contorllerFile = dirname(__FILE__).'/'.$module.'.controller.php';

		//获取图片配置参数
		require(HUONIAOINC."/config/".$module.".inc.php");
		$huoniaoTag->assign('atlasMax', $customAtlasMax);
		$huoniaoTag->assign('storeatlasMax', $custom_store_atlasMax);

		//汽车
		if($module == 'car'){
			// 获取商家模块公共配置
			$uid = $userLogin->getMemberID();

			//查询是否填写过入驻申请
			$sql = $dsql->SetQuery("SELECT `tag` FROM `#@__car_store` WHERE `userid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");

			$carHandlers = new handlers("car", "config");
			$carConfig   = $carHandlers->getHandle();
			$carConfig   = $carConfig['info'];

			$carTag_all = $carConfig['carTag'];
			$carTag_all_ = array();

			if(!empty($ret[0]['tag'])){
				$carTag_ = explode('|', $ret[0]['tag']);
			}else{
				$carTag_ = array();
			}
			foreach ($carTag_all as $v) {
				$carTag_all_[] = array(
					'name' => $v,
					'icon' => 'b_sertag_' . GetPinyin($v) . '.png',
					'active' => in_array($v, $carTag_) ? 1 : 0
				);
			}

			$huoniaoTag->assign('carTag_state', $carTag_all_);

		}elseif($module == 'travel'){//旅游
			$travelHandlers = new handlers($module, "module_type");
			$travelConfig   = $travelHandlers->getHandle();
			if($travelConfig['state'] == 100){
				$module_type = $travelConfig['info'];
				$huoniaoTag->assign('module_type', $module_type);
			}
		}elseif($module == 'pension'){// TODO 养老机构
			$pensionHandlers = new handlers($module, "pensionitem");
			$pensionConfig   = $pensionHandlers->getHandle(array("type"=>2));
			if($pensionConfig['state'] == 100){
				$jglx_type = $pensionConfig['info'];//print_R($jglx_type);exit;
				$huoniaoTag->assign('jglx_type', $jglx_type);
			}

			$pensionConfig   = $pensionHandlers->getHandle(array("type"=>5));
			if($pensionConfig['state'] == 100){
				$fwnr_type = $pensionConfig['info'];
				$huoniaoTag->assign('fwnr_type', $fwnr_type);
			}

			$pensionConfig   = $pensionHandlers->getHandle(array("type"=>1));
			if($pensionConfig['state'] == 100){
				$zgdx_type = $pensionConfig['info'];
				$huoniaoTag->assign('zgdx_type', $zgdx_type);
			}

			$pensionConfig   = $pensionHandlers->getHandle(array("type"=>3));
			if($pensionConfig['state'] == 100){
				$fjlx_type = $pensionConfig['info'];
				$huoniaoTag->assign('fjlx_type', $fjlx_type);
			}

			$pensionConfig   = $pensionHandlers->getHandle(array("type"=>4));
			if($pensionConfig['state'] == 100){
				$tsfw_type = $pensionConfig['info'];
				$huoniaoTag->assign('tsfw_type', $tsfw_type);
			}

		}

		if(file_exists($contorllerFile)){
			//声明以下均为接口类
			$handler = true;
			require_once($contorllerFile);

			$param = array(
				"action" => "storeDetail",
			);

            $module($param);


			// 获取商家公共配置
			$businessHandlers = new handlers("business", "storeDetail");
			$businessDetail = $businessHandlers->getHandle();

			if(is_array($businessDetail) && $businessDetail['state'] != 200){
				$businessDetail = $businessDetail['info'];
			}
			$huoniaoTag->assign('businessDetail', $businessDetail);

            if($module == "house"){
                $zjcom = 0;
                $sql = $dsql->SetQuery("SELECT * FROM `#@__house_zjcom` WHERE `userid` = $userid");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $zjcom = 1;
                }
                $huoniaoTag->assign("zjcom", $zjcom);
            }elseif($module == "car"){
				$zjcom = 0;
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__car_store` WHERE `userid` = $userid");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $zjcom = 1;
                }
                $huoniaoTag->assign("zjcom", $zjcom);
			}elseif($module == "education"){
				$zjcom = 0;
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__education_store` WHERE `userid` = $userid");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $zjcom = 1;
                }
                $huoniaoTag->assign("zjcom", $zjcom);
			}

			// 配置页面显示模板列表
			global $template;
			global $module;
			if($template == 'config' && $module == 'member'){
				//touch模板
				$dir = HUONIAOROOT . "/templates/website/";
				$floders = listDir($dir.'/touch');
				$skins = array();
				if(!empty($floders)){
					$i = 0;
					foreach($floders as $key => $floder){
						$config = $dir.'/touch/'.$floder.'/config.xml';
						if(file_exists($config)){
							//解析xml配置文件
							$xml = new DOMDocument();
							libxml_disable_entity_loader(false);
							$xml->load($config);
							$data = $xml->getElementsByTagName('Data')->item(0);
							$tplname = $data->getElementsByTagName("tplname")->item(0)->nodeValue;
							$copyright = $data->getElementsByTagName("copyright")->item(0)->nodeValue;

							$skins[$i]['tplname'] = $tplname;
							$skins[$i]['directory'] = $floder;
							$skins[$i]['copyright'] = $copyright;
							$i++;
						}
					}
				}
				$huoniaoTag->assign('touchTplList', $skins);
				$huoniaoTag->assign('touchTemplate', $customTouchTemplate);

			}

		}

	//店铺商品分类
	}elseif($action == "category"){

		$userLogin->checkUserIsLogin();
		$huoniaoTag->assign('module', $module);

		global $userLogin;
		$userid = $userLogin->getMemberID();
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$huoniaoTag->assign('storeid', $ret[0]['id']);
		}

		$detailHandels = new handlers($module, "category");
		$detailConfig  = $detailHandels->getHandle(array("son" => 1));

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				$huoniaoTag->assign('typeList', $detailConfig);

			}
		}
		return;

	//运费模板
	}elseif($action == "logistic"){

		$userLogin->checkUserIsLogin();
		$huoniaoTag->assign('module', $module);
		$huoniaoTag->assign('do', $do);
		$huoniaoTag->assign('id', (int)$id);

		$contorllerFile = dirname(__FILE__).'/'.$module.'.controller.php';
		if(file_exists($contorllerFile)){
			//声明以下均为接口类
			$handler = true;
			require_once($contorllerFile);

			if($id != 0){
				$param = array(
					"action" => "logisticDetail",
					"id"     => $id
				);
				$module($param);
			}else{
				$param = array(
					"action" => "logistic"
				);
				$module($param);
			}
		}
	// 获取会员列表
	}elseif($action == "memberList"){


	//首页
	}elseif($template == "index" || $template == "free"){

		//手机版首页不需要验证是否登录 by:20161231 guozi
		if(!isMobile()){
			$userLogin->checkUserIsLogin();
		}



		//获取自定义封面背景图片
		$userinfo = $userLogin->getMemberInfo();
		$userid = $userLogin->getMemberID();

		//移动端商家中心个人会员不得进入
		if($userinfo['userType'] == 1 && isMobile()){
			$userLogin->checkUserIsLogin();
		}

		$tempbg = $userinfo['tempbg'];
		if(!empty($tempbg)){
			$archives = $dsql->SetQuery("SELECT `big` FROM `#@__member_coverbg` WHERE `id` = ".$tempbg);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$huoniaoTag->assign('bannerUrl', getFilePath($results[0]['big']));
			}
		}

		//商家中心获取餐饮模块状态
		if($userinfo['userType'] == 2){

			$sql = $dsql->SetQuery("SELECT `id`, `diancan_state`, `dingzuo_state`, `paidui_state`, `maidan_state`, `bind_module` FROM `#@__business_list` WHERE `uid` = " . $userid);
			$res = $dsql->dsqlOper($sql, "results");
			if($res){
				$business = $res[0];

				$huoniaoTag->assign('diancan_state', $res[0]['diancan_state']);
				$huoniaoTag->assign('dingzuo_state', $res[0]['dingzuo_state']);
				$huoniaoTag->assign('paidui_state', $res[0]['paidui_state']);
				$huoniaoTag->assign('maidan_state', $res[0]['maidan_state']);
			}else{
				$business = "";
			}
		}

		$house_comid = 0;
		global $installModuleArr;
		if(in_array("house", $installModuleArr)){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__house_zjcom` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$house_comid = $ret[0]['id'];
			}
		}
		$huoniaoTag->assign('house_comid', $house_comid);

		$car_comid = 0;
		global $installModuleArr;
		if(in_array("car", $installModuleArr)){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__car_store` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$car_comid = $ret[0]['id'];
			}
		}
		$huoniaoTag->assign('car_comid', $car_comid);

		// 移动端商家首页获取可管理的模块
		if(isMobile()){
			global $ischeck;
			if(count($ischeck) > 1){

				if($business){
					$bind_module = $business['bind_module'] ? explode(',', $business['bind_module']) : array();
				}else{
					$bind_module = array();
				}

				$res = checkShowModule($bind_module, 'show', 'getConfig', 'getUrl');
				$showModule = $res['res'];
				$config = $res['config'];
				$huoniaoTag->assign('showModule', $showModule);
				$huoniaoTag->assign('businessConfig', $config);

				$huoniaoTag->assign('business', $business);
			}
		}

		// return;

	//其它需要验证登录的页面
	}elseif($template == "order"          		//管理订单
			 || $template == "record"		//交易记录
			 || $template == "message" 		//系统消息
			 || $template == "profile" 		//基本资料
			 || $template == "portrait"		//修改头像
			 // || $template == "connect"		//社交帐号绑定
			 || $template == "loginrecord"	//登录记录
			 || $template == "point"		//积分记录
			 || $template == "coupon"		//优惠券
			 || $template == "address"		//收货地址
			 || $template == "business-about"   //商家介绍
			 || $template == "business-news"    //商家动态
			 || $template == "business-albums"  //商家相册
			 || $template == "business-video"   //商家视频
			 || $template == "business-panor"   //商家全景
			 || $template == "business-comment" //商家点评
		 ){

		$userLogin->checkUserIsLogin();
		$huoniaoTag->assign('module', $module);

		//修改头像页输出头像尺寸
		if($template == 'portrait'){
			global $cfg_photoSmallWidth;
			global $cfg_photoSmallHeight;
			global $cfg_photoMiddleWidth;
			global $cfg_photoMiddleHeight;
			global $cfg_photoLargeWidth;
			global $cfg_photoLargeHeight;
			$huoniaoTag->assign('smallWidth', $cfg_photoSmallWidth);
			$huoniaoTag->assign('smallHeight', $cfg_photoSmallHeight);
			$huoniaoTag->assign('middleWidth', $cfg_photoMiddleWidth);
			$huoniaoTag->assign('middleHeight', $cfg_photoMiddleHeight);
			$huoniaoTag->assign('largeWidth', $cfg_photoLargeWidth);
			$huoniaoTag->assign('largeHeight', $cfg_photoLargeHeight);
		}

		//获取商家ID
		if($template == "business-comment"){
			global $userLogin;
			$userid = $userLogin->getMemberID();
		  $sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = " . $userid);
		  $res = $dsql->dsqlOper($sql, "results");
		  if($res){
			  $business = $res[0];
			  $huoniaoTag->assign('businessID', $business['id']);
		  }
	  	}

		if($template != "address") return;

	//提现
	}elseif($template == "withdraw" || $template == "bankCard" || $template == "alipay-record"){
		$userLogin->checkUserIsLogin();
		$uid = $userLogin->getMemberID();

		global $cfg_minWithdraw;  //起提金额
		global $cfg_maxWithdraw;  //最多提现
		global $cfg_withdrawFee;  //手续费
		global $cfg_withdrawCycle;  //提现周期  0不限制  1每周  2每月
		global $cfg_withdrawCycleWeek;  //周几
		global $cfg_withdrawCycleDay;  //几日
		global $cfg_withdrawPlatform;  //提现平台
		global $cfg_withdrawNote;  //提现说明

		$cfg_minWithdraw = (float)$cfg_minWithdraw;
		$cfg_maxWithdraw = (float)$cfg_maxWithdraw;
		$cfg_withdrawFee = (float)$cfg_withdrawFee;
		$cfg_withdrawCycle = (int)$cfg_withdrawCycle;
		$withdrawPlatform = $cfg_withdrawPlatform ? unserialize($cfg_withdrawPlatform) : array('weixin', 'alipay', 'bank');

		//提现周期
		$withdrawCycleState = 1;
		$withdrawCycleNote = '';
		if($cfg_withdrawCycle){
			//周几
			if($cfg_withdrawCycle == 1){

				$week = date("w", time());
				if($week != $cfg_withdrawCycleWeek){
					$array = $langData['siteConfig'][34][5];  //array('周日', '周一', '周二', '周三', '周四', '周五', '周六')
					$withdrawCycleState = 0;
					$withdrawCycleNote = str_replace('1', $array[$week], $langData['siteConfig'][36][0]);  //当前不可提现，提现时间：每周一
				}

			//几日
			}elseif($cfg_withdrawCycle == 2){

				$day = date("d", time());
				if($day != $cfg_withdrawCycleDay){
					$withdrawCycleState = 0;
					$withdrawCycleNote = str_replace('1', $cfg_withdrawCycleDay, $langData['siteConfig'][36][1]);  //当前不可提现，提现时间：每月1日
				}

			}
		}

		$huoniaoTag->assign("minWithdraw", $cfg_minWithdraw);
		$huoniaoTag->assign("maxWithdraw", $cfg_maxWithdraw);
		$huoniaoTag->assign("withdrawFee", $cfg_withdrawFee);
		$huoniaoTag->assign("withdrawCycleState", $withdrawCycleState);
		$huoniaoTag->assign("withdrawCycleNote", $withdrawCycleNote);
		$huoniaoTag->assign("withdrawPlatform", $withdrawPlatform);
		$huoniaoTag->assign("withdrawNote", nl2br($cfg_withdrawNote));

		//查询选用的帐号
		$id = (int)$id;
		$type = !empty($type) ? $type : ($withdrawPlatform ? $withdrawPlatform[0] : '');
		$bank = $alipay = array();
		if($id){
			$sql = $dsql->SetQuery("SELECT `bank`, `cardnum`, `cardname` FROM `#@__member_withdraw_card` WHERE `id` = $id AND `uid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret && is_array($ret)){
				if($ret[0]['bank'] != 'alipay'){
					$bank = $ret[0];
					$bank['cardnumLast'] = substr($bank['cardnum'], -4);
				}else{
					$type = "alipay";
					$alipay = $ret[0];
				}
			}
		}

		//提取第一个帐号
		if(empty($bank)){
			$sql = $dsql->SetQuery("SELECT `bank`, `cardnum`, `cardname` FROM `#@__member_withdraw_card` WHERE `uid` = $uid AND `bank` != 'alipay' AND `bank` != 'weixin' ORDER BY `id` DESC LIMIT 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret && is_array($ret)){
				$bank = $ret[0];
				$bank['cardnumLast'] = substr($bank['cardnum'], -4);
			}
		}
		if(empty($alipay)){
			$sql = $dsql->SetQuery("SELECT `bank`, `cardnum`, `cardname` FROM `#@__member_withdraw_card` WHERE `uid` = $uid AND `bank` = 'alipay' ORDER BY `id` DESC LIMIT 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret && is_array($ret)){
				$alipay = $ret[0];
			}
		}

		$huoniaoTag->assign("type", $type);
		$huoniaoTag->assign("bank", $bank);
		$huoniaoTag->assign("alipay", $alipay);
		$huoniaoTag->assign("new", $new);
		$huoniaoTag->assign("mod", $mod);


	//我的收藏
	}elseif($template == "collect"){
		$userLogin->checkUserIsLogin();
		$huoniaoTag->assign("module", $module);

	//收银结算
	}elseif($template == "checkout"){
		$userLogin->checkUserIsLogin();


	//打赏收益
	}elseif($template == "reward"){
		$userLogin->checkUserIsLogin();
		$uid = $userLogin->getMemberID();

		//计算打赏总收入
		$totalAmount = 0;
		$sql = $dsql->SetQuery("SELECT SUM(`amount`) totalAmount FROM `#@__member_reward` WHERE `state` = 1 AND `to` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$totalAmount = $ret[0]['totalAmount'];
		}

		//计算打赏总人数
		$totalCount = 0;
		$sql = $dsql->SetQuery("SELECT count(`id`) totalCount FROM `#@__member_reward` WHERE `state` = 1 AND `to` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$totalCount = $ret[0]['totalCount'];
		}

		$huoniaoTag->assign('totalMoney', sprintf("%.2f", $totalAmount));
		$huoniaoTag->assign('totalCount', (int)$totalCount);


	//帐户充值
	}elseif($template == "deposit" || $template == "convert"){

		$userLogin->checkUserIsLogin();

		$userinfo = $userLogin->getMemberInfo();
		$totalMoney = number_format($userinfo['money'], 2);
		$huoniaoTag->assign('totalMoney', $totalMoney);
		return;

	//安全中心
	}elseif($template == "security"){

		$userLogin->checkUserIsLogin();

		$huoniaoTag->assign('doget', $doget);

		//获取会员的安全保护问题
		$question1 = $question2 = "";
		$archives = $dsql->SetQuery("SELECT `question` FROM `#@__member_security` WHERE `uid` = '".$userLogin->getMemberID()."'");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$question = explode("$$", $results[0]['question']);
			$question1 = $question[0];
			$question2 = $question[1];
		}
		$huoniaoTag->assign('question1', $question1);
		$huoniaoTag->assign('question2', $question2);

		return;
	// 绑定第三方账号
	}elseif($template == "connect"){
		$userLogin->checkUserIsLogin();

		$sameConnData = GetCookie('sameConnData');
		DropCookie('sameConnData');
		$huoniaoTag->assign('sameConnData', $sameConnData);

	//发布举报
	}elseif($action == "complain"){

		if(!empty($_POST)){


		}else{
			$huoniaoTag->assign('module', $module);
			$huoniaoTag->assign('dopost', $dopost);
			$huoniaoTag->assign('aid', $aid);
			$huoniaoTag->assign('commonid', $commonid);
		}

	//邮箱绑定返回页面
	}elseif($template == "bindemail"){

		$userLogin->checkUserIsLogin();

		$state = 0;
		if(empty($data)){
			$content = $langData['siteConfig'][21][244];  //绑定失败，请检查链接地址是否完整！
		}else{

			//数据解密
			$mid = $userLogin->getMemberID();
			$RenrenCrypt = new RenrenCrypt();
			$data = $RenrenCrypt->php_decrypt(base64_decode($data));
			$arr = explode("$$", $data);
			$uid  = $arr[0];
			$ip   = $arr[1];
			$time = $arr[2];

			$param = array(
				"service"  => "member",
				"type"     => "user",
				"template" => "security",
				"doget"    => "chemail"
			);
			$bindUrl = getUrlPath($param);

			if(!is_numeric($uid) || !is_numeric($time)){
				$content = str_replace('1', $bindUrl, $langData['siteConfig'][21][245]);  //绑定失败，链接地址失效，请回到【<a href="1">绑定页面</a>】重新操作！
			}else{

				//判断是否同一帐号
				if($mid != $uid){
					$content = $langData['siteConfig'][21][246];  //绑定失败，请确认 <u>当前登录用户</u> 与 <u>邮箱链接中的用户</u> 是否一致！
				}else{

					//验证是否过期
					$now = GetMkTime(time());
					if($now - $time > 24 * 3600){
						$content = str_replace('1', $bindUrl, $langData['siteConfig'][21][247]);  //绑定失败，邮件链接已超过24小时的有效时间，请【<a href="1">重新绑定</a>】！
					}else{

						//验证会员
						$archives = $dsql->SetQuery("SELECT `id`, `emailCheck` FROM `#@__member` WHERE `id` = '$uid'");
						$user = $dsql->dsqlOper($archives, "results");
						if(!$user){
							$content = $langData['siteConfig'][21][248];  //绑定失败，会员不存在或已经删除，请确认后重试！
						}else{

							$state = 1;
							if($user[0]['emailCheck'] == 1){
								$content = $langData['siteConfig'][21][249];  //您已经成功绑定，无须再次提交！
							}else{

								//验证通过删除发送的验证码
								$archives = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'email' AND `lei` = 'bind' AND `by` = '$uid'");
								$dsql->dsqlOper($archives, "update");

								//更新用户状态
								$archives = $dsql->SetQuery("UPDATE `#@__member` SET `emailCheck` = 1 WHERE `id` = '$uid'");
								$dsql->dsqlOper($archives, "update");

								$content = $langData['siteConfig'][21][250];  //恭喜您，绑定成功！
							}

						}

					}
				}

			}

		}

		$huoniaoTag->assign('state', $state);
		$huoniaoTag->assign('content', $content);
		return;


	//重置密码
	}elseif($template == "resetpwd"){

		if(empty($data)){
			$huoniaoTag->assign("empty", "yes");
			return;
		}

		//验证安全链接
		$RenrenCrypt = new RenrenCrypt();
		$dataCode = $RenrenCrypt->php_decrypt(base64_decode($data));

		$dataArr = explode("$$", $dataCode);
		if(count($dataArr) != 5){
			$huoniaoTag->assign("empty", "yes");
			return;
		}
        if(empty($dataArr[0]) || empty($dataArr[4])){
            $huoniaoTag->assign("empty", "yes");
            return;
        }

		if($dataArr[0] == 1){
			$archives = $dsql->SetQuery("SELECT `id`, `password` FROM `#@__member` WHERE (`mtype` = 1 || `mtype` = 2) AND `email` = '".$dataArr[1]."'");
		}elseif($dataArr[0] == 2){
			$archives = $dsql->SetQuery("SELECT `id`, `password` FROM `#@__member` WHERE (`mtype` = 1 || `mtype` = 2) AND `phone` = '".$dataArr[1]."'");
		}
		$results  = $dsql->dsqlOper($archives, "results");
		if(!$results){
			$huoniaoTag->assign("empty", "yes");
			return;
		}
        $old = $results[0]['password'];

		$now = GetMkTime(time());
		if($now - $dataArr[3] > 24 * 3600){
			$huoniaoTag->assign("empty", "yes");
			return;
		}

        if(empty($old)){
            if($dataArr[4] != $dataArr[3]){
                $huoniaoTag->assign("empty", "yes");
                return;
            }
        }else{

            if($dataArr[4] != substr($old, 0, 10)){
                $huoniaoTag->assign("empty", "yes");
                return;
            }
        }

		$huoniaoTag->assign("data", $data);


	//提现详细信息
	}elseif($template == "withdraw_log_detail"){

		$userLogin->checkUserIsLogin();
		$userid = $userLogin->getMemberID();

		if(empty($id)){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
			die;
		}

        if($type == "p"){
		    $sql = $dsql->SetQuery("SELECT * FROM `#@__member_putforward` WHERE `id` = $id AND `userid` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){

                $results = $ret[0];
                $huoniaoTag->assign('tab', 'p');
                $huoniaoTag->assign('bank', $results['bank']);
                $huoniaoTag->assign('type', $results['type']);
                $huoniaoTag->assign('order_id', $results['order_id']);
                $huoniaoTag->assign('cardname', $results['cardname']);
                $huoniaoTag->assign('amount', $results['amount']);
                $huoniaoTag->assign('pubdate', date("Y-m-d H:i:s", $results['pubdate']));
                $huoniaoTag->assign('paydate', $results['paydate'] ? date("Y-m-d H:i:s", $results['paydate']) : "");
                $huoniaoTag->assign('state', $results['state']);
                $huoniaoTag->assign('note', $results['note']);

            }else{
                header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
                die;
            }
        }else{
            $sql = $dsql->SetQuery("SELECT * FROM `#@__member_withdraw` WHERE `id` = $id AND `uid` = $userid");
    		$ret = $dsql->dsqlOper($sql, "results");
    		if($ret){

    			$results = $ret[0];
                $huoniaoTag->assign('tab', 'w');
    			$huoniaoTag->assign('bank', $results['bank']);
    			if($results['bank'] == "alipay"){
    				$huoniaoTag->assign('cardnum', $results['cardnum']);
    			}else{
    				$cardnum = str_split($results['cardnum'], 4);
    				$huoniaoTag->assign('cardnum', join(" ", $cardnum));
    			}
    			$huoniaoTag->assign('cardname', $results['cardname']);
    			$huoniaoTag->assign('amount', $results['amount']);
    			$huoniaoTag->assign('tdate', date("Y-m-d H:i:s", $results['tdate']));
    			$huoniaoTag->assign('state', $results['state']);
    			$huoniaoTag->assign('note', $results['note']);
    			$huoniaoTag->assign('rdate', date("Y-m-d H:i:s", $results['rdate']));

    		}else{
    			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
    			die;
    		}
        }

		return;


	//订单详情页面
	}elseif($action == "orderdetail"){

		global $userLogin;
		global $cfg_thumbType;
		global $cfg_atlasType;
		$userid = $userLogin->getMemberID();

		if($userid == -1){
			$furl = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			header("location:".$cfg_secureAccess.$cfg_basehost."/login.html?furl=".$furl);
		}else{

			$huoniaoTag->assign('module', $module);

			if($module == "business"){
				$huoniaoTag->assign('type', $type);
				$act = $type."OrderDetail";
			}elseif($module == "paotui"){
				$module = "waimai";
				$act = "orderPaotuiDetail";
			}else{
				$act = "orderDetail";
			}
            // 退款通知买家时，消息通知跳转链接为商家会员中心，所以这里加个验证跳转
            if($module == "info"){
                global $tpl;
                if(strstr($tpl, 'company')){
                    $userinfo = $userLogin->getMemberInfo();
                    if($userinfo['userType'] == 1){
                        $paramBusi = array(
                            "service"  => "member",
                            "template" => "orderdetail",
                            "action"   => "info",
                            "type"   => "user",
                            "id"       => $id,
                            "param"       => 'type=out'
                        );
                        $url = getUrlPath($paramBusi);
                    }
                }
            }

			$detailHandels = new handlers($module, $act);
			$detailConfig  = $detailHandels->getHandle($id);
			if(is_array($detailConfig) && $detailConfig['state'] == 100){
				$detailConfig  = $detailConfig['info'];
				if(is_array($detailConfig)){

					$param = array("service"  => "member");
			        $busiDomain = getUrlPath($param);     //商家会员域名

					global $cfg_secureAccess;
					$currentPageUrl = $cfg_secureAccess . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

					//团购券类型的订单直接跳转至已消费团购券列表
					if($module == 'tuan' && !$detailConfig['product']['tuantype'] && strstr($currentPageUrl, $busiDomain)){
						$paramBusi = array(
                            "service"  => "member",
                            "template" => "quan",
                            "action"   => "tuan",
                            "param"    => 'ordernum=' . $detailConfig['ordernum']
                        );
                        $url = getUrlPath($paramBusi);
						header("location:".$url);
						die;
					}

					foreach ($detailConfig as $key => $value) {
						$huoniaoTag->assign('detail_'.$key, $value);
					}


					//已完成订单输出上传配置参数
					// if($key == "orderstate" && $value == 3){
						//获取图片配置参数
						require(HUONIAOINC."/config/".$module.".inc.php");

						if($customUpload == 1){
							$huoniaoTag->assign('thumbSize', $custom_thumbSize);
							$huoniaoTag->assign('thumbType', str_replace("|", ",", $custom_thumbType));
							$huoniaoTag->assign('atlasSize', $custom_atlasSize);
							$huoniaoTag->assign('atlasType', str_replace("|", ",", $custom_atlasType));
						}else{
							$huoniaoTag->assign('thumbType', str_replace("|", ",", $cfg_thumbType));
							$huoniaoTag->assign('atlasType', str_replace("|", ",", $cfg_atlasType));
						}
						$huoniaoTag->assign('atlasMax', (int)$customAtlasMax);
					// }

				}

				if($module == 'shop'){
					$sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__shop_branch_store` WHERE `userid` = $userid");
					$res = $dsql->dsqlOper($sql, "results");
					if($res){
						$branchStoreId = $res[0]['id'];
					}
				}


				$huoniaoTag->assign('rates', (int)$rates);
				$huoniaoTag->assign('type', $type);
				$huoniaoTag->assign('branch', (int)$branch);
				$huoniaoTag->assign('branchStoreId', (int)$branchStoreId);
				$huoniaoTag->assign('id', (int)$id);


			}else{
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
			}
		}
		return;

	//评价
	}elseif($action == "write-comment"){

		global $userLogin;
		global $cfg_thumbType;
		global $cfg_atlasType;
		$userid = $userLogin->getMemberID();

		if($userid == -1){
			$furl = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			header("location:".$cfg_secureAccess.$cfg_basehost."/login.html?furl=".$furl);
		}else{

			$huoniaoTag->assign('module', $module);
			$huoniaoTag->assign('id', (int)$id);

			//获取图片配置参数
			require(HUONIAOINC."/config/".$module.".inc.php");

			if($customUpload == 1){
				$huoniaoTag->assign('thumbSize', $custom_thumbSize);
				$huoniaoTag->assign('thumbType', str_replace("|", ",", $custom_thumbType));
				$huoniaoTag->assign('atlasSize', $custom_atlasSize);
				$huoniaoTag->assign('atlasType', str_replace("|", ",", $custom_atlasType));
			}else{
				$huoniaoTag->assign('thumbType', str_replace("|", ",", $cfg_thumbType));
				$huoniaoTag->assign('atlasType', str_replace("|", ",", $cfg_atlasType));
			}

			if($module == 'waimai'){
				$ordertype = empty($ordertype) || $ordertype != "paotui" ? "waimai" : "paotui";
				if($ordertype == "paotui"){
					$detailHandels = new handlers($module, "orderPaotuiDetail");
				}else{
					$detailHandels = new handlers($module, "orderDetail");
				}
			}else{
				$detailHandels = new handlers($module, "orderDetail");
			}

			$detailConfig  = $detailHandels->getHandle($id);

			if(is_array($detailConfig) && $detailConfig['state'] == 100){
				$detailConfig  = $detailConfig['info'];
				if(is_array($detailConfig)){

					// 区分外卖
					if($module == 'waimai'){
						$type = $ordertype == "paotui" ? 1 : 0;
						// 修改评论使用
						// $sql = $dsql->SetQuery("SELECT * FROM `#@__waimai_common` WHERE `oid` = $id AND `uid` = $userid AND `type` = $type");
						$type_ = $ordertype == "paotui" ? 'paotui-order' : 'waimai-order';
						$sql = $dsql->SetQuery("SELECT * FROM `#@__public_comment` WHERE `userid` = '$userid' AND `oid` = '$id' AND `type` = '$type_' AND `pid` = 0");
						$ret = $dsql->dsqlOper($sql, 'results');
						$common = array();
						if($ret){
							$pics = $ret[0]['pics'];
							if($pics != "") $ret[0]['pics'] = explode(",", $pics);
							$common = $ret[0];
						}else{
							$common = array("id" => "", "isanony" => 0, "star" => 0, "starps" => 0, "content" => "", "contentps" => "", "litpic" => "");
						}
						$huoniaoTag->assign('common', $common);
						$huoniaoTag->assign('ordertype', $ordertype);

					}else{

						if($detailConfig['orderstate'] == 3){

							$huoniaoTag->assign('product', $detailConfig['product']);

						}else{

							$param = array(
								"service"  => "member",
								"type"     => "user",
								"template" => "orderdetail",
								"module"   => "shop",
								"id"       => $id
							);

							header("location:".getUrlPath($param));
						}

					}

				}
			}else{
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
			}


		}
		return;

	//招聘求职
	}elseif($action == "job"){

		$userLogin->checkUserIsLogin();
		$huoniaoTag->assign('module', $module);

		//简历
		if($module == "resume"){
			global $cfg_photoSize;
			global $cfg_photoType;
			$huoniaoTag->assign('photoSize', $cfg_photoSize);
			$huoniaoTag->assign('photoType', str_replace("|", ",", $cfg_photoType));

			$detailHandels = new handlers($action, "resumeDetail");
			$detailConfig  = $detailHandels->getHandle();

			if(is_array($detailConfig) && $detailConfig['state'] == 100){
				$detailConfig  = $detailConfig['info'];
				if(is_array($detailConfig)){

					foreach ($detailConfig as $key => $value) {
						$huoniaoTag->assign('detail_'.$key, $value);
					}
				}
			}

		}


	//外卖菜单
	}elseif($action == "waimai-menus" || $action == "waimai-albums" || $action == "waimai-albums-add"){

		$userLogin->checkUserIsLogin();
		$huoniaoTag->assign('module', $module);

		global $userLogin;
		$userid = $userLogin->getMemberID();
		$storeid = 0;
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__waimai_store` WHERE `userid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$storeid = $ret[0]['id'];
		}
		$huoniaoTag->assign('storeid', $storeid);
		return;

	//活动报名
	}elseif($action == "huodong-reg"){
		$userLogin->checkUserIsLogin();

		$id = (int)$id;
		$huoniaoTag->assign("id", $id);

	//自助建站设计
	}elseif($action == "dressup-website"){
		$userLogin->checkUserIsLogin();
		$userid = $userLogin->getMemberID();

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__website` WHERE `state` = 1 AND `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			$param = array(
				"service"  => "member",
				"template" => "config",
				"action"   => "website"
			);
			header('location:'.getUrlPath($param));
		}

		$site = $userResult[0]['id'];
		$huoniaoTag->assign('PROJECTID', $site);

	//我参与的活动
	}elseif($action == "huodong-join"){
		$userLogin->checkUserIsLogin();

	// 第三方登陆用户绑定手机号
	}elseif($action == "bindMobile"){

		if($userLogin->getMemberID() > -1){
			header("location:".$cfg_secureAccess.$cfg_basehost);
		}

		$uid = GetCookie("connect_uid");
		if(empty($uid)){
			header("location:".$cfg_secureAccess.$cfg_basehost);
		}

		if(empty($type)){
			header("location:/404.html");
		}

		$url = isset($_SESSION['loginRedirect']) ? $_SESSION['loginRedirect'] : "";
		if(strstr($url, "logout.html") || strstr($url, "login.html") || strstr($url, "registerCheck") !== FALSE){
			$url = "";
		}
		$huoniaoTag->assign('code', $type);
		$huoniaoTag->assign('loginRedirect', $url);


	//系统版本信息
	}elseif($action == "version"){

		$sql = $dsql->SetQuery("SELECT `logo`, `android_version`, `ios_version`, `android_download`, `ios_download` FROM `#@__app_config` LIMIT 1");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$data = $ret[0];

			$huoniaoTag->assign('logo', getFilePath($data['logo']));
			$huoniaoTag->assign('android_version', $data['android_version']);
			$huoniaoTag->assign('ios_version', $data['ios_version']);
			$huoniaoTag->assign('android_download', $data['android_download']);
			$huoniaoTag->assign('ios_download', $data['ios_download']);
		}

	//会员升级 20170725
	}elseif($action == "upgrade"){
		$userLogin->checkUserIsLogin();

	}elseif($action == "upgrade-pay"){

		$userLogin->checkUserIsLogin();

		$param = array(
			"service"  => "member",
			"type"     => "user",
			"template" => "upgrade"
		);
		$upgradeUrl = getUrlPath($param);

		if(empty($level) || empty($day) || empty($daytype)){
			header("location:".$upgradeUrl);
			die;
		}

		//验证是否合法
		$sql = $dsql->SetQuery("SELECT `name`, `cost` FROM `#@__member_level` WHERE `id` = $level");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$name = $ret[0]['name'];
			$_day = $price = 0;
			$_daytype = "";
			$cost = !empty($ret[0]['cost']) ? unserialize($ret[0]['cost']) : array();

			if($cost){
				foreach ($cost as $key => $value) {
					if($value['day'] == $day && $value['daytype'] == $daytype){
						$_day = $day;
						$_daytype = $value['daytype'];
						$price = $value['price'];
					}
				}

				if(empty($_day)){
					header("location:".$upgradeUrl);
					die;
				}

				$huoniaoTag->assign('name', $name);
				$huoniaoTag->assign('level', $level);
				$huoniaoTag->assign('day', $_day);
				$huoniaoTag->assign('daytype', $_daytype);
				$huoniaoTag->assign('price', $price);

			}else{
				header("location:".$upgradeUrl);
				die;
			}

		}else{
			header("location:".$upgradeUrl);
			die;
		}

	//商家入驻 20170803
	//手机版有入驻前的介绍页面，电脑版没有，所以需要增加以下判断
	// 20180815
	// 电脑版也进入enter-upload页面
	// }elseif($action == "enter"){

	// 	$param = array(
	// 		"service"  => "member",
	// 		"type"     => "user",
	// 		"template" => "enter-upload"
	// 	);
	// 	header("location:" . getUrlPath($param));
	// 	die;

	//填写入驻资料
	}elseif($action == "enter-upload" || strpos($action, "business-config") !== false || strpos($action, "business-custom") !== false){

        // 移动端跳到个人中心
        if($action == "business-config"){
            if(isMobile()){
                $url_ = GetCurUrl();
                $url = getUrlPath(array("service" => "member", "type" => "user", "template" => $action));
                if(!strstr($url, $url_)){
                    header("location:".$url);
                    die;
                }
            }
        }

		$userLogin->checkUserIsLogin();

		//查询当前登录会员是否已经填写过资料
		//如果已经填写过，直接跳转至选择开通模块页
		//如果已经付过钱，需要跳转到等待审核页面
		//如果已经入驻成功，跳转到成功页面
		$uid = $userLogin->getMemberID();

		//查询是否填写过入驻申请
		$sql = $dsql->SetQuery("SELECT * FROM `#@__business_list` WHERE `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$state = $ret[0]['state'];

			// 更新会员状态为企业会员,如果后台将此会员类型设为普通会员，会导致页面无法访问
			// $sql = $dsql->SetQuery("UPDATE `#@__member` SET `mtype` = 2 WHERE `id` = $uid AND `mtype` = 1");
			// $dsql->dsqlOper($sql, "update");

			$sql = $dsql->SetQuery("SELECT `mtype` FROM `#@__member` WHERE `id` = $uid");
			$ret_ = $dsql->dsqlOper($sql, "results");
			// 待审核或待支付
			if($ret_[0]['mtype'] != 2 && $state == 4){
				$param = array(
					"service" => "member",
					"type" => "user",
				);
				$url = getUrlPath($param);
				die('<meta charset="UTF-8"><script type="text/javascript">alert("'.$langData['siteConfig'][33][51].'");top.location="'.$url.'";</script>');//您的帐号会员类型异常，请联系管理员！
			}
			if($ret[0]['state'] == 4){
				$param = array(
					"service" => "member",
					"type" => "user",
				);
				$url = getUrlPath($param);
				die('<meta charset="UTF-8"><script type="text/javascript">alert("'.$langData['siteConfig'][21][181].'");top.location="'.$url.'";</script>');//您还没有入驻商家！
			}
			foreach ($ret[0] as $key => $value) {
				if($key == "addrid"){
					$value = empty($value) ? "" : $value;
				}
				if($key == "tel" || $key == "qq" || $key == "email"){
					$value_ = empty($value) ? array() : explode(",", $value);
					$huoniaoTag->assign($key.'Arr', $value_);
				}
				if($key == "logo" || $key == "wechatqr" || $key == "video_pic" || $key == "mappic"){
					$huoniaoTag->assign($key.'Source', $value);
					$value = $value ? getFilePath($value) : "";
				}
				if(($key == "banner" || $key == "video" || $key == "qj_file") && $value){
					$source = explode(',', $value);
					$res = array();
					foreach ($source as $k => $v) {
						$res[$k]['path'] = getFilePath($v);
						$res[$k]['source'] = $v;
					}
					$huoniaoTag->assign($key.'Arr', $res);
				}
				if($key == "custom_nav"){
					$custom_navArr = array();
					if($value){
						$value_ = explode("|", $value);
						foreach ($value_ as $k => $v) {
							$d = explode(',', $v);
							$custom_navArr[$k] = array(
								'icon' => $d[0],
								'iconSource' => getFilePath($d[0]),
								'title' => $d[1],
								'url' => $d[2],
							);
						}
					}
					$huoniaoTag->assign('custom_navArr', $custom_navArr);
				}

				if($key == "circle"){
					$circle_ids = array();
					$circleName = array();
					if($value){
						if($ret[0]['addrid']){
							$sql = $dsql->SetQuery("SELECT * FROM `#@__site_city_circle` WHERE `id` IN (".$value.") ORDER BY `id`");
							$res = $dsql->dsqlOper($sql, "results");
							if($res){
								foreach ($res as $k => $v) {
									if($v['qid'] == $ret[0]['addrid']){
										$circle_ids[] = $v['id'];
										$circleName[] = $v['name'];
									}
								}
							}
						}
						$value_ = join(",", $circle_ids);
						if($value_ != $value){
							$sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `circle` = '$value_' WHERE `uid` = $uid");
							$dsql->dsqlOper($sql, "update");
						}
					}
					$circle_ids = $value ? explode(",", $value) : array();
					$huoniaoTag->assign('circle_ids', $circle_ids);
					$huoniaoTag->assign('circleName', join(" ", $circleName));

					if($value){
						$sql = $dsql->SetQuery("SELECT * FROM `#@__site_city_circle` WHERE `id` IN (".$value.")");
						$res = $dsql->dsqlOper($sql, "results");
					}
				}

				if($key == "weeks"){
					$weekDay = "";

					if($value){
						$value_ = explode(",", $value);
						$weeks = $langData['siteConfig'][34][5];
						if(count($value_) == 1){
							$weekDay = $value_[0];
						}else{
							$value_t = array();
							foreach ($value_ as $k => $v) {
								if($k == 0){
									$value_t[0] = $weeks[$v-1];
								}
								if($k > 0 && $k + 1 == count($value_)){
									$value_t[1] = $weeks[$v-1];
								}
								if($k > 0 && $v - $value_[$k-1] > 1){
									$value_t[0] = $weeks[$v-1];
									$value_t[1] = $weeks[$value_[0]-1];
									break;
								}
							}
							$weekDay = $value_t[0] . $langData['siteConfig'][13][7] . $value_t[1];//至
						}
					}
					$huoniaoTag->assign('weekDay', $weekDay);

				}

				if($key == "opentime"){
					$value = str_replace(";", "-", $value);
				}

				if($key == "tag_shop"){
					$huoniaoTag->assign('tag_shopArr', $value ? explode("|", $value) : array());
				}

				$huoniaoTag->assign($key, $value);
			}

			// 输出自定义菜单
			if($action == "business-config_custom" || $action == "business-custom_menu"){
				$sql = $dsql->SetQuery("SELECT * FROM `#@__business_menu` WHERE `uid` = $uid ORDER BY `weight`, `id`");
				$res = $dsql->dsqlOper($sql, "results");

				$huoniaoTag->assign('menuList', $res);
			}

			// 获取商家模块公共配置
			$businessHandlers = new handlers("business", "config");
			$businessConfig  = $businessHandlers->getHandle();
			$businessConfig = $businessConfig['info'];

			$businessTag_all = $businessConfig['businessTag'];
			$businessTag_all_ = array();

			$businessTag = $ret[0]['tag'];
			if($businessTag){
				$businessTag_ = explode('|', $businessTag);
			}else{
				$businessTag_ = array();
			}
			foreach ($businessTag_all as $v) {
				$businessTag_all_[] = array(
					'name' => $v,
					'icon' => 'b_sertag_' . GetPinyin($v) . '.png',
					'active' => in_array($v, $businessTag_) ? 1 : 0
				);
			}

			$huoniaoTag->assign('businessTag_state', $businessTag_all_);

		}else{
			$userinfo = $userLogin->getMemberInfo();
			if($userinfo['userType'] == 1){
				$param = array(
					"service" => "member",
					"type" => "user",
					"template" => "enter"
				);
			}else{
				$param = array(
					"service" => "member",
				);
			}
			$url = getUrlPath($param);
			header("location:".$url);
		}


	//选择要开通的模块
	//选择开通年限&选择支付方式
	}elseif($action == "enter-review"){

		$userLogin->checkUserIsLogin();
		$uid = $userLogin->getMemberID();

		//查询是否填写过入驻申请
		$sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__business_list` WHERE `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$id = $ret[0]['id'];
			$state = $ret[0]['state'];

			//状态为3时表示已经支付完成，待审核
			if($state != 3){
				$param = array(
					"service"  => "member",
					"type" => "user"
				);
				header("location:" . getUrlPath($param));
				die;
			}

		//没填写过的，跳转到首页
		}else{
			header("location:" . $cfg_secureAccess.$cfg_basehost);
		}


	//保障金
	}elseif($action == "promotion" || $action == "extract" || $action == "payment"){

		$userLogin->checkUserIsLogin();

		$huoniaoTag->assign("type", $type);

		//查询可提取的保障金 = 一年前的缴纳总额 - 已提取总额
		$totalPromotion = $alreadyExtract = 0;
		$uid = $userLogin->getMemberID();
		$yearAgo = GetMkTime(date("Y-m-d H:i:s", strtotime("-1 year")));
		$sql = $dsql->SetQuery("SELECT SUM(`amount`) as total FROM `#@__member_promotion` WHERE `type` = 1 AND `uid` = $uid AND `date` < $yearAgo");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$totalPromotion = $ret[0]['total'];
		}
		$sql = $dsql->SetQuery("SELECT SUM(`amount`) as total FROM `#@__member_promotion` WHERE `type` = 0 AND `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$alreadyExtract = $ret[0]['total'];
		}
		$huoniaoTag->assign("extract", sprintf('%.2f', ($totalPromotion - $alreadyExtract)));


	//模块管理
	}elseif($action == "module"){

		$userLogin->checkUserIsLogin();

		$huoniaoTag->assign("type", (int)$type);
		$huoniaoTag->assign("state", $state);

		$url = $furl ? urldecode($furl) : $_SERVER['HTTP_REFERER'];
		$huoniaoTag->assign("url", $url);

	//商家服务-配置
	}elseif($action == "business-service"){
		$userLogin->checkUserIsLogin();

		$param = array("type" => $type);
		$serviceHandel = new handlers("business", "serviceConfig");
		$serviceConfig  = $serviceHandel->getHandle($param);
		if(is_array($serviceConfig) && $serviceConfig['state'] == 100){
			$serviceConfig  = $serviceConfig['info'];
			if(is_array($serviceConfig)){
				foreach ($serviceConfig as $key => $value) {
					$huoniaoTag->assign($key, $value);
				}
			}
		}

		$huoniaoTag->assign('type', $type);

	//商家服务-订单内标
	}elseif($action == "business-service-order"){
		$userLogin->checkUserIsLogin();
		$huoniaoTag->assign('type', $type);
		$huoniaoTag->assign('state', $state);

	//商家点餐-商品
	}elseif($action == "business-service-list"){

		$userLogin->checkUserIsLogin();
		$uid = $userLogin->getMemberID();

		if($type == "diancan"){

			$param = array(
				"service" => "member",
				"template" => $action
			);
			$url = getUrlPath($param);

			if($dopost == "edit" || $dopost == "add"){
				$id = (int)$id;
				$huoniaoTag->assign('pics', '[]');
				if($dopost == "edit"){
					if(empty($id)){
						header("location:$url");
						return;
					}else{
						//获取信息内容
				        $sql = $dsql->SetQuery("SELECT * FROM `#@__business_diancan_list` WHERE `id` = $id AND `uid` = $uid");
				        $ret = $dsql->dsqlOper($sql, "results");
				        if($ret){

				            foreach ($ret[0] as $key => $value) {

				                //商品属性
				                if($key == "nature"){
				                    $value = unserialize($value);
				                }

				                //图片
				                if($key == "pics"){
				                    $value = !empty($value) ? json_encode(explode(",", $value)) : "[]";
				                }

				                $huoniaoTag->assign($key, $value);
				            }

				        }else{
				            header("location:$url");
							return;
				        }
					}
				}
				$huoniaoTag->assign("id", $id);
			}else{
				$huoniaoTag->assign("title", $title);
				$huoniaoTag->assign("typename", $typename);
				$huoniaoTag->assign("typeid", (int)$typeid);
			}

		}elseif($type == 'dingzuo'){

			$detailHandels = new handlers('business', "dingzuoCategory");
			$detailConfig  = $detailHandels->getHandle(array("son" => 1, "tab" => $dopost));

			if(is_array($detailConfig) && $detailConfig['state'] == 100){
				$detailConfig  = $detailConfig['info'];
				if(is_array($detailConfig)){

					$huoniaoTag->assign('typeList', $detailConfig);

				}
			}
		}


		$huoniaoTag->assign("type", $type);
		$huoniaoTag->assign("dopost", $dopost);


	//商家服务-订单列表
	}elseif($action == "business-diancan-order" || $action == "business-dingzuo-order" || $action == "business-paidui-order" || $action == "business-maidan-order"){
		$userLogin->checkUserIsLogin();

		$huoniaoTag->assign("type", $type);


	//会员主页
	}elseif($action == "user" || $action == "user_fans" || $action == "user_follow" || $action == "user_visitor" || $action == "user_message"){
		$id = (int)$id;
		//查询用户信息
		$sql = $dsql->SetQuery("SELECT m.`nickname`, m.`mtype`, m.`company`, m.`level`, m.`photo`, l.`name` level_name, l.`icon` level_icon, a.`typename` addr, count(mes.`id`) totalMessage, (SELECT count(`id`) FROM `#@__member_follow` WHERE `fid` = $id) as totalFans, (SELECT count(`id`) FROM `#@__member_follow` WHERE `tid` = $id) as totalFollow FROM `#@__member` m LEFT JOIN `#@__member_level` l ON l.`id` = m.`level` LEFT JOIN `#@__site_area` a ON a.`id` = m.`addr` LEFT JOIN `#@__member_message` mes ON mes.`tid` = m.`id` WHERE m.`id` = $id");

		$ret = $dsql->dsqlOper($sql, "results");
		if($ret && is_array($ret)){

			$data       = $ret[0];
			$nickname   = $data['mtype'] == 2 ? (!empty($data['company']) ? $data['company']: $data['nickname']) : $data['nickname'];
			$level      = $data['level'];
			$photo      = !empty($data['photo']) ? getFilePath($data['photo']) : "";
			$addr       = $data['addr'];
			$level_name = $data['level_name'];
			$level_icon = !empty($data['level_icon']) ? getFilePath($data['level_icon']) : "";
			$totalMessage = (int)$data['totalMessage'];
			$totalFans    = (int)$data['totalFans'];
			$totalFollow  = (int)$data['totalFollow'];

			$huoniaoTag->assign('id', $id);
			$huoniaoTag->assign('nickname', $nickname);
			$huoniaoTag->assign('photo', $photo);
			$huoniaoTag->assign('level', $level);
			$huoniaoTag->assign('level_name', $level_name);
			$huoniaoTag->assign('level_icon', $level_icon);
			$huoniaoTag->assign('addr', $addr);
			$huoniaoTag->assign('totalMessage', $totalMessage);
			$huoniaoTag->assign('totalFans', $totalFans);
			$huoniaoTag->assign('totalFollow', $totalFollow);

			$huoniaoTag->assign('action', $action);

			//判断当前登录会员是否已经关注过要访问的会员
			$userid = $userLogin->getMemberID();
			if($userid > 0){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $userid AND `fid` = $id");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret && is_array($ret)){
					$huoniaoTag->assign('isfollow', 1);
				}

				//记录浏览记录
				if($userid != $id){
					$time = time();
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_visitor` WHERE `uid` = $userid AND `tid` = $id");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						//浏览过的更新时间
						$sql = $dsql->SetQuery("UPDATE `#@__member_visitor` SET `date` = $time WHERE `uid` = $userid AND `tid` = id");
						$dsql->dsqlOper($sql, "update");
					}else{
						//新增记录
						$sql = $dsql->SetQuery("INSERT INTO `#@__member_visitor` (`uid`, `tid`, `date`) VALUES ('$userid', '$id', '$time')");
						$dsql->dsqlOper($sql, "update");
					}
				}

			}

			//简历地址
			global $installModuleArr;
			if(in_array("job", $installModuleArr)){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__job_resume` WHERE `userid` = $id");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret && is_array($ret)){
					$resumeid = $ret[0]['id'];
					$param = array(
						"service" => "job",
						"template" => "resume",
						"id" => $resumeid
					);
					$resumeUrl = getUrlPath($param);
					$huoniaoTag->assign("resumeUrl", $resumeUrl);
				}
			}

			//交友地址
			global $installModuleArr;
			if(in_array("dating", $installModuleArr)){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $id");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret && is_array($ret)){
					$datingid = $ret[0]['id'];
					$param = array(
						"service" => "dating",
						"template" => "u",
						"id" => $datingid
					);
					$datingUrl = getUrlPath($param);
					$huoniaoTag->assign("datingUrl", $datingUrl);
				}
			}


		}else{
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
			die;
		}

	//签到
	}elseif($action == "qiandao"){

		$userLogin->checkUserIsLogin();
		$uid = $userLogin->getMemberID();

		//统计登录会员总签到天数
		$totalQiandao = 0;
		$sql = $dsql->SetQuery("SELECT `id`, `date` FROM `#@__member_qiandao` WHERE `uid` = $uid ORDER BY `date` DESC");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$totalQiandao = count($ret);
		}
		$huoniaoTag->assign("totalQiandao", $totalQiandao);

		//统计连续签到天数
		$dateArr = array();
		if($ret){
			foreach ($ret as $key => $value) {
				array_push($dateArr, $value['date']);
			}
		}
		$huoniaoTag->assign("totalLianqian", getContinueDay($dateArr));

		//判断是否已经签到
		$todayQiandao = 0;
		if($ret){
			$lastQiandao = GetMkTime(date("Y-m-d", $ret[0]['date']));
			$today = GetMkTime(date("Y-m-d", time()));

			if($lastQiandao == $today){
				$todayQiandao = 1;
			}
		}
		$huoniaoTag->assign("todayQiandao", $todayQiandao);

		global $cfg_qiandao_state;
		if(!$cfg_qiandao_state){
			die($langData['siteConfig'][22][127]);  //签到功能未开启！
		}

	}elseif($action == "verify-tuan"){
		$cardnum = $_GET['cardnum'];
		$cardnum = empty($cardnum) ? array("") : explode(',',$cardnum);
		$huoniaoTag->assign('cardnum', $cardnum);

	}elseif($action == "livedetail"){
        $huoniaoTag->assign('id', $id);
        global $userLogin;
        global $dsql;
        global $cfg_secureAccess;
        global $cfg_basehost;
        $userid = $userLogin->getMemberID();
        if($userid==-1){
            header("location:".$cfg_basehost."/login.html");
        }
        //$sql = $dsql->SetQuery("SELECT `title`,`click`,`litpic`,`ftime`,`typeid`,`catid`,`flow`,`way`,`pushurl` FROM `huoniao_livelist` where id =(SELECT max(`id`) i FROM `#@__livelist` where user='$userid')");//
        $sql = $dsql->SetQuery("SELECT `id`,`up`,`title`,`way`,`streamname`,`starttime`,`state`,`click`,`livetime`,`litpic`,`password`,`startmoney`,`endmoney`,`ftime`,`typeid`,`catid`,`flow`,`way`,`pushurl`,`pullurl_pc`,`pullurl_touch`,`pulltype`, `arcrank` FROM `#@__livelist` where id='$id' and user='$userid'");
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            //播放地址
            $id = $res[0]['id'];
            $PulldetailHandels = new handlers('live', "getPullSteam");
            $param=array('id'=>$id,'type'=>'m3u8');
            $PulldetailConfig  = $PulldetailHandels->getHandle($param);
            if($PulldetailConfig['state']==100){
                $huoniaoTag->assign('pullUrl', $PulldetailConfig['info']);
            }
            if(isMobile()){
                if($res[0]['way']==1){
                    $param = array(
                        "service"     => "live",
                        "template"    => "detail",
                        "id"          => $id
                    );
                }else{
                    $param = array(
                        "service"     => "live",
                        "template"    => "h_detail",
                        "id"          => $id
                    );
                }
            }else{
                $param = array(
                    "service"     => "live",
                    "template"    => "detail",
                    "id"          => $id
                );
            }
            $webUrl = getUrlPath($param);
            //直播时间
            $starttime = !empty($res[0]['starttime']) ? $res[0]['starttime'] : time();
            $livetime  = !empty($res[0]['livetime']) ? $res[0]['livetime'] : 0;
            //直播限制
            $member = getMemberDetail($userid);
            if(!empty($member['level'])){
                $archives = $dsql->SetQuery("SELECT * FROM `#@__member_level` WHERE `id` = ".$member['level']);
                $results  = $dsql->dsqlOper($archives, "results");
                $fabuAmount=!empty($results[0]['privilege']) ? unserialize($results[0]['privilege']) : array('livetime'=>0);
            }else{
                require(HUONIAOINC."/config/settlement.inc.php");
                $fabuAmount = $cfg_fabuAmount ? unserialize($cfg_fabuAmount) : array('livetime'=>0);
            }
            $huoniaoTag->assign('starttime', $starttime);
            $huoniaoTag->assign('livetime', (string)$livetime);
            $huoniaoTag->assign('liveLimitTime', $fabuAmount['livetime']);

            $up  = $res[0]['up'];
            $state  = $res[0]['state'];
            $huoniaoTag->assign('state', $state);
            // $streamname  = $res[0]['streamname'];
            $streamName = 'live' . $res[0]['id'] . '-' . $res[0]['user'];
            //直播分类
            $archives  = $dsql->SetQuery("SELECT `typename` FROM `#@__livetype` WHERE `id` = ".$res[0]['typeid']);
            $result    = $dsql->dsqlOper($archives, "results");
            $typename  = !empty($result[0]['typename']) ? $result[0]['typename'] : '';
            //直播类型
            $catidtype = empty($res[0]['catid']) ? $langData['siteConfig'][31][56] : ($res[0]['catid']==1 ? $langData['siteConfig'][31][57] : $langData['siteConfig'][19][889]);//公开 加密 收费
            $catid = $res[0]['catid'];
            $title     = empty($res[0]['title'])   ? $langData['siteConfig'][34][39] : $res[0]['title'];//无标题
            //流畅度
            $flowname  = empty($res[0]['flow'])  ? $langData['siteConfig'][31][61] : ($res[0]['flow']==1  ? $langData['siteConfig'][31][62] : $langData['siteConfig'][31][63]);//流畅 普清 高清
            $flow  = $res[0]['flow'];
            $password  = $res[0]['password'];
            $startmoney  = $res[0]['startmoney'];
            $endmoney  = $res[0]['endmoney'];
            //直播方式
            $wayname   = empty($res[0]['way'])   ? $langData['siteConfig'][31][53] : $langData['siteConfig'][31][54]; //横屏 竖屏
            $way       = $res[0]['way'];
            $click   = empty($res[0]['click'])   ? '0' : $res[0]['click'];
            $litpic    = !empty($res[0]['litpic']) ? (strpos($res[0]['litpic'],'images') ? $cfg_secureAccess . $cfg_basehost . $res[0]['litpic'] : getFilePath($res[0]['litpic'])) : $cfg_secureAccess . $cfg_basehost . '/static/images/404.jpg';
            $ftime = !empty($res[0]['ftime']) ? date("Y.m.d H:i",$res[0]['ftime']) : date("Y.m.d H:i",time());
            $pushurl = !empty($res[0]['pushurl']) ? $res[0]['pushurl'] : '';
            $huoniaoTag->assign('typename', $typename);
            $huoniaoTag->assign('catidtype', $catidtype);
            $huoniaoTag->assign('catid', $catid);
            $huoniaoTag->assign('flowname', $flowname);
            $huoniaoTag->assign('flow', $flow);
            $huoniaoTag->assign('startmoney', $startmoney);
            $huoniaoTag->assign('endmoney', $endmoney);
            $huoniaoTag->assign('password', $password);
            $huoniaoTag->assign('click', $click);
            $huoniaoTag->assign('wayname', $wayname);
            $huoniaoTag->assign('up', $up);
            $huoniaoTag->assign('way', $way);
            $huoniaoTag->assign('ftime', $ftime);
            $huoniaoTag->assign('title', $title);
            $huoniaoTag->assign('pushurl', $res[0]['pushurl']);
            $huoniaoTag->assign('litpic', $litpic);
            $huoniaoTag->assign('webUrl', $webUrl);
            $huoniaoTag->assign('streamname', $streamname);
            $huoniaoTag->assign('pulltype', $res[0]['pulltype']);
            $huoniaoTag->assign('pullurl_pc', $res[0]['pullurl_pc']);
            $huoniaoTag->assign('pullurl_touch', $res[0]['pullurl_touch']);
            $huoniaoTag->assign('arcrank', $res[0]['arcrank']);

            $huoniaoTag->assign('module', 'live');

        }else{
            header("location:".$cfg_basehost."/login.html");
        }


    // 提现
	}elseif($action == "put_forward"){

		if(empty($type)){
			$type = "alipay";
		}

		$type = $type != "alipay" && $type != "wxpay" ? "alipay" : $type;

        $param = array(
            "service" => "member",
            "type" => "user",
        );

        if(empty($module)){
            header("location:".getUrlPath($param));
            die;
        }

		$min = 0;
		if($type == "alipay"){
			$min = 0.1;
			$title = "提现到支付宝";
		}elseif($type == "wxpay"){
			$min = 0.3;
			$title = "提现到微信";
		}

		$detail = array();

		if($module == "dating"){
			$param = array("utype" => $utype);
			$moduleHandels = new handlers('dating', "putForward");
			$moduleConfig  = $moduleHandels->getHandle($param);
			if(is_array($moduleConfig) && $moduleConfig['state'] == 100){
				$detail = $moduleConfig['info'];
			}else{
				echo '<script>alert("'.$moduleConfig['info'].'");window.history.go(-1);</script>';
				die;
			}

			if($detail['minPutMoney'] < $min){
				$detail['minPutMoney'] = $min;
			}

		}

		$detail['module'] = $module;
		$detail['type'] = $type;
		$detail['utype'] = (int)$utype;
		$detail['title'] = $title;

        $huoniaoTag->assign("detail", $detail);

        $url = $cfg_secureAccess.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        $url = $url ? urlencode($url) : "";
		$huoniaoTag->assign("url", $url);


	// 入驻商家支付页面
	}elseif($action == "joinPay"){
		$uid = $userLogin->getMemberID();
		if($uid == -1 || empty($ordernum)){
			header("location:".$cfg_secureAccess.$cfg_basehost);
			die;
		}
		// 入驻商家
		$channelName = "入驻商家";
		$sql = $dsql->SetQuery("SELECT o.`id`, o.`totalprice`, o.`state` FROM `#@__business_order` o LEFT JOIN `#@__business_list` b ON b.`id` = o.`bid` WHERE b.`uid` = $uid AND o.`ordernum` = '$ordernum'");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$state = $ret[0]['state'];
			if($state == 1){
				$param = array(
					"service" => "member",
					"type" => "user",
					"template" => "joinPayreturn",
					"param" => "ordernum=".$ordernum
				);
				header("location:".getUrlPath($param));
				die;
			}else{
				$totalAmount = $ret[0]['totalprice'];
				$ordernum = $ordernum;
				$huoniaoTag->assign('totalAmount', $totalAmount);
				$huoniaoTag->assign('ordernum', $ordernum);
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost);
			die;
		}
		$huoniaoTag->assign("channelName", $channelName);
	// 支付结果页面
	}elseif($action == "joinPayreturn"){
		$uid = $userLogin->getMemberID();
		if($uid == -1 || empty($ordernum)){
			header("location:".$cfg_secureAccess.$cfg_basehost);
			die;
		}
		$sql = $dsql->SetQuery("SELECT o.`id`, o.`totalprice`, o.`state`, o.`ordertype` FROM `#@__business_order` o LEFT JOIN `#@__business_list` b ON b.`id` = o.`bid` WHERE o.`ordernum` = '$ordernum' AND b.`uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$state = $ret[0]['state'];
			$ordertype = $ret[0]['ordertype'];
			$param = array(
				"service" => "member"
			);
			if($ordertype == "join"){
				$param['type'] = "user";
			}
			$url = getUrlPath($param);

			$huoniaoTag->assign('state', $state);
			$huoniaoTag->assign('url', $url);
		}

	// 选择区域
	}elseif($action == "choose_address"){
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			header("location:".$cfg_secureAccess.$cfg_basehost);
			die;
		}
        $showMap = 1;
		if($ser == "business"){
			$sql = $dsql->SetQuery("SELECT `lng`, `lat`, `addrid`, `cityid`, `address`, `landmark` FROM `#@__business_list` WHERE `uid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$lng = $ret[0]['lng'];
				$lat = $ret[0]['lat'];
				$addrid = $ret[0]['addrid'];
				$cityid = $ret[0]['cityid'];
				$address = $ret[0]['address'];
				$landmark = $ret[0]['landmark'];
			}else{
                $param = array(
                    "service" => "member",
                    "type" => "user"
                );
                header("location:".getUrlPath($param));
                die;
            }
		}elseif($ser == "house"){
            $showMap = 0;
            $sql = $dsql->SetQuery("SELECT `addr`, `cityid`, `address` FROM `#@__house_zjcom` WHERE `userid` = $uid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $addrid = $ret[0]['addr'];
                $cityid = $ret[0]['cityid'];
                $address = $ret[0]['address'];
            }else{
                $param = array(
                    "service" => "member",
                );
                header("location:".getUrlPath($param));
                die;
            }
        }
        $huoniaoTag->assign('showMap', $showMap);
		$huoniaoTag->assign('ser', $ser);
		$huoniaoTag->assign('act', $act);
		$huoniaoTag->assign('lng', $lng);
		$huoniaoTag->assign('lat', $lat);
		$huoniaoTag->assign('addrid', $addrid);
		$huoniaoTag->assign('cityid', $cityid);
		$huoniaoTag->assign('address', $address);
		$huoniaoTag->assign('landmark', $landmark);

	// 商家模块管理
	}elseif($action == "appmanage"){

		$userLogin->checkUserIsLogin();
		$userid = $userLogin->getMemberID();

		$sql = $dsql->SetQuery("SELECT `type`, `bind_module` FROM `#@__business_list` WHERE `uid` = " . $userid);
		$res = $dsql->dsqlOper($sql, "results");
		if($res){
			$type = $res[0]['type'];
			$bind_module = $res[0]['bind_module'] ? explode(',', $res[0]['bind_module']) : array();
		}else{
			$param = array(
				"service" => "member",
				"type" => "user",
			);
			header("location:".getUrlPath($param));
			die;
		}

		$showModule = checkShowModule($bind_module, 'manage', '', 'getUrl');

		$huoniaoTag->assign('showModule', $showModule);

	// 自助建站自定义导航
	}elseif($action == "website-custom_nav"){

		$userLogin->checkUserIsLogin();
		$userid = $userLogin->getMemberID();

		$custom_navArr = array();

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__website` WHERE `userid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$website = $ret[0]['id'];
			$huoniaoTag->assign('has_website', 1);
			$sql = $dsql->SetQuery("SELECT `id`, `alias`, `icon`, `title`, `jump_url` FROM `#@__website_touch` WHERE `sys` = 0 AND `website` = $website");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				foreach ($ret as $key => $value) {
					$custom_navArr[$key] = array(
						'id' => $value['id'],
						'icon' => $value['icon'],
						'iconSource' => $value['icon'] ? getFilePath($value['icon']) : "",
						'title' => $value['title'],
						'url' => $value['jump_url'],
					);
				}
			}
			$huoniaoTag->assign('custom_navArr', $custom_navArr);
		}

    // 入驻经纪人
	}elseif($action == "enter_house"){

        $userLogin->checkUserIsLogin();
        $userid = $userLogin->getMemberID();

        $obj = new house();
        $config = $obj->config();

        $hotline = $config['hotline'];
        $logoUrl = $config['logoUrl'];
        $channelDomain = $config['channelDomain'];
        $channelName = $config['channelName'];

        $huoniaoTag->assign('hotline', $hotline);
        $huoniaoTag->assign('logoUrl', $logoUrl);
        $huoniaoTag->assign('channelName', $channelName);
        $huoniaoTag->assign('channelDomain', $channelDomain);

        // 验证入驻情况
        $sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__house_zjuser` WHERE `userid` = $userid");
        $res = $dsql->dsqlOper($sql, "results");
        $huoniaoTag->assign('enter_zjuser', $res ? 1 : 0);

        $sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__house_zjcom` WHERE `userid` = $userid");
        $res = $dsql->dsqlOper($sql, "results");
        $huoniaoTag->assign('enter_zjcom', $res ? 1 : 0);

        $huoniaoTag->assign('type', $type);

    //房产预约委托
    }elseif($template == "house_yuyue_list"
            || $template == "house_yuyue"
            || $template == "house_entrust"
			|| $template == "house_enturst_list"
			|| $template == "car_entrust"
        ){
        $userLogin->checkUserIsLogin();
    // 经纪人套餐列表
    }elseif($template == "house_meallist"){

        $obj = new house();
        $config = $obj->config();

        $zjuserPriceCost = $config['zjuserPriceCost'];

        $huoniaoTag->assign('zjuserPriceCost', $zjuserPriceCost);
        $huoniaoTag->assign('type', $type);
        $huoniaoTag->assign('item', $item);
        $huoniaoTag->assign('upgrade', (int)$upgrade);

    // 支付
    }elseif($action == "pay"){
        $userLogin->checkUserIsLogin();
        $userid = $userLogin->getMemberID();

        $data = $_GET;
        unset($data['paytype']);
        unset($data['useBalance']);
        unset($data['balance']);

        extract($data);

        $param = array("service" => "member", "type" => "user");
        if(empty($ordertype) || empty($ordernum)){
            header("location:".getUrlPath($param));
            die;
        }
        $allowUseMoney = true;
        if($ordertype == "deposit"){
            $allowUseMoney = false;
        }

        $paramsHtml = "";
        global $service;
        // 重置service
        if($ordertype == "refreshTop"){
            $service = "siteConfig";
        }
        unset($data['ordernum']);
        foreach ($data as $key => $value) {
            $paramsHtml .= "<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\" />";
        }
        $paramsHtml .= "<input type=\"hidden\" name=\"final\" value=\"1\" />";  // 最终支付
        $huoniaoTag->assign('paramsHtml', $paramsHtml);
        $huoniaoTag->assign('ordernum', $ordernum);
        $huoniaoTag->assign('totalAmount', $amount);
        $huoniaoTag->assign('service', $service);
        $huoniaoTag->assign('orderurl', 'javascript:;');
        $huoniaoTag->assign('allowUseMoney', $allowUseMoney);

	}elseif($action == "housem"){//房源管理
		$userLogin->checkUserIsLogin();

		$type = $type ? $type : 'cf';
		$huoniaoTag->assign('type', $type);

    //配置自媒体
    }elseif(stripos($action, "config-selfmedia") !== false){

        $userLogin->checkUserIsLogin();
        $userid = $userLogin->getMemberID();
        $is_join = 0;

        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__article_selfmedia` WHERE `userid` = $userid");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $is_join = 1;

            $contorllerFile = dirname(__FILE__).'/article.controller.php';
            if(file_exists($contorllerFile)){
                //声明以下均为接口类
                $handler = true;
                require_once($contorllerFile);
                $param = array(
                    "action" => "mddetail",
                    "id"     => $ret[0]['id'],
                    "u"      => 1
                );
                article($param);
            }
        // 判断是否为子管理员
        }else{
            $sql = $dsql->SetQuery("SELECT * FROM `#@__article_selfmedia_manager` WHERE `userid` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $is_join = 2;
            }
        }
        $huoniaoTag->assign("is_join", $is_join);

        $article = new article();
        $config = $article->config();

        $huoniaoTag->assign('selfmediaGrantImg', $config['selfmediaGrantImg']);
        $huoniaoTag->assign('selfmediaGrantTpl', $config['selfmediaGrantTpl']);
        $huoniaoTag->assign('selfmediaAgreement', $config['selfmediaAgreement']);

	//入驻汽车经销商 预约管理 顾问管理 顾问入驻电脑端
    }elseif($action == "enter_car" || $action == "car_enter" || $action == "car" || $action == "carappoint" || $action == "adviser_car_add"){
		$userLogin->checkUserIsLogin();
        $userid = $userLogin->getMemberID();

        $obj = new car();
        $config = $obj->config();

        $hotline = $config['hotline'];
        $logoUrl = $config['logoUrl'];
        $channelDomain = $config['channelDomain'];
        $channelName = $config['channelName'];

        $huoniaoTag->assign('hotline', $hotline);
        $huoniaoTag->assign('logoUrl', $logoUrl);
        $huoniaoTag->assign('channelName', $channelName);
		$huoniaoTag->assign('channelDomain', $channelDomain);
		$huoniaoTag->assign('id', $id);

        // 验证入驻情况
        $sql        = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__car_adviser` WHERE `userid` = $userid");
        $adviserres = $dsql->dsqlOper($sql, "results");
        $huoniaoTag->assign('enter_zjuser', $adviserres ? 1 : 0);

        $sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__car_store` WHERE `userid` = $userid");
        $res = $dsql->dsqlOper($sql, "results");
		$huoniaoTag->assign('enter_zjcom', $res[0]['id'] ? 1 : 0);
		if($res){
			$huoniaoTag->assign('store', $res[0]['id']);
		}


		if($action == "car_enter"){//立即申请汽车顾问
			if(!empty($adviserres[0]['id'])){
				$param = array(
					"service" => "member",
					"type" => "user",
					"template" => "car-config"
				);
				header("location:" . getUrlPath($param));
				die;
			}
		}elseif($action == "adviser_car_add"){
			$comid = $res[0]['id'];
			if($do=="edit"){
				$detailHandels = new handlers("car", "adviserList");
				$detailConfig  = $detailHandels->getHandle(array("type" => 'getnormal', "u" => '1', "userid" => $id, "comid" => $comid));
				if(is_array($detailConfig) && $detailConfig['state'] == 100){
					$detailConfig  = $detailConfig['info'];
					if(is_array($detailConfig)){
						//输出详细信息
						foreach ($detailConfig['list'][0] as $key => $value) {
							$huoniaoTag->assign('detail_'.$key, $value);
						}
					}
				}else{
					header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
				}
			}
		}elseif($action == "enter_car"){
			$userinfo = $userLogin->getMemberInfo();
			if($userinfo['userType'] == 1){
				$param = array(
					"service" => "member",
					"type" => "user",
					"template" => "enter"
				);
                $url = getUrlPath($param);
				header("location:".$url);
			}else{
              if(!empty($res[0]['id'])){
                  $param = array(
                      "service" => "member",
                      "type" => "user",
                      "template" => "config-car"
                  );
                  header("location:" . getUrlPath($param));
                  die;
              }
            }
		}

		$huoniaoTag->assign('type', $type);

	}elseif(stripos($action, "config-car") !== false){//移动端商家配置

		$userLogin->checkUserIsLogin();
		$contorllerFile = dirname(__FILE__).'/car.controller.php';

		//获取图片配置参数
		require(HUONIAOINC."/config/car.inc.php");
		$huoniaoTag->assign('atlasMax', $customAtlasMax);
		$huoniaoTag->assign('storeatlasMax', $custom_store_atlasMax);

		// 获取商家模块公共配置
		$uid = $userLogin->getMemberID();

		//查询是否填写过入驻申请
		$sql = $dsql->SetQuery("SELECT `tag` FROM `#@__car_store` WHERE `userid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");

		$carHandlers = new handlers("car", "config");
		$carConfig   = $carHandlers->getHandle();
		$carConfig   = $carConfig['info'];

		$carTag_all = $carConfig['carTag'];
		$carTag_all_ = array();

		if(!empty($ret[0]['tag'])){
			$carTag_ = explode('|', $ret[0]['tag']);
		}else{
			$carTag_ = array();
		}
		foreach ($carTag_all as $v) {
			$carTag_all_[] = array(
				'name' => $v,
				'py'   => GetPinyin($v),
				'icon' => 'b_sertag_' . GetPinyin($v) . '.png',
				'active' => in_array($v, $carTag_) ? 1 : 0
			);
		}

		$huoniaoTag->assign('carTag_state', $carTag_all_);

		if(file_exists($contorllerFile)){
			//声明以下均为接口类
			$handler = true;
			require_once($contorllerFile);

			$param = array(
				"action" => "storeDetail",
			);

            car($param);

			$zjcom = 0;
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__car_store` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$zjcom = 1;
			}
			$huoniaoTag->assign("zjcom", $zjcom);

		}

	}elseif($action == 'car-config'){
		$userLogin->checkUserIsLogin();
		$userid = $userLogin->getMemberID();
		$jjr = 0;

		$sql = $dsql->SetQuery("SELECT * FROM `#@__car_adviser` WHERE `userid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$jjr = 1;

			$contorllerFile = dirname(__FILE__).'/car.controller.php';
			if(file_exists($contorllerFile)){
				//声明以下均为接口类
				$handler = true;
				require_once($contorllerFile);

				$param = array(
					"action" => "broker-detail",
					"id"     => $ret[0]['id'],
					"u"      => 1
				);
				car($param);
			}
		}
		$huoniaoTag->assign("jjr", $jjr);

        $zjcom = 0;
        $sql = $dsql->SetQuery("SELECT * FROM `#@__car_store` WHERE `userid` = $userid");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $zjcom = 1;
        }
        $huoniaoTag->assign("zjcom", $zjcom);

	}elseif($action == 'homemaking-nanny' || $action == 'homemaking-personal'){//保姆/月嫂管理 服务人员
		$userLogin->checkUserIsLogin();
		$userid = $userLogin->getMemberID();

		$huoniaoTag->assign("module", 'homemaking');
		$huoniaoTag->assign("state", $state);
		$huoniaoTag->assign("type", $type);
		$huoniaoTag->assign("typeid", $typeid ? $typeid : 0);

	}elseif($action == "homemaking-cancelservice" || $action == "homemaking-cancel" || $action == "homemaking-service" || $action == "homemaking-repair" || $action == "homemaking-dispatch"){//家政申请退款 确认服务费 售后维保 派单

		if($action == "homemaking-cancel" || $action == "homemaking-cancelservice"){
			$detailHandels = new handlers("homemaking", "config");
			$configArr  = $detailHandels->getHandle();
			$huoniaoTag->assign('refundReason', json_encode($configArr['info']['refundReason']));
			$huoniaoTag->assign('afterSalesType', json_encode($configArr['info']['afterSalesType']));
		}

		$huoniaoTag->assign('type', $type ? $type : 0);


		$detailHandels = new handlers("homemaking", "orderDetail");
		$detailConfig  = $detailHandels->getHandle($id);

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];//print_R($detailConfig);exit;
			if(is_array($detailConfig)){
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
				require(HUONIAOINC."/config/homemaking.inc.php");

				if($customUpload == 1){
					$huoniaoTag->assign('thumbSize', $custom_thumbSize);
					$huoniaoTag->assign('thumbType', str_replace("|", ",", $custom_thumbType));
					$huoniaoTag->assign('atlasSize', $custom_atlasSize);
					$huoniaoTag->assign('atlasType', str_replace("|", ",", $custom_atlasType));
				}else{
					$huoniaoTag->assign('thumbType', str_replace("|", ",", $cfg_thumbType));
					$huoniaoTag->assign('atlasType', str_replace("|", ",", $cfg_atlasType));
				}
				$huoniaoTag->assign('atlasMax', (int)$customAtlasMax);
			}

			$huoniaoTag->assign('rates', (int)$rates);
			$huoniaoTag->assign('type', $type);
			$huoniaoTag->assign('id', (int)$id);
			$huoniaoTag->assign('module', 'homemaking');

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}

	}elseif($action == "homemaking-courier"){//派单员订单

		$detailHandels = new handlers("homemaking", "personalDetail");
		$detailConfig  = $detailHandels->getHandle($id);

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];//print_R($detailConfig);exit;
			if(is_array($detailConfig)){
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}

			$huoniaoTag->assign('rates', (int)$rates);
			$huoniaoTag->assign('type', $type);
			$huoniaoTag->assign('id', (int)$id);
			$huoniaoTag->assign('module', 'homemaking');

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}
		$huoniaoTag->assign('id', (int)$id);
		$huoniaoTag->assign('module', 'homemaking');
	}elseif($action == "homemaking-courierorder"){
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__homemaking_personal` WHERE `userid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		$huoniaoTag->assign('personalId', $ret[0]['id'] ? $ret[0]['id'] : 0);

    // 分销
    }elseif(strstr($action, 'fenxiao')){
        global $cfg_fenxiaoState;
        if($cfg_fenxiaoState != 1){
            header("location:/404.html");
            die;
        }
        $uid = $userLogin->getMemberID();
        if($uid <= 0){
            $param = array(
                "service" => "siteConfig",
                "template" => "login",
            );
            header("location:".getUrlPath($param));
            die;
        }

        $sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__member_fenxiao_user` WHERE `uid` = $uid");
        $check = $dsql->dsqlOper($sql, "results");
        $huoniaoTag->assign('tj_state', $check ? $check[0]['state'] : -1);

        if($action == 'fenxiao_join'){
            if($check && $check[0]['state'] == 1){

            }
        }else{
            if(!$check || $check[0]['state'] != 1){
                $param = array(
                    "service" => "member",
                    "type" => "user",
                    "template" => "fenxiao_join",
                );
                header("location:".getUrlPath($param));
                die;
            }

            // 我的分销首页
            if($action == 'fenxiao'){
                $from_username = "";
                $from_userid = 0;
                $sql = $dsql->SetQuery("SELECT m2.`id`, m2.`username` FROM `#@__member` m1 LEFT JOIN `#@__member` m2 ON m2.`id` = m1.`from_uid` WHERE m1.`id` = $uid");
                $res = $dsql->dsqlOper($sql, "results");
                if($res){
                    $from_username = $res[0]['username'];
                    $from_userid = $res[0]['id'];
                }
                $huoniaoTag->assign('from_username', $from_username);
                $huoniaoTag->assign('from_userid', $from_userid);

                $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__member_fenxiao` WHERE `uid` = $uid");
                $res = $dsql->dsqlOper($sql, "results");
                $huoniaoTag->assign('totalAmount', $res[0]['total'] ? $res[0]['total'] : 0);

            }elseif($action == 'fenxiao_user'){

            }elseif($action == 'fenxiao_commission'){

            }elseif($action == 'fenxiao_commission_detail'){
                $id = (int)$id;
                $ser = new member(array("id" => $id));
                $order  = $ser->fenxiaoDetail();
                if(empty($order)){
                    $param = array(
                        "service" => "member",
                        "type" => "user",
                        "template" => "fenxiao_commission",
                    );
                    header("location:".getUrlPath($param));
                    die;
                }
                $huoniaoTag->assign('order', $order);
            }
        }
	}elseif($action == "marry"){//婚嫁
		$sql = $dsql->SetQuery("SELECT `id`, `title`, `bind_module` FROM `#@__marry_store` WHERE `state` = 1 AND `userid` = $userid");
		$res = $dsql->dsqlOper($sql, "results");
		if(!empty($res[0]['id']) && !empty($res[0]['bind_module'])){
			$isMarryStore = true;
			$bind_moduleArr = explode(',', $res[0]['bind_module']);
			$huoniaoTag->assign('bind_moduleArr', $bind_moduleArr);
		}else{
			$isMarryStore = false;
		}
		$huoniaoTag->assign('isMarryStore', $isMarryStore);
	}elseif($action == "marry-planmeal"){//套餐
		$huoniaoTag->assign('typeid', $typeid ? (int)$typeid : 0);
		$huoniaoTag->assign('module', 'marry');
		$huoniaoTag->assign('state', $state);
		$huoniaoTag->assign('type', $type);

	}elseif($action == "travel"){//旅游
		$userLogin->checkUserIsLogin();

		$sql = $dsql->SetQuery("SELECT `id`, `title`, `bind_module` FROM `#@__travel_store` WHERE `state` = 1 AND `userid` = $userid");
		$res = $dsql->dsqlOper($sql, "results");
		if(!empty($res[0]['id']) && !empty($res[0]['bind_module'])){
			$isTravelStore = true;
			$bind_moduleArr = explode(',', $res[0]['bind_module']);
			$huoniaoTag->assign('bind_moduleArr', $bind_moduleArr);
		}else{
			$isTravelStore = false;
		}
		if($userinfo['userType'] == 1) {
			$isTravelStore = true;
		}
		$huoniaoTag->assign('isTravelStore', $isTravelStore);
	}elseif($action == "travel-strategy" || $action == "travel-hotel" || $action == "travel-ticket" || $action == "travel-video" || $action == "travel-rentcar" || $action == "travel-visa" || $action == "travel-agency"){//TODO: 旅游酒店
		$userLogin->checkUserIsLogin();
		$huoniaoTag->assign('module', 'travel');
		$huoniaoTag->assign('state', $state);
		$huoniaoTag->assign('type', $type);
	// 发布图文直播·管理评论
    }elseif($action == "live_imgtext" || $action == "live_comment" || $action == "fabu_live_imgtext"){
        $userLogin->checkUserIsLogin();
        $userid = $userLogin->getMemberID();
        $id = (int)$id;
        if($id){
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__livelist` where id='$id' and user='$userid'");
            $res = $dsql->dsqlOper($sql, "results");
            if($res){
                $huoniaoTag->assign('id', $id);
                $huoniaoTag->assign('atlasMax', 9);
                return;
            }
        }
        header("location:/404.html");
        die;
	}elseif($action == "travel-cancelhotel" || $action == "travel-cancelticket" || $action == "travel-canceldetail"){//旅游申请退款
		$userLogin->checkUserIsLogin();

		$detailHandels = new handlers("travel", "orderDetail");
		$detailConfig  = $detailHandels->getHandle($id);


		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];//print_R($detailConfig);exit;
			if(is_array($detailConfig)){
				foreach ($detailConfig as $key => $value) {
					if($key == 'retDate'){
						$threeDay = date("Y-m-d", strtotime($value . "+1 day"));
						$huoniaoTag->assign('threeDay', $threeDay);
					}
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}

			$huoniaoTag->assign('id', (int)$id);
			$huoniaoTag->assign('module', 'travel');

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}
	}elseif($action == "verify-travel"){
		$cardnum = $_GET['cardnum'];
		$cardnum = empty($cardnum) ? array("") : explode(',',$cardnum);
		$huoniaoTag->assign('cardnum', $cardnum);

	}elseif($action == "education"){
		$userLogin->checkUserIsLogin();

		$sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__education_store` WHERE `state` = 1 AND `userid` = $userid");
		$res = $dsql->dsqlOper($sql, "results");
		if(!empty($res[0]['id'])){
			$isEducationStore = true;
		}else{
			$isEducationStore = false;
		}
		if($userinfo['userType'] == 2) {
			$userid = $res[0]['id'];
		}
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__education_tutor` WHERE `state` = 1 AND `userid` = $userid");
		$res = $dsql->dsqlOper($sql, "results");
		if(!empty($res[0]['id'])){
			$isEducationTutor = true;
		}else{
			$isEducationTutor = false;
		}
		$huoniaoTag->assign('isEducationStore', $isEducationStore);
		$huoniaoTag->assign('isEducationTutor', $isEducationTutor);
	}elseif($action == "pension-award" || $action == "pension-invitation"){
		$userLogin->checkUserIsLogin();
		$typeArr = empty($action) ? array("") : explode('-',$action);
		$huoniaoTag->assign('type', $typeArr[1]);
	}


    if(strpos($action, "dating-") !== false){

        $userLogin->checkUserIsLogin();

        $params['realServer'] = "member";
        $act = substr($action, 7);
        $params['action'] = $act;
        $params['template'] = $act;
        dating($params);

        foreach($_GET as $k => $v){
            $huoniaoTag->assign($k, $v);
        }

        //交友相册
        if($action == "dating-album-add"){
            //获取图片配置参数
            require(HUONIAOINC."/config/dating.inc.php");

            if($customUpload == 1){
                $huoniaoTag->assign('atlasSize', $custom_atlasSize);
                $huoniaoTag->assign('atlasType', "*.".str_replace("|", ";*.", $custom_atlasType));
            }
            $huoniaoTag->assign('atlasMax', (int)$customAtlasMax);
        }

    }

	global $template;
	if(empty($smarty)) return;

	if(!isset($return))
		$return = 'row'; //返回的变量数组名

	//注册一个block的索引，照顾smarty的版本
  if(method_exists($smarty, 'get_template_vars')){
      $_bindex = $smarty->get_template_vars('_bindex');
  }else{
      $_bindex = $smarty->getVariable('_bindex')->value;
  }

  if(!$_bindex){
      $_bindex = array();
  }

  if($return){
      if(!isset($_bindex[$return])){
          $_bindex[$return] = 1;
      }else{
          $_bindex[$return] ++;
      }
  }

  $smarty->assign('_bindex', $_bindex);

	//对象$smarty上注册一个数组以供block使用
	if(!isset($smarty->block_data)){
		$smarty->block_data = array();
	}

	//得一个本区块的专属数据存储空间
	$dataindex = md5(__FUNCTION__.md5(serialize($params)));
	$dataindex = substr($dataindex, 0, 16);

	//使用$smarty->block_data[$dataindex]来存储
	if(!$smarty->block_data[$dataindex]){
		//取得指定动作名
		$moduleHandels = new handlers($service, $action);
		$moduleReturn  = $moduleHandels->getHandle($params);
		if(!is_array($moduleReturn) || $moduleReturn['state'] != 100) return '';

		$moduleReturn  = $moduleReturn['info'];  //返回数据

		$pageInfo_ = $moduleReturn['pageInfo'];
		if($pageInfo_){

			//如果有分页数据则提取list键
			$moduleReturn  = $moduleReturn['list'];

			//把pageInfo定义为global变量
			global $pageInfo;
			$pageInfo = $pageInfo_;

			$smarty->assign("pageInfo", $pageInfo);
		}

		$smarty->block_data[$dataindex] = $moduleReturn;  //存储数据
        reset($smarty->block_data[$dataindex]);
	}

	//果没有数据，直接返回null,不必再执行了
	if(!$smarty->block_data[$dataindex]) {
		$repeat = false;
		return '';
	}

	if($action=="type"){
		//print_r($smarty->block_data[$dataindex]);die;
	}

	//一条数据出栈，并把它指派给$return，重复执行开关置位1
	if(list($key, $item) = each($smarty->block_data[$dataindex])){
		$smarty->assign($return, $item);
		$repeat = true;
	}

	//如果已经到达最后，重置数组指针，重复执行开关置位0
	if(!$item) {
		reset($smarty->block_data[$dataindex]);
		$repeat = false;
	}

	//打印内容
	print $content;
}
