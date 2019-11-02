<?php 

/**
	* @file pushtimer.class.php
	* @brief 定时推送客源
	* @author chenxin
	* @version v1.0
	* @date 2019-10-18
 */


/**
	错误编码：
	400：模板不存在
	401：模板已停用
	402：未开启网页消息
 */
class pushtimer {
	public $param;
	public static $langData;
	const MSG_TEMPID = 56;

	public function __construct($param = array()){
		$this->param = $param;
		global $langData;
		self::$langData = $langData;
	}

	public function push(){
		//查询是否定时推送模板信息
		$messageTemplateInfo = $this->getMessageTemplateInfo(self::MSG_TEMPID);
		if(!is_array($messageTemplateInfo)){
			return ["state"=>$messageTemplateInfo,"模板配置错误！"];
		}
		//查询经纪人信息
		global $dsql;
		$zjSql = $dsql->setQuery("select m.id,m.level,m.overide,ml.privilege from #@__house_zjuser zj inner join #@__member m on zj.userid=m.id inner join #@__huoniao_member_level ml on m.level=ml.id where m.level>0 and mtype=2");
		$zjList = $dsql->dsqlOper($zjSql,'results');
		//查询可以推送的用户数量
		$userSql = $dsql->SetQuery("select id,phone,nickname from #@__member m where m.mtype=1 and not exists(select * from #@__house_zjuser where userid=m.id) and not exists(select * from #@__member_collect mc where mc.aid=m.id and module='push_timer')");
		$userList = $dsql->dsqlOper($userSql,"results");
		shuffle($userList);
		foreach($zjList as $key => $val){
			//计算推送的个数
			$levelPushTimes = (unserialize($val['privilege']))['push_timer'];
			//叠加次数
			$overideArr = json_decode($val['overide'],true);
			$overideTimes = 0;
			foreach($overideArr as $v){
				if(date("Y-m-d H:i:s",time()) <= $v['expire']){
					$overideTimes += $v['times'];
				}
			};
			$pushTimes = $levelPushTimes + $overideTimes;
			for($i = 0; $i < $pushTimes; $i++){
				if(!$userList)	break;
				$userInfo = array_shift($userList);
				$expire = date("Y-m-d") . " 19:30:00";
				$visualPhone = getVisualPhone();
				$insertSql = $dsql->SetQuery("insert into #@__agentpushrecord(uid,zjid,expire,realphone,visualphone) values({$userInfo['id']},{$val['id']},'{$expire}','{$userInfo['phone']}','{$visualPhone}')");
				$dsql->dsqlOper($insertSql,"update");
			}
		}
		$this->notify();
		return;

	}

	public function getMessageTemplateInfo($msgId){
		global $dsql;
		$sql = $dsql->SetQuery("select id,title,site_state,site_title,site_body,state from huoniao_site_notify where id=".$msgId);
		$msgInfo = $dsql->dsqlOper($sql,"results");
		if(!$msgInfo)
			return 400;
		if(!$msgInfo[0]['state']){
			return 401;
		}
		if(!$msgInfo[0]['site_state']){
			return 402;
		}
		return $msgInfo[0];
	}

}
