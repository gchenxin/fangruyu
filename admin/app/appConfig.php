<?php
/**
 * APP基本配置
 *
 * @version        $Id: appConfig.php 2017-04-12 下午15:07:11 $
 * @package        HuoNiao.APP
 * @copyright      Copyright (c) 2013 - 2017, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("appConfig");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/app";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "appConfig.html";

$dir = "../../static/images/admin/app"; //当前目录

//异步提交
if(!empty($_POST)){
	if($token == "") die('token传递失败！');

	$sql = $dsql->SetQuery("SELECT `android_download` FROM `#@__app_config`");
	$ret = $dsql->dsqlOper($sql, "totalCount");

	//存在则更新，不存在插入
	$map_set = empty($map_set) ? 1 : $map_set;
	$peisong_map_set = empty($peisong_map_set) ? 1 : $peisong_map_set;
	$android_force = (int)$android_force;
	$ios_force = (int)$ios_force;
	$business_android_force = (int)$business_android_force;
	$business_ios_force = (int)$business_ios_force;
	$peisong_android_force = (int)$peisong_android_force;
	$peisong_ios_force = (int)$peisong_ios_force;
    $ios_shelf = (int)$ios_shelf;


	$buttonArr = array();
	if($bottomButton){
		foreach ($bottomButton['name'] as $key => $val) {
			$name   = $val;
			$icon   = $bottomButton['icon'][$key];
			$icon_h = $bottomButton['icon_h'][$key];
			$url    = $bottomButton['url'][$key];
			$login  = $bottomButton['login'][$key] ? $bottomButton['login'][$key] : 0;
			if($name){
				array_push($buttonArr, array(
					'name'   => $name,
					'icon'   => $icon,
					'icon_h' => $icon_h,
					'url'    => $url,
					'login'  => $login
				));
			}
		}
	}

	$customBottomButton = serialize($buttonArr);




	if($ret){
		$sql = $dsql->SetQuery("UPDATE `#@__app_config` SET `appname` = '$appname', `logo` = '$logo', `android_version` = '$android_version', `ios_version` = '$ios_version', `android_download` = '$android_download', `yyb_download` = '$yyb_download', `ios_download` = '$ios_download', `android_guide` = '$android_guide', `ios_guide` = '$ios_guide', `ad_pic` = '$ad_pic', `ad_link` = '$ad_link', `ad_time` = '$ad_time', `android_index` = '$android_index', `ios_index` = '$ios_index', `map_baidu_android` = '$map_baidu_android', `map_baidu_ios` = '$map_baidu_ios', `map_google_android` = '$map_google_android', `map_google_ios` = '$map_google_ios', `map_amap_android` = '$map_amap_android', `map_amap_ios` = '$map_amap_ios', `map_set` = '$map_set', `android_update` = '$android_update', `android_force` = '$android_force', `android_size` = '$android_size', `android_note` = '$android_note', `ios_update` = '$ios_update', `ios_force` = '$ios_force', `ios_note` = '$ios_note', `business_appname` = '$business_appname', `business_android_version` = '$business_android_version', `business_android_update` = '$business_android_update', `business_android_force` = '$business_android_force', `business_android_size` = '$business_android_size', `business_android_note` = '$business_android_note', `business_ios_version` = '$business_ios_version', `business_ios_update` = '$business_ios_update', `business_ios_force` = '$business_ios_force', `business_ios_note` = '$business_ios_note', `business_android_download` = '$business_android_download', `business_yyb_download` = '$business_yyb_download', `business_ios_download` = '$business_ios_download', `peisong_appname` = '$peisong_appname', `peisong_android_version` = '$peisong_android_version', `peisong_android_update` = '$peisong_android_update', `peisong_android_force` = '$peisong_android_force', `peisong_android_size` = '$peisong_android_size', `peisong_android_note` = '$peisong_android_note', `peisong_ios_version` = '$peisong_ios_version', `peisong_ios_update` = '$peisong_ios_update', `peisong_ios_force` = '$peisong_ios_force', `peisong_ios_note` = '$peisong_ios_note', `peisong_android_download` = '$peisong_android_download', `peisong_yyb_download` = '$peisong_yyb_download', `peisong_ios_download` = '$peisong_ios_download', `business_logo` = '$business_logo', `peisong_logo` = '$peisong_logo', `rongKeyID` = '$rongKeyID', `rongKeySecret` = '$rongKeySecret', `peisong_map_baidu_android` = '$peisong_map_baidu_android', `peisong_map_baidu_ios` = '$peisong_map_baidu_ios', `peisong_map_google_android` = '$peisong_map_google_android', `peisong_map_google_ios` = '$peisong_map_google_ios', `peisong_map_amap_android` = '$peisong_map_amap_android', `peisong_map_amap_ios` = '$peisong_map_amap_ios', `peisong_map_set` = '$peisong_map_set', `template` = '$touchTemplate', `customBottomButton` = '$customBottomButton', `ios_shelf` = $ios_shelf");
	}else{
		$sql = $dsql->SetQuery("INSERT INTO `#@__app_config` (`appname`, `logo`, `android_version`, `ios_version`, `android_download`, `yyb_download`, `ios_download`, `android_guide`, `ios_guide`, `ad_pic`, `ad_link`, `ad_time`, `android_index`, `ios_index`, `map_baidu_android`, `map_baidu_ios`, `map_google_android`, `map_google_ios`, `map_amap_android`, `map_amap_ios`, `map_set`, `android_update`, `android_size`, `android_note`, `ios_update`, `ios_note`, `business_appname`, `business_android_version`, `business_android_update`, `business_android_size`, `business_android_note`, `business_ios_version`, `business_ios_update`, `business_ios_note`,  `business_android_download`, `business_yyb_download`, `business_ios_download`, `peisong_appname`, `peisong_android_version`, `peisong_android_update`, `peisong_android_size`, `peisong_android_note`, `peisong_ios_version`, `peisong_ios_update`, `peisong_ios_note`, `peisong_android_download`, `peisong_yyb_download`, `peisong_ios_download`, `business_logo`, `peisong_logo`, `android_force`, `ios_force`, `business_android_force`, `business_ios_force`, `peisong_android_force`, `peisong_ios_force`, `rongKeyID`, `rongKeySecret`, `peisong_map_baidu_android`, `peisong_map_baidu_ios`, `peisong_map_google_android`, `peisong_map_google_ios`, `peisong_map_amap_android`, `peisong_map_amap_ios`, `peisong_map_set`, `template`, `customBottomButton`, `ios_shelf`) VALUES ('$appname', '$logo', '$android_version', '$ios_version', '$android_download', '$yyb_download', '$ios_download', '$android_guide', '$ios_guide', '$ad_pic', '$ad_link', '$ad_time', '$android_index', '$ios_index', '$map_baidu_android', '$map_baidu_ios', '$map_google_android', '$map_google_ios', '$map_amap_android', '$map_amap_ios', '$map_set', '$android_update', '$android_size', '$android_note', '$ios_update', '$ios_note', '$business_appname', '$business_android_version', '$business_android_update', '$business_android_size', '$business_android_note', '$business_ios_version', '$business_ios_update', '$business_ios_note', '$business_android_download', '$business_yyb_download', '$business_ios_download', '$peisong_appname', '$peisong_android_version', '$peisong_android_update', '$peisong_android_size', '$peisong_android_note', '$peisong_ios_version', '$peisong_ios_update', '$peisong_ios_note', '$peisong_android_download', '$peisong_yyb_download', '$peisong_ios_download', '$business_logo', '$peisong_logo', '$android_force', '$ios_force', '$business_android_force', '$business_ios_force', '$peisong_android_force', '$peisong_ios_force', '$rongKeyID', '$rongKeySecret', '$peisong_map_baidu_android', '$peisong_map_baidu_ios', '$peisong_map_google_android', '$peisong_map_google_ios', '$peisong_map_amap_android', '$peisong_map_amap_ios', '$peisong_map_set', '$touchTemplate', '$customBottomButton', $ios_shelf)");
	}

	$ret = $dsql->dsqlOper($sql, "update");
	if($ret == "ok"){
		updateAppConfig();  //更新APP配置文件
		die('{"state": 100, "info": '.json_encode("配置成功！").'}');
	}else{
		die('{"state": 200, "info": '.json_encode("配置失败，请联系管理员！" . $sql).'}');
	}
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/bootstrap-datetimepicker.min.js',
		'ui/jquery.dragsort-0.5.1.min.js',
		'publicUpload.js',
		'admin/app/appConfig.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$installWaimai = getModuleTitle(array('name'=>'waimai'));
	$huoniaoTag->assign('installWaimai', $installWaimai);
	$huoniaoTag->assign('action', 'app');

	//模板风格
    $floders = listDir($dir);
    $skins = array();
    $floders = listDir($dir . '/touch');
    $skins = array();
    if (!empty($floders)) {
        $i = 0;
        foreach ($floders as $key => $floder) {
            $config = $dir . '/touch/' . $floder . '/config.xml';
            if (file_exists($config)) {
                //解析xml配置文件
                $xml = new DOMDocument();
                libxml_disable_entity_loader(false);
                $xml->load($config);
                $data = $xml->getElementsByTagName('Data')->item(0);
                $tplname = $data->getElementsByTagName("tplname")->item(0)->nodeValue;
                $copyright = $data->getElementsByTagName("copyright")->item(0)->nodeValue;

                if(!strstr($floder, '__')) {
                    $skins[$i]['tplname'] = $tplname;
                    $skins[$i]['directory'] = $floder;
                    $skins[$i]['copyright'] = $copyright;
                    $i++;
                }
            }
        }
    }
    $huoniaoTag->assign('touchTplList', $skins);


	//查询当前配置
	$sql = $dsql->SetQuery("SELECT * FROM `#@__app_config` LIMIT 1");
	$ret = $dsql->dsqlOper($sql, "results");
	if($ret){
		$data = $ret[0];

		$huoniaoTag->assign('appname', $data['appname']);
		$huoniaoTag->assign('logo', $data['logo']);
		$huoniaoTag->assign('android_version', $data['android_version']);
		$huoniaoTag->assign('android_update', $data['android_update']);
		$huoniaoTag->assign('android_force', $data['android_force']);
		$huoniaoTag->assign('android_size', $data['android_size']);
		$huoniaoTag->assign('android_note', $data['android_note']);
		$huoniaoTag->assign('ios_version', $data['ios_version']);
		$huoniaoTag->assign('ios_update', $data['ios_update']);
		$huoniaoTag->assign('ios_force', $data['ios_force']);
		$huoniaoTag->assign('ios_note', $data['ios_note']);
		$huoniaoTag->assign('android_download', $data['android_download']);
		$huoniaoTag->assign('yyb_download', $data['yyb_download']);
		$huoniaoTag->assign('ios_download', $data['ios_download']);
		$huoniaoTag->assign('android_guide', json_encode(explode(",", $data['android_guide'])));
		$huoniaoTag->assign('ios_guide', json_encode(explode(",", $data['ios_guide'])));
		$huoniaoTag->assign('ad_pic', $data['ad_pic']);
		$huoniaoTag->assign('ad_link', $data['ad_link']);
		$huoniaoTag->assign('ad_time', $data['ad_time']);
		$huoniaoTag->assign('android_index', $data['android_index']);
		$huoniaoTag->assign('ios_index', $data['ios_index']);
		$huoniaoTag->assign('map_baidu_android', $data['map_baidu_android']);
		$huoniaoTag->assign('map_baidu_ios', $data['map_baidu_ios']);
		$huoniaoTag->assign('map_google_android', $data['map_google_android']);
		$huoniaoTag->assign('map_google_ios', $data['map_google_ios']);
        $huoniaoTag->assign('map_amap_android', $data['map_amap_android']);
        $huoniaoTag->assign('map_amap_ios', $data['map_amap_ios']);
		$huoniaoTag->assign('map_set', empty($data['map_set']) ? 1 : $data['map_set']);
		$huoniaoTag->assign('business_appname', $data['business_appname']);
		$huoniaoTag->assign('business_android_version', $data['business_android_version']);
		$huoniaoTag->assign('business_android_update', $data['business_android_update']);
		$huoniaoTag->assign('business_android_force', $data['business_android_force']);
		$huoniaoTag->assign('business_android_size', $data['business_android_size']);
		$huoniaoTag->assign('business_android_note', $data['business_android_note']);
		$huoniaoTag->assign('business_ios_version', $data['business_ios_version']);
		$huoniaoTag->assign('business_ios_update', $data['business_ios_update']);
		$huoniaoTag->assign('business_ios_force', $data['business_ios_force']);
		$huoniaoTag->assign('business_ios_note', $data['business_ios_note']);
		$huoniaoTag->assign('business_android_download', $data['business_android_download']);
		$huoniaoTag->assign('business_yyb_download', $data['business_yyb_download']);
		$huoniaoTag->assign('business_ios_download', $data['business_ios_download']);
		$huoniaoTag->assign('peisong_appname', $data['peisong_appname']);
		$huoniaoTag->assign('peisong_android_version', $data['peisong_android_version']);
		$huoniaoTag->assign('peisong_android_update', $data['peisong_android_update']);
		$huoniaoTag->assign('peisong_android_force', $data['peisong_android_force']);
		$huoniaoTag->assign('peisong_android_size', $data['peisong_android_size']);
		$huoniaoTag->assign('peisong_android_note', $data['peisong_android_note']);
		$huoniaoTag->assign('peisong_ios_version', $data['peisong_ios_version']);
		$huoniaoTag->assign('peisong_ios_update', $data['peisong_ios_update']);
		$huoniaoTag->assign('peisong_ios_force', $data['peisong_ios_force']);
		$huoniaoTag->assign('peisong_ios_note', $data['peisong_ios_note']);
		$huoniaoTag->assign('peisong_android_download', $data['peisong_android_download']);
		$huoniaoTag->assign('peisong_yyb_download', $data['peisong_yyb_download']);
		$huoniaoTag->assign('peisong_ios_download', $data['peisong_ios_download']);
		$huoniaoTag->assign('business_logo', $data['business_logo']);
		$huoniaoTag->assign('peisong_logo', $data['peisong_logo']);
		$huoniaoTag->assign('rongKeyID', $data['rongKeyID']);
		$huoniaoTag->assign('rongKeySecret', $data['rongKeySecret']);
        $huoniaoTag->assign('peisong_map_baidu_android', $data['peisong_map_baidu_android']);
        $huoniaoTag->assign('peisong_map_baidu_ios', $data['peisong_map_baidu_ios']);
        $huoniaoTag->assign('peisong_map_google_android', $data['peisong_map_google_android']);
        $huoniaoTag->assign('peisong_map_google_ios', $data['peisong_map_google_ios']);
        $huoniaoTag->assign('peisong_map_amap_android', $data['peisong_map_amap_android']);
        $huoniaoTag->assign('peisong_map_amap_ios', $data['peisong_map_amap_ios']);
		$huoniaoTag->assign('peisong_map_set', empty($data['peisong_map_set']) ? 1 : $data['peisong_map_set']);
        $huoniaoTag->assign('touchTemplate', $data['template']);
		$huoniaoTag->assign('ios_shelf', $data['ios_shelf']);

		$customBottomButton = $data['customBottomButton'] ? unserialize($data['customBottomButton']) : array();
		if(!$customBottomButton){
            //取系统默认信息
            $handels = new handlers('siteConfig', 'touchHomePageFooter');
            $return = $handels->getHandle(array('version' => '2.0'));

            if($return['state'] == 100){
                $customBottomButton = $return['info'];
            }

        }
		$huoniaoTag->assign('customBottomButton', $customBottomButton);
	}else{
		$huoniaoTag->assign('android_guide', '[]');
		$huoniaoTag->assign('ios_guide', '[]');
	}

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/app";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
