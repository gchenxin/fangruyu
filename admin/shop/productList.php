<?php
/**
 * 管理商城商品
 *
 * @version        $Id: productList.php 2014-2-11 下午17:26:10 $
 * @package        HuoNiao.Shop
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("productList");
$dsql  = new dsql($dbo);
$tpl   = dirname(__FILE__)."/../templates/shop";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "productList.html";

$tab = "shop_product";

if($dopost == "getList"){
	$pagestep = $pagestep == "" ? 10 : $pagestep;
	$page     = $page == "" ? 1 : $page;

	$where = "";

    $where2 = " AND `cityid` in (0,$adminCityIds)";

    if ($adminCity){
        $where2 = " AND `cityid` = $adminCity";
    }
    $houseid = array();
    $loupanSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__shop_store` WHERE 1=1".$where2);
    $loupanResult = $dsql->dsqlOper($loupanSql, "results");
    if($loupanResult){
        foreach($loupanResult as $key => $loupan){
            array_push($houseid, $loupan['id']);
        }
        $where .= " AND `store` in (".join(",", $houseid).")";
    }else{
        echo '{"state": 101, "info": ' . json_encode("暂无相关信息") . ', "pageInfo": {"totalPage": 0, "totalCount": 0, "totalGray": 0, "totalAudit": 0, "totalRefuse": 0}}';
        die;
    }

    if($sKeyword != ""){
		$storeSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__shop_store` WHERE `title` like '%$sKeyword%'".$where2);
		$storeResult = $dsql->dsqlOper($storeSql, "results");
		if($storeResult){
			$storeid = array();
			foreach($storeResult as $key => $store){
				array_push($storeid, $store['id']);
			}
            $where .= " AND `title` like '%$sKeyword%' OR `store` in (".join(",", $storeid).")";
		}else{
            $storeSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__shop_store` WHERE 1=1".$where2);
            $storeResult = $dsql->dsqlOper($storeSql, "results");
            if($storeResult){
                $storeid = array();
                foreach($storeResult as $key => $store){
                    array_push($storeid, $store['id']);
                }
                $where .= " AND `title` like '%$sKeyword%' AND `store` in (".join(",", $storeid).")";
            }
        }
	}

	if($sIndustry != ""){
		if($dsql->getTypeList($sIndustry, "shop_type")){
			$lower = arr_foreach($dsql->getTypeList($sIndustry, "shop_type"));
			$lower = $sIndustry.",".join(',',$lower);
		}else{
			$lower = $sIndustry;
		}
		$where .= " AND `type` in ($lower)";
	}

	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE 1 = 1");

	//总条数
	$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
	//总分页数
	$totalPage = ceil($totalCount/$pagestep);
	//待审核
	$totalGray = $dsql->dsqlOper($archives." AND `state` = 0".$where, "totalCount");
	//已上架
	$totalAudit = $dsql->dsqlOper($archives." AND `state` = 1".$where, "totalCount");
	//已下架
	$totalRefuse = $dsql->dsqlOper($archives." AND `state` = 2".$where, "totalCount");

	if($state != ""){
		$where .= " AND `state` = $state";

		if($state == 0){
			$totalPage = ceil($totalGray/$pagestep);
		}elseif($state == 1){
			$totalPage = ceil($totalAudit/$pagestep);
		}elseif($state == 2){
			$totalPage = ceil($totalRefuse/$pagestep);
		}
	}

	$where .= " order by `id` desc, `pubdate` desc";

	$atpage = $pagestep*($page-1);
	$where .= " LIMIT $atpage, $pagestep";
	$archives = $dsql->SetQuery("SELECT `id`, `type`, `title`, `litpic`, `store`, `price`, `inventory`, `sales`, `state`, `flag`, `pubdate` FROM `#@__".$tab."` WHERE 1 = 1".$where);
	$results = $dsql->dsqlOper($archives, "results");

	if(count($results) > 0){
		$list = array();
		foreach ($results as $key=>$value) {
			$list[$key]["id"] = $value["id"];
			$list[$key]["title"] = $value["title"];
			$list[$key]["litpic"] = str_replace("large", "small", $value["litpic"]);

			//店铺
			$list[$key]["storeid"] = $value["store"];
			if($value["store"] != 0){
				$storeSql = $dsql->SetQuery("SELECT `title`,`addrid` FROM `#@__shop_store` WHERE `id` = ". $value["store"]);
				$storename = $dsql->getTypeName($storeSql);
				$list[$key]["store"] = $storename[0]['title'];

				$param = array(
					"service"  => "shop",
					"template" => "store-detail",
					"id"       => $value['store']
				);
				$list[$key]["storeUrl"] = getUrlPath($param);

                //区域
                if($storename[0]['addrid'] == 0){
                    $list[$key]["addrname"] = "未知";
                }else{
                    $addrname = getPublicParentInfo(array('tab' => 'site_area', 'id' => $storename[0]['addrid'], 'type' => 'typename', 'split' => ' '));
                    $list[$key]["addrname"] = $addrname;
                }

			}else{
				$list[$key]["store"] = "官方直营";
                $list[$key]["addrname"] = "未知";
			}

			$list[$key]["price"]     = $value['price'];
			$list[$key]["inventory"] = $value["inventory"];
			$list[$key]["sales"]     = $value["sales"];


			//行业
			$list[$key]["typeid"] = $value["type"];
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__shop_type` WHERE `id` = ". $value["type"]);
			$typename = $dsql->getTypeName($typeSql);
			$list[$key]["typename"] = $typename[0]['typename'];

			$list[$key]["state"] = $value["state"];

			$append = array();
			if(!empty($value["flag"])){
				$flag = explode(",", $value["flag"]);
				if(in_array(0, $flag)){
					array_push($append, "推");
				}
				if(in_array(1, $flag)){
					array_push($append, "特");
				}
				if(in_array(2, $flag)){
					array_push($append, "热");
				}
			}
			$list[$key]["flag"] = join(",", $append);

			$list[$key]["pubdate"] = date('Y-m-d H:i:s', $value["pubdate"]);

			$param = array(
				"service"  => "shop",
				"template" => "detail",
				"id"       => $value['id']
			);
			$list[$key]["url"] = getUrlPath($param);
		}

		if(count($list) > 0){
			echo '{"state": 100, "info": '.json_encode("获取成功").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}, "productList": '.json_encode($list).'}';
		}else{
			echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}}';
		}

	}else{
		echo '{"state": 101, "info": '.json_encode("暂无相关信息").', "pageInfo": {"totalPage": '.$totalPage.', "totalCount": '.$totalCount.', "totalGray": '.$totalGray.', "totalAudit": '.$totalAudit.', "totalRefuse": '.$totalRefuse.'}}';
	}
	die;

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
			//删除评论
			$archives = $dsql->SetQuery("DELETE FROM `#@__shop_common` WHERE `aid` = ".$val);
			$dsql->dsqlOper($archives, "update");

			$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");

			//删除缩略图
			array_push($title, $results[0]['title']);
			delPicFile($results[0]['litpic'], "delThumb", "shop");

			//删除图集
			$pics = explode(",", $results[0]['pics']);
			foreach($pics as $k => $v){
				delPicFile($v, "delAtlas", "shop");

			}

			//删除内容图片
			$body = $results[0]['body'];
			if(!empty($body)){
				delEditorPic($body, "shop");
			}

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

//更新状态
}elseif($dopost == "updateState"){
	if(!testPurview("productEdit")){
		die('{"state": 200, "info": '.json_encode("对不起，您无权使用此功能！").'}');
	};
	$each = explode(",", $id);
	$error = array();
	if($id != ""){
		foreach($each as $val){
			$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `state` = ".$state." WHERE `id` = ".$val);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				$error[] = $val;
			}
		}
		if(!empty($error)){
			echo '{"state": 200, "info": '.json_encode($error).'}';
		}else{
			adminLog("更新商城商品状态", $id."=>".$state);
			echo '{"state": 100, "info": '.json_encode("修改成功！").'}';
		}
	}
	die;

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
		'ui/jquery-ui-selectable.js',
        'ui/chosen.jquery.min.js',
		'admin/shop/productList.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));
	$huoniaoTag->assign('notice', $notice);

    $huoniaoTag->assign('cityList', json_encode($adminCityArr));
	$huoniaoTag->assign('industryListArr', json_encode($dsql->getTypeList(0, "shop_type")));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/shop";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}

//获取行业分类列表
function getTypeList($id, $tab){
	global $dsql;
	$sql = $dsql->SetQuery("SELECT `id`, `parentid`, `typename` FROM `#@__".$tab."` WHERE `parentid` = $id ORDER BY `weight`");
	$results = $dsql->dsqlOper($sql, "results");
	if($results){
		return $results;
	}else{
		return '';
	}
}
