<?php
/**
 * 上传处理插件
 *
 * @version        $Id: upload.class.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.class
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
require_once('./common.inc.php');

$autoload = true;
function classLoaderQiniu($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = HUONIAOROOT . '/api/upload/' . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('classLoaderQiniu');
require(HUONIAOROOT . '/api/upload/Qiniu/functions.php');
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

$autoload = false;
header("Content-Type: text/html; charset=utf-8");
$mod = $_REQUEST['mod'];       //模块 article:新闻
$type = str_replace('.', '', $_REQUEST['type']);      //类型 thumb:缩略图 atlas:图集
$filetype = str_replace('.', '', $_REQUEST['filetype']);  //指定文件类型 此处为兼容flash/mp3文件
$o = $_REQUEST['o'];         //如果为true则保留大图
$picpath = $_POST['picpath'];      //要操作的图片路径
$direction = $_POST['direction'];    //图片旋转方向 left:逆时针 right:顺时针
$aid = $_POST['aid'];    //图片旋转方向 left:逆时针 right:顺时针
$custom_folder = "";

if (!empty($aid)) {
    $RenrenCrypt = new RenrenCrypt();
    $aid = $RenrenCrypt->php_decrypt(base64_decode($aid));
}
$aid = (int)$aid;

global $cfg_fileUrl;
global $site_fileUrl;
global $cfg_uploadDir;
global $cfg_softSize;
global $cfg_softType;
global $cfg_thumbSize;
global $cfg_thumbType;
global $cfg_atlasSize;
global $cfg_atlasType;
global $cfg_photoSize;
global $cfg_photoType;
global $cfg_flashSize;
global $cfg_audioSize;
global $cfg_audioType;
global $cfg_videoSize;
global $cfg_videoType;
global $cfg_thumbSmallWidth;
global $cfg_thumbSmallHeight;
global $cfg_thumbMiddleWidth;
global $cfg_thumbMiddleHeight;
global $cfg_thumbLargeWidth;
global $cfg_thumbLargeHeight;
global $cfg_atlasSmallWidth;
global $cfg_atlasSmallHeight;
global $cfg_photoSmallWidth;
global $cfg_photoSmallHeight;
global $cfg_photoMiddleWidth;
global $cfg_photoMiddleHeight;
global $cfg_photoLargeWidth;
global $cfg_photoLargeHeight;
global $cfg_photoCutType;
global $cfg_photoCutPostion;
global $cfg_quality;
global $cfg_ftpType;
global $cfg_ftpState;
global $cfg_ftpDir;

global $thumbMarkState;
global $atlasMarkState;
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

$custom_folder = $cfg_uploadDir;

$markConfig = array(
    "thumbMarkState" => $thumbMarkState,
    "atlasMarkState" => $atlasMarkState,
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

//载入频道配置参数
if ($mod != "siteConfig") {
    require_once(HUONIAOINC . "/config/" . $mod . ".inc.php");
    global $customUpload;
    global $custom_uploadDir;
    global $custom_softSize;
    global $custom_softType;
    global $custom_thumbSize;
    global $custom_thumbType;
    global $custom_atlasSize;
    global $custom_atlasType;
    global $custom_thumbSmallWidth;
    global $custom_thumbSmallHeight;
    global $custom_thumbMiddleWidth;
    global $custom_thumbMiddleHeight;
    global $custom_thumbLargeWidth;
    global $custom_thumbLargeHeight;
    global $custom_atlasSmallWidth;
    global $custom_atlasSmallHeight;
    global $custom_photoCutType;
    global $custom_photoCutPostion;
    global $custom_quality;
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
    global $custom_QINIUAccessKey;
    global $custom_QINIUSecretKey;
    global $custom_QINIUbucket;
    global $custom_QINIUdomain;

    global $customMark;
    global $custom_thumbMarkState;
    global $custom_atlasMarkState;
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
        $thumbMarkState = $custom_thumbMarkState;
        $atlasMarkState = $custom_atlasMarkState;

        $markConfig = array(
            "thumbMarkState" => $custom_thumbMarkState,
            "atlasMarkState" => $custom_atlasMarkState,
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

    if ($customUpload == 1) {
        $cfg_uploadDir = $custom_uploadDir;
        $cfg_softSize = $custom_softSize;
        $cfg_softType = $custom_softType;
        $cfg_thumbSize = $custom_thumbSize;
        $cfg_thumbType = $custom_thumbType;
        $cfg_atlasSize = $custom_atlasSize;
        $cfg_atlasType = $custom_atlasType;
        $cfg_thumbSmallWidth = $custom_thumbSmallWidth;
        $cfg_thumbSmallHeight = $custom_thumbSmallHeight;
        $cfg_thumbMiddleWidth = $custom_thumbMiddleWidth;
        $cfg_thumbMiddleHeight = $custom_thumbMiddleHeight;
        $cfg_thumbLargeWidth = $custom_thumbLargeWidth;
        $cfg_thumbLargeHeight = $custom_thumbLargeHeight;
        $cfg_atlasSmallWidth = $custom_atlasSmallWidth;
        $cfg_atlasSmallHeight = $custom_atlasSmallHeight;
        $cfg_photoCutType = $custom_photoCutType;
        $cfg_photoCutPostion = $custom_photoCutPostion;
        $cfg_quality = $custom_quality;
    }

    if($_POST['thumbLargeWidth']){
        $cfg_thumbLargeWidth = $_POST['thumbLargeWidth'];
    }
    if($_POST['thumbLargeHeight']){
        $cfg_thumbLargeHeight = $_POST['thumbLargeHeight'];
    }

    //普通FTP模式
    if ($customFtp == 1 && $custom_ftpType == 0 && $custom_ftpState == 1) {
        $cfg_ftpType = 0;
        $cfg_ftpState = 1;
        $cfg_ftpDir = $custom_ftpDir;

        //阿里云OSS
    } elseif ($customFtp == 1 && $custom_ftpType == 1) {
        $cfg_ftpType = 1;
        $cfg_ftpState = 0;
        $cfg_ftpDir = $custom_uploadDir;

        //七牛云
    } elseif ($customFtp == 1 && $custom_ftpType == 2) {
        $cfg_ftpType = 2;
        $cfg_ftpState = 0;
        $cfg_ftpDir = $custom_uploadDir;

        //本地
    } elseif ($customFtp == 1 && $custom_ftpType == 0 && $custom_ftpState == 0) {
        $cfg_ftpType = 3;
        $cfg_ftpState = 0;
        $cfg_ftpDir = $custom_uploadDir;
    }

    //自定义FTP配置
    if ($customFtp == 1) {

        //阿里云OSS
        if ($custom_ftpType == 1) {
            if (strpos($custom_OSSUrl, "http://") !== false) {
                $site_fileUrl = $custom_OSSUrl;
            } else {
                $site_fileUrl = "http://" . $custom_OSSUrl;
            }
            $custom_folder = $custom_uploadDir;
            //七牛云
        } elseif ($custom_ftpType == 2) {
            if (strpos($custom_QINIUdomain, "http://") !== false) {
                $site_fileUrl = $custom_QINIUdomain;
            } else {
                $site_fileUrl = "http://" . $custom_QINIUdomain;
            }
            $custom_folder = $custom_uploadDir;
            //普通FTP
        } elseif ($custom_ftpState == 1) {
            $site_fileUrl = $custom_ftpUrl . str_replace(".", "", $custom_ftpDir);
            $custom_folder = $custom_ftpDir;
            //本地
        } else {
            if ($customUpload == 1) {
                $site_fileUrl = ".." . $custom_uploadDir;
                $custom_folder = $custom_uploadDir;
            } else {
                $site_fileUrl = ".." . $cfg_uploadDir;
            }
        }
        //系统默认
    } else {
        //阿里云OSS
        if ($cfg_ftpType == 1) {
            if (strpos($cfg_OSSUrl, "http://") !== false) {
                $site_fileUrl = $cfg_OSSUrl;
            } else {
                $site_fileUrl = "http://" . $cfg_OSSUrl;
            }
            //七牛云
        } elseif ($cfg_ftpType == 2) {
            if (strpos($cfg_QINIUdomain, "http://") !== false) {
                $site_fileUrl = $cfg_QINIUdomain;
            } else {
                $site_fileUrl = "http://" . $cfg_QINIUdomain;
            }
            //普通FTP
        } elseif ($cfg_ftpState == 1) {
            $site_fileUrl = $cfg_fileUrl;
            $custom_folder = $cfg_ftpDir;
            //本地
        } else {
            $site_fileUrl = ".." . $cfg_uploadDir;
        }

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
    }
} else {
    //阿里云OSS
    if ($cfg_ftpType == 1) {
        if (strpos($cfg_OSSUrl, "http://") !== false) {
            $site_fileUrl = $cfg_OSSUrl;
        } else {
            $site_fileUrl = "http://" . $cfg_OSSUrl;
        }
        //七牛云
    } elseif ($cfg_ftpType == 2) {
        if (strpos($cfg_QINIUdomain, "http://") !== false) {
            $site_fileUrl = $cfg_QINIUdomain;
        } else {
            $site_fileUrl = "http://" . $cfg_QINIUdomain;
        }
        //普通FTP
    } elseif ($cfg_ftpState == 1) {
        $site_fileUrl = $cfg_fileUrl;
        $custom_folder = $cfg_ftpDir;
        //本地
    } else {
        $site_fileUrl = ".." . $cfg_uploadDir;
    }
}

//删除文件
if ($type == "delThumb" || $type == "delAtlas" || $type == "delConfig" || $type == "delLogo" || $type == "delFriendLink" || $type == "delAdv" || $type == "delCard" || $type == "delBrand" || $type == "delbrandLogo" || $type == "delFile" || $type == "delVideo" || $type == "delFlash" || $type == "delPhoto" || $type == "delthumb" || $type == "delatlas" || $type == "delconfig" || $type == "dellogo" || $type == "delfriendLink" || $type == "deladv" || $type == "delcard" || $type == "delbrand" || $type == "delbrandLogo" || $type == "delfile" || $type == "delvideo" || $type == "delflash" || $type == "delphoto" || $type == "deladvthumb" || $type == "delsingle" || $type == "delSingle" || $type == "delfilenail") {

  $fids = explode(',', $picpath);
  foreach ($fids as $key => $value) {
    //验证是否为微信传图
    $RenrenCrypt = new RenrenCrypt();
    $id = $RenrenCrypt->php_decrypt(base64_decode($value));

    if (!is_numeric($id)) return;

    $attachment = $dsql->SetQuery("SELECT `path` FROM `#@__attachment` WHERE `id` = " . $id);
    $results = $dsql->dsqlOper($attachment, "results");

    if (!$results) return;  //数据不存在
    $picpath_ = $results[0]['path'];

    //删除缩略图，如果图片为提取的内容第一张，则不执行删除操作
      if($type == 'delthumb' && strstr($picpath_, 'editor')) return;

    //微信传图删除
    if(strstr($picpath_, 'wxupimg')){

      //取ticket
      $sql = $dsql->SetQuery("SELECT `ticket` FROM `#@__site_wxupimg` WHERE `fid` = '$value'");
      $ret = $dsql->dsqlOper($sql, "results");
      if($ret){
        $ticket = $ret[0]['ticket'];

        $claFile = HUONIAOROOT . '/api/handlers/siteConfig.class.php';
        if (is_file($claFile)) {
            include_once $claFile;
        } else {
            return;
        }

        $ser = new siteConfig(array('ticket' => $ticket, 'fid' => $value));
        $ser->delWeixinUpImg();
      }


    }else{
      delPicFile($picpath, $type, $mod);
    }
  }

//旋转图片
} else if ($type == "rotateAtlas") {
    $pathModel = $action == "thumb" ? array("small", "middle", "large", "o_large") : array("large", "small");
    $direction = $direction == "left" ? 90 : 270;

    $dsql = new dsql($dbo);

    $RenrenCrypt = new RenrenCrypt();
    $id = $RenrenCrypt->php_decrypt(base64_decode($picpath));

    $attachment = $dsql->SetQuery("SELECT * FROM `#@__attachment` WHERE `id` = " . $id);
    $results = $dsql->dsqlOper($attachment, "results");

    if (!$results) die('{"state":"ERROR","info":' . json_encode("数据不存在！") . '}');  //数据不存在
    $picpath = $results[0]['path'];

    $wxupimg = false;
    if(strstr($picpath, 'wxupimg')){
      $pathModel = array("large");
      $wxupimg = true;
    }

    $picpath = explode("large/", $picpath);
    $picpath = $picpath[1];

    //循环操作相关文件
    foreach ($pathModel as $key => $value) {

        //读取远程文件
        set_time_limit(24 * 60 * 60);

        $folder = $action == "thumb" ? "thumb" : "atlas";
        if($wxupimg){
          $mod = 'siteConfig';
          $folder = 'wxupimg';
        }
        // if ($cfg_ftpType == 2) {
        //     $fileUrl = $site_fileUrl . "/" . $mod . "_" . $folder . "_" . $value . "_" . str_replace('/', '_', $picpath);
        // } else {
            $fileUrl = $site_fileUrl . "/" . $mod . "/" . $folder . "/" . $value . "/" . $picpath;
        // }
        //var_dump($fileUrl);
        $local_fileUrl = HUONIAOROOT . $custom_folder . "/" . $mod . "/" . $folder . "/" . $value . "/" . $picpath;
        $fileContent = @file_get_contents($fileUrl);

        //保存至本地
        if ($fileContent) {
            createFile($local_fileUrl);
            PutFile($local_fileUrl, $fileContent);
        }

        if (file_exists($local_fileUrl)) {
            //对本地文件进行旋转操作
            rotateAtlas($local_fileUrl, $degrees = $direction);

            //上传到远程服务器

            //普通FTP模式
            if ($cfg_ftpType == 0 && $cfg_ftpState == 1) {
                $ftpConfig = array();
                if ($mod != "siteConfig" && $customFtp == 1 && $custom_ftpState == 1) {
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

                $huoniao_ftp = new ftp($ftpConfig);
                $huoniao_ftp->connect();
                if ($huoniao_ftp->connectid) {
                    $huoniao_ftp->upload($local_fileUrl, $cfg_ftpDir . "/" . $mod . "/" . $folder . "/" . $value . "/" . $picpath);
                } else {
                    $error = 'FTP连接失败，请检查配置信息！';
                }

                //阿里云OSS
            } elseif ($cfg_ftpType == 1) {
                $OSSConfig = array();
                if ($mod != "siteConfig" && $customFtp == 1) {
                    $OSSConfig = array(
                        "bucketName" => $custom_OSSBucket,
                        "endpoint" => $custom_EndPoint,
                        "accessKey" => $custom_OSSKeyID,
                        "accessSecret" => $custom_OSSKeySecret
                    );
                }

                $aliyunOSS = new aliyunOSS($OSSConfig);
                $aliyunOSS->upload($mod . "/" . $folder . "/" . $value . "/" . $picpath, $local_fileUrl);

                $ossError = $aliyunOSS->error();
                if ($ossError) {
                    $error = $ossError;
                }
            } elseif ($cfg_ftpType == 2) {
                $autoload = true;
                $accessKey = $custom_QINIUAccessKey;
                $secretKey = $custom_QINIUSecretKey;
                // 构建鉴权对象
                $auth = new Auth($accessKey, $secretKey);
                // 要上传的空间
                $bucket = $custom_QINIUbucket;
                // 上传到七牛后保存的文件名
                $key = $mod . "/" . $folder . "/" . $value . "/" . $picpath;
                // 生成上传 Token
                $token = $auth->uploadToken($bucket,$key);
                // 要上传文件的本地路径
                $filePath = $local_fileUrl;
                // 初始化 UploadManager 对象并进行文件的上传。
                $uploadMgr = new UploadManager();
                // 调用 UploadManager 的 putFile 方法进行文件的上传。
                list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
                if ($err !== null) {
                    $error = $err;
                }
            }

        } else {
            $error = "要操作的文件不存在";
        }

    }

    //输出错误信息
    if (!empty($error)) {
        echo '{"state":"ERROR","info":"' . $error . '"}';
        die;
    } else {
        echo '{"state":"SUCCESS","info":' . json_encode("操作成功！") . '}';
        die;
    }


//上传文件
} else {

    //图集
    if ($type == "atlas") {
        //上传配置
        $config = array(
            "savePath" => ".." . $cfg_uploadDir . "/" . $mod . "/" . $type, //保存路径
            "allowFiles" => explode("|", $cfg_atlasType), //文件允许格式
            "maxSize" => $cfg_atlasSize, //文件大小限制，单位KB
            "fileType" => $type  //要操作的图片类型
        );

        //附件
    } elseif ($type == "file") {
        //上传配置
        $config = array(
            "savePath" => ".." . $cfg_uploadDir . "/" . $mod . "/" . $type, //保存路径
            "allowFiles" => explode("|", $cfg_softType), //文件允许格式
            "maxSize" => $cfg_softSize, //文件大小限制，单位KB
            "fileType" => $type  //要操作的图片类型
        );

        //照片
    } elseif ($type == "photo") {
        //上传配置
        $config = array(
            "savePath" => ".." . $cfg_uploadDir . "/" . $mod . "/" . $type, //保存路径
            "allowFiles" => explode("|", $cfg_photoType), //文件允许格式
            "maxSize" => $cfg_photoSize, //文件大小限制，单位KB
            "fileType" => $type  //要操作的图片类型
        );

        //品牌LOGO
    } elseif ($type == "brandLogo") {
        //上传配置
        $config = array(
            "savePath" => ".." . $cfg_uploadDir . "/" . $mod . "/" . $type, //保存路径
            "allowFiles" => explode("|", $cfg_thumbType), //文件允许格式
            "maxSize" => $cfg_thumbSize, //文件大小限制，单位KB
            "fileType" => $type  //要操作的图片类型
        );

    //favicon图标
    } elseif ($type == "favicon"){

      //上传配置
      $config = array(
          "savePath" => '', //保存路径
          "allowFiles" => array('ico'), //文件允许格式
          "maxSize" => $cfg_thumbSize, //文件大小限制，单位KB
          "fileType" => $type  //要操作的图片类型
      );

        //缩略图
    } else {
        if ($filetype == "flash") {
            //flash配置
            $config = array(
                "savePath" => ".." . $cfg_uploadDir . "/" . $mod . "/" . $filetype, //保存路径
                "allowFiles" => array("swf"), //文件允许格式
                "maxSize" => $cfg_thumbSize, //文件大小限制，单位KB
                "fileType" => $filetype  //要操作的类型
            );
        } elseif ($filetype == "audio") {
            //音频配置
            $config = array(
                "savePath" => ".." . $cfg_uploadDir . "/" . $mod . "/" . $filetype, //保存路径
                "allowFiles" => explode("|", $cfg_audioType), //文件允许格式
                "maxSize" => $cfg_audioSize, //文件大小限制，单位KB
                "fileType" => $filetype  //要操作的类型
            );

        } elseif ($filetype == "video") {

            //视频配置
            $config = array(
                "savePath" => ".." . $cfg_uploadDir . "/" . $mod . "/" . $filetype, //保存路径
                "allowFiles" => explode("|", $cfg_videoType), //文件允许格式
                "maxSize" => $cfg_videoSize, //文件大小限制，单位KB
                "fileType" => $filetype  //要操作的类型
            );
        } elseif ($filetype == "file") {
            //附件配置
            $config = array(
                "savePath" => ".." . $cfg_uploadDir . "/" . $mod . "/" . $filetype, //保存路径
                "allowFiles" => explode("|", $cfg_softType), //文件允许格式
                "maxSize" => $cfg_softSize, //文件大小限制，单位KB
                "fileType" => $filetype  //要操作的图片类型
            );
        } else {
            //缩略图配置
            $config = array(
                "savePath" => ".." . $cfg_uploadDir . "/" . $mod . "/" . $type, //保存路径
                "allowFiles" => explode("|", $cfg_thumbType), //文件允许格式
                "maxSize" => $cfg_thumbSize, //文件大小限制，单位KB
                "fileType" => $type  //要操作的图片类型
            );
        }
    }

    $error = "";

    //生成上传实例对象并完成上传
    $up = new upload("Filedata", $config, $base64);
    $info = $up->getFileInfo();
    $url = explode($cfg_uploadDir, $info["url"]);

    if($type == 'favicon'){
      $url = explode(HUONIAOROOT, $info["url"]);
    }

    $picWidth = $picHeight = 0;

    $remoteType = '';

    //判断状态
    if ($info['state'] == "SUCCESS") {

        $fileClass_ = explode(".", $info["originalName"]);
        $fileClass_ = $fileClass_[count($fileClass_) - 1];
        $fileClass = chkType($fileClass_);

        //APP端IM即时通讯附件
        //需要记录附件发送相关信息，发送人，接收人，发送时间，附件地址
        //在原接口地址基础上，新增：&chat=1&from=发送人ID&to=接收人ID&date=1531906792
        if($chat){

          // $date = (int)substr($date, 0, 10);

          //音频转码
          if($fileClass == 'audio' && $fileClass_ == 'amr'){
            $cfg_ftpType = -1;

            //七牛转码
            $sql = $dsql->SetQuery("SELECT * FROM `#@__app_audio_video_config` LIMIT 0, 1");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $data = $ret[0];
                $access_key    = $data['access_key'];
                $secret_key    = $data['secret_key'];
                $bucket        = $data['bucket'];
                $pipeline      = $data['pipeline'];
                $domain        = $data['domain'];
                $audio_quality = $data['audio_quality'];
            }else{
                $error = "请先配置APP音视频处理参数！";
            }

            if(!$error){
              $auth = new Auth($access_key, $secret_key);
              $newName = create_sess_id();
              $savekey = Qiniu\base64_urlSafeEncode($bucket.':'.$newName.'.mp3');

              //设置转码参数
              $fops = "avthumb/mp3/ab/{$audio_quality}k/ar/44100/acodec/libmp3lame";
              $fops = $fops.'|saveas/'.$savekey;

              $policy = array(
                  'persistentOps' => $fops,
                  'persistentPipeline' => $pipeline
              );

              //指定上传转码命令
              $uptoken = $auth->uploadToken($bucket, null, 3600, $policy);

              $uploadMgr = new UploadManager();
              list($ret, $err) = $uploadMgr->putFile($uptoken, $url[1], HUONIAOROOT . $cfg_uploadDir . $url[1]);

              if ($err !== null) {
                  $error = "转码失败！" . $err;
              }else{

                  //此时七牛云中同一段音频文件有amr和MP3两个格式的两个文件同时存在
                  $bucketMgr = new BucketManager($auth);

                  //为节省空间,删除amr格式文件
                  $bucketMgr->delete($bucket, $url[1]);

                  //删除服务器上的amr文件
                  @unlink(HUONIAOROOT . $cfg_uploadDir . $url[1]);

                  //由于使用七牛的地址直接播放会有延迟的问题，所以这里多做了一步从七牛再下载到本地的操作
                  // $file->OpenUrl("http://{$domain}/{$newName}.mp3"); # 远程文件地址
                  // $file->SaveToBin(HUONIAOROOT . $dir . $newName . ".mp3"); # 保存路径及文件名
                  // $file->Close(); # 释放资源

                  //验证文件是否下载成功
                  // if(!file_exists(HUONIAOROOT . $dir . $newName . ".mp3")){
                  // 	echo json_encode(array("state" => 200, "info" => "云端下载失败！"));
                  // }else{
                  $url[1] = "//{$domain}/{$newName}.mp3";
                  // }
              }
            }
          }

          if(!$error){
            $sql = $dsql->SetQuery("INSERT INTO `#@__".$mod."_chat` (`from`, `to`, `msg`, `date`) VALUE ('$from', '$to', '', '$date')");
            $aid = 0;
            $sql = $dsql->SetQuery("SELECT `id`, `date` FROM `#@__".$mod."_chat` WHERE `pid` = 0 AND ( (`from` = $from && `to` = $to) || (`from` = $to && `to` = $from) ) ");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $aid = $ret[0]['id'];
                if(strlen($ret[0]['date']) == 10){
                    $now = GetMkTime(time());
                    $sql = $dsql->SetQuery("UPDATE `#@__".$mod."_chat` SET `date` = '$now' WHERE `id` = $aid");
                    $dsql->dsqlOper($sql, "update");
                }
            }
            $sql = $dsql->SetQuery("INSERT INTO `#@__".$mod."_chat` (`from`, `to`, `msg`, `date`, `pid`) VALUE ('$from', '$to', '', '$date', '$aid')");
            $lastid = $dsql->dsqlOper($sql, "lastid");
          }
        }

        // 全景模块上传压缩包时只能上传到本地
        if(($mod == "quanjing" && $filetype == "zip") || $type == "favicon"){
            $cfg_ftpType = -1;
        }

        //生成缩略图
        if ($type == "thumb") {
            if ($mod == "special1" || $mod == "website1") {
                if ($filetype == "image") {
                    $small = $up->smallImg($cfg_thumbSmallWidth, $cfg_thumbSmallHeight, "small", $cfg_quality);
                }
            } else {
                $small = $up->smallImg($cfg_thumbSmallWidth, $cfg_thumbSmallHeight, "small", $cfg_quality);
                $middle = $up->smallImg($cfg_thumbMiddleWidth, $cfg_thumbMiddleHeight, "middle", $cfg_quality);
                $large = $up->smallImg($cfg_thumbLargeWidth, $cfg_thumbLargeHeight, "large", $cfg_quality);
                $o_large = $up->smallImg($cfg_thumbLargeWidth, $cfg_thumbLargeHeight, "o_large", $cfg_quality);
            }

            //生成水印图片
            if ($thumbMarkState == 1 && $mod != "special" && $mod != "website" && $mod != "siteConfig") {
                if (empty($filetype) || $filetype == "image") {
                    $waterMark = $up->waterMark($markConfig);
                }
            }

        } else if ($type == "atlas" && $mod != "siteConfig" && $utype != "adv") {
            $small = $up->smallImg($cfg_atlasSmallWidth, $cfg_atlasSmallHeight, "small", $cfg_quality);

            //生成水印图片
            if ($atlasMarkState == 1) {
                $waterMark = $up->waterMark($markConfig);
            }

        } else if ($type == "photo") {
            $small = $up->smallImg($cfg_photoSmallWidth, $cfg_photoSmallHeight, "small", $cfg_quality);
            $middle = $up->smallImg($cfg_photoMiddleWidth, $cfg_photoMiddleHeight, "middle", $cfg_quality);
            $large = $up->smallImg($cfg_photoLargeWidth, $cfg_photoLargeHeight, "large", $cfg_quality);

        } elseif ($type == "brandLogo") {
            global $custom_brandSmallWidth;
            global $custom_brandSmallHeight;
            global $custom_brandMiddleWidth;
            global $custom_brandMiddleHeight;
            global $custom_brandLargeWidth;
            global $custom_brandLargeHeight;
            $small = $up->smallImg($custom_brandSmallWidth, $custom_brandSmallHeight, "small", $cfg_quality);
            $middle = $up->smallImg($custom_brandMiddleWidth, $custom_brandMiddleHeight, "middle", $cfg_quality);
            $large = $up->smallImg($custom_brandLargeWidth, $custom_brandLargeHeight, "large", $cfg_quality);

        }


        if ($fileClass == "image") {
            $imgSize = @getimagesize($info['url']);
            $picWidth = (int)$imgSize[0];
            $picHeight = (int)$imgSize[1];
        }

        //上传到远程服务器

        //普通FTP模式
        if ($cfg_ftpType == 0 && $cfg_ftpState == 1) {
            $ftpConfig = array();
            if ($mod != "siteConfig" && $customFtp == 1 && $custom_ftpState == 1) {
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

            $huoniao_ftp = new ftp($ftpConfig);
            $huoniao_ftp->connect();
            if ($huoniao_ftp->connectid) {

                //专题和自助建站不需要大图
                if ($mod != "special1" && $mod != "website1") {
                    $huoniao_ftp->upload(HUONIAOROOT . $cfg_uploadDir . $url[1], $cfg_ftpDir . $url[1]);

                    if ($type != "config") {
                        $smallFile = str_replace("large", "small", $url[1]);
                        $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $smallFile;
                        $huoniao_ftp->upload($fileRootUrl, $cfg_ftpDir . $smallFile);
                    }
                    if ($type == "thumb") {
                        $middleFile = str_replace("large", "middle", $url[1]);
                        $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $middleFile;
                        $huoniao_ftp->upload($fileRootUrl, $cfg_ftpDir . $middleFile);

                        $o_largeFile = str_replace("large", "o_large", $url[1]);
                        $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $o_largeFile;
                        $huoniao_ftp->upload($fileRootUrl, $cfg_ftpDir . $o_largeFile);
                    }
                    if ($type == "photo" || $type == "brandLogo") {
                        $middleFile = str_replace("large", "middle", $url[1]);
                        $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $middleFile;
                        $huoniao_ftp->upload($fileRootUrl, $cfg_ftpDir . $middleFile);
                    }
                } else {
                    if ($type == "thumb" && ($filetype == "" || $filetype == "image")) {
                        //保留原图
                        if ($o == 'true') {
                            $huoniao_ftp->upload(HUONIAOROOT . $cfg_uploadDir . $url[1], $cfg_ftpDir . $url[1]);

                            $smallFile = str_replace("large", "small", $url[1]);
                            $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $smallFile;
                            $huoniao_ftp->upload($fileRootUrl, $cfg_ftpDir . $smallFile);

                            //只留小图
                        } else {
                            $huoniao_ftp->upload(HUONIAOROOT . $cfg_uploadDir . $url[1], $cfg_ftpDir . $url[1]);

                            $smallFile = str_replace("large", "small", $url[1]);
                            $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $smallFile;
                            $huoniao_ftp->upload($fileRootUrl, $cfg_ftpDir . $smallFile);
                        }
                    } else {
                        $huoniao_ftp->upload(HUONIAOROOT . $cfg_uploadDir . $url[1], $cfg_ftpDir . $url[1]);
                    }
                }


            } else {
                $error = 'FTP连接失败，请检查配置信息！';
            }

            //阿里云OSS
        } elseif ($cfg_ftpType == 1) {
            $remoteType = 'aliyun';
            $OSSConfig = array();
            if ($mod != "siteConfig") {
                $OSSConfig = array(
                    "bucketName" => $custom_OSSBucket,
                    "endpoint" => $custom_EndPoint,
                    "accessKey" => $custom_OSSKeyID,
                    "accessSecret" => $custom_OSSKeySecret
                );
            }

            $aliyunOSS = new aliyunOSS($OSSConfig);

            //专题和自助建站不需要大图
            if ($mod != "special1" && $mod != "website1") {
                $aliyunOSS->upload($url[1], HUONIAOROOT . $cfg_uploadDir . $url[1]);

                if ($type != "config") {
                    $smallFile = str_replace("large", "small", $url[1]);
                    $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $smallFile;
                    $aliyunOSS->upload($smallFile, $fileRootUrl);
                }
                if ($type == "thumb") {
                    $middleFile = str_replace("large", "middle", $url[1]);
                    $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $middleFile;
                    $aliyunOSS->upload($middleFile, $fileRootUrl);

                    $o_largeFile = str_replace("large", "o_large", $url[1]);
                    $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $o_largeFile;
                    $aliyunOSS->upload($o_largeFile, $fileRootUrl);
                }
                if ($type == "photo" || $type == "brandLogo") {
                    $middleFile = str_replace("large", "middle", $url[1]);
                    $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $middleFile;
                    $aliyunOSS->upload($middleFile, $fileRootUrl);
                }
            } else {
                if ($type == "thumb" && ($filetype == "" || $filetype == "image")) {
                    //保留原图
                    if ($o == 'true') {
                        $aliyunOSS->upload($url[1], HUONIAOROOT . $cfg_uploadDir . $url[1]);

                        $smallFile = str_replace("large", "small", $url[1]);
                        $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $smallFile;
                        $aliyunOSS->upload($smallFile, $fileRootUrl);

                        //只留小图
                    } else {
                        $smallFile = str_replace("large", "small", $url[1]);
                        $aliyunOSS->upload($smallFile, HUONIAOROOT . $cfg_uploadDir . $url[1]);
                    }
                } else {
                    $aliyunOSS->upload($url[1], HUONIAOROOT . $cfg_uploadDir . $url[1]);
                }
            }

            $ossError = $aliyunOSS->error();

            if ($ossError) {
                $error = $ossError;
            }
        } elseif ($cfg_ftpType == 2) {
            $remoteType = 'qiniu';
            $autoload = true;
            $accessKey = $cfg_QINIUAccessKey;
            $secretKey = $cfg_QINIUSecretKey;
            $bucket = $cfg_QINIUbucket;

            if($mod != 'siteConfig'){
              $accessKey = $custom_QINIUAccessKey;
              $secretKey = $custom_QINIUSecretKey;
              $bucket = $custom_QINIUbucket;
            }

            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);

            //视频转码
            // if($filetype == 'video' && $fileClass_ != 'mp4'){
            //   $url1 = str_replace('.'.$fileClass_, '.mp4', $url[1]);
            //   $newName = substr(str_replace('/', '_', $url1), 1);
            //   $savekey = Qiniu\base64_urlSafeEncode($bucket.':'.$url1);
            //
            //   //设置转码参数
            //   $fops = "avthumb/mp4";
            //   $fops = $fops.'|saveas/'.$savekey;
            //
            //   $policy = array(
            //       'persistentOps' => $fops,
            //       'persistentPipeline' => $bucket
            //   );
            //   $token = $auth->uploadToken($bucket, null, 3600, $policy);
            // }else{
            // }
            $token = $auth->uploadToken($bucket, null);

            // 生成上传 Token

            $uploadMgr = new UploadManager();

            //专题和自助建站不需要大图
            if ($mod != "special1" && $mod != "website1") {
                list($ret, $err) = $uploadMgr->putFile($token, $url[1], HUONIAOROOT . $cfg_uploadDir . $url[1]);

                if($filetype == 'video' && $fileClass_ != 'mp4'){
                  // $url[1] = $url1;
                }

                if ($type != "config") {
                    $smallFile = str_replace("large", "small", $url[1]);
                    $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $smallFile;
                    if (file_exists($fileRootUrl)) {
                        list($ret, $err) = $uploadMgr->putFile($token, $smallFile, $fileRootUrl);
                    }
                }
                if ($type == "thumb" && ($filetype == "" || $filetype == "image")) {
                    $middleFile = str_replace("large", "middle", $url[1]);
                    $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $middleFile;
                    list($ret, $err) = $uploadMgr->putFile($token, $middleFile, $fileRootUrl);

                    $o_largeFile = str_replace("large", "o_large", $url[1]);
                    $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $o_largeFile;
                    list($ret, $err) = $uploadMgr->putFile($token, $o_largeFile, $fileRootUrl);
                }
                if ($type == "photo" || $type == "brandLogo") {
                    $middleFile = str_replace("large", "middle", $url[1]);
                    $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $middleFile;
                    list($ret, $err) = $uploadMgr->putFile($token, $middleFile, $fileRootUrl);
                }
            } else {
                if ($type == "thumb" && ($filetype == "" || $filetype == "image")) {
                    //保留原图
                    if ($o == 'true') {
                        list($ret, $err) = $uploadMgr->putFile($token, $url[1], HUONIAOROOT . $cfg_uploadDir . $url[1]);

                        $smallFile = str_replace("large", "small", $url[1]);
                        $fileRootUrl = HUONIAOROOT . $cfg_uploadDir . $smallFile;
                        list($ret, $err) = $uploadMgr->putFile($token, $smallFile, $fileRootUrl);

                        //只留小图
                    } else {
                        $smallFile = str_replace("large", "small", $url[1]);
                        list($ret, $err) = $uploadMgr->putFile($token, $smallFile, HUONIAOROOT . $cfg_uploadDir . $url[1]);
                    }
                } else {
                    list($ret, $err) = $uploadMgr->putFile($token, $url[1], HUONIAOROOT . $cfg_uploadDir . $url[1]);
                }
            }
            if ($err !== null) {
                $error = $err;
            }
        }
    } else {
        $error = $info["state"];
    }

    $fid = "";
    $obj = $_REQUEST['obj'];

    if ($info["state"] == "SUCCESS" && $error == "") {
        $autoload = false;
        $dsql = new dsql($dbo);
        $userLogin = new userLogin($dbo);
        $userid = $userLogin->getMemberID();

        $originalName = $info["originalName"];
        if (strlen($originalName) > 50) {
//            $originalName = substr($originalName, strlen($originalName) - 50);
            $originalName = cn_substrR($originalName, 50);
        }

        $attachment = $dsql->SetQuery("INSERT INTO `#@__attachment` (`userid`, `filename`, `filetype`, `filesize`, `path`, `width`, `height`, `aid`, `pubdate`) VALUES ('$userid', '" . $originalName . "', '" . $fileClass . "', '" . $info["size"] . "', '" . $url[1] . "', '$picWidth', '$picHeight', '" . $aid . "', '" . GetMkTime(time()) . "')");
        $aid = $dsql->dsqlOper($attachment, "lastid");
        if (is_numeric($aid)) {
            $RenrenCrypt = new RenrenCrypt();
            $fid = base64_encode($RenrenCrypt->php_encrypt($aid));

            //APP端即时聊天附件
            if(is_numeric($lastid) && $chat){

              $filePath_ = '/include/attachment.php?f='.$fid;
              $msg = '';
              if($fileClass == 'image'){
                $msg = '<img src="'.$filePath_.'" />';
              }elseif($fileClass == 'audio'){
                $msg = '<audio data-duration="'.$duration.'"><source src="'.$filePath_.'"></audio>';
              }elseif($fileClass == 'video'){
                $msg = '<video><source src="'.$filePath_.'" type="video/mp4"></video>';
              }

              if($msg){
                $sql = $dsql->SetQuery("UPDATE `#@__".$mod."_chat` SET `msg` = '$msg' WHERE `id` = $lastid");
                $dsql->dsqlOper($sql, "update");
              }else{
                $sql = $dsql->SetQuery("DELETE FROM `#@__".$mod."_chat` WHERE `id` = $lastid");
                $dsql->dsqlOper($sql, "update");
              }

            }

        } else {
            $error = "数据写入失败！";
        }
    }

    $info["state"] = $error != "" ? $error : $info["state"];

    $poster = '';
    $turl = getFilePath($fid);
    if($fileClass == 'video' && $remoteType){
      if($remoteType == 'aliyun'){
        $poster = $turl.'?x-oss-process=video/snapshot,t_0,f_jpg,w_0,h_0,m_fast';
      }elseif($remoteType == 'qiniu'){
        $poster = $turl.'?vframe/jpg/offset/1/rotate/auto';
      }
    }

    if ($obj != "") {
        echo "<script language='javascript'>";
        if (empty($fid) || !empty($error)) {
            echo "alert('" . $info["state"] . "');location.href = '/include/upfile.inc.php?mod=$mod&type=$type&obj=$obj&filetype=$filetype';";
        } else {
            echo "parent.uploadSuccess('" . $obj . "', '" . $fid . "', '" . str_replace(".", "", $info["type"]) . "', '" . $turl . "', '" . $poster . "');";
        }
        echo "</script>";
    } else {
        echo '{"url":"' . $fid . '","turl": "' . $turl . '","fileType":"' . $info["type"] . '","fileSize":"' . $info["size"] . '","original":"' . $info["originalName"] . '","name":"' . $info["name"] . '","state":"' . $info["state"] . '","type":"' . $type . '","poster":"' . $poster . '", "width":"'.$picWidth.'", "height":"'.$picHeight.'"}';
    }
    die;
}

/**
 * 修改一个图片 让其翻转指定度数
 *
 * @param string $filename 文件名（包括文件路径）
 * @param float $degrees 旋转度数
 * @return boolean
 */
function rotateAtlas($filename, $degrees = 90)
{
    //读取图片
    $data = @getimagesize($filename);
    if ($data == false) return false;
    //读取旧图片
    switch ($data[2]) {
        case 1:
            $src_f = imagecreatefromgif($filename);
            break;
        case 2:
            $src_f = imagecreatefromjpeg($filename);
            break;
        case 3:
            $src_f = imagecreatefrompng($filename);
            break;
    }
    if ($src_f == "") return false;
    $rotate = @imagerotate($src_f, $degrees, 0);
    if (!imagejpeg($rotate, $filename, 100)) return false;
    @imagedestroy($rotate);
    return true;
}

//判断文件类型
function chkType($f = NULL)
{
    if (!empty($f)) {
        $f = strtolower($f);
        global $cfg_softType;
        global $cfg_thumbType;
        $flashType = "swf";
        global $cfg_audioType;
        global $cfg_videoType;

        $softType_ = explode("|", $cfg_softType);
        $thumbType_ = explode("|", $cfg_thumbType);
        $flashType_ = explode("|", $flashType);
        $audioType_ = explode("|", $cfg_audioType);
        $videoType_ = explode("|", $cfg_videoType);

        if (in_array($f, $softType_)) return "file";
        if (in_array($f, $thumbType_)) return "image";
        if (in_array($f, $flashType_)) return "flash";
        if (in_array($f, $audioType_)) return "audio";
        if (in_array($f, $videoType_)) return "video";
    } else {
        return "file";
    }
}
