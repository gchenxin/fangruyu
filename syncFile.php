<?php
$file = dirname(__FILE__) . $_GET['file'];

$file = str_replace('adminFolder', 'km_admin', $file);

if(file_exists($file)){
  header("Content-type:application/octet-stream");
  $filename = basename($file);
  header("Content-Disposition:attachment;filename = ".$filename);
  header("Accept-ranges:bytes");
  header("Accept-length:".filesize($file));
  readfile($file);
}else{
  echo "<script>alert('文件不存在')</script>";
}
