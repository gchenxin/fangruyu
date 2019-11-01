<?php
/**
 * 豆瓣快捷登录
 *
 * @version        $Id: douban.php $v1.0 2015-2-25 上午09:58:12 $
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
    $login[$i]['name'] = "豆瓣快捷登录";

    /* 回调地址 */
    $login[$i]['callback'] = $cfg_secureAccess.$cfg_basehost."/api/login.php?action=back&type=douban";

    /* 版本号 */
    $login[$i]['version']  = '1.0.0';

    /* 描述 */
    $login[$i]['desc'] = '你的网站使用此功能后，可以让用户无需注册流程直接登录，降低使用门槛，让你的网站轻松获得新用户，提升用户登录率。';

    /* 作者 */
    $login[$i]['author']   = '火鸟软件';

    /* 网址 */
    $login[$i]['website']  = 'http://www.huoniao.co';

    /* 配置信息 */
    $login[$i]['config'] = array(
		array('title' => 'API Key', 'name' => 'APIKEY', 'type' => 'text'),
		array('title' => 'Secret',  'name' => 'Secret', 'type' => 'text')
    );

    return;
}

/**
 * 类
 */
class Logindouban {

	/**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */

    function __construct(){
        $this->Logindouban();
    }

    function Logindouban(){}

    /**
     * 跳转到登录地址
     * @param   array   $data  配置信息
     */
    function login($data){
		require_once(dirname(__FILE__).'/DoubanOAuth.php');
        $APIKEY   = $data['APIKEY'];
        $Secret   = $data['Secret'];
        $callback = $data['callback'];

        $douban = new DoubanOAuth(array(
            'key' => $APIKEY,
            'secret' => $Secret,
            'redirect_url' => $callback,
        ));

        $url = $douban->getAuthorizeURL("douban_basic_common,book_basic_r,book_basic_w", "Something");

        header("Location:$url");
    }

	/**
	 * 登录成功返回
     * @param array $data 配置信息
     * @param array $return 返回信息
	 */
	function back($data, $return){

        global $logincode;

        require_once(dirname(__FILE__).'/DoubanOAuth.php');
        $APIKEY   = $data['APIKEY'];
        $Secret   = $data['Secret'];
        $callback = $data['callback'];

        $douban = new DoubanOAuth(array(
            'key' => $APIKEY,
            'secret' => $Secret,
            'redirect_url' => $callback,
        ));

        $result = $douban->getAccessToken($return['code']);
        $result = $douban->get('user/~me');
        if($result && $result['loc_id']){

            //登录验证
            $userLogin = new userLogin($dbo);

            $data = array(
                "code"     => $logincode,
                "key"      => $result['loc_id'],
                "nickname" => $result['name'],
                "photo"    => str_replace("/u", "/ul", $result['avatar']),
                "gender"   => "男"
            );

            $userLogin->loginConnect($data);

        }else{
            die("接口连接错误！");
        }
    }

}
