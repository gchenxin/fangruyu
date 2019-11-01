<?php  if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * Cookie处理插件
 *
 * @version        $Id: cookie.class.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.class
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

/**
 *  设置Cookie记录
 *
 * @param     string  $key    键
 * @param     string  $value  值
 * @param     string  $kptime  保持时间
 * @param     string  $pa     保存路径
 * @return    void
 */
if (!function_exists('PutCookie')){
    function PutCookie($key, $value, $kptime=0, $pa="/", $domain = ""){
        global $cfg_cookiePath, $cfg_cookieDomain, $cfg_cookiePre;
        $domain = $domain ? $domain : $cfg_cookieDomain;
        $_COOKIE[$cfg_cookiePre.$key] = $value;
        setcookie($cfg_cookiePre.$key, $value, time()+$kptime, $cfg_cookiePath, $domain);
        if(!strstr($domain, 'www.')){
            setcookie($cfg_cookiePre.$key, $value, time()+$kptime, $cfg_cookiePath, 'www.' . $domain);
        }
    }
}

/**
 *  清除Cookie记录
 *
 * @param     $key   键名
 * @return    void
 */
if (!function_exists('DropCookie')){
    function DropCookie($key){
        global $cfg_cookieDomain, $cfg_cookiePath, $cfg_cookiePre;
        unset($_COOKIE[$cfg_cookiePre.$key]);
        setcookie($cfg_cookiePre.$key, '', time()-360000, $cfg_cookiePath, $cfg_cookieDomain);

        $host = $_SERVER['HTTP_HOST'];
        $host_ = explode(".", $host);
        $domain = "";
        $len = count($host_);
        $start = $len > 2 ? $len - 2 : 0;
        for($i = $start; $i < $len; $i++){
            $domain .= ".".$host_[$i];
        }
        setcookie($cfg_cookiePre.$key, '', time()-360000, $cfg_cookiePath, $domain);
        if(!strstr($domain, 'www')){
            setcookie($cfg_cookiePre.$key, '', time()-360000, $cfg_cookiePath, 'www' . $domain);
        }
    }
}

/**
 *  获取Cookie记录
 *
 * @param     $key   键名
 * @return    string
 */
if (!function_exists('GetCookie')){
    function GetCookie($key){
        global $cfg_cookiePath, $cfg_cookieDomain, $cfg_cookiePre;
        if(!isset($_COOKIE[$cfg_cookiePre.$key])){
            return '';
        }else{
        	return $_COOKIE[$cfg_cookiePre.$key];
        }
    }
}
