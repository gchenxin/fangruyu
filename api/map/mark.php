<?php
/**
 * 地图标注
 *
 * @version        $Id: mark.php 2014-1-10 上午00:11:12 $
 * @package        HuoNiao.Map
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
require_once(dirname(__FILE__).'/../../include/common.inc.php');

if($mod == "") die('Module Err!');

$config = HUONIAOINC."/config/".$mod.".inc.php";
if(!is_file($config)) die('Config Error!');
include_once($config);

global $cfg_map;
global $cfg_basedir;
global $custom_map;

if($custom_map != 0){
	$cfg_map = $custom_map;
}

$lnglat = $_GET['lnglat'];
if(!empty($lnglat)){
	$lnglat = explode(",", $lnglat);
	$lng = $lnglat[0] == "undefined" ? 0 : (float)$lnglat[0];
	$lat = $lnglat[1] == "undefined" ? 0 : (float)$lnglat[1];
}else{
	$lat = 0;
	$lng = 0;
}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>地图标注</title>
<link rel="stylesheet" type="text/css" href="<?php $cfg_basedir ?>/static/css/ui/map/mark.css?v=<?php echo $cfg_staticVersion;?>" />
<?php
$jsApiFile = "";
//google
if($cfg_map == 1){
	global $cfg_map_google;
	$jsApiFile = '//maps.googleapis.com/maps/api/js?key='.$cfg_map_google.'&libraries=places';
//baidu
}elseif($cfg_map == 2){
	global $cfg_map_baidu;
	$jsApiFile = '//api.map.baidu.com/api?v=2.0&ak='.$cfg_map_baidu;
//tencent
}elseif($cfg_map == 3){
	global $cfg_map_qq;
	$jsApiFile = '//map.qq.com/api/js?key='.$cfg_map_qq;
//高德
}elseif($cfg_map == 4){
	global $cfg_map_amap;
	$jsApiFile = '//webapi.amap.com/maps?v=1.3&key='.$cfg_map_amap;
}
?>
<script type="text/javascript" src="<?php echo $jsApiFile; ?>"></script>
<!-- <script>document.domain = '<?php echo $cfg_basehost;?>';</script> -->
</head>

<body>
<div class="search"><input type="text" id="keyword" placeholder="搜索关键字" /><input type="button" id="search" value="搜索" /></div>
<div id="map" class="warp"></div>
<input type="hidden" name="city" id="city" value="<?php echo $city; ?>" />
<input type="hidden" name="addr" id="addr" value="<?php echo $address; ?>" />
<input type="hidden" name="lng" id="lng" value="<?php echo $lng; ?>" />
<input type="hidden" name="lat" id="lat" value="<?php echo $lat; ?>" />
<script src="<?php $cfg_basedir ?>/static/js/core/jquery-1.8.3.min.js"></script>
<?php
$dituType = "";
//google
if($cfg_map == 1){
	$dituType = 'google';
//baidu
}elseif($cfg_map == 2){
	$dituType = 'baidu';
//tencent
}elseif($cfg_map == 3){
	$dituType = 'qq';
//高德
}elseif($cfg_map == 4){
	$dituType = 'amap';
}
?>
<script src="<?php $cfg_basedir ?>/static/js/ui/map/<?php echo $dituType; ?>/mark.js?v=<?php echo $cfg_staticVersion;?>"></script>
</body>
</html>
