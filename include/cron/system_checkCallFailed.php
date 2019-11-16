<?php   
$files = fopen(HUONIAOROOT."/logs/checkCallFailed.log","a");
fwrite($files,"[" . date('Y-m-d H:i:s') . "]The check Call Failed Monitor Has Done!\n");

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
//查询当天推送列表
$date = date('Y-m-d');
$expire = $date . " 23:59:59";
$nextDate = date('Y-m-d', strtotime($date." +1 day"));
$callFailedSql = <<<EOT
	select distinct p.uid,p.zjid,p.realphone,p.visualphone,m.overide from #@__agentpushrecord p
	inner join (
		select caller,callee,relationPhone from #@__callrecord where date='{$date}' GROUP BY caller,callee,relationPhone
	) T on p.visualPhone=T.relationPhone and (p.realphone=T.callee or p.realphone=T.caller)
	inner join #@__member m on p.zjid=m.id
	where not EXISTS(
		select * from huoniao_callrecord c where c.date='{$date}' and c.callfailCode=0 and c.callunanswerCode=0 
		and p.visualphone=c.relationPhone and (p.realphone=c.callee or p.realphone=c.caller)
	) and p.expire='{$expire}'
EOT;
$sql = $dsql->SetQuery($callFailedSql);
$result = $dsql->dsqlOper($sql,'results');
fwrite($files, json_encode($result) ."\n");
if($result || !isset($result['state'])){
	foreach($result as $value){
		$overide = json_decode($value['overide'],true);
		if(!$overide){
			$overide = '[{"times":1,"startDate":"' . $nextDate . ' 00:00:00","during":1,"unit":"天","expire":"' . $nextDate . ' 23:59:59"}]';
		}else{
			$isModified = false;
			foreach($overide as $key=>$item){
				if($item['expire'] < date('Y-m-d H:i:s')){
					$overide[$key]['times'] = 1;
					$overide[$key]['startDate'] = $nextDate . ' 00:00:00';
					$overide[$key]['during'] = 1;
					$overide[$key]['unit'] = '天';
					$overide[$key]['expire'] = $nextDate . ' 23:59:59';
					$isModified = true;
					break;
				}
			}
			if(!$isModified){
				$overide[] = [
					'times' => 1,
					'startDate' => $nextDate . ' 00:00:00',
					'during' => 1,
					'unit' => '天',
					'expire' => $nextDate . " 23:59:59"
				];
			}
			$overide = json_encode($overide);
		}
		$updateSql = $dsql->SetQuery("update #@__member set overide='{$overide}' where id={$value['zjid']}");
		$dsql->dsqlOper($updateSql, 'update');
	}
}
fclose($files);
