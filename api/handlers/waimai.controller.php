<?php

/**
 * huoniaoTag模板标签函数插件-外卖模块
 *
 * @param $params array 参数集
 * @return array
 */
function waimai($params, $content = "", &$smarty = array(), &$repeat = array()){
	$service = "waimai";
	extract($params);
	global $template;
	if(empty($action)) return '';

	global $huoniaoTag;
	global $dsql;
	global $userLogin;
	global $cfg_secureAccess;
	global $cfg_basehost;
	global $do;

	$userid = $userLogin->getMemberID();
	// echo $userid;
	// die;
	$furl = urlencode(''.$cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

	//配送版登录
	if($do == "courier" && $action == "login"){
		if(checkCourierAccount() > -1){
			header("location:/?service=waimai&do=courier&template=index");
			die;
		}
		return;
	}


	//配送版退出
	if($do == "courier" && $action == "logout"){
		DropCookie("courier");
		header("location:/?service=waimai&do=courier&template=login");
		die;
	}


	//配送员订单详情
	if($do == "courier" && $action == "detail"){

		$id = (int)$id;
		$did = checkCourierAccount();

		if($did == -1){
			header("location:/?service=waimai&do=courier&template=login");
			die;
		}

		$ordertype = empty($ordertype) || $ordertype != "paotui" ? "waimai" : "paotui";

		//获取订单信息
		if($ordertype == "paotui"){
			$detailHandels = new handlers($service, "orderPaotuiDetail");
		}else{
			$detailHandels = new handlers($service, "orderDetail");
		}
		$detailConfig  = $detailHandels->getHandle(array("id" => $id));


		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				if($detailConfig['peisongid'] && $detailConfig['peisongid'] != $did){
					header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
				}

				//更新订单信息的推送状态为已查看
			  $sql = $dsql->SetQuery("UPDATE `#@__".$ordertype."_order` SET `courier_pushed` = 1 WHERE `id` = $id");
			  $ret = $dsql->dsqlOper($sql, "update");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

			}else{
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=2");
			}



		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=3");
		}

		$huoniaoTag->assign("ordertype", $ordertype);

		return;

	}

	//首页
	if($action == "index" || ($action == "comment" AND $do == "courier") || $action == "statistics" || $action == "statisticsHistory"){

		if($action == "index"){
			$local = empty($local) ? "auto" : $local;
			$huoniaoTag->assign("local", $local);
		}

		$ordertype = empty($ordertype) ? "waimai" : $ordertype;

		//配送版需要验证是否登录
		if($do == "courier"){

			$userid = checkCourierAccount();

			if($userid == -1){
				header("location:/?service=waimai&do=courier&template=login");
				die;
			}
			$state = $_GET['state'] ? $_GET['state'] : "3";
			$huoniaoTag->assign("state", $state);
			$huoniaoTag->assign("userid", $userid);
			$huoniaoTag->assign("ordertype", $ordertype);

			$sql = $dsql->SetQuery("SELECT `state` FROM `#@__waimai_courier` WHERE `id` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$huoniaoTag->assign("courier_state", $ret[0]['state']);
			}

			//统计
			if($action == "statistics"){

				$stime = GetMkTime(date("Y-m-d") . " 00:00:00");
				$etime = GetMkTime(date("Y-m-d") . " 23:59:59");

				//成功、收入
				$sql = $dsql->SetQuery("SELECT count(`id`) total, sum(`amount`) amount FROM `#@__waimai_order` WHERE `peisongid` = $userid AND `state` = 1 AND `pubdate` >= $stime AND `pubdate` <= $etime");
				$ret = $dsql->dsqlOper($sql, "results");
				$success = $ret[0]['total'];
				$amount = $ret[0]['amount'];

				//失败
				$sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE `peisongid` = $userid AND (`state` = 6 OR `state` = 7) AND `pubdate` >= $stime AND `pubdate` <= $etime");
				$ret = $dsql->dsqlOper($sql, "results");
				$failed = $ret[0]['total'];

				//配送费
				// $peisong = 0;
				// $sql = $dsql->SetQuery("SELECT `priceinfo` FROM `#@__waimai_order` WHERE `peisongid` = $userid AND `state` = 1 AND `pubdate` >= $stime AND `pubdate` <= $etime");
				// $ret = $dsql->dsqlOper($sql, "results");
				// if($ret){
				// 	foreach ($ret as $key => $value) {
				// 		$priceinfo = unserialize($value['priceinfo']);
				// 		foreach ($priceinfo as $k_ => $v_) {
				// 			if($v_['type'] == "peisong"){
				// 				$peisong += $v_['amount'];
				// 			}
				// 		}
				// 	}
				// }

				//收款（货到付款）
				$sql = $dsql->SetQuery("SELECT sum(`amount`) amount FROM `#@__waimai_order` WHERE `paytype` = 'delivery' AND `peisongid` = $userid AND `state` = 1 AND `pubdate` >= $stime AND `pubdate` <= $etime");
				$ret = $dsql->dsqlOper($sql, "results");
				$peisong = $ret[0]['amount'];

				$huoniaoTag->assign('success', (int)$success);
				$huoniaoTag->assign('amount', sprintf("%.2f", $amount));
				$huoniaoTag->assign('failed', (int)$failed);
				$huoniaoTag->assign('peisong', sprintf("%.2f", $peisong));

				// 跑腿统计
				//成功、收入
				$sql = $dsql->SetQuery("SELECT count(`id`) total, sum(`amount`) amount FROM `#@__paotui_order` WHERE `peisongid` = $userid AND `state` = 1 AND `pubdate` >= $stime AND `pubdate` <= $etime");
				$ret = $dsql->dsqlOper($sql, "results");
				$paotui_success = $ret[0]['total'];
				$paotui_amount = $ret[0]['amount'];

				//失败
				$sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__paotui_order` WHERE `peisongid` = $userid AND (`state` = 6 OR `state` = 7) AND `pubdate` >= $stime AND `pubdate` <= $etime");
				$ret = $dsql->dsqlOper($sql, "results");
				$paotui_failed = $ret[0]['total'];

				$huoniaoTag->assign('paotui_success', (int)$paotui_success);
				$huoniaoTag->assign('paotui_amount', sprintf("%.2f", $paotui_amount));
				$huoniaoTag->assign('paotui_failed', (int)$paotui_failed);

			}

			//统计历史
			if($action == "statisticsHistory"){

				if(empty($ordertype) || $ordertype != "paotui"){
					$dbname = "waimai_order";
				}else{
					$dbname = "paotui_order";
				}

			    $stime_ = strtotime($stime);
			    $etime_ = strtotime($etime);

	            //$sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__$dbname` WHERE `state` = 1 AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $totalSuccess = $dsql->dsqlOper($sql, "results");

	            //$sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__waimai_order` WHERE (`state` = 6 OR `state` = 7) AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__$dbname` WHERE (`state` = 6 OR `state` = 7) AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $totalFailed = $dsql->dsqlOper($sql, "results");

	            //$sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__$dbname` WHERE `state` = 1 AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $success = $dsql->dsqlOper($sql, "results");

	            //$sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `paytype` = 'delivery' AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__$dbname` WHERE `state` = 1 AND `paytype` = 'delivery' AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $delivery = $dsql->dsqlOper($sql, "results");

	            //$sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND `paytype` = 'money' AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__$dbname` WHERE `state` = 1 AND `paytype` = 'money' AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $money = $dsql->dsqlOper($sql, "results");

	            //$sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__waimai_order` WHERE `state` = 1 AND (`paytype` = 'wxpay' OR `paytype` = 'alipay') AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__$dbname` WHERE `state` = 1 AND (`paytype` = 'wxpay' OR `paytype` = 'alipay') AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            $online = $dsql->dsqlOper($sql, "results");

	            $peisong = $fuwu = 0;
	            //$sql = $dsql->SetQuery("SELECT `priceinfo` FROM `#@__waimai_order` WHERE `state` = 1 AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
	            if($dbname == "waimai_order"){
		            $sql = $dsql->SetQuery("SELECT `priceinfo` FROM `#@__$dbname` WHERE `state` = 1 AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
		            $ret = $dsql->dsqlOper($sql, "results");
		            if($ret){
		                foreach ($ret as $k => $v) {
		                    $priceinfo = empty($v['priceinfo']) ? array() : unserialize($v['priceinfo']);
		                    foreach ($priceinfo as $k_ => $v_) {
		                        if($v_['type'] == "peisong"){
		                            $peisong += $v_['amount'];
		                        }
		                        if($v_['type'] == "fuwu"){
		                            $fuwu += $v_['amount'];
		                        }
		                    }
		                }
		            }
		        }else{
		        	$sql = $dsql->SetQuery("SELECT SUM(`freight`) total FROM `#@__$dbname` WHERE `state` = 1 AND `pubdate` >= $stime_ AND `pubdate` <= $etime_ AND `peisongid` = " . $userid);
		        	$peisong = $dsql->dsqlOper($sql, "results");
		        	$peisong = $peisong[0]['total'];
		        }

				$huoniaoTag->assign('stime', $stime);
				$huoniaoTag->assign('etime', $etime);
				$huoniaoTag->assign('totalSuccess', (int)$totalSuccess[0]['total']);
				$huoniaoTag->assign('totalFailed', (int)$totalFailed[0]['total']);
				$huoniaoTag->assign('success', sprintf("%.2f", $success[0]['total']));
				$huoniaoTag->assign('delivery', sprintf("%.2f", $delivery[0]['total']));
				$huoniaoTag->assign('money', sprintf("%.2f", $money[0]['total']));
				$huoniaoTag->assign('online', sprintf("%.2f", $online[0]['total']));
				$huoniaoTag->assign('peisong', sprintf("%.2f", $peisong));
				$huoniaoTag->assign('fuwu', sprintf("%.2f", $fuwu));

			}

			// $arc = $dsql->SetQuery("SELECT avg(`starps`) r FROM `#@__waimai_common` WHERE `peisongid` = $userid");
			$arc = $dsql->SetQuery("SELECT avg(`starps`) r FROM `#@__public_comment` WHERE `peisongid` = '$userid'");
			$ret = $dsql->dsqlOper($arc, "results");
			if($ret){
				$rating = $ret[0]['r'];		//总评分
				$star = number_format($rating, 1);
			}else{
				$star = 0;
			}
			$huoniaoTag->assign("courier_star", $star);

			$huoniaoTag->assign("ordertype", $ordertype);

		}else{

			//店铺分类
			$typeArr = array();
		    $sql = $dsql->SetQuery("SELECT * FROM `#@__waimai_shop_type` ORDER BY `sort` DESC, `id` DESC");
		    $ret = $dsql->dsqlOper($sql, "results");
		    if($ret){
		        foreach ($ret as $key => $value) {
		            $typeArr[$key]['id'] = $value['id'];
		            $typeArr[$key]['title'] = $value['title'];
		        }
		    }
		    $huoniaoTag->assign('typeArr', $typeArr);

			$sql = $dsql->SetQuery("SELECT `title`, `description`, `tel`, `share_pic`, `index_banner`, `tubiao_nav`, `ad1`, `huodong_nav`, `shop` FROM `#@__waimai_system` LIMIT 0, 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$data = $ret[0];

				$huoniaoTag->assign('title', $data['title']);
				$huoniaoTag->assign('description', $data['description']);
				$huoniaoTag->assign('tel', $data['tel']);
				$huoniaoTag->assign('share_pic', $data['share_pic'] ? getFilePath($data['share_pic']) : '');
				$huoniaoTag->assign('shop', $data['shop']);

				$bannerArr = array();
				$banner = $data['index_banner'];
				if(!empty($banner)){
					$banner = explode(",", $banner);
					foreach($banner as $key => $val){
						$info = explode("##", $val);
						array_push($bannerArr, array(
							"pic"   => getFilePath($info[0]),
							"title" => $info[1],
							"link"  => $info[2]
						));
					}
				}
				$huoniaoTag->assign('banner', $bannerArr);

				$tubiaoArr = array();
				$tubiao = $data['tubiao_nav'];
				if(!empty($tubiao)){
					$tubiao = explode(",", $tubiao);
					foreach($tubiao as $key => $val){
						$info = explode("##", $val);
						array_push($tubiaoArr, array(
							"pic"   => getFilePath($info[0]),
							"title" => $info[1],
							"link"  => $info[2]
						));
					}
				}
				$huoniaoTag->assign('tubiao', $tubiaoArr);

				$ad1Arr = array();
				$ad1 = $data['ad1'];
				if(!empty($ad1)){
					$ad1 = explode(",", $ad1);
					foreach($ad1 as $key => $val){
						$info = explode("##", $val);
						array_push($ad1Arr, array(
							"pic"   => getFilePath($info[0]),
							"title" => $info[1],
							"link"  => $info[2]
						));
					}
				}
				$huoniaoTag->assign('ad1', $ad1Arr);

				$huodongArr = array();
				$huodong = $data['huodong_nav'];
				if(!empty($huodong)){
					$huodong = explode(",", $huodong);
					foreach($huodong as $key => $val){
						$info = explode("##", $val);
						array_push($huodongArr, array(
							"pic"   => getFilePath($info[0]),
							"title" => $info[1],
							"link"  => $info[2],
							"desc"  => $info[3]
						));
					}
				}
				$huoniaoTag->assign('huodong', $huodongArr);
			}

		}


	//店铺列表
	}elseif($action == "list" || $action == "slist"){

		$typeArr = array();
	    $sql = $dsql->SetQuery("SELECT * FROM `#@__waimai_shop_type` ORDER BY `sort` DESC, `id` DESC");
	    $ret = $dsql->dsqlOper($sql, "results");
	    if($ret){
	        foreach ($ret as $key => $value) {
	            $typeArr[$key]['id'] = $value['id'];
	            $typeArr[$key]['title'] = $value['title'];
	        }
	    }
	    $huoniaoTag->assign('typeArr', $typeArr);

		$typeid = (int)$typeid;
		$typename = "全部分类";
		if($typeid){
			$sql = $dsql->SetQuery("SELECT `title` FROM `#@__waimai_shop_type` WHERE `id` = $typeid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$typename = $ret[0]['title'];
			}
		}
	    $huoniaoTag->assign('typeid', $typeid);
	    $huoniaoTag->assign('typename', $typename);

	    if($action == "slist"){
		    $huoniaoTag->assign('keywords', $keywords);
		}


	//商铺详细
	}elseif($action == "shop" || $action == "buy" || $action == "detail" || $action == "info" || $action == "range" || $action == "photo" || $action == "cart" || $action == "confirm" || $action == "address"){

		$totalPrice = 0;
		$cartArr = array();
		$huoniaoTag->assign("ispay", $ispay);
		$huoniaoTag->assign("addressid", $addressid);

		//验证是否已经登录
		if(($action == "confirm" || $action == "cart" || $action == "address") && $userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		//验证是否为首单
		include(HUONIAOROOT . "/include/config/waimai.inc.php");

		$where = $custom_firstOrderType == 0 ? "`uid` = $userid AND `state` != 0" : "`uid` = $userid AND `sid` = $id AND `state` != 0";

		$firstOrder = 1;
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__waimai_order` WHERE ".$where);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$firstOrder = 0;
		}
		$huoniaoTag->assign("firstOrder", $firstOrder);

		// 购物车页面读取购物车信息
		$needInsertTab = false;
		if($action == "cart"){

			$tsql = $dsql->SetQuery("SELECT * FROM `#@__waimai_order_temp` WHERE `sid` = $id AND `uid` = $userid");
			$tret = $dsql->dsqlOper($tsql, "results");
			if($tret){
				$info = $tret[0];
				$paytype = $info['paytype'];
				$note = $info['note'];
				$paypwd = $info['paypwd'];
				$quan = $quan != '' ? $quan : $info['quanid'];
				$presetData = json_decode($info['preset'], true);

				$huoniaoTag->assign("paypwd", $paypwd);
				$huoniaoTag->assign("presetData", $presetData);
			}

		}


		//获取用户的第一条地址信息
		$juli = $address_id = $address_lng = $address_lat = 0;
		$address_person = $address_tel = $address_street = $address_address = "";

		/*if(empty($address)){
			$address = GetCookie("waimai_address");
		}*/
		if($address){

			// PutCookie("waimai_address", $address);

			$sql = $dsql->SetQuery("SELECT * FROM `#@__waimai_address` WHERE `uid` = $userid AND `id` = $address");
			$ret = $dsql->dsqlOper($sql, "results");
		}else{
			$sql = $dsql->SetQuery("SELECT * FROM `#@__waimai_address` WHERE `uid` = $userid ORDER BY `id`");
			$ret = $dsql->dsqlOper($sql, "results");

			if($ret){
				// 读取用户位置信息,取距离最近的地址
				$userAddr = GetCookie("waimai_useraddr");
				if($userAddr){
					$userAddr = explode(",", $userAddr);
					$mid = 0;
					$juliInit = 9999999999999999;
					$ret_ = $ret;
					foreach ($ret_ as $key => $value) {
						$juli_ = getDistance($value['lng'], $value['lat'], $userAddr[1], $userAddr[0]);
						if($juli_ < $juliInit){
							$juliInit = $juli_;
							$ret = array($value);
							$address = $value['id'];
						}
					}
				}
			}

		}

		$huoniaoTag->assign("note", $note);
		$huoniaoTag->assign("paytype", $paytype);
		$huoniaoTag->assign("quanid", (int)$quan);

		if($ret){
			$data = $ret[0];
			$address_id      = $data['id'];
			$address_person  = $data['person'];
			$address_tel     = $data['tel'];
			$address_street  = $data['street'];
			$address_address = $data['address'];
			$address_lng     = $data['lng'];
			$address_lat     = $data['lat'];
		}
		$huoniaoTag->assign("cart_address_id", $address_id);
		$huoniaoTag->assign("cart_address_person", $address_person);
		$huoniaoTag->assign("cart_address_tel", $address_tel);
		$huoniaoTag->assign("cart_address_street", $address_street);
		$huoniaoTag->assign("cart_address_address", $address_address);

		if($action == "address"){
			$huoniaoTag->assign("address", (int)$address);
		}
		if($action == "quan"){
			$huoniaoTag->assign("quan", (int)$quan);
		}



		//验证是否已经收藏
		$params = array(
			"module" => "waimai",
			"temp"   => "shop",
			"type"   => "add",
			"id"     => $id,
			"check"  => 1
		);
		$collect = checkIsCollect($params);
		$collect = $collect == "has" ? 1 : 0;
		$huoniaoTag->assign('collect', $collect);



		//获取店铺信息
		$detailHandels = new handlers($service, "storeDetail");
		$detailConfig  = $detailHandels->getHandle($id);

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				detailCheckCity($service, $detailConfig['id'], $detailConfig['cityid'], $action);

				//计算送餐地址与店铺间的距离
				if($address_id && $action == "cart"){
					$juli = getDistance($detailConfig['coordX'], $detailConfig['coordY'], $address_lat, $address_lng)/1000;

					//根据区域计算起送价和配送费
					if($detailConfig['delivery_fee_mode'] == 2){

						$prices = array();

						//验证送货地址是否在商家的服务区域
						$service_area_data = $detailConfig['service_area_data'];
						if($service_area_data){
							foreach ($service_area_data as $key => $value) {
								$qi     = $value['qisong'];
								$pei    = $value['peisong'];
								$points = $value['points'];

								$pointsArr = array();
								if(!empty($points)){
									$points = explode("|", $points);
									foreach ($points as $k => $v) {
										$po = explode(",", $v);
										array_push($pointsArr, array("lng" => $po[0], "lat" => $po[1]));
									}

									if(is_point_in_polygon(array("lng" => $address_lng, "lat" => $address_lat), $pointsArr)){
										array_push($prices, array("qisong" => $qi, "peisong" => $pei));
									}
								}

							}

						}

						//如果送货地址在服务区域，则将起送价和配送费更改为按区域的价格
						if($prices){
							$detailConfig['basicprice'] = $prices[0]['qisong'];
							$detailConfig['delivery_fee'] = $prices[0]['peisong'];

						//如果不在服务区域，提醒用户
						}else{
							$detailConfig['delivery_radius'] = 0.0000001;
						}

					}

				}

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

				$huoniaoTag->assign("juli", (float)$juli);


				//如果是商品详细页
				if($action == "detail"){
					//获取店铺信息
					$detailHandels = new handlers($service, "menuDetail");
					$detailConfig  = $detailHandels->getHandle($fid);

					if(is_array($detailConfig) && $detailConfig['state'] == 100){
						$detailConfig  = $detailConfig['info'];
						if(is_array($detailConfig)){
							//输出详细信息
							foreach ($detailConfig as $key => $value) {
								$huoniaoTag->assign('food_'.$key, $value);
			}
						}
					}
				}

			}



		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}

		return;


	//获取指定ID的商铺详细
	}elseif($action == "storeDetail"){

		$detailHandels = new handlers($service, "storeDetail");
		$detailConfig  = $detailHandels->getHandle($id);
		$state = 0;

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
				$state = 1;

			}
		}
		$huoniaoTag->assign('storeState', $state);
		return;

	//获取指定ID的商铺评分
	}elseif($action == "comment"){

		if(empty($id) || !is_numeric($id)){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
			die;
		}

		$detailHandels = new handlers($service, "storeDetailStar");
		$detailConfig  = $detailHandels->getHandle($id);
		$state = 0;

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
				$state = 1;

			}
		}

		// 店铺名
		$sql = $dsql->SetQuery("SELECT `shopname` FROM `#@__waimai_shop` WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$shopname = $ret[0]['shopname'];
		}

		//验证是否已经收藏
		$params = array(
			"module" => "waimai",
			"temp"   => "shop",
			"type"   => "add",
			"id"     => $id,
			"check"  => 1
		);
		$collect = checkIsCollect($params);
		$collect = $collect == "has" ? 1 : 0;

		$huoniaoTag->assign('id', $id);
		$huoniaoTag->assign('storeState', $state);
		$huoniaoTag->assign('shopname', $shopname);
		$huoniaoTag->assign('collect', $collect);


		return;

	//订单支付
	}elseif($action == "pay"){

		global $userLogin;
		global $cfg_secureAccess;
		$uid = $userLogin->getMemberID();

		if($uid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		$ordertype = empty($ordertype) || $ordertype != "paotui" ? "waimai" : "paotui";

		if($ordertype == "waimai"){

			//查询订单信息
			$sql = $dsql->SetQuery("SELECT `id`, `sid`, `amount` FROM `#@__waimai_order` WHERE `ordernum` = '$ordernum' AND `state` = 0 AND `uid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){

				$data   = $ret[0];
				$sid    = $data['sid'];
				$amount = $data['amount'];

				$delivery = 0;
				$bsql = $dsql->SetQuery("SELECT `paytype`, `offline_limit`, `pay_offline_limit` FROM `#@__waimai_shop` WHERE `id` = $sid");
				$bret = $dsql->dsqlOper($bsql, "results");
				if($bret){
					$bret = $bret[0];

					$delivery = 1;
					if(strstr($bret['paytype'], "1") === false || ($bret['offline_limit'] && $amount < $bret['pay_offline_limit'])){
						$delivery = 0;
					}
				}
				$huoniaoTag->assign("delivery", $delivery);

				$huoniaoTag->assign("ordernum", $ordernum);
				$huoniaoTag->assign("totalAmount", $amount);

			}else{
				$param = array(
					"service"  => "member",
					"type"     => "user",
					"template" => "order-waimai"
				);
				$url = getUrlPath($param);
				header("location:".$url);
			}

		}else{

			//查询订单信息
			$sql = $dsql->SetQuery("SELECT `id`, `amount` FROM `#@__paotui_order` WHERE `ordernum` = '$ordernum' AND `state` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){

				$data = $ret[0];
				$amount = $data['amount'];

				$huoniaoTag->assign("ordernum", $ordernum);
				$huoniaoTag->assign("totalAmount", $amount);

			}else{
				$param = array(
					"service"  => "member",
					"type"     => "user",
					"template" => "order-paotui"
				);
				$url = getUrlPath($param);
				header("location:".$url);
			}

		}

		$huoniaoTag->assign('ordertype', $ordertype);
		$huoniaoTag->assign('allowUsePoint', false);


	//支付结果页面
	}elseif($action == "payreturn"){

		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		$param = array(
			"service"  => "member",
			"type"     => "user",
			"template" => "order",
			"module"   => "waimai"
		);
		$url = getUrlPath($param);
		if(!empty($ordernum)){

			//根据支付订单号查询支付结果
			$archives = $dsql->SetQuery("SELECT `body`, `amount`, `paytype`, `state` FROM `#@__pay_log` WHERE `ordertype` = 'waimai' AND `ordernum` = '$ordernum' AND `uid` = $userid");
			$payDetail  = $dsql->dsqlOper($archives, "results");

			if($payDetail){

				$ordertype = $payDetail[0]['ordertype'];

				$state    = $payDetail[0]['state'];
				$paytype  = $payDetail[0]['paytype'];
				$body     = $payDetail[0]['body'];

				$body = unserialize($body);
				$type = $body['type'];

				$huoniaoTag->assign('state', $state);
				$huoniaoTag->assign('paytype', $paytype);
				$huoniaoTag->assign('ordernum', $ordernum);

				// 外卖
				if($type == 'waimai'){

					$sql = $dsql->SetQuery("SELECT `id`, `sid` FROM `#@__waimai_order` WHERE `ordernum` = '$ordernum'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$huoniaoTag->assign('orderid', $ret[0]['id']);
						$huoniaoTag->assign('sid',   $ret[0]['sid']);
					}else{
						header("location:".$url);
						die;
					}

				// 跑腿
				}elseif($type == 'paotui'){

					$sql = $dsql->SetQuery("SELECT `id`, `shop` FROM `#@__paotui_order` WHERE `ordernum` = '$ordernum'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$huoniaoTag->assign('orderid', $ret[0]['id']);
						$huoniaoTag->assign('shop',   $ret[0]['shop']);
					}else{
						header("location:".$url);
						die;
					}

				}

				$huoniaoTag->assign('ordertype', $type);



			//支付订单不存在
			}else{
				header("location:".$url);
			}

		}else{
			header("location:".$url);
		}

	//订单列表
	}elseif($action == "orderlist"){

		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		$page = (int)$page;
		$huoniaoTag->assign("page", $page);
		$huoniaoTag->assign("state", $state);
		$huoniaoTag->assign("userid", $userid);

	//订单详细
	}elseif($action == "orderdetail"){

		if(empty($id) || !is_numeric($id)){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
			die;
		}

		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		// $sql = $dsql->SetQuery("SELECT `ordernum`, `store`, `paytype`, `price`, `offer`, `peisong`, `people`, `contact`, `address`, `note`, `orderdate`, `state`, `paydate`, `songdate`, `confirm`, `peisong_note` FROM `#@__waimai_order` WHERE `id` = $id AND `userid` = $userid");
		// $ret = $dsql->dsqlOper($sql, "results");
		// if($ret){
		//
		// 	$order = $ret[0];
		// 	$huoniaoTag->assign("order_id", $id);
		// 	$huoniaoTag->assign("order_ordernum", $order['ordernum']);
		// 	$huoniaoTag->assign("order_store", $order['store']);
		// 	$huoniaoTag->assign("order_paytype", $order['paytype']);
		// 	$huoniaoTag->assign("order_price", $order['price']);
		// 	$huoniaoTag->assign("order_offer", $order['offer']);
		// 	$huoniaoTag->assign("order_peisong", $order['peisong']);
		// 	$huoniaoTag->assign("order_people", $order['people']);
		// 	$huoniaoTag->assign("order_contact", $order['contact']);
		// 	$huoniaoTag->assign("order_address", $order['address']);
		// 	$huoniaoTag->assign("order_note", $order['note']);
		// 	$huoniaoTag->assign("order_orderdate", $order['orderdate']);
		// 	$huoniaoTag->assign("order_state", $order['state']);
		// 	$huoniaoTag->assign("order_paydate", $order['paydate']);
		// 	$huoniaoTag->assign("order_songdate", $order['songdate']);
		// 	$huoniaoTag->assign("order_confirm", $order['confirm']);
		// 	$huoniaoTag->assign("order_peisong_note", $order['peisong_note']);
		//
		// 	$paytype = $order["paytype"];
		// 	if(!$paytype){
		// 		$huoniaoTag->assign("order_paytype", "未知");
		// 	}else{
		// 		//主表信息
		// 		$sql = $dsql->SetQuery("SELECT `pay_name` FROM `#@__site_payment` WHERE `pay_code` = '$paytype'");
		// 		$ret = $dsql->dsqlOper($sql, "results");
		// 		if(!empty($ret)){
		// 			$huoniaoTag->assign("order_paytype", $ret[0]['pay_name']);
		// 		}else{
		// 			$huoniaoTag->assign("order_paytype", $order["paytype"]);
		// 		}
		// 	}
		//
		// 	$menus = array();
		// 	$sql = $dsql->SetQuery("SELECT `pid`, `pname`, `price`, `count` FROM `#@__waimai_order_product` WHERE `orderid` = $id");
		// 	$ret = $dsql->dsqlOper($sql, "results");
		// 	if($ret){
		// 		foreach ($ret as $key => $value) {
		// 			array_push($menus, array(
		// 				"pid" => $value['pid'],
		// 				"pname" => $value['pname'],
		// 				"price" => $value['price'],
		// 				"count" => $value['count']
		// 			));
		// 		}
		// 	}
		// 	$huoniaoTag->assign("order_menus", $menus);
		//
		// 	//商家信息
		// 	$sql = $dsql->SetQuery("SELECT `title`, `addr`, `address`, `tel` FROM `#@__waimai_store` WHERE `id` = ".$order['store']);
		// 	$ret = $dsql->dsqlOper($sql, "results");
		// 	if($ret){
		// 		$data = $ret[0];
		// 		$huoniaoTag->assign("store_title", $data['title']);
		// 		$huoniaoTag->assign("store_address", $data['address']);
		// 		$huoniaoTag->assign("store_tel", $data['tel']);
		//
		// 		//区域
		// 		$addrSql = $dsql->SetQuery("SELECT `typename` FROM `#@__waimai_addr` WHERE `id` = ". $data["addr"]);
		// 		$addrname = $dsql->getTypeName($addrSql);
		// 		$huoniaoTag->assign("store_addr", $addrname[0]['typename']);
		// 	}
		//
		// }else{
		// 	header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
		// 	die;
		// }



	//发布菜单
	}elseif($action == "fabu"){

		//输出分类字段内容
		global $userLogin;
		$userid = $userLogin->getMemberID();

		global $detailArr;

		if($userid != -1 || $detailArr){

			$store = 0;
			$parentTypeid = 0;
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__waimai_store` WHERE `userid` = ".$userid);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$storeid = $ret[0]['id'];
			}

			//修改信息
			if($id){

				$detailHandels = new handlers($service, "menuDetail");
				$detailConfig  = $detailHandels->getHandle($id);

				if(is_array($detailConfig) && $detailConfig['state'] == 100){
					$detailConfig  = $detailConfig['info'];
					if(is_array($detailConfig)){

						if($storeid != $detailConfig['store']){
							header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
							die;
						}
						foreach ($detailConfig as $key => $value) {
							$huoniaoTag->assign('detail_'.$key, $value);
						}
					}
				}else{
					header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
					die;
				}

			}

			$huoniaoTag->assign('storeid', $storeid);

		}


	// --------------------------------跑腿
	// 帮我买详情页
	}elseif($action == 'paotui' || $action == "paotui-buy" || $action == "addressPaotui" ){

        $inc = HUONIAOINC . "/config/waimai.inc.php";
        include $inc;
        global $serviceMoney;
        $huoniaoTag->assign('serviceMoney', $serviceMoney ? $serviceMoney : '5');

		$huoniaoTag->assign('city', '');
		$huoniaoTag->assign('district', '');

		//验证是否已经登录
		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}


		if($action == 'paotui' || $action == 'paotui-buy'){
			// 判断是否在营业时间
			$began = date("Y-m-d") . " 08:30";
			$end = date("Y-m-d") . " 23:59";
			$begantime = strtotime($began);
			$endtime = strtotime($end);
			$now = time();

			$yingyeState = 1;
			if($now < $begantime){
				$yingyeState = -1;
			}

			$huoniaoTag->assign('yingyeState', $yingyeState);
			$huoniaoTag->assign('begantime', explode(" ", $began)[1]);
			$huoniaoTag->assign('endtime', explode(" ", $end)[1]);

			if($action == 'paotui'){
				return;
			}

		}

		$huoniaoTag->assign('shop', $shop);

		$frompage = $frompage ? $frompage : "paotui-buy";
		$huoniaoTag->assign('frompage', $frompage);


		//获取用户的第一条地址信息
		$address_id = $address_lng = $address_lat = 0;
		$address_person = $address_tel = $address_street = $address_address = "";
		if($address){
			$sql = $dsql->SetQuery("SELECT * FROM `#@__waimai_address` WHERE `uid` = $userid AND `id` = $address");
		}else{
			$sql = $dsql->SetQuery("SELECT * FROM `#@__waimai_address` WHERE `uid` = $userid ORDER BY `id` DESC LIMIT 0, 1");
		}

		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$data = $ret[0];
			$address_id      = $data['id'];
			$address_person  = $data['person'];
			$address_tel     = $data['tel'];
			$address_street  = $data['street'];
			$address_address = $data['address'];
			$address_lng     = $data['lng'];
			$address_lat     = $data['lat'];
		}
		$huoniaoTag->assign("cart_address_id", $address_id);
		$huoniaoTag->assign("cart_address_person", $address_person);
		$huoniaoTag->assign("cart_address_tel", $address_tel);
		$huoniaoTag->assign("cart_address_street", $address_street);
		$huoniaoTag->assign("cart_address_address", $address_address);

		$huoniaoTag->assign("cart_address_lng", $address_lng);
		$huoniaoTag->assign("cart_address_lat", $address_lat);


		if($action == "addressPaotui"){
			$huoniaoTag->assign("address", (int)$address);
		}

		if($action == "paotui-buy"){
			$huoniaoTag->assign("addr", $addr);
			$huoniaoTag->assign("lng", $lng);
			$huoniaoTag->assign("lat", $lat);
		}

		if($action == "paotui-song"){
			if(empty($shop) || empty($weight) || empty($price)){
				$param = array(
					"service"  => "waimai",
					"template" => "paotui"
				);
				$url = getUrlPath($param);
				header("location:".$url);
				die;
			}
		}
		$huoniaoTag->assign("weight", $weight);
		$huoniaoTag->assign("price", $price);


	}elseif($action == "paotui-song"){

        $inc = HUONIAOINC . "/config/waimai.inc.php";
        include $inc;
        global $serviceMoney;
        $huoniaoTag->assign('serviceMoney', $serviceMoney ? $serviceMoney : '5');

		if(empty($shop)){
			$param = array(
				"service" => 'waimai',
				"template" => 'paotui'
			);

			$url = getUrlPath($param);
			header("location:$url");
			return;
		}

		$huoniaoTag->assign('city', '');
		$huoniaoTag->assign('district', '');

		//验证是否已经登录
		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		// 判断是否在营业时间
		$began = date("Y-m-d") . " 08:30";
		$end = date("Y-m-d") . " 23:59";
		$begantime = strtotime($began);
		$endtime = strtotime($end);
		$now = time();

		$yingyeState = 1;
		if($now < $begantime){
			$yingyeState = -1;
		}

		$huoniaoTag->assign('yingyeState', $yingyeState);
		$huoniaoTag->assign('begantime', explode(" ", $began)[1]);
		$huoniaoTag->assign('endtime', explode(" ", $end)[1]);

		$huoniaoTag->assign('shop', $shop);
		$huoniaoTag->assign('weight', empty($weight) ? 1 : (int)$weight);

		$prie = (int)$price;
		if($price == 0){
			$price = "100元以下";
		}elseif($price == 1){
			$price = "100-200元";
		}elseif($price == 2){
			$price = "200-300元";
		}elseif($price == 3){
			$price = "300-400元";
		}elseif($price == 4){
			$price = "400-500元";
		}else{
			$price = "500元以上";
		}
		$huoniaoTag->assign('price', $price);
		$huoniaoTag->assign('from', $from);

	//订单继续支付
	}elseif($action == "paotuipay"){

		//查询订单信息
		$sql = $dsql->SetQuery("SELECT `ordernum`, `amount` FROM `#@__paotui_order` WHERE `id` = $orderid AND `state` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$data = $ret[0];
			$ordernum = $data['ordernum'];
			$amount = $data['amount'];

			$huoniaoTag->assign("ordernum", $ordernum);
			$huoniaoTag->assign("amount", $amount);

		}else{
			$param = array(
				"service"  => "member",
				"type"     => "user",
				"template" => "orderdetail",
				"module"   => "waimai",
				"id"       => $orderid
			);
			$url = getUrlPath($param);
			header("location:".$url);
		}


	//支付结果页面
	}elseif($action == "paotuipayreturn"){

		if($userid == -1){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/login.html?furl='.$furl);
			die;
		}

		$param = array(
			"service"  => "member",
			"type"     => "user",
			"template" => "order",
			"module"   => "waimai"
		);
		$url = getUrlPath($param);

		if(!empty($ordernum)){

			//根据支付订单号查询支付结果
			$archives = $dsql->SetQuery("SELECT `body`, `amount`, `state` FROM `#@__pay_log` WHERE `ordertype` = 'waimai' AND `ordernum` = '$ordernum' AND `uid` = $userid");
			$payDetail  = $dsql->dsqlOper($archives, "results");
			if($payDetail){

				$state = $payDetail[0]['state'];
				$ordernum = $payDetail[0]['body'];
				$huoniaoTag->assign('state', $state);
				$huoniaoTag->assign('ordernum', $ordernum);

				$sql = $dsql->SetQuery("SELECT `id`, `sid` FROM `#@__waimai_order` WHERE `ordernum` = '$ordernum'");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$huoniaoTag->assign('orderid', $ret[0]['id']);
					$huoniaoTag->assign('sid',   $ret[0]['sid']);
				}else{
					header("location:".$url);
					die;
				}


			//支付订单不存在
			}else{
				header("location:".$url);
			}

		}else{
			header("location:".$url);
		}

	//优惠券
	}elseif($action == "quan"){
		$huoniaoTag->assign("shopid", (int)$id);
		$huoniaoTag->assign("cart_address_id", (int)$address);
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


//验证配送员是否已经登录，并且帐号正常
function checkCourierAccount(){
	global $dsql;
	$did = GetCookie("courier");
	if($did){

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__waimai_courier` WHERE `id` = $did");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			PutCookie("courier", $did, 24 * 60 * 60 * 7, "/");
			return $did;
		}else{
			return -1;
		}

	}else{
		return -1;
	}

}
