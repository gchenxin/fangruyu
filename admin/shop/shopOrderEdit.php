<?php
/**
 * 查看修改商城订单信息
 *
 * @version        $Id: shopOrderEdit.php 2016-04-25 上午09:31:15 $
 * @package        HuoNiao.Shop
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("shopOrderEdit");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/shop";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "shopOrderEdit.html";

$action     = "shop_order";
$pagetitle  = "修改商城订单";
$dopost     = $dopost ? $dopost : "edit";

if($dopost != ""){
	//对字符进行处理
	$address   = cn_substrR($address,50);
	$people   = cn_substrR($people,10);
}

if($dopost == "edit"){

	$pagetitle = "修改商城订单";

	if($submit == "提交"){

		//表单二次验证
		if(trim($address) == ''){
			echo '{"state": 200, "info": "请输入街道地址"}';
			exit();
		}
		if(trim($people) == ''){
			echo '{"state": 200, "info": "请输入收货人姓名"}';
			exit();
		}
		if(trim($contact) == ''){
			echo '{"state": 200, "info": "请输入联系电话"}';
			exit();
		}

		//保存
		$archives = $dsql->SetQuery("UPDATE `#@__".$action."` SET `address` = '".$address."', `people` = '".$people."', `contact` = '".$contact."', `note` = '".$note."' WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "update");

		if($results != "ok"){
			echo '{"state": 200, "info": "保存失败！"}';
			exit();
		}

		adminLog("修改商城订单配送信息", $id);

		echo '{"state": 100, "info": "修改成功！"}';
		exit();

	}else{
		if(!empty($id)){

			//主表信息
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$action."` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "results");

			if(!empty($results)){

				$ordernum = $results[0]["ordernum"];
				$people   = $results[0]["people"];
				$userid   = $results[0]["userid"];
				$storeid  = $results[0]["store"];

				$param = array(
					"service"  => "shop",
					"template" => "store-detail",
					"id"       => $storeid
				);
				$storeUrl = getUrlPath($param);

				//用户名
				$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $results[0]["userid"]);
				$username = $dsql->dsqlOper($userSql, "results");
				if(count($username) > 0){
					$username = $username[0]['username'];
				}else{
					$username = "未知";
				}

				//商家
				$userSql = $dsql->SetQuery("SELECT `title` FROM `#@__shop_store` WHERE `id` = ". $storeid);
				$store = $dsql->dsqlOper($userSql, "results");
				if(count($store) > 0){
					$store = $store[0]['title'];
				}else{
					$store = "未知";
				}

				//订单商品
				$product = array();
				$orderprice = $orderpayprice = 0;
				$sql = $dsql->SetQuery("SELECT `proid`, `specation`, `price`, `count`, `logistic`, `discount`, `point`, `balance`, `payprice` FROM `#@__".$action."_product` WHERE `orderid` = ".$id);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					foreach ($ret as $k => $v) {

						$param = array(
							"service"  => "shop",
							"template" => "detail",
							"id"       => $v['proid']
						);
						$proUrl = getUrlPath($param);
						$proName = "产品名称";
						$proImg = "";
						$sql = $dsql->SetQuery("SELECT `title`, `litpic` FROM `#@__shop_product` WHERE `id` = ".$v['proid']);
						$res = $dsql->dsqlOper($sql, "results");
						if($res){
							$proName = $res[0]['title'];
							$proImg  = getFilePath($res[0]['litpic']);
						}

						array_push($product, array(
							"product"   => $proName,
							"proUrl"    => $proUrl,
							"proImg"    => $proImg,
							"specation" => $v['specation'],
							"price"     => $v['price'],
							"count"     => $v['count'],
							"logistic"  => $v['logistic'],
							"discount"  => $v['discount'],
							"point"     => $v['point'],
							"balance"   => $v['balance'],
							"payprice"  => $v['payprice']
						));

						$orderprice += $v['price'] * $v['count'] + $v['logistic'];
						$orderpayprice += $v['payprice'];
					}
				}
				$orderprice = sprintf("%.2f", $orderprice);
				$orderpayprice = sprintf("%.2f", $orderpayprice);


				$expCompany = $results[0]['exp-company'];
				$expNumber  = $results[0]['exp-number'];
				$expDate    = $results[0]['exp-date'];
				$orderstate = $results[0]["orderstate"];
				$expDate    = $results[0]['exp-date'];
				$retState   = $results[0]['ret-state'];
				$retOkdate  = $results[0]['ret-ok-date'];
				$retType   = $results[0]['ret-type'];
				$retNote   = $results[0]['ret-note'];

				$imglist = array();
				$pics = $results[0]['ret-pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
					foreach ($pics as $key => $value) {
						$imglist[$key]['val'] = $value;
						$imglist[$key]['path'] = getFilePath($value);
					}
				}
				$retPics   = $imglist;

				$retDate   = $results[0]['ret-date'];
				$retSnote  = $results[0]['ret-s-note'];

				$imglist = array();
				$pics = $results[0]['ret-s-pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
					foreach ($pics as $key => $value) {
						$imglist[$key]['val'] = $value;
						$imglist[$key]['path'] = getFilePath($value);
					}
				}
				$retSpics  = $imglist;

				$retSdate  = $results[0]['ret-s-date'];
				$orderdate = date('Y-m-d H:i:s', $results[0]["orderdate"]);
				$paytype = $results[0]["paytype"];
				$paydate = date('Y-m-d H:i:s', $results[0]["paydate"]);

				$people  = $results[0]["people"];
				$address = $results[0]["address"];
				$contact = $results[0]["contact"];
				$note    = $results[0]["note"];


			}else{
				ShowMsg('要修改的信息不存在或已删除！', "-1");
				die;
			}

		}else{
			ShowMsg('要修改的信息参数传递失败，请联系管理员！', "-1");
			die;
		}
	}
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/jquery.dragsort-0.5.1.min.js',
		'admin/shop/shopOrderEdit.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('action', $action);
	$huoniaoTag->assign('pagetitle', $pagetitle);
	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('id', $id);
	$huoniaoTag->assign('ordernum', $ordernum);
	$huoniaoTag->assign('userid', $userid);
	$huoniaoTag->assign('username', $username);
	$huoniaoTag->assign('storeid', $storeid);
	$huoniaoTag->assign('store', $store);
	$huoniaoTag->assign('storeUrl', $storeUrl);
	$huoniaoTag->assign('product', $product);
	$huoniaoTag->assign('orderprice', $orderprice);
	$huoniaoTag->assign('orderpayprice', $orderpayprice);
	$huoniaoTag->assign('people', $people);
	$huoniaoTag->assign('contact', $contact);
	$huoniaoTag->assign('address', $address);
	$huoniaoTag->assign('note', $note);


	//主表信息
	$sql = $dsql->SetQuery("SELECT `pay_name` FROM `#@__site_payment` WHERE `pay_code` = '".$paytype."'");
	$ret = $dsql->dsqlOper($sql, "results");
	if(!empty($ret)){
		$huoniaoTag->assign('paytype', $ret[0]['pay_name']);
	}else{

		global $cfg_pointName;
		$payname = "";
		if($paytype == "point,money"){
			$payname = $cfg_pointName."+余额";
		}elseif($paytype == "point"){
			$payname = $cfg_pointName;
		}elseif($paytype == "money"){
			$payname = "余额";
		}else{
			$payname = empty($paytype) ? "积分或余额" : $paytype;
		}
		$huoniaoTag->assign('paytype', $payname);
	}


	$huoniaoTag->assign('expCompany', $expCompany);
	$huoniaoTag->assign('expNumber', $expNumber);
	$huoniaoTag->assign('expDate', $expDate == 0 ? 0 : date("Y-m-d H:i:s", $expDate));
	$huoniaoTag->assign('orderstate', $orderstate);
	$huoniaoTag->assign('retState', $retState);
	$huoniaoTag->assign('ordermobile', $ordermobile);
	$huoniaoTag->assign('cardnum', $cardnum);
	$huoniaoTag->assign('orderdate', $orderdate);
	$huoniaoTag->assign('retOkdate', $retOkdate == 0 ? 0 : date("Y-m-d H:i:s", $retOkdate));

	$huoniaoTag->assign('retType', $retType);
	$huoniaoTag->assign('retNote', $retNote);
	$huoniaoTag->assign('retPics', $retPics);
	$huoniaoTag->assign('retDate', $retDate == 0 ? 0 : date("Y-m-d H:i:s", $retDate));
	$huoniaoTag->assign('retSnote', $retSnote);
	$huoniaoTag->assign('retSpics', $retSpics);
	$huoniaoTag->assign('retSdate', $retSdate == 0 ? 0 : date("Y-m-d H:i:s", $retSdate));
	$huoniaoTag->assign('paydate', $paydate);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/shop";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
