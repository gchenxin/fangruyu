<?php require_once(dirname(__FILE__).'/common.inc.php');?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>视频预览</title>
<style>
* {padding:0; margin:0;}
html, body, #video {width: 100%; height: 100%;}
</style>
</head>

<body>
<div id="video"></div>

<script type="text/javascript" src="../static/js/ui/ckplayer/ckplayer.js" charset="utf-8"></script>
<script type="text/javascript">
	var videoObject = {
		container: '#video',//“#”代表容器的ID，“.”或“”代表容器的class
		variable: 'player',//该属性必需设置，值等于下面的new chplayer()的对象
		// autoplay: true,//自动播放
		video:'<?php echo getRealFilePath($_GET['f']);?>'//视频地址
	};
	var player=new ckplayer(videoObject);
</script>
</body>
</html>
