<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 商家模块API接口
 *
 * @version        $Id: business.class.php 2017-3-23 上午12:01:21 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class business {
	private $param;  //参数

	/**
     * 构造函数
	 *
     * @param string $action 动作名
     */
    public function __construct($param = array()){
		$this->param = $param;
	}

	/**
     * 新闻基本参数
     * @return array
     */
	public function config(){

		require(HUONIAOINC."/config/business.inc.php");

		global $cfg_fileUrl;              //系统附件默认地址
		global $cfg_uploadDir;            //系统附件默认上传目录
		// global $customFtp;                //是否自定义FTP
		// global $custom_ftpState;          //FTP是否开启
		// global $custom_ftpUrl;            //远程附件地址
		// global $custom_ftpDir;            //FTP上传目录
		// global $custom_uploadDir;         //默认上传目录
		global $cfg_basehost;             //系统主域名

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
		global $cfg_hotline;           //咨询热线配置
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
		if(is_array($siteCityInfo)){
			$cityName = $siteCityInfo['name'];
		}

		//如果上传设置为系统默认，则以下参数使用系统默认
		if($customUpload == 0){
			$custom_softSize = $cfg_softSize;
			$custom_softType  = $cfg_softType;
			$custom_thumbSize = $cfg_thumbSize;
			$custom_thumbType = $cfg_thumbType;
			$custom_atlasSize = $cfg_atlasSize;
			$custom_atlasType = $cfg_atlasType;
		}

		$params = !empty($this->param) && !is_array($this->param) ? explode(',',$this->param) : "";

		// $domainInfo = getDomain('article', 'config');
		// $customChannelDomain = $domainInfo['domain'];
		// if($customSubDomain == 0){
		// 	$customChannelDomain = "http://".$customChannelDomain;
		// }elseif($customSubDomain == 1){
		// 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
		// }elseif($customSubDomain == 2){
		// 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
		// }

		// include HUONIAOINC.'/siteModuleDomain.inc.php';
		$customChannelDomain = getDomainFullUrl('business', $customSubDomain);

        //分站自定义配置
        $ser = 'business';
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
		if(!empty($params) > 0){

			foreach($params as $key => $param){
				if($param == "channelName"){
					$return['channelName'] = str_replace('$city', $cityName, $customChannelName);
				}elseif($param == "logoUrl"){

					//自定义LOGO
					if($customLogo == 1){
						$customLogoPath = getFilePath($customLogoUrl);
					}else{
						$customLogoPath = getFilePath($cfg_weblogo);
					}

					$return['logoUrl'] = $customLogoPath;
				}elseif($param == "subDomain"){
					$return['subDomain'] = $customSubDomain;
				}elseif($param == "channelDomain"){
					$return['channelDomain'] = $customChannelDomain;
				}elseif($param == "channelSwitch"){
					$return['channelSwitch'] = $customChannelSwitch;
				}elseif($param == "closeCause"){
					$return['closeCause'] = $customCloseCause;
				}elseif($param == "title"){
					$return['title'] = str_replace('$city', $cityName, $customSeoTitle);
				}elseif($param == "keywords"){
					$return['keywords'] = str_replace('$city', $cityName, $customSeoKeyword);
				}elseif($param == "description"){
					$return['description'] = str_replace('$city', $cityName, $customSeoDescription);
				}elseif($param == "agreement"){
					$return['agreement'] = str_replace('$city', $cityName, $customAgreement);
				}elseif($param == "businessTag"){
					$businessTag_ = array();
					if($customBusinessTag){
						$arr = explode("\n", $customBusinessTag);
						foreach ($arr as $k => $v) {
							$arr_ = explode('|', $v);
							foreach ($arr_ as $s => $r) {
								if(trim($r)){
									$businessTag_[] = trim($r);
								}
							}
						}
					}
					$return['businessTag'] = $businessTag_;
				}elseif($param == "cost"){
					$return['cost'] = unserialize($customCost);
				}elseif($param == "submission"){
					$return['submission'] = $submission;
				}elseif($param == "atlasMax"){
					$return['atlasMax'] = $customAtlasMax;
				}elseif($param == "template"){
					$return['template'] = $customTemplate;
				}elseif($param == "touchTemplate"){
					$return['touchTemplate'] = $customTouchTemplate;
				}elseif($param == "softSize"){
					$return['softSize'] = $custom_softSize;
				}elseif($param == "softType"){
					$return['softType'] = $custom_softType;
				}elseif($param == "thumbSize"){
					$return['thumbSize'] = $custom_thumbSize;
				}elseif($param == "thumbType"){
					$return['thumbType'] = $custom_thumbType;
				}elseif($param == "atlasSize"){
					$return['atlasSize'] = $custom_atlasSize;
				}elseif($param == "atlasType"){
					$return['atlasType'] = $custom_atlasType;
				}elseif($param == "listRule"){
					$return['listRule'] = $custom_listRule;
				}elseif($param == "detailRule"){
					$return['detailRule'] = $custom_detailRule;
				}elseif($param == "trialAutoAudit"){
					$return['trialAutoAudit'] = (int)$custom_trialAutoAudit;
				}elseif($param == "enterpriseAutoAudit"){
					$return['enterpriseAutoAudit'] = (int)$custom_enterpriseAutoAudit;
				}
			}

		}else{

			//自定义LOGO
			if($customLogo == 1){
				$customLogoPath = getFilePath($customLogoUrl);
			}else{
				$customLogoPath = getFilePath($cfg_weblogo);
			}

			$return['channelName']         = str_replace('$city', $cityName, $customChannelName);
			$return['logoUrl']             = $customLogoPath;
			$return['subDomain']           = $customSubDomain;
			$return['channelDomain']       = $customChannelDomain;
			$return['channelSwitch']       = $customChannelSwitch;
			$return['closeCause']          = $customCloseCause;
			$return['title']               = str_replace('$city', $cityName, $customSeoTitle);
			$return['keywords']            = str_replace('$city', $cityName, $customSeoKeyword);
			$return['description']         = str_replace('$city', $cityName, $customSeoDescription);
			$return['agreement']           = str_replace(' ', '&nbsp;', str_replace("\r\n", '<br>', str_replace('$city', $cityName, $customAgreement)));
			$return['cost']                = unserialize($customCost);
			$return['submission']          = $submission;
			$return['atlasMax']            = $customAtlasMax;
			$return['template']            = $customTemplate;
			$return['touchTemplate']       = $customTouchTemplate;
			$return['softSize']            = $custom_softSize;
			$return['softType']            = $custom_softType;
			$return['thumbSize']           = $custom_thumbSize;
			$return['thumbType']           = $custom_thumbType;
			$return['atlasSize']           = $custom_atlasSize;
			$return['atlasType']           = $custom_atlasType;
			$return['listRule']            = $custom_listRule;
			$return['detailRule']          = $custom_detailRule;
			$return['trialState']          = (int)$custom_trialState;
			$return['trialAutoAudit']      = (int)$custom_trialAutoAudit;
			$return['trialName']           = $custom_trialName;
			$return['trialCost']           = empty($custom_trialCost) ? array() : unserialize($custom_trialCost);
			$return['enterpriseState']     = (int)$custom_enterpriseState;
			$return['enterpriseAutoAudit'] = (int)$custom_enterpriseAutoAudit;
			$return['enterpriseName']      = $custom_enterpriseName;
			$return['enterpriseCost']      = empty($custom_enterpriseCost) ? array() : unserialize($custom_enterpriseCost);
			$return['joinAuth']            = empty($custom_joinAuth) ? array() : unserialize($custom_joinAuth);

			$businessTag_ = array();
			if($customBusinessTag){
				$arr = explode("\n", $customBusinessTag);
				foreach ($arr as $k => $v) {
					$arr_ = explode('|', $v);
					foreach ($arr_ as $s => $r) {
						if(trim($r)){
							$businessTag_[] = trim($r);
						}
					}
				}
			}
			$return['businessTag'] = $businessTag_;
		}

		return $return;

	}


	/**
     * 商家分类
     * @return array
     */
	public function type(){
		global $dsql;
		$type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$type     = (int)$this->param['type'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}
		$results = $dsql->getTypeList($type, "business_type", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 商家区域
     * @return array
     */
	public function addr(){
		global $dsql;
		$type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$type     = (int)$this->param['type'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}

		global $template;
		if($template && $template != 'page' && empty($type)){
			$type = getCityId();
		}

		//一级
		if(empty($type)){
            //可操作的城市，多个以,分隔
            $userLogin = new userLogin($dbo);
            $adminCityIds = $userLogin->getAdminCityIds();
            $adminCityIds = empty($adminCityIds) ? 0 : $adminCityIds;

			$cityArr = array();
			$sql = $dsql->SetQuery("SELECT c.*, a.`id` cid, a.`typename`, a.`pinyin` FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` WHERE c.`cid` in ($adminCityIds) ORDER BY c.`id`");
			$result = $dsql->dsqlOper($sql, "results");
			if($result){
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

		}else{
			$results = $dsql->getTypeList($type, "site_area", $son, $page, $pageSize, '', '', true);
			if($results){
				return $results;
			}
		}
	}


	/**
     * 商家列表
     * @return array
     */
	public function blist(){
		global $dsql;
		global $userLogin;
		global $langData;
		$pageinfo = $list = array();
		$store = $addrid = $typeid = $title = $orderby = $page = $pageSize = $where = $where1 = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$store    = $this->param['store'];
				$addrid   = $this->param['addrid'];
				$typeid   = $this->param['typeid'];
				$title    = $this->param['title'];
				$keywords = $this->param['keywords'];
				$max_longitude = $this->param['max_longitude'];
				$min_longitude = $this->param['min_longitude'];
				$max_latitude  = $this->param['max_latitude'];
				$min_latitude  = $this->param['min_latitude'];
                $lng      = $this->param['lng'];
                $lat      = $this->param['lat'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];

				$title = $title ? $title : $keywords;
			}
		}
		// 自动审核的情况下名称可能为空
		$cityid = getCityId($this->param['cityid']);
		//遍历区域
        if($cityid){
            $where .= " AND `cityid` = ".$cityid;
        }

        //区分企业版和体验版
        if($store){
            $where .= " AND `type` = " . $store;
        }

        $now = time();

        $where .= " AND `cityid` != 0 AND `title` != ''";

		//遍历区域
		if(!empty($addrid)){
			$addrList = $dsql->getTypeList($addrid, "site_area");
			if($addrList){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($addrList);
				$lower = $addrid.",".join(',',$lower);
			}else{
				$lower = $addrid;
			}
			$where .= " AND `addrid` in ($lower)";
		}

		//遍历分类
		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "business_type")){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($dsql->getTypeList($typeid, "business_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}
			$where .= " AND `typeid` in ($lower)";
		}

		//模糊查询关键字
		if(!empty($title)){
			$title = explode(" ", $title);
			$w = array();
			foreach ($title as $k => $v) {
				if(!empty($v)){
					$w[] = "`title` like '%".$v."%'";
				}
			}
			$where .= " AND (".join(" OR ", $w).")";
		}

		//地图可视区域内
		if(!empty($max_longitude) && !empty($min_longitude) && !empty($max_latitude) && !empty($min_latitude)){
			$where .= " AND `lng` <= '".$max_longitude."' AND `lng` >= '".$min_longitude."' AND `lat` <= '".$max_latitude."' AND `lat` >= '".$min_latitude."'";
		}

        //查询距离
        if((!empty($lng))&&(!empty($lat))){
            $select="ROUND(
		        6378.138 * 2 * ASIN(
		            SQRT(POW(SIN(($lat * PI() / 180 - l.`lat` * PI() / 180) / 2), 2) + COS($lat * PI() / 180) * COS(l.`lat` * PI() / 180) * POW(SIN(($lng * PI() / 180 - l.`lng` * PI() / 180) / 2), 2))
		        ) * 1000
		    ) AS distance,";
            $select="(2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*(".$lat."-l.`lat`)/360),2)+COS(3.1415926535898*".$lat."/180)* COS(l.`lat` * 3.1415926535898/180)*POW(SIN(3.1415926535898*(".$lng."-l.`lng`)/360),2))))*1000 AS distance,";

        }else{
            $select="";
        }

		$order = " ORDER BY l.isbid DESC, l.`id` DESC";


		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		//人气
		if($orderby == "1"){
			$order = " ORDER BY popularity DESC";

		//好评
		}elseif($orderby == "2"){
			$order = " ORDER BY praise DESC";

        //距离
        }elseif($orderby == "3"){
            $order = " ORDER BY distance ASC";
        }elseif($orderby == "4"){
        	$order = " ORDER BY pubdate DESC";
        }


		$archives = $dsql->SetQuery("SELECT l.`id`, l.`uid`, l.`isbid`, l.`title`, l.`logo`, l.`typeid`, l.`addrid`, l.`address`, l.`lng`, l.`lat`, l.`wechatname`, l.`wechatcode`, l.`wechatqr`, l.`tel`, l.`qq`, l.`email`, l.`pics`, l.`license`, l.`opentime`, l.`amount`, l.`parking`, l.`authattr`, l.`pubdate`, l.`type`, l.`qj_file`, l.`video`, l.`banner`,".$select." (SELECT COUNT(`id`)  FROM `#@__public_comment` c WHERE c.`aid` = l.`id` AND c.`type` = 'business' AND `ischeck` = 1) AS popularity, (SELECT COUNT(`id`) FROM `#@__public_comment` c WHERE c.`aid` = l.`id` AND c.`type` = 'business' AND `ischeck` = 1 AND `rating` = 1) AS praise FROM `#@__business_list` l WHERE l.`state` = 1 AND (l.`expired` = 0 || l.`expired` > ".$now.")".$where);
		$archives_count = $dsql->SetQuery("SELECT count(`id`) FROM `#@__business_list` l WHERE l.`state` = 1 AND (l.`expired` = 0 || l.`expired` > ".$now.")".$where);

		//总条数
		$totalResults = $dsql->dsqlOper($archives_count, "results", "NUM");
		$totalCount = (int)$totalResults[0][0];

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
		$results = $dsql->dsqlOper($archives.$where1.$order.$where, "results");

		if($results){
			$i = 0;
			foreach($results as $key => $val){

				$list[$i]['id']    = $val['id'];
				$list[$i]['title'] = $val['title'];
				$list[$i]['type'] = $val['type'];
				$list[$i]['isbid'] = $val['isbid'];
				$list[$i]['logo']  = !empty($val['logo']) ? getFilePath($val['logo']) : "";

				global $data;
				$data = "";
				$typeArr = getParentArr("business_type", $val['typeid']);
				$typeArr = array_reverse(parent_foreach($typeArr, "typename"));
				$list[$i]['typeid']      = $val['typeid'];
				$list[$i]['typename']    = $typeArr;

				global $data;
				$data = "";
				$addrArr = getParentArr("site_area", $val['addrid']);
				$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
				$list[$i]['addrid']      = $val['typeid'];
				$list[$i]['addrname']    = $addrArr;

				$list[$i]['address']    = $val['address'];
				$list[$i]['lng']        = $val['lng'];
				$list[$i]['lat']        = $val['lat'];
				$list[$i]['distance']   = $val['distance'] > 1000 ? sprintf("%.1f", $val['distance'] / 1000) . $langData['siteConfig'][13][23] : sprintf("%.1f", $val['distance']) . $langData['siteConfig'][13][22];  //距离   //千米  //米
				$list[$i]['wechatname'] = $val['wechatname'];
				$list[$i]['wechatcode'] = $val['wechatcode'];
				$list[$i]['wechatqr']   = !empty($val['wechatqr']) ? getFilePath($val['wechatqr']) : "";

				$tel = $val['tel'] ? explode(',', $val['tel']) : array();
				$qq = $val['qq'] ? explode(',', $val['qq']) : array();
				$email = $val['email'] ? explode(',', $val['email']) : array();

				$list[$i]['tel']        = $tel ? $tel[0] : "";
				$list[$i]['qq']         = $qq ? $qq[0] : "";
				$list[$i]['email']      = $email ? $email[0] : "";
				$list[$i]['telArr']     = $tel;
				$list[$i]['qqArr']      = $qq;
				$list[$i]['emailArr']   = $email;
				$list[$i]['face_qj']    = $val['qj_file'] ? 1 : 0;
				$list[$i]['face_video'] = $val['video'] ? 1 : 0;

				$picArr = array();
				$pics = $val['pics'];
				if($pics){
					$pics = explode(",", $pics);
					foreach ($pics as $k => $v) {
						array_push($picArr, getFilePath($v));
					}
				}
				$list[$i]['pics']    = $picArr;
				$list[$i]['license'] = !empty($val['license']) ? getFilePath($val['license']) : "";
				$list[$i]['opentime'] = $val['opentime'];
				$list[$i]['amount']   = $val['amount'];
				$list[$i]['parking']  = $val['parking'];
				$list[$i]['pubdate']  = $val['pubdate'];
				$list[$i]['comment']  = $val['popularity'];

				//认证
				$auth = array();
				if($val['authattr']){
					$authattr = explode(",", $val['authattr']);
					foreach ($authattr as $k => $v) {
						$sql = $dsql->SetQuery("SELECT `jc`, `typename` FROM `#@__business_authattr` WHERE `id` = $v");
						$ret = $dsql->dsqlOper($sql, "results");
						if($ret){
							array_push($auth, array("jc" => $ret[0]['jc'], "typename" => $ret[0]['typename']));
						}
					}
				}
				$list[$i]["auth"] = $auth;

				//全景数量
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_panor` WHERE `uid` = " . $val['id']);
				$panorCount = $dsql->dsqlOper($sql, 'totalCount');
				$list[$i]["panor"] = $panorCount;

				//视频数量
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_video` WHERE `uid` = " . $val['id']);
				$videoCount = $dsql->dsqlOper($sql, 'totalCount');
				$list[$i]["video"] = $videoCount;

				//会员信息
				$uinfo = $userLogin->getMemberInfo($val['uid']);
				if(is_array($uinfo)){
					$list[$i]['member'] = array(
						"userid"       => $uinfo['userid'],
						"company"      => $uinfo['company'],
						"photo"        => $uinfo['photo'],
						"online"       => $uinfo['online'],
						"emailCheck"   => $uinfo['emailCheck'],
						"phoneCheck"   => $uinfo['phoneCheck'],
						"licenseState" => $uinfo['licenseState'],
						"certifyState" => $uinfo['certifyState'],
						"promotion"    => $uinfo['promotion'],
					);
				}

                $bannerArr = array();
                $banner    = $val['banner'];
                if (!empty($banner)) {
                    $banner = explode(",", $banner);
                    foreach ($banner as $key => $value) {
                        array_push($bannerArr, array("pic" => getFilePath($value), "picSource" => $value));
                    }
                    //如果没有维护banner，调相册前5张
                }else{
                    $sql = $dsql->SetQuery("SELECT `litpic` FROM `#@__business_albums` WHERE `uid` = ".$val['id']." ORDER BY `id` DESC LIMIT 0, 5");
                    $ret  = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        foreach ($ret as $key => $v){
                            array_push($bannerArr, array("pic" => getFilePath($v['litpic']), "picSource" => $v['litpic']));
                        }
                    }
                }
                $list[$i]['banner'] = $bannerArr;

				//综合评分
				$sql = $dsql->SetQuery("SELECT avg(`rating`) r, count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `aid` = ".$val['id']." AND `pid` = 0 AND `type` = 'business'");
				$res = $dsql->dsqlOper($sql, "results");
				$rating = $res[0]['r'];		//总评分
				$list[$i]['rating']  = number_format($rating, 1);

				$list[$i]['comment'] = $res[0]['c'];

				// 默认URL
				$param = array(
					"service"     => "business",
					"template"    => "detail",
					"id"          => $val['id']
				);
				$list[$i]['url'] = getUrlPath($param);

				$url = "";
				if(strpos($val['bind_module'], "waimai") !== false){
					$sql = $dsql->SetQuery("SELECT m.`shopid` FROM `#@__waimai_shop_manager` m LEFT JOIN `#@__waimai_shop` s ON s.`id` = m.`shopid` WHERE s.`del` = 0 AND m.`userid` = ".$val['uid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$list[$i]['waimai'] = 1;
						$param = array(
							"service" => "waimai",
							"template" => "shop",
							"id" => $ret[0]['shopid']
						);
						$url = getUrlPath($param);
					}
				}
				$list[$i]['waimaiUrl'] = $url;


				//点评
				$sql                    = $dsql->SetQuery("SELECT avg(`sco1`) r, count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $val['id'] . " AND `pid` = 0");
				$res                    = $dsql->dsqlOper($sql, "results");
				$comment                = $res[0]['c'];    //点评数量
				$sco1                   = $res[0]['r'];    //总评分
				$list[$i]['comment'] = $comment;
				$list[$i]['sco1']    = number_format($sco1, 1);

				$sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $val['id'] . " AND `sco1` = 1 AND `pid` = 0");
				$res                    = $dsql->dsqlOper($sql, "results");
				$list[$i]['sco1_1'] = (int)$res[0][c];
				$sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $val['id'] . " AND `sco1` = 2 AND `pid` = 0");
				$res                    = $dsql->dsqlOper($sql, "results");
				$list[$i]['sco1_2'] = (int)$res[0][c];
				$sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $val['id'] . " AND `sco1` = 3 AND `pid` = 0");
				$res                    = $dsql->dsqlOper($sql, "results");
				$list[$i]['sco1_3'] = (int)$res[0][c];
				$sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $val['id'] . " AND `sco1` = 4 AND `pid` = 0");
				$res                    = $dsql->dsqlOper($sql, "results");
				$list[$i]['sco1_4'] = (int)$res[0][c];
				$sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $val['id'] . " AND `sco1` = 5 AND `pid` = 0");
				$res                    = $dsql->dsqlOper($sql, "results");
				$list[$i]['sco1_5'] = (int)$res[0][c];

				$i ++;
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 商家详细
     * @return array
     */
	public function storeDetail(){
		global $dsql;
		global $userLogin;
		global $langData;
		$storeDetail = array();
		$id = $this->param;
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id) && $uid == -1){
			return array("state" => 200, "info" => '格式错误！');
		}

		$where = " AND `state` = 1";
		if(!is_numeric($id)){
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = ".$uid);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$id = $results[0]['id'];
				$where = "";
			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][21][119]);  //该会员暂未开通商铺！
			}
		}

		$where .= " AND `expired` > ".GetMkTime(time());

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_list` WHERE `id` = ".$id.$where);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results) {

            $storeDetail["id"]    = $results[0]['id'];
            $storeDetail["uid"]   = $results[0]['uid'];
            $storeDetail["title"] = $results[0]['title'];
            $storeDetail["type"]  = $results[0]['type'];
            $storeDetail["click"]  = $results[0]['click'];

            $storeDetail["logoSource"] = $results[0]["logo"];
			$storeDetail["logo"]       = getFilePath($results[0]["logo"]);

			$storeDetail["mappicSource"] = $results[0]["mappic"];
            $storeDetail["mappic"]       = getFilePath($results[0]["mappic"]);

            $uid                   = $results[0]['uid'];
            $storeDetail['member'] = getMemberDetail($uid);

            $storeDetail["typeid"] = $results[0]['typeid'];
            global $data;
            $data    = "";
            $bustype = getParentArr("business_type", $results[0]['typeid']);
            if ($bustype) {
                $bustype                 = array_reverse(parent_foreach($bustype, "typename"));
                $storeDetail['typename'] = join(" > ", $bustype);
                $storeDetail['typenameArr'] = $bustype;
            } else {
                $storeDetail['typename'] = "";
                $storeDetail['typenameArr'] = array();
            }

            $storeDetail["addrid"] = $results[0]['addrid'];
            $storeDetail["cityid"] = $results[0]['cityid'];
            $addrName              = getParentArr("site_area", $results[0]['addrid']);
            global $data;
            $data                    = "";
            $addrArr                 = array_reverse(parent_foreach($addrName, "typename"));
            $addrArr                 = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
            $storeDetail['addrname'] = $addrArr;
            $storeDetail['addrnameList'] = join(" > ", $addrArr);

            $storeDetail["address"]    = $results[0]['address'];
            $storeDetail["lng"]        = $results[0]['lng'];
            $storeDetail["lat"]        = $results[0]['lat'];
            $storeDetail["lnglat"]     = $results[0]['lng'] . "," . $results[0]['lat'];
            $storeDetail["wechatname"] = $results[0]['wechatname'];
            $storeDetail["wechatcode"] = $results[0]['wechatcode'];

            $storeDetail["wechatqrSource"] = $results[0]['wechatqr'];
            $storeDetail["wechatqr"]       = getFilePath($results[0]["wechatqr"]);

            $tel = $results[0]['tel'] ? explode(",", $results[0]['tel']) : array();
            $qq = $results[0]['qq'] ? explode(",", $results[0]['qq']) : array();
            $email = $results[0]['email'] ? explode(",", $results[0]['email']) : array();
			$storeDetail["tel"]      = $tel ? $tel[0] : "";
			$storeDetail["qq"]       = $qq ? $qq[0] : "";
			$storeDetail["email"]    = $email ? $email[0] : "";
			$storeDetail["telArr"]   = $tel;
			$storeDetail["qqArr"]    = $qq;
			$storeDetail["emailArr"] = $email;

			$storeDetail["videoSource"]     = $results[0]['video'];
			$storeDetail["video"]           = getFilePath($results[0]["video"]);
			$storeDetail["video_picSource"] = $results[0]["video_pic"];
			$storeDetail["video_pic"]       = $results[0]["video_pic"] ? getFilePath($results[0]["video_pic"]) : "";

            // 全景
			$storeDetail["qj_type"]   = $results[0]['qj_type'];
			$storeDetail["qj_file"]   = $results[0]['qj_file'];

			$storeDetail["tag"]       = $results[0]['tag'];

			$tagArr = array();
			$tagArr_ = $results[0]['tag'] ? explode('|', $results[0]['tag']) : array();
			if($tagArr_){
				foreach ($tagArr_ as $k => $v) {
					$tagArr[$k] = array(
						"py" => GetPinyin($v),
						"val" => $v
					);
				}
			}
			$storeDetail["tagArr"] = $tagArr;

			$storeDetail["tag_shop"]    = $results[0]['tag_shop'];
			$storeDetail["tag_shopArr"] = $results[0]['tag_shop'] ? explode('|', $results[0]['tag_shop']) : array();

            $bannerArr = array();
            $banner    = $results[0]['banner'];
            if (!empty($banner)) {
                $banner = explode(",", $banner);
                foreach ($banner as $key => $value) {
                    array_push($bannerArr, array("pic" => getFilePath($value), "picSource" => $value));
                }
            //如果没有维护banner，调相册前5张
            }else{
                $archives = $dsql->SetQuery("SELECT `litpic` FROM `#@__business_albums` WHERE `uid` = $id ORDER BY `id` DESC LIMIT 0, 5");
                $res  = $dsql->dsqlOper($archives, "results");
                if($res){
                    foreach ($res as $key => $val){
                        array_push($bannerArr, array("pic" => getFilePath($val['litpic']), "picSource" => $val['litpic']));
                    }
                }
            }
            $storeDetail['banner'] = $bannerArr;

            $picsArr = array();
            $pics    = $results[0]['pics'];
            if (!empty($pics)) {
                $pics = explode(",", $pics);
                foreach ($pics as $key => $value) {
                    array_push($picsArr, array("pic" => getFilePath($value), "picSource" => $value));
                }
            }
            $storeDetail['pics'] = $picsArr;

            $storeDetail["jingyingSource"] = $results[0]["jingying"];
            $storeDetail["jingying"]       = getFilePath($results[0]["jingying"]);

            $picsArr = array();
            $pics    = $results[0]['certify'];
            if (!empty($pics)) {
                $pics = explode(",", $pics);
                foreach ($pics as $key => $value) {
                    array_push($picsArr, array("pic" => getFilePath($value), "picSource" => $value));
                }
            }
            $storeDetail['certify'] = $picsArr;

            $storeDetail["opentime"]         = str_replace(";", "-", $results[0]['opentime']);
            $storeDetail["amount"]           = $results[0]['amount'];
            $storeDetail["parking"]          = $results[0]['parking'];
            $storeDetail["state"]            = $results[0]['state'];
            $storeDetail["pubdate"]          = $results[0]['pubdate'];
            $storeDetail["name"]             = $results[0]['name'];
            $storeDetail["areaCode"]         = $results[0]['areaCode'];
            $storeDetail["phone"]            = $results[0]['phone'];
            $storeDetail["email"]            = $results[0]['email'];
            $storeDetail["cardnum"]          = $results[0]['cardnum'];
            $storeDetail["company"]          = $results[0]['company'];
            $storeDetail["licensenum"]       = $results[0]['licensenum'];
            $storeDetail["licenseSource"]    = $results[0]['license'];
            $storeDetail["license"]          = getFilePath($results[0]['license']);
            $storeDetail["accountsSource"]   = $results[0]['accounts'];
            $storeDetail["accounts"]         = getFilePath($results[0]['accounts']);
            $storeDetail["cardfrontSource"]  = $results[0]['cardfront'];
            $storeDetail["cardfront"]        = getFilePath($results[0]['cardfront']);
            $storeDetail["cardbehindSource"] = $results[0]['cardbehind'];
            $storeDetail["cardbehind"]       = getFilePath($results[0]['cardbehind']);
            $storeDetail["body"]             = $results[0]['body'];

			// 默认URL
			$param = array(
				"service"     => "business",
				"template"    => "detail",
				"id"          => $id
			);
			$storeDetail['url'] = getUrlPath($param);

            $weekDay = "";

			if($results[0]['weeks']){
				$spr = strstr($results[0]['weeks'], ';') ? ';' : ',';
				$value_ = explode($spr, $results[0]['weeks']);
				$weeks = array("周一","周二","周三","周四","周五","周六","周日");
				if(count($value_) == 1){
					$weekDay = $value_[0];
				}else{
					// $value_t = array();
					// foreach ($value_ as $k => $v) {
					// 	if($k == 0){
					// 		$value_t[0] = $weeks[$v-1];
					// 	}
					// 	if($k > 0 && $k + 1 == count($value_)){
					// 		$value_t[1] = $weeks[$v-1];
					// 	}
					// 	if($k > 0 && $v - $value_[$k-1] > 1){
					// 		$value_t[0] = $weeks[$v-1];
					// 		$value_t[1] = $weeks[$value_[0]-1];
					// 		break;
					// 	}
					// }
					$weekDay = $value_[0] ."至" . $value_[1];
				}
			}
            $storeDetail["weeks"]   = $results[0]['weeks'];
			$storeDetail["weekDay"] = $weekDay;


            //认证
            $auth = array();
            if ($results[0]['authattr']) {
				$authattr = explode(",", $results[0]['authattr']);
                foreach ($authattr as $k => $v) {
                    $sql = $dsql->SetQuery("SELECT `jc`, `typename` FROM `#@__business_authattr` WHERE `id` = $v");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if ($ret) {
                        array_push($auth, array("jc" => $ret[0]['jc'], "typename" => $ret[0]['typename']));
                    }
                }
            }
            $storeDetail["auth"] = $auth;

            //点评
			$sql                    = $dsql->SetQuery("SELECT avg(`sco1`) r, count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $id . " AND `pid` = 0");
			$res                    = $dsql->dsqlOper($sql, "results");
			$comment                = $res[0]['c'];    //点评数量
			$sco1                   = $res[0]['r'];    //总评分
			$storeDetail['comment'] = $comment;
			$storeDetail['sco1']    = number_format($sco1, 1);

			$storeDetail['intro'] = $results[0]['body'];

			// $sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $id . " AND `sco1` = 1 AND `pid` = 0");
			// $res                    = $dsql->dsqlOper($sql, "results");
			// $storeDetail['sco1_1'] = $res[0][c];
			// $sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $id . " AND `sco1` = 2 AND `pid` = 0");
			// $res                    = $dsql->dsqlOper($sql, "results");
			// $storeDetail['sco1_2'] = $res[0][c];
			// $sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $id . " AND `sco1` = 3 AND `pid` = 0");
			// $res                    = $dsql->dsqlOper($sql, "results");
			// $storeDetail['sco1_3'] = $res[0][c];
			// $sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $id . " AND `sco1` = 4 AND `pid` = 0");
			// $res                    = $dsql->dsqlOper($sql, "results");
			// $storeDetail['sco1_4'] = $res[0][c];
			// $sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $id . " AND `sco1` = 5 AND `pid` = 0");
			// $res                    = $dsql->dsqlOper($sql, "results");
			// $storeDetail['sco1_5'] = $res[0][c];

			// 带图
			$sql                    = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'business' AND `aid` = " . $id . " AND `pics` != '' AND `pid` = 0");
			$res                    = $dsql->dsqlOper($sql, "results");
			$storeDetail['comment_pic'] = $res[0][c];

            // 自定义导航
            $custom_nav = array();
			if($results[0]['custom_nav']){
				$value_ = explode("|", $results[0]['custom_nav']);
				foreach ($value_ as $k => $v) {
					$d = explode(',', $v);
					$custom_nav[$k] = array(
						'icon' => $d[0] ? getFilePath($d[0]) : "",
						'iconSource' => $d[0],
						'title' => $d[1],
						'url' => $d[2],
					);
				}
			}
			$storeDetail['custom_nav'] = $custom_nav;


            //查询商家已开通的店铺
			$bind_module = $results[0]['bind_module'];
        	$bind_module = empty($bind_module) ? array() : explode(",", $bind_module);

			$storeArr = $storeInfo = array();

			// 商家获取各个店铺的链接
			$store = checkShowModule($bind_module, 'show', '', 'getUrl', $id);

           	$storeDetail['store'] = $store;

           	foreach ($bind_module as $k => $v) {
           		if(!isset($store[$v])){
           			unset($bind_module[$k]);
           		}
           	}
           	$storeDetail["bind_module"] = $bind_module;

           	//查询商家餐饮服务设置
			$storeDetail["diancan_state"] = $results[0]['diancan_state'];
			$storeDetail["dingzuo_state"] = $results[0]['dingzuo_state'];
			$storeDetail["paidui_state"] = $results[0]['paidui_state'];
			$storeDetail["maidan_state"] = $results[0]['maidan_state'];
			$storeDetail["maidan_youhui_open"] = $results[0]['maidan_youhui_open'];
			$storeDetail["maidan_youhui_value"] = $results[0]['maidan_youhui_value'];

			$storeDetail["touch_skin"] = $results[0]['touch_skin'];

            // 输出到期时间
            if($uid == $results[0]['uid']){
            	$storeDetail['expired'] = $results[0]['expired'];

            	$now = GetMkTime(time());

            	if($results[0]['expired']){
        			$c = $results[0]['expired'] - $now;
            		if($c > 0){
	            		$days = floor(($c/86400)) + 1;
				        if($days < 30){
				        	$storeDetail['expiredFlor'] = $days."天";
				        }
				    }else{
				    	$storeDetail['expiredFlor'] = "已过期";
				    }
            	}
			}

            //验证是否已经收藏
			$params = array(
				"module" => "business",
				"temp"   => "detail",
				"type"   => "add",
				"id"     => $id,
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$storeDetail['collect'] = $collect == "has" ? 1 : 0;

			//是否有企业建站
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__website` where `userid` = " . $results[0]['uid']);
			$res = $dsql->dsqlOper($sql, "results");
			if(!empty($res[0]['id'])){
				$param = array(
					"service"      => "website",
					"template"     => "site".$res[0]['id']
				);
				$url = getUrlPath($param);
				$storeDetail["websiteUrl"] = $url;
			}

			//商家小程序码
			$param = array(
				"service"     => "business",
				"template"    => "detail",
				"id"          => $id
			);
			$burl = getUrlPath($param);

			$sql = $dsql->SetQuery("SELECT `id`, `fid` FROM `#@__site_wxmini_scene` WHERE `url` = '$burl'");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!empty($ret[0]['id'])){
				$storeDetail["businessQr"] = $ret[0]['fid'];
			}else{
				$storeDetail["businessQr"] = createWxMiniProgramScene($burl, HUONIAOROOT, true);
			}

            //print_r($storeDetail);die;
            return $storeDetail;
        }
	}


	/**
     * 商家介绍列表
     * @return array
     */
	public function introList(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$uid = $page = $pageSize = $where = $where1 = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$uid      = $this->param['uid'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if(!$uid){
			return array("state" => 200, "info" => '格式错误！');
		}

		$archives = $dsql->SetQuery("SELECT a.`id`, a.`title`, a.`body`, a.`click`, a.`pubdate`, b.`id` bid FROM `#@__business_about` a LEFT JOIN `#@__business_list` b ON b.`id` = a.`uid` WHERE a.`uid` = $uid ORDER BY a.`weight` DESC, a.`id` ASC");
		$results = $dsql->dsqlOper($archives, "results");

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']      = $val['id'];
				$list[$key]['title']   = $val['title'];
				$list[$key]['body']    = $val['body'];
				$list[$key]['click']   = $val['click'];
				$list[$key]['pubdate'] = $val['pubdate'];

				$param = array(
					"service"  => "business",
					"template" => "intro",
					"bid"      => $val['bid'],
					"id"       => $val['id']
				);
				$list[$key]['url'] = getUrlPath($param);
			}
		}

		return $list;
	}


	/**
     * 商家介绍详细
     * @return array
     */
	public function introDetail(){
		global $dsql;
		global $userLogin;
		$introDetail = array();
		$id = $this->param;

		if(!is_numeric($id)){
			return array("state" => 200, "info" => '格式错误！');
		}

		$archives = $dsql->SetQuery("SELECT a.*, b.`id` bid FROM `#@__business_about` a LEFT JOIN `#@__business_list` b ON b.`id` = a.`uid` WHERE a.`id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$introDetail["id"]      = $results[0]['id'];
			$introDetail["title"]   = $results[0]['title'];
			$introDetail["body"]    = $results[0]['body'];
			$introDetail["click"]   = $results[0]['click'];
			$introDetail["pubdate"] = $results[0]['pubdate'];
			$introDetail["bid"]     = $results[0]['bid'];
		}
		return $introDetail;
	}


	/**
     * 商家动态分类
     * @return array
     */
	public function news_type(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$uid = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$uid = $this->param['uid'];
			}
		}

		if(!$uid){
			return array("state" => 200, "info" => '格式错误！');
		}

		$archives = $dsql->SetQuery("SELECT t.`id`, t.`typename`, b.`id` bid FROM `#@__business_news_type` t LEFT JOIN `#@__business_list` b ON b.`id` = t.`uid` WHERE t.`uid` = $uid ORDER BY t.`weight` ASC");
		$results = $dsql->dsqlOper($archives, "results");

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']      = $val['id'];
				$list[$key]['typename']   = $val['typename'];
				$param = array(
					"service"  => "business",
					"template" => "news",
					"bid"      => $val['bid'],
					"id"       => $val['id']
				);
				$list[$key]['url'] = getUrlPath($param);
			}
		}

		return $list;
	}


	/**
     * 商家动态
     * @return array
     */
	 public function news_list(){
 		global $dsql;
 		global $userLogin;
		global $langData;
 		$pageinfo = $list = array();
 		$uid = $typeid = $page = $pageSize = $where = $where1 = "";

 		if(!empty($this->param)){
 			if(!is_array($this->param)){
 				return array("state" => 200, "info" => '格式错误！');
 			}else{
 				$uid      = $this->param['uid'];
 				$typeid   = $this->param['typeid'];
 				$page     = $this->param['page'];
 				$pageSize = $this->param['pageSize'];
 			}
 		}

		//会员ID
		if(empty($uid)){
			return array("state" => 200, "info" => '格式错误！');
		}

		$where .= " AND n.`uid` = $uid";

		//分类
		if(!empty($typeid)){
			$where .= " AND n.`typeid` = $typeid";
		}

 		$pageSize = empty($pageSize) ? 10 : $pageSize;
 		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT n.`id`, n.`typeid`, n.`title`, n.`click`, n.`pubdate`, l.`id` bid FROM `#@__business_news` n LEFT JOIN `#@__business_list` l ON l.`id` = n.`uid` WHERE 1 = 1".$where);

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
 		$results = $dsql->dsqlOper($archives.$where1.$order.$where, "results");

 		if($results){
 			foreach($results as $key => $val){
 				$list[$key]['id']    = $val['id'];
 				$list[$key]['title'] = $val['title'];

 				$list[$key]['typeid']   = $val['typeid'];
				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__business_news_type` WHERE `id` = ".$val['typeid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
	 				$list[$key]['typename'] = $ret[0]['typename'];
				}else{
					$list[$key]['typename'] = "";
				}

				$param = array(
 					"service"  => "business",
 					"template" => "news",
					"bid"      => $val['bid'],
 					"id"       => $val['id']
 				);
 				$list[$key]['typeurl'] = getUrlPath($param);

 				$list[$key]['click']   = $val['click'];
 				$list[$key]['pubdate'] = $val['pubdate'];
 				$list[$key]['bid']     = $val['bid'];

 				$param = array(
 					"service"     => "business",
 					"template"    => "newsd",
 					"bid"         => $val['bid'],
 					"id"          => $val['id']
 				);
 				$list[$key]['url'] = getUrlPath($param);
 			}
 		}

 		return array("pageInfo" => $pageinfo, "list" => $list);
 	}


	/**
     * 商家动态详细
     * @return array
     */
	public function news_detail(){
		global $dsql;
		global $userLogin;
		$newsDetail = array();
		$id = $this->param;

		if(!is_numeric($id)){
			return array("state" => 200, "info" => '格式错误！');
		}

		$archives = $dsql->SetQuery("SELECT n.*, b.`id` bid FROM `#@__business_news` n LEFT JOIN `#@__business_list` b ON b.`id` = n.`uid` WHERE n.`id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$newsDetail["id"]      = $results[0]['id'];
			$newsDetail["typeid"]  = $results[0]['typeid'];
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__business_news_type` WHERE `id` = ".$results[0]['typeid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$newsDetail['typename'] = $ret[0]['typename'];
			}else{
				$newsDetail['typename'] = "";
			}
			$newsDetail["title"]   = $results[0]['title'];
			$newsDetail["body"]    = $results[0]['body'];
			$newsDetail["click"]   = $results[0]['click'];
			$newsDetail["pubdate"] = $results[0]['pubdate'];
			$newsDetail["bid"]     = $results[0]['bid'];
		}
		return $newsDetail;
	}


	/**
     * 商家相册分类
     * @return array
     */
	public function albums_type(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$uid = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$uid = $this->param['uid'];
			}
		}

		if(!$uid){
			return array("state" => 200, "info" => '格式错误！');
		}

		$archives = $dsql->SetQuery("SELECT t.`id`, t.`typename`, b.`id` bid FROM `#@__business_albums_type` t LEFT JOIN `#@__business_list` b ON b.`id` = t.`uid` WHERE t.`uid` = $uid ORDER BY t.`weight` ASC");
		$results = $dsql->dsqlOper($archives, "results");

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']      = $val['id'];
				$list[$key]['typename']   = $val['typename'];
				$param = array(
					"service"  => "business",
					"template" => "albums",
					"bid"      => $val['bid'],
					"id"       => $val['id']
				);
				$list[$key]['url'] = getUrlPath($param);

				//查询相册的封面
				$litpic = "";
				$sql = $dsql->SetQuery("SELECT `litpic` FROM `#@__business_albums` WHERE `uid` = $uid AND `typeid` = ".$val['id']." AND `face` = 1 ORDER BY `id` ASC");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$litpic = getFilePath($ret[0]['litpic']);
				}else{
					$sql = $dsql->SetQuery("SELECT `litpic` FROM `#@__business_albums` WHERE `uid` = $uid AND `typeid` = ".$val['id']." ORDER BY `id` ASC");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$litpic = getFilePath($ret[0]['litpic']);
					}
				}

				$list[$key]['litpic'] = $litpic;
			}
		}

		return $list;
	}


	/**
     * 商家相册
     * @return array
     */
	 public function albums_list(){
 		global $dsql;
 		global $userLogin;
		global $langData;
 		$pageinfo = $list = array();
 		$uid = $typeid = $page = $pageSize = $where = $where1 = "";

 		if(!empty($this->param)){
 			if(!is_array($this->param)){
 				return array("state" => 200, "info" => '格式错误！');
 			}else{
 				$uid      = $this->param['uid'];
 				$u        = (int)$this->param['u'];
 				$typeid   = $this->param['typeid'];
 				$page     = $this->param['page'];
 				$pageSize = $this->param['pageSize'];
 			}
 		}

 		if($u){
 			$userid = $userLogin->getMemberID();
 			if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

 			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $userid");
 			$ret = $dsql->dsqlOper($sql, "results");
 			if($ret){
 				$uid = $ret[0]['id'];
 			}else{
 				return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
 			}
 		}
		//会员ID
		if(empty($uid)){
			return array("state" => 200, "info" => '格式错误！');
		}

		$where .= " AND a.`uid` = $uid";

		//分类
		if(!empty($typeid)){
			$where .= " AND a.`typeid` = $typeid";
		}

 		$pageSize = empty($pageSize) ? 10 : $pageSize;
 		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT a.`id`, a.`typeid`, a.`litpic`, a.`pubdate`, a.`face`, l.`id` bid FROM `#@__business_albums` a LEFT JOIN `#@__business_list` l ON l.`id` = a.`uid` WHERE 1 = 1".$where);

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
 		$results = $dsql->dsqlOper($archives.$where1.$order.$where, "results");

 		if($results){
 			$checkFace = array();
 			foreach($results as $key => $val){
 				$list[$key]['id']    = $val['id'];

 				$face = $val['face'];
 				// 不是封面时判断当前分类是否设置了封面
 				if($face == 0 && $u){
 					if(isset($checkFace[$val['typeid']])){
 						$face = $checkFace[$val['typeid']];
 					}else{
	 					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_albums` WHERE `typeid` = '".$val['typeid']."' AND `face` != 0");
	 					$ret = $dsql->dsqlOper($sql, "results");
	 					// 存在封面
	 					if($ret){
	 						$r = 0;
						// 没有设置封面
	 					}else{
	 						$face = 1;
	 						$r = 1;
	 					}
	 					$checkFace[$val['typeid']] = $r;
	 				}
 				}
 				if(!$u) $face = 1;
 				$list[$key]['face']  = $face;

 				$list[$key]['typeid']   = $val['typeid'];
				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__business_albums_type` WHERE `id` = ".$val['typeid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
	 				$list[$key]['typename'] = $ret[0]['typename'];
				}else{
					$list[$key]['typename'] = "";
				}

				$param = array(
 					"service"  => "business",
 					"template" => "albums",
					"bid"      => $val['bid'],
 					"id"       => $val['id']
 				);
 				$list[$key]['typeurl'] = getUrlPath($param);

 				$list[$key]['litpicSource'] = $val['litpic'];
 				$list[$key]['litpic']  = !empty($val['litpic']) ? getFilePath($val['litpic']) : "";
 				$list[$key]['pubdate'] = $val['pubdate'];
 				$list[$key]['bid']     = $val['bid'];

 				$param = array(
 					"service"     => "business",
 					"template"    => "albumsd",
 					"bid"         => $val['bid'],
 					"id"          => $val['id']
 				);
 				$list[$key]['url'] = getUrlPath($param);
 			}
 		}

 		return array("pageInfo" => $pageinfo, "list" => $list);
 	}


	/**
     * 商家相册详细
     * @return array
     */
	public function albums_detail(){
		global $dsql;
		global $userLogin;
		$newsDetail = array();
		$id = $this->param;

		if(!is_numeric($id)){
			return array("state" => 200, "info" => '格式错误！');
		}

		$archives = $dsql->SetQuery("SELECT a.*, b.`id` bid FROM `#@__business_albums` a LEFT JOIN `#@__business_list` b ON b.`id` = a.`uid` WHERE a.`id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$newsDetail["id"]      = $results[0]['id'];
			$newsDetail["typeid"]  = $results[0]['typeid'];
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__business_albums_type` WHERE `id` = ".$results[0]['typeid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$newsDetail['typename'] = $ret[0]['typename'];
			}else{
				$newsDetail['typename'] = "";
			}
			$newsDetail["litpic"]  = !empty($results[0]['litpic']) ? getFilePath($results[0]['litpic']) : "";
			$newsDetail["pubdate"] = $results[0]['pubdate'];
			$newsDetail["bid"]     = $results[0]['bid'];
		}
		return $newsDetail;
	}


	/**
     * 商家视频
     * @return array
     */
	 public function video_list(){
 		global $dsql;
 		global $userLogin;
		global $langData;
 		$pageinfo = $list = array();
 		$uid = $page = $pageSize = $where = $where1 = "";

 		if(!empty($this->param)){
 			if(!is_array($this->param)){
 				return array("state" => 200, "info" => '格式错误！');
 			}else{
 				$uid      = $this->param['uid'];
 				$page     = $this->param['page'];
 				$pageSize = $this->param['pageSize'];
 			}
 		}

		//会员ID
		if(empty($uid)){
			return array("state" => 200, "info" => '格式错误！');
		}

		$where .= " AND v.`uid` = $uid";

 		$pageSize = empty($pageSize) ? 10 : $pageSize;
 		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT v.`id`, v.`title`, v.`video`, v.`litpic`, v.`pubdate`, l.`id` bid FROM `#@__business_video` v LEFT JOIN `#@__business_list` l ON l.`id` = v.`uid` WHERE 1 = 1".$where);

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
 		$results = $dsql->dsqlOper($archives.$where1.$order.$where, "results");

 		if($results){
 			foreach($results as $key => $val){
 				$list[$key]['id']      = $val['id'];
				$list[$key]['title']   = $val['title'];
 				$list[$key]['litpic']  = !empty($val['litpic']) ? getFilePath($val['litpic']) : "";
 				$list[$key]['pubdate'] = $val['pubdate'];
				$list[$key]['bid']     = $val['bid'];
				$list[$key]['video']   = $val['video'];

 				$param = array(
 					"service"     => "business",
 					"template"    => "videod",
 					"bid"         => $val['bid'],
 					"id"          => $val['id']
 				);
 				$list[$key]['url'] = getUrlPath($param);
 			}
 		}

 		return array("pageInfo" => $pageinfo, "list" => $list);
 	}


	/**
     * 商家视频详细
     * @return array
     */
	public function video_detail(){
		global $dsql;
		global $userLogin;
		$videoDetail = array();
		$id = $this->param;

		if(!is_numeric($id)){
			return array("state" => 200, "info" => '格式错误！');
		}

		$archives = $dsql->SetQuery("SELECT v.*, b.`id` bid FROM `#@__business_video` v LEFT JOIN `#@__business_list` b ON b.`id` = v.`uid` WHERE v.`id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$videoDetail["id"]      = $results[0]['id'];
			$videoDetail["title"]   = $results[0]['title'];
			$videoDetail["litpic"]  = !empty($results[0]['litpic']) ? $results[0]['litpic'] : "";
			$videoDetail["video"]   = $results[0]['video'];
			$videoDetail["pubdate"] = $results[0]['pubdate'];
			$videoDetail["bid"]     = $results[0]['bid'];
		}
		return $videoDetail;
	}


	/**
     * 商家全景
     * @return array
     */
	 public function panor_list(){
 		global $dsql;
 		global $userLogin;
		global $langData;
 		$pageinfo = $list = array();
 		$uid = $page = $pageSize = $where = $where1 = "";

 		if(!empty($this->param)){
 			if(!is_array($this->param)){
 				return array("state" => 200, "info" => '格式错误！');
 			}else{
 				$uid      = $this->param['uid'];
 				$page     = $this->param['page'];
 				$pageSize = $this->param['pageSize'];
 			}
 		}

		//会员ID
		if(empty($uid)){
			return array("state" => 200, "info" => '格式错误！');
		}

		$where .= " AND v.`uid` = $uid";

 		$pageSize = empty($pageSize) ? 10 : $pageSize;
 		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT v.`id`, v.`title`, v.`panor`, v.`litpic`, v.`pubdate`, l.`id` bid FROM `#@__business_panor` v LEFT JOIN `#@__business_list` l ON l.`id` = v.`uid` WHERE 1 = 1".$where);

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
 		$results = $dsql->dsqlOper($archives.$where1.$order.$where, "results");

 		if($results){
 			foreach($results as $key => $val){
 				$list[$key]['id']      = $val['id'];
				$list[$key]['title']   = $val['title'];
 				$list[$key]['litpic']  = !empty($val['litpic']) ? getFilePath($val['litpic']) : "";
 				$list[$key]['pubdate'] = $val['pubdate'];
				$list[$key]['bid']     = $val['bid'];
				$list[$key]['panor']   = $val['panor'];

 				$param = array(
 					"service"     => "business",
 					"template"    => "panord",
 					"id"          => $val['id'],
					"param"       => "bid=" . $val['bid']
 				);
 				$list[$key]['url'] = getUrlPath($param);
 			}
 		}

 		return array("pageInfo" => $pageinfo, "list" => $list);
 	}


	/**
     * 商家全景详细
     * @return array
     */
	public function panor_detail(){
		global $dsql;
		global $userLogin;
		$panorDetail = array();
		$id = $this->param;

		if(!is_numeric($id)){
			return array("state" => 200, "info" => '格式错误！');
		}

		$archives = $dsql->SetQuery("SELECT v.*, b.`id` bid FROM `#@__business_panor` v LEFT JOIN `#@__business_list` b ON b.`id` = v.`uid` WHERE v.`id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$panorDetail["id"]      = $results[0]['id'];
			$panorDetail["title"]   = $results[0]['title'];
			$panorDetail["litpic"]  = !empty($results[0]['litpic']) ? $results[0]['litpic'] : "";
			$panorDetail["panor"]   = $results[0]['panor'];
			$panorDetail["pubdate"] = $results[0]['pubdate'];
			$panorDetail["bid"]     = $results[0]['bid'];
		}
		return $panorDetail;
	}


	/**
		* 配置商铺
		* @return array
		*/
	public function storeConfig(){
	  	global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$sql = $dsql->SetQuery("SELECT `id`, `state`, `type` FROM `#@__business_list` WHERE `uid` = $userid AND `state` != 4");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$busiDetail = $ret[0];
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][157]);  //您还没有入驻商家
		}

		$param      = $this->param;
		$title      = $param['title'];
		$tel        = $param['tel'];
		$qq         = $param['qq'];
		$email      = $param['email'];
		$company    = $param['company'];
		$addrid     = $param['addrid'];
		$cityid     = $param['cityid'];
		$address    = $param['address'];
		$logo       = $param['logo'];
		$wechatname = $param['wechatname'];
		$wechatcode = $param['wechatcode'];
		$wechatqr   = $param['wechatqr'];

		// if(empty($tel)) return array("state" => 200, "info" => $langData['siteConfig'][20][433]);  //请输入电话！
		// if(empty($email)) return array("state" => 200, "info" => $langData['siteConfig'][21][36]);  //请输入邮箱地址！
		if(empty($company)) return array("state" => 200, "info" => $langData['siteConfig'][20][274]);  //请填写公司名称
		if(empty($addrid)) return array("state" => 200, "info" => $langData['siteConfig'][20][134]);  //请选择区域！
		if(empty($address)) return array("state" => 200, "info" => $langData['siteConfig'][21][69]);  //请输入详细地址！
		if(empty($logo)) return array("state" => 200, "info" => $langData['siteConfig'][21][129]);  //请上传LOGO

		if(!is_array($tel)){
			$tel = array($tel);
		}
		$tel = array_filter($tel, function($v){
			return !empty($v);
		});
		if(!is_array($qq)){
			$qq = array($qq);
		}
		$qq = array_filter($qq, function($v){
			return !empty($v);
		});
		// if(empty($tel)) return array("state" => 200, "info" => $langData['siteConfig'][20][433]);  //请输入电话！
		if(!is_array($email)){
			$email = array($email);
		}
		$email = array_filter($email, function($v){
			preg_match('/\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/', $v, $matchEmail);
			return $matchEmail;
		});
		// if(empty($email)) return array("state" => 200, "info" => $langData['siteConfig'][20][497]);  //请输入正确的邮箱地址！

		$tel = join(",", $tel);
		$qq = join(",", $qq);
		$email = join(",", $email);

		$nowState = $busiDetail['state'];

		$sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `title` = '$title', `company` = '$company', `tel` = '$tel', `qq` = '$qq', `email` = '$email', `cityid` = '$cityid', `addrid` = '$addrid', `address` = '$address', `logo` = '$logo', `wechatname` = '$wechatname', `wechatcode` = '$wechatcode', `wechatqr` = '$wechatqr' $state WHERE `uid` = $userid");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			$param = array(
				"service" => "member",
				"type" => "user",
			);
			if($nowState == "3"){
				$param['template'] = "enter-review";
			}
			if($nowState == "1"){
				$param['type'] = "";
			}
			return getUrlPath($param);
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][132]);  //配置失败，请查检您输入的信息是否符合要求！
		}

	}


	/**
     * 动态分类
     * @return array
     */
	public function newstype(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		$archives = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__business_news_type` WHERE `uid` = $business ORDER BY `weight` ASC");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			return $results;
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][134]);  //暂无相关分类！
		}

	}




	/**
		* 更新动态分类
		* @return array
		*/
	public function updateNewsType(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		$data = $_POST['data'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($data)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][135]);  //请添加分类！
		}

		$data = str_replace("\\", '', $data);
		$json = json_decode($data);
		$json = objtoarr($json);

		foreach ($json as $key => $value) {
			$id     = $value['id'];
			$weight = $value['weight'];
			$val    = $value['val'];

			//更新
			if(is_numeric($id)){
				$sql = $dsql->SetQuery("UPDATE `#@__business_news_type` SET `typename` = '$val', `weight` = '$weight' WHERE `uid` = $business AND `id` = $id");
				$ret = $dsql->dsqlOper($sql, "update");

			//新增
			}else{
				$sql = $dsql->SetQuery("INSERT INTO `#@__business_news_type` (`uid`, `typename`, `weight`) VALUES ('$business', '$val', '$weight')");
				$ret = $dsql->dsqlOper($sql, "update");
			}
		}

		return $langData['siteConfig'][6][39];  //保存成功


	}




	/**
		* 删除动态分类
		* @return array
		*/
	public function delNewsType(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		$id  = $this->param['id'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($id)){
			return array("state" => 200, "info" => $langData['siteConfig'][20][300]);  //删除失败！
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_news_type` WHERE `id` = $id AND `uid` = ".$business);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$sql = $dsql->SetQuery("DELETE FROM `#@__business_news` WHERE `typeid` = ".$id);
			$dsql->dsqlOper($sql, "update");

			$sql = $dsql->SetQuery("DELETE FROM `#@__business_news_type` WHERE `id` = ".$id);
			$dsql->dsqlOper($sql, "update");
			return $langData['siteConfig'][21][136];  //删除成功！
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][137]);  //分类验证失败！
		}

	}


	/**
     * 动态信息
     * @return array
     */
	public function news(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid   = $userLogin->getMemberID();
		$pageinfo = $list = array();
		$typeid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$typeid   = (int)$this->param['typeid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}

		$business = $userResult[0]['id'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$where = " WHERE `uid` = $business";

		//类型
		if($typeid != ""){
			$where .= " AND `typeid` = $typeid";
		}

		$orderby = " ORDER BY `id` DESC";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_news`".$where.$orderby);

		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();
		if($results){

			foreach($results as $key => $val){

				$list[$key]['id'] = $val['id'];
				$list[$key]['typeid'] = $val['typeid'];

				$typeName = "";
				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__business_news_type` WHERE `id` = ".$val['typeid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$typeName = $ret[0]['typename'];
				}
				$list[$key]['typename'] = $typeName;

				$list[$key]['title'] = $val['title'];
				$list[$key]['click']  = $val['click'];
				$list[$key]['pubdate'] = $val['pubdate'];

				$param = array(
					"service"  => "business",
					"template" => "newsd",
                    "business" => $business,
					"id"       => $val['id']
				);
				$list[$key]['url']     = getUrlPath($param);

			}
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][138]);   //暂无相关数据！
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 新闻详细信息
     * @return array
     */
	public function newsDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_news` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			//信息分类
			$typename = "";
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__business_news_type` WHERE `id` = ".$results[0]['typeid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$typename = $ret[0]['typename'];
			}
			$results[0]['typename'] = $typename;

			return $results[0];
		}else{
			return array("state" => 200, "info" => '分类不存在！');
		}
	}


	/**
		* 新增动态信息
		* @return array
		*/
	public function addnews(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();
		$param   = $this->param;

		$title       = filterSensitiveWords(addslashes($param['title']));
		$typeid      = (int)($param['typeid']);
		$body        = filterSensitiveWords($param['body']);
		$pubdate     = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($title)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][139]);  //请输入信息标题
		}

		if(empty($typeid)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][140]);  //请选择信息分类
		}

		if(empty($body)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][141]);  //请输入信息内容
		}

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__business_news` (`uid`, `typeid`, `title`, `body`, `click`, `pubdate`) VALUES ('$business', '$typeid', '$title', '$body', '0', '$pubdate')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){
			return $aid;
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][142]);  //发布到数据时发生错误，请检查字段内容！
		}

	}



	/**
		* 修改动态信息
		* @return array
		*/
	public function editnews(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();
		$param   = $this->param;

		$title       = filterSensitiveWords(addslashes($param['title']));
		$id          = (int)($param['id']);
		$typeid      = (int)($param['typeid']);
		$body        = filterSensitiveWords($param['body']);

		if(empty($id)){
			return array("state" => 200, "info" => '请选择要修改的信息！');
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($title)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][139]);  //请输入信息标题
		}

		if(empty($typeid)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][140]);  //请选择信息分类
		}

		if(empty($body)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][141]);  //请输入信息内容
		}

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__business_news` SET `title` = '$title', `typeid` = '$typeid', `body` = '$body' WHERE `uid` = $business AND `id` = ".$id);
		$ret = $dsql->dsqlOper($archives, "update");

		if($ret == "ok"){
			return $langData['siteConfig'][20][229];  //修改成功！
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][142]);  //发布到数据时发生错误，请检查字段内容！
		}

	}


	/**
		* 删除动态信息
		* @return array
		*/
	public function delnews(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$userid = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_news` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['uid'] == $business){
				$archives = $dsql->SetQuery("DELETE FROM `#@__business_news` WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");
				return $langData['siteConfig'][21][136];  //删除成功！

			}else{
				return array("state" => 101, "info" => $langData['siteConfig'][21][143]);  //权限不足，请确认帐户信息后再进行操作！
			}
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][144]);  //信息不存在，或已经删除！
		}

	}


	/**
     * 介绍信息
     * @return array
     */
	public function about(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid   = $userLogin->getMemberID();
		$pageinfo = $list = array();
		$typeid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}

		$business = $userResult[0]['id'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$where = " WHERE `uid` = $business";

		$orderby = " ORDER BY `weight` DESC, `id` DESC";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_about`".$where.$orderby);

		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();
		if($results){

			foreach($results as $key => $val){

				$list[$key]['id'] = $val['id'];
				$list[$key]['title'] = $val['title'];
				$list[$key]['click']  = $val['click'];
				$list[$key]['pubdate'] = $val['pubdate'];

				$param = array(
					"service"  => "business",
					"template" => "intro",
					"business" => $business,
					"id"       => $val['id']
				);
				$list[$key]['url']     = getUrlPath($param);

			}
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][138]);  //暂无相关数据！
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 介绍详细信息
     * @return array
     */
	public function aboutDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_about` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			return $results[0];
		}else{
			return array("state" => 200, "info" => '信息不存在！');
		}
	}


	/**
		* 新增介绍信息
		* @return array
		*/
	public function addabout(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();
		$param   = $this->param;

		$title       = filterSensitiveWords(addslashes($param['title']));
		$body        = filterSensitiveWords($param['body']);
		$weight      = (int)$param['weight'];
		$pubdate     = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($title)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][139]);  //请输入信息标题
		}

		if(empty($body)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][141]);  //请输入信息内容
		}

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__business_about` (`uid`, `title`, `body`, `weight`, `click`, `pubdate`) VALUES ('$business', '$title', '$body', '$weight', '0', '$pubdate')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){
			return $aid;
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][142]);  //发布到数据时发生错误，请检查字段内容！
		}

	}



	/**
		* 修改介绍信息
		* @return array
		*/
	public function editabout(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();
		$param   = $this->param;

		$title       = filterSensitiveWords(addslashes($param['title']));
		$id          = (int)($param['id']);
		$body        = filterSensitiveWords($param['body']);
		$weight      = (int)($param['weight']);

		if(empty($id)){
			return array("state" => 200, "info" => '请选择要修改的信息！');
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($title)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][139]);  //请输入信息标题
		}

		if(empty($body)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][141]);  //请输入信息内容
		}

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__business_about` SET `title` = '$title', `body` = '$body', `weight` = '$weight' WHERE `uid` = $business AND `id` = ".$id);
		$ret = $dsql->dsqlOper($archives, "update");

		if($ret == "ok"){
			return $langData['siteConfig'][20][229];  //修改成功！
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][142]);  //发布到数据时发生错误，请检查字段内容！
		}

	}


	/**
		* 删除介绍信息
		* @return array
		*/
	public function delabout(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$userid = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_about` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['uid'] == $business){
				$archives = $dsql->SetQuery("DELETE FROM `#@__business_about` WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");
				return '删除成功！';

			}else{
				return array("state" => 101, "info" => $langData['siteConfig'][21][143]);  //权限不足，请确认帐户信息后再进行操作！
			}
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][144]);  //信息不存在，或已经删除！
		}

	}


	/**
     * 相册分类
     * @return array
     */
	public function albumstype(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		$archives = $dsql->SetQuery("SELECT `id`, `typename`, `weight` FROM `#@__business_albums_type` WHERE `uid` = $business ORDER BY `weight` ASC");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			return $results;
		}else{
			return array("state" => 200, "info" => '暂无相关分类！');
		}

	}




	/**
		* 更新相册分类
		* @return array
		*/
	public function updateAlbumsType(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		$data = $_POST['data'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($data)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][135]);  //请添加分类！
		}

		$data = str_replace("\\", '', $data);
		$json = json_decode($data);
		$json = objtoarr($json);


		foreach ($json as $key => $value) {
			$id     = $value['id'];
			$weight = $value['weight'];
			$val    = $value['val'];

			//更新
			if($id && is_numeric($id)){
				$sql = $dsql->SetQuery("UPDATE `#@__business_albums_type` SET `typename` = '$val', `weight` = '$weight' WHERE `uid` = $business AND `id` = $id");
				$ret = $dsql->dsqlOper($sql, "update");

			//新增
			}else{
				$sql = $dsql->SetQuery("INSERT INTO `#@__business_albums_type` (`uid`, `typename`, `weight`) VALUES ('$business', '$val', '$weight')");
				$ret = $dsql->dsqlOper($sql, "update");
			}
		}

		return $this->albumstype();

		return $langData['siteConfig'][6][39];  //保存成功


	}




	/**
		* 删除相册分类
		* @return array
		*/
	public function delAlbumsType(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		$id  = $this->param['id'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($id)){
			return array("state" => 200, "info" => $langData['siteConfig'][20][300]);  //删除失败！
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_albums_type` WHERE `id` = $id AND `uid` = ".$business);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$sql = $dsql->SetQuery("DELETE FROM `#@__business_albums` WHERE `typeid` = ".$id);
			$dsql->dsqlOper($sql, "update");

			$sql = $dsql->SetQuery("DELETE FROM `#@__business_albums_type` WHERE `id` = ".$id);
			$dsql->dsqlOper($sql, "update");
			return $langData['siteConfig'][21][136];  //删除成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][137]);  //分类验证失败！
		}

	}


	/**
     * 相册信息
     * @return array
     */
	public function albums(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid   = $userLogin->getMemberID();
		$pageinfo = $list = array();
		$typeid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$typeid   = (int)$this->param['typeid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}

		$business = $userResult[0]['id'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$where = " WHERE `uid` = $business";

		//类型
		if($typeid != ""){
			$where .= " AND `typeid` = $typeid";
		}

		$orderby = " ORDER BY `id` DESC";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_albums`".$where.$orderby);

		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();
		if($results){

			foreach($results as $key => $val){

				$list[$key]['id'] = $val['id'];
				$list[$key]['typeid'] = $val['typeid'];

				$typeName = "";
				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__business_albums_type` WHERE `id` = ".$val['typeid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$typeName = $ret[0]['typename'];
				}
				$list[$key]['typename'] = $typeName;

				$list[$key]['litpic'] = getFilePath($val['litpic']);
				$list[$key]['click']  = $val['click'];
				$list[$key]['pubdate'] = $val['pubdate'];

				$param = array(
					"service"  => "business",
					"template" => "albumsd",
 					"bid"      => $business,
					"id"       => $val['id']
				);
				$list[$key]['url']     = getUrlPath($param);

			}
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 相册详细信息
     * @return array
     */
	public function albumsDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_news` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			//信息分类
			$typename = "";
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__business_albums_type` WHERE `id` = ".$results[0]['typeid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$typename = $ret[0]['typename'];
			}
			$results[0]['typename'] = $typename;

			$results[0]['litpicSource'] = $results[0]['litpic'];
			$results[0]['litpic'] = getFilePath($results[0]['litpic']);

			return $results[0];
		}else{
			return array("state" => 200, "info" => '信息不存在！');
		}
	}


	/**
		* 新增相册信息
		* @return array
		*/
	public function addalbums(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();
		$param   = $this->param;

		$pics    = addslashes($param['pics']);
		$typeid  = (int)($param['typeid']);
		$pubdate = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($typeid)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][140]);  //请选择信息分类
		}

		if(empty($pics)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][145]);  //请上传图片
		}

		$picsArr = explode(",", $pics);
		foreach ($picsArr as $key => $value) {
			$info = explode("|", $value);
			$litpic = $info[0];
			$face = $info[1];
			$id = $info[2];
			if($id){
				$archives = $dsql->SetQuery("UPDATE `#@__business_albums` SET `face` = '$face' WHERE `id` = $id");
				$ret = $dsql->dsqlOper($archives, "update");
			}else{
				$archives = $dsql->SetQuery("INSERT INTO `#@__business_albums` (`uid`, `typeid`, `litpic`, `pubdate`, `face`) VALUES ('$business', '$typeid', '$litpic', '$pubdate', '$face')");
				$ret = $dsql->dsqlOper($archives, "lastid");
			}
		}
		return $ret;  //上传成功！

	}


	/**
		* 删除动态信息
		* @return array
		*/
	public function delalbums(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$userid = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_albums` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['uid'] == $business){

				//删除图集
				delPicFile($results['litpic'], "delAtlas", "business");

				$archives = $dsql->SetQuery("DELETE FROM `#@__business_albums` WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");
				return $langData['siteConfig'][21][136];  //删除成功！

			}else{
				return array("state" => 101, "info" => $langData['siteConfig'][21][143]);  //权限不足，请确认帐户信息后再进行操作！
			}
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][144]);  //信息不存在，或已经删除！
		}

	}


	/**
     * 商家视频
     * @return array
     */
	public function video(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid   = $userLogin->getMemberID();
		$pageinfo = $list = array();
		$typeid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}

		$business = $userResult[0]['id'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$where = " WHERE `uid` = $business";

		$orderby = " ORDER BY `id` DESC";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_video`".$where.$orderby);

		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();
		if($results){

			foreach($results as $key => $val){

				$list[$key]['id'] = $val['id'];
				$list[$key]['title'] = $val['title'];
				$list[$key]['litpic'] = getFilePath($val['litpic']);
				$list[$key]['click']  = $val['click'];
				$list[$key]['pubdate'] = $val['pubdate'];

				$param = array(
					"service"  => "business",
					"template" => "videod",
                    "business" => $business,
					"id"       => $val['id']
				);
				$list[$key]['url']     = getUrlPath($param);

			}
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 视频详细信息
     * @return array
     */
	public function videoDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_video` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$results[0]['litpicSource'] = $results[0]['litpic'];
			$results[0]['litpic'] = getFilePath($results[0]['litpic']);

			return $results[0];
		}else{
			return array("state" => 200, "info" => '信息不存在！');
		}
	}


	/**
		* 新增视频信息
		* @return array
		*/
	public function addvideo(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();
		$param   = $this->param;

		$title       = filterSensitiveWords(addslashes($param['title']));
		$litpic      = filterSensitiveWords($param['litpic']);
		$video       = $_REQUEST['video'];
		$pubdate     = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($title)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][139]);  //请输入信息标题
		}

		if(empty($litpic)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][145]);  //请上传图片
		}

		if(strstr($video, "<iframe")){
			preg_match_all('/<iframe.*?(?: |\t|\r|\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\t|\r|\n)+.*?)?>(.*?)<\/iframe.*?>/sim', $video, $iframe);
			$video = $iframe[1][0];
		}

		if(empty($video)){
			return array("state" => 200, "info" => $langData['siteConfig'][20][121]);  //请输入视频地址
		}

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__business_video` (`uid`, `title`, `litpic`, `video`, `click`, `pubdate`) VALUES ('$business', '$title', '$litpic', '$video', '0', '$pubdate')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){
			return $aid;
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][142]);  //发布到数据时发生错误，请检查字段内容！
		}

	}



	/**
		* 修改视频信息
		* @return array
		*/
	public function editvideo(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();
		$param   = $this->param;

		$title       = filterSensitiveWords(addslashes($param['title']));
		$id          = (int)($param['id']);
		$litpic      = filterSensitiveWords($param['litpic']);
		$video       = $_REQUEST['video'];
		$weight      = (int)($param['weight']);

		if(empty($id)){
			return array("state" => 200, "info" => '请选择要修改的信息！');
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($title)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][139]);  //请输入信息标题
		}

		if(empty($litpic)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][145]);  //请上传图片
		}

		if(strstr($video, "<iframe")){
			preg_match_all('/<iframe.*?(?: |\t|\r|\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\t|\r|\n)+.*?)?>(.*?)<\/iframe.*?>/sim', $video, $iframe);
			$video = $iframe[1][0];
		}

		if(empty($video)){
			return array("state" => 200, "info" => $langData['siteConfig'][20][121]);  //请输入视频地址
		}

		if(strstr($video, "iframe")){
			preg_match_all('/<iframe.*?(?: |\t|\r|\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\t|\r|\n)+.*?)?>(.*?)<\/iframe.*?>/sim', $video, $iframe);
			$video = $iframe[1][0];
		}

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__business_video` SET `title` = '$title', `litpic` = '$litpic', `video` = '$video' WHERE `uid` = $business AND `id` = ".$id);
		$ret = $dsql->dsqlOper($archives, "update");

		if($ret == "ok"){
			return $langData['siteConfig'][20][229];  //修改成功！
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][142]);  //发布到数据时发生错误，请检查字段内容！
		}

	}


	/**
		* 删除视频信息
		* @return array
		*/
	public function delvideo(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$userid = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_video` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['uid'] == $business){
				$archives = $dsql->SetQuery("DELETE FROM `#@__business_video` WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");
				return $langData['siteConfig'][21][136];  //删除成功！

			}else{
				return array("state" => 101, "info" => $langData['siteConfig'][21][143]);  //权限不足，请确认帐户信息后再进行操作！
			}
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][144]); //信息不存在，或已经删除！
		}

	}


	/**
     * 商家全景
     * @return array
     */
	public function panor(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid   = $userLogin->getMemberID();
		$pageinfo = $list = array();
		$typeid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}

		$business = $userResult[0]['id'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$where = " WHERE `uid` = $business";

		$orderby = " ORDER BY `id` DESC";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_panor`".$where.$orderby);

		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();
		if($results){

			foreach($results as $key => $val){

				$list[$key]['id'] = $val['id'];
				$list[$key]['title'] = $val['title'];
				$list[$key]['litpic'] = getFilePath($val['litpic']);
				$list[$key]['click']  = $val['click'];
				$list[$key]['pubdate'] = $val['pubdate'];

				$param = array(
					"service"  => "business",
					"template" => "panord",
					"id"       => $val['id'],
					"param"    => "bid=" . $business
				);
				$list[$key]['url']     = getUrlPath($param);

			}
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 全景详细信息
     * @return array
     */
	public function panorDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_panor` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$results[0]['litpicSource'] = $results[0]['litpic'];
			$results[0]['litpic'] = getFilePath($results[0]['litpic']);

			return $results[0];
		}else{
			return array("state" => 200, "info" => '信息不存在！');
		}
	}


	/**
		* 新增全景信息
		* @return array
		*/
	public function addpanor(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();
		$param   = $this->param;

		$title       = filterSensitiveWords(addslashes($param['title']));
		$litpic      = filterSensitiveWords($param['litpic']);
		$panor        = filterSensitiveWords($param['panor']);
		$pubdate     = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($title)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][139]);  //请输入信息标题
		}

		if(empty($litpic)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][145]);  //请上传图片
		}

		if(empty($panor)){
			return array("state" => 200, "info" => $langData['siteConfig'][20][122]);  //请输入全景地址
		}

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__business_panor` (`uid`, `title`, `litpic`, `panor`, `click`, `pubdate`) VALUES ('$business', '$title', '$litpic', '$panor', '0', '$pubdate')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){
			return $aid;
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][142]);  //发布到数据时发生错误，请检查字段内容！
		}

	}



	/**
		* 修改全景信息
		* @return array
		*/
	public function editpanor(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid  = $userLogin->getMemberID();
		$param   = $this->param;

		$title       = filterSensitiveWords(addslashes($param['title']));
		$id          = (int)($param['id']);
		$litpic      = filterSensitiveWords($param['litpic']);
		$panor       = filterSensitiveWords($param['panor']);
		$weight      = (int)($param['weight']);

		if(empty($id)){
			return array("state" => 200, "info" => '请选择要修改的信息！');
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		if(empty($title)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][139]);  //请输入信息标题
		}

		if(empty($litpic)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][145]);  //请上传图片
		}

		if(empty($panor)){
			return array("state" => 200, "info" => $langData['siteConfig'][20][122]);  //请输入全景地址
		}

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__business_panor` SET `title` = '$title', `litpic` = '$litpic', `panor` = '$panor' WHERE `uid` = $business AND `id` = ".$id);
		$ret = $dsql->dsqlOper($archives, "update");

		if($ret == "ok"){
			return $langData['siteConfig'][20][229];  //修改成功！
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][142]);  //发布到数据时发生错误，请检查字段内容！
		}

	}


	/**
		* 删除全景信息
		* @return array
		*/
	public function delpanor(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$userid = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}
		$business = $userResult[0]['id'];

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_panor` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['uid'] == $business){
				$archives = $dsql->SetQuery("DELETE FROM `#@__business_panor` WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");
				return $langData['siteConfig'][21][136];  //删除成功！

			}else{
				return array("state" => 101, "info" => $langData['siteConfig'][21][143]);  //权限不足，请确认帐户信息后再进行操作！
			}
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][144]);  //信息不存在，或已经删除！
		}

	}


	/**
     * 点评列表
     * @return array
     */
	public function comment(){
		global $dsql;
		global $userLogin;
		global $langData;
		$page = $pageSize = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$userid   = $userLogin->getMemberID();
		$pageinfo = $list = array();

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}

		$business = $userResult[0]['id'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$where = " WHERE `bid` = $business AND `isCheck` = 1";
		$orderby = " ORDER BY `id` DESC";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_comment`".$where.$orderby);

		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！

		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();
		if($results){

			foreach($results as $key => $val){
				$list[$key]['id'] = $val['id'];
				$list[$key]['userid'] = $val['userid'];
				if($val['userid']){
					$userinfo = $userLogin->getMemberInfo($val['userid']);
					$list[$key]['username'] = $userinfo['username'];
				}else{
					$list[$key]['username'] = $langData['siteConfig'][21][120];  //游客
				}
				$list[$key]['rating'] = $val['rating'];
				$list[$key]['content'] = $val['content'];
				$list[$key]['ip']  = $val['ip'];
				$list[$key]['ipaddr'] = $val['ipaddr'];
				$list[$key]['dtime'] = $val['dtime'];
				$list[$key]['reply'] = $val['reply'];
				$list[$key]['rtime'] = $val['rtime'];
			}
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据！
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
		* 删除点评信息
		* @return array
		*/
	public function delComment(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$userid = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}

		$business = $userResult[0]['id'];

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_comment` WHERE `id` = $id AND `isCheck` = 1");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['bid'] == $business){
				$archives = $dsql->SetQuery("DELETE FROM `#@__business_comment` WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");
				return $langData['siteConfig'][21][136];  //删除成功！
			}else{
				return array("state" => 101, "info" => $langData['siteConfig'][21][143]);  //权限不足，请确认帐户信息后再进行操作！
			}
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][144]);  //信息不存在，或已经删除！
		}

	}


	/**
		* 回复点评信息
		* @return array
		*/
	public function replyComment(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];
		$reply = $this->param['reply'];
		$rtime = GetMkTime(time());

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$userid = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}

		$business = $userResult[0]['id'];

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_comment` WHERE `id` = $id AND `isCheck` = 1");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['bid'] == $business){

				if(!empty($reply)){
					$archives = $dsql->SetQuery("UPDATE `#@__business_comment` SET `reply` = '$reply', `rtime` = '$rtime' WHERE `id` = ".$id);
				}else{
					$archives = $dsql->SetQuery("UPDATE `#@__business_comment` SET `reply` = '' WHERE `id` = ".$id);
				}
				$dsql->dsqlOper($archives, "update");
				return $langData['siteConfig'][21][147];  //回复成功！
			}else{
				return array("state" => 101, "info" => $langData['siteConfig'][21][143]);  //权限不足，请确认帐户信息后再进行操作！
			}
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][144]);  //信息不存在，或已经删除！
		}

	}


	/**
		* 商家点餐-商品分类
		* @return array
		*/
	public function diancanGetFoodType(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;

		$u      = $param['u'];
		$shopid = $param['shopid'];

		$uid = 0;
		if($u){
			$uid = $userLogin->getMemberID();
			if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}else{
			if(empty($shopid)) return array("state" => 200, "info" => $langData['siteConfig'][21][148]);  //未指定商家店铺
			$sql = $dsql->SetQuery("SELECT `uid` FROM `#@__business_list` WHERE `id` = $shopid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['uid'];
			}
		}

		$sql = $dsql->SetQuery("SELECT * FROM `#@__business_diancan_type` WHERE `uid` = $uid ORDER BY `sort` ASC");
		$ret = $dsql->dsqlOper($sql, "results");
		$list = array();
		foreach ($ret as $key => $value) {
			$list[$key] = $value;
		}

		return $list;

	}

	/**
		* 商家点餐-保存商品分类
		* @return array
		*/
	public function diancanSaveFoodType(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = ".$uid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['siteConfig'][21][133]);  //请先申请商家店铺！
		}

		$data = str_replace("\\", '', $_POST['data']);
		if($data == "") die;
		$json = json_decode($data);

		$json = objtoarr($json);
		foreach($json as $key => $val){
			if($val['id'] != ""){
				$archives = $dsql->SetQuery("SELECT * FROM `#@__business_diancan_type` WHERE `uid` = $uid AND `id` = ".$val['id']);
				$results = $dsql->dsqlOper($archives, "results");
				if($results){
					$where = array();
					if($results[0]['sort'] != $val['sort']){
						$where[] = '`sort` = '.$val['sort'];
					}
					if($results[0]['title'] != $val['val']){
						$where[] = '`title` = "'.$val['val'].'"';
					}
					if(!empty($where)){
						$archives = $dsql->SetQuery("UPDATE `#@__business_diancan_type` SET ".join(",", $where)." WHERE `id` = ".$val['id']);
						$dsql->dsqlOper($archives, "update");
					}
				}
			}else{
				if(!empty($val['val'])){
					$archives = $dsql->SetQuery("INSERT INTO `#@__business_diancan_type` (`uid`, `title`, `sort`) VALUES ('$uid', '".$val['val']."', ".$val['sort'].")");
					$dsql->dsqlOper($archives, "update");
				}
			}
		}

		// $sql = $dsql->SetQuery("SELECT * FROM `#@__business_diancan_type` WHERE `uid` = $uid ORDER BY `sort` ASC");
		// $ret = $dsql->dsqlOper($sql, "results");
		// $list = array();
		// foreach ($ret as $key => $value) {
		// 	$list[$key] = $value;
		// }

		// return $list;

		return "ok";

	}

	/**
	 * 商家点餐-删除商品分类
	 * @return [type] [description]
	 */
	public function diancanDelFoodType(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$param = $this->param;
		$id = is_array($param) ? $param['id'] : $param;

		if($id){
			$archives = $dsql->SetQuery("DELETE FROM `#@__business_diancan_type` WHERE `id` = '$id' AND `uid` = $uid");
			$results = $dsql->dsqlOper($archives, "update");
			if($results == "ok"){
				return $langData['siteConfig'][20][244];  //操作成功
			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][20][295]);  //操作失败！
			}
		}
	}

	/**
		* 商家点餐-保存商品
		* @return array
		*/
	public function diancanSaveFood(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$dbname = "business_diancan_list";

	    //获取表单数据
		$param     = $this->param;

		extract($param);

		$id        = (int)$id;
		$sort      = (int)$sort;
		$typeid    = (int)$typeid;
		$status    = (int)$status;
		$is_nature = (int)$is_nature;
		$price     = (float)$price;

	    //商品属性
	    $natureArr = array();
	    if($nature){
	        foreach ($nature as $key => $value) {
	            if($value['value']){
	                $arr = array();
	                foreach ($value['value'] as $k => $v) {
	                    array_push($arr, array(
	                        "value" => $v,
	                        "price" => $value['price'][$k],
	                        "is_open" => $value['is_open'][$k]
	                    ));
	                }
	                array_push($natureArr, array(
	                    "name" => $value['name'],
	                    "maxchoose" => $value['maxchoose'],
	                    "data" => $arr
	                ));
	            }
	        }
	    }
	    $nature = serialize($natureArr);

	    //商品名称
	    if(trim($title) == ""){
			return array("state" => 200, "info" => $langData['siteConfig'][21][149]);  //请输入商品名称
		}

	    //商品价格
	    if(trim($price) == ""){
	    	return array("state" => 200, "info" => $langData['siteConfig'][21][150]);  //请输入商品价格
		}

		if(empty($typeid)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][151]); //请选择商品分类
		}

	    if($id){

	        //验证商品是否存在
	        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__$dbname` WHERE `id` = $id");
	        $ret = $dsql->dsqlOper($sql, "totalCount");
	        if($ret <= 0){
	        	return array("state" => 200, "info" => $langData['siteConfig'][21][152]);  //商品不存在或已经删除！
	        }

	    }


	    //修改
	    if($id){

	        $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `sort` = '$sort', `title` = '$title', `price` = '$price', `typeid` = '$typeid', `label` = '$label', `status` = '$status', `descript` = '$descript', `is_nature` = '$is_nature', `nature` = '$nature', `pics` = '$pics'WHERE `id` = $id ");
	        $ret = $dsql->dsqlOper($sql, "update");
	        if($ret == "ok"){
	            return $langData['siteConfig'][6][39];  //保存成功
			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][21][153]);  //数据更新失败，请检查填写的信息是否合法！
			}


	    //新增
	    }else{

	        //保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__$dbname` (`uid`, `sort`, `title`, `price`, `typeid`, `label`, `status`, `descript`, `is_nature`, `nature`, `pics` ) VALUES ('$uid', '$sort', '$title', '$price', '$typeid', '$label', '$status', '$descript', '$is_nature', '$nature', '$pics')");
			$aid = $dsql->dsqlOper($archives, "lastid");

			if($aid){
				return $langData['siteConfig'][6][39];  //保存成功
			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][21][154]);  //数据插入失败，请检查填写的信息是否合法！
			}

	    }

	}

	/**
		* 商家点餐-商品列表
		* @return array
		*/
	public function diancanFoodList(){
		global $dsql;
		global $userLogin;
		global $langData;

		$where = "";

		$param = $this->param;

		$shopid   = (int)$param['shopid'];
		$title    = $param['title'];
		$typename = $param['typename'];
		$typeid   = (int)$param['typeid'];
		$page     = (int)$param['page'];
		$pageSize = (int)$param['pageSize'];
		$u      = (int)$param['u'];

		$page     = empty($page) ? 1 : $page;
		$pageSize = empty($pageSize) ? 20 : $pageSize;

		$uid = 0;
		$list = array();

		// 会员中心
		if($u){
			$uid = $userLogin->getMemberID();
			if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		}else{
			if(empty($shopid)) return array("state" => 200, "info" => $langData['siteConfig'][21][155]);  //未指定店铺

			$sql = $dsql->SetQuery("SELECT `uid` FROM `#@__business_list` WHERE `id` = $shopid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['uid'];
			}
		}

		if($title){
			$where .= " AND `title` LIKE '%".$title."%'";
		}

		if($typename && empty($typeid)){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_diancan_type` WHERE `title` LIKE '%".$typename."%'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				global $data;
				$data = "";
				$typeIdArr = arr_foreach($ret);
				$where .= " AND `typeid` IN (".join(",", $typeIdArr).")";
			}else{
				$where .= " AND 1 = 2";
			}
		}

		if($typeid){
			$where .= " AND `typeid` = $typeid";
		}


		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_diancan_list` WHERE `uid` = $uid".$where);
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

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_diancan_list` WHERE `uid` = $uid".$where);
		$atpage = ($page-1) * $pageSize;
		$results  = $dsql->dsqlOper($archives." ORDER BY `sort` DESC, `id` DESC LIMIT $atpage, $pageSize", "results");

		if($results){
			foreach ($results as $key => $val) {

				$list[$key]['id']     = $val['id'];
				$list[$key]['title']  = $val['title'];
				$list[$key]['price']  = $val['price'];
				$list[$key]['typeid'] = $val['typeid'];
				$list[$key]['sort']   = $val['sort'];
				$list[$key]['status'] = $val['status'];

				$typename = "";
				$sql = $dsql->SetQuery("SELECT `title` FROM `#@__business_diancan_type` WHERE `id` = ".$val['typeid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$typename = $ret[0]['title'];
				}
				$list[$key]['typename'] = $typename;

				$list[$key]['descript']  = $val['descript'];
				$list[$key]['is_nature']  = $val['is_nature'];
				$list[$key]['nature']  = unserialize($val['nature']);
				$list[$key]['nature_json']  = json_encode(unserialize($val['nature']));

				$picArr = array();
				if($val['pics']){
					$pics = explode(",", $val['pics']);
					foreach($pics as $k => $v){
						array_push($picArr, getFilePath($v));
					}
				}
				$list[$key]['pics'] = $picArr;
			}

		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
		* 商家点餐-删除商品
		* @return array
		*/
	public function diancanDelFood(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;

		$id = $param['id'];

		if(empty($id)) return array("state" => 200, "info" => "未指定商品");

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$archives = $dsql->SetQuery("DELETE FROM `#@__business_diancan_list` WHERE `uid` = $uid AND `id` IN (".$id.")");
		$results  = $dsql->dsqlOper($archives, "update");
		if($results == "ok"){
			return $langData['siteConfig'][20][444];  //删除成功！
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][20][300]);  //删除失败
		}


	}

	/**
		* 商家服务-配置
		* @return array
		*/
	public function serviceConfig(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;
		$shopid = (int)$param['shopid'];
		$type = $param['type'];

		$detail = array();

		$where = "";

		if(empty($shopid)){
			$uid = $userLogin->getMemberID();
			if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

			$where = "`uid` = $uid";
			$where = " AND `uid` = $uid";
		}else{
			$where = "`id` = $shopid";
			$where = " AND `id` = $shopid";
		}

		if(empty($type)) return array("state" => 200, "info" => $langData['siteConfig'][21][156]);  //未指定类别

		$fieldAll = array(
			'diancan' => array('diancan_state', 'diancan_tableware_open', 'diancan_tableware_price'),
			'dingzuo' => array('dingzuo_state', 'dingzuo_advance_state', 'dingzuo_advance_type', 'dingzuo_advance_value', 'dingzuo_min_people', 'dingzuo_baofang_open', 'dingzuo_baofang_min'),
			'paidui' => array('paidui_state', 'paidui_juli_limit', 'paidui_oncetime', 'paidui_overdue'),
			'maidan' => array('maidan_state', 'maidan_youhui_open', 'maidan_youhui_value', 'maidan_youhui_limit')
		);

		$fields = $fieldAll[$type];

		$archives = $dsql->SetQuery("SELECT `id`, `uid`, ".join(",", $fields)." FROM `#@__business_list` WHERE ".$where);
		$archives = $dsql->SetQuery("SELECT `id`, `uid`, `title`, `logo`, `addrid`, `address`, `lng`, `lat`, `tel`, `opentime`, `amount`, ".join(",", $fields)." FROM `#@__business_list` WHERE `state` = 1".$where);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$result = $results[0];
			foreach ($result as $key => $value) {
				if($key == "logo" && !empty($value)){
					$value = getFilePath($value);
				}elseif($key == "addrid"){
					$addrName = getParentArr("site_area", $value);
					global $data;
					$data = "";
					$addrArr = array_reverse(parent_foreach($addrName, "typename"));
					$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
					$detail['addrname'] = $addrArr;

				}elseif($key == "lng" || $key == "lat"){
					$value = empty($value) ? 0 : $value;
				}

				$detail[$key] = $value;
			}
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][157]);  //您还没有入驻商家
			return array("state" => 200, "info" => $langData['siteConfig'][21][158]);  //您还没有入驻商家或未审核
		}

		return $detail;

	}

	/**
		* 商家服务-开关
		* @return array
		*/
	public function serviceUpdateState(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;
		$type  = $param['type'];
		$get   = (int)$param['get'];

		$where = "";

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		if(empty($type)) return array("state" => 200, "info" => $langData['siteConfig'][21][156]);  //未指定类别

		$field = $type."_state";

		if($get){
			$state = 0;
			$archives = $dsql->SetQuery("SELECT `".$field."` FROM `#@__business_list` WHERE `state` = 1 AND `uid` = $uid");
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$state = $results[0][$field];
			}

			return $state;
			return array("status" => $state);
		}

		$archives = $dsql->SetQuery("UPDATE `#@__business_list` SET `".$field."` = !`".$field."` WHERE `state` = 1 AND `uid` = $uid");
		$results  = $dsql->dsqlOper($archives, "update");
		if($results == "ok"){
			return $langData['siteConfig'][20][244];  //操作成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][20][295]);  //操作失败！
		}

		return $detail;

	}


	/**
		* 商家服务-获取桌位配置
		* @return array
		*/
	public function serviceGetTable(){
		global $dsql;
		global $langData;

		$param = $this->param;
		$store = (int)$param['store'];

		if(empty($store)) return array("state" => 200, "info" => $langData['siteConfig'][21][155]);  //未指定店铺

		// 查询所有桌位
		$tableList = array();
		$people = array();
		$sql = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_table` WHERE `parentid` = 0 AND `type` = $store ORDER BY `weight` ASC");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			foreach ($ret as $key => $value) {
				$aid = $value['id'];

				$tableList[$key]['id']       = $aid;
				$tableList[$key]['typename'] = $value['typename'];
				$tableList[$key]['code']     = $value['code'];
				$tableList[$key]['min']      = $value['min'];
				$tableList[$key]['max']      = $value['max'];

				$people[] = $value['min'];
				$people[] = $value['max'];

				$lower = array();
				$sql = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_table` WHERE `parentid` = $aid ORDER BY `weight` ASC");
				$res = $dsql->dsqlOper($sql, "results");
				if($res){
					foreach ($res as $k => $val) {
						$lower[$k]['id']       = $val['id'];
						$lower[$k]['typename'] = $val['typename'];
					}
				}
				$tableList[$key]['lower'] = $lower;
			}

			sort($people);

			return array("min" => $people[0], "max" => $people[count($people)-1], "list" => $tableList);

		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][155]);  //暂无数据！
		}



	}

	/**
		* 商家点餐-修改配置
		* @return array
		*/
	public function diancanSaveConfig(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;

		$diancan_state = (int)$param['diancan_state'];
		$diancan_tableware_open = (int)$param['diancan_tableware_open'];
		$diancan_tableware_price = $param['diancan_tableware_price'];

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `diancan_state` = '$diancan_state', `diancan_tableware_open` = '$diancan_tableware_open', `diancan_tableware_price` = '$diancan_tableware_price' WHERE `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			return $langData['siteConfig'][20][229];  //修改成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][159]);  //修改失败，请重试
		}

	}


	/**
	  * 商家点餐-提交订单
	  */
	public function diancanDeal(){
		global $dsql;
		global $userLogin;
		global $langData;
		$uid = $userLogin->getMemberID();

		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}
		$userLogin->keepUser();

		$u             = (int)$this->param['u'];
		$id            = (int)$this->param['id'];
		$shop          = (int)$this->param['shop'];
		$order_content = json_decode($this->param['order_content'], true);
		$table         = $this->param['table'];
		$note          = $this->param['note'];
		$people        = (int)$this->param['people'];


		if($u){
			if(empty($id)) return array("state" => 200, "info" => $langData['siteConfig'][21][160]);  //未指定订单！
			// 验证店铺
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				return array("state" => 200, "info" => $langData['siteConfig'][21][161]);  //权限验证错误
			}else{
				$shop = $ret[0]['id'];
			}

			// 验证订单
			$sql = $dsql->SetQuery("SELECT `uid`, `state`, `pubdate` FROM `#@__business_diancan_order` WHERE `id` = $id AND `sid` = $shop");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				return array("state" => 200, "info" => $langData['siteConfig'][21][162]);  //订单不存在！
			}
			$to = $ret[0]['uid'];
			$orderdate = $ret[0]['pubdate'];

			$priceinfo_    = json_decode($this->param['priceinfo'], true);

		}else{
			if(empty($shop)) return array("state" => 200, "info" => $langData['siteConfig'][21][163]);  //店铺ID错误！
		}

		if(empty($table)) return array("state" => 200, "info" => $langData['siteConfig'][21][164]);  //请输入桌号
		if(empty($people)) return array("state" => 200, "info" => $langData['siteConfig'][21][165]);  //请输入顾客人数


		//店铺详细信息
		$this->param = array("shopid" => $shop, "type" => "diancan");
		$shopDetail = $this->serviceConfig();
		if(!$u){
			if(!$shopDetail['diancan_state']){
				return array("state" => 200, "info" => $langData['siteConfig'][21][166]); //该店铺关闭了点餐功能，您暂时无法线上点餐。
			}
		}

		if(empty($order_content)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][167]);  //购物车内容为空，下单失败！
		}

		// 验证桌号;
		$sql = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__business_dingzuo_table` WHERE `type` = $shop AND `typename` = '$table' AND `parentid` != 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 200, "info" => $langData['siteConfig'][21][168]);  //桌号不存在，请重新填写
		}else{
			$table = $ret[0]['typename'];
		}

		//验证商品
		$totalPrice = 0;
		$dabaoPrice = 0;
		$fids = array();
		$food = array();
		$foodTitle = array();
		foreach ($order_content as $key => $value) {

			$fid     = $value['id'];     //商品ID
			$fcount  = $value['count'];  //商品数量
			$fntitle = $value['ntitle']; //商品属性
			$fnprice = $value['nprice']; //商品属性

			array_push($fids, $fid);

			$sql = $dsql->SetQuery("SELECT * FROM `#@__business_diancan_list` WHERE `id` = $fid AND `uid` = ".$shopDetail['uid']." AND `status` = 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){

				$data = $ret[0];

				$price = $data['price'];

				$totalPrice += $price * $fcount;

				$food[$key]['id']     = $fid;
				$food[$key]['title']  = $data['title'];
				$food[$key]['ntitle'] = $fntitle;
				$food[$key]['nprice'] = $fnprice;
				$food[$key]['count']  = $fcount;
				$fprice = $data['price'];

				$foodTitle[]  = $data['title'];

				//商品属性
				if($data['is_nature']){
					$nature = unserialize($data['nature']);
					if($nature){
						$names = array();
						$prices = array();
						// print_r($nature);die;
						foreach ($nature as $k => $v) {
							$names[$k] = array();
							$prices[$k] = array();
							foreach ($v['data'] as $k_ => $v_) {
								array_push($names[$k], $v_['value']);
								array_push($prices[$k], $v_['price']);
							}
						}

						$namesArr = descartes($names);
						$pricesArr = descartes($prices);

						$names = array();
						$prices = array();

						if(count($namesArr) > 1){
							foreach ($namesArr as $k => $v) {
								array_push($names, join("/", $v));
							}
						}else{
							$names = $namesArr[0];
						}

						if(count($pricesArr) > 1){
							foreach ($pricesArr as $k => $v) {
								array_push($prices, array_sum($v));
							}
						}else{
							$prices = $pricesArr[0];
						}

						if($fntitle){
							$empty = false;
							// 多选的情况
							if(!in_array($fntitle, $names)){
								$fntitleArr = explode("/", $fntitle);
								$fntitle_ = array();
								foreach ($fntitleArr as $k => $v) {
									$_fntitle = array();
									if(strstr($v, '#')){
										$dealv_ = explode("#", $v);	// 下单多选属性
										$count = count($dealv_);
										$find = 0;
										foreach ($nature as $nk => $nv) {
											$maxchoose = $nv['maxchoose'];
											// 已选数量小于等于最多可选数量
											if($maxchoose >= $count){
												foreach ($nv['data'] as $k_ => $v_) {
													if(in_array($v_['value'], $dealv_)){
														if($v_['is_open']){
															$empty = true;
															break;
														}else{
															$find++;
														}
													}
												}
											}else{
												$empty = true;
											}
										}
										if($find < $count){
											$empty = true;
										}
										$_v = substr($v, 0, strpos($v, "#"));
									}else{
										$_v = $v;
									}
									array_push($fntitle_, $_v);
								}

								if($empty){
									return array("state" => 200, "info" => $value['title']."的".$fntitle."不存在，下单失败！");
								}else{
									//获取属性价格
									$fnprice = $prices[array_search(join("/", $fntitle_), $names)];

									$fprice += $fnprice;
									$totalPrice += $fnprice * $fcount;

								}

							// 单选的情况
							}else{
								//获取属性价格
								$fnprice = $prices[array_search($fntitle, $names)];

								$fprice += $fnprice;
								$totalPrice += $fnprice * $fcount;
							}
						}else{

							//获取属性价格
							$fnprice = $prices[array_search($fntitle, $names)];

							$fprice += $fnprice;
							$totalPrice += $fnprice * $fcount;
						}
					}
				}

				$food[$key]['price'] = $fprice;

			}else{
				return array("state" => 200, "info" => $value['title'].$langData['siteConfig'][21][169]);  //已经下架，下单失败！
			}

		}

		if(count($foodTitle) > 1){
			$title = $foodTitle[0].$langData['siteConfig'][21][170];  //等
		}else{
			$title = $foodTitle[0];
		}

		// 费用详情
		$priceinfo = array();

		// 商家修改
		if($u){
			if($priceinfo_){
				foreach ($priceinfo_ as $key => $value) {
					$amount = sprintf("%.2f", $value['amount']);

					$type = '';
					switch($value['type']){
						case 'canju' :
							$type = $langData['siteConfig'][21][171];  //餐具费
							break;
					}
					$totalPrice += $amount;
					array_push($priceinfo, array(
						"type" => $value['type'],
						"body" => $type,
						"amount" => $amount
					));
				}
			}

			$date = GetMkTime(time());
			$fids = join(",", $fids);
			$food = serialize($food);
			$priceinfo = serialize($priceinfo);

			$confirmdate = '';
			if($state == 0){
				$state = 3;
				$confirmdate = ", `confirmdate` = ".GetMkTime(time());
			}

			$sql = $dsql->SetQuery("UPDATE `#@__business_diancan_order` SET `table` = '$table', `people` = '$people', `fids` = '$fids', `food` = '$food', `priceinfo` = '$priceinfo', `amount` = $totalPrice, `note` = '$note', `state` = $state ".$confirmdate." WHERE `id` = $id");
			$res = $dsql->dsqlOper($sql, "update");

			if($state == 3 && $res == "ok"){

				$param = array(
					"service"  => "member",
					"type" => "user",
					"template" => "order-business",
					"param"   => "type=diancan"
				);
				// 通知用户
				$currency = echoCurrency(array("type" => "symbol"));
				updateMemberNotice($to, "会员-点餐成功", $param, array(
					"type" => $langData['siteConfig'][21][172],  //点餐成功通知
					"title" => $title,
					'amount' => $totalPrice,
					"date" => date("Y-m-d H:i:s", $orderdate),
					"body" => str_replace('1', $title, $langData['siteConfig'][21][173])."，".$langData['siteConfig'][19][306]."：".$currency.$totalPrice."，".$langData['siteConfig'][19][309]."：".date("Y-m-d H:i:s", $date)
				));  //您点的$title已经确认   金额   下单时间

				// 打印订单
				// printBusinesDiancan($id);
			}
			// 打印订单
				printBusinesDiancan($id);

			return $res;

		}else{
			if($shopDetail['diancan_tableware_open'] && $shopDetail['diancan_tableware_price']){
				$totalPrice += $people * $shopDetail['diancan_tableware_price'];
				array_push($priceinfo, array(
					"type" => "canju",
					"body" => $langData['siteConfig'][21][171],  //餐具费
					"amount" => sprintf("%.2f", $people * $shopDetail['diancan_tableware_price'])
				));
			}
		}


		//生成订单号
		$newOrdernum = create_ordernum();
		$pubdate = GetMkTime(time());
		$fids = join(",", $fids);

		$food = serialize($food);
		$preset = serialize($preset);
		$priceinfo = serialize($priceinfo);

		//查询是否下过单，防止重复下单
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_diancan_order` WHERE `uid` = $uid AND `sid` = $shop AND `state` = 0 AND `fids` = '$fids' AND `food` = '$food' AND `amount` = $totalPrice");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$id = $ret[0]['id'];
			$aid = $id;

			$sql = $dsql->SetQuery("UPDATE `#@__business_diancan_order` SET `ordernum` = '$newOrdernum', `note` = '$note', `pubdate` = '$pubdate', `table` = '$table', `people` = '$people' WHERE `id` = $id");

			$res = $dsql->dsqlOper($sql, "update");
			if($res != "ok"){
				return array("state" => 200, "info" => $langData['siteConfig'][21][174]);  //下单失败！
			}

		}else{
			$sql = $dsql->SetQuery("INSERT INTO
				`#@__business_diancan_order`
				(`uid`, `sid`, `ordernum`, `state`, `fids`, `food`, `people`, `table`, `amount`, `priceinfo`, `note`, `pubdate`)
				VALUES
				('$uid', '$shop', '$newOrdernum', '0', '$fids', '$food', '$people', '$table', '$totalPrice', '$priceinfo', '$note', '$pubdate')
			");
			$aid = $dsql->dsqlOper($sql, "lastid");
			if(!is_numeric($aid)){
				return array("state" => 200, "info" => $langData['siteConfig'][21][174]);  //下单失败！
			}


		}
		// 通知商家
		$param = array(
			"service"  => "member",
			"template" => "business-diancan-order"
		);
		updateMemberNotice($shopDetail['uid'], "会员-点餐成功", $param, array(
			"type" => $langData['siteConfig'][21][175],  //您有新的点餐订单
			"title" => $title,
			'amount' => $totalPrice,
			"date" => date("Y-m-d H:i:s", $orderdate),
			"body" => $langData['siteConfig'][21][175]  //您有新的点餐订单
		));

		// 打印订单

		return $newOrdernum;

	}


	/**
	  * 商家点餐-商家修改订单
	  */
	public function diancanEditDeal(){
		global $langData;
		$this->param['u'] = 1;
		$res = $this->diancanDeal();

		if($res == "ok"){
			return $langData['siteConfig'][20][229];  //修改成功
		}else{
			return array("state" => 200, "info" => ($res['info'] ? $res['info'] : $langData['siteConfig'][21][85]));  //提交失败！
		}

	}


	/**
	  * 商家点餐-订单列表
	  */
	public function diancanOrder(){
		global $dsql;
		global $userLogin;
		global $langData;
		$userid = $userLogin->getMemberID();
		$state = $where = '';

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$state    = $this->param['state'];
				$u        = (int)$this->param['u'];
				$sid      = (int)$this->param['sid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if($u){
			// 验证店铺
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				return array("state" => 200, "info" => $langData['siteConfig'][21][161]);  //权限验证错误
			}else{
				$sid = $ret[0]['id'];
			}

			$where .= " AND `sid` = $sid";

		}else{
			$where .= " AND `uid` = $userid";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		// 待确认
		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_diancan_order` WHERE 1 = 1".$where);
		$totalGray = $dsql->dsqlOper($archives." AND `state` = 0", "totalCount");

		// 已确认
		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_diancan_order` WHERE 1 = 1".$where);
		$totalAudit = $dsql->dsqlOper($archives." AND `state` = 3", "totalCount");

		// 总数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		if($state != ''){
			$where .= " AND `state` = $state";

			if($state == 0){
				$totalPage = ceil($totalGray/$pageSize);
			}elseif($state == 3){
				$totalPage = ceil($totalAudit/$pageSize);
			}

		}else{
			$totalPage = ceil($totalCount/$pageSize);
		}

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount,
			"totalGray" => $totalGray,
			"totalAudit" => $totalAudit
		);

		$list = array();
		$atpage = ($page - 1) * $pageSize;
		$where .= " ORDER BY `id` DESC LIMIT $atpage, $pageSize";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_diancan_order` WHERE 1 = 1".$where);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				$list[$key]['id']        = $value['id'];
				$list[$key]['ordernum']  = $value['ordernum'];
				$list[$key]['state']     = $value['state'];
				$list[$key]['food']      = unserialize($value['food']);
				$list[$key]['pubdate']   = $value['pubdate'];
				$list[$key]['table']     = $value['table'];
				$list[$key]['people']    = $value['people'];
				$list[$key]['amount']    = $value['amount'];
				$list[$key]['note']      = $value['note'];
				$list[$key]['priceinfo'] = unserialize($value['priceinfo']);

				if($u){
					$user = '';
					$sql = $dsql->SetQuery("SELECT `nickname`, `username`, `photo` FROM `#@__member` WHERE `id` = ".$value['uid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$user = array(
							'name' => empty($ret[0]['nickname']) ? $ret[0]['username'] : $ret[0]['nickname'],
							'photo' => empty($ret[0]['photo']) ? '' : getFilePath($ret[0]['photo'])
						);
					}
					$list[$key]['user'] = $user;

				// 商家信息
				}else{
					$sql = $dsql->SetQuery("SELECT `title`, `logo` FROM `#@__business_list` WHERE `id` = ".$value['sid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$param = array(
							"service" => "business",
							"templates" => "detail",
							"id" => $value['sid']
						);
						$url = getUrlPath($param);
						$list[$key]['store'] = array(
							"id" => $value['sid'],
							"title" => $ret[0]['title'],
							"logo" => $ret[0]['logo'] ? getFilePath($ret[0]['logo']) : "",
							"url" => $url
						);
					}
				}
			}

			return array("pageInfo" => $pageinfo, "list" => $list);
		}else{

			return array("pageInfo" => $pageinfo, "list" => $list);
		}




	}

	/**
	  * 商家点餐-订单详情
	  */
	public function diancanOrderDetail(){
		global $dsql;
		global $langData;
		$orderDetail = $cardnum = array();
		$id = $this->param;

		global $userLogin;
		$userid = $userLogin->getMemberID();

		if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_diancan_order` WHERE `id` = $id");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$result                   = $results[0];
			$orderDetail["id"]        = $result["id"];
			$orderDetail["sid"]       = $result["sid"];
			$orderDetail["ordernum"]  = $result["ordernum"];
			$orderDetail["pubdate"]   = $result["pubdate"];
			$orderDetail['table']     = $result['table'];
			$orderDetail['people']    = $result['people'];
			$orderDetail['state']     = $result['state'];
			$orderDetail['food']      = unserialize($result['food']);
			$orderDetail['food_json']   = json_encode(unserialize($result['food']));
			$orderDetail['pubdate']   = $result['pubdate'];
			$orderDetail['amount']    = $result['amount'];
			$orderDetail['note']      = $result['note'];
			$orderDetail['priceinfo'] = unserialize($result['priceinfo']);
		}
		return $orderDetail;
	}


	/**
		* 商家订座-修改配置
		* @return array
		*/
	public function dingzuoSaveConfig(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;

		$dingzuo_state = (int)$param['dingzuo_state'];
		$dingzuo_advance_state = (int)$param['dingzuo_advance_state'];
		$dingzuo_advance_type = (int)$param['dingzuo_advance_type'];
		$dingzuo_advance_value = (int)$param['dingzuo_advance_value'];
		$dingzuo_baofang_open = (int)$param['dingzuo_baofang_open'];
		$dingzuo_baofang_min = (int)$param['dingzuo_baofang_min'];
		$dingzuo_min_people = (int)$param['dingzuo_min_people'];

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `dingzuo_state` = '$dingzuo_state', `dingzuo_advance_state` = '$dingzuo_advance_state', `dingzuo_advance_type` = '$dingzuo_advance_type', `dingzuo_advance_value` = '$dingzuo_advance_value', `dingzuo_baofang_open` = '$dingzuo_baofang_open', `dingzuo_baofang_min` = '$dingzuo_baofang_min', `dingzuo_min_people` = '$dingzuo_min_people' WHERE `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			return $langData['siteConfig'][20][229];  //修改成功！
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][159]);  //修改失败，请重试
		}

	}


	/**
     * 商家订座-时间段配置
     * @return array
     */
	public function dingzuoCategory(){
		global $dsql;
		$u = $store = $type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$store    = (int)$this->param['store'];
				$tab      = $this->param['tab'];
				$type     = (int)$this->param['type'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}

		if(empty($tab)) return array("state" => 200, "info" => '格式错误！');

		if(empty($store)){
			global $userLogin;
			$userid = $userLogin->getMemberID();
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = ".$userid);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$store = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => '格式错误！');
			}
		}

		$list = array();

		if($tab == "time"){
			$list = $dsql->getTypeList($type, "business_dingzuo_".$tab, $son, $page, $pageSize, " AND `type` = $store");
		}else{
			$archives = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_table` WHERE `type` = $store AND `parentid` = $type");
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach ($results as $key => $value) {
					$list[$key] = $value;

					$sql = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_table` WHERE `type` = $store AND `parentid` = ".$value['id']);
					$ret  = $dsql->dsqlOper($sql, "results");
					if($ret){
						$list[$key]['lower'] = $ret;
					}else{
						$list[$key]['lower'] = "";
					}
				}
			}

		}
		if($list){
			return $list;
		}
	}


	/**
		* 更新商铺商品分类
		* @return array
		*/
	public function dingzuoUpdateCategory(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid   = $userLogin->getMemberID();
		$id       = $this->param['id'];
		$tab      = $this->param['tab'];
		$field    = $this->param['field'];
		$typename = $this->param['typename'];


		if(empty($field)) $field = 'typename';

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][159]);  //修改失败，请重试
		}

		if(empty($tab)){
			return array("state" => 200, "info" => '参数错误！');
		}

		if(empty($typename)){
			return array("state" => 200, "info" => $field == 'typename' ? $langData['siteConfig'][21][176] : $langData['siteConfig'][21][177]);  //请输入分类名称！  请输入内容
		}

		$typename = cn_substrR($typename,30);

		$typename = trim($typename);

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$storeid = $ret[0]['id'];

			//验证权限
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__business_dingzuo_".$tab."` WHERE `type` = $storeid AND `id` = ".$id);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){

				if($ret[0]['typename'] == $typename){
					return array("state" => 101, "info" => $langData['siteConfig'][21][178]);  //没有变化
				}else{
					$sql = $dsql->SetQuery("UPDATE `#@__business_dingzuo_".$tab."` SET `".$field."` = '$typename' WHERE `id` = ".$id);
					if($dsql->dsqlOper($sql, "update") == "ok"){
						return $langData['siteConfig'][20][229];  //修改成功！
					}else{
						return array("state" => 200, "info" => $langData['siteConfig'][21][179]);  //更新失败，请重试！
					}
				}

			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][21][180]);  //账号验证错误，更新失败！
			}

		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][181]);  //您还没有入驻商家！
		}

	}

	/**
		* 更新商家订座分类(时间段和桌位)
		* @return array
		*/
	public function dingzuoOperaCategory(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		$data   = $_POST['data'];
		$type   = $_POST['type'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(empty($data)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][135]);  //请添加分类！
		}

		if(empty($type)){
			return array("state" => 200, "info" => '参数错误！');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$storeid = $ret[0]['id'];

			$data = str_replace("\\", '', $data);
			$json = json_decode($data);

			$json = objtoarr($json);
			$json = $this->proTypeAjax($json, 0, "business_dingzuo_".$type, $storeid);
			return $json;

		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][181]);  //您还没有入驻商家！
		}


	}

	//更新分类
	public function proTypeAjax($json, $pid = 0, $tab, $tid){
		global $dsql;
		for($i = 0; $i < count($json); $i++){
			$id = $json[$i]["id"];
			$name = $json[$i]["name"];

			$name = trim($name);

			//如果ID为空则向数据库插入下级分类
			if($id == "" || $id == 0){
				$archives = $dsql->SetQuery("INSERT INTO `#@__".$tab."` (`type`, `parentid`, `typename`, `weight`, `pubdate`) VALUES ('$tid', '$pid', '$name', '$i', '".GetMkTime(time())."')");
				$id = $dsql->dsqlOper($archives, "lastid");
			}
			//其它为数据库已存在的分类需要验证分类名是否有改动，如果有改动则UPDATE
			else{
				$archives = $dsql->SetQuery("SELECT `typename`, `weight`, `parentid` FROM `#@__".$tab."` WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "results");
				if(!empty($results)){
					//验证分类名
					if($results[0]["typename"] != $name){
						$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `typename` = '$name' WHERE `id` = ".$id);
						$results = $dsql->dsqlOper($archives, "update");
					}

					//验证排序
					if($results[0]["weight"] != $i){
						$archives = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `weight` = '$i' WHERE `id` = ".$id);
						$results = $dsql->dsqlOper($archives, "update");
					}
				}
			}
			if(is_array($json[$i]["lower"])){
				$this->proTypeAjax($json[$i]["lower"], $id, $tab, $tid);
			}
		}
		return '保存成功！';

	}

	/**
		* 删除商家订座-分类
		* @return array
		*/
	public function dingzuoDelCategory(){
		global $dsql;
		global $userLogin;

		$userid = $userLogin->getMemberID();
		$id     = $this->param['id'];
		$tab    = $this->param['tab'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(empty($id)){
			return array("state" => 200, "info" => '删除失败，请重试！');
		}

		if(empty($tab)){
			return array("state" => 200, "info" => '参数错误！');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$storeid = $ret[0]['id'];

			$ids = explode(",", $id);
			foreach ($ids as $key => $value) {

				//验证权限
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_dingzuo_".$tab."` WHERE `type` = $storeid AND `id` = ".$value);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){

					$sql = $dsql->SetQuery("DELETE FROM `#@__business_dingzuo_".$tab."` WHERE `id` = ".$value." OR `parentid` = ".$value);
					if(!$dsql->dsqlOper($sql, "update") == "ok"){
						return array("state" => 200, "info" => $langData['siteConfig'][20][300]);  //删除失败，请重试！
					}

				}else{
					return array("state" => 200, "info" => $langData['siteConfig'][21][182]);  //账号验证错误，删除失败！
				}

			}
			return "删除成功！";

		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][181]);  //您还没有入驻商家！
		}

	}


	/**
		* 商家订座-用户下单
		* @return array
		*/
	public function dingzuoDeal(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$param        = $this->param;
		$store        = (int)$param['store'];
		$time         = $param['time'];
		$baofang      = (int)$param['baofang'];
		$baofang_only = (int)$param['baofang_only'];
		$people       = (int)$param['people'];
		$table        = (int)$param['table'];
		$name         = $param['name'];
		$sex          = (int)$param['sex'];
		$contact      = $param['contact'];
		$note         = $param['note'];

		if(empty($store)) return array("state" => 200, "info" => "参数错误");
		if(empty($time)) return array("state" => 200, "info" => $langData['siteConfig'][21][183]);  //请选择时间
		if(empty($people)) return array("state" => 200, "info" => $langData['siteConfig'][21][184]);  //请选择人数
		if(empty($contact)) return array("state" => 200, "info" => $langData['siteConfig'][21][185]);  //请填写手机号

		$time = GetMkTime($time);	//  预定时间
		$pubdate = GetMkTime(time());
		$this->param = array("shopid" => $store, "type" => "dingzuo");
		$cfg = $this->serviceConfig();

		if($cfg && !isset($cfg['state'])){

			if($cfg['dingzuo_state'] == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][186]);  //提交失败，商家关闭了订座功能

			// if($cfg['uid'] == $uid) return array("state" => 200, "info" => "您确定要预定自家店铺吗？");

			if($people < $cfg['dingzuo_min_people']){
				if($people < $cfg['dingzuo_min_people']) return array("state" => 200, "info" => str_replace('1', $cfg['dingzuo_baofang_min'], $langData['siteConfig'][21][187]));  //抱歉，本店最少1人起订
			}

			// 包房
			if($baofang){
				if($people < $cfg['dingzuo_baofang_min']) return array("state" => 200, "info" => str_replace('1', $cfg['dingzuo_baofang_min'], $langData['siteConfig'][21][188]));  //抱歉，包房最少1人
				$table = 0;
			}

			// 提前预定
			if($cfg['dingzuo_advance_state'] == 1 && $cfg['dingzuo_advance_value']){
				$dingzuo_advance_value = $cfg['dingzuo_advance_value'] * ($cfg['dingzuo_advance_type'] == 0 ? 3600 : 86400);
				$info = str_replace('1', $cfg['dingzuo_advance_value'].($cfg['dingzuo_advance_type'] == 0 ? $langData['siteConfig'][13][44] : $langData['siteConfig'][13][6]), $langData['siteConfig'][21][189]);  //抱歉，商家要求必须提前1预定    小时    天
				if($time - $pubdate < $dingzuo_advance_value) return array("state" => 200, "info" => $info);
			}

		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][190]);  //提交失败，商家不存在或状态异常
		}

		// 如果有桌位号，查询商家桌位配置
		if($table){
			$sql = $dsql->SetQuery("SELECT `parentid` FROM `#@__business_dingzuo_table` WHERE `type` = $store AND `id` = $table");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){

				$tableArea = $ret[0]['parentid'];
				// 区域
				$sql = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_table` WHERE `type` = $store AND `id` = ".$tableArea);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$min = $ret[0]['min'];
					$max = $ret[0]['max'];

					if($people < $min) return array("state" => 200, "info" => str_replace('1', $min, $langData['siteConfig'][21][191]));  //该桌位最少预定人数为1人
					if($people > $max) return array("state" => 200, "info" => str_replace('1', $max, $langData['siteConfig'][21][192]));  //该桌位最大预定人数为1人

					// 验证桌位是否可以预定
					$day = date("Y-m-d", $time);
					$date = (int)date("H", $time);
					$date .= ":".date("i", $time);
					$start = $end = 0;

					// 查询指定时间点所属时间段
					$sql = $dsql->SetQuery("SELECT `parentid` FROM `#@__business_dingzuo_time` WHERE `type` = $store AND `typename` = '$date'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$timearea = $ret[0]['parentid'];
					}else{
						return array("state" => 200, "info" => $langData['siteConfig'][21][193]);  //预定时间不正确
					}

					$day_start = strtotime($day." 00:00");
					$day_end = strtotime($day." 23:59");
					$checkSql = $dsql->SetQuery("SELECT `id` FROM `#@__business_dingzuo_order` WHERE `sid` = $store AND `table` = $table AND `timearea` = $timearea AND (`time` > $day_start AND `time` < $day_end) AND `state` != 2");
					$checkRet = $dsql->dsqlOper($checkSql, "results");
					if($checkRet){
						return array("state" => 200, "info" => $langData['siteConfig'][21][194]);  //抱歉，该桌位已被预定
					}

				}else{
					return array("state" => 200, "info" => $langData['siteConfig'][21][195]);  //桌位号不存在
				}
			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][21][195]);  //桌位号不存在
			}
		}

		$ordernum = create_ordernum();

		// 包房或者未指定桌位需要商家确认
		$state = ($baofang || empty($baofang) && empty($table)) ? 0 : 1;
		$archives = $dsql->SetQuery("INSERT INTO `#@__business_dingzuo_order` (`uid`, `ordernum`, `sid`, `timearea`, `time`, `people`, `table`, `baofang`, `baofang_only`, `name`, `sex`, `contact`, `note`, `pubdate`, `state`) VALUES ('$uid', '$ordernum', '$store', '$timearea', '$time', '$people', '$table', '$baofang', '$baofang_only', '$name', '$sex', '$contact', '$note', '$pubdate', '$state')");
		$lastid   = $dsql->dsqlOper($archives, "lastid");
		if(is_numeric($lastid)){

			if($state){
				// 通知用户
				$to = $uid;
				$param = array(
					"service"  => "member",
					"type" => "user",
					"template" => "order-business",
					"param"   => "type=dingzuo"
				);
				updateMemberNotice($to, "会员-订座成功", $param, array(
					"type" => $langData['siteConfig'][21][196],  //订座成功通知
					"contact" => $contact,
					'store' => $cfg['title'],
					"date" => date("Y-m-d H:i", $time),
					'people' => $people,
					'note' => $note,
					// 'body' => str_replace('1', $store, str_replace('2', $contact, str_replace('3', $date, $langData['siteConfig'][21][197]))),
					'fields' => array(
		                'keyword1' => '预留手机',
		                'keyword2' => '订座门店',
		                'keyword3' => '到店时间',
		                'keyword4' => '人数',
		                'keyword5' => '备注'
		            )
				));

				// 通知商家
				$to = $cfg['uid'];
				$param = array(
					"service"  => "member",
					"template" => "business-dingzuo-order"
				);
				updateMemberNotice($to, "会员-订座成功", $param, array(
					"type" => $langData['siteConfig'][21][198],  //您有新的订座订单
					"contact" => $contact,
					'store' => $cfg['title'],
					"date" => date("Y-m-d H:i", $time),
					'people' => $people,
					'note' => $note,
					// 'body' => $langData['siteConfig'][21][198],  //您有新的订座订单
					'fields' => array(
		                'keyword1' => '预留手机',
		                'keyword2' => '订座门店',
		                'keyword3' => '到店时间',
		                'keyword4' => '人数',
		                'keyword5' => '备注'
		            )
				));
			}

			return $ordernum;
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][20][180]);  //提交失败，请重试！
		}

	}

	/**
		* 商家订座-更改订单状态，确认或取消
		* @return array
		*/
	public function dingzuoUpdateState(){
		global $dsql;
		global $userLogin;
		global $langData;

		$where = "";

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$param = $this->param;
		$id    = (int)$param['id'];
		$u     = (int)$param['u'];
		$state = (int)$param['state'];

		if(empty($id) || empty($state)) return array("state" => 200, "info" => "参数错误！");


		if($u){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$store = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][21][161]);  //权限验证错误
			}

			$where = " AND `sid` = $store";
		}else{
			$where = " AND `uid` = $uid";
		}

		$sql = $dsql->SetQuery("SELECT `sid`, `uid`, `state`, `time`, `note`, `contact`, `people` FROM `#@__business_dingzuo_order` WHERE `id` = $id".$where);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 200, "info" => $langData['siteConfig'][21][162]);  //订单不存在！
		}
		$sid     =  $ret[0]['sid'];
		$to      =  $ret[0]['uid'];
		$state_  =  $ret[0]['state'];
		$time    =  $ret[0]['time'];
		$contact =  $ret[0]['contact'];
		$people  =  $ret[0]['people'];
		$note    =  $ret[0]['note'];

		$date = GetMkTime(time());

		if($state_ == $state) return array("state" => 200, "info" => $langData['siteConfig'][21][199]);  //操作失败，请检查订单状态
		if($state_ == 2) return array("state" => 200, "info" => $langData['siteConfig'][21][200]);  //当前订单状态无法修改
		if($date >= $time && $state != 2) return array("state" => 200, "info" => $langData['siteConfig'][21][201]);  //此订单已过期

		if($state == 2){
			$cancel_bec = $param['cancel_bec'];
			$cancel_date = GetMkTime(time());

			$more = ", `cancel_bec` = '$cancel_bec', `cancel_date` = '$cancel_date', `cancel_adm` = 1";
		}
		$sql = $dsql->SetQuery("UPDATE `#@__business_dingzuo_order` SET `state` = $state ".$more." WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){

			if($u && $state == 1){

				$sql = $dsql->SetQuery("SELECT `title` FROM `#@__business_list` WHERE `id` = $sid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$store = $ret[0]['title'];
				}

				$param = array(
					"service"  => "member",
					"type" => "user",
					"template" => "order-business",
					"param"   => "type=dingzuo"
				);

				// 通知用户
				updateMemberNotice($to, "会员-订座成功", $param, array("contact" => $contact, 'store' => $store, "date" => date("Y-m-d H:i", $time), 'people' => $people, 'note' => $note, 'fields' => array(
					'keyword1' => '预留手机',
					'keyword2' => '订座门店',
					'keyword3' => '到店时间',
					'keyword4' => '人数',
					'keyword5' => '备注'
				)));

			}


			return $langData['siteConfig'][20][244];  //操作成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][72]);  //操作失败，请重试！
		}


	}


	/**
		* 商家订座-获取商家指定日期的桌位信息
		* @return array
		*/
	public function dingzuoGetTable(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param  = $this->param;
		$store  = (int)$param['store'];
		$people = (int)$param['people'];
		$date   = $param['date'];


		$now = GetMkTime(time());

		$hours = '';
		if($date){
			$day = explode(" ", $date)[0];
			$hours = explode(" ", $date)[1];
		}else{
			$day = date("Y-m-d", $now);
		}
		$day_start = strtotime($day." 00:00");
		$day_end = strtotime($day." 23:59");

		$setStageId = 0;
		if($hours){
			$sql = $dsql->SetQuery("SELECT `parentid` FROM `#@__business_dingzuo_time` WHERE `typename` = '$hours'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$setStageId = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][21][202]);  //时间错误
			}
		}

		if(empty($store)) return array("state" => 200, "info" => $langData['siteConfig'][21][155]);  //未指定店铺


		$this->param = array("shopid" => $store, "type" => "dingzuo");
		$cfg = $this->serviceConfig();

		$stage        = array();	//时间段信息
		$time         = array();	//可预定时间点
		$table        = array();	//已预定桌位
		$have         = array();	//剩余桌位
		$canDealStage = array();	//可预定时间段

		if($cfg && !isset($cfg['state'])){

			if($cfg['dingzuo_state'] == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][203]);  //查询失败，商家关闭了订座功能

			// 提前预定的时间 s
			$dingzuo_advance_value = 0;
			if($cfg['dingzuo_advance_state'] == 1 && $cfg['dingzuo_advance_value']){
				$dingzuo_advance_value = $cfg['dingzuo_advance_value'] * ($cfg['dingzuo_advance_type'] == 0 ? 3600 : 86400);
			}



			// 查询所有桌位
			$tableList = array();
			$tableCount = 0;
			$sql = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_table` WHERE `parentid` = 0 AND `type` = ".$cfg['id']." ORDER BY `weight` ASC");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				foreach ($ret as $key => $value) {
					$aid = $value['id'];

					$tableList[$key]['id']       = $aid;
					$tableList[$key]['typename'] = $value['typename'];
					$tableList[$key]['code']     = $value['code'];
					$tableList[$key]['min']      = $value['min'];
					$tableList[$key]['max']      = $value['max'];

					$lower = array();
					$sql = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_table` WHERE `parentid` = $aid ORDER BY `weight` ASC");
					$res = $dsql->dsqlOper($sql, "results");
					if($res){
						foreach ($res as $k => $val) {
							$lower[$k]['id']       = $val['id'];
							$lower[$k]['typename'] = $val['typename'];

							$tableCount++;
						}
					}
					$tableList[$key]['lower'] = $lower;
				}
			}

			// 时间段
			$sql      = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_time` WHERE `type` = $store AND `parentid` = 0 ORDER BY `weight` ASC");
			$stageCfg = $dsql->dsqlOper($sql, "results");
			if($stageCfg){
				foreach ($stageCfg as $key => $value) {

					$stageid = $value['id']; 	// 时间段id

					$start = "00:00";
					$end = "23:59";

					// 此时间段已预定的桌位
					$hasReserve = array();
					$checkSql = $dsql->SetQuery("SELECT `id`, `table`, `time` FROM `#@__business_dingzuo_order` WHERE `sid` = $store AND `timearea` = $stageid AND `time` >= $day_start AND `time` <= $day_end AND `table` != 0 AND `state` != 2");
					$checkRet = $dsql->dsqlOper($checkSql, "results");
					if($checkRet){
						foreach ($checkRet as $m => $v) {
							array_push($hasReserve, array(
								"tableid" => $v['table'],
								"time" => date("H:i", $v['time']),
								"stageid" => $stageid,	// 时间段
							));
						}
					}

					// 时间段类所有时间点
					$time = array();
					$sql       = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_time` WHERE `parentid` = $stageid ORDER BY `weight` ASC");
					$hoursCfg = $dsql->dsqlOper($sql, "results");
					if($hoursCfg){
						foreach ($hoursCfg as $k => $val) {

							$time_house = $val['typename'];

							$time_ = strtotime($day." ".$time_house);

							// 当前时间早于最迟预定时间
							$last = $time_ - $dingzuo_advance_val;

							// 现在可预定的时间点
							if($now < $last){

								// 记录可预定的时间段
								if(!in_array($stageid, $canDealStage)){
									array_push($canDealStage, $stageid);
								}
								array_push($time, array(
									"parentid" => $stageid,
									"time" => trim($time_house),
									"date" => $day." ".trim($val['typename'])
								));

							}

							if($k == 0) $start = $time_house;
							if($k == count($hoursCfg) - 1) $end = $time_house;
						}
					}

					array_push($stage, array(
						"id" => $value["id"],
						"typename" => $value["typename"],
						"start" => $start,
						"end" => $end,
						"time" => $time,
						"hasReserve" => $hasReserve
					));
				}
			}

		}

		return array("stage" => $stage, "tableList" => $tableList, "tableCount" => $tableCount);

	}

	/**
	  * 商家订座-订单列表
	  */
	public function dingzuoOrder(){
		global $dsql;
		global $userLogin;
		global $langData;
		$userid = $userLogin->getMemberID();
		$state = $where = '';

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$state    = $this->param['state'];
				$u        = (int)$this->param['u'];
				$sid      = (int)$this->param['sid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if($u){
			// 验证店铺
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				return array("state" => 200, "info" => $langData['siteConfig'][21][161]);  //权限验证错误
			}else{
				$sid = $ret[0]['id'];
			}

			$where .= " AND `sid` = $sid";

		}else{
			$where .= " AND `uid` = $userid";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		// 待确认
		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_dingzuo_order` WHERE 1 = 1".$where);
		$totalGray = $dsql->dsqlOper($archives." AND `state` = 0", "totalCount");

		// 已确认
		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_dingzuo_order` WHERE 1 = 1".$where);
		$totalAudit = $dsql->dsqlOper($archives." AND `state` = 1", "totalCount");

		// 已取消
		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_dingzuo_order` WHERE 1 = 1".$where);
		$totalCancel = $dsql->dsqlOper($archives." AND `state` = 2", "totalCount");

		// 总数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		if($state != ''){
			$where .= " AND `state` = $state";

			if($state == 0){
				$totalPage = ceil($totalGray/$pageSize);
			}elseif($state == 3){
				$totalPage = ceil($totalAudit/$pageSize);
			}

		}else{
			$totalPage = ceil($totalCount/$pageSize);
		}

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount,
			"totalGray" => $totalGray,
			"totalAudit" => $totalAudit,
			"totalCancel" => $totalCancel
		);

		$list = array();
		$atpage = ($page - 1) * $pageSize;
		$where .= " ORDER BY `state` ASC, `id` DESC LIMIT $atpage, $pageSize";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_order` WHERE 1 = 1".$where);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				$list[$key]['id']          = $value['id'];
				$list[$key]['ordernum']    = $value['ordernum'];
				$list[$key]['state']       = $value['state'];
				$list[$key]['baofang']     = $value['baofang'];
				$list[$key]['baofang_only']     = $value['baofang_only'];
				$list[$key]['people']      = $value['people'];
				$list[$key]['note']        = $value['note'];
				$list[$key]['contact']     = $value['contact'];
				$list[$key]['cancel_bec']  = $value['cancel_bec'];
				$list[$key]['cancel_date'] = $value['cancel_date'] ? date("Y-m-d H:i:s", $value['cancel_date']) : "";
				$list[$key]['cancel_adm']  = $value['cancel_adm'];
				$list[$key]['pubdate']     = $value['pubdate'];
				$list[$key]['time']        = $value['time'];
				$list[$key]['name']        = $value['name'];
				$list[$key]['sex']         = $value['sex'];


				// 查询桌位信息
				$table = "";
				if($value['table']){
					$sql = $dsql->SetQuery("SELECT t1.`typename`, t2.`typename` AS ptypename FROM `#@__business_dingzuo_table` t1 LEFT JOIN `#@__business_dingzuo_table` t2 ON t2.`id` = t1.`parentid` WHERE t1.`id` = ".$value['table']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$table = $ret[0]['ptypename']." ".$ret[0]['typename'];
					}
				}

				$list[$key]['table'] = $table;

				if($u){

					/*$user = '';
					$sql = $dsql->SetQuery("SELECT `nickname`, `username`, `photo` FROM `#@__member` WHERE `id` = ".$value['uid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$user = array(
							'name' => empty($ret[0]['nickname']) ? $ret[0]['username'] : $ret[0]['nickname'],
							'photo' => empty($ret[0]['photo']) ? '' : getFilePath($ret[0]['photo'])
						);
					}
					$list[$key]['user'] = $user;*/

				// 商家信息
				}else{
					$sql = $dsql->SetQuery("SELECT `title`, `logo` FROM `#@__business_list` WHERE `id` = ".$value['sid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$param = array(
							"service" => "business",
							"templates" => "detail",
							"id" => $value['sid']
						);
						$url = getUrlPath($param);
						$list[$key]['store'] = array(
							"id" => $value['sid'],
							"title" => $ret[0]['title'],
							"logo" => $ret[0]['logo'] ? getFilePath($ret[0]['logo']) : "",
							"url" => $url
						);
					}
				}

			}

			return array("pageInfo" => $pageinfo, "list" => $list);
		}else{

			return array("pageInfo" => $pageinfo, "list" => $list);
		}

	}

	/**
		* 商家排队-修改配置
		* @return array
		*/
	public function paiduiSaveConfig(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;

		$paidui_state = (int)$param['paidui_state'];
		$paidui_juli_limit = (int)$param['paidui_juli_limit'];
		$paidui_oncetime = (int)$param['paidui_oncetime'];
		$paidui_overdue = $param['paidui_overdue'];

		$paidui_overdue = empty($paidui_overdue) ? $langData['siteConfig'][21][204] : $paidui_overdue;  //过号作废，请重新取号
		$paidui_oncetime = empty($paidui_oncetime) ? 60 : $paidui_oncetime;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `paidui_state` = '$paidui_state', `paidui_juli_limit` = '$paidui_juli_limit', `paidui_oncetime` = '$paidui_oncetime', `paidui_overdue` = '$paidui_overdue' WHERE `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			return $langData['siteConfig'][20][229];  //修改成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][159]);  //修改失败，请重试
		}

	}

	/**
		* 商家排队-查询当前排队情况
		* @return array
		*/
	public function paiduiSelect(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param   = $this->param;
		$store   = (int)$param['store'];
		if(empty($store)) return array("state" => 200, "info" => "参数错误");

		$list = array();

		// 桌位类型
		$sql    = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_table` WHERE `type` = $store AND `parentid` = 0 ORDER BY `weight` ASC");
		$tabRet = $dsql->dsqlOper($sql, "results");
		if($tabRet){
			foreach ($tabRet as $key => $value) {
				$sql   = $dsql->SetQuery("SELECT `id` FROM `#@__business_paidui_order` WHERE `sid` = $store AND `type` = ".$value['id']." AND `state` = 0");
				$count = $dsql->dsqlOper($sql, "totalCount");

				$list[$key] = array(
					"id" => $value["id"],
					"typename" => $value["typename"],
					"code" => $value["code"],
					"min" => $value["min"],
					"max" => $value["max"],
					"count" => $count,
				);
			}
			return $list;
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][205]);  //店铺暂未配置
		}


	}

	/**
		* 商家排队-用户下单
		* @return array
		*/
	public function paiduiDeal(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$param   = $this->param;
		$store   = (int)$param['store'];
		$people  = (int)$param['people'];

		if(empty($store)) return array("state" => 200, "info" => "参数错误");
		if(empty($people)) return array("state" => 200, "info" => $langData['siteConfig'][21][184]);  //请选择人数

		$time = GetMkTime($time);
		$pubdate = GetMkTime(time());

		$this->param = array("shopid" => $store, "type" => "paidui");
		$cfg = $this->serviceConfig();

		if($cfg && !isset($cfg['state'])){

			if($cfg['paidui_state'] == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][206]);  //提交失败，商家关闭了排队功能

			$archives = $dsql->SetQuery("SELECT * FROM `#@__business_dingzuo_table` WHERE `type` = $store AND `parentid` = 0 ORDER BY `max` ASC");
			$tableCfg = $dsql->dsqlOper($archives, "results");

			$type = 0;
			$typename = "";
			if($tableCfg){
				$typecount = count($tableCfg);
				foreach ($tableCfg as $key => $value) {
					if($people <= $value['max'] && $people >= $value['min']){
						$type = $value['id'];
						$typename = $value['code'];
						break;
					}
				}
				// 没有合适类型的桌位
				if(!$type){
					if($people < $tableCfg[0]['min']){
						$type = $tableCfg[0]['id'];
						$typename = $tableCfg[0]['code'];
					}elseif($people > $tableCfg[$typecount-1]['max']){
						$type = $tableCfg[$typecount-1]['id'];
						$typename = $tableCfg[$typecount-1]['code'];
					}
				}

				// 查找该类型桌位当天排队总数
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_paidui_order` WHERE `sid` = $store AND `type` = $type AND DATE_FORMAT(FROM_UNIXTIME(pubdate),'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d')");
				$count = $dsql->dsqlOper($sql, "totalCount");

				$ordernum = create_ordernum();
				$table = $typename.($count+1);

				$sql = $dsql->SetQuery("INSERT INTO `#@__business_paidui_order` (`uid`, `sid`, `ordernum`, `type`, `table`, `people`, `pubdate`, `state`) VALUES ('$uid', '$store', '$ordernum', '$type', '$table', '$people', '$pubdate', '0')");
				$aid = $dsql->dsqlOper($sql, "lastid");
				if(is_numeric($aid)){
					$to = $uid;
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_paidui_order` WHERE `sid` = $store AND `state` = 0 AND `type` = $type AND `id` < $aid");
					$before = $dsql->dsqlOper($sql, "totalCount");
					// 通知用户
					$param = array(
						"service"  => "member",
						"type" => "user",
						"template" => "order-business",
						"param"   => "type=paidui"
					);
					updateMemberNotice($to, "会员-排队成功", $param, array(
						'type' => $langData['siteConfig'][21][207],  //排队成功通知
						'store' => $cfg['title'],
						"table" => $table,
						'before' => $before,
						'time' => $cfg['paidui_oncetime'] * $before . $langData['siteConfig'][13][45],  //分钟
						'overdue' => $cfg['paidui_overdue'],
						'body' => str_replace('1', $store, str_replace('2', $table, str_replace('3', $before, $langData['siteConfig'][21][208]))),  //您在1取号成功，桌位号：2，在您之前还有3人。
						'fields' => array(
							'keyword1' => '商家名称',
							'keyword2' => '排队号码',
							'keyword3' => '前方排队数',
							'keyword4' => '预计等待时长',
						)
					));

					// 通知商家
					$param = array(
						"service"  => "member",
						"template" => "business-paidui-order"
					);
					updateMemberNotice($cfg['uid'], "会员-排队成功", $param, array(
						'type' => $langData['siteConfig'][21][209],  //您有新的排队订单
						'store' => $cfg['title'],
						"table" => $table,
						'before' => $before,
						'time' => $cfg['paidui_oncetime'] * $before . $langData['siteConfig'][13][45],  //分钟
						'overdue' => $cfg['paidui_overdue'],
						'body' => $langData['siteConfig'][21][209],  //您有新的排队订单
						'fields' => array(
							'keyword1' => '商家名称',
							'keyword2' => '排队号码',
							'keyword3' => '前方排队数',
							'keyword4' => '预计等待时长',
						)
					));

					return $ordernum;
				}else{
					return array("state" => 200, "info" => $langData['siteConfig'][20][180]);  //提交失败，请重试！
				}

			}else{
				if(empty($store)) return array("state" => 200, "info" => $langData['siteConfig'][20][180]);  //提交失败，请重试！
			}

		}
	}

	/**
	  * 商家排队-订单列表
	  */
	public function paiduiOrder(){
		global $dsql;
		global $userLogin;
		global $langData;
		$userid = $userLogin->getMemberID();
		$state = $where = '';

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$state    = $this->param['state'];
				$u        = (int)$this->param['u'];
				$sid      = (int)$this->param['sid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if($u){
			// 验证店铺
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				return array("state" => 200, "info" => $langData['siteConfig'][21][161]);  //权限验证错误
			}else{
				$sid = $ret[0]['id'];
			}

			$where .= " AND `sid` = $sid";

		}else{
			$where .= " AND `uid` = $userid";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		// 排队中
		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_paidui_order` WHERE 1 = 1".$where);
		$totalGray = $dsql->dsqlOper($archives." AND `state` = 0", "totalCount");

		// 已结束
		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_paidui_order` WHERE 1 = 1".$where);
		$totalAudit = $dsql->dsqlOper($archives." AND `state` = 1", "totalCount");

		// 已取消
		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_paidui_order` WHERE 1 = 1".$where);
		$totalCancel = $dsql->dsqlOper($archives." AND `state` = 2", "totalCount");

		// 总数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		if($state != ''){
			$where .= " AND `state` = $state";

			if($state == 0){
				$totalPage = ceil($totalGray/$pageSize);
			}elseif($state == 1){
				$totalPage = ceil($totalAudit/$pageSize);
			}elseif($state == 2){
				$totalPage = ceil($totalCancel/$pageSize);
			}

		}else{
			$totalPage = ceil($totalCount/$pageSize);
		}

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount,
			"totalGray" => $totalGray,
			"totalAudit" => $totalAudit,
			"totalCancel" => $totalCancel
		);

		$list = array();
		$atpage = ($page - 1) * $pageSize;
		$where .= " ORDER BY `state` ASC, `id` DESC LIMIT $atpage, $pageSize";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_paidui_order` WHERE 1 = 1".$where);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				$list[$key]['id']          = $value['id'];
				$list[$key]['ordernum']    = $value['ordernum'];
				$list[$key]['table']       = $value['table'];
				$list[$key]['state']       = $value['state'];
				$list[$key]['people']      = $value['people'];
				$list[$key]['cancel_bec']  = $value['cancel_bec'];
				$list[$key]['cancel_date'] = $value['cancel_date'] ? date("Y-m-d H:i:s", $value['cancel_date']) : "";
				$list[$key]['pubdate']     = $value['pubdate'];

				if($u){

					$user = '';
					$sql = $dsql->SetQuery("SELECT `nickname`, `username`, `photo` FROM `#@__member` WHERE `id` = ".$value['uid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$user = array(
							'name' => empty($ret[0]['nickname']) ? $ret[0]['username'] : $ret[0]['nickname'],
							'photo' => empty($ret[0]['photo']) ? '' : getFilePath($ret[0]['photo'])
						);
					}
					$list[$key]['user'] = $user;

				// 商家信息,排队进展
				}else{
					$sql = $dsql->SetQuery("SELECT `title`, `logo` FROM `#@__business_list` WHERE `id` = ".$value['sid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$param = array(
							"service" => "business",
							"templates" => "detail",
							"id" => $value['sid']
						);
						$url = getUrlPath($param);
						$list[$key]['store'] = array(
							"id" => $value['sid'],
							"title" => $ret[0]['title'],
							"logo" => $ret[0]['logo'] ? getFilePath($ret[0]['logo']) : "",
							"url" => $url
						);
					}

					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_paidui_order` WHERE `sid` = ".$value['sid']." AND `state` = 0 AND `type` = ".$value['type']." AND `id` < ".$value['id']);
					$before = $dsql->dsqlOper($sql, "totalCount");

					$list[$key]['before'] = $before;

				}

			}

			return array("pageInfo" => $pageinfo, "list" => $list);
		}else{

			return array("pageInfo" => $pageinfo, "list" => $list);
		}

	}

	/**
		* 商家排队-更改订单状态，确认或取消
		* @return array
		*/
	public function paiduiUpdateState(){
		global $dsql;
		global $userLogin;
		global $langData;

		$where = "";

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$param = $this->param;
		$u     = (int)$param['u'];
		$id    = (int)$param['id'];
		$state = (int)$param['state'];

		if(empty($id) || empty($state)) return array("state" => 200, "info" => "参数错误！");

		if($u){
			$sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__business_list` WHERE `uid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$store     = $ret[0]['id'];
				$storename =  $ret[0]['title'];
				$oncetime  =  $ret[0]['paidui_oncetime'];
				$overdue   =  $ret[0]['paidui_overdue'];
			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][21][161]);  //权限验证错误
			}

			$where = " AND `sid` = $store";
		}else{
			$where = " AND `uid` = $uid";
		}

		$sql = $dsql->SetQuery("SELECT * FROM `#@__business_paidui_order` WHERE `id` = $id".$where);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 200, "info" => $langData['siteConfig'][21][162]);  //订单不存在！
		}
		$state_ =  $ret[0]['state'];
		$to     =  $ret[0]['uid'];
		$table  =  $ret[0]['table'];


		if($state_ == $state) array("state" => 200, "info" => $langData['siteConfig'][21][199]);  //操作失败，请检查订单状态
		if($state_ == 2) array("state" => 200, "info" => $langData['siteConfig'][21][200]);  //当前订单状态无法修改

		if($state == 2){
			$cancel_bec = empty($param['cancel_bec']) ? $langData['siteConfig'][16][155] : $param['cancel_bec'];  //用户取消
			$cancel_date = GetMkTime(time());

			$more = ", `cancel_bec` = '$cancel_bec', `cancel_date` = '$cancel_date', `cancel_adm` = 1";
		}
		$sql = $dsql->SetQuery("UPDATE `#@__business_paidui_order` SET `state` = $state ".$more." WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){

			if($state == 2){
				// 通知用户
				$param = array(
					"service"  => "member",
					"type" => "user",
					"template" => "order-business",
					"param"   => "type=paidui"
				);

				$config = array(
					'store' => $storename,
					'cancel_bec' => $cancel_bec,
					'fields' => array(
						'keyword1' => '商家名称',
						'keyword2' => '取消原因'
					)
				);

				updateMemberNotice($to, "会员-排队取消", $param, $config);
			}
			// 通知用户排队进站
			$this->paiduiNoticeMenber();
			return $langData['siteConfig'][20][244];  //操作成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][72]);  //操作失败，请重试！
		}


	}


	/**
		* 商家排队-获取当前登陆用户在指定商家的排队情况
		* @return array
		*/
	public function paiduiGetMyorder(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$param = $this->param;
		$store   = (int)$param['store'];

		if(empty($store)) return array("state" => 200, "info" => "参数错误！");


		$archives = $dsql->SetQuery("SELECT `id`, `type`, `table` FROM `#@__business_paidui_order` WHERE `uid` = $uid AND `sid` = $store AND `state` = 0 ORDER BY `id` ASC");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$list = array();

			foreach ($results as $key => $value) {
				$list[$key]['id'] = $value['id'];
				$list[$key]['table'] = $value['table'];

				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_paidui_order` WHERE `sid` = $store AND `state` = 0 AND `type` = ".$value['type']." AND `id` < ".$value['id']);
				$before = $dsql->dsqlOper($sql, "totalCount");

				$list[$key]['before'] = $before;
			}
			return $list;
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][64]);  //暂无数据
		}
	}


	/**
		* 商家排队-通知用户排队进展
		* @return array
		*/
	public function paiduiNoticeMenber(){
		global $dsql;
		global $langData;

		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_paidui_order` WHERE `state` = 0 ORDER BY `id` ASC");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				$id       = $value['id'];
				$sid      = $value['sid'];
				$uid      = $value['uid'];
				$type     = $value['type'];
				$table    = $value['table'];
				$ordernum = $value['ordernum'];

				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_paidui_order` WHERE `sid` = $sid AND `state` = 0 AND `type` = $type AND `id` < $id");
				$before = $dsql->dsqlOper($sql, "totalCount");

				// 通知用户
				$param = array(
					"service"  => "business",
					"template" => "paidui-results",
					"ordernum"   => $ordernum
				);

				$sql = $dsql->SetQuery("SELECT `title`, `paidui_oncetime` FROM `#@__business_list` WHERE `id` = $sid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$store = $ret[0]['title'];
					$oncetime = $ret[0]['paidui_oncetime'];
				}

				if($before == 0){
					$date = $langData['siteConfig'][21][210];  //即将就餐，请及时前往商家
				}else{
					$date = date("H:i", (time() + $oncetime * $before));
				}

				$config = array(
					'store' => $store,
					'table' => $table,
					'before' => $before,
					'date' => $date,
					'fields' => array(
						'keyword1' => '商家名称',
						'keyword2' => '排队号码',
						'keyword3' => '前方排队数',
						'keyword4' => '预计开始时间'
					)
				);

				updateMemberNotice($uid, "会员-排队叫号通知", $param, $config);

			}
		}


	}


	/**
		* 商家买单-修改配置
		* @return array
		*/
	public function maidanSaveConfig(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;

		$maidan_state = (int)$param['maidan_state'];
		$maidan_youhui_open = (int)$param['maidan_youhui_open'];
		$maidan_youhui_value = (int)$param['maidan_youhui_value'];
		$maidan_youhui_limit = $param['maidan_youhui_limit'];

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `maidan_state` = '$maidan_state', `maidan_youhui_open` = '$maidan_youhui_open', `maidan_youhui_value` = '$maidan_youhui_value', `maidan_youhui_limit` = '$maidan_youhui_limit' WHERE `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			return $langData['siteConfig'][20][229];  //修改成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][159]);  //修改失败，请重试
		}

	}


	/**
		* 用户买单-下单
		* @return array
		*/
	public function maidanDeal(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();

		$param = $this->param;

		$store = $param['store'];
		$amount = (float)$param['amount'];
		$amount_alone = (float)$param['amount_alone'];

		$date = GetMkTime(time());

		if(empty($store)) return array("state" => 200, "info" => "参数错误");
		if(empty($amount)) return array("state" => 200, "info" => $langData['siteConfig'][21][211]);  //请输入金额

		$this->param = array("shopid" => $store, "type" => "maidan");
		$cfg = $this->serviceConfig();

		$totalPrice = 0;

		if($cfg && !isset($cfg['state'])){

			if($cfg['maidan_state']){

				// 开启了优惠
				if($cfg['maidan_youhui_open'] && $cfg['maidan_youhui_value']){
					$totalPrice = ($amount - $amount_alone) * (100 - $cfg['maidan_youhui_value']) / 100 + $amount_alone;
				}else{
					$totalPrice = $amount;
				}
				$pubdate = GetMkTime(time());
				$ordernum = create_ordernum();

				// 删除5分钟未支付的记录、当前用户之前未支付的记录
				$sql = $dsql->SetQuery("DELETE FROM `#@__business_maidan_order` WHERE `state` = 0 AND (`pubdate` < $date - 600 || `uid` = $uid)");
				$dsql->dsqlOper($sql, "results");


				$sql = $dsql->SetQuery("INSERT INTO `#@__business_maidan_order` (`uid`, `ordernum`, `sid`, `pubdate`, `amount`, `amount_alone`, `youhui_value`, `payamount`, `paytype`, `state`) VALUES ('$uid', '$ordernum', '$store', '$pubdate', '$amount', '$amount_alone', '".$cfg['maidan_youhui_value']."', '$totalPrice', '$paytype', '0')");
				$oid = $dsql->dsqlOper($sql, "lastid");
				if(is_numeric($oid)){
					return $ordernum;
				}else{
					return array("state" => 200, "info" => $langData['siteConfig'][21][212]);  //订单提交失败
				}
			}

		}

	}


	/**
	 * 支付前验证帐户积分和余额
	 */
	public function checkPayAmount(){
		global $dsql;
		global $userLogin;
		global $cfg_pointName;
		global $cfg_pointRatio;
		global $langData;

		$userid   = $userLogin->getMemberID();
		$param    = $this->param;

		$ordernum   = $param['ordernum'];    //订单号
		$useBalance = $param['useBalance'];  //是否使用余额
		$balance    = $param['balance'];     //使用的余额
		$paypwd     = $param['paypwd'];      //支付密码




		//验证订单
		$sql = $dsql->SetQuery("SELECT * FROM `#@__business_maidan_order` WHERE `ordernum` = '$ordernum' AND `state` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$totalPrice = $ret[0]['payamount'];
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][85]);  //提交失败！
		}

		// 没有使用余额
		if(empty($useBalance)){
			return $totalPrice;
		}

		if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		if(empty($ordernum)) return array("state" => 200, "info" => $langData['siteConfig'][21][85]);  //提交失败！

		//查询会员信息
		$userinfo  = $userLogin->getMemberInfo();
		$usermoney = $userinfo['money'];

		$tit      = array();
		$useTotal = 0;

		//判断是否使用余额，并且验证余额和支付密码
		if($useBalance == 1 && !empty($balance) && !empty($paypwd)){

			//验证支付密码
			$archives = $dsql->SetQuery("SELECT `id`, `paypwd` FROM `#@__member` WHERE `id` = '$userid'");
			$results  = $dsql->dsqlOper($archives, "results");
			$res = $results[0];
			$hash = $userLogin->_getSaltedHash($paypwd, $res['paypwd']);
			if($res['paypwd'] != $hash) return array("state" => 200, "info" => $langData['siteConfig'][21][89]);  //支付密码输入错误，请重试！

			//验证余额
			if($usermoney < $balance) return array("state" => 200, "info" => $langData['siteConfig'][21][213]);  //您的余额不足，支付失败！

			$useTotal += $balance;
			$tit[] = "余额";
		}

		if($useTotal > $totalPrice) return array("state" => 200, "info" => $langData['siteConfig'][21][214]);  //您使用的余额超出订单总费用，请重新输入！

		//返回需要支付的费用
		return sprintf("%.2f", $totalPrice - $useTotal);

	}



	/**
		* 用户买单-下单&支付
		* @return array
		*/
	public function pay_(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();

		$param = $this->param;

		$store = $param['store'];
		$amount = (float)$param['amount'];
		$amount_alone = (float)$param['amount_alone'];
		$paytype = $param['paytype'];

		$date = GetMkTime(time());

		if(empty($store)) return array("state" => 200, "info" => "参数错误");
		if(empty($amount)) return array("state" => 200, "info" => $langData['siteConfig'][21][211]);  //请输入金额
		if(empty($paytype)) return array("state" => 200, "info" => $langData['siteConfig'][21][75]);  //请选择支付方式

		$this->param = array("shopid" => $store, "type" => "maidan");
		$cfg = $this->serviceConfig();

		$totalPrice = 0;

		if($cfg && !isset($cfg['state'])){

			if($cfg['maidan_state']){

				// 开启了优惠
				if($cfg['maidan_youhui_open'] && $cfg['maidan_youhui_value']){
					$totalPrice = ($amount - $amount_alone) * (100 - $cfg['maidan_youhui_value']) / 100 + $amount_alone;
				}else{
					$totalPrice = $amount;
				}

				// 创建订单

				if($ordernum){
					//跳转至第三方支付页面
					createPayForm("business", $ordernum, $totalPrice, $paytype, $langData['siteConfig'][21][215]);  //商家买单
				}
				$pubdate = GetMkTime(time());
				$ordernum = create_ordernum();

				// 删除5分钟未支付的记录
				$sql = $dsql->SetQuery("DELETE FROM `#@__business_maidan_order` WHERE `state` = 0 AND `pubdate` < $date - 600");
				$dsql->dsqlOper($sql, "results");

				$sql = $dsql->SetQuery("INSERT INTO `#@__business_maidan_order` (`uid`, `ordernum`, `sid`, `pubdate`, `amount`, `amount_alone`, `youhui_value`, `payamount`, `paytype`, `state`) VALUES ('$uid', '$ordernum', '$store', '$pubdate', '$amount', '$amount_alone', '".$cfg['maidan_youhui_value']."', '$totalPrice', '$paytype', '0')");
				$oid = $dsql->dsqlOper($sql, "lastid");
				if(is_numeric($oid)){

					//跳转至第三方支付页面
					createPayForm("business", $ordernum, $totalPrice, $paytype, $langData['siteConfig'][21][215]);  //商家买单

				}else{
					$param = array(
						"service" => "business",
						"template" => "maidan",
						"id" => $store
					);
					$url = getUrlPath($param);
					die('<meta charset="UTF-8"><script type="text/javascript">alert("'.$langData['siteConfig'][21][216].'");top.location="'.$url.'";</script>');  //抱歉，支付失败，请重试！
				}

			}

		}

	}


	/**
		* 用户买单-支付
		* @return array
		*/
	public function pay(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid   = $userLogin->getMemberID();

		$param = $this->param;

		$ordernum   = $param['ordernum'];	 //订单号
		$paytype    = $param['paytype'];	 //支付方式
		$useBalance = $param['useBalance'];  //是否使用余额
		$balance    = $param['balance'];     //使用的余额
		$paypwd     = $param['paypwd'];      //支付密码

		$date = GetMkTime(time());

		if(empty($ordernum)) return array("state" => 200, "info" => "参数错误");
		if(empty($paytype)) return array("state" => 200, "info" => $langData['siteConfig'][21][75]);  //请选择支付方式

		$sql = $dsql->SetQuery("SELECT * FROM `#@__business_maidan_order` WHERE `ordernum` = '$ordernum' AND `state` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$order = $ret[0];
		}

		//验证需要支付的费用
		$payTotalAmount = $this->checkPayAmount();

		$param = array(
			"service"  => "business",
			"template" => "maidan",
			"id"       => $order['sid']
		);
		$url = getUrlPath($param);

		if(is_array($payTotalAmount)){
			header("location:".$url);
			die;
		}

		$pubdate = GetMkTime(time());
		$fields = array("`paydate` = $pubdate");
		$paytypeArr = array();
		// 如果使用了余额，更新订单使用余额信息
		if($useBalance == 1 && !empty($balance) && !empty($paypwd)){
			array_push($fields, "`balance` = $balance");
			array_push($paytypeArr, "money");
		}
		if($payTotalAmount > 0){
			array_push($paytypeArr, $paytype);
		}
		array_push($fields, "`paytype` = '".join(",", $paytypeArr)."'");

		$sql = $dsql->SetQuery("UPDATE `#@__business_maidan_order` SET ".join(", ", $fields)." WHERE `ordernum` = '$ordernum'");
		$ret = $dsql->dsqlOper($sql, "update");
		//如果需要支付的金额小于等于0，表示会员使用积分或余额已经付清了，不需要另外去支付
		if($payTotalAmount <= 0){

			$archives = $dsql->SetQuery("INSERT INTO `#@__pay_log` (`ordertype`, `ordernum`, `uid`, `body`, `amount`, `paytype`, `state`, `pubdate`) VALUES ('business', '$ordernum', '$userid', '$ordernum', '0', 'money', 1, $date)");
			$dsql->dsqlOper($archives, "results");

			$this->param = array(
				"ordernum" => $ordernum
			);
			$this->paySuccess();

			$param = array(
				"service" => "business",
				"template" => "payreturn",
				"ordernum" => $ordernum
			);
			$url = getUrlPath($param);
			header("location:".$url);
			die;
		}else{
			//跳转至第三方支付页面
			createPayForm("business", $ordernum, $payTotalAmount, $paytype, $langData['siteConfig'][21][215]);  //商家买单
		}
	}

	/**
		* 支付成功
		* @return array
		*/
	public function paySuccess(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;

		if(!empty($param)){

			$ordernum = $param['ordernum'];

			$where = $paytype == "money" ? " AND o.`ordernum` = '$ordernum'" : " AND l.`ordernum` = '$ordernum'";

			$archives = $dsql->SetQuery("SELECT o.`balance`, o.`amount`, o.`payamount`, l.`uid`, b.`title` store, b.`uid` sjuid FROM `#@__business_maidan_order` o LEFT JOIN `#@__pay_log` l ON l.`body` = o.`ordernum` LEFT JOIN `#@__business_list` b ON b.`id` = o.`sid` WHERE o.`ordernum` = '$ordernum' AND o.`state` = 0");
			$results = $dsql->dsqlOper($archives, "results");
			if($results){

				$store   = $results[0]['store'];	    // 店铺名称
				$amount_ = $results[0]['amount'];	    // 总金额
				$amount  = $results[0]['payamount'];	// 应付金额
				$balance = $results[0]['balance'];		// 使用的余额
				$uid     = $results[0]['uid'];
				$sjuid   = $results[0]['sjuid'];
				$date    = GetMkTime(time());


				$sql = $dsql->SetQuery("UPDATE `#@__business_maidan_order` SET `paydate` = '$date', `state` = 1 WHERE `ordernum` = '$ordernum'");
				$dsql->dsqlOper($sql, "update");

				// 登陆用户
				if($uid != -1){
					// 扣除余额
					if($balance){
						$sql = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - $balance WHERE `id` = $uid");
						$dsql->dsqlOper($sql, "results");
					}

					//保存操作日志
					$info = $langData['siteConfig'][21][215]."：".$ordernum;
					$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '0', '$amount', '$info', '$date')");
					$dsql->dsqlOper($archives, "lastid");


					// 通知用户
					$param = array(
						"service"  => "member",
						"type"  => "user",
						"template" => "order-business",
						"param"   => "type=maidan"
					);
					$currency = echoCurrency(array("type" => "symbol"));
					updateMemberNotice($uid, "会员-买单成功通知", $param, array(
						'type' => $langData['siteConfig'][21][217],  //买单成功通知
						'store' => $store,
						'ordernum' => $ordernum,
						'amount' => $amount_,
						'payamount' => $amount,
						"date" => date("H:i:s", $date),
						'body' => str_replace('1', $store, $langData['siteConfig'][21][218]).$currency.$payamount));   //您在1买单成功，支付金额：
				}


				// 通知商家
				$param = array(
					"service"  => "member",
					"template" => "business-maidan-order"
				);
				updateMemberNotice($sjuid, "会员-买单成功通知", $param, array(
					'type' => $langData['siteConfig'][21][219],  //您有新的买单信息
					'store' => $store,
					'ordernum' => $ordernum,
					'amount' => $amount_,
					'payamount' => $amount,
					"date" => date("H:i:s", $date),
					'body' => $langData['siteConfig'][21][219]  //您有新的买单信息
				));

				// 商家余额
				$sql = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + $amount WHERE `id` = $sjuid");
				$dsql->dsqlOper($sql, "results");

				//保存操作日志
				$info = $langData['siteConfig'][21][215] . "：" . $ordernum;
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$sjuid', '1', '$amount', '$info', '$date')");
				$dsql->dsqlOper($archives, "update");

			}
		}

	}

	/**
	  * 商家买单-订单列表
	  */
	public function maidanOrder(){
		global $dsql;
		global $userLogin;
		global $langData;
		$userid = $userLogin->getMemberID();

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$state    = $this->param['state'];
				$u        = (int)$this->param['u'];
				$sid      = (int)$this->param['sid'];
				$today      = (int)$this->param['today'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$where = " AND `state` = 1";

		if($u){
			// 验证店铺
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				return array("state" => 200, "info" => $langData['siteConfig'][21][161]);  //权限验证错误
			}else{
				$sid = $ret[0]['id'];
			}

			$where .= " AND `sid` = $sid";

		}else{
			$where .= " AND `uid` = $userid";
		}

		if($state != ''){
			$where .= " AND `state` = $state";
		}

		//今日订单
		if($today){
			$where .= " AND DATE_FORMAT(FROM_UNIXTIME(`paydate`), '%Y-%m-%d') = curdate()";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_maidan_order` WHERE 1 = 1".$where);

		// 已支付
		$totalAudit = $dsql->dsqlOper($archives." AND `state` = 1", "totalCount");

		// 未支付
		$totalGray = $dsql->dsqlOper($archives." AND `state` = 0", "totalCount");

		// 总数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		// 总页数
		$totalPage = ceil($totalCount/$pageSize);

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount,
			"totalAudit" => $totalAudit,
			"totalGray" => $totalGray,
		);

		$list = array();
		$atpage = ($page - 1) * $pageSize;
		$where .= " ORDER BY `id` DESC LIMIT $atpage, $pageSize";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__business_maidan_order` WHERE 1 = 1".$where);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				$list[$key]['id']           = $value['id'];
				$list[$key]['uid']          = $value['uid'];
				$list[$key]['sid']          = $value['sid'];
				$list[$key]['ordernum']     = $value['ordernum'];
				$list[$key]['pubdate']      = $value['pubdate'];
				$list[$key]['paydate']      = $value['paydate'];
				$list[$key]['paytype']      = $value['paytype'];
				$list[$key]['amount']       = $value['amount'];
				$list[$key]['state']        = $value['state'];
				$list[$key]['amount_alone'] = $value['amount_alone'];
				$list[$key]['youhui_value'] = $value['youhui_value'];
				$list[$key]['payamount']    = $value['payamount'];

				if($u){

					// $user = '';
					// if($value['uid'] == -1){
					// 	$user = '未登陆用户';
					// }else{
					// 	$sql = $dsql->SetQuery("SELECT `nickname`, `username`, `photo` FROM `#@__member` WHERE `id` = ".$value['uid']);
					// 	$ret = $dsql->dsqlOper($sql, "results");
					// 	if($ret){
					// 		$user = array(
					// 			'name' => empty($ret[0]['nickname']) ? $ret[0]['username'] : $ret[0]['nickname'],
					// 			'photo' => empty($ret[0]['photo']) ? '' : getFilePath($ret[0]['photo'])
					// 		);
					// 	}
					// }
					// $list[$key]['user'] = $user;

				// 商家信息,排队进展
				}else{
					$sql = $dsql->SetQuery("SELECT `title`, `logo` FROM `#@__business_list` WHERE `id` = ".$value['sid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$param = array(
							"service" => "business",
							"templates" => "detail",
							"id" => $value['sid']
						);
						$url = getUrlPath($param);
						$list[$key]['store'] = array(
							"id" => $value['sid'],
							"title" => $ret[0]['title'],
							"logo" => $ret[0]['logo'] ? getFilePath($ret[0]['logo']) : "",
							"url" => $url
						);
					}

					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_paidui_order` WHERE `sid` = $store AND `state` = 0 AND `type` = $type");
					$before = $dsql->dsqlOper($sql, "totalCount");

					$list[$key]['before'] = $before;

				}

			}

			return array("pageInfo" => $pageinfo, "list" => $list);
		}else{

			return array("pageInfo" => $pageinfo, "list" => $list);
		}

	}

	/**
		* 商家餐饮服务-用户取消订单
		* @return array
		*/
	public function serviceCancelOrder(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$param = $this->param;
		$id    = (int)$param['id'];
		$type  = $param['type'];
		$cancel_bec  = $param['cancel_bec'];

		if(empty($id) || empty($type)) return array("state" => 200, "info" => "参数错误！");

		$sql = $dsql->SetQuery("SELECT `state` FROM `#@__business_".$type."_order` WHERE `uid` = $uid AND `id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 200, "info" => $langData['siteConfig'][21][161]);  //订单不存在！
		}

		$state_ = $ret[0]['state'];
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][21][220]);  //更新失败，请检查订单状态

		// 订座和排队可取消
		if($type == "dingzuo" || $type == "paidui"){
			$state = 2;
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][20][295]);  //操作失败
		}

		$state = 2;

		$date = GetMkTime(time());

		$sql = $dsql->SetQuery("UPDATE `#@__business_".$type."_order` SET `state` = $state, `cancel_bec` = '$cancel_bec', `cancel_date` = '$date', `cancel_adm` = 0 WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			if($type == "paidui"){
				// 通知用户排队进展
				$this->paiduiNoticeMenber();
			}
			return $langData['siteConfig'][20][244];  //操作成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][20][295]);  //操作失败！
		}

	}


	public function serviceOrderDel(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$param = $this->param;
		$id    = (int)$param['id'];
		$type  = $param['type'];

		if(empty($id) || empty($type)) return array("state" => 200, "info" => "参数错误！");

		$sql = $dsql->SetQuery("SELECT `state` FROM `#@__business_".$type."_order` WHERE `id` = $id AND `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret) return array("state" => 200, "info" => $langData['siteConfig'][21][161]);  //订单不存在！

		$state = $ret[0]['state'];

		$del = false;	// 是否删除订单
		if($type == "dingzuo"){
			if($state != 1){
				$del = true;
			}
		}

		if($del){
			$sql = $dsql->SetQuery("DELETE FROM `#@__business_".$type."_order` WHERE `id` = $id");
			$dsql->dsqlOper($sql, "results");
			$ret = "ok";
		}else{
			$sql = $dsql->SetQuery("UPDATE `#@__business_".$type."_order` SET `del` = 1 WHERE `id` = $id");
			$ret = $dsql->dsqlOper($sql, "results");
		}
		if($ret == "ok"){
			return $langData['siteConfig'][20][244];  //操作成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][20][295]);  //操作失败！
		}
	}

	/**
	 * 更新商家资料
	 */
	public function updateStoreConfig(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$business = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][157]);  //您还没有入驻商家
		}

		$param    = $this->param;
		$logo     = $param['logo'];
		$title    = $param['title'];
		$tel      = $param['tel'];
		$lng      = $param['lng'];
		$lat      = $param['lat'];
		$cityid   = $param['cityid'];
		$addrid   = $param['addrid'];
		$address  = $param['address'];
		$landmark = $param['landmark'];
		$body     = $param['body'];
		$weeks    = $param['weeks'];
		$opentime = $param['opentime'];
		$wechatqr = $param['wechatqr'];
		$wechatcode = $param['wechatcode'];
		$qq       = $param['qq'];
		$mappic   = $param['mappic'];

		$fields = "";
		if($wechatcode){
			$fields .= "`wechatcode` = '$wechatcode',";
		}
		if($qq){
			$fields .= "`qq` = '$qq',";
		}
		if($mappic){
			$fields .= "`mappic` = '$mappic',";
		}
		if($logo){
			$fields .= "`logo` = '$logo',";
		}
		if($title){
			$fields .= "`title` = '$title',";
		}
		if($tel){
			if(substr($tel, -1, 1) == ","){
				$tel = substr($tel, 0, -1);
			}
			$fields .= "`tel` = '$tel',";
		}
		if($weeks){
            $weeks = str_replace(';', '至', $weeks);
			$fields .= "`weeks` = '$weeks',";
		}
		if($opentime){
			$fields .= "`opentime` = '$opentime',";
		}
		if($wechatqr){
			$fields .= "`wechatqr` = '$wechatqr',";
		}
		if(isset($param['banner'])){
			$fields .= "`banner` = '".$param['banner']."',";
		}
		if(isset($param['video'])){
			$fields .= "`video` = '".$param['video']."',";
		}
		if(isset($param['video_pic'])){
			$fields .= "`video_pic` = '".$param['video_pic']."',";
		}
		if(isset($param['qj_file']) || $param['qj_pics'] || $param['qj_url']){
		    $qj_type = (int)$param['qj_type'];
		    $qj_file = $param['qj_file'];
		    if(!isMobile()) {
                $qj_file = $param['qj_pics'];
                if ($qj_type == 1) {
                    $qj_file = $param['qj_url'];
                }
            }
			$fields .= "`qj_type` = '$qj_type',`qj_file` = '$qj_file',";
		}
		if(isset($param['custom_nav'])){
			$fields .= "`custom_nav` = '".$param['custom_nav']."',";
		}
		if(isset($param['tag'])){
		    $tag = $param['tag'];
		    $tag = is_array($tag) ? join("|", $tag) : $tag;
			$fields .= "`tag` = '$tag',";
		}else{
			if(!isMobile()){
				$fields .= "`tag` = '',";
			}
		}
		if(isset($param['tag_shop'])){
			$fields .= "`tag_shop` = '".$param['tag_shop']."',";
		}
		if(isset($param['circle'])){
			$fields .= "`circle` = '".$param['circle']."',";
		}


		// --------位置
		if($lng && $lat){
			$fields .= "`lng` = '$lng', `lat` = '$lat',";
		}
		if($addrid){
			$fields .= "`addrid` = '$addrid',";
		}
		if($cityid){
			$fields .= "`cityid` = '$cityid',";
		}
		if($address){
			$fields .= "`address` = '$address',";
		}
		if($landmark){
			$fields .= "`landmark` = '$landmark',";
		}

		// --------介绍
		if($body){
			$fields .= "`body` = '$body',";
		}

		if($fields == ""){
			return array("state" => 200, "info" => $langData['siteConfig'][20][295]);  //操作失败！
		}

		$fields = substr($fields, 0, strlen($fields) - 1);

		$sql = $dsql->SetQuery("UPDATE `#@__business_list` SET $fields WHERE `id` = ".$business);
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			return $langData['siteConfig'][20][244];  //操作成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][20][295]);  //操作失败！
		}
	}

	/**
	 * 更新商家自定义菜单
	 */
	public function updateStoreCustomMenu(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$business = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][157]);  //您还没有入驻商家
		}

		$param    = $this->param;
		$id       = (int)$param['id'];
		$jump     = (int)$param['jump'];
		$weight   = (int)$param['weight'];
		$del      = (int)$param['del'];
		$title    = $param['title'];
		$jump_url = $param['jump_url'];
		$body     = $param['body'];

		if(!$del){
			if(empty($title)){
				return array("state" => 200, "info" => "请输入标题");
			}
			if($jump){
				if(empty($jump_url)){
					return array("state" => 200, "info" => "请输入跳转链接");
				}
			}elseif(empty($body)){
				return array("state" => 200, "info" => "请输入正文");
			}
		}

		if($id){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_menu` WHERE `uid` = $uid AND `id` = $id");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				return array("state" => 200, "info" => "参数错误");
			}
			if($del){
				$sql = $dsql->SetQuery("DELETE FROM `#@__business_menu` WHERE `id` = $id");
			}else{
				$sql = $dsql->SetQuery("UPDATE `#@__business_menu` SET `title` = '$title', `jump` = '$jump', `jump_url` = '$jump_url', `body` = '$body' WHERE `id` = $id");
			}
		}else{
			$sql = $dsql->SetQuery("INSERT INTO `#@__business_menu` (`uid`, `title`, `jump`, `jump_url`, `body`, `weight`) VALUES ('$uid', '$title', '$jump', '$jump_url', '$body', '$weight')");
		}
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){

			$sql = $dsql->SetQuery("SELECT * FROM `#@__business_menu` WHERE `uid` = $uid ORDER BY `weight`, `id`");
			$ret = $dsql->dsqlOper($sql, "results");

			return $ret;

			return $langData['siteConfig'][20][244];  //操作成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][20][295]);  //操作失败！
		}

	}


	/**
	 * 获取商家自定义菜单
	 */
	public function getStoreCustomMenu(){
		global $dsql;
		$param = $this->param;
		$id = (int)$param['id'];
		$uid = (int)$param['uid'];

		if(empty($id) && empty($uid)) return array("state" => 200, "info" => "参数错误！");

		if(empty($uid)){
			$sql = $dsql->SetQuery("SELECT `uid` FROM `#@__business_list` WHERE `id` = $id");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['uid'];
			}else{
				return array("state" => 200, "info" => "商家不存在！");
			}
		}

        // 自定义菜单
        $menu = array();
        $sql = $dsql->SetQuery("SELECT * FROM `#@__business_menu` WHERE `uid` = $uid ORDER BY `weight`, `id`");
		$res = $dsql->dsqlOper($sql, "results");
		if($res){
			foreach ($res as $k => $v) {
				$menu[$k]['id'] = $v['id'];
				$menu[$k]['title'] = $v['title'];
				$menu[$k]['jump'] = $v['jump'];

				if($v['jump']){
					$menu[$k]['url'] = $v['jump_url'];
				}else{
					$menu[$k]['body'] = $v['body'];
				}
			}
		}

		return $menu;
	}

	/**
	 * 更新商家模块开关
	 */
	public function updateBusinessModuleSwitch(){
		global $dsql;
		global $userLogin;
		global $langData;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$sql = $dsql->SetQuery("SELECT `id`, `cityid`, `addrid`, `title`, `type`, `expired`, `bind_module` FROM `#@__business_list` WHERE `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$business = $ret[0];
			if($ret[0]['type'] == 1){
				// return array("state" => 200, "info" => '抱歉，此功能仅限企业版商家使用');
			}
			$now = time();
			if($ret['expired'] > $now){
				return array("state" => 200, "info" => '您的商家入驻状态已过期');
			}
			$bind_module = $ret[0]['bind_module'];
			$bind_module = $bind_module ? explode(",", $bind_module) : array();
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][21][157]);  //您还没有入驻商家
		}

		$param = $this->param;
		$name  = $param['module'];
		$state = (int)$param['state'];

		$tab = "";
		$userid_f = "";
		$no_store = array('dingzuo', 'paidui', 'diancan', 'maidan', 'tandian');

		if(!in_array($name, $no_store)){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_module` WHERE `name` = '$name' AND `state` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				return array("state" => 200, "info" => '参数错误！');
			}
			if($name == "shop"){
				$tab = "shop_store";
			}
			switch($name){
				case "shop" :
					$tab = "shop_store";
					$userid_f = "userid";
					break;
				case "info" :
					$tab = "infoshop";
					$userid_f = "uid";
					break;
				case "tuan" :
					$tab = "tuan_store";
					$userid_f = "uid";
					break;
				case "job" :
					$tab = "job_company";
					$userid_f = "uid";
					break;
				case "dating" :
					$tab = "dating_member";
					$userid_f = "userid";
					break;
				case "house" :
					$tab = "house_zjcom";
					$userid_f = "userid";
					break;
				case "waimai" :
					$tab = "waimai_shop";
					$userid_f = "userid";
					break;
			}
		// 切换开关
		}else{
			$sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `{$name}_state` = $state WHERE `id` = ".$business['id']);
			$dsql->dsqlOper($sql, "update");
		}

		$k = array_search($name, $bind_module);

		if($k === false){
			if($state){
				array_push($bind_module, $name);
			}
		}else{
			if(!$state){
				unset($bind_module[$k]);
			}
		}

		// 模块店铺开关
		if($tab){
			$where = "";
			if($name == "dating"){
				$where .= " AND `type` = 2";
			}
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `".$userid_f."` = $uid".$where);
			$res = $dsql->dsqlOper($sql, "update");
			if($res){
				$fields = " `store_switch` = $state";

				$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET ".$fields." WHERE `".$userid_f."` = $uid".$where);
				$ret = $dsql->dsqlOper($sql, "update");
			}
		}

		$new_bind_module = join(",", $bind_module);

		$sql = $dsql->SetQuery("UPDATE `#@__business_list` SET `bind_module` = '$new_bind_module' WHERE `id` = ".$business['id']);
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			return $langData['siteConfig'][20][244];  //操作成功
		}else{
			return array("state" => 200, "info" => $langData['siteConfig'][20][295]);  //操作失败！
		}

	}

	/**
	 * 发送商家信息到手机上
	 * 1、获取当前商家ID
	 * 2、获取用户的手机号码
	 */
	public function sendBusiness(){
		global $dsql;
		global $userLogin;

		$param   = $this->param;
		$id      = $param['id'];
		$phone   = $param['phone'];
		$uid = $userLogin->getMemberID();

		if(empty($id)){
			return array("state" => 200, "info" => '没有该信息');
		}

		if(empty($phone)){
			return array("state" => 200, "info" => '请填写手机号码');
		}

		$sql = $dsql->SetQuery("SELECT `id`, `uid`, `title`, `address`, `tel`, `phone` FROM `#@__business_list` WHERE `id` = '$id'");
		$res = $dsql->dsqlOper($sql, "results");
		if($res){
			$userid = $res[0]['uid'];
			$business = $res[0]['title'];
			$address = $res[0]['address'];
			$tel = $res[0]['tel'] . '/' . $res[0]['phone'];
			//消息通知
			$param = array(
				"service"  => "business",
				"template" => "detail",
				"id"       => $id
			);
			$url = getUrlPath($param);

			//查询帐户信息
			if($uid != -1){
				$sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = ".$uid);
				$ret = $dsql->dsqlOper($sql, "results");
				$username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
			}else{
				$username = '先生/女士';
			}

			if($uid != -1){
				updateMemberNotice($uid, "商家-发送商家联系方式", $param, array("username" => $username, "business" => $business, "address" => $address, "tel" => $tel, "url" => $url), $phone);
			}else{
				sendsms($phone, 1, "", "", false, false, "商家-发送商家联系方式", array("username" => $username, "business" => $business, "address" => $address, "tel" => $tel, "url" => $url));
			}
			return 'ok';
		}else{
			return array("state" => 200, "info" => '没有该信息');
		}
	}

	/**
     * 商家公告
     * @return array
     */
	public function notice(){
		global $dsql;
		$pageinfo = $list = array();
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$cityid = getCityId($this->param['cityid']);
		if($cityid){
			$where .= " AND `cityid` = ".$cityid;
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `color`, `redirecturl`, `litpic`, `pubdate`, `body` FROM `#@__business_noticelist` WHERE `arcrank` = 0 $where ORDER BY `weight` DESC, `id` DESC");
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";

		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();

		$param = array(
			"service"     => "business",
			"template"    => "noticesdetail",
			"id"          => "%id%"
		);
		$urlParam = getUrlPath($param);

		foreach ($results as $key => $val) {
			$list[$key]['title'] = $val['title'];
			$list[$key]['pubdate'] = $val['pubdate'];
			$list[$key]['color'] = $val['color'];
			$list[$key]['body'] = cn_substrR(html2text($val['body']), 150);
			$list[$key]['redirecturl'] = $val['redirecturl'];
			$list[$key]['litpic']      = getFilePath($val['litpic']);
			$list[$key]['url'] = str_replace("%id%", $val['id'], $urlParam);
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
     * 公告内容
     * @return array
     */
	public function noticeDetail(){
		global $dsql;
		$noticeDetail = array();

		if(empty($this->param)){
			return array("state" => 200, "info" => '公告ID不得为空！');
		}

		if(!is_numeric($this->param)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT `title`, `color`, `redirecturl`, `litpic`, `body`, `pubdate` FROM `#@__business_noticelist` WHERE `arcrank` = 0 AND `id` = ".$this->param);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$noticeDetail["title"]       = $results[0]['title'];
			$noticeDetail["color"]       = $results[0]['color'];
			$noticeDetail["redirecturl"] = $results[0]['redirecturl'];
			$noticeDetail["litpic"]      = getFilePath($results[0]['litpic']);
			$noticeDetail["body"]        = $results[0]['body'];
			$noticeDetail["pubdate"]     = $results[0]['pubdate'];
		}
		return $noticeDetail;
	}


	/********************* ^ 探店s ^ *****************************/

	/**
	 * 探店分类
	 */
	public function discoveryType(){
		global $dsql;
		$type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$type     = (int)$this->param['type'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}
		$results = $dsql->getTypeList($type, "business_discoverytype", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}

	/**
	 * 发布,编辑探店文章
	 */
	public function putDiscovery(){
		global $dsql;
		global $userLogin;

		$param  = $this->param;
		$cityid =  $param['cityid'];
		$title  = filterSensitiveWords(addslashes($param['title']));
		$typeid = (int)$param['typeid'];
		$litpic = $param['litpic'];
		$writer = $param['writer'];
		$id     = (int)$param['id'];
		$sid    = $param['sid'];
		$body   = filterSensitiveWords($param['body']);

		$userid = $userLogin->getMemberID();
		if($userid < 0) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		if($id){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_discoverylist` WHERE `uid` = $userid AND `id` = $id");
			$res = $dsql->dsqlOper($sql, "results");
			if(!$res) return array("state" => 200, "info" => "文章不存在或权限不足");
		}

		if(empty($cityid)) return array("state" => 200, "info" => "请选择城市");
		if(empty($title)) return array("state" => 200, "info" => "请填写标题");
		if(empty($litpic)) return array("state" => 200, "info" => "请上传缩略图");
		if(empty($body)) return array("state" => 200, "info" => "请填写正文");
		if(empty($writer)) return array("state" => 200, "info" => "请输入作者");

		$state = 0;

		if(empty($id)){
			$pubdate = time();
			$sql = $dsql->SetQuery("INSERT INTO `#@__business_discoverylist` (`cityid`, `typeid`, `sid`, `uid`, `title`, `litpic`, `body`, `state`, `pubdate`, `writer`) VALUES ('$cityid', '$typeid', '$sid', '$userid', '$title', '$litpic', '$body', $state, '$pubdate', '$writer')");
			$aid = $dsql->dsqlOper($sql, "lastid");
			if(is_numeric($aid)){
				return "发布成功，请等待审核";
			}else{
				return array("state" => 200, "info" => "发布失败，请重试");
			}
		}else{
			$sql = $dsql->SetQuery("UPDATE `#@__business_discoverylist` SET `cityid` = $cityid, `sid` = '$sid', `title` = '$title', `litpic` = '$litpic', `body` = '$body', `state` = $state, `writer` = '$writer' WHERE `id` = $id");
			$res = $dsql->dsqlOper($sql, "update");
			if($res == "ok"){
				return "修改成功，请等待审核";
			}else{
				return array("state" => 200, "info" => "修改失败，请重试");
			}
		}

	}

	/**
	 * 探店列表
	 */
	public function discoveryList(){
		global $dsql;
		global $userLogin;
		global $langData;
		$pageinfo = $pageInfo = $list = array();
		$store = $addrid = $typeid = $title = $orderby = $page = $pageSize = $where = $where1 = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$u        = $this->param['u'];
				$id       = (int)$this->param['id'];
				$typeid   = $this->param['typeid'];
				$title    = $this->param['title'];
				$keywords = $this->param['keywords'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];

				$title = $title ? $title : $keywords;
			}
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$loginUid = $userLogin->getMemberID();
		$ip = GetIP();

        if($u != 1){
			$cityid = getCityId($this->param['cityid']);
			if($cityid){
				$where .= " AND `cityid` in (".$cityid.")";
			}

			$where .= " AND `state` = 1";

		}else{
			$where .= " AND `uid` = $loginUid";

			if($state != ""){
				$where1 = " AND `state` = ".$state;
			}
		}

		if(empty($id)){

			if($typeid){
				$where .= " AND `typeid` = $typeid";
			}

			if($title){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `title` LIKE '%$title%'");
				$res = $dsql->dsqlOper($sql, "results");
				if($res){
					global $arr_data;
					$arr_data = "";
					$ids = arr_foreach($res);
					$where .= " AND `sid` IN (".join(",", $ids).")";
				}else{
					$where .= " AND 1 = 2";
				}
			}

			$orderby = " ORDER BY `weight` DESC, `id` DESC";

	        $now = time();

	        $sql = $dsql->SetQuery("SELECT COUNT(`id`) c FROM `#@__business_discoverylist` WHERE 1 = 1".$where);
	        $res = $dsql->dsqlOper($sql, "results");

	        $totalCount = $res[0]['c'];

	        //总分页数
			$totalPage = ceil($totalCount/$pageSize);

			if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

			$pageinfo = array(
				"page" => $page,
				"pageSize" => $pageSize,
				"totalPage" => $totalPage,
				"totalCount" => $totalCount
			);

			$archives = $dsql->SetQuery("SELECT * FROM `#@__business_discoverylist` WHERE 1 = 1".$where);

			$atpage = $pageSize*($page-1);
			$where = $pageSize != -1 ? " LIMIT $atpage, $pageSize" : "";

		}else{
			$where .= " AND `id` = $id";
			$archives = $dsql->SetQuery("SELECT * FROM `#@__business_discoverylist` WHERE 1 = 1");
		}

		$results = $dsql->dsqlOper($archives.$orderby.$where, "results");

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']          = $val['id'];
				$list[$key]['title']       = $val['title'];
				$list[$key]['click']       = $val['click'];
				$list[$key]['description'] = cn_substrR(strip_tags($val['body']), 100);
				$list[$key]['litpic']      = $val['litpic'] ? getFilePath($val['litpic']) : "";

				$mtype = 0;
				$photo = "";
				$username = "网友";

				$sql = $dsql->SetQuery("SELECT `id`, `mtype`, `nickname`, `company`, `photo` FROM `#@__member` WHERE `id` = ".$val['uid']);
				$res = $dsql->dsqlOper($sql, "results");
				if($res){
					$mtype = $res[0]['mtype'];
					$photo = $res[0]['photo'] ? getFilePath($res[0]['photo']) : "";
					switch($res[0]['mtype']){
						case 1:
							$username = $res[0]['nickname'];
							break;
						case 2:
							$username = $res[0]['company'] ? $res[0]['company'] : $res[0]['nickname'];
						default :
							$username = $res[0]['nickname'] ? $res[0]['nickname'] : "管理员";
					}
				}
				$list[$key]['mtype']  = $mtype;
				$list[$key]['writer'] = $val['writer'] ? $val['writer'] : $username;
				$list[$key]['photo']  = $photo;

				$list[$key]['typeid']  = $val['typeid'];
				$typename = "";
				if($val['typeid']){
					$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__business_discoverytype` WHERE `id` = ".$val['typeid']);
					$res = $dsql->dsqlOper($sql, "results");
					if($res){
						$typename = $res[0]['typename'];
					}
				}
				$list[$key]['typename'] = $typename;

				//验证是否已经点赞
				$zanparams = array(
					"module" => "business",
					"temp"   => "discovery_detail",
					"id"     => $val['id'],
					"check"  => 1
				);
				$zan = checkIsZan($zanparams);
				$list[$key]['zan'] = $zan == "has" ? 1 : 0;

				$list[$key]['zannum'] = (int)$val['zan'];

				if($id){
					$list[$key]['body'] = $val['body'];
				}
			}
		}

		return array("pageInfo" => $pageInfo, "list" => $list);

	}


	/**
	 * 探店详情
	 */
	public function discoveryDetail(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$id = (int)$param['id'];

		if($id){
			$detail = $this->discoveryList();

			if($detail && $detail['list']){
				return $detail['list'][0];
			}
		}
	}


	/**
     * 评论列表
     * @return array
     */
	public function common(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$newsid = $orderby = $page = $pageSize = $where = "";

		if(!is_array($this->param)){
			return array("state" => 200, "info" => '格式错误！');
		}else{
			$newsid    = $this->param['newsid'];
			$orderby   = $this->param['orderby'];
			$page     = $this->param['page'];
			$pageSize = $this->param['pageSize'];
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$oby = " ORDER BY `id` DESC";
		if($orderby == "hot"){
			$oby = " ORDER BY `good` DESC, `id` DESC";
		}

		$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__business_discoverycommon` WHERE `aid` = ".$newsid." AND `ischeck` = 1 AND `floor` = 0".$oby);
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

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
				$list[$key]['id']      = $val['id'];
				$list[$key]['userinfo'] = $userLogin->getMemberInfo($val['userid']);
				$list[$key]['content'] = $val['content'];
				$list[$key]['dtime']   = $val['dtime'];
				$list[$key]['ftime']   = floor((GetMkTime(time()) - $val['dtime']/86400)%30) > 30 ? date("Y-m-d", $val['dtime']) : FloorTime(GetMkTime(time()) - $val['dtime']);
				$list[$key]['ip']      = $val['ip'];
				$list[$key]['ipaddr']  = $val['ipaddr'];
				$list[$key]['good']    = $val['good'];
				$list[$key]['bad']     = $val['bad'];

				$userArr = explode(",", $val['duser']);
				$list[$key]['already'] = in_array($userLogin->getMemberID(), $userArr) ? 1 : 0;

				$list[$key]['lower']   = $this->getCommonList($val['id']);
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
	 * 遍历评论子级
	 * @param $fid int 评论ID
	 * @return array
	 */
	function getCommonList($fid){
		if(empty($fid)) return false;
		global $dsql;
		global $userLogin;

		$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__business_discoverycommon` WHERE `floor` = ".$fid." AND `ischeck` = 1 ORDER BY `id` DESC");
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		if($totalCount > 0){
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach($results as $key => $val){
					$list[$key]['id']      = $val['id'];
					$list[$key]['userinfo'] = $userLogin->getMemberInfo($val['userid']);
					$list[$key]['content'] = $val['content'];
					$list[$key]['dtime']   = $val['dtime'];
					$list[$key]['ftime']   = floor((GetMkTime(time()) - $val['dtime']/86400)%30) > 30 ? $val['dtime'] : FloorTime(GetMkTime(time()) - $val['dtime']);
					$list[$key]['ip']      = $val['ip'];
					$list[$key]['ipaddr']  = $val['ipaddr'];
					$list[$key]['good']    = $val['good'];
					$list[$key]['bad']     = $val['bad'];

					$userArr = explode(",", $val['duser']);
					$list[$key]['already'] = in_array($userLogin->getMemberID(), $userArr) ? 1 : 0;

					$list[$key]['lower']   = $this->getCommonList($val['id']);
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
	public function dingCommon(){
		global $dsql;
		global $userLogin;

		$id = $this->param['id'];
		if(empty($id)) return "请传递评论ID！";
		$memberID = $userLogin->getMemberID();
		if($memberID == -1 || empty($memberID)) return "请先登录！";

		$archives = $dsql->SetQuery("SELECT `duser` FROM `#@__business_discoverycommon` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){

			$duser = $results[0]['duser'];

			//如果此会员已经顶过则return
			$userArr = explode(",", $duser);
			if(in_array($userLogin->getMemberID(), $userArr)) return "已顶过！";

			//附加会员ID
			if(empty($duser)){
				$nuser = $userLogin->getMemberID();
			}else{
				$nuser = $duser . "," . $userLogin->getMemberID();
			}

			$archives = $dsql->SetQuery("UPDATE `#@__business_discoverycommon` SET `good` = `good` + 1 WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");

			$archives = $dsql->SetQuery("UPDATE `#@__business_discoverycommon` SET `duser` = '$nuser' WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");
			return $results;

		}else{
			return "评论不存在或已删除！";
		}
	}


	/**
     * 发表评论
     * @return array
     */
	public function sendCommon(){
		global $dsql;
		global $userLogin;
		$param = $this->param;

		$aid     = $param['aid'];
		$id      = $param['id'];
		$content = addslashes($param['content']);

		if(empty($aid) || empty($content)){
			return array("state" => 200, "info" => '必填项不得为空！');
		}

		$content = filterSensitiveWords(cn_substrR($content,250));

		include HUONIAOINC."/config/article.inc.php";
		$ischeck = (int)$customCommentCheck;

		$archives = $dsql->SetQuery("INSERT INTO `#@__business_discoverycommon` (`aid`, `floor`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `ischeck`, `duser`) VALUES ('$aid', '$id', '".$userLogin->getMemberID()."', '$content', ".GetMkTime(time()).", '".GetIP()."', '".getIpAddr(GetIP())."', 0, 0, '$ischeck', '')");
		$lid  = $dsql->dsqlOper($archives, "lastid");
		if($lid){
			$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__business_discoverycommon` WHERE `id` = ".$lid);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$list['id']      = $results[0]['id'];
				$list['userinfo'] = $userLogin->getMemberInfo($results[0]['userid']);
				$list['content'] = $results[0]['content'];
				$list['dtime']   = $results[0]['dtime'];
				$list['ftime']   = GetMkTime(time()) - $results[0]['dtime'] > 30 ? $results[0]['dtime'] : FloorTime(GetMkTime(time()) - $results[0]['dtime']);
				$list['ip']      = $results[0]['ip'];
				$list['ipaddr']  = $results[0]['ipaddr'];
				$list['good']    = $results[0]['good'];
				$list['bad']     = $results[0]['bad'];
				return $list;
			}
		}else{
			return array("state" => 200, "info" => '评论失败！');
		}

	}

	public function detailHtml(){
		global $dsql;
		global $cfg_staticPath;
		global $cfg_staticVersion;

		$param = $this->param;

		$id = (int)$this->param['id'];
		if(empty($id)) return '<p>商家信息错误</p>';

		$this->param = $id;
		$detail = $this->storeDetail();

		$isMobile = isMobile();
		$iframe = $param['iframe'];

		$content = "";
		$tpl = HUONIAOROOT."/templates/siteConfig/";
		$tpl .= $isMobile ? "business_panel_touch.html" : "business_panel.html";
		if(is_file($tpl)){
			$content = file_get_contents($tpl);
		}

		if(!$detail || (isset($detail['state']) && $detail['state'] != 1)){
			if($content == "") return '<p>店铺不存在或状态异常</p>';
			$replaceAll = array(
				'state',
				'cfg_staticPath',
				'cfg_staticVersion',
				'iframe',
			);
			$state = 0;
			foreach ($replaceAll as $value) {
				$content = str_replace("__{$value}__", ${$value}, $content);
			}
			return $content;
		}
		if($content == "") return '';

		// print_r($detail);die;
		$state = 1;
		$title = $detail['title'];
		$logo = $detail['logo'];
		$qq = $detail['qq'] ? $detail['qq'] : '暂无';
		$wechatcode = $detail['wechatcode'] ? $detail['wechatcode'] : '暂无';
		$address = $detail['address'];
		$weekDay = $detail['weekDay'];
		$opentime = $detail['opentime'];

		$param = array(
			"service" => "business",
			"template" => "detail",
			"id" => $detail['id']
		);
		$url = getUrlPath($param);


		$rz = "";
		if($detail['member']['phoneCheck']){
			$rz .= '<img src="/static/images/rz_phoneCheck.png" alt="">';
		}
		if($detail['member']['promotion']){
			$tit = '保障金：'.echoCurrency(array("type" => "symbol")).$detail['member']['promotion'].echoCurrency(array("type" => "short"));
			$rz .= '<img src="/static/images/rz_promotion.png" alt="" title="'.$tit.'">';
		}
		if($detail['member']['licenseState']){
			$rz .= '<img src="/static/images/rz_licenseState.png" alt="">';
		}



		$replaceAll = array(
			'state',
			'cfg_staticPath',
			'cfg_staticVersion',
			'url',
			'logo',
			'title',
			'rz',
			'qq',
			'wechatcode',
			'address',
			'weekDay',
			'opentime',
			'iframe',
		);

		foreach ($replaceAll as $value) {
			$content = str_replace("__{$value}__", ${$value}, $content);
		}
		return $content;
	}


	/********************* v 探店e v *****************************/

}
