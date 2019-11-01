<?php
/**
 * 普通上传处理插件
 *
 * @version        $Id: upfile.class.php 2013-11-17 上午16:14:36 $
 * @package        HuoNiao.class
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
require_once('./common.inc.php');

header("Content-Type: text/html; charset=utf-8");

$mod      = htmlspecialchars(RemoveXSS($_REQUEST['mod']));
$type     = htmlspecialchars(RemoveXSS($_REQUEST['type']));
$obj      = htmlspecialchars(RemoveXSS($_REQUEST['obj']));
$filetype = htmlspecialchars(RemoveXSS($_REQUEST['filetype']));
?>
<script language="javascript">
<!--
function mysub(){
	document.getElementById("ztupform").style.display="none";
	document.getElementById("esave").style.display="block";
	document.getElementById('form').submit();
}

function uploaddo() {
	var f = document.getElementById('form');
	if (f.Filedata.value == '') return;
	mysub();
}
-->
</script>
<style type="text/css">
<!--
body {margin:0px; padding:0px;}
#esave {display:none; line-height:25px; font-size:12px;}
#esave img {display:inline-block; vertical-align:middle;}
.uploadbg {width:100px; height:25px; display:inline-block; cursor:pointer; overflow:hidden; background:url(../static/images/ui/swfupload/uploadbutton.png) no-repeat left top;}
#Filedata {filter:alpha(opacity=00); -moz-opacity:.0; opacity:0.0; cursor:pointer; width: 100px; overflow: hidden;}
-->
</style>
<div id="esave"><img src="../static/images/ui/loading.gif" />&nbsp;正在上传...</div>
<div id="ztupform">
  <form method="post" id="form" action="upload.inc.php" enctype="multipart/form-data">
    <input type="hidden" name="mod" value="<?php echo $mod;?>" />
    <input type="hidden" name="type" value="<?php echo $type;?>" />
    <input type="hidden" name="obj" value="<?php echo $obj;?>" />
    <input type="hidden" name="filetype" value="<?php echo $filetype;?>" />
    <span class="uploadbg"><input type="file" name="Filedata" id="Filedata" onChange="uploaddo();"></span>
  </form>
</div>
