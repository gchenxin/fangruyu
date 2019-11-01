<?php
/**
 * 系统核心配置文件
 *
 * @version        $Id: common.inc.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.Libraries
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
ob_start();
error_reporting(E_ALL & ~E_NOTICE);

//系统全局变量
define('HUONIAOINC', str_replace("\\", '/', dirname(__FILE__) ) );         //当前目录
define('HUONIAOROOT', str_replace("\\", '/', substr(HUONIAOINC,0,-8) ) );  //根目录
define('HUONIAODATA', HUONIAOROOT.'/data');                                //系统配置目录
define('sysBtime', microtime(true));
define("ARTICLE_TABLE_SIZE", 100000);
//软件摘要信息
$cfg_softname     = '火鸟网站管理系统';              //软件中文名
$cfg_soft_enname  = 'HuoNiaoCMS';                  //软件英文名
$cfg_soft_devteam = 'HuoNiaoCMS官方团队';           //软件团队名
//$cfg_version      = 'V1_SP1';                    //软件版本
$cfg_soft_lang    = 'utf-8';                       //软件语言

header("Content-Type: text/html; charset={$cfg_soft_lang}");
header('Cache-Control: private');  //指定浏览器请求和响应遵循的缓存机制
// header('X-Frame-Options: SAMEORIGIN');  //页面只能被本站页面嵌入到iframe或者frame中。开启后将影响多域名同步登录功能

// 定义全局变量
$_G = [];

//系统基本配置信息
include_once(HUONIAOINC.'/config/siteConfig.inc.php');    //系统配置参数
include_once(HUONIAOINC.'/config/pointsConfig.inc.php');  //会员积分配置
include_once(HUONIAOINC.'/config/wechatConfig.inc.php');  //微信基本配置
include_once(HUONIAOINC.'/config/settlement.inc.php');    //会员结算配置
include_once(HUONIAOINC.'/config/qiandaoConfig.inc.php'); //签到规则配置
@include_once(HUONIAOINC.'/config/fenxiaoConfig.inc.php'); //分销配置

define('HUONIAOBUG', (int)$cfg_siteDebug); //开启调试
ini_set('display_errors', $cfg_siteDebug ? 'On' : 'Off'); //Debug设置

//安全协议
$cfg_secureAccess = $cfg_httpSecureAccess ? 'https://' : 'http://';

//http强制跳转
//如果访问协议与网站配置不符，将强制跳转至网站配置的协议
//如果接口必须使用https，但是网站却配置了http，可以在接口参数中增加https=1配置
$currentHttp = 'http';
if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (isset($_SERVER['HTTP_X_CLIENT_SCHEME']) && $_SERVER['HTTP_X_CLIENT_SCHEME'] == 'https')){
	if(!$cfg_httpSecureAccess && !$_GET['https']){
        header("location:http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]);
    die;
  }
	$currentHttp = 'https';
}else {
    if ($cfg_httpSecureAccess && !strstr($_SERVER['PHP_SELF'], 'appConfig.json') && !$_GET['signature']) {
        header("location:https://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]);
        die;
    }
}

//Session跨域设置
if(!empty($cfg_cookieDomain)){
  ini_set('session.cookie_path', '/');
  ini_set('session.cookie_domain', '.'.$cfg_cookieDomain);
  ini_set('session.cookie_lifetime', '1800');
  @session_set_cookie_params(0, '/', $cfg_cookieDomain);
}
session_start();
header("Cache-control: private");

//会员配置参数
include_once(HUONIAOINC.'/config/member.inc.php');

$cfg_attachment   = $cfg_secureAccess.$cfg_basehost.'/include/attachment.php?f=';  //附件访问地址
$cfg_staticPath   = $cfg_secureAccess.$cfg_basehost.'/static/'; //静态文件地址

//获取静态文件版本号
$m_file = HUONIAODATA."/admin/staticVersion.txt";
$cfg_staticVersion = time();
if(@filesize($m_file) > 0){
  $fp = @fopen($m_file,'r');
  $cfg_staticVersion = @fread($fp,filesize($m_file));
  fclose($fp);
}

//php5.1版本以上时区设置
//由于这个函数对于是php5.1以下版本并无意义，因此实际上的时间调用，应该用MyDate函数调用
if(PHP_VERSION > '5.1'){
    $time51 = $cfg_timeZone * -1;
    @date_default_timezone_set('Etc/GMT'.$time51);
}

//配置全局附件路径 $cfg_fileUrl
if($cfg_ftpState == 1){
	$cfg_fileUrl = $cfg_ftpUrl.str_replace(".", "", $cfg_ftpDir);
}else{
	$cfg_fileUrl = $cfg_uploadDir;
}

//转换上传的文件相关的变量及安全处理、并引用前台通用的上传函数
if($_FILES){
    include_once(HUONIAOINC.'/uploadsafe.inc.php');
}

//数据库配置文件
include_once(HUONIAOINC.'/dbinfo.inc.php');

//生成一个PDO对象
$dsn = "mysql:host=".$GLOBALS['DB_HOST'].";dbname=".$GLOBALS['DB_NAME'];
try{
	$_opts_values = array(PDO::ATTR_PERSISTENT=>true,PDO::ATTR_ERRMODE=>2,PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8mb4');
	$dbo = @new PDO($dsn, $GLOBALS['DB_USER'], $GLOBALS['DB_PASS'], $_opts_values);
}catch(Exception $e){
	//如果连接失败，输出错误
	if (HUONIAOBUG === TRUE){
		die($e->getMessage());
	}else{
		die('<center><br /><br />数据库链接失败，请检查配置信息！<br /><br />Database link failed. Check configuration information!</center>');
	}
}

//建立数据库连接
$dsql      = new dsql($dbo);
$userLogin = new userLogin($dbo);

//全局常用函数
include_once(HUONIAOINC.'/common.func.php');

//检测IP段
if(checkIpAccess(GetIP(), $cfg_iplimit) && !empty($cfg_iplimit)){
	die("<center><br /><br />您的IP已被限制！<br /><br />Your IP has been limited!</center>");
}

include_once(HUONIAOINC.'/class/aliyun-php-sdk-core/Config.php');   //引入阿里云SDK

//载入插件配置,并对其进行默认初始化
$cfg_plug_autoload = array(
	'charset',    /* 编码插件 */
	'string',     /* 字符串插件 */
	'time',       /* 日期插件 */
	'file',       /* 文件插件 */
	'util',       /* 单元插件 */
	'validate',   /* 数据验证插件 */
	'filter',     /* 过滤器插件 */
	'cookie',     /* cookies插件 */
	'upload',     /* 上传插件 */
	'debug',      /* 验证插件 */
	'myad',				/* 广告插件 */
	'cron',				/* 计划任务 */
	'SubTable',				/* 分表插件 */
	'FileCache',				/* 文件缓存插件 */
);
loadPlug($cfg_plug_autoload);

//Session保存路径
$sessSavePath = HUONIAODATA."/sessions/";
if(is_writeable($sessSavePath) && is_readable($sessSavePath)){
    @session_save_path($sessSavePath);
}

function _RunMagicQuotes(&$svar){
    if(!get_magic_quotes_gpc()){
        if( is_array($svar)){
            foreach($svar as $_k => $_v) $svar[$_k] = _RunMagicQuotes($_v);
        }else{
            if( strlen($svar)>0 && preg_match('#^(_GET|_POST|_COOKIE)#',$svar)){
              exit('Request var not allow!');
            }

            //先删除反斜杠，再增加反斜杠，如果不先删除，多提交几次就会出现：\\\\\\\'这种情况
            $svar = addslashes(stripslashes($svar));
        }
    }
    return $svar;
}

//检查和注册外部提交的变量
function CheckRequest(&$val){
	if (is_array($val)){
		foreach ($val as $_k=>$_v) {
			if($_k == 'nvarname') continue;
			CheckRequest($_k);
			CheckRequest($val[$_k]);
		}
	}else{
		if( strlen($val)>0 && preg_match('#^(_GET|_POST|_COOKIE)#',$val)){
			exit('Request var not allow!');
		}
	}
}

//var_dump($_REQUEST);exit;
CheckRequest($_REQUEST);

if(!defined('HUONIAOADMIN')) {
	define('HUONIAOADMIN', "");
}
foreach(Array('_GET','_POST','_COOKIE') as $_request){
	foreach($$_request as $_k => $_v){
		if($_k == 'nvarname') ${$_k} = $_v;
		else ${$_k} = _RunMagicQuotes($_v);

		//get请求过滤
		if($_request == '_GET' && $_v != 'getDatabaseStructure' && !HUONIAOADMIN){
			${$_k} = RemoveXSS(${$_k});
		}

		//搜索关键字过滤
		if((strstr($_k, 'title') || strstr($_k, 'keyword')) && !HUONIAOADMIN){
			${$_k} = RemoveXSS(${$_k});
		}
	}
}

//如果session没有防跨站请求标记则生成一个
if(!isset($_SESSION['token'])){
	$_SESSION['token'] = sha1(uniqid(mt_rand(), TRUE));
}


//获取当前城市

//获取访问详情  兼容win
$reqUri = $_SERVER["HTTP_X_REWRITE_URL"];
if($reqUri == null){
	$reqUri = $_SERVER["HTTP_X_ORIGINAL_URL"];
	if($reqUri == null){
		$reqUri = $_SERVER["REQUEST_URI"];
	}
}

$siteCityInfoCookie = GetCookie('siteCityInfo');
$siteCityName = '';
if($siteCityInfoCookie && !strstr($reqUri, 'changecity')){
	$siteCityInfoJson = json_decode($siteCityInfoCookie, true);
	if(is_array($siteCityInfoJson)){
		$siteCityInfo = $siteCityInfoJson;
	}
}



//站点根目录
$cfg_basedir = preg_replace('#\/include$#i', '', HUONIAOINC);

//模板引擎初始化配置
mb_regex_encoding($cfg_soft_lang);
include_once(HUONIAOINC."/tpl/Smarty.class.php");                   //包含smarty类文件
$huoniaoTag = new Smarty();                                         //建立smarty实例对象$smarty
$huoniaoTag->caching         = empty($cfg_cache_lifetime) ? FALSE : TRUE;  //是否使用缓存，项目在调试期间，不建议启用缓存
$huoniaoTag->template_dir    = HUONIAOROOT."/templates";            //设置模板目录
$huoniaoTag->compile_dir     = HUONIAOROOT."/templates_c/compiled"; //设置编译目录
$huoniaoTag->cache_dir       = HUONIAOROOT."/templates_c/caches";   //页面缓存文件夹
$huoniaoTag->cache_lifetime  = $cfg_cache_lifetime;                 //缓存时间
$huoniaoTag->left_delimiter  = "{#";                                //模板开始标记
$huoniaoTag->right_delimiter = "#}";                                //模板结束标记

if (HUONIAOBUG === TRUE){
	$huoniaoTag->force_compile = true;
}else{
	$huoniaoTag->force_compile = false;
}

// $huoniaoTag->compile_check   = false;														//每次访问都必须检测模板，默认为true
spl_autoload_register("__autoload");                                //解决 __autoload 和 Smarty 冲突

//初始化通用模板标签
$huoniaoTag->assign("HUONIAOROOT",    HUONIAOROOT);          //网站根目录
$huoniaoTag->assign("cfg_clihost",    $cfg_basehost);        //域名
$huoniaoTag->assign("cfg_currentHost",    $currentHttp . '://' . $_SERVER['HTTP_HOST']);        //当前域名
$huoniaoTag->assign("cfg_softname",   $cfg_softname);        //软件名
$huoniaoTag->assign("cfg_softenname", $cfg_soft_enname);     //软件英文名
$huoniaoTag->assign("cfg_version",    $cfg_version);         //软件版本
$huoniaoTag->assign("cfg_soft_lang",  $cfg_soft_lang);       //软件语言
$huoniaoTag->assign("thumbSize",      $cfg_thumbSize);       //缩略图上传大小限制
$huoniaoTag->assign("atlasSize",      $cfg_atlasSize);       //图集单张图片上传大小限制
$huoniaoTag->assign("thumbType",      "*.".str_replace("|", ";*.", $cfg_thumbType));     //缩略图上传类型限制
$huoniaoTag->assign("atlasType",      "*.".str_replace("|", ";*.", $cfg_atlasType));     //图集上传类型限制
$huoniaoTag->assign("audioType",      "*.".str_replace("|", ";*.", $cfg_audioType));     //音频上传类型限制
$huoniaoTag->assign("audioSize",      $cfg_audioSize);     //音频上传大小
$huoniaoTag->assign("HUONIAOINC",     HUONIAOINC);
$huoniaoTag->assign("HUONIAOROOT",    HUONIAOROOT);
$huoniaoTag->assign("HUONIAODATA",    HUONIAODATA);
$huoniaoTag->assign("HTTP_REFERER",   $_SERVER['HTTP_REFERER']);   //上一页的地址
$huoniaoTag->assign("cfg_fileUrl",    $cfg_fileUrl);
$huoniaoTag->assign("token",          $_SESSION['token']);         //全站token
$huoniaoTag->assign("editorFile",     includeFile('editor'));      //载入编辑器脚本
$huoniaoTag->assign("cfg_attachment", $cfg_attachment);            //附件访问地址
$huoniaoTag->assign('cfg_staticVersion', $cfg_staticVersion);  //静态资源版本号

$huoniaoTag->assign("cfg_secureAccess",   $cfg_secureAccess);
$huoniaoTag->assign("cfg_basehost",       $cfg_secureAccess.$cfg_basehost);

//短信登录开关
$huoniaoTag->assign("cfg_smsLoginState",   $cfg_smsLoginState);


//缓存
if(!is_file(HUONIAOINC."/class/memory.class.php") || !is_file(HUONIAOINC."/class/memory_redis.class.php")){
    class memory {
        public $enable = false;
        public function get($key, $prefix = '') {}
        public function set($key, $value, $ttl = 0, $prefix = '') {}
        public function rm($key, $prefix = '') {}
        public function clear() {}
        public function inc($key, $step = 1) {}
        public function dec($key, $step = 1) {}
    }
}
$HN_memory = new memory();


$siteCityName = "";
$siteCityInfoCookie = GetCookie('siteCityInfo');
if($siteCityInfoCookie){
	$siteCityInfoJson = json_decode($siteCityInfoCookie, true);
	if(is_array($siteCityInfoJson)){
		$siteCityInfo = $siteCityInfoJson;
		$siteCityName = $siteCityInfo['name'];
	}
}

//如果是选择城市页面，不需要进行城市关键字替换
if(strstr($_SERVER['REQUEST_URI'], 'changecity')){
	$siteCityName = '';
}



//获取城市分站自定义配置信息
$siteCityAdvancedConfig = array();
if($siteCityInfo && $siteCityInfo['cityid']){
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


$cfg_webname_h = $cfg_webname;
if($siteCityAdvancedConfig && $siteCityAdvancedConfig['siteConfig'] && $siteCityAdvancedConfig['siteConfig']['webname']){
    $cfg_webname_h = $siteCityAdvancedConfig['siteConfig']['webname'];
}


//后台配置文件不需要替换
$cfg_webname_ = $cfg_webname_h;
$cfg_shortname_ = $cfg_shortname;
if(!strstr($_SERVER['REQUEST_URI'], 'siteConfig') && !strstr($_SERVER['REQUEST_URI'], 'wechatConfig')){
	$cfg_webname_ = str_replace('$city', $siteCityName, stripslashes($cfg_webname_h));
	$cfg_shortname_ = str_replace('$city', $siteCityName, stripslashes($cfg_shortname));
}
$huoniaoTag->assign("cfg_webname",        $cfg_webname_);
$huoniaoTag->assign("cfg_shortname",      $cfg_shortname_);

//会员积分配置
$huoniaoTag->assign("cfg_pointState",     $cfg_pointState);
$huoniaoTag->assign("cfg_pointName",      $cfg_pointName);
$huoniaoTag->assign("cfg_pointRatio",     $cfg_pointRatio);
$huoniaoTag->assign("cfg_pointFee",       $cfg_pointFee);

//公交地铁状态
$huoniaoTag->assign("cfg_subway_state",       (int)$cfg_subway_state);
$huoniaoTag->assign("cfg_subway_title",       $cfg_subway_title ? $cfg_subway_title : '公交/地铁');

//微信基本配置
$huoniaoTag->assign("cfg_weixinName",     $cfg_wechatName);  //公众号名称
$huoniaoTag->assign("cfg_weixinCode",     $cfg_wechatCode);  //微信号
$huoniaoTag->assign("cfg_weixinQr",       getFilePath($cfg_wechatQr));  //二维码
$huoniaoTag->assign("cfg_miniProgramName", $cfg_miniProgramName);  //小程序名称
$huoniaoTag->assign("cfg_miniProgramQr",   getFilePath($cfg_miniProgramQr));  //二维码

//会员结算配置
$huoniaoTag->assign("cfg_rewardFee",      $cfg_rewardFee);

//商家结算配置
$huoniaoTag->assign("cfg_tuanFee",        $cfg_tuanFee);
$huoniaoTag->assign("cfg_shopFee",        $cfg_shopFee);
$huoniaoTag->assign("cfg_waimaiFee",      $cfg_waimaiFee);
$huoniaoTag->assign("cfg_homemakingFee",  $cfg_homemakingFee);
$huoniaoTag->assign("cfg_travelFee",      $cfg_travelFee);

//普通会员发布信息收费配置
$huoniaoTag->assign("cfg_fabuAmount",     $cfg_fabuAmount ? unserialize($cfg_fabuAmount) : array());

//极验验证
$huoniaoTag->assign("cfg_geetest",            (int)$cfg_geetest);            //是否开启  1为开启 0为未开启
$huoniaoTag->assign("cfg_geetest_pc_id",      $cfg_geetest_pc_id);      //网页端ID
$huoniaoTag->assign("cfg_geetest_pc_key",     $cfg_geetest_pc_key);     //网页端KEY
$huoniaoTag->assign("cfg_geetest_mobile_id",  $cfg_geetest_mobile_id);  //移动端ID
$huoniaoTag->assign("cfg_geetest_mobile_key", $cfg_geetest_mobile_key);     //移动端KEY

//签到规则
$huoniaoTag->assign("cfg_qiandao_state", $cfg_qiandao_state);
$huoniaoTag->assign("cfg_qiandao_buqianState", $cfg_qiandao_buqianState);
$huoniaoTag->assign("cfg_qiandao_buqianPrice", $cfg_qiandao_buqianPrice);
$huoniaoTag->assign("cfg_qiandao_note", $cfg_qiandao_note);

//分销
$huoniaoTag->assign("cfg_fenxiaoState", isset($cfg_fenxiaoState) ? $cfg_fenxiaoState : null);
$huoniaoTag->assign("cfg_fenxiaoName", $cfg_fenxiaoName);
$huoniaoTag->assign("cfg_fenxiaoLevel", $cfg_fenxiaoLevel);
$huoniaoTag->assign("cfg_fenxiaoNote", $cfg_fenxiaoNote);

//会员中心链接管理
$huoniaoTag->assign("cfg_ucenterLinks", explode(",", $cfg_ucenterLinks));

//聚合数据
$huoniaoTag->assign("cfg_juhe", $cfg_juhe ? unserialize($cfg_juhe) : array());

//是否为蜘蛛
$huoniaoTag->assign("isSpider", isSpider());

//app配置
$sql = $dsql->SetQuery("SELECT * FROM `#@__app_config` LIMIT 1");
$ret = $dsql->dsqlOper($sql, "results");
if($ret && is_array($ret)){
    $data = $ret[0];
    $huoniaoTag->assign('cfg_appname', $data['appname']);
    $huoniaoTag->assign('cfg_app_logo', getFilePath($data['logo']));
    $huoniaoTag->assign('cfg_app_android_version', $data['android_version']);
    $huoniaoTag->assign('cfg_app_ios_version', $data['ios_version']);
    $huoniaoTag->assign('cfg_app_android_download', $data['android_download']);
    $huoniaoTag->assign('cfg_app_yyb_download', $data['yyb_download']);
    $huoniaoTag->assign('cfg_app_ios_download', $data['ios_download']);

    $huoniaoTag->assign('cfg_app_business_appname', $data['business_appname']);
    $huoniaoTag->assign('cfg_app_business_logo', getFilePath($data['business_logo']));
    $huoniaoTag->assign('cfg_app_business_android_version', $data['business_android_version']);
    $huoniaoTag->assign('cfg_app_business_ios_version', $data['business_ios_version']);
    $huoniaoTag->assign('cfg_app_business_android_download', $data['business_android_download']);
    $huoniaoTag->assign('cfg_app_business_yyb_download', $data['business_yyb_download']);
    $huoniaoTag->assign('cfg_app_business_ios_download', $data['business_ios_download']);

    $huoniaoTag->assign('cfg_app_peisong_appname', $data['peisong_appname']);
    $huoniaoTag->assign('cfg_app_peisong_logo', getFilePath($data['peisong_logo']));
    $huoniaoTag->assign('cfg_app_peisong_android_version', $data['peisong_android_version']);
    $huoniaoTag->assign('cfg_app_peisong_ios_version', $data['peisong_ios_version']);
    $huoniaoTag->assign('cfg_app_peisong_android_download', $data['peisong_android_download']);
    $huoniaoTag->assign('cfg_app_peisong_yyb_download', $data['peisong_yyb_download']);
    $huoniaoTag->assign('cfg_app_peisong_ios_download', $data['peisong_ios_download']);

    $huoniaoTag->assign('cfg_ios_shelf', $data['ios_shelf']);
}

//地图配置
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

//百度单独显示
$huoniaoTag->assign('site_map_baidu_key', $cfg_map_baidu);
//高德单独显示
$huoniaoTag->assign('site_map_amap_key', $cfg_map_amap);
$huoniaoTag->assign('site_map_amap_apiFile', $cfg_secureAccess . "webapi.amap.com/maps?v=1.3&key=".$cfg_map_amap);

$huoniaoTag->registerPlugin("function", 'getUrlPath', 'getUrlPath');    //注册获取链接函数   主要以拼接静态URL为主  例：list-1-1-1-1-1-1-1.html
$huoniaoTag->registerPlugin("function", 'getUrlParam', 'getUrlParam');  //注册获取链接函数   主要以拼接URL参数为主  例：list.html?a=1&b=1&c=1&d=1
$huoniaoTag->registerPlugin("function", 'getPageList', 'getPageList');  //打印分页信息


//注册模板函数
$registerPlugin = array(
	"myad"           => "getMyAd",         //广告函数
	"echoCurrency"   => "echoCurrency",    //货币输出
	"getMyTime"      => "getMyTime",       //时间函数
	"getMyWeek"      => "getMyWeek",       //星期函数
	"bodyPageList"   => "bodyPageList",    //内容分页函数
	"getTypeInfo"    => "getTypeInfo",     //分类详细信息
	"getTypeName"    => "getTypeName",     //分类名称
	"getChannel"     => "getChannel",      //导航函数
	"getWeather"     => "getWeather",      //天气预报
	"changeFileSize" => "changeFileSize",  //附件地址
	"numberDaxie"    => "numberDaxie",     //数字大小写转换
	"FloorDay"       => "FloorDay",        //精确天数
	"FloorTimeByTemp" => "FloorTimeByTemp",  //XX时间前
	"getImgHeightByGeometric" => "getImgHeightByGeometric",      //根据图片路径、指定宽度，获取等比缩放后的高度
	"resizeImageSize" => "resizeImageSize",      //获取等比例缩放后的图片尺寸
	"getPublicParentInfo" => "getPublicParentInfo",      //根据指定表、指定ID获取相关信息
	"getModuleTitle" => "getModuleTitle",      //根据指定表、指定ID获取相关信息
	"verifyModuleAuth" => "verifyModuleAuth"      //根据模块标识验证会员是否有权限
);

if(!empty($registerPlugin)){
	foreach ($registerPlugin as $key => $value) {
		$huoniaoTag->registerPlugin("function", $key, $value);
	}
}


//临时解决模块中调用没有安装的模块时报错的问题 -by guozi 20160811
function registerPluginBlockNull(){};
$allModuleArr = array("article", "image", "info", "tuan", "house", "shop", "build", "furniture", "home", "renovation", "job", "dating", "marry", "paper", "special", "website", "waimai", "car", "travel", "tieba", "huodong", "huangye", "vote", "pic", "video", "live", "integral", "quanjing", "homemaking", "education");
foreach ($allModuleArr as $key => $value) {
  $huoniaoTag->registerPlugin("block", $value, "registerPluginBlockNull");
}


//获取系统模块
$isWxMiniprogram = GetCookie('isWxMiniprogram');
$installModuleArr = array();
$installModuleTitleArr = array();
$sql = $dsql->SetQuery("SELECT `name`, `title`, `subject`, `wx` FROM `#@__site_module` WHERE `state` = 0 AND `type` = 0");
$ret = $dsql->dsqlOper($sql, "results");
if($ret){
  foreach ($ret as $key => $value) {
      if(!$isWxMiniprogram || ($isWxMiniprogram && $value['wx'])) {
          $installModuleArr[] = $value['name'];
          $installModuleTitleArr[$value['name']] = $value['subject'] ? $value['subject'] : $value['title'];
      }
  }
}
$huoniaoTag->assign('installModuleArr', $installModuleArr);
$huoniaoTag->assign('installModuleTitleArr', $installModuleTitleArr);


//保证session中的防跨站标记与提交过来的标记一致
if($_POST['token'] != "" && $_POST['token'] != $_SESSION['token']){
	die('Error!<br />Code:Token');
}


//会员等级费用及特权
$sql = $dsql->SetQuery("SELECT `id`, `name`, `cost`, `privilege`, `icon` FROM `#@__member_level`");
$results = $dsql->dsqlOper($sql, "results");
$memberlevelList = array();
if($results && is_array($results)){
  foreach ($results as $key => $value) {
    $costArr      = empty($value['cost']) ? array() : unserialize($value['cost']);
    $privilegeArr = empty($value['privilege']) ? array() : unserialize($value['privilege']);
    $memberlevelList[$key]['id']   = $value['id'];
    $memberlevelList[$key]['name'] = $value['name'];
    $memberlevelList[$key]['icon'] = $value['icon'] ? getFilePath($value['icon']) : "";
    $memberlevelList[$key]['cost'] = $costArr;
    $memberlevelList[$key]['privilege'] = $privilegeArr;
  }
}
$huoniaoTag->assign('memberlevelList', $memberlevelList);


//将货币单位存入cookie
$currencyArr = array(
    "name"   => $currency_name,
    "short"  => $currency_short,
    "symbol" => $currency_symbol,
    "code"   => $currency_code,
    "rate"   => $currency_rate
);
PutCookie("currency", json_encode($currencyArr), 60 * 60, "/", $_SERVER['HTTP_HOST']);


//多语言
$langData = array();

//默认以后台配置为准，如果后台没有配置，取中文
$cfg_lang_dir = $cfg_lang ? $cfg_lang : 'zh-CN';

//获取cookie中的语言配置
$cookieLang = GetCookie('lang');

//如果url中指定了语言，第一优先考虑
if ($_GET['lang']) {
    $cfg_lang_dir = $_GET['lang'];

//如果cookie中指定了语言，第二优先考虑
} elseif ($cookieLang) {
    $cfg_lang_dir = $cookieLang;

//判断浏览器类型，如有新语言包，需要手动新增
} else {
    // $HTTP_ACCEPT_LANGUAGE = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4); //只取前4位，这样只判断最优先的语言。如果取前5位，可能出现en,zh的情况，影响判断。
    // if (preg_match("/zh-c/i", $HTTP_ACCEPT_LANGUAGE)){
    // 	$cfg_lang_dir = 'zh-CN';
    // }else if (preg_match("/zh/i", $HTTP_ACCEPT_LANGUAGE)){
    // 	$cfg_lang_dir = 'zh-HK';
    // }else if (preg_match("/en/i", $HTTP_ACCEPT_LANGUAGE)){
    // 	$cfg_lang_dir = 'en-US';
    // }
}

$lang_path = HUONIAOINC . "/lang/" . $cfg_lang_dir . "/";

//如果经过一系列判断之后获取到的语言包不存在，则使用系统默认配置
if (!is_dir($lang_path)) {
    $cfg_lang_dir = $cfg_lang ? $cfg_lang : 'zh-CN';
    $lang_path = HUONIAOINC . "/lang/" . $cfg_lang_dir . "/";
}

//如果使用系统默认配置的语言包依然不存在，则输入错误，结束浏览
if (!is_dir($lang_path)) {
    die('<center><br /><br />语言包读取失败，请联系管理员。<br /><br />Language package read failed. Please contact the administrator.</center>');
}

//读缓存
$langData_cache = $HN_memory->get('langData_' . $cfg_lang_dir);
if($langData_cache && !HUONIAOBUG){
    $langData = $langData_cache;
}else {
    $lang_dir = opendir($lang_path);
    while (($file = readdir($lang_dir)) !== false) {
        $sName = str_replace(".inc.php", "", $file);
        if ($file == '.' || $file == '..' || $file == 'config.xml') {
            continue;
        } else {
            $sub_dir = $lang_path . $file;
            if (file_exists($sub_dir)) {
                include_once($sub_dir);
                $langData[$sName] = $lang;
            }
        }
    }

    //写入缓存
    $HN_memory->set('langData_' . $cfg_lang_dir, $langData);
}

$huoniaoTag->assign('langData', $langData);
if($cookieLang){
	PutCookie("lang", $cfg_lang_dir, 60 * 60, "/");
}


//多语言列表
$lang_dir = HUONIAOINC . '/lang/';
$floders = listDir($lang_dir);
$langArr = array();
$langCurrentName = '';
if(!empty($floders)){
	$i = 0;
	foreach($floders as $key => $floder_){
		$langConfig = $lang_dir.'/'.$floder_.'/config.xml';
		if(file_exists($langConfig)){
			//解析xml配置文件
			$xml = new DOMDocument();
			libxml_disable_entity_loader(false);
			$xml->load($langConfig);
			$langDataXml = $xml->getElementsByTagName('Data')->item(0);
			$langName = $langDataXml->getElementsByTagName("name")->item(0)->nodeValue;

			array_push($langArr, array(
				'name' => $langName,
				'code' => $floder_
			));

			if($floder_ == $cfg_lang_dir){
				$langCurrentName = $langName;
			}
			$i++;
		}
	}
}
$huoniaoTag->assign('langList', array(
	'list' => $langArr,
	'currCode' => $cfg_lang_dir,
	'currName' => $langCurrentName
));


$huoniaoTag->assign('nowHour', getNowHour());   //获取当前时辰


//聚合数据接口-快递公司
$juhe_express_company = array(
    "sf" => "顺丰",
    "sto" => "申通",
    "yt" => "圆通",
    "yd" => "韵达",
    "tt" => "天天",
    "ems" => "EMS",
    "zto" => "中通",
    "ht" => "汇通",
    "qf" => "全峰",
    "db" => "德邦",
    "gt" => "国通",
    "rfd" => "如风达",
    "jd" => "京东快递",
    "zjs" => "宅急送",
    "ztky" => "中铁快运",
    "jiaji" => "佳吉快运",
    "suer" => "速尔快递",
    "xfwl" => "信丰物流",
    "yousu" => "优速快递",
    "zhongyou" => "中邮物流",
    "tdhy" => "天地华宇",
    "axd" => "安信达快递",
    "kuaijie" => "快捷速递",
    "dhl" => "DHL",
    "ds" => "D速物流",
    "fedexcn" => "FEDEX国内快递",
    "ocs" => "OCS",
    "tnt" => "TNT",
    "coe" => "东方快递",
    "cxwl" => "传喜物流",
    "cs" => "城市100",
    "cszx" => "城市之星物流",
    "aj" => "安捷快递",
    "bfdf" => "百福东方",
    "chengguang" => "程光快递",
    "dsf" => "递四方快递",
    "ctwl" => "长通物流",
    "feibao" => "飞豹快递",
    "ane66" => "安能快递",
    "ztoky" => "中通快运",
    "ycgky" => "远成物流",
    "ycky" => "远成快运",
    "youzheng" => "邮政快递",
    "bsky" => "百世快运",
    "suning" => "苏宁快递",
    "anneng" => "安能物流",
    "emsg" => "EMS国际",
    "fedex" => "Fedex国际",
    "ups" => "UPS国际快递",
    "aae" => "AAE全球专递",
    "else" => "其他"
);
$huoniaoTag->assign('juhe_express_company', $juhe_express_company);


//自动加载类库处理
function __autoload($classname){
	global $handler;
	global $autoload;
	$path = "";
	if(!$autoload){
	    $classname = preg_replace("/[^0-9a-z_]/i", '', $classname);
	    if(class_exists($classname)){
	        return TRUE;
	    }
	    // $classfile = ($handler ? HUONIAOROOT.'/api/handlers/' : HUONIAOINC.'/class/') . $classname. '.class.php';
		// $path = ($handler ? '/api/handlers/' : '/class/') . $classname. '.class.php';

        $classfile = HUONIAOROOT.'/api/handlers/' . $classname. '.class.php';

		if (is_file($classfile)){
            return include_once($classfile);
        } else {
            $classfile = HUONIAOINC.'/class/' . $classname. '.class.php';
            if (is_file($classfile)){
                return include_once($classfile);
            }
        }

		if (HUONIAOBUG === TRUE){
			echo '<pre>';
			echo $classname.' No Found!';
			echo '</pre>';
			exit();
		}else{
			header ("location:/404.html?__autoload=$classname");
			die();
		}
	}
}


//执行计划任务
Cron::run();
