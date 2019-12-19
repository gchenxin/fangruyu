<?php
/**
* @file hwVisualPhone.class.php
* @brief 华为云虚拟号码类库
* @author chenxin
* @version v1.0
* @date 2019-10-19
 */

class hwVisualPhone{
	public $appkey;
	public $appsecret;
	public $url;
	public $header;
	public $context_options;
	public $param;

	public function __construct($param = []){
		$this->appkey = "c4a07c0607a5GwjY15eiy11i6aRx";
		$this->appsecret = "IrGCEv0oYer2nP7BGmL1m1t6e3c2";
		$this->url = "https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/caas/relationnumber/partners/v1.0";
		$this->url = $this->url;
		
		$this->headers = [
		    'Accept: application/json',
		    'Content-Type: application/json;charset=UTF-8',
		    'Authorization: WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
		    'X-WSSE: ' . $this->buildWsseHeader($this->appkey, $this->appsecret)
		];

		$this->context_options = [
			'http' => [
				'header' => $this->headers,
				'ignore_errors' => true // 获取错误码,方便调测
			],
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false
			] // 为防止因HTTPS证书认证失败造成API调用失败,需要先忽略证书信任问题
		];

		$this->param = $param;
	}

	public function bind(){
		date_default_timezone_set('PRC');
		if(empty($this->param['caller']) || empty($this->param['callee']) || empty($this->param['relationPhone']))
			return;
		$caller = $this->param['caller'];
		$callee = $this->param['callee'];
		$relationPhone = $this->param['relationPhone'];
		$duration = $this->param['duration'] ?: 0;	
		//$areaCode = '0755'; // 需要绑定的X号码对应的城市码
		 // $callDirection = 0; // 允许呼叫的方向
		 // $duration = 86400; // 绑定关系保持时间,单位为秒。到期后会被系统自动解除绑定关系
		$recordFlag = 'true'; // 是否需要针对该绑定关系产生的所有通话录音
		 // $recordHintTone = 'recordHintTone.wav'; // 设置录音提示音
		 // $maxDuration = 60; // 设置允许单次通话进行的最长时间,单位为分钟。通话时间从接通被叫的时刻开始计算
		 // $lastMinVoice = 'lastMinVoice.wav'; // 设置通话剩余最后一分钟时的提示音
		$privateSms = 'false'; // 设置该绑定关系是否支持短信功能
		
		 // $callerHintTone = 'callerHintTone.wav'; // 设置A拨打X号码时的通话前等待音
		 // $calleeHintTone = 'calleeHintTone.wav'; // 设置B拨打X号码时的通话前等待音
		 // $preVoice = [
		 //     'callerHintTone' => $callerHintTone,
		 //     'calleeHintTone' => $calleeHintTone
		 // ];
		

		$data = json_encode([
		    'relationNum' => '+86'.$relationPhone,
		    //'areaCode' => $areaCode,
		    'callerNum' => '+86'.$caller,
		    'calleeNum' => '+86'.$callee,
		    // 'callDirection' => $callDirection,
		    'duration' => $duration,
		    'recordFlag' => $recordFlag,
		    // 'recordHintTone' => $recordHintTone,
		    // 'maxDuration' => $maxDuration,
		    // 'lastMinVoice' => $lastMinVoice,
		    'privateSms' => $privateSms,
		    // 'preVoice' => $preVoice
		]);
		
		$this->context_options['http']['method'] = "POST";
		$this->context_options['http']['content'] = $data;

		try {
			$response = file_get_contents($this->url, false, stream_context_create($this->context_options)); // 发送请求
			$response = json_decode($response,true);
			global $dsql;
			$expire = $duration != 0 ? date('Y-m-d H:i:s',time()+$duration) : 0;
			if($response['resultcode'] == 0){
				$insertSql = $dsql->SetQuery("insert into #@__phonebind(caller,callee,relationphone,subscriptionId,expire,state) values('{$caller}','{$callee}','{$relationPhone}','{$response['subscriptionId']}','{$expire}',1)");
				$dsql->dsqlOper($insertSql,'update');
				return $response['subscriptionId'];
			}
			$insertSql = $dsql->SetQuery("insert into #@__phonebind(caller,callee,relationphone,subscriptionId,expire,state,reason) values('{$caller}','{$callee}','{$relationPhone}','{$response['subscriptionId']}','{$expire}',0,'{$response['resultdesc']}')");
			$dsql->dsqlOper($insertSql,'update');
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return ;
	}

	public function callStop($sessionId){
		$sessionId = $sessionId ?: $this->param['sessionId'];
		$url = "https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/httpsessions/callStop/v2.0";
		$data = json_encode([
			"sessionid" => $sessionId,
			"signal" => 'call_stop'
		]);
		$this->context_options['http']['method'] = "POST";
		$this->context_options['http']['content'] = $data;
		try{
			$response = file_get_contents($url,false,stream_context_create($this->context_options));
			$files = fopen("/www/wwwroot/dengyunlong_26dj_com/logs.txt","a");
			fwrite($files, json_encode($this->context_options) . "\n");
			fwrite($files, "URL:". $url . "\n");
			fwrite($files,$response);
			$response = json_decode($response, true);	
			fclose($files);
		}catch (Exception $e){
			echo $e->getMessage();
		}
		return ;
	}
	
	public function buildWsseHeader($appKey, $appSecret) {
		date_default_timezone_set("UTC");
		$Created = date('Y-m-d\TH:i:s\Z'); //Created
		$nonce = uniqid(); //Nonce
		$base64 = base64_encode(hash('sha256', ($nonce . $Created . $this->appsecret), TRUE)); //PasswordDigest

		return sprintf("UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"", $this->appkey, $base64, $nonce, $Created);
	}


	public function getRelationPhone(){
		if(empty($this->param['subscriptionId']) && empty($this->param['relationPhone']))
			return ;
		$subscriptionId = $this->param['subscriptionId'];
		$relationPhone = $this->param['relationPhone'];
		$paramArr = [];
		if($subscriptionId)	$paramArr['subscriptionId'] = $subscriptionId;
		if($relationPhone) $paramArr['relationNum'] = '+86' . $relationPhone;
		$data = http_build_query($paramArr);
		$fullUrl = $this->url . '?' . $data;
		$this->context_options['http']['method'] = "GET";

		try {
			$response = file_get_contents($fullUrl, false, stream_context_create($this->context_options)); // 发送请求
			$response = json_decode($response, true);
			if($response['resultcode'] == 0){
				return $response['relationNumList'];
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return ;
	}

	public function unbind($sid = ''){
		if(empty($this->param['subscriptionId']) && empty($this->param['relationPhone']) && !$sid)
			return false;
		$subscriptionId = $this->param['subscriptionId'] ?: $sid;
		$relationPhone = $this->param['relationPhone'];
		// 请求URL参数
		$paramArr = [];
		if($subscriptionId)	$paramArr['subscriptionId'] = $subscriptionId;
		if($relationPhone) $paramArr['relationNum'] = '+86' . $relationPhone;
		$data = http_build_query($paramArr);
		// 完整请求地址
		$fullUrl = $this->url . '?' . $data;
		$this->context_options['http']['method'] = "DELETE";
		try {
			$response = file_get_contents($fullUrl, false, stream_context_create($this->context_options)); // 发送请求
			$response = json_decode($response, true);
			if($response['resultcode'] == 0){
				global $dsql;
				if($subscriptionId){
					$delsql = $dsql->SetQuery("delete from #@__phonebind where subscriptionId='{$subscriptionId}'");	
				}else{
					$delsql = $dsql->SetQuery("delete from #@__phonebind where relationphone='{$relationPhone}'");
				}
				$dsql->dsqlOper($delsql,'update');
				return true;
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return false;
	}

	public function modifyBindInfo(){
		if(empty($this->param['subscriptionId']) || empty($this->param['caller']) || empty($this->param['callee']))
			return;
		$subscriptionId = $this->param['subscriptionId'];
		$caller = $this->param['caller'];
		$callee = $this->param['callee'];
		$data = [
			'subscriptionId' => $subscriptionId,
		];
		if($caller)	$data['callerNum'] = '+86'.$caller;
		if($callee) $data['calleeNum'] = '+86'.$callee;
		$this->context_options['http']['method'] = "PUT";
		$this->context_options['http']['content'] = json_encode($data);
		try {
			$response = file_get_contents($this->url, false, stream_context_create($this->context_options)); // 发送请求
			$response = json_decode($response, true);
			if($response['resultcode'] == 0){
				global $dsql;
				$sql = $dsql->SetQuery("update #@__phonebind set caller='{$caller}',callee='{$callee}' where subcriptionId='{$subcriptionId}'");
				$dsql->dsqlOper($sql,'update');
				return true;
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return ;
	}
	
	/**
		* @brief 定期清理已经过期的数据
		*
		* @return 
	 */
	public function cleanHasExpireData(){
		global $dsql;
		$sql = $dsql->SetQuery("delete from #@__phonebind where now()>expire");
		$dsql->dsqlOper($sql,'update');
		$sql = $dsql->SetQuery("delete from #@__agentpushrecord where now()>expire");
		$dsql->dsqlOper($sql,'update');
		$sql = $dsql->SetQuery('delete from #@__callrecord where now()>`date`');
		$dsql->dsqlOper($sql,'update');
	}
	
	/**
		* @brief 呼叫状态事件通知
		*
		* @param $jsonBody
		*
		* @return 
	 */
	public function onCallState(){
		$jsonArr = json_decode($this->param['jsonBody'], true); //将通知消息解析为关联数组
		$eventType = $jsonArr['eventType']; //通知事件类型
		if (strcasecmp($eventType, 'fee') == 0 || !array_key_exists('statusInfo', $jsonArr)) {
			return;
		}

		$statusInfo = $jsonArr['statusInfo']; //呼叫状态事件信息
		if (strcasecmp($eventType, 'callin') == 0) {
			//呼入
			if (array_key_exists('sessionId', $statusInfo)) {
				//$this->callStop($statusInfo['sessionId']);
			}
			return;
		}
		if (strcasecmp($eventType, 'callout') == 0) {
			//呼出
			if (array_key_exists('sessionId', $statusInfo)) {
				//$this->callStop($statusInfo['sessionId']);
			}
			return;
		}
		if (strcasecmp($eventType, 'answer') == 0) {
			if (array_key_exists('sessionId', $statusInfo)) {
				global $dsql;
				$sql = $dsql->SetQuery("select caller,callee from #@__phonebind where subscriptionId='{$statusInfo['subscriptionId']}'");
				$info = $dsql->dsqlOper($sql, 'results');
				if($info && !isset($info['state'])){
					//不限制客户回拨经纪人次数
					if(strstr($statusInfo['called'], $info[0]['caller']))	return;
					$isLimit = $this->judgeCalledTimesLimit($info[0]['caller'],$info[0]['callee']);
					if(!$isLimit){
						$this->callStop($statusInfo['sessionId']);
					}
				}
			}
			return;
		}
	}

	public function onFeeEvent(){
		$jsonArr = json_decode($this->param['jsonBody'], true); //将通知消息解析为关联数组
		$eventType = $jsonArr['eventType']; //通知事件类型

		date_default_timezone_set("PRC");
		if (strcasecmp($eventType, 'fee') != 0 || !array_key_exists('feeLst', $jsonArr)) {
			return;
		}
		$feeLst = $jsonArr['feeLst'];
		if (sizeof($feeLst)) {
			foreach ($feeLst as $loop){
				if (array_key_exists('subscriptionId', $loop)) {
					//正常绑定
					$callUnaswRsn = $callFaild = 0;
					if($loop['fwdUnaswRsn']){
						$callUnaswRsn = $loop['fwdUnaswRsn'];
					}
					if($loop['ulFailReason']){
						$callFaild = $loop['ulFailReason'];
					}
					//记录次数
					global $dsql;
					$query = $dsql->SetQuery("select caller,callee,subscriptionId from #@__phonebind where subscriptionId='{$loop['subscriptionId']}'");
					$callInfo = $dsql->dsqlOper($query, "results");
					if(!$callInfo || isset($callInfo['state']))	continue;
					if(!$callInfo[0]['caller'] && $callInfo[0]['callee']){ //AX模式
						$caller = substr($loop['callerNum'],3);
						$callee = $callInfo[0]['callee'];
						$callDirection = 1;
						//查询改号码是否是本网站已收录用户，若没有，加入到访客表中
						$checkSql = $dsql->SetQuery("select phone from #@__member where phone='{$caller}' union ALL select phone from #@__visitor where phone='{$caller}'");
						$isExists = $dsql->dsqlOper($checkSql,'results');
						if(!$isExists && empty($isExists['state'])){
							$area = getAddridByPhone($caller);
							$zjInfo = $dsql->SetQuery("select id,phone,level from #@__member where phone='{$callee}'");
							$zjInfo = $dsql->dsqlOper($zjInfo,'results');
							if(!$zjInfo || !empty($zjInfo['state']))	continue;
							switch($zjInfo[0]['level']){
							case 1:$usertags=4;break;	//写字楼会员
							case 4:$usertags=1;break;//住房会员
							case 5:$usertags=2;break;//商铺会员
							case 6:$usertags=8;break;//厂房仓库会员
							}
							$insertSql = $dsql->SetQuery("insert into #@__visitor(phone,usertags,scantime,area) values('$caller','{$usertags}','" . time() . "','{$area}')");
							$dsql->dsqlOper($insertSql,"update");
						}
					}elseif(strstr($loop['callerNum'], $callInfo[0]['caller'])){
						$caller = $callInfo[0]['caller'];
						$callee = $callInfo[0]['callee'];
						$callDirection = 0;
					}else{
						$caller = $callInfo[0]['callee'];
						$callee = $callInfo[0]['caller'];
						$callDirection = 1;
					}
					$bindNum = substr($loop['bindNum'],3);
					$callDuration = strtotime($loop['callEndTime']) - strtotime($loop['fwdAnswerTime']);
					$insertSql = $dsql->SetQuery("insert into #@__callrecord values('{$caller}','{$callee}','{$bindNum}','" . date("Y-m-d") . "',{$callDuration},{$callDirection},{$callFaild},{$callUnaswRsn},'" . date('Y-m-d H:i:s') . "')");
					$dsql->dsqlOper($insertSql, 'update');
				}
			}
		} 
	}

	public function judgeCalledTimesLimit($caller = '',$callee = ''){
		$caller = $caller ?: $this->param['caller'];
		$callee = $callee ?: $this->param['callee'];
		global $dsql;
		//查询当天已经拨打的次数
		$hasCalledSql = $dsql->SetQuery("select * from #@__callrecord where caller in ('{$caller}','{$callee}') and callee in ('{$caller}','{$callee}') and `date`='" . date("Y-m-d") . "' and calldirection=0 and callfailCode=0 and callunanswerCode=0");
		$hasCalledTimes = $dsql->dsqlOper($hasCalledSql, 'results');
		if(!isset($hasCalledTimes['state'])){
			if(count($hasCalledTimes) >= 2) return false;
			else return true;
		}
	}

}
