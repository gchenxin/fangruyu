<?php
/**
 * 黄页频道配置
 *
 * @version        $Id: huangyeConfig.php 2017-1-1 上午10:00:00 $
 * @package        HuoNiao.Huangye
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__) . "/../inc/config.inc.php");
checkPurview("huangyeConfig");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__) . "/../templates/huangye";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "huangyeConfig.html";

$action = $action == "" ? "huangye" : $action;
$dir = "../../templates/" . $action; //当前目录

//删除模板文件夹
if ($action == "delTpl") {
    if ($token == "") die('token传递失败！');
    if ($dopost == "") die('参数传递失败！');

    if (empty($floder)) die('请选择要删除的模板！');

    $dir = "../../templates/" . $dopost; //当前目录
    $floder = $dir . "/" . iconv('utf-8', 'gbk', $floder);
    $deldir = deldir($floder);
    if ($deldir) {
        adminLog("修改分类信息设置", "删除模板：" . $floder);
        echo '{"state": 100, "info": ' . json_encode("删除成功！") . '}';
    } else {
        echo '{"state": 200, "info": ' . json_encode("删除失败！") . '}';
    }
    die;
}

//频道配置参数
include(HUONIAOINC . "/config/" . $action . ".inc.php");

//异步提交
if ($type != "") {
    if ($token == "") die('token传递失败！');

    if ($type == "site") {
        //基本设置
        $customChannelName = $channelname;
        $customLogo = $articleLogo;
        $customLogoUrl = $litpic;
        $customSubDomain = $subdomain;
        $customChannelDomain = $channeldomain;
        $customChannelSwitch = $channelswitch;
        $customCloseCause = $closecause;

        //seo设置
        $customSeoTitle = $title;
        $customSeoKeyword = $keywords;
        $customSeoDescription = $description;
        $hotline_config = $hotline_rad;
        $customHotline = $hotline;

        if ($customChannelName == "" || $customLogo == "" || $customChannelDomain == "")
            die('{"state": 200, "info": ' . json_encode("请填写完整！") . '}');

        //验证域名是否被使用
        if (!operaDomain('check', $customChannelDomain, $action, 'config'))
            die('{"state": 200, "info": ' . json_encode("域名已被占用，请重试！") . '}');

        adminLog("修改信息设置", "基本设置");

        //单独配置域名
    } elseif ($type == "domain") {

        $customSubDomain = $subdomain;
        $customChannelDomain = $channeldomain;

    } elseif ($type == "temp") {

        //模板风格
        $customTemplate = $articleTemplate;
        $customTouchTemplate = $touchTemplate;

        adminLog("修改信息设置", "模板风格");

    }

    //域名操作
    operaDomain('update', $customChannelDomain, $action, 'config');

    //基本设置文件内容
    $customInc = "<" . "?php\r\n";
    //基本设置
    $customInc .= "\$customChannelName = '" . $customChannelName . "';\r\n";
    $customInc .= "\$customLogo = " . $customLogo . ";\r\n";
    $customInc .= "\$customLogoUrl = '" . $customLogoUrl . "';\r\n";
    $customInc .= "\$customSubDomain = " . $customSubDomain . ";\r\n";
    $customInc .= "\$customChannelSwitch = " . $customChannelSwitch . ";\r\n";
    $customInc .= "\$customCloseCause = '" . $customCloseCause . "';\r\n";
    $customInc .= "\$customSeoTitle = '" . $customSeoTitle . "';\r\n";
    $customInc .= "\$customSeoKeyword = '" . $customSeoKeyword . "';\r\n";
    $customInc .= "\$customSeoDescription = '" . $customSeoDescription . "';\r\n";
    $customInc .= "\$hotline_config = " . $hotline_config . ";\r\n";
    $customInc .= "\$customHotline = '" . $customHotline . "';\r\n";
    //模板风格
    $customInc .= "\$customTemplate = '" . $customTemplate . "';\r\n";
    $customInc .= "\$customTouchTemplate = '" . $customTouchTemplate . "';\r\n";
    $customInc .= "?" . ">";

    $customIncFile = HUONIAOINC . "/config/" . $action . ".inc.php";
    $fp = fopen($customIncFile, "w") or die('{"state": 200, "info": ' . json_encode("写入文件 $customIncFile 失败，请检查权限！") . '}');
    fwrite($fp, $customInc);
    fclose($fp);

    die('{"state": 100, "info": ' . json_encode("配置成功！") . '}');
    exit;
}

//验证模板文件
if (file_exists($tpl . "/" . $templates)) {

    //js
    $jsFile = array(
        'ui/bootstrap.min.js',
        'ui/jquery.dragsort-0.5.1.min.js',
        'publicUpload.js',
        'admin/huangye/huangyeConfig.js'
    );
    $huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

    include(HUONIAOINC . "/config/" . $action . ".inc.php");
    global $cfg_basehost;
    global $customUpload;
    if ($customUpload == 1) {
        global $custom_thumbSize;
        global $custom_thumbType;
        $huoniaoTag->assign('thumbSize', $custom_thumbSize);
        $huoniaoTag->assign('thumbType', "*." . str_replace("|", ";*.", $custom_thumbType));
    }

    //基本设置
    $huoniaoTag->assign('channelname', $customChannelName);

    //频道LOGO
    $huoniaoTag->assign('articleLogo', array('0', '1'));
    $huoniaoTag->assign('articleLogoNames', array('系统默认', '自定义'));
    $huoniaoTag->assign('articleLogoChecked', $customLogo);
    $huoniaoTag->assign('articleLogoUrl', $customLogoUrl);

    //启用频道域名-单选
    $huoniaoTag->assign('cfg_basehost', $cfg_basehost);
    $huoniaoTag->assign('subdomain', array('0', '1', '2'));
    $huoniaoTag->assign('subdomainNames', array('主域名', '子域名', '子目录'));
    $huoniaoTag->assign('subdomainChecked', $customSubDomain);

    //获取域名信息
    $domainInfo = getDomain($action, 'config');
    $huoniaoTag->assign('channeldomain', $domainInfo['domain']);

    //频道开关-单选
    $huoniaoTag->assign('channelswitch', array('0', '1'));
    $huoniaoTag->assign('channelswitchNames', array('启用', '禁用'));
    $huoniaoTag->assign('channelswitchChecked', $customChannelSwitch);
    $huoniaoTag->assign('closecause', $customCloseCause);

    //seo设置
    $huoniaoTag->assign('title', $customSeoTitle);
    $huoniaoTag->assign('keywords', $customSeoKeyword);
    $huoniaoTag->assign('description', $customSeoDescription);

    //咨询热线
    $huoniaoTag->assign('hotlineVal', array('0', '1'));
    $huoniaoTag->assign('hotlineNames', array('系统默认', '自定义'));
    $huoniaoTag->assign('hotlineChecked', (int)$hotline_config);
    $huoniaoTag->assign('hotline', $customHotline);

    //模板风格
    $floders = listDir($dir);
    $skins = array();
    if (!empty($floders)) {
        $i = 0;
        foreach ($floders as $key => $floder) {
            $config = $dir . '/' . $floder . '/config.xml';
            if (file_exists($config)) {
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
    $huoniaoTag->assign('tplList', $skins);
    $huoniaoTag->assign('articleTemplate', $customTemplate);


    //touch模板
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

                $skins[$i]['tplname'] = $tplname;
                $skins[$i]['directory'] = $floder;
                $skins[$i]['copyright'] = $copyright;
                $i++;
            }
        }
    }
    $huoniaoTag->assign('touchTplList', $skins);
    $huoniaoTag->assign('touchTemplate', $customTouchTemplate);

    $huoniaoTag->assign('action', $action);
    $huoniaoTag->compile_dir = HUONIAOROOT . "/templates_c/admin/huangye";  //设置编译目录
    // die($templates);
    $huoniaoTag->display($templates);
} else {
    echo $templates . "模板文件未找到！";
}
