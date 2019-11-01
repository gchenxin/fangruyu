<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 黄页模块API接口
 *
 * @version        $Id: huangye.class.php 2014-3-24 下午14:51:14 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class huangye {
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
     * 黄页基本参数
     * @return array
     */
	public function config(){

		require(HUONIAOINC."/config/huangye.inc.php");

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

		// $domainHuangye = getDomain('huangye', 'config');
		// $customChannelDomain = $domainHuangye['domain'];
		// if($customSubDomain == 0){
		// 	$customChannelDomain = "http://".$customChannelDomain;
		// }elseif($customSubDomain == 1){
		// 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
		// }elseif($customSubDomain == 2){
		// 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
		// }

		// include HUONIAOINC.'/siteModuleDomain.inc.php';
		$customChannelDomain = getDomainFullUrl('huangye', $customSubDomain);

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
     * 黄页分类
     * @return array
     */
	public function type(){
		global $dsql;
		$type = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格addr式错误！');
			}else{
				$type     = (int)$this->param['type'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}

		$results = $dsql->getTypeList($type, "huangyetype", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 分类模糊匹配
     * @return array
     */
	public function searchType(){
		global $dsql;
		$key = trim($this->param['key']);

		$list = array();
		if(!empty($key)){
			$archives = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__huangyeype` WHERE (`typename` like '%".$key."%' OR `seotitle` like '%".$key."%' OR `keywords` like '%".$key."%' OR `description` like '%".$key."%') AND `parentid` != 0 LIMIT 0,10");
			$results  = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach ($results as $key => $value) {

					$list[$key]['id'] = $value['id'];
					global $data;
					$data = "";
					$typeArr = getParentArr("infotype", $value['id']);
					$typeArr = array_reverse(parent_foreach($typeArr, "typename"));
					$list[$key]['typename'] = join(" > ", $typeArr);

				}
			}
		}

		return $list;
	}


	/**
     * 黄页分类详细信息
     * @return array
     */
	public function typeDetail(){
		global $dsql;
		$id = $this->param;

		$id = !is_numeric($id) ? $id['id'] : $id;

		if(empty($id)) return array("state" => 200, "info" => '格式错误！');

		$archives = $dsql->SetQuery("SELECT `id`, `typename`, `seotitle`, `keywords`, `description` FROM `#@__huangyetype` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$param = array(
				"service"     => "huangye",
				"template"    => "list",
				"typeid"      => $id
			);
			$results[0]["url"] = getUrlPath($param);

			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__huangyetype` WHERE `parentid` = ".$id);
			$res = $dsql->dsqlOper($sql, "totalCount");
			if($res > 0){
				$results[0]["lower"] = $res;
			}

			return $results;
		}

	}


	/**
     * 黄页地区
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
				$child    = $this->param['child'];
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
                if(!empty($child)){
                    foreach ($result as $key => $value) {
                        $sql = $dsql->SetQuery("SELECT * FROM `#@__site_area` WHERE `parentid` = ".$value['cid']." ORDER BY `weight`");
                        $result = $dsql->dsqlOper($sql, "results");
                        array_push($cityArr, array(
                            "id" => $value['cid'],
                            "typename" => $value['typename'],
                            "pinyin" => $value['pinyin'],
		                        "hot" => $value['hot'],
                            "lower" => $result
                        ));
                    }
                }else{
                    foreach ($result as $key => $value) {
                        array_push($cityArr, array(
                            "id" => $value['cid'],
                            "typename" => $value['typename'],
                            "pinyin" => $value['pinyin'],
                            "lower" => array()
                        ));
                    }
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
     * 黄页列表
     * @return array
     */
	public function ilist(){
		global $dsql;
		global $userLogin;
		global $langData;
		$pageinfo = $list = $itemList = array();
		$nature = $typeid = $addrid = $valid = $title = $rec = $fire = $top = $thumb = $orderby = $u = $state = $userid = $page = $pageSize = $where = $where1 = $return = "";
		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$return   = $this->param['return'];
				$nature   = $this->param['nature'];
				$typeid   = $this->param['typeid'];
				$addrid   = $this->param['addrid'];
				$valid    = $this->param['valid'];
				$title    = $this->param['keywords'];
				$rec      = $this->param['rec'];
				$fire     = $this->param['fire'];
				$top      = $this->param['top'];
				$thumb    = $this->param['thumb'];
				$orderby  = $this->param['orderby'];
				$lng      = $this->param['lng'];
				$lat      = $this->param['lat'];
				$u        = $this->param['u'];
				$state    = $this->param['state'];
				$userid   = $this->param['userid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$cityid = getCityId($this->param['cityid']);
		if($cityid){
			$where .= " AND `cityid` = ".$cityid;
		}

		//是否输出当前登录会员的信息
		if($u != 1){
			$where .= " AND l.`arcrank` = 1";
		}else{
			$uid = $userLogin->getMemberID();
			$where .= " AND l.`userid` = ".$uid;

			if($state != ""){
				if($state == 4){
					$now = GetMkTime(time());
					$where1 = " AND (`valid` < ".$now." OR `valid` = 0)";
				}else{
					$where1 = " AND l.`arcrank` = ".$state;
				}
			}
		}
		//只查找不过期的信息
		if($u != 1){
			$now = GetMkTime(time());
			$where .= " AND (`valid` > ".$now." OR `valid` = 0)";
		}

		//信息性质
		if(!empty($nature)){

			//个人
			if($nature == 1){
				$where .= " AND ((SELECT `mtype` FROM `#@__member` WHERE `id` = l.`userid`) = 1 OR l.`userid` = -1)";

			//商家
			}elseif($nature == 2){
				$where .= " AND ((SELECT `mtype` FROM `#@__member` WHERE `id` = l.`userid`) = 2)";
			}

		}

		//遍历分类
		if(!empty($typeid)){
			$typeArr_ = $dsql->getTypeList($typeid, "huangyetype");
			if($typeArr_){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($typeArr_);
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}
			$where .= " AND `typeid` in ($lower)";
		}

		//遍历地区
		if(!empty($addrid)){
			$addrArr_ = $dsql->getTypeList($addrid, "site_area");
			if($addrArr_){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($addrArr_);
				$lower = $addrid.",".join(',',$lower);
			}else{
				$lower = $addrid;
			}
			$where .= " AND `addr` in ($lower)";
		}

		//有效期
		if(!empty($valid)){
			$where .= " AND `valid` = ".$valid;
		}

		if(!empty($title)){

			//搜索记录
			siteSearchLog("huangye", $title);

			$where .= " AND `title` like '%".$title."%'";
		}

		//推荐
		if(!empty($rec)){
			$where .= " AND `rec` = 1";
		}

		//火急
		if(!empty($fire)){
			$where .= " AND `fire` = 1";
		}

		//置顶
		if(!empty($top)){
			$where .= " AND `top` = 1";
		}

		//有图
		if(!empty($thumb)){
			$where .= " AND pics != ''";
		}

		//指定会员
		if(!empty($userid)){
			$where .= " AND `userid` = $userid";
		}

		$juli = "";
		if($lng && $lat){
			$juli = ", ROUND(
		        6378.138 * 2 * ASIN(
		            SQRT(POW(SIN(($lat * PI() / 180 - l.`latitude` * PI() / 180) / 2), 2) + COS($lat * PI() / 180) * COS(l.`latitude` * PI() / 180) * POW(SIN(($lng * PI() / 180 - l.`longitude` * PI() / 180) / 2), 2))
		        ) * 1000
		    ) AS juli";

			//筛选10KM范围内的店铺
			/*$where .= " AND ROUND(
				6378.138 * 2 * ASIN(
					SQRT(POW(SIN(($lat * PI() / 180 - l.`latitude` * PI() / 180) / 2), 2) + COS($lat * PI() / 180) * COS(l.`latitude` * PI() / 180) * POW(SIN(($lng * PI() / 180 - l.`longitude` * PI() / 180) / 2), 2))
				) * 1000
			) < 10000";*/
		}

		$order = " ORDER BY `isbid` DESC, `bid_price` DESC, `bid_start` ASC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
		//发布时间
		if($orderby == "1"){
			$order = " ORDER BY `isbid` DESC, `bid_price` DESC, `bid_start` ASC, `pubdate` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
		//浏览量
		}elseif($orderby == "2"){
			$order = " ORDER BY `isbid` DESC, `bid_price` DESC, `bid_start` ASC, `click` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
		//今日浏览量
		}elseif($orderby == "2.1"){
			$order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = curdate() ORDER BY `isbid` DESC, `bid_price` DESC, `bid_start` ASC, `click` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
		//昨日浏览量
		}elseif($orderby == "2.2"){
			$order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY) ORDER BY `isbid` DESC, `bid_price` DESC, `bid_start` ASC, `click` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
		//本周浏览量
		}elseif($orderby == "2.3"){
			$order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') >= DATE_SUB(curdate(), INTERVAL 7 DAY) ORDER BY `isbid` DESC, `bid_price` DESC, `bid_start` ASC, `click` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
		//本月浏览量
		}elseif($orderby == "2.4"){
			$order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m') = DATE_FORMAT(curdate(), '%Y-%m') ORDER BY `isbid` DESC, `bid_price` DESC, `bid_start` ASC, `click` DESC, `top` DESC, `fire` DESC, `rec` DESC, `weight` DESC, `id` DESC";
		//随机
		}elseif($orderby == "3"){
			$order = " ORDER BY rand()";
		// 距离
		}elseif($orderby == "5"){
			if($lng && $lat){
				$order = " ORDER BY `juli` ASC, `weight` DESC, `id` DESC";
				$where .= " AND `longitude` != 0 AND `latitude` != 0";
			}
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		//评论排行
		if(strstr($orderby, "4")){
			//今日评论
			if($orderby == "4.1"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m-%d') = curdate()";
			//昨日评论
			}elseif($orderby == "4.2"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY)";
			//本周评论
			}elseif($orderby == "4.3"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m-%d') >= DATE_SUB(curdate(), INTERVAL 7 DAY)";
			//本月评论
			}elseif($orderby == "4.4"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(l.`pubdate`), '%Y-%m') = DATE_FORMAT(curdate(), '%Y-%m')";
			}

			$order = " ORDER BY total DESC";

			$archives = $dsql->SetQuery("SELECT l.`id`, l.`title`, l.`typeid`, l.`color`, l.`litpic`, l.`pubdate`, l.`addr`, l.`click`, l.`tel`, l.`teladdr`, l.`email`, l.`weixin`, l.`weixinQr`, l.`address`, l.`project`, l.`rec`, l.`fire`, l.`top`, l.`rz1`, l.`rz2`, l.`rz3`, l.`rz4`, l.`userid`, l.`arcrank`, l.`valid`, l.`isbid`, l.`bid_end`, l.`bid_price`, (SELECT COUNT(`id`) FROM `#@__huangyecommon` WHERE `aid` = l.`id` AND `ischeck` = 1) AS total FROM `#@__huangyelist` l WHERE 1 = 1".$where);

		//普通查询
		}else{
			$archives = $dsql->SetQuery("SELECT l.`id`, l.`title`, l.`typeid`, l.`color`, l.`litpic`, l.`pubdate`, l.`addr`, l.`click`, l.`tel`, l.`teladdr`, l.`email`, l.`weixin`, l.`weixinQr`, l.`address`, l.`project`, l.`rec`, l.`fire`, l.`top`, l.`rz1`, l.`rz2`, l.`rz3`, l.`rz4`, l.`userid`, l.`arcrank`, l.`valid`, l.`isbid`, l.`bid_end`, l.`bid_price` $juli FROM `#@__huangyelist` as l WHERE 1 = 1".$where);
		}

		$archivesCount = $dsql->SetQuery("SELECT `id` FROM `#@__huangyelist` l WHERE 1 = 1".$where);
		//总条数
		$totalCount = $dsql->dsqlOper($archivesCount, "totalCount");
		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		if($return != "count"){
			//会员列表需要统计信息状态
			if($u == 1 && $userLogin->getMemberID() > -1){
				//待审核
				$totalGray = $dsql->dsqlOper($archives." AND `arcrank` = 0", "totalCount");
				//已审核
				$totalAudit = $dsql->dsqlOper($archives." AND `arcrank` = 1", "totalCount");
				//拒绝审核
				$totalRefuse = $dsql->dsqlOper($archives." AND `arcrank` = 2", "totalCount");
				//过期
				$now = GetMkTime(time());
				$totalExpire = $dsql->dsqlOper($archives." AND (`valid` < ".$now." AND `valid` <> 0)", "totalCount");

				$pageinfo['gray'] = $totalGray;
				$pageinfo['audit'] = $totalAudit;
				$pageinfo['refuse'] = $totalRefuse;
				$pageinfo['expire'] = $totalExpire;
			}

			$atpage = $pageSize*($page-1);
			$where = " LIMIT $atpage, $pageSize";
			$results = $dsql->dsqlOper($archives.$where1.$order.$where, "results");

			if($results){

				/*$param = array(
					"service"     => "huangye",
					"template"    => "list",
					"id"          => "%id%"
				);
				$typeurlParam = getUrlPath($param);

				$param = array(
					"service"     => "huangye",
					"template"    => "detail",
					"id"          => "%id%"
				);
				$urlParam = getUrlPath($param);*/

				foreach($results as $key => $val){
					$list[$key]['id']          = $val['id'];
					$list[$key]['title']       = $val['title'];
					$list[$key]['color']       = $val['color'];
					$list[$key]['litpic']      = !empty($val['litpic']) ? getFilePath($val['litpic']) : "";

					//会员发布信息统计
					$archives = $dsql->SetQuery("SELECT `id` FROM `#@__huangyelist` WHERE `userid` = ".$val['userid']);
					$results = $dsql->dsqlOper($archives, "totalCount");
					$list[$key]['fabuCount'] = $results;

					global $data;
					$data = "";
					$addrArr = getParentArr("huangyeaddr", $val['addr']);
					$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
					$list[$key]['address']     = join(" - ", $addrArr);

					$list[$key]['typeid']      = $val['typeid'];
					$list[$key]['project']     = $val['project'];

					$data = "";
					$typeLevel = getParentArr("huangyetype",$val['typeid']);
					if($typeLevel){
						$typeArr = array_reverse(parent_foreach($typeLevel, "typename"));
						$list[$key]['typeLevel'] = $typeArr;

						$data = "";
						$typeArr = array_reverse(parent_foreach($typeLevel, "id"));
						$list[$key]['typeLevelid'] = $typeArr;
						$list[$key]['typeLevelNum'] = 0;
					}else{
						$list[$key]['typeLevelNum'] = 0;
						$list[$key]['typeLevel'] = "";
						$list[$key]['typeLevelid'] = "";
					}

					$list[$key]['tel']     = $val['tel'];
					$list[$key]['teladdr'] = $val['teladdr'];
					$list[$key]['email'] = $val['email'];
					$list[$key]['weixin'] = $val['weixin'];
					$list[$key]['weixinQr'] = !empty($val['weixinQr']) ? getFilePath($val['weixinQr']) : "";;;
					$list[$key]['addressdet'] = $val['address'];

					$list[$key]['click'] = $val['click'];

					$list[$key]['pubdate'] = $val['pubdate'];
					$list[$key]['fire']    = $val['fire'];
					$list[$key]['rec']     = $val['rec'];
					$list[$key]['top']     = $val['top'];
					$list[$key]['isbid']   = $val['isbid'];
					$list[$key]['rz1']     = $val['rz1'];
					$list[$key]['rz2']     = $val['rz2'];
					$list[$key]['rz3']     = $val['rz3'];
					$list[$key]['rz4']     = $val['rz4'];

					if(isset($val['juli'])){
						$list[$key]['juli'] = $val['juli'] > 1000 ? sprintf("%.1f", $val['juli'] / 1000) . $langData['siteConfig'][13][23] : $val['juli'] . $langData['siteConfig'][13][22];  //距离   //千米  //米
					}

					$param = array(
						"service"  => "huangye",
						"template" => "detail",
						"id"       => $val['id']
					);
					$list[$key]['url'] = getUrlPath($param);

					$list[$key]['desc'] = cn_substrR(strip_tags($val['body']), 80);

					$archives = $dsql->SetQuery("SELECT `id` FROM `#@__huangyecommon` WHERE `aid` = ".$val['id']." AND `ischeck` = 1");
					$totalCount = $dsql->dsqlOper($archives, "totalCount");
					$list[$key]['common'] = $totalCount;

					//会员信息
					$member = getMemberDetail($val['userid']);
					$list[$key]['member'] = array(
						"nickname"     => $member['nickname'],
						"photo"        => $member['photo'],
						"userType"     => $member['userType'],
						"emailCheck"   => $member['emailCheck'],
						"phoneCheck"   => $member['phoneCheck'],
						"certifyState" => $member['certifyState']
					);

					//会员中心显示信息状态
					if($u == 1 && $userLogin->getMemberID() > -1){

						$now = GetMkTime(time());
						if($val['valid'] < $now AND $val['valid'] != 0){
							$list[$key]['arcrank'] = 4;
						}else{
							$list[$key]['arcrank'] = $val['arcrank'];
						}

						//显示竞价结束时间、每日预算
						$list[$key]['bid_price'] = $val['bid_price'];
						$list[$key]['bid_end'] = $val['bid_end'];
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
	public function detail(){
		global $dsql;
		global $userLogin;
		$infoDetail = array();
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//判断是否管理员已经登录
		$where = "";


		// 此处是为了判断信息在未审核状态下，只有管理员和发布者可以在前台浏览
		// if($userLogin->getUserID() == -1){
		//
		// 	$where = " AND `arcrank` = 1";
		//
		// 	//如果没有登录再验证会员是否已经登录
		// 	if($userLogin->getMemberID() == -1){
		// 		$where = " AND `arcrank` = 1";
		// 	}else{
		// 		$where = " AND `userid` = ".$userLogin->getMemberID();
		// 	}
		//
		// }

		$archives = $dsql->SetQuery("SELECT * FROM `#@__huangyelist` WHERE `id` = ".$id.$where);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){

			$valid = $results[0]['valid'];
			$now = GetMkTime(time());
			if($valid && $now > $valid){
				header("location:/404.html");
			}
			$pubdate = $results[0]['pubdate'];

			$infoDetail["id"]      = $results[0]['id'];
			$infoDetail["typeid"]  = $results[0]['typeid'];
			$infoDetail["cityid"]  = $results[0]['cityid'];
			$infoDetail["title"]   = $results[0]['title'];
			$infoDetail["litpic"]  = empty($results[0]['litpic']) ? "" : getFilePath($results[0]['litpic']);
			$infoDetail["peise"]   = $results[0]['peise'];
			$infoDetail["titlecode"]   = urlencode($results[0]['title']);
			$infoDetail["addrid"]  = $results[0]['addr'];

			// 导航菜单
			$arc = $dsql->SetQuery("SELECT * FROM `#@__huangyenav` WHERE `aid` = ".$infoDetail["id"]." AND `show` = 1 ORDER BY `weight`");
			$res  = $dsql->dsqlOper($arc, "results");
			if($res){
				$infoDetail["nav"]  = $res;
			}else{
				$infoDetail["nav"]  = array();
			}


			global $data;
			$data = "";
			$addrArr = getParentArr("site_area", $results[0]['addr']);
			$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
			$infoDetail['address'] = join(" > ", $addrArr);

			$infoDetail["addressdet"]  = $results[0]['address'];

			$infoDetail["validVal"]  = $results[0]['valid'];

			$infoDetail["person"]   = $results[0]['person'];

			$RenrenCrypt = new RenrenCrypt();
			$infoDetail["tel"]      = base64_encode($RenrenCrypt->php_encrypt($results[0]['tel']));

			//if($userLogin->getUserID() > -1 || $userLogin->getMemberID() > -1){
				$infoDetail["telNum"]   = $results[0]['tel'];
			//}

			$infoDetail["teladdr"]  = $results[0]['teladdr'];
			$infoDetail["longitude"]= empty($results[0]['longitude']) ? 0 : $results[0]['longitude'];
			$infoDetail["latitude"] = empty($results[0]['latitude']) ? 0 : $results[0]['latitude'];
			$infoDetail["qq"]       = $results[0]['qq'];
			$infoDetail["email"]    = $results[0]['email'];
			$infoDetail["weixin"]   = $results[0]['weixin'];
			$infoDetail["weixinQr"] = !empty($results[0]['weixinQr']) ? getFilePath($results[0]['weixinQr']) : "";
			$infoDetail["imglist"]  = empty($results[0]['pics']) ? "" : explode(",", $results[0]['pics']);
			$infoDetail["click"]    = $results[0]['click'];
			$infoDetail["ip"]       = preg_replace('/(\d+)\.(\d+)\.(\d+)\.(\d+)/is',"$1.$2.*.*", $results[0]['ip']);
			$infoDetail["ipaddr"]   = $results[0]['ipaddr'];
			$infoDetail["userid"]   = $results[0]['userid'];
			$infoDetail["pubdate"]  = $pubdate;
			$infoDetail['member']   = getMemberDetail($results[0]['userid']);
			$infoDetail["rec"]      = $results[0]['rec'];
			$infoDetail["fire"]     = $results[0]['fire'];
			$infoDetail["top"]      = $results[0]['top'];
			$infoDetail["rz1"]      = $results[0]['rz1'];
			$infoDetail["rz2"]      = $results[0]['rz2'];
			$infoDetail["rz3"]      = $results[0]['rz3'];
			$infoDetail["rz4"]      = $results[0]['rz4'];

			//会员发布信息统计
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__huangyelist` WHERE `userid` = ".$results[0]['userid']);
			$results1 = $dsql->dsqlOper($archives, "totalCount");
			$infoDetail['fabuCount'] = $results1;

			//会员名
			if($results[0]['userid'] == 0){
				$username = "admin";
				$photo = "";
			}else{
				$sql = $dsql->SetQuery("SELECT `mtype`, `nickname`, `company`, `photo` FROM `#@__member` WHERE `id` = ".$results[0]['userid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$username = $ret[0]['mtype'] && !empty($ret[0]['company']) ? $ret[0]['company'] : $ret[0]['nickname'];
					$photo    = $ret[0]['photo'];
				}
			}

			$infoDetail['user'] = array(
				"username" => $username,
				"photo"    => empty($photo) ? "" : getFilePath($photo)
			);

			//有效期
			if($valid != 0){
				$now = GetMkTime(time());
				if($pubdate + $valid * 86400 < $now){
					$infoDetail["valid"] = "已过期";
				}else{
					$infoDetail["valid"] = ceil((($pubdate + $valid * 86400) - $now) / 86400) . "天后过期";
				}
			}else{
				$infoDetail["valid"] = "长期有效";
			}

			//获取手机号码共发布多少条信息
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__huangyelist` WHERE `tel` = '".$results[0]['tel']."'");
			$results2 = $dsql->dsqlOper($archives, "totalCount");
			$infoDetail["telCount"] = $results2;

			//获取商家共发布多少条信息
			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__huangyelist` WHERE `userid` = '".$results[0]['userid']."'");
			$results2 = $dsql->dsqlOper($archives, "totalCount");
			$infoDetail["storeCount"] = $results2;

			$archives = $dsql->SetQuery("SELECT `id` FROM `#@__huangyecommon` WHERE `aid` = ".$results[0]['id']." AND `ischeck` = 1");
			$totalCount = $dsql->dsqlOper($archives, "totalCount");
			$infoDetail['common'] = $totalCount;

			// 获取店铺链接
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__business_list` WHERE `uid` = ".$results[0]['userid']);
			$ret = $dsql->dsqlOper($sql, "results");
			$url = "";
			if($ret){
				$param = array(
					"service"     => "business",
					"template"    => "detail",
					"id"          => $ret[0]['id']
				);
				$url = getUrlPath($param);
			}
			$infoDetail['bUrl'] = $url;

			// 获取官网链接
			$webUrl = "";

			global $installModuleArr;
			if(in_array('website', $installModuleArr)){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__website` WHERE `userid` = ".$results[0]['userid']);
				$ret  = $dsql->dsqlOper($sql, "results");
				if($ret){
					$id = $ret[0]['id'];
					$webHandels = new handlers('website', "storeDetail");
					$webDetail  = $webHandels->getHandle($id);
					if(is_array($webDetail)){
						$webUrl = $webDetail['info']['domain'];
					}
				}
			}
			$infoDetail['webUrl'] = $webUrl;

			//验证是否已经收藏
			$params = array(
				"module" => "huangye",
				"temp"   => "detail",
				"type"   => "add",
				"id"     => $results[0]['id'],
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$infoDetail['collect'] = $collect == "has" ? 1 : 0;

		}

		return $infoDetail;
	}


	/**
     * 评论列表
     * @return array
     */
	public function common(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$infoid = $orderby = $page = $pageSize = $where = "";

		if(!is_array($this->param)){
			return array("state" => 200, "info" => '格式错误！');
		}else{
			$infoid    = $this->param['infoid'];
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

		$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__huangyecommon` WHERE `aid` = ".$infoid." AND `ischeck` = 1 AND `floor` = 0".$oby);
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

		$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__huangyecommon` WHERE `floor` = ".$fid." AND `ischeck` = 1 ORDER BY `id` DESC");
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

		$archives = $dsql->SetQuery("SELECT `duser` FROM `#@__huangyecommon` WHERE `id` = ".$id);
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

			$archives = $dsql->SetQuery("UPDATE `#@__huangyecommon` SET `good` = `good` + 1 WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");

			$archives = $dsql->SetQuery("UPDATE `#@__huangyecommon` SET `duser` = '$nuser' WHERE `id` = ".$id);
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

		include HUONIAOINC."/config/huangye.inc.php";
		$ischeck = (int)$customCommentCheck;

		$archives = $dsql->SetQuery("INSERT INTO `#@__huangyecommon` (`aid`, `floor`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `ischeck`, `duser`) VALUES ('$aid', '$id', '".$userLogin->getMemberID()."', '$content', ".GetMkTime(time()).", '".GetIP()."', '".getIpAddr(GetIP())."', 0, 0, $ischeck, '')");
		$lid  = $dsql->dsqlOper($archives, "lastid");
		if($lid){
			$archives = $dsql->SetQuery("SELECT `id`, `aid`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__huangyecommon` WHERE `id` = ".$lid);
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				$list['id']      = $results[0]['id'];
				$list['aid']     = $results[0]['aid'];
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

	/**
     * 发布&修改黄页
     * @return array
     */
	public function put(){
		global $dsql;
		global $userLogin;

		$userid = $userLogin->getMemberID();
		if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

		$param = $this->param;
		// print_r($param);

		$id          = (int)$param['id'];
		$typeid      = (int)$param['typeid'];
		$title       = $param['title'];
		$litpic      = $param['litpic'];
		$addr        = (int)$param['addr'];
		$address     = $param['address'];
		$lnglat      = $param['lnglat'];
		$peise       = (int)$param['peise'];
		$person      = $param['person'];
		$tel         = $param['tel'];
		$email       = $param['email'];
		$weixin      = $param['weixin'];
		$weixinQr    = $param['weixinQr'];
		$project     = $param['project'];
		$detail      = $param['detail'];
		$imglist     = $param['imglist'];

		if(empty($typeid)){
			return array("state" => 200, "info" => "请选择黄页分类");
		}

		if(empty($title)){
			return array("state" => 200, "info" => "标题不能为空");
		}

		if(trim($litpic) == ''){
			return array("state" => 200, "info" => "请上传logo");
		}

		if(empty($addr)){
			return array("state" => 200, "info" => "请选择所属地区");
		}

		if(trim($lnglat) == ''){
			return array("state" => 200, "info" => "请标注联系地址坐标！");
		}

		/*if(trim($person) == ''){
			echo '{"state": 200, "info": "请输入联系人信息"}';
			exit();
		}*/

		if(trim($tel) == ''){
			return array("state" => 200, "info" => "请输入联系电话");
		}

		/*if(trim($project) == ''){
			echo '{"state": 200, "info": "请填写主营项目"}';
			exit();
		}*/

		// $valid = empty($valid) ? 0 : GetMkTime($valid);  //有效期
		$valid = 0;  //有效期

		// 自定义导航
		/*if(empty($detail)){
			echo '{"state": 200, "info": "请至少填写一项导航"}';
		}*/

		$ip = GetIP();
		$ipAddr = getIpAddr($ip);

		$teladdr = getTelAddr($tel);

		//坐标
		if(!empty($lnglat)){
			$lnglat = explode(",", $lnglat);
			$longitude = $lnglat[0];
			$latitude  = $lnglat[1];
		}


		$click = $rz1 = $rz2 = $rz3 = $rz4 = 0;
		$weight = 50;

		include HUONIAOINC."/config/huangye.inc.php";
		$ischeck = (int)$customFabuCheck;
		$arcrank = $ischeck;

		// 发布
		if(empty($id)){

			$archives = $dsql->SetQuery("INSERT INTO `#@__huangyelist` (`typeid`, `title`, `color`, `peise`, `litpic`, `weight`, `valid`, `addr`, `address`, `longitude`, `latitude`, `project`, `pics`, `person`, `tel`, `teladdr`, `qq`, `email`, `weixin`, `weixinQr`, `click`, `arcrank`, `ip`, `ipaddr`, `pubdate`, `userid`, `admin`, `rec`, `fire`, `top`, `rz1`, `rz2`, `rz3`, `rz4`) VALUES (".$typeid.", '".$title."', '".$color."', '".$peise."', '".$litpic."', ".$weight.", ".$valid.", '".$addr."', '".$address."', '$longitude', '$latitude', '".$project."', '$imglist', '".$person."', '".$tel."', '".$teladdr."', '".$qq."', '".$email."', '".$weixin."', '".$weixinQr."', ".$click.", ".$arcrank.", '".$ip."', '".$ipAddr."', ".GetMkTime(time()).", ".$userid.", ".$userid.", '$rec', '$fire', '$top', '$rz1', '$rz2', '$rz3', '$rz4')");
			$aid = $dsql->dsqlOper($archives, "lastid");

			if(!is_numeric($aid)){
				return array("state" => 200, "info" => "提交失败，请重试");
			}

		// 修改
		}else{

			$archives = $dsql->SetQuery("UPDATE `#@__huangyelist` SET `typeid` = ".$typeid.", `title` = '".$title."', `color` = '".$color."', `peise` = '".$peise."', `litpic` = '".$litpic."', `weight` = ".$weight.", `valid` = '".$valid."', `addr` = ".$addr.", `address` = '".$address."', `longitude` = '".$longitude."', `latitude` = '".$latitude."', `project` = '".$project."', `pics` = '$imglist', `person` = '".$person."', `tel` = '".$tel."', `teladdr` = '".$teladdr."', `qq` = '".$qq."', `email` = '".$email."', `weixin` = '".$weixin."', `weixinQr` = '".$weixinQr."', `arcrank` = ".$arcrank." WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				return array("state" => 200, "info" => "提交失败，请重试");
			}

			$aid = $id;
		}

		// 导航
		if(!empty($detail)){

			// 先删除
			$delSql = $dsql->SetQuery("DELETE FROM `#@__huangyenav` WHERE `aid` = ".$aid);
			$delRet = $dsql->dsqlOper($delSql, "update");

			$naveData = explode(";;;;;;", $detail);

			foreach ($naveData as $k1 => $value1) {
				$item = explode(",,,,,,", $value1);
				foreach ($item as $k2 => $value2) {
					$field = explode("::::::", $value2);
					${$field[0]} = $field[1];
				}
				//保存自定义导航
				$archives = $dsql->SetQuery("INSERT INTO `#@__huangyenav` (`aid`, `nav`, `body`, `mbody`, `show`, `weight`) VALUES (".$aid.", '".$nav."', '".$body."', '".$mbody."', '".$show."', '".$weight."')");
				$fid = $dsql->dsqlOper($archives, "lastid");
			}
		}

		return (empty($id) ? "提交成功" : "修改成功").(!$arcrank ? "，正在审核中，请耐心等待！" : "");

	}

}

//只取数组中的分类名
function parent_foreach_more($arr, $type) {
	global $data;
	if(!empty($arr)){
		if (!is_array ($arr) && $arr != NULL) {
			return $data;
		}
		foreach ($arr as $key => $val){
			if (is_array ($val)){
				parent_foreach($val, $type);
			} else {
				$typeArr = explode(",", $type);
				foreach ($typeArr as $k => $v) {
					if($val != NULL && $key == $v){
						$data[][$v]=$val;
					}
				}
			}
		}
		return $data;
	}else{
		return array();
	}
}
