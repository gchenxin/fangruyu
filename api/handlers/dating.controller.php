<?php

/**
 * huoniaoTag模板标签函数插件-交友模块
 *
 * @param $params array 参数集
 * @return array
 */
function dating($params, $content = "", &$smarty = array(), &$repeat = array()){
	$service = "dating";
	extract ($params);
	if(empty($action)) return '';

	global $huoniaoTag;
	global $dsql;
	global $userLogin;
	global $cfg_secureAccess;
	global $cfg_basehost;

	// 保存数据
	$moduleData = array();

	if(empty($smarty)){

		$userid = $userLogin->getMemberID();
		$furl = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

		$datingHandels = new handlers($service, "config");
		$datingConfig_ = $datingHandels->getHandle();
		$datingConfig = $datingConfig_['info'];

		$huoniaoTag->assign('datingConfig', $datingConfig);

		//获取当前登录用户的交友ID，如果还没有交友ID就进入我的资料页面，提示打开交友开关
		$uid = 0;
		$hnUid = 0;//红娘uid
		$storeUid = 0;//门店uid
		if($userid > -1){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = 0 AND `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
			}
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = 1 AND `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$hnUid = $ret[0]['id'];
			}
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = 2 AND `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$storeUid = $ret[0]['id'];
			}
		}
		$huoniaoTag->assign("uid", $uid);
		$huoniaoTag->assign("hnUid", $hnUid);
		$huoniaoTag->assign("storeUid", $storeUid);

		if($uid == 0 && $realServer != "member"){
			// 不需要登陆就可以访问的页面
			if($action != "index" && $action != "story" && $action != "story-detail"){
				$param = array(
					"service" => "dating",
					"template" => "index",
					"param" => "check=".GetMkTime(time())
				);
				header("location:".getUrlPath($param));
				die;
			}
		}

		// 用户
		if($uid > 0){

			// 更新最后活跃时间
			$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `activedate` = '".GetMkTime(time())."' WHERE `id` = $uid");
			$dsql->dsqlOper($sql, "update");

			//获取会员的被看过，看过的统计数据
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$uid' AND `type` = 1");
			$vin = $dsql->dsqlOper($sql, "totalCount");
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `uto` = '$uid' AND `type` = 1");
			$vout = $dsql->dsqlOper($sql, "totalCount");

			//获取会员的被关注，关注的统计数据
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$uid' AND `type` = 2");
			$fin = $dsql->dsqlOper($sql, "totalCount");
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `uto` = '$uid' AND `type` = 2");
			$fout = $dsql->dsqlOper($sql, "totalCount");

			//获取会员的被打招呼，打招呼的统计数据
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$uid' AND `type` = 3");
			$min = $dsql->dsqlOper($sql, "totalCount");
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `uto` = '$uid' AND `type` = 3");
			$mout = $dsql->dsqlOper($sql, "totalCount");

			// 收到的招呼-未读
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `uto` = '$uid' AND `type` = 3 AND `readto` = 0");
			$mout_new = $dsql->dsqlOper($sql, "totalCount");

			// 收到的礼物-未读
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_gift_put` WHERE `uto` = '$uid' AND `read` = 0");
			$gift_new = $dsql->dsqlOper($sql, "totalCount");

			// 新状态的牵线
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_lead` WHERE (
				(`ufrom` = '$uid' AND ( (`state` = 2 AND `new2` = 0) || (`state` = 3 AND `new3` = 0) ) )
				OR
				(`uto` = '$uid' AND `state` = 1 AND `new1` = 0)
			)");
			$lead_new = $dsql->dsqlOper($sql, "totalCount");

			$huoniaoTag->assign("vin", $vin);
			$huoniaoTag->assign("vout", $vout);
			$huoniaoTag->assign("fin", $fin);
			$huoniaoTag->assign("fout", $fout);
			$huoniaoTag->assign("min", $min);
			$huoniaoTag->assign("mout", $mout);
			$huoniaoTag->assign("mout_new", $mout_new);
			$huoniaoTag->assign("gift_new", $gift_new);
			$huoniaoTag->assign("lead_new", $lead_new);


			//输出当前登录交友账号的基本信息
			$dataHandels = new handlers($service, "memberInfo");
			$loginMemberDetail  = $dataHandels->getHandle($uid);
			if($loginMemberDetail && is_array($loginMemberDetail) && $loginMemberDetail['state'] == 100){
				$loginMemberDetail = $loginMemberDetail['info'];
				foreach ($loginMemberDetail as $key => $value) {
					$huoniaoTag->assign("u_".$key, $value);
				}
			}

			//获取会员的未查看的私信数量
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_review_list` WHERE `to` = $uid AND `isread` = 0");
			$ret = $dsql->dsqlOper($sql, "totalCount");
			$huoniaoTag->assign("u_review", $ret);

			// 查询是否有新动态
			if($action != "circle"){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_circle` WHERE `pubdate` > ".$loginMemberDetail['visit_circle_date']);
				$ret = $dsql->dsqlOper($sql, "results");
				$u_circle = $ret ? 1 : 0;
			}else{
				$u_circle = 0;
			}
			$huoniaoTag->assign("u_circle", $u_circle);


			// 赠送物品
			$today = date("Ymd");
			if($loginMemberDetail['dayinit'] != $today){
				if(isset($moduleData['levelConfig'])){
					$config = $moduleData['levelConfig'];
				}else{
					$detailHandels = new handlers($service, "getMemberLevelInfo");
					$detailConfig  = $detailHandels->getHandle(array("userid" => $userid, "common" => true));
					$config = $detailConfig['info'];
					$moduleData['levelConfig'] = $config;
				}
				$privilege = $config['privilege'];
				// 聊天钥匙
				if($privilege['key']){
					$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `key` = '".$privilege['key']."', `dayinit` = '$today' WHERE `id` = $uid");
					$dsql->dsqlOper($sql, "update");
				}
			}

		}
		// 红娘
		if($hnUid > 0){
			//输出当前登录交友账号的基本信息
			$dataHandels = new handlers($service, "hnInfo");
			$loginHnMemberDetail  = $dataHandels->getHandle($hnUid);
			if($loginHnMemberDetail && is_array($loginHnMemberDetail) && $loginHnMemberDetail['state'] == 100){
				$loginHnMemberDetail = $loginHnMemberDetail['info'];
				foreach ($loginHnMemberDetail as $key => $value) {
					$huoniaoTag->assign("hn_".$key, $value);
				}
			}
		}
		// 门店
		if($storeUid > 0){
			//输出当前登录交友账号的基本信息
			$dataHandels = new handlers($service, "storeInfo");
			$loginStoreMemberDetail  = $dataHandels->getHandle($storeUid);
			if($loginStoreMemberDetail && is_array($loginStoreMemberDetail) && $loginStoreMemberDetail['state'] == 100){
				$loginStoreMemberDetail = $loginStoreMemberDetail['info'];
				foreach ($loginStoreMemberDetail as $key => $value) {
					$huoniaoTag->assign("store_".$key, $value);
				}
			}
		}

		// 查询新消息
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE `isread` = 0 AND `pid` != 0 AND (`to` = $uid || `to` = $hnUid || `to` = $storeUid)");
		$ret = $dsql->dsqlOper($sql, "totalCount");
		$huoniaoTag->assign('myNewMsgCount', $ret);

	}

	$huoniaoTag->assign('keywords', $keywords);

	//我的交友
	if($action == "my" || $action == "my_hn" || $action == "my_store"){

		$uid = $userLogin->getMemberID();
		if($uid == -1){
			header("location:".$cfg_secureAccess.$cfg_basehost."/login.html?furl=".$furl);
		}

		if($action == "my"){
			// 谁看过我
			$param = array("oper" => "vist", "act" => "in", "new" => 1 );
			$dataHandels = new handlers($service, "visit");
			$lookMe  = $dataHandels->getHandle($param);

			// 谁喜欢我
			$param = array("oper" => "follow", "act" => "in", "new" => 1 );
			$dataHandels = new handlers($service, "visit");
			$followMe  = $dataHandels->getHandle($param);
			// print_r($followMe);

			// 我喜欢谁
			$param = array("oper" => "follow", "act" => "out", "new" => 1 );
			$dataHandels = new handlers($service, "visit");
			$myFollow  = $dataHandels->getHandle($param);

			$huoniaoTag->assign('lookMe', is_array($lookMe) && $lookMe['state'] == 100 ? $lookMe['info'] : "");
			$huoniaoTag->assign('followMe', is_array($followMe) && $followMe['state'] == 100 ? $followMe['info'] : "");
			$huoniaoTag->assign('myFollow', is_array($myFollow) && $myFollow['state'] == 100 ? $myFollow['info'] : "");
		}

		if($hnUid){
			// 我的牵线
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `company` = $hnUid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				global $arr_data;
				$arr_data = "";
				$uidList = arr_foreach($ret);
				$where1 = $where2 = "";
				$where1 .= " AND `uto` IN (".join(",", $uidList).")";
				$where2 .= " AND `ufrom` IN (".join(",", $uidList).")";
				// 新收到的
				$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE `state` = 1 AND `new1` = 0".$where1);
				$newLoadCount = $dsql->dsqlOper($sql, "totalCount");
				//新成功的牵线
				$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE `state` = 2 AND `new2` = 0".$where2);
				$newSuccCount = $dsql->dsqlOper($sql, "totalCount");
				//新失败的牵线
				$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE `state` = 3 AND `new3` = 0".$where2);
				$newFailCount = $dsql->dsqlOper($sql, "totalCount");

				$newLeadCount = $newLoadCount + $newSuccCount + $newFailCount;
				$huoniaoTag->assign('newLeadCount', $newLeadCount);

			}

			// 收到的申请
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_apply` WHERE `uto` = $hnUid AND `read` = 0");
			$newApplyCount1 = $dsql->dsqlOper($sql, "totalCount");
			$huoniaoTag->assign('newApplyCount1', $newApplyCount1);

			// 新的收入
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_money` WHERE `uid` = $hnUid AND `type` = 1 AND `extnew1` = 0");
			$newIncomeCount1 = $dsql->dsqlOper($sql, "totalCount");
			$huoniaoTag->assign('newIncomeCount1', $newIncomeCount1);
		}else{
			$huoniaoTag->assign('newLeadCount', 0);
			$huoniaoTag->assign('newApplyCount1', 0);
			$huoniaoTag->assign('newIncomeCount1', 0);
		}

		if($storeUid){
			// 收到的申请
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_apply` WHERE `uto` = $storeUid AND `read` = 0");
			$newApplyCount2 = $dsql->dsqlOper($sql, "totalCount");
			$huoniaoTag->assign('newApplyCount2', $newApplyCount2);

			// 新的收入
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `company` = $storeUid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				global $arr_data;
				$arr_data = "";
				$userIds = arr_foreach($ret);
				$where .= " AND `uid` IN (".join(",", $userIds).")";
			}else{
				$where .= " AND 1 = 2";
			}
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_money` WHERE `uid` = $hnUid AND `type` = 1 AND `extnew2` = 0".$where);
			$newIncomeCount2 = $dsql->dsqlOper($sql, "totalCount");
			$huoniaoTag->assign('newIncomeCount2', $newIncomeCount2);
		}else{
			$huoniaoTag->assign('newApplyCount2', 0);
			$huoniaoTag->assign('newIncomeCount2', 0);
		}

	}

	if($action == "index"){
		$check_ = 0;
		$check = (int)$check;
		if($check){
			$now = GetMkTime(time());
			if($now - $check <= 2){
				$check_ = 1;
			}
		}
		$huoniaoTag->assign("check", $check_);
	}


	//搜索
	if($action == "search"){

		$userinfo = $userLogin->getMemberInfo();
		if($userid > -1 && $sex == ""){
			if($userinfo['sex'] == 0){
				$sex = $_GET['sex'] = 1;
			}else{
				$sex = $_GET['sex'] = 0;
			}
		}elseif($sex == ""){
			$_GET['sex'] = 0;
		}

		//输出所有GET参数
		$pageParam = array();
		foreach($_GET as $key => $val){
			$huoniaoTag->assign($key, htmlspecialchars(RemoveXSS($val)));
			if($key != "service" && $key != "template" && $key != "page"){
				array_push($pageParam, $key."=".htmlspecialchars(RemoveXSS($val)));
			}
		}
		$huoniaoTag->assign("pageParam", join("&", $pageParam));


		//性别
		$sex = (int)$sex;
		if($sex == 0){
			$sexname = "女士";
		}else{
			$sexname = "男士";
		}
		$huoniaoTag->assign('sex', $sex);
		$huoniaoTag->assign('sexname', $sexname);

		//年龄
		$bage = $eage = 0;
		$agename = "";
		if(!empty($age)){
			$age = explode(",", $age);
			$bage = (int)$age[0];
			$eage = (int)$age[1];
			if(empty($bage) && empty($eage)){
				$agename = "18至28岁";
				$bage = 18;
				$eage = 28;
			}elseif(!empty($bage) && !empty($eage)){
				$agename = $bage."至".$eage."岁";
			}elseif(empty($bage)){
				$agename = "18至".$eage."岁";
				$bage = 18;
			}elseif(empty($eage)){
				$agename = $bage."岁以上";
			}
		}else{
			$agename = "18至28岁";
			$bage = 18;
			$eage = 28;
		}
		$huoniaoTag->assign('age', $age);
		$huoniaoTag->assign('agename', $agename);
		$huoniaoTag->assign('bage', $bage);
		$huoniaoTag->assign('eage', $eage);

		//身高
		$bhei = $ehei = 0;
		$heiname = "";
		if(!empty($height)){
			$hei = explode(",", $height);
			$bhei = (int)$hei[0];
			$ehei = (int)$hei[1];
			if(empty($bhei) && empty($ehei)){
				$heiname = "140以上";
				$bhei = 140;
				$ehei = 0;
			}elseif(!empty($bhei) && !empty($ehei)){
				$heiname = $bhei."至".$ehei."cm";
			}elseif(empty($bhei)){
				$heiname = "18至".$ehei."cm";
				$bhei = 18;
			}elseif(empty($ehei)){
				$heiname = $bhei."以上";
			}
		}else{
			$heiname = "140以上";
			$bhei = 140;
			$ehei = 0;
		}
		$huoniaoTag->assign('height', $height);
		$huoniaoTag->assign('heiname', $heiname);
		$huoniaoTag->assign('bhei', $bhei);
		$huoniaoTag->assign('ehei', $ehei);

		//地区
		$addrname = "地区";
		$addr = (int)$addr;
		$addr1 = $addr2 = 0;
		if($addr){
			//地区
			global $data;
			$data = "";
			$addrName = getParentArr("datingaddr", $addr);
			$addrName = array_reverse(parent_foreach($addrName, "id"));
			$addr1 = $addrName[0];
			$addr2 = $addrName[1];

			global $data;
			$data = "";
			$addrName = getParentArr("datingaddr", $addr);
			$addrName = array_reverse(parent_foreach($addrName, "typename"));
			$addrname = join(" ", $addrName);
		}
		$huoniaoTag->assign('addrname', $addrname);
		$huoniaoTag->assign('addr', $addr);
		$huoniaoTag->assign('addr1', (int)$addr1);
		$huoniaoTag->assign('addr2', (int)$addr2);

		//学历
		$education = (int)$education;
		$educationname = "学历";
		if($education){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$education);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$educationname = $ret[0]['typename'];
			}
		}
		$huoniaoTag->assign('education', $education);
		$huoniaoTag->assign('educationname', $educationname);

		//收入
		$income = (int)$income;
		$incomename = "收入";
		if($income){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$income);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$incomename = $ret[0]['typename'];
			}
		}
		$huoniaoTag->assign('income', $income);
		$huoniaoTag->assign('incomename', $incomename);

		//更多条件
		$more = 0;

		//类型
		$typeid = (int)$typeid;
		$typename = "类型";
		if($typeid){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$typeid);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$typename = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('typeid', $typeid);
		$huoniaoTag->assign('typename', $typename);

		//目的
		$purpose = (int)$purpose;
		$purposename = "交友目的";
		if($purpose){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$purpose);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$purposename = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('purpose', $purpose);
		$huoniaoTag->assign('purposename', $purposename);

		//婚姻情况
		$marriage = (int)$marriage;
		$marriagename = "婚姻情况";
		if($marriage){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$marriage);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$marriagename = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('marriage', $marriage);
		$huoniaoTag->assign('marriagename', $marriagename);

		//住房情况
		$housetag = (int)$housetag;
		$housetagname = "住房情况";
		if($housetag){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$housetag);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$housetagname = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('housetag', $housetag);
		$huoniaoTag->assign('housetagname', $housetagname);

		//体形
		$bodytype = (int)$bodytype;
		$bodytypename = "体形";
		if($bodytype){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$bodytype);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$bodytypename = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('bodytype', $bodytype);
		$huoniaoTag->assign('bodytypename', $bodytypename);

		//工作情况
		$workstatus = (int)$workstatus;
		$workstatusname = "工作情况";
		if($workstatus){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$workstatus);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$workstatusname = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('workstatus', $workstatus);
		$huoniaoTag->assign('workstatusname', $workstatusname);

		//吸烟
		$smoke = (int)$smoke;
		$smokename = "吸烟";
		if($smoke){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$smoke);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$smokename = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('smoke', $smoke);
		$huoniaoTag->assign('smokename', $smokename);

		//饮酒
		$drink = (int)$drink;
		$drinkname = "饮酒";
		if($drink){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$drink);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$drinkname = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('drink', $drink);
		$huoniaoTag->assign('drinkname', $drinkname);

		//作息时间
		$workandrest = (int)$workandrest;
		$workandrestname = "作息时间";
		if($workandrest){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$workandrest);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$workandrestname = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('workandrest', $workandrest);
		$huoniaoTag->assign('workandrestname', $workandrestname);

		//购车情况
		$cartag = (int)$cartag;
		$cartagname = "购车情况";
		if($cartag){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$cartag);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$cartagname = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('cartag', $cartag);
		$huoniaoTag->assign('cartagname', $cartagname);

		//制造浪漫
		$romance = (int)$romance;
		$romancename = "制造浪漫";
		if($romance){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ".$romance);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$romancename = $ret[0]['typename'];
			}
			$more = 1;
		}
		$huoniaoTag->assign('romance', $romance);
		$huoniaoTag->assign('romancename', $romancename);

		$huoniaoTag->assign('more', $more);
		$huoniaoTag->assign('online', (int)$online);
		$huoniaoTag->assign('orderby', (int)$orderby);


	//会员详情
	}elseif($action == "u"){

		if($id == $uid){
			$ismine = 1;
			$memberDetail = array("state" => 100, "info" => $loginMemberDetail);
		}else{
			$ismine = 0;
			$detailHandels = new handlers($service, "memberInfo");
			$memberDetail  = $detailHandels->getHandle($id);
		}
		$huoniaoTag->assign('id', $id);
		$huoniaoTag->assign('panel', $panel);
		$huoniaoTag->assign('ismine', $ismine);

		if(is_array($memberDetail) && $memberDetail['state'] == 100){
			$memberDetail  = $memberDetail['info'];
			if(is_array($memberDetail)){
				$lookSpecInfo = false;
				// 非本人
				if($uid != $id){
					if($loginMemberDetail['level']){
						$lookSpecInfo = true;
					}
						$max = 0;
						// 是否需要验证查看数量
						$unCheck = false;
						// 红娘或门店查看下属会员不需要验证查看会员数量
						if($hnUid || $storeUid){
							if($hnUid){
								if($memberDetail['company'] == $hnUid){
									$unCheck = true;
								}
							}else{
								if($memberDetail['company'] && $memberDetail['hn']['store'] && $memberDetail['hn']['store']['id'] == $storeUid){
									$unCheck = true;
								}
							}
						}
						// 不是红娘和门店
						if(!$unCheck){
							$sql = $dsql->SetQuery("SELECT `uto` FROM `#@__dating_visit` WHERE `ufrom` = $uid AND `type` = 1 AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = curdate()");
							$ret = $dsql->dsqlOper($sql, "results");
							if($ret){
								$has = false;
								foreach ($ret as $k => $v) {
									if($v['uto'] == $id){
										$has = true;
										break;
									}
								}
								// 不在今日访问记录中则判断数量
								if(!$has){
									if(isset($moduleData['levelConfig'])){
										$config = $moduleData['levelConfig'];
									}else{
										$detailHandels = new handlers($service, "getMemberLevelInfo");
										$detailConfig  = $detailHandels->getHandle(array("userid" => $userid, "common" => true));
										$config = $detailConfig['info'];
										$moduleData['levelConfig'] = $config;
									}

									$privilege = $config['privilege'];
									if($config['id'] == 1){
										if($privilege['see'] == 0 || count($ret) >= $privilege['see']){
											$max = 1;
										}
									}else{
										if(count($ret) >= $privilege['see'] && $privilege['see']){
											$max = 1;
										}
									}
								}
							}
						}
						if(!$max){
							// 增加访问记录
							$sql = $dsql->SetQuery("DELETE FROM `#@__dating_visit` WHERE `ufrom` = $uid AND `uto` = $id AND `type` = 1");
							$dsql->dsqlOper($sql, "update");
							$archives = $dsql->SetQuery("INSERT INTO `#@__dating_visit` (`ufrom`, `uto`, `type`, `pubdate`) VALUES ('$uid', '$id', '1', ".GetMkTime(time()).")");
							$dsql->dsqlOper($archives, "update");
						}
						$huoniaoTag->assign('todayVisitToMax', $max);

				}else{
					$lookSpecInfo = true;
				}
				//输出详细信息
				// print_r($memberDetail);
				foreach ($memberDetail as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

				//获取会员基本信息
				// $uinfo = $userLogin->getMemberInfo($memberDetail['userid']);
				// $huoniaoTag->assign('uinfo', $uinfo);

				// 是否可查看会员除联系方式外的高级信息
				$huoniaoTag->assign('lookSpecInfo', $lookSpecInfo);

				// 获取聊天信息
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE (`from` = $uid AND `to` = $id) OR (`from` = $id AND `to` = $uid)");
				$ret = $dsql->dsqlOper($sql, "results");
				$huoniaoTag->assign('hasChat', $ret ? 1 : 0);	//是否已创建聊天

				// 是否已牵线
				if($hnUid && $hnUid == $memberDetail['company']){
					$leadState = 0;
				}else{
					$sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__dating_lead` WHERE (`ufrom` = $uid && `uto` = $id) || (`ufrom` = $id && `uto` = $uid)");
					$ret = $dsql->dsqlOper($sql, "results");
					$leadState = $ret ? $ret[0]['state'] : 0;
				}
				$huoniaoTag->assign('leadState', $leadState);	//牵线状态

				$detailHandels = new handlers($service, "config");
				$detailConfig  = $detailHandels->getHandle();
				$config = $detailConfig['info'];
				$goldDeposit = $config['goldDeposit'];
				if(empty($goldDeposit)){
					$goldDeposit = array(6,300,680,1360,2040,2720);
				}else{
					$goldDeposit = explode(",", $goldDeposit);
				}
				$huoniaoTag->assign('goldDeposit', $goldDeposit);
				$huoniaoTag->assign('goldRatio', $config['goldRatio'] ? $config['goldRatio'] : 1);

			}

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}


	//相亲活动详情
	}elseif($action == "activity-detail"){

		if(!empty($id)){
			$detailHandels = new handlers($service, "activityDetail");
			$detailConfig  = $detailHandels->getHandle($id);

			$huoniaoTag->assign('id', $id);

			if(is_array($detailConfig) && $detailConfig['state'] == 100){
				$detailConfig  = $detailConfig['info'];
				if(is_array($detailConfig)){

					detailCheckCity("dating", $detailConfig['id'], $detailConfig['cityid'], "activity");

					//输出详细信息
					foreach ($detailConfig as $key => $value) {
						$huoniaoTag->assign('detail_'.$key, $value);
					}

				}

			}else{
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}


	//秀恩爱详情
	}elseif($action == "story-detail"){

		if(!empty($id)){
			$detailHandels = new handlers($service, "storyDetail");
			$detailConfig  = $detailHandels->getHandle($id);

			$huoniaoTag->assign('id', $id);

			if(is_array($detailConfig) && $detailConfig['state'] == 100){
				$detailConfig  = $detailConfig['info'];
				if(is_array($detailConfig)){

					//输出详细信息
					foreach ($detailConfig as $key => $value) {
						$huoniaoTag->assign('detail_'.$key, $value);
					}

				}

			}else{
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}

	}

	// 同城交友
	if($action == "samecity"){

		// 城市
		$area_data = array();
		$moduleHandels = new handlers("siteConfig", "siteCity");
		$moduleReturn  = $moduleHandels->getHandle();
		if(is_array($moduleReturn) && $moduleReturn['state'] == 100){
			$area_data = $moduleReturn['info'];
		}
		$huoniaoTag->assign('area_data_json', json_encode($area_data));

		// 婚姻
		$marriage_data = array();
		$moduleHandels = new handlers("dating", "item");
		$moduleReturn  = $moduleHandels->getHandle(array("type" => 2));
		if(is_array($moduleReturn) && $moduleReturn['state'] == 100){
			$marriage_data = $moduleReturn['info'];
		}
		$huoniaoTag->assign('marriage_data_json', json_encode($marriage_data));

		// 学历
		$edu_data = array();
		$moduleHandels = new handlers("dating", "item");
		$moduleReturn  = $moduleHandels->getHandle(array("type" => 7));
		if(is_array($moduleReturn) && $moduleReturn['state'] == 100){
			$edu_data = $moduleReturn['info'];
		}
		$huoniaoTag->assign('edu_data_json', json_encode($edu_data));

		// 收入
		$money_data = array();
		$moduleHandels = new handlers("dating", "item");
		$moduleReturn  = $moduleHandels->getHandle(array("type" => 6));
		if(is_array($moduleReturn) && $moduleReturn['state'] == 100){
			$money_data = $moduleReturn['info'];
		}
		$huoniaoTag->assign('money_data_json', json_encode($money_data));


	}elseif($action == "mydata_edit"){

		$huoniaoTag->assign('type', $type);
		// 学历
		$edu_data = array();
		$moduleHandels = new handlers("dating", "item");
		$moduleReturn  = $moduleHandels->getHandle(array("type" => 7, "son" => 0));
		if(is_array($moduleReturn) && $moduleReturn['state'] == 100){
			$edu_data = $moduleReturn['info'];
		}
		$huoniaoTag->assign('edu_data_json', json_encode($edu_data));

		// 收入
		$money_data = array();
		$moduleHandels = new handlers("dating", "item");
		$moduleReturn  = $moduleHandels->getHandle(array("type" => 6, "son" => 0));
		if(is_array($moduleReturn) && $moduleReturn['state'] == 100){
			$money_data = $moduleReturn['info'];
		}
		$huoniaoTag->assign('money_data_json', json_encode($money_data));

		// 收入线
		$moneyLine_data = array();
		$moduleHandels = new handlers("dating", "item");
		$moduleReturn  = $moduleHandels->getHandle(array("type" => 382, "son" => 0));
		if(is_array($moduleReturn) && $moduleReturn['state'] == 100){
			$moneyLine_data = $moduleReturn['info'];
		}
		$huoniaoTag->assign('moneyLine_data_json', json_encode($moneyLine_data));

	// 选择兴趣爱好
	}elseif($action == "my_interests"){
		$cfgHandlers = new handlers("dating", "config");
		$config = $cfgHandlers->getHandle();
		$huoniaoTag->assign('graspLength', $config['info']['graspLength']);

	// 图片展示
	}elseif($action == "picture"){

		$u = $uid == $id ? 1 : 0;
		$showDel = 0;
		$showFoot = 1;

		$huoniaoTag->assign('u', (int)$u);
		$huoniaoTag->assign('id', (int)$id);
		$huoniaoTag->assign('aid', (int)$aid);
		$huoniaoTag->assign('type', $type);
		$huoniaoTag->assign('atpage', (int)$atpage < 1 ? 1 : (int)$atpage);

		// 头像
		if($type == "photo"){
			$img = "";
			if($u){
				$img = $loginMemberDetail['photo'];
			}else{
				$sql = $dsql->SetQuery("SELECT `photo` FROM `#@__dating_member` WHERE `id` = $id");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$img = $ret[0]['photo'] ? getFilePath($ret[0]['photo']) : "";
				}
			}
			$huoniaoTag->assign('img', $img);
		}

		if($u){
			$showDel = 1;
			if($type == "photo"){
				$showDel = 0;
			}
		}
		if($type == "photo"){
			$showFoot = 0;
		}
		$huoniaoTag->assign('showDel', $showDel);
		$huoniaoTag->assign('showFoot', $showFoot);


	// 视频展示
	}elseif($action == "video"){
		// type:circle,myVideo
		$huoniaoTag->assign('type', $type);
		$huoniaoTag->assign('aid', (int)$aid);

		if($type == "myVideo" && $id){
			$mHndlers = new handlers($service, "getMemberSpecInfo");
			$mDetail  = $mHndlers->getHandle(array("name" => "my_video", "id" => $id));
			if(is_array($mDetail) && $mDetail['state'] == 100){
				$videoUrl = $mDetail['info'];
				$huoniaoTag->assign('videoUrl', $videoUrl);
			}
		}
	// 入驻红娘
	}elseif($action == "enter_hn"){

	}elseif($action == "hn_detail"){
		if($id == $hnUid){
			$ismine = 1;
			$detailConfig = array("state" => 100, "info" => $loginHnMemberDetail);
		}else{
			$ismine = 0;
			$detailHandels = new handlers($service, "hnInfo");
			$detailConfig  = $detailHandels->getHandle($id);
		}
		$huoniaoTag->assign('id', $id);
		$huoniaoTag->assign('ismine', $ismine);

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){
				//输出详细信息
				// print_r($detailConfig);
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}

	// 支付页面
	}elseif($action == "pay"){
		$param = array(
			"service" => "dating",
		);
		$moduleUrl = getUrlPath($param);
		if($ordernum){
			if($userid == -1 || $uid == 0){
				header("location:".$moduleUrl);
				die;
			}
			$sql = $dsql->SetQuery("SELECT `id`, `type`, `price`, `count`, `orderstate` FROM `#@__dating_order` WHERE `uid` = $uid AND `ordernum` = '$ordernum' AND `orderstate` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				if($ret[0]['`orderstate`'] != 0){
					$param = array(
						"service" => "dating",
						"template" => "payreturn",
						"param" => "ordernum=".$ordernum
					);
					header("location:".$getUrlPath($param));
					die;
				}
				// 升级会员
				if($ret[0]['type'] == 0){
					$totalAmount = $ret[0]['price'];
				// 充值金币
				}elseif($ret[0]['type'] == 1){
					$totalAmount = $ret[0]['price'] * $ret[0]['count'];
				// 购买牵线
				}elseif($ret[0]['type'] == 2){
					$totalAmount = $ret[0]['price'];
				}

				$huoniaoTag->assign('ordernum', $ordernum);
				$huoniaoTag->assign('totalAmount', $totalAmount);
				$huoniaoTag->assign('allowUsePoint', false);
			}else{
				header("location:".$moduleUrl);
				die;
			}
		}
	// 支付返回结果
	}elseif($action == "payreturn"){
		global $userLogin;
		$userid = $userLogin->getMemberID();

		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		if(!empty($ordernum)){

			//根据支付订单号查询支付结果
			$archives = $dsql->SetQuery("SELECT `body`, `amount`, `state`, `ordernum`, `pubdate` FROM `#@__pay_log` WHERE `ordertype` = 'dating' AND `ordernum` = '$ordernum' AND `uid` = $userid");
			$payDetail  = $dsql->dsqlOper($archives, "results");
			if($payDetail){
				$state = $payDetail[0]['state'];
				$ordernum = $payDetail[0]['ordernum'];
				$date = $payDetail[0]['pubdate'];
				$huoniaoTag->assign('state', $state);
				$huoniaoTag->assign('ordernum', $ordernum);
				$huoniaoTag->assign('date', $date);

				$furl = $_GET['furl'];
				$huoniaoTag->assign('furl', $furl);

			}
		}

	// 充值页面
	}elseif($action == "recharge"){
		$detailHandels = new handlers($service, "config");
		$detailConfig  = $detailHandels->getHandle();
		$config = $detailConfig['info'];

		$goldDeposit = $config['goldDeposit'];
		if(empty($goldDeposit)){
			$goldDeposit = array(6,300,680,1360,2040,2720);
		}else{
			$goldDeposit = explode(",", $goldDeposit);
		}
		$huoniaoTag->assign('goldDeposit', $goldDeposit);
		$huoniaoTag->assign('goldRatio', $config['goldRatio'] ? $config['goldRatio'] : 1);
	// 购买聊天钥匙
	}elseif($action == "buy_key"){
		$detailHandels = new handlers($service, "config");
		$detailConfig  = $detailHandels->getHandle();
		$config = $detailConfig['info'];
		$keyPrice = (int)$config['keyPrice'];

		// 根据等级配置计算会员送钥匙数量上下限
		if($loginMemberDetail['level'] == 0){
			$key_count = array();
			$dHandels = new handlers($service, "memberLevel");
			$dConfig  = $dHandels->getHandle(array("def" => 1));
			if(is_array($dConfig) && $dConfig['state'] == 100){
				$dConfig = $dConfig['info'];

				$key_ = array();
				$key_p = 0;
				foreach ($dConfig as $k => $v) {
					$privilege = unserialize($v['privilege']);
					if($v['id'] == 1){
						$key_count['def'] = $privilege['key'];
					}else{
						$key_[$k] = $privilege['key'];
					}
				}
				sort($key_);
				$key_count['min'] = $key_[0];
				$key_count['max'] = $key_[count($key_)-1];
			}
			$huoniaoTag->assign('key_count', $key_count);
		}

		$huoniaoTag->assign('keyPrice', $keyPrice);

	}elseif($action == "circle"){
		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['certifyState'] == 1 || $loginMemberDetail['level']){
			$huoniaoTag->assign('putAuth', 1);
		}
		$now = GetMktime(time());
		$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `visit_circle_date` = '$now' WHERE `id` = $uid");
		$dsql->dsqlOper($sql, "update");

	// 已收到的礼物状态设为已读
	}elseif($action == "myGift"){
		$sql = $dsql->SetQuery("UPDATE `#@__dating_gift_put` SET `read` = 1 WHERE `uto` = $uid");
		$dsql->dsqlOper($sql, "update");

		// 计算最多可提现金额
		$have = $loginMemberDetail['money'];
		if($have > 0){
			$goldRatio = $datingConfig['goldRatio'];
			$withdrawRatio = $datingConfig['withdrawRatio'];
			$money = sprintf("%.2f", $have / $goldRatio);
			$maxPutMoney = $money / (1 + $withdrawRatio / 100) ;
			$u_maxPutMoney = sprintf("%.2f", $maxPutMoney);
		}else{
			$u_maxPutMoney = 0;
		}
		$huoniaoTag->assign('u_maxPutMoney', $u_maxPutMoney);
	// Ta收到的礼物
	}elseif($action == "getGift"){
		$id = (int)$id;
		$ckHandels = new handlers($service, "getSysUid");
		$ckRes = $ckHandels->getHandle($id);
		if(is_array($ckRes) && $ckRes['info'] > 0){
			$huoniaoTag->assign('id', $id);
		}else{
			$param = array("service" => "dating");
			header("location:".getUrlPath($param));
			die;
		}
	}elseif($action == "news-list"){
		$typeid = (int)$typeid;
		$page = (int)$page;
		$page = $page == 0 ? 1 : $page;
		$huoniaoTag->assign('page', $page);

		$param = array("service" => "dating");

		if($typeid){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_schooltype` WHERE `id` = $typeid");
			$res = $dsql->dsqlOper($sql, "results");
			if($res){
				$typename = $res[0]['typename'];
				$huoniaoTag->assign('typeid', $typeid);
				$huoniaoTag->assign('typename', $typename);
			}else{
				$url = getUrlPath($param);
				header("location:".$url);
				die;
			}
		}else{
			$url = getUrlPath($param);
			header("location:".$url);
			die;
		}
	}elseif($action == "news-detail"){
		$id = (int)$id;
		$detailHandels = new handlers($service, "newsDetail");
		$detailConfig = $detailHandels->getHandle($id);
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig = $detailConfig['info'];
			foreach ($detailConfig as $key => $value) {
				$huoniaoTag->assign('detail_'.$key, $value);
			}
			$sql = $dsql->SetQuery("UPDATE `#@__dating_school` SET `click` = `click` + 1 WHERE `id` = $id");
			$dsql->dsqlOper($sql, "udpate");
		}else{
			$param = array("service" => "dating", "template" => "news");
			header("location:".getUrlPath($param));
			die;
		}
	}

	// if($action == "enter_hn" || $action == "hongniang" || $action == "hn_detail"){
	// 	if($hnUid){
	// 		$detailHandels = new handlers($service, "hnInfo");
	// 		$detailConfig  = $detailHandels->getHandle($hnUid);
	// 	}else{
	// 		$detailConfig = "";
	// 	}
	// 	if(is_array($detailConfig) && $detailConfig['state'] == 100){
	// 		foreach ($detailConfig['info'] as $key => $value) {
	// 			$huoniaoTag->assign('hn_'.$key, $value);
	// 		}
	// 	}
	// }

	if($action == "enter_store" || $action == "store" || $action == "store_detail"){
		if($storeUid){
			$detailHandels = new handlers($service, "storeInfo");
			$detailConfig  = $detailHandels->getHandle($storeUid);
		}else{
			$detailConfig = "";
		}
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			foreach ($detailConfig['info'] as $key => $value) {
				$huoniaoTag->assign('store_'.$key, $value);
			}
		}

	}

	// 会员中心-我的红娘
	if($action == "my_hn"){
		if(empty($hnUid)){
			$param = array(
				"service" => "dating",
				"template" => "enter_hn"
			);
			header("location:".getUrlPath($param));
			die;
		}
		// 我的牵线
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `company` = $hnUid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			global $arr_data;
			$arr_data = "";
			$uidList = arr_foreach($ret);
			$where1 = $where2 = "";
			$where1 .= " AND `uto` IN (".join(",", $uidList).")";
			$where2 .= " AND `ufrom` IN (".join(",", $uidList).")";
			// 新收到的
			$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE `state` = 1 AND `new1` = 0".$where1);
			$newLoadCount = $dsql->dsqlOper($sql, "totalCount");
			//新成功的牵线
			$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE `state` = 2 AND `new2` = 0".$where2);
			$newSuccCount = $dsql->dsqlOper($sql, "totalCount");
			//新失败的牵线
			$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE `state` = 3 AND `new3` = 0".$where2);
			$newFailCount = $dsql->dsqlOper($sql, "totalCount");

			$newLeadCount = $newLoadCount + $newSuccCount + $newFailCount;
			$huoniaoTag->assign('newLeadCount', $newLeadCount);

		}

		// 收到的申请
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_apply` WHERE `uto` = $hnUid AND `read` = 0");
		$newApplyCount = $dsql->dsqlOper($sql, "totalCount");
		$huoniaoTag->assign('newApplyCount', $newApplyCount);

		// 新的收入
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_money` WHERE `uid` = $hnUid AND `type` = 1 AND `extnew1` = 0");
		$newIncomeCount = $dsql->dsqlOper($sql, "totalCount");
		$huoniaoTag->assign('newIncomeCount', $newIncomeCount);


	// 会员中心-我的红娘相关页面
	}elseif($action == "my_user" || $action == "hn_income" || $action == "my_leadline_hn" || $action == "my_receiveapply"){
		$id = (int)$id;
		// 门店查看下属红娘信息
		if($id){
			if($id != $hnUid){
				if($storeUid){
					$sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__dating_member` WHERE `id` = $id AND `company` = $storeUid");
					$ret = $dsql->dsqlOper($sql, "results");
					if(!$ret){
						$param = array(
							"service" => "dating",
							"template" => "store_hn"
						);
						header("location:".getUrlPath($param));
						die;
					}else{
						$hnDetail = $ret[0];
					}
				}else{
					$id = 0;
				}
			}else{
				$id = 0;
			}
		}
		if(!$id) $hnDetail = $loginHnMemberDetail;

		if($hnDetail['state'] == 0){
			if(empty($smarty)){
				$param = array(
					"service" => "dating",
					"template" => "my_hn"
				);
				header("location:".getUrlPath($param));
				die;
			}
		}
		$huoniaoTag->assign("id", $id);
	}


	// 门店详情 申请门店服务
	if($action == "store_detail" || $action == "applyStore"){
		if($id == $storeUid){
			$ismine = 1;
			$detailConfig = array("state" => 100, "info" => $loginStoreMemberDetail);
		}else{
			$ismine = 0;
			$detailHandels = new handlers($service, "storeInfo");
			$detailConfig  = $detailHandels->getHandle($id);
		}
		$huoniaoTag->assign('id', $id);
		$huoniaoTag->assign('ismine', $ismine);

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){
				//输出详细信息
				// print_r($detailConfig);
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
			die;
		}

		if($action == "applyStore"){
			// 收入
			$money_data = array();
			$moduleHandels = new handlers("dating", "item");
			$moduleReturn  = $moduleHandels->getHandle(array("type" => 6, "son" => 0));
			if(is_array($moduleReturn) && $moduleReturn['state'] == 100){
				$money_data = $moduleReturn['info'];
			}
			$huoniaoTag->assign('money_data_json', json_encode($money_data));
		}

	}

	if($action == "hn_lead"){
		$id = (int)$id;
		if(empty($id)){
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
			die;
		}

		$sql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__dating_member` WHERE `id` = $id AND `type` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
			die;
		}else{
			$hnid = $ret[0]['company'];
		}

		$huoniaoTag->assign('id', $id);
		$huoniaoTag->assign('hnid', $hnid);

		// 收入
		$money_data = array();
		$moduleHandels = new handlers("dating", "item");
		$moduleReturn  = $moduleHandels->getHandle(array("type" => 6));
		if(is_array($moduleReturn) && $moduleReturn['state'] == 100){
			$money_data = $moduleReturn['info'];
		}
		$huoniaoTag->assign('money_data_json', json_encode($money_data));

	}
	// TA的关注和粉丝
	if($action == "fans"){
		$id = (int)$id;
		if($id){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `id` = $id AND `state` = 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
				die;
			}
			$type = (int)$type;
			$huoniaoTag->assign('id', $id);
			$huoniaoTag->assign('type', $type);
		}
	}

	// 我的人气
	if($action == "my_visit_record"){
		$type = (int)$type;
		$huoniaoTag->assign('type', $type);
	}
	if($action == "my_income"){
		$moduleHandels = new handlers('dating', "config");
		$moduleConfig  = $moduleHandels->getHandle();
		$moduleConfig = $moduleConfig['info'];

		$withdrawMinAmount = $moduleConfig['withdrawMinAmount'];

		$wxpayMinAmount = $withdrawMinAmount < 0.3 ? 0.3 : $withdrawMinAmount;
		$alipayMinAmount = $withdrawMinAmount < 0.1 ? 0.1 : $withdrawMinAmount;
		$huoniaoTag->assign('alipayMinAmount', $alipayMinAmount);
		$huoniaoTag->assign('wxpayMinAmount', $wxpayMinAmount);

	}

	if($action == "store_income_hn"){
		$id = (int)$id;
		$err = false;
		if(empty($id)){
			$err = true;

		}else{
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `company` = $storeUid AND `id` = $id AND `type` = 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){

				if($id == $hnUid){
					$user = $loginHnMemberDetail;
				}else{
					$dataHandels = new handlers($service, "hnInfo");
					$detail  = $dataHandels->getHandle($id);
					if($detail && is_array($detail) && $detail['state'] == 100){
						$user = $detail['info'];
					}
				}
				$huoniaoTag->assign('user', $user);
			}else{
				$err = true;
			}
		}

		if($err){
			$param = array(
				"service" => "dating",
				"template" => "store_income"
			);
			header("location:".getUrlPath($param));
			die;
		}
		$huoniaoTag->assign("id", $id);
	}

	if($action =="withdraw"){// 红娘现金提现
		global $userLogin;
		global $dsql;
		$userid = $userLogin->getMemberID();
		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		if(!empty($id)){
			$sql = $dsql->SetQuery("SELECT `bank`, `cardnum`, `cardname` FROM `#@__member_withdraw_card` WHERE `id` = '$id' AND `uid` = '$userid'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret && is_array($ret)){
				if($ret[0]['bank'] != 'alipay'){
					$bank = $ret[0];
					$bank['cardnumLast'] = substr($bank['cardnum'], -4);
				}else{
					$type = "alipay";
					$alipay = $ret[0];
				}
			}
		}else{
			//提取第一个帐号
			$sql = $dsql->SetQuery("SELECT `bank`, `cardnum`, `cardname` FROM `#@__member_withdraw_card` WHERE `uid` = '$userid' AND `bank` != 'alipay' ORDER BY `id` DESC LIMIT 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret && is_array($ret)){
				$bank = $ret[0];
				$bank['cardnumLast'] = substr($bank['cardnum'], -4);
			}
		}
		$huoniaoTag->assign("bank", $bank);

		$min = 0;
		$utype = (int)$utype;
		$param = array("utype" => $utype);
		$moduleHandels = new handlers('dating', "putForward");
		$moduleConfig  = $moduleHandels->getHandle($param);
		if(is_array($moduleConfig) && $moduleConfig['state'] == 100){
			$detail = $moduleConfig['info'];
		}else{
			echo '<script>alert("'.$moduleConfig['info'].'");window.history.go(-1);</script>';
			die;
		}

		if($detail['minPutMoney'] < $min){
			$detail['minPutMoney'] = $min;
		}

		$huoniaoTag->assign("detail", $detail);
		$huoniaoTag->assign("type", $type);
		$huoniaoTag->assign("utype", $utype);
		$huoniaoTag->assign("new", $new);

		if($url){
			$url = urldecode($url);
		}else{
			$param = array("service" => "member", "type" => "user", "template" => "put_forward", "param" => "module=dating&utype=0");
			$url = getUrlPath($param);
		}
		$huoniaoTag->assign("url", $url);
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

			// 没有数据时直接返回会出错？
            if(is_array($moduleReturn) && $moduleReturn['state'] == 100){
                $moduleReturn  = $moduleReturn['info'];  //返回数据
                $pageInfo_ = $moduleReturn['pageInfo'];
                if($pageInfo_){
                    //如果有分页数据则提取list键
                    $moduleReturn  = $moduleReturn['list'];
                    //把pageInfo定义为global变量
                    global $pageInfo;
                    $pageInfo = $pageInfo_;
                    $smarty->assign('pageInfo', $pageInfo);
                }

                $smarty->block_data[$dataindex] = $moduleReturn;  //存储数据
            }else{
                global $pageInfo;
                $pageInfo = array(
                    "totalCount" => 0
                );
                $smarty->assign('pageInfo', $pageInfo);
            }


		}

	}

	//果没有数据，直接返回null,不必再执行了
	if(!$smarty->block_data[$dataindex]) {
		$repeat = false;
		return '';
	}

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
