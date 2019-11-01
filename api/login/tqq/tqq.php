<?php
/**
 * 腾讯微博登录快捷登录
 *
 * @version        $Id: tqq.php $v1.0 2015-2-21 上午10:52:10 $
 * @package        HuoNiao.Login
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

if(!defined('HUONIAOINC')) exit('Request Error!');

$logincode = basename(__FILE__, '.php');

/* 基本信息 */
if(isset($set_modules) && $set_modules == TRUE){

    $i = isset($login) ? count($login) : 0;

    /* 代码 */
    $login[$i]['code'] = $logincode;

	/* 名称 */
    $login[$i]['name'] = "腾讯微博快捷登录";

    /* 回调地址 */
    $login[$i]['callback'] = $cfg_secureAccess.$cfg_basehost."/api/login.php?action=back&type=tqq";

    /* 版本号 */
    $login[$i]['version']  = '1.0.0';

    /* 描述 */
    $login[$i]['desc'] = '简化用户登录流程，获取微博活跃用户，提升网站访问量。';

    /* 作者 */
    $login[$i]['author']   = '火鸟软件';

    /* 网址 */
    $login[$i]['website']  = 'http://www.huoniao.co';

    /* 配置信息 */
    $login[$i]['config'] = array(
		array('title' => 'appkey',   'name' => 'appkey',  'type' => 'text')
    );

    return;
}

/**
 * 类
 */
class Logintqq {

	/**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */

    function __construct(){
        $this->Logintqq();
    }

    function Logintqq(){}

    /**
     * 跳转到登录地址
     * @param   array   $data  配置信息
     */
    function login($data){
        global $cfg_basehost;
        global $cfg_cookiePath;
        global $cfg_cookieDomain;

		echo <<<EOT
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>腾讯微博快速登录</title>
    <script src="{$cfg_secureAccess}{$cfg_basehost}/static/js/core/jquery-1.8.3.min.js"></script>
    <script src="http://mat1.gtimg.com/app/openjs/openjs.js"></script>
</head>
<body>
<div id="message">授权申请中，请稍候...</div>
    <script defer="defer">
        function login() {
            T.login(function(loginStatus) {
                getUserInfo();
            }, function(loginError) {
                if(loginError.message == "browser blocked popup authorize window"){
                    document.getElementById("message").innerHTML = "请允许浏览器弹出窗口！";
                }else if(loginError.message == "access_denied"){
                    alert("授权失败！");
                    window.close();
                }else{
                    alert(loginError.message);
                }
            });
        }
        function getUserInfo() {
            T.api("/user/info").success(function(response) {
                if (response.ret === 0) {
                    var data = response.data;
                    var nick = data.nick;
                    var openid = data.openid;
                    var sex = data.sex;
                    var head = data.head;
                    var photo = "";

                    if(head != ""){
                        head = head.split("/");
                        photo = "http://t2.qlogo.cn/mbloghead/"+head[head.length]+"/180";
                    }

                    $.cookie('openid', openid, {expires: 7, path: '{$cfg_cookiePath}', domain: '{$cfg_cookieDomain}'});
                    location.href = '{$data['callback']}&nickname='+nick+'&sex='+sex+'&openid='+openid+'&photo='+photo;

                } else {
                    alert(response.ret);
                }
            }).error(function(code, message) {
                alert(message);
            });
        }
        function init() {
            T.init({
                appkey: {$data['appkey']}
            });
            login();
        }
        init();
    </script>
</body>
</html>
EOT;
    }

	/**
	 * 登录成功返回
     * @param array $data 配置信息
     * @param array $return 返回信息
	 */
	function back($data, $return){

        global $logincode;

        //获取access_token
        if($return['openid'] == $_COOKIE["openid"]){

            DropCookie('openid');

            $key      = $return['openid'];
            $nickname = $return['nickname'];
            $photo    = $return['photo'];
            $gender   = $return['sex'] == 1 ? "男" : "女";

            //登录验证
            $userLogin = new userLogin($dbo);

            $data = array(
                "code"     => $logincode,
                "key"      => $key,
                "nickname" => $nickname,
                "photo"    => $photo,
                "gender"   => $gender
            );

            $userLogin->loginConnect($data);

        }else{
            die("接口连接错误！");
        }
    }

}
