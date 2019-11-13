<?php

$domain = ["test.com","www.test.com"];
$reverse = "fangruyu.net";

if(in_array($_SERVER['HTTP_HOST'], $domain)){
	$_SERVER['HTTP_HOST'] = $reverse;
}

if(in_array($_SERVER['SERVER_NAME'], $domain)){
	$_SERVER['SERVER_NAME'] = $reverse;
}
