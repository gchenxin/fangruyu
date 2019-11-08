<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 更新发布时间超过一天的房源信息
 *
 *
 * @version        $Id: house_updatePublishTime.php 2019-11-07 09:56:33$
 * @package        gchenxin
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

$time = strtotime(date('Y-m-d')) - 24*60*60;
$config = [
	"house_loupan"=>'pubdate',
	'house_sale'	=>	'pubdate',
	'house_zu'	=>	'pubdate',
	'house_sp'	=>	'pubdate',
	'house_xzl'	=>	'pubdate',
	'house_cf'	=>	'pubdate'
];

foreach ($config as $key=>$value){
	$sql = $dsql->SetQuery("update #@__{$key} set {$value}=".time()." where {$value}<{$time}");
	$dsql->dsqlOper($sql,'update');
}

