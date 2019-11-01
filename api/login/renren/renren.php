<?php
/**
 * 人人快捷登录
 *
 * @version        $Id: renren.php $v1.0 2015-2-25 上午09:58:12 $
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
    $login[$i]['name'] = "人人快捷登录";

    /* 回调地址 */
    $login[$i]['callback'] = $cfg_secureAccess.$cfg_basehost."/api/login.php?action=back&type=renren";

    /* 版本号 */
    $login[$i]['version']  = '1.0.0';

    /* 描述 */
    $login[$i]['desc'] = '直接使用人人账号登陆您的网站，获取人人高活跃用户';

    /* 作者 */
    $login[$i]['author']   = '火鸟软件';

    /* 网址 */
    $login[$i]['website']  = 'http://www.huoniao.co';

    /* 配置信息 */
    $login[$i]['config'] = array(
		array('title' => 'API KEY',  'name' => 'APIKEY', 'type' => 'text'),
		array('title' => 'Secret Key',  'name' => 'SecretKey', 'type' => 'text')
    );

    return;
}

/**
 * 类
 */
class Loginrenren {

	/**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */

    function __construct(){
        $this->Loginrenren();
    }

    function Loginrenren(){}

    /**
     * 跳转到登录地址
     * @param   array   $data  配置信息
     */
    function login($data){
		require_once(dirname(__FILE__).'/renrenApi.php');
        $renren_k = $data['APIKEY'];
        $renren_s = $data['SecretKey'];
        $callback_url = $data['callback'];
        $renren = new renrenPHP($renren_k, $renren_s);
        $login_url = $renren->login_url($callback_url, 'status_update read_user_status');
        header("Location:$login_url");
    }

	/**
	 * 登录成功返回
     * @param array $data 配置信息
     * @param array $return 返回信息
	 */
	function back($data, $return){

        global $logincode;

        require_once(dirname(__FILE__).'/renrenApi.php');
        $renren_k = $data['APIKEY'];
        $renren_s = $data['SecretKey'];
        $callback_url = $data['callback'];

        if(isset($return['code']) && $return['code']!=''){
            $renren = new renrenPHP($renren_k, $renren_s);
            $result = $renren->access_token($callback_url, $return['code']);
        }
        if(isset($result['access_token']) && $result['access_token'] != ''){

            $renren = new renrenPHP($renren_k, $renren_s, $result['access_token']);

            //获取登录用户信息
            $result=$renren->me();

            if($result){

                $response = $result['response'];

                //登录验证
                $userLogin = new userLogin($dbo);

                $data = array(
                    "code"     => $logincode,
                    "key"      => $response['id'],
                    "nickname" => $response['name'],
                    "photo"    => $response['avatar'][3]['url'],
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
