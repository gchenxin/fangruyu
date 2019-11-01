<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 外卖定时更新订单状态
 *
 * 半小时未支付的订单，更新状态为：6，取消支付
 *
 *
 * @version        $Id: waimai_updateOrderState.php 2016-12-09 下午17:18:16 $
 * @package        HuoNiao.cron
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

$time = time() - 1800;

$sql = $dsql->SetQuery("UPDATE `#@__waimai_order` SET `state` = 6, `failed` = '半小时内未支付，系统自动取消！' WHERE `state` = 0 AND `pubdate` < $time");
$dsql->dsqlOper($sql, "update");