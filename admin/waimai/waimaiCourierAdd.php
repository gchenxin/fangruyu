<?php
/**
 * 店铺管理 新建店铺
 *
 * @version        $Id: waimaiCourierAdd.php 2017-5-26 上午11:19:16 $
 * @package        HuoNiao.Courier
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', ".." );
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/waimai";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录

$dbname = "waimai_courier";
$templates = "waimaiCourierAdd.html";

//表单提交
if($_POST){

    //获取表单数据
    $id  = (int)$id;
    $sex = (int)$sex;
    $quit = (int)$quit;
    $name = $_POST['name'];

    if(empty($cityid)){
        echo '{"state": 200, "info": "请选择城市"}';
        exit();
    }

    $adminCityIdsArr = explode(',', $adminCityIds);
    if(!in_array($cityid, $adminCityIdsArr)){
        echo '{"state": 200, "info": "要发布的城市不在授权范围"}';
        exit();
    }

    //店铺名称
    if(trim($name) == ""){
		echo '{"state": 200, "info": "请输入姓名"}';
		exit();
	}

    //用户名
    if(trim($username) == ""){
		echo '{"state": 200, "info": "请输入用户名"}';
		exit();
	}

    //密码
    if(trim($password) == ""){
		echo '{"state": 200, "info": "请输入密码"}';
		exit();
	}

    //手机号
    if(trim($phone) == ""){
		echo '{"state": 200, "info": "请输入手机号码"}';
		exit();
	}

    //验证是否存在
    if($id){

        //先验证配送员是否存在
        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__$dbname` WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "totalCount");
        if($ret <= 0){
            echo '{"state": 200, "info": "配送员不存在或已经删除！"}';
			exit();
        }

        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__$dbname` WHERE (`name` = '$name' OR `username` = '$username' OR `phone` = '$phone') AND `id` != '$id'");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			echo '{"state": 200, "info": "配送员已经存在！"}';
			exit();
		}

    }else{
        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__$dbname` WHERE `name` = '$name' OR `username` = '$username' OR `phone` = '$phone'");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			echo '{"state": 200, "info": "配送员已经存在！"}';
			exit();
		}
    }


    //修改
    if($id){

        $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET
            `name` = '$name',
            `username` = '$username',
            `password` = '$password',
            `phone` = '$phone',
            `age` = '$age',
            `sex` = '$sex',
            `quit` = '$quit',
            `photo` = '$photo',
            `cityid` = '$cityid'
          WHERE `id` = $id
        ");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
			echo '{"state": 100, "info": '.json_encode("保存成功！").'}';
		}else{
			echo '{"state": 200, "info": "数据更新失败，请检查填写的信息是否合法！"}';
		}
        die;


    //新增
    }else{

        //保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__$dbname` (
            `name`,
            `username`,
            `password`,
            `phone`,
            `age`,
            `sex`,
            `photo`,
            `cityid`
        ) VALUES (
            '$name',
            '$username',
            '$password',
            '$phone',
            '$age',
            '$sex',
            '$photo',
            '$cityid'
        )");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if($aid){
			echo '{"state": 100, "id": '.$aid.', "info": '.json_encode("添加成功！").'}';
		}else{
			echo '{"state": 200, "info": "数据插入失败，请检查填写的信息是否合法！"}';
		}
		die;

    }

}


//验证模板文件
if(file_exists($tpl."/".$templates)){

    //css
	$cssFile = array(
        'ui/jquery.chosen.css',
		'admin/bootstrap1.css',
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
		'ui/jquery-ui.min.js',
		'ui/jquery.form.js',
		'ui/jquery.dragsort-0.5.1.min.js',
        'ui/chosen.jquery.min.js',
		'publicUpload.js',
		'admin/waimai/waimaiCourierAdd.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

    $huoniaoTag->assign('cityList', json_encode($adminCityArr));

    //获取信息内容
    if($id){
        $sql = $dsql->SetQuery("SELECT * FROM `#@__$dbname` WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){

            foreach ($ret[0] as $key => $value) {
                $huoniaoTag->assign($key, $value);
            }

        }else{
            showMsg("没有找到相关信息！", "-1");
            die;
        }
    }else{
        $huoniaoTag->assign('cityid', (int)$cityid);
    }

	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
