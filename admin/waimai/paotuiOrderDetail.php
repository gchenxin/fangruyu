<?php
/**
 * 跑腿订单详细
 *
 * @version        $Id: orderDetail.php 2017-5-25 上午10:16:21 $
 * @package        HuoNiao.Order
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', ".." );
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/waimai";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$dbname = "paotui_order";
$templates = "paotuiOrderDetail.html";


if(empty($id)){
    die;
}


$sql = $dsql->SetQuery("SELECT * FROM `#@__$dbname` WHERE `id` = $id");
$ret = $dsql->dsqlOper($sql, "results");
if($ret){

    foreach ($ret[0] as $key => $value) {

        //用户
        if($key == "uid"){
            $userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $value);
            $username = $dsql->dsqlOper($userSql, "results");
            if(count($username) > 0){
                $huoniaoTag->assign("username", $username[0]['username']);
            }
        }

        //支付方式
        if($key == "paytype"){
            $_paytype = '';
            switch ($value) {
                case 'wxpay':
                    $_paytype = '微信支付';
                    break;
                case 'alipay':
                    $_paytype = '支付宝';
                    break;
                case 'unionpay':
                    $_paytype = '银联支付';
                    break;
                case 'money':
                    $_paytype = '余额支付';
                    break;
                case 'delivery':
                    $_paytype = '货到付款';
                    break;
                default:
                    break;
            }
            $value = $_paytype;
        }

        //配送员
        if($key == "peisongid"){
            $sql = $dsql->SetQuery("SELECT `name` FROM `#@__waimai_courier` WHERE `id` = $value");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $huoniaoTag->assign("peisong", $ret[0]['name']);
            }
        }

        $huoniaoTag->assign($key, $value);
    }

}else{
    die;
}



//验证模板文件
if(file_exists($tpl."/".$templates)){

    //css
	$cssFile = array(
		'admin/jquery-ui.css',
		'admin/styles.css',
		'admin/chosen.min.css',
		'admin/ace-fonts.min.css',
		'admin/select.css',
		'admin/ace.min.css',
		'admin/animate.css',
		'admin/font-awesome.min.css',
		'admin/simple-line-icons.css',
		'admin/font.css',
		// 'admin/app.css'
	);
	$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));

    //js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'admin/waimai/paotuiOrderDetail.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
