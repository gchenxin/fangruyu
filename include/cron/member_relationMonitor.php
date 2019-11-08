<?php   
$files = fopen(HUONIAOROOT."/logs/monitorLogs.log","a");
fwrite($files,"[" . date('Y-m-d H:i:s') . "]The Monitor Has Done!\n");
fclose($files);
//if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 定时更新已过期会员为普通会员
 *
 * 如果会员超过系统配置时间没有活动，则自动更新会离线状态
 *
 * @version        $Id: member_cleanExpired.php 2017-07-28 下午13:57:10 $
 * @package        HuoNiao.cron
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

$sql = $dsql->SetQuery("select m.id,m.phone,m.expired from #@__house_zjuser zj inner join #@__member m on m.id=zj.userid where m.level>0 and " . time() . "<=m.expired");
$zjList = $dsql->dsqlOper($sql,"results");
if($zjList && !isset($zjList['state'])){
	foreach($zjList as $value){
		//查询收藏客户绑定已过期的列表
		$checkSql = $dsql->SetQuery("select mc.id,mc.aid,visitorphone,m.phone from #@__member_collect mc left join #@__member m on m.id=mc.aid inner join #@__phonebind pb on (mc.visualphone=pb.relationphone and ((pb.caller='{$value['phone']}' and ((mc.aid=-1 and mc.visitorphone=pb.callee) or (mc.aid!=-1 and m.phone=pb.callee))) or (pb.callee='{$value['phone']}' and ((mc.aid=-1 and mc.visitorphone=pb.caller) or (mc.aid!=-1 and m.phone=pb.caller))))) where mc.module='push_timer' and mc.userid={$value['id']} and pb.state=1 and now()>pb.expire");
		$hasExpireList = $dsql->dsqlOper($checkSql,"results");
		if(!isset($hasExpireList['state'])){
			$handle = new handlers('hwVisualPhone','bind');
			foreach($hasExpireList as $val){
				$userPhone = $val['aid'] == -1 ? $val['visitorphone'] : $val['phone'];
				$visualPhone = getVisualPhone($value['phone'],$userPhone);
				if(!$visualPhone)	continue;
				//重新绑定
				$duration = intval($value['expired']) - time();
				$result = $handle->getHandle(array('relationPhone'=>$visualPhone,'caller'=>$value['phone'],'callee'=>$userPhone,'duration'=>$duration));
				if($result){
					//更新虚拟号码
					$updateSql = $dsql->SetQuery("update #@__member_collect set visualphone='{$visualPhone}' where id={$val['id']}");
					$dsql->dsqlOper($updateSql,"update");
				}
			}
		}
	}

}
