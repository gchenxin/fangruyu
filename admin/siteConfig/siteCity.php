<?php
/**
 * 分站城市管理
 *
 * @version        $Id: siteCity.php 2018-01-11 下午14:46:24 $
 * @package        HuoNiao.siteConfig
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("siteCity");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/siteConfig";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "siteCity.html";


//开通城市
if($dopost == "add"){

	if(empty($cid)) die('{"state": 200, "info": "请选择所属城市"}');
	if($type === "") die('{"state": 200, "info": "请选择域名类型"}');
	if(empty($domain)) die('{"state": 200, "info": "请输入要绑定的域名"}');

	//查询是否已经开通
	$sql = $dsql->SetQuery("SELECT * FROM `#@__site_city` WHERE `cid` = ".$cid);
	$count = $dsql->dsqlOper($sql, "totalCount");
	if($count > 0) die('{"state": 200, "info": "您选择的城市已经开通，无需再次开通"}');

	//验证域名是否被使用
	if(!operaDomain('check', $domain, "siteConfig", 'city'))
	die('{"state": 200, "info": '.json_encode("域名已被占用，请重试！").'}');

	//新增
	$sql = $dsql->SetQuery("INSERT INTO `#@__site_city` (`cid`, `type`, `config`) VALUE ('$cid', '$type', '')");
	$lid = $dsql->dsqlOper($sql, "lastid");

	if(is_numeric($lid)){
		//域名操作
		operaDomain('update', $domain, 'siteConfig', "city", $cid);

        //更新缓存
        updateMemory();

		echo '{"state": 100, "info": "开通成功！"}';
	}else{
		die('{"state": 200, "info": "开通失败"}');
	}

	die;


//更新
}elseif($dopost == "update"){

	if(empty($id)) die('{"state": 200, "info": "Error"}');
	if($type === "") die('{"state": 200, "info": "请选择域名类型"}');
	if(empty($domain)) die('{"state": 200, "info": "请输入要绑定的域名"}');

	$sql = $dsql->SetQuery("SELECT `cid` FROM `#@__site_city` WHERE `cid` = $id");
	$ret = $dsql->dsqlOper($sql, "results");
	if($ret){

		$cid = $ret[0]['cid'];

		//验证域名是否被使用
		if(!operaDomain('check', $domain, "siteConfig", 'city', $cid))
		die('{"state": 200, "info": '.json_encode("域名已被占用，请重试！").'}');


		//域名操作
		operaDomain('update', $domain, 'siteConfig', "city", $cid);

		$sql = $dsql->SetQuery("UPDATE `#@__site_city` SET `type` = '$type' WHERE `cid` = ".$id);
		$dsql->dsqlOper($sql, "update");

        //更新缓存
        updateMemory();


		echo '{"state": 100, "info": "修改成功！"}';
		die;
	}else{
		echo '{"state": 100, "info": "城市信息获取失败！"}';
		die;
	}


//删除
}elseif($dopost == "del"){

	if(empty($id)) die('{"state": 200, "info": "Error"}');

	$sql = $dsql->SetQuery("SELECT `cid` FROM `#@__site_city` WHERE `id` = $id");
	$ret = $dsql->dsqlOper($sql, "results");
	if($ret){

		$cid = $ret[0]['cid'];

		$archives = $dsql->SetQuery("DELETE FROM `#@__site_city` WHERE `id` = ".$id);
		$dsql->dsqlOper($archives, "update");

		//删除域名配置
		$archives = $dsql->SetQuery("DELETE FROM `#@__domain` WHERE `module` = 'siteConfig' AND `part` = 'city' AND `iid` = ".$cid);
		$dsql->dsqlOper($archives, "update");

        //更新缓存
        updateMemory();

		echo '{"state": 100, "info": "删除成功！"}';
		die;
	}else{
		die('{"state": 200, "info": "Error"}');
	}


//设置默认城市
}elseif($dopost == "setDefaultCity"){

	if(empty($type) || empty($cid)) die('{"state": 200, "info": "Error"}');

	$sql = $dsql->SetQuery("UPDATE `#@__site_city` SET `default` = 0");
	$dsql->dsqlOper($sql, "update");

	//设置默认城市
	if($type == 'set'){
		$sql = $dsql->SetQuery("UPDATE `#@__site_city` SET `default` = 1 WHERE `cid` = $cid");
		$dsql->dsqlOper($sql, "update");
	}

    //更新缓存
    updateMemory();

	echo '{"state": 100, "info": "设置成功！"}';die;

//设置热门城市
}elseif($dopost == "hot"){

	if(empty($id)) die('{"state": 200, "info": "Error"}');

	$sql = $dsql->SetQuery("SELECT `cid` FROM `#@__site_city` WHERE `cid` = $id");
	$ret = $dsql->dsqlOper($sql, "results");
	if($ret){

		$cid = $ret[0]['cid'];
		$state = empty($state) ? 0 : $state;

		$archives = $dsql->SetQuery("UPDATE `#@__site_city` SET `hot` = '$state' WHERE `cid` = ".$id);
		$dsql->dsqlOper($archives, "update");

        //更新缓存
        updateMemory();

		echo '{"state": 100, "info": "修改成功！"}';
		die;
	}else{
		die('{"state": 200, "info": "Error"}');
	}

}



//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'admin/siteConfig/siteCity.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	//获取模块域名配置数据
	$domainArr = array();
	global $cfg_basehost;

	$sql = $dsql->SetQuery("SELECT c.*, a.`typename`, a.`id` aid FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` ORDER BY c.`id`");
	$result = $dsql->dsqlOper($sql, "results");
	if($result){
		foreach ($result as $key => $value) {

			$domainInfo = getDomain('siteConfig', 'city', $value['cid']);
			$domainArr[] = array(
				"id" => $value['id'],
				"aid" => $value['aid'],
				"name" => $value['typename'],
				"type" => $value['type'],
				"default" => $value['default'],
				"typeName" => getDomainTypeName($value['type']),
				"domain" => $domainInfo['domain'],
				"hot" => $value['hot'],
			);

		}
	}
	$huoniaoTag->assign('domainArr', json_encode($domainArr));

	//省
	$province = $dsql->getTypeList(0, "site_area", false);
	$huoniaoTag->assign('province', $province);

	$huoniaoTag->assign('basehost', $cfg_basehost);

	$huoniaoTag->assign('domaintype', array('0', '1', '2'));
	$huoniaoTag->assign('domaintypeNames',array('主域名', '子域名','子目录'));
	$huoniaoTag->assign('domaintypeChecked', 2);

    $huoniaoTag->assign('cfg_sameAddr_state', (int)$cfg_sameAddr_state);


	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/siteConfig";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}


function getDomainTypeName($type){
	$typeName = "";
	switch ($type) {
		case 0:
			$typeName = "主域名";
			break;
		case 1:
			$typeName = "子域名";
			break;
		case 2:
			$typeName = "子目录";
			break;
	}
	return $typeName;
}


//更新缓存
function updateMemory(){
    global $HN_memory;

    //清除缓存
    $HN_memory->rm('site_city');
    unlinkFile(HUONIAOROOT . '/system_site_city.json');

    //重新生成缓存
    $handels = new handlers('siteConfig', 'siteCity');
    $handels->getHandle();

}