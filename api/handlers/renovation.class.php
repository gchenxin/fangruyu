<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 装修模块API接口
 *
 * @version        $Id: renovation.class.php 2014-4-2 下午20:53:10 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class renovation {
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
     * 装修基本参数
     * @return array
     */
	public function config(){

		require(HUONIAOINC."/config/renovation.inc.php");

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

		// global $custom_xq_atlasMax;       //小区相册图片数量限制
		// global $custom_gs_atlasMax;       //公司资质图片数量限制
		// global $custom_case_atlasMax;     //效果图数量限制
		// global $custom_diary_atlasMax;    //施工现场图片数量限制

		global $cfg_map;                  //系统默认地图
		// global $custom_map;               //自定义地图
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

		//自定义地图配置
		if($custom_map == 0){
			$custom_map = $cfg_map;
		}

		$params = !empty($this->param) && !is_array($this->param) ? explode(',',$this->param) : "";

		// $domainInfo = getDomain('renovation', 'config');
		// $customChannelDomain = $domainInfo['domain'];
		// if($customSubDomain == 0){
		// 	$customChannelDomain = "http://".$customChannelDomain;
		// }elseif($customSubDomain == 1){
		// 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
		// }elseif($customSubDomain == 2){
		// 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
		// }

		// include HUONIAOINC.'/siteModuleDomain.inc.php';
		$customChannelDomain = getDomainFullUrl('renovation', $customSubDomain);

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
				}elseif($param == "xq_atlasMax"){
					$return['xq_atlasMax'] = $custom_xq_atlasMax;
				}elseif($param == "gs_atlasMax"){
					$return['gs_atlasMax'] = $custom_gs_atlasMax;
				}elseif($param == "case_atlasMax"){
					$return['case_atlasMax'] = $custom_case_atlasMax;
				}elseif($param == "diary_atlasMax"){
					$return['diary_atlasMax'] = $custom_diary_atlasMax;
				}elseif($param == "map"){
					$return['map'] = $custom_map;
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
			$return['xq_atlasMax']    = $custom_xq_atlasMax;
			$return['gs_atlasMax']    = $custom_gs_atlasMax;
			$return['case_atlasMax']  = $custom_case_atlasMax;
			$return['diary_atlasMax'] = $custom_diary_atlasMax;
			$return['map']           = $custom_map;
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
     * 装修分类
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
		$results = $dsql->getTypeList($type, "renovation_type", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 装修地区
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
	 * 小区列表
	 * @return array
	 *
	 */
	public function community(){
		global $dsql;
		$pageinfo = $list = array();
		$addrid = $page = $keywords = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$addrid   = $this->param['addrid'];
				$keywords = $this->param['keywords'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$where = " WHERE `state` = 1";

        $cityid = getCityId($this->param['cityid']);
        if($cityid){
            $where .= " AND `cityid` = ".$cityid;
        }

		//遍历区域
		if(!empty($addrid)){
			if($dsql->getTypeList($addrid, "site_area")){
				$addrArr = arr_foreach($dsql->getTypeList($addrid, "site_area"));
				$addrArr = join(',',$addrArr);
				$lower = $addrid.",".$addrArr;
			}else{
				$lower = $addrid;
			}
			$where .= " AND `addrid` in ($lower)";
		}

		if(!empty($keywords)){
			$where .= " AND `title` like '%".$keywords."%'";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `litpic`, `addrid`, `address`, `lnglat`, `price`, `click` FROM `#@__renovation_community` ".$where." ORDER BY `weight` DESC, `id` DESC");
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
		$RenrenCrypt = new RenrenCrypt();

		if($results){
			foreach($results as $key => $val){

				$list[$key]['id']     = $val['id'];
				$list[$key]['title']  = $val['title'];
				$list[$key]['litpic'] = getFilePath($val['litpic']);

				$fid = $RenrenCrypt->php_decrypt(base64_decode($val["litpic"]));
				$picwidth = $picheight = 0;
				if(is_numeric($fid)){
					$sql = $dsql->SetQuery("SELECT `width`, `height` FROM `#@__attachment` WHERE `id` = '$fid'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$picwidth = $ret[0]['width'];
						$picheight = $ret[0]['height'];
					}
				}else{
					$rpic = str_replace('/uploads', '', $val["litpic"]);
					$sql = $dsql->SetQuery("SELECT `width`, `height` FROM `#@__attachment` WHERE `path` = '$rpic'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$picwidth = $ret[0]['width'];
						$picheight = $ret[0]['height'];
					}
				}
				$list[$key]['picwidth'] = $picwidth;
				$list[$key]['picheight'] = $picheight;

				global $data;
				$data = "";
				$addrName = getParentArr("site_area", $val['addrid']);
				$addrName = array_reverse(parent_foreach($addrName, "typename"));
				$list[$key]['addr']    = join(" ", $addrName);

				$list[$key]['address']  = $val['address'];
				$list[$key]['lnglat']  = $val['lnglat'];
				$list[$key]['price']  = $val['price'];
				$list[$key]['click']  = $val['click'];

				$param = array(
					"service"     => "renovation",
					"template"    => "community",
					"id"          => $val['id']
				);

				$list[$key]['url'] =getUrlPath($param);
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);

	}


	/**
	 * 小区详细信息
	 * @return array
	 *
	 */
	public function communityDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_community` WHERE `state` = 1 AND `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$addrName = getParentArr("site_area", $results[0]['addrid']);
			$addrName = array_reverse(parent_foreach($addrName, "typename"));
			$results[0]['addr']    = join(" > ", $addrName);

			$results[0]["litpic"] = getFilePath($results[0]["litpic"]);

			$configArr = array();
			$config = $results[0]['config'];
			if(!empty($config)){
				$config = explode("|||", $config);
				foreach ($config as $key => $value) {
					$v = explode("###", $value);
					array_push($configArr, array("title" => $v[0], "note" => $v[1]));
				}
			}
			$results[0]['config'] = $configArr;

			$picsArr = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode("||", $pics);
				foreach ($pics as $key => $value) {
					$v = explode("##", $value);
					array_push($picsArr, array("pic" => getFilePath($v[0]), "title" => $v[1]));
				}
			}
			$results[0]['pics'] = $picsArr;

			return $results[0];
		}
	}


	/**
     * 装修招标
     * @return array
     */
	public function zhaobiao(){
		global $dsql;
		$pageinfo = $list = array();
		$type = $price = $nature = $addrid = $orderby = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$type     = $this->param['type'];
				$price    = $this->param['price'];
				$nature   = $this->param['nature'];
				$addrid   = $this->param['addrid'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$where = " WHERE `state` = 1";

		if(!empty($type)){
			$where .= " AND `btype` = ".$type;
		}

		if(!empty($price)){
			$where .= " AND `budget` = ".$price;
		}

		if(!empty($nature)){
			$where .= " AND `nature` = ".$nature;
		}

		//遍历区域
		if(!empty($addrid)){
			if($dsql->getTypeList($addrid, "site_area")){
				$addrArr = arr_foreach($dsql->getTypeList($addrid, "site_area"));
				$addrArr = join(',',$addrArr);
				$lower = $addrid.",".$addrArr;
			}else{
				$lower = $addrid;
			}
			$where .= " AND `addrid` in ($lower)";
		}

		//排序
		switch ($orderby){
			//默认
			case 0:
				$orderby = " ORDER BY `weight` DESC, `id` DESC";
				break;
			//预算升序
			case 1:
				$orderby = " ORDER BY `budget` ASC, `weight` DESC, `id` DESC";
				break;
			//预算降序
			case 2:
				$orderby = " ORDER BY `budget` DESC, `weight` DESC, `id` DESC";
				break;
			//时间降序
			case 3:
				$orderby = " ORDER BY `pubdate` DESC, `weight` DESC, `id` DESC";
				break;
			//人气降序
			case 4:
				$orderby = " ORDER BY `click` DESC, `weight` DESC, `id` DESC";
				break;
			//面积降序
			case 5:
				$orderby = " ORDER BY `area` DESC, `weight` DESC, `id` DESC";
				break;
			default:
				$orderby = " ORDER BY `weight` DESC, `id` DESC";
				break;
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `btype`, `budget`, `nature`, `area`, `start`, `state`, `people`, `pubdate` FROM `#@__renovation_zhaobiao` ".$where.$orderby);
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

				$btype = "";
				$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$val['btype']);
				$results_  = $dsql->dsqlOper($archives, "results");
				if($results){
					$btype = $results_[0]['typename'];
				}
				$results[$key]['btype'] = $btype;


				$budget = "";
				$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$val['budget']);
				$results_  = $dsql->dsqlOper($archives, "results");
				if($results){
					$budget = $results_[0]['typename'];
				}
				$results[$key]['budget'] = $budget;


				$nature = "";
				$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$val['nature']);
				$results_  = $dsql->dsqlOper($archives, "results");
				if($results){
					$nature = $results_[0]['typename'];
				}
				$results[$key]['nature'] = $nature;


				//判断是否过期
				if($val['end'] < GetMkTime(time())){
					// $results[$key]['state'] = "3";
					// $archives = $dsql->SetQuery("UPDATE `#@__renovation_zhaobiao` SET `state` = 3 WHERE `id` = ".$val['id']);
					// $dsql->dsqlOper($archives, "update");
				}


				$param = array(
					"service"     => "renovation",
					"template"    => "zb-detail",
					"id"          => $val['id']
				);
				$results[$key]['url'] = getUrlPath($param);

				array_push($list, $results[$key]);

			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 招标详细信息
     * @return array
     */
	public function zhaobiaoDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_zhaobiao` WHERE `state` = 1 AND `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$btype = "";
			$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$results[0]['btype']);
			$results_  = $dsql->dsqlOper($archives, "results");
			if($results){
				$btype = $results_[0]['typename'];
			}
			$results[0]['btype'] = $btype;

			$budget = "";
			$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$results[0]['budget']);
			$results_  = $dsql->dsqlOper($archives, "results");
			if($results){
				$budget = $results_[0]['typename'];
			}
			$results[0]['budget'] = $budget;

			$nature = "";
			$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$results[0]['nature']);
			$results_  = $dsql->dsqlOper($archives, "results");
			if($results){
				$nature = $results_[0]['typename'];
			}
			$results[0]['nature'] = $nature;

			$addrName = getParentArr("site_area", $results[0]['addrid']);
			$addrName = array_reverse(parent_foreach($addrName, "typename"));
			$results[0]['addr']    = join(" > ", $addrName);

			$results[0]["floorplans"] = getFilePath($results[0]["floorplans"]);

			//判断是否过期
			if($results[0]['end'] < GetMkTime(time())){
				$results[0]['state'] = 3;
				$archives = $dsql->SetQuery("UPDATE `#@__renovation_zhaobiao` SET `state` = 3 WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");
			}


			return $results[0];
		}
	}


	/**
     * 发表招标
     * @return array
     */
	public function sendZhaobiao(){
		global $dsql;
		$param = $this->param;

		$people  = $param['people'];
		$contact = $param['contact'];
		$addrid  = $param['addrid'];
		$area    = $param['area'];
		$budget  = $param['budget'];
		$nature  = $param['nature'];
		$note    = $param['note'];
		$pubdate = GetMkTime(time());

		if(empty($people) || empty($contact) || empty($addrid)){
			return array("state" => 200, "info" => '必填项不得为空！');
		}

		$title = $people . "发布的新招标信息";

		$cityid = getCityId();

		$archives = $dsql->SetQuery("INSERT INTO `#@__renovation_zhaobiao` (`cityid`, `title`, `people`, `contact`, `addrid`, `area`, `budget`, `nature`, `note`, `pubdate`) VALUES ('$cityid', '$title', '$people', '$contact', '$addrid', '$area', '$budget', '$nature', '$note', '$pubdate')");
		$results  = $dsql->dsqlOper($archives, "update");
		if($results == "ok"){
			return "申请成功！";
		}else{
			return array("state" => 200, "info" => '申请失败！');
		}

	}




	/**
     * 装修投标
     * @return array
     */
	public function toubiao(){
		global $dsql;
		$pageinfo = $list = array();
		$aid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$aid      = $this->param['aid'];
				$state    = $this->param['state'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if(!is_numeric($aid)) return array("state" => 200, "info" => '格式错误！');

		$where = " WHERE `state` != 0 AND `aid` = ".$aid;

		if(!empty($state)){
			$where .= " AND `state` = ".$state;
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `userid`, `material`, `auxiliary`, `labor`, `manage`, `design`, `note`, `property`, `file`, `state`, `pubdate` FROM `#@__renovation_toubiao` ".$where." ORDER BY `id` DESC");
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
				$results[$key]["file"] = getFilePath($val["file"]);
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $results);
	}


	/**
     * 装修公司
     * @return array
     */
	public function store(){
		global $dsql;
		global $cfg_secureAccess;
		global $cfg_basehost;
		$pageinfo = $list = array();
		$jiastyle = $comstyle = $style = $addrid = $property = $range = $title = $orderby = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$jiastyle = $this->param['jiastyle'];
				$comstyle = $this->param['comstyle'];
				$style    = $this->param['style'];
				$addrid   = $this->param['addrid'];
                $cityid   = $this->param['cityid'];
				$property = $this->param['property'];
				$range    = $this->param['range'];
				$title    = $this->param['title'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$where = " WHERE `state` = 1";

        $cityid = getCityId($this->param['cityid']);
        if($cityid){
            $where .= " AND `cityid` = ".$cityid;
        }

		if(!empty($jiastyle)){
			$where .= " AND FIND_IN_SET('$jiastyle', `jiastyle`)";
		}

		if(!empty($comstyle)){
			$where .= " AND FIND_IN_SET('$comstyle', `comstyle`)";
		}

		if(!empty($style)){
			$where .= " AND FIND_IN_SET('$style', `style`)";
		}

		if($property != ""){
			$property = explode(",", $property);
			foreach ($property as $key => $val) {
				$where .= " AND find_in_set('".$val."', `property`)";
			}
		}

		if(!empty($range)){
			$where .= " AND FIND_IN_SET('$range', `range`)";
		}

		if(!empty($title)){
			$where .= " AND `company` like '%".$title."%'";
		}

		//遍历区域
		if(!empty($addrid)){
			if($dsql->getTypeList($addrid, "site_area")){
				$addrArr = arr_foreach($dsql->getTypeList($addrid, "site_area"));
				$addrArr = join(',',$addrArr);
				$lower = $addrid.",".$addrArr;
			}else{
				$lower = $addrid;
			}
			$where .= " AND `addrid` in ($lower)";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$order = " ORDER BY `weight` DESC, `id` DESC";
		if($orderby == 1){
			$order = " ORDER BY `click` DESC, `id` DESC";
		}

		$archives = $dsql->SetQuery("SELECT `id`, `company`, `safeguard`, `domaintype`, `addrid`, `address`, `logo`, `contact`, `license`, `certi` FROM `#@__renovation_store`".$where.$order);

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
				$list[$key]['id']        = $val['id'];
				$list[$key]['company']   = $val['company'];
				$list[$key]['safeguard'] = $val['safeguard'];
				$list[$key]['address']   = $val['address'];
				$list[$key]['contact']   = $val['contact'];
				$list[$key]['license']   = $val['license'];
				$list[$key]['certi']     = $val['certi'];
				$list[$key]["logo"]      = getFilePath($val["logo"]);

				global $data;
				$data = "";
				$addrName = getParentArr("site_area", $val['addrid']);
				$addrName = array_reverse(parent_foreach($addrName, "typename"));
				$list[$key]['addr']    = join(" ", $addrName);

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `company` = ".$val['id']);
				$teamCount  = $dsql->dsqlOper($archives, "totalCount");
				$list[$key]["teamCount"] = $teamCount;

				$caseCount = $diaryCount = 0;
				$results_  = $dsql->dsqlOper($archives, "results");
				foreach($results_ as $k => $v){
					$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_case` WHERE `designer` = ".$v['id']);
					$case  = $dsql->dsqlOper($archives, "totalCount");
					$caseCount = $caseCount + $case;

					$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_diary` WHERE `designer` = ".$v['id']);
					$diary  = $dsql->dsqlOper($archives, "totalCount");
					$diaryCount = $diaryCount + $diary;
				}
				$list[$key]["caseCount"] = $caseCount;
				$list[$key]["diaryCount"] = $diaryCount;

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_rese` WHERE `company` = ".$val['id']);
				$reseCount  = $dsql->dsqlOper($archives, "totalCount");
				$list[$key]["reseCount"] = $reseCount;

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_guest` WHERE `company` = ".$val['id']);
				$guestCount  = $dsql->dsqlOper($archives, "totalCount");
				$list[$key]["guestCount"] = $guestCount;

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_storesale` WHERE `store` = ".$val['id']);
				$saleCount  = $dsql->dsqlOper($archives, "totalCount");
				$list[$key]["saleCount"] = $saleCount;

				$param = array(
					"service"     => "renovation",
					"template"    => "company-detail",
					"id"          => $val['id']
				);

				$url = getUrlPath($param);

				$this->param = "";
				$channelDomain = $this->config();
				$domainInfo = getDomain('renovation', 'renovation_store', $val['id']);

				//绑定主域名
				if($results[$key]["domaintype"] == 1 && $domainInfo['expires'] > GetMkTime(time())){
					$url = $cfg_secureAccess.$domainInfo['domain'];
				}

				$list[$key]['url'] = $url;

			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 装修公司详细信息
     * @return array
     */
	public function storeDetail(){
		global $dsql;
		global $userLogin;
		global $cfg_secureAccess;
		global $cfg_basehost;
		$id = $this->param;
		$uid = $userLogin->getMemberID();

		if(!is_numeric($id) && $uid == -1){
			return array("state" => 200, "info" => '格式错误！');
		}

		$where = " AND `state` = 1";
		if(!is_numeric($id)){
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = ".$uid);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				$id = $results[0]['id'];
				$where = "";
			}else{
				return array("state" => 200, "info" => '该会员暂未开通公司！');
			}
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_store` WHERE `id` = ".$id.$where);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
		    global $data;
		    $data="";
			$addrName = getParentArr("site_area", $results[0]['addrid']);
			$addrName = array_reverse(parent_foreach($addrName, "typename"));
			$results[0]['addr']    = $addrName;

			$results[0]["logoSource"] = $results[0]["logo"];
			$results[0]["logo"] = getFilePath($results[0]["logo"]);

			$certsArr = array();
			$certs = $results[0]['certs'];
			if(!empty($certs)){
				$certs = explode("||", $certs);
				foreach($certs as $key => $val){
					$val = explode("##", $val);
					$certsArr[$key]['picSource'] = $val[0];
					$certsArr[$key]['pic'] = getFilePath($val[0]);
					$certsArr[$key]['title'] = $val[1];
					$certsArr[$key]['note'] = $val[2];
				}
			}
			$results[0]["certs"] = $certsArr;

			$this->param = "";
			$channelDomain = $this->config();
			$domainInfo = getDomain('renovation', 'renovation_store', $id);

			/**
			 * 默认 || 模块配置为子目录并且信息配置为绑定子域名则访问方式转为默认
			 * （因为子域名是随模块配置变化，如果模块配置为子目录地址为乱掉。）
			 * 如：模块配置：http://menhu168.com/renovation
			 * 如果信息绑定子域名则会变成：http://demo.menhu168.com/renovation
			 * 这样会导致系统读取信息错误
			 */
			if($results[0]["domaintype"] == 0 || ($channelDomain['subDomain'] == 2 && $results[0]["domaintype"] == 2)){

				$results[0]["domain"] = $channelDomain['channelDomain']."/company-detail-".$id.".html";

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


			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `company` = ".$results[0]['id']);
			$teamCount  = $dsql->dsqlOper($archives, "totalCount");
			$results[0]["teamCount"] = $teamCount;

			$caseCount = $diaryCount = 0;
			$results_  = $dsql->dsqlOper($archives, "results");
			foreach($results_ as $k => $v){
				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_case` WHERE `designer` = ".$v['id']);
				$case  = $dsql->dsqlOper($archives, "totalCount");
				$caseCount = $caseCount + $case;

				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_diary` WHERE `designer` = ".$v['id']);
				$diary  = $dsql->dsqlOper($archives, "totalCount");
				$diaryCount = $diaryCount + $diary;
			}
			$results[0]["caseCount"] = $caseCount;
			$results[0]["diaryCount"] = $diaryCount;

			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_rese` WHERE `company` = ".$results[0]['id']);
			$reseCount  = $dsql->dsqlOper($archives, "totalCount");
			$results[0]["reseCount"] = $reseCount;

			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_guest` WHERE `company` = ".$results[0]['id']);
			$guestCount  = $dsql->dsqlOper($archives, "totalCount");
			$results[0]["guestCount"] = $guestCount;

			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_storesale` WHERE `store` = ".$results[0]['id']);
			$saleCount  = $dsql->dsqlOper($archives, "totalCount");
			$results[0]["saleCount"] = $saleCount;


			//服务区域
			$range = array();
			if(!empty($results[0]['range'])){
				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__site_area` WHERE `id` in (".$results[0]['range'].") ORDER BY INSTR(',".$results[0]['range'].",', CONCAT(',',id,','))");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					foreach ($ret as $key => $value) {
						array_push($range, $value['typename']);
					}
				}
			}
			$results[0]['rangeName'] = join(" ", $range);


			//家装专长
			$jiastyle = array();
			if(!empty($results[0]['jiastyle'])){
				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` in (".$results[0]['jiastyle'].")");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					foreach ($ret as $key => $value) {
						array_push($jiastyle, $value['typename']);
					}
				}
			}
			$results[0]['jiastyleName'] = join(" ", $jiastyle);


			//公装专长
			$comstyle = array();
			if(!empty($results[0]['comstyle'])){
				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` in (".$results[0]['comstyle'].")");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					foreach ($ret as $key => $value) {
						array_push($comstyle, $value['typename']);
					}
				}
			}
			$results[0]['comstyleName'] = join(" ", $comstyle);


			//专长风格
			$style = array();
			if(!empty($results[0]['style'])){
				$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` in (".$results[0]['style'].")");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					foreach ($ret as $key => $value) {
						array_push($style, $value['typename']);
					}
				}
			}
			$results[0]['styleName'] = join(" ", $style);


			//验证是否已经收藏
			$params = array(
				"module" => "renovation",
				"temp"   => "company-detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$results[0]['collect'] = $collect == "has" ? 1 : 0;

			return $results[0];
		}
	}


	/**
     * 公司促销活动
     * @return array
     */
	public function storeSale(){
		global $dsql;
		$pageinfo = $list = array();
		$storeid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$storeid  = $this->param['storeid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if(!is_numeric($storeid)) return array("state" => 200, "info" => '格式错误！');

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `click`, `pubdate` FROM `#@__renovation_storesale` WHERE `store` = $storeid ORDER BY `id` DESC");

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
     * 促销详细信息
     * @return array
     */
	public function saleDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_storesale` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			return $results;
		}
	}


	/**
     * 设计师
     * @return array
     */
	public function team(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$special = $style = $works = $company = $u = $orderby = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$special  = $this->param['special'];
				$style    = $this->param['style'];
				$works    = $this->param['works'];
				$company  = $this->param['company'];
				$u        = $this->param['u'];
				$orderby  = $this->param['orderby'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$where = " WHERE `state` = 1";

        if(!$u) {
            $cityid = getCityId($this->param['cityid']);
            if ($cityid) {
                $where2 = " AND `cityid` = $cityid";
            }

            $houseid = array();
            $loupanSql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__renovation_store` WHERE 1=1" . $where2);
            $loupanResult = $dsql->dsqlOper($loupanSql, "results");
            if ($loupanResult) {
                foreach ($loupanResult as $key => $loupan) {
                    array_push($houseid, $loupan['id']);
                }
                $where .= " AND `company` in (" . join(",", $houseid) . ")";
            } else {
                $where .= " AND 2=3";
            }
        }

		if(!empty($special)){
			$where .= " AND FIND_IN_SET('$special', `special`)";
		}

		if(!empty($style)){
			$where .= " AND FIND_IN_SET('$style', `style`)";
		}

		//工作年限
		if($works != ""){
			$works = explode(",", $works);
			if(empty($works[0])){
				$where .= " AND `works` < " . $works[1];
			}elseif(empty($price[1])){
				$where .= " AND `works` > " . $works[0];
			}else{
				$where .= " AND `works` BETWEEN " . $works[0] . " AND " . $works[1];
			}
		}

		//会员中心请求
		if($u == 1){

			$uid = $userLogin->getMemberID();

			if(!verifyModuleAuth(array("module" => "renovation"))){
				return array("state" => 200, "info" => '商家权限验证失败！');
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = $uid");
			$storeRes = $dsql->dsqlOper($sql, "results");
			if($storeRes){
				$company = $storeRes[0]['id'];
			}else{
				$company = "-1";
			}

		}

		if(!empty($company)){
			$where .= " AND `company` = ". $company;
		}

		$order = " ORDER BY `weight` DESC, `id` DESC";

		if($orderby == 1){
			$order = " ORDER BY `id` DESC";
		}elseif($orderby == 2){
			$order = " ORDER BY `click` DESC";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `name`, `works`, `post`, `photo`, `company`, `special`, `style`, `idea`, `click` FROM `#@__renovation_team`".$where.$order);

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

				$list[$key]['id'] = $val['id'];
				$list[$key]['name'] = $val['name'];
				$list[$key]['works'] = $val['works'];
				$list[$key]['post'] = $val['post'];
				$list[$key]['click'] = $val['click'];

				$list[$key]["photo"] = getFilePath($val["photo"]);

				$this->param = $val['company'];
				$list[$key]['company'] = $this->storeDetail();

				$specialArr = array();
				$special = $val['special'];
				if(!empty($special)){
					$special = explode(",", $special);
					foreach($special as $k => $v){
						$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$v);
						$typename  = $dsql->dsqlOper($archives, "results");
						if($typename){
							$specialArr[] = $typename[0]['typename'];
						}
					}
				}
				$list[$key]["special"] = join(",", $specialArr);

				$styleArr = array();
				$style = $val['style'];
				if(!empty($style)){
					$style = explode(",", $style);
					foreach($style as $k => $v){
						$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$v);
						$typename  = $dsql->dsqlOper($archives, "results");
						if($typename){
							$styleArr[] = $typename[0]['typename'];
						}
					}
				}
				$list[$key]["style"] = join(",", $styleArr);

				$list[$key]['idea'] = $val['idea'];

				$archives   = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_case` WHERE `designer` = ".$val['id']);
				$caseCount = $dsql->dsqlOper($archives, "totalCount");
				$list[$key]["case"] = $caseCount;

				$archives   = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_diary` WHERE `designer` = ".$val['id']);
				$caseCount = $dsql->dsqlOper($archives, "totalCount");
				$list[$key]["diary"] = $caseCount;

				$param = array(
					"service"     => "renovation",
					"template"    => "designer-detail",
					"id"          => $val['id']
				);
				$list[$key]['url'] = getUrlPath($param);

				//验证是否已经收藏
				$params = array(
					"module" => "renovation",
					"temp"   => "designer-detail",
					"type"   => "add",
					"id"     => $val['id'],
					"check"  => 1
				);
				$collect = checkIsCollect($params);
				$list[$key]['collect'] = $collect == "has" ? 1 : 0;
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 设计师详细信息
     * @return array
     */
	public function teamDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_team` WHERE `state` = 1 AND `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results[0]["photoSource"] = $results[0]["photo"];
			$results[0]["photo"] = getFilePath($results[0]["photo"]);

			$specialArr = array();
			$special = $results[0]['special'];
			$results[0]['specialids'] = $special;
			if(!empty($special)){
				$special = explode(",", $special);
				foreach($special as $k => $v){
					$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$v);
					$typename  = $dsql->dsqlOper($archives, "results");
					if($typename){
						$specialArr[] = $typename[0]['typename'];
					}
				}
			}
			$results[0]["special"] = join(",", $specialArr);

			$styleArr = array();
			$style = $results[0]['style'];
			$results[0]['styleids'] = $style;
			if(!empty($style)){
				$style = explode(",", $style);
				foreach($style as $k => $v){
					$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$v);
					$typename  = $dsql->dsqlOper($archives, "results");
					if($typename){
						$styleArr[] = $typename[0]['typename'];
					}
				}
			}
			$results[0]["style"] = join(",", $styleArr);

			$archives  = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_case` WHERE `designer` = ".$id);
			$caseCount = $dsql->dsqlOper($archives, "totalCount");
			$results[0]["case"] = $caseCount;

			$archives  = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_diary` WHERE `designer` = ".$id);
			$caseCount = $dsql->dsqlOper($archives, "totalCount");
			$results[0]["diary"] = $caseCount;

			$this->param = $results[0]['company'];
			$results[0]['company'] = $this->storeDetail();


			//验证是否已经收藏
			$params = array(
				"module" => "renovation",
				"temp"   => "designer-detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$results[0]['collect'] = $collect == "has" ? 1 : 0;


			unset($results[0]["weight"]);
			unset($results[0]["state"]);
			unset($results[0]["pubdate"]);

			return $results[0];
		}
	}


	/**
		* 新增设计师
		* @return array
		*/
	public function addTeam(){
		global $dsql;
		global $userLogin;

		$userid    = $userLogin->getMemberID();
		$param     = $this->param;

		$name    = filterSensitiveWords(addslashes($param['name']));
		$works   = filterSensitiveWords(addslashes($param['works']));
		$post    = filterSensitiveWords(addslashes($param['post']));
		$photo   = $param['photo'];
		$special = isset($param['special']) ? join(',',$param['special']) : '';
		$style   = isset($param['style']) ? join(',',$param['style']) : '';
		$idea    = filterSensitiveWords(addslashes($param['idea']));
		$note    = filterSensitiveWords(addslashes($param['note']));
		$pubdate = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__renovation_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => '您还未开通装修公司！');
		}

		if(!verifyModuleAuth(array("module" => "renovation"))){
			return array("state" => 200, "info" => '商家权限验证失败！');
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => '您的公司信息还在审核中，请通过审核后再发布！');
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => '您的公司信息审核失败，请通过审核后再发布！');
		}

		$sid = $userResult[0]['id'];

		if(empty($name)){
			return array("state" => 200, "info" => '请输入姓名');
		}

		if(empty($post)){
			return array("state" => 200, "info" => '请输入职位');
		}

		if(empty($photo)){
			return array("state" => 200, "info" => "请上传头像");
		}

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__renovation_team` (`name`, `works`, `post`, `photo`, `company`, `special`, `style`, `idea`, `note`, `weight`, `click`, `state`, `pubdate`) VALUES ('$name', '$works', '$post', '$photo', '$sid', '$special', '$style', '$idea', '$note', '1', '1', '1', '".GetMkTime(time())."')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){
			return $aid;
		}else{
			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');
		}

	}



	/**
		* 修改设计师信息
		* @return array
		*/
	public function editTeam(){
		global $dsql;
		global $userLogin;

		$userid  = $userLogin->getMemberID();
		$param   = $this->param;

		$id      = $param['id'];
		$name    = filterSensitiveWords(addslashes($param['name']));
		$works   = filterSensitiveWords(addslashes($param['works']));
		$post    = filterSensitiveWords(addslashes($param['post']));
		$photo   = $param['photo'];
		$special = isset($param['special']) ? join(',',$param['special']) : '';
		$style   = isset($param['style']) ? join(',',$param['style']) : '';
		$idea    = filterSensitiveWords(addslashes($param['idea']));
		$note    = filterSensitiveWords(addslashes($param['note']));

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__renovation_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => '您还未开通装修公司！');
		}

		if(!verifyModuleAuth(array("module" => "renovation"))){
			return array("state" => 200, "info" => '商家权限验证失败！');
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => '您的公司信息还在审核中，请通过审核后再发布！');
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => '您的公司信息审核失败，请通过审核后再发布！');
		}

		$sid = $userResult[0]['id'];

		$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `company` = $sid AND `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if(!$results){
			return array("state" => 101, "info" => '权限不足，请确认帐户信息后再进行操作！');
		}

		if(empty($name)){
			return array("state" => 200, "info" => '请输入姓名');
		}

		if(empty($post)){
			return array("state" => 200, "info" => '请输入职位');
		}

		if(empty($photo)){
			return array("state" => 200, "info" => "请上传头像");
		}

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__renovation_team` SET `name` = '$name', `works` = '$works', `post` = '$post', `userid` = '$userid', `photo` = '$photo', `special` = '$special', `style` = '$style', `idea` = '$idea', `note` = '$note' WHERE `id` = ".$id);
		$ret = $dsql->dsqlOper($archives, "update");

		if($ret == "ok"){
			return "修改成功！";
		}else{
			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');
		}

	}


	/**
		* 删除设计师
		* @return array
		*/
	public function delTeam(){
		global $dsql;
		global $userLogin;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = ".$uid);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 101, "info" => '公司信息不存在，删除失败！');
		}else{
			$sid = $ret[0]['id'];
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_team` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['company'] == $sid){

					//删除缩略图
					delPicFile($results['litpic'], "delThumb", "renovation");

					//删除图集
					$pics = explode(",", $results['pics']);
					foreach($pics as $k__ => $v__){
						delPicFile($v__, "delAtlas", "renovation");
					}

					$archives = $dsql->SetQuery("DELETE FROM `#@__renovation_team` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");
					return '删除成功！';

			}else{
				return array("state" => 101, "info" => '权限不足，请确认帐户信息后再进行操作！');
			}
		}else{
			return array("state" => 101, "info" => '成员不存在，或已经删除！');
		}

	}


	/**
     * 效果图
     * @return array
     */
	public function rcase(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$type = $jiastyle = $style = $comstyle = $apartment = $units = $kongjian = $area = $title = $designer = $company = $u = $orderby = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$type      = $this->param['type'];
				$style     = $this->param['style'];
				$units     = $this->param['units'];
				$kongjian  = $this->param['kongjian'];
				$jubu      = $this->param['jubu'];
				$comstyle  = $this->param['comstyle'];
				$apartment = $this->param['apartment'];
				$area      = $this->param['area'];
				$title     = $this->param['title'];
				$designer  = $this->param['designer'];
				$company   = $this->param['company'];
				$u         = $this->param['u'];
				$orderby   = $this->param['orderby'];
				$page      = $this->param['page'];
				$pageSize  = $this->param['pageSize'];
			}
		}

		$where = " WHERE `state` = 1";

        $cityid = getCityId($this->param['cityid']);
        if($cityid){
            $where2 = " AND `cityid` = $cityid";
        }

        $storeSql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__renovation_store` WHERE 1=1".$where2);
        $storeResult = $dsql->dsqlOper($storeSql, "results");
        $userid = array();
        if($storeResult) {
            foreach ($storeResult as $key => $store) {
                $userSql = $dsql->SetQuery("SELECT `id`, `name` FROM `#@__renovation_team` WHERE `company` =" . $store['id']);
                $userResult = $dsql->dsqlOper($userSql, "results");
                if ($userResult) {
                    foreach ($userResult as $ke => $user) {
                        array_push($userid, $user['id']);
                    }
                }
            }
						if($userid){
	            $where .= " AND `designer` in (" . join(",", $userid) . ")";
		        }else{
		        	$where .= " AND 4 = 5";
		        }
        }else{
            $where .= " AND 2=3";
        }

		if($type != ""){
			$where .= " AND `type` = ".$type;

			if($type == 0){
				if(!empty($style)){
					$where .= " AND `style` = ".$style;
				}
				if(!empty($units)){
					$where .= " AND `units` = ".$units;
				}
				if(!empty($kongjian)){
					$where .= " AND FIND_IN_SET(".$kongjian.", `kongjian`)";
				}
				if(!empty($jubu)){
					$where .= " AND FIND_IN_SET(".$jubu.", `jubu`)";
				}
			}elseif($type == 1){
				if(!empty($comstyle)){
					$where .= " AND `comstyle` = ".$comstyle;
				}
			}
		}

		if(!empty($apartment)){
			$where .= " AND `apartment` = ".$apartment;
		}

		//面积
		if($area != ""){
			$area = explode(",", $area);
			if(empty($area[0])){
				$where .= " AND `area` < " . $area[1];
			}elseif(empty($price[1])){
				$where .= " AND `area` > " . $area[0];
			}else{
				$where .= " AND `area` BETWEEN " . $area[0] . " AND " . $area[1];
			}
		}

		//关键词
		if(!empty($title)){
			$where .= " AND `title` like '%$title%'";
		}

		if(!empty($designer)){
			$where .= " AND `designer` = ".$designer;
		}

		//会员中心请求
		if($u == 1){

			$uid = $userLogin->getMemberID();

			if(!verifyModuleAuth(array("module" => "renovation"))){
				return array("state" => 200, "info" => '商家权限验证失败！');
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = $uid");
			$storeRes = $dsql->dsqlOper($sql, "results");
			if($storeRes){
				$company = $storeRes[0]['id'];
			}else{
				$company = "-1";
			}

		}

		if(!empty($company)){
			$teamids = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `company` = ".$company);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach($results as $k => $v){
					$teamids[] = $v['id'];
				}
			}
			if(!empty($teamids)){
				$where .= " AND `designer` in (".join(",", $teamids).")";
			}else{
				$where .= " AND 1 = 2";
			}
		}

		$order = " ORDER BY `weight` DESC, `id` DESC";
		if($orderby == "click"){
			$order = " ORDER BY `click` DESC, `weight` DESC, `id` DESC";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `litpic`, `click`, `pubdate` FROM `#@__renovation_case`".$where.$order);

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
		$RenrenCrypt = new RenrenCrypt();

		if($results){
			foreach($results as $key => $val){
				$results[$key]["litpic"] = getFilePath($val["litpic"]);

				$fid = $RenrenCrypt->php_decrypt(base64_decode($val["litpic"]));
				$picwidth = $picheight = 0;
				if(is_numeric($fid)){
					$sql = $dsql->SetQuery("SELECT `width`, `height` FROM `#@__attachment` WHERE `id` = '$fid'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$picwidth = $ret[0]['width'];
						$picheight = $ret[0]['height'];
					}
				}else{
					$rpic = str_replace('/uploads', '', $val["litpic"]);
					$sql = $dsql->SetQuery("SELECT `width`, `height` FROM `#@__attachment` WHERE `path` = '$rpic'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$picwidth = $ret[0]['width'];
						$picheight = $ret[0]['height'];
					}
				}
				$results[$key]['picwidth'] = $picwidth;
				$results[$key]['picheight'] = $picheight;

				$param = array(
					"service"     => "renovation",
					"template"    => "albums-detail",
					"id"          => $val['id']
				);
				$results[$key]['url'] = getUrlPath($param);

				array_push($list, $results[$key]);
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 效果图详细信息
     * @return array
     */
	public function caseDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_case` WHERE `state` = 1 AND `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results[0]["litpicSource"] = $results[0]["litpic"];
			$results[0]["litpic"] = getFilePath($results[0]["litpic"]);

			$style = $results[0]['style'];
			if(!empty($style)){
				$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$style);
				$typename  = $dsql->dsqlOper($archives, "results");
				if($typename){
					$style = $typename[0]['typename'];
				}
			}else{
				$style = "";
			}
			$results[0]["styleid"] = $results[0]["style"];
			$results[0]["style"]   = $style;

			$units = $results[0]['units'];
			if(!empty($units)){
				$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$units);
				$typename  = $dsql->dsqlOper($archives, "results");
				if($typename){
					$units = $typename[0]['typename'];
				}
			}
			$results[0]["unitsid"] = $results[0]["units"];
			$results[0]["units"] = $units;

			$comstyle = $results[0]['comstyle'];
			if(!empty($comstyle)){
				$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$comstyle);
				$typename  = $dsql->dsqlOper($archives, "results");
				if($typename){
					$comstyle = $typename[0]['typename'];
				}
			}else{
				$comstyle = "";
			}
			$results[0]["comstyleid"] = $results[0]["comstyle"];
			$results[0]["comstyle"] = $comstyle;

			$apartment = $results[0]['apartment'];
			if(!empty($apartment)){
				$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$apartment);
				$typename  = $dsql->dsqlOper($archives, "results");
				if($typename){
					$apartment = $typename[0]['typename'];
				}
			}
			$results[0]["apartmentid"] = $results[0]["apartment"];
			$results[0]["apartment"] = $apartment;

			$picsArr = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode(",", $pics);
				foreach($pics as $key => $val){
					$picsArr[$key]['pathSource'] = $val;
					$picsArr[$key]['path'] = getFilePath($val);
				}
			}
			$results[0]["pics"] = $picsArr;

			$this->param = $results[0]['designer'];
			$results[0]['designer'] = $this->teamDetail();

			return $results[0];
		}
	}


	/**
		* 新增效果图
		* @return array
		*/
	public function addAlbums(){
		global $dsql;
		global $userLogin;

		$userid    = $userLogin->getMemberID();
		$param     = $this->param;

		$designer = (int)$param['designer'];
		$title    = filterSensitiveWords(addslashes($param['title']));
		$type     = (int)$param['type'];

		$style    = 0;
		$units    = 0;
		$kongjian = "";
		$jubu     = "";
		$comstyle = 0;

		if($type == 0){
			$style = (int)$param['style'];
			$units = (int)$param['units'];
			$kongjian = isset($param['kongjian']) ? join(',',$param['kongjian']) : '';
			$jubu = isset($param['jubu']) ? join(',',$param['jubu']) : '';
		}else{
			$comstyle = (int)$param['comstyle'];
		}

		$litpic  = $param['litpic'];
		$imglist = $param['imglist'];
		$area    = (float)$param['area'];
		$apartment = (int)$param['apartment'];
		$note    = filterSensitiveWords(addslashes($param['note']));
		$pubdate = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__renovation_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => '您还未开通装修公司！');
		}

		if(!verifyModuleAuth(array("module" => "renovation"))){
			return array("state" => 200, "info" => '商家权限验证失败！');
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => '您的公司信息还在审核中，请通过审核后再发布！');
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => '您的公司信息审核失败，请通过审核后再发布！');
		}

		$sid = $userResult[0]['id'];

		if(empty($designer)){
			return array("state" => 200, "info" => '请选择设计师');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `company` = $sid AND `id` = ".$designer);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 200, "info" => '设计师不属于您的公司，或已经删除，请确认后重试！');
		}

		if(empty($title)){
			return array("state" => 200, "info" => '请输入效果图标题');
		}

		if(empty($litpic)){
			return array("state" => 200, "info" => '请上传缩略图');
		}

		if(empty($imglist)){
			return array("state" => 200, "info" => "请上传图集");
		}

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__renovation_case` (`title`, `type`, `kongjian`, `jubu`, `comstyle`, `style`, `litpic`, `designer`, `apartment`, `units`, `area`, `state`, `note`, `pics`, `pubdate`) VALUES ('$title', '$type', '$kongjian', '$jubu', '$comstyle', '$style', '$litpic', '$designer', '$apartment', '$units', '$area', '1', '$note', '$imglist', '".GetMkTime(time())."')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){
			return $aid;
		}else{
			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');
		}

	}



	/**
		* 修改效果图
		* @return array
		*/
	public function editAlbums(){
		global $dsql;
		global $userLogin;

		$userid    = $userLogin->getMemberID();
		$param     = $this->param;

		$id       = (int)$param['id'];
		$designer = (int)$param['designer'];
		$title    = filterSensitiveWords(addslashes($param['title']));
		$type     = (int)$param['type'];

		$style    = 0;
		$units    = 0;
		$kongjian = "";
		$jubu     = "";
		$comstyle = "";

		if($type == 0){
			$style = (int)$param['style'];
			$units = (int)$param['units'];
			$kongjian = isset($param['kongjian']) ? join(',',$param['kongjian']) : '';
			$jubu = isset($param['jubu']) ? join(',',$param['jubu']) : '';
		}else{
			$comstyle = (int)$param['comstyle'];
		}

		$litpic  = $param['litpic'];
		$imglist = $param['imglist'];
		$area    = (float)$param['area'];
		$apartment = (int)$param['apartment'];
		$note    = filterSensitiveWords(addslashes($param['note']));
		$pubdate = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__renovation_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => '您还未开通装修公司！');
		}

		if(!verifyModuleAuth(array("module" => "renovation"))){
			return array("state" => 200, "info" => '商家权限验证失败！');
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => '您的公司信息还在审核中，请通过审核后再发布！');
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => '您的公司信息审核失败，请通过审核后再发布！');
		}

		$sid = $userResult[0]['id'];


		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_case` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];

			$company = 0;
			$sql = $dsql->SetQuery("SELECT `company` FROM `#@__renovation_team` WHERE `id` = ".$results['designer']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$company = $ret[0]['company'];
			}else{
				return array("state" => 200, "info" => '公司不存在或已删除！');
			}

			if($company != $sid){
				return array("state" => 101, "info" => '权限不足，请确认帐户信息后再进行操作！');
			}
		}


		if(empty($designer)){
			return array("state" => 200, "info" => '请选择设计师');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `company` = $sid AND `id` = ".$designer);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 200, "info" => '设计师不属于您的公司，或已经删除，请确认后重试！');
		}

		if(empty($title)){
			return array("state" => 200, "info" => '请输入效果图标题');
		}

		if(empty($litpic)){
			return array("state" => 200, "info" => '请上传缩略图');
		}

		if(empty($imglist)){
			return array("state" => 200, "info" => "请上传图集");
		}

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__renovation_case` SET `title` = '$title', `type` = '$type', `kongjian` = '$kongjian', `jubu` = '$jubu', `comstyle` = '$comstyle', `style` = '$style', `litpic` = '$litpic', `designer` = '$designer', `apartment` = '$apartment', `units` = '$units', `area` = '$area', `note` = '$note', `pics` = '$imglist' WHERE `id` = ".$id);
		$ret = $dsql->dsqlOper($archives, "update");

		if($ret == "ok"){
			return "修改成功！";
		}else{
			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');
		}

	}


	/**
		* 删除效果图
		* @return array
		*/
	public function delAlbums(){
		global $dsql;
		global $userLogin;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = ".$uid);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 101, "info" => '公司信息不存在，删除失败！');
		}else{
			$sid = $ret[0]['id'];
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_case` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];

			$company = 0;
			$sql = $dsql->SetQuery("SELECT `company` FROM `#@__renovation_team` WHERE `id` = ".$results['designer']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$company = $ret[0]['company'];
			}

			if($company == $sid){

					//删除缩略图
					delPicFile($results['litpic'], "delThumb", "renovation");

					//删除图集
					$pics = explode(",", $results['pics']);
					foreach($pics as $k => $v){
						delPicFile($v, "delAtlas", "renovation");
					}

					$archives = $dsql->SetQuery("DELETE FROM `#@__renovation_case` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");
					return '删除成功！';

			}else{
				return array("state" => 101, "info" => '权限不足，请确认帐户信息后再进行操作！');
			}
		}else{
			return array("state" => 101, "info" => '成员不存在，或已经删除！');
		}

	}


	/**
     * 装修案例
     * @return array
     */
	public function diary(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$type = $btype = $style = $units = $area = $price = $comstyle = $addrid = $u = $company = $designer = $community = $title = $orderby = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$type      = (int)$this->param['type'];
				$btype     = $this->param['btype'];
				$style     = $this->param['style'];
				$units     = $this->param['units'];
				$comstyle  = $this->param['comstyle'];
				$addrid    = $this->param['addrid'];
				$area      = $this->param['area'];
				$price     = $this->param['price'];
				$u         = $this->param['u'];
				$company   = $this->param['company'];
				$designer  = $this->param['designer'];
				$community = $this->param['community'];
				$title     = $this->param['title'];
				$orderby   = $this->param['orderby'];
				$page      = $this->param['page'];
				$pageSize  = $this->param['pageSize'];
			}
		}

		$where = " WHERE `state` = 1";

        $cityid = getCityId($this->param['cityid']);
        if($cityid){
            $where2 = " AND `cityid` = $cityid";
        }

        $storeSql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__renovation_store` WHERE 1=1".$where2);
        $storeResult = $dsql->dsqlOper($storeSql, "results");
        $userid = array();
        if($storeResult) {
            foreach ($storeResult as $key => $store) {
                $userSql = $dsql->SetQuery("SELECT `id`, `name` FROM `#@__renovation_team` WHERE `company` =" . $store['id']);
                $userResult = $dsql->dsqlOper($userSql, "results");
                if ($userResult) {
                    foreach ($userResult as $ke => $user) {
                        array_push($userid, $user['id']);
                    }
                }
            }
						if($userid){
	            $where .= " AND `designer` in (" . join(",", $userid) . ")";
		        }else{
		        	$where .= " AND 4 = 5";
		        }
        }else{
            $where .= " AND 2=3";
        }

		if($type !== ""){
			$where .= " AND `type` = ".$type;

			if($type == 0){
				if(!empty($style)){
					$where .= " AND `style` = ".$style;
				}
				if(!empty($units)){
					$where .= " AND `units` = ".$units;
				}
			}elseif($type == 1){
				if(!empty($comstyle)){
					$where .= " AND `comstyle` = ".$comstyle;
				}

				//遍历地区
				if(!empty($addrid)){
					if($dsql->getTypeList($addrid, "site_area")){
						$addridArr = arr_foreach($dsql->getTypeList($addrid, "site_area"));
						$addridArr = join(',',$addridArr);
						$lower = $addrid.",".$addridArr;
					}else{
						$lower = $addrid;
					}
					$where .= " AND `addrid` in ($lower)";
				}
			}
		}


		if(!empty($btype)){
			$where .= " AND `btype` = ".$btype;
		}

		//面积
		if($area != ""){
			$area = explode(",", $area);
			if(empty($area[0])){
				$where .= " AND `area` < " . $area[1];
			}elseif(empty($price[1])){
				$where .= " AND `area` > " . $area[0];
			}else{
				$where .= " AND `area` BETWEEN " . $area[0] . " AND " . $area[1];
			}
		}

		//价格
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

		if(!empty($designer)){
			$where .= " AND `designer` = ".$designer;
		}

		//会员中心请求
		if($u == 1){

			$uid = $userLogin->getMemberID();

			if(!verifyModuleAuth(array("module" => "renovation"))){
				return array("state" => 200, "info" => '商家权限验证失败！');
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = $uid");
			$storeRes = $dsql->dsqlOper($sql, "results");
			if($storeRes){
				$company = $storeRes[0]['id'];
			}else{
				$company = "-1";
			}

		}

		if(!empty($company)){
			$teamids = array();
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `company` = ".$company);
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach($results as $k => $v){
					$teamids[] = $v['id'];
				}
			}
			if(!empty($teamids)){
				$where .= " AND `designer` in (".join(",", $teamids).")";
			}else{
				$where .= " AND 1 = 2";
			}
		}

		if(!empty($community)){
			$where .= " AND `communityid` = ".$community;
		}

		if(!empty($title)){
			$where .= " AND `title` like '%".$title."%'";
		}

		$order = " ORDER BY `weight` DESC, `id` DESC";
		if($orderby == "click"){
			$order = " ORDER BY `click` DESC, `weight` DESC, `id` DESC";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `type`, `btype`, `litpic`, `style`, `units`, `comstyle`, `area`, `price`, `designer`, `click`, `pubdate` FROM `#@__renovation_diary`".$where.$order);

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
		$RenrenCrypt = new RenrenCrypt();

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id'] = $val['id'];
				$list[$key]['title'] = $val['title'];
				$list[$key]['type'] = $val['type'];
				$list[$key]['area'] = $val['area'];
				$list[$key]['price'] = $val['price'];
				$list[$key]['click'] = $val['click'];
				$list[$key]["litpic"] = getFilePath($val["litpic"]);

				$fid = $RenrenCrypt->php_decrypt(base64_decode($val["litpic"]));
				$picwidth = $picheight = 0;
				if(is_numeric($fid)){
					$sql = $dsql->SetQuery("SELECT `width`, `height` FROM `#@__attachment` WHERE `id` = '$fid'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$picwidth = $ret[0]['width'];
						$picheight = $ret[0]['height'];
					}
				}else{
					$rpic = str_replace('/uploads', '', $val["litpic"]);
					$sql = $dsql->SetQuery("SELECT `width`, `height` FROM `#@__attachment` WHERE `path` = '$rpic'");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$picwidth = $ret[0]['width'];
						$picheight = $ret[0]['height'];
					}
				}
				$list[$key]['picwidth'] = $picwidth;
				$list[$key]['picheight'] = $picheight;

				$list[$key]['pubdate'] = $val['pubdate'];

				$btype = $val['btype'];
				if(!empty($btype)){
					$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$btype);
					$typename  = $dsql->dsqlOper($archives, "results");
					if($typename){
						$btype = $typename[0]['typename'];
					}
				}
				$list[$key]["btype"] = $btype;

				//家装
				if($val['type'] == 0){

					//风格
					$style = $val['style'];
					if(!empty($style)){
						$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$style);
						$typename  = $dsql->dsqlOper($archives, "results");
						if($typename){
							$style = $typename[0]['typename'];
						}
					}
					$list[$key]["style"] = $style;

					//户型
					$units = $val['units'];
					if(!empty($units)){
						$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$units);
						$typename  = $dsql->dsqlOper($archives, "results");
						if($typename){
							$units = $typename[0]['typename'];
						}
					}
					$list[$key]["units"] = $units;

				}

				//公装
				if($val['type'] == 0){

					//类型
					$comstyle = $val['comstyle'];
					if(!empty($comstyle)){
						$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$comstyle);
						$typename  = $dsql->dsqlOper($archives, "results");
						if($typename){
							$comstyle = $typename[0]['typename'];
						}
					}
					$list[$key]["comstyle"] = $comstyle;
				}

				//设计师
				$this->param = $val['designer'];
				$list[$key]['designer'] = $this->teamDetail();

				$param = array(
					"service"     => "renovation",
					"template"    => "case-detail",
					"id"          => $val['id']
				);
				$list[$key]['url'] = getUrlPath($param);
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 施工案例详细信息
     * @return array
     */
	public function diaryDetail(){
		global $dsql;
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_diary` WHERE `state` = 1 AND `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results[0]["litpicSource"] = $results[0]["litpic"];
			$results[0]["litpic"] = getFilePath($results[0]["litpic"]);
			$results[0]["unitspicSource"] = $results[0]["unitspic"];
			$results[0]["unitspic"] = !empty($results[0]["unitspic"]) ? getFilePath($results[0]["unitspic"]) : "";

			$btype = $results[0]['btype'];
			if(!empty($btype)){
				$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$btype);
				$typename  = $dsql->dsqlOper($archives, "results");
				if($typename){
					$btype = $typename[0]['typename'];
				}
			}
			$results[0]["btypeid"] = $results[0]['btype'];
			$results[0]["btype"] = $btype;

			//家装
			if($results[0]['type'] == 0){

				//风格
				$style = $results[0]['style'];
				if(!empty($style)){
					$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$style);
					$typename  = $dsql->dsqlOper($archives, "results");
					if($typename){
						$style = $typename[0]['typename'];
					}
				}
				$results[0]["styleid"] = $results[0]['style'];
				$results[0]["style"] = $style;

				//户型
				$units = $results[0]['units'];
				if(!empty($units)){
					$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$units);
					$typename  = $dsql->dsqlOper($archives, "results");
					if($typename){
						$units = $typename[0]['typename'];
					}
				}
				$results[0]["unitsid"] = $results[0]['units'];
				$results[0]["units"] = $units;

			}

			//公装
			if($results[0]['type'] == 1){

				//类型
				$comstyle = $results[0]['comstyle'];
				if(!empty($comstyle)){
					$archives = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_type` WHERE `id` = ".$comstyle);
					$typename  = $dsql->dsqlOper($archives, "results");
					if($typename){
						$comstyle = $typename[0]['typename'];
					}
				}
				$results[0]["comstyleid"] = $results[0]['comstyle'];
				$results[0]["comstyle"] = $comstyle;
			}

			//小区详细信息
			if($results[0]['communityid'] != 0){
				$this->param = $results[0]['communityid'];
				$results[0]['community'] = $this->communityDetail();
			}

			//设计师
			$this->param = $results[0]['designer'];
			$results[0]['designer'] = $this->teamDetail();

			//设计方案
			$caseName = "";
			$sql = $dsql->SetQuery("SELECT `title` FROM `#@__renovation_case` WHERE `id` = ".$results[0]['case']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$caseName = $ret[0]['title'];
			}
			$results[0]['caseName'] = $caseName;

			$picsArr = array();
			$pics = $results[0]['pics'];
			if(!empty($pics)){
				$pics = explode("||", $pics);
				foreach($pics as $key => $val){
					$value = explode("##", $val);
					$picsArr[$key]['pathSource'] = $value[0];
					$picsArr[$key]['path'] = getFilePath($value[0]);
					$picsArr[$key]['note'] = $value[1];
				}
			}
			$results[0]["pics"] = $picsArr;

			unset($results[0]['weight']);
			unset($results[0]['state']);
			unset($results[0]['pubdate']);


			//验证是否已经收藏
			$params = array(
				"module" => "renovation",
				"temp"   => "case-detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$results[0]['collect'] = $collect == "has" ? 1 : 0;

			return $results[0];
		}
	}


	/**
     * 日记内容列表
     * @return array
     */
	public function diaryList(){
		global $dsql;
		$pageinfo = $list = array();
		$aid = $page = $orderby = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$aid       = $this->param['aid'];
				$orderby   = $this->param['orderby'];
				$page      = $this->param['page'];
				$pageSize  = $this->param['pageSize'];
			}
		}

		if(!is_numeric($aid)) return array("state" => 200, "info" => '格式错误！');

		$where = " WHERE `state` = 0 AND `diary` = ".$aid;


		$orderby = " ORDER BY `id` ASC";
		if(!empty($orderby)){
			$orderby = " ORDER BY `id` DESC";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `body`, `pubdate` FROM `#@__renovation_diarylist`".$where.$orderby);

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
		* 新增案例
		* @return array
		*/
	public function addCase(){
		global $dsql;
		global $userLogin;

		$userid    = $userLogin->getMemberID();
		$param     = $this->param;

		$designer = (int)$param['designer'];
		$case     = (int)$param['case'];
		$title    = filterSensitiveWords(addslashes($param['title']));
		$type     = (int)$param['type'];

		$community = 0;
		$style    = 0;
		$units    = 0;
		$comstyle = 0;

		if($type == 0){
			$community = (int)$param['community'];
			$style = (int)$param['style'];
			$units = (int)$param['units'];
		}else{
			$comstyle = (int)$param['comstyle'];
		}

		$btype   = (int)$param['btype'];
		$litpic  = $param['litpic'];
		$unitspic = $param['unitspic'];
		$imglist = $param['imglist'];
		$area    = (float)$param['area'];
		$price   = (float)$param['price'];
		$began   = !empty($param['began']) ? GetMkTime($param['began']) : 0;
		$end     = filterSensitiveWords(addslashes($param['end']));
		$pubdate = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__renovation_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => '您还未开通装修公司！');
		}

		if(!verifyModuleAuth(array("module" => "renovation"))){
			return array("state" => 200, "info" => '商家权限验证失败！');
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => '您的公司信息还在审核中，请通过审核后再发布！');
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => '您的公司信息审核失败，请通过审核后再发布！');
		}

		$sid = $userResult[0]['id'];

		if(empty($designer)){
			return array("state" => 200, "info" => '请选择设计师');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `company` = $sid AND `id` = ".$designer);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 200, "info" => '设计师不属于您的公司，或已经删除，请确认后重试！');
		}

		if(empty($title)){
			return array("state" => 200, "info" => '请输入案例标题');
		}

		if(empty($litpic)){
			return array("state" => 200, "info" => '请上传缩略图');
		}

		if(empty($imglist)){
			return array("state" => 200, "info" => "请上传施工现场图");
		}

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__renovation_diary` (`title`, `type`, `style`, `units`, `comstyle`, `btype`, `litpic`, `designer`, `area`, `unitspic`, `price`, `case`, `communityid`, `began`, `end`, `state`, `pics`, `pubdate`) VALUES ('$title', '$type', '$style', '$units', '$comstyle', '$btype', '$litpic', '$designer', '$area', '$unitspic', '$price', '$case', '$community', '$began', '$end', '1', '$imglist', '".GetMkTime(time())."')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){
			return $aid;
		}else{
			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');
		}

	}



	/**
		* 修改案例
		* @return array
		*/
	public function editCase(){
		global $dsql;
		global $userLogin;

		$userid    = $userLogin->getMemberID();
		$param     = $this->param;

		$id       = (int)$param['id'];
		$designer = (int)$param['designer'];
		$case     = (int)$param['case'];
		$title    = filterSensitiveWords(addslashes($param['title']));
		$type     = (int)$param['type'];

		$community = 0;
		$style    = 0;
		$units    = 0;
		$comstyle = "";

		if($type == 0){
			$community = (int)$param['community'];
			$style = (int)$param['style'];
			$units = (int)$param['units'];
		}else{
			$comstyle = (int)$param['comstyle'];
		}

		$btype   = (int)$param['btype'];
		$litpic  = $param['litpic'];
		$unitspic = $param['unitspic'];
		$imglist = $param['imglist'];
		$area    = (float)$param['area'];
		$price   = (float)$param['price'];
		$began   = !empty($param['began']) ? GetMkTime($param['began']) : 0;
		$end     = filterSensitiveWords(addslashes($param['end']));
		$pubdate = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$userSql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__renovation_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if(!$userResult){
			return array("state" => 200, "info" => '您还未开通装修公司！');
		}

		if(!verifyModuleAuth(array("module" => "renovation"))){
			return array("state" => 200, "info" => '商家权限验证失败！');
		}

		if($userResult[0]['state'] == 0){
			return array("state" => 200, "info" => '您的公司信息还在审核中，请通过审核后再发布！');
		}

		if($userResult[0]['state'] == 2){
			return array("state" => 200, "info" => '您的公司信息审核失败，请通过审核后再发布！');
		}

		$sid = $userResult[0]['id'];

		if(empty($designer)){
			return array("state" => 200, "info" => '请选择设计师');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_team` WHERE `company` = $sid AND `id` = ".$designer);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 200, "info" => '设计师不属于您的公司，或已经删除，请确认后重试！');
		}

		if(empty($title)){
			return array("state" => 200, "info" => '请输入案例标题');
		}

		if(empty($litpic)){
			return array("state" => 200, "info" => '请上传缩略图');
		}

		if(empty($imglist)){
			return array("state" => 200, "info" => "请上传施工现场图");
		}

		//保存到主表
		$archives = $dsql->SetQuery("UPDATE `#@__renovation_diary` SET `title` = '$title', `type` = '$type', `style` = '$style', `units` = '$units', `comstyle` = '$comstyle', `btype` = '$btype', `litpic` = '$litpic', `area` = '$area', `unitspic` = '$unitspic', `price` = '$price', `designer` = '$designer', `case` = '$case', `communityid` = '$community', `began` = '$began', `end` = '$end', `pics` = '$imglist' WHERE `id` = ".$id);
		$ret = $dsql->dsqlOper($archives, "update");

		if($ret == "ok"){
			return "修改成功！";
		}else{
			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');
		}

	}


	/**
		* 删除案例
		* @return array
		*/
	public function delCase(){
		global $dsql;
		global $userLogin;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = ".$uid);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 101, "info" => '公司信息不存在，删除失败！');
		}else{
			$sid = $ret[0]['id'];
		}

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_diary` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];

			$company = 0;
			$sql = $dsql->SetQuery("SELECT `company` FROM `#@__renovation_team` WHERE `id` = ".$results['designer']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$company = $ret[0]['company'];
			}

			if($company == $sid){

					//删除缩略图
					delPicFile($results['litpic'], "delThumb", "renovation");

					//删除图集
					$pics = explode(",", $results['pics']);
					foreach($pics as $k => $v){
						delPicFile($v, "delAtlas", "renovation");
					}

					$archives = $dsql->SetQuery("DELETE FROM `#@__renovation_diary` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");
					return '删除成功！';

			}else{
				return array("state" => 101, "info" => '权限不足，请确认帐户信息后再进行操作！');
			}
		}else{
			return array("state" => 101, "info" => '成员不存在，或已经删除！');
		}

	}


	/**
     * 公司留言
     * @return array
     */
	public function guest(){
		global $dsql;
		$pageinfo = $list = array();
		$company = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$company  = $this->param['company'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if(!is_numeric($company)) return array("state" => 200, "info" => '格式错误！');
        $where = " AND `state` = 1";
        $cityid = getCityId($this->param['cityid']);
        if($cityid){
            $where2 = " AND `cityid` = $cityid";
        }

        $storeSql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__renovation_store` WHERE 1=1".$where2);
        $storeResult = $dsql->dsqlOper($storeSql, "results");
        if($storeResult){
            $storeid = array();
            foreach($storeResult as $key => $store){
                array_push($storeid, $store['id']);
            }
            $where .= " AND `company` in (".join(",", $storeid).")";
        }else{
            $where .= " AND 2=3";
        }

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `people`, `contact`, `ip`, `ipaddr`, `note`, `reply`, `pubdate` FROM `#@__renovation_guest` WHERE `company` = $company $where ORDER BY `id` DESC");

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
     * 发表留言
     * @return array
     */
	public function sendGuest(){
		global $dsql;
		$param = $this->param;

		$company  = $param['company'];
		$people   = $param['people'];
		$contact  = $param['contact'];
		$note     = $param['note'];

		if(empty($company) || empty($people) || empty($contact) || empty($note)){
			return array("state" => 200, "info" => '必填项不得为空！');
		}

		$archives = $dsql->SetQuery("INSERT INTO `#@__renovation_guest` (`company`, `people`, `contact`, `ip`, `ipaddr`, `note`, `state`, `pubdate`) VALUES ('$company', '$people', '$contact', '".GetIP()."', '".getIpAddr(GetIP())."', '$note', 0, ".GetMkTime(time()).")");
		$results  = $dsql->dsqlOper($archives, "update");
		if($results == "ok"){
			return "留言成功！";
		}else{
			return array("state" => 200, "info" => '留言失败！');
		}

	}


	/**
     * 公司预约
     * @return array
     */
	public function rese(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$company = $u = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$u        = $this->param['u'];
				$company  = $this->param['company'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		//会员中心请求
		if($u == 1){

			$uid = $userLogin->getMemberID();

			if(!verifyModuleAuth(array("module" => "renovation"))){
				return array("state" => 200, "info" => '商家权限验证失败！');
			}

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = $uid");
			$storeRes = $dsql->dsqlOper($sql, "results");
			if($storeRes){
				$company = $storeRes[0]['id'];
			}

		}

		if(!is_numeric($company)) return array("state" => 200, "info" => '格式错误！');

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `people`, `contact`, `community`, `userid`, `state` FROM `#@__renovation_rese` WHERE `company` = $company");

		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//未处理
		$state0 = $dsql->dsqlOper($archives." AND `state` = 0", "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"state0" => $state0,
			"totalCount" => $totalCount
		);

		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";

		$list = array();
		$results = $dsql->dsqlOper($archives." ORDER BY `id` DESC".$where, "results");
		if($results){
			foreach ($results as $key => $value) {
				$list[$key]['id'] = $value['id'];
				$list[$key]['people'] = $value['people'];
				$list[$key]['contact'] = $value['contact'];
				$list[$key]['community'] = $value['community'];

				$designer = "无";
				if($value['userid']){
					$sql = $dsql->SetQuery("SELECT `name` FROM `#@__renovation_team` WHERE `id` = ".$value['userid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$designer = $ret[0]['name'];
					}
				}
				$list[$key]['designer'] = $designer;

				$list[$key]['state'] = $value['state'];
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 发表预约
     * @return array
     */
	public function sendRese(){
		global $dsql;
		$param = $this->param;

		$company     = $param['company'];
		$userid      = $param['userid'];
		$people      = $param['people'];
		$contact     = $param['contact'];
		$community   = $param['community'];
		$appointment = $param['appointment'];
		$budget      = $param['budget'];
		$units       = $param['units'];
		$body        = $param['body'];

		if(empty($company) || empty($people) || empty($contact)){
			return array("state" => 200, "info" => '必填项不得为空！');
		}

		$archives = $dsql->SetQuery("INSERT INTO `#@__renovation_rese` (`company`, `userid`, `people`, `contact`, `community`, `appointment`, `budget`, `units`, `body`, `ip`, `ipaddr`, `state`, `pubdate`) VALUES ('$company', '$userid', '$people', '$contact', '$community', ".(!empty($appointment) ? GetMkTime($appointment) : 0).", '$budget', '$units', '$body', '".GetIP()."', '".getIpAddr(GetIP())."', 0, ".GetMkTime(time()).")");
		$results  = $dsql->dsqlOper($archives, "update");
		if($results == "ok"){
			return "预约成功！";
		}else{
			return array("state" => 200, "info" => '预约失败！');
		}

	}


	/**
		* 公司确认预约信息
		* @return array
		*/
	public function updateRese(){
		global $dsql;
		global $userLogin;
		$param = $this->param;

		$id = (int)$param['id'];

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = ".$uid);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			return array("state" => 101, "info" => '公司信息不存在，删除失败！');
		}else{
			$sid = $ret[0]['id'];
		}

		$sql = $dsql->SetQuery("SELECT `company` FROM `#@__renovation_rese` WHERE `id` = ".$id);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$company = $ret[0]['company'];
			if($company == $sid){

				$sql = $dsql->SetQuery("UPDATE `#@__renovation_rese` SET `state` = 1 WHERE `id` = ".$id);
				$ret = $dsql->dsqlOper($sql, "update");
				if($ret == "ok"){

					return "ok";

				}else{
					return array("state" => 101, "info" => '更新失败，请稍后重试！');
				}

			}else{
				return array("state" => 101, "info" => '权限不足，请确认帐户信息后再进行操作！');
			}

		}else{
			return array("state" => 101, "info" => '预约信息不存在或已经删除！');
		}

	}


	/**
     * 申请免费设计
     * @return array
     */
	public function sendEntrust(){
		global $dsql;
		$param = $this->param;

		$people  = $param['people'];
		$contact = $param['contact'];
		$addrid  = $param['addrid'];
		$body    = $param['body'];

		if(empty($people) || empty($contact) || empty($addrid)){
			return array("state" => 200, "info" => '必填项不得为空！');
		}

        $cityid = getCityId($this->param['cityid']);

		$archives = $dsql->SetQuery("INSERT INTO `#@__renovation_entrust` (`cityid`, `people`, `contact`, `addrid`, `body`, `ip`, `ipaddr`, `state`, `pubdate`) VALUES ('$cityid', '$people', '$contact', '$addrid', '$body', '".GetIP()."', '".getIpAddr(GetIP())."', 0, ".GetMkTime(time()).")");
		$results  = $dsql->dsqlOper($archives, "update");
		if($results == "ok"){
			return "申请成功！";
		}else{
			return array("state" => 200, "info" => '申请失败！');
		}

	}


	/**
     * 装修大学
     * @return array
     */
	public function news(){
		global $dsql;
		$pageinfo = $list = array();
		$typeid = $ispic = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$typeid   = $this->param['typeid'];
				$ispic    = $this->param['ispic'];
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

		//遍历分类
		if(!empty($typeid)){
			global $arr_data;
			$arr_data = "";
			$typeArr = $dsql->getTypeList($typeid, "renovation_newstype");
			if($typeArr){
				$lower = arr_foreach($typeArr);
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}
			$where .= " AND `typeid` in ($lower)";
		}

		//必须有图片
		if($ispic == 1){
			$where .= " AND `litpic` <> ''";
		}

		$archives = $dsql->SetQuery("SELECT `id`, `title`, `litpic`, `click`, `description`, `writer`, `pubdate` FROM `#@__renovation_news` WHERE `arcrank` = 0".$where." ORDER BY `weight` DESC, `id` DESC");
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
			foreach ($results as $key => $value) {
				$list[$key]['id']      = $value['id'];
				$list[$key]['title']   = $value['title'];
				$list[$key]['litpic']  = getFilePath($value['litpic']);
				$list[$key]['click']   = $value['click'];
				$list[$key]['description']  = $value['description'];
				$list[$key]['writer']  = $value['writer'];
				$list[$key]['pubdate'] = $value['pubdate'];

				$param = array(
					"service"     => "renovation",
					"template"    => "raiders-detail",
					"id"          => $value['id']
				);
				$list[$key]['url'] = getUrlPath($param);
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 装修大学信息详细
     * @return array
     */
	public function newsDetail(){
		global $dsql;
		$newsDetail = array();

		if(empty($this->param)){
			return array("state" => 200, "info" => '信息ID不得为空！');
		}

		if(!is_numeric($this->param)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT * FROM `#@__renovation_news` WHERE `arcrank` = 0 AND `id` = ".$this->param);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$newsDetail["id"]          = $results[0]['id'];
			$newsDetail["title"]       = $results[0]['title'];
			$newsDetail["typeid"]      = $results[0]['typeid'];
			$newsDetail["cityid"]      = $results[0]['cityid'];

			$typename = "";
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__renovation_newstype` WHERE `id` = ".$results[0]['typeid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$typename = $ret[0]['typename'];
			}
			$newsDetail['typename']    = $typename;

			$newsDetail["click"]       = $results[0]['click'];
			$newsDetail["source"]      = $results[0]['source'];
			$newsDetail["writer"]      = $results[0]['writer'];
			$newsDetail["keyword"]     = $results[0]['keyword'];
			$newsDetail["description"] = $results[0]['description'];
			$newsDetail["body"]        = $results[0]['body'];
			$newsDetail["pubdate"]     = $results[0]['pubdate'];
		}
		return $newsDetail;
	}


	/**
     * 装修大学分类
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
		$results = $dsql->getTypeList($type, "renovation_newstype", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}



	/**
		* 配置商铺
		* @return array
		*/
	public function storeConfig(){
		global $dsql;
		global $userLogin;

		$userid      = $userLogin->getMemberID();
		$param       = $this->param;
		$company     = filterSensitiveWords(addslashes($param['company']));
		$addrid      = (int)$param['addrid'];
		$cityid      = (int)$param['cityid'];
		$address     = filterSensitiveWords(addslashes($param['address']));
		$lnglat      = $param['lnglat'];
		$logo        = $param['logo'];
		$people      = filterSensitiveWords(addslashes($param['people']));
		$contact     = filterSensitiveWords(addslashes($param['contact']));
		$qq          = filterSensitiveWords(addslashes($param['qq']));
		$range       = isset($param['range']) ? join(',',$param['range']) : '';
		$jiastyle    = isset($param['jiastyle']) ? join(',',$param['jiastyle']) : '';
		$comstyle    = isset($param['comstyle']) ? join(',',$param['comstyle']) : '';
		$style       = isset($param['style']) ? join(',',$param['style']) : '';
		$scale       = filterSensitiveWords(addslashes($param['scale']));
		$afterService = filterSensitiveWords(addslashes($param['afterService']));
		$initDesign  = filterSensitiveWords(addslashes($param['initDesign']));
		$initBudget  = filterSensitiveWords(addslashes($param['initBudget']));
		$detaDesign  = filterSensitiveWords(addslashes($param['detaDesign']));
		$detaBudget  = filterSensitiveWords(addslashes($param['detaBudget']));
		$material    = filterSensitiveWords(addslashes($param['material']));
		$normative   = filterSensitiveWords(addslashes($param['normative']));
		$speService  = filterSensitiveWords(addslashes($param['speService']));
		$comType     = filterSensitiveWords(addslashes($param['comType']));
		$regFunds    = filterSensitiveWords(addslashes($param['regFunds']));
		$operPeriodb = !empty($param['operPeriodb']) ? GetMkTime($param['operPeriodb']) : 0;
		$operPeriode = !empty($param['operPeriode']) ? GetMkTime($param['operPeriode']) : 0;
		$founded     = !empty($param['founded']) ? GetMkTime($param['founded']) : 0;
		$authority   = filterSensitiveWords(addslashes($param['authority']));
		$operRange   = filterSensitiveWords(addslashes($param['operRange']));
		$inspection  = !empty($param['inspection']) ? GetMkTime($param['inspection']) : 0;
		$regnumber   = filterSensitiveWords(addslashes($param['regnumber']));
		$legalPer    = filterSensitiveWords(addslashes($param['legalPer']));
		$body        = filterSensitiveWords(addslashes($param['body']));
		$certs       = $param['certs'];
		$pubdate     = GetMkTime(time());

		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		//验证会员类型
		$userDetail = $userLogin->getMemberInfo();
		if($userDetail['userType'] != 2){
			return array("state" => 200, "info" => '账号验证错误，操作失败！');
		}

		if(!verifyModuleAuth(array("module" => "renovation"))){
			return array("state" => 200, "info" => '商家权限验证失败！');
		}

		if(empty($company)){
			return array("state" => 200, "info" => '请输入公司名称');
		}

		if(empty($addrid)){
			return array("state" => 200, "info" => '请选择所在区域');
		}

		if(empty($address)){
			return array("state" => 200, "info" => '请输入公司地址');
		}

		if(empty($logo)){
			return array("state" => 200, "info" => '请上传公司LOGO');
		}

		if(empty($people)){
			return array("state" => 200, "info" => '请输入联系人');
		}

		if(empty($contact)){
			return array("state" => 200, "info" => '请输入联系电话');
		}

		$userSql = $dsql->SetQuery("SELECT `id` FROM `#@__renovation_store` WHERE `userid` = ".$userid);
		$userResult = $dsql->dsqlOper($userSql, "results");

		//新商铺
		if(!$userResult){

			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__renovation_store` (`cityid`, `company`, `addrid`, `logo`, `userid`, `people`, `contact`, `qq`, `address`, `lnglat`, `range`, `jiastyle`, `comstyle`, `style`, `body`, `state`, `certs`, `scale`, `afterService`, `initDesign`, `initBudget`, `detaDesign`, `detaBudget`, `material`, `normative`, `speService`, `comType`, `regFunds`, `operPeriodb`, `operPeriode`, `founded`, `authority`, `operRange`, `inspection`, `regnumber`, `legalPer`, `pubdate`) VALUES ('$cityid', '$company', '$addrid', '$logo', '$userid', '$people', '$contact', '$qq', '$address', '$lnglat', '$range', '$jiastyle', '$comstyle', '$style', '$body', '0', '$certs', '$scale', '$afterService', '$initDesign', '$initBudget', '$detaDesign', '$detaBudget', '$material', '$normative', '$speService', '$comType', '$regFunds', '$operPeriodb', '$operPeriode', '$founded', '$authority', '$operRange', '$inspection', '$regnumber', '$legalPer', '$pubdate')");
			$aid = $dsql->dsqlOper($archives, "lastid");

			if(is_numeric($aid)){

				//后台消息通知
				updateAdminNotice("renovation", "store");

				return "配置成功，您的公司正在审核中，请耐心等待！";
			}else{
				return array("state" => 200, "info" => '配置失败，请查检您输入的信息是否符合要求！');
			}

		//更新商铺信息
		}else{

			//保存到主表
			$archives = $dsql->SetQuery("UPDATE `#@__renovation_store` SET `cityid` = '$cityid', `company` = '$company', `addrid` = '$addrid', `logo` = '$logo', `userid` = '$userid', `people` = '$people', `contact` = '$contact', `qq` = '$qq', `address` = '$address', `lnglat` = '$lnglat', `range` = '$range', `jiastyle` = '$jiastyle', `comstyle` = '$comstyle', `style` = '$style', `body` = '$body', `state` = '0', `certs` = '$certs', `scale` = '$scale', `afterService` = '$afterService', `initDesign` = '$initDesign', `initBudget` = '$initBudget', `detaDesign` = '$detaDesign', `detaBudget` = '$detaBudget', `material` = '$material', `normative` = '$normative', `speService` = '$speService', `comType` = '$comType', `regFunds` = '$regFunds', `operPeriodb` = '$operPeriodb', `operPeriode` = '$operPeriode', `founded` = '$founded', `authority` = '$authority', `operRange` = '$operRange', `inspection` = '$inspection', `regnumber` = '$regnumber', `legalPer` = '$legalPer' WHERE `userid` = ".$userid);
			$results = $dsql->dsqlOper($archives, "update");

			if($results == "ok"){

				//后台消息通知
				updateAdminNotice("renovation", "store");

				return "保存成功！";
			}else{
				return array("state" => 200, "info" => '配置失败，请查检您输入的信息是否符合要求！');
			}

		}

	}





}
