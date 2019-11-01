<?php
if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 外卖自动派单
 *
 * 规则：
 * 1. 自动分配所有已确认的订单
 * 2. 按照骑手离商家位置最近并且手上没有订单时优先派送
 * 3. 如果骑手手上有订单，则将新订单分派给其他骑手
 *
 * @version        $Id: waimai_autoDispatch.php 2017-6-8 下午16:55:10 $
 * @package        HuoNiao.cron
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

$file = HUONIAOINC . "/config/waimai.inc.php";
if(file_exists($file)){
    include HUONIAOINC . "/config/waimai.inc.php";
}

global $maxCount;
global $maxJuli;

$maxCount = empty($custom_autoDispatchCount) ? 5 : $custom_autoDispatchCount;
$maxJuli = empty($custom_autoDispatchJuli) ? 2 : $custom_autoDispatchJuli;

include_once HUONIAOROOT."/api/handlers/siteConfig.class.php";

//派单前的计算
function autoDispatch(){
    global $dsql;
    $siteConfigService = new siteConfig();
    $cityArr = $siteConfigService->siteCity();

    if(!$cityArr){
        $cityArr = array(
            0 => array(
                "cid" => 0
            ),
        );
    }

    foreach ($cityArr as $cityInfo) {

        $cityid = $cityInfo['cid'];

        if($cityid){
            $where = " AND `cityid` = ".$cityid;
        }else{
            $where = "";
        }

        //查询骑手信息及订单量
        $sql = $dsql->SetQuery("SELECT c.`id`, c.`lng`, c.`lat` FROM `#@__waimai_courier` c WHERE c.`state` = 1".$where);
        $courierArr = $dsql->dsqlOper($sql, "results");

        //查询订单信息
        $sql = $dsql->SetQuery("SELECT o.`id`, s.`coordX`, s.`coordY` FROM `#@__waimai_order` o LEFT JOIN `#@__waimai_shop` s ON s.`id` = o.`sid` WHERE o.`state` = 3 AND s.`merchant_deliver` = 0".$where);
        $orderArr = $dsql->dsqlOper($sql, "results");

        $treeArr = array();
        foreach ($orderArr as $key => $value) {
            foreach ($courierArr as $k => $v) {
                array_push($treeArr, array(
                    "courierID"  => $v['id'],
                    "courierLng" => $v['lng'],
                    "courierLat" => $v['lat'],
                    "orderID"    => $value['id'],
                    "shopLng"    => $value['coordX'],
                    "shopLat"    => $value['coordY'],
                    "juli"       => getDistance($v['lat'], $v['lng'], $value['coordY'], $value['coordX'])
                ));
            }
        }

        //将相同订单号的数组拼接
        $newArr = array();
        foreach ($treeArr as $key => $value) {
            if(!$newArr[$value['orderID']]){
                $newArr[$value['orderID']] = array();
            }
            array_push($newArr[$value['orderID']], $value);
        }

        //将相同订单的数组分配给最合适的骑手
        foreach ($newArr as $key => $value) {
            autoDispatchCourier($value);
        }

    }

}


//派单给骑手
function autoDispatchCourier($arr){
    global $dsql;

    if($arr){
        $oArr = array();
        $time = GetMkTime(time());
        //这次主要计算骑手当前手上有多少订单
        foreach ($arr as $key => $value) {
            $sql = $dsql->SetQuery("SELECT count(`id`) count FROM `#@__waimai_order` WHERE (`state` = 4 OR `state` = 5) AND `peisongid` = " . $value['courierID']);
            $ret = $dsql->dsqlOper($sql, "results");
            $value['orderCount'] = $ret[0]['count'];
            array_push($oArr, $value);
        }

        $kindex = 0;
        $currArr = $oArr[0];
        if(count($oArr) > 1){
            foreach ($oArr as $key => $value) {
                if($key > 0 && ($value['juli'] < $currArr['juli'] && ($value['orderCount'] < $currArr['orderCount'] || $value['orderCount'] == 0))){
                    $kindex = $key;
                    $currArr = $value;
                }
            }
        }

        //每个配送员最多分配5个订单，并且是2公里范围以内的订单
        global $maxCount;
        global $maxJuli;

        if($currArr['orderCount'] < $maxCount && $currArr['juli'] < ($maxJuli * 1000) ){
            $courier = $currArr['courierID'];
            $orderid = $currArr['orderID'];
            $sql = $dsql->SetQuery("UPDATE `#@__waimai_order` SET `state` = 4, `peisongid` = '$courier', `peidate` = '$time' WHERE `state` = 3 AND `id` = $orderid");
            $ret = $dsql->dsqlOper($sql, "update");

            if($ret == "ok"){

                sendapppush($courier, "您有新的配送订单", "点击查看", "", "newfenpeiorder");

                //消息通知用户
                $sql_ = $dsql->SetQuery("SELECT o.`uid`, o.`ordernumstore`, o.`pubdate`, o.`food`, o.`amount`, s.`shopname`, c.`name`, c.`phone` FROM `#@__waimai_order` o LEFT JOIN `#@__waimai_shop` s ON s.`id` = o.`sid` LEFT JOIN `#@__waimai_courier` c ON c.`id` = o.`peisongid` WHERE o.`id` = $orderid");
                $ret_ = $dsql->dsqlOper($sql_, "results");
                if($ret_){
                    $data = $ret_[0];

                    $uid           = $data['uid'];
                    $ordernumstore = $data['ordernumstore'];
                    $pubdate       = $data['pubdate'];
                    $food          = unserialize($data['food']);
                    $amount        = $data['amount'];
                    $shopname      = $data['shopname'];
                    $name          = $data['name'];
                    $phone         = $data['phone'];

                    $foods = array();
                    foreach ($food as $key => $value) {
                        array_push($foods, $value['title'] . " " . $value['count'] . "份");
                    }

                    $param = array(
                        "service"  => "member",
                        "type"     => "user",
                        "template" => "orderdetail",
                        "module"   => "waimai",
                        "id"       => $orderid
                    );

                    //自定义配置
                    $config = array(
                        "ordernum" => $shopname.$ordernumstore,
                        "orderdate" => date("Y-m-d H:i:s", $pubdate),
                        "orderinfo" => join(" ", $foods),
                        "orderprice" => $amount,
                        "peisong" => $name . "，" . $phone,
                        "fields" => array(
                            'keyword1' => '订单号',
                            'keyword2' => '订单详情',
                            'keyword3' => '订单金额',
                            'keyword4' => '配送人员'
                        )
                    );

                    updateMemberNotice($uid, "会员-订单配送提醒", $param, $config);
                }

            }

        }else{
            array_splice($arr, $kindex, 1);
            autoDispatchCourier($arr);
        }

    }
}


autoDispatch();







/**
 * 二维数组根据字段进行排序
 * @params array $array 需要排序的数组
 * @params string $field 排序的字段
 * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
 */
 function arraySequence($array, $field, $sort = 'SORT_DESC'){
    $arrSort = array();
    foreach ($array as $uniqid => $row) {
        foreach ($row as $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $array);
    return $array;
}
