<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 婚嫁模块API接口
 *
 * @version        $Id: marry.class.php 2014-8-5 下午17:10:21 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class marry {
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
     * 婚嫁信息基本参数
     * @return array
     */
	public function config(){

		require(HUONIAOINC."/config/marry.inc.php");

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

		// $domainInfo = getDomain('marry', 'config');
		// $customChannelDomain = $domainInfo['domain'];
		// if($customSubDomain == 0){
		// 	$customChannelDomain = "http://".$customChannelDomain;
		// }elseif($customSubDomain == 1){
		// 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
		// }elseif($customSubDomain == 2){
		// 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
		// }

		// include HUONIAOINC.'/siteModuleDomain.inc.php';
		$customChannelDomain = getDomainFullUrl('marry', $customSubDomain);

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
			$return['storeatlasMax'] = $custom_store_atlasMax;
			$return['marryhotelfieldatlasMax']   = $custom_marryhotelfield_atlasMax;
			$return['marryweddingcaratlasMax']   = $custom_marryweddingcar_atlasMax;
			$return['marryplancaseatlasMax']     = $custom_marryplancase_atlasMax;
			$return['marryplanmealatlasMax']     = $custom_marryplanmeal_atlasMax;

			$marryTag_ = array();
			if($custommarryTag){
				$arr = explode("\n", $custommarryTag);
				foreach ($arr as $k => $v) {
					$arr_ = explode('|', $v);
					foreach ($arr_ as $s => $r) {
						if(trim($r)){
							$marryTag_[] = trim($r);
						}
					}
				}
			}
			$return['marryTag']        = $marryTag_;


		}

		return $return;

	}

	/**
     * 信息地区
     * @return array
     */
    public function addr(){
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
     * 婚嫁分类
     * @return array
     */
	public function type(){
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
		$results = $dsql->getTypeList($type, "marry_type", $son, $page, $pageSize);
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
	 * 商家列表
	 */
	public function storeList(){
		global $dsql;
		global $langData;
		$pageinfo = $list = array();
		$page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
			}else{
				$search   = $this->param['search'];
				$typeid   = $this->param['typeid'];
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
				//1：婚宴酒店;2、婚礼策划;3、婚宴套餐;
				$istype   = (int)$this->param['istype'];
				$istype   = $istype ? $istype : 1;
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

		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "marry_type")){
                global $arr_data;
                $arr_data = array();
                $lower = arr_foreach($dsql->getTypeList($typeid, "marry_type"));
                $lower = $typeid.",".join(',',$lower);
            }else{
                $lower = $typeid;
            }

			$where .= " AND `typeid` in ($lower)";
		}

		if(!empty($search)){

			siteSearchLog("marry", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__marry_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `user`.title like '%$search%' OR `store`.address like '%$search%'");
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
                $orderby_ = " ORDER BY `click` DESC, `rec` DESC, `weight` DESC, `id` DESC";
                break;
            //发布时间降序
            case 2:
                $orderby_ = " ORDER BY `pubdate` DESC, `rec` DESC, `weight` DESC, `id` DESC";
				break;
			//推荐排序
			case 3:
                $orderby_ = " ORDER BY `rec` DESC, `weight` DESC, `pubdate` DESC, `id` DESC";
				break;
			//距离排序
			case 4:
				if((!empty($lng))&&(!empty($lat))){
					$orderby_ = " ORDER BY distance ASC";
				}
				break;
			//价格排序
			case 5:
				$orderby_ = " ORDER BY `price` DESC, `rec` DESC, `weight` DESC, `pubdate` DESC, `id` DESC";
				break;
			case 6:
				$orderby_ = " ORDER BY `price` ASC, `rec` DESC, `weight` DESC, `pubdate` DESC, `id` DESC";
				break;
            default:
                $orderby_ = " ORDER BY `weight` DESC, `rec` DESC, `id` DESC";
                break;
        }

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__marry_store` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("marry_store_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => $langData['siteConfig'][21][64]);//暂无数据！

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		$archives = $dsql->SetQuery("SELECT `flag`, `bind_module`, `price`, `taoxi`, `anli`, `title`, `pubdate`, `pics`, `lat`, `lng`, `id`,`userid`, `typeid`, `address`, `tel`, `tag`, `addrid`, ".$select." `rec` FROM `#@__marry_store` WHERE 1 = 1".$where);
		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$sql = $dsql->SetQuery($archives.$orderby_.$where);
		$results = getCache("marry_store_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['address']   = $val['address'];
				$list[$key]['tel']       = $val['tel'];
				$list[$key]['lng']       = $val['lng'];
				$list[$key]['lat']       = $val['lat'];
				$list[$key]['rec']       = $val['rec'];
				$list[$key]['tag']       = $val['tag'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['price']     = $val['price'];
				$list[$key]['taoxi']     = $val['taoxi'];
				$list[$key]['anli']      = $val['anli'];
				$list[$key]['flag']      = $val['flag'];
				$list[$key]['bind_module']= $val['bind_module'];
				
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

				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__marry_type` WHERE `id` = ".$val['typeid']);
				$ret = $dsql->dsqlOper($sql, "results");
				$list[$key]['typename']   = $ret[0]['typename'] ? $ret[0]['typename'] : '';

				$imglist = array();
				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
					$list[$key]['litpic'] = getFilePath($pics[0]);
				}else{
					$sql = $dsql->SetQuery("SELECT `pics` FROM `#@__marry_list` WHERE `company` = ".$val['id']." AND `state` = 1 ORDER BY `weight` DESC, `id` DESC LIMIT 0,1");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						if(!empty($ret[0]['pics'])){
							$pics = explode(",", $ret[0]['pics']);
						} 
						$list[$key]['litpic'] = $pics[0]? getFilePath($pics[0]) : "/static/images/404.jpg";
					}else{
						$list[$key]['litpic'] = "/static/images/404.jpg";
					}
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

				$flagArr = array();
				if(!empty($val['flag'])){
					$flag = explode(",", $val['flag']);
					foreach ($flag as $k => $v) {
						$flagArr[$k] = array(
							"jc" => $v,
							"py" =>  GetPinyin($v)
						);
					}
				}
				$list[$key]['flagAll'] = $flagArr;

				if($filter!=''){
					$param = array(
						"service" => "marry",
						"template" => "store-detail",
						"id" => $val['id'],
						"istype" => $istype,
						"typeid" => $filter
					);
				}else{
					$param = array(
						"service" => "marry",
						"template" => "store-detail",
						"id" => $val['id'],
						"istype" => $istype,
					);
				}
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
			return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
		}

		$where = " AND `state` = 1";
		if(!is_numeric($id)){
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `userid` = ".$uid);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$id = $results[0]['id'];
				$where = "";
			}else{
				return array("state" => 200, "info" => $langData['marry'][5][11]);//该会员暂未开通公司
			}
		}

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `cityid`, `lng`, `lat`, `tel`, `click`, `state`, `rec`, `userid`, `typeid`, `addrid`, `price`, `pics`, `taoxi`, `anli`, `bind_module`, `flag`, `video`, `note`, `address`, `tag` FROM `#@__marry_store` WHERE `id` = ".$id.$where);
		$results  = getCache("marry_store_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]         = $results[0]['id'];
			$storeDetail["title"]      = $results[0]['title'];
			$storeDetail['cityid']     = $results[0]['cityid'];
			$storeDetail["lng"]        = $results[0]['lng'];
			$storeDetail["lat"]        = $results[0]['lat'];
			$storeDetail["tel"]        = $results[0]['tel'];
			$storeDetail["click"]      = $results[0]['click'];
			$storeDetail["state"]      = $results[0]['state'];
			$storeDetail["tag"]        = $results[0]['tag'];
			$storeDetail["rec"]        = $results[0]['rec'];
			$storeDetail["userid"]     = $results[0]['userid'];
			$storeDetail["price"]      = $results[0]['price'];
			$storeDetail["taoxi"]      = $results[0]['taoxi'];
			$storeDetail["anli"]       = $results[0]['anli'];
			$storeDetail["bind_module"]= $results[0]['bind_module'];
			$storeDetail["flag"]       = $results[0]['flag'];
			$storeDetail["video"]      = $results[0]['video'];
			$storeDetail["note"]       = $results[0]['note'];
			$storeDetail["videoSource"]= getFilePath($results[0]['video']);

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
			$storeDetail["tagArr"]  = $tagArr;
			$storeDetail["tagArr_"] = $tagArr_;

			$flagArr = array();
			$flagArr_ = $results[0]['flag'] ? explode(',', $results[0]['flag']) : array();
			if($flagArr_){
				foreach ($flagArr_ as $k => $v) {
					$flagArr[$k] = array(
						"py" => GetPinyin($v),
						"val" => $v
					);
				}
			}
			$storeDetail["flagArr"]  = $flagArr;
			$storeDetail["flagArr_"] = $flagArr_;

			//会员信息
			$uid = $results[0]['userid'];
			$storeDetail['member']     = getMemberDetail($uid);

			$storeDetail["typeid"]     = $results[0]['typeid'];
			global $data;
			$data = "";
			$tuantype = getParentArr("marry_type", $results[0]['typeid']);
			if($tuantype){
				$tuantype = array_reverse(parent_foreach($tuantype, "typename"));
				$storeDetail['typename'] = join(" > ", $tuantype);
				$storeDetail['typenameonly'] = count($tuantype) > 2 ? $tuantype[1] : $tuantype[0];
			}else{
				$storeDetail['typename'] = "";
				$storeDetail['typenameonly'] = "";
			}

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

			// $archives = $dsql->SetQuery("SELECT `id` FROM `#@__marrycommon` WHERE `aid` = ".$results[0]['id']." AND `type` = 0 AND `ischeck` = 1 AND `floor` = 0");
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'marry-store' AND `aid` = '$id' AND `pid` = 0");
			$totalCount = $dsql->dsqlOper($archives, "totalCount");
            $storeDetail['common'] = $totalCount;

			//验证是否已经收藏
			$collect = '';
			if($uid != -1){
				$params = array(
					"module" => "marry",
					"temp"   => "store-detail" . "|" . $istype . "|" . $typeid,
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
		$typeid      = (int)$param['typeid'];
		$address     = $param['address'];
		$bind_module = $param['bind_module'];
		$tel         = $param['tel'];
		$pics        = $param['pics'];
		$taoxi       = $param['taoxi'];
		$anli        = $param['anli'];
		$video       = $param['video'];
		$price       = $param['price'];
		$note        = filterSensitiveWords(addslashes($param['note']));
		if(isset($param['tag'])){
		    $tag = $param['tag'];
		    $tag = is_array($tag) ? join("|", $tag) : $tag;
		}
		$pubdate = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录
		}

		//验证会员类型
		$userDetail = $userLogin->getMemberInfo();
		if($userDetail['userType'] != 2){
			return array("state" => 200, "info" => $langData['marry'][5][1]);//账号验证错误，操作失败
		}

		//权限验证
		if(!verifyModuleAuth(array("module" => "marry"))){
			return array("state" => 200, "info" => $langData['marry'][5][2]);//商家权限验证失败
		}

		if(empty($title)){
			return array("state" => 200, "info" => $langData['marry'][5][3]);//请填写公司名称
		}

		if(empty($cityid)){
			return array("state" => 200, "info" => $langData['marry'][5][4]);//请选择所在地区
		}

		if(empty($tel)){
			return array("state" => 200, "info" => $langData['marry'][5][5]);//请填写联系方式
		}

		if(empty($pics)){
			return array("state" => 200, "info" => $langData['marry'][5][6]);//请上传图集
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		//新商铺
		if(!$userResult){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__marry_store` (`cityid`, `title`, `userid`, `typeid`, `addrid`, `address`, `tel`, `price`, `pics`, `video`, `note`, `bind_module`, `taoxi`, `anli`, `tag`, `pubdate`, `state`) VALUES ('$cityid', '$title', '$userid', '$typeid', '$addrid', '$address', '$tel', '$price', '$pics', '$video', '$note', '$bind_module', '$taoxi', '$anli', '$tag', '$pubdate', '0')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){

				//后台消息通知
				updateAdminNotice("marry", "store");
				updateCache("marry_store_list", 300);
				clearCache("marry_store_total", 'key');

				return $langData['marry'][5][7];//配置成功，您的商铺正在审核中，请耐心等待！
			}else{
				return array("state" => 200, "info" => $langData['marry'][5][8]);//配置失败，请查检您输入的信息是否符合要求！
			}
		}else{
			//保存到主表
			$archives = $dsql->SetQuery("UPDATE `#@__marry_store` SET `cityid` = '$cityid', `title` = '$title', `userid` = '$userid', `typeid` = '$typeid', `addrid` = '$addrid', `address` = '$address', `tel` = '$tel', `price` = '$price', `pics` = '$pics', `video` = '$video', `note` = '$note', `bind_module` = '$bind_module', `taoxi` = '$taoxi', `anli` = '$anli', `tag` = '$tag', `state` = '0' WHERE `userid` = ".$userid);
			$results = $dsql->dsqlOper($archives, "update");

			if($results == "ok"){
				// 检查缓存
				$id = $userResult[0]['id'];
				checkCache("marry_store_list", $id);
				clearCache("marry_store_total", 'key');
				clearCache("marry_store_detail", $id);

				return $langData['marry'][5][9];//保存成功！
			}else{
				return array("state" => 200, "info" => $langData['marry'][5][8]);//配置失败，请查检您输入的信息是否符合要求！
			}
		}

	}

	
	/**
	 * 操作婚宴场地
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operHotelfield(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/marry.inc.php");
		$custommarryhotelfieldCheck = (int)$custommarryhotelfieldCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$maxtable        =  (int)$param['maxtable'];
		$besttable       =  (int)$param['besttable'];
		$floorheight     =  (int)$param['floorheight'];
		$area     	     =  $param['area'];
		$column     	 =  (int)$param['column'] ? (int)$param['column'] : 0;
		$fields     	 =  (int)$param['fields'] ? (int)$param['fields'] : 0;
		$pics     	     =  $param['pics'];
		$pubdate         =  GetMkTime(time());

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
			return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `typeid`, `state` FROM `#@__marry_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['marry'][5][13]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['marry'][5][14]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['marry'][5][15]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title)) return array("state" => 200, "info" => $langData['marry'][4][9]);//请输入公司名称！
			if(empty($maxtable)) return array("state" => 200, "info" => $langData['marry'][4][19]);//请输入容纳桌数！
			if(empty($besttable)) return array("state" => 200, "info" => $langData['marry'][4][20]);//请输入最佳桌数！
			if(empty($floorheight)) return array("state" => 200, "info" => $langData['marry'][4][21]);//请输入层高！
			if(empty($area)) return array("state" => 200, "info" => $langData['marry'][4][22]);//请输入面积！
			if(empty($pics)) return array("state" => 200, "info" => $langData['marry'][4][8]);//请上传图片图片
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__marry_hotelfield` (`title`, `userid`, `company`, `pics`, `maxtable`, `besttable`, `floorheight`, `area`, `column`, `fields`, `pubdate`, `state`) VALUES ('$title', '$userid', '$company', '$pics', '$maxtable', '$besttable', '$floorheight', '$area', '$column', '$fields', '$pubdate', '$custommarryhotelfieldCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){
				if($custommarryhotelfieldCheck){
					updateCache("marry_hotelfield_list", 300);
				}
				clearCache("marry_hotelfield_total", 'key');
				//后台消息通知
				updateAdminNotice("marry", "hotelfield");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['marry']['5']['16']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__marry_hotelfield` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__marry_hotelfield` SET `title` = '$title', `userid` = '$userid', `company` = '$company', `pics` = '$pics', `maxtable` = '$maxtable', `besttable` = '$besttable', `floorheight` = '$floorheight', `area` = '$area', `column` = '$column', `fields` = '$fields', `state` = '$custommarryhotelfieldCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['marry']['5']['18']); //保存到数据时发生错误，请检查字段内容！
				}

				updateAdminNotice("marry", "hotelfield");

				// 清除缓存
				clearCache("marry_hotelfield_detail", $id);
				checkCache("marry_hotelfield_list", $id);
				clearCache("marry_hotelfield_total", 'key');
				

				return $langData['marry'][5][17];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`pics`, s.`userid` FROM `#@__marry_hotelfield` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					//删除图集
					delPicFile($results['pics'], "delAtlas", "marry");
					// 清除缓存
					clearCache("marry_hotelfield_detail", $id);
					checkCache("marry_hotelfield_list", $id);
					clearCache("marry_hotelfield_total", 'key');

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__marry_hotelfield` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['marry'][5][19]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['marry'][5][20]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}
	}

	/**
	 * 婚宴场地列表
	 * @return array
	 */
	public function hotelfieldList(){
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
				//$where .= " AND `cityid` = ".$cityid;

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `state` = 1 AND `cityid` = $cityid");
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
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
				return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `userid` = $uid");
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

		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "marry_type")){
				$lower = arr_foreach($dsql->getTypeList($typeid, "marry_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}

			$sidArr = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `typeid` in ($lower)");
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
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `addrid` in ($lower)");
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

			siteSearchLog("marry", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__marry_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
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

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `pics`, `maxtable`, `besttable`, `floorheight`, `area`, `column`, `fields`, `click`, `pubdate`, `state`  FROM `#@__marry_hotelfield` WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__marry_hotelfield` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("marry_hotelfield_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
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
		$results = getCache("marry_hotelfield_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['maxtable']  = $val['maxtable'];
				$list[$key]['besttable'] = $val['besttable'];
				$list[$key]['floorheight']= $val['floorheight'];
				$list[$key]['area']      = $val['area'];
				$list[$key]['column']    = $val['column'];
				$list[$key]['fields']    = $val['fields'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];

				$list[$key]["columnname"]= $val['column'] == 1 ? $langData['marry'][2][52] : $langData['marry'][2][53];//有 无
				$list[$key]["fieldsname"]= $val['fields'] == 1 ? $langData['marry'][2][57] : $langData['marry'][2][56];//长方形 正方形

				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
					$list[$key]['litpic'] = getFilePath($pics[0]);
				}else{
					$list[$key]['litpic']  = '';
				}

				$param = array(
					"service" => "marry",
					"template" => "hotelfield-detail",
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
     * 婚宴场地详细
     * @return array
     */
	public function hotelfieldDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
		}

		//$where = " AND `state` = 1";
		
		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `pics`, `maxtable`, `besttable`, `floorheight`, `area`, `column`, `fields`, `click`, `pubdate`, `state` FROM `#@__marry_hotelfield` WHERE `id` = ".$id.$where);
		$results  = getCache("marry_hotelfield_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail['maxtable']    = $results[0]['maxtable'];
			$storeDetail["besttable"]   = $results[0]['besttable'];
			$storeDetail["floorheight"] = $results[0]['floorheight'];
			$storeDetail["area"]        = $results[0]['area'];
			$storeDetail["column"]      = $results[0]['column'];
			$storeDetail["columnname"]  = $results[0]['column'] == 1 ? $langData['marry'][2][52] : $langData['marry'][2][53];//有 无
			$storeDetail["fields"]      = $results[0]['fields'];
			$storeDetail["fieldsname"]  = $results[0]['fields'] == 1 ? $langData['marry'][2][57] : $langData['marry'][2][56];//长方形 正方形
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];

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
				"service"  => "marry",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "marry",
				"temp"   => "hotelfield-detail",
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
	 * 操作婚宴菜单
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operHotelmenu(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/marry.inc.php");
		$custommarryhotelmenuCheck = (int)$custommarryhotelmenuCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$price     	     =  (float)$param['price'];
		if(isset($param['dishname'])){
		    $dishname = $param['dishname'];
		    $dishname = is_array($dishname) ? join("|", $dishname) : $dishname;
		}
		$pubdate         =  GetMkTime(time());

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
			return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `typeid`, `state` FROM `#@__marry_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['marry'][5][13]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['marry'][5][14]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['marry'][5][15]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title)) return array("state" => 200, "info" => $langData['marry'][5][32]);//请输入套餐名称！
			if(empty($price)) return array("state" => 200, "info" => $langData['marry'][5][33]);//请输入套餐价格！
			if(empty($dishname)) return array("state" => 200, "info" => $langData['marry'][5][34]);//请输入菜品名称！
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__marry_hotelmenu` (`title`, `userid`, `company`, `price`, `dishname`, `pubdate`, `state`) VALUES ('$title', '$userid', '$company', '$price', '$dishname', '$pubdate', '$custommarryhotelmenuCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){
				if($custommarryhotelmenuCheck){
					updateCache("marry_hotelmenu_list", 300);
					clearCache("marry_hotelmenu_total", 'key');
				}
				//后台消息通知
				updateAdminNotice("marry", "hotelmenu");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['marry']['5']['16']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__marry_hotelmenu` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__marry_hotelmenu` SET `title` = '$title', `userid` = '$userid', `company` = '$company', `price` = '$price', `dishname` = '$dishname', `state` = '$custommarryhotelmenuCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['marry']['5']['18']); //保存到数据时发生错误，请检查字段内容！
				}

				updateAdminNotice("marry", "hotelmenu");

				// 清除缓存
				clearCache("marry_hotelmenu_detail", $id);
				checkCache("marry_hotelmenu_list", $id);
				clearCache("marry_hotelmenu_total", 'key');
				

				return $langData['marry'][5][17];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, s.`userid` FROM `#@__marry_hotelmenu` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					// 清除缓存
					clearCache("marry_hotelmenu_detail", $id);
					checkCache("marry_hotelmenu_list", $id);
					clearCache("marry_hotelmenu_total", 'key');

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__marry_hotelmenu` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['marry'][5][19]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['marry'][5][20]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}
	}

	/**
	 * 婚宴菜单列表
	 * @return array
	 */
	public function hotelmenuList(){
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
				//$where .= " AND `cityid` = ".$cityid;

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `state` = 1 AND `cityid` = $cityid");
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
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
				return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `userid` = $uid");
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

		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "marry_type")){
				$lower = arr_foreach($dsql->getTypeList($typeid, "marry_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}

			$sidArr = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `typeid` in ($lower)");
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
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `addrid` in ($lower)");
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

			siteSearchLog("marry", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__marry_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
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

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `price`, `dishname`, `click`, `pubdate`, `state` FROM `#@__marry_hotelmenu` WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__marry_hotelmenu` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("marry_hotelmenu_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
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
		$results = getCache("marry_hotelmenu_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['price']     = $val['price'];
				$list[$key]['dishname']  = $val['dishname'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];

				$tagArr_ = $val['dishname'] ? explode('|', $val['dishname']) : array();
				$list[$key]["tagArr_"]   = $tagArr_;

				$param = array(
					"service" => "marry",
					"template" => "hotelmenu-detail",
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
     * 婚宴菜单详细
     * @return array
     */
	public function hotelmenuDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
		}

		//$where = " AND `state` = 1";
		
		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `price`, `dishname`, `click`, `pubdate`, `state` FROM `#@__marry_hotelmenu` WHERE `id` = ".$id.$where);
		$results  = getCache("marry_hotelmenu_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail['price']       = $results[0]['price'];
			$storeDetail["dishname"]    = $results[0]['dishname'];
			$tagArr_ = $results[0]['dishname'] ? explode('|', $results[0]['dishname']) : array();
			$storeDetail["tagArr_"]     = $tagArr_;
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];

			$lower = [];
			$param['id']    = $results[0]['company'];
			$this->param    = $param;
			$store          = $this->storeDetail();
			if(!empty($store)){
				$lower = $store;
			}
			$storeDetail['store'] = $lower;

			$param = array(
				"service"  => "marry",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "marry",
				"temp"   => "hotelmenu-detail",
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
	 * 操作主持人
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operHost(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/marry.inc.php");
		$custommarryhostCheck = (int)$custommarryhostCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$hostname        =  filterSensitiveWords(addslashes($param['hostname']));
		$price     	     =  (float)$param['price'];
		$tel             =  $param['tel'];
		$note            =  $param['note'];
		$photo           =  $param['photo'];
		$worksItem       =  $param['worksItem'];
		$pubdate         =  GetMkTime(time());

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
			return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `typeid`, `state` FROM `#@__marry_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['marry'][5][13]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['marry'][5][14]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['marry'][5][15]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($hostname)) return array("state" => 200, "info" => $langData['marry'][4][25]);//请输入姓名！
			if(empty($price)) return array("state" => 200, "info" => $langData['marry'][4][14]);//请输入价格！
			if(empty($tel)) return array("state" => 200, "info" => $langData['marry'][5][34]);//请输入手机号！
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__marry_host` (`hostname`, `userid`, `company`, `photo`, `tel`, `price`, `note`, `pubdate`, `state`) VALUES ('$hostname', '$userid', '$company', '$photo', '$tel', '$price', '$note', '$pubdate', '$custommarryhostCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){
				if(!empty($worksItem)){
					$worksItemArr = explode("|||", $worksItem);
					foreach($worksItemArr as $val){
						$workInfo = explode("$$$", $val);
						$worksql  = $dsql->SetQuery("INSERT INTO `#@__marry_hostvideo` (`title`, `litpic`, `hostid`, `video`, `pubdate`) VALUES ('$workInfo[0]', '$workInfo[1]', '$aid', '$workInfo[2]', '$pubdate')");
						$dsql->dsqlOper($worksql, "update");
					}
				}
				if($custommarryhostCheck){
					updateCache("marry_host_list", 300);
					clearCache("marry_host_total", 'key');
				}
				//后台消息通知
				updateAdminNotice("marry", "host");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['marry']['5']['16']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__marry_host` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__marry_host` SET `hostname` = '$hostname', `userid` = '$userid', `company` = '$company', `photo` = '$photo', `tel` = '$tel', `price` = '$price', `note` = '$note', `state` = '$custommarryhostCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['marry']['5']['18']); //保存到数据时发生错误，请检查字段内容！
				}

				updateAdminNotice("marry", "host");

				$archives = $dsql->SetQuery("DELETE FROM `#@__marry_hostvideo` WHERE `hostid` = ".$id);
				$dsql->dsqlOper($archives, "update");

				if(!empty($worksItem)){
					$worksItemArr = explode("|||", $worksItem);
					foreach($worksItemArr as $val){
						$workInfo = explode("$$$", $val);
						$worksql  = $dsql->SetQuery("INSERT INTO `#@__marry_hostvideo` (`title`, `litpic`, `hostid`, `video`, `pubdate`) VALUES ('$workInfo[0]', '$workInfo[1]', '$id', '$workInfo[2]', '$pubdate')");
						$dsql->dsqlOper($worksql, "update");
					}
				}

				// 清除缓存
				clearCache("marry_host_detail", $id);
				checkCache("marry_host_list", $id);
				clearCache("marry_host_total", 'key');

				return $langData['marry'][5][17];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`photo`, s.`userid` FROM `#@__marry_host` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					// 清除缓存
					clearCache("marry_host_detail", $id);
					checkCache("marry_host_list", $id);
					clearCache("marry_host_total", 'key');

					delPicFile($results['photo'], "delThumb", "marry");

					$sql = $dsql->SetQuery("SELECT `litpic`, `video` FROM `#@__marry_hostvideo` WHERE `hostid` = ".$id);
					$res = $dsql->dsqlOper($sql, "results");
					if($res){
						foreach($res as $v){
							delPicFile($v['litpic'], "delThumb", "marry");
							delPicFile($v['video'], "delVideo", "marry");
						}
					}

					$archives = $dsql->SetQuery("DELETE FROM `#@__marry_hostvideo` WHERE `hostid` = ".$id);
					$dsql->dsqlOper($archives, "update");

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__marry_host` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['marry'][5][19]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['marry'][5][20]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}
	}

	/**
	 * 主持人列表
	 * @return array
	 */
	public function hostList(){
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
				//$where .= " AND `cityid` = ".$cityid;

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `state` = 1 AND `cityid` = $cityid");
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
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
				return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `userid` = $uid");
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

		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "marry_type")){
				$lower = arr_foreach($dsql->getTypeList($typeid, "marry_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}

			$sidArr = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `typeid` in ($lower)");
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
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `addrid` in ($lower)");
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

			siteSearchLog("marry", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__marry_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
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

		$archives = $dsql->SetQuery("SELECT `id`, `hostname`, `userid`, `company`, `photo`, `tel`, `price`, `click`, `pubdate`, `state` FROM `#@__marry_host` WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__marry_host` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("marry_host_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
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
		$results = getCache("marry_host_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['hostname']  = $val['hostname'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['price']     = $val['price'];
				$list[$key]['tel']       = $val['tel'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['photo']     = $val['photo'];
				$list[$key]['photoSource']= $val['photo'] ? getFilePath($val['photo']) : '';

				$workArr = [];
				$sql = $dsql->SetQuery("SELECT `id`, `title`, `litpic` FROM `#@__marry_hostvideo` WHERE `hostid` = '".$val['id']."' ORDER BY  `weight` DESC, `pubdate` DESC, `id` DESC limit 0,3");
				$res = $dsql->dsqlOper($sql, "results");
				if(!empty($res)){
					foreach($res as $k=> $v){
						$workArr[$k]['id']           = $v['id'];
						$workArr[$k]['title']        = $v['title'];
						$workArr[$k]['litpic']       = $v['litpic'];
						$workArr[$k]['litpicSource'] = $v['litpic'] ? getFilePath($v['litpic']) : '';
					}
				}
				$list[$key]["workArr"]     = $workArr;

				$param = array(
					"service" => "marry",
					"template" => "host-detail",
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
     * 主持人详细
     * @return array
     */
	public function hostDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
		}

		//$where = " AND `state` = 1";
		
		$archives = $dsql->SetQuery("SELECT `id`, `hostname`, `userid`, `company`, `price`, `tel`, `photo`, `note`, `click`, `pubdate`, `state` FROM `#@__marry_host` WHERE `id` = ".$id.$where);
		$results  = getCache("marry_host_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["hostname"]    = $results[0]['hostname'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail['price']       = $results[0]['price'];
			$storeDetail["tel"]         = $results[0]['tel'];
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];
			$storeDetail["note"]        = $results[0]['note'];
			$storeDetail["photo"]       = $results[0]['photo'];
			$storeDetail["photoSource"] = $results[0]['photo'] ? getFilePath($results[0]['photo']) : '';

			$workArr = [];
			$sql = $dsql->SetQuery("SELECT `id`, `title`, `litpic`, `video` FROM `#@__marry_hostvideo` WHERE `hostid` = '$id' ORDER BY  `weight` DESC, `pubdate` DESC, `id` DESC");
			$res = $dsql->dsqlOper($sql, "results");
			if(!empty($res)){
				foreach($res as $key=> $val){
					$workArr[$key]['id']           = $val['id'];
					$workArr[$key]['title']        = $val['title'];
					$workArr[$key]['litpic']       = $val['litpic'];
					$workArr[$key]['litpicSource'] = $val['litpic'] ? getFilePath($val['litpic']) : '';
					$workArr[$key]['video']        = $val['video'];
					$workArr[$key]['videoSource']  = $val['video'] ? getFilePath($val['video']) : '';
				}
			}
			$storeDetail["workArr"]     = $workArr;

			$lower = [];
			$param['id']    = $results[0]['company'];
			$this->param    = $param;
			$store          = $this->storeDetail();
			if(!empty($store)){
				$lower = $store;
			}
			$storeDetail['store'] = $lower;

			$param = array(
				"service"  => "marry",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "marry",
				"temp"   => "host-detail",
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
	 * 操作婚车
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operRental(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/marry.inc.php");
		$customweddingcarCheck = (int)$customweddingcarCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$price     	     =  (float)$param['price'];
		$pics            =  $param['pics'];
		$duration        =  $param['duration'];
		$kilometre       =  $param['kilometre'];
		$pubdate         =  GetMkTime(time());

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
			return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `typeid`, `state` FROM `#@__marry_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['marry'][5][13]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['marry'][5][14]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['marry'][5][15]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title)) return array("state" => 200, "info" => $langData['marry'][4][30]);//请输入名称！
			if(empty($price)) return array("state" => 200, "info" => $langData['marry'][4][14]);//请输入价格！
			if(empty($duration) || empty($kilometre)) return array("state" => 200, "info" => $langData['marry'][4][31]);//请输入时长！
			if(empty($pics)) return array("state" => 200, "info" => $langData['marry'][4][8]);//请至少上传一张图片！
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__marry_weddingcar` (`title`, `userid`, `company`, `pics`, `price`, `duration`, `kilometre`, `pubdate`, `state`) VALUES ('$title', '$userid', '$company', '$pics', '$price', '$duration', '$kilometre', '$pubdate', '$customweddingcarCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){
				if($customweddingcarCheck){
					updateCache("marry_weddingcar_list", 300);
					clearCache("marry_weddingcar_total", 'key');
				}
				//后台消息通知
				updateAdminNotice("marry", "weddingcar");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['marry']['5']['16']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__marry_weddingcar` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__marry_weddingcar` SET `title` = '$title', `userid` = '$userid', `company` = '$company', `pics` = '$pics', `price` = '$price', `duration` = '$duration', `kilometre` = '$kilometre', `state` = '$customweddingcarCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['marry']['5']['18']); //保存到数据时发生错误，请检查字段内容！
				}

				updateAdminNotice("marry", "weddingcar");

				// 清除缓存
				clearCache("marry_weddingcar_detail", $id);
				checkCache("marry_weddingcar_list", $id);
				clearCache("marry_weddingcar_total", 'key');

				return $langData['marry'][5][17];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`pics`, s.`userid` FROM `#@__marry_weddingcar` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					// 清除缓存
					clearCache("marry_weddingcar_detail", $id);
					checkCache("marry_weddingcar_list", $id);
					clearCache("marry_weddingcar_total", 'key');

					delPicFile($results['pics'], "delAtlas", "marry");

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__marry_weddingcar` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['marry'][5][19]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['marry'][5][20]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}
	}

	/**
	 * 婚车列表
	 * @return array
	 */
	public function rentalList(){
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
				//$where .= " AND `cityid` = ".$cityid;

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `state` = 1 AND `cityid` = $cityid");
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
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
				return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `userid` = $uid");
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

		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "marry_type")){
				$lower = arr_foreach($dsql->getTypeList($typeid, "marry_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}

			$sidArr = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `typeid` in ($lower)");
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
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `addrid` in ($lower)");
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

			siteSearchLog("marry", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__marry_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
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

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `pics`, `price`, `duration`, `kilometre`, `click`, `pubdate`, `state` FROM `#@__marry_weddingcar` WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__marry_weddingcar` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("marry_weddingcar_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
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
		$results = getCache("marry_weddingcar_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['price']     = $val['price'];
				$list[$key]['pics']      = $val['pics'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['duration']  = $val['duration'];
				$list[$key]['kilometre'] = $val['kilometre'];

				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
				}
				$list[$key]['litpic'] = $pics[0] ? getFilePath($pics[0]) : '';

				$param = array(
					"service" => "marry",
					"template" => "rental-detail",
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
     * 婚车详细
     * @return array
     */
	public function rentalDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
		}

		//$where = " AND `state` = 1";
		
		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `price`, `pics`, `kilometre`, `duration`, `click`, `pubdate`, `state` FROM `#@__marry_weddingcar` WHERE `id` = ".$id.$where);
		$results  = getCache("marry_weddingcar_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail['price']       = $results[0]['price'];
			$storeDetail["pics"]        = $results[0]['pics'];
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];
			$storeDetail["duration"]    = $results[0]['duration'];
			$storeDetail["kilometre"]   = $results[0]['kilometre'];

			// $archives = $dsql->SetQuery("SELECT `id` FROM `#@__marrycommon` WHERE `aid` = ".$results[0]['id']." AND `type` = 1 AND `ischeck` = 1 AND `floor` = 0");
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'marry-rental' AND `aid` = '$id' AND `pid` = 0");
			$totalCount = $dsql->dsqlOper($archives, "totalCount");
            $storeDetail['common'] = $totalCount;

			$lower = [];
			$param['id']    = $results[0]['company'];
			$this->param    = $param;
			$store          = $this->storeDetail();
			if(!empty($store)){
				$lower = $store;
			}
			$storeDetail['store'] = $lower;

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

			$param = array(
				"service"  => "marry",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "marry",
				"temp"   => "rental-detail",
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
	 * 操作案例
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operPlancase(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/marry.inc.php");
		$custommarryplancaseCheck = (int)$custommarryplancaseCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$holdingtime     =  GetMkTime($param['holdingtime']);
		$pics            =  $param['pics'];
		$hoteltitle      =  $param['hoteltitle'];
		$pubdate         =  GetMkTime(time());

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
			return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `typeid`, `state` FROM `#@__marry_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['marry'][5][13]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['marry'][5][14]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['marry'][5][15]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title)) return array("state" => 200, "info" => $langData['marry'][4][16]);//请输入标题！
			if(empty($holdingtime)) return array("state" => 200, "info" => $langData['marry'][4][17]);//请选择日期！
			if(empty($hoteltitle)) return array("state" => 200, "info" => $langData['marry'][4][31]);//请输入酒店名称！
			if(empty($pics)) return array("state" => 200, "info" => $langData['marry'][4][18]);//请至少上传一张图片！
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__marry_plancase` (`title`, `userid`, `company`, `pics`, `holdingtime`, `hoteltitle`, `pubdate`, `state`) VALUES ('$title', '$userid', '$company', '$pics', '$holdingtime', '$hoteltitle', '$pubdate', '$custommarryplancaseCheck')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){
				if($custommarryplancaseCheck){
					updateCache("marry_plancase_list", 300);
					clearCache("marry_plancase_total", 'key');
				}
				//后台消息通知
				updateAdminNotice("marry", "plancase");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['marry']['5']['16']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__marry_plancase` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__marry_plancase` SET `title` = '$title', `userid` = '$userid', `company` = '$company', `pics` = '$pics', `holdingtime` = '$holdingtime', `hoteltitle` = '$hoteltitle', `state` = '$custommarryplancaseCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['marry']['5']['18']); //保存到数据时发生错误，请检查字段内容！
				}

				updateAdminNotice("marry", "plancase");

				// 清除缓存
				clearCache("marry_plancase_detail", $id);
				checkCache("marry_plancase_list", $id);
				clearCache("marry_plancase_total", 'key');

				return $langData['marry'][5][17];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`pics`, s.`userid` FROM `#@__marry_plancase` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					// 清除缓存
					clearCache("marry_plancase_detail", $id);
					checkCache("marry_plancase_list", $id);
					clearCache("marry_plancase_total", 'key');

					delPicFile($results['pics'], "delAtlas", "marry");

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__marry_plancase` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['marry'][5][19]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['marry'][5][20]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}
	}

	/**
	 * 案例列表
	 * @return array
	 */
	public function plancaseList(){
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
				//$where .= " AND `cityid` = ".$cityid;

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `state` = 1 AND `cityid` = $cityid");
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
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
				return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `userid` = $uid");
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

		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "marry_type")){
				$lower = arr_foreach($dsql->getTypeList($typeid, "marry_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}

			$sidArr = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `typeid` in ($lower)");
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
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `addrid` in ($lower)");
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

			siteSearchLog("marry", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__marry_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
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

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `pics`, `holdingtime`, `hoteltitle`, `click`, `pubdate`, `state` FROM `#@__marry_plancase` WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__marry_plancase` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("marry_plancase_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
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
		$results = getCache("marry_plancase_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['hoteltitle']= $val['hoteltitle'];
				$list[$key]['pics']      = $val['pics'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['holdingtime']= $val['holdingtime'];
				$list[$key]['holdingtimeSource']= $val['holdingtime'] ? date("Y-m-d", $val['holdingtime']) : '';
				$list[$key]['holdingtimeSource1']= $val['holdingtime'] ? date("m月d日", $val['holdingtime']) : '';

				$pics = $val['pics'];
				if(!empty($pics)){
					$pics = explode(",", $pics);
				}
				$list[$key]['litpic'] = $pics[0] ? getFilePath($pics[0]) : '';

				$param = array(
					"service" => "marry",
					"template" => "plancase-detail",
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
     * 案例详细
     * @return array
     */
	public function plancaseDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
		}

		//$where = " AND `state` = 1";
		
		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `holdingtime`, `pics`, `hoteltitle`, `click`, `pubdate`, `state` FROM `#@__marry_plancase` WHERE `id` = ".$id.$where);
		$results  = getCache("marry_plancase_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail["pics"]        = $results[0]['pics'];
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];
			$storeDetail["holdingtime"] = $results[0]['holdingtime'];
			$storeDetail["holdingtimeSource"] = $results[0]['holdingtime'] ? date("Y-m-d", $results[0]['holdingtime']) : '';
			$storeDetail["hoteltitle"]  = $results[0]['hoteltitle'];

			$lower = [];
			$param['id']    = $results[0]['company'];
			$this->param    = $param;
			$store          = $this->storeDetail();
			if(!empty($store)){
				$lower = $store;
			}
			$storeDetail['store'] = $lower;

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

			$param = array(
				"service"  => "marry",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "marry",
				"temp"   => "plancase-detail",
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
	 * 操作套餐
	 * oper=add: 增加
	 * oper=del: 删除
	 * oper=update: 更新
	 * @return array
	 */
	public function operPlanmeal(){
		global $dsql;
		global $userLogin;
		global $langData;

		require(HUONIAOINC."/config/marry.inc.php");
		$custommarryplanmealCheck = (int)$custommarryplanmealCheck;

		$userid      = $userLogin->getMemberID();
		$userinfo    = $userLogin->getMemberInfo();
		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);//登录超时，请重新登录！
		}

		$param           =  $this->param;
		$id              =  $param['id'];
		$oper            =  $param['oper'];
		$title           =  filterSensitiveWords(addslashes($param['title']));
		$price           =  (float)$param['price'];
		$pics            =  $param['pics'];
		$typeid          =  $param['typeid'] ? $param['typeid'] : 0;
		if(isset($param['tag'])){
		    $tag = $param['tag'];
		    $tag = is_array($tag) ? join("|", $tag) : $tag;
		}
		$pubdate         =  GetMkTime(time());

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
			return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `typeid`, `state` FROM `#@__marry_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['marry'][5][13]);//您还未开通婚嫁公司！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['marry'][5][14]);//您的公司信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['marry'][5][15]);//您的公司信息审核失败，请通过审核后再发布！
		}

		if($oper == 'add' || $oper == 'update'){
			if(empty($title)) return array("state" => 200, "info" => $langData['marry'][4][16]);//请输入标题！
			if(empty($price)) return array("state" => 200, "info" => $langData['marry'][4][14]);//请输入价格！
			if(empty($pics)) return array("state" => 200, "info" => $langData['marry'][4][18]);//请至少上传一张图片！
		}elseif($oper == 'del'){
			if(!is_numeric($id)) return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误！
		}

		$company = $userResult[0]['id'];

		if($oper == 'add'){
			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__marry_planmeal` (`title`, `userid`, `company`, `pics`, `type`, `tag`, `pubdate`, `state`, `price`) VALUES ('$title', '$userid', '$company', '$pics', '$typeid', '$tag', '$pubdate', '$custommarryplanmealCheck', '$price')");
			$aid = $dsql->dsqlOper($archives, "lastid");
			if(is_numeric($aid)){
				if($custommarryplanmealCheck){
					updateCache("marry_planmeal_list", 300);
					clearCache("marry_planmeal_total", 'key');
				}
				//后台消息通知
				updateAdminNotice("marry", "planmeal");

				return $aid;
			}else{
				return array("state" => 101, "info" => $langData['marry']['5']['16']);//发布到数据时发生错误，请检查字段内容！
			}
		}elseif($oper == 'update'){
			$archives = $dsql->SetQuery("SELECT l.`id` FROM `#@__marry_planmeal` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				//保存到主表
				$archives = $dsql->SetQuery("UPDATE `#@__marry_planmeal` SET `title` = '$title', `userid` = '$userid', `company` = '$company', `pics` = '$pics', `type` = '$typeid', `price` = '$price', `tag` = '$tag', `state` = '$custommarryplanmealCheck' WHERE `id` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");
				if($results != "ok"){
					return array("state" => 200, "info" => $langData['marry']['5']['18']); //保存到数据时发生错误，请检查字段内容！
				}

				updateAdminNotice("marry", "planmeal");

				// 清除缓存
				clearCache("marry_planmeal_detail", $id);
				checkCache("marry_planmeal_list", $id);
				clearCache("marry_planmeal_total", 'key');

				return $langData['marry'][5][17];//修改成功！
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}elseif($oper == 'del'){
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`pics`, s.`userid` FROM `#@__marry_planmeal` l LEFT JOIN `#@__marry_store` s ON s.`id` = l.`company` WHERE l.`id` = ".$id);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$results = $results[0];
				if($results['userid'] == $userid){
					// 清除缓存
					clearCache("marry_planmeal_detail", $id);
					checkCache("marry_planmeal_list", $id);
					clearCache("marry_planmeal_total", 'key');

					delPicFile($results['pics'], "delAtlas", "marry");

					//删除表
					$archives = $dsql->SetQuery("DELETE FROM `#@__marry_planmeal` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					return array("state" => 100, "info" => $langData['marry'][5][19]);//删除成功！
				}else{
					return array("state" => 101, "info" => $langData['marry'][5][20]);//权限不足，请确认帐户信息后再进行操作！
				}
			}else{
				return array("state" => 101, "info" => $langData['marry'][5][21]);//信息不存在，或已经删除！
			}
		}
	}

	/**
	 * 套餐列表
	 * @return array
	 */
	public function planmealList(){
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
				$type     = (int)$this->param['type'];
				$type     = $type ? (int)$type : 0;
				$istype   = (int)$this->param['istype'];
				$businessid = (int)$this->param['businessid'];
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
				//$where .= " AND `cityid` = ".$cityid;

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `state` = 1 AND `cityid` = $cityid");
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
			if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "marry"))){
				return array("state" => 200, "info" => $langData['marry'][5][12]);//商家权限验证失败
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `userid` = $uid");
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

		if(is_numeric($type) && $type >= 0){
			$where .= " AND `type` = '$type'";
		}

		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "marry_type")){
				$lower = arr_foreach($dsql->getTypeList($typeid, "marry_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}

			$sidArr = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `typeid` in ($lower)");
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
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE `addrid` in ($lower)");
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

			siteSearchLog("marry", $search);

			$sidArr = array();
	        $userSql = $dsql->SetQuery("SELECT `store`.id FROM `#@__marry_store` store LEFT JOIN `#@__member` user ON `user`.id = `store`.userid WHERE `store`.title like '%$search%' OR `store`.address like '%$search%'");
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

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `pics`, `tag`, `price`, `type`, `click`, `pubdate`, `state` FROM `#@__marry_planmeal` WHERE 1 = 1".$where);
		//总条数
		$arc = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__marry_planmeal` WHERE 1 = 1".$where);
		//总条数
		$totalCount = getCache("marry_planmeal_total", $arc, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));
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
		$results = getCache("marry_planmeal_list", $sql, 300, array("disabled" => $u));
		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']        = $val['id'];
				$list[$key]['title']     = $val['title'];
				$list[$key]['userid']    = $val['userid'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['tag']       = $val['tag'];
				$list[$key]['pics']      = $val['pics'];
				$list[$key]['click']     = $val['click'];
				$list[$key]['pubdate']   = $val['pubdate'];
				$list[$key]['state']     = $val['state'];
				$list[$key]['price']     = $val['price'];

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
				}
				$list[$key]['litpic'] = $pics[0] ? getFilePath($pics[0]) : '';

				$param = array(
					"service" => "marry",
					"template" => "planmeal-detail",
					"id" => $val['id'],
					"typeid" => $type,
					"istype" => $istype,
					"businessid" => $businessid
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
     * 套餐详细
     * @return array
     */
	public function planmealDetail(){
		global $dsql;
		global $langData;
		global $userLogin;
		$storeDetail = array();
		$id = $this->param;
		$typeid = isset($id['typeid']) ? $id['typeid'] : 0;
		$istype = isset($id['istype']) ? $id['istype'] : 0;
		$businessid = isset($id['businessid']) ? $id['businessid'] : 0;
		$id = is_numeric($id) ? $id : $id['id'];
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['marry'][5][10]);//格式错误
		}

		//$where = " AND `state` = 1";
		
		$archives = $dsql->SetQuery("SELECT `id`, `title`, `userid`, `company`, `price`, `pics`, `tag`, `type`, `click`, `pubdate`, `state` FROM `#@__marry_planmeal` WHERE `id` = ".$id.$where);
		$results  = getCache("marry_planmeal_detail", $archives, 0, $id);
		if($results){
			$storeDetail["id"]          = $results[0]['id'];
			$storeDetail["title"]       = $results[0]['title'];
			$storeDetail['userid']      = $results[0]['userid'];
			$storeDetail['company']     = $results[0]['company'];
			$storeDetail["pics"]        = $results[0]['pics'];
			$storeDetail["click"]       = $results[0]['click'];
			$storeDetail["pubdate"]     = $results[0]['pubdate'];
			$storeDetail["state"]       = $results[0]['state'];
			$storeDetail["price"]       = $results[0]['price'];
			$storeDetail["tag"]         = $results[0]['tag'];
			$storeDetail["type"]        = $results[0]['type'];

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

			$lower = [];
			$param['id']    = $results[0]['company'];
			$this->param    = $param;
			$store          = $this->storeDetail();
			if(!empty($store)){
				$lower = $store;
			}
			$storeDetail['store'] = $lower;

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

			$param = array(
				"service"  => "marry",
				"template" => "store-detail",
				"id"       => $results[0]['company']
			);
			$storeDetail['companyurl'] = getUrlPath($param);

			//验证是否已经收藏
			$collect = '';
			if($uid != -1){
				$params = array(
					"module" => "marry",
					"temp"   => "planmeal-detail" . "|" . $typeid . "|" . $istype . "|" . $businessid,
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

        if(empty($fid)) return array("state" => 200, "info" => $langData['marry'][5][44]);//参数错误

        $pageSize = empty($pageSize) ? 99999 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if($fid){
            $where = " AND `floor` = '$fid'";
        }

        $where .= " AND `ischeck` = 1";

        $archives = $dsql->SetQuery("SELECT `id` FROM `#@__marrycommon` WHERE 1 = 1".$where);
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

        $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__marrycommon` WHERE 1 = 1".$where);

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
			return array("state" => 200, "info" => $langData['marry'][5][44]);//格式错误！
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

		$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__marrycommon` WHERE `aid` = ".$newsid." $where AND `ischeck` = 1 AND `floor` = 0".$oby);//print_R($archives);exit;
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

		$id = $this->param['id'];
		if(empty($id)) return "请传递评论ID！";
		$memberID = $userLogin->getMemberID();
		if($memberID == -1 || empty($memberID)) return $langData['siteConfig'][20][262];//登录超时，请重新登录！

		$archives = $dsql->SetQuery("SELECT `duser` FROM `#@__marrycommon` WHERE `id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){

			$duser = $results[0]['duser'];

			//如果此会员已经顶过则return
			$userArr = explode(",", $duser);
			if(in_array($userLogin->getMemberID(), $userArr)) return $langData['marry'][5][45];//已顶过！

			//附加会员ID
			if(empty($duser)){
				$nuser = $userLogin->getMemberID();
			}else{
				$nuser = $duser . "," . $userLogin->getMemberID();
			}

			$archives = $dsql->SetQuery("UPDATE `#@__marrycommon` SET `good` = `good` + 1 WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");

			$archives = $dsql->SetQuery("UPDATE `#@__marrycommon` SET `duser` = '$nuser' WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");
			return $results;

		}else{
			return $langData['marry'][5][46];//评论不存在或已删除！
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
			return array("state" => 200, "info" => $langData['marry'][5][47]);//必填项不得为空！
		}

		$content = filterSensitiveWords(cn_substrR($content,250));

		include HUONIAOINC."/config/marry.inc.php";
		$ischeck = (int)$customCommentCheck;

        $userid = $userLogin->getMemberID();

		$archives = $dsql->SetQuery("INSERT INTO `#@__marrycommon` (`aid`, `floor`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `ischeck`, `duser`, `type`) VALUES ('$aid', '$id', '".$userid."', '$content', ".GetMkTime(time()).", '".GetIP()."', '".getIpAddr(GetIP())."', 0, 0, '$ischeck', '', '$type')");
		$lid  = $dsql->dsqlOper($archives, "lastid");
		if($lid){

			$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__marrycommon` WHERE `id` = ".$lid);
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
			return array("state" => 200, "info" => $langData['marry'][5][48]);//评论失败！
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

        $sql = $dsql->SetQuery("SELECT * FROM `#@__marrycommon` WHERE `id` = $id AND `isCheck` = 1 ");//print_R($sql);exit;
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
                        $sql = $dsql->SetQuery("SELECT `content`, `userid` FROM `#@__marrycommon` WHERE `id` = '$value' AND `isCheck` = 1 ");
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
	 * 分类 2019-04-23
	 */
	public function module_type(){
		global $langData;
		$typeList = array();
        $typeList[] = array('id' => 1, 'typename' => $langData['marry'][2][13], 'lower' => array());//婚宴酒店
		$typeList[] = array('id' => 2, 'typename' => $langData['marry'][2][14], 'lower' => array());//婚礼策划
		$typeList[] = array('id' => 3, 'typename' => $langData['marry'][2][15], 'lower' => array());//租婚车
		$typeList[] = array('id' => 4, 'typename' => $langData['marry'][2][6], 'lower' => array());//婚纱摄影
		$typeList[] = array('id' => 5, 'typename' => $langData['marry'][2][7], 'lower' => array());//摄影跟拍
		$typeList[] = array('id' => 6, 'typename' => $langData['marry'][2][16], 'lower' => array());//婚礼主持
		$typeList[] = array('id' => 7, 'typename' => $langData['marry'][2][8], 'lower' => array());//珠宝首饰
		$typeList[] = array('id' => 8, 'typename' => $langData['marry'][2][9], 'lower' => array());//摄像跟拍
		$typeList[] = array('id' => 9, 'typename' => $langData['marry'][2][10], 'lower' => array());//新娘跟妆
		$typeList[] = array('id' => 10, 'typename' => $langData['marry'][2][11], 'lower' => array());//婚纱礼服
        return $typeList;
	}

	public function gettypename($fun, $id){
        $list = $this->$fun();
        return $list[array_search($id, array_column($list, "id"))]['typename'];
    }


}
