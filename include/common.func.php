<?php
/**
 * 系统核心函数存放文件
 *
 * @version        $Id: common.func.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.Libraries
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

if (!defined('HUONIAOINC')) exit('Request Error!');

require_once(HUONIAOROOT . '/include/common.inc.php');

if(!is_file(HUONIAOINC."/class/memory.class.php") || !is_file(HUONIAOINC."/class/memory_redis.class.php")){
    class memory2 {
        public $enable = false;
        public function get($key, $prefix = '') {}
        public function set($key, $value, $ttl = 0, $prefix = '') {}
        public function rm($key, $prefix = '') {}
        public function clear() {}
        public function inc($key, $step = 1) {}
        public function dec($key, $step = 1) {}
    }
    $HN_memory = new memory2();
}else{
    $HN_memory = new memory();
}

$autoload = true;
function classLoaderQiniu_($class){
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = HUONIAOROOT . '/api/upload/' . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('classLoaderQiniu_');
require(HUONIAOROOT . '/api/upload/Qiniu/functions.php');
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

$autoload = false;

/**
 *  系统默认载入插件
 *
 * @access    public
 * @param     mix $plug 插件名称,可以是数组,可以是单个字符串
 * @return    void
 */
$_plugs = array();
function loadPlug($plugs){
    //如果是数组,则进行递归操作
    if (is_array($plugs)) {
        foreach ($plugs as $huoniao) {
            loadPlug($huoniao);
        }
        return;
    }

    if (isset($_plugs[$plugs])) {
        return;
    }
    if (file_exists(HUONIAOINC . '/class/' . $plugs . '.class.php')) {
        include_once(HUONIAOINC . '/class/' . $plugs . '.class.php');
        $_plugs[$plugs] = TRUE;
    }
    //无法载入插件
    if (!isset($_plugs[$plugs])) {
        exit('Unable to load the requested file: class/' . $plugs . '.class.php');
    }
}

/**
 *  短消息函数,可以在某个动作处理后友好的提示信息
 *
 * @param     string $msg 消息提示信息
 * @param     string $gourl 跳转地址
 * @param     int $onlymsg 仅显示信息
 * @param     int $limittime 限制时间
 * @return    void
 */
function ShowMsg($msg, $gourl, $onlymsg = 0, $limittime = 0){
    global $langData;
    $htmlhead = "<html>\r\n<head>\r\n<title>" . $langData['siteConfig'][21][5] . "</title>\r\n";
    $htmlhead .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" . $GLOBALS['cfg_soft_lang'] . "\" />\r\n";
    $htmlhead .= "<link rel='stylesheet' rel='stylesheet' href='" . HUONIAOADMIN . "/../static/css/admin/bootstrap.css?v=4' />";
    $htmlhead .= "<link rel='stylesheet' rel='stylesheet' href='" . HUONIAOADMIN . "/../static/css/admin/common.css?v=1' />";
    $htmlhead .= "<base target='_self'/>\r\n</head>\r\n<body>\r\n<script>\r\n";
    $htmlfoot = "\r\n</script>\r\n</body>\r\n</html>\r\n";

    $litime = ($limittime == 0 ? 1000 : $limittime);
    $func = '';

    if ($gourl == '-1') {
        if ($limittime == 0) $litime = 5000;
        $gourl = "javascript:history.go(-1);";
    }

    if ($gourl == '' || $onlymsg == 1) {
        $msg = "<script>alert(\"" . str_replace("\"", "“", $msg) . "\");</script>";
    } else {
        //当网址为:close::objname 时, 关闭父框架的id=objname元素
        if (preg_match('/close::/', $gourl)) {
            $tgobj = trim(preg_replace('/close::/', '', $gourl));
            $gourl = 'javascript:;';
            $func .= "window.parent.document.getElementById('{$tgobj}').style.display='none';\r\n";
        }

        $func .= "	var pgo=0;\r\n";
        $func .= "	function JumpUrl(){\r\n";
        $func .= "		if(pgo==0){ location='$gourl'; pgo=1; }\r\n";
        $func .= "	}\r\n";
        $rmsg = $func;
        $rmsg .= "	document.write(\"<div class='s-tip'><div class='s-tip-head'><h1>" . $GLOBALS['cfg_soft_enname'] . " " . $langData['siteConfig'][21][6] . "：</h1></div>\");\r\n";
        $rmsg .= "	document.write(\"<div class='s-tip-body'>" . str_replace("\"", "“", $msg) . "\");\r\n";
        $rmsg .= "	document.write(\"";

        if ($onlymsg == 0) {
            if ($gourl != 'javascript:;' && $gourl != '') {
                $rmsg .= "<br /><a href='{$gourl}'>" . $langData['siteConfig'][21][7] . "</a></div>\");\r\n";
                $rmsg .= "	setTimeout('JumpUrl()',$litime);";
            } else {
                $rmsg .= "<br /></div>\");\r\n";
            }
        } else {
            $rmsg .= "<br /><br /></div>\");\r\n";
        }
        $msg = $htmlhead . $rmsg . $htmlfoot;
    }
    echo $msg;
}

/*
 * 获取软件当前版本
 */
function getSoftVersion(){
    $m_file = HUONIAODATA . "/admin/version.txt";
    $version = "";
    if (filesize($m_file) > 0) {
        $fp = fopen($m_file, 'r');
        $version = fread($fp, filesize($m_file));
        fclose($fp);
    }
    return $version;
}

/**
 * 检查功能模块状态
 *
 * @param array $config
 * @return string
 */
function checkModuleState($config = array()){
    if ($config['visitState']) {
        die($config['visitMessage']);
    }
    if ($config['channelSwitch']) {
        die($config['closeCause']);
    }
}

/**
 *  获取验证码的session值
 *
 * @return    string
 */
function GetCkVdValue(){
    @session_id($_COOKIE['PHPSESSID']);
    return isset($_SESSION['huoniao_vdimg_value']) ? $_SESSION['huoniao_vdimg_value'] : '';
}

/**
 *  PHP某些版本有Bug，不能在同一作用域中同时读session并改注销它，因此调用后需执行本函数
 *
 * @return    void
 */
function ResetVdValue(){
    $_SESSION['huoniao_vdimg_value'] = '';
}

//获取用户真实地址
function GetIP(){
    static $realip = NULL;
    if ($realip !== NULL) {
        return $realip;
    }
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            /* 取X-Forwarded-For中第x个非unknown的有效IP字符? */
            foreach ($arr as $ip) {
                $ip = trim($ip);
                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}

//获取IP真实地址
function getIpAddr($ip, $type = 'string'){

  // $curl = curl_init();
  // curl_setopt($curl, CURLOPT_URL, "http://wap.ip138.com/ip_search138.asp?ip=$ip");
  // curl_setopt($curl, CURLOPT_HEADER, 0);
  // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  // curl_setopt($curl, CURLOPT_TIMEOUT, 1);
  // $con = curl_exec($curl);
  // curl_close($curl);
  //
  // $preg="/\<\/b\>\<br\/\>\<b\>(.*)\<\/b\>/U";
  // reg_match_all($preg,$con,$arr);
  // eturn $arr[1][0];


  //淘宝
//  $curl = curl_init();
//  curl_setopt($curl, CURLOPT_URL, "http://ip.taobao.com/service/getIpInfo.php?ip=$ip");
//  curl_setopt($curl, CURLOPT_HEADER, 0);
//  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//  curl_setopt($curl, CURLOPT_TIMEOUT, 1);
//  $con = curl_exec($curl);
//  curl_close($curl);
//
//  $con = json_decode($con, true);
//  if($con['code'] == 0){
//      $data = $con['data'];
//      if($type == 'string'){
//        return $data['country'] == "中国" ? $data['region'] . $data['city'] . " " . $data['isp'] : $data['country'] . $data['region'] . $data['city'] . " " . $data['isp'];
//      }elseif($type == 'json'){
//        return $data;
//      }
//  }else{
//      return "未知";
//  }

    global $cfg_juhe;
    $has = false;


    //聚合数据接口
    if($cfg_juhe){

        $cfg_juhe = is_array($cfg_juhe) ? $cfg_juhe : unserialize($cfg_juhe);

        $key = $cfg_juhe['ipAddr'];

        if($key) {

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "http://apis.juhe.cn/ip/ipNew?ip=$ip&key=" . $key);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 3);
            $con = curl_exec($curl);
            curl_close($curl);

            $con = json_decode($con, true);
            if ($con['resultcode'] == 200) {
                $has = true;
                $data = $con['result'];
                if ($type == 'string') {
                    return $data['Province'] . ' ' . $data['City'] . ' ' . $data['Isp'];
                } elseif ($type == 'json') {
                    return array(
                        'location' => $data['Province'] . ' ' . $data['City'] . ' ' . $data['Isp'],
                        'region' => $data['Province'],
                        'city' => $data['City']
                    );
                }
            } else {
                //失败后继续使用默认接口
                //return "未知";
            }
        }

    }


    //百度
    if(!$has) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://opendata.baidu.com/api.php?resource_id=6006&ie=gbk&oe=utf8&query=$ip");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        $con = curl_exec($curl);
        curl_close($curl);

        $con = json_decode($con, true);
        if ($con['status'] == 0 && $con['data']) {
            $data = $con['data'][0];

            $location = $data['location'];
            $locationArr = explode(' ', $location);
            $pcData = $locationArr[0];
            $pcDataArr = explode('省', $pcData);
            $region = $pcDataArr[0];
            $city = $pcDataArr[1];
            $data['region'] = $region;
            $data['city'] = $city;

            if ($type == 'string') {
                return $data['location'];
            } elseif ($type == 'json') {
                return $data;
            }
        } else {
            return "未知";
        }
    }


}

//检查IP段
function checkIpAccess($ip = '', $accesslist = ''){
    $accesslist = trim($accesslist);
    $accesslist = preg_replace('/($s*$)|(^s*^)/m', '',$accesslist);
    return preg_match("/^(" . str_replace(array("\r\n", ' '), array('|', ''), preg_quote($accesslist, '/')) . ")/", $ip);
}

//获取手机归属地
function getTelAddr($tel){

    global $cfg_juhe;
    $has = false;

    //聚合数据接口
    if($cfg_juhe){

        $cfg_juhe = is_array($cfg_juhe) ? $cfg_juhe : unserialize($cfg_juhe);
        $key = $cfg_juhe['mobileAddr'];

        if($key) {

            $has = true;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "http://apis.juhe.cn/mobile/get?phone=$tel&key=" . $key);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 3);
            $con = curl_exec($curl);
            curl_close($curl);

            $con = json_decode($con, true);
            if ($con['resultcode'] == 200) {
                $data = $con['result'];
                return $data['province'] . $data['city'] . ' ' . $data['company'];
            } else {
                return "未知";
            }
        }

    }

    if(!$has) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://wap.ip138.com/sim_search138.asp?mobile=$tel");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        $con = curl_exec($curl);
        curl_close($curl);

        //$con = iconv("gb2312","utf-8//IGNORE",$con);
        $preg = "/归属地：(.*)\<br\/\>/U";
        preg_match_all($preg, $con, $arr);
        return $arr[1][0];
    }
}

//转换编码，将Unicode编码转换成可以浏览的utf-8编码
function unicode_decode($name){
    $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches)) {
        $name = '';
        for ($j = 0; $j < count($matches[0]); $j++) {
            $str = $matches[0][$j];
            if (strpos($str, '\\u') === 0) {
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code) . chr($code2);
                $c = iconv('UCS-2', 'UTF-8', $c);
                $name .= $c;
            } else {
                $name .= $str;
            }
        }
    }
    return $name;
}

/**
 * 获得当前的脚本网址
 *
 * @return    string
 */
function GetCurUrl(){
    if (!empty($_SERVER["REQUEST_URI"])) {
        $scriptName = $_SERVER["REQUEST_URI"];
        $nowurl = $scriptName;
    } else {
        $scriptName = $_SERVER["PHP_SELF"];
        if (empty($_SERVER["QUERY_STRING"])) {
            $nowurl = $scriptName;
        } else {
            $nowurl = $scriptName . "?" . $_SERVER["QUERY_STRING"];
        }
    }
    return $nowurl;
}

/*
 * 函数名称：create_sess_id()
 * 函数作用：产生以个随机的会话ID
 * 参   数：$len: 需要会话字符串的长度，默认为32位，不要低于16位
 * 返 回 值：返回会话ID
 */
function create_sess_id($len = 32){
    //校验提交的长度是否合法
    if (!is_numeric($len) || ($len > 32) || ($len < 16)) {
        return;
    }
    //获取当前时间的微秒
    list($u, $s) = explode(' ', microtime());
    $time = (float)$u + (float)$s;
    //产生一个随机数
    $rand_num = rand(100000, 999999);
    $rand_num = rand($rand_num, $time);
    mt_srand($rand_num);
    $rand_num = mt_rand();
    //产生SessionID
    $sess_id = md5(md5($time) . md5($rand_num));
    //截取指定需要长度的SessionID
    $sess_id = substr($sess_id, 0, $len);
    return $sess_id;
}

/*
 * 函数名称：create_check_code()
 * 函数作用：产生以个随机的校验码
 * 参   数：$len: 需要校验码的长度, 请不要长于16位,缺省为4位
 * 返 回 值：返回指定长度的校验码
 */
function create_check_code($len = 4){
    if (!is_numeric($len) || ($len > 15) || ($len < 1)) {
        return;
    }

    $check_code = substr(create_sess_id(), 16, $len);
    return strtoupper($check_code);
}


/**
 * 生成订单号
 * @return string
 */
function create_ordernum(){
    return intval(date('y')) .
        strtoupper(dechex(date('m'))) . date('d') .
        substr(time(), -5) . substr(microtime(), 2, 4) . sprintf('%02d', rand(0, 99));
}


/**
 * 生成指定数量的随机字符
 * $len 长度
 * $type 类型 1 数字  2 字母  3混合
 */
function genSecret($len = 6, $type = 1){
    $secret = '';
    for ($i = 0; $i < $len; $i++) {
        if ($type == 1) {
            if (0 == $i) {
                $secret .= chr(rand(49, 57));
            } else {
                $secret .= chr(rand(48, 57));
            }
        } else if ($type == 2) {
            $secret .= chr(rand(65, 90));
        } else {
            if (0 == $i) {
                $secret .= chr(rand(65, 90));
            } else {
                $secret .= (0 == rand(0, 1)) ? chr(rand(65, 90)) : chr(rand(48, 57));
            }
        }
    }
    return $secret;
}

/**
 * 遍历多维数组为一维数组
 *
 * @param array 传入的多维数组
 * @return array 返回一维数组
 */
function arr_foreach($arr){
    global $arr_data;
    if (!is_array($arr) && $arr != NULL) {
        return $arr_data;
    }
    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            arr_foreach($val);
        } else {
            if ($val && $val != NULL && $key == "id") {
              $arr_data = empty($arr_data) ? array() : $arr_data;
              array_push($arr_data, $val);
                // $arr_data[] = $val;
            }
        }
    }
    return $arr_data;
}

//stdClass Object对象转普通数组
function objtoarr($obj){
    $ret = array();
    if (!$obj) return false;
    foreach ($obj as $key => $value) {
        if (gettype($value) == 'array' || gettype($value) == 'object') {
            $ret[$key] = objtoarr($value);
        } else {
            $ret[$key] = $value;
        }
    }
    return $ret;
}

//二维数组排序
function array_sort($arr, $keys, $type = 'asc'){
    $keysvalue = $new_array = array();
    foreach ($arr as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

//分类操作
function typeAjax($arr, $pid = 0, $dopost, $more = array()){
    $dsql = new dsql($dbo);

    if (!is_array($arr) && $arr != NULL) {
        return '{"state": 200, "info": "保存失败！"}';
    }

    $more_field = $more_val = "";
    if($more){
        $more_field = $more[0] ? ", ".$more[0] : "";
        $more_val = isset($more[1]) ? ", ".$more[1] : "";
    }

    for ($i = 0; $i < count($arr); $i++) {
        $id = $arr[$i]["id"];
        $name = $arr[$i]["name"];
        $icon = $arr[$i]["icon"];
        $longitude = $arr[$i]["longitude"];
        $latitude = $arr[$i]["latitude"];

        if($more_val){
            $n = preg_match_all("/#(\w+)#/", $more_val, $res);
            if($n){
                foreach ($res[1] as $k => $v) {
                    $more_val = str_replace($res[0][$k], $arr[$i][$v], $more_val);
                }
            }
        }

        //如果ID为空则向数据库插入下级分类
        if ($id == "" || $id == 0) {

            //新闻频道包含拼音、拼音首字母
            if ($dopost == "articletype" || $dopost == "pictype" || $dopost == "car_brandtype" || $dopost == "travel_visacountrytype") {
                $pinyin = GetPinyin($name);
                $py = GetPinyin($name, 1);

                $archives = $dsql->SetQuery("INSERT INTO `#@__" . $dopost . "` (`parentid`, `typename`, `pinyin`, `py`, `weight`, `pubdate` {$more_field}) VALUES ('$pid', '$name', '$pinyin', '$py', '$i', '" . GetMkTime(time()) . "' {$more_val})");

                //房产频道特殊字段
            } elseif ($dopost == "houseaddr") {
                $archives = $dsql->SetQuery("INSERT INTO `#@__" . $dopost . "` (`parentid`, `typename`, `weight`, `pubdate`, `longitude`, `latitude`) VALUES ('$pid', '$name', '$i', '" . GetMkTime(time()) . "', '$longitude', '$latitude')");
                // 其它
            } else {
                $archives = $dsql->SetQuery("INSERT INTO `#@__" . $dopost . "` (`parentid`, `typename`, `weight`, `pubdate` {$more_field}) VALUES ('$pid', '$name', '$i', '" . GetMkTime(time()) . "' {$more_val})");
            }
            $id = $dsql->dsqlOper($archives, "lastid");

            adminLog("添加分类", $dopost . "=>" . $name);
        } //其它为数据库已存在的分类需要验证分类名是否有改动，如果有改动则UPDATE
        else {
            $hasIcon = false;
            //房产频道特殊字段
            if ($dopost == "houseaddr") {
                $archives = $dsql->SetQuery("SELECT `typename`, `weight`, `longitude`, `latitude` FROM `#@__" . $dopost . "` WHERE `id` = " . $id);
                // 分类有图标
            } elseif ($dopost == "education_type" || $dopost == "marry_type" || $dopost == "homemaking_type" || $dopost == "car_brandtype" || $dopost == "business_type" || $dopost == "tieba_type" || $dopost == "integral_type" || $dopost == "infotype" || $dopost == "tuantype" || $dopost == "shop_type" || $dopost == "huangyetype") {
                $hasIcon = true;
                $archives = $dsql->SetQuery("SELECT `typename`, `weight`, `icon` FROM `#@__" . $dopost . "` WHERE `id` = " . $id);
            } else {
                $archives = $dsql->SetQuery("SELECT * FROM `#@__" . $dopost . "` WHERE `id` = " . $id);
            }
            $results = $dsql->dsqlOper($archives, "results");
            if (!empty($results)) {
                //验证分类名
                if ($results[0]["typename"] != $name) {

                    //新闻频道包含拼音、拼音首字母
                    if ($dopost == "article" || $dopost == "pic") {
                        $pinyin = GetPinyin($name);
                        $py = GetPinyin($name, 1);
                        $archives = $dsql->SetQuery("UPDATE `#@__" . $dopost . "` SET `typename` = '$name', `pinyin` = '$pinyin', `py` = '$py' WHERE `id` = " . $id);
                    } else {
                        $archives = $dsql->SetQuery("UPDATE `#@__" . $dopost . "` SET `typename` = '$name' WHERE `id` = " . $id);
                    }
                    $dsql->dsqlOper($archives, "update");

                    adminLog("修改分类名", $dopost . "=>" . $name);
                }

                //验证排序
                if ($results[0]["weight"] != $i) {
                    $archives = $dsql->SetQuery("UPDATE `#@__" . $dopost . "` SET `weight` = '$i' WHERE `id` = " . $id);
                    $dsql->dsqlOper($archives, "update");

                    adminLog("修改分类排序", $dopost . "=>" . $name . "=>" . $i);
                }


                //房产频道特殊字段
                if ($dopost == "houseaddr") {
                    if ($results[0]["longitude"] != $longitude || $results[0]["latitude"] != $latitude) {
                        $archives = $dsql->SetQuery("UPDATE `#@__" . $dopost . "` SET `longitude` = '$longitude', `latitude` = '$latitude' WHERE `id` = " . $id);
                        $dsql->dsqlOper($archives, "update");
                        adminLog("修改房产区域坐标", $dopost . "=>" . $name . "=>" . $longitude . "," . $latitude);
                    }

                }

                // 带分类图标
                if ($hasIcon || isset($results[0]['icon'])) {
                    if ($results[0]['icon'] != $icon) {
                        $archives = $dsql->SetQuery("UPDATE `#@__" . $dopost . "` SET `icon` = '$icon' WHERE `id` = " . $id);
                        $dsql->dsqlOper($archives, "update");

                        if ($dopost == "integal_type") {
                            $tit = "积分商城";
                        }elseif ($dopost == "infotype") {
                            $tit = "分类信息";
                        }elseif ($dopost == "tuantype"){
                            $tit = "团购商家";
                        }elseif ($dopost == "shop_type"){
                            $tit = "商城";
                        }elseif ($dopost == "huangyetype"){
                            $tit = "黄页";
                        }elseif ($dopost == "business_type"){
                            $tit = "商家";
                        }else if($dopost == "car_brandtype"){
                            $tit = "汽车";
                        }elseif($dopost == "homemaking_type"){
                            $tit = "家政服务";
                        }elseif($dopost == "marry_type"){
                            $tit = "婚嫁";
                        }elseif($dopost == "education_type"){
                            $tit = "教育";
                        }
                        adminLog("修改" . $tit . "分类图标", $dopost . "=>" . $name . "=>" . $icon);
                    }
                }


            }
        }
        if (is_array($arr[$i]["lower"])) {
            typeAjax($arr[$i]["lower"], $id, $dopost, $more);
        }
    }
    return '{"state": 100, "info": "保存成功！"}';
}

/* 获取分类信息 */
function getTypeInfo($params){
    extract($params);
    $typeHandels = new handlers($service, "typeDetail");
    $typeConfig = $typeHandels->getHandle($typeid);

    if (is_array($typeConfig) && $typeConfig['state'] == 100) {
        $typeConfig = $typeConfig['info'];
        if (is_array($typeConfig)) {
            foreach ($typeConfig[0] as $key => $value) {
                if ($key == $return) {
                    return $value;
                }
            }
        }
    }
}

/* 获取分类名称 */
function getTypeName($params){
    $params['return'] = "typename";
    return getTypeInfo($params);
}

/**
 * 删除文件
 *
 * @param $picpath string 要删除的图片路径
 * @param $type string 要删除的图片类型
 * @param $mod string 要删除的模块
 * @return array
 */
function delPicFile($picpath, $type, $mod){
    global $dsql;
    global $cfg_ftpState;
    global $cfg_ftpType;
    global $cfg_ftpSSL;
    global $cfg_ftpPasv;
    global $cfg_ftpUrl;
    global $cfg_uploadDir;
    global $cfg_ftpServer;
    global $cfg_ftpPort;
    global $cfg_ftpDir;
    global $cfg_ftpUser;
    global $cfg_ftpPwd;
    global $cfg_ftpTimeout;
    global $cfg_OSSUrl;
    global $cfg_OSSBucket;
    global $cfg_EndPoint;
    global $cfg_OSSKeyID;
    global $cfg_OSSKeySecret;
    global $cfg_fileUrl;
    global $cfg_basedir;
    global $cfg_QINIUAccessKey;
    global $cfg_QINIUSecretKey;
    global $cfg_QINIUbucket;
    global $cfg_QINIUdomain;
    global $autoload;
    global $HN_memory;

    $picpathArr = $picpathArr_ = array();
    $picpath = explode(",", $picpath);

    foreach ($picpath as $val) {
        $RenrenCrypt = new RenrenCrypt();
        $id = $RenrenCrypt->php_decrypt(base64_decode($val));

        if (!is_numeric($id)) return;

        $attachment = $dsql->SetQuery("SELECT `path` FROM `#@__attachment` WHERE `id` = " . $id);
        $results = $dsql->dsqlOper($attachment, "results");

        if (!$results) return;  //数据不存在
        $picpath = $results[0]['path'];

        $picpathArr_[] = $picpath;
        if ($type == "editor") {
            if (strpos($picpath, "file") !== false) {
                $picpath = explode("file/", $picpath);
            } elseif (strpos($picpath, "remote") !== false) {
                $picpath = explode("remote/", $picpath);
            } else {
                $picpath = explode("image/", $picpath);
            }
        } elseif ($type == "delFile" || $type == "delfile") {
            $picpath = explode("file/", $picpath);
        } else {
            $picpath = explode("large/", $picpath);
        }
        $picpathArr[] = $picpath[1];

        $attachment = $dsql->SetQuery("DELETE FROM `#@__attachment` WHERE `id` = " . $id);
        $dsql->dsqlOper($attachment, "update");

        //删除缓存
        $HN_memory->rm('attachment_' . $id);

    }
    $picpath = join(",", $picpathArr);

    if (!empty($picpath)) {
        if ($mod != "siteConfig" && $mod) {
            require(HUONIAOINC . "/config/" . $mod . ".inc.php");

            if (empty($customFtp)) {
                global $customFtp;
            }
            if (empty($custom_ftpState)) {
                global $custom_ftpState;
            }
            if (empty($custom_ftpServer)) {
                global $custom_ftpServer;
            }
            if (empty($custom_ftpPort)) {
                global $custom_ftpPort;
            }
            if (empty($custom_ftpUser)) {
                global $custom_ftpUser;
            }
            if (empty($custom_ftpPwd)) {
                global $custom_ftpPwd;
            }
            if (empty($custom_ftpTimeout)) {
                global $custom_ftpTimeout;
            }
            if (empty($custom_ftpSSL)) {
                global $custom_ftpSSL;
            }
            if (empty($custom_ftpPasv)) {
                global $custom_ftpPasv;
            }
            if (empty($custom_ftpDir)) {
                global $custom_ftpDir;
            }
            if (empty($custom_ftpUrl)) {
                global $custom_ftpUrl;
            }
            if (empty($custom_uploadDir)) {
                global $custom_uploadDir;
            }
            if (empty($custom_ftpType)) {
                global $custom_ftpType;
            }
            if (empty($custom_OSSUrl)) {
                global $custom_OSSUrl;
            }
            if (empty($custom_OSSBucket)) {
                global $custom_OSSBucket;
            }
            if (empty($custom_EndPoint)) {
                global $custom_EndPoint;
            }
            if (empty($custom_OSSKeyID)) {
                global $custom_OSSKeyID;
            }
            if (empty($custom_OSSKeySecret)) {
                global $custom_OSSKeySecret;
            }
            if (empty($custom_QINIUAccessKey)) {
                global $custom_QINIUAccessKey;
            }
            if (empty($custom_QINIUSecretKey)) {
                global $custom_QINIUSecretKey;
            }
            if (empty($custom_QINIUbucket)) {
                global $custom_QINIUbucket;
            }
            if (empty($custom_QINIUdomain)) {
                global $custom_QINIUdomain;
            }

            //默认FTP帐号
            if ($customFtp == 0) {
                $custom_ftpState = $cfg_ftpState;
                $custom_ftpType = $cfg_ftpType;
                $custom_ftpSSL = $cfg_ftpSSL;
                $custom_ftpPasv = $cfg_ftpPasv;
                $custom_ftpUrl = $cfg_ftpUrl;
                $custom_uploadDir = $cfg_uploadDir;
                $custom_ftpServer = $cfg_ftpServer;
                $custom_ftpPort = $cfg_ftpPort;
                $custom_ftpDir = $cfg_ftpDir;
                $custom_ftpUser = $cfg_ftpUser;
                $custom_ftpPwd = $cfg_ftpPwd;
                $custom_ftpTimeout = $cfg_ftpTimeout;
                $custom_OSSUrl = $cfg_OSSUrl;
                $custom_OSSBucket = $cfg_OSSBucket;
                $custom_EndPoint = $cfg_EndPoint;
                $custom_OSSKeyID = $cfg_OSSKeyID;
                $custom_OSSKeySecret = $cfg_OSSKeySecret;
                $custom_QINIUAccessKey = $cfg_QINIUAccessKey;
                $custom_QINIUSecretKey = $cfg_QINIUSecretKey;
                $custom_QINIUbucket = $cfg_QINIUbucket;
                $custom_QINIUdomain = $cfg_QINIUdomain;
            }

            //自定义FTP配置
            if ($customFtp == 1) {
                //阿里云OSS
                if ($custom_ftpType == 1) {
                    if (strpos($custom_OSSUrl, "https://") !== false) {
                        $site_fileUrl = $custom_OSSUrl;
                    } else {
                        $site_fileUrl = "https://" . $custom_OSSUrl;
                    }
                    //七牛云
                } elseif ($custom_ftpType == 2) {
                    if (strpos($custom_QINIUdomain, "http://") !== false) {
                        $site_fileUrl = $custom_QINIUdomain;
                    } else {
                        $site_fileUrl = "http://" . $custom_QINIUdomain;
                    }
                    //普通FTP
                } elseif ($custom_ftpState == 1) {
                    $site_fileUrl = $custom_ftpUrl . str_replace(".", "", $custom_ftpDir);
                    //本地
                } else {
                    if ($customUpload == 1) {
                        $site_fileUrl = $custom_uploadDir;
                    } else {
                        $site_fileUrl = $cfg_uploadDir;
                    }
                }
                //系统默认
            } else {
                //阿里云OSS
                if ($cfg_ftpType == 1) {
                    if (strpos($cfg_OSSUrl, "https://") !== false) {
                        $site_fileUrl = $cfg_OSSUrl;
                    } else {
                        $site_fileUrl = "https://" . $cfg_OSSUrl;
                    }
                    //七牛云
                } elseif ($cfg_ftpType == 2) {
                    if (strpos($cfg_QINIUdomain, "http://") !== false) {
                        $site_fileUrl = $cfg_QINIUdomain;
                    } else {
                        $site_fileUrl = "http://" . $cfg_QINIUdomain;
                    }
                    //普通FTP
                } elseif ($cfg_ftpState == 1) {
                    $site_fileUrl = $custom_ftpDir;
                    //本地
                } else {
                    $site_fileUrl = $cfg_uploadDir;
                }
            }

            //模块自定义情况
            //普通FTP模式
            if ($customFtp == 1 && $custom_ftpType == 0 && $custom_ftpState == 1) {
                $cfg_ftpType = 0;
                $cfg_ftpState = 1;
                $cfg_ftpDir = $custom_ftpDir;

                //阿里云OSS
            } elseif ($customFtp == 1 && $custom_ftpType == 1) {
                $cfg_ftpType = 1;
                $cfg_ftpState = 0;
                $cfg_ftpDir = $custom_uploadDir;

                //七牛云
            } elseif ($customFtp == 1 && $custom_ftpType == 2) {
                $cfg_ftpType = 2;
                $cfg_ftpState = 0;
                $cfg_ftpDir = $custom_uploadDir;

                //本地
            } elseif ($customFtp == 1 && $custom_ftpType == 0 && $custom_ftpState == 0) {
                $cfg_ftpType = 3;
                $cfg_ftpState = 0;
                $cfg_ftpDir = $custom_uploadDir;

            }

        } else {
            //阿里云OSS
            if ($cfg_ftpType == 1) {
                if (strpos($cfg_OSSUrl, "https://") !== false) {
                    $site_fileUrl = $cfg_OSSUrl;
                } else {
                    $site_fileUrl = "https://" . $cfg_OSSUrl;
                }
                //七牛云
            } elseif ($cfg_ftpType == 2) {
                if (strpos($cfg_QINIUdomain, "http://") !== false) {
                    $site_fileUrl = $cfg_QINIUdomain;
                } else {
                    $site_fileUrl = "http://" . $cfg_QINIUdomain;
                }
                //普通FTP
            } elseif ($cfg_ftpState == 1) {
                $site_fileUrl = $cfg_ftpDir;
                //本地
            } else {
                $site_fileUrl = $cfg_uploadDir;
            }
        }

        //列出要删除的文件类型
        if ($type == "delThumb" || $type == "delthumb") {
            $pathType = "thumb";
            $pathModel = array("small", "middle", "large", "o_large");
        } else if ($type == "delAtlas" || $type == "delatlas") {
            $pathType = "atlas";
            $pathModel = array("small", "large");
        } else if ($type == "delConfig" || $type == "delconfig") {
            $pathType = "config";
            $pathModel = array("large");
        } else if ($type == "delFriendLink" || $type == "delfriendLink") {
            $pathType = "friendlink";
            $pathModel = array("large");
        } else if ($type == "delAdv" || $type == "deladv" || $type == "deladvthumb") {
            $pathType = $type == "deladvthumb" ? "advthumb" : "adv";
            $pathModel = array("large");
        } else if ($type == "delCard" || $type == "delcard") {
            $pathType = "card";
            $pathModel = array("large");
        } else if ($type == "delLogo" || $type == "dellogo") {
            $pathType = "logo";
            $pathModel = array("large");
        } else if ($type == "delBrand" || $type == "delbrand") {
            $pathType = "brand";
            $pathModel = array("large");
        } else if ($type == "delbrandLogo" || $type == "delbrandlogo") {
            $pathType = "brandLogo";
            $pathModel = array("small", "middle", "large");
        } else if ($type == "delFile" || $type == "delfile" || $type == "delfilenail") {
            $pathType = "file";
            $pathModel = array("large");
        } else if ($type == "delVideo" || $type == "delvideo") {
            $pathType = "video";
            $pathModel = array("large");
        } else if ($type == "delFlash" || $type == "delflash") {
            $pathType = "flash";
            $pathModel = array("large");
        } else if ($type == "delPhoto" || $type == "delphoto") {
            $pathType = "photo";
            $pathModel = array("small", "middle", "large");
        } else if ($type == "delWxUpImg") {
            $pathType = "wxupimg";
            $pathModel = array("large");
        } else if ($type == "delWxminProgram") {
            $pathType = "wxminProgram";
            $pathModel = array("large");
        } else if ($type == "delSingle" || $type == "delsingle") {
            $pathType = "single";
            $pathModel = array("large");
        }


        //编辑器附件
        if ($type == "editor") {
            //阿里云OSS
            if ($cfg_ftpType == 1) {
                $OSSConfig = array();
                if ($mod != "siteConfig") {
                    $OSSConfig = array(
                        "bucketName" => $custom_OSSBucket,
                        "endpoint" => $custom_EndPoint,
                        "accessKey" => $custom_OSSKeyID,
                        "accessSecret" => $custom_OSSKeySecret
                    );
                }

                include_once(HUONIAOINC . '/class/aliyunOSS.class.php');
                $aliyunOSS = new aliyunOSS($OSSConfig);

                foreach ($picpathArr_ as $val) {
                    $aliyunOSS->delete($val);
                    $ossError = $aliyunOSS->error();
                }

                if ($ossError) {
                    $error = $ossError;
                }
                //七牛云
            } elseif ($cfg_ftpType == 2) {
                $autoload = true;
                $accessKey = $custom_QINIUAccessKey;
                $secretKey = $custom_QINIUSecretKey;
                // 构建鉴权对象
                $auth = new Auth($accessKey, $secretKey);
                // 要上传的空间
                $bucket = $custom_QINIUbucket;
                //初始化BucketManager
                $bucketMgr = new BucketManager($auth);
                foreach ($picpathArr_ as $val) {
                    $err = $bucketMgr->delete($bucket, $val);
                }
                if ($err !== null) {
                    $error = $err;
                }
                //普通FTP模式
            } elseif ($cfg_ftpType === 0 && $cfg_ftpState === 1) {
                $ftpConfig = array();
                if ($mod != "siteConfig" && $customFtp == 1 && $custom_ftpState == 1) {
                    $ftpConfig = array(
                        "on" => $custom_ftpState, //是否开启
                        "host" => $custom_ftpServer, //FTP服务器地址
                        "port" => $custom_ftpPort, //FTP服务器端口
                        "username" => $custom_ftpUser, //FTP帐号
                        "password" => $custom_ftpPwd,  //FTP密码
                        "attachdir" => $custom_ftpDir,  //FTP上传目录
                        "attachurl" => $custom_ftpUrl,  //远程附件地址
                        "timeout" => $custom_ftpTimeout,  //FTP超时
                        "ssl" => $custom_ftpSSL,  //启用SSL连接
                        "pasv" => $custom_ftpPasv  //被动模式连接
                    );
                }

                global $autoload;
                global $handler;
                $autoload = false;
                $handler = false;
                $huoniao_ftp = new ftp($ftpConfig);
                $huoniao_ftp->connect();
                if ($huoniao_ftp->connectid) {
                    foreach ($picpathArr_ as $val) {
                        $filePath = $cfg_ftpDir . $val;
                        if (!$huoniao_ftp->ftp_delete($filePath)) {
                            $error = "要删除的文件不存在";
                        }
                    }
                }

                //本地附件
            } else {
                foreach ($picpathArr_ as $val) {
                    $filePath = HUONIAOROOT . $site_fileUrl . $val;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    } else {
                        $error = "要删除的文件不存在";
                    }
                }
            }

            //输出错误信息
            if (!empty($error)) {
                //echo '{"state":"ERROR","info":"'.$error.'"}';
            }

            //缩略图、图集、附件
        } else {
            if (!empty($pathModel)) {
                //循环操作相关文件
                foreach ($pathModel as $key => $value) {
                    $imgPath = explode(",", $picpath);
                    foreach ($imgPath as $val) {

                        //阿里云OSS
                        if ($cfg_ftpType == 1) {
                            $OSSConfig = array();
                            if ($mod != "siteConfig") {
                                $OSSConfig = array(
                                    "bucketName" => $custom_OSSBucket,
                                    "endpoint" => $custom_EndPoint,
                                    "accessKey" => $custom_OSSKeyID,
                                    "accessSecret" => $custom_OSSKeySecret
                                );
                            }

                            global $autoload;
                            $autoload = false;
                            require_once HUONIAOINC . '/class/aliyunOSS.class.php';
                            $aliyunOSS = new aliyunOSS($OSSConfig);
                            $aliyunOSS->delete($mod . "/" . $pathType . "/" . $value . "/" . $val);
                            $ossError = $aliyunOSS->error();

                            if ($ossError) {
                                $error = $ossError;
                            }
                            //七牛云
                        } elseif ($cfg_ftpType == 2) {
                            $autoload = true;
                            $accessKey = $custom_QINIUAccessKey;
                            $secretKey = $custom_QINIUSecretKey;
                            //echo $accessKey;die;
                            // 构建鉴权对象
                            $auth = new Auth($accessKey, $secretKey);
                            // 要上传的空间
                            $bucket = $custom_QINIUbucket;
                            //初始化BucketManager
                            $bucketMgr = new BucketManager($auth);
                            $err = $bucketMgr->delete($bucket, $mod . "/" . $pathType . "/" . $value . "/" . $val);
                            if ($err !== null) {
                                $error = $err;
                            }
                            //普通FTP模式
                        } elseif ($cfg_ftpType == 0 && $cfg_ftpState == 1) {
                            $ftpConfig = array();
                            if ($mod != "siteConfig" && $customFtp == 1 && $custom_ftpState == 1) {
                                $ftpConfig = array(
                                    "on" => $custom_ftpState, //是否开启
                                    "host" => $custom_ftpServer, //FTP服务器地址
                                    "port" => $custom_ftpPort, //FTP服务器端口
                                    "username" => $custom_ftpUser, //FTP帐号
                                    "password" => $custom_ftpPwd,  //FTP密码
                                    "attachdir" => $custom_ftpDir,  //FTP上传目录
                                    "attachurl" => $custom_ftpUrl,  //远程附件地址
                                    "timeout" => $custom_ftpTimeout,  //FTP超时
                                    "ssl" => $custom_ftpSSL,  //启用SSL连接
                                    "pasv" => $custom_ftpPasv  //被动模式连接
                                );
                            }


                            global $autoload;
                            global $handler;
                            $autoload = false;
                            $handler = false;
                            $huoniao_ftp = new ftp($ftpConfig);
                            $huoniao_ftp->connect();
                            if ($huoniao_ftp->connectid) {
                                $filePath = $cfg_ftpDir . "/" . $mod . "/" . $pathType . "/" . $value . "/" . $val;
                                if (!$huoniao_ftp->ftp_delete($filePath)) {
                                    $error = "要删除的文件不存在";
                                }
                            }

                            //本地附件
                        } else {
                            $filePath = HUONIAOROOT . $site_fileUrl . "/" . $mod . "/" . $pathType . "/" . $value . "/" . $val;
                            include_once(HUONIAOINC . '/class/file.class.php');
                            if (!unlinkFile($filePath)) {
                                $error = "要删除的文件不存在";
                            }
                        }
                    }

                }

                //输出错误信息
                if (!empty($error)) {
                    //echo '{"state":"ERROR","info":"'.$error.'"}';
                }

            } else {
                //echo '{"state":"ERROR","info":"PathModel Is Wrong!"}';
            }
        }

    } else {
        //echo '{"state":"ERROR","info":"Empty Path!"}';
    }
}

//提取内容图片并删除
function delEditorPic($body, $dopost){
    global $cfg_attachment;
    $attachment = substr($cfg_attachment, 1, strlen($cfg_attachment));

    global $cfg_basehost;
    $attachment = str_replace("http://" . $cfg_basehost, "", $cfg_attachment);
    $attachment = str_replace("https://" . $cfg_basehost, "", $cfg_attachment);
    $attachment = substr($attachment, 1, strlen($attachment));

    $attachment = str_replace("/", "\/", $attachment);
    $attachment = str_replace(".", "\.", $attachment);
    $attachment = str_replace("?", "\?", $attachment);
    $attachment = str_replace("=", "\=", $attachment);

    preg_match_all("/$attachment(.*)[\"|'| ]/isU", $body, $picList);
    $picList = array_unique($picList[1]);

    //删除内容图片
    if (!empty($picList)) {
        $editorPic = array();
        foreach ($picList as $v_) {
            $editorPic[] = $v_;
        }
        $editorPic = !empty($editorPic) ? join(",", $editorPic) : "";
        if (!empty($editorPic)) {
            delPicFile($editorPic, "editor", $dopost);
        }
    }
}

//获取分类所有父级
function getParentArr($tab, $typeid){
    global $dsql;
    global $HN_memory;
    if (!empty($typeid)) {

        //网站地区读缓存
        $site_area_cache = $HN_memory->get('site_area_' . $typeid);
        if($tab == 'site_area' && $site_area_cache){
            return $site_area_cache;
        }else {
            $archives = $dsql->SetQuery("SELECT `id`, `parentid`, `typename` FROM `#@__" . $tab . "` WHERE `id` = " . $typeid);
            if($tab == "article"){
                $results = getCache($tab."_par", $archives, 0, array("sign" => $typeid));
            }else{
                $results = $dsql->dsqlOper($archives, "results");
            }
            if ($results) {
                if ($results[0]['parentid'] != 0) {
                    $results[]["lower"] = getParentArr($tab, $results[0]['parentid']);
                }
            }

            //写缓存
            if($tab == 'site_area') {
                $HN_memory->set('site_area_' . $typeid, $results);
            }

            return $results;
        }
    }
}

//只取数组中的分类名
function parent_foreach($arr, $type){
    global $data;
    $data = is_array($data) ? $data : array();
    if (!empty($arr)) {
        if (!is_array($arr) && $arr != NULL) {
            return $data;
        }
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                parent_foreach($val, $type);
            } else {
                if ($val != NULL && $key == $type) {
                    // $data[]=$val;
                    array_push($data, $val);
                }
            }
        }
        return $data;
    } else {
        return array();
    }
}

//笛卡尔集
function descartes(){
    $t = func_get_args();
    if(func_num_args() == 1 && is_array($t[0])){
        return call_user_func_array(__FUNCTION__, $t[0]);
    }
    $a = array_shift($t);
    if (!is_array($a)) $a = array($a);
    $a = array_chunk($a, 1);
    do {
        $r = array();
        $b = array_shift($t);
        if (!is_array($b)) $b = array($b);
        foreach ($a as $p)
            foreach (array_chunk($b, 1) as $q)
                $r[] = array_merge($p, $q);
        $a = $r;
    } while ($t);
    return $r;
}

/**
 * 记录操作日志
 *
 * @param $name string 运作
 * @param $note string 其它
 */
function adminLog($name = "", $note = ""){
    $dsql = new dsql($dbo);
    $userLogin = new userLogin($dbo);

    $archives = $dsql->SetQuery("INSERT INTO `#@__sitelog` (`admin`, `name`, `note`, `ip`, `pubdate`) VALUES (" . $userLogin->getUserID() . ", '$name', '" . str_replace("'", "\'", $note) . "', '" . GetIP() . "', '" . GetMkTime(time()) . "')");
    $result = $dsql->dsqlOper($archives, "update");
    if ($result != "ok") {
        //echo "管理员日志记录失败！";
    }
}

/*
 * 邮件发送函数
 * @param $email      string 收件人
 * @param $mailtitle  string 主题
 * @param $mailbody   string 内容
 */
function sendmail($email, $mailtitle, $mailbody){
    global $cfg_mail, $cfg_mailServer, $cfg_mailPort, $cfg_mailFrom, $cfg_mailUser, $cfg_mailPass, $siteCityName, $cfg_shortname;
    $mailServer = explode(",", $cfg_mailServer);
    $mailPort = explode(",", $cfg_mailPort);
    $mailFrom = explode(",", $cfg_mailFrom);
    $mailUser = explode(",", $cfg_mailUser);
    $mailPass = explode(",", $cfg_mailPass);

    $shortname = str_replace('$city', $siteCityName, stripslashes($cfg_shortname));

    $c_mailServer = $c_mailPort = $c_mailFrom = $c_mailUser = $c_mailPass = "";
    foreach ($mailServer as $key => $value) {
        if ($key == $cfg_mail) {
            $c_mailServer = $mailServer[$key];
            $c_mailPort = $mailPort[$key];
            $c_mailFrom = $mailFrom[$key];
            $c_mailUser = $mailUser[$key];
            $c_mailPass = $mailPass[$key];
        }
    }


    if (!empty($c_mailServer)) {

        require_once(HUONIAOINC . '/class/PHPMailer.class.php');

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->CharSet = "UTF-8";
        $mail->Host = $c_mailServer;
        $mail->SMTPAuth = true;
        $mail->Username = $c_mailUser;
        $mail->Password = $c_mailPass;
        if ($c_mailPort == "465") {
            $mail->SMTPSecure = 'ssl';
        }
        $mail->Port = $c_mailPort;

        //解决部分服务器无法正常发送的问题，原因是ssl认证未通过
        $mail->SMTPOptions = array(
      		'ssl' => array(
    		    'verify_peer' => false,
    		    'verify_peer_name' => false,
    		    'allow_self_signed' => true
  		    )
    		);

        $mail->setFrom($c_mailFrom, $shortname);
        $mail->addAddress($email);
        $mail->isHTML(true);

        $mail->Subject = $mailtitle;
        $mail->Body = htmlspecialchars_decode($mailbody);

        if (!$mail->send()) {
          require_once HUONIAOROOT."/api/payment/log.php";
          $logHandler= new CLogFileHandler(HUONIAOINC.'/noticeErrorMessage.log');
          $log = Log::Init($logHandler, 15);
          Log::DEBUG("邮件发送错误日志");
          Log::DEBUG("发送内容：" . json_encode(array("邮箱：" => $email, "标题：" => $mailtitle, "内容：" => $mailbody), JSON_UNESCAPED_UNICODE));
          Log::DEBUG("错误内容：" . json_encode($mail->ErrorInfo, JSON_UNESCAPED_UNICODE) . "\r\n\r\n");

          return $mail->ErrorInfo;
        }

    } else {
        return '邮件发送失败，ErrCode: 邮件配置2';
        exit();
    }

    // if(!empty($c_mailServer)){
    // 	$mailtype = 'HTML';
    // 	require_once(HUONIAOINC.'/class/mail.class.php');
    // 	$smtp = new smtp($c_mailServer, $c_mailPort, true, $c_mailFrom, $c_mailPass);
    // 	$smtp->debug = false;
    // 	if(!$smtp->smtp_sockopen($c_mailServer)){
    // 		return '邮件发送失败，ErrCode: 邮件配置1';exit();
    // 	}
    // 	$smtp->sendmail($email, $cfg_webname, $c_mailFrom, $mailtitle, htmlspecialchars_decode($mailbody), $mailtype);
    // }else{
    // 	return '邮件发送失败，ErrCode: 邮件配置2';exit();
    // }

}

/*
 * 短信发送函数
 * @param $phone          string   接收手机号码
 * @param $id             string   短信模板ID（如果类型为数字则代表当前系统的数据ID，如果为其他则代表其他平台的营销型短信模板）
 * @param $code           int      变量内容
 * @param $type           string   类型
 * @param $has            boolean  是否已经存在
 * @param $promotion      boolean  是否为营销型短信
 * @
 */
function sendsms($phone, $id, $code, $type = "", $has = false, $promotion = false, $notify = "", $config = array()){

    global $dsql;
    global $userLogin;
    global $cfg_smsAlidayu;
    global $handler;
    $handler = false;

    $uid = $userLogin->getMemberID();
    $ip = GetIP();
    $now = GetMkTime(time());

    //前台未登录的获取后台登录帐号
    if ($uid == -1) {
        $uid = $userLogin->getUserID();
    }

    //获取短信内容
    $tempid = "";
    $content = "";
    if (is_numeric($id) && $id == 1 && $notify) {
        $config['code'] = $code;
        $contentTpl = getInfoTempContent("sms", $notify, $config);
        if ($contentTpl) {
            $tempid = $contentTpl['title'];
            $content = $contentTpl['content'];
            if($tempid == "" || $content == ""){
                return array("state" => 200, "info" => '短信通知未启用');
            }
        }else{
            return array("state" => 200, "info" => '短信通知未启用');
        }

//        if ($tempid == "" && $content == "") {
//            return array("state" => 200, "info" => "短信通知未开启，发送失败！");
//        }
    } else {
        //如果是营销型短信或重新发送短信，短信内容则为ID
        if ($promotion) {
            $content = $id;
        } else {
            $content = "您的短信验证码：" . $code;
        }
    }

//    if(empty($content) || empty($tempid)) return;

    //如果是阿里平台
    if ($cfg_smsAlidayu == 1) {

//        $phone_ = substr($phone, -11);
        $phone_ = $phone;

        $archives = $dsql->SetQuery("SELECT * FROM `#@__sitesms` WHERE `state` = 1");
        $results = $dsql->dsqlOper($archives, "results");
        if ($results) {

            $data = $results[0];
            $portal = $data['title'];
            $username = $data['username'];
            $password = $data['password'];
            $signCode = $data['signCode'];

            //如果是数据
            if (is_numeric($id) && $id == 1 && $notify) {
                // $sql = $dsql->SetQuery("SELECT `tempid` FROM `#@__site_smstemp` WHERE `id` = $id");
                // $ret = $dsql->dsqlOper($sql, "results");
                // if($ret){
                //     $tempid = $ret[0]['tempid'];
                // }else{
                //     //阿里大鱼测试模板
                //     $tempid   = "SMS_10652302";
                //     $signCode = "大鱼测试";
                // }
            } else {
                $tempid = $id;
            }

            if ($tempid) {

                //阿里云
                if ($portal == "阿里云") {

                    include_once HUONIAOINC . '/class/sms/aliyun/SendSmsRequest.php';

                    //短信API产品名
                    $product = "Dysmsapi";
                    //短信API产品域名
                    $domain = "dysmsapi.aliyuncs.com";
                    //暂时不支持多Region
                    $region = "cn-hangzhou";

                    //初始化访问的acsCleint
                    $profile = DefaultProfile::getProfile($region, $username, $password);
                    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
                    $acsClient = new DefaultAcsClient($profile);

                    $request = new SendSmsRequest();
                    //必填-短信接收号码
                    $request->setPhoneNumbers($phone_);
                    //必填-短信签名
                    $request->setSignName($signCode);
                    //必填-短信模板Code
                    $request->setTemplateCode($tempid);
                    //选填-假如模板中存在变量需要替换则为必填(JSON格式)
                    //测试短信不需要传递变量
                    if (is_numeric($id) && $id == 1 && $notify) {
                        $paramData = array();
                        foreach ($config as $key => $value) {
                            if ($key != "url") {
                                array_push($paramData, "\"$key\":\"$value\"");
                            }
                        }
                        $request->setTemplateParam("{" . join(",", $paramData) . "}");
                    }

                    //发起访问请求
                    $acsResponse = $acsClient->getAcsResponse($request);
                    $resp = objtoarr($acsResponse);
                    if ($resp['Code'] == "OK") {
                        $return = "ok";
                    } else {
                        require_once HUONIAOROOT."/api/payment/log.php";
                        $logHandler= new CLogFileHandler(HUONIAOINC.'/noticeErrorMessage.log');
                        $log = Log::Init($logHandler, 15);
                        Log::DEBUG("短信发送错误日志");
                        Log::DEBUG("发送内容：" . json_encode(array("平台：" => "阿里云", "模板ID：" => $tempid, "发送号码：" => $phone_, "参数：" => $paramData, "签名：" => $signCode), JSON_UNESCAPED_UNICODE));
                        Log::DEBUG("错误返回：" . json_encode($resp, JSON_UNESCAPED_UNICODE) . "\r\n\r\n");

                        $return = "发送失败，" . $resp['Message'];
                    }

                    //阿里大于
                } elseif (strstr($portal, "大鱼") || strstr($portal, "大于")) {
                    $c = new TopClient();
                    $c->appkey = $username;
                    $c->secretKey = $password;
                    $req = new AlibabaAliqinFcSmsNumSendRequest();
                    $req->setSmsType("normal");
                    $req->setSmsFreeSignName($signCode);
                    //测试短信不需要传递变量
                    if (is_numeric($id) && $id == 1 && $notify) {
                        $paramData = array();
                        foreach ($config as $key => $value) {
                            if ($key != "url" && $key != "domain") {
                                array_push($paramData, $key . ":'" . $value . "'");
                            }
                        }
                        $req->setSmsParam("{" . join(",", $paramData) . "}");
                    } else {
                        $req->setSmsParam("{customer: '火鸟客服'}");
                    }


                    $req->setRecNum($phone_);
                    $req->setSmsTemplateCode($tempid);
                    $resp = objtoarr($c->execute($req));

                    if ($resp['result'] && $resp['result']['success']) {
                        $return = "ok";
                    } else {
                        require_once HUONIAOROOT."/api/payment/log.php";
                        $logHandler= new CLogFileHandler(HUONIAOINC.'/noticeErrorMessage.log');
                        $log = Log::Init($logHandler, 15);
                        Log::DEBUG("短信发送错误日志");
                        Log::DEBUG("发送内容：" . json_encode(array("平台：" => "阿里大于：", "模板ID：" => $tempid, "发送号码：" => $phone_, "参数：" => $paramData, "签名：" => $signCode), JSON_UNESCAPED_UNICODE));
                        Log::DEBUG("错误返回：" . json_encode($resp, JSON_UNESCAPED_UNICODE) . "\r\n\r\n");

                        $return = "发送失败，CODE[" . $resp['code'] . "]，MSG[" . $resp['msg'] . "], SUB_MSG[" . $resp['sub_msg'] . "]";
                    }
                }

            } else {
                $return = "短信模板ID未设置！";
            }

        } else {
            $return = "发送失败，短信平台未配置！";
        }



    } elseif($cfg_smsAlidayu == 2){//腾讯云短信
//    	$phone_ = substr($phone, -11);
//    	//获取区号
//    	$areaCode = substr($phone, 0,-11);
//    	$areaCode = !empty($areaCode) ? $areaCode : '86';

        $areaCode = (int)$_REQUEST['areaCode'];
        $areaCode = !empty($areaCode) ? $areaCode : 86;

        if($areaCode == 86) {
            $phone_  = $phone = substr($phone, -11);
        }else{
            $phone_ = $areaCode ? substr($phone, strlen($areaCode)) : $phone;
        }

  		include_once HUONIAOINC . '/class/sms/tencent/SmsSingleSender.php';

  		$archives = $dsql->SetQuery("SELECT * FROM `#@__sitesms` WHERE `state` = 1");
      $results = $dsql->dsqlOper($archives, "results");
      if ($results) {
  			$data = $results[0];
            $username = $data['username'];
            $password = $data['password'];
            $signCode = $data['signCode'];
  			// 短信应用SDK AppID
  			$appid ="$username";
  			// 短信应用SDK AppKey
  			$appkey = "$password";
  			// 短信模板ID
  			$templateId = $tempid;
  			// 签名
  			$smsSign = "$signCode";

  			$ssender = new SmsSingleSender($appid, $appkey);
  			//获取通知模板
		    $sql = $dsql->SetQuery("SELECT * FROM `#@__site_notify` WHERE `title` = '$notify' AND `state` = 1");
		    $ret = $dsql->dsqlOper($sql, "results");
		    if($ret){
  				//短信模板
  				$data=$ret[0];
	        if ($data['sms_state']) {
	            $tencentContent = $data['sms_body'];
	        }
		    }
  			$paramData = array();
  			foreach($config as $key => $value){
  				if(strpos($tencentContent,$key)){
  					array_push($paramData, $value);
  				}
  			}

          /**
           * 腾讯云
           * 1、send 方法要把模板写死才能用 用sendWithParam这个方法
           * 2、$paramData 模板参数与传参数量对应才可以。
           */
  		    $result = $ssender->sendWithParam($areaCode, $phone_, $templateId,$paramData, $smsSign, "", "");
  		    $rsp = json_decode($result);
  		    $resp = objtoarr($rsp);
  		    if($resp['result']==0){
  				$return = "ok";
  		    }else{
            require_once HUONIAOROOT."/api/payment/log.php";
            $logHandler= new CLogFileHandler(HUONIAOINC.'/noticeErrorMessage.log');
            $log = Log::Init($logHandler, 15);
            Log::DEBUG("短信发送错误日志");
            Log::DEBUG("发送内容：" . json_encode(array("平台：" => "腾讯云", "区号：" => $areaCode, "发送号码：" => $phone_, "模板ID：" => $templateId, "参数：" => $paramData, "签名：" => $smsSign), JSON_UNESCAPED_UNICODE));
            Log::DEBUG("错误返回：" . json_encode($resp, JSON_UNESCAPED_UNICODE) . "\r\n\r\n");

  				$return = "发送失败";
  		    }
        }else{
			    $return = "发送失败，短信平台未配置！";
        }

        //其他普通短信平台
    } else {
        $sms = new sms($dbo);
        $return = $sms->send($phone, $content);
    }

    if ($return == "ok") {
        if ($has) {
            $archives = $dsql->SetQuery("UPDATE `#@__site_messagelog` SET `code` = '$code', `body` = '$content', `pubdate` = '$now', `ip` = '$ip' WHERE `type` = 'phone' AND `lei` = '$type' AND `user` = '$phone'");
            $results = $dsql->dsqlOper($archives, "update");
        } else {
            messageLog("phone", $type, $phone, $title, $content, $uid, 0, $code, $tempid);
        }
        return "ok";

    } else {
        messageLog("phone", $type, $phone, $title, $content, $uid, 1, $code, $tempid);
        return array("state" => 200, "info" => $return);
    }

}

/*
 * 记录信息发送日志
 * @param $type    string 信息类型
 * @param $lei     string 类别
 * @param $user    string 收件人
 * @param $title   string 主题
 * @param $body    string 信息内容
 * @param $by      int    操作人
 * @param $state   int    状态
 * @param $code    string 验证关键字
 */
function messageLog($type, $lei, $user, $title, $body, $by, $state, $code = "", $tempid = ""){
    $dsql = new dsql($dbo);
    $userLogin = new userLogin($dbo);
    $ip = GetIP();

    $archives = $dsql->SetQuery("INSERT INTO `#@__site_messagelog` (`type`, `lei`, `user`, `title`, `body`, `code`, `tempid`, `by`, `state`, `pubdate`, `ip`) VALUES ('$type', '$lei', '$user', '$title', '$body', '$code', '$tempid', $by, $state, '" . GetMkTime(time()) . "', '$ip')");
    $result = $dsql->dsqlOper($archives, "update");
    if ($result != "ok") {
        //echo "信息发送日志记录失败！";
    }
}


/*
 * 获取邮件、短信发送内容
 * @param string $type 类型 sms: 短信  mail: 邮件
 * @param int $id   模板ID
 * @param array config 系统配置参数
 * @return string
 */
function getInfoTempContent($type, $notify, $config = array()){
    global $dsql;
    global $cfg_basehost;
    global $cfg_webname;
    global $cfg_shortname;
    global $siteCityName;
    global $cfg_attachment;
    global $cfg_weblogo;
    global $cfg_hotline;
    $time = date("Y-m-d H:i:s", time());

    if (empty($type) || empty($notify)) return "";

    $webname = str_replace('$city', $siteCityName, stripslashes($cfg_webname));
    $shortname = str_replace('$city', $siteCityName, stripslashes($cfg_shortname));

    $configArr = array(
        "basehost" => $cfg_basehost,
        "webname" => $webname,
        "shortname" => $shortname,
        "weblogo" => $cfg_attachment . $cfg_weblogo,
        "hotline" => $cfg_hotline,
        "time" => $time
    );

    if ($config) {
        foreach ($config as $key => $value) {
            $configArr[$key] = $value;
        }
    }

    $return = array();

    //获取通知模板
    $sql = $dsql->SetQuery("SELECT * FROM `#@__site_notify` WHERE `title` = '$notify' AND `state` = 1");
    $ret = $dsql->dsqlOper($sql, "results");
    if ($ret) {

        $data = $ret[0];

        $title = "";
        $content = "";

        //邮件模板
        if ($type == "mail" && $data['email_state']) {
            $title = $data['email_title'];
            $content = $data['email_body'];
        }

        //短信模板
        if ($type == "sms" && $data['sms_state']) {
            $title = $data['sms_tempid'];
            $content = $data['sms_body'];
        }

        //短信模板
        if ($type == "wechat" && $data['wechat_state']) {
            $title = $data['wechat_tempid'];
            $content = $data['wechat_body'];
        }

        //网页即时消息
        if ($type == "site" && $data['site_state']) {
            $title = $data['site_title'];
            // $content = $data['site_body'];
            $content = $data['wechat_body'] ? $data['wechat_body'] : $data['site_body'];  //新版需要具体内容，换成公众号的模板内容。by:gz 20190614
        }

        //APP推送
        if ($type == "app" && $data['app_state']) {
            $title = $data['app_title'];
            $content = $data['app_body'];
        }

        if ($title || $content) {
            foreach ($configArr as $key => $value) {
                if ($key == "username"){
                    $sql = $dsql->SetQuery("SELECT `nickname`, `realname` FROM `#@__member` WHERE `username` = '$value'");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $value = $ret[0]['realname'] ? $ret[0]['realname'] : ($ret[0]['nickname'] ? $ret[0]['nickname'] : $value);
                    }
                }
                if ($title) {
                    $title = str_replace("$" . $key, $value, $title);
                }
                if ($content) {
                    $content = str_replace("$" . $key, $value, $content);
                }
            }
        }

        //新版替换模板key值 by:gz 20190614
        if($type == "site" && $content && $configArr['fields']){
            $fields = $configArr['fields'];
            foreach ($fields as $key => $value) {
                $content = str_replace($key, $value, $content);
            }
        }

        return array("title" => $title, "content" => $content);

    } else {
        return array("title" => "", "content" => "");
    }

}


/*
 * 载入脚本、样式
 * @param $type   string 文件类型
 * @param $file   array  文件列表
 */
function includeFile($type, $file = array()){
    global $cfg_attachment;
    global $cfg_secureAccess;
    global $cfg_basehost;
    global $cfg_staticVersion;
    $v = $cfg_staticVersion;
    $f = !empty($file) ? '&f=' . join(",", $file) : "";
    if ($type == 'css') {
        $fileArr = array();
        $fileArr[] = "<link rel='stylesheet' type='text/css' href='" . $cfg_secureAccess . $cfg_basehost . "/static/css/admin/datetimepicker.css?v=$v' />";
        $fileArr[] = "<link rel='stylesheet' type='text/css' href='" . $cfg_secureAccess . $cfg_basehost . "/static/css/admin/common.css?v=$v' />";
        $fileArr[] = "<link rel='stylesheet' type='text/css' href='" . $cfg_secureAccess . $cfg_basehost . "/static/css/admin/bootstrap.css?v=$v' />";
        foreach ($file as $key => $value) {
            $fileArr[] = "<link rel='stylesheet' type='text/css' href='" . $cfg_secureAccess . $cfg_basehost . "/static/css/" . $value . "?v=$v'/>";
        }
        return join("\r\n", $fileArr) . "\r\n<script>var cfg_attachment = '" . $cfg_attachment . "';</script>";
        //return "<link rel='stylesheet' type='text/css' href='".$hs."' />\r\n<script>var cfg_attachment = '".$cfg_attachment."';</script>";
    } elseif ($type == 'js') {
        $fileArr = array();
        // $fileArr[] = "<script>document.domain = '".$cfg_basehost."';</script>";
        $fileArr[] = "<script type='text/javascript' src='" . $cfg_secureAccess . $cfg_basehost . "/static/js/core/jquery-1.8.3.min.js?v=$v'></script>";
        $fileArr[] = "<script type='text/javascript' src='" . $cfg_secureAccess . $cfg_basehost . "/static/js/admin/common.js?v=$v'></script>";
        $fileArr[] = "<script type='text/javascript' src='" . $cfg_secureAccess . $cfg_basehost . "/static/js/ui/jquery.dialog-4.2.0.js?v=$v'></script>";
        foreach ($file as $key => $value) {
            $fileArr[] = "<script type='text/javascript' src='" . $cfg_secureAccess . $cfg_basehost . "/static/js/" . $value . "?v=$v'></script>";
        }
        return join("\r\n", $fileArr);

        //return "<script type='text/javascript' src='".$hs."'></script>";
    } elseif ($type == 'editor') {
        return "<script type='text/javascript' src='" . $cfg_secureAccess . $cfg_basehost . "/include/ueditor/ueditor.config.js?v=".$v."'></script>\r\n<script type='text/javascript' src='" . $cfg_secureAccess . $cfg_basehost . "/include/ueditor/ueditor.all.js?v=".$v."'></script>";
        //return '<script type="text/javascript" src="../../include/include.inc.php?t=include&f=ueditor/ueditor.config.js,ueditor/ueditor.all.js"></script>  <!-- editor -->';
    }
}

/**
 *  清除指定后缀的模板缓存或编译文件
 *
 * @access  public
 * @param  bool $is_cache 是否清除缓存还是清出编译文件
 * @param  string $ext 模块名
 *
 * @return int        返回清除的文件个数
 */
function clear_tpl_files($is_cache = true, $admin = false, $ext = ''){
    $dirs = array();

    if ($is_cache) {
        $dirs[] = HUONIAOROOT . "/templates_c/caches/";
    } else {
        if ($admin) {
            $dirs[] = HUONIAOROOT . "/templates_c/admin/";
        }
        $dirs[] = HUONIAOROOT . "/templates_c/compiled/";
    }

    $str_len = strlen($ext);
    $count = 0;

    foreach ($dirs AS $dir) {
        $folder = @opendir($dir);

        if ($folder === false) {
            continue;
        }

        while ($file = @readdir($folder)) {

            if ($file == '.' || $file == '..' || $file == 'index.htm' || $file == 'index.html') {
                continue;
            }

            if ($file == $ext) {
                deldir($dir . $file);
                $count++;
            }

        }
        @closedir($folder);
    }

    return $count;
}

/**
 * 清除模版编译文件
 *
 * @access  public
 * @param   mix $ext 模块名
 * @return  void
 */
function clear_compiled_files($ext = '', $admin = false){
    return clear_tpl_files(false, $admin, $ext);
}

/**
 * 清除模板缓存文件
 *
 * @access  public
 * @param   mix $ext 模块名
 * @return  void
 */
function clear_cache_files($ext = '', $admin = false){
    return clear_tpl_files(true, $admin, $ext);
}

/**
 * 清除模版编译和缓存文件
 *
 * @access  public
 * @param   mix $ext 模块名
 * @return  void
 */
function clear_all_files($ext = '', $admin = false){
    return clear_tpl_files(false, $admin, $ext) + clear_tpl_files(true, $admin, $ext);
}

//换行格式化
function RpLine($str){
    $str = str_replace("\r", "\\r", $str);
    $str = str_replace("\n", "\\n", $str);
    return $str;
}

/**
 * 域名操作
 *
 * @param string $opera 操作类型  check: 检测是否可用, update: 更新/新增
 * @param string $domain 域名
 * @param string $module 模块
 * @param string $part 栏目
 * @param int $id 信息ID域名
 * @param int $expires 过期时间
 * @param string $note 过期提示
 * @return void
 */
function operaDomain($opera, $domain, $module, $part, $id = 0, $expires = 0, $note = "", $state = 0, $refund = ""){

    if (!empty($domain) && !empty($module) && !empty($part)) {
        global $cfg_basehost;
        global $dsql;
        global $cfg_holdsubdomain;
        global $HN_memory;

        $expires = !empty($expires) ? $expires : 0;

        $domain = strtolower($domain);

        if ($cfg_basehost == $domain) die('{"state": 200, "info": ' . json_encode("设置的域名与系统网站域名冲突，请重新输入！") . '}');

        $hold = explode("|", $cfg_holdsubdomain);
        if (in_array($domain, $hold)) die('{"state": 200, "info": ' . json_encode("设置的域名属于系统保留域名，请重新输入！") . '}');

        if (!preg_match("/^([0-9a-z][0-9a-z-.]{0,49})?[0-9a-z]$/", $domain)) {
            die('{"state": 2001, "info": ' . json_encode("不符合域名规则，请重新输入！<br /><br />提示：<br />1. 域名可由英文字母（不区分大小写）、数字、\"-\"构成；<br />2. 不能使用空格及特殊字符（如!、$、&、?等）；<br />3. \"-\"不能单独填写，不能放在开头或结尾。") . '}');
        }

        $dsql = new dsql($dbo);

        //检查是否可用
        if ($opera == "check") {

            $archives = $dsql->SetQuery("SELECT `module`, `part`, `iid` FROM `#@__domain` WHERE `domain` = '$domain'");
            $results = $dsql->dsqlOper($archives, "results");
            if ($results) {
                if ($results[0]['iid'] == $id && $results[0]['module'] == $module && $results[0]['art'] == $art) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
            die;


            //更新/新增
        } elseif ($opera == "update") {

            $where = "";
            if (!empty($id)) {
                $where = " AND `iid` = " . $id;
            }

            //先检查数据库是否存在
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__domain` WHERE `module` = '$module' AND `part` = '$part'" . $where);
            $results = $dsql->dsqlOper($archives, "results");
            //存在
            if ($results) {

                //更新数据库
                $archives = $dsql->SetQuery("UPDATE `#@__domain` SET `domain` = '$domain', `expires` = '$expires', `note` = '$note', `state` = '$state', `refund` = '$refund' WHERE `module` = '$module' AND `part` = '$part' AND `iid` = " . $id);
                $results = $dsql->dsqlOper($archives, "results");
                if ($results) {

                    //更新缓存
                    $HN_memory->set('domain_' . $module . '_' . $part . ($id ? '_' . $id : ''), array(
                        "domain" => $domain,
                        "expires" => $expires,
                        "note" => $note,
                        "state" => $state,
                        "refund" => $refund
                    ));

                    return true;
                } else {
                    die('{"state": 200, "info": ' . json_encode("系统错误，域名操作失败！") . '}');
                }

                //不存在
            } else {

                //新增
                $archives = $dsql->SetQuery("INSERT INTO `#@__domain` (`domain`, `module`, `part`, `iid`, `expires`, `note`, `state`, `refund`) VALUES ('$domain', '$module', '$part', '$id', '$expires', '$note', '$state', '$refund')");
                $results = $dsql->dsqlOper($archives, "results");
                if ($results) {

                    //更新缓存
                    $HN_memory->set('domain_' . $module . '_' . $part . ($id ? '_' . $id : ''), array(
                        "domain" => $domain,
                        "expires" => $expires,
                        "note" => $note,
                        "state" => $state,
                        "refund" => $refund
                    ));

                    return true;
                } else {
                    die('{"state": 200, "info": ' . json_encode("系统错误，域名操作失败！") . '}');
                }

            }

        }

    } else {
        return false;
    }

}

/**
 * 获取指定模块的域名
 * @param string $module 模块
 * @param string $part 栏目
 * @param int $id 信息ID
 * @return array
 **/
function getDomain($module, $part, $id = 0){

    global $HN_memory;
    global $_G;

    if (!empty($module) && !empty($part)) {
        global $dsql;

        $where = "";
        if (!empty($id)) {
            $where = " AND `iid` = " . $id;
        }

        //读缓存
        $key = "domain_" . $module . "_" . $part . ($id ? "_" . $id : "");
        if(isset($_G[$key])){
            return $_G[$key];
        }
        $domain_cache = $HN_memory->get($key);
        if($domain_cache){
            return $domain_cache;
        }else {
            $archives = $dsql->SetQuery("SELECT `domain`, `expires`, `note`, `state`, `refund` FROM `#@__domain` WHERE `module` = '$module' AND `part` = '$part'" . $where);
            $results = $dsql->dsqlOper($archives, "results");
            if ($results) {

                //写缓存
                $HN_memory->set($key, $results[0]);
                $_G[$key] = $results[0];
                return $results[0];
            } else {
                $_G[$key] = array("domain" => "", "expires" => "", "note" => "");
                return $_G[$key];
            }
        }

    }

}

/**
 * 检测用户名是否可注册
 * @param string $username
 * @return string
 **/
function checkMember($username){
    global $cfg_holduser;
    global $langData;
    $hold = explode("|", $cfg_holduser);
    if (in_array($username, $hold)) die('{"state": 200, "info": ' . json_encode($langData['siteConfig'][33][52]) . '}');//该用户名归系统保留，请重新输入！

    $dsql = new dsql($dbo);
    $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` = '$username'");
    $results = $dsql->dsqlOper($archives, "results");
    if ($results) {
        return false;
    } else {
        return true;
    }
}

/**
 * 远程抓取
 * @param $uri
 * @param $config
 */
function getRemoteImage($uri, $config, $type, $dirname, $smallImg = false, $stream = false){

    global $customFtp;
    global $custom_ftpState;
    global $custom_ftpDir;
    global $custom_ftpServer;
    global $custom_ftpPort;
    global $custom_ftpUser;
    global $custom_ftpPwd;
    global $custom_ftpUrl;
    global $custom_ftpTimeout;
    global $custom_ftpSSL;
    global $custom_ftpPasv;
    global $custom_OSSBucket;
    global $custom_EndPoint;
    global $custom_OSSKeyID;
    global $custom_OSSKeySecret;
    global $custom_QINIUAccessKey;
    global $custom_QINIUSecretKey;
    global $custom_QINIUbucket;
    global $cfg_QINIUAccessKey;
    global $cfg_QINIUSecretKey;
    global $cfg_QINIUbucket;
    global $cfg_QINIUdomain;

    global $editor_uploadDir;
    global $editor_ftpState;
    global $editor_ftpDir;
    global $editor_ftpType;

    global $autoload;
    $autoload = false;

    if ($smallImg) {
        global $cfg_photoSmallWidth;
        global $cfg_photoSmallHeight;
        global $cfg_photoMiddleWidth;
        global $cfg_photoMiddleHeight;
        global $cfg_photoLargeWidth;
        global $cfg_photoLargeHeight;
        global $cfg_photoCutType;
        global $cfg_photoCutPostion;
        global $cfg_quality;
    }

    //忽略抓取时间限制
    set_time_limit(10);
    //ue_separate_ue  ue用于传递数据分割符号
    //$imgUrls = explode("ue_separate_ue", $uri);
    $tmpNames = array();
    foreach ($uri as $imgUrl) {

        if(!$stream) {
            $imgUrl = htmlspecialchars($imgUrl);
            $imgUrl = str_replace("&amp;", "&", $imgUrl);

            //http开头验证
            if (strpos($imgUrl, "http") !== 0) {
                //ERROR_HTTP_LINK";
                array_push($tmpNames, array(
                    "url" => $imgUrl
                ));
                continue;
            }

            preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
            $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

            //判断是否是合法 url
            if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
                //INVALID_URL;
                array_push($tmpNames, array(
                    "url" => $imgUrl
                ));
                continue;
            }

            preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
            $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

            // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
            $ip = gethostbyname($host_without_protocol);
            // 判断是否是私有 ip
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
                //INVALID_IP;
                array_push($tmpNames, array(
                    "url" => $imgUrl
                ));
                continue;
            }

            //获取请求头并检测死链
            // $heads = @get_headers($imgUrl, 1);
            // if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            //   //ERROR_DEAD_LINK;
            //   array_push($tmpNames, array(
            //     "url" => $imgUrl
            //   ));
            //   continue;
            // }

            //格式验证(扩展名验证和Content-Type验证)
            // 显示此段将会影响微信头像的抓取，因为微信头像没有后缀，by: guozi 20170505
            $fileType = str_replace(".", "", strtolower(strrchr($imgUrl, '.')));
            $fileType = (empty($fileType) || strlen($fileType) > 4) ? "png" : $fileType;
            // if (!in_array($fileType, $config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
            //   //ERROR_HTTP_CONTENTTYPE;
            //   array_push($tmpNames, array(
            //     "url" => $imgUrl
            //   ));
            //   continue;
            // }

            //读取文件内容
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $imgUrl);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            $img = curl_exec($curl);
            curl_close($curl);


            //大小验证
            $uriSize = strlen($img); //得到图片大小
            $allowSize = 1024 * $config['maxSize'];
            if ($uriSize > $allowSize) {
                array_push($tmpNames, array(
                    "url" => $imgUrl
                ));
                continue;
            }

        //文件流形式，用于小程序码
        }else{
            $img = $imgUrl;
            $fileType = 'png';
            $uriSize = strlen($img);
        }

        //创建保存位置
        $savePath = $config['savePath'];

        if (!file_exists($savePath)) {
            if (!mkdir("$savePath", 0777, true)) {
                continue;
            };
        }
        //写入文件
        $filename = rand(1, 10000) . time() . '.' . $fileType;
        $tmpName = $savePath . $filename;
        try {
            $fp2 = @fopen($tmpName, "a");
            @fwrite($fp2, $img);
            @fclose($fp2);

            $filePath = str_replace($dirname . $editor_uploadDir, "", $tmpName);

            //缩小图片
            if ($smallImg) {
                $remote = array();
                $imgInfo = array();

                //获取文件信息
                $imageInfo = @getimagesize($tmpName); // 获取文件大小
                $imgInfo["width"] = $imageInfo[0];   // 获取文件宽度
                $imgInfo["height"] = $imageInfo[1];  // 获取文件高度
                $imgInfo["type"] = $imageInfo[2];    // 获取文件类型
                $imgInfo["name"] = $filename;        // 获取文件名称

                $remote['imgInfo'] = $imgInfo;
                $remote['fullName'] = $tmpName;
                $remote['savePath'] = ".." . $editor_uploadDir . "/siteConfig/photo";

                $up = new upload("", null, false, true);
                $small = $up->smallImg($cfg_photoSmallWidth, $cfg_photoSmallHeight, "small", $cfg_quality, $remote);
                $middle = $up->smallImg($cfg_photoMiddleWidth, $cfg_photoMiddleHeight, "middle", $cfg_quality, $remote);
                $large = $up->smallImg($cfg_photoLargeWidth, $cfg_photoLargeHeight, "large", $cfg_quality, $remote);
            }

            //普通FTP模式
            if ($editor_ftpType == 0 && $editor_ftpState == 1 && !strstr($filePath, 'house/community')) {
                $ftpConfig = array();
                if ($type != "siteConfig" && $customFtp == 1 && $custom_ftpState == 1) {
                    $ftpConfig = array(
                        "on" => $custom_ftpState, //是否开启
                        "host" => $custom_ftpServer, //FTP服务器地址
                        "port" => $custom_ftpPort, //FTP服务器端口
                        "username" => $custom_ftpUser, //FTP帐号
                        "password" => $custom_ftpPwd,  //FTP密码
                        "attachdir" => $custom_ftpDir,  //FTP上传目录
                        "attachurl" => $custom_ftpUrl,  //远程附件地址
                        "timeout" => $custom_ftpTimeout,  //FTP超时
                        "ssl" => $custom_ftpSSL,  //启用SSL连接
                        "pasv" => $custom_ftpPasv  //被动模式连接
                    );
                }

                require_once HUONIAOINC . '/class/ftp.class.php';
                $huoniao_ftp = new ftp($ftpConfig);
                $huoniao_ftp->connect();
                if ($huoniao_ftp->connectid) {
                    $huoniao_ftp->upload(HUONIAOROOT . $editor_uploadDir . $filePath, $editor_ftpDir . $filePath);

                    if ($smallImg) {
                        $middleFile = str_replace("large", "middle", $filePath);
                        $fileRootUrl = HUONIAOROOT . $editor_uploadDir . $middleFile;
                        $huoniao_ftp->upload($fileRootUrl, $editor_ftpDir . $middleFile);

                        $smallFile = str_replace("large", "small", $filePath);
                        $fileRootUrl = HUONIAOROOT . $editor_uploadDir . $smallFile;
                        $huoniao_ftp->upload($fileRootUrl, $editor_ftpDir . $smallFile);
                    }
                }

                //阿里云OSS
            } elseif ($editor_ftpType == 1 && !strstr($filePath, 'house/community')) {
                $OSSConfig = array();
                if ($type != "siteConfig") {
                    $OSSConfig = array(
                        "bucketName" => $custom_OSSBucket,
                        "endpoint" => $custom_EndPoint,
                        "accessKey" => $custom_OSSKeyID,
                        "accessSecret" => $custom_OSSKeySecret
                    );
                }

                require_once HUONIAOINC . '/class/aliyunOSS.class.php';
                $aliyunOSS = new aliyunOSS($OSSConfig);
                $aliyunOSS->upload($filePath, HUONIAOROOT . $editor_uploadDir . $filePath);

                if ($smallImg) {
                    $middleFile = str_replace("large", "middle", $filePath);
                    $aliyunOSS->upload($middleFile, HUONIAOROOT . $editor_uploadDir . $middleFile);

                    $smallFile = str_replace("large", "small", $filePath);
                    $aliyunOSS->upload($smallFile, HUONIAOROOT . $editor_uploadDir . $smallFile);
                }
            } elseif ($editor_ftpType == 2 && !strstr($filePath, 'house/community')) {
                $accessKey = $cfg_QINIUAccessKey;
                $secretKey = $cfg_QINIUSecretKey;

                $autoload = true;
                // 构建鉴权对象
                $auth = new Auth($accessKey, $secretKey);
                // 要上传的空间
                $bucket = $cfg_QINIUbucket;
                // 上传到七牛后保存的文件名
                $key = $filePath;
                // 生成上传 Token
                $token = $auth->uploadToken($bucket,$key);
                // 初始化 UploadManager 对象并进行文件的上传。
                $uploadMgr = new UploadManager();
                // 调用 UploadManager 的 putFile 方法进行文件的上传。
                $uploadMgr->putFile($token, $key, HUONIAOROOT . $editor_uploadDir . $filePath);

                if ($smallImg) {
                    $middleFile = str_replace("large", "middle", $filePath);
                    $token = $auth->uploadToken($bucket,$middleFile);
                    $uploadMgr->putFile($token, $middleFile, HUONIAOROOT . $editor_uploadDir . $middleFile);

                    $smallFile = str_replace("large", "small", $filePath);
                    $token = $auth->uploadToken($bucket,$smallFile);
                    $uploadMgr->putFile($token, $smallFile, HUONIAOROOT . $editor_uploadDir . $smallFile);
                }
            }

            $dsql = new dsql($dbo);
            $userLogin = new userLogin($dbo);
            $userid = $userLogin->getUserID();

            $attachment = $dsql->SetQuery("INSERT INTO `#@__attachment` (`userid`, `filename`, `filetype`, `filesize`, `path`, `pubdate`) VALUES ('$userid', '" . $filename . "', 'image', '" . $uriSize . "', '" . $filePath . "', '" . GetMkTime(time()) . "')");
            $aid = $dsql->dsqlOper($attachment, "lastid");
            if (!is_numeric($aid)) die('{"state":"数据写入失败！"}');

            $RenrenCrypt = new RenrenCrypt();
            $fid = base64_encode($RenrenCrypt->php_encrypt($aid));


            global $cfg_basehost;
            global $cfg_attachment;
            $attachmentPath = str_replace("http://" . $cfg_basehost, "", str_replace("https://" . $cfg_basehost, "", $cfg_attachment));
            $path = $attachmentPath . $fid;

            array_push($tmpNames, array(
                "state" => "SUCCESS",
                "url" => $path,
                "turl" => getFilePath($fid),
                "fid" => $fid,
                "size" => $uriSize,
                "path" => $editor_uploadDir . $filePath,
                "filename" => $filename,
                "source" => $stream ? '小程序码' : htmlspecialchars($imgUrl)
            ));

        } catch (Exception $e) {
            array_push($tmpNames, array(
                "url" => $imgUrl
            ));
        }
    }

    $state = count($tmpNames) ? 'SUCCESS' : 'ERROR';

    $returnArr = json_encode(array(
        'state' => $state,
        'list' => $tmpNames
    ));

    return $returnArr;
}


/**
 * 获取附件的真实地址
 * @param string $file 文件ID
 * @return string *
 */
function getRealFilePath($file){

    global $dsql;
    global $cfg_secureAccess;
    global $cfg_basehost;
    global $cfg_fileUrl;
    global $cfg_uploadDir;
    global $editor_uploadDir;
    global $cfg_ftpType;
    global $cfg_ftpState;
    global $cfg_ftpUrl;
    global $cfg_ftpDir;
    global $cfg_OSSUrl;
    global $cfg_QINIUdomain;
    global $HN_memory;

    if (strstr($file, "http") || strstr($file, "//")) {
        return $file;
    }

    if (strstr($file, ".jpg") || strstr($file, ".jpeg") || strstr($file, ".gif") || strstr($file, ".png") || strstr($file, ".bmp")) {

      $fileDomain = $cfg_secureAccess . $cfg_basehost . (strstr($file, $cfg_uploadDir) ? '' : $cfg_uploadDir);
      if(strstr($file, '/static/')){
          $fileDomain = $cfg_secureAccess . $cfg_basehost;
      }else if($cfg_ftpTyp == 0 && $cfg_ftpState == 1){
        $fileDomain = $cfg_ftpUrl . $cfg_ftpDir;
        $file = strstr($file, '/uploads') ? str_replace('/uploads', '', $file) : $file;
      }elseif($cfg_ftpType == 1){
        $fileDomain = (strstr($cfg_OSSUrl, 'http') ? '' : $cfg_secureAccess) . $cfg_OSSUrl;
        $file = strstr($file, '/uploads') ? str_replace('/uploads', '', $file) : $file;
      }elseif($cfg_ftpTyp == 2){
        $fileDomain = $cfg_QINIUdomain;
      }
      return $fileDomain . $file;
    }

    $RenrenCrypt = new RenrenCrypt();
    $fid = is_numeric($file) ? $file : $RenrenCrypt->php_decrypt(base64_decode($file));

    //如果不是数字类型，则直接返回字段内容，主要用于兼容数据导入
    if (!is_numeric($fid)) return $cfg_secureAccess . $cfg_basehost . (strstr($file, $cfg_uploadDir) ? '' : $cfg_uploadDir) . $file;

    //读缓存
    $attachmentCache = $HN_memory->get('attachment_' . $fid);
    if($attachmentCache){
        $results = $attachmentCache;
    }else {
        $archives = $dsql->SetQuery("SELECT `path` FROM `#@__attachment` WHERE `id` = '$fid'");
        $results = $dsql->dsqlOper($archives, "results");
    }
    if ($results && is_array($results)) {

        //写入缓存
        $HN_memory->set('attachment_' . $fid, $results);

        $path = $results[0]['path'];
        $module = explode("/", $path);
        $module = $module[1];

        $incFile = HUONIAOINC . "/config/" . $module . ".inc.php";
        if (!file_exists($incFile)) return "";
        require $incFile;

        if (empty($editor_uploadDir) && $custom_uploadDir) {
            $editor_uploadDir = $custom_uploadDir;
        }else{
          $editor_uploadDir = $cfg_uploadDir;
        }

        //模块自定义配置
        if ($customFtp == 1) {

            //普通FTP模式
            if ($custom_ftpType == 0) {

                //启用远程FTP
                if ($custom_ftpState == 1) {
                    $site_fileUrl = $custom_ftpUrl . $custom_ftpDir;

                    //本地模式
                } else {
                    $site_fileUrl = $customUpload == 1 ? $cfg_secureAccess . $cfg_basehost . $custom_uploadDir : $cfg_secureAccess . $cfg_basehost . $editor_uploadDir;
                }

                //阿里云
            } elseif ($custom_ftpType == 1) {
                $site_fileUrl = (strstr($custom_OSSUrl, 'http') ? '' : $cfg_secureAccess) . $custom_OSSUrl;
            } elseif ($custom_ftpType == 2) {
                $site_fileUrl = (strstr($custom_QINIUdomain, 'http') ? '' : $cfg_secureAccess) . $custom_QINIUdomain . '/';
                // $path=substr(str_replace('/','_',$path),1);
            }

            //系统默认
        } else {

            //普通FTP模式
            if ($cfg_ftpType == 0) {

                //启用远程FTP
                if ($cfg_ftpState == 1) {
                    $site_fileUrl = $cfg_ftpUrl . $cfg_ftpDir;

                    //本地模式
                } else {
                    $site_fileUrl = $cfg_secureAccess . $cfg_basehost . $editor_uploadDir;
                }

                //阿里云
            } elseif ($cfg_ftpType == 1) {
                $site_fileUrl = (strstr($cfg_OSSUrl, 'http') ? '' : $cfg_secureAccess) . $cfg_OSSUrl;
            } elseif ($cfg_ftpType == 2) {
                $site_fileUrl = (strstr($cfg_QINIUdomain, 'http') ? '' : $cfg_secureAccess) . $cfg_QINIUdomain . '/';
                if(strpos($path, "photo") !== false){
                    // $path=substr(str_replace('/','_',$path),1);
                }
            }

        }

        return $site_fileUrl . $path;

    } else {
        return "";
    }

}


/**
 * 获取附件的真实地址
 * @param string $file 文件ID
 * @return string *
 */
function getFilePath($file){
    if (empty($file)) return "";
    global $cfg_hideUrl;
if ($cfg_hideUrl == 1) {
global $cfg_attachment;
return $cfg_attachment . $file;
} elseif ($cfg_hideUrl == 0) {
return getRealFilePath($file);
}
}


/**
 * 获取附件不同尺寸
 * @param string $url 文件地址
 * @param string $type 要转换的类型
 * @return string *
 */
function changeFileSize($params){
    extract($params);
    if(empty($url)) return false;
    global $cfg_hideUrl;
    global $cfg_ftpType;
    global $cfg_ftpState;
    global $cfg_ftpUrl;
    global $cfg_ftpDir;
    global $cfg_secureAccess;
    global $cfg_basehost;
    global $cfg_OSSUrl;
    global $cfg_QINIUdomain;
    global $cfg_uploadDir;

    if($type == "small" || $type == "middle"){
        if(!strstr($url, "atlas") && !strstr($url, "photo") && !strstr($url, "thumb")){
            return $url;
        }
    }
    $localpath = $cfg_secureAccess . $cfg_basehost . $cfg_uploadDir;
    $urls = explode($localpath, $url);
    $url_ = $urls[1];

    if($url_){
        if(strstr($url, "http") || strstr($url, "//") || strstr($url_, "editor")){
            return $url;
        }
      //普通FTP模式
      if ($cfg_ftpType == 0) {

        //启用远程FTP
        if ($cfg_ftpState == 1) {
          $site_fileUrl = $cfg_ftpUrl . $cfg_ftpDir;

        //本地模式
        } else {
          $site_fileUrl = $cfg_secureAccess . $cfg_basehost . $cfg_uploadDir;
        }

      //阿里云
      } elseif ($cfg_ftpType == 1) {
        $site_fileUrl = "https://" . $cfg_OSSUrl;

      //七牛云
      } elseif ($cfg_ftpType == 2) {
        $site_fileUrl = "http://" . $cfg_QINIUdomain;
        // $url_='/'.substr(str_replace('/','_',$url_),1);
      }

      if (empty($type)) return $site_fileUrl.$url_;

      if ($cfg_hideUrl == 1) {
        if($cfg_ftpType == 2){
          // $url_=substr(str_replace('/','_',$url_),1);
        }
        return $site_fileUrl.$url_ . "&type=" . $type;
      } else {
        $file = str_replace("large", $type, $url_);
        return $site_fileUrl.$file;
      }
  }else{
    if (empty($type)) return $url;

    if ($cfg_hideUrl == 1) {
      if($cfg_ftpType == 2){
        // $url=substr(str_replace('/','_',$url),1);
      }
      return $url . "&type=" . $type;
    } else {
      $file = str_replace("large", $type, $url);
      return $file;
    }
  }
}


/**
 * 判断是否为手机端
 * @return bool
 */
function isMobile(){
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (preg_match("/iphone|ios|android|mini|mobile|mobi|Nokia|Symbian|iPod|iPad|Windows\s+Phone|MQQBrowser|wp7|wp8|UCBrowser7|UCWEB|360\s+Aphone\s+Browser|AppleWebKit/", $useragent)) {
        return true;
    } else {
        return false;
    }
}


/**
 * 判断是否为微信端
 * @return bool
 */
function isWeixin(){
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($useragent, 'micromessenger') !== false) {
        return true;
    } else {
        return false;
    }
}


/**
 * 判断是否为支付宝端
 * @return bool
 */
function isAlipay(){
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($useragent, 'alipay') !== false) {
        return true;
    } else {
        return false;
    }
}


/**
 * 判断是否为APP端
 * @return bool
 */
function isApp(){
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($useragent, 'huoniao') !== false) {
        return true;
    } else {
        return false;
    }
}


/**
 * 判断是否为安卓APP端
 * @return bool
 */
function isAndroidApp(){
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($useragent, 'huoniao_android') !== false) {
        return true;
    } else {
        return false;
    }
}


/**
 * 判断是否为苹果APP端
 * @return bool
 */
function isIOSApp(){
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($useragent, 'huoniao_ios') !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取URL指定参数值
 * @param string $url 要处理的字符串（默认为当前页面地址）
 * @param string $key 要获取的key值
 * @return string
 */
function getUrlQuery($url, $key){
    $conf = explode("?", ($url ? $url : $_SERVER['REQUEST_URI']));
    $conf = $conf[1];
    $arr = $conf ? explode("&", $conf) : array();
    foreach ($arr as $k => $v) {
        $query = explode("=", $arr[$k]);
        if ($query[0] == $key) {
            return $query[1];
        }
    }
    return false;
}

/**
 * 根据出生日期计算年龄
 * @param string $birth 要计算的出生日期（格式：1970-1-1）
 * @return int
 */
function getBirthAge($birth){
    if ($birth) {
        list($by, $bm, $bd) = explode('-', $birth);
        $cm = date('n');
        $cd = date('j');
        $age = date('Y') - $by - 1;
        if ($cm > $bm || $cm == $bm && $cd > $bd) $age++;
        return $age;
    }
}

/**
 * 取得URL链接地址
 * @param array $params 参数集
 * @return string
 */
function getUrlPath($params){

    extract($params);
    global $dsql;
    global $cfg_urlRewrite;
    global $cfg_secureAccess;
    global $cfg_basehost;

    $configFilePath = HUONIAOINC."/config/$service.inc.php";
    if(file_exists($configFilePath)){
      include($configFilePath);
    }else{
      return;
    }

    $encodeParam = array();
    if (!empty($params['param'])) {
        $paramArr = explode('&', $params['param']);
        foreach ($paramArr as $key => $val){
            $par = explode('=', $val);
            array_push($encodeParam, $par[0] . '=' . (preg_match('/[\x{4e00}-\x{9fa5}]/u', $par[1]) > 0 ? urlencode(urldecode($par[1])) : $par[1]));
        }
        $params['param'] = join('&', $encodeParam);
    }


    //系统模块
    if ($service == "siteConfig") {
        $domain = $cfg_secureAccess . $cfg_basehost;

        //服务协议兼容标题生成链接
        if($template == 'protocol' && $title){
            unset($params['title']);
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_singellist` WHERE `type` = 'agree' AND `title` = '$title'");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $params['id'] = $ret[0]['id'];
            }
        }

    } elseif ($service != "member") {

        //模块域名
        $domain = getDomainFullUrl($service, $customSubDomain);

        //新闻频道自定义URL
        if ($service == "article" || $service == "image") {
            $ser = $service;

            $claFile = HUONIAOROOT . '/api/handlers/' . $ser . '.class.php';
            if (is_file($claFile)) {
                include_once $claFile;
            } else {
                return;
            }

            $articleService = new $ser();
            $articleConfig = $articleService->config();
            $listRule = $articleConfig['listRule'];
            $detailRule = $articleConfig['detailRule'];

            //验证不是跳转类型
            $flag1 = explode(",", $flag);
            if (!in_array("t", $flag1) || empty($redirecturl)) {

                if ($template == "list") {

                    $after = "";
                    if (!empty($params['param'])) {
                        $after = "?" . $params['param'];
                    }

                    if (!empty($typeid) && is_numeric($typeid)) {

                        //查询分类信息
                        $sql = $dsql->SetQuery("SELECT `pinyin`, `py` FROM `#@__" . $ser . "type` WHERE `id` = $typeid");
                        if($ser == "article"){
                            $ret = getCache($ser."type_py", $sql, 0, array("sign" => $typeid));
                        }else{
                            $ret = $dsql->dsqlOper($sql, "results");
                        }
                        if ($ret) {

                            $pinyin = $ret[0]['pinyin'];
                            $py = $ret[0]['py'];

                            //分类全拼
                            if ($listRule == 1) {
                                return $domain . "/" . $pinyin . "/" . $after;

                                //分类首字母
                            } elseif ($listRule == 2) {
                                return $domain . "/" . $py . "/" . $after;

                            }

                        }

                    }
                } elseif ($template == "detail") {

                    //查询分类信息
                    $folder = "";

                    // 优先传入typeid，不需要join查询
                    if (!empty($typeid) && is_numeric($typeid)) {
                        $sql = $dsql->SetQuery("SELECT `pinyin`, `py` FROM `#@__" . $ser . "type` WHERE `id` = $typeid");
                        if($ser == "article"){
                            $ret = getCache($ser."type_py", $sql, 0, array("sign" => $typeid));
                        }else{
                            $ret = $dsql->dsqlOper($sql, "results");

                        }
                    }else{
                        $sql = $dsql->SetQuery("SELECT t.`pinyin`, t.`py` FROM `#@__" . $ser . "type` t LEFT JOIN `#@__" . $ser . "list` l ON l.`typeid` = t.`id` WHERE l.`id` = $id");
                        $ret = $dsql->dsqlOper($sql, "results");
                    }
                    if ($ret) {
                        $pinyin = $ret[0]['pinyin'];
                        $py = $ret[0]['py'];

                        //分类全拼
                        if ($listRule == 1) {
                            $folder = "/" . $pinyin;

                            //分类首字母
                        } elseif ($listRule == 2) {
                            $folder = "/" . $py;
                        }
                    }

                    //不需要前缀
                    if ($detailRule == 1) {
                        return $domain . $folder . "/" . $id . ".html";
                    } else {
                        return $domain . $folder . "/detail-" . $id . ".html";
                    }

                }

            }


        }

        //团购频道域名配置
        if ($service == "tuan_tuan") {

            include_once HUONIAOROOT . '/api/handlers/tuan.class.php';

            //团购商品详细页链接，需要根据商品相关信息获取相应的URL
            if (!empty($id) && is_numeric($id)) {

                $sql = $dsql->SetQuery("SELECT d.`domain` FROM `#@__tuanlist` l LEFT JOIN `#@__tuan_store` s ON s.`id` = l.`sid` LEFT JOIN `#@__site_area` a ON a.`id` = s.`addrid` LEFT JOIN `#@__tuan_city` c ON c.`cid` = a.`parentid` LEFT JOIN `#@__domain` d ON d.`iid` = c.`id` WHERE d.`module` = 'tuan' AND d.`part` = 'city' AND l.`id` = $id");
                $ret = $dsql->dsqlOper($sql, "results");
                if ($ret) {

                    global $city;
                    $city = $ret[0]['domain'];
                    $tuanService = new tuan();
                    $domainInfo = $tuanService->getCity();
                    $tuanDomain = $domainInfo['url'];
                    $domain = $tuanDomain;

                } else {

                    //其他例外情况，比如获取商家链接
                    global $city;
                    $tuanService = new tuan();
                    $domainInfo = $tuanService->getCity();
                    $tuanDomain = $domainInfo['url'];
                    $domain = $tuanDomain;

                }

            } else {

                //重置自定义配置
                $subDomain = $customSubDomain;
                global $customSubDomain;
                $customSubDomain = $subDomain;
                $tuanService = new tuan();
                $domainInfo = $tuanService->getCity();
                $tuanDomain = $domainInfo['url'];
                $domain = $tuanDomain;

            }


            // 此处少验证一种情况
            // 当cookie中没有城市信息时，domain输出为空，这里需要调整为：
            // 如果template为detail时，需要根据传过来的商品ID所属商家的所在城市输出相应的domain


        }

        //会员链接
    } else {

        global $handler;
        $handler = true;
        $configHandels = new handlers("member", "config");
        $moduleConfig = $configHandels->getHandle();
        if (is_array($moduleConfig) && $moduleConfig['state'] == 100) {
            $moduleConfig = $moduleConfig['info'];
            global $cfg_userSubDomain;
            global $cfg_busiSubDomain;

            $domain = $type == "user" ? $moduleConfig['userDomain'] : $moduleConfig['busiDomain'];
            // if($type == "user"){
            // 	$sub = "";
            // 	if($cfg_userSubDomain == 1){
            // 		$sub = "/u";
            // 	}
            // 	$domain = $moduleConfig['userDomain'].$sub;
            // }else{
            // 	$sub = "";
            // 	if($cfg_busiSubDomain == 1){
            // 		$sub = "/b";
            // 	}
            // 	$domain = $moduleConfig['busiDomain'].$sub;
            // }

            unset($params['type']);

        } else {
            $domain = $cfg_secureAccess . $cfg_basehost . "/" . $service;
        }
    }

    //如果是列表页面，判断页码值是否存在，如果不存在则初始化
    if ($template == "list" && empty($page)) {
        // $params['page'] = 1;
    }

    $flag = explode(",", $flag);
    //跳转类型
    if (in_array("t", $flag) && $redirecturl) {
        return $redirecturl . '" target="_blank';

        //站内类型
    } else {
        $param = array();
        $paramRewrite = array();
        foreach ($params as $key => $value) {
            if ($key != "flag" && $key != "redirecturl") {
                if ($key == "param") {
                    $param[] = $value;
                } else {
                    $param[] = $key . "=" . $value;
                }
                if (($cfg_urlRewrite || $service == "member") && $key != "service" && $key != "param") {
                    $paramRewrite[] = $value;
                }
            }
        }

        $after = "";
        if (!empty($params['param'])) {
            $after = "?" . $params['param'];
        }

        //伪静态
        if ($cfg_urlRewrite || $service == "member") {
            if (!empty($paramRewrite)) {
                if ($service == "website" && (strstr($template, "preview") || strstr($template, "site"))) {

                    //站点独立域名验证
                    if (strstr($template, "site")) {
                        $websiteid = (int)str_replace("site", "", $template);

                        if (is_numeric($websiteid)) {
                            $sql = $dsql->SetQuery("SELECT `domaintype` FROM `#@__website` WHERE `id` = $websiteid");
                            $ret = $dsql->dsqlOper($sql, "results");
                            if ($ret) {
                                $getDomain = getDomain("website", "website", $websiteid);
                                if ($getDomain && $ret[0]['domaintype'] && $getDomain['state'] == 1 && !isMobile()) {
                                    return $cfg_secureAccess . $getDomain['domain'] . ($alias ? "/" . $alias . ".html" : "");
                                }
                            }
                        }


                    }

                    return $domain . "/" . $template . ($alias ? "/" . $alias . ".html" : "") . $after;
                } else {
                    return $domain . "/" . ($service == 'member' && $template == 'chat' ? 'im/' : '') . join("-", $paramRewrite) . ".html" . $after;
                }
            } else {
                return $domain;
            }

            //动态
        } else {
            if ($service == "website" && (strstr($template, "preview") || strstr($template, "site"))) {
                if (strstr($template, "preview")) {
                    return $cfg_secureAccess . $cfg_basehost . '/website.php?type=template&id=' . str_replace("preview", "", $template) . ($alias ? "&alias=" . $alias : "");
                } elseif (strstr($template, "site")) {
                    return $cfg_secureAccess . $cfg_basehost . '/website.php?id=' . str_replace("site", "", $template) . ($alias ? "&alias=" . $alias : "");
                }
            } else {
                return $cfg_secureAccess . $cfg_basehost . '/index.php?' . join("&", $param);
            }
        }
    }

}


/**
 * 取得URL链接地址
 * @param array $params 参数集
 * @param $url  域名前缀 格式：http://domain.com/list.html
 * @param $data 现有参数 格式：a=1&b=2&c=3
 * @param $item 组合参数 格式：item=1:a;2:aa;3:aaa 一组有两个值，用冒号隔开，多个组之间用分号隔开
 * @param $specification 组合参数 格式：specification=1;2;3  多个值之间用分号隔开
 * @param 新参数 a=2 (数组格式)   返回结果会把$data中的a=1更新为a=2
 * @return string
 */
function getUrlParam($params){
    extract($params);
    $paramData = array();

    $ljf = strpos($url, ".html") !== false ? "?" : "&";

    //现有参数
    if ($data) {
        parse_str($data, $nData);
        foreach ($nData as $k => $v) {
            if ($v !== "") {
                $paramData[$k] = $v;
            }
        }
    }


    //新参数&&覆盖旧参数
    // print_r($params);
    foreach ($params as $key => $value) {
        if ($key != "url" && $key != "data" && $key != "item" && $key != "specification") {
            if ($value !== "") {  //flag为属性值，有为0的情况，这里要排除限制
                $paramData[$key] = $value;
            } else {
                unset($paramData[$key]);
            }
        }
    }


    //特殊情况 item
    //更新：当现有值为：1:a;2:aa;3:aaa时，新传过来的值为：1:b，此时要更新1:a的值为：1:b
    //新增：当现有值为：1:a;2:aa;3:aaa时，新传过来的值为：4:aaaa，此时要更新现有值为：1:a;2:aa;3:aaa;4:aaaa
    //删除：当现有值为：1:a;2:aa;3:aaa时，新传过来的值为：2:0，此时要更新现有值为：1:a;3:aaa;4:aaaa
    if ($item !== "") {
        $nItem = explode(":", $item);
        $pItem = $paramData['item'];
        $pItem = !empty($nItem[1]) ? (($pItem ? $pItem . ";" : "") . $item) : $pItem;
        $pItemArr = explode(";", $pItem);
        $pItemArr = array_flip(array_flip($pItemArr));   //去除相同元素
        sort($pItemArr);

        //更新相同级别的选项值
        $nItemArr = array();
        foreach ($pItemArr as $key => $value) {
            $vArr = explode(":", $value);
            if ($vArr[0] == $nItem[0]) {
                $nItemArr[$vArr[0]] = $nItem[1];
            } else {
                $nItemArr[$vArr[0]] = $vArr[1];
            }
        }

        //组合新的选项值
        $newArr = array();
        foreach ($nItemArr as $key => $value) {
            if (!empty($value)) {
                array_push($newArr, $key . ":" . $value);
            }
        }
        $paramData['item'] = join(";", $newArr);
    } else {
        $paramData['item'] = "";
    }


    //特殊情况 specification
    //情况参考上面的item
    if ($specification !== "") {
        $nSpe = explode(":", $specification);
        $pSpe = $paramData['specification'];
        $pSpe = !empty($nSpe[1]) ? (($pSpe ? $pSpe . ";" : "") . $specification) : $pSpe;
        $pSpeArr = explode(";", $pSpe);
        $pSpeArr = array_flip(array_flip($pSpeArr));   //去除相同元素
        sort($pSpeArr);

        //更新相同级别的选项值
        $nSpeArr = array();
        foreach ($pSpeArr as $key => $value) {
            $vArr = explode(":", $value);
            if ($vArr[0] == $nSpe[0]) {
                $nSpeArr[$vArr[0]] = $nSpe[1];
            } else {
                $nSpeArr[$vArr[0]] = $vArr[1];
            }
        }

        //组合新的选项值
        $newArr = array();
        foreach ($nSpeArr as $key => $value) {
            if (!empty($value)) {
                array_push($newArr, $key . ":" . $value);
            }
        }
        $paramData['specification'] = join(";", $newArr);
    } else {
        $paramData['specification'] = "";
    }

    $param = array();
    if ($paramData) {
        foreach ($paramData as $key => $value) {
            if ($value !== "") {
                array_push($param, $key . "=" . $value);
            }
        }
    }

    //sort($param);
    $param = $ljf . join("&", $param);
    return $url . $param;
}


/**
 * 打印分页html
 * @param array $params 参数集
 * @return string
 */
function getPageList($params){
    extract($params);
    unset($params['pageInfo']);

    //引入分页类
    include_once(HUONIAOINC . '/class/pagelist.class.php');

    //获取pageInfo
    if(!$pageInfo){
        global $pageInfo;
    }
    global $typeid;
    global $cfg_secureAccess;
    global $cfg_basehost;

    $page = (int)$pageInfo['page'];
    $pageSize = (int)$pageInfo['pageSize'];
    $totalPage = (int)$pageInfo['totalPage'];
    $totalCount = (int)$pageInfo['totalCount'];

    $param = array();
    foreach ($params as $key => $value) {
        if ($key != "pageType") {
            $param[$key] = $value;
        }
    }

    if (!array_key_exists("typeid", $params)) {
        //$param['typeid'] = $typeid;
    }

    if ($pageType != "dynamic") {
        $param['page'] = "#page#";
    }

    $url = getUrlPath($param);
    if ($params['service'] == "siteConfig") {
        if ($params['template'] == "user") {
            if ($params['action'] == "follow" || $params['action'] == "fans" || $params['action'] == "visitor") {
                $url = $cfg_secureAccess . $cfg_basehost . "/user/" . $params['id'] . "/" . $params['action'] . ".html?page=#page#";
            }
        }
    }
    $pageConfig = array(
        'total_rows' => $totalCount,
        'method' => 'html',
        'parameter' => $url,
        'now_page' => $page,
        'list_rows' => $pageSize,
    );
    $page = new pagelist($pageConfig);
    echo $page->show();

}

/* 内容分页 */
function bodyPageList($params){
    extract($params);
    global $all;
    global $langData;
    $pagesss = '_huoniao_page_break_tag_';  //设定分页标签
    $a = strpos($body, $pagesss);
    if ($a && !$all) {
        $con = explode($pagesss, $body);
        $cons = count($con);
        @$p = ceil($page);
        if (!$p || $p < 0) $p = 1;
        // $url = $_SERVER["REQUEST_URI"];
        // $parse_url = parse_url($url);
        // $url_query = $parse_url["query"];
        // if($url_query){
        //  $url_query = ereg_replace("(^|&)p=$p", "", $url_query);
        //  $url = str_replace($parse_url["query"], $url_query, $url);
        // 	if($url_query) $url .= "&p"; else $url .= "p";
        // }else {
        // 	$url .= "?p";
        // }
        if ($cons <= 1) return false;//只有一页时不显示分页
        $pagenav = '<div class="page fn-clear"><ul>';
        //上一页
        if ($p == 1) {
            $pagenav .= '<li><span class="disabled">' . $langData['siteConfig'][6][33] . '</span></li>';
        } else {
            $pagenav .= "<li><a href='?p=" . ($p - 1) . "'>" . $langData['siteConfig'][6][33] . "</a></li>";
        }
        for ($i = 1; $i <= $cons; $i++) {
            if ($i == $p) {
                $pagenav .= '<li><span>' . $i . '</span></li>';
            } else {
                $pagenav .= "<li><a href='?p=$i'>$i</a></li>";
            }
        }
        //下一页
        if ($p == $cons) {
            $pagenav .= '<li><span class="disabled">' . $langData['siteConfig'][6][34] . '</span></li>';
        } else {
            $pagenav .= "<li><a href='?p=" . ($p + 1) . "'>" . $langData['siteConfig'][6][34] . "</a></li>";
        }
        //显示全文
        $pagenav .= "<li><a href='?all=1'>" . $langData['siteConfig'][21][8] . "</a></li>";
        $pagenav .= "</ul></div>";
        return $pagenav;
    }
}

/* 打印导航 */
function getChannel($params){
    extract($params);
    global $typeid;
    $pid = 0;
    if ($tab) {
        $typeName = getParentArr($tab, $typeid);
        $typeName = !empty($typeName) ? array_reverse(parent_foreach($typeName, "id")) : 1;
        $pid = $typeName[0];
    }
    $params['son'] = "1";
    $handler = true;
    $channel = "";
    $moduleHandels = new handlers($service, "type");
    $moduleReturn = $moduleHandels->getHandle($params);
    if ($moduleReturn['state'] == 100 && is_array($moduleReturn['info'])) {
        $channel = printChannel($moduleReturn['info'], $pid);
    }
    return $channel;
}

function printChannel($data, $pid = 0){
    $return = "";
    if ($data) {
        foreach ($data as $key => $value) {
            $lower = $value['lower'];
            $cla = array();
            if ($lower) {
                $cla[] = "sub";
            }
            if ($pid == $value['id']) {
                $cla[] = 'on';
            }
            $clas = $cla ? ' class="' . join(" ", $cla) . '"' : '';
            $return .= '<li' . $clas . '>';
            $return .= '<a href="' . $value['url'] . '">' . $value['typename'] . '</a>';
            if ($lower) {
                $return .= '<ul>';
                $return .= printChannel($lower);
                $return .= '</ul>';
            }
            $return .= '</li>';
        }
    }
    return $return;
}

/* 获取附件后缀名 */
function getAttachType($id){
    if (!empty($id)) {
        global $dsql;
        $RenrenCrypt = new RenrenCrypt();
        $id = $RenrenCrypt->php_decrypt(base64_decode($id));

        if (is_numeric($id)) {
            $attachment = $dsql->SetQuery("SELECT `filename` FROM `#@__attachment` WHERE `id` = " . $id);
            $results = $dsql->dsqlOper($attachment, "results");
            if ($results) {
                $filename = $results[0]['filename'];
                $filetype = strtolower(strrchr($filename, '.'));
                return $filetype;
            }
        }
    }
}

/* 根据文件类型输出不同的内容 */
function getAttachHtml($id = "", $href = "", $title = "", $width = 0, $height = 0, $exp = false, $insert = "", $advMark = ""){
    $html = "";
    $width = !empty($width) ? $width : "100%";
    $height = !empty($height) ? $height : "";
    $src = getFilePath($id); //附件路径
    global $langData;

    //验证附件后缀
    global $cfg_hideUrl;
    if ($cfg_hideUrl == 1) {
        $filetype = getAttachType($id);
    } else {
        $filetype = strtolower(strrchr($src, '.'));
    }

    if ($filetype == ".swf") {
        $html = '<div class="siteAdvObj"><embed width="' . $width . '" height="' . $height . '" src="' . $src . '" type="application/x-shockwave-flash" quality="high" wmode="opaque">' . $advMark . '</div>';
    } else {
        if ($href == "") {
            $html = '<div class="siteAdvObj"><a href="javascript:;" style="cursor:default;"><img src="' . $src . '" width="' . $width . '" height="' . $height . '" alt="' . $title . '" />' . (!empty($insert) ? $insert : "") . '</a>' . $advMark . '</div>';
            if ($exp) {
                $html .= '<p>' . $title . '</p>';
            }
        } else {
            $html = '<div class="siteAdvObj"><a href="' . $href . '" target="_blank"><img src="' . $src . '" width="' . $width . '" height="' . $height . '" alt="' . $title . '" />' . (!empty($insert) ? $insert : "") . '</a>' . $advMark . '</div>';
            if ($exp) {
                $html .= '<p>' . $title . '<a href="' . $href . '" target="_blank">' . $langData['siteConfig'][21][9] . '</a></p>';
            }
        }
    }
    return $html;
}

/* 静态页面获取当前时间 */
function getMyTime($params, $smarty){
    if (empty($params["format"])) {
        $format = "%b %e, %Y";
    } else {
        $format = $params["format"];
    }

    $rtime = strftime($format, time());

    if ($params["type"] == "nongli") {
        require_once HUONIAOINC . '/class/lunar.class.php';
        $lunar = new lunar();
        $rtime = $lunar->S2L($rtime);
        $rtime = explode("年", $rtime);
        $rtime = $rtime[1];
    }
    return $rtime;
}

/* 静态页面获取当前星期几 */
function getMyWeek($params, $smarty){
    global $langData;
    $prefix = $params['prefix'];  //前缀，升级多语言版本后，此前缀暂时不用了
    $week = !empty($params['date']) ? date("w", strtotime($params['date'])) : date("w");
    $weekarray = array($langData['siteConfig'][14][10], $langData['siteConfig'][14][4], $langData['siteConfig'][14][5], $langData['siteConfig'][14][6], $langData['siteConfig'][14][7], $langData['siteConfig'][14][8], $langData['siteConfig'][14][9]);
    return $weekarray[$week];
}

/* 天气数据 */
function getWeather($params, $smarty){
    extract($params);
    global $cfg_secureAccess;
    global $cfg_basehost;

    $day = $day < 1 ? 1 : $day;
    $day = $day > 6 ? 6 : $day;

    $imgUrl = $cfg_secureAccess . $cfg_basehost . "/static/images/ui/weather/" . $skin . "/";

    //如果没有传城市名称
    if (empty($city)) {

        //先判断系统默认城市
        global $siteCityInfo;
        if (!empty($siteCityInfo)) {
            $city = $siteCityInfo['name'];

            //如果系统默认城市为空，则自动获取当前城市
        } else {
            $cityData = getIpAddr(GetIP());
            if ($cityData == "本地局域网") {
                $city = "北京";
            } else {
                $cityData = explode("省", $cityData);
                $cityData = explode("市", $cityData[1]);
                $city = $cityData[0];
            }
        }
    }

    //根据城市名获取数据库中的编码
    global $dsql;
    $sql = $dsql->SetQuery("SELECT * FROM `#@__site_area` WHERE `typename` = '$city' AND `weather_code` <> ''");
    $results = $dsql->dsqlOper($sql, "results");
    if ($results) {
        $code = $results[count($results) - 1]['weather_code'];
    } else {
        $code = '101010100';
    }

    $weatherArr = array();

    // 360
    $url = "http://cdn.weather.hao.360.cn/sed_api_weather_info.php?app=360chrome&code=$code";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    $con = curl_exec($curl);
    curl_close($curl);

    if ($con) {
        $con = str_replace("callback(", "", $con);
        $con = str_replace(");", "", $con);
        $weatherinfo = json_decode($con, true);
        if (is_array($weatherinfo)) {
            $weatherinfo = $weatherinfo['weather'];
            for ($i = 0; $i < $day; $i++) {
                $f = $i + 1;

                $info = $weatherinfo[$i]['info'];

                $bday = $info['day'];
                $night = $info['night'];

                //白天
                $dimg = $bday[0];
                $dweather = $bday[1];
                $dtemp = $bday[2];
                $dwind = $bday[3] == "无持续风向" ? $bday[4] : $bday[3];

                //晚上
                $nimg = $night[0];
                $nweather = $night[1];
                $ntemp = $night[2];
                $nwind = $night[3] == "无持续风向" ? $night[4] : $night[3];

                $weather = $dweather . ($nweather == $dweather ? "" : "转" . $nweather);
                $temp = ($ntemp == $dtemp ? "" : $ntemp . "-") . $dtemp . "°C";
                $wind = $dwind . ($nwind == $dwind ? "" : "转" . $nwind);

                $img = ($dimg !== "" ? '<img src="' . $imgUrl . $dimg . '.png" class="wd" />' : "") . ($nimg !== "" ? '<img src="' . $imgUrl . $nimg . '.png" class="wn" />' : "");
                if ($dimg == $nimg) {
                    $img = $dimg !== "" ? '<img src="' . $imgUrl . $dimg . '.png" class="w0" />' : "";
                }

                $param = array(
                    "date" => $weatherinfo['date'],
                    "prefix" => "周"
                );
                $date = getMyWeek($param, $smarty);

                if ($f == 1) {
                    $date = "今天";
                } else if ($f == 2) {
                    $date = "明天";
                } else if ($f == 3) {
                    $date = "后天";
                }

                $weatherArr[$i] = '<li class="weather' . $f . '">
		  		<span class="date">' . $date . '</span>
		  		<span class="pic" title="' . $weather . '">' . $img . '</span>
		  		<span class="weather">' . $weather . '</span>
		  		<span class="temp">' . $temp . '</span>
		  		<span class="wind">' . $wind . '</span>
		  	</li>';
            }

        }
    }


    // 小米
    // $url = "http://weatherapi.market.xiaomi.com/wtr-v2/weather?cityId=$code";

    // $curl = curl_init();
    //  curl_setopt($curl, CURLOPT_URL, $url);
    //  curl_setopt($curl, CURLOPT_HEADER, 0);
    //  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //  curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    //  $con = curl_exec($curl);
    //  curl_close($curl);

    //  if($con){
    //   $weatherinfo = json_decode($con, true);
    //   if(is_array($weatherinfo)){
    // 	  $weatherinfo = $weatherinfo['forecast'];
    // 	  for($i = 0; $i < $day; $i++) {
    // 	  	$f = $i + 1;
    // 	  	$d = $i*2+1;
    // 	  	$n = $i*2+2;

    // 	  	$weather = $weatherinfo['weather'.$f];
    // 	  	$temp    = $weatherinfo['temp'.$f];
    // 	  	$wind    = $weatherinfo['wind'.$f];
    // 	  	$dimg    = getWeatherIcon($weatherinfo['img_title'.$d]);
    // 	  	$nimg    = getWeatherIcon($weatherinfo['img_title'.$n]);

    // 	  	$img = ($dimg !== "" ? '<img src="'.$imgUrl.$dimg.'.png" class="wd" />' : "").($nimg !== "" ? '<img src="'.$imgUrl.$nimg.'.png" class="wn" />' : "");
    // 	  	if($dimg == $nimg){
    // 	  		$img = $dimg !== "" ? '<img src="'.$imgUrl.$dimg.'.png" class="w0" />' : "";
    // 	  	}

    // 	  	$param = array(
    // 	  		"date"   => date("Y-m-d", strtotime("+".$i." day")),
    // 	  		"prefix" => "周"
    // 	  	);
    // 	  	$date = getMyWeek($param, $smarty);

    // 	  	if($f == 1){
    // 	  		$date = "今天";
    // 	  	}else if($f == 2){
    // 	  		$date = "明天";
    // 	  	}else if($f == 3){
    // 	  		$date = "后天";
    // 	  	}

    // 	  	$weatherArr[$i] = '<li class="weather'.$f.'">
    // 	  		<span class="date">'.$date.'</span>
    // 	  		<span class="pic" title="'.$weather.'">'.$img.'</span>
    // 	  		<span class="weather">'.$weather.'</span>
    // 	  		<span class="temp">'.$temp.'</span>
    // 	  		<span class="wind">'.$wind.'</span>
    // 	  	</li>';
    // 	  }

    // 	}
    // }

    return join(" ", $weatherArr);
}

//根据天气名称返回相应的图标名
function getWeatherIcon($tit){
    $code = 0;
    switch ($tit) {
        case '晴':
            $code = 0;
            break;
        case '多云':
            $code = 1;
            break;
        case '阴':
            $code = 2;
            break;
        case '阵雨':
            $code = 3;
            break;
        case '雷阵雨':
            $code = 4;
            break;
        case '雷阵雨伴有冰雹':
            $code = 5;
            break;
        case '雨夹雪':
            $code = 6;
            break;
        case '小雨':
            $code = 7;
            break;
        case '中雨':
            $code = 8;
            break;
        case '大雨':
            $code = 9;
            break;
        case '暴雨':
            $code = 10;
            break;
        case '大暴雪':
            $code = 11;
            break;
        case '特大暴雪':
            $code = 12;
            break;
        case '阵雪':
            $code = 13;
            break;
        case '小雪':
            $code = 14;
            break;
        case '中雪':
            $code = 15;
            break;
        case '大雪':
            $code = 16;
            break;
        case '暴雪':
            $code = 17;
            break;
        case '雾':
            $code = 18;
            break;
        case '冻雨':
            $code = 19;
            break;
        case '沙尘暴':
            $code = 20;
            break;
        case '小雨-中雨':
            $code = 21;
            break;
        case '中雨-大雨':
            $code = 22;
            break;
        case '大雨-暴雨':
            $code = 23;
            break;
        case '暴雨-大暴雨':
            $code = 24;
            break;
        case '大暴雨-特大暴雨':
            $code = 25;
            break;
        case '小雪-中雪':
            $code = 26;
            break;
        case '中雪-大雪':
            $code = 27;
            break;
        case '大雪-暴雪':
            $code = 28;
            break;
        case '浮尘':
            $code = 29;
            break;
        case '扬沙':
            $code = 30;
            break;
        case '强沙尘暴':
            $code = 31;
            break;
        case '飑':
            $code = 32;
            break;
        case '龙卷风':
            $code = 33;
            break;
        case '弱高吹雪':
            $code = 34;
            break;
        case '轻雾':
            $code = 35;
            break;
        default:
            $code = 0;
            break;
    }
    return $code;
}


/**
 * 数字大小写转换
 *
 */
function numberDaxie($params){
    extract($params);
    $number = substr($number, 0, 2);
    $arr = array("零", "一", "二", "三", "四", "五", "六", "七", "八", "九");
    if (strlen($number) == 1) {
        $result = $arr[$number];
    } else {
        if ($number == 10) {
            $result = "十";
        } else {
            if ($number < 20) {
                $result = "十";
            } else {
                $result = $arr[substr($number, 0, 1)] . "十";
            }
            if (substr($number, 1, 1) != "0") {
                $result .= $arr[substr($number, 1, 1)];
            }
        }
    }
    return $result;
}


/**
 * 获取等比缩放后的值
 * @param int $pic_width 原图宽
 * @param int $pic_height 原图高
 * @param int $maxwidth 最大宽
 * @param int $maxheight 最大高
 *
 */
function resizeImage($pic_width, $pic_height, $maxwidth, $maxheight){
    if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
        if ($maxwidth && $pic_width > $maxwidth) {
            $widthratio = $maxwidth / $pic_width;
            $resizewidth_tag = true;
        }

        if ($maxheight && $pic_height > $maxheight) {
            $heightratio = $maxheight / $pic_height;
            $resizeheight_tag = true;
        }

        if ($resizewidth_tag && $resizeheight_tag) {
            if ($widthratio < $heightratio) {
                $ratio = $widthratio;
            } else {
                $ratio = $heightratio;
            }
        }

        if ($resizewidth_tag && !$resizeheight_tag) {
            $ratio = $widthratio;
        }
        if ($resizeheight_tag && !$resizewidth_tag) {
            $ratio = $heightratio;
        }

        $newSize = array(
            "width" => intval($pic_width * $ratio),
            "height" => intval($pic_height * $ratio)
        );

        if ($return) {
            return $newSize[$return];
        } else {
            return $newSize;
        }

    } else {
        if ($return == "width") {
            return $pic_width;
        } elseif ($return == "height") {
            return $pic_height;
        } else {
            return array("width" => $pic_width, "height" => $pic_height);
        }
    }
}


function resizeImageSize($params){
    extract($params);
    $arr = resizeImage($pic_width, $pic_height, $maxwidth, $maxheight);
    if ($return == "width") {
        return $arr['width'];
    } elseif ($return == "height") {
        return $arr['height'];
    } else {
        return $arr;
    }
}


/**
 * 根据图片路径、指定宽度，获取等比缩放后的高度
 * @param string $src 图片路径
 * @param int $width 最大宽度
 */
function getImgHeightByGeometric($params){
    extract($params);
    if (!empty($src) && !empty($width)) {
        $img = getimagesize($src);
        $imgSize = resizeImage($img[0], $img[1], $width, $img[1]);
        if ($imgSize) {
            return $imgSize['height'];
        }
    }
}


/**
 * 对内容进行敏感词过虑
 * @param string $body 需要处理的内容
 * @return string
 */
function filterSensitiveWords($body, $removeXSS = true){
    if (empty($body)) return "";
    global $cfg_replacestr;
    if (!empty($cfg_replacestr)) {

        //修复如果后台一个字段串为|时，数据最后一个值为空，导致替换的内容全部为空的问题；  by: 20180402  guozi
        $lastStr = substr($cfg_replacestr, -1);
        if($lastStr == '|'){
            $cfg_replacestr = substr($cfg_replacestr, 0, strlen($cfg_replacestr)-1);
        }

        $replacestr = explode("|", $cfg_replacestr);
        $badword = array_combine($replacestr, array_fill(0, count($replacestr), '***'));
        return $removeXSS ? RemoveXSS(strtr($body, $badword)) : strtr($body, $badword);
    } else {
        return $removeXSS ? RemoveXSS($body) : $body;
    }

}


/**
 * 判断点是否在多边形区域内
 * @param array $polygon 多边形坐标集
 * @param array $lnglat 指定坐标点
 * @param return $boolean
 */
function isPointInPolygon($polygon, $lnglat){
    $c = 0;
    $i = $j = 0;
    for ($j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
        if (((($polygon[$i][1] <= $lnglat[1]) && ($lnglat[1] < $polygon[$j][1])) ||
                (($polygon[$j][1] <= $lnglat[1]) && ($lnglat[1] < $polygon[$i][1])))
            && ($lnglat[0] < ($polygon[$j][0] - $polygon[$i][0]) * ($lnglat[1] - $polygon[$i][1]) / ($polygon[$j][1] - $polygon[$i][1]) + $polygon[$i][0])
        ) {
            $c = 1;
        }
    }
    return $c;
}

//获取会员详情
function getMemberDetail($id){
    $detail = array();
    global $handler;
    $handler = true;
    $memberHandels = new handlers("member", "detail");
    $memberConfig = $memberHandels->getHandle($id);
    if (is_array($memberConfig) && $memberConfig['state'] == 100) {
        $memberConfig = $memberConfig['info'];
        $detail = $memberConfig;
    }
    return $detail;
}


//验证信息是否已经收藏
function checkIsCollect($param){
    global $handler;
    $handler = true;
    $Handels = new handlers("member", "collect");
    $return = $Handels->getHandle($param);
    if (is_array($return) && $return['state'] == 100) {
        $returns = $return['info'];
        return $returns;
    }
}

//验证信息是否已经点赞
function checkIsZan($param){
    global $handler;
    $handler = true;
    $Handels = new handlers("member", "getZan");
    $return = $Handels->getHandle($param);
    if (is_array($return) && $return['state'] == 100) {
        $returns = $return['info'];
        return $returns;
    }
}


/**
 * 后台消息通知
 * @param $module 模块
 * @param $part   栏目
 */
function updateAdminNotice($module, $part){

    global $dsql;
    $sql = $dsql->SetQuery("INSERT INTO `#@__site_admin_notice` (`module`, `part`) VALUES ('$module', '$part')");
    $dsql->dsqlOper($sql, "update");

}


/**
 * 前台会员消息通知
 * @param $module 模块
 * @param $part   栏目
 */
function updateMemberNotice($uid, $notify, $param = array(), $config = array(), $customPhone = '', $im = array()){
    global $dsql;
    if (!$uid) return;

    // 直接传入手机号或邮箱，包含uid用来发送微信消息。
    // 用处：发送消息给简历上填写的联系方式
    if(is_array($uid)){
        extract($uid);
    }
    if (!$uid) return;

    //查询会员信息
    $sql = $dsql->SetQuery("SELECT `phone`, `phoneCheck`, `email`, `emailCheck`, `wechat_openid` FROM `#@__member` WHERE `id` = $uid AND `state` = 1");
    $ret = $dsql->dsqlOper($sql, "results");
    if (!$ret) return;

    $phone = $phone ? $phone : $ret[0]['phone'];
    $phoneCheck = $phone ? 1 : $ret[0]['phoneCheck'];
    $email = $email ? $email : $ret[0]['email'];
    $emailCheck = $email ? 1 : $ret[0]['emailCheck'];
    $wechat_openid = $ret[0]['wechat_openid'];

    //信息URL
    $url = "";
    if ($param) {
        $url = is_array($param) ? getUrlPath($param) : $param;
        $config['url'] = $url;
    }

    //邮件通知
    if ($email && $emailCheck) {
        $cArr = getInfoTempContent("mail", $notify, $config);
        $title = $cArr['title'];
        $content = $cArr['content'];
        if ($title || $content) {
            sendmail($email, $title, $content);
        }
    }

    //短信通知
    if (($phone && $phoneCheck) || $customPhone) {
        sendsms($customPhone ? $customPhone : $phone, 1, "", "", false, false, $notify, $config);
    }

    //微信公众号
    if ($wechat_openid) {
        $cArr = getInfoTempContent("wechat", $notify, $config);
        $title = $cArr['title'];
        $content = $cArr['content'];
        if ($title || $content) {
            sendwechat($wechat_openid, $title, $content, $url);
        }
    }

    //网页即时消息
    $cArr = getInfoTempContent("site", $notify, $config);
    $title = $cArr['title'];
    $content = $cArr['content'];
    if ($title != "" || $content != "") {
        $time = GetMkTime(time());
        $urlParam = serialize($param);
        $log = $dsql->SetQuery("INSERT INTO `#@__member_letter` (`admin`, `type`, `title`, `body`, `urlParam`, `success`, `date`) VALUE ('0', '0', '$title', '$content', '$urlParam', 1, '$time')");
        $lid = $dsql->dsqlOper($log, "lastid");
        if (!is_numeric($lid)) return;

        $sql = $dsql->SetQuery("INSERT INTO `#@__member_letter_log` (`lid`, `uid`, `state`, `date`) VALUE ('$lid', '$uid', 0, 0)");
        $ret = $dsql->dsqlOper($sql, "update");
    }

    //APP推送
    $cArr = getInfoTempContent("app", $notify, $config);
    $title = $cArr['title'];
    $content = $cArr['content'];
    if ($title || $content) {
        sendapppush($uid, $title, $content, $url, "default", false, $im);
    }

}


/**
 * 发送微信模板消息
 * @param $conn    会员绑定的微信公众平台唯一ID
 * @param $tempid  微信消息模板ID
 * @param $config  配置数据
 * @param $url     点击后跳转的位置
 */
function sendwechat($conn, $tempid, $config, $url){

    $msgData = '{"touser":"' . $conn . '", "template_id":"' . $tempid . '", "url":"' . $url . '", "data": ' . $config . '}';

    //引入配置文件
    $wechatConfig = HUONIAOINC . "/config/wechatConfig.inc.php";
    if (!file_exists($wechatConfig)) return '{"state": 200, "info": "请先设置微信开发者信息！"}';
    require($wechatConfig);

    include_once(HUONIAOROOT . "/include/class/WechatJSSDK.class.php");
    $jssdk = new WechatJSSDK($cfg_wechatAppid, $cfg_wechatAppsecret);
    $token = $jssdk->getAccessToken();


    // $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$cfg_wechatAppid&secret=$cfg_wechatAppsecret";
    // $ch = curl_init($url);
    // curl_setopt($ch, CURLOPT_HEADER, 0);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($ch, CURLOPT_POST, 0);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    // $output = curl_exec($ch);
    // curl_close($ch);
    // if(empty($output)){
    // 	return '{"state": 200, "info": "Token获取失败，请检查微信开发者帐号配置信息！"}';
    // }
    // $result = json_decode($output, true);
    // if(isset($result['errcode'])) {
    // 	return '{"state": 200, "info": "'.$result['errcode']."：".$result['errmsg'].'"}';
    // }
    //
    // $token = $result['access_token'];

    //发送模板消息
    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$token";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msgData);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $output = curl_exec($ch);
    curl_close($ch);
    if (empty($output)) {
        return '{"state": 200, "info": "请求失败，请稍候重试！"}';
    }
    $result = json_decode($output, true);
    if (isset($result['errcode'])) {
        if(HUONIAOBUG){
          require_once HUONIAOROOT."/api/payment/log.php";
          $logHandler= new CLogFileHandler(HUONIAOINC.'/noticeErrorMessage.log');
          $log = Log::Init($logHandler, 15);
          Log::DEBUG("微信模板消息发送错误日志");
          Log::DEBUG("发送数据：".$msgData);
          Log::DEBUG("微信返回：".json_encode($result, JSON_UNESCAPED_UNICODE) . "\r\n\r\n");
        }
        return '{"state": 200, "info": "' . getWechatMsgErrCode($result['errcode']) . '"}';
    }

    return '{"state": 100, "info": "发送成功！"}';
}


//根据返回码取中文说明
function getWechatMsgErrCode($code){
    $info = "未知错误！";
    switch ($code) {
        case -1:
            $info = "系统繁忙";
            break;
        case 0:
            $info = "请求成功";
            break;
        case 40001:
            $info = "验证失败";
            break;
        case 40002:
            $info = "不合法的凭证类型";
            break;
        case 40003:
            $info = "不合法的OpenID";
            break;
        case 40004:
            $info = "不合法的媒体文件类型";
            break;
        case 40005:
            $info = "不合法的文件类型";
            break;
        case 40006:
            $info = "不合法的文件大小";
            break;
        case 40007:
            $info = "不合法的媒体文件id";
            break;
        case 40008:
            $info = "不合法的消息类型";
            break;
        case 40009:
            $info = "不合法的图片文件大小";
            break;
        case 40010:
            $info = "不合法的语音文件大小";
            break;
        case 40011:
            $info = "不合法的视频文件大小";
            break;
        case 40012:
            $info = "不合法的缩略图文件大小";
            break;
        case 40013:
            $info = "不合法的APPID";
            break;
        case 41001:
            $info = "缺少access_token参数";
            break;
        case 41002:
            $info = "缺少appid参数";
            break;
        case 41003:
            $info = "缺少refresh_token参数";
            break;
        case 41004:
            $info = "缺少secret参数";
            break;
        case 41005:
            $info = "缺少多媒体文件数据";
            break;
        case 41006:
            $info = "access_token超时";
            break;
        case 42001:
            $info = "需要GET请求";
            break;
        case 43002:
            $info = "需要POST请求";
            break;
        case 43003:
            $info = "需要HTTPS请求";
            break;
        case 44001:
            $info = "多媒体文件为空";
            break;
        case 44002:
            $info = "POST的数据包为空";
            break;
        case 44003:
            $info = "图文消息内容为空";
            break;
        case 45001:
            $info = "多媒体文件大小超过限制";
            break;
        case 45002:
            $info = "消息内容超过限制";
            break;
        case 45003:
            $info = "标题字段超过限制";
            break;
        case 45004:
            $info = "描述字段超过限制";
            break;
        case 45005:
            $info = "链接字段超过限制";
            break;
        case 45006:
            $info = "图片链接字段超过限制";
            break;
        case 45007:
            $info = "语音播放时间超过限制";
            break;
        case 45008:
            $info = "图文消息超过限制";
            break;
        case 45009:
            $info = "接口调用超过限制";
            break;
        case 46001:
            $info = "不存在媒体数据";
            break;
        case 47001:
            $info = "解析JSON/XML内容错误";
            break;
    }
    return $info;
}


/**
 * APP推送消息
 * @param $uid     会员id
 * @param $title   消息标题
 * @param $body    消息内容
 * @param $url     跳转地址
 * @param $music   音效
 * @param $all     是否推送所有设备
 */
function sendapppush($uid, $title, $body, $url = "", $music = "default", $all = false, $im = array()){
    global $dsql;
    global $cfg_basehost;
    global $cfg_secureAccess;

    if (!$all && (!$uid || $uid < 1)) return;

    if(!$all){
        //是否登录设备s
        $sourceclientAll = '';
        $iosSend = $androidSend = false;
        $sql = $dsql->SetQuery("SELECT `sourceclient` FROM `#@__member`  WHERE `id` = $uid");
        $res = $dsql->dsqlOper($sql, "results");
        if($res[0]['sourceclient']){
            $sourceclientAll = unserialize($res[0]['sourceclient']);
            foreach ($sourceclientAll as $key => $value) {
                $val = strtolower($value['type']);
                if (preg_match("/android|mini|mobile|mobi|Nokia|Symbian/", $val)) {
                    $androidSend = true;
                }
                if(preg_match("/iphone|ios|iPod|iPad|/", $val)){
                    $iosSend = true;
                }
            }
        }
        //是否登录设备e

        //查询会员未读消息数量
        //$sql = $dsql->SetQuery("SELECT log.`id` FROM `#@__member_letter_log` log LEFT JOIN `#@__member_letter` l ON l.`id` = log.`lid` WHERE log.`state` = 0 AND l.`type` = 0 AND log.`uid` = $uid");
        //$msgnum = $dsql->dsqlOper($sql, "totalCount");
        $msgnum = 0;
    }else{
        $iosSend = $androidSend = true;
    }

    //查询推送配置
    $platform = $android_access_id = $android_access_key = $android_secret_key = $ios_access_id = $ios_access_key = $ios_secret_key = "";
    $sql = $dsql->SetQuery("SELECT * FROM `#@__app_push_config` LIMIT 0, 1");
    $ret = $dsql->dsqlOper($sql, "results");
    if ($ret) {
        $data = $ret[0];
        $platform = $data['platform'];
        $android_access_id = $data['android_access_id'];
        $android_access_key = $data['android_access_key'];
        $android_secret_key = $data['android_secret_key'];
        $ios_access_id = $data['ios_access_id'];
        $ios_access_key = $data['ios_access_key'];
        $ios_secret_key = $data['ios_secret_key'];


        //配送员版
        if ($music == "peisongordercancel" || $music == "newfenpeiorder" || $music == "paotuidaiqiang") {

            $url = empty($url) ? $cfg_secureAccess . $cfg_basehost . "/?service=waimai&do=courier&state=4,5" : $url;
            $android_access_id = $data['peisong_android_access_id'];
            $android_access_key = $data['peisong_android_access_key'];
            $android_secret_key = $data['peisong_android_secret_key'];

            $ios_access_id = $data['peisong_ios_access_id'];
            $ios_access_key = $data['peisong_ios_access_key'];
            $ios_secret_key = $data['peisong_ios_secret_key'];
        } //商家版
        elseif ($music == "shopordercancel" || $music == "newshoporder") {
            $android_access_id = $data['business_android_access_id'];
            $android_access_key = $data['business_android_access_key'];
            $android_secret_key = $data['business_android_secret_key'];

            $ios_access_id = $data['business_ios_access_id'];
            $ios_access_key = $data['business_ios_access_key'];
            $ios_secret_key = $data['business_ios_secret_key'];

            $music = $music == "shopordercancel" ? "peisongordercancel" : "newwaimaiorder";
        }

    }


    //友盟推送
    if ($platform == "umeng") {
        require_once(HUONIAOINC . '/class/push/umeng/AndroidCustomizedcast.php');
        require_once(HUONIAOINC . '/class/push/umeng/IOSCustomizedcast.php');

        //安卓推送
        if($androidSend){
            $customizedcast = new AndroidCustomizedcast();
            $customizedcast->setAppMasterSecret($android_secret_key);
            $customizedcast->setPredefinedKeyValue("appkey", $android_access_key);
            $customizedcast->setPredefinedKeyValue("timestamp", strval(time()));
            if(!$all){
                $customizedcast->setPredefinedKeyValue("alias", $uid);
                $customizedcast->setPredefinedKeyValue("alias_type", "userID");
            }
            $customizedcast->setPredefinedKeyValue("ticker", $title);
            $customizedcast->setPredefinedKeyValue("title", $title);
            $customizedcast->setPredefinedKeyValue("text", $body);
            $customizedcast->setPredefinedKeyValue("after_open", "go_app");
            $customizedcast->setExtraField("url", $url);
            if($im){
            	foreach ($im as $key => $value) {
            		$customizedcast->setExtraField($key, $value);
            	}
            }
            $customizedcast->send();
        }

        //ios推送
        if($iosSend){
            $customizedcast = new IOSCustomizedcast();
            $customizedcast->setAppMasterSecret($ios_secret_key);
            $customizedcast->setPredefinedKeyValue("appkey", $ios_access_key);
            $customizedcast->setPredefinedKeyValue("timestamp", strval(time()));
            if(!$all){
                $customizedcast->setPredefinedKeyValue("alias", $uid);
                $customizedcast->setPredefinedKeyValue("alias_type", "userID");
            }
            $customizedcast->setPredefinedKeyValue("alert", $body);
            $customizedcast->setPredefinedKeyValue("badge", $msgnum);
            $customizedcast->setPredefinedKeyValue("sound", "chime");
            $customizedcast->setPredefinedKeyValue("production_mode", "false");
            $customizedcast->setCustomizedField("url", $url);
            if($im){
            	foreach ($im as $key => $value) {
            		$customizedcast->setExtraField($key, $value);
            	}
            }
            $customizedcast->send();
        }

        //阿里云推送
    } elseif ($platform == "aliyun") {

        include_once HUONIAOINC . '/class/push/aliyun/PushRequest.php';


        // ------------------------------android
        if($androidSend){
            $iClientProfile = DefaultProfile::getProfile("cn-hangzhou", $android_access_id, $android_secret_key);
            if($iClientProfile){
                $client = new DefaultAcsClient($iClientProfile);
                $request = new PushRequest();

                // 推送目标
                $request->setAppKey($android_access_key);
                if($all){
                    $request->setTarget("ALL");
                    $request->setTargetValue("ALL");
                }else{
                    $request->setTarget("ACCOUNT"); //推送目标: DEVICE:推送给设备; ACCOUNT:推送给指定帐号,TAG:推送给自定义标签; ALL: 推送给全部
                    $request->setTargetValue($uid); //根据Target来设定，如Target=device, 则对应的值为 设备id1,设备id2. 多个值使用逗号分隔.(帐号与设备有一次最多100个的限制)
                }
                $request->setDeviceType("ANDROID"); //设备类型 ANDROID iOS ALL.
                $request->setPushType("NOTICE"); //消息类型 MESSAGE NOTICE
                $request->setTitle($title); // 消息的标题
                $request->setBody($body); // 消息的内容

                // 推送配置: Android
                $request->setAndroidNotifyType("BOTH");//通知的提醒方式 "VIBRATE" : 震动 "SOUND" : 声音 "BOTH" : 声音和震动 NONE : 静音
                $request->setAndroidNotificationBarType(1);//通知栏自定义样式0-100
                $request->setAndroidNotificationChannel("huoniao");
                $request->setAndroidMusic($music);//Android通知音乐

                $params = array(
                	"music" => $music,
                	"url" => $url
                );
                if($im){
                	$request->setAndroidOpenType("NONE");//点击通知后动作 "APPLICATION" : 打开应用 "ACTIVITY" : 打开AndroidActivity "URL" : 打开URL "NONE" : 无跳转
                	foreach ($im as $key => $value) {
                		$params[$key] = $value;
                	}
                }else{
                	$request->setAndroidOpenType("NONE");//点击通知后动作 "APPLICATION" : 打开应用 "ACTIVITY" : 打开AndroidActivity "URL" : 打开URL "NONE" : 无跳转
                }
	            $request->setAndroidExtParameters(json_encode($params)); // 设定android类型设备通知的扩展属性设定android类型设备通知的扩展属性


                $response = $client->getAcsResponse($request);
            }
        }


        // ------------------------------ios
        if($iosSend){
            $iClientProfile_ios = DefaultProfile::getProfile("cn-hangzhou", $ios_access_id, $ios_secret_key);
            if($iClientProfile_ios){
                $client = new DefaultAcsClient($iClientProfile_ios);
                $request = new PushRequest();

                // 推送目标
                $request->setAppKey($ios_access_key);
                if($all){
                    $request->setTarget("ALL");
                    $request->setTargetValue("ALL");
                }else{
                    $request->setTarget("ACCOUNT"); //推送目标: DEVICE:推送给设备; ACCOUNT:推送给指定帐号,TAG:推送给自定义标签; ALL: 推送给全部
                    $request->setTargetValue($uid); //根据Target来设定，如Target=device, 则对应的值为 设备id1,设备id2. 多个值使用逗号分隔.(帐号与设备有一次最多100个的限制)
                }
                $request->setDeviceType("iOS"); //设备类型 ANDROID iOS ALL.
                $request->setPushType("NOTICE"); //消息类型 MESSAGE NOTICE
                $request->setTitle($title); // 消息的标题
                $request->setBody($body); // 消息的内容

                // 推送配置: iOS
                $request->setiOSSilentNotification("true");//是否开启静默通知
                $request->setiOSMusic($music . ".m4a"); // iOS通知声音
                $request->setiOSBadge($msgnum); // iOS应用图标右上角角标
                $request->setiOSApnsEnv("PRODUCT");//iOS的通知是通过APNs中心来发送的，需要填写对应的环境信息。"DEV" : 表示开发环境 "PRODUCT" : 表示生产环境
                $request->setiOSRemind("false"); // 推送时设备不在线（既与移动推送的服务端的长连接通道不通），则这条推送会做为通知，通过苹果的APNs通道送达一次(发送通知时,Summary为通知的内容,Message不起作用)。注意：离线消息转通知仅适用于生产环境
                $request->setiOSRemindBody("iOSRemindBody");//iOS消息转通知时使用的iOS通知内容，仅当iOSApnsEnv=PRODUCT && iOSRemind为true时有效

                $params = array(
                	"url" => $url
                );
                if($im){
                	foreach ($im as $key => $value) {
                		$params[$key] = $value;
                	}
                }
                $request->setiOSExtParameters(json_encode($params)); //自定义的kv结构,开发者扩展用 针对iOS设备

                $response = $client->getAcsResponse($request);
            }
        }

    }

//初始化日志
// require_once HUONIAOROOT."/api/payment/log.php";
// $logHandler= new CLogFileHandler(HUONIAOROOT.'/api/push.log');
// $log = Log::Init($logHandler, 15);

// $arr = array(
//     "uid" => $uid,
//     "title" => $title,
//     "body" => $body,
//     "url" => $url,
//     "music" => $music,
//     "android_access_id" => $android_access_id,
//     "android_secret_key" => $android_secret_key,
//     "android_access_key" => $android_access_key,
//     "ios_access_id" => $ios_access_id,
//     "ios_secret_key" => $ios_secret_key,
//     "ios_access_key" => $ios_access_key,
// );
// $str = [];
// foreach ($arr as $key => $value) {
//     array_push($str, $key . " : " . $value);
// }
// Log::DEBUG($pubdate . "\r" . join("\r\n", $str) . "\r\n\r\n");

}


/**
 * 创建支付中转页面
 * @param $service  所属频道
 * @param $ordernum 订单号
 * @param $price    订单金额
 * @param $paytype  支付方式
 * @return html
 */
function createPayForm($service, $ordernum, $price, $paytype, $subject, $param = array()){
  global $qr;
    if (!empty($service) && !empty($ordernum) && !empty($price) && (!empty($paytype) || !empty($qr))) {

        global $cfg_shortname;
        global $dsql;
        global $userLogin;
        global $siteCityInfo;

        $siteCityName = $siteCityInfo ? $siteCityInfo['name'] : '';

        $paytype = explode("$", $paytype);
        $paycode = $paytype[0];
        $bank = empty($paytype[1]) || $paytype[1] == null ? '' : $paytype[1];

        $paymentFile = HUONIAOROOT . "/api/payment/$paycode/$paycode.php";

        //验证支付类文件是否存在
        if ((!$qr && file_exists($paymentFile)) || $qr) {

            if(!$qr){
              require_once($paymentFile);
              $archives = $dsql->SetQuery("SELECT `pay_config` FROM `#@__site_payment` WHERE `pay_code` = '$paycode' AND `state` = 1");
              $payment = $dsql->dsqlOper($archives, "results");
            }
            if ($payment || $qr) {

              if(!$qr){
                $pay_config = unserialize($payment[0]['pay_config']);
                $paymentArr = array();

                //验证配置
                foreach ($pay_config as $key => $value) {
                    if (!empty($value['value'])) {
                        $paymentArr[$value['name']] = $value['value'];
                    }
                }
              }

                if (!empty($paymentArr) || $qr) {
                    //   global $autoload;
                    //   $autoload = true;

                    if(!$qr){
                      $pay = new $paycode();
                      $order = array();
                    }

                    //如果订单号有多个，需要重新生成支付订单号  by: guozi  20170711
                    if (strstr($ordernum, ",")) {
                        $order_sn = create_ordernum();
                    } else {
                        $order_sn = $ordernum;
                    }

                    $order['service'] = $service;
                    $order['order_amount'] = sprintf('%.2f', $price);
                    $order['order_sn'] = $order_sn;
                    $order['subject'] = str_replace('$city', $siteCityName, $cfg_shortname) . "_" . $subject;
                    $order['bank'] = $bank;
                    $order['ordernum'] = $ordernum;

                    //向数据库插入记录
                    $userid = $userLogin->getMemberID();
                    $userid = $userid == -1 ? $param['userid'] : $userid;

                    //删除当前订单没有支付的历史记录
                    $sql = $dsql->SetQuery("DELETE FROM `#@__pay_log` WHERE `body` = '$ordernum' AND `state` = 0");
                    $dsql->dsqlOper($sql, "update");


                    //会员付款、外卖单独配置
                    if ($service == "member" || $service == "waimai" || $param) {
                        $ordernum = serialize($param);
                    }

                    //验证订单号是否已经存在
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__pay_log` WHERE `ordernum` = '$order_sn'");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if(!$ret){
                      $date = GetMkTime(time());
                      $archives = $dsql->SetQuery("INSERT INTO `#@__pay_log` (`ordertype`, `ordernum`, `uid`, `body`, `amount`, `paytype`, `state`, `pubdate`) VALUES ('$service', '$order_sn', '$userid', '$ordernum', '$price', '$paycode', 0, $date)");
                      $dsql->dsqlOper($archives, "results");
                    }

                    if($qr){
                      return $order;
                    }else{
                      echo $pay->get_code($order, $paymentArr);
                    }
                    die;

                } else {
                    die("配置错误，请联系管理员000！");
                }


            } else {
                die("支付方式不存在，001！");
            }

        } else {
            die("支付方式不存在，002！");
        }


    } else {
        die("配置错误，请联系管理员003！");
    }

}


/**
 * 数组排序
 * @param $arrays     要操作的数组
 * @param $sort_key   指定的键值
 * @param $sort_order 排列顺序  SORT_ASC、SORT_DESC
 * @param $sort_type  排序类型  SORT_REGULAR、SORT_NUMERIC、SORT_STRING
 * @return array
 */
function array_sortby($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC){
    if (is_array($arrays)) {
        foreach ($arrays as $array) {
            if (is_array($array)) {
                $key_arrays[] = $array[$sort_key];
            } else {
                return false;
            }
        }
    } else {
        return false;
    }

    array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
    return $arrays;
}


/**
 * 拼接运费详细
 * @param $bearFreight           是否包邮 0：自定义  1：免费
 * @param $valuation             计价方式 0：按件  1：按重量  2：按体积
 * @param $express_start         默认运费几件以内
 * @param $express_postage       默认运费
 * @param $express_plus          递增数量
 * @param $express_postageplus   递增费用
 * @param $preferentialStandard  超过数量免费
 * @param $preferentialMoney     超过费用免费
 * @return string
 */
function getPriceDetail($bearFreight, $valuation, $express_start, $express_postage, $express_plus, $express_postageplus, $preferentialStandard, $preferentialMoney){
    $ret = "";
    $currency = echoCurrency(array("type" => "short"));

    global $langData;

    if ($bearFreight == 0) {

        $val = "";
        switch ($valuation) {
            case 0:
                $val = $langData['siteConfig'][21][10];  //件
                break;
            case 1:
                $val = "kg";
                break;
            case 2:
                $val = "m³";
                break;
        }

        $ret = $langData['siteConfig'][19][325] . "：" . $express_start . $val . $langData['siteConfig'][21][11] . $express_postage . $currency;   //运费     内

        if ($express_plus > 0) {
            $ret .= "，" . $langData['siteConfig'][21][12] . $express_plus . $val . "，" . $langData['siteConfig'][21][13] . $express_postageplus . $currency;   //每增加      加
        }

        if ($preferentialStandard > 0 && $preferentialMoney > 0) {
            $ret .= "（" . $langData['siteConfig'][21][14] . $preferentialStandard . $val . "，" . $langData['siteConfig'][21][15] . $preferentialMoney . $currency . $langData['siteConfig'][21][16] . "）";   //满     并且满     免邮费
        } elseif ($preferentialStandard > 0) {
            $ret .= "（" . $langData['siteConfig'][21][14] . $preferentialStandard . $val . $currency . $langData['siteConfig'][21][16] . "）";   //满    免邮费
        } elseif ($preferentialMoney > 0) {
            $ret .= "（" . $langData['siteConfig'][21][14] . $preferentialMoney . $currency . $langData['siteConfig'][21][16] . "）";   //满     免邮费
        }

    } else {
        $ret = $langData['siteConfig'][21][16];    //免邮费
    }
    return $ret;
}


/**
 * 计算运费
 * @param $config : 运费配置信息
 * @param $price : 单价
 * @param $count : 商品数量
 * @param $volume : 体积
 * @param $weight : 重量
 * @return int
 */
function getLogisticPrice($config, $price, $count, $volume, $weight){

    $bearFreight = $config['bearFreight'];
    $valuation = $config['valuation'];
    $express_start = $config['express_start'];
    $express_postage = $config['express_postage'];
    $express_plus = $config['express_plus'];
    $express_postageplus = $config['express_postageplus'];
    $preferentialStandard = $config['preferentialStandard'];
    $preferentialMoney = $config['preferentialMoney'];

    if ($bearFreight == 1) return 0;

    //总价
    $totalPrice = $price * $count;

    $logistic = 0;

    //计费对象
    $obj = $count;
    $ncount = $count;

    //按重量
    if ($valuation == 1) {
        $obj = $weight * $count;
        $ncount = $count * $weight;

        //按体积
    } elseif ($valuation == 2) {
        $obj = $volume * $count;
        $ncount = $count * $volume;
    }

    //默认运费
    $logistic += $express_postage;

    //续加
    if ($express_start > 0) {
        $postage = $obj - $express_start;
        if ($postage > 0 && $express_plus > 0) {
            $logistic += floor($postage / $express_plus) * $express_postageplus;
        }
    }

    //免费政策
    if (!empty($preferentialStandard) && $ncount >= $preferentialStandard && !empty($preferentialMoney) && $totalPrice >= $preferentialMoney) {
        $logistic = 0;
    } elseif (($preferentialStandard > 0 && $ncount >= $preferentialStandard && $preferentialMoney == 0) || ($preferentialMoney > 0 && $totalPrice >= $preferentialMoney && $preferentialStandard == 0)) {
        $logistic = 0;
    }

    return $logistic;

}


/**
 * 合并订单运费，一个订单只需要一次运费
 * @param $data : 订单信息  参考：shop.controller confirm-order
 * @return array  array('店铺ID' => 运费, '店铺ID' => 运费)
 */
function calculationOrderLogistic($config){
    global $dsql;
    $logisticArr = array();

    //先将同一店铺相册运费模板的数据分离
    foreach ($config as $key => $val){
        foreach ($val['list'] as $k => $v){
            $logisticArr[$val['sid']][$v['logisticId']][] = $v;
        }
    }

    $data = array();
    foreach ($logisticArr as $key => $val){

        $logistic = 0;

        foreach ($val as $k => $v){
            $price = $count = $volume = $weight = 0;
            foreach ($v as $k_ => $v_){
                $price += $v_['price'];
                $count += $v_['count'];
                $volume += $v_['volume'];
                $weight += $v_['weight'];
            }

            $bearFreight = 0;
            $valuation = 0;
            $express_start = 0;
            $express_postage = 0;
            $express_plus = 0;
            $express_postageplus = 0;
            $preferentialStandard = 0;
            $preferentialMoney = 0;

            $archives = $dsql->SetQuery("SELECT * FROM `#@__shop_logistictemplate` WHERE `id` = $k");
            $ret = $dsql->dsqlOper($archives, "results");
            if($ret){
                $value = $ret[0];
                $bearFreight = $value["bearFreight"];
                $valuation = $value["valuation"];
                $express_start = $value["express_start"];
                $express_postage = $value["express_postage"];
                $express_plus = $value["express_plus"];
                $express_postageplus = $value["express_postageplus"];
                $preferentialStandard = $value["preferentialStandard"];
                $preferentialMoney = $value["preferentialMoney"];
            }

            $arr = array(
                'bearFreight' => $bearFreight,
                'valuation' => $valuation,
                'express_start' => $express_start,
                'express_postage' => $express_postage,
                'express_plus' => $express_plus,
                'express_postageplus' => $express_postageplus,
                'preferentialStandard' => $preferentialStandard,
                'preferentialMoney' => $preferentialMoney,
            );

            $logistic += getLogisticPrice($arr, $price, $count, $volume, $weight);
        }

        $data[$key] = $logistic;

    }

    return $data;
}


/**
 * 更新htaccess静态规则文件
 */
function updateHtaccess(){
    return false;
}


//Geetest 极验验证码验证
function verifyGeetest($challenge, $validate, $seccode, $type = "pc"){
    global $cfg_geetest_id;
    global $cfg_geetest_key;
    global $userLogin;

    $userid = $_SESSION['user_id'];

    global $handler;
    $handler = false;

    $GtSdk = new geetestlib($cfg_geetest_id, $cfg_geetest_key);

    //服务器正常
    if ($_SESSION['gtserver'] == 1) {
        $result = $GtSdk->success_validate($challenge, $validate, $seccode, $userid);
        if ($result) {
            return '{"status":"success"}';
        } else {
            return '{"status":"fail"}';
        }
    } else {  //服务器宕机,走failback模式
        if ($GtSdk->fail_validate($challenge, $validate, $seccode)) {
            return '{"status":"success"}';
        } else {
            return '{"status":"fail"}';
        }
    }


}


//更新APP配置文件
function updateAppConfig(){

    global $dsql;
    global $cfg_secureAccess;
    global $cfg_basehost;
    global $cfg_webname;
    global $cfg_shortname;

    //引导页
    $android_guide = array();
    $ios_guide = array();

    //广告
    $ad_pic = $ad_link = $ad_time = "";

    //登录
    $qq_appid = $qq_appkey = $wechat_appid = $wechat_appsecret = $sina_akey = $sina_skey = "";

    //APP配置参数
    $sql = $dsql->SetQuery("SELECT * FROM `#@__app_config` LIMIT 1");
    $ret = $dsql->dsqlOper($sql, "results");
    if ($ret) {
        $data = $ret[0];

        $ios_shelf = $data['ios_shelf'];
        $android = $data['android_guide'];
        $ios = $data['ios_guide'];
        $android_index = $data['android_index'];
        $ios_index = $data['ios_index'];
        $map_baidu_android = $data['map_baidu_android'];
        $map_baidu_ios = $data['map_baidu_ios'];
        $map_google_android = $data['map_google_android'];
        $map_google_ios = $data['map_google_ios'];
        $map_amap_android = $data['map_amap_android'];
        $map_amap_ios = $data['map_amap_ios'];
        $map_set = $data['map_set'];
        $peisong_map_baidu_android = $data['peisong_map_baidu_android'];
        $peisong_map_baidu_ios = $data['peisong_map_baidu_ios'];
        $peisong_map_google_android = $data['peisong_map_google_android'];
        $peisong_map_google_ios = $data['peisong_map_google_ios'];
        $peisong_map_amap_android = $data['peisong_map_amap_android'];
        $peisong_map_amap_ios = $data['peisong_map_amap_ios'];
        $peisong_map_set = $data['peisong_map_set'];

        //安卓引导页
        if (!empty($android)) {
            $androidArr = explode(",", $android);
            foreach ($androidArr as $key => $value) {
                array_push($android_guide, getFilePath($value));
            }
        }

        //IOS引导页
        if (!empty($ios)) {
            $iosArr = explode(",", $ios);
            foreach ($iosArr as $key => $value) {
                array_push($ios_guide, getFilePath($value));
            }
        }

        $ad_pic = $data['ad_pic'] ? getFilePath($data['ad_pic']) : "";
        $ad_link = $data['ad_link'];
        $ad_time = $data['ad_time'];

        //安装包信息
        $android_version = $data['android_version'];
        $android_update = $data['android_update'];
        $android_force = $data['android_force'];
        $android_size = $data['android_size'];
        $android_note = $data['android_note'] ? json_encode($data['android_note']) : "\"\"";
        $ios_version = $data['ios_version'];
        $ios_update = $data['ios_update'];
        $ios_force = $data['ios_force'];
        $ios_note = $data['ios_note'] ? json_encode($data['ios_note']) : "\"\"";
        $android_download = $data['android_download'];
        $ios_download = $data['ios_download'];

        $business_android_version = $data['business_android_version'];
        $business_android_update = $data['business_android_update'];
        $business_android_force = $data['business_android_force'];
        $business_android_size = $data['business_android_size'];
        $business_android_note = json_encode($data['business_android_note']);
        $business_ios_version = $data['business_ios_version'];
        $business_ios_update = $data['business_ios_update'];
        $business_ios_force = $data['business_ios_force'];
        $business_ios_note = json_encode($data['business_ios_note']);
        $business_android_download = $data['business_android_download'];
        $business_ios_download = $data['business_ios_download'];
        $peisong_android_version = $data['peisong_android_version'];
        $peisong_android_update = $data['peisong_android_update'];
        $peisong_android_force = $data['peisong_android_force'];
        $peisong_android_size = $data['peisong_android_size'];
        $peisong_android_note = json_encode($data['peisong_android_note']);
        $peisong_ios_version = $data['peisong_ios_version'];
        $peisong_ios_update = $data['peisong_ios_update'];
        $peisong_ios_force = $data['peisong_ios_force'];
        $peisong_ios_note = json_encode($data['peisong_ios_note']);
        $peisong_android_download = $data['peisong_android_download'];
        $peisong_ios_download = $data['peisong_ios_download'];
        $rongKeyID = $data['rongKeyID'];
        $rongKeySecret = $data['rongKeySecret'];

        //模板风格
        $template = $data['template'];
    }

    //登录配置参数
    $sql = $dsql->SetQuery("SELECT `code`, `config` FROM `#@__site_loginconnect`");
    $ret = $dsql->dsqlOper($sql, "results");
    if ($ret) {
        foreach ($ret as $key => $value) {
            $config = unserialize($value['config']);

            $configArr = array();
            foreach ($config as $k => $v) {
                $configArr[$v['name']] = $v['value'];
            }

            //QQ
            if ($value['code'] == 'qq') {
                $qq_appid = $configArr['app_appid'];
                $qq_appkey = $configArr['app_appkey'];

                //微信
            } elseif ($value['code'] == 'wechat') {
                $wechat_appid = $configArr['appid_app'];
                $wechat_appsecret = $configArr['appsecret_app'];

                //新浪
            } elseif ($value['code'] == 'sina') {
                $sina_akey = $configArr['akey_app'];
                $sina_skey = $configArr['skey_app'];
            }
        }
    }

    //推送
    $android_access_id = $android_access_key = $android_secret_key = $ios_access_id = $ios_access_key = $ios_secret_key = "";
    $sql = $dsql->SetQuery("SELECT * FROM `#@__app_push_config` LIMIT 1");
    $ret = $dsql->dsqlOper($sql, "results");
    if ($ret) {
        $data = $ret[0];

        $android_access_id = $data['android_access_id'];
        $android_access_key = $data['android_access_key'];
        $android_secret_key = $data['android_secret_key'];
        $ios_access_id = $data['ios_access_id'];
        $ios_access_key = $data['ios_access_key'];
        $ios_secret_key = $data['ios_secret_key'];

        $business_android_access_id = $data['business_android_access_id'];
        $business_android_access_key = $data['business_android_access_key'];
        $business_android_secret_key = $data['business_android_secret_key'];
        $business_ios_access_id = $data['business_ios_access_id'];
        $business_ios_access_key = $data['business_ios_access_key'];
        $business_ios_secret_key = $data['business_ios_secret_key'];

        $peisong_android_access_id = $data['peisong_android_access_id'];
        $peisong_android_access_key = $data['peisong_android_access_key'];
        $peisong_android_secret_key = $data['peisong_android_secret_key'];
        $peisong_ios_access_id = $data['peisong_ios_access_id'];
        $peisong_ios_access_key = $data['peisong_ios_access_key'];
        $peisong_ios_secret_key = $data['peisong_ios_secret_key'];


    }

    $siteCityName = "";
    $siteCityInfoCookie = GetCookie('siteCityInfo');
    if($siteCityInfoCookie){
    	$siteCityInfoJson = json_decode($siteCityInfoCookie, true);
    	if(is_array($siteCityInfoJson)){
    		$siteCityInfo = $siteCityInfoJson;
    		$siteCityName = $siteCityInfo['name'];
    	}
    }

    //基本设置文件内容
    $customInc = "{";
    //基本设置
    $customInc .= "\"cfg_basehost\": \"" . $cfg_secureAccess . $cfg_basehost . "\",";
    $customInc .= "\"cfg_ios_index\": \"" . $ios_index . "\",";
    $customInc .= "\"cfg_android_index\": \"" . $android_index . "\",";
    $customInc .= "\"cfg_ios_review\": \"" . $ios_shelf . "\",";
    $customInc .= "\"cfg_user_index\": \"" . getUrlPath(array("service" => "member", "type" => "user")) . "\",";
    $customInc .= "\"cfg_webname\": " . json_encode(str_replace('$city', $siteCityName, stripslashes($cfg_webname))) . ",";
    $customInc .= "\"cfg_shortname\": " . json_encode(str_replace('$city', $siteCityName, stripslashes($cfg_shortname))) . ",";
    $customInc .= "\"cfg_guide\": {";
    $customInc .= "\"android\": " . json_encode($android_guide) . ",";
    $customInc .= "\"ios\": " . json_encode($ios_guide) . "";
    $customInc .= "},";
    $customInc .= "\"cfg_rongcloud\": {";
    $customInc .= "\"id\": \"$rongKeyID\",";
    $customInc .= "\"secret\": \"$rongKeySecret\"";
    $customInc .= "},";
    $customInc .= "\"cfg_startad\": {";
    $customInc .= "\"time\": \"$ad_time\",";
    $customInc .= "\"src\": \"$ad_pic\",";
    $customInc .= "\"link\": \"$ad_link\"";
    $customInc .= "},";
    $customInc .= "\"cfg_umeng\": {";
    $customInc .= "\"android\": \"$android_access_key\",";
    $customInc .= "\"ios\": \"$ios_access_key\"";
    $customInc .= "},";
    $customInc .= "\"cfg_loginconnect\": {";
    $customInc .= "\"qq\": {";
    $customInc .= "\"appid\": \"$qq_appid\",";
    $customInc .= "\"appkey\": \"$qq_appkey\"";
    $customInc .= "},";
    $customInc .= "\"wechat\": {";
    $customInc .= "\"appid\": \"$wechat_appid\",";
    $customInc .= "\"appsecret\": \"$wechat_appsecret\"";
    $customInc .= "},";
    $customInc .= "\"sina\": {";
    $customInc .= "\"akey\": \"$sina_akey\",";
    $customInc .= "\"skey\": \"$sina_skey\"";
    $customInc .= "}";
    $customInc .= "},";
    $customInc .= "\"template\": \"$template\",";

    //此处只为信鸽配置，由于将推送换成了友盟，此处就不需要了  by: 20170621  guozi
    // $customInc .= "\"cfg_push\":{";
    // $customInc .= "\"android\":{";
    // $customInc .= "\"access_id\": \"$android_access_id\",";
    // $customInc .= "\"access_key\": \"$android_access_key\",";
    // $customInc .= "\"secret_key\": \"$android_secret_key\"";
    // $customInc .= "},";
    // $customInc .= "\"ios\":{";
    // $customInc .= "\"access_id\": \"$ios_access_id\",";
    // $customInc .= "\"access_key\": \"$ios_access_key\",";
    // $customInc .= "\"secret_key\": \"$ios_secret_key\"";
    // $customInc .= "}";
    // $customInc .= "},";

    $customInc .= "\"cfg_app_info\":{";
    // $customInc .= "\"portal\":{";
    $customInc .= "\"android\":{";
    $customInc .= "\"version\": \"$android_version\",";
    $customInc .= "\"update\": \"$android_update\",";
    $customInc .= "\"size\": \"$android_size\",";
    $customInc .= "\"note\": $android_note,";
    $customInc .= "\"url\": \"$android_download\",";
    $customInc .= "\"force\": \"$android_force\"";
    $customInc .= "},";
    $customInc .= "\"ios\":{";
    $customInc .= "\"version\": \"$ios_version\",";
    $customInc .= "\"update\": \"$ios_update\",";
    $customInc .= "\"note\": $ios_note,";
    $customInc .= "\"url\": \"$ios_download\",";
    $customInc .= "\"force\": \"$ios_force\"";
    $customInc .= "},";
    // $customInc .= "},";
    $customInc .= "\"business\":{";
    $customInc .= "\"android\":{";
    $customInc .= "\"version\": \"$business_android_version\",";
    $customInc .= "\"update\": \"$business_android_update\",";
    $customInc .= "\"size\": \"$business_android_size\",";
    $customInc .= "\"note\": $business_android_note,";
    $customInc .= "\"url\": \"$business_android_download\",";
    $customInc .= "\"force\": \"$business_android_force\"";
    $customInc .= "},";
    $customInc .= "\"ios\":{";
    $customInc .= "\"version\": \"$business_ios_version\",";
    $customInc .= "\"update\": \"$business_ios_update\",";
    $customInc .= "\"note\": $business_ios_note,";
    $customInc .= "\"url\": \"$business_ios_download\",";
    $customInc .= "\"force\": \"$business_ios_force\"";
    $customInc .= "}";
    $customInc .= "},";
    $customInc .= "\"peisong\":{";
    $customInc .= "\"android\":{";
    $customInc .= "\"version\": \"$peisong_android_version\",";
    $customInc .= "\"update\": \"$peisong_android_update\",";
    $customInc .= "\"size\": \"$peisong_android_size\",";
    $customInc .= "\"note\": $peisong_android_note,";
    $customInc .= "\"url\": \"$peisong_android_download\",";
    $customInc .= "\"force\": \"$peisong_android_force\"";
    $customInc .= "},";
    $customInc .= "\"ios\":{";
    $customInc .= "\"version\": \"$peisong_ios_version\",";
    $customInc .= "\"update\": \"$peisong_ios_update\",";
    $customInc .= "\"note\": $peisong_ios_note,";
    $customInc .= "\"url\": \"$peisong_ios_download\",";
    $customInc .= "\"force\": \"$peisong_ios_force\"";
    $customInc .= "}";
    $customInc .= "}";
    $customInc .= "},";

    //门户地图
    $map_current = "";
    if ($map_set == 1) {
        $map_current = "google";
    } elseif ($map_set == 2) {
        $map_current = "baidu";
    } elseif ($map_set == 3) {
        $map_current = "qq";
    } elseif ($map_set == 4) {
        $map_current = "amap";
    }
    $customInc .= "\"cfg_map_current\":\"$map_current\",";

    $customInc .= "\"cfg_map\":{";
    $customInc .= "\"baidu\":{";
    $customInc .= "\"android\": \"" . $map_baidu_android . "\",";
    $customInc .= "\"ios\": \"" . $map_baidu_ios . "\"";
    $customInc .= "},";
    $customInc .= "\"google\":{";
    $customInc .= "\"android\": \"" . $map_google_android . "\",";
    $customInc .= "\"ios\": \"" . $map_google_ios . "\"";
    $customInc .= "},";
    $customInc .= "\"amap\":{";
    $customInc .= "\"android\": \"" . $map_amap_android . "\",";
    $customInc .= "\"ios\": \"" . $map_amap_ios . "\"";
    $customInc .= "}";
    $customInc .= "},";

    //骑手地图
    $map_current = "";
    if ($peisong_map_set == 1) {
        $map_current = "google";
    } elseif ($peisong_map_set == 2) {
        $map_current = "baidu";
    } elseif ($peisong_map_set == 3) {
        $map_current = "qq";
    } elseif ($peisong_map_set == 4) {
        $map_current = "amap";
    }
    $customInc .= "\"peisong_map_current\":\"$map_current\",";

    $customInc .= "\"peisong_map\":{";
    $customInc .= "\"baidu\":{";
    $customInc .= "\"android\": \"" . $peisong_map_baidu_android . "\",";
    $customInc .= "\"ios\": \"" . $peisong_map_baidu_ios . "\"";
    $customInc .= "},";
    $customInc .= "\"google\":{";
    $customInc .= "\"android\": \"" . $peisong_map_google_android . "\",";
    $customInc .= "\"ios\": \"" . $peisong_map_google_ios . "\"";
    $customInc .= "},";
    $customInc .= "\"amap\":{";
    $customInc .= "\"android\": \"" . $peisong_map_amap_android . "\",";
    $customInc .= "\"ios\": \"" . $peisong_map_amap_ios . "\"";
    $customInc .= "}";
    $customInc .= "}";

    $customInc .= "}";

    $customIncFile = HUONIAOROOT . "/api/appConfig.json";
    $fp = fopen($customIncFile, "w") or die('{"state": 200, "info": ' . json_encode("写入文件 $customIncFile 失败，请检查权限！") . '}');
    fwrite($fp, $customInc);
    fclose($fp);
}


/**
    * 根据指定表、指定ID获取相关信息
    * 根据指定区域ID，获取所在分站   使用方法：getPublicParentInfo(array('tab' => 'site_area', 'id' => $addrid, 'split' => '/', 'type' => 'typename', 'action' => 'addr'));
    * @return array
    */
function getPublicParentInfo($params){
    global $dsql;
    extract($params);
    if (empty($tab) || empty($id)) return;
    $currIndex = 0;
    $cityArr = array();

    $type = $type ? $type : "id";
    $split = $split ? $split : ",";

    $typeArr = getParentArr($tab, $id);

    global $data;
    $data = "";
    $typeIds = array_reverse(parent_foreach($typeArr, 'id'));

    global $data;
    $data = "";
    $arr = array_reverse(parent_foreach($typeArr, $type));

    //当action为area时，代表输出全部信息，不需要验证开通的城市
    if($action != 'area'){

      //此操作为了不让前台输出多余信息，比如开通了苏州分站，城市的详细信息只需要从苏州开始显示就可以了，不需要显示江苏
      $sql = $dsql->SetQuery("SELECT a.`id` cid FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` ORDER BY c.`id`");
      $result = $dsql->dsqlOper($sql, "results");
      if($result){
        foreach ($result as $key => $value) {
          array_push($cityArr, $value['cid']);
        }
      }

      foreach ($typeIds as $key => $value) {
        foreach ($cityArr as $k => $v) {
          if($v == $value){
            $currIndex = $key;
          }
        }
      }

    }

    return join($split, array_slice($arr, $currIndex));

}


//输出货币标识
function echoCurrency($params, $smarty = array()){
    $type = $params['type'];

    global $currency_name;
    global $currency_short;
    global $currency_symbol;
    global $currency_code;

    $currency_name = !empty($currency_name) ? $currency_name : "人民币";
    $currency_short = !empty($currency_short) ? $currency_short : "元";
    $currency_symbol = !empty($currency_symbol) ? $currency_symbol : "¥";
    $currency_code = !empty($currency_code) ? $currency_code : "RMB";

    if ($type == "name") {
        return $currency_name;
    } elseif ($type == "short") {
        return $currency_short;
    } elseif ($type == "symbol") {
        return $currency_symbol;
    } elseif ($type == "code") {
        return $currency_code;
    }
}


//根据模块标识获取模块名称
function getModuleTitle($params, $smarty = array()){
    global $dsql;

    $name = $params['name'];
    if (!empty($name)) {
        $sql = $dsql->SetQuery("SELECT `title`, `subject` FROM `#@__site_module` WHERE `name` = '$name'");
        $ret = $dsql->dsqlOper($sql, "results");
        if ($ret) {
            return $ret[0]['subject'] ? $ret[0]['subject'] : $ret[0]['title'];
        }
    }
}


//搜索记录
function siteSearchLog($module, $keyword){
    global $dsql;
    if (empty($module) || empty($keyword)) {
        return;
    }

    $time = GetMkTime(time());

    //查询是否搜索过
    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_search` WHERE `module` = '$module' AND `keyword` = '$keyword'");
    $ret = $dsql->dsqlOper($sql, "results");
    if ($ret) {
        //搜索过的增加搜索次数
        $sql = $dsql->SetQuery("UPDATE `#@__site_search` SET `count` = `count` + 1, `lasttime` = $time WHERE `module` = '$module' AND `keyword` = '$keyword'");
        $dsql->dsqlOper($sql, "update");
    } else {
        //没有搜索过的新增记录
        $sql = $dsql->SetQuery("INSERT INTO `#@__site_search` (`module`, `keyword`, `count`, `lasttime`) VALUES ('$module', '$keyword', '1', $time)");
        $dsql->dsqlOper($sql, "update");
    }
}


//获取会员等级特权
function getMemberLevelAuth($level){
    global $dsql;
    $return = array();

    //验证是否合法
    $sql = $dsql->SetQuery("SELECT `privilege` FROM `#@__member_level` WHERE `id` = $level");
    $ret = $dsql->dsqlOper($sql, "results");
    if ($ret) {
        $return = !empty($ret[0]['privilege']) ? unserialize($ret[0]['privilege']) : array();
    }
    return $return;

}


//根据模块标识验证会员是否有权限
//type=userCenter时，判断会员类型，个人会员直接通过，企业会员则验证绑定模块
function verifyModuleAuth($params, $smarty = array()){
    global $dsql;
    global $userLogin;

    $now = time();
    $module = $params['module'];
    $type   = $params['type'];
    $userid = $userLogin->getMemberID();

    $bind_module = array();
    if($userid != -1){

        // 不需要验证的模块
        if($module == "business" || $module == "huangye" || $module == "integral") return true;

        if($type == "userCenter"){
            $sql = $dsql->SetQuery("SELECT `mtype` FROM `#@__member` WHERE `id` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret[0]['mtype'] == 1){
                return true;
            }
        }

        $sql = $dsql->SetQuery("SELECT `id`, `bind_module` FROM `#@__business_list` WHERE `uid` = $userid AND `state` != 4");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $bind_module = $ret[0]['bind_module'] ? explode(',', $ret[0]['bind_module']) : array();
        }else{
            return false;
        }

        $showModule = checkShowModule($bind_module, 'manage');
        if(isset($showModule[$module]) && $showModule[$module]['show']){
            return true;
        }else{
            return false;
        }
    }

}


/**
 * @desc 根据两点间的经纬度计算距离
 * @param float $lat 纬度值
 * @param float $lng 经度值
 */
function getDistance($lng1, $lat1, $lng2, $lat2){ // 自动派单时的正确参数顺序
    // function getDistance($lat1, $lng1, $lat2, $lng2){
    $earthRadius = 6367000; //approximate radius of earth in meters

    /*
       Convert these degrees to radians
       to work with the formula
     */

    $lat1 = ($lat1 * pi()) / 180;
    $lng1 = ($lng1 * pi()) / 180;

    $lat2 = ($lat2 * pi()) / 180;
    $lng2 = ($lng2 * pi()) / 180;

    /*
       Using the
       Haversine formula

       http://en.wikipedia.org/wiki/Haversine_formula

       calculate the distance
     */

    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}


//打印
// pp 为强制打印，不需要验证是否开启自动打印外卖新订单选项
function printerWaimaiOrder($id, $pp = false){
    global $cfg_shortname;
    global $dsql;
    global $langData;

    $date = GetMkTime(date("Y-m-d"));


    //消息通知
    $sql = $dsql->SetQuery("SELECT
         s.`shopname`, s.`smsvalid`, s.`sms_phone`, s.`auto_printer`, s.`showordernum`, s.`bind_print`, s.`print_config`, s.`print_state`, o.`state`, o.`ordernum`, o.`ordernumstore`, o.`food`, o.`person`, o.`tel`, o.`address`, o.`paytype`, o.`amount`, o.`priceinfo`, o.`preset`, o.`note`, o.`pubdate`, o.`uid`
         FROM `#@__waimai_shop` s LEFT JOIN `#@__waimai_order` o ON o.`sid` = s.`id` WHERE o.`id` = $id");
    $ret = $dsql->dsqlOper($sql, "results");
    if ($ret) {
        $data = $ret[0];
        $shopname = $data['shopname'];
        $smsvalid = $data['smsvalid'];
        $sms_phone = $data['sms_phone'];
        $auto_printer = $data['auto_printer'];
        $showordernum = $data['showordernum'];
        $bind_print = $data['bind_print'];
        $print_config = $data['print_config'];
        $print_state = $data['print_state'];

        $state = $data['state'];
        $ordernum = $data['ordernum'];
        $ordernumstore = $data['ordernumstore'];
        $food = unserialize($data['food']);
        $person = $data['person'];
        $tel = $data['tel'];
        $address = $data['address'];
        $paytype = $data['paytype'];
        $amount = $data['amount'];
        $priceinfo = unserialize($data['priceinfo']);
        $preset = unserialize($data['preset']);
        $note = $data['note'];
        $pubdate = $data['pubdate'];
        $uid = $data['uid'];
        $count = explode("-", $ordernumstore);
        $count = $showordernum ? $count[1] : 0;


        //货到付款     已付款
        $amountInfo = $paytype == "delivery" ? $langData['siteConfig'][16][51] . "：" . $amount : $langData['siteConfig'][9][24] . "：" . $amount;

        //短信通知
        if ($smsvalid && $sms_phone && !$pp) {
            sendsms($sms_phone, 1, "", "", false, false, "会员-商家新订单通知", array("title" => ""));
        }


        //微信通知买家
        if ($state == 3) {
            $foods = array();
            foreach ($food as $key => $value) {
                array_push($foods, $value['title'] . " " . $value['count'] . $langData['siteConfig'][21][17]);  //份
            }

            $param = array(
                "service" => "member",
                "type" => "user",
                "template" => "orderdetail",
                "module" => "waimai",
                "id" => $id
            );

            //自定义配置
            $config = array(
                "ordernum" => $shopname . $ordernumstore,
                "orderdate" => date("Y-m-d H:i:s", $pubdate),
                "orderinfo" => join(" ", $foods),
                "orderprice" => $amount,
                "fields" => array(
                    'keyword1' => '订单编号',
                    'keyword2' => '下单时间',
                    'keyword3' => '订单详情',
                    'keyword4' => '订单金额'
                )
            );

            updateMemberNotice($uid, "会员-订单确认提醒", $param, $config);
        }


        //计算

        if (($auto_printer || $pp) && $bind_print == 1 && $print_config && $print_state == 1) {
            $print_config = unserialize($print_config);


            $num = "";
            if ($count) {
                $num = " #" . $count;
            }

            //预设内容
            $presets = "";
            $presetArr = array();
            if ($preset) {
                foreach ($preset as $key => $value) {
                    if (!empty($value['value'])) {
                        array_push($presetArr, $value['title'] . "：" . $value['value'] . "\r");
                    }
                }
            }
            if ($presetArr) {
                $presets = join("", $presetArr) . "********************************";
            }

            //菜单内容
            $foods = array();
            if ($food) {
                foreach ($food as $key => $value) {
                    $title = $value['title'];
                    if ($value['ntitle']) {
                        $title .= "（" . $value['ntitle'] . "）";
                    }
                    array_push($foods, $title . "\r                 ×<FB>" . $value['count'] . "</FB>     " . (sprintf('%.2f', $value['price'] * $value['count'])) . "\r................................");
                    // array_push($foods, "<tr><td>".$title."</td><td>*".$value['count']."</td><td>".(sprintf('%.2f', $value['price'] * $value['count']))."</td></tr>");
                }
            }
            $foods = join("\r", $foods);

            //费用详细
            $prices = "";
            $priceArr = array();
            if ($priceinfo) {
                array_push($priceArr, "<table><tr><td></td><td></td><td></td></tr>");
                foreach ($priceinfo as $key => $value) {
                    $oper = "";
                    if ($value['type'] == "youhui" || $value['type'] == "manjian" || $value['type'] == "shoudan") {
                        $oper = "-";
                    }
                    array_push($priceArr, "<tr><td>" . $value['body'] . "</td><td></td><td>" . $oper . $value['amount'] . "</td></tr>");
                }
                array_push($priceArr, "</table>");
            }
            if ($priceArr) {
                $prices = join("", $priceArr) . "\r********************************";
            }


            $noteText = !empty($note) ? "<FH><FW><FB>$note</FB></FW></FH>" . "\r********************************" : "";

//订单号
//时间
//地址
//姓名
//电话

//商品名称    数量     小计

//祝您购物愉快    完
            $content = "<FB><FH2><center>" . $shopname . $num . "</center></FH2></FB>
********************************
" . $langData['siteConfig'][19][308] . "：$ordernumstore
" . $langData['siteConfig'][19][384] . "：" . date("Y-m-d H:i:s", $pubdate) . "
" . $langData['siteConfig'][19][9] . "：$address
" . $langData['siteConfig'][19][4] . "：$person
" . $langData['siteConfig'][3][1] . "：$tel
********************************
$presets
" . $langData['siteConfig'][19][486] . "           " . $langData['siteConfig'][19][311] . "    " . $langData['siteConfig'][19][549] . "
********************************
$foods
$prices
$noteText
<FH2><FW>" . $amountInfo . echoCurrency(array("type" => "short")) . "</FW></FH2>
<center>" . $cfg_shortname . $langData['siteConfig'][21][19] . $num . ($num ? $langData['siteConfig'][21][18] : "") . "</center>";

            // $content = "<FB2><FH2><center>".$cfg_shortname.$num."</center></FH2></FB2>
            // ********************************
            // <FH>单号：$ordernum</FH>
            // <FH>时间：".date("Y-m-d H:i:s", $pubdate)."</FH>
            // <FH>地址：$address</FH>
            // <FH>姓名：$person</FH>
            // <FH>电话：$tel</FH>
            // ********************************
            // $presets
            // <table>
            // <tr><td>商品名</td><td>数量</td><td>小计</td></tr>
            // $foods
            // </table>
            // ********************************
            // $prices
            // <FH><FW>$note</FW></FH>
            // ********************************
            // <FH2><FW>已付款：".$amount."元</FW></FH2>
            //
            //
            // \r\n\r\n\r\n";
            //
            // echo $content;die;


//初始化日志
            require_once HUONIAOROOT . "/api/payment/log.php";
            $logHandler = new CLogFileHandler(HUONIAOROOT . '/api/waimaiPrint.log');
            $log = Log::Init($logHandler, 15);
            Log::DEBUG($pubdate . "\r" . $content . "\r\n\r\n");

            include(HUONIAOINC . '/config/waimai.inc.php');
            require_once(HUONIAOINC . '/class/waimaiPrint.class.php');

            $partner = $customPartnerId;
            $apikey = $customPrintKey;

            $print = new waimaiPrint();
            foreach ($print_config as $k => $v) {

                $mcode = $v['mcode'];
                $msign = $v['msign'];

                if ($partner && $apikey && $mcode && $msign && $content) {

                    $report = $print->action_print($partner, $mcode, $content, $apikey, $msign);
                    $report = json_decode($report, true);

                    //打印成功后更新订单打印接口id
                    // 更新打印记录状态
                    if ($report['state'] == 1) {

                        $time = GetMkTime(time());

                        $print_dataid = $report['id'];

                        $sql = $dsql->SetQuery("UPDATE `#@__waimai_order` SET `print_dataid` = '$print_dataid' WHERE `id` = $id");
                        $dsql->dsqlOper($sql, "update");

                    }

                }
            }


        }

    }
}


//打印商家点餐订单
function printBusinesDiancan($id){

    global $dsql;
    global $cfg_shortname;

    $sql = $dsql->SetQuery("SELECT o.*, s.`title` AS shopname, s.`bind_print`, s.`print_state`, s.`print_config` FROM `#@__business_diancan_order` o LEFT JOIN `#@__business_list` s ON s.`id` = o.`sid` WHERE o.`id` = '$id' AND o.`print_dataid` = ''");
    $ret = $dsql->dsqlOper($sql, "results");
    if ($ret) {
        $data = $ret[0];
        if (!$data['bind_print'] || !$data['print_state'] || empty($data['print_config']) || unserialize($data['print_config']) === false) {
            return;
        }


        $sid = $data['sid'];
        $shopname = $data['shopname'];
        $ordernum = $data['ordernum'];
        $table = $data['table'];
        $people = $data['people'];
        $food = unserialize($data['food']);
        $amount = $data['amount'];
        $priceinfo = unserialize($data['priceinfo']);
        $note = $data['note'];
        $pubdate = $data['pubdate'];
        $print_dataid = $data['print_dataid'];
        $print_config = unserialize($data['print_config']);


        //菜单内容
        $foods = array();
        if ($food) {
            foreach ($food as $key => $value) {
                $title = $value['title'];
                $title = $value['title'];
                if ($value['ntitle']) {
                    $title .= "（" . $value['ntitle'] . "）";
                }
                array_push($foods, $title . "\r                 ×<FB>" . $value['count'] . "</FB>     " . (sprintf('%.2f', $value['price'] * $value['count'])) . "\r................................");
            }
        }
        $foods = join("\r", $foods);

        //费用详细
        $prices = "";
        $priceArr = array();
        if ($priceinfo) {
            array_push($priceArr, "<table><tr><td></td><td></td><td></td></tr>");
            foreach ($priceinfo as $key => $value) {
                $oper = "";
                array_push($priceArr, "<tr><td>" . $value['body'] . "</td><td></td><td>" . $oper . $value['amount'] . "</td></tr>");
            }
            array_push($priceArr, "</table>");
        }
        if ($priceArr) {
            $prices = join("", $priceArr) . "\r********************************";
        }

        $noteText = !empty($note) ? "<FH><FW><FB>$note</FB></FW></FH>" . "\r********************************" : "";

    } else {
        return;
    }

//单号
//时间
//桌号
//人数
//商品名称    数量     小计
//合计
//祝您就餐愉快

    $content = "<FB><FH2><center>" . $shopname . "</center></FH2></FB>
********************************
" . $langData['siteConfig'][19][308] . "：$ordernum
" . $langData['siteConfig'][19][384] . "：" . date("Y-m-d H:i:s", $pubdate) . "
" . $langData['siteConfig'][16][73] . "：$table
" . $langData['siteConfig'][16][72] . "：$people
********************************
" . $langData['siteConfig'][19][486] . "           " . $langData['siteConfig'][19][311] . "    " . $langData['siteConfig'][19][549] . "
********************************
$foods
$prices
$noteText
<FH2><FW>" . $langData['siteConfig'][21][20] . "：" . $amount . echoCurrency(array("type" => "short")) . "</FW></FH2>
<center>" . $cfg_shortname . $langData['siteConfig'][21][21] . "</center>";

//初始化日志
    require_once HUONIAOROOT . "/api/payment/log.php";
    $logHandler = new CLogFileHandler(HUONIAOROOT . '/api/diancanPrint.log');
    $log = Log::Init($logHandler, 15);
    Log::DEBUG($pubdate . "\r" . $content . "\r\n\r\n");

    include(HUONIAOINC . '/config/business.inc.php');
    require_once(HUONIAOINC . '/class/waimaiPrint.class.php');

    $partner = $customPartnerId;
    $apikey = $customPrintKey;

    $print = new waimaiPrint();
    foreach ($print_config as $k => $v) {
        $mcode = $v['mcode'];
        $msign = $v['msign'];

        if ($partner && $apikey && $mcode && $msign && $content) {

            $report = $print->action_print($partner, $mcode, $content, $apikey, $msign);
            $report = json_decode($report, true);

            //打印成功后更新订单打印接口id
            // 更新打印记录状态
            if ($report['state'] == 1) {

                $print_dataid = $report['id'];

                $sql = $dsql->SetQuery("UPDATE `#@__business_diancan_order` SET `print_dataid` = '$print_dataid' WHERE `id` = $id");
                $dsql->dsqlOper($sql, "update");

            }

        }
    }

}


/**
 * 判断一个坐标是否在一个多边形内（由多个坐标围成的）
 * 基本思想是利用射线法，计算射线与多边形各边的交点，如果是偶数，则点在多边形外，否则
 * 在多边形内。还会考虑一些特殊情况，如点在多边形顶点上，点在多边形边上等特殊情况。
 * @param $point 指定点坐标
 * @param $pts 多边形坐标 顺时针方向
 */
function is_point_in_polygon($point, $pts){
    $N = count($pts);
    $boundOrVertex = true; //如果点位于多边形的顶点或边上，也算做点在多边形内，直接返回true
    $intersectCount = 0;//cross points count of x
    $precision = 2e-10; //浮点类型计算时候与0比较时候的容差
    $p1 = 0;//neighbour bound vertices
    $p2 = 0;
    $p = $point; //测试点

    $p1 = $pts[0];//left vertex
    for ($i = 1; $i <= $N; ++$i) {//check all rays
        // dump($p1);
        if ($p['lng'] == $p1['lng'] && $p['lat'] == $p1['lat']) {
            return $boundOrVertex;//p is an vertex
        }

        $p2 = $pts[$i % $N];//right vertex
        if ($p['lat'] < min($p1['lat'], $p2['lat']) || $p['lat'] > max($p1['lat'], $p2['lat'])) {//ray is outside of our interests
            $p1 = $p2;
            continue;//next ray left point
        }

        if ($p['lat'] > min($p1['lat'], $p2['lat']) && $p['lat'] < max($p1['lat'], $p2['lat'])) {//ray is crossing over by the algorithm (common part of)
            if ($p['lng'] <= max($p1['lng'], $p2['lng'])) {//x is before of ray
                if ($p1['lat'] == $p2['lat'] && $p['lng'] >= min($p1['lng'], $p2['lng'])) {//overlies on a horizontal ray
                    return $boundOrVertex;
                }

                if ($p1['lng'] == $p2['lng']) {//ray is vertical
                    if ($p1['lng'] == $p['lng']) {//overlies on a vertical ray
                        return $boundOrVertex;
                    } else {//before ray
                        ++$intersectCount;
                    }
                } else {//cross point on the left side
                    $xinters = ($p['lat'] - $p1['lat']) * ($p2['lng'] - $p1['lng']) / ($p2['lat'] - $p1['lat']) + $p1['lng'];//cross point of lng
                    if (abs($p['lng'] - $xinters) < $precision) {//overlies on a ray
                        return $boundOrVertex;
                    }

                    if ($p['lng'] < $xinters) {//before ray
                        ++$intersectCount;
                    }
                }
            }
        } else {//special case when ray is crossing through the vertex
            if ($p['lat'] == $p2['lat'] && $p['lng'] <= $p2['lng']) {//p crossing over p2
                $p3 = $pts[($i + 1) % $N]; //next vertex
                if ($p['lat'] >= min($p1['lat'], $p3['lat']) && $p['lat'] <= max($p1['lat'], $p3['lat'])) { //p.lat lies between p1.lat & p3.lat
                    ++$intersectCount;
                } else {
                    $intersectCount += 2;
                }
            }
        }
        $p1 = $p2;//next ray left point
    }

    if ($intersectCount % 2 == 0) {//偶数在多边形外
        return false;
    } else { //奇数在多边形内
        return true;
    }
}

//验证多少天连续
function getContinueDay($day_list){
    //昨天开始时间戳
    $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));

    $day_list0 = GetMkTime(date("Y-m-d", $day_list[0]));

    $days = 1;

    //如果今天没有签到
    if ($beginToday == $day_list0) {
        $days = 1;
    } else {
        if ($beginYesterday != $day_list0) {
            if ($beginToday == $day_list0) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    $count = count($day_list);
    for ($i = 0; $i < $count; $i++) {
        if ($i < $count - 1) {
            $res = compareDay($day_list[$i], $day_list[$i + 1]);
            if ($res) {
                $days++;
            } else {
                break;
            }
        }
    }
    return $days;
}

function compareDay($curDay, $nextDay){
    $lastBegin = mktime(0, 0, 0, date('m', $curDay), date('d', $curDay) - 1, date('Y', $curDay));
    $lastEnd = mktime(0, 0, 0, date('m', $curDay), date('d', $curDay), date('Y', $curDay)) - 1;
    if ($nextDay >= $lastBegin && $nextDay <= $lastEnd) {
        return true;
    } else {
        return false;
    }
}


//根据城市ID，获取分站城市名
function getSiteCityName($cid){
  global $dsql;
  $sql = $dsql->SetQuery("SELECT a.`typename` FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` WHERE a.`id` = " . $cid);
  $ret = $dsql->dsqlOper($sql, "results");
  if($ret){
    return $ret[0]['typename'];
  }else{
    return '未知';
  }
}

//获取模块数据
function getModuleList($showIndex = false){
    global $HN_memory;
	global $dsql;

	//读缓存
    $module_cache = $HN_memory->get('site_module');
    if($module_cache){

        if ($showIndex) {
            array_unshift($module_cache, array(
                "title" => "首页",
                "name" => "index"
            ));
        }

        return $module_cache;
    }else {

        $sql = $dsql->SetQuery("SELECT `title`, `subject`, `name` FROM `#@__site_module` WHERE `parentid` != 0 AND `state` = 0 AND `type` = 0 ORDER BY `weight`, `id`");
        try {
            $result = $dsql->dsqlOper($sql, "results");

            $i = 0;
            if ($result) {//如果有子类

                foreach ($result as $key => $value) {
                    $results[$i]["title"] = $value['subject'] ? $value['subject'] : $value['title'];
                    $results[$i]["name"] = $value['name'];
                    $i++;
                }

                //写缓存
                $HN_memory->set('site_module', $results);

                if ($showIndex) {
                    array_unshift($results, array(
                        "title" => "首页",
                        "name" => "index"
                    ));
                }

                return $results;
            } else {
                return "";
            }

        } catch (Exception $e) {
            //die("模块数据获取失败！");
        }
    }
}


//系统入口城市域名检测
function checkSiteCity(){
  global $service;
  global $template;
  global $city;
  global $cfg_secureAccess;
  global $cfg_basehost;
  global $cityDomainType;

  $spider = isSpider();
  if($spider && $service == 'siteConfig' && $template == 'index' && empty($city) && (!$cityDomainType || $cityDomainType == 2)){
    global $cfg_spiderIndex;

    //切换城市页
    if(!$cfg_spiderIndex){
      header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?spider=' . $spider);
      die;
    }
  }

  //定位所在城市
  if(((($template == 'index' || $template == 'appindex') && $service == 'siteConfig') || $service != 'siteConfig') && $service != 'member' && $template != 'changecity' && !strstr($_SERVER['PHP_SELF'], 'ajax.php')){

  	include_once(HUONIAOROOT.'/api/handlers/siteConfig.class.php');

  	//已经传了城市，需要验证传过来的城市信息是否合法
  	if($city){

  		$siteConfigService = new siteConfig($city);
  		$cityDomain = $siteConfigService->verifyCityDomain();
  		if(is_array($cityDomain)){

  			//验证失败
  			if($cityDomain['state'] == 200){

          $singelCityInfo = checkDefaultCity();
          if($singelCityInfo){
            header('location:' . $singelCityInfo['url']);
            die;
          }else{
    				header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?state=1');
    				die;
          }

  			//系统暂未开通分站功能！
  			}elseif($cityDomain['state'] == 201){

          $singelCityInfo = checkDefaultCity();
          if($singelCityInfo){
            header('location:' . $singelCityInfo['url']);
            die;
          }else{
            header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?state=2');
            die;
          }

        //验证成功
        }else{
          PutCookie('siteCityInfo', json_encode($cityDomain), 86400 * 7, "/", $cityDomain['type'] == 0 ? $city : "");
          return $cityDomain;
        }

  		//获取失败
  		}else{

        $singelCityInfo = checkDefaultCity();
        if($singelCityInfo){
          header('location:' . $singelCityInfo['url']);
          die;
        }else{
          header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?state=3');
          die;
        }

  		}

    //没有传任何城市信息，等于刚进来，需要走IP自动定位功能
  	}else{

      //验证Cookie
      $siteCityInfoCookie = GetCookie('siteCityInfo');
      if($siteCityInfoCookie){
        $siteCityInfoJson = json_decode($siteCityInfoCookie, true);
        if(is_array($siteCityInfoJson)){

          //验证Cookie中保存的城市信息是否存在，主要考虑到如果把城市信息删除后，访问过这个城市的用户会跳到404
          $siteConfigService = new siteConfig($siteCityInfoJson['domain']);
      		$cityDomain = $siteConfigService->verifyCityDomain();
      		if(is_array($cityDomain)){

      			//验证失败
      			if($cityDomain['state'] == 200){

              $singelCityInfo = checkDefaultCity();
              if($singelCityInfo){
                header('location:' . $singelCityInfo['url']);
                die;
              }else{
        				header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?state=4');
        				die;
              }

      			//系统暂未开通分站功能！
      			}elseif($cityDomain['state'] == 201){

              $singelCityInfo = checkDefaultCity();
              if($singelCityInfo){
                header('location:' . $singelCityInfo['url']);
                die;
              }else{
        				header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?state=5');
        				die;
              }

            //验证成功
            }else{
              PutCookie('siteCityInfo', json_encode($cityDomain), 86400 * 7, "/", $cityDomain['type'] == 0 ? $city : "");
              return $cityDomain;
              header('location:'.$siteCityInfoJson['url']);
              die;
            }

      		//获取失败
      		}else{

            $singelCityInfo = checkDefaultCity();
            if($singelCityInfo){
              header('location:' . $singelCityInfo['url']);
              die;
            }else{
      				header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?state=6');
      				die;
            }

      		}


        }else{

          $singelCityInfo = checkDefaultCity();
          if($singelCityInfo){
            header('location:' . $singelCityInfo['url']);
            die;
          }else{
    				header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?state=7');
    				die;
          }

        }

      //IP定位
      }else{

        //验证是否搜索引擎抓取
        $spider = isSpider();
        if($spider){
          global $cfg_spiderIndex;

          //切换城市页
          if(!$cfg_spiderIndex){
            header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?spider=' . $spider);
            die;
          }
        }

        //如果只开通了一个分站，直接使用这个分站数据
        $siteConfigService = new siteConfig();
        $cityDomain = $siteConfigService->siteCity();
        if(count($cityDomain) == 1){
          $singelCityInfo = $cityDomain[0];
          PutCookie('siteCityInfo', json_encode($singelCityInfo), 86400 * 7, "/", $singelCityInfo['type'] == 0 ? $city : "");
          return $singelCityInfo;
        }

    		//当前城市
    		$cityData = getIpAddr(getIP(), 'json');
    		if($cityData){
    			$siteConfigService = new siteConfig(array(
    				'province' => $cityData['region'],
    				'city' => $cityData['city']
    			));
    			$cityInfo = $siteConfigService->verifyCity();
    			if(is_array($cityInfo)){

    				//您所在的地区暂未开通分站
    				if($cityInfo['state'] == 200){

              $singelCityInfo = checkDefaultCity();
              if($singelCityInfo){
                PutCookie('siteCityInfo', json_encode($singelCityInfo), 86400 * 7, "/", $singelCityInfo['type'] == 0 ? $city : "");
                return $singelCityInfo;
                // header('location:' . $singelCityInfo['url']);

              }else{
        				header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?state=8');
                die;
              }

    				//系统暂未添加分站城市
    				}elseif($cityInfo['state'] == 201){

    				}else{
              //获取成功
              PutCookie('siteCityInfo', json_encode($cityInfo), 86400 * 7, "/", $cityInfo['type'] == 0 ? $city : "");
    					header('location:'.$cityInfo['url']);
    					die;
            }

    			//识别失败，手动选择分站
    			}else{

            $singelCityInfo = checkDefaultCity();
            if($singelCityInfo){
              header('location:' . $singelCityInfo['url']);
              die;
            }else{
      				header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?state=9');
      				die;
            }

    			}

    		//IP获取失败，手动选择分站
    		}else{

          $singelCityInfo = checkDefaultCity();
          if($singelCityInfo){
            header('location:' . $singelCityInfo['url']);
            die;
          }else{
    				header('location:'.$cfg_secureAccess.$cfg_basehost.'/changecity.html?state=10');
    				die;
          }

    		}

      }

  	}

  }else{
    $siteCityInfoCookie = GetCookie('siteCityInfo');
    if($siteCityInfoCookie){
      $siteCityInfoJson = json_decode($siteCityInfoCookie, true);
      if(is_array($siteCityInfoJson)){
        return $siteCityInfoJson;
      }
    }
  }

}


//判断是否为单城市或是否设置了默认城市，如果条件成立，则返回城市信息
function checkDefaultCity(){
  $siteConfigService = new siteConfig();
  $cityDomain = $siteConfigService->siteCity();
  if(count($cityDomain) == 1){
    return $cityDomain[0];
  }else{
    $cityData = array();
    foreach ($cityDomain as $key => $value) {
      if($value['default']){
        $cityData = $value;
      }
    }
    return $cityData;
  }
}



//根据模块名及域名类型拼接完整url
function getDomainFullUrl($module, $customSubDomain, $customModule = array()){
  global $cfg_secureAccess;
  global $cfg_basehost;
  global $siteCityInfo;
  global $dsql;
  global $HN_memory;
  global $_G;

  $siteCityInfo_ = $customModule ? $customModule : $siteCityInfo;

  //如果只开通了一个模块，直接使用系统域名

  //读缓存
  if(isset($_G['site_module_count'])){
    $siteModule = $_G['site_module_count'];
  }else{
      $moduleCountCache = $HN_memory->get('site_module_count');
      if($moduleCountCache){
          $siteModule = $moduleCountCache;
      }else {
          $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__site_module` WHERE `state` = 0 AND `type` = 0 AND `name` != ''");
          $ret = $dsql->dsqlOper($sql, "results");
          if($ret){
              $siteModule = $ret[0]['total'];

              //写缓存
              $HN_memory->set('site_module_count', $siteModule);
          }
      }
      $_G['site_module_count'] = $siteModule;
    }
  $domainInfo = getDomain($module, 'config');
  $domain = $domainInfo['domain'];

  //如果有分站
  if($siteCityInfo_ && is_array($siteCityInfo_) && $siteCityInfo_['count'] > 1 && $module != "website"){

    $cityUrl    = $siteCityInfo_['url'];
    $cityDomain = $siteCityInfo_['domain'];
    $cityType   = $siteCityInfo_['type'];

    //子域名
    if($customSubDomain == 1){

      //如果分站绑定了独立域名，模块如果绑定二级域名，最终将是：article.suzhou.com
      if($cityType == 0){
        $domain = $cfg_secureAccess.$domain.".".str_replace("www.", "", $cityDomain);

      //如果分站绑定了子域名，模块如果绑定二级域名，最终将是：article.suzhou.ihuoniao.cn
      }elseif($cityType == 1){
        $domain = $cfg_secureAccess.$domain.".".str_replace("www.", "", $cityDomain).".".$cfg_basehost;

      //如果分站绑定了子目录，模块如果绑定二级域名，最终将是：ihuoniao.cn/suzhou/article
      }elseif($cityType == 2){
        $domain = $cityUrl . ($siteModule == 1 && $module != "business" ? "" : "/".$domain);
      }

    //子目录
    }elseif($customSubDomain == 2 && $cityType == 2){
      $domain = $cityUrl . ($siteModule == 1 && $module != "business" ? "" : "/".$domain);

    //主域名及其他
    }else{
      $domain = $cityUrl . ($siteModule == 1 && $module != "business" ? "" : "/".$domain);
    }

  }else{
    if($siteModule == 1 && $module != 'business'){
      return $cfg_secureAccess.$cfg_basehost;
    }else{
      if($customSubDomain == 0){
        $domain = $cfg_secureAccess.$domain;
      }elseif($customSubDomain == 1){
        $domain = $cfg_secureAccess.$domain.".".str_replace("www.", "", $cfg_basehost);
      }elseif($customSubDomain == 2){
        $domain = $cfg_secureAccess.$cfg_basehost."/".$domain;
      }
    }
  }
  return $domain;
}

// 接口获取城市id
function getCityId($cid = 0){
    if(empty($cid)){
        $siteCityInfoCookie = GetCookie('siteCityInfo');
        if($siteCityInfoCookie){
            $siteCityInfoJson = json_decode($siteCityInfoCookie, true);
            if(is_array($siteCityInfoJson)){
              $siteCityInfo = $siteCityInfoJson;
              $cid = $siteCityInfo['cityid'];
            }
        }else{
            $url = $_SERVER['HTTP_REFERER'];
            $data = "dopost=getSiteCityInfo";
            $curl = curl_init();
            curl_setopt($curl,CURLOPT_URL,$url);
            curl_setopt($curl,CURLOPT_HEADER,0);
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, FALSE);
        		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl,CURLOPT_POST,1);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
            $cid = curl_exec($curl);
            curl_close($curl);
        }
    }
    return (int)$cid;
}

// 详情页判断城市
function detailCheckCity($service, $id, $cityid, $template = "detail"){
    $cid = getCityId();
    global $siteCityInfo;
    global $cfg_secureAccess;
    global $do;
    global $service;

    if($do == 'edit' || $service == 'member') return;

    $site = new siteConfig();
    $arr = $site->siteCity();
    $count = count($arr);
    if($count == 1){
        return;
    }

    $incFile = HUONIAOINC . "/config/" . $service . ".inc.php";
    require $incFile;

    $url = $siteCityInfo['url'];
    if(substr($url, 0, 4) == 'www.') {
        $url = substr($url, 4);
    }

    $url = str_replace('http://', '', str_replace('https://', '', $url));
    $currUrl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if($cid && $cityid && ($cityid != $cid || ($customSubDomain != 0 && !strstr($currUrl, $url)))){
        global $siteCityInfo;
        global $city;
        global $dsql;
        global $data;

        $sql = $dsql->SetQuery("SELECT d.`domain` FROM `#@__domain` d LEFT JOIN `#@__site_city` c ON c.`cid` = d.`iid` WHERE d.`module` = 'siteConfig' AND d.`part` = 'city' AND c.`cid` = $cityid");
        $ret = $dsql->dsqlOper($sql, "results");
        if(!$ret) return false;
        $city = $ret[0]['domain'];
        $siteCityInfo = checkSiteCity();
        $url = getUrlPath(array("service" => $service, "template" => $template, "id" => $id));

        header("location:".$url);
        die;
    }
}

// 重置template,获取参数
function checkPagePath(&$service, $pagePath, $reqUri){
    global $dsql;
    global $cityDomainType;
    global $huoniaoTag;
    global $installModuleArr;
    $arr = array_filter($installModuleArr);
    $moduleCount = count($arr);
    $sql = $dsql->SetQuery("SELECT * FROM `#@__domain` WHERE `domain` = '$service'");
    $results = $dsql->dsqlOper($sql, "results");
    if($results){
        $module     = $results[0]['module'];
        $expires    = $results[0]['expires'];
        $note       = $results[0]['note'];
        //判断是否过期
        if($todayDate < $expires || empty($expires)){
            $service = $module;
        }else{
            die($note);
        }
    }


    if($service == "website"){
        $reqUri_ = trim($reqUri, "/");
        $reqUriArr = explode("/", $reqUri_);

        if($cityDomainType == 3){
            array_unshift($reqUriArr, $service);
        }
        if(isset($reqUriArr[1])){

            if(strstr($reqUriArr[1], ".html")){
                $pagePathArr = explode('-', $pagePath);
                $pagePath = $pagePathArr[0];
                if($pagePath == "templates"){
                    $_REQUEST['typeid'] = $pagePathArr[1];
                }elseif($pagePath == "mobile"){
                    $pagePath = "mobile-market";
                }
            }elseif(strstr($reqUriArr[1], "preview") || strstr($reqUriArr[1], "site")){
                global $id;
                global $alias;
                global $type;

                $id = str_replace("preview", "", $reqUriArr[1]);
                $id = (int)str_replace("site", "", $id);

                if(isset($reqUriArr[2])){
                    $req = explode('?', $reqUriArr[2]);
                    $req = $req[0];
                    $alias = str_replace(".html", "", $req);
                }else{
                    $alias = "index";
                }

                if(strstr($reqUriArr[1], "preview")){
                    $type = "template";
                }

                if(!isMobile()){
                    include("website.php");
                    die;
                }else{
                    return $alias;
                }
            }
        }

        if($moduleCount == 1 &&
            (strstr($pagePath, "about-")
                || $pagePath == "about"
                || strstr($pagePath, "help-")
                || $pagePath == "help"
                || $pagePath == "404"
            )
        ){
        }else{
            return $pagePath;
        }

    // 专题
    }elseif($service == "special"){
        $pagePathArr = explode('-', $pagePath);
        $pagePath = $pagePathArr[0];
        if($pagePath == "detail"){
            global $id;
            $id = $pagePathArr[1];

            include("special.php");
            die;
        }else{
            if(isset($pagePathArr[1])){
                $_REQUEST['id'] = $pagePathArr[1];
            }
            return $pagePath;
        }
    }


    global $cityDomainType;

    if($pagePath == "index" || $pagePath == "404") return $pagePath;

    if($service == "member"){
        $reqUri_ = trim($reqUri, "/");

        $reqUri_ = explode('?', $reqUri_);
        $reqUri_ = $reqUri_[0];
        $reqUriArr = explode("/", $reqUri_);

        // print_r($reqUriArr);die;

        foreach ($reqUriArr as $key => $value) {
            if($reqUriArr[$key] == "user"){
                $_REQUEST['id'] = $reqUriArr[$key+1];
                if(empty($reqUriArr[$key+2])){
                    return "user";
                }else{
                    return "user_".str_replace('.html','',$reqUriArr[$key+2]);
                }
            }
            if(stripos($reqUriArr[$key], 'config-house') !== false){
              $param = array("service"  => "member");
          		$busiDomain = getUrlPath($param);     //商家会员域名
              global $cfg_secureAccess;
              global $httpHost;
              $fullUri = $cfg_secureAccess . $httpHost . $reqUri;
              if(!strstr($fullUri, $busiDomain)){
                return str_replace('.html', '', $reqUriArr[$key]);
              }
            }
            if(stripos($reqUriArr[$key], 'config-selfmedia') !== false){
              return str_replace(".html", "", $reqUriArr[$key]);
            }

            if(strstr($reqUriArr[$key], 'write-comment')){

              $pathArr_wc = explode('-', $reqUriArr[$key]);
              $_REQUEST['module'] = $pathArr_wc[$key+1];
              $_REQUEST['id'] = str_replace('.html', '', $pathArr_wc[$key+2]);
              return 'write-comment';
            }

            if($reqUriArr[$key] == 'config-tuan.html'){
              $_REQUEST['template'] = 'config';
              $_REQUEST['module'] = 'tuan';
              return "config";
            }

            if(stripos($reqUriArr[$key], 'config-car') !== false){
                $param = array("service"  => "member");
                    $busiDomain = getUrlPath($param);     //商家会员域名
                global $cfg_secureAccess;
                global $httpHost;
                $fullUri = $cfg_secureAccess . $httpHost . $reqUri;
                if(!strstr($fullUri, $busiDomain)){
                    return str_replace('.html', '', $reqUriArr[$key]);
                }
            }

            // if(($reqUriArr[$key] == 'dressup-website.html' || $reqUriArr[$key] == 'website-news.html' || $reqUriArr[$key] == 'website-guest.html' || $reqUriArr[$key] == 'website-honor.html' || $reqUriArr[$key] == 'alipay-record.html')){
            //   return str_replace('.html', '', $reqUriArr[$key]);
            // }
            if(($reqUriArr[$key] == 'dressup-website.html' || (strpos($reqUriArr[$key], 'fabu') === false && strpos($reqUriArr[$key], 'website-') !== false) || $reqUriArr[$key] == 'alipay-record.html')){
                $f = explode('?', $reqUriArr[$key]);
                $f = $f[0];
                return str_replace('.html', '', $f);
            }
        }



    }


    $isSpecial = false; // 特殊页面
    //默认URL规则
    if(strstr($pagePath, '-')){

        $pagePathArr = explode('-', $pagePath);
        $pagePath = $pagePathArr[0];

        //列表
        if(($pagePath == 'list' || $pagePath == 'slist') && count($pagePathArr) == 2){
            $typeid = $pagePathArr[1];
            $_REQUEST['typeid'] = $typeid;
            $_REQUEST['id'] = $typeid;

        //详情
        }elseif($pagePath == 'detail' && count($pagePathArr) == 2){
            $id = $pagePathArr[1];
            $_REQUEST['id'] = $id;

        // 支付返回
        }elseif($pagePath == 'payreturn'){
            $_REQUEST['ordernum'] = $pagePathArr[1];

        // 评论
        }elseif($pagePath == 'comment'){
            $_REQUEST['id'] = $pagePathArr[1];

        }else{
            $isSpecial = true;
        }

    }else{

        // 可以自定义URL规则的模块
        if($service == "image" || $service == "article"){
            $isSpecial = true;
        }
        // $isSpecial = true;
        // $pagePathArr = array($pagePath);
    }

    if(!$isSpecial) return $pagePath;

    $fields = array();
    $data = array();
    $fields = array();
    $dataArr = array();

    // if($service == "siteConfig"){
        if($pagePath == "help" || $pagePath == "notice"){
            if(isset($pagePathArr[1])){
                if(is_numeric($pagePathArr[1])){
                    $_REQUEST['id'] = $pagePathArr[1];
                }else{
                    $pagePath = $pagePath.'-detail';
                    $_REQUEST['id'] = $pagePathArr[2];
                }
            }
        }elseif($pagePath == "about" || $pagePath == "protocol"){
            $_REQUEST['id'] = $pagePathArr[1];
        }elseif($pagePath == "complain"){
            $_REQUEST['module'] = $pagePathArr[1];
            $_REQUEST['dopost'] = $pagePathArr[2];
            $_REQUEST['aid'] = $pagePathArr[3];
        }elseif($pagePathArr[0] == 'search' && $pagePathArr[1] == 'list'){
          $pagePath = 'search-list';
        }
    // }

    // print_r($_REQUEST);
    if($service == "member"){
        // module
        if($pagePath == "order" || $pagePath == "collect" || $pagePath == "job" || $pagePath == "category" || $pagePath == "team" || $pagePath == "teamAdd" || $pagePath == "albums" || $pagePath == "albumsAdd" || $pagePath == "case" || $pagePath == "caseAdd" || $pagePath == "booking" || $pagePath == "resume" || $pagePath == "invitation" || $pagePath == "collections"){

            $_REQUEST['module'] = $pagePathArr[1];

        // id
        }elseif($pagePath == "message_detail" || $pagePath == "withdraw_log_detail" || $pagePath == "user"){

            $_REQUEST['id'] = $pagePathArr[1];

        // module,id
        }elseif($pagePath == "orderdetail" || $pagePath == "logistic" || $pagePath == "write-comment"){

            if($pagePathArr[1] == 'business'){
              $_REQUEST['module'] = $pagePathArr[1];
              $_REQUEST['type'] = $pagePathArr[2];
              $_REQUEST['id'] = $pagePathArr[3];
            }else{
              $_REQUEST['module'] = $pagePathArr[1];
              $_REQUEST['id'] = isset($pagePathArr[2]) ? $pagePathArr[2] : 0;
            }

        }elseif($pagePath == "config"){

            if($domainPart == "user"){
                $pagePath .= "-".$pagePathArr[1];
            }
            $mdf = explode('_', $pagePathArr[1]);
            // 不改变 controller中action的值，根据pagePath_ 重新指定模板
            if(count($mdf) > 1){
                $huoniaoTag->assign('pagePath_', "config-".$pagePathArr[1]);
            }
            $_REQUEST['module'] = $mdf[0];

        // add file
        }elseif($pagePath == "car" || $pagePath == "dating" || $pagePath == "huodong" || $pagePath == "verify" || $pagePath == "quan" || $pagePath == "house" || $pagePath == "checkout" || $pagePath == "payment-success" || $pagePath == "alipay-record" || strstr($pagePath, "enter") || strstr($pagePath, "select") || strstr($pagePath, "confirm") || strstr($pagePath, "upgrade") || $pagePath == "quan-tuan" || $pagePath == "select-module" || $pagePath == "confirm-order" || $pagePath == "payment-success" || $pagePath == "vote"){

            // 横线连接的不是数字则认为是文件名的一部分
            $pagePath = "";
            foreach ($pagePathArr as $v) {
                if(!is_numeric($v)){
                    $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
                }else{
                    $_REQUEST['id'] = $v;
                }
            }
            // $pagePath .= isset($pagePathArr[1]) ? "-".$pagePathArr[1] : "";

        // file,type
        }elseif($pagePath == "business"){

            if($pagePathArr[1] == "diancan" || $pagePathArr[1] == "dingzuo" || $pagePathArr[1] == "paidui" || $pagePathArr[1] == "maidan"){
                $pagePath .= "-service".(isset($pagePathArr[2]) ? "-".$pagePathArr[2] : "");
                $_REQUEST['type'] = $pagePathArr[1];
            }else{
                $pagePath .= "-".$pagePathArr[1];
            }

        // module,typeid,type
        }elseif($pagePath == "manage" || $pagePath == "fabu" || $pagePath == "statistics"){

            $_REQUEST['module'] = $pagePathArr[1];
            if(isset($pagePathArr[2])){
                if(is_numeric($pagePathArr[2])){
                    $_REQUEST['typeid'] = $pagePathArr[2];
                }else{
                    $_REQUEST['type'] = $pagePathArr[2];
                }
            }

        }elseif($pagePath == "security"){

            $_REQUEST['doget'] = $pagePathArr[1];

        }elseif($pagePath == "complain"){
            $_REQUEST['module'] = $pagePathArr[1];
            $_REQUEST['dopost'] = $pagePathArr[2];
            $_REQUEST['aid'] = $pagePathArr[3];
        }elseif($pagePath == "housem"){
            $_REQUEST['type'] = $pagePathArr[1];

        }elseif($pagePath == "homemaking"){
            $pagePath .= "-".$pagePathArr[1];
            $_REQUEST['id'] = isset($pagePathArr[2]) ? $pagePathArr[2] : '';
        }elseif($pagePath == "marry"){
            $pagePath .= "-".$pagePathArr[1];
            $_REQUEST['id'] = isset($pagePathArr[2]) ? $pagePathArr[2] : '';

        // module,id,type
        }elseif($pagePath == "fabusuccess"){
            $_REQUEST['module'] = $pagePathArr[1];
            if(isset($pagePathArr[2])){
                if(is_numeric($pagePathArr[2])){
                    $_REQUEST['id'] = $pagePathArr[2];
                }else{
                    $_REQUEST['type'] = $pagePathArr[2];
                }
            }
        }elseif($pagePath == "travel" || $pagePath == "education" || $pagePath == "pension"){
            $pagePath .= "-".$pagePathArr[1];
            $_REQUEST['id'] = isset($pagePathArr[2]) ? $pagePathArr[2] : '';
        }
    }

    //新闻资讯
    if($service == 'article'){

        include_once(HUONIAOINC . '/config/'.$service.'.inc.php');

        //自定义URL规则
        if($pagePath != "pay" && !strstr($pagePath, '-')){

            //根据reqUri判断是否为详细页
            //reqUri包含.html则代表详细页   比如：1.html
            //这里可能是媒体列表和详情页、头条、图片等新闻类型
            if(strstr($reqUri, '.html')){

                if(is_numeric($pagePath)){
                    $_REQUEST['id'] = $pagePath;
                    $pagePath = 'detail';
                }else{
                    $reqUri_ = basename($reqUri, ".html");
                    $reqUri_ = explode("-", $reqUri_);
                    if(isset($reqUri_[1])){
                        $_REQUEST['id'] = $reqUri_[1];
                    }
                }

            //其他情况为列表页，包含分类全拼和简拼
            }else{
                //全拼
                if($custom_listRule == 1){
                    $_REQUEST['pinyin'] = $pagePath;
                    $pagePath = 'list';

                //简拼
                }elseif($custom_listRule == 2){
                    $_REQUEST['py'] = $pagePath;
                    $pagePath = 'list';
                }
            }
        }

        if(strstr($reqUri, 'search.html')){
          $pagePath = 'search';
        }

    //图说新闻
    }elseif($service == 'image'){
        include_once(HUONIAOINC . '/config/'.$service.'.inc.php');
        //自定义URL规则
        if(!strstr($pagePath, '-')){

            //根据reqUri判断是否为详细页
            //reqUri包含.html则代表详细页   比如：1.html
            if(strstr($reqUri, '.html')){
                $_REQUEST['id'] = $pagePath;
                $pagePath = 'detail';

            //其他情况为列表页，包含分类全拼和简拼
            }else{
                //全拼
                if($custom_listRule == 1){
                    $_REQUEST['pinyin'] = $pagePath;
                    $pagePath = 'list';

                //简拼
                }elseif($custom_listRule == 2){
                    $_REQUEST['py'] = $pagePath;
                    $pagePath = 'list';
                }

            }

        }

        if(strstr($reqUri, 'search.html')){
            $pagePath = 'search';
        }

    // 分类信息
    }elseif($service == 'info'){
         if($pagePathArr[0] == "store"){
            $_REQUEST['id'] = $pagePathArr[1];
         }elseif ($pagePathArr[0] == "homepage"){
             $_REQUEST['id'] = $pagePathArr[1];
         }elseif ($pagePathArr[0] == "business"){
             $_REQUEST['id'] = $pagePathArr[1];
         }elseif ($pagePathArr[0] == "confirm"){
             $_REQUEST['id'] = $pagePathArr[1];
         }elseif ($pagePathArr[0] == "pay"){
             $_REQUEST['ordernum'] = $pagePathArr[1];
         }elseif ($pagePathArr[0] == "store_list"){
             $_REQUEST['list_id'] = $pagePathArr[1];
             $_REQUEST['addr_id'] = $pagePathArr[2];
         }elseif ($pagePathArr[0] == "category"){
            $_REQUEST['typeid'] = $pagePathArr[1];
         }elseif ($pagePathArr[0] == "comdetail"){
            $_REQUEST['id'] = $pagePathArr[1];
         }
    // 团
    }elseif($service == 'tuan'){
        if($pagePath == 'list' || $pagePath == 'pintuan' || $pagePath == 'haodian' || $pagePath == 'voucher'){
            for($s = 1; $s <= count($pagePathArr); $s++){
                $dataArr[] = $pagePathArr[$s];
            }
            $fields = array("typeid", "addrid", "business", "subway", "station", "circle");
        }elseif($pagePath == 'tdetail' || $pagePath == 'ptdetail' || $pagePath == 'detail' || $pagePath == 'pic' || $pagePath == 'review'){
            $_REQUEST['id'] = $pagePathArr[1];
        }elseif($pagePath == 'new'){
            $_REQUEST['typeid'] = isset($pagePathArr[1]) ? $pagePathArr[1] : 0;
        }elseif($pagePath == 'store'){
            $_REQUEST['uid'] = $pagePathArr[1];
        }elseif($pagePath == 'buy'){
            $_REQUEST['id'] = $pagePathArr[1];
            $_REQUEST['count'] = $pagePathArr[2];
        }elseif($pagePath == 'sqdetail' || $pagePath == 'dindan' || $pagePath == 'storereview' || $pagePath == 'storecommon'){
            $_REQUEST['id'] = $pagePathArr[1];
        }elseif($pagePath == 'shangquan'){
            for($s = 1; $s <= count($pagePathArr); $s++){
                $dataArr[] = $pagePathArr[$s];
            }
            $fields = array("typeid", "addrid", "circle");
        }
    // 房产
    }elseif($service == 'house'){
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        $fields = array("id", "aid", "page");

        if($pagePath == "faq"){
            $fields = array("typeid");
        }elseif($pagePath == "faq-detail"){
            $fields = array("id");
        }elseif($pagePath == "broker"){
            $fields = array("addrid", "business", "page");
        }elseif($pagePath == "broker-detail"){
            $fields = array("id", "tpl", "page");
        }elseif($pagePath == "loupan-news"){
            $fields = array("id", "page");
        }elseif($pagePath == "news" || $pagePath == "news-list"){
            $fields = array("typeid", "page");
        }elseif(strstr($pagePath, "store-detail")){
            $fields = array("id");
            if($pagePath != "store-detail"){
                $data = array();
                for($s = 4; $s < count($pagePathArr); $s++){
                    $data[] = $pagePathArr[$s];
                }
                $pagePath = "store-detail";
                $_REQUEST['tpl'] = $pagePathArr[2];
            }
        }elseif(strstr($pagePath, "calculator") || strstr($pagePath, "map")){
            $pagePath = $pagePathArr[0];
            $fields = array("do");
            $dataArr = array($pagePathArr[1]);
        }elseif($pagePath == "xzl" || $pagePath == "cf"){
            $fields = array("type", "addrid", "business","area","price","protype","usertype","keywords","page");
        }elseif($pagePath == "sp"){
            $fields = array("type", "addrid", "business","area","price","protype","industry","usertype","keywords","page");
        }elseif($pagePath == "cw"){
            $fields = array("type", "addrid", "business","area","price","usertype","keywords","page");
        }else{
            if($pagePathArr[1] == "album" || $pagePathArr[1] == "album"){
                if($pagePathArr[2] != "detail"){
                    $fields = array("id", $pagePathArr[1]);
                }
            }elseif($pagePathArr[1] == "hx"){
                if($pagePathArr[2] != "detail"){
                    $fields = array("id", "room");
                }else{
                    $fields = array("id", "aid");
                }
            }elseif($pagePathArr[1] == "sale" || $pagePathArr[1] == "zu"){
                $fields = array("id", "page");

            }elseif($pagePathArr[1] == "broker"){
                if($pagePathArr[2] != "detail"){
                    $fields = array("addrid", "business", "page");
                }else{
                    $fields = array("id", "tpl", "page", "keywords");
                }
            }
        }
        if(!strstr($pagePath, "-") && $i >= 1){
            $data = $dataArr;
        }
    // 商城
    }elseif($service == 'shop'){
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        if($pagePathArr[0] == "brand"){
            if($pagePathArr[1] != "detail"){
                $fields = array("typeid", "page");
            }else{
                $fields = array("id", "typeid", "page");
            }
        }elseif($pagePathArr[0] == "store"){
            if($pagePathArr[1] != "detail"){
                $fields = array("typeid", "addrid", "business", "page");
                if($pagePathArr[1] == "category"){
					$fields = array("id", "typeid", "addrid", "business", "page");
                }
            }else{
                $fields = array("id", "typeid", "page");
            }
        }elseif($pagePathArr[0] == "news"){
            if($pagePathArr[1] != "detail"){
                $fields = array("typeid", "page");
            }else{
                $fields = array("id", "typeid", "page");
            }
        }
    // 装修
    }elseif($service == 'renovation'){
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        $fields = array("id", "page");
        if($pagePathArr[0] == "zb"){
            if($pagePathArr[1] != "detail"){
                $fields = array("page");
            }
        }

        if($pagePathArr[0] == 'raiders' && is_numeric($pagePathArr[1])){
          $pagePath = 'raiders-list';
        }elseif($pagePathArr[0] == 'raiders' && $pagePathArr[1] == 'detail' && is_numeric($pagePathArr[2])){
          $pagePath = 'raiders-detail';
        }

    // 招聘
    }elseif($service == 'job'){
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        if($i == 1){
            if($pagePath == "company" || $pagePath == "resume" || $pagePath == "zhaopinhui"){
                $pagePath = $pagePath."-detail";
            }
        }
        $fields = array("id");
        if($pagePathArr[0] == "news" || $pagePathArr[0] == "doc"){
            if($pagePathArr[1] != "detail"){
                $fields = array("typeid", "page");
            }
        }
    // 交友
    }elseif($service == 'dating'){
        if($pagePath == "activity" || $pagePath == "story" || $pagePath == "news"){
            if(isset($pagePathArr[1])){
                if($pagePath == "news"){
                    if($pagePathArr[1] == "list"){
                        $pagePath = $pagePath."-list";
                        if(isset($pagePathArr[2])){
                            $_REQUEST['typeid'] = $pagePathArr[2];
                        }
                    }elseif($pagePathArr[1] == "detail"){
                        $pagePath = $pagePath."-detail";
                        $_REQUEST['id'] = $pagePathArr[2];
                    }
                }else{
                    $pagePath = $pagePath."-detail";
                    $_REQUEST['id'] = $pagePathArr[1];
                }
            }
        }elseif($pagePathArr[0] == "u" || $pagePathArr[0] == "getGift" || $pagePath == "hn_detail" || $pagePath == "store_detail" || $pagePath == "applyStore" || $pagePath == "hn_lead" || $pagePath == "fans" || $pagePath == "store_income_hn" || $pagePath == "my_user"){
            $_REQUEST['id'] = $pagePathArr[1];
        }
    // 外卖
    }elseif($service == 'waimai'){
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        $fields = array("id");
        if($pagePathArr[0] == "paotuipay"){
            $fields = array("orderid");
        }elseif($pagePathArr[0] == "detail"){
            $fields = array("id", "fid");
        }
    // 专题
    }elseif($service == "zhuanti"){
        if(isset($pagePathArr[1])){
            $_REQUEST['id'] = $pagePathArr[1];
        }
    // 报刊
    }elseif($service == "paper"){
        if(isset($pagePathArr[1])){
            $_REQUEST['id'] = $pagePathArr[1];
        }
    // 视频
    }elseif($service == "video"){
        if($pagePath == "list"){
            $_REQUEST['typeid'] = $pagePathArr[1];
            if(isset($pagePathArr[2])){
                $_REQUEST['page'] = $pagePathArr[2];
            }
        }else if($pagePath == "personal"){
            $_REQUEST['service'] = $pagePathArr[1];
            $_REQUEST['id'] = $pagePathArr[1];

        }

    // 黄页
    }elseif($service == "huangye"){
        if($pagePath == "list"){
            $data = array();
            for($s = 1; $s <= count($pagePathArr); $s++){
                $data[] = $pagePathArr[$s];
            }
        }elseif($pagePath == 'detail'){
            $_REQUEST['id'] = $pagePathArr[1];
        }
    // 投票
    }elseif($service == "vote"){
        foreach ($pagePathArr as $k => $v) {
            if(is_numeric($v)){
                $dataArr[] = urldecode($v);
            }
        }
        $fields = array("id", "orderby", "page");
        if($pagePath=="search"){
			$fields = array("state", "orderby", "page");
        }
    // 贴吧
    }elseif($service == "tieba"){
        foreach ($pagePathArr as $k => $v) {
            if(is_numeric($v)){
                $dataArr[] = urldecode($v);
            }
        }
        $fields = array("id");
    // 活动
    }elseif($service == "huodong"){
        if($pagePath == "list"){
            $_REQUEST['typeid'] = isset($pagePathArr[1]) ? $pagePathArr[1] : 0;
        }elseif($pagePath == "detail" || $pagePath == "business"){
            $_REQUEST['id'] = $pagePathArr[1];
        }elseif($pagePath == "order"){
            $_REQUEST['id'] = $pagePathArr[1];
            $_REQUEST['fid'] = isset($pagePathArr[2]) ? $pagePathArr[2] : '';
        }
    // 积分商城
    }elseif($service == "integral"){
        if($pagePath == "confirm"){
            $pagePath = "confirm-order";
        }
    // 商家
    }if($service == "business"){
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            // (int)substr($v, 0, 2) != 0 时 是订单号
            if(!$end && $v && strstr($v, ',') === false && (!is_numeric($v) && (int)$v == 0 && (int)substr($v, 0, 2) == 0) ){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }

        if($pagePath == "paidui-results" || $pagePath == "dingzuo-results"){
            $_REQUEST['ordernum'] = $dataArr[0];
        }else{
            $fields = array("bid", "id");
            if($pagePathArr[0] == "diancan"){
                $fields = array("bid", "fid");
            }elseif($pagePathArr[0] == "noticesdetail" || $pagePathArr[0] == "detail" || $pagePathArr[0] == "panord" || $pagePathArr[0] == "discovery"){
                $fields = array("id");
            }
        }

    // 直播
    }elseif($service == "live"){
        /* foreach ($pagePathArr as $k => $v) {
            if(is_numeric($v)){
                $dataArr[] = urldecode($v);
            }
        } */
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        if($pagePathArr[0] == "anchor_index"){
			$fields = array("userid");
        }elseif($pagePathArr[0] == "livelist"){
			$fields = array("typeid", "state", "orderby", "page");
        }elseif($pagePathArr[0] == "h_detail"){
            $fields = array("id");
        }elseif($pagePathArr[0] == "check_pass"){
            $fields = array("id");
        }elseif($pagePathArr[0] == "returnLivePay"){
            $fields = array("ordernum");
        }elseif ($pagePathArr[0] == "sharePage"){
            $fields = array("liveid");
        }elseif ($pagePathArr[0] == 'pay'){
            $fields = array("ordernum");
        }elseif($pagePathArr[0] =='sharePageAfter'){
            $fields = array("share_user","share_live");
        }elseif($pagePathArr[0] =='redPacket'){
            $fields = array("liveid", "chatid");
        }elseif($pagePathArr[0] == "search"){
			$fields = array("type", "orderby", "state", "page");
        }
    //汽车
	}elseif($service == "car"){
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        $fields = array("id", "aid", "page");

        if($pagePath == "broker"){
            $fields = array("addrid", "business", "page");
        }elseif($pagePath == "broker-detail"){
            $fields = array("id", "tpl", "page");
        }elseif($pagePath == "news" || $pagePath == "news-list"){
            $fields = array("typeid", "page");
        }elseif(strstr($pagePath, "store-detail")){
            $fields = array("id");
            if($pagePath != "store-detail"){
                $data = array();
                for($s = 4; $s < count($pagePathArr); $s++){
                    $data[] = $pagePathArr[$s];
                }
                $pagePath = "store-detail";
                $_REQUEST['tpl'] = $pagePathArr[2];
            }
        }else{
            if($pagePathArr[1] == "detail"){
                $fields = array("id", "page");
            }
        }
        if(!strstr($pagePath, "-") && $i >= 1){
            $data = $dataArr;
        }
    //家政
    }elseif($service == "homemaking"){
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        $fields = array("id", "aid", "page");

        if(strstr($pagePath, "store-detail")){
            $fields = array("id");
            if($pagePath != "store-detail"){
                $data = array();
                for($s = 4; $s < count($pagePathArr); $s++){
                    $data[] = $pagePathArr[$s];
                }
                $pagePath = "store-detail";
                $_REQUEST['tpl'] = $pagePathArr[2];
            }
        }elseif($pagePath == 'buy'){
            $_REQUEST['id'] = $pagePathArr[1];
            $_REQUEST['count'] = $pagePathArr[2];
        }else{
            if($pagePathArr[1] == "detail"){
                $fields = array("id", "page");
            }
        }
        if(!strstr($pagePath, "-") && $i >= 1){
            $data = $dataArr;
        }
    //婚嫁
    }elseif($service == "marry"){
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        $fields = array("id", "aid", "page");

        if(strstr($pagePath, "store-detail")){
            $fields = array("id", "istype", "typeid");
            if($pagePath != "store-detail"){
                $data = array();
                for($s = 4; $s < count($pagePathArr); $s++){
                    $data[] = $pagePathArr[$s];
                }
                $pagePath = "store-detail";
                $_REQUEST['tpl'] = $pagePathArr[2];
            }
        }elseif($pagePath == 'detail'){
            $_REQUEST['id'] = $pagePathArr[1];
            $_REQUEST['typeid'] = $pagePathArr[2];
        }elseif($pagePath == 'planmeallist'){
            $fields = array("id", "typeid", "istype", "businessid");
        }else{
            if($pagePathArr[1] == "detail"){
                $fields = array("id", "page");
            }
        }

        if($pagePath == "planmeal-detail"){
            $fields = array("id", "typeid", "istype", "businessid");
        }

        if(!strstr($pagePath, "-") && $i >= 1){
            $data = $dataArr;
        }
    }elseif($service == "travel"){//旅游
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        $fields = array("id", "aid", "page");

        if(strstr($pagePath, "store-detail")){
            $fields = array("id");
            if($pagePath != "store-detail"){
                $data = array();
                for($s = 4; $s < count($pagePathArr); $s++){
                    $data[] = $pagePathArr[$s];
                }
                $pagePath = "store-detail";
                $_REQUEST['tpl'] = $pagePathArr[2];
            }
        }elseif($pagePath == 'detail'){
            $_REQUEST['id'] = $pagePathArr[1];
        }elseif($pagePath == 'visa'){
            $fields = array("country");
        }elseif($pagePath == 'confirm-order'){
            $fields = array("type", "id");
        }elseif($pagePath == 'travel-ticketstate' || $pagePath == 'travel-hotelstate'){
            $fields = array("ordernum");
        }

        if(!strstr($pagePath, "-") && $i >= 1){
            $data = $dataArr;
        }
    }elseif($service == "education"){//教育
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        $fields = array("id", "aid", "page");

        if(strstr($pagePath, "store-detail")){
            $fields = array("id");
            if($pagePath != "store-detail"){
                $data = array();
                for($s = 4; $s < count($pagePathArr); $s++){
                    $data[] = $pagePathArr[$s];
                }
                $pagePath = "store-detail";
                $_REQUEST['tpl'] = $pagePathArr[2];
            }
        }elseif($pagePath == 'detail'){
            $_REQUEST['id'] = $pagePathArr[1];
        }elseif($pagePath == 'confirm-order'){
            $fields = array("type", "id");
        }

        if(!strstr($pagePath, "-") && $i >= 1){
            $data = $dataArr;
        }
    }elseif($service == "pension"){//养老
        $pagePath = "";
        $i = $end = 0;
        foreach ($pagePathArr as $k => $v) {
            if(!$end && $v && strstr($v, ',') === false && !is_numeric($v)){
                $pagePath = $pagePath ? ($pagePath."-".$v) : $v;
            }else{
                $dataArr[] = urldecode($v);
                $i++;
                $end = 1;
            }
        }
        $fields = array("id", "aid", "page");

        if(strstr($pagePath, "store-detail")){
            $fields = array("id");
            if($pagePath != "store-detail"){
                $data = array();
                for($s = 4; $s < count($pagePathArr); $s++){
                    $data[] = $pagePathArr[$s];
                }
                $pagePath = "store-detail";
                $_REQUEST['tpl'] = $pagePathArr[2];
            }
        }elseif($pagePath == 'detail'){
            $_REQUEST['id'] = $pagePathArr[1];
        }elseif($pagePath == 'confirm-order'){
            $fields = array("type", "id");
        }

        if(!strstr($pagePath, "-") && $i >= 1){
            $data = $dataArr;
        }
    }



    if($fields && $dataArr){
        foreach ($fields as $key => $value) {
            if(isset($dataArr[$key])){
                $_REQUEST[$value] = $dataArr[$key];
            }
        }
    }
    if($data){
        $_GET['data'] = join('-', $data);
    }
//     print_r($fields);
//     print_r($dataArr);
//     die;
    return $pagePath;
}


//根据第三方视频链接，获取真实文件播放地址
function getRealVideoUrl($url){
  if(!empty($url)){

    //腾讯视频
    //获取教程 https://www.jiezhe.net/post/38.html
    if(strstr($url, 'v.qq.com')){
      $vid = getUrlQuery($url, 'vid');
      if($vid){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://vv.video.qq.com/getinfo?vids=$vid&platform=101001&charge=0&otype=json");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $con = curl_exec($curl);
        curl_close($curl);

        if($con){
          $con = str_replace('QZOutputJson=', '', $con);
          $con = substr($con, 0, -1);
          $con = json_decode($con, true);

          if(is_array($con)){
            $vl = $con['vl'];
            $vi = $vl['vi'][0];
            $ui = $vi['ul']['ui'][0];

            $fn = $vi['fn'];  //mp4地址
            $fvkey = $vi['fvkey'];  //fvkey
            $url = $ui['url'];

            return $url . $fn . '?vkey=' . $fvkey;
          }
        }
      }
    }
    return $url;
  }
}


//替换emoji表情
function filter_emoji($str) {
  $regex = '/(\\\u[ed][0-9a-f]{3})/i';
  $str = json_encode($str);
  $str = preg_replace($regex, '', $str);
  return json_decode($str);
}


// ----------------------------审核流程
// 审核配置
function getAuditConfig($module = "article"){
    include HUONIAOINC."/config/".$module.".inc.php";
    return array(
        "switch" => (int)$custom_auditSwitch,
        "auth" => (int)$custom_auditAuth,
        "type" => (int)$custom_auditType,
    );
}
// 获取组织架构
function getAdminOrganizatList($id = 0){
    global $dsql;
    if($id){
        $parList = getParentArr("site_organizat", $id);
        if($parList && isset($parList[1])){

            $ret = array();
            global $data;
            $data     = $par_id = $par_admin = $par_name = array();
            $par_id   = parent_foreach($parList[1], "id");
            $data     = array();
            $par_name = parent_foreach($parList[1], "typename");

            foreach ($par_id as $key => $value) {
                $ret[$key] = array(
                    "id" => $par_id[$key],
                    "typename" => $par_name[$key],
                );
            }
            return $ret;
        }else{
            $ret = array();
            $sql = $dsql->SetQuery("SELECT `typename` FROM `#@__site_organizat` WHERE `id` = $id");
            $res = $dsql->dsqlOper($sql, "results");
            if($res){
                $ret[0] = array(
                    "top" => 1,
                    "id" => $id,
                    "typename" => $res[0]['typename'],
                );
            }
            return $ret;
        }
    }else{
        $ret = $dsql->getTypeList(0, "site_organizat", true, 1, 1000, "", "admin");
        return $ret;
    }
}
/**
 * [判断管理员是否在组织架构中]
 * @param  integer $admin   [管理员id]
 * @param  integer $checkid [有多个平行审核级别时，需要checkid]
 * @return [type]           [所在组织架构id]
 */
function getAdminOrganId($admin = 0, $checkid = 0){
    global $dsql;
    global $userLogin;
    if($admin == 0) $admin = $userLogin->getUserID();
    $reg = "(^$admin$|^$admin,|,$admin,|,$admin$)";
    $where = " `admin` REGEXP '".$reg."' ";
    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_organizat` WHERE ".$where);
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        if($checkid){
            if($checkid == $ret[0]['id']){
                return $ret[0]['id'];
            }else{
                global $data;
                $data = "";
                $typeArr = getParentArr("site_organizat", $checkid);
                $typeArr = array_reverse(parent_foreach($typeArr, "id"));

                if(in_array($checkid, $typeArr)){
                    return $ret[0]['id'];
                }else{
                    return 0;
                }
            }
        }else{
            return $ret[0]['id'];
        }
    }else{
        return 0;
    }
}
/**
 * [判断当前管理员是否需要走审核流程]
 * 如果开启了审核流程，不是超管或一级审核人员，则走审核流程
 * @param  string  $module [description]
 * @param  boolean $strict [为true时，一级审核人员也返回true]
 * @return [type]          [description]
 */
function checkAdminArcrank($module = "article", $strict = false){
    global $dsql;
    global $userLogin;
    $adminID = $userLogin->getUserID();
    $config = getAuditConfig($module);
    $need_audit = 0;
    // 开启审核
    if($config['switch']){
        $purview = $userLogin->getPurview();
        // 排除超级管理员
        if(!preg_match('/founder/i', $purview)){
            $organizat = getAdminOrganizatList();
            if($organizat){
                $need_audit = 1;
                global $data;
                $data = array();
                $organizat_admin = parent_foreach($organizat, "admin");
                $has = false;
                foreach ($organizat_admin as $key => $value) {
                    $ids = explode(',', $value);
                    if(in_array($adminID, $ids)){
                        $has = true;
                        // 顶级管理员不需要走审核流程
                        if($key == 0){
                            if($strict){
                                $need_audit = 2;
                            }else{
                                $need_audit = 0;
                            }
                        }
                        // if($key == 0 && !$strict){
                        //     $need_audit = false;
                        // }
                        break;
                    }
                }
                // if(!$has) $need_audit = false;
            }

        }
    }

    return $need_audit;
}
// 获取管理员在组织机构中的详细信息
function getAdminOrganDetail($admin = 0){
    global $dsql;
    global $userLogin;
    if($admin == 0) $admin = $userLogin->getUserID();

    $detail = array();

    $reg = "(^$admin$|^$admin,|,$admin,|,$admin$)";
    $where = " `admin` REGEXP '".$reg."' ";
    $sql = $dsql->SetQuery("SELECT * FROM `#@__site_organizat` WHERE ".$where);
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        $ret = $ret[0];
        $arc = $dsql->SetQuery("SELECT `nickname` FROM `#@__member` WHERE `id` = ".$admin);
        $res = $dsql->dsqlOper($arc, "results");
        $nickname = $res[0]['nickname'];

        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_organizat` WHERE `parentid` = ".$ret['id']);
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            $bottom = 0;
        }else{
            $bottom = 1;
        }
        $top = $ret['parentid'] ? 0 : 1;

        $detail = array(
            "id" => $ret[id],
            "typename" => $ret['typename'],
            "nickname" => $nickname,
            "top" => $top,
            "bottom" => $bottom,
        );
    }

    return $detail;
}
// 验证当前管理员是否可修改信息状态，并且审核人员没有审核通过
function checkAdminEditAuth($module, $aid){
    global $dsql;
    global $userLogin;
    $adminID = $userLogin->getUserID();

    $config = getAuditConfig($module);
    // 开启审核
    if($config['switch']){

        $tab = $module."list";
        $sql = $dsql->SetQuery("SELECT `arcrank`, `audit_log` FROM `#@__".$tab."` WHERE `id` = $aid AND `admin` = $adminID");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            // 全部为待审核状态才可编辑
            $audit_log = $ret[0]['audit_log'];
            if($audit_log){
                $audit_log = unserialize($audit_log);
                if($audit_log){
                    if($ret[0]['arcrank'] == 1){
                        return false;
                    }
                    $all_wait = true;
                    foreach ($audit_log as $key => $value) {
                        if($value['state'] != 0){
                            $all_wait = false;
                            break;
                        }
                    }
                    if($all_wait){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return true;
                }
            }else{
                return true;
            }
        }else{
            return false;
        }
    }else{
        return true;
    }

    return true;
}

//获取蜘蛛爬虫名或防采集
function isSpider(){
  $bots = array(
    'Google'    => 'googlebot',
    'Baidu'     => 'baiduspider',
    '360'       => '360spider',
    'Yahoo'     => 'yahoo! slurp',
    'Soso'      => 'sosospider',
    'Msn'       => 'msnbot',
    'Altavista' => 'scooter',
    'Sogou'     => 'sogou spider',
    'Sogou1'     => 'sogou',
    'Yodao'     => 'yodaobot',
    'Bing'      => 'bingbot',
    'Slurp'     => 'slurp'
  );
  $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
  if($userAgent){
    foreach($bots as $k => $v){
        // 有浏览器标识不作为爬虫
      if((strstr($v,$userAgent) || strstr($userAgent,$v)) && strstr($userAgent, 'browser') == false){
        return $k;
        break;
      }
    }
  }
  return false;
}



//通过出生年月获取属相及生肖
function birthdayInfo($bithday){
    if(strstr($bithday,'-') === false && strlen($bithday) !== 8){
        $bithday = date("Y-m-d",$bithday);
    }

    if(strlen($bithday) < 8){
        return false;
    }
    $tmpstr= explode('-',$bithday);
    if(count($tmpstr)!==3){
        return false;
    }
    $y=(int)$tmpstr[0];
    $m=(int)$tmpstr[1];
    $d=(int)$tmpstr[2];
    $result=array();
    $xzdict=array('摩羯','水瓶','双鱼','白羊','金牛','双子','巨蟹','狮子','处女','天秤','天蝎','射手');
    $zone=array(1222,122,222,321,421,522,622,722,822,922,1022,1122,1222);
    if((100*$m+$d)>=$zone[0]||(100*$m+$d)<$zone[1]){
        $i=0;
    }else{
        for($i=1;$i<12;$i++){
            if((100 * $m + $d) >= $zone[$i] && (100 * $m + $d) < $zone[$i+1]){
                break;
            }
        }
    }
    $result['xz']=$xzdict[$i].'座';
    $gzdict=array(array('甲','乙','丙','丁','戊','己','庚','辛','壬','癸'),array('子','丑','寅','卯','辰','巳','午','未','申','酉','戌','亥'));
    $i= $y-1900+36;
    $result['gz']=$gzdict[0][($i%10)].$gzdict[1][($i%12)];
    $sxdict=array('鼠','牛','虎','兔','龙','蛇','马','羊','猴','鸡','狗','猪');
    $result['sx']=$sxdict[(($y-4)%12)];
    return $result;
}

//获取融云Token
function getRongCloudToken($uid = 0, $username = '', $photo = ''){
  global $dsql;

  if(empty($uid)) return array("state" => 200, "info" => '会员ID不得为空！');

  //获取融云配置
  $sql = $dsql->SetQuery("SELECT * FROM `#@__app_config` LIMIT 1");
  $ret = $dsql->dsqlOper($sql, "results");
  if($ret){
    $data = $ret[0];
    $appKey = $data['rongKeyID'];
    $appSecret = $data['rongKeySecret'];

    if($appKey && $appSecret){
      //获取token
      include_once(HUONIAOINC."/class/imserver/im.class.php");
      $RongCloud = new im($appKey, $appSecret);

      $token = $RongCloud->getToken($uid, $username, $photo);
      $tokenArr = json_decode($token, true);
      if($tokenArr['code'] != 200){
        return array("state" => 200, "info" => '获取token参数错误！');
      }

      return $tokenArr['token'];
    }else{
      return array("state" => 200, "info" => '融云参数未填写，请至后台APP配置中填写完整！');
    }

  }else{
    return array("state" => 200, "info" => '服务器配置参数获取失败！');
  }

}


//检测系统最新版本
function checkSystemUpdate(){

    global $cfg_basehost;
    require_once(HUONIAODATA."/admin/config_official.php");
    $version = getSoftVersion();
    $returnUrl = $cfg_basehost."||".$_SERVER['PHP_SELF']."||".$version;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $cloudHost.'/include/ajax.php?action=checkSystemUpdate&data='.base64_encode($returnUrl).'&v='.time());
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    $con = curl_exec($curl);

    $data = '';
    if($con){
        $data = json_decode($con, true);
    }
    curl_close($curl);
    return $data;

}


//查找文章id所在的表
function getBreakTableById($articleId)
{
    global $dsql;
    $sql_choose = $dsql->SetQuery("SELECT * FROM `#@__article_breakup_table`");
    $breakup_table_res = $dsql->dsqlOper($sql_choose, "results");
    $compareArr = array_column($breakup_table_res, 'begin_id');
    if($index = array_search($articleId, $compareArr)){
        $break_table_name = $breakup_table_res[$index]['table_name'];
    }else{
        array_push($compareArr, $articleId);
        array_push($compareArr, 0);
        sort($compareArr);
        $index = array_search($articleId, $compareArr);
        $search_begin_id = $compareArr[$index-1];
        if($search_begin_id == 0){
            $break_table_name = '#@__articlelist';
        }else{
            $table_index = array_search($search_begin_id, array_column($breakup_table_res, 'begin_id'));
            $break_table_name = $breakup_table_res[$table_index]['table_name'];
        }
    }
    return $break_table_name;
}

//计算两个时间戳相差的时分秒
function timediff($begin_time,$end_time){
    if($begin_time < $end_time){
        $starttime = $begin_time;
        $endtime = $end_time;
    }else{
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    //计算天数
    $timediff = $endtime-$starttime;
    $days = intval($timediff/86400);
    //计算小时数
    $remain = $timediff%86400;
    $hours = intval($remain/3600);
    //计算分钟数
    $remain = $remain%3600;
    $mins = intval($remain/60);
    //计算秒数
    $secs = $remain%60;
    $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
    return $res;
}
//创建文章分表
function createArticleTable($lastId){
    global $dsql;
    global $DB_PREFIX;
    $startId = (int)$lastId+1;
    $tableName = '#@__articlelist_' . $startId;
//     $sql = $dsql->SetQuery("DESC `#@__articlelist`");
//     $res = $dsql->dsqlOper($sql, "results");
//     $sql2 = "CREATE TABLE `$tableName`(";
//     foreach ($res as $re){
//         $Field = $re['Field'];
//         $Type = $re['Type'];
//         $Null = $re['Null'] == "NO" ? "NOT NULL" : "";
//         $Extra = $re['Extra'];
//         if(is_null($re['Default'])){
//             $Default = '';
//         }else{
//             $Default = $re['Default'] == "" ? 'DEFAULT \'\'' : ("DEFAULT " . $re['Default']);
//         }
//         $sql2 .= "$Field $Type $Null $Extra $Default, ";
//     }
//     $sql2 .= "  PRIMARY KEY (`id`),
//   KEY `title` (`title`),
//   KEY `typeid` (`typeid`),
//   KEY `arcrank` (`arcrank`),
//   KEY `del` (`del`,`arcrank`,`typeid`,`flag`,`litpic`,`weight`) USING BTREE,
//   KEY `flag` (`flag`),
//   KEY `admin` (`admin`),
//   KEY `keywords` (`keywords`),
//   KEY `weight` (`weight`,`id`),
//   KEY `flag_` (`flag_h`,`flag_b`,`flag_r`,`flag_t`,`flag_p`)
// ) ";
// $sql2 .= "ENGINE=MyISAM AUTO_INCREMENT=$startId DEFAULT CHARSET=utf8 COMMENT='新闻信息';";
// $sql3 = $dsql->SetQuery($sql2);
// $dsql->dsqlOper($sql3, "update");

    $sql = $dsql->SetQuery("show create table #@__articlelist");
    $res = $dsql->dsqlOper($sql, "results");
    $defSql = $res[0]['Create Table'];
    $defSql = str_replace("\r","",$defSql);
    $defSql = str_replace("\n","",$defSql);
    $defSql = preg_replace("#AUTO_INCREMENT=([0-9]{1,})[ \r\n\t]{1,}#i", "AUTO_INCREMENT=$startId", $defSql);

    // 创建分表
    $sql = str_replace($DB_PREFIX.'articlelist', "{$tableName}", $defSql);
    $sql = $dsql->SetQuery($sql);
    $res = $dsql->dsqlOper($sql, "update");

    // $sql = $dsql->SetQuery("CREATE TABLE IF NOT EXISTS {$tableName} (LIKE `#@__articlelist`)");
    // $res = $dsql->dsqlOper($sql, "udpate");

    // 更新主表总表关联表
    $sql = $dsql->SetQuery("SELECT * FROM `#@__article_breakup_table`");
    $res = $dsql->dsqlOper($sql, "results");
    $un = array();
    $un[] = "`#@__articlelist`";
    foreach ($res as $key => $value) {
        $un[] = "`".$value['table_name']."`";
    }
    $un[] = "`{$tableName}`";


    $sql = $dsql->SetQuery("DROP TABLE IF EXISTS `#@__articlelist_all`");
    $res = $dsql->dsqlOper($sql, "update");

    $sql = preg_replace("#AUTO_INCREMENT=([0-9]{1,})[ \r\n\t]{1,}#i", "", $defSql);
    $sql = str_replace('articlelist', "articlelist_all", $sql);
    $sql = str_replace('ENGINE=MyISAM', 'ENGINE=MRG_MyISAM', $sql);
    $sql .= " UNION=(".join(",", $un).");";
    $sql = $dsql->SetQuery($sql);
    $res = $dsql->dsqlOper($sql, "update");

    // // 更新主表总表关联表
    // $sql = $dsql->SetQuery("SELECT * FROM `#@__article_breakup_table`");
    // $res = $dsql->dsqlOper($sql, "results");

    // $un = array();
    // $un[] = "`#@__articlelist`";
    // foreach ($res as $key => $value) {
    //     $un[] = "`".$value['table_name']."`";
    // }
    // $un[] = "`{$tableName}`";
    // $sql = $dsql->SetQuery("ALTER TABLE `#@__articlelist_all` UNION=(".join(",", $un).")");
    // $res = $dsql->dsqlOper($sql, "update");
    return $tableName;
}

//保存分表记录
function saveBreakUpTable($new_table, $startId){
    global $dsql;
    $startId = $startId + 1;
    $sql = $dsql->SetQuery("INSERT INTO `#@__article_breakup_table` (`table_name`, `begin_id`) VALUES ('$new_table', $startId)");
    $dsql->dsqlOper($sql, "update");
}
//获取分表
function getReverseBreakTable(){
    global $dsql;
    $sql_choose = $dsql->SetQuery("SELECT * FROM `#@__article_breakup_table`");
    $breakup_table_res = $dsql->dsqlOper($sql_choose, "results");
    $break_last_table = $breakup_table_res[count($breakup_table_res)-1]['table_name']; //最新的表
    $rev_break_table_res = array_reverse($breakup_table_res);
    array_push($rev_break_table_res, array('table_name' => '#@__articlelist', 'begin_id' =>0));
    return array('tables' => $rev_break_table_res, 'last_table' => $break_last_table);
}


//获取指定key
function pageCountGet($key)
{
    global $dsql;
    $now = time();
    $key = str_replace("'", "\"", $key);
    $sql = $dsql->SetQuery("SELECT * FROM `#@__article_pagecount_cache` WHERE `key` = '$key'");
    $res = $dsql->dsqlOper($sql, "results");
    return $res[0]['value'];
}
//是否存在
function pageCountHas($key)
{
    global $dsql;
    $key = str_replace("'", "\"", $key);
    $sql = $dsql->SetQuery("SELECT * FROM `#@__article_pagecount_cache` WHERE `key` = '$key'");
    $res = $dsql->dsqlOper($sql, "results");
    if(!empty($res)){
        return 1;
    }else{
        return 0;
    }
}
//是否过期
function pageCountGuoQi($key)
{
    global $dsql;
    $now = time();
    $key = str_replace("'", "\"", $key);
    $sql = $dsql->SetQuery("SELECT * FROM `#@__article_pagecount_cache` WHERE `key` = '$key'");
    $res = $dsql->dsqlOper($sql, "results");
    $insert_time = $res[0]['update_at'];
    $diff = timediff($insert_time, $now);
    if($diff['hour'] > 2){
        return 0;
    }else{
        return 1;
    }
}
//更新key
function pageCountUp($key, $value)
{
    global $dsql;
    $time = time();
    $key = str_replace("'", "\"", $key);
    $sql = $dsql->SetQuery("UPDATE `#@__article_pagecount_cache` SET `value` = $value, `update_at` = $time WHERE `key` = '$key'");
    $dsql->dsqlOper($sql, "update");
}
//设置key
function pageCountSet($key, $value){
    global $dsql;
    $time = time();
    $key = str_replace("'", "\"", $key);
    $sql = $dsql->SetQuery("INSERT INTO `#@__article_pagecount_cache` (`key`, `value`, `update_at`) VALUES ('$key', $value, $time)");
    $dsql->dsqlOper($sql, "lastid");
}

// 创建店铺时更新店铺开关
function updateStoreSwitch($module, $tab, $uid, $aid){
    global $dsql;
    if(empty($module) || empty($tab) || empty($uid) || empty($aid)) return;
    $state = 1;

    $sql = $dsql->SetQuery("SELECT `bind_module` FROM `#@__business_list` WHERE `uid` = $uid");
    $ret = $dsql->dsqlOper($sql, "results");
    if(!$ret) return;

    $bind_module = $ret[0]['bind_module'];
    if(empty($bind_module)){
        $state = 0;
    }else{
        $bind_module = explode(",", $bind_module);
        if(!in_array($module, $bind_module)){
            $state = 0;
        }
    }
    $sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `store_switch` = $state WHERE `id` = $aid");
    $dsql->dsqlOper($sql, "update");
}

// 商家模块管理
function checkShowModule($bind_module = array(), $usetype = "manage", $getConfig = "", $getUrl = "", $bid = 0){
    global $dsql;
    global $userLogin;
    global $cfg_secureAccess;
    global $cfg_basehost;
    global $langData;

    $busi = new business();
    $config = $busi->config();
    $joinAuth = $config['joinAuth'];
    if(empty($joinAuth) || !is_array($joinAuth)){
        if($getConfig == "getConfig"){
            return array('res' => array(), 'config' => $config);
        }else{
            return array();
        }
    }
    //print_r($joinAuth);die;

    $is_setbid = !!$bid;    // 是否是指定了商家id，用来判断是否是前台商家页面

    $uid = $userLogin->getMemberID();

    if($bid == 0){
        $sql = $dsql->SetQuery("SELECT `id`, `uid`, `type`, `state`, `bind_module` FROM `#@__business_list` WHERE `uid` = $uid");
    }else{
        $sql = $dsql->SetQuery("SELECT `id`, `uid`, `type`, `state`, `bind_module` FROM `#@__business_list` WHERE `id` = $bid");
    }
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        $bid = $ret[0]['id'];
        $uid = $ret[0]['uid'];
        $type = $ret[0]['type'];
        $state = $ret[0]['state'];
        $bind_module = $ret[0]['bind_module'] ? explode(',', $ret[0]['bind_module']) : array();
    }else{
        return array();
    }

    $allModule = array();
    $allModule[] = array('name' => 'dingzuo', 'title' => $langData['siteConfig'][16][6], 'is_module' => 0, 'check' => 'null');//订座
    $allModule[] = array('name' => 'paidui', 'title' => $langData['siteConfig'][16][7], 'is_module' => 0, 'check' => 'null');//排队
    $allModule[] = array('name' => 'diancan', 'title' => $langData['siteConfig'][16][5], 'is_module' => 0, 'check' => 'null');//点餐
    $allModule[] = array('name' => 'maidan', 'title' => $langData['siteConfig'][16][8], 'is_module' => 0, 'check' => 'null');//买单
    $allModule[] = array('name' => 'article', 'title' => '新闻', 'is_module' => 1, 'check' => 'module', 'for_uc' => 1);
    $allModule[] = array('name' => 'renovation', 'title' => '装修', 'is_module' => 1, 'check' => 'store', 'for_uc' => 1);
    $allModule[] = array('name' => 'tieba', 'title' => '贴吧', 'is_module' => 1, 'check' => 'module', 'for_uc' => 1);
    $allModule[] = array('name' => 'shop', 'title' => '商城', 'is_module' => 1, 'check' => 'store');
    $allModule[] = array('name' => 'website', 'title' => '官网', 'is_module' => 1, 'check' => '');
    $allModule[] = array('name' => 'info', 'title' => '二手', 'is_module' => 1, 'check' => 'null');
    $allModule[] = array('name' => 'job', 'title' => '招聘', 'is_module' => 1, 'check' => 'store');
    $allModule[] = array('name' => 'house', 'title' => '房产', 'is_module' => 1, 'check' => 'store');
    $allModule[] = array('name' => 'dating', 'title' => '婚介所', 'is_module' => 1, 'check' => 'store');
    $allModule[] = array('name' => 'tuan', 'title' => '团购', 'is_module' => 1, 'check' => 'store');
    $allModule[] = array('name' => 'video', 'title' => '短视频', 'is_module' => 1, 'check' => 'module');
    $allModule[] = array('name' => 'quanjing', 'title' => 'VR全景', 'is_module' => 1, 'check' => 'newMedia');
    $allModule[] = array('name' => 'live', 'title' => '直播', 'is_module' => 1, 'check' => 'module');
    $allModule[] = array('name' => 'waimai', 'title' => '外卖', 'is_module' => 1, 'check' => 'store');
    // $allModule[] = array('name' => 'huangye', 'title' => '黄页', 'is_module' => 1, 'check' => 'null');
    // $allModule[] = array('name' => 'tandian', 'title' => '探店', 'is_module' => 0, 'check' => 'null');
    $allModule[] = array('name' => 'vote', 'title' => '投票', 'is_module' => 1, 'check' => 'module');
    $allModule[] = array('name' => 'huodong', 'title' => '活动', 'is_module' => 1, 'check' => 'module');
    // $allModule[] = array('name' => 'huodong', 'title' => '活动店铺', 'is_module' => 0, 'check' => 'store');
    $allModule[] = array('name' => 'car', 'title' => '汽车', 'is_module' => 1, 'check' => 'module');
    $allModule[] = array('name' => 'homemaking', 'title' => '家政', 'is_module' => 1, 'check' => 'store');
    $allModule[] = array('name' => 'marry', 'title' => '婚嫁', 'is_module' => 1, 'check' => 'store');
    $allModule[] = array('name' => 'travel', 'title' => $langData['travel'][12][9], 'is_module' => 1, 'check' => 'store');
    $allModule[] = array('name' => 'education', 'title' => $langData['education'][7][3], 'is_module' => 1, 'check' => 'store');
    $allModule[] = array('name' => 'pension', 'title' => $langData['pension'][0][0], 'is_module' => 1, 'check' => 'store');

    global $installModuleArr;
    global $installModuleTitleArr;

    $showModule = array();

    $isMobile = isMobile();
    $isMember = !$is_setbid;

    foreach ($allModule as $key => $value) {

        $name = $value['name'];
        // 会员中心模块管理显示所有模块，如果商家类型没有权限则给出提示
        $purview = 1;

        if($value['is_module']){
            if($value['name'] != 'huangye' && !in_array($value['name'], $installModuleArr)){
                continue;
            }
            $value['title'] = $installModuleTitleArr[$value['name']];
        }

        if(!$isMember && isset($value['for_uc']) && $value['for_uc']){
            continue;
        }

        if($value['check'] == 'null'){

        }elseif($value['check'] == 'newMedia'){
            if(!isset($joinAuth[$value['check']]['perm'])) continue;
            if(!in_array($type, $joinAuth[$value['check']]['perm'])){
                if($isMember && $type == 1){
                    if(!in_array(2, $joinAuth[$value['check']]['perm'])) continue;
                    $purview = 0;
                }else{
                    continue;
                }
            }
        }elseif($value['check'] != ''){
            if(!isset($joinAuth[$value['check']][$value['name']]) || !isset($joinAuth[$value['check']][$value['name']]['perm']) || !in_array($type, $joinAuth[$value['check']][$value['name']]['perm'])){
                if($isMember && $type == 1){
                    if(!isset($joinAuth[$value['check']][$value['name']]) || !isset($joinAuth[$value['check']][$value['name']]['perm']) || !in_array(2, $joinAuth[$value['check']][$value['name']]['perm'])) continue;
                    $purview = 0;
                }else{
                    continue;
                }
            }
        }else{
            if(!isset($joinAuth[$value['name']]) || !isset($joinAuth[$value['name']]['perm']) || !in_array($type, $joinAuth[$value['name']]['perm'])){
                if($isMember && $type == 1){
                    if(!isset($joinAuth[$value['name']]) || !isset($joinAuth[$value['name']]['perm']) || !in_array(2, $joinAuth[$value['name']]['perm'])) continue;
                    $purview = 0;
                }else{
                    continue;
                }
            }
        }
        // 电脑端缺少升级页面 暂时只显示可操作的模块
//        if($purview == 0) continue;

        $show = in_array($value['name'], $bind_module) ? 1 : 0;
        $show = $purview ? $show : 0;
        if($usetype == "show" && !$show) continue;

        //模块图标
        $icon = !$value['is_module'] ? ('/static/images/business/' . $value['name'] . '.png?v=' . $cfg_staticVersion) : ('/static/images/admin/nav/' . $value['name'] . '.png?v=' . $cfg_staticVersion);
        if($value['is_module']){
            $sql = $dsql->SetQuery("SELECT `icon` FROM `#@__site_module` WHERE `name` = '".$value['name']."'");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $icon = getFilePath($ret[0]['icon']);
            }
        }

        $showModule[$value['name']] = array(
            'name' => $value['name'],
            'title' => $value['title'],
            'is_module' => $value['is_module'],
            'show' => $show,
            'icon' => $icon,
            'purview' => $purview
        );

        if($getUrl == "getUrl"){
            $has = false;
            $url = "";
            $sid = 0;
            $param = array();
            if($value['name'] == 'shop'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "shop");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "shop", "template" => "store-detail", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'website'){
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__website` WHERE `userid` = $uid AND `state` = 1");
                $ret = $dsql->dsqlOper($sql, "results");
                if ($ret) {
                    $has = true;
                    $sid = $ret[0]['id'];
                    $param = array("service" => "website", "template" => "site".$sid);
                }
            }elseif($value['name'] == 'info'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "info");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__infoshop` WHERE `uid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "info", "template" => "business", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'job'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "job");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__job_company` WHERE `userid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "job", "template" => "company", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'dating'){
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $uid AND `type` = 2 AND `state` = 1");
                $ret = $dsql->dsqlOper($sql, "results");
                if ($ret) {
                    $has = true;
                    $sid = $ret[0]['id'];
                    $param = array("service" => "dating", "template" => "store_detail", "id" => $sid);
                }
            }elseif($value['name'] == 'tuan'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "tuan");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__tuan_store` WHERE `uid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "tuan", "template" => "store", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'video'){

            }elseif($value['name'] == 'quanjing'){

            }elseif($value['name'] == 'live'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "type" => "user", "template" => "manage");
                }

            }elseif($value['name'] == 'waimai'){
                if($isMember){
                    // $has = true;
                    // $url = $cfg_secureAccess.$cfg_basehost."/wmsj";
                }else{
                    $sql = $dsql->SetQuery("SELECT s.`id` FROM `#@__waimai_shop` s LEFT JOIN `#@__waimai_shop_manager` m ON m.`shopid` = s.`id` WHERE m.`userid` = $uid AND s.`status` = 1 AND s.`ordervalid` = 1 AND s.`del` = 0 ORDER BY `id` DESC LIMIT 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "waimai", "template" => "shop", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'huangye'){
                $has = true;
                $sid = $bid;
                $param = array("service" => "huangye", "template" => "detail", "id" => $sid);
            }elseif($value['name'] == 'vote'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "type" => "user", "template" => "vote");
                }
            }elseif($value['name'] == 'huodong'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "type" => "user", "template" => "huodong");
                }
            }elseif($value['name'] == 'article'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "type" => "user", "template" => "article");
                }
            }elseif($value['name'] == 'tieba'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "type" => "user", "template" => "manage-tieba");
                }
            }elseif($value['name'] == 'house'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "house");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__house_zjcom` WHERE `userid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "house", "template" => "store-detail", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'renovation'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "renovation");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "renovation", "template" => "company", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'paidui'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "business-paidui-order");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `paidui_state` = 1 AND  `uid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "business", "template" => "paidui", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'maidan'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "business-maidan-order");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `maidan_state` = 1 AND  `uid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "business", "template" => "maidan", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'dingzuo'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "business-dingzuo-order");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `dingzuo_state` = 1 AND  `uid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "business", "template" => "dingzuo-online", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'diancan'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "business-diancan-order");
                }else{
                    $desk=RemoveXSS($_GET['desk']);
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `diancan_state` = 1 AND  `uid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "business", "template" => "diancan", "id" => $sid, "param" => "desk=".$desk);
                    }
                }
            }elseif($value['name'] == 'car'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "type" => 'user', "template" => "car");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__car_store` WHERE `userid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "car", "template" => "store", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'homemaking'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "homemaking");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__homemaking_store` WHERE `userid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "homemaking", "template" => "store-detail", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'marry'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "marry");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `userid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "marry", "template" => "store-detail", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'travel'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "type" => 'user', "template" => "travel");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "travel", "template" => "store-detail", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'education'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "type" => 'user', "template" => "education");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__education_store` WHERE `userid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "education", "template" => "store-detail", "id" => $sid);
                    }
                }
            }elseif($value['name'] == 'pension'){
                if($isMember){
                    $has = true;
                    $param = array("service" => "member", "template" => "pension");
                }else{
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__pension_store` WHERE `userid` = $uid AND `state` = 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        $has = true;
                        $sid = $ret[0]['id'];
                        $param = array("service" => "pension", "template" => "store-detail", "id" => $sid);
                    }
                }
            }

            if($has){
                if($param){
                    $url = getUrlPath($param);
                }
                $showModule[$value['name']]['sid'] = $sid;
                $showModule[$value['name']]['url'] = $url;

            }else{
                // 没有店铺时隐藏
                if($is_setbid){
                    unset($showModule[$value['name']]);
                    continue;
                }

                // 移动端会员中心不显示的模块
                if($isMobile && $isMember){
                    if(
                        $name == "website"
                        // || $name == "renovation"
                        // || $name == "tuan"
                        // || $name == "job"
                        // || $name == "house"
                        || $name == "dating"
                        || $name == "quanjing"
                        || $name == "waimai"
                        // || $name == "shop"
                    ){
                        unset($showModule[$value['name']]);
                    }
                }
            }
        }
    }

    if($getConfig == "getConfig"){
        return array('res' => $showModule, 'config' => $config);
    }
    return $showModule;

}



//生成自定义小程序二维码
function createWxMiniProgramScene($url = '', $path = '../..', $async = false){
    if(empty($url)) {
        if($async){
            return array("state" => 200, "info" => '链接不能为空！');
        }else {
            die(json_encode(array("state" => 200, "info" => '链接不能为空！')));
        }
    }

    //往数据库添加数据
    global $dsql;
    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_wxmini_scene` WHERE `url` = '$url'");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        if($async) {
             return array("state" => 200, "info" => '要生成的链接已经存在，无须重复创建！');
        }else{
            die(json_encode(array("state" => 200, "info" => '要生成的链接已经存在，无须重复创建！')));
        }
    }

    $time = time();
    $sql = $dsql->SetQuery("INSERT INTO `#@__site_wxmini_scene` (`url`, `date`, `fid`, `count`) VALUES ('$url', '$time', '', '0')");
    $lid = $dsql->dsqlOper($sql, "lastid");
    if(!is_numeric($lid)){
        if($async) {
            return array("state" => 200, "info" => '添加失败，请到商店中校验并同步最新的数据库结构！');
        }else{
            die(json_encode(array("state" => 200, "info" => '添加失败，请到商店中校验并同步最新的数据库结构！')));
        }
    }

    global $cfg_miniProgramAppid;
    global $cfg_miniProgramAppsecret;

    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$cfg_miniProgramAppid&secret=$cfg_miniProgramAppsecret";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = json_decode(curl_exec($curl));
    curl_close($curl);

    if(isset($res->errcode)) {
        $sql = $dsql->SetQuery("DELETE FROM `#@__site_wxmini_scene` WHERE `id` = $lid");
        $dsql->dsqlOper($sql, "update");
        if($async) {
            return array("state" => 200, "info" => $res->errcode . "_" . $res->errmsg);
        }else {
            die(json_encode(array("state" => 200, "info" => $res->errcode . "_" . $res->errmsg)));
        }
    }

    $access_token = $res->access_token;
    $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token;
    $data = array(
        'scene'			=> $lid,
        'width'			=> 500,
        'page'			=> 'pages/redirect/index'
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);// 是否为POST请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));// 处理请求数据
    $res = curl_exec($curl);
    $res_ = json_decode($res, true);
    curl_close($curl);

    if(isset($res_['errcode'])) {
        $sql = $dsql->SetQuery("DELETE FROM `#@__site_wxmini_scene` WHERE `id` = $lid");
        $dsql->dsqlOper($sql, "update");
        if($async) {
            return array("state" => 200, "info" => $res_['errcode'] . "_" . $res_['errmsg']);
        }else {
            die(json_encode(array("state" => 200, "info" => $res_['errcode'] . "_" . $res_['errmsg'])));
        }
    }

    /* 上传配置 */
    $config = array(
        "savePath" => $path . "/uploads/siteConfig/wxminProgram/large/".date( "Y" )."/".date( "m" )."/".date( "d" )."/"
    );
    $fieldName = array($res);

    global $cfg_ftpUrl;
    global $cfg_fileUrl;
    global $cfg_uploadDir;
    global $cfg_ftpType;
    global $cfg_ftpState;
    global $cfg_ftpDir;
    global $cfg_quality;
    global $cfg_softSize;
    global $cfg_softType;
    global $cfg_editorSize;
    global $cfg_editorType;
    global $cfg_videoSize;
    global $cfg_videoType;
    global $cfg_meditorPicWidth;
    global $editorMarkState;
    global $editor_ftpType;
    global $editor_ftpState;
    global $customUpload;
    global $custom_uploadDir;
    global $customFtp;
    global $custom_ftpType;
    global $custom_ftpState;
    global $custom_ftpDir;
    global $custom_ftpServer;
    global $custom_ftpPort;
    global $custom_ftpUser;
    global $custom_ftpPwd;
    global $custom_ftpDir;
    global $custom_ftpUrl;
    global $custom_ftpTimeout;
    global $custom_ftpSSL;
    global $custom_ftpPasv;
    global $custom_OSSUrl;
    global $custom_OSSBucket;
    global $custom_EndPoint;
    global $custom_OSSKeyID;
    global $custom_OSSKeySecret;
    global $custom_QINIUAccessKey;
    global $custom_QINIUSecretKey;
    global $custom_QINIUbucket;
    global $custom_QINIUdomain;
    global $editor_ftpDir;

    $cfg_softType = explode("|", $cfg_softType);
    $cfg_editorType = explode("|", $cfg_editorType);
    $cfg_videoType = explode("|", $cfg_videoType);

    $cfg_uploadDir = "/" . $path . $cfg_uploadDir;
    $editor_ftpType = $cfg_ftpType;

    $custom_ftpState = $editor_ftpState = $cfg_ftpState;
    $custom_ftpType = $cfg_ftpType;
    $custom_ftpSSL = $cfg_ftpSSL;
    $custom_ftpPasv = $cfg_ftpPasv;
    $custom_ftpUrl = $cfg_ftpUrl;
    $custom_ftpServer = $cfg_ftpServer;
    $custom_ftpPort = $cfg_ftpPort;
    $custom_ftpDir = $editor_ftpDir = $cfg_ftpDir;
    $custom_ftpUser = $cfg_ftpUser;
    $custom_ftpPwd = $cfg_ftpPwd;
    $custom_ftpTimeout = $cfg_ftpTimeout;
    $custom_OSSUrl = $cfg_OSSUrl;
    $custom_OSSBucket = $cfg_OSSBucket;
    $custom_EndPoint = $cfg_EndPoint;
    $custom_OSSKeyID = $cfg_OSSKeyID;
    $custom_OSSKeySecret = $cfg_OSSKeySecret;
    $custom_QINIUAccessKey = $cfg_QINIUAccessKey;
    $custom_QINIUSecretKey = $cfg_QINIUSecretKey;
    $custom_QINIUbucket = $cfg_QINIUbucket;
    $custom_QINIUdomain = $cfg_QINIUdomain;

    $remoteImg = json_decode(getRemoteImage($fieldName, $config, 'siteConfig', $path, false, true), true);
    if($remoteImg['state'] == 'SUCCESS'){
        $fid = $remoteImg['list'][0]['fid'];
    }else{
        $sql = $dsql->SetQuery("DELETE FROM `#@__site_wxmini_scene` WHERE `id` = $lid");
        $dsql->dsqlOper($sql, "update");
        if($async) {
            return array("state" => 200, "info" => "文件创建失败，请检查上传配置！");
        }else{
            die(json_encode(array("state" => 200, "info" => "文件创建失败，请检查上传配置！")));
        }
    }

    $sql = $dsql->SetQuery("UPDATE `#@__site_wxmini_scene` SET `fid` = '$fid' WHERE `id` = $lid");
    $ret = $dsql->dsqlOper($sql, 'update');
    if($ret == 'ok') {
        if($async) {
            return getFilePath($fid);
        }else{
            die(json_encode(array("state" => 100, "info" => "创建成功！")));
        }
    }else{
        $sql = $dsql->SetQuery("DELETE FROM `#@__site_wxmini_scene` WHERE `id` = $lid");
        $dsql->dsqlOper($sql, "update");
        if($async) {
            return array("state" => 200, "info" => "创建失败！");
        }else{
            die(json_encode(array("state" => 200, "info" => "创建失败！")));
        }
    }
}


function checkOnlineUserCount($time = 0, $max = 10, $speed = 0){
    set_time_limit(1);
    if($time == 0) return true;
    $file = HUONIAOROOT."/data/cache/";

    if (!file_exists($file)) {
        if (!mkdir("$file", 0777, true)) {
            return false;
        };
    }

    $file = $file . 'user.txt';

    if(!is_file($file)){
        @fopen($file, "w");
        $content = "";
    }else{
        $fp = @fopen($file, "r");
        $content = @fread($fp, filesize($file));
    }

    $max += 3;
    $ratio = $time > 1234567890 ? 1000 : 1;

    $r = false;
    $body = array();
    $now = time() * $ratio;
    if(empty($content)){
        $r = true;
        $body = array(
            "s".$time => array($time, $now, $speed)
        );
    }else{
        $content = unserialize($content);
        // print_r($content);

        $has = false;
        $all_use = true;
        foreach ($content as $key => $value) {
            if( ($now - $value[1]) < ($max * $ratio) ){
                if($key == "s".$time){
                    $has = true;
                    $body[$key] = array($value[1], $now, $speed);
                }else{
                    $body[$key] = $value;
                }
                if($value[2] == 0) $all_use = false;
            }
        }
        if(!$has){
            $body["s".$time] = array($time, $now, $speed);
        }
    }

    if(!$all_use){
        $i = 0;
        foreach ($body as $key => $value) {
            if($value[2] == 0){
                if($i == 0 && "s".$time == $key){
                    $r = true;
                    break;
                }
                $i++;
            }
        }
    }else{
        $body_ = array_sortby($body, 2, SORT_DESC);
        $first_key = key($body_);
        if("s".$time == $first_key) $r = true;
    }

    $body = serialize($body);
    $fp = @fopen($file, "w");
    @fwrite($fp, $body);
    @fclose($fp);

    return $r;

}


//查询快递进度
// {
//     "datetime": "2019-01-26 11:27:28",
//     "remark": "[郑州市]顺丰速运 已收取快件",
//     "zone": ""
// }
function getExpressTrack($com, $no, $tab, $id){

    global $cfg_juhe;

    $cfg_juhe = is_array($cfg_juhe) ? $cfg_juhe : unserialize($cfg_juhe);
    $key = $cfg_juhe['express'];

    if($key && $com && $no) {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://v.juhe.cn/exp/index?com=$com&no=$no&key=" . $key);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        $con = curl_exec($curl);
        curl_close($curl);

        $con = json_decode($con, true);
        if ($con['resultcode'] == 200) {
            $data = $con['result'];
            $list = serialize(array_reverse($data['list']));

            //物流信息不会发生变化时将数据存到订单信息表中
            if($data['status']){
                global $dsql;
                $sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `exp-track` = '$list' WHERE `id` = $id");
                $dsql->dsqlOper($sql, "update");
            }

            return $list;
        }

    }

}


/**
 * 读取缓存
 * disabled 禁用缓存，如会员中心信息列表
 */
function getCache($module, $sql, $time = 0, $param = array()){
    global $HN_memory;
    global $dsql;
    $cache = true;
    if(is_array($param)){
        extract($param);
    }else{
        $sign = $param;
    }

    if(gettype($sql) == "string"){
        $sql = str_replace("\t", " ", $sql);
        $sql = str_replace("\n", " ", $sql);
        $sql = str_replace("\r", " ", $sql);
        $sql = str_replace("  ", " ", $sql);

        $sql2 = strtolower($sql);
        $sign = $sign ? $sign : md5($sql2);
        $sign = $module . '_' . $sign;


        // 列表只缓存第一页的数据
        $limit = strstr($sql2, "limit");
        if($limit){
            if(strstr($limit, ",")){
                $limit_ = str_replace("limit", "", $limit);
                $limit_ = trim($limit_);
                $num = explode(",", $limit_);
                $s = (int)$num[0];
                $e = (int)$num[1];
                if($s >= $e && $s >= 10){
                    $cache = false;
                }
            }
        }
    }else{
        $sign = $module . ($sign ? "_".$sign : "");
    }

    $memberCache = empty($disabled) && $cache ? $HN_memory->get($sign) : FALSE;
    if($test){
        // var_dump($memberCache);die;
    }
    if($memberCache !== NULL && $memberCache !== FALSE){
        $results = $memberCache;
    }else{
        if(gettype($sql) == "string"){
            $results = $dsql->dsqlOper($sql, $type ? $type : "results");
        }else{
            $results = call_user_func($sql);
        }
        if($name && is_array($results)){
            $results = $results ? $results[0][$name] : "";
        }
        if($disabled || !$cache) return $results;
        // 如果结果为空，缓存时间强制设为300秒
        if(empty($results) && $time == 0) $time = 300;
        $HN_memory->set($sign, $results, $time);

        // 列表查询，保存这条缓存包含哪些数据
        if(empty($name) && $limit && $results && isset($results[0]['id'])){
            $now = time();
            $sign_key = $module."_key";
            $data = $HN_memory->get($sign_key);
            $data = $data ? $data : array();
            if($data){
                $has = false;
                foreach ($data as $key => $value) {
                    if($key == $sign){
                        $has = true;
                        $list = array_merge($data[$sign]['list'], array_column($results, 'id'));
                        $data[$key]['list'] = $list;

                    }elseif($HN_memory->get($key) === false){
                        unset($data[$key]);
                    }
                }
                if(!$has){
                    $data[$sign] = array(
                            'sql' => $sql,
                            'list' => array_column($results, 'id')
                        );
                }
            }else{
                $data[$sign] = array(
                    'sql' => $sql,
                    'list' => array_column($results, 'id')
                );
            }
            $HN_memory->set($sign_key, $data);
        }elseif($savekey){
            $sign_key = $module."_key";
            $save_sign = gettype($sql) == 'string' ? $sql : '';
            $data = $HN_memory->get($sign_key);
            $data = $data ? $data : array();
            if($data){
                if(!array_search($sign, $data)){
                    $data[$sign] = $save_sign;
                }
            }else{
                $data[$sign] = $save_sign;
            }
            $HN_memory->set($sign_key, $data);
        }

    }
    if(strstr($sign, 'detail') && count($results) == 1 && isset($results[0]['click'])){
        $results[0]['click']++;
        $HN_memory->set($sign, $results);
        $results[0]['click']--;
    }
    return $results;
}

/**
 * 清除缓存
 * sign=key 时清除所有结果集缓存
 */
function clearCache($module, $sign = "key"){
    global $HN_memory;
    if($sign == "key"){
        $sign_key = $module."_key";
        $data = $HN_memory->get($sign_key);
        if($data){
            foreach ($data as $key => $value) {
                $HN_memory->rm($key);
            }
        }
        // 清除分类名称缓存
        if(!strstr($module, "list")){
            for($i = 1; $i < 400; $i++){
                $HN_memory->rm($module."_".$i);
            }
        }

        $HN_memory->rm($sign_key);
        return;
    }
    $arr = is_array($sign) ? $sign : explode(",", $sign);
    foreach ($arr as $key => $value) {
        $HN_memory->rm($module . "_" . $value);
    }
}

/**
 * 检查缓存，结果集类型
 */
function checkCache($module, $ids){
    if(empty($ids)) return;
    global $HN_memory;
    $sign_key = $module."_key";
    $data = $HN_memory->get($sign_key);
    if($data){
        $count = count($data);
        $idArr = is_array($ids) ? $ids : explode(",", $ids);
        foreach ($idArr as $id) {
            foreach ($data as $key => $value) {
                if($value && is_array($value) && in_array($id, $value['list'])){
                    $HN_memory->rm($key);
                    unset($data[$key]);

                    // 遍历清除 sql中limit左边部分相同的缓存
                    $sql = explode("limit", $value['sql']);
                    $sql = $sql[0];
                    foreach ($data as $k => $v) {
                        if(strstr($v['sql'], $sql)){
                            $HN_memory->rm($k);
                            unset($data[$k]);
                        }
                    }
                }
            }
        }
        if($data){
            if(count($data) != $count){
                $HN_memory->set($sign_key, $data);
            }
        }else{
            $HN_memory->rm($sign_key);
        }
    }
}


/**
 * 发布新信息时更新缓存
 */
function updateCache($module, $time = 0){
    global $dsql;
    global $HN_memory;

    $sign_key = $module."_key";
    $data = $HN_memory->get($sign_key);
    if($data){
        foreach ($data as $key => $value) {
            $sql = $value['sql'];
            $res = $dsql->dsqlOper($sql, "results");
            $HN_memory->set($key, $res, $time);
        }
    }
}

/**
 * 取每个5分钟整的时间戳
 */
function getTimeStep(){
    $time = time();
    $hour = date("H");
    $minute = date("i");
    $minute_ = 0;
    $step = 5;
    if($minute > 10){
        $g = $minute % 10;
        $minute_ = $minute - $g;
        if($g >= $step){
            $minute_ += $step;
        }
    }
    return strtotime(date("Y-m-d {$hour}:{$minute_}"));
}

//获取虚拟号码
function getVisualPhone($caller,$callee){
	$dsql = new dsql($dbo);
	$sql = $dsql->SetQuery("select phone from #@__relationphone r where r.state=1 and not exists(select * from #@__phonebind pb where now()<=pb.expire and pb.state=1 and pb.relationphone=r.phone and (pb.caller='{$caller}' or pb.callee='{$caller}' or pb.caller='{$callee}' or pb.callee='{$callee}'))");
	$phoneList = $dsql->dsqlOper($sql,"results");
	if(!is_array($phoneList))	return false;
	shuffle($phoneList);
	$phoneInfo = array_shift($phoneList);
	return $phoneInfo['phone'];
}

//异步请求
function asynExec($domainName, $domainPort, $detailPage){
	$fp = fsockopen($domainName, $domainPort, $errNo, $errStr, 30);
	if (!$fp){
		return false;
	}else{
		stream_set_blocking($fp,true);      //开启了手册上说的非阻塞模式
		stream_set_timeout($fp,1);
		$out = "GET {$detailPage} HTTP/1.1\r\n";
		$out .= "Host: {$domainName}\r\n";
		$out .= "Connection: Close\r\n\r\n";
					
		fwrite($fp, $out);
		usleep(1000);
		//while(!feof($fp)){
            //echo fgets($fp, 128);
        //}
		fclose($fp);
	}
}
