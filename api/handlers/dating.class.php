<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 交友模块API接口
 *
 * @version        $Id: dating.class.php 2014-7-24 下午15:20:18 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class dating {
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
     * 自助建站基本参数
     * @return array
     */
	public function config(){

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
		// global $custom_map;               //自定义地图
		// global $customAtlasMax;           //图集数量限制
		global $tagsLength;               //交友标签最多数量
		global $graspLength;              //会的技能最多数量
		global $learnLength;              //想学技能最多数量
		global $eventsInfo;               //活动免责说明

		global $cfg_map;                  //系统默认地图
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

		require(HUONIAOINC."/config/dating.inc.php");

		//获取当前城市名
		global $siteCityInfo;
		if(is_array($siteCityInfo)){
			$cityName = $siteCityInfo['name'];
		}

		if(empty($custom_map)) $custom_map = $cfg_map;

		//如果上传设置为系统默认，则以下参数使用系统默认
		if($customUpload == 0){
			$custom_softSize = $cfg_softSize;
			$custom_softType  = $cfg_softType;
			$custom_thumbSize = $cfg_thumbSize;
			$custom_thumbType = $cfg_thumbType;
			$custom_atlasSize = $cfg_atlasSize;
			$custom_atlasType = $cfg_atlasType;
		}

		$hotline = $hotline_config == 0 ? $cfg_hotline : $customHotline;

		$params = !empty($this->param) && !is_array($this->param) ? explode(',',$this->param) : "";

		// $domainInfo = getDomain('dating', 'config');
		// $customChannelDomain = $domainInfo['domain'];
		// if($customSubDomain == 0){
		// 	$customChannelDomain = "http://".$customChannelDomain;
		// }elseif($customSubDomain == 1){
		// 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
		// }elseif($customSubDomain == 2){
		// 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
		// }

		// include HUONIAOINC.'/siteModuleDomain.inc.php';
		$customChannelDomain = getDomainFullUrl('dating', $customSubDomain);

        //分站自定义配置
        $ser = 'dating';
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
						$customLogo = getFilePath($customLogoUrl);
					}else{
						$customLogo = getFilePath($cfg_weblogo);
					}

					$return['logoUrl'] = $customLogo;
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
				}elseif($param == "hotline"){
					$return['hotline'] = $hotline;
				}elseif($param == "atlasMax"){
					$return['atlasMax'] = $customAtlasMax;
				}elseif($param == "map"){
					$return['map'] = $custom_map;
				}elseif($param == "template"){
					$return['template'] = $customTemplate;
				}elseif($param == "touchTemplate"){
					$return['touchTemplate'] = $customTouchTemplate;
				}elseif($param == "tagsLength"){
					$return['tagsLength'] = $tagsLength;
				}elseif($param == "graspLength"){
					$return['graspLength'] = $graspLength;
				}elseif($param == "learnLength"){
					$return['learnLength'] = $learnLength;
				}elseif($param == "eventsInfo"){
					$return['eventsInfo'] = $eventsInfo;
				}elseif($param == "softSize"){
					$return['softSize'] = $custom_softSize;
				}elseif($param == "softType"){
					$return['softType'] = $custom_softType;
				}elseif($param == "thumbSize"){
					$return['thumbSize'] = $custom_thumbSize;
				}elseif($param == "thumbType"){
					$return['thumbType'] = $custom_thumbType;
				}
			}

		}else{

			//自定义LOGO
			if($customLogo == 1){
				$customLogo = getFilePath($customLogoUrl);
			}else{
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
			$return['map']           = $custom_map;
			$return['template']      = $customTemplate;
			$return['touchTemplate'] = $customTouchTemplate;
			$return['tagsLength']    = $tagsLength;
			$return['graspLength']   = $graspLength;
			$return['learnLength']   = $learnLength;
			$return['eventsInfo']    = $eventsInfo;
			$return['softSize']      = $custom_softSize;
			$return['softType']      = $custom_softType;
			$return['thumbSize']     = $custom_thumbSize;
			$return['thumbType']     = $custom_thumbType;

			$return['goldName']      = $goldName;
			$return['goldRatio']     = $goldRatio;
			$return['goldDeposit']   = $goldDeposit;
			$return['keyPrice']      = $keyPrice;

			$return['leadPrice']      = $leadPrice ? unserialize($leadPrice) : array();

			if(!empty($extractRatio)){
				$extractRatio = unserialize($extractRatio);
				$r = array();
				foreach ($extractRatio as $k => $v) {
					$r[$v['type']] = array(
						"hn1" => $v['hn1'],
						"hn2" => $v['hn2'],
						"u2" => $v['u2'],
						"pt" => $v['pt'],
					);
				}
				$return['extractRatio'] = $r;
			}else{
				$return['extractRatio'] = "";
			}
			$return['withdrawRatio']     = (float)$withdrawRatio;
			$return['withdrawMinAmount'] = (float)$withdrawMinAmount;
			$return['voiceswitch']       = (int)$voiceswitch;
			$return['videoswitch']       = (int)$videoswitch;

			$return['plat_title']        = $plat_title;
			$return['plat_litpic']       = $plat_litpic ? getFilePath($plat_litpic) : "";
			$return['plat_service']      = $plat_service;

		}

		return $return;

	}


	/**
     * 固定字段
     * @return array
     */
	public function item(){
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
		$results = $dsql->getTypeList($type, "dating_item", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 交友地区
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
     * 标签
     * @return array
     */
	public function tags(){
		global $dsql;

		$type = !empty($this->param) ? $this->param : 0;
		if(!is_numeric($type)) return array("state" => 200, "info" => '格式错误！');

		$results = $dsql->getTypeList($type, "dating_tags");
		if($results){
			return $results;
		}
	}


	/**
     * 技能
     * @return array
     */
	public function skill(){
		global $dsql;
		$type = !empty($this->param) ? $this->param : 0;

		$param = $this->param ? $this->param : 0;
		$type = is_array($param) ? $param['type'] : $param;
		$type = (int)$type;
		if(!is_numeric($type)) return array("state" => 200, "info" => '格式错误！');

		$results = $dsql->getTypeList($type, "dating_skill");
		if($results){
			return $results;
		}
	}

	/**
     * 会员列表
     * @return array
     */
	public function memberList(){
		global $dsql;
		global $userLogin;
		global $langData;
		$userid = $userLogin->getMemberID();

		// 测试
		$test = 0;

		// $isPC = !isMobile();

		$pageinfo = $list = array();
		$sex = $addrid = $age = $marriage = $height = $bodytype = $housetag = $workstatus = $income = $education = $smoke = $drink = $workandrest = $cartag = $maxconsume = $romance = $property = $online = $page = $pageSize = $orderby = $where = $mWhere = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$sex           = $this->param['sex'];
				$addrid        = (int)$this->param['addrid'];
				$age           = $this->param['age'];
				$marriage      = (int)$this->param['marriage'];
				$height        = $this->param['height'];
				$bodytype      = (int)$this->param['bodytype'];
				$workstatus    = (int)$this->param['workstatus'];
				$income        = (int)$this->param['income'];
				$education     = (int)$this->param['education'];
				$smoke         = (int)$this->param['smoke'];
				$drink         = (int)$this->param['drink'];
				$workandrest   = (int)$this->param['workandrest'];
				$maxconsume    = (int)$this->param['maxconsume'];
				$romance       = (int)$this->param['romance'];
				$property      = $this->param['property'];
				$online        = (int)$this->param['online'];
				$orderby       = $this->param['orderby'];
				$lng           = $this->param['lng'];
				$lat           = $this->param['lat'];
				$company       = (int)$this->param['company'];
				$store         = (int)$this->param['store'];
				$nearby        = (int)$this->param['nearby'];
				$nickname      = $this->param['nickname'] ? $this->param['nickname'] : $this->param['keywords'];
				$housetag      = (int)$this->param['housetag'];
				$cartag        = (int)$this->param['cartag'];
				$household     = (int)$this->param['household'];
				$hometown      = (int)$this->param['hometown'];
				$child         = (int)$this->param['child'];
				$constellation = (int)$this->param['constellation'];
				$page          = (int)$this->param['page'];
				$pageSize      = (int)$this->param['pageSize'];
			}
		}

		$where1 = $where2 = "";

		$is_par = false;	// 是否为查询会员的所属红娘或门店，用于输出手机号；

		$uLevel = 0;
		$uid = $uid1 = $uid2 = $uid1Company = 0;
		$sql = $dsql->SetQuery("SELECT `id`, `type`, `level`, `company` FROM `#@__dating_member` WHERE `userid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			foreach ($ret as $k => $v) {
				if($v['type'] == 0){
					$uid = $v['id'];
					$uLevel = $v['level'];
					$where2 .=  " AND d.`id` != $uid";
				}elseif($v['type'] == 1){
					$uid1 = $v['id'];
					$uid1Company = $v['company'];
				}elseif($v['type'] == 2){
					$uid2 = $v['id'];
				}
			}
		}else{
			$uLevel = 0;
		}

		// $cityid = getCityId($this->param['cityid']);
		// if($cityid && $u != 1){
		// 	$where .= " AND `cityid` = ".$cityid;
		// }else{
		// 	$where .= " AND `cityid` !=0 ";
		// }


		$where .= " AND d.`type` = 0 AND d.`state` = 1 AND d.`dateswitch` = 1";

		//在线
		if($online){
			$where .= " AND m.`online` > 0";
		}

		//红娘所属
		if($company){
			$where .= " AND d.`company` = $company";
			if($company == $uid1) $is_par = true;
		}
		// 门店所属
		if($store){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `company` = $store AND `type` = 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				global $arr_data;
				$arr_data = "";
				$hnIds = arr_foreach($ret);
				$where .= " AND d.`company` IN (".join(",", $hnIds).")";
			}else{
				$where .= " AND 1 = 2";
			}
			if($store == $uid2) $is_par = true;
		}


		if(empty($company) && empty($store)){
			$where .= $where2;
		}else{
			$is_par = true;
		}

		//性别
		if($sex != ""){
			$where .= " AND d.`sex` = ".$sex;
		}

		//门店或者红娘查看自己的用户时不筛选照片，否则只输出有照片的会员
		// echo $company."==".$uid1."==".$uid2."==";die;
		if(($company && ($company == $uid1) ) || ($store && $store == $uid2) || ($uid1Company && $uid1Company == $uid2) ){

		}else{
			$where .= " AND d.`photo` != ''";
		}
		// 基本筛选 s-------------------------------------------

		//区域
		if(!empty($addrid)){
			$area_arr = $dsql->getTypeList($addrid, "site_area");
			if($area_arr){
				$lower = arr_foreach($area_arr);
				$lower = $addrid.",".join(',',$lower);
			}else{
				$lower = $addrid;
			}
			$where .= " AND d.`addrid` in ($lower)";
		}

		//年龄计算
		if(!empty($age)){

			$age = explode(",", $age);
			if(count($age) == 2){
				$fage = $age[0];
				$tage = $age[1];

				if(!empty($fage) && !empty($tage)){
					if(!empty($fage)){
						$fage = date('Y') - $fage - 1;
						$fage = GetMkTime($fage."-12-31");
					}
					if(!empty($tage)){
						$tage = date('Y') - $tage - 2;
						$tage = GetMkTime($tage."-12-31");
					}
					$where .= " AND d.`birthday` BETWEEN $tage AND $fage";
				}elseif(empty($fage) && empty($tage)){

				}elseif(empty($fage)){
					$tage = date('Y') - $tage - 1;
					$tage = GetMkTime($tage."-12-31");
					$where .= " AND d.`birthday` > $tage";
				}elseif(empty($tage)){
					$fage = date('Y') - $fage - 1;
					$fage = GetMkTime($fage."-12-31");
					$where .= " AND d.`birthday` < $fage";
				}
			}

		}

		//身高
		if(!empty($height)){
			$height = explode(",", $height);
			if(empty($height[0])){
				if($height[1] == '145'){
					$where .= " AND d.`height` = -1";
				}else{
					$where .= " AND d.`height` < " . $height[1];
				}
			}elseif(empty($height[1])){
				if($height[1] == '210'){
					$where .= " AND d.`height` = -2";
				}else{
					$where .= " AND d.`height` > " . $height[0];
				}
			}else{
				$where .= " AND d.`height` BETWEEN " . $height[0] . " AND " . $height[1];
			}
		}

		//婚姻情况
		if(!empty($marriage)){
			$where .= ' AND d.`marriage` = '.$marriage;
		}

		//学历
		if(!empty($education)){
			$where .= ' AND d.`education` = '.$education;
		}

		//收入
		if(!empty($income)){
			$where .= ' AND d.`income` = '.$income;
		}

		// 基本筛选 e-------------------------------------------

		// 高级筛选 s-------------------------------------------
		if($uLevel > 1){
			//居住情况
			if(!empty($housetag)){
				$where .= ' AND d.`housetag` = '.$housetag;
			}

			//购车情况
			if(!empty($cartag)){
				$where .= ' AND d.`cartag` = '.$cartag;
			}

			//户口
			if(!empty($household)){
				// $where .= ' AND d.`household` = '.$household;
				$area_arr = $dsql->getTypeList($household, "site_area");
				if($area_arr){
					$lower = arr_foreach($area_arr);
					$lower = $household.",".join(',',$lower);
				}else{
					$lower = $household;
				}
				$where .= " AND d.`household` in ($lower)";
			}

			//家乡
			if(!empty($hometown)){
				// $where .= ' AND d.`hometown` = '.$hometown;
				$area_arr = $dsql->getTypeList($hometown, "site_area");
				if($area_arr){
					$lower = arr_foreach($area_arr);
					$lower = $hometown.",".join(',',$lower);
				}else{
					$lower = $hometown;
				}
				$where .= " AND d.`hometown` in ($lower)";
			}

			//有无子女
			if(!empty($child)){
				$where .= ' AND d.`child` = '.$child;
			}

			//星座
			if(!empty($constellation) && $constellation > 0 && $constellation < 13){
				$xz = array('白羊座','金牛座','双子座','巨蟹座','狮子座','处女座','天秤座','天蝎座','射手座','魔羯座','水瓶座','双鱼座');
				$xzn = $xz[$constellation-1];
				$where .= " AND d.`constellation` = '$xzn'";
			}
		}
		// 高级筛选 e-------------------------------------------

		//体型
		if(!empty($bodytype)){
			$where .= ' AND d.`bodytype` = '.$bodytype;
		}

		//工作状态
		if(!empty($workstatus)){
			$where .= ' AND d.`workstatus` = '.$workstatus;
		}

		//吸烟
		if(!empty($smoke)){
			$where .= ' AND d.`smoke` = '.$smoke;
		}

		//喝酒
		if(!empty($drink)){
			$where .= ' AND d.`drink` = '.$drink;
		}

		//作息时间
		if(!empty($workandrest)){
			$where .= ' AND d.`workandrest` = '.$workandrest;
		}
		// 昵称
		if(!empty($nickname)){
			$where .= " AND d.`nickname` LIKE '%$nickname%'";
		}

		//最大消费
		$juli = "";
		if($lng && $lat){
			$juli = ", ROUND(
		        6378.138 * 2 * ASIN(
		            SQRT(POW(SIN(($lat * PI() / 180 - d.`lat` * PI() / 180) / 2), 2) + COS($lat * PI() / 180) * COS(d.`lat` * PI() / 180) * POW(SIN(($lng * PI() / 180 - d.`lng` * PI() / 180) / 2), 2))
		        ) * 1000
		    ) AS juli";

			//筛选20KM范围内的店铺
			if($nearby){
				$nearby = $nearby < 10 ? 10 : $nearby;
				$where .= " AND ROUND(
					6378.138 * 2 * ASIN(
						SQRT(POW(SIN(($lat * PI() / 180 - d.`lat` * PI() / 180) / 2), 2) + COS($lat * PI() / 180) * COS(d.`lat` * PI() / 180) * POW(SIN(($lng * PI() / 180 - s.`lng` * PI() / 180) / 2), 2))
					) * 1000
				) < ".($nearby * 1000);
			}
		}

		$order = " ORDER BY m.`online` DESC, d.`id` DESC";
		// 最新加入
		if($orderby == 1){
			$order = " ORDER BY d.`jointime` DESC, m.`online` DESC";
			// $order = " ORDER BY m.`birthday` ASC, m.`online` DESC, d.`id` DESC";
		// 最近登录
		}elseif($orderby == 2){
			$order = " ORDER BY d.`activedate` DESC, m.`online` DESC";
		// 收入排序
		}elseif($orderby == 3){
			$order = " ORDER BY d.`income` DESC, m.`online` DESC, d.`id` DESC";
		// 距离排序
		}elseif($orderby == 4 && $juli){
			$order = " ORDER BY `juli`, m.`online` DESC, d.`id` DESC";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;


		$archives_count = $dsql->SetQuery("SELECT COUNT(d.`id`) total FROM `#@__dating_member` d LEFT JOIN `#@__member` m ON m.`id` = d.`userid` WHERE m.`id` = d.`userid`".$where);


		//总条数
		$results_count = $dsql->dsqlOper($archives_count, "results");
		$totalCount = $results_count[0]['total'];
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$archives = $dsql->SetQuery("SELECT d.*, m.`phone`, m.`phoneCheck`, m.`online`, m.`certifyState`
			$juli
			FROM `#@__dating_member` d LEFT JOIN `#@__member` m ON m.`id` = d.`userid` WHERE m.`id` = d.`userid`".$where.$order);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();

		if($results){
			// print_r($results);die;
			// $RenrenCrypt = new RenrenCrypt();

			$sql = $dsql->SetQuery("SELECT `id`, `name`, `icon` FROM `#@__dating_level`");
			$res = $dsql->dsqlOper($sql, "results");
			$levelCfg = array();
			if($res){
				foreach ($res as $k => $v) {
					$levelCfg[$v['id']] = array(
						"id" => $v['id'],
						"name" => $v['name'],
						"icon" => $v['icon'] ? getFilePath($v["icon"]) : ""
					);
				}
			}

			foreach($results as $key => $val){
				$list[$key]['id']       = $val['id'];
				$list[$key]['userid']   = $val['userid'];
				$list[$key]["sex"]      = $val['sex'];
				// $list[$key]["nickname"] = $val['level'] ? '<font style="color:#ff0101;">'.$val['nickname'].'</font>' : $val['nickname'];
				$list[$key]["nickname"] = $val['nickname'];
				$list[$key]["profile"]  = $val['profile'];
				$list[$key]["photo"]    = getFilePath($val["photo"]);
				$age                    = !empty($val['birthday']) ? getBirthAge(date("Y-m-d", $val['birthday'])) : "";
				$list[$key]["age"]      = $age;
				$list[$key]["level"]    = (int)$val['level'];
				$list[$key]["levelName"] = $val['level'] ? $levelCfg[$val['level']]['name'] : "";
				$list[$key]["levelIcon"] = $val['level'] ? $levelCfg[$val['level']]['icon'] : "";

				$list[$key]['like']    = $val['like'];
				$list[$key]['company'] = $val['company'];
				$list[$key]['entruct'] = $val['entruct'];

				$list[$key]['my_video_state'] = (int)$val['my_video_state'];
				$list[$key]['my_voice_state'] = (int)$val['my_voice_state'];

				$arr = array();
				$interests = $val['interests'];
				if($interests){
					$archives = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__dating_skill` WHERE `id` IN (".$interests.")");
					$typeResults = $dsql->dsqlOper($archives, "results");
					if($typeResults){
						foreach ($typeResults as $k => $v) {
							$arr[] = $v['typename'];
						}
					}
				}
				$list[$key]['interests'] = $arr;

				// 定向隐身
				$set_visit_ys = 0;
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `type` = 10 AND `ufrom` = ".$val['id']." AND `uto` = $uid");
				$ret = $dsql->dsqlOper($sql, "results");
				if(!$ret){
					if($val['juli']){
						$list[$key]['juli'] = $val['juli'] > 1000 ? (sprintf("%.1f", $val['juli'] / 1000) . $langData['siteConfig'][13][23]) : ($val['juli'] . $langData['siteConfig'][13][22]);  //距离   //千米  //米
					}else{
						$list[$key]['juli'] = '';
					}
				}else{
					$set_visit_ys = 1;
					$list[$key]['juli'] = '';
				}

				// $archives = $dsql->SetQuery("SELECT `online`, `certifyState`, `phoneCheck` FROM `#@__member` WHERE `id` = ".$val['userid']);
				// $userResults = $dsql->dsqlOper($archives, "results");
				// if($results){}
				$list[$key]["online"]       = (int)$val['online'];
				$list[$key]["certifyState"] = $val['certifyState'] == 1 ? 1 : 0;
				$list[$key]["phoneCheck"]   = (int)$val['phoneCheck'];

				// 定向隐身
				if((int)$userResults[0]['online']){
					if($set_visit_ys){
						$list[$key]["online"] = 0;
					}
				}



				//地区
				global $data;
				$data = "";
				$addrName = getParentArr("site_area", $val["addrid"]);
				$addrName = array_reverse(parent_foreach($addrName, "typename"));
				$list[$key]['addrName'] = $addrName;


				//标签
				// $tags = $val["tags"];
				// $list[$key]['tags'] = $tags;
				// $tagsSelected = array();
				// if(!empty($tags)){
				// 	$tags = explode(",", $tags);
				// 	foreach($tags as $value){
				// 		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_tags` WHERE `id` = $value");
				// 		$typeResults = $dsql->dsqlOper($archives, "results");
				// 		$name = $typeResults ? $typeResults[0]['typename'] : "";
				// 		array_push($tagsSelected, $name);
				// 	}
				// 	$list[$key]["tagsName"] = join(", ", $tagsSelected);
				// }

				//职务
				$list[$key]['duties'] = $val['duties'];
				if($val['duties']){
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $val["duties"]);
					$typeRes = $dsql->dsqlOper($typeSql, "results");
					$dutiesName = $typeRes ? $typeRes[0]['typename'] : "";
					$typename = $dsql->getTypeName($typeSql);
				}else{
					$dutiesName = "";
				}
				$list[$key]["dutiesName"] = $dutiesName;

				//学历
				$list[$key]['education'] = $val['education'];
				if($val['education']){
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $val["education"]);
					$typeRes = $dsql->dsqlOper($typeSql, "results");
					$educationName = $typeRes ? $typeRes[0]['typename'] : "";
				}else{
					$educationName = "";
				}
				$list[$key]["educationName"] = $educationName;

				//收入
				$list[$key]['income'] = $val['income'];
				if($val['income']){
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $val["income"]);
					$typeRes = $dsql->dsqlOper($typeSql, "results");
					$incomeName = $typeRes ? $typeRes[0]['typename'] : "";
					$typename = $dsql->getTypeName($typeSql);
				}else{
					$incomeName = "";
				}
				$list[$key]["incomeName"] = $incomeName;

				//婚姻情况
				$typename = "";
				$list[$key]['income'] = $val['income'];
				if($val["marriage"]){
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $val["marriage"]);
					$typename = $dsql->getTypeName($typeSql);
					$typename = $typename[0]['typename'];
				}
				$list[$key]["marriageName"] = $typename;

				$list[$key]['lng'] = $val['lng'];
				$list[$key]['lat'] = $val['lat'];
				$list[$key]['height'] = $val['height'];

				// 身高
				if($val['height'] == -1){
					$list[$key]['heightName'] = '145cm以下';
				}elseif($val['height'] == -2){
					$list[$key]['heightName'] = '210cm以上';
				}elseif($val['height'] == 0){
					$list[$key]['heightName'] = "";
				}else{
					$list[$key]['heightName'] = $val['height'].'cm';
				}

				//相册数量
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_album` WHERE `uid` = ".$val['userid']);
				$ret = $dsql->dsqlOper($sql, "totalCount");
				$list[$key]['album'] = $ret;

				$v1 = $v2 = $v2_2 = 0;
				if($uid > -1){
					//是否已经打过招呼
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$uid' AND `uto` = ".$val['id']." AND `type` = 3");
					$v1 = $dsql->dsqlOper($sql, "totalCount");

					//是否已经关注
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$uid' AND `uto` = ".$val['id']." AND `type` = 2");
					$v2 = $dsql->dsqlOper($sql, "totalCount");

					//是否已经被关注
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `uto` = '$uid' AND `ufrom` = ".$val['id']." AND `type` = 2");
					$v2_2 = $dsql->dsqlOper($sql, "totalCount");
				}
				$list[$key]['visit'] = $v1;
				$list[$key]['follow'] = $v2;
				$list[$key]['followby'] = $v2_2;

				// 相册数量
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_album` WHERE `uid` = ".$val['userid']);
				$list[$key]['picNum'] = $dsql->dsqlOper($sql, "totalCount");

				// 关注数
				// $sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `type` = 2 AND `uto` = ".$val['id']);
				// $list[$key]['like'] = $dsql->dsqlOper($sql, "totalCount");

				// 是否已申请牵线
				$sql = $dsql->SetQuery("SELECT `state` FROM `#@__dating_lead` WHERE (`ufrom` = $uid AND `uto` = ".$val['id'].") OR (`ufrom` = ".$val['id']." AND `uto` = $uid)");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$leadHas = 1;
					$leadState = $ret[0]['state'];
				}else{
					$leadHas = 0;
					$leadState = 0;
				}
				$list[$key]['leadHas'] = $leadHas;
				$list[$key]['leadState'] = $leadState;

				//URL
				$param = array(
					"service"  => "dating",
					"template" => "u",
					"id"       => $val['id']
				);
				$list[$key]["url"] = getUrlPath($param);

				if($is_par){
					$list[$key]['phone'] = $val['phone'];
				}

			}
		}else{
			return array("state" => 200, "info" => '暂无相关数据！');
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 会员信息（此处只取交友字段，会员其它信息请到会员接口查询）
     * @return array
     */
	public function memberInfo($autoCheck = false){
		global $dsql;
		global $userLogin;
		$id = $this->param;

		$where = "";
		$detail = array();

		$uid = $userLogin->getMemberID();

		//获取登录会员的交友ID

		$sql = $dsql->SetQuery("SELECT `id`, `level` FROM `#@__dating_member` WHERE `type` = 0 AND `userid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
			$ulevel = $ret[0]['level'];
		}

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		if($uid != $id){
			$where .= " AND d.`dateswitch` = 1 AND d.`state` = 1";
		}

		// 查看除联系方式外的特殊信息
		if($ulevel == 0 && $uid != $id){
			$lookSpecInfo = false;
		}else{
			$lookSpecInfo = true;
		}

		$archives = $dsql->SetQuery("SELECT d.* FROM `#@__dating_member` d LEFT JOIN `#@__member` m ON m.`id` = d.`userid` WHERE d.`userid` = m.`id` AND d.`id` = ".$id.$where);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$sql = $dsql->SetQuery("SELECT `phone`, `phoneCheck`, `certifyState`, `realname`, `online` FROM `#@__member` WHERE `id` = ".$results[0]['userid']);
			$ret = $dsql->dsqlOper($sql, "results");
			$sysUser = $ret[0];

			// 当前登陆用户
			if($uid == $id){
				$detail['qq']                = $results[0]['qq'];
				$detail['wechat']            = $results[0]['wechat'];
				$detail['money']             = $results[0]['money'];
				$detail['key']               = $results[0]['key'] + $results[0]['key_buy'];
				$detail['expired']           = $results[0]['expired'];
				$detail['phone']             = $sysUser['phone'];
				$detail['realname']          = $sysUser['realname'];
				$detail['lead']              = (int)$results[0]['lead'];
				$detail['leadExpired']       = $results[0]['leadExpired'];
				$detail['dayinit']           = $results[0]['dayinit'];
				$detail['visit_circle_date'] = $results[0]['visit_circle_date'];
				$detail['my_voice']          = $results[0]['my_voice'];
				$detail['my_video']          = $results[0]['my_video'];
				$detail['photo_']            = $results[0]['photo'];
				$detail['cover_']            = $results[0]['cover'];

				$leadCan = $results[0]['lead'];
				if($results[0]['lead'] && $results[0]['leadExpired']){
					$now = GetMkTime(time());
					if($now > $results[0]['leadExpired']){
						$leadCan = 0;
					}else{
						$leadCan = $results[0]['lead'];
					}
				}
				$detail['leadCan'] = $leadCan;
			}

			$detail['type']       = $results[0]['type'];
			$detail['online']     = $sysUser['online'];
			$detail['phoneCheck'] = $sysUser['phoneCheck'] == 1 ? 1 : 0;

			$detail['cityid'] = $results[0]['cityid'];

			$detail['my_voice_state'] = (int)$results[0]['my_voice_state'];
			$detail['my_video_state'] = (int)$results[0]['my_video_state'];

			$company = (int)$results[0]['company'];

			if($company){
				$this->param = $company;
				$detail['hn'] = $this->hnInfo();
			}else{
				$detail['hn'] = "";
			}
			$detail['company'] = $results[0]['company'];


			$detail['my_video_has'] = $detail['my_video'] ? 1 : 0;
			$detail['my_video_state'] = $detail['my_video_state'];

			// print_r($results[0]);
			$detail['id']           = $results[0]['id'];
			$detail['photo']        = $results[0]['photo'];
			$detail['lng']          = $results[0]['lng'];
			$detail['lat']          = $results[0]['lat'];
			$detail['profile']      = $results[0]['profile'];
			$detail['level']        = (int)$results[0]['level'];
			$detail['certifyState'] = $sysUser['certifyState'] == 1 ? 1 : 0;

			if($results[0]['level']){
				$sql = $dsql->SetQuery("SELECT `name`, `icon` FROM `#@__dating_level` WHERE `id` = ".$results[0]['level']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$detail['levelName']      = $ret[0]['name'];
					$detail['levelIcon']      = $ret[0]['icon'] ? getFilePath($ret[0]['icon']) : "";
				}
			}
			$detail['dateswitch'] = $results[0]['dateswitch'];
			$detail['state']      = $results[0]['state'];


			// 基本资料 hide: 子女情况、购车情况、购房情况
			$detail['numid']     = $results[0]['numid'];
			$detail['nickname']  = $results[0]['nickname'];
			$detail['sex']       = $results[0]['sex'];
			$detail['birthday']  = $results[0]['birthday'];
			$detail['height']    = $results[0]['height'] ;
			$detail['education'] = $results[0]['education'];
			$detail['income']    = $results[0]['income'];
			$detail['addrid']    = $results[0]['addrid'];
			$detail['marriage']  = $results[0]['marriage'];

			// 身高
			if($results[0]['height'] == -1){
				$detail['heightName'] = '145cm以下';
			}elseif($results[0]['height'] == -2){
				$detail['heightName'] = '210cm以上';
			}elseif($results[0]['height'] == 0){
				$detail['heightName'] = "";
			}else{
				$detail['heightName'] = $results[0]['height'].'cm';
			}

			// 小档案 hide: 家乡、户口、星座
			$detail['nation']          = $results[0]['nation'];//民族
			$detail['zodiac']          = $results[0]['zodiac'];//属相
			$detail['zodiacName']      = $results[0]['zodiac'];//属相
			$detail['bloodtype']       = $results[0]['bloodtype'];//血型
			$detail['bodytype']        = $results[0]['bodytype'];//体型
			$detail['bodyweight']      = $results[0]['bodyweight'];//体重
			$detail['looks']           = $results[0]['looks'];//相貌自评
			$detail['religion']        = $results[0]['religion'];//宗教信仰
			$detail['drink']           = $results[0]['drink'];//饮酒
			$detail['smoke']           = $results[0]['smoke'];//吸烟
			$detail['workandrest']     = $results[0]['workandrest'];//生活作息

			$detail['looksName']       = $results[0]['looks'] ? ($results[0]['looks']."分") : "";//相貌自评

			// 体重
			if($results[0]['bodyweight'] == -1){
				$detail['bodyweightName'] = '40公斤以下';
			}elseif($results[0]['bodyweight'] == -2){
				$detail['bodyweightName'] = '120公斤以上';
			}elseif($results[0]['bodyweight'] == 0){
				$detail['bodyweightName'] = "";
			}else{
				$detail['bodyweightName'] = $results[0]['bodyweight'].'公斤';
			}


			// 教育及工作
			$detail['school']          = $results[0]['school'];//毕业院校
			$detail['schoolName']      = $results[0]['school'];//毕业院校
			$detail['major']           = $results[0]['major'];//所学专业
			$detail['duties']          = $results[0]['duties'];//职务
			$detail['nature']          = $results[0]['nature'];//公司性质
			$detail['industry']        = $results[0]['industry'];//公司行业
			$detail['workstatus']      = $results[0]['workstatus'];//工作状态
			$detail['language']        = $results[0]['language'];//掌握语言

			// 家庭状况
			$detail['familyrank']      = $results[0]['familyrank'];//家庭排行
			$detail['parentstatus']    = $results[0]['parentstatus'];//父母情况
			$detail['fatherwork']      = $results[0]['fatherwork'];//父亲工作
			$detail['motherwork']      = $results[0]['motherwork'];//母亲工作
			$detail['parenteconomy']   = $results[0]['parenteconomy'];//父母经济
			$detail['parentinsurance'] = $results[0]['parentinsurance'];//父母医保

			// 爱情规划
			$detail['marriagetime']    = $results[0]['marriagetime'];//何时结婚
			$detail['datetype']        = $results[0]['datetype'];//约会方式
			$detail['othervalue']      = $results[0]['othervalue'];//希望对方看重
			$detail['weddingtype']     = $results[0]['weddingtype'];//婚礼形式
			$detail['livetogeparent']  = $results[0]['livetogeparent'];//愿与对方父母同住
			$detail['givebaby']        = $results[0]['givebaby'];//是否想要孩子
			$detail['cooking']         = $results[0]['cooking'];//厨艺状况
			$detail['housework']       = $results[0]['housework'];//家务分工

			// 择偶意向
			$detail['fromage']     = $results[0]['fromage'];//最小年龄
			$detail['toage']       = $results[0]['toage'];//最大年龄
			$detail['dfheight']    = $results[0]['dfheight'];//最低身高
			$detail['dtheight']    = $results[0]['dtheight'];//最高身高
			$detail['dfeducation'] = $results[0]['dfeducation'];//最低学历
			$detail['dteducation'] = $results[0]['dteducation'];//最高学历
			$detail['dfincome']    = $results[0]['dfincome'];//最低收入
			$detail['dtincome']    = $results[0]['dtincome'];//最高收入
			$detail['daddr']       = $results[0]['daddr'];//居住地
			$detail['dmarriage']   = $results[0]['dmarriage'];//婚姻状况
			$detail['dhousetag']   = $results[0]['dhousetag'];//购房情况
			$detail['dchild']      = $results[0]['dchild'];//子女情况

			if($results[0]['photo']){
				$detail['photo'] = $detail['phototurl'] = getFilePath($results[0]['photo']);
			}
			if($results[0]['cover']){
				$results[0]['cover'] = $detail['coverturl'] = getFilePath($results[0]['cover']);
			}
			$detail["age"]      = !empty($results[0]['birthday']) ? getBirthAge(date("Y-m-d", $results[0]['birthday'])) : 0;



			// 兴趣爱好
			$interests = $results[0]["interests"];
			$detail['interests'] = $interests;
			$interestsSelected = array();
			if(!empty($interests)){
				$interests = explode(",", $interests);
				foreach($interests as $val){
					$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_skill` WHERE `id` = $val");
					$typeResults = $dsql->dsqlOper($archives, "results");
					$name = $typeResults ? $typeResults[0]['typename'] : "";
					array_push($interestsSelected, $name);
				}
				$detail["interestsName"] = join(", ", $interestsSelected);
				$detail["interestsArr"] = $interestsSelected;
				$detail["interestsIdsArr"] = $interests;
			}else{
				$detail["interestsName"] = "";
				$detail["interestsArr"] = array();
				$detail["interestsIdsArr"] = array();
			}

			// 基本资料-----------------------------
			//地区
			global $data;
			$addrName = array();
			$addrNameMini = "";
			if($results[0]["addrid"]){
				$data = "";
				$addrName = getParentArr("site_area", $results[0]["addrid"]);
				$addrNameMini = $addrName[0]['typename'];
				$addrName = array_reverse(parent_foreach($addrName, "typename"));
			}
			$detail['addrName'] = join("", $addrName);
			$detail['addrNameMini'] = $addrNameMini;
			//学历
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["education"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["educationName"] = $typename[0]['typename'];
			//收入
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["income"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["incomeName"] = $typename[0]['typename'];
			//婚姻情况
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["marriage"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["marriageName"] = $typename[0]['typename'];


			// 小档案-----------------------------
			//民族
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["nation"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["nation"] = $results[0]["nation"];
			$detail["nationName"] = $typename[0]['typename'];
			//体型
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["bodytype"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["bodytypeName"] = $typename[0]['typename'];

			//宗教
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["religion"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["religionName"] = $typename[0]['typename'];

			//是否喝酒
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["drink"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["drinkName"] = $typename[0]['typename'];

			//是否吸烟
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["smoke"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["smokeName"] = $typename[0]['typename'];

			//作息情况
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["workandrest"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["workandrestName"] = $typename[0]['typename'];


			// 教育及工作-----------------------------
			// 所学专业
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["major"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["majorName"] = $typename[0]['typename'];

			// 职务
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["duties"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["dutiesName"] = $typename[0]['typename'];

			// 公司性质
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["nature"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["natureName"] = $typename[0]['typename'];

			// 公司行业
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["industry"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["industryName"] = $typename[0]['typename'];

			// 工作状态
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["workstatus"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["workstatusName"] = $typename[0]['typename'];

			// 掌握语言
			$language = $results[0]["language"];
			$languageSelected = array();
			$languageIdArr = array();
			if(!empty($language)){
				$language = explode(",", $language);
				foreach($language as $val){
					$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_item` WHERE `id` = $val");
					$typeResults = $dsql->dsqlOper($archives, "results");
					$name = $typeResults ? $typeResults[0]['typename'] : "";
					array_push($languageSelected, $name);
					array_push($languageIdArr, $val);
				}
				$detail["languageName"] = join(", ", $languageSelected);
				$detail["languageArr"] = $languageSelected;
				$detail["languageIdArr"] = $languageIdArr;
			}

			// 家庭状况-----------------------------
			// 排行
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["familyrank"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["familyrankName"] = $typename[0]['typename'];
			// 父母情况
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["parentstatus"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["parentstatusName"] = $typename[0]['typename'];
			// 父亲工作
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["fatherwork"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["fatherworkName"] = $typename[0]['typename'];
			// 母亲工作
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["motherwork"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["motherworkName"] = $typename[0]['typename'];
			// 父母经济
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["parenteconomy"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["parenteconomyName"] = $typename[0]['typename'];
			// 父母医保
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["parentinsurance"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["parentinsuranceName"] = $typename[0]['typename'];


			// 爱情规划-----------------------------
			// 何时结婚
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["marriagetime"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["marriagetimeName"] = $typename[0]['typename'];
			// 约会方式
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["datetype"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["datetypeName"] = $typename[0]['typename'];
			// 希望对方看重
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["othervalue"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["othervalueName"] = $typename[0]['typename'];
			// 婚礼形式
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["weddingtype"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["weddingtypeName"] = $typename[0]['typename'];
			// 愿与对方父母同住
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["livetogeparent"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["livetogeparentName"] = $typename[0]['typename'];
			// 是否想要孩子
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["givebaby"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["givebabyName"] = $typename[0]['typename'];
			// 厨艺状况
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["cooking"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["cookingName"] = $typename[0]['typename'];
			// 家务分工
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["housework"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["houseworkName"] = $typename[0]['typename'];


			// 择偶意向-----------------------------
			// 最低学历
			if($results[0]['dfeducation']){
				$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dfeducation"]);
				$typename = $dsql->getTypeName($typeSql);
				$typename = $typename[0]['typename'];
			}else{
				$typename = '不限';
			}
			$detail["dfeducationName"] = $typename;
			// 最高学历
			if($results[0]['dteducation']){
				$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dteducation"]);
				$typename = $dsql->getTypeName($typeSql);
				$typename = $typename[0]['typename'];
			}else{
				$typename = '不限';
			}
			$detail["dteducationName"] = $typename;
			// 婚姻状况
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dmarriage"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["dmarriageName"] = $typename[0]['typename'];
			// 购房情况
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dhousetag"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["dhousetagName"] = $typename[0]['typename'];
			// 子女情况
			$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dchild"]);
			$typename = $dsql->getTypeName($typeSql);
			$detail["dchildName"] = $typename[0]['typename'];

			// 地区
			global $data;
			$addrName = array();
			if($results[0]["daddr"]){
				$data = "";
				$addrName = getParentArr("site_area", $results[0]["daddr"]);
				$addrName = array_reverse(parent_foreach($addrName, "typename"));
			}
			$detail['daddrName'] = join(" ", $addrName);

			// 拼接择偶年龄
			if($results[0]['fromage'] || $results[0]['toage']){
				if($results[0]['fromage'] && !$results[0]['toage']){
					$dage = $results[0]['fromage']."岁以上";
				}elseif(!$results[0]['fromage'] && $results[0]['toage']){
					$dage = $results[0]['toage']."岁以下";
				}elseif($results[0]['fromage'] == $results[0]['toage']){
					$dage = $results[0]['fromage']."岁";
				}else{
					$dage = $results[0]['fromage']."-".$results[0]['toage']."岁";
				}
			}else{
				$dage = '';
			}
			$detail['dage'] = $dage;

			// 拼接择偶身高
			if($results[0]['dfheight'] || $results[0]['dtheight']){
				if($results[0]['dfheight'] && !$results[0]['dtheight']){
					$dheight = $results[0]['dfheight']."厘米以上";
				}elseif(!$results[0]['dfheight'] && $results[0]['dtheight']){
					$dheight = $results[0]['dtheight']."厘米以下";
				}elseif($results[0]['dfheight'] == $results[0]['dtheight']){
					$dage = $results[0]['dfheight']."厘米";
				}else{
					$dheight = $results[0]['dfheight']."-".$results[0]['dtheight']."厘米";
				}
			}else{
				$dheight = '';
			}
			$detail['dheight'] = $dheight;

			// 拼接择偶学历
			if($results[0]['dfeducation'] || $results[0]['dteducation']){
				if($results[0]['dfeducation'] && !$results[0]['dteducation']){
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dfeducation"]);
					$typename = $dsql->getTypeName($typeSql);
					$deducation = $typename[0]['typename']."以上";
				}elseif(!$results[0]['dfeducation'] && $results[0]['dteducation']){
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dteducation"]);
					$typename = $dsql->getTypeName($typeSql);
					$deducation = $typename[0]['typename']."以下";
				}elseif($results[0]['dfeducation'] == $results[0]['dteducation']){
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dfeducation"]);
					$typename = $dsql->getTypeName($typeSql);
					$deducation = $typename[0]['typename'];
				}else{
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dfeducation"]);
					$typename = $dsql->getTypeName($typeSql);
					$deducation = $typename[0]['typename'];
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dteducation"]);
					$typename = $dsql->getTypeName($typeSql);
					$deducation .= "-".$typename[0]['typename'];
				}

			}else{
				$deducation = '';
			}
			$detail['deducation'] = $deducation;

			// 拼接收入
			if($results[0]['dfincome'] || $results[0]['dtincome']){
				if($results[0]['dfincome'] && !$results[0]['dtincome']){
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dfincome"]);
					$typename = $dsql->getTypeName($typeSql);
					$dincome = $typename[0]['typename']."以上";

					$detail['dfincomeName'] = $typename[0]['typename'];
					$detail['dtincomeName'] = "不限";

				}elseif(!$results[0]['dfincome'] && $results[0]['dtincome']){
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dtincome"]);
					$typename = $dsql->getTypeName($typeSql);
					$dincome = $typename[0]['typename']."以下";

					$detail['dfincomeName'] = "不限";
					$detail['dtincomeName'] = $typename[0]['typename'];

				}elseif($results[0]['dfincome'] == $results[0]['dtincome']){
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dfincome"]);
					$typename = $dsql->getTypeName($typeSql);
					$dincome = $typename[0]['typename'];

					$detail['dfincomeName'] = $typename[0]['typename'];
					$detail['dtincomeName'] = $typename[0]['typename'];

				}else{
					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dfincome"]);
					$typename = $dsql->getTypeName($typeSql);
					$dincome = $typename[0]['typename'];
					$detail['dfincomeName'] = $typename[0]['typename'];

					$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["dtincome"]);
					$typename = $dsql->getTypeName($typeSql);
					$dincome .= "-".$typename[0]['typename'];
					$detail['dtincomeName'] = $typename[0]['typename'];
				}

			}else{
				$dincome = '不限';
				$detail['dfincomeName'] = $detail['dtincomeName'] = "不限";
			}
			$detail['dincome'] = $dincome;


			// 会员查看特权 ssssssssssssssssss
			if($lookSpecInfo){
				//子女
				$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["child"]);
				$typename = $dsql->getTypeName($typeSql);
				$detail["child"] = $results[0]["child"];
				$detail["childName"] = $typename[0]['typename'];

				//居住情况
				$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["housetag"]);
				$typename = $dsql->getTypeName($typeSql);
				$detail["housetag"] = $results[0]["housetag"];
				$detail["housetagName"] = $typename[0]['typename'];

				//购车情况
				$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = ". $results[0]["cartag"]);
				$typename = $dsql->getTypeName($typeSql);
				$detail["cartag"] = $results[0]["cartag"];
				$detail["cartagName"] = $typename[0]['typename'];

				//家乡
				global $data;
				$addrName = array();
				if($results[0]["hometown"]){
					$data = "";
					$addrName = getParentArr("site_area", $results[0]["hometown"]);
					$addrName = array_reverse(parent_foreach($addrName, "typename"));
				}
				$detail['hometown'] = $results[0]["hometown"];
				$detail['hometownName'] = join("", $addrName);

				//户口
				global $data;
				$addrName = array();
				if($results[0]["household"]){
					$data = "";
					$addrName = getParentArr("site_area", $results[0]["household"]);
					$addrName = array_reverse(parent_foreach($addrName, "typename"));
				}
				$detail['household'] = $results[0]["household"];
				$detail['householdName'] = join("", $addrName);

				// 星座
				$detail['constellation'] = $results[0]['constellation'];
				$detail['constellationName'] = $results[0]['constellation'];


				// 最后活跃时间
				if($results[0]['activedate']){
					$hide = 0;
					// 定向隐身
					if($uid != $id){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `type` = 10 AND `ufrom` = $id AND `uto` = $uid");
						$ret = $dsql->dsqlOper($sql, "results");
						if($ret){
							$hide = 1;
						}
					}
					if($hide){
						$detail['activedate'] = "";
					}else{
						$detail['activedate'] = FloorTime(GetMkTime(time()) - $results[0]['activedate']);
					}
				}else{
					$detail['activedate'] = "";
				}

				$detail['jointime']   = $results[0]['jointime'];
			}

			// 会员查看特权 eeeeeeeeeeeeee

			$v1 = $v2 = $v2_2 = $v3 = $v4 = 0;
			if($uid > -1){

				//是否已经关注
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$uid' AND `uto` = ".$results[0]['id']." AND `type` = 2");
				$v2 = $dsql->dsqlOper($sql, "totalCount");

				//是否已经打过招呼
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$uid' AND `uto` = ".$results[0]['id']." AND `type` = 3");
				$v3 = $dsql->dsqlOper($sql, "totalCount");

				//是否已经喜欢
				// $sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$uid' AND `uto` = ".$results[0]['id']." AND `type` = 4");
				// $v4 = $dsql->dsqlOper($sql, "totalCount");

				//是否已经被关注
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `uto` = '$uid' AND `ufrom` = ".$results[0]['id']." AND `type` = 2");
				$v2_2 = $dsql->dsqlOper($sql, "totalCount");
			}
			// $detail['visit'] = $v1;
			$detail['follow'] = $v2;
			$detail['followby'] = $v2_2;
			$detail['meet'] = $v3;
			$detail['like'] = $v2;

			// 他关注人数
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = ".$results[0]['id']." AND `type` = 2");
			$count = $dsql->dsqlOper($sql, "totalCount");
			$detail['o_follow'] = $count;

			// 关注他的人数
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `uto` = ".$results[0]['id']." AND `type` = 2");
			$count = $dsql->dsqlOper($sql, "totalCount");
			$detail['o_followby'] = $count;


			$param = array(
				"service" => "dating",
				"template" => "u",
				"id" => $id
			);
			$url = getUrlPath($param);
			$detail['url'] = $url;

			// 加密输出联系方式
			$qq = $results[0]['qq'];
			$wechat = $results[0]['wechat'];
			$phone = $sysUser['phone'];

			if($qq){
				$qq = substr($qq, 0, 3)."*****".substr($qq, -2);
				$detail['qq_enc'] = $qq;
			}
			if($wechat){
				$wechat = substr($wechat, 0, 3)."*****".substr($wechat, -2);
				$detail['wechat_enc'] = $wechat;
			}
			if($phone){
				$phone = substr($phone, 0, 3)."****".substr($phone, -4);
				$detail['phone_enc'] = $phone;
			}

			return $detail;
		}else{
			return array("state" => 200, "info" => '会员不存在！');
		}
	}

	/**
	 * 红娘列表
	 */
	public function hnList(){
		global $dsql;
		global $userLogin;

		$company  = (int)$this->param['company'];
		$type     = (int)$this->param['type'];
		$keywords = $this->param['keywords'];
		$page     = $this->param['page'];
		$pageSize = $this->param['pageSize'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$uid = $userLogin->getMemberID();

		$pageinfo = $list = array();

		//获取登录会员的交友红娘ID
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = $type AND `userid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
		}else{
			$uid = 0;
		}

		$where = " WHERE `type` = 1";

		if($company){
			$where .= " AND `company` = $company";
		}
		if($keywords){
			$where .= " AND (`nickname` LIKE '%$keywords%' || `tel` LIKE '%$keywords%')";
		}
		if($uid != $company){
			$where .= " AND `state` = 1";
		}

		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` $where");
		// echo $archives;
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
		$where .= " LIMIT $atpage, $pageSize";

		$archives = $dsql->SetQuery("SELECT `id`, `nickname`, `year`, `case`, `advice`, `photo`, `tel` FROM `#@__dating_member` $where");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				$list[$key]['id'] = $value['id'];
				$list[$key]['nickname'] = $value['nickname'];
				$list[$key]['year'] = $value['year'];
				$list[$key]['case'] = $value['case'];
				$list[$key]['advice'] = $value['advice'];
				$list[$key]['phone'] = $value['tel'];

				$photo                   = $value['photo'] ? getFilePath($value['photo']) : "";
				$list[$key]['phototurl'] = $photo;
				$list[$key]['photo']     = $photo;

				$param = array(
					"service" => "dating",
					"template" => "hn_detail",
					"id" => $value["id"]
				);
				$list[$key]['url'] = getUrlPath($param);

				$param = array(
					"service" => "dating",
					"template" => "my_user",
					"id" => $value['id']
				);
				$list[$key]['userUrl'] = getUrlPath($param);
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
     * 红娘信息
     * @return array
     */
	public function hnInfo(){
		global $dsql;
		global $userLogin;
		$id = $this->param;
		$id = is_array($this->param) ? $id['id'] : $id;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$userid = $userLogin->getMemberID();

		$detail = array();

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_member` WHERE `id` = ".$id." AND `type` = 1 AND ( (`state` = 1 AND `userid` != $userid) || `userid` = $userid )");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$results = $results[0];

			$detail['id']       = $results['id'];
			$detail['cityid']   = $results['cityid'];
			$detail['type']     = $results['type'];
			$detail['nickname'] = $results['nickname'];
			$detail['profile']  = $results['profile'];
			$detail['year']     = $results['year'];
			$detail['case']     = $results['case'];
			$detail['year']     = $results['year'];
			$detail['qq']       = $results['qq'];
			$detail['wechat']   = $results['wechat'];
			$detail['tel']      = $results['tel'];
			$detail['honor']    = $results['honor'];
			$detail['advice']   = $results['advice'];
			$detail['address']  = $results['address'];
			$detail['company']  = $results['company'];
			$detail['state']    = $results['state'];
			$detail['tags']     = $results['tags'];

			//标签
			$tags = $results["tags"];
			$tagsArr = $tags ? explode(",", $tags) : array();
			$detail['tagsArr']  = $tagsArr;

			// 所属门店
			if($results['company']){
				$this->param = $results['company'];
				$store = $this->storeInfo();
			}else{
				$store = "";
			}
			$detail['store']    = $store;

			if($userid == $results['userid']){
				$detail['photo_']     = $results['photo'];
				$detail['cover_']     = $results['cover'];
				$detail['phone']      = $results['phone'];
				$detail['phoneCheck'] = $results['phoneCheck'];
			}

			$detail['photo']     = $detail['phototurl'] = $results['photo'] ? getFilePath($results['photo']) : "";
			$detail['coverturl'] = $results['cover'] ? getFilePath($results['cover']) : "";

			$param = array(
				"service" => "dating",
				"template" => "hn_detail",
				"id" => $id
			);
			$detail['url'] = getUrlPath($param);

			return $detail;
		}
	}

	/**
	 * 门店列表
	 */
	public function storeList(){
		global $dsql;
		global $userLogin;

		$cityid   = (int)$this->param['cityid'];
		$page     = $this->param['page'];
		$pageSize = $this->param['pageSize'];
		$keywords = $this->param['keywords'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$uid = $userLogin->getMemberID();

		$pageinfo = $list = array();

		//获取登录会员的交友ID
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = 2 AND `userid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
		}else{
			$uid = 0;
		}

		$where = " WHERE `type` = 2 AND `state` = 1";

		if($cityid){
			$where .= " AND `cityid` = $cityid";
		}

		if($keywords){
			$where .= " AND (`nickname` LIKE '%$keywords%' || `tel` LIKE '%$keywords%')";
		}

		$archives = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__dating_member` $where");
		$results = $dsql->dsqlOper($archives, "results");

		//总条数
		$totalCount = $results[0]['total'];
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
		$where .= " LIMIT $atpage, $pageSize";

		$archives = $dsql->SetQuery("SELECT `id`, `nickname`, `lng`, `lat`, `tel`, `photo`, `profile`, `tags`, `address`, `cover`, `cityid` FROM `#@__dating_member` $where");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				$list[$key]['id'] = $value['id'];
				$list[$key]['nickname'] = $value['nickname'];
				$list[$key]['lng'] = $value['lng'];
				$list[$key]['lat'] = $value['lat'];
				$list[$key]['tags'] = $value['tags'];
				$list[$key]['address'] = $value['address'];
				$list[$key]['tel'] = $value['tel'];
				$list[$key]['profile'] = $value['profile'];
				$list[$key]['photo'] = $value['photo'];
				$list[$key]['phototurl'] = $value['photo'] ? getFilePath($value['photo']) : "";
				$list[$key]['coverturl'] = $value['cover'] ? getFilePath($value['cover']) : "";

				if($value['cityid']){
					$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__site_area` WHERE `id` = ".$value['cityid']);
					$res = $dsql->dsqlOper($sql, "results");
					if($res){
						$list[$key]['cityname'] = $res[0]['typename'];
					}
				}

				$tags = $value['tags'];
				if($tags){
					$list[$key]['tags'] = explode(',', $tags);
				}else{
					$list[$key]['tags'] = array();
				}

				$param = array(
					"service" => "dating",
					"template" => "store_detail",
					"id" => $value["id"]
				);
				$list[$key]['url'] = getUrlPath($param);

				$sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__dating_member` WHERE `company` = ".$value['id']." AND `type` = 1");
				$res = $dsql->dsqlOper($sql, "results");
				$list[$key]['team'] = $res[0]['total'];

			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 门店信息
     * @return array
     */
	public function storeInfo(){
		global $dsql;
		global $userLogin;
		$id = $this->param;

		$userid = $userLogin->getMemberID();

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		if($userid != $id){
			$where .= " AND `state` = 1";
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_member` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			if($userid == $results[0]['userid']){
				$results[0]['photo_'] = $results[0]['photo'];
				$results[0]['cover_'] = $results[0]['cover'];
			}

			if($userid != $results[0]['userid']){
				unset($results[0]['phone']);
				unset($results[0]['phoneCheck']);
			}


			//地区
			// global $data;
			// $addrName = array();
			// if($results[0]["addrid"]){
			// 	$data = "";
			// 	$addrName = getParentArr("datingaddr", $results[0]["addrid"]);
			// 	$addrName = array_reverse(parent_foreach($addrName, "typename"));
			// }
			// $results[0]['addre'] = join(" ", $addrName);
			if($results[0]['lng'] == '') $results[0]['lng'] = 0;
			if($results[0]['lat'] == '') $results[0]['lat'] = 0;

			$results[0]['phototurl'] = $results[0]['photo'] ? getFilePath($results[0]['photo']) : "";
			$results[0]['coverturl'] = $results[0]['cover'] ? getFilePath($results[0]['cover']) : "";

			//标签
			$tags = $results[0]["tags"];
			$results[0]["tagsArr"] = $tags ? explode(",", $tags) : array();

			$param = array(
				"service" => "dating",
				"template" => "store_detail",
				"id" => $id
			);
			$results[0]['url'] = getUrlPath($param);

			$param = array(
				"service" => "dating",
				"template" => "my_user",
				"id" => $id
			);
			$results[0]['userUrl'] = getUrlPath($param);

			return $results[0];
		}else{
			return array("state" => 200, "info" => '红娘不存在！');
		}
	}


	/**
     * 相册分类
     * @return array
     */
	public function albumType(){
		global $dsql;
		$userid = !empty($this->param) ? $this->param : 0;
		if(!is_numeric($userid)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_album_type` WHERE `uid` = ".$userid);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach($results as $key => $val){
				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__dating_album` WHERE `uid` = $userid AND `atype` = ".$results[$key]['id']);
				$results[$key]['count'] = $dsql->dsqlOper($archives, "totalCount");
			}
			return $results;
		}else{
			return array("state" => 200, "info" => '分类不存在！');
		}
		die;
	}


	/**
     * 相册分类详细信息
     * @return array
     */
	public function albumTypeInfo(){
		global $dsql;
		$userid = $typeid = "";
		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$userid      = $this->param['userid'];
				$typeid      = $this->param['typeid'];
			}
		}
		if(empty($userid) || empty($typeid)){
			return array("state" => 200, "info" => '必传参数不完整！');
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_album_type` WHERE `id` = ".$typeid);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__dating_album` WHERE `uid` = $userid AND `atype` = ".$results[0]['id']);
			$results[0]['count'] = $dsql->dsqlOper($archives, "totalCount");
			return $results;
		}else{
			return array("state" => 200, "info" => '分类不存在！');
		}
		die;
	}


	/**
     * 相册列表
     * @return array
     */
	public function albumList(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$u = $userid = $typeid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$u           = $this->param['u'];
				$userid      = $this->param['userid'];
				$isdating    = (int)$this->param['isdating'];
				$typeid      = $this->param['typeid'];
				$page        = $this->param['page'];
				$pageSize    = $this->param['pageSize'];
			}
		}

		$wh = "";
		$orderby = " ORDER BY `id` DESC";

		$loginUid = $userLogin->getMemberID();

		if($loginUid == -1) return array("state" => 200, "info" => '登陆超时，请重新登陆');

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $loginUid AND `type` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => 'error');
		}

		// 传入的userid是交友会员id
		if($userid){
			if($isdating){
				$ouid = $userid;
				$userid = $this->getSysUid($userid);
			}else{
				$ouid = $this->getDatingUid($userid);
			}
			if($userid == $loginUid){
				$u = 1;
			}
		}

		if($u){
			$userid = $loginUid;
			$auth_album = -1;
		}else{
			if(empty($userid)) return array("state" => 200, "info" => '必传参数不完整！');
			$wh .= " AND `state` = 1";

			// 隐藏相册
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `type` = 12 AND `uto` = $uid AND `ufrom` = $ouid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$where .= " AND 1 = 2";
			}


			$levelCfg = $this->getMemberLevelInfo($loginUid, true);
			if($levelCfg !== false){
				$privilege = $levelCfg['privilege'];
				$levelId = $privilege['id'];
				$auth_album = $privilege['album'];
				// 会员设置为0时表示不限制
				if($levelId != 1 && $auth_album == 0){
					$auth_album = -1;
				}
			}else{
				$auth_album = -1;//不限制
			}
		}


		if(!empty($typeid)){
			$wh = " AND `atype` = ".$typeid;
		}

		$where .= " AND `uid` = ".$userid.$wh;

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `path`, `note`, `pubdate`, `state`, `zan_user` FROM `#@__dating_album` WHERE 1 = 1".$where.$orderby);

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

		// 限制查看数量
		if($auth_album != -1){
			$count_before = $atpage;
		}else{
			$count_before = 0;
		}

		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();

		if($results){

			foreach($results as $key => $val){
				$list[$key]['id'] = $val['id'];

				// 是否点赞
				$zan_has = 0;
				$zan_user = $val['zan_user'];
				if($zan_user){
					$zan_user = explode(",", $zan_user);
					if(in_array($loginUid, $zan_user)){
						$zan_has = 1;
					}
				}

				if($auth_album != -1 && $count_before + $key + 1 > $auth_album){
					$list[$key]['limit'] = 1;
					$list[$key]['turl'] = '';
					$list[$key]['path'] = '';
					$list[$key]['note'] = '';
				}else{
					$list[$key]['limit'] = 0;
					$list[$key]['turl'] = $val["path"];
					$list[$key]['path'] = getFilePath($val["path"]);
					$list[$key]['note'] = $val['note'];
					$list[$key]['zan_has'] = $zan_has;
				}

				$list[$key]['pubdtae'] = $val['pubdtae'];
				if($u){
					$list[$key]['state'] = $val['state'];
				}
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 上传照片
     * @return array
     */
	public function uploadAlbum(){
		global $dsql;
		global $userLogin;
		$img = $this->param['img'];
		$uid = $userLogin->getMemberID();

		if(empty($img)) return array("state" => 200, "info" => '请选择要上传的图片！');
		if($uid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		if(is_string($img)){
			$img = explode(",", $img);
		}
		foreach ($img as $key => $val) {
			$value = explode("|", $val);
			$pic = $value[0];
			$note = $value[1];
			if(!empty($note)){
				$note = cn_substrR(filterSensitiveWords(addslashes($note)), 200);
			}

			$sql = $dsql->SetQuery("INSERT INTO `#@__dating_album` (`uid`, `atype`, `path`, `note`, `state`, `pubdate`, `zan`, `zan_user`) VALUES ('$uid', 0, '$pic', '$note', 1, ".GetMkTime(time()).", '0', '')");
			$dsql->dsqlOper($sql, "update");
		}

		return "上传成功！";
	}


	/**
     * 删除照片
     * @return array
     */
	public function albumDel(){
		global $dsql;
		global $userLogin;
		$id = $this->param['id'];
		$uid = $userLogin->getMemberID();
		$where = "";

		if($uid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');
		if(empty($id)) return array("state" => 200, "info" => '参数错误');

		$where .= " AND `id` IN (".$id.")";

		$archives = $dsql->SetQuery("SELECT `id`, `path` FROM `#@__dating_album` WHERE `uid` = $uid".$where);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				//删除缩略图
				delPicFile($value['path'], "delAtlas", "dating");
				$sql = $dsql->SetQuery("DELETE FROM `#@__dating_album` WHERE `id` = ".$value['id']);
				$dsql->dsqlOper($sql, "update");
			}
			return "删除成功！";

		}else{
			return array("state" => 200, "info" => '删除失败！');
		}
	}


	/**
     * 修改照片
     * @return array
     */
	public function albumEdit(){
		global $dsql;
		global $userLogin;
		$id = $this->param['id'];
		$note = $this->param['note'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		if($uid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		if(!empty($note)){
			$note = cn_substrR(filterSensitiveWords(addslashes($note)), 200);
		}

		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__dating_album` WHERE `uid` = $uid AND `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$sql = $dsql->SetQuery("UPDATE `#@__dating_album` SET `note` = '$note' WHERE `id` = ".$results[0]['id']);
			$dsql->dsqlOper($sql, "update");

			return "修改成功！";

		}else{
			return array("state" => 200, "info" => '修改失败！');
		}
	}



	/**
     * 照片详细信息
     * @return array
     */
	public function albumInfo(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_album` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results[0]["path"] = getFilePath($results[0]["path"]);
			return $results;
		}
	}


	/**
     * 照片评论
     * @return array
     */
	public function albumCommon(){
		global $dsql;
		$pageinfo = $list = array();
		$albumidalbumid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$albumid     = $this->param['albumid'];
				$page        = $this->param['page'];
				$pageSize    = $this->param['pageSize'];
			}
		}

		if(empty($albumid)){
			return array("state" => 200, "info" => '必传参数不完整！');
		}

		$where = " WHERE `aid` = $albumid";

		$orderby = " ORDER BY `id` DESC";

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `uid`, `type`, `content`, `pubdate` FROM `#@__dating_album_review`".$where.$orderby);

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

		return array("pageInfo" => $pageinfo, "list" => $results);
	}

	/**
     * 照片点赞
     * @return array
     */
	public function albumDing(){
		global $dsql;
		global $userLogin;
		$id = (int)$this->param['id'];

		$userid = $userLogin->getMemberID();

		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		if(empty($id)){
			return array("state" => 200, "info" => '必传参数不完整！');
		}

		$where = " WHERE `id` = $id";

		$sql = $dsql->SetQuery("SELECT `id`, `zan_user` FROM `#@__dating_album` WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$zan_user = $ret[0]['zan_user'];
			if($zan_user){
				$zan_user = explode(",", $zan_user);
				if(in_array($userid, $zan_user)){
					$zan_user = array_diff($zan_user, array($userid));
				}else{
					$zan_user[] = $userid;
				}
			}else{
				$zan_user = array($userid);
			}
			$count = count($zan_user);

			$sql = $dsql->SetQuery("UPDATE `#@__dating_album` SET `zan` = '$count', `zan_user` = '".join(",", $zan_user)."' WHERE `id` = $id");
			$ret = $dsql->dsqlOper($sql, "update");
			if($ret == "ok"){
				return "操作成功";
			}else{
				return array("state" => 200, "info" => $sql);
			}
		}

	}


	/**
     * 会员私信
     * @return array
     */
	public function review(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$userid = $read = $page = $pageSize = $where = "";

		$userid = $userLogin->getMemberID();

		if(empty($userid)) return array("state" => 200, "info" => '登录超时，请重新登录！');

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uids = array();
			foreach ($ret as $key => $value) {
				$uids[] = $value['id'];
			}
			$where .= " AND ( ( `from` IN (".join(",", $uids).") AND `delfrom` = 0 ) || ( `to` IN (".join(",", $uids).")  AND `delto` = 0 ) ) ";
		}else{
			return array("state" => 200, "info" => '暂无相关信息！');
		}


		$reviewId = $param['reviewId'];
		$master   = $param['master'];
		$visitor  = $param['visitor'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_chat` WHERE `pid` = 0".$where." ORDER BY `date` DESC");

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
		$noread = 0;
		if($results){
			$userList = array();
			foreach ($results as $key => $value) {
				$isread = $value['isread'];
				$pubdate = $value['date'];
				$note = $value['msg'];

				$my = 0;
				$user = array();

				if(in_array($value['from'], $uids)){
					$isread = -1;
					$my = $value['from'];

					if(isset($userList[$value['to']])){
						$user = $userList[$value['to']];
					}else{
						$sql = $dsql->SetQuery("SELECT `type`, `userid` FROM `#@__dating_member` WHERE `id` = ".$value['to']);
						$ret = $dsql->dsqlOper($sql, "results");
						if($ret){
							$this->param = $value['to'];
							if($ret[0]['type'] == 0){
								$user = $this->memberInfo(true);
							}elseif($ret[0]['type'] == 1){
								$user = $this->hnInfo(true);
							}
							$user['userid'] = $ret[0]['userid'];
							$userList[$value['to']] = $user;
						}else{
							$userList[$value['to']] = "";
						}
					}
				}else{
					$my = $value['to'];
					if(isset($userList[$value['from']])){
						$user = $userList[$value['from']];
					}else{
						$sql = $dsql->SetQuery("SELECT `type`, `userid` FROM `#@__dating_member` WHERE `id` = ".$value['from']);
						$ret = $dsql->dsqlOper($sql, "results");
						if($ret){
							$this->param = $value['from'];
							if($ret[0]['type'] == 0){
								$user = $this->memberInfo(true);
							}elseif($ret[0]['type'] == 1){
								$user = $this->hnInfo(true);
							}
							$user['userid'] = $ret[0]['userid'];
							$userList[$value['from']] = $user;
						}else{
							$userList[$value['from']] = "";
						}
					}
				}

				if(!$user){
					$user = array(
						'id' => 0,
						'type' => 0,
						'nickname' => '用户不存在或已被删除',
						'photo' => '',
						'phototurl' => '',
						'level' => 0,
						'certifyState' => 0,
					);
				}
				$list[$key]['user'] = $user;


				$isread = 1;
				//获取最后一条信息
				$sql = $dsql->SetQuery("SELECT `msg`, `from`, `to`, `isread`, `date` FROM `#@__dating_chat` WHERE `pid` = ".$value['id']." ORDER BY `id` DESC LIMIT 0,1");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$note = $ret[0]['msg'];
					$pubdate = $ret[0]['date'];
					// 最后一条是对方发的
					if(in_array($ret[0]['to'], $uids)){
						$isread = $ret[0]['isread'];
					}
					// 查询是否有未读消息
					if($isread){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE `pid` = ".$value['id']." AND `isread` = 0 AND `to` IN (".join(",", $uids).") LIMIT 0,1");
						$ret = $dsql->dsqlOper($sql, "results");
						if($ret){
							$isread = 0;
						}
					}
				}
				if($isread == 0){
					$noread++;
				}

				$pubdate = $pubdate > 1000000000 ? ceil($pubdate / 1000) : $pubdate;
				$list[$key]['info'] = array(
					"id"      => $value['id'],
					"my"      => $my,
					"isread"  => $isread,
					"note"    => $note,
					"pubdate" => $pubdate,
					"time"    => date("H:i", $pubdate),
				);
			}
		}

		$pageinfo['noread'] = $noread;

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 私信详情
     * @return array
     */
	public function reviewDetail(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$userid = $id = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$userid      = $this->param['userid'];
				$id          = $this->param['id'];
				$page        = $this->param['page'];
				$pageSize    = $this->param['pageSize'];
			}
		}

		if(empty($id)) return array("state" => 200, "info" => '格式错误！');

		if(empty($userid)){
			$uid = $userLogin->getMemberID();
			if($uid == -1){
				return array("state" => 200, "info" => '登录超时，请重新登录！');
			}else{
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $uid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$userid = $ret[0]['id'];
				}else{
					return array("state" => 200, "info" => '暂无相关信息！');
				}
			}
		}

		$where = " WHERE `rid` = $id AND (`from` = $userid OR `to` = $userid)";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_review_list`".$where." ORDER BY `id` DESC");

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
		if($results){
			$results = array_reverse($results);  //为了实现聊天效果，记录需倒序输出
			foreach ($results as $key => $value) {
				$list[$key]['from'] = $value['from'];
				$list[$key]['to'] = $value['to'];
				$list[$key]['content'] = $value['content'];
				$list[$key]['pubdate'] = $value['pubdate'];
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
	 * 发送私信验证
	 */
	public function sendChatCheck(){
		global $dsql;

		$from = (int)$this->param['from'];
		$to   = (int)$this->param['to'];

		if(empty($from) || empty($to)) return array("state" => 200, "info" => '参数错误');

		$sql = $dsql->SetQuery("SELECT `id`, `type`, `level` FROM `#@__dating_member` WHERE `id` = $from || `id` = $to");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			if(count($ret) != 2) return array("state" => 200, "info" => '用户信息错误');
			if($ret[0]['id'] == $from){
				$ufrom = $ret[0];
				$uto = $ret[1];
			}else{
				$ufrom = $ret[1];
				$uto = $ret[0];
			}
			// 判断是否已存在聊天
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE ( (`from` = $from AND `to` = $to) || (`from` = $to || `to` = $from) ) AND `pid` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				return array("state" => 200, "info" => '操作异常');
			}
			$pid = $ret[0]['id'];

			// 都是用户身份
			if($ufrom['type'] == 0 && $uto['type'] == 0){
				$ufrom_level = $ufrom['level'] ? $ufrom['level'] : 1;

				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE `pid` = $pid AND `from` = '$from'");
				$count = $dsql->dsqlOper($sql, "totalCount");

				$sql = $dsql->SetQuery("SELECT `id`, `privilege` FROM `#@__dating_level` WHERE `id` = $ufrom_level");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$privilege = $ret[0]['privilege'];
					$privilege = unserialize($privilege);
					if($ufrom_level > 1 && $privilege['chat'] == 0){
						return $pid;
					}
					if($count >= $privilege['chat']){
						return array("state" => 200, "info" => "您的聊天条数已达上限，请升级会员");
					}else{
						return $pid;
					}
				}else{
					return array("state" => 200, "info" => '用户信息错误');
				}
			}else{
				return $pid;
			}

		}else{
			return array("state" => 200, "info" => '用户信息错误');
		}

	}

	/**
	 * 创建私信
	 */
	public function createReview(){
		global $dsql;
		global $userLogin;

		$id     = $this->param['id'];
		$type   = (int)$this->param['type'];
		$userid = $userLogin->getMemberID();

		if($userid ==  -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		$sql = $dsql->SetQuery("SELECT `id`, `type`, `key`, `key_buy` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = $type");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid     = $ret[0]['id'];
			$utype   = $ret[0]['type'];
			$key     = $ret[0]['key'];
			$key_buy = $ret[0]['key_buy'];
		}else{
			return array("state" => 200, "info" => 'error');
		}

		if($uid == $id){
			return array("state" => 200, "info" => '自己给自己发私信多没意思呀~');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE (`from` = $uid AND `to` = $id) OR (`from` = $id AND `to` = $uid)");

		$sql = $dsql->SetQuery("SELECT `type`, `dateswitch`, `state` FROM `#@__dating_member` WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$tutype = $ret[0]['type'];
			$tdateswitch = $ret[0]['dateswitch'];
			$tstate = $ret[0]['state'];
			if($tutype == 0){
				if(!$tdateswitch) return array("state" => 200, "info" => '用户当前状态无法发送消息！');
				if(!$tstate) return array("state" => 200, "info" => '用户状态异常！');
			}
		}


		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE (`from` = $uid AND `to` = $id) OR (`from` = $id AND `to` = $uid)");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			return array("state" => 200, "info" => '聊天已存在~');
		}

		$useKey = 0;
		if($utype == 0 && $tutype == 0){
			$useKey = 1;
			if($key == 0 && $key_buy == 0){
				return array("state" => 200, "info" => '您还没有聊天钥匙或已用完，请先购买');
			}
		}

		$archives = $dsql->SetQuery("INSERT INTO `#@__dating_chat` (`from`, `to`, `date`, `msg`) VALUES ('$uid', '$id', ".GetMkTime(time()).", '')");
		$ret = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($ret)){
			if($useKey){
				$key_f = $key > 0 ? 'key' : 'key_buy';
				$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `".$key_f."` = `".$key_f."` - 1 WHERE `id` = $uid");
				$dsql->dsqlOper($sql, "update");
			}
			return array("state" => 100, "info" => '创建聊天成功');
		}else{
			return array("state" => 200, "info" => '创建聊天失败');
		}
	}



	/**
     * 发私信
     * @return array
     */
	public function fabuReview(){
		global $dsql;
		global $userLogin;

		$note   = cn_substrR(filterSensitiveWords(addslashes($this->param['note'])), 200);
		$id     = $this->param['id'];
		$userid = $userLogin->getMemberID();

		if(!empty($id) && $userid > -1){

			//获取登录会员的交友ID
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = 0 AND `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => 'error');
			}

			if($uid == $id){
				return array("state" => 200, "info" => '自己给自己发私信多没意思呀~');
			}

			//判断是否已经有对话
			$sql = $dsql->SetQuery("SELECT `id`, `ufrom`, `uto` FROM `#@__dating_review` WHERE (`ufrom` = $uid AND `uto` = $id) OR (`ufrom` = $id AND `uto` = $uid)");
			$ret = $dsql->dsqlOper($sql, "results");

			//如果已经有过对话，只需要增加对话内容
			if($ret){
				$rid   = $ret[0]['id'];
				$ufrom = $ret[0]['ufrom'];
				$uto   = $ret[0]['uto'];

				$newfile = $uid == $ufrom ? "isread1" : "isread2";

				//更新私信状态为未读
				$sql = $dsql->SetQuery("UPDATE `#@__dating_review` SET `pubdate` = ".GetMkTime(time()).", `".$newfile."` = 0 WHERE `id` = $rid");
				$dsql->dsqlOper($sql, "update");

				$archives = $dsql->SetQuery("INSERT INTO `#@__dating_review_list` (`rid`, `from`, `to`, `content`, `isread`, `pubdate`) VALUES ('$rid', '$uid', '$id', '$note', 0, ".GetMkTime(time()).")");
				$ret = $dsql->dsqlOper($archives, "update");

			//如果还没有过对话，需要增加对话记录及对话内容
			}else{

				//return array("state" => 200, "info" => '发送失败，聊天尚未创建！');

				$archives = $dsql->SetQuery("INSERT INTO `#@__dating_review` (`ufrom`, `uto`, `pubdate`) VALUES ('$uid', '$id', ".GetMkTime(time()).")");
				$ret = $dsql->dsqlOper($archives, "lastid");

				if(is_numeric($ret)){
				 	$archives = $dsql->SetQuery("INSERT INTO `#@__dating_review_list` (`rid`, `from`, `to`, `content`, `isread`, `pubdate`) VALUES ('$ret', '$uid', '$id', '$note', 0, ".GetMkTime(time()).")");
				 	$ret = $dsql->dsqlOper($archives, "update");
				}else{
				 	$ret = "false";
				}

			}

			if($ret == "ok"){
				return "ok";
			}else{
				return array("state" => 200, "info" => '发送失败，请稍后重试！');
			}
		}
	}


	/**
		* 删除私信
		* @return array
		*/
	public function delReview(){
		global $dsql;
		global $userLogin;

		$id = $this->param['id'];

		if(empty($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uids = array();
			foreach ($ret as $key => $value) {
				$uids[] = $value['id'];
			}
		}else{
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$ids = explode(",", $id);
		foreach ($ids as $key => $value) {
			$archives = $dsql->SetQuery("SELECT `id`, `from`, `to` `delfrom`, `delto` FROM `#@__dating_chat` WHERE `id` = $value AND (`from` IN (".join(",", $uids).") OR `to` IN (".join(",", $uids)."))");
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$del = 0;
				// 发起方
				if(in_array($results[0]['from'], $uids)){
					if($results[0]['delto']){
						$del = 1;
					}else{
						$sql = $dsql->SetQuery("UPDATE `#@__dating_chat` SET `delfrom` = 1 WHERE `id` = ".$value);
					}
				}else{
					if($results[0]['delfrom']){
						$del = 1;
					}else{
						$sql = $dsql->SetQuery("UPDATE `#@__dating_chat` SET `delto` = 1 WHERE `id` = ".$value);
					}
				}
				if($del){
					$archives = $dsql->SetQuery("DELETE FROM `#@__dating_chat` WHERE `id` = ".$value);
					$dsql->dsqlOper($archives, "update");

					$archives = $dsql->SetQuery("DELETE FROM `#@__dating_chat` WHERE `pid` = ".$value);
					$dsql->dsqlOper($archives, "update");
				}else{
					$dsql->dsqlOper($sql, "update");
				}
			}
		}
		return '删除成功！';

	}

	/**
	 * 私信标记为已读
	 */
	public function readReview(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$id = (int)$this->param['id'];
		if($id){
			$sql = $dsql->SetQuery("SELECT `from`, `to` FROM `#@__dating_chat` WHERE `id` = $id");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$from = $ret[0]['from'];
				$to = $ret[0]['to'];

				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND (`id` = $from || `id` = $to)");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$sql = $dsql->SetQuery("UPDATE `#@__dating_chat` SET `isread` = 1 WHERE (`id` = $id OR (`pid` = $id AND `to` = ".$ret[0]['id'].") )" );
					$ret = $dsql->dsqlOper($sql, "update");
					if($ret == "ok"){
						return "操作成功";
					}else{
						return array("state" => 200, "info" => "操作失败");
					}
				}else{
					return array("state" => 200, "info" => "权限不足，操作失败");
				}
			}else{
				return array("state" => 200, "info" => "信息不存在或已删除");
			}
		}

	}


	/**
     * 成功故事
     * @return array
     */
	public function story(){
		global $dsql;
		$pageinfo = $list = array();
		$process = $tags = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$process     = $this->param['process'];
				$tags        = $this->param['tags'];
				$page        = $this->param['page'];
				$pageSize    = $this->param['pageSize'];
			}
		}

		$where = " WHERE `state` = 1";

		//进程
		if($process != ""){
			$where .= " AND `process` = ".$process;
		}

		//标签
		if(!empty($tags)){
			$where .= " AND FIND_IN_SET('".$tags."', `tags`)";
		}

		$orderby = " ORDER BY `id` desc";

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `fid`, `tid`, `litpic`, `process`, `kdate`, `title`, `pubdate`, `content`, `tags` FROM `#@__dating_story`".$where.$orderby);

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

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']      = $val['id'];
				$list[$key]['fid']     = $val['fid'];

				$archives = $dsql->SetQuery("SELECT `nickname`, `photo`, `birthday`, `sex`, `addr` FROM `#@__member` WHERE `id` = ".$val['fid']);
				$userResults = $dsql->dsqlOper($archives, "results");
				if($results){
					$list[$key]["fnickname"] = $userResults[0]['nickname'];
					$list[$key]["fphoto"] = getFilePath($userResults[0]["photo"]);
					$list[$key]["fage"] = !empty($userResults[0]['birthday']) ? getBirthAge(date("Y-m-d", $userResults[0]['birthday'])) : "";
					$list[$key]["fsex"] = $userResults[0]['sex'];

					//地区
					if(!empty($userResults[0]["addr"])){
						$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__datingaddr` WHERE `id` = ". $userResults[0]["addr"]);
						$typename = $dsql->getTypeName($typeSql);
						$list[$key]["faddr"] = $typename[0]['typename'];
					}
				}

				$list[$key]['tid']     = $val['tid'];

				if($val['tid'] != 0){
					$archives = $dsql->SetQuery("SELECT `nickname`, `photo`, `birthday`, `sex`, `addr` FROM `#@__member` WHERE `id` = ".$val['tid']);
					$userResults = $dsql->dsqlOper($archives, "results");
					if($results){
						$list[$key]["tnickname"] = $userResults[0]['nickname'];
						$list[$key]["tphoto"] = getFilePath($userResults[0]["photo"]);
						$list[$key]["tage"] = !empty($userResults[0]['birthday']) ? getBirthAge(date("Y-m-d", $userResults[0]['birthday'])) : "";
						$list[$key]["tsex"] = $userResults[0]['sex'];

						//地区
						if(!empty($userResults[0]["addr"])){
							$typeSql = $dsql->SetQuery("SELECT `typename` FROM `#@__datingaddr` WHERE `id` = ". $userResults[0]["addr"]);
							$typename = $dsql->getTypeName($typeSql);
							$list[$key]["taddr"] = $typename[0]['typename'];
						}
					}
				}

				$list[$key]["litpic"]  = getFilePath($val["litpic"]);
				$list[$key]['process'] = $val['process'];
				$list[$key]['kdate']   = $val['kdate'];
				$list[$key]['title']   = $val['title'];
				$list[$key]['pubdate'] = $val['pubdate'];
				$list[$key]['content'] = cn_substrR(strip_tags($val['content']), 100);

				$tagsArr = array();
				$tags = explode(",", $val['tags']);
				foreach ($tags as $k => $v) {
					if($v){
						$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = $v");
						$ret = $dsql->dsqlOper($sql, "results");
						if($ret){
							array_push($tagsArr, $ret[0]['typename']);
						}
					}
				}
				$list[$key]['tags'] = $tagsArr;

				//URL
				$param = array(
					"service"  => "dating",
					"template" => "story",
					"id"       => $val['id']
				);
				$list[$key]["url"] = getUrlPath($param);
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 故事详情
     * @return array
     */
	public function storyDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_story` WHERE `id` = $id AND `state` = 1");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$archives = $dsql->SetQuery("SELECT `nickname`, `photo`, `birthday`, `sex`, `addr` FROM `#@__member` WHERE `id` = ".$results[0]['fid']);
			$userResults = $dsql->dsqlOper($archives, "results");
			if($results){
				$results[0]["fnickname"] = $userResults[0]['nickname'];
				$results[0]["fphoto"] = getFilePath($userResults[0]["photo"]);
			}

			$archives = $dsql->SetQuery("SELECT `nickname`, `photo`, `birthday`, `sex`, `addr` FROM `#@__member` WHERE `id` = ".$results[0]['tid']);
			$userResults = $dsql->dsqlOper($archives, "results");
			if($results){
				$results[0]["tnickname"] = $userResults[0]['nickname'];
				$results[0]["tphoto"] = getFilePath($userResults[0]["photo"]);
			}

			$results[0]["litpic"] = getFilePath($results[0]["litpic"]);

			$picsArr = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode(",", $pics);
				foreach ($pics as $key => $value) {
					array_push($picsArr, array("pic" => getFilePath($value), "picSource" => $value));
				}
			}
			$results[0]['pics'] = $picsArr;

			//URL
			$param = array(
				"service"  => "dating",
				"template" => "story",
				"id"       => $id
			);
			$results[0]["url"] = getUrlPath($param);
		}
		return $results[0];
	}


	/**
     * 相亲活动分类
     * @return array
     */
	public function activityType(){
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
		$results = $dsql->getTypeList($type, "dating_activitytype", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 相亲活动
     * @return array
     */
	public function activity(){
		global $dsql;
		$pageinfo = $list = array();
		$where = $page = $pageSize = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$typeid      = $this->param['typeid'];
				$page        = $this->param['page'];
				$pageSize    = $this->param['pageSize'];
			}
		}

		$cityid = getCityId($this->param['cityid']);
		if($cityid){
			$where .= " WHERE `cityid` = ".$cityid;
		}

		//遍历分类
		if(!empty($typeid)){
			$alltype = $dsql->getTypeList($typeid, "dating_activitytype");
			if($alltype){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($alltype);
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}
			$where .= " AND `typeid` in ($lower)";
		}

		$orderby = " ORDER BY `id` desc";

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `typeid`, `title`, `tag`, `litpic`, `btime`, `etime`, `deadline`, `address`, `else`, `pubdate` FROM `#@__dating_activity`".$where.$orderby);

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

		if($results){
			$now = GetMkTime(time());

			foreach($results as $key => $val){
				$list[$key]['id'] = $val['id'];
				$list[$key]['title'] = $val['title'];
				$list[$key]['tag'] = $val['tag'];
				$list[$key]["litpic"] = getFilePath($val["litpic"]);
				$list[$key]['btime'] = $val['btime'];
				$list[$key]['etime'] = $val['etime'];
				$list[$key]['deadline'] = $val['deadline'];
				$list[$key]['address'] = $val['address'];
				$list[$key]['else'] = $val['else'];
				$list[$key]['pubdate'] = $val['pubdate'];
				$list[$key]['typeid']      = $val['typeid'];

				// $state 0:未开始1:进行中2:已结束
				if($now < $val['btime']){
					$state = 0;
					$day = ($val['btime'] - $now) / 86400;
				}elseif($now >= $val['btime'] && $now < $val['etime']){
					$state = 1;
					$day = ($val['etime'] - $now) / 86400;
				}else{
					$state = 2;
					$day = 0;
				}
				$list[$key]['day'] = (int)$day;
				$list[$key]['state'] = $state;

				global $data;
				$data = "";
				$typeArr = getParentArr("dating_activitytype", $val['typeid']);
				$typeArr = array_reverse(parent_foreach($typeArr, "typename"));
				$list[$key]['typeName']    = $typeArr;
				$list[$key]['typename']    = $typeArr[count($typeArr)-1];

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__dating_activity_take` WHERE `aid` = ".$val["id"]);
				$been = $dsql->dsqlOper($archives, "totalCount");
				$list[$key]["been"] = $been;

				//URL
				$param = array(
					"service"  => "dating",
					"template" => "activity",
					"id"       => $val['id']
				);
				$list[$key]["url"] = getUrlPath($param);
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 活动详情
     * @return array
     */
	public function activityDetail(){
		global $dsql;
		global $userLogin;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$userid = $userLogin->getMemberID();


		$duid = $this->getDatingUid($userid);

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_activity` WHERE `id` = $id");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$results[0]["lnglat"] = explode(",", $results[0]["lnglat"]);
			$results[0]["litpic"] = getFilePath($results[0]["litpic"]);

			//报名总数
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__dating_activity_take` WHERE `aid` = ".$id);
			$been = $dsql->dsqlOper($archives, "totalCount");
			$results[0]["been"] = $been;

			//男士报名数量
			$archives = $dsql->SetQuery("SELECT t.`id` FROM `#@__dating_activity_take` t LEFT JOIN `#@__dating_member` m ON m.`id` = t.`uid` LEFT JOIN `#@__member` u ON u.`id` = m.`userid` WHERE u.`sex` = 1 AND `aid` = ".$id);
			$man = $dsql->dsqlOper($archives, "totalCount");
			$results[0]["man"] = $man;

			//女士报名数量
			$archives = $dsql->SetQuery("SELECT t.`id` FROM `#@__dating_activity_take` t LEFT JOIN `#@__dating_member` m ON m.`id` = t.`uid` LEFT JOIN `#@__member` u ON u.`id` = m.`userid` WHERE u.`sex` = 0 AND `aid` = ".$id);
			$lady = $dsql->dsqlOper($archives, "totalCount");
			$results[0]["lady"] = $lady;

			// 查看是否报名
			if($duid > 0){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_activity_take` WHERE `aid` = $id AND `uid` = $duid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$results[0]['active'] = 1;
				}else{
					$results[0]['active'] = 0;
				}
			}

		}
		return $results[0];
	}


	/**
     * 活动报名
     * @return array
     */
	public function activityTake(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$aid         = $this->param['aid'];
				$sex         = $this->param['sex'];
				$page        = $this->param['page'];
				$pageSize    = $this->param['pageSize'];
			}
		}

		if(empty($aid)){
			return array("state" => 200, "info" => '格式错误！');
		}

		$where = " WHERE act.aid = $aid";

		if($sex !== ""){
			$where .= " AND user.sex = $sex";
		}

		$orderby = " ORDER BY act.id desc";

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT act.`uid`, m.`userid`, act.`pubdate` FROM `#@__dating_activity_take` act LEFT JOIN `#@__dating_member` m ON m.`id` = act.`uid` LEFT JOIN `#@__member` user ON m.userid = user.id".$where.$orderby);

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

		if($results){
			foreach($results as $key => $val){
				$list[$key]['uid'] = $val['uid'];

				$uinfo = $userLogin->getMemberInfo($val['userid']);
				if($uinfo['userType'] == 2){
					$uinfo['nickname'] = $uinfo['person'];
				}
				$uinfo["age"] = !empty($uinfo['birthday']) ? getBirthAge(date("Y-m-d", $uinfo['birthday'])) : "";
				$list[$key]['uinfo'] = $uinfo;

				$education = "";
				$sql = $dsql->SetQuery("SELECT i.`typename` FROM `#@__dating_item` i LEFT JOIN `#@__dating_member` m ON m.`education` = i.`id` WHERE m.`id` = ".$val['uid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$education = $ret[0]['typename'];
				}
				$list[$key]['education'] = $education;
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}



	/**
     * 活动报名
     * @return array
     */
	public function fabuActivity(){
		global $dsql;
		global $userLogin;

		$id     = $this->param['id'];
		$userid = $userLogin->getMemberID();

		if(!empty($id) && $userid > -1){

			$pubdate = GetMkTime(time());

			//获取登录会员的交友ID
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => 'error');
			}

			$sql = $dsql->SetQuery("SELECT `deadline` FROM `#@__dating_activity` WHERE `id` = $id ");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				if($ret[0]['deadline'] < $pubdate){
					return array("state" => 200, "info" => '该活动报名已截止');
				}
			}else{
				return array("state" => 200, "info" => '活动不存在');
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_activity_take` WHERE `aid` = $id AND `uid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				return array("state" => 200, "info" => '您已经报过名！');
			}

			$archives = $dsql->SetQuery("INSERT INTO `#@__dating_activity_take` (`aid`, `uid`, `pubdate`) VALUES ('$id', '$uid', '$pubdate')");
			$ret = $dsql->dsqlOper($archives, "update");
			if($ret == "ok"){
				return "ok";
			}else{
				return array("state" => 200, "info" => '报名失败，请稍后重试！');
			}
		}
	}



	/**
     * 婚恋课堂分类
     * @return array
     */
	public function newsType(){
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
		$results = $dsql->getTypeList($type, "dating_schooltype", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 婚恋课堂
     * @return array
     */
	public function news(){
		global $dsql;
		$pageinfo = $list = array();
		$where = $page = $pageSize = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$typeid      = $this->param['typeid'];
				$noid        = $this->param['noid'];
				$orderby     = $this->param['orderby'];
				$page        = $this->param['page'];
				$pageSize    = $this->param['pageSize'];
			}
		}

		$where = " WHERE 1 = 1";

		$cityid = getCityId($this->param['cityid']);
		if($cityid){
			// $where .= " AND `cityid` = ".$cityid;
		}

		//遍历分类
		if(!empty($typeid)){
			$alltype = $dsql->getTypeList($typeid, "dating_schooltype");
			if($alltype){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($alltype);
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}
			$where .= " AND `typeid` in ($lower)";
		}
		$where .= " AND `arcrank` = 1";

		if(!empty($noid)){
			if(is_array($noid)){
				$noid = join(",", $noid);
			}else{
	            $noid = rtrim($noid, ",");
			}
            if($noid){
			    $where .= " AND `id` not in($noid)";
			}
        }

        if($orerby == "click"){
			$orderby = " ORDER BY `click` DESC, `weight` DESC, `id` desc";
        }else{
			$orderby = " ORDER BY `weight` DESC, `id` desc";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__dating_school`".$where);
		$results = $dsql->dsqlOper($archives, "results");
		//总条数
		$totalCount = $results[0]['total'];
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$archives = $dsql->SetQuery("SELECT `id`, `typeid`, `title`, `description`, `litpic`, `pics`, `pubdate`, `click`, `weight` FROM `#@__dating_school`".$where.$orderby);
		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();

		$now = GetMktime(time());
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']      = $val['id'];
				$list[$key]['title']   = $val['title'];
				$list[$key]["litpic"]  = getFilePath($val["litpic"]);
				$list[$key]['pubdate'] = $val['pubdate'];
				$list[$key]['pubdatef'] = floorTime($now - $val['pubdate']);
				$list[$key]['typeid']  = $val['typeid'];
				$list[$key]['click']   = $val['click'];
				$list[$key]['weight']  = $val['weight'];
				$list[$key]['writer']  = $val['writer'];
				$list[$key]['source']  = $val['source'];
				$list[$key]['description']  = $val['description'];

				$imgList = array();
				if($val['pics']){
					$pics = explode(",", $val['pics']);
					foreach ($pics as $k => $v) {
						$imgList[$k] = getFilePath($v);
					}
				}
				$list[$key]['pics'] = $imgList;

				global $data;
				$data = "";
				$typeArr = getParentArr("dating_schooltype", $val['typeid']);
				$typeArr = array_reverse(parent_foreach($typeArr, "typename"));
				$list[$key]['typeName']    = $typeArr;
				$list[$key]['typename']    = $typeArr[count($typeArr)-1];

				//URL
				$param = array(
					"service"  => "dating",
					"template" => "news-detail",
					"id"       => $val['id']
				);
				$list[$key]["url"] = getUrlPath($param);
			}
		}
		// if($noid){
		// 	print_r($pageinfo);
		// 	print_r($list);
		// 	die;
		// }
		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 婚恋课堂详情
     * @return array
     */
	public function newsDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_school` WHERE `id` = $id AND `arcrank` = 1");
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$imgList = array();
			$pics = $results[0]['pics'];
			if($pics){
				$pics = explode(",", $pics);
				foreach ($pics as $k => $v) {
					$imgList[$k] = getFilePath($v);
				}
			}
			$results[0]['pics'] = $imgList;

			return $results[0];
		}
	}


	/**
     * 人气
     * @return array
     */
	public function visit(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$oper = $act = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$obj         = (int)$this->param['obj'];	// 查看会员id
				$oper        = $this->param['oper'];
				$act         = $this->param['act'];
				$new         = (int)$this->param['new'];
				$autoread    = (int)$this->param['autoread'];	//标记为已读
				$page        = $this->param['page'];
				$pageSize    = $this->param['pageSize'];
			}
		}

		// 登陆用户id
		$uid = $userLogin->getMemberID();
		// 登陆用户交友id
		$mid = 0;
		if($uid == -1){
			return array("state" => 200, "info" => '登录失败，请重新登录！');
		}else{
			$sql = $dsql->SetQuery("SELECT `id`, `level` FROM `#@__dating_member` WHERE `userid` = $uid AND `type` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$mid = $ret[0]['id'];
				$mLevel = $ret[0]['level'] > 1 ? $ret[0]['level'] : 0;
			}
		}
		if(!$mid) return array("state" => 200, "info" => '会员不存在！');

		if(empty($oper) || empty($act)){
			return array("state" => 200, "info" => '格式错误！');
		}

		//看过
		$type = 0;
		// 限会员或本人查看
		if($oper == "visit"){
			if($mLevel || empty($obj) || $obj == $mid){
				$type = 1;
				$where = " AND `type` = 1";
			}else{
				return array("state" => 200, "info" => '您还不是会员，无法查看此信息');
			}
		//关注
		}elseif($oper == "follow"){
			$type = 2;
			$where = " AND `type` = 2";
		//打招呼
		}elseif($oper == "meet"){
			$type = 3;
			$where = " AND `type` = 3";
		//喜欢
		}elseif($oper == "like"){
			$type = 4;
			$where = " AND `type` = 4";
		// 隐身
		}elseif($oper == "stealth"){
			$type = 10;
			$where = " AND `type` = $type";
		// 隐藏动态
		}elseif($oper == "hideCircle"){
			$type = 11;
			$where = " AND `type` = $type";
		// 隐藏相册
		}elseif($oper == "hideAlbum"){
			$type = 12;
			$where = " AND `type` = $type";
		}

		if($type >= 10){
			if(!$mLevel){
				return array("state" => 200, "info" => '您还不是会员，无法进行此操作');
			}
		}

		// 查看他人访问记录,仅会员可查看
		$spec = false;
		if($obj && $obj != $mid){
			$spec = true;
			if($oper == "visit" || $oper == "follow"){
				if($mLevel){
					if($act == "in"){
						$where .= " AND `uto` = $obj";
					}else{
						$where .= " AND `ufrom` = $obj";
					}
				}else{
					return array("state" => 101, "info" => '您还不是会员，请充值会员');
					$where .= " AND 1 = 3";
				}
			}else{
				$where .= " AND 1 = 4";
			}
		}
		if(!$spec){
			if($act == "in"){
				$where .= " AND `uto` = $mid AND `delto` = 0";
			}elseif($act == "out"){
				$where .= " AND `ufrom` = $mid AND `delfrom` = 0";
			}
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		// 未读
		if($new){
			$readField = $act == "in" ? "readto" : "readfrom";
			$where .= " AND `".$readField."` = 0";
			$pageSize = 1;
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_visit` WHERE 1 = 1".$where." ORDER BY `id` DESC");
		// echo $archives;die;

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
		if($results){
			$no = 0;
			$delIds = array();
			foreach($results as $key => $val){

				$uid = $act == "in" ? $val['ufrom'] : $val['uto'];
				$this->param = $uid;
				$minfo = $this->memberInfo();
				$member = $userLogin->getMemberInfo($minfo['userid']);

				if(is_array($minfo) && $minfo['state'] != 200 && $member){
					$list[$no]['id'] = $val['id'];
					$list[$no]['readfrom'] = (int)$val['readfrom'];
					$list[$no]['readto'] = (int)$val['readto'];

					$list[$no]['member'] = $minfo;

					$list[$no]['pubdate'] = date("Y-m-d H:i:s", $val['pubdate']);

					//是否已经关注
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = $mid AND `uto` = $uid AND `type` = 2");
					$follow = $dsql->dsqlOper($sql, "totalCount");
					$list[$no]['follow'] = $follow;

					//是否已经被关注
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `uto` = $mid AND `ufrom` = $uid AND `type` = 2");
					$followby = $dsql->dsqlOper($sql, "totalCount");
					$list[$no]['followby'] = $followby;

					//是否已经打招呼
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = $mid AND `uto` = $uid AND `type` = 3");
					$meet = $dsql->dsqlOper($sql, "totalCount");
					$list[$no]['meet'] = $meet;

					$no ++;
				}else{
					$delIds[] = $val['id'];
				}

			}
			if($delIds){
				$sql = $dsql->SetQuery("DELETE FROM `#@__dating_visit` WHERE `id` IN (".join(",", $delIds).")");
				$dsql->dsqlOper($sql, "update");
			}
		}

		// 手动标记为已读
		if($autoread && $type){
			if($act == "in"){
				$sql = $dsql->SetQuery("UPDATE `#@__dating_visit` SET `readto` = 1 WHERE `uto` = $mid AND `readto` = 0 AND `type` = $type");
			}elseif($act == "out"){
				$sql = $dsql->SetQuery("UPDATE `#@__dating_visit` SET `readfrom` = 1 WHERE `ufrom` = $mid AND `readfrom` = 0 AND `type` = $type");
			}
			$dsql->dsqlOper($sql, "update");
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 好友
     * @return array
     */
	public function friendList(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$oper = $act = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$u        = (int)$this->param['u'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		// 登陆用户id
		$userid = $userLogin->getMemberID();
		// 登陆用户交友id
		if($userid == -1){
			return array("state" => 200, "info" => '登录失败，请重新登录！');
		}else{
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => 'error');
			}
		}
		$archives = $dsql->SetQuery("SELECT COUNT(v1.`id`) total FROM `#@__dating_visit` v1 WHERE v1.`ufrom` = $uid AND v1.`type` = 2 AND v1.`uto`
				IN (SELECT v2.`ufrom` FROM `#@__dating_visit` v2 WHERE v2.`uto` = $uid and v2.`type` = 2)
			");
		$result = $dsql->dsqlOper($archives, "results");

		//总条数
		$totalCount = $result[0]['total'];
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$archives = $dsql->SetQuery("SELECT v1.* FROM `#@__dating_visit` v1 WHERE v1.`ufrom` = $uid AND v1.`type` = 2 AND v1.`uto`
				IN (SELECT v2.`ufrom` FROM `#@__dating_visit` v2 WHERE v2.`uto` = $uid and v2.`type` = 2)
			");
		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";

		$results = $dsql->dsqlOper($archives.$where, "results");

		$list = array();
		if($results){
			foreach ($results as $key => $value) {
				$sql = $dsql->SetQuery("SELECT d.`id`, d.`nickname`, d.`photo`, d.`profile`, m.`certifyState` FROM `#@__dating_member` d LEFT JOIN `#@__member` m ON m.`id` = d.`userid` WHERE d.`id` = ".$value['uto']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$user = array(
						"id" => $value['uto'],
						"nickname" => $ret[0]['nickname'],
						"profile" => $ret[0]['profile'],
						"certifyState" => $ret[0]['certifyState'] == 1 ? 1 : 0,
						"py" => getFirstCharter($ret[0]['nickname']),
						"photo" => $ret[0]['photo'] ? getFilePath($ret[0]['photo']) : "",
						"url" => getUrlPath(array("service" => "dating", "template" => "u", "id" => $value['uto']))
					);
					$list[] = $user;
				}
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);

	}


	/**
     * 关注、打招呼、浏览、喜欢
     * @return array
     */
	public function visitOper(){
		global $dsql;
		global $userLogin;

		$type   = (int)$this->param['type'];
		$type10 = $this->param['type10'];
		$type11 = $this->param['type11'];
		$type12 = $this->param['type12'];
		$id     = $this->param['id'];
		$state  = $this->param['state'];
		$userid = $userLogin->getMemberID();

		if(!empty($id) && $userid > -1){

			//获取登录会员的交友ID
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => 'error');
			}

			if($uid == $id){
				return array("state" => 200, "info" => 'self');
			}

			if($type){

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$uid' AND `uto` = '$id' AND `type` = '$type'");
				$return = $dsql->dsqlOper($archives, "totalCount");

				if($return == 0){
					$archives = $dsql->SetQuery("INSERT INTO `#@__dating_visit` (`ufrom`, `uto`, `type`, `pubdate`) VALUES ('$uid', '$id', '$type', ".GetMkTime(time()).")");
					$dsql->dsqlOper($archives, "update");

					// like数加1
					if($type == 2){
						$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `like` = `like` + 1 WHERE `id` = $id");
						$dsql->dsqlOper($sql, "update");
					}
					return "ok";
				}else{
					return "has";
				}

			}elseif($type10 != '' && $type11 != '' && $type12 != ''){
				$all = array(10,11,12);
				foreach ($all as $key => $value) {
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$uid' AND `uto` = '$id' AND `type` = $value");
					$ret = $dsql->dsqlOper($sql, "results");

					$f = "type".$value;
					if(${$f}){
						if(!$ret){
							$sql = $dsql->SetQuery("INSERT INTO `#@__dating_visit` (`ufrom`, `uto`, `type`, `pubdate`) VALUES ('$uid', '$id', '$value', ".GetMkTime(time()).")");
							$dsql->dsqlOper($archives, "update");
						}
					}else{
						if($ret){
							$sql = $dsql->SetQuery("DELETE FROM `#@__dating_visit` WHERE `id` = ".$ret[0]['id']);
							$dsql->dsqlOper($sql, "update");
						}
					}
				}
				return "ok";
			}
		}
	}


	/**
     * 取消关注
     * @return array
     */
	public function cancelFollow(){
		global $dsql;
		global $userLogin;

		$id     = $this->param['id'];
		$userid = $userLogin->getMemberID();

		if(!empty($id) && $userid > -1){

			//获取登录会员的交友ID
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => 'error');
			}

			if($uid == $id){
				return array("state" => 200, "info" => 'self');
			}

			$archives = $dsql->SetQuery("DELETE FROM `#@__dating_visit` WHERE `ufrom` = $uid AND `uto` = $id AND `type` = 2");
			$dsql->dsqlOper($archives, "update");

			// like数减1
			$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `like` = `like` - 1 WHERE `id` = $id");
			$dsql->dsqlOper($sql, "update");
			return "ok";

		}
	}



	/**
     * 取消喜欢
     * @return array
     */
	public function cancelLike(){
		global $dsql;
		global $userLogin;

		$id     = $this->param['id'];
		$userid = $userLogin->getMemberID();

		if(!empty($id) && $userid > -1){

			//获取登录会员的交友ID
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => 'error');
			}

			if($uid == $id){
				return array("state" => 200, "info" => 'self');
			}

			$archives = $dsql->SetQuery("DELETE FROM `#@__dating_visit` WHERE `ufrom` = $uid AND `uto` = $id AND `type` = 4");
			$dsql->dsqlOper($archives, "update");
			return "ok";

		}
	}

	/**
	 * 关注、打招呼、浏览、喜欢 -- 删除操作
	 */
	public function visitDel(){
		global $dsql;
		global $userLogin;

		$id     = $this->param['id'];	// 信息id
		$userid = $userLogin->getMemberID();

		if(!empty($id) && $userid > -1){

			//获取登录会员的交友ID
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => 'error');
			}

			$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_visit` WHERE `id` = $id AND (`ufrom` = $uid OR `uto` = $uid)");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$ret = $ret[0];
				$del = false;
				$delField = "";
				if($ret['ufrom'] == $uid){
					if($ret['delto'] == 1){
						$del = true;
					}else{
						$delField = "delfrom";
					}
				}else{
					if($ret['delfrom'] == 1){
						$del = true;
					}else{
						$delField = "delto";
					}
				}

				if($del){
					$sql = $dsql->SetQuery("DELETE FROM `#@__dating_visit` WHERE `id` = $id");
				}else{
					$sql = $dsql->SetQuery("UPDATE `#@__dating_visit` SET `".$delField."` = 1 WHERE `id` = $id");
				}
				$ret = $dsql->dsqlOper($sql, "update");
				if($ret == "ok"){
					return "操作成功";
				}else{
					return array("state" => 200, "info" => '操作失败');
				}
			}else{
				return array("state" => 200, "info" => '信息不存在');
			}

		}
	}

	/**
	 * 关注、打招呼、浏览、喜欢 -- 标记已读
	 */
	public function visitRead(){
		global $dsql;
		global $userLogin;

		$id     = $this->param['id'];	// 信息id
		$userid = $userLogin->getMemberID();

		if(!empty($id) && $userid > -1){

			//获取登录会员的交友ID
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => 'error');
			}

			$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_visit` WHERE `id` = $id AND (`ufrom` = $uid OR `uto` = $uid)");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$ret = $ret[0];
				if($ret['ufrom'] == $uid){
					$readFiled = "readfrom";
				}else{
					$readFiled = "readto";
				}

				$sql = $dsql->SetQuery("UPDATE `#@__dating_visit` SET `".$readFiled."` = 1 WHERE `id` = $id");
				$ret = $dsql->dsqlOper($sql, "update");
				if($ret == "ok"){
					return "操作成功";
				}else{
					return array("state" => 200, "info" => '操作失败');
				}
			}else{
				return array("state" => 200, "info" => '信息不存在');
			}

		}
	}

	/**
	 *
	 */


	/**
     * 更新会员资料
     * @return array
     */
	public function updateProfile(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();

		$fields = array();

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$param  = $this->param;
		$upType = (int)$param['upType'];

		if(empty($upType)){
			return array("state" => 200, "info" => '参数错误！');
		}

		if($upType < 30){
			$type = 0;
		}elseif($upType < 60){
			$type = 1;
		}else{
			$type = 2;
		}

		$now = GetMkTime(time());
		$lockTime = $now + 3600 * 24 * 30;

		$userDetail = array();
		$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = $type");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$userDetail = $ret[0];
		}else{
			return array("state" => 200, "info" => '操作异常');
		}


		// 语音介绍
		if($upType == 2){
			$fields = array("my_voice");
		// 视频认证
		}elseif($upType == 3){
			$fields = array("my_video");
		// 联系方式
		}elseif($upType == 4){
			$fields = array("qq", "wechat");
		// 自我介绍
		}elseif($upType == 5){
			$fields = array("profile");
		// 兴趣爱好
		}elseif($upType == 7){
			$fields = array("interests");
		// 基本资料、择偶条件等
		}elseif($upType == 1){

			// -------基本资料
			array_push($fields, "nickname", "sex", "child", "housetag", "cartag", "income", "addrid", "cityid", "birthday", "height", "addrid", "marriage");

			if($param['birthday']){
				$birthdayInfo = birthdayInfo($param['birthday']);
				$param['zodiac'] = $birthdayInfo['sx'];
				$param['constellation'] = $birthdayInfo['xz'];
				$param['birthday'] = GetMkTime($param['birthday']);
				array_push($fields, "birthday", "zodiac", "constellation");
			}
			// 生日
			// if($param['birthday'] && $param['birthday'] != $userDetail['birthday'] && !$userDetail['birthday_lock']){
			// 	$param['birthday'] = GetMkTime($param['birthday']);

			// 	$birthdayInfo = birthdayInfo($param['birthday']);

			// 	$param['zodiac'] = $birthdayInfo['sx'];
			// 	$param['constellation'] = $birthdayInfo['xz'];

			// 	array_push($fields, "birthday", "zodiac", "constellation");

			// 	if($userDetail['birthday']){
			// 		$param['birthday_lock'] = 1;
			// 		array_push($fields, "birthday_lock");
			// 	}

			// }
			// // 身高
			// if($param['height'] != $userDetail['height'] && $userDetail['height_locktime'] < $now){
			// 	$param['height_locktime'] = $lockTime;
			// 	array_push($fields, "height", "height_locktime");
			// }
			// // 学历
			// if($param['education'] != $userDetail['education'] && $userDetail['education_locktime'] < $now){
			// 	$param['education_locktime'] = $lockTime;
			// 	array_push($fields, "education", "education_locktime");
			// }
			// // 居住地
			// if($param['addrid'] != $userDetail['addrid'] && $userDetail['addrid_locktime'] < $now){
			// 	$param['addrid_locktime'] = $lockTime;
			// 	array_push($fields, "addrid", "addrid_locktime");
			// }
			// // 婚姻状态,由已婚状态改为其他状态时的操作
			// if($param['marriage']){
			// 	if($userDetail['marriage'] == 41 && $marriage != 41){

			// 	}else{
			// 		array_push($fields, "marriage");
			// 	}
			// }


			// -------择偶意向
			array_push($fields, "fromage", "toage", "dfheight", "dtheight", "dfeducation", "dteducation", "dfincome", "dtincome", "daddr", "dmarriage", "dhousetag", "dchild");

			// -------小档案
			// hide zodiac:属相 constellation:星座
			array_push($fields, "hometown", "household", "nation", "bloodtype", "bodytype", "bodyweight", "looks", "religion", "drink", "smoke", "workandrest");

			// -------教育及工作
			array_push($fields, "school", "major", "duties", "nature", "industry", "workstatus", "language");

			// -------家庭状况
			array_push($fields, "familyrank", "parentstatus", "fatherwork", "motherwork", "parenteconomy", "parentinsurance");

			// -------爱情规划
			array_push($fields, "marriagetime", "datetype", "othervalue", "weddingtype", "livetogeparent", "givebaby", "cooking", "housework");

		// 头像
		}elseif($upType == 8){
			$fields = array("photo");
		// 封面
		}elseif($upType == 9){
			$fields = array("cover");
		// 语音视频
		}elseif($upType == 10 || $upType == 11){
			$config = $this->config();
			$config = $config['info'];

			if($upType == 10){
				$param['my_voice_state'] = (int)$config['voiceswitch'] ? 0 : 1;
				$fields = array("my_voice", "my_voice_state");
			}elseif($upType == 11){
				$param['my_video_state'] = (int)$config['videoswitch'] ? 0 : 1;
				$fields = array("my_video", "my_video_state");
			}

		// 红娘--------------
		// 昵称
		}elseif($upType == 30){
			$fields = array("nickname");
		// 案例
		}elseif($upType == 31){
			$fields = array("case");
		// 经验
		}elseif($upType == 32){
			$fields = array("year");
		// 自我介绍
		}elseif($upType == 33){
			$fields = array("profile");
		// 荣誉
		}elseif($upType == 34){
			$fields = array("honor");
		// 擅长领域
		}elseif($upType == 35){
			$fields = array("tags");
		// 婚恋建议
		}elseif($upType == 36){
			$fields = array("advice");
		// 婚恋建议
		}elseif($upType == 37){
			$fields = array("cover");
		// 婚恋建议
		}elseif($upType == 38){
			$fields = array("tel", "qq", "wechat");
		// 头像
		}elseif($upType == 39){
			$fields = array("photo");

		// 门店--------------
		// 简介
		}elseif($upType == 60){
			$fields = array("profile");
		// 地址
		}elseif($upType == 61){
			$fields = array("address");
		// 电话
		}elseif($upType == 62){
			$fields = array("tel");
		// 公交
		}elseif($upType == 63){
			$fields = array("bus");
		// 标签
		}elseif($upType == 64){
			$fields = array("tags");
		// 坐标
		}elseif($upType == 65){
			$fields = array("lng", "lat");
		// 封面
		}elseif($upType == 66){
			$fields = array("cover");
		}else{
			return array("state" => 200, "info" => '参数错误！');
		}

		$str = array();
		foreach ($fields as $key => $value) {
			$str[] = "`$value` = '".$param[$value]."'";
		}

		$archives = $dsql->SetQuery("UPDATE `#@__dating_member` SET ".join(",", $str)." WHERE `userid` = ".$userid." AND `type` = $type");
		// echo $archives;die;
		$return = $dsql->dsqlOper($archives, "update");
		// $return = "ok";
		if($return == "ok"){
			if($fields[0] == "cover" && $param['old']){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_coverbg` WHERE `litpic` = '".$param['old']."'");
				$num = $dsql->dsqlOper($sql, "totalCount");
				if($num == 0){
					delPicFile($param['old'], "delAtlas", "dating");
				}
			}
			return "保存成功！";
		}else{
			return array("state" => 200, "info" => '保存失败！');
		}

	}

	// PC端更新个人资料
	public function updateProfile_user(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();

		$fields = array();

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}
		$userinfo = $userLogin->getMemberInfo();

		$param  = $this->param;
		$upType = $param['upType'];

		$now = GetMkTime(time());
		$lockTime = $now + 3600 * 24 * 30;

		$userDetail = array();
		$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$userDetail = $ret[0];
		}else{
			return array("state" => 200, "info" => '操作异常');
		}

		if(empty($param['phone'])) return array("state" => 200, "info" => '请填写手机号');

		$phone = $param['phone'];
		if($userinfo['phone'] != $phone){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` = '$phone' || `phone` = '$phone'");
			$res = $dsql->dsqlOper($sql, "results");
			if($res){
				return array("state" => 200, "info" => '该手机号已被注册');
			}
		}
		if(empty($param['nickname'])) return array("state" => 200, "info" => '请填写昵称');
		if(empty($param['birthday'])) return array("state" => 200, "info" => '请填写出生日期');
		if(empty($param['height'])) return array("state" => 200, "info" => '请填写身高');
		if(empty($param['addrid'])) return array("state" => 200, "info" => '请填写居住地');



		$zodiac = $constellation = $birthday = "";
		if($param['birthday']){
			$birthdayInfo = birthdayInfo($param['birthday']);
			$zodiac = $birthdayInfo['sx'];
			$constellation = $birthdayInfo['xz'];
			$birthday = GetMkTime($param['birthday']);
		}

		$language = $param['language'] ? join(",", $param['language']) : "";

		$lnglat = $param['lnglat'];
		if($lnglat && $lnglat != ","){
			$lnglat = explode(",", $lnglat);
			$lng = $lnglat[0];
			$lat = $lnglat[0];
			$lnglat = ", `lng` = '$lng', `lat` = '$lat'";
		}else{
			$lnglat = "";
		}

		$looks = (int)$param['looks'];
		$looks = $looks > 10 ? 10 : ($looks < 0 ? 0 : $looks);

		$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET
			`dateswitch` = '".(int)$param['dateswitch']."',
			`qq` = '".$param['qq']."', `wechat` = '".$param['wechat']."',
			`profile` = '".$param['profile']."',
			`fromage` = '".(int)$param['fromage']."', `toage` = '".(int)$param['toage']."',
			`dfheight` = '".(int)$param['dfheight']."', `dtheight` = '".(int)$param['dtheight']."',
			`dfeducation` = '".(int)$param['dfeducation']."', `dteducation` = '".(int)$param['dteducation']."',
			`dfincome` = '".(int)$param['dfincome']."', `dtincome` = '".(int)$param['dtincome']."',
			`dfincome` = '".(int)$param['dfincome']."', `dtincome` = '".(int)$param['dtincome']."',
			`interests` = '".$param['interests']."',
			`daddr` = '".(int)$param['daddr']."', `dmarriage` = '".(int)$param['dmarriage']."', `dhousetag` = '".(int)$param['dhousetag']."', `dchild` = '".(int)$param['dchild']."',
			`interests` = '".$param['interests']."',
			`nickname` = '".$param['nickname']."', `sex` = '".(int)$param['sex']."', `zodiac` = '$zodiac', `constellation` = '$constellation', `birthday` = '$birthday',`height` = '".(int)$param['height']."', `education` = '".(int)$param['education']."', `income` = '".(int)$param['income']."', `addrid` = '".(int)$param['addrid']."', `cityid` = '".(int)$param['cityid']."', `marriage` = '".(int)$param['marriage']."', `child` = '".(int)$param['child']."', `housetag` = '".(int)$param['housetag']."', `cartag` = '".(int)$param['cartag']."' ".$lnglat."
			, `hometown` = '".(int)$param['hometown']."', `household` = '".(int)$param['household']."', `nation` = '".(int)$param['nation']."', `bloodtype` = '".$param['bloodtype']."', `bodytype` = '".(int)$param['bodytype']."', `bodyweight` = '".(int)$param['bodyweight']."', `looks` = '".$looks."', `religion` = '".(int)$param['religion']."', `drink` = '".(int)$param['drink']."', `smoke` = '".(int)$param['smoke']."', `workandrest` = '".(int)$param['workandrest']."',
			`school` = '".$param['school']."', `major` = '".(int)$param['major']."', `duties` = '".(int)$param['duties']."', `nature` = '".(int)$param['nature']."', `industry` = '".(int)$param['industry']."', `workstatus` = '".(int)$param['workstatus']."', `language` = '".$language."',
			`familyrank` = '".(int)$param['familyrank']."', `parentstatus` = '".(int)$param['parentstatus']."', `fatherwork` = '".(int)$param['fatherwork']."', `motherwork` = '".(int)$param['motherwork']."', `parenteconomy` = '".(int)$param['parenteconomy']."', `parentstatus` = '".(int)$param['parentstatus']."', `parentinsurance` = '".(int)$param['parentinsurance']."',
			`marriagetime` = '".(int)$param['marriagetime']."', `datetype` = '".(int)$param['datetype']."', `othervalue` = '".(int)$param['othervalue']."', `weddingtype` = '".(int)$param['weddingtype']."', `livetogeparent` = '".(int)$param['livetogeparent']."', `givebaby` = '".(int)$param['givebaby']."', `cooking` = '".(int)$param['cooking']."', `housework` = '".(int)$param['housework']."' WHERE `userid` = ".$userid." AND `type` = 0");

		$res = $dsql->dsqlOper($sql, "update");
		if($res == "ok"){
			if($userinfo['phone'] != $phone){
				$sql = $dsql->SetQuery("UPDATE `#@__member` SET `phone` = '$phone', `phoneCheck` = 0 WHERE `id` = $userid");
				$dsql->dsqlOper($sql, "update");
			}
			return "操作成功";
		}else{
			return array("state" => 200, "info" => "操作失败");
		}
	}

	// PC端更新门店资料
	public function updateProfile_store(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();

		$fields = array();

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}
		$userinfo = $userLogin->getMemberInfo();

		$param  = $this->param;

		$now = GetMkTime(time());

		$storeId = 0;
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 2");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$storeId = $ret[0]['id'];
		}

		$nickname = $param['nickname'];
		$profile = $param['profile'];
		$address = $param['address'];
		$lnglat = $param['lnglat'];
		$tel = $param['tel'];
		$photo = $param['photo'];
		$bus = $param['bus'];
		$tags = $param['tags'];

		if(empty($nickname)) return array("state" => 200, "info" => '请填写门店名称');
		if(empty($profile)) return array("state" => 200, "info" => '请填写门店简介');
		if(empty($address)) return array("state" => 200, "info" => '请填写门店地址');
		if(empty($lnglat) || $lnglat == ",") return array("state" => 200, "info" => '请选择门店坐标');
		if(empty($tel)) return array("state" => 200, "info" => '请填写门店电话');
		if(empty($photo)) return array("state" => 200, "info" => '请上传门店代表图');

		$lnglat = explode(",", $lnglat);
		$lng = $lnglat[0];
		$lat = $lnglat[1];

		$where = $storeId ? " AND `id` != $storeId" : "";
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `nickname` = '$nickname' AND `type` = 2 AND `state` = 1".$where);
		$res = $dsql->dsqlOper($sql, "results");
		if($res) return array("state" => 200, "info" => '门店名称已存在');

		if($storeId){
			$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `nickname` = '$nickname', `profile` = '$profile', `address` = '$address', `lng` = '$lng', `lat` = '$lat', `tel` = '$tel', `photo` = '$photo', `bus` = '$bus', `tags` = '$tags' WHERE `id` = $storeId");
		}else{
			$sql = $dsql->SetQuery("INSERT INTO `#@__dating_member` (`type`, `userid`, `nickname`, `profile`, `address`, `lng`, `lat`, `tel`, `photo`, `bus`, `tags`) VALUES (2, $userid, '$nickname', '$profile', '$address', '$lng', '$lat', '$tel', '$photo', '$bus', '$tags')");
		}
		$res = $dsql->dsqlOper($sql, "update");
		if($res == "ok"){
			return "提交成功";
		}else{
			return array("state" => 200, "info" => '提交失败');
		}
	}

	// PC端更新红娘资料
	public function updateProfile_hn(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();

		$fields = array();

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}
		$userinfo = $userLogin->getMemberInfo();

		$param  = $this->param;

		$now = GetMkTime(time());

		$hnId = 0;
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 1");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$hnId = $ret[0]['id'];
		}

		$nickname = $param['nickname'];
		$photo = $param['photo'];
		$profile  = $param['profile'];
		$tel      = $param['tel'];
		$qq       = $param['qq'];
		$wechat   = $param['wechat'];
		$advice   = $param['advice'];
		$honor    = $param['honor'];
		$case     = (int)$param['case'];
		$year     = (int)$param['year'];
		$tags     = $param['tags'];

		if(empty($nickname)) return array("state" => 200, "info" => '请填写名称');
		if(empty($photo)) return array("state" => 200, "info" => '请上传头像');
		if(empty($profile)) return array("state" => 200, "info" => '请填写简介');
		if(empty($tel)) return array("state" => 200, "info" => '请填写电话');


		if($hnId){
			$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `nickname` = '$nickname', `photo` = '$photo', `profile` = '$profile', `tel` = '$tel', `qq` = '$qq', `wechat` = '$wechat', `advice` = '$advice', `honor` = '$honor', `case` = '$case', `year` = '$year', `tags` = '$tags' WHERE `id` = $hnId");
		}else{
			$sql = $dsql->SetQuery("INSERT INTO `#@__dating_member` (`type`, `userid`, `nickname`, `photo`, `profile`, `tel`, `qq`, `wechat`, `advice`, `honor`, `case`, `year`, `tags`) VALUES (1, $userid, '$nickname', '$photo', '$profile', '$advice', '$qq', '$wechat', '$tel', '$honor', '$case', '$year', '$tags')");
		}
		$res = $dsql->dsqlOper($sql, "update");
		if($res == "ok"){
			return "提交成功";
		}else{
			return array("state" => 200, "info" => '提交失败');
		}
	}


	// ---------------------------------1807
	/**
	 * 交友开关
	 */
	public function datingSwitch(){
		global $dsql;
		global $userLogin;

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		$state = (int)$this->param['state'];

		if($state == 1){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = 0 AND `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				$now = GetMkTime(time());
				$fromage = $toage = $addrid_locktime = $marriage = $child = $height = $height_locktime = $bodytype = $housetag = $workstatus = $income = $income_locktime = $education = $education_locktime = $smoke = $drink = $workandrest = $cartag = $dfheight = $dtheight = $addr = $dmarriage = $dhousetag = $deducation = $dincome = $nation = $bodyweight = $looks = $religion = $major = $duties = $nature = $industry = $familyrank = $parentstatus = $fatherwork = $motherwork = $parenteconomy = $parentinsurance = $marriagetime = $datetype = $othervalue = $weddingtype = $livetogeparent = $givebaby = $cooking = $housework = $level = $expired = $dfeducation = $dteducation = $dfincome = $dtincome = $dchild = $my_video_state = 0;

				$sql = $dsql->SetQuery("SELECT `sex`, `nickname`, `photo`, `phone`, `phoneCheck`, `addr`, `birthday`, `qq` FROM `#@__member` WHERE `id` = ".$userid);
				$ret = $dsql->dsqlOper($sql, "results");
				$detail = $ret[0];

				$tags = $sign = $interests = $language = $profile = $wechat = $cover = $my_voice = $my_video = "";
				$addrid        = $hometown = $household = $detail['addr'];
				$nickname      = $detail['nickname'];
				$sex           = $detail['sex'];
				$sex_lock      = 0;
				$birthday      = $detail['birthday'];
				$birthday_lock = 0;
				$qq            = $detail['qq'];
				$photo         = $detail['photo'];

				if($birthday){
					$birthday = GetMkTime($birthday);
				}else{
					$birthday = 0;
				}


				$data = array(
					"type" => 0,
					"addrid" => $addrid,
					"nickname" => $nickname,
					"sex" => $sex,
					"birthday" => $birthday,
					"sex" => $sex,
					"qq" => $qq,
					"photo" => $photo,
					"dateswitch" => 1,
					"state" => 1,
				);
				$r = $this->joinInsert($data);
				if($r){

					$uid = $r;

					$numid = (int)date('y');
					if($r < 1000){
						$numid .= substr(time(), -4);
					}
					$numid .= $r;
					$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `numid` = '$numid' WHERE `id` = $r");
					$dsql->dsqlOper($sql, "update");

					return "恭喜，您已注册成功，请完善您的资料。未上传头像或没有验证手机号将无法展示";
				}else{
					return "注册失败，请重新提交";
				}
			}else{
				$uid = $ret[0]['id'];
			}
		}else{
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = 0 AND `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => 'error');
			}
		}

		$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `dateswitch` = '$state' WHERE `id` = $uid");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			return "操作成功";
		}else{
			return array("state" => 200, "info" => '操作失败！');
		}

	}

	// 根据登陆用户id获取交友id
	function getDatingUid($uid = 0, $type = 0){
		global $dsql;
		global $userLogin;

		$where = "";
		if($uid == 0){
			$uid = $userLogin->getMemberID();
		}
		if($uid > 0){
			if($type !== NULL){
				$where = " AND `type` = $type";
			}
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $uid".$where);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				return $ret[0]['id'];
			}else{
				return -1;
			}
		}else{
			return -1;
		}
	}

	// 根据交友id获取登陆用户id
	function getSysUid($uid = 0){
		global $dsql;
		global $userLogin;
		$param = $this->param;
		if($uid == 0) $uid = (int)$param;
		if($uid > 0){
			$sql = $dsql->SetQuery("SELECT `userid` FROM `#@__dating_member` WHERE `id` = '$uid'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				return $ret[0]['userid'];
			}else{
				return 0;
			}
		}else{
			return -1;
		}
	}

	/**
	 * 入驻红娘
	 * @return [type] [description]
	 */
	public function joinHongNiang(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		$param = $this->param;
		$photo = $param['photo'];
		$nickname = $param['nickname'];
		$phone = $param['phone'];
		$profile = $param['profile'];

		$state = 0;
		$sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__dating_member` WHERE `type` = 1 AND `userid` = '$userid'");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
			$state = $ret[0]['state'];
			if($state == 0){
				$info = "您已入驻红娘，请等待审核";
			}elseif($state == 1){
				$info = "您已入驻红娘";
			}
			return array("state" => 200, "info" => $info);
		}

		if(empty($nickname)) return array("state" => 200, "info" => '请填写姓名');
		if(empty($phone)) return array("state" => 200, "info" => '请填写手机号');
		if(empty($profile)) return array("state" => 200, "info" => '请填写个人简介');
		if(empty($photo)) return array("state" => 200, "info" => '请上传头像');

		// 审核失败后重新提交
	    if($state == 2){
	      $sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `nickname` = '$nickname', `photo` = '$photo', `profile` = '$profile', `phone` = '$phone', `state` = 0 WHERE `id` = $uid");
	      $ret = $dsql->dsqlOper($sql, "update");
	      if($ret == "ok"){
	        return "入驻申请已提交，请耐心等待审核";
	      }else{
	        return "入驻申请提交失败，请重新提交";
	      }
	    }

		$data = array(
			"type" => 1,
			"nickname" => $nickname,
			"phone" => $phone,
			"tel" => $phone,
			"profile" => $profile,
			"photo" => $photo,
		);
		$r = $this->joinInsert($data);
		if($r){
			return "入驻申请已提交，请耐心等待审核";
		}else{
			return "入驻申请提交失败，请重新提交";
		}

	}

	/**
	 * 入驻门店
	 * @return [type] [description]
	 */
	public function joinStore(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		$param = $this->param;
		$photo = $param['photo'];
		$nickname = $param['nickname'];
		$phone = $param['phone'];
		$profile = $param['profile'];
		$address = $param['address'];

		$state = 0;
		$sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__dating_member` WHERE `type` = 2 AND `userid` = '$userid'");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
			$state = $ret[0]['state'];
			if($state == 0){
				$info = "您已入驻门店，请等待审核";
			}elseif($state == 1){
				$info = "您已入驻门店";
			}
			return array("state" => 200, "info" => $info);
		}

		if(empty($nickname)) return array("state" => 200, "info" => '请填写门店名称');
		if(empty($phone)) return array("state" => 200, "info" => '请填写手机号');
		if(empty($address)) return array("state" => 200, "info" => '请填写门店地址');
		if(empty($profile)) return array("state" => 200, "info" => '请填写个人简介');
		if(empty($photo)) return array("state" => 200, "info" => '请上传头像');

		// 审核失败后重新提交
	    if($state == 2){
	      $sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `nickname` = '$nickname', `photo` = '$photo', `profile` = '$profile', `phone` = '$phone', `address` = '$address', `state` = 0 WHERE `id` = $uid");
	      $ret = $dsql->dsqlOper($sql, "update");
	      if($ret == "ok"){
	        return "入驻申请已提交，请耐心等待审核";
	      }else{
	        return "入驻申请提交失败，请重新提交";
	      }
	    }

		$data = array(
			"type" => 2,
			"nickname" => $nickname,
			"phone" => $phone,
			"tel" => $phone,
			"address" => $address,
			"profile" => $profile,
			"photo" => $photo,
		);
		$r = $this->joinInsert($data);
		if($r){

			// 更新店铺开关
            updateStoreSwitch("dating", "dating_member", $userid, $r);

			return "入驻申请已提交，请耐心等待审核";
		}else{
			return "入驻申请提交失败，请重新提交";
		}

	}

	/**
	 * 添加用户
	 */
	public function addUser(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		$param    =  $this->param;
		$type     = (int)$param['type'];
		$mobile   = $param['mobile'];
		$password = $param['password'];
		$nickname = $param['nickname'];

		$entrust = 0;

		if(empty($type)) return array("state" => 200, "info" => '参数错误');
		if(empty($mobile)) return array("state" => 200, "info" => '请填写手机号');
		if(empty($password)) return array("state" => 200, "info" => '请填写登陆密码');
		if($type == 1) $entrust  = (int)$param['entrust'];


		// 验证权限
		$sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__dating_member` WHERE `userid` = '$userid' AND `type` = $type");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			if($ret[0]['state'] == 0){
				return array("state" => 200, "info" => '您的入驻申请尚未通过');
			}
			if($ret[0]['state'] == 2){
				return array("state" => 200, "info" => '您的入驻申请已被拒绝');
			}
			$uid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => '您还没有入驻');
		}

		// 入驻会员类型
		$type--;

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `phone` = '$mobile' || `username` = '$mobile'");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			return array("state" => 200, "info" => '手机号已存在，请重新输入');
		}
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `phone` = '$mobile'");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			return array("state" => 200, "info" => '手机号已存在，请重新输入');
		}

		$mtype      = 1;
		$username   = $phone = $mobile;
		$email      = $company = "";
		$emailCheck = 0;
		$phoneCheck = 1;
		$regtime    = GetMkTime(time());
		$regip      = GetIP();
		$regipaddr  = getIpAddr($regip);
		$passwd     = $userLogin->_getSaltedHash($password);

		$nickname   = $nickname ? $nickname : 'user_'.substr(time(), -4);


		$archives = $dsql->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `email`, `emailCheck`, `phone`, `phoneCheck`, `company`, `regtime`, `regip`, `regipaddr`, `state`) VALUES ('$mtype', '$username', '$passwd', '$nickname', '$email', '0', '$phone', '0', '$company', '$regtime', '$regip', '$regipaddr', '1')");
		$aid = $dsql->dsqlOper($archives, "lastid");
		if(is_numeric($aid)){
			$data = array(
				"userid" => $aid,
				"type" => $type,
				"nickname" => $nickname,
				"phone" => $phone,
				"company" => $uid,
				"entrust" => $entrust,
				"dateswitch" => 1,
				"state" => 1,
			);
			$r = $this->joinInsert($data);
			if($r){
				$uid = $r;

				$numid = (int)date('y');
				if($r < 1000){
					$numid .= substr(time(), -4);
				}
				$numid .= $r;
				$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `numid` = '$numid' WHERE `id` = $r");
				$dsql->dsqlOper($sql, "update");

				return "添加成功";
			}else{
				return "添加失败";
			}
		}

	}

	/**
	 * 添加、注册会员写入数据
	 * @return [type] [description]
	 */
	private function joinInsert($data){
		global $dsql;
		global $userLogin;

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		if(empty($data) || !is_array($data)) return array("state" => 200, "info" => '提交失败');


		$jointime = $activedate = $now = GetMkTime(time());
		$cityid = $fromage = $toage = $addrid_locktime = $marriage = $child = $height = $height_locktime = $bodytype = $housetag = $workstatus = $income = $income_locktime = $education = $education_locktime = $smoke = $drink = $workandrest = $cartag = $dfheight = $dtheight = $daddr = $dmarriage = $dhousetag = $deducation = $dincome = $nation = $bodyweight = $looks = $religion = $major = $duties = $nature = $industry = $familyrank = $parentstatus = $fatherwork = $motherwork = $parenteconomy = $parentinsurance = $marriagetime = $datetype = $othervalue = $weddingtype = $livetogeparent = $givebaby = $cooking = $housework = $level = $expired = $dfeducation = $dteducation = $dfincome = $dtincome = $dchild = $my_video_state = $state = $sex = $sex_lock = $birthday = $birthday_lock = $lng = $lat = $dateswitch = 0;

		$tags = $sign = $interests = $language = $wechat = $cover = $my_voice = $my_video = $qq = $wechat = $profile = $zodiac = $constellation = "";
		$addrid = $hometown = $household = $numid = $like = 0;
		$case = $year = $company = $entrust = 0;
		$honor = $good = $advice = $address = '';

		extract($data);

		if($birthday){
			$birthdayInfo = birthdayInfo($birthday);
			$zodiac = $birthdayInfo['sx'];
			$constellation = $birthdayInfo['xz'];
		}

		$archives = $dsql->SetQuery("INSERT INTO `#@__dating_member` (`cityid`, `type`, `entrust`, `userid`, `fromage`, `toage`, `tags`, `addrid`, `addrid_locktime`, `sign`, `marriage`, `child`, `height`, `height_locktime`, `bodytype`, `housetag`, `workstatus`, `income`, `income_locktime`, `education`, `education_locktime`, `smoke`, `drink`, `workandrest`, `cartag`, `dfheight`, `dtheight`, `daddr`, `dmarriage`, `dhousetag`, `deducation`, `dincome`, `jointime`, `numid`, `nickname`, `sex`, `sex_lock`, `birthday`, `birthday_lock`, `interests`, `hometown`, `household`, `nation`, `zodiac`, `constellation`, `bloodtype`, `bodyweight`, `looks`, `religion`, `school`, `major`, `duties`, `nature`, `industry`, `language`, `familyrank`, `parentstatus`, `fatherwork`, `motherwork`, `parenteconomy`, `parentinsurance`, `marriagetime`, `datetype`, `othervalue`, `weddingtype`, `livetogeparent`, `givebaby`, `cooking`, `housework`, `profile`, `lng`, `lat`, `phone`, `qq`, `wechat`, `dfeducation`, `dteducation`, `dfincome`, `dtincome`, `dchild`, `dateswitch`, `my_voice`, `my_video`, `my_video_state`, `photo`, `cover`, `level`, `expired`, `state`, `case`, `year`, `company`, `honor`, `advice`, `address`, `like`, `activedate`) VALUES ('$cityid', '$type', '$entrust', '$userid', '$fromage', '$toage', '$tags', '$addrid', '$addrid_locktime', '$sign', '$marriage', '$child', '$height', '$height_locktime', '$bodytype', '$housetag', '$workstatus', '$income', '$income_locktime', '$education', '$education_locktime', '$smoke', '$drink', '$workandrest', '$cartag', '$dfheight', '$dtheight', '$daddr', '$dmarriage', '$dhousetag', '$deducation', '$dincome', '$jointime', '$numid', '$nickname', '$sex', '$sex_lock', '$birthday', '$birthday_lock', '$interests', '$hometown', '$household', '$nation', '$zodiac', '$constellation', '$bloodtype', '$bodyweight', '$looks', '$religion', '$school', '$major', '$duties', '$nature', '$industry', '$language', '$familyrank', '$parentstatus', '$fatherwork', '$motherwork', '$parenteconomy', '$parentinsurance', '$marriagetime', '$datetype', '$othervalue', '$weddingtype', '$livetogeparent', '$givebaby', '$cooking', '$housework', '$profile', '$lng', '$lat', '$phone', '$qq', '$wechat', '$dfeducation', '$dteducation', '$dfincome', '$dtincome', '$dchild', '$dateswitch', '$my_voice', '$my_video', '$my_video_state', '$photo', '$cover', '$level', '$expired', '$state', '$case', '$year', '$company', '$honor', '$advice', '$address', '$like', '$activedate')");
		$return = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($return)){
			return $return;
		}else{
			return false;
		}
	}

	/**
	 * 更新坐标
	 */
	public function updateLocation(){
		global $dsql;
		global $userLogin;

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		$param = $this->param;
		$lng = $param['lng'];
		$lat = $param['lat'];

		if(empty($lng) || empty($lat)) return array("state" => 200, "info" => "坐标信息错误");

		$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `lng` = '$lng', `lat` = '$lat' WHERE `type` = 0 AND `userid` = '$userid'");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			return "更新成功";
		}else{
			return array("state" => 200, "info" => "更新失败");
		}
	}

	/**
	 * 获取会员联系方式或其它限付费用户查看的信息
	 * 用户使用了牵线并且牵线成功后可以查看联系方式(即使用户设置了隐藏联系方式，仍可以查看)
	 */
	public function getMemberSpecInfo(){
		global $dsql;
		global $userLogin;

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");
		$param = $this->param;
		$id    = $param['id'];
		$name  = $param['name'];
		$type  = (int)$param['type'];

		if(empty($id) || empty($name)) return array("state" => 200, "info" => "参数错误");

		$check = $this->getMemberLevelInfo($userid);

		$uid = $this->getDatingUid($userid);
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = $type");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
			$uids = array();
			if($type == 1){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `company` = $uid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					foreach ($ret as $k => $v) {
						$uids[$k] = $v['id'];
					}
				}else{
					return array("state" => 200, "info" => "操作错误");
				}
			}else{
				$uids[] = $uid;
			}
		}else{
			return array("state" => 200, "info" => "操作权限错误");
		}


		$sql = $dsql->SetQuery("SELECT m.`phone`, d.`userid`, d.`qq`, d.`wechat`, d.`company`, d.`entrust`, d.`my_voice`, d.`my_voice_state`, d.`my_video`, d.`my_video_state` FROM `#@__dating_member` d LEFT JOIN `#@__member` m ON m.`id` = d.`userid` WHERE d.`id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$purviews = false;
			$ret = $ret[0];
			if($check === false && $userid != $ret['userid']){
				// 判断是否有牵线，并且牵线成功
				if($name == "contact" || $name == "phone" || $name == "qq" || $name == "wechat"){
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_lead` WHERE `state` = 2 AND (`ufrom` IN (".join(",", $uids).") && `uto` = $id) || (`ufrom` = $id && `uto` IN (".join(",", $uids).")) ");
					$res = $dsql->dsqlOper($sql, "results");
					if($res){
						$purviews = true;
					}else{
						return array("state" => 200, "info" => "您还不是会员，无法查看此信息");
					}
				}else{
					return array("state" => 200, "info" => "您还不是会员，无法查看此信息");
				}
			}

			if($name == "contact" || $name == "phone" || $name == "qq" || $name == "wechat"){
				if(!$purviews && $ret['entrust']){
					return array("state" => 200, "info" => "该会员已设置了隐藏联系方式，请联系红娘");
				}
			}

			if($name == "contact"){
				$r = array(
					"phone" => $ret["phone"],
					"qq" => $ret["qq"],
					"wechat" => $ret["wechat"],
				);
				return $r;
			}else{
				if(isset($ret[$name])){
					if($ret[$name]){
						$r = $ret[$name];
						if($name == "my_voice" || $name == "my_video"){
							$r = $ret[$name."_state"] == 1 ? getFilePath($r) : "";
						}
						return $r;
					}else{
						return array("state" => 200, "info" => "该会员未完善此项资料");
					}
				}else{
					return array("state" => 200, "info" => "资料不存在或未开放");
				}
			}
		}

	}

	/**
	 * 获取会员等级信息
	 * $common为true时，为了获取普通用户的特权配置
	 */
	public function getMemberLevelInfo($userid = 0, $common = false){
		global $dsql;
		global $userLogin;
		$param = $this->param;

		if($userid == 0) $userid = (int)$param['userid'];
		if($common == false) $common = (boolean)$param['common'];

		$sql = $dsql->SetQuery("SELECT `id`, `level` FROM `#@__dating_member` WHERE `userid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$level = $ret[0]['level'];
			if($common && $level == 0){
				$level = 1;
			}
			if($level){
				$sql = $dsql->SetQuery("SELECT `name`, `privilege` FROM `#@__dating_level` WHERE `id` = $level");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$privilege = $ret[0]['privilege'];
					$privilege = unserialize($privilege);
					$data = array(
							"id" => $level,
							"name" => $ret[0]['name'],
							"privilege" => $privilege
						);
					return $data;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * 验证操作权限
	 */
	public function checkPuriview(){
		global $dsql;
		global $userLogin;

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");
		$param = $this->param;

		$id   = $param['id'];
		$type = $param['type'];
		if(empty($id) || empty($type)) return array("state" => 200, "info" => "参数错误");

		$sql = $dsql->SetQuery("SELECT `id`, `level`, `key` FROM `#@__dating_member` WHERE `state` = 1 AND `dateswitch` = 1 `userid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$ret = $ret[0];
			$uid = $ret['id'];

			if($id == $uid) return array("state" => 200, "info" => '自己给自己发私信多没意思呀~');

			$puriviewConfig = $this->config();
			// 私信
			if($type == "review"){
				if($ret['key'] == 0){
					return array("state" => 200, "info" => "您今日聊天钥匙已用完，请升级会员或购买钥匙");
				}else{
					//判断是否已经有对话
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_review` WHERE (`ufrom` = $uid AND `uto` = $id) OR (`ufrom` = $id AND `uto` = $uid)");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$reviewId = $ret['id'];
					// 创建聊天
					}else{
						$archives = $dsql->SetQuery("INSERT INTO `#@__dating_review` (`ufrom`, `uto`, `pubdate`) VALUES ('$uid', '$id', ".GetMkTime(time()).")");
						$ret = $dsql->dsqlOper($archives, "lastid");
						if(is_numeric($ret)){
							$reviewId = $ret;
						}else{
							return array("state" => 200, "info" => "创建聊天失败，请重试");
						}
					}
					// 返回查询用户信息，对话id ？
					return $reviewId;
				}
			}
		}else{
			return array("state" => 200, "info" => "会员不存在或状态异常");
		}

	}

	/**
	 * 获取等级列表
	 */
	public function memberLevel(){
		global $dsql;
		$order = $this->param['order'];
		$order = $order ? $order : 'DESC';
		$def = (int)$this->param['def'];
		$def = $def ? "" : " AND `def` = 0";
		$list = array();
		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_level` WHERE 1 = 1 $def ORDER BY `id` ".$order);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			foreach ($results as $key => $value) {
				foreach ($value as $k => $v) {
					if($k == "icon" && $v){
						$v = getFilePath($v);
					}
					$list[$key][$k] = $v;
				}
			}
		}
		return $list;
	}


	/**
	 * 升级会员
	 */
	public function upgrade(){
		global $dsql;
		global $userLogin;
		$param = $this->param;
		$paytype    = $param['paytype'];
		$check      = $param['check'];
		$id         = (int)$param['id'];
		$qr         = (int)$param['qr'];
		$useBalance = (int)$param['useBalance'];

		$isPC = !isMobile();

		$userid = $userLogin->getMemberID();
		if($userid == -1){
			if($check){
				return array("state" => 200, "info" => "登陆超时，请重新登陆");
			}else{
				die("登陆超时，请重新登陆");
			}
		}else{
			$uid = $this->getDatingUid($userid);
			if($uid == 0){
				if($check){
					return array("state" => 200, "info" => "您还没有注册交友用户");
				}else{
					die("您还没有注册交友用户");
				}
			}
		}

		$userinfo = $userLogin->getMemberInfo();
		$usermoney = $userinfo['money'];

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_level` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$detail = $results[0];
			$sql = $dsql->SetQuery("SELECT `level`, `expired` FROM `#@__dating_member` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			$ret = $ret[0];
			$lastlevel = $ret['level'];
			$lastexpired = $ret['expired'];

			if($lastlevel){
				if($ret['level'] > $id){
					if($check){
						return array("state" => 200, "info" => "您当前的会员等级高于操作等级");
					}else{
						die("您当前的会员等级高于操作等级");
					}
				}elseif($ret['level'] < $id){
					if($check){
						$time = date("Y-m-d H:i:s", $lastexpired);
						return array("state" => 101, "info" => "您当前已经是VIP会员，并且与".$time."到期。升级后到期时间将从当前时间开始计算。确定要进行升级操作吗？");
					}
				}else{
					if($check) return "ok";
				}
			}else{
				if($check) return "ok";
			}
		}else{
			if($check){
				return array("state" => 200, "info" => "会员等级不存在");
			}else{
				die("会员等级不存在");
			}
		}

		// 创建订单
		$price = $detail['cost'];
		$count = $detail['month'];
		$title = "VIP".$count."个月".$price."元";
		$ordernum = create_ordernum();
		$orderdate = GetMkTime(time());

		$totalPrice = $price;

		$param = array(
			"lastexpired" => $lastexpired,
			"lastlevel" => $lastlevel,
			"level" => $id,
			"month" => $count,
		);
		$param = serialize($param);
		// 删除未支付的订单
		$sql = $dsql->SetQuery("DELETE FROM `#@__dating_order` WHERE `type` = 0 AND `uid` = $uid AND `orderstate` = 0");
		$dsql->dsqlOper($sql, "update");

		$sql = $dsql->SetQuery("INSERT INTO `#@__dating_order` (`uid`, `type`, `title`, `ordernum`, `orderdate`, `paytype`, `paydate`, `orderstate`, `payprice`, `price`, `count`, `totalPrice`, `param`) VALUES ('$uid', '0', '$title', '$ordernum', '$orderdate', '', '0', '0', '0', '$price', '1', '$price', '$param')");
		$ret = $dsql->dsqlOper($sql, "lastid");

		if($isPC){
			$balance = 0;
			if($useBalance && $usermoney > 0){
				$balance = $usermoney > $totalPrice ? $totalPrice : $usermoney;
				$payprice = $totalPrice - $balance;
			}else{
				$payprice = $totalPrice;
			}
			if($payprice > 0 && $qr){
				return	createPayForm("dating", $ordernum, $payprice, $paytype, '交友充值会员');
			}else{
				return $ordernum;
			}

		}

		$param = array(
			"service" => "dating",
			"template" => "pay",
			"param" => "ordernum=".$ordernum,
		);
		$url = getUrlPath($param);
		header("location:".$url);

	}

	/**
	 * 购买金币
	 */
	public function buyGold(){
		global $dsql;
	    global $userLogin;
		$param      = $this->param;
		$check      = $param['check'];
		$count      = (int)$param['count'];
		$paytype    = $param['paytype'];
		$qr         = (int)$param['qr'];
		$useBalance = (int)$param['useBalance'];

		$isPC = !isMobile();

	    $userid = $userLogin->getMemberID();
	    if($userid == -1){
	    	if($check){
				return array("state" => 200, "info" => "登陆超时，请重新登陆");
	      	}else{
	        	die("登陆超时，请重新登陆");
	      	}
	    }else{
	      	$uid = $this->getDatingUid($userid);
	      	if($uid == 0){
	        	if($check){
	          		return array("state" => 200, "info" => "您还没有注册交友用户");
	        	}else{
	          		die("您还没有注册交友用户");
	        	}
	      	}
	    }

	    $config  = $this->config();
	    $goldDeposit = $config['goldDeposit'];

	    if(empty($goldDeposit)){
	    	$goldDeposit = array(6,300,680,1360,2040,2720);
	    }else{
	    	$goldDeposit = explode(",", $goldDeposit);
	    }
	    if(in_array($count, $goldDeposit)){
	    	if($check) return "ok";
	    }else{
	    	if($check){
	        	return array("state" => 200, "info" => "充值金额有误");
	      	}else{
	        	die("充值金额有误");
	      	}
	    }


	    $config = $this->config();
	    $globName = $config['goldName'];

	    // 创建订单
	    $price = 1 / $config['goldRatio'];
	    $totalPrice = $price * $count;
	    $title = "购买".$count.$globName;
	    $ordernum = create_ordernum();
	    $orderdate = GetMkTime(time());

	    $param = array(
	    	"count" => $count
	    );
	    $param = serialize($param);
	    // 删除未支付的订单
	    $sql = $dsql->SetQuery("DELETE FROM `#@__dating_order` WHERE `type` = 1 AND `uid` = $uid AND `orderstate` = 0");
	    $dsql->dsqlOper($sql, "update");

	    $sql = $dsql->SetQuery("INSERT INTO `#@__dating_order` (`uid`, `type`, `title`, `ordernum`, `orderdate`, `paytype`, `paydate`, `orderstate`, `payprice`, `price`, `count`, `totalPrice`, `param`) VALUES ('$uid', '1', '$title', '$ordernum', '$orderdate', '', '0', '0', '0', '$price', '$count', '$totalPrice', '$param')");
	    $ret = $dsql->dsqlOper($sql, "lastid");

	    if($isPC){
	    	$userinfo = $userLogin->getMemberInfo();
			$usermoney = $userinfo['money'];

			$balance = 0;
			if($useBalance && $usermoney > 0){
				$balance = $usermoney > $totalPrice ? $totalPrice : $usermoney;
				$payprice = $totalPrice - $balance;
			}else{
				$payprice = $totalPrice;
			}
			if($payprice > 0 && $qr){
				return	createPayForm("dating", $ordernum, $payprice, $paytype, '交友购买'.$globName);
			}else{
				return $ordernum;
			}

		}

	    $param = array(
	      "service" => "dating",
	      "template" => "pay",
	      "param" => "ordernum=".$ordernum,
	    );
	    $url = getUrlPath($param);
	    header("location:".$url);
	}

	/**
	 * 支付前检查
	 */
	public function checkPayAmount(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$ordernum   = $param['ordernum'];
		$useBalance = $param['useBalance'];
		$balance    = (float)$param['balance'];
		$paypwd     = $this->param['paypwd'];      //支付密码
		$paypwd     = $this->param['paypwd'];      //支付密码

		$userid     = $userLogin->getMemberID();

		$isPC = !isMobile();

		if($ordernum){
			$sql = $dsql->SetQuery("SELECT `id`, `type`, `orderstate`, `price`, `count`, `totalPrice` FROM `#@__dating_order` WHERE `ordernum` = '$ordernum'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$payprice = $ret[0]['totalPrice'];
				// if($ret[0]['type'] == 0){
				// 	$payprice = $ret[0]['price'];
				// }elseif($ret[0]['type'] == 1){
				// 	$payprice = $ret[0]['price'] * $ret[0]['count'];
				// }

				// 使用余额
				if($useBalance && $balance){

					if(!$isPC){
						if(empty($paypwd)){
							return array("state" => 200, "info" => "请输入支付密码！");
						}

						//验证支付密码
						$archives = $dsql->SetQuery("SELECT `id`, `paypwd` FROM `#@__member` WHERE `id` = '$userid'");
						$results  = $dsql->dsqlOper($archives, "results");
						$res = $results[0];
						$hash = $userLogin->_getSaltedHash($paypwd, $res['paypwd']);
						if($res['paypwd'] != $hash){
							return array("state" => 200, "info" => "支付密码输入错误，请重试！");
						}
					}

					$userinfo = $userLogin->getMemberInfo();
					$usermoney = $userinfo['money'];

					//验证余额
					if($usermoney < $balance){
						return array("state" => 200, "info" => "您的余额不足，支付失败！");
					}

					$payprice -= $balance;

				}

				return sprintf("%.2f", $payprice);

			}else{
				return array("state" => 200, "info" => "订单不存在");
			}
		}else{
			return array("state" => 200, "info" => "参数错误");
		}
	}


	/**
	 * 支付
	 */
	public function pay(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$ordernum   = $param['ordernum'];
		$paytype    = $param['paytype'];
		$useBalance = $param['useBalance'];
		$balance    = (float)$param['balance'];
		$paypwd     = $this->param['paypwd'];      //支付密码
		$furl       = $this->param['furl'];       //指定返回地址

		$userid     = $userLogin->getMemberID();

		$r = $this->checkPayAmount();

		if(!is_array($r)){
			$payprice = (float)$r;
			if($payprice > 0){
				$tit = "交友订单";
				$sql = $dsql->SetQuery("SELECT `type` FROM `#@__dating_order` WHERE `ordernum` = '$ordernum'");
				$res = $dsql->dsqlOper($sql, "results");
				if($res){
					$type = $res[0]['type'];
					switch($type){
						case 0:
							$tit = "交友购买VIP订单";
							break;
						case 1:
							$tit = "交友购买金币";
							break;
						case 2:
							$tit = "交友购买牵线";
							break;
					}
				}
				//跳转至第三方支付页面
				createPayForm("dating", $ordernum, $payprice, $paytype, $tit);
			}else{
				$param = array(
					"type" => 0
				);
				$body = serialize($param);
				$date = GetMkTime(time());

				$archives = $dsql->SetQuery("INSERT INTO `#@__pay_log` (`ordertype`, `ordernum`, `uid`, `body`, `amount`, `paytype`, `state`, `pubdate`) VALUES ('dating', '$ordernum', '$userid', '$body', 0, '$paytype', 1, $date)");
				$dsql->dsqlOper($archives, "results");

				//执行支付成功的操作
				$this->param = array(
					"paytype" => $paytype,
					"ordernum" => $ordernum
				);
				$this->paySuccess();

				$param = array(
					"service"  => "dating",
					"template" => "payreturn",
					"ordernum" => $ordernum
				);
				if($furl){
					$param['param'] = "furl=".$furl;
				}
				$url = getUrlPath($param);
				header("location:".$url);
			}
		}
	}


	/**
	 * 支付成功
	 */
	public function paySuccess(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();

		$param = $this->param;
		if(!empty($param)){
			global $dsql;
			global $cfg_secureAccess;
			global $cfg_basehost;

			$paytype        = $param['paytype'];
			$ordernum       = $param['ordernum'];
			$date           = GetMkTime(time());

			//查询订单信息
			$archive = $dsql->SetQuery("SELECT `body`, `amount`, `uid` FROM `#@__pay_log` WHERE `ordertype` = 'dating' AND `ordernum` = '$ordernum' AND `state` = 1");
			$results = $dsql->dsqlOper($archive, "results");
			if(!$results) return;
			$orderDetail = $results[0];
			$userid = $orderDetail['uid'];

			$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_order` WHERE `ordernum` = '$ordernum'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){

				$ret        = $ret[0];
				$title      = $ret['title'];
				$uid        = $ret['uid'];
				$type       = $ret['type'];
				$price      = $ret['price'];
				$count      = $ret['count'];
				$totalPrice = $ret['totalPrice'];
				$paydate    = $ret['paydate'];
				$param      = $ret['param'] ? unserialize($ret['param']) : array();

				$param['oid'] = $ret['id'];

				if($paydate == 0){
					$payprice = $orderDetail['amount'];
					$balance  = $totalPrice - $payprice;

					// 余额支付部分
					if($balance > 0){
						$sql = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - $balance WHERE `id` = $userid");
						$res = $dsql->dsqlOper($sql, "update");
					}

					$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$totalPrice', '$title', '$date')");
					$dsql->dsqlOper($archives, "update");

					//更新订单状态
					$archives = $dsql->SetQuery("UPDATE `#@__dating_order` SET `orderstate` = 1, `payprice` = '$payprice', `paytype` = '$paytype', `paydate` = '$date' WHERE `ordernum` = '$ordernum'");
					$dsql->dsqlOper($archives, "update");

					// 更多操作
					$param['uid'] = $uid;
					$param['totalPrice'] = $totalPrice;
					$this->paySuccessOpera($type, $param);

				}
			}


		}
	}

	/**
	 * 支付成功后更多操作
	 */
	private function paySuccessOpera($type = '', $param = array()){
		global $dsql;

		$oid        = $param['oid'];
		$uid        = $param['uid'];
		$totalPrice = $param['totalPrice'];

		// 购买VIP
		if($type == 0){
			$config = $this->config();

			$lastlevel   = $param['lastlevel'];
			$lastexpired = $param['lastexpired'];
			$level       = $param['level'];
			$month       = $param['month'];

			$sql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__dating_member` WHERE `id` = $uid AND `level` = $lastlevel AND `expired` = $lastexpired");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){

				if($level == $lastlevel){
					$date = date("Y-m-d H:i:s", $lastexpired);
					$newDate = strtotime("{$date}+{$month} month");
				}else{
					$newDate = strtotime("+{$month} month");
				}
				$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `level` = '$level', `expired` = '$newDate' WHERE `id` = $uid");
				$dsql->dsqlOper($sql, "udpate");

				$hnid = $ret[0]['company'];
				$sql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__dating_member` WHERE `id` = $hnid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$storeid = $ret[0]['company'];
				}else{
					$hnid = 0;
				}
				// 红娘提成
				if($hnid){

					$extRatio = $config['extractRatio']['buyVip']['hn1'];
					$extMoney = sprintf('%.2f', $totalPrice * $extRatio / 100);
					if($extMoney > 0){
						// 创建记录
						$type     = 1;
						$category = 'vip';
						$oid      = 0;
						$pubdate  = GetMkTime(time());
						$ordernum = create_ordernum();
						$puid     = $uid;
						$amount   = $totalPrice;
						$title    = "购买VIP";

						$touid = $hnid;
						$fuid = $uid;
						$sql = $dsql->SetQuery("INSERT INTO `#@__dating_money` (`uid`, `fuid`, `puid`, `type`, `category`, `title`, `oid`, `ordernum`, `amount`, `extRatio`, `pubdate`) VALUES ('$touid', '$fuid', '$puid', '$type', '$category', '$title', '$oid', '$ordernum', '$amount', '$extRatio', '$pubdate')");
						$ret = $dsql->dsqlOper($sql, "lastid");

						// 如果红娘有店铺，把提成打入店铺账户，否则打入红娘账户
						if($storeid){
							$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + '$extMoney' WHERE `id` = $storeid");
							$dsql->dsqlOper($sql, "update");
						}else{
							$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + '$extMoney' WHERE `id` = $hnid");
							$dsql->dsqlOper($sql, "update");
						}

					}
				}
			}


		// 购买金币
		}elseif($type == 1){

			$count       = $param['count'];

			$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + $count WHERE `id` = $uid");
			$dsql->dsqlOper($sql, "udpate");

		// 购买牵线
		}elseif($type == 2){

			$month       = $param['month'];
			$count       = $param['count'];

			$sql = $dsql->SetQuery("SELECT `company`, `lead`, `leadExpired` FROM `#@__dating_member` WHERE `id` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$hnid = $ret[0]['company'];
				$newLead = $count;
				// 已经有牵线次数，判断是否过期
				if($ret[0]['lead'] && $ret[0]['leadExpired']){
					$now = GetMkTime(time());
					// 已过期：清零
					if($now > $ret[0]['leadExpired']){
						$newDate = strtotime("+{$month} month");
					// 未过期：续期
					}else{
						$newLead = $ret[0]['lead'] + $count;
						$date = date("Y-m-d H:i:s", $ret[0]['leadExpired']);
						$newDate = strtotime("{$date}+{$month} month");
					}
				}else{
					$newDate = strtotime("+{$month} month");
				}
				$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `lead` = '$newLead', `leadExpired` = '$newDate'  WHERE `id` = $uid");
				$dsql->dsqlOper($sql, "udpate");
			}


		}
	}

	/**
	 * 购买聊天钥匙
	 */
	public function buyKey(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();

		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		$param = $this->param;
		$count = $param['count'];
		if(empty($count)) return array("state" => 200, "info" => "请选择购买的钥匙数量");

		$sql = $dsql->SetQuery("SELECT `id`, `money` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];

			$config = $this->config();
			$price = $config['keyPrice'];
			$totalPrice = $price * $count;

			if($ret[0]['money'] < $totalPrice){
				return array("state" => 200, "info" => "金币不足，请先充值");
			}else{
				$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` - $totalPrice, `key_buy` = `key_buy` + $count WHERE `userid` = $userid  AND `type` = 0");
				$ret = $dsql->dsqlOper($sql, "update");
				if($ret == "ok"){
					// 创建订单
					$ordernum = create_ordernum();
					$orderdate = GetMkTime(time());
					$paytype = "money";
					$title = "购买".$count."把聊天钥匙";
					$param = array();
					$param = serialize($param);

					$sql = $dsql->SetQuery("INSERT INTO `#@__dating_order` (`uid`, `type`, `title`, `ordernum`, `orderdate`, `paytype`, `paydate`, `orderstate`, `payprice`, `price`, `count`, `totalPrice`, `param`) VALUES ('$uid', '3', '$title', '$ordernum', '$orderdate', '$paytype', '$orderdate', '1', '0', '$price', '$count', '$totalPrice', '$param')");
					$ret = $dsql->dsqlOper($sql, "lastid");


					return "购买成功";
				}else{
					return array("state" => 200, "info" => "购买失败，请重试");
				}
			}

		}else{
			return array("state" => 200, "info" => "抱歉，您还没有注册交友用户");
		}
	}

	/**
	 * 发布动态
	 */
	public function putCircle(){
		global $dsql;
		global $userLogin;

		$content = "";

		$param   = $this->param;
		$app     = (int)$param['app'];
		$userid  = (int)$param['userid'];
		$content = $param['content'];
		$type    = (int)$param['type'];
		$file    = $param['file'];

		// return array("state" => 100, "info" => "ok");

		if($app){
			$appData = file_get_contents('php://input');
			if(empty($appData)){
				return array("state" => 200, "info" => '要发表的内容为空！');
			}

			$appData = json_decode($appData, true);
			// print_r($appData);die;
			if(!is_array($appData)){
				return array("state" => 200, "info" => '数据格式错误！');
			}
			$userid  = $appData['userid'];
			$content = $appData['title'];
			$type    = $appData['type'];
			$data    = $appData['data'];

			$file_ = array();
			foreach ($data as $key => $value) {
				$k_ = array_keys($value);
				$v_ = array_values($value);
				$k = $k_[0];
				$v = $v_[0];

				if($k == "image" || $k == "video"){
					array_push($file_, $v);
				}
				$file = join(",", $file_);
			}

		}else{
			$userid = $userLogin->getMemberID();
			if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");
		}
		$sql = $dsql->SetQuery("SELECT `id`, `level` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret) return array("state" => 200, "info" => "您还没有注册交友用户");

		$uid = $ret[0]['id'];

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['certifyState'] != 1 && $ret[0]['level'] == 0){
			return array("state" => 200, "info" => "您当前无法发布动态，请购买会员服务或完成实名认证");
		}

		if(empty($type)) return array("state" => 200, "info" => "参数错误");

		if($type == 1){
			if(empty($content)) return array("state" => 200, "info" => "请填写说说内容");
		}elseif($type == 2 || $type == 3){
			if(empty($file)){
				if(empty($content)){
					return array("state" => 200, "info" => "发布内容为空");
				}
				$type = 1;
			}
		}else{
			return array("state" => 200, "info" => "不支持的类型");
		}


		$pubdate = GetMkTime(time());

		$sql = $dsql->SetQuery("INSERT INTO `#@__dating_circle` (`uid`, `type`, `content`, `file`, `pubdate`, `zan`, `zan_user`) VALUES ('$uid', '$type', '$content', '$file', '$pubdate', '0', '')");
		$ret = $dsql->dsqlOper($sql, "lastid");
		if(is_numeric($ret)){
			return "发布成功";
		}else{
			return array("state" => 200, "info" => "发布失败");
		}

	}

	/**
	 * 动态列表
	 */
	public function circleList(){
		global $dsql;
		global $userLogin;
		global $langData;
		$where = "";
		$param = $this->param;
		$u = (int)$param['u'];
		$uid = (int)$param['uid'];
		$page     = (int)$this->param['page'];
		$pageSize = (int)$this->param['pageSize'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$userid = $userLogin->getMemberID();

		if($u){
			if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");
		}else{
			if($uid){
				$where .= " AND `uid` = $uid";
			}
		}

		$mid = 0;
		$lng = $lat = 0;
		if($userid > 0){
			$sql = $dsql->SetQuery("SELECT `id` ,`lng`, `lat` FROM `#@__dating_member` WHERE `type` = 0 AND `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$mid = $ret[0]['id'];
				$lng = $ret[0]['lng'];
				$lat = $ret[0]['lat'];
			}
		}

		// 定向隐藏动态
		if($uid){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `type` = 11 AND `uto` = $mid AND `ufrom` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$where .= " AND 1 = 2";
			}
		}else{
			// $sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `type` = 11 AND `uto` = $mid");
			// $ret = $dsql->dsqlOper($sql, "results");
			// if($ret){
			// 	global $data;
			// 	$data = "";
			// 	$ids = array_reverse(parent_foreach($ret, "id"));

			// }
			if($mid){
				$where .= " AND `uid` NOT IN (SELECT `ufrom` FROM `#@__dating_visit` WHERE `type` = 11 AND `uto` = $mid)";
			}
		}

		//总条数
		$archives = $dsql->SetQuery("SELECT COUNT(`id`) c FROM `#@__dating_circle` WHERE 1 = 1".$where);
		$results = $dsql->dsqlOper($archives, "results");
		$totalCount = $results[0]['c'];
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_circle` WHERE 1 = 1".$where);

		$atpage = $pageSize*($page-1);
		$where = " ORDER BY `id` DESC LIMIT $atpage, $pageSize";

		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();

		if($results){
			$today = date("Ymd");
			foreach ($results as $key => $value) {
				$list[$key]['id']      = $value['id'];
				$list[$key]['uid']     = $value['uid'];
				$list[$key]['type']    = $value['type'];
				$list[$key]['pubdate'] = $value['pubdate'];
				$list[$key]['content'] = $value['content'];
				$list[$key]['zan']     = $value['zan'];

				$day = date("Ymd", $value['pubdate']);
				$list[$key]['time'] = $today == $day ? date("H:i", $value['pubdate']) : date("Y-m-d H:i", $value['pubdate']);
				$list[$key]['floorTime'] = FloorTime(time() - $val['pubdate']);

				$list[$key]['time_d'] = date("m/d", $value['pubdate']);

				if($value['type'] != 0){
					$file = $value['file'];
					$file_arr = array();
					if($file){
						$file = explode(",", $file);
						foreach ($file as $k => $v) {
							$file_arr[$k] = getFilePath($v);
						}
					}
					$list[$key]['file'] = $file_arr;
				}else{
					$list[$key]['file'] = "";
				}

				$this->param = $value['uid'];
				$user = $this->memberInfo();
				$list[$key]['user'] = $user;


				$juli = '';
				$zan_has = 0;
				$v1 = $v2 = $v3 = 0;

				// 是否点赞
				$zan_user = $value['zan_user'];
				if($zan_user){
					$zan_user = explode(",", $zan_user);
					if(in_array($mid, $zan_user)){
						$zan_has = 1;
					}
				}

				// 不是当前登陆用户
				if($mid && $mid != $value['uid']){

					// 定向隐身
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `type` = 10 AND `ufrom` = ".$value['uid']." AND `uto` = $mid");
					$ret = $dsql->dsqlOper($sql, "results");
					if(!$ret){

						// 计算距离
						if($lng && $lat && $user['lng'] && $user['lat']){
							$juli = getDistance($lng, $lat, $user['lng'], $user['lat']);
							$juli = $juli > 1000 ? (sprintf("%.1f", $juli / 1000) . $langData['siteConfig'][13][23]) : ($juli . $langData['siteConfig'][13][22]);  //距离   //千米  //米
						}
					}

					//是否已经打过招呼
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$mid' AND `uto` = ".$value['uid']." AND `type` = 3");
					$v1 = $dsql->dsqlOper($sql, "totalCount");

					//是否已经关注
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_visit` WHERE `ufrom` = '$mid' AND `uto` = ".$value['uid']." AND `type` = 2");
					$v2 = $dsql->dsqlOper($sql, "totalCount");

					//是否已经牵线
					$sql = $dsql->SetQuery("SELECT `state` FROM `#@__dating_lead` WHERE `ufrom` = '$mid' AND `uto` = ".$value['uid']);
					$res = $dsql->dsqlOper($sql, "results");
					if($res){
						$v3 = $res[0]['state'];
					}

				}
				$list[$key]['juli'] = $juli;

				$list[$key]['zan_has'] = $zan_has;
				$list[$key]['visit'] = $v1;
				$list[$key]['follow'] = $v2;
				$list[$key]['lead'] = $v3;


			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);

	}


	/**
	 * 动态相册图片列表
	 */
	public function circleFileList(){
		global $dsql;
		global $userLogin;
		$param = $this->param;
		$id    = (int)$param['id'];

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");
		if(empty($id)) return array("state" => 200, "info" => "参数错误");

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = 0 AND `userid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => "error");
		}

		$sql = $dsql->SetQuery("SELECT `file`, `type`, `zan_user` FROM `#@__dating_circle` WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$list = array();
			$type = $ret[0]['type'];
			$file = $ret[0]['file'];

			// 是否点赞
			$zan_has = 0;
			$zan_user = $ret[0]['zan_user'];
			if($zan_user){
				$zan_user = explode(",", $zan_user);
				if(in_array($uid, $zan_user)){
					$zan_has = 1;
				}
			}
			if($file){
				$file = explode(",", $file);
				foreach ($file as $key => $value) {
					$list[$key]['zan_has'] = $zan_has;
					$list[$key]['path']    = getFilePath($value);
				}
				return $list;
			}else{
				return array("state" => 200, "info" => "附件为空");
			}
		}else{
			return array("state" => 200, "info" => "信息不存在");
		}
	}


	/**
	 * 动态点赞/取消
	 */
	public function circleOper(){
		global $dsql;
		global $userLogin;
		$param = $this->param;
		$id = (int)$param['id'];
		$type = $param['type'];

		$userid = $userLogin->getMemberID();

		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");
		if(empty($id)) return array("state" => 200, "info" => "参数错误");

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `type` = 0 AND `userid` = $userid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => "error");
		}

		$has = false;
		$sql = $dsql->SetQuery("SELECT `id`, `uid`, `zan_user`, `type`, `file` FROM `#@__dating_circle` WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			if($type == "del"){
				$sql = $dsql->SetQuery("DELETE FROM `#@__dating_circle` WHERE `id` = $id");
				$res = $dsql->dsqlOper($sql, "update");
				if($res == "ok"){
					if($ret[0]['type'] == 2 || $ret[0]['type'] ==3){
						$deltype = $ret[0]['type'] == 1 ? "delAtlas" : "delVideo";
						foreach ($res[0]['file'] as $k => $v) {
							delPicFile($v, "delAtlas", "dating");
						}
					}
					return "操作成功";
				}else{
					return array("state" => 200, "info" => "操作失败");
				}
			}

			$zan_user = $ret[0]['zan_user'];
			$num = 1;
			if($zan_user){
				$zan_user = explode(",", $zan_user);
				$count_ = count($zan_user);
				if(in_array($uid, $zan_user)){
					$num = -1;
					$zan_user = array_diff($zan_user, array($uid));
				}else{
					$zan_user[] = $uid;
				}
			}else{
				$zan_user = array($uid);
			}
			$sql = $dsql->SetQuery("UPDATE `#@__dating_circle` SET `zan` = `zan` + ".$num.", `zan_user` = '".join(",", $zan_user)."' WHERE `id` = $id");
			$ret = $dsql->dsqlOper($sql, "update");
			if($ret == "ok"){
				return '操作成功';
			}else{
				return array("state" => 200, "info" => "操作失败");
			}
		}else{
			return array("state" => 200, "info" => "信息不存在");
		}

	}

	/**
	 * 封面列表
	 */
	public function coverList(){
		global $dsql;

		$list = array();
		$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_coverbg`");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			foreach ($ret as $key => $value) {
				$list[$key]['id'] = $value['id'];
				$list[$key]['title'] = $value['title'];
				$list[$key]['litpic'] = $value['litpic'];

				$pic = $value['litpic'] ? getFilePath($value['litpic']) : "";
				$list[$key]['small'] = $pic ? changeFileSize(array("url" => $pic, "type" => "small")) : "";
				$list[$key]['large'] = $pic ? changeFileSize(array("url" => $pic, "type" => "large")) : "";
			}
		}

		return $list;
	}

	/**
	 * 礼物列表
	 */
	public function giftList(){
		global $dsql;

		$list = array();
		$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_gift` ORDER BY `weight` DESC");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$config = $this->config();
			foreach ($ret as $key => $value) {
				$list[$key]['id'] = $value['id'];
				$list[$key]['title'] = $value['title'];
				$list[$key]['price'] = $value['price'];

				$pic = $value['litpic'] ? getFilePath($value['litpic']) : "";
				$list[$key]['small'] = $pic;
				$list[$key]['large'] = $pic;
			}
		}

		return $list;
	}

	/**
	 * 送礼物
	 */
	public function putGift(){
		global $dsql;
		global $userLogin;

		$param = $this->param;

		$id     = (int)$param['id'];
		$gid    = (int)$param['gid'];
		$userid = $userLogin->getMemberID();

		if(!empty($id) && !empty($gid) && $userid > -1){

			//获取登录会员的交友ID
			$sql = $dsql->SetQuery("SELECT `id`, `money`, `company` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid = $ret[0]['id'];
				$money = $ret[0]['money'];
				$hnid = $ret[0]['company'];
			}else{
				return array("state" => 200, "info" => '您还没有注册交友用户');
			}

			if($uid == $id){
				return array("state" => 200, "info" => '自己不能给自己送礼物哦~');
			}
			$sql = $dsql->SetQuery("SELECT `company` FROM `#@__dating_member` WHERE `id` = $id AND `type` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$hnid2 = $ret[0]['company'];
			}else{
				return array("state" => 200, "info" => '送礼物的用户不存在~');
			}

			// 验证礼物
			$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_gift` WHERE `id` = '$gid'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$price = $ret[0]['price'];
				if($money < $money){
					return array("state" => 200, "info" => '您的余额不足，请先充值或选择其它礼物');
				}
			}else{
				return array("state" => 200, "info" => '礼物不存在');
			}

			$archives = $dsql->SetQuery("INSERT INTO `#@__dating_gift_put` (`ufrom`, `uto`, `gid`, `price`, `pubdate`, `read`) VALUES ('$uid', '$id', '$gid', '$price', ".GetMkTime(time()).", '0')");
			$ret = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($ret)){

				// 更新余额
				$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` - $price WHERE `id` = $uid");
				$dsql->dsqlOper($sql, "update");

				$config = $this->config();
				if($config['extractRatio']){

					// 创建记录
					$type     = 1;
					$category = 'gift';
					$oid      = 0;
					$pubdate  = GetMkTime(time());
					$ordernum = create_ordernum();
					$puid     = $uid;

					$amount   = $price;

					// 主动方红娘
					$sql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__dating_member` WHERE `id` = $hnid");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$storeid1 = $ret[0]['company'];
					}else{
						$hnid = 0;
					}
					if($hnid){
						$extRatio = $config['extractRatio']['sendGift']['hn1'];
						$extMoney = $price * $extRatio / 100;
						if($extMoney > 0){
							$fuid  = $uid;
							$title = "送礼物";

							$touid = $hnid;
							$sql = $dsql->SetQuery("INSERT INTO `#@__dating_money` (`uid`, `fuid`, `puid`, `type`, `category`, `title`, `oid`, `ordernum`, `amount`, `extRatio`, `pubdate`) VALUES ('$touid', '$fuid', '$puid', '$type', '$category', '$title', '$oid', '$ordernum', '$amount', '$extRatio', '$pubdate')");
							$ret = $dsql->dsqlOper($sql, "lastid");

							// 如果红娘有店铺，把提成打入店铺账户，否则打入红娘账户
							if($storeid1){
								$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + '$extMoney' WHERE `id` = $storeid1");
								$dsql->dsqlOper($sql, "update");
							}else{
								$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + '$extMoney' WHERE `id` = $hnid");
								$dsql->dsqlOper($sql, "update");
							}

						}
					}
					// 被动方红娘
					$sql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__dating_member` WHERE `id` = $hnid2");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$storeid2 = $ret[0]['company'];
					}else{
						$hnid2 = 0;
					}
					if($hnid2){
						$extRatio = $config['extractRatio']['sendGift']['hn2'];
						$extMoney = $price * $extRatio / 100;
						if($extMoney > 0){
							$fuid     = $id;
							$title    = "收到礼物";

							$touid = $hnid2;
							$sql = $dsql->SetQuery("INSERT INTO `#@__dating_money` (`uid`, `fuid`, `puid`, `type`, `category`, `title`, `oid`, `ordernum`, `amount`, `extRatio`, `pubdate`) VALUES ('$touid', '$fuid', '$puid', '$type', '$category', '$title', '$oid', '$ordernum', '$amount', '$extRatio', '$pubdate')");
							$ret = $dsql->dsqlOper($sql, "lastid");

							// 如果红娘有店铺，把提成打入店铺账户，否则打入红娘账户
							if($storeid1){
								$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + '$extMoney' WHERE `id` = $storeid2");
								$dsql->dsqlOper($sql, "update");
							}else{
								$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + '$extMoney' WHERE `id` = $hnid2");
								$dsql->dsqlOper($sql, "update");
							}
						}
					}
					// 收礼物用户
					$extRatio = $config['extractRatio']['sendGift']['u2'];
					$extMoney = $price * $extRatio / 100;
					if($extMoney > 0){
						$title    = "收到礼物";
						// 操作余额
						$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + $extMoney WHERE `id` = $id");
						$dsql->dsqlOper($sql, "update");

						$touid = $id;
						$sql = $dsql->SetQuery("INSERT INTO `#@__dating_money` (`uid`, `fuid`, `puid`, `type`, `category`, `title`, `oid`, `ordernum`, `amount`, `extRatio`, `pubdate`) VALUES ('$touid', '$fuid', '$puid', '$type', '$category', '$title', '$oid', '$ordernum', '$amount', '$extRatio', '$pubdate')");
						$ret = $dsql->dsqlOper($sql, "lastid");
					}
				}

				return "赠送成功";
			}else{
				return array("state" => 200, "info" => "赠送失败");
			}

		}

	}


	/**
	 * 收到的礼物，送出的礼物
	 */
	public function myGift(){
		global $dsql;
		global $userLogin;
		$where = "";
		$param = $this->param;

		$u = (int)$param['u'];
		$uid = (int)$param['uid'];
		$type = (int)$param['type']; // 0:收到的1:送出的
		$groupby     = $this->param['groupby'];
		$page        = $this->param['page'];
		$pageSize    = $this->param['pageSize'];

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$loginUid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => "error");
		}
		if($type == 1){
			// 只能查看自己收到的礼物
			$where .= " AND `ufrom` = $loginUid";
			$group = " GROUP BY `uto`";
		}else{
			if($u){
				$where .= " AND `uto` = $loginUid";
			}else{
				if(empty($uid)) return array("state" => 200, "info" => "参数错误");
				$where .= " AND `uto` = $uid";
			}
			$group = " GROUP BY `ufrom`";
		}

		if($groupby == "gid"){
			$group = " GROUP BY `gid`";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_gift_put` WHERE 1 = 1".$where.$group);
		// echo $archives;die;
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

		if($results){

			$memberAll = array();
			$giftAll = array();

			foreach ($results as $key => $value) {

				// 按礼物分组
				if($groupby == "gid"){
					$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_gift` WHERE `id` = ".$value['gid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$gdetail = array();

						$gdetail['id'] = $gid;
						$gdetail['title'] = $ret[0]['title'];
						$gdetail['litpic'] = getFilePath($ret[0]['litpic']);
					}

					$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_gift_put` WHERE `uto` = $uid AND `gid` = ".$value['gid']);
					$count = $dsql->dsqlOper($sql, "totalCount");
					$gdetail['count'] = $count;
					$list[$key] = $gdetail;

				}else{

					$ufrom = $value['ufrom'];
					$uto = $value['uto'];
					$giftList = array();

					$otherUid = $type == 0 ? $ufrom : $uto;

					if(!isset($memberAll[$otherUid])){
						$this->param = $otherUid;
						$minfo = $this->memberInfo();
						if($minfo['state'] != 200){
							$memberAll[$otherUid] = $this->memberInfo();
						}else{
							break;
						}
					}else{
						$minfo = $memberAll[$otherUid];
					}

					$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_gift_put` WHERE `ufrom` = $ufrom AND `uto` = $uto GROUP BY `gid`");
					$ret = $dsql->dsqlOper($sql, "results");
					foreach ($ret as $k => $v) {
						$gid = $v['gid'];
						if(!isset($giftAll[$gid])){
							$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_gift` WHERE `id` = ".$v['gid']);
							$ret = $dsql->dsqlOper($sql, "results");
							if($ret){
								$gdetail = array();

								$gdetail['id'] = $gid;
								$gdetail['title'] = $ret[0]['title'];
								$gdetail['litpic'] = getFilePath($ret[0]['litpic']);

								$giftAll[$gid] = $gdetail;
							}
						}

						$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_gift_put` WHERE `ufrom` = $ufrom AND `uto` = $uto AND `gid` = $gid");
						$count = $dsql->dsqlOper($sql, "totalCount");

						$giftList[$k] = array(
								"id" => $v['id'],
								"gift" => $giftAll[$gid],
								"count" => $count,
								"member" => $memberAll[$ufrom]
							);
					}

					$list[$key]['ufrom'] = $ufrom;
					$list[$key]['uto'] = $uto;
					$list[$key]['gift'] = $giftList;
					$list[$key]['member'] = $minfo;

				}

			}
		}

		// print_r($list);
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
	 * 提交门店/红娘申请
	 */
	public function putApply(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;

		$type     = (int)$param['type']; //提交的红娘或门店
		$uto      = (int)$param['uto'];	//提交的红娘或门店
		$ufor     = (int)$param['ufor']; //提交给红娘时目标会员
		$realname = $param['realname'];
		$areaCode = $param['areaCode'];
		$mobile   = $param['mobile'];
		$code     = $param['code'];
		$money    = (int)$param['money'];
		$city     = (int)$param['city'];

		$userid   = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		if(empty($type)) return array("state" => 200, "info" => "参数错误");
		$tit = $type == 1 ? "红娘" : "门店";

		$userinfo = $userLogin->getMemberInfo();

		$did = $this->getDatingUid($userid);
		if($did < 0) return array("state" => 200, "info" => "此功能仅限交友注册用户使用");

		if(empty($uto)) return array("state" => 200, "info" => "未指定".$tit);


		if($type == 1 && $ufor){
			$sql = $dsql->SetQuery("SELECT `id`, `state`, `company`, `sex` FROM `#@__dating_member` WHERE `type` = 0 AND `id` = $ufor");
			$ret = $dsql->dsqlOper($sql, "results");
			if(!$ret){
				return array("state" => 200, "info" => "不存在");
			}else{
				$uinfo = $ret[0];
				if($ret[0]['state'] != 1){
					return array("state" => 200, "info" => "用户状态异常");
				}
				if($ret[0]['company'] == 0){
					return array("state" => 200, "info" => "用户没有所属红娘");
				}elseif($ret[0]['company'] != $uto){
					$uto = $ret[0]['company'];
				}
			}
		}

		$sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__dating_member` WHERE `type` = $type AND `id` = $uto");
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 200, "info" => $tit."不存在");
		}else{
			if($ret[0]['state'] != 1){
				return array("state" => 200, "info" => $tit."状态异常");
			}
		}

		if(empty($realname)) return array("state" => 200, "info" => "请填写您的真实姓名");

		if(!$userinfo['phoneCheck']){
			if(empty($mobile)) return array("state" => 200, "info" => "请填写手机号");
			if(empty($code)) return array("state" => 200, "info" => "请填写验证码");

			//非国际版不需要验证区域码
			$archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$international = $results[0]['international'];
				if(!$international){
					$areaCode = "";
				}
			}else{
				return array("state" => 200, "info" => '短信平台未配置，发送失败！');
			}

			//验证输入的验证码
			$archives = $dsql->SetQuery("SELECT `id`, `pubdate` FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'verify' AND `user` = '".$areaCode.$mobile."' AND `code` = '$code'");
			$results  = $dsql->dsqlOper($archives, "results");
			if(!$results){
				return array("state" => 200, "info" => $langData['siteConfig'][21][222]);
			}else{

				//5分钟有效期
				if($regtime - $results[0]['pubdate'] > 300) return array("state" => 200, "info" => $langData['siteConfig'][21][33]); //验证码已过期，请重新获取！

				//验证通过删除发送的验证码
				$archives = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `type` = 'phone' AND `lei` = 'verify' AND `user` = '".$areaCode.$mobile."' AND `code` = '$code'");
				$dsql->dsqlOper($archives, "update");
			}

			// 更新手机号验证状态
			$sql = $dsql->SetQuery("UPDATE `#@__member` SET `phone` = '$mobile', `phoneCheck` = 1, `realname` = '$realname' WHERE `id` = $userid");
			$dsql->dsqlOper($sql, "update");

		}else{
			$mobile = $userinfo['phone'];
		}


		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_apply` WHERE `ufrom` = $did AND `uto` = $uto AND `ufor` = '$ufor' AND `state` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			return array("state" => 200, "info" => "您已经向该".$tit."提交过申请，请耐心等待回复");
		}else{
			$sex = $uinfo['sex'];
			$pubdate = GetMktime(time());
			$sql = $dsql->SetQuery("INSERT INTO `#@__dating_apply` (`ufrom`, `uto`, `ufor`, `realname`, `mobile`, `sex`, `money`, `city`, `pubdate`, `state`, `dotime`, `read`) VALUES ('$did', '$uto', '$ufor', '$realname', '$mobile', '$sex', '$money', '$city', '$pubdate', '0', '0', '0')");
			$ret = $dsql->dsqlOper($sql, "lastid");
			if(is_numeric($ret)){
				return "提交成功，请耐心等待回复";
			}else{
				return array("state" => 200, "info" => "提交失败，请重试！");
			}
		}
	}

	/**
	 * 申请列表
	 */
	public function applyList(){
		global $dsql;
		global $userLogin;
		global $langData;

		$where = "";

		$param = $this->param;
		$type  = (int)$param['type'];
		$sex   = $param['sex'];
		$autoread = (int)$this->param['autoread'];
		$page     = $this->param['page'];
		$pageSize = $this->param['pageSize'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$userid   = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		if(empty($type)) return array("state" => 200, "info" => "参数错误");

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = $type");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => "error");
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_apply` WHERE `uto` = $uid".$where);

		$sql = $dsql->SetQuery($archives . " AND `sex` = 1 AND `read` = 0");
		$boyCount = $dsql->dsqlOper($sql, "totalCount");
		$sql = $dsql->SetQuery($archives . " AND `sex` = 0 AND `read` = 0");
		$girlCount = $dsql->dsqlOper($sql, "totalCount");

		if($sex != ""){
			$where .= " AND `sex` = $sex";
		}
		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__dating_apply` WHERE `uto` = $uid".$where);
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount,
			"boyCount" => $boyCount,
			"girlCount" => $girlCount,
		);

		$atpage = $pageSize*($page-1);
		$where .= " LIMIT $atpage, $pageSize";

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_apply` WHERE `uto` = $uid $where");
		$results = $dsql->dsqlOper($archives, "results");
		$list = array();
		if($results){
			$from = array();
			foreach ($results as $key => $value) {
				$list[$key]['id'] = $value['id'];
				$list[$key]['ufor'] = $value['ufor'];
				$list[$key]['realname'] = $value['realname'];
				$list[$key]['mobile'] = $value['mobile'];
				$list[$key]['sex'] = $value['sex'];
				$list[$key]['pubdate'] = $value['pubdate'];
				$list[$key]['state'] = $value['state'];
				$list[$key]['read'] = $value['read'];

				if(isset($from[$value['ufrom']])){
					$user = $from[$value['ufrom']];
				}else{
					$this->param = $value['ufrom'];
					$user = $this->memberInfo();
					$from[$value['ufrom']] = $user;
				}
				$list[$key]['ufrom'] = $user;

				$money = $value['money'];
				$city = $value['city'];

				if($money){
					$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__dating_item` WHERE `id` = $money");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$money = $ret[0]['typename'];
					}
				}
				if($city){
					global $data;
					$data = "";
					$addrName = getParentArr("site_area", $city);
					$addrName = array_reverse(parent_foreach($addrName, "typename"));
					$city = join("-", $addrName);
				}

				$list[$key]['money'] = $money;
				$list[$key]['city'] = $city;
			}
		}else{
			return array("state" => 200, "info" => '暂无相关数据！');
		}

		if($autoread){
			$sql = $dsql->SetQuery("UPDATE `#@__dating_apply` SET `read` = 1 WHERE `uto` = $uid AND `read` = 0");
			$dsql->dsqlOper($sql, "update");
		}

		return array("pageInfo" => $pageinfo, "list" => $list);

	}

	/**
	 * 申请标记为已读
	 */
	public function applyOper(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;
		$id    = (int)$param['id'];
		$type  = (int)$param['type'];

		$userid   = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");
		if(empty($id) || empty($type)) return array("state" => 200, "info" => "参数错误");

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = ".$type);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => "error");
		}

		$sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__dating_apply` WHERE `id` = $id AND `uto` = $uid");
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret){
			if($ret[0]['state'] != 0){
				return array("state" => 100, "info" => "has");
			}else{
				$pubdbate = GetMkTime(time());
				$sql = $dsql->SetQuery("UPDATE `#@__dating_apply` SET `state` = 1, `dotime` = '$pubdate' WHERE `id` = $id");
				$ret = $dsql->dsqlOper($sql, "update");
				if($ret == "ok"){
					return "操作成功";
				}else{
					return array("state" => 200, "info" => "操作失败！");
				}
			}
		}else{
			return array("state" => 200, "info" => "信息不存在或权限不足");
		}
	}

	/**
	 * 购买牵线
	 */
	public function buyLead(){
		global $dsql;
		global $userLogin;
		$param      = $this->param;
		$paytype    = $param['paytype'];
		$check      = $param['check'];
		$id         = (int)$param['id'];
		$qr         = (int)$param['qr'];
		$useBalance = (int)$param['useBalance'];

		$isPC = !isMobile();

		$userid = $userLogin->getMemberID();
		if($userid == -1){
			if($check){
				return array("state" => 200, "info" => "登陆超时，请重新登陆");
			}else{
				die("登陆超时，请重新登陆");
			}
		}else{
			$uid = $this->getDatingUid($userid);
			if($uid == 0){
				if($check){
					return array("state" => 200, "info" => "您还没有注册交友用户");
				}else{
					die("您还没有注册交友用户");
				}
			}
		}
		$userinfo = $userLogin->getMemberInfo();
		$usermoney = $userinfo['money'];

		$config = $this->config();
		$config = $config['leadPrice'];

		$price = $month = $count = 0;
		foreach ($config as $key => $value) {
			if($id == $key){
				$month = $value['month'];
				$price = $value['price'];
				$count = $value['count'];
				break;
			}
		}
		if($month == 0 || $price == 0 || $count == 0){
			if($check){
				return array("state" => 200, "info" => "参数错误或未配置价格信息");
			}else{
				die("参数错误或未配置价格信息");
			}
		}

		if($check){
			return "ok";
		}

		// 创建订单
		$title = "购买牵线".$month."个月共".$count."次";
		$ordernum = create_ordernum();
		$orderdate = GetMkTime(time());
		$totalPrice = $price;

		$param = array(
			"month" => $month,
			"count" => $count,
		);
		$param = serialize($param);
		$type = 2;

		// 删除未支付的订单
		$sql = $dsql->SetQuery("DELETE FROM `#@__dating_order` WHERE `type` = $type AND `uid` = $uid AND `orderstate` = 0");
		$dsql->dsqlOper($sql, "update");

		$sql = $dsql->SetQuery("INSERT INTO `#@__dating_order` (`uid`, `type`, `title`, `ordernum`, `orderdate`, `paytype`, `paydate`, `orderstate`, `payprice`, `price`, `count`, `totalPrice`, `param`) VALUES ('$uid', '$type', '$title', '$ordernum', '$orderdate', '', '0', '0', '0', '$price', '$count', '$totalPrice', '$param')");
		$ret = $dsql->dsqlOper($sql, "lastid");

		if($isPC){
			$balance = 0;
			if($useBalance && $usermoney > 0){
				$balance = $usermoney > $totalPrice ? $totalPrice : $usermoney;
				$payprice = $totalPrice - $balance;
			}else{
				$payprice = $totalPrice;
			}

			if($payprice > 0 && $qr){
				return	createPayForm("dating", $ordernum, $payprice, $paytype, '交友购买牵线');
			}else{
				return $ordernum;
			}

		}

		$param = array(
			"service" => "dating",
			"template" => "pay",
			"param" => "ordernum=".$ordernum,
		);
		$url = getUrlPath($param);
		header("location:".$url);

	}

	/**
	 * 发起牵线
	 */
	public function putLead(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$id    = (int)$param['id'];

		$userid = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => "登陆超时，请重新登陆");
		}else{
			$uid = $this->getDatingUid($userid);
			if($uid == 0){
				return array("state" => 200, "info" => "您还没有注册交友用户");
			}
		}

		if(empty($id)) return array("state" => 200, "info" => "参数错误");

		if($uid == $id) return array("state" => 200, "info" => "你真的要跟自己牵线么~，看看别人吧");

		$sql = $dsql->SetQuery("SELECT `lead`, `leadExpired` FROM `#@__dating_member` WHERE `id` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		$ret = $ret[0];
		if($ret['lead'] == 0) return array("state" => 200, "info" => "您的剩余牵线次数为0，请先购买");

		$now = GetMkTime(time());
		if($ret['leadExpired']){
			if($now > $ret['leadExpired']){
				return array("state" => 200, "info" => "您的牵线次数已到期，请重新购买");
			}
		}

		// 验证被牵线会员
		$sql = $dsql->SetQuery("SELECT `id`, `state`, `company` FROM `#@__dating_member` WHERE `id` = $id AND `state` = 1");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$hnid = $ret[0]['company'];
			if($hnid){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `id` = $hnid AND `state` = 1");
				$ret = $dsql->dsqlOper($sql, "results");
				if(!$ret){
					return array("state" => 200, "info" => "抱歉，该用户的所属红娘不存在或状态异常");
				}
			}else{
				return array("state" => 200, "info" => "抱歉，该用户的所属红娘不存在");
			}
		}else{
			return array("state" => 200, "info" => "抱歉，您要牵线的用户不存在");
		}
		// 验证是已有记录
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_lead` WHERE (`ufrom` = $uid AND `uto` = $id) || (`ufrom` = $id AND `uto` = $uid)");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret) return array("state" => 200, "info" => "您和该用户已经存在牵线申请");

		// 创建牵线记录
		$sql = $dsql->SetQuery("INSERT INTO `#@__dating_lead` (`ufrom`, `uto`, `hnid`, `pubdate`, `state`, `admin`, `dotime`, `new1`, `new2`, `new3`) VALUES ('$uid', '$id', '$hnid', '$now', '1', '0', '0', '0', '0', '0')");
		$ret = $dsql->dsqlOper($sql, "lastid");
		if(is_numeric($ret)){
			$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `lead` = `lead` - 1 WHERE `id` = $uid");
			$dsql->dsqlOper($sql, "update");
			return "提交成功！";
		}else{
			return array("state" => 200, "info" => "提交失败，请重试！");
		}

	}

	/**
	 * 牵线记录
	 */
	public function leadList(){
		global $dsql;
		global $userLogin;

		$where = "";

		$param = $this->param;
		$ishn     = (int)$this->param['ishn'];	//红娘管理
		$spec     = $this->param['spec'];	//主动被动
		$state    = (int)$this->param['state'];
		$page     = (int)$this->param['page'];
		$pageSize = (int)$this->param['pageSize'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$userid = $userLogin->getMemberID();
		if($userid == -1)	return array("state" => 200, "info" => "登陆超时，请重新登陆");

		$type = $ishn ? 1 : 0;
		$sql = $dsql->SetQuery("SELECT `id`, `type` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = $type");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => "操作权限错误");
		}

		$where1 = $where2 = "";// 新收到、新成功+新失败
		// 红娘
		if($type == 1){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `company` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				global $arr_data;
				$arr_data = "";
				$uidList = arr_foreach($ret);

				if($spec == "in"){
					$where .= " AND ( `uto` IN (".join(",", $uidList).") )";
				}elseif($spec == "out"){
					$where .= " AND ( `ufrom` IN (".join(",", $uidList).") )";
				}else{
					$where .= " AND ( `ufrom` IN (".join(",", $uidList).") OR `uto` IN (".join(",", $uidList).") )";
				}
				$where1 .= " AND `uto` IN (".join(",", $uidList).")";
				$where2 .= " AND `ufrom` IN (".join(",", $uidList).")";
			}else{
				$where .= " AND 1 = 2";
				$where1 .= " AND 1 = 2";
				$where2 .= " AND 1 = 2";
			}
		}else{
			if($spec == "in"){
				$where .= " AND ( `uto` = $uid )";
			}elseif($spec == "out"){
				$where .= " AND ( `ufrom` = $uid )";
			}else{
				$where .= " AND ( `ufrom` = $uid OR `uto` = $uid )";
			}
			$where1 .= " AND `uto` = $uid";
			$where2 .= " AND `ufrom` = $uid";
		}


		//新收到的牵线
		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE `state` = 1 AND `new1` = 0".$where1);
		// echo $archives;die;
		$newLoadCount = $dsql->dsqlOper($archives, "totalCount");

		//新成功的牵线
		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE `state` = 2 AND `new2` = 0".$where2);
		$newSuccCount = $dsql->dsqlOper($archives, "totalCount");

		//新失败的牵线
		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE `state` = 3 AND `new3` = 0".$where2);
		// echo $archives;die;
		$newFailCount = $dsql->dsqlOper($archives, "totalCount");

		if($state){
			$where .= " AND `state` = $state";
		}

		//总条数
		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE 1 = 1".$where);
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		// echo $archives;die;

		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount,
			"newLoadCount" => $newLoadCount,
			"newSuccCount" => $newSuccCount,
			"newFailCount" => $newFailCount,
		);

		$atpage = $pageSize*($page-1);
		$where = " ORDER BY `id` DESC LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where, "results");
		$list = array();

		if($results){
			foreach ($results as $key => $value) {
				$listIds[] = $value['id'];
				$list[$key]['id']      = (int)$value['id'];
				$list[$key]['ufrom']   = (int)$value['ufrom'];
				$list[$key]['uto']     = (int)$value['uto'];
				$list[$key]['hnid']    = (int)$value['hnid'];
				$list[$key]['pubdate'] = (int)$value['pubdate'];
				$list[$key]['state']   = (int)$value['state'];
				$list[$key]['admin']   = (int)$value['admin'];
				$list[$key]['dotime']  = (int)$value['dotime'];
				$list[$key]['new1']    = (int)$value['new1'];
				$list[$key]['new2']    = (int)$value['new2'];
				$list[$key]['new3']    = (int)$value['new3'];

				// 红娘
				if($type == 1){
					if(in_array($value['ufrom'], $uidList)){
						$list[$key]['zd'] = "ufrom";
					}else{
						$list[$key]['zd'] = "uto";
					}
					$this->param = $value['ufrom'];
					$list[$key]['ufromUser'] = $this->memberInfo();
					$this->param = $value['uto'];
					$list[$key]['utoUser'] = $this->memberInfo();
				}else{
					if($value['ufrom'] == $uid){
						$admin_iszd = true;
						$list[$key]['zd'] = 1;
						$this->param = $value['uto'];
					}elseif($value['uto'] == $uid){
						$list[$key]['zd'] = 0;
						$this->param = $value['ufrom'];
					}
					$list[$key]['user'] = $this->memberInfo();
				}

				// 判断是否已创建聊天
				$from = $value['ufrom'];
				$to = $value['uto'];
				if($type == 0){
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE ( (`from` = $from AND `to` = $to) OR (`from` = $to AND `to` = $from) )");
					$total = $dsql->dsqlOper($sql, "totalCount");
					$list[$key]['chat'] = $total ? 1 : 0;
				}else{
					// 红娘和发起方
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE ( (`from` = $uid AND `to` = $from) OR (`from` = $from AND `to` = $uid) )");
					$total = $dsql->dsqlOper($sql, "totalCount");
					$list[$key]['chat1'] = $total ? 1 : 0;
					// 红娘和接收方
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE ( (`from` = $uid AND `to` = $to) OR (`from` = $to AND `to` = $uid) )");
					$total = $dsql->dsqlOper($sql, "totalCount");
					$list[$key]['chat2'] = $total ? 1 : 0;
				}
			}
		}

		$field = "new".$state;
		$where = "";
		// 红娘
		if($state){
			if($type == 1){
				if($state == 1){
					$where = " `uto` IN (".join(",", $uidList).")";
				}else{
					$where = " `ufrom` IN (".join(",", $uidList).")";
				}
			}else{
				if($state == 1){
					$where = " `uto` = $uid";
				}else{
					$where = " `ufrom` = $uid";
				}
			}
			$sql = $dsql->SetQuery("UPDATE `#@__dating_lead` SET `".$field."` = 1 WHERE `".$field."` = 0 AND `state` = '$state' AND".$where);
			// echo $sql;
			$dsql->dsqlOper($sql, "update");
		}

		return array("pageInfo" => $pageinfo, "list" => $list);

	}

	/**
	 * 牵线操作
	 */
	public function leadOper(){
		global $dsql;
		global $userLogin;

		$param   = $this->param;
		$id      = (int)$this->param['id'];			//牵线id
		$operUid = (int)$this->param['operUid'];	//操作的用户id 当红娘操作时需要此参数
		$state   = (int)$this->param['state'];

		$userid = $userLogin->getMemberID();
		if($userid == -1)	return array("state" => 200, "info" => "登陆超时，请重新登陆");

		// state=4时取消牵线
		if(empty($id) || empty($state) || $state < 1 || $state > 4) return array("state" => 200, "info" => "参数错误");

		if(empty($operUid)){
			$where = " AND `type` = 0";
		}else{
			$where = " AND `type` = 1";
		}
		$sql = $dsql->SetQuery("SELECT `id`, `type`, `company` FROM `#@__dating_member` WHERE `userid` = $userid $where");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid     = $ret[0]['id'];
			$company = $ret[0]['company'];
			$type    = $ret[0]['type'];
		}else{
			return array("state" => 200, "info" => "操作权限错误");
		}

		// 验证牵线信息
		$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_lead` WHERE `id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$detail = $ret[0];
			if($state == $detail['state']) return array("state" => 200, "info" => "状态未变化");
		}else{
			return array("state" => 200, "info" => "牵线不存在");
		}

		// 是否主动
		$zd = false;

		// 红娘
		if($type == 1){
			if(empty($operUid)) return array("state" => 200, "info" => "参数错误");
			// 主动
			if($operUid == $detail['ufrom']){
				$zd = true;
			// 被动
			}elseif($operUid == $detail['uto']){

			}else{
				return array("state" => 200, "info" => "操作权限错误1");
			}
			if($uid != $detail['hnid']) return array("state" => 200, "info" => "操作权限错误2");

		}else{
			if($uid == $detail['ufrom']) $zd = true;
		}

		if($zd){
			if($state != 4) return array("state" => 200, "info" => "操作权限错误3");
		}else{
			// if($state != 2 && $state != 3) return array("state" => 200, "info" => "操作权限错误e");
		}

		$now = GetMkTime(time());

		if($state == 4){
			$sql = $dsql->SetQuery("DELETE FROM `#@__dating_lead` WHERE `id` = $id");
		}else{
			$sql = $dsql->SetQuery("UPDATE `#@__dating_lead` SET `state` = '$state', `admin` = '$uid', `dotime` = '$now' WHERE `id` = $id");
		}
		$ret = $dsql->dsqlOper($sql, "update");
		if($ret == "ok"){
			// 牵线次数+1
			if($state == 4){
				$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `lead` = `lead` + 1 WHERE `id` = ".$detail['ufrom']);
				$dsql->dsqlOper($sql, "update");
			}

			// 牵线成功
			if($state == 2){
				$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_order` WHERE `uid` = ".$detail['ufrom']." AND `type` = 2 AND `orderstate` = 1 ORDER BY `id` DESC LIMIT 0, 1");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){

					$config = $this->config();
					if($config['extractRatio']){

						// 创建订单
						$type     = 1;
						$category = 'lead';
						$oid      = 0;
						$pubdate  = GetMkTime(time());
						$date     = $orderdate;
						$ordernum = create_ordernum();

						$amount  = $ret[0]['totalPrice'] / $ret[0]['count'];

						// 主动方红娘
						$extRatio = $config['extractRatio']['buyLead']['hn1'];
						$extMoney = $amount * $extRatio / 100;
						if($extMoney > 0){
							$title = "红娘服务-主动方";
							// 操作余额
							$sql = $dsql->SetQuery("SELECT `company` FROM `#@__dating_member` WHERE `id` = ".$detail['ufrom']." AND `company` != 0");
							$ret = $dsql->dsqlOper($sql, "results");
							if($ret){

								$hnid = $ret[0]['company'];

								$sql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__dating_member` WHERE `id` = $hnid");
								$ret = $dsql->dsqlOper($sql, "results");
								if($ret){
									$storeid = $ret[0]['company'];
								}else{
									$hnid = 0;
								}

								$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + $extMoney WHERE `id` = $hnid");
								$dsql->dsqlOper($sql, "update");

								if($hnid){
									$touid = $hnid;
									$fuid = $detail['ufrom'];
									$puid = $detail['ufrom'];
									$sql = $dsql->SetQuery("INSERT INTO `#@__dating_money` (`uid`, `fuid`, `puid`, `type`, `category`, `title`, `oid`, `ordernum`, `amount`, `extRatio`, `pubdate`) VALUES ('$touid', '$fuid', '$puid', '$type', '$category', '$title', '$oid', '$ordernum', '$amount', '$extRatio', '$pubdate')");
									$ret = $dsql->dsqlOper($sql, "lastid");

									// 如果红娘有店铺，把提成打入店铺账户，否则打入红娘账户
									if($storeid){
										$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + '$extMoney' WHERE `id` = $storeid");
										$dsql->dsqlOper($sql, "update");
									}else{
										$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + '$extMoney' WHERE `id` = $hnid");
										$dsql->dsqlOper($sql, "update");
									}
								}


							}
						}
						// 被动方红娘
						$extRatio = $config['extractRatio']['buyLead']['hn2'];
						$extMoney = $amount * $extRatio / 100;
						if($extMoney > 0){
							$title = "红娘服务-被动方";
							// 操作余额
							$hnid = $type == 1 ? $uid : $company;

							$sql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__dating_member` WHERE `id` = $hnid");
							$ret = $dsql->dsqlOper($sql, "results");
							if($ret){
								$storeid = $ret[0]['company'];
							}else{
								$hnid = 0;
							}
							if($hnid){

								$touid = $hnid;
								$fuid = $detail['uto'];
								$puid = $detail['ufrom'];
								$sql = $dsql->SetQuery("INSERT INTO `#@__dating_money` (`uid`, `fuid`, `puid`, `type`, `category`, `title`, `oid`, `ordernum`, `amount`, `extRatio`, `pubdate`) VALUES ('$touid', '$fuid', '$puid', '$type', '$category', '$title', '$oid', '$ordernum', '$amount', '$extRatio', '$pubdate')");
								$ret = $dsql->dsqlOper($sql, "lastid");

								// 如果红娘有店铺，把提成打入店铺账户，否则打入红娘账户
								if($storeid){
									$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + '$extMoney' WHERE `id` = $storeid");
									$dsql->dsqlOper($sql, "update");
								}else{
									$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` + '$extMoney' WHERE `id` = $hnid");
									$dsql->dsqlOper($sql, "update");
								}
							}
						}
					}

				}
			}

			return "操作成功";
		}else{
			return array("state" => 200, "info" => "操作失败！");
		}


	}


	/**
	 * 收入列表
	 */
	public function incomeList(){
		global $dsql;
		global $userLogin;

		$category = "";

		$param     = $this->param;
		$spec      = $this->param['spec'];	// 收入支出
		$keywords  = $this->param['keywords'];	// 收入支出
		$type      = (int)$this->param['type']; // 会员类型
		$category  = $this->param['category'];
		$hnid      = (int)$this->param['hnid'];//指定红娘
		$autoread  = (int)$this->param['autoread'];
		$page      = (int)$this->param['page'];
		$pageSize  = (int)$this->param['pageSize'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$userid = $userLogin->getMemberID();
		if($userid == -1)	return array("state" => 200, "info" => "登陆超时，请重新登陆");

		// state=4时取消牵线
		if(empty($type)) return array("state" => 200, "info" => "参数错误");

		// 如果获取指定红娘的收入明细，则登陆用户须为该红娘的所属门店
		$type_ = $hnid ? 2 : $type;
		$sql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = $type_");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			if($hnid){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `company` = $uid AND `id` = $hnid AND `type` = 1");
				$res = $dsql->dsqlOper($sql, "results");
				if(!$res){
					return array("state" => 200, "info" => "操作权限错误");
				}
			}
			$uid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => "操作权限错误");
		}

		$where = "";

		// 红娘
		if($type == 1){
			if($hnid){
				$where .= " AND `uid` = $hnid";
			}else{
				$where .= " AND `uid` = $uid";
			}
		}else{
			$where .= " AND 1 = 2";
		}

		if($spec == "in"){
			$where .= " AND `type` = 1";
		}elseif($spec == "out"){
			$where .= " AND `type` = 0";
		}

		if($category != ''){
			$where .= " AND `category` = '$category'";
		}

		if($keywords){
			$where .= " AND `ordernum` LIKE '%$keywords%'";
		}

		$archives = $dsql->SetQuery("SELECT SUM(`amount` * `extRatio` / 100) total FROM `#@__dating_money` WHERE 1 = 1".$where);
		$results = $dsql->dsqlOper($archives, "results");
		$totalMoney = sprintf("%.2f", $results[0]['total']);


		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_money` WHERE 1 = 1".$where);
		// echo $archives;die;
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		// if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount,
			"totalMoney" => $totalMoney,
		);

		$atpage = $pageSize*($page-1);
		$where2 = " LIMIT $atpage, $pageSize";

		$list = array();
		$results = $dsql->dsqlOper($archives.$where2, "results");
		if($results){
			foreach ($results as $key => $value) {
				$list[$key]['id']         = $value['id'];
				$list[$key]['title']      = $value['title'];
				$list[$key]['ordernum']   = $value['ordernum'];
				$list[$key]['pubdate']    = $value['pubdate'];
				$list[$key]['amount']     = $value['amount'];
				$list[$key]['extRatio']   = $value['extRatio'];
				$list[$key]['extMoney']   = sprintf('%.2f', $value['amount'] * $value['extRatio'] / 100);

				$this->param = $value['fuid'];
				$user = $this->memberInfo();

				$list[$key]['user'] = $user;
			}
		}

		if($list && $autoread){
			$sql = $dsql->SetQuery("UPDATE `#@__dating_money` SET `extnew".$type."` = 1 WHERE 1 = 1".$where);
			$dsql->dsqlOper($sql, "update");
		}

		return array("pageInfo" => $pageinfo, "list" => $list);

	}

	/**
	 * 店铺收入列表
	 */
	public function incomeStoreList(){
		global $dsql;
		global $userLogin;

		$category = "";

		$param     = $this->param;
		$spec      = $this->param['spec'];	// 收入支出
		// $type      = (int)$this->param['type']; // 会员类型
		$category  = $this->param['category'];
		$autoread  = (int)$this->param['autoread'];
		$ordertype = $this->param['ordertype'];
		$keywords  = $this->param['keywords'];
		$page      = (int)$this->param['page'];
		$pageSize  = (int)$this->param['pageSize'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$userid = $userLogin->getMemberID();
		if($userid == -1)	return array("state" => 200, "info" => "登陆超时，请重新登陆");

		$type = 2;
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = $type");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => "操作权限错误");
		}

		$where = $where1 = $where2 = $where3 = "";


		if($spec == "in"){
			$where .= " AND `type` = 1";
		}elseif($spec == "out"){
			$where .= " AND `type` = 0";
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `company` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			global $arr_data;
			$arr_data = "";
			$userIds = arr_foreach($ret);
			$where1 .= " AND `uid` IN (".join(",", $userIds).")";
		}else{
			$where1 .= " AND 1 = 2";
		}

		if($category != ''){
			$where .= " AND `category` = '$category'";
		}
		if($keywords){
			if(is_numeric($keywords)){
				$where .=" AND (`uid` = $keywords || `fuid` = $keywords || `puid` = $keywords)";
			}else{
				$where .= " AND (`title` LIKE '%$keywords%' || `ordernum` LIKE '%$keywords%')";
			}
		}

		// 总收入
		$archives = $dsql->SetQuery("SELECT SUM(`amount` * `extRatio` / 100) total FROM `#@__dating_money` WHERE 1 = 1".$where.$where1);
		$results = $dsql->dsqlOper($archives, "results");
		$totalMoney = sprintf("%.2f", $results[0]['total']);


		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__dating_money` WHERE 1 = 1".$where.$where1." GROUP BY `uid`");
		// echo $archives;die;
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		// if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount,
			"totalMoney" => $totalMoney,
		);

		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_money` WHERE 1 = 1".$where.$where1." GROUP BY `uid`");
		$atpage = $pageSize*($page-1);
		$where2 = " LIMIT $atpage, $pageSize";

		$list = array();
		$results = $dsql->dsqlOper($archives.$where2, "results");
		if($results){
			foreach ($results as $key => $value) {

				$list[$key]['uid']         = $value['uid'];

				$sql = $dsql->SetQuery("SELECT SUM(`amount` * `extRatio` / 100) total FROM `#@__dating_money` WHERE `uid` = ".$value['uid'].$where);
				$ret = $dsql->dsqlOper($sql, "results");
				$total = sprintf("%.2f", $ret[0]['total']);

				$list[$key]['totalMoney'] = $total;

				$param = array(
					"service" => "dating",
					"template" => "store_income_hn",
					"id" => $value['uid']
				);
				$list[$key]['url'] = getUrlPath($param);

				$this->param = $value['uid'];
				$user = $this->hnInfo();

				$list[$key]['user'] = $user;
			}
		}

		if($list && $autoread){
			$sql = $dsql->SetQuery("UPDATE `#@__dating_money` SET `extnew".$type."` = 1 WHERE 1 = 1".$where);
			$dsql->dsqlOper($sql, "update");
		}

		return array("pageInfo" => $pageinfo, "list" => $list);

	}

	/**
	 * 提现前计算
	 */
	public function putForward(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$type = (int)$param['utype'];

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		$sql = $dsql->SetQuery("SELECT `id`, `state`, `money`, `company` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = $type");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			if($ret[0]['state'] != 1){
				return array("state" => 200, "info" => "用户状态异常");
			}
			// 店铺下的红娘不能提现
			if($type == 1 && $ret[0]['company']){
				return array("state" => 200, "info" => "抱歉，您无权进行此操作，请联系店铺管理员");
			}
			$ret = $ret[0];
		}
		$config = $this->config();
		$goldRatio = $config['goldRatio'];
		$withdrawRatio = $config['withdrawRatio'];

		$have = $ret['money'];
		$money = sprintf("%.2f", $have / $goldRatio);
		$maxPutMoney = $money / (1 + $withdrawRatio / 100) ;
		$maxPutMoney = sprintf("%.2f", $maxPutMoney);

		$data = array(
			"id" => $ret['id'],
			"moneyName" => $config['goldName'],
			"have" => $have,	//拥有金币
			"money" => $money,	//兑换成人民币
			"goldRatio" => $config['goldRatio'],	//兑换比例
			"maxPutMoney" => $maxPutMoney,	// 最多提现金额
			"minPutMoney" => $config['withdrawMinAmount'],	//最少提现金额
			"info" => "提现手续费：".$config['withdrawRatio']."%"
		);
		// print_r($data);
		return $data;

	}

	/**
	 * app上传语音和视频认证后，查询上传结果
	 */
	public function checkUpload(){
		global $dsql;
		global $userLogin;

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "error");

		$where = "";

		$type = $this->param['type'];
		$date = (int)$this->param['date'];

		if(empty($type)) return array("state" => 200, "info" => "error1");

		if($type != "voice" && $type != "video") return array("state" => 200, "info" => "error2");

		if($type == "voice"){
			$filename = 'datingAuthVideo_'.$userid.'.mp3';
		}elseif($type == "video"){
			$filename = 'datingAuthVideo_'.$userid.'.mp4';
		}else{
			return array("state" => 200, "info" => "error3");
		}

		$now = time();
		$date = $date > $now ? $now : $date;
		if($date){
			$where .= " AND `pubdate` > '$date'";
		}

		$sql = $dsql->SetQuery("SELECT `id`, `path` FROM `#@__attachment` WHERE `filename` = '$filename'".$where." ORDER BY `id` DESC");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$id = $ret[0]['id'];
			$RenrenCrypt = new RenrenCrypt();
			$url = base64_encode($RenrenCrypt->php_encrypt($id));

			global $cfg_basehost;
			global $cfg_secureAccess;
			global $cfg_uploadDir;
			$turl = $cfg_secureAccess.$cfg_basehost.$cfg_uploadDir.$ret[0]['path'];

			$config = $this->config();

			if($type == "voice"){
				$check = (int)$config['voiceswitch'];
			}elseif($type == "video"){
				$check = (int)$config['videoswitch'];
			}



			$state = $check ? 0 : 1;
			$info = $state ? "认证成功" : "上传成功，等待认证！";

			// 删除之前的数据
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__attachment` WHERE `filename` = '$filename' AND `id` < $id");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				foreach ($ret as $key => $value) {
					$RenrenCrypt = new RenrenCrypt();
					$path = base64_encode($RenrenCrypt->php_encrypt($value['id']));
					if($type == "voice"){
						delPicFile($path, "delfile", "dating");
					}else{
						delPicFile($path, "delvideo", "dating");
					}
				}
			}

			$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `my_".$type."` = '$url', `my_".$type."_state` = $state WHERE `userid` = $userid AND `type` = 0");
			$dsql->dsqlOper($sql, "update");

			return array("type" => $type, "state" => $state, "url" => $url, "turl" => $turl, "info" => $info);

		}else{
			// return $sql;
			return "not";
		}

	}


	/**
	 * app端获取聊天双方信息
	 */
	public function getChatUserInfo(){
		global $dsql;
		global $cfg_secureAccess;
		global $cfg_basehost;
		$param = $this->param;

		if(!is_array($param)) return array("state" => 200, "info" => '格式错误！');

		$from = (int)$param['from'];
		$to   = (int)$param['to'];

		if(empty($from)) return array("state" => 200, "info" => '发送人会员ID不得为空！');
		if(empty($to)) return array("state" => 200, "info" => '接收人会员ID不得为空！');

		$sql = $dsql->SetQuery("SELECT `id`, `type`, `userid` FROM `#@__dating_member` WHERE `id` = $from");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$type = $ret[0]['type'];
			$userid = $ret[0]['userid'];
			$this->param = $from;
			if($type == 0){
				$ufrom = $this->memberInfo();
			}elseif($type == 1){
				$ufrom = $this->hnInfo();
			}
		}else{
			return array("state" => 200, "info" => '发送人会员不存在！');
		}
		$sql = $dsql->SetQuery("SELECT `id`, `type` FROM `#@__dating_member` WHERE `id` = $to");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$type = $ret[0]['type'];
			$this->param = $to;
			if($type == 0){
				$uto = $this->memberInfo();
			}elseif($type == 1){
				$uto = $this->hnInfo();
			}
		}else{
			return array("state" => 200, "info" => '接收人会员不存在！');
		}

		$sendfile = $hideinfo = $hideto10 = $hideto11 = $hideto12 = $inventory = 0;
		$hidetext = "";
		if($ufrom['type'] == 0 && $uto['type'] == 0){
			if($ufrom['level']){
				$sendfile = 1;
				$hideinfo = 1;
			}else{
				$hidetext = "您还不是会员，不能使用此功能";
			}

			// 隐身设置
			$sql = $dsql->SetQuery("SELECT `id`, `type` FROM `#@__dating_visit` WHERE `ufrom` = $from AND `uto` = $to AND (`type` = 10 || `type` = 11 || `type` = 12)");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				foreach ($ret as $k => $v) {
					$f = "hideto".$v['type'];
					${$f} = 1;
				}
			}
			// 剩余聊天条数
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE ( (`from` = $from AND `to` = $to) OR (`from` = $to AND `to` = $from ) ) AND `pid` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$hasChat = 1;
				$aid = $ret[0]['id'];

				// 聊天条数
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_chat` WHERE `pid` = $aid AND `from` = $from");
				$hasChatCount = $dsql->dsqlOper($sql, "totalCount");

				$this->param = array("userid" => $userid, "common" => true);
				$config = $this->getMemberLevelInfo();

				$privilege = $config['privilege'];
				if($config['id'] == 1){
					if($privilege['chat'] == 0 || $hasChatCount >= $privilege['chat']){
						$inventory = 0;
					}else{
						$inventory = $privilege['chat'] - $hasChatCount;
					}
				}else{
					if($privilege['chat']){
						if($hasChatCount >= $privilege['chat']){
							$inventory = 0;
						}else{
							$inventory = $privilege['chat'] - $hasChatCount;
						}
					}else{
						$inventory = -1;
					}
				}

			}
		}else{
			$inventory = -1;
			$sendfile  = 1;
			$hideinfo  = 0;
			$hidetext  = "当前会话无法使用此功能";
		}

		$info = array(
			"visitor" => array(
				"id" => $from,
				"nickname" => $ufrom['nickname'],
				"photo" => $ufrom['photo'] ? $ufrom['photo'] : ($cfg_secureAccess . $cfg_basehost . '/static/images/default_user.jpg'),
				"level" => (int)$ufrom['level'],
				"inventory" => $inventory,
			),
			"master" => array(
				"id" => $to,
				"nickname" => $uto['nickname'],
				"photo" => $uto['photo'] ? $uto['photo'] : ($cfg_secureAccess . $cfg_basehost . '/static/images/default_user.jpg'),
			),
			"msgtomaxtext"  => "您的聊天条数已达上限，请升级会员",
			"sendfiletext"  => "您还不是会员，不能发送图片和语音",
			"sendfile" => $sendfile,	// 是否可以发送附件
			"hideinfo" => $hideinfo,  	// 是否可以设置隐藏信息
	        "hidetext" => $hidetext,	// 无法使用隐藏功能是的提示信息
	        "hideto10" => $hideto10, 	// 当前定向隐身状态
	        "hideto11" => $hideto11, 	// 当前隐藏动态状态
	        "hideto12" => $hideto12, 	// 当前隐藏相册状态
		);

		return $info;

	}

	/**
	 * 我的订单
	 */
	public function orderList(){
		global $dsql;
		global $userLogin;

		$where = $type = "";

		$param    = $this->param;
		$type     = $param['type'];
		$page     = (int)$param['page'];
		$pageSize = (int)$param['pageSize'];

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		if($type != ''){
			$type = (int)$type;
			$where .= " AND `type` = $type";
		}

		$sql = $dsql->SetQuery("SELECT * FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 0");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$uid = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => "error");
		}
		$archives = $dsql->SetQuery("SELECT * FROM `#@__dating_order` WHERE `uid` = $uid".$where);
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

		if($results){
			foreach ($results as $key => $value) {
				$list[$key]['id'] = $value['id'];
				$list[$key]['title'] = $value['title'];
				$list[$key]['ordernum'] = $value['ordernum'];
				$list[$key]['type'] = $value['type'];
				$list[$key]['orderdate'] = $value['orderdate'];
				$list[$key]['paytype'] = $value['paytype'];
				$list[$key]['paydate'] = $value['paydate'];
				$list[$key]['orderstate'] = $value['orderstate'];
				$list[$key]['payprice'] = $value['payprice'];
				$list[$key]['price'] = $value['price'];
				$list[$key]['count'] = $value['count'];
				$list[$key]['totalPrice'] = $value['totalPrice'];
				$list[$key]['param'] = $value['param'] ? unserialize($value['param']) : array();
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);

	}

	/**
	 * 相亲交友现金提现
	 */
	public function withdraw(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$type = (int)$param['utype'];
		$bank     = $param['bank'];
		$cardnum  = $param['cardnum'];
		$cardname = $param['cardname'];
		$amount   = $param['amount'];
		$date     = GetMkTime(time());

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		if(empty($bank) || empty($cardnum) || empty($cardname) || empty($amount)) return array("state" => 200, "info" => '请填写完整！');

		$config = $this->config();
		if($amount < $config['withdrawMinAmount']) return array("state" => 200, "info" => '抱歉，最低提现金额为'.$config['withdrawMinAmount'].'！');

		$sql = $dsql->SetQuery("SELECT `id`, `state`, `money`, `company` FROM `#@__dating_member` WHERE `userid` = '$userid' AND `type` = '$type'");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			if($ret[0]['state'] != 1){
				return array("state" => 200, "info" => "用户状态异常");
			}
			// 店铺下的红娘不能提现
			if($type == 1 && $ret[0]['company']){
				return array("state" => 200, "info" => "抱歉，您无权进行此操作，请联系店铺管理员");
			}
			$ret = $ret[0];
		}

		//判断银行卡是否存在
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_withdraw_card` WHERE `uid` = '$userid' AND `bank` = '$bank' AND `cardnum` = '$cardnum' AND `cardname` = '$cardname'");
		$result = $dsql->dsqlOper($sql, "results");
		if($result){
			$cid = $result[0]['id'];
		}else{
			//添加银行卡
			$sql = $dsql->SetQuery("INSERT INTO `#@__member_withdraw_card` (`uid`, `bank`, `cardnum`, `cardname`, `date`) VALUES ('$userid', '$bank', '$cardnum', '$cardname', '$date')");
			$cid = $dsql->dsqlOper($sql, "lastid");
		}

		if(is_numeric($cid)){
			//生成提现记录
			$sql = $dsql->SetQuery("INSERT INTO `#@__member_putforward` (`userid`, `utype`, `module`, `type`, `amount`, `pubdate`, `order_id`, `cardname`,`bank`) VALUES ('$userid', '$type', 'dating', 'bank', '$amount', '$date', '$cardnum', '$cardname','$bank')");
			$wid = $dsql->dsqlOper($sql, "lastid");
			//$sql = $dsql->SetQuery("INSERT INTO `#@__member_withdraw` (`uid`, `bank`, `cardnum`, `cardname`, `amount`, `tdate`, `state`,`type`) VALUES ('$userid', '$bank', '$cardnum', '$cardname', '$amount', '$date', 0,'0')");
			//$wid = $dsql->dsqlOper($sql, "lastid");

			if(is_numeric($wid)){


				$goldRatio = $config['goldRatio'];
				$withdrawRatio = $config['withdrawRatio'];

				// 扣除的金币 = 人民币 * 兑换比例 + 提现手续费
				$money         = $amount * $goldRatio * (1 + $withdrawRatio / 100);

				$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `money` = `money` - $money WHERE `id` = ".$ret['id']);
				$dsql->dsqlOper($sql, "update");

				return $wid;
			}else{
				return array("state" => 200, "info" => "提现失败，请稍后再试！");
			}
		}else{
			return array("state" => 200, "info" => "提现失败，请稍后再试！");
		}
	}

	// 删除红娘
	public function delHn(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();

		$fields = array();

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}
		$userinfo = $userLogin->getMemberInfo();

		$param = $this->param;
		$id    = (int)$param['id'];

		if(empty($id)) return array("state" => 200, "info" => '参数错误');


		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 2 AND `state` = 1");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$storeId = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => '权限不足，操作失败');
		}

		$sql = $dsql->SetQuery("DELETE FROM `#@__dating_member` WHERE `id` = $id AND `type` = 1 AND `company` = $storeId");
		$res = $dsql->dsqlOper($sql, "update");
		if($res == "ok"){
			return "操作成功";
		}else{
			return array("state" => 200, "info" => '操作失败');
		}

	}

	// 删除用户
	public function delUser(){
		global $dsql;
		global $userLogin;
		$userid = $userLogin->getMemberID();

		$fields = array();

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}
		$userinfo = $userLogin->getMemberInfo();

		$param = $this->param;
		$id    = (int)$param['id'];

		if(empty($id)) return array("state" => 200, "info" => '参数错误');


		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $userid AND `type` = 1 AND `state` = 1");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$hnId = $ret[0]['id'];
		}else{
			return array("state" => 200, "info" => '权限不足，操作失败');
		}

		$sql = $dsql->SetQuery("DELETE FROM `#@__dating_member` WHERE `id` = $id AND `type` = 0 AND `company` = $hnId");
		$res = $dsql->dsqlOper($sql, "update");
		if($res == "ok"){
			return "操作成功";
		}else{
			return array("state" => 200, "info" => '操作失败');
		}

	}

	// 快速注册
	public function fastRegister(){
		global $dsql;
		global $userLogin;

		$uid = $userLogin->getMemberID();
		if($uid > 0) return array("state" => 200, "info" => '您已经登陆');

		$param = $this->param;

		$sex      = (int)$param['sex'];
		$year     = (int)$param['year'];
		$month    = (int)$param['month'];
		$day      = (int)$param['day'];
		$cityid   = (int)$param['cityid'];
		$addrid   = (int)$param['addrid'];
		$marriage = (int)$param['marriage'];
		$nickname = $param['nickname'];
		$areaCode = $param['areaCode'];

		$month = $month < 10 ? "0".$month : $month;
		$day = $day < 10 ? "0".$day : $day;

		$birthday = strtotime($year."-".$month."-".$day);

		if(empty($year) || empty($month) || empty($day)) return array("state" => 200, "info" => '请填写出生日期');
		if(empty($addrid)) return array("state" => 200, "info" => '请选择工作地区');

		//新用户注册
		//提供初始密码
        // $passwdInit = '111111';
        // $passwd = $userLogin->_getSaltedHash($passwdInit);
        $passwd = "";
        $mtype = 1;
        $times = time();
        $ip = GetIP();
		$ipaddr = getIpAddr($ip);
        //保存到主表
        $username = "user_".$times;
        $nickname   = $nickname ? $nickname : 'user_'.substr($times, -4);
        $areaCode = empty($areaCode) ? "86" : $areaCode;

        $archives = $dsql->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `areaCode`, `phone`, `phoneCheck`, `regtime`, `regip`, `regipaddr`, `state`, `purviews`) VALUES ('$mtype', '$username', '$passwd', '$nickname', '$areaCode', '', '0', '$times', '$ip', '$ipaddr', '1', '')");
        $uid = $dsql->dsqlOper($archives, "lastid");
        if(is_numeric($uid)){
	        $user = array(
	        	"id" => $uid,
	        	"state" => 1,
	        	"password" => $passwd,
	        );
			$res = $userLogin->memberLoginCheckForSmsCode($user);

			$param = array(
				"service" => "member",
				"type" => "user",
				"template" => "dating",
				"action" => "mydata",
			);
			$url = getUrlPath($param);

			$data = array(
				"userid" => $uid,
				"type" => 0,
				"nickname" => $nickname,
				"phone" => '',
				"company" => 0,
				"entrust" => 0,
				"dateswitch" => 1,
				"birthday" => $birthday,
				"marriage" => $marriage,
				"state" => 1,
			);
			$r = $this->joinInsert($data);
			if($r){
				$uid = $r;

				$numid = (int)date('y');
				if($r < 1000){
					$numid .= substr(time(), -4);
				}
				$numid .= $r;
				$sql = $dsql->SetQuery("UPDATE `#@__dating_member` SET `numid` = '$numid' WHERE `id` = $r");
				$dsql->dsqlOper($sql, "update");

				return array("code" => 100, "info" => "注册成功", "url" => $url);
			}else{
				return array("code" => 200, "info" => "交友会员注册失败，请到会员中心添加", "url" => $url);
			}

		}else{
			return array("state" => 200, "info" => '注册失败，请稍候重试！');
		}

	}

	/**
     * 情感课堂首页 更多
     */
    public function getNewsType(){
        global $dsql;
        if(!empty($this->param)){
            if(!is_array($this->param)){
                return array("state" => 200, "info" => '格式错误！');
            }else{
                $page     = (int)$this->param['page'];
                $pageSize = (int)$this->param['pageSize'];
            }
        }

        $page = $page ? $page : 1;
        $pageSize = $pageSize ? $pageSize : 10;
        $typeList = $this->newsType();

		$html = '';
        if($typeList){
        	$n = ($page-1) * $pageSize;
        	foreach ($typeList as $key => $value) {

        		$idx = $n + $key + 1;

	        	$typeid = $value['id'];
				$html .='<div class="pubbox mainbox">';

				$html .='<div class="leadertop fn-clear">';
				$html .='<span class="ltit" data-id="'.$value['id'].'">'.$value['typename'].'</span>';
				$param = array(
					"service"     => "dating",
					"template"    => "news-list",
					"id"          => $typeid
				);
				$url        = getUrlPath($param);

				$html.='<a href="'.$url.'" class="news_more">更多 <em>>></em></a>';
				$html.='</div>';

				$html.='<div class="contbox fn-clear">';

				//左边广告
				$html.='<div class="nl fn-left">';
				$param = array("title" => '交友电脑端_模板二_情感课堂__广告'.$idx.'_1');
				$html.='<div class="adbox">';
				$html.= getMyAd($param);
				$html.='</div>';
				$param = array("title" => '交友电脑端_模板二_情感课堂__广告'.$idx.'_2');
				$html.='<div class="adbox">';
				$html.= getMyAd($param);
				$html.='</div>';
				$html.='</div>';


				$html.='<div class="nm fn-left">';

				$this->param = array("typeid" => $typeid, "page" => 1, "pageSize" => 18);
				$news = $this->news();

				$html1 = $html2 = $html3 = $html4 = $html5 = $html6 = '';
				if($news && isset($news['list'])){
					$news = $news['list'];
					foreach($news as $key=>$val){
						$param = array(
							"service"     => "house",
							"template"    => "detail",
							"id"          => $val['id']
						);
						$url        = getUrlPath($param);
						if($key==0){
							$html1='<h2><a href="'.$url.'" target="_blank">'.$val['title'].'</a></h2>';
						}elseif($key==5){
							$html3='<h2><a href="'.$url.'" target="_blank">'.$val['title'].'</a></h2>';
						}elseif($key>0 && $key<5){
							$html2 .='<li><a href="'.$url.'" target="_blank"><em>•</em> '.$val['title'].'</a></li>';
						}elseif($key>5 && $key<10){
							$html4 .='<li><a href="'.$url.'" target="_blank"><em>•</em> '.$val['title'].'</a></li>';
						}elseif($key==10){
							$litpic = !empty($val['litpic']) ? getFilePath($val['litpic']) : "";
							$html5 = '<div class="imgbox"><a href="'.$url.'"><img src="'.$litpic.'" target="_blank" alt=""><div class="imgInfo"><h3>'.$val['title'].'</h3><div class="bg"></div></div></a></div>';
						}else{
							$html6 .='<li><a href="'.$url.'" target="_blank"><em>•</em> '.$val['title'].'</a></li>';
						}
					}
				}

				$html .= $html1 . '<ul>' .$html2 . '</ul>' . $html3 . '<ul>' . $html4 .'</ul>';
				$html.='</div>';

				//右边新闻
				$html.='<div class="nr fn-left">';
				$html .=$html5 . '<ul>' .$html6 . '</ul>';
				$html.='</div>';

				$html.='</div>';
				$html.='</div>';
			}
			echo $html;exit;
        }else{
        	echo 1;exit;
        }

        //return $ret;
    }

    /**
     * 获取统计数据
     */
    public function getTotal(){
    	global $dsql;
    	global $userLogin;
    	$uid = $userLogin->getMemberID();

    	if($uid == -1) return array("state" => 200, "info" => "参数错误");

    	$sql = $dsql->SetQuery("SELECT `id` FROM `#@__dating_member` WHERE `userid` = $uid AND `type` = 0");
    	$res = $dsql->dsqlOper($sql, "results");
    	if(!$res) return array("state" => 200, "info" => "用户不存在");

    	$did = $res[0]['id'];

    	$data = array();

		$sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__dating_album` WHERE `uid` = ".$uid);
		$ret = $dsql->dsqlOper($sql, "results");
		$data['album'] = $ret[0]['total'];

		$sql = $dsql->SetQuery("SELECT COUNT(v1.`id`) total FROM `#@__dating_visit` v1 WHERE v1.`ufrom` = $did AND v1.`type` = 2 AND v1.`uto`
				IN (SELECT v2.`ufrom` FROM `#@__dating_visit` v2 WHERE v2.`uto` = $did and v2.`type` = 2)
			");
		$ret = $dsql->dsqlOper($sql, "results");
		$data['friend'] = $ret[0]['total'];

		$sql = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__dating_gift_put` WHERE `uto` = $did");
		$ret = $dsql->dsqlOper($sql, "results");
		$data['gift'] = $ret[0]['total'];

		return $data;
    }

}
