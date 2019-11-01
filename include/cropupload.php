<?php
/**
 * 自定义头像
 *
 * @version        $Id: cropupload.php 2015-7-16 下午20:07:41 $
 * @package        HuoNiao.Include
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
require_once('./common.inc.php');

if(empty($coordW) || empty($coordH) || empty($width) || empty($height)) callBack(200, '参数传递失败！', $callback);

$mod = $mod == "dating" ? "dating" : "member";

global $dsql;
global $userLogin;

$uid = $userLogin->getMemberID();
if($uid == -1) callBack(200, '登录超时，请重新登录！', $callback);

$RenrenCrypt = new RenrenCrypt();

if(strstr($picid, ".jpg") || strstr($picid, ".jpeg") || strstr($picid, ".gif") || strstr($picid, ".png") || strstr($picid, ".bmp")){
	$picpath = $picid;
}else{
	$id = (int)$RenrenCrypt->php_decrypt(base64_decode($picid));

	$attachment = $dsql->SetQuery("SELECT * FROM `#@__attachment` WHERE `id` = ".$id);
	$results = $dsql->dsqlOper($attachment, "results");

	if(!$results) callBack(200, '图片不存在或已删除，请重试！', $callback);
	$picpath = $results[0]['path'];
}
$picpath = explode("large/", $picpath);
$picpath = $picpath[1];

//声明全局变量
global $site_fileUrl;
global $cfg_uploadDir;
global $cfg_ftpDir;
global $cfg_ftpType;
global $cfg_ftpState;
global $cfg_OSSBucket;
global $cfg_EndPoint;
global $cfg_OSSKeyID;
global $cfg_OSSKeySecret;
global $cfg_QINIUAccessKey;
global $cfg_QINIUSecretKey;
global $cfg_QINIUbucket;

global $cfg_photoSmallWidth;
global $cfg_photoSmallHeight;
global $cfg_photoMiddleWidth;
global $cfg_photoMiddleHeight;
global $cfg_photoLargeWidth;
global $cfg_photoLargeHeight;

$custom_folder = $cfg_uploadDir;

//读取远程文件
set_time_limit(24*60*60);

$folder = "/siteConfig/card/large/";
$local_fileUrl = HUONIAOROOT.$custom_folder.$folder.$picpath;
$fileContent = @file_get_contents(getRealFilePath($picid));

//保存至本地
if($fileContent && !file_exists($local_fileUrl)){
	createFile($local_fileUrl);
	PutFile($local_fileUrl, $fileContent);
}

if(file_exists($local_fileUrl)){
	//生成大头像
	$imageInfo = getimagesize($local_fileUrl);
	$srcW = $imageInfo[0];
	$srcH = $imageInfo[1];

	if($imageInfo[2] == 1) {
		$img   = imagecreatefromgif($local_fileUrl);
		$img_t = ".gif";
	} elseif($imageInfo[2] == 2) {
		$img   = imagecreatefromjpeg($local_fileUrl);
		$img_t = ".jpg";
	} elseif($imageInfo[2] == 3) {
		$img   = imagecreatefrompng($local_fileUrl);
		$img_t = ".png";
	} else {
		$img   = "";
		$img_t = "";
	}

	//根据传回的参数缩放图片
	$operaArr = array(
		array(
			"folder" => "large",
			"width" => $cfg_photoLargeWidth,
			"height" => $cfg_photoLargeHeight
		),
		array(
			"folder" => "middle",
			"width" => $cfg_photoMiddleWidth,
			"height" => $cfg_photoMiddleHeight
		),
		array(
			"folder" => "small",
			"width" => $cfg_photoSmallWidth,
			"height" => $cfg_photoSmallHeight
		)
	);

	//计算图片差值
	$rideW = 1;
	$rideH = 1;
	if($srcW > $width){
		$rideW = $srcW/$width;
		$coordX = $rideW*$coordX;
	}
	if($srcH > $height){
		$rideH = $srcH/$height;
		$coordY = $rideH*$coordY;
	}

	$timeFolder = date( "Y" )."/".date( "m" )."/".date("d");
	$newName = time() . rand( 1 , 10000 ) . $img_t;

	foreach ($operaArr as $key => $val) {
		if(function_exists("imagecreatetruecolor")) {
			$newImg = imagecreatetruecolor($val['width'], $val['height']);
			$background = imagecolorallocate($newImg, 255, 255, 255);
			imagefill($newImg,0,0,$background);
			ImageCopyResampled($newImg, $img, 0, 0, $coordX, $coordY, $val['width'], $val['height'], $coordW * $rideW, $coordH * $rideH);
		}else{
			$newImg = imagecreatetruecolor($val['width'], $val['height']);
			ImageCopyResized($newImg, $img, 0, 0, $coordX, $coordY, $val['width'], $val['height'], $coordW * $rideW, $coordH * $rideH);
		}

		$pathStr = HUONIAOROOT.$custom_folder."/siteConfig/photo/".$val['folder']."/".$timeFolder;
		createDir($pathStr);
		$newPath = $pathStr."/".$newName;
		if (file_exists($newPath)) @unlink($newPath);
		ImageJpeg($newImg, $newPath, 100);
		ImageDestroy($newImg);
	}

	ImageDestroy($img);


	//上传到远程服务器

	//普通FTP模式
	if($cfg_ftpType == 0 && $cfg_ftpState == 1){
		include_once(HUONIAOINC."/class/ftp.class.php");
		$huoniao_ftp = new ftp();
		$huoniao_ftp->connect();
		if($huoniao_ftp->connectid) {
			foreach ($operaArr as $key => $val) {
				$nfolder = "/siteConfig/photo/".$val['folder']."/".$timeFolder."/".$newName;
				$huoniao_ftp->upload(HUONIAOROOT.$custom_folder.$nfolder, $cfg_ftpDir.$nfolder);
			}
			$huoniao_ftp->ftp_delete($custom_folder.$folder.$picpath);
		}else{
			$error = 'FTP连接失败，请检查配置信息！';
		}

	//阿里云OSS
	}elseif($cfg_ftpType == 1){
		include_once(HUONIAOINC."/class/aliyunOSS.class.php");
        $OSSConfig = array(
            "bucketName" => $cfg_OSSBucket,
            "endpoint" => $cfg_EndPoint,
            "accessKey" => $cfg_OSSKeyID,
            "accessSecret" => $cfg_OSSKeySecret
        );
        $aliyunOSS = new aliyunOSS($OSSConfig);
		foreach ($operaArr as $key => $val) {
			$nfolder = "/siteConfig/photo/".$val['folder']."/".$timeFolder."/".$newName;
			$aliyunOSS->upload($nfolder, HUONIAOROOT.$custom_folder.$nfolder);
			$ossError = $aliyunOSS->error();
            //echo $nfolder;
            //echo HUONIAOROOT.$custom_folder.$nfolder;
			if($ossError){
				$error = $ossError;
			}
			$aliyunOSS->delete($folder.$picpath);
		}

	}elseif($cfg_ftpType==2){
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
        $autoload = false;

        $accessKey = $cfg_QINIUAccessKey;
        $secretKey = $cfg_QINIUSecretKey;
        $auth = new \Qiniu\Auth($accessKey, $secretKey);
        $bucket = $cfg_QINIUbucket;
        $uploadMgr = new \Qiniu\Storage\UploadManager();
        $bucketMgr = new \Qiniu\Storage\BucketManager($auth);
        foreach ($operaArr as $key => $val) {
            $nfolder = "/siteConfig/photo/".$val['folder']."/".$timeFolder."/".$newName;
            $key=substr(str_replace('/','_',$nfolder),1);
            $token = $auth->uploadToken($bucket,$nfolder);
            list($ret, $err)=$uploadMgr->putFile($token,$nfolder, HUONIAOROOT.$custom_folder.$nfolder);
            if ($err !== null) {
                $error = $err;
            }
            // $bucketMgr->delete($bucket, $nfolder);
        }

    }

	unlinkFile($local_fileUrl);
	$archives = $dsql->SetQuery("DELETE FROM `#@__attachment` WHERE `id` = ".$id);
	$dsql->dsqlOper($archives, "update");

}else{
	$error = "图片操作失败，请检查服务器权限！";
}

//输出错误信息
if(!empty($error)){
	callBack(200, $error, $callback);
}else{

	$archives = $dsql->SetQuery("INSERT INTO `#@__attachment` (`userid`, `filename`, `filetype`, `filesize`, `path`, `aid`, `pubdate`) VALUES ('$uid', '$newName', 'image', 0, '"."/siteConfig/photo/large/".$timeFolder."/".$newName."', 0, '".GetMkTime(time())."')");
	$aid = $dsql->dsqlOper($archives, "lastid");
	if(is_numeric($aid)){
		$fid = base64_encode($RenrenCrypt->php_encrypt($aid));

		//删除之前的头像文件
		// $handler = true;
		// $uinfo = $userLogin->getMemberInfo();
		// $oldPhoto = $uinfo['photoSource'];
		// if(!empty($oldPhoto)){
		// 	delPicFile($oldPhoto, "delPhoto", "siteConfig");
		// }

		// $archives = $dsql->SetQuery("UPDATE `#@__member` SET `photo` = '/siteConfig/photo/large/".$timeFolder."/".$newName."' WHERE `id` = '$uid'");
        if($mod == "dating"){
          $archives = $dsql->SetQuery("UPDATE `#@__dating_member` SET `photo` = '$fid' WHERE `userid` = '$uid'");
        }else{
          $archives = $dsql->SetQuery("UPDATE `#@__member` SET `photo` = '$fid' WHERE `id` = '$uid'");
        }
		$dsql->dsqlOper($archives, "update");

		callBack(100, '操作成功！', $callback);
	}else{
		callBack(200, "数据写入失败！", $callback);
	}
}

function callBack($state, $info, $callback){

	$return = array("state" => $state, "info" => $info);

	//输出到浏览器
	if($callback){
		echo $callback."(".json_encode($return).")";
	}else{
		echo json_encode($return);
	}
	die;
}
