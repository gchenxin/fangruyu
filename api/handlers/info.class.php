<?php if (!defined('HUONIAOINC')) exit('Request Error!');

/**
 * 信息模块API接口
 *
 * @version        $Id: info.class.php 2014-3-24 下午14:51:14 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
class info
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
     * 信息基本参数
     * @return array
     */
    public function config()
    {

        require(HUONIAOINC . "/config/info.inc.php");

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
        // global $customHotline;            //咨询热线
        // global $customAtlasMax;           //图集数量限制
        // global $customTemplate;           //模板风格

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

        // $domainInfo = getDomain('info', 'config');
        // $customChannelDomain = $domainInfo['domain'];
        // if($customSubDomain == 0){
        // 	$customChannelDomain = "http://".$customChannelDomain;
        // }elseif($customSubDomain == 1){
        // 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
        // }elseif($customSubDomain == 2){
        // 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
        // }

        // include HUONIAOINC.'/siteModuleDomain.inc.php';
        $customChannelDomain = getDomainFullUrl('info', $customSubDomain);

        //分站自定义配置
        $ser = 'info';
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
                        $customLogo = getFilePath($customLogoUrl);
                    } else {
                        $customLogo = getFilePath($cfg_weblogo);
                    }

                    $return['logoUrl'] = $customLogo;
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
                }
            }

        } else {

            //自定义LOGO
            if ($customLogo == 1) {
                $customLogo = getFilePath($customLogoUrl);
            } else {
                $customLogo = getFilePath($cfg_weblogo);
            }

            $return['channelName']   = str_replace('$city', $cityName, $customChannelName);
            $return['logoUrl']       = $customLogo;
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
        }

        return $return;

    }

    /**
     * 商圈
     * @return array
     */
    public function circle(){
        global $dsql;
        global $langData;
        $type = $page = $pageSize = $where = "";

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => $langData['info'][1][58]);//格式错误！
            }else{
                $type     = (int)$this->param['type'];
                $page     = (int)$this->param['page'];
                $pageSize = (int)$this->param['pageSize'];
                $son      = $this->param['son'] == 0 ? false : true;
            }
        }

        if(empty($type)) return array("state" => 200, "info" => $langData['info'][1][58]);

        $page = (int)$page;
        $pageSize = (int)$pageSize;

        $page = empty($page) ? 1 : $page;
        $pageSize = empty($pageSize) ? 1000 : $pageSize;
        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";

        $sql = $dsql->SetQuery("SELECT * FROM `#@__site_city_circle` WHERE `qid` = $type".$where);
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            return $ret;
        }
    }


    /**
     * 配置商铺
     * @return array
     */
    public function storeConfig()
    {
        global $dsql;
        global $userLogin;
        global $langData;

        $userid    = $userLogin->getMemberID();
        $param     = $this->param;
        $stype     = (int)$param['stype'];
        $addrid    = (int)$param['addrid'];
        $cityid    = (int)$param['cityid'];
        $circle    = $param['circle'];
        $circle    = isset($circle) ? join(',', $circle) : '';
        $subway    = $param['subway'];
        $subway    = isset($subway) ? join(',', $subway) : '';
        $address   = filterSensitiveWords(addslashes($param['address']));
        $lnglat    = filterSensitiveWords(addslashes($param['lnglat']));
        $phone     = filterSensitiveWords(addslashes($param['phone']));
        $openStart = filterSensitiveWords(addslashes($param['openStart']));
        $openEnd   = filterSensitiveWords(addslashes($param['openEnd']));
        $note      = filterSensitiveWords(addslashes($param['note']));
        $body      = filterSensitiveWords(addslashes($param['body']));
        $pubdate   = GetMkTime(time());
        $video     = $param['video'];
        $tel       = $param['tel'];
        $imglist   = $param['imglist'];
        $wechat_pic   = $param['wechat_pic'];
        if (empty($cityid)) {
            $cityInfoArr = getPublicParentInfo(array('tab' => 'site_area', 'id' => $addrid));
            $cityInfoArr = explode(',', $cityInfoArr);
            $cityid      = $cityInfoArr[0];
        }

        if ($userid <= 0 || $userid == '') {
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
        }

        //验证会员类型
        $userDetail = $userLogin->getMemberInfo();
        if ($userDetail['userType'] != 2) {
            return array("state" => 200, "info" => $langData['info'][1][59]);//账号验证错误，操作失败！
        }

        //权限验证
        if (!verifyModuleAuth(array("module" => "info"))) {
            return array("state" => 200, "info" => $langData['info'][1][60]);//商家权限验证失败！
        }

        if (empty($stype)) {
            return array("state" => 200, "info" => $langData['info'][1][61]);//请选择所属类别
        }

        if (empty($addrid)) {
            return array("state" => 200, "info" => $langData['info'][1][62]);//请选择所在区域
        }

        if (empty($circle)) {
            //return array("state" => 200, "info" => $langData['info'][1][63]);//请选择所在商圈
        }

        if (empty($address)) {
            return array("state" => 200, "info" => $langData['info'][1][64]);//请输入详细地址
        }

        if (empty($phone)) {
            return array("state" => 200, "info" => $langData['info'][1][65]);//请输入联系电话
        }

        if (empty($openStart) || empty($openEnd)) {
            return array("state" => 200, "info" => $langData['info'][1][66]);//请选择营业时间
        }

        if (empty($imglist)) {
            return array("state" => 200, "info" => $langData['info'][1][67]);//请上传图集
        }

        $openStart = str_replace(":", "", $openStart);
        $openEnd   = str_replace(":", "", $openEnd);

        if (empty($note)) {
            //return array("state" => 200, "info" => $langData['info'][1][68]);//请输入简介
        }
        $note = cn_substrR($note, 200);

        if (empty($body)) {
            // return array("state" => 200, "info" => $langData['info'][1][69]);//请输入详细介绍
        }
        if (!empty($lnglat)) {
            $lnglatArr = explode(",", $lnglat);
            $lng       = $lnglatArr[0];
            $lat       = $lnglatArr[1];
        }

        $userSql    = $dsql->SetQuery("SELECT `id` FROM `#@__infoshop` WHERE `uid` = " . $userid);
        $userResult = $dsql->dsqlOper($userSql, "results");


        //新商铺
        if (!$userResult) {

            //保存到主表

            $archives = $dsql->SetQuery("INSERT INTO `#@__infoshop` (`uid`, `stype`, `addrid`, `address`, `circle`, `subway`, `lnglat`, `tel`,
    `openStart`, `openEnd`, `note`, `body`, `jointime`, `click`, `weight`, `state`, `cityid`,  `video`, `video_pic`, `pic`, `phone`, `wechat_pic` )
    VALUES ('$userid', '$stype', '$addrid', '$address', '$circle', '$subway', '$lnglat', '$phone', '$openStart', '$openEnd', '$note',
    '$body', '" . GetMkTime(time()) . "', '1', '1', '0', '$cityid',  '$video', '', '$imglist', '$tel', '$wechat_pic')");
            $aid      = $dsql->dsqlOper($archives, "lastid");

            if (is_numeric($aid)) {
                // 更新店铺开关
                updateStoreSwitch("info", "infoshop", $userid, $aid);
                //后台消息通知
                updateAdminNotice("info", "shop");

                return $langData['info'][1][70];//配置成功，您的商铺正在审核中，请耐心等待！
            } else {
                return array("state" => 200, "info" => $langData['info'][1][71]);//配置失败，请查检您输入的信息是否符合要求！
            }
            //更新商铺信息
        } else {
            //保存到主表
            $archives = $dsql->SetQuery("UPDATE `#@__infoshop` SET `cityid` = '$cityid', `stype` = '$stype', `subway` = '$subway', `addrid` = '$addrid', `address` = '$address', `circle` = '$circle', `lnglat` = '$lnglat', `tel` = '$phone',
`openStart` = '$openStart', `openEnd` = '$openEnd', `note` = '$note', `body` = '$body',  `video` = '$video', `pic` = '$imglist', `phone` = '$tel' , `video_pic` = '', `wechat_pic` = '$wechat_pic' WHERE `uid` = " . $userid);
            $results  = $dsql->dsqlOper($archives, "update");
            if ($results == "ok") {
                // 清除店铺详情缓存
                clearCache("info_shop_detail", $userResult[0]['id']);
                return $langData['info'][1][72];//保存成功！
            } else {
                return array("state" => 200, "info" => $langData['info'][1][71]);
            }

        }


    }


    /**
     * 信息分类
     * @return array
     */
    public function type()
    {
        global $dsql;
        global $langData;
        $type = $page = $pageSize = $where = "";

        if (!empty($this->param)) {
            if (!is_array($this->param)) {
                return array("state" => 200, "info" => $langData['info'][1][58]);//格式错误！
            } else {
                $type     = (int)$this->param['type'];
                $page     = (int)$this->param['page'];
                $pageSize = (int)$this->param['pageSize'];
                $son      = $this->param['son'] == 0 ? false : true;
            }
        }

        $results = $dsql->getTypeList($type, "infotype", $son, $page, $pageSize);
        if ($results) {
            return $results;
        }
    }


    /**
     * 分类模糊匹配
     * @return array
     */
    public function searchType()
    {
        global $dsql;
        $key = trim($this->param['key']);

        $list = array();
        if (!empty($key)) {
            $archives = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__infotype` WHERE (`typename` like '%" . $key . "%' OR `seotitle` like '%" . $key . "%' OR `keywords` like '%" . $key . "%' OR `description` like '%" . $key . "%') AND `parentid` != 0 LIMIT 0,10");
            $results  = $dsql->dsqlOper($archives, "results");
            if ($results) {
                foreach ($results as $key => $value) {

                    $list[$key]['id'] = $value['id'];
                    global $data;
                    $data                   = "";
                    $typeArr                = getParentArr("infotype", $value['id']);
                    $typeArr                = array_reverse(parent_foreach($typeArr, "typename"));
                    $list[$key]['typename'] = join(" > ", $typeArr);

                }
            }
        }

        return $list;
    }


    /**
     * 信息分类详细信息
     * @return array
     */
    public function typeDetail()
    {
        global $dsql;
        global $langData;
        $id = $this->param;

        $id = !is_numeric($id) ? $id['id'] : $id;

        if (empty($id)) return array("state" => 200, "info" => $langData['info'][1][58]);

        $archives = $dsql->SetQuery("SELECT `id`, `typename`, `seotitle`, `keywords`, `description` FROM `#@__infotype` WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archives, "results");
        if ($results) {
            $archives = $dsql->SetQuery("SELECT `id`, `field`, `title`, `formtype`, `required`, `options`, `default` FROM `#@__infotypeitem` WHERE `tid` = " . $id . " ORDER BY `orderby` DESC");
            $typeitem = $dsql->dsqlOper($archives, "results");
            if ($typeitem) {
                foreach ($typeitem as $key => $item) {
                    $results[0]["item"][$key]['id']       = $item['id'];
                    $results[0]["item"][$key]['field']    = $item['field'];
                    $results[0]["item"][$key]['title']    = $item['title'];
                    $results[0]["item"][$key]['formtype'] = $item['formtype'];
                    $results[0]["item"][$key]['required'] = $item['required'];
                    if ($item["options"] != "") {
                        $options                             = join('|', preg_split("[\r\n]", $item["options"]));
                        $results[0]["item"][$key]['options'] = explode("\r\n", $item["options"]);
                    }
                    $results[0]["item"][$key]['default'] = explode("|", $item['default']);
                }
            }

            $param             = array(
                "service" => "info",
                "template" => "list",
                "typeid" => $id
            );
            $results[0]["url"] = getUrlPath($param);

            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__infotype` WHERE `parentid` = " . $id);
            $res = $dsql->dsqlOper($sql, "totalCount");
            if ($res > 0) {
                $results[0]["lower"] = $res;
            }

            return $results;
        }

    }


    /**
     * 信息地区
     * @return array
     */
    public function addr()
    {
        global $dsql;
        global $langData;
        $type = $page = $pageSize = $where = "";

        if (!empty($this->param)) {
            if (!is_array($this->param)) {
                return array("state" => 200, "info" => $langData['info'][1][58]);
            } else {
                $type     = (int)$this->param['type'];
                $page     = (int)$this->param['page'];
                $pageSize = (int)$this->param['pageSize'];
                $son      = $this->param['son'] == 0 ? false : true;
            }
        }

        global $template;
        if ($template && $template != 'page' && empty($type)) {
            $type = getCityId();
        }

        //一级
        if (empty($type)) {
            //可操作的城市，多个以,分隔
            $userLogin    = new userLogin($dbo);
            $adminCityIds = $userLogin->getAdminCityIds();
            $adminCityIds = empty($adminCityIds) ? 0 : $adminCityIds;

            $cityArr = array();
            $sql     = $dsql->SetQuery("SELECT c.*, a.`id` cid, a.`typename`, a.`pinyin` FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` WHERE c.`cid` in ($adminCityIds) ORDER BY c.`id`");
            $result  = $dsql->dsqlOper($sql, "results");
            if ($result) {
                foreach ($result as $key => $value) {
                    array_push($cityArr, array(
                        "id" => $value['cid'],
                        "typename" => $value['typename'],
                        "pinyin" => $value['pinyin'],
                        "hot" => $value['hot'],
                        "lower" => array()
                    ));
                }
            }
            return $cityArr;

        } else {
            $results = $dsql->getTypeList($type, "site_area", $son, $page, $pageSize, '', '', true);
            if ($results) {
                return $results;
            }
        }
    }


    /**
     * 信息列表
     * @return array
     */
    public function ilist_v2()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = $itemList = array();
        $nature   = $typeid = $addrid = $valid = $title = $rec = $fire = $top = $thumb = $orderby = $u = $state = $uid = $userid = $tel = $notbid = $page = $pageSize = $where = $where1 = "";
        if (!empty($this->param)) {
            if (!is_array($this->param)) {
                return array("state" => 200, "info" => $langData['info'][1][58]);
            } else {
                $nature   = $this->param['nature'];
                $memberType   = $this->param['memberType'];
                $typeid   = $this->param['typeid'];
                $addrid   = $this->param['addrid'];
                $valid    = $this->param['valid'];
                $title    = $this->param['title'];
                $itemList = $_REQUEST['item'];
                $rec      = $this->param['rec'];
                $fire     = $this->param['fire'];
                $top      = $this->param['top'];
                $thumb    = $this->param['thumb'];
                $video    = $this->param['video'];
                $orderby  = $this->param['orderby'];
                $u        = $this->param['u'];
                $state    = $this->param['state'];
                $uid      = $this->param['uid'];
                $userid   = $this->param['userid'];
                $tel      = $this->param['tel'];
                $notbid   = $this->param['notbid'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
                $shopid   = $this->param['shopid'];
                $lng      = $this->param['lng'];
                $lat      = $this->param['lat'];
                $flag     = $this->param['flag'];
                $price_section     = $this->param['price_section'];
            }
        }
        $now = strtotime(date("Y-m-d"));

        if ($shopid) {
            $sql       = "SELECT `uid` FROM `#@__infoshop` WHERE `id` = $shopid";
            $sql       = $dsql->SetQuery($sql);
            $shop_user = $dsql->dsqlOper($sql, "results");
            $uid       = $shop_user[0]['uid'];
        }

        //指定会员
        if (!empty($userid)) {
            $where .= " AND `userid` = $userid";
        }

        //指定会员
        if (!empty($uid)) {
            $where .= " AND `userid` = $uid";
        }

        $cityid = getCityId($this->param['cityid']);
        if ($cityid && !$userid && !$uid && $u != 1) {
            $where .= " AND `cityid` = " . $cityid;
        } else {
            $where .= " AND `cityid` != 0";
        }

        //是否输出当前登录会员的信息
        if ($u != 1) {
            $where .= " AND l.`arcrank` = 1 AND l.`waitpay` = 0 AND `is_valid` = 0";
        } else {
            $uid      = $userLogin->getMemberID();
            $userinfo = $userLogin->getMemberInfo();

            if ($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "info"))) {
                return array("state" => 200, "info" => '商家权限验证失败！');
            }

            $where .= " AND l.`userid` = " . $uid;

            if ($state != "") {
                if ($state == 4) {
                    // $now    = GetMkTime(time());
                    $where1 = " AND (`valid` < " . $now . " OR `valid` = 0)";
                } else {
                    $where1 = " AND l.`arcrank` = " . $state;
                }
            }
        }

        //推荐
        if (!empty($rec)) {
            $where .= " AND `rec` = 1";
        }

        //火急
        if (!empty($fire)) {
            $where .= " AND `fire` = 1";
        }

        //置顶
        if (!empty($top)) {
//            $where .= " AND `top` = 1";
        }

        //指定电话号码
        if (!empty($tel)) {
            $where .= " AND `tel` = '$tel'";
        }

        if (!empty($video)) {
            $where .= " AND `video` != '' ";
        }

        //只查找不过期的信息
        if ($u != 1) {
            // $now   = GetMkTime(time());
            // $where .= " AND `valid` > " . $now . " AND `valid` <> 0";
            $where .= " AND `valid` > " . $now;
        }

        //信息性质
        if (!empty($nature)) {
            $sql   = $dsql->SetQuery("SELECT `uid` FROM `#@__infoshop` WHERE `cityid` = '$cityid' AND `state` = 1");
            $resID = $dsql->dsqlOper($sql, "results");
            $idArr = array();
            foreach($resID as $v){
                array_push($idArr, $v['uid']);
            }
            $idArr = !empty($idArr) ? join(',',  $idArr) : '';
            //个人
            if ($nature == 1) {
                // $where .= " AND ((SELECT `mtype` FROM `#@__member` WHERE `id` = l.`userid`) = 1 OR l.`userid` = -1)";
                $where .= " AND `userid` not in ($idArr)";
                //商家
            } elseif ($nature == 2) {
                // $where .= " AND ((SELECT `mtype` FROM `#@__member` WHERE `id` = l.`userid`) = 2)";
                $where .= " AND `userid` in ($idArr)";
            }

        }

        //遍历分类
        if (!empty($typeid)) {
            $typeArr = $dsql->getTypeList($typeid, "infotype");
            if ($typeArr) {
                global $arr_data;
                $arr_data = array();
                $lower    = arr_foreach($typeArr);
                $lower    = $typeid . "," . join(',', $lower);
            } else {
                $lower = $typeid;
            }
            $where .= " AND `typeid` in ($lower)";
        }

        //遍历地区
        if (!empty($addrid)) {
            if ($dsql->getTypeList($addrid, "site_area")) {
                global $arr_data;
                $arr_data = array();
                $lower    = arr_foreach($dsql->getTypeList($addrid, "site_area"));
                $lower    = $addrid . "," . join(',', $lower);
            } else {
                $lower = $addrid;
            }
            $where .= " AND `addr` in ($lower)";
        }

        if (!empty($title)) {

            //搜索记录
            siteSearchLog("info", $title);

            $where .= " AND (`title` like '%" . $title . "%' OR `tel` like '%" . $title . "%')";
        }

        //取出字段表中满足条件的所有信息ID Start
        $aidArr = $infoidArr = $aid = array();

        $tj = true;

        if (!empty($itemList)) {
            $itemList = json_decode($itemList, true);
            if(is_array($itemList)){
                foreach ($itemList as $k => $v) {
                    if (!empty($v['value'])) {
                        $archives = $dsql->SetQuery("SELECT `aid` FROM `#@__infoitem` WHERE `iid` = " . $v['id'] . " AND find_in_set('" . $v['value'] . "', `value`)");
                        $results  = $dsql->dsqlOper($archives, "results");
                        if ($results) {
                            foreach ($results as $key => $val) {
                                $infoidArr[$k][$key] = $val['aid'];
                            }
                        } else {
                            $tj = false;
                        }
                    }
                }
            }else{
                $tj = false;
            }
        }

        if (!$tj) $infoidArr = array();

        //二维数组转一维
        if (!empty($infoidArr)) {
            foreach ($infoidArr as $id) {
                $aid[] = join(",", $id);
            }
        }

        $aid = join(",", $aid);
        $aid = explode(",", $aid);

        //取出重复次数最多的信息ID
        $aidcount = array_count_values($aid);
        foreach ($aidcount as $key => $val) {
            if ($val == count($infoidArr)) {
                $aidArr[] = $key;
            }
        }

        $aidArr = join(",", $aidArr);
        //取出字段表中满足条件的所有信息ID End
        if (!empty($itemList) && empty($infoidArr)) {
            $where .= " AND 1 = 2";
        } else {
            if (!empty($aidArr)) {
                $where .= " AND `id` in ($aidArr)";
            }
        }

        //有图
        if (!empty($thumb)) {
            $where .= " AND (SELECT COUNT(`id`) FROM `#@__infopic` WHERE `aid` = l.`id`) > 0";
        }
        //价格筛选
        if($price_section){
            $price_section = explode(",", $price_section);
            if(!empty($price_section)){
                $price_section_1 = $price_section[0];
                $price_section_2 = $price_section[1];
                $where .= " AND `price` BETWEEN $price_section_1 AND $price_section_2 ";
            }
        }
//        $where .= " AND is_valid = 0 ";

        $order = " ORDER BY `isbid` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
        //价格
        if ($orderby == "price") {
            $order = " ORDER BY `isbid` DESC, `price` ASC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
            //发布时间
        } elseif ($orderby == "1") {
            if($notbid){
                $order = " ORDER BY `pubdate` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
            }else{
                $order = " ORDER BY `isbid` DESC, `pubdate` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
            }
            //浏览量
        } elseif ($orderby == "2") {
            $order = " ORDER BY `isbid` DESC, `click` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
            //今日浏览量
        } elseif ($orderby == "2.1") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = curdate() ORDER BY `isbid` DESC, `click` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
            //昨日浏览量
        } elseif ($orderby == "2.2") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY) ORDER BY `isbid` DESC, `click` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
            //本周浏览量
        } elseif ($orderby == "2.3") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') >= DATE_SUB(curdate(), INTERVAL 7 DAY) ORDER BY `isbid` DESC, `click` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
            //本月浏览量
        } elseif ($orderby == "2.4") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m') = DATE_FORMAT(curdate(), '%Y-%m') ORDER BY `isbid` DESC, `click` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
            //随机
        } elseif ($orderby == "3") {
            $order = " ORDER BY rand()";
        }else if($orderby == "5"){
            //按价格
            $order = " ORDER BY `price` DESC ";
        }else if($orderby == "5.1"){
            //按价格
            $order = " ORDER BY `price` ASC ";
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        //查询距离
        $select = "";
		if((!empty($lng))&&(!empty($lat))){
            $select="(2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*(".$lng."-l.`longitude`)/360),2)+COS(3.1415926535898*".$lng."/180)* COS(l.`longitude` * 3.1415926535898/180)*POW(SIN(3.1415926535898*(".$lat."-l.`latitude`)/360),2))))*1000 AS distance,";
        }else{
            $select="";
        }

        //评论排行
        if (strstr($orderby, "4")) {
            //今日评论
            if ($orderby == "4.1") {
                $where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m-%d') = curdate()";
                //昨日评论
            } elseif ($orderby == "4.2") {
                $where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY)";
                //本周评论
            } elseif ($orderby == "4.3") {
                $where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m-%d') >= DATE_SUB(curdate(), INTERVAL 7 DAY)";
                //本月评论
            } elseif ($orderby == "4.4") {
                $where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m') = DATE_FORMAT(curdate(), '%Y-%m')";
            }

            $order = " ORDER BY total DESC";

            $archives = $dsql->SetQuery("SELECT l.`id`, l.`titleRed`, l.`titleBlod`, l.`title`, l.`is_valid`, l.`typeid`, l.`price`, l.`video`, l.`color`, l.`pubdate`, l.`body`, l.`addr`, l.`click`, l.`tel`, l.`teladdr`, l.`rec`, l.`fire`, l.`top`, l.`userid`, l.`arcrank`, l.`valid`, l.`isbid`, l.`bid_end`, l.`bid_price`, l.`price_switch`, (SELECT COUNT(`id`) FROM `#@__public_comment` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `pid` = 0 AND `type` = 'info-detail') AS total FROM `#@__infolist` l WHERE 1 = 1" . $where);

            //普通查询
        } else {
            $archives = $dsql->SetQuery("SELECT l.`id`, l.`titleRed`, l.`titleBlod`, l.`title`, l.`is_valid`, l.`typeid`, l.`price`, l.`video`, l.`longitude`, l.`longitude`,".$select." l.`color`, l.`pubdate`, l.`body`, l.`addr`, l.`click`, l.`tel`, l.`teladdr`, l.`rec`, l.`fire`, l.`top`, l.`userid`, l.`arcrank`, l.`valid`, l.`isbid`, l.`bid_end`, l.`bid_price`, l.`price_switch` FROM `#@__infolist` as l WHERE 1 = 1" . $where);
        }

        //总条数
        //$totalCount = $dsql->dsqlOper($archives, "totalCount");
        $sql = $dsql->SetQuery("SELECT COUNT(l.`id`) total FROM `#@__infolist` l WHERE 1 = 1".$where);
        $totalCount = (int)getCache("info_total", $sql, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));

        //总分页数
        $totalPage = ceil($totalCount / $pageSize);

        if ($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

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
            //过期
            $now         = GetMkTime(time());
            // $totalExpire = $dsql->dsqlOper($archives . " AND `valid` < " . $now . " AND `valid` <> 0", "totalCount");
            $totalExpire = $dsql->dsqlOper($archives . " AND `valid` < " . $now, "totalCount");

            $pageinfo['gray']   = $totalGray;
            $pageinfo['audit']  = $totalAudit;
            $pageinfo['refuse'] = $totalRefuse;
            $pageinfo['expire'] = $totalExpire;
        }

        $atpage = $pageSize * ($page - 1);
        $where  = " LIMIT $atpage, $pageSize";

        // $results = $dsql->dsqlOper($archives . $where1 . $order . $where, "results");
        $sql = $archives . $where1 . $order . $where;
        $results = getCache("info_list", $sql, 300, array("disabled" => $u));

        if ($results) {

            $param        = array(
                "service" => "info",
                "template" => "list",
                "id" => "%id%"
            );
            $typeurlParam = getUrlPath($param);

            $param    = array(
                "service" => "info",
                "template" => "detail",
                "id" => "%id%"
            );
            $urlParam = getUrlPath($param);

            $param    = array(
                "service" => "info",
                "template" => "business",
                "id" => "%id%"
            );
            $busiParam = getUrlPath($param);

            $param    = array(
                "service" => "info",
                "template" => "homepage",
                "id" => "%id%"
            );
            $homeParam = getUrlPath($param);

            $now = GetMkTime(time());

            $tmpData = array();

            foreach ($results as $key => $val) {
                $list[$key]['id']      = $val['id'];
                $list[$key]["titleNew"]= $val['title'];

                $className  = '';
                $className1 = '';
                $htmlName   = '';
                $htmlName1  = '';
                if($val['titleRed']){
                    $className  = '<font style="color:#ff3d08">';
                    $className1 = '</font>';
                }
                if($val['titleBlod']){
                    $htmlName  = '<strong>';
                    $htmlName1 = '</strong>';
                }
                $list[$key]["title"]  = $className . $htmlName . $val['title'] . $htmlName1 . $className1;
                $list[$key]['color']   = $val['color'];
                $list[$key]['price']   = $val['price'];
                $list[$key]['is_valid']= $val['is_valid'];
                $list[$key]['price_switch']   = $val['price_switch'];
                $list[$key]['video']   = $val['video'] ? getFilePath($val['video']) : '';
                $list[$key]['is_shop'] = 0;
                //查询是否是商家
                if($val['userid']){
                    if(isset($tmpData['is_shop'][$val['userid']])){
                        $is_shop = $tmpData['is_shop'][$val['userid']];
                    }else{
                        $sql     = $dsql->SetQuery("SELECT `lnglat` FROM `#@__infoshop` WHERE `uid` = " . $val['userid']);
                        $is_shop = $dsql->dsqlOper($sql, "results");
                        $tmpData['is_shop'][$val['userid']] = $is_shop;
                    }
                    if ($is_shop) {
                        $list[$key]['is_shop']     = 1;
                        // $list[$key]['lnglat']      = $is_shop[0]['lnglat'] ? explode(",", $is_shop[0]['lnglat']) : array(0, 0);
                        //
                        // $distance = getDistance($lng2, $lat2, $list[$key]['lnglat'][0], $list[$key]['lnglat'][1]);
                        // $list[$key]['lnglat_diff'] = sprintf("%.2f", ($distance / 1000));

                    }
                }

                $list[$key]['lnglat'] = array($val['longitude'], $val['latitude']);

                if($lng && $lat){
                    $list[$key]['distance']  = $val['distance'] > 1000 ? sprintf("%.1f", $val['distance'] / 1000) . $langData['siteConfig'][13][23] : sprintf("%.1f", $val['distance']) . $langData['siteConfig'][13][22];  //距离   //千米  //米
    				if(strpos($list[$key]['distance'],'千米')){
    					$list[$key]['distance'] = str_replace("千米",'km',$list[$key]['distance']);
    				}elseif(strpos($list[$key]['distance'],'米')){
    					$list[$key]['distance'] = str_replace("米",'m',$list[$key]['distance']);
    				}
                }

                //会员发布信息统计
                $fabuCount = 0;
                if($val['userid']){

                    if(isset($tmpData['fabuCount'][$val['userid']])){
                        $fabuCount = $tmpData['fabuCount'][$val['userid']];
                    }else{
                        $archives  = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infolist` WHERE `is_valid` = 0 AND `waitpay` = 0 AND `arcrank` = 1 AND `userid` = " . $val['userid']);
                        $res       = $dsql->dsqlOper($archives, "results");
                        $fabuCount = $res[0]['total'];
                        $tmpData['fabuCount'][$val['userid']] = $fabuCount;
                    }


                }
                $list[$key]['fabuCount'] = $fabuCount;

                global $data;
                $data                  = "";

                if(isset($tmpData['addrArr'][$val['addr']])){
                    $addrArr = $tmpData['addrArr'][$val['addr']];
                }else{
                    $addrArr               = getParentArr("site_area", $val['addr']);
                    $tmpData['addrArr'][$val['addr']] = $addrArr;
                }
                $addrArr               = array_reverse(parent_foreach($addrArr, "typename"));
                $list[$key]['address'] = $addrArr;

                $list[$key]['typeid'] = $val['typeid'];


                $typename = "";
                if(isset($tmpData['typename'][$val['typeid']])){
                    $typename = $tmpData['typename'][$val['typeid']];
                }else{
                    $archives = $dsql->SetQuery("SELECT `typename` FROM `#@__infotype` WHERE `id` = " . $val['typeid']);
                    $typename = getCache("info_type", $archives, 0, array("sign" => $val['typeid'], "name" => "typename"));
                    $tmpData['typename'][$val['typeid']] = $typename ? $typename : "";
                }
                $list[$key]['typename'] = $typename;

                $list[$key]['tel']     = $val['tel'];
                $list[$key]['tel_']    = preg_replace('/(1[3456789]{1}[0-9])[0-9]{4}([0-9]{4})/is',"$1****$2", $val['tel']);
                $list[$key]['teladdr'] = $val['teladdr'];

                $list[$key]['click'] = $val['click'];

                $list[$key]['pubdate']  = $val['pubdate'];

                $list[$key]['pubdate1'] = FloorTime(GetMkTime(time()) - $val['pubdate'], 3);
                $list[$key]['pubdate_istoday']  = 0;
                $list[$key]['pubdate2']  = date("H:i", $val['pubdate']);
                if(date("Y-m-d", $val['pubdate']) == date("Y-m-d", time())){
                    $list[$key]['pubdate_istoday']  = 1;
                }


                $list[$key]['pubdate_is']  = date("H:i", $val['pubdate']);

                $list[$key]['fire'] = $val['fire'];
                $list[$key]['rec']  = $val['rec'];
                $list[$key]['top']  = $val['top'];

                if ($val['isbid']) {
                    $top_arr                    = [];
                    $list[$key]["rec_fire_top"] = 'top';
                    $top_arr[]                  = $key;
                }

                //图集信息
                $picArr = [];
                $archives = $dsql->SetQuery("SELECT `picPath` FROM `#@__infopic` WHERE `aid` = " . $val['id'] . " ORDER BY `id` ASC LIMIT 0, 6");
                $results  = $dsql->dsqlOper($archives, "results");
                if (!empty($results)) {
                    $list[$key]['litpic'] = getFilePath($results[0]["picPath"]);
                    foreach($results as $k=> $v){
						$picArr[$k]['litpic'] = $v['picPath'] ? getFilePath($v['picPath']) : '';
					}
                }
                $list[$key]["picArr"]     = $picArr;

                $archives    = $dsql->SetQuery("SELECT `id` FROM `#@__member_collect` WHERE `module` = 'info' AND `action` = 'detail' AND `aid` = " . $val['id']);
                $collectnum  = $dsql->dsqlOper($archives, "totalCount");
                $list[$key]['collectnum'] = $collectnum;


                $list[$key]['isbid']   = $val['isbid'];
                $list[$key]['valid']   = $val['valid'];
                $list[$key]['isvalid'] = ($val['valid'] != 0 && $val['valid'] > $now) ? 0 : 1;

                //是否商家
                $shopinfo     = $dsql->SetQuery("SELECT `id` FROM `#@__infoshop` WHERE `uid` = " . $val['userid']);
                $infoshop     = $dsql->dsqlOper($shopinfo, "results");
                $info_shop_id = 0;
                if ($infoshop) {
                    $info_shop_id = $infoshop[0]['id'];
                }

                if($info_shop_id){
                    $list[$key]['busiurl'] = str_replace("%id%", $info_shop_id, $busiParam);
                }else{
                    $list[$key]['busiurl'] = str_replace("%id%", $val['userid'], $homeParam);
                }
                $list[$key]['typeurl'] = str_replace("%id%", $val['typeid'], $typeurlParam);
                $list[$key]['url']     = str_replace("%id%", $val['id'], $urlParam);


                $archives             = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infopic` WHERE `aid` = " . $val['id']);
                $res                  = $dsql->dsqlOper($archives, "results");
                $list[$key]['pcount'] = $res[0]['total'];

                $list[$key]['desc'] = cn_substrR(strip_tags($val['body']), 80);

                // $archives             = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infocommon` WHERE `aid` = " . $val['id'] . " AND `ischeck` = 1 AND `floor` = 0");
                $archives = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'info-detail' AND `aid` = " . $val['id'] . " AND `pid` = 0");
                $res                  = $dsql->dsqlOper($archives, "results");
                $list[$key]['common'] = $res[0]['total'];

                //会员信息
                $member = array(
                    'userid' => 0,
                    'nickname' => '',
                    'photo' => '',
                    'userType' => 0,
                    'emailCheck' => 0,
                    'phoneCheck' => 0,
                    'certifyState' => 0,
                    'phone' => '',
                );
                if($val['userid']){
                    $member = getMemberDetail($val['userid']);
                }

                $list[$key]['member'] = $member['userid'] ? array(
                    "id" => $member['userid'],
                    "nickname" => $member['nickname'],
                    "photo" => $member['photo'],
                    "userType" => $member['userType'],
                    "emailCheck" => $member['emailCheck'],
                    "phoneCheck" => $member['phoneCheck'],
                    "certifyState" => $member['certifyState'],
                    "phone" => $val['tel']
                ) : array();

                $param_u = [
                    'service' => 'info',
                    'template' => $is_shop ? 'business' : 'homepage',
                    'id' => $is_shop ? $is_shop[0]['id'] : $member['userid'],
                ];
                $list[$key]['url_user'] = getUrlPath($param_u);


                //验证是否已经收藏
                $params                = array(
                    "module" => "info",
                    "temp" => "detail",
                    "type" => "add",
                    "id" => $val['id'],
                    "check" => 1
                );
                $collect               = checkIsCollect($params);
                $list[$key]['collect'] = $collect == "has" ? 1 : 0;




                //会员中心显示信息状态
                if ($u == 1 && $userLogin->getMemberID() > -1) {

                    $now = GetMkTime(time());
                    if ($val['pubdate'] + $val['valid'] * 86400 < $now AND $val['valid'] != 0) {
                        $list[$key]['arcrank'] = 4;
                    } else {
                        $list[$key]['arcrank'] = $val['arcrank'];
                    }

                    //显示竞价结束时间、每日预算
                    $list[$key]['bid_price'] = $val['bid_price'];
                    $list[$key]['bid_end']   = $val['bid_end'];

                    $list[$key]['waitpay'] = $val['waitpay'];
                }


            }
            $resList = $list;
            // if(!$flag && $orderby != 'price'){
            //     $resarr1 = [];
            //     $resarr2 = [];
            //     foreach ($list as $key => $item) {
            //         if ($item['top'] == 1) {
            //             $resarr1[$key] = $item;
            //         } else {
            //             $resarr2[$key] = $item;
            //         }
            //     }
            //     $resList = array_merge($resarr1, $resarr2);
            // }


        }
        if($memberType == 1){
            if($resList){
                foreach ($resList as $index => $item){
                    if($item['is_shop'] == 1){
                        unset($resList[$index]);
                    }
                }
                $resList1 = [];
                foreach ($resList as $index => $item){
                    array_push($resList1, $item);
                }
                return array("pageInfo" => $pageinfo, "list" => $resList1);
            }
        }else if($memberType == 2){
            if($resList){
                foreach ($resList as $index => $item){
                    if($item['is_shop'] != 1){
                        unset($resList[$index]);
                    }
                }
                $resList2 = [];
                foreach ($resList as $index => $item){
                    array_push($resList2, $item);
                }
                return array("pageInfo" => $pageinfo, "list" => $resList2);
            }
        }


        return array("pageInfo" => $pageinfo, "list" => $resList);
    }


    public function ilist()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = $itemList = array();
        $nature   = $typeid = $addrid = $valid = $title = $rec = $fire = $top = $thumb = $orderby = $u = $state = $uid = $userid = $tel = $page = $pageSize = $where = $where1 = "";
        if (!empty($this->param)) {
            if (!is_array($this->param)) {
                return array("state" => 200, "info" => $langData['info'][1][58]);
            } else {
                $nature   = $this->param['nature'];
                $typeid   = $this->param['typeid'];
                $addrid   = $this->param['addrid'];
                $valid    = $this->param['valid'];
                $title    = $this->param['title'];
                $itemList = $this->param['item'];
                $rec      = $this->param['rec'];
                $fire     = $this->param['fire'];
                $top      = $this->param['top'];
                $thumb    = $this->param['thumb'];
                $orderby  = $this->param['orderby'];
                $u        = $this->param['u'];
                $state    = $this->param['state'];
                $uid      = $this->param['uid'];
                $userid   = $this->param['userid'];
                $tel      = $this->param['tel'];
				$lng      = $this->param['lng'];
                $lat      = $this->param['lat'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }
        $now = strtotime(date("Y-m-d"));

        //指定会员
        if (!empty($userid)) {
            $where .= " AND l.`userid` = $userid";
        }

        //指定会员
        if (!empty($uid)) {
            $where .= " AND l.`userid` = $uid";
        }

        $cityid = getCityId($this->param['cityid']);
        if ($cityid && !$userid && !$uid && $u != 1) {
            $where .= " AND l.`cityid` = " . $cityid;
        } else {
            if(!$u){
                $where .= " AND l.`cityid` != 0";
            }
        }

        //推荐
        if (!empty($rec)) {
            $where .= " AND l.`rec` = 1";
        }

        //火急
        if (!empty($fire)) {
            $where .= " AND l.`fire` = 1";
        }

        //置顶
        if (!empty($top)) {
            // $where .= " AND `top` = 1";
        }

        //指定电话号码
        if (!empty($tel)) {
            $where .= " AND l.`tel` = '$tel'";
        }

        //是否输出当前登录会员的信息

        if ($u != 1) {
            $where .= " AND l.`arcrank` = 1 AND l.`waitpay` = 0 AND `is_valid` = 0";
        } else {
            $uid      = $userLogin->getMemberID();
            $userinfo = $userLogin->getMemberInfo();

            if ($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "info"))) {
                return array("state" => 200, "info" => $langData['info'][1][73]);//'商家权限验证失败！'
            }

            $where .= " AND l.`userid` = " . $uid;

            if ($state != "") {
                if ($state == 4) {
                    // $now    = GetMkTime(time());
                    $where1 = " AND (l.`valid` < " . $now . " OR l.`valid` = 0)";
                } else {
                    $where1 = " AND l.`arcrank` = " . $state;
                }
            }
        }

        //只查找不过期的信息
        if ($u != 1) {
            // $now   = GetMkTime(time());
            $now = strtotime(date("Y-m-d"));
            // $where .= " AND l.`valid` > " . $now . " AND l.`valid` <> 0";
            $where .= " AND l.`valid` > " . $now;
        }

        //信息性质
        if (!empty($nature)) {

            //个人
            if ($nature == 1) {
                $where .= " AND ((SELECT `mtype` FROM `#@__member` WHERE `id` = l.`userid`) = 1 OR l.`userid` = -1)";

                //商家
            } elseif ($nature == 2) {
                $where .= " AND ((SELECT `mtype` FROM `#@__member` WHERE `id` = l.`userid`) = 2)";
            }

        }

        //遍历分类
        if (!empty($typeid)) {
            if ($dsql->getTypeList($typeid, "infotype")) {
                global $arr_data;
                $arr_data = array();
                $lower    = arr_foreach($dsql->getTypeList($typeid, "infotype"));
                $lower    = $typeid . "," . join(',', $lower);
            } else {
                $lower = $typeid;
            }
            $where .= " AND l.`typeid` in ($lower)";
        }

        //遍历地区
        if (!empty($addrid)) {
            if ($dsql->getTypeList($addrid, "site_area")) {
                global $arr_data;
                $arr_data = array();
                $lower    = arr_foreach($dsql->getTypeList($addrid, "site_area"));
                $lower    = $addrid . "," . join(',', $lower);
            } else {
                $lower = $addrid;
            }
            $where .= " AND l.`addr` in ($lower)";
        }

        if (!empty($title)) {

            //搜索记录
            siteSearchLog("info", $title);

            $where .= " AND (l.`title` like '%" . $title . "%' OR l.`tel` like '%" . $title . "%')";
        }

        //取出字段表中满足条件的所有信息ID Start
        $aidArr = $infoidArr = $aid = array();

        $tj = true;

        if (!empty($itemList)) {
            $itemList = json_decode($itemList, true);
            foreach ($itemList as $k => $v) {
                if (!empty($v['value'])) {
                    $archives = $dsql->SetQuery("SELECT `aid` FROM `#@__infoitem` WHERE `iid` = " . $v['id'] . " AND find_in_set('" . $v['value'] . "', `value`)");
                    $results  = $dsql->dsqlOper($archives, "results");
                    if ($results) {
                        foreach ($results as $key => $val) {
                            $infoidArr[$k][$key] = $val['aid'];
                        }
                    } else {
                        $tj = false;
                    }
                }
            }
        }

        if (!$tj) $infoidArr = array();

        //二维数组转一维
        if (!empty($infoidArr)) {
            foreach ($infoidArr as $id) {
                $aid[] = join(",", $id);
            }
        }

        $aid = join(",", $aid);
        $aid = explode(",", $aid);

        //取出重复次数最多的信息ID
        $aidcount = array_count_values($aid);
        foreach ($aidcount as $key => $val) {
            if ($val == count($infoidArr)) {
                $aidArr[] = $key;
            }
        }

        $aidArr = join(",", $aidArr);
        //取出字段表中满足条件的所有信息ID End
        if (!empty($itemList) && empty($infoidArr)) {
            $where .= " AND 1 = 2";
        } else {
            if (!empty($aidArr)) {
                $where .= " AND l.`id` in ($aidArr)";
            }
        }

        //有图
        if (!empty($thumb)) {
            $where .= " AND (SELECT COUNT(`id`) FROM `#@__infopic` WHERE `aid` = l.`id`) > 0";
        }

        //取当前星期，当前时间
        // $time = time();
        $time = getTimeStep();
        $week = date('w', $time);
        $hour = (int)date('H');
        $ob   = "l.`bid_week{$week}` = 'all'";
        if ($hour > 8 && $hour < 21) {
            $ob .= " or l.`bid_week{$week}` = 'day'";
        }
        $ob = "($ob)";

        //排序规则
        //置顶  普通置顶无需特殊验证，计划置顶先验证开始时间，然后验证当天(周几)是否做置顶，再验证当前时间点(早8晚8)是否做置顶，与当天的逻辑是或的关系；（结束时间这里不做验证，交给计划任务来处理，置顶结束掉，由计划任务更新信息状态），注意，数据返回的时候同样需要验证计划置顶的当前时间状态
        //火急
        //推荐
        //后台自定义
        //ID
        $order = " ORDER BY case when l.`isbid` = 1 and l.`bid_type` = 'normal' then 1 else 2 end, case when l.`isbid` = 1 and l.`bid_type` = 'plan' and l.`bid_start` < $time and $ob then 1 else 2 end, l.`fire` DESC, l.`rec` DESC, l.`weight` DESC, l.`pubdate` DESC, l.`id` DESC";
        //价格
        if ($orderby == "price") {
            $order = " ORDER BY case when l.`isbid` = 1 and l.`bid_type` = 'normal' then 1 else 2 end, case when l.`isbid` = 1 and l.`bid_type` = 'plan' and l.`bid_start` < $time and $ob then 1 else 2 end, l.`price` DESC, l.`fire` DESC, l.`rec` DESC, l.`weight` DESC, l.`id` DESC";
            //发布时间
        } elseif ($orderby == "1") {
            $order = " ORDER BY case when l.`isbid` = 1 and l.`bid_type` = 'normal' then 1 else 2 end, case when l.`isbid` = 1 and l.`bid_type` = 'plan' and l.`bid_start` < $time and $ob then 1 else 2 end, l.`pubdate` DESC, l.`fire` DESC, l.`rec` DESC, l.`weight` DESC, l.`id` DESC";
            //浏览量
        } elseif ($orderby == "2") {
            $order = " ORDER BY case when l.`isbid` = 1 and l.`bid_type` = 'normal' then 1 else 2 end, case when l.`isbid` = 1 and l.`bid_type` = 'plan' and l.`bid_start` < $time and $ob then 1 else 2 end, l.`click` DESC, l.`fire` DESC, l.`rec` DESC, l.`weight` DESC, l.`id` DESC";
            //今日浏览量
        } elseif ($orderby == "2.1") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = curdate() ORDER BY case when l.`isbid` = 1 and l.`bid_type` = 'normal' then 1 else 2 end, case when l.`isbid` = 1 and l.`bid_type` = 'plan' and l.`bid_start` < $time and $ob then 1 else 2 end, l.`click` DESC, l.`fire` DESC, l.`rec` DESC, l.`weight` DESC, l.`id` DESC";
            //昨日浏览量
        } elseif ($orderby == "2.2") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY) ORDER BY case when l.`isbid` = 1 and l.`bid_type` = 'normal' then 1 else 2 end, case when l.`isbid` = 1 and l.`bid_type` = 'plan' and l.`bid_start` < $time and $ob then 1 else 2 end, l.`click` DESC, l.`fire` DESC, l.`rec` DESC, l.`weight` DESC, l.`id` DESC";
            //本周浏览量
        } elseif ($orderby == "2.3") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') >= DATE_SUB(curdate(), INTERVAL 7 DAY) ORDER BY case when l.`isbid` = 1 and l.`bid_type` = 'normal' then 1 else 2 end, case when l.`isbid` = 1 and l.`bid_type` = 'plan' and l.`bid_start` < $time and $ob then 1 else 2 end, l.`click` DESC, l.`fire` DESC, l.`rec` DESC, l.`weight` DESC, l.`id` DESC";
            //本月浏览量
        } elseif ($orderby == "2.4") {
            $order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m') = DATE_FORMAT(curdate(), '%Y-%m') ORDER BY case when l.`isbid` = 1 and l.`bid_type` = 'normal' then 1 else 2 end, case when l.`isbid` = 1 and l.`bid_type` = 'plan' and l.`bid_start` < $time and $ob then 1 else 2 end, l.`click` DESC, l.`fire` DESC, l.`rec` DESC, l.`weight` DESC, l.`id` DESC";
            //随机
        } elseif ($orderby == "3") {
            $order = " ORDER BY rand()";
        }

        // 会员中心
        if($u){
            $order = " ORDER BY l.`id` DESC";
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        //查询距离
        $select = "";
		if((!empty($lng))&&(!empty($lat))){
            $select="(2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*(".$lng."-l.`longitude`)/360),2)+COS(3.1415926535898*".$lng."/180)* COS(l.`longitude` * 3.1415926535898/180)*POW(SIN(3.1415926535898*(".$lat."-l.`latitude`)/360),2))))*1000 AS distance,";
        }else{
            $select="";
        }

        //评论排行
        if (strstr($orderby, "4")) {
            //今日评论
            if ($orderby == "4.1") {
                $where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m-%d') = curdate()";
                //昨日评论
            } elseif ($orderby == "4.2") {
                $where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY)";
                //本周评论
            } elseif ($orderby == "4.3") {
                $where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m-%d') >= DATE_SUB(curdate(), INTERVAL 7 DAY)";
                //本月评论
            } elseif ($orderby == "4.4") {
                $where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m') = DATE_FORMAT(curdate(), '%Y-%m')";
            }

            $order = " ORDER BY total DESC";

            $archives = $dsql->SetQuery("SELECT l.`id`, l.`titleBlod`, l.`titleRed`, l.`title`, l.`is_valid`, l.`typeid`, l.`price`, l.`price_switch`, l.`video`, l.`color`, l.`pubdate`, l.`body`, l.`addr`, l.`click`, l.`tel`, l.`teladdr`, l.`rec`, l.`fire`, l.`userid`, l.`arcrank`, l.`valid`, l.`isbid`, l.`bid_type`, l.`bid_week0`, l.`bid_week1`, l.`bid_week2`, l.`bid_week3`, l.`bid_week4`, l.`bid_week5`, l.`bid_week6`, l.`bid_start`, l.`bid_end`, l.`bid_price`, l.`waitpay`, l.`refreshSmart`, l.`refreshCount`, l.`refreshTimes`, l.`refreshPrice`, l.`refreshBegan`, l.`refreshNext`, l.`refreshSurplus`, (SELECT COUNT(`id`) FROM `#@__public_comment` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `pid` = 0 AND `type` = 'info-detail') AS total FROM `#@__infolist` l WHERE 1 = 1" . $where);


            //普通查询
        } else {
            $archives = $dsql->SetQuery("SELECT l.`id`, l.`titleBlod`, l.`titleRed`, l.`title`, l.`is_valid`, l.`typeid`, l.`price`, l.`price_switch`, l.`video`, l.`color`, l.`pubdate`, l.`body`, l.`addr`, l.`click`, l.`tel`, l.`teladdr`, l.`rec`, l.`fire`, l.`userid`, l.`arcrank`, ".$select."l.`valid`, l.`isbid`, l.`bid_type`, l.`bid_week0`, l.`bid_week1`, l.`bid_week2`, l.`bid_week3`, l.`bid_week4`, l.`bid_week5`, l.`bid_week6`, l.`bid_start`, l.`bid_end`, l.`bid_price`, l.`waitpay`, l.`refreshSmart`, l.`refreshCount`, l.`refreshTimes`, l.`refreshPrice`, l.`refreshBegan`, l.`refreshNext`, l.`refreshSurplus` FROM `#@__infolist` as l WHERE 1 = 1" . $where);
        }

        //总条数
        // $totalCount = $dsql->dsqlOper($archives, "totalCount");
        $sql = $dsql->SetQuery("SELECT COUNT(l.`id`) total FROM `#@__infolist` l WHERE 1 = 1".$where);
        $total = getCache("info_total", $sql, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
        $totalCount = $total;
        //总分页数
        $totalPage = ceil($totalCount / $pageSize);

        if ($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

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
            //过期
            $now         = GetMkTime(time());
            // $totalExpire = $dsql->dsqlOper($archives . " AND `valid` < " . $now . " AND `valid` <> 0", "totalCount");
            $totalExpire = $dsql->dsqlOper($archives . " AND `valid` < " . $now, "totalCount");

            $pageinfo['gray']   = $totalGray;
            $pageinfo['audit']  = $totalAudit;
            $pageinfo['refuse'] = $totalRefuse;
            $pageinfo['expire'] = $totalExpire;
        }

        $atpage = $pageSize * ($page - 1);
        $where  = " LIMIT $atpage, $pageSize";

        // $results = $dsql->dsqlOper($archives . $where1 . $order . $where, "results");
        $results = getCache("info_list", $archives.$where1.$order.$where, 300, array("disabled" => $u));

        if ($results) {

            $param        = array(
                "service" => "info",
                "template" => "list",
                "id" => "%id%"
            );
            $typeurlParam = getUrlPath($param);

            $param    = array(
                "service" => "info",
                "template" => "detail",
                "id" => "%id%"
            );
            $urlParam = getUrlPath($param);

            $now = $time;

            foreach ($results as $key => $val) {
                $list[$key]['id']    = $val['id'];
                $list[$key]["titleNew"]  = $val['title'];

                $className  = '';
                $className1 = '';
                $htmlName   = '';
                $htmlName1  = '';
                if($val['titleRed']){
                    $className  = '<font style="color:#ff3d08">';
                    $className1 = '</font>';
                }
                if($val['titleBlod']){
                    $htmlName  = '<strong>';
                    $htmlName1 = '</strong>';
                }
                $list[$key]["title"]  = $className . $htmlName . $val['title'] . $htmlName1 . $className1;


                $list[$key]['color'] = $val['color'];
                $list[$key]['price'] = $val['price'];
                $list[$key]['is_valid'] = $val['is_valid'];
                $list[$key]['price_switch']   = $val['price_switch'];
                $list[$key]['video'] = $val['video'] ? getFilePath($val['video']) : '';

                if($lng && $lat){
                    $list[$key]['distance']  = $val['distance'] > 1000 ? sprintf("%.1f", $val['distance'] / 1000) . $langData['siteConfig'][13][23] : sprintf("%.1f", $val['distance']) . $langData['siteConfig'][13][22];  //距离   //千米  //米
    				if(strpos($list[$key]['distance'],'千米')){
    					$list[$key]['distance'] = str_replace("千米",'km',$list[$key]['distance']);
    				}elseif(strpos($list[$key]['distance'],'米')){
    					$list[$key]['distance'] = str_replace("米",'m',$list[$key]['distance']);
    				}
                }

                //会员发布信息统计
                $archives                = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infolist` WHERE `is_valid` = 0 AND `waitpay` = 0 AND `arcrank` = 1 AND `userid` = " . $val['userid']);
                $results                 = $dsql->dsqlOper($archives, "totalCount");
                $list[$key]['fabuCount'] = $results;

                global $data;
                $data                  = "";
                $addrArr               = getParentArr("site_area", $val['addr']);
                $addrArr               = array_reverse(parent_foreach($addrArr, "typename"));
                $addrArr               = array_slice($addrArr, -2, 2);
                $list[$key]['address'] = join(" - ", $addrArr);

                $list[$key]['typeid'] = $val['typeid'];
                $archives             = $dsql->SetQuery("SELECT `typename` FROM `#@__infotype` WHERE `id` = " . $val['typeid']);
                $typename = getCache("info_type", $archives, 0, array("sign" => $val['typeid'], "name" => "typename"));
                $typename = $typename ? $typename : "";
                $list[$key]['typename'] = $typename;

                $list[$key]['tel']     = $val['tel'];
                $list[$key]['teladdr'] = $val['teladdr'];

                $list[$key]['click'] = $val['click'];

                $list[$key]['pubdate'] = $val['pubdate'];

                $list[$key]['pubdate1'] = FloorTime(GetMkTime(time()) - $val['pubdate']);
                $list[$key]['fire']     = $val['fire'];
                $list[$key]['rec']      = $val['rec'];
                // $list[$key]['top']     = $val['top'];

                $isbid = $val['isbid'];
                //计划置顶需要验证当前时间点是否为置顶状态，如果不是，则输出为0
                if ($val['bid_type'] == 'plan' && !$u) {
                    if ($val['bid_week' . $week] == '' || ($val['bid_start'] > $now && !$u) || ($val['bid_week' . $week] == 'day' && ($hour < 8 || $hour > 20))) {
                        $isbid = 0;
                    }
                }
                $list[$key]['isbid']   = $isbid;
                $list[$key]['valid']   = $val['valid'];
                $list[$key]['isvalid'] = ($val['valid'] != 0 && $val['valid'] > $now) ? 0 : 1;

                $list[$key]['typeurl'] = str_replace("%id%", $val['typeid'], $typeurlParam);
                $list[$key]['url']     = str_replace("%id%", $val['id'], $urlParam);

                //图集信息
                $picArr = [];
                $archives = $dsql->SetQuery("SELECT `picPath` FROM `#@__infopic` WHERE `aid` = " . $val['id'] . " ORDER BY `id` ASC LIMIT 0, 6");
                $results  = $dsql->dsqlOper($archives, "results");
                if (!empty($results)) {
                    $list[$key]['litpic'] = getFilePath($results[0]["picPath"]);
                    foreach($results as $k=> $v){
						$picArr[$k]['litpic'] = $v['picPath'] ? getFilePath($v['picPath']) : '';
					}
                }
                $list[$key]["picArr"]     = $picArr;

                $archives             = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infopic` WHERE `aid` = " . $val['id']);
                $res                  = $dsql->dsqlOper($archives, "results");
                $list[$key]['pcount'] = $res[0]['total'];

                $list[$key]['desc'] = cn_substrR(strip_tags($val['body']), 80);

                // $archives             = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infocommon` WHERE `aid` = " . $val['id'] . " AND `ischeck` = 1 AND `floor` = 0");
                $archives = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'info-detail' AND `aid` = " . $val['id'] . " AND `pid` = 0");
                $res                  = $dsql->dsqlOper($archives, "results");
                $list[$key]['common'] = $res[0]['total'];

                $archives    = $dsql->SetQuery("SELECT `id` FROM `#@__member_collect` WHERE `module` = 'info' AND `action` = 'detail' AND `aid` = " . $val['id']);
                $collectnum  = $dsql->dsqlOper($archives, "totalCount");
                $list[$key]['collectnum'] = $collectnum;

                //会员信息
                $member               = getMemberDetail($val['userid']);
                $list[$key]['member'] = array(
                    "id" => $val['userid'],
                    "nickname" => $member['nickname'],
                    "photo" => $member['photo'],
                    "userType" => $member['userType'],
                    "emailCheck" => $member['emailCheck'],
                    "phoneCheck" => $member['phoneCheck'],
                    "certifyState" => $member['certifyState']
                );

                //验证是否已经收藏
                $params                = array(
                    "module" => "info",
                    "temp" => "detail",
                    "type" => "add",
                    "id" => $val['id'],
                    "check" => 1
                );
                $collect               = checkIsCollect($params);
                $list[$key]['collect'] = $collect == "has" ? 1 : 0;

                //会员中心显示信息状态
                if ($u == 1 && $userLogin->getMemberID() > -1) {

                    $now = GetMkTime(time());
                    if ($val['pubdate'] + $val['valid'] * 86400 < $now AND $val['valid'] != 0) {
                        $list[$key]['arcrank'] = 4;
                    } else {
                        $list[$key]['arcrank'] = $val['arcrank'];
                    }

                    //显示置顶信息
                    if ($isbid) {
                        $list[$key]['bid_type']  = $val['bid_type'];
                        $list[$key]['bid_price'] = $val['bid_price'];
                        $list[$key]['bid_start'] = $val['bid_start'];
                        $list[$key]['bid_end']   = $val['bid_end'];

                        //计划置顶详细
                        if ($val['bid_type'] == 'plan') {
                            $tp_beganDate = date('Y-m-d', $val['bid_start']);
                            $tp_endDate   = date('Y-m-d', $val['bid_end']);

                            $diffDays   = (int)(diffBetweenTwoDays($tp_beganDate, $tp_endDate) + 1);
                            $tp_planArr = array();

                            $weekArr = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');

                            //时间范围内每天的明细
                            for ($i = 0; $i < $diffDays; $i++) {
                                $began = GetMkTime($tp_beganDate);
                                $day   = AddDay($began, $i);
                                $week  = date("w", $day);

                                if ($val['bid_week' . $week]) {
                                    array_push($tp_planArr, array(
                                        'date' => date('Y-m-d', $day),
                                        'week' => $weekArr[$week],
                                        'type' => $val['bid_week' . $week],
                                        'state' => $day < GetMkTime(date('Y-m-d', time())) ? 0 : 1
                                    ));
                                }
                            }

                            $list[$key]['bid_plan'] = $tp_planArr;
                        }
                    }

                    $list[$key]['waitpay'] = $val['waitpay'];

                    //智能刷新
                    $refreshSmartState = (int)$val['refreshSmart'];
                    if ($val['refreshSurplus'] <= 0) {
                        $refreshSmartState = 0;
                    }
                    $list[$key]['refreshSmart'] = $refreshSmartState;
                    if ($refreshSmartState) {
                        $list[$key]['refreshCount']   = $val['refreshCount'];
                        $list[$key]['refreshTimes']   = $val['refreshTimes'];
                        $list[$key]['refreshPrice']   = $val['refreshPrice'];
                        $list[$key]['refreshBegan']   = $val['refreshBegan'];
                        $list[$key]['refreshNext']    = $val['refreshNext'];
                        $list[$key]['refreshSurplus'] = $val['refreshSurplus'];
                    }
                }

            }

        }

        return array("pageInfo" => $pageinfo, "list" => $list);
    }


    /**
     * 信息详细
     * @return array
     */
    public function detail()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $infoDetail = array();
        $id         = $this->param;
        $id         = is_numeric($id) ? $id : $id['id'];

        if (!is_numeric($id)) return array("state" => 200, "info" => $langData['info'][1][58]);

        //判断是否管理员已经登录
        $where = "";


        // 此处是为了判断信息在未审核状态下，只有管理员和发布者可以在前台浏览
        if ($userLogin->getUserID() == -1) {

            $where = " AND `arcrank` = 1";

            //如果没有登录再验证会员是否已经登录
            if ($userLogin->getMemberID() == -1) {
                $where = " AND `arcrank` = 1";
            } else {
                $where = " AND (`arcrank` = 1 OR `userid` = " . $userLogin->getMemberID() . ")";
            }

            $where .= " AND `waitpay` = 0";
        }

        $archives = $dsql->SetQuery("SELECT * FROM `#@__infolist` WHERE `id` = " . $id . $where);
        // $results  = $dsql->dsqlOper($archives, "results");
        $results = getCache("info_detail", $archives, 0, $id);
        if ($results) {

            $valid   = $results[0]['valid'];
            $pubdate = $results[0]['pubdate'];

            $infoDetail["id"]     = $results[0]['id'];
            $infoDetail["titleBlod"]     = $results[0]['titleBlod'];
            $infoDetail["titleRed"]     = $results[0]['titleRed'];
            $infoDetail["cityid"] = $results[0]['cityid'];
            $infoDetail["typeid"] = $results[0]['typeid'];
            $infoDetail["price_switch"] = $results[0]['price_switch'];
            $infoDetail['typename'] = '';
            $archives             = $dsql->SetQuery("SELECT `typename`, `parentid` FROM `#@__infotype` WHERE `id` = " . $results[0]['typeid']);
            $typename             = $dsql->dsqlOper($archives, "results");
            if($typename){
                $infoDetail['typename'] = $typename[0]['typename'];

                $infoDetail['p_typeid'] = $typename[0]['parentid'];

                $archives             = $dsql->SetQuery("SELECT `typename` FROM `#@__infotype` WHERE `id` = " . $typename[0]['parentid']);
                $typename             = $dsql->dsqlOper($archives, "results");
                $infoDetail['p_typename'] = $typename[0]['typename'];

            }

            if($results[0]['titleRed']){
                $className  = '<font style="color:#ff3d08">';
                $className1 = '</font>';
            }
            if($results[0]['titleBlod']){
                $htmlName  = '<strong>';
                $htmlName1 = '</strong>';
            }
            $infoDetail["titlenew"]  = $className . $htmlName . $results[0]['title'] . $htmlName1 . $className1;
            $infoDetail["title"]     = $results[0]['title'];

            $infoDetail["is_valid"]  = $results[0]['is_valid'];
            $infoDetail["addrid"] = $results[0]['addr'];
            $infoDetail["video"]  = $results[0]['video'] ? getFilePath($results[0]['video']) : '';

            global $data;
            $data                  = "";
            $addrArr               = getParentArr("site_area", $results[0]['addr']);
            $addrArr               = array_reverse(parent_foreach($addrArr, "typename"));
            $infoDetail['address'] = join(" > ", $addrArr);

            $infoDetail["validVal"] = $results[0]['valid'];

            $item       = array();
            $infoitem   = $dsql->SetQuery("SELECT `iid`, `value` FROM `#@__infoitem` WHERE `aid` = " . $results[0]['id'] . " ORDER BY `id` ASC");
            $itemResult = $dsql->dsqlOper($infoitem, "results");
            if ($itemResult) {
                foreach ($itemResult as $key => $val) {
                    $typeitem   = $dsql->SetQuery("SELECT i.`id`, i.`title` FROM `#@__infotypeitem` i LEFT JOIN `#@__infotype` t ON t.`id` = i.`tid` WHERE i.`id` = " . $val['iid'] . " AND t.`id` = i.`tid`");
                    $itemResult = $dsql->dsqlOper($typeitem, "results");
                    if ($itemResult) {
                        $item[$key]['id']    = $val['iid'];
                        $item[$key]['type']  = $itemResult[0]['title'];
                        $item[$key]['value'] = $val['value'];
                    }
                }
            }
            $infoDetail["item"] = $item;

            $infoDetail["body"]   = $results[0]['body'];
            $infoDetail["mbody"]  = empty($results[0]['mbody']) ? $results[0]['body'] : $results[0]['mbody'];
            $infoDetail["person"] = $results[0]['person'];

            $RenrenCrypt       = new RenrenCrypt();
            $infoDetail["tel"] = base64_encode($RenrenCrypt->php_encrypt($results[0]['tel']));

            //if($userLogin->getUserID() > -1 || $userLogin->getMemberID() > -1){
            $infoDetail["telNum"] = $results[0]['tel'];
            $infoDetail["telNum_"] = preg_replace('/(1[3456789]{1}[0-9])[0-9]{4}([0-9]{4})/is',"$1****$2", $results[0]['tel']);
            //}

            $infoDetail["teladdr"]                = $results[0]['teladdr'];
            $infoDetail["yunfei"]                = $results[0]['yunfei'];
            $infoDetail["qq"]                     = $results[0]['qq'];
            $infoDetail["click"]                  = $results[0]['click'];
            $infoDetail["ip"]                     = preg_replace('/(\d+)\.(\d+)\.(\d+)\.(\d+)/is', "$1.$2.*.*", $results[0]['ip']);
            $infoDetail["ipaddr"]                 = $results[0]['ipaddr'];
            $infoDetail["userid"]                 = $results[0]['userid'];
            $infoDetail["pubdate"]                = $pubdate;
            $infoDetail['member']                 = getMemberDetail($results[0]['userid']);

            $infoDetail['member']['phone'] = $results[0]['tel'];
            $infoDetail['member']['qq'] = $results[0]['qq'];

            $infoDetail['member']['regtime_year'] = date("Y") - substr(FloorTime(time() - strtotime($infoDetail['member']['regtime'])), 0, 4);

            $days = (time() - (strtotime($infoDetail['member']['regtime']))) / 3600 / 24;

            $mons                   = (int)($days / 30);
            $infoDetail['member']['mons'] = $mons;

            $infoDetail["rec"]                    = $results[0]['rec'];
            $infoDetail["fire"]                   = $results[0]['fire'];
            $infoDetail["top"]                    = $results[0]['top'];
            if ($results[0]['top']) {
                $infoDetail["rec_fire_top"] = 'top';
            }

            $shopinfo     = $dsql->SetQuery("SELECT `id` FROM `#@__infoshop` WHERE `uid` = {$results[0]['userid']}");
            $infoshop     = $dsql->dsqlOper($shopinfo, "results");
            $info_shop_id = 0;
            if ($infoshop) {
                $info_shop_id = $infoshop[0]['id'];
            }
            $infoDetail['info_shop_id'] = $info_shop_id;

            //会员发布信息统计
            $archives                = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infolist` WHERE `is_valid` = 0 AND `waitpay` = 0 AND `arcrank` = 1 AND `userid` = " . $results[0]['userid']);
            $infoDetail['fabuCount'] = getCache("info", $archives, 300, array("name" => "total"));

            $archives    = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member_collect` WHERE `module` = 'info' AND `action` = 'detail' AND `aid` = " . $results[0]['id']);
            $infoDetail['collectnum'] = getCache("info", $archives, 300, array("name" => "total"));
            // $collectnum  = $dsql->dsqlOper($archives, "totalCount");
            // $infoDetail['collectnum'] = $collectnum;

            //验证是否已经收藏
            $params = array(
                "module" => "info",
                "temp" => "detail",
                "type" => "add",
                "id" => $results[0]['id'],
                "check" => 1
            );
            $collect = checkIsCollect($params);
            $infoDetail['collect'] = $collect == "has" ? 1 : 0;
            $infoDetail["price"]  = $results[0]['price'];
            $infoDetail["price1"]  = $results[0]['price'] + $results[0]['yunfei'] ;

            //有效期
            $now                     = GetMkTime(time());
            $infoDetail["valid"]     = $valid;
            $infoDetail["isvalid"]   = ($valid == 0 || $valid < $now) ? 1 : 0;
            $infoDetail["validCeil"] = ($valid != 0 && $valid > $now) ? ceil(($valid - $now) / 86400) . "天后过期" : "已过期";


            //获取手机号码共发布多少条信息
            $archives               = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infolist` WHERE `arcrank` = 1 AND `tel` = '" . $results[0]['tel'] . "'");
            $infoDetail['telCount'] = getCache("info", $archives, 300, array("name" => "total"));

            //获取商家共发布多少条信息
            $archives                 = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infolist` WHERE `arcrank` = 1 AND `userid` = '" . $results[0]['userid'] . "'");
            $infoDetail['storeCount'] = getCache("info", $archives, 300, array("name" => "total"));

            // $archives             = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infocommon` WHERE `aid` = " . $results[0]['id'] . " AND `ischeck` = 1 AND `floor` = 0");
            $archives = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'info-detail' AND `aid` = '$id' AND `pid` = 0");
            $infoDetail['common'] = getCache("info", $archives, 300, array("name" => "total"));

            // $archives = $dsql->SetQuery("SELECT `userid` FROM `#@__infocommon` WHERE `aid` = " . $results[0]['id'] . " AND `ischeck` = 1 AND `floor` = 0");
            $archives = $dsql->SetQuery("SELECT `userid`  FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'info-detail' AND `aid` = '$id' AND `pid` = 0");
            $commons  = $dsql->dsqlOper($archives, "results");
            foreach ($commons as &$common) {
                $users = getMemberDetail($common['userid']);

                $common['photo']    = $users['photo'];
                $common['username'] = $users['username'];
            }
            $infoDetail['commons'] = $commons;
            $userLogin_id = $userLogin->getMemberID();
            $is_collected              = $dsql->SetQuery("SELECT `id` FROM `#@__site_followmap` WHERE `userid` = $userLogin_id  AND `userid_b` = " . $results[0]['userid'] . " AND `temp` = 'info'" );
            $is_collected              = $dsql->dsqlOper($is_collected, "results");

            $infoDetail['is_collected'] = is_array($is_collected)&&!empty($is_collected) ? 1 : 0;


            //推荐信息
            $this->param = array('typeid' => $results[0]['typeid']);
            $tj_infos    = $this->ilist_v2();
            if (!empty($tj_infos) && $tj_infos['list']) {
                foreach ($tj_infos['list'] as $k => $info) {
                    if ($results[0]['id'] == $info['id']) {
                        unset($tj_infos['list'][$k]);
                    }
                }
            }

            $infoDetail['tj_infos'] = $tj_infos ? $tj_infos['list'] : [];

            //图表信息
            $archives = $dsql->SetQuery("SELECT `picPath`, `picInfo` FROM `#@__infopic` WHERE `aid` = " . $id . " ORDER BY `id` ASC");
            $results  = $dsql->dsqlOper($archives, "results");


            if (!empty($results)) {
                $imglist = array();
                foreach ($results as $key => $value) {
                    $imglist[$key]["path"]       = getFilePath($value["picPath"]);
                    $imglist[$key]["pathSource"] = $value["picPath"];
                    $imglist[$key]["info"]       = $value["picInfo"];
                }
            } else {
                $imglist = array();
            }

            $infoDetail["imglist"] = $imglist;

        }
        return $infoDetail;
    }


    /**
     * 评论列表
     * @return array
     */
    public function common()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $infoid   = $orderby = $page = $pageSize = $where = "";

        if (!is_array($this->param)) {
            return array("state" => 200, "info" => $langData['info'][1][58]);
        } else {
            $infoid   = $this->param['infoid'];
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

        $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__infocommon` WHERE `aid` = " . $infoid . " AND `ischeck` = 1 AND `floor` = 0" . $oby);
        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount / $pageSize);

        if ($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

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

                // $list[$key]['lower'] = $this->getCommonList($val['id']);
                $lower = null;
                $param['fid'] = $val['id'];
                $param['page'] = 1;
                $param['pageSize'] = 100;
                $this->param = $param;
                $child = $this->getCommonList();

                if(!isset($child['state']) || $child['state'] != 200){
                    $lower = $child['list'];
                }

                $list[$key]['lower'] = $lower;
            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);
    }


    /**
     * 遍历评论子级
     * @param $fid int 评论ID
     * @return array
     */
    function getCommonList()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        // if (empty($fid)) return false;
        $param    = $this->param;
        $fid      = (int)$param['fid'];
        $page     = (int)$this->param['page'];
        $pageSize = (int)$this->param['pageSize'];

        $pageSize = empty($pageSize) ? 99999 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if($fid){
            $where = " AND `floor` = '$fid'";
        }

        $where .= " AND `ischeck` = 1";

        $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__infocommon` WHERE `floor` = " . $fid . " AND `ischeck` = 1 ORDER BY `id` DESC");
        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

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

                    // $list[$key]['lower'] = $this->getCommonList($val['id']);
                    $lower = null;
                    $param['fid'] = $val['id'];
                    $param['page'] = 1;
                    $param['pageSize'] = 100;
                    $this->param = $param;
                    $child = $this->getCommonList();
                    if(!isset($child['state']) || $child['state'] != 200){
                        $lower = $child['list'];
                    }

                    $list[$key]['lower'] = $lower;
                }
                // return $list;
                return array("pageInfo" => $pageinfo, "list" => $list);
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
        global $langData;

        $id = $this->param['id'];
        if (empty($id)) return $langData['info'][1][74];//"请传递评论ID！"
        $memberID = $userLogin->getMemberID();
        if ($memberID == -1 || empty($memberID)) return $langData['info'][1][75];//请先登录！

        $archives = $dsql->SetQuery("SELECT `duser` FROM `#@__infocommon` WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archives, "results");
        if ($results) {

            $duser = $results[0]['duser'];

            //如果此会员已经顶过则return
            $userArr = explode(",", $duser);
            if (in_array($userLogin->getMemberID(), $userArr)) return $langData['info'][1][76];//已顶过！

            //附加会员ID
            if (empty($duser)) {
                $nuser = $userLogin->getMemberID();
            } else {
                $nuser = $duser . "," . $userLogin->getMemberID();
            }

            $archives = $dsql->SetQuery("UPDATE `#@__infocommon` SET `good` = `good` + 1 WHERE `id` = " . $id);
            $results  = $dsql->dsqlOper($archives, "update");

            $archives = $dsql->SetQuery("UPDATE `#@__infocommon` SET `duser` = '$nuser' WHERE `id` = " . $id);
            $results  = $dsql->dsqlOper($archives, "update");
            return $results;

        } else {
            return $langData['info'][1][77];//评论不存在或已删除！
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
        global $langData;
        $param = $this->param;

        $aid     = $param['aid'];
        $id      = $param['id'];
        $content = addslashes($param['content']);

        if (empty($aid) || empty($content)) {
            return array("state" => 200, "info" => $langData['info'][1][78]);//'必填项不得为空！'
        }

        $content = filterSensitiveWords(cn_substrR($content, 250));

        include HUONIAOINC . "/config/info.inc.php";
        $state = (int)$customCommentCheck;

        $archives = $dsql->SetQuery("INSERT INTO `#@__infocommon` (`aid`, `floor`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `ischeck`, `duser`) VALUES ('$aid', '$id', '" . $userLogin->getMemberID() . "', '$content', " . GetMkTime(time()) . ", '" . GetIP() . "', '" . getIpAddr(GetIP()) . "', 0, 0, '$state', '')");
        $lid      = $dsql->dsqlOper($archives, "lastid");
        if ($lid) {
            $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__infocommon` WHERE `id` = " . $lid);
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
            return array("state" => 200, "info" => $langData['info'][1][79]);//'评论失败！'
        }

    }

    /**
     * 评价详情
     */
    public function commentDetail(){
        global $dsql;
        global $userLogin;

        $uid = $userLogin->getMemberID();

        $param = $this->param;
        $id    = (int)$param['id'];

        $sql = $dsql->SetQuery("SELECT * FROM `#@__infocommon` WHERE `id` = $id AND `isCheck` = 1 ");//print_R($sql);exit;
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $detail = array();
            $zan_has = 0;
            $ret = $ret[0];
            foreach ($ret as $key => $value) {

                //获取父级内容
                if($key == "floor"){
                    if($value){
                        $content  = '';
                        $username = '';
                        $sql = $dsql->SetQuery("SELECT `content`, `userid` FROM `#@__infocommon` WHERE `id` = '$value' AND `isCheck` = 1 ");
                        $par = $dsql->dsqlOper($sql, "results");
                        if($par){
                            $content = $par[0]['content'];

                            $sql = $dsql->SetQuery("SELECT `id`, `mtype`, `nickname`, `company` FROM `#@__member` WHERE `id` IN (".$par[0]['userid'].")");
                            $res = $dsql->dsqlOper($sql, "results");
                            if($res[0]['mtype'] == 2){
                                $username = $res[0]['company'] ? $res[0]['company'] : $res[0]['nickname'];
                            }else{
                                $username = $res[0]['nickname'];
                            }
                        }
                        $detail['parcontent'] = $content;
                        $detail['parusername'] = $username;
                    }
                }

                if($key == "duser"){
                    $zan_userArr = array();
                    if($value){
                        $sql = $dsql->SetQuery("SELECT `id`, `mtype`, `nickname`, `company`, `photo` FROM `#@__member` WHERE `id` IN (".$value.")");
                        $res = $dsql->dsqlOper($sql, "results");
                        if($res){
                            $value_ = explode(",", $value);
                            if($uid != -1 && in_array($uid, $value_)){
                                $zan_has = 1;
                            }
                            foreach ($value_ as $k => $v) {
                                foreach ($res as $s => $sv) {
                                    if($sv['id'] == $v){
                                        if($sv['mtype'] == "2"){
                                            $nickname = $sv['company'] ? $sv['company'] : $sv['nickname'];
                                        }else{
                                            $nickname = $sv['nickname'];
                                        }
                                        $photo = $sv['photo'] ? getFilePath($sv['photo']) : "";
                                        $zan_userArr[] = array(
                                            "id" => $v,
                                            "nickname" => $nickname,
                                            "photo" => $photo
                                        );
                                    }
                                }
                            }
                        }
                    }
                    $detail['zan_userArr'] = $zan_userArr;
                }

                $detail[$key] = $value;
            }

            $detail['zan_has'] = $zan_has;

            if($ret['isanony']){
                $detail['user'] = array(
                    "id" => 0,
                    "nickname" => "匿名用户",
                    "photo" => ""
                );
            }else{
                $sql = $dsql->SetQuery("SELECT `id`, `mtype`, `nickname`, `company`, `photo` FROM `#@__member` WHERE `id` = " . $ret['userid']);
                $res = $dsql->dsqlOper($sql, "results");
                if(!empty($res[0]['id'])){
                    if($res[0]['mtype'] == "2"){
                        $nickname = $res[0]['company'] ? $res[0]['company'] : $res[0]['nickname'];
                    }else{
                        $nickname = $res[0]['nickname'];
                    }
                    $photo = $res[0]['photo'] ? getFilePath($res[0]['photo']) : "";
                    $userinfo= array(
                        "id" => $res[0]['id'],
                        "nickname" => $nickname,
                        "photo" => $photo
                    );
                }
                $detail['user'] = $userinfo;
            }
            return $detail;
        }
    }


    /**
     * 发布信息
     * @return array
     */
    public function put()
    {
        global $dsql;
        global $userLogin;
        global $langData;

        $param = $this->param;

        $typeid  = $param['typeid'];
        $title   = filterSensitiveWords(addslashes($param['title']));
        $addr    = (int)$param['addr'];
        $cityid  = (int)$param['cityid'];
        $price   = (float)$param['price'];
        $person  = filterSensitiveWords(addslashes($param['person']));
        $qq      = filterSensitiveWords($param['qq']);
        $tel     = filterSensitiveWords($param['tel']);
        $valid   = $param['valid'];
        $body    = filterSensitiveWords($param['body'], false);
        $imglist = $param['imglist'];
        $valid   = $param['valid'];
        $video   = $param['video'];
        $longitude = $param['longitude'];
        $latitude = $param['latitude'];
        $yunfei   = (float)$param['yunfei'];
        $price_switch   = (int)$param['price_switch'];

        if (empty($typeid)) return array("state" => 200, "info" => $langData['info'][1][80]);//'分类传递失败'
        if (empty($title)) return array("state" => 200, "info" => $langData['info'][1][81]);//'标题不得为空'

        $title = cn_substrR($title, 50);

        include HUONIAOINC . "/config/info.inc.php";
        $arcrank = (int)$customFabuCheck;

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if ($uid == -1) {
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
        }

        //用户信息
        $userinfo = $userLogin->getMemberInfo();

        // 需要支付费用
        $amount = 0;

        // 是否独立支付 普通会员或者付费会员超出限制
        $alonepay = 0;

        $alreadyFabu = 0; // 付费会员当天已免费发布数量

        //权限验证
        if ($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "info"))) {
            return array("state" => 200, "info" => $langData['info'][1][60]);//'商家权限验证失败！'
        }

        //企业会员或已经升级为收费会员的状态才可以发布 --> 普通会员也可发布
        if ($userinfo['userType'] == 1) {

            $toMax = false;

            if ($userinfo['level']) {

                $memberLevelAuth = getMemberLevelAuth($userinfo['level']);
                $infoCount       = (int)$memberLevelAuth['info'];

                //统计用户当天已发布数量 @
                $today    = GetMkTime(date("Y-m-d", time()));
                $tomorrow = GetMkTime(date("Y-m-d", strtotime("+1 day")));
                $sql      = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__infolist` WHERE `userid` = $uid AND `pubdate` >= $today AND `pubdate` < $tomorrow AND `alonepay` = 0 AND `waitpay` = 0");
                $ret      = $dsql->dsqlOper($sql, "results");
                if ($ret) {
                    $alreadyFabu = $ret[0]['total'];
                    if ($alreadyFabu >= $infoCount) {
                        $toMax = true;
                        // return array("state" => 200, "info" => $langData['info'][1][82]);//'当天发布信息数量已达等级上限！'
                    } else {
                        $arcrank = 1;
                    }
                }
            }

            // 普通会员或者付费会员当天发布数量达上限
            if ($userinfo['level'] == 0 || $toMax) {

                $alonepay = 1;

                global $cfg_fabuAmount;
                $fabuAmount = $cfg_fabuAmount ? unserialize($cfg_fabuAmount) : array();

                if ($fabuAmount) {
                    $amount = $fabuAmount["info"];
                } else {
                    $amount = 0;
                }

            }

        }

        //获取分类下相应字段
        $infoitem    = $dsql->SetQuery("SELECT * FROM `#@__infotypeitem` WHERE `tid` = " . $typeid . " ORDER BY `orderby` DESC");
        $itemResults = $dsql->dsqlOper($infoitem, "results");

        //验证字段内容
        if (count($itemResults) > 0) {
            foreach ($itemResults as $key => $value) {
                if ($value["required"] == 1 && $param[$value["field"]] == "") {
                    if ($value["formtype"] == "text") {
                        return array("state" => 200, "info" => $value['title'] . $langData['info'][1][83]);//不能为空
                    } else {
                        return array("state" => 200, "info" => $langData['info'][1][84] . $value['title']);//请选择
                    }
                }
            }
        }

        if (empty($addr)) return array("state" => 200, "info" => $langData['info'][1][85]);//请选择所在区域
        if (empty($person)) return array("state" => 200, "info" => $langData['info'][1][86]);//请输入联系人
        if (empty($tel)) return array("state" => 200, "info" => $langData['info'][1][87]);//请输入手机号码
        if (empty($valid)) return array("state" => 200, "info" => $langData['info'][1][88]);//请选择有效期

        $person = cn_substrR($person, 6);
        $tel    = cn_substrR($tel, 11);

        $ip     = GetIP();
        $ipAddr = getIpAddr($ip);

        $teladdr = getTelAddr($tel);
        $valid   = GetMkTime($valid);

        $yunfei = $yunfei ? $yunfei : 0;
        $price = $price ? $price : 0;
        //保存到主表
        $waitpay  = $amount > 0 ? 1 : 0;
        $archives = $dsql->SetQuery("INSERT INTO `#@__infolist` (`cityid`, `typeid`, `title`, `valid`, `addr`, `price`, `body`, `person`, `tel`, `teladdr`, `qq`, `ip`, `ipaddr`, `pubdate`, `userid`, `arcrank`, `waitpay`, `alonepay`, `weight`,`video`, `yunfei`, `price_switch`, `longitude`, `latitude`) VALUES ('$cityid', '$typeid', '$title', '$valid', '$addr', '$price', '$body', '$person', '$tel', '$teladdr', '$qq', '$ip', '$ipAddr', " . GetMkTime(time()) . ", '$uid', '$arcrank', '$waitpay', '$alonepay', 1,'$video', $yunfei, '$price_switch', '$longitude', '$latitude')");
        $aid      = $dsql->dsqlOper($archives, "lastid");

        if (is_numeric($aid)) {
            //保存字段内容
            if (count($itemResults) > 0) {
                foreach ($itemResults as $key => $value) {
                    $val = $param[$value['field']];
                    if ($value['formtype'] == "checkbox") {
                        $val = join(",", $val);
                    }
                    $val      = filterSensitiveWords($val);
                    $infoitem = $dsql->SetQuery("INSERT INTO `#@__infoitem` (`aid`, `iid`, `value`) VALUES (" . $aid . ", " . $value['id'] . ", '" . $val . "')");
                    $dsql->dsqlOper($infoitem, "update");
                }
            }

            //保存图集表
            if ($imglist != "") {
                $picList = explode(",", $imglist);
                foreach ($picList as $k => $v) {
                    $picInfo = explode("|", $v);
                    $pics    = $dsql->SetQuery("INSERT INTO `#@__infopic` (`aid`, `picPath`, `picInfo`) VALUES (" . $aid . ", '" . $picInfo[0] . "', '" . filterSensitiveWords($picInfo[1]) . "')");
                    $dsql->dsqlOper($pics, "update");
                }
            }

            //后台消息通知
            updateAdminNotice("info", "detail");

            if ($userinfo['level']) {
                $auth = array("level" => $userinfo['level'], "levelname" => $userinfo['levelName'], "alreadycount" => $alreadyFabu, "maxcount" => $infoCount);
            } else {
                $auth = array("level" => 0, "levelname" => $langData['info'][1][89], "maxcount" => 0);//普通会员
            }

            if($arcrank){
                updateCache("info_list", 300);
            }

            return array("auth" => $auth, "aid" => $aid, "amount" => $amount);
            // return $aid;

        } else {

            return array("state" => 101, "info" => $langData['info'][1][90]);//发布到数据时发生错误，请检查字段内容！

        }

    }


    /**
     * 修改信息
     * @return array
     */
    public function edit()
    {
        global $dsql;
        global $userLogin;
        global $langData;

        $param = $this->param;

        $id = $param['id'];

        if (empty($id)) return array("state" => 200, "info" => $langData['info'][1][91]);//'数据传递失败！'

        $typeid  = $param['typeid'];
        $title   = filterSensitiveWords(addslashes($param['title']));
        $addr    = (int)$param['addr'];
        $cityid  = (int)$param['cityid'];
        $price   = (int)$param['price'];
        $person  = filterSensitiveWords(addslashes($param['person']));
        $qq      = filterSensitiveWords($param['qq']);
        $tel     = filterSensitiveWords($param['tel']);
        $valid   = $param['valid'];
        $body    = filterSensitiveWords($param['body'], false);
        $imglist = $param['imglist'];
        $valid   = $param['valid'];
        $video   = $param['video'];
        $longitude = $param['longitude'];
        $latitude = $param['latitude'];
        $price_switch   = (int)$param['price_switch'];
        $yunfei  = (float)$param['yunfei'];
        $yunfei = $yunfei ? $yunfei : 0;

        if (empty($typeid)) return array("state" => 200, "info" => $langData['info'][1][80]);//'分类传递失败'
        if (empty($title)) return array("state" => 200, "info" => $langData['info'][1][81]);//'标题不得为空'

        $title = cn_substrR($title, 50);

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if ($uid == -1) {
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//'登录超时，请重新登录！'
        }

        $userinfo = $userLogin->getMemberInfo();

        //权限验证
        if ($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "info"))) {
            return array("state" => 200, "info" => $langData['info'][1][50]);//'商家权限验证失败！'
        }

        $archives = $dsql->SetQuery("SELECT `id` FROM `#@__infolist` WHERE `id` = " . $id . " AND `userid` = " . $uid);
        $results  = $dsql->dsqlOper($archives, "results");
        if (!$results) {
            return array("state" => 200, "info" => $langData['info'][1][82]);//'权限不足，修改失败！'
        }

        //获取分类下相应字段
        $infoitem    = $dsql->SetQuery("SELECT * FROM `#@__infotypeitem` WHERE `tid` = " . $typeid . " ORDER BY `orderby` DESC");
        $itemResults = $dsql->dsqlOper($infoitem, "results");

        //验证字段内容
        if (count($itemResults) > 0) {
            foreach ($itemResults as $key => $value) {
                if ($value["required"] == 1 && $param[$value["field"]] == "") {
                    if ($value["formtype"] == "text") {
                        return array("state" => 200, "info" => $value['title'] . $langData['info'][1][83]);//不能为空
                    } else {
                        return array("state" => 200, "info" => $langData['info'][1][84] . $value['title']);//请选择
                    }
                }
            }
        }

        if (empty($addr)) return array("state" => 200, "info" => $langData['info'][1][85]);//请选择所在区域
        if (empty($person)) return array("state" => 200, "info" => $langData['info'][1][86]);//请输入联系人
        if (empty($tel)) return array("state" => 200, "info" => $langData['info'][1][87]);//请输入手机号码
        if (empty($valid)) return array("state" => 200, "info" => $langData['info'][1][88]);//请选择有效期

        $person = cn_substrR($person, 6);
        $tel    = cn_substrR($tel, 11);

        $ip     = GetIP();
        $ipAddr = getIpAddr($ip);

        include HUONIAOINC . "/config/info.inc.php";
        $state = (int)$customFabuCheck;

        $teladdr = getTelAddr($tel);
        $valid   = GetMkTime($valid);

        //保存到主表
        $archives = $dsql->SetQuery("UPDATE `#@__infolist` SET `cityid` = '" . $cityid . "', `title` = '" . $title . "', `valid` = " . $valid . ", `addr` = " . $addr . ", `price` = " . $price . ", `body` = '" . $body . "', `person` = '" . $person . "', `tel` = '" . $tel . "', `teladdr` = '" . $teladdr . "', `qq` = '" . $qq . "', `arcrank` = '$state',`video`='$video', `price_switch`='$price_switch', `yunfei`='$yunfei', `longitude` = '$longitude', `latitude` = '$latitude' WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archives, "update");

        if ($results != "ok") {
            return array("state" => 200, "info" => $langData['info'][1][93]);//保存到数据时发生错误，请检查字段内容！
        }

        //先删除信息所属字段
        $archives = $dsql->SetQuery("DELETE FROM `#@__infoitem` WHERE `aid` = " . $id);
        $results  = $dsql->dsqlOper($archives, "update");

        //保存字段内容
        if (count($itemResults) > 0) {
            foreach ($itemResults as $key => $value) {

                $val = $_POST[$value['field']];
                if ($value['formtype'] == "checkbox") {
                    $val = join(",", $val);
                }
                $val      = filterSensitiveWords($val);
                $infoitem = $dsql->SetQuery("INSERT INTO `#@__infoitem` (`aid`, `iid`, `value`) VALUES (" . $id . ", " . $value['id'] . ", '" . $val . "')");
                $dsql->dsqlOper($infoitem, "update");
            }
        }

        //先删除信息所属图集
        $archives = $dsql->SetQuery("DELETE FROM `#@__infopic` WHERE `aid` = " . $id);
        $results  = $dsql->dsqlOper($archives, "update");

        //保存图集表
        if ($imglist != "") {
            $picList = explode(",", $imglist);
            foreach ($picList as $k => $v) {
                $picInfo = explode("|", $v);
                $pics    = $dsql->SetQuery("INSERT INTO `#@__infopic` (`aid`, `picPath`, `picInfo`) VALUES (" . $id . ", '" . $picInfo[0] . "', '" . filterSensitiveWords($picInfo[1]) . "')");
                $dsql->dsqlOper($pics, "update");
            }
        }

        //后台消息通知
        updateAdminNotice("info", "detail");

        // 清除缓存
        clearCache("info_detail", $id);
        clearCache("info_total", "key");
        checkCache("info_list", $id);

        return $langData['info'][1][94];//修改成功！

    }


    /**
     * 删除信息
     * @return array
     */
    public function del()
    {
        global $dsql;
        global $userLogin;
        global $langData;

        $id = $this->param['id'];

        if (!is_numeric($id)) return array("state" => 200, "info" => $langData['info'][1][58]);

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if ($uid == -1) {
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
        }

        $archives = $dsql->SetQuery("SELECT * FROM `#@__infolist` WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archives, "results");
        if ($results) {
            $results = $results[0];
            if ($results['userid'] == $uid) {

                //如果已经竞价，不可以删除
                if ($results['isbid'] == 1) {
                    return array("state" => 101, "info" => $langData['info'][1][95]);//竞价状态的信息不可以删除！
                }

                //删除评论
                $archives = $dsql->SetQuery("DELETE FROM `#@__public_comment` WHERE `type` = 'tieba-detail' AND `aid` = " . $id);
                $dsql->dsqlOper($archives, "update");

                $archives = $dsql->SetQuery("SELECT * FROM `#@__infolist` WHERE `id` = " . $id);
                $results  = $dsql->dsqlOper($archives, "results");

                //删除缩略图
                delPicFile($results[0]['litpic'], "delThumb", "info");

                //删除视频
                delPicFile($results[0]['video'], "delVideo", "info");

                $body = $results[0]['body'];
                if (!empty($body)) {
                    delEditorPic($body, "info");
                }

                //删除图集
                $archives = $dsql->SetQuery("SELECT `picPath` FROM `#@__infopic` WHERE `aid` = " . $id);
                $results  = $dsql->dsqlOper($archives, "results");

                //删除图片文件
                if (!empty($results)) {
                    $atlasPic = "";
                    foreach ($results as $key => $value) {
                        $atlasPic .= $value['picPath'] . ",";
                    }
                    delPicFile(substr($atlasPic, 0, strlen($atlasPic) - 1), "delAtlas", "info");
                }

                $archives = $dsql->SetQuery("DELETE FROM `#@__infopic` WHERE `aid` = " . $id);
                $dsql->dsqlOper($archives, "update");

                //删除字段
                $archives = $dsql->SetQuery("DELETE FROM `#@__infoitem` WHERE `aid` = " . $id);
                $dsql->dsqlOper($archives, "update");

                //删除表
                $archives = $dsql->SetQuery("DELETE FROM `#@__infolist` WHERE `id` = " . $id);
                $dsql->dsqlOper($archives, "update");

                // 清除缓存
                checkCache("info_list", $id);
                clearCache("info_total", "key");
                clearCache("info_detail", $id);

                return array("state" => 100, "info" => $langData['info'][1][96]);//删除成功！
            } else {
                return array("state" => 101, "info" => $langData['info'][1][97]);//权限不足，请确认帐户信息后再进行操作！
            }
        } else {
            return array("state" => 101, "info" => $langData['info'][1][98]);//信息不存在，或已经删除！
        }

    }


    /**
     * 验证信息状态是否可以竞价
     * @return array
     */
    public function checkBidState()
    {
        global $dsql;
        global $userLogin;
        global $langData;

        $aid = $this->param['aid'];

        if (!is_numeric($aid)) return array("state" => 200, "info" => $langData['info'][1][58]);

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if ($uid == -1) {
            return array("state" => 101, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
        }

        $archives = $dsql->SetQuery("SELECT `arcrank`, `isbid`, `userid`, `bid_price`, `bid_end`, `valid` FROM `#@__infolist` WHERE `id` = " . $aid);
        $results  = $dsql->dsqlOper($archives, "results");
        if ($results) {
            //已过期的不可以竞价
            $now = GetMkTime(time());
            if ($results[0]['valid'] == 0 || $results[0]['valid'] < $now) {
                return array("state" => 200, "info" => $langData['info'][1][99]);//过期的信息不可以竞价！！
            }
            if ($results[0]['userid'] != $uid) {
                return array("state" => 200, "info" => $langData['info'][1][100]);//您走错地方了吧，只能竞价自己发布的信息哦！
            } elseif ($results[0]['arcrank'] != 1) {
                return array("state" => 200, "info" => $langData['info'][2][0]);//只有已审核的信息才可以竞价！
            } elseif ($results[0]['isbid'] == 1) {
                //已经竞价
                return array('isbid' => 1, 'bid_price' => $results[0]['bid_price'], 'bid_end' => $results[0]['bid_end'], 'now' => GetMkTime(time()));
            } else {
                return 'true';
            }
        } else {
            return array("state" => 200, "info" => $langData['info'][2][1]);//信息不存在，或已经删除，不可以竞价，请确认后重试！
        }

    }


    /**
     * 竞价
     * @return array
     */
    public function bid()
    {
        global $dsql;
        global $userLogin;
        global $cfg_secureAccess;
        global $cfg_basehost;
        global $langData;

        $param   = $this->param;
        $aid     = $param['aid'];           //信息ID
        $price   = (float)$param['price'];  //每日预算
        $day     = (int)$param['day'];      //竞价时长
        $paytype = $param['paytype'];       //支付方式

        $amount = $price * $day;  //总费用

        $uid = $userLogin->getMemberID();  //当前登录用户
        if ($uid == -1) {
            header("location:" . $cfg_secureAccess . $cfg_basehost . "/login.html");
            die;
        }

        //信息url
        $param = array(
            "service" => "member",
            "type" => "user",
            "template" => "manage",
            "module" => "info"
        );
        $url   = getUrlPath($param);

        //验证金额
        if ($amount <= 0 || !is_numeric($aid) || empty($paytype)) {
            header("location:" . $url);
            die;
        }

        //查询信息
        $sql = $dsql->SetQuery("SELECT `arcrank`, `isbid`, `userid`, `valid` FROM `#@__infolist` WHERE `id` = " . $aid);
        $ret = $dsql->dsqlOper($sql, "results");
        if (!$ret) {
            //信息不存在
            header("location:" . $url);
            die;
        }
        $userid = $ret[0]['userid'];

        //没有审核的信息不可以竞价
        if ($ret[0]['arcrank'] != 1) {
            header("location:" . $url);
            die;
        }

        //已过期的不可以竞价
        $now = GetMkTime(time());
        if ($ret[0]['valid'] == 0 || $ret[0]['valid'] < $now) {
            header("location:" . $url);
            die;
        }

        //已经竞价的，不可以再提交
        if ($ret[0]['isbid'] == 1) {
            header("location:" . $url);
            die;
        }

        //只能给自己发布的信息竞价
        if ($userid != $uid) {
            header("location:" . $url);
            die;
        }

        //价格或时长验证
        if (empty($price) || empty($day)) {
            header("location:" . $url);
            die;
        }

        //订单号
        $ordernum = create_ordernum();

        //当前时间
        $start = GetMkTime(time());
        $end   = $start + $day * 24 * 3600;

        $archives = $dsql->SetQuery("INSERT INTO `#@__member_bid` (`ordernum`, `module`, `part`, `uid`, `aid`, `start`, `end`, `price`, `state`) VALUES ('$ordernum', 'info', 'detail', '$uid', '$aid', '$start', '$end', '$price', 0)");
        $return   = $dsql->dsqlOper($archives, "update");
        if ($return != "ok") {
            die($langData['info'][2][2]);//提交失败，请稍候重试！
        }

        //跳转至第三方支付页面
        createPayForm("info", $ordernum, $amount, $paytype, $langData['info'][2][4]);//分类信息竞价

    }


    /**
     * 竞价加价
     * @return array
     */
    public function bidIncrease()
    {
        global $dsql;
        global $userLogin;
        global $cfg_secureAccess;
        global $cfg_basehost;
        global $langData;

        $param   = $this->param;
        $aid     = $param['aid'];           //信息ID
        $price   = (float)$param['price'];  //每日预算
        $paytype = $param['paytype'];       //支付方式

        $uid = $userLogin->getMemberID();  //当前登录用户
        if ($uid == -1) {
            header("location:" . $cfg_secureAccess . $cfg_basehost . "/login.html");
            die;
        }

        //信息url
        $param = array(
            "service" => "member",
            "type" => "user",
            "template" => "manage",
            "module" => "info"
        );
        $url   = getUrlPath($param);

        //验证金额
        if (!is_numeric($aid) || empty($paytype)) {
            header("location:" . $url);
            die;
        }

        //查询信息
        $sql = $dsql->SetQuery("SELECT `arcrank`, `isbid`, `userid`, `bid_price`, `bid_start`, `bid_end` FROM `#@__infolist` WHERE `id` = " . $aid);
        $ret = $dsql->dsqlOper($sql, "results");
        if (!$ret) {
            //信息不存在
            header("location:" . $url);
            die;
        }
        $userid = $ret[0]['userid'];

        //没有审核的信息不可以竞价
        if ($ret[0]['arcrank'] != 1) {
            header("location:" . $url);
            die;
        }

        //如果没有参加过竞价，则不可以进行加价操作
        if ($ret[0]['isbid'] != 1) {
            header("location:" . $url);
            die;
        }

        //只能给自己发布的信息竞价
        if ($userid != $uid) {
            header("location:" . $url);
            die;
        }

        //计算剩余竞价天数
        $day = ceil(($ret[0]['bid_end'] - GetMkTime(time())) / 24 / 3600);

        //价格或时长验证
        if (empty($price) || empty($day)) {
            header("location:" . $url);
            die;
        }

        //支付金额
        $amount = $day * $price;

        //订单号
        $ordernum = create_ordernum();

        //当前时间
        $start = $ret[0]['bid_start'];
        $end   = $ret[0]['bid_end'];

        $archives = $dsql->SetQuery("INSERT INTO `#@__member_bid` (`ordernum`, `module`, `part`, `uid`, `aid`, `start`, `end`, `price`, `state`) VALUES ('$ordernum', 'info', 'detail', '$uid', '$aid', '$start', '$end', '$price', 0)");
        $return   = $dsql->dsqlOper($archives, "update");
        if ($return != "ok") {
            die($langData['info'][2][2]);//"提交失败，请稍候重试！"
        }

        //跳转至第三方支付页面
        createPayForm("info", $ordernum, $amount, $paytype, $langData['info'][2][4]);//分类信息竞价加价

    }


    /**
     * 支付成功
     * 此处进行支付成功后的操作，例如发送短信等服务
     *
     */
    public function paySuccess()
    {
        global $cfg_secureAccess;
        global $cfg_basehost;

        $param = $this->param;
        if (!empty($param)) {
            global $dsql;

            $paytype  = $param['paytype'];
            $ordernum = $param['ordernum'];
            $ordertype = $param['ordertype'];
            $date     = GetMkTime(time());

            $sql = $dsql->SetQuery("SELECT * FROM `#@__pay_log` WHERE `ordertype` = 'info' AND `ordernum` = '$ordernum' AND `state` = 1");
            $res = $dsql->dsqlOper($sql, "results");
            if(!$res) return;
            $body = unserialize($res[0]['body']);
            $ordertype = "";
            if($body){
                $ordertype = $body['type'];
            }
            if($ordertype == 'info'){

                //查询订单信息
                $archives = $dsql->SetQuery("SELECT * FROM `#@__info_order` WHERE `ordernum` = '$ordernum' AND `orderstate` = 0");
                $res = $dsql->dsqlOper($archives, "results");
                if(!$res) return;

                //商品
                $prod = $res[0]['prod'];
                //购买用户
                $userid = $res[0]['userid'];
                //订单id
                $orderid = $res[0]['id'];

                $totalPrice = $upoint = $ubalance = 0;

                // 查询商家ID
                $arc = $dsql->SetQuery("SELECT `uid` FROM `#@__infoshop` WHERE `id` = ".$res[0]['store']);
                $storeList = $dsql->dsqlOper($arc, "results");
                $uid       = $storeList[0]['uid'];

                $totalPrice = $res[0]['payprice']; //实际支付
                $upoint     = $res[0]['point']; // 使用的积分
                $ubalance   = $res[0]['balance']; //使用的余额

                //更新订单状态
                $archives = $dsql->SetQuery("UPDATE `#@__info_order` SET `orderstate` = 1, `paytype` = '$paytype', `paydate` = '$date' WHERE `ordernum` = '$ordernum'");
                $dsql->dsqlOper($archives, "update");

                //更新物品状态
                $archives = $dsql->SetQuery("UPDATE `#@__infolist` SET `is_valid` = 1 WHERE `id` = $prod");
                $dsql->dsqlOper($archives, "update");

                // 清除缓存
                checkCache("info_list", $prod);
                clearCache("info_total", "key");
                clearCache("info_detail", $prod);

                //扣除会员积分
                if(!empty($upoint) && $upoint > 0){
                    $archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` - '$upoint' WHERE `id` = '$userid'");
                    $dsql->dsqlOper($archives, "update");
                    //保存操作日志
                    $info = '支付二手订单';
                    $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$upoint', '$info', '$date')");
                    $dsql->dsqlOper($archives, "update");
                }

                //扣除会员余额
                if(!empty($ubalance) && $ubalance > 0){
                    $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$ubalance' WHERE `id` = '$userid'");
                    $dsql->dsqlOper($archives, "update");
                    $totalPrice += $ubalance;
                }

                //增加冻结金额
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` + '$totalPrice' WHERE `id` = '$userid'");
                $dsql->dsqlOper($archives, "update");

                //保存操作日志
                $info = '二手消费';
                $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$totalPrice', '$info', '$date')");
                $dsql->dsqlOper($archives, "update");

                //支付成功，会员消息通知
                $paramUser = array(
                    "service"  => "member",
                    "type"     => "user",
                    "template" => "orderdetail",
                    "action"   => "info",
                    "id"       => $orderid
                );

                $paramBusi = array(
                    "service"  => "member",
                    "template" => "orderdetail",
                    "action"   => "info",
                    "id"       => $orderid
                );

                //获取会员名
                $username = "";
                $sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $userid");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
                }

                //自定义配置
                $config = array(
                    "username" => $username,
                    "order" => $ordernum,
                    "amount" => $totalPrice,
                    "title" => "二手订单",
                    "fields" => array(
                        'keyword1' => '商品信息',
                        'keyword2' => '订单金额',
                        'keyword3' => '订单状态'
                    )
                );

                updateMemberNotice($userid, "会员-订单支付成功", $paramUser, $config);

                //获取会员名
                $username = "";
                if($uid){
                    $sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $uid");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
                    }
                }else{
                    $username = '先生/女士';
                }

                //自定义配置
                $config = array(
                    "username" => $username,
                    "title" => "二手订单",
                    "order" => $ordernum,
                    "amount" => $totalPrice,
                    "date" => date("Y-m-d H:i:s", time()),
                    "fields" => array(
                        'keyword1' => '订单编号',
                        'keyword2' => '商品名称',
                        'keyword3' => '订单金额',
                        'keyword4' => '付款状态',
                        'keyword5' => '付款时间'
                    )
                );

                updateMemberNotice($uid, "会员-商家新订单通知", $paramBusi, $config);

                $param = array(
                    "service" => "member",
                    "template" => "orderdetail",
                    "module" => "info",
                    "type" => "user",
                    "id" => $orderid
                );
                $url   = getUrlPath($param);
                return $url;

            }else{
                //竞价订单
                //查询订单信息
                $sql = $dsql->SetQuery("SELECT * FROM `#@__member_bid` WHERE `ordernum` = '$ordernum' AND `state` = 0");
                $ret = $dsql->dsqlOper($sql, "results");


                if ($ret) {

                    $bid   = $ret[0]['id'];
                    $uid   = $ret[0]['uid'];
                    $aid   = $ret[0]['aid'];
                    $start = $ret[0]['start'];
                    $end   = $ret[0]['end'];
                    $price = $ret[0]['price'];

                    //总价 = (结束时间 - 开始时间) 得到天数 * 每日预算
                    $day    = ($end - $start) / 24 / 3600;
                    $amount = $day * $price;

                    //信息
                    $sql       = $dsql->SetQuery("SELECT `title`, `isbid`, `bid_price` FROM `#@__infolist` WHERE `id` = $aid");
                    $ret       = $dsql->dsqlOper($sql, "results");
                    $title     = $ret[0]['title'];
                    $isbid     = $ret[0]['isbid'];
                    $bid_price = $ret[0]['bid_price'];

                    //更新订单状态
                    $sql = $dsql->SetQuery("UPDATE `#@__member_bid` SET `state` = 1 WHERE `id` = " . $bid);
                    $dsql->dsqlOper($sql, "update");

                    $currency = echoCurrency(array("type" => "short"));

                    //加价
                    if ($isbid == 1) {

                        $title = '加价，每天增加预算' . $price . $currency . '：<a href="' . $cfg_secureAccess . $cfg_basehost . '/index.php?service=info&template=detail&id=' . $aid . '" target="_blank">' . $title . '</a>';

                        //更新信息竞价状态
                        $sql = $dsql->SetQuery("UPDATE `#@__infolist` SET `bid_price` = `bid_price` + '$price' WHERE `id` = " . $aid);
                        $dsql->dsqlOper($sql, "update");

                        //保存操作日志
                        $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '0', '$amount', '信息竞价$title', '$date')");
                        $dsql->dsqlOper($archives, "update");


                        //竞价
                    } else {

                        $title = $day . '天，每天预算' . $price . $currency . '，结束时间：' . date("Y-m-d H:i:s", $end) . '：<a href="' . $cfg_secureAccess . $cfg_basehost . '/index.php?service=info&template=detail&id=' . $aid . '" target="_blank">' . $title . '</a>';

                        //更新信息竞价状态
                        $sql = $dsql->SetQuery("UPDATE `#@__infolist` SET `isbid` = 1, `bid_price` = '$price', `bid_start` = '$start', `bid_end` = '$end' WHERE `id` = " . $aid);
                        $dsql->dsqlOper($sql, "update");

                        //保存操作日志
                        $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '0', '$amount', '信息竞价$title', '$date')");
                        $dsql->dsqlOper($archives, "update");

                    }

                    // 清除缓存
                    clearCache("info_list", "key");
                    clearCache("info_detail", $aid);
                }
            }

        }
    }

    /**
     * 主页滚动信息
     * @return json
     */
    public function indexInfo()
    {
        global $dsql;
        $param = $this->param;
        $sql   = $dsql->SetQuery("SELECT * FROm `#@__infolist`  WHERE `arcrank` = 1 ORDER BY `id` DESC LIMIT 6");
        $ret   = $dsql->dsqlOper($sql, "results");
        foreach ($ret as $key => $item) {

            $param            = array(
                "service" => "info",
                "template" => "detail",
                "id" => $item['id']
            );
            $urlParam         = getUrlPath($param);
            $ret[$key]['url'] = $urlParam;
        }
        if (!empty($ret)) {
            return $ret;
        }
    }

    /**
     * 获取指定用户发布的信息
     */
    public function getUserHomeList()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $param = $this->param;
        if (!empty($param)) {
            $userid   = $param['userid'];
            $keywords = $param['keywords'];
        } else {
            return array('state' => 200, 'info' => $langData['info'][1][58]);
        }
        $where = '';
        if ($keywords) {
            $where .= " AND `title` LIKE '%$keywords%'";
        }

        $where .= " AND `arcrank` = 1 AND `waitpay` = 0 AND `is_valid` = 0";

        $sql      = $dsql->SetQuery("SELECT * FROM `#@__infolist` WHERE `userid` = $userid" . $where . " ORDER BY `id` DESC");
        $list     = $dsql->dsqlOper($sql, "results");
        $param    = array(
            "service" => "info",
            "template" => "detail",
            "id" => "%id%"
        );
        $urlParam = getUrlPath($param);
        if (!empty($list)) {

            foreach ($list as $key => $val) {

                $list[$key]['id']    = $val['id'];
                $list[$key]['title'] = $val['title'];
                $list[$key]['color'] = $val['color'];
                $list[$key]['price'] = $val['price'];
                $list[$key]['video'] = $val['video'] ? getFilePath($val['video']) : '';



                //会员发布信息统计
                $archives                = $dsql->SetQuery("SELECT `id` FROM `#@__infolist` WHERE `is_valid` = 0 AND `arcrank` = 1 AND `waitpay` = 0 AND `userid` = " . $val['userid']);
                $results                 = $dsql->dsqlOper($archives, "totalCount");
                $list[$key]['fabuCount'] = $results;

                global $data;
                $data                  = "";
                $addrArr               = getParentArr("site_area", $val['addr']);
                $addrArr               = array_reverse(parent_foreach($addrArr, "typename"));
                $list[$key]['address'] = $addrArr;

                $list[$key]['typeid'] = $val['typeid'];
                $archives             = $dsql->SetQuery("SELECT `typename` FROM `#@__infotype` WHERE `id` = " . $val['typeid']);
                $typename             = $dsql->dsqlOper($archives, "results");
                if ($typename) {
                    $list[$key]['typename'] = $typename[0]['typename'];
                } else {
                    $list[$key]['typename'] = "";
                }

                $list[$key]['tel']     = $val['tel'];
                $list[$key]['teladdr'] = $val['teladdr'];

                $list[$key]['click'] = $val['click'];

                $list[$key]['pubdate']  = $val['pubdate'];
                $list[$key]['pubdate1'] = FloorTime(GetMkTime(time()) - $val['pubdate'], 3);
                $list[$key]['fire']     = $val['fire'];
                $list[$key]['rec']      = $val['rec'];
                $list[$key]['top']      = $val['top'];
                if ($val['top']) {
                    $list[$key]["rec_fire_top"] = 'top';
                }

                $list[$key]['isbid']   = $val['isbid'];
                $list[$key]['valid']   = $val['valid'];
                $list[$key]['isvalid'] = ($val['valid'] != 0 && $val['valid'] > $now) ? 0 : 1;

                $list[$key]['typeurl'] = str_replace("%id%", $val['typeid'], $typeurlParam);
                $list[$key]['url']     = str_replace("%id%", $val['id'], $urlParam);

                //图集信息
                $archives = $dsql->SetQuery("SELECT `picPath` FROM `#@__infopic` WHERE `aid` = " . $val['id'] . " ORDER BY `id` ASC LIMIT 0, 1");
                $results  = $dsql->dsqlOper($archives, "results");
                if (!empty($results)) {
                    $list[$key]['litpic'] = getFilePath($results[0]["picPath"]);
                }

                $archives             = $dsql->SetQuery("SELECT `id` FROM `#@__infopic` WHERE `aid` = " . $val['id']);
                $results              = $dsql->dsqlOper($archives, "totalCount");
                $list[$key]['pcount'] = $results;

                $list[$key]['desc'] = cn_substrR(strip_tags($val['body']), 80);

                $archives             = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `type` = 'tieba-detail' AND `aid` = " . $val['id'] . " AND `ischeck` = 1 AND `pid` = 0");
                $totalCount           = $dsql->dsqlOper($archives, "totalCount");
                $list[$key]['common'] = $totalCount;

            }
        }
        return $list;
    }

    /**
     * 商家店铺列表
     */
    public function shopList()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $param = $this->param;
        if (!empty($param)) {
            $id       = $param['id'];
            $orderby  = $param['orderby'];
            $pagesize = $param['pagesize'];
            $page     = $param['page'];
            $lat2     = $param['lat2'];
            $lng2     = $param['lng2'];
            $addrid   = $param['addrid'];
            $thumb    = $param['thumb'];
            $video    = $param['video'];
            $title    = $param['title'];
            $top      = $param['top'];
            $typeid   = $param['typeid'];

        } else {
            // return array('state' => 200, 'info' => $langData['info'][1][58]);
        }
        $where = 'WHERE 1=1 ';

        $page     = empty($page) ? 1 : $page;
        $pagesize = empty($pagesize) ? 10 : $pagesize;

        if ($id) {
            $where .= " AND `id` = $id";
        }

        if($title){
            $uids = $dsql->dsqlOper($dsql->SetQuery("SELECT `uid` FROM `#@__infoshop`"), "results");
            $store_member = [];
            $ids = array_column($uids, 'uid');
            $ids = join(",", $ids);
            $store_mems = $dsql->dsqlOper($dsql->SetQuery("SELECT `nickname`, `id` FROM `#@__member` WHERE `id` in ({$ids}) AND `company` LIKE '%$title%'"), "results");
            if(!empty($store_mems)){
                $store_member = array_column($store_mems, "id");
            }
            if($store_member){
                $where .= " AND `uid` in (" . join(',', $store_member) . ")";
            }else{
                $where .= " AND 1 = 2";
            }
        }

        if($thumb){
            $where .= " AND `pic` != ''";
        }
        if($video){
            $where .= " AND `video` != ''";
        }
        if($top){
            $where .= " AND `top` = 1";
        }
        //遍历地区
        if (!empty($addrid) && $addrid != 0) {
            if ($dsql->getTypeList($addrid, "site_area")) {
                global $arr_data;
                $arr_data = array();
                $lower    = arr_foreach($dsql->getTypeList($addrid, "site_area"));
                $lower    = $addrid . "," . join(',', $lower);
            } else {
                $lower = $addrid;
            }
            $where .= " AND `addrid` in ($lower)";
        }

        //遍历分类
        if (!empty($typeid)) {
            if ($dsql->getTypeList($typeid, "infotype")) {
                global $arr_data;
                $arr_data = array();
                $lower    = arr_foreach($dsql->getTypeList($typeid, "infotype"));
                $lower    = $typeid . "," . join(',', $lower);
            } else {
                $lower = $typeid;
            }
            $where .= " AND `stype` in ($lower) ";
        }

        $where .= ' AND `state` = 1 ';
        if ($orderby == '1') {
            $where .= " ORDER BY `id` DESC, `weight` DESC ,`top` DESC";
        }elseif ($orderby == '2'){
            $where .= " ORDER BY `click` DESC ";
        }elseif ($orderby == '3'){
            $where .= " ORDER BY rand() ";
        }

        if(empty($id)){
            $archives = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infoshop`".$where);
            // $results = $dsql->dsqlOper($archives, "results");
            // $totalCount = $results[0]['total'];
            $totalCount = getCache("info_shop_total", $archives, 300, array("name" => "total"));
        }else{
            $totalCount = 1;
        }

        if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

        $totalPage = ceil($totalCount / $pagesize);

        $pageinfo = ['totalCount' => $totalCount, 'page'=>$page, 'totalPage'=> $totalPage, 'pageSize'=>$pagesize];

        if (empty($id) && $pagesize && $page) {
            $offset = $pagesize * ($page - 1);
            $where  .= " LIMIT $offset, $pagesize";
        }

        $sql = "SELECT * FROM `#@__infoshop` " . $where;

        $archives = $dsql->SetQuery($sql);
        // $results  = $dsql->dsqlOper($archives, "results");
        if(empty($id)){
            $results = getCache("info_shop_list", $archives, 300);
        }else{
            $results = getCache("info_shop_detail", $archives, 0, $id);
        }

        $totalPage = ceil(count($results) / $pagesize);

        if (is_array($results)) {

            foreach ($results as &$result) {
                $user = getMemberDetail($result['uid']);
                $user['company'] = $user['company'] ? $user['company'] : $user['nickname'];
                $result['user'] = $user;

                $sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__infolist` WHERE `userid` = {$result['uid']}" );
                $total = getCache("info", $sql, 300, array("name" => "total"));
                $result['fabu_num'] = $total;

                $sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member_follow` WHERE `fid` = " . $result['uid']);
                $total = getCache("info", $sql, 300, array("name" => "total"));
                $result['fensi'] = $total;

                $sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member_follow` WHERE `tid` = " . $result['uid']);
                $total = getCache("info", $sql, 300, array("name" => "total"));
                $result['guanzhu'] = $total;

                //是否关注
                $userLoginid = $userLogin->getMemberID();
                $sql = "SELECT `id` FROM `#@__member_follow` WHERE `tid` = $userLoginid AND `fid` = {$result['uid']}";
                $sql = $dsql->SetQuery($sql);
                $is_foll = $dsql->dsqlOper($sql, "results");
                $result['is_follow'] = $is_foll ? 1 : 0;

                $days           = (time() - (strtotime($result['user']['regtime']))) / 3600 / 24;

                $mons                   = (int)($days / 30);
                $result['user']['mons'] = $mons;
                $archives               = $dsql->SetQuery("SELECT `typename` FROM `#@__infotype` WHERE `id` = " . $result['stype']);
                // $typenames              = $dsql->dsqlOper($archives, "results");
                $typenames = getCache("info_type", $archives, 0, array("sign" => $result['stype'], "name" => "typename"));
                if ($typenames) {
                    $result['typename'] = !empty($typenames[0]['typename']) ? $typenames[0]['typename'] : $typenames;
                } else {
                    $result['typename'] = '未知';
                }

                $result['notes'] = $result['note'] ? cn_substrR($result['note'], 80) : '';

                $businesshours = '';
                if($result['openStart'] && $result['openEnd']){
                    $openStart = str_split($result['openStart'], 2);
                    $openStart = $openStart[0] . ":" . $openStart[1];
                    $openEnd   = str_split($result['openEnd'], 2);
                    $openEnd   = $openEnd[0] . ":" . $openEnd[1];
                }
                $result['businesshours'] = $openStart . "-" . $openEnd;

                $sql    = $dsql->SetQuery("SELECT `id` FROM `#@__member_collect` WHERE `module` = 'info' AND `action` = 'shop' AND `aid` = " . $result['id']);
                // $total  = getCache("info", $sql, 300, array("name" => "total"));
                // $result['collectnum'] = $total;
                $collectnum  = $dsql->dsqlOper($sql, "totalCount");
                $result['collectnum'] = $collectnum;


                //验证是否已经收藏
                $params                = array(
                    "module" => "info",
                    "temp" => "shop",
                    "type" => "add",
                    "id" => $result['id'],
                    "check" => 1
                );
                $collect               = checkIsCollect($params);
                $result['collect'] = $collect == "has" ? 1 : 0;

                $result['video']     = $result['video'] ? getFilePath($result['video']) : ''; //店铺视频
                $result['video_pic'] = $result['video_pic'] ? getFilePath($result['video_pic']) : ''; //视频封面
                if($result['wechat_pic']){
                    $wechat_pic_1 = explode(',', $result['wechat_pic'])[0];
                    $result['wechat_pic'] =  getFilePath($wechat_pic_1);
                }


                $pic_tmp = $result['pic'] ? explode('###', str_replace('||', '', $result['pic'])) : ''; //店铺图片
                if(!empty($pic_tmp[0]) && strpos($pic_tmp[0], ',') !== false){
                    $pic_tmp = explode(',', $pic_tmp[0]);
                }
                if ($pic_tmp) {
                    foreach ($pic_tmp as $k => &$item) {
                        $pic_tmp[$k] = getFilePath($item);
                    }
                }
                $result['pics'] = $pic_tmp;
                $result['pcount'] = count($pic_tmp);

                global $data;
                $data     = "";
                $addrArr            = getParentArr("site_area", $result['addrid']);
                $addrArr            = array_reverse(parent_foreach($addrArr, "typename"));
                $result['address_'] = $addrArr;

                $addrArr               = $result['address_'];
                $addrArr               = array_reverse(parent_foreach($addrArr, "typename"));
                $result['address_app'] = join(" > ", $addrArr);

                $param         = array(
                    "service" => "info",
                    "template" => "business",
                    "id" => $result['id']
                );
                $result['url'] = getUrlPath($param);

                // $sql   = "SELECT * FROM `#@__info_shopcommon` WHERE `ischeck` = 1 AND `floor` = '0' AND `pid` = " . $result['id'];
                // $sql   = $dsql->SetQuery($sql);

                $sql = $dsql->SetQuery("SELECT count(`id`) totalCount FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'info-business' AND `aid` = " . $result['id'] . " AND `pid` = 0");
                $comms = $dsql->dsqlOper($sql, "results");
                //店铺评论
                $result['shop_common'] = $comms[0]['totalCount'];
                //商家坐标
                $result['lnglat'] = $result['lnglat'] ? explode(',', $result['lnglat']) : array(0, 0);
                //商家距离
                $distance = getDistance($result['lnglat']['0'], $result['lnglat']['1'], $lng2, $lat2);
                $result['lnglat_diff'] = sprintf("%.2f", ($distance / 1000));

                //取商家前5条商品
                $this->param = array(
                    'shopid' => $result['id'],
                    'pageSize' => 5
                );
                $result['product'] = $this->ilist_v2();

            }
            unset($result);

            if(!$orderby){
                $res1 = [];
                $res2 = [];
                foreach ($results as $key => $result) {
                    if ($result['top'] == 1) {
                        $res1[$key] = $result;
                    } else {
                        $res2[$key] = $result;
                    }
                }
                $resList = array_merge($res1, $res2);
            }else{
                $resList = $results;
            }

        }
        if(!$resList){
            return array('state' =>200, 'info' => '');
        }

        return array("pageInfo" => $pageinfo, "list" => $resList);
    }

    public function getFenSiList(){
        global $dsql;
        $param = $this->param;
        $type = $param['type'];
        $user_id = $param['user_id'];
        try{
            if($type == 'f'){
                $fensi              = $dsql->SetQuery("SELECT * FROM `#@__site_followmap` WHERE `userid_b` = " . $user_id . " AND `temp` = 'info'" );
                $fensi              = $dsql->dsqlOper($fensi, "results");
                //粉丝列表
                $res = [];
                foreach ($fensi as $value){
                    $arr = [];
                    $arr['user'] = getMemberDetail($value['userid']);
                    $shop = $this->isShop($value['userid']);
                    $param = [
                        'service' => 'info',
                        'template' => $shop ? 'business' : 'homepage',
                        'id' => $shop ? $shop['id'] : $arr['user']['userid']
                    ];
                    $arr['url'] = getUrlPath($param);
                    $arr['is_shop'] = $shop ? 1 : 0;
                    if($shop){
                        $archives               = $dsql->SetQuery("SELECT `typename` FROM `#@__infotype` WHERE `id` = " . $shop['stype']);
                        $typenames              = $dsql->dsqlOper($archives, "results");
                        $arr['typename'] = $typenames[0]['typename'];
                    }
                    $sql     = $dsql->SetQuery("SELECT `id` FROM `#@__infolist` WHERE `userid` = " . $value['userid']);
                    $infos = $dsql->dsqlOper($sql, "results");
                    $arr['info_count'] = count($infos);
                    $fensi              = $dsql->SetQuery("SELECT * FROM `#@__site_followmap` WHERE `userid_b` = " . $value['userid'] . " AND `temp` = 'info'" );
                    $fensi              = $dsql->dsqlOper($fensi, "results");
                    $arr['fensi_count'] = count($fensi);
                    array_push($res, $arr);
                }
            }else if ($type == 'g'){
                $guanzhu              = $dsql->SetQuery("SELECT * FROM `#@__site_followmap` WHERE `userid` = " . $user_id . " AND `temp` = 'info'" );
                $guanzhu              = $dsql->dsqlOper($guanzhu, "results");
                //关注列表
                $res = [];
                foreach ($guanzhu as $value){
                    $arr = [];
                    $arr['user'] = getMemberDetail($value['userid_b']);
                    $arr['is_shop'] = $this->isShop($value['userid_b']);
                    $shop = $this->isShop($value['userid_b']);
                    $param = [
                        'service' => 'info',
                        'template' => $shop ? 'business' : 'homepage',
                        'id' => $shop ? $shop['id'] : $arr['user']['userid']
                    ];
                    $arr['url'] = getUrlPath($param);
                    $arr['is_shop'] = $shop ? 1 : 0;
                    if($shop){
                        $archives               = $dsql->SetQuery("SELECT `typename` FROM `#@__infotype` WHERE `id` = " . $shop['stype']);
                        $typenames              = $dsql->dsqlOper($archives, "results");
                        $arr['typename'] = $typenames[0]['typename'];
                    }
                    $sql     = $dsql->SetQuery("SELECT `id` FROM `#@__infolist` WHERE `userid` = " . $value['userid_b']);
                    $infos = $dsql->dsqlOper($sql, "results");
                    $arr['info_count'] = count($infos);
                    $fensi              = $dsql->SetQuery("SELECT * FROM `#@__site_followmap` WHERE `userid_b` = " . $value['userid_b'] . " AND `temp` = 'info'" );
                    $fensi              = $dsql->dsqlOper($fensi, "results");
                    $arr['fensi_count'] = count($fensi);
                    array_push($res, $arr);
                }
            }
        }catch (\Exception $e){
            return array('state' =>200, 'info' => '');
        }

        return $res;
    }

    public function isShop($uid)
    {
        global $dsql;
        //查询是否是商家
        $sql     = $dsql->SetQuery("SELECT * FROM `#@__infoshop` WHERE `uid` = " . $uid);
        $is_shop = $dsql->dsqlOper($sql, "results");
        if($is_shop && count($is_shop) > 0){
            return $is_shop[0];
        }else{
            return 0;
        }
    }

    public function storeDetail()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $storeDetail = array();
        $id          = $this->param;
        $id          = is_numeric($id) ? $id : $id['id'];
        $uid         = $userLogin->getMemberID();

        if (!is_numeric($id) && $uid == -1) {
            return array("state" => 200, "info" => $langData['info'][1][58]);
        }

        $where = " AND `state` = 1";
        if (!is_numeric($id)) {
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__infoshop` WHERE `uid` = " . $uid);
            $results  = $dsql->dsqlOper($archives, "results");
            if ($results) {
                $id    = $results[0]['id'];
                $where = "";
            } else {
                return array("state" => 200, "info" => $langData['info'][2][5]);//该会员暂未开通商铺！
            }
        }

        $archives = $dsql->SetQuery("SELECT * FROM `#@__infoshop` WHERE `id` = " . $id . $where);
        $results  = $dsql->dsqlOper($archives, "results");
        if ($results) {
            $storeDetail["id"]    = $results[0]['id'];
            $storeDetail["istop"] = $results[0]['top'];


            $this->param = [
                'userid' => $uid
            ];

            $uid                   = $results[0]['uid'];
            $storeDetail['member'] = getMemberDetail($uid);

            $storeDetail["typeid"] = $results[0]['stype'];
            global $data;
            $data     = "";
            $infotype = getParentArr("infotype", $results[0]['stype']);
            if ($infotype) {
                $infotype                    = array_reverse(parent_foreach($infotype, "typename"));
                $storeDetail['typename']     = join(" > ", $infotype);
                $storeDetail['typenameonly'] = count($infotype) > 2 ? $infotype[1] : $infotype[0];
            } else {
                $storeDetail['typename']     = "";
                $storeDetail['typenameonly'] = "";
            }

            $storeDetail["addrid"] = $results[0]['addrid'];
            global $data;
            $data     = "";
            $addrName = getParentArr("site_area", $results[0]['addrid']);
            global $data;
            $data                    = "";
            $addrArr                 = array_reverse(parent_foreach($addrName, "typename"));
            $addrArr                 = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
            $storeDetail['addrname'] = $addrArr;


            global $data;
            $data                = "";
            $addrArr             = array_reverse(parent_foreach($addrName, "id"));
            $storeDetail['city'] = count($addrArr) > 2 ? $addrArr[1] : $addrArr[0];

            $storeDetail["address"]      = $results[0]['address'];
            $storeDetail["shortaddress"] = cn_substrR($results[0]['address'], 10);

            if (!empty($results[0]['circle'])) {
                $storeDetail["circleid"]   = explode(",", $results[0]['circle']);
                $storeDetail['circlelist'] = json_encode(explode(",", $results[0]['circle']));
                $circleArr                 = array();
                $sql                       = $dsql->SetQuery("SELECT `name` FROM `#@__site_city_circle` WHERE `id` in (" . $results[0]['circle'] . ")");
                $creturn                   = $dsql->dsqlOper($sql, "results");
                if ($creturn) {
                    foreach ($creturn as $key => $value) {
                        $circleArr[$key] = $value['name'];
                    }
                }
                $storeDetail["circle"] = join("、", $circleArr);
            } else {
                $storeDetail["circle"]     = "";
                $storeDetail['circlelist'] = 0;
            }

            $subwayIds                        = $results[0]['subway'];
            $storeDetail["subwayid"]          = explode(",", $subwayIds);
            $storeDetail["subwaystationlist"] = json_encode(explode(",", $results[0]['subway']));
            $subwayArr                        = array();

            if (!empty($subwayIds)) {
                $sql     = $dsql->SetQuery("SELECT `title` FROM `#@__site_subway_station` WHERE `id` in (" . $subwayIds . ")");
                $creturn = $dsql->dsqlOper($sql, "results");
                if ($creturn) {
                    foreach ($creturn as $key => $value) {
                        $subwayArr[$key] = $value['title'];
                    }
                }
            }
            $storeDetail["subway"] = join("、", $subwayArr);

            $storeDetail["lnglat"]    = $results[0]['lnglat'];
            $storeDetail["tel"]       = $results[0]['tel'];
            $openStart                = $results[0]['openStart'];
            $open1                    = substr($openStart, 0, 2);
            $open2                    = substr($openStart, 2);
            $storeDetail["openStart"] = $open1 . ":" . $open2;

            $openEnd                = $results[0]['openEnd'];
            $end1                   = substr($openEnd, 0, 2);
            $end2                   = substr($openEnd, 2);
            $storeDetail["openEnd"] = $end1 . ":" . $end2;

            $storeDetail["score"]       = $results[0]['score'];
            $storeDetail["click"]       = $results[0]['click'];
            $storeDetail["note"]        = $results[0]['note'];
            $storeDetail["body"]        = $results[0]['body'];
            $storeDetail["state"]       = $results[0]['state'];
            $storeDetail["phone"]       = $results[0]['phone'];
            $storeDetail["video"]       = $results[0]['video'];
            $storeDetail["sourcevideo"] = $results[0]['video'] ? getFilePath($results[0]['video']) : '';
            //验证是否已经收藏
            $params                 = array(
                "module" => "info",
                "temp" => "info",
                "type" => "add",
                "id" => $results[0]['id'],
                "check" => 1
            );
            $collect                = checkIsCollect($params);
            $storeDetail['collect'] = $collect == "has" ? 1 : 0;
            //图集
            $imglist = array();
            $pics    = str_replace('||', '', $results[0]['pic']);
            if (!empty($pics)) {
                $pics = explode("###", $pics);
                foreach ($pics as $key => $value) {
                    $imglist[$key]['path']       = getFilePath($value);
                    $imglist[$key]['pathSource'] = $value;
                }
            } else {
                $imglist[$key]['path'] = '';
            }
            $storeDetail['pics'] = $imglist;

            $imglist1 = array();
            $pics1    = $results[0]['wechat_pic'];
            if (!empty($pics1)) {
                $pics1 = explode(",", $pics1);
                if(count($pics1) > 0){
                    $pics1 = [$pics1[0]];
                }
                foreach ($pics1 as $key => $value) {
                    $imglist1[$key]['path']       = getFilePath($value);
                    $imglist1[$key]['pathSource'] = $value;
                }
            } else {
                $imglist1[$key]['path'] = '';
            }
            $storeDetail['wechat_pic'] = $imglist1;


            $imgGroup = array();
            global $cfg_attachment;
            $attachment = substr($cfg_attachment, 1, strlen($cfg_attachment));

            $attachment = substr("/include/attachment.php?f=", 1, strlen("/include/attachment.php?f="));

            global $cfg_basehost;
            $attachment = str_replace("http://" . $cfg_basehost, "", $cfg_attachment);
            $attachment = str_replace("https://" . $cfg_basehost, "", $cfg_attachment);
            $attachment = substr($attachment, 1, strlen($attachment));

            $attachment = str_replace("/", "\/", $attachment);
            $attachment = str_replace(".", "\.", $attachment);
            $attachment = str_replace("?", "\?", $attachment);
            $attachment = str_replace("=", "\=", $attachment);

            preg_match_all("/$attachment(.*)[\"|'| ]/isU", $results[0]['body'], $picList);
            $picList = array_unique($picList[1]);

            //内容图片
            if (!empty($picList)) {
                foreach ($picList as $v_) {
                    $filePath = getRealFilePath($v_);
                    $fileType = explode(".", $filePath);
                    $fileType = strtolower($fileType[count($fileType) - 1]);
                    $ftype    = array("jpg", "jpge", "gif", "jpeg", "png", "bmp");
                    if (in_array($fileType, $ftype)) {
                        $imgGroup[] = $filePath;
                    }
                }
            }


            preg_match_all('/<img[^>]+src=[\'\" ]?([^ \'\"?]+)[\'\" >]/isU', $results[0]['body'], $picList);
            $picList = array_unique($picList[1]);
            if (!empty($picList)) {
                foreach ($picList as $v_) {
                    $imgGroup[] = $v_;
                }
            }

            $storeDetail['imgGroup'] = $imgGroup;


            //统计评论数量
            // $sql                        = $dsql->SetQuery("SELECT count(`id`) totalCommon FROM `#@__info_shopcommon`  WHERE `ischeck` = 1 AND `pid` = " . $id);
            $sql = $dsql->SetQuery("SELECT count(`id`) totalCommon FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'info-business' AND `aid` = '$id' AND `pid` = 0");
            $ret                        = $dsql->dsqlOper($sql, "results");
            $storeDetail['totalCommon'] = $ret[0]['totalCommon'];

            $storeDetail['cityid'] = $results[0]['cityid'];

            $param         = array(
                "service" => "info",
                "template" => "business",
                "id" => $results[0]['id']
            );
            $storeDetail['domain'] = getUrlPath($param);

        }
        return $storeDetail;
    }

    /**
     * 下单
     * @return string
     */
    public function dealTouch()
    {
        global $dsql;
        global $userLogin;
        global $cfg_basehost;
        global $cfg_pointRatio;
        global $langData;

        $param = $this->param;

        $param1 = array(
            "service" => "info",
            "template" => "confirm",
            "id" => $param['buy_id'],
        );
        $url    = getUrlPath($param1);


        //验证需要支付的费用
        $payTotalAmount = $this->checkPayAmount();

        //重置表单参数
        $this->param = $param;

        $payCheck = $this->payCheck();
        if ($payCheck != "ok" || is_array($payTotalAmount)) {

            if ($payCheck != "ok") {
                if($this->param['flag1']){
                    echo "<script> window.onload = function (){alert(\"{$payCheck['info']}\");window.location.href = '$url'; } </script>";exit;
                }else{
                    return $payCheck;
                }

            }

            header("location:" . $url);
            die;
        }


        $ordernum   = $param['ordernum'];
        $pros       = $param['pros'];
        $addressid  = $param['addressid'];
        $paytype    = $param['paytype'];
        $note       = $param['note'];
        $flag1      = $param['flag1'];
        $flag       = $param['flag'];
        $usePinput  = $param['usePinput'];
        $point      = (float)$param['point'];
        $useBalance = $param['useBalance'];
        $balance    = (float)$param['balance'];
        $userid     = $userLogin->getMemberID();

        if (empty($addressid)) return array("state" => 200, "info" => $langData['shop'][4][15]);  //请选择收货地址
        if (empty($pros)) return array("state" => 200, "info" => $langData['shop'][4][4]);  //格式错误

        //收货地址信息
        global $data;
        $data     = "";
        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_address` WHERE `uid` = $userid AND `id` = $addressid");
        $userAddr = $dsql->dsqlOper($archives, "results");
        if (!$userAddr) return array("state" => 200, "info" => $langData['info'][2][6]);  //会员地址库信息不存在或已删除
        $addrArr = getParentArr("site_area", $userAddr[0]['addrid']);
        $addrArr = array_reverse(parent_foreach($addrArr, "typename"));
        $addr    = join(" ", $addrArr);
        $address = $addr . $userAddr[0]['address'];
        $person  = $userAddr[0]['person'];
        $mobile  = $userAddr[0]['mobile'];
        $tel     = $userAddr[0]['tel'];
        $contact = !empty($mobile) ? $mobile . (!empty($tel) ? " / " . $tel : "") : $tel;

        $this->param = $pros;
        $detail      = $this->detail();

        //价格
        $price = $detail['price'] + (int)($detail['yunfei']);

        $opArr       = array();
        $ordernumArr = array();

        //新订单
        $newOrdernum = create_ordernum();

        //删除该用户之前的无效订单
        $delsql = $dsql->SetQuery("DELETE FROM `#@__info_order` WHERE `userid` = $userid AND `prod` = $pros AND `orderstate` = 0");
        $dsql->dsqlOper($delsql, 'update');

        //新增主表
        $store = $detail['info_shop_id'] ? $detail['info_shop_id'] : 0;
        $note  = $note[$store];
        $sql   = $dsql->SetQuery("INSERT INTO `#@__info_order` (`ordernum`, `store`, `userid`, `orderstate`, `orderdate`, `paytype`, `people`, `address`, `contact`, `note`, `prod`, `price` , `point`)
                                                      VALUES ('$newOrdernum', '$store', '$userid', 0, " . GetMkTime(time()) . ", '$paytype', '$person', '$address', '$contact', '$note', '$pros', '$price', '$point')");
        $oid   = $dsql->dsqlOper($sql, "lastid");

        if (!$oid) {
            return array("state" => 200, "info" => $langData['siteConfig'][21][174]);  //下单失败！
        }

        $RenrenCrypt = new RenrenCrypt();
        $ids         = base64_encode($RenrenCrypt->php_encrypt($newOrdernum));
        // 电脑端
        if($flag){
            $this->param = [
                'ordernum' => $newOrdernum,
                'paytype' => $paytype,
                'usePinput' => $param['usePinput'],
                'point' => $param['point'],
                'useBalance' => $param['useBalance'],
                'balance' => $param['balance'],
                'paypwd' => $param['paypwd'],
                'check' => $param['check'],
                'yunfei' => (int)($detail['yunfei']),
                'flag1' => $flag1,
                'flag' => 1,
            ];
            return $this->pay();
        }

        $param = array(
            "service" => "info",
            "template" => "pay",
            "param" => "ordernum=" . $ids
        );
        return getUrlPath($param);


    }

    /**
     * 支付前验证订单内容
     * 验证内容：商品是否存在，是否过期
     * @return array
     */
    public function payCheck()
    {
        global $dsql;
        global $userLogin;
        global $langData;

        $param = $this->param;
        $pros  = $param['pros'];

        if (empty($pros)) return array("state" => 200, "info" => $langData['shop'][4][11]);  //商品信息传递失败！

        $userid = $userLogin->getMemberID();

        //验证商品是否存在
        $this->param = $pros;
        $detail      = $this->detail();
        $this->param = $param;

        if (!is_array($detail)) {
            $info = $langData['shop'][4][13];  //订单中包含不存在或已下架的商品，请确认后重试！      提交失败，您要购买的商品不存在或已下架！
            return array("state" => 200, "info" => $info);
        }

        //验证是否为自己的店铺
        $sql = $dsql->SetQuery("SELECT `uid` FROM `#@__infoshop` WHERE `uid` = $userid");
        $ret = $dsql->dsqlOper($sql, "results");
        if ($ret) {
            if ($detail['member']['userid'] == $ret[0]['uid']) {
                return array("state" => 200, "info" => $langData['shop'][4][14]);  //企业会员不得购买自己店铺的商品！
            }
        }

        //验证购买的是不是自己的商品
        if($detail['member']['userid'] == $userid){
            return array("state" => 200, "info" => $langData['info'][2][7]);  //企业会员不得购买自己店铺的商品！
        }


        //是否有效
        if ($detail['arcrank'] != 1 && $detail['valid'] < time() || $detail['is_valid'] == 1) {
            return array("state" => 200, "info" => $langData['info'][2][8]);//您要购买的商品已经失效
        }

        return "ok";

    }

    /**
     * 支付前验证帐户积分和余额
     */
    public function checkPayAmount()
    {
        global $dsql;
        global $userLogin;
        global $cfg_pointName;
        global $cfg_pointRatio;
        global $langData;

        $userid = $userLogin->getMemberID();
        $param  = $this->param;

        $pros       = $param['pros'];        //商品

        if ($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        if (empty($pros) && empty($ordernum)) return array("state" => 200, "info" => $langData['shop'][4][9]);  //提交失败，商品信息提交失败！

        //订单状态验证
        $payCheck = $this->payCheck();
        if ($payCheck != "ok") return array("state" => 200, "info" => $payCheck['info']);

        $this->param = $pros;
        $detail      = $this->detail();

        //价格
        $price = $detail['price'] + (int)($detail['yunfei']);

        //返回需要支付的费用
        return sprintf("%.2f", $price );

    }

    /**
     * 支付
     */
    public function pay()
    {
        global $dsql;
        global $cfg_secureAccess;
        global $cfg_basehost;
        global $userLogin;
        global $cfg_pointRatio;
        global $langData;
        global $cfg_pointName;

        $ordernum   = $this->param['ordernum'];
        $paytype    = $this->param['paytype'];
        $usePinput  = $this->param['usePinput'];
        $point      = (float)$this->param['point'];
        $useBalance = $this->param['useBalance'];
        $balance    = (float)$this->param['balance'];
        $paypwd     = $this->param['paypwd'];      //支付密码
        $check      = (int)$this->param['check'];
        $flag       = (int)$this->param['flag'];     // 电脑端
        $flag1      = (int)$this->param['flag1'];    // 为1时表示没有使用积分余额，直接跳转到第三方支付页面;为0时返回url
        $yunfei     = (int)$this->param['yunfei'];

        // $isPC = $flag;
        // $check = $check;
        // echo $check."==".$flag1."===".$flag;die;
        $userid = $userLogin->getMemberID();
        if ($userid == -1) {
            if ($check || !$flag1) {
                return array("state" => 200, "info" => $langData['info'][2][9]);//登陆超时
            } else {
                die($langData['info'][2][9]);//登陆超时
            }
        }

        if ($ordernum) {
            $ordersql = $dsql->SetQuery("SELECT * FROM `#@__info_order` WHERE `ordernum` = '$ordernum'");
            $orderinfo = $dsql->dsqlOper($ordersql, "results");
            if ($orderinfo) {
                $data          = $orderinfo[0];
                $id            = $data['id'];
                $uid           = $data['userid'];
                $sid           = $data['store'];
                $totalPrice    = $data['price'] + $yunfei;
                $prod    = $data['prod'];

                $date = GetMkTime(time());


                //查询会员信息
                $userinfo  = $userLogin->getMemberInfo();
                $usermoney = $userinfo['money'];
                $userpoint = $userinfo['point'];

                $tit      = array();
                $useTotal = 0;

                //判断是否使用积分，并且验证剩余积分
                if ($usePinput == 1 && !empty($point)) {
                    if ($userpoint < $point) return array("state" => 200, "info" => $langData['info'][2][10] . $cfg_pointName . $langData['info'][2][11]);//"您的可用" . $cfg_pointName . "不足，支付失败！"
                    $useTotal += $point / $cfg_pointRatio;
                    $tit[]    = "integral";
                }

                //判断是否使用余额，并且验证余额和支付密码
                if ($useBalance == 1 && !empty($balance) && !empty($paypwd)) {

                    if (!empty($balance) && empty($paypwd)) {
                        if ($check || !$flag1) {
                            return array("state" => 200, "info" => $langData['info'][2][12]);//请输入支付密码
                        } else {
                            die($langData['info'][2][12]);
                        }
                    }

                    //验证支付密码
                    $archives = $dsql->SetQuery("SELECT `id`, `paypwd` FROM `#@__member` WHERE `id` = '$userid'");
                    $results  = $dsql->dsqlOper($archives, "results");
                    $res      = $results[0];
                    $hash     = $userLogin->_getSaltedHash($paypwd, $res['paypwd']);

                    if ($res['paypwd'] != $hash) {
                        if ($check || !$flag1) {
                            return array("state" => 200, "info" => $langData['info'][2][13]);//支付密码输入错误，请重试！
                        } else {
                            die($langData['info'][2][13]);
                        }
                    }

                    //验证余额
                    if ($usermoney < $balance) {
                        if ($check || !$flag1) {
                            return array("state" => 200, "info" => $langData['info'][2][14]);//您的余额不足，支付失败！
                        } else {
                            die($langData['info'][2][14]);
                        }
                    }

                    $useTotal += $balance;
                    $tit[]    = "money";

                }


                // 使用了余额
                if ($useTotal) {

                    if ($useTotal > $totalPrice) {
                        if ($check || !$flag1) {
                            return array("state" => 200, "info" => $langData['info'][2][15] . join($langData['info'][2][17], $tit) . $langData['info'][2][16] . join($langData['info'][2][17], $tit));//"您使用的" . join("和", $tit) . "超出订单总费用，请重新输入要使用的" . join("和", $tit))
                        } else {
                            die("您使用的" . join("和", $tit) . "超出订单总费用，请重新输入要使用的" . join("和", $tit));
                        }
                        // 余额不足
                    } elseif ($useTotal < $totalPrice && empty($paytype)) {
                        if ($check || !$flag1) {
                            return array("state" => 200, "info" => $langData['info'][2][18]);//请选择在线支付方式！
                        } else {
                            die($langData['info'][2][18]);
                        }
                    }
                }

                $amount = $totalPrice - $useTotal;
                if ($amount > 0 && empty($paytype)) {
                    if ($check || !$flag1) {
                        return array("state" => 200, "info" => $langData['info'][2][18]);//请选择在线支付方式！
                    } else {
                        die($langData['info'][2][18]);
                    }
                }

                if ($check) return "ok";


                $param = array(
                    "type" => "info"
                );

                //记录实际支付信息
                $sqlorder = $dsql->SetQuery("UPDATE `#@__info_order` SET  `payprice` = $amount, `point` = $point, `balance` = $balance WHERE `ordernum` = '$ordernum'");
                $dsql->dsqlOper($sqlorder, 'update');



                if ($amount > 0) {
                    // 电脑端并且使用了积分余额时返回跳转链接
                    if($flag && !$flag1){
                        $param = $this->param;
                        unset($param['flag']);
                        unset($param['flag1']);
                        unset($param['check']);
                        // print_r($param);die;
                        return "/include/ajax.php?service=info&action=pay&".http_build_query($param);
                    }else{
                        // echo $ordernum."=".$amount."===".$paytype;die;
                        createPayForm("info", $ordernum, $amount, $paytype, $langData['info'][2][19], $param);//二手订单
                    }

                    // 余额支付
                } else {

                    $paytype = $langData['info'][2][20];//余额

                    $body     = serialize($param);
                    $archives = $dsql->SetQuery("INSERT INTO `#@__pay_log` (`ordertype`, `ordernum`, `uid`, `body`, `amount`, `paytype`, `state`, `pubdate`) VALUES ('info', '$ordernum', '$userid', '$body', 0, '$paytype', 1, $date)");
                    $dsql->dsqlOper($archives, "results");

                    //执行支付成功的操作
                    $this->param = array(
                        "paytype" => $paytype,
                        "ordernum" => $ordernum,
                        "ordertype" => 'info'
                    );
                    $url = $this->paySuccess();

                    if($flag){
                        return $url;
                    }
                    header("location:" . $url);
                    die;

                }

            } else {
                if ($check) {
                    return array("state" => 200, "info" => $langData['info'][2][21]);//订单不存在或已支付
                } else {
                    $param = array(
                        "service" => "info",
                        "template" => "index"
                    );
                    $url = getUrlPath($param);
                    header("location:" . $url);
                    die();
                }
            }

        } else {
            if ($flag) {
                return array("state" => 200, "info" => $langData['info'][2][22]);//订单不存在
            } else {
                $param = array(
                    "service" => "info",
                    "template" => "index"
                );
                $url   = getUrlPath($param);
                header("location:" . $url);
                die();
            }
        }



    }


    /**
     * 订单列表
     * @return array
     */
    public function orderList(){
        global $dsql;
        global $langData;
        $pageinfo = $list = array();
        $store = $state = $userid = $page = $pageSize = $where = "";

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => $langData['info'][1][58]);
            }else{
                $store    = $this->param['store'];
                $type     = $this->param['type'];
                $state    = $this->param['state'];
                $userid   = $this->param['userid'];
                $ordernum = $this->param['ordernum'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        if(empty($userid)){
            global $userLogin;
            $userid = $userLogin->getMemberID();
        }
        if(empty($userid)) return array("state" => 200, "info" => $langData['info'][2][23]);// 会员ID不得为空！

        //个人会员订单列表
        if(empty($store)){
            $where = ' `userid` = '.$userid;


        }else{
            //商家会员订单列表
            if(empty($type)){
                if(!verifyModuleAuth(array("module" => "info"))){
                    return array("state" => 200, "info" => $langData['shop'][4][1]);  //商家权限验证失败！
                }

                $sid = 0;
                $userSql = $dsql->SetQuery("SELECT `id` FROM `#@__infoshop` WHERE `uid` = ".$userid);
                $userResult = $dsql->dsqlOper($userSql, "results");
                if(!$userResult){
                    return array("state" => 200, "info" => $langData['info'][2][24]);//'您还未开通二手信息店铺！'
                }else{
                    $sid = $userResult[0]['id'];
                }

                $where = ' `store` = '.$sid;
            }else{
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__infolist` WHERE `userid` = ". $userid);
                $proid = $dsql->dsqlOper($sql, "results");
                if($proid){
                    foreach ($proid as $shopid){
                        $sid[] = $shopid['id'];
                    }
                }
                if(!count($sid)){
                    return array("pageInfo" => $pageinfo, "list" => array());
                }else{
                    $where = " `prod` in (". join(',', $sid) . ')';
                }
            }
        }

        $archives = $dsql->SetQuery("SELECT " .
            "`id`, `ordernum`, `store`, `prod`, `userid`, `orderstate`, `orderdate`, `paytype`, `ret-state`, `exp-date` " .
            "FROM `#@__info_order` " .
            "WHERE".$where);

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        //未付款
        $unpaid = $dsql->dsqlOper($archives." AND `orderstate` = 0", "totalCount");
        //已支付
        $ongoing = $dsql->dsqlOper($archives." AND `orderstate` = 1", "totalCount");
        //已完成
        $success = $dsql->dsqlOper($archives." AND `orderstate` = 4", "totalCount");
        //申请退款
        $refunded = $dsql->dsqlOper($archives." AND `orderstate` = 6 AND `ret-state` != 0", "totalCount");
        //待发货
        $rates = $dsql->dsqlOper($archives." AND `orderstate` = 1 ", "totalCount");
        //已发货
        $recei = $dsql->dsqlOper($archives." AND `orderstate` = 3", "totalCount");
        //退款成功
        $closed = $dsql->dsqlOper($archives." AND `orderstate` = 7", "totalCount");

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount,
            "unpaid"   => $unpaid,
            "ongoing"  => $ongoing,
            "success"  => $success,
            "refunded" => $refunded,
            "rates"    => $rates,
            "recei"    => $recei,
            "closed"   => $closed,
        );

        if($totalCount == 0) return array("pageInfo" => $pageinfo, "list" => array());

        $where = "";
        if($state != "" && $state != 4 && $state != 5 && $state != 6){
            $where = " AND `orderstate` = " . $state;
        }

        //已完成
        if($state == 4){
            $where = " AND `orderstate` = 4";
        }

        //待评价
        if($state == 5){
            $where = " AND `orderstate` = 3 AND `common` = 0";
        }

        //已发货
        if($state == 3){
            $where = " AND `orderstate` = 3 AND `exp-date` != 0";
        }

        //退款中
        if($state == 8){
            $where = " AND `orderstate` = 8 ";
        }
        //申请退款
        if($state == 6){
            $where = " AND `orderstate` = 6 ";
        }
        $atpage = $pageSize*($page-1);
        $where .= " ORDER BY `id` DESC LIMIT $atpage, $pageSize";

        $results = $dsql->dsqlOper($archives.$where, "results");
        if($results){

            $param = array(
                "service"     => "info",
                "template"    => "detail",
                "id"          => "%id%"
            );
            $urlParam = getUrlPath($param);

            $param = array(
                "service"     => "info",
                "template"    => "pay",
                "param"       => "ordernum=%id%"
            );
            $payurlParam = getUrlPath($param);

            $param = array(
                "service"  => "member",
                "type"     => "user",
                "template" => "orderdetail",
                "module"   => "info",
                "id"       => "%id%",
                "param"    => "rates=1"
            );
            $commonUrlParam = getUrlPath($param);

            $i = 0;
            foreach($results as $key => $val){

                $sql = $dsql->SetQuery("SELECT * FROM `#@__infolist` WHERE `id` = ".$val['prod']);
                $ret = $dsql->dsqlOper($sql, "results");

                if($ret){

                    //商家订单列表显示买家会员信息
                    if(!empty($store)){

                        $member = getMemberDetail($val['userid']);
                        $list[$i]['member'] = array(
                            "nickname"     => $member['nickname'],
                            "certifyState" => $member['certifyState'],
                            "qq"           => $member['qq']
                        );


                        //个人会员订单列表显示商家信息
                    }else{

                        $this->param = $val['store'];
                        $storeConfig = $this->storeDetail();
                        if(is_array($storeConfig)){
                            if(empty($storeConfig)){
                                $shop_userid = $ret[0]['userid'];
                                $shop_userinfo = getMemberDetail($shop_userid);
                                $param11 = [
                                    'service' => 'info',
                                    'template' => 'homepage',
                                    'id' => $shop_userid
                                ];
                                $userdomain = getUrlPath($param11);
                                $list[$i]['store'] = array(
                                    "id"     => 0,
                                    "title"  => $shop_userinfo['nickname'],
                                    "domain" => $userdomain,
                                );
                            }else{
                                $list[$i]['store'] = array(
                                    "id"     => $storeConfig['id'],
                                    "title"  => $storeConfig['member']['nickname'],
                                    "domain" => $storeConfig['domain'],
                                );
                            }

                        }else{
                            $list[$i]['store'] = array(
                                "id"     => 0,
                                "title"  => $langData['shop'][4][37]  //官方直营
                            );
                        }

                    }

                    $list[$i]['id']          = $val['id'];
                    $list[$i]['ordernum']    = $val['ordernum'];
                    $list[$i]['orderstate']  = $val['orderstate'];
                    $list[$i]['orderdate']   = $val['orderdate'];
                    $list[$i]['retState']    = $val['ret-state'];
                    $list[$i]['expDate']     = $val['exp-date'];

                    //支付方式
                    $paySql = $dsql->SetQuery("SELECT `pay_name` FROM `#@__site_payment` WHERE `pay_code` = '".$val["paytype"]."'");
                    $payResult = $dsql->dsqlOper($paySql, "results");
                    if(!empty($payResult)){
                        $list[$i]["paytype"]   = $payResult[0]["pay_name"];
                    }else{
                        global $cfg_pointName;
                        $payname = "";
                        if($val["paytype"] == "point,money"){
                            $payname = $cfg_pointName."+".$langData['siteConfig'][19][363];  //余额
                        }elseif($val["paytype"] == "point"){
                            $payname = $cfg_pointName;
                        }elseif($val["paytype"] == "余额"){
                            $payname = $langData['siteConfig'][19][363]; //余额
                        }
                        $list[$i]["paytype"]   = $payname;
                    }

                    //未付款的提供付款链接
                    if($val['orderstate'] == 0){
                        $RenrenCrypt = new RenrenCrypt();
                        $encodeid = base64_encode($RenrenCrypt->php_encrypt($val["ordernum"]));
                        $list[$i]["payurl"] = str_replace("%id%", $encodeid, $payurlParam);
                    }

                    //评价
                    $list[$i]['common'] = $val['common'];

                    //商品信息
                    $productArr = array();
                    $totalPayPrice = 0;
                    foreach ($ret as $k => $v) {
                        global $oper;
                        $oper = "user";
                        $this->param = $v['id'];
                        $detail = $this->detail();

                        $list[$i]['product'][$k]['title'] = $detail['title'];
                        $imglist = $detail['imglist'];
                        if(!empty($imglist)){
                            $list[$i]['product'][$k]['litpic'] = $imglist[0]['path'];
                        }else{
                            $list[$i]['product'][$k]['litpic'] = '';
                        }


                        $list[$i]['product'][$k]['url'] = str_replace("%id%", $v['id'], $urlParam);

                        $list[$i]['product'][$k]['price'] = $v['price'];
                        $list[$i]['product'][$k]['count'] =0;
                        $list[$i]['product'][$k]['specation'] ='';

                        //未付款的不计算积分和余额部分
                        if($val['orderstate'] == 0){
                            $totalPayPrice += $v['price'];
                        }else{
                            $totalPayPrice += $v['payprice'];
                        }
                    }
                    $list[$i]['totalPayPrice'] = sprintf("%.2f", $totalPayPrice);

                    $i++;
                }

            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);

    }


    /**
     * 订单详细
     * @return array
     */
    public function orderDetail(){
        global $dsql;
        global $langData;
        $orderDetail = $cardnum = array();
        $id = $this->param;

        global $userLogin;
        $userid = $userLogin->getMemberID();

        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        if(!is_numeric($id)) return array("state" => 200, "info" => $langData['info'][1][58]);

        //主表信息
        $sql = $dsql->SetQuery("SELECT o.`store` FROM `#@__info_order` o WHERE o.`id` = ".$id);
        $storeinfo = $dsql->dsqlOper($sql, "results");
        if(!empty($storeinfo[0]['store'])){
            $archives = $dsql->SetQuery("SELECT o.* FROM `#@__info_order` o LEFT JOIN `#@__infoshop` s ON s.`id` = o.`store` WHERE (o.`userid` = '$userid' OR s.`uid` = '$userid') AND o.`id` = ".$id);
        }else{
            $archives = $dsql->SetQuery("SELECT o.* FROM `#@__info_order` o WHERE o.`id` = ".$id);
        }
        $results = $dsql->dsqlOper($archives, "results");

        if(!empty($results)){
            $results = $results[0];

            $orderDetail["ordernum"]   = $results["ordernum"];
            $orderDetail["store"]      = $results["store"];
            $orderDetail["orderstate"] = $results["orderstate"];
            $orderDetail["orderdate"]  = $results["orderdate"];
            $orderDetail["common"]     = $results["common"];


            //店铺信息
            $store = array();
            if($results['store'] != 0){
                $storeHandels = new handlers("info", "storeDetail");
                $storeConfig  = $storeHandels->getHandle($results['store']);
                if(is_array($storeConfig) && $storeConfig['state'] == 100){
                    $storeConfig  = $storeConfig['info'];
                    if(is_array($storeConfig)){
                        $store = $storeConfig;
                    }
                }
            }
            $orderDetail['store'] = $store;


            //配送信息
            $orderDetail["username"]    = $results["people"];
            $orderDetail["useraddr"]    = $results["address"];
            $orderDetail["usercontact"] = $results["contact"];
            $orderDetail["note"]        = $results["note"];

            $yunfei = $dsql->SetQuery("SELECT `yunfei` FROM `#@__infolist` WHERE `id` = {$results['prod']}");
            $yunfei = $dsql->dsqlOper($yunfei, "results");
            $orderDetail["yunfei"]        = $yunfei[0]["yunfei"];


            //未付款的提供付款链接
            if($results['orderstate'] == 0){
                $RenrenCrypt = new RenrenCrypt();
                $encodeid = base64_encode($RenrenCrypt->php_encrypt($results["ordernum"]));

                $param = array(
                    "service"     => "info",
                    "template"    => "pay",
                    "param"       => "ordernum=".$encodeid
                );
                $payurl = getUrlPath($param);

                $orderDetail["payurl"] = $payurl;
            }


            //支付方式
            $paySql = $dsql->SetQuery("SELECT `pay_name` FROM `#@__site_payment` WHERE `pay_code` = '".$results["paytype"]."'");
            $payResult = $dsql->dsqlOper($paySql, "results");
            if(!empty($payResult)){
                $orderDetail["paytype"]   = $payResult[0]["pay_name"];
            }else{
                global $cfg_pointName;
                $payname = "";
                if($results["paytype"] == "point,money"){
                    $payname = $cfg_pointName."+" . $langData['siteConfig'][19][363]; //余额
                }elseif($results["paytype"] == "point"){
                    $payname = $cfg_pointName;
                }elseif($results["paytype"] == "余额"){
                    $payname = $langData['siteConfig'][19][363]; //余额
                }
                $orderDetail["paytype"]   = $payname;
            }

            $orderDetail["paydate"]   = $results["paydate"];

            //快递公司&单号
            $orderDetail["expCompany"] = $results["exp-company"];
            $orderDetail["expNumber"]  = $results["exp-number"];
            $orderDetail["expDate"]    = $results["exp-date"];

            //卖家回复
            $orderDetail["retSnote"]    = $results["ret-s-note"];
            $imglist = array();
            $pics = $results['ret-s-pics'];
            if(!empty($pics)){
                $pics = explode(",", $pics);
                foreach ($pics as $key => $value) {
                    $imglist[$key]['val'] = $value;
                    $imglist[$key]['path'] = getFilePath($value);
                }
            }

            $orderDetail["retSpics"]    = $imglist;
            $orderDetail["retSdate"]    = $results["ret-s-date"];


            //退款状态
            $orderDetail["retState"]    = $results["ret-state"];

            //退款原因
            $orderDetail["retType"]    = $results["ret-type"];
            $orderDetail["retNote"]    = $results["ret-note"];

            $imglist = array();
            $pics = $results['ret-pics'];
            if(!empty($pics)){
                $pics = explode(",", $pics);
                foreach ($pics as $key => $value) {
                    $imglist[$key]['val'] = $value;
                    $imglist[$key]['path'] = getFilePath($value);
                }
            }

            $orderDetail["retPics"]    = $imglist;
            $orderDetail["retDate"]    = $results["ret-date"];

            //退款确定时间
            $orderDetail["retOkdate"]    = $results["ret-ok-date"];
            $orderDetail['now'] = GetMkTime(time());


            //商品列表
            $totalPoint = 0;
            $totalBalance = 0;
            $totalPayPrice = 0;
            $totalYunfei = 0;

            $sql = $dsql->SetQuery("SELECT * FROM `#@__infolist` WHERE `id` = ".$results['prod']);
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $p = 0;
                $proDetail = array();
                foreach ($ret as $key => $value) {

                    //查询商品详细信息
                    global $oper;
                    $oper = "user";
                    $this->param = $value['id'];
                    $detailConfig = $this->detail();

                    $proDetail[$p]['id']        = $detailConfig['id'];
                    $proDetail[$p]['title']     = $detailConfig['title'];
                    $proDetail[$p]['litpic']    = $detailConfig['imglist'];
                    $proDetail[$p]['speid']     = $value['speid'];
                    $proDetail[$p]['specation'] = $value['specation'];
                    $proDetail[$p]['price']     = $value['price'];
                    $proDetail[$p]['count']     = 1;
                    $proDetail[$p]['yunfei']  = $value['yunfei'];
                    $proDetail[$p]['discount']  = 0;
                    $proDetail[$p]['point']     = $results['point'];
                    $proDetail[$p]['balance']   = $results['balance'];


                    //评价
                    if($results['orderstate'] == 3){

                        $sql = $dsql->SetQuery("SELECT `rating`, `score1`, `score2`, `score3`, `pics`, `content`, `ischeck` FROM `#@__shop_common` WHERE `aid` = ".$id." AND `speid` = '".$value['speid']."' AND `pid` = ".$value['proid']);
                        $ret = $dsql->dsqlOper($sql, "results");
                        $common = array();
                        if($ret){
                            if(!empty($ret[0]['pics'])){
                                $picArr = array();
                                $pics = explode(",", $ret[0]['pics']);
                                foreach ($pics as $k => $v) {
                                    array_push($picArr, array(
                                        "source" => $v,
                                        "url"    => getFilePath($v)
                                    ));
                                }
                                $ret[0]['pics'] = $picArr;
                            }
                            $common = $ret[0];
                        }
                        $proDetail[$p]['common'] = $common;

                    }


                    //如果是未支付的，不计算积分和余额
                    $payprice = $results['orderstate'] == 0 ? $value['price'] + $value['logistic']  : $results['payprice'];
                    $proDetail[$p]['payprice']  = sprintf("%.2f", $payprice);
                    $p++;

                    $totalPoint    += $value['point'];
                    $totalBalance  += $value['balance'];
                    $totalPayPrice += $payprice;

                }
            }

            $orderDetail['product'] = $proDetail;
            $orderDetail['totalBalance'] = sprintf("%.2f", $totalBalance);
            $orderDetail['totalPayPrice'] = sprintf("%.2f", $totalPayPrice);
            $orderDetail['totalPrice'] = sprintf("%.2f", ($totalYunfei + $results['price']));

        }

        return $orderDetail;
    }


    /**
     * 删除订单
     * @return array
     */
    public function delOrder(){
        global $dsql;
        global $userLogin;
        global $langData;

        $id = $this->param['id'];

        if(!is_numeric($id)) return array("state" => 200, "info" => $langData['info'][1][58]);

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        }

        $archives = $dsql->SetQuery("SELECT * FROM `#@__info_order` WHERE `id` = ".$id);
        $results  = $dsql->dsqlOper($archives, "results");
        if($results){
            $results = $results[0];
            if($results['userid'] == $uid){

                if($results['orderstate'] == 0){
                    $archives = $dsql->SetQuery("DELETE FROM `#@__info_order` WHERE `id` = ".$id);
                    $dsql->dsqlOper($archives, "update");

                    $archives = $dsql->SetQuery("UPDATE `#@__infolist` SET  `is_valid` = 0 WHERE `id` = ".$results['prod']);
                    $dsql->dsqlOper($archives, "update");

                    return $langData['siteConfig'][20][444];  //删除成功！
                }else{
                    return array("state" => 101, "info" => $langData['shop'][4][38]);  //订单为不可删除状态！
                }

            }else{
                return array("state" => 101, "info" => $langData['shop'][4][39]);  //权限不足，请确认帐户信息后再进行操作！
            }
        }else{
            return array("state" => 101, "info" => $langData['shop'][4][40]);  //订单不存在，或已经删除！
        }

    }


    /**
     * 商家发货
     */
    public function delivery(){
        global $dsql;
        global $userLogin;
        global $langData;

        $id      = $this->param['id']; // 订单id
        $company = $this->param['company'];
        $number  = $this->param['number'];

        if(empty($id) || empty($company) || empty($number)) return array("state" => 200, "info" => $langData['shop'][4][20]);  //数据不完整，请检查！

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        }

        //验证订单
        $sql = $dsql->SetQuery("SELECT o.`store` FROM `#@__info_order` o WHERE o.`id` = ".$id);
        $storeinfo = $dsql->dsqlOper($sql, "results");
        if(!empty($storeinfo[0]['store'])){
            $archives = $dsql->SetQuery("SELECT o.`id`, o.`userid`, o.`ordernum` FROM `#@__info_order` o LEFT JOIN `#@__infoshop` s ON o.`store` = s.`id` LEFT JOIN `#@__member` m ON s.`uid` = m.`id` WHERE o.`id` = '$id' AND m.`id` = '$uid' AND o.`orderstate` = 1");
        }else{
            $archives = $dsql->SetQuery("SELECT o.`id`, o.`userid`, o.`ordernum` FROM `#@__info_order` o WHERE o.`id` = '$id' AND o.`orderstate` = 1");
        }

        $results  = $dsql->dsqlOper($archives, "results");
        if($results){

            $userid = $results[0]['userid'];
            $ordernum = $results[0]['ordernum'];

            $paramBusi = array(
                "service"  => "member",
                "type"     => "user",
                "template" => "orderdetail",
                "action"   => "info",
                "id"       => $id
            );

            //获取会员名
            $username = "";
            $sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
            }

            //自定义配置
            $config = array(
                "username" => $username,
                "order" => $ordernum,
                "expCompany" => $company,
                "exp_company" => $company,
                "expnumber" => $number,
                "exp_number" => $number,
                "fields" => array(
                    'keyword1' => '订单编号',
                    'keyword2' => '快递公司',
                    'keyword3' => '快递单号'
                )
            );

            updateMemberNotice($userid, "会员-订单发货通知", $paramBusi, $config);


            //更新订单状态
            $now = GetMkTime(time());
            $sql = $dsql->SetQuery("UPDATE `#@__info_order` SET `orderstate` = 3, `exp-company` = '$company', `exp-number` = '$number', `exp-date` = '$now' WHERE `id` = ".$id);
            $dsql->dsqlOper($sql, "update");
            return $langData['siteConfig'][20][244];  //操作成功

        }else{
            return array("state" => 200, "info" => $langData['shop'][4][23]);  //操作失败，请核实订单状态后再操作！
        }

    }


    /**
     * 商家退款
     */
    public function refundPay(){
        global $dsql;
        global $userLogin;
        global $langData;

        $id = $this->param['id'];

        if(empty($id)) return array("state" => 200, "info" => $langData['shop'][4][20]);  //数据不完整，请检查！

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        }

        //验证订单
        $sql = $dsql->SetQuery("SELECT o.`store` FROM `#@__info_order` o WHERE o.`id` = ".$id);
        $storeinfo = $dsql->dsqlOper($sql, "results");
        if(!empty($storeinfo[0]['store'])){
            $archives = $dsql->SetQuery("SELECT o.`id`, o.`ordernum`, o.`userid`, o.`store`, s.`uid`  FROM `#@__info_order` o LEFT JOIN `#@__infoshop` s ON o.`store` = s.`id` LEFT JOIN `#@__member` m ON s.`uid` = m.`id` WHERE o.`id` = '$id' AND m.`id` = '$uid' AND o.`orderstate` = 6 AND o.`ret-state` = 1");
        }else{
            $archives = $dsql->SetQuery("SELECT o.`id`, o.`ordernum`, o.`userid`, o.`store`, o.`prod`  FROM `#@__info_order` o WHERE o.`id` = '$id' AND o.`orderstate` = 6 AND o.`ret-state` = 1");
        }

        $results  = $dsql->dsqlOper($archives, "results");
        if($results){

            $orderid    = $results[0]['id'];         //需要退回的订单ID
            $ordernum   = $results[0]['ordernum'];   //需要退回的订单号
            $userid     = $results[0]['userid'];     //需要退回的会员ID
            if(!empty($results[0]['store'])){
                $uid        = $results[0]['uid'];        //商家会员ID
            }else{
                $sql = $dsql->SetQuery("SELECT `userid` FROM `#@__infolist` WHERE `id` = ".$results[0]['prod']);
                $res = $dsql->dsqlOper($sql, "results");
                $uid = $res[0]['userid'];        //商家会员ID
            }
            $totalMoney = 0;
            $totalPoint = 0;

            $sql = $dsql->SetQuery("SELECT `point`, `balance`, `payprice` FROM `#@__info_order` WHERE `id` = '$orderid'");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $totalMoney = $ret[0]['balance'] + $ret[0]['payprice'];
                $totalPoint = $ret[0]['point'];
            }

            //更新订单状态
            $now = GetMkTime(time());
            $sql = $dsql->SetQuery("UPDATE `#@__info_order` SET `ret-state` = 0, `orderstate` = 7, `ret-ok-date` = '$now' WHERE `id` = ".$id);
            $dsql->dsqlOper($sql, "update");

            //退回积分
            if(!empty($totalPoint)){
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + '$totalPoint' WHERE `id` = '$userid'");
                $dsql->dsqlOper($archives, "update");

                //保存操作日志
                $info = '二手订单退回' . '：' . $ordernum;  //商城订单退回
                $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$totalPoint', '$info', '$now')");
                $dsql->dsqlOper($archives, "update");
            }

            //退回余额
            if($totalMoney > 0){
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$totalMoney' WHERE `id` = '$userid'");
                $dsql->dsqlOper($archives, "update");

                //保存操作日志
                $info = '二手订单退回' . '：' . $ordernum;  //商城订单退回
                $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$totalMoney', '$info', '$now')");
                $dsql->dsqlOper($archives, "update");


                //减去冻结金额
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` - '$totalMoney' WHERE `id` = '$userid'");
                $dsql->dsqlOper($archives, "update");

                //如果冻结金额小于0，重置冻结金额为0
                $archives = $dsql->SetQuery("SELECT `freeze` FROM `#@__member` WHERE `id` = '$userid'");
                $ret = $dsql->dsqlOper($archives, "results");
                if($ret){
                    if($ret[0]['freeze'] < 0){
                        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = 0 WHERE `id` = '$userid'");
                        $dsql->dsqlOper($archives, "update");
                    }
                }

            }


            $paramBusi = array(
                "service"  => "member",
                "type"     => "user",
                "template" => "orderdetail",
                "action"   => "info",
                "id"       => $id
            );

            //获取会员名
            $username = "";
            $sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
            }

            //自定义配置
            $config = array(
                "username" => $username,
                "order" => $ordernum,
                "amount" => $totalMoney,
                "fields" => array(
                    'keyword1' => '退款状态',
                    'keyword2' => '退款金额',
                    'keyword3' => '审核说明'
                )
            );

            updateMemberNotice($userid, "会员-订单退款成功", $paramBusi, $config);



            return $langData['siteConfig'][9][34];  //退款成功

        }else{
            return array("state" => 200, "info" => $langData['siteConfig'][0][23]);  //操作失败，请核实订单状态后再操作！
        }

    }


    /**
     * 商家退款回复
     */
    public function refundReply(){
        global $dsql;
        global $userLogin;
        global $langData;

        $id      = $this->param['id'];
        $pics    = $this->param['pics'];
        $content = $this->param['content'];

        if(empty($id)) return array("state" => 200, "info" => $langData['shop'][4][20]);  //数据不完整，请检查！
        if(empty($content)) return array("state" => 200, "info" => $langData['shop'][4][26]);  //请输入回复内容！

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        }

        $content = filterSensitiveWords(addslashes($content));
        $content = cn_substrR($content, 500);

        //验证订单
        $sql = $dsql->SetQuery("SELECT o.`store` FROM `#@__info_order` o WHERE o.`id` = ".$id);
        $storeinfo = $dsql->dsqlOper($sql, "results");
        if(!empty($storeinfo[0]['store'])){
            $archives = $dsql->SetQuery("SELECT o.`id`, o.`userid`, o.`ordernum`, o.`prod` FROM `#@__info_order` o LEFT JOIN `#@__infoshop` s ON o.`store` = s.`id` LEFT JOIN `#@__member` m ON s.`uid` = m.`id` WHERE o.`id` = '$id' AND m.`id` = '$uid' AND o.`orderstate` = 6 AND o.`ret-state` = 1");
        }else{
            $archives = $dsql->SetQuery("SELECT o.`id`, o.`userid`, o.`ordernum`, o.`prod` FROM `#@__info_order` o WHERE o.`id` = '$id' AND o.`orderstate` = 6 AND o.`ret-state` = 1");
        }
        $results  = $dsql->dsqlOper($archives, "results");
        if($results){

            $userid = $results[0]['userid'];
            $ordernum = $results[0]['ordernum'];
            $prod = $results[0]['prod'];

            // 查询订单商品
            $orderprice = 0;
            $arc = $dsql->SetQuery("SELECT `yunfei`, `price` FROM `#@__infolist` WHERE `id` = ".$prod);
            $proList = $dsql->dsqlOper($arc, "results");
            if($proList){
                foreach ($proList as $key => $value) {
                    $orderprice = $value['price'] + $value['yunfei'];
                }
            }

            $paramBusi = array(
                "service"  => "member",
                "type"     => "user",
                "template" => "orderdetail",
                "action"   => "info",
                "id"       => $id
            );

            //获取会员名
            $username = "";
            $sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
            }

            //自定义配置
            $config = array(
                "username" => $username,
                "order" => $ordernum,
                "amount" => $orderprice,
                "info" => $content,
                "fields" => array(
                    'keyword1' => '退款状态',
                    'keyword2' => '退款金额',
                    'keyword3' => '审核说明'
                )
            );

            updateMemberNotice($userid, "会员-退款申请卖家回复", $paramBusi, $config);

            //更新订单状态
            $now = GetMkTime(time());
            $sql = $dsql->SetQuery("UPDATE `#@__info_order` SET `ret-s-note` = '$content', `ret-s-pics` = '$pics', `ret-s-date` = '$now' WHERE `id` = ".$id);
            $dsql->dsqlOper($sql, "update");

            return $langData['siteConfig'][21][147];  //回复成功！

        }else{
            return array("state" => 200, "info" => $langData['shop'][4][27]);  //回复失败，请核实订单状态后再操作！
        }
    }


    /**
     * 买家申请退款
     */
    public function refund(){
        global $dsql;
        global $userLogin;
        global $langData;

        $id      = $this->param['id'];
        $type    = $this->param['type'];
        $pics    = $this->param['pics'];
        $content = $this->param['content'];

        if(empty($id)) return array("state" => 200, "info" => $langData['shop'][4][20]);  //数据不完整，请检查！
        if(empty($type)) return array("state" => 200, "info" => $langData['shop'][4][21]);  //请选择退款原因！
        if(empty($content)) return array("state" => 200, "info" => $langData['shop'][4][22]);  //请输入退款说明！

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        }

        $type    = filterSensitiveWords(addslashes($type));
        $content = filterSensitiveWords(addslashes($content));
        $type    = cn_substrR($type, 20);
        $content = cn_substrR($content, 500);

        //验证订单
        $sql = $dsql->SetQuery("SELECT o.`store` FROM `#@__info_order` o WHERE o.`id` = ".$id);
        $storeinfo = $dsql->dsqlOper($sql, "results");
        if(!empty($storeinfo[0]['store'])){
            $archives = $dsql->SetQuery("SELECT o.`id`, o.`ordernum`, o.`prod`, o.`store`, s.`uid` FROM `#@__info_order` o LEFT JOIN `#@__infoshop` s ON s.`id` = o.`store` WHERE o.`id` = '$id' AND o.`userid` = '$uid' AND (o.`orderstate` = 1 OR o.`orderstate` = 6 OR (o.`orderstate` = 2 AND o.`paydate` != 0)) AND o.`ret-state` = 0");
        }else{
            $archives = $dsql->SetQuery("SELECT o.`id`, o.`ordernum`, o.`prod`, o.`store` FROM `#@__info_order` o  WHERE o.`id` = '$id' AND o.`userid` = '$uid' AND (o.`orderstate` = 1 OR o.`orderstate` = 6 OR (o.`orderstate` = 2 AND o.`paydate` != 0)) AND o.`ret-state` = 0");
        }

        $results  = $dsql->dsqlOper($archives, "results");
        if($results){
            if(!empty($results[0]['store'])){
                $userid = $results[0]['uid'];  //卖家会员ID
            }else{
                $sql    = $dsql->SetQuery("SELECT `userid` FROM `#@__infolist` WHERE `id` = ".$results[0]['prod']);
                $res    = $dsql->dsqlOper($sql, "results");
                $userid = $res[0]['userid'];   //卖家会员ID
            }

            $ordernum = $results[0]['ordernum'];  //订单号
            $prod = $results[0]['prod'];  //订单号

            // 查询订单商品
            $orderprice = 0;
            $arc = $dsql->SetQuery("SELECT `yunfei`, `price` FROM `#@__infolist` WHERE `id` = ".$prod);
            $proList = $dsql->dsqlOper($arc, "results");
            if($proList){
                foreach ($proList as $key => $value) {
                    $orderprice += $value['price'] + $value['yunfei'];
                }
            }

            $paramBusi = array(
                "service"  => "member",
                "template" => "orderdetail",
                "action"   => "info",
                "id"       => $id
            );
            if(!empty($results[0]['store'])){
                $paramBusi['type'] = 'user';
                $paramBusi['param'] = 'type=out';
            }

            //获取会员名
            $username = "";
            $sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $uid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
            }

            //自定义配置
            $config = array(
                "username" => $username,
                "order" => $ordernum,
                "amount" => $orderprice,
                "info" => $content,
                "fields" => array(
                    'keyword1' => '退款原因',
                    'keyword2' => '退款金额'
                )
            );

            updateMemberNotice($userid, "会员-订单退款通知", $paramBusi, $config);


            $date       = GetMkTime(time());
            $sql = $dsql->SetQuery("UPDATE `#@__info_order` SET `orderstate` = 6, `ret-state` = 1, `ret-type` = '$type', `ret-note` = '$content', `ret-pics` = '$pics', `ret-date` = '$date' WHERE `id` = ".$id);
            $dsql->dsqlOper($sql, "update");

            return $langData['siteConfig'][20][244];  //操作成功

        }else{
            return array("state" => 200, "info" => $langData['shop'][4][23]);  //操作失败，请核实订单状态后再操作！
        }
    }


    /**
     * 买家确认收货
     */
    public function receipt(){
        global $dsql;
        global $userLogin;
        global $langData;

        $id = $this->param['id'];

        if(empty($id)) return array("state" => 200, "info" => $langData['shop'][4][24]);  //操作失败，参数传递错误！

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        }

        //验证订单
        $sql = $dsql->SetQuery("SELECT o.`store` FROM `#@__info_order` o WHERE o.`id` = ".$id);
        $storeinfo = $dsql->dsqlOper($sql, "results");
        if(!empty($storeinfo[0]['store'])){
            $archives = $dsql->SetQuery("SELECT o.`ordernum`, o.`userid`, o.`store`, o.`prod`, s.`uid` uid FROM `#@__info_order` o LEFT JOIN `#@__infoshop` s ON s.`id` = o.`store` WHERE o.`id` = '$id' AND o.`userid` = '$uid' AND o.`orderstate` = 3");
        }else{
            $archives = $dsql->SetQuery("SELECT o.`ordernum`, o.`userid`, o.`store`, o.`prod` FROM `#@__info_order` o WHERE o.`id` = '$id' AND o.`userid` = '$uid' AND o.`orderstate` = 3");
        }

        $results  = $dsql->dsqlOper($archives, "results");
        if($results){

            //更新订单状态
            $sql = $dsql->SetQuery("UPDATE `#@__info_order` SET `orderstate` = '4' WHERE `id` = ".$id);
            $dsql->dsqlOper($sql, "update");


            //将订单费用转至商家账户
            $ordernum = $results[0]['ordernum'];
            $userid   = $results[0]['userid'];
            if(!empty($results[0]['store'])){
                $uid      = $results[0]['uid'];
            }else{
                $sql = $dsql->SetQuery("SELECT `userid` FROM `#@__infolist` WHERE `id` = ".$results[0]['prod']);
                $res = $dsql->dsqlOper($sql, "results");
                $uid = $res[0]['userid'];        //商家会员ID
            }
            $totalMoney = 0;
            $freezeMoney = 0;

            //计算费用
            $sql = $dsql->SetQuery("SELECT `price`, `point`, `balance`, `prod`, `payprice` FROM `#@__info_order` WHERE `id` = $id");
            $ret = $dsql->dsqlOper($sql, "results");

            if($ret){
                $sql2 = $dsql->SetQuery("SELECT `yunfei` FROM `#@__infolist` WHERE `id` = ". $ret[0]['prod']);
                $ret2 = $dsql->dsqlOper($sql2, "results");
                if($ret2){
                    $yunfei = $ret2[0]['yunfei'];
                }
                // $totalMoney = $ret[0]['price']  + $yunfei;
                $totalMoney = $ret[0]['price']; //swa190326
                $freezeMoney = $ret[0]['balance'] + $ret[0]['payprice'];
            }

            if($totalMoney > 0){

                //扣除佣金
                global $cfg_shopFee;
                $cfg_shopFee = (float)$cfg_shopFee;

                $fee = $totalMoney * $cfg_shopFee / 100;
                $fee = $fee < 0.01 ? 0 : $fee;
                $totalMoney_ = sprintf('%.2f', $totalMoney - $fee);

                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$totalMoney_' WHERE `id` = '$uid'");
                $dsql->dsqlOper($archives, "update");

                //保存操作日志
                $now = GetMkTime(time());
                $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '1', '$totalMoney_', '二手订单：$ordernum', '$now')");
                $dsql->dsqlOper($archives, "update");
            }


            //减去冻结金额
            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` - '$freezeMoney' WHERE `id` = '$userid'");
            $dsql->dsqlOper($archives, "update");

            //如果冻结金额小于0，重置冻结金额为0
            $archives = $dsql->SetQuery("SELECT `freeze` FROM `#@__member` WHERE `id` = '$userid'");
            $ret = $dsql->dsqlOper($archives, "results");
            if($ret){
                if($ret[0]['freeze'] < 0){
                    $archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = 0 WHERE `id` = '$userid'");
                    $dsql->dsqlOper($archives, "update");
                }
            }

            //保存操作日志
            $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$freezeMoney', '商城消费：$ordernum', '$now')");
            $dsql->dsqlOper($archives, "update");


            //商家会员消息通知
            $paramBusi = array(
                "service"  => "member",
                "template" => "orderdetail",
                "action"   => "shop",
                "id"       => $id
            );

            //获取会员名
            $username = "";
            $sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $uid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
            }

            //自定义配置
			$config = array(
				"username" => $username,
				"title" => $ordernum,
				"amount" => $totalMoney,
				"fields" => array(
					'keyword1' => '商品信息',
					'keyword2' => '订单金额',
					'keyword3' => '订单状态'
				)
			);

            updateMemberNotice($uid, "会员-商品成交通知", $paramBusi, $config);

            //返积分
            (new member())->returnPoint("info", $userid, $totalMoney, $ordernum);

            return $langData['siteConfig'][20][244];  //操作成功

        }else{
            return array("state" => 200, "info" => $langData['shop'][4][23]);  //操作失败，请核实订单状态后再操作！
        }
    }


    /**
     * 关注
     * @return array
     */
    public function follow()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $userid = $userLogin->getMemberID();
        if ($userid == -1) {
            return array('state' => 200, 'info' => $langData['info'][2][25]);//'登录超时！'
        }
        $param = $this->param;
        if (!empty($param)) {

            $vid  = $param['vid'];
            $type = $param['type'];
            $temp = $param['temp'];
        } else {
            return array('state' => 200, 'info' => $langData['info'][2][26]);//参数不正确！
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
                return array('state' => 200, 'info' => $langData['info'][2][27]);//'您不可以关注自己！'
            }
        }

        if ($type) {
            //查看是否已经关注
            $sql = $dsql->SetQuery("SELECT * FROM `#@__site_followmap` WHERE `userid` = $userid AND `userid_b` = $userid_b AND `temp` = '$temp'");
            $ret = $dsql->dsqlOper($sql, "results");
            if ($ret) {
                return array('state' => 200, 'info' => $langData['info'][2][28]);//'您已关注！'
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

    /**
     * 评论列表
     * @return array
     */
    public function shopcommon()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $infoid   = $orderby = $page = $pageSize = $where = "";

        if (!is_array($this->param)) {
            return array("state" => 200, "info" => $langData['info'][1][58]);
        } else {
            $infoid   = $this->param['infoid'];
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

        $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__info_shopcommon` WHERE `pid` = " . $infoid . " AND `ischeck` = 1 AND `floor` = 0" . $oby);
        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount / $pageSize);

        if ($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

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

                // $list[$key]['lower'] = $this->shopgetCommonList($val['id']);
                $lower = null;
                $param['fid'] = $val['id'];
                $param['page'] = 1;
                $param['pageSize'] = 100;
                $this->param = $param;
                $child = $this->getCommonList();

                if(!isset($child['state']) || $child['state'] != 200){
                    $lower = $child['list'];
                }

                $list[$key]['lower'] = $lower;
            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);
    }


    /**
     * 商家遍历评论子级
     * @param $fid int 评论ID
     * @return array
     */
    function shopgetCommonList()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        // if (empty($fid)) return false;
        $param    = $this->param;
        $fid      = (int)$param['fid'];
        $page     = (int)$this->param['page'];
        $pageSize = (int)$this->param['pageSize'];

        $pageSize = empty($pageSize) ? 99999 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if($fid){
            $where = " AND `floor` = '$fid'";
        }

        $where .= " AND `ischeck` = 1";

        $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__info_shopcommon` WHERE `floor` = " . $fid . " AND `ischeck` = 1 ORDER BY `id` DESC");
        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

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

                    // $list[$key]['lower'] = $this->getCommonList($val['id']);
                    $lower = null;
                    $param['fid'] = $val['id'];
                    $param['page'] = 1;
                    $param['pageSize'] = 100;
                    $this->param = $param;
                    $child = $this->shopgetCommonList();
                    if(!isset($child['state']) || $child['state'] != 200){
                        $lower = $child['list'];
                    }

                    $list[$key]['lower'] = $lower;
                }
                // return $list;
                return array("pageInfo" => $pageinfo, "list" => $list);
            }
        }
    }


    /**
     * 商家顶评论
     * @param $id int 评论ID
     * @param string
     **/
    public function shopdingCommon()
    {
        global $dsql;
        global $userLogin;
        global $langData;

        $id = $this->param['id'];
        if (empty($id)) return $langData['info'][1][74];//"请传递评论ID！"
        $memberID = $userLogin->getMemberID();
        if ($memberID == -1 || empty($memberID)) return $langData['info'][1][75];//请先登录！

        $archives = $dsql->SetQuery("SELECT `duser` FROM `#@__info_shopcommon` WHERE `id` = " . $id);
        $results  = $dsql->dsqlOper($archives, "results");
        if ($results) {

            $duser = $results[0]['duser'];

            //如果此会员已经顶过则return
            $userArr = explode(",", $duser);
            if (in_array($userLogin->getMemberID(), $userArr)) return $langData['info'][1][76];//已顶过！

            //附加会员ID
            if (empty($duser)) {
                $nuser = $userLogin->getMemberID();
            } else {
                $nuser = $duser . "," . $userLogin->getMemberID();
            }

            $archives = $dsql->SetQuery("UPDATE `#@__info_shopcommon` SET `good` = `good` + 1 WHERE `id` = " . $id);
            $results  = $dsql->dsqlOper($archives, "update");

            $archives = $dsql->SetQuery("UPDATE `#@__info_shopcommon` SET `duser` = '$nuser' WHERE `id` = " . $id);
            $results  = $dsql->dsqlOper($archives, "update");
            return $results;

        } else {
            return $langData['info'][1][77];//评论不存在或已删除！
        }
    }


    /**
     * 商家发表评论
     * @return array
     */
    public function shopsendCommon()
    {
        global $dsql;
        global $userLogin;
        global $langData;
        $param = $this->param;

        $aid     = $param['aid'];
        $id      = $param['id'];
        $content = addslashes($param['content']);

        if (empty($aid) || empty($content)) {
            return array("state" => 200, "info" => $langData['info'][1][78]);//'必填项不得为空！'
        }

        $content = filterSensitiveWords(cn_substrR($content, 250));

        include HUONIAOINC . "/config/info.inc.php";
        $state = (int)$customCommentCheck;

        $archives = $dsql->SetQuery("INSERT INTO `#@__info_shopcommon` (`pid`, `floor`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `ischeck`, `duser`) VALUES ('$aid', '$id', '" . $userLogin->getMemberID() . "', '$content', " . GetMkTime(time()) . ", '" . GetIP() . "', '" . getIpAddr(GetIP()) . "', 0, 0, '$state', '')");
        $lid      = $dsql->dsqlOper($archives, "lastid");
        if ($lid) {
            $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__info_shopcommon` WHERE `id` = " . $lid);
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
            return array("state" => 200, "info" => $langData['info'][1][79]);//'评论失败！'
        }

    }

    /**
     * 商家评价详情
     */
    public function shopcommentDetail(){
        global $dsql;
        global $userLogin;

        $uid = $userLogin->getMemberID();

        $param = $this->param;
        $id    = (int)$param['id'];

        $sql = $dsql->SetQuery("SELECT * FROM `#@__info_shopcommon` WHERE `id` = $id AND `isCheck` = 1 ");//print_R($sql);exit;
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $detail = array();
            $zan_has = 0;
            $ret = $ret[0];
            foreach ($ret as $key => $value) {

                //获取父级内容
                if($key == "floor"){
                    if($value){
                        $content  = '';
                        $username = '';
                        $sql = $dsql->SetQuery("SELECT `content`, `userid` FROM `#@__info_shopcommon` WHERE `id` = '$value' AND `isCheck` = 1 ");
                        $par = $dsql->dsqlOper($sql, "results");
                        if($par){
                            $content = $par[0]['content'];

                            $sql = $dsql->SetQuery("SELECT `id`, `mtype`, `nickname`, `company` FROM `#@__member` WHERE `id` IN (".$par[0]['userid'].")");
                            $res = $dsql->dsqlOper($sql, "results");
                            if($res[0]['mtype'] == 2){
                                $username = $res[0]['company'] ? $res[0]['company'] : $res[0]['nickname'];
                            }else{
                                $username = $res[0]['nickname'];
                            }
                        }
                        $detail['parcontent'] = $content;
                        $detail['parusername'] = $username;
                    }
                }

                if($key == "duser"){
                    $zan_userArr = array();
                    if($value){
                        $sql = $dsql->SetQuery("SELECT `id`, `mtype`, `nickname`, `company`, `photo` FROM `#@__member` WHERE `id` IN (".$value.")");
                        $res = $dsql->dsqlOper($sql, "results");
                        if($res){
                            $value_ = explode(",", $value);
                            if($uid != -1 && in_array($uid, $value_)){
                                $zan_has = 1;
                            }
                            foreach ($value_ as $k => $v) {
                                foreach ($res as $s => $sv) {
                                    if($sv['id'] == $v){
                                        if($sv['mtype'] == "2"){
                                            $nickname = $sv['company'] ? $sv['company'] : $sv['nickname'];
                                        }else{
                                            $nickname = $sv['nickname'];
                                        }
                                        $photo = $sv['photo'] ? getFilePath($sv['photo']) : "";
                                        $zan_userArr[] = array(
                                            "id" => $v,
                                            "nickname" => $nickname,
                                            "photo" => $photo
                                        );
                                    }
                                }
                            }
                        }
                    }
                    $detail['zan_userArr'] = $zan_userArr;
                }

                $detail[$key] = $value;
            }

            $detail['zan_has'] = $zan_has;

            if($ret['isanony']){
                $detail['user'] = array(
                    "id" => 0,
                    "nickname" => "匿名用户",
                    "photo" => ""
                );
            }else{
                $sql = $dsql->SetQuery("SELECT `id`, `mtype`, `nickname`, `company`, `photo` FROM `#@__member` WHERE `id` = " . $ret['userid']);
                $res = $dsql->dsqlOper($sql, "results");
                if(!empty($res[0]['id'])){
                    if($res[0]['mtype'] == "2"){
                        $nickname = $res[0]['company'] ? $res[0]['company'] : $res[0]['nickname'];
                    }else{
                        $nickname = $res[0]['nickname'];
                    }
                    $photo = $res[0]['photo'] ? getFilePath($res[0]['photo']) : "";
                    $userinfo= array(
                        "id" => $res[0]['id'],
                        "nickname" => $nickname,
                        "photo" => $photo
                    );
                }
                $detail['user'] = $userinfo;
            }
            return $detail;
        }
    }

}
