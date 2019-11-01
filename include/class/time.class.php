<?php  if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 时间戳插件
 *
 * @version        $Id: time.class.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.class
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

/**
 *  返回格林威治标准时间
 *
 * @param     string  $format  字符串格式
 * @param     string  $timest  时间基准
 * @return    string
 */
if (!function_exists('MyDate')){
    function MyDate($format='Y-m-d H:i:s', $timest=0){
        global $cfg_cli_time;
        $addtime = $cfg_cli_time * 3600;
        if(empty($format)){
            $format = 'Y-m-d H:i:s';
        }
        return gmdate($format, $timest+$addtime);
    }
}


/**
 * 从普通时间转换为Linux时间截
 *
 * @param     string   $dtime  普通时间
 * @return    string
 */
if (!function_exists('GetMkTime')){
    function GetMkTime($dtime){
        if(!preg_match("/[^0-9]/", $dtime)){
            return $dtime;
        }
        $dtime = trim($dtime);
        $dt = Array(1970, 1, 1, 0, 0, 0);
        $dtime = preg_replace("/[\r\n\t]|日|秒/", " ", $dtime);
        $dtime = str_replace("年", "-", $dtime);
        $dtime = str_replace("月", "-", $dtime);
        $dtime = str_replace("时", ":", $dtime);
        $dtime = str_replace("分", ":", $dtime);
        $dtime = trim(preg_replace("/[ ]{1,}/", " ", $dtime));
        $ds = explode(" ", $dtime);
        $ymd = explode("-", $ds[0]);
        if(!isset($ymd[1])){
            $ymd = explode(".", $ds[0]);
        }
        if(isset($ymd[0])){
            $dt[0] = $ymd[0];
        }
        if(isset($ymd[1])) $dt[1] = $ymd[1];
        if(isset($ymd[2])) $dt[2] = $ymd[2];
        if(strlen($dt[0])==2) $dt[0] = '20'.$dt[0];
        if(isset($ds[1])){
            $hms = explode(":", $ds[1]);
            if(isset($hms[0])) $dt[3] = $hms[0];
            if(isset($hms[1])) $dt[4] = $hms[1];
            if(isset($hms[2])) $dt[5] = $hms[2];
        }
        foreach($dt as $k=>$v){
            $v = preg_replace("/^0{1,}/", '', trim($v));
            if($v==''){
                $dt[$k] = 0;
            }
        }
        $mt = mktime($dt[3], $dt[4], $dt[5], $dt[1], $dt[2], $dt[0]);
        if(!empty($mt)){
              return $mt;
        }else{
              return time();
        }
    }
}


/**
 *  减去时间
 *
 * @param     int  $ntime  当前时间
 * @param     int  $ctime  减少的时间
 * @return    int
 */
if (!function_exists('SubDay')){
    function SubDay($ntime, $ctime){
        $dayst = 3600 * 24;
        $cday = ceil(($ntime-$ctime)/$dayst);
        return $cday;
    }
}


/**
 *  增加天数
 *
 * @param     int  $ntime  当前时间
 * @param     int  $aday   增加天数
 * @return    int
 */
if (!function_exists('AddDay')){
    function AddDay($ntime, $aday){
        $dayst = 3600 * 24;
        $oktime = $ntime + ($aday * $dayst);
        return $oktime;
    }
}


/**
 *  返回格式化(Y-m-d H:i:s)的是时间
 *
 * @param     int    $mktime  时间戳
 * @return    string
 */
if (!function_exists('GetDateTimeMk')){
    function GetDateTimeMk($mktime){
        return MyDate('Y-m-d H:i:s',$mktime);
    }
}

/**
 *  返回格式化(Y-m-d)的日期
 *
 * @param     int    $mktime  时间戳
 * @return    string
 */
if (!function_exists('GetDateMk')){
    function GetDateMk($mktime){
        if($mktime=="0") return "暂无";
        else return MyDate("Y-m-d", $mktime);
    }
}


/**
 *  将时间转换为距离现在的精确时间
 *
 * @param     int   $seconds  秒数
 * @return    string
 */
if (!function_exists('FloorTime')){
    function FloorTime($seconds, $n = 1){
        global $langData;
        $days = floor(($seconds/86400));
        $hours = floor(($seconds/3600)%24);
        $minutes = floor(($seconds/60)%60);
        $second = floor($seconds%60);
        if($second <= 60) $times = $langData['siteConfig'][13][47];  //刚刚
        if($minutes >= 1) $times = $minutes.$langData['siteConfig'][13][41];  //分钟前
        if($hours >= 1) $times = $hours.$langData['siteConfig'][13][42];  //小时前
        if($days >= 1)  $times = $days.$langData['siteConfig'][13][43];  //天前
        if($days > 30) {
          if($n == 1){
              $times = date("Y-m-d", (GetMkTime(time() - $seconds)));
          }elseif ($n == 2){
              $times = date("Y-m-d H:i:s", (GetMkTime(time() - $seconds)));
          }elseif ($n == 3){
              $times = date("m-d", (GetMkTime(time() - $seconds)));
          }
        }
        return $times;
    }
}

if (!function_exists('FloorTimeByTemp')){
    function FloorTimeByTemp($params, $smarty = array()){
        global $langData;
        $timestamp = $params['timestamp'];
        $format = $params['format'];
        $time = time();

        return FloorTime($time - $timestamp, $format);
    }
}

/**
 * 获取当前时辰
 *
 */
if (!function_exists('getNowHour')){
    function getNowHour(){
      global $langData;
        $h=date('G');
        if ($h<11) return $langData['siteConfig'][14][0];  //早上好
        else if ($h<13) return $langData['siteConfig'][14][1];  //中午好
        else if ($h<17) return $langData['siteConfig'][14][2];  //下午好
        else return $langData['siteConfig'][14][3];  //晚上好
    }
}

/**
 * 根据天数转换为精确时间
 *
 */
if (!function_exists('FloorDay')){
    function FloorDay($params, $smarty = array()){
      global $langData;
      $day = $params['day'];

      $return = "";
      $year = floor(($day/360)%12);
      $month = floor(($day/30));
      if($day >= 1) $return = $day . $langData['siteConfig'][13][6];  //天
      if($month >= 1) $return = $month . $langData['siteConfig'][13][31];  //个月
      if($year >= 1) $return = $year . $langData['siteConfig'][13][14];  //年

      return $return;
    }
}

/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
 * @param string $day1
 * @param string $day2
 * @return number
 */
if (!function_exists('diffBetweenTwoDays')){
    function diffBetweenTwoDays($day1, $day2){
      $second1 = strtotime($day1);
      $second2 = strtotime($day2);

      if ($second1 < $second2) {
        $tmp = $second2;
        $second2 = $second1;
        $second1 = $tmp;
      }
      return ($second1 - $second2) / 86400;
    }
}
