<?php
/**
 * 360登录快捷登录
 *
 * @version        $Id: 360.php $v1.0 2015-2-21 下午15:26:21 $
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
    $login[$i]['name'] = "360快捷登录";

    /* 回调地址 */
    $login[$i]['callback'] = $cfg_secureAccess.$cfg_basehost."/api/login.php?action=back&type=360";

    /* 版本号 */
    $login[$i]['version']  = '1.0.0';

    /* 描述 */
    $login[$i]['desc'] = '网站接入360连接，可轻松将360用户转变为网站用户';

    /* 作者 */
    $login[$i]['author']   = '火鸟软件';

    /* 网址 */
    $login[$i]['website']  = 'http://www.huoniao.co';

    /* 配置信息 */
    $login[$i]['config'] = array(
		array('title' => 'APPKEY',  'name' => 'APPKEY', 'type' => 'text'),
		array('title' => 'SECRET',  'name' => 'SECRET', 'type' => 'text')
    );

    return;
}

/**
 * 类
 */
class Login360 {

	/**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */

    function __construct(){
        $this->Login360();
    }

    function Login360(){}

    /**
     * 跳转到登录地址
     * @param   array   $data  配置信息
     */
    function login($data){
		require_once(dirname(__FILE__).'/QClient.php');
        $APPKEY   = $data['APPKEY'];
        $SECRET   = $data['SECRET'];
        $REDIRECT = $data['callback'];

        $connection = new QOAuth2($APPKEY, $SECRET, '');
        $url = $connection->getAuthorizeURL('code', $REDIRECT, 'basic');
        header("Location:$url");
    }

	/**
	 * 登录成功返回
     * @param array $data 配置信息
     * @param array $return 返回信息
	 */
	function back($data, $return){

        global $logincode;

        require_once(dirname(__FILE__).'/QClient.php');
        $APPKEY   = $data['APPKEY'];
        $SECRET   = $data['SECRET'];
        $REDIRECT = $data['callback'];

        if(array_key_exists('code', $return)) {
            $connect = new QOAuth2($APPKEY, $SECRET, '');

            $response = $connect->getAccessTokenByCode($return['code'], $REDIRECT);
            if(isset($response['access_token'])) {
                $connection = new QClient($APPKEY, $SECRET, $response['access_token']);
                $apiResult = $connection->userMe();

                //登录验证
                $userLogin = new userLogin($dbo);

                $data = array(
                    "code"     => $logincode,
                    "key"      => $apiResult['id'],
                    "nickname" => $apiResult['name'],
                    "photo"    => str_replace("48_48", "200_200", $apiResult['avatar']),
                    "gender"   => "男"
                );

                $userLogin->loginConnect($data);

            }else{
                die("接口连接错误！");
            }
        }else{
            die("接口连接错误！");
        }
    }

}
