<?php
/**
 * 管理商城商品订单
 *
 * @version        $Id: shopOrder.php 2014-2-20 下午14:00:10 $
 * @package        HuoNiao.Shop
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("shopOrder");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/shop";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "shopOrder.html";

$action = "shop_order";

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	$where = '';

    $where2 = " AND `cityid` in (0,$adminCityIds)";

    if ($adminCity){
        $where2 = " AND `cityid` = $adminCity";
    }

    $archives = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE 1=1".$where2);
    $results = $dsql->dsqlOper($archives, "results");
    if(count($results) > 0){
        $list = array();
        foreach ($results as $key=>$value) {
            $list[] = $value["id"];
        }
        $idList = join(",", $list);
        $where .= " AND `store` in ($idList)";
    }else{
        echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": 0, "totalCount": 0, "totalGray": 0, "totalAudit": 0, "totalRefuse": 0}}';die;
    }

	if($sKeyword != ""){

		$where .= " AND `ordernum` like '%$sKeyword%' OR `people` like '%$sKeyword%' OR `contact` like '%$sKeyword%' OR `address` like '%$sKeyword%'";

		//商品
		$proSql = $dsql->SetQuery("SELECT pp.`orderid` FROM `#@__shop_product` p LEFT JOIN `#@__shop_order_product` pp ON pp.`proid` = p.`id` WHERE p.`title` like '%$sKeyword%'");
		$proResult = $dsql->dsqlOper($proSql, "results");
		if($proResult){
			$orderid = array();
			foreach($proResult as $key => $pro){
				array_push($orderid, $pro['orderid']);
			}
			if(!empty($orderid)){
				$where .= " OR `id` in (".join(",", $orderid).")";
			}
		}

		//个人会员
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` like '%$sKeyword%' OR `nickname` like '%$sKeyword%'");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			$userid = array();
			foreach($userResult as $key => $user){
				array_push($userid, $user['id']);
			}
			if(!empty($userid)){
				$where .= " OR `userid` in (".join(",", $userid).")";
			}
		}

		//商家会员
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` like '%$sKeyword%' OR `nickname` like '%$sKeyword%' OR `company` like '%$sKeyword%'");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			$userid = array();
			foreach($userResult as $key => $user){
				array_push($userid, $user['id']);
			}
			if(!empty($userid)){
				$where .= " OR `store` in (".join(",", $userid).")";
			}
		}

	}

	if($start != ""){
		$where .= " AND `orderdate` >= ". GetMkTime($start." 00:00:00");
	}

	if($end != ""){
		$where .= " AND `orderdate` <= ". GetMkTime($end." 23:59:59");
	}

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$action."` WHERE 1 = 1".$where);

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);
	//未付款
	$state0 = $dsql->dsqlOper($archives." AND `orderstate` = 0", "totalCount");
	//未使用
	$state1 = $dsql->dsqlOper($archives." AND `orderstate` = 1", "totalCount");
	//成功
	$state3 = $dsql->dsqlOper($archives." AND `orderstate` = 3", "totalCount");
	//已退款
	$state4 = $dsql->dsqlOper($archives." AND `ret-state` = 1", "totalCount");
	//已发货
	$state6 = $dsql->dsqlOper($archives." AND `orderstate` = 6 AND `exp-date` != 0", "totalCount");
	//退款成功
	$state7 = $dsql->dsqlOper($archives." AND `orderstate` = 7", "totalCount");
	//交易关闭
	$state10 = $dsql->dsqlOper($archives." AND `orderstate` = 10", "totalCount");


	if($state != ""){
		if($state != "" && $state != 4 && $state != 5 && $state != 6){
			$where = " AND `orderstate` = " . $state;
		}

		//退款
		if($state == 4){
			$where = " AND `ret-state` = 1";
		}

		//已发货
		if($state == 6){
			$where = " AND `orderstate` = 6 AND `exp-date` != 0";
		}

		if($state == 0){
			$totalPage = ceil($state0/$pagestep);
		}elseif($state == 1){
			$totalPage = ceil($state1/$pagestep);
		}elseif($state == 3){
			$totalPage = ceil($state3/$pagestep);
		}elseif($state == 4){
			$totalPage = ceil($state4/$pagestep);
		}elseif($state == 6){
			$totalPage = ceil($state6/$pagestep);
		}elseif($state == 7){
			$totalPage = ceil($state7/$pagestep);
		}elseif($state == 10){
			$totalPage = ceil($state10/$pagestep);
		}
	}

	$where .= " order by `id` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `ordernum`, `store`, `userid`, `orderstate`, `orderdate`, `paytype`, `logistic`, `ret-state` FROM `#@__".$action."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["ordernum"] = $value["ordernum"];
			$list[$key]["userid"] = $value["userid"];

			//用户名
			$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $value["userid"]);
			$username = $dsql->dsqlOper($userSql, "results");
			if(count($username) > 0){
				$list[$key]["username"] = $username[0]['username'];
			}else{
				$list[$key]["username"] = "未知";
			}

			$list[$key]["storeid"] = $value["store"];

			//商家
			$userSql = $dsql->SetQuery("SELECT `title` FROM `#@__shop_store` WHERE `id` = ". $value["store"]);
			$store = $dsql->dsqlOper($userSql, "results");
			if(count($store) > 0){
				$param = array(
					"service"  => "shop",
					"template" => "store-detail",
					"id"       => $value['store']
				);
				$list[$key]["storeUrl"] = getUrlPath($param);
				$list[$key]["store"] = $store[0]['title'];
			}else{
				$list[$key]["storeUrl"] = "javascript:;";
				$list[$key]["store"] = "未知";
			}

			//订单金额
			$orderprice = 0;
			$sql = $dsql->SetQuery("SELECT `price`, `count`, `logistic` FROM `#@__".$action."_product` WHERE `orderid` = ".$value['id']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				foreach ($ret as $k => $v) {
					$orderprice += $v['price'] * $v['count'];
				}
			}
            $orderprice += $value['logistic'];
			$list[$key]["orderprice"] = sprintf("%.2f", $orderprice);


			$list[$key]["orderstate"] = $value["orderstate"];
			$list[$key]["orderdate"] = date('Y-m-d H:i:s', $value["orderdate"]);

			//主表信息
			$sql = $dsql->SetQuery("SELECT `pay_name` FROM `#@__site_payment` WHERE `pay_code` = '".$value["paytype"]."'");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!empty($ret)){
				$list[$key]["paytype"] = $ret[0]['pay_name'];
			}else{
				$list[$key]["paytype"] = empty($value["paytype"]) ? "积分或余额" : $value["paytype"];
			}

			$list[$key]['retState'] = $value['ret-state'];
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "state0": '.$state0.', "state1": '.$state1.', "state3": '.$state3.', "state4": '.$state4.', "state6": '.$state6.', "state7": '.$state7.', "state10": '.$state10.'}, "shopOrder": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "state0": '.$state0.', "state1": '.$state1.', "state3": '.$state3.', "state4": '.$state4.', "state6": '.$state6.', "state7": '.$state7.', "state10": '.$state10.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "state0": '.$state0.', "state1": '.$state1.', "state3": '.$state3.', "state4": '.$state4.', "state6": '.$state6.', "state7": '.$state7.', "state10": '.$state10.'}}';
	}
	die;

//删除
}elseif($dopost == "del"){
	if(!testPurview("shopOrderDel")){
		die('{"state": 200, "info": '.json_encode('对不起，您无权使用此功能！').'}');
	}
	if($id == "") die;
	$each = explode(",", $id);
	$error = array();
	foreach($each as $val){
		$archives = $dsql->SetQuery("DELETE FROM `#@__".$action."` WHERE `id` = ".$val);
		$results = $dsql->dsqlOper($archives, "update");
		if($results != "ok"){
			$error[] = $val;
		}

		$archives = $dsql->SetQuery("DELETE FROM `#@__".$action."_product` WHERE `orderid` = ".$val);
		$dsql->dsqlOper($archives, "update");
	}
	if(!empty($error)){
		echo '{"state": 200, "info": '.json_encode($error).'}';
	}else{
		adminLog("删除商城订单", $id);
		echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
	}
	die;


//付款
/**
	* 付款业务逻辑
	* 1. 验证订单状态，只有状态为未付款时才可以往下进行
	* 2. 验证订单中的商品：1. 订单中含有不存在或已经下架的商品
	*                    2. 订单中的商品库存不足
	* 3. 会员账户余额，不足需要先到会员管理页面充值
	* 4. 上面三种都通过之后就可以进行支付成功后的操作：
	* 5. 更新订单的支付方式
	* 6. 更新订单中商品的已售数量、库存（包括不同规格的库存）
	* 7. 扣除会员账户余额并做相关记录
	* 8. 更新订单状态为已付款
	* 9. 后续操作（如：发送短信通知等）
	*/
}elseif($dopost == "payment"){
	if(!testPurview("shopOrderEdit")){
		die('{"state": 200, "info": '.json_encode('对不起，您无权使用此功能！').'}');
	}
	if(!empty($id)){
		$archives = $dsql->SetQuery("SELECT `ordernum`, `userid`, `orderstate`, `logistic`, `amount` FROM `#@__".$action."` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");

		if($results){
			$ordernum = $results[0]['ordernum'];
			$userid = $results[0]["userid"];
			$orderstate = $results[0]["orderstate"];
			$logistic = $results[0]["logistic"];
			$amount = $results[0]["amount"];

			if($orderstate == 0){

				$orderprice = $logistic;
				$opArr = array();

				//订单商品
				$sql = $dsql->SetQuery("SELECT `id`, `proid`, `speid`, `specation`, `price`, `count`, `logistic`, `discount` FROM `#@__shop_order_product` WHERE `orderid` = ".$id);
				$res = $dsql->dsqlOper($sql, "results");
				if($res){

					foreach ($res as $key => $value) {

						$opid      = $value['id'];
						$proid     = $value['proid'];
						$speid     = $value['speid'];
						$specation = $value['specation'];
						$price     = $value['price'];
						$count     = $value['count'];
//						$logistic  = $value['logistic'];
						$discount  = $value['discount'];

						global $handler;
						$handler = true;
						$detailHandels = new handlers("shop", "detail");
						$detailConfig  = $detailHandels->getHandle($proid);
						if(is_array($detailConfig) && $detailConfig['state'] == 100){
							$detail  = $detailConfig['info'];
							if(is_array($detail)){

								//验证商品库存
								$inventor = $detail['inventory'];
								if($detail['specification']){
									foreach($detail['specification'] as $k => $v){
										if($v['spe'] == $speid){
											$inventor = $v['price'][2];
										}
									}
								}

								if(($detail['limit'] < $count && $detail['limit'] != 0) || $inventor < $count && $inventor != 0) {
									echo '{"state": 200, "info": '.json_encode('【'.$detail['title'].'  '.$specation.'】库存不足').'}';
									die;
								}

								$oprice = $price * $count + $discount;
								$orderprice += $oprice;

								array_push($opArr, array(
									"id"    => $opid,
									"proid" => $proid,
									"speid" => $speid,
									"count" => $count,
									"price" => $oprice
								));


							}else{
								echo '{"state": 200, "info": '.json_encode("商品不存在，付款失败！").'}';
								die;
							}
						}else{
							echo '{"state": 200, "info": '.json_encode("商品不存在，付款失败！").'}';
							die;
						}

					}


					//会员信息
					$userSql = $dsql->SetQuery("SELECT `money` FROM `#@__member` WHERE `id` = ". $userid);
					$userResult = $dsql->dsqlOper($userSql, "results");

					if($userResult){

						if($userResult[0]['money'] >= $amount){

							//更新订单支付方式
							$sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `paytype` = '管理员支付' WHERE `id` = ".$id);
							$dsql->dsqlOper($sql, "update");


							//更新商品信息
							foreach ($opArr as $key => $value) {

								$_id    = $value['id'];
								$_proid = $value['proid'];
								$_count = $value['count'];
								$_speid = $value['speid'];
								$_price = $value['price'];

								//更新订单实付金额
								$sql = $dsql->SetQuery("UPDATE `#@__shop_order_product` SET `point` = 0, `balance` = 0, `payprice` = '$_price' WHERE `id` = ".$_id);
								$dsql->dsqlOper($sql, "update");

								//更新已购买数量
								$sql = $dsql->SetQuery("UPDATE `#@__shop_product` SET `sales` = `sales` + $_count, `inventory` = `inventory` - $_count WHERE `id` = ".$_proid);
								$dsql->dsqlOper($sql, "update");

								//更新库存
								$sql = $dsql->SetQuery("SELECT `specification` FROM `#@__shop_product` WHERE `id` = $_proid");
								$ret = $dsql->dsqlOper($sql, "results");
								if($ret){
									$specification = $ret[0]['specification'];
									if(!empty($specification)){
										$nSpec = array();
										$specification = explode("|", $specification);
										foreach ($specification as $k => $v) {
											$specArr = explode(",", $v);
												if($specArr[0] == $_speid){
												$spec = explode("#", $v);
												$nCount = $spec[2] - $_count;
												$nCount = $nCount < 0 ? 0 : $nCount;
												array_push($nSpec, $spec[0]."#".$spec[1].$nCount);
											}else{
												array_push($nSpec, $v);
											}
										}

										$sql = $dsql->SetQuery("UPDATE `#@__shop_product` SET `specification` = '".join("|", $nSpec)."' WHERE `id` = '$_proid'");
										$dsql->dsqlOper($sql, "update");
									}
								}

							}



							//扣除会员帐户
							$userOpera = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - $amount WHERE `id` = ". $userid);
							$dsql->dsqlOper($userOpera, "update");

                            //增加冻结金额
                            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` + '$amount' WHERE `id` = '$userid'");
                            $dsql->dsqlOper($archives, "update");

							//记录消费日志
							$logs = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `amount`, `type`, `info`, `date`) VALUES (".$userid.", ".$amount.", 0, '商城消费：".$ordernum."', ".GetMkTime(time()).")");
							$dsql->dsqlOper($logs, "update");

							//更新订单状态
							$orderOpera = $dsql->SetQuery("UPDATE `#@__".$action."` SET `orderstate` = 1, `paydate` = ".GetMkTime(time())." WHERE `id` = ". $id);
							$dsql->dsqlOper($orderOpera, "update");

							adminLog("为会员手动支付商城订单", $ordernum);

							echo '{"state": 100, "info": '.json_encode("付款成功！").'}';
							die;

						}else{
							echo '{"state": 200, "info": '.json_encode("会员帐户余额不足，请先进行充值！").'}';
							die;
						}

					}else{
						echo '{"state": 200, "info": '.json_encode("会员不存在，无法继续支付！").'}';
						die;
					}


				}

			}else{
				echo '{"state": 200, "info": '.json_encode("此订单不是未付款状态，请确认后操作！").'}';
				die;
			}
		}else{
			echo '{"state": 200, "info": '.json_encode("订单不存在，请刷新页面！").'}';
			die;
		}

	}else{
		echo '{"state": 200, "info": '.json_encode("订单ID为空，操作失败！").'}';
		die;
	}
	die;


//退款
/**
	* 退款业务逻辑
	* 1. 验证订单状态，只有状态为已付款、申请退款、已发货时才可以往下进行
	* 2. 计算需要退回的余额及积分
	* 3. 更新会员余额及积分并做相关记录
	* 4. 更新订单中商品的已售数量、库存（包括不同规格的库存）
	* 5. 更新订单状态为已退款
	* 6. 后续操作（如：发送短信通知等）
	*/
}elseif($dopost == "refund"){
	if(!testPurview("shopOrderEdit")){
		die('{"state": 200, "info": '.json_encode('对不起，您无权使用此功能！').'}');
	}
	if(!empty($id)){
		$archives = $dsql->SetQuery("SELECT `ordernum`, `userid`, `orderstate`, `logistic`, `amount`, `balance`, `point`, `payprice` FROM `#@__".$action."` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");

		if($results){

			$ordernum   = $results[0]['ordernum'];
			$userid     = $results[0]["userid"];
			$orderstate = $results[0]["orderstate"];
			$logistic = $results[0]["logistic"];
			$amount = $results[0]["amount"];
			$balance = $results[0]["balance"];
			$point = $results[0]["point"];
			$payprice = $results[0]["payprice"];

			$orderTotalAmount = $balance + $payprice;

			if($orderstate == 1 || $orderstate == 4 || $orderstate == 6){

				//计算需要退回的积分及余额
				$totalPoint = 0;
				$totalMoney = $logistic;

				$opArr = array();

				$sql = $dsql->SetQuery("SELECT `proid`, `speid`, `count`, `point`, `balance`, `payprice` FROM `#@__shop_order_product` WHERE `orderid` = ".$id);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					foreach ($ret as $key => $value) {
						$totalPoint += $value['point'];
						$totalMoney += $value['balance'] + $value['payprice'];

						array_push($opArr, array(
							"proid" => $value['proid'],
							"speid" => $value['speid'],
							"count" => $value['count']
						));
					}
				}


				//会员信息
				$userSql = $dsql->SetQuery("SELECT `money` FROM `#@__member` WHERE `id` = ". $userid);
				$userResult = $dsql->dsqlOper($userSql, "results");

				if($userResult){

					//退回积分
					if(!empty($point)){
						$archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + '$point' WHERE `id` = '$userid'");
						$dsql->dsqlOper($archives, "update");

						//保存操作日志
						$archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$point', '商城订单退回：$ordernum', ".GetMkTime(time()).")");
						$dsql->dsqlOper($archives, "update");
					}

					//会员帐户充值
					if($orderTotalAmount > 0){
						$userOpera = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + ".$orderTotalAmount." WHERE `id` = ". $userid);
						$dsql->dsqlOper($userOpera, "update");

						//记录退款日志
						$logs = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `amount`, `type`, `info`, `date`) VALUES (".$userid.", ".$orderTotalAmount.", 1, '商城订单退款：".$ordernum."', ".GetMkTime(time()).")");
						$dsql->dsqlOper($logs, "update");
					}

					//更新订单状态
					$orderOpera = $dsql->SetQuery("UPDATE `#@__".$action."` SET `orderstate` = 7, `ret-state` = 0, `ret-ok-date` = ".GetMkTime(time())." WHERE `id` = ". $id);
					$dsql->dsqlOper($orderOpera, "update");


					//更新商品已售数量及库存
					foreach ($opArr as $key => $value) {

						$_proid = $value['proid'];
						$_count = $value['count'];
						$_speid = $value['speid'];

						//更新已购买数量
						$sql = $dsql->SetQuery("UPDATE `#@__shop_product` SET `sales` = `sales` - $_count, `inventory` = `inventory` + $_count WHERE `id` = ".$_proid);
						$dsql->dsqlOper($sql, "update");

						//更新库存
						$sql = $dsql->SetQuery("SELECT `specification` FROM `#@__shop_product` WHERE `id` = $_proid");
						$ret = $dsql->dsqlOper($sql, "results");
						if($ret){
							$specification = $ret[0]['specification'];
							if(!empty($specification)){
								$nSpec = array();
								$specification = explode("|", $specification);
								foreach ($specification as $k => $v) {
									$specArr = explode(",", $v);
										if($specArr[0] == $_speid){
										$spec = explode("#", $v);
										$nCount = $spec[2] + $_count;
										array_push($nSpec, $spec[0]."#".$spec[1].$nCount);
									}else{
										array_push($nSpec, $v);
									}
								}

								$sql = $dsql->SetQuery("UPDATE `#@__shop_product` SET `specification` = '".join("|", $nSpec)."' WHERE `id` = '$_proid'");
								$dsql->dsqlOper($sql, "update");
							}
						}

					}



					echo '{"state": 100, "info": '.json_encode("操作成功，款项已退还至会员帐户！").'}';
					die;

				}else{
					echo '{"state": 200, "info": '.json_encode("会员不存在，无法继续退款！").'}';
					die;
				}

			}else{
				echo '{"state": 200, "info": '.json_encode("订单当前状态不支持手动退款！").'}';
				die;
			}
		}else{
			echo '{"state": 200, "info": '.json_encode("订单不存在，请刷新页面！").'}';
			die;
		}

	}else{
		echo '{"state": 200, "info": '.json_encode("订单ID为空，操作失败！").'}';
		die;
	}


}

//验证模板文件
if(file_exists($tpl."/".$templates)){
    //css
    $cssFile = array(
        'ui/jquery.chosen.css',
        'admin/chosen.min.css'
    );
    $huoniaoTag->assign('cssFile', includeFile('css', $cssFile));
	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/bootstrap-datetimepicker.min.js',
        'ui/chosen.jquery.min.js',
		'admin/shop/shopOrder.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

    $huoniaoTag->assign('cityList', json_encode($adminCityArr));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/shop";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
