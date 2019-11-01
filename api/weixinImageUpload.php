<?php
//系统核心配置文件
require_once(dirname(__FILE__).'/../include/common.inc.php');
$autoload = true;

global $cfg_wechatAppid;
global $cfg_wechatAppsecret;
global $cfg_uploadDir;
global $cfg_basehost;
global $cfg_secureAccess;

$media_id = $_REQUEST["media_id"];
$module   = $_REQUEST["module"];
$module   = empty($module) ? "siteConfig" : $module;

if(empty($media_id)) die(json_encode(array("state" => 200, "info" => "微信配置错误！")));

/* 引入系统参数 */
require_once(HUONIAOINC . "/config/" . $module . ".inc.php");

global $cfg_ftpUrl;
global $cfg_fileUrl;
global $cfg_uploadDir;
global $cfg_ftpType;
global $cfg_ftpState;
global $cfg_ftpDir;
global $cfg_quality;
global $cfg_softSize;
global $cfg_softType;
global $cfg_editorSize;
global $cfg_editorType;
global $cfg_videoSize;
global $cfg_videoType;
global $cfg_meditorPicWidth;
global $editorMarkState;

$editor_ftpState = $cfg_ftpState;
$editor_ftpDir = $cfg_ftpDir;
$editor_ftpType = $cfg_ftpType;

global $customUpload;
global $custom_uploadDir;
global $customFtp;
global $custom_ftpType;
global $custom_ftpState;
global $custom_ftpDir;
global $custom_ftpServer;
global $custom_ftpPort;
global $custom_ftpUser;
global $custom_ftpPwd;
global $custom_ftpDir;
global $custom_ftpUrl;
global $custom_ftpTimeout;
global $custom_ftpSSL;
global $custom_ftpPasv;
global $custom_OSSUrl;
global $custom_OSSBucket;
global $custom_EndPoint;
global $custom_OSSKeyID;
global $custom_OSSKeySecret;
global $cfg_QINIUAccessKey;
global $cfg_QINIUSecretKey;
global $cfg_QINIUbucket;
global $cfg_QINIUdomain;

//默认FTP帐号
if ($customFtp == 0) {
    $custom_ftpState = $cfg_ftpState;
    $custom_ftpType = $cfg_ftpType;
    $custom_ftpSSL = $cfg_ftpSSL;
    $custom_ftpPasv = $cfg_ftpPasv;
    $custom_ftpUrl = $cfg_ftpUrl;
    $custom_ftpServer = $cfg_ftpServer;
    $custom_ftpPort = $cfg_ftpPort;
    $custom_ftpDir = $cfg_ftpDir;
    $custom_ftpUser = $cfg_ftpUser;
    $custom_ftpPwd = $cfg_ftpPwd;
    $custom_ftpTimeout = $cfg_ftpTimeout;
    $custom_OSSUrl = $cfg_OSSUrl;
    $custom_OSSBucket = $cfg_OSSBucket;
    $custom_EndPoint = $cfg_EndPoint;
    $custom_OSSKeyID = $cfg_OSSKeyID;
    $custom_OSSKeySecret = $cfg_OSSKeySecret;
    $custom_QINIUAccessKey = $cfg_QINIUAccessKey;
    $custom_QINIUSecretKey = $cfg_QINIUSecretKey;
    $custom_QINIUbucket = $cfg_QINIUbucket;
    $custom_QINIUdomain = $cfg_QINIUdomain;
}

global $thumbMarkState;
global $atlasMarkState;
global $editorMarkState;
global $waterMarkWidth;
global $waterMarkHeight;
global $waterMarkPostion;
global $waterMarkType;
global $waterMarkText;
global $markFontfamily;
global $markFontsize;
global $markFontColor;
global $markFile;
global $markPadding;
global $markTransparent;
global $markQuality;

$markConfig = array(
    "thumbMarkState" => $thumbMarkState,
    "atlasMarkState" => $atlasMarkState,
    "editorMarkState" => $editorMarkState,
    "waterMarkWidth" => $waterMarkWidth,
    "waterMarkHeight" => $waterMarkHeight,
    "waterMarkPostion" => $waterMarkPostion,
    "waterMarkType" => $waterMarkType,
    "waterMarkText" => $waterMarkText,
    "markFontfamily" => $markFontfamily,
    "markFontsize" => $markFontsize,
    "markFontColor" => $markFontColor,
    "markFile" => $markFile,
    "markPadding" => $markPadding,
    "markTransparent" => $markTransparent,
    "markQuality" => $markQuality
);

if ($modelType != "siteConfig") {
    global $customMark;
    global $custom_thumbMarkState;
    global $custom_atlasMarkState;
    global $custom_editorMarkState;
    global $custom_waterMarkWidth;
    global $custom_waterMarkHeight;
    global $custom_waterMarkPostion;
    global $custom_waterMarkType;
    global $custom_waterMarkText;
    global $custom_markFontfamily;
    global $custom_markFontsize;
    global $custom_markFontColor;
    global $custom_markFile;
    global $custom_markPadding;
    global $custom_markTransparent;
    global $custom_markQuality;

    if ($customMark == 1) {
        $markConfig = array(
            "thumbMarkState" => $custom_thumbMarkState,
            "atlasMarkState" => $custom_atlasMarkState,
            "editorMarkState" => $custom_editorMarkState,
            "waterMarkWidth" => $custom_waterMarkWidth,
            "waterMarkHeight" => $custom_waterMarkHeight,
            "waterMarkPostion" => $custom_waterMarkPostion,
            "waterMarkType" => $custom_waterMarkType,
            "waterMarkText" => $custom_waterMarkText,
            "markFontfamily" => $custom_markFontfamily,
            "markFontsize" => $custom_markFontsize,
            "markFontColor" => $custom_markFontColor,
            "markFile" => $custom_markFile,
            "markPadding" => $custom_markPadding,
            "markTransparent" => $custom_markTransparent,
            "markQuality" => $custom_markQuality
        );
    }
}

if ($customUpload == 1 && $custom_ftpState == 1) {
    $editor_fileUrl = $custom_ftpUrl;
    $editor_uploadDir = $custom_uploadDir;
    $editor_ftpDir = $custom_ftpDir;
}
//普通FTP模式
if ($customFtp == 1 && $custom_ftpType == 0 && $custom_ftpState == 1) {
    $editor_ftpType = 0;
    $editor_ftpState = 1;

//阿里云OSS
} elseif ($customFtp == 1 && $custom_ftpType == 1) {
    $editor_ftpType = 1;
    $editor_ftpState = 0;

    //七牛云
} elseif ($customFtp == 1 && $custom_ftpType == 2) {
    $editor_ftpType = 2;
    $editor_ftpState = 0;

//本地
} elseif ($customFtp == 1 && $custom_ftpType == 0 && $custom_ftpState == 0) {
    $editor_ftpType = 3;
    $editor_ftpState = 0;

}

if($cfg_wechatAppid && $cfg_wechatAppsecret){
    include_once(HUONIAOROOT."/include/class/WechatJSSDK.class.php");
    $jssdk = new WechatJSSDK($cfg_wechatAppid, $cfg_wechatAppsecret);
    $access_token = $jssdk->getAccessToken();

    //微信上传下载媒体文件
    $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";

    //保存的路径及文件名
    $dir = $cfg_uploadDir . "/" . $module . "/weixin/" . date("Y") . "/" . date("m") . "/" . date("d") . "/";
    $name = time() . rand(1, 10000) . ".jpg";
    if(!file_exists(HUONIAOROOT . $dir)){
        if (!mkdir(HUONIAOROOT . $dir, 0777, true)){
            return array("state" => 200, "info" => "服务器没有创建文件夹权限！");
        }
    }

    /* 下载文件 */
    include_once(HUONIAOROOT."/include/class/httpdown.class.php");
    $file = new httpdown();
    $file->OpenUrl($url); # 远程文件地址
    $file->SaveToBin(HUONIAOROOT . $dir . $name); # 保存路径及文件名
    $file->Close(); # 释放资源

    $imageInfo = getimagesize(HUONIAOROOT . $dir . $name);
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $fileSize = filesize(HUONIAOROOT . $dir . $name);

    $filepath_1 = str_replace($cfg_uploadDir, '', $dir . $name);

    //普通FTP模式
    if ($editor_ftpType == 0 && $editor_ftpState == 1) {
        $ftpConfig = array();
        if ($modelType != "siteConfig" && $customFtp == 1 && $custom_ftpState == 1) {
            $ftpConfig = array(
                "on" => $custom_ftpState, //是否开启
                "host" => $custom_ftpServer, //FTP服务器地址
                "port" => $custom_ftpPort, //FTP服务器端口
                "username" => $custom_ftpUser, //FTP帐号
                "password" => $custom_ftpPwd,  //FTP密码
                "attachdir" => $custom_ftpDir,  //FTP上传目录
                "attachurl" => $custom_ftpUrl,  //远程附件地址
                "timeout" => $custom_ftpTimeout,  //FTP超时
                "ssl" => $custom_ftpSSL,  //启用SSL连接
                "pasv" => $custom_ftpPasv  //被动模式连接
            );
        }

        include_once(HUONIAOROOT."/include/class/ftp.class.php");
        $huoniao_ftp = new ftp($ftpConfig);
        $huoniao_ftp->connect();
        if ($huoniao_ftp->connectid) {
            $huoniao_ftp->upload(HUONIAOROOT . $dir . $name, $editor_ftpDir . $dir . $name);
        }

        //阿里云OSS
    } elseif ($editor_ftpType == 1) {
        $OSSConfig = array();
        if ($modelType != "siteConfig") {
            $OSSConfig = array(
                "bucketName" => $custom_OSSBucket,
                "endpoint" => $custom_EndPoint,
                "accessKey" => $custom_OSSKeyID,
                "accessSecret" => $custom_OSSKeySecret
            );
        }

        include_once(HUONIAOROOT."/include/class/aliyunOSS.class.php");
        $aliyunOSS = new aliyunOSS($OSSConfig);
        $aliyunOSS->upload($filepath_1, HUONIAOROOT . $dir . $name);
    } elseif ($editor_ftpType == 2) {
        $autoload = true;
        $accessKey = $custom_QINIUAccessKey;
        $secretKey = $custom_QINIUSecretKey;
        // 构建鉴权对象
        $auth = new \Qiniu\Auth($accessKey, $secretKey);
        // 要上传的空间
        $bucket = $custom_QINIUbucket;
        // 上传到七牛后保存的文件名
        //$key = substr(str_replace('/', '_', $filepath_1), 1);
        // $key = substr($filepath_1, 1);
        // 生成上传 Token
        $token = $auth->uploadToken($bucket,$filepath_1);
        // 要上传文件的本地路径
        $filePath = $local_fileUrl;
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new \Qiniu\Storage\UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        $uploadMgr->putFile($token, $filepath_1, HUONIAOROOT . $dir . $name);
    }

    $autoload = false;
    $dsql = new dsql($dbo);
    $userLogin = new userLogin($dbo);
    $userid = $userLogin->getMemberID();

    $attachment = $dsql->SetQuery("INSERT INTO `#@__attachment` (`userid`, `filename`, `filetype`, `filesize`, `path`, `width`, `height`, `pubdate`) VALUES ('$userid', '" . $name . "', 'image', '" . $fileSize . "', '" . $filepath_1 . "', '$width', '$height', '" . GetMkTime(time()) . "')");
    $aid = $dsql->dsqlOper($attachment, "lastid");
    if (!$aid) die('{"state":"数据写入失败！"}');

    $RenrenCrypt = new RenrenCrypt();
    $fid = base64_encode($RenrenCrypt->php_encrypt($aid));

    $path = $cfg_attachment . $fid;

    //20160128修复编辑器输出域名问题
    global $cfg_basehost;
    $cfg_attachment = str_replace("http://" . $cfg_basehost, "", $cfg_attachment);
    $attachmentPath = str_replace("https://" . $cfg_basehost, "", $cfg_attachment);
    $path = $attachmentPath . $fid;

    echo json_encode(array("state" => 100, "fid" => $fid, "url" => $path, "turl" => getFilePath($fid)));

}else{
    echo json_encode(array("state" => 200, "info" => "微信配置错误！"));
}
