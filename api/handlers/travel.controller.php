<?php

/**
 * huoniaoTag模板标签函数插件-旅游模块
 *
 * @param $params array 参数集
 * @return array
 */
function travel($params, $content = "", &$smarty = array(), &$repeat = array()){
	$service = "travel";
	extract ($params);
	if(empty($action)) return '';

	global $template;
	global $huoniaoTag;
	global $dsql;
	global $userLogin;
	global $cfg_basehost;
	global $cfg_secureAccess;
	global $langData;

	$userid = $userLogin->getMemberID();

	if($action == "storeDetail" || $action == "store-detail"){
		$detailHandels = new handlers($service, "storeDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id));
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;

				global $template;
				if($template != 'config'){
					detailCheckCity("travel", $detailConfig['id'], $detailConfig['cityid'], "store-detail");
				}

				if($action == "store-detail"){
					//更新浏览次数
					$sql = $dsql->SetQuery("UPDATE `#@__travel_store` SET `click` = `click` + 1 WHERE `id` = ".$id);
					$dsql->dsqlOper($sql, "results");
				}

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
				$state = 1;

			}

			$huoniaoTag->assign('storeState', $state);
			$huoniaoTag->assign('storeId', $id);
		}else{
			if($action == "store-detail"){
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
			}
		}
	}elseif($action == "fabu"){//会员中心发布

		if($type == "hotel"){//旅游酒店
			$act = "hotelDetail";
		}elseif($type == "ticket"){//景点门票
			$act = "ticketDetail";
		}elseif($type == "rentcar"){//旅游汽车
			$act = "rentcarDetail";
		}elseif($type == "visa"){//签证
			$act = "visaDetail";
		}elseif($type == "agency"){//周边游
			$act = "agencyDetail";
		}elseif($type == "video"){//旅游视频
			$act = "videoDetail";
		}elseif($type == "strategy"){//旅游攻略
			$act = "strategyDetail";
		}

		if($id){
			$detailHandels = new handlers($service, $act);
			$detailConfig  = $detailHandels->getHandle($id);
			if(is_array($detailConfig) && $detailConfig['state'] == 100){
				$detailConfig  = $detailConfig['info'];//print_R($detailConfig);exit;
				if(is_array($detailConfig)){
					foreach ($detailConfig as $key => $value) {
						$huoniaoTag->assign('detail_'.$key, $value);
					}
				}
			}else{
				header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
				die;
			}
		}

	}elseif($action == "video-detail"){//视频详情
		$detailHandels = new handlers($service, "videoDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id));
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;
				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__travel_video` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "rentcar-detail"){//租车详情
		$detailHandels = new handlers($service, "rentcarDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id));
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;
				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__travel_rentcar` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "strategy-detail"){//攻略详情
		$detailHandels = new handlers($service, "strategyDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id));
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;
				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__travel_strategy` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "ticket-detail"){//攻略详情
		$detailHandels = new handlers($service, "ticketDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id));
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;
				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__travel_ticket` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "hotel-detail"){//酒店民宿
		$detailHandels = new handlers($service, "hotelDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id));
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;
				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__travel_hotel` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "agency-detail"){//周边游
		$detailHandels = new handlers($service, "agencyDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id));
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;
				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__travel_agency` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "visacountry-detail"){

		$detailHandels = new handlers($service, "countryDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id));
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;
				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__travel_visacountrytype` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

				require(HUONIAOINC."/config/travel.inc.php");
				$customvisaCountryStatement = $customvisaCountryStatement;
				$huoniaoTag->assign('visaCountryStatement', $customvisaCountryStatement);

			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == 'visa'){
		$detailHandels = new handlers($service, "countryDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $country));
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){
				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('list_'.$key, $value);
				}
			}
		}
		$huoniaoTag->assign('country', $country);
	}elseif($action == "visa-detail" || $action == "require"){//签证

		$detailHandels = new handlers($service, "visaDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id));
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;
				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__travel_visa` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

				if($type==1){
					$requirename = $langData['travel'][8][54];
					$aid = $detailConfig['incumbents'];
				}elseif($type==2){
					$requirename = $langData['travel'][8][56];
					$aid = $detailConfig['retiree'];
				}elseif($type==3){
					$requirename = $langData['travel'][8][55];
					$aid = $detailConfig['professional'];
				}elseif($type==4){
					$requirename = $langData['travel'][8][57];
					$aid = $detailConfig['students'];
				}elseif($type==5){
					$requirename = $langData['travel'][8][58];
					$aid = $detailConfig['children'];
				}

				$huoniaoTag->assign('type', (int)$type);
				$huoniaoTag->assign('requirename', $requirename);
				$huoniaoTag->assign('aid', $aid);

			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "comment"){
		// print_R($id);exit;
		$type = $type ? (int)$type : 0;

		if(empty($type)){
			$act = 'videoDetail';
		}elseif($type == 1){
			$act = 'strategyDetail';
		}elseif($type == 2){
			$act = 'ticketDetail';
		}elseif($type == 3){
			$act = 'agencyDetail';
		}elseif($type == 4){
			$act = 'visaDetail';
		}

		$detailHandels = new handlers($service, $act);
		$detailConfig  = $detailHandels->getHandle($id);
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;
				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}

		$huoniaoTag->assign('type', $type);

	}elseif($action == "comdetail"){
		$id   = (int)$id;
		$type = $type ? (int)$type : 0;
		$huoniaoTag->assign('type', $type);

        $detailHandels = new handlers("member", "commentDetail");
        $detail  = $detailHandels->getHandle(array("id" => $id));
        if(is_array($detail) && $detail['state'] == 100){
            $detail  = $detail['info'];
            foreach ($detail as $key => $value) {
                $huoniaoTag->assign('detail_'.$key, $value);
			}
			
			if(empty($type)){
				$act = 'videoDetail';
			}elseif($type == 1){
				$act = 'strategyDetail';
			}elseif($type == 2){
				$act = 'ticketDetail';
			}elseif($type == 3){
				$act = 'agencyDetail';
			}elseif($type == 4){
				$act = 'visaDetail';
			}

			$aid = $detail['aid'];
			$detailHandels = new handlers($service, $act);
			$detailConfig  = $detailHandels->getHandle($aid);
			if(is_array($detailConfig) && $detailConfig['state'] == 100){
				$detailConfig  = $detailConfig['info'];
				if(is_array($detailConfig)){//print_R($detailConfig);exit;
					//输出详细信息
					foreach ($detailConfig as $key => $value) {
						$huoniaoTag->assign('details_'.$key, $value);
					}
				}
			}

        }else{
            $param = array(
				"service" => $service,
			);
			header("location:".getUrlPath($param));
			die;
        }
	}elseif($action == "confirm-order"){//提交订单
		
		global $userLogin;
		$userid = $userLogin->getMemberID();

		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}
		
		$huoniaoTag->assign('type', (int)$type);
		$huoniaoTag->assign('id', $id);

		$isorder = false;

		if($type == 1 || $type == 2){
			$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $id ");
			$ret = $dsql->dsqlOper($sql, 'results');
			if(!empty($ret)){
				$isorder = true;
				if($type == 1){//景点门票
					$act = 'ticketDetail';
				}elseif($type == 2){//周边游
					$act = 'agencyDetail';
				}
				
				$newid = $ret[0]['ticketid'];
				foreach($ret[0] as $key => $value){
					if($key == 'specialtime'){
						$specialtime = unserialize($value);
					}else{
						${$key} = $value;
					}
					
					$huoniaoTag->assign($key, $value);
					$huoniaoTag->assign('specialtime', $specialtime);
					$huoniaoTag->assign('specialtimejson', $specialtime ? json_encode($specialtime) : '');
				}
				
			}else{
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
			}
		}elseif($type == 3){//酒店入驻
			$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `area`, `iswindow`, `typeid`, `breakfast`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $id ");
			$ret = $dsql->dsqlOper($sql, 'results');
			if(!empty($ret)){
				$isorder = true;

				$act = 'hotelDetail';
				$newid = $ret[0]['hotelid'];

				$travel = new travel();
				foreach($ret[0] as $key => $value){
					if($key == 'specialtime'){
						$specialtime = unserialize($value);
					}elseif($key == 'iswindow'){
						$iswindowname = $travel->gettypename("iswindow_type", $value);
					}elseif($key == 'typeid'){
						$typeidname = $travel->gettypename("room_type", $value);
					}elseif($key == 'breakfast'){
						$breakfastname = $travel->gettypename("breakfast_type", $value);
					}else{
						${$key} = $value;
					}
					
					$huoniaoTag->assign($key, $value);
					$huoniaoTag->assign('iswindowname', $iswindowname);
					$huoniaoTag->assign('typeidname', $typeidname);
					$huoniaoTag->assign('breakfastname', $breakfastname);
					$huoniaoTag->assign('specialtime', $specialtime);
					$huoniaoTag->assign('specialtimejson', $specialtime ? json_encode($specialtime) : '');
				}

			}else{
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
			}
		}elseif($type == 4){
			$isorder = true;
			$newid = $id;
			$act = 'visaDetail';
		}

		if($isorder){
			$detailHandels = new handlers($service, $act);
			$detailConfig  = $detailHandels->getHandle($newid);
			if(is_array($detailConfig) && $detailConfig['state'] == 100){
				$detailConfig  = $detailConfig['info'];//print_R($detailConfig);exit;
				if(is_array($detailConfig)){
					foreach ($detailConfig as $key => $value) {
						$huoniaoTag->assign('detail_'.$key, $value);
					}
				}
			}else{
				header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
				die;
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=2");
		}

	}elseif($action == "comfirm"){//确认订单
		global $userLogin;
		$userid = $userLogin->getMemberID();

		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		$huoniaoTag->assign('ordernumold', $ordernum);

		$RenrenCrypt = new RenrenCrypt();
		$ordernums = $RenrenCrypt->php_decrypt(base64_decode($ordernum));

		if(!empty($ordernums)){
			$huoniaoTag->assign('ordernum', $ordernums);

			$ordernumArr = explode(",", $ordernums);
			$orderArr    = array();
			$totalAmount = 0;

			foreach ($ordernumArr as $key => $value) {
				//获取订单内容
				$archives = $dsql->SetQuery("SELECT `proid`, `procount`, `people`, `contact`, `idcard`, `orderprice`, `type`, `orderstate`, `walktime`, `departuretime`, `email` FROM `#@__travel_order` WHERE `ordernum` = '$value' AND `userid` = $userid");
				$orderDetail  = $dsql->dsqlOper($archives, "results");
				if($orderDetail){
					$proid      = $orderDetail[0]['proid'];
					$procount   = $orderDetail[0]['procount'];
					$orderprice = $orderDetail[0]['orderprice'];
					$orderstate = $orderDetail[0]['orderstate'];
					$type       = $orderDetail[0]['type'];
					$walktime   = $orderDetail[0]['walktime'];
					$people     = $orderDetail[0]['people'];
					$contact    = $orderDetail[0]['contact'];
					$idcard     = $orderDetail[0]['idcard'];
					$departuretime     = $orderDetail[0]['departuretime'];
					$email     = $orderDetail[0]['email'];
					//总价
					$totalAmount += $orderprice * $procount;

					//验证订单状态，如果不是待付款状态则跳转至订单列表
					if($orderstate != 0){
						$param = array(
							"service"     => "member",
							"type"        => "user",
							"template"    => "order",
							"module"      => "travel"
						);
						$url = getUrlPath($param);
						header("location:".$url);
						die;
					}

					$isorder = false;

					if($type==4){//签证
						$isorder = true;
						$newid   = $proid;
						$act     = 'visaDetail';
					}else{
					
						if($type==1 || $type==2){//景点门票 周边游
							$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
						}elseif($type==3){//酒店
							$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
						}
						$ret = $dsql->dsqlOper($sql, 'results');
						if(!empty($ret)){
							$isorder = true;
							if($type==1){
								$act = 'ticketDetail';
							}elseif($type==2){
								$act = 'agencyDetail';
							}elseif($type==3){
								$act = 'hotelDetail';
							}
							if($type==1 || $type==2){
								$newid = $ret[0]['ticketid'];
							}elseif($type==3){
								$newid = $ret[0]['hotelid'];
							}
							foreach($ret[0] as $key => $value){
								if($key == 'specialtime'){
									$specialtime = unserialize($value);
								}else{
									${$key} = $value;
								}
								
								$huoniaoTag->assign($key, $value);
								$huoniaoTag->assign('specialtime', $specialtime);
								$huoniaoTag->assign('specialtimejson', $specialtime ? json_encode($specialtime) : '');
							}
							
						}else{
							header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
						}
					}

					if($isorder){
						$detailHandels = new handlers($service, $act);
						$detailConfig  = $detailHandels->getHandle($newid);
						if(is_array($detailConfig) && $detailConfig['state'] == 100){
							$detailConfig  = $detailConfig['info'];//print_R($detailConfig);exit;
							if(is_array($detailConfig)){
								foreach ($detailConfig as $key => $value) {
									$huoniaoTag->assign('detail_'.$key, $value);
								}
							}
						}else{
							header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
							die;
						}
					}


				}else{//订单不存在
					header("location:".$cfg_secureAccess.$cfg_basehost);
					die;
				}
			}
			$huoniaoTag->assign('type', $type);
			$huoniaoTag->assign('procount', $procount);
			$huoniaoTag->assign('walktime', $walktime);
			$huoniaoTag->assign('people', $people);
			$huoniaoTag->assign('contact', $contact);
			$huoniaoTag->assign('departuretime', $departuretime);
			$huoniaoTag->assign('email', $email);
			$huoniaoTag->assign('idcard', $idcard);
			$huoniaoTag->assign('orderArr', $orderArr);
			$huoniaoTag->assign('totalAmount', sprintf("%.2f", $totalAmount));
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost);
			die;
		}
	}elseif($action == "pay"){
		global $userLogin;
		$userid = $userLogin->getMemberID();

		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		$RenrenCrypt = new RenrenCrypt();
		$ordernums = $RenrenCrypt->php_decrypt(base64_decode($ordernum));

		if(!empty($ordernums)){
			$huoniaoTag->assign('ordernum', $ordernums);

			$ordernumArr = explode(",", $ordernums);
			$orderArr    = array();
			$totalAmount = 0;

			foreach ($ordernumArr as $key => $value) {

				//获取订单内容
				$archives = $dsql->SetQuery("SELECT `proid`, `procount`, `orderprice`, `type`, `orderstate` FROM `#@__travel_order` WHERE `ordernum` = '$value' AND `userid` = $userid");
				$orderDetail  = $dsql->dsqlOper($archives, "results");
				if($orderDetail){

					$proid      = $orderDetail[0]['proid'];
					$procount   = $orderDetail[0]['procount'];
					$orderprice = $orderDetail[0]['orderprice'];
					$orderstate = $orderDetail[0]['orderstate'];
					$type       = $orderDetail[0]['type'];
					//总价
					$totalAmount += $orderprice * $procount;

					//验证订单状态，如果不是待付款状态则跳转至订单列表
					if($orderstate != 0){
						$param = array(
							"service"     => "member",
							"type"        => "user",
							"template"    => "order",
							"module"      => "travel"
						);
						$url = getUrlPath($param);
						header("location:".$url);
						die;
					}

					$isorder = false;

					if($type==4){//签证
						$isorder = true;
						$newid   = $proid;
						$act     = 'visaDetail';
					}else{

						if($type==1 || $type==2){//景点门票 周边游
							$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
						}elseif($type==3){//酒店
							$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
						}
						$ret = $dsql->dsqlOper($sql, 'results');
						if(!empty($ret)){
							$isorder = true;
							if($type==1){
								$act = 'ticketDetail';
							}elseif($type==2){
								$act = 'agencyDetail';
							}elseif($type==3){
								$act = 'hotelDetail';
							}
							if($type==1 || $type==2){
								$newid = $ret[0]['ticketid'];
							}elseif($type==3){
								$newid = $ret[0]['hotelid'];
							}
							foreach($ret[0] as $key => $value){
								if($key == 'specialtime'){
									$specialtime = unserialize($value);
								}else{
									${$key} = $value;
								}
								
								$huoniaoTag->assign($key, $value);
								$huoniaoTag->assign('specialtime', $specialtime);
								$huoniaoTag->assign('specialtimejson', $specialtime ? json_encode($specialtime) : '');
							}
							
						}else{
							header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
						}
					}

					if($isorder){
						$detailHandels = new handlers($service, $act);
						$detailConfig  = $detailHandels->getHandle($newid);
						if(is_array($detailConfig) && $detailConfig['state'] == 100){
							$detailConfig  = $detailConfig['info'];//print_R($detailConfig);exit;
							if(is_array($detailConfig)){
								foreach ($detailConfig as $key => $value) {
									$huoniaoTag->assign('detail_'.$key, $value);
								}
							}
						}else{
							header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
							die;
						}
					}

				//订单不存在
				}else{
					header("location:".$cfg_secureAccess.$cfg_basehost);
					die;
				}

			}

			$huoniaoTag->assign('orderArr', $orderArr);
			$huoniaoTag->assign('totalAmount', sprintf("%.2f", $totalAmount));

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost);
			die;
		}

	//支付结果页面
	}elseif($action == "payreturn" || $action == "travel-ticketstate" || $action == "travel-hotelstate"){
		global $userLogin;
		$userid = $userLogin->getMemberID();

		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}
		
		
		if(!empty($ordernum)){

			//根据支付订单号查询支付结果
			$archives = $dsql->SetQuery("SELECT `body`, `amount`, `state` FROM `#@__pay_log` WHERE `ordertype` = 'travel' AND `ordernum` = '$ordernum' AND `uid` = $userid");
			$payDetail  = $dsql->dsqlOper($archives, "results");
			if($payDetail){

				$proArr = array();
				$isaddr = 0;
				$address = "";
				$i = 0;
				$ids = explode(",", $payDetail[0]['body']);

				foreach ($ids as $key => $value) {

					//查询订单详细信息
					$archives = $dsql->SetQuery("SELECT `id`, `proid`, `procount`, `orderstate`, `people`,`contact`, `orderprice`, `type`, `walktime`, `departuretime` FROM `#@__travel_order` WHERE `ordernum` = '$value' AND `userid` = $userid");
					$orderDetail  = $dsql->dsqlOper($archives, "results");
					if($orderDetail){
						$orderDetail = $orderDetail[0];

						$type  = $orderDetail['type'];
						$orderid = $orderDetail['id'];
						$orderstate = $orderDetail['orderstate'];
						$proid = $orderDetail['proid'];
						$people = $orderDetail['people'];
						$contact = $orderDetail['contact'];
						$walktimes = $orderDetail['walktime'] ? date("m月d日", $orderDetail['walktime']) : "";
						$departuretime = $orderDetail['departuretime'] ? date("m月d日", $orderDetail['departuretime']) : "";
						$id    = $orderDetail['id'];

						

						$detailHandels = new handlers($service, "orderDetail");
						$detailConfig  = $detailHandels->getHandle($id);

						if(is_array($detailConfig) && $detailConfig['state'] == 100){
							$detailConfig  = $detailConfig['info'];//print_R($detailConfig);exit;
							if(is_array($detailConfig)){
								foreach ($detailConfig as $key => $value) {
									$huoniaoTag->assign('detail_'.$key, $value);
								}
							}
						}else{
							header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
						}

						if($type==3){
							$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `area`, `iswindow`, `typeid`, `breakfast`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");//print_R($sql);exit;
							$ret = $dsql->dsqlOper($sql, 'results');
							if(!empty($ret)){
								$travel = new travel();
								foreach($ret[0] as $key => $value){
									if($key == 'specialtime'){
										$specialtime = unserialize($value);
									}elseif($key == 'iswindow'){
										$iswindowname = $travel->gettypename("iswindow_type", $value);
									}elseif($key == 'typeid'){
										$typeidname = $travel->gettypename("room_type", $value);
									}elseif($key == 'breakfast'){
										$breakfastname = $travel->gettypename("breakfast_type", $value);
									}else{
										${$key} = $value;
									}
									
									$huoniaoTag->assign($key, $value);
									$huoniaoTag->assign('iswindowname', $iswindowname);
									$huoniaoTag->assign('typeidname', $typeidname);
									$huoniaoTag->assign('breakfastname', $breakfastname);
									$huoniaoTag->assign('specialtime', $specialtime);
									$huoniaoTag->assign('specialtimejson', $specialtime ? json_encode($specialtime) : '');
								}

							}else{
								header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
							}
						}
						
					}
				}


				$huoniaoTag->assign('state', $payDetail[0]['state']);
				$huoniaoTag->assign('walktimes', $walktimes);
				$huoniaoTag->assign('orderid', $orderid);
				$huoniaoTag->assign('people', $people);
				$huoniaoTag->assign('contact', $contact);
				$huoniaoTag->assign('orderstate', $orderstate);
				$huoniaoTag->assign('departuretime', $departuretime);
				$huoniaoTag->assign('totalAmount', sprintf("%.2f", $payDetail[0]['amount']));



			//支付订单不存在
			}else{
				header("location:".$cfg_secureAccess.$cfg_basehost);
				die;
			}

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost);
			die;
		}
	}elseif($action == "index"){
		$detailHandels = new handlers($service, "config");
		$detailConfig  = $detailHandels->getHandle();
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){
				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('index_'.$key, $value);
				}
			}
		}
	}




	if(empty($smarty)) return;

	if(!isset($return))
		$return = 'row'; //返回的变量数组名

	//注册一个block的索引，照顾smarty的版本
    if(method_exists($smarty, 'get_template_vars')){
        $_bindex = $smarty->get_template_vars('_bindex');
    }else{
        $_bindex = $smarty->getVariable('_bindex')->value;
    }

    if(!$_bindex){
        $_bindex = array();
    }

    if($return){
        if(!isset($_bindex[$return])){
            $_bindex[$return] = 1;
        }else{
            $_bindex[$return] ++;
        }
    }

    $smarty->assign('_bindex', $_bindex);

	//对象$smarty上注册一个数组以供block使用
	if(!isset($smarty->block_data)){
		$smarty->block_data = array();
	}

	//得一个本区块的专属数据存储空间
	$dataindex = md5(__FUNCTION__.md5(serialize($params)));
	$dataindex = substr($dataindex, 0, 16);

	//使用$smarty->block_data[$dataindex]来存储
	if(!$smarty->block_data[$dataindex]){
		//取得指定动作名
		$moduleHandels = new handlers($service, $action);

		$param = $params;
		$moduleReturn  = $moduleHandels->getHandle($param);

		//只返回数据统计信息
		if($pageData == 1){
			if(!is_array($moduleReturn) || $moduleReturn['state'] != 100){
				$pageInfo_ = array("totalCount" => 0, "gray" => 0, "audit" => 0, "refuse" => 0);
			}else{
				$moduleReturn  = $moduleReturn['info'];  //返回数据
				$pageInfo_ = $moduleReturn['pageInfo'];
			}
			$smarty->block_data[$dataindex] = array($pageInfo_);

		//指定数据
		}elseif(!empty($get)){
			$retArr = $moduleReturn['state'] == 100 ? $moduleReturn['info'][$get] : "";
			$retArr = is_array($retArr) ? $retArr : array();
			$smarty->block_data[$dataindex] = $retArr;

		//正常返回
		}else{

			global $pageInfo;
			if(!is_array($moduleReturn) || $moduleReturn['state'] != 100) {
				$pageInfo = array();
				$smarty->assign('pageInfo', $pageInfo);
				return '';
			}
			$moduleReturn  = $moduleReturn['info'];  //返回数据
			$pageInfo_ = $moduleReturn['pageInfo'];
			if($pageInfo_){
				//如果有分页数据则提取list键
				$moduleReturn  = $moduleReturn['list'];
				$pageInfo = $pageInfo_;
			}else{
				$pageInfo = array();
			}
			$smarty->assign('pageInfo', $pageInfo);
			$smarty->block_data[$dataindex] = $moduleReturn;  //存储数据

		}

	}

	//果没有数据，直接返回null,不必再执行了
	if(!$smarty->block_data[$dataindex]) {
		$repeat = false;
		return '';
	}

	//一条数据出栈，并把它指派给$return，重复执行开关置位1
	if(list($key, $item) = each($smarty->block_data[$dataindex])){
		$smarty->assign($return, $item);
		$repeat = true;
	}

	//如果已经到达最后，重置数组指针，重复执行开关置位0
	if(!$item) {
		reset($smarty->block_data[$dataindex]);
		$repeat = false;
	}

	//打印内容
	print $content;
}
