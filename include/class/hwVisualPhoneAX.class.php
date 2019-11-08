<?php
/**
* @file hwVisualPhone.class.php
* @brief 华为云虚拟号码类库 AX模式
* @author chenxin
* @version v1.0
* @date 2019-10-19
 */

class hwVisualPhoneAX{
	public $appkey;
	public $appsecret;
	public $url;
	public $header;
	public $context_options;
	public $param;

	public function __construct($param = []){
		$this->appkey = "F7V94jeti3iGcQkGnIkk1e2R99R5";
		$this->appsecret = "5g9HR1gfP5yH6l89U4dc12Wx5y0f";
		$this->url = "https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/provision/caas/privatenumber/v1.0";
		$this->duration = 600;
		
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
		if(empty($this->param['callee']) || empty($this->param['relationPhone']))
			return;
		$callee = $this->param['callee'];
		$privateNum = $this->param['relationPhone'];
		// $privateNumType = 'mobile-virtual'; //固定为mobile-virtual
		// $areaCode = '0755'; //需要绑定的X号码对应的城市码
		// $recordFlag = 'false'; //是否需要针对该绑定关系产生的所有通话录音
		// $recordHintTone = 'recordHintTone.wav'; //设置录音提示音
		$calleeNumDisplay = '0'; // 设置非A用户呼叫X时,A接到呼叫时的主显号码
		// $privateSms = 'true'; //设置该绑定关系是否支持短信功能
	
		// $callerHintTone = 'callerHintTone.wav'; //设置A拨打X号码时的通话前等待音
		// $calleeHintTone = 'calleeHintTone.wav'; //设置非A用户拨打X号码时的通话前等待音
		// $preVoice = [
		//     'callerHintTone' => $callerHintTone,
		//     'calleeHintTone' => $calleeHintTone
		// ];		

		
		$data = json_encode([
			'origNum' => '+86' . $callee,
			'privateNum' => '+86' . $privateNum,
			// 'privateNumType' => $privateNumType,
			// 'areaCode' => $areaCode,
			// 'recordFlag' => $recordFlag,
			// 'recordHintTone' => $recordHintTone,
			'calleeNumDisplay' => $calleeNumDisplay,
			// 'privateSms' => $privateSms,
			// 'preVoice' => $preVoice
		]);
		
		$this->context_options['http']['method'] = "POST";
		$this->context_options['http']['content'] = $data;

		try {
			$response = file_get_contents($this->url, false, stream_context_create($this->context_options)); // 发送请求
			$response = json_decode($response,true);
			global $dsql;
			$expire = date('Y-m-d H:i:s',time()+$this->duration);
			if($response['resultcode'] == 0){
				$insertSql = $dsql->SetQuery("insert into #@__phonebind(caller,callee,relationphone,subscriptionId,expire,state) values('','{$callee}','{$privateNum}','{$response['subscriptionId']}','{$expire}',1)");
				$dsql->dsqlOper($insertSql,'update');
				$updateSql = $dsql->SetQuery("update #@__relationphone set expire='{$expire}' where phone={$privateNum} and model=0");
				$dsql->dsqlOper($updateSql,'update');
				return $response['subscriptionId'];
			}
			$insertSql = $dsql->SetQuery("insert into #@__phonebind(caller,callee,relationphone,subscriptionId,expire,state,reason) values('','{$callee}','{$privateNum}','{$response['subscriptionId']}','{$expire}',0,'{$response['resultdesc']}')");
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


	public function unbind(){
		if(empty($this->param['callee']) && empty($this->param['relationPhone']))
			return false;
		$callee = $this->param['callee'];
		$relationPhone = $this->param['relationPhone'];
		// 请求URL参数
		$paramArr = [];
		if($callee)	$paramArr['origNum'] = '+86' . $callee;
		if($relationPhone) $paramArr['privateNum'] = '+86' . $relationPhone;
		$data = http_build_query($paramArr);
		// 完整请求地址
		$fullUrl = $this->url . '?' . $data;
		$this->context_options['http']['method'] = "DELETE";
		try {
			$response = file_get_contents($fullUrl, false, stream_context_create($this->context_options)); // 发送请求
			$response = json_decode($response, true);
			if($response['resultcode'] == 0){
				global $dsql;
				$delsql = $dsql->SetQuery("delete from #@__phonebind where relationphone='{$relationPhone}' and callee='{$callee}'");
				$dsql->dsqlOper($delsql,'update');
				return true;
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return false;
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

		if (strcasecmp($eventType, 'fee') != 0 || !array_key_exists('feeLst', $jsonArr)) {
			return;
		}

		$feeLst = $jsonArr['feeLst'];
		if (sizeof($feeLst)) {
			foreach ($feeLst as $loop){
				if (array_key_exists('subscriptionId', $loop)) {
					//正常绑定
					if(!$loop['fwdUnaswRsn'] && !$loop['ulFailReason']){
						//接通,记录次数
						global $dsql;
						$query = $dsql->SetQuery("select caller,callee,subscriptionId from #@__phonebind where subscriptionId='{$loop['subscriptionId']}'");
						$callInfo = $dsql->dsqlOper($query, "results");
						$insertSql = $dsql->SetQuery("insert into #@__callrecord values('{$callInfo[0]['caller']}','{$callInfo[0]['callee']}','" . date("Y-m-d") . "')");
						$dsql->dsqlOper($insertSql, 'update');
						/*$callLimit = $this->judgeCalledTimesLimit($callInfo[0]['caller'],$callInfo[0]['callee']);
						if(!$callLimit){
							//取消绑定
							$this->unbind($callInfo[0]['subscriptionId']);
						}*/
					}
				}
			}
		} 
	}


}
