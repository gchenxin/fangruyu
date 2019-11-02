<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 旅游模块API接口
 *
 * @version        $Id: travel.class.php 2019-5-20 下午17:10:21 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2019, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class travel {
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
     * 旅游信息基本参数
     * @return array
     */
	public function config(){

		require(HUONIAOINC."/config/travel.inc.php");

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
		global $hotline_config;           //咨询热线配置
		// global $customHotline;            //咨询热线
		// global $customTemplate;           //模板风格
		// global $custom_map;               //自定义地图
		// global $custom_hotel_atlasMax;    //酒店场地图集数量限制
		// global $custom_sy_atlasMax;       //摄影公司图集数量限制
		// global $custom_hq_atlasMax;       //婚庆公司图集数量限制
		// global $custom_sy_zp_atlasMax;    //摄影作品图集数量限制
		// global $custom_sy_al_atlasMax;    //摄影案例图集数量限制
		// global $custom_hq_zp_atlasMax;    //婚庆作品图集数量限制

		global $cfg_map;                  //系统默认地图

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

		// $domainInfo = getDomain('travel', 'config');
		// $customChannelDomain = $domainInfo['domain'];
		// if($customSubDomain == 0){
		// 	$customChannelDomain = "http://".$customChannelDomain;
		// }elseif($customSubDomain == 1){
		// 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
		// }elseif($customSubDomain == 2){
		// 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
		// }

		// include HUONIAOINC.'/siteModuleDomain.inc.php';
		$customChannelDomain = getDomainFullUrl('travel', $customSubDomain);

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
				}elseif($param == "hotel_atlasMax"){
					$return['hotel_atlasMax'] = $custom_hotel_atlasMax;
				}elseif($param == "sy_atlasMax"){
					$return['sy_atlasMax'] = $custom_sy_atlasMax;
				}elseif($param == "hq_atlasMax"){
					$return['hq_atlasMax'] = $custom_hq_atlasMax;
				}elseif($param == "sy_zp_atlasMax"){
					$return['sy_zp_atlasMax'] = $custom_sy_zp_atlasMax;
				}elseif($param == "sy_al_atlasMax"){
					$return['sy_al_atlasMax'] = $custom_sy_al_atlasMax;
				}elseif($param == "hq_zp_atlasMax"){
					$return['hq_zp_atlasMax'] = $custom_hq_zp_atlasMax;
				}elseif($param == "template"){
					$return['template'] = $customTemplate;
				}elseif($param == "touchTemplate"){
					$return['touchTemplate'] = $customTouchTemplate;
				}elseif($param == "map"){
					$return['map'] = $custom_map;
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
			$return['template']      = $customTemplate;
			$return['touchTemplate'] = $customTouchTemplate;
			$return['map']           = $custom_map;
			$return['softSize']      = $custom_softSize;
			$return['softType']      = $custom_softType;
			$return['thumbSize']     = $custom_thumbSize;
			$return['thumbType']     = $custom_thumbType;
			$return['storeatlasMax']          = $custom_store_atlasMax;
			$return['travelhotelatlasMax']    = $custom_travelhotel_atlasMax;
			$return['travelticketatlasMax']   = $custom_travelticket_atlasMax;
			$return['travelstrategyatlasMax'] = $custom_travelstrategy_atlasMax;
			$return['travelrentcaratlasMax']  = $custom_travelrentcar_atlasMax;
			$return['travelvisaatlasMax']     = $custom_travelvisa_atlasMax;
			$return['travelagencyatlasMax']   = $custom_travelagency_atlasMax;

			$return['travelTrainCheck']   = $customtravelTrainCheck;
			$return['travelTrainTouchUrl']= $customtravelTrainTouchUrl;
			$return['travelPlaneCheck']   = $customtravelPlaneCheck;
			$return['travelPlaneTouchUrl']= $customtravelPlaneTouchUrl;


		}

		return $return;

	}

	/**
     * 旅游汽车分类
     * @return array
     */
	public function rentcartype(){
		global $dsql;
		global $langData;
		$type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][8][0]);//格式错误
			}else{
				$type     = (int)$this->param['type'];
				$value    = (int)$this->param['value'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}
		$results = $dsql->getTypeList($type, "travel_rentcartype", $son, $page, $pageSize);
		$list = array();
		if($results){
			if($value){
				foreach ($results as $key => $value) {
					$list[$key]['id']    = $value['id'];
					$list[$key]['value'] = $value['typename'];
				}
				return $list;
			}else{
				return $results;
			}
		}
	}

	/**
     * 旅游签证分类
     * @return array
     */
	public function visatype(){
		global $dsql;
		global $langData;
		$type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][8][0]);//格式错误
			}else{
				$type     = (int)$this->param['type'];
				$value    = (int)$this->param['value'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}
		$results = $dsql->getTypeList($type, "travel_visatype", $son, $page, $pageSize);
		$list = array();
		if($results){
			if($value){
				foreach ($results as $key => $value) {
					$list[$key]['id']    = $value['id'];
					$list[$key]['value'] = $value['typename'];
				}
				return $list;
			}else{
				return $results;
			}
		}
	}

	/**
     * 旅游攻略分类
     * @return array
     */
	public function strategytype(){
		global $dsql;
		global $langData;
		$type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][8][0]);//格式错误
			}else{
				$type     = (int)$this->param['type'];
				$value    = (int)$this->param['value'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}
		$results = $dsql->getTypeList($type, "travel_strategytype", $son, $page, $pageSize);
		$list = array();
		if($results){
			if($value){
				foreach ($results as $key => $value) {
					$list[$key]['id']    = $value['id'];
					$list[$key]['value'] = $value['typename'];
				}
				return $list;
			}else{
				return $results;
			}
		}
	}

	/**
     * 旅游签证所需材料
     * @return array
     */
	public function travelitem(){
		global $dsql;
		global $langData;
		$pageinfo = $list = array();
		$orderby = $page = $pageSize = $where = $where1 = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['car'][7][0]);//格式错误
			}else{
				$orderby  = $this->param['orderby'];
				$aid      = $this->param['aid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if($aid){
			$where = " AND `id` in ($aid)";
		}

		$order = " ORDER BY `weight` DESC, `pubdate` DESC, `id` DESC";

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT  `id`, `parentid`, `typename`, `weight`, `description`, `pubdate`, `typeid` FROM `#@__travelitem` WHERE 1=1 ".$where);

		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__travelitem` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("travel_item_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));

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

		$sql = $dsql->SetQuery($archives.$where1.$order.$where);
		$results = getCache("travel_item_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']    = $val['id'];
				$list[$key]['parentid']    = $val['parentid'];
				$list[$key]['typename']    = $val['typename'];
				$list[$key]['weight']      = $val['weight'];
				$list[$key]['pubdate']     = $val['pubdate'];
				$list[$key]['typeid']      = $val['typeid'];
				if($val['typeid']==1){
					$list[$key]['typeidname']= $langData['travel'][12][97];
				}elseif($val['typeid']==2){
					$list[$key]['typeidname']= $langData['travel'][12][98];
				}else{
					$list[$key]['typeidname']= $langData['travel'][12][99];
				}
				$list[$key]['description'] = $val['description'];
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
	 * 签证国家分类
	 */
	public function countrytype(){
		global $dsql;
		global $langData;
		$pageinfo = $list = array();
		$orderby = $page = $pageSize = $where = $where1 = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['car'][7][0]);//格式错误
			}else{
				$hot      = $this->param['hot'];
				$continent= $this->param['continent'];
				$typeid   = $this->param['typeid'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if(!empty($hot)){
			$where = " AND `hot` = 1";
		}

		if(!empty($continent)){
			$where = " AND `continent` = '$continent'";
		}

		if($typeid!=''){
			$where = " AND `typeid` = '$typeid'";
		}

		$order = " ORDER BY `weight` DESC, `pubdate` DESC, `id` DESC";

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		//首字母
		if($orderby == "1"){
			$order = " ORDER BY `py` DESC, `hot` DESC";
		//热门
		}elseif($orderby == "2"){
			$order = " ORDER BY `hot` DESC";
        }elseif($orderby == "3"){
			$order = " ORDER BY `py` ASC, `hot` DESC";
        }

		$archives = $dsql->SetQuery("SELECT  `id`, `parentid`, `typename`, `weight`, `pubdate`, `icon`, `pinyin`, `py`, `hot`, `price`, `continent`, `typeid` FROM `#@__travel_visacountrytype` WHERE 1=1 ".$where);


		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__travel_visacountrytype` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("travel_visacountrytype_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));

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
		$sql = $dsql->SetQuery($archives.$where1.$order.$where);
		$results = getCache("travel_visacountrytype_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']    = $val['id'];
				$list[$key]['parentid']    = $val['parentid'];
				$list[$key]['typename']    = $val['typename'];
				$list[$key]['weight']      = $val['weight'];
				$list[$key]['pubdate']     = $val['pubdate'];
				$list[$key]['price']       = $val['price'];
				$list[$key]['continent']   = $val['continent'];
				$list[$key]['typeid']      = $val['typeid'];
				$list[$key]['pinyin']      = $val['pinyin'];
				$list[$key]['py']          = $val['py'];
				$list[$key]['firstword']   = $val['py'] ? strtoupper(substr($val['py'], 0, 1)) : '';
				$list[$key]['hot']         = $val['hot'];
				$list[$key]['icon']        = empty($val['icon']) ? '' : getFilePath($val['icon']);
				$list[$key]['typeidname']  = $val['typeid']!='' ? $this->gettypename("typeid_type", $val['typeid']) : '';
				$list[$key]['continentname']  = $val['continent'] ? $this->gettypename("continent_type", $val['continent']) : '';
				$param = array(
					"service" => "travel",
					"template" => "visacountry-detail",
					"id" => $val['id']
				);
				$url = getUrlPath($param);
				$list[$key]['url'] = $url;
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
     * 签证国家详细
     * @return array
     */
	public function countryDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		$where = " AND `state` = 1";

		$archives = $dsql->SetQuery("SELECT `id`, `pubdate`, `typename`, `icon`, `pinyin`, `py`, `hot`, `continent`, `typeid`, `duration`, `condition`, `price`, `click`, `expenses`  FROM `#@__travel_visacountrytype` WHERE `id` = ".$id.$where);
		$results  = getCache("travel_visacountrytype_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]             = $results[0]['id'];
			$storeDetail["click"]          = $results[0]['click'];
			$storeDetail['pubdate']        = $results[0]['pubdate'];
			$storeDetail['typename']       = $results[0]['typename'];
			$storeDetail["icon"]           = $results[0]['icon'];
			$storeDetail["iconSource"]     = getFilePath($results[0]['icon']);
			$storeDetail["pinyin"]         = $results[0]['pinyin'];
			$storeDetail["py"]             = $results[0]['py'];
			$storeDetail["hot"]            = $results[0]['hot'];
			$storeDetail["continent"]      = $results[0]['continent'];
			$storeDetail['continentname']  = $results[0]['continent'] ? $this->gettypename("continent_type", $results[0]['continent']) : '';
			$storeDetail["typeid"]         = $results[0]['typeid'];
			$storeDetail['typeidname']     = $results[0]['typeid']!='' ? $this->gettypename("typeid_type", $results[0]['typeid']) : '';
			$storeDetail["duration"]       = $results[0]['duration'];
			$storeDetail["condition"]      = $results[0]['condition'];
			$storeDetail["price"]          = $results[0]['price'];
			$storeDetail["expenses"]       = $results[0]['expenses'];

			//验证是否已经收藏
			$collect = '';
			if($uid != -1){
				$params = array(
					"module" => "travel",
					"temp"   => "visacountry-detail",
					"type"   => "add",
					"id"     => $results[0]['id'],
					"check"  => 1
				);
				$collect = checkIsCollect($params);
			}
			$storeDetail['collect'] = $collect == "has" ? 1 : 0;
		}
		return $storeDetail;
	}

	/**
     * 信息地区
     * @return array
     */
    public function addr(){
        global $dsql;
        global $langData;
        $type = $page = $pageSize = $where = "";

        if (!empty($this->param)) {
            if (!is_array($this->param)) {
                return array("state" => 200, "info" => $langData['travel'][12][23]);
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
	 * 商家列表
	 */
	public function storeList(){
		global $dsql;
		global $langData;
		$pageinfo = $list = array();
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['travel'][12][13]);//格式错误
			}else{
				$search   = $this->param['search'];
				$addrid   = $this->param['addrid'];
				$orderby  = $this->param['orderby'];
				$max_longitude = $this->param['max_longitude'];
				$min_longitude = $this->param['min_longitude'];
				$max_latitude  = $this->param['max_latitude'];
				$min_latitude  = $this->param['min_latitude'];
				$lng      = $this->param['lng'];
				$lat      = $this->param['lat'];
				$u        = $this->param['u'];
				$filter   = $this->param['filter'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$where = " AND `state` = 1";

		$cityid = getCityId($this->param['cityid']);
		//遍历区域
        if($cityid){
            $where .= " AND `cityid` = '$cityid'";
		}

		if(!empty($filter)){
			$where .= " AND FIND_IN_SET('".$filter."', `bind_module`)";
		}

		if(!empty($addrid)){
			if($dsql->getTypeList($addrid, "site_area")){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($dsql->getTypeList($addrid, "site_area"));
				$lower = $addrid.",".join(',',$lower);
			}else{
				$lower = $addrid;
			}
			$where .= " AND `addrid` in ($lower)";
		}

		if(!empty($search)){

			siteSearchLog("travel", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__travel_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `user`.title like '%$search%' OR `store`.address like '%$search%'");
	        $results = $dsql->dsqlOper($userSql, "results");
	        foreach ($results as $key => $value) {
	            $sidArr[$key] = $value['id'];
	        }

	        if(!empty($sidArr)){
	            $where .= " AND (`title` like '%$search%' OR `address` like '%$search%' OR `userid` in (".join(",",$sidArr)."))";
	        }else{
	            $where .= " AND (`title` like '%$search%' OR `address` like '%$search%')";
	        }
		}

		//地图可视区域内
		if(!empty($max_longitude) && !empty($min_longitude) && !empty($max_latitude) && !empty($min_latitude)){
			$where .= " AND `lng` <= '".$max_longitude."' AND `lng` >= '".$min_longitude."' AND `lat` <= '".$max_latitude."' AND `lat` >= '".$min_latitude."'";
		}

		//查询距离
		if((!empty($lng))&&(!empty($lat))){
            $select="(2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*(".$lat."-`lat`)/360),2)+COS(3.1415926535898*".$lat."/180)* COS(`lat` * 3.1415926535898/180)*POW(SIN(3.1415926535898*(".$lng."-`lng`)/360),2))))*1000 AS distance,";
        }else{
            $select="";
        }

		//排序
        switch ($orderby){
            //浏览量
            case 1:
                $orderby_ = " ORDER BY `click` DESC, `weight` DESC, `id` DESC";
                break;
            //发布时间降序
            case 2:
                $orderby_ = " ORDER BY `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//推荐排序
			case 3:
                //$orderby_ = " ORDER BY `rec` DESC, `weight` DESC, `pubdate` DESC, `id` DESC";
				break;
			//距离排序
			case 4:
				if((!empty($lng))&&(!empty($lat))){
					$orderby_ = " ORDER BY distance ASC";
				}
				break;
            default:
                $orderby_ = " ORDER BY `weight` DESC, `id` DESC";
                break;
        }

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__travel_store` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("travel_store_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$archives = $dsql->SetQuery("SELECT `bind_module`, `title`, `pubdate`, `pics`, `video`, `license`, `lat`, `lng`, `id`,`userid`, `address`, `tel`, `addrid`, ".$select." `contact` FROM `#@__travel_store` WHERE 1 = 1".$where);
		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$sql = $dsql->SetQuery($archives.$orderby_.$where);
		$results = getCache("travel_store_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['address']   = $val['address'];
				$list[$key]['tel']       = $val['tel'];
				$list[$key]['lng']       = $val['lng'];
				$list[$key]['lat']       = $val['lat'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['bind_module']= $val['bind_module'];
				$list[$key]['contact']   = $val['contact'];
				$list[$key]['license']   = $val['license'];
				$list[$key]['video']     = $val['video'];
				$list[$key]['distance']  = $val['distance'] > 1000 ? sprintf("%.1f", $val['distance'] / 1000) . $langData['siteConfig'][13][23] : sprintf("%.1f", $val['distance']) . $langData['siteConfig'][13][22];  //距离   //千米  //米
				if(strpos($list[$key]['distance'],'千米')){
					$list[$key]['distance'] = str_replace("千米",'km',$list[$key]['distance']);
				}elseif(strpos($list[$key]['distance'],'米')){
					$list[$key]['distance'] = str_replace("米",'m',$list[$key]['distance']);
				}

				$bind_moduleArr  = array();
				$bind_moduleArr_ = $val['bind_module'] ? explode(',', $val['bind_module']) : array();
				if($bind_moduleArr_){
					foreach($bind_moduleArr_ as $k => $row){
						$bind_modulename = $this->gettypename("module_type", $row);
						$bind_moduleArr[$k] = array(
							"id" => $row,
							"val" => $bind_modulename
						);
					}
				}
				$list[$key]["bind_moduleArr"]  = $bind_moduleArr;
				$list[$key]["bind_moduleArr_"] = $bind_moduleArr_;

				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
				}
				$list[$key]['litpic'] = !empty($pics) ? getFilePath($pics[0]) : '';

				if(!empty($val['addrid'])){
					$addrName = getParentArr("site_area", $val['addrid']);
					global $data;
					$data = "";
					$addrArr = array_reverse(parent_foreach($addrName, "typename"));
					$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
					$list[$key]['addrname']  = $addrArr;
				}else{
					$list[$key]['addrname'] = "";
				}

				$param = array(
					"service" => "travel",
					"template" => "store-detail",
					"id" => $val['id']
				);
				$url = getUrlPath($param);
				$list[$key]['url'] = $url;
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
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$istype = isset($id['istype']) ? $id['istype'] : 0;
		$typeid = isset($id['typeid']) ? $id['typeid'] : 0;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id) && $uid == -1){
			return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误
		}

		$where = " AND `state` = 1";
		if(!is_numeric($id)){
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = ".$uid);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$id = $results[0]['id'];
				$where = "";
			}else{
				return array("state" => 200, "info" => $langData['travel'][12][24]);//该会员暂未开通公司
			}
		}

		$archives = $dsql->SetQuery("SELECT `id`, `cityid`, `title`, `userid`, `lng`, `flag`, `lat`, `addrid`, `address`, `tel`, `contact`, `pics`, `video`, `note`, `license`, `servicetime`, `click`, `pubdate`, `state`, `bind_module`  FROM `#@__travel_store` WHERE `id` = ".$id.$where);
		$results  = getCache("travel_store_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]         = $results[0]['id'];
			$storeDetail['cityid']     = $results[0]['cityid'];
			$storeDetail['flag']       = $results[0]['flag'];
			$storeDetail["title"]      = $results[0]['title'];
			$storeDetail["userid"]     = $results[0]['userid'];
			$storeDetail["address"]    = $results[0]['address'];
			$storeDetail["lng"]        = $results[0]['lng'];
			$storeDetail["lat"]        = $results[0]['lat'];
			$storeDetail["tel"]        = $results[0]['tel'];
			$storeDetail["contact"]    = $results[0]['contact'];
			$storeDetail["click"]      = $results[0]['click'];
			$storeDetail["state"]      = $results[0]['state'];
			$storeDetail["license"]    = $results[0]['license'];
			$storeDetail["servicetime"]= $results[0]['servicetime'];
			$storeDetail["note"]       = $results[0]['note'];
			$storeDetail["video"]      = $results[0]['video'];
			$storeDetail["videoSource"]= getFilePath($results[0]['video']);
			$storeDetail["bind_module"]= $results[0]['bind_module'];

			$bind_moduleArr  = array();
			$bind_moduleArr_ = $results[0]['bind_module'] ? explode(',', $results[0]['bind_module']) : array();
			if($bind_moduleArr_){
				foreach($bind_moduleArr_ as $k => $val){
					$bind_modulename = $this->gettypename("module_type", $val);
					$bind_moduleArr[$k] = array(
						"id" => $val,
						"val" => $bind_modulename
					);
				}
			}
			$storeDetail["bind_moduleArr"]  = $bind_moduleArr;
			$storeDetail["bind_moduleArr_"] = $bind_moduleArr_;

			//会员信息
			$uid = $results[0]['userid'];
			$storeDetail['member']     = getMemberDetail($uid);

			$storeDetail["addrid"]  = $addrid = $results[0]['addrid'];
            $archives = $dsql->SetQuery("SELECT `parentid` FROM `#@__site_area` WHERE `id` = '$addrid'");
            $ret = $dsql->dsqlOper($archives, "results");
            if($ret){
				$storeDetail["circleAddrid"] = $ret[0]['parentid'];
            }
			$addrName = getParentArr("site_area", $results[0]['addrid']);
			global $data;
			$data = "";
			$addrArr = array_reverse(parent_foreach($addrName, "typename"));
			$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
			$storeDetail['addrname']  = $addrArr;

			global $data;
			$data = "";
			$addrArr = array_reverse(parent_foreach($addrName, "id"));
			$storeDetail['city'] = count($addrArr) > 2 ? $addrArr[1] : $addrArr[0];
			$storeDetail["address"]    = $results[0]['address'];

			//验证是否已经收藏
			$collect = '';
			if($uid != -1){
				$params = array(
					"module" => "travel",
					"temp"   => "store-detail",
					"type"   => "add",
					"id"     => $results[0]['id'],
					"check"  => 1
				);
				$collect = checkIsCollect($params);
			}
			$storeDetail['collect'] = $collect == "has" ? 1 : 0;

			//图集
			$imglist = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode(",", $pics);
				foreach ($pics as $key => $value) {
					$imglist[$key]['path'] = getFilePath($value);
					$imglist[$key]['pathSource'] = $value;
				}
			}
			$storeDetail['pics'] = $imglist;
		}//print_R($storeDetail);exit;
		return $storeDetail;
	}

	/**
	* 配置商铺
	* @return array
	*/
	public function storeConfig(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid      = $userLogin->getMemberID();
		$param       = $this->param;

		$title       = filterSensitiveWords(addslashes($param['title']));
		$addrid      = (int)$param['addrid'];
		$cityid      = (int)$param['cityid'];
		$address     = $param['address'];
		$bind_module = $param['bind_module'];
		$tel         = $param['tel'];
		$pics        = $param['pics'];
		$video       = $param['video'];
		$license     = $param['license'];
		$servicetime = $param['servicetime'];
		$contact     = $param['contact'];
		$note        = filterSensitiveWords(addslashes($param['note']));
		$pubdate = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录
		}

		//验证会员类型
		$userDetail = $userLogin->getMemberInfo();
		if($userDetail['userType'] != 2){
			return array("state" => 200, "info" => $langData['travel'][12][22]);//账号验证错误，操作失败
		}

		//权限验证
		if(!verifyModuleAuth(array("module" => "travel"))){
			return array("state" => 200, "info" => $langData['travel'][12][14]);//商家权限验证失败
		}

		if(empty($title)){
			return array("state" => 200, "info" => $langData['travel'][12][15]);//请填写公司名称
		}

		if(empty($cityid)){
			return array("state" => 200, "info" => $langData['travel'][12][16]);//请选择所在地区
		}

		if(empty($tel)){
			return array("state" => 200, "info" => $langData['travel'][12][17]);//请填写联系方式
		}

		if(empty($contact)){
			return array("state" => 200, "info" => $langData['travel'][12][13]);//请填写联系人
		}

		if(empty($pics)){
			return array("state" => 200, "info" => $langData['travel'][12][18]);//请上传图集
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		//新商铺
		if(!$userResult){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__travel_store` (`cityid`, `title`, `userid`, `addrid`, `address`, `tel`, `contact`, `pics`, `video`, `note`, `pubdate`, `bind_module`, `license`, `servicetime`, `state`) VALUES ('$cityid', '$title', '$userid', '$addrid', '$address', '$tel', '$contact', '$pics', '$video', '$note', '$pubdate', '$bind_module', '$license', '$servicetime', '0')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){

				//更新当前会员下已经发布的信息性质
				$sql = $dsql->SetQuery("UPDATE `#@__travel_video` SET `usertype` = 1, `userid` = '$aid' WHERE `userid` = '$userid'");
				$dsql->dsqlOper($sql, "update");

				$sql = $dsql->SetQuery("UPDATE `#@__travel_strategy` SET `usertype` = 1, `userid` = '$aid' WHERE `userid` = '$userid'");
				$dsql->dsqlOper($sql, "update");

				//后台消息通知
				updateAdminNotice("travel", "store");
				updateCache("travel_store_list", 300);
				clearCache("travel_store_total", 'key');

				return $langData['travel'][12][19];//配置成功，您的商铺正在审核中，请耐心等待！
			}else{
				return array("state" => 200, "info" => $langData['travel'][12][20]);//配置失败，请查检您输入的信息是否符合要求！
			}
		}else{
			//保存到主表
			$archives = $dsql->SetQuery("UPDATE `#@__travel_store` SET `cityid` = '$cityid', `title` = '$title', `userid` = '$userid', `addrid` = '$addrid', `address` = '$address', `tel` = '$tel', `contact` = '$contact', `pics` = '$pics', `video` = '$video', `note` = '$note', `bind_module` = '$bind_module', `license` = '$license', `servicetime` = '$servicetime', `state` = '0' WHERE `userid` = ".$userid);
			$results = $dsql->dsqlOper($archives, "update");

			if($results == "ok"){
				$oldid = $userResult[0]['id'];
				//更新当前会员下已经发布的信息性质
				$sql = $dsql->SetQuery("UPDATE `#@__travel_video` SET `usertype` = 1, `userid` = '$oldid' WHERE `userid` = '$userid'");
				$dsql->dsqlOper($sql, "update");

				$sql = $dsql->SetQuery("UPDATE `#@__travel_strategy` SET `usertype` = 1, `userid` = '$oldid' WHERE `userid` = '$userid'");
				$dsql->dsqlOper($sql, "update");

				// 检查缓存
				$id = $userResult[0]['id'];
				checkCache("travel_store_list", $id);
				clearCache("travel_store_total", 'key');
				clearCache("travel_store_detail", $id);

				return $langData['travel'][12][21];//保存成功！
			}else{
				return array("state" => 200, "info" => $langData['travel'][12][20]);//配置失败，请查检您输入的信息是否符合要求！
			}
		}

	}

	/**
	 * 操作旅游酒店
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operHotel(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/travel.inc.php");
		$customtravelhotelCheck = (int)$customtravelhotelCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$typeid          =  (int)$param['typeid'];
		$addrid          =  (int)$param['addrid'];
		$cityid          =  (int)$param['cityid'];
		$address     	 =  $param['address'];
		$price     	     =  (float)$param['price'];
		$tag     	     =  $param['tag'];
		$pics     	     =  $param['pics'];
		$video     	     =  $param['video'];
		$roomlist     	 =  $param['roomlist'];
		$pubdate         =  GetMkTime(time());

		$roomlist        = json_decode($roomlist, true);



		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
			return array("state" => 200, "info" => $langData['travel'][12][27]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__travel_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['travel'][12][28]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['travel'][12][29]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['travel'][12][30]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title))  return array("state" => 200, "info" => $langData['travel'][12][31]);//请输入公司名称！
			if(empty($addrid)) return array("state" => 200, "info" => $langData['travel'][12][32]);//请输入所在地区！
			if(empty($pics))   return array("state" => 200, "info" => $langData['travel'][12][33]);//请上传图片图片
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__travel_hotel` (`userid`, `company`, `title`, `cityid`, `typeid`, `addrid`, `address`, `pics`, `video`, `price`, `tag`, `pubdate`, `state`) VALUES ('$userid', '$company', '$title', '$cityid', '$typeid', '$addrid', '$address', '$pics', '$video', '$price', '$tag', '$pubdate', '$customtravelhotelCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){
				//保存房间信息
				if(!empty($roomlist)){
					foreach($roomlist as $val){
						$specialtime = stripslashes($val['specialtime']);
						$specialtime = json_decode($specialtime, true);
						$specialtime = serialize($specialtime);
						$pics = $dsql->SetQuery("INSERT INTO `#@__travel_hotelroom` (`title`, `hotelid`, `area`, `iswindow`, `typeid`, `breakfast`, `price`, `specialtime`, `pubdate`) VALUES ('$val[title]', '$aid', '$val[area]', '$val[iswindow]', '$val[typeid]', '$val[breakfast]', '$val[price]', '$specialtime', '$pubdate')");
						$dsql->dsqlOper($pics, "update");
					}
				}

				if($customtravelhotelCheck){
					updateCache("travel_hotel_list", 300);
				}

				clearCache("travel_hotel_total", 'key');

				//后台消息通知
				updateAdminNotice("travel", "hotel");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['travel']['12']['34']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__travel_hotel` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__travel_hotel` SET `userid` = '$userid', `company` = '$company', `title` = '$title', `cityid` = '$cityid', `typeid` = '$typeid', `addrid` = '$addrid', `address` = '$address', `pics` = '$pics', `video` = '$video', `price` = '$price', `tag` = '$tag', `state` = '$customtravelhotelCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['travel']['12']['34']); //保存到数据时发生错误，请检查字段内容！
				}

				//保存房间信息
				if(!empty($roomlist)){
					foreach($roomlist as $val){
						$specialtime = stripslashes($val['specialtime']);
						$specialtime = json_decode($specialtime, true);
						$specialtime = serialize($specialtime);

						if(!empty($val['id'])){
							$archives = $dsql->SetQuery("UPDATE `#@__travel_hotelroom` SET `title` = '$val[title]', `hotelid` = '$id', `area` = '$val[area]', `iswindow` = '$val[iswindow]', `typeid` = '$val[typeid]', `breakfast` = '$val[breakfast]', `price` = '$val[price]', `specialtime` = '$specialtime' WHERE `id` = ".$val['id']);
							$dsql->dsqlOper($archives, "update");
						}else{
							$pics = $dsql->SetQuery("INSERT INTO `#@__travel_hotelroom` (`title`, `hotelid`, `area`, `iswindow`, `typeid`, `breakfast`, `price`, `specialtime`, `pubdate`) VALUES ('$val[title]', '$id', '$val[area]', '$val[iswindow]', '$val[typeid]', '$val[breakfast]', '$val[price]', '$specialtime', '$pubdate')");
							$dsql->dsqlOper($pics, "update");
						}
					}
				}

				updateAdminNotice("travel", "hotel");

				// 清除缓存
				clearCache("travel_hotel_detail", $id);
				checkCache("travel_hotel_list", $id);
				clearCache("travel_hotel_total", 'key');


				return $langData['travel'][12][35];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`pics`, l.`video`, s.`userid` FROM `#@__travel_hotel` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					//删除图集
					delPicFile($results['pics'], "delAtlas", "travel");
					delPicFile($results['video'], "delVideo", "travel");
					// 清除缓存
					clearCache("travel_hotel_detail", $id);
					checkCache("travel_hotel_list", $id);
					clearCache("travel_hotel_total", 'key');

					$sql = $dsql->SetQuery("DELETE FROM `#@__travel_hotelroom` WHERE `hotelid` = ".$id);
					$dsql->dsqlOper($sql, "update");

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__travel_hotel` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['travel'][12][36]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['travel'][12][37]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}

	}

	/**
	 * 旅游酒店列表
	 * @return array
	 */
	public function hotelList(){
		global $dsql;
		global $langData;
		global $userLogin;
		$pageinfo = $list = array();
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
			}else{
				$search   = $this->param['search'];
				$store    = $this->param['store'];
				$state    = $this->param['state'];
				$lat      = $this->param['lat'];
				$lng      = $this->param['lng'];
				$noid	  = $this->param['noid'];
				$u        = $this->param['u'];
				$addrid   = $this->param['addrid'];
				$typeid   = $this->param['typeid'];
				$price    = $this->param['price'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$uid      = $userLogin->getMemberID();
		$userinfo = $userLogin->getMemberInfo();

		if($typeid){
			$where .= " AND `typeid` in ($typeid) ";
		}

		if($noid){
			$where .= " AND `id` not in ($noid) ";
		}

		if($u!=1){
			$where .= " AND `state` = 1 ";

			$cityid = getCityId($this->param['cityid']);
			if($cityid){
				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `state` = 1 AND `cityid` = $cityid");
				$results = $dsql->dsqlOper($archives, "results");
				if(is_array($results)){
					foreach ($results as $key => $value) {
						$sidArr[$key] = $value['id'];
					}
					if(!empty($sidArr)){
						$where .= " AND `company` in (".join(",",$sidArr).")";
					}else{
						$where .= " AND 0 = 1";
					}
				}else{
					$where .= " AND 0 = 1";
				}
			}
		}else{
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
				return array("state" => 200, "info" => $langData['travel'][12][14]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = $uid");
			$storeRes = $dsql->dsqlOper($sql, "results");
			if($storeRes){
				$where .= " AND `company` = ".$storeRes[0]['id'];
			}else{
				$where .= " AND 1 = 2";
			}

			if($state!=''){
				$where .= " AND `state` = ".$state;
			}
		}

		if($store){
			$where .= " AND `company` = '$store'";
		}

		if(!empty($addrid)){
			if($dsql->getTypeList($addrid, "site_area")){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($dsql->getTypeList($addrid, "site_area"));
				$lower = $addrid.",".join(',',$lower);
			}else{
				$lower = $addrid;
			}
			$where .= " AND `addrid` in ($lower)";
		}

		//价格区间
		if($price != ""){
			$price = explode(",", $price);
			if(empty($price[0])){
				$where .= " AND `price` < " . $price[1];
			}elseif(empty($price[1])){
				$where .= " AND `price` > " . $price[0];
			}else{
				$where .= " AND `price` BETWEEN " . $price[0] . " AND " . $price[1];
			}
		}

		if(!empty($search)){

			siteSearchLog("travel", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__travel_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
	        $results = $dsql->dsqlOper($userSql, "results");
	        foreach ($results as $key => $value) {
	            $sidArr[$key] = $value['id'];
	        }

	        if(!empty($sidArr)){
	            $where .= " AND (`title` like '%$search%' OR `company` in (".join(",",$sidArr)."))";
	        }else{
	            $where .= " AND `title` like '%$search%'";
	        }
		}

		//查询距离
		if((!empty($lng))&&(!empty($lat))){
            $select="(2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*(".$lat."-`lat`)/360),2)+COS(3.1415926535898*".$lat."/180)* COS(`lat` * 3.1415926535898/180)*POW(SIN(3.1415926535898*(".$lng."-`lng`)/360),2))))*1000 AS distance,";
        }else{
            $select="";
        }


		//排序
        switch ($orderby){
            //浏览量
            case 1:
                $orderby_ = " ORDER BY `click` DESC, `weight` DESC, `id` DESC";
                break;
            //发布时间降序
            case 2:
                $orderby_ = " ORDER BY `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//价格降序
			case 3:
				$orderby_ = " ORDER BY `price` DESC, `click` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//价格升序
			case 4:
				$orderby_ = " ORDER BY `price` ASC, `click` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
            default:
                $orderby_ = " ORDER BY `weight` DESC, `id` DESC";
                break;
        }

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `address`, `pics`, `video`, `price`, `tag`, `cityid`, `typeid`, `addrid`, `click`, `pubdate`, ".$select." `state`  FROM `#@__travel_hotel` WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__travel_hotel` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("travel_hotel_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		//会员列表需要统计信息状态
		if($u == 1 && $uid > -1){
			//待审核
			$totalGray = $dsql->dsqlOper($archives." AND `state` = 0", "totalCount");
			//已审核
			$totalAudit = $dsql->dsqlOper($archives." AND `state` = 1", "totalCount");
			//拒绝审核
			$totalRefuse = $dsql->dsqlOper($archives." AND `state` = 2", "totalCount");

			$pageinfo['gray'] = $totalGray;
			$pageinfo['audit'] = $totalAudit;
			$pageinfo['refuse'] = $totalRefuse;
		}

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$sql = $dsql->SetQuery($archives.$orderby_.$where);
		$results = getCache("travel_hotel_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['price']     = $val['price'];
				$list[$key]['cityid']    = $val['cityid'];
				$list[$key]['typeid']    = $val['typeid'];
				$list[$key]['typename']  = $val['typeid'] ? $this->gettypename("travelhotel_type", $val['typeid']) : '';

				$list[$key]['distance']  = $val['distance'] > 1000 ? sprintf("%.1f", $val['distance'] / 1000) . $langData['siteConfig'][13][23] : sprintf("%.1f", $val['distance']) . $langData['siteConfig'][13][22];  //距离   //千米  //米
				if(strpos($list[$key]['distance'],'千米')){
					$list[$key]['distance'] = str_replace("千米",'km',$list[$key]['distance']);
				}elseif(strpos($list[$key]['distance'],'米')){
					$list[$key]['distance'] = str_replace("米",'m',$list[$key]['distance']);
				}

				if(!empty($val['addrid'])){
					$addrName = getParentArr("site_area", $val['addrid']);
					global $data;
					$data = "";
					$addrArr = array_reverse(parent_foreach($addrName, "typename"));
					$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
					$list[$key]['addrname']  = $addrArr;
				}else{
					$list[$key]['addrname'] = "";
				}

				$tagArr = array();
				if(!empty($val['tag'])){
					$tag = explode("|", $val['tag']);
					foreach ($tag as $k => $v) {
						$tagArr[$k] = array(
							"jc" => $v,
							"py" =>  GetPinyin($v)
						);
					}
				}
				$list[$key]['tagAll'] = $tagArr;

				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
					$list[$key]['litpic'] = getFilePath($pics[0]);
				}else{
					$list[$key]['litpic']  = '';
				}
				$list[$key]['video'] = $val['video'] ? getFilePath($val['video']) : '';

				$param = array(
					"service" => "travel",
					"template" => "hotel-detail",
					"id" => $val['id']
				);
				$url = getUrlPath($param);

				$list[$key]['url'] = $url;

				$lower = [];
				$param['id']    = $val['company'];
				$this->param    = $param;
				$store          = $this->storeDetail();
				if(!empty($store)){
					$lower = $store;
				}
				$list[$key]['store'] = $lower;
			}

		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
     * 旅游酒店详细
     * @return array
     */
	public function hotelDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误
		}

		//$where = " AND `state` = 1";

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `pics`, `price`, `tag`, `video`, `lng`, `lat`, `cityid`, `typeid`, `addrid`, `address`, `click`, `pubdate`, `state` FROM `#@__travel_hotel` WHERE `id` = ".$id.$where);
		$results  = getCache("travel_hotel_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["lng"]         = $results[0]['lng'];
			$storeDetail["lat"]         = $results[0]['lat'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail['price']       = $results[0]['price'];
			$storeDetail["cityid"]      = $results[0]['cityid'];
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];
			$storeDetail['addrid']      = $results[0]['addrid'];
			$storeDetail['address']     = $results[0]['address'];
			$storeDetail['tag']         = $results[0]['tag'];
			$storeDetail['video']       = $results[0]['video'];
			$storeDetail['videopath']   = $results[0]['video'] ? getFilePath($results[0]['video']) : '';
			$storeDetail['typeid']      = $results[0]['typeid'];
			$storeDetail['typename']    = $results[0]['typeid'] ? $this->gettypename("travelhotel_type", $results[0]['typeid']) : '';

			//获取房间信息
			$workArr = [];
			$sql = $dsql->SetQuery("SELECT `id`, `title`, `area`, `iswindow`, `typeid`, `breakfast`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `hotelid` = '$id' AND `valid` = 0 ORDER BY  `weight` DESC, `pubdate` DESC, `id` DESC");
			$res = $dsql->dsqlOper($sql, "results");
			if(!empty($res)){
				foreach($res as $key=> $val){
					$workArr[$key]['id']           = $val['id'];
					$workArr[$key]['title']        = $val['title'];
					$workArr[$key]['area']         = $val['area'];
					$workArr[$key]['iswindow']     = $val['iswindow'];
					$workArr[$key]['typeid']       = $val['typeid'];
					$workArr[$key]['breakfast']    = $val['breakfast'];
					$workArr[$key]['price']        = $val['price'];
					$workArr[$key]['iswindowname'] = $val['iswindow']!='' ? $this->gettypename("iswindow_type", $val['iswindow']) : '';
					$workArr[$key]['typename']     = $val['typeid'] !=''  ? $this->gettypename("room_type", $val['typeid']) : '';
					$workArr[$key]['breakfastname']= $val['breakfast']!=''? $this->gettypename("breakfast_type", $val['breakfast']) : '';

					$workArr[$key]['specialtime']  = $val['specialtime'] ? unserialize($val['specialtime']) : '';
					$workArr[$key]['specialtimejson']  = $val['specialtime'] ? json_encode(unserialize($val['specialtime'])) : '';

				}
			}
			$storeDetail["workArr"]         = $workArr;

			if(!empty($results[0]['addrid'])){
				$addrName = getParentArr("site_area", $results[0]['addrid']);
				global $data;
				$data = "";
				$addrArr = array_reverse(parent_foreach($addrName, "typename"));
				$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
				$storeDetail['addrname']  = $addrArr;
			}else{
				$storeDetail['addrname'] = "";
			}

			$tagArr = array();
			if(!empty($results[0]['tag'])){
				$tag = explode("|", $results[0]['tag']);
				foreach ($tag as $k => $v) {
					$tagArr[$k] = array(
						"jc" => $v,
						"py" =>  GetPinyin($v)
					);
				}
			}
			$storeDetail['tagAll'] = $tagArr;

			//图集
			$imglist = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode(",", $pics);
				foreach ($pics as $key => $value) {
					$imglist[$key]['path'] = getFilePath($value);
					$imglist[$key]['pathSource'] = $value;
				}
			}
			$storeDetail['pics'] = $imglist;

			$lower = [];
			$param['id']    = $results[0]['company'];
			$this->param    = $param;
			$store          = $this->storeDetail();
			if(!empty($store)){
				$lower = $store;
			}
			$storeDetail['store'] = $lower;

			$param = array(
				"service"  => "travel",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "travel",
				"temp"   => "hotel-detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$storeDetail['collect'] = $collect == "has" ? 1 : 0;

		}
		return $storeDetail;
	}

	/**
	 * 操作景点门票
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operTicket(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/travel.inc.php");
		$customtravelticketCheck = (int)$customtravelticketCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$addrid          =  (int)$param['addrid'];
		$cityid          =  (int)$param['cityid'];
		$address     	 =  $param['address'];
		$tag     	     =  $param['tag'];
		$pics     	     =  $param['pics'];
		$video     	     =  $param['video'];
		$opentime        =  $param['opentime'];
		$travelname      =   $param['travelname'];
		$note            =  filterSensitiveWords(addslashes($param['note']));
		$ticketlist      =  $param['ticketlist'];
		$pubdate         =  GetMkTime(time());
		$ticketlist      = json_decode($ticketlist, true);



		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
			return array("state" => 200, "info" => $langData['travel'][12][27]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__travel_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['travel'][12][28]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['travel'][12][29]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['travel'][12][30]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title))  return array("state" => 200, "info" => $langData['travel'][3][30]);//请输入景点名称
			if(empty($address)) return array("state" => 200, "info" => $langData['travel'][8][61]);//请输入详细地址
			if(empty($opentime)) return array("state" => 200, "info" => $langData['travel'][5][56]);//请输入开放时间
			if(empty($pics))   return array("state" => 200, "info" => $langData['travel'][3][27]);//请至少上传一张图片
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__travel_ticket` (`note`, `userid`, `company`, `title`, `cityid`, `opentime`, `addrid`, `address`, `pics`, `video`, `travelname`, `tag`, `pubdate`, `state`) VALUES ('$note', '$userid', '$company', '$title', '$cityid', '$opentime', '$addrid', '$address', '$pics', '$video', '$travelname', '$tag', '$pubdate', '$customtravelticketCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){
				//保存房间信息
				if(!empty($ticketlist)){
					foreach($ticketlist as $val){
						$specialtime = stripslashes($val['specialtime']);
						$specialtime = json_decode($specialtime, true);
						$specialtime = serialize($specialtime);
						$pics = $dsql->SetQuery("INSERT INTO `#@__travel_ticketinfo` (`typeid`, `title`, `ticketid`, `price`, `specialtime`, `pubdate`) VALUES ('0', '$val[title]', '$aid', '$val[price]', '$specialtime', '$pubdate')");
						$dsql->dsqlOper($pics, "update");
					}
				}

				if($customtravelticketCheck){
					updateCache("travel_ticket_list", 300);
				}

				clearCache("travel_ticket_total", 'key');

				//后台消息通知
				updateAdminNotice("travel", "ticket");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['travel']['12']['34']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__travel_ticket` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__travel_ticket` SET `note` = '$note', `userid` = '$userid', `company` = '$company', `title` = '$title', `cityid` = '$cityid', `opentime` = '$opentime', `addrid` = '$addrid', `address` = '$address', `pics` = '$pics', `video` = '$video', `travelname` = '$travelname', `tag` = '$tag', `state` = '$customtravelticketCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['travel']['12']['34']); //保存到数据时发生错误，请检查字段内容！
				}

				if(!empty($ticketlist)){
					foreach($ticketlist as $val){
						$specialtime = stripslashes($val['specialtime']);
						$specialtime = json_decode($specialtime, true);
						$specialtime = serialize($specialtime);

						if(!empty($val['id'])){
							$archives = $dsql->SetQuery("UPDATE `#@__travel_ticketinfo` SET `typeid` = '0', `title` = '$val[title]', `ticketid` = '$id', `price` = '$val[price]', `specialtime` = '$specialtime' WHERE `id` = ".$val['id']);
							$dsql->dsqlOper($archives, "update");
						}else{
							$pics = $dsql->SetQuery("INSERT INTO `#@__travel_ticketinfo` (`typeid`, `title`, `ticketid`, `price`, `specialtime`, `pubdate`) VALUES ('0', '$val[title]', '$id', '$val[price]', '$specialtime', '$pubdate')");
							$dsql->dsqlOper($pics, "update");
						}
					}
				}

				updateAdminNotice("travel", "ticket");

				// 清除缓存
				clearCache("travel_ticket_detail", $id);
				checkCache("travel_ticket_list", $id);
				clearCache("travel_ticket_total", 'key');


				return $langData['travel'][12][35];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`pics`, l.`video`, s.`userid` FROM `#@__travel_ticket` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					//删除图集
					delPicFile($results['pics'], "delAtlas", "travel");
					delPicFile($results['video'], "delVideo", "travel");
					// 清除缓存
					clearCache("travel_ticket_detail", $id);
					checkCache("travel_ticket_list", $id);
					clearCache("travel_ticket_total", 'key');

					$sql = $dsql->SetQuery("DELETE FROM `#@__travel_ticketinfo` WHERE `typeid` =0 and `ticketid` = ".$id);
					$dsql->dsqlOper($sql, "update");

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__travel_ticket` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['travel'][12][36]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['travel'][12][37]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}

	}

	/**
	 * 旅游景点门票
	 * @return array
	 */
	public function ticketList(){
		global $dsql;
		global $langData;
		global $userLogin;
		$pageinfo = $list = array();
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
			}else{
				$search   = $this->param['search'];
				$store    = $this->param['store'];
				$state    = $this->param['state'];
				$flag	  = $this->param['flag'];
				$u        = $this->param['u'];
				$rec      = $this->param['rec'];
				$noid	  = $this->param['noid'];
				$addrid   = $this->param['addrid'];
				$lng      = $this->param['lng'];
                $lat      = $this->param['lat'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$uid      = $userLogin->getMemberID();
		$userinfo = $userLogin->getMemberInfo();

		if($rec){
			$where .= " AND `rec` = 1";
		}

		if($flag){
			$where .= " AND `flag` in ($flag)";
		}

		if($noid){
			$where .= " AND `id` not in ($noid)";
		}

		if($u!=1){
			$where .= " AND `state` = 1 ";

			$cityid = getCityId($this->param['cityid']);
			if($cityid){
				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `state` = 1 AND `cityid` = $cityid");
				$results = $dsql->dsqlOper($archives, "results");
				if(is_array($results)){
					foreach ($results as $key => $value) {
						$sidArr[$key] = $value['id'];
					}
					if(!empty($sidArr)){
						$where .= " AND `company` in (".join(",",$sidArr).")";
					}else{
						$where .= " AND 0 = 1";
					}
				}else{
					$where .= " AND 0 = 1";
				}
			}
		}else{
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
				return array("state" => 200, "info" => $langData['travel'][12][14]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = $uid");
			$storeRes = $dsql->dsqlOper($sql, "results");
			if($storeRes){
				$where .= " AND `company` = ".$storeRes[0]['id'];
			}else{
				$where .= " AND 1 = 2";
			}

			if($state!=''){
				$where .= " AND `state` = ".$state;
			}
		}

		if($store){
			$where .= " AND `company` = '$store'";
		}

		if(!empty($addrid)){
			if($dsql->getTypeList($addrid, "site_area")){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($dsql->getTypeList($addrid, "site_area"));
				$lower = $addrid.",".join(',',$lower);
			}else{
				$lower = $addrid;
			}
			$where .= " AND `addrid` in ($lower)";
			/* $sidArr = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `addrid` in ($lower)");
			$results = $dsql->dsqlOper($archives, "results");
			if(is_array($results)){
				foreach ($results as $key => $value) {
					$sidArr[$key] = $value['id'];
				}
				if(!empty($sidArr)){
					$where .= " AND (`company` in (".join(",",$sidArr).") OR `addrid` in ($lower))";
				}else{
					$where .= " AND `addrid` in ($lower)";
				}
			}else{
				$where .= " AND `addrid` in ($lower)";
			} */
		}

		if(!empty($search)){

			siteSearchLog("travel", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__travel_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
	        $results = $dsql->dsqlOper($userSql, "results");
	        foreach ($results as $key => $value) {
	            $sidArr[$key] = $value['id'];
	        }

	        if(!empty($sidArr)){
	            $where .= " AND (`title` like '%$search%' OR `company` in (".join(",",$sidArr)."))";
	        }else{
	            $where .= " AND `title` like '%$search%'";
	        }
		}

		//查询距离
		if((!empty($lng))&&(!empty($lat))){
            $select="(2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*(".$lat."-`lat`)/360),2)+COS(3.1415926535898*".$lat."/180)* COS(`lat` * 3.1415926535898/180)*POW(SIN(3.1415926535898*(".$lng."-`lng`)/360),2))))*1000 AS distance,";
        }else{
            $select="";
        }


		//排序
        switch ($orderby){
            //浏览量
            case 1:
                $orderby_ = " ORDER BY `click` DESC, `rec` DESC, `weight` DESC, `id` DESC";
                break;
            //发布时间降序
            case 2:
                $orderby_ = " ORDER BY `pubdate` DESC, `rec` DESC, `weight` DESC, `id` DESC";
				break;
			//推荐降序
			case 3:
                $orderby_ = " ORDER BY `rec` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//价格降序
			case 4:
				$orderby_ = " ORDER BY `price` DESC, `rec` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
				//距离排序
			case 5:
				if((!empty($lng))&&(!empty($lat))){
					$orderby_ = " ORDER BY distance DESC, `rec` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				}
				break;
			//销量降序
			case 6:
				$orderby_ = " ORDER BY `sale` DESC, `rec` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//景点级别 降序
			case 7:
				$orderby_ = " ORDER BY `sale` DESC, `rec` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
            default:
                $orderby_ = " ORDER BY `pubdate` DESC, `weight` DESC, `id` DESC";
                break;
        }

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `flag`, `rec`, `userid`, `company`, `address`, `pics`, `video`, `opentime`, `tag`, `cityid`, `travelname`, `addrid`, `click`, `pubdate`, ".$select." `state`, (SELECT avg(c.`price`) FROM `#@__travel_ticketinfo` c WHERE c.`ticketid` = l.`id` AND c.`typeid` = '0' ) AS price, (SELECT sum(c.`sale`) FROM `#@__travel_ticketinfo` c WHERE c.`ticketid` = l.`id` AND c.`typeid` = '0' ) AS sale  FROM `#@__travel_ticket` l WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__travel_ticket` l WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("travel_ticket_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		//会员列表需要统计信息状态
		if($u == 1 && $uid > -1){
			//待审核
			$totalGray = $dsql->dsqlOper($archives." AND `state` = 0", "totalCount");
			//已审核
			$totalAudit = $dsql->dsqlOper($archives." AND `state` = 1", "totalCount");
			//拒绝审核
			$totalRefuse = $dsql->dsqlOper($archives." AND `state` = 2", "totalCount");

			$pageinfo['gray'] = $totalGray;
			$pageinfo['audit'] = $totalAudit;
			$pageinfo['refuse'] = $totalRefuse;
		}

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$sql = $dsql->SetQuery($archives.$orderby_.$where);
		$results = getCache("travel_ticket_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['price']     = round($val['price'], 2);
				$list[$key]['title']     = $val['title'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['sale']      = $val['sale'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['travelname']= $val['travelname'];
				$list[$key]['cityid']    = $val['cityid'];
				$list[$key]['opentime']  = $val['opentime'];
				$list[$key]['flag']      = $val['flag'];
				$list[$key]['rec']       = $val['rec'];
				$list[$key]['flagname']  = $val['flag'] ? $this->gettypename("star_type", $val['flag']) : '';

				$list[$key]['distance']  = $val['distance'] > 1000 ? sprintf("%.1f", $val['distance'] / 1000) . $langData['siteConfig'][13][23] : sprintf("%.1f", $val['distance']) . $langData['siteConfig'][13][22];  //距离   //千米  //米
				if(strpos($list[$key]['distance'],'千米')){
					$list[$key]['distance'] = str_replace("千米",'km',$list[$key]['distance']);
				}elseif(strpos($list[$key]['distance'],'米')){
					$list[$key]['distance'] = str_replace("米",'m',$list[$key]['distance']);
				}


				if(!empty($val['addrid'])){
					$addrName = getParentArr("site_area", $val['addrid']);
					global $data;
					$data = "";
					$addrArr = array_reverse(parent_foreach($addrName, "typename"));
					$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
					$list[$key]['addrname']  = $addrArr;
				}else{
					$list[$key]['addrname'] = "";
				}

				$tagArr = array();
				if(!empty($val['tag'])){
					$tag = explode("|", $val['tag']);
					foreach ($tag as $k => $v) {
						$tagArr[$k] = array(
							"jc" => $v,
							"py" =>  GetPinyin($v)
						);
					}
				}
				$list[$key]['tagAll'] = $tagArr;

				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
					$list[$key]['litpic'] = getFilePath($pics[0]);
				}else{
					$list[$key]['litpic']  = '';
				}
				$list[$key]['video'] = $val['video'] ? getFilePath($val['video']) : '';

				$param = array(
					"service" => "travel",
					"template" => "ticket-detail",
					"id" => $val['id']
				);
				$url = getUrlPath($param);

				$list[$key]['url'] = $url;

				$lower = [];
				$param['id']    = $val['company'];
				$this->param    = $param;
				$store          = $this->storeDetail();
				if(!empty($store)){
					$lower = $store;
				}
				$list[$key]['store'] = $lower;
			}

		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
     * 旅游景点门票
     * @return array
     */
	public function ticketDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误
		}

		//$where = " AND `state` = 1";

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `note`, `company`, `pics`, `opentime`, `tag`, `video`, `lng`, `lat`, `cityid`, `travelname`, `addrid`, `address`, `click`, `pubdate`, `state` FROM `#@__travel_ticket` WHERE `id` = ".$id.$where);
		$results  = getCache("travel_ticket_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["lng"]         = $results[0]['lng'];
			$storeDetail["lat"]         = $results[0]['lat'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail['opentime']    = $results[0]['opentime'];
			$storeDetail["cityid"]      = $results[0]['cityid'];
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];
			$storeDetail['addrid']      = $results[0]['addrid'];
			$storeDetail['address']     = $results[0]['address'];
			$storeDetail['note']        = $results[0]['note'];
			$storeDetail['tag']         = $results[0]['tag'];
			$storeDetail['video']       = $results[0]['video'];
			$storeDetail['videopath']   = $results[0]['video'] ? getFilePath($results[0]['video']) : '';
			$storeDetail['travelname']  = $results[0]['travelname'];

			//获取房间信息
			$workArr = [];
			$sql = $dsql->SetQuery("SELECT `id`, `title`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `typeid` = '0' and `ticketid` = '$id' ORDER BY  `weight` DESC, `pubdate` DESC, `id` DESC");
			$res = $dsql->dsqlOper($sql, "results");
			if(!empty($res)){
				foreach($res as $key=> $val){
					$workArr[$key]['id']           = $val['id'];
					$workArr[$key]['title']        = $val['title'];
					$workArr[$key]['price']        = $val['price'];
					$workArr[$key]['specialtime']  = $val['specialtime'] ? unserialize($val['specialtime']) : '';
					$workArr[$key]['specialtimejson']  = $val['specialtime'] ? json_encode(unserialize($val['specialtime'])) : '';

				}
			}
			$storeDetail["workArr"]         = $workArr;

			// $archives = $dsql->SetQuery("SELECT `id` FROM `#@__travelcommon` WHERE `aid` = ".$results[0]['id']." AND `type` = 2 AND `ischeck` = 1 AND `floor` = 0");
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'travel-ticket' AND `aid` = '$id' AND `pid` = 0");
			$totalCount = $dsql->dsqlOper($archives, "totalCount");
            $storeDetail['common'] = $totalCount;

			if(!empty($results[0]['addrid'])){
				$addrName = getParentArr("site_area", $results[0]['addrid']);
				global $data;
				$data = "";
				$addrArr = array_reverse(parent_foreach($addrName, "typename"));
				$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
				$storeDetail['addrname']  = $addrArr;
			}else{
				$storeDetail['addrname'] = "";
			}

			$tagArr = array();
			if(!empty($results[0]['tag'])){
				$tag = explode("|", $results[0]['tag']);
				foreach ($tag as $k => $v) {
					$tagArr[$k] = array(
						"jc" => $v,
						"py" =>  GetPinyin($v)
					);
				}
			}
			$storeDetail['tagAll'] = $tagArr;

			//图集
			$imglist = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode(",", $pics);
				foreach ($pics as $key => $value) {
					$imglist[$key]['path'] = getFilePath($value);
					$imglist[$key]['pathSource'] = $value;
				}
			}
			$storeDetail['pics'] = $imglist;

			$lower = [];
			$param['id']    = $results[0]['company'];
			$this->param    = $param;
			$store          = $this->storeDetail();
			if(!empty($store)){
				$lower = $store;
			}
			$storeDetail['store'] = $lower;

			$param = array(
				"service"  => "travel",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "travel",
				"temp"   => "ticket-detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$storeDetail['collect'] = $collect == "has" ? 1 : 0;

		}
		return $storeDetail;
	}

	/**
	 * 操作旅游租车
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operRentcar(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/travel.inc.php");
		$customtravelrentcarCheck = (int)$customtravelrentcarCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$addrid          =  (int)$param['addrid'];
		$cityid          =  (int)$param['cityid'];
		$address         =  $param['address'];
		$pics     	     =  $param['pics'];
		$video     	     =  $param['video'];
		$tag     	     =  $param['tag'];
		$typeid     	 =  (int)$param['typeid'];
		$price     	     =  (float)$param['price'];
		$pubdate         =  GetMkTime(time());

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
			return array("state" => 200, "info" => $langData['travel'][12][27]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__travel_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['travel'][12][28]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['travel'][12][29]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['travel'][12][30]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title))   return array("state" => 200, "info" => $langData['travel'][12][42]);//请输入汽车标题
			if(empty($typeid))  return array("state" => 200, "info" => $langData['travel'][9][9]);//请选择车型
			if(empty($address)) return array("state" => 200, "info" => $langData['travel'][8][61]);//请输入详细地址
			if($price == '')    return array("state" => 200, "info" => $langData['travel'][9][11]);//请输入租金
			if(empty($pics))    return array("state" => 200, "info" => $langData['travel'][3][27]);//请至少上传一张图片
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__travel_rentcar` (`typeid`, `userid`, `company`, `title`, `cityid`, `addrid`, `address`, `pics`, `video`, `price`, `tag`, `pubdate`, `state`) VALUES ('$typeid', '$userid', '$company', '$title', '$cityid', '$addrid', '$address', '$pics', '$video', '$price', '$tag', '$pubdate', '$customtravelrentcarCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){

				if($customtravelrentcarCheck){
					updateCache("travel_rentcar_list", 300);
				}

				clearCache("travel_rentcar_total", 'key');

				//后台消息通知
				updateAdminNotice("travel", "rentcar");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['travel']['12']['34']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__travel_rentcar` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__travel_rentcar` SET `typeid` = '$typeid', `price` = '$price', `userid` = '$userid', `company` = '$company', `title` = '$title', `cityid` = '$cityid', `addrid` = '$addrid', `address` = '$address', `pics` = '$pics', `video` = '$video', `tag` = '$tag', `state` = '$customtravelrentcarCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['travel']['12']['34']); //保存到数据时发生错误，请检查字段内容！
				}

				updateAdminNotice("travel", "rentcar");

				// 清除缓存
				clearCache("travel_rentcar_detail", $id);
				checkCache("travel_rentcar_list", $id);
				clearCache("travel_rentcar_total", 'key');


				return $langData['travel'][12][35];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`pics`, l.`video`, s.`userid` FROM `#@__travel_rentcar` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					//删除图集
					delPicFile($results['pics'], "delAtlas", "travel");
					delPicFile($results['video'], "delVideo", "travel");
					// 清除缓存
					clearCache("travel_rentcar_detail", $id);
					checkCache("travel_rentcar_list", $id);
					clearCache("travel_rentcar_total", 'key');

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__travel_rentcar` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['travel'][12][36]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['travel'][12][37]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}

	}

	/**
	 * 旅游旅游租车
	 * @return array
	 */
	public function rentcarList(){
		global $dsql;
		global $langData;
		global $userLogin;
		$pageinfo = $list = array();
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
			}else{
				$search   = $this->param['search'];
				$store    = $this->param['store'];
				$state    = $this->param['state'];
				$typeid   = $this->param['typeid'];
				$price    = $this->param['price'];
				$noid	  = $this->param['noid'];
				$u        = $this->param['u'];
				$addrid   = $this->param['addrid'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$uid      = $userLogin->getMemberID();
		$userinfo = $userLogin->getMemberInfo();

		if($u!=1){
			$where .= " AND `state` = 1 ";

			$cityid = getCityId($this->param['cityid']);
			if($cityid){
				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `state` = 1 AND `cityid` = $cityid");
				$results = $dsql->dsqlOper($archives, "results");
				if(is_array($results)){
					foreach ($results as $key => $value) {
						$sidArr[$key] = $value['id'];
					}
					if(!empty($sidArr)){
						$where .= " AND `cityid` = '$cityid' AND `company` in (".join(",",$sidArr).")";
					}else{
						$where .= " AND `cityid` = '$cityid'";
					}
				}else{
					$where .= " AND `cityid` = '$cityid'";
				}
			}
		}else{
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
				return array("state" => 200, "info" => $langData['travel'][12][14]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = $uid");
			$storeRes = $dsql->dsqlOper($sql, "results");
			if($storeRes){
				$where .= " AND `company` = ".$storeRes[0]['id'];
			}else{
				$where .= " AND 1 = 2";
			}

			if($state!=''){
				$where .= " AND `state` = ".$state;
			}
		}

		if($store){
			$where .= " AND `company` = '$store'";
		}

		if($typeid){
			$where .= " AND `typeid` IN ($typeid)";
		}

		if($noid){
			$where .= " AND `id` NOT IN ($noid)";
		}

		if(!empty($addrid)){
			if($dsql->getTypeList($addrid, "site_area")){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($dsql->getTypeList($addrid, "site_area"));
				$lower = $addrid.",".join(',',$lower);
			}else{
				$lower = $addrid;
			}
			$sidArr = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `addrid` in ($lower)");
			$results = $dsql->dsqlOper($archives, "results");
			if(is_array($results)){
				foreach ($results as $key => $value) {
					$sidArr[$key] = $value['id'];
				}
				if(!empty($sidArr)){
					$where .= " AND (`company` in (".join(",",$sidArr).") OR `addrid` in ($lower))";
				}else{
					$where .= " AND `addrid` in ($lower)";
				}
			}else{
				$where .= " AND `addrid` in ($lower)";
			}
		}

		//价格区间
		if($price != ""){
			$price = explode(",", $price);
			if(empty($price[0])){
				$where .= " AND `price` < " . $price[1];
			}elseif(empty($price[1])){
				$where .= " AND `price` > " . $price[0];
			}else{
				$where .= " AND `price` BETWEEN " . $price[0] . " AND " . $price[1];
			}
		}

		if(!empty($search)){

			siteSearchLog("travel", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__travel_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
	        $results = $dsql->dsqlOper($userSql, "results");
	        foreach ($results as $key => $value) {
	            $sidArr[$key] = $value['id'];
	        }

	        if(!empty($sidArr)){
	            $where .= " AND (`title` like '%$search%' OR `company` in (".join(",",$sidArr)."))";
	        }else{
	            $where .= " AND `title` like '%$search%'";
	        }
		}


		//排序
        switch ($orderby){
            //浏览量
            case 1:
                $orderby_ = " ORDER BY `click` DESC, `weight` DESC, `id` DESC";
                break;
            //发布时间降序
            case 2:
                $orderby_ = " ORDER BY `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
            default:
                $orderby_ = " ORDER BY `weight` DESC, `id` DESC";
                break;
        }

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `price`, `title`, `userid`, `company`, `address`, `pics`, `video`, `typeid`, `tag`, `cityid`, `addrid`, `click`, `pubdate`, `state`  FROM `#@__travel_rentcar` WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__travel_rentcar` WHERE 1 = 1".$where);//print_R($archives);exit;
		//总条数
		$totalCount = getCache("travel_rentcar_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		//会员列表需要统计信息状态
		if($u == 1 && $uid > -1){
			//待审核
			$totalGray = $dsql->dsqlOper($archives." AND `state` = 0", "totalCount");
			//已审核
			$totalAudit = $dsql->dsqlOper($archives." AND `state` = 1", "totalCount");
			//拒绝审核
			$totalRefuse = $dsql->dsqlOper($archives." AND `state` = 2", "totalCount");

			$pageinfo['gray'] = $totalGray;
			$pageinfo['audit'] = $totalAudit;
			$pageinfo['refuse'] = $totalRefuse;
		}

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$sql = $dsql->SetQuery($archives.$orderby_.$where);
		$results = getCache("travel_rentcar_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['typeid']    = $val['typeid'];
				$list[$key]['cityid']    = $val['cityid'];
				$list[$key]['price']     = $val['price'];

				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__travel_rentcartype` WHERE `id` = ".$val['typeid']);
				$ret = $dsql->dsqlOper($sql, "results");
				$list[$key]['typename']   = $ret[0]['typename'] ? $ret[0]['typename'] : '';

				if(!empty($val['addrid'])){
					$addrName = getParentArr("site_area", $val['addrid']);
					global $data;
					$data = "";
					$addrArr = array_reverse(parent_foreach($addrName, "typename"));
					$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
					$list[$key]['addrname']  = $addrArr;
				}else{
					$list[$key]['addrname'] = "";
				}

				$tagArr = array();
				if(!empty($val['tag'])){
					$tag = explode("|", $val['tag']);
					foreach ($tag as $k => $v) {
						$tagArr[$k] = array(
							"jc" => $v,
							"py" =>  GetPinyin($v)
						);
					}
				}
				$list[$key]['tagAll'] = $tagArr;

				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
					$list[$key]['litpic'] = getFilePath($pics[0]);
				}else{
					$list[$key]['litpic']  = '';
				}
				$list[$key]['video'] = $val['video'] ? getFilePath($val['video']) : '';

				$param = array(
					"service" => "travel",
					"template" => "rentcar-detail",
					"id" => $val['id']
				);
				$url = getUrlPath($param);

				$list[$key]['url'] = $url;

				$lower = [];
				$param['id']    = $val['company'];
				$this->param    = $param;
				$store          = $this->storeDetail();
				if(!empty($store)){
					$lower = $store;
				}
				$list[$key]['store'] = $lower;
			}

		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
     * 旅游旅游租车
     * @return array
     */
	public function rentcarDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误
		}

		//$where = " AND `state` = 1";

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `price`, `company`, `pics`, `typeid`, `tag`, `video`, `lng`, `lat`, `cityid`, `addrid`, `address`, `click`, `pubdate`, `state` FROM `#@__travel_rentcar` WHERE `id` = ".$id.$where);
		$results  = getCache("travel_rentcar_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail['typeid']      = $results[0]['typeid'];
			$storeDetail['lng']         = $results[0]['lng'];
			$storeDetail['lat']         = $results[0]['lat'];
			$storeDetail["cityid"]      = $results[0]['cityid'];
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];
			$storeDetail['addrid']      = $results[0]['addrid'];
			$storeDetail['address']     = $results[0]['address'];
			$storeDetail['tag']         = $results[0]['tag'];
			$storeDetail['price']       = $results[0]['price'];
			$storeDetail['video']       = $results[0]['video'];
			$storeDetail['videopath']   = $results[0]['video'] ? getFilePath($results[0]['video']) : '';

			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__travel_rentcartype` WHERE `id` = ".$results[0]['typeid']);
			$ret = $dsql->dsqlOper($sql, "results");
			$storeDetail['typename']   = $ret[0]['typename'] ? $ret[0]['typename'] : '';

			if(!empty($results[0]['addrid'])){
				$addrName = getParentArr("site_area", $results[0]['addrid']);
				global $data;
				$data = "";
				$addrArr = array_reverse(parent_foreach($addrName, "typename"));
				$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
				$storeDetail['addrname']  = $addrArr;
			}else{
				$storeDetail['addrname'] = "";
			}

			$tagArr = array();
			if(!empty($results[0]['tag'])){
				$tag = explode("|", $results[0]['tag']);
				foreach ($tag as $k => $v) {
					$tagArr[$k] = array(
						"jc" => $v,
						"py" =>  GetPinyin($v)
					);
				}
			}
			$storeDetail['tagAll'] = $tagArr;

			//图集
			$imglist = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode(",", $pics);
				foreach ($pics as $key => $value) {
					$imglist[$key]['path'] = getFilePath($value);
					$imglist[$key]['pathSource'] = $value;
				}
			}
			$storeDetail['pics'] = $imglist;

			$lower = [];
			$param['id']    = $results[0]['company'];
			$this->param    = $param;
			$store          = $this->storeDetail();
			if(!empty($store)){
				$lower = $store;
			}
			$storeDetail['store'] = $lower;

			$param = array(
				"service"  => "travel",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "travel",
				"temp"   => "rentcar-detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$storeDetail['collect'] = $collect == "has" ? 1 : 0;

		}
		return $storeDetail;
	}

	/**
	 * 操作旅游签证
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operVisa(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/travel.inc.php");
		$customtravelvisaCheck = (int)$customtravelvisaCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$subtitle        =  filterSensitiveWords(addslashes($param['subtitle']));
		$pics     	     =  $param['pics'];
		$video     	     =  $param['video'];
		$price     	     =  (float)$param['price'];
		$country         =  (int)$param['country'];
		$typeid          =  (int)$param['typeid'];
		$entrytimes      =  $param['entrytimes'];
		$staytimes     	 =  $param['staytimes'];
		$earliestdate    =  GetMkTime($param['earliestdate']);
		$valid     	     =  $param['valid'];
		$processingtime  =  $param['processingtime'];
		$scope     	     =  $param['scope'];
		$materials     	 =  $param['materials'];
		$serviceincludes =  $param['serviceincludes'];
		$incumbents      =  $param['incumbents'];
		$retiree     	 =  $param['retiree'];
		$professional    =  $param['professional'];
		$students     	 =  $param['students'];
		$children     	 =  $param['children'];
		$others     	 =  filterSensitiveWords(addslashes($param['others']));
		$reminder     	 =  filterSensitiveWords(addslashes($param['reminder']));
		$notice     	 =  filterSensitiveWords(addslashes($param['notice']));
		$processingflow  =  $param['processingflow'];

		$pubdate         =  GetMkTime(time());

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
			return array("state" => 200, "info" => $langData['travel'][12][27]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__travel_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['travel'][12][28]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['travel'][12][29]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['travel'][12][30]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title))    return array("state" => 200, "info" => $langData['travel'][11][27]);//请输入标题
			if(empty($scope))    return array("state" => 200, "info" => $langData['travel'][8][76]);//请输入受理范围
			if(empty($serviceincludes))    return array("state" => 200, "info" => $langData['travel'][8][77]);//请输入包含的服务内容
			if(empty($country))    return array("state" => 200, "info" => $langData['travel'][8][78]);//请选择目的地
			if(empty($typeid))     return array("state" => 200, "info" => $langData['travel'][8][70]);//请选择签证类型
			if($price == '')       return array("state" => 200, "info" => $langData['travel'][11][47]);//请输入价格
			if(empty($entrytimes)) return array("state" => 200, "info" => $langData['travel'][8][71]);//请请输入签证次数
			if(empty($staytimes))  return array("state" => 200, "info" => $langData['travel'][8][72]);//请输入停留天数
			if(empty($earliestdate))  return array("state" => 200, "info" => $langData['travel'][8][73]);//请选择最早可预订时间
			if(empty($valid))  return array("state" => 200, "info" => $langData['travel'][8][74]);//请输入有效时间
			if(empty($processingtime))  return array("state" => 200, "info" => $langData['travel'][8][75]);//请输入办理时长
			if(empty($pics))  return array("state" => 200, "info" => $langData['travel'][3][27]);//请至少上传一张图片
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__travel_visa` (`userid`, `company`, `title`, `subtitle`, `country`, `typeid`, `price`, `entrytimes`, `staytimes`, `earliestdate`, `valid`, `processingtime`, `pics`, `video`, `scope`, `materials`, `serviceincludes`, `incumbents`, `retiree`, `professional`, `students`, `children`, `others`, `reminder`, `notice`, `processingflow`, `pubdate`, `state`) VALUES ('$userid', '$company', '$title', '$subtitle', '$country', '$typeid', '$price', '$entrytimes', '$staytimes', '$earliestdate', '$valid', '$processingtime', '$pics', '$video', '$scope', '$materials', '$serviceincludes', '$incumbents', '$retiree', '$professional', '$students', '$children', '$others', '$reminder', '$notice', '$processingflow', '$pubdate', '$customtravelvisaCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){

				if($customtravelvisaCheck){
					updateCache("travel_visa_list", 300);
				}

				clearCache("travel_visa_total", 'key');

				//后台消息通知
				updateAdminNotice("travel", "visa");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['travel']['12']['34']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__travel_visa` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__travel_visa` SET `userid` = '$userid', `company` = '$company', `title` = '$title', `subtitle` = '$subtitle', `country` = '$country', `typeid` = '$typeid', `price` = '$price', `entrytimes` = '$entrytimes', `staytimes` = '$staytimes', `earliestdate` = '$earliestdate', `valid` = '$valid', `processingtime` = '$processingtime', `pics` = '$pics', `video` = '$video', `scope` = '$scope', `materials` = '$materials', `serviceincludes` = '$serviceincludes', `incumbents` = '$incumbents', `retiree` = '$retiree', `professional` = '$professional', `students` = '$students', `children` = '$children', `others` = '$others', `reminder` = '$reminder', `notice` = '$notice', `processingflow` = '$processingflow', `state` = '$customtravelvisaCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['travel']['12']['34']); //保存到数据时发生错误，请检查字段内容！
				}

				updateAdminNotice("travel", "visa");

				// 清除缓存
				clearCache("travel_visa_detail", $id);
				checkCache("travel_visa_list", $id);
				clearCache("travel_visa_total", 'key');


				return $langData['travel'][12][35];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`pics`, l.`video`, s.`userid` FROM `#@__travel_visa` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					//删除图集
					delPicFile($results['pics'], "delAtlas", "travel");
					delPicFile($results['video'], "delVideo", "travel");
					// 清除缓存
					clearCache("travel_visa_detail", $id);
					checkCache("travel_visa_list", $id);
					clearCache("travel_visa_total", 'key');

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__travel_visa` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['travel'][12][36]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['travel'][12][37]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}

	}

	/**
	 * 旅游旅游签证
	 * @return array
	 */
	public function visaList(){
		global $dsql;
		global $langData;
		global $userLogin;
		$pageinfo = $list = array();
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
			}else{
				$search   = $this->param['search'];
				$store    = $this->param['store'];
				$state    = $this->param['state'];
				$typeid   = $this->param['typeid'];
				$country  = $this->param['country'];
				$u        = $this->param['u'];
				$addrid   = $this->param['addrid'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$uid      = $userLogin->getMemberID();
		$userinfo = $userLogin->getMemberInfo();

		if($u!=1){
			$where .= " AND `state` = 1 ";

			$cityid = getCityId($this->param['cityid']);
			if($cityid){
				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `state` = 1 AND `cityid` = $cityid");
				$results = $dsql->dsqlOper($archives, "results");
				if(is_array($results)){
					foreach ($results as $key => $value) {
						$sidArr[$key] = $value['id'];
					}
					if(!empty($sidArr)){
						$where .= " AND `company` in (".join(",",$sidArr).")";
					}else{
						$where .= " AND 0 = 1";
					}
				}else{
					$where .= " AND 0 = 1";
				}
			}
		}else{
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
				return array("state" => 200, "info" => $langData['travel'][12][14]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = $uid");
			$storeRes = $dsql->dsqlOper($sql, "results");
			if($storeRes){
				$where .= " AND `company` = ".$storeRes[0]['id'];
			}else{
				$where .= " AND 1 = 2";
			}

			if($state!=''){
				$where .= " AND `state` = ".$state;
			}
		}

		if($store){
			$where .= " AND `company` = '$store'";
		}

		if($typeid){
			$where .= " AND `typeid` = '$typeid'";
		}

		if($country){
			$where .= " AND `country` = '$country'";
		}

		if(!empty($addrid)){
			if($dsql->getTypeList($addrid, "site_area")){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($dsql->getTypeList($addrid, "site_area"));
				$lower = $addrid.",".join(',',$lower);
			}else{
				$lower = $addrid;
			}
			$sidArr = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `addrid` in ($lower)");
			$results = $dsql->dsqlOper($archives, "results");
			if(is_array($results)){
				foreach ($results as $key => $value) {
					$sidArr[$key] = $value['id'];
				}
				if(!empty($sidArr)){
					$where .= " AND `company` in (".join(",",$sidArr).")";
				}else{
					$where .= " AND 1 = 2";
				}
			}else{
				$where .= " AND 1 = 2";
			}
		}

		if(!empty($search)){

			siteSearchLog("travel", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__travel_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
	        $results = $dsql->dsqlOper($userSql, "results");
	        foreach ($results as $key => $value) {
	            $sidArr[$key] = $value['id'];
	        }

	        if(!empty($sidArr)){
	            $where .= " AND (`title` like '%$search%' OR `company` in (".join(",",$sidArr)."))";
	        }else{
	            $where .= " AND `title` like '%$search%'";
	        }
		}


		//排序
        switch ($orderby){
            //浏览量
            case 1:
                $orderby_ = " ORDER BY `click` DESC, `weight` DESC, `id` DESC";
                break;
            //发布时间降序
            case 2:
                $orderby_ = " ORDER BY `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//价格降序
			case 3:
                $orderby_ = " ORDER BY `price` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//价格升序
			case 4:
                $orderby_ = " ORDER BY `price` ASC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//价格升序
			case 5:
				$orderby_ = " ORDER BY `sale` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
            default:
                $orderby_ = " ORDER BY `weight` DESC, `id` DESC";
                break;
        }

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `price`, `title`, `subtitle`, `userid`, `company`, `pics`, `video`, `typeid`, `country`, `click`, `pubdate`, `state`  FROM `#@__travel_visa` WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__travel_visa` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("travel_visa_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		//会员列表需要统计信息状态
		if($u == 1 && $uid > -1){
			//待审核
			$totalGray = $dsql->dsqlOper($archives." AND `state` = 0", "totalCount");
			//已审核
			$totalAudit = $dsql->dsqlOper($archives." AND `state` = 1", "totalCount");
			//拒绝审核
			$totalRefuse = $dsql->dsqlOper($archives." AND `state` = 2", "totalCount");

			$pageinfo['gray'] = $totalGray;
			$pageinfo['audit'] = $totalAudit;
			$pageinfo['refuse'] = $totalRefuse;
		}

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$sql = $dsql->SetQuery($archives.$orderby_.$where);
		$results = getCache("travel_visa_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['subtitle']  = $val['subtitle'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['typeid']    = $val['typeid'];
				$list[$key]['country']   = $val['country'];
				$list[$key]['price']     = $val['price'];

				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__travel_visatype` WHERE `id` = ".$val['typeid']);
				$ret = $dsql->dsqlOper($sql, "results");
				$list[$key]['typename']   = $ret[0]['typename'] ? $ret[0]['typename'] : '';

				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__travel_visacountrytype` WHERE `id` = ".$val['country']);
				$ret = $dsql->dsqlOper($sql, "results");
				$list[$key]['countryname']   = $ret[0]['typename'] ? $ret[0]['typename'] : '';

				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
					$list[$key]['litpic'] = getFilePath($pics[0]);
				}else{
					$list[$key]['litpic']  = '';
				}
				$list[$key]['video'] = $val['video'] ? getFilePath($val['video']) : '';

				$param = array(
					"service" => "travel",
					"template" => "visa-detail",
					"id" => $val['id']
				);
				$url = getUrlPath($param);

				$list[$key]['url'] = $url;

				$lower = [];
				$param['id']    = $val['company'];
				$this->param    = $param;
				$store          = $this->storeDetail();
				if(!empty($store)){
					$lower = $store;
				}
				$list[$key]['store'] = $lower;
			}

		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
     * 旅游旅游签证
     * @return array
     */
	public function visaDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误
		}

		//$where = " AND `state` = 1";

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `subtitle`, `country`, `entrytimes`, `staytimes`, `earliestdate`, `valid`, `processingtime`, `scope`, `materials`, `serviceincludes`, `incumbents`, `retiree`, `professional`, `students`, `children`, `others`, `reminder`, `notice`, `processingflow`, `userid`, `price`, `company`, `pics`, `typeid`, `video`, `click`, `pubdate`, `state` FROM `#@__travel_visa` WHERE `id` = ".$id.$where);
		$results  = getCache("travel_visa_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail["subtitle"]    = $results[0]['subtitle'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail['typeid']      = $results[0]['typeid'];
			$storeDetail["country"]     = $results[0]['country'];
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];
			$storeDetail['entrytimes']  = $results[0]['entrytimes'];
			$storeDetail['staytimes']   = $results[0]['staytimes'];
			$storeDetail['earliestdates']   = $results[0]['earliestdate'];
			$storeDetail['earliestdate']= $results[0]['earliestdate'] ? date("Y-m-d", $results[0]['earliestdate']) : '';
			$storeDetail['price']       = $results[0]['price'];
			$storeDetail['video']       = $results[0]['video'];
			$storeDetail['videopath']   = $results[0]['video'] ? getFilePath($results[0]['video']) : '';
			$storeDetail['valid']       = $results[0]['valid'];
			$storeDetail['processingtime']= $results[0]['processingtime'];
			$storeDetail['scope']       = $results[0]['scope'];
			$storeDetail['materials']   = $results[0]['materials'];
			$storeDetail['serviceincludes']= $results[0]['serviceincludes'];

			$storeDetail['incumbents']  = $results[0]['incumbents'];

			$storeDetail['incumbentsnum']  = $results[0]['incumbents'] ? count(explode(',', $results[0]['incumbents'])) : 0;

			$storeDetail['retiree']     = $results[0]['retiree'];

			$storeDetail['retireenum']  = $results[0]['retiree'] ? count(explode(',', $results[0]['retiree'])) : 0;

			$storeDetail['professional']= $results[0]['professional'];

			$storeDetail['professionalnum']  = $results[0]['professional'] ? count(explode(',', $results[0]['professional'])) : 0;

			$storeDetail['students']    = $results[0]['students'];

			$storeDetail['studentsnum']  = $results[0]['students'] ? count(explode(',', $results[0]['students'])) : 0;

			$storeDetail['children']    = $results[0]['children'];

			$storeDetail['childrennum']  = $results[0]['children'] ? count(explode(',', $results[0]['children'])) : 0;

			$storeDetail['others']      = $results[0]['others'];
			$storeDetail['reminder']    = $results[0]['reminder'];
			$storeDetail['notice']      = $results[0]['notice'];
			$storeDetail['processingflow']= $results[0]['processingflow'];

			// $archives = $dsql->SetQuery("SELECT `id` FROM `#@__travelcommon` WHERE `aid` = ".$results[0]['id']." AND `type` = 4 AND `ischeck` = 1 AND `floor` = 0");
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'travel-visa' AND `aid` = '$id' AND `pid` = 0");
			$totalCount = $dsql->dsqlOper($archives, "totalCount");
            $storeDetail['common'] = $totalCount;

			$processingflowArr = array();
			if(!empty($results[0]['processingflow'])){
				$processingflow = explode("|||", $results[0]['processingflow']);
				foreach ($processingflow as $key => $value) {
					$val = explode("$$$", $value);
					$processingflowArr[$key]['title'] = $val[0];
					$processingflowArr[$key]['note'] = $val[1];
				}
			}
			$storeDetail['processingflowArr']      = $processingflowArr;

			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__travel_visatype` WHERE `id` = ".$results[0]['typeid']);
			$ret = $dsql->dsqlOper($sql, "results");
			$storeDetail['typename']   = $ret[0]['typename'] ? $ret[0]['typename'] : '';

			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__travel_visacountrytype` WHERE `id` = ".$results[0]['country']);
			$ret = $dsql->dsqlOper($sql, "results");
			$storeDetail['countryname']   = $ret[0]['typename'] ? $ret[0]['typename'] : '';

			//图集
			$imglist = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode(",", $pics);
				foreach ($pics as $key => $value) {
					$imglist[$key]['path'] = getFilePath($value);
					$imglist[$key]['pathSource'] = $value;
				}
			}
			$storeDetail['pics'] = $imglist;

			$lower = [];
			$param['id']    = $results[0]['company'];
			$this->param    = $param;
			$store          = $this->storeDetail();
			if(!empty($store)){
				$lower = $store;
			}
			$storeDetail['store'] = $lower;

			$param = array(
				"service"  => "travel",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "travel",
				"temp"   => "visa-detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$storeDetail['collect'] = $collect == "has" ? 1 : 0;

		}
		return $storeDetail;
	}

	/**
	 * 操作周边游
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operAgency(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/travel.inc.php");
		$customtravelagencyCheck = (int)$customtravelagencyCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$addrid          =  (int)$param['addrid'];
		$cityid          =  (int)$param['cityid'];
		$typeid          =  $param['typeid'];
		$address     	 =  $param['address'];
		$tag     	     =  $param['tag'];
		$price     	     =  (float)$param['price'];
		$pics     	     =  $param['pics'];
		$imglist     	 =  $param['imglist'];
		$video     	     =  $param['video'];
		$missiontime     =  $param['missiontime'];
		$travelservice   =   $param['travelservice'];
		$note            =  filterSensitiveWords(addslashes($param['note']));
		$expense         =  filterSensitiveWords(addslashes($param['expense']));
		$instructions    =  filterSensitiveWords(addslashes($param['instructions']));
		$itinerary       =   $param['itinerary'];
		$ticketlist      =  $param['ticketlist'];
		$pubdate         =  GetMkTime(time());
		$ticketlist      =  json_decode($ticketlist, true);



		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
			return array("state" => 200, "info" => $langData['travel'][12][27]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__travel_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['travel'][12][28]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['travel'][12][29]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['travel'][12][30]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title))  return array("state" => 200, "info" => $langData['travel'][3][30]);//请输入景点名称
			if($typeid == '') return array("state" => 200, "info" => $langData['travel'][3][28]);//请选择旅游类型
			if(empty($addrid)) return array("state" => 200, "info" => $langData['travel'][3][29]);//请请选择区域
			if(empty($address))return array("state" => 200, "info" => $langData['travel'][8][61]);//请输入详细地址
			if(empty($pics))   return array("state" => 200, "info" => $langData['travel'][3][27]);//请至少上传一张图片
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__travel_agency` (`tag`, `imglist`, `userid`, `company`, `title`, `typeid`, `cityid`, `addrid`, `address`, `missiontime`, `pics`, `video`, `travelservice`, `note`, `itinerary`, `expense`, `instructions`, `pubdate`, `state`) VALUES ('$tag', '$imglist', '$userid', '$company', '$title','$typeid', '$cityid', '$addrid', '$address', '$missiontime', '$pics', '$video', '$travelservice', '$note', '$itinerary', '$expense', '$instructions', '$pubdate', '$customtravelagencyCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){
				//保存房间信息
				if(!empty($ticketlist)){
					foreach($ticketlist as $val){
						$specialtime = stripslashes($val['specialtime']);
						$specialtime = json_decode($specialtime, true);
						$specialtime = serialize($specialtime);
						$pics = $dsql->SetQuery("INSERT INTO `#@__travel_ticketinfo` (`typeid`, `title`, `ticketid`, `price`, `specialtime`, `pubdate`) VALUES ('1', '$val[title]', '$aid', '$val[price]', '$specialtime', '$pubdate')");
						$dsql->dsqlOper($pics, "update");
					}
				}

				if($customtravelagencyCheck){
					updateCache("travel_agency_list", 300);
				}

				clearCache("travel_agency_total", 'key');

				//后台消息通知
				updateAdminNotice("travel", "agency");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['travel']['12']['34']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__travel_agency` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__travel_agency` SET `tag` = '$tag', `imglist` = '$imglist', `userid` = '$userid', `company` = '$company', `title` = '$title', `typeid` = '$typeid', `cityid` = '$cityid', `addrid` = '$addrid', `address` = '$address', `missiontime` = '$missiontime', `pics` = '$pics', `video` = '$video', `travelservice` = '$travelservice', `note` = '$note', `itinerary` = '$itinerary', `expense` = '$expense', `instructions` = '$instructions', `state` = '$customtravelagencyCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['travel']['12']['34']); //保存到数据时发生错误，请检查字段内容！
				}

				if(!empty($ticketlist)){
					foreach($ticketlist as $val){
						$specialtime = stripslashes($val['specialtime']);
						$specialtime = json_decode($specialtime, true);
						$specialtime = serialize($specialtime);

						if(!empty($val['id'])){
							$archives = $dsql->SetQuery("UPDATE `#@__travel_ticketinfo` SET `typeid` = '1', `title` = '$val[title]', `ticketid` = '$id', `price` = '$val[price]', `specialtime` = '$specialtime' WHERE `id` = ".$val['id']);
							$dsql->dsqlOper($archives, "update");
						}else{
							$pics = $dsql->SetQuery("INSERT INTO `#@__travel_ticketinfo` (`typeid`, `title`, `ticketid`, `price`, `specialtime`, `pubdate`) VALUES ('1', '$val[title]', '$id', '$val[price]', '$specialtime', '$pubdate')");
							$dsql->dsqlOper($pics, "update");
						}
					}
				}

				updateAdminNotice("travel", "agency");

				// 清除缓存
				clearCache("travel_agency_detail", $id);
				checkCache("travel_agency_list", $id);
				clearCache("travel_agency_total", 'key');


				return $langData['travel'][12][35];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`pics`, l.`video`, l.`imglist`, s.`userid` FROM `#@__travel_agency` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					//删除图集
					delPicFile($results['pics'], "delAtlas", "travel");
					delPicFile($results['imglist'], "delAtlas", "travel");
					delPicFile($results['video'], "delVideo", "travel");
					// 清除缓存
					clearCache("travel_agency_detail", $id);
					checkCache("travel_agency_list", $id);
					clearCache("travel_agency_total", 'key');

					$sql = $dsql->SetQuery("DELETE FROM `#@__travel_ticketinfo` WHERE `typeid` =1 and `ticketid` = ".$id);
					$dsql->dsqlOper($sql, "update");

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__travel_agency` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['travel'][12][36]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['travel'][12][37]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}

	}

	/**
	 * 周边游
	 * @return array
	 */
	public function agencyList(){
		global $dsql;
		global $langData;
		global $userLogin;
		$pageinfo = $list = array();
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
			}else{
				$search   = $this->param['search'];
				$store    = $this->param['store'];
				$state    = $this->param['state'];
				$typeid   = $this->param['typeid'];
				$noid     = $this->param['noid'];
				$price    = $this->param['price'];
				$flag     = $this->param['flag'];
				$u        = $this->param['u'];
				$addrid   = $this->param['addrid'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$uid      = $userLogin->getMemberID();
		$userinfo = $userLogin->getMemberInfo();

		if($u!=1){
			$where .= " AND `state` = 1 ";

			$cityid = getCityId($this->param['cityid']);
			if($cityid){
				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `state` = 1 AND `cityid` = $cityid");
				$results = $dsql->dsqlOper($archives, "results");
				if(is_array($results)){
					foreach ($results as $key => $value) {
						$sidArr[$key] = $value['id'];
					}
					if(!empty($sidArr)){
						$where .= " AND `company` in (".join(",",$sidArr).")";
					}else{
						$where .= " AND 0 = 1";
					}
				}else{
					$where .= " AND 0 = 1";
				}
			}
		}else{
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
				return array("state" => 200, "info" => $langData['travel'][12][14]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = $uid");
			$storeRes = $dsql->dsqlOper($sql, "results");
			if($storeRes){
				$where .= " AND `company` = ".$storeRes[0]['id'];
			}else{
				$where .= " AND 1 = 2";
			}

			if($state!=''){
				$where .= " AND `state` = ".$state;
			}
		}

		if($store){
			$where .= " AND `company` = '$store'";
		}

		if($typeid != ''){
			$where .= " AND `typeid` = '$typeid'";
		}

		if($noid){
			$where .= " AND `id` not in ($noid)";
		}

		if($flag){
			$where .= " AND `flag` in ($flag)";
		}

		if(!empty($addrid)){
			if($dsql->getTypeList($addrid, "site_area")){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($dsql->getTypeList($addrid, "site_area"));
				$lower = $addrid.",".join(',',$lower);
			}else{
				$lower = $addrid;
			}
			$where .= " AND `addrid` in ($lower)";

		}

		//价格区间
		if($price != ""){
			$price = explode(",", $price);
			if(empty($price[0])){
				$where .= " AND ( (SELECT avg(c.`price`) FROM `#@__travel_ticketinfo` c WHERE c.`ticketid` = l.`id` AND c.`typeid` = '1') is null or (SELECT avg(c.`price`) FROM `#@__travel_ticketinfo` c WHERE c.`ticketid` = l.`id` AND c.`typeid` = '1' AND c.`price` < " . $price[1]."))";
			}elseif(empty($price[1])){
				$where .= " AND (SELECT avg(c.`price`) FROM `#@__travel_ticketinfo` c WHERE c.`ticketid` = l.`id` AND c.`typeid` = '1' AND c.`price` > " . $price[0].")";
			}else{
				$where .= " AND (SELECT avg(c.`price`) FROM `#@__travel_ticketinfo` c WHERE c.`ticketid` = l.`id` AND c.`typeid` = '1' AND c.`price` BETWEEN " . $price[0] . " AND " . $price[1].")";
			}
		}

		if(!empty($search)){

			siteSearchLog("travel", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__travel_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
	        $results = $dsql->dsqlOper($userSql, "results");
	        foreach ($results as $key => $value) {
	            $sidArr[$key] = $value['id'];
	        }

	        if(!empty($sidArr)){
	            $where .= " AND (`title` like '%$search%' OR `company` in (".join(",",$sidArr)."))";
	        }else{
	            $where .= " AND `title` like '%$search%'";
	        }
		}


		//排序
        switch ($orderby){
            //浏览量
            case 1:
                $orderby_ = " ORDER BY `click` DESC, `weight` DESC, `id` DESC";
                break;
            //发布时间降序
            case 2:
                $orderby_ = " ORDER BY `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//销量降序
			case 3:
                $orderby_ = " ORDER BY `sale` DESC, `click` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//景点级别降序
			case 4:
                $orderby_ = " ORDER BY `flag` DESC, `click` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//景点级别降序
			case 5:
				$orderby_ = " ORDER BY `price` DESC, `click` DESC, `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
            default:
                $orderby_ = " ORDER BY `pubdate` DESC, `weight` DESC, `id` DESC";
                break;
        }

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `flag`, `address`, `missiontime`, `pics`, `video`, `travelservice`, `tag`, `cityid`, `typeid`, `addrid`, `click`, `pubdate`, (SELECT avg(c.`price`) FROM `#@__travel_ticketinfo` c WHERE c.`ticketid` = l.`id` AND c.`typeid` = '1' ) AS price, (SELECT sum(c.`sale`) FROM `#@__travel_ticketinfo` c WHERE c.`ticketid` = l.`id` AND c.`typeid` = '1' ) AS sale, `state`  FROM `#@__travel_agency` l WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__travel_agency` l WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("travel_agency_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		//会员列表需要统计信息状态
		if($u == 1 && $uid > -1){
			//待审核
			$totalGray = $dsql->dsqlOper($archives." AND `state` = 0", "totalCount");
			//已审核
			$totalAudit = $dsql->dsqlOper($archives." AND `state` = 1", "totalCount");
			//拒绝审核
			$totalRefuse = $dsql->dsqlOper($archives." AND `state` = 2", "totalCount");

			$pageinfo['gray'] = $totalGray;
			$pageinfo['audit'] = $totalAudit;
			$pageinfo['refuse'] = $totalRefuse;
		}

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$sql = $dsql->SetQuery($archives.$orderby_.$where);
		$results = getCache("travel_agency_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['missiontime']= $val['missiontime'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]["price"]     = $val["price"] ? round($val["price"], 2) : 0;
				$list[$key]['company']   = $val['company'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['travelservice']= $val['travelservice'];
				$list[$key]['cityid']    = $val['cityid'];
				$list[$key]['typeid']    = $val['typeid'];
				$list[$key]['typename']  = $val['typeid']!='' ? $this->gettypename("travelagency_type", $val['typeid']) : '';
				$list[$key]['flag']      = $val['flag'];
				$list[$key]['flagname']  = $val['flag'] ? $this->gettypename("star_type", $val['flag']) : '';

				if(!empty($val['addrid'])){
					$addrName = getParentArr("site_area", $val['addrid']);
					global $data;
					$data = "";
					$addrArr = array_reverse(parent_foreach($addrName, "typename"));
					$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
					$list[$key]['addrname']  = $addrArr;
				}else{
					$list[$key]['addrname'] = "";
				}

				$tagArr = array();
				if(!empty($val['tag'])){
					$tag = explode("|", $val['tag']);
					foreach ($tag as $k => $v) {
						$tagArr[$k] = array(
							"jc" => $v,
							"py" =>  GetPinyin($v)
						);
					}
				}
				$list[$key]['tagAll'] = $tagArr;

				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
					$list[$key]['litpic'] = getFilePath($pics[0]);
				}else{
					$list[$key]['litpic']  = '';
				}
				$list[$key]['video'] = $val['video'] ? getFilePath($val['video']) : '';

				$param = array(
					"service" => "travel",
					"template" => "agency-detail",
					"id" => $val['id']
				);
				$url = getUrlPath($param);

				$list[$key]['url'] = $url;

				$lower = [];
				$param['id']    = $val['company'];
				$this->param    = $param;
				$store          = $this->storeDetail();
				if(!empty($store)){
					$lower = $store;
				}
				$list[$key]['store'] = $lower;
			}

		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
     * 周边游
     * @return array
     */
	public function agencyDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误
		}

		//$where = " AND `state` = 1";

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `flag`, `typeid`, `instructions`, `expense`, `itinerary`, `note`, `company`, `imglist`, `pics`, `missiontime`, `tag`, `video`, `cityid`, `travelservice`, `addrid`, `address`, `click`, `pubdate`, (SELECT avg(c.`price`) FROM `#@__travel_ticketinfo` c WHERE c.`ticketid` = l.`id` AND c.`typeid` = '1' ) AS price, `state` FROM `#@__travel_agency` l WHERE `id` = ".$id.$where);
		$results  = getCache("travel_agency_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail['missiontime'] = $results[0]['missiontime'];
			$storeDetail['typeid']      = $results[0]['typeid'];
			$storeDetail["cityid"]      = $results[0]['cityid'];
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];
			$storeDetail['addrid']      = $results[0]['addrid'];
			$storeDetail['address']     = $results[0]['address'];
			$storeDetail['note']        = $results[0]['note'];
			$storeDetail["price"]       = $results[0]['price'] ? round($results[0]['price'], 2) : 0;
			$storeDetail['tag']         = $results[0]['tag'];
			$storeDetail['expense']     = $results[0]['expense'];
			$storeDetail['instructions']= $results[0]['instructions'];
			$storeDetail['video']       = $results[0]['video'];
			$storeDetail['videopath']   = $results[0]['video'] ? getFilePath($results[0]['video']) : '';
			$storeDetail['travelservice']  = $results[0]['travelservice'];
			$storeDetail['typename']    = $results[0]['typeid']!='' ? $this->gettypename("travelagency_type", $results[0]['typeid']) : '';
			$storeDetail['flag']        = $results[0]['flag'];
			$storeDetail['flagname']    = $results[0]['flag'] ? $this->gettypename("star_type", $results[0]['flag']) : '';

			// $archives = $dsql->SetQuery("SELECT `id` FROM `#@__travelcommon` WHERE `aid` = ".$results[0]['id']." AND `type` = 3 AND `ischeck` = 1 AND `floor` = 0");
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'travel-agency' AND `aid` = '$id' AND `pid` = 0");
			$totalCount = $dsql->dsqlOper($archives, "totalCount");
            $storeDetail['common'] = $totalCount;

			$itineraryArr = array();
			if(!empty($results[0]['itinerary'])){
				$itinerary = explode("|||", $results[0]['itinerary']);
				foreach ($itinerary as $key => $value) {
					$val = explode("$$$", $value);
					$itineraryArr[$key]['title'] = $val[0];
					$itineraryArr[$key]['note'] = $val[1];
				}
			}
			$storeDetail['itineraryArr']      = $itineraryArr;


			//获取房间信息
			$workArr = [];
			$sql = $dsql->SetQuery("SELECT `id`, `title`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `typeid` = '1' and `ticketid` = '$id' ORDER BY  `weight` DESC, `pubdate` DESC, `id` DESC");
			$res = $dsql->dsqlOper($sql, "results");
			if(!empty($res)){
				foreach($res as $key=> $val){
					$workArr[$key]['id']           = $val['id'];
					$workArr[$key]['title']        = $val['title'];
					$workArr[$key]['price']        = $val['price'];
					$workArr[$key]['specialtime']  = $val['specialtime'] ? unserialize($val['specialtime']) : '';
					$workArr[$key]['specialtimejson']  = $val['specialtime'] ? json_encode(unserialize($val['specialtime'])) : '';

				}
			}
			$storeDetail["workArr"]         = $workArr;

			if(!empty($results[0]['addrid'])){
				$addrName = getParentArr("site_area", $results[0]['addrid']);
				global $data;
				$data = "";
				$addrArr = array_reverse(parent_foreach($addrName, "typename"));
				$addrArr = count($addrArr) > 2 ? array_splice($addrArr, 1) : $addrArr;
				$storeDetail['addrname']  = $addrArr;
			}else{
				$storeDetail['addrname'] = "";
			}

			$tagArr = array();
			if(!empty($results[0]['tag'])){
				$tag = explode("|", $results[0]['tag']);
				foreach ($tag as $k => $v) {
					$tagArr[$k] = array(
						"jc" => $v,
						"py" =>  GetPinyin($v)
					);
				}
			}
			$storeDetail['tagAll'] = $tagArr;

			//图集
			$imglist = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode(",", $pics);
				foreach ($pics as $key => $value) {
					$imglist[$key]['path'] = getFilePath($value);
					$imglist[$key]['pathSource'] = $value;
				}
			}
			$storeDetail['pics'] = $imglist;

			//图集
			$imglistArr = array();
			$imglist = $results[0]['imglist'];
			if(!empty($imglist)){
				$imglist = explode(",", $imglist);
				foreach ($imglist as $key => $value) {
					$imglistArr[$key]['path'] = getFilePath($value);
					$imglistArr[$key]['pathSource'] = $value;
				}
			}
			$storeDetail['imglist'] = $imglistArr;

			$lower = [];
			$param['id']    = $results[0]['company'];
			$this->param    = $param;
			$store          = $this->storeDetail();
			if(!empty($store)){
				$lower = $store;
			}
			$storeDetail['store'] = $lower;

			$param = array(
				"service"  => "travel",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "travel",
				"temp"   => "agency-detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$storeDetail['collect'] = $collect == "has" ? 1 : 0;

		}
		return $storeDetail;
	}

	/**
	 * 操作旅游视频
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operVideo(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/travel.inc.php");
		$customtravelvideoCheck = (int)$customtravelvideoCheck;

		$userid      = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$litpic          =  $param['litpic'];
		$video     	     =  $param['video'];
		$pubdate         =  GetMkTime(time());

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
			return array("state" => 200, "info" => $langData['travel'][12][27]);//商家权限验证失败！
		}

		$usertype = 0;
		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__travel_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			$usertype = 1;
			$userid   = $userResult[0]['id'];
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title))  return array("state" => 200, "info" => $langData['travel'][11][27]);//请输入标题
			if(empty($litpic)) return array("state" => 200, "info" => $langData['travel'][12][50]);//请上传封面
			if(empty($video))  return array("state" => 200, "info" => $langData['travel'][12][49]);//请上传视频
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！
		}

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__travel_video` (`usertype`, `userid`, `title`, `litpic`, `video`, `pubdate`, `state`) VALUES ('$usertype', '$userid', '$title', '$litpic', '$video', '$pubdate', '$customtravelvideoCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){

				if($customtravelvideoCheck){
					updateCache("travel_video_list", 300);
				}

				clearCache("travel_video_total", 'key');

				//后台消息通知
				updateAdminNotice("travel", "video");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['travel']['12']['34']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__travel_video` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__travel_video` SET `usertype` = '$usertype', `userid` = '$userid', `title` = '$title', `litpic` = '$litpic', `video` = '$video', `state` = '$customtravelvideoCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['travel']['12']['34']); //保存到数据时发生错误，请检查字段内容！
				}

				updateAdminNotice("travel", "video");

				// 清除缓存
				clearCache("travel_video_detail", $id);
				checkCache("travel_video_list", $id);
				clearCache("travel_video_total", 'key');


				return $langData['travel'][12][35];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			if($userResult){
				$archives = $dsql->SetQuery("SELECT l.`id`, l.`litpic`, l.`video`, l.`userid` FROM `#@__travel_video` l WHERE l.`id` = '$id' AND l.`userid` = " . $userResult[0]['id']);

			}else{
				$archives = $dsql->SetQuery("SELECT l.`id`, l.`litpic`, l.`video`, l.`userid` FROM `#@__travel_video` l WHERE l.`id` = '$id' AND l.`userid` = " . $userid);
			}
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					//删除图集
					delPicFile($results['litpic'], "delAtlas", "travel");
					delPicFile($results['video'], "delVideo", "travel");
					// 清除缓存
					clearCache("travel_video_detail", $id);
					checkCache("travel_video_list", $id);
					clearCache("travel_video_total", 'key');

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__travel_video` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['travel'][12][36]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['travel'][12][37]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}

		}

	}

	/**
	 * 旅游视频
	 * @return array
	 */
	public function videoList(){
		global $dsql;
		global $langData;
		global $userLogin;
		$pageinfo = $list = array();
		$page = $pageSize = $where = $where1 =  "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
			}else{
				$search   = $this->param['search'];
				$store    = $this->param['store'];
				$state    = $this->param['state'];
				$typeid   = $this->param['typeid'];
				$noid     = $this->param['noid'];
				$u        = $this->param['u'];
				$usertype = $this->param['usertype'];
				$addrid   = $this->param['addrid'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$uid      = $userLogin->getMemberID();
		$userinfo = $userLogin->getMemberInfo();


		$zj_state = "";
		if($u != 1){
			$cityid = getCityId($this->param['cityid']);
			if($cityid){
				$where1 .= " AND `cityid` in (".$cityid.")";
			}

			$where .= " AND s.`state` = 1 ";

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE 1=1 $where1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$storeList = array();
				foreach ($ret as $k => $v) {
					array_push($storeList, $v['id']);
				}
				$where .= " AND ( s.`usertype` = 0 OR (s.`state` = 1 AND s.`usertype` = 1 AND s.`userid` in(".join(',',$storeList).")))";
			}

			$zj_state = " AND z.`state` = 1";

		}else{
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$where .= " AND s.`usertype` = 1 AND s.`userid` = ".$ret[0]['id'];
			}else{
				$where .= " AND s.`usertype` = 0 AND s.`userid` = ".$uid;
			}

			if($state != ""){
				$where .= " AND s.`state` = ".$state;
			}
		}

		if($usertype!=''){
			$where .= " AND s.`usertype` = '$usertype'";
		}

		if($noid){
			$where .= " AND s.`id` not in($noid)";
		}

		if(!empty($search)){

			//搜索记录
			if(!empty($search)){
				siteSearchLog("travel", $search);
			}
			$title = explode(" ", $search);
			$w = array();
			foreach ($title as $k => $v) {
				if(!empty($v)){
					$w[] = " s.`title` like '%".$v."%'";
				}
			}
			$where .= " AND (".join(" OR ", $w).")";
		}


		//排序
        switch ($orderby){
            //浏览量
            case 1:
                $orderby_ = " ORDER BY s.`click` DESC, s.`weight` DESC, s.`id` DESC";
                break;
            //发布时间降序
            case 2:
                $orderby_ = " ORDER BY s.`pubdate` DESC, s.`weight` DESC, s.`id` DESC";
				break;
            default:
                $orderby_ = " ORDER BY s.`weight` DESC, s.`id` DESC";
                break;
        }

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT s.`id`, s.`usertype`, s.`userid`, s.`title`, s.`litpic`, s.`video`, s.`click`, s.`pubdate`, s.`state`, z.`id` as comid FROM `#@__travel_video` s LEFT JOIN `#@__travel_store` z ON z.`id` = s.`userid` WHERE (s.`usertype` = 0 OR (s.`usertype` = 1 AND s.`userid` = z.`id`".$zj_state.")) ".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(s.`id`) total FROM `#@__travel_video` s LEFT JOIN `#@__travel_store` z ON z.`id` = s.`userid` WHERE (s.`usertype` = 0 OR (s.`usertype` = 1 AND s.`userid` = z.`id`".$zj_state.")) ".$where);
		//总条数
		$totalCount = getCache("travel_video_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		//会员列表需要统计信息状态
		if($u == 1 && $uid > -1){
			//待审核
			$totalGray = $dsql->dsqlOper($archives." AND s.`state` = 0", "totalCount");
			//已审核
			$totalAudit = $dsql->dsqlOper($archives." AND s.`state` = 1", "totalCount");
			//拒绝审核
			$totalRefuse = $dsql->dsqlOper($archives." AND s.`state` = 2", "totalCount");

			$pageinfo['gray'] = $totalGray;
			$pageinfo['audit'] = $totalAudit;
			$pageinfo['refuse'] = $totalRefuse;
		}

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$sql = $dsql->SetQuery($archives.$orderby_.$where);
		$results = getCache("travel_video_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['usertype']  = $val['usertype'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['litpic']    = $val['litpic'] ? getFilePath($val['litpic']) : '';
				$list[$key]['video']     = $val['video']  ? getFilePath($val['video'])  : '';

				$param = array(
					"service" => "travel",
					"template" => "video-detail",
					"id" => $val['id']
				);
				$url = getUrlPath($param);
				$list[$key]['url'] = $url;

				$userid = $val['userid'];
				$nickname = $photo =  "";
				if($val['usertype']==1){
					$archives = $dsql->SetQuery("SELECT m.`nickname`, m.`company`, m.`photo`, zjcom.`title`, zjcom.`pics`,  zjcom.`id` zjcomid  FROM `#@__member` m LEFT JOIN `#@__travel_store` zjcom ON m.`id` = zjcom.`userid` WHERE zjcom.`state` = 1 AND zjcom.`id` = ".$userid);
					$member = $dsql->dsqlOper($archives, "results");
					if($member){
						$picsArr   = !empty($member[0]['pics']) ? explode(',', $member[0]['pics']) : '';
						$photo     = $picsArr[0] ? getFilePath($picsArr[0]) : $member[0]['photo'];
						$title     = $member[0]['title'];
						$zjcomid   = $member[0]['zjcomid'];
					}
					$url = getUrlPath(array("service" => "travel", "template" => "store-detail", "id" => $zjcomid));
					$userArr['url']       = $url;
					$userArr['photo']     = $photo;
					$userArr['title']     = $title;
					$userArr['nickname']  = $member[0]['company'] ? $member[0]['company'] : $member[0]['nickname'];
				}else{
					$archives = $dsql->SetQuery("SELECT `nickname`, `username`, `photo` FROM `#@__member`  WHERE  `id` = ".$userid);
					$member   = $dsql->dsqlOper($archives, "results");
					if($member){
						$nickname  = $member[0]['nickname'] ? $member[0]['nickname'] : $member[0]['username'];
						$photo     = getFilePath($member[0]['photo']);
					}
					$userArr['nickname']  = $nickname;
					$userArr['photo']     = $photo;
				}
				$list[$key]['user'] = $userArr;

				/* if($val['usertype'] == 1){
					$lower = [];
					$param['id']    = $val['comid'];
					$this->param    = $param;
					$store          = $this->storeDetail();
					if(!empty($store)){
						$lower = $store;
					}
					$list[$key]['store'] = $lower;
				}else{

				} */

			}

		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
     * 旅游视频
     * @return array
     */
	public function videoDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uids = $uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误
		}

		//$where = " AND `state` = 1";

		$archives = $dsql->SetQuery("SELECT s.`id`, s.`zan`, s.`usertype`, s.`userid`, s.`title`, s.`litpic`, s.`video`, s.`click`, s.`pubdate`, s.`state`, z.`id` as comid FROM `#@__travel_video` s LEFT JOIN `#@__travel_store` z ON z.`id` = s.`userid` WHERE s.`id` = ".$id.$where);
		$results  = getCache("travel_video_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['usertype']    = $results[0]['usertype'];
			$storeDetail['zan']         = $results[0]['zan'];

			$storeDetail['litpicpath']  = $results[0]['litpic'];
			$storeDetail['litpic']      = $results[0]['litpic'] ? getFilePath($results[0]['litpic']) : '';
			$storeDetail['video']       = $results[0]['video'];
			$storeDetail['videopath']   = $results[0]['video'] ? getFilePath($results[0]['video']) : '';

			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];

			$userid = $results[0]['userid'];
			$uid = $nickname = $photo =  "";
			if($results[0]['usertype']==1){
				$archives = $dsql->SetQuery("SELECT m.`id` uid , m.`nickname`, m.`company`, m.`photo`, zjcom.`title`, zjcom.`pics`,  zjcom.`id` zjcomid  FROM `#@__member` m LEFT JOIN `#@__travel_store` zjcom ON m.`id` = zjcom.`userid` WHERE zjcom.`state` = 1 AND zjcom.`id` = ".$userid);
				$member = $dsql->dsqlOper($archives, "results");
				if($member){
					$picsArr   = !empty($member[0]['pics']) ? explode(',', $member[0]['pics']) : '';
					$photo     = $member[0]['photo'] ? getFilePath($member[0]['photo']) : getFilePath($picsArr[0]);
					$title     = $member[0]['title'];
					$uid       = $member[0]['uid'];
					$zjcomid   = $member[0]['zjcomid'];
				}
				$userArr['uid']       = $uid;
				$userArr['photo']     = $photo;
				$userArr['title']     = $title;
				$userArr['nickname']  = $member[0]['company'] ? $member[0]['company'] : $member[0]['nickname'];
			}else{
				$archives = $dsql->SetQuery("SELECT `id` uid, `nickname`, `username`, `photo` FROM `#@__member`  WHERE  `id` = ".$userid);
				$member   = $dsql->dsqlOper($archives, "results");
				if($member){
					$nickname  = $member[0]['nickname'] ? $member[0]['nickname'] : $member[0]['username'];
					$photo     = getFilePath($member[0]['photo']);
					$uid       = $member[0]['uid'];
				}
				$userArr['uid']       = $uid;
				$userArr['nickname']  = $nickname;
				$userArr['photo']     = $photo;
			}
			$storeDetail['user'] = $userArr;

			//是否相互关注
			if($uids != -1){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `for` = '' AND `tid` = $uids AND `fid` = $uid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret && is_array($ret)){
					$storeDetail['isfollow'] = 1;
				}

				//验证是否已经点赞
				$zanparams = array(
					"module" => "travel",
					"temp"   => "video-detail",
					"id"     => $id,
					"check"  => 1
				);
				$zan = checkIsZan($zanparams);
				$storeDetail['up'] = $zan == "has" ? 1 : 0;
			}

			// $archives = $dsql->SetQuery("SELECT `id` FROM `#@__travelcommon` WHERE `aid` = ".$results[0]['id']." AND `type` = 0 AND `ischeck` = 1 AND `floor` = 0");
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'travel-video' AND `aid` = '$id' AND `pid` = 0");
			$totalCount = $dsql->dsqlOper($archives, "totalCount");
            $storeDetail['common'] = $totalCount;

			if($results[0]['usertype']==1){
				$param = array(
					"service"  => "travel",
					"template" => "store-detail",
					"id"       => $results[0]['company']
				);
				$storeDetail['companyurl'] = getUrlPath($param);
			}

			//验证是否已经收藏
			$params = array(
				"module" => "travel",
				"temp"   => "video-detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$storeDetail['collect'] = $collect == "has" ? 1 : 0;

		}
		return $storeDetail;
	}

	/**
	 * 操作旅游攻略
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operStrategy(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/travel.inc.php");
		$customtravelstrategyCheck = (int)$customtravelstrategyCheck;

		$userid      = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$litpic          =  $param['litpic'];
		$typeid          =  (int)$param['typeid'];
		$pics            =  $param['pics'];
		$note     	     =  $param['note'];
		$pubdate         =  GetMkTime(time());

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
			return array("state" => 200, "info" => $langData['travel'][12][27]);//商家权限验证失败！
		}

		$usertype = 0;
		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__travel_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			$usertype = 1;
			$userid   = $userResult[0]['id'];
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title))  return array("state" => 200, "info" => $langData['travel'][11][27]);//请输入标题
			if(empty($typeid)) return array("state" => 200, "info" => $langData['travel'][6][14]);//请选择攻略类型
			if(empty($pics))   return array("state" => 200, "info" => $langData['travel'][3][27]);//请至少上传一张图片
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！
		}

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__travel_strategy` (`typeid`, `usertype`, `userid`, `title`, `litpic`, `pics`, `note`, `pubdate`, `state`) VALUES ('$typeid', '$usertype', '$userid', '$title', '$litpic', '$pics', '$note', '$pubdate', '$customtravelstrategyCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){

				if($customtravelstrategyCheck){
					updateCache("travel_strategy_list", 300);
				}

				clearCache("travel_strategy_total", 'key');

				//后台消息通知
				updateAdminNotice("travel", "strategy");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['travel']['12']['34']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__travel_strategy` l LEFT JOIN `#@__travel_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__travel_strategy` SET `typeid` = '$typeid', `usertype` = '$usertype', `userid` = '$userid', `title` = '$title', `litpic` = '$litpic', `pics` = '$pics', `note` = '$note', `state` = '$customtravelstrategyCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['travel']['12']['34']); //保存到数据时发生错误，请检查字段内容！
				}

				updateAdminNotice("travel", "strategy");

				// 清除缓存
				clearCache("travel_strategy_detail", $id);
				checkCache("travel_strategy_list", $id);
				clearCache("travel_strategy_total", 'key');


				return $langData['travel'][12][35];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			if($userResult){
				$archives = $dsql->SetQuery("SELECT l.`id`, l.`litpic`, l.`pics`, l.`userid` FROM `#@__travel_strategy` l WHERE l.`id` = '$id' AND l.`userid` = " . $userResult[0]['id']);

			}else{
				$archives = $dsql->SetQuery("SELECT l.`id`, l.`litpic`, l.`pics`, l.`userid` FROM `#@__travel_strategy` l WHERE l.`id` = '$id' AND l.`userid` = " . $userid);
			}
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					//删除图集
					delPicFile($results['litpic'], "delAtlas", "travel");
					delPicFile($results['pics'], "delAtlas", "travel");
					// 清除缓存
					clearCache("travel_strategy_detail", $id);
					checkCache("travel_strategy_list", $id);
					clearCache("travel_strategy_total", 'key');

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__travel_strategy` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['travel'][12][36]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['travel'][12][37]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['travel'][12][38]);//信息不存在，或已经删除！
			}

		}

	}

	/**
	 * 旅游攻略
	 * @return array
	 */
	public function strategyList(){
		global $dsql;
		global $langData;
		global $userLogin;
		$pageinfo = $list = array();
		$page = $pageSize = $where = $where1 =  "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
			}else{
				$search   = $this->param['search'];
				$store    = $this->param['store'];
				$state    = $this->param['state'];
				$typeid   = $this->param['typeid'];
				$u        = $this->param['u'];
				$usertype = $this->param['usertype'];
				$addrid   = $this->param['addrid'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$uid      = $userLogin->getMemberID();
		$userinfo = $userLogin->getMemberInfo();


		if($typeid){
			$where .= " AND s.`typeid` = '$typeid' ";
		}

		$zj_state = "";
		if($u != 1){
			$cityid = getCityId($this->param['cityid']);
			if($cityid){
				$where1 .= " AND `cityid` in (".$cityid.")";
			}

			$where .= " AND s.`state` = 1 ";

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE 1=1 $where1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$storeList = array();
				foreach ($ret as $k => $v) {
					array_push($storeList, $v['id']);
				}
				if($typeid){
					$where .= " OR ( s.`typeid` = '$typeid' AND s.`usertype` = 1 AND s.`userid` in(".join(',',$storeList)."))";
				}else{
					$where .= " OR ( s.`usertype` = 1 AND s.`userid` in(".join(',',$storeList)."))";
				}

			}else{
				//$where .= " AND s.`usertype` = 0 AND s.`userid` = ".$uid;
			}

			$zj_state = " AND z.`state` = 1";

		}else{
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$where .= " AND s.`usertype` = 1 AND s.`userid` = ".$ret[0]['id'];
			}else{
				$where .= " AND s.`usertype` = 0 AND s.`userid` = ".$uid;
			}

			if($state != ""){
				$where .= " AND s.`state` = ".$state;
			}
		}

		if($usertype!=''){
			$where .= " AND s.`usertype` = '$usertype'";
		}

		if(!empty($search)){

			//搜索记录
			if(!empty($search)){
				siteSearchLog("travel", $search);
			}
			$title = explode(" ", $search);
			$w = array();
			foreach ($title as $k => $v) {
				if(!empty($v)){
					$w[] = " s.`title` like '%".$v."%'";
				}
			}
			$where .= " AND (".join(" OR ", $w).")";
		}


		//排序
        switch ($orderby){
            //浏览量
            case 1:
                $orderby_ = " ORDER BY s.`click` DESC, s.`weight` DESC, s.`id` DESC";
                break;
            //发布时间降序
            case 2:
                $orderby_ = " ORDER BY s.`pubdate` DESC, s.`weight` DESC, s.`id` DESC";
				break;
            default:
                $orderby_ = " ORDER BY s.`weight` DESC, s.`id` DESC";
                break;
        }

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT s.`id`, s.`usertype`, s.`userid`, s.`typeid`, s.`title`, s.`pics`, s.`click`, s.`pubdate`, s.`state`, z.`id` as comid FROM `#@__travel_strategy` s LEFT JOIN `#@__travel_store` z ON z.`id` = s.`userid` WHERE (s.`usertype` = 0 OR (s.`usertype` = 1 AND s.`userid` = z.`id`".$zj_state.")) ".$where);//print_R($archives);exit;
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(s.`id`) total FROM `#@__travel_strategy` s LEFT JOIN `#@__travel_store` z ON z.`id` = s.`userid` WHERE (s.`usertype` = 0 OR (s.`usertype` = 1 AND s.`userid` = z.`id`".$zj_state.")) ".$where);
		//总条数
		$totalCount = getCache("travel_strategy_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		//会员列表需要统计信息状态
		if($u == 1 && $uid > -1){
			//待审核
			$totalGray = $dsql->dsqlOper($archives." AND s.`state` = 0", "totalCount");
			//已审核
			$totalAudit = $dsql->dsqlOper($archives." AND s.`state` = 1", "totalCount");
			//拒绝审核
			$totalRefuse = $dsql->dsqlOper($archives." AND s.`state` = 2", "totalCount");

			$pageinfo['gray'] = $totalGray;
			$pageinfo['audit'] = $totalAudit;
			$pageinfo['refuse'] = $totalRefuse;
		}

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$sql = $dsql->SetQuery($archives.$orderby_.$where);
		$results = getCache("travel_strategy_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['usertype']  = $val['usertype'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['typeid']    = $val['typeid'];

				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__travel_strategytype` WHERE `id` = ".$val['typeid']);
				$ret = $dsql->dsqlOper($sql, "results");
				$list[$key]['typename']   = $ret[0]['typename'] ? $ret[0]['typename'] : '';

				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
					$list[$key]['litpic'] = getFilePath($pics[0]);
				}else{
					$list[$key]['litpic']  = '';
				}

				$param = array(
					"service" => "travel",
					"template" => "strategy-detail",
					"id" => $val['id']
				);
				$url = getUrlPath($param);
				$list[$key]['url'] = $url;

				$userid = $val['userid'];
				$nickname = $photo =  "";
				if($val['usertype']==1){
					$archives = $dsql->SetQuery("SELECT m.`nickname`, m.`company`, m.`photo`, zjcom.`title`, zjcom.`pics`,  zjcom.`id` zjcomid  FROM `#@__member` m LEFT JOIN `#@__travel_store` zjcom ON m.`id` = zjcom.`userid` WHERE zjcom.`state` = 1 AND zjcom.`id` = ".$userid);
					$member = $dsql->dsqlOper($archives, "results");
					if($member){
						$picsArr   = !empty($member[0]['pics']) ? explode(',', $member[0]['pics']) : '';
						$photo     = $picsArr[0] ? getFilePath($picsArr[0]) : $member[0]['photo'];
						$title     = $member[0]['title'];
						$zjcomid   = $member[0]['zjcomid'];
					}
					$url = getUrlPath(array("service" => "travel", "template" => "store-detail", "id" => $zjcomid));
					$userArr['url']       = $url;
					$userArr['photo']     = $photo;
					$userArr['title']     = $title;
					$userArr['nickname']  = $member[0]['company'] ? $member[0]['company'] : $member[0]['nickname'];
				}else{
					$archives = $dsql->SetQuery("SELECT `nickname`, `username`, `photo` FROM `#@__member`  WHERE  `id` = ".$userid);
					$member   = $dsql->dsqlOper($archives, "results");
					if($member){
						$nickname  = $member[0]['nickname'] ? $member[0]['nickname'] : $member[0]['username'];
						$photo     = getFilePath($member[0]['photo']);
					}
					$userArr['nickname']  = $nickname;
					$userArr['photo']     = $photo;
				}
				$list[$key]['user'] = $userArr;

				/* if($val['usertype'] == 1){
					$lower = [];
					$param['id']    = $val['comid'];
					$this->param    = $param;
					$store          = $this->storeDetail();
					if(!empty($store)){
						$lower = $store;
					}
					$list[$key]['store'] = $lower;
				}else{

				} */

			}

		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}

	/**
     * 旅游攻略
     * @return array
     */
	public function strategyDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误
		}

		//$where = " AND `state` = 1";

		$archives = $dsql->SetQuery("SELECT s.`id`, s.`usertype`, s.`typeid`, s.`note`, s.`userid`, s.`title`, s.`litpic`, s.`pics`, s.`click`, s.`pubdate`, s.`state`, z.`id` as comid FROM `#@__travel_strategy` s LEFT JOIN `#@__travel_store` z ON z.`id` = s.`userid` WHERE s.`id` = ".$id.$where);
		$results  = getCache("travel_strategy_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['usertype']    = $results[0]['usertype'];
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];
			$storeDetail["note"]        = $results[0]['note'];
			$storeDetail["typeid"]      = $results[0]['typeid'];

			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__travel_strategytype` WHERE `id` = ".$results[0]['typeid']);
			$ret = $dsql->dsqlOper($sql, "results");
			$storeDetail['typename']   = $ret[0]['typename'] ? $ret[0]['typename'] : '';

			//图集
			$imglist = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode(",", $pics);
				foreach ($pics as $key => $value) {
					$imglist[$key]['path'] = getFilePath($value);
					$imglist[$key]['pathSource'] = $value;
				}
			}
			$storeDetail['pics'] = $imglist;

			$litpicArr = array();
			$litpic = $results[0]['litpic'];
			if(!empty($litpic)){
				$litpic = explode(",", $litpic);
				foreach ($litpic as $key => $value) {
					$litpicArr[$key]['path'] = getFilePath($value);
					$litpicArr[$key]['pathSource'] = $value;
				}
			}
			$storeDetail['litpic'] = $litpicArr;

			$userid = $results[0]['userid'];
			$nickname = $photo =  "";
			if($results[0]['usertype']==1){
				$archives = $dsql->SetQuery("SELECT m.`nickname`, m.`company`, m.`photo`, zjcom.`title`, zjcom.`pics`,  zjcom.`id` zjcomid  FROM `#@__member` m LEFT JOIN `#@__travel_store` zjcom ON m.`id` = zjcom.`userid` WHERE zjcom.`state` = 1 AND zjcom.`id` = ".$userid);
				$member = $dsql->dsqlOper($archives, "results");
				if($member){
					$picsArr   = !empty($member[0]['pics']) ? explode(',', $member[0]['pics']) : '';
					$photo     = $picsArr[0] ? getFilePath($picsArr[0]) : $member[0]['photo'];
					$title     = $member[0]['title'];
					$zjcomid   = $member[0]['zjcomid'];
				}
				$userArr['photo']     = $photo;
				$userArr['title']     = $title;
				$userArr['nickname']  = $member[0]['company'] ? $member[0]['company'] : $member[0]['nickname'];
			}else{
				$archives = $dsql->SetQuery("SELECT `nickname`, `username`, `photo` FROM `#@__member`  WHERE  `id` = ".$userid);
				$member   = $dsql->dsqlOper($archives, "results");
				if($member){
					$nickname  = $member[0]['nickname'] ? $member[0]['nickname'] : $member[0]['username'];
					$photo     = getFilePath($member[0]['photo']);
				}
				$userArr['nickname']  = $nickname;
				$userArr['photo']     = $photo;
			}
			$storeDetail['user'] = $userArr;

			// $archives = $dsql->SetQuery("SELECT `id` FROM `#@__travelcommon` WHERE `aid` = ".$results[0]['id']." AND `type` = 1 AND `ischeck` = 1 AND `floor` = 0");
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'travel-strategy' AND `aid` = '$id' AND `pid` = 0");
			$totalCount = $dsql->dsqlOper($archives, "totalCount");
            $storeDetail['common'] = $totalCount;

			if($results[0]['usertype']==1){
				$param = array(
					"service"  => "travel",
					"template" => "store-detail",
					"id"       => $results[0]['company']
				);
				$storeDetail['companyurl'] = getUrlPath($param);
			}

			//验证是否已经收藏
			$params = array(
				"module" => "travel",
				"temp"   => "strategy-detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$storeDetail['collect'] = $collect == "has" ? 1 : 0;

		}
		return $storeDetail;
	}

	/**
	 * 遍历评论子级
	 * @param $fid int 评论ID
	 * @return array
	 */
	function getCommonList(){
        global $dsql;
        global $userLogin;
        global $langData;

        $pageinfo = array();

        $param    = $this->param;
        $fid      = (int)$param['fid'];
        $page     = (int)$this->param['page'];
        $pageSize = (int)$this->param['pageSize'];

        if(empty($fid)) return array("state" => 200, "info" => $langData['travel'][12][23]);//参数错误

        $pageSize = empty($pageSize) ? 99999 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if($fid){
            $where = " AND `floor` = '$fid'";
        }

        $where .= " AND `ischeck` = 1";

        $archives = $dsql->SetQuery("SELECT `id` FROM `#@__travelcommon` WHERE 1 = 1".$where);
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

        $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__travelcommon` WHERE 1 = 1".$where);

        $order = " ORDER BY `id` ASC";
        $atpage = $pageSize*($page-1);
        $where = " LIMIT $atpage, $pageSize";
        $results = $dsql->dsqlOper($archives.$order.$where, "results");
        $list = array();

        if(is_array($results) && !empty($results)){
            foreach ($results as $key => $val) {
                $list[$key]['id']      = $val['id'];
                $list[$key]['userinfo']= $userLogin->getMemberInfo($val['userid']);
                $list[$key]['content'] = $val['content'];
                $list[$key]['dtime']   = $val['dtime'];
                $list[$key]['ftime']   = floor((GetMkTime(time()) - $val['dtime']/86400)%30) > 30 ? $val['dtime'] : FloorTime(GetMkTime(time()) - $val['dtime']);
                $list[$key]['ip']      = $val['ip'];
                $list[$key]['ipaddr']  = $val['ipaddr'];
                $list[$key]['good']    = $val['good'];
                $list[$key]['bad']     = $val['bad'];

                $userArr = explode(",", $val['duser']);
                $list[$key]['already'] = in_array($userLogin->getMemberID(), $userArr) ? 1 : 0;

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
     * 评论列表
     * @return array
     */
	public function common(){
		global $dsql;
		global $userLogin;
		global $langData;
		$pageinfo = $list = array();
		$newsid = $orderby = $page = $pageSize = $where = "";

		if(!is_array($this->param)){
			return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！
		}else{
			$newsid    = $this->param['newsid'];
			$orderby   = $this->param['orderby'];
			$typeid    = $this->param['typeid'];
			$page      = $this->param['page'];
			$pageSize  = $this->param['pageSize'];
		}
		if($typeid!=''){
			$where = " AND `type` = '$typeid'";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$oby = " ORDER BY `id` DESC";
		if($orderby == "hot"){
			$oby = " ORDER BY `good` DESC, `id` DESC";
		}

		$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__travelcommon` WHERE `aid` = ".$newsid." $where AND `ischeck` = 1 AND `floor` = 0".$oby);//print_R($archives);exit;
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

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
	 * 顶评论
	 * @param $id int 评论ID
	 * @param string
	 **/
	public function dingCommon(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id   = $this->param['id'];
		$type = $type ? $type : 0;
		if(empty($id)) return $langData['travel'][12][67];
		$memberID = $userLogin->getMemberID();
		if($memberID == -1 || empty($memberID)) return $langData['siteConfig'][20][262];//登录超时，请重新登录！

		$archives = $dsql->SetQuery("SELECT `duser` FROM `#@__travelcommon` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){

			$duser = $results[0]['duser'];

			//如果此会员已经顶过则return
			$userArr = explode(",", $duser);
			if(in_array($userLogin->getMemberID(), $userArr)) return $langData['travel'][12][68];//已顶过！

			//附加会员ID
			if(empty($duser)){
				$nuser = $userLogin->getMemberID();
			}else{
				$nuser = $duser . "," . $userLogin->getMemberID();
			}

			$archives = $dsql->SetQuery("UPDATE `#@__travelcommon` SET `good` = `good` + 1 WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");

			$archives = $dsql->SetQuery("UPDATE `#@__travelcommon` SET `duser` = '$nuser' WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");
			return $results;

		}else{
			return $langData['travel'][12][69];//评论不存在或已删除！
		}
	}

	/**
     * 发表评论
     * @return array
     */
	public function sendCommon(){
		global $dsql;
		global $userLogin;
		global $langData;
		$param = $this->param;

		$aid     = $param['aid'];
		$id      = $param['id'];
		$type    = (int)$param['type'];
		$content = addslashes($param['content']);

		if(empty($aid) || empty($content)){
			return array("state" => 200, "info" => $langData['travel'][12][70]);//必填项不得为空！
		}

		$content = filterSensitiveWords(cn_substrR($content,250));

		include HUONIAOINC."/config/travel.inc.php";
		$ischeck = (int)$customCommentCheck;

        $userid = $userLogin->getMemberID();

		$archives = $dsql->SetQuery("INSERT INTO `#@__travelcommon` (`aid`, `floor`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `ischeck`, `duser`, `type`) VALUES ('$aid', '$id', '".$userid."', '$content', ".GetMkTime(time()).", '".GetIP()."', '".getIpAddr(GetIP())."', 0, 0, '$ischeck', '', '$type')");
		$lid  = $dsql->dsqlOper($archives, "lastid");
		if($lid){

			$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__travelcommon` WHERE `id` = ".$lid);
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
			return array("state" => 200, "info" => $langData['travel'][12][71]);//评论失败！
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

        $sql = $dsql->SetQuery("SELECT * FROM `#@__travelcommon` WHERE `id` = $id AND `isCheck` = 1 ");//print_R($sql);exit;
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
                        $sql = $dsql->SetQuery("SELECT `content`, `userid` FROM `#@__travelcommon` WHERE `id` = '$value' AND `isCheck` = 1 ");
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
	 * 下单
	 * @return array
	 */
	public function deal(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param = $this->param;
		$type      = (int)$param['type'];    //类型 1：景点门票；2：周边游；3：酒店入驻；4：签证
		$proid     = (int)$param['proid'];
		$procount  = (int)$param['procount'];
		$people    = $param['people'];
		$contact   = $param['contact'];
		$idcard    = $param['idcard'];//身份证
		$walktime  = GetMkTime($param['walktime']);//出行时间 入驻时间
		$departuretime  = GetMkTime($param['departuretime']);//离店时间
		$email     = $param['email'];
		$applicantinformation     = $param['applicantinformation'];//申请人信息
		$addressid = $param['addressid'];
		$note      = filterSensitiveWords(addslashes($param['note']));

		if(empty($proid))    return array("state" => 200, "info" => $langData['travel'][12][23]);  //格式错误
		if(empty($procount)) return array("state" => 200, "info" => $langData['travel'][13][12]);   //数量不能为空
		if($type == 4){
			if(empty($addressid)) return array("state" => 200, "info" => $langData['travel'][13][78]);  //请选择回寄地址

			//收货地址信息
			global $data;
			$data = "";
			$archives = $dsql->SetQuery("SELECT * FROM `#@__member_address` WHERE `uid` = $userid AND `id` = $addressid");
			$userAddr = $dsql->dsqlOper($archives, "results");
			if(!$userAddr) return array("state" => 200, "info" => $langData['siteConfig'][21][105]);  //会员地址库信息不存在或已删除
			$addrArr = getParentArr("site_area", $userAddr[0]['addrid']);
			$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
			$addr    = join(" ", $addrArr);

			$backaddress = $addr . $userAddr[0]['address'];
			$backpeople  = $userAddr[0]['person'];
			$mobile      = $userAddr[0]['mobile'];
			$tel         = $userAddr[0]['tel'];
			$backcontact = !empty($mobile) ? $mobile . (!empty($tel) ? " / ".$tel : "") : $tel;
		}


		if($type == 1 || $type == 2){//景点门票 周边游
			$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
			$ret = $dsql->dsqlOper($sql, 'results');
			if(is_array($ret)){

				$this->param = $ret[0]['ticketid'];
				if($type == 1){
					$detail = $this->ticketDetail();
				}elseif($type == 2){
					$detail = $this->agencyDetail();
				}

				$store = $detail['store']['id'];

				if($detail['store']['userid'] == $userid) return array("state" => 200, "info" => $langData['travel'][13][14]); //企业会员不可以购买自己的门票！
				if(!is_array($detail)) return array("state" => 200, "info" => $langData['travel'][13][13]);//门票信息不存在

				$price = $ret[0]['price'];
				$specialtime = $ret[0]['specialtime'] ? unserialize($ret[0]['specialtime']) : array();

				$priceArr = array();
				if(!empty($specialtime)){
					foreach($specialtime as $key=>$val){
						if($walktime >= GetMkTime($val['stime']) && $walktime < GetMkTime($val['etime'])){
							$priceArr[] = $val['price'];
						}
					}
				}

				if(count($priceArr) > 0){
					$price = array_pop($priceArr);
				}else{
					$price = $price;
				}

			}else{
				return array("state" => 200, "info" => $langData['travel'][13][13]);//门票信息不存在
			}
		}elseif($type == 3){//酒店入驻
			$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
			$ret = $dsql->dsqlOper($sql, 'results');
			if(is_array($ret)){

				$this->param = $ret[0]['hotelid'];
				$detail = $this->hotelDetail();

				$store = $detail['store']['id'];

				if($detail['store']['userid'] == $userid) return array("state" => 200, "info" => $langData['travel'][13][14]); //企业会员不可以购买自己的门票！
				if(!is_array($detail)) return array("state" => 200, "info" => $langData['travel'][13][71]);//房间信息不存在

				$price = $ret[0]['price'];
				$specialtime = $ret[0]['specialtime'] ? unserialize($ret[0]['specialtime']) : array();

				$priceArr = array();
				if(!empty($specialtime)){
					foreach($specialtime as $key=>$val){
						if($walktime >= GetMkTime($val['stime']) && $walktime < GetMkTime($val['etime'])){
							$priceArr[] = $val['price'];
						}
					}
				}

				if(count($priceArr) > 0){
					$price = array_pop($priceArr);
				}else{
					$price = $price;
				}

			}else{
				return array("state" => 200, "info" => $langData['travel'][13][71]);//房间信息不存在
			}
		}elseif($type == 4){//签证
			$this->param = $proid;
			$detail = $this->visaDetail();

			$store = $detail['store']['id'];

			if($detail['store']['userid'] == $userid) return array("state" => 200, "info" => $langData['travel'][13][79]); //企业会员不可以购买自己的签证信息！
			if(!is_array($detail)) return array("state" => 200, "info" => $langData['travel'][13][80]);//签证信息不存在

			$price = $detail['price'];
		}

		$ordernum = create_ordernum();

		$pubdate   = GetMkTime(time());
		$nopaydate = $pubdate + 1800;

		$archives = $dsql->SetQuery("INSERT INTO `#@__travel_order` (`ordernum`, `store`, `proid`, `userid`, `orderstate`, `orderdate`, `procount`, `people`, `contact`, `orderprice`, `type`, `tab`, `idcard`, `walktime`, `nopaydate`, `departuretime`, `applicantinformation`, `email`, `note`, `backpeople`, `backcontact`, `backaddress`) VALUES ('$ordernum', '$store', '$proid', '$userid', '0', '$pubdate', '$procount', '$people', '$contact', '$price', '$type', 'travel', '$idcard', '$walktime', '$nopaydate', '$departuretime', '$applicantinformation', '$email', '$note', '$backpeople', '$backcontact', '$backaddress')");

		$return = $dsql->dsqlOper($archives, "update");
		if($return == "ok"){
			$url[] = $ordernum;
		}else{
			return array("state" => 200, "info" => $langData['homemaking'][9][99]);//下单失败
		}

		$RenrenCrypt = new RenrenCrypt();
		$ids = base64_encode($RenrenCrypt->php_encrypt(join(",", $url)));

		/* $param = array(
			"service"     => "travel",
			"template"    => "pay",
			"param"       => "ordernum=".$ids
		); */
		$param = array(
			"service"     => "travel",
			"template"    => "comfirm",
			"param"       => "ordernum=".$ids
		);
		return getUrlPath($param);
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

		//订单状态验证
		$payCheck = $this->payCheck();
		if($payCheck != "ok") return array("state" => 200, "info" => $payCheck['info']);

		$ordernum   = $param['ordernum'];    //订单号
		$usePinput  = $param['usePinput'];   //是否使用积分
		$point      = $param['point'];       //使用的积分
		$useBalance = $param['useBalance'];  //是否使用余额
		$balance    = $param['balance'];     //使用的余额
		$paypwd     = $param['paypwd'];      //支付密码

		if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请登录后重试！
		if(empty($ordernum)) return array("state" => 200, "info" => $langData['travel'][13][15]);//提交失败，订单号不能为空！
		if(!empty($balance) && empty($paypwd)) return array("state" => 200, "info" => $langData['travel'][13][16]);//请输入支付密码！

		$totalPrice = 0;
		$ordernumArr = explode(",", $ordernum);
		foreach ($ordernumArr as $key => $value) {
			//查询订单信息
			$archives = $dsql->SetQuery("SELECT `procount`, `orderprice` FROM `#@__travel_order` WHERE `ordernum` = '$value'");
			$results  = $dsql->dsqlOper($archives, "results");
			$res = $results[0];
			$procount   = $res['procount'];
			$orderprice = $res['orderprice'];

			$totalPrice += $orderprice * $procount;
		}

		//查询会员信息
		$userinfo  = $userLogin->getMemberInfo();
		$usermoney = $userinfo['money'];
		$userpoint = $userinfo['point'];

		$tit      = array();
		$useTotal = 0;

		//判断是否使用积分，并且验证剩余积分
		if($usePinput == 1 && !empty($point)){
			if($userpoint < $point) return array("state" => 200, "info" => $langData['travel'][13][17].$cfg_pointName.$langData['travel'][13][18]);//您的可用".$cfg_pointName."不足，支付失败！
			$useTotal += $point / $cfg_pointRatio;
			$tit[] = $cfg_pointName;
		}

		//判断是否使用余额，并且验证余额和支付密码
		if($useBalance == 1 && !empty($balance) && !empty($paypwd)){
			//验证支付密码
			$archives = $dsql->SetQuery("SELECT `id`, `paypwd` FROM `#@__member` WHERE `id` = '$userid'");
			$results  = $dsql->dsqlOper($archives, "results");
			$res = $results[0];
			$hash = $userLogin->_getSaltedHash($paypwd, $res['paypwd']);
			if($res['paypwd'] != $hash) return array("state" => 200, "info" => $langData['travel'][13][19]);//支付密码输入错误，请重试！
			//验证余额
			if($usermoney < $balance) return array("state" => 200, "info" => $langData['travel'][13][20]);//您的余额不足，支付失败！
			$useTotal += $balance;
			$tit[] = $langData['travel'][13][21];//余额
		}

		if($useTotal > $totalPrice) return array("state" => 200, "info" => $langData['travel'][13][22].join($langData['travel'][13][23], $tit).$langData['travel'][13][24].join($langData['travel'][13][23], $tit));//"您使用的".join("和", $tit)."超出订单总费用，请重新输入要使用的".join("和", $tit)

		//返回需要支付的费用
		return sprintf("%.2f", $totalPrice - $useTotal);

	}

	/**
	 * 支付前验证订单内容
	 * @return array
	 */
	public function payCheck(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;
		$ordernum = $param['ordernum'];

		if(empty($ordernum)) return array("state" => 200, "info" => $langData['travel'][13][25]);//订单号传递失败！

		$userid = $userLogin->getMemberID();
		$ordernumArr = explode(",", $ordernum);
		foreach ($ordernumArr as $key => $value) {

			//获取订单内容
			$archives = $dsql->SetQuery("SELECT `proid`, `procount`, `orderprice`, `orderstate`, `type` FROM `#@__travel_order` WHERE `ordernum` = '$value' AND `userid` = $userid");
			$orderDetail  = $dsql->dsqlOper($archives, "results");
			if($orderDetail){
				$proid      = $orderDetail[0]['proid'];
				$procount   = $orderDetail[0]['procount'];
				$orderprice = $orderDetail[0]['orderprice'];
				$orderstate = $orderDetail[0]['orderstate'];
				$type       = $orderDetail[0]['type'];

				//验证订单状态
				if($orderstate != 0){
					//订单中包含状态异常的订单，请确认后重试！ 订单状态异常，请确认后重试！
					$info = count($ordernumArr) > 1 ? $langData['travel'][13][26] : $langData['travel'][13][27];
					return array("state" => 200, "info" => $info);
				}

				$this->param = $proid;
				if($type == 1 || $type == 2){//门票
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
					$ret = $dsql->dsqlOper($sql, 'results');
					if(!empty($ret)){
						$this->param = $ret[0]['ticketid'];
						if($type == 1){
							$proDetail = $this->ticketDetail();
						}elseif($type == 2){
							$proDetail = $this->agencyDetail();
						}
					}
				}elseif($type == 3){//酒店
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
					$ret = $dsql->dsqlOper($sql, 'results');
					if(!empty($ret)){
						$this->param = $ret[0]['hotelid'];
						$proDetail = $this->hotelDetail();
					}
				}elseif($type == 4){//签证
					$this->param = $proid;
					$proDetail = $this->visaDetail();
				}

				//验证是否为自己的店铺
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE `userid` = $userid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					if($proDetail['company'] == $ret[0]['id']){
						return array("state" => 200, "info" => $langData['travel'][13][28]);//企业会员不得购买自己公司的！
					}
				}

				//获取商品详细信息
				if(empty($proDetail)){
					//订单中包含不存在或已下架的商品，请确认后重试！ 提交失败，您要购买的商品不存在或已下架！
					$info = count($ordernumArr) > 1 ? $langData['travel'][13][29] : $langData['travel'][13][30];
					return array("state" => 200, "info" => $info);
				}
			//订单不存在
			}else{
				//订单中包含不存在的订单，请确认后重试！ 订单不存在或已删除，请确认后重试！
				$info = count($ordernumArr) > 1 ? $langData['travel'][13][31] : $langData['travel'][13][32];
				return array("state" => 200, "info" => $info);
			}
		}
		return "ok";
	}

	/**
	 * 支付
	 * @return array
	 */
	public function pay(){
		global $dsql;
		global $cfg_basehost;
		global $cfg_pointRatio;
		global $langData;

		$param =  $this->param;

		//验证需要支付的费用
		$payTotalAmount = $this->checkPayAmount();
		//重置表单参数
		$this->param = $param;

		if($this->payCheck() != "ok" || is_array($payTotalAmount)){
			$param = array(
				"service"     => "member",
				"type"        => "user",
				"template"    => "order",
				"module"      => "travel"
			);
			$url = getUrlPath($param);
			header("location:".$url);
			die;
		}

		$ordernum   = $param['ordernum'];
		$paytype    = $param['paytype'];
		$usePinput  = $param['usePinput'];
		$point      = (float)$param['point'];
		$useBalance = $param['useBalance'];
		$balance    = (float)$param['balance'];
		$ordernumArr = explode(",", $ordernum);

		//如果有使用积分或余额则更新订单内容的价格策略
		if(($usePinput && !empty($point)) || ($useBalance && !empty($balance))){

			$pointMoney = $usePinput ? $point / $cfg_pointRatio : 0;
			$balanceMoney = $balance;

			foreach ($ordernumArr as $key => $value) {

				//查询订单信息
				$archives = $dsql->SetQuery("SELECT `procount`, `orderprice` FROM `#@__travel_order` WHERE `ordernum` = '$value'");
				$results  = $dsql->dsqlOper($archives, "results");
				$res = $results[0];
				$procount   = $res['procount'];   //数量
				$orderprice = $res['orderprice']; //单价
				$oprice = $procount * $orderprice;  //单个订单总价 = 数量 * 单价

				$usePointMoney = 0;
				$useBalanceMoney = 0;

				//先判断积分是否足够支付总价
				//如果足够支付：
				//1.把还需要支付的总价重置为0
				//2.积分总额减去用掉的
				//3.记录已经使用的积分
				if($oprice < $pointMoney){
					$pointMoney -= $oprice;
					$usePointMoney = $oprice;
					$oprice = 0;
				//积分不够支付再判断余额是否足够
				//如果积分不足以支付总价：
				//1.总价减去积分抵扣掉的部部分
				//2.积分总额设置为0
				//3.记录已经使用的积分
				}else{
					$oprice -= $pointMoney;
					$usePointMoney = $pointMoney;
					$pointMoney = 0;
					//验证余额是否足够支付剩余部分的总价
					//如果足够支付：
					//1.把还需要支付的总价重置为0
					//2.余额减去用掉的部分
					//3.记录已经使用的余额
					if($oprice < $balanceMoney){
						$balanceMoney -= $oprice;
						$useBalanceMoney = $oprice;
						$oprice = 0;
					//余额不够支付的情况
					//1.总价减去余额付过的部分
					//2.余额设置为0
					//3.记录已经使用的余额
					}else{
						$oprice -= $balanceMoney;
						$useBalanceMoney = $balanceMoney;
						$balanceMoney = 0;
					}
				}

				$pointMoney_ = $usePointMoney * $cfg_pointRatio;
				$archives = $dsql->SetQuery("UPDATE `#@__travel_order` SET `point` = '$pointMoney_', `balance` = '$useBalanceMoney', `payprice` = '$oprice' WHERE `ordernum` = '$value'");
				$dsql->dsqlOper($archives, "update");
			}

		//如果没有使用积分或余额，重置积分&余额等价格信息
		}else{
			foreach ($ordernumArr as $key => $value) {
				//查询订单信息
				$archives = $dsql->SetQuery("SELECT `procount`, `orderprice` FROM `#@__travel_order` WHERE `ordernum` = '$value'");
				$results  = $dsql->dsqlOper($archives, "results");
				$res = $results[0];
				$procount   = $res['procount'];   //数量
				$orderprice = $res['orderprice']; //单价
				$oprice = $procount * $orderprice;  //单个订单总价 = 数量 * 单价

				$archives = $dsql->SetQuery("UPDATE `#@__travel_order` SET `point` = '0', `balance` = '0', `payprice` = '$oprice' WHERE `ordernum` = '$value'");
				$dsql->dsqlOper($archives, "update");
			}
		}

		//1.如果需要支付的金额小于等于0，表示会员使用积分或余额已经付清了，不需要另外去支付
		//2.这种情况直接更新订单状态，并跳转至支付成功页即可
		//3.对会员的积分和余额进行扣除操作
		if($payTotalAmount <= 0){

			$date = GetMkTime(time());
			$paytype = array();

			//扣除会员账户积分和余额
			foreach ($ordernumArr as $key => $value) {

				//查询订单信息
				$archives = $dsql->SetQuery("SELECT `id`, `userid`, `point`, `balance`, `type` FROM `#@__travel_order` WHERE `ordernum` = '$value'");
				$results  = $dsql->dsqlOper($archives, "results");
				$res = $results[0];
				$userid  = $res['userid'];   //购买用户ID
				$orderid  = $res['id'];   //订单ID
				$upoint   = $res['point'];    //使用的积分
				$ubalance = $res['balance'];  //使用的余额
				$type     = $res['type'];

				//扣除会员积分
				if(!empty($upoint) && $upoint > 0){
					$paytype[] = "point";
				}

				//扣除会员余额
				if(!empty($ubalance) && $ubalance > 0){
					$paytype[] = "money";
				}

			}


			//增加支付日志
			$paylognum = create_ordernum();
			$archives = $dsql->SetQuery("INSERT INTO `#@__pay_log` (`ordertype`, `ordernum`, `uid`, `body`, `amount`, `paytype`, `state`) VALUES ('travel', '$paylognum', '$userid', '$ordernum', '0', '".join(",", $paytype)."', '1')");
			$dsql->dsqlOper($archives, "update");

			//执行支付成功的操作
			$this->param = array(
				"paytype" => join(",", $paytype),
				"ordernum" => $ordernum
			);
			$this->paySuccess();

			//跳转至支付成功页面
			/* $param = array(
				"service"     => "travel",
				"template"    => "payreturn",
				"ordernum"    => $paylognum
			); */
			if($type==1 || $type==2){
				$param = array(
					"service"     => "travel",
					"template"    => "travel-ticketstate",
					"ordernum"    => $paylognum
				);
			}elseif($type==3){
				$param = array(
					"service"     => "travel",
					"template"    => "travel-hotelstate",
					"ordernum"    => $paylognum
				);
			}elseif($type==4){
				$param = array(
					"service"     => "travel",
					"template"    => "payreturn",
					"ordernum"    => $paylognum
				);
			}
			$url = getUrlPath($param);
			header("location:".$url);

		}else{
			//跳转至第三方支付页面
			createPayForm("travel", $ordernum, $payTotalAmount, $paytype, $langData['travel'][13][33]);//旅游订单
		}

	}

	/**
	 * 支付成功
	 * 此处进行支付成功后的操作，例如发送短信等服务
	 *
	 */
	public function paySuccess(){
		$param = $this->param;
		if(!empty($param)){
			global $dsql;
			global $langData;

			$paytype  = $param['paytype'];
			$paramArr = explode(",", $param['ordernum']);
			$date = GetMkTime(time());

			foreach ($paramArr as $key => $value) {

				$archives = $dsql->SetQuery("SELECT `id`, `userid`, `point`, `balance`, `payprice`, `paydate`, `proid`, `procount`, `people`, `contact`, `idcard`, `orderprice`, `type`, `orderstate`, `walktime`, `departuretime` FROM `#@__travel_order` WHERE `ordernum` = '$value'");
				$res  = $dsql->dsqlOper($archives, "results");
				if($res){
					$type           = $res[0]['type'];
					$proid          = $res[0]['proid'];
					$orderid        = $res[0]['id'];
					$walktime       = $res[0]['walktime'];
					$procount       = $res[0]['procount'];
					$upoint         = $res[0]['point'];
					$ubalance       = $res[0]['balance'];
					$payprice       = $res[0]['payprice'];
					$paydate        = $res[0]['paydate'];
					$departuretime  = $res[0]['departuretime'];
					$userid         = $res[0]['userid'];//会员ID

					if($type==1 || $type==2){//景点门票 周边游
						$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
						$ret = $dsql->dsqlOper($sql, 'results');
						if(!empty($ret)){
							$title          = $ret[0]['title'];
							$this->param = $ret[0]['ticketid'];
							if($type==1){
								$proDetail = $this->ticketDetail();
							}elseif($type==2){
								$proDetail = $this->agencyDetail();
							}
							$uid            = $proDetail['store']['userid'];//商家ID
						}
					}elseif($type==3){
						$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
						$ret = $dsql->dsqlOper($sql, 'results');
						if(!empty($ret)){
							$title          = $ret[0]['title'];
							$this->param = $ret[0]['hotelid'];
							$proDetail = $this->hotelDetail();
							$uid            = $proDetail['store']['userid'];//商家ID
						}
					}elseif($type==4){
						$this->param = $proid;
						$proDetail   = $this->visaDetail();
						$uid         = $proDetail['store']['userid'];//商家ID
						$title       = $proDetail['title'];
					}

					//判断是否已经更新过状态，如果已经更新过则不进行下面的操作
					if($paydate == 0){
						//更新订单状态
						$archives = $dsql->SetQuery("UPDATE `#@__travel_order` SET `orderstate` = 1, `paydate` = '$date', `paytype` = '$paytype' WHERE `ordernum` = '$value'");
						$dsql->dsqlOper($archives, "update");

						//更新已购买数量
						if($type==1 || $type==2){//景点门票 周边游
							$sql = $dsql->SetQuery("UPDATE `#@__travel_ticketinfo` SET `sale` = `sale` + $procount WHERE `id` = '$proid'");
							$dsql->dsqlOper($sql, "update");
						}elseif($type==3){
							$sql = $dsql->SetQuery("UPDATE `#@__travel_hotelroom` SET `valid` = 1, `sale` = `sale` + $procount WHERE `id` = '$proid'");
							$dsql->dsqlOper($sql, "update");
						}elseif($type==4){
							$sql = $dsql->SetQuery("UPDATE `#@__travel_visa` SET `sale` = `sale` + $procount WHERE `id` = '$proid'");
							$dsql->dsqlOper($sql, "update");
						}

						$totalPrice = $payprice;

						//扣除会员积分
						if(!empty($upoint) && $upoint > 0){
							$archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` - '$upoint' WHERE `id` = '$userid'");
							$dsql->dsqlOper($archives, "update");

							//保存操作日志
							$info = $langData['travel'][13][34] . $value;
							$archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$upoint', '$info', '$date')");//支付旅游订单
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
						if($totalPrice>0){
							$info_ = $langData['travel'][13][34] . $value;
							$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$totalPrice', '$info_', '$date')");//支付旅游订单
							$dsql->dsqlOper($archives, "update");
						}

						//生成服务码
						// if($type == 1 || $type == 2 || $type == 3 || $type == 4){
							$sqlQuan = array();
							$carddate = GetMkTime(time());
							if($type == 3){
								$expireddate = $departuretime;
							}else{
								$expireddate = $walktime;
							}
							// for ($i = 0; $i < $procount; $i++) {
								$cardnum = genSecret(12, 1);
								$sqlQuan[0] = "('$orderid', '$cardnum', '$carddate', 0, '$expireddate')";
							// }

							$sql = $dsql->SetQuery("INSERT INTO `#@__travelquan` (`orderid`, `cardnum`, `carddate`, `usedate`, `expireddate`) VALUES ".join(",", $sqlQuan));
							$dsql->dsqlOper($sql, "update");
						// }

						//支付成功，会员消息通知
						$paramUser = array(
							"service"  => "member",
							"type"     => "user",
							"template" => "orderdetail",
							"action"   => "travel",
							"id"       => $orderid
						);

						$paramBusi = array(
							"service"  => "member",
							"template" => "orderdetail",
							"action"   => "travel",
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
							"title" => $title,
							"order" => $value,
							"amount" => $totalPrice,
							"fields" => array(
								'keyword1' => '商品信息',
								'keyword2' => '订单金额',
								'keyword3' => '订单状态'
							)
						);

						updateMemberNotice($userid, "会员-订单支付成功", $paramUser, $config);

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
							"title" => $title,
							"order" => $value,
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
					}

				}

			}
		}
	}

	/**
     * 旅游订单列表
     * @return array
     */
	public function orderList(){
		global $dsql;
		global $langData;
		$pageinfo = $list = array();
		$store = $state = $userid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！
			}else{
				$store    = $this->param['store'];
				$type     = (int)$this->param['type'];
				$state    = $this->param['state'];
				$userid   = $this->param['userid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if(empty($userid)){
			global $userLogin;
			$userid = $userLogin->getMemberID();
		}
		if(empty($userid)) return array("state" => 200, "info" => $langData['travel'][13][35]);//会员ID不得为空！

		//个人会员订单 派单人员
		if(empty($store)){
			$where = " o.`userid` = '$userid' ";
			if($type){
				$where .= " AND o.`type` = '$type' ";
			}
			$archives = $dsql->SetQuery("SELECT o.`id`, o.`ordernum`, o.`store`, o.`proid`, o.`userid`, o.`procount`, o.`orderprice`, o.`orderstate`, o.`orderdate`, o.`paydate`, o.`ret-state`, o.`type` FROM `#@__travel_order` o  LEFT JOIN `#@__travel_store` s ON o.`store` = s.`id` LEFT JOIN `#@__member` m ON m.`id` = s.`userid` WHERE " . $where);
		//商家订单列表
		}else{
			$where = " s.`userid` = '$userid' AND o.`orderstate` != 0 ";
			if($type){
				$where .= " AND o.`type` = '$type' ";
			}
			$userinfo = $userLogin->getMemberInfo();
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
				return array("state" => 200, "info" => $langData['travel'][12][27]);//商家权限验证失败！
			}
			$archives = $dsql->SetQuery("SELECT o.`id`, o.`ordernum`, o.`store`, o.`proid`, o.`userid`, o.`procount`, o.`orderprice`, o.`orderstate`, o.`orderdate`, o.`paydate`, o.`ret-state`, o.`type` FROM `#@__travel_order` o LEFT JOIN `#@__travel_store` s ON o.`store` = s.`id` LEFT JOIN `#@__member` m ON m.`id` = s.`userid` WHERE " . $where);
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);
		//未付款
		$state0 = $dsql->dsqlOper($archives." AND `orderstate` = 0", "totalCount");
		//已付款，待确认
		$state1 = $dsql->dsqlOper($archives." AND `orderstate` = 1", "totalCount");
		//待服务
		$state2 = $dsql->dsqlOper($archives." AND `orderstate` = 2", "totalCount");

		$state20 = $dsql->dsqlOper($archives." AND (`orderstate` = 2 or `orderstate` = 4)", "totalCount");
		//服务无效
		$state3 = $dsql->dsqlOper($archives." AND `orderstate` = 3", "totalCount");
		//已确认，待服务
		$state4 = $dsql->dsqlOper($archives." AND `orderstate` = 4", "totalCount");
		//已服务，待客户验收
		$state5 = $dsql->dsqlOper($archives." AND `orderstate` = 5", "totalCount");
		//服务完成
		$state6 = $dsql->dsqlOper($archives." AND `orderstate` = 6", "totalCount");
		//已取消
		$state7 = $dsql->dsqlOper($archives." AND `orderstate` = 7", "totalCount");
		//退款中
		$state8 = $dsql->dsqlOper($archives." AND `orderstate` = 8 and `ret-state` = 1 ", "totalCount");
		//已退款
		$state9 = $dsql->dsqlOper($archives." AND `orderstate` = 9", "totalCount");

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount,
			"state0"    => $state0,
			"state1"    => $state1,
			"state2"    => $state2,
			"state3"    => $state3,
			"state4"    => $state4,
			"state5"    => $state5,
			"state6"    => $state6,
			"state7"    => $state7,
			"state8"    => $state8,
			"state9"    => $state9,
			"state20"   => $state20
		);

		if($totalCount == 0) return array("pageInfo" => $pageinfo, "list" => array());

		$where = "";
		if($state != "" && $state != 20){
			$where = " AND `orderstate` = " . $state;
		}

		//退款
		if($state == 8){
			$where = " AND `ret-state` = 1";
		}

		if($state == 20){
			$where = " AND (`orderstate` = 2 or `orderstate` = 4)";
		}

		//待评价
		if($state == 5){
			//$where = " AND `orderstate` = 3 AND `common` = 0";
		}

		//已发货
		if($state == 6){
			//$where = " AND `orderstate` = 6 AND `exp-date` != 0";
		}

		$atpage = $pageSize*($page-1);
		$where .= " ORDER BY `id` DESC LIMIT $atpage, $pageSize";

		$results = $dsql->dsqlOper($archives.$where, "results");
		if($results){

			$param = array(
				"service"     => "travel",
				"template"    => "pay",
				"param"       => "ordernum=%id%"
			);
			$payurlParam = getUrlPath($param);

			$param = array(
				"service"  => "member",
				"type"     => "user",
				"template" => "orderdetail",
				"module"   => "travel",
				"id"       => "%id%",
				"param"    => "rates=1"
			);
			$commonUrlParam = getUrlPath($param);

			foreach($results as $key => $val){
				$list[$key]['id']          = $val['id'];
				$list[$key]['ordernum']    = $val['ordernum'];
				$list[$key]['proid']       = $val['proid'];
				$list[$key]['procount']    = $val['procount'];
				$list[$key]['store']       = $val['store'];

				//计算订单价格
				$totalPrice = $val['orderprice'] * $val['procount'];
				$list[$key]['orderprice']  = $totalPrice;
				$list[$key]["orderstate"]  = $val['orderstate'];
				$list[$key]['orderdate']   = $val['orderdate'];
				$list[$key]['paydate']     = $val['paydate'];
				$list[$key]['retState']    = $val['ret-state'];

				//买家信息
				$uid = $val['userid'];
				$list[$key]['member']     = getMemberDetail($uid);

				//服务详细
				if($val['type'] == 1 || $val['type'] == 2){//景点门票 周边游
					// $this->param = $val['proid'];
					$proid = $val['proid'];
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
					$ret = $dsql->dsqlOper($sql, 'results');
					if(!empty($ret)){
						$list[$key]['title']       = $ret[0]['title'];
						$this->param = $ret[0]['ticketid'];
						if($val['type'] == 1){
							$proDetail = $this->ticketDetail();
						}elseif($val['type'] == 2){
							$proDetail = $this->agencyDetail();
						}
					}
					if($val['type'] == 1){
						$param = array(
							"service"     => "travel",
							"template"    => "ticket-detail",
							"id"          => $proDetail['id']
						);
					}elseif($val['type'] == 2){
						$param = array(
							"service"     => "travel",
							"template"    => "agency-detail",
							"id"          => $proDetail['id']
						);
					}

				}elseif($val['type'] == 3){//酒店入驻
					$proid = $val['proid'];
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
					$ret = $dsql->dsqlOper($sql, 'results');
					if(!empty($ret)){
						$list[$key]['title']       = $ret[0]['title'];
						$this->param = $ret[0]['hotelid'];
						$proDetail = $this->hotelDetail();
					}

					$param = array(
						"service"     => "travel",
						"template"    => "hotel-detail",
						"id"          => $proDetail['id']
					);

				}elseif($val['type'] == 4){//签证

					$this->param = $val['proid'];
					$proDetail = $this->visaDetail();
					$list[$key]['title']  = $proDetail['title'];

					$param = array(
						"service"     => "travel",
						"template"    => "visa-detail",
						"id"          => $proDetail['id']
					);

				}

				$list[$key]['product']['title']  = $ret[0]['title'] ? $ret[0]['title'] : $proDetail['title'];
				$list[$key]['product']['litpic'] = $proDetail['pics'][0]['path'];
				$list[$key]['product']['url'] = getUrlPath($param);

				//未付款的提供付款链接
				if($val['orderstate'] == 0){
					$RenrenCrypt = new RenrenCrypt();
					$encodeid = base64_encode($RenrenCrypt->php_encrypt($val["ordernum"]));
					$list[$key]["payurl"] = str_replace("%id%", $encodeid, $payurlParam);
				}

				//服务码
				$list[$key]["type"] = $val["type"];
				$cardnum = array();
				if(($val["type"] == 1 || $val["type"] == 2 || $val["type"] == 3 || $val["type"] == 4) && $val['orderstate'] != 0){
					$cardSql = $dsql->SetQuery("SELECT `cardnum`, `usedate`, `expireddate` FROM `#@__travelquan` WHERE `orderid` = ". $val["id"]);
					$cardResult = $dsql->dsqlOper($cardSql, "results");
					if($cardResult){
						$list[$key]["cardnum"] = $cardResult[0]['cardnum'];
						foreach($cardResult as $k => $row){
							$cardnum[$k]['cardnum']     = join(" ", str_split($row['cardnum'], 4));
							$cardnum[$k]['usedate']     = $row['usedate'];
							$cardnum[$k]['expireddate'] = $row['expireddate'];
							$cardnumList[$k] = $row['cardnum'];
						}
					}
				}

				if($cardnum){
					$param = array(
						"service" => "member",
						"template" => "verify-travel",
						"param" => "cardnum=".join(",", $cardnumList)
					);
					$list[$key]["cardnumUrl"] = getUrlPath($param);
				}

			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
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

		if(!is_numeric($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__travel_order` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['userid'] == $uid){
				//未付款 已取消 交易完成 退款成功
				if($results['orderstate'] == 0 || $results['orderstate'] == 7 || $results['orderstate'] == 3 || $results['orderstate'] == 9){
					$archives = $dsql->SetQuery("DELETE FROM `#@__travel_order` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return  $langData['travel'][13][39];//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['travel'][13][40]);//订单为不可删除状态！
				}
			}else{
				return array("state" => 101, "info" => $langData['travel'][13][41]);//权限不足，请确认帐户信息后再进行操作！
			}
		}else{
			return array("state" => 101, "info" => $langData['travel'][13][42]);//订单不存在，或已经删除！
		}
	}

	/**
     * 旅游订单详细
     * @return array
     */
	public function orderDetail(){
		global $dsql;
		global $langData;
		$orderDetail = $cardnum = $cardnumList = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];

		global $userLogin;
		$userid = $userLogin->getMemberID();

		// if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//格式错误！
		if(!is_numeric($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！

		//主表信息
		$archives = $dsql->SetQuery("SELECT o.*, s.`id`as sid FROM `#@__travel_order` o  LEFT JOIN `#@__travel_store` s ON o.`store` = s.`id` WHERE ( o.`userid` = '$userid' OR s.`userid` = '$userid') AND o.`id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");
		if(!empty($results)){

			$results = $results[0];

			$orderDetail["ordernum"]   = $results["ordernum"];
			$orderDetail["walktime"]   = $results["walktime"];
			$orderDetail["departuretime"]   = $results["departuretime"];
			$orderDetail["userid"]     = $results["userid"];
			$orderDetail["contact"]    = $results["contact"];
			$orderDetail["failnote"]   = $results["failnote"];
			$orderDetail["proid"]   = $results["proid"];
			$orderDetail["enddate"]    = FloorTime($results["nopaydate"], $n = 2);

			//会员信息
			$orderDetail['member']     = getMemberDetail($results['userid']);

			//商品信息
			$proid = $results['proid'];
			if($results['type'] == 4){//签证
				$this->param = $proid;
				$detail = $this->visaDetail();
				$orderDetail['title']  = $detail['title'];

				$param = array(
					"service"     => "travel",
					"template"    => "visa-detail",
					"id"          => $detail['id']
				);
			}else{
				if($results['type'] == 1 || $results['type'] == 2){//景点门票 周边游
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
				}elseif($results['type'] == 3){//酒店
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
				}
				$ret = $dsql->dsqlOper($sql, 'results');
				if(!empty($ret)){
					$orderDetail['title']       = $ret[0]['title'];
					$orderDetail['price']       = $ret[0]['price'];

					if($results['type'] == 1 || $results['type'] == 2){
						$this->param = $ret[0]['ticketid'];
					}elseif($results['type'] == 3){
						$this->param = $ret[0]['hotelid'];
					}

					if($results['type'] == 1){
						$detail = $this->ticketDetail();
						$param = array(
							"service"     => "travel",
							"template"    => "ticket-detail",
							"id"          => $detail['id']
						);
					}elseif($results['type'] == 2){
						$detail = $this->agencyDetail();
						$param = array(
							"service"     => "travel",
							"template"    => "agency-detail",
							"id"          => $detail['id']
						);

					}elseif($results['type'] == 3){
						$detail = $this->hotelDetail();
						$param = array(
							"service"     => "travel",
							"template"    => "hotel-detail",
							"id"          => $detail['id']
						);
					}
				}
			}

			$orderDetail['product']['id']       = $detail['id'];
			$orderDetail['product']['title']    = $detail['title'];
			$orderDetail['product']['opentime'] = $detail['opentime'];
			$orderDetail['product']['address']  = $detail['address'];
			$orderDetail['product']['travelservice']  = $detail['travelservice'];
			$orderDetail['product']['missiontime']  = $detail['missiontime'];
			$orderDetail['product']['lng']      = $detail['lng'];
			$orderDetail['product']['lat']      = $detail['lat'];
			$orderDetail['product']['litpic']   = $detail['pics'][0]['path'];
			$orderDetail['product']['price']    = $detail['price'] ? $detail['price'] : $ret[0]['price'];

			$url = getUrlPath($param);
			$orderDetail['product']['url'] = $url;

			$orderDetail["procount"]   = $results["procount"];

			//总价
			$totalAmount = 0;
			$orderprice = $results["orderprice"];
			$point      = $results["point"];
			$balance    = $results["balance"];
			$payprice   = $results["payprice"];
			$procount   = $results["procount"];
			$totalAmount += $orderprice * $procount;


			$orderDetail["orderprice"] = $orderprice;
			$orderDetail["totalmoney"] = $totalAmount;
			$orderDetail["point"]      = $point;
			$orderDetail["balance"]    = $balance;
			$orderDetail["payprice"]   = $payprice;
			$orderDetail["orderstate"] = $results["orderstate"];
			$orderDetail["orderdate"]  = $results["orderdate"];

			$orderDetail["type"]  = $results["type"];
			$orderDetail["people"]  = $results["people"];
			$orderDetail["contact"]  = $results["contact"];
			$orderDetail["note"]  = $results["note"];
			$orderDetail["idcard"]  = $results["idcard"];
			$orderDetail["backpeople"]  = $results["backpeople"];
			$orderDetail["backcontact"]  = $results["backcontact"];
			$orderDetail["backaddress"]  = $results["backaddress"];
			$orderDetail["email"]  = $results["email"];
			$orderDetail["applicantinformation"]  = $results["applicantinformation"];

			$applicantinformationArr = array();
			if(!empty($results['applicantinformation'])){
				$applicantinformation = explode("|||", $results['applicantinformation']);
				foreach ($applicantinformation as $key => $value) {
					$val = explode("$$$", $value);
					$applicantinformationArr[$key]['name'] = $val[0];
					$applicantinformationArr[$key]['birth']  = $val[1];
					$applicantinformationArr[$key]['typename']  = $val[2];
				}
			}
			$orderDetail['applicantinformationArr']      = $applicantinformationArr;


			//未付款的提供付款链接
			if($results['orderstate'] == 0){
				$RenrenCrypt = new RenrenCrypt();
				$encodeid = base64_encode($RenrenCrypt->php_encrypt($results["ordernum"]));

				$param = array(
					"service"     => "travel",
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
					$payname = $cfg_pointName."+".$langData['homemaking'][9][43];//余额
				}elseif($results["paytype"] == "point"){
					$payname = $cfg_pointName;
				}elseif($results["paytype"] == "money"){
					$payname = $langData['homemaking'][9][43];//余额
				}
				$orderDetail["paytype"]   = $payname;
			}

			$orderDetail["paydate"]   = $results["paydate"];

			//服务码
			if(($results["type"] == 1 || $results["type"] == 2 || $results["type"] == 3 || $results["type"] == 4) && $results['orderstate'] != 0){
				$cardSql = $dsql->SetQuery("SELECT `cardnum`, `usedate`, `expireddate` FROM `#@__travelquan` WHERE `orderid` = ". $results["id"]);
				$cardResult = $dsql->dsqlOper($cardSql, "results");
				if($cardResult){
					foreach($cardResult as $key => $val){
						$cardnum[$key]['cardnum']     = join(" ", str_split($val['cardnum'], 4));
						$cardnum[$key]['usedate']     = $val['usedate'];
						$cardnum[$key]['expireddate'] = $val['expireddate'];
						$cardnumList[$key] = $val['cardnum'];
					}
				}
			}

			if($cardnum){
				$orderDetail["cardnum"]   = $cardnum;

				$param = array(
					"service" => "member",
					"template" => "verify-travel",
					"param" => "cardnum=".join(",", $cardnumList)
				);
				$orderDetail["cardnumUrl"] = getUrlPath($param);
			}

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

			$picslist = array();
			$pics = $results['pics'];
			if(!empty($pics)){
				$picsArr = explode(",", $pics);
				foreach ($picsArr as $key => $value) {
					$picslist[$key]['val'] = $value;
					$picslist[$key]['path'] = getFilePath($value);
				}
			}
			$orderDetail["picslist"]    = $picslist;

			//退款确定时间
			$orderDetail["retOkdate"]    = $results["ret-ok-date"];

			$orderDetail['now'] = GetMkTime(time());


			//卖家信息
			$this->param = (int)$results['sid'];
			$orderDetail['store'] = $this->storeDetail();

		}

		return $orderDetail;
	}

	/**
	 * 操作订单状态
	 * oper=cancelrefund 会员取消退款 返回上一次订单状态
	 * oper=cancel 会员取消退款
	 * @return array
	 */
	public function operOrder(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$pubdate         =  GetMkTime(time());
		$date            =  GetMkTime(time());

		if(empty($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//参数错误！

		if($oper == "cancelrefund"){//会员取消退款
			$archives = $dsql->SetQuery("SELECT `id`, `userid`, `refundtorderstate` FROM `#@__travel_order` WHERE `id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				$orderstate = $results['refundtorderstate'];

				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__travel_order` SET  `ret-date` = '0', `refundtorderstate` = '0', `orderstate` = '$orderstate', `ret-state` = 0 WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['travel'][13][44]); //保存到数据时发生错误，请检查字段内容！
				}

				return $langData['travel'][13][45];//提交成功！
			}else{
				return array("state" => 101, "info" => $langData['travel'][13][46]);//订单不存在，或已经删除！
			}
		}elseif($oper == 'cancel'){//取消订单
			$archives = $dsql->SetQuery("SELECT `id`, `proid`, `type`, `procount`, `userid`, `ordernum`, `walktime`, `point`, `balance`, `payprice`, `refundtorderstate` FROM `#@__travel_order` WHERE `id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results  = $results[0];

				$point    = $results['point'];    //需要退回的积分
				$balance  = $results['balance'];  //需要退回的余额
				$payprice = $results['payprice']; //需要退回的支付金额
				$ordernum = $results['ordernum'];
				$type     = $results['type'];
				$procount = $results['procount'];
				$proid    = $results['proid'];
				$walktime = $results['walktime'];

				$now1 = GetMkTime(date('Y-m-d', time()));

				if($type == 3){
					if($walktime < $now1){
						return array("state" => 101, "info" => $langData['travel'][13][93]);//逾期不可取消！
					}
					//更新已购买数量
					$sql = $dsql->SetQuery("UPDATE `#@__travel_hotelroom` SET `sale` = `sale` - $procount WHERE `id` = '$proid'");
					$dsql->dsqlOper($sql, "update");
				}

				//更新订单状态
				$sql = $dsql->SetQuery("UPDATE `#@__travel_order` SET `orderstate` = 7 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "update");

				//退回积分
				if(!empty($point)){
					$archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + '$point' WHERE `id` = '$userid'");
					$dsql->dsqlOper($archives, "update");

					//保存操作日志
					$info = $langData['travel'][13][75].$ordernum;
					$archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$point', '$info', '$date')");
					$dsql->dsqlOper($archives, "update");
				}

				//退回余额
				$money = $balance + $payprice;
				if($money > 0){
					$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$money' WHERE `id` = '$userid'");
					$dsql->dsqlOper($archives, "update");

					//保存操作日志
					$info = $langData['travel'][13][75].$ordernum;
					$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$money', '$info', '$date')");
					$dsql->dsqlOper($archives, "update");


					//减去会员的冻结金额
					$archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` - '$money' WHERE `id` = '$userid'");
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

				return $langData['travel'][13][45];//提交成功！

			}else{
				return array("state" => 101, "info" => $langData['travel'][13][46]);//订单不存在，或已经删除！
			}
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

		if(empty($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//参数错误！
		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		//验证订单
		$archives = $dsql->SetQuery("SELECT o.`id`, o.`orderprice`, o.`ordernum`, o.`procount`, o.`orderstate`, o.`proid`, o.`type`, s.`userid` as uid FROM `#@__travel_order` o LEFT JOIN `#@__travel_store` s ON o.`store` = s.`id` WHERE o.`id` = '$id' AND o.`userid` = '$uid' AND (o.`orderstate` = 1 OR o.`orderstate` = 5 OR o.`orderstate` = 2 OR o.`orderstate` = 4) AND o.`ret-state` = 0");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			//商品信息
			if($results[0]['type'] == 1 || $results[0]['type'] == 2){//景点门票 周边游
				$proid = $results[0]['proid'];
				$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
				$ret = $dsql->dsqlOper($sql, 'results');
				if(!empty($ret)){
					$orderDetail['title']       = $ret[0]['title'];
					$this->param = $ret[0]['ticketid'];
					if($results[0]['type'] == 1){
						$detail = $this->ticketDetail();
					}elseif($results[0]['type'] == 2){
						$detail = $this->agencyDetail();
					}
				}
			}elseif($results[0]['type'] == 3){//酒店
				$proid = $results[0]['proid'];
				$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
				$ret = $dsql->dsqlOper($sql, 'results');
				if(!empty($ret)){
					$orderDetail['title']       = $ret[0]['title'];
					$this->param = $ret[0]['hotelid'];
					$detail = $this->hotelDetail();
				}
			}elseif($results[0]['type'] == 4){
				$this->param = $results[0]['proid'];
				$detail = $this->visaDetail();
			}

			$title      = $ret[0]['title'] ? $ret[0]['title'] : $detail['title'];      //商品名称
			$procount   = $results[0]['procount'];   //购买数量
			$orderprice = $results[0]['orderprice']; //单价
			$ordernum   = $results[0]['ordernum'];   //订单号
			$sid        = $results[0]['uid'];        //卖家会员ID
			$orderstate = $results[0]['orderstate']; //订单状态
			$date       = GetMkTime(time());
			$retnote    = '';

			$orderIdArr = array();

			$paramBusi = array(
				"service"  => "member",
				"template" => "orderdetail",
				"action"   => "travel",
				"id"       => $id
			);

			//获取会员名
			$username = "";
			$sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $sid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
			}
			updateMemberNotice($sid, "会员-订单退款通知", $paramBusi, array("username" => $username, "order" => $ordernum, 'amount' => $orderprice, 'info' => $retnote));

			$sql = $dsql->SetQuery("UPDATE `#@__travel_order` SET `refundtorderstate` = '$orderstate', `orderstate` = 8, `ret-state` = 1, `ret-date` = '$date' WHERE `id` = ".$id);
			$dsql->dsqlOper($sql, "update");

			return $langData['travel'][13][70];//操作成功！

		}else{
			return array("state" => 200, "info" => $langData['homemaking'][9][70]);//操作失败，请核实订单状态后再操作！
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

		if(empty($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);//数据不完整，请检查！

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}


		//验证订单
		$archives = $dsql->SetQuery("SELECT o.`id`, o.`ordernum`, o.`point`, o.`proid`, o.`type`, o.`balance`, o.`payprice`, o.`orderprice`, o.`procount`, o.`userid` FROM `#@__travel_order` o LEFT JOIN `#@__travel_store` s ON o.`store` = s.`id` WHERE o.`id` = '$id' AND s.`userid` = '$uid' AND o.`orderstate` = 8 AND o.`ret-state` = 1");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			//验证商家账户余额是否足以支付退款
			$uinfo = $userLogin->getMemberInfo();
			$umoney = $uinfo['money'];

			$date     = GetMkTime(time());
			$proid      = $results[0]['proid'];      //商品ID
			$ordernum   = $results[0]['ordernum'];   //需要退回的订单号
			$orderprice = $results[0]['orderprice']; //订单商品单价
			$procount   = $results[0]['procount'];   //订单商品数量
			$totalMoney = $orderprice * $procount;   //需要扣除商家的费用

			// $title  //商品名称
			//商品名称
			if($results[0]['type'] == 1 || $results[0]['type'] == 2){//景点门票
				$proid = $results[0]['proid'];
				$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
				$ret = $dsql->dsqlOper($sql, 'results');
				if(!empty($ret)){
					$title       = $ret[0]['title'];
				}

				//更新已购买数量
				$sql = $dsql->SetQuery("UPDATE `#@__travel_ticketinfo` SET `sale` = `sale` - $procount WHERE `id` = '$proid'");
				$dsql->dsqlOper($sql, "update");

			}elseif($results[0]['type'] == 3){//酒店
				$proid = $results[0]['proid'];
				$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
				$ret = $dsql->dsqlOper($sql, 'results');
				if(!empty($ret)){
					$title       = $ret[0]['title'];
				}

				//更新已购买数量
				$sql = $dsql->SetQuery("UPDATE `#@__travel_hotelroom` SET `valid` = 0, `sale` = `sale` - $procount WHERE `id` = '$proid'");
				$dsql->dsqlOper($sql, "update");
			}elseif($results[0]['type'] == 4){//酒店
				$proid = $results[0]['proid'];
				$sql = $dsql->SetQuery("SELECT `id`, `title`, `price` FROM `#@__travel_visa` WHERE `id` = $proid ");
				$ret = $dsql->dsqlOper($sql, 'results');
				if(!empty($ret)){
					$title       = $ret[0]['title'];
				}

				//更新已购买数量
				$sql = $dsql->SetQuery("UPDATE `#@__travel_visa` SET `sale` = `sale` - $procount WHERE `id` = '$proid'");
				$dsql->dsqlOper($sql, "update");
			}

			//更新订单状态
			$now = GetMkTime(time());
			$sql = $dsql->SetQuery("UPDATE `#@__travel_order` SET `ret-state` = 0, `orderstate` = 9, `ret-ok-date` = '$now' WHERE `id` = ".$id);
			$dsql->dsqlOper($sql, "update");

			//退回会员积分、余额
			$userid   = $results[0]['userid'];   //需要退回的会员ID
			$point    = $results[0]['point'];    //需要退回的积分
			$balance  = $results[0]['balance'];  //需要退回的余额
			$payprice = $results[0]['payprice']; //需要退回的支付金额

			//退回积分
			if(!empty($point) && $point>0){
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + '$point' WHERE `id` = '$userid'");
				$dsql->dsqlOper($archives, "update");

				//保存操作日志
				$info = $langData['travel'][13][75].$ordernum;
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$point', '$info', '$date')");
				$dsql->dsqlOper($archives, "update");
			}

			//退回余额
			$money = $balance + $payprice;
			if($money > 0){
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$money' WHERE `id` = '$userid'");
				$dsql->dsqlOper($archives, "update");

				//保存操作日志
				$info = $langData['travel'][13][75].$ordernum;
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$money', '$info', '$date')");
				$dsql->dsqlOper($archives, "update");


				//减去会员的冻结金额
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` - '$money' WHERE `id` = '$userid'");
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
				"action"   => "travel",
				"id"       => $id
			);

			//获取会员名
			$username = "";
			$sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
			}
			updateMemberNotice($userid, "会员-订单退款成功", $paramBusi, array("username" => $username, "order" => $ordernum, 'amount' => $money));

			return $langData['homemaking'][9][73];//退款成功！

		}else{
			return array("state" => 200, "info" => $langData['homemaking'][9][70]);//操作失败，请核实订单状态后再操作！
		}

	}

	/**
	 * 验证旅游券状态
	 */
	public function verifyQuan(){
		global $dsql;
		global $userLogin;
		global $langData;

		$code = $this->param['code'];
		$now  = GetMkTime(time());
		$now1 = GetMkTime(date('Y-m-d', time()));

		if(!is_numeric($code)) return array("state" => 200, "info" => $langData['travel'][12][23]);//格式错误！

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
			return array("state" => 200, "info" => $langData['travel'][12][27]);//商家权限验证失败！
		}

		//查询券
		$archives = $dsql->SetQuery("SELECT q.`usedate`, q.`expireddate`, o.`proid`, o.`departuretime`, o.`type`, o.`orderprice` FROM `#@__travelquan` q LEFT JOIN `#@__travel_order` o ON o.`id` = q.`orderid` LEFT JOIN `#@__travel_store` s ON o.`store` = s.`id` WHERE (o.`orderstate` = 1 OR o.`orderstate` = 2) AND q.`cardnum` = '".$code."' AND s.`userid` = ".$uid);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$usedate     = $results[0]['usedate'];
			$expireddate = $results[0]['expireddate'];
			$proid       = $results[0]['proid'];
			$type        = $results[0]['type'];
			$departuretime= $results[0]['departuretime'];

			//是否已经使用过
			if($usedate != 0){
				$usedate = date("Y-m-d H:i:s", $usedate);
				return array("state" => 101, "info" => $langData['travel'][13][54].$usedate.$langData['travel'][13][55]);//'验证失败，此券已于'.$usedate.'使用过了！'

			//是否已经过期
			}elseif($expireddate < $now1){
				if($type==3){
					if($departuretime < $now1){
						return array("state" => 101, "info" => $langData['travel'][13][56]);//验证失败，此券已经过期！
					}
				}else{
					return array("state" => 101, "info" => $langData['travel'][13][56]);//验证失败，此券已经过期！
				}
			//可以使用
			}else{

				//获取信息
				if($type == 1 || $type == 2){//景点门票 周边游
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
				}elseif($type == 3){//酒店
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
				}elseif($type == 4){//签证
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `price` FROM `#@__travel_visa` WHERE `id` = $proid ");
				}
				$ret = $dsql->dsqlOper($sql, 'results');
				if(!empty($ret)){
					$id    = $ret[0]['id'];
					$title = $ret[0]['title'];

					if($type == 1 || $type == 2){
						$this->param = $ret[0]['ticketid'];
					}elseif($type == 3){
						$this->param = $ret[0]['hotelid'];
					}elseif($type == 4){
						$this->param = $ret[0]['id'];
					}

					if($type == 1){
						$proDetail = $this->ticketDetail();
						$param = array(
							"service"  => "travel",
							"template" => "ticket-detail",
							"id"       => $proDetail['id']
						);
					}elseif($type == 2){
						$proDetail = $this->agencyDetail();
						$param = array(
							"service"  => "travel",
							"template" => "agency-detail",
							"id"       => $proDetail['id']
						);
					}elseif($type == 3){
						$proDetail = $this->hotelDetail();
						$param = array(
							"service"  => "travel",
							"template" => "hotel-detail",
							"id"       => $proDetail['id']
						);
					}elseif($type == 4){
						$proDetail = $this->visaDetail();
						$param = array(
							"service"  => "travel",
							"template" => "visa-detail",
							"id"       => $proDetail['id']
						);
					}

					$url = getUrlPath($param);
					$currency = echoCurrency(array("type" => "short"));
					return $langData['travel'][13][58]."<a href='".$url."' target='_blank'>$title</a> [".$ret[0]['price'].$currency."]";//验证成功，项目：

				}else{
					return array("state" => 101, "info" => $langData['travel'][13][57]);//'验证失败，信息不存在！'
				}
			}

		}else{
			return array("state" => 101, "info" => $langData['travel'][13][61]);//密码错误，请与消费者确认提供的密码是否正确！
		}


	}


	/**
	 * 消费旅游券
	 */
	public function useQuan(){
		global $dsql;
		global $userLogin;
		global $langData;

		$codes = $this->param['codes'];
		$now   = GetMkTime(time());
		$uid   = $userLogin->getMemberID();

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "travel"))){
			return array("state" => 200, "info" => $langData['travel'][12][27]);//商家权限验证失败！
		}

		if(empty($codes)) return array("state" => 200, "info" => $langData['travel'][13][59]);//请输入要消费的旅游券密码！

		$codeArr = explode(",", $codes);
		$success = 0;
		foreach ($codeArr as $key => $value) {

			$this->param['code'] = $value;
			$verify = $this->verifyQuan();
			if(!is_array($verify)){

				$sql = $dsql->SetQuery("UPDATE `#@__travelquan` SET `usedate` = '$now' WHERE `cardnum` = '$value'");
				$res  = $dsql->dsqlOper($sql, "update");

				if($res == "ok"){
					$success++;

					//查询订单信息
					$sql = $dsql->SetQuery("SELECT q.`orderid`, o.`type`, o.`proid`, o.`orderprice`, o.`userid`, o.`procount`, o.`orderprice`, o.`balance`, o.`payprice` FROM `#@__travelquan` q LEFT JOIN `#@__travel_order` o ON o.`id` = q.`orderid` WHERE q.`cardnum` = '$value'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){

						$orderid    = $ret[0]['orderid'];
						$procount   = $ret[0]['procount'];   //数量
						$orderprice = $ret[0]['orderprice']; //单价
						$balance    = $ret[0]['balance'];    //余额金额
						$payprice   = $ret[0]['payprice'];   //支付金额
						$userid     = $ret[0]['userid'];     //买家ID
						$type       = $ret[0]['type'];
						$proid      = $ret[0]['proid'];


						//更新订单状态，如果券都用掉了，就更新订单状态为已使用
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travelquan` WHERE `orderid` = (SELECT `orderid` FROM `#@__travelquan` WHERE `cardnum` = '$value') AND `usedate` = 0");
						$ret = $dsql->dsqlOper($sql, "totalCount");
						if($ret == 0){
							$sql = $dsql->SetQuery("UPDATE `#@__travel_order` SET `orderstate` = 3, `ret-state` = 0 WHERE `id` = '$orderid'");
							$dsql->dsqlOper($sql, "update");
						}

						/* if($type==3){//酒店
							$sql = $dsql->SetQuery("UPDATE `#@__travel_hotelroom` SET `valid` = 0 WHERE `id` = '$proid'");
							$dsql->dsqlOper($sql, "update");
						} */


						//如果有使用余额和第三方支付，将买家冻结的金额移除并增加日志
						$totalPayPrice = $balance + $payprice;
						if($totalPayPrice > 0){

							//减去消费会员的冻结金额
							$archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` - '$totalPayPrice' WHERE `id` = '$userid'");
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
							$info = $langData['travel'][13][60] . $value;
							$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$totalPayPrice', '$info', '$now')");//旅游券消费：
							$dsql->dsqlOper($archives, "update");

						}


						//扣除佣金
						$totalPrice = $procount * $orderprice;

						//扣除佣金
						global $cfg_travelFee;
						$cfg_travelFee = (float)$cfg_travelFee;

						$fee = $totalPrice * $cfg_travelFee / 100;
						$fee = $fee < 0.01 ? 0 : $fee;
						$totalPrice_ = sprintf('%.2f', $totalPrice - $fee);

						//将费用转至商家帐户
						$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$totalPrice_' WHERE `id` = '$uid'");
						$dsql->dsqlOper($archives, "update");

						//保存操作日志
						$info = $langData['travel'][13][60] . $value;
						$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '1', '$totalPrice_', '$info', '$now')");
						$dsql->dsqlOper($archives, "update");

						//返积分
      					(new member())->returnPoint("travel", $userid, $totalPrice, $value);

					}

				}

			}

		}

		if($success > 0){
			return $langData['travel'][13][65];//消费成功！
		}else{
			return array("state" => 200, "info" => $langData['travel'][13][66]);//消费失败，请检查您输入的旅游券密码！
		}

	}


	/**
	 * 撤消旅游券
	 */
	public function cancelQuan(){
		global $dsql;
		global $userLogin;
		global $langData;

		$ids = $this->param['ids'];

		if(empty($ids)) return array("state" => 200, "info" => $langData['travel'][13][59]);//请输入要消费的旅游券密码！

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$idArr   = explode(",", $ids);
		$success = 0;
		$now     = GetMkTime(time());

		foreach ($idArr as $key => $value) {

			$archives = $dsql->SetQuery("SELECT q.`cardnum`, o.`id`, o.`orderprice`, o.`userid`, o.`procount`, o.`orderprice`, o.`balance`, o.`payprice` FROM `#@__travelquan` q LEFT JOIN `#@__travel_order` o ON o.`id` = q.`orderid` LEFT JOIN `#@__travel_store` s ON o.`store` = s.`id` WHERE q.`id` = '$value' AND s.`uid` = ".$uid);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){

				$cardnum    = $results[0]['cardnum'];

				$procount   = $results[0]['procount'];   //数量
				$orderprice = $results[0]['orderprice']; //单价
				$balance    = $results[0]['balance'];    //余额金额
				$payprice   = $results[0]['payprice'];   //支付金额
				$userid     = $results[0]['userid'];     //买家ID

				$uinfo = $userLogin->getMemberInfo();
				$umoney = $uinfo['money'];



				//扣除佣金
				$orderprice = $procount * $orderprice;

				global $cfg_travelFee;
				$cfg_travelFee = (float)$cfg_travelFee;

				$fee = $orderprice * $cfg_travelFee / 100;
				$fee = $fee < 0.01 ? 0 : $fee;
				$orderprice_ = sprintf('%.2f', $orderprice - $fee);

				//判断商家账户全额是否充足
				if($umoney < $orderprice_) return array("state" => 200, "info" => $langData['travel'][13][67]);//您的账户余额不足，无法撤消，请先充值！

				//从商家帐户减去相应金额
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$orderprice_' WHERE `id` = '$uid'");
				$dsql->dsqlOper($archives, "update");

				//保存操作日志
				$info = $langData['travel'][13][68].$cardnum;
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '0', '$orderprice_', '$info', '$now')");//撤消旅游券：
				$dsql->dsqlOper($archives, "update");


				//将旅游券状态更改为未使用
				$sql = $dsql->SetQuery("UPDATE `#@__travelquan` SET `usedate` = 0 WHERE `id` = '$value'");
				$dsql->dsqlOper($sql, "update");

				//更新订单状态
				$sql = $dsql->SetQuery("UPDATE `#@__travel_order` SET `orderstate` = 1 WHERE `id` = ".$results[0]['id']);
				$dsql->dsqlOper($sql, "update");


				//增加消费会员的冻结金额
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` + '$orderprice' WHERE `id` = '$userid'");
				$dsql->dsqlOper($archives, "update");

				//保存操作日志
				$info = $langData['travel'][13][69].$cardnum;
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$orderprice', '$info', '$now')");//旅游券撤消后冻结：
				$dsql->dsqlOper($archives, "update");

			}

		}

		return $langData['travel'][13][70];//操作成功！
	}

	/**
	 * 一键续住
	 */
	public function oneKeyContinued(){
		//以离店时间
		global $dsql;
		global $userLogin;
		global $langData;

		//获取用户ID
		$userid = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param = $this->param;
		$id    = $param['id'];

		if(empty($id))    return array("state" => 200, "info" => $langData['travel'][12][23]);  //格式错误


		$sql = $dsql->SetQuery("SELECT `id`, `walktime`, `departuretime`, `orderstate`, `proid`, `procount`, `people`, `contact` FROM `#@__travel_order` WHERE `id` = $id ");
		$ret = $dsql->dsqlOper($sql, 'results');
		if($ret){
			$id = $ret[0]['id'];
			$walktime = $ret[0]['walktime'];
			$departuretime = $ret[0]['departuretime'];
			$proid = $ret[0]['proid'];
			$people = $ret[0]['people'];
			$contact = $ret[0]['contact'];
			$procount = $ret[0]['procount'];
			$orderstate = $ret[0]['orderstate'];

			$now = GetMkTime(date("Y-m-d"));
			if(($orderstate == 1 && $departuretime > $now) || ($orderstate == 3 && $departuretime > $now)){
				return array("state" => 200, "info" => $langData['travel'][13][102] . date("Y-m-d", $departuretime) . $langData['travel'][13][103]);  //该房间已出售，与2019-06-16到期
			}

			$daytime = $departuretime - $walktime;

			$walktime = GetMkTime(date("Y-m-d"));
			$departuretime = $walktime + $daytime;

			$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
			$ret = $dsql->dsqlOper($sql, 'results');
			if(is_array($ret)){

				$this->param = $ret[0]['hotelid'];
				$detail = $this->hotelDetail();

				$store = $detail['store']['id'];

				if($detail['store']['userid'] == $userid) return array("state" => 200, "info" => $langData['travel'][13][14]); //企业会员不可以购买自己的门票！
				if(!is_array($detail)) return array("state" => 200, "info" => $langData['travel'][13][71]);//房间信息不存在

				$price = $ret[0]['price'];
				$specialtime = $ret[0]['specialtime'] ? unserialize($ret[0]['specialtime']) : '';

				$priceArr = array();
				if(!empty($specialtime)){
					foreach($specialtime as $key=>$val){
						if($walktime >= GetMkTime($val['stime']) && $walktime < GetMkTime($val['etime'])){
							$priceArr[] = $val['price'];
						}
					}
				}

				if(count($priceArr) > 0){
					$price = array_pop($priceArr);
				}else{
					$price = $price;
				}

				$ordernum = create_ordernum();

				$pubdate   = GetMkTime(time());
				$nopaydate = $pubdate + 1800;

				$archives = $dsql->SetQuery("INSERT INTO `#@__travel_order` (`ordernum`, `store`, `proid`, `userid`, `orderstate`, `orderdate`, `procount`, `people`, `contact`, `orderprice`, `type`, `tab`, `walktime`, `nopaydate`, `departuretime`) VALUES ('$ordernum', '$store', '$proid', '$userid', '0', '$pubdate', '$procount', '$people', '$contact', '$price', '3', 'travel', '$walktime', '$nopaydate', '$departuretime')");
				$return = $dsql->dsqlOper($archives, "update");
				if($return == "ok"){
					$url[] = $ordernum;
				}else{
					return array("state" => 200, "info" => $langData['homemaking'][9][99]);//下单失败
				}

				$RenrenCrypt = new RenrenCrypt();
				$ids = base64_encode($RenrenCrypt->php_encrypt(join(",", $url)));

				$param = array(
					"service"     => "travel",
					"template"    => "comfirm",
					"param"       => "ordernum=".$ids
				);
				return getUrlPath($param);

			}else{
				return array("state" => 200, "info" => $langData['travel'][13][71]);//房间信息不存在
			}

		}else{
			return array("state" => 200, "info" => $langData['travel'][13][71]);  //格式错误
		}

	}

	/**
	 * 计划任务
	 */
	public function receipt(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];

		if(empty($id)) return array("state" => 200, "info" => $langData['travel'][12][23]);  //参数错误！

		//验证订单
		$archives = $dsql->SetQuery("SELECT o.`id`, o.`ordernum`, o.`procount`, o.`orderprice`, o.`balance`, o.`payprice`, o.`userid`, o.`type`, o.`proid` FROM `#@__travel_order` o  WHERE o.`id` = '$id' AND o.`orderstate` = 1");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			//更新订单状态
			$now = GetMkTime(time());
			$sql = $dsql->SetQuery("UPDATE `#@__travel_order` SET `orderstate` = '3' WHERE `id` = ".$id);
			$dsql->dsqlOper($sql, "update");

			//将订单费用转到卖家帐户
			$date       = GetMkTime(time());
			$ordernum   = $results[0]['ordernum'];   //订单号
			$procount   = $results[0]['procount'];   //数量
			$orderprice = $results[0]['orderprice']; //单价
			$balance    = $results[0]['balance'];    //余额金额
			$payprice   = $results[0]['payprice'];   //支付金额
			$userid     = $results[0]['userid'];     //买家ID
			$type       = $results[0]['type'];
			$proid      = $results[0]['proid'];
			$orderid    = $results[0]['id'];

			if($type == 4){//签证
				$this->param = $proid;
				$detail = $this->visaDetail();

			}else{
				if($type == 1 || $type == 2){//景点门票 周边游
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `ticketid`, `price`, `specialtime` FROM `#@__travel_ticketinfo` WHERE `id` = $proid ");
				}elseif($type == 3){//酒店
					$sql = $dsql->SetQuery("SELECT `id`, `title`, `hotelid`, `price`, `specialtime` FROM `#@__travel_hotelroom` WHERE `id` = $proid ");
				}
				$ret = $dsql->dsqlOper($sql, 'results');

				if($type == 1 || $type == 2){
					$this->param = $ret[0]['ticketid'];
				}elseif($type == 3){
					$this->param = $ret[0]['hotelid'];
				}

				if($type == 1){
					$detail = $this->ticketDetail();
				}elseif($type == 2){
					$detail = $this->agencyDetail();
				}elseif($type == 3){
					$detail = $this->hotelDetail();

					$sql = $dsql->SetQuery("UPDATE `#@__travel_hotelroom` SET `valid` = 0 WHERE `id` = '$proid'");
					$dsql->dsqlOper($sql, "update");

				}
			}
			$uid        = $detail['store']['userid'];         //卖家ID
			$title      = $ret[0]['title'] ? $ret[0]['title'] : $detail['title'];                   //商品名称

			//如果有使用余额和第三方支付，将买家冻结的金额移除并增加日志
			$totalPayPrice = $balance + $payprice;
			if($totalPayPrice > 0){
				//减去消费会员的冻结金额
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` - '$totalPayPrice' WHERE `id` = '$userid'");
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
				$info = $langData['travel'][13][60] . $ordernum;
				//保存操作日志
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$totalPayPrice', '$info', '$date')");
				$dsql->dsqlOper($archives, "update");
			}

			$sql = $dsql->SetQuery("UPDATE `#@__travelquan` SET `usedate` = '$date' WHERE `orderid` = '$orderid'");
			$dsql->dsqlOper($sql, "update");

			//商家结算
			//扣除佣金
			global $cfg_travelFee;
			$cfg_travelFee = (float)$cfg_travelFee;

			$fee = $totalPayPrice * $cfg_travelFee / 100;
			$fee = $fee < 0.01 ? 0 : $fee;
			$totalPayPrice_ = sprintf('%.2f', $totalPayPrice - $fee);

			if($totalPayPrice_ > 0){
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$totalPayPrice_' WHERE `id` = '$uid'");
				$dsql->dsqlOper($archives, "update");

				//保存操作日志
				$info = $langData['travel'][13][60] . $ordernum;
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '1', '$totalPayPrice_', '$info', '$date')");
				$dsql->dsqlOper($archives, "update");
			}

			//商家会员消息通知
			$paramBusi = array(
				"service"  => "member",
				"template" => "orderdetail",
				"action"   => "travel",
				"id"       => $id
			);

			//获取会员名
			$username = "";
			$sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
			}
			updateMemberNotice($uid, "会员-商品成交通知", $paramBusi, array("username" => $username, "title" => $title, 'amount' => $totalPayPrice));

			//返积分
			(new member())->returnPoint("travel", $userid, $totalPayPrice, $ordernum);

			return $langData['siteConfig'][20][244];  //操作成功

		}else{
			return array("state" => 200, "info" => $langData['shop'][4][23]);  //操作失败，请核实订单状态后再操作！
		}
	}

	/**
	 * 分类 2019-05-23
	 */
	public function module_type(){
		global $langData;
		$typeList = array();
        $typeList[] = array('id' => 1, 'typename' => $langData['travel'][7][13], 'lower' => array());//酒店
		$typeList[] = array('id' => 2, 'typename' => $langData['travel'][1][0], 'lower' => array());//景点门票
		$typeList[] = array('id' => 3, 'typename' => $langData['travel'][9][2], 'lower' => array());//租车
		$typeList[] = array('id' => 4, 'typename' => $langData['travel'][5][0], 'lower' => array());//旅行社
		$typeList[] = array('id' => 5, 'typename' => $langData['travel'][3][1], 'lower' => array());//视频
		$typeList[] = array('id' => 6, 'typename' => $langData['travel'][0][7], 'lower' => array());//旅游攻略
		$typeList[] = array('id' => 7, 'typename' => $langData['travel'][5][5], 'lower' => array());//签证
        return $typeList;
	}

	/**
	 * 窗户分类
	 */
	public function iswindow_type(){
		global $langData;
		$typeList = array();
        $typeList[] = array('id' => 0, 'typename' => $langData['travel'][12][2], 'lower' => array());//无窗
		$typeList[] = array('id' => 1, 'typename' => $langData['travel'][12][3], 'lower' => array());//有窗
        return $typeList;
	}

	/**
	 * 房间类型
	 */
	public function room_type(){
		global $langData;
		$typeList = array();
        $typeList[] = array('id' => 0, 'typename' => $langData['travel'][12][4], 'lower' => array());//双床
		$typeList[] = array('id' => 1, 'typename' => $langData['travel'][12][5], 'lower' => array());//多床
		$typeList[] = array('id' => 2, 'typename' => $langData['travel'][12][6], 'lower' => array());//大床
        return $typeList;
	}

	/**
	 * 早餐分类
	 */
	public function breakfast_type(){
		global $langData;
		$typeList = array();
        $typeList[] = array('id' => 0, 'typename' => $langData['travel'][12][7], 'lower' => array());//不含早餐
		$typeList[] = array('id' => 1, 'typename' => $langData['travel'][12][8], 'lower' => array());//含早餐
        return $typeList;
	}

	/**
	 * 酒店分类
	 */
	public function travelhotel_type(){
		global $langData;
		$typeList = array();
        $typeList[] = array('id' => 1, 'typename' => $langData['travel'][12][0], 'lower' => array());//星级酒店
		$typeList[] = array('id' => 2, 'typename' => $langData['travel'][12][1], 'lower' => array());//民宿
        return $typeList;
	}

	/**
	 * 周边游分类
	 */
	public function travelagency_type(){
		global $langData;
		$typeList = array();
        $typeList[] = array('id' => 0, 'typename' => $langData['travel'][2][0], 'lower' => array()); //一日游
		$typeList[] = array('id' => 1, 'typename' => $langData['travel'][2][13], 'lower' => array());//跟团游
        return $typeList;
	}

	/**
	 * 景区分类
	 */
	public function star_type(){
		global $langData;
		$typeList = array();
        $typeList[] = array('id' => 1, 'typename' => $langData['travel'][12][52], 'lower' => array()); //A级
		$typeList[] = array('id' => 2, 'typename' => $langData['travel'][12][53], 'lower' => array());//2A级
		$typeList[] = array('id' => 3, 'typename' => $langData['travel'][12][54], 'lower' => array());//3A级
		$typeList[] = array('id' => 4, 'typename' => $langData['travel'][12][55], 'lower' => array());//4A级
		$typeList[] = array('id' => 5, 'typename' => $langData['travel'][12][56], 'lower' => array());//5A级
        return $typeList;
	}

	/**
	 * 签证地区洲分类
	 */
	public function continent_type(){
		global $langData;
		$typeList = array();
        $typeList[] = array('id' => 1, 'typename' => $langData['travel'][12][81], 'lower' => array()); //亚洲
		$typeList[] = array('id' => 2, 'typename' => $langData['travel'][12][82], 'lower' => array()); //大洋洲
		$typeList[] = array('id' => 3, 'typename' => $langData['travel'][12][83], 'lower' => array()); //美洲
		$typeList[] = array('id' => 4, 'typename' => $langData['travel'][12][84], 'lower' => array()); //欧洲
		$typeList[] = array('id' => 5, 'typename' => $langData['travel'][12][85], 'lower' => array()); //非洲
        return $typeList;
	}

	/**
	 * 签证分类
	 */
	public function typeid_type(){
		global $langData;
		$typeList = array();
        $typeList[] = array('id' => 0, 'typename' => $langData['travel'][12][86], 'lower' => array()); //免签
		$typeList[] = array('id' => 1, 'typename' => $langData['travel'][12][87], 'lower' => array()); //落地签
        return $typeList;
	}

	public function gettypename($fun, $id){
        $list = $this->$fun();
        return $list[array_search($id, array_column($list, "id"))]['typename'];
    }


}
