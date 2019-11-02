<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 活动API接口
 *
 * @version        $Id: huodong.class.php 2016-12-22 上午10:43:10 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class huodong {
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
     * 贴吧基本参数
     * @return array
     */
	public function config(){

		require(HUONIAOINC."/config/huodong.inc.php");

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

		// $domainInfo = getDomain('huodong', 'config');
		// $customChannelDomain = $domainInfo['domain'];
		// if($customSubDomain == 0){
		// 	$customChannelDomain = "http://".$customChannelDomain;
		// }elseif($customSubDomain == 1){
		// 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
		// }elseif($customSubDomain == 2){
		// 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
		// }

		// include HUONIAOINC.'/siteModuleDomain.inc.php';
		$customChannelDomain = getDomainFullUrl('huodong', $customSubDomain);

        //分站自定义配置
        $ser = 'huodong';
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
			$return['softSize']      = $custom_softSize;
			$return['softType']      = $custom_softType;
			$return['thumbSize']     = $custom_thumbSize;
			$return['thumbType']     = $custom_thumbType;
		}

		return $return;

	}


	/**
     * 活动分类
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
		$results = $dsql->getTypeList($type, "huodong_type", $son, $page, $pageSize);
		if($results){
			return $results;
		}
	}


	/**
     * 活动地区
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

        }else {
            $results = $dsql->getTypeList($type, "site_area", $son, $page, $pageSize, '', '', true);
            if ($results) {
                return $results;
            }
        }
	}


	/**
     * 活动列表
     * @return array
     */
	public function hlist(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$typeid = $keywords = $addrid = $times = $orderby = $u = $uid = $state = $feetype = $page = $pageSize = $where = $where1 = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$typeid   = $this->param['typeid'];
				$keywords = $this->param['keywords'];
				$addrid   = $this->param['addrid'];
				$times    = $this->param['times'];
				$orderby  = $this->param['orderby'];
				$u        = $this->param['u'];
				$uid      = $this->param['uid'];
				$state    = $this->param['state'];
				$feetype  = $this->param['feetype'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$cityid = getCityId($this->param['cityid']);
		if($cityid && $u !=1){
			$where .= " AND `cityid` = ".$cityid;
		}else{
			$where .= " AND `cityid` !=0 ";
		}

		$userid = $userLogin->getMemberID();

		//是否输出当前登录会员的信息
		if($u != 1){
			$where .= " AND l.`state` = 1 AND l.`waitpay` = 0";
		}else{
			$where .= " AND l.`uid` = ".$userid;
			if($state != ""){
				$where1 = " AND l.`state` = ".$state;
			}

			// if(!verifyModuleAuth(array("module" => "huodong"))){
			// 	return array("state" => 200, "info" => '商家权限验证失败！');
			// }
		}

		//遍历分类
		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "huodong_type")){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($dsql->getTypeList($typeid, "huodong_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}
			$where .= " AND `typeid` in ($lower)";
		}

		//模糊查询关键字
		if(!empty($keywords)){

			//搜索记录
			siteSearchLog("huodong", $keywords);

			$keywords = explode(" ", $keywords);
			$w = array();
			foreach ($keywords as $k => $v) {
				if(!empty($v)){
					$w[] = "`title` like '%".$v."%'";
				}
			}
			$where .= " AND (".join(" OR ", $w).")";
		}

		//遍历区域
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

		//时间筛选
		$time = GetMkTime(date("Y-m-d", time()));
		if(!empty($times)){

			//今天
			if($times == "today"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(`began`), '%Y-%m-%d') = curdate()";

			//明天
			}elseif($times == "tomorrow"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(`began`), '%Y-%m-%d') = DATE_ADD(curdate(), INTERVAL 1 DAY)";

			//一周以内
			}elseif($times == "week"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(`began`), '%Y-%m-%d') < DATE_ADD(curdate(), INTERVAL 7 DAY) AND `began` >= $time";

			//一月以内
			}elseif($times == "month"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(`began`), '%Y-%m-%d') < DATE_ADD(curdate(), INTERVAL 30 DAY) AND `began` >= $time";

			//其他日期
			}else{
				$time = GetMkTime($times);
				if($time){
					$where .= " AND `began` = $time";
				}else{
					$where .= " AND 1 = 2";
				}
			}

		}

		//收费类型
		if($feetype != ""){
			$where .= " AND `feetype` != $feetype";
		}

		//指定会员的数据
		if(!empty($uid)){
			$where .= " AND `uid` = $uid";
		}

		$order = " ORDER BY `id` DESC";

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		//评论排行
		if($orderby == "reply"){
			$order = " ORDER BY reply DESC, `id` DESC";
		}
		if($orderby == "reg"){
			$order = " ORDER BY reg DESC, `id` DESC";
		}
		if($orderby == "click"){
			$order = " ORDER BY l.`click` DESC, `id` DESC";
		}

		$archives = $dsql->SetQuery("SELECT l.`id`, l.`typeid`, l.`uid`, l.`title`, l.`litpic`, l.`began`, l.`end`, l.`baoming`, l.`baomingend`, l.`addrid`, l.`address`, l.`click`, l.`feetype`, l.`state`, l.`pubdate`, (SELECT COUNT(`id`)  FROM `#@__public_comment` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `type` = 'huodong-detail' AND `pid` = 0) AS reply, (SELECT COUNT(`id`)  FROM `#@__huodong_reg` WHERE `hid` = l.`id`) AS reg, l.`waitpay` FROM `#@__huodong_list` l WHERE 1 = 1".$where);
		$archives_count = $dsql->SetQuery("SELECT count(`id`) FROM `#@__huodong_list` l WHERE 1 = 1".$where);

		//总条数
		$totalResults = $dsql->dsqlOper($archives_count, "results", "NUM");
		$totalCount = (int)$totalResults[0][0];

		//总分页数
		$totalPage = ceil($totalCount/$pageSize);

		if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');

		$pageinfo = array(
			"page" => $page,
			"pageSize" => $pageSize,
			"totalPage" => $totalPage,
			"totalCount" => $totalCount
		);

		//会员列表需要统计信息状态
		if($u == 1 && $userid > -1){
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
		$results = $dsql->dsqlOper($archives.$where1.$order.$where, "results");

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']     = $val['id'];

				//分类
				$list[$key]['typeid'] = $val['typeid'];
				global $data;
				$data = "";
				$typeArr = getParentArr("huodong_type", $val['typeid']);
				$typeArr = array_reverse(parent_foreach($typeArr, "typename"));
				$list[$key]['typename']= $typeArr;

				//区域
				$list[$key]['addrid'] = $val['addrid'];
				global $data;
				$data = "";
				$addrArr = getParentArr("site_area", $val['addrid']);
				$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
				$list[$key]['addrname']= $addrArr;

				//会员信息
				$list[$key]['uid']    = $val['uid'];
				$username = $photo = "";
				$sql = $dsql->SetQuery("SELECT `mtype`, `nickname`, `company`, `photo` FROM `#@__member` WHERE `id` = ".$val['uid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$username = $ret[0]['mtype'] && !empty($ret[0]['company']) ? $ret[0]['company'] : $ret[0]['nickname'];
					$photo    = getFilePath($ret[0]['photo']);
				}
				$list[$key]['username']  = $username;
				$list[$key]['userphoto'] = $photo;

				$list[$key]['title']      = $val['title'];
				$list[$key]['litpic']     = getFilePath($val['litpic']);
				$list[$key]['began']      = $val['began'];
				$list[$key]['end']        = $val['end'];
				$list[$key]['baoming']    = $val['baoming'];
				$list[$key]['baomingend'] = $val['baomingend'];
				$list[$key]['address']    = $val['address'];
				$list[$key]['click']      = $val['click'];
				$list[$key]['feetype']    = $val['feetype'];
				$list[$key]['pubdate']    = $val['pubdate'];

				//最低费用
				if($val['feetype']){
					$mprice = array();
					$sql = $dsql->SetQuery("SELECT `price` FROM `#@__huodong_fee` WHERE `hid` = ".$val['id']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						foreach ($ret as $k => $v) {
							array_push($mprice, $v['price']);
						}
					}
					$list[$key]['mprice'] = min($mprice);
				}


				//会员中心显示信息状态
				if($u == 1 && $userLogin->getMemberID() > -1){
					$list[$key]['state'] = $val['state'];
					$list[$key]['waitpay'] = $val['waitpay'];
				}

				$list[$key]['reply'] = $val['reply'];
				$list[$key]['reg']   = $val['reg'];

				$param = array(
					"service"     => "huodong",
					"template"    => "detail",
					"id"          => $val['id']
				);
				$list[$key]['url'] = getUrlPath($param);


				//报名人数
				$reg = 0;
				$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__huodong_reg` WHERE `hid` = ".$val['id']." AND (`state` = 1 || `state` = 2)");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$reg = $ret[0]['t'];
				}
				$list[$key]['reg'] = $reg;

				//报名截止天数
				$now = GetMkTime(time());
				$list[$key]["surplus"] = ($val['baoming'] ? $val['end'] : $val['baomingend']) - $now;

			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 活动详细
     * @return array
     */
	public function detail(){
		global $dsql;
		global $userLogin;
		$detail = array();
		$id = $this->param;

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//判断是否管理员已经登录
		//功能点：管理员和信息的发布者可以查看所有状态的信息
		$where = "";
		if($userLogin->getUserID() == -1){

			$where = " AND `state` = 1";

			//如果没有登录再验证会员是否已经登录
			if($userLogin->getMemberID() == -1){
				$where = " AND `state` = 1";
			}else{
				$where = " AND (`state` = 1 OR `uid` = ".$userLogin->getMemberID().")";
			}

		}
		$where .= " AND `waitpay` = 0";

		$archives = $dsql->SetQuery("SELECT * FROM `#@__huodong_list` WHERE `id` = ".$id.$where);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$detail["id"]          = $results[0]['id'];
			$detail["uid"]         = $results[0]['uid'];

			//父级ID
			$pid = 0;
			$sql = $dsql->SetQuery("SELECT `parentid` FROM `#@__huodong_type` WHERE `id` = ".$results[0]['typeid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$pid = $ret[0]['parentid'];
			}

			$detail["pid"]         = $pid;
			$detail["cityid"]      = $results[0]['cityid'];
			$detail["typeid"]      = $results[0]['typeid'];
			$detail["title"]       = $results[0]['title'];
			$detail["litpic"]      = getFilePath($results[0]['litpic']);
			$detail["litpicSource"] = !empty($results[0]['litpic']) ? $results[0]['litpic'] : "";
			$detail["began"]       = $results[0]['began'];
			$detail["end"]         = $results[0]['end'];
			$detail["baoming"]     = $results[0]['baoming'];
			$detail["baomingend"]  = $results[0]['baomingend'];
			$detail["addrid"]      = $results[0]['addrid'];
			$detail["address"]     = $results[0]['address'];
			$detail["body"]        = $results[0]['body'];
			$detail["feetype"]     = $results[0]['feetype'];
			$detail["max"]         = $results[0]['max'];
			$detail["contact"]     = $results[0]['contact'];
			$detail["click"]       = $results[0]['click'];
			$detail["pubdate"]     = $results[0]['pubdate'];
			$detail["property"]    = $results[0]['property'] ? unserialize($results[0]['property']) : array();

			//报名截止天数
			$now = GetMkTime(time());
			$detail["surplus"] = ($results[0]['baoming'] ? $results[0]['end'] : $results[0]['baomingend']) - $now;

			//会员名
			$username = "";
			$photo = "";
			$sql = $dsql->SetQuery("SELECT `mtype`, `nickname`, `company`, `photo` FROM `#@__member` WHERE `id` = ".$results[0]['uid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$username = $ret[0]['mtype'] && !empty($ret[0]['company']) ? $ret[0]['company'] : $ret[0]['nickname'];
				$photo    = $ret[0]['photo'];
			}

			$detail['uid']  = $results[0]['uid'];

			//举办过的活动&参与人数统计
			$huodongCount = 0;
			$regCount = 0;
			$sql = $dsql->SetQuery("SELECT count(l.`id`) lcount FROM `#@__huodong_list` l WHERE l.`uid` = ".$results[0]['uid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$huodongCount = $ret[0]['lcount'];
			}
			$sql = $dsql->SetQuery("SELECT count(r.`id`) rcount FROM `#@__huodong_reg` r LEFT JOIN `#@__huodong_list` l ON l.`id` = r.`hid` WHERE (r.`state` = 1 || r.`state` = 2) AND l.`uid` = ".$results[0]['uid']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$regCount = $ret[0]['rcount'];
			}

			$detail['user'] = array(
				"username" => $username,
				"photo"    => getFilePath($photo),
				"huodongCount" => (int)$huodongCount,
				"regCount" => (int)$regCount
			);

			//分类名称
			global $data;
			$data = "";
			$typeArr = getParentArr("huodong_type", $results[0]['typeid']);
			$typeArr = array_reverse(parent_foreach($typeArr, "typename"));
			$detail['typeid']    = $results[0]['typeid'];
			$detail['typename']  = $typeArr;

			//区域名称
			global $data;
			$data = "";
			$detail['addrid'] = $results[0]['addrid'];
			global $data;
			$data = "";
			$addrArr = getParentArr("site_area", $results[0]['addrid']);
			$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
      $detail['addrname'] = $addrArr;

			//报名人数
			$reg = 0;
			$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__huodong_reg` WHERE `hid` = $id AND (`state` = 1 || `state` = 2)");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$reg = $ret[0]['t'];
			}
			$detail['reg'] = $reg;

			//评论人数
			$reply = 0;
			// $sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__huodong_reply` WHERE `hid` = $id and `state` = 1");
			$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'huodong-detail' AND `aid` = '$id' AND `pid` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$reply = $ret[0]['t'];
			}
			$detail['reply'] = $reply;

			//电子票
			if($results[0]['feetype'] == 1){
				$feeList = array();
				$sql = $dsql->SetQuery("SELECT f.`id`, f.`title`, f.`price`, f.`max`, (SELECT count(`id`) FROM `#@__huodong_reg` WHERE `hid` = $id AND `fid` = f.`id`) reg FROM `#@__huodong_fee` f WHERE `hid` = $id ORDER BY `id` ASC");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					foreach ($ret as $key => $value) {
						array_push($feeList, array(
							"id" => $value['id'],
							"title" => $value['title'],
							"price" => $value['price'],
							"max"   => $value['max'],
							"reg"   => $value['reg']
						));
					}
				}
				$detail['feeList'] = $feeList;
			}

			//查询是否已经报名
			$isbaoming = 0;
			$uid = $userLogin->getMemberID();
			if($uid != -1){
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__huodong_reg` WHERE `hid` = $id AND `uid` = $uid AND `state` = 1");
				$ret = $dsql->dsqlOper($sql, "totalCount");
				if($ret > 0){
					$isbaoming = 1;
				}
			}
			$detail['isbaoming'] = $isbaoming;


			//验证是否已经收藏
			$params = array(
				"module" => "huodong",
				"temp"   => "detail",
				"type"   => "add",
				"id"     => $id,
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$detail['collect'] = $collect;

		}
		return $detail;
	}


	/**
     * 主办方列表
     * @return array
     */
	public function organizer(){
		global $dsql;
		global $userLogin;
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

		$order    = " ORDER BY count DESC";
		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT l.`uid`, count(l.`id`) as count, m.`photo`, m.`mtype`, m.`nickname`, m.`company` FROM `#@__huodong_list` l LEFT JOIN `#@__member` m ON m.`id` = l.`uid` GROUP BY l.`uid`");

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
		$results = $dsql->dsqlOper($archives.$order.$where, "results");

		if($results){
			foreach($results as $key => $val){
				$list[$key]['uid']    = $val['uid'];
				$list[$key]['count']  = $val['count'];

				$list[$key]['nickname'] = $val['mtype'] && !empty($val['company']) ? $val['company'] : $val['nickname'];
				$list[$key]['photo']    = getFilePath($val['photo']);

				//回复数量
				$reg = 0;
				$sql = $dsql->SetQuery("SELECT count(r.`id`) count FROM `#@__huodong_reg` r LEFT JOIN `#@__huodong_list` l ON l.`id` = r.`hid` WHERE l.`uid` = ".$val['uid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$reg = $ret[0]['count'];
				}
				$list[$key]['reg'] = $reg;

				$param = array(
					"service"  => "huodong",
					"template" => "business",
					"id"       => $val['uid']
				);
				$list[$key]['url'] = getUrlPath($param);
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 评论列表
     * @return array
     */
	public function reply(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$hid = $uid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$hid      = $this->param['hid'];
				$uid      = $this->param['uid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if(empty($hid)) return array("state" => 200, "info" => '格式错误！');

		$where = " `state` = 1 AND `rid` = 0 AND `hid` = ".$hid;

		//指定会员ID
		if(!empty($uid)){
			$where .= " AND `uid` = ".$uid;
		}

		$order    = " ORDER BY `id` DESC";
		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `uid`, `content`, `pubdate` FROM `#@__huodong_reply` WHERE ".$where);
		$archives_count = $dsql->SetQuery("SELECT count(`id`) FROM `#@__huodong_reply` l WHERE ".$where);

		//总条数
		$totalResults = $dsql->dsqlOper($archives_count, "results", "NUM");
		$totalCount = (int)$totalResults[0][0];

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
		$results = $dsql->dsqlOper($archives.$order.$where, "results");

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']     = $val['id'];
				$list[$key]['uid']    = $val['uid'];

				$list[$key]['content']  = $val['content'];
				$list[$key]['pubdate']  = $val['pubdate'];
				$list[$key]['floortime'] = FloorTime(GetMkTime(time()) - $val['pubdate'], 2);

				//回复数量
				$reply = 0;
				$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_reply` WHERE `state` = 1 AND `rid` = ".$val['id']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$reply = $ret[0]['t'];
				}
				$list[$key]['reply'] = $reply;

				//发布者信息
				$member = array();
				$memberID = $val['uid'];
				if($memberID){
					$memberInfo = $userLogin->getMemberInfo($memberID);
					if(is_array($memberInfo)){
						$member = array(
							"id" => $memberID,
							"photo" => $memberInfo['photo'],
							"nickname" => $memberInfo['nickname']
						);
					}
				}
				$list[$key]['member'] = $member;

				$list[$key]['lower']  = $this->getReplyList($val['id']);
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
	 * 遍历评论子级
	 * @param $id int 评论ID
	 * @return array
	 */
	function getReplyList($id){
		if(empty($id)) return false;
		global $dsql;
		global $userLogin;

		$archives = $dsql->SetQuery("SELECT `id`, `uid`, `content`, `pubdate` FROM `#@__huodong_reply` WHERE `state` = 1 AND `rid` = ".$id);
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		if($totalCount > 0){
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach($results as $key => $val){
					$list[$key]['id']     = $val['id'];
					$list[$key]['uid']    = $val['uid'];

					$list[$key]['content']  = $val['content'];
					$list[$key]['pubdate']  = $val['pubdate'];
					$list[$key]['floortime'] = FloorTime(GetMkTime(time()) - $val['pubdate'], 2);

					//回复数量
					$reply = 0;
					$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_reply` WHERE `state` = 1 AND `rid` = ".$val['id']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$reply = $ret[0]['t'];
					}
					$list[$key]['reply'] = $reply;

					//发布者信息
					$member = array();
					$memberID = $val['uid'];
					if($memberID){
						$memberInfo = $userLogin->getMemberInfo($memberID);
						if(is_array($memberInfo)){
							$member = array(
								"id" => $memberID,
								"photo" => $memberInfo['photo'],
								"nickname" => $memberInfo['nickname']
							);
						}
					}
					$list[$key]['member'] = $member;

					$list[$key]['lower']  = $this->getReplyList($val['id']);
				}
				return $list;
			}
		}
	}


	/**
		* 发表回复
		* @return array
		*/
	public function sendReply(){
		global $dsql;
		global $userLogin;

		$param = $this->param;

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$hid   = $param['hid'];
		$rid   = $param['rid'];
		$content = filterSensitiveWords($param['content']);
		$content = cn_substrR($content, 200);

		$ip = GetIp();
		$pubdate = GetMkTime(time());

		include HUONIAOINC."/config/huodong.inc.php";
		$state = (int)$customCommentCheck;

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__huodong_reply` (`hid`, `rid`, `uid`, `content`, `pubdate`, `ip`, `state`) VALUES ('$hid', '$rid', '$uid', '$content', '$pubdate', '$ip', '$state')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){

			$info = array();
			$memberInfo = $userLogin->getMemberInfo($uid);
			if(is_array($memberInfo)){
				$info = array(
					"id" => $uid,
					"aid" => $aid,
					"photo" => $memberInfo['photo'],
					"nickname" => $memberInfo['nickname'],
					"content" => $content,
					"pubdate" => FloorTime(GetMkTime(time()) - $pubdate, 2),
					"state"   => $state
				);
			}
			return $info;

		}else{
			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');
		}

	}


	/**
		* 报名
		* @return array
		*/
	public function join(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$id = (int)$param['id'];
		$fid = (int)$param['fid'];
		$data = json_decode($param['data'], true);
		$time = GetMkTime(time());

		if(empty($id)) return array("state" => 200, "info" => '活动参数传递错误，报名失败！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		if(!is_array($data)) return array("state" => 200, "info" => '请填写报名信息');

		$data = serialize($data);

		//验证是否已经报名
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__huodong_reg` WHERE `hid` = $id AND `uid` = $uid AND `state` = 1");
		$ret = $dsql->dsqlOper($sql, "totalCount");
		if($ret > 0) return array("state" => 200, "info" => '您已经报过名，无须重复提交！');

		//查询活动ID
		$sql = $dsql->SetQuery("SELECT l.`title`, l.`end`, l.`baoming`, l.`baomingend`, l.`feetype`, l.`max`, (SELECT count(`id`) FROM `#@__huodong_reg` r WHERE r.`hid` = $id AND r.`state` = 1) reg FROM `#@__huodong_list` l WHERE l.`state` = 1 AND l.`id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$ret = $ret[0];
			$huodong = $ret['title'];
			$end  = $ret['baoming'] ? $ret['end'] : $ret['baomingend'];

			if($time > $end) return array("state" => 200, "info" => '报名时间已经截止，报名失败！');

			//收费类型，验证余票
			if($ret['feetype']){

				$sql = $dsql->SetQuery("SELECT f.`title`, f.`price`, f.`max`, (SELECT count(`id`) FROM `#@__huodong_reg` WHERE `hid` = $id AND `fid` = $fid AND `state` = 1) reg FROM `#@__huodong_fee` f WHERE f.`hid` = $id AND f.`id` = $fid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){

					$fee = $ret[0];
					if($fee['reg'] >= $fee['max'] && $fee['max'] != 0){
						return array("state" => 200, "info" => '名额已经用完，报名失败！');
					}

					//如果是免费的直接报名成功
					if($fee['price'] <= 0){
						$cardnum = genSecret(12, 1);
						$sql = $dsql->SetQuery("INSERT INTO `#@__huodong_reg` (`hid`, `fid`, `uid`, `date`, `property`, `code`) VALUES ('$id', '$fid', '$uid', '$time', '$data', '$cardnum')");
						$ret = $dsql->dsqlOper($sql, "lastid");
						if(is_numeric($ret)){
							return "报名成功！";
						}else{
							return array("state" => 200, "info" => '程序错误，报名失败！');
						}
					}

					//返回下单页面
					$param = array(
						"service"  => "huodong",
						"template" => "order",
						"id"       => $id,
						"fid"      => $fid,
						"param"    => "data=".urlencode($data)
					);
					return getUrlPath($param);

				}else{
					return array("state" => 200, "info" => '报名失败，收费项不存在，请确认后重试！');
				}

			//免费类型的报名成功，添加报名数据
			}else{

				//验证余票
				if($ret['max'] <= $ret['reg']){
					return array("state" => 200, "info" => '名额已经用完，报名失败！');
				}

				$cardnum = genSecret(12, 1);
				$sql = $dsql->SetQuery("INSERT INTO `#@__huodong_reg` (`hid`, `fid`, `uid`, `date`, `property`, `code`) VALUES ('$id', '$fid', '$uid', '$time', '$data', '$cardnum')");
				$ret = $dsql->dsqlOper($sql, "lastid");
				if(is_numeric($ret)){
					return "报名成功！";
				}else{
					return array("state" => 200, "info" => '程序错误，报名失败！');
				}
			}

		}else{
			return array("state" => 200, "info" => '活动不存在，报名失败！');
		}

	}



	/**
		* 支付
		* @return array
		*/
	public function pay(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$id = (int)$param['id'];
		$fid = (int)$param['fid'];
		$check = (int)$param['check'];
		$paytype = $param['paytype'];
		$data = $param['data'];
		$time = GetMkTime(time());
		$usePinput  = $param['usePinput'];   //是否使用积分
		$point      = $param['point'];       //使用的积分
		$useBalance = $param['useBalance'];  //是否使用余额
		$balance    = $param['balance'];     //使用的余额
		$paypwd     = $param['paypwd'];      //支付密码

		if(empty($id)) {
			if($check){
				return array("state" => 200, "info" => '活动参数传递错误，报名失败！');
			}else{
				die('活动参数传递错误，报名失败！');
			}
		}

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1) {
			if($check){
				return array("state" => 200, "info" => '登录超时，请重新登录！');
			}else{
				die('登录超时，请重新登录！');
			}
		}

		//验证是否已经报名
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__huodong_reg` WHERE `hid` = $id AND `uid` = $uid AND `state` = 1");
		$ret = $dsql->dsqlOper($sql, "totalCount");
		if($ret > 0) {
			if($check){
				return array("state" => 200, "info" => '您已经报过名，无须重复提交！');
			}else{
				die('您已经报过名，无须重复提交！');
			}
		}

		$dedata = unserialize($data);
		if(!is_array($dedata)) return array("state" => 200, "info" => '报名信息错误，请重新提交！');

		//查询活动ID
		$sql = $dsql->SetQuery("SELECT l.`title`, l.`end`, l.`baoming`, l.`baomingend`, l.`feetype`, l.`max`, (SELECT count(`id`) FROM `#@__huodong_reg` r WHERE r.`hid` = $id) reg FROM `#@__huodong_list` l WHERE l.`state` = 1 AND l.`id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$ret = $ret[0];
			$huodong = $ret['title'];
			$end  = $ret['baoming'] ? $ret['end'] : $ret['baomingend'];

			if($time > $end) {
				if($check){
					return array("state" => 200, "info" => '报名时间已经截止，报名失败！');
				}else{
					die('报名时间已经截止，报名失败！');
				}
			}

			//收费类型，验证余票
			if($ret['feetype']){

				$sql = $dsql->SetQuery("SELECT f.`title`, f.`price`, f.`max`, (SELECT count(`id`) FROM `#@__huodong_reg` WHERE `hid` = $id AND `fid` = $fid AND `state` = 1) reg FROM `#@__huodong_fee` f WHERE f.`hid` = $id AND f.`id` = $fid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){

					$fee = $ret[0];
					if($fee['reg'] >= $fee['max'] && $fee['max'] != 0){
						if($check){
							return array("state" => 200, "info" => '名额已经用完，报名失败！');
						}else{
							die('名额已经用完，报名失败！');
						}
					}

					//如果是免费的直接报名成功
					if($fee['price'] <= 0){
						if($check){
							return array("state" => 200, "info" => '免费活动无须支付！');
						}else{
							die('免费活动无须支付！');
						}
					}

					$price = $fee['price'];

					global $cfg_pointName;

					//查询会员信息
					$userinfo  = $userLogin->getMemberInfo();
					$usermoney = $userinfo['money'];
					$userpoint = $userinfo['point'];

					$tit      = array();
					$useTotal = 0;

					//判断是否使用积分，并且验证剩余积分
					if($usePinput == 1 && !empty($point)){
						if($userpoint < $point){
							if($check){
								return array("state" => 200, "info" => "您的可用".$cfg_pointName."不足，支付失败！");
							}else{
								die("您的可用".$cfg_pointName."不足，支付失败！");
							}
						}
						$useTotal += $point / $cfg_pointRatio;
						$tit[] = 'point';
					}else{
						$point = 0;
					}

					//判断是否使用余额，并且验证余额和支付密码
					if($useBalance == 1 && !empty($balance) && !empty($paypwd)){

						//验证支付密码
						$archives = $dsql->SetQuery("SELECT `id`, `paypwd` FROM `#@__member` WHERE `id` = '$uid'");
						$results  = $dsql->dsqlOper($archives, "results");
						$res = $results[0];
						$hash = $userLogin->_getSaltedHash($paypwd, $res['paypwd']);
						if($res['paypwd'] != $hash){
							if($check){
								return array("state" => 200, "info" => "支付密码输入错误，请重试！");
							}else{
								die("支付密码输入错误，请重试！");
							}
						}

						//验证余额
						if($usermoney < $balance){
							if($check){
								return array("state" => 200, "info" => "您的余额不足，支付失败！");
							}else{
								die("您的余额不足，支付失败！");
							}
						}

						$useTotal += $balance;
						$tit[] = "money";
					}else{
						$balance = 0;
					}

					if($useTotal > $price){
						if($check){
							return array("state" => 200, "info" => "您使用的".join("和", $tit)."超出订单总费用，请重新输入要使用的".join("和", $tit));
						}else{
							die("您使用的".join("和", $tit)."超出订单总费用，请重新输入要使用的".join("和", $tit));
						}
					}

					$payprice = $price - $useTotal;

					//验证
					if($check){
						return "ok";

					//跳转至第三方支付页面
					}else{

						$ordernum = create_ordernum();

						$archives = $dsql->SetQuery("INSERT INTO `#@__huodong_order` (`ordernum`, `uid`, `hid`, `fid`, `price`, `date`, `state`, `paytype`, `point`, `balance`, `payprice`, `property`) VALUES ('$ordernum', '$uid', '$id', '$fid', '$price', ".GetMkTime(time()).", 0, '$paytype', '$point', '$balance', '$payprice', '$data')");

						$return = $dsql->dsqlOper($archives, "update");
						if($return != "ok"){
							if($check){
								return array("state" => 200, "info" => '提交失败，请稍候重试！');
							}else{
								die('提交失败，请稍候重试！');
							}
						}

						if($payprice > 0){
							createPayForm("huodong", $ordernum, $payprice, $paytype, "活动报名");
						}else{
							$sql = $dsql->SetQuery("INSERT INTO `#@__pay_log` (`ordertype`, `ordernum`, `uid`, `body`, `amount`, `paytype`, `state`, `pubdate`) VALUES ('huodong', '$ordernum', '$uid', '$ordernum', '$payprice', '".join(",", $tit)."', 1, ".GetMkTime(time()).")");
							$ret = $dsql->dsqlOper($sql, "lastid");

							$param = array(
								"ordernum" => $ordernum,
								"paytype" => join(",", $tit)
							);
							$this->param = $param;
							$this->paySuccess();

							$param = array(
								"service" => "huodong",
								"template" => "payreturn",
								"ordernum" => $ordernum
							);
							$url = getUrlPath($param);
							header("location:".$url);
						}
					}

				}else{
					if($check){
						return array("state" => 200, "info" => '报名失败，收费项不存在，请确认后重试！');
					}else{
						die('报名失败，收费项不存在，请确认后重试！');
					}
				}

			//免费类型的报名成功，添加报名数据
			}else{
				if($check){
					return array("state" => 200, "info" => '免费活动，无须支付费用！');
				}else{
					die('免费活动，无须支付费用！');
				}
			}

		}else{
			if($check){
				return array("state" => 200, "info" => '活动不存在，支付失败！');
			}else{
				die('活动不存在，支付失败！');
			}
		}

	}


	/**
	 * 支付成功
	 * 此处进行支付成功后的操作，例如发送短信等服务
	 *
	 */
	public function paySuccess(){
		global $cfg_basehost;
		global $dsql;

		$param = $this->param;
		if(!empty($param)){

			$paytype  = $param['paytype'];
			$ordernum = $param['ordernum'];
			$date     = GetMkTime(time());

			//查询订单信息
			$sql = $dsql->SetQuery("SELECT * FROM `#@__huodong_order` WHERE `ordernum` = '$ordernum'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$uid     = $ret[0]['uid'];
				$hid     = $ret[0]['hid'];
				$fid     = $ret[0]['fid'];
				$price   = $ret[0]['price'];
				$point   = $ret[0]['point'];
				$balance = $ret[0]['balance'];
				$balance = $ret[0]['balance'];
				$property = $ret[0]['property'];

				//查询活动信息
				$title = "";
				$sql = $dsql->SetQuery("SELECT `title` FROM `#@__huodong_list` WHERE `id` = $hid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$title = $ret[0]['title'];
				}

				//更新订单状态
				$sql = $dsql->SetQuery("UPDATE `#@__huodong_order` SET `state` = 1 WHERE `ordernum` = '$ordernum'");
				$dsql->dsqlOper($sql, "update");

				//保存操作日志
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '0', '$price', '活动报名：$title', '$date')");
				$dsql->dsqlOper($archives, "update");

				if($point || $balance){
					$archives = $dsql->SetQuery("UPDATE `#@__member` SET `point` = `point` - $point, `money` = `money` - $balance WHERE `id` = $uid");
					$dsql->dsqlOper($archives, "update");

					if($point){
						//保存操作日志
						$archives = $dsql->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '0', '$point', '活动报名：$ordernum', '$date')");
						$dsql->dsqlOper($archives, "update");
					}
				}

				//增加报名记录
				$cardnum = genSecret(12, 1);
				$sql = $dsql->SetQuery("INSERT INTO `#@__huodong_reg` (`hid`, `fid`, `uid`, `date`, `property`, `code`) VALUES ('$hid', '$fid', '$uid', '$date', '$property', '$cardnum')");
				$dsql->dsqlOper($sql, "update");

			}

		}
	}


	/**
		* 报名详情
		* @return array
		*/
	public function regDetail(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$id = (int)$param['id'];

		if(empty($id)) return array("state" => 200, "info" => '活动参数传递错误，操作失败！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		//验证是否已经报名
		$hid = 0;
		$date = 0;
		$property = array();
		$state = 0;
		$code = '';
		$nickname = '';
		$price = 0;
		$sql = $dsql->SetQuery("SELECT r.`hid`, r.`date`, r.`property`, r.`state`, r.`code`, m.`nickname`, f.`price` FROM `#@__huodong_reg` r LEFT JOIN `#@__member` m ON m.`id` = r.`uid` LEFT JOIN `#@__huodong_fee` f ON f.`id` = r.`fid` WHERE r.`id` = $id AND r.`uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$hid = $ret[0]['hid'];
			$date = $ret[0]['date'];
			$property = $ret[0]['property'];
			$property = $property ? unserialize($property) : array();
			$state = $ret[0]['state'];
			$code = $ret[0]['code'];
			$nickname = $ret[0]['nickname'];
			$price = $ret[0]['price'];
		}else{
			return array("state" => 200, "info" => '您还没有报名，或者已经取消！');
		}

		//查询活动ID
		$sql = $dsql->SetQuery("SELECT l.`title`, l.`litpic`, l.`contact`, l.`began`, l.`addrid`, l.`address`, m.`nickname`, m.`company` FROM `#@__huodong_list` l LEFT JOIN `#@__member` m ON m.`id` = l.`uid` WHERE l.`state` = 1 AND l.`id` = $hid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$data = $ret[0];
			$title = $data['title'];
			$litpic  = $data['litpic'] ? getFilePath($data['litpic']) : '';
			$contact = $data['contact'];
			$began = $data['began'];
			$addrid = $data['addrid'];
			$address = $data['address'];
			$nickname = $data['company'] ? $data['company'] : $data['nickname'];

			//区域
			global $data;
			$data = "";
			$addrArr = getParentArr("site_area", $addrid);
			$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
			$address = join(" ", $addrArr) . ' ' . $address;

			return array(
				'date' => $date,
				'property' => $property,
				'state' => $state,
				'code' => join(" ", str_split($code, 4)),
				'nickname' => $nickname,
				'price' => $price,
				'title' => $title,
				'litpic' => $litpic,
				'began' => $began,
				'address' => $address,
				'url' => getUrlPath(array("service" => "huodong", "template" => "detail", "id" => $hid)),
				'contact' => $contact
			);

		}else{
			return array("state" => 200, "info" => '活动不存在，操作失败！');
		}

	}


	/**
		* 取消报名
		* @return array
		*/
	public function cancelJoin(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$id = (int)$param['id'];
		$time = GetMkTime(time());

		if(empty($id)) return array("state" => 200, "info" => '活动参数传递错误，操作失败！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		//验证是否已经报名
		$hid = 0;
		$fid = 0;
		$sql = $dsql->SetQuery("SELECT `id`, `hid`, `fid` FROM `#@__huodong_reg` WHERE `state` = 1 AND `id` = $id AND `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$hid = $ret[0]['hid'];
			$fid = $ret[0]['fid'];
		}else{
			$sql = $dsql->SetQuery("SELECT `id`, `hid`, `fid` FROM `#@__huodong_reg` WHERE `state` = 1 AND `hid` = $id AND `uid` = $uid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$id  = $ret[0]['id'];
				$hid = $ret[0]['hid'];
				$fid = $ret[0]['fid'];
			}else{
				return array("state" => 200, "info" => '您还没有报名，或者已经取消！');
			}
		}

		//查询活动ID
		$sql = $dsql->SetQuery("SELECT `title`, `end`, `feetype` FROM `#@__huodong_list` WHERE `state` = 1 AND `id` = $hid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$data = $ret[0];
			$huodong = $data['title'];
			$end  = $data['end'];

			// if($time > $end) return array("state" => 200, "info" => '活动已经结束，无法取消！');

			//收费类型，退回费用
			if($data['feetype']){

				$sql = $dsql->SetQuery("SELECT `title`, `price` FROM `#@__huodong_fee` WHERE `hid` = $hid AND `id` = $fid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){

					$fee = $ret[0];
					$amount = $fee['price'];

					//退还费用
					if($amount > 0){
						$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$amount' WHERE `id` = '$uid'");
						$dsql->dsqlOper($archives, "update");

						//保存操作日志
						$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '1', '$amount', '活动退款：$huodong', '$time')");
						$dsql->dsqlOper($archives, "update");

						$sql = $dsql->SetQuery("UPDATE `#@__huodong_reg` SET `state` = 4 WHERE `id` = $id AND `fid` = $fid AND `uid` = $uid");
					}else{
						$sql = $dsql->SetQuery("UPDATE `#@__huodong_reg` SET `state` = 3 WHERE `id` = $id AND `fid` = $fid AND `uid` = $uid");
					}

					//更新报名状态为已退款
					$ret = $dsql->dsqlOper($sql, "update");
					if($ret == "ok"){
						return "取消成功！";
					}else{
						return array("state" => 200, "info" => '程序错误，操作失败！');
					}

					//删除报名记录
					// $sql = $dsql->SetQuery("DELETE FROM `#@__huodong_reg` WHERE `hid` = $id AND `fid` = $fid AND `uid` = $uid");
					// $ret = $dsql->dsqlOper($sql, "update");
					// if($ret == "ok"){
					// 	return "取消成功！";
					// }else{
					// 	return array("state" => 200, "info" => '程序错误，操作失败！');
					// }

				}else{
					return array("state" => 200, "info" => '取消失败，收费项不存在，请联系主办方退款！');
				}

			//免费类型的删除报名记录
			}else{

				//更新报名状态为已取消
				$sql = $dsql->SetQuery("UPDATE `#@__huodong_reg` SET `state` = 3 WHERE (`id` = $id OR `hid` = $id) AND `fid` = $fid AND `uid` = $uid");
				$ret = $dsql->dsqlOper($sql, "update");
				if($ret == "ok"){
					return "取消成功！";
				}else{
					return array("state" => 200, "info" => '程序错误，操作失败！');
				}

				// $sql = $dsql->SetQuery("DELETE FROM `#@__huodong_reg` WHERE `hid` = $id AND `uid` = $uid");
				// $ret = $dsql->dsqlOper($sql, "update");
				// if($ret == "ok"){
				// 	return "取消成功！";
				// }else{
				// 	return array("state" => 200, "info" => '程序错误，操作失败！');
				// }
			}

		}else{
			return array("state" => 200, "info" => '活动不存在，操作失败！');
		}

	}


	/**
		* 完成报名
		* @return array
		*/
	public function compleateJoin(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$id = (int)$param['id'];
		$time = GetMkTime(time());

		if(empty($id)) return array("state" => 200, "info" => '活动参数传递错误，操作失败！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');

		//验证是否已经报名
		$hid = 0;
		$fid = 0;
		$sql = $dsql->SetQuery("SELECT `id`, `hid`, `fid` FROM `#@__huodong_reg` WHERE `state` = 1 AND `id` = $id AND `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$hid = $ret[0]['hid'];
			$fid = $ret[0]['fid'];
		}else{
			return array("state" => 200, "info" => '您还没有报名，或者已经取消！');
		}

		//查询活动ID
		$sid = 0;
		$sql = $dsql->SetQuery("SELECT `title`, `end`, `feetype`, `uid` FROM `#@__huodong_list` WHERE `state` = 1 AND `id` = $hid");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$data = $ret[0];
			$huodong = $data['title'];
			$end  = $data['end'];
			$sid  = $data['uid'];

			//收费类型，结算费用
			if($data['feetype']){

				$sql = $dsql->SetQuery("SELECT `title`, `price` FROM `#@__huodong_fee` WHERE `hid` = $hid AND `id` = $fid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){

					$fee = $ret[0];
					$amount = $fee['price'];

					//扣除佣金
					global $cfg_huodongFee;
					$cfg_huodongFee = (float)$cfg_huodongFee;

					$fee = $amount * $cfg_huodongFee / 100;
					$fee = $fee < 0.01 ? 0 : $fee;
					$amount_ = sprintf('%.2f', $amount - $fee);

					//费用转给商家
					if($amount > 0){
						$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$amount_' WHERE `id` = '$sid'");
						$dsql->dsqlOper($archives, "update");

						//保存操作日志
						$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$sid', '1', '$amount', '活动收入：$huodong', '$time')");
						$dsql->dsqlOper($archives, "update");
					}

					//更新报名状态为已完成
					$sql = $dsql->SetQuery("UPDATE `#@__huodong_reg` SET `state` = 2 WHERE `id` = $id AND `fid` = $fid AND `uid` = $uid");
					$ret = $dsql->dsqlOper($sql, "update");
					if($ret == "ok"){
						return "操作成功！";
					}else{
						return array("state" => 200, "info" => '程序错误，操作失败！');
					}

				}else{
					return array("state" => 200, "info" => '取消失败，收费项不存在，请联系主办方退款！');
				}

			//免费类型的
			}else{

				//更新报名状态为已完成
				$sql = $dsql->SetQuery("UPDATE `#@__huodong_reg` SET `state` = 2 WHERE `id` = $id AND `fid` = $fid AND `uid` = $uid");
				$ret = $dsql->dsqlOper($sql, "update");
				if($ret == "ok"){
					return "操作成功！";
				}else{
					return array("state" => 200, "info" => '程序错误，操作失败！');
				}

			}

		}else{
			return array("state" => 200, "info" => '活动不存在，操作失败！');
		}

	}



	/**
		* 发布活动
		* @return array
		*/
	public function fabu(){
		global $dsql;
		global $userLogin;

		$param = $this->param;

		$typeid   = $param['typeid'];
		$title    = filterSensitiveWords(addslashes($param['title']));
		$litpic   = $param['litpic'];
		$began    = GetMkTime($param['began']);
		$end      = GetMkTime($param['end']);
		$baoming  = (int)$param['baoming'];
		$baomingend = (int)GetMkTime($param['baomingend']);
		$addrid   = (int)$param['addrid'];
		$cityid   = (int)$param['cityid'];
		$address  = filterSensitiveWords($param['address']);
		$body     = filterSensitiveWords($param['body'], false);
		$fee      = (int)$param['fee'];
		$max      = (int)$param['max'];
		$fee_title = $param['fee_title'];
		$fee_price = $param['fee_price'];
		$fee_max   = $param['fee_max'];
		$contact   = filterSensitiveWords(addslashes($param['contact']));
		$property  = json_decode($param['property'], true);

		if(empty($cityid)){
			$cityInfoArr = getPublicParentInfo(array('tab' => 'site_area', 'id' => $addrid));
			$cityInfoArr = explode(',', $cityInfoArr);
			$cityid = $cityInfoArr[0];
		}

		global $dellink, $autolitpic;
		include HUONIAOINC."/config/huodong.inc.php";
		$arcrank = (int)$customFabuCheck;

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		//用户信息
		$userinfo = $userLogin->getMemberInfo();

		// 需要支付费用
		$amount = 0;

		// 是否独立支付 普通会员或者付费会员超出限制
		$alonepay = 0;

		$alreadyFabu = 0; // 付费会员当天已免费发布数量

		//企业会员或已经升级为收费会员的状态才可以发布 --> 普通会员也可发布
		if($userinfo['userType'] == 1){

			$toMax = false;

			if($userinfo['level']){

				$memberLevelAuth = getMemberLevelAuth($userinfo['level']);
				$huodongCount = (int)$memberLevelAuth['huodong'];

				//统计用户当天已发布数量 @
				$today = GetMkTime(date("Y-m-d", time()));
				$tomorrow = GetMkTime(date("Y-m-d", strtotime("+1 day")));
				$sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__huodong_list` WHERE `uid` = $uid AND `pubdate` >= $today AND `pubdate` < $tomorrow AND `alonepay` = 0 AND `waitpay` = 0");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$alreadyFabu = $ret[0]['total'];
					if($alreadyFabu >= $huodongCount){
						$toMax = true;
						// return array("state" => 200, "info" => '当天发布信息数量已达等级上限！');
					}else{
						$arcrank = 1;
					}
				}
			}

			// 普通会员或者付费会员当天发布数量达上限
			if($userinfo['level'] == 0 || $toMax){

				$alonepay = 1;

				global $cfg_fabuAmount;
				$fabuAmount = $cfg_fabuAmount ? unserialize($cfg_fabuAmount) : array();

				if($fabuAmount){
					$amount = $fabuAmount["huodong"];
				}else{
					$amount = 0;
				}

			}

		}

		// if($userinfo['userType'] == 2){
		// 	if(!verifyModuleAuth(array("module" => "huodong"))){
		// 		return array("state" => 200, "info" => '商家权限验证失败！');
		// 	}
		// }

		if(empty($typeid)) return array("state" => 200, "info" => '请选择活动类型');
		if(empty($title)) return array("state" => 200, "info" => '请输入活动主题');
		if(empty($litpic)) return array("state" => 200, "info" => '请添加活动海报');
		if(empty($began)) return array("state" => 200, "info" => '请选择活动开始时间');
		if(empty($end)) return array("state" => 200, "info" => '请选择活动结束');
		if(empty($baomingend) && $baomingend) return array("state" => 200, "info" => '请选择报名截止时间');
		if(empty($addrid)) return array("state" => 200, "info" => '请选择活动区域');
		if(empty($address)) return array("state" => 200, "info" => '请输入活动详细地址');

		$feeArr = array();
		if($fee){

			if(empty($fee_title)) return array("state" => 200, "info" => '请填写电子票内容');

			//验证费用内容
			foreach ($fee_title as $key => $value) {
				$fee_tit = filterSensitiveWords($value);
				$fee_pri = (float)$fee_price[$key];
				$fee_cou = (int)$fee_max[$key];

				if(!empty($fee_tit)){
					array_push($feeArr, array(
						"title" => $fee_tit,
						"price" => $fee_pri,
						"max"   => $fee_cou
					));
				}
			}

			if(empty($feeArr)) return array("state" => 200, "info" => '请填写电子票内容');
		}else{
			if(empty($max)) return array("state" => 200, "info" => '请输入人数上限');
		}

		if(!is_array($property)) return array("state" => 200, "info" => '报名填写信息格式有误');

		if(empty($contact)) return array("state" => 200, "info" => '请输入主办方联系方式');

		$title   = cn_substrR($title, 100);
		$address = cn_substrR($address, 200);
		$contact = cn_substrR($contact, 200);
		$now     = GetMkTime(time());
		$ip      = GetIP();

		$property = serialize($property);

		//保存到主表
		$waitpay = $amount > 0 ? 1 : 0;
		$archives = $dsql->SetQuery("INSERT INTO `#@__huodong_list` (`cityid`, `uid`, `typeid`, `title`, `litpic`, `began`, `end`, `baoming`, `baomingend`, `addrid`, `address`, `body`, `feetype`, `max`, `contact`, `pubdate`, `ip`, `state`, `waitpay`, `alonepay`, `property`) VALUES ('$cityid', '$uid', '$typeid', '$title', '$litpic', '$began', '$end', '$baoming', '$baomingend', '$addrid', '$address', '$body', '$fee', '$max', '$contact', '$now', '$ip', '$arcrank', '$waitpay', '$alonepay', '$property')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){

			//保存费用
			if($fee && $feeArr){
				foreach($feeArr as $k => $v){
					$tit = $v['title'];
					$pri = $v['price'];
					$max = $v['max'];

					$price = $dsql->SetQuery("INSERT INTO `#@__huodong_fee` (`hid`, `title`, `price`, `max`) VALUES ('$aid', '$tit', '$pri', '$max')");
					$dsql->dsqlOper($price, "update");
				}
			}

			//后台消息通知
			if(!$arcrank){
				updateAdminNotice("huodong", "detail");
				// return "发布成功，请等待管理员审核！";
			}else{
				// $param = array(
				// 	"service"  => "huodong",
				// 	"template" => "detail",
				// 	"id"       => $aid
				// );
				// return getUrlPath($param);
			}

			if($userinfo['level']){
				$auth = array("level" => $userinfo['level'], "levelname" => $userinfo['levelName'], "alreadycount" => $alreadyFabu, "maxcount" => $huodongCount);
			}else{
				$auth = array("level" => 0, "levelname" => "普通会员", "maxcount" => 0);
			}
			return array("auth" => $auth, "aid" => $aid, "amount" => $amount);


		}else{

			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');

		}

	}



	/**
		* 修改活动
		* @return array
		*/
	public function edit(){
		global $dsql;
		global $userLogin;

		$param = $this->param;

		$id       = $param['id'];
		$typeid   = $param['typeid'];
		$title    = filterSensitiveWords(addslashes($param['title']));
		$litpic   = $param['litpic'];
		$began    = GetMkTime($param['began']);
		$end      = GetMkTime($param['end']);
		$baoming  = (int)$param['baoming'];
		$baomingend = (int)GetMkTime($param['baomingend']);
		$addrid   = (int)$param['addrid'];
		$cityid   = (int)$param['cityid'];
		$address  = filterSensitiveWords($param['address']);
		$body     = filterSensitiveWords($param['body'], false);
		$fee      = (int)$param['fee'];
		$max      = (int)$param['max'];
		$fee_title = $param['fee_title'];
		$fee_price = $param['fee_price'];
		$fee_max   = $param['fee_max'];
		$contact   = filterSensitiveWords(addslashes($param['contact']));
		$property  = json_decode($param['property'], true);

		global $dellink, $autolitpic;
		include HUONIAOINC."/config/huodong.inc.php";
		$state = (int)$customFabuCheck;

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		//查询活动ID
		$reg = 0;
		$sql = $dsql->SetQuery("SELECT l.`uid`, (SELECT count(`id`) FROM `#@__huodong_reg` r WHERE r.`hid` = $id) reg FROM `#@__huodong_list` l WHERE l.`id` = $id");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){

			$data = $ret[0];

			if($data['uid'] != $uid) return array("state" => 200, "info" => '会员权限验证错误，修改失败！');
			$reg = $data['reg'];

		}else{
			return array("state" => 200, "info" => '活动不存在或已经删除，修改失败！');
		}

		if(empty($id)) return array("state" => 200, "info" => '活动信息传递错误，修改失败');
		if(empty($typeid)) return array("state" => 200, "info" => '请选择活动类型');
		if(empty($title)) return array("state" => 200, "info" => '请输入活动主题');
		if(empty($litpic)) return array("state" => 200, "info" => '请添加活动海报');
		if(empty($began)) return array("state" => 200, "info" => '请选择活动开始时间');
		if(empty($end)) return array("state" => 200, "info" => '请选择活动结束');
		if(empty($baomingend) && $baomingend) return array("state" => 200, "info" => '请选择报名截止时间');
		if(empty($addrid)) return array("state" => 200, "info" => '请选择活动区域');
		if(empty($address)) return array("state" => 200, "info" => '请输入活动详细地址');


		//只有没报过名的活动才可以修改
		if($reg == 0){
			$feeArr = array();
			if($fee){

				if(empty($fee_title)) return array("state" => 200, "info" => '请填写电子票内容');

				//验证费用内容
				foreach ($fee_title as $key => $value) {
					$fee_tit = filterSensitiveWords($value);
					$fee_pri = (float)$fee_price[$key];
					$fee_cou = (int)$fee_max[$key];

					if(!empty($fee_tit)){
						array_push($feeArr, array(
							"title" => $fee_tit,
							"price" => $fee_pri,
							"max"   => $fee_cou
						));
					}
				}

				if(empty($feeArr)) return array("state" => 200, "info" => '请填写电子票内容');
			}else{
				if(empty($max)) return array("state" => 200, "info" => '请输入人数上限');
			}
		}

		if(!is_array($property)) return array("state" => 200, "info" => '报名填写信息格式有误');

		if(empty($contact)) return array("state" => 200, "info" => '请输入主办方联系方式');

		$title   = cn_substrR($title, 100);
		$address = cn_substrR($address, 200);
		$contact = cn_substrR($contact, 200);
		$now     = GetMkTime(time());
		$ip      = GetIP();

		$property = serialize($property);

		//保存到主表
		if($reg > 0){
			$sql = $dsql->SetQuery("UPDATE `#@__huodong_list` SET `cityid` = '$cityid', `typeid` = '$typeid', `title` = '$title', `litpic` = '$litpic', `began` = '$began', `end` = '$end', `baoming` = '$baoming', `baomingend` = '$baomingend', `addrid` = '$addrid', `address` = '$address', `body` = '$body', `contact` = '$contact', `state` = '$state', `property` = '$property' WHERE `id` = $id");
		}else{
			$sql = $dsql->SetQuery("UPDATE `#@__huodong_list` SET `cityid` = '$cityid', `typeid` = '$typeid', `title` = '$title', `litpic` = '$litpic', `began` = '$began', `end` = '$end', `baoming` = '$baoming', `baomingend` = '$baomingend', `addrid` = '$addrid', `address` = '$address', `body` = '$body', `feetype` = '$fee', `max` = '$max', `contact` = '$contact', `state` = '$state', `property` = '$property' WHERE `id` = $id");
		}
		$ret = $dsql->dsqlOper($sql, "update");

		if($ret == "ok"){

			//保存费用
			if($fee && $feeArr && $reg == 0){

				//先删除现有收费项
				$sql = $dsql->SetQuery("DELETE FROM `#@__huodong_fee` WHERE `hid` = $id");
				$dsql->dsqlOper($sql, "update");

				foreach($feeArr as $k => $v){
					$tit = $v['title'];
					$pri = $v['price'];
					$max = $v['max'];

					$price = $dsql->SetQuery("INSERT INTO `#@__huodong_fee` (`hid`, `title`, `price`, `max`) VALUES ('$id', '$tit', '$pri', '$max')");
					$dsql->dsqlOper($price, "update");
				}
			}

			//后台消息通知
			if(!$state){
				updateAdminNotice("huodong", "detail");
				return "修改成功，请等待管理员审核！";
			}else{
				return "修改成功";
			}

		}else{

			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');

		}

	}


	/**
		* 删除活动
		* @return array
		*/
	public function del(){
		global $dsql;
		global $userLogin;

		$id = $this->param['id'];

		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$archives = $dsql->SetQuery("SELECT l.*, (SELECT count(`id`) FROM `#@__huodong_reg` r WHERE r.`hid` = $id) reg FROM `#@__huodong_list` l WHERE l.`id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['uid'] == $uid){

				//已经有报名的不可以删除
				if($results['reg'] > 0){
					return array("state" => 200, "info" => '已经有会员报名，不可以删除！');
				}else{

					//删除评论
					$archives = $dsql->SetQuery("DELETE FROM `#@__public_comment` WHERE `type` = 'huodong-detail' AND `aid` = ".$id);
					$dsql->dsqlOper($archives, "update");

					//删除缩略图
					delPicFile($results['litpic'], "delThumb", "huodong");

					if(!empty($results['body'])){
						delEditorPic($results['body'], "info");
					}

					$archives = $dsql->SetQuery("DELETE FROM `#@__huodong_list` WHERE `id` = ".$id);
					$dsql->dsqlOper($archives, "update");
					return array("state" => 100, "info" => '删除成功！');
				}

			}else{
				return array("state" => 101, "info" => '权限不足，请确认帐户信息后再进行操作！');
			}
		}else{
			return array("state" => 101, "info" => '活动不存在，或已经删除！');
		}

	}


	/**
	 * 报名记录
	 * @param $hid int 活动ID
	 * @return array
	 */
	function regList(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$hid   = $param['hid']; //活动ID
		$state = $param['state']; //状态

		if($state){
			$where = " AND r.`state` = $state";
		}else{
			$where = " AND (r.`state` = 1 || r.`state` = 2)";
		}

		$uid = $userLogin->getMemberID();
		$archives = $dsql->SetQuery("SELECT m.`mtype`, m.`nickname`, m.`company`, m.`photo`, m.`phone`, r.`uid`, r.`date`, r.`fid`, r.`property`, r.`state`, f.`title`, f.`price`, l.`uid` userid FROM `#@__huodong_reg` r LEFT JOIN `#@__member` m ON m.`id` = r.`uid` LEFT JOIN `#@__huodong_fee` f ON f.`id` = r.`fid` LEFT JOIN `#@__huodong_list` l ON l.`id` = $hid WHERE r.`hid` = ".$hid);

		//总条数
		$totalCount = $dsql->dsqlOper($archives.$where, "totalCount");

		$unchecked = $dsql->dsqlOper($archives . " AND r.`state` = 1", "totalCount");
		$checked = $dsql->dsqlOper($archives . " AND r.`state` = 2", "totalCount");

		$list = array();
		if($totalCount > 0){
			$results = $dsql->dsqlOper($archives.$where." ORDER BY r.`id` DESC", "results");
			if($results){
				foreach($results as $key => $val){
					$list[$key]['uid']      = $val['uid'];
					$list[$key]['nickname'] = $val['mtype'] && !empty($val['company']) ? $val['company'] : $val['nickname'];
					$list[$key]['photo']    = !empty($val['photo']) ? getFilePath($val['photo']) : "";
					$list[$key]['date']     = $val['date'];
					$list[$key]['property'] = $val['property'] ? unserialize($val['property']) : array();
					$list[$key]['state']    = $val['state'];

					//如果是发布者请求，列出报名的详细信息
					if($uid == $val['userid']){
						$list[$key]['phone'] = $val['phone'];
						$list[$key]['title'] = $val['title'];
						$list[$key]['price'] = $val['price'];
						$list[$key]['date']  = $val['date'];
					}
				}
			}
		}
		return array("pageInfo" => array("totalCount" => $totalCount, "unchecked" => $unchecked, "checked" => $checked), "list" => $list);
	}


	/**
	 * 参与记录
	 * @return array
	 */
	function joinList(){
		global $dsql;
		global $userLogin;

		$uid      = $userLogin->getMemberID();
		$page     = (int)$this->param['page'];
		$pageSize = (int)$this->param['pageSize'];
		$state    = (int)$this->param['state'];
		$now      = GetMkTime(time());

		if($uid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		$where = "";
		if($state){
			$where = " AND r.`state` = $state";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$atpage = $pageSize*($page-1);
		$limit = " LIMIT $atpage, $pageSize";

		$archives = $dsql->SetQuery("SELECT r.`id`, r.`hid`, r.`date`, r.`state`, l.`title`, l.`litpic`, l.`began`, l.`end`, l.`addrid`, l.`address` FROM `#@__huodong_reg` r LEFT JOIN `#@__huodong_list` l ON l.`id` = r.`hid` WHERE r.`uid` = ".$uid." AND l.`waitpay` = 0");
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
		//待参与
		$involved = $dsql->dsqlOper($archives." AND r.`state` = 1", "totalCount");
		//已完成
		$success = $dsql->dsqlOper($archives." AND r.`state` = 2", "totalCount");
		//已取消
		$cancel = $dsql->dsqlOper($archives." AND r.`state` = 3", "totalCount");
		//已退款
		$refund = $dsql->dsqlOper($archives." AND r.`state` = 4", "totalCount");

		//总分页数
		$totalPage = ceil((int)$totalCount/(int)$pageSize);

		$archives .= " AND l.`state` = 1".$where." ORDER BY r.`id` DESC".$limit;

		$list = array();
		if($totalCount > 0){
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach($results as $key => $val){
					$list[$key]['id']     = $val['id'];
					$list[$key]['title']  = $val['title'];
					$list[$key]['litpic'] = getFilePath($val['litpic']);
					$list[$key]['date']   = $val['date'];
					$list[$key]['began']  = $val['began'];
					$list[$key]['end']  = $val['end'];

					//区域
					global $data;
					$data = "";
					$addrArr = getParentArr("site_area", $val['addrid']);
					$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
					$list[$key]['addrname']= $addrArr;

					$list[$key]['address'] = $val['address'];
					$list[$key]['state'] = $val['state'];

					$param = array(
						"service"  => "huodong",
						"template" => "detail",
						"id"       => $val['hid']
					);
					$list[$key]['url'] = getUrlPath($param);

					$list[$key]['going'] = $val['end'] > $now ? 1 : 0;
				}
			}
		}
		return array("pageInfo" => array("totalCount" => $totalCount, "involved" => $involved, "success" => $success, "cancel" => $cancel, "refund" => $refund, "totalPage" => $totalPage), "list" => $list);
	}


	/**
	 * 验票签到
	 */
	public function verifyCode(){
		global $dsql;
		global $userLogin;

		$codes = $this->param['codes'];
		$hid   = $this->param['hid'];
		$now   = GetMkTime(time());
		$uid   = $userLogin->getMemberID();

		$userinfo = $userLogin->getMemberInfo();
		if($userinfo['userType'] == 2 && !verifyModuleAuth(array("module" => "huodong"))){
			return array("state" => 200, "info" => '商家权限验证失败！');
		}

		if(empty($hid)) return array("state" => 200, "info" => '活动ID传输错误！！');
		if(empty($codes)) return array("state" => 200, "info" => '请输入电子票号！');

		//查询当前会员是否是活动发布者
		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__huodong_list` WHERE `id` = $hid AND `uid` = $uid");
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret) return array("state" => 200, "info" => '商家权限验证失败！');

		$codeArr = explode(",", $codes);
		$success = 0;
		foreach ($codeArr as $key => $value) {

			//查询电子票
			$sql = $dsql->SetQuery("SELECT r.`id`, f.`price` FROM `#@__huodong_reg` r LEFT JOIN `#@__huodong_fee` f ON f.`id` = r.`fid` WHERE r.`hid` = $hid AND r.`code` = '$value' AND r.`state` = 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$id = $ret[0]['id'];
				$price = $ret[0]['price'];

				//更新电子票状态
				$sql = $dsql->SetQuery("UPDATE `#@__huodong_reg` SET `state` = 2 WHERE `id` = $id");
				$dsql->dsqlOper($sql, "update");

				//扣除佣金
				global $cfg_huodongFee;
				$cfg_huodongFee = (float)$cfg_huodongFee;

				$fee = $price * $cfg_huodongFee / 100;
				$fee = $fee < 0.01 ? 0 : $fee;
				$price_ = sprintf('%.2f', $price - $fee);

				if($price_ > 0){
					//将费用转至商家帐户
					$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$price_' WHERE `id` = '$uid'");
					$dsql->dsqlOper($archives, "update");

					//保存操作日志
					$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '1', '$price_', '活动签到成功：$value', '$now')");
					$dsql->dsqlOper($archives, "update");
				}

				$success++;
			}

		}

		if($success > 0){
			return "签到成功！";
		}else{
			return array("state" => 200, "info" => '签到失败，请检查您输入的电子票号！');
		}

	}


}
