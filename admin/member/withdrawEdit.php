<?php
/**
 * 查看修改提现详细信息
 *
 * @version        $Id: withdrawEdit.php 2013-12-11 上午10:53:46 $
 * @package        HuoNiao.Member
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("withdraw");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/member";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "withdrawEdit.html";

$action     = "member_withdraw";
$pagetitle  = "提现详细信息";
$dopost     = $dopost ? $dopost : "edit";

if($dopost != ""){
	//对字符进行处理
	$note   = cn_substrR($note,200);
	$time   = GetMkTime(time());
}

if($dopost == "edit"){

	$pagetitle = "修改团购订单信息";

	if($submit == "提交"){

		//表单二次验证
		if(trim($state) == ''){
			echo '{"state": 200, "info": "请选择更新到的状态"}';
			exit();
		}


		if(trim($note) == ''){
			echo '{"state": 200, "info": "请输入提现结果"}';
			exit();
		}

		//保存
		$archives = $dsql->SetQuery("UPDATE `#@__".$action."` SET `note` = '$note', `state` = '$state', `rdate` = '$time' WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "update");

		if($results != "ok"){
			echo '{"state": 200, "info": "保存失败！"}';
			exit();
		}


		$sql = $dsql->SetQuery("SELECT `uid`, `amount` FROM `#@__".$action."` WHERE `id` = ".$id);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid    = $ret[0]['uid'];
			$amount = $ret[0]['amount'];

			$param = array(
				"service"  => "member",
				"type"     => "user",
				"template" => "withdraw_log_detail",
				"id"       => $id
			);

			//自定义配置
			$config = array(
				"username" => $username,
				"amount" => $amount,
				"date" => date("Y-m-d H:i:s", $time),
				"info" => $note,
				"fields" => array(
					'keyword1' => '提现金额',
					'keyword2' => '提现时间',
					'keyword3' => '提现状态'
				)
			);

			//提现成功，减少会员的冻结金额，并且增加明细日志
			if($state == 1){

				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` - '$amount' WHERE `id` = '$uid'");
				$dsql->dsqlOper($archives, "update");

				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '0', '$amount', '提现', '$time')");
				$aid = $dsql->dsqlOper($archives, "lastid");

				updateMemberNotice($uid, "会员-提现申请审核通过", $param, $config);


			//提现失败，减少会员的冻结金额，增加可用余额
			}elseif($state == 2){

				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$amount', `freeze` = `freeze` - '$amount' WHERE `id` = '$uid'");
				$dsql->dsqlOper($archives, "update");

				updateMemberNotice($uid, "会员-提现申请审核失败", $param, $config);

			}

		}



		adminLog("更新提现状态", $id);

		echo '{"state": 100, "info": "修改成功！"}';
		exit();

	}else{
		if(!empty($id)){

			//主表信息
			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$action."` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "results");

			if(!empty($results)){

				$uid = $results[0]["uid"];

				//用户名
				$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $uid);
				$username = $dsql->dsqlOper($userSql, "results");
				if(count($username) > 0){
					$username = $username[0]['username'];
				}else{
					$username = "未知";
				}

				$bank   = $results[0]["bank"];
				$cardnum   = $results[0]["cardnum"];
				$cardname   = $results[0]["cardname"];
				$amount   = $results[0]["amount"];
				$tdate = date('Y-m-d H:i:s', $results[0]["tdate"]);
				$state = $results[0]["state"];
				$rdate = $results[0]["rdate"];
				$note = $results[0]["note"];

			}else{
				ShowMsg('要修改的信息不存在或已删除！', "-1");
				die;
			}

		}else{
			ShowMsg('要修改的信息参数传递失败，请联系管理员！', "-1");
			die;
		}
	}

//付款
}elseif($dopost == "transfers"){

	if($id){
		$ret = transfers($id);
		echo $ret;

	}else{
		echo '{"state": 200, "info": "请选择要操作的信息！"}';
	}
	die;
}


//自动转账
function transfers($id){

	global $dsql;
	global $cfg_withdrawFee;

	$sql = $dsql->SetQuery("SELECT `uid`, `bank`, `cardnum`, `cardname`, `amount` FROM `#@__member_withdraw` WHERE `id` = $id AND `state` = 0");
	$ret = $dsql->dsqlOper($sql, "results");
	if($ret){

		$uid = $ret[0]['uid'];
		$bank = $ret[0]['bank'];
		$cardnum = $ret[0]['cardnum'];
		$cardname = $ret[0]['cardname'];
		$amount = $ret[0]['amount'];

		$ordernum = create_ordernum();

		//验证类型
        $realname = $wechat_openid = '';
        $sql = $dsql->SetQuery("SELECT `realname`, `wechat_openid` FROM `#@__member` WHERE `id` = " . $uid);
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $realname = $ret[0]['realname'];
            $wechat_openid = $ret[0]['wechat_openid'];

            if($bank == 'weixin' && !$wechat_openid){
				return '{"state": 200, "info": "提现会员需要先绑定微信账号"}';
            }
        }

		if($bank != 'weixin' && $bank != 'alipay'){
			return '{"state": 200, "info": "不支持银行卡在线转账！"}';
		}else{

			$amount_ = $cfg_withdrawFee ? $amount * (100 - $cfg_withdrawFee) / 100 : $amount;
			$amount_ = sprintf("%.2f", $amount_);

			//微信提现
			if($bank == "weixin"){
				$order = array(
					'ordernum' => $ordernum,
					'openid' => $wechat_openid,
					'name' => $realname,
					'amount' => $amount_
				);

				require_once(HUONIAOROOT."/api/payment/wxpay/wxpayTransfers.php");
				$wxpayTransfers = new wxpayTransfers();
				$return = $wxpayTransfers->transfers($order);

				if($return['state'] != 100){
					return json_encode($return);
				}
			}else{

				if($realname != $cardname){
					return '{"state": 200, "info": "申请失败，提现到的账户真实姓名与实名认证信息不一致！"}';
				}
				$order = array(
					'ordernum' => $ordernum,
					'account' => $cardnum,
					'name' => $cardname,
					'amount' => $amount_
				);

				require_once(HUONIAOROOT."/api/payment/alipay/alipayTransfers.php");
				$alipayTransfers = new alipayTransfers();
				$return = $alipayTransfers->transfers($order);

				if($return['state'] != 100){
					return json_encode($return);
				}
			}


			$rdate = $return['date'];
			$payment_no = $return['payment_no'];

			$note = '提现成功，付款单号：'. $payment_no;

			//扣除冻结金额
			$archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` - '$amount' WHERE `id` = '$uid'");
			$dsql->dsqlOper($archives, "update");

			//保存操作日志
			$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '0', '$amount', '余额提现：$payment_no', '$rdate')");
			$dsql->dsqlOper($archives, "update");

			//更新记录状态
			$sql = $dsql->SetQuery("UPDATE `#@__member_withdraw` SET `state` = 1, `note` = '$note', `rdate` = '$rdate' WHERE `id` = $id");
			$dsql->dsqlOper($sql, "update");

			//自定义配置
			$param = array(
				"service"  => "member",
				"type"     => "user",
				"template" => "withdraw_log_detail",
				"id"       => $id
			);

			$config = array(
				"username" => $realname,
				"amount" => $amount,
				"date" => date("Y-m-d H:i:s", $rdate),
				"info" => $note,
				"fields" => array(
					'keyword1' => '提现金额',
					'keyword2' => '提现时间',
					'keyword3' => '提现状态'
				)
			);

			updateMemberNotice($uid, "会员-提现申请审核通过", $param, $config);

			return '{"state": 100, "info": "操作成功！"}';

		}

	}else{
		return '{"state": 200, "info": "信息不存在，或已经操作过！"}';
	}
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/jquery.dragsort-0.5.1.min.js',
		'admin/member/withdrawEdit.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('action', $action);
	$huoniaoTag->assign('pagetitle', $pagetitle);
	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('id', $id);
	$huoniaoTag->assign('uid', $uid);
	$huoniaoTag->assign('username', $username);
	$huoniaoTag->assign('bank', $bank);
	$huoniaoTag->assign('cardnum', $cardnum);
	$huoniaoTag->assign('cardname', $cardname);
	$huoniaoTag->assign('amount', $amount);
	$huoniaoTag->assign('tdate', $tdate);
	$huoniaoTag->assign('state', $state);
	$huoniaoTag->assign('note', $note);
	$huoniaoTag->assign('rdate', $rdate == 0 ? 0 : date("Y-m-d H:i:s", $rdate));

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/member";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
