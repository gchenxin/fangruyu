<?php
/**
 * 管理团购订单
 *
 * @version        $Id: tuanOrderList.php 2013-12-9 下午21:11:13 $
 * @package        HuoNiao.Tuan
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("tuanOrderList");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/tuan";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "tuanOrderList.html";

$action = "tuan_order";

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	$where = "";

    $where2 = " AND `store`.cityid in ($adminCityIds)";

    if ($adminCity){
        $where2 = " AND `store`.cityid = $adminCity";
    }

    $sidArr = array();
    $userSql = $dsql->SetQuery("SELECT `store`.id  FROM `#@__tuan_store` store WHERE 1=1".$where2);
    $results = $dsql->dsqlOper($userSql, "results");
    foreach ($results as $key => $value) {
        $sidArr[$key] = $value['id'];
    }
    if(!empty($sidArr)){
        $where3 = " AND `sid` in (".join(",",$sidArr).")";
    }else{
        echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": 0, "totalCount": 0, "totalGray": 0, "totalAudit": 0, "totalRefuse": 0}}';die;
    }

    $proSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__tuanlist` WHERE 1=1".$where3);
    $proResult = $dsql->dsqlOper($proSql, "results");
    if($proResult){
        $proid = array();
        foreach($proResult as $key => $pro){
            array_push($proid, $pro['id']);
        }
        if(!empty($proid)){
            $where .= " AND `proid` in (".join(",", $proid).")";
        }
    }

	if($sKeyword != ""){

		$where .= " AND (`ordernum` like '%$sKeyword%'";

        $proSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__tuanlist` WHERE `title` like '%$sKeyword%'".$where3);
        $proResult = $dsql->dsqlOper($proSql, "results");
        if($proResult){
            $proid = array();
            foreach($proResult as $key => $pro){
                array_push($proid, $pro['id']);
            }
            if(!empty($proid)){
                $where .= " OR `proid` in (".join(",", $proid).")";
            }
        }

		$userSql = $dsql->SetQuery("SELECT `id`, `username` FROM `#@__member` WHERE `username` like '%$sKeyword%'");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			$userid = array();
			foreach($userResult as $key => $user){
				array_push($userid, $user['id']);
			}
			if(!empty($userid)) {
                $where .= " OR `userid` in (" . join(",", $userid) . "))";
            }
		}else{
            $where .= " ) ";
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
	//已过期
	$state2 = $dsql->dsqlOper($archives." AND `orderstate` = 2", "totalCount");
	//已使用
	$state3 = $dsql->dsqlOper($archives." AND `orderstate` = 3", "totalCount");
	//已退款
	$state4 = $dsql->dsqlOper($archives." AND `ret-state` = 1", "totalCount");
	//已发货
	$state6 = $dsql->dsqlOper($archives." AND `orderstate` = 6 AND `exp-date` != 0", "totalCount");
	//交易关闭
	$state7 = $dsql->dsqlOper($archives." AND `orderstate` = 7", "totalCount");

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
		}elseif($state == 2){
			$totalPage = ceil($state2/$pagestep);
		}elseif($state == 3){
			$totalPage = ceil($state3/$pagestep);
		}elseif($state == 4){
			$totalPage = ceil($state4/$pagestep);
		}elseif($state == 6){
			$totalPage = ceil($state6/$pagestep);
		}elseif($state == 7){
			$totalPage = ceil($state7/$pagestep);
		}
	}

	$where .= " order by `id` desc";
	$totalPrice = 0;

	//计算总价
	$sql = $dsql->SetQuery("SELECT SUM(`orderprice` * `procount`) as price FROM `#@__".$action."` WHERE 1 = 1".$where);
	$ret = $dsql->dsqlOper($sql, "results");
	if($ret){
		$totalPrice = (float)$ret[0]['price'];
	}

	$totalPrice = sprintf("%.2f", $totalPrice);


	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `ordernum`, `userid`, `proid`, `orderprice`, `procount`, `propolic`, `orderstate`, `ret-state`, `exp-date`, `usercontact`, `orderdate`, `paytype`, `paydate` FROM `#@__".$action."` WHERE 1 = 1".$where);
	//echo $archives;die;
	$results = $dsql->dsqlOper($archives, "results");

	$list = array();

	if(count($results) > 0){
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["ordernum"] = $value["ordernum"];
			$list[$key]["userid"] = $value["userid"];
			$list[$key]["paydate"] = $value["paydate"];

			//用户名
			$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $value["userid"]);
			$username = $dsql->dsqlOper($userSql, "results");
			if(count($username) > 0){
				$list[$key]["username"] = $username[0]['username'];
			}else{
				$list[$key]["username"] = "未知";
			}

			$list[$key]["proid"] = $value["proid"];

			//团购商品
			$proSql = $dsql->SetQuery("SELECT `id`, `title`, `enddate`, `minnum`, `defbuynum`, `buynum` FROM `#@__tuanlist` WHERE `id` = ". $value["proid"]);
			$proname = $dsql->dsqlOper($proSql, "results");
			if(count($proname) > 0){
				$list[$key]["proname"] = $proname[0]['title'];
			}else{
				$list[$key]["proname"] = "未知";
			}

			$param = array(
				"service"     => "tuan",
				"template"    => "detail",
				"id"          => $proname[0]['id']
			);
			$list[$key]['prourl'] = getUrlPath($param);


			//计算订单价格
			$price = $value['orderprice'] * $value['procount'];
			$propolic   = $value['propolic'];
			$policy     = unserialize($propolic);
			if(!empty($propolic) && !empty($policy)){
				$freight  = $policy['freight'];
				$freeshi  = $policy['freeshi'];

				if($value['procount'] <= $freeshi){
					$price += $freight;
				}
			}
			$list[$key]["orderprice"] = sprintf("%.2f", $price);

			//判断订单状态为未付款，如果已经过期，更新订单状态
			// if($value["orderstate"] == 0){
			// 	if(GetMkTime(time()) > $proname[0]['enddate']){
			// 		$list[$key]["orderstate"] = "2";

			// 		$updateProSql = $dsql->SetQuery("UPDATE `#@__".$action."` SET `orderstate` = 2 WHERE `id` = ". $value["id"]);
			// 		$dsql->dsqlOper($updateProSql, "update");

			// 	}else{
			// 		$list[$key]["orderstate"] = $value["orderstate"];
			// 	}

			// //如果是已经付款的，检查活动是否结束，如果结束检查团购是否成功，如果成功则更新订单状态
			// }elseif($value["orderstate"] == 1){
			// 	if(GetMkTime(time()) > $proname[0]['enddate'] && $proname[0]['minnum'] <= $proname[0]['defbuynum'] + $proname[0]['buynum']){
			// 		$list[$key]["orderstate"] = "4";

			// 		$updateProSql = $dsql->SetQuery("UPDATE `#@__".$action."` SET `orderstate` = 4 WHERE `id` = ". $value["id"]);
			// 		$dsql->dsqlOper($updateProSql, "update");
			// 	}else{
			// 		$list[$key]["orderstate"] = $value["orderstate"];
			// 	}

			// //如果是已经过期的，检查商品活动是否已过期（这里主要是针对后期改动商品结束日期），如果没有过期，则更新订单状态为未付款
			// }elseif($value["orderstate"] == 2){
			// 	if(GetMkTime(time()) <= $proname[0]['enddate']){
			// 		$list[$key]["orderstate"] = "0";

			// 		$proSql = $dsql->SetQuery("UPDATE `#@__".$action."` SET `orderstate` = 0 WHERE `id` = ". $value["id"]);
			// 		$dsql->dsqlOper($proSql, "update");

			// 	}else{
			// 		$list[$key]["orderstate"] = $value["orderstate"];
			// 	}
			// }else{
			// 	$list[$key]["orderstate"] = $value["orderstate"];
			// }

			$list[$key]["orderstate"] = $value["orderstate"];
			$list[$key]["retState"] = $value["ret-state"];
			$list[$key]["expDate"] = $value["exp-date"];
			$list[$key]["usercontact"] = $value["usercontact"];
			$list[$key]["orderdate"] = date('Y-m-d H:i:s', $value["orderdate"]);

			//主表信息
			$sql = $dsql->SetQuery("SELECT `pay_name` FROM `#@__site_payment` WHERE `pay_code` = '".$value["paytype"]."'");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!empty($ret)){
				$list[$key]["paytype"] = $ret[0]['pay_name'];
			}else{
				$list[$key]["paytype"] = $value["paytype"];
			}
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "state0": '.$state0.', "state1": '.$state1.', "state2": '.$state2.', "state3": '.$state3.', "state4": '.$state4.', "state6": '.$state6.', "state7": '.$state7.'}, "totalPrice": '.$totalPrice.', "tuanOrderList": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "state0": '.$state0.', "state1": '.$state1.', "state2": '.$state2.', "state3": '.$state3.', "state4": '.$state4.', "state6": '.$state6.', "state7": '.$state7.'}, "totalPrice": '.$totalPrice.'}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "state0": '.$state0.', "state1": '.$state1.', "state2": '.$state2.', "state3": '.$state3.', "state4": '.$state4.', "state6": '.$state6.', "state7": '.$state7.'}, "totalPrice": '.$totalPrice.'}';
	}
	die;

//删除
}elseif($dopost == "del"){
	if(!testPurview("delTuanOrder")){
		die('{"state": 200, "info": '.json_encode('对不起，您无权使用此功能！').'}');
	}
	if($id == "") die;
	$each = explode(",", $id);
	$error = array();
	foreach($each as $val){
		$archives = $dsql->SetQuery("SELECT `id`, `ordernum`, `userid`, `proid`, `procount`, `orderprice`, `orderstate`, `paydate` FROM `#@__".$action."` WHERE `id` = ".$val);
		$results = $dsql->dsqlOper($archives, "results");

		$orderid = $results[0]['id'];
		$ordernum = $results[0]['ordernum'];
		$orderprice = $results[0]['orderprice'];
		$userid = $results[0]["userid"];
		$proid = $results[0]["proid"];
		$procount = $results[0]["procount"];
		$orderstate = $results[0]["orderstate"];
		$paydate = $results[0]['paydate'];

		//退款
		if($orderstate != 0 && $orderstate != 3 && $orderstate != 7 && ($orderstate ==2 && $paydate!=0)){
			//团购商品
			$proSql = $dsql->SetQuery("SELECT `tuantype` FROM `#@__tuanlist` WHERE `id` = ". $proid);
			$proname = $dsql->dsqlOper($proSql, "results");

			$tuantype = $proname[0]['tuantype'];

			//会员信息
			$userSql = $dsql->SetQuery("SELECT `money` FROM `#@__member` WHERE `id` = ". $userid);
			$userResult = $dsql->dsqlOper($userSql, "results");

			if($userResult){

				//会员帐户充值
				$price = $userResult[0]['money'] + $orderprice * $procount;
				$userOpera = $dsql->SetQuery("UPDATE `#@__member` SET `money` = ".$price." WHERE `id` = ". $userid);
				$dsql->dsqlOper($userOpera, "update");

				//记录退款日志
				$logs = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$orderprice', '团购退款：$ordernum', ".GetMkTime(time()).")");
				$dsql->dsqlOper($logs, "update");

			}

			//更新团购已购买数量
			$proSql = $dsql->SetQuery("UPDATE `#@__tuanlist` SET `buynum` = `buynum` - ".$procount."");
			$dsql->dsqlOper($proSql, "update");

			//删除团购券
			if($tuantype == 0){
				$archives = $dsql->SetQuery("DELETE FROM `#@__tuanquan` WHERE `orderid` = ".$orderid);
				$results = $dsql->dsqlOper($archives, "update");
			}
		}

		$archives = $dsql->SetQuery("DELETE FROM `#@__".$action."` WHERE `id` = ".$val);
		$results = $dsql->dsqlOper($archives, "update");
		if($results != "ok"){
			$error[] = $val;
		}
	}
	if(!empty($error)){
		echo '{"state": 200, "info": '.json_encode($error).'}';
	}else{
		adminLog("删除团购订单", $id);
		echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
	}
	die;

//付款
}elseif($dopost == "payment"){
	if(!testPurview("refundTuanOrder")){
		die('{"state": 200, "info": '.json_encode('对不起，您无权使用此功能！').'}');
	}
	if(!empty($id)){
		$archives = $dsql->SetQuery("SELECT `ordernum`, `userid`, `proid`, `procount`, `orderprice`, `orderstate` FROM `#@__".$action."` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");

		if($results){
			$ordernum = $results[0]['ordernum'];
			$orderprice = $results[0]['orderprice'];
			$userid = $results[0]["userid"];
			$proid = $results[0]["proid"];
			$procount = $results[0]["procount"];
			$orderstate = $results[0]["orderstate"];

			if($orderstate == 0){
				//团购商品
				$proSql = $dsql->SetQuery("SELECT l.`title`, l.`enddate`, l.`maxnum`, l.`limit`, l.`defbuynum`, l.`buynum`, l.`tuantype`, l.`freight`, l.`freeshi`, l.`expireddate`, s.`uid` FROM `#@__tuanlist` l LEFT JOIN `#@__tuan_store` s ON s.`id` = l.`sid` WHERE l.`id` = ". $proid);
				$proname = $dsql->dsqlOper($proSql, "results");

				if(!$proname){
					echo '{"state": 200, "info": '.json_encode("商品不存在，付款失败！").'}';
					die;
				}

				$title     = $proname[0]['title'];
				$uid       = $proname[0]['uid'];
				$enddate   = $proname[0]['enddate'];
				$maxnum    = $proname[0]['maxnum'];
				$limit     = $proname[0]['limit'];
				$defbuynum = $proname[0]['defbuynum'];
				$buynum    = $proname[0]['buynum'];
				$expireddate = $proname[0]['expireddate'];
				$totalBuy  = $defbuynum + $buynum + $procount;

				if($limit != 0 && $procount > $limit){
					echo '{"state": 200, "info": '.json_encode("购买数量超过商家限制，付款失败！").'}';
					die;
				}

				if($maxnum != 0 && $maxnum < $totalBuy){
					echo '{"state": 200, "info": '.json_encode("库存不足，付款失败！").'}';
					die;
				}

				if($proname[0]['tuantype'] == 2){
					if($results[0]["procount"] < $proname[0]["freeshi"]){
						$orderprice = $orderprice + $proname[0]['freight'];
					}
				}

				if(GetMkTime(time()) > $enddate){
					echo '{"state": 200, "info": '.json_encode("此团购商品已经过期，无法付款，请确认后操作！").'}';
					die;
				}else{

					//会员信息
					$userSql = $dsql->SetQuery("SELECT `username`, `money` FROM `#@__member` WHERE `id` = ". $userid);
					$userResult = $dsql->dsqlOper($userSql, "results");

					if($userResult){

						if($userResult[0]['money'] > $orderprice){
							//扣除会员帐户
							$price = $userResult[0]['money'] - $orderprice;
							$userOpera = $dsql->SetQuery("UPDATE `#@__member` SET `money` = ".$price." WHERE `id` = ". $userid);
							$dsql->dsqlOper($userOpera, "update");

							//记录消费日志
							$logs = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `amount`, `type`, `info`, `date`) VALUES (".$userid.", ".$orderprice.", 0, '团购消费：".$ordernum."', ".GetMkTime(time()).")");
							$dsql->dsqlOper($logs, "update");

							//更新团购已购买数量
							$proSql = $dsql->SetQuery("UPDATE `#@__tuanlist` SET `buynum` = `buynum` + $procount WHERE `id` = ".$proid);
							$dsql->dsqlOper($proSql, "update");

							//更新订单状态
							$orderOpera = $dsql->SetQuery("UPDATE `#@__".$action."` SET `orderstate` = 1, `paydate` = ".GetMkTime(time())." WHERE `id` = ". $id);
							$dsql->dsqlOper($orderOpera, "update");


							//生成团购券
							if($proname[0]['tuantype'] == 0){
								$sqlQuan = array();
								$carddate = GetMkTime(time());
								for ($i = 0; $i < $procount; $i++) {
									$cardnum = genSecret(12, 1);
									$sqlQuan[$i] = "('$id', '$cardnum', '$carddate', 0, '$expireddate')";
								}

								$sql = $dsql->SetQuery("INSERT INTO `#@__tuanquan` (`orderid`, `cardnum`, `carddate`, `usedate`, `expireddate`) VALUES ".join(",", $sqlQuan));
								$dsql->dsqlOper($sql, "update");
							}


							//支付成功，会员消息通知
							$paramUser = array(
								"service"  => "member",
								"type"     => "user",
								"template" => "orderdetail",
								"action"   => "tuan",
								"id"       => $id
							);

							$paramBusi = array(
								"service"  => "member",
								"template" => "orderdetail",
								"action"   => "tuan",
								"id"       => $id
							);

							//自定义配置
							$config = array(
								"username" => $userResult[0]['username'],
								"order" => $ordernum,
								"amount" => $orderprice,
								"fields" => array(
									'keyword1' => '商品信息',
									'keyword2' => '订单金额',
									'keyword3' => '订单状态'
								)
							);

							updateMemberNotice($userid, "会员-订单支付成功", $paramUser, $config);


							//获取会员名
							$username = "";
							$sql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = $uid");
							$ret = $dsql->dsqlOper($sql, "results");
							if($ret){
								$username = $ret[0]['username'];
							}

							//自定义配置
							$config = array(
								"username" => $username,
								"title" => $title,
								"order" => $ordernum,
								"amount" => $amount,
								"date" => date("Y-m-d H:i:s", time()),
								"fields" => array(
									'keyword1' => '订单编号',
									'keyword2' => '商品名称',
									'keyword3' => '订单金额',
									'keyword4' => '付款状态',
									'keyword5' => '付款时间'
								)
							);

							updateMemberNotice($uid, "会员-商家新订单通知", $paramBusi, $config);


							adminLog("为会员手动支付团购订单", $ordernum);

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

//退款
}elseif($dopost == "refund"){
	if(!testPurview("refundTuanOrder")){
		die('{"state": 200, "info": '.json_encode('对不起，您无权使用此功能！').'}');
	}
	if(!empty($id)){
		$archives = $dsql->SetQuery("SELECT o.`ordernum`, o.`point`, o.`pinid`, o.`pintype`, o.`balance`, o.`payprice`, o.`userid`, o.`proid`, o.`procount`, o.`orderprice`, o.`propolic`, o.`orderstate`, s.`uid`, l.`title` FROM `#@__".$action."` o LEFT JOIN `#@__tuanlist` l ON l.`id` = o.`proid` LEFT JOIN `#@__tuan_store` s ON s.`id` = l.`sid` WHERE o.`id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");

		if($results){

			$ordernum   = $results[0]['ordernum'];
			$orderprice = $results[0]['balance'] + $results[0]['payprice'];
			$userid     = $results[0]["userid"];
			$proid      = $results[0]["proid"];
			$procount   = $results[0]["procount"];
			$propolic   = $results[0]["propolic"];
			$orderstate = $results[0]["orderstate"];
			$uid        = $results[0]["uid"];
			$point      = $results[0]["point"];
			$title      = $results[0]["title"];
			$pinid      = $results[0]['pinid'];      //拼团id
			$pintype    = $results[0]['pintype'];    //是否团长

			if($orderstate == 1 || $orderstate == 2 || $orderstate == 4 || $orderstate == 6){

				//计算运费
				$policy = unserialize($propolic);
				if(!empty($propolic) && !empty($policy)){
					$freight  = $policy['freight'];
					$freeshi  = $policy['freeshi'];

					if($procount <= $freeshi){
						$orderprice += $freight;
					}
				}

				//拼团未成功
				if($pinid != 0){
					$sql = $dsql->SetQuery("SELECT `tid`, `user`, `state`, `people` FROM `#@__tuan_pin`  WHERE `id` = '$pinid'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret[0]['state']==3){
						echo '{"state": 200, "info": '.json_encode("拼团成功后，不能执行操作退款！").'}';
						die;
					}else{
						if($ret[0]['people'] == 1){//只有一个
							$sql = $dsql->SetQuery("UPDATE `#@__tuan_pin` SET `state` = 2 WHERE `id` = ". $pinid);
							$dsql->dsqlOper($sql, "update");
						}else{
							$userN = $ret[0]['user'];
							$userNarr = explode(',', $userN);
							$userNarr = array_merge(array_diff($userNarr, array($userid)));
							$userNarr = array_unique($userNarr);
							$useridPin = $userNarr[0];
							$userNarr = implode(',', $userNarr);
							if($pintype == 1){
								$wherePin = ", userid = '$useridPin'";
							}
							$sql = $dsql->SetQuery("UPDATE `#@__tuan_pin` SET `people` = people - 1, `user` = '$userNarr' $wherePin WHERE `id` = ". $pinid);
							$dsql->dsqlOper($sql, "update");
						}
					}
				}


				//只有买家确认收货后，费用才会转到商家会员，订单在可退款的状态下，是不需要扣除商家费用的 by: guozi 20160425
				//商家
				// $sql = $dsql->SetQuery("SELECT `money` FROM `#@__member` WHERE `id` = ". $uid);
				// $ret = $dsql->dsqlOper($sql, "results");
				// if(!$ret) die('{"state": 200, "info": '.json_encode("商家不存在，无法继续退款！").'}');
				// if($ret[0]['money'] < $orderprice) die('{"state": 200, "info": '.json_encode("商家帐户余额不足，请先充值！").'}');


				//会员信息
				$userSql = $dsql->SetQuery("SELECT `username`, `money` FROM `#@__member` WHERE `id` = ". $userid);
				$userResult = $dsql->dsqlOper($userSql, "results");

				if($userResult){

					//退回积分 by: guozi 20160425
					if(!empty($point)){
						$archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + '$point' WHERE `id` = '$userid'");
						$dsql->dsqlOper($archives, "update");

						//保存操作日志
						$archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$point', '团购订单退回：$ordernum', ".GetMkTime(time()).")");
						$dsql->dsqlOper($archives, "update");
					}

					//会员帐户充值
					if($orderprice > 0){
						$userOpera = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + ".$orderprice." WHERE `id` = ". $userid);
						$dsql->dsqlOper($userOpera, "update");

						//记录退款日志
						$logs = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `amount`, `type`, `info`, `date`) VALUES (".$userid.", ".$orderprice.", 1, '团购订单退款：".$ordernum."', ".GetMkTime(time()).")");
						$dsql->dsqlOper($logs, "update");
					}


					//只有买家确认收货后，费用才会转到商家会员，订单在可退款的状态下，是不需要扣除商家费用的 by: guozi 20160425
					//商家帐户扣款
					// $userOpera = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - ".$orderprice." WHERE `id` = ". $uid);
					// $dsql->dsqlOper($userOpera, "update");

					//记录退款日志
					// $logs = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `amount`, `type`, `info`, `date`) VALUES (".$uid.", ".$orderprice.", 0, '团购订单退款：".$ordernum."', ".GetMkTime(time()).")");
					// $dsql->dsqlOper($logs, "update");


					//更新团购已购买数量
					$proSql = $dsql->SetQuery("UPDATE `#@__tuanlist` SET `buynum` = `buynum` - $procount");
					$dsql->dsqlOper($proSql, "update");

					//更新订单状态
					$orderOpera = $dsql->SetQuery("UPDATE `#@__".$action."` SET `orderstate` = 7, `ret-state` = 0, `ret-type` = '其他', `ret-note` = '管理员提交', `ret-ok-date` = ".GetMkTime(time())." WHERE `id` = ". $id);
					$dsql->dsqlOper($orderOpera, "update");


					//退款成功，会员消息通知
					$paramUser = array(
						"service"  => "member",
						"type"     => "user",
						"template" => "orderdetail",
						"action"   => "tuan",
						"id"       => $id
					);

					$paramBusi = array(
						"service"  => "member",
						"template" => "orderdetail",
						"action"   => "tuan",
						"id"       => $id
					);

					//自定义配置
					$config = array(
						"username" => $userResult[0]['username'],
						"order" => $ordernum,
						"amount" => $orderprice,
						"fields" => array(
							'keyword1' => '退款状态',
							'keyword2' => '退款金额',
							'keyword3' => '审核说明'
						)
					);

					updateMemberNotice($userid, "会员-订单退款成功", $param, $config);


					//获取会员名
					$username = "";
					$sql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = $uid");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$username = $ret[0]['username'];
					}

					//自定义配置
					$config = array(
						"username" => $username,
						"order" => $ordernum,
						"amount" => $orderprice,
						"info" => "管理员手动退款",
						"fields" => array(
							'keyword1' => '退款原因',
							'keyword2' => '退款金额'
						)
					);

					updateMemberNotice($uid, "会员-订单退款通知", $param, $config);



					adminLog("为会员手动退款团购订单", $ordernum);

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
		'admin/tuan/tuanOrderList.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
    $huoniaoTag->assign('cityList', json_encode($adminCityArr));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/tuan";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
