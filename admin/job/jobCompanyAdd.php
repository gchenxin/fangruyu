<?php
/**
 * 添加招聘企业
 *
 * @version        $Id: jobCompanyAdd.php 2014-3-17 上午09:07:10 $
 * @package        HuoNiao.Job
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$tpl = dirname(__FILE__)."/../templates/job";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "jobCompanyAdd.html";

$tab = "job_company";
$dopost = $dopost ? $dopost : "save";        //操作类型 save添加 edit修改
if($dopost == "edit"){
	$pagetitle = "修改招聘企业";
	checkPurview("jobCompanyEdit");
}else{
	$pagetitle = "添加招聘企业";
	checkPurview("jobCompanyAdd");
}

if(empty($domaintype)) $domaintype = 0;
if(empty($domainexp)) $domainexp = 0;
$domainexp = empty($domainexp) ? 0 : GetMkTime($domainexp);
if(empty($userid)) $userid = 0;
if(empty($weight)) $weight = 1;
if(empty($state)) $state = 0;
if(!empty($property)) $property = join(",", $property);
if(!empty($welfare)) $welfare = join(",", $welfare);

if($_POST['submit'] == "提交"){

	if(empty($postcode)) $postcode = 0;

	if($token == "") die('token传递失败！');
	//二次验证
	if(empty($title)){
		echo '{"state": 200, "info": "请输入公司名称！"}';
		exit();
	}

	if($domaintype != 0){
		if(empty($domain)){
			echo '{"state": 200, "info": "请输入要绑定的域名！"}';
			exit();
		}

		//验证域名是否被使用
		if(!operaDomain('check', $domain, 'job', $tab, $id, GetMkTime($domainexp)))
		die('{"state": 200, "info": '.json_encode("域名已被占用，请重试！").'}');
	}

	if(empty($nature)){
		echo '{"state": 200, "info": "请选择公司性质！"}';
		exit();
	}

	if(empty($scale)){
		echo '{"state": 200, "info": "请选择公司规模！"}';
		exit();
	}

	if(empty($industry)){
		echo '{"state": 200, "info": "请选择经营行业！"}';
		exit();
	}

	if(empty($litpic)){
		echo '{"state": 200, "info": "请上传logo！"}';
		exit();
	}

	if($userid == 0 && trim($user) == ''){
		echo '{"state": 200, "info": "请选择会员名"}';
		exit();
	}
	if($userid == 0){
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` = '".$user."'");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			echo '{"state": 200, "info": "会员不存在，请在联想列表中选择"}';
			exit();
		}
		$userid = $userResult[0]['id'];
	}else{
		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			echo '{"state": 200, "info": "会员不存在，请在联想列表中选择"}';
			exit();
		}
	}

	if(empty($people)){
		echo '{"state": 200, "info": "请输入联系人！"}';
		exit();
	}

	if(empty($contact)){
		echo '{"state": 200, "info": "请输入联系电话！"}';
		exit();
	}

	if(trim($addrid) == ""){
		echo '{"state": 200, "info": "请选择区域板块"}';
		exit();
	}

	if(empty($address)){
		echo '{"state": 200, "info": "请输入公司地址！"}';
		exit();
	}

	//检测是否已经注册
	if($dopost == "save"){

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `title` = '".$title."'");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			echo '{"state": 200, "info": "公司名称已被注册，不可以重复添加！"}';
			exit();
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `userid` = '".$userid."'");
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			echo '{"state": 200, "info": "此会员已授权管理其它公司，一个会员不可以管理多个公司！"}';
			exit();
		}

	}else{

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `title` = '".$title."' AND `id` != ". $id);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			echo '{"state": 200, "info": "公司名称已被注册，不可以重复添加！"}';
			exit();
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `userid` = '".$userid."' AND `id` != ". $id);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			echo '{"state": 200, "info": "此会员已授权管理其它公司，一个会员不可以管理多个公司！"}';
			exit();
		}
	}

}

if($dopost == "save" && $submit == "提交"){
	//保存到表
	$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`cityid`, `title`, `domaintype`, `nature`, `scale`, `industry`, `logo`, `userid`, `people`, `contact`, `addrid`, `address`, `lnglat`, `postcode`, `email`, `site`, `body`, `pics`, `weight`, `state`, `property`, `seotitle`, `keywords`, `description`, `pubdate`, `welfare`) VALUES ('$cityid', '$title', '$domaintype', '$nature', '$scale', '$industry', '$litpic', '$userid', '$people', '$contact', '$addrid', '$address', '$lnglat', '$postcode', '$email', '$site', '$body', '$pics', '$weight', '$state', '$property', '$seotitle', '$keywords', '$description', '".GetMkTime(time())."', '$welfare') ");
	//echo $archives;die;
	$aid = $dsql->dsqlOper($archives, "lastid");

	if($aid){
		//域名操作
		operaDomain('update', $domain, 'job', $tab, $aid, GetMkTime($domainexp), $domaintip);

		$param = array(
			"service"  => "job",
			"template" => "company",
			"id"       => $aid
		);
		$url = getUrlPath($param);

		if($state == 1){
			updateCache("job_company_list", 300);
			clearCache("job_company_list", "key");
			clearCache("job_company_total", "key");
		}
		adminLog("添加招聘企业", $title);
		echo '{"state": 100, "url": "'.$url.'"}';
	}else{
		echo '{"state": 200, "info": '.json_encode("保存到数据库失败！").'}';
	}
	die;
}elseif($dopost == "edit"){

	if($submit == "提交"){

		$sql = $dsql->SetQuery("SELECT `state` FROM `#@__".$tab."` WHERE `id` = ".$id);
		$res = $dsql->dsqlOper($sql, "results");
		$state_ = $res[0]['state'];

		//保存到表
		$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `cityid` = '$cityid', `title` = '$title', `domaintype` = '$domaintype', `nature` = '$nature', `scale` = '$scale', `industry` = '$industry', `logo` = '$litpic', `userid` = '$userid', `people` = '$people', `contact` = '$contact', `addrid` = '$addrid', `address` = '$address', `lnglat` = '$lnglat', `postcode` = '$postcode', `email` = '$email', `site` = '$site', `body` = '$body', `pics` = '$pics', `weight` = '$weight', `state` = '$state', `property` = '$property', `seotitle` = '$seotitle', `keywords` = '$keywords', `description` = '$description', `welfare` = '$welfare' WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "update");

		if($results == "ok"){
			//域名操作
			operaDomain('update', $domain, 'job', $tab, $id, GetMkTime($domainexp), $domaintip);

			$param = array(
				"service"  => "job",
				"template" => "company",
				"id"       => $id
			);
			$url = getUrlPath($param);

			// 清除缓存
			checkCache("job_company_list", $id);
			clearCache("job_company_detail", $id);
			if(($state != 1 && $state_ == 1)|| ($state == 1 && $state_ != 1)){
				clearCache("job_company_total", "key");
				if($state == 1){
					clearCache("job_company_list", "key");
				}
			}

			adminLog("修改招聘企业", $title);
			echo '{"state": 100, "info": '.json_encode('修改成功！').', "url": "'.$url.'"}';
		}else{
			echo '{"state": 200, "info": '.json_encode('修改失败！').'}';
		}
		die;
	}

	if(!empty($id)){

		//主表信息
		$archives = $dsql->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");

		if(!empty($results)){

			$title       = $results[0]['title'];
			$domaintype  = $results[0]['domaintype'];

			//获取域名信息
			$domainInfo = getDomain('job', $tab, $id);
			$domain      = $domainInfo['domain'];
			$domainexp   = $domainInfo['expires'];
			$domaintip   = $domainInfo['note'];

			$nature      = $results[0]['nature'];
			$scale       = $results[0]['scale'];
			$industry    = $results[0]['industry'];
			$logo        = $results[0]['logo'];
			$userid      = $results[0]['userid'];
			$people      = $results[0]['people'];
			$contact     = $results[0]['contact'];
			$addrid      = $results[0]['addrid'];
			$address     = $results[0]['address'];
			$lnglat      = $results[0]['lnglat'];
			$postcode    = $results[0]['postcode'];
			$email       = $results[0]['email'];
			$site        = $results[0]['site'];
			$body        = $results[0]['body'];
			$pics        = $results[0]['pics'];
			$weight      = $results[0]['weight'];
			$state       = $results[0]['state'];
			$property    = $results[0]['property'];
			$seotitle    = $results[0]['seotitle'];
			$keywords    = $results[0]['keywords'];
			$description = $results[0]['description'];
            $cityid       = $results[0]['cityid'];
            $welfare       = $results[0]['welfare'];

		}else{
			ShowMsg('要修改的信息不存在或已删除！', "-1");
			die;
		}

	}else{
		ShowMsg('要修改的信息参数传递失败，请联系管理员！', "-1");
		die;
	}

}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'ui/jquery.dragsort-0.5.1.min.js',
		'ui/bootstrap-datetimepicker.min.js',
		'publicUpload.js',
		'publicAddr.js',
		'admin/job/jobCompanyAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	$huoniaoTag->assign('dopost', $dopost);
	$huoniaoTag->assign('pagetitle', $pagetitle);
	require_once(HUONIAOINC."/config/job.inc.php");
	global $cfg_basehost;
	global $customChannelDomain;
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
	$huoniaoTag->assign('mapCity', $cfg_mapCity);
	$huoniaoTag->assign('basehost', $cfg_basehost);

	//获取域名信息
	$domainInfo = getDomain('job', 'config');
	$huoniaoTag->assign('subdomain', $domainInfo['domain']);

	$huoniaoTag->assign('id', $id);

	$huoniaoTag->assign('title', $title);

	global $customSubDomain;
	$huoniaoTag->assign('customSubDomain', $customSubDomain);
	if($customSubDomain != 2){
		$huoniaoTag->assign('domaintype', array('0', '1', '2'));
		$huoniaoTag->assign('domaintypeNames',array('默认','绑定主域名','绑定子域名'));
	}else{
		$huoniaoTag->assign('domaintype', array('0', '1'));
		$huoniaoTag->assign('domaintypeNames',array('默认','绑定主域名'));
	}
	if($customSubDomain == 2 && $domaintype == 2) $domaintype = 0;

	$huoniaoTag->assign('domaintypeChecked', $domaintype == "" ? 0 : $domaintype);
	$huoniaoTag->assign('domain', $domain);
	$huoniaoTag->assign('domainexp', $domainexp == 0 ? "" : date("Y-m-d H:i:s", $domainexp));
	$huoniaoTag->assign('domaintip', $domaintip);

	//公司性质
	$archives = $dsql->SetQuery("SELECT * FROM `#@__jobitem` WHERE `parentid` = 5 ORDER BY `weight` ASC");
	$results = $dsql->dsqlOper($archives, "results");
	$list = array(0 => '请选择');
	foreach($results as $value){
		$list[$value['id']] = $value['typename'];
	}
	$huoniaoTag->assign('natureList', $list);
	$huoniaoTag->assign('nature', $nature == "" ? 0 : $nature);

	//公司规模
	$archives = $dsql->SetQuery("SELECT * FROM `#@__jobitem` WHERE `parentid` = 6 ORDER BY `weight` ASC");
	$results = $dsql->dsqlOper($archives, "results");
	$list = array(0 => '请选择');
	foreach($results as $value){
		$list[$value['id']] = $value['typename'];
	}
	$huoniaoTag->assign('scaleList', $list);
	$huoniaoTag->assign('scale', $scale == "" ? 0 : $scale);

	//经营行业
	$huoniaoTag->assign('industry', $industry == "" ? 0 : $industry);
	$huoniaoTag->assign('industryListArr', json_encode($dsql->getTypeList(0, "job_industry")));

	$huoniaoTag->assign('litpic', $logo);

	$huoniaoTag->assign('userid', $userid);
	$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $userid);
	$username = $dsql->getTypeName($userSql);
	$huoniaoTag->assign('username', $username[0]['username']);

	$huoniaoTag->assign('people', $people);
	$huoniaoTag->assign('contact', $contact);

	//区域
	$huoniaoTag->assign('addrListArr', json_encode($dsql->getTypeList(0, "jobaddr")));
	$huoniaoTag->assign('addrid', $addrid == "" ? 0 : $addrid);

	$huoniaoTag->assign('address', $address);
	$huoniaoTag->assign('lnglat', $lnglat);
	$huoniaoTag->assign('postcode', empty($postcode) ? "" : $postcode);
	$huoniaoTag->assign('email', $email);
	$huoniaoTag->assign('site', $site);
	$huoniaoTag->assign('body', $body);

	$huoniaoTag->assign('picsList', '[]');
	if(!empty($pics)){
		$picsArr = array();
		$pics = explode("###", $pics);
		foreach ($pics as $key => $value) {
			$val = explode("||", $value);
			$picsArr[$key] = $val;
		}
		$huoniaoTag->assign('picsList', json_encode($picsArr));
	}

	$huoniaoTag->assign('weight', $weight);

    $huoniaoTag->assign('cityid', $cityid);

	//显示状态
	$huoniaoTag->assign('stateopt', array('0', '1', '2'));
	$huoniaoTag->assign('statenames',array('待审核','已审核','审核拒绝'));
	$huoniaoTag->assign('state', $state == "" ? 1 : $state);

	//属性
	$huoniaoTag->assign('propertyVal',array('r','m','u'));
	$huoniaoTag->assign('propertyList',array('推荐','名企','紧急'));
	$huoniaoTag->assign('property', !empty($property) ? explode(",", $property) : "");

	//公司福利
    $huoniaoTag->assign('welfareList',array('五险一金','绩效奖金','全勤奖', '周末双休', '包吃', '包住', '年底双薪', '房补', '话补', '交通补助', '饭补', '加班补助', '外出旅游', '晋升平台', '生日福利'));
    $huoniaoTag->assign('welfare', !empty($welfare) ? explode(",", $welfare) : "");


    $huoniaoTag->assign('seotitle', $seotitle);
	$huoniaoTag->assign('keywords', $keywords);
	$huoniaoTag->assign('description', $description);
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/job";  //设置编译目录
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
