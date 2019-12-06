<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
	* @file crons.class.php
	* @brief 新版本系统定时任务，基于宝塔linux面板
	* @author cgchenxin
	* @version v1.0
	* @date 2019-11-21
 */


class crons {
	private $param;  //参数
    public static $langData;
    /**
     * 构造函数
     *
     * @param string $action 动作名
     */
    public function __construct($param = array()){
        $this->param = $param;
        global $langData;
        self::$langData = $langData;
    }

	public function test(){
		return 11;
	}

	/**
		* @brief 定时推送客源,以及当天失败的客户补推
		*
		* @return 
	 */
	public function pushTimer(){
		global $cfg_basehost;
		global $cfg_secureAccess;
		file_get_contents($cfg_secureAccess.$cfg_basehost."/include/ajax.php?service=member&action=push");
		return true;
	}

	/**
		* @brief 绑定关系监视器 处理会员收藏的客户中绑定关系过期的用户
		*
		* @return 
	 */
	public function relationMonitor(){
		global $dsql;
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
	}


	/**
		* @brief 定时解绑AX模式号码
		*
		* @return 
	 */
	public function unbindRelation(){
		global $dsql;
		$sql = $dsql->SetQuery("Select phone,callee from #@__relationphone r inner join #@__phonebind p on r.phone=p.relationphone where r.state=1 and model=0 and now()>r.expire");
		$hasExpired = $dsql->dsqlOper($sql, 'results');
		if(!empty($hasExpired['state'])){
			exit(0);
		}
		foreach ($hasExpired as $key => $item){
			$handler = new handlers('hwVisualPhoneAX','unbind');
			$handler->getHandle(array('callee'=>$item['callee'],'relationPhone'=>$item['phone']));
		}
	}
	
	/**
		* @brief 检查当天通话失败的用户，第二天补推一个
		*
		* @return 
	 */
	public function checkCallFailed(){
		global $dsql;
		$files = fopen(HUONIAOROOT."/logs/checkCallFailed.log","a");
		fwrite($files,"[" . date('Y-m-d H:i:s') . "]The check Call Failed Monitor Has Done!\n");
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
	}

	/**
		* @brief 定时清理数据
		*
		* @return 
	 */
	public function dataClean(){
		global $dsql;
		$sql = $dsql->SetQuery("delete from #@__phonebind where now()>expire");
		$dsql->dsqlOper($sql,'update');
		$sql = $dsql->SetQuery("delete from #@__agentpushrecord where now()>expire");
		$dsql->dsqlOper($sql,'update');
		$sql = $dsql->SetQuery('delete from #@__callrecord where now()>`date`');
		$dsql->dsqlOper($sql,'update');
		$sql = $dsql->SetQuery('delete from #@__agentpushlog where now()>`date`');
		$dsql->dsqlOper($sql,'update');
	}
	
	public function updatePublishTime(){
		$time = strtotime(date('Y-m-d')) - 24*60*60;
		global $dsql;
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
	}	

}
