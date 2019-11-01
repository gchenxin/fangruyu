<?php if (!defined('HUONIAOINC')) exit('Request Error!');

/**
 * 视频模块API接口
 *
 * @version        $Id: video.class.php 2017-1-18 上午11:17:20 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
class video
{
    private $param;  //参数

    /**
     * 构造函数
     *
     * @param string $action 动作名
     */
    public function __construct($param = array())
    {
        $this->param = $param;
    }

    /**
     * 新闻基本参数
     * @return array
     */
    public function config()
    {

        require(HUONIAOINC . "/config/video.inc.php");

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

        //获取当前城市名
        global $siteCityInfo;
        if (is_array($siteCityInfo)) {
            $cityName = $siteCityInfo['name'];
        }

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

        // $domainInfo = getDomain('video', 'config');
        // $customChannelDomain = $domainInfo['domain'];
        // if($customSubDomain == 0){
        // 	$customChannelDomain = "http://".$customChannelDomain;
        // }elseif($customSubDomain == 1){
        // 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
        // }elseif($customSubDomain == 2){
        // 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
        // }

        // include HUONIAOINC.'/siteModuleDomain.inc.php';
        $customChannelDomain = getDomainFullUrl('video', $customSubDomain);

        //分站自定义配置
        $ser = 'video';
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
                    $return['channelName'] = str_replace('$city', $cityName, $customChannelName);
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
                    $return['title'] = str_replace('$city', $cityName, $customSeoTitle);
                } elseif ($param == "keywords") {
                    $return['keywords'] = str_replace('$city', $cityName, $customSeoKeyword);
                } elseif ($param == "description") {
                    $return['description'] = str_replace('$city', $cityName, $customSeoDescription);
                } elseif ($param == "hotline") {
                    $return['hotline'] = $hotline;
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

            $return['channelName']   = str_replace('$city', $cityName, $customChannelName);
            $return['logoUrl']       = $customLogoPath;
            $return['subDomain']     = $customSubDomain;
            $return['channelDomain'] = $customChannelDomain;
            $return['channelSwitch'] = $customChannelSwitch;
            $return['closeCause']    = $customCloseCause;
            $return['title']         = str_replace('$city', $cityName, $customSeoTitle);
            $return['keywords']      = str_replace('$city', $cityName, $customSeoKeyword);
            $return['description']   = str_replace('$city', $cityName, $customSeoDescription);
            $return['hotline']       = $hotline;
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
     * 视频分类
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
        $results = $dsql->getTypeList($type, "videotype", $son, $page, $pageSize);
        if ($results) {
            return $results;
        }
    }


    /**
     * 视频分类详细信息
     * @return array
     */
    public function typeDetail()
    {
        global $dsql;
        $typeDetail = array();
        $typeid     = $this->param;
        $archives   = $dsql->SetQuery("SELECT `id`, `typename`, `seotitle`, `keywords`, `description` FROM `#@__videotype` WHERE `id` = " . $typeid);
        $results    = $dsql->dsqlOper($archives, "results");
        if ($results && is_array($results)) {
            $param             = array(
                "service" => "video",
                "template" => "list",
                "typeid" => $typeid
            );
            $results[0]["url"] = getUrlPath($param);
            return $results;
        }
    }


    /**
     * 视频列表
     * @return array
     */
    public function alist()
    {
        global $dsql;
        global $userLogin;
        $pageinfo = $list = array();
        $typeid   = $title = $flag = $thumb = $orderby = $u = $state = $group_img = $page = $pageSize = $where = $where1 = "";

        if (!empty($this->param)) {
            if (!is_array($this->param)) {
                return array("state" => 200, "info" => '格式错误！');
            } else {
                $typeid    = $this->param['typeid'];
                $title     = $this->param['title'];
                $flag      = $this->param['flag'];
                $thumb     = $this->param['thumb'];
                $orderby   = $this->param['orderby'];
                $u         = $this->param['u'];
                $state     = $this->param['state'];
                $group_img = $this->param['group_img'];
                $page      = $this->param['page'];
                $pageSize  = $this->param['pageSize'];
                $lnglat    = $this->param['lnglat'];
                $tongcheng = $this->param['tongcheng'];
                $userid    = $this->param['userid'];
            }
        }

        $cityid = getCityId($this->param['cityid']);
        if ($cityid) {
            $where .= " AND `cityid` = " . $cityid;
        }

        //是否输出当前登录会员的信息
        if ($u != 1) {
            $where .= " AND l.`arcrank` = 1";
        } else {
            $uid   = $userLogin->getMemberID();
            $where .= " AND l.`admin` = " . $uid;

            if ($state != "") {
                $where1 = " AND l.`arcrank` = " . $state;
            }
        }

        //遍历分类
        if (!empty($typeid)) {
            if ($dsql->getTypeList($typeid, "videotype")) {
                global $arr_data;
                $arr_data = array();
                $lower    = arr_foreach($dsql->getTypeList($typeid, "videotype"));
                $lower    = $typeid . "," . join(',', $lower);
            } else {
                $lower = $typeid;
            }
            $where .= " AND `typeid` in ($lower)";
        }

        if ($userid) {
            $where .= " AND `admin` = $userid";
        }

        //模糊查询关键字
        if (!empty($title)) {

            //搜索记录
            siteSearchLog("video", $title);

            $title = explode(" ", $title);
            $w     = array();
            foreach ($title as $k => $v) {
                if (!empty($v)) {
                    $w[] = "`title` like '%" . $v . "%' OR `keywords` like '%" . $v . "%'";
                }
            }
            $where .= " AND (" . join(" OR ", $w) . ")";
        }

        //匹配自定义属性
        if (!empty($flag)) {
            $flag = explode(",", $flag);
            $w    = array();
            foreach ($flag as $k => $v) {
                $w[] = "`flag` like '%" . $v . "%'";
            }
            $where .= " AND (" . join(" AND ", $w) . ")";
        }


        //缩略图
        if ($thumb === "0") {
            $where .= " AND `litpic` = ''";
        } elseif ($thumb === "1") {
            $where .= " AND `litpic` != ''";
        }

        $order = " ORDER BY `weight` DESC, `id` DESC";
        //发布时间
        if ($orderby == "1") {
            $order = " ORDER BY `pubdate` DESC, `weight` DESC, `id` DESC";
            //浏览量
        } elseif ($orderby == "2") {
            $order = " ORDER BY `click` DESC, `weight` DESC, `id` DESC";
            //今日浏览量
        } elseif ($orderby == "2.1") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = curdate() ORDER BY `click` DESC, `weight` DESC, `id` DESC";
            //昨日浏览量
        } elseif ($orderby == "2.2") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY) ORDER BY `click` DESC, `weight` DESC, `id` DESC";
            //本周浏览量
        } elseif ($orderby == "2.3") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') >= DATE_SUB(curdate(), INTERVAL 7 DAY) ORDER BY `click` DESC, `weight` DESC, `id` DESC";
            //本月浏览量
        } elseif ($orderby == "2.4") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m') = DATE_FORMAT(curdate(), '%Y-%m') ORDER BY `click` DESC, `weight` DESC, `id` DESC";
            //随机
        } elseif ($orderby == "3") {
            $order = " ORDER BY rand()";
        } else if ($orderby == '4') {
            $order = "ORDER BY `distance` ASC ";
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives       = $dsql->SetQuery("SELECT `id` FROM `#@__videolist` l WHERE `del` = 0" . $where);
        $archives_count = $dsql->SetQuery("SELECT count(`id`) FROM `#@__videolist` l WHERE `del` = 0" . $where);

        //总条数
        $totalResults = $dsql->dsqlOper($archives_count, "results", "NUM");
        $totalCount   = (int)$totalResults[0][0];

        //总分页数
        $totalPage = ceil($totalCount / $pageSize);

        if ($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        //会员列表需要统计信息状态
        if ($u == 1 && $userLogin->getMemberID() > -1) {
            //待审核
            $totalGray = $dsql->dsqlOper($archives . " AND `arcrank` = 0", "totalCount");
            //已审核
            $totalAudit = $dsql->dsqlOper($archives . " AND `arcrank` = 1", "totalCount");
            //拒绝审核
            $totalRefuse = $dsql->dsqlOper($archives . " AND `arcrank` = 2", "totalCount");

            $pageinfo['gray']   = $totalGray;
            $pageinfo['audit']  = $totalAudit;
            $pageinfo['refuse'] = $totalRefuse;
        }

        if ($tongcheng) {
            $lnglat_  = explode(',', $lnglat);
            $lng_     = $lnglat_[0];
            $lat_     = $lnglat_[1];
            $archives = $dsql->SetQuery("SELECT `id`, `title`, `subtitle`, `typeid`, `flag`, `keywords`, `lng`,`lat`,`description`, `source`, `redirecturl`, `litpic`, `color`, `click`, l.`arcrank`, `pubdate`, `admin`,  (SELECT COUNT(`id`)  FROM `#@__public_comment` WHERE `type` = 'video-detail' AND `pid` = 0 AND `aid` = l.`id` AND `ischeck` = 1) AS total,
FORMAT((
    6371 * acos(
        cos(radians($lat_)) * cos(radians(lat)) * cos(
            radians(lng) - radians($lng_)
        ) + sin(radians($lat_)) * sin(radians(lat))
    )
),2) AS distance
FROM `#@__videolist` l WHERE `del` = 0" . $where);
            $where1   .= " HAVING distance < 30 ";
        } else {
            $archives = $dsql->SetQuery("SELECT `id`, `title`, `subtitle`, `typeid`, `flag`, `keywords`, `lng`,`lat`,`description`, `source`, `redirecturl`, `litpic`, `color`, `click`, l.`arcrank`, `pubdate`, `admin`,  (SELECT COUNT(`id`)  FROM `#@__public_comment` WHERE `type` = 'video-detail' AND `pid` = 0 AND `aid` = l.`id` AND `ischeck` = 1) AS total FROM `#@__videolist` l WHERE `del` = 0" . $where);
        }

        $atpage  = $pageSize * ($page - 1);
        $where   = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives . $where1 . $order . $where, "results");

        // echo $archives;

        if ($results) {
            global $cfg_clihost;
            foreach ($results as $key => $val) {
                $flag                   = explode(",", $val['flag']);
                $list[$key]['id']       = $val['id'];
                $list[$key]['title']    = in_array("b", $flag) ? '<strong>' . $val['title'] . '</strong>' : $val['title'];
                $list[$key]['subtitle'] = $val['subtitle'];
                $list[$key]['typeid']   = $val['typeid'];

                global $data;
                $data                   = "";
                $typeArr                = getParentArr("videotype", $val['typeid']);
                $typeArr                = array_reverse(parent_foreach($typeArr, "typename"));
                $list[$key]['typeName'] = $typeArr;

                $list[$key]['flag']        = $val['flag'];
                $list[$key]['keywords']    = $val['keywords'];
                $list[$key]['description'] = $val['description'];
                $list[$key]['source']      = $val['source'];
                $list[$key]['redirecturl'] = $val['redirecturl'];
                $list[$key]['litpic']      = !empty($val['litpic']) ? getFilePath($val['litpic']) : "";


                $list[$key]['color']    = $val['color'];
                $list[$key]['click']    = $val['click'];
                $list[$key]['videourl'] = $val['videourl'];
                $list[$key]['common']   = $val['total'];

                $list[$key]['is_user'] = 1;
                $list[$key]['user']      = getMemberDetail($val['admin']);
                if(count($list[$key]['user']) < 10){
                    $list[$key]['user'] = array('username' => '管理员');
                    $list[$key]['is_user'] = 0;
                }

                // $sql                    = $dsql->SetQuery("SELECT * FROM `#@__site_zanmap` WHERE `vid` = {$val['id']} AND `temp` = 'video' ");
                $sql                    = $dsql->SetQuery("SELECT * FROM `#@__public_up` WHERE `type` = '0' AND `tid` = {$val['id']} AND `module` = 'video' AND `action` = 'detail' ");
                $ret                    = $dsql->dsqlOper($sql, 'totalCount');
                $list[$key]['zanCount'] = $ret;


                //会员中心显示信息状态
                if ($u == 1 && $userLogin->getMemberID() > -1) {
                    $list[$key]['arcrank'] = $val['arcrank'];
                }

                $list[$key]['is_zan']    = 0;
                $list[$key]['is_follow'] = 0;

                $list[$key]['pubdate']  = $val['pubdate'];
                $list[$key]['pubdate1'] = date("H:i", $val['pubdate']);
                $list[$key]['pubdate2'] = date("m-d", $val['pubdate']);
                $user_id                = $userLogin->getMemberID();
                // $sql                    = $dsql->SetQuery("SELECT * FROM `#@__site_zanmap` WHERE `vid` = {$val['id']}  AND `userid` = $user_id  AND `temp` = 'video' ");
                $sql                    = $dsql->SetQuery("SELECT * FROM `#@__public_up` WHERE `type` = '0' AND `ruid` = $user_id AND `tid` = {$val['id']} AND `module` = 'video' AND `action` = 'detail' ");
                $ret                    = $dsql->dsqlOper($sql, 'totalCount');
                if ($ret) {
                    $list[$key]['is_zan'] = 1;
                }

                $sql = $dsql->SetQuery("SELECT * FROM `#@__site_followmap` WHERE `userid_b` = {$val['admin']}  AND `userid` = $user_id  AND `temp` = 'video' ");
                $ret = $dsql->dsqlOper($sql, 'totalCount');
                if ($ret) {
                    $list[$key]['is_follow'] = 1;
                }
                if ($lnglat) {

                    $list[$key]['distance_'] = $val['distance'];
                }

                $param                  = array(
                    "service" => "video",
                    "template" => "personal",
                    "id" => $val['admin'],
                );
                $list[$key]['user_url'] = getUrlPath($param);

                $param             = array(
                    "service" => "video",
                    "template" => "detail",
                    "id" => $val['id'],
                    "flag" => $val['flag'],
                    "redirecturl" => $val['redirecturl']
                );
                $list[$key]['url'] = getUrlPath($param);

            }
        }else{
            return array("state" => 200, "info" => '暂无数据！');
        }

        return array("pageInfo" => $pageinfo, "list" => $list);
    }


    /**
     * 视频信息详细
     * @return array
     */
    public function detail()
    {
        global $dsql;
        global $userLogin;
        $articleDetail = array();
        $id            = $this->param;
        $id            = is_numeric($id) ? $id : $id['id'];
        if (!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

        //判断是否管理员已经登录
        //功能点：管理员和信息的发布者可以查看所有状态的信息
        $where = "";
        if ($userLogin->getUserID() == -1) {

            $where = " AND `arcrank` = 1 AND `del` = 0";

            //如果没有登录再验证会员是否已经登录
            if ($userLogin->getMemberID() == -1) {
                $where = " AND `arcrank` = 1 AND `del` = 0";
            } else {
                $where = " AND (`arcrank` = 1 AND `del` = 0 OR `admin` = " . $userLogin->getMemberID() . ")";
            }

        }

        $archives = $dsql->SetQuery("SELECT * FROM `#@__videolist` WHERE `id` = " . $id . $where);
        $results  = $dsql->dsqlOper($archives, "results");
        if ($results) {

            $isWxMiniprogram = GetCookie('isWxMiniprogram');
            $is_android_app = isAndroidApp();

            global $cfg_clihost;
            $articleDetail["id"]           = $results[0]['id'];
            $articleDetail["title"]        = $results[0]['title'];
            $articleDetail["cityid"]       = $results[0]['cityid'];
            $articleDetail["subtitle"]     = $results[0]['subtitle'];
            $articleDetail["flag"]         = $results[0]['flag'];
            $articleDetail["admin"]        = $results[0]['admin'];
            $articleDetail["redirecturl"]  = $results[0]['redirecturl'];
            $articleDetail["litpic"]       = !empty($results[0]['litpic']) ? getFilePath($results[0]['litpic']) : "";
            $articleDetail["litpicSource"] = !empty($results[0]['litpic']) ? $results[0]['litpic'] : "";
            $articleDetail["source"]       = $results[0]['source'];
            $articleDetail["sourceurl"]    = $results[0]['sourceurl'];
            $articleDetail["writer"]       = $results[0]['writer'];
            $articleDetail["typeid"]       = $results[0]['typeid'];
            $videotype                     = $results[0]['videotype'];
            $videourl                      = $results[0]['videourl'];

            $user_ = getMemberDetail($results[0]['admin']);
            if (count($user_) > 5) {
                $articleDetail["user"] = $user_;
            }else{
                $articleDetail["user"] = 0;
            }

            if ($videotype) {
                if (stripos($videourl, '<iframe') !== false) {
                    $videourl = str_replace("<iframe", "", $videourl);
                    $videourl = str_replace("iframe>", "", $videourl);
                    $videourl = str_replace("</", "", $videourl);
                    $videourl = str_replace(">", "", $videourl);
                    $iframe   = explode(" ", $videourl);
                    foreach ($iframe as $k => $v) {
                        if (stripos($v, 'src') !== false) {
                            $videourl = str_replace("'", "", $v);
                            $videourl = str_replace('"', "", $videourl);
                            $videourl = str_replace("src=", "", $videourl);
                            break;
                        }
                    }
                }
                $videourl = stripslashes($videourl);

                //提取真实播放地址
                $articleDetail["realVideoUrl"] = getRealVideoUrl($videourl);
            } else {
                $videourl = getFilePath($videourl);
            }

            $isWxMiniprogram = GetCookie('isWxMiniprogram');
            if($articleDetail["realVideoUrl"] && ($isWxMiniprogram || $is_android_app)){
                $videourl = $articleDetail["realVideoUrl"];
                $videotype = 0;
            }
            if($videotype && pathinfo($videourl)['extension'] == 'mp4'){
                $videotype = 0;
            }
            $articleDetail["videotype"] = $videotype;
            $articleDetail["videourl"]  = $videourl;

            global $data;
            $data                      = "";
            $typeArr                   = getParentArr("videotype", $results[0]['typeid']);
            $typeArr                   = array_reverse(parent_foreach($typeArr, "typename"));
            $articleDetail['typeName'] = $typeArr;

            $param                    = array(
                "service" => "video",
                "template" => "list",
                "typeid" => $results[0]['typeid']
            );
            $articleDetail['typeUrl'] = getUrlPath($param);

            $articleDetail["keywords"]     = str_replace(",", " ", $results[0]['keywords']);
            $articleDetail["keywordsList"] = explode(" ", str_replace(",", " ", $results[0]['keywords']));
            $articleDetail["description"]  = $results[0]['description'];
            $articleDetail["click"]        = $results[0]['click'];
            $articleDetail["color"]        = $results[0]['color'];
            $articleDetail["arcrank"]      = $results[0]['arcrank'];
            $articleDetail["pubdate"]      = $results[0]['pubdate'];
            $user_id                       = $userLogin->getMemberID();
            //是否关注
            $articleDetail['is_follow'] = 0;
            $sql                        = $dsql->SetQuery("SELECT * FROM `#@__site_followmap` WHERE `userid_b` = {$results[0]['admin']}  AND `userid` = $user_id  AND `temp` = 'video' ");
            $ret                        = $dsql->dsqlOper($sql, 'totalCount');
            if ($ret) {
                $articleDetail['is_follow'] = 1;
            }
            //是否点赞
            $articleDetail['is_zan'] = 0;
            // $sql                     = $dsql->SetQuery("SELECT * FROM `#@__site_zanmap` WHERE `vid` = {$results[0]['id']}  AND `userid` = $user_id  AND `temp` = 'video' ");
            $sql                    = $dsql->SetQuery("SELECT * FROM `#@__public_up` WHERE `type` = '0' AND `ruid` = $user_id AND `tid` = {$results[0]['id']} AND `module` = 'video' AND `action` = 'detail' ");
            $ret                     = $dsql->dsqlOper($sql, 'totalCount');
            if ($ret) {
                $articleDetail['is_zan'] = 1;
            }

            //视频评论
            $archives   = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'video-detail' AND `aid` = {$results[0]['id']} AND `pid` = 0");
			$totalCount = $dsql->dsqlOper($archives, "totalCount");
            $articleDetail['common'] = $totalCount;

            /* $sql = $dsql->SetQuery("SELECT * FROM `#@__videocommon` WHERE `aid` = {$results[0]['id']} AND `ischeck` = 1 AND `floor` = 0");
            $ret = $dsql->dsqlOper($sql, 'results');
            foreach ($ret as $key => $item) {
                $ret[$key]['user'] = getMemberDetail($item['userid']);
                //$item['id'] 评论id
                $sql_comm_zan          = $dsql->SetQuery("SELECT * FROM `#@__site_zanmap` WHERE `vid` = {$item['id']} AND `temp` = 'video_common'");
                $zan_comm_ret          = $dsql->dsqlOper($sql_comm_zan, 'totalCount');
                $ret[$key]['zan_comm'] = $zan_comm_ret;

                //查看自己是否点过赞（评论）
                $sql_comm_zan_is          = $dsql->SetQuery("SELECT * FROM `#@__site_zanmap` WHERE `vid` = {$item['id']} AND `temp` = 'video_common' AND `userid` = $user_id");
                $is_zan_comm              = $dsql->dsqlOper($sql_comm_zan_is, 'totalCount');
                $ret[$key]['is_zan_comm'] = $is_zan_comm;

                //回复
                $sql_ = $dsql->SetQuery("SELECT * FROM `#@__videocommon` WHERE `floor` = {$item['id']} AND `ischeck` = 1 AND `aid` = {$results[0]['id']}");
                $ret_ = $dsql->dsqlOper($sql_, 'results');
                foreach ($ret_ as $key_ => $value_) {
                    $ret_[$key_]['user'] = getMemberDetail($value_['userid']);
                }
                $ret[$key]['floor_common'] = $ret_;
            }

            $articleDetail['common_list'] = $ret; */

            //视频总数
            $sql                        = $dsql->SetQuery("SELECT * FROM `#@__videolist` WHERE `admin` = {$results[0]['admin']}");
            $ret                        = $dsql->dsqlOper($sql, 'totalCount');
            $articleDetail['video_num'] = $ret;
            //粉丝数
            $sql                         = $dsql->SetQuery("SELECT * FROM `#@__site_followmap` WHERE `userid_b` = {$results[0]['admin']} AND `temp` = 'video' ");
            $ret                         = $dsql->dsqlOper($sql, 'totalCount');
            $articleDetail['follow_num'] = $ret;
            $param                       = array(
                "service" => "video",
                "template" => "detail",
                "id" => $id
            );
            $articleDetail['url']        = getUrlPath($param);

        }
        return $articleDetail;
    }


    /**
     * 获取下一条信息内容
     * @return array
     */
    public function nextData()
    {
        global $dsql;
        global $userLogin;
        $articleDetail = array();
        $id            = $this->param['id'];
        if (!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

        $sql = $dsql->SetQuery("select `id` from `#@__videolist` where `id` = (select max(`id`) from `#@__videolist` where `id` < $id)");
        $ret = $dsql->dsqlOper($sql, "results");
        if ($ret) {
            $this->param = $ret[0]['id'];
            $data        = $this->detail();
            return $data;
        }

    }

    /**
     * 评论列表
     * @return array
     */
    public function common()
    {
        global $dsql;
        global $userLogin;
        $pageinfo = $list = array();
        $newsid   = $orderby = $page = $pageSize = $where = "";

        if (!is_array($this->param)) {
            return array("state" => 200, "info" => '格式错误！');
        } else {
            $newsid   = $this->param['newsid'];
            $orderby  = $this->param['orderby'];
            $page     = $this->param['page'];
            $pageSize = $this->param['pageSize'];
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $oby = " ORDER BY `id` DESC";
        if ($orderby == "hot") {
            $oby = " ORDER BY `good` DESC, `id` DESC";
        }

        $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__videocommon` WHERE `aid` = " . $newsid . " AND `ischeck` = 1 AND `floor` = 0" . $oby);
        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount / $pageSize);

        if ($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $atpage = $pageSize * ($page - 1);
        $where  = " LIMIT $atpage, $pageSize";

        $results = $dsql->dsqlOper($archives . $where, "results");
        if ($results) {
            foreach ($results as $key => $val) {
                $list[$key]['id']       = $val['id'];
                $list[$key]['userinfo'] = $userLogin->getMemberInfo($val['userid']);
                $list[$key]['content']  = $val['content'];
                $list[$key]['dtime']    = $val['dtime'];
                $list[$key]['ftime']    = floor((GetMkTime(time()) - $val['dtime'] / 86400) % 30) > 30 ? date("Y-m-d", $val['dtime']) : FloorTime(GetMkTime(time()) - $val['dtime']);
                $list[$key]['ip']       = $val['ip'];
                $list[$key]['ipaddr']   = $val['ipaddr'];
                $list[$key]['good']     = $val['good'];
                $list[$key]['bad']      = $val['bad'];

                $userArr               = explode(",", $val['duser']);
                $list[$key]['already'] = in_array($userLogin->getMemberID(), $userArr) ? 1 : 0;

                $list[$key]['lower'] = $this->getCommonList($val['id']);
            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);
    }


    /**
     * 遍历评论子级
     * @param $fid int 评论ID
     * @return array
     */
    function getCommonList($fid)
    {
        if (empty($fid)) return false;
        global $dsql;
        global $userLogin;

        $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__videocommon` WHERE `floor` = " . $fid . " AND `ischeck` = 1 ORDER BY `id` DESC");
        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");

        if ($totalCount > 0) {
            $results = $dsql->dsqlOper($archives, "results");
            if ($results) {
                foreach ($results as $key => $val) {
                    $list[$key]['id']       = $val['id'];
                    $list[$key]['userinfo'] = $userLogin->getMemberInfo($val['userid']);
                    $list[$key]['content']  = $val['content'];
                    $list[$key]['dtime']    = $val['dtime'];
                    $list[$key]['ftime']    = floor((GetMkTime(time()) - $val['dtime'] / 86400) % 30) > 30 ? $val['dtime'] : FloorTime(GetMkTime(time()) - $val['dtime']);
                    $list[$key]['ip']       = $val['ip'];
                    $list[$key]['ipaddr']   = $val['ipaddr'];
                    $list[$key]['good']     = $val['good'];
                    $list[$key]['bad']      = $val['bad'];

                    $userArr               = explode(",", $val['duser']);
                    $list[$key]['already'] = in_array($userLogin->getMemberID(), $userArr) ? 1 : 0;

                    $list[$key]['lower'] = $this->getCommonList($val['id']);
                }
                return $list;
            }
        }
    }


    /**
     * 顶评论
     * @param $id int 评论ID
     * @param string
     **/
    public function dingCommon()
    {
        global $dsql;
        global $userLogin;

        $id = $this->param['id'];
        if (empty($id)) return "请传递评论ID！";
        $memberID = $userLogin->getMemberID();
        if ($memberID == -1 || empty($memberID)) return "请先登录！";

        $archives = $dsql->SetQuery("SELECT `duser` FROM `#@__videocommon` WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archives, "results");
        if ($results) {

            $duser = $results[0]['duser'];

            //如果此会员已经顶过则return
            $userArr = explode(",", $duser);
            if (in_array($userLogin->getMemberID(), $userArr)) return "已顶过！";

            //附加会员ID
            if (empty($duser)) {
                $nuser = $userLogin->getMemberID();
            } else {
                $nuser = $duser . "," . $userLogin->getMemberID();
            }

            $archives = $dsql->SetQuery("UPDATE `#@__videocommon` SET `good` = `good` + 1 WHERE `id` = " . $id);
            $results  = $dsql->dsqlOper($archives, "update");

            $archives = $dsql->SetQuery("UPDATE `#@__videocommon` SET `duser` = '$nuser' WHERE `id` = " . $id);
            $results  = $dsql->dsqlOper($archives, "update");
            return $results;

        } else {
            return "评论不存在或已删除！";
        }
    }


    /**
     * 发表评论
     * @return array
     */
    public function sendCommon()
    {
        global $dsql;
        global $userLogin;
        $param = $this->param;
        if ($userLogin->getMemberID() == -1) {
            return array("state" => 200, 'info' => '请先登录！');
        }
        $aid     = $param['aid'];
        $id      = $param['id'];
        $content = addslashes($param['content']);

        if (empty($aid) || empty($content)) {
            return array("state" => 200, "info" => '必填项不得为空！');
        }

        $content = filterSensitiveWords(cn_substrR($content, 250));

        include HUONIAOINC . "/config/video.inc.php";
        $ischeck = (int)$customCommentCheck;

        $archives = $dsql->SetQuery("INSERT INTO `#@__videocommon` (`aid`, `floor`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `ischeck`, `duser`) VALUES ('$aid', '$id', '" . $userLogin->getMemberID() . "', '$content', " . GetMkTime(time()) . ", '" . GetIP() . "', '" . getIpAddr(GetIP()) . "', 0, 0, '$ischeck', '')");
        $lid      = $dsql->dsqlOper($archives, "lastid");
        if ($lid) {
            $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__videocommon` WHERE `id` = " . $lid);
            $results  = $dsql->dsqlOper($archives, "results");
            if ($results) {
                $list['id']       = $results[0]['id'];
                $list['userinfo'] = $userLogin->getMemberInfo($results[0]['userid']);
                $list['content']  = $results[0]['content'];
                $list['dtime']    = $results[0]['dtime'];
                $list['ftime']    = GetMkTime(time()) - $results[0]['dtime'] > 30 ? $results[0]['dtime'] : FloorTime(GetMkTime(time()) - $results[0]['dtime']);
                $list['ip']       = $results[0]['ip'];
                $list['ipaddr']   = $results[0]['ipaddr'];
                $list['good']     = $results[0]['good'];
                $list['bad']      = $results[0]['bad'];
                return $list;
            }
        } else {
            return array("state" => 200, "info" => '评论失败！');
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

        return array('state' => 100, 'info' => $ret);
    }

    /**
     * 关注
     * @return array
     */
    public function follow()
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

        //查询发布视频的用户
        if ($temp == 'video' || $temp == 'video_common') {
            if ($vid) {
                $sql      = $dsql->SetQuery("SELECT `admin` FROM `#@__videolist` WHERE `id` = $vid");
                $ret      = $dsql->dsqlOper($sql, "results");
                $userid_b = $ret[0]['admin'];
            } else {
                $userid_b = $param['userid'];
            }

        } elseif ($temp == 'quanjing') {
            $sql      = $dsql->SetQuery("SELECT `admin` FROM `#@__quanjinglist` WHERE `id` = $vid");
            $ret      = $dsql->dsqlOper($sql, "results");
            $userid_b = $ret[0]['admin'];
        }elseif ($temp == 'info'){
            $userid_b = $vid;
            if($vid == $userid){
                return array('state' => 200, 'info' => '您不可以关注自己！');
            }
        }

        if ($type) {
            //查看是否已经关注
            $sql = $dsql->SetQuery("SELECT * FROM `#@__site_followmap` WHERE `userid` = $userid AND `userid_b` = $userid_b AND `temp` = '$temp'");
            $ret = $dsql->dsqlOper($sql, "results");
            if ($ret) {
                return array('state' => 200, 'info' => '您已关注！');
            }
            $date = time();
            $sql  = $dsql->SetQuery("INSERT INTO `#@__site_followmap` (`userid`, `userid_b`, `temp`, `date`) VALUES ($userid , $userid_b, '$temp', $date)");
            $ret  = $dsql->dsqlOper($sql, "update");
        } else {
            //取关
            $sql = $dsql->SetQuery("DELETE FROM `#@__site_followmap` WHERE `userid` = $userid AND `userid_b` = $userid_b AND `temp` = '$temp'");
            $ret = $dsql->dsqlOper($sql, "update");
        }
        return array('state' => 100, 'info' => $ret);

    }

}
