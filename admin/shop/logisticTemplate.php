<?php
/**
 * 管理运费模板
 *
 * @version        $Id: logisticTemplate.php 2015-11-13 下午14:06:21 $
 * @package        HuoNiao.Shop
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("logisticTemplate");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/shop";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$tab = "shop_logistictemplate";

$sid = (int)$sid;   //sid为0表示官方直营运费模板

//列表
if($dopost == ""){

    //css
    $cssFile = array(
        'ui/jquery.chosen.css',
        'admin/chosen.min.css'
    );

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
        'ui/chosen.jquery.min.js',
		'admin/shop/logisticTemplate.js'
	);

	$templates = "logisticTemplate.html";

	$list = array();
	$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `sid` = $sid".$where." ORDER BY `id` DESC");
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["title"] = $value["title"];
			$list[$key]['detail'] = getPriceDetail($value["bearFreight"], $value['valuation'], $value['express_start'], $value['express_postage'], $value['express_plus'], $value['express_postageplus'], $value['preferentialStandard'], $value['preferentialMoney']);
		}

	}

	$huoniaoTag->assign('list', $list);

	if($do == "ajax"){
		echo '{"state": 100, "info": '.json_encode("获取成功").', "list": '.json_encode($list).'}';
		die;
	}


//获取运费详细
}elseif($dopost == "detail"){

	if(!empty($id)){

		$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `id` = $id");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$value = $results[0];
			echo getPriceDetail($value["bearFreight"], $value['valuation'], $value['express_start'], $value['express_postage'], $value['express_plus'], $value['express_postageplus'], $value['preferentialStandard'], $value['preferentialMoney']);
		}

	}

	die;


//新增
}elseif($dopost == "add" || $dopost == "edit"){


	if($submit == "提交"){

		if(empty($title)) die('{"state": 200, "info": "请输入模板名称！"}');

		$purchase = (float)$purchase;
		$express_start = (float)$express_start;
		$express_postage = (float)$express_postage;
		$express_plus = (float)$express_plus;
		$express_postageplus = (float)$express_postageplus;
		$preferentialStandard = (float)$preferentialStandard;
		$preferentialMoney = (float)$preferentialMoney;


		if($bearFreight == 1){

			$purchase = 0;
			$express_start = 0;
			$express_postage = 0;
			$express_plus = 0;
			$express_postageplus = 0;
			$preferentialStandard = 0;
			$preferentialMoney = 0;

		}


		if($dopost == "add"){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__shop_logistictemplate` ( `sid`, `title`, `bearFreight`, `valuation`, `express_start`, `express_postage`, `express_plus`, `express_postageplus`, `preferentialStandard`, `preferentialMoney`) VALUES ( '$sid', '$title', '$bearFreight', '$valuation', '$express_start', '$express_postage', '$express_plus', '$express_postageplus', '$preferentialStandard', '$preferentialMoney')");
			$results = $dsql->dsqlOper($archives, "update");

			if($results == "ok"){
				adminLog("新增运费模板", $title);
				echo '{"state": 100, "info": "添加成功！"}';die;
			}else{
				echo '{"state": 200, "info": "添加失败！"}';die;
			}

		}else{

			//保存到主表
			$archives = $dsql->SetQuery("UPDATE `#@__shop_logistictemplate` SET `title` = '$title', `bearFreight` = '$bearFreight', `valuation` = '$valuation', `express_start` = '$express_start', `express_postage` = '$express_postage', `express_plus` = '$express_plus', `express_postageplus` = '$express_postageplus', `preferentialStandard` = '$preferentialStandard', `preferentialMoney` = '$preferentialMoney' WHERE `id` = ".$_POST['id']);
			$results = $dsql->dsqlOper($archives, "update");

			if($results == "ok"){
				adminLog("修改运费模板", $title);
				echo '{"state": 100, "info": "修改成功！"}';die;
			}else{
				echo '{"state": 200, "info": "修改失败！"}';die;
			}

		}

		die;
	}

	$valuationTxt = "件";

	if($dopost == 'edit'){

		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_logistictemplate` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$res = $results[0];

			$sid = $res['sid'];
			$title = $res['title'];
			$bearFreight = $res['bearFreight'];
			$valuation = $res['valuation'];
			$express_start = $res['express_start'];
			$express_postage = $res['express_postage'];
			$express_plus = $res['express_plus'];
			$express_postageplus = $res['express_postageplus'];
			$preferentialStandard = $res['preferentialStandard'];
			$preferentialMoney = $res['preferentialMoney'];


			$huoniaoTag->assign('title', $title);
			$huoniaoTag->assign('express_start', $express_start);
			$huoniaoTag->assign('express_postage', $express_postage);
			$huoniaoTag->assign('express_plus', $express_plus);
			$huoniaoTag->assign('express_postageplus', $express_postageplus);
			$huoniaoTag->assign('preferentialStandard', $preferentialStandard);
			$huoniaoTag->assign('preferentialMoney', $preferentialMoney);


			switch ($valuation) {
				case 0:
					$valuationTxt = "件";
					break;
				case 1:
					$valuationTxt = "kg";
					break;
				case 2:
					$valuationTxt = "m³";
					break;
			}

		}

	}

	$huoniaoTag->assign('valuationTxt', $valuationTxt);

    $huoniaoTag->assign('cityList', json_encode($adminCityArr));

    //css
    $cssFile = array(
        'ui/jquery.chosen.css',
        'admin/chosen.min.css'
    );

	//js
	$jsFile = array(
        'ui/chosen.jquery.min.js',
		'admin/shop/logisticTemplateAdd.js'
	);

	$templates = "logisticTemplateAdd.html";


	//是否包邮
	$huoniaoTag->assign('bearFreightopt', array('0', '1'));
	$huoniaoTag->assign('bearFreightnames',array('自定义邮费','免邮费'));
	$huoniaoTag->assign('bearFreight', $bearFreight == "" ? 0 : $bearFreight);

	//计价方式
	$huoniaoTag->assign('valuationopt', array('0', '1', '2'));
	$huoniaoTag->assign('valuationnames',array('按件数','按重量', '按体积'));
	$huoniaoTag->assign('valuation', $valuation == "" ? 0 : $valuation);


//删除
}elseif($dopost == "del"){
	if(!testPurview("productDel")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	if($id != ""){

		$each = explode(",", $id);
		$error = array();
		$title = array();
		foreach($each as $val){

			//删除表
			$archives = $dsql->SetQuery("DELETE FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}

		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("删除商城商品", join(", ", $title));
			echo '{"state": 100, "info": '.json_encode("删除成功！").'}';
		}
		die;

	}
	die;

}


//验证模板文件
if(file_exists($tpl."/".$templates)){

    $huoniaoTag->assign('cssFile', includeFile('css', $cssFile));
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('id', $id);
	$huoniaoTag->assign('sid', $sid);

	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/shop";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
