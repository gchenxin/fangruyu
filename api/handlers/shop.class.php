<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 商城模块API接口
 *
 * @version        $Id: shop.class.php 2014-3-23 上午09:25:10 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class shop {
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
     * 商城基本参数
     * @return array
     */
	public function config(){

		require(HUONIAOINC."/config/shop.inc.php");

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

		$hotline = $hotline_config == 0 ? $cfg_hotline : $customHotline;

		$params = !empty($this->param) && !is_array($this->param) ? explode(',',$this->param) : "";

		// $domainInfo = getDomain('shop', 'config');
		// $customChannelDomain = $domainInfo['domain'];
		// if($customSubDomain == 0){
		// 	$customChannelDomain = "http://".$customChannelDomain;
		// }elseif($customSubDomain == 1){
		// 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
		// }elseif($customSubDomain == 2){
		// 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
		// }

		// include HUONIAOINC.'/siteModuleDomain.inc.php';
		$customChannelDomain = getDomainFullUrl('shop', $customSubDomain);

        //分站自定义配置
        $ser = 'shop';
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
     * 商城地区
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
	 * 热门关键词
	 * @reseturn array
	 */
	public function hotKeywords(){
		global $dsql;

		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_hotkeyword` WHERE `state` = 0 ORDER BY `weight` DESC, `id` DESC");
		$results = $dsql->dsqlOper($archives, "results");

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']      = $val['id'];
				$list[$key]['keyword'] = $val['keyword'];
				$list[$key]['href']    = $val['href'];
				$list[$key]['color']   = $val['color'];
				$list[$key]['target']  = $val['target'] == 0 ? "_blank" : "_self";
				$list[$key]['blod']    = $val['blod'];
			}
		}

		return $list;
	}


	/**
     * 商城公告
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

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `color`, `redirecturl`, `litpic` FROM `#@__shop_noticelist` WHERE `arcrank` = 0 $where ORDER BY `weight` DESC, `id` DESC");
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
			"service"     => "shop",
			"template"    => "notice",
			"id"          => "%id%"
		);
		$urlParam = getUrlPath($param);

		foreach ($results as $key => $val) {
			$list[$key]['title'] = $val['title'];
			$list[$key]['color'] = $val['color'];
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

		$archives = $dsql->SetQuery("SELECT `title`, `color`, `redirecturl`, `litpic`, `body`, `pubdate` FROM `#@__shop_noticelist` WHERE `arcrank` = 0 AND `id` = ".$this->param);
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


	/**
     * 商城资讯
     * @return array
     */
	public function news(){
		global $dsql;
		global $langData;
		$pageinfo = $list = array();
		$typeid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$typeid   = $this->param['typeid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$cityid = getCityId($this->param['cityid']);

        //全国情况，不进行区域筛选
        global $siteCityInfo;
        $siteCityInfoName = $siteCityInfo['name'];
		if($cityid && $siteCityInfoName != '全国'){
			$addrList = $dsql->getTypeList($cityid, "site_area");
			if($addrList){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($addrList);
				$lower = $cityid.",".join(',',$lower);
			}else{
				$lower = $cityid;
			}
			$where .= " AND `cityid` in ($lower)";
		}

		//遍历分类
		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "shop_news_type")){
				$lower = arr_foreach($dsql->getTypeList($typeid, "shop_news_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}
			$where .= " AND `typeid` in ($lower)";
		}

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `litpic`, `body`, `typeid`, `pubdate` FROM `#@__shop_news` WHERE `arcrank` = 0".$where." ORDER BY `weight` DESC, `id` DESC");
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
		$list = array();

		$param = array(
			"service"     => "shop",
			"template"    => "news-detail",
			"id"          => "%id%"
		);
		$urlParam = getUrlPath($param);

		foreach ($results as $key => $val) {
			$list[$key]['id']      = $val['id'];
			$list[$key]['title']   = $val['title'];
			$list[$key]['litpic']  = getFilePath($val['litpic']);
			$list[$key]['pubdate'] = $val['pubdate'];
			$list[$key]['url'] = str_replace("%id%", $val['id'], $urlParam);
			$list[$key]['note'] = mb_substr(strip_tags($val['body']), 0, 100);

			$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__shop_news_type` WHERE `id` = ".$val['typeid']);
			$typename = $dsql->dsqlOper($archives, "results");
			if($typename){
				$list[$key]['typename']   = $typename[0]['typename'];
			}else{
				$list[$key]['typename']   = "";
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 资讯详细信息
     * @return array
     */
	public function newsDetail(){
		global $dsql;
		$detail = array();

		if(empty($this->param)){
			return array("state" => 200, "info" => '信息ID不得为空！');
		}

		if(!is_numeric($this->param)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT `id`, `cityid`, `title`, `typeid`, `litpic`, `body`, `pubdate` FROM `#@__shop_news` WHERE `arcrank` = 0 AND `id` = ".$this->param);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$detail["id"]          = $results[0]['id'];
			$detail["title"]       = $results[0]['title'];
			$detail["typeid"]      = $results[0]['typeid'];
			$detail["cityid"]      = $results[0]['cityid'];

			$typename = "";
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__shop_news_type` WHERE `id` = ".$results[0]['typeid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$typename = $ret[0]['typename'];
			}
			$detail['typename']    = $typename;

			$detail["litpic"]      = getFilePath($results[0]['litpic']);
			$detail["body"]        = $results[0]['body'];
			$detail["pubdate"]     = $results[0]['pubdate'];
		}
		return $detail;
	}


	/**
     * 资讯分类
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

		$results = $dsql->getTypeList($type, "shop_news_type", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 商城分类
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

		$results = $dsql->getTypeList($type, "shop_type", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 商城分类-发布商品时使用
     * @return array
     */
	public function getTypeList(){
		global $dsql;
		$tid = (int)$this->param['tid'];

		$list = array();
		if($tid == 0){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `parentid` = 0 ORDER BY `weight`");
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach($results as $key => $val){
					$list[$key] = array();
					$list_1 = array();
					$archives_1 = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `parentid` = ".$val['id']." ORDER BY `weight`");
					$results_1 = $dsql->dsqlOper($archives_1, "results");
					if($results_1){
						foreach($results_1 as $key_1 => $val_1){
							$list_1[$key_1]["id"] = $val_1['id'];
							$list_1[$key_1]["typename"] = $val_1['typename'];

							$list_1[$key_1]["type"] = 0;
							$archives_2 = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `parentid` = ".$val_1['id']." ORDER BY `weight`");
							$results_2 = $dsql->dsqlOper($archives_2, "results");
							if($results_2){
								$list_1[$key_1]["type"] = 1;
							}
						}
					}
					if(!empty($list_1)){
						$list[$key]["typeid"] = $val['id'];
						$list[$key]["typename"] = $val['typename'];
						$list[$key]["subnav"] = $list_1;
					}
				}
			}
		}else{
			$list = array();
			$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `parentid` = ".$tid." ORDER BY `weight`");
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach($results as $key => $val){
					$list[$key]["id"] = $val['id'];
					$list[$key]["typename"] = $val['typename'];

					$list[$key]["type"] = 0;
					$archives_1 = $dsql->SetQuery("SELECT * FROM `#@__shop_type` WHERE `parentid` = ".$val['id']." ORDER BY `weight`");
					$results_1 = $dsql->dsqlOper($archives_1, "results");
					if($results_1){
						$list[$key]["type"] = 1;
					}
				}
			}
		}
		if(!empty($list)){
			return $list;
		}else{
			echo '{"state": 200, "info": "获取失败！"}';
		}
		die;


	}


	/**
     * 商城分类-发布商品时使用
     * @return array
     */
	public function typeParent(){
		$typeid = (int)$this->param['typeid'];

		$proTypeName = getParentArr("shop_type", $typeid);
		$proTypeName = array_reverse(parent_foreach($proTypeName, "typename"));

		//遍历所选分类ID
		$proId = array_reverse(parent_foreach(getParentArr("shop_type", $typeid), "id"));
		$proId = array_slice($proId, 0, count($proTypeName));
		if(!empty($proId)){
			return $proId;
		}
		die;

	}



	/**
     * 分类字段
     * @return array
     */
	public function typeItem(){
		global $dsql;

		$typeid = $this->param['typeid'];
		if(!is_numeric($typeid)) return array("state" => 200, "info" => '格式错误！');

		//遍历所选分类ID
		global $data;
		$data = "";
		$parentArr = getParentArr("shop_type", $typeid);
		if(empty($parentArr)) return;

		$proId = array_reverse(parent_foreach($parentArr, "id"));
		foreach($proId as $key => $val){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_item` WHERE `type` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$typeid = $val;
			}
		}

		//获取分类属性
		$proItemList = array();
		if($typeid != 0){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_item` WHERE `type` = ".$typeid." AND `parentid` = 0 ORDER BY `weight`");
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach($results as $key => $val){

					$id = $val['id'];
					$typeName = $val['typename'];
					$w = strstr($val['flag'], 'w');

					$archives_ = $dsql->SetQuery("SELECT * FROM `#@__shop_item` WHERE `parentid` = ".$val['id']." ORDER BY `weight`");
					$results_ = $dsql->dsqlOper($archives_, "results");

					if($results_){
						$listItem = array();
						foreach($results_ as $key_ => $val_){
							if($w){
								$listItem[$key_]['id'] = $val_['typename'];
							}else{
								$listItem[$key_]['id'] = $val_['id'];
							}
							$listItem[$key_]['val'] = $val_['typename'];
						}
					}

					$proItemList[$key]['id'] = $id;
					$proItemList[$key]['typeName'] = $typeName;
					$proItemList[$key]['listItem'] = $listItem;
				}
			}
		}
		return $proItemList;
	}


	/**
     * 分类规格
     * @return array
     */
	public function typeSpecification(){
		global $dsql;

		$typeid = $this->param['typeid'];
		if(!is_numeric($typeid)) return array("state" => 200, "info" => '格式错误！');

		//遍历所选分类ID
		global $data;
		$data = "";
		$parentArr = getParentArr("shop_type", $typeid);
		if(empty($parentArr)) return;

		$proId = array_reverse(parent_foreach($parentArr, "id"));
		foreach($proId as $key => $val){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_item` WHERE `type` = ".$val);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$typeid = $val;
			}
		}

		//获取分类属性
		$specification = array();
		$archives = $dsql->SetQuery("SELECT `spe` FROM `#@__shop_type` WHERE `id` = ".$typeid);
		$results = $dsql->dsqlOper($archives, "results");
		if($results){
			$spe = explode(",", $results[0]['spe']);
			foreach($spe as $key => $val){
				if(!empty($val)){
					$archives_1 = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__shop_specification` WHERE `id` = ".$val);
					$results_1 = $dsql->dsqlOper($archives_1, "results");
					if($results_1){
						$speItem = array();
						foreach($results_1 as $key_1 => $val_1){
							$archives_2 = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__shop_specification` WHERE `parentid` = ".$val_1['id']);
							$results_2 = $dsql->dsqlOper($archives_2, "results");
							if($results_2){
								foreach($results_2 as $key_2 => $val_2){
									$speItem[$key_2]['id']  = $val_2['id'];
									$speItem[$key_2]['val'] = $val_2['typename'];
								}
							}
						}
						if($speItem){
							$specification[$key]['id']       = $results_1[0]['id'];
							$specification[$key]['typeName'] = $results_1[0]['typename'];
							$specification[$key]['listItem'] = $speItem;
						}
					}
				}
			}
		}

		return $specification;
	}


	/**
     * 商城品牌
     * @return array
     */
	public function brand(){
		global $dsql;
		global $langData;
		$pageinfo = $list = array();
		$typeid = $rec = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$typeid   = $this->param['typeid'];
				$category = $this->param['category'];
				$rec      = $this->param['rec'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

        $cityid = getCityId($this->param['cityid']);
        if($cityid){
            $where .= " AND `cityid` = ".$cityid;
        }

		if(!empty($typeid)){
			$where .= " AND `type` = ".$typeid;
		}

		//分类
		if (!empty($category)) {
			$reg   = "(^$category$|^$category,|,$category,|,$category)";
			$where .= " AND `category` REGEXP '" . $reg . "' ";
		}

		if(!empty($rec)){
			$where .= " AND `rec` = 1";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `logo` FROM `#@__shop_brand` WHERE 1 = 1".$where." ORDER BY `id` DESC");
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
		$list = array();

		$param = array(
			"service"     => "shop",
			"template"    => "brand-detail",
			"id"          => "%id%"
		);
		$urlParam = getUrlPath($param);

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id'] = $val['id'];
				$list[$key]['title'] = $val['title'];
				$list[$key]['logo']  = getFilePath($val['logo']);
				$list[$key]['url'] = str_replace("%id%", $val['id'], $urlParam);
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 品牌分类
     * @return array
     */
	public function brandType(){
		global $dsql;

		$results = $dsql->getTypeList(0, "shop_brandtype");
		if($results){
			return $results;
		}
	}


	/**
     * 店铺列表
     * @return array
     */
	public function store(){
		global $dsql;
		global $langData;
		$pageinfo = $list = array();
		$industry = $addrid = $rec = $certi = $orderby = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$industry = $this->param['industry'];
				$addrid   = $this->param['addrid'];
				$rec      = $this->param['rec'];
				$certi    = $this->param['certi'];
				$orderby  = $this->param['orderby'];
				$title    = $this->param['title'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$where = " WHERE `state` = 1";

		$cityid = getCityId($this->param['cityid']);
		if($cityid){
			$where .= " AND `cityid` = $cityid";
		}

		if(!empty($industry)){
			$where .= " AND `industry` = ".$industry;
		}

		//全国情况，不进行区域筛选
		global $siteCityInfo;
		$siteCityInfoName = $siteCityInfo['name'];

		if(!empty($addrid) && $siteCityInfoName != '全国'){
		    $areaArr = $dsql->getTypeList($addrid, "site_area");
			if($areaArr){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($areaArr);
				$lower = $addrid.",".join(',',$lower);
			}else{
				$lower = $addrid;
			}
			$where .= " AND `addrid` in ($lower)";
		}

		if(!empty($rec)){
			$where .= " AND `rec` = 1";
		}

		if(!empty($certi)){
			$where .= " AND `certi` = 1";
		}

		if(!empty($title)){
			//搜索记录
			siteSearchLog("shop", $title);
			$where .= " AND `title` like '%".$title."%'";
		}

		$order = " ORDER BY `rec` DESC, `weight` DESC, `id` DESC";
		//浏览量
		if($orderby == "1"){
			$order = " ORDER BY `click` DESC, `rec` DESC, `weight` DESC, `id` DESC";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `logo`, `title`, `domaintype`, `company`, `referred`, `addrid`, `address`, `industry`, `project`, `logo`, `userid`, `tel`, `qq`, `wechatcode`, `click`, `certi`, `rec`, `pubdate` FROM `#@__shop_store` ".$where.$order);

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
		$list = array();

		$param = array(
			"service"     => "shop",
			"template"    => "store-detail",
			"id"          => "%id%"
		);
		$urlParam = getUrlPath($param);

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id'] = $val['id'];
				$list[$key]['title'] = $val['title'];
				$list[$key]['wechatcode'] = $val['wechatcode'];
				$list[$key]["logo"] = getFilePath($val["logo"]);

				//访问地址
				$url = "";
				if($val['domaintype'] == 1){
					$domainInfo = getDomain('shop', 'shop_store', $val['id']);
					$url = "http://".$domainInfo['domain'];
				}else{
					$url = str_replace("%id%", $val['id'], $urlParam);
				}
				$list[$key]['url'] = $url;

				$list[$key]['referred'] = $val['referred'];
				$list[$key]['company'] = $val['company'];

				global $data;
				$data = "";
				$addrArr = getParentArr("site_area", $val['addrid']);
				$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
				$list[$key]['addr'] = $addrArr;
				$list[$key]['address'] = $val['address'];

				$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__shop_type` WHERE `id` = ".$val['industry']);
				$typename = $dsql->dsqlOper($archives, "results");
				if($typename){
					$list[$key]['industry']   = $typename[0]['typename'];
				}else{
					$list[$key]['industry']   = "";
				}

				$list[$key]['project'] = $val['project'];
				$list[$key]['logo']  = getFilePath($val['logo']);

				//会员信息
				$list[$key]['userinfo'] = getMemberDetail($val['userid']);

				$list[$key]['tel'] = $val['tel'];
				$list[$key]['qq'] = $val['qq'];
				$list[$key]['click'] = $val['click'];
				$list[$key]['certi'] = $val['certi'];
				$list[$key]['rec'] = $val['rec'];
				$list[$key]['pubdate'] = $val['pubdate'];

				//商品总数
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_product` WHERE `store` = ".$val['id']." AND `state` = 1");
				$pcount = $dsql->dsqlOper($sql, "totalCount");
				$list[$key]['productCount'] = $pcount;

				//评论数量
				// $sql = $dsql->SetQuery("SELECT c.`id` FROM `#@__shop_common` c LEFT JOIN `#@__shop_order` o ON o.`id` = c.`aid` WHERE o.`store` = ".$val['id']." AND c.`ischeck` = 1");
				$sql = $dsql->SetQuery("SELECT c.`id` FROM `#@__public_comment` c LEFT JOIN `#@__shop_order` o ON o.`id` = c.`oid` WHERE o.`orderstate` = 3 AND c.`ischeck` = 1 AND c.`type` = 'shop-order' AND o.`store` = ".$val['id']." AND c.`pid` = 0");
				$rcount = $dsql->dsqlOper($sql, "totalCount");
				$list[$key]['reviewCount'] = $rcount;

				//好评率
				// $sql = $dsql->SetQuery("SELECT c.`id` FROM `#@__shop_common` c LEFT JOIN `#@__shop_order` o ON o.`id` = c.`aid` WHERE o.`store` = ".$val['id']." AND c.`rating` = 1 AND c.`ischeck` = 1");
				$sql = $dsql->SetQuery("SELECT c.`id` FROM `#@__public_comment` c LEFT JOIN `#@__shop_order` o ON o.`id` = c.`oid` WHERE o.`orderstate` = 3 AND c.`ischeck` = 1 AND c.`rating` = 1 AND c.`type` = 'shop-order' AND o.`store` = ".$val['id']." AND c.`pid` = 0");
				$hpcount = $dsql->dsqlOper($sql, "totalCount");

				$rating = $hpcount > 0 ? ($hpcount/$rcount * 100) : 0;
				$list[$key]['rating'] = ($rating > 0 ? sprintf("%.2f", $rating) : 0) . "%";

			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 店铺详细信息
     * @return array
     */
	public function storeDetail(){
		global $dsql;
		global $userLogin;
		global $cfg_secureAccess;
		global $cfg_basehost;
		global $langData;
		$listingDetail = array();
		$id = $this->param;
		$uid = $userLogin->getMemberID();
		$id = is_numeric($id) ? $id : $id['id'];
		if(!is_numeric($id) && $uid == -1){
			return array("state" => 200, "info" => '格式错误！');
		}

		$where = " AND `state` = 1";
		if(!is_numeric($id)){
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$uid);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$id = $results[0]['id'];
				$where = "";
			}else{
				return array("state" => 200, "info" => $langData['shop'][4][0]);  //该会员暂未开通商铺！
			}
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_store` WHERE `id` = ".$id.$where);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			//取其中商品中的图片
			$sql = $dsql->SetQuery("SELECT `litpic` FROM `#@__shop_product` WHERE `litpic`!='' AND `store` = '$id' ORDER BY RAND() LIMIT 1");
			$res = $dsql->dsqlOper($sql, "results");
			if(!empty($res)){
				$results[0]["bgphoto"] = !empty($res[0]["litpic"]) ? getFilePath($res[0]["litpic"]) : '';
			}

			global $data;
			$data = "";
			$addrName = getParentArr("site_area", $results[0]['addrid']);
			$addrName = array_reverse(parent_foreach($addrName, "typename"));
			$results[0]['addr']    = $addrName;

			$industry = $dsql->SetQuery("SELECT `typename` FROM `#@__shop_type` WHERE `id` = ".$results[0]['industry']);
			$industry  = $dsql->dsqlOper($industry, "results");
			$results[0]['industryid'] = $results[0]['industry'];
			$results[0]['industry']   = $industry[0]['typename'];

			$results[0]["logoSource"] = $results[0]["logo"];
			$results[0]["logo"] = getFilePath($results[0]["logo"]);

			$results[0]["wechatqrSource"] = $results[0]["wechatqr"];
			$results[0]["wechatqr"] = getFilePath($results[0]["wechatqr"]);

			$this->param = "";
			$channelDomain = $this->config();
			$domainInfo = getDomain('shop', 'shop_store', $id);

			/**
			 * 默认 || 模块配置为子目录并且信息配置为绑定子域名则访问方式转为默认
			 * （因为子域名是随模块配置变化，如果模块配置为子目录地址为乱掉。）
			 * 如：模块配置：http://menhu168.com/shop
			 * 如果信息绑定子域名则会变成：http://demo.menhu168.com/shop
			 * 这样会导致系统读取信息错误
			 */
			if($results[0]["domaintype"] == 0 || ($channelDomain['subDomain'] == 2 && $results[0]["domaintype"] == 2)){

				$param = array(
					"service"     => "shop",
					"template"    => "store-detail",
					"id"          => $id
				);
				$urlParam = getUrlPath($param);
				$results[0]["domain"] = $urlParam;

			//绑定主域名
			}elseif($results[0]["domaintype"] == 1){

				$results[0]["domain"] = $cfg_secureAccess . $domainInfo['domain'];
				$results[0]["domainexp"] = date("Y-m-d H:i:s", $domainInfo['expires']);
				$results[0]["domaintip"] = $domainInfo['note'];

			//绑定子域名
			}elseif($results[0]["domaintype"] == 2){

				$results[0]["domain"] = str_replace("http://", "http://".$domainInfo['domain'].".", $channelDomain['channelDomain']);
				$results[0]["domain"] = str_replace("https://", "https://".$domainInfo['domain'].".", $channelDomain['channelDomain']);
				$results[0]["domainexp"] = date("Y-m-d H:i:s", $domainInfo['expires']);
				$results[0]["domaintip"] = $domainInfo['note'];

			}

			//验证是否已经收藏
			$params = array(
				"module" => "shop",
				"temp"   => "store-detail",
				"type"   => "add",
				"id"     => $id,
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$results[0]['collect'] = $collect == "has" ? 1 : 0;

			//评论数量
			// $sql = $dsql->SetQuery("SELECT c.`id` FROM `#@__shop_common` c LEFT JOIN `#@__shop_order` o ON o.`id` = c.`aid` WHERE o.`store` = '$id' AND c.`ischeck` = 1");
			$sql = $dsql->SetQuery("SELECT c.`id` FROM `#@__public_comment` c LEFT JOIN `#@__shop_order` o ON o.`id` = c.`oid` WHERE o.`orderstate` = 3 AND c.`ischeck` = 1 AND c.`type` = 'shop-order' AND o.`store` = '$id' AND c.`pid` = 0");
			$rcount = $dsql->dsqlOper($sql, "totalCount");
			//好评率
			// $sql = $dsql->SetQuery("SELECT c.`id` FROM `#@__shop_common` c LEFT JOIN `#@__shop_order` o ON o.`id` = c.`aid` WHERE o.`store` = '$id' AND c.`rating` = 1 AND c.`ischeck` = 1");
			$sql = $dsql->SetQuery("SELECT c.`id` FROM `#@__public_comment` c LEFT JOIN `#@__shop_order` o ON o.`id` = c.`oid` WHERE o.`orderstate` = 3 AND c.`ischeck` = 1 AND c.`rating` = 1 AND c.`type` = 'shop-order' AND o.`store` = '$id' AND c.`pid` = 0");
			$hpcount = $dsql->dsqlOper($sql, "totalCount");

			$rating = $hpcount > 0 ? ($hpcount/$rcount * 100) : 0;
			$results[0]['rating'] = ($rating > 0 ? sprintf("%.2f", $rating) : 0) . "%";

			//会员信息
			$userinfo = getMemberDetail($results[0]['userid']);
			$results[0]['certifyState'] = $userinfo['certifyState'];

			//图集
            $imglist = array();
            $pics    = str_replace('||', '', $results[0]['pic']);
            if (!empty($pics)) {
                $pics = explode("###", $pics);//print_R($pics);exit;
                foreach ($pics as $key => $value) {
					if(!empty($value)){
						$imglist[$key]['path']       = getFilePath($value);
                    	$imglist[$key]['pathSource'] = $value;
					}
                }
            } else {
                //$imglist[$key]['path'] = '';
            }
            $results[0]['pics'] = $imglist;

			return $results[0];
		}
	}



	/**
     * 店铺商品分类
     * @return array
     */
	public function category(){
		global $dsql;
		$store = $type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$store    = (int)$this->param['store'];
				$type     = (int)$this->param['type'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}

		if(empty($store)){
			global $userLogin;
			$userid = $userLogin->getMemberID();
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$store = $ret[0]['id'];
			}else{
				return array("state" => 200, "info" => '格式错误！');
			}
		}

		$results = $dsql->getTypeList($type, "shop_category", $son, $page, $pageSize, " AND `type` = $store");
		if($results){
			return $results;
		}
	}



	/**
     * 商品列表
     * @return array
     */
	public function slist(){
		global $dsql;
		global $userLogin;
		global $langData;
		$pageinfo = $list = array();
		$typeid = $title = $item = $specification = $brand = $store = $storetype = $price = $flag = $limited = $orderby = $u = $state = $page = $pageSize = $where = $where1 = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$typeid         = $this->param['typeid'];
				$title          = $this->param['title'] ? $this->param['title'] : $this->param['keywords'];
				$keywords       = $this->param['keywords'];
				$item           = $this->param['item'];
				$specification  = $this->param['specification'];
				$brand          = $this->param['brand'];
				$store          = $this->param['store'];
				$storetype      = $this->param['storetype'];
				$price          = $this->param['price'];
				$flag           = $this->param['flag'];
				$limited        = $this->param['limited'];
				$orderby        = $this->param['orderby'];
				$u              = $this->param['u'];
				$state          = $this->param['state'];
				$time           = $this->param['time'];
				$hourly         = $this->param['hourly'];
				$page           = $this->param['page'];
				$pageSize       = $this->param['pageSize'];
			}
		}

        $where2 = " `state` = 1";
        $cityid = getCityId($this->param['cityid']);
        if($cityid){
            $where2 .= " AND `cityid` = ".$cityid;
        }
        if($u != 1){
	        $archives = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE ".$where2);
	        $results  = $dsql->dsqlOper($archives, "results");
	        if($results){
	            foreach ($results as $key => $value) {
	                $sidArr[$key] = $value['id'];
	            }
	            $where .= " AND p.`store` in (".join(",",$sidArr).")";
	        }else{
	            $where .= " AND 2 = 3";
	        }
	    }

		//限时抢 准点秒
		//限时抢筛选
		if(!empty($limited)){
			$now = GetMkTime(time());

			//未开始
			if($limited == "1"){
				$where .= " AND find_in_set(3, p.`flag`) AND p.`btime` > $now";

			//进行中
			}elseif($limited == "2"){
				$where .= " AND find_in_set(3, p.`flag`) AND p.`btime` <= $now AND p.`etime` > $now";

			//已结束
			}elseif($limited == "3"){
				$where .= " AND find_in_set(3, p.`flag`) AND p.`etime` < $now";

			}elseif($limited == "4"){//限时抢购
				$start = $time - 3600;
				$where .= " AND FIND_IN_SET(3, p.`flag`) AND `btime` >= '$start' AND `etime` <= '$time'";
			}elseif($limited == "5"){//限时秒杀
				$start = time();
		    	//$where .= " AND FIND_IN_SET(4, p.`flag`) AND `ketime` >= '$start'";
		    	$where .= " AND FIND_IN_SET(4, p.`flag`) ";
			}elseif($limited == "6"){//不包括限时抢购 限时秒杀
				$where .= " AND INSTR(p.`flag`,'3')=0 AND INSTR(p.`flag`,'4')=0";
			}

		//正常情况下不显示未开始和已结束的商品
		}else{

			$now = GetMkTime(time());//OR (p.`kstime` <= $now AND p.`ketime` > $now) OR (p.`kstime` = 0 AND p.`ketime` = 0)
			$where .= " AND (
				(p.`kstime` <= $now AND p.`ketime` > $now) OR
				(p.`btime` <= $now AND p.`etime` > $now) OR
				(p.`btime` = 0 AND p.`etime` = 0 AND p.`kstime` = 0 AND p.`ketime` = 0)
			)";

		}


		//遍历分类
		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "shop_type")){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($dsql->getTypeList($typeid, "shop_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}
			$where .= " AND p.`type` in ($lower)";
		}


		if(!empty($title)){

			//搜索记录
			siteSearchLog("shop", $title);

			$where .= " AND p.`title` like '%".$title."%'";
		}


		//字段筛选
		if(!empty($item)){
			$item_ = explode(";", $item);
			$itemArr = array();
			foreach ($item_ as $key => $value) {
				$val = explode(":", $value);
				$itemArr[] = "p.`property` like '%".$val[0]."#".$val[1]."%'";
			}
			if(!empty($itemArr)){
				$where .= " AND (".join(" AND ", $itemArr).")";
			}else{
				$where .= " AND 1 = 2";
			}
		}


		//规格筛选
		if(!empty($specification)){
			$speList = array();
			$nspe = array();
			$speArr = explode(";", $specification);
			foreach ($speArr as $key => $value) {
				$v = explode(":", $value);
				array_push($nspe, $v[1]);
			}
			$specation = count($nspe) > 1 ? descartes($nspe) : $nspe;
			foreach($specation as $key => $val){
				if(is_array($val)){
					array_push($speList, join("-", $val));
				}else{
					array_push($speList, $val);
				}
			}

			$speArr = array();
			foreach($speList as $k => $v){
				$speArr[] = "p.`specification` like '%".$v.",%' OR p.`specification` like '%".$v."-%'";
			}
			if(!empty($speArr)){
				$where .= " AND (".join(" AND ", $speArr).")";
			}else{
				$where .= " AND 1 = 2";
			}

		}


		//品牌
		if(!empty($brand)){
			$where .= " AND p.`brand` = ".$brand;
		}

		//商铺
		if($store != ""){
			$store = is_array($store) && isset($store['sid']) ? $store['sid'] : $store;
			//分类
			if(!empty($storetype)){

				$sids = array($storetype);

				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_category` WHERE `parentid` = $storetype AND `type` = $store");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					foreach ($ret as $key => $value) {
						array_push($sids, $value['id']);
					}
				}

				$st = array();
				foreach ($sids as $key => $value) {
					array_push($st, "find_in_set(".$value.", p.`category`)");
				}
				$where .= " AND p.`store` = ".$store." AND (".join(" OR ", $st).")";
			}else{
				$where .= " AND p.`store` = ".$store;
			}
		}


		//价格区间
		if($price != ""){
			$price = explode(",", $price);
			if(empty($price[0])){
				$where .= " AND p.`price` < " . $price[1];
			}elseif(empty($price[1])){
				$where .= " AND p.`price` > " . $price[0];
			}else{
				$where .= " AND p.`price` BETWEEN " . $price[0] . " AND " . $price[1];
			}
		}


		//属性
		if($flag || $flag == "0"){
			$flagArr = explode(",", $flag);
			if($flagArr){
				$flag = array();
				foreach ($flagArr as $key => $value) {
					$flag[$key] = "FIND_IN_SET(".$value.", p.`flag`)";
				}
				$where .= " AND " . join(" AND ", $flag);
			}
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		//排序
		switch ($orderby){
			//默认
			case 0:
				$orderby = " ORDER BY p.`sort` DESC, p.`id` DESC";
				break;
			//销量降序
			case 1:
				$orderby = " ORDER BY p.`sales` DESC, p.`sort` DESC, p.`id` DESC";
				break;
			//销量升序
			case 2:
				$orderby = " ORDER BY p.`sales` ASC, p.`sort` DESC, p.`id` DESC";
				break;
			//价格升序
			case 3:
				$orderby = " ORDER BY p.`price` ASC, p.`sort` DESC, p.`id` DESC";
				break;
			//价格降序
			case 4:
				$orderby = " ORDER BY p.`price` DESC, p.`sort` DESC, p.`id` DESC";
				break;
			//时间降序
			case 5:
				$orderby = " ORDER BY p.`pubdate` DESC, p.`sort` DESC, p.`id` DESC";
				break;
			//人气降序
			case 6:
				$orderby = " ORDER BY p.`click` DESC, p.`sort` DESC, p.`id` DESC";
				break;
			default:
				$orderby = " ORDER BY p.`sort` DESC, p.`id` DESC";
				break;
		}


		//是否输出当前登录会员的信息
		if($u != 1){
			$where .= " AND p.`state` = 1";
		}else{
			$uid = $userLogin->getMemberID();

			if(!verifyModuleAuth(array("module" => "shop"))){
				return array("state" => 200, "info" => $langData['shop'][4][1]);  //商家权限验证失败！
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = $uid");
			$storeRes = $dsql->dsqlOper($sql, "results");
			if($storeRes){
				$where .= " AND p.`store` = ".$storeRes[0]['id'];
			}else{
				$where .= " AND 1 = 2";
			}

			if($state != ""){
				$where1 = " AND p.`state` = ".$state;
			}
		}

		$archives = $dsql->SetQuery("SELECT " .
									"p.`id`, p.`type`, p.`kstime`,p.`ketime`,p.`title`, p.`store`, p.`mprice`, p.`price`, p.`sales`, p.`inventory`, p.`litpic`, p.`flag`, p.`btime`, p.`etime`, p.`state`, p.`pubdate`, p.`specification` " .
									"FROM `#@__shop_product` p LEFT JOIN `#@__shop_store` s ON s.`id` = p.`store`" .
									"WHERE " .
									"1 = 1".$where);
		//print_R($archives);exit;
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

		//会员列表需要统计信息状态
		if($u == 1 && $uid > -1){
			//待审核
			$totalGray = $dsql->dsqlOper($archives." AND p.`state` = 0", "totalCount");
			//已审核
			$totalAudit = $dsql->dsqlOper($archives." AND p.`state` = 1", "totalCount");
			//拒绝审核
			$totalRefuse = $dsql->dsqlOper($archives." AND p.`state` = 2", "totalCount");

			$pageinfo['gray'] = $totalGray;
			$pageinfo['audit'] = $totalAudit;
			$pageinfo['refuse'] = $totalRefuse;
		}

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";

		$results = $dsql->dsqlOper($archives.$where1.$orderby.$where, "results");

		//echo $archives.$where1.$orderby.$where;die;

		$param = array(
			"service"     => "shop",
			"template"    => "detail",
			"id"          => "%id%"
		);
		$urlParam = getUrlPath($param);


		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']       = $val['id'];
				$list[$key]['title']    = $val['title'];
				$list[$key]['store']    = $val['store'];
				$list[$key]['mprice']   = $val['mprice'];
				$list[$key]['price']    = $val['price'];
				$list[$key]['sales']    = $val['sales'];
				$list[$key]['inventory'] = $val['inventory'];
				$list[$key]['litpic']   = getFilePath($val['litpic']);

				$rec   = 0;
				$tejia = 0;
				$hot   = 0;
				$panic = 0;
				$flag = explode(",", $val['flag']);
				if(in_array(0, $flag)){
					$rec = 1;
				}
				if(in_array(1, $flag)){
					$tejia = 1;
				}
				if(in_array(2, $flag)){
					$hot = 1;
				}
				if(in_array(3, $flag)){
					$panic = 1;

					$list[$key]['btime']  = $val['btime'];
					$list[$key]['etime']  = $val['etime'];
				}
				if(in_array(4, $flag)){
				    $panic = 2;

				    $list[$key]['kstime']  = $val['kstime'];
				    $list[$key]['ketime']  = $val['ketime'];
				}

				if($limited==4){//限时抢购状态
					if(time()<$time-3600){//大于现在的时间
						$list[$key]['states'] = 1;//查看详情
					}else{
						$list[$key]['states'] = 2;//立即抢购
					}
					if(date('Ymd', $val['btime']) == date('Ymd')) {
						$list[$key]['statestime'] = date('H:i', $val['btime']);
					    $list[$key]['statesname'] = '今天';
					}else{
						$list[$key]['statestime'] = date('H:i', $val['btime']);
						$list[$key]['statesname'] = '明天';
					}
				}

				$list[$key]['rec']    = $rec;
				$list[$key]['tejia']  = $tejia;
				$list[$key]['hot']    = $hot;
				$list[$key]['panic']  = $panic;

				// $sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_common` WHERE `pid` = ".$val['id']);
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'shop-order' AND `aid` = ".$val['id']." AND `pid` = 0");
				$comment = $dsql->dsqlOper($sql, "totalCount");
				$list[$key]['comment']  = $comment;

				if($u == 1){
					$list[$key]['state'] = $val['state'];
					$list[$key]['pubdate'] = $val['pubdate'];
				}

				$list[$key]['url'] = str_replace("%id%", $val['id'], $urlParam);



				//规格值
				$specification = array();
				$specifiIds = array();
				$speArr = array();
				$specifiList = $val['specification'];
				if(!empty($specifiList)){
					$specifiArr = explode("|", $specifiList);
					foreach($specifiArr as $k_ => $v_){
						$value = explode(",", $v_);
						$ids = explode("-", $value[0]);

						$speArr[$k_]['spe'] = $value[0];
						$speArr[$k_]['price'] = explode("#", $value[1]);

						foreach($ids as $key_ => $val_){
							if(!in_array($val_, $specifiIds)){
								array_push($specifiIds, $val_);
							}
						}
					}
				}
				$list[$key]['specification'] = $speArr;

				//遍历所选分类ID
				$typeid = $val['type'];

				if($typeid != 0){
					$archives = $dsql->SetQuery("SELECT `spe` FROM `#@__shop_type` WHERE `id` = ".$typeid);
					$typeResults = $dsql->dsqlOper($archives, "results");
					if($typeResults){
						if($typeResults[0]['spe']){
							$spe = explode(",", $typeResults[0]['spe']);
							foreach($spe as $key_ => $val_){
								$archives_1 = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__shop_specification` WHERE `id` = ".$val_);
								$results_1 = $dsql->dsqlOper($archives_1, "results");
								if($results_1){
									$speItem = array();
									foreach($results_1 as $key_1 => $val_1){
										$archives_2 = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__shop_specification` WHERE `parentid` = ".$val_1['id']);
										$results_2 = $dsql->dsqlOper($archives_2, "results");
										if($results_2){
											$i = 0;
											foreach($results_2 as $key_2 => $val_2){
												if(in_array($val_2['id'], $specifiIds)){
													$speItem[$i]['name'] = $val_2['typename'];
													$speItem[$i]['id'] = $val_2['id'];
													$i++;
												}
											}
										}
									}
									if($speItem){
										$specification[$key_]['id'] = $results_1[0]['id'];
										$specification[$key_]['typename'] = $results_1[0]['typename'];
										$specification[$key_]['item'] = $speItem;
									}
								}
							}
						}
					}
				}

				$list[$key]["specificationArr"] = $specification;

				//商家信息
				$sql = $dsql->SetQuery("SELECT `id`, `title`, `domaintype`, `addrid` FROM `#@__shop_store` WHERE `id` = ".$val['store']);
				$res = $dsql->dsqlOper($sql, "results");
				if(!empty($res)){
					$list[$key]["storeTitle"] = $res[0]['title'];
					$param = array(
						"service"     => "shop",
						"template"    => "store-detail",
						"id"          => "%id%"
					);
					$storeurlParam = getUrlPath($param);
					$url = "";
					if($res[0]['domaintype'] == 1){
						$domainInfo = getDomain('shop', 'shop_store', $res[0]['id']);
						$url = "http://".$domainInfo['domain'];
					}else{
						$url = str_replace("%id%", $res[0]['id'], $storeurlParam);
					}
					$list[$key]['storeurl'] = $url;
					global $data;
					$data = "";
					$addrName = getParentArr("site_area", $res[0]['addrid']);
					$addrName = array_reverse(parent_foreach($addrName, "typename"));
					$list[$key]['alladdr']    = $addrName;
					$list[$key]['addr']    = $addrName[0].$addrName[1];
				}
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);

	}


	/**
     * 商品详细信息
     * @return array
     */
	public function detail(){
		global $dsql;
		global $oper;
		$listingDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$where = " AND `state` = 1";
		if($oper == "user"){
			$where = "";
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_product` WHERE 1 = 1 AND `id` = ".$id.$where);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$flag = explode(",", $results[0]['flag']);
			if(!empty($results[0]["flag"])){
				if(in_array(3, $flag)){
					$results[0]['states']  = 1;
					$results[0]['btime']  = $results[0]['btime'];
					$results[0]['etime']  = $results[0]['etime'];
				}
				if(in_array(4, $flag)){
					$results[0]['states']  = 2;
				    $results[0]['kstime']  = $results[0]['kstime'];
				    $results[0]['ketime']  = $results[0]['ketime'];
				}
			}

			//分类名
			global $data;
			$data = "";
			$proType = getParentArr("shop_type", $results[0]["type"]);
			$proName = array_reverse(parent_foreach($proType, "typename"));
			$results[0]["typename"] = $proName;
			$data = "";
			$proIds = array_reverse(parent_foreach($proType, "id"));
			$results[0]["typeids"] = $proIds;

			//品牌信息
			$brandName = $brandLogo = "";
			$archives = $dsql->SetQuery("SELECT `title`, `logo` FROM `#@__shop_brand` WHERE `id` = ".$results[0]['brand']." ORDER BY `id` DESC");
			$brandResults = $dsql->dsqlOper($archives, "results");
			if($brandResults){
				$brandName = $brandResults[0]['title'];
				$brandLogo = getFilePath($brandResults[0]['logo']);
			}
			$results[0]["brandName"] = $brandName;
			$results[0]["brandLogo"] = $brandLogo;

			//字段值
			$propertyArr = array();
			$property = $results[0]["property"];
			$results[0]['propertyId'] = $property;
			if(!empty($property)){
				$property = explode("|", $property);
				foreach($property as $k => $v){
					$item = explode("#", $v);
					$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__shop_item` WHERE `id` = ".$item[0]);
					$itemResults = $dsql->dsqlOper($archives, "results");
					if($itemResults){
						$propertyArr[$k]['typename'] = $itemResults[0]['typename'];
						//只有数字的时候
						if(is_numeric($item[1])){
							$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__shop_item` WHERE `id` = ".$item[1]);
							$itemResults = $dsql->dsqlOper($archives, "results");
							if($itemResults){
								$propertyArr[$k]['val'] = $itemResults[0]['typename'];
							}

						//包含，号的时候
					}elseif(strpos($item[1], ",") !== false){
							$val = explode(",", $item[1]);
							$value = array();
							foreach($val as $k_ => $v_){
								$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__shop_item` WHERE `id` = ".$v_);
								$itemResults = $dsql->dsqlOper($archives, "results");
								if($itemResults){
									$value[] = $itemResults[0]['typename'];
								}
							}
							$propertyArr[$k]['val'] = join(",", $value);

						//其它，直接输出
						}else{
							$propertyArr[$k]['val'] = $item[1];
						}
					}
				}
			}
			$results[0]["property"] = $propertyArr;


			//规格值
			$specification = array();
			$specifiIds = array();
			$speArr = array();
			$specifiList = $results[0]['specification'];
			if(!empty($specifiList)){
				$specifiArr = explode("|", $specifiList);
				foreach($specifiArr as $key => $val){
					$value = explode(",", $val);
					$ids = explode("-", $value[0]);

					$spe = explode('-', $value[0]);
					sort($spe);
					$speArr[$key]['spe'] = join("-", $spe);
					$speArr[$key]['price'] = explode("#", $value[1]);

					foreach($ids as $key_ => $val_){
						if(!in_array($val_, $specifiIds)){
							array_push($specifiIds, $val_);
						}
					}
				}
			}
			$results[0]['specification'] = $speArr;
			$results[0]['specifiList'] = $specifiList;

			//遍历所选分类ID
			$typeid = $results[0]['type'];
			// $parentArr = getParentArr("shop_type", $results[0]['type']);
			// if(empty($parentArr)) return;
			//
			// $proId = array_reverse(parent_foreach($parentArr, "id"));
			// $proId = array_diff($proId, $proType);
			// foreach($proId as $key => $val){
			// 	$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_item` WHERE `type` = ".$val);
			// 	$proResults = $dsql->dsqlOper($archives, "results");
			// 	if($proResults){
			// 		$typeid = $val;
			// 	}
			// }

			if($typeid != 0){
				$archives = $dsql->SetQuery("SELECT `spe` FROM `#@__shop_type` WHERE `id` = ".$typeid);
				$typeResults = $dsql->dsqlOper($archives, "results");
				if($typeResults){
					if($typeResults[0]['spe']){
						$spe = explode(",", $typeResults[0]['spe']);
						foreach($spe as $key => $val){
							$archives_1 = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__shop_specification` WHERE `id` = ".$val);
							$results_1 = $dsql->dsqlOper($archives_1, "results");
							if($results_1){
								$speItem = array();
								foreach($results_1 as $key_1 => $val_1){
									$archives_2 = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__shop_specification` WHERE `parentid` = ".$val_1['id']);
									$results_2 = $dsql->dsqlOper($archives_2, "results");
									if($results_2){
										$i = 0;
										foreach($results_2 as $key_2 => $val_2){
											if(in_array($val_2['id'], $specifiIds)){
												$speItem[$i]['name'] = $val_2['typename'];
												$speItem[$i]['id'] = $val_2['id'];
												$i++;
											}
										}
									}
								}
								if($speItem){
									$specification[$key]['id'] = $results_1[0]['id'];
									$specification[$key]['typename'] = $results_1[0]['typename'];
									$specification[$key]['item'] = $speItem;
								}
							}
						}
					}
				}
			}

			$results[0]["specificationArr"] = $specification;
			$results[0]["litpicSource"] = $results[0]["litpic"];
			$results[0]["litpic"] = getFilePath($results[0]["litpic"]);

			//图集
			$imgList = array();
			$pics = $results[0]["pics"];
			if(!empty($pics)){
				$pics = explode(",", $pics);
				foreach($pics as $key => $val){
					$imgList[$key]['pathSource'] = $val;
					$imgList[$key]['path'] = getFilePath($val);
				}
			}
			$results[0]["pics"] = $imgList;
			$results[0]["video"]= $results[0]['video'] ? getFilePath($results[0]['video']) : '';

			//商家信息
			$this->param = (int)$results[0]['store'];
			$results[0]["store"] = $this->storeDetail();

			//物流信息
			$logisticNote = "";
            $bearFreight = 0;
            $valuation = 0;
            $express_start = 0;
            $express_postage = 0;
            $express_plus = 0;
            $express_postageplus = 0;
            $preferentialStandard = 0;
            $preferentialMoney = 0;

			$logisticId = $results[0]['logistic'];
			$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_logistictemplate` WHERE `id` = $logisticId");
			$ret = $dsql->dsqlOper($archives, "results");
			if($ret){
				$value = $ret[0];
				$logisticNote = getPriceDetail($value["bearFreight"], $value['valuation'], $value['express_start'], $value['express_postage'], $value['express_plus'], $value['express_postageplus'], $value['preferentialStandard'], $value['preferentialMoney']);

				$bearFreight = $value["bearFreight"];
				$valuation = $value["valuation"];
				$express_start = $value["express_start"];
				$express_postage = $value["express_postage"];
				$express_plus = $value["express_plus"];
				$express_postageplus = $value["express_postageplus"];
				$preferentialStandard = $value["preferentialStandard"];
				$preferentialMoney = $value["preferentialMoney"];
			}
			$results[0]['logisticNote'] = $logisticNote;

			$results[0]['logistic'] = array();
			$results[0]['logistic']['bearFreight'] = $bearFreight;
			$results[0]['logistic']['valuation'] = $valuation;
			$results[0]['logistic']['express_start'] = $express_start;
			$results[0]['logistic']['express_postage'] = $express_postage;
			$results[0]['logistic']['express_plus'] = $express_plus;
			$results[0]['logistic']['express_postageplus'] = $express_postageplus;
			$results[0]['logistic']['preferentialStandard'] = $preferentialStandard;
			$results[0]['logistic']['preferentialMoney'] = $preferentialMoney;

			$results[0]['logisticId'] = $logisticId;



			//评价
			// $sql = $dsql->SetQuery("SELECT `rating` FROM `#@__shop_common` c WHERE c.`ischeck` = 1 AND c.`pid` = ".$id);
			$sql = $dsql->SetQuery("SELECT c.`rating` FROM `#@__public_comment` c WHERE c.`ischeck` = 1 AND c.`type` = 'shop-order' AND c.`aid` = '$id' AND c.`pid` = 0");
			$res = $dsql->dsqlOper($sql, "results");
			$rat = $rat1 = 0;
			foreach($res as $k => $v){
				if($v['rating'] == 1){
					$rat1++;
				}
				$rat++;
			}
			//好评率
			$rating = 0;
			if($rat1 && $rat){
				$rating = number_format($rat1/$rat, 1);
			}
			$totalCommon  = $dsql->dsqlOper($sql, "totalCount");  //评价总人数

			// $sql = $dsql->SetQuery("SELECT avg(c.`score1`) s1, avg(c.`score2`) s2, avg(c.`score3`) s3 FROM `#@__shop_common` c WHERE c.`ischeck` = 1 AND c.`pid` = ".$id);
			$sql = $dsql->SetQuery("SELECT avg(c.`sco1`) s1, avg(c.`sco2`) s2, avg(c.`sco3`) s3 FROM `#@__public_comment` c WHERE c.`ischeck` = 1 AND c.`type` = 'shop-order' AND c.`aid` = '$id' AND c.`pid` = 0");
			$res = $dsql->dsqlOper($sql, "results");
			$score1 = $res[0]['s1'];  //分项1
			$score2 = $res[0]['s2'];  //分项2
			$score3 = $res[0]['s3'];  //分项3


			$results[0]['totalCommon'] = $totalCommon;
			$results[0]['rating'] = number_format($rating, 1);
			$results[0]['score1'] = number_format($score1, 1);
			$results[0]['score2'] = number_format($score2, 1);
			$results[0]['score3'] = number_format($score3, 1);

			$param = array(
				"service"     => "shop",
				"template"    => "detail",
				"id"          => $id
			);
			$results[0]['url'] = getUrlPath($param);

			//验证是否已经收藏
			$params = array(
				"module" => "shop",
				"temp"   => "detail",
				"type"   => "add",
				"id"     => $id,
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$results[0]['collect'] = $collect == "has" ? 1 : 0;

			//验证是否已经收藏
			$storeP = array(
				"module" => "shop",
				"temp"   => "store-detail",
				"type"   => "add",
				"id"     => $results[0]['store']['id'],
				"check"  => 1
			);
			$storeCollect = checkIsCollect($storeP);

			$results[0]['storeCollect'] = $storeCollect == "has" ? 1 : 0;

            $results[0]['inventory'] = $results[0]['inventory'] < 0 ? 0 : $results[0]['inventory'];  //库存

			// 所属分站
//			$sql = $dsql->SetQuery("SELECT `addrid` FROM `#@__shop_store` WHERE `id` = ".$results[0]['store']);
			// $ret = $dsql->dsqlOper($sql)

			return $results[0];
		}
	}


	/**
		* 购物车列表&&下单商品列表
		* 两处共用一个方法，不同点是购物车列表获取cookie信息，下单商品列表获取传递过来的信息
		* @return array
		*/
	public function getCartList(){

		global $dsql;
		global $langData;

		$param = $this->param;
		if(is_array($param)) unset($param['time']);

		//区分购物车或下单商品列表
		if(!empty($param)){
			$cartData = $param;
		}else{
			$param = array("module" => "shop");
			$moduleHandler = new handlers("member", "operateCart");
			$moduleContent = $moduleHandler->getHandle($param);
			$cartData = $moduleContent['state'] == 100 ? $moduleContent['info']['content'] : '';
			if($cartData){
				$cartData = explode("|", $moduleContent['info']['content']);
			}
		}

		if(!empty($cartData)){

			$return = array();
			$param = array(
				"service"     => "shop",
				"template"    => "detail",
				"id"          => "%id%"
			);
			$urlParam = getUrlPath($param);

			$i = 0;
			foreach ($cartData as $key => $value) {
				$val = explode(",", $value);

				$this->param = $val[0];
				$detail = $this->detail();

				if($detail){
					if($detail['store']){
						$return[$i]['store']  = array(
							"id"     => $detail['store']['id'],
							"title"  => $detail['store']['title'],
							"domain" => $detail['store']['domain'],
							"qq"     => $detail['store']['qq'],
							"address" => $detail['store']['address'],
							"tel"     => $detail['store']['tel']
						);
					}

					$return[$i]['id']     = $val[0];
					$return[$i]['specation']  = $val[1];
					$return[$i]['count']  = $val[2];
					$return[$i]['title']  = $detail['title'];

					$param = array("url" => $detail['litpic'], "type" => "middle");
					$return[$i]['thumb']  = changeFileSize($param);

					//价格
					$price = $detail['price'];
					$inventor = $detail['inventory'];
					if($detail['specification']){
						foreach($detail['specification'] as $k => $v){
							if($v['spe'] == $val[1]){
								$price = $v['price'][1];
								$inventor = $v['price'][2];
							}
						}
					}
					$return[$i]['price']  = $price;
					$return[$i]['inventor'] = $inventor;

					$return[$i]['limit']    = $detail['limit'];
					$return[$i]['volume']   = $detail['volume'];
					$return[$i]['weight']   = $detail['weight'];
					$return[$i]['logisticId']   = $detail['logisticId'];

					$dLogistic = $detail['logistic'];
					$logistic = getLogisticPrice($dLogistic, $price, $val[2], $detail['volume'], $detail['weight']);
					$return[$i]['logistic']  = $logistic;
					$return[$i]['logisticTemp'] = $dLogistic;

					$return[$i]['logisticNote'] = getPriceDetail($dLogistic["bearFreight"], $dLogistic['valuation'], $dLogistic['express_start'], $dLogistic['express_postage'], $dLogistic['express_plus'], $dLogistic['express_postageplus'], $dLogistic['preferentialStandard'], $dLogistic['preferentialMoney']);

					$return[$i]['url'] = str_replace("%id%", $val[0], $urlParam);

					//规格名
					$speInfo = array();
					$speArr = explode("-", $val[1]);
					foreach ($speArr as $k => $v) {
						foreach($detail['specificationArr'] as $kk => $vv){
							$typename = $vv['typename'];
							foreach($vv['item'] as $kkk => $vvv){
								if($vvv['id'] == $v){
									array_push($speInfo, $typename."：".$vvv['name']);
								}
							}
						}
					}
					$return[$i]['speInfo'] = join("；", $speInfo);
					$i++;
				}
			}

			return $return;
		}else{
			return array("state" => 200, "info" => $langData['shop'][4][2]);  //商品列表为空
		}

	}



	/**
	 * 确认订单
	 * @return array
	 */
	public function confirm_order(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;

		if(empty($param)) return array("state" => 200, "info" => $langData['shop'][4][3]);  //商品为空

		$pros = $param['pros'];
		if(!is_array($pros)) return array("state" => 200, "info" => $langData['shop'][4][4]);  //格式错误

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！


		//获取商品相关信息
		$storeArr = array();
		foreach($pros as $key => $value){
			//pro[0]商品ID，pro[1]规格，pro[2]数量
			$pro = explode(",", $value);

			$this->param = $pro[0];
			$detail = $this->detail();

			if(!is_array($detail)) return array("state" => 200, "info" => $langData['shop'][4][5]);  //购物车中含有不存在的商品，请刷新页面重试！

			//规格名
			$speInfo = array();
			$speArr = explode("-", $pro[1]);
			foreach ($speArr as $k => $v) {
				foreach($detail['specificationArr'] as $kk => $vv){
					$typename = $vv['typename'];
					foreach($vv['item'] as $kkk => $vvv){
						if($vvv['id'] == $v){
							array_push($speInfo, $typename."：".$vvv['name']);
						}
					}
				}
			}

			//判断限时抢是否已经结束
			if(strpos($detail['flag'], '3') !== false){
				if($detail['btime'] > time()) return array("state" => 200, "info" => '【'.$detail['title'].'  '.join("；", $speInfo).'】' . $langData['shop'][4][6]);  //活动还未开始
				if($detail['etime'] < time()) return array("state" => 200, "info" => '【'.$detail['title'].'  '.join("；", $speInfo).'】' . $langData['shop'][4][7]);  //活动已经结束
			}

			//判断秒杀是否已经结束
			if(strpos($detail['flag'], '4') !== false){
				if($detail['kstime'] > time()) return array("state" => 200, "info" => '【'.$detail['title'].'  '.join("；", $speInfo).'】' . $langData['shop'][4][6]);  //活动还未开始
				if($detail['ketime'] < time()) return array("state" => 200, "info" => '【'.$detail['title'].'  '.join("；", $speInfo).'】' . $langData['shop'][4][7]);  //活动已经结束
			}

			//价格&&库存
			$price = $detail['price'];
			$inventor = $detail['inventory'];
			if($detail['specification']){
				foreach($detail['specification'] as $k => $v){
					if($v['spe'] == $pro[1]){
						$price = $v['price'][1];
						$inventor = $v['price'][2];
					}
				}
			}

			if(($detail['limit'] < $pro[2] && $detail['limit'] != 0) || $inventor < $pro[2]) return array("state" => 200, "info" => '【'.$detail['title'].'  '.join("；", $speInfo).'】' . $langData['shop'][4][8]);  //库存不足

		}

		return true;

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

		$pros       = $param['pros'];        //商品
		$ordernum   = $param['ordernum'];    //订单号
		$usePinput  = $param['usePinput'];   //是否使用积分
		$point      = $param['point'];       //使用的积分
		$useBalance = $param['useBalance'];  //是否使用余额
		$balance    = $param['balance'];     //使用的余额
		$paypwd     = $param['paypwd'];      //支付密码

		if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		if(empty($pros) && empty($ordernum)) return array("state" => 200, "info" => $langData['shop'][4][9]);  //提交失败，商品信息提交失败！

		//如果是支付页面，根据订单号查询出商品的规格信息
		if(!empty($ordernum)){
			$sql = $dsql->SetQuery("SELECT p.`proid`, p.`speid`, p.`count` FROM `#@__shop_order` o LEFT JOIN `#@__shop_order_product` p ON p.`orderid` = o.`id` WHERE o.`ordernum` = '$ordernum'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$speid = array();
				foreach($ret as $key => $value){
					array_push($speid, $value['proid'].",".$value['speid'].",".$value['count']);
				}
				$pros = join("|", $speid);
				$this->param['pros'] = $pros;
			}else{
				return array("state" => 200, "info" => $langData['shop'][4][10]);  //订单提交失败！
			}
		}

		//订单状态验证
		$payCheck = $this->payCheck();
		if($payCheck != "ok") return array("state" => 200, "info" => $payCheck['info']);

		if($useBalance == 1 && !empty($balance) && empty($paypwd)) return array("state" => 200, "info" => $langData['siteConfig'][21][88]);  //请输入支付密码！

		$totalPrice = 0;
		$proArr = explode("|", $pros);

        foreach($proArr as $key => $value){
            //pro[0]商品ID，pro[1]规格，pro[2]数量
            $pro = explode(",", $value);

            $this->param = $pro[0];
            $detail = $this->detail();

            //价格&&库存
            $price = $detail['price'];
            if($detail['specification']){
                foreach($detail['specification'] as $k => $v){
                    if($v['spe'] == $pro[1]){
                        $price = $v['price'][1];
                    }
                }
            }

            //物流
            $logistic = getLogisticPrice($detail['logistic'], $price, $pro[2], $detail['volume'], $detail['weight']);

            //规格名
            $speInfo = array();
            $speArr = explode("-", $pro[1]);
            foreach ($speArr as $k => $v) {
                foreach($detail['specificationArr'] as $kk => $vv){
                    $typename = $vv['typename'];
                    foreach($vv['item'] as $kkk => $vvv){
                        if($vvv['id'] == $v){
                            array_push($speInfo, $typename."：".$vvv['name']);
                        }
                    }
                }
            }

            $storeArr[$key]['proid']     = $pro[0];
            $storeArr[$key]['speid']     = $pro[1];
            $storeArr[$key]['specation'] = join("$$$", $speInfo);
            $storeArr[$key]['count']     = $pro[2];
            $storeArr[$key]['volume']    = $detail['volume'];
            $storeArr[$key]['weight']    = $detail['weight'];
            $storeArr[$key]['price']     = $price;
            $storeArr[$key]['logistic']  = $logistic;
            $storeArr[$key]['logisticId']  = $detail['logisticId'];
            $storeArr[$key]['store']     = (int)$detail['store']['id'];

            $totalPrice += $price * $pro[2];
        }


        $orderList = array();

        //对相同商铺的商品分组，对相同商铺的商品生成一个订单号
        $i = 0;
        foreach($storeArr as $k => $v){

            //是否已经存在
            $h = 0;
            foreach($orderList as $key => $value) {
                if($value['store'] == $v['store']){
                    $h = 1;
                }
            }

            $data = array(
                "proid"     => $v['proid'],
                "speid"     => $v['speid'],
                "specation" => $v['specation'],
                "count"     => $v['count'],
                "volume"    => $v['volume'],
                "weight"    => $v['weight'],
                "orderid"   => $v['orderid'],
                "price"     => $v['price'],
                "logistic"  => $v['logistic'],
                "logisticId"  => $v['logisticId']
            );

            //如果不存在则新建一级
            if(!$h){
                $orderList[$i]['sid']    = $v['store'];
                $orderList[$i]['logistic'] = 0;
                $orderList[$i]['list']   = array($data);
                $i++;
            }else{

                //如果已存在则push
                foreach ($orderList as $key => $value) {
                    if($value['store'] == $v['store']){
                        array_push($orderList[$key]['list'], $data);
                    }
                }

            }

        }

        //合并计算运费
        $orderLogistic = calculationOrderLogistic($orderList);
        foreach ($orderLogistic as $key => $val){
            $totalPrice += $val;
        }


//		foreach ($proArr as $key => $value) {
//
//			//proInfo[0] 商品ID　proInfo[1] 商品规格  proInfo[2] 数量
//			$proInfo = explode(",", $value);
//
//			$this->param = $proInfo[0];
//			$detail = $this->detail();
//
//			//价格&&库存
//			$price = $detail['price'];
//			$inventor = $detail['inventory'];
//			if($detail['specification']){
//				foreach($detail['specification'] as $k => $v){
//					if($v['spe'] == $proInfo[1]){
//						$price = $v['price'][1];
//						$inventor = $v['price'][2];
//					}
//				}
//			}
//
//			//物流
//			$logistic = getLogisticPrice($detail['logistic'], $price, $proInfo[2], $detail['volume'], $detail['weight']);
//
//			//单个商品总金额
//			$totalPrice += $price * $proInfo[2] + $logistic;
//
//			//如果是支付页面，计算折扣金额并从单个商品总金额中减去
//			if(!empty($ordernum)){
//
//				$sql = $dsql->SetQuery("SELECT p.`discount` FROM `#@__shop_order` o LEFT JOIN `#@__shop_order_product` p ON p.`orderid` = o.`id` WHERE p.`proid` = ".$proInfo[0]." AND p.`speid` = '".$proInfo[1]."' AND p.`count` = ".$proInfo[2]." AND o.`ordernum` = '$ordernum'");
//				$ret = $dsql->dsqlOper($sql, "results");
//				if($ret){
//					$totalPrice += $ret[0]['discount'];
//				}
//
//			}
//		}


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
		if($useBalance == 1 && !empty($balance) && !empty($paypwd)){

			//验证支付密码
			$archives = $dsql->SetQuery("SELECT `id`, `paypwd` FROM `#@__member` WHERE `id` = '$userid'");
			$results  = $dsql->dsqlOper($archives, "results");
			$res = $results[0];
			$hash = $userLogin->_getSaltedHash($paypwd, $res['paypwd']);
			if($res['paypwd'] != $hash) return array("state" => 200, "info" => $langData['siteConfig'][21][89]);  //支付密码输入错误，请重试！

			//验证余额
			if($usermoney < $balance) return array("state" => 200, "info" => $langData['siteConfig'][20][213]);  //您的余额不足，支付失败！

			$useTotal += $balance;
			$tit[] = $langData['siteConfig'][19][363];  //余额
		}

        $useTotal = sprintf("%.2f", $useTotal);
        $totalPrice = sprintf("%.2f", $totalPrice);

		if($useTotal > $totalPrice) return array("state" => 200, "info" => str_replace('1', join($langData['siteConfig'][13][46], $tit), $langData['siteConfig'][22][104]));  //和  您使用的1超出订单总费用，请重新输入！

		//返回需要支付的费用
		return sprintf("%.2f", $totalPrice - $useTotal);

	}


	/**
	 * 支付前验证订单内容
	 * 验证内容：商品是否存在，商品库存
	 * @return array
	 */
	public function payCheck(){
		global $dsql;
		global $userLogin;
		global $langData;

		$param = $this->param;
		$pros = $param['pros'];

		if(empty($pros)) return array("state" => 200, "info" => $langData['shop'][4][11]);  //商品信息传递失败！

		$userid = $userLogin->getMemberID();
		$proArr = explode("|", $pros);
		foreach ($proArr as $key => $value) {

			//proInfo[0] 商品ID　proInfo[1] 商品规格  proInfo[2] 数量
			$proInfo = explode(",", $value);

			//验证商品是否存在
			$this->param = $proInfo[0];
			$detail = $this->detail();
			if(!is_array($detail)){
				$info = count($proArr) > 1 ? $langData['shop'][4][12] : $langData['shop'][4][13];  //订单中包含不存在或已下架的商品，请确认后重试！      提交失败，您要购买的商品不存在或已下架！
				return array("state" => 200, "info" => $info);
			}


			//验证是否为自己的店铺
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = $userid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				if($detail['store']['id'] == $ret[0]['id']){
					return array("state" => 200, "info" => $langData['shop'][4][14]);  //企业会员不得购买自己店铺的商品！
				}
			}

			//规格名
			$speInfo = array();
			$speArr = explode("-", $pro[1]);
			foreach ($speArr as $k => $v) {
				foreach($detail['specificationArr'] as $kk => $vv){
					$typename = $vv['typename'];
					foreach($vv['item'] as $kkk => $vvv){
						if($vvv['id'] == $v){
							array_push($speInfo, $typename."：".$vvv['name']);
						}
					}
				}
			}

			//判断限时抢是否已经结束
			if(strpos($detail['flag'], '3') !== false){
				if($detail['btime'] > time()) return array("state" => 200, "info" => '【'.$detail['title'].'  '.join("；", $speInfo).'】' . $langData['shop'][4][6]);  //活动还未开始
				if($detail['etime'] < time()) return array("state" => 200, "info" => '【'.$detail['title'].'  '.join("；", $speInfo).'】' . $langData['shop'][4][7]);  //活动已经结束
			}

			//验证商品库存
			$inventor = $detail['inventory'];
			if($detail['specification']){
				foreach($detail['specification'] as $k => $v){
					if($v['spe'] == $proInfo[1]){
						$inventor = $v['price'][2];
					}
				}
			}

			if(($detail['limit'] < $proInfo[2] && $detail['limit'] != 0) || $inventor < $proInfo[2]) return array("state" => 200, "info" => '【'.$detail['title'].'  '.join("；", $speInfo).'】' . $langData['shop'][4][8]);  //库存不足

		}

		return "ok";

	}



	/**
	 * PC端下单&支付
	 * @return array
	 */
	public function pay(){
		global $dsql;
		global $userLogin;
		global $cfg_basehost;
		global $cfg_pointRatio;
		global $langData;

		$param = array(
			"service"     => "member",
			"type"        => "user",
			"template"    => "order",
			"module"      => "shop"
		);
		$url = getUrlPath($param);

		//如果是支付页面，根据订单号查询出商品的规格信息
		$orderid = 0;
		if(!empty($this->param['ordernum'])){
			$sql = $dsql->SetQuery("SELECT o.`id`, p.`proid`, p.`speid`, p.`count` FROM `#@__shop_order` o LEFT JOIN `#@__shop_order_product` p ON p.`orderid` = o.`id` WHERE o.`ordernum` = '".$this->param['ordernum']."'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$speid = array();
				foreach($ret as $key => $value){
					array_push($speid, $value['proid'].",".$value['speid'].",".$value['count']);
				}
				$pros = join("|", $speid);

				$orderid = $ret[0]['id'];
				$this->param['pros'] = $pros;
			}else{
				header("location:".$url);
				die;
			}
		}

		$param =  $this->param;

		//验证需要支付的费用
		$payTotalAmount = $this->checkPayAmount();

		//重置表单参数
		$this->param = $param;

		if($this->payCheck() != "ok" || is_array($payTotalAmount)){
			header("location:".$url);
			die;
		}

		$ordernum   = $param['ordernum'];
		$pros       = $param['pros'];
		$addressid  = $param['addressid'];
		$paytype    = $param['paytype'];
		$note       = $param['note'];
		$usePinput  = $param['usePinput'];
		$point      = (float)$param['point'];
		$useBalance = $param['useBalance'];
		$balance    = (float)$param['balance'];
		$userid     = $userLogin->getMemberID();

		if(empty($pros)) return array("state" => 200, "info" => $langData['shop'][4][4]);  //格式错误
		if(empty($paytype)) return array("state" => 200, "info" => $langData['siteConfig'][21][75]);  //请选择支付方式

		if(empty($ordernum)){
			if(empty($addressid)) return array("state" => 200, "info" => $langData['shop'][4][15]);  //请选择收货地址
		}

		//收货地址信息
		if(empty($ordernum) || !empty($addressid)){
			global $data;
			$data = "";
			$archives = $dsql->SetQuery("SELECT * FROM `#@__member_address` WHERE `uid` = $userid AND `id` = $addressid");
			$userAddr = $dsql->dsqlOper($archives, "results");
			if(!$userAddr) return array("state" => 200, "info" => $langData['shop'][4][16]);  //会员地址库信息不存在或已删除
			$addrArr = getParentArr("site_area", $userAddr[0]['addrid']);
			$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
			$addr    = join(" ", $addrArr);
			$address = $addr . $userAddr[0]['address'];
			$person = $userAddr[0]['person'];
			$mobile = $userAddr[0]['mobile'];
			$tel    = $userAddr[0]['tel'];
			$contact = !empty($mobile) ? $mobile . (!empty($tel) ? " / ".$tel : "") : $tel;
		}


		//获取商品相关信息
		$storeArr = array();

		$pros = explode("|", $pros);
		foreach($pros as $key => $value){
			//pro[0]商品ID，pro[1]规格，pro[2]数量
			$pro = explode(",", $value);

			$this->param = $pro[0];
			$detail = $this->detail();

			//价格&&库存
			$price = $detail['price'];
			if($detail['specification']){
				foreach($detail['specification'] as $k => $v){
					if($v['spe'] == $pro[1]){
						$price = $v['price'][1];
					}
				}
			}

			//物流
			$logistic = getLogisticPrice($detail['logistic'], $price, $pro[2], $detail['volume'], $detail['weight']);

			//规格名
			$speInfo = array();
			$speArr = explode("-", $pro[1]);
			foreach ($speArr as $k => $v) {
				foreach($detail['specificationArr'] as $kk => $vv){
					$typename = $vv['typename'];
					foreach($vv['item'] as $kkk => $vvv){
						if($vvv['id'] == $v){
							array_push($speInfo, $typename."：".$vvv['name']);
						}
					}
				}
			}

			$storeArr[$key]['proid']     = $pro[0];
			$storeArr[$key]['speid']     = $pro[1];
			$storeArr[$key]['specation'] = join("$$$", $speInfo);
			$storeArr[$key]['count']     = $pro[2];
            $storeArr[$key]['volume']    = $detail['volume'];
            $storeArr[$key]['weight']    = $detail['weight'];
			$storeArr[$key]['price']     = $price;
			$storeArr[$key]['logistic']  = $logistic;
			$storeArr[$key]['logisticId']  = $detail['logisticId'];
			$storeArr[$key]['store']     = (int)$detail['store']['id'];
		}


		$orderList = array();

		//对相同商铺的商品分组，对相同商铺的商品生成一个订单号
		$i = 0;
		foreach($storeArr as $k => $v){

			//是否已经存在
			$h = 0;
			foreach($orderList as $key => $value) {
				if($value['store'] == $v['store']){
					$h = 1;
				}
			}

			$data = array(
				"proid"     => $v['proid'],
				"speid"     => $v['speid'],
				"specation" => $v['specation'],
				"count"     => $v['count'],
				"volume"    => $v['volume'],
				"weight"    => $v['weight'],
				"orderid"   => $v['orderid'],
				"price"     => $v['price'],
				"logistic"  => $v['logistic'],
				"logisticId"  => $v['logisticId']
			);

			//如果不存在则新建一级
			if(!$h){
				$orderList[$i]['store']    = $v['store'];
				$orderList[$i]['sid']    = $v['store'];
                $orderList[$i]['logistic'] = 0;
				$orderList[$i]['list']   = array($data);
				$i++;
			}else{

				//如果已存在则push
				foreach ($orderList as $key => $value) {
					if($value['store'] == $v['store']){
						array_push($orderList[$key]['list'], $data);
					}
				}

			}

		}

        //合并计算运费
        $orderLogistic = calculationOrderLogistic($orderList);

        foreach ($orderLogistic as $key => $val){
            foreach ($orderList as $k => $v){
                if($v['sid'] == $key){
                    $orderList[$k]['logistic'] = $val;
                }
            }
        }

		$opArr = array();
		$ordernumArr = array();

		//如果是支付页面只需要更新订单信息
		if(!empty($ordernum)){

			//如果是从支付成功后进入的订单详细页返回过来的，直接跳转至商城首页，否则会出现重复支付的问题！
			$this->param = 'channelDomain';
			$shopConfig = $this->config();
			$shopIndexUrl = $shopConfig['channelDomain'];

			$sql = $dsql->SetQuery("SELECT `orderstate` FROM `#@__shop_order` WHERE `ordernum` = '$ordernum'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$orderstate = $ret[0]['orderstate'];
				if($orderstate != 0){
					header("location:".$shopIndexUrl);
					die;
				}

			//如果没有找到订单，同样跳转到商城首页
			}else{
				header("location:".$shopIndexUrl);
				die;
			}

			array_push($ordernumArr, $ordernum);

			foreach ($orderList as $key => $value) {

                $logistic = $value['logistic'];

				//更新主表
				if(empty($addressid)){
					$sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `paytype` = '$paytype', `logistic` = '$logistic', `payprice` = '$payTotalAmount' WHERE `ordernum` = '$ordernum'");
				}else{
					$sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `paytype` = '$paytype', `logistic` = '$logistic', `payprice` = '$payTotalAmount', `people` = '$person', `address` = '$address', `contact` = '$contact', `note` = '$note' WHERE `ordernum` = '$ordernum'");
				}
				$dsql->dsqlOper($sql, "update");

				$order_amount = $logistic;  //订单总金额

				//更新订单商品价格及运费为最新价格
				foreach($value['list'] as $k => $v){

					//获取订单商品ID
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_order_product` WHERE `orderid` = $orderid AND `proid` = ".$v['proid']." AND `speid` = '".$v['speid']."' AND `count` = ".$v['count']);
					$ret = $dsql->dsqlOper($sql, "results");
					$pid = $ret[0]['id'];

					$sql = $dsql->SetQuery("UPDATE `#@__shop_order_product` SET `price` = ".$v['price'].", `logistic` = ".$v['logistic']." WHERE `id` = ".$pid);
					$dsql->dsqlOper($sql, "update");

					array_push($opArr, array(
						"id" => $pid,
						"price" => $v['price'],
						"count" => $v['count'],
						"logistic" => $v['logistic'],
						"ordernum" => $ordernum
					));

                    $order_amount += $v['price'] * $v['count'];
				}

				//更新订单总金额（包含运费）
                $sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `amount` = '$order_amount', `balance` = 0, `point` = 0 WHERE `ordernum` = '$ordernum'");
				$dsql->dsqlOper($sql, "update");

			}

		//新订单
		}else{

			//每个商铺生成一个订单
			foreach ($orderList as $key => $value) {

				//新订单
				$newOrdernum = create_ordernum();
				$tr = true;

				//新增主表
				$store = $value["store"];
                $logistic = $value['logistic'];
				$no = $note[$store];
				$sql = $dsql->SetQuery("INSERT INTO `#@__shop_order` (`ordernum`, `store`, `userid`, `orderstate`, `orderdate`, `paytype`, `logistic`, `payprice`, `people`, `address`, `contact`, `note`, `tab`) VALUES ('$newOrdernum', '$store', '$userid', 0, ".GetMkTime(time()).", '$paytype', '$logistic', '$payTotalAmount', '$person', '$address', '$contact', '$no', 'shop')");
				$oid = $dsql->dsqlOper($sql, "lastid");

				if($oid){

					array_push($ordernumArr, $newOrdernum);

                    $order_amount = $logistic;  //订单总金额

					//新增订单产品表
					foreach($value['list'] as $k => $v){
						$archives = $dsql->SetQuery("INSERT INTO `#@__shop_order_product` (`orderid`, `proid`, `speid`, `specation`, `price`, `count`, `logistic`) VALUES ('$oid', ".$v['proid'].", '".$v['speid']."', '".$v['specation']."', ".$v['price'].", ".$v['count'].", ".$v['logistic'].")");
						$opid = $dsql->dsqlOper($archives, "lastid");
						if(!is_numeric($opid)){
							$tr = false;
						}else{
							//将新的订单产品组合，以供分配使用的积分或余额
							array_push($opArr, array(
								"id" => $opid,
								"price" => $v['price'],
								"count" => $v['count'],
								"logistic" => $v['logistic'],
								"ordernum" => $newOrdernum
							));

                            $order_amount += $v['price'] * $v['count'];
						}
					}

                    //更新订单总金额（包含运费）
                    $sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `amount` = '$order_amount', `balance` = 0, `point` = 0 WHERE `id` = '$oid'");
                    $dsql->dsqlOper($sql, "update");

				}else{
					return array("state" => 200, "info" => $langData['siteConfig'][21][174]);  //下单失败！
				}

				if(!$tr){
					return array("state" => 200, "info" => $langData['siteConfig'][21][174]);  //下单失败！
				}

			}

		}


		//如果有使用积分或余额则更新订单内容的价格信息
		if(($usePinput && !empty($point)) || ($useBalance && !empty($balance))){

			$pointMoney = $point / $cfg_pointRatio;
			$balanceMoney = $balance;

			foreach ($opArr as $key => $value) {

				$oprice = $value['price'] * $value['count'];  //单个订单总价 = 单价 * 数量

				$usePointMoney = 0;
				$useBalanceMoney = 0;

                $order_point = 0;
                $order_balance = 0;

				//查询订单运费
                $order_logistic = 0;
                $sql = $dsql->SetQuery("SELECT `logistic` FROM `#@__shop_order` WHERE `balance` = 0 AND `point` = 0 AND `ordernum` = '".$value['ordernum']."'");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $order_logistic = $ret[0]['logistic'];
                }

                //积分&余额优先扣除运费
                //先判断积分是否足够支付运费
                //如果足够支付：
                //1.把还需要支付的运费重置为0
                //2.积分总额减去用掉的
                //3.记录已经使用的积分
                if($order_logistic < $pointMoney){

                    $pointMoney -= $order_logistic;
                    $order_point = $order_logistic;
                    $order_logistic = 0;


                    //积分不够支付再判断余额是否足够
                    //如果积分不足以支付总价：
                    //1.总价减去积分抵扣掉的部分
                    //2.积分总额设置为0
                    //3.记录已经使用的积分
                }else{

                    $order_logistic -= $pointMoney;
                    $order_point = $pointMoney;
                    $pointMoney = 0;

                    //验证余额是否足够支付剩余部分的运费
                    //如果足够支付：
                    //1.把还需要支付的运费重置为0
                    //2.余额减去用掉的部分
                    //3.记录已经使用的余额
                    if($order_logistic < $balanceMoney){

                        $balanceMoney -= $order_logistic;
                        $order_balance = $order_logistic;
                        $order_logistic = 0;

                        //余额不够支付的情况
                        //1.运费减去余额付过的部分
                        //2.余额设置为0
                        //3.记录已经使用的余额
                    }else{

                        $order_logistic -= $balanceMoney;
                        $order_balance = $balanceMoney;
                        $balanceMoney = 0;

                    }

                }


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

				//更新单独商品的支付信息
				$pointMoney_ = $usePointMoney * $cfg_pointRatio;
				$archives = $dsql->SetQuery("UPDATE `#@__shop_order_product` SET `point` = '$pointMoney_', `balance` = '$useBalanceMoney', `payprice` = '$oprice' WHERE `id` = ".$value['id']);
				$dsql->dsqlOper($archives, "update");

                //更新整个订单的支付信息
                $order_point = $pointMoney_ + $order_point * $cfg_pointRatio;
                $order_balance += $useBalanceMoney;
                $sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `balance` = `balance` + '$order_balance', `point` = `point` + '$order_point' WHERE `ordernum` = '".$value['ordernum']."'");
                $dsql->dsqlOper($sql, "update");

			}

		//如果没有使用积分或余额，重置积分&余额等价格信息
		}else{
			foreach ($opArr as $key => $value) {
				$payprice = $value['price'] * $value['count'];
				$archives = $dsql->SetQuery("UPDATE `#@__shop_order_product` SET `point` = '0', `balance` = '0', `payprice` = '$payprice' WHERE `id` = ".$value['id']);
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
			foreach ($opArr as $key => $value) {

				//查询订单信息
				$archives = $dsql->SetQuery("SELECT `point`, `balance` FROM `#@__shop_order_product` WHERE `id` = ".$value['id']);
				$results  = $dsql->dsqlOper($archives, "results");
				$res = $results[0];

				$upoint   = $res['point'];    //使用的积分
				$ubalance = $res['balance'];  //使用的余额
				// $ordernum = $ret['ordernum']; //订单号

				//扣除会员积分
				if(!empty($upoint) && $upoint > 0){
					// $archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` - '$upoint' WHERE `id` = '$userid'");
					// $dsql->dsqlOper($archives, "update");
					//
					// //保存操作日志
					// $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$upoint', '支付商城订单：$ordernum', '$date')");
					// $dsql->dsqlOper($archives, "update");

					$paytype[] = "point";
				}

				//扣除会员余额
				if(!empty($ubalance) && $ubalance > 0){
					// $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$ubalance' WHERE `id` = '$userid'");
					// $dsql->dsqlOper($archives, "update");
					//
					// //保存操作日志
					// $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$ubalance', '支付商城订单：$ordernum', '$date')");
					// $dsql->dsqlOper($archives, "update");

					$paytype[] = "money";
				}

			}

			$ordernumArr = join(",", $ordernumArr);
			$paytype = array_unique($paytype);

			//增加支付日志
			$paylognum = create_ordernum();
			$archives = $dsql->SetQuery("INSERT INTO `#@__pay_log` (`ordertype`, `ordernum`, `uid`, `body`, `amount`, `paytype`, `state`) VALUES ('shop', '$paylognum', '$userid', '$ordernumArr', '0', '".join(",", $paytype)."', '1')");
			$dsql->dsqlOper($archives, "update");


			//执行支付成功的操作
			$this->param = array(
				"paytype" => join(",", $paytype),
				"ordernum" => $ordernumArr
			);
			$this->paySuccess();

			//跳转至支付成功页面
			$param = array(
				"service"     => "shop",
				"template"    => "payreturn",
				"ordernum"    => $paylognum
			);
			$url = getUrlPath($param);
			header("location:".$url);

		}else{
			//跳转至第三方支付页面
			createPayForm("shop", join(",", $ordernumArr), $payTotalAmount, $paytype, $langData['shop'][4][17]);  //商城订单
		}

	}



	/**
	 * 移动端下单
	 * @return array
	 */
	public function dealTouch(){
		global $dsql;
		global $userLogin;
		global $cfg_basehost;
		global $cfg_pointRatio;
		global $langData;

		$param = array(
			"service"     => "member",
			"type"        => "user",
			"template"    => "order",
			"module"      => "shop"
		);
		$url = getUrlPath($param);

		$param =  $this->param;

		//验证需要支付的费用
		$payTotalAmount = $this->checkPayAmount();

		//重置表单参数
		$this->param = $param;

		$payCheck = $this->payCheck();
		if($payCheck != "ok" || is_array($payTotalAmount)){

			if($payCheck != "ok"){
				return $payCheck;
			}

			header("location:".$url);
			die;
		}

		$ordernum   = $param['ordernum'];
		$pros       = $param['pros'];
		$addressid  = $param['addressid'];
		$paytype    = $param['paytype'];
		$note       = $param['note'];
		$usePinput  = $param['usePinput'];
		$point      = (float)$param['point'];
		$useBalance = $param['useBalance'];
		$balance    = (float)$param['balance'];
		$userid     = $userLogin->getMemberID();

		if(empty($addressid)) return array("state" => 200, "info" => $langData['shop'][4][15]);  //请选择收货地址
		if(empty($pros)) return array("state" => 200, "info" => $langData['shop'][4][4]);  //格式错误

		//收货地址信息
		global $data;
		$data = "";
		$archives = $dsql->SetQuery("SELECT * FROM `#@__member_address` WHERE `uid` = $userid AND `id` = $addressid");
		$userAddr = $dsql->dsqlOper($archives, "results");
		if(!$userAddr) return array("state" => 200, "info" => $langData['siteConfig'][21][105]);  //会员地址库信息不存在或已删除
		$addrArr = getParentArr("site_area", $userAddr[0]['addrid']);
		$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
		$addr    = join(" ", $addrArr);
		$address = $addr . $userAddr[0]['address'];
		$person = $userAddr[0]['person'];
		$mobile = $userAddr[0]['mobile'];
		$tel    = $userAddr[0]['tel'];
		$contact = !empty($mobile) ? $mobile . (!empty($tel) ? " / ".$tel : "") : $tel;


		//获取商品相关信息
		$storeArr = array();

		$pros = explode("|", $pros);
		foreach($pros as $key => $value){
			//pro[0]商品ID，pro[1]规格，pro[2]数量
			$pro = explode(",", $value);

			$this->param = $pro[0];
			$detail = $this->detail();

			//价格&&库存
			$price = $detail['price'];
			if($detail['specification']){
				foreach($detail['specification'] as $k => $v){
					if($v['spe'] == $pro[1]){
						$price = $v['price'][1];
					}
				}
			}

			//物流
			$logistic = getLogisticPrice($detail['logistic'], $price, $pro[2], $detail['volume'], $detail['weight']);

			//规格名
			$speInfo = array();
			$speArr = explode("-", $pro[1]);
			foreach ($speArr as $k => $v) {
				foreach($detail['specificationArr'] as $kk => $vv){
					$typename = $vv['typename'];
					foreach($vv['item'] as $kkk => $vvv){
						if($vvv['id'] == $v){
							array_push($speInfo, $typename."：".$vvv['name']);
						}
					}
				}
			}

			$storeArr[$key]['proid']     = $pro[0];
			$storeArr[$key]['speid']     = $pro[1];
			$storeArr[$key]['specation'] = join("$$$", $speInfo);
			$storeArr[$key]['count']     = $pro[2];
            $storeArr[$key]['volume']    = $detail['volume'];
            $storeArr[$key]['weight']    = $detail['weight'];
			$storeArr[$key]['price']     = $price;
			$storeArr[$key]['logistic']  = $logistic;
            $storeArr[$key]['logisticId']  = $detail['logisticId'];
			$storeArr[$key]['store']     = (int)$detail['store']['id'];
		}


		$orderList = array();

		//对相同商铺的商品分组，对相同商铺的商品生成一个订单号
		$i = 0;
		foreach($storeArr as $k => $v){

			//是否已经存在
			$h = 0;
			foreach($orderList as $key => $value) {
				if($value['store'] == $v['store']){
					$h = 1;
				}
			}

			$data = array(
				"proid"     => $v['proid'],
				"speid"     => $v['speid'],
				"specation" => $v['specation'],
				"count"     => $v['count'],
				"volume"    => $v['volume'],
				"weight"    => $v['weight'],
				"orderid"   => $v['orderid'],
				"price"     => $v['price'],
				"logistic"  => $v['logistic'],
				"logisticId"  => $v['logisticId']
			);

			//如果不存在则新建一级
			if(!$h){
				$orderList[$i]['store']    = $v['store'];
				$orderList[$i]['sid']    = $v['store'];
				$orderList[$i]['list']   = array($data);
				$i++;
			}else{

				//如果已存在则push
				foreach ($orderList as $key => $value) {
					if($value['store'] == $v['store']){
						array_push($orderList[$key]['list'], $data);
					}
				}

			}

		}

        //合并计算运费
        $orderLogistic = calculationOrderLogistic($orderList);

        foreach ($orderLogistic as $key => $val){
            foreach ($orderList as $k => $v){
                if($v['sid'] == $key){
                    $orderList[$k]['logistic'] = $val;
                }
            }
        }


		$opArr = array();
		$ordernumArr = array();

		//如果是支付页面只需要更新订单信息
		if(!empty($ordernum)){

			array_push($ordernumArr, $ordernum);

			foreach ($orderList as $key => $value) {

                $logistic = $value['logistic'];

				//更新主表
				$sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `paytype` = '$paytype', `logistic` = '$logistic', `payprice` = '$payTotalAmount', `people` = '$person', `address` = '$address', `contact` = '$contact', `note` = '$note'");
				$dsql->dsqlOper($sql, "update");

                $order_amount = $logistic;  //订单总金额

				//更新订单商品价格及运费为最新价格
				foreach($value['list'] as $k => $v){

					//获取订单商品ID
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_order_product` WHERE `orderid` = $orderid AND `proid` = ".$v['proid']." AND `speid` = '".$v['speid']."' AND `count` = ".$v['count']);
					$ret = $dsql->dsqlOper($sql, "results");
					$pid = $ret[0]['id'];

					$sql = $dsql->SetQuery("UPDATE `#@__shop_order_product` SET `price` = ".$v['price'].", `logistic` = ".$v['logistic']." WHERE `id` = ".$pid);
					$dsql->dsqlOper($sql, "update");

					array_push($opArr, array(
						"id" => $pid,
						"price" => $v['price'],
						"count" => $v['count'],
						"logistic" => $v['logistic'],
						"ordernum" => $ordernum
					));

                    $order_amount += $v['price'] * $v['count'];
				}

                //更新订单总金额（包含运费）
                $sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `amount` = '$order_amount', `balance` = 0, `point` = 0 WHERE `ordernum` = '$ordernum'");
                $dsql->dsqlOper($sql, "update");
			}

		//新订单
		}else{

			//每个商铺生成一个订单
			foreach ($orderList as $key => $value) {

				//新订单
				$newOrdernum = create_ordernum();
				$tr = true;

				//新增主表
				$store = $value["store"];
				$no = $note[$store];
				$sql = $dsql->SetQuery("INSERT INTO `#@__shop_order` (`ordernum`, `store`, `userid`, `orderstate`, `orderdate`, `paytype`, `logistic`, `payprice`, `people`, `address`, `contact`, `note`, `tab`) VALUES ('$newOrdernum', '$store', '$userid', 0, ".GetMkTime(time()).", '$paytype', '$logistic', '$payTotalAmount', '$person', '$address', '$contact', '$no', 'shop')");
				$oid = $dsql->dsqlOper($sql, "lastid");

				if($oid){

					array_push($ordernumArr, $newOrdernum);

                    $order_amount = $logistic;  //订单总金额

					//新增订单产品表
					foreach($value['list'] as $k => $v){
						$archives = $dsql->SetQuery("INSERT INTO `#@__shop_order_product` (`orderid`, `proid`, `speid`, `specation`, `price`, `count`, `logistic`) VALUES ('$oid', ".$v['proid'].", '".$v['speid']."', '".$v['specation']."', ".$v['price'].", ".$v['count'].", ".$v['logistic'].")");
						$opid = $dsql->dsqlOper($archives, "lastid");
						if(!is_numeric($opid)){
							$tr = false;
						}else{
							//将新的订单产品组合，以供分配使用的积分或余额
							array_push($opArr, array(
								"id" => $opid,
								"price" => $v['price'],
								"count" => $v['count'],
								"logistic" => $v['logistic'],
								"ordernum" => $newOrdernum
							));

                            $order_amount += $v['price'] * $v['count'];
						}
					}

                    //更新订单总金额（包含运费）
                    $sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `amount` = '$order_amount', `balance` = 0, `point` = 0 WHERE `id` = '$oid'");
                    $dsql->dsqlOper($sql, "update");

				}else{
					return array("state" => 200, "info" => $langData['siteConfig'][21][174]);  //下单失败！
				}

				if(!$tr){
					return array("state" => 200, "info" => $langData['siteConfig'][21][174]);  //下单失败！
				}

			}

		}


		//如果有使用积分或余额则更新订单内容的价格信息
		if(($usePinput && !empty($point)) || ($useBalance && !empty($balance))){

			$pointMoney = $point / $cfg_pointRatio;
			$balanceMoney = $balance;

			foreach ($opArr as $key => $value) {

				$oprice = $value['price'] * $value['count'];  //单个订单总价 = 单价 * 数量

				$usePointMoney = 0;
				$useBalanceMoney = 0;

                $order_point = 0;
                $order_balance = 0;

                //查询订单运费
                $order_logistic = 0;
                $sql = $dsql->SetQuery("SELECT `logistic` FROM `#@__shop_order` WHERE `balance` = 0 AND `point` = 0 AND `ordernum` = '".$value['ordernum']."'");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $order_logistic = $ret[0]['logistic'];
                }

                //积分&余额优先扣除运费
                //先判断积分是否足够支付运费
                //如果足够支付：
                //1.把还需要支付的运费重置为0
                //2.积分总额减去用掉的
                //3.记录已经使用的积分
                if($order_logistic < $pointMoney){

                    $pointMoney -= $order_logistic;
                    $order_point = $order_logistic;
                    $order_logistic = 0;


                    //积分不够支付再判断余额是否足够
                    //如果积分不足以支付总价：
                    //1.总价减去积分抵扣掉的部分
                    //2.积分总额设置为0
                    //3.记录已经使用的积分
                }else{

                    $order_logistic -= $pointMoney;
                    $order_point = $pointMoney;
                    $pointMoney = 0;

                    //验证余额是否足够支付剩余部分的运费
                    //如果足够支付：
                    //1.把还需要支付的运费重置为0
                    //2.余额减去用掉的部分
                    //3.记录已经使用的余额
                    if($order_logistic < $balanceMoney){

                        $balanceMoney -= $order_logistic;
                        $order_balance = $order_logistic;
                        $order_logistic = 0;

                        //余额不够支付的情况
                        //1.运费减去余额付过的部分
                        //2.余额设置为0
                        //3.记录已经使用的余额
                    }else{

                        $order_logistic -= $balanceMoney;
                        $order_balance = $balanceMoney;
                        $balanceMoney = 0;

                    }

                }


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
				$archives = $dsql->SetQuery("UPDATE `#@__shop_order_product` SET `point` = '$pointMoney_', `balance` = '$useBalanceMoney', `payprice` = '$oprice' WHERE `id` = ".$value['id']);
				$dsql->dsqlOper($archives, "update");

                //更新整个订单的支付信息
                $order_point = $pointMoney_ + $order_point * $cfg_pointRatio;
                $order_balance += $useBalanceMoney;
                $sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `balance` = `balance` + '$order_balance', `point` = `point` + '$order_point' WHERE `ordernum` = '".$value['ordernum']."'");
                $dsql->dsqlOper($sql, "update");
			}

		//如果没有使用积分或余额，重置积分&余额等价格信息
		}else{
			foreach ($opArr as $key => $value) {
				$payprice = $value['price'] * $value['count'];
				$archives = $dsql->SetQuery("UPDATE `#@__shop_order_product` SET `point` = '0', `balance` = '0', `payprice` = '$payprice' WHERE `id` = ".$value['id']);
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
			foreach ($opArr as $key => $value) {

				//查询订单信息
				$archives = $dsql->SetQuery("SELECT `point`, `balance` FROM `#@__shop_order_product` WHERE `id` = ".$value['id']);
				$results  = $dsql->dsqlOper($archives, "results");
				$res = $results[0];

				$upoint   = $res['point'];    //使用的积分
				$ubalance = $res['balance'];  //使用的余额
				// $ordernum = $ret['ordernum']; //订单号

				//扣除会员积分
				if(!empty($upoint) && $upoint > 0){
					// $archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` - '$upoint' WHERE `id` = '$userid'");
					// $dsql->dsqlOper($archives, "update");
					//
					// //保存操作日志
					// $archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$upoint', '支付商城订单：$ordernum', '$date')");
					// $dsql->dsqlOper($archives, "update");

					$paytype[] = "point";
				}

				//扣除会员余额
				if(!empty($ubalance) && $ubalance > 0){
					// $archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$ubalance' WHERE `id` = '$userid'");
					// $dsql->dsqlOper($archives, "update");
					//
					// //保存操作日志
					// $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$ubalance', '支付商城订单：$ordernum', '$date')");
					// $dsql->dsqlOper($archives, "update");

					$paytype[] = "money";
				}

			}

			$ordernumArr = join(",", $ordernumArr);
            $paytype = array_unique($paytype);

			//增加支付日志
			$paylognum = create_ordernum();
			$archives = $dsql->SetQuery("INSERT INTO `#@__pay_log` (`ordertype`, `ordernum`, `uid`, `body`, `amount`, `paytype`, `state`) VALUES ('shop', '$paylognum', '$userid', '$ordernumArr', '0', '".join(",", $paytype)."', '1')");
			$dsql->dsqlOper($archives, "update");


			//执行支付成功的操作
			$this->param = array(
				"paytype" => join(",", $paytype),
				"ordernum" => $ordernumArr
			);
			$this->paySuccess();

			//跳转至支付成功页面
			$param = array(
				"service"     => "shop",
				"template"    => "payreturn",
				"ordernum"    => $paylognum
			);
			$url = getUrlPath($param);
			return $url;
			// header("location:".$url);

		}else{

			//如果有多个订单，需要跳转到订单中心，分别支付
            //为什么电脑端可以合并支付，而移动端不可以？  by gz 20190112
            //原因：由于电脑端和移动端的支付提交订单逻辑不同（电脑端一个页面完成填写订单、选择支付方式，而移动端是分两个页面来做的），电脑端提交订单用的pay()，而移动端提交订单用的dealTouch()
            //电脑端执行完pay()后直接进入到了第三方支付，而移动端执行完dealTouch()，最后还会再跳到pay()
            //pay()里面新订单是根据商品ID、规则信息等创建的信息，老订单是根据传过来的订单号（controller中的pay和class中的pay都是查询的一个订单号，没有兼容多个订单的情况）
            //后面如果要做移动端的多订单合并支付，首先把下单的逻辑梳理清楚，然后修改controller中的pay和class中的pay
			if(count($ordernumArr) > 1){
				return $url;
			}else{
				$RenrenCrypt = new RenrenCrypt();
				$ids = base64_encode($RenrenCrypt->php_encrypt(join(",", $ordernumArr)));

				$param = array(
					"service"     => "shop",
					"template"    => "pay",
					"param"       => "ordernum=".$ids
				);
				return getUrlPath($param);
			}

		}

	}


	/**
	 * 支付成功
	 * 此处进行支付成功后的操作，例如发送短信等服务
	 *
	 */
	public function paySuccess(){
		global $langData;
		$param = $this->param;
		if(!empty($param)){
			global $dsql;

			$paytype  = $param['paytype'];
			$paramArr = explode(",", $param['ordernum']);
			$date = GetMkTime(time());

			foreach ($paramArr as $key => $value) {

				//查询订单信息
				$archives = $dsql->SetQuery("SELECT o.`id`, o.`userid`, o.`paydate`, o.`logistic`, o.`amount`, o.`balance`, o.`payprice`, o.`point`, s.`userid` uid FROM `#@__shop_order` o LEFT JOIN `#@__shop_store` s ON s.`id` = o.`store` WHERE o.`ordernum` = '$value'");
				$res = $dsql->dsqlOper($archives, "results");
				if($res){

				    $order_amount = $res[0]['amount'];
				    $order_balance = $res[0]['balance'];
				    $order_payprice = $res[0]['payprice'];
				    $order_point = $res[0]['point'];

					// 查询订单商品
					$arc = $dsql->SetQuery("SELECT `proid`, `speid`, `count`, `logistic`, `point`, `balance`, `payprice` FROM `#@__shop_order_product` WHERE `orderid` = ".$res[0]['id']);
					$proList = $dsql->dsqlOper($arc, "results");

					$orderid  = $res[0]['id'];
					$userid   = $res[0]['userid'];
					$uid      = $res[0]['uid'];
					$paydate  = $res[0]['paydate'];

					$totalPrice = $upoint = $ubalance = 0;

					//判断是否已经更新过状态，如果已经更新过则不进行下面的操作
					if($paydate == 0){

						//更新订单状态
						$archives = $dsql->SetQuery("UPDATE `#@__shop_order` SET `orderstate` = 1, `paydate` = '$date', `paytype` = '$paytype' WHERE `ordernum` = '$value'");
						$dsql->dsqlOper($archives, "update");

						foreach ($proList as $k => $val) {
							$proid      = $val['proid'];
							$speid      = $val['speid'];
							$count      = $val['count'];
							$totalPrice += $val['payprice'];
//							$upoint     += $val['point'];
//							$ubalance   += $val['balance'];

							//更新已购买数量
							$sql = $dsql->SetQuery("UPDATE `#@__shop_product` SET `sales` = `sales` + $count, `inventory` = `inventory` - $count WHERE `id` = '$proid'");
							$dsql->dsqlOper($sql, "update");

							//更新库存
							$sql = $dsql->SetQuery("SELECT `specification` FROM `#@__shop_product` WHERE `id` = $proid");
							$ret = $dsql->dsqlOper($sql, "results");
							if($ret){
								$specification = $ret[0]['specification'];
								if(!empty($specification)){
									$nSpec = array();
									$specification = explode("|", $specification);
									foreach ($specification as $k => $v) {
										$specArr = explode(",", $v);
											if($specArr[0] == $speid){
											$spec = explode("#", $v);
											$nCount = $spec[2] - $count;
											$nCount = $nCount < 0 ? 0 : $nCount;
											array_push($nSpec, $spec[0]."#".$spec[1]."#".$nCount);
										}else{
											array_push($nSpec, $v);
										}
									}

									$sql = $dsql->SetQuery("UPDATE `#@__shop_product` SET `specification` = '".join("|", $nSpec)."' WHERE `id` = '$proid'");
									$dsql->dsqlOper($sql, "update");
								}
							}

						}

						//扣除会员积分
						if(!empty($order_point) && $order_point > 0){
							$archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` - '$order_point' WHERE `id` = '$userid'");
							$dsql->dsqlOper($archives, "update");

							//保存操作日志
							$info = $langData['shop'][4][18] . '：' . $value;  //支付商城订单
							$archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$order_point', '$info', '$date')");
							$dsql->dsqlOper($archives, "update");
						}

						//扣除会员余额
						if(!empty($order_balance) && $order_balance > 0){
							$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` - '$order_balance' WHERE `id` = '$userid'");
							$dsql->dsqlOper($archives, "update");

							//保存操作日志，下面已经有冻结的记录，里面包含了余额
							//$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$order_balance', '支付商城订单：$value', '$date')");
							//$dsql->dsqlOper($archives, "update");

//							$totalPrice += $order_balance;
						}


						//增加冻结金额
                        $order_freeze = $order_balance + $order_payprice;
						$archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` + '$order_freeze' WHERE `id` = '$userid'");
						$dsql->dsqlOper($archives, "update");

						//保存操作日志
						$info = $langData['shop'][4][19] . '：' . $value;  //商城消费
						$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$order_amount', '$info', '$date')");
						$dsql->dsqlOper($archives, "update");



						//支付成功，会员消息通知
						$paramUser = array(
							"service"  => "member",
							"type"     => "user",
							"template" => "orderdetail",
							"action"   => "shop",
							"id"       => $orderid
						);

						$paramBusi = array(
							"service"  => "member",
							"template" => "orderdetail",
							"action"   => "shop",
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
							"order" => $value,
							"amount" => $order_amount,
							"title" => "商城订单",
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
							"title" => "商城订单",
							"order" => $value,
							"amount" => $order_amount,
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
		$archives = $dsql->SetQuery("SELECT o.`id`, o.`ordernum`, s.`userid` FROM `#@__shop_order` o LEFT JOIN `#@__shop_store` s ON s.`id` = o.`store` WHERE o.`id` = '$id' AND o.`userid` = '$uid' AND (o.`orderstate` = 1 OR o.`orderstate` = 6 OR (o.`orderstate` = 2 AND o.`paydate` != 0)) AND o.`ret-state` = 0");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$userid = $results[0]['userid'];  //卖家会员ID
			$ordernum = $results[0]['ordernum'];  //订单号

			// 查询订单商品
			$orderprice = 0;
			$arc = $dsql->SetQuery("SELECT `count`, `logistic`, `price` FROM `#@__shop_order_product` WHERE `orderid` = ".$id);
			$proList = $dsql->dsqlOper($arc, "results");
			if($proList){
				foreach ($proList as $key => $value) {
					$orderprice += $value['count'] * $value['price'] + $value['logistic'];
				}
			}

			$paramBusi = array(
				"service"  => "member",
				"template" => "orderdetail",
				"action"   => "shop",
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
                    'keyword1' => '退款原因',
                    'keyword2' => '退款金额'
                )
            );

			updateMemberNotice($userid, "会员-订单退款通知", $paramBusi, $config);


			$date       = GetMkTime(time());
			$sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `orderstate` = 6, `ret-state` = 1, `ret-type` = '$type', `ret-note` = '$content', `ret-pics` = '$pics', `ret-date` = '$date' WHERE `id` = ".$id);
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
		$archives = $dsql->SetQuery("SELECT o.`ordernum`, o.`userid`, o.`amount`, o.`logistic`, o.`balance`, s.`userid` uid FROM `#@__shop_order` o LEFT JOIN `#@__shop_store` s ON s.`id` = o.`store` WHERE o.`id` = '$id' AND o.`userid` = '$uid' AND o.`orderstate` = 6");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			//更新订单状态
			$sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `orderstate` = '3' WHERE `id` = ".$id);
			$dsql->dsqlOper($sql, "update");


			//将订单费用转至商家账户
			$ordernum = $results[0]['ordernum'];
			$userid   = $results[0]['userid'];
			$uid      = $results[0]['uid'];
			$amount   = $results[0]['amount'];
			$logistic = $results[0]['logistic'];
			$balance  = $results[0]['balance'];
			$totalMoney = $logistic;
			$freezeMoney = 0;

			//计算费用
			$sql = $dsql->SetQuery("SELECT `price`, `count`, `logistic`, `discount`, `balance`, `payprice` FROM `#@__shop_order_product` WHERE `orderid` = $id");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				foreach($ret as $key => $val){
					$totalMoney += $val['price'] * $val['count'] + $val['discount'];
					$freezeMoney += $val['balance'] + $val['payprice'];
				}
			}

			if($amount > 0){

				//扣除佣金
				global $cfg_shopFee;
				$cfg_shopFee = (float)$cfg_shopFee;

				$fee = $amount * $cfg_shopFee / 100;
				$fee = $fee < 0.01 ? 0 : $fee;
				$amount_ = sprintf('%.2f', $amount - $fee);

				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$amount_' WHERE `id` = '$uid'");
				$dsql->dsqlOper($archives, "update");

				//保存操作日志
				$now = GetMkTime(time());
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '1', '$amount_', '商城订单：$ordernum', '$now')");
				$dsql->dsqlOper($archives, "update");
			}


			//减去冻结金额
			$archives = $dsql->SetQuery("UPDATE `#@__member` SET `freeze` = `freeze` - '$balance' WHERE `id` = '$userid'");
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
			// $archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '0', '$balance', '商城消费：$ordernum', '$now')");
			// $dsql->dsqlOper($archives, "update");


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
				"amount" => $amount,
				"fields" => array(
					'keyword1' => '商品信息',
					'keyword2' => '订单金额',
					'keyword3' => '订单状态'
				)
			);

			updateMemberNotice($uid, "会员-商品成交通知", $paramBusi, $config);


			//返积分
			(new member())->returnPoint("shop", $userid, $amount, $ordernum);

			return $langData['siteConfig'][20][244];  //操作成功

		}else{
			return array("state" => 200, "info" => $langData['shop'][4][23]);  //操作失败，请核实订单状态后再操作！
		}
	}



	/**
	 * 商家发货
	 */
	public function delivery(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id      = $this->param['id'];
		$company = $this->param['company'];
		$number  = $this->param['number'];

		if(empty($id) || empty($company) || empty($number)) return array("state" => 200, "info" => $langData['shop'][4][20]);  //数据不完整，请检查！

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		//验证订单
		$archives = $dsql->SetQuery("SELECT o.`id`, o.`userid`, o.`ordernum` FROM `#@__shop_order` o LEFT JOIN `#@__shop_store` s ON o.`store` = s.`id` LEFT JOIN `#@__member` m ON s.`userid` = m.`id` WHERE o.`id` = '$id' AND m.`id` = '$uid' AND o.`orderstate` = 1");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$userid = $results[0]['userid'];
			$ordernum = $results[0]['ordernum'];

			$paramBusi = array(
				"service"  => "member",
				"type"     => "user",
				"template" => "orderdetail",
				"action"   => "shop",
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
			$sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `orderstate` = 6, `exp-company` = '$company', `exp-number` = '$number', `exp-date` = '$now' WHERE `id` = ".$id);
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
		$archives = $dsql->SetQuery("SELECT o.`id`, o.`ordernum`, o.`userid`, o.`balance`, o.`point`, o.`logistic`, o.`payprice`, s.`userid` uid FROM `#@__shop_order` o LEFT JOIN `#@__shop_store` s ON o.`store` = s.`id` LEFT JOIN `#@__member` m ON s.`userid` = m.`id` WHERE o.`id` = '$id' AND m.`id` = '$uid' AND o.`orderstate` = 6 AND o.`ret-state` = 1");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$orderid    = $results[0]['id'];         //需要退回的订单ID
			$ordernum   = $results[0]['ordernum'];   //需要退回的订单号
			$userid     = $results[0]['userid'];     //需要退回的会员ID
			$uid        = $results[0]['uid'];        //商家会员ID
			$balance    = $results[0]['balance'];    //余额支付
			$point      = $results[0]['point'];      //积分支付
			$payprice   = $results[0]['payprice'];   //实际支付
			$logistic   = $results[0]['logistic'];   //运费
			$totalMoney = $balance + $payprice;
			$totalPoint = 0;

//			$sql = $dsql->SetQuery("SELECT `point`, `balance`, `payprice` FROM `#@__shop_order_product` WHERE `orderid` = '$orderid'");
//			$ret = $dsql->dsqlOper($sql, "results");
//			if($ret){
//				foreach($ret as $key => $val){
//					$totalMoney += $val['balance'] + $val['payprice'];
//					$totalPoint += $val['point'];
//				}
//			}

			//更新订单状态
			$now = GetMkTime(time());
			$sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `ret-state` = 0, `orderstate` = 7, `ret-ok-date` = '$now' WHERE `id` = ".$id);
			$dsql->dsqlOper($sql, "update");

			//退回积分
			if(!empty($point)){
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` + '$point' WHERE `id` = '$userid'");
				$dsql->dsqlOper($archives, "update");

				//保存操作日志
				$info = $langData['shop'][4][25] . '：' . $ordernum;  //商城订单退回
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$point', '$info', '$now')");
				$dsql->dsqlOper($archives, "update");
			}

			//退回余额
			if($totalMoney > 0){
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$totalMoney' WHERE `id` = '$userid'");
				$dsql->dsqlOper($archives, "update");

				//保存操作日志
				$info = $langData['shop'][4][25] . '：' . $ordernum;  //商城订单退回
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
				"action"   => "shop",
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
		$archives = $dsql->SetQuery("SELECT o.`id`, o.`userid`, o.`ordernum` FROM `#@__shop_order` o LEFT JOIN `#@__shop_store` s ON o.`store` = s.`id` LEFT JOIN `#@__member` m ON s.`userid` = m.`id` WHERE o.`id` = '$id' AND m.`id` = '$uid' AND o.`orderstate` = 6 AND o.`ret-state` = 1");
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$userid = $results[0]['userid'];
			$ordernum = $results[0]['ordernum'];

			// 查询订单商品
			$orderprice = 0;
			$arc = $dsql->SetQuery("SELECT `count`, `logistic`, `price` FROM `#@__shop_order_product` WHERE `orderid` = ".$id);
			$proList = $dsql->dsqlOper($arc, "results");
			if($proList){
				foreach ($proList as $key => $value) {
					$orderprice += $value['count'] * $value['price'] + $value['logistic'];
				}
			}

			$paramBusi = array(
				"service"  => "member",
				"type"     => "user",
				"template" => "orderdetail",
				"action"   => "shop",
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
			$sql = $dsql->SetQuery("UPDATE `#@__shop_order` SET `ret-s-note` = '$content', `ret-s-pics` = '$pics', `ret-s-date` = '$now' WHERE `id` = ".$id);
			$dsql->dsqlOper($sql, "update");

			return $langData['siteConfig'][21][147];  //回复成功！

		}else{
			return array("state" => 200, "info" => $langData['shop'][4][27]);  //回复失败，请核实订单状态后再操作！
		}
	}




	/**
     * 商品评论
     * @return array
     */
	public function common(){
		global $dsql;
		global $userLogin;
		global $langData;
		$pageinfo = $list = array();
		$id = $filter = $orderby = $page = $pageSize = $where = $px = "";

		if(!is_array($this->param)){
			return array("state" => 200, "info" => '格式错误！');
		}else{
			$id       = $this->param['id'];
			$filter   = $this->param['filter'];
			$orderby  = $this->param['orderby'];
			$page     = $this->param['page'];
			$pageSize = $this->param['pageSize'];
		}

		if(empty($id)) return array("state" => 200, "info" => '格式错误！');

		//筛选
		if(!empty($filter)){
			if($filter == "pic"){
				$where .= " AND `pics` <> ''";
			}elseif($filter == "h"){
				$where .= " AND `rating` = 1";
			}elseif($filter == "z"){
				$where .= " AND `rating` = 2";
			}elseif($filter == "c"){
				$where .= " AND `rating` = 3";
			}
		}

		//排序
		$px = " ORDER BY `rating` ASC, `floor` ASC, `id` DESC";
		if(!empty($orderby)){
			if($orderby == "time"){
				$px = " ORDER BY `floor` ASC, `id` DESC";
			}
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `rating`, `specation`, `userid`, `pics`, `content`, `dtime` FROM `#@__shop_common` WHERE `pid` = ".$id." AND `ischeck` = 1".$where.$px);
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
				$list[$key]['rating']  = $val['rating'];
				$list[$key]['specation']  = str_replace("$$$", "，", $val['specation']);

				$imgArr = array();
				$pics = $val['pics'];
				if(!empty($pics)){
					$picArr = explode(",", $pics);
					foreach ($picArr as $k => $v) {
						$imgArr[$k] = getFilePath($v);
					}
				}
				$list[$key]['pics'] = $imgArr;

				$list[$key]['content'] = $val['content'];
				$list[$key]['dtime']   = $val['dtime'];

				$userinfo = $userLogin->getMemberInfo($val['userid']);
				if(is_array($userinfo)){
					$list[$key]['user']['id'] = $val['userid'];
					$list[$key]['user']['photo'] = $userinfo['photo'];
					$list[$key]['user']['nickname'] = $userinfo['nickname'];
				}else{
					$list[$key]['user']['id'] = "";
					$list[$key]['user']['photo'] = "";
					$list[$key]['user']['nickname'] = $langData['siteConfig'][21][120];  //游客
				}

			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
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

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！

		$orderid = $param['orderid'];
		$rating  = $param['rating'];
		$score1  = $param['score1'];
		$score2  = $param['score2'];
		$score3  = $param['score3'];
		$note    = $param['note'];
		$img     = $param['img'];
		$ip      = GetIP();
		$ipaddr  = getIpAddr($ip);
		$date    = GetMkTime(time());

		if(empty($orderid)) return array("state" => 200, "info" => $langData['shop'][4][28]);  //订单信息提交失败！

		//订单详细
		$this->param = $orderid;
		$orderDetail = $this->orderDetail();

		if($orderDetail['orderstate'] == 3){

			foreach($orderDetail['product'] as $key => $value){

				$pid   = $value['id'];
				$speid = $value['speid'];
				$specation = $value['specation'];
				$rat  = $rating[$pid."_".$speid];
				$sco1 = $score1[$pid."_".$speid];
				$sco2 = $score2[$pid."_".$speid];
				$sco3 = $score3[$pid."_".$speid];
				$not  = $note[$pid."_".$speid];
				$im   = $img[$pid."_".$speid];

				if(empty($rat)) return array("state" => 200, "info" => $langData['shop'][4][29]);  //请选择商品评价！
				if(empty($sco1)) return array("state" => 200, "info" => $langData['shop'][4][30]);  //请给商品描述打分
				if(empty($sco2)) return array("state" => 200, "info" => $langData['shop'][4][31]);  //请给商家服务打分
				if(empty($sco3)) return array("state" => 200, "info" => $langData['shop'][4][32]);  //请给商品质量打分
				if(empty($not)) return array("state" => 200, "info" => $langData['shop'][4][33]);  //请输入评价内容！

				//修改
				if($orderDetail['common'] == 1){
					$archives = $dsql->SetQuery("UPDATE `#@__shop_common` SET `rating` = '$rat', `score1` = '$sco1', `score2` = '$sco2', `score3` = '$sco3', `pics` = '$im', `content` = '$not', `dtime` = '$date', `ip` = '$ip', `ipaddr` = '$ipaddr', `ischeck` = 0 WHERE `pid` = '$pid' AND `aid` = '$orderid'");
					$dsql->dsqlOper($archives, "update");

				//新增
				}else{
					$archives = $dsql->SetQuery("INSERT INTO `#@__shop_common` (`aid`, `pid`, `speid`, `specation`, `floor`, `userid`, `rating`, `score1`, `score2`, `score3`, `pics`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `ischeck`) VALUES ('$orderid', '$pid', '$speid', '$specation', '0', '$userid', '$rat', '$sco1', '$sco2', '$sco3', '$im', '$not', '$date', '$ip', '$ipaddr', 0, 0, 0)");
					$dsql->dsqlOper($archives, "update");
				}

			}

			$archives = $dsql->SetQuery("UPDATE `#@__shop_order` SET `common` = 1 WHERE `id` = '$orderid'");
			$dsql->dsqlOper($archives, "update");
			return $langData['siteConfig'][20][196];  //评价成功！

		}else{
			return array("state" => 200, "info" => $langData['shop'][4][34]);  //订单状态有误！
		}

	}


	/**
     * 商城订单列表
     * @return array
     */
	public function orderList(){
		global $dsql;
		global $langData;
		$pageinfo = $list = array();
		$store = $state = $userid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$store    = $this->param['store'];
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
		if(empty($userid)) return array("state" => 200, "info" => '会员ID不得为空！');

		//个人会员订单列表
		if(empty($store)){
			$where = ' `userid` = '.$userid;

		//商家会员订单列表
		}else{

			if(!verifyModuleAuth(array("module" => "shop"))){
				return array("state" => 200, "info" => $langData['shop'][4][1]);  //商家权限验证失败！
			}

			$sid = 0;
			$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
			$userResult = $dsql->dsqlOper($userSql, "results");
			if(!$userResult){
				return array("state" => 200, "info" => $langData['shop'][4][36]);  //您还未开通商城店铺！
			}else{
				$sid = $userResult[0]['id'];
			}

			$where = ' `store` = '.$sid;
		}

		$archives = $dsql->SetQuery("SELECT " .
								"`id`, `ordernum`, `store`, `userid`, `orderstate`, `orderdate`, `paytype`, `logistic`, `ret-state`, `exp-date`, `common` " .
								"FROM `#@__shop_order` " .
								"WHERE".$where);

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		//未付款
		$unpaid = $dsql->dsqlOper($archives." AND `orderstate` = 0", "totalCount");
		//未使用
		$ongoing = $dsql->dsqlOper($archives." AND `orderstate` = 1", "totalCount");
		//已使用
		$success = $dsql->dsqlOper($archives." AND `orderstate` = 3", "totalCount");
		//等待退款
		$refunded = $dsql->dsqlOper($archives." AND `orderstate` != 3 AND `ret-state` = 1", "totalCount");
		//待评价
		$rates = $dsql->dsqlOper($archives." AND `orderstate` = 3 AND `common` = 0", "totalCount");
		//已发货/待收货
		$recei = $dsql->dsqlOper($archives." AND `orderstate` = 6 AND `exp-date` != 0", "totalCount");
		//退款成功
		$closed = $dsql->dsqlOper($archives." AND `orderstate` = 7", "totalCount");
		//关闭/失败
		$cancel = $dsql->dsqlOper($archives." AND `orderstate` = 10", "totalCount");

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
			"cancel"   => $cancel
		);

		if($totalCount == 0) return array("pageInfo" => $pageinfo, "list" => array());

		$where = "";
		if($state != "" && $state != 4 && $state != 5 && $state != 6){
			$where = " AND `orderstate` = " . $state;
		}

		//退款
		if($state == 4){
			$where = " AND `orderstate` != 3 AND `ret-state` = 1";
		}

		//待评价
		if($state == 5){
			$where = " AND `orderstate` = 3 AND `common` = 0";
		}

		//已发货
		if($state == 6){
			$where = " AND `orderstate` = 6 AND `exp-date` != 0";
		}

		$atpage = $pageSize*($page-1);
		$where .= " ORDER BY `id` DESC LIMIT $atpage, $pageSize";

		$results = $dsql->dsqlOper($archives.$where, "results");
		if($results){

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

			$i = 0;
			foreach($results as $key => $val){

				$sql = $dsql->SetQuery("SELECT * FROM `#@__shop_order_product` WHERE `orderid` = ".$val['id']);
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
							$list[$i]['store'] = array(
								"id"     => $storeConfig['id'],
								"title"  => $storeConfig['title'],
								"domain" => $storeConfig['domain'],
								"qq"     => $storeConfig['qq']
							);
						}else{
							$list[$i]['store'] = array(
								"id"     => 0,
								"title"  => $langData['shop'][4][37]  //官方直营
							);
						}

					}

					$list[$i]['id']          = $val['id'];
					$list[$i]['ordernum']    = $val['ordernum'];
					$list[$i]['logistic']    = $val['logistic'];
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
						}elseif($val["paytype"] == "money"){
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
						$this->param = $v['proid'];
						$detail = $this->detail();

						$list[$i]['product'][$k]['title'] = $detail['title'];
						$list[$i]['product'][$k]['litpic'] = $detail['litpic'];
						$list[$i]['product'][$k]['url'] = str_replace("%id%", $v['proid'], $urlParam);

						$list[$i]['product'][$k]['price'] = $v['price'];
						$list[$i]['product'][$k]['count'] = $v['count'];
						$list[$i]['product'][$k]['specation'] = $v['specation'];

						//未付款的不计算积分和余额部分
						if($val['orderstate'] == 0){
							$totalPayPrice += $v['price'] * $v['count'] + $v['discount'];
						}else{
//							$totalPayPrice += $v['payprice'];
							$totalPayPrice += $v['price'] * $v['count'] + $v['discount'];
						}
					}

                    $totalPayPrice += $val['logistic'];
					$list[$i]['totalPayPrice'] = sprintf("%.2f", $totalPayPrice);

					$i++;
				}

			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);

	}


	/**
     * 商城订单详细
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
		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//主表信息
		$archives = $dsql->SetQuery("SELECT o.* FROM `#@__shop_order` o LEFT JOIN `#@__shop_store` s ON s.`id` = o.`store` WHERE (o.`userid` = '$userid' OR s.`userid` = '$userid') AND o.`id` = ".$id);
		$results = $dsql->dsqlOper($archives, "results");

		if(!empty($results)){
			$results = $results[0];

			$orderDetail["ordernum"]   = $results["ordernum"];
			$orderDetail["store"]      = $results["store"];
			$orderDetail["orderstate"] = $results["orderstate"];
			$orderDetail["orderdate"]  = $results["orderdate"];
			$orderDetail["common"]     = $results["common"];
			$orderDetail["logistic"]   = $results["logistic"];
			$orderDetail["userid"]     = $results["userid"];


			//店铺信息
			$store = array();
			if($results['store'] != 0){
				$storeHandels = new handlers("shop", "storeDetail");
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


			//未付款的提供付款链接
			if($results['orderstate'] == 0){
				$RenrenCrypt = new RenrenCrypt();
				$encodeid = base64_encode($RenrenCrypt->php_encrypt($results["ordernum"]));

				$param = array(
					"service"     => "shop",
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
				}elseif($results["paytype"] == "money"){
					$payname = $langData['siteConfig'][19][363]; //余额
				}
				$orderDetail["paytype"]   = $payname;
			}

			$orderDetail["paydate"]   = $results["paydate"];

			//快递公司&单号
			$orderDetail["expCompany"] = $results["exp-company"];
			$orderDetail["expNumber"]  = $results["exp-number"];
			$orderDetail["expDate"]    = $results["exp-date"];

			//快递跟踪
            $expTrack = $results["exp-track"];
            if(!$expTrack && ($results["orderstate"] == 3 || $results["orderstate"] == 4 || $results["orderstate"] == 6 || $results["orderstate"] == 7) && $results["exp-company"] != 'else'){
                $expTrack = getExpressTrack($results["exp-company"], $results["exp-number"], 'shop_order', $id);
            }
            $orderDetail["expTrack"] = $expTrack ? unserialize($expTrack) : array();

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

			$sql = $dsql->SetQuery("SELECT * FROM `#@__shop_order_product` WHERE `orderid` = ".$id);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$p = 0;
				$proDetail = array();
				foreach ($ret as $key => $value) {

					//查询商品详细信息
					global $oper;
					$oper = "user";
					$this->param = $value['proid'];
					$detailConfig = $this->detail();

					$proDetail[$p]['id']        = $detailConfig['id'];
					$proDetail[$p]['title']     = $detailConfig['title'];
					$proDetail[$p]['litpic']    = $detailConfig['litpic'];
					$proDetail[$p]['orderid']   = $value['orderid'];
					$proDetail[$p]['proid']     = $value['proid'];
					$proDetail[$p]['speid']     = $value['speid'];
					$proDetail[$p]['specation'] = $value['specation'];
					$proDetail[$p]['price']     = $value['price'];
					$proDetail[$p]['count']     = $value['count'];
					$proDetail[$p]['logistic']  = $value['logistic'];
					$proDetail[$p]['discount']  = $value['discount'];
					$proDetail[$p]['point']     = $value['point'];
					$proDetail[$p]['balance']   = $value['balance'];

					//评价
					if($results['orderstate'] == 3){

						// $sql = $dsql->SetQuery("SELECT `rating`, `score1`, `score2`, `score3`, `pics`, `content`, `ischeck` FROM `#@__shop_common` WHERE `aid` = ".$id." AND `speid` = '".$value['speid']."' AND `pid` = ".$value['proid']);
						$sql = $dsql->SetQuery("SELECT `rating`, `sco1` as score1, `sco2` as score2, `sco3` as score3, `pics`, `content`, `ischeck` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'shop-order' AND `oid` = '$id' AND `speid` = '".$value['speid']."' AND `pid` = 0");
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
					$payprice = $results['orderstate'] == 0 ? $value['price'] * $value['count'] + $value['discount'] : $value['payprice'];
					$proDetail[$p]['payprice']  = sprintf("%.2f", $payprice);
					$p++;

					$totalPoint    += $value['point'];
					$totalBalance  += $value['balance'];
					$totalPayPrice += $value['price'] * $value['count'] + $value['discount'];

				}
			}

            $totalPayPrice += $results["logistic"];

			$orderDetail['product'] = $proDetail;
			$orderDetail['totalBalance'] = sprintf("%.2f", $totalBalance);
			$orderDetail['totalPayPrice'] = sprintf("%.2f", $totalPayPrice);

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

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_order` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['userid'] == $uid){

				if($results['orderstate'] == 0){
					$archives = $dsql->SetQuery("DELETE FROM `#@__shop_order` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");

					$archives = $dsql->SetQuery("DELETE FROM `#@__shop_order_product` WHERE `orderid` = ".$id);
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
		* 配置商铺
		* @return array
		*/
	public function storeConfig(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid      = $userLogin->getMemberID();
		$param       = $this->param;
        $cityid      = (int)$param['cityid'];
		$industry    = (int)$param['industry'];
		$addrid      = (int)$param['addrid'];
		$company     = filterSensitiveWords(addslashes($param['company']));
		$title       = filterSensitiveWords(addslashes($param['title']));
		$referred    = filterSensitiveWords(addslashes($param['referred']));
		$address     = filterSensitiveWords(addslashes($param['address']));
		$project     = filterSensitiveWords(addslashes($param['project']));
		$logo        = $param['logo'];
		$people      = filterSensitiveWords(addslashes($param['people']));
		$contact     = filterSensitiveWords(addslashes($param['contact']));
		$tel         = filterSensitiveWords(addslashes($param['telphone']));
		$qq          = filterSensitiveWords(addslashes($param['qq']));
		$wechatcode  = filterSensitiveWords(addslashes($param['wechatcode']));
		$wechatqr    = $param['wechatqr'];
		$body        = filterSensitiveWords(addslashes($param['body']));
		$pubdate     = GetMkTime(time());
		$imglist     = $param['imglist'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		//验证会员类型
		$userDetail = $userLogin->getMemberInfo();
		if($userDetail['userType'] != 2){
			return array("state" => 200, "info" => $langData['siteConfig'][21][127]);  //账号验证错误，操作失败！
		}

		if(!verifyModuleAuth(array("module" => "shop"))){
			return array("state" => 200, "info" => $langData['shop'][4][1]);  //商家权限验证失败！
		}

		if(empty($industry)){
			return array("state" => 200, "info" => $langData['shop'][4][41]);  //请选择所属行业
		}

		if(empty($addrid)){
			return array("state" => 200, "info" => $langData['siteConfig'][20][68]);  //请选择所在区域
		}

		if(empty($company)){
			return array("state" => 200, "info" => $langData['shop'][4][42]);  //请输入公司名称
		}

		if(empty($title)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][128]);  //请输入店铺名称
		}

		if(empty($referred)){
			return array("state" => 200, "info" => $langData['shop'][4][43]);  //请输入店铺简称
		}

		if(empty($address)){
			return array("state" => 200, "info" => $langData['shop'][4][44]);  //请输入公司地址
		}

		if(empty($logo)){
			return array("state" => 200, "info" => $langData['shop'][4][45]);  //请上传店铺LOGO
		}

		if(empty($people)){
			return array("state" => 200, "info" => $langData['shop'][4][46]);  //请输入联系人
		}

		if(empty($contact)){
			return array("state" => 200, "info" => $langData['siteConfig'][20][433]);  //请输入联系电话
		}

		if(empty($tel)){
			return array("state" => 200, "info" => $langData['shop'][4][47]);  //请输入客服电话
		}

		if(empty($body)){
			return array("state" => 200, "info" => $langData['shop'][4][48]);  //请输入详细介绍
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");

		//新商铺
		if(!$userResult){

			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__shop_store` (`cityid`, `userid`, `title`, `company`, `referred`, `addrid`, `address`, `industry`, `project`, `logo`, `people`, `contact`, `tel`, `qq`, `note`, `click`, `weight`, `state`, `certi`, `rec`, `pubdate`, `wechatcode`, `wechatqr`, `pic`) VALUES ('$cityid', '$userid', '$title', '$company', '$referred', '$addrid', '$address', '$industry', '$project', '$logo', '$people', '$contact', '$tel', '$qq', '$body', '1', '1', '0', '0', '0', '$pubdate', '$wechatcode', '$wechatqr', '$imglist')");
			$aid = $dsql->dsqlOper($archives, "lastid");

			if(is_numeric($aid)){

				//后台消息通知
				updateAdminNotice("shop", "store");

				return $langData['shop'][4][49];  //配置成功，您的商铺正在审核中，请耐心等待！
			}else{
				return array("state" => 200, "info" => $langData['shop'][4][50]);  //配置失败，请查检您输入的信息是否符合要求！
			}

		//更新商铺信息
		}else{

			//保存到主表
			$archives = $dsql->SetQuery("UPDATE `#@__shop_store` SET `cityid` = '$cityid', `company` = '$company', `title` = '$title', `referred` = '$referred', `addrid` = '$addrid', `address` = '$address', `industry` = '$industry', `project` = '$project', `logo` = '$logo', `people` = '$people', `contact` = '$contact', `tel` = '$tel', `qq` = '$qq', `note` = '$body', `state` = 0, `wechatcode` = '$wechatcode', `wechatqr` = '$wechatqr', `pic` = '$imglist' WHERE `userid` = ".$userid);
			$results = $dsql->dsqlOper($archives, "update");

			if($results == "ok"){

				//后台消息通知
				updateAdminNotice("shop", "store");

				return $langData['siteConfig'][6][39];  //保存成功
			}else{
				return array("state" => 200, "info" => $langData['shop'][4][50]);  //配置失败，请查检您输入的信息是否符合要求！
			}

		}

	}




	/**
		* 删除商铺商品分类
		* @return array
		*/
	public function delCategory(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		$id     = $this->param['id'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(empty($id)){
			return array("state" => 200, "info" => $langData['siteConfig'][20][300]);  //删除失败
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$storeid = $ret[0]['id'];

			$ids = explode(",", $id);
			foreach ($ids as $key => $value) {

				//验证权限
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_category` WHERE `type` = $storeid AND `id` = ".$value);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){

					$sql = $dsql->SetQuery("DELETE FROM `#@__shop_category` WHERE `id` = ".$value." OR `parentid` = ".$value);
					if(!$dsql->dsqlOper($sql, "update") == "ok"){
						return array("state" => 200, "info" => $langData['siteConfig'][20][300]);  //删除失败
					}

				}else{
					return array("state" => 200, "info" => $langData['siteConfig'][21][182]);  //账号验证错误，删除失败！
				}

			}
			return $langData['siteConfig'][21][136];  //删除成功！

		}else{
			return array("state" => 200, "info" => $langData['shop'][4][51]);  //您的账户暂未开通商品商铺功能！
		}

	}




	/**
		* 更新商铺商品分类
		* @return array
		*/
	public function updateCategory(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid   = $userLogin->getMemberID();
		$id       = $this->param['id'];
		$typename = $this->param['typename'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(!is_numeric($id)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][179]);  //更新失败，请重试！
		}

		if(empty($typename)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][176]);  //请输入分类名称！
		}

		$typename = cn_substrR($typename,30);

		global $userLogin;
		$userid = $userLogin->getMemberID();
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$storeid = $ret[0]['id'];

			//验证权限
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__shop_category` WHERE `type` = $storeid AND `id` = ".$id);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){

				if($ret[0]['typename'] == $typename){
					return array("state" => 101, "info" => $langData['siteConfig'][21][178]);  //没有变化
				}else{
					$sql = $dsql->SetQuery("UPDATE `#@__shop_category` SET `typename` = '$typename' WHERE `id` = ".$id);
					if($dsql->dsqlOper($sql, "update") == "ok"){
						return $langData['siteConfig'][21][106];  //更新成功！
					}else{
						return array("state" => 200, "info" => $langData['siteConfig'][21][179]);  //更新失败，请重试！
					}
				}

			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][21][180]);  //账号验证错误，更新失败！
			}

		}else{
			return array("state" => 200, "info" => $langData['shop'][20][51]);  //您的账户暂未开通商品商铺功能！
		}

	}




	/**
		* 更新商铺商品分类
		* @return array
		*/
	public function operaCategory(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		$data   = $_POST['data'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(!verifyModuleAuth(array("module" => "shop"))){
			return array("state" => 200, "info" => $langData['shop'][4][1]);  //商家权限验证失败！
		}

		if(empty($data)){
			return array("state" => 200, "info" => $langData['siteConfig'][21][135]);  //请添加分类！
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$storeid = $ret[0]['id'];

			$data = str_replace("\\", '', $data);
			$json = json_decode($data);

			$json = objtoarr($json);
			$json = $this->proTypeAjax($json, 0, "shop_category", $storeid);
			return $json;

		}else{
			return array("state" => 200, "info" => $langData['shop'][4][51]);  //您的账户暂未开通商品商铺功能！
		}


	}

	//更新分类
	public function proTypeAjax($json, $pid = 0, $tab, $tid){
		global $dsql;
		global $langData;
		for($i = 0; $i < count($json); $i++){
			$id = $json[$i]["id"];
			$name = $json[$i]["name"];

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
						$dsql->dsqlOper($archives, "update");
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
		return $langData['siteConfig'][6][39];  //保存成功

	}


	/**
		* 下架商品
		* @return array
		*/
	public function offShelf(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_product` p LEFT JOIN `#@__shop_store` s ON s.`id` = p.`store` WHERE p.`id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];

			if($results['userid'] == $uid){

				$archives = $dsql->SetQuery("UPDATE `#@__shop_product` SET `state` = 2 WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");

				return array("state" => 100, "info" => $langData['siteConfig'][20][244]);  //操作成功
			}else{
				return array("state" => 101, "info" => $langData['shop'][4][39]);  //权限不足，请确认帐户信息后再进行操作！
			}
		}else{
			return array("state" => 101, "info" => $langData['shop'][4][35]);  //商品不存在，或已经删除！
		}

	}


	/**
		* 上架商品
		* @return array
		*/
	public function upShelf(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_product` p LEFT JOIN `#@__shop_store` s ON s.`id` = p.`store` WHERE p.`id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];

			if($results['userid'] == $uid){

				$archives = $dsql->SetQuery("UPDATE `#@__shop_product` SET `state` = 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");

				return array("state" => 100, "info" => $langData['siteConfig'][20][244]);  //操作成功
			}else{
				return array("state" => 101, "info" => $langData['shop'][4][39]);  //权限不足，请确认帐户信息后再进行操作！
			}
		}else{
			return array("state" => 101, "info" => $langData['shop'][4][35]);  //商品不存在，或已经删除！
		}

	}



	/**
		* 运费详情
		* @return array
		*/
	public function logisticTemplate(){
		global $dsql;
		global $userLogin;
		global $langData;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(!empty($id)){
			$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_logistictemplate` WHERE `id` = $id");
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$value = $results[0];
				return getPriceDetail($value["bearFreight"], $value['valuation'], $value['express_start'], $value['express_postage'], $value['express_plus'], $value['express_postageplus'], $value['preferentialStandard'], $value['preferentialMoney']);
			}else{
				return array("state" => 101, "info" => $langData['shop'][4][52]);  //模板不存在！
			}
		}

	}


	/**
		* 发布信息
		* @return array
		*/
	public function put(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid    = $userLogin->getMemberID();
		$param     = $this->param;

		$typeid    = $param['typeid'];
		$brand     = (int)$param['brand'];
		$itemid    = $param['itemid'];
		$title     = filterSensitiveWords(addslashes($param['title']));
		$category  = $param['category'];
		$mprice    = $param['mprice'];
		$price     = $param['price'];
		$logistic  = $param['logistic'];
		$volume    = (float)$param['volume'];
		$weight    = (float)$param['weight'];
		$inventory = $param['inventory'];
		$limit     = $param['limit'];
		$litpic    = $param['litpic'];
		$imglist   = $param['imglist'];
		$video     = $param['video'];
		$body      = filterSensitiveWords(addslashes($param['body']));
		$pubdate   = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['shop'][4][36]);  //您还未开通商城店铺！
		}

		if(!verifyModuleAuth(array("module" => "shop"))){
			return array("state" => 200, "info" => $langData['shop'][4][1]);  //商家权限验证失败！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['shop'][4][53]);  //您的商铺信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['shop'][4][54]);  //您的商铺信息审核失败，请通过审核后再发布！
		}

		$sid = $userResult[0]['id'];

		if($typeid == ""){
			return array("state" => 200, "info" => $langData['shop'][4][55]);  //分类获取失败，请重新选择分类！
		}

		if($itemid == ""){
			return array("state" => 200, "info" => $langData['shop'][4][56]);  //分类属性ID获取失败，请重新选择分类！
		}

		//获取分类下相应属性
		$property = array();
		$propertyName = "item";
		$shopitem = $dsql->SetQuery("SELECT `id`, `typename`, `flag` FROM `#@__shop_item` WHERE `type` = ".$itemid." AND `parentid` = 0 ORDER BY `weight`");
		$shopResults = $dsql->dsqlOper($shopitem, "results");
		foreach($shopResults as $key => $val){

			$id = $val['id'];
			$typeName = $val['typename'];
			$r = strstr($val['flag'], 'r');
			$proval = $_POST[$propertyName.$id];

			if(is_array($proval)){
				if($r && empty($proval)){
					return array("state" => 200, "info" => $langData['siteConfig'][7][2].$typeName.'！');  //请选择
				}
				if(!empty($proval)){
					array_push($property, $id."#".join(",", $proval));
				}
			}else{
				if($r && $proval == ""){
					return array("state" => 200, "info" => $langData['siteConfig'][7][2].$typeName.'！');  //请选择
				}
				if(!empty($proval)){
					array_push($property, $id."#".$proval);
				}
			}
		}
		$property = join("|", $property);


		if($title == ""){
			return array("state" => 200, "info" => $langData['shop'][4][57]);  //请输入商品标题！
		}

		$category = isset($category) ? join(',',$category) : '';

		if(!preg_match("/^0|\d*\.?\d+$/i", $mprice, $matches)){
			return array("state" => 200, "info" => $langData['shop'][4][58]);  //市场价不得为空，类型为数字！
		}

		if(!preg_match("/^0|\d*\.?\d+$/i", $price, $matches)){
			return array("state" => 200, "info" => $langData['shop'][4][59]);  //一口价不得为空，类型为数字！
		}

		if(empty($logistic)){
			return array("state" => 200, "info" => $langData['shop'][4][60]);  //请选择物流运费模板！
		}

		//获取分类下相应规格
		$specifival = array();
		$spearray = array();
		$invent = 0;
		$typeitem = $dsql->SetQuery("SELECT `spe` FROM `#@__shop_type` WHERE `id` = ".$typeid."");
		$typeResults = $dsql->dsqlOper($typeitem, "results");
		if($typeResults){
			$spe = $typeResults[0]['spe'];
			if($spe != ""){
				$spe = explode(",", $spe);
				foreach($spe as $key => $val){
					$speitem = array();
					$speSql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_specification` WHERE `id` = ".$val);
					$speResults = $dsql->dsqlOper($speSql, "results");
					if($speResults){
						$speval = $_POST["spe".$speResults[0]['id']];
						if(!empty($speval) != ""){
							array_push($spearray, $speval);
						}
					}
				}
			}
		}

		if(!empty($spearray)){
			if(count($spearray) > 1){
				$spearray = descartes($spearray);
			}else{
				$spearray = $spearray[0];
			}
			foreach($spearray as $key => $val){
				$speid = $val;
				if(is_array($val)){
					$speid = join("-", $val);
				}
				$spemprice = $_POST["f_mprice_".$speid];
				$speprice = $_POST["f_price_".$speid];
				$speinventory = $_POST["f_inventory_".$speid];
				if(!preg_match("/^0|\d*\.?\d+$/i", $spemprice, $matches)){
					return array("state" => 200, "info" => $langData['shop'][4][61]);  //规格表中价格不得为空，类型为数字！
				}elseif(!preg_match("/^0|\d*\.?\d+$/i", $speprice, $matches)){
					return array("state" => 200, "info" => $langData['shop'][4][62]);  //规格表中库存不得为空，类型为数字！
				}elseif(!preg_match("/^0|\d*\.?\d+$/i", $speinventory, $matches)){
					return array("state" => 200, "info" => $langData['shop'][4][62]);  //规格表中库存不得为空，类型为数字！
				}else{
					$invent += $speinventory;
					array_push($specifival, $speid.",".$spemprice."#".$speprice."#".$speinventory);
				}
			}
		}

		if(!empty($specifival)){
			$specifival = join("|", $specifival);
			$inventory = $invent;
		}else{
			$specifival = "";

			if(!preg_match("/^0|\d*\.?\d+$/i", $inventory, $matches)){
				return array("state" => 200, "info" => $langData['shop'][4][63]);  //库存不得为空，类型为数字！
			}
		}

		if(empty($litpic)){
			return array("state" => 200, "info" => $langData['shop'][4][64]);  //请上传商品缩略图！
			exit();
		}

		if(empty($imglist)){
			return array("state" => 200, "info" => $langData['shop'][4][65]);  //请上传商品图集！
			exit();
		}

		if(trim($body) == ''){
			return array("state" => 200, "info" => $langData['shop'][4][66]);  //请输入商品描述
		}

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__shop_product` (`type`, `title`, `brand`, `property`, `store`, `category`, `mprice`, `price`, `logistic`, `volume`, `weight`, `specification`, `inventory`, `limit`, `sales`, `litpic`, `sort`, `click`, `state`, `pics`, `body`, `mbody`, `pubdate`, `video`) VALUES ('$typeid', '$title', '$brand', '$property', '$sid', '$category', '$mprice', '$price', '$logistic', '$volume', '$weight', '$specifival', '$inventory', '$limit', '0', '$litpic', '1', '1', '0', '$imglist', '$body', '$body', '$pubdate', '$video')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){

			//后台消息通知
			updateAdminNotice("shop", "detail");

			return $aid;
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][142]);  //发布到数据时发生错误，请检查字段内容！
		}

	}



	/**
		* 修改信息
		* @return array
		*/
	public function edit(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid    = $userLogin->getMemberID();
		$param     = $this->param;

		$id        = $param['id'];
		$typeid    = $param['typeid'];
		$brand     = (int)$param['brand'];
		$itemid    = $param['itemid'];
		$title     = filterSensitiveWords(addslashes($param['title']));
		$category  = $param['category'];
		$mprice    = $param['mprice'];
		$price     = $param['price'];
		$logistic  = $param['logistic'];
		$volume    = (float)$param['volume'];
		$weight    = (float)$param['weight'];
		$inventory = $param['inventory'];
		$limit     = $param['limit'];
		$litpic    = $param['litpic'];
		$imglist   = $param['imglist'];
		$video     = $param['video'];
		$body      = filterSensitiveWords(addslashes($param['body']));
		$pubdate   = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['shop'][4][36]);  //您还未开通商城店铺！
		}

		if(!verifyModuleAuth(array("module" => "shop"))){
			return array("state" => 200, "info" => $langData['shop'][4][1]);  //商家权限验证失败！
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => $langData['shop'][4][53]);  //您的商铺信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => $langData['shop'][4][54]);  //您的商铺信息审核失败，请通过审核后再发布！
		}

		$sid = $userResult[0]['id'];

		if($id == ""){
			return array("state" => 200, "info" => $langData['shop'][4][67]);  //信息提交失败，请重试！
		}

		if($typeid == ""){
			return array("state" => 200, "info" => $langData['shop'][4][55]);  //分类获取失败，请重新选择分类！
		}

		if($itemid == ""){
			return array("state" => 200, "info" => $langData['shop'][4][56]);  //分类属性ID获取失败，请重新选择分类！
		}

		//获取分类下相应属性
		$property = array();
		$propertyName = "item";
		$shopitem = $dsql->SetQuery("SELECT `id`, `typename`, `flag` FROM `#@__shop_item` WHERE `type` = ".$itemid." AND `parentid` = 0 ORDER BY `weight`");
		$shopResults = $dsql->dsqlOper($shopitem, "results");
		foreach($shopResults as $key => $val){

			$pid = $val['id'];
			$typeName = $val['typename'];
			$r = strstr($val['flag'], 'r');
			$proval = $_POST[$propertyName.$pid];

			if(is_array($proval)){
				if($r && empty($proval)){
					return array("state" => 200, "info" => $langData['siteConfig'][7][2].$typeName.'！');  //请选择
				}
				if(!empty($proval)){
					array_push($property, $pid."#".join(",", $proval));
				}
			}else{
				if($r && $proval == ""){
					return array("state" => 200, "info" => $langData['siteConfig'][7][2].$typeName.'！');  //请选择
				}
				if(!empty($proval)){
					array_push($property, $pid."#".$proval);
				}
			}
		}
		$property = join("|", $property);


		if($title == ""){
			return array("state" => 200, "info" => $langData['shop'][4][57]);  //请输入商品标题！
		}

		$category = isset($category) ? join(',',$category) : '';

		if(!preg_match("/^0|\d*\.?\d+$/i", $mprice, $matches)){
			return array("state" => 200, "info" => $langData['shop'][4][58]);  //市场价不得为空，类型为数字！
		}

		if(!preg_match("/^0|\d*\.?\d+$/i", $price, $matches)){
			return array("state" => 200, "info" => $langData['shop'][4][59]);  //一口价不得为空，类型为数字！
		}

		if(empty($logistic)){
			return array("state" => 200, "info" => $langData['shop'][4][60]);  //请选择物流运费模板！
		}

		//获取分类下相应规格
		$specifival = array();
		$spearray = array();
		$invent = 0;
		$typeitem = $dsql->SetQuery("SELECT `spe` FROM `#@__shop_type` WHERE `id` = ".$typeid."");
		$typeResults = $dsql->dsqlOper($typeitem, "results");
		if($typeResults){
			$spe = $typeResults[0]['spe'];
			if($spe != ""){
				$spe = explode(",", $spe);
				foreach($spe as $key => $val){
					$speitem = array();
					$speSql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_specification` WHERE `id` = ".$val);
					$speResults = $dsql->dsqlOper($speSql, "results");
					if($speResults){
						$speval = $_POST["spe".$speResults[0]['id']];
						if(!empty($speval) != ""){
							array_push($spearray, $speval);
						}
					}
				}
			}
		}

		if(!empty($spearray)){
			if(count($spearray) > 1){
				$spearray = descartes($spearray);
			}else{
				$spearray = $spearray[0];
			}
			foreach($spearray as $key => $val){
				$speid = $val;
				if(is_array($val)){
					$speid = join("-", $val);
				}
				$spemprice = $_POST["f_mprice_".$speid];
				$speprice = $_POST["f_price_".$speid];
				$speinventory = $_POST["f_inventory_".$speid];
				if(!preg_match("/^0|\d*\.?\d+$/i", $spemprice, $matches)){
					return array("state" => 200, "info" => $langData['shop'][4][61]);  //规格表中价格不得为空，类型为数字！
				}elseif(!preg_match("/^0|\d*\.?\d+$/i", $speprice, $matches)){
					return array("state" => 200, "info" => $langData['shop'][4][62]);  //规格表中库存不得为空，类型为数字！
				}elseif(!preg_match("/^0|\d*\.?\d+$/i", $speinventory, $matches)){
					return array("state" => 200, "info" => $langData['shop'][4][62]);  //规格表中库存不得为空，类型为数字！
				}else{
					$invent += $speinventory;
					array_push($specifival, $speid.",".$spemprice."#".$speprice."#".$speinventory);
				}
			}
		}

		if(!empty($specifival)){
			$specifival = join("|", $specifival);
			$inventory = $invent;
		}else{
			$specifival = "";

			if(!preg_match("/^0|\d*\.?\d+$/i", $inventory, $matches)){
				return array("state" => 200, "info" => $langData['shop'][4][63]);  //库存不得为空，类型为数字！
			}
		}

		if(empty($litpic)){
			return array("state" => 200, "info" => $langData['shop'][4][64]);  //请上传商品缩略图！
			exit();
		}

		if(empty($imglist)){
			return array("state" => 200, "info" => $langData['shop'][4][65]);  //请上传商品图集！
			exit();
		}

		if(trim($body) == ''){
			return array("state" => 200, "info" => $langData['shop'][4][66]);  //请输入商品描述
		}

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__shop_product` SET `type` = '$typeid', `title` = '$title', `brand` = '$brand', `property` = '$property', `category` = '$category', `mprice` = '$mprice', `price` = '$price', `logistic` = '$logistic', `volume` = '$volume', `weight` = '$weight', `specification` = '$specifival', `inventory` = '$inventory', `limit` = '$limit', `litpic` = '$litpic', `state` = 0, `pics` = '$imglist', `body` = '$body', `video` = '$video' WHERE `id` = ".$id);
		$ret = $dsql->dsqlOper($archives, "update");

		if($ret == "ok"){

			//后台消息通知
			updateAdminNotice("shop", "detail");

			return "修改成功！";
		}else{
			return array("state" => 101, "info" => $langData['siteConfig'][21][142]);  //发布到数据时发生错误，请检查字段内容！
		}

	}



	/**
		* 商家运费模板列表
		* @return array
		*/
	public function logistic(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid    = $userLogin->getMemberID();

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => $langData['shop'][4][36]);  //您还未开通商城店铺！
		}

		if(!verifyModuleAuth(array("module" => "shop"))){
			return array("state" => 200, "info" => $langData['shop'][4][1]);  //商家权限验证失败！
		}

		if($userResult[0]['state'] == 0){
			//return array("state" => 200, "info" => $langData['shop'][4][53]);  //您的商铺信息还在审核中，请通过审核后再发布！
		}

		if($userResult[0]['state'] == 2){
			//return array("state" => 200, "info" => $langData['shop'][4][54]);  //您的商铺信息审核失败，请通过审核后再发布！
		}

		$sid = $userResult[0]['id'];

		$list = array();
		$archives = $dsql->SetQuery("SELECT * FROM `#@__shop_logistictemplate` WHERE `sid` = $sid ORDER BY `id` DESC");
		$results = $dsql->dsqlOper($archives, "results");

		if(count($results) > 0){
			foreach ($results as $key=>$value) {
				$list[$key]["id"] = $value["id"];
				$list[$key]["title"] = $value["title"];
				$list[$key]['detail'] = getPriceDetail($value["bearFreight"], $value['valuation'], $value['express_start'], $value['express_postage'], $value['express_plus'], $value['express_postageplus'], $value['preferentialStandard'], $value['preferentialMoney']);
			}
		}

		return $list;


	}




	/**
		* 删除运费模板
		* @return array
		*/
	public function delLogistic(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		$id     = $this->param['id'];

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(empty($id)){
			return array("state" => 200, "info" => $langData['siteConfig'][20][300]);  //删除失败！
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$sid = $ret[0]['id'];
			$sql = $dsql->SetQuery("DELETE FROM `#@__shop_logistictemplate` WHERE `id` = ".$id." AND `sid` = ".$sid);
			if(!$dsql->dsqlOper($sql, "update") == "ok"){
				return array("state" => 200, "info" => $langData['siteConfig'][20][300]);  //删除失败！
			}

			return $langData['siteConfig'][20][444];  //删除成功！

		}else{
			return array("state" => 200, "info" => $langData['shop'][4][51]);  //您的账户暂未开通商品商铺功能！
		}

	}




	/**
		* 新增运费模板
		* @return array
		*/
	public function logisticAdd(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		$param = $this->param;
		$title = $param['title'];
		if(empty($title)) return array("state" => 200, "info" => $langData['shop'][4][68]);  //请输入模板名称！

        $cityid               = (int)$param['cityid'];
        if(empty($cityid)) return array("state" => 200, "info" => $langData['siteConfig'][20][585]);

		$bearFreight          = (int)$param['bearFreight'];
		$valuation            = (int)$param['valuation'];
		$purchase             = (float)$param['purchase'];
		$express_start        = (float)$param['express_start'];
		$express_postage      = (float)$param['express_postage'];
		$express_plus         = (float)$param['express_plus'];
		$express_postageplus  = (float)$param['express_postageplus'];
		$preferentialStandard = (float)$param['preferentialStandard'];
		$preferentialMoney    = (float)$param['preferentialMoney'];

		if($bearFreight == 1){
			$purchase = 0;
			$express_start = 0;
			$express_postage = 0;
			$express_plus = 0;
			$express_postageplus = 0;
			$preferentialStandard = 0;
			$preferentialMoney = 0;
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(!verifyModuleAuth(array("module" => "shop"))){
			return array("state" => 200, "info" => $langData['shop'][4][1]);  //商家权限验证失败！
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$sid = $ret[0]['id'];

			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__shop_logistictemplate` (`cityid`, `sid`, `title`, `bearFreight`, `valuation`, `express_start`, `express_postage`, `express_plus`, `express_postageplus`, `preferentialStandard`, `preferentialMoney`) VALUES ('$cityid', '$sid', '$title', '$bearFreight', '$valuation', '$express_start', '$express_postage', '$express_plus', '$express_postageplus', '$preferentialStandard', '$preferentialMoney')");
			$results = $dsql->dsqlOper($archives, "update");

			if($results == "ok"){
				return $langData['siteConfig'][21][107];  //添加成功！
			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][22][108]);  //添加失败！
			}

		}else{
			return array("state" => 200, "info" => $langData['shop'][4][51]);  //您的账户暂未开通商品商铺功能！
		}

	}




	/**
		* 修改运费模板
		* @return array
		*/
	public function logisticEdit(){
		global $dsql;
		global $userLogin;
		global $langData;

		$userid = $userLogin->getMemberID();
		$param = $this->param;
		$id    = $param['id'];
		$title = $param['title'];
		$cityid = $param['cityid'];
		if(empty($id)) return array("state" => 200, "info" => $langData['siteConfig'][20][180]);  //提交失败，请重试！
		if(empty($title)) return array("state" => 200, "info" => $langData['shop'][4][68]);  //请输入模板名称！
        if(empty($cityid)) return array("state" => 200, "info" => $langData['siteConfig'][20][585]);

		$bearFreight          = (int)$param['bearFreight'];
		$valuation            = (int)$param['valuation'];
		$purchase             = (float)$param['purchase'];
		$express_start        = (float)$param['express_start'];
		$express_postage      = (float)$param['express_postage'];
		$express_plus         = (float)$param['express_plus'];
		$express_postageplus  = (float)$param['express_postageplus'];
		$preferentialStandard = (float)$param['preferentialStandard'];
		$preferentialMoney    = (float)$param['preferentialMoney'];

		if($bearFreight == 1){
			$purchase = 0;
			$express_start = 0;
			$express_postage = 0;
			$express_plus = 0;
			$express_postageplus = 0;
			$preferentialStandard = 0;
			$preferentialMoney = 0;
		}

		if($userid == -1){
			return array("state" => 200, "info" => $langData['siteConfig'][20][262]);  //登录超时，请重新登录！
		}

		if(!verifyModuleAuth(array("module" => "shop"))){
			return array("state" => 200, "info" => $langData['shop'][4][1]);  //商家权限验证失败！
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__shop_store` WHERE `userid` = ".$userid);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$sid = $ret[0]['id'];

			//保存到主表
			$archives = $dsql->SetQuery("UPDATE `#@__shop_logistictemplate` SET `cityid` = '$cityid', `title` = '$title', `bearFreight` = '$bearFreight', `valuation` = '$valuation', `express_start` = '$express_start', `express_postage` = '$express_postage', `express_plus` = '$express_plus', `express_postageplus` = '$express_postageplus', `preferentialStandard` = '$preferentialStandard', `preferentialMoney` = '$preferentialMoney' WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");

			if($results == "ok"){
				return $langData['siteConfig'][20][229];  //修改成功！
			}else{
				return array("state" => 200, "info" => $langData['siteConfig'][21][108]);  //添加失败！
			}

		}else{
			return array("state" => 200, "info" => $langData['shop'][4][51]);  //您的账户暂未开通商品商铺功能！
		}

	}

	/**
	 * 获取当前系统的五个时间段
	 */
	public function systemTime(){
	    $time = $this->param['time'];
	    $num  = $this->param['num'];
	    $num  = !empty($num) ? ($num > 24 ? 24 : $num) : 5;
	    $list = array();
	    $nowTime = GetMkTime(time());

	    if(!empty($time)){
	        $now = $nowH = $time;
	    }else{
	        $now = $nowH = date("H");
	    }

	    for($i = 0; $i < $num; $i++){
	        if($nowH>=9){
	            $zero = '';
	        }else{
	            $zero = '0';
	        }
	        $list[$i]['showTime'] = $zero.$nowH.":00";
	        $list[$i]['nowTime']  = (int)$nowH;
	        $nowH += 1;
	        if($nowH>24){
	            $nowH = 1;
	        }
	        $d   =   date("Y-m-d").' '.$nowH.":00:00";
	        if(strtotime($d) > strtotime(date('Y-m-d H:i:s'))){

	        }else{
	            $d   =   date("Y-m-d",strtotime('+1 day')).' '.$nowH.":00:00";
	        }
	        $list[$i]['nextHour'] = GetMkTime($d);
	    }
	    return array(
	        "now"      => $now,
	        "nowTime"  => $nowTime,
	        "list"     => $list
	    );
	    //print_R($list);exit;
	}





}
