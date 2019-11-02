<?php
/**
 * 邮件账号管理
 *
 * @version        $Id: emailAccount.php 2015-8-5 下午23:58:11 $
 * @package        HuoNiao.Config
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("emailAccount");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/siteConfig";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "emailAccount.html";

if($action != ""){
	if($token == "") die('token传递失败！');

	//获取邮件帐号信息
	if($action == "getMailInfo"){
		if($id !== ""){
			$mailInfo = array();
			$mailServer = explode(",", $cfg_mailServer);
			$mailPort   = explode(",", $cfg_mailPort);
			$mailFrom   = explode(",", $cfg_mailFrom);
			$mailUser   = explode(",", $cfg_mailUser);
			$mailPass   = explode(",", $cfg_mailPass);
			foreach ($mailServer as $key => $value) {
				if($key == $id){
					$mailInfo[0] = $mailServer[$key];
					$mailInfo[1] = $mailPort[$key];
					$mailInfo[2] = $mailFrom[$key];
					$mailInfo[3] = $mailUser[$key];
					$mailInfo[4] = $mailPass[$key];
				}
			}
			echo json_encode($mailInfo);
		}
		die;

	}else{
		if($action == "updateMail"){

			//启用邮件帐号
			$cfg_mail = $mail;

			adminLog("修改系统基本参数", "邮件配置");

		}elseif($action == "delMail"){

			//删除邮件帐号

			adminLog("修改系统基本参数", "邮件配置");

		}elseif($action == "email"){
			//邮件配置
			$c_mailServer      = $mailServer;
			$c_mailPort        = $mailPort;
			$c_mailFrom        = $mailFrom;
			$c_mailUser        = $mailUser;
			$c_mailPass        = $mailPass;

			if(empty($c_mailServer) || empty($c_mailPort) || empty($c_mailFrom) || empty($c_mailUser) || empty($c_mailPass))
			die('{"state": 200, "info": '.json_encode("请填写完整！").'}');

			adminLog("修改系统基本参数", "邮件配置");

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
		if($dopost == "addMail"){
			$cfg_mailServer = empty($cfg_mailServer) ? $c_mailServer : $cfg_mailServer.",".$c_mailServer;
			$cfg_mailPort   = empty($cfg_mailPort) ? $c_mailPort : $cfg_mailPort.",".$c_mailPort;
			$cfg_mailFrom   = empty($cfg_mailFrom) ? $c_mailFrom : $cfg_mailFrom.",".$c_mailFrom;
			$cfg_mailUser   = empty($cfg_mailUser) ? $c_mailUser : $cfg_mailUser.",".$c_mailUser;
			$cfg_mailPass   = empty($cfg_mailPass) ? $c_mailPass : $cfg_mailPass.",".$c_mailPass;
		}elseif($dopost == "editMail"){
			if($id !== ""){
				$mailServer = explode(",", $cfg_mailServer);
				$mailPort   = explode(",", $cfg_mailPort);
				$mailFrom   = explode(",", $cfg_mailFrom);
				$mailUser   = explode(",", $cfg_mailUser);
				$mailPass   = explode(",", $cfg_mailPass);
				foreach ($mailServer as $key => $value) {
					if($key == $id){
						$mailServer[$key] = $c_mailServer;
						$mailPort[$key]   = $c_mailPort;
						$mailFrom[$key]   = $c_mailFrom;
						$mailUser[$key]   = $c_mailUser;
						$mailPass[$key]   = $c_mailPass;
					}
				}

				$cfg_mailServer = join(",", $mailServer);
				$cfg_mailPort   = join(",", $mailPort);
				$cfg_mailFrom   = join(",", $mailFrom);
				$cfg_mailUser   = join(",", $mailUser);
				$cfg_mailPass   = join(",", $mailPass);
			}else{
				die('{"state": 200, "info": '.json_encode("参数传递失败！").'}');
			}
		}elseif($dopost == "delMail"){
			if($index !== ""){
				if($cfg_mail == $index){
					die('{"state": 200, "info": '.json_encode("启用状态下无法删除！").'}');
				}

				//新帐号ID
				$cfg_mail = $nid;

				$mailServer = explode(",", $cfg_mailServer);
				$mailPort   = explode(",", $cfg_mailPort);
				$mailFrom   = explode(",", $cfg_mailFrom);
				$mailUser   = explode(",", $cfg_mailUser);
				$mailPass   = explode(",", $cfg_mailPass);
				foreach ($mailServer as $key => $value) {
					if($key == $index){
						array_splice($mailServer, $key, 1);
						array_splice($mailPort, $key, 1);
						array_splice($mailFrom, $key, 1);
						array_splice($mailUser, $key, 1);
						array_splice($mailPass, $key, 1);
					}
				}

				$cfg_mailServer = join(",", $mailServer);
				$cfg_mailPort   = join(",", $mailPort);
				$cfg_mailFrom   = join(",", $mailFrom);
				$cfg_mailUser   = join(",", $mailUser);
				$cfg_mailPass   = join(",", $mailPass);
			}else{
				die('{"state": 200, "info": '.json_encode("参数传递失败！").'}');
			}
		}
		$configFile .= "\$cfg_mail = ".$cfg_mail.";\r\n";
		$configFile .= "\$cfg_mailServer = '".$cfg_mailServer."';\r\n";
		$configFile .= "\$cfg_mailPort = '".$cfg_mailPort."';\r\n";
		$configFile .= "\$cfg_mailFrom = '".$cfg_mailFrom."';\r\n";
		$configFile .= "\$cfg_mailUser = '".$cfg_mailUser."';\r\n";
		$configFile .= "\$cfg_mailPass = '".$cfg_mailPass."';\r\n";

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
		$configFile .= "\$cfg_holdsubdomain = '"._RunMagicQuotes($cfg_holdsubdomain)."';\r\n";
		$configFile .= "\$cfg_iplimit = '"._RunMagicQuotes($cfg_iplimit)."';\r\n";
		$configFile .= "\$cfg_errLoginCount = ".(int)_RunMagicQuotes($cfg_errLoginCount).";\r\n";
		$configFile .= "\$cfg_loginLock = ".(int)_RunMagicQuotes($cfg_loginLock).";\r\n";
		$configFile .= "\$cfg_smsLoginState = ".(int)$cfg_smsLoginState.";\r\n";
		$configFile .= "\$cfg_memberVerified = ".(int)$cfg_memberVerified.";\r\n";
		$configFile .= "\$cfg_memberVerifiedInfo = '"._RunMagicQuotes($cfg_memberVerifiedInfo)."';\r\n";
		$configFile .= "\$cfg_memberBindPhone = ".(int)$cfg_memberBindPhone.";\r\n";
		$configFile .= "\$cfg_memberBindPhoneInfo = '"._RunMagicQuotes($cfg_memberBindPhoneInfo)."';\r\n";
		$configFile .= "\$cfg_regstatus = ".(int)_RunMagicQuotes($cfg_regstatus).";\r\n";
		$configFile .= "\$cfg_regverify = "._RunMagicQuotes($cfg_regverify).";\r\n";
		$configFile .= "\$cfg_regtime = "._RunMagicQuotes($cfg_regtime).";\r\n";
		$configFile .= "\$cfg_holduser = '"._RunMagicQuotes($cfg_holduser)."';\r\n";
		$configFile .= "\$cfg_regclosemessage = '"._RunMagicQuotes($cfg_regclosemessage)."';\r\n";
		$configFile .= "\$cfg_replacestr = '"._RunMagicQuotes($cfg_replacestr)."';\r\n";
		$configFile .= "\$cfg_regtype = '"._RunMagicQuotes($cfg_regtype)."';\r\n";
	    $configFile .= "\$cfg_regfields = '"._RunMagicQuotes($cfg_regfields)."';\r\n";

		//验证码
		$configFile .= "\$cfg_seccodestatus = '"._RunMagicQuotes($cfg_seccodestatus)."';\r\n";
		$configFile .= "\$cfg_seccodetype = "._RunMagicQuotes($cfg_seccodetype).";\r\n";
		$configFile .= "\$cfg_seccodewidth = "._RunMagicQuotes($cfg_seccodewidth).";\r\n";
		$configFile .= "\$cfg_seccodeheight = "._RunMagicQuotes($cfg_seccodeheight).";\r\n";
		$configFile .= "\$cfg_seccodefamily = '"._RunMagicQuotes($cfg_seccodefamily)."';\r\n";
		$configFile .= "\$cfg_scecodeangle = "._RunMagicQuotes($cfg_scecodeangle).";\r\n";
		$configFile .= "\$cfg_scecodewarping = "._RunMagicQuotes($cfg_scecodewarping).";\r\n";
		$configFile .= "\$cfg_scecodeshadow = "._RunMagicQuotes($cfg_scecodeshadow).";\r\n";
		$configFile .= "\$cfg_scecodeanimator = "._RunMagicQuotes($cfg_scecodeanimator).";\r\n";

		//安全问题
		$configFile .= "\$cfg_secqaastatus = '"._RunMagicQuotes($cfg_secqaastatus)."';\r\n";

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
}

//配置参数
require_once(HUONIAOINC.'/config/siteConfig.inc.php');

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'admin/siteConfig/emailAccount.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	//邮件配置
	$mailItem = array();
	$mailServer = explode(",", $cfg_mailServer);
	$mailPort   = explode(",", $cfg_mailPort);
	$mailFrom   = explode(",", $cfg_mailFrom);
	if(!empty($cfg_mailServer)){
		foreach ($mailServer as $key => $value) {
			$state = '<font class="muted">未启用</font>';
			$cla = '';
			if($key == $cfg_mail){
				$state = '<font class="text-success">已启用</font>';
				$cla = ' current';
			}
			$mailItem[] = '<dl class="mail-item clearfix'.$cla.'"><div class="bg">启用此帐号</div><dt>服务器：</dt><dd>'.$mailServer[$key].'</dd><dt>端口：</dt><dd>'.$mailPort[$key].'</dd><dt>发信人：</dt><dd>'.$mailFrom[$key].'</dd><div class="opera">'.$state.'<a href="javascript:;" class="del btn btn-mini" title="删除"><s class="icon-trash"></s></a><a href="javascript:;" class="edit btn btn-mini" title="修改"><s class="icon-edit"></s></a></div></dl>';
		}
	}
	$huoniaoTag->assign('mailItem', join("", $mailItem));

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/siteConfig";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
