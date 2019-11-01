<?php
/**
 * 管理网站地区
 *
 * @version        $Id: siteAddr.php 2015-10-24 上午9:26:10 $
 * @package        HuoNiao.Config
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("siteAddr");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/siteConfig";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "siteAddr.html";

$db = "site_area";

//修改分类
if($dopost == "updateType"){

	$value = $_REQUEST['value'];

	if($id != ""){
		$archives = $dsql->SetQuery("SELECT * FROM `#@__$db` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");

		if(!empty($results)){

			if($action == "single"){

				//天气代码
				if($type == "weather_code"){
					if($results[0]['weather_code'] != $value){
						$archives = $dsql->SetQuery("UPDATE `#@__$db` SET `weather_code` = '$value' WHERE `id` = ".$id);
						$results = $dsql->dsqlOper($archives, "update");
					}else{
						die('{"state": 101, "info": '.json_encode('无变化！').'}');
					}

				//名称
				}else if($type == "name"){
					if($value == "") die('{"state": 101, "info": '.json_encode('请输入内容').'}');
					if($results[0]['typename'] != $value){
						$pinyin = GetPinyin($value);
						$archives = $dsql->SetQuery("UPDATE `#@__$db` SET `typename` = '$value', `pinyin` = '$pinyin' WHERE `id` = ".$id);
						$results = $dsql->dsqlOper($archives, "update");
					}else{
						die('{"state": 101, "info": '.json_encode('无变化！').'}');
					}
				}else if($type == "longitude") {
                    if ($results[0]['longitude'] != $value) {
                        $archives = $dsql->SetQuery("UPDATE `#@__$db` SET `longitude` = '$value' WHERE `id` = " . $id);
                        $results = $dsql->dsqlOper($archives, "update");
                    } else {
                        die('{"state": 101, "info": ' . json_encode('无变化！') . '}');
                    }
                }else{
                    if($results[0]['latitude'] != $value){
                        $archives = $dsql->SetQuery("UPDATE `#@__$db` SET `latitude` = '$value' WHERE `id` = ".$id);
                        $results = $dsql->dsqlOper($archives, "update");
                    }else{
                        die('{"state": 101, "info": '.json_encode('无变化！').'}');
                    }
                }


			}else{
				//天气代码
				if($type == "weather_code"){
					$archives = $dsql->SetQuery("UPDATE `#@__$db` SET `weather_code` = '$value' WHERE `id` = ".$id);

				//名称
				}else if($type == "name"){
					if($value == "") die('{"state": 101, "info": '.json_encode('请输入内容').'}');
					$value  = cn_substrR($value,30);
					$pinyin = GetPinyin($value);
					$archives = $dsql->SetQuery("UPDATE `#@__$db` SET `typename` = '$value', `pinyin` = '$pinyin' WHERE `id` = ".$id);

                //经度
				}else if($type == "longitude") {
                    $archives = $dsql->SetQuery("UPDATE `#@__$db` SET `longitude` = '$value' WHERE `id` = " . $id);

                //纬度
                }else{
                    $archives = $dsql->SetQuery("UPDATE `#@__$db` SET `latitude` = '$value' WHERE `id` = ".$id);
                }
				$results  = $dsql->dsqlOper($archives, "update");
			}

			if($results != "ok"){
				die('{"state": 101, "info": '.json_encode('修改失败，请重试！').'}');
			}else{
				$title = $type == "weather_code" ? "城市天气ID" : "";
				adminLog("修改网站地区".$title, $value);
				die('{"state": 100, "info": '.json_encode('修改成功！').'}');
			}

		}else{
			die('{"state": 101, "info": '.json_encode('要修改的信息不存在或已删除！').'}');
		}
	}
	die;

//删除分类
}else if($dopost == "del"){
	if($id != ""){

		$idsArr = array();
		$idexp = explode(",", $id);

		//获取所有子级
		foreach ($idexp as $k => $id) {
			$childArr = $dsql->getTypeList($id, $db, 1);
			if(is_array($childArr)){
				global $data;
				$data = "";
				$idsArr = array_merge($idsArr, array_reverse(parent_foreach($childArr, "id")));
			}
			$idsArr[] = $id;
		}

		$archives = $dsql->SetQuery("DELETE FROM `#@__$db` WHERE `id` in (".join(",", $idsArr).")");
		$dsql->dsqlOper($archives, "update");

		adminLog("删除网站地区", join(",", $idsArr));
		die('{"state": 100, "info": '.json_encode('删除成功！').'}');

	}
	die;

//更新信息分类
}else if($dopost == "typeAjax"){
	$data = str_replace("\\", '', $_POST['data']);
	if($data != ""){
		$json = json_decode($data);

		$json = objtoarr($json);

		$parentid = 0;
		$level = 1;
		if(!empty($did)){
			$parentid = $did;
			$level = 2;
		}elseif(!empty($cid)){
			$parentid = $cid;
			$level = 3;
		}elseif(!empty($pid)){
			$parentid = $pid;
			$level = 4;
		}

		$json = typeOpera($json, $parentid, $db, $level);
		echo $json;
	}
	die;
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/jquery.dragsort-0.5.1.min.js',
		'ui/jquery-ui-sortable.js',
		'admin/siteConfig/siteAddr.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('action', $action);


	//省
	$province = $dsql->getTypeList(0, $db, false);
	$huoniaoTag->assign('province', $province);
	$listArr = $province;

	$pid = (int)$pid;
	$pname = "--省份--";
	$huoniaoTag->assign('pid', $pid);
	if($pid){
		$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__$db` WHERE `id` = ".$pid);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$pname = $results[0]['typename'];
		}

		//市
		$city = $dsql->getTypeList($pid, $db, false);
		$huoniaoTag->assign('city', $city);
		$listArr = $city;
	}
	$huoniaoTag->assign('pname', $pname);


	//市
	$cid = (int)$cid;
	$cname = "--城市--";
	if($cid){
		$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__$db` WHERE `id` = ".$cid);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$cname = $results[0]['typename'];
		}

		//州县
		$district = $dsql->getTypeList($cid, $db, false);
		$huoniaoTag->assign('district', $district);
		$listArr = $district;
	}
	$huoniaoTag->assign('cid', $cid);
	$huoniaoTag->assign('cname', $cname);


	//市
	$did = (int)$did;
	$dname = "--州县--";
	if($did){
		$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__$db` WHERE `id` = ".$did);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$dname = $results[0]['typename'];
		}

		//城镇
		$town = $dsql->getTypeList($did, $db, false);
		$listArr = $town;
	}
	$huoniaoTag->assign('did', $did);
	$huoniaoTag->assign('dname', $dname);

	$huoniaoTag->assign('typeListArr', json_encode($listArr));


	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/siteConfig";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}



function typeOpera($arr, $pid = 0, $db, $level){
	$dsql = new dsql($dbo);

	if (!is_array($arr) && $arr != NULL) {
		return '{"state": 200, "info": "保存失败！"}';
	}
	for($i = 0; $i < count($arr); $i++){
		$id = $arr[$i]["id"];
		$name = $arr[$i]["name"];
		$weather = $arr[$i]["weather"];
		$pinyin = GetPinyin($name);
        $longitude = $arr[$i]["longitude"];
        $latitude = $arr[$i]["latitude"];

		//如果ID为空则向数据库插入下级分类
		if($id == "" || $id == 0){
			$archives = $dsql->SetQuery("INSERT INTO `#@__".$db."` (`parentid`, `typename`, `pinyin`, `level`, `weather_code`, `weight`, `longitude`, `latitude`) VALUES ('$pid', '$name', '$pinyin', '$level', '$weather', '$i', '$longitude', '$latitude')");
			$id = $dsql->dsqlOper($archives, "lastid");

			adminLog("添加网站地区", $name);
		}
		//其它为数据库已存在的分类需要验证名称或天气ID是否有改动，如果有改动则UPDATE
		else{
			$archives = $dsql->SetQuery("SELECT `typename`, `weather_code`, `weight`, `longitude`, `latitude` FROM `#@__".$db."` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "results");
			if(!empty($results)){
				//验证名称
				if($results[0]["typename"] != $name){
					$archives = $dsql->SetQuery("UPDATE `#@__".$db."` SET `typename` = '$name', `pinyin` = '$pinyin' WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					adminLog("修改网站地区名称", $name);
				}
				//验证分类名
				if($results[0]["weather_code"] != $weather){
					$archives = $dsql->SetQuery("UPDATE `#@__".$db."` SET `weather_code` = '$weather' WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					adminLog("修改网站地区城市天气ID", $name."=>".$weather);
				}

                //验证分类名
                if(($results[0]["longitude"] != $longitude) || ($results[0]["latitude"] != $latitude)){
                    $archives = $dsql->SetQuery("UPDATE `#@__".$db."` SET `longitude` = '$longitude', `latitude` = '$latitude' WHERE `id` = ".$id);
                    $dsql->dsqlOper($archives, "update");

                    adminLog("修改网站地区经纬度", $name."=>".$weather);
                }

				//验证排序
				if($results[0]["weight"] != $i){
					$archives = $dsql->SetQuery("UPDATE `#@__".$db."` SET `weight` = '$i' WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					adminLog("修改网站地区排序", $name."=>".$i);
				}


			}
		}
	}
	return '{"state": 100, "info": "保存成功！"}';
}
