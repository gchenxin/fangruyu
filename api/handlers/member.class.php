<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 会员模块API接口
 *
 * @version        $Id: member.class.php 2015-6-11 上午10:19:21 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class member {
    private $param;  //参数
    public static $langData;
	const MSG_TEMPID=56;
    /**
     * 构造函数
     *
     * @param string $action 动作名
     */
    public function __construct($param = array()){
        $this->param = $param;
        $this->temp = array();
        global $langData;
        self::$langData = $langData;
    }

    /**
     * 基本参数
     * @return array
     */
    public function config(){

        global $cfg_basehost;          //系统主域名
        global $cfg_secureAccess;

        global $cfg_userSubDomain;     //个人会员
        global $cfg_busiSubDomain;     //企业会员

        $params = !empty($this->param) && !is_array($this->param) ? explode(',',$this->param) : "";

        //个人会员
        // $userDomainInfo = getDomain('member', 'user');
        // $userChannelDomain = $userDomainInfo['domain'];
        // if($cfg_userSubDomain == 0){
        // 	$userChannelDomain = "http://".$userChannelDomain;
        // }elseif($cfg_userSubDomain == 1){
        // 	$userChannelDomain = "http://".$userChannelDomain.".".$cfg_basehost;
        // }elseif($cfg_userSubDomain == 2){
        // 	$userChannelDomain = "http://".$cfg_basehost."/".$userChannelDomain;
        // }

        //企业会员
        // $busiDomainInfo = getDomain('member', 'busi');
        // $busiChannelDomain = $busiDomainInfo['domain'];
        // if($cfg_busiSubDomain == 0){
        // 	$busiChannelDomain = "http://".$busiChannelDomain;
        // }elseif($cfg_busiSubDomain == 1){
        // 	$busiChannelDomain = "http://".$busiChannelDomain.".".$cfg_basehost;
        // }elseif($cfg_busiSubDomain == 2){
        // 	$busiChannelDomain = "http://".$cfg_basehost."/".$busiChannelDomain;
        // }

        // include HUONIAOINC.'/siteModuleDomain.inc.php';
        // $userChannelDomain = $userDomain;
        // $busiChannelDomain = $busiDomain;

        //个人会员
        $userDomainInfo = getDomain('member', 'user');
        $userChannelDomain = $userDomainInfo['domain'];
        if($cfg_userSubDomain == 0){
            $userChannelDomain = $cfg_secureAccess.$userChannelDomain;
        }elseif($cfg_userSubDomain == 1){
            $userChannelDomain = $cfg_secureAccess.$userChannelDomain.".".str_replace("www.", "", $cfg_basehost);
        }elseif($cfg_userSubDomain == 2){
            $userChannelDomain = $cfg_secureAccess.$cfg_basehost."/".$userChannelDomain;
        }

        // $siteDomainInc .= "\$userDomain = '".$userChannelDomain."';\r\n";

        //企业会员
        $busiDomainInfo = getDomain('member', 'busi');
        $busiChannelDomain = $busiDomainInfo['domain'];
        if($cfg_busiSubDomain == 0){
            $busiChannelDomain = $cfg_secureAccess.$busiChannelDomain;
        }elseif($cfg_busiSubDomain == 1){
            $busiChannelDomain = $cfg_secureAccess.$busiChannelDomain.".".str_replace("www.", "", $cfg_basehost);
        }elseif($cfg_busiSubDomain == 2){
            $busiChannelDomain = $cfg_secureAccess.$cfg_basehost."/".$busiChannelDomain;
        }

        // $siteDomainInc .= "\$busiDomain = '".$busiChannelDomain."';\r\n";

        $return = array();
        if(!empty($params) > 0){

            foreach($params as $key => $param){
                if($param == "userDomain"){
                    $return['userDomain'] = $userChannelDomain;
                }elseif($param == "busiDomain"){
                    $return['busiDomain'] = $busiChannelDomain;
                }
            }

        }else{
            $return['userDomain'] = $userChannelDomain;
            $return['busiDomain'] = $busiChannelDomain;
        }

        return $return;

    }


    /**
     * 会员信息详情
     * @return array
     */
    public function detail($uid = 0, $isloginUser = false){
        global $dsql;
        global $userLogin;
        global $langData;
        global $installModuleArr;
        global $HN_memory;
        $detail = array();
        $id = $uid ? $uid : $this->param;

        $userid = $userLogin->getMemberID();

        $shop = 0;  //是否查询会员开通店铺信息
        $friend = 0;  //是否验证是否为好友
        $from = 0;  //与哪个会员验证是否为好友

        // $id = is_array($id) ? $id['id'] : $id;
        if(is_array($id)){

            $shop = (int)$id['shop'];
            $friend = (int)$id['friend'];
            $from = (int)$id['from'];

            if(isset($id['userkey'])){
                $userkey = $id['userkey'];
                $RenrenCrypt = new RenrenCrypt();
                $userinfo = $RenrenCrypt->php_decrypt(base64_decode($userkey));
                $userinfo = explode('@@@@', $userinfo);

                $openid = $userinfo[0];
                $session_key = $userinfo[1];

                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `wechat_mini_openid` = '$openid'");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $id = $ret[0]['id'];
                }else{
                    $id = $id['id'];
                }
            }else{
                $id = $id['id'];
            }
        }

        $id = (int)$id;

        if($id <= 0) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);

        //读缓存
        $memberCache = $HN_memory->get('member_' . $id);
        if($memberCache){
            $results = $memberCache;
        }else {
            $archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `state` = 1 AND `mtype` != 0 AND `mtype` != 3 AND `id` = " . $id);
            $results = $dsql->dsqlOper($archives, "results");
        }
        if($results && is_array($results)){

            //写入缓存
            $HN_memory->set('member_' . $id, $results);

            $detail['userid'] = $results[0]['id'];

            //验证会员是否已经登录，如果没有登录，将不输出重要信息
            if($userid == $id || $isloginUser){
                $RenrenCrypt = new RenrenCrypt();
                $detail['userid_encode'] = base64_encode($RenrenCrypt->php_encrypt($results[0]['id'] . '&' . $results[0]['password']));
            }

            global $cfg_cookiePre;
            $detail['cookiePre'] = $cfg_cookiePre . 'login_user';

            $detail['userType'] = $results[0]['mtype'];
            $detail['level'] = $results[0]['level'];

            $levelName = $langData['siteConfig'][21][31];   //普通会员
            $levelIcon = "";

            //查询
            if($results[0]['level']){
                $sql = $dsql->SetQuery("SELECT `name`, `icon` FROM `#@__member_level` WHERE `id` = " . $results[0]['level']);
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $levelName = $ret[0]['name'];
                    $levelIcon = empty($ret[0]['icon']) ? "" : getFilePath($ret[0]['icon']);
                }
            }
            $detail['levelName'] = $levelName;
            $detail['levelIcon'] = $levelIcon;
            $detail['expired'] = $results[0]['expired'];

            $detail['username'] = $results[0]['username'];

            $detail['pwd'] = $results[0]['password'] ? 1 : 0;  //如果密码不为空，则输出1，如果没有设置过密码，输出0

            if($results[0]['mtype'] == 2){
                $detail['nickname'] = !empty($results[0]['company']) ? $results[0]['company'] : $results[0]['nickname'];
                $detail['person'] = $results[0]['realname'];
            }else{
                $detail['nickname'] = $results[0]['nickname'];
            }
            $detail['certifyState'] = $results[0]['certifyState'];
            $detail['certifyInfo'] = $results[0]['certifyInfo'];
            $detail['idcard'] = preg_replace('/([0-9]{5})[0-9]{11}(.*?)/is',"$1*************$2", $results[0]['idcard']);
            $detail['paypwdCheck'] = $results[0]['paypwdCheck'];
            $detail['email'] = $results[0]['email'];
            $detail['emailEncrypt'] = preg_replace('/([0-9a-zA-Z]{3})(.*?)@(.*?)/is',"$1***@$3", $results[0]['email']);
            $detail['emailCheck'] = $results[0]['emailCheck'];
            $detail['areaCode'] = empty($results[0]['areaCode']) ? "86" : $results[0]['areaCode'];
            $detail['phone'] = $results[0]['phone'];
            $detail['phoneEncrypt'] = preg_replace('/(1[34578]{1}[0-9])[0-9]{4}([0-9]{4})/is',"$1****$2", $results[0]['phone']);
            $detail['phoneCheck'] = $results[0]['phoneCheck'];
            $detail['qq'] = $results[0]['qq'];
            $detail['photo'] = strstr($results[0]['photo'], 'http') ? $results[0]['photo'] : getFilePath($results[0]['photo']);
            $detail['photoSource'] = $results[0]['photo'];
            $detail['sex'] = $results[0]['sex'];
            $detail['birthday'] = $results[0]['birthday'];

            $sql = $dsql->SetQuery("SELECT log.`id` FROM `#@__member_letter_log` log LEFT JOIN `#@__member_letter` l ON l.`id` = log.`lid` WHERE log.`state` = 0 AND l.`type` = 0 AND log.`uid` = $id");
            $msgnum = $dsql->dsqlOper($sql, "totalCount");

            $detail['message']  = $msgnum;

            global $cfg_ucenterLinks;
            // $detail['money']  = strstr($cfg_ucenterLinks, 'balance') ? $results[0]['money'] : 0;
            $detail['money']  = $results[0]['money'];
            $detail['freeze']  = $results[0]['freeze'];

            global $cfg_pointState;
            $detail['point']  = $cfg_pointState ? $results[0]['point'] : 0;
            $detail['promotion']  = $results[0]['promotion'];
            $detail['regtime']  = date("Y-m-d H:i:s", $results[0]['regtime']);
            $detail['regtimeold']  = $results[0]['regtime'];
            $detail['regip']  = $results[0]['regip'];
            $detail['regipaddr']  = $results[0]['regipaddr'];
            $detail['lastlogintime']  = $results[0]['lastlogintime'] ? date("Y-m-d H:i:s", $results[0]['lastlogintime']) : "";
            $detail['lastloginip']  = $results[0]['lastloginip'];
            $detail['lastloginipaddr']  = $results[0]['lastloginipaddr'];
            $detail['tempbg']  = $results[0]['tempbg'];
            $detail['online']  = $results[0]['online'];

            //区域
            $detail['addrid'] = $results[0]['addr'];
            global $data;
            $data = "";
            $addrArr = getParentArr("site_area", $results[0]['addr']);
            if($addrArr){
                $addrArr = array_reverse(parent_foreach($addrArr, "typename"));
                $detail['addrName'] = join(" > ", $addrArr);
            }else{
                $detail['addrName'] = "";
            }

            if($results[0]['mtype'] == 2){
                $detail['company'] = $results[0]['company'];
                $detail['address'] = $results[0]['address'];
                $detail['licenseState'] = $results[0]['licenseState'];
                $detail['licenseInfo'] = $results[0]['licenseInfo'];

                // 商家类型
                $sql = $dsql->SetQuery("SELECT `id`, `type`, `expired`, `state` FROM `#@__business_list` WHERE `uid` = ".$id." AND `state` != 4");
                $res = $dsql->dsqlOper($sql, "results");
                if($res){
                    $busiId = $res[0]['id'];
                    $busiType = $res[0]['type'];
                    $busiState = $res[0]['state'];
                    $busiExpired = $res[0]['expired'];
                }else{
                    $busiId = 0;
                    $busiType = 0;
                    $busiState = 0;
                    $busiExpired = 0;
                }
                $detail['busiId'] = $busiId;
                $detail['busiType'] = $busiType;
                $detail['busiState'] = $busiState;
                $detail['busiExpired'] = $busiExpired;
            }

            $total = 100;
            //验证支付密码
            if(empty($results[0]['paypwd'])){
                $total -= 20;
            }
            //验证实名
            if($results[0]['mtype'] == 1 && $results[0]['certifyState'] != 1){
                $total -= 20;
            }elseif($results[0]['mtype'] == 2 && $results[0]['licenseState'] != 1){
                $total -= 20;
            }
            //验证手机
            if($results[0]['phoneCheck'] != 1){
                $total -= 20;
            }
            //验证邮箱
            if($results[0]['emailCheck'] != 1){
                $total -= 20;
            }
            //验证密保问题
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_security` WHERE `uid` = ".$id);
            $security = $dsql->dsqlOper($archives, "totalCount");
            if($security < 1){
                $total -= 20;
                $detail['question']  = 0;
            }else{
                $detail['question']  = 1;
            }

            $detail['security'] = $total;

            //融云Token
            // $rongCloudToken = getRongCloudToken($id, $detail['nickname'], $detail['photo']);
            // $detail['rongCloudToken'] = is_array($rongCloudToken) ? 0 : $rongCloudToken;
            if($userid == $id){
                $detail['wechat_conn'] = $results[0]['wechat_conn'];
                $detail['wechat_openid'] = $results[0]['wechat_openid'];
                $detail['alipay_conn'] = $results[0]['alipay_conn'];
                $detail['realname']    = $results[0]['realname'];
            }

            // 获取交友会员id
            $dating_uid = 0;
            if(in_array('dating', $installModuleArr)){
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = '$id'");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $dating_uid = $ret[0]['id'];
                }
            }
            $detail['dating_uid'] = $dating_uid;

            ksort($detail);
        }


        //验证两人是否为好友
        if($friend){

            $userid = $from ? $from : $userid;

            $isfriend = 0;
            $sql = $dsql->SetQuery("SELECT `delfrom`, `delto` FROM `#@__member_friend` WHERE ((`fid` = $userid AND `tid` = $id) OR (`fid` = $id AND `tid` = $userid)) AND `state` = 1 AND `delfrom` = 0 AND `delto` = 0");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $delfrom = $ret[0]['delfrom'];
                $delto = $ret[0]['delto'];
                if(!$delfrom && !$delto){
                    $isfriend = 1;
                }
            }
            $detail['isfriend'] = $isfriend;
        }


        //查询会员开通的店铺信息
        if($shop && $detail['userType'] == 2){

            $shopList = array();
            global $installModuleArr;  //已安装模块

            //商家信息
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$sid = $results[0]['id'];
                $configHandels = new handlers('business', "storeDetail");
                $ret = $configHandels->getHandle($sid);
                if($ret['state'] == 100){
                    array_push($shopList, array(
                        'module' => 'business',
                        'type' => 'store',
                        'title' => $ret['info']['title'],
                        'note' => join('、', $ret['info']['tag_shopArr']),
                        'url' => $ret['info']['url'],
                    ));
                }
			}

            //自媒体
            if(in_array('article', $installModuleArr)){
                //自媒体信息
				$configHandels = new handlers('article', "selfmediaDetail");
				$ret = $configHandels->getHandle(array("uid" => $id));
                if($ret['state'] == 100 && $ret['info']['state']){
                    array_push($shopList, array(
                        'module' => 'article',
                        'type' => 'selfmedia',
                        'title' => $ret['info']['ac_name'],
                        'note' => $ret['info']['ac_profile'],
                        'url' => $ret['info']['url'],
                    ));
                }
            }

            //二手车
            if(in_array('car', $installModuleArr)){
                $archives = $dsql->SetQuery("SELECT `id` FROM `#@__car_store` WHERE `userid` = ".$id);
    			$results  = $dsql->dsqlOper($archives, "results");
    			if($results){
    				$sid = $results[0]['id'];
                    $configHandels = new handlers('car', "storeDetail");
                    $ret = $configHandels->getHandle($sid);
                    if($ret['state'] == 100){
                        array_push($shopList, array(
                            'module' => 'car',
                            'type' => 'store',
                            'title' => $ret['info']['title'],
                            'note' => $ret['info']['tag'],
                            'url' => $ret['info']['url'],
                        ));
                    }
    			}
            }

            //二手车
            if(in_array('homemaking', $installModuleArr)){
                $archives = $dsql->SetQuery("SELECT `id` FROM `#@__homemaking_store` WHERE `userid` = ".$id);
    			$results  = $dsql->dsqlOper($archives, "results");
    			if($results){
    				$sid = $results[0]['id'];
                    $configHandels = new handlers('homemaking', "storeDetail");
                    $ret = $configHandels->getHandle($sid);
                    if($ret['state'] == 100){
                        array_push($shopList, array(
                            'module' => 'homemaking',
                            'type' => 'store',
                            'title' => $ret['info']['title'],
                            'note' => $ret['info']['tag'],
                            'url' => $ret['info']['url'],
                        ));
                    }
    			}
            }

            //房产中介
            if(in_array('house', $installModuleArr)){
                $archives = $dsql->SetQuery("SELECT `id` FROM `#@__house_zjcom` WHERE `userid` = ".$id);
    			$results  = $dsql->dsqlOper($archives, "results");
    			if($results){
    				$sid = $results[0]['id'];
                    $configHandels = new handlers('house', "zjComDetail");
                    $ret = $configHandels->getHandle($sid);
                    if($ret['state'] == 100){
                        array_push($shopList, array(
                            'module' => 'house',
                            'type' => 'store',
                            'title' => $ret['info']['title'],
                            'note' => $ret['info']['address'],
                            'url' => $ret['info']['url'],
                        ));
                    }
    			}
            }

            //分类信息
            if(in_array('info', $installModuleArr)){
                $archives = $dsql->SetQuery("SELECT `id` FROM `#@__infoshop` WHERE `uid` = ".$id);
    			$results  = $dsql->dsqlOper($archives, "results");
    			if($results){
    				$sid = $results[0]['id'];
                    $configHandels = new handlers('info', "storeDetail");
                    $ret = $configHandels->getHandle($sid);
                    if($ret['state'] == 100){
                        array_push($shopList, array(
                            'module' => 'info',
                            'type' => 'store',
                            'title' => $ret['info']['member']['company'],
                            'note' => $ret['info']['note'],
                            'url' => $ret['info']['domain'],
                        ));
                    }
    			}
            }

            //招聘企业
            if(in_array('job', $installModuleArr)){
                $archives = $dsql->SetQuery("SELECT `id` FROM `#@__job_company` WHERE `userid` = ".$id);
    			$results  = $dsql->dsqlOper($archives, "results");
    			if($results){
    				$sid = $results[0]['id'];
                    $configHandels = new handlers('job', "companyDetail");
                    $ret = $configHandels->getHandle($sid);
                    if($ret['state'] == 100){

                        //招聘职位
                        $now = time();
                        $sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__job_post` WHERE `company` = '$sid' AND `state` = 1 AND `valid` > '$now'");
                        $ret_ = $dsql->dsqlOper($sql, "results");
                        $post = $ret_[0]['total'];
                        $purl = getUrlPath(array(
                            'service' => 'job',
                            'template' => 'company-job',
                            'id' => $sid
                        ));

                        //工资统计
                        $min = $max = $wage = array();
                        $sql  = $dsql->SetQuery("SELECT
        				(SELECT MIN(`wage`) FROM `#@__job_wage` WHERE `work` = wage.`work` AND `state` = 1 AND `cid` = '$sid') as min,
        				(SELECT MAX(`wage`) FROM `#@__job_wage` WHERE `work` = wage.`work` AND `state` = 1 AND `cid` = '$sid') as max
        				FROM `#@__job_wage` AS wage WHERE wage.`state` = 1 AND `cid` = '$sid' GROUP BY wage.`work` ORDER BY wage.`id` ASC");
                        $ret_  = $dsql->dsqlOper($sql, "results");
                        if($ret_){
                            foreach ($ret_ as $key => $value) {
                                array_push($min, $value['min']);
                                array_push($max, $value['max']);
                            }
                        }
                        $wurl = getUrlPath(array(
                            'service' => 'job',
                            'template' => 'company-salary',
                            'id' => $sid
                        ));

                        array_push($shopList, array(
                            'module' => 'job',
                            'type' => 'store',
                            'title' => $ret['info']['title'],
                            'note' => $ret['info']['address'],
                            'url' => $ret['info']['domain'],
                            'item' => array(
                                array(
                                    'title' => '在招职位：' . $post . '个',
                                    'url' => $purl
                                ),
                                $wage = array(
                                    'title' => '薪资范围：' . min($min) . '~' . max($max),
                                    'url' => $wurl
                                )
                            )
                        ));
                    }
    			}
            }

            //商城店铺
            if(in_array('shop', $installModuleArr)){
                $archives = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$id);
    			$results  = $dsql->dsqlOper($archives, "results");
    			if($results){
    				$sid = $results[0]['id'];
                    $configHandels = new handlers('shop', "storeDetail");
                    $ret = $configHandels->getHandle($sid);
                    if($ret['state'] == 100){
                        array_push($shopList, array(
                            'module' => 'shop',
                            'type' => 'store',
                            'title' => $ret['info']['title'],
                            'note' => $ret['info']['project'],
                            'url' => $ret['info']['domain'],
                        ));
                    }
    			}
            }

            //团购店铺
            if(in_array('tuan', $installModuleArr)){
                $archives = $dsql->SetQuery("SELECT `id` FROM `#@__tuan_store` WHERE `uid` = ".$id);
    			$results  = $dsql->dsqlOper($archives, "results");
    			if($results){
    				$sid = $results[0]['id'];
                    $configHandels = new handlers('tuan', "storeDetail");
                    $ret = $configHandels->getHandle($sid);
                    if($ret['state'] == 100){
                        array_push($shopList, array(
                            'module' => 'tuan',
                            'type' => 'store',
                            'title' => $ret['info']['member']['company'],
                            'note' => $ret['info']['typename'],
                            'url' => $ret['info']['url'],
                        ));
                    }
    			}
            }

            //外卖店铺
            if(in_array('waimai', $installModuleArr)){
                $archives = $dsql->SetQuery("SELECT `shopid` FROM `#@__waimai_shop_manager` WHERE `userid` = ".$id." ORDER BY `id` ASC");
    			$results  = $dsql->dsqlOper($archives, "results");
    			if($results){
    				$sid = $results[0]['shopid'];
                    $configHandels = new handlers('waimai', "storeDetail");
                    $ret = $configHandels->getHandle($sid);
                    if($ret['state'] == 100){
                        array_push($shopList, array(
                            'module' => 'waimai',
                            'type' => 'store',
                            'title' => $ret['info']['shopname'],
                            'note' => $ret['info']['address'],
                            'url' => $ret['info']['url'],
                        ));
                    }
    			}
            }

            //自助建站
            if(in_array('website', $installModuleArr)){
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__website` where `userid` = " . $id);
    			$res = $dsql->dsqlOper($sql, "results");
    			if(!empty($res[0]['id'])){
                    $param = array(
    					"service"      => "website",
    					"template"     => "site".$res[0]['id']
    				);
    				$url = getUrlPath($param);

                    array_push($shopList, array(
                        'module' => 'website',
                        'type' => 'store',
                        'title' => '企业站点',
                        'note' => '点击访问企业主页',
                        'url' => $url,
                    ));
    			}
            }

            $detail['shopList'] = $shopList;

        }

        return $detail;
    }


    //站内消息
    public function message(){
        global $dsql;
        global $userLogin;
        $pageinfo = $list = array();
        $state = $notice = $page = $pageSize = $im = $type = 0;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//格式错误！
            }else{
                $state    = $this->param['state'];
                $notice   = $this->param['notice'];
                $im       = $this->param['im'];
                $type     = $this->param['type'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $uid = $this->param['userid'] ? $this->param['userid'] : $userLogin->getMemberID();

        if(!is_numeric($uid)) return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives = $dsql->SetQuery("SELECT log.`id`, log.`state`, l.`title`, l.`date`, l.`body` FROM `#@__member_letter_log` log LEFT JOIN `#@__member_letter` l ON l.`id` = log.`lid` WHERE log.`uid` = ".$uid);

        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");

        if($totalCount == 0 && $type != 'tongji') return array("state" => 200, "info" => self::$langData['siteConfig'][21][64]);//暂无数据！

        //未读
        $unread     = $dsql->dsqlOper($archives." AND log.`state` = 0", "totalCount");
        //已读
        $read       = $dsql->dsqlOper($archives." AND log.`state` = 1", "totalCount");
        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        if($type == 'tongji'){
            //点赞未读
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_up` WHERE `isread` = 0 and `uid` = ".$uid);
            $upunread= $dsql->dsqlOper($archives, "totalCount");

            //评论未读
            $where_ = " AND `userid` = '$uid'";
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1".$where_);
            $ret = $dsql->dsqlOper($sql, "results");
            $sidList = array();
            foreach ($ret as $k => $v) {
                array_push($sidList, $v['id']);
            }
            if(!empty($sidList)){
                $whereC = " AND  (`sid` in(".join(',',$sidList).") or (`masterid` = '$uid' AND `sid` = '0'))";
            }else{
                $whereC = " AND  `masterid` = '$uid' AND `sid` = '0'";
            }

            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `isread` = 0 " . $whereC);
            $commentunread= $dsql->dsqlOper($archives, "totalCount");
        }

        $where = "";
        if($state != ""){
            $where = " AND log.`state` = '$state'";

            if($state == 0){
                $totalPage = ceil($unread/$pageSize);
            }elseif($state == 1){
                $totalPage = ceil($read/$pageSize);
            }
        }

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount,
            "unread" => $unread,
            "read" => $read
        );

        //统计IM未读消息数量
        if($im){
            $im = 0;
            $handels = new handlers("siteConfig", "getImFriendList");
            $return  = $handels->getHandle(array("userid" => $uid, "type" => "temp", "tongji" => 1));
            if($return['state'] == 100){
                foreach ($return['info'] as $key => $value) {
                    $im += (int)$value['lastMessage']['unread'];
                }
            }
            $pageinfo['im'] = $im;
        }

        //如果只是获取统计信息，则不需要获取消息列表
        if($type == "tongji"){
            $pageinfo['upunread'] = $upunread;
            $pageinfo['commentunread'] = $commentunread;
            return array("pageInfo" => $pageinfo);
        }

        $atpage = $pageSize*($page-1);
        $results = $dsql->dsqlOper($archives.$where." ORDER BY log.`id` DESC LIMIT $atpage, $pageSize", "results");

        $uinfo = $userLogin->getMemberInfo();
        if($results){
            foreach($results as $key => $val){
                $list[$key]['date']  = date("Y-m-d H:i:s", $val['date']);
                $list[$key]['state'] = $val['state'];
                $list[$key]['title'] = $val['title'];
                $list[$key]['id']    = $val['id'];
                $list[$key]['body']    = strstr($val['body'], 'first') && strstr($val['body'], 'remark') ? json_decode($val['body'], true) : $val['body'];

                if($uinfo['userType'] == 2){
                    $param = array(
                        "service"     => "member",
                        "template"    => "message_detail",
                        "id"          => $val['id']
                    );
                }else{
                    $param = array(
                        "service"     => "member",
                        "type"        => "user",
                        "template"    => "message_detail",
                        "id"          => $val['id']
                    );
                }

                $list[$key]['url'] = getUrlPath($param);
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);
    }


    //查询未通知的新消息
    public function getNewNotice(){

        global $dsql;
        global $userLogin;
        global $langData;
        $uid = $userLogin->getMemberID();

        if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        //查询消息通知
        $sql = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__member_letter_log` WHERE `uid` = $uid AND `notice` = 0");
        $ret = $dsql->dsqlOper($sql, "results");
        $hasnew = $ret[0]['c'];

        return $hasnew;
    }


    //清除未通知的新消息
    public function clearNewNotice(){

        global $dsql;
        global $userLogin;
        global $langData;
        $uid = $userLogin->getMemberID();

        if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        //查询消息通知
        $sql = $dsql->SetQuery("UPDATE `#@__member_letter_log` SET `notice` = 1 WHERE `uid` = $uid");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            return true;
        }
    }


    //删除站内信
    public function delMessage(){
        global $dsql;
        global $userLogin;
        global $langData;
        $uid = $userLogin->getMemberID();

        if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $id = $this->param['id'];

        if(empty($id)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][18]);//没有要删除的信息！

        $archives = $dsql->SetQuery("DELETE FROM `#@__member_letter_log` WHERE `id` in (".$id.") AND `uid` = '$uid'");
        $dsql->dsqlOper($archives, "update");

        return self::$langData['siteConfig'][21][136];//"删除成功！";

	}

	//发送站内信
	public function sendMessage(){
	    global $dsql;
	    global $langData;
        if(empty($this->param['uid'])) return array("state" => 500, "info" => $langData['siteConfig'][33][13]);  //参数错误！
	    $date = time();
	    $letterData = [
				"admin" =>  0,
	            "type"  =>  0,
	            "title" =>  '',
				"body"  =>  '',
				"urlParam"  =>  serialize($this->param),
				"success"   =>  1,
				"error" =>  0,
				"date"  =>  $date
		];
		$letterData = array_merge($letterData,array_intersect_key($this->param,$letterData));
		$valueString = "'" . implode("','",$letterData) . "'";
        $archives = $dsql->SetQuery("INSERT INTO `#@__member_letter`(`admin`,`type`,`title`,`body`,`urlParam`,`success`,`error`,`date`) VALUES($valueString)");
		$recordId = $dsql->dsqlOper($archives, "lastid");
		if(isset($recordId['state']) && $recordId['state'] == '100')	return array("state"=>100,"info" => self::$langData['siteConfig'][20][526]);	//发送失败，请重试！
        $uid = $this->param['uid'];
        $archives = $dsql->SetQuery("INSERT INTO `#@__member_letter_log`(`lid`,`uid`,`state`,`date`,`notice`) VALUES('$recordId','$uid','0','$date','0')");
        $dsql->dsqlOper($archives, "update");
        return self::$langData['siteConfig'][20][298];  //发送成功！
	}


    //设置站内信为已读
    public function setMessageRead(){
        global $dsql;
        global $userLogin;
        global $langData;
        $uid = $userLogin->getMemberID();

        if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $id = $this->param['id'];

        if(empty($id)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][19]);//没有要操作的信息！

        $archives = $dsql->SetQuery("UPDATE `#@__member_letter_log` SET `state` = 1 WHERE `id` in (".$id.") AND `uid` = '$uid'");
        $dsql->dsqlOper($archives, "update");

        return self::$langData['siteConfig'][33][24];//"设置成功！";

    }


    //验证用户身份
    public function authentication(){
        global $dsql;
        global $userLogin;
        global $langData;
        $id = $userLogin->getMemberID();

        if($id == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $param = $this->param;
        $do    = $param['do'];
        $opera = $param['opera'];

        $uinfo = $userLogin->getMemberInfo();

        //使用手机验证
        if($do == "authPhone"){

            if($uinfo['phoneCheck'] != 1) return array("state" => 200, "info" => $langData['siteConfig'][21][32]);    //您的手机暂未认证，请使用其它方式验证！

            $areaCode = $uinfo['areaCode'];
            $archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
            $results = $dsql->dsqlOper($archives, "results");
            if($results){
                $international = $results[0]['international'];
                if(!$international){
                    $areaCode = "";
                }
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][3]);//短信平台未配置，发送失败！
            }

            $phone   = $areaCode.$uinfo['phone'];
            $vdimgck = $param['vdimgck'];

            //验证输入的验证码
            $archives = $dsql->SetQuery("SELECT `id`, `pubdate` FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'auth' AND `user` = '$phone' AND `code` = '$vdimgck'");
            $results  = $dsql->dsqlOper($archives, "results");
            if(!$results){
                return array("state" => 200, "info" => $langData['siteConfig'][20][99]);    //验证码输入错误，请重试！
            }else{

                //5分钟有效期
                $now = GetMkTime(time());
                if($now - $results[0]['pubdate'] > 300) return array("state" => 200, "info" => $langData['siteConfig'][21][33]);   //验证码已过期，请重新获取！

                //验证通过删除发送的验证码
                $archives = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'auth' AND `user` = '$phone' AND `code` = '$vdimgck'");
                $dsql->dsqlOper($archives, "update");

                //执行接下来的操作
                $this->doAuthOpera($id, $opera);

                return self::$langData['siteConfig'][33][23];//"验证通过！";
            }

            //使用邮箱验证
        }elseif($do == "authEmail"){

            if($uinfo['emailCheck'] != 1) return array("state" => 200, "info" => $langData['siteConfig'][21][34]);   //您的邮箱暂未认证，请使用其它方式验证！

            $email   = $uinfo['email'];
            $vdimgck = $param['vdimgck'];

            //验证输入的验证码
            $archives = $dsql->SetQuery("SELECT `id`, `pubdate` FROM `#@__site_messagelog` WHERE `type` = 'email' AND `lei` = 'auth' AND `user` = '$email' AND `code` = '$vdimgck'");
            $results  = $dsql->dsqlOper($archives, "results");
            if(!$results){
                return array("state" => 200, "info" => $langData['siteConfig'][20][99]);    //验证码输入错误，请重试！
            }else{

                //5分钟有效期
                $now = GetMkTime(time());
                if(round(($now - $results[0]['pubdate'])/3600) > 24) return array("state" => 200, "info" => $langData['siteConfig'][21][33]);   //验证码已过期，请重新获取！

                //验证通过删除发送的验证码
                $archives = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'email' AND `lei` = 'auth' AND `user` = '$email' AND `code` = '$vdimgck'");
                $dsql->dsqlOper($archives, "update");

                //执行接下来的操作
                $this->doAuthOpera($id, $opera);

                return self::$langData['siteConfig'][33][23];//"验证通过！";
            }

            //使用安全保护问题
        }elseif($do == "authQuestion"){

            if($uinfo['question'] == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][35]);   //您还没有启用安全保护问题，请使用其它方式验证！

            $answer   = $param['answer'];

            //验证输入的验证码
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_security` WHERE `uid` = '$id' AND `answer` = '$answer'");
            $results  = $dsql->dsqlOper($archives, "totalCount");
            if($results == 0){
                return array("state" => 200, "info" => $langData['siteConfig'][20][103]);   //您输入的答案不正确，请重试！
            }else{

                //执行接下来的操作
                $this->doAuthOpera($id, $opera);

                return self::$langData['siteConfig'][33][23];//"验证通过！";
            }

        }

    }

    public function doAuthOpera($id, $type){

        if(empty($type)) return;

        global $dsql;

        //重置支付密码
        if($type == "paypwd"){
            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `paypwd` = '', `paypwdCheck` = 0 WHERE `id` = '$id'");
            $dsql->dsqlOper($archives, "update");

            //修改手机号码
        }elseif($type == "changePhone"){
            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `phone` = '', `phoneCheck` = 0 WHERE `id` = '$id'");
            $dsql->dsqlOper($archives, "update");

            //修改邮箱
        }elseif($type == "changeEmail"){
            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `email` = '', `emailCheck` = 0 WHERE `id` = '$id'");
            $dsql->dsqlOper($archives, "update");

            //修改安全保护问题
        }elseif($type == "changeQuestion"){
            $archives = $dsql->SetQuery("DELETE FROM `#@__member_security` WHERE `uid` = '$id'");
            $dsql->dsqlOper($archives, "update");
        }

    }

    //修改基本资料
    public function updateProfile(){
        global $dsql;
        global $userLogin;
        global $langData;
        $return = array();
        $id = $userLogin->getMemberID();
        $uinfo = $userLogin->getMemberInfo();
        if($id == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $param    = $this->param;
        $sex      = (int)$param['sex'];
        $qq       = (int)$param['qq'];
        $addr     = (int)$param['addr'];
        $birthday = $param['birthday'];
        $birthday = !empty($birthday) ? GetMkTime($birthday) : 0;

        $params = '';

        if($qq){
            $params .= ", `qq` = '$qq'";
        }
        if($addr){
            $params .= ", `addr` = '$addr'";
        }
        if($birthday){
            $params .= ", `birthday` = '$birthday'";
        }

        if($uinfo['userType'] == 2){
            $company = !empty($param['company']) ? $param['company'] : $param['nickname'];
            $address = $param['address'];
            $nickname = $param['person'];

            if($company){
                $params .= ", `company` = '$company'";
            }
            if($nickname){
                $params .= ", `nickname` = '$nickname'";
            }
            if($address){
                $params .= ", `address` = '$address'";
            }

        }else{

            $nickname = $param['nickname'];
            if($nickname){
                $params .= ", `nickname` = '$nickname'";
            }
        }

        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `sex` = '$sex'".$params." WHERE `id` = ".$id);
        $results = $dsql->dsqlOper($archives, "update");
        if($results == "ok"){
            return self::$langData['siteConfig'][20][229];//"修改成功！";
        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][20]);//修改失败！
        }

    }


    //找回密码
    public function backpassword(){
        global $dsql;
        global $cfg_secureAccess;
        global $cfg_basehost;
        global $cfg_webname;
        global $cfg_geetest;
        global $langData;
        $param = $this->param;

        if(empty($param)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'

        $type     = $param['type'];    //类型，1邮箱，2手机
        $email    = $param['email'];    //邮箱
        $areaCode = $param['areaCode']; //区域代码
        $phone    = $param['phone'];    //手机
        $vericode = $param['vericode']; //验证码
        $vdimgck  = $param['vdimgck'];  //手机验证码
        $isend    = $param['isend'];

        //如果没有开启了极验
        if(!$cfg_geetest){
            if(strtolower($vericode) != $_SESSION['huoniao_vdimg_value'] && !$isend) return array("state" => 200, "info" => $langData['siteConfig'][20][99]);   //验证码输入错误，请重试！
        }

        $ip = GetIP();
        $now = GetMkTime(time());
        $RenrenCrypt = new RenrenCrypt();

        //邮箱
        if($type == 1){

            if($cfg_geetest){
                $geetest_challenge = $param['geetest_challenge'];
                $geetest_validate  = $param['geetest_validate'];
                $geetest_seccode   = $param['geetest_seccode'];
                $terminal          = $param['terminal'];
                $terminal = empty($terminal) ? "pc" : $terminal;

                $verifyGeetest = json_decode(verifyGeetest($geetest_challenge, $geetest_validate, $geetest_seccode, $terminal), true);
                if(!is_array($verifyGeetest) || $verifyGeetest['status'] == 'fail'){
                    return array("state" => 200, "info" => $langData['siteConfig'][21][22]);   //图形验证错误，请重试！
                }
            }

            if(empty($email)) return array("state" => 200, "info" => $langData['siteConfig'][21][36]);    //请输入邮箱地址！
            $archives = $dsql->SetQuery("SELECT `id`, `password` FROM `#@__member` WHERE `email` = '$email'");
            $results  = $dsql->dsqlOper($archives, "results");
            if(!$results) return array("state" => 200, "info" => $langData['siteConfig'][20][79]);     //该邮箱地址没有注册过会员！

            $pwd = $results[0]['password'];
            $pwd = empty($pwd) ? $now : substr($pwd, 0, 10);

            $data = base64_encode($RenrenCrypt->php_encrypt("1$$".$email."$$".$ip."$$".$now."$$".$pwd));

            $link = $cfg_secureAccess.$cfg_basehost."/resetpwd.html?data=".$data;

            //获取邮件内容
            $cArr = getInfoTempContent("mail", '会员-找回密码-发送验证码', array("link" => $link));
            $title = $cArr['title'];
            $content = $cArr['content'];

            if($title == "" && $content == ""){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][4]);//邮件通知功能未开启，发送失败！
            }

            // $title = "找回密码-".$cfg_webname;
            // $content = "请点击下面的链接去重置密码：<br /><a href='".$link."'>".$link."</a><br />（如果点击链接没反应，请复制激活链接，粘贴到浏览器地址栏后访问）<br /><br />为了保障您帐号的安全性，请在24小时内完成重置，超时需要重新获取邮件。<br /><br />".$cfg_webname."<br />".date("Y-m-d", time())."<br /><br />如您错误的收到了此邮件，请不要点击链接。<br />这是一封系统自动发出的邮件，请不要直接回复。";

            $replay = sendmail($email, $title, $content);

            if(!empty($replay)){
                messageLog("email", "fpwd", $email, $title, addslashes($content), 0, 1);
                return array("state" => 200, "info" => $langData['siteConfig'][21][37]);     //邮件发送失败，请稍候重试！
            }else{
                messageLog("email", "fpwd", $email, $title, addslashes($content), 0, 0);
                return $langData['siteConfig'][21][38];    //邮件发送成功，请在24小时内点击邮件中的链接继续操作！
            }

            //手机
        }elseif($type == 2){

            if(empty($areaCode)) return array("state" => 200, "info" => $langData['siteConfig'][21][39]);  //请选择区域代码！
            if(empty($phone)) return array("state" => 200, "info" => $langData['siteConfig'][20][239]);    //请输入手机号
            $archives = $dsql->SetQuery("SELECT `id`, `password` FROM `#@__member` WHERE `phone` = '$phone'");
            $results  = $dsql->dsqlOper($archives, "results");
            if(!$results) return array("state" => 200, "info" => $langData['siteConfig'][20][77]);   //该手机号码没有注册过会员！

            $now = GetMkTime(time());

            $pwd = $results[0]['password'];
            $pwd = empty($pwd) ? $now : substr($pwd, 0, 10);

            $areaCode = (int)$areaCode;

            //非国际版不需要验证区域码
            $acode = $areaCode;
            $archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
            $results = $dsql->dsqlOper($archives, "results");
            if($results){
                $international = $results[0]['international'];
                if(!$international){
                    $acode = "";
                }
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][3]);//短信平台未配置，发送失败！
            }


            //验证输入的验证码
            $archives = $dsql->SetQuery("SELECT `id`, `pubdate` FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'fpwd' AND `user` = '".$acode.$phone."' AND `code` = '$vdimgck'");
            $results  = $dsql->dsqlOper($archives, "results");
            if(!$results){
                return array("state" => 200, "info" => $langData['siteConfig'][20][99]);    //验证码输入错误，请重试！
            }else{

                //5分钟有效期
                if($now - $results[0]['pubdate'] > 300) return array("state" => 200, "info" => $langData['siteConfig'][21][33]);    //验证码已过期，请重新获取！

                //验证通过删除发送的验证码
                $archives = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'fpwd' AND `user` = '".$acode.$phone."' AND `code` = '$vdimgck'");
                $dsql->dsqlOper($archives, "update");

                $data = base64_encode($RenrenCrypt->php_encrypt("2$$".$phone."$$".$ip."$$".$now."$$".$pwd));
                return $cfg_secureAccess.$cfg_basehost."/resetpwd.html?data=".$data;
            }

        }


    }


    //重置密码
    public function resetpwd(){
        global $dsql;
        global $userLogin;
        global $langData;
        $param = $this->param;

        $data = $param['data'];    //安全验证数据
        $npwd = $param['npwd'];    //新密码

        if(empty($data)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][21]);//非法请求！


        //验证安全链接
        $RenrenCrypt = new RenrenCrypt();
        $data = $RenrenCrypt->php_decrypt(base64_decode($data));

        $dataArr = explode("$$", $data);
        if(count($dataArr) != 5) return array("state" => 200, "info" => self::$langData['siteConfig'][33][21]);//非法请求！
        if(empty($dataArr[0]) || empty($dataArr[4])) return array("state" => 200, "info" => self::$langData['siteConfig'][33][21]);//非法请求！

        if($dataArr[0] == 1){
            $archives = $dsql->SetQuery("SELECT `id`, `password` FROM `#@__member` WHERE (`mtype` = 1 || `mtype` = 2) AND `email` = '".$dataArr[1]."'");
        }elseif($dataArr[0] == 2){
            $archives = $dsql->SetQuery("SELECT `id`, `password` FROM `#@__member` WHERE (`mtype` = 1 || `mtype` = 2) AND `phone` = '".$dataArr[1]."'");
        }
        $results  = $dsql->dsqlOper($archives, "results");
        if(!$results) return array("state" => 200, "info" => '会员不存在！');
        $old = $results[0]['password'];

        $now = GetMkTime(time());
        if($now - $dataArr[3] > 24 * 3600) return array("state" => 200, "info" => $langData['siteConfig'][21][40]);    //重置链接超时，请重新获取！

        if(empty($old)){
            if($dataArr[4] != $dataArr[3]) return array("state" => 200, "info" => self::$langData['siteConfig'][33][21]);//非法请求！
        }else{
            if($dataArr[4] != substr($old, 0, 10)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][21]);//非法请求！
        }


        //新密码
        if(empty($npwd)) return array("state" => 200, "info" => $langData['siteConfig'][21][41]);   //请输入新密码！
        preg_match('/^.{5,}$/', $npwd, $matchPassword);
        if(!$matchPassword) return array("state" => 200, "info" => $langData['siteConfig'][21][42]);   //新密码太过简单，请重新输入，最少5位！


        $ret  = $dsql->dsqlOper($archives, "results");
        $uid = $ret[0]['id'];

        $newPass = $userLogin->_getSaltedHash($npwd);

        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `password` = '$newPass' WHERE `id` = ".$uid);
        $results = $dsql->dsqlOper($archives, "update");
        if($results == "ok"){
            return self::$langData['siteConfig'][20][560];//密码重置成功！
        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][22]);//密码重置失败！
        }

    }



    //修改用户信息
    public function updateAccount(){
        global $dsql;
        global $userLogin;
        global $langData;
        $return = array();
        $id = $userLogin->getMemberID();
        if($id == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $param = $this->param;
        $do = $param['do'];

        //修改昵称
        if($do == "nick"){

            $name = $param['name'];
            if(empty($name) || $name == "undefined") return array("state" => 200, "info" => $langData['siteConfig'][20][87]);   //请输入新昵称
            $name = cn_substrR($name, 10);

            $memberInfo = $userLogin->getMemberInfo();
            if($memberInfo['userType'] == 2){
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `nickname` = '$name', `company` = '$name' WHERE `id` = ".$id);
            }else{
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `nickname` = '$name' WHERE `id` = ".$id);
            }

            $results = $dsql->dsqlOper($archives, "update");
            if($results == "ok"){
                return self::$langData['siteConfig'][20][229];//"修改成功！";
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][20]);//修改失败！
            }

            //修改密码
        }elseif($do == "password"){

            $userinfo = $userLogin->getMemberInfo();
            $u_pwd = $userinfo['pwd'];

            $old = $param['old'];
            $new = $param['new'];
            $confirm = $param['confirm'];

            if(($u_pwd && (empty($old) || $old == "undefined")) || empty($new) || $new == "undefined" || empty($confirm) || $confirm == "undefined") return array("state" => 200, "info" => self::$langData['siteConfig'][21][45]);//请输入完整！

            if(strlen($new) < 6) return array("state" => 200, "info" => $langData['siteConfig'][21][42]);   //新密码太过简单，请重新输入，最少5位！

            $archives = $dsql->SetQuery("SELECT `password` FROM `#@__member` WHERE `id` = ".$id);
            $results = $dsql->dsqlOper($archives, "results");
            $uinfo = $results[0];

            if($u_pwd){
                $hash = $userLogin->_getSaltedHash($old, $uinfo['password']);
                if($hash != $uinfo['password']) return array("state" => 200, "info" => $langData['siteConfig'][21][43]);   //您输入的当前密码不正确，请确认后重试！
            }

            if($new != $confirm) return array("state" => 200, "info" => $langData['siteConfig'][21][44]);   //两次输入的新密码不一致，请重新输入！
            $newPass = $userLogin->_getSaltedHash($new);

            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `password` = '$newPass' WHERE `id` = ".$id);
            $results = $dsql->dsqlOper($archives, "update");
            if($results == "ok"){
                return self::$langData['siteConfig'][20][229];//"修改成功！";
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][20]);//修改失败！
            }

            //设置支付密码
        }elseif($do == "paypwdAdd"){

            $pay1 = $param['pay1'];
            $pay2 = $param['pay2'];

            if(empty($pay1) || $pay1 == "undefined" || empty($pay2) || $pay2 == "undefined") return array("state" => 200, "info" => $langData['siteConfig'][21][45]);   //请输入完整！

            if(strlen($pay1) < 6) return array("state" => 200, "info" => $langData['siteConfig'][21][46]);   //支付密码太过简单，请重新输入！

            $paypwd = $userLogin->_getSaltedHash($pay1);

            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `paypwd` = '$paypwd', `paypwdCheck` = 1 WHERE `id` = ".$id);
            $results = $dsql->dsqlOper($archives, "update");
            if($results == "ok"){
                return self::$langData['siteConfig'][33][24];//"设置成功！";
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][25]);//设置失败！
            }

            //修改支付密码
        }elseif($do == "paypwdEdit"){

            $old = $param['old'];
            $new = $param['new'];
            $confirm = $param['confirm'];

            if(empty($old) || $old == "undefined" || empty($new) || $new == "undefined" || empty($confirm) || $confirm == "undefined") return array("state" => 200, "info" => $langData['siteConfig'][21][45]);   //请输入完整！

            if(strlen($new) < 6) return array("state" => 200, "info" => $langData['siteConfig'][21][47]);    //新的支付密码太过简单，请重新输入！

            $archives = $dsql->SetQuery("SELECT `paypwd` FROM `#@__member` WHERE `id` = ".$id);
            $results = $dsql->dsqlOper($archives, "results");
            $uinfo = $results[0];

            $hash = $userLogin->_getSaltedHash($old, $uinfo['paypwd']);
            if($hash != $uinfo['paypwd']) return array("state" => 200, "info" => $langData['siteConfig'][21][43]);   //您输入的当前密码不正确，请确认后重试！

            if($new != $confirm) return array("state" => 200, "info" => $langData['siteConfig'][21][44]);   //两次输入的新密码不一致，请重新输入！
            $newPass = $userLogin->_getSaltedHash($new);

            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `paypwd` = '$newPass' WHERE `id` = ".$id);
            $results = $dsql->dsqlOper($archives, "update");
            if($results == "ok"){
                return self::$langData['siteConfig'][20][229];//"修改成功！";
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][20]);//修改失败！
            }

            //实名认证
        }elseif($do == "certify"){

            $memberInfo    = $userLogin->getMemberInfo();
            $realname = $param['realname'];
            $idcard   = $param['idcard'];
            $front    = $param['front'];
            $back     = $param['back'];
            $license  = $param['license'];

            $realname = cn_substrR($realname, 10);
            $idcard = cn_substrR($idcard, 18);

            if(empty($realname) || $realname == "undefined" || empty($idcard) || $idcard == "undefined" || empty($front) || $front == "undefined" || empty($back) || $back == "undefined") return array("state" => 200, "info" => $langData['siteConfig'][21][45]);   //请输入完整！

            //企业
            if($memberInfo['userType'] == 2){
                if(empty($license)) return array("state" => 200, "info" => $langData['siteConfig'][20][109]);   //请上传营业执照！

                $archives = $dsql->SetQuery("SELECT `licenseState` FROM `#@__member` WHERE `id` = ".$id);
                $results = $dsql->dsqlOper($archives, "results");
                $uinfo = $results[0];
                if($uinfo['licenseState'] == 1) return array("state" => 200, "info" => $langData['siteConfig'][21][48]);   //您的企业认证已经通过审核！
                if($uinfo['licenseState'] == 3) return array("state" => 200, "info" => $langData['siteConfig'][21][49]);   //您的企业认证信息已经提交，请等待工作人员审核！
            }

            //个人
            if($memberInfo['userType'] == 1){
                $archives = $dsql->SetQuery("SELECT `certifyState` FROM `#@__member` WHERE `id` = ".$id);
                $results = $dsql->dsqlOper($archives, "results");
                $uinfo = $results[0];
                if($uinfo['certifyState'] == 1) return array("state" => 200, "info" => $langData['siteConfig'][21][50]);   //您的实名认证已经通过审核！
                if($uinfo['certifyState'] == 3) return array("state" => 200, "info" => $langData['siteConfig'][21][51]);   //您的实名认证信息已经提交，请等待工作人员审核！
            }


            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `idcard` = '$idcard' AND `id` != ".$id);
            $results = $dsql->dsqlOper($archives, "totalCount");
            global $cfg_hotline;
            if($results > 0) return array("state" => 200, "info" => $langData['siteConfig'][21][52]);    //您输入的身份证号码已经被其他帐号绑定！


            if($memberInfo['userType'] == 1){
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `realname` = '$realname', `idcard` = '$idcard', `idcardFront` = '$front', `idcardBack` = '$back', `certifyState` = 3 WHERE `id` = ".$id);
            }
            if($memberInfo['userType'] == 2){
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `realname` = '$realname', `idcard` = '$idcard', `idcardFront` = '$front', `idcardBack` = '$back', `license` = '$license', `certifyState` = 3, `licenseState` = 3 WHERE `id` = ".$id);
            }

            $results = $dsql->dsqlOper($archives, "update");
            if($results == "ok"){

                //后台消息通知
                updateAdminNotice("member", "certify");

                return $langData['siteConfig'][21][53];   //提交成功，请等待工作人员审核！
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][25]);//设置失败！
            }

            //获取实名认证信息
        }elseif($do == "getCerfityData"){

            $return = array();
            $archives = $dsql->SetQuery("SELECT `mtype`, `certifyState`, `certifyInfo`, `realname`, `idcard`, `idcardFront`, `idcardBack`, `license`, `licenseState`, `licenseInfo` FROM `#@__member` WHERE `id` = ".$id);
            $results = $dsql->dsqlOper($archives, "results");
            $uinfo = $results[0];

            if($uinfo['mtype'] == 1){
                if($uinfo['certifyState'] == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][54]);   //获取失败，您还未提交实名认证信息！
                if($uinfo['certifyState'] == 2) return array("state" => 200, "info" => $uinfo['certifyInfo']);
            }

            if($uinfo['mtype'] == 2){
                if($uinfo['licenseState'] == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][55]);   //获取失败，您还未提交公司认证信息！
                if($uinfo['licenseState'] == 2) return array("state" => 200, "info" => $uinfo['licenseInfo']);

                $return['license'] = getFilePath($uinfo['license']);
            }

            $return['realname'] = $uinfo['realname'];
            $return['idcard']   = $uinfo['idcard'];
            $return['front']    = getFilePath($uinfo['idcardFront']);
            $return['back']     = getFilePath($uinfo['idcardBack']);
            return $return;

            //手机绑定
        }elseif($do == "chphone"){

            $areaCode = $param['areaCode'];
            $phone    = $param['phone'];
            $vdimgck  = $param['vdimgck'];
            global $cfg_hotline;

            $archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
            $results = $dsql->dsqlOper($archives, "results");
            if($results){
                $international = $results[0]['international'];
                if(!$international){
                    $areaCode = "";
                }
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][3]);//短信平台未配置，发送失败！
            }

            //if(!preg_match("/1[34578]{1}\d{9}$/", $phone)){
            //return array("state" => 200, "info" => '请输入正确的手机号码！');
            //}

            //判断手机号码是否已经被绑定
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `phone` = '$phone' AND `areaCode` = '$areaCode' AND `id` <> $id");
            $results  = $dsql->dsqlOper($archives, "totalCount");
            if($results > 0) {
                // 修改其他账号的手机号绑定
                $changeUidByPhone = $param['changeUidByPhone'];
                if($changeUidByPhone){
                    $RenrenCrypt = new RenrenCrypt();
                    $changeUidByPhone = $RenrenCrypt->php_decrypt(base64_decode($changeUidByPhone));
                    $changeUidByPhone = (int)$changeUidByPhone;
                }
                if($changeUidByPhone && $changeUidByPhone != $id){
                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `phone` = '$phone' AND `id` = $changeUidByPhone");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if(!$ret){
                        $changeUidByPhone = 0;
                    }
                }else{
                    $changeUidByPhone = 0;
                }
                // 未已确认操作
                if(empty($changeUidByPhone)){
                    $archives = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'verify' AND `user` = '".$areaCode.$phone."'");
                    $dsql->dsqlOper($archives, "update");
                    return array("state" => 200, "info" => $langData['siteConfig'][21][56]);   //您输入的手机号码已经被其他帐号绑定！
                }
            }

            //验证输入的验证码
            $archives = $dsql->SetQuery("SELECT `id`, `pubdate` FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'verify' AND `user` = '".$areaCode.$phone."' AND `code` = '$vdimgck'");
            $results  = $dsql->dsqlOper($archives, "results");
            if(!$results){
                return array("state" => 200, "info" => $langData['siteConfig'][20][99]);   //验证码输入错误，请重试！
            }else{

                //5分钟有效期
                $now = GetMkTime(time());
                if($now - $results[0]['pubdate'] > 300) return array("state" => 200, "info" => $langData['siteConfig'][21][33]);   //验证码已过期，请重新获取！

                //验证通过删除发送的验证码
                $archives = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'verify' AND `user` = '".$areaCode.$phone."' AND `code` = '$vdimgck'");
                $dsql->dsqlOper($archives, "update");

                //更新用户状态
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `phone` = '$phone', `phoneCheck` = 1 WHERE `id` = '$id'");
                $dsql->dsqlOper($archives, "update");

                // 同手机号账户解绑
                if($changeUidByPhone){
                    $archives = $dsql->SetQuery("UPDATE `#@__member` SET `phone` = '', `phoneCheck` = 0 WHERE `id` = '$changeUidByPhone'");
                    $dsql->dsqlOper($archives, "update");
                }

                return self::$langData['siteConfig'][33][23];//"验证通过！";
            }

            //邮箱绑定
        }elseif($do == "chemail"){

            $email = $param['email'];
            global $cfg_hotline;
            global $cfg_secureAccess;
            global $cfg_basehost;
            global $cfg_webname;

            if(!CheckEmail($email)){
                return array("state" => 200, "info" => $langData['siteConfig'][21][57]);   //请输入正确的邮箱地址！
            }

            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `email` = '$email' AND `id` <> $id");
            $results  = $dsql->dsqlOper($archives, "totalCount");
            if($results > 0) return array("state" => 200, "info" => $langData['siteConfig'][21][58]);  //您输入的邮箱已经被其他帐号绑定！

            $ip = GetIP();
            $now = GetMkTime(time());
            global $cfg_webname;

            //URL地址加密
            $RenrenCrypt = new RenrenCrypt();
            $data = base64_encode($RenrenCrypt->php_encrypt($id."$$".$ip."$$".$now));

            $link = $cfg_secureAccess.$cfg_basehost."/index.php?service=member&template=bindemail&data=".$data;

            //获取邮件内容
            $cArr = getInfoTempContent("mail", '会员-手机邮箱绑定-发送验证码', array("link" => $link));
            $title = $cArr['title'];
            $content = $cArr['content'];

            if($title == "" && $content == ""){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][4]);//邮件通知功能未开启，发送失败！
            }

            // $title = $cfg_webname."-邮箱绑定";
            // $content = "请点击下面的链接完成绑定<br /><a href='".$link."'>".$link."</a><br />（如果点击链接没反应，请复制激活链接，粘贴到浏览器地址栏后访问）<br /><br />为了保障您帐号的安全性，请在 48小时内完成绑定，此链接将在您绑定过后失效！<br />激活邮件将在您激活一次后失效。<br /><br />".$cfg_webname."<br />".date("Y-m-d", time())."<br /><br />如您错误的收到了此邮件，请不要点击绑定按钮。<br />这是一封系统自动发出的邮件，请不要直接回复。";

            $replay = sendmail($email, $title, $content);

            if(!empty($replay)){
                messageLog("email", "bind", $email, $title, addslashes($content), $id, 1);
                return array("state" => 200, "info" => $langData['siteConfig'][21][37]);  //邮件发送失败，请稍候重试！
            }else{
                //更新用户状态
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `email` = '$email' WHERE `id` = '$id'");
                $dsql->dsqlOper($archives, "update");

                messageLog("email", "bind", $email, $title, addslashes($content), $id, 0);
                return $langData['siteConfig'][21][38];   //邮件发送成功，请在24小时内点击邮件中的链接继续操作！
            }

            //设置安全保护问题
        }elseif($do == "question"){

            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_security` WHERE `uid` = '$id'");
            $results  = $dsql->dsqlOper($archives, "totalCount");
            if($results > 0){
                return array("state" => 200, "info" => $langData['siteConfig'][21][59]);   //您已经设置过安全保护问题！
            }

            $q1     = $param['q1'];
            $q2     = $param['q2'];
            $answer = $param['answer'];

            if(empty($q1)) return array("state" => 200, "info" => $langData['siteConfig'][21][60]);  //请选择问题一！
            if(empty($q2)) return array("state" => 200, "info" => $langData['siteConfig'][21][61]);  //请选择问题二！
            if(empty($answer)) return array("state" => 200, "info" => $langData['siteConfig'][21][62]);  //请输入您的问题答案！

            $question = $q1."$$".$q2;

            $archives = $dsql->SetQuery("INSERT INTO `#@__member_security` (`uid`, `question`, `answer`) VALUES ('$id', '$question', '$answer')");
            $return = $dsql->dsqlOper($archives, "update");
            if($return == "ok"){
                return self::$langData['siteConfig'][33][24];//"设置成功！";
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][63]);   //设置失败，请刷新页面重试！
            }

        }

    }


    //安全体检
    public function riskAdvicePolicy(){
        global $dsql;
        global $userLogin;
        global $langData;
        $return = array();
        $id = $userLogin->getMemberID();

        if($id == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `state` = 1 AND `id` = ".$id);
        $results  = $dsql->dsqlOper($archives, "results");
        if($results){
            $total = 100;
            //验证支付密码
            if(empty($results[0]['paypwd'])){
                $return['paypwd'] = "false";
            }else{
                $return['paypwd'] = "ok";
            }
            //验证实名
            if($results[0]['mtype'] == 1 && $results[0]['certifyState'] != 1){
                $return['certifyState'] = "false";
            }elseif($results[0]['mtype'] == 2 && $results[0]['licenseState'] != 1){
                $return['certifyState'] = "false";
                $return['licenseState'] = "false";
            }else{
                $return['certifyState'] = "ok";
                if($results[0]['mtype'] == 2) {
                    $return['licenseState'] = "ok";
                }
            }
            //验证手机
            if($results[0]['phoneCheck'] != 1){
                $return['phoneCheck'] = "false";
            }else{
                $return['phoneCheck'] = "ok";
            }
            //验证邮箱
            if($results[0]['emailCheck'] != 1){
                $return['emailCheck'] = "false";
            }else{
                $return['emailCheck'] = "ok";
            }
            //验证密保问题
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_security` WHERE `uid` = ".$id);
            $security = $dsql->dsqlOper($archives, "totalCount");
            if($security < 1){
                $return['security'] = "false";
            }else{
                $return['security'] = "ok";
            }
        }
        return $return;
    }


    /**
     * 交易明细
     * @return array
     */
    public function record(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $type = $page = $pageSize = 0;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $type     = $this->param['type'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $uid = $userLogin->getMemberID();

        if(!is_numeric($uid)) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_money` WHERE `userid` = ".$uid);

        $where = "";
        if($type != ""){
            $where = " AND `type` = '$type'";
        }


        //总条数
        $totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
        //总收入
        $add = $dsql->SetQuery("SELECT SUM(`amount`) AS amount FROM `#@__member_money` WHERE `userid` = ".$uid." AND `type` = 1");
        $totalAdd = $dsql->dsqlOper($add, "results");
        //总支出
        $less = $dsql->SetQuery("SELECT SUM(`amount`) AS amount FROM `#@__member_money` WHERE `userid` = ".$uid." AND `type` = 0");
        $totalLess = $dsql->dsqlOper($less, "results");
        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);   //暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount,
            "totalAdd" => (float)$totalAdd[0]['amount'],
            "totalLess" => (float)$totalLess[0]['amount']
        );

        $atpage = $pageSize*($page-1);
        $results = $dsql->dsqlOper($archives.$where." ORDER BY `date` DESC LIMIT $atpage, $pageSize", "results");

        if($results){
            foreach($results as $key => $val){
                $flag = explode(",", $val['flag']);
                $list[$key]['date'] = date("Y-m-d H:i:s", $val['date']);
                $list[$key]['type']   = $val['type'];
                $list[$key]['amount'] = $val['amount'];
                $list[$key]['info'] = $val['info'];
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);
    }


    /**
     * 积分明细
     * @return array
     */
    public function point(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $type = $page = $pageSize = 0;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $type     = $this->param['type'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $uid = $userLogin->getMemberID();

        if(!is_numeric($uid)) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_point` WHERE `userid` = ".$uid);

        $where = "";
        if($type != ""){
            $where = " AND `type` = '$type'";
        }

        //总条数
        $totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
        //总收入
        $add = $dsql->SetQuery("SELECT SUM(`amount`) AS amount FROM `#@__member_point` WHERE `userid` = ".$uid." AND `type` = 1");
        $totalAdd = $dsql->dsqlOper($add, "results");
        //总支出
        $less = $dsql->SetQuery("SELECT SUM(`amount`) AS amount FROM `#@__member_point` WHERE `userid` = ".$uid." AND `type` = 0");
        $totalLess = $dsql->dsqlOper($less, "results");
        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);   //暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount,
            "totalAdd" => (int)$totalAdd[0]['amount'],
            "totalLess" => (int)$totalLess[0]['amount']
        );

        $atpage = $pageSize*($page-1);
        $results = $dsql->dsqlOper($archives.$where." ORDER BY `date` DESC LIMIT $atpage, $pageSize", "results");

        if($results){
            foreach($results as $key => $val){
                $flag = explode(",", $val['flag']);
                $list[$key]['date'] = date("Y-m-d H:i:s", $val['date']);
                $list[$key]['type']   = $val['type'];
                $list[$key]['amount'] = $val['amount'];
                $list[$key]['info'] = $val['info'];
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);
    }


    /**
     * 被打赏记录
     * @return array
     */
    public function reward(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $page = $pageSize = 0;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $uid = $userLogin->getMemberID();

        if(!is_numeric($uid)) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_reward` WHERE `state` = 1 AND `to` = ".$uid);

        //总条数
        $totalCount = $dsql->dsqlOper($archives.$where, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);   //暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $atpage = $pageSize*($page-1);
        $results = $dsql->dsqlOper($archives." ORDER BY `id` DESC LIMIT $atpage, $pageSize", "results");

        if($results){
            foreach($results as $key => $val){
                $list[$key]['id'] = $val['id'];

                $title = $url = "";
                $sql = $dsql->SetQuery("SELECT `title` FROM `#@__".$val['module']."list` WHERE `id` = ".$val['aid']);
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret && is_array($ret)){
                    $title = $ret[0]['title'];

                    $param = array(
                        "service"     => $val['module'],
                        "template"    => "detail",
                        "id"      => $val['aid']
                    );
                    $url = getUrlPath($param);
                }
                $list[$key]['title'] = $title;
                $list[$key]['url'] = $url;

                $user = $langData['siteConfig'][21][65];  //匿名
                if($val['user'] != -1){
                    $sql = $dsql->SetQuery("SELECT `nickname` FROM `#@__member` WHERE `id` = ".$val['uid']);
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret && is_array($ret)){
                        $user = $ret[0]['nickname'];
                    }
                }
                $list[$key]['user'] = $user;

                $list[$key]['amount'] = $val['amount'];
                $list[$key]['date'] = date("Y-m-d H:i:s", $val['date']);
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);
    }


    /**
     * 登录记录
     * @return array
     */
    public function loginrecord(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $page = $pageSize = 0;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $uid = $userLogin->getMemberID();

        if(!is_numeric($uid)) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_login` WHERE `userid` = ".$uid." ORDER BY `logintime` DESC");

        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);   //暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives.$where, "results");

        if($results){
            foreach($results as $key => $val){
                $list[$key]['time'] = date("Y-m-d H:i:s", $val['logintime']);
                $list[$key]['ip']   = $val['loginip'];
                $list[$key]['addr'] = $val['ipaddr'];
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);
    }


    /**
     * 自定义封面图片列表
     * @return array
     */
    public function customCoverBg(){
        global $dsql;
        global $userLogin;
        $pageinfo = $typeList = $list = array();
        $loadtype = $typeid = $page = $pageSize = 0;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $loadtype = $this->param['loadtype'];
                $typeid   = $this->param['typeid'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $where = "";
        if(!empty($typeid)){
            if($typeid == "rec"){
                $where = " AND `rec` = 1";
            }else{
                $where = " AND `typeid` = $typeid";
            }
        }

        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_coverbg` WHERE 1 = 1".$where." ORDER BY `id` DESC");

        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        if($totalCount == 0) return array("state" => 200, "info" => self::$langData['siteConfig'][21][64]);//暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives.$where, "results");


        if($results){
            foreach($results as $key => $val){
                $list[$key]['id']     = $val['id'];
                $list[$key]['title']  = $val['title'];
                $list[$key]['litpic'] = getFilePath($val['litpic']);
                $list[$key]['big']    = getFilePath($val['big']);
            }
        }

        if($loadtype){
            $typeList = $dsql->getTypeList(0, "member_coverbg_type");
        }

        return array("pageInfo" => $pageinfo, "typeList" => $typeList, "list" => $list);

    }


    //更新自定义封面图片
    public function updateCoverBg(){
        global $dsql;
        global $userLogin;
        global $langData;
        $return = array();
        $uid = $userLogin->getMemberID();

        if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $id = $this->param['id'];
        if(empty($id)) return array("state" => 200, "info" => $langData['siteConfig'][21][66]);   //请选择要设置的图片！

        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `tempbg` = '$id' WHERE `id` = '$uid'");
        $dsql->dsqlOper($archives, "update");
        return "ok";

    }


    /**
     * 收货地址
     * @return array
     */
    public function address(){
        global $dsql;
        global $userLogin;
        global $langData;
        $list = array();

        $uid = $userLogin->getMemberID();
        if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_address` WHERE `uid` = ".$uid." ORDER BY `id` DESC");

        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        $pageinfo = array("totalCount" => $totalCount);

        $results = $dsql->dsqlOper($archives.$where, "results");

        if($results){
            foreach($results as $key => $val){
                $list[$key]['id']      = $val['id'];
                $list[$key]['addrid']  = $val['addrid'];
                $list[$key]['addrids'] = "";

                if($val['addrid'] == 0){
                    $list[$key]['addrid']  = $langData['siteConfig'][21][67];   //未知
                }else{
                    $addrName = getParentArr("site_area", $val['addrid']);
                    global $data;
                    $data = "";
                    $addrNameArr = array_reverse(parent_foreach($addrName, "typename"));
                    $list[$key]['addrname']  = join(" ", $addrNameArr);

                    global $data;
                    $data = "";
                    $addrIdArr = array_reverse(parent_foreach($addrName, "id"));
                    $list[$key]['addrids']  = join(" ", $addrIdArr);
                }

                $list[$key]['address'] = $val['address'];
                $list[$key]['person']  = $val['person'];
                $list[$key]['mobile']  = $val['mobile'];
                $list[$key]['tel']     = $val['tel'];
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);
    }



    /**
     * 添加收货地址
     * @return array
     */
    public function addressAdd(){
        global $dsql;
        global $userLogin;
        global $langData;

        $uid = $userLogin->getMemberID();
        if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $id      = $this->param['id'];
        $addrid  = $this->param['addrid'];
        $address = $this->param['address'];
        $person  = $this->param['person'];
        $mobile  = $this->param['mobile'];
        $tel     = $this->param['tel'];

        if(empty($addrid)) return array("state" => 200, "info" => $langData['siteConfig'][21][68]);  //请选择所在区域！
        if(empty($address)) return array("state" => 200, "info" => $langData['siteConfig'][21][69]);  //请输入详细地址！
        if(empty($person)) return array("state" => 200, "info" => $langData['siteConfig'][21][70]);  //请输入收货人姓名！
        if(empty($mobile) && empty($tel)) return array("state" => 200, "info" => $langData['siteConfig'][21][71]);  //手机号码和固定电话至少输入一项

        $address = cn_substrR($address, 100);
        $person  = cn_substrR($person, 25);
        $mobile  = !empty($mobile) ? cn_substrR($mobile, 11) : "";
        $tel     = !empty($tel) ? cn_substrR($tel, 100) : "";

        if(empty($id)){
            $archives = $dsql->SetQuery("INSERT INTO `#@__member_address` (`uid`, `addrid`, `address`, `person`, `mobile`, `tel`) VALUES ('$uid', '$addrid', '$address', '$person', '$mobile', '$tel')");
            $return = $dsql->dsqlOper($archives, "lastid");
            if(is_numeric($return)){
                return $return;
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][72]);  //操作失败，请重试！
            }
        }else{
            $archives = $dsql->SetQuery("UPDATE `#@__member_address` SET `addrid` = '$addrid', `address` = '$address', `person` = '$person', `mobile` = '$mobile', `tel` = '$tel' WHERE `uid` = '$uid' AND `id` = ".$id);
            $return = $dsql->dsqlOper($archives, "update");
            if($return == "ok"){
                return '操作成功！';
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][72]);  //操作失败，请重试！
            }
        }

    }


    //删除收货地址
    public function addressDel(){
        global $dsql;
        global $userLogin;
        global $langData;
        $return = array();
        $uid = $userLogin->getMemberID();

        if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $id = $this->param['id'];
        if(empty($id)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][26]);//请选择要删除的信息！

        $archives = $dsql->SetQuery("DELETE FROM `#@__member_address` WHERE `uid` = '$uid' AND `id` = '$id'");
        $return = $dsql->dsqlOper($archives, "update");
        if($return == "ok"){
            return "ok";
        }else{
            array("state" => 200, "info" => self::$langData['siteConfig'][33][27]);//删除失败，请重试！
        }

    }


    //解绑社交帐号
    public function unbindConnect(){
        global $dsql;
        global $userLogin;
        global $langData;
        $return = array();
        $uid = $userLogin->getMemberID();

        if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $id = $this->param['id'];
        if(empty($id)) return array("state" => 200, "info" => $langData['siteConfig'][21][73]);   //请选择要解绑的社交帐号！

        $wechat_openid = $id == "wechat" ? ", `wechat_openid` = ''" : "";

        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `".$id."_conn` = '' ".$wechat_openid." WHERE `id` = '$uid'");
        $return = $dsql->dsqlOper($archives, "update");
        if($return == "ok"){
            return "ok";
        }else{
            array("state" => 200, "info" => $langData['siteConfig'][21][72]);   //操作失败，请重试！
        }

    }



    /**
     * 添加、删除、判断收藏信息
     * @return array
     */
    public function collect(){
        global $dsql;
        global $userLogin;

        $module = $this->param['module'];
        $temp   = $this->param['temp'];
        $id     = $this->param['id'];
        $type   = $this->param['type'];
        $check  = $this->param['check'];

        $userid = $userLogin->getMemberID();

        if(!empty($module) && !empty($temp) && !empty($id) && $userid > -1){

            //多个ID
            if(strstr($id, ",")){
                $id = explode(",", $id);

                foreach ($id as $k => $v) {

                    //新增收藏，先验证是否已经收藏过
                    if($type == "add"){
                        $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_collect` WHERE `module` = '$module' AND `action` = '$temp' AND `aid` = '$v' AND `userid` = '$userid'");
                        $return = $dsql->dsqlOper($archives, "totalCount");

                        if($return == 0){
                            $archives = $dsql->SetQuery("INSERT INTO `#@__member_collect` (`module`, `action`, `aid`, `userid`, `pubdate`) VALUES ('$module', '$temp', '$v', '$userid', ".GetMkTime(time()).")");
                            $dsql->dsqlOper($archives, "update");
                        }

                        //删除收藏
					}elseif($type == "del"){	
                        $archives = $dsql->SetQuery("DELETE FROM `#@__member_collect` WHERE `module` = '$module' AND `action` = '$temp' AND `aid` = '$v' AND `userid` = '$userid'");
						$dsql->dsqlOper($archives, "update");
						
                    }

                }
                return "ok";

            }else{
                //新增收藏，先验证是否已经收藏过
                if($type == "add"){
                    $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_collect` WHERE `module` = '$module' AND `action` = '$temp' AND `aid` = '$id' AND `userid` = '$userid'");
                    $return = $dsql->dsqlOper($archives, "totalCount");

                    if($return == 0){
                        if($check == 1){
                            return "no";
						}
                        $archives = $dsql->SetQuery("INSERT INTO `#@__member_collect` (`module`, `action`, `aid`, `userid`, `pubdate`) VALUES ('$module', '$temp', '$id', '$userid', ".GetMkTime(time()).")");
                        $dsql->dsqlOper($archives, "update");
                    }else{
                        return "has";
                    }
                    return "ok";

                    //删除收藏
                }elseif($type == "del"){
                    $archives = $dsql->SetQuery("DELETE FROM `#@__member_collect` WHERE `module` = '$module' AND `action` = '$temp' AND `aid` = '$id' AND `userid` = '$userid'");
                    $dsql->dsqlOper($archives, "update");
                    return "ok";
                }
            }
        }

	}


    //收藏列表
    public function collectList(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
		$module = $temp = $page = $pageSize = $where = "";


        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $module   = $this->param['module'];
                $temp     = $this->param['temp'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
		}

		$uid = $userLogin->getMemberID();
		$userInfo = $userLogin->getMemberInfo($uid);
		if(($module == 'push_timer' || $module == 'push_timer_temp') && ($userInfo['level'] == 0 || time()>$userInfo['expired']))
			return array("state" => 200, "info" => $langData['siteConfig'][21][64]);
        if(!is_numeric($uid)) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if(!empty($module)){
            $where .= " AND `module` = '$module'";
        }

        if(!empty($temp)){
            $where .= " AND `action` = '$temp'";
		}	
		if($module == "push_timer_temp"){
			$archives = $dsql->SetQuery("SELECT `id`, `uid` as aid, visualphone,'push_timer_temp' as `module`,'push' as `action`,now() pubdate,collected FROM #@__agentpushrecord WHERE `zjid` = {$uid} and now()<=expire");
		}else{
			$archives = $dsql->SetQuery("SELECT `id`, `aid`, `module`, `action`, `pubdate`, `visualphone` FROM `#@__member_collect` WHERE `userid` = ".$uid.$where);
		}
        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);

        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
		);

        $atpage = $pageSize*($page-1);
        $results = $dsql->dsqlOper($archives." ORDER BY `id` DESC LIMIT $atpage, $pageSize", "results");
        if($results){

            global $handler;
			$handler = true;	


			foreach($results as $key => $val){
				
                $module = $val['module'];
                $action = $val['action'];

                $list[$key]['id'] = $val['id'];
                $list[$key]['aid'] = $val['aid'];
				$list[$key]['pubdate'] = date("Y-m-d H:i:s", $val['pubdate']);

				if($module == 'push_timer' || $module == 'push_timer_temp'){
					if($val['aid'] == -1){
						$detail = ['photo'=>'','nickname'=>'未知','username'=>'--'];
					}else{
						$detail = $userLogin->getMemberInfo($val['aid']);
					}	
					$list[$key]['detail'] = [
						"photo" => $detail['photo'],
						"nickname"	=> $detail['nickname'],
						"username"	=> $detail['username'],
						"phone"	=>	$val['visualphone']
					];
					if($module == "push_timer_temp"){
						$list[$key]['detail']['collected'] = $val['collected'];
					}
					continue;
				}

                //主要用于获取信息URL，具体值以模板名称、模块的class文件、URL规则为主
                $act = $action;
                $act = $act == "loupan_detail" ? "loupan-detail" : $act;
                $act = $act == "sale_detail" ? "sale-detail" : $act;
                $act = $act == "zu_detail" ? "zu-detail" : $act;
                $act = $act == "xzl_detail" ? "xzl-detail" : $act;
                $act = $act == "sp_detail" ? "sp-detail" : $act;
                $act = $act == "cf_detail" ? "cf-detail" : $act;


                $param = array(
                    "service"     => $module,
                    "template"    => $act,
                    "id"          => $val['aid']
                );

                //主要用于读取信息详细信息，具体值以模块的class文件为主
                $act = $action;
                $act = $act == "loupan_detail" ? "loupanDetail" : $act;
                $act = $act == "sale_detail" ? "saleDetail" : $act;
                $act = $act == "zu_detail" ? "zuDetail" : $act;
                $act = $act == "xzl_detail" ? "xzlDetail" : $act;
                $act = $act == "sp_detail" ? "spDetail" : $act;
                $act = $act == "cf_detail" ? "cfDetail" : $act;
                $act = $act == "store-detail" ? "storeDetail" : $act;
                $act = $act == "case-detail" ? "diaryDetail" : $act;
                $act = $act == "company-detail" ? "storeDetail" : $act;
                $act = $act == "designer-detail" ? "teamDetail" : $act;
                $act = $module == "waimai" ? "storeDetail" : $act;
                $act = $act == "nanny-detail" ? "nannyDetail" : $act;
                //婚嫁plan-detail
                $act = $act == "plan-detail" ? "storeDetail" : $act;
                $act = $act == "hotelfield-detail" ? "hotelfieldDetail" : $act;
                $act = $act == "plancase-detail" ? "plancaseDetail" : $act;
                $act = $act == "planmeal-detail" ? "planmealDetail" : $act;
                $act = $act == "rental-detail" ? "rentalDetail" : $act;
                $act = $act == "host-detail" ? "hostDetail" : $act;
                $act =  $module == "marry" && $act == "detail" ? "storeDetail" : $act;
                //对婚嫁公司进行处理
                if($module == "marry" && strpos($act, "store-detail") !== false){
                    $act = "storeDetail";
                }elseif($module == "marry" && strpos($act, "planmeal-detail") !== false){
                    $act = "planmealDetail";
                }
                //旅游
                if($module == "travel" && $act != "store-detail"){
                    $actArr = explode('-', $act);
                    $act = $actArr[0].$actArr[1];
                }
                //教育
                if($module == "education" && $act != "store-detail" && $act != "detail"){
                    $actArr = explode('-', $act);
                    $act = $actArr[0].$actArr[1];
                }
				
				
				$handels = new handlers($module, $act);
				$detail  = $handels->getHandle($val['aid']);
					
				if(is_array($detail) && $detail['state'] == 100){
					$detail  = $detail['info'];
					if(is_array($detail)){
						$list[$key]['detail'] = $detail;

						//装修公司
						if($action == "company-detail"){
							$list[$key]['detail']['title'] = $detail['company'];
						}

						//设计师
						if($action == "designer-detail"){
							$list[$key]['detail']['title'] = $detail['name'];
						}

						//简历
						if($action == "resume"){
							$list[$key]['detail']['title'] = $detail['name'];
						}

						//外卖
						if($action == "waimai"){
							$list[$key]['detail']['title'] = $detail['shopname'];
						}

						//婚嫁 套餐
						if($module == "marry" && $action == "planmeal-detail"){
							$param = array(
								"service"     => $module,
								"template"    => $act,
								"id"          => $detail['id'],
								"typeid"      => $detail['type']
							);
						}elseif($module == "marry" && strpos($action, "store-detail") !== false){
							$actionArr = explode("|", $action);
							$param = array(
								"service"     => $module,
								"template"    => "store-detail",
								"id"          => $detail['id'],
								"istype"      => $actionArr[1],
								"typeid"      => $actionArr[2],
							);
						}elseif($module == "marry" && strpos($action, "planmeal-detail") !== false){
							$actionArr = explode("|", $action);
							$param = array(
								"service"     => $module,
								"template"    => "planmeal-detail",
								"id"          => $detail['id'],
								"typeid"      => $actionArr[1],
								"istype"      => $actionArr[2],
								"businessid"  => $actionArr[3]
							);
						}

						if(!$detail['url']){
							$list[$key]['detail']['url'] = getUrlPath($param);
						}
					}
				}else{
					$handels = new handlers($module, $action."Detail");
					$detail  = $handels->getHandle($val['aid']);

					if(is_array($detail) && $detail['state'] == 100){
						$detail  = $detail['info'];
						if(is_array($detail)){
							$list[$key]['detail'] = $detail;

							//装修公司
							if($action == "company-detail"){
								$list[$key]['detail']['title'] = $detail['company'];
							}

							//设计师
							if($action == "designer-detail"){
								$list[$key]['detail']['title'] = $detail['name'];
							}

							//简历
							if($action == "resume"){
								$list[$key]['detail']['title'] = $detail['name'];
							}

							//外卖
							if($action == "waimai"){
								$list[$key]['detail']['title'] = $detail['shopname'];
							}

							if(!$detail['url']){
								$list[$key]['detail']['url'] = getUrlPath($param);
							}
						}
					}
				}
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);
	}

	public function collectTempCustomer(){
		global $dsql;
		global $userLogin;
		global $langData;
		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		if(empty($this->param['id'])){
			return ['state'=>200,'info'=>'参数错误！'];
		}
		//查看是否已经收藏
		$checkSql = $dsql->SetQuery("select id,collected from #@__agentpushrecord where id=" . $this->param['id']);
		$isCollected = $dsql->dsqlOper($checkSql,"results");
		if($isCollected && !isset($isCollected['state'])){
			if($isCollected[0]['collected'] == 1)
				return ['state'=>200,'info'=>"已经收藏！"];
		}
		$userInfo = $userLogin->getMemberInfo($uid);
		if($userInfo['level'] == 0 || time()>$userInfo['expired'])	return "身份信息有误！";
		$pushSql = $dsql->SetQuery("select * from #@__agentpushrecord where id={$this->param['id']}");
		$pushInfo = $dsql->dsqlOper($pushSql,'results');
		if(!$pushInfo || !empty($pushInfo['state']))
			return ['state'=>200,'info'=>'记录不存在！'];
		$pushInfo = $pushInfo[0];
		//检查是否有绑定关系
		$checkSql = $dsql->SetQuery("select subscriptionId from #@__phonebind pb where state=1 and now()<=expire and ((caller='{$userInfo['phone']}' and callee='{$pushInfo['realphone']}') or (caller='{$pushInfo['realphone']}' and callee='{$userInfo['phone']}'))");
		$hasBound = $dsql->dsqlOper($checkSql, "results");
		/*$handler = new handlers('hwVisualPhone','judgeCalledTimesLimit');
		$times = $handler->getHandle(array('caller'=>$userInfo['phone'],'callee'=>$pushInfo['realphone']));
		if(!$times){
			$visitorPhone = $pushInfo['uid'] == -1 ? $pushInfo['realphone'] : '';
			$insertSql = $dsql->SetQuery("insert into #@__member_collect(module,action,aid,userid,pubdate,visualphone,visitorphone) values('push_timer','push',{$pushInfo['uid']},{$uid}," . time() . ",'CalledLimit','{$visitorPhone}')");
			$dsql->dsqlOper($insertSql, "update");
			return true;
		}*/
		if($hasBound && !isset($hasBound['state'])){
			
			//重新绑定
			$handler = new handlers('hwVisualPhone','unbind');
			$handler->getHandle(array('subscriptionId'=>$hasBound[0]['subscriptionId']));
		}
		$visualPhone = getVisualPhone($userInfo['phone'],$pushInfo['realphone']);
		if(!$visualPhone){
			return ['state'=>200,'info'=>'无法获取号码!'];
		}
		$handler = new handlers('hwVisualPhone','bind');
		$duration = intval($userInfo['expired']) - time();
		$bindResult = $handler->getHandle(array('relationPhone'=>$visualPhone,'caller'=>$userInfo['phone'],'callee'=>$pushInfo['realphone'],'duration'=>$duration));
		if($bindResult){
			$visitorPhone = $pushInfo['uid'] == -1 ? $pushInfo['realphone'] : '';
			$aid = $pushInfo['uid'] == -1 ? "-9999" : $pushInfo['uid'];
			$insertSql = $dsql->SetQuery("insert into #@__member_collect(module,action,aid,userid,pubdate,visualphone,visitorphone) values('push_timer','push',{$aid},{$uid}," . time() . ",'{$visualPhone}','{$visitorPhone}')");
			$dsql->dsqlOper($insertSql, "update");
			$updateTempSql = $dsql->SetQuery("update #@__agentpushrecord set visualphone='{$visualPhone}',collected=1 where id={$this->param['id']}");
			$dsql->dsqlOper($updateTempSql,'update');
			return true;
		}
		return ['state'=>200,'info'=>'绑定失败！'];
	}



    //删除收藏
    public function delCollect(){
        global $dsql;
        global $userLogin;
        global $langData;
        $uid = $userLogin->getMemberID();

        if($uid == -1 || !$uid) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $id = $this->param['id'];

		if(empty($id)) return array("state" => 200, "info" => '没有要删除的信息！');

		if($this->param['type'] == "push_timer"){
			$query = $dsql->SetQuery("select aid,visualphone,visitorphone,subscriptionId,m.phone from #@__member_collect mc left join #@__member m on m.id=mc.aid inner join #@__phonebind pb on (pb.relationphone=mc.visualphone and pb.state=1 and now()<=pb.expire and (mc.visitorphone=pb.caller or mc.visitorphone=pb.callee or m.phone=pb.caller or m.phone=pb.callee)) where mc.id in ({$id})");
			$result = $dsql->dsqlOper($query,'results');
			$handler = new handlers('hwVisualPhone','unbind');
			foreach($result as $value){
				$userPhone = $value['aid'] == -1 ? $value['visitorphone'] : $value['phone'];
				$cancleCollectSql = $dsql->SetQuery("update #@__agentpushrecord set collected=0 where zjid={$uid} and realphone='{$userPhone}' and visualphone='{$value['visualphone']}' and now()<=expire");
				$dsql->dsqlOper($cancleCollectSql,'update');
				$handler->getHandle(array('subscriptionId'=>$value['subscriptionId']));
			}
		}
		if($this->param['type'] == "push_timer_temp"){
			$archives = $dsql->SetQuery("DELETE FROM `#@__agentpushrecord` WHERE `id` in (".$id.") AND `zjid` = '$uid'");
			$dsql->dsqlOper($archives, "update");
		}else{
			$archives = $dsql->SetQuery("DELETE FROM `#@__member_collect` WHERE `id` in (".$id.") AND `userid` = '$uid'");
			$dsql->dsqlOper($archives, "update");
		} 

        return self::$langData['siteConfig'][21][136];//"删除成功！";

    }



    /**
     * 充值
     * @return array
     */
    public function deposit(){

        $param =  $this->param;
        $param_ = $param;
        $amount = $param['amount'];
        $ordernum = $param['ordernum'];
        $paytype = $param['paytype'];
        $final      = (int)$param['final']; // 最终支付

        $isMobile = isMobile();

        global $userLogin;
        global $langData;

        if($userLogin->getMemberID() == -1) die($langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        if($amount <= 0){
            die($langData['siteConfig'][21][74]);   //充值金额必须为整数或小数，小数点后不超过2位。
        }
        if(empty($paytype)){
            die($langData['siteConfig'][21][75]);   //请选择支付方式！
        }

        $ordernum = $ordernum ? $ordernum : create_ordernum();

        if($isMobile && empty($final)){
            $param_['ordernum'] = $ordernum;
            $param_['ordertype'] = 'deposit';
            $param = array(
                "service" => "member",
                "type" => "user",
                "template" => "pay",
                "param" => http_build_query($param_)
            );
            header("location:".getUrlPath($param));
            die;
        }

        return createPayForm("member", $ordernum, $amount, $paytype, $langData['siteConfig'][21][76], array("type" => "deposit"));   //账户充值

    }



    /**
     * 升级会员
     * @return array
     */
    public function upgrade(){

        $param      =  $this->param;
        $param_ = $param;

        $amount     = $param['amount'];
        $paytype    = $param['paytype'];
        $useBalance = $param['useBalance'];
        $level      = $param['level'];
        $day        = $param['day'];
        $daytype    = $param['daytype'];
        $date       = GetMkTime(time());
        $final      = (int)$param['final']; // 最终支付

        $isMobile = isMobile();

        global $userLogin;
        global $dsql;
        global $langData;

        $userid = $userLogin->getMemberID();
        $userid = $param['userid'] ? $param['userid'] : $userid;

        if($userid == -1) die($langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        if($amount <= 0) die($langData['siteConfig'][21][77]);   //支付金额必须为整数或小数，小数点后不超过2位。
        if(empty($paytype))	die($langData['siteConfig'][21][75]);   //请选择支付方式！

        if(empty($level) || empty($day) || empty($daytype)) die($langData['siteConfig'][21][78]);   //请选择要升级的会员类型！

        //验证是否合法
        $sql = $dsql->SetQuery("SELECT `name`, `cost` FROM `#@__member_level` WHERE `id` = $level");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){

            $name = $ret[0]['name'];
            $_day = $price = 0;
            $_daytype = "";
            $cost = !empty($ret[0]['cost']) ? unserialize($ret[0]['cost']) : array();

            if($cost){
                foreach ($cost as $key => $value) {
                    if($value['day'] == $day && $value['daytype'] == $daytype){
                        $_day = $day;
                        $_daytype = $value['daytype'];
                        $price = $value['price'];
                    }
                }

                if(empty($_day) || empty($_daytype)){
                    die($langData['siteConfig'][21][79]);   //会员等级类型配置错误，支付提交失败！
                }

                $dayname = "";
                if($_daytype == "day"){
                    $dayname = $langData['siteConfig'][13][6];  //天
                }elseif($_daytype == "month"){
                    $dayname = $langData['siteConfig'][13][31];   //个月
                }elseif($_daytype == "year"){
                    $dayname = $langData['siteConfig'][13][14];  //年
                }
                $ordernum = $param['ordernum'] ? $param['ordernum'] : create_ordernum();
                $title = $langData['siteConfig'][19][656]."{$name}{$day}{$dayname}";   //开通
                $param = array("type" => "upgrade", "level" => $level, "name" => $name, "day" => $_day, "daytype" => $_daytype, "title" => $title, "userid" => $userid, "price" => $price);

                // 移动端统一使用公共支付页面
                if($isMobile && empty($final)){
                    $useBalance = 0;
                }
                //余额支付
                if($useBalance){

                    //查询会员信息
                    $userinfo  = $userLogin->getMemberInfo($userid);
                    $usermoney = $userinfo['money'];

                    //如果使用了余额，计算还需要支付多少费用
                    $payprice = $price;
                    $useBalance = 0;
                    if($usermoney < $price){
                        $useBalance = $usermoney;
                        $payprice = $price - $usermoney;
                    }else{
                        $useBalance = $price;
                        $payprice = 0;
                    }

                    $payprice = sprintf('%.2f', $payprice);
                    $param['price'] = $payprice;
                    $param['balance'] = $useBalance;

                    //如果余额足够支付，不需要额外支付任何费用，直接升级
                    if($payprice <= 0){
                        $this->upgradeSuccess($param);

                        $urlParam = array(
                            "service" => "member",
                            "type"    => "user"
                        );
                        header("location:" . getUrlPath($urlParam));

                        //支付剩余费用
                    }else{
                        return createPayForm("member", $ordernum, $payprice, $paytype, $title, $param);
                    }


                    //第三方支付
                }else{
                    if($isMobile && empty($final)){
                        $param_['ordernum'] = $ordernum;
                        $param_['ordertype'] = 'upgrade_filter_user';
                        $param = array(
                            "service" => "member",
                            "type" => "user",
                            "template" => "pay",
                            "param" => http_build_query($param_)
                        );
                        header("location:".getUrlPath($param));
                        die;
                    }
                    return createPayForm("member", $ordernum, $price, $paytype, $title, $param);
                }


            }else{
                die($langData['siteConfig'][21][81]);   //会员等级类型费用未设置，支付提交失败！
            }

        }else{
            die($langData['siteConfig'][21][82]);   //会员等级类型不存在，请重新选择！
        }



    }


    /**
     * 查询订单状态
     * 付款等待页面，隔时查询待付款的订单状态，如果已经支付成功，则返回成功后要跳转的页面
     *
     */
    public function tradePayResult(){
        $param = $this->param;

        if(empty($param)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'

        $order = $param['order'];

        //如果type == 1，则$order为商品订单号，否则$order为支付订单号
        //如果type == 2，则代表会员充值页面，不需要指定订单号，只需要查询最后一笔ordertype为member的订单状态即可
        $checktype = $param['type'];

        if(empty($order) && $checktype != 2) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'

        global $dsql;

        if($checktype == 1){
            $archives = $dsql->SetQuery("SELECT `ordernum`, `ordertype`, `body`, `state` FROM `#@__pay_log` WHERE `body` = '$order'");
        }elseif($checktype == 2){
            $archives = $dsql->SetQuery("SELECT `ordertype`, `body`, `state` FROM `#@__pay_log` WHERE `ordertype` = 'member' ORDER BY `id` DESC LIMIT 0, 1");
        }else{
            $archives = $dsql->SetQuery("SELECT `ordertype`, `body`, `state` FROM `#@__pay_log` WHERE `ordernum` = '$order'");
        }
        $results = $dsql->dsqlOper($archives, "results");
        if($results){

            $ordertype = $results[0]['ordertype'];
            $travelorder= $results[0]['ordernum'];
            $body      = $results[0]['body'];
            $state     = $results[0]['state'];

            if($state == 1){

                $date = array();
                $orderArr = explode(",", $body);

                //如果是多个订单，则跳转到订单列表
                if(count($orderArr) > 1){
                    $data = array(
                        "service"  => "member",
                        "type"     => "user",
                        "template" => "order",
                        "module"   => $ordertype
                    );
                }else{

                    //如果是会员充值，则跳转到消费记录页面
                    if($ordertype == "member"){

                        $data = array(
                            "service"  => "member",
                            "type"     => "user",
                            "template" => "record"
                        );

                        $bodyArr = unserialize($body);
                        $type = $bodyArr['type'];

                        //入驻成功的跳转到入驻页面
                        if($type == "join"){
                            $data = array(
                                "service"  => "member",
                                "type"     => "user",
                                "template" => "business-config"
                            );

                            //入驻续费或升级的跳转到会员中心
                        }elseif($type == "join_renew" || $type == "join_upgrade"){
                            $data = array(
                                "service"  => "member",
                            );

                            //升级成功后跳转到会员中心首页
                        }elseif($type == "upgrade"){
                            $data = array(
                                "service"  => "member",
                                "type"     => "user"
                            );

                            //升级成功后跳转到会员中心首页
                        }elseif($type == "fabu"){

                            $module = $body['module'];
                            $class  = $body['class'];

                            $tmp = "record";

                            if($module == 'article' || $module == 'info' || $module == 'house' || $module == 'tieba'){

                                $tmp = "manage-".$module;

                                if($module == 'house'){
                                    $tmp .= "-".$class;
                                }

                            }else{

                            }

                            $data = array(
                                "service"  => "member",
                                "type"     => "user",
                                "template" => $tmp
                            );

                        }

                        //如果是打赏，则跳转到打赏结果页面
                        //如果是信息竞价，则跳转到支付结果页面
                    }elseif($ordertype == "article" || $ordertype == "tieba" || $ordertype == "info" || $ordertype == "house"){

                        $data = array(
                            "service"  => $ordertype,
                            "template" => "payreturn",
                            "ordernum" => $order
                        );


                        //外卖频道支付成功页面单独配置
                    }elseif($ordertype == "waimai" && !isMobile()){

                        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__".$ordertype."_order` WHERE `ordernum` = '$body'");
                        $ret = $dsql->dsqlOper($sql, "results");
                        if($ret){
                            $data = array(
                                "service"  => $ordertype,
                                "template" => "orderdetail",
                                "id"       => $ret[0]['id']
                            );
                        }else{
                            return array("state" => 200, "info" => self::$langData['siteConfig'][21][162]);//订单不存在！
                        }

                        // 交友没有订单详情页
                    }elseif($ordertype == "dating"){
                        $data = array(
                            "service"  => $ordertype,
                            "template" => "payreturn",
                            "ordernum" => $order
                        );

                        //单个订单跳转到订单详细页
                    }elseif($ordertype == "huodong"){
                        $data = array(
                            "service"  => "member",
                            "type"     => "user",
                            "template" => "huodong-join"
                        );
                    }elseif($ordertype == "live"){
                        $sql = $dsql->SetQuery("SELECT `live_id` FROM `#@__live_payorder` WHERE `order_id` = '$body'");
                        $ret = $dsql->dsqlOper($sql, "results");

                        $data = array(
                            "service"  => $ordertype,
                            "template" => "h_detail",
                            "id"       => $ret[0]['live_id']
                        );
                    }else{
                        if($ordertype == 'homemaking'){//家政线上付款 付款成功直接跳转原来的订单页面
                            $sql = $dsql->SetQuery("SELECT `id`, `ordernumid` FROM `#@__".$ordertype."_order` WHERE `ordernum` = '$body'");
                        }elseif($ordertype == 'travel'){
                            $sql = $dsql->SetQuery("SELECT `id`, `type`, `ordernum` FROM `#@__".$ordertype."_order` WHERE `ordernum` = '$body'");
                        }else{
                            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__".$ordertype."_order` WHERE `ordernum` = '$body'");
                        }
                        $ret = $dsql->dsqlOper($sql, "results");
                        if($ret){
                            if($ordertype == 'homemaking'){
                                if(!empty($ret[0]['ordernumid'])){
                                    $ordernumid = $ret[0]['ordernumid'];
                                    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__".$ordertype."_order` WHERE `ordernum` = '$ordernumid'");
                                    $res = $dsql->dsqlOper($sql, "results");
                                    if($res){
                                        $id = $res[0]['id'];
                                    }
                                }else{
                                    $id = $ret[0]['id'];
                                }
                                $data = array(
                                    "service"  => "member",
                                    "type"     => "user",
                                    "template" => "orderdetail",
                                    "module"   => $ordertype,
                                    "id"       => $id
                                );
                            }elseif($ordertype == 'travel'){
                                if($ret[0]['type'] == 3){//酒店
                                    $data = array(
                                        "service"     => "travel",
                                        "template"    => "travel-hotelstate",
                                        "ordernum"    => $travelorder
                                    );
                                }else{//景点门票 周边游 签证
                                    $data = array(
                                        "service"     => "travel",
                                        "template"    => "travel-ticketstate",
                                        "ordernum"    => $travelorder
                                    );
                                }
                            }else{
                                $data = array(
                                    "service"  => "member",
                                    "type"     => "user",
                                    "template" => "orderdetail",
                                    "module"   => $ordertype,
                                    "id"       => $ret[0]['id']
                                );
                            }


                        }else{
                            return array("state" => 200, "info" => self::$langData['siteConfig'][21][162]);//订单不存在！
                        }

                    }

                }

                $url = getUrlPath($data);
                return $url;

            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][28]);//交易没有支付成功
            }

        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][21][162]);//交易没有支付成功
        }


    }


    /**
     * 支付成功
     * 此处进行支付成功后的操作，例如发送短信等服务
     *
     */
    public function paySuccess(){
        $param = $this->param;

        // print_r($param);die;
        if(!empty($param)){
            global $dsql;
            global $langData;

            $paytype  = $param['paytype'];
            $ordernum = $param['ordernum'];

            $archives = $dsql->SetQuery("SELECT `amount`, `uid`, `body` FROM `#@__pay_log` WHERE `ordernum` = '$ordernum' AND `state` = 1");
            $results = $dsql->dsqlOper($archives, "results");
            if($results){

                $amount = $results[0]['amount'];
                $uid    = $results[0]['uid'];
                $body   = unserialize($results[0]['body']);
                $date   = GetMkTime(time());

                if($body && is_array($body)){
                    $type = $body['type'];

                    //充值
                    if($type == "deposit"){
                        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$amount' WHERE `id` = '$uid'");
                        $dsql->dsqlOper($archives, "update");

                        //保存操作日志
                        $info = $langData['siteConfig'][21][76].";".$paytype;  //账户充值
                        $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '1', '$amount', '$info', '$date')");
                        $dsql->dsqlOper($archives, "update");

                        //升级
                    }elseif($type == "upgrade"){
                        $this->upgradeSuccess($body);

                        //商家入驻
                    }elseif($type == "join" || $type == "join_renew" || $type == "join_upgrade"){
                        $this->joinSuccess($body, $ordernum);

                        //缴纳保障金
                    }elseif($type == "promotion"){
                        $this->promotionSuccess($body);

                        // 发布信息
                    }elseif($type == "fabu" || $type == "fabuPay"){
                        $this->fabuPaySuccess($body);

                        //信息刷新置顶
                    }elseif($type == 'refreshTop'){
                        $busiHandlers = new handlers("siteConfig", "refreshTopSuccess");
                        $busiConfig = $busiHandlers->getHandle($ordernum);
                    }

                }

            }

        }

    }


    /**
     * 会员升级后的相关操作
     */
    public function upgradeSuccess($param){

        global $dsql;
        global $userLogin;
        global $langData;

        $userid  = $param['userid'];
        $level   = (int)$param['level'];
        $day     = (int)$param['day'];
        $price   = sprintf("%.2f", $param['price']);
        $daytype = $param['daytype'];
        $title   = $param['title'];
        $balance = sprintf("%.2f", $param['balance']);

        $userinfo  = $userLogin->getMemberInfo($userid);
        $money = sprintf("%.2f", $userinfo['money']);

        //如果使用了余额支付，二次验证帐户余额是否足够
        if($balance){
            if($money < $balance){
                die($langData['siteConfig'][21][83]);   //会员余额与支付时所选金额不匹配，升级失败！
            }
        }

        //查询会员是否已经是会员
        $sql = $dsql->SetQuery("SELECT `level`, `expired` FROM `#@__member` WHERE `id` = $userid");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $_level = $ret[0]['level'];
            $_expired = $ret[0]['expired'];

            //如果已经开通并且是同级会员，在原有结束时间上增加相应天数
            if($_level == $level){
                $date = date("Y-m-d H:i:s", $_expired);
                $newDate = strtotime("{$date}+{$day} {$daytype}");
                $sql = $dsql->SetQuery("UPDATE `#@__member` SET `expired` = $newDate, `expired_notify_day` = 0, `expired_notify_week` = 0, `expired_notify_month` = 0 WHERE `id` = $userid");
                $dsql->dsqlOper($sql, "update");

                //如果还没有开通会员或者是升级成为其他类型的会员
            }else{
                $newDate = strtotime("+{$day} {$daytype}");
                $sql = $dsql->SetQuery("UPDATE `#@__member` SET `level` = $level, `expired` = $newDate, `expired_notify_day` = 0, `expired_notify_week` = 0, `expired_notify_month` = 0 WHERE `id` = $userid");
                $dsql->dsqlOper($sql, "update");
            }

            //保存操作日志
            $date = time();
            $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$price', '$title', '$date')");
            $dsql->dsqlOper($archives, "update");

            //余额支付扣除会员余额
            if($balance){
                //扣除会员余额
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$balance' WHERE `id` = '$userid'");
                $dsql->dsqlOper($archives, "update");

                //保存操作日志
                $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$balance', '$title', '$date')");
                $dsql->dsqlOper($archives, "update");
            }

            //消息通知
            $param['username'] = $userinfo['nickname'];
            $param['expired'] = date("Y-m-d H:i:s", $newDate);
            $param['fields'] = array(
                'keyword1' => '用户名',
                'keyword2' => '开通时长',
                'keyword3' => '开通费用',
                'keyword4' => '到期时间'
            );

            updateMemberNotice($userid, "会员-升级成功", array(), $param);

        }
    }


    /**
     * 提现卡号记录
     * @return array
     */
    public function withdraw_card(){

        $param =  $this->param;
        $type  = $param['type'];

        global $dsql;
        global $userLogin;
        global $langData;
        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $where = " AND `bank` != 'alipay' AND `bank` != 'weixin'";
        if($type == "alipay"){
            $where = " AND `bank` = 'alipay'";
        }

        $sql = $dsql->SetQuery("SELECT `id`, `bank`, `cardnum`, `cardname` FROM `#@__member_withdraw_card` WHERE `uid` = '$userid'".$where." ORDER BY `id` DESC");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $list = array();
            foreach ($ret as $key => $value) {
                if($value['bank'] != "alipay"){
                    $cardnumLast = substr($value['cardnum'], -4);
                }

                array_push($list, array("id" => $value['id'], "bank" => $value['bank'], "cardnum" => $value['cardnum'], "cardname" => $value['cardname'], "cardnumLast" => $cardnumLast));
            }
            return $list;
        }

    }


    /**
     * 删除提现卡号记录
     * @return array
     */
    public function withdraw_card_del(){

        $param =  $this->param;
        $id  = $param['id'];
        if(empty($id)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][29]);//请选择要删除的历史记录

        global $dsql;
        global $userLogin;
        global $langData;
        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $sql = $dsql->SetQuery("DELETE FROM `#@__member_withdraw_card` WHERE `uid` = '$userid' AND `id` in ($id)");
        $dsql->dsqlOper($sql, "update");

    }




    /**
     * 提现
     * @return array
     */
    public function withdraw(){

        global $dsql;
        global $userLogin;
        global $langData;

        $param    =  $this->param;
        $bank     = $param['bank'];
        $cardnum  = $param['cardnum'];
        $cardname = $param['cardname'];
        $amount   = $param['amount'];
        $date     = GetMkTime(time());

        global $cfg_minWithdraw;  //起提金额
		global $cfg_maxWithdraw;  //最多提现
		global $cfg_withdrawFee;  //手续费
        global $cfg_maxCountWithdraw;  //每天最多提现次数
        global $cfg_maxAmountWithdraw;  //每天最多提现金额
		global $cfg_withdrawCycle;  //提现周期  0不限制  1每周  2每月
		global $cfg_withdrawCycleWeek;  //周几
		global $cfg_withdrawCycleDay;  //几日
		global $cfg_withdrawPlatform;  //提现平台
        global $cfg_withdrawCheckType;  //付款方式
		global $cfg_withdrawNote;  //提现说明

        $cfg_minWithdraw = (float)$cfg_minWithdraw;
		$cfg_maxWithdraw = (float)$cfg_maxWithdraw;
		$cfg_withdrawFee = (float)$cfg_withdrawFee;
		$cfg_withdrawCycle = (int)$cfg_withdrawCycle;
        $cfg_withdrawCheckType = (int)$cfg_withdrawCheckType;
		$withdrawPlatform = $cfg_withdrawPlatform ? unserialize($cfg_withdrawPlatform) : array('weixin', 'alipay', 'bank');

		//提现周期
		if($cfg_withdrawCycle){
			//周几
			if($cfg_withdrawCycle == 1){

				$week = date("w", time());
				if($week != $cfg_withdrawCycleWeek){
					$array = $langData['siteConfig'][34][5];  //array('周日', '周一', '周二', '周三', '周四', '周五', '周六')
                    return array("state" => 200, "info" => str_replace('1', $array[$week], $langData['siteConfig'][36][0]));  //当前不可提现，提现时间：每周一
				}

			//几日
			}elseif($cfg_withdrawCycle == 2){

				$day = date("d", time());
				if($day != $cfg_withdrawCycleDay){
                    return array("state" => 200, "info" => str_replace('1', $cfg_withdrawCycleDay, $langData['siteConfig'][36][1]));  //当前不可提现，提现时间：每月1日
				}

			}
		}

        if((($bank == 'weixin' || $bank == 'alipay') && !in_array($bank, $withdrawPlatform)) || ($bank != 'weixin' && $bank != 'alipay' && !in_array('bank', $withdrawPlatform))){
            return array("state" => 200, "info" => $langData['siteConfig'][36][2]);  //不支持的提现方式
        }

        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        if(empty($bank) || ($bank != 'weixin' && (empty($cardnum) || empty($cardname))) || empty($amount)) return array("state" => 200, "info" => $langData['siteConfig'][33][30]);//请填写完整！

        $this->param['id'] = $userid;
        $detail = $this->detail();

        if(($detail['userType'] == 2 && $detail['licenseState'] != 1) || ($detail['userType'] == 1 && $detail['certifyState'] != 1)){
            return array("state" => 200, "info" => $langData['siteConfig'][33][49]);  //请先进行实名认证
        }

        if($cfg_minWithdraw && $amount < $cfg_minWithdraw){
            return array("state" => 200, "info" => str_replace('1', $cfg_minWithdraw, $langData['siteConfig'][36][3]));  //起提金额：1元
        }

        if($cfg_maxWithdraw && $amount > $cfg_maxWithdraw){
            return array("state" => 200, "info" => str_replace('1', $cfg_maxWithdraw, $langData['siteConfig'][36][4]));  //单次最多提现：1元
        }

        //统计当天交易量
        if($cfg_maxCountWithdraw || $cfg_maxAmountWithdraw){
            $start = GetMkTime(date("Y-m-d"));
            $end = $start + 86400;
            $sql = $dsql->SetQuery("SELECT SUM(`amount`) amount, COUNT(`id`) count FROM `#@__member_withdraw` WHERE `uid` = '$userid' AND `tdate` >= $start AND `tdate` < $end");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $todayAmount = $ret[0]['amount'];
                $todayCount = $ret[0]['count'];

                if($cfg_maxCountWithdraw && $todayCount > $cfg_maxCountWithdraw){
                    return array("state" => 200, "info" => str_replace('1', $cfg_maxCountWithdraw, $langData['siteConfig'][36][5]));  //每天最多提现1次
                }

                if($cfg_maxAmountWithdraw && $todayAmount > $cfg_maxAmountWithdraw){
                    return array("state" => 200, "info" => str_replace('1', $cfg_maxAmountWithdraw, $langData['siteConfig'][36][6]));  //每天最多提现1元
                }

            }
        }

        if($detail['money'] < $amount) return array("state" => 200, "info" => $langData['siteConfig'][21][84]);  //帐户余额不足，提现失败！

        //验证类型
        $realname = $wechat_openid = '';
        $sql = $dsql->SetQuery("SELECT `realname`, `wechat_openid` FROM `#@__member` WHERE `id` = " . $userid);
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $realname = $ret[0]['realname'];
            $wechat_openid = $ret[0]['wechat_openid'];

            if($bank == 'weixin' && !$wechat_openid){
                return array("state" => 200, "info" => $langData['siteConfig'][36][7]);  //请先绑定微信账号
            }
        }

        $ordernum = create_ordernum();

        //判断银行卡是否存在
        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_withdraw_card` WHERE `uid` = '$userid' AND `bank` = '$bank' AND `cardnum` = '$cardnum' AND `cardname` = '$cardname'");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $cid = $ret[0]['id'];
        }else{
            //添加银行卡
            $sql = $dsql->SetQuery("INSERT INTO `#@__member_withdraw_card` (`uid`, `bank`, `cardnum`, `cardname`, `date`) VALUES ('$userid', '$bank', '$cardnum', '$cardname', '$date')");
            $cid = $dsql->dsqlOper($sql, "lastid");
        }

        if(is_numeric($cid)){

            //会员申请后自动付款
            if(!$cfg_withdrawCheckType && ($bank == 'weixin' || $bank == 'alipay')){

                $amount_ = $cfg_withdrawFee ? $amount * (100 - $cfg_withdrawFee) / 100 : $amount;
                $amount_ = sprintf("%.2f", $amount_);

                //微信提现
                if($bank == "weixin"){
                    $order = array(
                        'ordernum' => $ordernum,
                        'openid' => $wechat_openid,
                        'name' => $realname,
                        'amount' => $amount_
                    );

                    require_once(HUONIAOROOT."/api/payment/wxpay/wxpayTransfers.php");
                    $wxpayTransfers = new wxpayTransfers();
                    $return = $wxpayTransfers->transfers($order);

                    if($return['state'] != 100){
                        return $return;
                    }
                }else{

                    if($realname != $cardname){
                        return array("state" => 200, "info" => $langData['siteConfig'][36][8]);  //申请失败，提现到的账户真实姓名与实名认证信息不一致！
                    }
                    $order = array(
                        'ordernum' => $ordernum,
                        'account' => $cardnum,
                        'name' => $cardname,
                        'amount' => $amount_
                    );

                    require_once(HUONIAOROOT."/api/payment/alipay/alipayTransfers.php");
                    $alipayTransfers = new alipayTransfers();
                    $return = $alipayTransfers->transfers($order);

                    if($return['state'] != 100){
                        return $return;
                    }
                }

                $rdate = $return['date'];
                $payment_no = $return['payment_no'];

                $note = '提现成功，付款单号：'. $payment_no;

                //扣除余额
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$amount' WHERE `id` = '$userid'");
                $dsql->dsqlOper($archives, "update");

                //保存操作日志
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$amount', '余额提现：$payment_no', '$rdate')");
				$dsql->dsqlOper($archives, "update");

                //生成提现记录
                    $sql = $dsql->SetQuery("INSERT INTO `#@__member_withdraw` (`uid`, `bank`, `cardnum`, `cardname`, `amount`, `tdate`, `state`, `note`, `rdate`) VALUES ('$userid', '$bank', '$cardnum', '$cardname', '$amount', '$date', 1, '$note', '$rdate')");
                $wid = $dsql->dsqlOper($sql, "lastid");

                if(is_numeric($wid)){

                    //自定义配置
                    $param = array(
        				"service"  => "member",
        				"type"     => "user",
        				"template" => "withdraw_log_detail",
        				"id"       => $wid
        			);

        			$config = array(
        				"username" => $realname,
        				"amount" => $amount,
        				"date" => date("Y-m-d H:i:s", $rdate),
        				"info" => $note,
        				"fields" => array(
        					'keyword1' => '提现金额',
        					'keyword2' => '提现时间',
        					'keyword3' => '提现状态'
        				)
        			);

                    updateMemberNotice($userid, "会员-提现申请审核通过", $param, $config);

                    return $wid;
                }else{
                    //如果数据库写入失败，返回字符串，前端跳到提现列表页
                    return 'error';
                }

            }

            //生成提现记录
            $sql = $dsql->SetQuery("INSERT INTO `#@__member_withdraw` (`uid`, `bank`, `cardnum`, `cardname`, `amount`, `tdate`, `state`) VALUES ('$userid', '$bank', '$cardnum', '$cardname', '$amount', '$date', 0)");
            $wid = $dsql->dsqlOper($sql, "lastid");

            if(is_numeric($wid)){

                // 减去余额、冻结金额
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$amount', `freeze` = `freeze` + '$amount' WHERE `id` = '$userid'");
                $dsql->dsqlOper($archives, "update");

                return $wid;
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][85].'_201');  //提交失败！
            }

        }else{
            return array("state" => 200, "info" => $langData['siteConfig'][21][85].'_200');  //提交失败！
        }

    }


    /**
     * 提现记录
     * @return array
     */
    public function withdraw_log(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $state = $page = $pageSize = 0;

        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $state     = $this->param['state'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $where = "";
        if($state != ""){
            $where = " AND `state` = $state";
        }

        // $archives = $dsql->SetQuery("SELECT * FROM `#@__member_withdraw` WHERE `uid` = $userid".$where." ORDER BY `id` DESC");
        $archives = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member_withdraw` WHERE `uid` = $userid".$where." UNION ALL SELECT COUNT(`id`) total FROM `#@__member_putforward` WHERE `userid` = $userid");
        $result = $dsql->dsqlOper($archives, "results");

        //总条数
        $totalCount = 0;
        if($result){
            foreach ($result as $key => $value) {
                $totalCount += $value['total'];
            }
        }
        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);   //暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $archives = $dsql->SetQuery("SELECT `id`, 'w' as tab FROM `#@__member_withdraw` WHERE `uid` = $userid".$where." UNION ALL SELECT `id`, 'p' as tab FROM `#@__member_putforward` WHERE `userid` = $userid ORDER BY `id` DESC");
        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives.$where, "results");

        $param = array(
            "service"  => "member",
            "type"     => "user",
            "template" => "withdraw_log_detail",
            "id"       => "%id"
        );
        $url = getUrlPath($param);

        if($results){
            foreach($results as $key => $val){
                $id = $val['id'];
                $tab = $val['tab'];

                $list[$key]['id']       = $val['id'];
                $list[$key]['tab']      = $tab;

                if($tab == "w"){
                    $sql = $dsql->SetQuery("SELECT *  FROM `#@__member_withdraw` WHERE `id` = $id");
                    $res = $dsql->dsqlOper($sql, "results");
                    $res = $res[0];

                    $list[$key]['bank']     = $res['bank'];
                    $list[$key]['cardnum']  = $res['cardnum'];
                    $list[$key]['cardname'] = $res['cardname'];
                    $list[$key]['amount']   = $res['amount'];
                    $list[$key]['tdate']    = $res['tdate'];
                    $list[$key]['state']    = $res['state'];
                    $list[$key]['url']      = str_replace("%id", $res['id'], $url);

                }elseif($tab == "p"){
                    $sql = $dsql->SetQuery("SELECT *  FROM `#@__member_putforward` WHERE `id` = $id");
                    $res = $dsql->dsqlOper($sql, "results");
                    $res = $res[0];

                    $list[$key]['type']     = $res['type'];
                    $list[$key]['bank']     = $res['bank'];
                    $list[$key]['order_id'] = $res['order_id'];
                    $list[$key]['cardname'] = $res['cardname'];
                    $list[$key]['amount']   = $res['amount'];
                    $list[$key]['tdate']    = $res['pubdate'];
                    $list[$key]['paydate']  = $res['paydate'];
                    $list[$key]['state']    = $res['state'];
                    $list[$key]['url']      = str_replace("%id", $res['id'], $url)."?type=p";
                }
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);

    }


    /**
     * 现金与积分兑换
     * @return array
     */
    public function convert(){
        global $dsql;
        global $userLogin;
        global $langData;
        $param =  $this->param;
        $amount = $param['amount'];
        $paypwd = $param['paywd'] ? $param['paywd'] : $param['paypwd'];

        $date = GetMkTime(time());

        global $userLogin;
        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        if($amount <= 0){
            die($langData['siteConfig'][21][86]);
        }

        $this->param['id'] = $userid;
        $detail = $this->detail();
        if($detail['money'] < $amount) return array("state" => 200, "info" => $langData['siteConfig'][21][87]);   //帐户余额不足，兑换失败！

        if(empty($paypwd)){
            die($langData['siteConfig'][21][88]);   //请输入支付密码！
        }

        //验证支付密码
        $archives = $dsql->SetQuery("SELECT `id`, `paypwd` FROM `#@__member` WHERE `id` = '$userid'");
        $results  = $dsql->dsqlOper($archives, "results");
        $res = $results[0];
        $hash = $userLogin->_getSaltedHash($paypwd, $res['paypwd']);
        if($res['paypwd'] != $hash) return array("state" => 200, "info" => $langData['siteConfig'][21][89]);


        global $cfg_pointRatio;
        $totalConvert = $amount * $cfg_pointRatio;

        //扣除金额
        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$amount' WHERE `id` = '$userid'");
        $dsql->dsqlOper($archives, "update");

        //保存操作日志
        $info = $langData['siteConfig'][21][90] . '：' . $amount;
        $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$amount', '$info', '$date')");
        $dsql->dsqlOper($archives, "update");


        //增加积分
        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + '$totalConvert' WHERE `id` = '$userid'");
        $dsql->dsqlOper($archives, "update");

        //保存操作日志
        $info = $langData['siteConfig'][21][91] . '：' . $amount;
        $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$totalConvert', '$info', '$date')");
        $dsql->dsqlOper($archives, "update");

        return $langData['siteConfig'][20][217];  //兑换成功！

    }


    /**
     * 积分转赠
     * @return array
     */
    public function transfer(){
        global $dsql;
        global $userLogin;
        global $cfg_pointFee;
        global $langData;

        $param =  $this->param;
        $user   = $param['user'];
        $amount = $param['amount'];
        $paypwd = $param['paypwd'];

        $date = GetMkTime(time());

        global $userLogin;
        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        //验证会员
        $toUser = 0;
        $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` = '$user'");
        $results  = $dsql->dsqlOper($archives, "results");
        if($results){
            $toUser = $results[0]['id'];
        }else{
            return array("state" => 200, "info" => $langData['siteConfig'][21][92]);   //对方会员不存在，请确认后重试！
        }

        if($userid == $toUser) return array("state" => 200, "info" => $langData['siteConfig'][21][93]);   //不可以转赠给自己！

        if($amount <= 0) return array("state" => 200, "info" => $langData['siteConfig'][21][94]);  //转赠数量必须为整数或小数，小数点后不超过2位。

        $this->param['id'] = $userid;
        $detail = $this->detail();
        $username = $detail['username'];
        if($detail['point'] < $amount) return array("state" => 200, "info" => $langData['siteConfig'][21][95]);   //帐户积分不足，转赠失败！

        if(empty($paypwd)) return array("state" => 200, "info" => $langData['siteConfig'][21][88]);   //请输入支付密码！

        //验证支付密码
        $archives = $dsql->SetQuery("SELECT `id`, `paypwd` FROM `#@__member` WHERE `id` = '$userid'");
        $results  = $dsql->dsqlOper($archives, "results");
        $res = $results[0];
        $hash = $userLogin->_getSaltedHash($paypwd, $res['paypwd']);
        if($res['paypwd'] != $hash) return array("state" => 200, "info" => $langData['siteConfig'][21][89]);  //支付密码输入错误，请重试！


        global $cfg_pointRatio;
        $totalTransfer = $amount - $amount * $cfg_pointFee / 100;

        //减少积分
        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` - '$amount' WHERE `id` = '$userid'");
        $dsql->dsqlOper($archives, "update");

        //保存操作日志
        $info = $langData['siteConfig'][21][96] . "：$user => $amount";  //转出给会员
        $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$amount', '$info', '$date')");
        $dsql->dsqlOper($archives, "update");

        //增加积分
        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + '$totalTransfer' WHERE `id` = '$toUser'");
        $dsql->dsqlOper($archives, "update");

        //保存操作日志
        $info = "$username ".$langData['siteConfig'][19][730]." => $totalTransfer";  //转赠
        $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$toUser', '1', '$totalTransfer', '$info', '$date')");
        $dsql->dsqlOper($archives, "update");

        return $langData['siteConfig'][21][97];  //转赠成功！

    }


    /**
     * 交友会员注册
     * @return array
     */
    public function regDating(){
        global $dsql;
        global $userLogin;
        global $cfg_regstatus;
        global $cfg_regclosemessage;
        global $langData;
        if($cfg_regstatus == 0){
            die('200|'.$cfg_regclosemessage);
        }

        $type   = (int)$this->param['type'];
        $mobile = $this->param['mobile'];
        $email = $this->param['email'];
        $password = $this->param['password'];
        $sex   = (int)$this->param['sex'];
        $year  = (int)$this->param['year'];
        $month = (int)$this->param['month'];
        $day   = (int)$this->param['day'];
        $addr  = (int)$this->param['addr'];

        $username = "";

        if($type == 1){
            //手机
            if(empty($mobile)){
                die('205|' . $langData['siteConfig'][20][239]);  //请输入手机号
            }
            preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $mobile, $matchPhone);
            if(!$matchPhone){
                die('205|' . $langData['siteConfig'][21][98]);  //手机号码格式错误！
            }

            $archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `phone` = '$mobile'");
            $return = $dsql->dsqlOper($archives, "results");
            if($return){
                die('205|' . $langData['siteConfig'][21][98]);  //此手机号码已被注册！
            }

            $username = $mobile;

        }elseif($type == 2){

            //邮箱
            if(empty($email)){
                die('204|' . $langData['siteConfig'][21][36]);  //请输入邮箱地址！
            }
            preg_match('/\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/', $email, $matchEmail);
            if(!$matchEmail){
                die('204|' . $langData['siteConfig'][21][100]);  //邮箱地址格式错误！
            }

            $archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `email` = '$email'");
            $return = $dsql->dsqlOper($archives, "results");
            if($return){
                die('204|' . $langData['siteConfig'][21][101]);  //此邮箱地址已被注册！
            }

            $username = $email;

        }else{
            die('204|' . $langData['siteConfig'][21][102]);  //请选择注册方式！
        }

        //验证密码
        if(empty($password)){
            die('202|' . $langData['siteConfig'][20][164]);   //请输入密码
        }
        preg_match('/^.{5,}$/', $password, $matchPassword);
        if(!$matchPassword){
            die('202|' . $langData['siteConfig'][21][103]);  //密码长度最少为5位！
        }

        //验证区域
        if(empty($addr)){
            die('202|' . $langData['siteConfig'][21][68]);  //请选择所在区域！
        }

        $birthday = GetMkTime($year."-".$month."-".$day);

        $passwd   = $userLogin->_getSaltedHash($password);
        $regtime  = GetMkTime(time());
        $regip    = GetIP();
        $regipaddr = getIpAddr($regip);

        $archives = $dsql->SetQuery("SELECT `regtime` FROM `#@__member` WHERE `regip` = '$regip' AND `state` = 1 ORDER BY `id` DESC LIMIT 0, 1");
        $return = $dsql->dsqlOper($archives, "results");
        if($return){
            global $cfg_regtime;
            if(round(($regtime - $return[0]['regtime'])/60) < $cfg_regtime){
                die('200|' . str_replace('1', $cfg_regtime, $langData['siteConfig'][21][104]));   //本站限制每次注册间隔时间为1分钟，请稍后再注册。
            }
        }

        //保存到主表
        $archives = $dsql->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `email`, `emailCheck`, `phone`, `phoneCheck`, `regtime`, `regip`, `regipaddr`, `state`) VALUES ('1', '$username', '$passwd', '$username', '$email', '0', '$mobile', '0', '$regtime', '$regip', '$regipaddr', '1')");
        $aid = $dsql->dsqlOper($archives, "lastid");

        if($aid){

            //论坛同步
            if($type == 2){
                $data['username'] = $username;
                $data['password'] = $password;
                $data['email']    = $email;
                $userLogin->bbsSync($data, "register");
            }

            //自动登录
            $ureg = $userLogin->memberLogin($username, $password);

            //注册交友会员
            $sql = $dsql->SetQuery("INSERT INTO `#@__dating_member` (`userid`, `addrid`, `jointime`) VALUES ('$aid', '$addr', '$regtime')");
            $ret = $dsql->dsqlOper($sql, "update");

            die('100|' . $langData['siteConfig'][20][172]);   //注册成功！

        }else{
            die('200|' . $langData['siteConfig'][20][175]);   //网络错误，注册失败！
        }
        return;

    }



    /**
     * 会员入驻商家
     *
     */
    public function joinBusiness(){
        global $dsql;
        global $userLogin;
        global $langData;
        global $cfg_secureAccess;
        global $cfg_basehost;

        $name       = $this->param['name'];
        $areaCode   = (int)$this->param['areaCode'];
        $areaCode   = empty($areaCode) ? "86" : $areaCode;
        $phone      = $this->param['phone'];
        $yzm        = $this->param['yzm'];
        $typeid     = (int)$this->param['typeid'];
        $type       = (int)$this->param['type'];
        $cost       = abs((int)$this->param['cost']);
        $useBalance = (int)$this->param['useBalance'];
        $type_      = $this->param['type_'];
        $paytype    = $this->param['paytype'];

        $isPC = !isMobile();

        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        if(empty($cityid)){
            $cityid = getCityId();
        }

        // 升级或续费 验证店铺状态
        $renew_upgrade = false;
        $nowBusi = array();
        if($type_ == "renew" || $type_ == "upgrade"){
            $type_temp = $type_;
            $type_ = "";
            $sql = $dsql->SetQuery("SELECT * FROM `#@__business_list` WHERE `uid` = $userid AND `state` != 4");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $type_ = $type_temp;
                $nowBusi = $ret[0];
                $renew_upgrade = true;
            }
        }
        if($type_ == "renew" || $type_ == "upgrade"){
            $name = $nowBusi['name'];
            $phone = $nowBusi['phone'];
            $typeid = $nowBusi['typeid'];

            if($type_ == "renew"){
                $type = $nowBusi['type'];
            }else{
                $type = 2;
            }
        }

        $ordertype = $type_ ? $type_ : "join";

        // print_r($nowBusi);die;

        $date = time();

        $busiHandlers = new handlers("business", "config");
        $busiConfig = $busiHandlers->getHandle();
        $busiConfig = $busiConfig['info'];

        if($type == 1){
            if(!$busiConfig['trialState'] && !$renew_upgrade) return array("state" => 200, "info" => $busiConfig['trialName']. self::$langData['siteConfig'][33][31]);//已停止入驻
            $priceCost = $busiConfig['trialCost'];
        }elseif($type == 2){
            if(!$busiConfig['enterpriseState'] && $type_ != "renew") return array("state" => 200, "info" => $busiConfig['enterpriseName']. self::$langData['siteConfig'][33][31]);//已停止入驻
            $priceCost = $busiConfig['enterpriseCost'];
        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][32]);//抱歉，入驻类型错误
        }
        if($priceCost){
            if($cost >= count($priceCost)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][33]);//抱歉，入驻时间不正确
            }
            $priceCost = $priceCost[$cost];

        }else{
            // return array("state" => 200, "info" => "抱歉，暂时无法入驻商家，请联系网站管理员");
        }

        if(empty($type)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][34]);   //请输入入驻类型

        if(!$renew_upgrade){

            if(empty($name)) return array("state" => 200, "info" => $langData['siteConfig'][20][330]);   //请输入姓名
            if(empty($phone)) return array("state" => 200, "info" => $langData['siteConfig'][20][239]);   //请输入手机号
            if(empty($typeid)) return array("state" => 200, "info" => $langData['siteConfig'][20][322]);  //请选择经营品类
            if(empty($yzm)) return array("state" => 200, "info" => $langData['siteConfig'][20][28]);  //请输入短信验证码

            //非国际版不需要验证区域码
            $acode = $areaCode;
            $archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
            $results = $dsql->dsqlOper($archives, "results");
            if($results){
                $international = $results[0]['international'];
                if(!$international){
                    $acode = "";
                }
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][3]);//短信平台未配置，发送失败！
            }

            //验证输入的验证码
            $archives = $dsql->SetQuery("SELECT `id`, `pubdate` FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'join' AND `user` = '".$acode.$phone."' AND `code` = '$yzm'");
            $results  = $dsql->dsqlOper($archives, "results");
            if(!$results){
                return array("state" => 200, "info" => $langData['siteConfig'][20][99]);  //验证码输入错误，请重试！
            }else{

                //5分钟有效期
                if($date - $results[0]['pubdate'] > 300) return array("state" => 200, "info" => $langData['siteConfig'][21][33]);  //验证码已过期，请重新获取！

                //验证通过删除发送的验证码
                $archives = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'join' AND `user` = '".$acode.$phone."' AND `code` = '$yzm'");
                $dsql->dsqlOper($archives, "update");
            }
        }

        $ordernum = create_ordernum();
        $orderdate = GetMkTime(time());


        if($priceCost){
            $time = $priceCost['time'];
            $time_type = $priceCost['type'];
            $totalprice = $priceCost['price'];
        }else{
            $time = 0;
            $time_type = "";
            $totalprice = 0;
        }

        // 现金支付部分
        $payprice = $totalprice;
        // 余额支付部分
        $useBalance_ = 0;

        if($totalprice && $useBalance){
            $userinfo = $userLogin->getMemberInfo($userid);
            $usermoney = $userinfo['money'];
            if($usermoney > 0){
                $useBalance_ = $usermoney > $totalprice ? $totalprice : $usermoney;
                $payprice = $totalprice - $useBalance_;
            }
        }

        if($totalprice > 0){
            $param = array(
                "service"  => "member",
                "type"     => "user",
                "template" => "joinPay",
                "param" => "ordernum=".$ordernum
            );
        }else{
            $param_ = array(
                "buy_type" => $type,
                "buy_time" => $time,
                "buy_time_type" => $time_type,
                "totalPrice" => 0
            );
            if($renew_upgrade){
                $param = array(
                    "service"  => "member",
                    "type"     => "user",
                );
            }else{
                $param = array(
                    "service"  => "member",
                    "type"     => "user",
                    "template" => "business-config",
                );
            }
        }
        $url = getUrlPath($param);

        $param_pcurl = array(
            "service" => "member",
            "action" => "joinPay",
            "ordernum" => $ordernum,
            "useBalance" => $useBalance,
            "paytype" => $paytype,
            "ordertype" => $ordertype,
        );
        $d = array();
        foreach ($param_pcurl as $k => $v) {
            $d[] = $k."=".$v;
        }
        $pc_url = $cfg_secureAccess.$cfg_basehost."/include/ajax.php?".join("&", $d);

        $param_pc = array(
            "type" => $ordertype,
            "userid" => $userid,
            "totalPrice" => $totalprice,
            "paytype" => $paytype,
            "balance" => $useBalance_,
            "amount" => $payprice,
            "buy_type" => $type,
            "buy_time" => $time,
            "buy_time_type" => $time_type,
        );

        //查询是否开通过
        $sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__business_list` WHERE `uid` = $userid");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){

            $id    = $ret[0]['id'];
            $state = $ret[0]['state'];

            $param_['business'] = $id;
            $param_pc['business'] = $id;

            // 删除未支付的订单
            $sql = $dsql->SetQuery("DELETE FROM `#@__business_order` WHERE `bid` = $id AND `state` = 0");
            $dsql->dsqlOper($sql, "update");

            //已经提交过但还没有支付
            if($state == 4){

                //更新资料
                $sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `type` = '$type', `cityid` = '$cityid', `typeid` = '$typeid', `name` = '$name', `areaCode` = '$areaCode', `phone` = '$phone' WHERE `id` = $id");
                $ret = $dsql->dsqlOper($sql, "update");
                if($ret == "ok"){

                    $sql = $dsql->SetQuery("INSERT INTO `#@__business_order` (`bid`, `ordernum`, `totalprice`, `offer`, `balance`, `paytype`, `amount`, `date`, `paydate`, `state`, `ordertype`, `type`, `time`, `time_type`) VALUES ('$id', '$ordernum', '$totalprice', '0', '$useBalance_', '$paytype', '0', '$date', '0', '0', '$ordertype', '$type', '$time', '$time_type')");
                    $oid = $dsql->dsqlOper($sql, "lastid");
                    if(!is_numeric($oid)){
                        return array("state" => 200, "info" => $langData['siteConfig'][21][85]);  //提交失败！
                    }

                    // 现金支付部分为0
                    if($isPC){
                        if($payprice == 0){
                            $this->joinSuccess($param_pc, $ordernum);
                            return "ok";
                        }else{
                            return $pc_url;
                        }
                    }elseif($totalprice == 0){
                        $this->joinSuccess($param_, $ordernum);
                    }

                    return $url;
                }else{
                    return array("state" => 200, "info" => $langData['siteConfig'][21][85]);  //提交失败！
                }

                // 续费或升级
            }elseif($renew_upgrade){

                $sql = $dsql->SetQuery("INSERT INTO `#@__business_order` (`bid`, `ordernum`, `totalprice`, `offer`, `balance`, `paytype`, `amount`, `date`, `paydate`, `state`, `ordertype`, `type`, `time`, `time_type`) VALUES ('$id', '$ordernum', '$totalprice', '0', '$useBalance_', '$paytype', '0', '$date', '0', '0', '$ordertype', '$type', '$time', '$time_type')");
                $oid = $dsql->dsqlOper($sql, "lastid");
                if(!is_numeric($oid)){
                    return array("state" => 200, "info" => $langData['siteConfig'][21][85]);  //提交失败！
                }

                // 现金支付部分为0
                if($isPC){
                    if($payprice == 0){
                        $this->joinSuccess($param_pc, $ordernum);
                        return "ok";
                    }else{
                        return $pc_url;
                    }
                }elseif($totalprice == 0){
                    $this->joinSuccess($param_, $ordernum);
                }


                return $url;
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][105]);  //您已经申请，无需重复提交！
            }

        }else{

            //打印机
            $print_config = serialize(array());

            $addrid = 0;
            $sql = $dsql->SetQuery("INSERT INTO `#@__business_list` (`cityid`, `uid`, `logo`, `type`, `typeid`, `addrid`, `address`, `jingying`, `pubdate`, `state`, `name`, `areaCode`, `phone`, `email`, `cardnum`, `company`, `licensenum`, `license`, `accounts`, `cardfront`, `cardbehind`, `banner`, `pics`, `certify`, `body`, `wifi`, `bind_print`, `print_config`, `print_state`, `stateinfo`, `video`, `qj_file`, `custom_nav`) VALUES ('$cityid', '$userid', '$logo', '$type', '$typeid', '$addrid', '$address', '$jingying', '$date', '4', '$name', '$areaCode', '$phone', '$email', '$cardnum', '$company', '$licensenum', '$license', '$accounts', '$cardfront', '$cardbehind', '', '', '', '', '0', '0', '$print_config', '0', '', '', '', '')");
            $lid = $dsql->dsqlOper($sql, "lastid");
            if(is_numeric($lid)){
                $sql = $dsql->SetQuery("INSERT INTO `#@__business_order` (`bid`, `ordernum`, `totalprice`, `offer`, `balance`, `paytype`, `amount`, `date`, `paydate`, `state`, `ordertype`, `type`, `time`, `time_type`) VALUES ('$lid', '$ordernum', '$totalprice', '0', '$useBalance_', '$paytype', '0', '$date', '0', '0', '$ordertype', '$type', '$time', '$time_type')");
                $oid = $dsql->dsqlOper($sql, "lastid");
                if(!is_numeric($oid)){
                    return array("state" => 200, "info" => $langData['siteConfig'][21][85]);  //提交失败！
                }

                // 现金支付部分为0
                if($isPC){
                    if($payprice == 0){
                        $param_pc['business'] = $lid;
                        $this->joinSuccess($param_pc, $ordernum);
                        return "ok";
                    }else{
                        return $pc_url;
                    }
                }elseif($totalprice == 0){
                    $param_['business'] = $lid;
                    $this->joinSuccess($param_, $ordernum);
                }


                return $url;
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][85]);  //提交失败！
            }
        }

    }



    /**
     * 支付前检查 返回总费用
     */
    public function checkPayAmount(){
        global $dsql;
        global $userLogin;
        global $cfg_pointName;
        global $cfg_pointRatio;
        global $langData;

        $userid   = $userLogin->getMemberID();
        $param    = $this->param;

        $ordertype  = $param['ordertype'];    //订单类型
        $ordernum   = $param['ordernum'];    //订单号
        $usePinput  = $param['usePinput'];   //是否使用积分
        $point      = $param['point'];       //使用的积分
        $useBalance = $param['useBalance'];  //是否使用余额
        $balance    = $param['balance'];     //使用的余额
        $paypwd     = $param['paypwd'];      //支付密码
        $paytype    = $param['paytype'];     //支付方式

        $userid = $param['userid'] ? $param['userid'] : $userid;

        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        if(empty($ordertype) || empty($ordernum)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误

        $totalPrice = 0;

        // 商家入驻相关
        if($ordertype == "joinPay" || $ordertype == "join" || $ordertype == "renew" || $ordertype == "upgrade"){
            //查询是否有录入资料
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $business = $ret[0]['id'];
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][85]);  //提交失败！
            }

            // 验证订单
            $sql = $dsql->SetQuery("SELECT * FROM `#@__business_order` WHERE `bid` = $business AND `ordernum` = '$ordernum' AND `state` = 0");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $order = $ret[0];
                if($order['state'] != 0){
                    return array("state" => 200, "info" => $langData['siteConfig'][21][85]);  //提交失败！
                }
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][85]);  //提交失败！
            }
            $totalPrice = $order['totalprice'];

        // 发布信息支付
        }elseif($ordertype == "fabuPay"){
            $module = $param['module'];
            $tourl  = $param['tourl'];
            if(empty($module)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误
            global $cfg_fabuAmount;
            $fabuAmount = $cfg_fabuAmount ? unserialize($cfg_fabuAmount) : array();

            if($fabuAmount){
                $totalPrice = $fabuAmount[$module];
            }
        // 其他类型暂时不验证费用
        }else{
            $totalPrice = 9999999;
        }

        //查询会员信息
        $userinfo  = $userLogin->getMemberInfo();
        $usermoney = $userinfo['money'];
        $userpoint = $userinfo['point'];

        $tit      = array();
        $useTotal = 0;

        //判断是否使用积分，并且验证剩余积分
        if($usePinput == 1 && !empty($point)){
            if($userpoint < $point) return array("state" => 200, "info" => $langData['siteConfig'][21][103]);  //您的可用积分不足，支付失败！
            $useTotal += $point / $cfg_pointRatio;
            $tit[] = $cfg_pointName;
        }

        //判断是否使用余额，并且验证余额和支付密码
        if($useBalance == 1 && !empty($balance)){
            if(isMobile()){
                if(empty($paypwd)){
                    return array("state" => 200, "info" => $langData['siteConfig'][21][88]);  //请输入支付密码！
                }else{
                    //验证支付密码
                    $archives = $dsql->SetQuery("SELECT `id`, `paypwd` FROM `#@__member` WHERE `id` = '$userid'");
                    $results  = $dsql->dsqlOper($archives, "results");
                    $res = $results[0];
                    $hash = $userLogin->_getSaltedHash($paypwd, $res['paypwd']);
                    if($res['paypwd'] != $hash) return array("state" => 200, "info" => $langData['siteConfig'][21][89]);  //支付密码输入错误，请重试！
                }
            }
            //验证余额
            if($usermoney < $balance) return array("state" => 200, "info" => $langData['siteConfig'][20][213]);  //您的余额不足，支付失败！

            $useTotal += $balance;
            $tit[] = $langData['siteConfig'][19][363];  //余额
        }

        if($useTotal > $totalPrice) return array("state" => 200, "info" => str_replace('1', join($langData['siteConfig'][13][46], $tit), $langData['siteConfig'][21][104]));  //和  您使用的1超出订单总费用，请重新输入！

        return sprintf('%.2f', $totalPrice);

    }

    /**
     * 支付
     */
    public function pay(){
        global $dsql;
        global $userLogin;

        $param    = $this->param;

        $ordertype  = $param['ordertype'];   //类型
        if(empty($ordertype)) die(self::$langData['siteConfig'][30][47]);//操作错误！
        $ordertype = explode('_filter_', $ordertype);
        $ordertype = $ordertype[0];

        if(method_exists($this, $ordertype)){
            $this->$ordertype();
        }else{
            die(self::$langData['siteConfig'][30][47]);//操作错误！
        }

    }


    /**
     * 入驻商家支付签约
     */
    public function joinPay(){
        global $dsql;
        global $userLogin;
        global $cfg_pointName;
        global $cfg_pointRatio;
        global $langData;
        global $cfg_secureAccess;
        global $cfg_basehost;

        $userid   = $userLogin->getMemberID();
        $param    = $this->param;

        $ordernum   = $param['ordernum'];    //订单号
        $usePinput  = $param['usePinput'];   //是否使用积分
        $point      = $param['point'];       //使用的积分
        $useBalance = $param['useBalance'];  //是否使用余额
        $balance    = $param['balance'];     //使用的余额
        $paypwd     = $param['paypwd'];      //支付密码
        $paytype    = $param['paytype'];     //支付方式

        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        // 错误跳转页面
        $url = $cfg_secureAccess.$cfg_basehost;
        // 成功跳转页面
        $param_ = array(
            "service" => "member",
            "type" => "user",
            "template" => "business-config",
        );
        $sucUrl = getUrlPath($param_);

        $totalPrice = $this->checkPayAmount();

        if(is_array($totalPrice)){
            die($totalPrice['info']);
        }

        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $userid");
        $ret = $dsql->dsqlOper($sql, "results");
        $business = $ret[0]['id'];

        $sql = $dsql->SetQuery("SELECT * FROM `#@__business_order` WHERE `ordernum` = '$ordernum' AND `state` = 0");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $order = $ret[0];

            // 成功跳转页面
            $param_ = array(
                "service" => "member",
            );
            if($order['ordertype'] == "join"){
                $param_['type'] = "user";
                $param_['template'] = "business-config";
            }
            $sucUrl = getUrlPath($param_);
        }else{
            $url = $cfg_secureAccess.$cfg_basehost;
            header("location:".$url);
            die;
        }


        $ordertype = $order['ordertype'] == "join" ? "join" : "join_".$order['ordertype'];

        // 总金额大于0
        if($totalPrice > 0){

            //如果使用了余额，计算还需要支付多少费用
            $payprice = $totalPrice;
            $useBalance_ = 0;

            // 使用余额
            if($useBalance){
                $userinfo = $userLogin->getMemberInfo();
                $money = $userinfo['money'];

                if($money < $totalPrice){
                    $useBalance_ = $money;
                    $payprice = $totalPrice - $money;
                }else{
                    $useBalance_ = $totalPrice;
                    $payprice = 0;
                }
            }

            $param = array(
                "type" => $ordertype,
                "userid" => $userid,
                "business" => $business,
                "totalPrice" => $totalPrice,
                "paytype" => $paytype,
                "balance" => $useBalance_,
                "amount" => sprintf('%.2f', $payprice),
                "buy_type" => $order['type'],
                "buy_time" => $order['time'],
                "buy_time_type" => $order['time_type'],
            );

            //如果还需要在线支付的费用小于等于0，则直接扣除余额，更新会员类型及商家状态
            if($payprice <= 0){

                // print_r($param);die;

                $this->joinSuccess($param, $ordernum);

                //跳转至等待审核页面
                header("location:".$sucUrl);
                die;

            }

            //跳转至第三方支付页面
            createPayForm("member", $ordernum, $payprice, $paytype, $langData['siteConfig'][19][789], $param);  //商家入驻

            // 总金额为0
        }else{
            $param = array(
                "type" => $ordertype,
                "userid" => $userid,
                "business" => $business,
                "totalPrice" => $totalPrice,
                "paytype" => '',
                "balance" => 0,
                "amount" => 0,
                "buy_type" => $order['type'],
                "buy_time" => $order['time'],
                "buy_time_type" => $order['time_type'],
            );
            $this->joinSuccess($param, $ordernum);

            //跳转至等待审核页面
            header("location:".$sucUrl);
            die;
        }
    }


    /**
     * 入驻成功
     */
    private function joinSuccess($param, $ordernum){
        global $dsql;
        global $userinfo;
        global $userLogin;
        global $langData;
        global $cfg_pointName;
        // print_r($param);
        $userid        = $param['userid'];
        $business      = $param['business'];
        $paytype       = $param['paytype'];
        $balance       = (float)$param['balance'];
        $totalPrice    = (float)$param['totalPrice'];
        $buy_type      = $param['buy_type'];
        $buy_time      = $param['buy_time'];
        $buy_time_type = $param['buy_time_type'];
        $userinfo      = $userLogin->getMemberInfo();

        //如果使用了余额付款，二次验证余额是否大于等于支付时使用的金额
        if($balance > 0){
            $money = $userinfo['money'];
            if($money < $balance){
                die($langData['siteConfig'][21][106]);  //会员余额与支付时所选余额不匹配，开通失败！
            }
        }

        $sql = $dsql->SetQuery("SELECT `id`, `ordertype` FROM `#@__business_order` WHERE `ordernum` = '$ordernum' AND `state` = 0");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $orderDetail = $ret[0];
        }else{
            return;
        }

        $time = time();
        $ip = GetIP();
        $ipaddr = getIpAddr($ip);

        $busiHandlers = new handlers("business", "config");
        $busiConfig = $busiHandlers->getHandle();
        $busiConfig = $busiConfig['info'];

        if($buy_type == 1){
            $tit = $busiConfig['trialName'];
        }elseif($buy_type == 2){
            $tit = $busiConfig['enterpriseName'];
        }
        $tit .= $buy_time;
        if($buy_time_type == "day"){
            $tit .= $langData['siteConfig'][13][6];
        }elseif($buy_time_type == "month"){
            $tit .= $langData['siteConfig'][13][31];
        }elseif($buy_time_type == "year"){
            $tit .= $langData['siteConfig'][13][14];
        }

        if($orderDetail['ordertype'] == "join"){
            $title = $tit;
        }elseif($orderDetail['ordertype'] == "renew"){
            $title = $langData['siteConfig'][19][661] . $tit;
        }elseif($orderDetail['ordertype'] == "upgrade"){
            $title = "升级" . $tit;
        }

        // 更新商家信息、订单状态
        $archive = $dsql->SetQuery("SELECT * FROM `#@__business_list` WHERE `id` = $business");
        $result  = $dsql->dsqlOper($archive, "results");
        if($result){
            $busiDetail = $result[0];
            $userid = $busiDetail['uid'];

            // 类型相同，续期
            if($busiDetail['type'] == $buy_type){
                $expired_ = (int)$busiDetail['expired'];
                if($expired_ < $time){
                    $expired = strtotime("+{$buy_time} {$buy_time_type}");
                }else{
                    $expired = strtotime("+{$buy_time} {$buy_time_type}", $expired_);
                }
            }else{
                $expired = strtotime("+{$buy_time} {$buy_time_type}");
            }

            $state_f = "";
            $type_f = "";

            $bind_module_f = "";

            if($busiDetail['state'] == 4){
                $autoAudit = $buy_type == 1 ? $busiConfig['trialAutoAudit'] : $busiConfig['enterpriseAutoAudit'];
                $state = $autoAudit ? 1 : 3;
                $state_f = ", `state` = $state";

                if($autoAudit){
                    // 更新会员类型
                    $sql = $dsql->SetQuery("UPDATE `#@__member` SET `mtype` = 2 WHERE `id` = $userid");
                    $dsql->dsqlOper($sql, "update");
                }

                $showModule = checkShowModule(array(), "manage");
                $d = array_keys($showModule);
                $bind_module = join(",", $d);

                $bind_module_f = ", `bind_module` = '$bind_module'";
            }
            if($orderDetail['ordertype'] == "upgrade"){
                $type_f = ", `type` = ".$buy_type;
            }


            $sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `expired` = $expired $state_f $type_f $bind_module_f WHERE `id` = $business");
            $dsql->dsqlOper($sql, "update");

            $sql = $dsql->SetQuery("UPDATE `#@__business_order` SET `paytype` = '$paytype', `paydate` = '$time', `balance` = '$balance', `amount` = '".($totalPrice - $balance)."', `state` = 1 WHERE `ordernum` = '$ordernum'");
            $ret = $dsql->dsqlOper($sql, "update");

        }

        //扣除会员余额
        if($balance > 0){
            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$balance' WHERE `id` = '$userid'");
            $dsql->dsqlOper($archives, "update");
        }

        //保存操作日志
        // 19-661 :续费
        $info = $langData['siteConfig'][19][789] . "，" . $title;
        $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$totalPrice', '$info', '$time')");
        $dsql->dsqlOper($archives, "update");

        // 新入驻商家通知后台，返积分
        if($busiDetail['state'] == 4){

            // 查看是否存在历史入驻订单
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_order` WHERE `bid` = $business AND `state` = 1");
            $count = $dsql->dsqlOper($sql, "totalCount");
            if($count == 1){

                if($buy_type == 1){
                    $priceCost = $busiConfig['trialCost'];
                }elseif($buy_type == 2){
                    $priceCost = $busiConfig['enterpriseCost'];
                }
                $point = 0;
                foreach ($priceCost as $key => $value) {
                    if($value['time'] == $buy_time && $value['type'] == $buy_time_type && $value['price'] == $totalPrice){
                        $point = $value['point'];
                    }
                }
                if($point > 0){
                    $sql = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + $point 	WHERE `id` = $userid");
                    $ret = $dsql->dsqlOper($sql, "update");
                    if($ret == "ok"){
                        $note = "入驻商家返".$point.$cfg_pointName;
                        $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$point', '$note', '$time')");
                        $aid = $dsql->dsqlOper($archives, "lastid");
                    }
                }
            }

            updateAdminNotice("business", "join");
        }

    }


    /**
     * 缴纳保障金
     */
    public function promotion(){
        global $dsql;
        global $userLogin;
        global $cfg_secureAccess;
        global $cfg_basehost;
        global $langData;

        $amount = (float)$this->param['amount'];
        $balance = (int)$this->param['balance'];
        $paytype = $this->param['paytype'];
        $qr = (int)$this->param['qr'];

        $param = array(
            "service" => "member",
            "template" => "promotion"
        );
        $url = getUrlPath($param);

        $userid = $userLogin->getMemberID();
        if($userid == -1){
            header("location:".$cfg_secureAccess.$cfg_basehost."/login.html?furl=" . urlencode($url));
        }


        if(empty($amount)){
            header("location:".$url);
        }

        //最少缴纳1000
        if($amount < 1000){
            header("location:".$url);
        }

        //如果使用了余额，计算还需要支付多少费用
        $payprice = $amount;
        $useBalance = 0;
        if($balance){
            $userinfo = $userLogin->getMemberInfo();
            $money = $userinfo['money'];

            if($money < $amount){
                $useBalance = $money;
                $payprice = $amount - $money;
            }else{
                $useBalance = $amount;
                $payprice = 0;
            }
        }

        $time = time();
        $ordernum = create_ordernum();

        $param = array(
            "type" => "promotion",
            "userid" => $userid,
            "totalPrice" => $amount,
            "ordernum" => $ordernum,
            "balance" => $useBalance,
            "amount" => $payprice,
            "price" => $payprice,
            "qr" => $qr
        );

        //如果还需要在线支付的费用小于等于0，则直接扣除余额，更新会员类型及商家状态
        if($payprice <= 0 && !$qr){

            $this->promotionSuccess($param);

            //跳转至等待审核页面
            header("location:".$url);

        }

        //跳转至第三方支付页面
        return createPayForm("member", $ordernum, $payprice, $paytype, $langData['siteConfig'][21][107], $param);   //商家缴纳保障金

    }


    /**
     * 保障金缴纳成功
     */
    public function promotionSuccess($param){
        global $dsql;
        global $langData;
        $userid = $param['userid'];
        $ordernum = $param['ordernum'];
        $totalPrice = $param['totalPrice'];
        $balance = $param['balance'];

        $time = time();

        //扣除会员余额
        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$balance', `promotion` = `promotion` + '$totalPrice' WHERE `id` = '$userid'");
        $dsql->dsqlOper($archives, "update");

        //保存操作日志
        $info = $langData['siteConfig'][21][107].echoCurrency(array("type" => "symbol")).$totalPrice;  //商家缴纳保障金
        if($balance){
            $info .= "(".$langData['siteConfig'][21][108].echoCurrency(array("type" => "symbol")).$balance.")";  //其中余额支付
        }
        $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$balance', '$info', '$time')");
        $dsql->dsqlOper($archives, "update");

        //订单记录
        $sql = $dsql->SetQuery("INSERT INTO `#@__member_promotion` (`uid`, `type`, `amount`, `ordernum`, `date`) VALUES ('$userid', '1', '$totalPrice', '$ordernum', '$time')");
        $dsql->dsqlOper($sql, "update");
    }


    /**
     * 商家入驻订单
     * @return array
     */
    public function joinOrder(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $page = $pageSize = 0;

        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives = $dsql->SetQuery("SELECT o.* FROM `#@__business_order` o LEFT JOIN `#@__business_list` l ON o.`bid` = l.`id` WHERE l.`uid` = $userid ORDER BY `id` DESC");

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

        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives.$where, "results");

        if($results){
            foreach($results as $key => $val){
                $list[$key]['id']         = $val['id'];
                $list[$key]['ordernum']   = $val['ordernum'];
                $list[$key]['totalprice'] = $val['totalprice'];
                $list[$key]['offer']      = $val['offer'];
                $list[$key]['balance']    = $val['balance'];

                $paytype = $val['paytype'];
                $sql = $dsql->SetQuery("SELECT `pay_name` FROM `#@__site_payment` WHERE `pay_code` = '$paytype'");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $paytype = $ret[0]['pay_name'];
                }

                $list[$key]['paytype']    = $paytype;
                $list[$key]['amount']     = $val['amount'];
                $list[$key]['date']       = date("Y-m-d H:i:s", $val['date']);

                //查询开通的模块
                $modules = array();
                $sql = $dsql->SetQuery("SELECT `module`, `unitprice`, `count`, `expired` FROM `#@__business_order_module` WHERE `oid` = " . $val['id']);
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    foreach ($ret as $k => $v) {
                        array_push($modules, array(
                            "name" => getModuleTitle(array("name" => $v["module"])),
                            "unitprice" => $v["unitprice"],
                            "count" => $v["count"],
                            "expired" => date("Y-m-d H:i:s", $v["expired"])
                        ));
                    }
                }
                $list[$key]["modules"] = $modules;

            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);

    }


    /**
     * 保障金订单
     * @return array
     */
    public function promotionOrder(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $type = $page = $pageSize = 0;

        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $type     = $this->param['type'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $where = "";
        if($type !== ""){
            $where = " AND `type` = $type";
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_promotion` WHERE `uid` = $userid".$where." ORDER BY `id` DESC");

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

        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives.$where, "results");

        if($results){
            foreach($results as $key => $val){
                $list[$key]['id']       = $val['id'];
                $list[$key]['type']     = $val['type'];
                $list[$key]['amount']   = $val['amount'];
                $list[$key]['ordernum'] = $val['ordernum'];
                $list[$key]['title']    = $val['title'];
                $list[$key]['note']     = $val['note'];
                $list[$key]['date']     = date("Y-m-d H:i:s", $val['date']);
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);

    }


    /**
     * 提取保障金
     */
    public function extract(){
        global $dsql;
        global $userLogin;
        global $langData;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $title = $this->param['title'];
                $note  = $this->param['note'];
            }
        }

        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        //查询可提取的保障金 = 一年前的缴纳总额 - 已提取总额
        $totalPromotion = $alreadyExtract = 0;
        $uid = $userLogin->getMemberID();
        $yearAgo = GetMkTime(date("Y-m-d H:i:s", strtotime("-1 year")));
        $sql = $dsql->SetQuery("SELECT SUM(`amount`) as total FROM `#@__member_promotion` WHERE `type` = 1 AND `uid` = $uid AND `date` < $yearAgo");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $totalPromotion = $ret[0]['total'];
        }
        $sql = $dsql->SetQuery("SELECT SUM(`amount`) as total FROM `#@__member_promotion` WHERE `type` = 0 AND `uid` = $uid");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $alreadyExtract = $ret[0]['total'];
        }
        $extract = sprintf('%.2f', ($totalPromotion - $alreadyExtract));

        if($extract <= 0){
            return array("state" => 200, "info" => $langData['siteConfig'][21][109]);  //可提取保障金为0，提取失败！
        }

        $time = time();
        $ordernum = create_ordernum();

        //增加会员余额
        $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$extract', `promotion` = `promotion` - '$extract' WHERE `id` = '$userid'");
        $dsql->dsqlOper($archives, "update");

        //保存操作日志
        $info = $langData['siteConfig'][21][110];  //提取保障金
        $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$extract', '$info', '$time')");
        $dsql->dsqlOper($archives, "update");

        //订单记录
        $sql = $dsql->SetQuery("INSERT INTO `#@__member_promotion` (`uid`, `type`, `amount`, `ordernum`, `date`, `title`, `note`) VALUES ('$userid', '0', '$extract', '$ordernum', '$time', '$title', '$note')");
        $dsql->dsqlOper($sql, "update");

        return $langData['siteConfig'][21][111];


    }



    /**
     * 查询订单（合并查询）
     */
    public function orderList(){
        global $dsql;
        global $userLogin;
        global $installModuleArr;
        global $langData;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $state    = $this->param['state'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $totalCount = $totalPage = $unused = $unpaid = $recei = $used = 0;
        $union = array();

        if(in_array("tuan", $installModuleArr)){

            //计算团购订单数量
            $sql = $dsql->SetQuery("SELECT count(`id`) as total FROM `#@__tuan_order` WHERE `userid` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            $totalCount += $ret[0]['total'];

            //待发货订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 1", "results");
            $unused += $ret[0]['total'];

            //未付款订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 0", "results");
            $unpaid += $ret[0]['total'];

            //待收货订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 6", "results");
            $recei += $ret[0]['total'];

            //完成订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 3", "results");
            $used += $ret[0]['total'];

            //筛选状态
            $where = "";
            if($state != ""){
                $where = " AND `orderstate` = $state";
            }

            $sql = "SELECT * FROM (SELECT `id`, `ordernum`, `tab` FROM `#@__tuan_order` WHERE `userid` = $userid".$where." ORDER BY `id` ASC) as tuan";
            array_push($union, $sql);
        }

        if(in_array("shop", $installModuleArr)){

            //计算商城订单数量
            $sql = $dsql->SetQuery("SELECT count(`id`) as total FROM `#@__shop_order` WHERE `userid` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            $totalCount += $ret[0]['total'];

            //待发货订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 1", "results");
            $unused += $ret[0]['total'];

            //未付款订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 0", "results");
            $unpaid += $ret[0]['total'];

            //待收货订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 6", "results");
            $recei += $ret[0]['total'];

            //完成订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 3", "results");
            $used += $ret[0]['total'];

            //筛选状态
            $where = "";
            if($state != ""){
                $where = " AND `orderstate` = $state";
            }

            $sql = "SELECT * FROM (SELECT `id`, `ordernum`, `tab` FROM `#@__shop_order` WHERE `userid` = $userid".$where." ORDER BY `id` ASC) as shop";
            array_push($union, $sql);
        }

        // if(in_array("waimai", $installModuleArr)){
        //
        // 	//计算外卖订单数量
        // 	$sql = $dsql->SetQuery("SELECT count(`id`) as total FROM `#@__waimai_order` WHERE `uid` = $userid");
        // 	$ret = $dsql->dsqlOper($sql, "results");
        // 	$totalCount += $ret[0]['total'];
        //
        // 	//待发货订单数量
        // 	$ret = $dsql->dsqlOper($sql . " AND `state` = 2", "results");
        // 	$unused += $ret[0]['total'];
        //
        // 	//未付款订单数量
        // 	$ret = $dsql->dsqlOper($sql . " AND `state` = 0", "results");
        // 	$unpaid += $ret[0]['total'];
        //
        // 	//待收货订单数量
        // 	$ret = $dsql->dsqlOper($sql . " AND `state` = 5", "results");
        // 	$recei += $ret[0]['total'];
        //
        // 	//完成订单数量
        // 	$ret = $dsql->dsqlOper($sql . " AND `state` = 1", "results");
        // 	$used += $ret[0]['total'];
        //
        // 	//筛选状态
        // 	$where = "";
        // 	if($state != ""){
        // 		$state = $state == 6 ? 5 : $state;
        // 		$where = " AND `state` = $state";
        // 	}
        //
        // 	$sql = "SELECT * FROM (SELECT `id`, `ordernum`, `tab` FROM `#@__waimai_order` WHERE `userid` = $userid".$where." ORDER BY `id` ASC) as waimai";
        // 	array_push($union, $sql);
        // }

        if(in_array("info", $installModuleArr)){

            //计算商城订单数量
            $sql = $dsql->SetQuery("SELECT count(`id`) as total FROM `#@__info_order` WHERE `userid` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            $totalCount += $ret[0]['total'];

            //待发货订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 1", "results");
            $unused += $ret[0]['total'];

            //未付款订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 0", "results");
            $unpaid += $ret[0]['total'];

            //待收货订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 3", "results");
            $recei += $ret[0]['total'];

            //完成订单数量
            $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 4", "results");
            $used += $ret[0]['total'];

            //筛选状态
            $where = "";
            if($state != ""){
                $where = " AND `orderstate` = $state";
            }

            $sql = "SELECT * FROM (SELECT `id`, `ordernum`, `tab` FROM `#@__info_order` WHERE `userid` = $userid".$where." ORDER BY `id` ASC) as info";
            array_push($union, $sql);
        }

        if(empty($union)){
            return array("state" => 200, "info" => $langData['siteConfig'][21][112]);  //暂无需要查询的模块！
        }

        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        if($totalCount == 0) return array("state" => 100, "pageInfo" => $pageinfo, "list" => array());

        $pageinfo['unpaid'] = $unpaid;
        $pageinfo['unused'] = $unused;
        $pageinfo['recei']  = $recei;
        $pageinfo['used']   = $used;

        $atpage = $pageSize*($page-1);

        $i = 0;
        $list = array();
        $archives = $dsql->SetQuery(join(" UNION ALL ", $union) . " ORDER BY `id` DESC LIMIT $atpage, $pageSize");
        $results = $dsql->dsqlOper($archives, "results");
        if($results && is_array($results)){
            foreach ($results as $key => $value) {
                $id = $value['id'];
                $ordernum = $value['ordernum'];
                $tab = $value['tab'];

                //团购订单
                if($tab == "tuan"){

                    $param = array(
                        "service"     => "tuan",
                        "template"    => "pay",
                        "param"       => "ordernum=%id%"
                    );
                    $payurlParam = getUrlPath($param);

                    $param = array(
                        "service"  => "member",
                        "type"     => "user",
                        "template" => "orderdetail",
                        "module"   => "tuan",
                        "id"       => "%id%",
                        "param"    => "rates=1"
                    );
                    $commonUrlParam = getUrlPath($param);

                    $sql = $dsql->SetQuery("SELECT o.`proid`, o.`procount`, o.`orderprice`, o.`propolic`, o.`orderstate`, o.`orderdate`, o.`paydate`, o.`ret-state`, o.`exp-date`, m.`company` " .
                        "FROM `#@__tuan_order` o LEFT JOIN `#@__tuanlist` l ON o.`proid` = l.`id` LEFT JOIN `#@__tuan_store` s ON l.`sid` = s.`id` LEFT JOIN `#@__member` m ON m.`id` = s.`uid` WHERE o.`id` = $id");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret && is_array($ret)){

                        $val = $ret[0];
                        $list[$i]['tab']         = $tab;
                        $list[$i]['id']          = $id;
                        $list[$i]['ordernum']    = $ordernum;
                        $list[$i]['proid']       = $val['proid'];
                        $list[$i]['procount']    = $val['procount'];
                        $list[$i]['company']     = $val['company'];

                        //计算订单价格
                        $totalPrice = $val['orderprice'] * $val['procount'];
                        $propolic   = $val['propolic'];
                        $policy     = unserialize($propolic);
                        if(!empty($propolic) && !empty($policy)){
                            $freight  = $policy['freight'];
                            $freeshi  = $policy['freeshi'];

                            if($val['procount'] <= $freeshi){
                                $totalPrice += $freight;
                            }
                        }

                        $list[$i]['orderprice']  = $totalPrice;
                        $list[$i]["orderstate"]  = $val['orderstate'];
                        $list[$i]['orderdate']   = $val['orderdate'];
                        $list[$i]['paydate']     = $val['paydate'];
                        $list[$i]['retState']    = $val['ret-state'];
                        $list[$i]['expDate']     = $val['exp-date'];


                        $detailHandels = new handlers("tuan", "detail");
                        $detail  = $detailHandels->getHandle($val['proid']);
                        if($detail && $detail['state'] == 100){
                            $data = $detail['info'];
                            $list[$i]['product']['title'] = $data['title'];
                            $list[$i]['product']['enddate'] = $data['enddate'];
                            $list[$i]['product']['litpic'] = $data['litpic'];
                        }else{
                            $list[$i]['product']['title'] = $langData['siteConfig'][13][20];  //无
                            $list[$i]['product']['enddate'] = 0;
                            $list[$i]['product']['litpic'] = "";
                        }

                        $param = array(
                            "service"     => "tuan",
                            "template"    => "detail",
                            "id"          => $val['proid']
                        );
                        $list[$i]['product']['url'] = getUrlPath($param);

                        //未付款的提供付款链接
                        if($val['orderstate'] == 0){
                            $RenrenCrypt = new RenrenCrypt();
                            $encodeid = base64_encode($RenrenCrypt->php_encrypt($ordernum));
                            $list[$i]["payurl"] = str_replace("%id%", $encodeid, $payurlParam);
                        }

                        //评价
                        if($val['orderstate'] == 3){
                            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__tuancommon` WHERE `aid` = ".$id);
                            $common = $dsql->dsqlOper($archives, "totalCount");
                            $iscommon = $common > 0 ? 1 : 0;
                            $list[$i]['common'] = $iscommon;
                            $list[$i]['commonUrl'] = str_replace("%id%", $id, $commonUrlParam);
                        }

                        $i++;

                    }
                }


                //商城
                if($tab == "shop"){
                    $sql = $dsql->SetQuery("SELECT `store`, `userid`, `orderstate`, `orderdate`, `paytype`, `logistic`, `ret-state`, `exp-date`, `common` FROM `#@__shop_order` WHERE `id` = $id");
                    $ret_ = $dsql->dsqlOper($sql, "results");
                    if($ret_ && is_array($ret_)){

                        $val = $ret_[0];

                        $sql = $dsql->SetQuery("SELECT * FROM `#@__shop_order_product` WHERE `orderid` = ".$id);
                        $ret = $dsql->dsqlOper($sql, "results");

                        if($ret && is_array($ret)){

                            $param = array(
                                "service"     => "shop",
                                "template"    => "detail",
                                "id"          => "%id%"
                            );
                            $urlParam = getUrlPath($param);

                            $param = array(
                                "service"     => "shop",
                                "template"    => "pay",
                                "param"       => "ordernum=%id%"
                            );
                            $payurlParam = getUrlPath($param);

                            $param = array(
                                "service"  => "member",
                                "type"     => "user",
                                "template" => "orderdetail",
                                "module"   => "shop",
                                "id"       => "%id%",
                                "param"    => "rates=1"
                            );
                            $commonUrlParam = getUrlPath($param);

                            $list[$i]['tab']         = $tab;
                            $list[$i]['id']          = $id;
                            $list[$i]['ordernum']    = $ordernum;

                            //商家信息
                            $detailHandels = new handlers("shop", "storeDetail");
                            $detail  = $detailHandels->getHandle($val['store']);
                            if($detail && $detail['state'] == 100){
                                $data = $detail['info'];
                                $list[$i]['store'] = array(
                                    "id"     => $data['id'],
                                    "title"  => $data['title'],
                                    "domain" => $data['domain'],
                                    "qq"     => $data['qq']
                                );
                            }else{
                                $list[$i]['store'] = array(
                                    "id"     => 0,
                                    "title"  => $langData['siteConfig'][19][709]   //官方直营
                                );
                            }


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
                                    $payname = $cfg_pointName."+" . $langData['siteConfig'][19][363];  //余额
                                }elseif($val["paytype"] == "point"){
                                    $payname = $cfg_pointName;
                                }elseif($val["paytype"] == "money"){
                                    $payname = $langData['siteConfig'][19][363];  //余额
                                }
                                $list[$i]["paytype"]   = $payname;
                            }

                            //未付款的提供付款链接
                            if($val['orderstate'] == 0){
                                $RenrenCrypt = new RenrenCrypt();
                                $encodeid = base64_encode($RenrenCrypt->php_encrypt($ordernum));
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

                                $detailHandels = new handlers("shop", "detail");
                                $detail  = $detailHandels->getHandle($v['proid']);
                                if($detail && $detail['state'] == 100){
                                    $data = $detail['info'];

                                    $list[$i]['product'][$k]['title'] = $data['title'];
                                    $list[$i]['product'][$k]['litpic'] = $data['litpic'];
                                    $list[$i]['product'][$k]['url'] = str_replace("%id%", $v['proid'], $urlParam);

                                    $list[$i]['product'][$k]['price'] = $v['price'];
                                    $list[$i]['product'][$k]['count'] = $v['count'];
                                    $list[$i]['product'][$k]['specation'] = $v['specation'];
                                }

                                //未付款的不计算积分和余额部分
                                if($val['orderstate'] == 0){
                                    $totalPayPrice += $v['price'] * $v['count'] + $v['discount'];
                                }else{
                                    $totalPayPrice += $v['price'] * $v['count'] + $v['discount'];
                                }
                            }

                            $totalPayPrice += $val['logistic'];
                            $list[$i]['totalPayPrice'] = sprintf("%.2f", $totalPayPrice);

                            $i++;

                        }
                    }
                }


                //二手
                if($tab == "info"){
                    $sql = $dsql->SetQuery("SELECT `store`, `prod`, `userid`, `orderstate`, `orderdate`, `paytype`, `ret-state`, `exp-date`, `common` FROM `#@__info_order` WHERE `id` = $id");
                    $ret_ = $dsql->dsqlOper($sql, "results");
                    if($ret_ && is_array($ret_)){

                        $val = $ret_[0];

                        $sql = $dsql->SetQuery("SELECT * FROM `#@__infolist` WHERE `id` = ".$val['prod']);
                        $ret = $dsql->dsqlOper($sql, "results");

                        if($ret && is_array($ret)){

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

                            $list[$i]['tab']         = $tab;
                            $list[$i]['id']          = $id;
                            $list[$i]['ordernum']    = $ordernum;

                            //商家信息
                            $detailHandels = new handlers("info", "storeDetail");
                            $detail  = $detailHandels->getHandle($val['store']);
                            if($detail && $detail['state'] == 100){
                                $data = $detail['info'];
                                $list[$i]['store'] = array(
                                    "id"     => $data['id'],
                                    "title"  => $data['member']['nickname'],
                                    "domain" => $data['domain'],
                                    "qq"     => $data['member']['qq']
                                );
                            }else{
                                $list[$i]['store'] = array(
                                    "id"     => 0,
                                    "title"  => $langData['siteConfig'][19][709]   //官方直营
                                );
                            }


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
                                    $payname = $cfg_pointName."+" . $langData['siteConfig'][19][363];  //余额
                                }elseif($val["paytype"] == "point"){
                                    $payname = $cfg_pointName;
                                }elseif($val["paytype"] == "money"){
                                    $payname = $langData['siteConfig'][19][363];  //余额
                                }
                                $list[$i]["paytype"]   = $payname;
                            }

                            //未付款的提供付款链接
                            if($val['orderstate'] == 0){
                                $RenrenCrypt = new RenrenCrypt();
                                $encodeid = base64_encode($RenrenCrypt->php_encrypt($ordernum));
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

                                $detailHandels = new handlers("info", "detail");
                                $detail  = $detailHandels->getHandle($v['id']);
                                if($detail && $detail['state'] == 100){
                                    $data = $detail['info'];

                                    $list[$i]['product'][$k]['title'] = $data['title'];
                                    $list[$i]['product'][$k]['litpic'] = $data['imglist'][0]['path'];
                                    $list[$i]['product'][$k]['url'] = str_replace("%id%", $v['id'], $urlParam);

                                    $list[$i]['product'][$k]['price'] = $v['price'];
                                    $list[$i]['product'][$k]['yunfei'] = $v['yunfei'];
                                }

                                //未付款的不计算积分和余额部分
                                if($val['orderstate'] == 0){
                                    $totalPayPrice += $v['price']  + $v['yunfei'] ;
                                }else{
                                    $totalPayPrice += $v['price']  + $v['yunfei'];
                                }
                            }
                            $list[$i]['totalPayPrice'] = sprintf("%.2f", $totalPayPrice);

                            $i++;

                        }
                    }
                }

                //外卖
                // if($tab == "waimai"){
                //
                // 	$sql = $dsql->SetQuery("SELECT `id`, `ordernum`, `userid`, `store`, `price`, `paytype`, `peisong`, `offer`, `orderdate`, `note`, `state` FROM `#@__waimai_order` WHERE `id` = $id");
                // 	$ret = $dsql->dsqlOper($sql, "results");
                // 	if($ret && is_array($ret)){
                // 		$data = $ret[0];
                //
                // 		$list[$i]['id'] = $data['id'];
                // 		$list[$i]['ordernum']  = $data['ordernum'];
                // 		$list[$i]['userid']    = $data['userid'];
                // 		$list[$i]['store']     = $data['store'];
                // 		$list[$i]['price']     = $data['price'];
                // 		$list[$i]['paytype']   = $data['paytype'];
                // 		$list[$i]['peisong']   = $data['peisong'];
                // 		$list[$i]['offer']     = $data['offer'];
                // 		$list[$i]['orderdate'] = $data['orderdate'];
                // 		$list[$i]['note']      = $data['note'];
                // 		$list[$i]['state']     = $data['state'];
                //
                // 		$param = array(
                // 			"service" => "waimai",
                // 			"templates" => "confirm",
                // 			"param" => "ordernum=".$data['ordernum']
                // 		);
                // 		$payurl = getUrlPath($param);
                // 		$list[$i]['payurl']    = $payurl;
                //
                // 		//用户名
                // 		$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $data["userid"]);
                // 		$username = $dsql->dsqlOper($userSql, "results");
                // 		$list[$i]["username"] = $username[0]['username'];
                //
                // 		//餐厅
                // 		$storeSql = $dsql->SetQuery("SELECT `title` FROM `#@__waimai_store` WHERE `id` = ". $data['store']);
                // 		$storename = $dsql->getTypeName($storeSql);
                // 		$list[$i]["storename"] = $storename[0]["title"];
                //
                // 		$paytype = $data["paytype"];
                // 		if(!$paytype){
                // 			$list[$i]["paytype"] = "未知";
                // 		}else{
                // 			//主表信息
                // 			$sql = $dsql->SetQuery("SELECT `pay_name` FROM `#@__site_payment` WHERE `pay_code` = '$paytype'");
                // 			$ret = $dsql->dsqlOper($sql, "results");
                // 			if(!empty($ret)){
                // 				$list[$i]["paytype"] = $ret[0]['pay_name'];
                // 			}else{
                // 				$list[$i]["paytype"] = $data["paytype"];
                // 			}
                // 		}
                //
                // 		//订单内容
                // 		$menuList = array();
                // 		$sql = $dsql->SetQuery("SELECT `pid`, `pname`, `price`, `count` FROM `#@__waimai_order_product` WHERE `orderid` = ".$data['id']);
                // 		$ret = $dsql->dsqlOper($sql, "results");
                // 		if($ret){
                // 			foreach ($ret as $k => $v) {
                // 				$menuList[$k]['pid'] = $v['pid'];
                // 				$menuList[$k]['pname'] = $v['pname'];
                // 				$menuList[$k]['price'] = $v['price'];
                // 				$menuList[$k]['count'] = $v['count'];
                // 			}
                // 		}
                // 		$list[$i]["menus"] = $menuList;
                //
                // 		$i++;
                // 	}
                //
                // }


            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);

    }



    /**
     * 商家查询订单（合并查询）
     */
    public function storeOrderList(){
        global $dsql;
        global $userLogin;
        global $installModuleArr;
        global $langData;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $state    = $this->param['state'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $totalCount = $totalPage = $unused = $refund = $recei = $used = 0;
        $union = array();

        if(in_array("tuan", $installModuleArr)){

            //计算团购订单数量
            $sql = $dsql->SetQuery("SELECT count(o.`id`) as total FROM " .
                "`#@__tuan_order` o LEFT JOIN `#@__tuanlist` l ON o.`proid` = l.`id` LEFT JOIN `#@__tuan_store` s ON l.`sid` = s.`id` LEFT JOIN `#@__member` m ON m.`id` = s.`uid` " .
                "WHERE l.`tuantype` = 2 AND (o.`orderstate` = 1 OR o.`orderstate` = 3 OR o.`orderstate` = 4 OR o.`orderstate` = 6 OR o.`orderstate` = 7) AND s.`uid` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            $totalCount += $ret[0]['total'];

            //待发货订单数量
            $ret = $dsql->dsqlOper($sql . " AND o.`orderstate` = 1", "results");
            $unused += $ret[0]['total'];

            //待收货订单数量
            $ret = $dsql->dsqlOper($sql . " AND o.`orderstate` = 6 AND o.`exp-date` != 0", "results");
            $recei += $ret[0]['total'];

            //退款订单数量
            $ret = $dsql->dsqlOper($sql . " AND o.`orderstate` = 4", "results");
            $refund += $ret[0]['total'];

            //完成订单数量
            $ret = $dsql->dsqlOper($sql . " AND o.`orderstate` = 3", "results");
            $used += $ret[0]['total'];

            //筛选状态
            $where = "";
            if($state != ""){
                if($state == 6){
                    $where = " AND o.`orderstate` = 6 AND o.`exp-date` != 0";
                }else{
                    $where = " AND o.`orderstate` = $state";
                }
            }

            $sql = "SELECT * FROM (SELECT o.`id`, o.`ordernum`, o.`tab` FROM `#@__tuan_order` o LEFT JOIN `#@__tuanlist` l ON o.`proid` = l.`id` LEFT JOIN `#@__tuan_store` s ON l.`sid` = s.`id` LEFT JOIN `#@__member` m ON m.`id` = s.`uid` " .
                "WHERE l.`tuantype` = 2 AND (o.`orderstate` = 1 OR o.`orderstate` = 3 OR o.`orderstate` = 4 OR o.`orderstate` = 6 OR o.`orderstate` = 7) AND s.`uid` = $userid".$where." ORDER BY `id` ASC) as tuan";
            array_push($union, $sql);
        }

        if(in_array("shop", $installModuleArr)){

            //先查询商城商家ID
            $userSql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
            $userResult = $dsql->dsqlOper($userSql, "results");
            if($userResult && is_array($userResult)){
                $sid = $userResult[0]['id'];

                //计算商城订单数量
                $sql = $dsql->SetQuery("SELECT count(`id`) as total FROM `#@__shop_order` WHERE (`orderstate` = 1 OR `orderstate` = 3 OR `orderstate` = 4 OR `orderstate` = 6 OR `orderstate` = 7) AND `store` = $sid");
                $ret = $dsql->dsqlOper($sql, "results");
                $totalCount += $ret[0]['total'];

                //待发货订单数量
                $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 1", "results");
                $unused += $ret[0]['total'];

                //待收货订单数量
                $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 6 AND `exp-date` != 0", "results");
                $recei += $ret[0]['total'];

                //退款订单数量
                $ret = $dsql->dsqlOper($sql . " AND `orderstate` != 3 AND `ret-state` = 1", "results");
                $refund += $ret[0]['total'];

                //完成订单数量
                $ret = $dsql->dsqlOper($sql . " AND `orderstate` = 3", "results");
                $used += $ret[0]['total'];

                //筛选状态
                $where = "";
                if($state != ""){
                    if($state == 4){
                        $where = " AND `orderstate` != 3 AND `ret-state` = 1";
                    }elseif($state == 6){
                        $where = " AND `orderstate` = $state AND `exp-date` != 0";
                    }else{
                        $where = " AND `orderstate` = $state";
                    }
                }

                $sql = "SELECT * FROM (SELECT `id`, `ordernum`, `tab` FROM `#@__shop_order` WHERE (`orderstate` = 1 OR `orderstate` = 3 OR `orderstate` = 4 OR `orderstate` = 6 OR `orderstate` = 7) AND `store` = $sid".$where." ORDER BY `id` ASC) as shop";
                array_push($union, $sql);
            }
        }

        // if(in_array("waimai", $installModuleArr)){
        //
        // 	$sql = $dsql->SetQuery("SELECT `id` FROM `#@__waimai_store` WHERE `userid` = $userid");
        // 	$ret = $dsql->dsqlOper($sql, "results");
        // 	if($ret && is_array($ret)){
        // 		$sid = $ret[0]['id'];
        //
        // 		//计算外卖订单数量
        // 		$sql = $dsql->SetQuery("SELECT count(`id`) as total FROM `#@__waimai_order` WHERE (`state` = 1 OR `state` = 2 OR `state` = 3) AND `store` = $sid");
        // 		$ret = $dsql->dsqlOper($sql, "results");
        // 		$totalCount += $ret[0]['total'];
        //
        // 		//待发货订单数量
        // 		$ret = $dsql->dsqlOper($sql . " AND `state` = 1", "results");
        // 		$unused += $ret[0]['total'];
        //
        // 		//待收货订单数量
        // 		$ret = $dsql->dsqlOper($sql . " AND `state` = 2", "results");
        // 		$recei += $ret[0]['total'];
        //
        // 		//完成订单数量
        // 		$ret = $dsql->dsqlOper($sql . " AND `state` = 3", "results");
        // 		$used += $ret[0]['total'];
        //
        // 		//筛选状态
        // 		$where = "";
        // 		if($state != ""){
        // 			if($state == 4){
        // 				$where = " AND 1 = 2";
        // 			}else{
        // 				$state = $state == 6 ? 2 : $state;
        // 				$where = " AND `state` = $state";
        // 			}
        // 		}
        //
        // 		$sql = "SELECT * FROM (SELECT `id`, `ordernum`, `tab` FROM `#@__waimai_order` WHERE (`state` = 1 OR `state` = 2 OR `state` = 3) AND `store` = $sid".$where." ORDER BY `id` ASC) as waimai";
        // 		array_push($union, $sql);
        // 	}
        // }

        if(empty($union)){
            return array("state" => 200, "info" => $langData['siteConfig'][21][112]);  //暂无需要查询的模块！
        }

        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        if($totalCount == 0) return array("state" => 100, "pageInfo" => $pageinfo, "list" => array());

        $pageinfo['unused'] = $unused;
        $pageinfo['recei']  = $recei;
        $pageinfo['refund'] = $refund;
        $pageinfo['used']   = $used;

        $atpage = $pageSize*($page-1);

        $i = 0;
        $list = array();
        $archives = $dsql->SetQuery(join(" UNION ALL ", $union) . " ORDER BY `id` DESC LIMIT $atpage, $pageSize");
        $results = $dsql->dsqlOper($archives, "results");
        if($results && is_array($results)){
            foreach ($results as $key => $value) {
                $id = $value['id'];
                $ordernum = $value['ordernum'];
                $tab = $value['tab'];

                //团购订单
                if($tab == "tuan"){

                    $param = array(
                        "service"     => "tuan",
                        "template"    => "pay",
                        "param"       => "ordernum=%id%"
                    );
                    $payurlParam = getUrlPath($param);

                    $param = array(
                        "service"  => "member",
                        "type"     => "user",
                        "template" => "orderdetail",
                        "module"   => "tuan",
                        "id"       => "%id%",
                        "param"    => "rates=1"
                    );
                    $commonUrlParam = getUrlPath($param);

                    $sql = $dsql->SetQuery("SELECT o.`proid`, o.`procount`, o.`orderprice`, o.`propolic`, o.`orderstate`, o.`orderdate`, o.`paydate`, o.`ret-state`, o.`exp-date`, m.`company` " .
                        "FROM `#@__tuan_order` o LEFT JOIN `#@__tuanlist` l ON o.`proid` = l.`id` LEFT JOIN `#@__tuan_store` s ON l.`sid` = s.`id` LEFT JOIN `#@__member` m ON m.`id` = s.`uid` WHERE o.`id` = $id");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret && is_array($ret)){

                        $val = $ret[0];
                        $list[$i]['tab']         = $tab;
                        $list[$i]['id']          = $id;
                        $list[$i]['ordernum']    = $ordernum;
                        $list[$i]['proid']       = $val['proid'];
                        $list[$i]['procount']    = $val['procount'];
                        $list[$i]['company']     = $val['company'];

                        //计算订单价格
                        $totalPrice = $val['orderprice'] * $val['procount'];
                        $propolic   = $val['propolic'];
                        $policy     = unserialize($propolic);
                        if(!empty($propolic) && !empty($policy)){
                            $freight  = $policy['freight'];
                            $freeshi  = $policy['freeshi'];

                            if($val['procount'] <= $freeshi){
                                $totalPrice += $freight;
                            }
                        }

                        $list[$i]['orderprice']  = $totalPrice;
                        $list[$i]["orderstate"]  = $val['orderstate'];
                        $list[$i]['orderdate']   = $val['orderdate'];
                        $list[$i]['paydate']     = $val['paydate'];
                        $list[$i]['retState']    = $val['ret-state'];
                        $list[$i]['expDate']     = $val['exp-date'];


                        $detailHandels = new handlers("tuan", "detail");
                        $detail  = $detailHandels->getHandle($val['proid']);
                        if($detail && $detail['state'] == 100){
                            $data = $detail['info'];
                            $list[$i]['product']['title'] = $data['title'];
                            $list[$i]['product']['enddate'] = $data['enddate'];
                            $list[$i]['product']['litpic'] = $data['litpic'];
                        }else{
                            $list[$i]['product']['title'] = $langData['siteConfig'][13][20];  //无
                            $list[$i]['product']['enddate'] = 0;
                            $list[$i]['product']['litpic'] = "";
                        }

                        $param = array(
                            "service"     => "tuan",
                            "template"    => "detail",
                            "id"          => $val['proid']
                        );
                        $list[$i]['product']['url'] = getUrlPath($param);

                        //未付款的提供付款链接
                        if($val['orderstate'] == 0){
                            $RenrenCrypt = new RenrenCrypt();
                            $encodeid = base64_encode($RenrenCrypt->php_encrypt($ordernum));
                            $list[$i]["payurl"] = str_replace("%id%", $encodeid, $payurlParam);
                        }

                        //评价
                        if($val['orderstate'] == 3){
                            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__tuancommon` WHERE `aid` = ".$id);
                            $common = $dsql->dsqlOper($archives, "totalCount");
                            $iscommon = $common > 0 ? 1 : 0;
                            $list[$i]['common'] = $iscommon;
                            $list[$i]['commonUrl'] = str_replace("%id%", $id, $commonUrlParam);
                        }

                        $i++;

                    }
                }


                //商城
                if($tab == "shop"){
                    $sql = $dsql->SetQuery("SELECT `store`, `userid`, `orderstate`, `orderdate`, `paytype`, `ret-state`, `exp-date`, `common` FROM `#@__shop_order` WHERE `id` = $id");
                    $ret_ = $dsql->dsqlOper($sql, "results");
                    if($ret_ && is_array($ret_)){

                        $val = $ret_[0];

                        $sql = $dsql->SetQuery("SELECT * FROM `#@__shop_order_product` WHERE `orderid` = ".$id);
                        $ret = $dsql->dsqlOper($sql, "results");

                        if($ret && is_array($ret)){

                            $param = array(
                                "service"     => "shop",
                                "template"    => "detail",
                                "id"          => "%id%"
                            );
                            $urlParam = getUrlPath($param);

                            $param = array(
                                "service"     => "shop",
                                "template"    => "pay",
                                "param"       => "ordernum=%id%"
                            );
                            $payurlParam = getUrlPath($param);

                            $param = array(
                                "service"  => "member",
                                "type"     => "user",
                                "template" => "orderdetail",
                                "module"   => "shop",
                                "id"       => "%id%",
                                "param"    => "rates=1"
                            );
                            $commonUrlParam = getUrlPath($param);

                            $list[$i]['tab']         = $tab;
                            $list[$i]['id']          = $id;
                            $list[$i]['ordernum']    = $ordernum;

                            //商家信息
                            $detailHandels = new handlers("shop", "storeDetail");
                            $detail  = $detailHandels->getHandle($val['store']);
                            if($detail && $detail['state'] == 100){
                                $data = $detail['info'];
                                $list[$i]['store'] = array(
                                    "id"     => $data['id'],
                                    "title"  => $data['title'],
                                    "domain" => $data['domain'],
                                    "qq"     => $data['qq']
                                );
                            }else{
                                $list[$i]['store'] = array(
                                    "id"     => 0,
                                    "title"  => $langData['siteConfig'][19][709]  //官方直营
                                );
                            }


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
                                    $payname = $cfg_pointName."+余额";
                                }elseif($val["paytype"] == "point"){
                                    $payname = $cfg_pointName;
                                }elseif($val["paytype"] == "money"){
                                    $payname = "余额";
                                }
                                $list[$i]["paytype"]   = $payname;
                            }

                            //未付款的提供付款链接
                            if($val['orderstate'] == 0){
                                $RenrenCrypt = new RenrenCrypt();
                                $encodeid = base64_encode($RenrenCrypt->php_encrypt($ordernum));
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

                                $detailHandels = new handlers("shop", "detail");
                                $detail  = $detailHandels->getHandle($v['proid']);
                                if($detail && $detail['state'] == 100){
                                    $data = $detail['info'];

                                    $list[$i]['product'][$k]['title'] = $data['title'];
                                    $list[$i]['product'][$k]['litpic'] = $data['litpic'];
                                    $list[$i]['product'][$k]['url'] = str_replace("%id%", $v['proid'], $urlParam);

                                    $list[$i]['product'][$k]['price'] = $v['price'];
                                    $list[$i]['product'][$k]['count'] = $v['count'];
                                    $list[$i]['product'][$k]['specation'] = $v['specation'];
                                }

                                //未付款的不计算积分和余额部分
                                if($val['orderstate'] == 0){
                                    $totalPayPrice += $v['price'] * $v['count'] + $v['logistic'] + $v['discount'];
                                }else{
                                    $totalPayPrice += $v['payprice'];
                                }
                            }
                            $list[$i]['totalPayPrice'] = sprintf("%.2f", $totalPayPrice);

                            $i++;

                        }
                    }
                }


                //外卖
                // if($tab == "waimai"){
                //
                // 	$sql = $dsql->SetQuery("SELECT `userid`, `store`, `price`, `paytype`, `peisong`, `offer`, `orderdate`, `note`, `state` FROM `#@__waimai_order` WHERE `id` = $id");
                // 	$ret = $dsql->dsqlOper($sql, "results");
                // 	if($ret && is_array($ret)){
                // 		$data = $ret[0];
                //
                // 		$list[$i]['id'] 			 = $id;
                // 		$list[$i]['ordernum']  = $ordernum;
                // 		$list[$i]['tab']       = $tab;
                // 		$list[$i]['userid']    = $data['userid'];
                // 		$list[$i]['store']     = $data['store'];
                // 		$list[$i]['price']     = $data['price'];
                // 		$list[$i]['paytype']   = $data['paytype'];
                // 		$list[$i]['peisong']   = $data['peisong'];
                // 		$list[$i]['offer']     = $data['offer'];
                // 		$list[$i]['orderdate'] = $data['orderdate'];
                // 		$list[$i]['note']      = $data['note'];
                // 		$list[$i]['state']     = $data['state'];
                //
                // 		//用户名
                // 		$userSql = $dsql->SetQuery("SELECT `username` FROM `#@__member` WHERE `id` = ". $data["userid"]);
                // 		$username = $dsql->dsqlOper($userSql, "results");
                // 		$list[$i]["username"] = $username[0]['username'];
                //
                // 		//餐厅
                // 		$storeSql = $dsql->SetQuery("SELECT `title` FROM `#@__waimai_store` WHERE `id` = ". $data['store']);
                // 		$storename = $dsql->getTypeName($storeSql);
                // 		$list[$i]["storename"] = $storename[0]["title"];
                //
                // 		$paytype = $data["paytype"];
                // 		if(!$paytype){
                // 			$list[$i]["paytype"] = "未知";
                // 		}else{
                // 			//主表信息
                // 			$sql = $dsql->SetQuery("SELECT `pay_name` FROM `#@__site_payment` WHERE `pay_code` = '$paytype'");
                // 			$ret = $dsql->dsqlOper($sql, "results");
                // 			if(!empty($ret)){
                // 				$list[$i]["paytype"] = $ret[0]['pay_name'];
                // 			}else{
                // 				$list[$i]["paytype"] = $data["paytype"];
                // 			}
                // 		}
                //
                // 		//订单内容
                // 		$menuList = array();
                // 		$sql = $dsql->SetQuery("SELECT `pid`, `pname`, `price`, `count` FROM `#@__waimai_order_product` WHERE `orderid` = ".$id);
                // 		$ret = $dsql->dsqlOper($sql, "results");
                // 		if($ret){
                // 			foreach ($ret as $k => $v) {
                // 				$menuList[$k]['pid'] = $v['pid'];
                // 				$menuList[$k]['pname'] = $v['pname'];
                // 				$menuList[$k]['price'] = $v['price'];
                // 				$menuList[$k]['count'] = $v['count'];
                // 			}
                // 		}
                // 		$list[$i]["menus"] = $menuList;
                //
                // 		$i++;
                // 	}
                //
                // }


            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);

    }


    /**
     * 普通会员发布信息，支付
     * @return array
     */
    public function fabuPay(){
        $param =  $this->param;
        $param_ = $param;

        $module     = $param['module'];
        $type       = $param['type'];
        $aid        = $param['aid'];
        $paytype    = $param['paytype'];
        $tourl      = $param['tourl'];
        $useBalance = (int)$param['useBalance'];
        $qr         = (int)$param['qr'];
        $ordernum   = $param['ordernum'];
        $final      = (int)$param['final']; // 最终支付

        if(empty($module) || empty($aid)) die(self::$langData['siteConfig'][33][13]);

        $isMobile = isMobile();

        global $dsql;
        global $userLogin;
        global $langData;
        $userid = $userLogin->getMemberID();
        if($userid == -1) die($langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $tab = "";
        if($module == "house"){
            if(empty($type)) die(self::$langData['siteConfig'][33][13]);
            $tab = "house_".$type;
        }elseif($module == "car" || $module == "huodong" || $module == "tieba" || $module == "vote"){
            $tab = $module."_list";
        }elseif($module == "education"){
            $tab = $module."_courses";
        }else{
            $tab = $module."list";
        }

        $archive = $dsql->SetQuery("SELECT `title` FROM `#@__".$tab."` WHERE `id` = $aid AND `waitpay` = 1");
        $results = $dsql->dsqlOper($archive, "results");
        if($results){

            $title = $results[0]['title'];

            //用户信息
            $userinfo = $userLogin->getMemberInfo();

            global $cfg_fabuAmount;
            $fabuAmount = $cfg_fabuAmount ? unserialize($cfg_fabuAmount) : array();

            $amount = 0; // 应付金额;
            if($fabuAmount){
                $amount = $fabuAmount[$module];
            }

            $balance = 0;	// 使用余额
            $payAmount = 0; // 在线支付金额;

            // 使用余额
            if($useBalance){

                if($amount <= $userinfo['money']){
                    $payAmount = 0;
                }else{
                    $payAmount = $amount - $userinfo['money'];
                }

                $balance = $amount - $payAmount;

            }else{
                $payAmount = $amount;
            }

            $param = array(
                "userid" => $userid,
                "amount" => $amount,
                "balance" => $balance,
                "online" => $payAmount,
                "type" => "fabu",
                "module" => $module,
                "class" => $type,
                "tab" => $tab,
                "aid" => $aid,
                "title" => $title,
                "userid" => $userid
            );

            // 实际支付金额大于0
            if($payAmount || $qr){

                $ordernum = $ordernum ? $ordernum : create_ordernum();
                $param['type'] = 'fabuPay';

                if($isMobile && empty($final)){
                    $param_['ordernum'] = $ordernum;
                    $param_['ordertype'] = 'fabuPay';
                    $param = array(
                        "service" => "member",
                        "type" => "user",
                        "template" => "pay",
                        "param" => http_build_query($param_)
                    );
                    header("location:".getUrlPath($param));
                    die;
                }

                return createPayForm("member", $ordernum, $payAmount, $paytype, $langData['siteConfig'][21][113], $param);  //会员发布信息

            }else{

                $this->fabuPaySuccess($param);

                if($tourl){
                    header("location:".$tourl);
                }else{
                    return $langData['siteConfig'][20][341];  //发布成功
                }

            }

        }else{
            die($langData['siteConfig'][21][114]);  //信息不存在或已完成支付
        }


    }

    /**
     * 会员发布信息支付成功 - 普通会员发布或者付费会员当天超量发布
     */
    public function fabuPaySuccess($param){
        global $dsql;
        global $langData;
        $userid  = $param['userid'];
        $module  = $param['module'];
        $tab     = $param['tab'];
        $title   = $param['title'];
        $aid     = $param['aid'];
        $amount  = $param['amount'];
        $balance = $param['balance'];

        $time = time();

        $archive = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `id` = $aid AND `waitpay` = 1");
        $results = $dsql->dsqlOper($archive, "results");
        if($results){

            //扣除会员余额
            if($balance){
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$balance' WHERE `id` = '$userid'");
                $dsql->dsqlOper($archives, "update");
            }

            //保存操作日志
            $info = $langData['siteConfig'][6][143]."-".$title;  //发布信息
            $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$amount', '$info', '$time')");
            $dsql->dsqlOper($archives, "update");


            // 判断是否需要审核通过
            // include HUONIAOINC."/config/".$module.".inc.php";
            // $arcrank    = (int)$customFabuCheck;

            // , `arcrank` = $arcrank

            $admin = "admin";
            if($module == "info" || $module == "house" || $module == "car" || $module == "education"){
                $admin = "userid";
            }elseif($module == "tieba" || $module == "huodong"){
                $admin = "uid";
            }

            $pubdate = GetMkTime(time());

            $upd = ', `state` = 1';
            if($module == 'article' || $module == 'info' || $module == 'live'){
                $upd = ', `arcrank` = 1';
            }

            $sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `waitpay` = 0, `pubdate` = '$pubdate'".$upd." WHERE `id` = $aid");
            $dsql->dsqlOper($sql, "update");

            //直播
            if($module == 'live'){
                $configHandels = new handlers('live', "createLiveDetail");
                $configHandels->getHandle(array('id' => $aid));
            }

        }

    }

    /**
     * 发布信息继续支付时判断是否需要支付费用
     */
    public function checkFabuAmount(){
        global $dsql;
        global $userLogin;
        global $langData;

        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        }

        $module = $this->param['module'];
        if(empty($module)){
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]."，module");//参数错误
        }

        $needpay = 0;

        //用户信息
        $userinfo = $userLogin->getMemberInfo();

        $alreadyFabu = 0;

        if($userinfo['userType'] == 1){

            if($userinfo['level']){
                $memberLevelAuth = getMemberLevelAuth($userinfo['level']);
                $maxCount = (int)$memberLevelAuth[$module];

                //统计用户当天已发布数量 @
                $today = GetMkTime(date("Y-m-d", time()));
                $tomorrow = GetMkTime(date("Y-m-d", strtotime("+1 day")));

                $tab = array();
                if($module == "house"){
                    $tab = array("house_sale", "house_zu", "house_xzl", "house_sp", "house_xzl");
                }elseif($module == "car" || $module == "huodong" || $module == "tieba" || $module == "vote"){
                    $tab = array($module."_list");
                }elseif($module == "education"){
                    $tab = array($module."_courses");
                }else{
                    $tab = array($module."list");
                }

                $admin = "admin";
                if($module == "car" || $module == "info" || $module == "house"){
                    $admin = "userid";
                }elseif($module == "huodong" || $module == "tieba"){
                    $admin = "uid";
                }

                foreach ($tab as $key => $value) {
                    $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__".$value."` WHERE `".$admin."` = $uid AND `pubdate` >= $today AND `pubdate` < $tomorrow AND `alonepay` = 0 AND `waitpay` = 0");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $alreadyFabu += $ret[0]['total'];
                    }
                }
                if($alreadyFabu >= $maxCount){
                    $needpay = 1;
                }
            }else{
                $needpay = 1;
            }
        }

        if($userinfo['level']){
            $auth = array("level" => $userinfo['level'], "levelname" => $userinfo['levelName'], "alreadycount" => $alreadyFabu, "maxcount" => $maxCount);
        }else{
            $auth = array("level" => 0, "levelname" => $langData['siteConfig'][21][31], "alreadycount" => $alreadyFabu, "maxcount" => 0);  //普通会员
        }

        return array("state" => 100, "auth" => $auth, "needpay" => $needpay);

    }

    /**
     * 付费会员免费更新待支付信息状态 支付状态和审核状态
     */
    public function updateFabuPaystate(){
        global $dsql;
        global $userLogin;
        global $langData;

        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        }

        $module = $this->param['module'];
        $type = $this->param['type'];

        $aid = (int)$this->param['aid'];
        if(empty($module) || empty($aid)){
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误
        }

        $tab = "";
        $state = "state";
        if($module == "house"){
            if(empty($type)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误
            $tab = "house_".$type;
        }elseif($module == "car" || $module == "huodong" || $module == "tieba" || $module == "vote"){
            $tab = $module."_list";
        }elseif($module == "education"){
            $tab = $module."_courses";
        }else{
            $tab = $module."list";
            $state = "arcrank";
        }

        $archive = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `id` = $aid AND `waitpay` = 1");
        $results = $dsql->dsqlOper($archive, "results");
        if($results){
            $pubdate = GetMkTime(time());
            $sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `pubdate` = $pubdate, `waitpay` = 0, `alonepay` = 0, `".$state."` = 1 WHERE `id` = $aid");
            $ret = $dsql->dsqlOper($sql, "update");
            if($ret == "ok"){
                return self::$langData['siteConfig'][20][244];//操作成功;
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][20][295]);//操作失败！
            }
        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][21][114]);//信息不存在或已完成支付
        }

    }



    /**
     * 统计指定日期的收入
     */
    public function statisticsDateRevenue(){
        global $dsql;
        global $userLogin;
        global $installModuleArr;
        global $langData;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $date = $this->param['date'];
            }
        }

        $date = empty($date) ? date("Y-m-d", time()) : $date;  //如果为空取当天
        $date = strtotime($date);


        $began = $date;  //一天的开始时间
        $end   = $date + 24 * 3600 - 1;  //一天的结束时间

        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        //要返回的数组内容
        $data = array();

        //团购
        if(in_array("tuan", $installModuleArr) && verifyModuleAuth(array("module" => "tuan"))){
            $totalAmount = $totalCount = 0;
            $sql = $dsql->SetQuery("SELECT o.`procount`, o.`orderprice`, o.`propolic`, l.`tuantype` FROM `#@__tuan_order` o LEFT JOIN `#@__tuanlist` l ON o.`proid` = l.`id` LEFT JOIN `#@__tuan_store` s ON l.`sid` = s.`id` WHERE s.`uid` = '$userid' AND o.`orderstate` = 3 AND o.`orderdate` >= $began AND o.`orderdate` <= $end");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret && is_array($ret)){
                foreach ($ret as $key => $value) {
                    $totalCount++;

                    $procount   = $value['procount'];
                    $orderprice = $value['orderprice'];
                    $propolic   = $value['propolic'];
                    $tuantype   = $value['tuantype'];

                    $totalAmount += $orderprice * $procount;

                    //快递类型，需要计算运费
                    if($tuantype == 2){
                        $policy = unserialize($propolic);
                        if(!empty($propolic) && !empty($policy)){
                            $freight  = $policy['freight'];
                            $freeshi  = $policy['freeshi'];

                            //如果达不到免物流费的数量，则总价再加上运费
                            if($procount <= $freeshi){
                                $totalAmount += $freight;
                            }
                        }
                    }

                }

            }

            //扣除佣金
            global $cfg_tuanFee;
            $cfg_tuanFee = (float)$cfg_tuanFee;

            $fee = $totalAmount * $cfg_tuanFee / 100;
            $fee = $fee < 0.01 ? 0 : $fee;
            $totalAmount = $totalAmount - $fee;

            array_push($data, array(
                "module" => "tuan",
                "count"  => (int)$totalCount,
                "amount" => sprintf('%.2f', $totalAmount)
            ));
        }

        //商城
        if(in_array("shop", $installModuleArr) && verifyModuleAuth(array("module" => "shop"))){
            $totalAmount = $totalCount = 0;
            $sql = $dsql->SetQuery("SELECT o.`id` FROM `#@__shop_order` o LEFT JOIN `#@__shop_store` s ON s.`id` = o.`store` WHERE s.`userid` = '$userid' AND o.`orderstate` = 3 AND o.`orderdate` >= $began AND o.`orderdate` <= $end");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret && is_array($ret)){
                foreach ($ret as $key => $value) {
                    $id = $value['id'];

                    $opsql = $dsql->SetQuery("SELECT `price`, `count`, `logistic`, `discount`, `balance`, `payprice` FROM `#@__shop_order_product` WHERE `orderid` = $id");
                    $opret = $dsql->dsqlOper($opsql, "results");
                    if($opret){
                        $totalCount++;

                        foreach ($opret as $k => $v) {

                            $price    = $v['price'];
                            $count    = $v['count'];
                            $logistic = $v['logistic'];
                            $discount = $v['discount'];

                            $totalAmount += $price * $count + $logistic + $discount;

                        }
                    }
                }

            }

            //扣除佣金
            global $cfg_shopFee;
            $cfg_shopFee = (float)$cfg_shopFee;

            $fee = $totalAmount * $cfg_shopFee / 100;
            $fee = $fee < 0.01 ? 0 : $fee;
            $totalAmount = $totalAmount - $fee;

            array_push($data, array(
                "module" => "shop",
                "count"  => (int)$totalCount,
                "amount" => sprintf('%.2f', $totalAmount)
            ));
        }

        //外卖
        if(in_array("waimai", $installModuleArr) && verifyModuleAuth(array("module" => "waimai"))){
            $sql = $dsql->SetQuery("SELECT `shopid` id FROM `#@__waimai_shop_manager` WHERE `userid` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $store = $ret[0]['id'];

                $totalAmount = $totalCount = 0;
                $sql = $dsql->SetQuery("SELECT o.`ordernum`, o.`amount` FROM `#@__waimai_order` o LEFT JOIN `#@__waimai_shop` s ON o.`sid` = s.`id` WHERE o.`state` = 1 AND o.`pubdate` >= $began AND o.`pubdate` <= $end AND o.`sid` = $store");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret && is_array($ret)){
                    foreach ($ret as $key => $value) {
                        $totalCount++;

                        $totalAmount += $value['amount'];

                    }

                }

                //扣除佣金
                // global $cfg_waimaiFee;
                // $cfg_waimaiFee = (float)$cfg_waimaiFee;

                // $fee = $totalAmount * $cfg_waimaiFee / 100;
                // $fee = $fee < 0.01 ? 0 : $fee;
                // $totalAmount = $totalAmount - $fee;

                array_push($data, array(
                    "module" => "waimai",
                    "count"  => (int)$totalCount,
                    "amount" => sprintf('%.2f', $totalAmount)
                ));
            }
        }

        // print_r($data);die;

        return $data;

    }

    /*
	 * 获取订单明细
	 */
    public function getOrderList(){
        global $dsql;
        global $userLogin;
        global $langData;

        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $module   = $this->param['module'];
        $page     = $this->param['page'];
        $pageSize = $this->param['pageSize'];
        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if(empty($module)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]."，module");//参数错误
        if($module != "tuan" && $module != "shop" && $module != "waimai") return array("state" => 200, "info" => self::$langData['siteConfig'][33][35]);//该模块没有相关数据

        $user = "userid";
        if($module == "tuan"){
            $user = "uid";
        }

        if($module == "waimai"){
            $sql = $dsql->SetQuery("SELECT `shopid` id FROM `#@__waimai_shop_manager` WHERE `".$user."` = $userid");
        }else{
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__".$module."_store` WHERE `".$user."` = $userid AND `state` = 1");
        }
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $store = $ret[0]['id'];
        }else{
            return array("state" => 200, "info" => $langData['siteConfig'][21][115]);  //您还没有开通相关店铺或店铺状态异常
        }

        $list = array();
        $totalPrice = 0;

        if($module == "shop"){
            $archives = $dsql->SetQuery("SELECT `id`, `paydate`, `paytype`, `ordernum`, `userid`, `orderdate` FROM `#@__shop_order` WHERE `store` = '$store' AND `orderstate` = 3");
            //总条数
            $totalCount = $dsql->dsqlOper($archives, "totalCount");

            if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

            //扣除佣金
            global $cfg_shopFee;
            $cfg_shopFee = (float)$cfg_shopFee;

            $atpage = $pageSize*($page-1);
            $where = " ORDER BY `id` DESC LIMIT $atpage, $pageSize";

            $results = $dsql->dsqlOper($archives.$where, "results");
            if($results){

                $sql = $dsql->SetQuery("SELECT `title`, `logo` FROM `#@__".$module."_store` WHERE `id` = $store");
                $ret = $dsql->dsqlOper($sql, "results");

                $param = array(
                    "service" => $module,
                    "template" => "store-detail",
                    "id" => $store
                );
                $storeInfo = array(
                    "id" => $store,
                    "title" => $ret[0]['title'],
                    "logo" => empty($ret[0]['logo']) ? "" : getFilePath($ret[0]['logo']),
                    "url" => getUrlPath($param)
                );

                foreach ($results as $key => $value) {

                    $sql = $dsql->SetQuery("SELECT `nickname` FROM `#@__member` WHERE `id` = ".$value['userid']);
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $user = $ret[0]['nickname'] ? $ret[0]['nickname'] : "user_".$value['userid'];
                    }else{
                        $user = $value['userid'];
                    }

                    $totalPrice = 0;

                    $id = $value['id'];
                    //计算费用
                    $sql = $dsql->SetQuery("SELECT `price`, `count`, `logistic`, `discount`, `balance`, `payprice` FROM `#@__shop_order_product` WHERE `orderid` = $id");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        foreach($ret as $k => $val){
                            $totalPrice += $val['price'] * $val['count'] + $val['logistic'] + $val['discount'];
                        }
                    }

                    $totalPrice_ = 0;
                    if($totalPrice > 0){

                        $fee = $totalPrice * $cfg_shopFee / 100;
                        $fee = $fee < 0.01 ? 0 : $fee;
                        $totalPrice_ = sprintf('%.2f', $totalPrice - $fee);

                    }

                    array_push($list, array(
                        "id" => $id,
                        "paytype" => $value['paytype'],
                        "date" => date("Y-m-d H:i:s", $value['paydate']),
                        "orderdate" => date("Y-m-d H:i:s", $value['orderdate']),
                        "ordernum" => $value['ordernum'],
                        "user" => $user,
                        "amount" => $totalPrice_,
                        "store" => $storeInfo
                    ));
                }
            }

        }elseif($module == "waimai"){
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__waimai_order` WHERE `sid` = ".$store." AND `state` = 1");
            //总条数
            $totalCount = $dsql->dsqlOper($archives, "totalCount");

            if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

            $atpage = $pageSize*($page-1);
            $where = " ORDER BY `id` DESC LIMIT $atpage, $pageSize";

            $archives = $dsql->SetQuery("SELECT `id`, `amount` price, `confirmdate`, `paytype`, `ordernum`, `uid`, `okdate` FROM `#@__waimai_order` WHERE `sid` = ".$store." AND `state` = 1");
            $results = $dsql->dsqlOper($archives.$where, "results");
            if($results){

                $sql = $dsql->SetQuery("SELECT `shopname`, `shop_banner` FROM `#@__".$module."_shop` WHERE `id` = $store");
                $ret = $dsql->dsqlOper($sql, "results");

                $param = array(
                    "service" => $module,
                    "template" => "shop",
                    "id" => $store
                );

                $shop_banner = $ret[0]['shop_banner'] ? explode(',', $ret[0]['shop_banner']) : array();
                $shop_banner = $shop_banner ? $shop_banner[0] : '';

                $storeInfo = array(
                    "id" => $store,
                    "title" => $ret[0]['shopname'],
                    "logo" => empty($shop_banner) ? "" : getFilePath($shop_banner),
                    "url" => getUrlPath($param)
                );

                foreach ($results as $key => $value) {

                    $sql = $dsql->SetQuery("SELECT `nickname` FROM `#@__member` WHERE `id` = ".$value['uid']);
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $user = $ret[0]['nickname'] ? $ret[0]['nickname'] : "user_".$value['uid'];
                    }else{
                        $user = $value['uid'];
                    }

                    $totalPrice = $value['price'];

                    // $totalPrice = sprintf("%.2f", $value['price'] + $value['peisong'] - $value['offer']);
                    //扣除佣金
                    // global $cfg_waimaiFee;
                    // $cfg_waimaiFee = (float)$cfg_waimaiFee;

                    $fee = $totalPrice * $cfg_waimaiFee / 100;
                    $fee = $fee < 0.01 ? 0 : $fee;
                    $totalPrice_ = sprintf('%.2f', $totalPrice - $fee);

                    array_push($list, array(
                        "id" => $value['id'],
                        "paytype" => $value['paytype'],
                        "date" => date("Y-m-d H:i:s", $value['confirmdate']),
                        "orderdate" => date("Y-m-d H:i:s", $value['okdate']),
                        "ordernum" => $value['ordernum'],
                        "user" => $user,
                        "amount" => $totalPrice_,
                        "store" => $storeInfo
                    ));
                }
            }

        }elseif($module == "tuan"){
            $proidList = array();
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__tuanlist` WHERE `sid` = $store");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                global $data;
                $data = "";
                $proidList = parent_foreach($ret, "id");
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！
            }
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__tuan_order` WHERE `proid` IN (".join(",", $proidList).") AND `orderstate` = 3");
            //总条数
            $totalCount = $dsql->dsqlOper($archives, "totalCount");

            if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

            $atpage = $pageSize*($page-1);
            $where = " ORDER BY `id` DESC LIMIT $atpage, $pageSize";

            $archives = $dsql->SetQuery("SELECT `id`, `ordernum`, `procount`, `orderprice`, `balance`, `payprice`, `propolic`, `paydate`, `paytype`, `userid`, `orderdate` FROM `#@__tuan_order` WHERE `proid` IN (".join(",", $proidList).") AND `orderstate` = 3");
            $results = $dsql->dsqlOper($archives.$where, "results");
            if($results){

                $sql = $dsql->SetQuery("SELECT m.`company` FROM `#@__member` m LEFT JOIN `#@__tuan_store` s ON s.`uid` = m.`id` WHERE s.`id` = $store");
                $ret = $dsql->dsqlOper($sql, "results");

                $param = array(
                    "service" => $module,
                    "template" => "store",
                    "id" => $store
                );
                $storeInfo = array(
                    "id" => $store,
                    "title" => $ret[0]['company'],
                    "logo" => "",
                    "url" => getUrlPath($param)
                );

                foreach ($results as $key => $value) {

                    $sql = $dsql->SetQuery("SELECT `nickname` FROM `#@__member` WHERE `id` = ".$value['userid']);
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $user = $ret[0]['nickname'] ? $ret[0]['nickname'] : "user_".$value['userid'];
                    }else{
                        $user = $value['userid'];
                    }

                    $totalPrice = 0;

                    $procount   = $value['procount'];   //数量
                    $orderprice = $value['orderprice']; //单价
                    $propolic   = $value['propolic'];   //邮费
                    $policy     = unserialize($propolic);

                    //商家结算
                    $totalPrice += $orderprice * $procount;
                    $freightMoney = 0;

                    if(!empty($propolic) && !empty($policy)){
                        $freight  = $policy['freight'];
                        $freeshi  = $policy['freeshi'];

                        //如果达不到免物流费的数量，则总价再加上运费
                        if($procount <= $freeshi){
                            $totalPrice += $freight;
                            $freightMoney = $freight;
                        }
                    }

                    //扣除佣金
                    global $cfg_tuanFee;
                    $cfg_tuanFee = (float)$cfg_tuanFee;

                    $fee = $totalPrice * $cfg_tuanFee / 100;
                    $fee = $fee < 0.01 ? 0 : $fee;
                    $totalPrice_ = sprintf('%.2f', $totalPrice - $fee);

                    array_push($list, array(
                        "id" => $value['id'],
                        "paytype" => $value['paytype'],
                        "date" => date("Y-m-d H:i:s", $value['paydate']),
                        "orderdate" => date("Y-m-d H:i:s", $value['orderdate']),
                        "ordernum" => $value['ordernum'],
                        "user" => $user,
                        "amount" => $totalPrice_,
                        "store" => $storeInfo
                    ));
                }
            }
        }

        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        return array("pageInfo" => $pageinfo, "list" => $list);

    }


    /**
     * 访客记录
     * @return array
     */
    public function visitor(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $uid = $page = $pageSize = 0;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $uid      = $this->param['uid'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        if(empty($uid)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][12]);//会员ID不得为空！

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_visitor` WHERE `tid` = $uid ORDER BY `id` DESC");

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

        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives.$where, "results");

        if($results){
            foreach($results as $key => $val){

                //查询会员信息
                if($val['uid'] > 0){
                    $this->param = $val['uid'];
                    $detail = $this->detail();
                    if($detail && is_array($detail)){
                        $list[$key]['uid']  = $val['uid'];
                        $list[$key]['nickname'] = $detail['nickname'];
                        $list[$key]['level'] = $detail['level'];
                        $list[$key]['levelName'] = $detail['levelName'];
                        $list[$key]['levelIcon'] = $detail['levelIcon'];
                        $list[$key]['photo'] = $detail['photo'];
                        $addrName = explode(" > ", $detail['addrName']);
                        $list[$key]['addrName'] = $addrName[count($addrName)-1];
                        $list[$key]['date'] = FloorTime(time() - $val['date']);
                    }
                }

            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);

    }


    /**
     * 会员粉丝/关注
     * @return array
     */
    public function follow(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $uid = $type = $page = $pageSize = 0;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $id       = (int)$this->param['id'];
                $uid      = $this->param['uid'];
                $type     = $this->param['type'];
                $for      = $this->param['for'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $uid = $id ? $id : $uid;

        if(empty($uid)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $for_ = empty($for) ? "user" : $for;


        $where = "`fid` = $uid";
        if($type == "follow"){
            $where = "`tid` = $uid";
        }
        $where .= " AND `for` = '$for'";



        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_follow` WHERE ".$where." ORDER BY `id` DESC");

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

        //当前登录会员ID
        $loginid = $userLogin->getMemberID();

        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives.$where, "results");

        if($results){
            foreach($results as $key => $val){

                if($for_ == "user"){

                    $userid = $type == "follow" ? $val['fid'] : $val['tid'];

                    $list[$key]['for']  = $for_;
                    $list[$key]['uid']  = $userid;
                    $list[$key]['date'] = FloorTime(time() - $val['date']);

                    //查询会员信息
                    $this->param = $userid;
                    $detail = $this->detail();
                    if($detail && is_array($detail)){
                        $list[$key]['nickname'] = $detail['nickname'];
                        $list[$key]['level'] = $detail['level'];
                        $list[$key]['levelName'] = $detail['levelName'];
                        $list[$key]['levelIcon'] = $detail['levelIcon'];
                        $list[$key]['photo'] = $detail['photo'];
                        $addrName = explode(" > ", $detail['addrName']);
                        $list[$key]['addrName'] = $addrName[count($addrName)-1];
                    }else{
                        $list[$key]['state'] = 1;
                    }

                    //判断是否互相关注
                    if($loginid != -1){
                        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $loginid AND `fid` = $userid");
                        $ret = $dsql->dsqlOper($sql, "results");
                        if($ret && is_array($ret)){
                            $list[$key]['isfollow'] = 1;
                        }
                    }

                // 关注自媒体
                }elseif($for_ == "media"){
                    $article = new article();
                    $media = $article->selfmediaDetail(0, $val['fid']);
                    if($media){
                        $list[$key]['meidia'] = $media;
                        $list[$key]['for']  = $for_;
                        $list[$key]['date'] = FloorTime(time() - $val['date']);
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
    public function followMember(){
        global $dsql;
        global $langData;
        global $userLogin;
        $id = $this->param['id'];
        $for = $this->param['for'];

        if(empty($id)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误

        $userid = $this->param['from'] ? $this->param['from'] : $userLogin->getMemberID();

        if($userid <= 0) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！

        if(empty($for)){
            if($userid == $id) return array("state" => 200, "info" => self::$langData['siteConfig'][33][36]);//不能关注自己
        }
        if($for == "media"){
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__article_selfmedia` WHERE `userid` = $userid AND `id` = $id");
            $res = $dsql->dsqlOper($sql, "reuslts");
            if($res){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][37]);//不能关注自己的自媒体账号
            }
        }

        $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = '$userid' AND `fid` = '$id' AND `for` = '$for'");
        $return = $dsql->dsqlOper($archives, "totalCount");

        $time = time();
        if($return == 0){
            $archives = $dsql->SetQuery("INSERT INTO `#@__member_follow` (`fid`, `tid`, `date`, `for`) VALUES ('$id', '$userid', '$time', '$for')");
            $dsql->dsqlOper($archives, "update");
        }else{
            $archives = $dsql->SetQuery("DELETE FROM `#@__member_follow` WHERE `tid` = '$userid' AND `fid` = '$id' AND `for` = '$for'");
            $dsql->dsqlOper($archives, "update");
        }
        return "ok";


    }


    /**
     * 会员留言列表
     * @return array
     */
    public function messageList(){
        global $dsql;
        global $userLogin;
        global $langData;
        $pageinfo = $list = array();
        $uid = $page = $pageSize = 0;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $uid      = $this->param['uid'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        if(empty($uid)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][12]);//会员ID不得为空！

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives = $dsql->SetQuery("SELECT * FROM `#@__member_message` WHERE `tid` = $uid ORDER BY `id` DESC");

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

        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives.$where, "results");

        if($results){
            foreach($results as $key => $val){

                $list[$key]['id']  = $val['id'];
                $list[$key]['uid']  = $val['uid'];
                $list[$key]['content']  = $val['content'];
                $list[$key]['ip']  = $val['ip'];
                $list[$key]['ipaddr']  = $val['ipaddr'];
                $list[$key]['date'] = FloorTime(time() - $val['date']);

                //查询会员信息
                $this->param = $val['uid'];
                $detail = $this->detail();
                if($detail && is_array($detail)){
                    $list[$key]['nickname'] = $detail['nickname'];
                    $list[$key]['level'] = $detail['level'];
                    $list[$key]['levelName'] = $detail['levelName'];
                    $list[$key]['levelIcon'] = $detail['levelIcon'];
                    $list[$key]['photo'] = $detail['photo'];
                    $addrName = explode(" > ", $detail['addrName']);
                    $list[$key]['addrName'] = $addrName[count($addrName)-1];
                }else{
                    $list[$key]['state'] = 1;
                }

                //回复内容
                if($val['rid']){
                    $reply = array();
                    $sql = $dsql->SetQuery("SELECT `uid`, `content`, `ip`, `ipaddr`, `date` FROM `#@__member_message` WHERE `id` = ".$val['rid']);
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $data = $ret[0];
                        $reply['uid'] = $data['uid'];
                        $reply['content'] = $data['content'];
                        $reply['ip'] = $data['ip'];
                        $reply['ipaddr'] = $data['ipaddr'];
                        $reply['date'] = FloorTime(time() - $data['date']);

                        //查询会员信息
                        $this->param = $data['uid'];
                        $detail = $this->detail();
                        if($detail && is_array($detail)){
                            $reply['uid'] = $data['uid'];
                            $reply['nickname'] = $detail['nickname'];
                            $reply['level'] = $detail['level'];
                            $reply['levelName'] = $detail['levelName'];
                            $reply['levelIcon'] = $detail['levelIcon'];
                            $reply['photo'] = $detail['photo'];
                            $addrName = explode(" > ", $detail['addrName']);
                            $reply['addrName'] = $addrName[count($addrName)-1];
                        }else{
                            $reply['state'] = 1;
                        }
                    }

                    if($reply){
                        $list[$key]['reply'] = $reply;
                    }
                }
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);

    }




    /**
     * 会员列表
     * @return array
     */
    public function memberList(){
        global $dsql;
        global $userLogin;
        global $cfg_secureAccess;
        global $cfg_basehost;
        global $langData;
        $uid = $userLogin->getMemberID();

        $where = "";
        $list = array();
        if(!empty($this->param)){
            if(is_array($this->param)){
                $type     = (int)$this->param['type'];
                $page     = $this->param['page'];
                $pageSize = $this->param['pageSize'];
            }
        }

        $where .= " AND `mtype` != 0 AND `mtype` != 3";
        if($type){
            $where .= " AND `mtype` = $type";
        }

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;


        $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE 1 = 1".$where);
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

        $atpage = $pageSize*($page-1);
        $where .= " ORDER BY `id` DESC LIMIT $atpage, $pageSize";

        $archives = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `photo`, `level` FROM `#@__member` WHERE 1 = 1".$where);
        $results  = $dsql->dsqlOper($archives, "results");
        if($results){
            foreach ($results as $key => $value) {
                $list[$key]['id'] = $value['id'];
                $list[$key]['nickname'] = empty($value['nickname']) ? $value['username'] : $value['nickname'];
                $list[$key]['photo'] = empty($value['photo']) ? '' : getFilePath($value['photo']);
                $list[$key]['level'] = $value['level'];

                $name = $langData['siteConfig'][19][849];  //普通会员
                $icon = '';
                if($value['level']){
                    $arc = $dsql->SetQuery("SELECT `name`, `icon` FROM `#@__member_level` WHERE `id` = ".$value['level']);
                    $ret = $dsql->dsqlOper($arc, "results");
                    if(!empty($ret)){
                        $name = $ret[0]['name'];
                        $icon = $ret[0]['icon'];
                    }
                }

                $list[$key]['levelname'] = $name;
                $list[$key]['levelicon'] = empty($icon) ? '' : getFilePath($icon);

                // 登陆会员检查是否关注
                $follow = 0;
                if($uid != -1){
                    if($uid == $value['id']){
                        $follow = -1;
                    }else{
                        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $uid AND `fid` = ".$value['id']);
                        $ret = $dsql->dsqlOper($sql, "results");
                        if($ret){
                            $follow = 1;
                        }
                    }
                }
                $list[$key]['follow'] = $follow;

                $list[$key]['url'] = $cfg_secureAccess.$cfg_basehost."/user/".$value['id'];
            }
        }else{
            return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！
        }

        return array("pageInfo" => $pageinfo, "list" => $list);

    }

    /**
     * 操作购物车
     * @return array
     */
    public function operateCart(){
        global $dsql;
        global $userLogin;
        global $langData;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $type     = $this->param['type'];
                $store    = (int)$this->param['store'];
                $module   = $this->param['module'];
                $content  = $this->param['content'];
            }
        }

        //当前登录会员ID
        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        if(empty($module)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误
        if($module == 'waimai' && empty($store)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误

        $type = empty($type) ? 'get' : $type;

        $sql = $dsql->SetQuery("SELECT `".$module."` FROM `#@__member_cart` WHERE `userid` = $userid");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $content_ = $ret[0][$module];

            $contentOld = $content_;
            $contentNew = "";
            if($module == 'waimai'){
                if($content_){
                    $contentArr = explode(",", $content_);
                    foreach ($contentArr as $key => $value) {
                        $info = explode("#", $value);
                        if($info[0] == $store){
                            $contentOld = $info[1];
                            if(empty($content)){
                                unset($contentArr[$key]);
                            }else{
                                $contentArr[$key] = $store."#".$content;
                            }
                            break;
                        }
                    }
                    if($contentOld == ""){
                        $contentNew = $content_.",".$store."#".$content;
                    }else{
                        if(count($contentArr) > 20){
                            $contentArr[0] = $store."#".$content;
                        }
                        $contentNew = join(",", $contentArr);
                    }
                }else{
                    $contentNew = $store."#".$content;
                }
            }else{
                $contentNew = $content;
            }
            if($type == 'get'){
                return array("content" => $contentOld);
            }

            $sql = $dsql->SetQuery("UPDATE `#@__member_cart` SET `".$module."` = '$contentNew' WHERE `userid` = $userid");
            $ret = $dsql->dsqlOper($sql, "update");
        }else{
            if($type == 'get'){
                return array("content" => '');
            }
            $sql = $dsql->SetQuery("INSERT INTO `#@__member_cart` (`userid`, `".$module."`) VALUES ('$userid', '$content')");
            $ret = $dsql->dsqlOper($sql, "update");
        }

        if($ret == "ok"){
            return self::$langData['siteConfig'][22][106];//更新成功！
        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][21][179]);//更新失败，请重试！
        }

    }

    /**
     * 签到日志记录
     * @return array
     */
    public function qiandaoRecord(){
        global $dsql;
        global $userLogin;
        global $langData;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $year  = (int)$this->param['year'];
                $month = (int)$this->param['month'];
            }
        }

        global $cfg_qiandao_state;
        if(!$cfg_qiandao_state) return array("state" => 200, "info" => $langData['siteConfig'][22][127]);  //签到功能未开启！

        //当前登录会员ID
        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        if(empty($year) || empty($month)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误

        //时间范围   指定年月第一天和最后一天
        $startTime = GetMkTime($year."-".$month."-1 00:00:00");
        $endTime = strtotime("+1 months", GetMkTime($year."-".$month."-1 00:00:00")) - 1;

        //查询指定范围时间内的数据

        $alreadyQiandaoDate = array();  //记录已经签到的天

        //已签到数据
        $alreadyQiandao = array();
        $sql = $dsql->SetQuery("SELECT `date`, `note` FROM `#@__member_qiandao` WHERE `uid` = $userid AND `date` >= $startTime AND `date` <= $endTime");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            foreach ($ret as $key => $value) {
                $date = (int)date("d", $value['date']);
                array_push($alreadyQiandao, array(
                    "date" => $date,
                    "note" => $value['note']
                ));

                array_push($alreadyQiandaoDate, $date);
            }
        }

        //查询当月没有签到的天
        $notQiandao = array();
        if($year == date("Y") && $month == date("m")){
            $currentMonthTotalDay = date('t', GetMktime($year."-".$month."-1"));  //查询月共多少天
            for ($i = 1; $i <= $currentMonthTotalDay; $i++) {
                if(!in_array($i, $alreadyQiandaoDate) && $i < date("d")){
                    array_push($notQiandao, $i);
                }
            }
        }

        //查询特殊日期
        $specialDate = array();
        global $cfg_qiandao_teshu;
        $qiandao_teshu = unserialize($cfg_qiandao_teshu);
        foreach ($qiandao_teshu as $key => $value) {
            array_push($specialDate, array(
                "date" => date("Y-m-d", $value['date']),
                "title" => $value['title'],
                "color" => $value['color']
            ));
        }


        return array("alreadyQiandao" => $alreadyQiandao, "notQiandao" => $notQiandao, "specialDate" => $specialDate);
    }

    /**
     * 签到
     * @return array
     */
    public function qiandao(){
        global $dsql;
        global $userLogin;
        global $langData;
        global $cfg_pointName;

        global $cfg_qiandao_buqianState;  //开启补签
        global $cfg_qiandao_buqianPrice;  //补签扣除积分
        global $cfg_qiandao_firstReward;  //首次签到
        global $cfg_qiandao_reward;       //日常签到
        global $cfg_qiandao_lianqian;     //连续签到
        global $cfg_qiandao_zongqian;     //总签到
        global $cfg_qiandao_teshu;        //特殊签到

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $date  = $this->param['date'];
            }
        }

        global $cfg_qiandao_state;
        if(!$cfg_qiandao_state) return array("state" => 200, "info" => $langData['siteConfig'][22][127]);  //签到功能未开启！

        //当前登录会员ID
        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
        if(empty($date)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误

        $date = GetMkTime($date);
        $now = time();
        $qiandaoTime = $now;

        $reward = 0;
        $note = array();

        //大于当前时间的
        if($date > $now) return array("state" => 200, "info" => self::$langData['siteConfig'][21][85]);//提交失败

        //不在当前月份的
        if(date("Y-m", $date) != date("Y-m", $now)) return array("state" => 200, "info" => self::$langData['siteConfig'][21][85]);//提交失败

        //判断是否补签.
        $buqian = 0;
        if($date >= GetMkTime(date("Y-m-1", time())) && $date < $now && $date != GetMkTime(date("Y-m-d", $now))){
            $buqian = 1;

            //查询积分余额是否足够
            $uinfo = $userLogin->getMemberInfo();
            $point = $uinfo['point'];

            if($point < $cfg_qiandao_buqianPrice) return array("state" => 200, "info" => $langData['siteConfig'][20][219]);  //可用积分不足！

            $reward -= $cfg_qiandao_buqianPrice;
            $n = $langData['siteConfig'][22][114] . date("Y-m-d", $date) . " -" . $cfg_qiandao_buqianPrice . $cfg_pointName;   //补签 2017-12-12 -1积分
            array_push($note, $n);

            //积分扣除
            // $sql = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` - $cfg_qiandao_buqianPrice WHERE `id` = $userid");
            // $dsql->dsqlOper($sql, "update");

            //记录积分动态
            // $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$cfg_qiandao_buqianPrice', '$n', '$now')");
            // $aid = $dsql->dsqlOper($archives, "lastid");

            $qiandaoTime = $date;

            //正常签到
        }else{
            //判断今天是否已经签过
            $sql = $dsql->SetQuery("SELECT `date` FROM `#@__member_qiandao` WHERE `uid` = $userid ORDER BY `date` DESC LIMIT 1");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $lastQiandao = GetMkTime(date("Y-m-d", $ret[0]['date']));
                $today = GetMkTime(date("Y-m-d", time()));

                if($lastQiandao == $today){
                    return array("state" => 200, "info" => $langData['siteConfig'][22][125]);  //今天已经签过，无需重复签到！
                }
            }
        }

        //判断是否首次签到
        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_qiandao` WHERE `uid` = $userid");
        $ret = $dsql->dsqlOper($sql, "totalCount");
        if($ret == 0){
            $reward += $cfg_qiandao_firstReward;
            array_push($note, $langData['siteConfig'][22][130]."+".$cfg_qiandao_firstReward.$cfg_pointName);  //首次签到 +1 积分
        }else{

            //是否满足总签到条件
            $totalQiandao = 1;
            $sql = $dsql->SetQuery("SELECT `id`, `date` FROM `#@__member_qiandao` WHERE `uid` = $userid ORDER BY `date` DESC");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $totalQiandao += count($ret);
            }

            $iszongqian = false;
            $zongqian = unserialize($cfg_qiandao_zongqian);
            if($zongqian){
                foreach ($zongqian as $key => $value) {
                    $day = (int)$value['day'];
                    if($day == $totalQiandao && !$iszongqian){
                        $iszongqian = true;
                        $reward += $value['reward'];
                        array_push($note, $langData['siteConfig'][22][112].$day.$langData['siteConfig'][13][6]."+".$value['reward'].$cfg_pointName);  //总签到1天+1积分
                    }
                }
            }

            //是否满足连续签到条件
            if(!$iszongqian){
                $totalLianqian = 1;
                $dateArr = array();
                if($ret){
                    foreach ($ret as $key => $value) {
                        array_push($dateArr, $value['date']);
                    }
                }

                $totalLianqian += getContinueDay($dateArr);

                $islianqian = false;
                $lianqian = unserialize($cfg_qiandao_lianqian);
                if($lianqian){
                    foreach ($lianqian as $key => $value) {
                        $day = (int)$value['day'];
                        if($day == $totalLianqian && !$islianqian){
                            $islianqian = true;
                            $reward += $value['reward'];
                            array_push($note, $langData['siteConfig'][22][111].$day.$langData['siteConfig'][13][6]."+".$value['reward'].$cfg_pointName);  //连续签到1天+1积分
                        }
                    }

                    //都不满足则为日常签到
                    if(!$islianqian && !$buqian){
                        $reward += $cfg_qiandao_reward;
                        array_push($note, $langData['siteConfig'][22][131]."+".$cfg_qiandao_reward.$cfg_pointName);  //日常签到+1积分
                    }
                }
            }

        }

        //特殊日期签到
        $isteshu = false;
        $teshu = unserialize($cfg_qiandao_teshu);
        if($teshu){
            foreach ($teshu as $key => $value) {
                if($value['date'] == $date && !$isteshu){
                    $reward += $value['reward'];
                    array_push($note, $value['title']."+".$value['reward'].$cfg_pointName);  //日常签到+1积分
                }
            }
        }

        $reward_ = ($reward > 0 ? "+" : "") . $reward;
        $note_ = join(" ", $note);

        $ip = GetIP();
        $ipaddr = getIpAddr($ip);

        //记录签到信息
        $sql = $dsql->SetQuery("INSERT INTO `#@__member_qiandao` (`uid`, `reward`, `date`, `note`, `ip`, `ipaddr`) VALUES ('$userid', '$reward_', '$qiandaoTime', '$note_', '$ip', '$ipaddr')");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){

            //积分到账
            $sql = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + $reward WHERE `id` = $userid");
            $dsql->dsqlOper($sql, "update");

            //记录积分动态
            $ope = $reward > 0 ? 1 : 0;
            $rew = $reward > 0 ? $reward : abs($reward);
            $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '$ope', '$rew', '$note_', '$now')");
            $aid = $dsql->dsqlOper($archives, "lastid");

            //查询帐户信息
            $archives = $dsql->SetQuery("SELECT `username`, `nickname`, `mtype`, `money`, `freeze`, `point` FROM `#@__member` WHERE `id` = ".$userid);
            $results = $dsql->dsqlOper($archives, "results");

            //用户名
            $username = $results[0]['nickname'] ? $results[0]['nickname'] : $results[0]['username'];
            $mtype    = $results[0]['mtype'];
            $point    = $results[0]['point'];

            //会员中心积分记录页面链接
            if($mtype == 2){
                $param = array(
                    "service"  => "member",
                    "type"     => "user",
                    "template" => "point"
                );
            }else{
                $param = array(
                    "service"  => "member",
                    "type"     => "user",
                    "template" => "point"
                );
            }

            //自定义配置
            $config = array(
                "username" => $username,
                "amount" => $reward_,
                "point" => $point,
                "date" => date("Y-m-d H:i:s", $now),
                "info" => $note_,
                "fields" => array(
                    'keyword1' => '变动时间',
                    'keyword2' => '变动积分',
                    'keyword3' => '积分余额'
                )
            );

            updateMemberNotice($userid, "会员-积分变动通知", $param, $config);

            //连签次数
            $sql = $dsql->SetQuery("SELECT `date` FROM `#@__member_qiandao` WHERE `uid` = $userid ORDER BY `date` DESC");
            $ret = $dsql->dsqlOper($sql, "results");
            $dateArr = array();
            if($ret){
                foreach ($ret as $key => $value) {
                    array_push($dateArr, $value['date']);
                }
            }
            $days = getContinueDay($dateArr);

            //总签次数
            $totalQiandao = 0;
            $sql = $dsql->SetQuery("SELECT `id`, `date` FROM `#@__member_qiandao` WHERE `uid` = $userid ORDER BY `date` DESC");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $totalQiandao += count($ret);
            }

            $uinfo = $userLogin->getMemberInfo();
            $point = $uinfo['point'];

            return array("days" => $days, "reward" => (int)$reward, "note" => $note_, "zongqian" => $totalQiandao, "point" => $point);

        }else{
            return array("state" => 200, "info" => $langData['siteConfig'][22][126]);  //签到失败！
        }

    }

    /**
     * 举报
     * @return array
     */
    public function complain(){
        global $dsql;
        global $userLogin;
        global $langData;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $module = $this->param['module'];
                $dopost = $this->param['dopost'];
                $type = $this->param['type'];
                $desc = $this->param['desc'];
                $phone = $this->param['phone'];
                $qq = $this->param['qq'];
                $aid = $this->param['aid'];
                $vdimgck = $this->param['vdimgck'];
                $cityid = $this->param['cityid'];
                $commonid = (int)$this->param['commonid'];
            }
        }

        $return = "";
        $type = htmlspecialchars(RemoveXSS(filterSensitiveWords(cn_substrR(addslashes($type), 50))));
        $desc = htmlspecialchars(RemoveXSS(filterSensitiveWords(cn_substrR(addslashes($desc), 200))));
        $phone = htmlspecialchars(RemoveXSS(filterSensitiveWords(cn_substrR(addslashes($phone), 50))));
        $qq = htmlspecialchars(RemoveXSS(filterSensitiveWords(cn_substrR(addslashes($qq), 50))));

        if(empty($cityid)){
            $cityid = getCityId();
        }

        if(empty($type) || empty($module) || empty($dopost) || empty($aid)){
            $return = array("state" => 200, "info" => self::$langData['siteConfig'][33][1]);//必填项不得为空！
        }

        if(empty($return)){
            if($vdimgck && strtolower($vdimgck) != $_SESSION['huoniao_vdimg_value']) $return = array("state" => 200, "info" => $langData['siteConfig'][20][99]);  //验证码输入错误，请重试！
        }

        if(empty($return)){
            //获取用户ID
            $uid    = $userLogin->getMemberID();
            $ip     = GetIP();
            $ipAddr = getIpAddr($ip);

            if($uid == -1){
                if(!empty($commonid)){
                    $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_complain` WHERE `module` = '$module' AND `action` = '$dopost' AND `aid` = '$aid' AND `commonid` = '$commonid' AND `ip` = '$ip'");
                }else{
                    $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_complain` WHERE `module` = '$module' AND `action` = '$dopost' AND `aid` = '$aid' AND `commonid` = '0' AND `ip` = '$ip'");
                }

                $count = $dsql->dsqlOper($archives, "totalCount");
            }else{
                if(!empty($commonid)){
                    $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_complain` WHERE `module` = '$module' AND `action` = '$dopost' AND `aid` = '$aid' AND `commonid` = '$commonid' AND `userid` = '$uid'");
                }else{
                    $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_complain` WHERE `module` = '$module' AND `action` = '$dopost' AND `aid` = '$aid' AND `commonid` = '0' AND `userid` = '$uid'");
                }
                $count = $dsql->dsqlOper($archives, "totalCount");
            }
            if($count == 0){
                $archives = $dsql->SetQuery("INSERT INTO `#@__member_complain` (`cityid`, `module`, `action`, `aid`, `type`, `desc`, `phone`, `qq`, `userid`, `ip`, `ipaddr`, `pubdate`, `state`, `commonid`) VALUES ('$cityid', '$module', '$dopost', '$aid', '$type', '$desc', '$phone', '$qq', '$uid', '$ip', '$ipAddr', ".GetMkTime(time()).", 0, '$commonid')");
                $dsql->dsqlOper($archives, "update");

                $return = $langData['siteConfig'][21][242];  //举报成功！
            }else{
                $return = array("state" => 101, "info" => $langData['siteConfig'][21][243]);  //您已经举报过此条信息，无需再次提交！
            }
        }

        return $return;
    }

    /**
     * 注册提交前验证
     */
    public function registAccountCheck(){
        global $dsql;
        global $langData;

        $param = $this->param;
        $rtype   = $param['rtype'];
        $account = $param['account'];
        $from    = $param['from'];
        $code    = $param['code'];

        if(empty($rtype) || empty($account)){
            return array("state" => 200, "info" => "empty");
        }

        //用户名
        if($rtype == 1){
            global $cfg_regstatus;
            global $cfg_regclosemessage;
            if($cfg_regstatus == 0){
                return array("state" => 200, "info" => $cfg_regclosemessage);
            }

            preg_match("/^[a-zA-Z]{1}[0-9a-zA-Z_]{4,15}$/iu", $account, $matchUsername);
            if(!$matchUsername){
                return array("state" => 200, "info" => $langData['siteConfig'][21][226]);
                //用户名格式有误！<br />英文字母、数字、下划线以内的5-20个字！<br />并且只能以字母开头！
            }
            if(!checkMember($account)){
                return array("state" => 200, "info" => $langData['siteConfig'][21][227]);
                //用户名已存在！
            }

            // 邮箱
        }elseif($rtype == 2){
            $archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `email` = '$account'");
            $return = $dsql->dsqlOper($archives, "results");
            if($return){
                return array("state" => 200, "info" => $langData['siteConfig'][21][230]);
                //此邮箱已被注册！
            }

            // 手机
        }elseif($rtype == 3){
            $areaCode = $param['areaCode'];
            if(empty($areaCode)){
                return array("state" => 200, "info" => "empty");
            }
            if($areaCode == "86"){
                preg_match('/0?1[0-9]{10}/', $account, $matchPhone);
                if(!$matchPhone){
                    return array("state" => 200, "info" => "error");
                }
            }

            $archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `phone` = '$account'");
            $return = $dsql->dsqlOper($archives, "results");
            if($return){
                // 绑定手机号
                if($from == "bind" && $code){
                    // 如果已绑定第三方账号，提示用户
                    if($return[0][$code.'_conn']){
                        return array("state" => 200, "info" => self::$langData['siteConfig'][33][38]);//"该手机号码已注册并绑定了第三方账号，如需将手机号绑定此第三方账号，请先用手机登陆，然后在安全中心进行解绑，然后再绑定此第三方账号！   "
                    }else{
                        return "ok";
                    }
                }else{
                    return array("state" => 200, "info" => $langData['siteConfig'][21][231]."   ");			//此手机号已被注册！
                }
            }
        }

        return "ok";
    }

    /**
     * 小程序登陆
     */
    public function miniProgramLogin(){
        global $dsql;
        global $cfg_miniProgramAppid;
        global $cfg_miniProgramAppsecret;

        $param     = $this->param;
        $code      = $param['code'];

        if(empty($code)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//格式错误！

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$cfg_miniProgramAppid."&secret=".$cfg_miniProgramAppsecret."&js_code=".$code."&grant_type=authorization_code";

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_HEADER,0);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);//证书检查
        // curl_setopt($curl,CURLOPT_POST,1);
        // curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        $result = curl_exec($curl);
        curl_close($curl);


        $data = json_decode($result);
        $data = objtoarr($data);

        $openid = $data['openid'];
        $UnionID = isset($data['unionid']) ? $data['unionid'] : "";
        $session_key = $data['session_key'];
        $field_session = $session_key.'#'.GetMktime(time());

        //保存到数据库中，以备支付时，没有取到unionid时使用
        $sql = $dsql->SetQUery("SELECT `id` FROM `#@__site_wxmini_unionid` WHERE `conn` = '$UnionID'");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret && is_array($ret)){
            $sql = $dsql->SetQuery("UPDATE `#@__site_wxmini_unionid` SET `openid` = '$openid', `unionid` = '$field_session' WHERE `conn` = '$UnionID'");
            $dsql->dsqlOper($sql, "update");
        }else{
            $sql = $dsql->SetQuery("INSERT INTO `#@__site_wxmini_unionid` (`conn`, `openid`, `unionid`) VALUES ('$UnionID', '$openid', '$field_session')");
            $dsql->dsqlOper($sql, "update");
        }

        // 自定义登陆态
        $state = array();
        // 是否新用户
        $newUser = 1;

        $uid = 0;

        // 获取到公众平台唯一标识
        // 如果用openid会创建第二个账号
        if($UnionID){
            $where = "`wechat_conn` = '$UnionID'";

            $sql = $dsql->SetQuery("SELECT `id`, `wechat_mini_openid` FROM `#@__member` WHERE ".$where);
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $newUser = 0;
                $uid = $ret[0]['id'];

                $field_openid = "";
                if(empty($ret[0]['wechat_mini_openid'])){
                    $field_openid = ", `wechat_mini_openid` = '$openid'";
                }

                $sql = $dsql->SetQuery("UPDATE `#@__member` SET `wechat_mini_session` = '$field_session' ".$field_openid." WHERE `id` = $uid");
                $dsql->dsqlOper($sql, "update");
            }
        }

        $state = array(
            0 => $openid,
            1 => $session_key
        );

        $RenrenCrypt = new RenrenCrypt();
        $key = base64_encode($RenrenCrypt->php_encrypt(join("@@@@", $state)));

        return array("key" => $key, "newUser" => $newUser);

    }

    /**
     * 小程序注册用户
     */
    public function miniProgramRegister(){
        global $dsql;
        global $cfg_miniProgramAppid;
        global $cfg_miniProgramAppsecret;

        $time   = GetMkTime(time());

        $param   = $this->param;
        $userkey = $param['userkey'];
        if(empty($userkey)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//格式错误！

        $RenrenCrypt = new RenrenCrypt();
        $userinfo = $RenrenCrypt->php_decrypt(base64_decode($userkey));
        $userinfo = explode('@@@@', $userinfo);

        $openid = $userinfo[0];
        $session_key = $userinfo[1];

        if(empty($openid) || empty($session_key)) return array("state" => 200, "info" => "参数错误！");

        $nickname    = $param['nickname'];
        $photo       = $param['photo'];
        $city        = $param['city'];
        $province    = $param['province'];
        $sex         = (int)$param['sex'];

        if(empty($nickname))  return array("state" => 200, "info" => "用户信息获取失败");

        $wechat_conn = "";
        $pic = "";
        $addr = 0;

        // unionid
        $iv            = $param['iv'];
        $encryptedData = $param['encryptedData'];
        if($iv && $encryptedData){
            include_once HUONIAOINC . "/class/miniProgram/wxBizDataCrypt.php";
            $pc = new WXBizDataCrypt($cfg_miniProgramAppid, $session_key);
            $errCode = $pc->decryptData($encryptedData, $iv, $data);

            if ($errCode == 0) {
                $data = json_decode($data);
                $data = objtoarr($data);
                $wechat_conn = $data['unionId'];
            }
        }

        if(empty($wechat_conn))  return array("state" => 200, "info" => "用户信息获取失败".$errCode);

        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `wechat_conn` = '$wechat_conn'");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            return array("state" => 200, "info" => "用户已注册");
        }

        // 区域
        if($city){
            $city = strtolower($city);
            $sql = $dsql->SetQuery("SELECT `id`, `parentid` FROM `#@__site_area` WHERE `pinyin` LIKE '%$city%'");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                if(count($ret) == 1 || empty($province)){
                    $addr = $ret[0]['id'];
                }else{
                    $province = strtolower($province);
                    foreach ($ret as $k => $v) {
                        $sql = $dsql->SetQuery("SELECT `pinyin` FROM `#@__site_area` WHERE `id` = ".$v['parentid']);
                        $ret = $dsql->dsqlOper($sql, "results");
                        if($ret){
                            if(strstr($ret[0]['pinyin'], $province) !== false){
                                $addr = $v['id'];
                                break;
                            }
                        }
                    }
                }
            }
        }

        $ip     = GetIP();
        $ipaddr = getIpAddr($ip);

        $chcode = strtolower(create_check_code(8));
        $username = $chcode."@wechat";

        //$sql = $dsql->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `photo`, `sex`, `addr`, `wechat_conn`, `wechat_openid`, `wechat_mini_openid`, `regtime`, `logincount`, `lastlogintime`, `lastloginip`, `lastloginipaddr`, `regip`, `regipaddr`, `state`) VALUES ('1', '$username', '', '$nickname', '$photo', '$sex', '$addr', '$wechat_conn', '', '$openid', '$time', '1', '$time', '$ip', '$ipaddr', '$ip', '$ipaddr', '1')");
        $session_key = $session_key.'#'.GetMktime(time());
        $sql = $dsql->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `photo`, `sex`, `addr`, `wechat_conn`, `wechat_openid`, `wechat_mini_openid`, `regtime`, `logincount`, `lastlogintime`, `lastloginip`, `lastloginipaddr`, `regip`, `regipaddr`, `state`, `wechat_mini_session`) VALUES ('1', '$username', '', '$nickname', '$photo', '$sex', '$addr', '$wechat_conn', '', '$openid', '$time', '1', '$time', '$ip', '$ipaddr', '$ip', '$ipaddr', '1', '$session_key')");
        $uid = $dsql->dsqlOper($sql, "lastid");

        if(is_numeric($uid)){
            return "注册成功！";
        }else{
            return array("state" => 200, "info" => "注册失败！");
        }

    }

    //绑定社交帐号-移除原有账号的绑定
    public function changeConnectBind(){
        global $dsql;
        global $userLogin;
        global $langData;
        $return = array();
        $uid = $userLogin->getMemberID();

        if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        $code     = $this->param['code'];
        $sameConn = $this->param['sameConn'];

        if(empty($code)) array("state" => 200, "info" => $langData['siteConfig'][21][72]);   //操作失败，请重试！
        if(empty($sameConn)) array("state" => 200, "info" => $langData['siteConfig'][21][72]);   //操作失败，请重试！

        $RenrenCrypt = new RenrenCrypt();
        $sameConn    = $RenrenCrypt->php_decrypt(base64_decode($sameConn));
        $sameConn    = explode("&", $sameConn);
        if(count($sameConn) != 2)  return array("state" => 200, "info" => $langData['siteConfig'][21][72]);   //操作失败，请重试！
        $sameConnId = $sameConn[0];
        $sameConnDa = $sameConn[1];

        $code_field = $code == "wechat" ? "`wechat_conn`, `wechat_openid`" : "`".$code."_conn`";
        $sql = $dsql->SetQuery("SELECT ".$code_field." FROM `#@__member` WHERE `id` = $sameConnId AND `".$code."_conn` = '$sameConnDa'");
        $ret = $dsql->dsqlOper($sql, "results");
        if(!$ret){
            return array("state" => 200, "info" => $langData['siteConfig'][21][72]."1");   //操作失败，请重试！
        }else{
            if($code == "wechat"){
                $up_field = "`wechat_conn` = '".$ret[0]['wechat_conn']."', `wechat_openid` = '".$ret[0]['wechat_openid']."'";
            }else{
                $up_field = "`".$code."_conn` = '".$ret[0][$code."_conn"]."'";
            }
            $sql = $dsql->SetQuery("UPDATE `#@__member` SET ".$up_field." WHERE `id` = ".$uid);
            $res = $dsql->dsqlOper($sql, "update");
            if($res == "ok"){
                if($code == "wechat"){
                    $up_field = "`wechat_conn` = '', `wechat_openid` = ''";
                }else{
                    $up_field = "`".$code."_conn` = ''";
                }
                $sql = $dsql->SetQuery("UPDATE `#@__member` SET ".$up_field." WHERE `id` = ".$sameConnId);
                $ret = $dsql->dsqlOper($sql, "update");
                return "ok";
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][72]."2");   //操作失败，请重试！
            }
        }

    }

    /**
     * 提现到支付宝、微信
     */
    public function putForward(){
        $param  =  $this->param;
        $module = $param['module'];
        $utype  = $param['utype'];
        $type   = $param['type'];
        $amount = $param['amount'];
        $date   = GetMkTime(time());

        global $dsql;
        global $userLogin;
        global $langData;
        $userid = $userLogin->getMemberID();
        if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

        if(empty($type) || empty($amount)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][30]);//请填写完整！



        // if($amount < 1) return array("state" => 200, "info" => self::$langData['siteConfig'][33][30]);//请填写完整！

        if($type == "alipay"){
            $tit = self::$langData['siteConfig'][19][302];//支付宝
            $conn = "alipay_conn";
        }elseif($type == "wxpay"){
            $tit = self::$langData['siteConfig'][27][139];//微信
            $conn = "wechat_openid";
        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][39]);//提现方式不存在
        }

        $this->param['id'] = $userid;
        $detail = $this->detail();
        // print_r($detail);die;

        if($detail['certifyState'] != 1) return array("state" => 200, "info" => self::$langData['siteConfig'][30][57]);//请先完成实名认证，再进行提现到支付宝、微信的操作
        if($detail[$conn] == '') return array("state" => 200, "info" => self::$langData['siteConfig'][33][40].$tit);

        if($module == "dating"){
            if($utype == "") return array("state" => 200, "info" => self::$langData['siteConfig'][33][30]);//请填写完整！

            $param = array("utype" => $utype);
            $moduleHandels = new handlers('dating', "putForward");
            $moduleConfig  = $moduleHandels->getHandle($param);
            if(is_array($moduleConfig) && $moduleConfig['state'] == 100){
                $datingDetail = $moduleConfig['info'];
            }else{
                return array("state" => 200, "info" => $langData['siteConfig'][21][72]);  //操作失败，请重试！
            }

            if($datingDetail['maxPutMoney'] < $amount) return array("state" => 200, "info" => $langData['siteConfig'][21][84]);  //帐户余额不足，提现失败！

        }else{
            return array("state" => 200, "info" => $langData['siteConfig'][21][72]);  //操作失败，请重试！
            if($detail['money'] < $amount) return array("state" => 200, "info" => $langData['siteConfig'][21][84]);  //帐户余额不足，提现失败！
        }

        $order = array(
            "account" => $detail[$conn],
            "amount" => $amount,
            "realname" => $detail['realname'],
            "remark" => '提现'
        );

        global $handler;
        $handler = true;

        $file = HUONIAOROOT."/api/payment/".$type."/".$type."_.php";
        require_once $file;
        $className = $type."Transfer";
        $transfer = new $className();
        $return = $transfer->transfer($order);

        if(is_array($return)){
            if($return['state'] == 100){
                $order_id = $return['order_id'];
                $paydate = $return['pay_date'];

                $sql = $dsql->SetQuery("INSERT INTO `#@__member_putforward` (`userid`, `utype`, `module`, `type`, `amount`, `pubdate`, `order_id`, `paydate`) VALUES ('$userid', '$utype', '$module', '$type', '$amount', '$date', '$order_id', '$paydate')");
                $dsql->dsqlOper($sql, "lastid");

                if($module == "dating"){
                    $moduleHandels = new handlers('dating', "config");
                    $moduleConfig  = $moduleHandels->getHandle();
                    $moduleConfig  = $moduleConfig['info'];
                    $goldRatio     = $moduleConfig['goldRatio'];
                    $withdrawRatio = $moduleConfig['withdrawRatio'];

                    // 扣除的金币 = 人民币 * 兑换比例 + 提现手续费
                    $money         = $amount * $goldRatio * (1 + $withdrawRatio / 100);

                    $sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` - $money WHERE `id` = ".$datingDetail['id']);
                    $dsql->dsqlOper($sql, "update");

                }else{
                    $sql = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - $amount WHERE `id` = $userid");
                    $dsql->dsqlOper($sql, "update");
                }

                return self::$langData['siteConfig'][33][41];//提现成功！
            }elseif($return['state'] == 101){
                return array("state" => 200, "info" => $return['info']);
            }else{
                return array("state" => 200, "info" => $return['info']);
            }
        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][42]);//提现失败，请稍后再试！
        }

    }


    /**
     * 短信登录
     */
    public function smsLogin(){
        global $userLogin;
        global $dsql;
        if($userLogin->getMemberID() > -1){
            return array('state' =>200, 'info' => self::$langData['siteConfig'][33][43]);//您已经登录，无须重复登录！

        }else{
            $ip = GetIP();
            $ipaddr = getIpAddr($ip);
            $areaCode = $this->param['areaCode'];
            $phone = $this->param['phone'];
            $vercode = $this->param['code'];
            $deviceTitle = $this->param['deviceTitle'];
            $deviceType = $this->param['deviceType'];
            $deviceSerial = $this->param['deviceSerial'];


            if($phone == ''){
                return array('state' =>200, 'info' => self::$langData['siteConfig'][20][463]);//请输入手机号码
            }

            //国际版需要验证区域码
            $cphone_ = $phone;
            $archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
            $results = $dsql->dsqlOper($archives, "results");
            if($results){
                $international = $results[0]['international'];
                if($international){
                    $cphone_ = $areaCode.$phone;
                }
            }

            //判断验证码
            $sql_code = $dsql->SetQuery("SELECT * FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'sms_login' AND `ip` = '$ip' AND `user` = '$cphone_' ORDER BY `id` DESC LIMIT 1");
            $res_code = $dsql->dsqlOper($sql_code, "results");
            if($res_code){
                $code = $res_code[0]['code'];
                if(strtolower($vercode) != $code){
                    return array('state' =>200, 'info' => self::$langData['siteConfig'][21][222]);//验证码输入错误，请重试！
                }
            }

            $isNewMember = 0;
            //判断是否是新用户
            $sql_mem = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `phone` = '$phone'");
            $res_mem = $dsql->dsqlOper($sql_mem, "results");

            $sql_mem_ = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `username` = '$phone'");
            $res_mem_ = $dsql->dsqlOper($sql_mem_, "results");

            $sourceArr = array(
                "title" => $deviceTitle,
                "type"  => $deviceType,
                "serial" => $deviceSerial,
                "pudate" => time()
            );

            //当手机号和用户名都不存在时才是新会员
            if(!$res_mem && !$res_mem_){
                $isNewMember = 1;
                //新用户手机注册
                //提供初始密码
                $passwdInit = '111111';
                $passwd = $userLogin->_getSaltedHash($passwdInit);
                $areaCode = 86;
                $mtype = 1;
                $times = time();
                //记录当前设备s
                if(!empty($deviceTitle) && !empty($deviceSerial) && !empty($deviceType)){
                    $sourceclient[] = $sourceArr;
                    $sourceclient   = serialize($sourceclient);
                }
                //记录当前设备e
                //保存到主表
                $nickname = preg_replace('/(1[34578]{1}[0-9])[0-9]{4}([0-9]{4})/is',"$1****$2", $phone);
                $archives = $dsql->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `areaCode`, `phone`, `phoneCheck`, `regtime`, `regip`, `regipaddr`, `state`, `purviews`, `sourceclient`)
VALUES ('$mtype', '$phone', '$passwd', '$nickname', '$areaCode', '$phone', '1', '$times', '$ip', '$ipaddr', '1', '', '$sourceclient')");
                $aid = $dsql->dsqlOper($archives, "lastid");

                if(is_numeric($aid)){
                    $userLogin->registGiving($aid);

                    //论坛同步
                    global $cfg_bbsState;
                    global $cfg_bbsType;
                    if($cfg_bbsState == 1 && $cfg_bbsType != ""){
                        $chcode = strtolower(create_check_code(8));
                        $data['username'] = $account;
                        $data['password'] = $passwd;
                        $data['email']    = $chcode."@qq.com";
                        $userLogin->bbsSync($data, "register");
                    }

                }else{
                    return array('state' => 200, 'info' => self::$langData['siteConfig'][21][235]);//注册失败，请稍候重试！
                }

            }
            $archives = $dsql->SetQuery("SELECT * FROM `#@__failedlogin` WHERE `ip` = '$ip'");
            $results = $dsql->dsqlOper($archives, "results");

            //登录前验证
            if($results){
                $count = $results[0]['count'];
                $timedifference = GetMkTime(time()) - $results[0]['date'];
                if($timedifference/60 < $loginTimes && $count >= $loginCount && $loginCount > 0 && $loginTimes > 0){
                    return array('state'=>200, 'info' => self::$langData['siteConfig'][21][223]);//您错误的次数太多，请1分钟后重试！
                    die;
                }
            }

            if($isNewMember){
                //新用户登录
                $newUser =  $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `id` = '$aid'");
                $newUserRes =  $dsql->dsqlOper($newUser, "results");
                $res = $userLogin->memberLoginCheckForSmsCode($newUserRes[0]);
            }else{
                $res = $userLogin->memberLoginCheckForSmsCode($res_mem ? $res_mem[0] : ($res_mem_ ? $res_mem_[0] : array()));
            }

            //登录成功
            if($res){

                //删除已验证过的验证码
                $sql_code = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'sms_login' AND `ip` = '$ip' AND `user` = '$cphone_' AND `code` = '$vercode'");
                $dsql->dsqlOper($sql_code, "update");

                $userid = $userLogin->getMemberID();

                //记录当前设备s
                $client = $res_mem ? $res_mem[0] : ($res_mem_ ? $res_mem_[0] : array());
                if($client['sourceclient']){
					$sourceclientAll = unserialize($client['sourceclient']);
                }
                $sourceclients = array();
				if(!empty($deviceTitle) && !empty($deviceSerial) && !empty($deviceType)){
					if(!empty($sourceclientAll)){
						$sourceclients = $sourceclientAll;
						//$foundTitle  = array_search($deviceTitle, array_column($sourceclientAll, 'title'));
						$foundSerial = array_search($deviceSerial, array_column($sourceclientAll, 'serial'));
						//$foundType   = array_search($deviceType, array_column($sourceclientAll, 'type'));
						if($foundSerial){
							//如果已有，更新时间，以Serial为准
							$sourceclients[$foundSerial]['pudate'] = time();
						}else{
							array_push($sourceclients, $sourceArr);
						}
					}else{
						$sourceclients[] = $sourceArr;
					}
					$sourceclients = serialize($sourceclients);

					$where_ = "`sourceclient` = '$sourceclients',";
                }
                //记录当前设备e

                $archives = $dsql->SetQuery("UPDATE `#@__member` SET $where_ `logincount` = `logincount` + 1, `lastlogintime` = ".GetMkTime(time()).", `lastloginip` = '".$ip."', `lastloginipaddr` = '".$ipaddr."' WHERE `id` = ".$userid);
                $dsql->dsqlOper($archives, "update");

                //保存到主表
                $archives = $dsql->SetQuery("INSERT INTO `#@__member_login` (`userid`, `logintime`, `loginip`, `ipaddr`) VALUES ('$userid', '".GetMkTime(time())."', '$ip', '$ipaddr')");
                $dsql->dsqlOper($archives, "update");

                $userinfo = $userLogin->getMemberInfo();

                if($isNewMember){
                    $userinfo['isNew'] = 1;
                }

                return $userinfo;
                // if($isNewMember){
                //     return $passwdInit;
                // }else{
                //     return 'success';
                // }
            }
        }
    }

    /**
     * 发布评价
     */
    public function sendComment(){
        global $dsql;
        global $userLogin;
        global $langData;

        $userid = $userLogin->getMemberID();

        if($userid == -1){
            return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！
        }

        $param   = $this->param;
        $type    = $param['type'];
        $check   = $param['check'];//是否ajax输出内容
        $pid     = (int)$param['pid'];
        $aid     = (int)$param['aid'];
        $oid     = (int)$param['oid'];
        $rating  = $type == 'shop-order' ? $param['rating'] : (int)$param['rating'];
        $sco1    = $type == 'shop-order' ? $param['sco1'] : (int)$param['sco1'];
        $sco2    = $type == 'shop-order' ? $param['sco2'] : (int)$param['sco2'];
        $sco3    = $type == 'shop-order' ? $param['sco3'] : (int)$param['sco3'];
        $content = $type == 'shop-order' ? $param['content'] : addslashes($param['content']);
        $audio   = $param['audio'];
        $pics    = $param['pics'];
        $video   = $param['video'];
        //外卖
        $peisongid= (int)$param['peisongid'];
        $star     = (int)$param['star'];
        $starps   = (int)$param['starps'];
        $contentps= $param['contentps'];
        $reply    = $param['reply'];
        $isanony  = (int)$param['isanony'];
        $vdimgck  = filterSensitiveWords($param['vdimgck']);

        $ip     = GetIP();
        $ipaddr = getIpAddr($ip);
        $dtime  = GetMkTime(time());
        $replydate = GetMkTime(time());

        if($type == 'paotui-order' || $type == 'waimai-order'){
            $module = 'waimai';
        }else{
            $typeArr = explode('-', $type);
            $module  = $typeArr[0];
        }
        include HUONIAOINC."/config/".$module.".inc.php";
        $ischeck = (int)$customCommentCheck;

        if(empty($type) || empty($aid)){
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误
        }
        // if(empty($rating)) return array("state" => 200, "info" => "请选择总体满意度");
        if(($type!='quanjing-detail' && $type!='info-detail' && $type!='info-business' && $type!='travel-ticket' && $type!='travel-video' && $type!='travel-strategy' && $type!='travel-visa' && $type!='travel-agency' && $type!='marry-store' && $type!='marry-rental' && $type!='article-detail' && $type!='waimai-order' && $type!='paotui-order' && $type!='video-detail' && $type!='tieba-detail') && empty($sco1)) return array("state" => 200, "info" => self::$langData['siteConfig'][19][334]);
        //请选择评分

        if(!isMobile() && $type=='tieba-detail'){
			$vdimgck = strtolower($vdimgck);
			if($vdimgck != $_SESSION['huoniao_vdimg_value']) return array("state" => 200, "info" => '验证码输入错误');
		}

        // if(empty($content) && empty($audio) && empty($pics) && empty($video)){
        // 	return array("state" => 200, "info" => "评价内容为空");
        // }

        if($type == 'waimai-order'){
            if (empty($star)) return array("state" => 200, "info" => '请给店铺打分！');
            if (empty($starps)) return array("state" => 200, "info" => '请给配送员打分！');
        }elseif($type == 'paitui-order'){
            if (empty($starps)) return array("state" => 200, "info" => '请给配送员打分！');
            $star = 0;
        }else{
            $star = 0;
        }

        $time = 0;
        //查询信息 获取原来的发布人ID
        $masterid = 0;
        if($type == 'business'){
            $archives = $dsql->SetQuery("SELECT `uid` FROM `#@__business_list` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['uid'];
            }
        }elseif($type == 'pension-store' && $module='pension'){
            $archives = $dsql->SetQuery("SELECT `userid` FROM `#@__".$module."_store` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['userid'];
            }
        }elseif($type == 'info-business'){
            $archives = $dsql->SetQuery("SELECT `uid` FROM `#@__infoshop` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['uid'];
            }
        }elseif($type == 'info-detail'){
            $archives = $dsql->SetQuery("SELECT `userid` FROM `#@__infolist` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['userid'];
            }
        }elseif($type == 'huodong-detail'){
            $archives = $dsql->SetQuery("SELECT `uid` FROM `#@__huodong_list` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['uid'];
            }
        }elseif($type == 'article-detail'){
            $archives = $dsql->SetQuery("SELECT `admin` FROM `#@__articlelist_all` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['admin'];
            }
        }elseif($type == 'tieba-detail'){
            $archives = $dsql->SetQuery("SELECT `uid` FROM `#@__tieba_list` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['uid'];
            }
        }elseif($type == 'video-detail'){
            $archives = $dsql->SetQuery("SELECT `admin` FROM `#@__videolist` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['admin'];
            }
        }elseif($type == 'quanjing-detail'){
            $archives = $dsql->SetQuery("SELECT `admin` FROM `#@__quanjinglist` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['admin'];
            }
        }elseif($type == 'marry-store'){
            $archives = $dsql->SetQuery("SELECT `userid` FROM `#@__marry_store` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['userid'];
            }
        }elseif($type == 'marry-rental'){
            $archives = $dsql->SetQuery("SELECT `company` FROM `#@__marry_weddingcar` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $company = $results[0]['company'];
                $sql = $dsql->SetQuery("SELECT `id`, `userid` FROM `#@__marry_store` WHERE `id` = '$company'");
                $res = $dsql->dsqlOper($sql, "results");
                if($res && is_array($res)){
                    $masterid = $res[0]['userid'];
                }
            }
        }elseif($type == 'travel-store'){
            $archives = $dsql->SetQuery("SELECT `userid` FROM `#@__travel_store` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $masterid = $results[0]['userid'];
            }
        }elseif($type == 'travel-ticket'){
            $archives = $dsql->SetQuery("SELECT `company` FROM `#@__travel_ticket` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $company = $results[0]['company'];
                $sql = $dsql->SetQuery("SELECT `id`, `userid` FROM `#@__travel_store` WHERE `id` = '$company'");
                $res = $dsql->dsqlOper($sql, "results");
                if($res && is_array($res)){
                    $masterid = $res[0]['userid'];
                }
            }
        }elseif($type == 'travel-video'){
            $archives = $dsql->SetQuery("SELECT `usertype`, `userid` FROM `#@__travel_video` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                if($results[0]['usertype'] == 1){
                    $company = $results[0]['userid'];
                    $sql = $dsql->SetQuery("SELECT `id`, `userid` FROM `#@__travel_store` WHERE `id` = '$company'");
                    $res = $dsql->dsqlOper($sql, "results");
                    if($res && is_array($res)){
                        $masterid = $res[0]['userid'];
                    }
                }else{
                    $masterid = $results[0]['userid'];
                }
            }
        }elseif($type == 'travel-strategy'){
            $archives = $dsql->SetQuery("SELECT `usertype`, `userid` FROM `#@__travel_strategy` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                if($results[0]['usertype'] == 1){
                    $company = $results[0]['userid'];
                    $sql = $dsql->SetQuery("SELECT `id`, `userid` FROM `#@__travel_store` WHERE `id` = '$company'");
                    $res = $dsql->dsqlOper($sql, "results");
                    if($res && is_array($res)){
                        $masterid = $res[0]['userid'];
                    }
                }else{
                    $masterid = $results[0]['userid'];
                }
            }
        }elseif($type == 'travel-visa'){
            $archives = $dsql->SetQuery("SELECT `company` FROM `#@__travel_visa` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $company = $results[0]['company'];
                $sql = $dsql->SetQuery("SELECT `id`, `userid` FROM `#@__travel_store` WHERE `id` = '$company'");
                $res = $dsql->dsqlOper($sql, "results");
                if($res && is_array($res)){
                    $masterid = $res[0]['userid'];
                }
            }
        }elseif($type == 'travel-agency'){
            $archives = $dsql->SetQuery("SELECT `company` FROM `#@__travel_agency` WHERE `id` = '$aid'");
            $results = $dsql->dsqlOper($archives, "results");
            if($results && is_array($results)){
                $company = $results[0]['company'];
                $sql = $dsql->SetQuery("SELECT `id`, `userid` FROM `#@__travel_store` WHERE `id` = '$company'");
                $res = $dsql->dsqlOper($sql, "results");
                if($res && is_array($res)){
                    $masterid = $res[0]['userid'];
                }
            }
        }elseif($type == 'tuan-store'){

            if(empty($content) || strlen($content) < 15) return array("state" => 200, "info" => '请输入评价内容，最少15个字！');

            $sql = $dsql->SetQuery("SELECT `id`, `uid` FROM `#@__tuan_store` WHERE `id` = '$aid'");
            $res = $dsql->dsqlOper($sql, "results");
            if(empty($res)){
                return array("state" => 200, "info" => '商家不存在，评价失败！');
            }else{
                $masterid = $res[0]['uid'];
                if($res[0]['uid'] == $userid) return array("state" => 200, "info" => '自家店铺不能评论！');
            }
        }elseif($type == 'tuan-order'){
            $sql = $dsql->SetQuery("SELECT `userid`, `orderstate`, `proid`, `id` FROM `#@__tuan_order` WHERE `id` = '$aid'");
            $order = $dsql->dsqlOper($sql, "results");
            if($order){
                $oid = $order[0]['id'];
                $aid = $order[0]['proid'];

                $archives = $dsql->SetQuery("SELECT `sid` FROM `#@__tuanlist` WHERE `id` = '$aid'");
                $results = $dsql->dsqlOper($archives, "results");
                if($results && is_array($results)){
                    $sid = $results[0]['sid'];
                    $sql = $dsql->SetQuery("SELECT `id`, `uid` FROM `#@__tuan_store` WHERE `id` = '$sid'");
                    $res = $dsql->dsqlOper($sql, "results");
                    if($res && is_array($res)){
                        $masterid = $res[0]['uid'];
                    }
                }

                if($order[0]['userid'] != $userid) return array("state" => 200, "info" => '验证账户权限失败，请使用购买过此商品的账号登录后再评价！');
                if($order[0]['orderstate'] != 3) return array("state" => 200, "info" => '订单当前状态不可评价，请使用后再评价！');
            }else{
                return array("state" => 200, "info" => '订单不存在，评价失败！');
            }
        }elseif($type == 'shop-order'){
            $orderHandels = new handlers("shop", "orderDetail");
            $order        = $orderHandels->getHandle($aid);
            if(is_array($order) && $order['state'] == 100){
                $order  = $order['info'];

                if($order['userid'] != $userid) return array("state" => 200, "info" => '验证账户权限失败，请使用购买过此商品的账号登录后再评价！');
                if($order['orderstate'] != 3) return array("state" => 200, "info" => '订单当前状态不可评价，请使用后再评价！');

                foreach($order['product'] as $key => $value){

                    $pid   = $value['id'];
                    $proid = $value['proid'];
                    $orderid = $value['orderid'];
                    $speid = $value['speid'];
                    $specation = $value['specation'];
                    $rating  = $rating[$pid."_".$speid];
                    $sco1 = $sco1[$pid."_".$speid];
                    $sco2 = $sco2[$pid."_".$speid];
                    $sco3 = $sco3[$pid."_".$speid];
                    $content  = $content[$pid."_".$speid];
                    $pics   = $pics[$pid."_".$speid];

                    if(empty($rating)) return array("state" => 200, "info" => $langData['shop'][4][29]);  //请选择商品评价！
                    if(empty($sco1)) return array("state" => 200, "info" => $langData['shop'][4][30]);  //请给商品描述打分
                    if(empty($sco2)) return array("state" => 200, "info" => $langData['shop'][4][31]);  //请给商家服务打分
                    if(empty($sco3)) return array("state" => 200, "info" => $langData['shop'][4][32]);  //请给商品质量打分
                    if(empty($content)) return array("state" => 200, "info" => $langData['shop'][4][33]);  //请输入评价内容！

                    $archives = $dsql->SetQuery("SELECT `store` FROM `#@__shop_product` WHERE `id` = '$proid'");
                    $results = $dsql->dsqlOper($archives, "results");
                    if($results && is_array($results)){
                        $store = $results[0]['store'];
                        $sql = $dsql->SetQuery("SELECT `id`, `userid` FROM `#@__shop_store` WHERE `id` = '$store'");
                        $res = $dsql->dsqlOper($sql, "results");
                        if($res && is_array($res)){
                            $masterid = $results[0]['userid'];
                        }
                    }
                    //修改
                    if($order['common'] == 1){
                        $sql = $dsql->SetQuery("UPDATE `#@__public_comment` SET `masterid` = '$masterid', `ischeck` = '$ischeck', `ipaddr` = '$ipaddr', `ip` = '$ip', `dtime` = '$dtime', `content` = '$content', `pics` = '$pics', `sco3` = '$sco3', `sco2` = '$sco2', `sco1` = '$sco1', `rating` = '$rating' WHERE `oid` = '$orderid'");
                        $results  = $dsql->dsqlOper($sql, "update");

                    //新增
                    }else{
                        $sql = $dsql->SetQuery("INSERT INTO `#@__public_comment` (`pid`, `type`, `aid`, `oid`, `userid`, `rating`, `sco1`, `content`, `pics`, `audio`, `video`, `dtime`, `ip`, `ipaddr`, `ischeck`, `zan`, `zan_user`, `sco2`, `sco3`, `speid`, `specation`, `masterid`) VALUES ('0', '$type', '$proid', '$orderid', '$userid', '$rating', '$sco1', '$content', '$pics', '$audio', '$video', '$dtime', '$ip', '$ipaddr', '$ischeck', '0', '', '$sco2', '$sco3', '$speid', '$specation', '$masterid')");
                        $dsql->dsqlOper($sql, "update");
                    }
                }

                $archives = $dsql->SetQuery("UPDATE `#@__shop_order` SET `common` = 1 WHERE `id` = '$aid'");
                $dsql->dsqlOper($archives, "update");
                return $langData['siteConfig'][20][196];  //评价成功！

            }else{
                return array("state" => 200, "info" => '订单不存在，评价失败！');
            }
        }elseif($type == 'waimai-order'){

            $sql = $dsql->SetQuery("SELECT `id`, `sid`, `iscomment`, `uid`, `peisongid`, `paydate`, `okdate` FROM `#@__waimai_order` WHERE `id` = $aid");
            $ret = $dsql->dsqlOper($sql, "results");
            if ($ret) {
                $aid       = $ret[0]['sid'];//商铺
                $oid       = $ret[0]['id'];//订单
                $peisongid = $ret[0]['peisongid'];
                $paydate   = $ret[0]['paydate'];
                $okdate    = $ret[0]['okdate'];
                $time      = ceil(($okdate - $paydate) / 60);
                $sql = $dsql->SetQuery("SELECT `userid` FROM `#@__waimai_store` WHERE `id` = " . $ret[0]['sid']);
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $masterid  = $ret[0]['userid'];
                }
            } else {
                return array("state" => 200, "info" => '订单不存在！');
            }

        }elseif($type == 'paotui-order'){

            $sql = $dsql->SetQuery("SELECT `id`, `iscomment`, `peisongid`, `paydate`, `okdate` FROM `#@__paotui_order` WHERE `id` = $aid");
            $ret = $dsql->dsqlOper($sql, "results");
            if ($ret) {
                $peisongid = $ret[0]['peisongid'];
                $oid       = $ret[0]['id'];//订单
                $aid       = 0;
                $peisongid = $ret[0]['peisongid'];
                $paydate   = $ret[0]['paydate'];
                $okdate    = $ret[0]['okdate'];
                $time      = ceil(($okdate - $paydate) / 60);
            } else {
                return array("state" => 200, "info" => '订单不存在！');
            }

        }

        if($aid && $pid){
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `type` = '$type' AND `aid` = $aid AND `pid` = 0");
            $ret = $dsql->dsqlOper($sql, "results");
            if(!$ret){
                $pid = 0;
            }
        }

        $type_ = explode('_', $type);
        $module = $type_[0];

        if(count($type_) == 2){

            // 商品类验证订单并且只能评价一次，没有回复功能
            if($type_[1] == "goods"){

                $pid = 0;

                if(empty($oid)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误


                // 商城
                if($module == "shop"){
                    $sql = $dsql->SetQuery("SELECT o.`id` FROM `#@__shop_order` o LEFT JOIN `#@__shop_order_product` p ON p.`orderid` = o.`id` WHERE o.`id` = $oid AND o.`userid` = $userid AND p.`proid` = $aid");
                    $ret = $dsql->dsqlOper($sql, "results");


                    // 团购
                }elseif($module == "tuan"){
                    $sql = $dsql->SetQuery("SELECT o.`id` FROM `#@__tuan_order` o WHERE o.`id` = $oid AND o.`userid` = $userid AND o.`proid` = $aid");

                }elseif($module == "waimai"){
                    $sql = $dsql->SetQuery("SELECT o.`id` FROM `#@__waimai_order` o LEFT JOIN `#@__waimai_order_product` p ON p.`orderid` = o.`id` WHERE o.`id` = $oid AND o.`userid` = $userid AND p.`pid` = $aid");
                }
                if(!$ret){
                    return array("state" => 200, "info" => "订单信息错误");
                }
                // 验证评价是否已存在
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `aid` = '$aid' AND `oid` = '$oid' AND `userid` = '$userid' AND `type` = '$type'");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    return array("state" => 200, "info" => "您已经评价过该商品");
                }

                // 店铺类验证店铺是否存在
            }elseif($type_[1] == "store"){
                $oid = 0;
            }
        }



        //查询评价信息 团购、外卖需要这一步，其他的不需要
        $commentid = 0;
        if($type == 'tuan-order' || $type == 'waimai-order' || $type == 'paotui-order'){
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `type` = '$type' and `oid` = '$oid'");
            $res = $dsql->dsqlOper($sql, "results");
            $commentid = $res[0]['id'];
        }

        if($commentid > 0 && !empty($oid)){
            $sql = $dsql->SetQuery("UPDATE `#@__public_comment` SET `masterid` = '$masterid', `ischeck` = '$ischeck', `ipaddr` = '$ipaddr', `ip` = '$ip', `dtime` = '$dtime', `content` = '$content', `pics` = '$pics', `sco3` = '$sco3', `sco2` = '$sco2', `sco1` = '$sco1', `rating` = '$rating', `peisongid` = '$peisongid', `star` = '$star', `starps` = '$starps', `contentps` = '$contentps', `isanony` = '$isanony' WHERE `oid` = '$oid'");
            $results  = $dsql->dsqlOper($sql, "update");
            if($results == "ok"){
                $ret = $commentid;
            }else{
                $ret = null;
            }
        }else{
            $sql = $dsql->SetQuery("INSERT INTO `#@__public_comment` (`masterid`, `pid`, `type`, `aid`, `oid`, `userid`, `rating`, `sco1`, `content`, `pics`, `audio`, `video`, `dtime`, `ip`, `ipaddr`, `ischeck`, `zan`, `zan_user`, `sco2`, `sco3`, `speid`, `specation`, `peisongid`, `star`, `starps`, `contentps`, `isanony`, `time`) VALUES ('$masterid', '$pid', '$type', '$aid', '$oid', '$userid', '$rating', '$sco1', '$content', '$pics', '$audio', '$video', '$dtime', '$ip', '$ipaddr', '$ischeck', '0', '', '$sco2', '$sco3', '$speid', '$specation', '$peisongid', '$star', '$starps', '$contentps', '$isanony', '$time')");
            $ret = $dsql->dsqlOper($sql, "lastid");
        }

        if(is_numeric($ret)){

            if($type == 'tuan-order'){
                $archives = $dsql->SetQuery("UPDATE `#@__tuan_order` SET `common` = 1 WHERE `id` = '$oid'");
			    $dsql->dsqlOper($archives, "update");
            }elseif($type == 'waimai-order'){
                $sql = $dsql->SetQuery("UPDATE `#@__waimai_order` SET `iscomment` = 1 WHERE `id` = $oid");
                $dsql->dsqlOper($sql, "update");
            }elseif($type == 'paotui-order'){
                $sql = $dsql->SetQuery("UPDATE `#@__paotui_order` SET `iscomment` = 1 WHERE `id` = $oid");
                $dsql->dsqlOper($sql, "update");
            }

            if($check){
                $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `zan`, `zan_user`, `ischeck` FROM `#@__public_comment` WHERE `id` = " . $ret);
                $results  = $dsql->dsqlOper($archives, "results");
                if ($results) {
                    $list['id']       = $results[0]['id'];
                    $list['userinfo'] = $userLogin->getMemberInfo($results[0]['userid']);
                    $list['content']  = $results[0]['content'];
                    $list['dtime']    = $results[0]['dtime'];
                    $list['ftime']    = GetMkTime(time()) - $results[0]['dtime'] > 30 ? $results[0]['dtime'] : FloorTime(GetMkTime(time()) - $results[0]['dtime']);
                    $list['ip']       = $results[0]['ip'];
                    $list['ipaddr']   = $results[0]['ipaddr'];
                    $list['zan']      = $results[0]['zan'];
                    $list['ischeck']  = $results[0]['ischeck'];
                    return $list;
                }
            }
            return self::$langData['siteConfig'][20][196];//评价成功！
        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][44]);//评价失败
        }

    }

    /**
     * 回复评价
     */
    public function replyComment(){
        global $dsql;
        global $userLogin;

        $userid = $userLogin->getMemberID();
        if($userid == -1){
            return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！
        }

        $param   = $this->param;
        $id      = (int)$param['id']; // 原评论id
        $check   = $param['check'];//是否ajax输出内容
        $content = $param['content'];

        if(empty($id)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误
        if(empty($content)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][45]);//请填写回复内容

        $sid = $id;

        $sql = $dsql->SetQuery("SELECT `id`, `pid`, `rid`, `aid`, `type`, `masterid` FROM `#@__public_comment` WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            // 回复一级评论
            if($ret[0]['pid'] == 0){
                $rid = 0;
                $parent = $ret[0];
            }else{
                $sql = $dsql->SetQuery("SELECT `id`, `pid`, `rid`, `aid`, `type` FROM `#@__public_comment` WHERE `id` = ".$ret[0]['pid']);
                $res = $dsql->dsqlOper($sql, "results");
                if($res){
                    $rid = $ret[0]['rid'] ? $ret[0]['rid'] : $ret[0]['id'];
                    $parent = $res[0];
                }
            }
            $pid  = $parent['id'];
            $type = $parent['type'];
            $aid  = $parent['aid'];
            $oid  = $parent['oid'];
            $masterid  = $ret[0]['masterid'];

            $ip     = GetIP();
            $ipaddr = getIpAddr($ip);
            $dtime  = GetMkTime(time());

            if($type == 'paotui-order' || $type == 'waimai-order'){
                $module = 'waimai';
            }else{
                $typeArr = explode('-', $type);
                $module  = $typeArr[0];
            }
            include HUONIAOINC."/config/".$module.".inc.php";
            $ischeck = (int)$customCommentCheck;

            $sco1 = 0;
            $rating = 0;
            $isanony = 0;
            $oid = $oid ? $oid : 0;
            $pics = $audio = $video = "";

            $sql = $dsql->SetQuery("INSERT INTO `#@__public_comment` (`masterid`, `pid`, `type`, `aid`, `oid`, `userid`, `rating`, `sco1`, `content`, `pics`, `audio`, `video`, `dtime`, `ip`, `ipaddr`, `ischeck`, `zan`, `zan_user`, `isanony`, `rid`, `sid`) VALUES ('$masterid', '$pid', '$type', '$aid', '$oid', '$userid', '$rating', '$sco1', '$content', '$pics', '$audio', '$video', '$dtime', '$ip', '$ipaddr', '$ischeck', '0', '', '$isanony', '$rid', '$sid')");
            $ret = $dsql->dsqlOper($sql, "lastid");
            if(is_numeric($ret)){
                if($check){
                    $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `zan`, `zan_user`, `ischeck` FROM `#@__public_comment` WHERE `id` = " . $ret);
                    $results  = $dsql->dsqlOper($archives, "results");
                    if ($results) {
                        $list['id']       = $results[0]['id'];
                        $list['userinfo'] = $userLogin->getMemberInfo($results[0]['userid']);
                        $list['content']  = $results[0]['content'];
                        $list['dtime']    = $results[0]['dtime'];
                        $list['ftime']    = GetMkTime(time()) - $results[0]['dtime'] > 30 ? $results[0]['dtime'] : FloorTime(GetMkTime(time()) - $results[0]['dtime']);
                        $list['ip']       = $results[0]['ip'];
                        $list['ipaddr']   = $results[0]['ipaddr'];
                        $list['zan']      = $results[0]['zan'];
                        $list['ischeck']  = $results[0]['ischeck'];
                        return $list;
                    }
                }
                return self::$langData['siteConfig'][20][196];//评价成功！
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][44]);//评价失败
            }

        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][46]);//原评价不存在
        }

    }

    /**
     * 评论点赞
     */
    public function dingComment(){
        global $dsql;
        global $userLogin;

        $userid = $userLogin->getMemberID();
        if($userid == -1){
            return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！
        }

        $param   = $this->param;
        $id      = (int)$param['id'];
        $type    = $param['type'];

        if(empty($id)){
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误
        }

        // 评论信息
        $sql = $dsql->SetQuery("SELECT `userid`, `zan_user`, `type` FROM `#@__public_comment` WHERE `id` = $id AND `ischeck` = 1");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){

            if($userid == $ret[0]['userid']){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][47]);//自己不能给自己点赞哦~
            }

            $zan_ip = $ret[0]['zan_ip'];
            $zan_user = $ret[0]['zan_user'];
            $action     = $ret[0]['type'];

            $zan_user_arr = $zan_user ? explode(',', $zan_user) : array();

            $ip = GetIP();

            if($action!='paotui-order' || $action!='waimai-order'){
                $actArr = explode('-', $action);
                $module = $actArr[0];
                $temp   = $action;
            }else{
                $module = 'waimai';
                $temp   = $action;
            }


            if($type == "add"){
                if(in_array($userid, $zan_user_arr)){
                    return array("state" => 200, "info" => self::$langData['siteConfig'][33][48]);//您已经赞过
                }
                $zan_user_arr[] = $userid;
            }else{
                $k = array_search($userid, $zan_user_arr);
                if($k === false) return self::$langData['siteConfig'][20][244];//操作成功;
                unset($zan_user_arr[$k]);
            }

            $param['id']     = $id;
            $param['uid']    = $ret[0]['userid'];
            $param['module'] = $module;
            $param['temp']   = $temp;
            $param['type']   = 1;
            $this->param     = $param;
            $this->getZan();



            $sql = $dsql->SetQuery("UPDATE `#@__public_comment` SET `zan` = ".count($zan_user_arr).", `zan_user` = '".join(",", $zan_user_arr)."' WHERE `id` = $id");
            $ret = $dsql->dsqlOper($sql, "update");
            if($ret == "ok"){
                return self::$langData['siteConfig'][20][244];//操作成功;
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][21][72]);//操作失败，请重试！
            }

        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][33][46]);//原评价不存在

        }

    }


    /**
     * 获取评价列表
     */
    public function getComment(){
        global $dsql;
        global $userLogin;
        global $langData;

        $pageinfo = array();

        $userid = $userLogin->getMemberID();

        $param    = $this->param;
        $type     = $param['type'];
        $pid      = (int)$param['pid'];
        $aid      = (int)$param['aid'];
        $oid      = (int)$param['oid'];
        $rating   = (int)$param['rating'];
        $sco1     = $param['sco1'];
        $content  = (int)$param['content'];
        $audio    = (int)$param['audio'];
        $pics     = (int)$param['pics'];
        $video    = (int)$param['video'];
        $son      = (int)$param['son'];
        $u        = (int)$param['u'];
        $onlyself = (int)$param['onlyself'];
        $isAjax   = (int)$param['isAjax'];
        $filter   = $param['filter'];
        $orderby  = $param['orderby'];
        $uid      = (int)$param['uid'];
        $peisongid= (int)$param['peisongid'];//配送员
        $page     = (int)$param['page'];
        $pageSize = (int)$param['pageSize'];

        $where1 = '';

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if (!$peisongid && $u!=1) {
            if(empty($type) || empty($aid)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误
            }

            $where = " AND `type` = '$type' AND `aid` = '$aid' AND `pid` = '$pid'";
        }

        if($u==1){//只调取别人的评论自己的评论;
            $where1 .= " AND `userid` = '$userid'";
            if($onlyself==1){
                $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1".$where1);
                $ret = $dsql->dsqlOper($sql, "results");
                $sidList = array();
				foreach ($ret as $k => $v) {
					array_push($sidList, $v['id']);
                }
                if(!empty($sidList)){
                    $where .= " AND  (`sid` in(".join(',',$sidList).") or (`masterid` = '$userid' AND `sid` = '0'))";
                }else{
                    $where .= " AND  `masterid` = '$userid' AND `sid` = '0'";
                }
            }
        }

        //指定会员ID 帖子
		if(!empty($uid)){
			$where .= " AND `userid` = ".$uid;
		}

        if($oid){
            $where .= " AND `oid` = '$oid'";
        }

        if ($peisongid) {
            require_once(HUONIAOROOT . "/api/handlers/waimai.controller.php");
            $peisongid = $peisongid == 1 ? checkCourierAccount() : $peisongid;
            $where     .= " AND `peisongid` = $peisongid";
        }

        //筛选
		if(!empty($filter)){
			if($filter == "pic"){
				$where .= " AND `pics` <> ''";
			}elseif($filter == "lower"){
				$where .= " AND `rating` < 3";
            }elseif($filter == "h"){
				$where .= " AND `rating` = 1";
			}elseif($filter == "z"){
				$where .= " AND `rating` = 2";
			}elseif($filter == "c"){
				$where .= " AND `rating` = 3";
			}
        }
        
        if($sco1){
            $sco1_a = explode(',', $sco1);
            $where1 = array();
            foreach ($sco1_a as $k => $v) {
                $where1[$k] = "`sco1` = $v";
            }
            $where .= " AND (".join(" || ", $where1).")";
        }
        if($content){
            $where .= " AND `content` != ''";
        }
        if($audio){
            $where .= " AND `audio` != ''";
        }
        if($pics){
            $where .= " AND `pics` != ''";
        }
        if($video){
            $where .= " AND `video` != ''";
        }

        if(!$isAjax){
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1".$where);
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

            // 好中差评
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `sco1` = 1".$where);
            $sco1_ = $dsql->dsqlOper($sql, "totalCount");
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `sco1` = 2".$where);
            $sco2_ = $dsql->dsqlOper($sql, "totalCount");
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `sco1` = 3".$where);
            $sco3_ = $dsql->dsqlOper($sql, "totalCount");
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `sco1` = 4".$where);
            $sco4_ = $dsql->dsqlOper($sql, "totalCount");
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `sco1` = 5".$where);
            $sco5_ = $dsql->dsqlOper($sql, "totalCount");

            $pageinfo['sco1'] = $sco1_;
            $pageinfo['sco2'] = $sco2_;
            $pageinfo['sco3'] = $sco3_;
            $pageinfo['sco4'] = $sco4_;
            $pageinfo['sco5'] = $sco5_;

            // 带图片的
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `pics` != ''".$where);
            $res = $dsql->dsqlOper($sql, "totalCount");

            $pageinfo['pic'] = $res;
        }



        

        $atpage = $pageSize*($page-1);
        $where_limit = " ORDER BY `id` DESC LIMIT $atpage, $pageSize";
        if ($orderby == "hot") {
            $where_limit = " ORDER BY `zan` DESC, `id` DESC LIMIT $atpage, $pageSize";
        }elseif($orderby == "1"){
            $where_limit = " ORDER BY `id` ASC LIMIT $atpage, $pageSize";
        }

        $archives = $dsql->SetQuery("SELECT `id`, `pid`, `sid`, `type`, `ipaddr`, `aid`, `oid`, `userid`, `rating`, `sco1`, `content`, `pics`, `audio`, `video`, `dtime`, `ip`, `zan`, `zan_user`, `isanony`, `specation`, `peisongid`, `star`, `starps`, `contentps`, `reply`, `replydate`, `time` FROM `#@__public_comment` WHERE `ischeck` = 1".$where.$where_limit);
        $results = $dsql->dsqlOper($archives, "results");

        $list = array();

        if(is_array($results) && !empty($results)){
            $sco1Txt = self::$langData['siteConfig'][34][9];//array(0 => "", 1 => "失望", 2 => "一般", 3 => "还行", 4 => "满意", 5 => "超赞")
            foreach ($results as $key => $value) {
                if($u==1){//只调取别人的评论自己的评论,取上一级评论;
                    if($onlyself==1){
                        if(!empty($value['sid'])){
                            $sql = $dsql->SetQuery("SELECT `id`, `content` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `id` = ".$value['sid']);
                            $ret = $dsql->dsqlOper($sql, "results");
                            if(!empty($ret[0]['id'])){
                                $list[$key]['parent']['id']      = $ret[0]['id'];
                                $list[$key]['parent']['content']      = $ret[0]['content'];
                            }
                        }
                    }
                }

                $list[$key]['id']      = $value['id'];
                $list[$key]['sid']     = $value['sid'];
                $list[$key]['pid']     = $value['pid'];
                $list[$key]['type']    = $value['type'];
                $list[$key]['oid']     = $value['oid'];
                $list[$key]['ipaddr']  = $value['ipaddr'];
                $list[$key]['specation']= str_replace("$$$", "，", $value['specation']);
                $list[$key]['isanony'] = $value['isanony'];
                $list[$key]['ftime']    = floor((GetMkTime(time()) - $value['dtime'] / 86400) % 30) > 30 ? date("Y-m-d", $value['dtime']) : FloorTime(GetMkTime(time()) - $value['dtime']);

                $list[$key]['peisongid']  = $value['peisongid'];
                $list[$key]['star']       = $value['star'];
                $list[$key]['starps']     = $value['starps'];
                $list[$key]['contentps']  = $value['contentps'];
                $list[$key]['reply']      = $value['reply'];
                $list[$key]['time']       = $value['time'];
                $list[$key]['replydate']  = $value['replydate'];
                $list[$key]['replydatef'] = $value['replydate'] ? date("Y-m-d H:i:s", $value['replydate']) : "";

                $is_self = $userid == $value['userid'] ? 1 : 0;
                $list[$key]['is_self'] = $is_self;

                if(!$value['isanony']){
                    $this->param = $value['userid'];
                    $user = $this->detail();
                }else{
                    $user = array(
                        "nickname" => self::$langData['siteConfig'][34][10], //匿名用户
                        "photo" => ""
                    );
                }
                $list[$key]['user'] = $user;

                $list[$key]['rating'] = $value['rating'];
                $list[$key]['sco1'] = $value['sco1'];
                $list[$key]['sco1Txt'] = $sco1Txt[$value['sco1']];
                $list[$key]['content'] = $value['content'];

                $list[$key]['dtime'] = $value['dtime'];

                if($value['pid'] == 0){
                    /* $param = array(
                        "service" => "business",
                        "type" => "comdetail",
                        "id" => $value['id']
                    );
                    $list[$key]['url'] = getUrlPath($param); */

                }

                $act    = $value['type'];
                if($act!='paotui-order'){
                    $actArr = explode('-', $act);
                    $module = $actArr[0];
                    $action = $actArr[1];
                    $action_= $actArr[1];

                    //处理商家、二手商家、团购商品详情
                    if($module == "business" || ($module == "info" && $action == "business") || ($module == "waimai" && $action == "order")){
                        $action = "storeDetail";
                    }elseif(($module == "tuan" && $action == "order") || ($module == "shop" && $action == "order")){
                        $action = "detail";
                    }

                    $param = array(
                        "service"     => $module,
                        "template"    => $action,
                        "id"          => $value['aid']
                    );

                    $handels = new handlers($module, $action);
                    $detail  = $handels->getHandle($value['aid']);
                    if(is_array($detail) && $detail['state'] == 100){
                        $detail  = $detail['info'];
                        if(is_array($detail)){
                            $list[$key]['detail'] = $detail;

                            if($module == "waimai" && $action == "order"){
                                $list[$key]['detail']['title'] = $detail['shopname'];
                            }

                            if($module == "info" && $action_ == "business"){
                                $param = array(
                                    "service"     => $module,
                                    "template"    => "business",
                                    "id"          => $detail['id'],
                                );
                            }

                            if(!$detail['url']){
                                $list[$key]['detail']['url'] = getUrlPath($param);
                            }
                        }
                    }else{
                        $handels = new handlers($module, $action."Detail");
                        $detail  = $handels->getHandle($value['aid']);

                        if(is_array($detail) && $detail['state'] == 100){
                            $detail  = $detail['info'];
                            if(is_array($detail)){
                                $list[$key]['detail'] = $detail;

                                if($module == "travel" || ($module == "marry" && $action_ == "store") || ($module == "marry" && $action_ == "rental")){
                                    $param = array(
                                        "service"     => $module,
                                        "template"    => $action_ . '-' . 'detail',
                                        "id"          => $detail['id'],
                                    );
                                }

                                if(!$detail['url']){
                                    $list[$key]['detail']['url'] = getUrlPath($param);
                                }
                            }
                        }
                    }
                }else{
                    $sql = $dsql->SetQuery("SELECT o.`id`, o.`shop` shopname, o.`ordernum` ordernumstore FROM (`#@__public_comment` c LEFT JOIN `#@__paotui_order` o ON c.`oid` = o.`id`) WHERE c.`oid` = ".$value['oid']);
                    $shop = $dsql->dsqlOper($sql, "results");
                    if($shop){
                        $list[$key]['detail']['id']    = $shop[0]['id'];
                        $list[$key]['detail']['title'] = $shop[0]['shopname'];
                    }

                }

                $pics = $value['pics'];
                $picsArr = array();
                if($pics){
                    $pics_ = explode(',', $pics);
                    foreach ($pics_ as $k => $v) {
                        $picsArr[] = getFilePath($v);
                    }
                }
                $list[$key]['pics'] = $picsArr;

                $list[$key]['audio'] = $value['audio'] ? getFilePath($value['audio']) : "";
                $list[$key]['video'] = $value['video'] ? getFilePath($value['video']) : "";
                $list[$key]['ip'] = $value['ip'];
                $list[$key]['zan'] = $value['zan'];

                $zan_has = 0;
                if($userid != -1 && $value['zan_user']){
                    $zan_user = explode(',', $value['zan_user']);
                    if(in_array($userid, $zan_user)){
                        $zan_has = 1;
                    }
                }
                $list[$key]['zan_has'] = $zan_has;

                //帖子
                if($type == 'tieba-detail'){
                    $memberID = $value['userid'];
                    $$tizi_memberTotal = 0;
					$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `uid` = $memberID");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$tizi_memberTotal = $ret[0]['t'];
                    }
                    $list[$key]['tizi_memberTotal'] = $tizi_memberTotal;
					//精华总数
					$tizi_memberJinghuaTotal = 0;
					$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `jinghua` = 1 AND `uid` = $memberID");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$tizi_memberJinghuaTotal = $ret[0]['t'];
                    }
                    $list[$key]['tizi_memberJinghuaTotal'] = $tizi_memberJinghuaTotal;

                    //回复数量
                    $replynums = 0;
                    $sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'tieba-detail' AND `pid` = ".$value['id']);
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $replynums = $ret[0]['t'];
                    }
                    $list[$key]['replynums'] = $replynums;
                }

                if($userid != -1){
                    $isfollow = 0;
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $userid AND `fid` = " . $value['userid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret && is_array($ret)){
                        $isfollow = 1;
                    }
                    $list[$key]['isfollow'] = $isfollow;
                }

                // 获取子评论
                if($son){
                    $lower = array();
                    if($value['pid'] == 0){
                        $param['pid'] = $value['id'];
                        $param['page'] = 1;
                        $param['pageSize'] = 100;
                        $this->param = $param;

                        $child = $this->getChildComment();

                        if(!isset($lower['state']) || !$lower['state'] == 200){
                            $lower = $child['list'];
                        }

                        $list[$key]['lower'] = array(
                            "count" => count($lower),
                            "list" => $lower
                        );
                    }
                }






            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);

    }

    /**
     * 获取级子评论
     */
    public function getChildComment(){
        global $dsql;
        global $userLogin;
        global $langData;

        $pageinfo = array();

        $userid = $userLogin->getMemberID();

        $param    = $this->param;
        $pid      = (int)$param['pid'];
        $rid      = (int)$param['rid'];
        $sid      = (int)$param['sid'];
        $page     = (int)$this->param['page'];
        $pageSize = (int)$this->param['pageSize'];

        if(empty($pid) && empty($rid) && empty($sid)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if($rid){
            $where = " AND `rid` = $rid";
        }else{
            $where = " AND `pid` = $pid AND `rid` = 0";
        }
        if($sid){
            $where = " AND `sid` = $sid";
        }
        $where .= " AND `ischeck` = 1";

        if(!$rid){
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE 1 = 1".$where);

            $archives_ = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE 1 = 1 AND `pid` = $pid");

            //总条数
            $totalCount = $dsql->dsqlOper($archives, "totalCount");
            //总条数包含三级评论
            $totalCount_all = $dsql->dsqlOper($archives_, "totalCount");

            //总分页数
            $totalPage = ceil($totalCount/$pageSize);

            if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

            $pageinfo = array(
                "page" => $page,
                "pageSize" => $pageSize,
                "totalPage" => $totalPage,
                "totalCount" => $totalCount,
                "totalCount_all" => $totalCount_all,
            );
        }

        $archives = $dsql->SetQuery("SELECT * FROM `#@__public_comment` WHERE 1 = 1".$where);

        $order = " ORDER BY `id` ASC";
        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives.$order.$where, "results");
        $list = array();

        if(is_array($results) && !empty($results)){
            $temp = $this->temp;
            foreach ($results as $key => $value) {
                $list[$key]['id'] = $value['id'];
                $list[$key]['pid'] = $value['pid'];
                $list[$key]['type'] = $value['type'];
                $list[$key]['oid'] = $value['oid'];
                $list[$key]['rid'] = $value['rid'];
                $list[$key]['sid'] = $value['sid'];
                $list[$key]['ipaddr']   = $value['ipaddr'];
                $list[$key]['zan'] = $value['zan'];
                $list[$key]['ftime']    = floor((GetMkTime(time()) - $value['dtime'] / 86400) % 30) > 30 ? date("Y-m-d", $value['dtime']) : FloorTime(GetMkTime(time()) - $value['dtime']);

                if(isset($temp[$value['userid']])){
                    $user = $temp[$value['userid']];
                }else{
                    $this->param = $value['userid'];
                    $user = $this->detail();
                    $temp[$value['userid']] = $user;
                }
                $list[$key]['user'] = $user;

                $zan_has = 0;
                if($userid != -1 && $value['zan_user']){
                    $zan_user = explode(',', $value['zan_user']);
                    if(in_array($userid, $zan_user)){
                        $zan_has = 1;
                    }
                }
                $list[$key]['zan_has'] = $zan_has;

                if($rid){
                    $sid = $value['sid'];
                    $sql = $dsql->SetQuery("SELECT `userid` FROM `#@__public_comment` WHERE `id` = ".$sid);
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $uid = $ret[0]['userid'];
                        if(isset($temp[$uid])){
                            $user = $temp[$uid];
                        }else{
                            $this->param = $value['uid'];
                            $user = $this->detail();
                            $temp[$uid] = $user;
                        }
                    }else{
                        $user = array();
                    }
                    $list[$key]['member'] = $user;
                }

                $this->temp = $temp;

                $list[$key]['content'] = $value['content'];
                $list[$key]['dtime'] = $value['dtime'];
                $list[$key]['ip'] = $value['ip'];

                // 获取子评论
                if(!$rid){
                    $lower = array();
                    $param['rid'] = $value['id'];
                    $param['page'] = 1;
                    $param['pageSize'] = 100;
                    $this->param = $param;

                    $child = $this->getChildComment();

                    if(!isset($child['state']) || $child['state'] != 200){
                        $lower = $child['list'];
                    }

                    $list[$key]['lower'] = array(
                        "count" => count($lower),
                        "list" => $lower
                    );
                }
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);


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

        // $sql = $dsql->SetQuery("SELECT * FROM `#@__public_comment` WHERE `id` = $id AND `isCheck` = 1 AND `pid` = 0");
        $sql = $dsql->SetQuery("SELECT * FROM `#@__public_comment` WHERE `id` = $id ");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            $detail = array();
            $zan_has = 0;
            $ret = $ret[0];
            foreach ($ret as $key => $value) {
                if($key == "pics"){
                    $pics = array();
                    $value_ = $value ? explode(",", $value) : array();
                    foreach ($value_ as $k => $v) {
                        $pics[] = getFilePath($v);
                    }
                    $value = $pics;
                }
                if($key == "audio" || $key == "video"){
                    $value = getFilePath($value);
                }

                if($key == "zan_user"){
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
                $this->param = $ret['userid'];
                $detail['user'] = $this->detail();
            }
            return $detail;
        }
    }

    /**
     * 删除商家评论
     */
  	public function delComment(){
    	global $dsql;
        global $userLogin;

        $uid = $userLogin->getMemberID();

        $param = $this->param;
        $id    = (int)$param['id'];

      	if($uid==-1) return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！

      	if(empty($id)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误

      	$sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `id` = '$id'");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
            $sql = $dsql->SetQuery("DELETE FROM `#@__public_up` WHERE `type` = '1' and `tid` = '$id'");
            $dsql->dsqlOper($sql, "update");

			$archives = $dsql->SetQuery("DELETE FROM `#@__public_comment` WHERE `id` = '$id'");
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				return array("state" => 200, "info" => self::$langData['siteConfig'][20][300]);//删除失败！
			}
          	return self::$langData['siteConfig'][21][136];//"删除成功！";
		}else{
			return array("state" => 200, "info" => self::$langData['siteConfig'][20][300]);//删除失败！
		}
    }

    /**
     * 公共点赞
     */
    public function getZan(){
		global $dsql;
		global $userLogin;
		$param = $this->param;

		$id       = $param['id'];
        $uid      = $param['uid'];
        $module   = $param['module'];
        $temp     = $param['temp'];
        $type     = (int)$param['type'];
        $check    = $param['check'];

		$userid      = $userLogin->getMemberID();

        $puctime = time();
        if(!$check){

            if($userid == -1){
                return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！
            }

            if(empty($id) || empty($module) || empty($temp)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]);//参数错误
        }


        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_up`  WHERE `type` = '$type' and `action` = '$temp' and `module` = '$module' and `tid` = '$id' and `ruid` = '$userid'");
        $res = $dsql->dsqlOper($sql, "results");
        if(!empty($res)){
            if($check){
                return 'has';
            }
            if($module == 'article' && $temp == 'detail'){
                $sub = new SubTable('article', '#@__articlelist');
                $break_table = $sub->getSubTableById($id);
                $archives = $dsql->SetQuery("UPDATE `".$break_table."` SET  `zan` = zan - 1 WHERE `id` = '$id'");
                $results = $dsql->dsqlOper($archives, "update");
            }elseif($module == 'travel' && $temp == 'video-detail'){
                $archives = $dsql->SetQuery("UPDATE `#@__travel_video` SET  `zan` = zan - 1 WHERE `id` = '$id'");
                $results = $dsql->dsqlOper($archives, "update");
            }else{
                $results = 'ok';
            }

            if($results == 'ok'){
                $archives = $dsql->SetQuery("DELETE FROM `#@__public_up` WHERE `type` = '$type' and `action` = '$temp' and `module` = '$module' and `tid` = '$id' and `ruid` = '$userid'");
				$dsql->dsqlOper($archives, "update");

                // 清除缓存
                if($module == 'article' && $temp == 'detail'){
                    checkCache("article_list", $id);
                    clearCache("article_detail", $id);
                }elseif($module == 'travel' && $temp == 'video-detail'){
                    checkCache("travel_video_list", $id);
                    clearCache("travel_video_detail", $id);
                }

				return 'ok';
            }else{
                return array("state" => 200, "info" => self::$langData['siteConfig'][21][72]);//操作失败，请重试！
            }
        }else{
            if($check){
                return 'no';
            }
            if($module == 'article' && $temp == 'detail'){
                $sub = new SubTable('article', '#@__articlelist');
                $break_table = $sub->getSubTableById($id);
                $archives = $dsql->SetQuery("UPDATE `".$break_table."` SET  `zan` = zan + 1 WHERE `id` = '$id'");
                $results = $dsql->dsqlOper($archives, "update");
            }elseif($module == 'travel' && $temp == 'video-detail'){
                $archives = $dsql->SetQuery("UPDATE `#@__travel_video` SET  `zan` = zan + 1 WHERE `id` = '$id'");
                $results = $dsql->dsqlOper($archives, "update");
            }else{
                $results = 'ok';
            }

            if($results != "ok"){
				return array("state" => 200, "info" => self::$langData['siteConfig'][21][72]);//操作失败，请重试！
			}else{
				//插入点赞人信息
				$archives = $dsql->SetQuery("INSERT INTO `#@__public_up` (`uid`, `tid`, `ruid`, `module`, `action`, `puctime`, `type`) VALUES ('$uid', '$id', '$userid', '$module', '$temp', '$puctime', '$type')");
				$dsql->dsqlOper($archives, "update");

                // 清除缓存
                if($module == 'article' && $temp == 'detail'){
                    checkCache("article_list", $id);
                    clearCache("article_detail", $id);
                }elseif($module == 'travel' && $temp == 'video-detail'){
                    checkCache("travel_video_list", $id);
                    clearCache("travel_video_detail", $id);
                }

				return 'ok';
			}
        }
    }

    /**
     * 点赞人列表
     */
	public function upList(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
        $orderby = $page = $pageSize = $where = $where1 = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
			}else{
                $tid      = $this->param['tid'];
                $module   = $this->param['module'];
                $uid      = (int)$this->param['uid'];
                $u        = (int)$this->param['u'];
                $type     = (int)$this->param['type'];
                $temp     = $this->param['temp'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

        $userid = $userLogin->getMemberID();

        if($u==1){
            $where .=" AND `uid` = '$userid'";
        }

		if(!empty($tid)){
			$where .=" AND `tid` in ($tid)";
        }

        if($type != ''){
			$where .=" AND `type` = '$type'";
        }

        if(!empty($module)){
			$where .=" and module='$module'";
        }

        if(!empty($temp)){
			$where .=" and action='$temp'";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$order = " ORDER BY `puctime` DESC, `id` DESC";

		$archives_count = $dsql->SetQuery("SELECT count(`id`) FROM `#@__public_up` l WHERE 1 = 1".$where);
		//总条数
		$totalResults = $dsql->dsqlOper($archives_count, "results", "NUM");
		$totalCount = (int)$totalResults[0][0];

		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => self::$langData['siteConfig'][21][64]);//暂无数据！

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$archives = $dsql->SetQuery("SELECT `id`, `uid`, `tid`, `ruid`, `puctime`, `type`, `action`, `module` FROM `#@__public_up` l WHERE 1 = 1".$where);
		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where1.$order.$where, "results");
		if($results){
			foreach($results as $key => $val){
				//楼主信息
				/* $upUsername = $upPhoto = "";
				$sql = $dsql->SetQuery("SELECT `nickname`, `photo` FROM `#@__member` WHERE `id` = ".$val['uid']);
				$upRet = $dsql->dsqlOper($sql, "results");
				if($upRet){
					$upUsername = $upRet[0]['nickname'];
					$upPhoto    = getFilePath($upRet[0]['photo']);
				}
				$list[$key]['upUsername'] = $upUsername;
				$list[$key]['upPhoto'] = $upPhoto; */

				//点赞人信息
				$uid = $username = $photo = "";
				$sql = $dsql->SetQuery("SELECT `id`, `nickname`, `photo` FROM `#@__member` WHERE `id` = ".$val['ruid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$username = $ret[0]['nickname'];
					$uid	  = $ret[0]['id'];
					$photo    = getFilePath($ret[0]['photo']);
				}
				$list[$key]['uid'] = $uid;
				$list[$key]['username'] = $username;
                $list[$key]['photo'] = $photo;

                //点赞信息
                $list[$key]['puctime'] = $val['puctime'];
                $list[$key]['type'] = $val['type'];
                $list[$key]['id']   = $val['id'];
                $module = $val['module'];
                if($val['action']!='paotui-order'){
                    if($val['type'] == 1){//评论点赞
                        $actArr = explode('-', $val['action']);
                        $action = $actArr[1];
                        $action_= $actArr[1];
                        if($module == "business" && $val['action'] == 'business'){
                            $action_= 'detail';
                        }
                        //处理商家、二手商家、团购商品详情
                        if($module == "business" || ($module == "info" && $action == "business") || ($module == "waimai" && $action == "order")){
                            $action = "storeDetail";
                        }elseif(($module == "tuan" && $action == "order") || ($module == "shop" && $action == "order")){
                            $action = "detail";
                        }
                        $commentcontent = '';
                        $sql = $dsql->SetQuery("SELECT `aid`, `oid`, `content` FROM `#@__public_comment` WHERE `id` = '".$val['tid']."' ");
                        $ret = $dsql->dsqlOper($sql, "results");
                        if($ret){
                            $commentcontent = $ret[0]['content'];
                            $tid = $ret[0]['aid'];
                        }

                    }else{//信息点赞

                        $action = $val['action'];
                        $action_= $val['action'];
                        if($val['module'] == 'travel' && $val['action'] == 'video-detail'){
                            $action = 'videoDetail';
                        }elseif($val['module'] == 'live' && $val['action'] == 'h_detail'){
                            $action = 'detail';
                        }

                        $tid = $val['tid'];
                    }

                    $param = array(
                        "service"     => $module,
                        "template"    => $action_,
                        "id"          => $tid
                    );

                    $handels = new handlers($module, $action);
                    $detail  = $handels->getHandle($tid);
                    if(is_array($detail) && $detail['state'] == 100){
                        $detail  = $detail['info'];
                        if(is_array($detail)){
                            $list[$key]['detail'] = $detail;

                            if($module == "waimai" && $action == "order"){
                                $list[$key]['detail']['title'] = $detail['shopname'];
                            }

                            if($commentcontent){
                                $list[$key]['detail']['commentcontent'] = $commentcontent;
                            }

                            if($module == "info" && $action_ == "business"){
                                $param = array(
                                    "service"     => $module,
                                    "template"    => "business",
                                    "id"          => $detail['id'],
                                );
                            }

                            if(!$detail['url']){
                                $list[$key]['detail']['url'] = getUrlPath($param);
                            }
                        }
                    }else{
                        $handels = new handlers($module, $action."Detail");
                        $detail  = $handels->getHandle($tid);

                        if(is_array($detail) && $detail['state'] == 100){
                            $detail  = $detail['info'];
                            if(is_array($detail)){
                                $list[$key]['detail'] = $detail;

                                if($module == "travel" || ($module == "marry" && $action_ == "store") || ($module == "marry" && $action_ == "rental")){
                                    $param = array(
                                        "service"     => $module,
                                        "template"    => $action_ . '-' . 'detail',
                                        "id"          => $detail['id'],
                                    );
                                }

                                if($commentcontent){
                                    $list[$key]['detail']['commentcontent'] = $commentcontent;
                                }

                                if(!$detail['url']){
                                    $list[$key]['detail']['url'] = getUrlPath($param);
                                }
                            }
                        }
                    }
                }else{
                    $commentcontent = '';
                    $sql = $dsql->SetQuery("SELECT `aid`, `oid`, `content` FROM `#@__public_comment` WHERE `id` = '".$val['tid']."' ");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $commentcontent = $ret[0]['content'];
                        $tid = $ret[0]['oid'];
                    }

                    $sql = $dsql->SetQuery("SELECT o.`id`, o.`shop` shopname, o.`ordernum` ordernumstore FROM (`#@__public_comment` c LEFT JOIN `#@__paotui_order` o ON c.`oid` = o.`id`) WHERE c.`oid` = ".$tid);
                    $shop = $dsql->dsqlOper($sql, "results");
                    if($shop){
                        $list[$key]['detail']['id']    = $shop[0]['id'];
                        $list[$key]['detail']['title'] = $shop[0]['shopname'];
                        $list[$key]['detail']['commentcontent'] = $commentcontent;
                    }
                }

                $totalAudit = 0;
                if($module == 'article' && $temp == 'detail'){
                    $sub = new SubTable('article', '#@__articlelist');
                    $breakup_table_res = $sub->getSubTable();
                    foreach ($breakup_table_res as $item){
                        $sql_count1 = $dsql->SetQuery("SELECT `id` FROM `".$item['table_name']."` l WHERE `del` = 0 AND `arcrank` = 1 AND `admin` =" . $val['ruid']);
                        $break_count_ = $dsql->dsqlOper($sql_count1 . $where, "totalCount");
                        $totalAudit += $break_count_;
                    }
                }
                $list[$key]['infoTotal'] = $totalAudit;

                //帖子总数
                if($module == 'tieba'){
                    $sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `uid` = " . $val['ruid']);
                    $ret = $dsql->dsqlOper($sql, "results");
                    $list[$key]['tiziTotal'] = $ret[0]['t'];
                }

				//关注人数
				$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__member_follow` WHERE `tid` = " . $val['ruid']);
				$followret = $dsql->dsqlOper($sql, "results");
				$list[$key]['followTotal'] = $followret[0]['t'];
				//粉丝人数
				$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__member_follow` WHERE `fid` = " . $val['ruid']);
				$fansret = $dsql->dsqlOper($sql, "results");
				$list[$key]['totalFans'] = $fansret[0]['t'];

				//点赞人和楼主是否相互关注
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $userid AND `fid` = " . $val['ruid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$list[$key]['isfollow'] = 1;
				}elseif($userid == $val['ruid']){
					$list[$key]['isfollow'] = 2;
				}else{
					$list[$key]['isfollow'] = 0;
                }
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
    }

    /**
     * 留言建议
     * @return array
     */
    public function suggestion(){
        global $dsql;
        global $userLogin;
        global $langData;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
            }else{
                $desc = $this->param['desc'];
                $phone = $this->param['phone'];
                $vdimgck = $this->param['vdimgck'];
            }
        }

        $return = "";
        $type = htmlspecialchars(RemoveXSS(filterSensitiveWords(cn_substrR(addslashes($type), 50))));
        $desc = htmlspecialchars(RemoveXSS(filterSensitiveWords(cn_substrR(addslashes($desc), 200))));
        $phone = htmlspecialchars(RemoveXSS(filterSensitiveWords(cn_substrR(addslashes($phone), 50))));


        if($vdimgck && strtolower($vdimgck) != $_SESSION['huoniao_vdimg_value']) $return = array("state" => 200, "info" => $langData['siteConfig'][20][99]);  //验证码输入错误，请重试！

        //获取用户ID
        $uid    = $userLogin->getMemberID();
        $ip     = GetIP();
        $ipAddr = getIpAddr($ip);

        $archives = $dsql->SetQuery("INSERT INTO `#@__member_suggestion` (`desc`, `phone`, `userid`, `ip`, `ipaddr`, `pubdate`, `state`) VALUES ('$desc', '$phone', '$uid', '$ip', '$ipAddr', ".GetMkTime(time()).", 0)");
        $res  = $dsql->dsqlOper($archives, "update");
        if($res != "ok"){
            $return = '留言失败';
        }else{
            $return = '留言成功';
        }
        return $return;
    }

    /**
     * 留言建议列表
     * @return array
     */
    public function suggestionlist(){
        global $dsql;
		global $userLogin;
		global $langData;
		$pageinfo = $list = array();
        $title = $orderby = $page = $pageSize = $where = "";

        if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//'格式错误！'
			}else{
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
        }

        $where = " AND `state` = 1";

        $order = " ORDER BY `id` DESC";

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $archives = $dsql->SetQuery("SELECT `id`, `desc`, `userid`, `pubdate`, `state`, `note`, `opuid`, `optime` FROM `#@__member_suggestion` WHERE 1 = 1" . $where);
        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");
        //总分页数
        $totalPage = ceil($totalCount / $pageSize);

        if ($totalCount == 0) return array("state" => 200, "info" => self::$langData['siteConfig'][21][64]);//暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $atpage = $pageSize * ($page - 1);
        $where  = " LIMIT $atpage, $pageSize";

        $results = $dsql->dsqlOper($archives . $order . $where, "results");
        if ($results) {
            foreach ($results as $key => $val) {
                $list[$key]['id']      = $val['id'];
                $list[$key]['desc']    = $val['desc'];
                $list[$key]['note']    = $val['note'];
                $list[$key]['pubdate'] = $val['pubdate'];
                $list[$key]['optime']  = $val['optime'];
                $list[$key]['opuid']   = $val['opuid'];
                $list[$key]['pubdate1']= !empty($val['pubdate']) ? date("Y/m/d", $val['pubdate']) : '';
                $list[$key]['pubdate2']= !empty($val['pubdate']) ? date("Y-m-d H:i", $val['pubdate']) : '';
                $list[$key]['optime1'] = !empty($val['optime']) ? date("Y-m-d H:i", $val['optime']) : '';
                //会员信息
                $member               = getMemberDetail($val['userid']);
                $list[$key]['member'] = array(
                    "id" => $val['userid'],
                    "nickname" => $member['nickname'],
                    "photo" => $member['photo'],
                    "userType" => $member['userType']
                );
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $list);

    }

    /**
     * 消费返积分
     */
    public function returnPoint($module, $userid, $amount, $ordernum){
        global $dsql;
        global $cfg_returnPointState;
        global $cfg_returnPoint_tuan;
        global $cfg_returnPoint_shop;
        global $cfg_returnPoint_info;
        global $cfg_returnPoint_waimai;
        global $cfg_returnPoint_homemaking;
        global $cfg_returnPoint_travel;
        global $cfg_returnPoint_education;
        global $installModuleTitleArr;

        $ratio = 0;
        $tit = self::$langData['siteConfig'][34][11];//订单返积分;
        switch($module){
            case 'tuan':
                $ratio = $cfg_returnPoint_tuan;
                break;
            case 'shop':
                $ratio = $cfg_returnPoint_shop;
                break;
            case 'info':
                $ratio = $cfg_returnPoint_info;
                break;
            case 'waimai':
                $ratio = $cfg_returnPoint_waimai;
                break;
            case 'homemaking':
                $ratio = $cfg_returnPoint_homemaking;
                break;
            case 'travel':
                $ratio = $cfg_returnPoint_travel;
                break;
            case 'education':
                $ratio = $cfg_returnPoint_education;
                break;
        }
        $title = $installModuleTitleArr[$module].$tit.$ordernum;
        if($cfg_returnPointState && $ratio){
            $point = (int)($amount * $ratio / 100);
            if($point > 0){
                $now = time();
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + $point WHERE `id` = $userid");
                $res = $dsql->dsqlOper($archives, "update");
                if($res == "ok"){
                    //保存操作日志
                    $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$point', '$title', '$now')");
                    $dsql->dsqlOper($archives, "update");
                }
            }
        }

        $this->returnFxMoney($module, $userid, $ordernum);
    }

    /**
     * 分销返佣金
     */
    public function returnFxMoney($module = "", $userid = 0, $ordernum = ""){
        // $module = 'shop';
        // $ordernum = '1941608508875270';
        // $userid = 2001;

        global $dsql;
        global $userLogin;
        global $cfg_fenxiaoName;
        global $cfg_fenxiaoState;
        global $cfg_fenxiaoAmount;
        global $cfg_fenxiaoLevel;

        // $userid = $userLogin->getMemberID();
        // if($userid <= 0) return;

        $fenxiaoAmount = $cfg_fenxiaoAmount;
        $fenxiaoLevel = $cfg_fenxiaoLevel ? unserialize($cfg_fenxiaoLevel) : array();
        if($cfg_fenxiaoState != 1 || !$fenxiaoLevel) return;

        $levelUID = $this->getFXS($userid, count($fenxiaoLevel), 0, []);

        // print_r($levelUID);
        // print_r($fenxiaoLevel);
        // die;
        if($levelUID){

            $product = array();

            if($module == 'shop'){
                $sql = $dsql->SetQuery("SELECT p.`id`, p.`fx_reward`, p.`title`, op.`price`, op.`count`, o.`userid`, s.`userid` storeuid, m.`username` FROM `#@__shop_product` p LEFT JOIN `#@__shop_order_product` op ON op.`proid` = p.`id` LEFT JOIN `#@__shop_order` o ON o.`id` = op.`orderid` LEFT JOIN `#@__shop_store` s ON s.`id` = p.`store` LEFT JOIN `#@__member` m ON m.`id` = o.`userid` WHERE o.`ordernum` = '$ordernum'");
                $res = $dsql->dsqlOper($sql, "results");
                if(!$res) return;

                $storeuid = $res[0]['storeuid'];
                $username = $res[0]['username'];
                $totalPrice = 0;//商品总金额
                $totalAmount = 0;//佣金总额
                foreach ($res as $k => $v) {
                    $totalPrice_ = $v['price'] * $v['count'];
                    $totalPrice += $totalPrice_;
                    $fx_reward_ratio = $v['fx_reward'];
                    if($v['fx_reward'] == '0'){
                        $fx_reward = 0;
                    }elseif($v['fx_reward']){
                        if(strstr($v['fx_reward'], '%')){
                            $fx_reward = $v['price'] * $v['count'] * (float)$v['fx_reward'] / 100;
                        }else{
                            $fx_reward = $v['fx_reward'] * $v['count'];
                        }
                    }else{
                        $fx_reward_ratio = $fenxiaoAmount."%";
                        $fx_reward = $totalPrice_ * $fenxiaoAmount / 100;
                    }
                    $totalAmount_ = $fx_reward;
                    $totalAmount += $totalAmount_;

                    if($fx_reward > 0){
                        $product[] = array(
                            'id' => $v['id'],
                            'title' => $v['title'],
                            'price' => $v['price'],
                            'count' => $v['count'],
                            'fx_reward_ratio' => $fx_reward_ratio,
                            'fx_reward' => $fx_reward,
                        );
                    }
                }

                $this->putFxReward($module, $storeuid, $ordernum, $userid, $username, $totalAmount, $levelUID, $fenxiaoLevel, $product);

            }elseif($module == 'tuan'){

                // 团购券
                $is_quan = 0;
                if(strlen((string)$ordernum) == 12){
                    $is_quan = 1;
                    $sql = $dsql->SetQuery(
                        "SELECT p.`id`, p.`fx_reward`, p.`title`, o.`userid`, o.`orderprice`, o.`procount`, o.`ordernum`, s.`uid` storeuid, m.`username` FROM `#@__tuanlist` p
                        LEFT JOIN `#@__tuan_order` o ON o.`proid` = p.`id`
                        LEFT JOIN `#@__tuanquan` q ON q.`orderid` = o.`id`
                        LEFT JOIN `#@__tuan_store` s ON s.`id` = p.`sid`
                        LEFT JOIN `#@__member` m ON m.`id` = o.`userid`
                        WHERE q.`cardnum` = '$ordernum' AND q.`usedate` <> 0"
                    );
                    $res = $dsql->dsqlOper($sql, "results");
                    if(!$res) return;

                    $storeuid = $res[0]['storeuid'];
                    $username = $res[0]['username'];
                    $ordernum = $res[0]['ordernum'];
                    $totalPrice = 0;//商品总金额
                    $totalAmount = 0;//佣金总额

                    $totalPrice = $res[0]['orderprice'];
                    $fx_reward_ratio = $res[0]['fx_reward'];
                    if($res[0]['fx_reward'] == '0'){
                        $fx_reward = 0;
                    }elseif($res[0]['fx_reward']){
                        if(strstr($res[0]['fx_reward'], '%')){
                            $fx_reward = $res[0]['orderprice'] * (float)$res[0]['fx_reward'] / 100;
                        }else{
                            $fx_reward = $res[0]['fx_reward'];
                        }
                    }else{
                        $fx_reward_ratio = $fenxiaoAmount."%";
                        $fx_reward = ($totalPrice * $fenxiaoAmount / 100);
                    }
                    $totalAmount += $fx_reward;

                // 快递
                }else{
                    $sql = $dsql->SetQuery(
                        "SELECT p.`id`, p.`fx_reward`, p.`title`, o.`userid`, o.`orderprice`, o.`procount`, s.`uid` storeuid, m.`username` FROM `#@__tuanlist` p
                        LEFT JOIN `#@__tuan_order` o ON o.`proid` = p.`id`
                        LEFT JOIN `#@__tuan_store` s ON s.`id` = p.`sid`
                        LEFT JOIN `#@__member` m ON m.`id` = o.`userid`
                        WHERE o.`ordernum` = '$ordernum'"
                    );
                    $res = $dsql->dsqlOper($sql, "results");
                    if(!$res) return;

                    $storeuid = $res[0]['storeuid'];
                    $username = $res[0]['username'];
                    $totalPrice = 0;//商品总金额
                    $totalAmount = 0;//佣金总额

                    $totalPrice = $res[0]['orderprice'] * $res[0]['procount'];
                    $fx_reward_ratio = $res[0]['fx_reward'];
                    if($res[0]['fx_reward'] == '0'){
                        $fx_reward = 0;
                    }elseif($res[0]['fx_reward']){
                        if(strstr($res[0]['fx_reward'], '%')){
                            $fx_reward = $res[0]['orderprice'] * $res[0]['procount'] * (float)$res[0]['fx_reward'] / 100;
                        }else{
                            $fx_reward = $res[0]['fx_reward'] * $res[0]['procount'];
                        }
                    }else{
                        $fx_reward_ratio = $fenxiaoAmount."%";
                        $fx_reward = ($totalPrice * $fenxiaoAmount / 100);
                    }
                    $totalAmount += $fx_reward;
                }

                if($fx_reward > 0){
                    $product[] = array(
                        'id' => $res[0]['id'],
                        'title' => $res[0]['title'],
                        'price' => $res[0]['orderprice'],
                        'count' => $is_quan ? 1 : $res[0]['procount'],
                        'fx_reward_ratio' => $fx_reward_ratio,
                        'fx_reward' => $fx_reward,
                    );
                }
                $this->putFxReward($module, $storeuid, $ordernum, $userid, $username, $totalAmount, $levelUID, $fenxiaoLevel, $product);

            }elseif($module == 'info'){
                $sql = $dsql->SetQuery(
                    "SELECT o.`price`, p.`userid` storeuid, p.`fx_reward`, m.`username` FROM `#@__info_order` o
                    LEFT JOIN `#@__infolist` p ON p.`id` = o.`prod`
                    LEFT JOIN `#@__member` m ON m.`id` = o.`userid`
                    WHERE o.`ordernum` = '$ordernum'"
                );
                $res = $dsql->dsqlOper($sql, "results");
                if($res){

                    $storeuid = $res[0]['storeuid'];
                    $username = $res[0]['username'];
                    $totalPrice = 0;//商品总金额
                    $totalAmount = 0;//佣金总额

                    $totalPrice = $res[0]['price'];
                    if($res[0]['fx_reward'] == '0'){
                        $fx_reward = 0;
                    }elseif($res[0]['fx_reward']){
                        if(strstr($res[0]['fx_reward'], '%')){
                            $fx_reward = $res[0]['price'] * (float)$res[0]['fx_reward'] / 100;
                        }else{
                            $fx_reward = $res[0]['fx_reward'];
                        }
                    }else{
                        $fx_reward = ($totalPrice * $fenxiaoAmount / 100);
                    }
                    $totalAmount += $fx_reward;

                    $this->putFxReward($module, $storeuid, $ordernum, $userid, $username, $totalAmount, $levelUID, $fenxiaoLevel, $product);

                }

            }elseif($module == 'waimai'){
                $sql = $dsql->SetQuery(
                    "SELECT o.`food`, o.`priceinfo`, s.`userid` storeuid, m.`username` FROM `#@__waimai_order` o
                    LEFT JOIN `#@__waimai_shop` s ON s.`id` = o.`sid`
                    LEFT JOIN `#@__member` m ON m.`id` = o.`uid`
                    WHERE o.`ordernum` = '$ordernum'"
                );// AND `state` = 1
                $res = $dsql->dsqlOper($sql, "results");

                if($res){
                    $order = $res[0];

                    $storeuid = $res[0]['storeuid'];
                    $username = $res[0]['username'];
                    $totalPrice = 0;//商品总金额
                    $totalAmount = 0;//佣金总额

                    $food = unserialize($order['food']);
                    // $priceinfo = unserialize($order['priceinfo']);
                    // print_r($food);
                    foreach ($food as $k => $v) {
                        $totalPrice_ = $v['price'] * $v['count'];
                        $totalPrice += $totalPrice_;
                        $fx_reward_ratio = $v['fx_reward'];
                        if($v['fx_reward'] == '0'){
                            $fx_reward = 0;
                        }elseif($v['fx_reward']){
                            if(strstr($v['fx_reward'], '%')){
                                $fx_reward = $v['price'] * $v['count'] * (float)$v['fx_reward'] / 100;
                            }else{
                                $fx_reward = $v['fx_reward'] * $v['count'];
                            }
                        }else{
                            $fx_reward_ratio = $fenxiaoAmount."%";
                            $fx_reward = $totalPrice_ * $fenxiaoAmount / 100;
                        }
                        $totalAmount_ = $fx_reward;
                        $totalAmount += $totalAmount_;

                        if($fx_reward > 0){
                            $product[] = array(
                                'id' => $v['id'],
                                'title' => $v['title'],
                                'price' => $v['price'],
                                'count' => $v['count'],
                                'fx_reward_ratio' => $fx_reward_ratio,
                                'fx_reward' => $fx_reward,
                            );
                        }
                    }

                    $this->putFxReward($module, $storeuid, $ordernum, $userid, $username, $totalAmount, $levelUID, $fenxiaoLevel, $product);
                }
            }

        }

    }

    /**
     * 发放佣金
     */
    private function putFxReward($module, $storeuid, $ordernum, $userid, $username, $totalAmount = 0, $levelUID = array(), $fenxiaoLevel = array(), $product = array()){
        global $dsql;
        global $cfg_fenxiaoName;
        global $installModuleTitleArr;

        if($totalAmount <= 0) return;

        $date = time();
        $total_put = 0;
        $product = serialize($product);

        foreach ($levelUID as $k => $uv) {
            // 如果某一级用户不是分销商，结束发放
            if(empty($uv['fid']) || $uv['fstate'] != 1) break;

            $v = $uv['id'];
            $amount = sprintf("%.2f", $totalAmount * $fenxiaoLevel[$k]['fee'] / 100);
            $total_put += $amount;
            $level = $k + 1;

            //增加会员余额
            $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$amount' WHERE `id` = '$v'");
            $dsql->dsqlOper($archives, "update");

            //保存操作日志
            $title = $fenxiaoLevel[$k]['name'].'佣金收益：'.($k == 0 ? $username : $levelUID[$k-1]['username']).' - '.$installModuleTitleArr[$module].':'.$ordernum;
            $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$v', '1', '$amount', '$title', '$date')");
            $dsql->dsqlOper($archives, "update");

            // 分销记录
            $child = $k == 0 ? $userid : $levelUID[$k-1]['id'];
            $fee = $fenxiaoLevel[$k]['fee'];
            $archives = $dsql->SetQuery("INSERT INTO `#@__member_fenxiao` (`module`, `uid`, `by`, `child`, `ordernum`, `level`, `amount`, `pubdate`, `product`, `fee`) VALUES ('$module', $v, $userid, $child, '$ordernum', '$level', $amount, $date, '$product', '$fee')");
            $dsql->dsqlOper($archives, "update");

        }
        // 扣除商家余额 已取消，由平台承担费用
        // $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$total_put' WHERE `id` = '$storeuid'");
        // $dsql->dsqlOper($archives, "update");

        //保存操作日志
        // $title = '支付分销佣金：'.$ordernum;
        // $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$storeuid', '0', '$total_put', '$title', '$date')");
        // $dsql->dsqlOper($archives, "update");

    }
    /**
     * 获取分销商，从一级开始，购买人的直接上级为一级
     */
    private function getFXS($uid, $level, $no, $arr = []){
        global $dsql;

        $sql = $dsql->SetQuery("SELECT m.`id`, m.`from_uid`, m.`username`, f.`id` fid, f.`state` fstate FROM `#@__member` m LEFT JOIN `#@__member_fenxiao_user` f ON f.`uid` = m.`id` WHERE m.`id` = ".$uid);
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            if($no > 0){
                $arr[] = $res[0];
            }
            if($no < $level && $res[0]['from_uid']){
                return $this->getFXS($res[0]['from_uid'], $level, $no+1, $arr);
            }
            return $arr;
        }
        return $arr;
    }

    /**
     * 我推荐的用户
     */
    public function myRecUser(){
        global $dsql;
        global $userLogin;
        global $cfg_fenxiaoLevel;

        $orderby  = $this->param['orderby'];
        $page     = (int)$this->param['page'];
        $pageSize = (int)$this->param['pageSize'];

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $uid = $userLogin->getMemberID();
        if($uid <= 0) return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！

        $sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member` WHERE `from_uid` = $uid");
        $res = $dsql->dsqlOper($sql, "results");
        // 总条数
        $totalCount = $res[0]['total'];
        //总分页数
        $totalPage = ceil($totalCount / $pageSize);

        if($totalCount == 0) return array("state" => 200, "info" => self::$langData['siteConfig'][21][64]);  //暂无数据！

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $atpage = $pageSize * ($page - 1);
        $where  = " LIMIT $atpage, $pageSize";

        $order = ' ORDER BY `id` DESC';
        if($orderby == 1){
            $order = ' ORDER BY `id`';
        }

        //$sql = $dsql->SetQuery("SELECT `id`, `username`, (SELECT COUNT(m2.`id`) FROM `#@__member` m2 WHERE m2.`from_uid` = `id`) child FROM `#@__member` m1 WHERE `from_uid` = $uid".$where);

        $sql = $dsql->SetQuery("SELECT `id`, `username`, `photo`, `regtime` FROM `#@__member` WHERE `from_uid` = $uid".$order.$where);
        $res = $dsql->dsqlOper($sql, "results");

        $list = array();
        if($res){
            $fenxiaoLevel = $cfg_fenxiaoLevel ? unserialize($cfg_fenxiaoLevel) : array();
            $level = count($fenxiaoLevel);
            foreach ($res as $key => $value) {
                $list[$key]['id'] = $value['id'];
                $user = array(
                    'username' => $value['username'],
                    'regtime' => $value['regtime'],
                    'photo' => $value['photo'] ? getFilePath($value['photo']) : "",
                );
                $list[$key]['user'] = $user;

                // $usercount = $this->getAllFenxiaoSub($value['id'], $level, 1, 0);
                // if($usercount){
                //     $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__member_fenxiao` WHERE `child` = ".$value['id']);
                //     $ret = $dsql->dsqlOper($sql, "results");
                //     $useramount = $ret[0]['total'] ? $ret[0]['total'] : 0;
                // }else{
                //     $useramount = 0;
                // }
                $sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member` WHERE `from_uid` = ".$value['id']);
                $ret = $dsql->dsqlOper($sql, "results");
                $usercount = $ret[0]['total'];
                $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__member_fenxiao` WHERE `uid` = $uid AND `child` = ".$value['id']);
                $ret = $dsql->dsqlOper($sql, "results");
                $useramount = $ret[0]['total'] ? $ret[0]['total'] : 0;
                $list[$key]['usercount'] = $usercount;
                $list[$key]['useramount'] = $useramount;
            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);
    }

    /**
     * 获取所有下线及佣金总额，不一定是分销商
     */
    public function getAllFenxiaoSub($uid, $level, $no, $count){
        global $dsql;
        $sql = $dsql->SetQuery("SELECT f.`id`, m.`id` uid FROM `#@__member` m LEFT JOIN `#@__member_fenxiao` f ON m.`id` = f.`uid` WHERE m.`from_uid` = $uid");
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            if($no < $level){
                foreach ($res as $key => $value) {
                    $count += $this->getAllFenxiaoSub($value['uid'], $level, ++$no, 0);
                }
            }
            $count += count($res);
        }
        return $count;
    }

    /**
     * 分销佣金记录
     */
    public function fenxiaoLog(){
        global $dsql;
        global $userLogin;
        global $cfg_fenxiaoLevel;
        $list = array();
        $where = "";

        $date     = $this->param['date'];
        $page     = (int)$this->param['page'];
        $pageSize = (int)$this->param['pageSize'];

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if($date){
            $where .= " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m') = '$date'";
        }

        $uid = $userLogin->getMemberID();
        if($uid <= 0){
            return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
        }
        $sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member_fenxiao` WHERE `uid` = $uid".$where);
        $res = $dsql->dsqlOper($sql, "results");
        $totalCount = $res[0]['total'];

        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        if($totalCount == 0) return array("state" => 200, "info" => self::$langData['siteConfig'][21][64]);//暂无数据！

        $fenxiaoLevel = $cfg_fenxiaoLevel ? unserialize($cfg_fenxiaoLevel) : array();

        // $level = $fenxiaoLevel ? count($fenxiaoLevel) : 3;

        $totalAmount = 0;
        // for ($i = 1; $i <= $level ; $i++) {
        //     $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__member_fenxiao` WHERE `uid` = $uid AND `level` = $i");
        //     $res = $dsql->dsqlOper($sql, "results");
        //     $totalAmount += $res[0]['total'];
        // }
        $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__member_fenxiao` WHERE `uid` = $uid".$where);
        $res = $dsql->dsqlOper($sql, "results");
        $totalAmount = $res[0]['total'];

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount,
            "totalAmount" => $totalAmount
        );

        $atpage = $pageSize*($page-1);
        $limit = " LIMIT $atpage, $pageSize";

        $sql = $dsql->SetQuery("SELECT * FROM `#@__member_fenxiao` WHERE `uid` = $uid $where ORDER BY `id` DESC".$limit);
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            $amount_month = array();
            foreach ($res as $key => $value) {
                $list[$key]['id'] = $value['id'];
                $list[$key]['module'] = $value['module'];
                $list[$key]['ordernum'] = $value['ordernum'];
                $list[$key]['amount'] = $value['amount'];

//                $month = date('Ym', $value['pubdate']);
//                if(!isset($amount_month[$month])){
//                    $sql = $dsql->SetQuery("SELECT SUM(`amount`) total FROM `#@__member_fenxiao` WHERE `uid` = $uid AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`),'%Y%m') = '$month'");
//                    $res = $dsql->dsqlOper($sql, "results");
//                    $amount_month[$month] = $res[0]['total'];
//                }
                $list[$key]['pubdate'] = $value['pubdate'];
            }
            //$pageinfo['amount_month'] = $amount_month;
        }
        return array("pageInfo" => $pageinfo, "list" => $list);
    }

    /**
     * 分销订单详情
     */
    public function fenxiaoDetail(){
        global $dsql;
        global $userLogin;
        global $cfg_fenxiaoName;
        global $cfg_fenxiaoLevel;
        global $installModuleTitleArr;

        $fenxiaoLevel = $cfg_fenxiaoLevel ? unserialize($cfg_fenxiaoLevel) : array();

        $uid = $userLogin->getMemberID();
        if($uid <= 0) return array("state" => 0, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！

        $param = $this->param;
        $id = (int)$param['id'];

        if(empty($id)) return false;

        $sql = $dsql->SetQuery("SELECT f.*, m.`username` FROM `#@__member_fenxiao` f LEFT JOIN `#@__member` m ON m.`id` = f.`by`  WHERE f.`uid` = $uid AND f.`id` = $id");
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            $log = $res[0];
            $detail = array();


            $detail['id'] = $log['id'];
            $detail['module'] = $log['module'];
            $detail['moduleName'] = isset($log['module']) ? $installModuleTitleArr[$log['module']] : $log['module'];
            $detail['ordernum'] = $log['ordernum'];
            $detail['amount'] = $log['amount'];
            $detail['pubdate'] = $log['pubdate'];
            $detail['by'] = $log['by'];
            $detail['byuser'] = $log['username'];
            $detail['fee'] = $log['fee'];
            $detail['level'] = $log['level'];
            if(isset($fenxiaoLevel[$log['level']-1])){
                $detail['levelName'] = $fenxiaoLevel[$log['level']-1]['name'];
            }else{
                if($log['level'] < 7){
                    $detail['levelName'] = self::$langData['siteConfig'][30]['6'.$log['level']];
                }else{
                    $detail['levelName'] = $log['level'] . '级' . $cfg_fenxiaoName;
                }
            }
            $reward = 0;
            $product = unserialize($log['product']);
            foreach ($product as $k => $v) {
                $reward += $v['fx_reward'];
                $title = $v['title'];
                $litpic = "";
                $url = "";
                switch ($log['module']) {
                    case 'shop':
                        $sql = $dsql->SetQuery("SELECT `id`, `title`, `litpic` FROM `#@__shop_product` WHERE `id` = ".$v['id']);
                        $res = $dsql->dsqlOper($sql, "results");
                        if($res){
                            $title = $res[0]['title'];
                            $litpic = $res[0]['litpic'] ? getFilePath($res[0]['litpic']) : '';
                        }
                        break;
                    case 'tuan':
                        $sql = $dsql->SetQuery("SELECT `id`, `title`, `litpic` FROM `#@__tuanlist` WHERE `id` = ".$v['id']);
                        $res = $dsql->dsqlOper($sql, "results");
                        if($res){
                            $title = $res[0]['title'];
                            $litpic = $res[0]['litpic'] ? getFilePath($res[0]['litpic']) : '';
                        }
                        break;
                    case 'waimai':
                        $sql = $dsql->SetQuery("SELECT `id`, `title`, `pics` FROM `#@__waimai_list` WHERE `id` = ".$v['id']);
                        $res = $dsql->dsqlOper($sql, "results");
                        if($res){
                            if($res[0]['pics']){
                                $pics = explode(",", $res[0]['pics']);
                                $title = $res[0]['title'];
                                $litpic = getFilePath($pics[0]);
                            }
                        }
                        break;
                }
                $url = getUrlPath(array("service" => $log['module'], "template" => "detail", "id" => $v['id']));
                $product[$k]['title'] = $title;
                $product[$k]['litpic'] = $litpic;
                $product[$k]['url'] = $url;

            }
            $detail['product'] = $product;
            $detail['reward'] = $reward;

            return $detail;
        }
    }

    /**
     * 分销商申请
     */
    public function fenxiaoJoin(){
        global $dsql;
        global $userLogin;
        global $cfg_fenxiaoState;
        global $cfg_fenxiaoName;

        $param = $this->param;

        $uid = $userLogin->getMemberID();
        if($uid <= 0) return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！
        if($cfg_fenxiaoState != 1) return array("state" => 200, "info" => self::$langData['siteConfig'][33][21]);//非法请求！

        $sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__member_fenxiao_user` WHERE `uid` = $uid");
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            if($res[0]['state'] == 0) return array("state" => 200, "info" => self::$langData['siteConfig'][21][105]);//您已经申请，无需重复提交！
            if($res[0]['state'] == 2) return array("state" => 200, "info" => '您提交的申请已被拒绝，请联系管理员');//您提交的申请已被拒绝，请联系管理员
            return array("state" => 200, "info" => '您已经是'.$cfg_fenxiaoName);//您已经申请，无需重复提交！
        }

        $userinfo = $userLogin->getMemberInfo();

        $phone = $param['phone'];
        $vercode = $param['vercode'];
        if(empty($phone)) return array("state" => 200, "info" => self::$langData['siteConfig'][20][29]);//请输入您的手机号
        if(empty($vercode)) return array("state" => 200, "info" => self::$langData['siteConfig'][20][540]);//请填写验证码

        //国际版需要验证区域码
        $cphone_ = $phone;
        $archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
        $results = $dsql->dsqlOper($archives, "results");
        if($results){
            $international = $results[0]['international'];
            if($international){
                $cphone_ = $areaCode.$phone;
            }
        }

        $ip = GetIP();
        $sql_code = $dsql->SetQuery("SELECT * FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'verify' AND `ip` = '$ip' AND `user` = '$cphone_' ORDER BY `id` DESC LIMIT 1");
        $res_code = $dsql->dsqlOper($sql_code, "results");
        if($res_code){
            $code = $res_code[0]['code'];
            $codeID = $res_code[0]['id'];

            if(strtolower($vercode) != $code){
                return array('state' =>200, 'info' => '验证码输入错误，请重试！');
            }

            //5分钟有效期
            $now = GetMkTime(time());
            if($now - $res_code[0]['pubdate'] > 300) return array("state" => 200, "info" => $langData['siteConfig'][21][33]);   //验证码已过期，请重新获取！
        }else{
            return array('state' =>200, 'info' => '验证码输入错误，请重试！');
        }

        $state = 0;
        $pubdate = time();

        $sql = $dsql->SetQuery("INSERT INTO `#@__member_fenxiao_user` (`uid`, `phone`, `state`, `pubdate`) VALUES ($uid, '$phone', $state, $pubdate)");
        $res = $dsql->dsqlOper($sql, "lastid");
        if($res && is_numeric($res)){

            if(isset($codeID)){
                $sql = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `id` = $codeID");
                $dsql->dsqlOper($sql, "update");
            }

            updateAdminNotice("member", "fenxiaoUser");

            return self::$langData['siteConfig'][20][193];//提交成功，请耐心等待申请结果！
        }else{
            return array("state" => 200, "info" => self::$langData['siteConfig'][20][180]);//提交失败，请重试！
        }
    }

    /**
     * APP签到链接
     */
    public function qiandaoUrl(){
        global $dsql;
        global $cfg_qiandao_state;

        $url = '';
        $state = 0;
        if($cfg_qiandao_state){
            $param = array(
                'service' => 'member',
                'type' => 'user',
                'template' => 'qiandao'
            );
            $state = 1;
            $url = getUrlPath($param);
        }
        return array('state' => $state, 'url' => $url);
    }

    /**
     * 将点赞和评论更新为已读
     */
    public function updateRead(){
        global $dsql;
        global $userLogin;

        $param = $this->param;

        $uid = $userLogin->getMemberID();
        if($uid <= 0) return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！

        $type = $param['type'];

        if($type == 'zan'){//点赞

            $archives = $dsql->SetQuery("UPDATE `#@__public_up` SET `isread` = 1 WHERE `isread` = 0 and `uid` = '$uid'");
            $res = $dsql->dsqlOper($archives, "update");
        }else{
            //评论未读
            $where_ = " AND `userid` = '$uid'";
            $sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1".$where_);
            $ret = $dsql->dsqlOper($sql, "results");
            $sidList = array();
            foreach ($ret as $k => $v) {
                array_push($sidList, $v['id']);
            }
            if(!empty($sidList)){
                $whereC = " AND  (`sid` in(".join(',',$sidList).") or (`masterid` = '$uid' AND `sid` = '0'))";
            }else{
                $whereC = " AND  `masterid` = '$uid' AND `sid` = '0'";
            }

            $archives = $dsql->SetQuery("UPDATE `#@__public_comment` SET `isread` = 1 WHERE `isread` = 0 " . $whereC);
            $res = $dsql->dsqlOper($archives, "update");
        }

        if($res == 'ok'){
            return 'ok';
        }else{
            return '参数错误！';
        }
	}

	public function recordPushLog($zjId,$usertags,$state,$totalpush,$success,$failed,$reason){
		global $dsql;
		$template = "insert into #@__agentpushlog(zjid,usertags,state,totalpush,success,failed,reason,date) values({zjid},{usertags},{state},{totalpush},{success},{failed},'{reason}','{date}')";
		$pattern = ['{zjid}','{usertags}','{state}','{totalpush}','{success}','{failed}','{reason}','{date}'];
		$replace = [
			'zjid' => $zjId,
			'usertags' => $usertags,
			'state' => $state,
			'totalpush' => $totalpush,
			'success' => $success,
			'failed' => $failed,
			'reason' => $reason,
			'date' => date('Y-m-d')
		];
		$logs = $dsql->SetQuery(str_replace($pattern, $replace, $template));
		$result = $dsql->dsqlOper($logs, 'update');
		return;
	}

	public function push(){
		set_time_limit(0);
		//查询是否定时推送模板信息
		$messageTemplateInfo = $this->getMessageTemplateInfo(self::MSG_TEMPID);
		$isPushSiteMessage = true;
		if(!is_array($messageTemplateInfo)){
			$isPushSiteMessage = false;
			//return ["state"=>$messageTemplateInfo,"模板配置错误！\n"];
		}
		global $cfg_basehost;
		global $cfg_secureAccess;
		$port = $cfg_secureAccess == 'http://' ? 80 : 443;
		//查询经纪人信息
		global $dsql;
		//当天是否已经推送
		$sql = $dsql->SetQuery("select * from #@__agentpushlog where date='" . date('Y-m-d') . "' limit 1");
		$isPushed = $dsql->dsqlOper($sql,'results');
		$isRetry = false;
		if($isPushed && empty($isPushed['state'])){
			//查询未推送成功的用户列表
			$pushFailedSql = $dsql->SetQuery("select l.`id` logid,l.`zjid` id,l.`usertags`,l.`state`,l.`totalpush`,l.`success`,l.`failed`,m.phone,(case when ISNULL(zj.addr) or zj.addr='' or zj.addr=0 then zj.cityid else zj.addr end) addr from #@__agentpushlog l inner join #@__member m on l.zjid=m.id inner join huoniao_house_zjuser zj on zj.userid=l.zjid where l.state=0 and l.date='" . date('Y-m-d') . "'");
			$zjList = $dsql->dsqlOper($pushFailedSql, 'results');
			if(!$zjList || isset($zjList['state'])){
				return true;
			}
			$isRetry = true;
			if(count($zjList) == 1 && !isset($zjList['state']) && $zjList[0]['id'] == 0){
				$isRetry = false;
				$zjSql = $dsql->setQuery("select m.id,m.level,m.overide,ml.privilege,m.phone,(case when ISNULL(zj.addr) or zj.addr='' or zj.addr=0 then zj.cityid else zj.addr end) addr from #@__house_zjuser zj inner join #@__member m on zj.userid=m.id inner join #@__member_level ml on m.level=ml.id where m.level>0");
				$zjList = $dsql->dsqlOper($zjSql,'results');
			}
		}else{
			$zjSql = $dsql->setQuery("select m.id,m.level,m.overide,ml.privilege,m.phone,(case when ISNULL(zj.addr) or zj.addr='' or zj.addr=0 then zj.cityid else zj.addr end) addr from #@__house_zjuser zj inner join #@__member m on zj.userid=m.id inner join #@__member_level ml on m.level=ml.id where m.level>0");
			$zjList = $dsql->dsqlOper($zjSql,'results');
		}
		if(!$zjList || !empty($zjList['state'])){
			$this->recordPushLog(0,0,1,0,0,0,'无符合条件的经纪人');
		}else{
			$lastLoginTimeLimit = time() - 2592000;
			//查询可以推送的用户数量 新增条件  and usertags>0
			$userSql = $dsql->SetQuery("select id,phone,m.usertags,addr from (select id,phone,usertags,addr from #@__member where mtype=1 and lastlogintime>={$lastLoginTimeLimit} and usertags>0 union select -1,phone,max(v.usertags) usertags,max(area) addr from #@__visitor v where scantime>={$lastLoginTimeLimit} and not exists(select * from #@__member m where m.phone=v.phone) group by v.phone) m where not exists(select id from #@__house_zjuser where (userid=m.id and userid!=-1) or wx=m.phone) and not exists(select * from #@__member_collect mc where (mc.aid=m.id or mc.visitorphone=m.phone) and module='push_timer') and not exists(select phone from #@__agentresource ar where ar.phone=m.phone)");
			$userList = $dsql->dsqlOper($userSql,"results");
			if(!$userList || !empty($userList['state'])){
				$this->recordPushLog(0,0,0,0,0,0,'无可用客源');
				return false;
			}
			shuffle($userList);
		}
		foreach($zjList as $key => $val){
			if($isRetry){
				$pushTimes = $val['failed'];
				$zjCategory = $val['usertags'];
			}else{
				//计算推送的个数
				$levelPushTimes = (unserialize($val['privilege']))['push_timer'];
				//叠加次数
				$overideArr = json_decode($val['overide'],true);
				$overideTimes = 0;
				foreach($overideArr as $v){
					if(date("Y-m-d H:i:s",time()) <= $v['expire']){
						$overideTimes += $v['times'];
					}
				};
				$pushTimes = $levelPushTimes + $overideTimes;
				//经纪人会员类型
				$zjCategory = '';
				switch($val['level']){
				case 1:$zjCategory=4;break;	//写字楼会员
				case 4:$zjCategory=1;break;//住房会员
				case 5:$zjCategory=2;break;//商铺会员
				case 6:$zjCategory=8;break;//厂房仓库会员
				}
			}
			$hasPushed = 0;
			for($i = 0; $i < $pushTimes; $i++){
				if(!$userList){	
					$this->recordPushLog($val['id'],$zjCategory,0,$pushTimes,$hasPushed,$pushTimes - $hasPushed,'可用客源已分完');
					break;
				}
				$userInfo = [];
				foreach($userList as $k=>$values){
					if($values['usertags'] & $zjCategory){
						if($val['addr']){
							$addridArr = arr_foreach($dsql->getTypeList($val['addr'], "site_area"));
							$addridArr = join(',',$addridArr) . ",{$val['addr']}";
							$addridArr = explode(',', $addridArr);
							if(in_array($values['addr'], $addridArr)){
								$userInfo = $values;
								unset($userList[$k]);
								break;
							}
						}else{
							$userInfo = $values;
							unset($userList[$k]);
							break;
						}
					}
				}
				if(!$userInfo){ 
					$this->recordPushLog($val['id'],$zjCategory,0,$pushTimes,$hasPushed,$pushTimes - $hasPushed,'可用客源已分完');
					break;
				}
				$expire = date("Y-m-d") . " 23:59:59";
				$visualPhone = getVisualPhone($val['phone'],$userInfo['phone']);
				if(!$visualPhone){	
					$this->recordPushLog($val['id'],$zjCategory,0,$pushTimes,$hasPushed,$pushTimes - $hasPushed,'暂无可用的虚拟号码');
					break;
				}
				//虚拟号码绑定
				$handle = new handlers('hwVisualPhone','bind');
				$duration = strtotime(date('Y-m-d') . "19:30:00") - time();
				$result = $handle->getHandle(array('relationPhone'=>$visualPhone,"caller"=>$val['phone'],'callee'=>$userInfo['phone'],'duration'=>$duration));
				if(!$result){
					$this->recordPushLog($val['id'],$zjCategory,0,$pushTimes,$hasPushed,$pushTimes - $hasPushed,'绑定失败');
					continue;
				}
				$insertSql = $dsql->SetQuery("insert into #@__agentpushrecord(uid,zjid,expire,realphone,visualphone) values({$userInfo['id']},{$val['id']},'{$expire}','{$userInfo['phone']}','{$visualPhone}')");
				$result = $dsql->dsqlOper($insertSql,"lastid");
				$hasPushed++;
			}
			$this->recordPushLog($val['id'],$zjCategory,1,$pushTimes,$hasPushed,$pushTimes - $hasPushed,'成功');
			//异步推送消息通知
			if($isPushSiteMessage){
				$messageBody = str_replace('{{number}}',$hasPushed,$messageTemplateInfo['site_body']);
				file_get_contents($cfg_secureAccess.$cfg_basehost."/include/ajax.php?service=member&action=sendMessage&uid={$val['id']}&title={$messageTemplateInfo['site_title']}&body={$messageBody}");
				//asynExec($cfg_httpSecureAccess.$cfg_basehost,$port,"/include/ajax.php?service=member&action=sendMessage&uid={$val['id']}&title={$messageTemplateInfo['site_title']}&body={$messageBody}");
			}
		}
		return true;

	}

 /**
	错误编码：
	400：模板不存在
	401：模板已停用
	402：未开启网页消息
 */
	public function getMessageTemplateInfo($msgId){
		global $dsql;
		$sql = $dsql->SetQuery("select id,title,site_state,site_title,site_body,state from huoniao_site_notify where id=".$msgId);
		$msgInfo = $dsql->dsqlOper($sql,"results");
		if(!$msgInfo)
			return 400;
		if(!$msgInfo[0]['state']){
			return 401;
		}
		if(!$msgInfo[0]['site_state']){
			return 402;
		}
		return $msgInfo[0];
	}

	/**
		* @brief 华为云隐私通话话单通知地址
		*
		* @return 
	 */
	public function callNotify(){
		$jsonBody = file_get_contents('php://input');
		$handle = new handlers('hwVisualPhone','onFeeEvent');
		$result = $handle->getHandle(array('jsonBody'=>$jsonBody));
		return $result;
	}
	
	/**
		* @brief 短信通知通知地址
		*
		* @return 
	 */
	public function smsNotify(){
		//var_dump(getFilePath('http://91ifumei.com/uploads/house/atlas/large/2018/12/29/15460751562708.jpg'));
		return 12;
	}

	/**
		* @brief 呼叫状态接收地址
		*
		* @return 
	 */
	public function callStateNotify(){
		$jsonBody = file_get_contents('php://input');
		$handle = new handlers('hwVisualPhone','onCallState');
		$result = $handle->getHandle(array('jsonBody'=>$jsonBody));
		return ;
	}

	public function calltest(){
		$handle = new handlers('hwVisualPhone','bind');
		$result = $handle->getHandle(array('relationPhone'=>'17152381855','caller'=>'13379462155','callee'=>'17341390521','duration'=>3600));
		return $result;
	}	

	public function callstop(){
		$sessionId = '1202_8883_4294967295_20191115021457@callenabler245hlj.huaweicaas.com';
		$handle = new handlers('hwVisualPhone','callStop');
		$result = $handle->getHandle(array('sessionId'=>$sessionId));
	}

	/**
		* @brief 楼盘详情页临时分配虚拟号码
		*
		* @return 
	 */
	public function getTempVisualPhone(){
		global $dsql;
		global $langData;
		if(empty($this->param["itemid"]) && empty($this->param['type'])){
			return array("state" => 101, "info" => 'Param Invalid!');
		}
		$table = "";
		switch($this->param['type']){
			case "sale": $table="house_sale";break;
			case "zu" : $table="house_zu";break;
			case "sp" : $table="house_sp";break;
			case "xzl": $table="house_xzl";break;
			case "cf" : $table="house_cf";break;
			case "loupan": $table="house_loupan";break;
		}
		$zjPhone = '';
		if($this->param['id']){	//房源有经纪人
			$zjInfo = $userLogin->getMemberInfo($this->param['id']);
			$zjPhone = preg_replace("/[^0-9]/",'',$zjInfo['phone']);
		}else{
			//查询经纪人联系方式
			$sql = $dsql->SetQuery("select id,contact from #@__{$table} where id={$this->param['itemid']}");
			$result = $dsql->dsqlOper($sql, "results");
			if($result && !isset($result['state'])){
				$zjInfo = $result[0];
				$zjPhone = preg_replace("/[^0-9]/",'',$zjInfo['contact']);
			}
		}
		//没有联系方式的返回固定的真实电话
		if(!$zjPhone)	$zjPhone = '18580105610';	
		$sql = $dsql->SetQuery("select * from #@__phonebind where now()<=expire and state=1 and callee='{$zjPhone}'");
		$isPhoneHasBound = $dsql->dsqlOper($sql,'results');
		if(!$isPhoneHasBound || isset($isPhoneHasBound['state'])){
			$visualPhone = getVisualPhone($zjPhone,'',0);
			if($visualPhone){
				$handler = new handlers('hwVisualPhoneAX','bind');
				$result = $handler->getHandle(array('relationPhone'=>$visualPhone,'callee'=>$zjPhone));
				if($result)	return ['phone'=>$visualPhone];
			}
		}
		if($isPhoneHasBound && !isset($isPhoneHasBound['state'])){
			return ['phone'=>$isPhoneHasBound[0]['relationphone']];
		}
		
		return null;
	}
	
	/**
		* @brief 获取爬取数据中未被经纪人认领的数据
		*
		* @return 
	 */
	public function getFreeHouseList(){
        global $dsql;
        global $userLogin;
        $pageinfo = array();
        $type = $page = $pageSize = $cityId = 0;

        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//格式错误！
            }else{
                $page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
				$type = empty($this->param['type']) ? 0 : $this->param['type'];
				$cityId = empty($this->param['cityId']) ? 0 : $this->param['cityId'];
            }
        }
		if(!$type) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//格式错误！
        $uid = $this->param['userid'] ? $this->param['userid'] : $userLogin->getMemberID();

        if(!is_numeric($uid)) return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时，请重新登录！

        $pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;
		$table = $fields = '';
		switch($type){
		case 1:
			$table = 'house_loupan';
			$fields = 'id,title,price,addr,addrid,litpic,null area,salestate,1 usertype';
			break;
		case 2:
			$table = 'house_sale';
			$fields = 'id,title,price,unitprice,address,addrid,litpic,area,usertype,room,hall,guard';
			break;
		case 4:
			$table = 'house_zu';
			$fields = 'id,title,price,address,addrid,litpic,area,rentype,usertype,room,hall,guard';
			break;
		case 8:
			$table = 'house_sp';
			$fields = 'id,title,price,address,addrid,litpic,area,type,usertype';
			break;
		case 16:
			$table = 'house_xzl';
			$fields = 'id,title,price,address,addrid,litpic,area,type,usertype,zhuangxiu';
			break;
		case 32:
			$table = 'house_cf';
			$fields = 'id,title,price,address,addrid,litpic,area,type,usertype';
			break;
		}
		$where = "state=1 and externalno!='' and (userid='' or userid=0)";
		if($cityId)	$where .= " and cityId in ({$cityId})";
		if($type == 1)	$where .= " and hot=1";
		$orderby = "weight desc";
        $archives = $dsql->SetQuery("select {$fields} from #@__{$table} where {$where} order by {$orderby}");
		echo $archives;
        //总条数
        $totalCount = $dsql->dsqlOper($archives, "totalCount");

        if($totalCount == 0) return array("state" => 200, "info" => self::$langData['siteConfig'][21][64]);//暂无数据！

        //总分页数
        $totalPage = ceil($totalCount/$pageSize);

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount,
        );

        $atpage = $pageSize*($page-1);
        $results = $dsql->dsqlOper($archives." LIMIT $atpage, $pageSize", "results");

        $uinfo = $userLogin->getMemberInfo();
        if($results){
            foreach($results as $key => $val){
                $results[$key]['litpic']  = getFilePath($val['litpic']);
            }
        }

        return array("pageInfo" => $pageinfo, "list" => $results);
    }
	
	public function wxShare(){
		global $userLogin;
		global $dsql;
		global $langData;
		if(empty($this->param['aid']) || empty($this->param['type']) || !$this->param['aid'] || !$this->param['type'])
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);//格式错误！
		$uid = $userLogin->getMemberID();
		if(!$uid || $uid == -1){
			return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);//登录超时！
		}
		$table = "";
		switch($this->param['type']){
			case 1:$table = "house_loupan";break;
			case 2:$table = "house_sale";break;
			case 3:$table = "house_zu";break;
			case 4:$table = "house_sp";break;
			case 5:$table = "house_xzl";break;
			case 6:$table = "house_cf";break;
		}
		//检查房源和用户是否一致
		/*$checkSql = $dsql->SetQuery("select * from #@__{$table} h inner join #@__house_zjuser z on h.userid=z.id inner join #@__member m on z.userid=m.id where h.id={$this->param['aid']} and m.id={$uid}");
		$result = $dsql->dsqlOper($checkSql,'results');
		if(!$result){
			return ['state'=>200,'info'=>"身份和房源信息核对失败！"];
		}*/
		//查询记录是否存在
		$sql = $dsql->SetQuery("select id from #@__wechat_share where userid={$uid} and aid={$this->param['aid']} and type={$this->param['type']}");
		$isExists = $dsql->dsqlOper($sql,"results");
		$date = date("Y-m-d H:i:s");
		if(!$isExists){
			$insertSql = $dsql->SetQuery("insert into #@__wechat_share(userid,aid,type,state,date) values({$uid},{$this->param['aid']},{$this->param['type']},1,'{$date}')");
			$result = $dsql->dsqlOper($insertSql, "lastid");
		}else{
			$updateSql = $dsql->SetQuery("update #@__wechat_share set shareTimes=shareTimes+1 where id={$isExists[0]['id']}");
			$dsql->dsqlOper($updateSql,"update");
		}

		return true;
	}

	public function getWxShareData(){
		//查询微信分享的统计数据
		global $userLogin;
		global $dsql;
		$uid = $userLogin->getMemberID();
		if(!$uid || $uid == -1){
			return ['state'=>200,'info'=>'login timeout!'];
		}
		$userInfo = $userLogin->getMemberInfo($uid);
		if($userInfo['usertype'] == 0 && ($userInfo['level'] == 0 or $userInfo['expired'] < time())){
			return ['state'=>200,'info'=>'permission denied!'];
		}
		$date = date('Y-m-d') . ' 00:00:00';
		$recentThreeDays = date('Y-m-d', strtotime(date('Y-m-d').' -2 day')) . ' 00:00:00';
		$recentAWeek = date('Y-m-d', strtotime(date('Y-m-d').' -6 day')) . ' 00:00:00';
		$sql = <<<EOT
-- share
select A.title,sum(A.shareTimes) totalTimes,A.tags from (
select 'shareTime' title,shareTimes,(case when type<=6 then 6 else type end) tags from huoniao_wechat_share where userid={$uid}
) A GROUP BY A.tags
union All
-- scan
select B.title,sum(B.clickTimes) totalTimes,B.tags from (
select 'scanTime' title,clickTimes,(case when s.type<=6 then 6 else type end) tags from huoniao_wechat_clickrecord cr inner join huoniao_wechat_share s on cr.sid=s.id 
where s.userid={$uid} and cr.date>='{$date}'
) B GROUP BY B.tags
union All
-- customer
select C.title,count(C.openid) totalTimes,C.tags from (
select 'visitorNumber' title,openid,(case when s.type<=6 then 6 else type end) tags from huoniao_wechat_clickrecord cr inner join huoniao_wechat_share s on cr.sid=s.id 
where s.userid={$uid} and cr.date>='{$date}'
) C GROUP BY C.tags
union All
-- 今日新增访客数
select 'visitorsInDay',count(openid) customNum,0 from huoniao_wechat_clickrecord cr inner join huoniao_wechat_share s on cr.sid=s.id 
where s.userid={$uid} and cr.date>='{$date}'
union ALL
-- 最近三天访客数
select 'visitorsInThreeDays',count(openid) customNum,0 from huoniao_wechat_clickrecord cr inner join huoniao_wechat_share s on cr.sid=s.id 
where s.userid={$uid} and cr.date>='{$recentThreeDays}'
union All
-- 最近七天访客数
select 'visitorsInWeek',count(openid) customNum,0 from huoniao_wechat_clickrecord cr inner join huoniao_wechat_share s on cr.sid=s.id 
where s.userid={$uid} and cr.date>='{$recentAWeek}'
EOT;
		$sql = $dsql->SetQuery($sql);
		$result = $dsql->dsqlOper($sql,'results');
		$data = [];
		//按类别的键值处理数据
		foreach ($result as $key=>$value){
			$data[$value['tags']][] = &$result[$key];
		}
		return $data;
	}
	
	public function test(){
		return getAddridByPhone('17341390521');
	}
}
