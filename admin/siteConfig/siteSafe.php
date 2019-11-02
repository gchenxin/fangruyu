<?php
/**
 * 网站安全设置
 *
 * @version        $Id: siteSafe.php 2013-11-20 下午21:09:15 $
 * @package        HuoNiao.Config
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("siteSafe");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/siteConfig";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "siteSafe.html";
$dir       = "../../templates/siteConfig"; //当前目录

if($action != ""){
	if($token == "") die('token传递失败！');

	if($action == "basic"){
		//基本配置
		$cfg_holdsubdomain     = $holdsubdomain;
		$cfg_iplimit           = $iplimit;
		$cfg_errLoginCount     = (int)$errLoginCount;
		$cfg_loginLock         = (int)$loginLock;
		$cfg_smsLoginState     = (int)$smsLoginState;
		$cfg_memberVerified    = (int)$memberVerified;
		$cfg_memberVerifiedInfo = $memberVerifiedInfo;
		$cfg_memberBindPhone    = (int)$memberBindPhone;
		$cfg_memberBindPhoneInfo = $memberBindPhoneInfo;
		$cfg_regstatus         = (int)$regstatus;
		$cfg_regverify         = $regverify;
		$cfg_regtime           = $regtime;
		$cfg_holduser          = $holduser;
		$cfg_regclosemessage   = $regclosemessage;
		$cfg_replacestr        = $replacestr;
		$cfg_regtype           = isset($regtype) ? join(',',$regtype) : '';
		$cfg_regfields         = isset($regfields) ? join(',',$regfields) : '';

		$cfg_memberVerifiedInfo = $cfg_memberVerifiedInfo ? $cfg_memberVerifiedInfo : '请先进行实名认证！';

		adminLog("修改网站安全设置", "基本设置");

	}elseif($action == "verify"){
		//验证码
		$cfg_seccodestatus     = isset($seccodestatus) ? join(',',$seccodestatus) : '';
		$cfg_seccodetype       = $seccodetype;
		$cfg_seccodewidth      = $seccodewidth;
		$cfg_seccodeheight     = $seccodeheight;
		$cfg_seccodefamily     = $seccodefamily;
		$cfg_scecodeangle      = $scecodeangle;
		$cfg_scecodewarping    = $scecodewarping;
		$cfg_scecodeshadow     = $scecodeshadow;
		$cfg_scecodeanimator   = $scecodeanimator;

		adminLog("修改网站安全设置", "验证码设置");

	}elseif($action == "question"){
		//安全问题
		$cfg_secqaastatus      = isset($secqaastatus) ? join(',',$secqaastatus) : '';
		$question              = isset($question) ? join(',',$question) : '';
		$answer                = isset($answer) ? join(',',$answer) : '';

		$archives = $dsql->SetQuery("DELETE FROM `#@__safeqa`");
		$dsql->dsqlOper($archives, "results");

		$questionList = explode(",", $question);
		$answerList = explode(",", $answer);

		for($i = 0; $i < count($questionList); $i++){
			if($questionList[$i] != "" && $answerList[$i] != ""){
				$archives = $dsql->SetQuery("INSERT INTO `#@__safeqa` (`question`, `answer`) VALUES ('".$questionList[$i]."', '".$answerList[$i]."')");
				$dsql->dsqlOper($archives, "results");
			}
		}

		adminLog("修改网站安全设置", "验证问题设置");

	//极验验证码
	}elseif($action == "geetest"){
		$cfg_geetest            = (int)$geetest;
		$cfg_geetest_id      = $geetest_id;
		$cfg_geetest_key     = $geetest_key;

		adminLog("修改网站安全设置", "配置极验验证码");

	}

	//站点信息文件内容
	$configFile = "<"."?php\r\n";
	$configFile .= "\$cfg_basehost = '"._RunMagicQuotes($cfg_basehost)."';\r\n";
	$configFile .= "\$cfg_webname = '"._RunMagicQuotes($cfg_webname)."';\r\n";
	$configFile .= "\$cfg_shortname = '"._RunMagicQuotes($cfg_shortname)."';\r\n";
	$configFile .= "\$cfg_weblogo = '"._RunMagicQuotes($cfg_weblogo)."';\r\n";
    $configFile .= "\$cfg_sharePic = '"._RunMagicQuotes($cfg_sharePic)."';\r\n";
	$configFile .= "\$cfg_keywords = '"._RunMagicQuotes($cfg_keywords)."';\r\n";
	$configFile .= "\$cfg_description = '"._RunMagicQuotes($cfg_description)."';\r\n";
	$configFile .= "\$cfg_beian = '"._RunMagicQuotes($cfg_beian)."';\r\n";
	$configFile .= "\$cfg_hotline = '"._RunMagicQuotes($cfg_hotline)."';\r\n";
	$configFile .= "\$cfg_powerby = '"._RunMagicQuotes($cfg_powerby)."';\r\n";
	$configFile .= "\$cfg_statisticscode = '"._RunMagicQuotes($cfg_statisticscode)."';\r\n";
	$configFile .= "\$cfg_visitState = "._RunMagicQuotes($cfg_visitState).";\r\n";
	$configFile .= "\$cfg_visitMessage = '"._RunMagicQuotes($cfg_visitMessage)."';\r\n";
	$configFile .= "\$cfg_timeZone = "._RunMagicQuotes($cfg_timeZone).";\r\n";
	$configFile .= "\$cfg_mapCity = '"._RunMagicQuotes($cfg_mapCity)."';\r\n";
	$configFile .= "\$cfg_map = "._RunMagicQuotes($cfg_map).";\r\n";
	$configFile .= "\$cfg_map_google = '"._RunMagicQuotes($cfg_map_google)."';\r\n";
	$configFile .= "\$cfg_map_baidu = '"._RunMagicQuotes($cfg_map_baidu)."';\r\n";
	$configFile .= "\$cfg_map_baidu_server = '"._RunMagicQuotes($cfg_map_baidu_server)."';\r\n";
	$configFile .= "\$cfg_map_qq = '"._RunMagicQuotes($cfg_map_qq)."';\r\n";
	$configFile .= "\$cfg_map_amap = '"._RunMagicQuotes($cfg_map_amap)."';\r\n";
	$configFile .= "\$cfg_map_amap_server = '"._RunMagicQuotes($cfg_map_amap_server)."';\r\n";
	$configFile .= "\$cfg_weatherCity = '"._RunMagicQuotes($cfg_weatherCity)."';\r\n";
	$configFile .= "\$cfg_onlinetime = "._RunMagicQuotes($cfg_onlinetime).";\r\n";
	$configFile .= "\$cfg_cookiePath = '"._RunMagicQuotes($cfg_cookiePath)."';\r\n";
	$configFile .= "\$cfg_cookieDomain = '"._RunMagicQuotes($cfg_cookieDomain)."';\r\n";
	$configFile .= "\$cfg_cookiePre = '"._RunMagicQuotes($cfg_cookiePre)."';\r\n";
	$configFile .= "\$cfg_cache_lifetime = '"._RunMagicQuotes($cfg_cache_lifetime)."';\r\n";
	$configFile .= "\$cfg_lang = '"._RunMagicQuotes($cfg_lang)."';\r\n";
	$configFile .= "\$cfg_remoteStatic = '"._RunMagicQuotes($cfg_remoteStatic)."';\r\n";
	$configFile .= "\$cfg_spiderIndex = '".(int)_RunMagicQuotes($cfg_spiderIndex)."';\r\n";
	$configFile .= "\$cfg_urlRewrite = '"._RunMagicQuotes($cfg_urlRewrite)."';\r\n";
	$configFile .= "\$cfg_hideUrl = '"._RunMagicQuotes($cfg_hideUrl)."';\r\n";
	$configFile .= "\$cfg_bindMobile = '"._RunMagicQuotes($cfg_bindMobile)."';\r\n";
	$configFile .= "\$cfg_httpSecureAccess = '"._RunMagicQuotes($cfg_httpSecureAccess)."';\r\n";
	$configFile .= "\$cfg_siteDebug = '"._RunMagicQuotes($cfg_siteDebug)."';\r\n";
	$configFile .= "\$cfg_weixinQr = '"._RunMagicQuotes($cfg_weixinQr)."';\r\n";
	$configFile .= "\$cfg_template = '"._RunMagicQuotes($cfg_template)."';\r\n";
	$configFile .= "\$cfg_touchTemplate = '"._RunMagicQuotes($cfg_touchTemplate)."';\r\n";
	$configFile .= "\$cfg_defaultindex = '"._RunMagicQuotes($cfg_defaultindex)."';\r\n";
	$configFile .= "\$cfg_smsAlidayu = ".(int)$cfg_smsAlidayu.";\r\n";
	// 客服联系方式
	$configFile .= "\$cfg_server_tel = '".$cfg_server_tel."';\r\n";
	$configFile .= "\$cfg_server_qq = '".$cfg_server_qq."';\r\n";
	$configFile .= "\$cfg_server_wx = '".$cfg_server_wx."';\r\n";
	$configFile .= "\$cfg_server_wxQr = '".$cfg_server_wxQr."';\r\n";

	//邮件配置
	$configFile .= "\$cfg_mail = "._RunMagicQuotes($cfg_mail).";\r\n";
	$configFile .= "\$cfg_mailServer = '"._RunMagicQuotes($cfg_mailServer)."';\r\n";
	$configFile .= "\$cfg_mailPort = '"._RunMagicQuotes($cfg_mailPort)."';\r\n";
	$configFile .= "\$cfg_mailFrom = '"._RunMagicQuotes($cfg_mailFrom)."';\r\n";
	$configFile .= "\$cfg_mailUser = '"._RunMagicQuotes($cfg_mailUser)."';\r\n";
	$configFile .= "\$cfg_mailPass = '"._RunMagicQuotes($cfg_mailPass)."';\r\n";

	//上传配置
	$configFile .= "\$cfg_uploadDir = '"._RunMagicQuotes($cfg_uploadDir)."';\r\n";
	$configFile .= "\$cfg_softSize = "._RunMagicQuotes($cfg_softSize).";\r\n";
	$configFile .= "\$cfg_softType = '"._RunMagicQuotes($cfg_softType)."';\r\n";
	$configFile .= "\$cfg_thumbSize = "._RunMagicQuotes($cfg_thumbSize).";\r\n";
	$configFile .= "\$cfg_thumbType = '"._RunMagicQuotes($cfg_thumbType)."';\r\n";
	$configFile .= "\$cfg_atlasSize = "._RunMagicQuotes($cfg_atlasSize).";\r\n";
	$configFile .= "\$cfg_atlasType = '"._RunMagicQuotes($cfg_atlasType)."';\r\n";
	$configFile .= "\$cfg_editorSize = "._RunMagicQuotes($cfg_editorSize).";\r\n";
	$configFile .= "\$cfg_editorType = '"._RunMagicQuotes($cfg_editorType)."';\r\n";
	$configFile .= "\$cfg_photoSize = "._RunMagicQuotes($cfg_photoSize).";\r\n";
	$configFile .= "\$cfg_photoType = '"._RunMagicQuotes($cfg_photoType)."';\r\n";
	$configFile .= "\$cfg_flashSize = "._RunMagicQuotes($cfg_flashSize).";\r\n";
	$configFile .= "\$cfg_audioSize = "._RunMagicQuotes($cfg_audioSize).";\r\n";
	$configFile .= "\$cfg_audioType = '"._RunMagicQuotes($cfg_audioType)."';\r\n";
	$configFile .= "\$cfg_videoSize = "._RunMagicQuotes($cfg_videoSize).";\r\n";
	$configFile .= "\$cfg_videoType = '"._RunMagicQuotes($cfg_videoType)."';\r\n";
	$configFile .= "\$cfg_thumbSmallWidth = "._RunMagicQuotes($cfg_thumbSmallWidth).";\r\n";
	$configFile .= "\$cfg_thumbSmallHeight = "._RunMagicQuotes($cfg_thumbSmallHeight).";\r\n";
	$configFile .= "\$cfg_thumbMiddleWidth = "._RunMagicQuotes($cfg_thumbMiddleWidth).";\r\n";
	$configFile .= "\$cfg_thumbMiddleHeight = "._RunMagicQuotes($cfg_thumbMiddleHeight).";\r\n";
	$configFile .= "\$cfg_thumbLargeWidth = "._RunMagicQuotes($cfg_thumbLargeWidth).";\r\n";
	$configFile .= "\$cfg_thumbLargeHeight = "._RunMagicQuotes($cfg_thumbLargeHeight).";\r\n";
	$configFile .= "\$cfg_atlasSmallWidth = "._RunMagicQuotes($cfg_atlasSmallWidth).";\r\n";
	$configFile .= "\$cfg_atlasSmallHeight = "._RunMagicQuotes($cfg_atlasSmallHeight).";\r\n";
	$configFile .= "\$cfg_photoSmallWidth = "._RunMagicQuotes($cfg_photoSmallWidth).";\r\n";
	$configFile .= "\$cfg_photoSmallHeight = "._RunMagicQuotes($cfg_photoSmallHeight).";\r\n";
	$configFile .= "\$cfg_photoMiddleWidth = "._RunMagicQuotes($cfg_photoMiddleWidth).";\r\n";
	$configFile .= "\$cfg_photoMiddleHeight = "._RunMagicQuotes($cfg_photoMiddleHeight).";\r\n";
	$configFile .= "\$cfg_photoLargeWidth = "._RunMagicQuotes($cfg_photoLargeWidth).";\r\n";
	$configFile .= "\$cfg_photoLargeHeight = "._RunMagicQuotes($cfg_photoLargeHeight).";\r\n";
	$configFile .= "\$cfg_meditorPicWidth = "._RunMagicQuotes($cfg_meditorPicWidth).";\r\n";
	$configFile .= "\$cfg_photoCutType = '"._RunMagicQuotes($cfg_photoCutType)."';\r\n";
	$configFile .= "\$cfg_photoCutPostion = '"._RunMagicQuotes($cfg_photoCutPostion)."';\r\n";
	$configFile .= "\$cfg_quality = "._RunMagicQuotes($cfg_quality).";\r\n";

	//远程附件
	$configFile .= "\$cfg_ftpType = "._RunMagicQuotes($cfg_ftpType).";\r\n";
	$configFile .= "\$cfg_ftpState = "._RunMagicQuotes($cfg_ftpState).";\r\n";
	$configFile .= "\$cfg_ftpSSL = "._RunMagicQuotes($cfg_ftpSSL).";\r\n";
	$configFile .= "\$cfg_ftpPasv = "._RunMagicQuotes($cfg_ftpPasv).";\r\n";
	$configFile .= "\$cfg_ftpUrl = '"._RunMagicQuotes($cfg_ftpUrl)."';\r\n";
	$configFile .= "\$cfg_ftpServer = '"._RunMagicQuotes($cfg_ftpServer)."';\r\n";
	$configFile .= "\$cfg_ftpPort = "._RunMagicQuotes($cfg_ftpPort).";\r\n";
	$configFile .= "\$cfg_ftpDir = '"._RunMagicQuotes($cfg_ftpDir)."';\r\n";
	$configFile .= "\$cfg_ftpUser = '"._RunMagicQuotes($cfg_ftpUser)."';\r\n";
	$configFile .= "\$cfg_ftpPwd = '"._RunMagicQuotes($cfg_ftpPwd)."';\r\n";
	$configFile .= "\$cfg_ftpTimeout = "._RunMagicQuotes($cfg_ftpTimeout).";\r\n";
	$configFile .= "\$cfg_OSSUrl = '"._RunMagicQuotes($cfg_OSSUrl)."';\r\n";
	$configFile .= "\$cfg_OSSBucket = '"._RunMagicQuotes($cfg_OSSBucket)."';\r\n";
	$configFile .= "\$cfg_EndPoint = '"._RunMagicQuotes($cfg_EndPoint)."';\r\n";
	$configFile .= "\$cfg_OSSKeyID = '"._RunMagicQuotes($cfg_OSSKeyID)."';\r\n";
	$configFile .= "\$cfg_OSSKeySecret = '"._RunMagicQuotes($cfg_OSSKeySecret)."';\r\n";
    $configFile .= "\$cfg_QINIUAccessKey = '"._RunMagicQuotes($cfg_QINIUAccessKey)."';\r\n";
    $configFile .= "\$cfg_QINIUSecretKey = '"._RunMagicQuotes($cfg_QINIUSecretKey)."';\r\n";
    $configFile .= "\$cfg_QINIUbucket = '"._RunMagicQuotes($cfg_QINIUbucket)."';\r\n";
    $configFile .= "\$cfg_QINIUdomain = '"._RunMagicQuotes($cfg_QINIUdomain)."';\r\n";

	//水印设置
	$configFile .= "\$thumbMarkState = "._RunMagicQuotes($thumbMarkState).";\r\n";
	$configFile .= "\$atlasMarkState = "._RunMagicQuotes($atlasMarkState).";\r\n";
	$configFile .= "\$editorMarkState = "._RunMagicQuotes($editorMarkState).";\r\n";
	$configFile .= "\$waterMarkWidth = "._RunMagicQuotes($waterMarkWidth).";\r\n";
	$configFile .= "\$waterMarkHeight = "._RunMagicQuotes($waterMarkHeight).";\r\n";
	$configFile .= "\$waterMarkPostion = "._RunMagicQuotes($waterMarkPostion).";\r\n";
	$configFile .= "\$waterMarkType = "._RunMagicQuotes($waterMarkType).";\r\n";
	$configFile .= "\$waterMarkText = '"._RunMagicQuotes($waterMarkText)."';\r\n";
	$configFile .= "\$markFontfamily = '"._RunMagicQuotes($markFontfamily)."';\r\n";
	$configFile .= "\$markFontsize = "._RunMagicQuotes($markFontsize).";\r\n";
	$configFile .= "\$markFontColor = '"._RunMagicQuotes($markFontColor)."';\r\n";
	$configFile .= "\$markFile = '"._RunMagicQuotes($markFile)."';\r\n";
	$configFile .= "\$markPadding = "._RunMagicQuotes($markPadding).";\r\n";
	$configFile .= "\$markTransparent = "._RunMagicQuotes($markTransparent).";\r\n";
	$configFile .= "\$markQuality = "._RunMagicQuotes($markQuality).";\r\n";

	//计量单位
	$currency_name   = !empty($currency_name) ? $currency_name : "人民币";
	$currency_short  = !empty($currency_short) ? $currency_short : "元";
	$currency_symbol = !empty($currency_symbol) ? $currency_symbol : "¥";
	$currency_code   = !empty($currency_code) ? $currency_code : "RMB";
	$currency_rate   = !empty($currency_rate) ? $currency_rate : "1";

	$configFile .= "\$currency_name = '"._RunMagicQuotes($currency_name)."';\r\n";
	$configFile .= "\$currency_short = '"._RunMagicQuotes($currency_short)."';\r\n";
	$configFile .= "\$currency_symbol = '"._RunMagicQuotes($currency_symbol)."';\r\n";
	$configFile .= "\$currency_code = '"._RunMagicQuotes($currency_code)."';\r\n";
	$configFile .= "\$currency_rate = '"._RunMagicQuotes($currency_rate)."';\r\n";

	//广告标识
	$configFile .= "\$cfg_advMarkState = ".(int)_RunMagicQuotes($cfg_advMarkState).";\r\n";
	$configFile .= "\$cfg_advMarkPostion = ".(int)_RunMagicQuotes($cfg_advMarkPostion).";\r\n";

	//会员中心链接管理
	$configFile .= "\$cfg_ucenterLinks = '"._RunMagicQuotes($cfg_ucenterLinks)."';\r\n";

	//自然语言处理
	$configFile .= "\$cfg_nlp_AppID = '"._RunMagicQuotes($cfg_nlp_AppID)."';\r\n";
	$configFile .= "\$cfg_nlp_APIKey = '"._RunMagicQuotes($cfg_nlp_APIKey)."';\r\n";
	$configFile .= "\$cfg_nlp_Secret = '"._RunMagicQuotes($cfg_nlp_Secret)."';\r\n";

    //聚合数据接口
    $configFile .= "\$cfg_juhe = '".$cfg_juhe."';\r\n";

	//即时通讯接口
	$configFile .= "\$cfg_km_accesskey_id = '"._RunMagicQuotes($cfg_km_accesskey_id)."';\r\n";
	$configFile .= "\$cfg_km_accesskey_secret = '"._RunMagicQuotes($cfg_km_accesskey_secret)."';\r\n";

	//公交地铁
	$configFile .= "\$cfg_subway_state = ".(int)_RunMagicQuotes($cfg_subway_state).";\r\n";
	$configFile .= "\$cfg_subway_title = '"._RunMagicQuotes($cfg_subway_title)."';\r\n";

    //重复区域
    $configFile .= "\$cfg_sameAddr_state = ".(int)_RunMagicQuotes($cfg_sameAddr_state).";\r\n";

	//基本安全配置
	$configFile .= "\$cfg_holdsubdomain = '".$cfg_holdsubdomain."';\r\n";
	$configFile .= "\$cfg_iplimit = '".$cfg_iplimit."';\r\n";
	$configFile .= "\$cfg_errLoginCount = ".(int)$cfg_errLoginCount.";\r\n";
	$configFile .= "\$cfg_loginLock = ".(int)$cfg_loginLock.";\r\n";
	$configFile .= "\$cfg_smsLoginState = ".(int)$cfg_smsLoginState.";\r\n";
	$configFile .= "\$cfg_memberVerified = ".(int)$cfg_memberVerified.";\r\n";
	$configFile .= "\$cfg_memberVerifiedInfo = '"._RunMagicQuotes($cfg_memberVerifiedInfo)."';\r\n";
	$configFile .= "\$cfg_memberBindPhone = ".(int)$cfg_memberBindPhone.";\r\n";
	$configFile .= "\$cfg_memberBindPhoneInfo = '"._RunMagicQuotes($cfg_memberBindPhoneInfo)."';\r\n";
	$configFile .= "\$cfg_regstatus = ".(int)$cfg_regstatus.";\r\n";
	$configFile .= "\$cfg_regverify = ".$cfg_regverify.";\r\n";
	$configFile .= "\$cfg_regtime = ".$cfg_regtime.";\r\n";
	$configFile .= "\$cfg_holduser = '".$cfg_holduser."';\r\n";
	$configFile .= "\$cfg_regclosemessage = '".$cfg_regclosemessage."';\r\n";
	$configFile .= "\$cfg_replacestr = '".$cfg_replacestr."';\r\n";
	$configFile .= "\$cfg_regtype = '".$cfg_regtype."';\r\n";
	$configFile .= "\$cfg_regfields = '".$cfg_regfields."';\r\n";

	//验证码
	$configFile .= "\$cfg_seccodestatus = '".$cfg_seccodestatus."';\r\n";
	$configFile .= "\$cfg_seccodetype = ".$cfg_seccodetype.";\r\n";
	$configFile .= "\$cfg_seccodewidth = ".$cfg_seccodewidth.";\r\n";
	$configFile .= "\$cfg_seccodeheight = ".$cfg_seccodeheight.";\r\n";
	$configFile .= "\$cfg_seccodefamily = '".$cfg_seccodefamily."';\r\n";
	$configFile .= "\$cfg_scecodeangle = ".$cfg_scecodeangle.";\r\n";
	$configFile .= "\$cfg_scecodewarping = ".$cfg_scecodewarping.";\r\n";
	$configFile .= "\$cfg_scecodeshadow = ".$cfg_scecodeshadow.";\r\n";
	$configFile .= "\$cfg_scecodeanimator = ".$cfg_scecodeanimator.";\r\n";

	//安全问题
	$configFile .= "\$cfg_secqaastatus = '".$cfg_secqaastatus."';\r\n";

	//论坛配置参数
	$configFile .= "\$cfg_bbsName = '"._RunMagicQuotes($cfg_bbsName)."';\r\n";
	$configFile .= "\$cfg_bbsUrl = '"._RunMagicQuotes($cfg_bbsUrl)."';\r\n";
	$configFile .= "\$cfg_bbsState = ".(int)$cfg_bbsState.";\r\n";
	$configFile .= "\$cfg_bbsType = '"._RunMagicQuotes($cfg_bbsType)."';\r\n";

	//极验验证码
	$configFile .= "\$cfg_geetest = ".(int)$cfg_geetest.";\r\n";
	$configFile .= "\$cfg_geetest_id = '"._RunMagicQuotes($cfg_geetest_id)."';\r\n";
	$configFile .= "\$cfg_geetest_key = '"._RunMagicQuotes($cfg_geetest_key)."';\r\n";

	$configFile .= "?".">";

	$configIncFile = HUONIAOINC.'/config/siteConfig.inc.php';
	$fp = fopen($configIncFile, "w") or die('{"state": 200, "info": '.json_encode("写入文件 $configIncFile 失败，请检查权限！").'}');
	fwrite($fp, $configFile);
	fclose($fp);

	die('{"state": 100, "info": '.json_encode("配置成功！").'}');
	exit;
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'admin/siteConfig/siteSafe.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('holdsubdomain', $cfg_holdsubdomain);
	$huoniaoTag->assign('iplimit', $cfg_iplimit);
	$huoniaoTag->assign('errLoginCount', $cfg_errLoginCount);
	$huoniaoTag->assign('loginLock', $cfg_loginLock);

	//短信验证码登录-单选
	$huoniaoTag->assign('smsLoginState', array('1', '0'));
	$huoniaoTag->assign('smsLoginStateNames',array('开启','关闭'));
	$huoniaoTag->assign('smsLoginStateChecked', (int)$cfg_smsLoginState);

	//发布信息实名认证-单选
	$huoniaoTag->assign('memberVerified', array('1', '0'));
	$huoniaoTag->assign('memberVerifiedNames',array('开启','关闭'));
	$huoniaoTag->assign('memberVerifiedChecked', (int)$cfg_memberVerified);
	$huoniaoTag->assign('memberVerifiedInfo', $cfg_memberVerifiedInfo ? $cfg_memberVerifiedInfo : '请先进行实名认证！');

	//发布信息绑定手机-单选
	$huoniaoTag->assign('memberBindPhone', array('1', '0'));
	$huoniaoTag->assign('memberBindPhoneNames',array('开启','关闭'));
	$huoniaoTag->assign('memberBindPhoneChecked', (int)$cfg_memberBindPhone);
	$huoniaoTag->assign('memberBindPhoneInfo', $cfg_memberBindPhoneInfo ? $cfg_memberBindPhoneInfo : '请先进行手机绑定！');

	//会员注册开关-单选
	$huoniaoTag->assign('regstatus', array('1', '0'));
	$huoniaoTag->assign('regstatusNames',array('开启','关闭'));
	$huoniaoTag->assign('regstatusChecked', $cfg_regstatus);

	//会员注册类型开关-多选
	$huoniaoTag->assign('regType', array('1', '2','3'));
	$huoniaoTag->assign('regTypeName',array('用户名注册','手机注册','邮箱注册'));
	$huoniaoTag->assign('regTypeChecked', explode(",", $cfg_regtype));
	//会员注册字段开关-多选
	$huoniaoTag->assign('regFields', array('1', '2','3'));
	$huoniaoTag->assign('regFieldsName',array('真实姓名','邮箱','手机'));
	$huoniaoTag->assign('regFieldsChecked', explode(",", $cfg_regfields));

	//注册验证-单选
	$huoniaoTag->assign('regverify', array('0', '1', '2'));
	$huoniaoTag->assign('regverifyNames',array('不验证','邮件验证','短信验证'));
	$huoniaoTag->assign('regverifyChecked', $cfg_regverify);

	$huoniaoTag->assign('regtime', $cfg_regtime);
	$huoniaoTag->assign('holduser', $cfg_holduser);

	$huoniaoTag->assign('regclosemessage', $cfg_regclosemessage);
	$huoniaoTag->assign('replacestr', $cfg_replacestr);

	//启用验证码-多选
	$huoniaoTag->assign('seccodestatus',array('reg','login'));
	$huoniaoTag->assign('seccodestatusList',array('新用户注册','用户登录'));
	$huoniaoTag->assign('seccodestatusitem', explode(",", $cfg_seccodestatus));

	//验证码类型-单选
	$huoniaoTag->assign('seccodetype', array('1', '2', '3', '4'));
	$huoniaoTag->assign('seccodetypeNames',array('数字','字母','汉字','算术'));
	$huoniaoTag->assign('seccodetypeChecked', $cfg_seccodetype);

	$huoniaoTag->assign('seccodewidth', $cfg_seccodewidth);
	$huoniaoTag->assign('seccodeheight', $cfg_seccodeheight);

	//水印字体
	$ttfFloder = HUONIAOINC."/data/fonts/";
	if(is_dir($ttfFloder)){
		if ($file = opendir($ttfFloder)){
			$fileArray = array();
			while ($f = readdir($file)){
				if($f != '.' && $f != '..'){
					array_push($fileArray, $f);
				}
			}
			//字体文件-下拉菜单
			$huoniaoTag->assign('seccodefamily', $fileArray);
			$huoniaoTag->assign('seccodefamilySelected', $cfg_seccodefamily);
		}
	}

	//随机倾斜度-单选
	$huoniaoTag->assign('scecodeangle', array('1', '0'));
	$huoniaoTag->assign('scecodeangleNames',array('是','否'));
	$huoniaoTag->assign('scecodeangleChecked', $cfg_scecodeangle);

	//随机扭曲-单选
	$huoniaoTag->assign('scecodewarping', array('1', '0'));
	$huoniaoTag->assign('scecodewarpingNames',array('是','否'));
	$huoniaoTag->assign('scecodewarpingChecked', $cfg_scecodewarping);

	//文字阴影-单选
	$huoniaoTag->assign('scecodeshadow', array('1', '0'));
	$huoniaoTag->assign('scecodeshadowNames',array('是','否'));
	$huoniaoTag->assign('scecodeshadowChecked', $cfg_scecodeshadow);

	//GIF 动画-单选
	$huoniaoTag->assign('scecodeanimator', array('1', '0'));
	$huoniaoTag->assign('scecodeanimatorNames',array('是','否'));
	$huoniaoTag->assign('scecodeanimatorChecked', $cfg_scecodeanimator);

	//启用验证问题-多选
	$huoniaoTag->assign('secqaastatus',array('reg'));
	$huoniaoTag->assign('secqaastatusList',array('新用户注册'));
	$huoniaoTag->assign('secqaastatusitem', explode(",", $cfg_secqaastatus));

	$archives = $dsql->SetQuery("SELECT `question`, `answer` FROM `#@__safeqa`");
	$results = $dsql->dsqlOper($archives, "results");

	//极验验证码
	$huoniaoTag->assign('geetest', array('1', '0'));
	$huoniaoTag->assign('geetestNames',array('是','否'));
	$huoniaoTag->assign('geetestChecked', (int)$cfg_geetest);

	$huoniaoTag->assign('geetest_id', $cfg_geetest_id);
	$huoniaoTag->assign('geetest_key', $cfg_geetest_key);

	$huoniaoTag->assign('safeqa', json_encode($results));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/siteConfig";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
