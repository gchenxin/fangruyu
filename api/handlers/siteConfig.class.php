<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 系统模块API接口
 *
 * @version        $Id: siteConfig.class.php 2014-3-20 下午17:56:16 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class siteConfig {
	private $param;  //参数
    public static $langData;
	/**
     * 构造函数
	 *
     * @param string $action 动作名
     */
    public function __construct($param = array()){
		$this->param = $param;
        global $langData;
        self::$langData = $langData;
	}

	/**
     * 系统基本参数
     * @return array
     */
	public function config(){

		global $cfg_basehost;        //网站域名
		global $cfg_webname;         //网站名称
		global $cfg_shortname;       //简称
		global $cfg_fileUrl;         //网站附件地址
		global $cfg_weblogo;         //网站logo地址
		global $cfg_keywords;        //网站关键字
		global $cfg_description;     //网站描述
		global $cfg_beian;           //网站ICP备案号
		global $cfg_hotline;         //网站咨询热线
		global $cfg_powerby;         //网站版权信息
		global $cfg_statisticscode;  //统计代码
		global $cfg_visitState;      //网站运营状态
		global $cfg_visitMessage;    //禁用时的说明信息
		global $cfg_timeZone;        //网站默认时区
		global $cfg_mapCity;         //地图默认城市
		global $cfg_map;             //地图配置
		global $cfg_map_google;      //google密钥
		global $cfg_map_baidu;       //百度密钥
		global $cfg_map_qq;          //腾讯密钥
		global $cfg_map_amap;        //高德密钥
		global $cfg_template;        //首页风格
		global $cfg_touchTemplate;   //首页风格
		global $cfg_softSize;        //附件上传限制大小
		global $cfg_softType;        //附件上传类型限制
		global $cfg_thumbSize;       //缩略图上传限制大小
		global $cfg_thumbType;       //缩略图上传类型限制
		global $cfg_atlasSize;       //图集上传限制大小
		global $cfg_atlasType;       //图集上传类型限制
		global $cfg_photoSize;       //头像上传限制大小
		global $cfg_photoType;       //头像上传类型限制
		global $cfg_staticVersion;
		//var_dump($cfg_staticVersion);
		//获取当前城市名
		global $siteCityInfo;
		if(is_array($siteCityInfo)){
			$cityName = $siteCityInfo['name'];
		}


		$cfg_weblogo = getFilePath($cfg_weblogo);
		$params = !empty($this->param) && !is_array($this->param) ? explode(',',$this->param) : "";

		switch ($cfg_map) {
			case 1:
				$cfg_map_key = $cfg_map_google;
				break;
			case 2:
				$cfg_map_key = $cfg_map_baidu;
				break;
			case 3:
				$cfg_map_key = $cfg_map_qq;
				break;
			case 4:
				$cfg_map_key = $cfg_map_amap;
				break;
			default:
				$cfg_map_key = $cfg_map_baidu;
				break;
		}

		$return = array();
		if(!empty($params) > 0){

			foreach($params as $key => $param){
				if($param == "baseHost"){
					$return['baseHost'] = $cfg_basehost;
				}elseif($param == "webName"){
					$return['webName'] = str_replace('$city', $cityName, $cfg_webname);
				}elseif($param == "shortName"){
					$return['shortName'] = str_replace('$city', $cityName, $cfg_shortname);
				}elseif($param == "webLogo"){
					$return['webLogo'] = $cfg_weblogo;
				}elseif($param == "keywords"){
					$return['keywords'] = str_replace('$city', $cityName, $cfg_keywords);
				}elseif($param == "description"){
					$return['description'] = str_replace('$city', $cityName, $cfg_description);
				}elseif($param == "beian"){
					$return['beian'] = $cfg_beian;
				}elseif($param == "hotline"){
					$return['hotline'] = $cfg_hotline;
				}elseif($param == "powerby"){
					$return['powerby'] = str_replace('$city', $cityName, $cfg_powerby);
				}elseif($param == "statisticscode"){
					$return['statisticscode'] = $cfg_statisticscode;
				}elseif($param == "visitState"){
					$return['visitState'] = $cfg_visitState;
				}elseif($param == "visitMessage"){
					$return['visitMessage'] = $cfg_visitMessage;
				}elseif($param == "timeZone"){
					$return['timeZone'] = $cfg_timeZone;
				}elseif($param == "mapCity"){
					$return['mapCity'] = $cfg_mapCity;
				}elseif($param == "map"){
					$return['map'] = $cfg_map;
				}elseif($param == "mapKey"){
					$return['mapKey'] = $cfg_map_key;
				}elseif($param == "template"){
					$return['template'] = $cfg_template;
				}elseif($param == "touchTemplate"){
					$return['touchTemplate'] = $cfg_touchTemplate;
				}elseif($param == "softSize"){
					$return['softSize'] = $cfg_softSize;
				}elseif($param == "softType"){
					$return['softType'] = $cfg_softType;
				}elseif($param == "thumbSize"){
					$return['thumbSize'] = $cfg_thumbSize;
				}elseif($param == "thumbType"){
					$return['thumbType'] = $cfg_thumbType;
				}elseif($param == "atlasSize"){
					$return['atlasSize'] = $cfg_atlasSize;
				}elseif($param == "atlasType"){
					$return['atlasType'] = $cfg_atlasType;
				}elseif($param == "photoSize"){
					$return['photoSize'] = $cfg_photoSize;
				}elseif($param == "photoType"){
					$return['photoType'] = $cfg_photoType;
				}elseif($param == "staticVersion"){
					$return['staticVersion'] = $cfg_staticVerison;
				}
			}

		}else{
			$return['baseHost']       = $cfg_basehost;
			$return['webName']        = str_replace('$city', $cityName, $cfg_webname);
			$return['shortName']      = str_replace('$city', $cityName, $cfg_shortname);
			$return['webLogo']        = $cfg_weblogo;
			$return['keywords']       = str_replace('$city', $cityName, $cfg_keywords);
			$return['description']    = str_replace('$city', $cityName, $cfg_description);
			$return['beian']          = $cfg_beian;
			$return['hotline']        = $cfg_hotline;
			$return['powerby']        = str_replace('$city', $cityName, $cfg_powerby);
			$return['statisticscode'] = $cfg_statisticscode;
			$return['visitState']     = $cfg_visitState;
			$return['visitMessage']   = $cfg_visitMessage;
			$return['timeZone']       = $cfg_timeZone;
			$return['mapCity']        = $cfg_mapCity;
			$return['map']            = $cfg_map;
			$return['mapKey']         = $cfg_map_key;
			$return['template']       = $cfg_template;
			$return['touchTemplate']  = $cfg_touchTemplate;
			$return['softSize']       = $cfg_softSize;
			$return['softType']       = $cfg_softType;
			$return['thumbSize']      = $cfg_thumbSize;
			$return['thumbType']      = $cfg_thumbType;
			$return['atlasSize']      = $cfg_atlasSize;
			$return['atlasType']      = $cfg_atlasType;
			$return['photoSize']      = $cfg_photoSize;
			$return['photoType']      = $cfg_photoType;
			$return['staticVerison']  = $cfg_staticVersion;
		}

		return $return;

	}


	/**
     * 系统所有模块
     * @return array
     */
	public function siteModule(){
		global $dsql;
		global $cfg_staticPath;
		global $cfg_secureAccess;
		global $cfg_basehost;
		global $cfg_staticVersion;
		global $cfg_sharePic;

		$platform = $this->param['platform'];    //平台
		$type = $this->param['type'];    //默认只输出系统已安装模块，如果为 1 则输出后台模块管理中的所有数据，包括自定义导航

        $isWxMiniprogram = GetCookie('isWxMiniprogram');

		$page = $this->param['page'];			 //根据页面筛选

		$moduleArr = array();
		$config_path = HUONIAOINC."/config/";

		$where = '';

		if($page == 'touchHome'){
		    $type = 1;
        }
		if(!$type){
			$where = ' AND `type` = 0';
		}

		$sql = $dsql->SetQuery("SELECT `type`, `title`, `subject`, `name`, `wx`, `icon`, `link`, `bold`, `target`, `color` FROM `#@__site_module` WHERE `state` = 0".$where." ORDER BY `weight`, `id`");
		$result = $dsql->dsqlOper($sql, "results");
		if($result){
			foreach ($result as $key => $value) {
                if(
                    !$isWxMiniprogram || ($isWxMiniprogram && $value['wx'])
                ){
					$sName = $value['name'];

					if($page == "touchHome"){
						if($sName == "special"|| $sName == "website") continue;
					}

					//引入配置文件
					$serviceInc = $config_path.$sName.".inc.php";
					if(file_exists($serviceInc)){
						require($serviceInc);
					}

					//重置自定义配置
					$subDomain = $customSubDomain;
					global $customSubDomain;
					$customSubDomain = $subDomain;

					//获取功能模块配置参数
                    if($sName) {
                        $configHandels = new handlers($sName, "config");
                        $moduleConfig = $configHandels->getHandle();
                    }

					if((is_array($moduleConfig) && $moduleConfig['state'] == 100) || $value['type'] == 1){
						$moduleConfig  = $moduleConfig['info'];

						$moduleArr[] = array(
							"name" => $value['subject'] ? $value['subject'] : $value['title'],
							"icon" => (strstr($value['icon'], '/') ? $cfg_secureAccess.$cfg_basehost.$value['icon'] : (strstr($value['icon'], '.') ? $cfg_secureAccess.$cfg_basehost.'/static/images/admin/nav/' . $value['icon'] : getFilePath($value['icon']))) . "?v=".$cfg_staticVersion,
							"code" => $value['name'],
							"bold" => $value['bold'],
							"target" => $value['target'],
							"color" => $value['color'],
							"searchUrl" => $cfg_secureAccess.$cfg_basehost.'/search-list.html?action='.$sName.'&keywords=',
							"url" => $value['type'] ? $value['link'] : $moduleConfig['channelDomain'],
							'title' => $moduleConfig['title'],
							'description' => $moduleConfig['description'],
							'logo' => $customSharePic ? getFilePath($customSharePic) : ($cfg_sharePic ? getFilePath($cfg_sharePic) : $moduleConfig['logoUrl'])
						);

					}
				}
			}
		}

		return $moduleArr;
	}


	/**
	 * 已开通的城市
	 */
	public function siteCity(){
		global $dsql;
		global $cfg_secureAccess;
		global $cfg_basehost;
		global $customSubDomain;
		global $cfg_staticVersion;
		global $HN_memory;

        $list = array();
		$module = is_array($this->param) ? $this->param['module'] : 'siteConfig';  //所在模块

        //读缓存
        $site_city_cache = $HN_memory->get('site_city');
        if($site_city_cache){
            $list = $site_city_cache;
        }else {

            //缓存城市数据
            $data = json_decode(@file_get_contents(HUONIAOROOT . "/system_site_city.json"), true);
            if (!$data || $data['expire_time'] < $cfg_staticVersion) {
                $sql = $dsql->SetQuery("SELECT c.*, a.`id` cid, a.`typename`, a.`pinyin` FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` ORDER BY c.`id`");
                $ret = $dsql->dsqlOper($sql, "results");
                if ($ret) {

                    foreach ($ret as $key => $value) {
                        $domainInfo = getDomain('siteConfig', 'city', $value['cid']);
                        $domain = $domainInfo['domain'];
                        $ndomain = "";
                        if ($value['type'] == 0) {
                            $ndomain = $domain;
                        } elseif ($value['type'] == 1) {
                            $ndomain = $domain . "." . str_replace('www.', '', $cfg_basehost);
                        } elseif ($value['type'] == 2) {
                            $ndomain = $cfg_basehost . (count($ret) == 1 ? "" : "/" . $domain);
                        }

                        $list[$key]['id'] = $value['id'];
                        $list[$key]['cityid'] = $value['cid'];
                        $list[$key]['domain'] = $domain;
                        $list[$key]['url'] = $cfg_secureAccess . $ndomain;
                        $list[$key]['name'] = $value['typename'];
                        $list[$key]['pinyin'] = $value['pinyin'];
                        $list[$key]['type'] = $value['type'];
                        $list[$key]['default'] = $value['default'];
                        $list[$key]['hot'] = $value['hot'];
                        $list[$key]['count'] = count($ret);  //分站城市数量
                    }

                    if ($list) {

                        //写入缓存
                        $HN_memory->set('site_city', $list);

                        //文件缓存
                        $siteCityData = new stdClass();
                        $siteCityData->expire_time = time();
                        $siteCityData->data = $list;
                        $fp = @fopen(HUONIAOROOT . "/system_site_city.json", "w");
                        @fwrite($fp, json_encode($siteCityData));
                        @fclose($fp);
                    }
                }
            } else {
                $list = $data['data'];
            }
        }

        //自定义模块
        if($module && $module != 'siteConfig'){
            require(HUONIAOINC."/config/".$module.".inc.php");

            foreach ($list as $key => $val) {
                $customChannelDomain = getDomainFullUrl($module, $customSubDomain, $val);
                $list[$key]['url'] = $customChannelDomain;
            }
        }

		return $list;
	}


	/**
	 * 根据定位的城市验证城市是否开通
	 */
	public function verifyCity(){
		global $dsql;
		$province = $this->param['region'];    //省
		$city     = $this->param['city'];      //市
		$district = $this->param['district'];  //区
		$module   = $this->param['module'];    //所在模块

		if(empty($province) && empty($city)){
			return array("state" => 200, "info" => '数据不得为空！');
		}

		$data = array();
		$cityArr = $this->siteCity();
		if($cityArr){
			foreach ($cityArr as $key => $value) {
				if(strpos($province, $value['name']) !== false || strpos($city, $value['name']) !== false || strpos($district, $value['name']) !== false){
					$data = array("name" => $value['name'], "cityid" => $value['cityid'], "pinyin" => $value['pinyin'], "url" => $value['url'], "domain" => $value['domain'], "type" => $value['type'], "default" => $value['default'], "count" => $value['count']);

					//自定义模块
//					if($module && $module != 'siteConfig'){
//						require(HUONIAOINC."/config/".$module.".inc.php");
//						$customChannelDomain = getDomainFullUrl($module, $customSubDomain, $value);
//						$data['url'] = $customChannelDomain;
//					}

				}
			}

			if($data){
				return $data;
			}else{
				return array("state" => 200, "info" => $city . $district . '未开通分站');
			}

		}else{
			return array("state" => 200, "info" => '未开通分站');
		}

	}


    /**
     * 根据定位的城市获取城市信息
     * 传入省市区：江苏省 苏州市 吴中区，返回数据库中的城市信息：array(ids => 166, 2066, names => 苏州, 吴中);
     */
    public function verifyCityInfo(){
        global $dsql;
        $province = $this->param['region'];    //省
        $city     = $this->param['city'];      //市
        $district = $this->param['district'];  //区

        if(empty($province) && empty($city) && empty($district)){
            return array("state" => 200, "info" => '数据不得为空！');
        }

        $cid = $scid = 0;  //定位城市ID，下探到的最后一级区域ID
        $nameArr = array();
        $cityArr = $this->siteCity();
        if($cityArr){
            foreach ($cityArr as $key => $value) {
                if(strpos($province, $value['name']) !== false){
                    $cid = $value['cityid'];
                    array_push($nameArr, $city);
                }
                if(strpos($city, $value['name']) !== false){
                    $cid = $value['cityid'];
                    array_push($nameArr, $district);
                }
                if(strpos($district, $value['name']) !== false){
                    $cid = $value['cityid'];
                }
            }

            //默认赋值，区域ID等于城市ID
            $scid = $cid;

            //如果需要继续往下
            if($nameArr) {
                //获取分站城市下的区域
                $cityInfoArr = $dsql->getTypeList($cid, "site_area");

                foreach ($nameArr as $key => $val) {
                    foreach ($cityInfoArr as $k => $v) {
                        if(strpos($val, $v['typename']) !== false){
                            $scid = $v['id'];
                        }
                        //下级
                        if($v['lower']){
                            foreach ($v['lower'] as $k_ => $v_) {
                                if(strpos($val, $v_['typename']) !== false){
                                    $scid = $v_['id'];
                                }
                            }
                        }
                    }

                }
            }

            //取区域ID所有父级
            global $data;
            $data = "";
            $addrArr = getParentArr("site_area", $scid);
            $addrIds = array_reverse(parent_foreach($addrArr, "id"));

            global $data;
            $data = "";
            $addrNames = array_reverse(parent_foreach($addrArr, "typename"));

            $idIndex = array_search($cid, $addrIds);
            $newIdsArr = array_slice($addrIds, $idIndex);
            $newNamesArr = array_slice($addrNames, $idIndex);

            if($cid){
                return array('ids' => $newIdsArr, 'names' => $newNamesArr);
            }else{
                return array("state" => 200, "info" => $city . $district . '未开通分站');
            }

        }else{
            return array("state" => 200, "info" => '未开通分站');
        }

    }




	/**
	 * 根据城市简拼验证城市是否开通
	 */
	public function verifyCityDomain(){
		global $dsql;
		$domain = $this->param;
		$domain = is_array($domain) ? $domain['domain'] : $domain;
		if(empty($domain)){
			return array("state" => 200, "info" => '数据不得为空！');
		}

		$data = array();
		$cityArr = $this->siteCity();
		if($cityArr){
			foreach ($cityArr as $key => $value) {
				if($value['domain'] == $domain){
					$data = array("cityid" => $value['cityid'], "name" => $value['name'], "pinyin" => $value['pinyin'], "url" => $value['url'], "domain" => $value['domain'], "type" => $value['type'], "default" => $value['default'], "count" => $value['count']);
				}
			}

			if($data){
				return $data;
			}else{
				return array("state" => 200, "info" => '验证失败！');
			}

		}else{
			return array("state" => 201, "info" => '系统暂未开通分站功能！');
		}



		//验证省份
	}


	/**
     * 安全配置参数
     * @return array
     */
	public function safe(){

		global $cfg_regstatus;        //会员注册开关
		global $cfg_regclosemessage;  //会员注册关闭原因
		global $cfg_replacestr;       //敏感词过滤
		global $cfg_seccodestatus;    //启用验证码的功能
		global $cfg_secqaastatus;     //启用验证问题的功能
		$params = !empty($this->param) && !is_array($this->param) ? explode(',',$this->param) : "";

		$return = array();
		if(!empty($params)){

			foreach($params as $key => $param){
				if($param == "regstatus"){
					$return['regstatus'] = $cfg_regstatus;
				}elseif($param == "regclosemessage"){
					$return['regclosemessage'] = $cfg_regclosemessage;
				}elseif($param == "replacestr"){
					$return['replacestr'] = $cfg_replacestr;
				}elseif($param == "seccodestatus"){
					$return['seccodestatus'] = $cfg_seccodestatus;
				}elseif($param == "secqaastatus"){
					$return['secqaastatus'] = $cfg_secqaastatus;
				}elseif($param == "safeqa"){
					$return['safeqa'] = $this->safeqa();
				}
			}

		}else{
			$return['regstatus'] = $cfg_regstatus;
			$return['regclosemessage'] = $cfg_regclosemessage;
			$return['replacestr'] = $cfg_replacestr;
			$return['seccodestatus'] = $cfg_seccodestatus;
			$return['secqaastatus'] = $cfg_secqaastatus;
			$return['safeqa'] = $this->safeqa();
		}

		return $return;

	}


	/**
     * 验证问题数据
     * @return array
     */
	public function safeqa(){
		global $dsql;
		$archives = $dsql->SetQuery("SELECT `id`, `question`, `answer` FROM `#@__safeqa`");
		$results = $dsql->dsqlOper($archives, "results");
		return $results;
	}


	/**
     * 支付方式
     * @return array
     */
	public function payment(){
		global $dsql;
        $isWxMiniprogram = GetCookie('isWxMiniprogram');
        $list = array();

		$archives = $dsql->SetQuery("SELECT `id`, `pay_code`, `pay_name`, `pay_desc` FROM `#@__site_payment` WHERE `state` = 1 ORDER BY `weight`, `id` DESC");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
		    foreach ($results as $key => $val){
		        if($isWxMiniprogram && $val['pay_code'] == 'wxpay'){
		            array_push($list, $val);
                }elseif(!$isWxMiniprogram){
                    array_push($list, $val);
                }
            }
		}
		return $list;
	}


	/**
     * 网站地区
     * @return array
     */
	public function addr(){
        global $dsql;
		$type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
			}else{
				$type     = (int)$this->param['type'];
				$hideSameCity = (int)$this->param['hideSameCity'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}

        //可操作的城市，多个以,分隔
        $userLogin = new userLogin($dbo);
        $adminCityIds = $userLogin->getAdminCityIds();
        $adminCityIds = empty($adminCityIds) ? 0 : $adminCityIds;

		//一级
		if(empty($type)){

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
			$results = $dsql->getTypeList($type, "site_area", $son, $page, $pageSize, '', '', $hideSameCity);
			if($results){
				return $results;
			}
		}
	}


	/**
     * 网站地区
     * @return array
     */
	public function area(){
		global $dsql;
		$type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
			}else{
				$type     = (int)$this->param['type'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}
		$results = $dsql->getTypeList($type, "site_area", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 地铁线路
     * @return array
     */
	public function subway(){
		global $dsql;
		$city = "";
		$subwayListArr = array();

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
			}else{
				$city = (int)$this->param['city'];
				$addrids = $this->param['addrids'];
			}
		}

		if(empty($addrids)){
			$addrids = $city;
		}
		// if(empty($city)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
		if(empty($addrids)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		// 地址集合
		$addrArr = explode(",", $addrids);
		rsort($addrArr);
		foreach ($addrArr as $key => $value) {
			$sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__site_subway` WHERE `cid` = ".$value." ORDER BY `weight`");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				foreach ($ret as $key => $value) {
					$subwayListArr[$key]['id'] = $value['id'];
					$subwayListArr[$key]['title'] = $value['title'];

					$sql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__site_subway_station` WHERE `sid` = ".$value['id']." ORDER BY `weight`");
					$res = $dsql->dsqlOper($sql, "results");
					$subwayListArr[$key]['lower'] = $res;
				}
				break;
			}
		}
		return $subwayListArr;
		// $sql = $dsql->SetQuery("SELECT * FROM `#@__site_subway` WHERE `cid` = $city ORDER BY `weight`");
		// $ret = $dsql->dsqlOper($sql, "results");
		// if($ret){
		// 	return $ret;
		// }
	}


	/**
     * 地铁站点
     * @return array
     */
	public function subwayStation(){
		global $dsql;
		$type = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
			}else{
				$type = (int)$this->param['type'];
			}
		}

		if(empty($type)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		$sql = $dsql->SetQuery("SELECT * FROM `#@__site_subway_station` WHERE `sid` = $type ORDER BY `weight`");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			return $ret;
		}
	}


	/**
     * 已安装模块信息
     * @return array
     */
	public function module(){
		global $dsql;

		$archives = $dsql->SetQuery("SELECT `id`, `icon`, `subject` as title, `subject`, `name`  FROM `#@__site_module` WHERE `state` = 0 AND `type` = 0 AND `parentid` != 0 ORDER BY `weight`, `id`");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			return $results;
		}
	}


	/**
	 * 热门关键词
	 */
	public function hotkeywords(){
		$module = $this->param['module'];

		$where = "";
		$cityid = getCityId($this->param['cityid']);
		if($cityid){
			$where .= " AND `cityid` = ".$cityid;
		}
		$list = array();
		if($module){
			global $dsql;
			$archives = $dsql->SetQuery("SELECT `keyword`, `color`, `href`, `blod`  FROM `#@__site_hotkeywords` WHERE `state` = 0 AND `module` = '$module' $where ORDER BY `weight` DESC, `id` DESC");
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
                switch ($module){
                    case 'article':
                    case 'image':
                        $template='search';
                        break;
                    case 'vote':
                    case 'tieba':
                        $template='index';
                        break;
                    case 'house':
                        $template='sale';
                        break;
                    case 'renovation':
                        $template='albums';
                        break;
                    case 'shop':
                        $template='shop';
                        break;
                    case 'job':
                        $template='zhaopin';
                        break;
                    case 'website':
                        $template='templates';
                        break;
                    default:
                        $template='list';
                        break;
                }
                switch ($module){
                    case 'tuan':
                    case 'renovation':
                        $param='search_keyword=%key%';
                        break;
                    case 'job':
                        $param='title=%key%';
                        break;
                    default:
                        $param='keywords=%key%';
                        break;
                }
				$param = array(
					"service"  => $module,
					"template" => $template,
					"param"    => $param
				);
				$urlParam = getUrlPath($param);

				foreach ($results as $key => $value) {
					$keyword = $value['keyword'];
					$list[$key]['oldkeyword'] = $keyword;
					if(!empty($value['color'])){
						$keyword = '<font color="'.$value['color'].'">'.$keyword.'</font>';
					}
					if($value['blod'] == 1){
						$keyword = '<strong>'.$keyword.'</strong>';
					}
					$list[$key]['keyword'] = $keyword;

					$url = $value['href'];
					if(empty($url)){
						$url = str_replace("%key%", $value['keyword'], $urlParam);
					}
					$list[$key]['href'] = $url;
					$list[$key]['target'] = $value['target'];
				}
			}
		}
		return $list;
	}


	/**
     * 单页文档
     * @return array
     */
	public function singel(){
		global $dsql;
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
			}else{
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
			}
		}

		$pageSize = empty($pageSize) ? 999 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";

		$archives = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__site_singellist` WHERE `type` = 'singel' ORDER BY `id` ASC".$where);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			return $results;
		}
	}


	/**
     * 单页文档内容
     * @return array
     */
	public function singelDetail(){
		global $dsql;
		$singeDetail = array();

		if(empty($this->param)){
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
		}

		if(!is_numeric($this->param)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		$archives = $dsql->SetQuery("SELECT `title`, `body`, `pubdate` FROM `#@__site_singellist` WHERE `type` = 'singel' AND `id` = ".$this->param);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$singeDetail["title"]   = $results[0]['title'];
			$singeDetail["body"]    = $results[0]['body'];
			$singeDetail["pubdate"] = $results[0]['pubdate'];
		}
		return $singeDetail;
	}


	/**
     * 网站公告
     * @return array
     */
	public function notice(){
		global $dsql;
        global $langData;
		$pageinfo = $list = array();
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
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

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `color`, `redirecturl`, `pubdate`, `body` FROM `#@__site_noticelist` WHERE `arcrank` = 0 $where ORDER BY `weight` DESC, `id` DESC");
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]); //暂无数据

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";

		$list = array();
		$results = $dsql->dsqlOper($archives.$where, "results");
		if($results && is_array($results)){
			foreach ($results as $key => $value) {
				$list[$key]['id'] = $value['id'];
				$list[$key]['title'] = $value['title'];
				$list[$key]['color'] = $value['color'];
				$list[$key]['pubdate'] = $value['pubdate'];
				$list[$key]['description'] = cn_substrR(strip_tags($value['body']), 100);

				$url = "";
				if($value['redirecturl']){
					$url = $value['redirecturl'];
				}else{
					$param = array(
						"service"     => "siteConfig",
						"template"    => "notice-detail",
						"typeid"      => $value['id']
					);
					$url = getUrlPath($param);
				}
				$list[$key]['url'] = $url;

			}
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
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
		}

		if(!is_numeric($this->param)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		$archives = $dsql->SetQuery("SELECT `title`, `color`, `redirecturl`, `body`, `pubdate` FROM `#@__site_noticelist` WHERE `arcrank` = 0 AND `id` = ".$this->param);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$noticeDetail["title"]       = $results[0]['title'];
			$noticeDetail["color"]       = $results[0]['color'];
			$noticeDetail["redirecturl"] = $results[0]['redirecturl'];
			$noticeDetail["body"]        = $results[0]['body'];
			$noticeDetail["pubdate"]     = $results[0]['pubdate'];
		}
		return $noticeDetail;
	}


	/**
     * 帮助信息
     * @return array
     */
	public function helps(){
		global $dsql;
		$pageinfo = $list = array();
		$typeid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
			}else{
				$typeid   = $this->param['typeid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		//遍历分类
		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "site_helpstype")){
				$lower = arr_foreach($dsql->getTypeList($typeid, "site_helpstype"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}
			$where .= " AND `typeid` in ($lower)";
		}

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `pubdate` FROM `#@__site_helpslist` WHERE `arcrank` = 0".$where." ORDER BY `weight` DESC, `id` DESC");
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => self::$langData['siteConfig'][21][64]); //暂无数据

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";

		$list = array();
		$results = $dsql->dsqlOper($archives.$where, "results");
		if($results){
			foreach ($results as $key => $value) {
				$list[$key]['id'] = $value['id'];
				$list[$key]['title'] = $value['title'];
				$list[$key]['pubdate'] = $value['pubdate'];

				$param = array(
					"service"     => "siteConfig",
					"template"    => "help-detail",
					"id"          => $value['id']
				);
				$list[$key]['url'] = getUrlPath($param);
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 帮助信息详细
     * @return array
     */
	public function helpsDetail(){
		global $dsql;
		$helpsDetail = array();

		if(empty($this->param)){
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
		}

		if(is_numeric($this->param)) {
            $archives = $dsql->SetQuery("SELECT `title`, `typeid`, `body`, `pubdate` FROM `#@__site_helpslist` WHERE `arcrank` = 0 AND `id` = ".$this->param);
            $results  = $dsql->dsqlOper($archives, "results");
            if($results){
                $helpsDetail["title"]       = $results[0]['title'];
                $helpsDetail["typeid"]      = $results[0]['typeid'];
                $helpsDetail["body"]        = $results[0]['body'];
                $helpsDetail["pubdate"]     = $results[0]['pubdate'];
            }
        }else{
		    $id = $this->param['id'];
            $archives = $dsql->SetQuery("SELECT `title`, `typeid`, `body`, `pubdate` FROM `#@__site_helpslist` WHERE `arcrank` = 0 AND `id` = ".$id);
            $results  = $dsql->dsqlOper($archives, "results");
            if($results){
                $helpsDetail["title"]       = $results[0]['title'];
                $helpsDetail["typeid"]      = $results[0]['typeid'];
                $helpsDetail["body"]        = strip_tags($results[0]['body']);
                $helpsDetail["pubdate"]     = $results[0]['pubdate'];
            }
        }


		return $helpsDetail;
	}


	/**
     * 帮助信息分类
     * @return array
     */
	public function helpsType(){
		global $dsql;
		$type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
			}else{
				$type     = (int)$this->param['type'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}
		$results = $dsql->getTypeList($type, "site_helpstype", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 网站协议
     * @return array
     */
	public function agree(){
		global $dsql;

		if(empty($this->param)){
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
		}

		if(!is_numeric($this->param)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		$archives = $dsql->SetQuery("SELECT `title`, `body` FROM `#@__site_singellist` WHERE `type` = 'agree' AND `id` = ".$this->param);
		$results  = $dsql->dsqlOper($archives, "results");
		return $results;
	}


	/**
     * 网站广告
     * @return array
     */
	public function adv(){
		global $dsql;
		global $cityid;
		global $userLogin;

		global $cfg_advMarkState;  //广告标识状态  0关闭  1开启
        global $cfg_advMarkPostion;  //标识位置  0左上  1右上  2左下  3右下

		$currentCityId = getCityId(is_array($this->param) ? $this->param['cityid'] : 0);
		$cityid = $cityid ? $cityid : $currentCityId;  //当前城市ID

		$param = $this->param;

		//普通模式
		if(is_numeric($param)){
			$id = $param;

		//分站广告
		}else{
			$model = $param['model'];
			$title = $param['title'];
			if($model != "" && $title != ""){

				//团购
				if($model == 'tuan'){
					$tuanService = new tuan();
					$domainInfo = $tuanService->getCity();
					if(empty($domainInfo)) return array("state" => 200, "info" => '城市不存在！');

					$cityid = $domainInfo['cid'];

					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__advlist` WHERE `cityid` = $cityid AND `title` = '$title'");
					$ret = $dsql->dsqlOper($sql, "results");
					if(!$ret) return array("state" => 200, "info" => '广告不存在！');
					$id = $ret[0]['id'];

				//其他情况
				}else{
                    if($param['id'] == "stream"){
                        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__advlist` WHERE `title` = '$title' AND `state` = 1 AND (`class` = 1 || `class` = 2) ORDER BY rand() LIMIT 1");
                    }else{
    					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__advlist` WHERE `title` = '$title' ORDER BY `id` DESC LIMIT 1");
                    }
					$ret = $dsql->dsqlOper($sql, "results");
					if(!$ret){
                        if($param['id'] == "stream"){
                            return "";
                        }
						$uid = $userLogin->getUserID();
						if($uid == -1){
							return array("state" => 200, "info" => '城市不存在！');
						}else{
							$rand = create_check_code();
							$adlist = array();
							$adlist['class'] = 1;
							$adlist['type'] = 'code';
							$adlist['body'] = '<div class="advPlaceholder" id="adP_'.$rand.'"><div class="apCon"><span class="ad_tit">广告位：</span><a class="ad_stu" href="https://help.kumanyun.com/help-5-607.html" target="_blank">官方教程</a><div class="ad_title"><h5 title="这是广告位名称">'.$title.'</h5><h6></h6></div><div class="ad_tips" title="操作提示：复制广告位名称，后台添加此名称的广告即可！">操作提示：复制广告位名称，后台添加此名称的广告即可！</div></div></div><script>calculatedAdvSize("adP_'.$rand.'")</script>';
							return $adlist;
						}
					}else{
						$id = $ret[0]['id'];
					}
				}

			//其他类型
			}else{
				$id = $param['id'];
			}
		}


		if(empty($id)){
			return array("state" => 200, "info" => '广告ID不得为空！');
		}

		if(!is_numeric($id)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		$adlist = array();

		//先查询广告位默认数据
		$archives = $dsql->SetQuery("SELECT `class`, `title`, `starttime`, `endtime`, `body`, `state` FROM `#@__advlist` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$cla   = $results[0]['class'];
			$title = $results[0]['title'];
			$start = $results[0]['starttime'];
			$end   = $results[0]['endtime'];
			$body  = $results[0]['body'];
			$state = $results[0]['state'];
			$date  = GetMkTime(time());

			if($state != 1) return array("state" => 200, "info" => '广告已隐藏！');
			if($date < $start && !empty($start)) return array("state" => 200, "info" => '广告还未开始！');
			if($date > $end && !empty($end)) return array("state" => 200, "info" => '广告已结束！');

            $adlist['id'] = $id;
			$adlist['class'] = $cla;
			$adlist['advTitle'] = $title;
			$body = explode("$$", $body);

			//普通广告
			if($cla == 1){
				$adlist['type'] = $body[0];

				//代码
				if($body[0] == "code"){
					$adlist['body'] = $body[1];
					$adlist['mark'] = $body[2];

				//文字
				}elseif($body[0] == "text"){
					$adlist['title'] = $body[1];
					$adlist['color'] = $body[2];
					$adlist['link']  = $body[3];
					$adlist['size']  = $body[4];
					$adlist['mark']  = $body[5];

				//图片
				}elseif($body[0] == "pic"){
					$adlist['src']    = $body[1];
					$adlist['turl']   = getRealFilePath($body[1]);
					$adlist['href']   = $body[2];
					$adlist['title']  = $body[3];
					$adlist['width']  = $body[4];
					$adlist['height'] = $body[5];
					$adlist['mark']   = $body[6];

					$adlist['advMarkState'] = $cfg_advMarkState;
                    $adlist['advMarkPostion'] = $cfg_advMarkPostion;
                    $adlist['markState'] = (int)$body[6];

				//flash
				}elseif($body[0] == "flash"){
					$adlist['src']    = $body[1];
					$adlist['width']  = $body[2];
					$adlist['height'] = $body[3];
					$adlist['mark']   = $body[4];

				}

			//多图广告
			}elseif($cla == 2){
				$adlist['width']  = $body[0];
				$adlist['height'] = $body[1];
				$list = explode("||", $body[2]);
				foreach ($list as $key => $value) {
					$bod = explode("##", $value);
					$adlist['list'][$key]['src']   = $bod[0];
					$adlist['list'][$key]['turl']  = getRealFilePath($bod[0]);
					$adlist['list'][$key]['title'] = $bod[1];
					$adlist['list'][$key]['link']  = $bod[2];
					$adlist['list'][$key]['desc']  = $bod[3];
					$adlist['list'][$key]['mark']  = $bod[4];

					$adlist['list'][$key]['advMarkState'] = $cfg_advMarkState;
                    $adlist['list'][$key]['advMarkPostion'] = $cfg_advMarkPostion;
                    $adlist['list'][$key]['markState'] = (int)$bod[4];
				}

			//伸缩广告
			}elseif($cla == 3){
				$adlist['time']  = $body[0];
				$adlist['width']  = $body[1];
				$adlist['link']  = $body[2];
				$adlist['large'] = $body[3];
				$adlist['largeHeight'] = $body[4];
				$adlist['small'] = $body[5];
				$adlist['smallHeight'] = $body[6];
				$adlist['mark'] = $body[7];

			//对联广告
			}elseif($cla == 4){
				$adlist['width']  = $body[0];
				$adlist['adwidth']  = $body[1];
				$adlist['adheight']  = $body[2];
				$adlist['topheight']  = $body[3];
				$left  = explode("##", $body[4]);
				$adlist['left']['src']   = $left[0];
				$adlist['left']['link']  = $left[1];
				$adlist['left']['title'] = $left[2];
				$adlist['left']['mark'] = $left[3];
				$right = explode("##", $body[5]);
				$adlist['right']['src']   = $right[0];
				$adlist['right']['link']  = $right[1];
				$adlist['right']['title'] = $right[2];
				$adlist['right']['mark'] = $right[3];

			}elseif($cla == 5){
				$adlist['body'] = $body;
			}


			//查询城市广告位数据
			if($cityid){
				$sql = $dsql->SetQuery("SELECT `body` FROM `#@__advlist_city` WHERE `aid` = $id AND `cityid` = $cityid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){

					$adlist = array();
                    $adlist['id'] = $id;
					$adlist['class'] = $cla;
					$adlist['advTitle'] = $title;

					$body = $ret[0]['body'];
					$body = explode("$$", $body);

					//普通广告
					if($cla == 1){
						$adlist['type'] = $body[0];

						//代码
						if($body[0] == "code"){
							$adlist['body'] = $body[1];
							$adlist['mark'] = $body[2];

						//文字
						}elseif($body[0] == "text"){
							$adlist['title'] = $body[1];
							$adlist['color'] = $body[2];
							$adlist['link']  = $body[3];
							$adlist['size']  = $body[4];
							$adlist['mark']  = $body[5];

						//图片
						}elseif($body[0] == "pic"){
							$adlist['src']    = $body[1];
							$adlist['turl']   = getRealFilePath($body[1]);
							$adlist['href']   = $body[2];
							$adlist['title']  = $body[3];
							$adlist['width']  = $body[4];
							$adlist['height'] = $body[5];
							$adlist['mark']   = $body[6];

							$adlist['advMarkState'] = $cfg_advMarkState;
		                    $adlist['advMarkPostion'] = $cfg_advMarkPostion;
		                    $adlist['markState'] = (int)$body[6];

						//flash
						}elseif($body[0] == "flash"){
							$adlist['src']    = $body[1];
							$adlist['width']  = $body[2];
							$adlist['height'] = $body[3];
							$adlist['mark']   = $body[4];

						}

					//多图广告
					}elseif($cla == 2){
						$adlist['width']  = $body[0];
						$adlist['height'] = $body[1];
						$list = explode("||", $body[2]);
						foreach ($list as $key => $value) {
							$bod = explode("##", $value);
							$adlist['list'][$key]['src']   = $bod[0];
							$adlist['list'][$key]['turl']  = getRealFilePath($bod[0]);
							$adlist['list'][$key]['title'] = $bod[1];
							$adlist['list'][$key]['link']  = $bod[2];
							$adlist['list'][$key]['desc']  = $bod[3];
							$adlist['list'][$key]['mark']  = $bod[4];

							$adlist['list'][$key]['advMarkState'] = $cfg_advMarkState;
		                    $adlist['list'][$key]['advMarkPostion'] = $cfg_advMarkPostion;
		                    $adlist['list'][$key]['markState'] = (int)$bod[4];
						}

					//伸缩广告
					}elseif($cla == 3){
						$adlist['time']  = $body[0];
						$adlist['width']  = $body[1];
						$adlist['link']  = $body[2];
						$adlist['large'] = $body[3];
						$adlist['largeHeight'] = $body[4];
						$adlist['small'] = $body[5];
						$adlist['smallHeight'] = $body[6];
						$adlist['mark'] = $body[7];

					//对联广告
					}elseif($cla == 4){
						$adlist['width']  = $body[0];
						$adlist['adwidth']  = $body[1];
						$adlist['adheight']  = $body[2];
						$adlist['topheight']  = $body[3];
						$left  = explode("##", $body[4]);
						$adlist['left']['src']   = $left[0];
						$adlist['left']['link']  = $left[1];
						$adlist['left']['title'] = $left[2];
						$adlist['left']['mark'] = $left[3];
						$right = explode("##", $body[5]);
						$adlist['right']['src']   = $right[0];
						$adlist['right']['link']  = $right[1];
						$adlist['right']['title'] = $right[2];
						$adlist['right']['mark'] = $right[3];

					}elseif($cla == 5){
						$adlist['body'] = $body;
					}
				}
			}


		}
		return $adlist;
	}


	/**
     * 友情链接分类
     * @return array
     */
	public function friendLinkType(){
		global $dsql;
		$module = $this->param;

		if(empty($module)){
			return array("state" => 200, "info" => '模块名为空！');
		}

		$archives = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__site_friendlinktype` WHERE `model` = '$module' ORDER BY `weight` DESC, `id` DESC");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			return $results;
		}
	}


	/**
     * 友情链接
     * @return array
     */
	public function friendLink(){
		global $dsql;
		$param = $this->param;
		$list = array();
		$where = "";

		$module = $param['module'];
		$type = $param['type'];

		if(empty($module)){
			return array("state" => 200, "info" => '模块名为空！');
		}

		$cityid = getCityId($this->param['cityid']);
		if($cityid){
			$where .= " AND `cityid` = ".$cityid;
		}

		//遍历分类
		if(!empty($type)){
			if($dsql->getTypeList($type, "site_friendlinktype")){
				$lower = arr_foreach($dsql->getTypeList($type, "site_friendlinktype"));
				$lower = $type.",".join(',',$lower);
			}else{
				$lower = $type;
			}
			$where .= " AND `type` in ($lower)";
		}

		$archives = $dsql->SetQuery("SELECT `id`, `sitename`, `sitelink`, `litpic` FROM `#@__site_friendlinklist` WHERE `module` = '$module' AND `arcrank` = 0".$where." ORDER BY `weight` DESC, `id` DESC");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			global $cfg_fileUrl;
			foreach($results as $key => $val){
				$list[$key]['id']       = $val['id'];
				$list[$key]['sitename'] = $val['sitename'];
				$list[$key]['sitelink'] = $val['sitelink'];
				$list[$key]['litpic']   = !empty($val['litpic']) ? $cfg_fileUrl.$val['litpic'] : "";
			}
			return $list;
		}
	}


	/**
	 * 自动提取关键词、描述
	 * @param $type  提取类型 keywords: 关键词  description: 描述
	 * @param $body  需要提取的内容
	 * @return string
	 */
	public function autoget(){
		$param = $this->param;
		$type = $param['type'];
		$title = $param['title'];
		$body = $param['body'];

		if(!empty($type) && !empty($body)){

			$keywords = $description = "";
			$return = AnalyseHtmlBody($body, $description, $keywords, $title);

			if($type == "keywords"){
				return $keywords;
			}else{
				return $description;
			}

		}
	}


	/**
	 * 获取天气预报
	 *
	 */
	public function getWeatherApi(){
		$param = $this->param;

		$weatherInfo = getWeather($param, $smarty);
		return $weatherInfo;
	}


	/**
     * 发送邮件
     * @return array
     */
	public function sendMail(){
		$param = $this->param;

		$email     = $param['email'];
		$mailtitle = $param['mailtitle'];
		$mailbody  = $param['mailbody'];

		if(empty($email) || empty($mailtitle) || empty($mailbody)){
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][1] ) ;//必填项不得为空！
		}

		//发送邮件
		$sendmail = sendmail($email, $mailtitle, $mailbody);
		if($sendmail != ""){
			return "200";
		}else{
			return self::$langData['siteConfig'][20][298]; //发送成功
		}

	}


	/**
		* 判断输入的验证码是否正确
		* @return array
		*/
	public function checkVdimgck(){
		$param = $this->param;
		$code = $param['code'];

		$code = strtolower($code);
		if($code != $_SESSION['huoniao_vdimg_value']){
			return "error";
		}else{
			return "ok";
		}
	}


	/**
		* 发送手机验证码
		* @return array
		*/
	public function getPhoneVerify(){
		$param = $this->param;

		global $dsql;
		global $userLogin;
		global $cfg_shortname;
		global $cfg_hotline;
		global $cfg_geetest;
		global $langData;

		//获取用户ID
		$uid = $userLogin->getMemberID();
		$ip  = GetIP();
		$now = GetMkTime(time());
		$has = false;

		if(!is_array($param)){
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
		}else{
			$type     = $param['type'];
			$code     = $param['code'];			//　第三方登陆类型
			$phone    = $param['phone'];
			$from     = $param['from'];
			$areaCode = (int)$param['areaCode'];
		}


		//如果是进行身份验证，需要进行登录验证，并获取登录用户的手机号码
		if($type == "auth"){
			if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
			$memberInfo = $userLogin->getMemberInfo();
			$phone    = $memberInfo['phone'];
			$areaCode = $memberInfo['areaCode'];
		}

		//如果是入驻商家
		if($type == "join"){
			//如果开启了极验
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

			// $archives = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `phone` = '$phone'");
			// $results  = $dsql->dsqlOper($archives, "totalCount");
			// if($results > 0) return array("state" => 200, "info" => '该手机号码已经入驻过商家！');
		}

		if($type == "sms_login"){
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
        }


		// $terminal = isMobile() ? "mobile" : "pc";

		//如果是注册，需要验证邮箱是否被注册
		if($type == "signup"){

			//如果开启了极验
//			if($cfg_geetest && empty($code)){
//				$geetest_challenge = $param['geetest_challenge'];
//				$geetest_validate  = $param['geetest_validate'];
//				$geetest_seccode   = $param['geetest_seccode'];
//				$terminal          = $param['terminal'];
//				$terminal = empty($terminal) ? "pc" : $terminal;
//
//				$verifyGeetest = json_decode(verifyGeetest($geetest_challenge, $geetest_validate, $geetest_seccode, $terminal), true);
//				if(!is_array($verifyGeetest) || $verifyGeetest['status'] == 'fail'){
//					return array("state" => 200, "info" => $langData['siteConfig'][21][22]);    //图形验证错误，请重试！
//				}
//			}

			if($code){
				$code_field = ", `".$code."_conn`";
			}else{
				$code_field = "";
			}
			$archives = $dsql->SetQuery("SELECT `id`".$code_field." FROM `#@__member` WHERE `phone` = '$phone'");
			$results  = $dsql->dsqlOper($archives, "totalCount");
			// if($results > 0) return array("state" => 200, "info" => $langData['siteConfig'][20][76]);   //该手机号码已经注册过会员！
			if($results){
				// 如果来自绑定操作
				if($from == "bind" && $code){
					// 如果已绑定第三方账号，提示用户
					if($results[0][$code.'_conn']){
						return array("state" => 200, "info" => $langData['siteConfig'][33][2]);   //该手机号码已注册并绑定了此第三方账号，如需将手机号绑定此第三方账号，请先用手机登陆，然后在安全中心进行解绑，然后再绑定此第三方账号！
					}
				}else{
					return array("state" => 200, "info" => $langData['siteConfig'][20][76]);   //该手机号码已经注册过会员！
				}
			}
		}

		//如果是找回密码，需要验证手机号码是否存在
		if($type == "fpwd"){

			$vericode = $param['vericode']; //验证码
			$isend    = $param['isend'];

			//如果开启了极验
			if($cfg_geetest){
				$geetest_challenge = $param['geetest_challenge'];
				$geetest_validate  = $param['geetest_validate'];
				$geetest_seccode   = $param['geetest_seccode'];
				$terminal          = $param['terminal'];
				$terminal = empty($terminal) ? "pc" : $terminal;

				$verifyGeetest = json_decode(verifyGeetest($geetest_challenge, $geetest_validate, $geetest_seccode, $terminal), true);
				if(!is_array($verifyGeetest) || $verifyGeetest['status'] == 'fail'){
					return array("state" => 200, "info" => $langData['siteConfig'][21][22]);    //图形验证错误，请重试！
				}
			}
			else{
				if(strtolower($vericode) != $_SESSION['huoniao_vdimg_value'] && !$isend) return array("state" => 200, "info" => $langData['siteConfig'][20][99]);   //验证码输入错误，请重试！
			}

			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `phone` = '$phone'");
			$results  = $dsql->dsqlOper($archives, "totalCount");
			if($results == 0) return array("state" => 200, "info" => $langData['siteConfig'][20][77]);    //该手机号码没有注册过会员！
		}



		if(empty($type) || empty($phone) || empty($areaCode)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

        //非国际版不需要验证区域码
		$archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
				$international = $results[0]['international'];
				if(!$international){
					$areaCode = "";
				}
		}else{
				return array("state" => 200, "info" => $langData['siteConfig'][33][3]); //短信平台未配置，发送失败！
		}

		$archives = $dsql->SetQuery("SELECT `pubdate` FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `by` = '$uid' AND `lei` = '$type' AND `user` = '".$areaCode.$phone."'");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$has = true;
			$time = $now - $results[0]['time'];
			if($time < 60){
				return array("state" => 200, "info" => str_replace('1', (60-$time), $langData['siteConfig'][21][23]));    //您的发送频率太快，请1秒后稍候重试！
			}
		}

		$content = "";
		$code = $rand_num = rand(100000, 999999);

		//手机认证
		if($type == "verify"){
			$smsTemp = "会员-手机邮箱绑定-发送验证码";
			//$content = "校验码".$code."，您正在进行手机绑定，工作人员不会向您索取，请勿泄漏。如有疑问请致电".$cfg_hotline."。";

		//注册验证
		}elseif($type == "signup"){
			$smsTemp = "会员-注册验证-发送验证码";
			//$content = "校验码".$code."，您`正在进行身份验证，工作人员不会向您索取，请勿泄漏。如有疑问请致电".$cfg_hotline."。";

		//身份验证
		}elseif($type == "auth" || $type == "join"){
			$smsTemp = "会员-安全验证-发送验证码";
			//$content = "校验码".$code."，您正在进行身份验证，工作人员不会向您索取，请勿泄漏。如有疑问请致电".$cfg_hotline."。";

		//找回密码
		}elseif($type == "fpwd"){
			$smsTemp = "会员-找回密码-发送验证码";
			//$content = "校验码".$code."，工作人员不会向您索取，请勿泄漏。如有疑问请致电".$cfg_hotline."。";

		}elseif($type == "sms_login"){
            $smsTemp = "会员-短信登录-发送验证码";
        }elseif ($type == 'shop_order_remind'){
            $smsTemp = "会员-短信登录-发送验证码";
        }

		//发送短信
		if($smsTemp){
			return sendsms($areaCode.$phone, 1, $code, $type, $has, false, $smsTemp);
		}

		//获取短信内容
		// $content = "";
		// $contentTpl = getInfoTempContent("sms", $smsTempId, array("code" => $code));
		// if($contentTpl){
		// 	$content = $contentTpl['content'];
		// }
		//
		// //调用发送短信接口
		// include_once(HUONIAOINC."/class/sms.class.php");
		// $sms = new sms($dbo);
		// $return = $sms->send($phone, $content);
		//
		// if($return == "ok"){
		// 	if($has){
		// 		$archives = $dsql->SetQuery("UPDATE `#@__site_messagelog` SET `code` = '$code', `body` = '$content', `pubdate` = '$now', `ip` = '$ip' WHERE `type` = 'phone' AND `lei` = '$type' AND `user` = '$phone'");
		// 		$results  = $dsql->dsqlOper($archives, "update");
		// 	}else{
		// 		messageLog("phone", $type, $phone, $title, $content, $uid, 0, $code);
		// 	}
		// 	return "ok";
		//
		// }else{
		// 	messageLog("phone", $type, $phone, $title, $content, $uid, 1, $code);
		// 	return array("state" => 200, "info" => '验证码发送失败，请重试！');
		// }

	}


	/**
		* 发送邮箱验证码
		* @return array
		*/
	public function getEmailVerify(){
		$param = $this->param;

		global $dsql;
		global $userLogin;
		global $cfg_shortname;
		global $cfg_hotline;
		global $cfg_webname;
		global $cfg_geetest;
		global $langData;

		//获取用户ID
		$uid = $userLogin->getMemberID();
		$ip  = GetIP();
		$now = GetMkTime(time());
		$has = false;

		if(!is_array($param)){
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！
		}else{
			$type  = $param['type'];
			$email = $param['email'];
		}

		// $terminal = isMobile() ? "mobile" : "pc";

		//如果是进行身份验证，需要进行登录验证，并获取登录用户的手机号码
		if($type == "auth"){
			if($uid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
			$memberInfo = $userLogin->getMemberInfo();
			$email = $memberInfo['email'];

		//如果是注册，需要验证邮箱是否被注册
		}elseif($type == "signup"){

			//如果开启了极验
			if($cfg_geetest){
				$geetest_challenge = $param['geetest_challenge'];
				$geetest_validate  = $param['geetest_validate'];
				$geetest_seccode   = $param['geetest_seccode'];
				$terminal          = $param['terminal'];
				$terminal = empty($terminal) ? "pc" : $terminal;

				$verifyGeetest = json_decode(verifyGeetest($geetest_challenge, $geetest_validate, $geetest_seccode, $terminal), true);
				if(!is_array($verifyGeetest) || $verifyGeetest['status'] == 'fail'){
					return array("state" => 200, "info" => $langData['siteConfig'][21][22]);    //图形验证错误，请重试！
				}
			}

			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `email` = '$email'");
			$results  = $dsql->dsqlOper($archives, "totalCount");
			if($results > 0) return array("state" => 200, "info" => $langData['siteConfig'][20][78]);    //该邮箱地址已经注册过会员！
		}

		if(empty($type) || empty($email)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		$archives = $dsql->SetQuery("SELECT `pubdate` FROM `#@__site_messagelog` WHERE `type` = 'email' AND `by` = '$uid' AND `lei` = '$type' AND `user` = '$email'");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$has = true;
			$time = $now - $results[0]['pubdate'];
			if($time < 60){
				return array("state" => 200, "info" => str_replace('1', (60-$time), $langData['siteConfig'][21][23]));    //您的发送频率太快，请1秒后稍候重试！
			}
		}

		$title   = "";
		$content = "";
		$code = $rand_num = rand(100000, 999999);

		//身份验证
		if($type == "auth" || $type == "signup"){

			$tit = "会员-注册验证-发送验证码";
			if($type == "auth"){
				$tit = "会员-安全验证-发送验证码";
			}

			//获取邮件内容
			$cArr = getInfoTempContent("mail", $tit, array("code" => $code));

			$title = $cArr['title'];
			$content = $cArr['content'];

			// $title = $cfg_webname."-邮箱验证";
			// $content = "您正在进行邮箱验证，本次请求的验证码为：<strong>".$code."</strong>，<br /><br />为了保障您帐号的安全性，请在 48小时内完成绑定，此链接将在您绑定过后失效！<br />激活邮件将在您激活一次后失效。<br /><br />".$cfg_webname."<br />".date("Y-m-d", time())."<br /><br />如您错误的收到了此邮件，请不要点击绑定按钮。<br />这是一封系统自动发出的邮件，请不要直接回复。";
		}

		if($title == "" && $content == ""){
			return array("state" => 200, "info" => $langData['siteConfig'][33][4]);//邮件通知功能未开启，发送失败！
		}

		//调用发送邮件接口
		$replay = sendmail($email, $title, $content);

		$content = addslashes($content);

		if(empty($replay)){

			if($has){
				$archives = $dsql->SetQuery("UPDATE `#@__site_messagelog` SET `code` = '$code', `body` = '$content', `pubdate` = '$now', `ip` = '$ip' WHERE `type` = 'email' AND `lei` = '$type' AND `user` = '$email'");
				$dsql->dsqlOper($archives, "update");
			}else{
				messageLog("email", $type, $email, $title, $content, $uid, 0, $code);
			}

			return "ok";

		}else{
			messageLog("email", $type, $email, $title, $content, $uid, 1, $code);
			return array("state" => 200, "info" => $langData['siteConfig'][20][74]);     //验证码发送失败，请重试！
		}

	}


	/**
		* 获取网站已开通的第三方登录平台
		* @return array
		*/
	public function getLoginConnect(){
		global $dsql;

		$list = array();
		$archives = $dsql->SetQuery("SELECT `id`, `code`, `name` FROM `#@__site_loginconnect` WHERE `state` = 1 ORDER BY `weight`, `id`");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach($results as $key => $val){
				$list[$key]['code']  = $val['code'];
				$list[$key]['name']  = $val['name'];
			}
		}

		return $list;
	}


	/**
		* 获取用户已经绑定的登录平台
		* @return array
		*/
	public function getUserBindLoginConnect(){
		global $dsql;
		global $userLogin;

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => self::$langData['siteConfig'][21][121]);//登录超时，请刷新页面重试！

		$archives = $dsql->SetQuery("SELECT * FROM `#@__member` WHERE `id` = $uid");
		$results = $dsql->dsqlOper($archives, "results");
		if(!$results) return array("state" => 200, "info" => self::$langData['siteConfig'][33][5]);//用户不存在！

		$open = array();
		$i = 0;
		foreach ($results[0] as $key => $value) {
			if(strstr($key, "_conn") && !empty($value)){
				$open[$i] = str_replace("_conn", "", $key);
				$i++;
			}
		}

		$list = array();
		$archives = $dsql->SetQuery("SELECT `id`, `code`, `name` FROM `#@__site_loginconnect` WHERE `state` = 1 ORDER BY `weight`, `id`");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach($results as $key => $val){
				$state = 0;
				if(in_array($val['code'], $open)){
					$state = 1;
				}
				$list[$key]['state'] = $state;
				$list[$key]['code']  = $val['code'];
				$list[$key]['name']  = $val['name'];
			}
		}

		$list = array_sort($list, "state", "desc");

		return $list;
	}



	/**
		* 根据指定表、指定ID获取相关信息
		* @return array
		*/
	public function getPublicParentInfo(){
		global $dsql;
		$param = $this->param;

		$tab  = $param['tab'];
		$id   = $param['id'];

		global $data;
		$data = "";
		$typeArr = getParentArr($tab, $id);
		$ids = array_reverse(parent_foreach($typeArr, "id"));

		global $data;
		$data = "";
		$typeArr = getParentArr($tab, $id);
		$typenames = array_reverse(parent_foreach($typeArr, "typename"));

		return array(
			"ids" => $ids,
			"names" => $typenames
		);

	}

	/**
	 * 移动端大首页底部菜单
	 */
	public function touchHomePageFooter(){
		global $dsql;
		global $langData;
		global $cfg_secureAccess;
		global $cfg_basehost;
		$param = $this->param;

		$tplDir = empty($tplDir) ? $cfg_secureAccess.$cfg_basehost."/static/" : $tplDir;

		$version  = $param['version'];

		//APP配置参数
		$ios_index = $cfg_secureAccess.$cfg_basehost;
		$sql = $dsql->SetQuery("SELECT * FROM `#@__app_config` LIMIT 1");
		$ret = $dsql->dsqlOper($sql, "results");
		if ($ret) {
//			$ios_index = $ret[0]['ios_index'];
			$customBottomButton = $ret[0]['customBottomButton'] ? unserialize($ret[0]['customBottomButton']) : array();
			if(!empty($customBottomButton)){
				foreach ($customBottomButton as $key => $val) {
					$customBottomButton[$key]['name']   = $val['name'];
					$customBottomButton[$key]['icon']   = $val['icon']   ? getFilePath($val['icon'])   : '';
					$customBottomButton[$key]['icon_h'] = $val['icon_h'] ? getFilePath($val['icon_h']) : '';
					$customBottomButton[$key]['url']    = $val['url'];
					$customBottomButton[$key]['login']  = $val['login'] ? $val['login'] : 0;
				}
			}
		}

		if($version == '2.0'){
			if(!empty($customBottomButton) && is_array($customBottomButton) && count($customBottomButton)==5){
				$menu = $customBottomButton;
			}else{
				$menu = array(
					0 => array(
							"name" => '首页',
							"icon" => $tplDir."images/touchHomePageFooter/2.0/ficon1.png",
							"icon_h" => $tplDir."images/touchHomePageFooter/2.0/aficon1.png",
							"url" => $ios_index
						),
					1 => array(
							"name" => '口碑商家',
							"icon" => $tplDir."images/touchHomePageFooter/2.0/ficon2.png",
							"icon_h" => $tplDir."images/touchHomePageFooter/2.0/aficon2.png",
							"url" => getUrlPath(array("service" => "business"))
						),
					2 => array(
							"name" => '发布入驻',
							"icon" => $tplDir."images/touchHomePageFooter/2.0/ficon3.png",
							"icon_h" => $tplDir."images/touchHomePageFooter/2.0/aficon3.png",
							"url" => getUrlPath(array("service" => "member", "type" => "user", "template" => "fabuJoin_touch_popup_3.4")),
							"login" => 1
						),
					3 => array(
							"name" => '消息',
							"icon" => $tplDir."images/touchHomePageFooter/2.0/ficon4.png",
							"icon_h" => $tplDir."images/touchHomePageFooter/2.0/aficon4.png",
							"url" => getUrlPath(array("service" => "member", "type" => "user", "template" => "message")),
							"login" => 1
						),
					4 => array(
							"name" => '我的',
							"icon" => $tplDir."images/touchHomePageFooter/2.0/ficon5.png",
							"icon_h" => $tplDir."images/touchHomePageFooter/2.0/aficon5.png",
							"url" => getUrlPath(array("service" => "member", "type" => "user"))
						)
				);
			}
		}else{
			$menu = array(
				0 => array(
						"name" => $langData['siteConfig'][0][0],
						"icon" => $tplDir."images/ficon1.png",
						"icon_h" => $tplDir."images/aficon1.png",
						"url" => $ios_index
					),
				1 => array(
						"name" => $langData['siteConfig'][16][0],
						"icon" => $tplDir."images/ficon2.png",
						"icon_h" => $tplDir."images/aficon2.png",
						"url" => getUrlPath(array("service" => "siteConfig", "template" => "tcquan"))
					),
				2 => array(
						"name" => $langData['siteConfig'][11][0],
						"icon" => $tplDir."images/ficon3.png",
						"icon_h" => $tplDir."images/aficon3.png",
						"url" => getUrlPath(array("service" => "siteConfig", "template" => "post"))
					),
				3 => array(
						"name" => $langData['siteConfig'][16][1],
						"icon" => $tplDir."images/ficon4.png",
						"icon_h" => $tplDir."images/aficon4.png",
						"url" => getUrlPath(array("service" => "business"))
					),
				4 => array(
						"name" => $langData['siteConfig'][10][0],
						"icon" => $tplDir."images/ficon5.png",
						"icon_h" => $tplDir."images/aficon5.png",
						"url" => getUrlPath(array("service" => "member", "type" => "user"))
					)
			);
		}

		return $menu;
	}


	//获取数据库表结构
	public function getDatabaseStructure(){
		global $dsql;
		$table = $dsql->get_db_detail($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASS'], $GLOBALS['DB_NAME'], $errors);
		$table['prefix'] = $GLOBALS['DB_PREFIX'];

		if($errors){
			return array("state" => 200, "info" => join("、", $errors));
		}
		return $table;
	}

	/**
	 * [验证手机号是否被其他用户绑定]
	 * @return [type] [description]
	 */
	public function checkPhoneBindState(){
		global $dsql;
		global $userLogin;
		$admin = $userLogin->getMemberID();
		if($admin == -1) return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$param = $this->param;
		$phone = $param['phone'];
		if(empty($phone)) return array("state" => 200, "info" => self::$langData['siteConfig'][20][239]);    //请输入手机号

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `phone` = '$phone' AND `id` != $admin");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$RenrenCrypt = new RenrenCrypt();
			$sameConn = base64_encode($RenrenCrypt->php_encrypt($ret[0]['id']));
			return $sameConn;
		}else{
			return "no";
		}
	}


	/**
		* 根据GPS坐标获取详细信息
		* 由于使用的百度地图接口，需要在后台提前配置好百度地图的密钥
		*/
	public function getLocationByGeocoding(){
		global $cfg_map;
		global $cfg_map_baidu_server;
		global $cfg_map_amap_server;
		$param = $this->param;
		$location = $param['location'];

		//百度
		if($cfg_map == 2){
			$curl = curl_init();
		    curl_setopt($curl, CURLOPT_URL, 'http://api.map.baidu.com/geocoder/v2/?location='.$location.'&output=json&pois=1&ak='.$cfg_map_baidu_server);
		    curl_setopt($curl, CURLOPT_HEADER, 0);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($curl, CURLOPT_TIMEOUT, 20);
		    $con = curl_exec($curl);
		    curl_close($curl);

			if($con){
				$con = json_decode($con, true);

				if($con['status'] == 0){
					$result = $con['result'];
					$lng = $result['location']['lng'];
					$lat = $result['location']['lat'];
					$name = $result['sematic_description'];
					$address = $result['formatted_address'];
					$province = $result['addressComponent']['province'];
					$city = $result['addressComponent']['city'];
					$district = $result['addressComponent']['district'];

					//周边poi
					if(count($result['pois']) > 0){
						$poi = $result['pois'][0];
						$name = $poi['name'];
						$address = $poi['addr'];
					}

					return array(
						'lng' => $lng,
						'lat' => $lat,
						'name' => $name,
						'address' => $address,
						'province' => $province,
						'city' => $city,
						'district' => $district
					);

				}else{
					return array('state' => 200, 'info' => $con['message']);
				}
			}

		//高德
		}elseif($cfg_map == 4){

			$lnglat = explode(',', $location);
			$location = $lnglat[1] . ',' . $lnglat[0];  //调整顺序，经度在前，纬度在后

			$curl = curl_init();
		    curl_setopt($curl, CURLOPT_URL, 'https://restapi.amap.com/v3/geocode/regeo?location='.$location.'&extensions=all&batch=false&roadlevel=0&key='.$cfg_map_amap_server);
		    curl_setopt($curl, CURLOPT_HEADER, 0);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($curl, CURLOPT_TIMEOUT, 20);
		    $con = curl_exec($curl);
		    curl_close($curl);

			if($con){
				$con = json_decode($con, true);

				if($con['status'] == 1){
					$result = $con['regeocodes'];
					$name = $result['addressComponent']['township'];
					$address = $result['formatted_address'];
					$province = $result['addressComponent']['province'];
					$city = $result['addressComponent']['city'];
					$district = $result['addressComponent']['district'];

					//周边poi
					if(count($result['pois']) > 0){
						$poi = $result['pois'][0];
						$name = $poi['name'];
					}

					return array(
						'lng' => $lnglat[1],
						'lat' => $lnglat[0],
						'name' => $name,
						'address' => $address,
						'province' => $province,
						'city' => $city,
						'district' => $district
					);

				}else{
					return array('state' => 200, 'info' => $con['info']);
				}
			}
		}
	}



	/**
		* 接口方式获取融云Token
		*/
	public function getRongCloudToken(){
		global $dsql;
		global $userLogin;
		$id = (int)$this->param['id'];
		$type = $this->param['type'];
		if(empty($id)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		if($type == "dating"){
			$sql = $dsql->SetQuery("SELECT `id`, `nickname`, `photo` FROM `#@__dating_member` WHERE `id` = $id");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$userinfo = array(
					"nickname" => $ret[0]['nickname'],
					"photo" => $ret[0]['photo'] ? getFilePath($ret[0]['photo']) : "",
				);
			}else{
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][5]);//用户不存在！
			}
		}else{
			$userinfo = $userLogin->getMemberInfo($id);
		}
		if(is_array($userinfo)){
			$rongCloudToken = getRongCloudToken($id, $userinfo['nickname'], $userinfo['photo']);
			return $rongCloudToken;
		}else{
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][5]);//用户不存在！
		}
	}


	/**
		* 新增即时聊天对话
		* @author gz
		* @param string $mod  模块
		* @param int    $from 发送人会员ID
		* @param int    $to   接收人会员ID
		* @param string $msg  对话内容
		* @param int    $date 时间 unit时间戳
		* @return array
		* @date 2018-07-18
		*/
	public function sendChatTalk(){
		global $dsql;
		$param = $this->param;

		if(!is_array($param)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		$mod  = $param['mod'];
		$from = (int)$param['from'];
		$to   = (int)$param['to'];
		$msg  = $param['msg'];
		$date = $param['date'];

		if(empty($mod)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][6]);//模块名不得为空！
		if(empty($from)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][7]);//发送人会员ID不得为空！
		if(empty($to)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][8]);//接收人会员ID不得为空！
		if(empty($msg)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][9]);//对话内容不得为空！

		// $date = empty($date) ? GetMkTime(time()) : $date;

		$sql = $dsql->SetQuery("INSERT INTO `#@__".$mod."_chat` (`from`, `to`, `msg`, `date`) VALUES ('$from', '$to', '$msg', '$date')");
		if($mod == "dating"){

			$configHandels = new handlers($mod, "sendChatCheck");
			$check = $configHandels->getHandle(array("from" => $from, "to" => $to));

			if(is_array($check) && $check['state'] == 100){
				$pid = $check['info'];
			}else{
				return $check;
			}
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__".$mod."_chat` WHERE ( `pid` = 0 AND ( (`from` = $from && `to` = $to) || (`from` = $to && `to` = $from) ) )");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$pid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][10]);//数据写入失败！
		}

		$sql = $dsql->SetQuery("INSERT INTO `#@__".$mod."_chat` (`from`, `to`, `msg`, `date`, `pid`) VALUES ('$from', '$to', '$msg', '$date', '$pid')");
		$ret = $dsql->dsqlOper($sql, 'lastid');
		if(is_numeric($ret)){
			return self::$langData['siteConfig'][33][11];//对话成功！
		}else{
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][10]);//数据写入失败！
		}

	}


	/**
		* 获取即时聊天对话
		* @author gz
		* @param string $mod      模块
		* @param int    $userid1  会员ID1
		* @param int    $userid2  会员ID2
		* @param int    $page     页码
		* @param int    $pageSize 每页数量
		* @return array
		* @date 2018-07-18
		*/
	public function getChatTalk(){
		global $dsql;
		global $userLogin;
		global $cfg_secureAccess;
		global $cfg_basehost;
        global $langData;
		$param = $this->param;
		$pageinfo = $list  = array();

		if(!is_array($param)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		$mod      = $param['mod'];
		$userid1  = $param['userid1'];
		$userid2  = $param['userid2'];
		$page     = (int)$param['page'];
		$pageSize = (int)$param['pageSize'];

		if(empty($mod)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][6]);//模块名不得为空！
		if(empty($userid1) || empty($userid2)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][12]);//会员ID不得为空！

		$page = empty($page) ? 1 : $page;
		$pageSize = empty($pageSize) ? 20 : $pageSize;

		$archives = $dsql->SetQuery("SELECT `from`, `to`, `msg`, `date` FROM `#@__".$mod."_chat` WHERE `pid` != 0 AND ( (`from` = '$userid1' AND `to` = '$userid2') OR (`from` = '$userid2' AND `to` = '$userid1') ) ORDER BY `id` DESC");
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]); //暂无数据

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize * ($page - 1);
		$results = $dsql->dsqlOper($archives." LIMIT $atpage, $pageSize", "results");

		if($results){
			// $user1 = $userLogin->getMemberInfo($userid1);
			// $user2 = $userLogin->getMemberInfo($userid2);
			//
			// $user1 = is_array($user1) ? array(
			// 	'userid' => $user1['userid'],
			// 	'username' => $user1['username'],
			// 	'nickname' => $user1['nickname'],
			// 	'photo' => $user1['photo'],
			// 	'userType' => $user1['userType'],
			// 	'level' => $user1['level'],
			// 	'levelName' => $user1['levelName'],
			// 	'online' => $user1['online']
			// ) : array();
			//
			// $user2 = is_array($user2) ? array(
			// 	'userid' => $user2['userid'],
			// 	'username' => $user2['username'],
			// 	'nickname' => $user2['nickname'],
			// 	'photo' => $user2['photo'],
			// 	'userType' => $user2['userType'],
			// 	'level' => $user2['level'],
			// 	'levelName' => $user2['levelName'],
			// 	'online' => $user2['online']
			// ) : array();

			foreach($results as $key=>$row){

				//普通情况直接输出字段内容
				$msg = $row['msg'];

				//APP下输出
				if($param['app']){
					//文件内容
					$msg = array(
						'type' => 'text',
						'value' => $row['msg']
					);

					//附件内容
					if(strstr($row['msg'], 'src=')){
						preg_match("/<(.*?)src=\"(.+?)\".*?>/", $row['msg'], $src);

						if(strstr($row['msg'], '<img')){
							$type = 'image';
						}else if(strstr($row['msg'], '<audio')){
							$type = 'audio';
						}else if(strstr($row['msg'], '<video')){
							$type = 'video';
						}

						$msg = array(
							'type' => $type,
							'value' => $cfg_secureAccess . $cfg_basehost . $src[2]
						);
						if($type == 'audio'){
							preg_match("/<(.*?)data-duration=\"(.+?)\".*?>/", $row['msg'], $duration);
							$msg['duration'] = $duration[2];
						}
					}
				}

				$list[$key] = array(
					// 'user1' => $row['from'] == $userid1 ? $user1 : $user2,
					// 'user2' => $row['to'] == $userid1 ? $user1 : $user2,
					'from' => $row['from'],
					'to' => $row['to'],
					'msg' => $msg,
					'date' => $row['date']
				);
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);

	}


	/*
	 * 生成带参数的微信二维码
	 * keyword: 微信传图
	 */
	public function getWeixinQrCode(){
		global $userLogin;
		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => '登录超时！');

		//引入配置文件
		$wechatConfig = HUONIAOINC."/config/wechatConfig.inc.php";
		if(!file_exists($wechatConfig)) array("state" => 200, "info" => '请先设置微信开发者信息！');
		require($wechatConfig);

		include_once(HUONIAOROOT."/include/class/WechatJSSDK.class.php");
    $jssdk = new WechatJSSDK($cfg_wechatAppid, $cfg_wechatAppsecret);
    $token = $jssdk->getAccessToken();

		$rand = create_ordernum();

		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"expire_seconds": 28800, "action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "微信传图_'.$rand.'"}}}');
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $output = curl_exec($ch);
    curl_close($ch);

		if(empty($output)){
			return array("state" => 200, "info" => '请求失败，请稍候重试！');
		}

		$result = json_decode($output, true);
		if($result['errcode']){
			return array("state" => 200, "info" => json_encode($result));
		}else{
			$ticket = $result['ticket'];
			return array(
				'ticket' => $rand,
				'url' => 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket
			);
		}

	}


	/*
	 * 根据ticket返回已上传图片
	 * @param ticket string
	 * @return array
	 */
	public function getWeixinUpImg(){
		global $dsql;
		global $userLogin;
		$param = $this->param;
		$list  = array();

		if(!is_array($param)) return array("state" => 200, "info" => "格式错误！");

		$ticket = $param['ticket'];
		if(empty($ticket)) return array("state" => 200, "info" => '凭证错误！');

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => '登录超时！');

		$expTime = time() - 28800;  //8个小时以内有效
		$sql = $dsql->SetQuery("SELECT `fid` FROM `#@__site_wxupimg` WHERE `ticket` = '$ticket' AND `time` > $expTime AND `fid` != '' ORDER BY `id` ASC");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret && is_array($ret)){
			foreach ($ret as $key => $value) {
				array_push($list, array('fid' => $value['fid'], 'src' => getFilePath($value['fid'])));
			}
		}

		return $list;
	}


	/*
	 * 删除微信传图的指定图片
	 * @param ticket string
	 * @param fid string
	 * @return string
	 */
	public function delWeixinUpImg(){
		global $dsql;
		global $userLogin;
		$param = $this->param;
		$list  = array();

		if(!is_array($param)) return array("state" => 200, "info" => "格式错误！");

		$ticket = $param['ticket'];
		if(empty($ticket)) return array("state" => 200, "info" => '凭证错误！');

		$fid = $param['fid'];
		if(empty($fid)) return array("state" => 200, "info" => '图片ID错误！');

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => '登录超时！');

		$sql = $dsql->SetQuery("DELETE FROM `#@__site_wxupimg` WHERE `ticket` = '$ticket' AND `fid` = '$fid'");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == 'ok'){
			delPicFile($fid, "delWxUpImg", "siteConfig");
			return 'success';
		}else{
			return array("state" => 200, "info" => '登录超时！');
		}
	}


	/*
	 * 刷新置顶配置
	 * @return array
	 */
	public function getRefreshTopConfig(){
		global $userLogin;
		global $installModuleArr;

		$configFile = HUONIAOINC.'/config/refreshTop.inc.php';
		if(file_exists($configFile)){
			require($configFile);

			$arr = array();

			//二手
			if(in_array('info', $installModuleArr)){
				$arr['info'] = array(
					'refreshFreeTimes' => (int)$cfg_info_refreshFreeTimes,
					'refreshNormalPrice' => (float)$cfg_info_refreshNormalPrice,
					'titleBlodlDay' => (int)$cfg_info_titleBlodlDay,
					'titleBlodlPrice' => (float)$cfg_info_titleBlodlPrice,
					'titleRedDay' => (int)$cfg_info_titleRedDay,
					'titleRedPrice' => (float)$cfg_info_titleRedPrice,
					'refreshSmart' => $this->computeRefreshSmart((float)$cfg_info_refreshNormalPrice, $cfg_info_refreshSmart ? unserialize($cfg_info_refreshSmart) : array()),
					'topNormal' => $this->computeTopNormal($cfg_info_topNormal ? unserialize($cfg_info_topNormal) : array()),
					'topPlan' => $cfg_info_topPlan ? unserialize($cfg_info_topPlan) : array()
				);
			}

			//房产
			if(in_array('house', $installModuleArr)){
				$userInfo = $userLogin->getMemberInfo();
				$money = $userInfo['money'];
				$arr['house'] = array(
					'refreshFreeTimes' => (int)$cfg_house_refreshFreeTimes,
					'refreshNormalPrice' => (float)$cfg_house_refreshNormalPrice,
					'refreshSmart' => $this->computeRefreshSmart((float)$cfg_house_refreshNormalPrice, $cfg_house_refreshSmart ? unserialize($cfg_house_refreshSmart) : array()),
					'topNormal' => $this->computeTopNormal($cfg_house_topNormal ? unserialize($cfg_house_topNormal) : array()),
					'topPlan' => $cfg_house_topPlan ? unserialize($cfg_house_topPlan) : array(),
					'topDeposit' => $cfg_house_topPlan ? unserialize($cfg_house_topDeposit) : array()
				);
				$arr['house']['topDeposit']['availableCoins'] = $money / $arr['house']['topDeposit']['price'];
			}

			//招聘
			if(in_array('job', $installModuleArr)){
				$arr['job'] = array(
					'refreshFreeTimes' => (int)$cfg_job_refreshFreeTimes,
					'refreshNormalPrice' => (float)$cfg_job_refreshNormalPrice,
					'refreshSmart' => $this->computeRefreshSmart((float)$cfg_job_refreshNormalPrice, $cfg_job_refreshSmart ? unserialize($cfg_job_refreshSmart) : array()),
					'topNormal' => $this->computeTopNormal($cfg_job_topNormal ? unserialize($cfg_job_topNormal) : array()),
					'topPlan' => $cfg_job_topPlan ? unserialize($cfg_job_topPlan) : array()
				);
			}

			//汽车
			if(in_array('car', $installModuleArr)){
				$arr['car'] = array(
					'refreshFreeTimes' => (int)$cfg_car_refreshFreeTimes,
					'refreshNormalPrice' => (float)$cfg_car_refreshNormalPrice,
					'refreshSmart' => $this->computeRefreshSmart((float)$cfg_car_refreshNormalPrice, $cfg_car_refreshSmart ? unserialize($cfg_car_refreshSmart) : array()),
					'topNormal' => $this->computeTopNormal($cfg_car_topNormal ? unserialize($cfg_car_topNormal) : array()),
					'topPlan' => $cfg_car_topPlan ? unserialize($cfg_car_topPlan) : array()
				);
			}

			//教育
			if(in_array('education', $installModuleArr)){
				$arr['education'] = array(
					'refreshFreeTimes' => (int)$cfg_education_refreshFreeTimes,
					'refreshNormalPrice' => (float)$cfg_education_refreshNormalPrice,
					'refreshSmart' => $this->computeRefreshSmart((float)$cfg_education_refreshNormalPrice, $cfg_education_refreshSmart ? unserialize($cfg_education_refreshSmart) : array()),
					'topNormal' => $this->computeTopNormal($cfg_education_topNormal ? unserialize($cfg_education_topNormal) : array()),
					'topPlan' => $cfg_education_topPlan ? unserialize($cfg_education_topPlan) : array()
				);
			}

			return $arr;

		}else{
			return array("state" => 200, "info" => '请管理员到后台配置刷新置顶费用！');
		}

	}


	//计算智能刷新折扣、单价、优惠
	public function computeRefreshSmart($normal = 0, $arr = array()){
		if($normal && $arr){
			foreach ($arr as $key => $value) {
				$times = (int)$value['times'];
				$price = sprintf("%.2f", $value['price']);
				if($times && $price){
					$discount = sprintf("%.1f", ($price / ($normal * $times)) * 10);
					$unit = sprintf("%.2f", $price / $times);
					$offer = sprintf("%.2f", ($normal * $times) - $price);

					$arr[$key]['times'] = $times;
					$arr[$key]['day'] = (int)$value['day'];
					$arr[$key]['price'] = $price;
					$arr[$key]['discount'] = $discount < 10 && $discount > 0 ? $discount . self::$langData['siteConfig'][34][0] : self::$langData['siteConfig'][34][1];// 折 : 无折扣
					$arr[$key]['unit'] = $unit;
					$arr[$key]['offer'] = $offer;
				}
			}
		}
		return $arr;
	}


	//计算普通置顶折扣、优惠
	public function computeTopNormal($arr = array()){
		if($arr){
			$unitPrice = 0;
			foreach ($arr as $key => $value) {
				$day = (int)$value['day'];
				$price = sprintf("%.2f", $value['price']);
				if($day && $price){

					$arr[$key]['day'] = $day;
					$arr[$key]['price'] = $price;

					//取第一条单价
					if($key == 0){
						$unitPrice = sprintf("%.2f", $price / $day);
						$arr[$key]['discount'] = self::$langData['siteConfig'][34][1];//无折扣
						$arr[$key]['offer'] = sprintf("%.2f", 0);
					}else{
						$discount = sprintf("%.1f", ($price / ($unitPrice * $day)) * 10);
						$offer = sprintf("%.2f", ($unitPrice * $day) - $price);
						$arr[$key]['discount'] = $discount < 10 && $discount > 0 ? $discount . self::$langData['siteConfig'][34][0] : self::$langData['siteConfig'][34][1];// 折 : 无折扣
                        $arr[$key]['offer'] = $offer;
					}

				}
			}
		}
		return $arr;
	}


	//根据指定日期和时段，按照计划置顶规则，计算最终费用
	public function computeTopPlanAmount(){
	  $param = $this->param;
	  $plan = $param['plan'];
		array_unshift($plan, array_pop($plan));
	  $data = explode('|', $param['data']);

	  $beganDate = $data[0];
	  $endDate = $data[1];
	  $period = explode(',', $data[2]);

		$diffDays = (int)(diffBetweenTwoDays($beganDate, $endDate) + 1);
		$amount = 0;

		//时间范围内每天的费用
		for ($i = 0; $i < $diffDays; $i++) {
			$began = GetMkTime($beganDate);
			$day = AddDay($began, $i);
			$week = date("w", $day);

			if($period[$week]){
				$amount += $plan[$week][$period[$week]];
			}
		}
		return sprintf("%.2f", $amount);

	}


	/**
   * 信息刷新配置
	 * @param module string 模块标识
	 * @param act    string 模块二级标识   例：房产的二手房 租房等
	 * @return array  具体配置，包括免费次数、普通刷新价格、智能刷新配置、会员当前模块已免费刷新次数
   */
	public function refreshTopConfig(){
		global $dsql;
		global $userLogin;
		$param = $this->param;

		if(!is_array($param)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		$module = $param['module'];
		$act    = $param['act'];
		$userid = $param['userid'];

		if(empty($module)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]."，module");//参数错误
		// if(empty($act)) return array("state" => 200, "info" => '参数传递错误，act！');

		$uid = $userid ? $userid : $userLogin->getMemberID();
		if($uid == -1 || !$uid) return array("state" => 200, "info" => self::$langData['siteConfig'][21][121]);//登录超时，请刷新页面重试！

		$refreshTopConfig = $this->getRefreshTopConfig();
		if($refreshTopConfig){

			$moduleConfig = $refreshTopConfig[$module];
			if($moduleConfig){

				$count = 0;

				//计算会员已经免费刷新的次数
				if($module == 'info'){
					$sql = $dsql->SetQuery("SELECT SUM(`refreshFree`) total FROM `#@__".$module."list` WHERE `userid` = $uid AND `refreshFree` > 0");
					$ret = $dsql->dsqlOper($sql, "results");
					if(is_array($ret)){
						$count = $ret[0]['total'];
					}
				}
				if($module == 'house'){

					$where = ' AND `userid` = ' . $uid;
					$uid_ = $uid;

					$ischeck_zjuserMeal = false;
					$zjuserMeal = array(
						"iszjuser" => 0,
						"sys_openmeal" => 0,
						"meal" => array(),
					);

					//查询当前会员是否为中介
					$sql = $dsql->SetQuery("SELECT `id`, `meal` FROM `#@__house_zjuser` WHERE `userid` = $uid");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){

						$meal = $ret[0]['meal'] ? unserialize($ret[0]['meal']) : array();
						$house = new house();
						//置顶计算的是金币不需要计算次数，刷新需要计算已经刷新的次数
	                    $check = $house->checkZjuserMeal($meal, 'refresh');

	                	if($check['state'] != 101){
	                    	$ischeck_zjuserMeal = true;
	                    	$zjuserMeal['sys_openmeal'] = 1;
	                    }

	                    $count = 0;

	                    $zjuserMeal['iszjuser'] = 1;
                    	$zjuserMeal['meal'] = $meal;
                    	$zjuserMeal['meal_check'] = $check;


						$uid_ = $ret[0]['id'];
						$where = ' AND `usertype` = 1 AND `userid` = ' . $uid_;
					}

					if(!$ischeck_zjuserMeal){
						$sql = $dsql->SetQuery("SELECT SUM(`refreshFree`) total FROM `#@__".$module."_".$act."` WHERE 1 = 1".$where." AND `refreshFree` > 0");
						$ret = $dsql->dsqlOper($sql, "results");
						if(is_array($ret)){
							$count = $ret[0]['total'];
						}
					}

					$moduleConfig['zjuserMeal'] = $zjuserMeal;
				}
				if($module == 'job'){

					$uid_ = $uid;
					//查询当前会员是否为中介
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__".$module."_company` WHERE `userid` = $uid");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$uid_ = $ret[0]['id'];
					}

					$sql = $dsql->SetQuery("SELECT SUM(`refreshFree`) total FROM `#@__".$module."_post` WHERE `company` = $uid_ AND `refreshFree` > 0");
					$ret = $dsql->dsqlOper($sql, "results");
					if(is_array($ret)){
						$count = $ret[0]['total'];
					}
				}

				if($module == 'car'){
					$uid_ = $uid;
					//查询当前会员是否为顾问
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__car_store` WHERE `userid` = $uid");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$uid_ = $ret[0]['id'];
					}

					$sql = $dsql->SetQuery("SELECT SUM(`refreshFree`) total FROM `#@__car_list` WHERE `userid` = $uid_ AND `refreshFree` > 0");
					$ret = $dsql->dsqlOper($sql, "results");
					if(is_array($ret)){
						$count = $ret[0]['total'];
					}
				}

				if($module == 'education'){
					$uid_ = $uid;
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__education_store` WHERE `userid` = $uid");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$uid_ = $ret[0]['id'];
					}

					$sql = $dsql->SetQuery("SELECT SUM(`refreshFree`) total FROM `#@__education_courses` WHERE `userid` = $uid_ AND `refreshFree` > 0");
					$ret = $dsql->dsqlOper($sql, "results");
					if(is_array($ret)){
						$count = $ret[0]['total'];
					}
				}

				return array('config' => $moduleConfig, 'memberFreeCount' => (int)$count);

			}else{
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][14]);//"请求的模块无配置内容，请确认系统是否安装此模块！\r或者联系网站管理员确认后台是否已经配置刷新置顶功能！"
			}

		}else{
			return array("state" => 200, "info" => self::$langData['siteConfig'][33][15]);//'配置信息错误，请联系管理员检查后台配置！'
		}
	}


	/**
   * 信息免费刷新
	 * @param module string 模块标识
	 * @param act    string 模块二级标识   例：房产的二手房 租房等
	 * @param aid    int 信息ID
	 * @return array
   */
	public function freeRefresh(){
		global $dsql;
		global $userLogin;
		$param = $this->param;

		if(!is_array($param)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][0]);    //格式错误！

		$module = $param['module'];
		$act    = $param['act'];
		$aid    = $param['aid'];

		if(empty($module)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]."，module");//参数错误
		// if(empty($act)) return array("state" => 200, "info" => '参数传递错误，act！');
		if(empty($aid)) return array("state" => 200, "info" => self::$langData['siteConfig'][33][13]."，aid");//参数错误

		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => self::$langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$this->param = array(
			'module' => $module,
			'act' => $act
		);
		$refreshConfig = $this->refreshTopConfig();
		if($refreshConfig['state'] == 200){
			return $refreshConfig;
		}else{
			$rtConfig = $refreshConfig['config'];
			$refreshFreeTimes = $rtConfig['refreshFreeTimes'];  //可免费刷新次数
			$refreshNormalPrice = $rtConfig['refreshNormalPrice'];  //普通刷新价格
			$refreshSmart = $rtConfig['refreshSmart'];  //智能刷新配置
			$memberFreeCount = $refreshConfig['memberFreeCount'];
			$surplusFreeRefresh = (int)($refreshFreeTimes - $memberFreeCount);

			//如果还有免费次数
			if($surplusFreeRefresh > 0){

				$time = GetMkTime(time());

				//更新信息
				if($module == 'info'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$module."list` SET `refreshFree` = `refreshFree`+1, `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'house'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$module."_".$act."` SET `refreshFree` = `refreshFree`+1, `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'job'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$module."_post` SET `refreshFree` = `refreshFree`+1, `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'car'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$module."_list` SET `refreshFree` = `refreshFree`+1, `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'education'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$module."_courses` SET `refreshFree` = `refreshFree`+1, `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($ret == 'ok'){
                    // 更新缓存
                    if($module == "house"){
                        checkCache($module."_".$act."_list", $aid);
                        clearCache($module."_".$act."_detail", $aid);
                    }else{
                        checkCache($module."_list", $aid);
                        clearCache($module."_detail", $aid);
                    }
					return self::$langData['siteConfig'][32][33].$module.($act ? "_".$act : "")."_detail"; //刷新成功;
				}else{
					return array("state" => 200, "info" => self::$langData['siteConfig'][33][78]);//网络错误，刷新失败！
				}

			}else{
				return array("state" => 200, "info" => self::$langData['siteConfig'][33][16]);//您的免费刷新次数已用完，不再享有免费刷新。
			}
		}


	}


	/**
		* 信息刷新置顶
		* @return array
		*/
	public function refreshTop(){
		$param =  $this->param;
        $param_ = $param;

		$type       = $param['type'];
		$module     = $param['module'];
		$act        = $param['act'];
		$paytype    = $param['paytype'];
		$aid        = $param['aid'];
		$useBalance = (int)$param['useBalance'];
		$config     = $param['config'];
		$tourl      = $param['tourl'];
		$qr         = (int)$param['qr'];
		$ordernum   = $param['ordernum'];
		$final      = (int)$param['final']; // 最终支付
		$titleblod  = $param['titleblod'] ? $param['titleblod'] : ''; //加粗
		$titlered   = $param['titlered'] ? $param['titlered'] : ''; //加红


        $isMobile = isMobile();


		if(empty($module) || empty($aid)) die(self::$langData['siteConfig'][33][13]);//参数错误

		global $dsql;
		global $userLogin;
		global $langData;
		$userid = $userLogin->getMemberID();
		if($userid == -1) die($langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		//判断是否经纪人
		$check_zjuser = false;
        if($module == "house"){
            $sql = $dsql->SetQuery("SELECT `id`, `meal` FROM `#@__house_zjuser` WHERE `userid` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
            	$zjuid = $ret[0]['id'];
                $meal = $ret[0]['meal'] ? unserialize($ret[0]['meal']) : array();
                $house = new house();
                $mealCheck = $house->checkZjuserMeal($meal);
                if($mealCheck['state'] == 200){
                	return $mealCheck;

                }elseif($mealCheck['state'] == 100){
                	$check_zjuser = true;
                }
            }
        }

		//用户信息
		$userinfo = $userLogin->getMemberInfo();

		$this->param = $param;
		$refreshConfig = $this->refreshTopConfig();
		if($refreshConfig['state'] == 200){
			die($refreshConfig['info']);
		}else{
			$rtConfig = $refreshConfig['config'];
			$refreshFreeTimes = $rtConfig['refreshFreeTimes'];  //可免费刷新次数
			$refreshNormalPrice = $rtConfig['refreshNormalPrice'];  //普通刷新价格
			$refreshSmart = $rtConfig['refreshSmart'];  //智能刷新配置
			$topNormal = $rtConfig['topNormal'];  //普通置顶配置
			$topPlan = $rtConfig['topPlan'];  //计划置顶配置
			$topExchange = $rtConfig['topDeposit'];

			$titleBlodlDay   = $rtConfig['titleBlodlDay'];  //标题加粗时长
			$titleBlodlPrice = $rtConfig['titleBlodlPrice'];  //标题加粗价格
			$titleRedDay     = $rtConfig['titleRedDay'];  //标题加红时长
			$titleRedPrice   = $rtConfig['titleRedPrice'];  //标题加红价格

			$memberFreeCount = $refreshConfig['memberFreeCount'];
			$surplusFreeRefresh = (int)($refreshFreeTimes - $memberFreeCount);
		}

		$tit = '';

		$need_times = 0;	// 经纪人操作 需要消耗的次数/天数

		//普通刷新
		if($type == 'refresh'){
			$amount = $refreshNormalPrice;
			$tit = self::$langData['siteConfig'][32][29];//普通刷新

			$need_times = 1;

		//智能刷新
		}elseif($type == 'smartRefresh'){
			$config = (int)$config;
			$amount = $refreshSmart[$config]['price'];
			$tit = self::$langData['siteConfig'][32][28];//智能刷新

			$need_times = $refreshSmart[$config]['times'];

		//普通置顶
		}elseif($type == 'topping'){
			$config = (int)$config;
			$amount = $topNormal[$config]['price'];
			$tit = self::$langData['siteConfig'][32][38];//立即置顶

			$need_times = $topNormal[$config]['times'];

		//计划置顶
		}elseif($type == 'toppingPlan'){
			$this->param = array('plan' => $topPlan, 'data' => $config);
			$amount = $this->computeTopPlanAmount();
			$tit = self::$langData['siteConfig'][32][39];//计划置顶

		    $data = explode('|', $config);

		    $beganDate = $data[0];
		    $endDate = $data[1];
		    $period = explode(',', $data[2]);

		    $diffDays = (int)(diffBetweenTwoDays($beganDate, $endDate) + 1);
			$need_times = $diffDays;

		}

		//标题加粗加红
		if(!empty($type) && $type == 'boldred'){
			if(!empty($titleblod) && $titleblod == 'titleblod'){
				$amount = $titleBlodlPrice;
				$tit = $langData['info'][1][56];//标题加粗
			}

			if(!empty($titlered) && $titlered == 'titlered'){
				$amount += $titleRedPrice;
				$tit .= $langData['info'][1][57];//标题加红
			}

		}elseif(!empty($type) && $type != 'boldred'){
			if(!empty($titleblod) && $titleblod == 'titleblod'){
				$amount += $titleBlodlPrice;
				$tit = $langData['info'][1][56];//标题加粗
			}

			if(!empty($titlered) && $titlered == 'titlered'){
				$amount += $titleRedPrice;
				$tit .= $langData['info'][1][57];//标题加红
			}
		}

		$balance = 0;	// 使用余额
		$payAmount = 0; // 在线支付金额;

        if($isMobile && empty($final)){
            $useBalance = 0;
        }
		// 使用余额
		if($useBalance){
			//金币转换为金额
			if(($amount * $topExchange['price']) <= $userinfo['money']){
				$payAmount = 0;
			}else{
				$payAmount = ($amount * $topExchange['price']) - $userinfo['money'];
			}

			$balance = ($amount * $topExchange['price']) - $payAmount;

		}else{
			$payAmount = ($amount * $topExchange['price']);
		}

		$param = array(
			"userid" => $userid,
			"amount" => $amount,
			"balance" => $balance,
			"online" => $payAmount,
			"type" => "refreshTop",
			"module" => $module,
			"act" => $act,
			"class" => $type,
			"aid" => $aid,
			"config" => $config,
			"titleblod" => $titleblod,
			"titlered" => $titlered
		);



		if($check_zjuser){

			if(stripos($type, "refresh") !== false){
				$has_times = $meal['refresh'];
				$tit_ = self::$langData['siteConfig'][34][3];//刷新次数
				$type_ = "refresh";
			}elseif(stripos($type, "topping") !== false){
				$has_times = $meal['settop'];
				$type_ = "settop";
				$tit_ = self::$langData['siteConfig'][34][4];//置顶天数
			}
			/*if($has_times < $need_times){
				return array("state" => 200, "info" => self::$langData['siteConfig'][20][603].$tit_.self::$langData['siteConfig'][34][2]);//"剩余".$tit_."不足"
			}*/
			
			if($has_times < $amount && (($amount - $has_times) * $topExchange['price']) > $userinfo['money']){
				return array("state"=>200, "info"=>"可用余额不足！");
			}

		}

		// 非经纪人或者后台没有配置经纪人套餐时 实际支付金额大于0
		if(!$check_zjuser && ($payAmount || $qr) ){

			$ordernum = $ordernum ? $ordernum : create_ordernum();
			$param['type'] = 'refreshTop';

            if($isMobile && empty($final)){
                $param_['ordernum'] = $ordernum;
                $param_['ordertype'] = 'refreshTop';
                $param = array(
                    "service" => "member",
                    "type" => "user",
                    "template" => "pay",
                    "param" => http_build_query($param_)
                );
                header("location:".getUrlPath($param));
                die;
            }

			return createPayForm("member", $ordernum, $payAmount, $paytype, $tit, $param);  //会员发布信息

		}else{
			if($check_zjuser){
				$param['amount'] = 0;
				$param['balance'] = 0;
				$param['online'] = 0;
				$param['check_zjuser'] = 1;
			}
			$this->refreshTopSuccess($param);

			// 更新套餐余量
			if($check_zjuser){
				if(strstr($type, "topping")){
					$dopost = $type_ . ":" . $amount;
				}else{
					$dopost = $type_ . ":" . $need_times;
				}
				$house = new house();
				$house->updateZjuserMeal($zjuid, $dopost, $meal, $aid, $act, $type, $topExchange['price']);

				return self::$langData['siteConfig'][20][244];//"操作成功";
			}

			if($tourl){
				header("location:".$tourl);
			}else{
				return self::$langData['siteConfig'][16][55];  //支付成功
			}

		}


	}

	/**
		* 刷新置顶支付成功
		* 安全考虑，接口调用传支付的ordernum，内部使用直接传array
		*/
	public function refreshTopSuccess($param = array()){
		global $dsql;
		global $langData;

		if(empty($param)){
			$param = $this->param;
		}

		if(!is_array($param)){
			$ordernum = $this->param;
			$archives = $dsql->SetQuery("SELECT `body` FROM `#@__pay_log` WHERE `ordernum` = '$ordernum' AND `state` = 1");
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$param = unserialize($results[0]['body']);
			}else{
				die(self::$langData['siteConfig'][33][17]);//支付订单不存在！
			}
		}

		$userid      = $param['userid'];
		$module      = $param['module'];
		$act         = $param['act'];
		$class       = $param['class'];
		$aid         = $param['aid'];
		$amount      = $param['amount'];
		$balance     = $param['balance'];
		$online      = $param['online'];
		$type        = $param['type'];
		$config      = $param['config'];
		$titleblod   = $param['titleblod'];
		$titlered    = $param['titlered'];

		$check_zjuser = $param['check_zjuser'];

		$this->param = $param;
		$refreshConfig = $this->refreshTopConfig();
		if($refreshConfig['state'] == 200){
			die($refreshConfig['info']);
		}else{
			$rtConfig = $refreshConfig['config'];
			$refreshFreeTimes = $rtConfig['refreshFreeTimes'];  //可免费刷新次数
			$refreshNormalPrice = $rtConfig['refreshNormalPrice'];  //普通刷新价格
			$refreshSmart = $rtConfig['refreshSmart'];  //智能刷新配置
			$topNormal = $rtConfig['topNormal'];  //智能刷新配置
			$topPlan = $rtConfig['topPlan'];  //智能刷新配置

			$titleBlodlDay   = $rtConfig['titleBlodlDay'];  //标题加粗时长
			$titleBlodlPrice = $rtConfig['titleBlodlPrice'];  //标题加粗价格
			$titleRedDay     = $rtConfig['titleRedDay'];  //标题加红时长
			$titleRedPrice   = $rtConfig['titleRedPrice'];  //标题加红价格

			$memberFreeCount = $refreshConfig['memberFreeCount'];
			$surplusFreeRefresh = (int)($refreshFreeTimes - $memberFreeCount);
		}

		$time = GetMkTime(time());

		$tab = $module . 'list';
		if($module == 'house'){
			$tab = $module . '_' . $act;
		}elseif($module == 'job'){
			$tab = $module . '_post';
		}elseif($module == 'car'){
			$tab = $module . '_list';
		}elseif($module == 'education'){
			$tab = $module . '_courses';
		}

		$archive = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__".$tab."` WHERE `id` = $aid");
		$results = $dsql->dsqlOper($archive, "results");
		if($results){

			//获取智能刷新的配置信息
			if($class == 'smartRefresh'){
				$config = (int)$config;
				$smartData = $refreshSmart[$config];
				if($smartData){
					$sr_day = $smartData['day'];
					$sr_discount = $smartData['discount'];
					$sr_offer = $smartData['offer'];
					$sr_price = $smartData['price'];
					$sr_times = $smartData['times'];
					$sr_unit = $smartData['unit'];
				}
			}

			//普通置顶信息
			if($class == 'topping'){
				$config = (int)$config;
				$topData = $topNormal[$config];
				if($topData){
					$tp_day = $topData['day'];
					$tp_price = $topData['price'];
					$tp_discount = $topData['discount'];
					$tp_offer = $topData['offer'];
				}
			}

			//计划置顶信息
			if($class == 'toppingPlan'){
				$configArr = explode('|', $config);
				$tp_beganDate = $configArr[0];
				$tp_endDate = $configArr[1];
				$period = explode(',', $configArr[2]);

				$diffDays = (int)(diffBetweenTwoDays($tp_beganDate, $tp_endDate) + 1);
				$tp_planArr = array();
				$tp_week = array();

				$weekArr = self::$langData['siteConfig'][34][5];

				//时间范围内每天的明细
				for ($i = 0; $i < $diffDays; $i++) {
					$began = GetMkTime($tp_beganDate);
					$day = AddDay($began, $i);
					$week = date("w", $day);

					if($period[$week]){
						array_push($tp_planArr, date('Y-m-d', $day) . " " . $weekArr[$week] . " " . ($period[$week] == 'all' ? '全天' : '早8点-晚8点'));
						array_push($tp_week, array(
							'week' => $week,
							'type' => $period[$week]
						));
					}
				}

				$this->param = array('plan' => $topPlan, 'data' => $config);
				$tp_amount = $this->computeTopPlanAmount();
			}

			$tit = '';
			if($class == 'refresh'){
				$tit = self::$langData['siteConfig'][16][70];//刷新
			}elseif($class == 'smartRefresh'){
				$tit = self::$langData['siteConfig'][32][28] . $sr_day . self::$langData['siteConfig'][13][6] . $sr_times . '次';//'智能刷新' . $sr_day . '天' . $sr_times . '次';
			}elseif($class == 'topping'){
				$tit = self::$langData['siteConfig'][19][762] . $tp_day . self::$langData['siteConfig'][13][6];//'置顶' . $tp_day . '天';
			}elseif($class == 'toppingPlan'){
				$tit = self::$langData['siteConfig'][32][39] . '：' . join('、', $tp_planArr);//计划置顶：
			}

			//标题加粗加红
			$field = '';
			if($titleblod == 'titleblod'){
				$tit = $tit . '-' . $langData['info'][1][56];
				$titleBlodDay = AddDay($time, $titleBlodlDay);
				$field = " ,`titleBlod` = 1, `titleBlodDay` = '$titleBlodDay'";
			}
			if($titlered == 'titlered'){
				$tit = $tit . '-' . $langData['info'][1][57];
				$titleRedDay = AddDay($time, $titleRedDay);
				$field .= " ,`titleRed` = 1, `titleRedDay` = '$titleRedDay'";
			}

			$tname = self::$langData['siteConfig'][19][216];// '信息';
			if($module == 'info'){
				$tname = self::$langData['siteConfig'][16][18];//二手信息
			}elseif($module == 'house'){
				if($act == 'sale'){
					$tname = self::$langData['siteConfig'][19][218];//二手房
				}elseif($act == 'zu'){
					$tname = self::$langData['siteConfig'][19][219];//租房
				}elseif($act == 'xzl'){
					$tname = self::$langData['siteConfig'][19][220];//写字楼
				}elseif($act == 'sp'){
					$tname = self::$langData['siteConfig'][19][221];//商铺
				}elseif($act == 'cf'){
					$tname = self::$langData['siteConfig'][19][761];//厂房
				}
			}elseif($module == 'job'){
				$tname = self::$langData['siteConfig'][34][6];//招聘职位
			}elseif($module == 'car'){
				$tname = self::$langData['siteConfig'][34][7];//汽车门户
			}elseif($module == 'education'){
				$tname = self::$langData['education'][7][18];//教育课程
			}

			if($check_zjuser){
				$amount = 0;
				$balance = 0;
				$tp_price = 0;
				$tp_amount = 0;
			}

            //扣除会员余额
            if($balance){
                $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$balance' WHERE `id` = '$userid'");
                $dsql->dsqlOper($archives, "update");
            }

			//保存操作日志
			$info = $tname.$tit."-".$results[0]['title'];
			if($check_zjuser){
				$info = self::$langData['siteConfig'][34][8]."：".$info;//使用经纪人套餐
			}
			$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$amount', '$info', '$time')");
			$dsql->dsqlOper($archives, "update");

			//刷新业务
			if($class == 'refresh'){

				//更新信息
				if($module == 'info'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'house'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'job'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'car'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'education'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

			//智能刷新
			//先进行一次刷新，然后更新信息，并查询需要更新的总次数，刷新时长，刷新价格，开始刷新时间，下次刷新时间，刷新剩余次数
			}elseif($class == 'smartRefresh'){

				//下次刷新时间
				$nextRefreshTime = $time + (int)(24/($sr_times/$sr_day)) * 3600;
				$refreshSurplus = $sr_times - 1;

				//更新信息
				if($module == 'info'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `refreshSmart` = 1, `refreshCount` = '$sr_times', `refreshTimes` = '$sr_day', `refreshPrice` = '$sr_times', `refreshBegan` = '$time', `refreshNext` = '$nextRefreshTime', `refreshSurplus` = '$refreshSurplus', `pubdate` = '$time' $field WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'house'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `refreshSmart` = 1, `refreshCount` = '$sr_times', `refreshTimes` = '$sr_day', `refreshPrice` = '$sr_times', `refreshBegan` = '$time', `refreshNext` = '$nextRefreshTime', `refreshSurplus` = '$refreshSurplus', `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'job'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `refreshSmart` = 1, `refreshCount` = '$sr_times', `refreshTimes` = '$sr_day', `refreshPrice` = '$sr_times', `refreshBegan` = '$time', `refreshNext` = '$nextRefreshTime', `refreshSurplus` = '$refreshSurplus', `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'car'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `refreshSmart` = 1, `refreshCount` = '$sr_times', `refreshTimes` = '$sr_day', `refreshPrice` = '$sr_times', `refreshBegan` = '$time', `refreshNext` = '$nextRefreshTime', `refreshSurplus` = '$refreshSurplus', `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'education'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `refreshSmart` = 1, `refreshCount` = '$sr_times', `refreshTimes` = '$sr_day', `refreshPrice` = '$sr_times', `refreshBegan` = '$time', `refreshNext` = '$nextRefreshTime', `refreshSurplus` = '$refreshSurplus', `pubdate` = '$time' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

			//普通置顶
			//这里使用了最开始的竞价字段
			}elseif($class == 'topping'){

				$bid_end = AddDay($time, $tp_day);

				//更新信息
				if($module == 'info'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `isbid` = 1, `bid_type` = 'normal', `bid_price` = '$tp_price', `bid_start` = '$time', `bid_end` = '$bid_end' $field WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'house'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `isbid` = 1, `bid_type` = 'normal', `bid_price` = '$tp_price', `bid_start` = '$time', `bid_end` = '$bid_end' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'job'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `isbid` = 1, `bid_type` = 'normal', `bid_price` = '$tp_price', `bid_start` = '$time', `bid_end` = '$bid_end' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'car'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `isbid` = 1, `bid_type` = 'normal', `bid_price` = '$tp_price', `bid_start` = '$time', `bid_end` = '$bid_end' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'education'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `isbid` = 1, `bid_type` = 'normal', `bid_price` = '$tp_price', `bid_start` = '$time', `bid_end` = '$bid_end' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

			//计划置顶
			}elseif($class == 'toppingPlan'){

				$tp_beganDate = GetMkTime($tp_beganDate);
				$tp_endDate = GetMkTime($tp_endDate);

				$tp_weekSet = array();
				foreach ($tp_week as $key => $value) {
					array_push($tp_weekSet, "`bid_week".$value["week"]."` = '".$value['type']."'");
				}
				$tp_weekUpdate = ', ' . join(', ', $tp_weekSet);

				//更新信息
				if($module == 'info'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `isbid` = 1, `bid_type` = 'plan', `bid_price` = '$tp_amount', `bid_start` = '$tp_beganDate', `bid_end` = '$tp_endDate'".$tp_weekUpdate." WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'house'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `isbid` = 1, `bid_type` = 'plan', `bid_price` = '$tp_amount', `bid_start` = '$tp_beganDate', `bid_end` = '$tp_endDate'".$tp_weekUpdate." WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'job'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `isbid` = 1, `bid_type` = 'plan', `bid_price` = '$tp_amount', `bid_start` = '$tp_beganDate', `bid_end` = '$tp_endDate'".$tp_weekUpdate." WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'car'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `isbid` = 1, `bid_type` = 'plan', `bid_price` = '$tp_amount', `bid_start` = '$tp_beganDate', `bid_end` = '$tp_endDate'".$tp_weekUpdate." WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

				if($module == 'education'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `isbid` = 1, `bid_type` = 'plan', `bid_price` = '$tp_amount', `bid_start` = '$tp_beganDate', `bid_end` = '$tp_endDate'".$tp_weekUpdate." WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}

			//标题加粗加红
			}elseif($class == 'boldred'){
				//更新信息
				$titleBlod = 0;
				$titleBlodDay = 0;
				if($titleblod == 'titleblod'){
					$titleBlodDay = AddDay($time, $titleBlodlDay);
					$titleBlod = 1;
				}
				$titleRed = 0;
				$titleRedDay = 0;
				if($titlered == 'titlered'){
					$titleRedDay = AddDay($time, $titleRedDay);
					$titleRed    = 1;
				}
				if($module == 'info'){
					$sql = $dsql->SetQuery("UPDATE `#@__".$tab."` SET `titleBlod` = '$titleBlod', `titleBlodDay` = '$titleBlodDay', `titleRed` = '$titleRed', `titleRedDay` = '$titleRedDay' WHERE `id` = $aid");
					$ret = $dsql->dsqlOper($sql, "update");
				}
			}

            // 清除缓存
            clearCache($module."_list", "key");
            clearCache($module."_detail", $aid);
		}

	}

	/**
	 * 获取商圈
	 */
	public function getCircle(){
		global $dsql;

		$param = $this->param;
		$cid = (int)$param['cid'];
		$qid = (int)$param['qid'];

		$where = "";
		if($cid){
			$where .= " AND `cid` = $cid";
		}
		if($qid){
			$where .= " AND `qid` = $qid";
		}

		$sql = $dsql->SetQuery("SELECT * FROM `#@__site_city_circle` WHERE 1 = 1".$where);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			return $ret;
		}
	}

	// 114便民查询地图周边POI
	public function get114ConveniencePoiList(){
		global $cfg_map;
		global $site_map_key;
		global $cfg_map_baidu_server;
		global $cfg_map_amap_server;
		global $cfg_map_google;

		$lat = $this->param['lat'];
		$lng = $this->param['lng'];
		$directory = $this->param['directory'];
		$pageSize = (int)$this->param['pageSize'];
		$page = (int)$this->param['page'];
		$radius = (int)$this->param['radius'];
		$pagetoken = $this->param['pagetoken'];

		$pageSize = $pageSize ? $pageSize : 10;
		$radius = $radius ? $radius : 5000;

		if(empty($lat) || empty($lng) || empty($directory)){
			return array("state" => 200, "info" => '参数错误！');
		}

		//百度地图
		if($cfg_map == 2) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'http://api.map.baidu.com/place/v2/search?query=' . $directory . '&scope=2&location=' . $lat . ',' . $lng . '&radius=' . $radius . '&page_num=' . $page . '&page_size=' . $pageSize . '&output=json&ak=' . $cfg_map_baidu_server);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            $data = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if ($data['status'] == 0 && $data['message'] == 'ok') {
                $total = $data['total'];
                $totalPage = ceil($total / $pageSize);
                $results = $data['results'];

                $list = array();
                foreach ($results as $key => $value) {
                    array_push($list, array(
                        'name' => $value['name'],
                        'lat' => $value['location']['lat'],
                        'lng' => $value['location']['lng'],
                        'address' => $value['address'],
                        'tel' => $value['telephone'],
                        'url' => getUrlPath(array(
                            'service' => 'siteConfig',
                            'template' => '114_detail',
                            'param' => 'name=' . urlencode($value['name']) . '&lat=' . $value['location']['lat'] . '&lng=' . $value['location']['lng'] . '&address=' . urlencode($value['address']) . '&tel=' . $value['telephone']
                        ))
                    ));
                }

                return array(
                    'totalCount' => $total,
                    'totalPage' => $totalPage,
                    'list' => $list
                );
            } else {
                return array("state" => 200, "info" => $data['message']);
            }

        //高德
        }elseif($cfg_map == 4){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://restapi.amap.com/v3/place/around?key='. $cfg_map_amap_server .'&location=' . $lat . ',' . $lng . '&keywords=' . $directory . '&types=&radius=' . $radius . '&offset=' . $pageSize . '&page=' . $page . '&extensions=all');
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            $data = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if ($data['status'] == 1 && $data['info'] == 'OK') {
                $total = $data['count'];
                $totalPage = ceil($total / $pageSize);
                $results = $data['pois'];

                $list = array();
                foreach ($results as $key => $value) {

                    $location = explode(',', $value['location']);

                    if($value['address']) {
                        array_push($list, array(
                            'name' => $value['name'],
                            'lat' => $location[1],
                            'lng' => $location[0],
                            'address' => $value['address'],
                            'tel' => $value['tel'] ? $value['tel'] : '',
                            'url' => getUrlPath(array(
                                'service' => 'siteConfig',
                                'template' => '114_detail',
                                'param' => 'name=' . urlencode($value['name']) . '&lat=' . $location[1] . '&lng=' . $location[0] . '&address=' . urlencode($value['address']) . '&tel=' . ($value['tel'] ? $value['tel'] : '')
                            ))
                        ));
                    }
                }

                return array(
                    'totalCount' => $total,
                    'totalPage' => $totalPage,
                    'list' => $list
                );
            } else {
                return array("state" => 200, "info" => $data['message']);
			}

		//谷歌
		}elseif($cfg_map == 1){

			//https://developers.google.com/places/web-service/search?refresh=1
            $radius = $radius >= 50000 ? 50000 : $radius;//以米做单位的 最多查询20个

          	//查找场所请求 @desc:查找位置请求接受文本输入，并返回一个位置 免费
          	//$url = 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input='. $directory . '&inputtype=textquery&language=ZH-CN&fields=icon,name,photos&locationbias=' . 'circle:' . $radius . '@' . $lat . ',' . $lng . '&key=' . $cfg_map_google;

          	//附近搜索 会产生费用
          	//$url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=' . $lat . ',' . $lng . '&language=ZH-CN&radius=' . $radius . '&keyword=' . $directory . '&key=' . $cfg_map_google;

          	//文本搜索 会产生费用 https://maps.googleapis.com/maps/api/place/textsearch/json?query=123+main+street&location=42.3675294,-71.186966&radius=10000&key=YOUR_API_KEY
          	if(!empty($pagetoken) && $page>=1){
            	$url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?pagetoken=' . $pagetoken . '&key=' . $cfg_map_google;
            }else{
            	$url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . $directory . '&language=ZH-CN&location=' . $lat . ',' . $lng . '&radius=' . $radius . '&key=' . $cfg_map_google;
            }

          	//https://developers.google.com/places/web-service/search?refresh=1#PlaceSearchPaging

          	//查看其他结果
          	//https://maps.googleapis.com/maps/api/place/nearbysearch/json?pagetoken=$pagetoken&key=YOUR_API_KEY

          	$curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 20);
			$data = json_decode(curl_exec($curl), true);
			//echo 'Curl error: ' . curl_error($curl);exit;
			curl_close($curl);
			if ($data['status'] == 'OK') {
                $pagetoken = $data['next_page_token'];
				$results   = $data['results'];

                $list = array();
                foreach ($results as $key => $value) {
                	if($value['formatted_address']) {
                        array_push($list, array(
                            'name' => $value['name'],
                            'lat' => $value['geometry']['location']['lat'],
                            'lng' => $value['geometry']['location']['lng'],
                            'address' => $value['formatted_address'],
                            'tel' => $value['tel'] ? $value['tel'] : '',
                            'url' => getUrlPath(array(
                                'service' => 'siteConfig',
                                'template' => '114_detail',
                                'param' => 'name=' . urlencode($value['name']) . '&lat=' .$value['geometry']['location']['lat'] . '&lng=' . $value['geometry']['location']['lng'] . '&address=' . urlencode($value['formatted_address']) . '&tel=' . ($value['tel'] ? $value['tel'] : '')
                            ))
                        ));
                    }
                }

              	return array(
                    'totalCount' => 3,
                    'totalPage' => 2,
                    'pagetoken' => $pagetoken,
                    'list' => $list
                );
			}else{
            	return array("state" => 200, "info" => $data['status']);
            }
		}


	}

    /**
     * 获取自定义小程序二维码的跳转链接
     */
    public function getWxMiniProgramScene(){
        global $dsql;

        $param = $this->param;
        $scene = (int)$param['scene'];

        if($scene){
            $sql = $dsql->SetQuery("SELECT `url` FROM `#@__site_wxmini_scene` WHERE `id` = " . $scene);
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $url = $ret[0]['url'];

                //更新访问次数
                $sql = $dsql->SetQuery("UPDATE `#@__site_wxmini_scene` SET `count` = `count` + 1 WHERE `id` = " . $scene);
                $dsql->dsqlOper($sql, "update");

                return $url;
            }else{
                return array("state" => 200, "info" => "二维码错误，即将回到首页...");
            }
        }else{
            return array("state" => 200, "info" => "二维码错误，即将回到首页...");
        }
    }

    /**
     * 生成自定义小程序二维码
     */
    public function createWxMiniProgramScene(){
        global $dsql;

        $param = $this->param;
        $url = (int)$param['url'];

        if(empty($url)) return array("state" => 200, "info" => '链接不能为空！');

        //往数据库添加数据
        $sql = $dsql->SetQuery("SELECT `id`, `fid` FROM `#@__site_wxmini_scene` WHERE `url` = '$url'");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            return getFilePath($ret[0]['fid']);
        }

        return createWxMiniProgramScene($url, '..', true);
    }


    /**
     * 支付前检查 验证支付密码、用户积分和余额是否充足，不验证总额
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

        return "ok";

        // if($useTotal > $totalPrice) return array("state" => 200, "info" => str_replace('1', join($langData['siteConfig'][13][46], $tit), $langData['siteConfig'][21][104]));  //和  您使用的1超出订单总费用，请重新输入！

        // return sprintf('%.2f', $totalPrice);

    }

    /**
     * 支付
     */
    public function pay(){
        global $dsql;
        global $userLogin;

        $param    = $this->param;

        $ordertype  = $param['ordertype'];   //类型

        if($ordertype && method_exists($this, $ordertype)){
            $this->$ordertype();
        }else{
            die("操作错误！");
        }

    }

    /**
     * 移动端便捷导航规则
     */
    public function getFastNavigationRule(){
        global $dsql;

        $fabu = $cart = array();
        $sql = $dsql->SetQuery("SELECT `name`, `title`, `subject` FROM `#@__site_module` WHERE `state` = 0 AND `type` = 0 AND `name` != ''");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            foreach ($ret as $key => $val){

                //发布相关
                if($val['name'] == 'article' || $val['name'] == 'info' || $val['name'] == 'house' || $val['name'] == 'tieba' || $val['name'] == 'huodong' || $val['name'] == 'live'){

                    if($val['name'] == 'info') {
                        $link = getUrlPath(array(
                            'service' => 'member',
                            'type' => 'user',
                            'template' => 'info'
                        )) . '#Stype';
                    }elseif($val['name'] == 'house'){
                        $link = getUrlPath(array(
                            'service' => 'member',
                            'type' => 'user',
                            'template' => 'fabu_house'
                        ));
                    }elseif($val['name'] == 'tieba'){
                        $link = getUrlPath(array(
                            'service' => $val['name'],
                            'template' => 'fabu'
                        ));
                    }elseif($val['name'] == 'huodong'){
                        $link = getUrlPath(array(
                            'service' => $val['name'],
                            'template' => 'fabu'
                        ));
                    }else {
                        $link = getUrlPath(array(
                            'service' => 'member',
                            'type' => 'user',
                            'template' => 'fabu',
                            'action' => $val['name']
                        ));
                    }

                    array_push($fabu, array(
                        'title' => $val['subject'] ? $val['subject'] : $val['title'],
                        'domain' => getUrlPath(array(
                            'service' => $val['name']
                        )),
                        'link' => $link
                    ));
                }

                //购物
                if($val['name'] == 'shop'){
                    array_push($cart, array(
                        'title' => $val['subject'] ? $val['subject'] : $val['title'],
                        'domain' => getUrlPath(array(
                            'service' => $val['name']
                        )),
                        'link' => getUrlPath(array(
                            'service' => $val['name'],
                            'template' => 'cart'
                        ))
                    ));
                }
            }
        }

        global $cfg_secureAccess;
        global $cfg_basehost;
        global $cfg_wechatQr;
        global $cfg_wechatName;
        global $cfg_miniProgramQr;
        global $cfg_miniProgramName;

        return array(
            'basehost' => $cfg_secureAccess . $cfg_basehost,
            'weixin' => array(
                'qr' => $cfg_wechatQr ? getFilePath($cfg_wechatQr) : '',
                'name' => $cfg_wechatName,
                'mQr' => $cfg_miniProgramQr ? getFilePath($cfg_miniProgramQr) : '',
                'mName' => $cfg_miniProgramName
            ),
            'member' => array(
                'busiDomain' => getUrlPath(array(
                    'service' => 'member'
                )),
                'userDomain' => getUrlPath(array(
                    'service' => 'member',
                    'type' => 'user'
                ))
            ),
            'fabu' => $fabu,
            'cart' => $cart
        );

    }

    /**
     * 移动端获取头部模块链接
     */
    public function touchAllBlock(){
        global $cfg_basehost;
        global $cfg_secureAccess;
        global $cfg_staticVersion;
        global $langData;
        global $installModuleArr;

        $url = $cfg_secureAccess.$cfg_basehost;

        $param = array("service" => "member", "type" => "user");
        $memberUrl = getUrlPath($param);
        $param = array("service" => "shop", "template" => "cart");
        $cartUrl = getUrlPath($param);

        $menu = array();

        if(in_array("shop", $installModuleArr)){
            // 购物车
            $menu[] = array(
                'name' => $langData['siteConfig'][22][12],
                'icon' => $url.'/static/images/admin/nav/shop_car.png?v='.$cfg_staticVersion,
                'url' => $cartUrl,
                'color' => '',
	            'code' => 'cart',
                'bold' => 0
            );
        }

        $this->param = array("type" => 1);
        $module = $this->siteModule();

        foreach ($module as $key => $value) {
            if($value['code'] == 'special' || $value['code'] == 'website') continue;

            $menu[] = array(
                'name' => $value['name'],
                'icon' => $value['icon'],
                'url' => $value['url'],
                'color' => $value['color'],
	            'code' => $value['code'],
                'bold' => $value['bold'],
				'title' => $value['title'],
				'title' => $value['title'],
				'description' => $value['description'],
				'logo' => $value['logo'],
            );
        }

        return $menu;
	}






	/**
     * IM相关
     */


 	/**
      * 搜索用户
      */
 	public function searchMember(){
 		global $dsql;

 		$keywords = $this->param['keywords'];

		$sql = $dsql->SetQuery("SELECT `id`, `nickname`, `photo` FROM `#@__member` WHERE (`id` = '$keywords' OR (`phone` = '$keywords' AND `phoneCheck` = 1)) AND `mtype` > 0");
 	    $ret = $dsql->dsqlOper($sql, "results");

		$list = array();
 	    if($ret){
 	        foreach($ret as $key => $val){
				//用户信息
				$configHandels = new handlers('member', "detail");
				$detail = $configHandels->getHandle(array("id" => $val['id'], "friend" => 1));
				if($detail['state'] == 100){
	 	            array_push($list, $detail['info']);
				}
 	        }
 	    }
 		return $list;
 	}


   /**
	 * 添加好友
	 */
   public function applyFriend(){
	   global $dsql;
	   global $userLogin;
	   global $langData;

		//用户ID
		$userid = $this->param['userid'] ? $this->param['userid'] : $userLogin->getMemberID();

		if($userid < 1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
		}

	   $tid = $this->param['tid'];
	   $note = $this->param['note'];
	   $time = time();

	   if($userid == $tid){
		   return array("state" => 200, "info" => "不可以添加自己为好友！");   //登录超时，请重新登录！
	   }

	   //发送申请
	   $fromToken = '';
	   $this->param = array('userid' => $userid);
	   $token = $this->getImToken();
	   $fromToken = $token['token'];

	   $toToken = '';
	   $this->param = array('userid' => $tid);
	   $token = $this->getImToken();
	   $toToken = $token['token'];
	   $tname = $token['name'];

	   //验证是否已经是好友
	   $sql = $dsql->SetQuery("SELECT `id`, `fid`, `tid`, `state`, `delfrom`, `delto` FROM `#@__member_friend` WHERE (`fid` = $userid AND `tid` = $tid) OR (`fid` = $tid AND `tid` = $userid)");
	   $ret = $dsql->dsqlOper($sql, "results");
	   if($ret){
		   $id = $ret[0]['id'];
		   $fid_ = $ret[0]['fid'];
		   $tid_ = $ret[0]['tid'];
		   $state = $ret[0]['state'];
		   $delfrom = $ret[0]['delfrom'];
		   $delto = $ret[0]['delto'];

		   if($state){

			   //如果已经是好友，但是申请人将对方删除了，此时直接更新状态，不需要再申请
			   if($fid_ == $userid && $delfrom){
				   $sql = $dsql->SetQuery("UPDATE `#@__member_friend` SET `delfrom` = 0 WHERE `id` = " . $id);
				   $dsql->dsqlOper($sql, "update");

				   //发送消息
				   $this->param = array(
					   'fid' => $userid,
					   'from' => $fromToken,
					   'tid' => $tid,
					   'to' => $toToken,
					   'type' => 'member',
					   'contentType' => 'apply',
					   'content' => '你们已成功添加为好友'
				   );
				   $this->sendImChat();

				   return '添加成功';
			   }

			   //如果已经是好友，但是申请人将对方删除了，此时直接更新状态，不需要再申请
			   if($tid_ == $userid && $delto){
				   $sql = $dsql->SetQuery("UPDATE `#@__member_friend` SET `delto` = 0 WHERE `id` = " . $id);
				   $dsql->dsqlOper($sql, "update");

				   //发送消息
				   $this->param = array(
					   'fid' => $userid,
					   'from' => $fromToken,
					   'tid' => $tid,
					   'to' => $toToken,
					   'type' => 'member',
					   'contentType' => 'apply',
					   'content' => '你们已成功添加为好友'
				   );
				   $this->sendImChat();

				   return '添加成功';
			   }

			   //如果已经是好友，并且双方都没有删除
			   if(!$delfrom && !$delto){
					return array("state" => 200, "info" => "你们已经是好友了！");
				}
		   }else{
			   //更新状态
			   $sql = $dsql->SetQuery("UPDATE `#@__member_friend` SET `state` = 1, `delto` = 1, `date` = '$time' WHERE `id` = " . $id);
			   $dsql->dsqlOper($sql, "update");
		   }
	   }else{
		   $sql = $dsql->SetQuery("INSERT INTO `#@__member_friend` (`fid`, `tid`, `state`, `date`, `delfrom`, `delto`, `temp`, `tempdelfrom`, `tempdelto`) VALUES ('$userid', '$tid', 1, '$time', 0, 1, 1, 0, 0)");
		   $dsql->dsqlOper($sql, "update");
	   }


	   //发送消息
	   $this->param = array(
		   'fid' => $userid,
		   'from' => $fromToken,
		   'tid' => $tid,
		   'to' => $toToken,
		   'type' => 'member',
		   'contentType' => 'apply',
		   'content' => $note
	   );
	   $ret = $this->sendImChat();


	   //会员通知
	   $param = array(
		   "service"  => "member",
		   "type" => "user",
		   "template" => "chat",
		   "uid"   => $userid
	   );

	   //自定义配置
	   $config = array(
		   "title" => "好友通知",
		   "content" => $tname . "请求加您好友",
		   "date" => date("Y-m-d H:i:s", time())
	   );
	   updateMemberNotice($tid, "会员-消息提醒", $param, $config, "", array('type' => 'im', 'from' => $userid, 'to' => $tid));


	   //正常申请流程
	   return '申请成功';
   }



    /**
     * 删除好友
     */
	public function delFriend(){
		global $userLogin;
		global $langData;
		global $dsql;

		//用户ID
		$userid = $userLogin->getMemberID();

		if($userid < 1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
		}

		$tid = $this->param['tid'];  //要删除的好友ID
		$type = $this->param['type'];  //删除类型，temp代表删除临时会话

		if(empty($tid)){
			return array("state" => 200, "info" => "请选择要删除的好友！");
		}


		//删除好友
		if($type != 'temp'){
			$sql = $dsql->SetQuery("UPDATE `#@__member_friend` SET `delfrom` = 1 WHERE `fid` = '$userid' AND `tid` = '$tid'");
			$dsql->dsqlOper($sql, "update");

			$sql = $dsql->SetQuery("UPDATE `#@__member_friend` SET `delto` = 1 WHERE `fid` = '$tid' AND `tid` = '$userid'");
			$dsql->dsqlOper($sql, "update");
		}

		//删除临时会话列表
		$sql = $dsql->SetQuery("UPDATE `#@__member_friend` SET `tempdelfrom` = 1 WHERE `fid` = '$userid' AND `tid` = '$tid'");
		$dsql->dsqlOper($sql, "update");

		$sql = $dsql->SetQuery("UPDATE `#@__member_friend` SET `tempdelto` = 1 WHERE `fid` = '$tid' AND `tid` = '$userid'");
		$dsql->dsqlOper($sql, "update");

		return '删除成功';
	}



    /**
     * 获取IM Token
	 * return array {
	 *    "online": 1,
	 *    "server": "wss://api.kumanyun.com/chat/",
	 *    "uid": "29",
	 *    "name": "昵称",
	 *    "photo": "头像",
	 *    "token": "VjJWUmFBTmtCemhUYmdoaURtbFdObFJyRFQ1U1lncHBCMklLT0FCcVZXTUZJUVo0QTJaVGJBPT0="
	 * }
     */
	public function getImToken(){
		global $userLogin;
		global $langData;
		global $cfg_km_accesskey_id;
		global $cfg_km_accesskey_secret;

		//用户ID
		$userid = $this->param['userid'] ? $this->param['userid'] : $userLogin->getMemberID();

		if($userid < 1){
			//return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
		}

		//用户信息
		if($userid > 0){
			$configHandels = new handlers('member', "detail");
			$userinfo = $configHandels->getHandle(array("id" => $userid, "friend" => 1, "from" => (int)$this->param['from']));

			if($userinfo['state'] != 100){
				return array("state" => 200, "info" => "用户信息错误");
			}

			//获取Token
			$params = array (
			    'uid' => $userid,
			    'name' => $userinfo['info']['nickname'],
			    'photo' => $userinfo['info']['photo'],
			    'type' => 'member',
				'getLastMessage' => 1
			);
		}else{
			//获取Token
			$params = array (
			    'uid' => 0,
			    'name' => '游客',
			    'photo' => '',
			    'type' => 'member'
			);
		}

		$request = new SendRequest_($cfg_km_accesskey_id, $cfg_km_accesskey_secret);
		$token = $request->curl('/chat/getToken.php', $params, 'urlencoded', 'POST');
		$tokenArr = json_decode($token, true);
		if($tokenArr['state'] == 100){
			$tokenArr['uid'] = $userid;
			$tokenArr['name'] = $userinfo['info']['nickname'];
			$tokenArr['photo'] = $userinfo['info']['photo'];
			$tokenArr['isfriend'] = $userinfo['info']['isfriend'];
			$tokenArr['token'] = $tokenArr['info'];
			$tokenArr['AccessKeyID'] = $cfg_km_accesskey_id;
			unset($tokenArr['state']);
			unset($tokenArr['info']);
		}
		return $tokenArr;
	}


	/**
     * 获取IM 好友列表
	 * return array {
	 *    "id": "1",
	 *    "userinfo": {
	 *        "uid": "20",
	 *        "name": "昵称",
	 *        "photo": "头像",
	 *        "getLastMessage": 1,
	 *        "friend": 29,
	 *        "type": "member"
	 *    },
	 *    "token": "VUdKU2ExMDZBVDRJTlFoaVVqVUNZZ1k1VkdkY2JGbzVWVEFLT0FOcEF6VlJkVm9rQUdVRU1nPT0=",
	 *    "online": 0,
	 *    "lastMessage": {
	 *        "time": "1559539594",
	 *        "type": "text",
	 *        "content": "最后一条信息内容",
	 *        "unread": "0"
	 *    }
	 * }
     */
	public function getImFriendList(){
		global $dsql;
		global $userLogin;
		global $cfg_km_accesskey_id;
		global $cfg_km_accesskey_secret;

		$userid = (int)$this->param['userid'];
		$type = $this->param['type'];
		$tongji = (int)$this->param['tongji'];

		//临时会话列表
		if($type == 'temp'){
			$sql = $dsql->SetQuery("SELECT `id`, `fid`, `tid` FROM `#@__member_friend` WHERE `fid` = '$userid' AND `tempdelfrom` = 0 AND `temp` = 1 UNION ALL SELECT `id`, `fid`, `tid` FROM `#@__member_friend` WHERE `tid` = '$userid' AND `tempdelto` = 0 AND `temp` = 1");
		}else{
			$sql = $dsql->SetQuery("SELECT `id`, `fid`, `tid` FROM `#@__member_friend` WHERE `fid` = '$userid' AND `delfrom` = 0 AND `delto` = 0 AND `state` = 1 UNION ALL SELECT `id`, `fid`, `tid` FROM `#@__member_friend` WHERE `tid` = '$userid' AND `delfrom` = 0 AND `delto` = 0 AND `state` = 1");
		}

		$friendList = array();

	    $ret = $dsql->dsqlOper($sql, "results");
	    if($ret){
			$dataArr = array();
	        foreach($ret as $key => $val){
	            $_id = $val['id'];
	            $_fid = $val['fid'];
	            $_tid = $val['tid'];
	            $_lastmessage = $val['lastmessage'];
	            $_lastdate = $val['lastdate'];

	            $toUserid = $_fid == $userid ? $_tid : $_fid;
	            $toUserinfo = $userLogin->getMemberInfo($toUserid);

				if(is_array($toUserinfo) && $toUserinfo['nickname']){

					//整理数据，下面一次性请求，否则会占用资源
					$toParams = array (
						'id' => $_id,
						'uid' => $toUserid,
						'name' => $toUserinfo['nickname'],
						'photo' => $toUserinfo['photo'],
						'getLastMessage' => 1,
						'friend' => $userid,
						'type' => 'member',
						'tongji' => $tongji
					);
					array_push($dataArr, $toParams);

				}
	        }

			if($dataArr){

				//获取Token
				$request = new SendRequest_($cfg_km_accesskey_id, $cfg_km_accesskey_secret);
				$token = $request->curl('/chat/getFriendStatistics.php', $dataArr, 'json', 'POST');
				$toTokenArr = json_decode($token, true);

				if($toTokenArr && $toTokenArr['state'] == 100){
					$info = $toTokenArr['info'];
					if($info && is_array($info)){
						foreach ($info as $key => $value) {

							$userinfo = array();
							foreach ($dataArr as $k => $v) {
								if($v['id'] == $value['id']){
									$userinfo = array(
										'uid' => $v['uid'],
										'name' => $v['name'],
										'photo' => $v['photo'],
										'friend' => $v['friend'],
										'uid' => $v['uid'],
										'type' => $v['type']
									);
								}
							}

							array_push($friendList, array(
								'id' => $value['id'],
								'lastMessage' => $value['lastMessage'],
								'online' => $value['online'],
								'token' => $value['token'],
								'userinfo' => $userinfo
							));

						}
					}
				}

			}
	    }

	    $online = array_column($friendList, 'online');
	    array_multisort($online, SORT_DESC, $friendList);

	    //对数据二次清洗，将在线状态的会员，按最新聊天时间排序
	    $onlineList = array();
	    $offlineList = array();
	    foreach ($friendList as $key => $value) {
	        if($value['online']){
	            $value['lastMessageTime'] = $value['lastMessage'] && $value['lastMessage']['time'] ? $value['lastMessage']['time'] : 0;
	            array_push($onlineList, $value);
	        }else{
	            array_push($offlineList, $value);
	        }
	    }

	    //按最新聊天时间排序
	    $online = array_column($onlineList, 'lastMessageTime');
	    array_multisort($online, SORT_DESC, $onlineList);

	    //合并在线和不在线的会员
	    $friendList = array_merge($onlineList, $offlineList);

		return $friendList;

	}



   /**
	* 获取IM 聊天记录
	* return array {
	*    "pageInfo": {
	*        "page": 1,
	*        "pageSize": 20,
	*        "totalPage": 15,
	*        "totalCount": 300
	*    },
	*    "list": [
	*        {
	*            "id": "390",
	*            "fid": "29",
	*            "tid": "20",
	*            "time": "1559539594",
	*            "type": "text",
	*            "content": "内容"
	*        },
	*    ],
	*    "userInfo": {
	*        "20": {
	*            "name": "昵称",
	*            "photo": "头像",
	*            "online": 0
	*        },
	*        "29": {
	*            "name": "昵称",
	*            "photo": "头像",
	*            "online": 1
	*        }
	*    }
	* }
	*/
   public function getImChatLog(){
	   global $userLogin;
	   global $langData;
	   global $cfg_km_accesskey_id;
	   global $cfg_km_accesskey_secret;

	   $userid = $this->param['userid'] ? $this->param['userid'] : $userLogin->getMemberID();

	   $from = $this->param['from'];
	   $to = $this->param['to'];
	   $time = (int)$this->param['time'];
	   $page = (int)$this->param['page'];
	   $pageSize = (int)$this->param['pageSize'];

	   if($userid < 1){
		   return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
	   }

	   //获取Token
       $params = array (
           'action' => 'log',
           'from' => $from,
           'to' => $to,
           'time' => $time ? (int)$time : time(),
           'page' => $page ? (int)$page : 1,
           'pageSize' => $pageSize ? (int)$pageSize : 20
       );
       $request = new SendRequest_($cfg_km_accesskey_id, $cfg_km_accesskey_secret);
       $ret = $request->curl('/chat/chat.php', $params, 'urlencoded', 'POST');
       $ret = json_decode($ret, true);
	   if($ret['state'] == 100){
		   unset($ret['state']);
	   }
	   return $ret;

   }



  /**
   * 发送IM 聊天
   */
  public function sendImChat(){
		global $userLogin;
		global $dsql;
		global $langData;
 	    global $cfg_km_accesskey_id;
 	    global $cfg_km_accesskey_secret;

		$userid = $userLogin->getMemberID();
		$userid = $this->param['userid'] ? $this->param['userid'] : $userid;

		if($userid < 1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
		}

		$fid = (int)$this->param['fid'];
		$from = $this->param['from'];
		$tid = (int)$this->param['tid'];
		$to = $this->param['to'];
		$type = $this->param['type'];
		$contentType = $this->param['contentType'];
		$content = $this->param['content'];

		if($fid != $userid){
			return array("state" => 200, "info" => '发送人不是当前登录账号！');
		}

		if($fid == $tid){
			return array("state" => 200, "info" => '不可以给自己发信息！');
		}

		//检查好友表是否存在两人关系，如果没有，则新增记录
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_friend` WHERE (`fid` = $fid AND `tid` = $tid) OR (`fid` = $tid AND `tid` = $fid)");
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			$sql = $dsql->SetQuery("INSERT INTO `#@__member_friend` (`fid`, `tid`, `state`, `date`, `delfrom`, `delto`, `temp`, `tempdelfrom`, `tempdelto`) VALUES ('$fid', '$tid', 0, 0, 0, 0, 1, 0, 0)");
			$dsql->dsqlOper($sql, "update");
		}


		//更新临时会话列表
		$sql = $dsql->SetQuery("UPDATE `#@__member_friend` SET `tempdelfrom` = 0, `tempdelto` = 0, `temp` = 1 WHERE (`fid` = $fid AND `tid` = $tid) OR (`fid` = $tid AND `tid` = $fid)");
		$dsql->dsqlOper($sql, "update");

		//获取Token
		$params = array (
			'action' => 'sendToUser',
			'type' => $type,
			'contentType' => $contentType,
			'from' => $from,
			'to' => $to,
			'msg' => $content
		);
		$request = new SendRequest_($cfg_km_accesskey_id, $cfg_km_accesskey_secret);
		$ret = $request->curl('/chat/chat.php', $params, 'json', 'POST');
		$ret = json_decode($ret, true);
		if($ret['state'] == 100){

			//未读消息发送提醒
			if($ret['info']['unread']){
		 	   $param = array(
		 		   "service"  => "member",
				   "type" => "user",
		 		   "template" => "chat",
		 		   "uid"   => $fid
		 	   );

			   //查询用户信息
			   $uinfo = $userLogin->getMemberInfo($fid);
			   $name = $uinfo['nickname'];

			   $note = '';
			   if($contentType == 'text'){
				   $note = strstr($content, '△') ? '[表情]' : $content;
			   }elseif($contentType == 'image'){
				   $note = '[图片]';
			   }elseif($contentType == 'video'){
				   $note = '[视频]';
			   }elseif($contentType == 'audio'){
				   $note = '[语音]';
			   }elseif($contentType == 'recfriend'){
				   $note = '[好友推荐]';
			   }elseif($contentType == 'mapshare'){
				   $note = '[地图位置]';
			   }elseif($contentType == 'apply'){
				   $note = '[好友申请]';
			   }elseif($contentType == 'link'){
				   $note = '[链接]';
			   }

		 	   //自定义配置
		 	   $config = array(
		 		   "title" => "聊天消息",
		 		   "content" => $name . "：" . $note,
				   "date" => date("Y-m-d H:i:s", time())
		 	   );
		 	   updateMemberNotice($tid, "会员-消息提醒", $param, $config, "", array('type' => 'im', 'from' => $fid, 'to' => $tid));
			}

			return 'success';
		}else{
			return $ret;
		}

	}



   /**
	* 创建聊天室
	*/
	public function createChatRoom(){
		global $userLogin;
		global $langData;
 	    global $cfg_km_accesskey_id;
 	    global $cfg_km_accesskey_secret;

		$userid = $userLogin->getMemberID();
		$userid = $this->param['userid'] ? $this->param['userid'] : $userid;

		if($userid < 1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
		}

		$mark  = $this->param['mark'];
		$title = $this->param['title'];
		$url   = $this->param['url'];

		$params = array (
			'type' => 'chat',
			'uid' => $userid,
			'mark' => $mark,
			'title' => $title,
			'date' => time(),
			'url' => $url
		);
		$request = new SendRequest_($cfg_km_accesskey_id, $cfg_km_accesskey_secret);
		$ret = $request->curl('/chat/getToken.php', $params, 'urlencoded', 'POST');
		$ret = json_decode($ret, true);
		if($ret['state'] == 100){
			// return $ret;
			return $ret['server'];
		}else{
			return $ret;
		}

	}



   /**
	* 加入聊天室
	*/
	public function joinChatRoom(){
		global $userLogin;
		global $langData;
		global $cfg_km_accesskey_id;
		global $cfg_km_accesskey_secret;

		//APP端传用户ID
		if(isApp()){
			$userid = $this->param['userid'];
		}else{
			$userid = $userLogin->getMemberID();
		}

		if($userid < 1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
		}

		$mark  = $this->param['mark'];
		$from = $this->param['from'];

		$param = array (
	        'action' => 'joinChat',
	        'mark' => $mark,
	        'uid' => $from
	    );
	    $request = new SendRequest_($cfg_km_accesskey_id, $cfg_km_accesskey_secret);
	    $ret = $request->curl('/chat/chat.php', $param, 'json', 'POST');
		$ret = json_decode($ret, true);
		if($ret['state'] == 100){
			return $ret['info'];
		}else{
			return $ret;
		}

	}



   /**
	* 获取聊天室在线人数
	*/
	public function getChatRoomOnlineUserCount(){
		global $userLogin;
		global $langData;
		global $cfg_km_accesskey_id;
		global $cfg_km_accesskey_secret;

		$userid = $userLogin->getMemberID();

		if($userid < 1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
		}

		$from  = $this->param['from'];
		$mark  = $this->param['mark'];

		$param = array (
	        'action' => 'chatRoomOnlineUserCount',
	        'from' => $from,
	        'mark' => $mark
	    );
	    $request = new SendRequest_($cfg_km_accesskey_id, $cfg_km_accesskey_secret);
	    $ret = $request->curl('/chat/chat.php', $param, 'json', 'POST');
		$ret = json_decode($ret, true);
		if($ret['state'] == 100){
			return $ret['info'];
		}else{
			return $ret;
		}

	}



   /**
	* 获取IM 聊天室记录
	* return array {
	*    "pageInfo": {
	*        "page": 1,
	*        "pageSize": 20,
	*        "totalPage": 15,
	*        "totalCount": 300
	*    },
	*    "list": [
	*        {
	*            "id": "390",
	*            "fid": "29",
	*            "tid": "20",
	*            "time": "1559539594",
	*            "type": "text",
	*            "content": "内容"
	*        },
	*    ]
	* }
	*/
   public function getImChatRoomLog(){
	   global $userLogin;
	   global $langData;
	   global $cfg_km_accesskey_id;
	   global $cfg_km_accesskey_secret;

	   //APP端传用户ID
	   if(isApp()){
		   $userid = $this->param['userid'];
	   }else{
		   $userid = $userLogin->getMemberID();
	   }

	   $from = $this->param['from'];
	   $mark = $this->param['mark'];
	   $time = (int)$this->param['time'];
	   $page = (int)$this->param['page'];
	   $pageSize = (int)$this->param['pageSize'];

	   if($userid < 1){
		   //return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
	   }

	   //获取Token
       $params = array (
           'action' => 'chatLog',
           'from' => $from,
           'mark' => $mark,
           'time' => $time ? (int)$time : time(),
           'page' => $page ? (int)$page : 1,
           'pageSize' => $pageSize ? (int)$pageSize : 20
       );
       $request = new SendRequest_($cfg_km_accesskey_id, $cfg_km_accesskey_secret);
       $ret = $request->curl('/chat/chat.php', $params, 'urlencoded', 'POST');
       $ret = json_decode($ret, true);
	   if($ret['state'] == 100){
		   unset($ret['state']);
	   }
	   return $ret;

   }



    /**
     * 发送IM 聊天室
     */
    public function sendImChatRoom(){
		global $userLogin;
		global $dsql;
		global $langData;
		global $cfg_km_accesskey_id;
		global $cfg_km_accesskey_secret;

		//APP端传用户ID
		if(isApp()){
 		   $userid = $this->param['userid'];
 	    }else{
 		   $userid = $userLogin->getMemberID();
 	    }

		if($userid < 1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);   //登录超时，请重新登录！
		}

		$from = $this->param['from'];
		$mark = $this->param['mark'];
		$contentType = $this->param['contentType'];
		$content = $this->param['content'];

		//获取Token
		$params = array (
			'action' => 'sendToChat',
			'contentType' => $contentType,
			'from' => $from,
			'mark' => $mark,
			'msg' => $content
		);

		$request = new SendRequest_($cfg_km_accesskey_id, $cfg_km_accesskey_secret);
		$ret = $request->curl('/chat/chat.php', $params, 'json', 'POST');
		$ret = json_decode($ret, true);
		if($ret['state'] == 100){
			return $ret['info'];
		}else{
			return $ret;
		}

	}

	public function getUrl(){
		//getUrlPath($this->param);
		return getUrlPath($this->param['param']);
	}

}
