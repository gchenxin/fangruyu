<?php
use function Qiniu\json_decode;

if (!defined('HUONIAOINC')) exit('Request Error!');

/**
 * 直播模块API接口
 *
 * @version        $Id: live.class.php 2017-6-01 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2015, HuoNiao, Inc.
 * @link           http://www.huoniao.co/
 */
class live
{
    private $param;  //参数

    /**
     * 构造函数
     *
     * @param string $action 动作名
     */
    public function __construct($param = array())
    {
        global $dsql;
        $this->param = $param;
        include_once(HUONIAOROOT . "/api/live/alilive/alilive.class.php");
        $this->aliLive = new Alilive();

        // $custom_rongKeyID = $custom_rongKeySecret = "";
        // $sql              = $dsql->SetQuery("SELECT * FROM `#@__app_config` LIMIT 1");
        // $ret              = $dsql->dsqlOper($sql, "results");
        // if ($ret) {
        //     $data                 = $ret[0];
        //     $custom_rongKeyID     = $data['rongKeyID'];
        //     $custom_rongKeySecret = $data['rongKeySecret'];
        // }
        // $appKey    = $custom_rongKeyID;
        // $appSecret = $custom_rongKeySecret;
        //
        // include_once(HUONIAOINC . "/class/imserver/im.class.php");
        // $this->RongCloud = new im($appKey, $appSecret);
    }

    /**
     * 直播基本参数
     * @return array
     */
    public function config()
    {

        require(HUONIAOINC . "/config/live.inc.php");

        global $cfg_fileUrl;              //系统附件默认地址
        global $cfg_uploadDir;            //系统附件默认上传目录
        // global $customFtp;                //是否自定义FTP
        // global $custom_ftpState;          //FTP是否开启
        // global $custom_ftpUrl;            //远程附件地址
        // global $custom_ftpDir;            //FTP上传目录
        // global $custom_uploadDir;         //默认上传目录
        global $cfg_basehost;             //系统主域名
        global $cfg_hotline;              //系统默认咨询热线

        // global $customChannelName;        //模块名称
        // global $customLogo;               //logo使用方式
        global $cfg_weblogo;              //系统默认logo地址
        // global $customLogoUrl;            //logo地址
        // global $customSubDomain;          //访问方式
        // global $customChannelSwitch;      //模块状态
        // global $customCloseCause;         //模块禁用说明
        // global $customSeoTitle;           //seo标题
        // global $customSeoKeyword;         //seo关键字
        // global $customSeoDescription;     //seo描述
        // global $hotline_config;           //咨询热线配置
        // global $customHotline;            //咨询热线
        // global $submission;               //投稿邮箱
        // global $customAtlasMax;           //图集数量限制
        // global $customTemplate;           //模板风格
        //
        // global $customUpload;             //上传配置是否自定义
        global $cfg_softSize;             //系统附件上传限制大小
        global $cfg_softType;             //系统附件上传类型限制
        global $cfg_thumbSize;            //系统缩略图上传限制大小
        global $cfg_thumbType;            //系统缩略图上传类型限制
        global $cfg_atlasSize;            //系统图集上传限制大小
        global $cfg_atlasType;            //系统图集上传类型限制

        // global $custom_softSize;          //附件上传限制大小
        // global $custom_softType;          //附件上传类型限制
        // global $custom_thumbSize;         //缩略图上传限制大小
        // global $custom_thumbType;         //缩略图上传类型限制
        // global $custom_atlasSize;         //图集上传限制大小
        // global $custom_atlasType;         //图集上传类型限制

        global $cfg_standard;
        global $cfg_narrowband;


        //如果上传设置为系统默认，则以下参数使用系统默认
        if ($customUpload == 0) {
            $custom_softSize  = $cfg_softSize;
            $custom_softType  = $cfg_softType;
            $custom_thumbSize = $cfg_thumbSize;
            $custom_thumbType = $cfg_thumbType;
            $custom_atlasSize = $cfg_atlasSize;
            $custom_atlasType = $cfg_atlasType;
        }

        $hotline = $hotline_config == 0 ? $cfg_hotline : $customHotline;

        $params = !empty($this->param) && !is_array($this->param) ? explode(',', $this->param) : "";

        // $domainInfo = getDomain('article', 'config');
        // $customChannelDomain = $domainInfo['domain'];
        // if($customSubDomain == 0){
        // 	$customChannelDomain = "http://".$customChannelDomain;
        // }elseif($customSubDomain == 1){
        // 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
        // }elseif($customSubDomain == 2){
        // 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
        // }
        //include HUONIAOINC.'/siteModuleDomain.inc.php';
        $customChannelDomain = getDomainFullUrl('live', $customSubDomain);

        //分站自定义配置
        $ser = 'live';
        global $siteCityAdvancedConfig;
        if($siteCityAdvancedConfig && $siteCityAdvancedConfig[$ser]){
            if($siteCityAdvancedConfig[$ser]['title']){
                $customSeoTitle = $siteCityAdvancedConfig[$ser]['title'];
            }
            if($siteCityAdvancedConfig[$ser]['keywords']){
                $customSeoKeyword = $siteCityAdvancedConfig[$ser]['keywords'];
            }
            if($siteCityAdvancedConfig[$ser]['description']){
                $customSeoDescription = $siteCityAdvancedConfig[$ser]['description'];
            }
            if($siteCityAdvancedConfig[$ser]['logo']){
                $customLogoUrl = $siteCityAdvancedConfig[$ser]['logo'];
            }
            if($siteCityAdvancedConfig[$ser]['hotline']){
                $hotline = $siteCityAdvancedConfig[$ser]['hotline'];
            }
        }

        $return = array();
        if (!empty($params) > 0) {

            foreach ($params as $key => $param) {
                if ($param == "channelName") {
                    $return['channelName'] = $customChannelName;
                } elseif ($param == "logoUrl") {

                    //自定义LOGO
                    if ($customLogo == 1) {
                        $customLogoPath = getFilePath($customLogoUrl);
                    } else {
                        $customLogoPath = getFilePath($cfg_weblogo);
                    }
                    $return['logoUrl'] = $customLogoPath;
                } elseif ($param == "subDomain") {
                    $return['subDomain'] = $customSubDomain;
                } elseif ($param == "channelDomain") {
                    $return['channelDomain'] = $customChannelDomain;
                } elseif ($param == "channelSwitch") {
                    $return['channelSwitch'] = $customChannelSwitch;
                } elseif ($param == "closeCause") {
                    $return['closeCause'] = $customCloseCause;
                } elseif ($param == "title") {
                    $return['title'] = $customSeoTitle;
                } elseif ($param == "keywords") {
                    $return['keywords'] = $customSeoKeyword;
                } elseif ($param == "description") {
                    $return['description'] = $customSeoDescription;
                } elseif ($param == "hotline") {
                    $return['hotline'] = $hotline;
                } elseif ($param == "submission") {
                    $return['submission'] = $submission;
                } elseif ($param == "atlasMax") {
                    $return['atlasMax'] = $customAtlasMax;
                } elseif ($param == "template") {
                    $return['template'] = $customTemplate;
                } elseif ($param == "touchTemplate") {
                    $return['touchTemplate'] = $customTouchTemplate;
                } elseif ($param == "softSize") {
                    $return['softSize'] = $custom_softSize;
                } elseif ($param == "softType") {
                    $return['softType'] = $custom_softType;
                } elseif ($param == "thumbSize") {
                    $return['thumbSize'] = $custom_thumbSize;
                } elseif ($param == "thumbType") {
                    $return['thumbType'] = $custom_thumbType;
                } elseif ($param == "atlasSize") {
                    $return['atlasSize'] = $custom_atlasSize;
                } elseif ($param == "atlasType") {
                    $return['atlasType'] = $custom_atlasType;
                } elseif ($param == "listRule") {
                    $return['listRule'] = $custom_listRule;
                } elseif ($param == "detailRule") {
                    $return['detailRule'] = $custom_detailRule;
                }
            }

        } else {

            //自定义LOGO
            if ($customLogo == 1) {
                $customLogoPath = getFilePath($customLogoUrl);
            } else {
                $customLogoPath = getFilePath($cfg_weblogo);
            }

            $return['channelName']   = $customChannelName;
            $return['logoUrl']       = $customLogoPath;
            $return['subDomain']     = $customSubDomain;
            $return['channelDomain'] = $customChannelDomain;
            $return['channelSwitch'] = $customChannelSwitch;
            $return['closeCause']    = $customCloseCause;
            $return['title']         = $customSeoTitle;
            $return['keywords']      = $customSeoKeyword;
            $return['description']   = $customSeoDescription;
            $return['hotline']       = $hotline;
            $return['submission']    = $submission;
            $return['atlasMax']      = $customAtlasMax;
            $return['template']      = $customTemplate;
            $return['touchTemplate'] = $customTouchTemplate;
            $return['softSize']      = $custom_softSize;
            $return['softType']      = $custom_softType;
            $return['thumbSize']     = $custom_thumbSize;
            $return['thumbType']     = $custom_thumbType;
            $return['atlasSize']     = $custom_atlasSize;
            $return['atlasType']     = $custom_atlasType;
            $return['listRule']      = $custom_listRule;
            $return['detailRule']    = $custom_detailRule;
        }

        return $return;

    }

    /**
     * 直播分类
     * @return array
     */
    public function type()
    {
        global $dsql;
        $type = $page = $pageSize = $where = "";

        if (!empty($this->param)) {
            if (!is_array($this->param)) {
                return array("state" => 200, "info" => '格式错误！');
            } else {
                $type     = (int)$this->param['type'];
                $page     = (int)$this->param['page'];
                $pageSize = (int)$this->param['pageSize'];
                $son      = $this->param['son'] == 0 ? false : true;
            }
        }
        $results = $dsql->getTypeList($type, "livetype", $son, $page, $pageSize);
        if ($results) {
            $list = array();
            foreach ($results as $key => $row) {
                $param         = array(
                    "service" => "live",
                    "template" => "livelist",
                    "typeid" => $row['id']
                );
                $row["newurl"] = getUrlPath($param);
                $list[$key]    = $row;
            }
            return $list;
        }
    }

    /**
     * 直播列表详细信息
     * @return array
     */
    public function typeDetail()
    {
        global $dsql;
        $typeDetail = array();
        $typeid     = $this->param;
        $archives   = $dsql->SetQuery("SELECT `id`, `typename`, `seotitle`, `keywords`, `description` FROM `#@__livetype` WHERE `id` = " . $typeid);
        $results    = $dsql->dsqlOper($archives, "results");
        if ($results && is_array($results)) {
            $param             = array(
                "service" => "live",
                "template" => "livelist",
                "typeid" => $typeid
            );
            $results[0]["url"] = getUrlPath($param);
            return $results;
        }
    }

    /**
     * 直播列表
     * 1、正在直播（正在直播的）
     * 2、直播分类直播列表（正在直播的）
     * 3、主播直播列表（结束直播的和正在直播的）
     */
    public function alive()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $typeid   = $type = $title = $where = $where1 = $inner = "";
        $uid   = $userLogin->getMemberID();

        if (!empty($this->param)) {
            if (!is_array($this->param)) {
                return array("state" => 200, "info" => '格式错误！');
            } else {
                $typeid = $this->param['typeid'];
                $userid = $this->param['uid'];
                $title    = $this->param['title'];
                $type     = $this->param['type'];
                $lng      = $this->param['lng'];
				$lat      = $this->param['lat'];
                $u        = $this->param['u'];
                $state    = $this->param['state'];
                $orderby  = $this->param['orderby'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
                $typename = $this->param['typename'];
                $myfollow = (int)$this->param['myfollow'];//我关注的主播发布的
                $mybooking = (int)$this->param['mybooking'];//我的预约
                $mo = $this->param['mo'];
            }
        }

        if($myfollow){
            if($uid < 1){
                return array("state" => 200, "info" => '您还没有登录');
            }
            // $inner = "INNER JOIN (SELECT f.fid FROM huoniao_member_follow f WHERE f.for = ''AND f.tid = $uid ) AS tmp";
            $inner = "INNER JOIN (SELECT a.`uid` FROM `#@__live_anchor` a LEFT JOIN `#@__member_follow` f ON f.`fid` = a.`uid` WHERE f.`tid` = $uid AND f.`for` = '') AS tmp";
            $where .= " AND tmp.`uid` = l.`user`";
        }
        if(!$myfollow && $mybooking){
            if($uid < 1){
                return array("state" => 200, "info" => '您还没有登录');
            }
            $inner = "INNER JOIN (SELECT b.`aid` FROM `#@__live_booking` b WHERE b.`uid` = $uid) AS tmp";
            $where .= " AND tmp.`aid` = l.`id`";
        }else{
            $mybooking = 0;
        }

        if($typename){
            $types = $dsql->SetQuery("SELECT `id` FROM `#@__livetype` WHERE `typename` LIKE '%$typename%'");
            $type_ret = $dsql->dsqlOper($types, "results");
            if($type_ret){
                $typeid = $type_ret[0]['id'];
            }
        }

        if ($u) {
            $where .= " AND l.`user` = " . $uid;
        }else{
            $where .= " AND l.`arcrank` = 1";
        }

        if ($state != '') {
            $where .= " AND l.`state` = " . $state;
        }


        if (!empty($type)) {
            if ($type == 1) {
                $where .= " and l.`state` in (1,2)";
            } elseif ($type == 2) {//未直播的、结束直播的和正在直播的
                $where .= " and l.`state` in (0,1,2)";
            } elseif ($type == 3) {//结束直播的和正在直播的
                $where .= " and l.`state` in (1,2)";
            } elseif ($type == 4) {//正在直播的
                $where .= " and l.`state` = 1";
            }elseif ($type == 5) {//精彩回放
                $where .= " and l.`state` = 2";
            }
        }

        //$userid = $userLogin->getMemberID();
        if (!empty($userid) && $type != 1) {
            $where .= " and l.`user` = '$userid'";
        }
        if (!empty($typeid)) {
            $where .= " and l.`typeid` = '$typeid'";
        }

        if (!empty($title)) {

            siteSearchLog("live", $title);

            if($orderby == 3 || $orderby == 'active'){
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` like '%$title%' or `nickname` like '%$title%' or `company` like '%$title%'");
                $retname = $dsql->dsqlOper($sql, "results");
                if(!empty($retname) && is_array($retname)){
                    $list_name = array();
                    foreach ($retname as $key => $value) {
                        $list_name[] = $value["id"];
                    }
                    $idList = join(",", $list_name);
                    $where .= " AND  l.`user` in ($idList) ";
                }
            }else{
                $where .= " AND l.`title` like '%" . $title . "%'";
            }
        }
        if($mo){
            $where .= " and l.`way` = 0 ";
        }

        //查询距离
		if((!empty($lng))&&(!empty($lat))){
            $select="(2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*(".$lat."-l.`lat`)/360),2)+COS(3.1415926535898*".$lat."/180)* COS(l.`lat` * 3.1415926535898/180)*POW(SIN(3.1415926535898*(".$lng."-l.`lng`)/360),2))))*1000 AS distance,";
        }else{
            $select="";
        }

        if ($type == 1) {
            $order = " ORDER BY l.`state` asc, l.`id` DESC";
        } elseif ($type == 2) {
            $order = " ORDER BY FIELD(l.`state`, 0, 1, 2), l.`id` DESC";
        } elseif ($type == 3) {
            $order = " ORDER BY FIELD(l.`state`, 1, 2), l.`id` DESC";
        }

        if ($orderby == 'click' || $orderby == '1') {
            $order = " ORDER BY l.`click` DESC, l.`id` DESC";
        }else if($orderby == 'time' || $orderby == '2'){
            $order = " ORDER BY l.`ftime` DESC, l.`id` DESC";
        }elseif($orderby == "active" || $orderby == '3'){//直播最多的用户
			$order = " GROUP BY l.`user` order by count(`click`) desc, count(`id`) desc";
        }elseif ($orderby == 4) {
            if((!empty($lng))&&(!empty($lat))){
                $order = " ORDER BY `distance` ASC";
            }
        }elseif ($orderby == 5) {
            $order = " ORDER BY FIELD(l.`state`, 1, 2), l.`id` DESC, l.`click` DESC, l.`id` DESC";
        }
        
        

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;
        if($orderby == "active" || $orderby == '3'){
            $archives_count = $dsql->SetQuery("SELECT count(distinct `user`) count FROM `#@__livelist` l $inner WHERE 1=1" . $where);
        }else{
            $archives_count = $dsql->SetQuery("SELECT count(`id`) count FROM `#@__livelist` l $inner WHERE 1=1" . $where);
        }

        //总条数
        $totalResults = $dsql->dsqlOper($archives_count, "results");
        $totalResults = $totalResults[0]['count'];
        $totalCount   = (int)$totalResults;

        //总分页数
        $totalPage = ceil($totalCount / $pageSize);
        if ($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $archives = $dsql->SetQuery("SELECT l.`id`,l.`way`,l.`user`,l.`up`,l.`lng`,l.`lat`,l.`title`,l.`typeid`,l.`starttime`,l.`litpic`,l.`click`,l.`state`,l.`ftime`,l.`livetime`,l.`arcrank`,l.`pulltype`,l.`pullurl_pc`,l.`pullurl_touch`,l.`state`,l.`ossurl`, ".$select." l.`streamname` FROM `#@__livelist` l $inner WHERE 1=1" . $where);

        $atpage  = $pageSize * ($page - 1);
        $limit   .= " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives .  $where . $order .$limit , "results");
        if ($results) {
            foreach ($results as $key => $val) {
                $list[$key]['id']     = $val['id'];
                $list[$key]['user']   = $val['user'];
                $list[$key]['title']  = $val['title'];
                $list[$key]['typeid'] = $val['typeid'];
                $list[$key]['lng']    = $val['lng'];
                $list[$key]['lat']    = $val['lat'];
                if (!empty($val['litpic'])) {
                    if (strpos($val['litpic'], 'images')) {
                        $list[$key]['litpic'] = $val['litpic'];
                    } else {
                        $list[$key]['litpic'] = getFilePath($val['litpic']);
                    }
                } else {
                    $list[$key]['litpic'] = '/static/images/404.jpg';
                }
                $list[$key]['distance']  = $val['distance'] > 1000 ? sprintf("%.1f", $val['distance'] / 1000) . $langData['siteConfig'][13][23] : sprintf("%.1f", $val['distance']) . $langData['siteConfig'][13][22];  //距离   //千米  //米
				if(strpos($list[$key]['distance'],'千米')){
					$list[$key]['distance'] = str_replace("千米",'km',$list[$key]['distance']);
				}elseif(strpos($list[$key]['distance'],'米')){
					$list[$key]['distance'] = str_replace("米",'m',$list[$key]['distance']);
				}

                $sql  = $dsql->SetQuery("SELECT `typename` FROM `#@__livetype` where id = '".$val['typeid']."'");
                $ret  = $dsql->dsqlOper($sql, "results");
                $list[$key]['typename'] = !empty($ret[0]['typename']) ? $ret[0]['typename'] : '';

                //$list[$key]['litpic'] = !empty($val['litpic']) ? getFilePath($val['litpic']) : '/static/images/404.jpg';
                $list[$key]['click'] = $val['click'];
                $list[$key]['up']    = $val['up'];
                $list[$key]['ftimes']= $val['ftime'];
                $list[$key]['state'] = $val['state'];
                $list[$key]['ftime'] = !empty($val['ftime']) ? date("Y-m-d H:i:s", $val['ftime']) : '无';
                if($val['state'] == 2){
                    $fenzhong = (int)($val['livetime'] / 1000 / 60);
                    $second = $val['livetime'] / 1000 % 60;
                    $list[$key]['times'] = $fenzhong . ':'.($second > 10 ? $second : '0'.$second);
                }

                //会员信息
                $member                 = getMemberDetail($val['user']);
                $list[$key]['nickname'] = !empty($member['username']) ? cn_substrR($member['username'], 15) : cn_substrR($member['nickname'], 5);
                $list[$key]['photo']    = !empty($member['photo']) ? getFilePath($member['photo']) : '/static/images/noPhoto_40.jpg';
                $list[$key]['certifyState'] = $member['certifyState'];

                if (isMobile()) {
                    if ($val['way'] == 1) {
                        $param = array(
                            "service" => "live",
                            "template" => "detail",
                            "id" => $val['id']
                        );
                    } else {
                        $param = array(
                            "service" => "live",
                            "template" => "h_detail",
                            "id" => $val['id']
                        );
                    }
                } else {
                    $param = array(
                        "service" => "live",
                        "template" => "detail",
                        "id" => $val['id']
                    );
                }

                $urlparam             = array(
                    "service" => "member",
                    "type" => "user",
                    "template" => "livedetail",
                );
                $list[$key]['newurl'] = getUrlPath($urlparam) . '?id=' . $val['id'];
                $list[$key]['url']    = getUrlPath($param);
                if($u){
                    $list[$key]['arcrank'] = $val['arcrank'];
                }

                //播放地址
                if($val['pulltype']==1){
                    $list[$key]['mp4url']  = $val['pullurl_pc'];
                    $list[$key]['m3u8url'] = $val['pullurl_touch'];
                    $list[$key]['playurl'] = isMobile() ? $val['pullurl_touch'] : $val['pullurl_pc'];
                }else{
                    if($val['state']==2){
                        include HUONIAOINC . "/config/live.inc.php";
                        if(empty($val['ossurl'])){
                            $this->param = $val['streamname'];
                            $Pulldetail  = $this->describeLiveStreamRecordIndexFiles();
                            if ($Pulldetail['state'] == 100 && is_array($Pulldetail['info']['RecordIndexInfoList']['RecordIndexInfo'])) {
                                $RecordIndexInfo = $Pulldetail['info']['RecordIndexInfoList']['RecordIndexInfo'];
                                $mp4File         = $m3u8File = '';

                                $OssObject = "";
                                $Duration = 0;
                                foreach ($RecordIndexInfo as $key => $value) {
                                    if (strstr($value['OssObject'], 'm3u8')) {
                                        $m3u8File  = $custom_server . $value['OssObject'];
                                        $OssObject = str_replace('.m3u8', '', $value['OssObject']);
                                    }
                                    if (strstr($value['OssObject'], 'mp4')) {
                                        $mp4File   = $custom_server . $value['OssObject'];
                                        $OssObject = str_replace('.mp4', '', $value['OssObject']);
                                    }
                                    $Duration = $value['Duration'] * 1000;
                                }

                                $archives = $dsql->SetQuery("UPDATE `#@__livelist` SET `ossurl` = '$OssObject', `livetime` = $Duration WHERE `id` = " . $val['id']);
                                $dsql->dsqlOper($archives, "update");

                                $list[$key]['mp4url']  = $mp4File;
                                $list[$key]['m3u8url'] = $m3u8File;

                            }else{
                                $list[$key]['playurl'] = '';
                            }
                        }else{
                            $list[$key]['mp4url']  = $custom_server . $val['ossurl'] . ".mp4";
                            $list[$key]['m3u8url'] = $custom_server . $val['ossurl'] . ".m3u8";
                        }
                    }elseif($val['state']==1){
                        $param['id']   = $val['id'];
                        $param['type'] = isMobile() ? 'm3u8' : 'flv';
                        $this->param = $param;
                        $Pulldetail = $this->getPullSteam();
                        $list[$key]['playurl']   = $Pulldetail;
                    }
                }

                if($orderby=='active' || $orderby=='3'){
                    //粉丝人数
                    // $sql     = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__live_follow` WHERE `fid` = " . $val['user']);
                    $sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__member_follow` WHERE `fid` = " . $val['user'] . " AND `for` = ''");
                    $fansret = $dsql->dsqlOper($sql, "results");
                    $list[$key]['totalFans'] = $fansret[0]['t'];

                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__livelist` WHERE `state` = 1 AND `user` = " . $val['user']);
                    $res = $dsql->dsqlOper($sql, "results");
                    $list[$key]['online'] = $res[0]['id'] ? 1 : 0;
                }
                
                
                //是否关注发布人
	            $uid = $userLogin->getMemberID();
	            if($uid > 0){
		            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $uid AND `fid` = " . $val['user'] . " AND `for` = ''");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$list[$key]['isMfollow'] = 1;//关注
					}elseif($uid == $val['user']){
						$list[$key]['isMfollow'] = 2;//自己
					}else{
						$list[$key]['isMfollow'] = 0;//未关注
					}
				}else{
                    $list[$key]['isMfollow'] = 0;//未关注
                }
				

                $param = array(
                    "service" => "live",
                    "template" => "anchor_index",
                    "userid" => $val['user']
                );
                $list[$key]['userurl'] = getUrlPath($param);

                // 预约状态
                $booking = 0;
                if($mybooking){
                    $booking = 1;
                }else{
                    if($uid > 0){
                        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__live_booking` WHERE `uid` = $uid AND `aid` = ".$val['id']);
                        $res = $dsql->dsqlOper($sql, "results");
                        if($res){
                            $booking = 1;
                        }
                    }
                }
                $list[$key]['booking'] = $booking;
                
                //预约人数
                $bookingCount = 0;
                $sql = $dsql->SetQuery("SELECT count(`id`) totalCount FROM `#@__live_booking` WHERE `aid` = ".$val['id']);
                $res = $dsql->dsqlOper($sql, "results");
                if($res){
                    $bookingCount = $res[0]['totalCount'];
                }
                $list[$key]['bookingCount'] = $bookingCount;

            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);
    }

    /**
     * 直播粉丝/关注
     * @return array
     */
    public function follow()
    {
        global $dsql;
        global $userLogin;

        $pageinfo = $list = array();
        $uid      = $type = $page = $pageSize = 0;

        if (!empty($this->param)) {
            if (!is_array($this->param)) {
                return array("state" => 200, "info" => '格式错误！');
            } else {
                $uid      = $this->param['uid'];
                $type     = $this->param['type'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        if (empty($uid)) return array("state" => 200, "info" => '会员ID传递失败！');

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $where = "`fid` = $uid";
        if ($type == "follow") {
            $where = "`tid` = $uid";
        }

        $archives = $dsql->SetQuery("SELECT * FROM `#@__live_follow` WHERE " . $where . " ORDER BY `id` DESC");

        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount / $pageSize);

        if ($totalCount == 0) return array("state" => 200, "info" => '无数据');  //暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        //当前登录会员ID
        $loginid = $userLogin->getMemberID();

        $atpage  = $pageSize * ($page - 1);
        $where   = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives . $where, "results");
        if ($results) {
            foreach ($results as $key => $val) {

                $userid            = $type == "follow" ? $val['fid'] : $val['tid'];
                $list[$key]['uid'] = $userid;

                //用户查看主播列表页面
                $param                 = array(
                    "service" => "live",
                    "template" => "anchor_index",
                    "userid" => $userid
                );
                $list[$key]['userurl'] = getUrlPath($param);

                //查询会员信息
                //$this->param = $userid;
                //$detail = $this->detail();
                $detail = getMemberDetail($userid);
                if ($detail && is_array($detail)) {
                    $list[$key]['nickname'] = $detail['nickname'] ? $detail['nickname'] : '无名';
                    $list[$key]['photo']    = !empty($detail['photo']) ? $detail['photo'] : '/static/images/noPhoto_40.jpg';
                } else {
                    $list[$key]['state']    = 1;
                    $list[$key]['nickname'] = '无名';
                    $list[$key]['photo']    = '/static/images/noPhoto_40.jpg';
                }

                //判断是否关注对方
                if ($loginid != -1) {
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__live_follow` WHERE `tid` = $loginid AND `fid` = $userid");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret && is_array($ret)) {
                        $list[$key]['isfollow'] = 1;
                    }
                }
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);
    }

    /**
     * 添加、删除、会员关注
     * @return array
     */
    public function followMember()
    {
        global $dsql;
        global $userLogin;
        $id     = $this->param['id'];
        $userid = $userLogin->getMemberID();
        if (!empty($id) && $userid > -1 && $id != $userid) {

            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__live_follow` WHERE `tid` = '$userid' AND `fid` = '$id'");
            $return   = $dsql->dsqlOper($archives, "totalCount");

            $time = time();
            if ($return == 0) {
                $archives = $dsql->SetQuery("INSERT INTO `#@__live_follow` (`fid`, `tid`, `date`) VALUES ('$id', '$userid', '$time')");
                $dsql->dsqlOper($archives, "update");
            } else {
                $archives = $dsql->SetQuery("DELETE FROM `#@__live_follow` WHERE `tid` = '$userid' AND `fid` = '$id'");
                $dsql->dsqlOper($archives, "update");
            }
            return "ok";

        }

    }

    /**
     * 直播编辑
     */
    public function edit()
    {
        global $dsql;
        global $userLogin;

        $param = $this->param;

        $id = $param['id'];

        if (empty($id)) return array("state" => 200, "info" => '数据传递失败！');

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if ($uid == -1) {
            return array("state" => 200, "info" => '登录超时，请重新登录！');
        }

        $title      = $param['title'];
        $typeid     = $param['typeid'];
        $catid      = (int)$param['catid'];
        $litpic     = $param['litpic'];
        $ftime      = !empty($param['valid']) ? strtotime($param['valid']) : time();
        $password   = $param['password'];
        $startmoney = $param['startmoney'];
        $endmoney   = $param['endmoney'];
        $way        = $param['way'];
        $flow       = $param['flow'];
        $pulltype   = (int)$param['pulltype'];
        $pullurl_pc    = trim($param['pullurl_pc']);
        $pullurl_touch    = trim($param['pullurl_touch']);
        $note       = $param['note'];
        $menu       = empty($param['menu']) ? array() : $param['menu'];
        $lnglat        = $param['lnglat'];
        if($lnglat){
            $a = explode(",", $lnglat);
            $lng = $a[0];
            $lat = $a[1];
        }else{
            $lng = $lat = "";
        }

        if (empty($title)) return array("state" => 200, "info" => '标题不得为空');

        if (empty($litpic)) return array("state" => 200, "info" => '封面不得为空');

        if ($catid == 1) {
            $startmoney = '';
            $endmoney   = '';
            if (empty($password)) return array("state" => 200, "info" => '密码不得为空');
        } elseif ($catid == 2) {
            $password = '';
            if (empty($startmoney)) return array("state" => 200, "info" => '开始收费不得为空');
            if (empty($endmoney)) return array("state" => 200, "info" => '结束收费不得为空');
        }

        $sql = $dsql->SetQuery("SELECT `id`, `pulltype`, `pushurl`, `ftime` FROM `#@__livelist` WHERE `id` = $id AND `user` = $uid");
        $res = $dsql->dsqlOper($sql, "results");
        if(!$res){
            return array("state" => 200, "info" => '信息不存在');
        }
        $ftime_ = $res[0]['ftime'];
        $pushurl_ = '';
        // if($res[0]['pushtype']){
        //     if(empty($pushurl)) return array("state" => 200, "info" => '请输入第三方推流地址');
        //     if($pushurl != $res[0]['pushurl']){
        //         $pushurl_ = ", `pushurl` = '$pushurl'";
        //     }
        // }
        $pushurl = $res[0]['pushurl'];
        if(empty($pulltype) && empty($res[0]['pushurl'])){

            $streamName = 'live' . $id . '-' . $uid;
            $vhost      = $this->aliLive->vhost;
            $time       = time() + 2592000;  //1个月有效期
            $videohost  = $this->aliLive->video_host;
            $vhost      = $this->aliLive->vhost;
            $appName    = $this->aliLive->appName;
            $privateKey = $this->aliLive->privateKey;
            if ($privateKey) {
                $auth_key = md5('/' . $appName . '/' . $streamName . '-' . $time . '-0-0-' . $privateKey);
                //生成推流地址
                $pushurl = $videohost . '/' . $appName . '/' . $streamName . '?auth_key=' . $time . '-0-0-' . $auth_key;
            } else {
                //生成推流地址
                $pushurl = $videohost . '/' . $appName . '/' . $streamName;
            }
        }
        $pushurl_ = ", `pulltype` = $pulltype, `pushurl` = '$pushurl', `pullurl_pc` = '$pullurl_pc', `pullurl_touch` = '$pullurl_touch'";

        $menuArr = array();
        foreach ($menu as $key => $value) {
            $value['sys'] = (int)$value['sys'];
            $value['show'] = (int)$value['show'];

            if(empty($value['name']) || (!(int)$value['sys'] && empty($value['url'])) )  {
                print_r($value);die;
                return array("state" => 200, "info" => '请填写完整直播菜单');
            }
            $menuArr[] = $value;
        }
        $menuData = serialize($menuArr);
        if(strlen($menuData) > 2000){
            echo '{"state": 200, "info": "直播菜单总长度超出限制"}';
            exit();
        }

        //保存到主表
        $archives = $dsql->SetQuery("UPDATE `#@__livelist` SET  `title`='$title',`litpic`='$litpic',`typeid`='$typeid',`catid`='$catid',`ftime`='$ftime',`password`='$password',`startmoney`='$startmoney',`endmoney`='$endmoney',`way`='$way',`flow`='$flow',`note` = '$note', `menu` = '$menuData', `arcrank` = 0 $pushurl_, `lng` = '$lng', `lat` = '$lat' WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archives, "update");
        if ($results != "ok") {
            return array("state" => 200, "info" => '保存到数据时发生错误，请检查字段内容！');
        } else {

            //创建聊天室
            $param = array(
                "service" => "live",
                "template" => "detail",
                "id" => $id
            );
            $configHandels = new handlers('siteConfig', "createChatRoom");
            $configHandels->getHandle(array("userid" => $uid, "mark" => "chatRoom" . $id, "title" => $title, "url" => getUrlPath($param)));

            //return $id;

            // 更新直播预约表中时间
            if($ftime_ != $ftime){
                $sql = $dsql->SetQuery("UPDATE `#@__live_booking` SET `ftime` = $ftime WHERE `aid` = $id AND `notice` = 0");
                $dsql->dsqlOper($sql, "update");
            }
            return array("id" => $id);
        }
    }

    /**
     * 点赞
     */
    public function getUp()
    {
        global $dsql;
        $param = $this->param;

        $id = $param['id'];
        $up = $param['up'];

        if (empty($id)) return array("state" => 200, "info" => '数据传递失败！');

        //保存到主表
        $archives = $dsql->SetQuery("UPDATE `#@__livelist` SET  `up`=up+'1' WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archiverfs, "update");
        if ($results != "ok") {
            return array("state" => 200, "info" => '数据出错！');
        } else {
            return 'ok';
        }
    }

    /**
     * 结束直播
     */
    public function updateState()
    {
        global $dsql;
        global $userLogin;
        $param = $this->param;

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if ($uid == -1) {
            return array("state" => 200, "info" => '登录超时，请重新登录！');
        }

        $id    = $param['id'];
        $state = $param['state'];//1正在直播；2：结束直播

        $state = !empty($state) ? $state : 2;

        if (empty($id)) return array("state" => 200, "info" => '数据传递失败！');

        //保存到主表
        $archives = $dsql->SetQuery("UPDATE `#@__livelist` SET  `state`='$state' WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archives, "update");
        if ($results != "ok") {
            return array("state" => 200, "info" => '数据出错！');
        } else {
            return 'ok';
        }
    }


    /**
     * 生成推流地址
     * @param $streamName 用户专有名
     * @param $vhost 加速域名
     * @param $time 有效时间单位秒
     */
    public function getPushSteam()
    {
        global $userLogin;
        global $dsql;
        $param  = $this->param;
        $userid = $userLogin->getMemberID();
        //$userid = $param['userid'];
        if ($userid == -1) {
            return array("state" => 200, "info" => '请先登录！');
        }
        if (!is_numeric($userid)) return array("state" => 200, "info" => '登录超时，请登录后重试！');

        $title         = $param['title'];
        $typeid        = $param['typeid'];
        $catid         = (int)$param['catid'];
        $litpic        = $param['litpic'];
        $ftime         = !empty($param['valid']) ? strtotime($param['valid']) : time();
        $password      = $param['password'];
        $startmoney    = $param['startmoney'];
        $endmoney      = $param['endmoney'];
        $way           = $param['way'];
        $flow          = $param['flow'];
        $pulltype      = (int)$param['pulltype'];
        $pullurl_pc    = trim($param['pullurl_pc']);
        $pullurl_touch = trim($param['pullurl_touch']);
        $note          = $param['note'];
        $menu          = empty($param['menu']) ? array() : $param['menu'];
        $lnglat        = $param['lnglat'];
        if($lnglat){
            $a = explode(",", $lnglat);
            $lng = $a[0];
            $lat = $a[1];
        }else{
            $lng = $lat = "";
        }

        include HUONIAOINC."/config/live.inc.php";
        $arcrank     = (int)$customFabuCheck;

        if (empty($title)) {
            return array("state" => 200, "info" => '请填写直播标题！');
        }
        if (empty($litpic)) {
            return array("state" => 200, "info" => '请填写直播封面！');
        }
        if ($catid == 1) {
            if (empty($password)) return array("state" => 200, "info" => '密码不得为空');
        } elseif ($catid == 2) {
            if (empty($startmoney)) return array("state" => 200, "info" => '开始收费不得为空');
            if (empty($endmoney)) return array("state" => 200, "info" => '结束收费不得为空');
        }

        if($pulltype && empty($pullurl_pc) && empty($pullurl_touch)) return array("state" => 200, "info" => '请输入第三方拉流地址');

        //判断用户是否超出等级限制
        $this->param['user'] = $userid;
        $num                 = $this->checkLiveNum();
        if ($num == -1) {
            return array("state" => 200, "info" => '超出直播条数！');
        } elseif ($num == -2) {
            return array("state" => 200, "info" => '请先完成认证！');
        } else {

            // 	$this->param['user'] = $userid;
            // $limitTime = $this->userLimitTime();
            // if($limitTime==-1) return array("state" => 200, "info" => '超出用户直播时间！');

            // $sql   = $dsql->SetQuery("SELECT max(`id`) i FROM `#@__livelist`");
            // $res   = $dsql->dsqlOper($sql, "results");
            // $maxid = $res[0]['i'] + 1;        //最大id

            // $streamName = 'live' . $maxid . '-' . $userid;
            // $vhost      = $this->aliLive->vhost;
            // $time       = time() + 2592000;  //1个月有效期
            // $videohost  = $this->aliLive->video_host;
            // $vhost      = $this->aliLive->vhost;
            // $appName    = $this->aliLive->appName;
            // $privateKey = $this->aliLive->privateKey;
            // if ($privateKey) {
            //     $auth_key = md5('/' . $appName . '/' . $streamName . '-' . $time . '-0-0-' . $privateKey);
            //     //生成推流地址
            //     $pushurl = $videohost . '/' . $appName . '/' . $streamName . '?auth_key=' . $time . '-0-0-' . $auth_key;
            // } else {
            //     //生成推流地址
            //     $pushurl = $videohost . '/' . $appName . '/' . $streamName;
            // }

            $pushurl = '';

            // $mediaId = $mediaDetail['id'];

            $menuArr = array();
            foreach ($menu as $key => $value) {
                $value['sys'] = (int)$value['sys'];
                $value['show'] = (int)$value['show'];

                if(empty($value['name']) || (!(int)$value['sys'] && empty($value['url'])) )  {
                    return array("state" => 200, "info" => '请填写完整直播菜单');
                }
                $menuArr[] = $value;
            }
            $menuData = serialize($menuArr);
            if(strlen($menuData) > 2000){
                echo '{"state": 200, "info": "直播菜单总长度超出限制"}';
                exit();
            }

            $sql = $dsql->SetQuery("INSERT INTO `#@__livelist` (`user`, `pushurl`,`title`,`typeid`,`catid`,`ftime`,`password`,`startmoney`,`endmoney`,`way`,`flow`,`litpic`,`state`, `note`, `menu`, `pulltype`, `pullurl_pc`, `pullurl_touch`, `arcrank`, `starttime`, `streamname`, `lng`, `lat`) VALUES ('$userid', '$pushurl','$title','$typeid','$catid','$ftime','$password','$startmoney','$endmoney','$way','$flow','$litpic','0', '$note', '$menuData', $pulltype, '$pullurl_pc', '$pullurl_touch', '$arcrank', 0, '', '$lng', '$lat')");
            $lid = $dsql->dsqlOper($sql, "lastid");
            if (is_numeric($lid)) {

                $maxid = $lid;        //自增id

                if(empty($pulltype)){

                    $streamName = 'live' . $maxid . '-' . $userid;
                    $vhost      = $this->aliLive->vhost;
                    $time       = time() + 2592000;  //1个月有效期
                    $videohost  = $this->aliLive->video_host;
                    $vhost      = $this->aliLive->vhost;
                    $appName    = $this->aliLive->appName;
                    $privateKey = $this->aliLive->privateKey;

                    if ($privateKey) {
                        $auth_key = md5('/' . $appName . '/' . $streamName . '-' . $time . '-0-0-' . $privateKey);
                        //生成推流地址
                        $pushurl = $videohost . '/' . $appName . '/' . $streamName . '?auth_key=' . $time . '-0-0-' . $auth_key;
                    } else {
                        //生成推流地址
                        $pushurl = $videohost . '/' . $appName . '/' . $streamName;
                    }
                    $arc = $dsql->SetQuery("UPDATE `#@__livelist` SET `pushurl` = '$pushurl', `streamname` = '$streamName' WHERE `id` = $lid");
                    $dsql->dsqlOper($arc, "update");
                }

                $this->param = $maxid . '-' . $userid;
                $this->addLiveAppRecordConfig();

                //直播分类
                $archives = $dsql->SetQuery("SELECT `typename` FROM `#@__livetype` WHERE `id` = " . $typeid);
                $result   = $dsql->dsqlOper($archives, "results");
                $typename = !empty($result[0]['typename']) ? $result[0]['typename'] : '';
                //直播类型
                $catidtype = empty($catid) ? '公开' : ($catid == 1 ? '加密' : '收费');
                //流畅度
                $flowname = $flow == 1 ? '流畅' : ($flow == 2 ? '普清' : '高清');
                //直播方式
                $wayname = empty($way) ? '横屏' : '竖屏';

                //创建聊天室
                $param = array(
                    "service" => "live",
                    "template" => "detail",
                    "id" => $maxid
                );

                $configHandels = new handlers('siteConfig', "createChatRoom");
				$configHandels->getHandle(array("userid" => $userid, "mark" => "chatRoom" . $maxid, "title" => $title, "url" => getUrlPath($param)));

                // 创建主播
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__live_anchor` WHERE `uid` = $userid");
                $res = $dsql->dsqlOper($sql, "results");
                if(!$res){
                    $now = time();
                    $sql = $dsql->SetQuery("INSERT INTO `#@__live_anchor` (`uid`, `rec`, `pubdate`) VALUES ($userid, 0, $now)");
                    $dsql->dsqlOper($sql, "lastid");
                }

                return array("pushurl" => $pushurl, "id" => $maxid, "typename" => $typename, "catidtype" => $catidtype, "wayname" => $wayname, "flowname" => $flowname);

            } else {
                return array("state" => 200, "info" => '直播创建失败！');
            }
        }
    }

    /**
     * 生成拉流地址
     * @param $streamName 用户专有名
     * @param $vhost 加速域名
     * @param $type 视频格式 支持rtmp、flv、m3u8三种格式
     */
//    public function getPullSteam(){
//        $type=$_GET['type'];
//        $id=$_GET['id'];
//        global $dsql;
//        $sql = $dsql->SetQuery("SELECT `flv`,`m3u8` FROM `#@__livelist` WHERE `id` = $id");
//        $ret = $dsql->dsqlOper($sql, "results");
//        if(!empty($ret)){
//            switch ($type){
//                case 'flv':
//                    $pullurl=$ret[0]['flv'];
//                    break;
//                case 'm3u8':
//                    $pullurl=$ret[0]['m3u8'];
//                    break;
//            }
//            return $pullurl;
//        }else{
//            return array("state" => 200, "info" => '该直播不存在！');
//        }
//    }
    /**
     * 生成拉流地址
     * @param $streamName 用户专有名
     * @param $vhost 加速域名
     * @param $type 视频格式 支持rtmp、flv、m3u8三种格式
     */
    public function getPullSteam()
    {
        global $dsql;
        global $cfg_secureAccess;
        $param = $this->param;
        $type  = $param['type'];
        $id    = $param['id'];
        $sql   = $dsql->SetQuery("SELECT `user` FROM `#@__livelist` WHERE `id` = $id");
        $res   = $dsql->dsqlOper($sql, "results");
        if ($res) {
            $streamName = 'live' . $id . '-' . $res[0]['user'];
            $time       = time() + 300;
            $appName    = $this->aliLive->appName;
            $privateKey = $this->aliLive->privateKey;
            $vhost      = $this->aliLive->vhost;
            $playhost   = $this->aliLive->play_host;
            $playprivatekey = $this->aliLive->playprivatekey;
            $url        = '';
            switch ($type) {
                case 'flv':
                    $host = (strstr($vhost, 'http') ? '' : $cfg_secureAccess) . $playhost;
                    $url  = '/' . $appName . '/' . $streamName . '.flv';
                    break;
                case 'm3u8':
                    $host = (strstr($vhost, 'http') ? '' : $cfg_secureAccess) . $playhost;
                    $url  = '/' . $appName . '/' . $streamName . '.m3u8';
                    break;
                default:
                    $host = (strstr($vhost, 'http') ? '' : $cfg_secureAccess) . $playhost;
                    $url  = '/' . $appName . '/' . $streamName . '.m3u8';
                    break;
            }
            if ($playprivatekey) {
                $auth_key = md5($url . '-' . $time . '-0-0-' . $playprivatekey);
                $url      = $host . $url . '?auth_key=' . $time . '-0-0-' . $auth_key;
            } else {
                $url = $host . $url;
            }
            return $url;
        } else {
            return array("state" => 200, "info" => '该直播不存在！');
        }
    }

    /**
     * 配置 APP 录制，输出内容保存到 OSS 中
     * @param $domainName  直播域名
     * @param $appName     应用名
     */
    public function addLiveAppRecordConfig()
    {
        $streamname      = $this->param;
        require(HUONIAOINC . "/config/live.inc.php");
        $apiParams  = array(
            'Action' => 'AddLiveAppRecordConfig',
            'DomainName' => $this->aliLive->play_host,
            'AppName' => $this->aliLive->appName,
            'StreamName' => $streamname,
            'OssBucket'  => $custom_OSSBucket,
            'OssEndpoint'=> $custom_OSSUrl,

          	'RecordFormat.1.Format' => 'm3u8',
            'RecordFormat.1.CycleDuration' => $this->aliLive->duration,
            'RecordFormat.1.OssObjectPrefix' => 'record/'.$this->aliLive->appName.'/'.$streamname.'/{Sequence}{EscapedStartTime}{EscapedEndTime}',
            'RecordFormat.1.SliceOssObjectPrefix' => 'record/'.$this->aliLive->appName.'/'.$streamname.'/{UnixTimestamp}_{Sequence}',

            'RecordFormat.2.Format' => 'flv',
            'RecordFormat.2.CycleDuration' => $this->aliLive->duration,
            'RecordFormat.2.OssObjectPrefix' => 'record/'.$this->aliLive->appName.'/'.$streamname.'/{Sequence}{EscapedStartTime}{EscapedEndTime}',

            'RecordFormat.3.Format' => 'mp4',
            'RecordFormat.3.CycleDuration' => $this->aliLive->duration,
            'RecordFormat.3.OssObjectPrefix' => 'record/'.$this->aliLive->appName.'/'.$streamname.'/{Sequence}{EscapedStartTime}{EscapedEndTime}'
        );
        return $this->aliLive->aliApi($apiParams, $credential = "GET", $domain = "live.aliyuncs.com");
    }

    /**
     * 查询在线人数
     * @param $domainName  直播域名
     * @param $appName     应用名
     * @param $streamName  推流名
     */
    public function describeLiveStreamOnlineUserNum()
    {
        $param      = $this->param;
        $streamname = $param['Streamname'];
        $apiParams  = array(
            'Action' => 'DescribeLiveStreamOnlineUserNum',
            'DomainName' => $this->aliLive->vhost,
            'AppName' => $this->aliLive->appName,
            'StreamName' => $streamname,
        );

        return $this->aliLive->aliApi($apiParams, $credential = "GET", $domain = "live.aliyuncs.com");
    }

    /**
     * 查询直播浏览人数
     */
    public function getLiveNum()
    {
        global $dsql;
        $param      = $this->param;
        $streamname = $param['Streamname'];
        if (!empty($streamname)) {
            $wheresql = " and streamname='$streamname' ";
        }
        $sql = $dsql->SetQuery("SELECT `id`,`title`,`streamname`,`click` FROM `#@__livelist` WHERE 1=1 $wheresql ");
        $res = $dsql->dsqlOper($sql, "results");
        if ($res) {
            $click = !empty($res[0]['click']) ? $res[0]['click'] : 0;
            return array("click" => $click);
            //echo '{"state":100,"info":'$click'}';exit;
        } else {
            return array("state" => 200, "info" => '无数据');
        }
    }

    /**
     * 获取某一时间段内某个域名(或域名下某应用或某个流)的推流记录
     * @param $domainName  直播域名
     * @param $appName     应用名
     * @param $streamName  推流名
     */
    public function describeLiveStreamsPublishList()
    {
        $apiParams = array(
            'Action' => 'DescribeLiveStreamsPublishList',
            'DomainName' => $this->aliLive->vhost,
            'AppName' => $this->aliLive->appName,
            'StartTime' => gmdate("Y-m-d\T00:00:00\Z", strtotime("-30 day")),
            'EndTime' => gmdate("Y-m-d\T00:00:00\Z"),
//            'PageSize'=>2,
//            'PageNumber'=>1
        );
        //return  $apiParams;
        return $this->aliLive->aliApi($apiParams, $credential = "GET", $domain = "live.aliyuncs.com");

    }

    /**
     * 查询推流在线列表
     * @param $domainName  直播域名
     * @param $appName     应用名
     * @param $streamName  推流名
     */
    public function describeLiveStreamsOnlineList()
    {
        $apiParams = array(
            'Action' => 'DescribeLiveStreamsOnlineList',
            'DomainName' => $this->aliLive->vhost,
            'AppName' => $this->aliLive->appName,
        );
        return $this->aliLive->aliApi($apiParams, $credential = "GET", $domain = "live.aliyuncs.com");
    }

    /**
     * 查询录制索引文件
     * @param $domainName  直播域名
     * @param $appName     应用名
     * @param $streamName  推流名
     */
    public function describeLiveStreamRecordIndexFiles()
    {
        $param = $this->param;
        //$StreamName = $param['StreamName'];
        $apiParams = array(
            'Action' => 'DescribeLiveStreamRecordIndexFiles',
            'DomainName' => $this->aliLive->play_host,
            'AppName' => $this->aliLive->appName,
            'StreamName' => $param,
            'StartTime' => gmdate("Y-m-d\T00:00:00\Z", strtotime("-1 day")),
            'EndTime' => gmdate("Y-m-d\T00:00:00\Z", strtotime("+1 day")),
            'PageNum' => 1,
            'PageSize' => 10,
            'Order' => 'asc'
        );

        return $this->aliLive->aliApi($apiParams, $credential = "GET", $domain = "live.aliyuncs.com");
    }

    /**
     * 查询单个录制索引文件
     * @param $domainName  直播域名
     * @param $appName     应用名
     * @param $streamName  推流名
     */
    public function describeLiveStreamRecordIndexFile()
    {
        $apiParams = array(
            'Action' => 'DescribeLiveStreamRecordIndexFile',
            'DomainName' => $this->aliLive->vhost,
            'AppName' => $this->aliLive->appName,
            'StreamName' => 'test1',
            'RecordId' => '396e74b2-5097-439c-b463-94dbc06ef502'
        );
        return $this->aliLive->aliApi($apiParams, $credential = "GET", $domain = "live.aliyuncs.com");
    }

    /**
     * 查询推流黑名单列表
     * @param $domainName  直播域名
     * @param $appName     应用名
     */
    public function describeLiveStreamsBlockList()
    {
        $apiParams = array(
            'Action' => 'DescribeLiveStreamsBlockList',
            'DomainName' => $this->aliLive->vhost
        );
        return $this->aliLive->aliApi($apiParams, $credential = "GET", $domain = "live.aliyuncs.com");
    }

    /**
     * 禁止直播流推送
     * @param $domainName  直播域名
     * @param $appName     应用名
     */
    public function forbidLiveStream()
    {
        $param      = $this->param;
        $streamname = $param['streamname'];
        $apiParams  = array(
            'Action' => 'ForbidLiveStream',
            'DomainName' => $this->aliLive->vhost,
            'AppName' => $this->aliLive->appName,
            'StreamName' => $streamname,
            'LiveStreamType' => 'publisher'
        );
        return $this->aliLive->aliApi($apiParams, $credential = "GET", $domain = "live.aliyuncs.com");
    }

    /**
     * 直播信息详细
     * @return array
     */
    public function detail()
    {
        global $dsql;
        global $userLogin;
        global $cfg_secureAccess;
        global $cfg_basehost;
        $id = $this->param;

        if (!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

        $archives = $dsql->SetQuery("SELECT `id`,`user`,`pushurl`,`title`,`up`,`litpic`,`way`,`streamname`,`state`,`click`, `ossurl`, `catid` , `startmoney`, `endmoney` , `ftime`, `note`, `menu`, `pulltype`, `pullurl_pc`, `pullurl_touch`, `typeid`, `lng`, `lat` FROM `#@__livelist` WHERE `id` = " . $id." AND `arcrank` = 1");
        $results  = $dsql->dsqlOper($archives, "results");
        if ($results) {
            $results[0]['lng'] = $results[0]['lng'];
            $results[0]['lat'] = $results[0]['lat'];
            if(!empty($results[0]['lng']) && !empty($results[0]['lat'])){
                $results[0]["lnglat"]     = $results[0]['lng'] . ',' . $results[0]['lat'];
            }

            if (!empty($results[0]['litpic'])) {
                if (strpos($results[0]['litpic'], 'images')) {
                    $results[0]['litpic'] = $cfg_secureAccess . $cfg_basehost . $results[0]['litpic'];
                } else {
                    $results[0]['litpic'] = getFilePath($results[0]['litpic']);
                }
            } else {
                $results[0]['litpic'] = $cfg_secureAccess . $cfg_basehost . '/static/images/404.jpg';
            }

            $results[0]['click'] = $results[0]['click'] >= 10000 ? sprintf("%.1f", $results[0]['click'] / 10000)."万" : $results[0]['click'];

            //查找 正在直播 直播结束
            $archives   = $dsql->SetQuery("SELECT `id` FROM `#@__livelist` WHERE `user` = '".$results[0]['user']."' and state in (1,2)");
            $totalCount = $dsql->dsqlOper($archives, "totalCount");
            $results[0]['livenum'] = $totalCount;
            $results[0]['user'] = $results[0]['user'];

            $sql  = $dsql->SetQuery("SELECT `typename` FROM `#@__livetype` where id = '".$results[0]['typeid']."'");
            $ret  = $dsql->dsqlOper($sql, "results");
            $results[0]['typename'] = !empty($ret[0]['typename']) ? $ret[0]['typename'] : '';

            //获取主播信息
            $member                 = getMemberDetail($results[0]['user']);
            $results[0]['nickname'] = !empty($member['username']) ? $member['username'] : cn_substrR($member['nickname'], 5);
            $results[0]['photo']    = !empty($member['photo']) ? $member['photo'] : '/static/images/404.jpg';
            //用户查看主播列表页面
            $param                 = array(
                "service" => "live",
                "template" => "anchor_index",
                "userid" => $results[0]['user']
            );
            $results[0]['userurl'] = getUrlPath($param);
            $results[0]['start_time'] = $results[0]['ftime'] ? date("Y/m/d H:i", $results[0]['ftime']) : '暂未开播';


            //用户是否可以发言
            // $uid                     = $userLogin->getMemberID();//用户
            // $results[0]['token']     = '';
            // $results[0]['username']  = '';
            // $results[0]['userphoto'] = '';

            //查询当前配置
            // $custom_rongKeyID = $custom_rongKeySecret = "";
            // $sql              = $dsql->SetQuery("SELECT * FROM `#@__app_config` LIMIT 1");
            // $ret              = $dsql->dsqlOper($sql, "results");
            // if ($ret) {
            //     $data                 = $ret[0];
            //     $custom_rongKeyID     = $data['rongKeyID'];
            //     $custom_rongKeySecret = $data['rongKeySecret'];
            // }
            // $appKey    = $custom_rongKeyID;
            // $appSecret = $custom_rongKeySecret;
            // //获取token
            // include_once(HUONIAOINC . "/class/imserver/im.class.php");
            // $RongCloud = new im($appKey, $appSecret);
            // if ($uid > 0) {
            //     $uinfo    = $userLogin->getMemberInfo($uid);
            //     $token    = $RongCloud->getToken($uid, $uinfo['username'], $uinfo['photo']);
            //     $tokenArr = json_decode($token, true);
            //     if ($tokenArr['code'] != 200) {
            //         $results[0]['token']  = '获取token参数错误';
            //         //return array("state" => 200, "info" => '获取token参数错误！');
            //     }else{
            //         $results[0]['token']     = $tokenArr['token'];
            //     }
            //     $results[0]['appKey']    = $appKey;
            //     $results[0]['username']  = $uinfo['nickname'] ? $uinfo['nickname'] : $uinfo['username'];
            //     $results[0]['userphoto'] = !empty($uinfo['photo']) ? $uinfo['photo'] : '/static/images/noPhoto_40.jpg';
            // } else {
            //     //必须默认个token
            //     $token    = $RongCloud->getToken($uid, '默认', '');
            //     $tokenArr = json_decode($token, true);
            //     if ($tokenArr['code'] != 200) {
            //         $results[0]['token']  = '获取token参数错误';
            //         //return array("state" => 200, "info" => '获取token参数错误！');
            //     }else{
            //         $results[0]['token']  = $tokenArr['token'];
            //     }
            //     $results[0]['appKey'] = $appKey;
            // }

            //$detail['starttime']  = date("Y-m-d H:i:s", $results[0]['starttime']);
            $results[0]['wayname'] = $results[0]['way'] == 1 ? '竖屏' : '横屏';

            //是否点赞
            $iszan = 0;
            // $sql                    = $dsql->SetQuery("SELECT * FROM `#@__site_zanmap` WHERE `vid` = $id AND `temp` = 'live' ");
            $sql                    = $dsql->SetQuery("SELECT `id` FROM `#@__public_up` WHERE `type` = '0' AND `tid` = $id AND `module` = 'live' AND `action` = 'h_detail' ");
            $ret                    = $dsql->dsqlOper($sql, 'totalCount');
            $results[0]['zanCount'] = $ret;


            // 是否关注 个人
            $uid = $userLogin->getMemberID();
            $userid = $results[0]['user'];
            $isfollow = 0;
            if($uid > 0){
                $archives = $dsql->SetQuery("SELECT `id` FROM `#@__live_follow` WHERE `fid` = '$userid' AND `tid` = '$uid'");
                $isfollow = $dsql->dsqlOper($archives, "totalCount");

                //是否相互关注
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $uid AND `fid` = " . $userid);
                $isMfollow = $dsql->dsqlOper($sql, "results");

                /* $sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_zanmap` WHERE `vid` = $id AND `userid` = $uid AND `temp` = 'live'");
                $ret = $dsql->dsqlOper($sql, "results");
                $iszan = $ret ? 1 : 0; */
                $zanparams = array(
                    "module" => "live",
                    "temp"   => "h_detail",
                    "id"     => $id,
                    "check"  => 1
                );
                $iszan = checkIsZan($zanparams);
                $iszan = $iszan == 'has' ? 1 : 0;
            }
            $results[0]['isfollow'] = $isfollow;
            $results[0]['isMfollow'] = $isMfollow[0]['id'] ? 1 : 0;
            $results[0]['iszan'] = $iszan;

            //粉丝人数
            $sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__member_follow` WHERE `fid` = " . $results[0]['user']);
            $fansret = $dsql->dsqlOper($sql, "results");
            $results[0]['totalFans'] = $fansret[0]['t'];



            // 自媒体信息
            // $obj = new article();
            // $check = $obj->selfmedia_verify($results[0]['user'], '', 'check', $vdata);
            // if($check == "ok"){
            //     //是否关注 媒体号
            //     $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $uid AND `fid` = " . $vdata['id'] . " AND `for` = 'media'");
            //     $ret = $dsql->dsqlOper($sql, "results");
            //     $vdata['isfollow'] = $ret ? 1 : 0;
            // }
            // $results[0]['media'] = $check == "ok" ? $vdata : array();

            $menu = "";
            if($results[0]['menu']){
                $menu = unserialize($results[0]['menu']);
                if($menu !== false){
                    foreach ($menu as $key => $value) {
                        if(!$value['show']){
                            unset($menu[$key]);
                        }
                    }
                    $menu = array_values($menu);
                }
            }
            if($menu == ""){
                $menu = array(
                    0 => array('sys' => 1, 'name' => '图文', 'url' => ''),
                    1 => array('sys' => 2, 'name' => '互动', 'url' => ''),
                    2 => array('sys' => 3, 'name' => '榜单', 'url' => ''),
                );
            }
            $results[0]['menu'] = $menu;

            //创建聊天室
            $param = array(
                "service" => "live",
                "template" => "detail",
                "id" => $id
            );
            $configHandels = new handlers('siteConfig', "createChatRoom");
            $configHandels->getHandle(array("userid" => $results[0]['user'], "mark" => "chatRoom" . $id, "title" => $results[0]['title'], "url" => getUrlPath($param)));

        }
        return $results;
    }

    /**
     * 判断用户的直播条数是否超出限制
     */
    public function checkLiveNum()
    {
        global $dsql;
        $param = $this->param;

        $id = $param['user'];

        //查找 正在直播 直播结束 拉入黑名单的
        $archives = $dsql->SetQuery("SELECT `id` FROM `#@__livelist` WHERE user = '$id' and state in (0,1,2)");
        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //会员信息
        $member = getMemberDetail($id);
        if (!empty($member['level'])) {
            $archives   = $dsql->SetQuery("SELECT * FROM `#@__member_level` WHERE `id` = " . $member['level']);
            $results    = $dsql->dsqlOper($archives, "results");
            $fabuAmount = !empty($results[0]['privilege']) ? unserialize($results[0]['privilege']) : array();

        } else {
            require(HUONIAOINC . "/config/settlement.inc.php");
            $fabuAmount = $cfg_fabuAmount ? unserialize($cfg_fabuAmount) : array();

        }
        if ($member['certifyState'] != 1) return -2;

        if ($totalCount >= $fabuAmount['live']) {
            //return array("state" => 200, "info" => '超出会员限制');
            return -1;
        } else {
            //return 'ok';
            return 1;
            //echo '{"state":100,"info":"ok"}';exit;
        }
    }

    /**
     * 用户删除历史直播
     */
    public function delUserLive()
    {
        global $dsql;
        global $userLogin;
        global $autoload;
        $uid = $userLogin->getMemberID();
        if ($uid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

        $id = $this->param['id'];

        require(HUONIAOINC . "/config/live.inc.php");
        if ($customCommentCheck == 1) return array("state" => 200, "info" => '没有权限删除！');

        if (empty($id)) return array("state" => 200, "info" => '没有要删除的信息！');

        $archives = $dsql->SetQuery("SELECT * FROM `#@__livelist` WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archives, "results");
        if ($results) {
            $this->param = $results[0]['streamname'];
            $detail      = $this->describeLiveStreamRecordIndexFiles();
            $file        = $detail['RecordIndexInfoList']['RecordIndexInfo'][0]['OssObject'];
            //require(HUONIAOINC."/config/live.inc.php");
            $OSSConfig = array(
                "bucketName" => "$custom_OSSBucket",
                "endpoint" => "$custom_OSSUrl",
                "accessKey" => "$custom_OSSKeyID",
                "accessSecret" => "$custom_OSSKeySecret"
            );
            $autoload  = true;
            include_once HUONIAOINC . '/class/aliyunOSS.class.php';
            $aliyunOSS = new aliyunOSS($OSSConfig);

            if ($file) {

                $aliyunOSS->delete($file);
                $ossError = $aliyunOSS->error();
                if (empty($ossError)) {
                    $archives = $dsql->SetQuery("DELETE FROM `#@__livelist` WHERE `id` in (" . $id . ") and user='$uid'");
                    $dsql->dsqlOper($archives, "update");
                    echo '{"state":100,"info":"删除成功！"}';
                    exit;
                } else {
                    return array("state" => 200, "info" => '删除错误！');
                }
            } else {
                $archives = $dsql->SetQuery("DELETE FROM `#@__livelist` WHERE `id` in (" . $id . ") and user='$uid'");
                $dsql->dsqlOper($archives, "update");
                return "删除成功";
            }
        } else {
            return array("state" => 200, "info" => '没有要删除的信息！');
        }
    }

    /**
     * 用户直播时间限制
     */
    public function userLimitTime()
    {
        global $dsql;
        $uid = $this->param['user'];

        //会员信息
        if (empty($uid)) return array("state" => 200, "info" => '登录超时，请重新登录！');

        $member = getMemberDetail($uid);
        if (!empty($member['level'])) {
            $archives   = $dsql->SetQuery("SELECT * FROM `#@__member_level` WHERE `id` = " . $member['level']);
            $results    = $dsql->dsqlOper($archives, "results");
            $fabuAmount = !empty($results[0]['privilege']) ? unserialize($results[0]['privilege']) : array('livetime' => 0);
        } else {
            require(HUONIAOINC . "/config/settlement.inc.php");
            $fabuAmount = $cfg_fabuAmount ? unserialize($cfg_fabuAmount) : array('livetime' => 0);
        }

        //查询用户的已经直播的时间
        $timesql     = $dsql->SetQuery("SELECT sum(livetime) as totaltime FROM `#@__livelist` WHERE `user` ='$uid' and state in (1,2)");
        $timeresults = $dsql->dsqlOper($timesql, "results");
        $useTime     = round($timeresults[0]['totaltime'] / (60 * 60 * 1000), 2);

        if ($useTime > $fabuAmount['livetime']) {
            return -1;
        } else {
            return 1;
        }
    }

    /**
     * 记录直播当前时间
     * 直播的播时间
     */
    public function updateTime()
    {
        global $dsql;
        $id       = $this->param['id'];//当前直播活动
        $time     = $this->param['time'];//当前直播时间
        $time     = substr($time, 0, -3); //java时间戳需要去除后三位
        $livetime = $this->param['livetime'];//直播的播时间

        if (empty($id)) return array("state" => 200, "info" => '参数错误');

        $archives = $dsql->SetQuery("UPDATE `#@__livelist` SET `starttime` = '$time',`livetime`='$livetime' WHERE `id` = " . $id);
        $dsql->dsqlOper($archives, "update");
        return 'ok';
    }

    /**
     * 获取时间
     */
    public function selectLiveTime()
    {
        global $dsql;
        $id = $this->param['id'];//当前直播活动

        $archives = $dsql->SetQuery("SELECT `id`,`livetime` FROM `#@__livelist` WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archives, "results");

        return array("livetime" => $results[0]['livetime']);
    }

    /**
     * 获取用户的token
     */
    public function getUserToken()
    {
        $param    = $this->param;
        $userid   = $param['userid'];
        $username = $param['username'];
        $userlogo = $param['userlogo'];
        $token    = $this->RongCloud->getToken($userid, $username, $username);

        $tokenArr = json_decode($token, true);
        if ($tokenArr['code'] != 200) {
            return array("state" => 200, "info" => '操作失败！');
        } else {
            echo '{"state":100,"info":"' . $tokenArr['token'] . '"}';
            exit;
            //return $tokenArr;
        }
    }

    /**
     * 创建聊天室
     */
    public function createRoom()
    {
        global $dsql;

        $param  = $this->param;
        $id     = $param['id'];
        $userid = $param['userid'];
        $chatid = 'chatRoom' . $id;

        //查询直播间信息
        $sql = $dsql->SetQuery("SELECT `title`, `user` FROM `#@__livelist` WHERE `id` = ". $id);
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $title = $ret[0]['title'];

            //创建聊天室
            $param = array(
                "service" => "live",
                "template" => "detail",
                "id" => $id
            );
            $configHandels = new handlers('siteConfig', "createChatRoom");
            $configHandels->getHandle(array("userid" => $userid, "mark" => "chatRoom" . $id, "title" => $title, "url" => getUrlPath($param)));

            return $chatid;
        }else{
            return array("state" => 200, "info" => '直播间不存在或已经删除！');
        }

    }

    /**
     * 聊天室禁言
     */
    public function limitTalk()
    {
        $param = $this->param;

        $id       = $param['id'];
        $chatname = $param['chatname'];
        $time     = $param['time'];

        $token    = $this->RongCloud->addGagUser($id, $chatname, $time);
        $tokenArr = json_decode($token, true);
        if ($tokenArr['code'] != 200) {
            return array("state" => 200, "info" => '操作失败！');
        } else {
            echo '{"state":100,"info":"操作成功"}';
            exit;
        }
    }

    /**
     * 聊天室解除禁言
     */
    public function unLimitTalk()
    {
        $param = $this->param;

        $id       = $param['id'];
        $chatname = $param['chatname'];

        $token    = $this->RongCloud->rollbackGagUser($id, $chatname);
        $tokenArr = json_decode($token, true);
        print_R($tokenArr);
        exit;
    }

    /**
     * 用户发送信息插入数据库中
     */
    public function chatTalk()
    {
        global $dsql;
        global $userLogin;
        $param = $this->param;
        if($userLogin->getMemberID() == -1)
        {
            // return array("state" => 200, "info" => '请登录！');
        }
        $chatid    = $param['chatid'];
        $userid    = $param['userid'];
        $username  = $param['username'];
        $userphoto = $param['userphoto'];
        $content   = addslashes($param['content']);
        $system    = $param['system'];

        $ftime = GetMkTime(time());
        if (empty($chatid) || empty($content)) {
            return array("state" => 200, "info" => '必填项不得为空！');
        }

        if(!$system && (strstr($content, "__T__:") || strstr($content, "__H__:") || strstr($content, "__L__:"))){
            return array("state" => 200, "info" => '内容非法！');
        }

        if(!strstr($chatid, "chatroom")){
            $chatid = "chatroom".$chatid;
        }
        if(strstr($content, "__T__:")){
            $path = str_replace("__T__:", "",$content);
            $content = '__T__:'. getFilePath($path);
        }

        $archives = $dsql->SetQuery("INSERT INTO `#@__livechat` (`chatid`,`userid`,`username`,`userphoto`,`content`,`ftime`,`ip`) VALUES ('$chatid','$userid','$username','$userphoto','$content','$ftime','" . GetIP() . "')");
        $lid      = $dsql->dsqlOper($archives, "lastid");

        if ($lid) {
            return "评论成功";
        } else {
            return array("state" => 200, "info" => '评论失败！');
        }
    }

    /**
     * 聊天室聊天记录查询
     */
    public function talkList()
    {
        global $dsql;
        global $userLogin;
        $param    = $this->param;
        $where    = "";
        $pageinfo = $list = array();

        $page     = $param['page'];
        $pageSize = $param['pageSize'];
        $chatid   = $param['chatid'];
        $date     = $param['date'];

        if (!empty($chatid)) {
            $where .= " and chatid='$chatid'";
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archivesCount = $dsql->SetQuery("SELECT `id` FROM `#@__livechat` WHERE 1 = 1" . $where);
        //总条数
        $totalCount = $dsql->dsqlOper($archivesCount, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount / $pageSize);

        if ($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');



        if($date){
            $where .= ' AND `ftime` >= ' . $date;
        }

        $order = " ORDER BY ftime DESC";

        $atpage   = $pageSize * ($page - 1);
        $where1   = " LIMIT $atpage, $pageSize";
        $archives = $dsql->SetQuery("SELECT id,chatid,userid,username,userphoto,content,ftime FROM `#@__livechat` WHERE 1 = 1" . $where);
        $results  = $dsql->dsqlOper($archives . $order . $where1, "results");
        foreach ($results as $k => $v){
            //红包
            if(strstr($v['content'], "__H__:")){
                //检查是否被抢完
                $h_id = str_replace("__H__:", "", $v['content']);
                $sql = $dsql->SetQuery("SELECT `state`,`note` FROM `#@__live_hongbao` WHERE `id` = $h_id");
                $ret = $dsql->dsqlOper($sql, "results");
                $results[$k]['note'] = $ret[0]['note'] ? $ret[0]['note'] : '恭喜发财，大吉大利';

                if($ret[0]['state'] == 1){
                    $results[$k]['h_state'] = 1; //已抢完
                }else{
                    //检查自己是否抢过
                    $is_sql   = $dsql->SetQuery("SELECT `id` FROM `#@__live_hrecv_list` WHERE `hid` = $h_id AND `recv_user` = {$userLogin->getMemberID()}");
                    $is_count = $dsql->dsqlOper($is_sql, "totalCount");
                    $results[$k]['h_state'] = $is_count ? 2 : ''; //已抢过
                }
            }
            //礼物
            if(strstr($v['content'], "__L__:")){
                //检查是否被抢完
                $h_id = str_replace("__L__:", "", $v['content']);
                $sql = $dsql->SetQuery("SELECT * FROM `#@__live_reward` WHERE `id` = $h_id");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $results[$k]['is_gift'] = $ret[0]['gift_id'] ? 1 : 0;
                    $results[$k]['num'] = $ret[0]['num'];
                    $results[$k]['amount'] = $ret[0]['amount'];
                    if($ret[0]['gift_id'] != 0){
                        $sql_ = $dsql->SetQuery("SELECT `gift_name` FROM `#@__live_gift` WHERE `id` = {$ret[0]['gift_id']}");
                        $ret_ = $dsql->dsqlOper($sql_, "results");
                        $results[$k]['gift_name'] = $ret_[0]['gift_name'];
                    }
                }
            }
        }

        if ($results) {
            foreach ($results as $key => $row) {
                $row['userphoto'] = !empty($row['userphoto']) ? $row['userphoto'] : '/static/images/noPhoto_40.jpg';
                $row['ftime']     = date("Y-m-d", $row['ftime']);
                $list[$key]       = $row;
            }
        }
        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount,
            "date" => array_reverse($list) ? array_reverse($list)[0]['ftime'] : date( 'm-d H:i:s', time())
        );
        return array("pageInfo" => $pageinfo, "list" => array_reverse($list));

    }

    /**
     * 获取ossurl
     */
    public function getOssUrl()
    {
        global $dsql;
        //$param 		= $this->param;
        $id = $this->param;

        if (empty($id)) return array("state" => 200, "info" => '参数错误');

        $sql = $dsql->SetQuery("SELECT `id`,`user`,`streamname` FROM `#@__livelist` WHERE `id` = $id ");
        $ret = $dsql->dsqlOper($sql, "results");

        if ($ret) {
            $this->param = $ret[0]['streamname'];
            $detail      = $this->describeLiveStreamRecordIndexFiles();
            if (isset($detail['Message'])) {
                return 'ok';
            } else {
                $file       = $detail['RecordIndexInfoList']['RecordIndexInfo'][0]['OssObject'];
                $requestUrl = $detail['RecordIndexInfoList']['RecordIndexInfo'][0]['RecordUrl'];
                if ($file) {
                    $archives = $dsql->SetQuery("UPDATE `#@__livelist` SET  `ossobject`='$file',`ossurl`='$requestUrl' WHERE `id` = " . $id);
                    $results  = $dsql->dsqlOper($archives, "update");
                }
                return 'ok';
            }
        }
    }


    /**
     * 直播支付
     */
    public function livePay()
    {
        global $dsql;
        global $userLogin;
        global $cfg_basehost;
        global $langData;

        $isMobile = isMobile();

        $param   = $this->param;
        $liveid  = $param['liveid'];      //liveID
        $amount  = $param['amount'];   //金额
        $paytype = $param['paytype'];  //支付方式
        $qr      = $param['qr'];  //扫码支付

        $uid = $userLogin->getMemberID();  //当前登录用户

        if ($uid == -1) {
            if($qr){
                return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
            }else{
                header("location:" . $cfg_basehost . "/login.html");
                exit;
            }
        }

        $sql = $dsql->SetQuery("SELECT `state`, `startmoney`, `endmoney` from `#@__livelist` WHERE id = {$liveid}");
        $res = $dsql->dsqlOper($sql, "results");
        if ($res) {
            if ($res[0]['state'] == 1) {
                $amount = $res[0]['startmoney'];
            } else {
                $amount = $res[0]['endmoney'];
            }
        }

        //验证金额
        if ($amount <= 0 || !is_numeric($liveid)) {
            $ischeck = true;
            if($isMobile){
                $url = getUrlPath(array(
                    'service' => 'live',
                    'template' => 'h_detail',
                    'id' => $liveid
                ));
                header("location:" . $url);
                die;
            }else{
                if(empty($paytype)){
                    $ischeck = true;
                }else{
                    $ischeck = false;
                }

                if(!$ischeck){
                    $url = getUrlPath(array(
                        'service' => 'live',
                        'template' => 'detail',
                        'id' => $liveid
                    ));
                    if($qr){
                        return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！
                    }else{
                        header("location:" . $url);
                        die;
                    }

                }

            }
        }

        //订单号
        $ordernum = create_ordernum();
        $date     = GetMkTime(time());
        $archives = $dsql->SetQuery("INSERT INTO `#@__live_payorder` (`live_id`, `user_id`, `order_id`, `date`, `amount`, `status`, `paysee`) VALUES ('$liveid', '$uid', '$ordernum', '$date', '$amount', '0', '1')");
        $return   = $dsql->dsqlOper($archives, "update");
        if ($return != "ok") {
            if($qr){
                return array("state" => 200, "info" => "提交失败，请稍候重试！");//提交失败，请稍候重试！
            }else{
                die("提交失败，请稍候重试！");
            }
        }
        if($qr){
            return createPayForm("live", $ordernum, $amount, $paytype, "观看直播");
        }

        if($isMobile){
            $param = array(
                "service" => "live",
                "template" => "pay",
                "param" => "ordernum=".$ordernum
            );
            header("location:".getUrlPath($param));
            die;
        }

        //跳转至第三方支付页面
        createPayForm("live", $ordernum, $amount, $paytype, "观看直播");

    }

    /**
     * 直播支付回调
     */
    public function paySuccess()
    {
        global $cfg_secureAccess;
        global $cfg_basehost;
        global $userLogin;

        $param = $this->param;
        if (!empty($param)) {
            global $dsql;
            $liveid    = $param['liveid'];      //liveID
            $amount    = $param['amount'];   //金额
            $paytype   = $param['paytype'];  //支付方式
            $ordernum  = $param['ordernum'];     //订单号
            $ishongbao = $param['hongbao'];
            $gift      = $param['gift'];

            $uid  = $userLogin->getMemberID();  //当前登录用户
            $date = GetMkTime(time());

            //查询订单信息
            $sql = $dsql->SetQuery("SELECT * FROM `#@__live_payorder` WHERE `order_id` = '$ordernum'");
            $ret = $dsql->dsqlOper($sql, "results");
            if ($ret) {

                $id     = $ret[0]['id'];
                $lid    = $ret[0]['live_id'];
                $to     = $ret[0]['user_id'];
                $aid    = $ret[0]['order_id'];
                $amount = $ret[0]['amount'];

                $upoint   = $ret[0]['point'];
                $ubalance = $ret[0]['balance'];
                $payprice = $ret[0]['payprice'];
                $ishongbao= $ret[0]['hongbao'];
                $gift     = $ret[0]['gift'];
                $paysee   = $ret[0]['paysee'];
                if($paysee){
                    $sql = $dsql->SetQuery("SELECT `user` FROM `#@__livelist` WHERE `id` = '$lid'");
                    $userret = $dsql->dsqlOper($sql, "results");
                    $userto  = $userret[0]['user'];
                }


                $sql_in = $dsql->SetQuery("UPDATE `#@__livelist_auth` set `is_auth` = 1 where `user_id` = {$to} AND `live_id` = '$lid'");
                $dsql->dsqlOper($sql_in, "update");

                $archives = $dsql->SetQuery("UPDATE `#@__live_payorder` SET `status` = '1' WHERE `order_id` = '$aid'");
                $dsql->dsqlOper($archives, "update");


                //获取会员名
                $username = "";
                $sql      = $dsql->SetQuery("SELECT `username`, `nickname`,`photo` FROM `#@__member` WHERE `id` = $to");
                $ret      = $dsql->dsqlOper($sql, "results");
                if ($ret) {
                    $username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
                    $photo = getFilePath($ret[0]['photo']);
                }

                if($ishongbao || $gift || $paysee){

                    if($ishongbao){
                        $info = '直播红包积分消费：' . $ordernum;
                        $info_ = '直播红包消费：' . $ordernum;
                    }elseif($gift){
                        $info = '直播打赏积分消费：' . $ordernum;
                        $info_ = '直播打赏消费：' . $ordernum;
                    }elseif($paysee){
                        $info = '直播付费观看消费：' . $ordernum;
                        $info_ = '直播付费观看消费：' . $ordernum;
                    }

                    $totalPrice = $payprice;

                    //扣除会员积分
                    if(!empty($upoint) && $upoint > 0){
                        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` - '$upoint' WHERE `id` = '$to'");
                        $dsql->dsqlOper($archives, "update");

                        //保存操作日志

                        $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$to', '0', '$upoint', '$info', '$date')");
                        $dsql->dsqlOper($archives, "update");
                    }

                    //扣除会员余额
                    if(!empty($ubalance) && $ubalance > 0){
                        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$ubalance' WHERE `id` = '$to'");
                        $dsql->dsqlOper($archives, "update");
                        $totalPrice += $ubalance;
                    }

                    //增加冻结金额
                    $archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` + '$totalPrice' WHERE `id` = '$to'");
                    $dsql->dsqlOper($archives, "update");

                    //保存操作日志
                    if($totalPrice>0 || $paysee){
                        $totalPrice = $paysee ? $amount : $totalPrice;
                        $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$to', '0', '$totalPrice', '$info_', '$date')");//家政消费
                        $dsql->dsqlOper($archives, "update");
                    }
                }

                if ($ishongbao) {
                    $sql_h      = $dsql->SetQuery("SELECT * FROM `#@__live_hongbao` WHERE `payid` = '$ordernum'");
                    $ret_h      = $dsql->dsqlOper($sql_h, "results");
                    if($ret_h){
                        $data_h = $ret_h[0];

                        $fromToken = '';
                        $IMtokn = new siteConfig();
                        if(method_exists($IMtokn,'getImToken')){
                            $token     = $IMtokn->getImToken();
                            $fromToken = $token['token'];

                            $this->param = array(
                                'content' => '__H__:' . $data_h['id'],
                                'contentType' => 'text',
                                'from' => $fromToken,
                                'mark' => 'chatRoom' . $lid
                            );
                            $IMtokn = new siteConfig($this->param);
                            $IMtokn->sendImChatRoom();
                        }

                        // 发送红包
                        $this->param = [
                            'userid' => $uid,
                            'username' => $username,
                            'content' => '__H__:' . $data_h['id'],
                            'userphoto' => $photo,
                            'chatid' => $data_h['chatid'],
                            'system' => true
                        ];
                        $this->chatTalk();
                    }


                }elseif($gift){
                    $sql_h      = $dsql->SetQuery("SELECT * FROM `#@__live_reward` WHERE `payid` = '$ordernum'");
                    $ret_h      = $dsql->dsqlOper($sql_h, "results");
                    if($ret_h){
                        $data_h = $ret_h[0];

                    }

                    include HUONIAOROOT . '/include/config/settlement.inc.php';
                    $amount = $amount - $amount * $cfg_liveFee * 0.01;
                    //将费用打给直播用户

                    $sql_user    = $dsql->SetQuery("SELECT `user` FROM `#@__livelist` WHERE `id` = '$lid'");
                    $ret_user    = $dsql->dsqlOper($sql_user, "results");
                    $liveuser    = $ret_user[0]['user'];

                    $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$amount' WHERE `id` = '$liveuser'");
                    $dsql->dsqlOper($archives, "update");

                    $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$liveuser', '1', '$amount', '直播打赏', '$date')");
                    $dsql->dsqlOper($archives, "update");

                    $fromToken = '';
                    $IMtokn = new siteConfig();
                    if(method_exists($IMtokn,'getImToken')){
                        $token     = $IMtokn->getImToken();
                        $fromToken = $token['token'];

                        $this->param = array(
                            'content' => '__L__:' . $data_h['id'],
                            'contentType' => 'text',
                            'from' => $fromToken,
                            'mark' => 'chatRoom' . $lid
                        );
                        $IMtokn = new siteConfig($this->param);
                        $IMtokn->sendImChatRoom();
                    }


                    // 发送红包
                    $this->param = [
                        'userid' => $uid,
                        'username' => $username,
                        'content' => '__L__:' . $data_h['id'],
                        'userphoto' => $photo,
                        'chatid' => $data_h['chatid'] ? str_replace("chatroom", "", $data_h['chatid']) : '',
                        'system' => true
                    ];
                    $this->chatTalk();

                } else {
                    //将费用打给直播用户
                    if($paysee){
                        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$amount' WHERE `id` = '$userto'");
                        $dsql->dsqlOper($archives, "update");

                        $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userto', '1', '$amount', '付费观看收入', '$date')");
                        $dsql->dsqlOper($archives, "update");
                    }else{
                        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$amount' WHERE `id` = '$to'");
                        $dsql->dsqlOper($archives, "update");
                    }

                }
                //会员通知
                $param = array(
                    "service" => "live",
                    "template" => "detail",
                    "id" => $lid
                );

                updateMemberNotice($to, "会员-直播通知", $param, array("username" => $username, "title" => '', 'amount' => $amount, "date" => date("Y-m-d H:i:s", $date)));

            }

        }
    }


    /**
     * 获取打赏榜
     */
    public function getRewardList()
    {
        global $dsql;
        $param = $this->param;
        if (!empty($param)) {
            $liveid = $param['liveid'];
        } else {
            return array('state' => 200, 'info' => '参数不正确');
        }

        $sql = $dsql->SetQuery("SELECT `id`, `live_id`, `reward_userid`, `amount`, (SELECT sum(`amount`) FROM `#@__live_reward` WHERE `live_id` = $liveid AND `reward_userid` = a.`reward_userid` AND `gift_id` = 0) sumamount FROM `#@__live_reward` a WHERE `live_id` = $liveid AND `gift_id` = 0 GROUP BY `reward_userid` ORDER BY `sumamount` DESC LIMIT 0,10");
        $ret = $dsql->dsqlOper($sql, "results");

        foreach ($ret as $key => $item) {
            $ret[$key]['user'] = getMemberDetail($item['reward_userid']);
        }

        return $ret;


    }

    /**
     * 邀请榜
     */
    public function getShareList()
    {
        global $dsql;
        $param = $this->param;
        if (!empty($param)) {
            $liveid = $param['liveid'];
        } else {
            return array('state' => 200, 'info' => '参数不正确');
        }

        $sql = $dsql->SetQuery("SELECT a.`id`, a.`live_id`, a.`share_userid`, (SELECT count(`id`) FROM `#@__live_share_success_user` WHERE `live_id` = $liveid  AND `share_user` = a.share_userid ) scount FROM `#@__live_share` a WHERE a.`live_id` = $liveid GROUP BY a.`share_userid` ORDER BY `scount` DESC LIMIT 0, 10");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            foreach ($ret as $key => $item) {
                $ret[$key]['user'] = getMemberDetail($item['share_userid']);
            }
        }
        return $ret;

    }

    /**
     * 生成红包
     */
    public function makeHongbao()
    {
        global $dsql;
        global $userLogin;
        $param = $this->param;
        if (!empty($param)) {
            $liveid = $param['liveid'];
            $amount = $param['amount'];
            $count  = $param['count'];
            $note   = $param['note'];
            $chatRoomId   = $param['chatid'];
        } else {
            return array('state' => 200, 'info' => '参数不正确');
        }
        if($amount < 1){
            return array('state' => 200, 'info' => '最少金额为1元');
        }

        $date   = time();
        $userid = $userLogin->getMemberID();
        if ($userid == -1) {
            return array("state" => 200, "info" => '登录超时');
        }

        $ordernum = $date . $amount . $chatRoomId . rand(100, 300);


        $archives = $dsql->SetQuery("INSERT INTO `#@__live_payorder` (`live_id`, `user_id`, `order_id`, `date`, `amount`, `status`) VALUES ('$liveid', '$userid', '$ordernum', '$date', '$amount', '0' )");
        $return   = $dsql->dsqlOper($archives, "update");

        $chatRoomId = 'chatroom'.$chatRoomId;
        $sql = $dsql->SetQuery("INSERT INTO `#@__live_hongbao` (`live_id` , `amount`, `user_id`, `count`, `payid`, `date`, `note`, `amount1`, `count1`, `chatid`) VALUES ( $liveid, '$amount', $userid, $count, '$ordernum', $date, '$note', '$amount', $count, '$chatRoomId')");
        $hid = $dsql->dsqlOper($sql, 'lastid');
        $param  = [
            'service' => 'live',
            'template' => 'pay',
        ];
        $url = getUrlPath($param);
        return $url . '?ordernum='.$ordernum;
    }

    /**
     * 红包支付
     */
    public function pay()
    {
        global $dsql;
        global $cfg_secureAccess;
        global $cfg_basehost;
        global $userLogin;
        global $cfg_pointRatio;

        $ordernum   = $this->param['ordernum'];
        $paytype    = $this->param['paytype'];
        $check      = (int)$this->param['check']; //第一次异步请求为1，第二次同步为0
        $usePinput  = $this->param['usePinput'];
        $point      = (float)$this->param['point'];
        $useBalance = $this->param['useBalance'];
        $balance    = (float)$this->param['balance'];
        $paypwd     = $this->param['paypwd'];

        $userid = $userLogin->getMemberID();
        if ($userid == -1) {
            if ($check) {
                return array("state" => 200, "info" => "登陆超时");
            } else {
                die("登陆超时");
            }
        }
        if ($ordernum) {
            $sql = $dsql->SetQuery("SELECT * FROM `#@__live_payorder` WHERE `order_id` = '$ordernum'");
            $ret = $dsql->dsqlOper($sql, "results");
            if ($ret) {
                $data       = $ret[0];
                $live_id    = $data['live_id'];
                $totalPrice = $data['amount'];
                $paysee     = $data['paysee'];
                $date       = GetMkTime(time());
                //查询会员信息
                $userinfo  = $userLogin->getMemberInfo();
                $usermoney = $userinfo['money'];
                $userpoint = $userinfo['point'];
                $tit       = array();
                $useTotal  = 0;
                //判断是否使用积分，并且验证剩余积分
                if ($usePinput == 1 && !empty($point)) {
                    if ($userpoint < $point) return array("state" => 200, "info" => "您的可用" . $cfg_pointName . "不足，支付失败！");
                    $useTotal += $point / $cfg_pointRatio;
                    $tit[]    = "integral";
                }
                //判断是否使用余额，并且验证余额和支付密码
                if ($useBalance == 1 && !empty($balance) && !empty($paypwd)) {
                    if (!empty($balance) && empty($paypwd)) {
                        if ($check) {
                            return array("state" => 200, "info" => "请输入支付密码！");
                        } else {
                            die("请输入支付密码！");
                        }
                    }
                    //验证支付密码
                    $archives = $dsql->SetQuery("SELECT `id`, `paypwd` FROM `#@__member` WHERE `id` = '$userid'");
                    $results  = $dsql->dsqlOper($archives, "results");
                    $res      = $results[0];
                    $hash     = $userLogin->_getSaltedHash($paypwd, $res['paypwd']);
                    if ($res['paypwd'] != $hash) {
                        if ($check) {
                            return array("state" => 200, "info" => "支付密码输入错误，请重试！");
                        } else {
                            die("支付密码输入错误，请重试！");
                        }
                    }
                    //验证余额
                    if ($usermoney < $balance) {
                        if ($check) {
                            return array("state" => 200, "info" => "您的余额不足，支付失败！");
                        } else {
                            die("您的余额不足，支付失败！");
                        }
                    }
                    $useTotal += $balance;
                    $tit[]    = "money";
                }
                // 使用了余额
                if ($useTotal) {
                    if ($useTotal > $totalPrice) {
                        if ($check) {
                            return array("state" => 200, "info" => "您使用的" . join("和", $tit) . "超出订单总费用，请重新输入要使用的" . join("和", $tit));
                        } else {
                            die("您使用的" . join("和", $tit) . "超出订单总费用，请重新输入要使用的" . join("和", $tit));
                        }
                        // 余额不足
                    }
                }
                $amount = $totalPrice - $useTotal;
                if ($amount > 0 && empty($paytype)) {
                    if ($check) {
                        return array("state" => 200, "info" => "请选择支付方式！");
                    } else {
                        die("请选择支付方式！");
                    }
                }

                //判断是否是积分与余额支付s
                $pointMoney = $usePinput ? $point / $cfg_pointRatio : 0;
                $balanceMoney = $balance;

                $usePointMoney = 0;
				$useBalanceMoney = 0;

				//先判断积分是否足够支付总价
				//如果足够支付：
				//1.把还需要支付的总价重置为0
				//2.积分总额减去用掉的
				//3.记录已经使用的积分
				if($totalPrice < $pointMoney){
					$pointMoney -= $totalPrice;
					$usePointMoney = $totalPrice;
					$totalPrice = 0;
				//积分不够支付再判断余额是否足够
				//如果积分不足以支付总价：
				//1.总价减去积分抵扣掉的部部分
				//2.积分总额设置为0
				//3.记录已经使用的积分
				}else{
					$totalPrice -= $pointMoney;
					$usePointMoney = $pointMoney;
					$pointMoney = 0;
					//验证余额是否足够支付剩余部分的总价
					//如果足够支付：
					//1.把还需要支付的总价重置为0
					//2.余额减去用掉的部分
					//3.记录已经使用的余额
					if($totalPrice < $balanceMoney){
						$balanceMoney -= $totalPrice;
						$useBalanceMoney = $totalPrice;
						$totalPrice = 0;
					//余额不够支付的情况
					//1.总价减去余额付过的部分
					//2.余额设置为0
					//3.记录已经使用的余额
					}else{
						$totalPrice -= $balanceMoney;
						$useBalanceMoney = $balanceMoney;
						$balanceMoney = 0;
					}
				}
                $pointMoney_ = $usePointMoney * $cfg_pointRatio;
                if($paysee==1){//付费观看
                    $gift = 0;
                    $hongbao = 0;
                }else{
                    if(strstr($ordernum, '00000' )){
                        $gift = 1;
                        $hongbao = 0;
                    }else{
                        $hongbao = 1;
                        $gift = 0;
                    }
                }

                $archives = $dsql->SetQuery("UPDATE `#@__live_payorder` SET `gift` = '$gift', `hongbao` = '$hongbao', `point` = '$pointMoney_', `balance` = '$useBalanceMoney', `payprice` = '$totalPrice' WHERE `order_id` = '$ordernum'");
                $dsql->dsqlOper($archives, "update");
                //判断是否是积分与余额支付e

                if ($check) return "ok";
                if ($amount > 0) {
                    if($paysee==1){
                        $tit = '观看直播';
                    }else{
                        $tit = '直播红包';
                    }
                    createPayForm("live", $ordernum, $amount, $paytype, $tit);
                    // 余额支付
                } else {

                    $body     = serialize($param);
                    $archives = $dsql->SetQuery("INSERT INTO `#@__pay_log` (`ordertype`, `ordernum`, `uid`, `body`, `amount`, `paytype`, `state`, `pubdate`) VALUES ('live', '$ordernum', '$userid', '$body', 0, '$paytype', 1, $date)");
                    $dsql->dsqlOper($archives, "results");
                    if(strstr($ordernum, '00000' )){
                        $this->param = array(
                            "paytype" => $paytype,
                            "ordernum" => $ordernum,
                            "gift" => 1
                        );
                    }else{
                        $this->param = array(
                            "paytype" => $paytype,
                            "ordernum" => $ordernum,
                            "hongbao" => 1
                        );
                    }
                    //执行支付成功的操作

                    $this->paySuccess();

                    $param = array(
                        "service" => "live",
                        "template" => "h_detail",
                        "id" => $live_id
                    );
                    $url   = getUrlPath($param);
                    header("location:" . $url);
                }

            } else {
                if ($check) {
                    return array("state" => 200, "info" => "订单不存在或已支付");
                } else {
                    $param = array(
                        "service" => "live",
                        "template" => "detail",
                        "id" => $live_id
                    );
                    $url   = getUrlPath($param);
                    header("location:" . $url);
                    die();
                }
            }

        } else {
            if ($check) {
                return array("state" => 200, "info" => "订单不存在");
            } else {
                $param = array(
                    "service" => "live",
                    "template" => "detail",
                    "id" => $live_id
                );
                $url   = getUrlPath($param);
                header("location:" . $url);
                die();
            }

        }
    }

    /**
     * 抢红包
     */
    public function getHongbao()
    {
        global $dsql;
        global $userLogin;
        $param = $this->param;
        if (!empty($param)) {
            $h_id = $param['h_id'];
            $date      = time();
        } else {
            return array('state' => 200, 'info' => '参数不正确');
        }
        $loginUSer = $userLogin->getMemberID();
        if ($loginUSer == -1) {
            return array("state" => 200, "info" => '登录超时');
        }

        //获取红包
        $sql = $dsql->SetQuery("SELECT * FROM `#@__live_hongbao` WHERE `id` = $h_id ");
        $ret = $dsql->dsqlOper($sql, "results");
        if ($ret) {
            $amount  = $ret[0]['amount']; //总额
            $count   = $ret[0]['count'];
            $state   = $ret[0]['state'];
            $amount1 = $ret[0]['amount1']; //红包剩余金额
            $count1  = $ret[0]['count1']; //剩余数量
            $hid     = $ret[0]['id']; //红包id
            //判断当前用户是否抢过
            $is_sql   = $dsql->SetQuery("SELECT `id` FROM `#@__live_hrecv_list` WHERE `hid` = $hid AND `recv_user` = $loginUSer");
            $is_count = $dsql->dsqlOper($is_sql, "totalCount");
            if ($is_count) {
                $state = 202;
                $info = '不能重复领取';
                goto EEE;
            }
            if ($state == 1 || $count1 == 0 || $amount1 <= 0) {
                $state = 201;
                $info = '红包已被抢完';
                goto EEE;
            }
            if ($count1 == 1) {
                $get_amount = $amount1;
            } else {
                //剩余平均值
                $pre = ($amount1 / $count1) * 2;
                $min = 0.01;
                $max = $pre;
                //抢到的红包
                $get_amount = round($min + mt_rand() / mt_getrandmax() * ($max - $min), 2);
            }

            //抢到的用户加钱
            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$get_amount' WHERE `id` = $loginUSer");
            $dsql->dsqlOper($archives, "update");

            $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$loginUSer', '1', '$get_amount', '抢直播红包收入', '$date')");
            $dsql->dsqlOper($archives, "update");

            $shengyu_m = $amount1 - $get_amount;
            $shengyu_c = $count1 - 1;
            if ($shengyu_m <= 0 || $shengyu_c == 0) {
                //已抢完
                $archives = $dsql->SetQuery("UPDATE `#@__live_hongbao` SET  `amount1` =  '0', `count1` = 0, `state` = 1 WHERE `id` = $hid ");
                $dsql->dsqlOper($archives, "update");
                $state = 203;
                $iss = 1;
            } else {
                //更新剩余红包
                $archives = $dsql->SetQuery("UPDATE `#@__live_hongbao` SET  `amount1` =  '$shengyu_m', `count1` = $shengyu_c WHERE `id` = $hid");
                $dsql->dsqlOper($archives, "update");
            }

            //用户抢红包记录
            $sql = $dsql->SetQuery("INSERT INTO `#@__live_hrecv_list` (`hid` , `recv_user`, `recv_money`, `date`) VALUES ( $hid, $loginUSer, '$get_amount', $date)");
            $dsql->dsqlOper($sql, 'update');
        }
        EEE:
        return ['state' => '100',
            'shengyu_money' => $shengyu_m, 'shengyu_count' => $shengyu_c, 'get_amount' => $get_amount, 'is_fin' => $iss ? $iss : 0, 'states' => $state ? $state : 200, 'info' => $info
        ];


    }

    public function getHongBaoInfo()
    {
        global $dsql;
        global $userLogin;
        $uid = $userLogin->getMemberID();
        $param = $this->param;
        if (!empty($param)) {
            $id = $param['h_id'];
        } else {
            return array('state' => 200, 'info' => '参数不正确');
        }
        $sql = $dsql->SetQuery("SELECT * FROM `#@__live_hongbao` WHERE `id` = $id");
        $ret1 = $dsql->dsqlOper($sql, "results");
        foreach ($ret1 as &$v){
            $v['user'] = getMemberDetail($v['user_id']);
        }
        unset($v);
        $sql = $dsql->SetQuery("SELECT * FROM `#@__live_hrecv_list` WHERE `hid` = $id AND `recv_user` = $uid");
        $ret2 = $dsql->dsqlOper($sql, "results");

        $sql = $dsql->SetQuery("SELECT * FROM `#@__live_hrecv_list` WHERE `hid` = $id ");
        $ret3 = $dsql->dsqlOper($sql, "results");
        foreach ($ret3 as $k => &$item){
            $item['user'] = getMemberDetail($item['recv_user']);
            $item['date'] = date("H:i", $item['date']);
        }
        unset($item);
        return array('state' => 100, 'list'=>$ret3, 'user' => $ret2[0], 'hongbao'=>$ret1[0]);
    }


    public function getGift()
    {
        global $dsql;
        $sql = $dsql->SetQuery("SELECT * FROM `#@__live_gift`");
        $ret = $dsql->dsqlOper($sql, "results");
        $list = array();
        if($ret){
            foreach($ret as $key => $val){
                array_push($list, array(
                    'id' => $val['id'],
                    'gift_name' => $val['gift_name'],
                    'gift_price' => $val['gift_price'],
                    'gift_litpic' => getFilePath($val['gift_litpic'])
                ));
            }
        }
        return $list;
    }

    //送礼物
    public function songGift()
    {
        global $dsql;
        global $userLogin;
        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array('state' => 200, 'info' => '请登录');
        }
        $param = $this->param;
        $reward_userid = $param['reward_userid'];
        $live_id = $param['live_id'];
        $num = $param['num'];
        $gift_id = $param['gift_id'];
        $chat_id = $param['chat_id'];
        $amount = $param['amount'];
        if($gift_id != 0){
            $sql = $dsql->SetQuery("SELECT * FROM `#@__live_gift` WHERE `id` = $gift_id");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $gift_price = $ret[0]['gift_price'];
                $amount = $num * $gift_price;
            }else{
                return array('state' => 200, 'info' => '参数不正确');
            }
        }

        $time = time();
        $order = '00000' . $time . $reward_userid . rand(100, 500);
        $archives = $dsql->SetQuery("INSERT INTO `#@__live_payorder` (`live_id`, `user_id`, `order_id`, `date`, `amount`, `status`) VALUES ('$live_id', '$uid', '$order', '$time', '$amount', '0' )");
        $return   = $dsql->dsqlOper($archives, "update");

        $archives = $dsql->SetQuery("INSERT INTO `#@__live_reward` (`live_id`, `reward_userid`, `amount`, `payid`, `date`, `state`, `gift_id`, `num`, `chatid`) VALUES ('$live_id', '$uid', '$amount', '$order', '$time', '0', $gift_id, $num , '$chat_id')");
        $return   = $dsql->dsqlOper($archives, "update");

        $params = [
            'service' => 'live',
            'template' => 'pay'
        ];
        $url = getUrlPath($params) . '?ordernum=' . $order;
        return $url;

    }


    /**
     * 图文直播
     */
    public function fabuImgText(){
        global $dsql;
        global $userLogin;

        $uid = $userLogin->getMemberID();
        if($uid <= 0) return array('state' => 200, 'info' => '登陆超时，请重新登陆');

        $param = $this->param;
        $id = (int)$param['id'];
        $text = $param['text'];
        $imglist = $param['imglist'];

        if(empty($id)) return array('state' => 200, 'info' => '参数错误');

        if(empty($text) && empty($imglist)) return array('state' => 200, 'info' => '请上传图片或输入文字内容');

        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__livelist` WHERE `id` = $id AND `user` = $uid");
        $res = $dsql->dsqlOper($sql, "results");
        if(!$res) return array('state' => 200, 'info' => '直播不存在');
        // if($res['state'] == 0) return array('state' => 200, 'info' => '直播未开始');

        $pubdate = time();
        $sql = $dsql->SetQuery("INSERT INTO `#@__live_imgtext` (`live_id`, `img`, `text`, `pubdate`) VALUES ('$id', '$imglist', '$text', '$pubdate')");
        $res = $dsql->dsqlOper($sql, "lastid");
        if(is_numeric($res)){
            return '发布成功';
        }else{
            return array('state' => 200, 'info' => '发布失败');
        }

    }

    /**
     * 图文直播列表
     */
    public function imgTextList(){
        global $dsql;
        global $userLogin;
        $param    = $this->param;
        $where    = "";
        $pageinfo = $list = array();
        $totalPage = $totalCount = 0;

        $page     = $param['page'];
        $pageSize = $param['pageSize'];
        $chatid   = $param['chatid'];
        $order    = $param['order'];
        $keywords = $param['keywords'];
        $date     = $param['date'];
        $id       = $param['id'];
        $get      = $param['get'];

        if (empty($chatid)) {
            return array('state' => 200, 'info' => '直播id');
        }

        $where .= " and `live_id` = '$chatid'";
        if($keywords){
            $where .= " and `text` LIKE '%$keywords%'";
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if(empty($get)){
            $archivesCount = $dsql->SetQuery("SELECT COUNT(*) c FROM `#@__live_imgtext` WHERE 1 = 1" . $where);
            //总条数
            $res = $dsql->dsqlOper($archivesCount, "results");
            $totalCount = $res[0]['c'];
            //总分页数
            $totalPage = ceil($totalCount / $pageSize);

            if ($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');
        }

        // 获取最新
        if($date){
            $where .= ' AND `pubdate` > ' . $date;
        }
        if($id){
            $where .= ' AND `id` < ' . $id;
        }

        $order = $order ? $order : "DESC";
        $order = " ORDER BY pubdate ".$order;

        $atpage   = $pageSize * ($page - 1);
        $where1   = " LIMIT $atpage, $pageSize";
        $archives = $dsql->SetQuery("SELECT * FROM `#@__live_imgtext` WHERE 1 = 1" . $where);
        $results  = $dsql->dsqlOper($archives . $order . $where1, "results");
        $list = array();
        foreach ($results as $k => $v){
            $list[$k]['id'] = $v['id'];
            $list[$k]['text'] = $v['text'];
            $list[$k]['live_id'] = $v['live_id'];
            $list[$k]['pubdate'] = $v['pubdate'];

            //用户信息
            $sql = $dsql->SetQuery("SELECT `id`, `user` FROM `#@__livelist` WHERE `id` = " . $v['live_id']."");
            $res = $dsql->dsqlOper($sql, "results");
            $member                 = getMemberDetail($res[0]['user']);
            $list[$k]['nickname']   = !empty($member['username']) ? $member['username'] : cn_substrR($member['nickname'], 5);
            $list[$k]['photo']      = !empty($member['photo']) ? $member['photo'] : '/static/images/404.jpg';
            //用户查看主播列表页面
            $param                 = array(
                "service" => "live",
                "template" => "anchor_index",
                "userid" => $res[0]['user']
            );
            $list[$k]['userurl'] = getUrlPath($param);

            $img = $v['img'];
            $pic = array();
            if($img){
                $a = explode(',', $img);
                foreach ($a as $s => $p) {
                    $pic[$s] = getFilePath($p);
                }
            }
            $list[$k]['img'] = $pic;
        }

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount,
            // "date" => $list ? $list[0]['pubdate'] : time()
        );

        return array("pageInfo" => $pageinfo, "list" => $list);
    }
    /**
     * 删除图文直播消息
     */
    public function delImgText(){
        global $dsql;
        global $userLogin;

        $uid = $userLogin->getMemberID();
        if($uid <= 0) return array('state' => 200, 'info' => '登陆超时，请重新登陆');

        $param = $this->param;
        $id = (int)$param['id'];
        if(empty($id)) return array('state' => 200, 'info' => '参数错误');

        // $sql = $dsql->SetQuery("SELECT `id` FROM `#@__livelist` where id='$id' and user='$userid'");
        // $res = $dsql->dsqlOper($sql, "results");
        // if(!$res) return array('state' => 200, 'info' => '直播不在或权限不足');

        $sql = $dsql->SetQuery("SELECT c.`id`, c.`img` FROM `#@__live_imgtext` c LEFT JOIN `huoniao_livelist` l ON c.`live_id` = l.`id` WHERE c.`id` = $id AND l.`user` = $uid");
        $res = $dsql->dsqlOper($sql, "results");
        if(!$res) return array('state' => 200, 'info' => '消息不存在或权限不足');

        $img = $res[0]['img'];

        $sql = $dsql->SetQuery("DELETE FROM `#@__live_imgtext` WHERE `id` = $id");
        $res = $dsql->dsqlOper($sql, "update");
        if($res == "ok"){
            if($img){
                delPicFile($img, 'atlas', 'live');
            }
            return "操作成功";
        }else{
            return array('state' => 200, 'info' => '操作失败');
        }

    }

    /**
     * 删除直播评论
     */
    public function delComment(){
        global $dsql;
        global $userLogin;

        $uid = $userLogin->getMemberID();
        if($uid <= 0) return array('state' => 200, 'info' => '登陆超时，请重新登陆');

        $param = $this->param;
        $id = (int)$param['id'];
        if(empty($id)) return array('state' => 200, 'info' => '参数错误');

        // $sql = $dsql->SetQuery("SELECT `id` FROM `#@__livelist` where id='$id' and user='$userid'");
        // $res = $dsql->dsqlOper($sql, "results");
        // if(!$res) return array('state' => 200, 'info' => '直播不在或权限不足');

        $sql = $dsql->SetQuery("SELECT c.`id` FROM `huoniao_livechat` c LEFT JOIN `huoniao_livelist` l ON substring(c.`chatid`, 9) = l.`id` WHERE c.`id` = $id AND l.`user` = $uid");
        $res = $dsql->dsqlOper($sql, "results");
        if(!$res) return array('state' => 200, 'info' => '评论不存在或权限不足');

        // $sql = $dsql->SetQuery("SELECT `id`, `chatid` FROM `#@__livechat` where `id`='$id'");
        // $res = $dsql->dsqlOper($sql, "results");
        // if(!$res) return array('state' => 200, 'info' => '评论不存在');

        // $lid = str_replace("chatroom", "", $res[0]['chatid']);
        // $sql = $dsql->SetQuery("SELECT `id` FROM `#@__livelist` where `id` = $lid AND `user`='$uid'");
        // $res = $dsql->dsqlOper($sql, "results");
        // if(!$res) return array('state' => 200, 'info' => '权限不足');

        $sql = $dsql->SetQuery("DELETE FROM `#@__livechat` WHERE `id` = $id");
        $res = $dsql->dsqlOper($sql, "update");
        if($res == "ok"){
            return "操作成功";
        }else{
            return array('state' => 200, 'info' => '操作失败');
        }

    }


    /**
     * 点赞
     * @return array
     */
    public function dianzan()
    {
        global $dsql;
        global $userLogin;
        $userid = $userLogin->getMemberID();
        if ($userid == -1) {
            return array('state' => 200, 'info' => '登录超时！');
        }
        $param = $this->param;
        if (!empty($param)) {

            $vid  = $param['vid'];
            $type = $param['type'];
            $temp = $param['temp'];
        } else {
            return array('state' => 200, 'info' => '参数不正确！');
        }
        if ($type == 1) {
            //查看是否已经点过赞
            $sql = $dsql->SetQuery("SELECT * FROM `#@__site_zanmap` WHERE `vid` = $vid AND `userid` = $userid AND `temp` = '$temp'");
            $ret = $dsql->dsqlOper($sql, "results");
            if ($ret) {
                return array('state' => 200, 'info' => '您已点赞！');
            }
            $date = time();
            $sql  = $dsql->SetQuery("INSERT INTO `#@__site_zanmap` (`userid`, `vid`, `temp`, `date`) VALUES ($userid , $vid, '$temp' ,$date)");
            $ret  = $dsql->dsqlOper($sql, "update");
        } else {
            //取消
            $sql = $dsql->SetQuery("DELETE FROM `#@__site_zanmap` WHERE `userid` = $userid AND `vid` = $vid AND `temp` = '$temp'");
            $ret = $dsql->dsqlOper($sql, "update");
        }
        if($ret == "ok"){
            return "ok";
        }else{
            return array('state' => 200, 'info' => '操作失败');
        }
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo()
    {
        global $dsql;
        $param = $this->param;

        $id  = $param['id'];

        if(empty($id)) return array('state' => 200, 'info' => '参数不正确！');

        //会员信息
        $member                 = getMemberDetail($id);
        $memberinfo['nickname'] = !empty($member['company']) ? $member['company'] : $member['nickname'];
        $memberinfo['userid']   = $id;
        $memberinfo['photo']    = !empty($member['photo']) ? getFilePath($member['photo']) : '/static/images/noPhoto_40.jpg';

        //粉丝人数
        $sql     = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__live_follow` WHERE `fid` = " . $id);
        $fansret = $dsql->dsqlOper($sql, "results");
        $memberinfo['totalFans'] = $fansret[0]['t'];

        $sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__livelist` WHERE `user` = " . $id);
        $res = $dsql->dsqlOper($sql, "results");
        $memberinfo['livenum'] = $res[0]['t'];

        $param = array(
            "service" => "live",
            "template" => "anchor_index",
            "userid" => $id
        );
        $memberinfo['userurl'] = getUrlPath($param);

        return $memberinfo;
    }

    /**
     * 聊天具体信息
     */
    public function getChatDetail(){
        global $dsql;
        global $langData;
        global $userLogin;
        $param = $this->param;

        $h_id  = (int)$param['h_id'];
        $type  = (int)$param['type'];

        if(empty($h_id)) return array('state' => 200, 'info' => '参数不正确！');

        $chatArr = array();
        if($type==1){//礼物
            $sql = $dsql->SetQuery("SELECT `gift_id`, `num`, `amount` FROM `#@__live_reward` WHERE `id` = $h_id");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $chatArr['is_gift'] = $ret[0]['gift_id'] ? 1 : 0;
                $chatArr['num']     = $ret[0]['num'];
                $chatArr['amount']  = $ret[0]['amount'];
                $chatArr['type']    = $type;
                if($ret[0]['gift_id'] != 0){
                    $sql_ = $dsql->SetQuery("SELECT `gift_name` FROM `#@__live_gift` WHERE `id` = {$ret[0]['gift_id']}");
                    $ret_ = $dsql->dsqlOper($sql_, "results");
                    $chatArr['gift_name'] = $ret_[0]['gift_name'];
                }
            }
        }elseif($type==2){//红包
            $sql = $dsql->SetQuery("SELECT `state`,`note` FROM `#@__live_hongbao` WHERE `id` = $h_id");
            $ret = $dsql->dsqlOper($sql, "results");
            $chatArr['note'] = $ret[0]['note'] ? $ret[0]['note'] : '恭喜发财，大吉大利';
            $chatArr['type'] = $type;
            if($ret[0]['state'] == 1){
                $chatArr['h_state'] = 1; //已抢完
            }else{
                //检查自己是否抢过
                $is_sql   = $dsql->SetQuery("SELECT `id` FROM `#@__live_hrecv_list` WHERE `hid` = $h_id AND `recv_user` = {$userLogin->getMemberID()}");
                $is_count = $dsql->dsqlOper($is_sql, "totalCount");
                $chatArr['h_state'] = $is_count ? 2 : ''; //已抢过
            }
        }

        return $chatArr;
    }


    /**
     * 直播预约
     */
    public function liveBooking(){
        global $dsql;
        global $userLogin;
        $uid = $userLogin->getMemberID();
        if($uid < 1) return array('state' => 200, 'info' => '登陆超时，请重新登陆');

        $param = $this->param;
        $aid = (int)$this->param['aid'];

        $now = time();

        $sql = $dsql->SetQuery("SELECT `ftime`, `state` FROM `#@__livelist` WHERE `id` = $aid AND `arcrank` = 1");
        $res = $dsql->dsqlOper($sql, "results");
        if(!$res) return array('state' => 200, 'info' => '直播不存在');
        if($res[0]['state'] == 1) return array('state' => 200, 'info' => '直播正在进行');
        if($res[0]['state'] == 2) return array('state' => 200, 'info' => '直播已结束');
        $ftime = $res[0]['ftime'];

        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__live_booking` WHERE `uid` = $uid AND `aid` = $aid");
        $res = $dsql->dsqlOper($sql, "results");
        
        //已经预约的，取消预约
        if($res) {
        	$sql = $dsql->SetQuery("DELETE FROM `#@__live_booking` WHERE `aid` = $aid AND `uid` = '$uid'");
        	$ret = $dsql->dsqlOper($sql, "update");
        	if($ret == 'ok'){
        		return "取消成功";
        	}else{
        		return array('state' => 200, 'info' => '取消失败');
        	}
        }

        // 距离开始时间大于30分钟可以预约
        if($ftime > $now && $ftime - $now < 1800){
            return array('state' => 200, 'info' => '离直播时间不足30分钟，无需预约');
        }

        $sql = $dsql->SetQuery("INSERT INTO `#@__live_booking` (`aid`, `uid`, `ftime`, `notice`, `pubdate`) VALUES ($aid, $uid, $ftime, 0, $now)");
        $id = $dsql->dsqlOper($sql, "lastid");
        if(is_numeric($id)){
            return "预约成功";
        }else{
            return array('state' => 200, 'info' => '预约失败，请稍后重试');
        }
    }

    /**
     * 主播列表
     */
    public function anchorList(){
        global $dsql;
        global $userLogin;
        $where = "";
        $r        = $this->param['r'];
        $myfollow = $this->param['myfollow'];
        $order    = $this->param['order'];
        $title    = $this->param['title'];
        $page     = $this->param['page'];
        $pageSize = $this->param['pageSize'];
        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $uid = $userLogin->getMemberID();

        if($r){
            $where .= " AND `rec` = 1";
        }
        if($myfollow){
            if($uid < 1){
                return array("state" => 200, "info" => '您还没有登录');
            }
            $where .= " AND f.`id` is not null";
        }
        
        if($title){
        	$where .= " AND m.`nickname` like '%".$title."%'";
        }

        $order = " ORDER BY `id` DESC";

        if($order == "r"){
            $order = " ORDER BY `rec` DESC, `id` DESC";
        }elseif($order == "fans"){
            $order = " ORDER BY `fans` DESC, `id` DESC";
        }
        
        $sql = $dsql->SetQuery("SELECT COUNT(*) c FROM `#@__live_anchor` a LEFT JOIN `#@__member_follow` f ON f.`fid` = a.`uid` AND f.`tid` = $uid AND f.`for` = '' LEFT JOIN `#@__member` m ON m.`id` = a.`uid` WHERE 1 = 1".$where);
        $res = $dsql->dsqlOper($sql, "results");
        $totalCount = $res[0]['c'];

        if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $atpage = $pageSize*($page-1);
        $where .= " LIMIT $atpage, $pageSize";

        $list = array();
        $sql = $dsql->SetQuery("SELECT a.`id`, a.`uid`, m.`username`, m.`nickname`, m.`addr`, m.`photo`, (SELECT COUNT(*) FROM `#@__member_follow` f WHERE f.`fid` = a.`uid` AND f.`for` = '') fans, (SELECT COUNT(*) FROM `#@__member_follow` f WHERE f.`tid` = a.`uid` AND f.`for` = '') gz FROM `#@__live_anchor` a LEFT JOIN `#@__member_follow` f ON f.`fid` = a.`uid` AND f.`tid` = $uid AND f.`for` = '' LEFT JOIN `#@__member` m ON m.`id` = a.`uid` WHERE 1 = 1".$where);
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            $uid = $userLogin->getMemberID();
            foreach ($res as $key => $value) {
                $list[$key]['id'] = $value['id'];
                $list[$key]['uid'] = $value['uid'];
                $list[$key]['nickname'] = $value['nickname'] ? $value['nickname'] : $value['username'];
                $list[$key]['photo'] = getFilePath($value['photo']);
                $list[$key]['totalFans'] = $value['fans'];
                $list[$key]['gz'] = $value['gz'];

                //是否关注发布人
                if($uid > 0){
	                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $uid AND `fid` = " . $value['uid'] . " AND `for` = ''");
	                $ret = $dsql->dsqlOper($sql, "results");
	                if($ret){
	                    $list[$key]['isMfollow'] = 1;//关注
	                }elseif($uid == $value['uid']){
	                    $list[$key]['isMfollow'] = 2;//自己
	                }else{
	                    $list[$key]['isMfollow'] = 0;//未关注
	                }
                }else{
                	$list[$key]['isMfollow'] = 0;//未关注
                }
                
                //发布直播数量
                $liveCount = 0;
                $sql = $dsql->SetQuery("SELECT count(*) total FROM `#@__livelist` WHERE `user` = " . $value['uid'] . " AND `arcrank` = 1");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                	$liveCount = $ret[0]['total'];
                }
                $list[$key]['liveCount'] = $liveCount;

                //最近直播
                $sql = $dsql->SetQuery("SELECT `title` FROM `#@__livelist` WHERE `user` = " . $value['uid'] . " AND `arcrank` = 1 ORDER BY `ftime` DESC, `id` DESC limit 1");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $list[$key]['liveTitle'] = $ret[0]['title'];
                }
                
                //区域
                $addr = $value['addr'];
                global $data;
	            $data = "";
	            $addrArr = getParentArr("site_area", $addr);
	            if($addrArr){
	                $addrArr = array_reverse(parent_foreach($addrArr, "typename"));
	                $list[$key]['addrname'] = $addrArr;
	            }else{
	                $list[$key]['addrname'] = array();
	            }
	            
	            //是否正在直播
	            $isLiving = 0;
	            $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__livelist` WHERE `state` = 1 AND `arcrank` = 1 AND `user` = " . $value['uid']);
	            $ret = $dsql->dsqlOper($sql, "results");
	            if($ret && $ret[0]['total'] > 0){
	            	$isLiving = 1;
	            }
	            $list[$key]['isLiving'] = $isLiving;
	            
	            //会员等级
	            $userinfo = $userLogin->getMemberInfo($value['uid']);
	            $list[$key]['level'] = array(
	            	'id' => $userinfo['level'],
	            	'name' => $userinfo['levelName'],
	            	'icon' => $userinfo['levelIcon']
	            );
                
                $param = array(
                    "service" => "live",
                    "template" => "anchor_index",
                    "userid" => $value['id']
                );
                $list[$key]['url'] = getUrlPath($param);
            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);
    }

}


function percent_encode($res)
{
    $res = trim(utf8_encode(urlencode($res)));
    //$res=utf8_encode($res);
    $res = str_replace(array('+', '*', '%7E'), array('%20', '%2A', '~'), $res);
    return $res;
}

function signString($source, $accessSecret)
{
    return base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
}

function uuid($prefix = '')
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid  = substr($chars, 0, 8) . '-';
    $uuid  .= substr($chars, 8, 4) . '-';
    $uuid  .= substr($chars, 12, 4) . '-';
    $uuid  .= substr($chars, 16, 4) . '-';
    $uuid  .= substr($chars, 20, 12);
    return $prefix . $uuid;
}
