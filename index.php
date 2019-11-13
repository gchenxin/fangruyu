<?php 
//系统核心配置文件
include_once('./domain_reverse.php');
var_dump($_SERVER);
return;
require_once(dirname(__FILE__).'/include/common.inc.php');

//域名检测
$httpHost  = $_SERVER['HTTP_HOST'];  //来访域名

//获取访问详情  兼容win
$reqUri = $_SERVER["HTTP_X_REWRITE_URL"];
if($reqUri == null){
	$reqUri = $_SERVER["HTTP_X_ORIGINAL_URL"];
	if($reqUri == null){
		$reqUri = $_SERVER["REQUEST_URI"];
	}
}

$service = 'siteConfig';

//绑定独立域名配置
$domainPart = "";
$domainIid = 0;

//城市分站域名类型  0主域名  1子域名  2子目录  3三级域名   默认为子目录
$cityDomainType = 0;
// 绑定的域名是否属于会员中心
$domainIsMember = false;

//模块域名类型   0默认为子目录  1主域名  2子域名
$siteModuleDomainType = 0;

$dirDomain = $cfg_secureAccess . $httpHost . $reqUri;
$todayDate = GetMkTime(time());
$cfg_basehost_ = str_replace("www.", "", $cfg_basehost);
if($cfg_basehost_ != str_replace("www.", "", $httpHost) && empty($_GET['service'])){

	//全域名匹配数据库是否存在
	$httpHost_ = str_replace("www.", "", $httpHost);
	$sql = $dsql->SetQuery("SELECT * FROM `#@__domain` WHERE `domain` = '$httpHost' OR `domain` = '$httpHost_'");
	$results = $dsql->dsqlOper($sql, "results");
	if($results){

		$domain     = $results[0]['domain'];
		$module     = $results[0]['module'];
		$domainPart = $results[0]['part'];
		$domainIid  = $results[0]['iid'];
		$expires    = $results[0]['expires'];
		$note       = $results[0]['note'];

		//判断是否过期
		if($todayDate < $expires || empty($expires)){
			 $service = $module;

		}else{
			die($note);
		}

		//分站域名验证
		if($module == 'siteConfig' && $domainPart == 'city'){
			$cityDomainType = 0;
			$city = $domain;
		}

		//模块绑定独立域名
		if($module != 'siteConfig' && $domainPart == 'config'){
			$siteModuleDomainType = 1;
		}

		if($module == "member"){
			$domainIsMember = true;
		}

	//二级、三级域名
	}else{
		$httpHostSub = str_replace(".".$cfg_basehost_, "", $httpHost);

		//三级域名
		if(strstr($httpHostSub, '.')){
			$httpHostSub_ = $httpHostSub;
			$httpHostSubArr = explode('.', $httpHostSub_);
			$httpHostSub_ = $httpHostSubArr[1];  //提取出城市域名

			//这里还需要再验证一次城市绑定主域名，模块绑定子域名的情况，例：城市绑定：suzhou.com，模块绑定：article.suzhou.com
			$hostDomain_ = str_replace($httpHostSubArr[0] . ".", "", $httpHost_);
			$sql = $dsql->SetQuery("SELECT * FROM `#@__domain` WHERE `domain` = '$hostDomain_'");
			$results = $dsql->dsqlOper($sql, "results");
			if($results){
				$isSubMainDomain = true;
				$httpHostSub = $hostDomain_;

			}else{
				//这里还需要再次验证城市绑定主域名，模块绑定子域名的情况，例：article.beijing.ihuoniao.cn
				$hostDomain_ = str_replace("." . $cfg_basehost, "", $httpHost_);

				//这里还需要再验证一次城市绑定主域名，模块绑定子域名的情况，例：城市绑定：www.suzhou.com，模块绑定：article.suzhou.com
				//与上面不一样的地方是带www了
				if(substr_count($hostDomain_, '.') == 2){
					$hostDomain_ = 'www.' . str_replace($httpHostSubArr[0] . ".", "", $httpHost_);
					$sql = $dsql->SetQuery("SELECT * FROM `#@__domain` WHERE `domain` = '$hostDomain_'");
					$results = $dsql->dsqlOper($sql, "results");
					if($results){
						$isSubMainDomain = true;
						$httpHostSub = $hostDomain_;
					}

				//三级域名
				}else{
					if(strstr($hostDomain_, '.')){
						$hostDomain_1 = $hostDomain_;
						$hostDomainArr = explode('.', $hostDomain_1);
						$httpHostSub_ = $httpHostSubArr[1];  //提取出城市域名

						$sql = $dsql->SetQuery("SELECT * FROM `#@__domain` WHERE `domain` = '$httpHostSub_'");
						$results = $dsql->dsqlOper($sql, "results");
						if($results){
							$isSubMainDomain = true;
							$httpHostSub = $httpHostSub_;
						}
					}
				}


			}
		}

		//二级域名
		$sql = $dsql->SetQuery("SELECT * FROM `#@__domain` WHERE `domain` = '$httpHostSub'");
		$results = $dsql->dsqlOper($sql, "results");
		if($results){

			$domain     = $results[0]['domain'];
			$module     = $results[0]['module'];
			$domainPart = $results[0]['part'];
			$domainIid  = $results[0]['iid'];
			$expires    = $results[0]['expires'];
			$note       = $results[0]['note'];

			//判断是否过期
			if($todayDate < $expires || empty($expires)){
				 $service = $module;

			}else{
				die($note);
			}

			//分站域名验证
			if($module == 'siteConfig' && $domainPart == 'city'){
				$cityDomainType = 1;
				$city = $domain;
			}

			//模块绑定独立域名
			if($module != 'siteConfig' && $domainPart == 'config'){
				$siteModuleDomainType = 2;
			}

			//是否多城市
			$sql = $dsql->SetQuery('SELECT `id` FROM `#@__site_city`');
			$ret = $dsql->dsqlOper($sql, 'totalCount');
			if($ret == 1){
				//修复单城市，多模块无法访问内页问题
				$cityDomainType = 3;
				$httpHostSubArr = explode('.', $httpHostSub);
			}elseif($httpHostSubArr){
				$cityDomainType = 3;
			}

			if($module == "member"){
				$domainIsMember = true;
			}

		//域名不存在
		}else{

			// print_r($httpHostSub_);die;

			// die("<center><br /><br />域名不存在，请确认后重试1！<br /><br />The domain name does not exist. Please confirm and try again 1!</center>");
		}


	}

//子目录的情况
}else{

	$reqUriArr = explode("/", $reqUri);
	$subDomain = $reqUriArr[1];
	$subDomain = explode("?", $subDomain);
	$subDomain = $subDomain[0];
	$subDomain = explode("-", $subDomain);
	$subDomain = $subDomain[0];

	if($subDomain != "user"){
		$sql = $dsql->SetQuery("SELECT * FROM `#@__domain` WHERE `domain` = '$subDomain'");
		$results = $dsql->dsqlOper($sql, "results");
		if($results){

			$domain     = $results[0]['domain'];
			$module     = $results[0]['module'];
			$domainPart = $results[0]['part'];
			$domainIid  = $results[0]['iid'];
			$expires    = $results[0]['expires'];
			$note       = $results[0]['note'];

			//判断是否过期
			if($todayDate < $expires || empty($expires)){
				 $service = $module;

			}else{
				die($note);
			}

			//分站域名验证
			if($module == 'siteConfig' && $domainPart == 'city'){
				$cityDomainType = 2;
				$city = $domain;
			}

			if($module == "member"){
				$domainIsMember = true;
			}

		}else{

			if($reqUri != '/' && !empty($reqUri) && !strstr($reqUri, '.html')){
				// header ("location:/404.html");
				// die;
			}
		}
	}else{
		$domainIsMember = true;
	}

}
//域名检测 e



//管理授权登录会员账号
if($action == 'authorizedLogin' && $userLogin->getUserID() != -1 && $id){

	$sql = $dsql->SetQuery("SELECT `password` FROM `#@__member` WHERE `id` = " . $id);
	$ret = $dsql->dsqlOper($sql, "results");
	if($ret){
		$userLogin->keepUserID = $userLogin->keepMemberID;
		$userLogin->userID = $id;
		$userLogin->userPASSWORD = $ret[0]['password'];
		$userLogin->keepUser();

		$userDomain = getUrlPath(array("service" => "member", "type" => "user"));
		header("location:" . $userDomain);
		die;
	}
}


//路由分配
$requestPathArr_ = explode('/', $reqUri);

$template_ = explode("?", $requestPathArr_[1]);
$template = str_replace('.html', '', $template_[0]);

$singlePageTemplate = array(
	//会员相关
	'member' => array(
		'complain', 'login_popup', 'login', 'loginCheck', 'loginFrame', 'sso', 'logout', 'fpwd', 'resetpwd', 'register', 'registerCheck', 'registerCheck_v1', 'registerSuccess',
		'registerVerifyEmail', 'memberVerifyEmail', 'registerVerifyPhone', 'memberVerifyPhone', 'getUserInfo', 'bindMobile', 'suggestion'
	),
);

//重置特殊情况下单页的服务名
foreach ($singlePageTemplate as $key => $value) {
	foreach ($value as $k => $v) {
		if(strstr($template, $v)){
			$service = $key;
			$isSystemPage = true;
		}
	}	
}

//如果只开通了一个模块，service直接使用这个模块
$sql = $dsql->SetQuery("SELECT `subject`, `name` FROM `#@__site_module` WHERE `state` = 0 AND `type` = 0 AND `name` != ''");
$siteModule = $dsql->dsqlOper($sql, "results");
if(count($siteModule) == 1){
	$module = $siteModule[0]['name'];
}

$siteCityCount = 0;
$sql = $dsql->SetQuery("SELECT count(`id`) totalCount FROM `#@__site_city`");
$ret = $dsql->dsqlOper($sql, "results");
if($ret){
	$siteCityCount = $ret[0]['totalCount'];
}

if((strstr($reqUri, 'http') || count($requestPathArr_) < 3) && strstr($requestPathArr_[1], '.html') && ($cityDomainType == 0 || $isSystemPage)){

	// $service = $domainIsMember ? "member" : $service;
	$service = $domainIsMember ? "member" : ($isSystemPage ? $service : ($module ? $module : $service) );
	$template = checkPagePath($service, $template, $reqUri);
}else{

	$requestPath = str_replace('.html', '', $reqUri);
	$requestPath = explode("?", $requestPath);
	$requestPathArr = explode('/', $requestPath[0]);
	//子目录   结构：/城市/模块/页面   例：ihuoniao.cn/suzhou/article/list-1.html
	if($cityDomainType == 2){

		if(count($siteModule) == 1 && $requestPathArr[2] != 'business'){
			$service = $module;
			$pagePath = $requestPathArr[3] ? $requestPathArr[3] : $requestPathArr[2];
		}else{

			//如果只有一个分站，并且模块绑定了独立域名
			if($siteCityCount == 1 && $siteModuleDomainType == 1){
				$pagePath = $requestPathArr[2] ? $requestPathArr[2] : $requestPathArr[1];  //页面
				$pagePath = empty($pagePath) ? 'index' : $pagePath;
			}else{
				$service = $requestPathArr[2];  //模块
				$pagePath = $requestPathArr[4] ? $requestPathArr[4] : $requestPathArr[3];  //页面
				$pagePath = empty($pagePath) ? 'index' : $pagePath;

				$service = $domainIsMember ? "member" : $service;
			}
		}

		$template = checkPagePath($service, $pagePath, $reqUri);


	//子域名  结构：域名.主域名/模块/页面   例：suzhou.ihuoniao.cn/article/list-1.html
	}elseif($cityDomainType == 1){

		if(count($siteModule) == 1 && $requestPathArr[1] != 'business'){
	    $service = $module;
	    $pagePath = $requestPathArr[1];
	  }else{
			$service = $requestPathArr[1];  //模块
			$pagePath = $requestPathArr[3] ? $requestPathArr[3] : $requestPathArr[2];  //页面
			$pagePath = empty($pagePath) ? 'index' : $pagePath;

			$service = $domainIsMember ? "member" : $service;
		}

		$template = checkPagePath($service, $pagePath, $reqUri);


	//主域名  结构：域名/模块/页面   例：www.suzhou.com/article/list-1.html
	}elseif($cityDomainType == 0){

		//如果只有一个分站，并且模块绑定了独立域名
		if($siteCityCount == 1 && $siteModuleDomainType == 1){
			$pagePath = $requestPathArr[2] ? $requestPathArr[2] : $requestPathArr[1];  //页面
			$pagePath = empty($pagePath) ? 'index' : $pagePath;
		}else{
			if($module != "member" && $domain != $requestPathArr[1]){
				// $service = $module ? $module : $service;
				// array_unshift($requestPathArr, '');
				// $requestPathArr[1] = $module;
			}
	        if($requestPathArr[1]){
	            $service = $requestPathArr[1];  //模块
	        }
			$pagePath = $requestPathArr[3] ? $requestPathArr[3] : $requestPathArr[2];  //页面
			$pagePath = empty($pagePath) ? 'index' : $pagePath;

			$service = $domainIsMember ? "member" : $service;
		}

		$template = checkPagePath($service, $pagePath, $reqUri);

	//三级域名  结构：模块.城市.主域名/页面  例：article.suzhou.ihuoniao.cn/list-1.html
	//域名检测的地方已经将城市筛选出来，这里只需要将模块及页面取出来
	}elseif($cityDomainType == 3 && $httpHostSubArr){

		$service = $httpHostSubArr[0];  //模块

		//如果是城市主域名，模块二级域名的情况，例：article.suzhou.com
		if($isSubMainDomain){
			$pagePath = $requestPathArr[2] ? $requestPathArr[2] : $requestPathArr[1];  //页面
		}else{
			$pagePath = $requestPathArr[2] ? $requestPathArr[2] : $requestPathArr[1];  //页面
		}
		$pagePath = empty($pagePath) ? 'index' : $pagePath;

		$service = $domainIsMember ? "member" : $service;

		$template = checkPagePath($service, $pagePath, $reqUri);

	}


}


$service = $_GET['service'] ? $_GET['service'] : ($domainIsMember ? "member" : $service);

$service = !empty($cfg_defaultindex) && ($service == 'siteConfig' || empty($service)) ? $cfg_defaultindex : (!empty($service) ? $service : "siteConfig");
$template = !empty($_GET['template']) ? $_GET['template'] : (!empty($template) ? $template : "index");

$config_path = HUONIAOINC."/config/";
$templates   = str_replace(".php", "", $template).".html";

//验证模块状态
if($service != "siteConfig" && $service != "member" && $service != "business"){
	$sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_module` WHERE `name` = '$service' AND `state` = 0 AND `type` = 0");
	$ret = $dsql->dsqlOper($sql, "totalCount");
	if($ret == 0){
	    //判断文件类型
        $ftype = explode('.', $reqUri);
        $ftype = $ftype[1];
        if(in_array($ftype, array('jpg', 'jpeg', 'gif', 'png', 'bmp'))){
            header("location:".$cfg_secureAccess.$cfg_basehost."/static/images/404.jpg?from=index_392&uri=" . $reqUri);
            die;
        }else {
            header("location:" . $cfg_secureAccess . $cfg_basehost . "?from=index_395&uri=" . $reqUri);
            die;
        }
	}
}

//引入独立业务
if(is_file(HUONIAOROOT."/api/private/index.php")){
	include(HUONIAOROOT."/api/private/index.php");
}

//当前登录会员ID
$userid = $userLogin->getMemberID();

if($_GET['fromShare']){
	PutCookie('fromShare', $_GET['fromShare'], $cfg_onlinetime * 60 * 60);
}
//微信JSSDK
if(isWeixin() && isMobile() && $cfg_wechatAppid && $cfg_wechatAppsecret){
	$handler = false;
	$jssdk = new WechatJSSDK($cfg_wechatAppid, $cfg_wechatAppsecret);
	$signPackage = $jssdk->getSignPackage();
	$huoniaoTag->assign('wxjssdk_appId', $signPackage['appId']);
	$huoniaoTag->assign('wxjssdk_timestamp', $signPackage['timestamp']);
	$huoniaoTag->assign('wxjssdk_nonceStr', $signPackage['nonceStr']);
	$huoniaoTag->assign('wxjssdk_signature', $signPackage['signature']);


	//微信自动登录
	$connect_uid = GetCookie("connect_uid");
	$connect_code = GetCookie("connect_code");
	if($cfg_wechatAutoLogin && $userid == -1 && empty($connect_uid) && $template != "login" && $template != "logout" && $template != "register" && $template != "fpwd" && $template != "registerCheck_v1" && $template != "security" && $template != "loginCheck"){
		$_SESSION['loginRedirect'] = $cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
		$_SESSION['state'] = md5(uniqid(rand(), TRUE));
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=";
		$redirect_uri = urlencode($cfg_secureAccess.$cfg_basehost."/api/login.php?action=back&type=wechat");
		$scope = $cfg_wechatType == "1" ? "snsapi_userinfo" : "snsapi_base";
		$login_url = $url.$cfg_wechatAppid."&redirect_uri=".$redirect_uri."&response_type=code&scope=".$scope."&state=".$_SESSION['state']."#wechat_redirect";
		header("Location:$login_url");
		die;
	}

	//如果已经登录了，并且是通过扫码的，直接绑定分销关系
	$fromShare_ = GetCookie('fromShare');
	if($fromShare_ && $userid > 0){
		$userLogin->registGiving($userid, false);
	}

	//已经登录但没有绑定手机的，继续跳转到绑定页面
	if(!empty($connect_uid) && !empty($connect_code) && $template != "bindMobile" && $cfg_wechatBindPhone && $template != "login" && $template != "logout" && $template != "register" && $template != "fpwd" && $template != "registerCheck_v1" && $template != "security"){
		if($userid > -1){
			PutCookie("connect_uid", "");
			if(!isset($_SESSION['loginRedirect'])){
				header("location:".$cfg_secureAccess.$cfg_basehost);
			}else{
				header("location:" . $_SESSION['loginRedirect']);
			}
			die;
		}elseif($userid == -1){
			PutCookie("connect_uid", "");
			header("location:".$cfg_secureAccess.$cfg_basehost . "/login.html");
			die;
		}else{
			$_SESSION['loginRedirect'] = $cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
			header('Location:'.$cfg_secureAccess.$cfg_basehost.'/bindMobile.html?type='.$connect_code);
			die;
		}
	}
}

//入口检测
$siteCityInfoCookie = GetCookie('siteCityInfo');
if($siteCityInfoCookie && empty($city)){
	$siteCityInfoJson = json_decode($siteCityInfoCookie, true);
	if(is_array($siteCityInfoJson)){
		$siteCityInfo = $siteCityInfoJson;
	}
}
if($module != "website" && $do != "courier"){
	$siteCityInfo = checkSiteCity();
}

//当前城市信息
if($siteCityInfo){
	$city = $siteCityInfo['domain'];
	$siteCityName = $siteCityInfo['name'];

	$huoniaoTag->assign('siteCityInfo', $siteCityInfo);
	$huoniaoTag->assign('siteCityInfoArr', json_encode($siteCityInfo));

	if(!$siteCityAdvancedConfig && $siteCityInfo['cityid']){
	    $sql = $dsql->SetQuery("SELECT `config` FROM `#@__site_city` WHERE `cid` = " . $siteCityInfo['cityid']);
	    $ret = $dsql->dsqlOper($sql, "results");
	    if(is_array($ret)){
	        $advancedConfig = $ret[0]['config'] ? $ret[0]['config'] : '';
	        $advancedConfigArr = $advancedConfig ? unserialize($advancedConfig) : array();
	        if($advancedConfigArr){
	            $siteCityAdvancedConfig = $advancedConfigArr;
	        }
	    }
	}
}


//访问独立域名时 获取分站信息
if($dopost == "getSiteCityInfo"){
	echo $siteCityInfo['cityid'];
	die;
}


//引入当前模块配置文件
if($service != "siteConfig" && $service != "member"){
	$serviceInc = $config_path.$service.".inc.php";
	if(file_exists($serviceInc)){
		include $serviceInc;
	}else{
		die("<center><br /><br />服务名不存在！<br /><br />The service name does not exist!</center>");
	}
}

//声明以下均为接口类
$handler = true;

//获取当前模块配置参数
$configHandels = new handlers($service, "config");
$moduleConfig  = $configHandels->getHandle();

if(!is_array($moduleConfig) && $moduleConfig['state'] != 100) die('<center><br /><br />模块数据获取失败！<br /><br />Module data acquisition failed!</center>');
$moduleConfig  = $moduleConfig['info'];

//如果系统配置了子频道为大首页、当访问大首页时自动跳转至子频道域名，前提是子频道设置的为二级域名，如果不做跳转，同步登录和登录浮动窗口为报错误
$moduleDomain = $moduleConfig['channelDomain'];
if($moduleConfig['subDomain'] == 1 && !empty($cfg_defaultindex) && $cfg_defaultindex != "siteConfig" && $cfg_defaultindex == $service && ($httpHost != str_replace("http://", "", $moduleDomain) || $httpHost != str_replace("https://", "", $moduleDomain))){
	header("location:".$moduleDomain."?from=index_459");  //检查模块域名类型是否与分站域名类型相符
	die;
}

//地图配置
$module_map =  $moduleConfig['map'];
if(!empty($module_map)){
    $cfg_map = $module_map;
}

switch ($cfg_map) {
    case 1:
        $site_map = "google";
        $site_map_key = $cfg_map_google;
        $site_map_apiFile = $cfg_secureAccess . "maps.googleapis.com/maps/api/js?key=".$site_map_key."&sensor=false&libraries=places";
        break;
    case 2:
        $site_map = "baidu";
        $site_map_key = $cfg_map_baidu;
        $site_map_apiFile = $cfg_secureAccess . "api.map.baidu.com/api?v=2.0&ak=".$site_map_key;
        break;
    case 3:
        $site_map = "qq";
        $site_map_key = $cfg_map_qq;
        $site_map_apiFile = $cfg_secureAccess . "map.qq.com/api/js?key=".$cfg_map_qq."&libraries=drawing";
        break;
    case 4:
        $site_map = "amap";
        $site_map_key = $cfg_map_amap;
        $site_map_apiFile = $cfg_secureAccess . "webapi.amap.com/maps?v=1.3&key=".$site_map_key;
        break;
    default:
        $site_map = "baidu";
        $site_map_key = $cfg_map_baidu;
        $site_map_apiFile = $cfg_secureAccess . "api.map.baidu.com/api?v=2.0&ak=".$site_map_key;
        break;
}

$huoniaoTag->assign('site_map', $site_map);
$huoniaoTag->assign('site_map_key', $site_map_key);
$huoniaoTag->assign('site_map_apiFile', $site_map_apiFile);


//输入当前模块配置参数
$configName = array_keys($moduleConfig);
foreach ($configName as $config) {
	$huoniaoTag->assign($service.'_'.$config, $moduleConfig[$config]);
}

//注册当前模块函数
$contorllerFile = dirname(__FILE__).'/api/handlers/siteConfig.controller.php';
if(file_exists($contorllerFile)){
	require_once($contorllerFile);
	$huoniaoTag->registerPlugin("block", "siteConfig", "siteConfig");
}


//自助建站独立域名选项
//需要将php.ini中的allow_url_include开启
if($module == "website" && !empty($domainPart) && !empty($domainIid)){

	//电脑端
	if(!isMobile()){
		//获取URL参数
		$urlParam = array();
		foreach(Array('_GET','_POST') as $_request){
			foreach($$_request as $_k => $_v){
				if($_k != 'template'){
					array_push($urlParam, $_k . "=" . RemoveXSS($_v));
				}
			}
		}

		include($cfg_secureAccess.$cfg_basehost."/website.php?id={$domainIid}&alias={$template}" . ($urlParam ? "&" . join("&", $urlParam) : ""));
		die;

	//移动端跳转
	}else{
		$param = array(
			"service"      => "website",
			"template"     => "site".$domainIid
		);
		$url = getUrlPath($param);
		header("location:".$url);
		die;
	}
}

checkModuleState(array("visitState" => $cfg_visitState, "visitMessage" => $cfg_visitMessage));
//普通频道
if($service != "member"){

	//检查模块状态
	checkModuleState($moduleConfig);

	//设置模板目录
	$tplFloder = $moduleConfig['template'];
	$touchTplFloder = $moduleConfig['touchTemplate'];

    //分站自定义配置
    if($siteCityAdvancedConfig && $siteCityAdvancedConfig[$service]){
        if($siteCityAdvancedConfig[$service]['template']){
            $tempFloder = $siteCityAdvancedConfig[$service]['template'];
            if(file_exists(HUONIAOROOT.'/templates/' . $service . '/' . $tempFloder . '/config.xml')) {
                $tplFloder = $tempFloder;
            }
        }
        if($siteCityAdvancedConfig[$service]['touchTemplate']){
            $tempFloder = $siteCityAdvancedConfig[$service]['touchTemplate'];
            if(file_exists(HUONIAOROOT.'/templates/' . $service . '/touch/' . $tempFloder . '/config.xml')) {
                $touchTplFloder = $tempFloder;
            }
        }
    }

	if(!empty($skin)) $tplFloder = $skin;

	$touchTplFloder = empty($touchTplFloder) ? "default" : $touchTplFloder;

	$ser = $service;
	$tplFloder = $tplFloder . "/";
	$touchTplFloder = $touchTplFloder . "/";

	// 自助建站（移动端）不作下面的验证：$template有冲突
	if($service != "website" || !isMobile()){
		//统计模块数量
		$moduleCount = 0;
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_module` WHERE `state` = 0 AND `type` = 0 AND `name` != ''");
		$moduleCount = $dsql->dsqlOper($sql, "totalCount");

		//404、mobile页面
		if($template == "404" || $template == "mobile" || $template == "changecity" || $template == "appindex" || $template == "tcquan" || $template == "post" || ($cfg_defaultindex != 'siteConfig' && ($template == 'search' || $template == 'search-list') && isMobile())){
			$tplFloder = "";

			if(($cfg_defaultindex != 'siteConfig' && $template == 'search') || $template != 'search'){
				$touchTplFloder = "";
				$ser = 'siteConfig';
			}

			if(isMobile() && $template != "appindex" && $template != "tcquan" && $template != "post" && $template != "search" && $template != "search-list"){
				$touchTplFloder = "";
				$templates = $template . "_touch.html";
			}elseif((count($siteModule) > 1 && $template == 'search') || $template != 'search'){
				$touchTplFloder = "";
				include HUONIAOINC . "/config/siteConfig.inc.php";
				$touchTplFloder = $cfg_touchTemplate . "/";
			}elseif($template == 'search' || $template == 'search-list'){
                include HUONIAOINC . "/config/siteConfig.inc.php";
                $touchTplFloder = $cfg_touchTemplate . "/";
			    $tplFloder = 'touch/' . $touchTplFloder;
            }
		}

		//单页、帮助、协议、公告
		if($template == "about" || $template == "help" || $template == "help-detail" || $template == "notice" || $template == "notice-detail" || $template == "protocol" || $template == "app" || $template == "feedback"){
			$ser = $template == "help-detail" ? "help" : ($template == "notice-detail" ? "notice" : $template);
			$tplFloder = "";
			$touchTplFloder = "";

			$service = 'siteConfig';

			//如果只有一个模块
			if(count($siteModule) == 1){
				//获取当前模块配置参数
				$configHandels = new handlers($service, "config");
				$moduleConfig  = $configHandels->getHandle();

				if(!is_array($moduleConfig) && $moduleConfig['state'] != 100) die('<center><br /><br />模块数据获取失败！<br /><br />Module data acquisition failed!</center>');
				$moduleConfig  = $moduleConfig['info'];

				//如果系统配置了子频道为大首页、当访问大首页时自动跳转至子频道域名，前提是子频道设置的为二级域名，如果不做跳转，同步登录和登录浮动窗口为报错误
				$moduleDomain = $moduleConfig['channelDomain'];
				if($moduleConfig['subDomain'] == 1 && !empty($cfg_defaultindex) && $cfg_defaultindex != "siteConfig" && $cfg_defaultindex == $service && ($httpHost != str_replace("http://", "", $moduleDomain) || $httpHost != str_replace("https://", "", $moduleDomain))){
					header("location:".$moduleDomain);
					die;
				}

				//输入当前模块配置参数
				$configName = array_keys($moduleConfig);
				foreach ($configName as $config) {
					$huoniaoTag->assign($service.'_'.$config, $moduleConfig[$config]);
				}

				//注册当前模块函数
				// $contorllerFile = dirname(__FILE__).'/api/handlers/'.$service.'.controller.php';
				// if(file_exists($contorllerFile)){
				// 	require_once($contorllerFile);
				// 	$huoniaoTag->registerPlugin("block", $service, $service);
				// }
			}

		}
	}

	//自助建站移动版判断用户使用模板
	if($service == "website"){
		// 移动端并且是站点
		if(isMobile() && $id){
			$tplFloder = "skin1/";

			$sql = $dsql->SetQuery("SELECT `touch_temp` FROM `#@__website` WHERE `id` = $id");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$skin = "";
				if($ret[0]['touch_temp']){
					$skin = $ret[0]['touch_temp'];
					$skin_d = "./templates/website/touch/".$skin;
					if(!is_dir($skin_d)) $skin = "";
				}
				if($skin == ""){
					$dir = "./templates/website/touch";
					$floders = listDir($dir);
					if(!empty($floders)){
				    	$skin = $floders[0];
			    	}
			    }

				$tplFloder = $skin . "/";
			}
			$tpl = "/templates/" . $ser . "/touch/" . $tplFloder;
		}else{
			$tpl = "/templates/" . $ser . "/" . $tplFloder;
		}
	}else{
		$tpl = "/templates/" . $ser . "/" . ((isMobile() && (!empty($touchTplFloder) || $template == "about" || $template == "protocol" || $template == "help" || $template == "help-detail" || $template == "notice" || $template == "notice-detail" || $template == "feedback")) ? "touch/".$touchTplFloder : $tplFloder);
	}

	//APP Page Config
	$tpl = $template == "app" ? $tpl . $type . "/" : $tpl;
	$templates = $template == "app" ? $page.".html" : $templates;


//会员频道
}else{

	// 会员中心绑定主域名时
	if(!isMobile() && !$isSystemPage && (($domainPart == 'user' && $cfg_userSubDomain == 0) || ($domainPart == 'busi' && $cfg_busiSubDomain == 0))){
		$r = $userLogin->checkUserIsLogin("result");
		if($r['uid'] < 0){
			$template = "ssoUserCenter";
			$templates = $template.".html";
			$huoniaoTag->assign('changeUser', $r['changeUser']);
			$huoniaoTag->assign('errorUrl', $r['url']);
			$huoniaoTag->assign('succUrl', $furl);	//登陆成功后跳转页面
		}
	}

	$param = array("service" => "member",	"type" => "user");
	$userDomain = getUrlPath($param);
	$param = array("service"  => "member");
	$busiDomain = getUrlPath($param);

	//判断访问类型
	$ischeck = explode($busiDomain, $dirDomain);

	//如果是访问的企业会员域名，模板选择企业会员的模板
	if(count($ischeck) > 1 && (substr($ischeck[1], 0, 1) == "/" || substr($ischeck[1], 0, 1) == "") && $template != "ssoUserRedirect" && $template != "ssoUser" && $template != "sso" && $template != "ssoUserCenter"){
		$tpl = "/templates/member/company/";
        $huoniaoTag->assign('userTemplateType', 2);
	}else{
		$tpl = "/templates/member/";
        $huoniaoTag->assign('userTemplateType', 1);
	}

	if($template == "enter" && isMobile()){
		$templates = "fabuJoin_touch_popup_3.4.html";
	}

	$tpl .= isMobile() && $template != "ssoUserRedirect" && $template != "ssoUser" && $template != "ssoUserCenter" ? "touch/" : "";

}


//遍历所有模块配置文件
//此处是为了让整站在任何模板中通过{#$service_configItem#}的方式直接调取指定频道的基本信息；
//如获取团购频道的名称和域名：{#$tuan_channelName#}，{#$tuan_channelDomain#}
//当前默认只输出：模块名、模块链接，两个参数，如果要输出更多信息，请修改：$sNameParam变量的内容，清空或增加
$config_dir = opendir($config_path);
while(($file = readdir($config_dir)) !== false){
	$sName = str_replace(".inc.php", "", $file);
	$sub_dir = $config_path . $file;
  if($file == '.' || $file == '..' || $sName == "pointsConfig" || $sName == "qiandaoConfig" || $sName == "wechatConfig" || $sName == "settlement" || $sName == "refreshTop"){
      continue;
	}else if(file_exists($sub_dir)){

		//获取功能模块配置参数
		$sNameParam = $sName == "siteConfig" || $sName == "member" ? "" : "channelName,channelDomain";

		if(($sName != 'siteConfig' && $sName != 'member' && $sName != 'business') && !in_array($sName, $installModuleArr)){
			continue;
		}

		$sNameHandels = new handlers($sName, "config");
		$sNameConfig  = $sNameHandels->getHandle($sNameParam);

		if(is_array($sNameConfig) && $sNameConfig['state'] == 100){
			$sNameConfig  = $sNameConfig['info'];

			//输出配置信息
			$sConfigName = array_keys($sNameConfig);
			foreach ($sConfigName as $config) {
				$huoniaoTag->assign($sName.'_'.$config, $sNameConfig[$config]);
			}

			//注册函数
			$contorllerFile = dirname(__FILE__).'/api/handlers/'.$sName.'.controller.php';
			if(file_exists($contorllerFile)){
				require_once($contorllerFile);
				$huoniaoTag->registerPlugin("block", $sName, $sName);
			}
	 	}

    }
}


//商家移动端详情页区分体验版和企业版
if($service == "business" && (is_numeric($_REQUEST['id']) || is_numeric($_REQUEST['bid']))){
	//查询商家是否有开启自定义模板
	$businessDetailID = $_REQUEST['id'];
	if(is_numeric($_REQUEST['bid'])){
		$businessDetailID = $_REQUEST['bid'];
	}
	if($templates == "detail.html"){
		$sql = $dsql->SetQuery("SELECT `touch_skin`, `type` FROM `#@__business_list` WHERE `id` = $businessDetailID LIMIT 1");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret && is_array($ret)){
			if($ret[0]['type'] == 1){
				$templates = "free.html";
				// include_once HUONIAOINC . "/config/huangye.inc.php";
				// $tpl = '/templates/huangye/touch/' . $customTouchTemplate . '/';
			}
		}
	}
}

if(isset($huoniaoTag->tpl_vars['tuan_channelDomain'])){
	$huoniaoTag->assign('tuanDomain', $huoniaoTag->tpl_vars['tuan_channelDomain']);
}

//团购单独设置
// if($service == "tuan_tuan"){
//
// 	//当前城市域名
// 	$city = str_replace("/", "", $city);
// 	require($serviceInc);
// 	$tuanService = new tuan();
// 	$domainInfo = $tuanService->getCity();
// 	if(!empty($domainInfo)){
//
// 		$tuanDomain = $domainInfo['url'];
// 		$huoniaoTag->assign('city', $city);   //城市拼音
// 		$huoniaoTag->assign('cityid', $domainInfo['cid']);  //城市ID
// 		$huoniaoTag->assign('cityname', $domainInfo['typename']);  //城市名称
// 		$huoniaoTag->assign('tuanDomain', $tuanDomain);  //城市域名
//
// 		PutCookie("tuan_city", $city, 86400 * 365);
//
// 		//自动跳转到城市首页
// 		if(!empty($city) && ($template == "" || $template == "changecity") && $do != "initiative"){
// 			header("location:".$tuanDomain);die;
// 		}
//
// 	//城市为空时直接访问选择城市页
// 	}else{
// 		$templates = "changecity.html";
// 	}
//
// }


//执行当前页面指定的函数：$template
foreach ($_REQUEST as $key => $value) {
	if(is_array($value)){
		$params[$key] = $value;
	}else{
		$params[$key] = addslashes(htmlspecialchars(RemoveXSS($value)));
	}
}
$params['action'] = $template;
$params['template'] = $template;
$service($params);


//会员状态
if($userid > -1 && $template != "logout"){

	if($template == "resetpwd"){
		header("location://".$cfg_basehost);
		die;
	}

	$userLogin->keepUserID = $userLogin->keepMemberID;
	$userLogin->keepUser();
	$userinfo = $userLogin->getMemberInfo();
	$huoniaoTag->assign('userinfo', $userinfo);

	//如果是企业会员，获取企业会员开通的模块
	$userOpenModules = array();
	$notOpenModules = array();
	if($userinfo['userType'] == 2){
		$now = time();
		$modulesNameArr = array();
		// $sql = $dsql->SetQuery("SELECT `id`, `module`, `expired` FROM `#@__business_order_module` WHERE `id` IN (SELECT SUBSTRING_INDEX(group_concat(m.`id` ORDER BY m.`expired` DESC), ',', 1) FROM `#@__business_order_module` m LEFT JOIN `#@__business_order` o ON o.`id` = m.`oid` LEFT JOIN `#@__business_list` l ON l.`id` = o.`bid` WHERE l.`uid` = $userid AND m.`expired` >= $now group by m.`module`) ORDER BY `expired` ASC");
		// $ret = $dsql->dsqlOper($sql, "results");
		// if($ret && is_array($ret)){
		// 	foreach ($ret as $key => $value) {
		// 		$t = floor(($value['expired'] - time()) / 86400);
		// 		$ret[$key]['expired'] = $t && $t < 30 ? ($t == 0 ? $langData['siteConfig'][13][24] . date("H:i:s", $value['expired']) : $t . $langData['siteConfig'][13][30]) : date("Y-m-d H:i:s", $value['expired']);
		// 		array_push($modulesNameArr, $value['module']);
		// 	}
		// 	$userOpenModules = $ret;
		// }

		// $costArr = array();
		// include HUONIAOINC . '/config/business.inc.php';
		// if($customCost){
		// 	$costArr = unserialize($customCost);
		// }

		//未开通的模块
		// if($costArr){
		// 	foreach ($costArr as $key => $value) {

		// 		//如果已经开通的，将价格插入数组中
		// 		if(in_array($key, $modulesNameArr)){
		// 			foreach ($userOpenModules as $k => $v) {
		// 				if($v['module'] == $key){
		// 					$userOpenModules[$k]['price'] = $value;
		// 				}
		// 			}

		// 		//没有开通的，单独存放
		// 		}else{
		// 			array_push($notOpenModules, array("module" => $key, "price" => $value));
		// 		}

		// 	}
		// }

		// 会员中心首页区分模板
		if(isMobile() && $templates == "index.html" && strpos($tpl, "member/company")){
			if($userinfo['busiType'] == 1){
				$templates = "free.html";
			}elseif($userinfo['busiType'] == 2){
				$templates = "index.html";
			}else{

			}
		}

	}
	// $huoniaoTag->assign('userOpenModules', $userOpenModules);
	// $huoniaoTag->assign('notOpenModules', $notOpenModules);

	// $userOpenModules_name = array_column($userOpenModules, 'module');
	// $huoniaoTag->assign('userOpenModules_name', $userOpenModules_name);

}


//验证码规则
global $cfg_seccodestatus;
$seccodestatus = explode(",", $cfg_seccodestatus);
$loginCode = "";
if(in_array("login", $seccodestatus)){
	$loginCode = 1;
}
$huoniaoTag->assign('loginCode', $loginCode);


//统计会员数量及在线人数
$memberStatistics = array();
$sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member` WHERE `mtype` = 1 AND `state` = 1 UNION SELECT COUNT(`id`) total FROM `#@__member` WHERE `mtype` = 2 AND `state` = 1");
$ret = getCache("member", $sql, 600, array("sign" => "total"));
$memberStatistics['total'] = $ret[0]['total'] + $ret[1]['total'];
$sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member` WHERE `online` > 0 AND `mtype` = 1 AND `state` = 1 UNION SELECT COUNT(`id`) total FROM `#@__member` WHERE `online` > 0 AND `mtype` = 2 AND `state` = 1");
$ret = getCache("member", $sql, 400, array("sign" => "online"));
$memberStatistics['online'] = $ret[0]['total'] + $ret[1]['total'];

$huoniaoTag->assign('memberStatistics', $memberStatistics);


//统计已入驻商家数量
$businessSettledCount = 0;
$sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__business_list` WHERE `state` = 1");
$ret = $dsql->dsqlOper($sql, "results");
if($ret){
	$businessSettledCount = $ret[0]['total'];
}
$huoniaoTag->assign('businessSettledCount', $businessSettledCount);


//外卖配送
if($service == "waimai" && $do == "courier"){
	$tpl = "/templates/courier/";
}

//IM聊天
if($service == 'member' && strstr($reqUri, 'im/')){
	$tpl .= 'im/';
}

//验证模板文件
$tplDir = HUONIAOROOT.$tpl;
if(file_exists($tplDir.$templates)){

	$huoniaoTag->assign('city', $city);
	$page = $page ? $page : ($_REQUEST['page'] ? $_REQUEST['page'] : 1);
	$huoniaoTag->template_dir = $tplDir;
	$huoniaoTag->assign('page', empty($page) && !isset($_REQUEST['page']) ? 1 : $page);   //当前页码

	if($cfg_remoteStatic){
		$huoniaoTag->assign('templets_skin', $cfg_remoteStatic.$tpl);  //模块路径
		$huoniaoTag->assign('cfg_staticPath', $cfg_remoteStatic . '/static/');  //静态资源路径
	}else{
		$huoniaoTag->assign('templets_skin', $cfg_secureAccess.$cfg_basehost.$tpl);  //模块路径
		$huoniaoTag->assign('cfg_staticPath', $cfg_staticPath);  //静态资源路径
	}
	$huoniaoTag->assign('cfg_staticVersion', $cfg_staticVersion);  //静态资源版本号
	$huoniaoTag->assign('cfg_hideUrl', $cfg_hideUrl);        //是否隐藏静态资源路径
	$huoniaoTag->assign('template', $template);    //当前模板
	$huoniaoTag->assign('service', $service);      //当前模块
	$huoniaoTag->assign('search_keyword', $search_keyword);  //搜索关键字
	$huoniaoTag->assign('backModule', $backModule);  //来源模块

	//如果是选择城市页面，不需要进行城市关键字替换
	if($templates == 'changecity.html'){
		$siteCityName = '';

		//定位当前城市
		$cityData = getIpAddr(getIP(), 'json');
		if(is_array($cityData)){
			$siteConfigService = new siteConfig(array(
				'province' => $cityData['region'],
				'city' => $cityData['city']
			));
			$cityInfo = $siteConfigService->verifyCity();
			if(is_array($cityInfo) && $cityInfo['name']){
				$huoniaoTag->assign('cityInfo', $cityInfo);
			}
		}
	}

	//分站自定义配置
    if($siteCityAdvancedConfig && $siteCityAdvancedConfig['siteConfig']){
        if($siteCityAdvancedConfig['siteConfig']['webname']) {
            $cfg_webname = $siteCityAdvancedConfig['siteConfig']['webname'];
        }
        if($siteCityAdvancedConfig['siteConfig']['weblogo']) {
            $cfg_weblogo = $siteCityAdvancedConfig['siteConfig']['weblogo'];
        }
        if($siteCityAdvancedConfig['siteConfig']['keywords']) {
            $cfg_keywords = $siteCityAdvancedConfig['siteConfig']['keywords'];
        }
        if($siteCityAdvancedConfig['siteConfig']['description']) {
            $cfg_description = $siteCityAdvancedConfig['siteConfig']['description'];
        }
        if($siteCityAdvancedConfig['siteConfig']['hotline']) {
            $cfg_hotline = $siteCityAdvancedConfig['siteConfig']['hotline'];
        }
        if($siteCityAdvancedConfig['siteConfig']['statisticscode']) {
            $cfg_statisticscode = $siteCityAdvancedConfig['siteConfig']['statisticscode'];
        }
        if($siteCityAdvancedConfig['siteConfig']['powerby']) {
            $cfg_powerby = $siteCityAdvancedConfig['siteConfig']['powerby'];
        }
    }

	//系统配置参数
	$huoniaoTag->assign("cfg_webname",        str_replace('$city', $siteCityName, stripslashes($cfg_webname)));
	$huoniaoTag->assign("cfg_shortname",      str_replace('$city', $siteCityName, stripslashes($cfg_shortname)));

	$huoniaoTag->assign("cfg_weblogo",        getFilePath($cfg_weblogo));
	$huoniaoTag->assign("cfg_keywords",       str_replace('$city', $siteCityName, stripslashes($cfg_keywords)));
	$huoniaoTag->assign("cfg_description",    str_replace('$city', $siteCityName, stripslashes($cfg_description)));
	$huoniaoTag->assign("cfg_beian",          stripslashes($cfg_beian));
	$huoniaoTag->assign("cfg_hotline",        stripslashes($cfg_hotline));
	$huoniaoTag->assign("cfg_powerby",        str_replace('$city', $siteCityName, stripslashes($cfg_powerby)));
	$huoniaoTag->assign("cfg_statisticscode", str_replace("></script>'", "><\/script>'", stripslashes($cfg_statisticscode)));
	$huoniaoTag->assign("cfg_mapCity",        $cfg_mapCity);
	$huoniaoTag->assign("cfg_weatherCity",    $cfg_weatherCity);
	$huoniaoTag->assign("cfg_template",       $cfg_template);
	$huoniaoTag->assign("cfg_cookieDomain",   $cfg_cookieDomain);
	$huoniaoTag->assign("cfg_cookiePre",      $cfg_cookiePre);
	$huoniaoTag->assign("cfg_bbsUrl",         $cfg_bbsUrl);
	$huoniaoTag->assign("cfg_server_tel",     empty($cfg_server_tel) ? array() : explode(",", $cfg_server_tel));
	$huoniaoTag->assign("cfg_server_qq",      empty($cfg_server_qq) ? array() : explode(",", $cfg_server_qq));
	$huoniaoTag->assign("cfg_server_wx",      $cfg_server_wx);
	$huoniaoTag->assign("cfg_server_wxQr",    empty($cfg_server_wxQr) ? "" : getFilePath($cfg_server_wxQr));

	//是否APP端
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$is_app = preg_match("/huoniao/", $useragent) ? 1 : 0;
	$huoniaoTag->assign('is_app', $is_app);

	//是否苹果APP端
	$is_ios_app = preg_match("/huoniao_ios/", $useragent) ? 1 : 0;
	$huoniaoTag->assign('is_ios_app', $is_ios_app);

	//是否安卓APP端
	$is_android_app = preg_match("/huoniao_android/", $useragent) ? 1 : 0;
	$huoniaoTag->assign('is_android_app', $is_android_app);

	//是否微信端
	$huoniaoTag->assign('isWeixin', isWeixin());

	//是否小程序端
	$isWxMiniprogram = GetCookie('isWxMiniprogram');
	$huoniaoTag->assign('isWxMiniprogram', $isWxMiniprogram);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/compiled/".$service."/".(isMobile() ? "touch/" : "").$template;  //设置编译目录

	$huoniaoTag->display($templates);

	//移动端统计
	if(isMobile()){
		echo '<div style="display: none;">'.str_replace("></script>'", "><\/script>'", stripslashes($cfg_statisticscode)).'</div>';
	}


	//移动端自定义分享LOGO
    if(isMobile()) {
        $sharePic = $cfg_sharePic;
        if($service != 'siteConfig' && $customSharePic){
            $sharePic = $customSharePic;
        }
        echo '<script>var shareAdvancedUrl = "'.getFilePath($sharePic).'";</script>';
    }

    //微信端隐藏域名
	if(isWeixin()) {
        echo '<div style="position: fixed; left: 0; top: 0; right: 0; z-index: -1; height: 2rem; background-image: -webkit-linear-gradient( -90deg, rgb(255,255,255) 25%, rgba(255,255,255,0) 100%);"></div>';
    }
    if($userinfo){
    	PutCookie('userid', $userinfo['userid'], $cfg_onlinetime * 60 * 60);
    }else{
    	DropCookie('userid');
    }

	echo "<!-- Processed in page load ".number_format((microtime(true) - sysBtime), 6)." second(s), ".$dsql->querynum." queries ,".$dsql->querytime." second(s) -->";
	// echo "<!-- Processed in ".$dsql->querytime." second(s), ".$dsql->querynum." queries -->";

//	 echo $dsql->querysql;  //输出页面中用到的SQL

    //按使用数量排序
	// $sqls = explode("<br />", $dsql->querysql);
	// $arrs = array();
	// $list = array();
	// foreach ($sqls as $q){
	//    if(!in_array($q, $arrs)){
	//        array_push($arrs, $q);
	//        array_push($list, array(
	//            1, $q
	//        ));
	//    }else{
	//        foreach ($list as $k => $v){
	//            if($v[1] == $q){
	//                $list[$k][0] += 1;
	//            }
	//        }
	//    }
	// }
	// $c = array_column($list, 0);
	// array_multisort($c, SORT_DESC, $list);
	// foreach ($list as $item) {
	//    echo $item[0] . "_____" . $item[1] . "<br />";
	// }

}else{
	if($cfg_siteDebug){
		die("The requested URL '".$tplDir.$templates."' was not found on this server.");
	}else{
		header ("location:/404.html");
		die;
	}
}
