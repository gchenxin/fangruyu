<?php   
$files = fopen(HUONIAOROOT."/logs/unbind.log","a");
fwrite($files,"[" . date('Y-m-d H:i:s') . "]The unbind Relatione Monitor Has Done!\n");
fclose($files);
//if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 定时检查取消AX模式的绑定
 *
 *
 * @version        $Id: member_cleanExpired.php 2017-07-28 下午13:57:10 $
 * @package        HuoNiao.cron
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

$sql = $dsql->SetQuery("Select phone,callee from #@__relationphone r inner join #@__phonebind p on r.phone=p.relationphone where r.state=1 and model=0 and now()>r.expire");
$hasExpired = $dsql->dsqlOper($sql, 'results');
if(!empty($hasExpired['state'])){
	fwrite($files,"查询出错,请检查！\n");
	fclose($files);
	exit(0);
}
foreach ($hasExpired as $key => $item){
	$handler = new handlers('hwVisualPhoneAX','unbind');
	$handler->getHandle(array('callee'=>$item['callee'],'relationPhone'=>$item['phone']));
}
fclose($files);
