<?php
/**
 * 管理商城分类
 *
 * @version        $Id: productAdd.php 2014-2-12 下午23:10:15 $
 * @package        HuoNiao.Shop
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("productAdd");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/shop";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$adminAreaIDs = '';
if($userType == 3){
    $sql = $dsql->SetQuery("SELECT `mgroupid` FROM `#@__member` WHERE `id` = " . $userLogin->getUserID());
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        $adminCityID = $ret[0]['mgroupid'];

        global $data;
        $data = '';
        $adminAreaData = $dsql->getTypeList($adminCityID, 'site_area');
        $adminAreaIDArr = parent_foreach($adminAreaData, 'id');
        $adminAreaIDs = join(',', $adminAreaIDArr);
    }
}

if($action == ""){
	$templates = "selectCategory.html";

	if($typeid != ""){
		//遍历所选分类名称，输出格式：分类名 > 分类名
		global $data;
		$data = "";
		$proTypeName = getParentArr("shop_type", $typeid);
		$proTypeName = array_reverse(parent_foreach($proTypeName, "typename"));
		$huoniaoTag->assign('proType', join(" > ", $proTypeName));
	}else{
		$huoniaoTag->assign('proType', "无");
	}

	$huoniaoTag->assign('typeid', $typeid);
	$huoniaoTag->assign('id', $id);

	//js
	$jsFile = array(
		'admin/shop/selectCategory.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
}else{

	if($dopost == "edit"){
		if(!empty($id)){
			if($submit != "提交"){
				//主表信息
				$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_product` WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "results");

				if(!empty($results)){

					if($_GET['typeid'] == ""){
						$typeid      = $results[0]['type'];
					}
					$title       = $results[0]['title'];
					$brand       = $results[0]['brand'];
					$property    = $results[0]['property'];
					$store       = $results[0]['store'];
					$category    = $results[0]['category'];
					$mprice      = $results[0]['mprice'];
					$price       = $results[0]['price'];
					$logistic    = $results[0]['logistic'];
					$volume      = $results[0]['volume'];
					$weight      = $results[0]['weight'];
					$specifiList = $results[0]['specification'];
					$inventory   = $results[0]['inventory'];
					$limit       = $results[0]['limit'];
					$btime       = $results[0]['btime'];
					$etime       = $results[0]['etime'];
					$litpic      = $results[0]['litpic'];
					$sort        = $results[0]['sort'];
					$click       = $results[0]['click'];
					$state       = $results[0]['state'];
					$flag        = $results[0]['flag'];
					$kstime      = $results[0]['kstime'];
					$ketime      = $results[0]['ketime'];
					$pics        = $results[0]['pics'];
					$body        = $results[0]['body'];
					$mbody       = $results[0]['mbody'];
					$video       = $results[0]['video'];
					$fx_reward   = $results[0]['fx_reward'];

				}else{
					ShowMsg('要修改的信息不存在或已删除！', "-1");
					die;
				}
			}
		}else{
			ShowMsg('要修改的信息参数传递失败，请联系管理员！', "-1");
			die;
		}
	}

	if($typeid == ""){
		header("location:productAdd.php");
	}

	$huoniaoTag->assign('typeid', $typeid);

	//表单验证
	if($submit == "提交"){

		$brand     = (int)$brand;
		$mprice    = (float)$mprice;
		$price     = (float)$price;
		$logistic  = (int)$logistic;
		$volume    = (float)$volume;
		$weight    = (float)$weight;
		$inventory = (int)$inventory;
		$limit     = (int)$limit;
		$click     = (int)$click;
		$sort      = (int)$sort;
		$btime     = !empty($btime) ? GetMkTime($btime) : 0;
		$etime     = !empty($etime) ? GetMkTime($etime) : 0;
		$kstime    = !empty($kstime) ? GetMkTime($kstime) : 0;
		$ketime    = !empty($ketime) ? GetMkTime($ketime) : 0;
		if($fx_reward){
			$tmp = $fx_reward;
			if(strstr($fx_reward, '%')){
				if(substr($fx_reward, -1) != '%'){
					echo '{"state": 200, "info": "分销佣金设置错误"}';
					exit();
				}
				$fx_reward = (float)$fx_reward.'%';
			}else{
				$fx_reward = (float)$fx_reward;
			}
			if(strlen($tmp) != strlen($fx_reward)){
				echo '{"state": 200, "info": "分销佣金设置错误"}';
				exit();
			}
		}

		if($typeid == ""){
			echo '{"state": 200, "info": "分类获取失败，请重新选择分类！"}';
			exit();
		}

		if($itemid == ""){
			echo '{"state": 200, "info": "分类属性ID获取失败，请重新选择分类！"}';
			exit();
		}

		if(empty($store)){
			echo '{"state": 200, "info": "请选择所属店铺！"}';
			exit();
		}

		//获取分类下相应属性
		$property = array();
		$propertyName = "item";
		$shopitem = $dsql->SetQuery("SELECT `id`, `typename`, `flag` FROM `#@__shop_item` WHERE `type` = ".$itemid." AND `parentid` = 0 ORDER BY `weight`");
		$shopResults = $dsql->dsqlOper($shopitem, "results");
		foreach($shopResults as $key => $val){

			$id = $val['id'];
			$typeName = $val['typename'];
			$r = strstr($val['flag'], 'r');
			$proval = $_POST[$propertyName.$id];

			if(is_array($proval)){
				if($r && empty($proval)){
					echo '{"state": 200, "info": "请选择'.$typeName.'！"}';
					exit();
				}
				if(!empty($proval)){
					array_push($property, $id."#".join(",", $proval));
				}
			}else{
				if($r && $proval == ""){
					echo '{"state": 200, "info": "请选择'.$typeName.'！"}';
					exit();
				}
				if(!empty($proval)){
					array_push($property, $id."#".$proval);
				}
			}

		}
		$property = join("|", $property);

		if($title == ""){
			echo '{"state": 200, "info": "请输入商品标题！"}';
			exit();
		}

		$category = isset($category) ? join(',',$category) : '';

		if(!preg_match("/^0|\d*\.?\d+$/i", $mprice, $matches)){
			echo '{"state": 200, "info": "市场价不得为空，类型为数字！"}';
			exit();
		}

		if(!preg_match("/^0|\d*\.?\d+$/i", $price, $matches)){
			echo '{"state": 200, "info": "一口价不得为空，类型为数字！"}';
			exit();
		}

		if(empty($logistic)){
			echo '{"state": 200, "info": "请选择物流运费模板！"}';
			exit();
		}

		//获取分类下相应规格
		$specifival = array();
		$spearray = array();
		$invent = 0;
		$typeitem = $dsql->SetQuery("SELECT `spe` FROM `#@__shop_type` WHERE `id` = ".$typeid."");
		$typeResults = $dsql->dsqlOper($typeitem, "results");
		if($typeResults){
			$spe = $typeResults[0]['spe'];
			if($spe != ""){
				$spe = explode(",", $spe);
				foreach($spe as $key => $val){
					$speitem = array();
					$speSql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_specification` WHERE `id` = ".$val);
					$speResults = $dsql->dsqlOper($speSql, "results");
					if($speResults){
						$speval = $_POST["spe".$speResults[0]['id']];
						if(!empty($speval) != ""){
							array_push($spearray, $speval);
						}
					}
				}
			}
		}

		if(!empty($spearray)){
			if(count($spearray) > 1){
				$spearray = descartes($spearray);
			}else{
				$spearray = $spearray[0];
			}
			foreach($spearray as $key => $val){
				$speid = $val;
				if(is_array($val)){
					$speid = join("-", $val);
				}
				$spemprice = $_POST["f_mprice_".$speid];
				$speprice = $_POST["f_price_".$speid];
				$speinventory = $_POST["f_inventory_".$speid];
				if(!preg_match("/^0|\d*\.?\d+$/i", $spemprice, $matches)){
					echo '{"state": 200, "info": "规格表中价格不得为空，类型为数字！"}';
					exit();
				}elseif(!preg_match("/^0|\d*\.?\d+$/i", $speprice, $matches)){
					echo '{"state": 200, "info": "规格表中库存不得为空，类型为数字！"}';
					exit();
				}elseif(!preg_match("/^0|\d*\.?\d+$/i", $speinventory, $matches)){
					echo '{"state": 200, "info": "规格表中库存不得为空，类型为数字！"}';
					exit();
				}else{
					$invent += $speinventory;
					array_push($specifival, $speid.",".$spemprice."#".$speprice."#".$speinventory);
				}
			}
		}

		if(!empty($specifival)){
			$specifival = join("|", $specifival);
			$inventory = $invent;
		}else{
			$specifival = "";

			if(!preg_match("/^0|\d*\.?\d+$/i", $inventory, $matches)){
				echo '{"state": 200, "info": "库存不得为空，类型为数字！"}';
				exit();
			}
		}

		if(empty($litpic)){
			echo '{"state": 200, "info": "请上传商品代表图片！"}';
			exit();
		}

		$click = (int)$click;
		$sort = (int)$sort;
		$flag = isset($flag) ? join(',',$flag) : '';

		if(strpos($flag, "3") === false){
			$btime = 0;
			$etime = 0;
		}
		
		if(strpos($flag, "4") === false){
		    $kstime = 0;
		    $ketime = 0;
		}

		if(empty($imglist)){
			echo '{"state": 200, "info": "请上传商品图集！"}';
			exit();
		}

	}

	$templates = "productAdd.html";

	//js
	$jsFile = array(
		'ui/jquery.dragsort-0.5.1.min.js',
		'ui/bootstrap-datetimepicker.min.js',
		'ui/jquery.dragsort-0.5.1.min.js',
		'publicUpload.js',
		'admin/shop/productAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	require_once(HUONIAOINC."/config/shop.inc.php");
	global $customUpload;
	if($customUpload == 1){
		global $custom_thumbSize;
		global $custom_thumbType;
		global $custom_atlasSize;
		global $custom_atlasType;
		$huoniaoTag->assign('thumbSize', $custom_thumbSize);
		$huoniaoTag->assign('thumbType', "*.".str_replace("|", ";*.", $custom_thumbType));
		$huoniaoTag->assign('atlasSize', $custom_atlasSize);
		$huoniaoTag->assign('atlasType', "*.".str_replace("|", ";*.", $custom_atlasType));
	}

	$huoniaoTag->assign('id', $id);

	//遍历所选分类名称，输出格式：分类名 > 分类名
	global $data;
	$data = "";
	$proType = getParentArr("shop_type", $typeid);
	$proType = array_reverse(parent_foreach($proType, "typename"));
	$huoniaoTag->assign('proType', join(" > ", $proType));

	//遍历所选分类ID
	global $data;
	$data = "";
	$proId = array_reverse(parent_foreach(getParentArr("shop_type", $typeid), "id"));
	$proId = array_slice($proId, 0, count($proType));

	//根据分类ID，获取分类属性值
	$itemid = 0;
	if(count($proId) > 0){
		foreach($proId as $key => $val){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_item` WHERE `type` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$itemid = $val;
			}
		}
	}

	//品牌Array
	$brandOption = array();
	array_push($brandOption, '<option value="">请选择</option>');
	$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_brandtype` ORDER BY `weight`");
	$results = $dsql->dsqlOper($archives, "results");
	if($results){
		foreach($results as $key => $val){
			$archives_ = $dsql->SetQuery("SELECT * FROM `#@__shop_brand` WHERE `type` = ".$val['id']." ORDER BY `weight`");
			$results_ = $dsql->dsqlOper($archives_, "results");
			$branditem = array();
			if($results_){
				foreach($results_ as $key_ => $val_){
					$selected = "";
					if($val_['id'] == $brand){
						$selected = " selected";
					}
					array_push($branditem, '<option value="'.$val_['id'].'"'.$selected.'>&nbsp;&nbsp;&nbsp;&nbsp;|--'.$val_['title'].'</option>');
				}
				if(!empty($branditem)){
					array_push($brandOption, '<optgroup label="|--'.$val["typename"].'"></optgroup>');
					array_push($brandOption, join("", $branditem));
				}
			}
		}
	}
	$huoniaoTag->assign('brandOption', join("", $brandOption));


	//运费模板Array
	$logisticOption = array();
	$store = (int)$store;
	array_push($logisticOption, '<option value="0">请选择运费模板</option>');
	$archives = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__shop_logistictemplate` WHERE `sid` = ".$store." ORDER BY `id` DESC");
	$results = $dsql->dsqlOper($archives, "results");
	if($results){
		foreach($results as $key => $val){
			$selected = "";
			if($val["id"] == $logistic){
				$selected = " selected";
			}
			array_push($logisticOption, '<option value="'.$val["id"].'"'.$selected.'>'.$val["title"].'</option>');
		}
	}
	$huoniaoTag->assign('logisticOption', join("", $logisticOption));


	$huoniaoTag->assign('proItemList', join("", getItemList($property, $itemid)));

	//店铺Array
	$storeOption = array();
	array_push($storeOption, '<option value="0">请选择</option>');

    $where = " AND `cityid` in (0,$adminCityIds)";

    //城市管理员
    if($userType == 3){
        if($adminAreaIDs){
                $where .= " AND `addrid` in ($adminAreaIDs)";
        }else{
            $where .= " AND 1 = 2";
        }
    }
	$archives = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__shop_store` WHERE 1=1".$where." ORDER BY `weight`");
	$results = $dsql->dsqlOper($archives, "results");
	if($results){
		foreach($results as $key => $val){
			$selected = "";
			if($val["id"] == $store){
				$selected = " selected";
			}
			array_push($storeOption, '<option value="'.$val["id"].'"'.$selected.'>'.$val["title"].'</option>');
		}
	}
	$huoniaoTag->assign('storeOption', join("", $storeOption));

	//根据分类ID，获取分类属性值
	$itemid1 = 0;
	if(count($proId) > 0){
		foreach($proId as $key => $val){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `spe` != '' AND `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$itemid1 = $val;
			}
		}
	}

	$speArr = getSpeList($specifiList, $itemid1);
	$huoniaoTag->assign('specification', join("", $speArr['specification']));
	$huoniaoTag->assign('specifiVal', json_encode($speArr['specifiVal']));

	//状态
	$huoniaoTag->assign('stateopt', array('0', '1', '2'));
	$huoniaoTag->assign('statenames',array('待审核','已上架','已下架'));
	$huoniaoTag->assign('state', $state == "" ? 1 : $state);

	//其它属性
	$huoniaoTag->assign('flagopt', array('0', '1', '2', '3', '4'));
	$huoniaoTag->assign('flagnames',array('推荐','特价','热卖','限时抢','准点秒'));
	$huoniaoTag->assign('flag', empty($flag) ? "" : explode(",", $flag));

}

//获取商品分类
if($dopost == "getTypeList"){
	$list = array();
	if($tid == 0){
		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `parentid` = 0 ORDER BY `weight`");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach($results as $key => $val){
				$list[$key] = array();
				$list_1 = array();
				$archives_1 = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `parentid` = ".$val['id']." ORDER BY `weight`");
				$results_1 = $dsql->dsqlOper($archives_1, "results");
				if($results_1){
					foreach($results_1 as $key_1 => $val_1){
						$list_1[$key_1]["id"] = $val_1['id'];
						$list_1[$key_1]["typename"] = $val_1['typename'];

						$list_1[$key_1]["type"] = 0;
						$archives_2 = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `parentid` = ".$val_1['id']." ORDER BY `weight`");
						$results_2 = $dsql->dsqlOper($archives_2, "results");
						if($results_2){
							$list_1[$key_1]["type"] = 1;
						}
					}
				}
				$list[$key]["typeid"] = $val['id'];
				$list[$key]["typename"] = $val['typename'];
				$list[$key]["subnav"] = $list_1;
			}
		}
	}else{
		$list = array();
		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `parentid` = ".$tid." ORDER BY `weight`");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach($results as $key => $val){
				$list[$key]["id"] = $val['id'];
				$list[$key]["typename"] = $val['typename'];

				$list[$key]["type"] = 0;
				$archives_1 = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `parentid` = ".$val['id']." ORDER BY `weight`");
				$results_1 = $dsql->dsqlOper($archives_1, "results");
				if($results_1){
					$list[$key]["type"] = 1;
				}
			}
		}
	}
	if(!empty($list)){
		echo '{"state": 100, "info": "获取成功！", "list": '.json_encode($list).'}';
	}else{
		echo '{"state": 200, "info": "获取失败！"}';
	}
	die;

//获取分类的所有父级
}elseif($dopost == "typeParent"){
	//遍历所选分类ID
	global $data;
	$data = "";
	$proId = array_reverse(parent_foreach(getParentArr("shop_type", $typeid), "id"));
	$proId = array_slice($proId, 0, count($proTypeName));
	if(!empty($proId)){
		echo json_encode($proId);
	}
	die;

//获取店铺分类
}elseif($dopost == "getStoreType"){
	if($sid){
		$ids = array();
		if($id != ""){
			$archives = $dsql->SetQuery("SELECT `category` FROM `#@__shop_product` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$ids = explode(",", $results[0]['category']);
			}
		}
		$archives = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__shop_category` WHERE `type` = ".$sid." AND `parentid` = 0 ORDER BY `weight`");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$cList = array();
			foreach($results as $key => $val){
				$selected = "";
				if(in_array($val['id'], $ids)){
					$selected = " selected";
				}
				array_push($cList, '<option value="'.$val['id'].'"'.$selected.'>|--'.$val['typename'].'</option>');
				$archives_ = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__shop_category` WHERE `parentid` = ".$val['id']." ORDER BY `weight`");
				$results_ = $dsql->dsqlOper($archives_, "results");
				if($results_){
					foreach($results_ as $key_ => $val_){
						$selected = "";
						if(in_array($val_['id'], $ids)){
							$selected = " selected";
						}
						array_push($cList, '<option value="'.$val_['id'].'"'.$selected.'>&nbsp;&nbsp;&nbsp;&nbsp;|--'.$val_['typename'].'</option>');
					}
				}
			}
			if(!empty($cList)){
				echo '{"state": 100, "info": "获取成功！", "list": '.json_encode('<option value="">请选择,支持多选</option>'.join("", $cList)).'}';
			}else{
				echo '{"state": 200, "info": "获取失败！"}';
			}
		}
	}
	die;

//上架新商品
}elseif($dopost == "save"){

	//保存到主表
	$archives = $dsql->SetQuery("INSERT INTO `#@__shop_product` (`type`, `title`, `brand`, `property`, `store`, `category`, `mprice`, `price`, `logistic`, `volume`, `weight`, `specification`, `inventory`, `limit`, `litpic`, `sort`, `click`, `state`, `flag`, `btime`, `etime`, `pics`, `body`, `mbody`, `pubdate`, `video`, `kstime`, `ketime`, `fx_reward`) VALUES ('$typeid', '$title', '$brand', '$property', '$store', '$category', '$mprice', '$price', '$logistic', '$volume', '$weight', '$specifival', '$inventory', '$limit', '$litpic', '$sort', '$click', '$state', '$flag', '$btime', '$etime', '$imglist', '$body', '$mbody', ".GetMkTime(time()).", '$video', '$kstime', '$ketime', '$fx_reward')");
	$aid = $dsql->dsqlOper($archives, "lastid");

	if($aid){
		adminLog("上架新商品", $title);

		$param = array(
			"service"  => "shop",
			"template" => "detail",
			"id"       => $aid
		);
		$url = getUrlPath($param);

		echo '{"state": 100, "url": "'.$url.'"}';die;
	}else{
		echo '{"state": 200, "info": "添加失败！"}';die;
	}

//修改商品
}elseif($dopost == "edit"){
	//表单验证
	if($submit == "提交"){

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__shop_product` SET `type` = '$typeid', `title` = '$title', `brand` = '$brand', `property` = '$property', `store` = '$store', `category` = '$category', `mprice` = '$mprice', `price` = '$price', `logistic` = '$logistic', `volume` = '$volume', `weight` = '$weight', `specification` = '$specifival', `inventory` = '$inventory', `limit` = '$limit', `litpic` = '$litpic', `sort` = '$sort', `click` = '$click', `state` = '$state', `flag` = '$flag', `btime` = '$btime', `etime` = '$etime', `pics` = '$imglist', `body` = '$body', `mbody` = '$mbody', `video` = '$video', `kstime` = '$kstime', `ketime` = '$ketime', `fx_reward` = '$fx_reward' WHERE `id` = ".$_POST['id']);
		$results = $dsql->dsqlOper($archives, "update");

		if($results == "ok"){
			adminLog("修改商城商品", $title);

			$param = array(
				"service"  => "shop",
				"template" => "detail",
				"id"       => $_POST['id']
			);
			$url = getUrlPath($param);

			echo '{"state": 100, "info": "修改成功！", "url": "'.$url.'"}';die;
		}else{
			echo '{"state": 200, "info": "修改失败！"}';die;
		}

		die;
	}else{
		$huoniaoTag->assign('title', $title);
		$huoniaoTag->assign('brand', $brand);
		$huoniaoTag->assign('store', $store);
		$huoniaoTag->assign('category', $category);
		$huoniaoTag->assign('mprice', $mprice);
		$huoniaoTag->assign('price', $price);
		$huoniaoTag->assign('logistic', $logistic);
		$huoniaoTag->assign('volume', $volume);
		$huoniaoTag->assign('weight', $weight);
		$huoniaoTag->assign('inventory', $inventory);
		$huoniaoTag->assign('limit', $limit);
		$huoniaoTag->assign('btime', !empty($btime) ? date("Y-m-d H:i:s", $btime) : "");
		$huoniaoTag->assign('etime', !empty($etime) ? date("Y-m-d H:i:s", $etime) : "");
		$huoniaoTag->assign('kstime', !empty($kstime) ? date("Y-m-d H:i:s", $kstime) : "");
		$huoniaoTag->assign('ketime', !empty($ketime) ? date("Y-m-d H:i:s", $ketime) : "");
		$huoniaoTag->assign('litpic', $litpic);
		$huoniaoTag->assign('sort', $sort);
		$huoniaoTag->assign('click', $click);
		$imglist = array();
		if(!empty($pics)){
			$imglist = explode(",", $pics);
		}
		$huoniaoTag->assign('body', $body);
		$huoniaoTag->assign('mbody', $mbody);
		$huoniaoTag->assign('video', $video);
		$huoniaoTag->assign('fx_reward', $fx_reward);
	}
}

//获取属性
function getItemList($property, $itemid){
	global $dsql;
	//获取分类属性
	$proItemList = array();
	$propertyArr = array();
	$propertyIds = array();
	$propertyVal = array();
	if(!empty($property)){
		$propertyArr = explode("|", $property);
		foreach($propertyArr as $key => $val){
			$value = explode("#", $val);
			array_push($propertyIds, $value[0]);
			array_push($propertyVal, $value[1]);
		}
	}
	if($itemid != 0){
		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_item` WHERE `type` = ".$itemid." AND `parentid` = 0 ORDER BY `weight`");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach($results as $key => $val){

				$id = $val['id'];
				$typeName = $val['typename'];
				$r = strstr($val['flag'], 'r');
				$w = strstr($val['flag'], 'w');
				$c = strstr($val['flag'], 'c');

				$archives_ = $dsql->SetQuery("SELECT * FROM `#@__shop_item` WHERE `parentid` = ".$val['id']." ORDER BY `weight`");
				$results_ = $dsql->dsqlOper($archives_, "results");

				if($results_){
					$listItem = array();
					$requri = $requri_ = "";
					if($r){
						$requri = ' data-required="true"';
						$requri_ = '<font color="#f00">*</font>';
					}
					$properVal = array();
					if(!empty($propertyIds) && $_GET['typeid'] == ""){
						$found = array_search($id, $propertyIds);
						$properVal = $propertyVal[$found];
					}else{
						$properVal = "";
					}

					//可输入
					if($w){
						array_push($listItem, '<input type="text" name="item'.$id.'" id="item'.$id.'"'.$requri.' placeholder="点击选择或直接输入内容" data-regex="\S+" value="'.$properVal.'" />');
						if($r){
							array_push($listItem, '<span class="input-tips"><s></s>请选择或直接输入'.$typeName.'属性</span>');
						}
						array_push($listItem, '<div class="popup_key"><ul>');
						foreach($results_ as $key_ => $val_){
							array_push($listItem, '<li data-id="'.$val_['id'].'" title="'.$val_['typename'].'">'.$val_['typename'].'</li>');
						}
						array_push($listItem, '</ul></div>');

					//多选
					}elseif($c){

						$properVal = array();
						if(!empty($propertyIds) && $_GET['typeid'] == ""){
							$found = array_search($id, $propertyIds);
							if($found){
								$properVal = explode(",", $propertyVal[$found]);
							}
						}

						foreach($results_ as $key_ => $val_){

							$checked = "";
							if(in_array($val_['id'], $properVal)){
								$checked = " checked";
							}

							array_push($listItem, '<label><input type="checkbox" name="item'.$id.'[]" value="'.$val_['id'].'"'.$requri.$checked.' />'.$val_['typename'].'</label>');
						}
						if($r){
							array_push($listItem, '<span class="input-tips"><s></s>请选择'.$typeName.'属性</span>');
						}

						array_push($listItem, '<br /><span class="label label-info checkAll" style="margin-top:5px;">全选</span>');

					//下拉菜单
					}else{
						array_push($listItem, '<span><select name="item'.$id.'" id="item'.$id.'" class="input-large"'.$requri.'>');
						array_push($listItem, '<option value="">请选择</option>');
						foreach($results_ as $key_ => $val_){
							$selected = "";
							if($val_['id'] == $properVal){
								$selected = " selected";
							}

							array_push($listItem, '<option value="'.$val_['id'].'"'.$selected.'>'.$val_['typename'].'</option>');
						}
						array_push($listItem, '</select></span>');
						if($r){
							array_push($listItem, '<span class="input-tips"><s></s>请选择'.$typeName.'属性</span>');
						}
					}

					if(!empty($listItem)){
						array_push($proItemList, '<dl class="clearfix"><dt><label for="item'.$id.'">'.$typeName.'：'.$requri_.'</label></dt>');
						$cla = $c ? ' class="radio"' : "";
						$pos = $w ? ' style="position:static;"' : "";
						array_push($proItemList, '<dd'.$cla.$pos.'>'.join("", $listItem).'</dd>');
						array_push($proItemList, '</dl>');
					}

				}
			}
		}
	}
	return $proItemList;
}

//获取规格
function getSpeList($specifiList, $itemid){
	global $dsql;
	//获取分类规格
	$specification = array();
	$specifiArr = array();
	$specifiIds = array();
	$specifiVal = array();
	if(!empty($specifiList) && $_GET['typeid'] == ""){
		$specifiArr = explode("|", $specifiList);
		foreach($specifiArr as $key => $val){
			$value = explode(",", $val);
			$ids = explode("-", $value[0]);
			foreach($ids as $key_ => $val_){
				if(!in_array($val_, $specifiIds)){
					array_push($specifiIds, $val_);
				}
			}
			array_push($specifiVal, $value[1]);
		}
	}
	if($itemid != 0){
		$archives = $dsql->SetQuery("SELECT `spe` FROM `#@__shop_type` WHERE `id` = ".$itemid);
		$results = $dsql->dsqlOper($archives, "results");
		if($results && !empty($results[0]['spe'])){
			$spe = explode(",", $results[0]['spe']);
			foreach($spe as $key => $val){
				$archives_1 = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__shop_specification` WHERE `id` = ".$val);
				$results_1 = $dsql->dsqlOper($archives_1, "results");
				if($results_1){
					$speItem = array();
					foreach($results_1 as $key_1 => $val_1){
						$archives_2 = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__shop_specification` WHERE `parentid` = ".$val_1['id']);
						$results_2 = $dsql->dsqlOper($archives_2, "results");
						if($results_2){
							foreach($results_2 as $key_2 => $val_2){
								$checked = "";
								if(in_array($val_2['id'], $specifiIds)){
									$checked = " checked";
								}
								array_push($speItem, '<label><input type="checkbox" name="spe'.$val.'[]" id="spe'.$val.'" title="'.$val_2['typename'].'" value="'.$val_2['id'].'"'.$checked.' />'.$val_2['typename'].'</label>');
							}
						}
					}
					if($speItem){
						array_push($specification, '<dl class="clearfix"><dt><label>'.$results_1[0]['typename'].'：</label></dt>');
						array_push($specification, '<dd class="radio" data-title="'.$results_1[0]['typename'].'" data-id="'.$results_1[0]['id'].'"><div class="clearfix">'.join("", $speItem).'</div><span class="label label-info checkAll" style="margin-top:5px;">全选</span></dd>');
						array_push($specification, '</dl>');
					}
				}
			}
		}
	}
	return array("specifiVal" => $specifiVal, "specification" => $specification);
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	$huoniaoTag->assign('dopost', $dopost ? $dopost : "save");
	$huoniaoTag->assign('imglist', json_encode(!empty($imglist) ? $imglist : array()));
	$huoniaoTag->assign('itemid', $itemid);
	$huoniaoTag->assign('click', $click == "" ? "1" : $click);
	$huoniaoTag->assign('sort', $sort == "" ? "1" : $sort);
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/shop";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
