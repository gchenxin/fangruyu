<?php   
/**
 * 定时推送客源信息
 *
 *
 * @version        $Id: member_cleanOnline.php 2015-11-16 下午14:02:22 $
 * @package        HuoNiao.cron
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
global $cfg_basehost;
file_get_contents('http://'.$cfg_basehost.'/include/ajax.php?service=member&action=push');
