<?php
/**
 * 用户管理
 *
 * @version        $Id: memberList.php 2014-11-15 上午10:03:17 $
 * @package        HuoNiao.Member
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__)."/../inc/config.inc.php");
checkPurview("memberList");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/member";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$template = "callRecord.html";

//css
$cssFile = array(
  'ui/jquery.chosen.css',
  'admin/chosen.min.css'
);
//js
$jsFile = array(
	'ui/jquery.dragsort-0.5.1.min.js',
	'publicUpload.js',
	'publicAddr.js',
);
$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));

$pagestep = $pagestep == "" ? 10 : $pagestep;
$page     = $page == "" ? 1 : $page;

$where = '1=1';
if($caller){
	$where .= " and c.caller={$caller}";
}
if($start){
	$where .= " and c.date>='{$start}'";
}
if($end){
	$where .= " and c.date < '{$end}'";
}
if($relationPhone){
	$where .= " and c.relationPhone='{$relationPhone}'";
}
$orderby = "c.date desc,c.id";
$atpage = $pagestep * ($page -1);
$limit = " limit {$atpage}, $pagestep";
$archives = <<<EOT
select c.*,f.description failed,u.description unanswer from huoniao_callrecord c
	INNER JOIN huoniao_callFailedCode f on c.callfailCode=f.id
	inner join huoniao_callFailedCode u on c.callunanswerCode=u.id
where {$where} ORDER BY {$orderby} {$limit}
EOT;

$archives = $dsql->SetQuery($archives);
$result = $dsql->dsqlOper($archives, "results");
if($result){
	$huoniaoTag->assign("callrecord", $result);
}else{
	$huoniaoTag->assign("callrecord", "NoData!");
}

if(file_exists($tpl."/".$templates)){
	$huoniaoTag->assign("jsFile", includeFile('js', $jsFile));
	$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));
	$huoniaoTag->compile_dir = HUONIAOROOT."/templates_c/admin/member";  //设置编译目录
	$huoniaoTag->display($templates);

}else{
	echo $templates."模板文件未找到！";
}
