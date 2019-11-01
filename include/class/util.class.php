<?php  if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 核心插件
 *
 * @version        $Id: util.class.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.class
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

/**
 *  获得当前的脚本网址
 *
 * @return    string
 */
if (!function_exists('GetCurUrl')){
    function GetCurUrl(){
        if(!empty($_SERVER["REQUEST_URI"])){
            $scriptName = $_SERVER["REQUEST_URI"];
            $nowurl = $scriptName;
        }else{
            $scriptName = $_SERVER["PHP_SELF"];
            if(empty($_SERVER["QUERY_STRING"])){
                $nowurl = $scriptName;
            }else{
                $nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
            }
        }
        return $nowurl;
    }
}

/**
 *  生成一个随机字符
 *
 * @access    public
 * @param     string  $ddnum
 * @return    string
 */
if (!function_exists('dd2char')){
    function dd2char($ddnum){
        $ddnum = strval($ddnum);
        $slen = strlen($ddnum);
        $okdd = '';
        $nn = '';
        for($i=0;$i<$slen;$i++){
            if(isset($ddnum[$i+1])){
                $n = $ddnum[$i].$ddnum[$i+1];
                if(($n>96 && $n<123) || ($n>64 && $n<91)){
                    $okdd .= chr($n);
                    $i++;
                }else{
                    $okdd .= $ddnum[$i];
                }
            }else{
                $okdd .= $ddnum[$i];
            }
        }
        return $okdd;
    }
}

/**
 *  json_encode兼容函数
 *
 * @access    public
 * @param     string  $data
 * @return    string
 */
if (!function_exists('json_encode')) {
	function format_json_value(&$value){
		if(is_bool($value)) {
			$value = $value?'TRUE':'FALSE';
		} else if (is_int($value)) {
			$value = intval($value);
		} else if (is_float($value)) {
			$value = floatval($value);
		} else if (defined($value) && $value === NULL) {
			$value = strval(constant($value));
		} else if (is_string($value)) {
			$value = '"'.addslashes($value).'"';
		}
		return $value;
	}

    function json_encode($data){
        if(is_object($data)) {
            //对象转换成数组
            $data = get_object_vars($data);
        }else if(!is_array($data)) {
            // 普通格式直接输出
            return format_json_value($data);
        }
        // 判断是否关联数组
        if(empty($data) || is_numeric(implode('',array_keys($data)))) {
            $assoc  =  FALSE;
        }else {
            $assoc  =  TRUE;
        }
        // 组装 Json字符串
        $json = $assoc ? '{' : '[' ;
        foreach($data as $key=>$val) {
            if(!is_NULL($val)) {
                if($assoc) {
                    $json .= "\"$key\":".json_encode($val).",";
                }else {
                    $json .= json_encode($val).",";
                }
            }
        }
        if(strlen($json)>1) {// 加上判断 防止空数组
            $json  = substr($json,0,-1);
        }
        $json .= $assoc ? '}' : ']' ;
        return $json;
    }
}

/**
 *  json_decode兼容函数
 *
 * @access    public
 * @param     string  $json  json数据
 * @param     string  $assoc  当该参数为 TRUE 时，将返回 array 而非 object
 * @return    string
 */
if (!function_exists('json_decode')) {
    function json_decode($json, $assoc=FALSE){
        // 目前不支持二维数组或对象
        $begin  =  substr($json,0,1) ;
        if(!in_array($begin,array('{','[')))
            // 不是对象或者数组直接返回
            return $json;
        $parse = substr($json,1,-1);
        $data  = explode(',',$parse);
        if($flag = $begin =='{' ) {
            // 转换成PHP对象
            $result   = new stdClass();
            foreach($data as $val) {
                $item    = explode(':',$val);
                $key =  substr($item[0],1,-1);
                $result->$key = json_decode($item[1],$assoc);
            }
            if($assoc)
                $result   = get_object_vars($result);
        }else {
            // 转换成PHP数组
            $result   = array();
            foreach($data as $val)
                $result[]  =  json_decode($val,$assoc);
        }
        return $result;
    }
}