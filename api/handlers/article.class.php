<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 新闻模块API接口
 *
 * @version        $Id: article.class.php 2014-3-22 下午13:36:15 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class article {
	private $param;  //参数
    public static $version = 201906;

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

		require(HUONIAOINC."/config/article.inc.php");

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

		$hotline = $hotline_config == 0 ? $cfg_hotline : $customHotline;

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
		$customChannelDomain = getDomainFullUrl('article', $customSubDomain);

        //分站自定义配置
        $ser = 'article';
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
				}elseif($param == "hotline"){
					$return['hotline'] = $hotline;
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
				}
			}

		}else{

			//自定义LOGO
			if($customLogo == 1){
				$customLogoPath = getFilePath($customLogoUrl);
			}else{
				$customLogoPath = getFilePath($cfg_weblogo);
			}

			$return['channelName']   = str_replace('$city', $cityName, $customChannelName);
			$return['logoUrl']       = $customLogoPath;
			$return['subDomain']     = $customSubDomain;
			$return['channelDomain'] = $customChannelDomain;
			$return['channelSwitch'] = $customChannelSwitch;
			$return['closeCause']    = $customCloseCause;
			$return['title']         = str_replace('$city', $cityName, $customSeoTitle);
			$return['keywords']      = str_replace('$city', $cityName, $customSeoKeyword);
			$return['description']   = str_replace('$city', $cityName, $customSeoDescription);
			$return['hotline']       = $hotline;
			$return['submission']    = $submission;
			$return['atlasMax']      = $customAtlasMax;
			$return['template']      = $customTemplate;
			$return['touchTemplate'] = $customTouchTemplate;
			$return['softSize']      = $custom_softSize;
			$return['softType']      = $custom_softType;
			$return['thumbSize']     = $custom_thumbSize;
			$return['thumbType']     = $custom_thumbType;
			$return['atlasSize']     = $custom_atlasSize;
			$return['atlasType']     = $custom_atlasType;
			$return['listRule']      = $custom_listRule;
			$return['detailRule']    = $custom_detailRule;
            // $return['selfmediaSwitch']    = (int)$custom_selfmediaSwitch;
            // $return['selfmediaJoinSwitch']    = (int)$custom_selfmediaJoinSwitch;
            // $selfmediaField = $custom_selfmediaField;
            // $arr = array();
            // if($selfmediaField){
            //     $selfmediaField = explode("\n", $selfmediaField);
            //     foreach ($selfmediaField as $key => $value) {
            //         $d = explode(":", $value);
            //         $arr[] = array('id' => $d[0], 'typename' => trim($d[1]));
            //     }
            // }
            // $return['selfmediaField'] = $arr;
            // $selfmediaLicense = $custom_selfmediaLicense;
            // $arr = array();
            // if($selfmediaLicense){
            //     $selfmediaLicense = explode("\n", $selfmediaLicense);
            //     foreach ($selfmediaLicense as $key => $value) {
            //         $d = explode(":", $value);
            //         $arr[] = array('id' => $d[0], 'typename' => $d[1]);
            //     }
            // }
            // $return['selfmediaLicense'] = $arr;
            // $return['selfmediaEditLimit'] = $custom_selfmediaEditLimit ? unserialize($custom_selfmediaEditLimit) : array();
            $return['selfmediaGrantImg']    = getFilePath($custom_selfmediaGrantImg);
            $return['selfmediaGrantTpl']    = getFilePath($custom_selfmediaGrantTpl);
            $return['selfmediaAgreement']    = $custom_selfmediaAgreement ? stripslashes($custom_selfmediaAgreement) : '';

		}

		return $return;

	}


	/**
     * 新闻分类
     * @return array
     */
	public function type(){
		global $dsql;
		$mold = $type = $page = $pageSize = 0;

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
                $mold     = (int)$this->param['mold'];
				$type     = (int)$this->param['type'];
				$page     = (int)$this->param['page'];
				$pageSize = (int)$this->param['pageSize'];
				$son      = $this->param['son'] == 0 ? false : true;
			}
		}
        $cond = " AND `mold` = $mold";
		$results = $dsql->getTypeList($type, "articletype", $son, $page, $pageSize, $cond);
		if($results){
			return $results;
		}
	}

    /**
     * 城市分类
     * @return array
     */
    public function city(){
        $userLogin = new userLogin($dbo);
        $adminCityArr = $userLogin->getAdminCity();
        $results = empty($adminCityArr) ? array() : $adminCityArr;
        if($results){
            return $results;
        }
    }


	/**
     * 新闻分类详细信息
     * @return array
     */
	public function typeDetail(){
		global $dsql;
		$typeDetail = array();
		$typeid = $this->param;
		$archives = $dsql->SetQuery("SELECT `id`, `typename`, `seotitle`, `keywords`, `description`, `parentid` FROM `#@__articletype` WHERE `id` = ".$typeid);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results && is_array($results)){
            if($results[0]['parentid']){
                global $data;
                $data = "";
                $typeArr = getParentArr("articletype", $results[0]['parentid']);
                $typeArr = array_reverse(parent_foreach($typeArr, "id"));
                $topTypeid = $typeArr[0];
            }else{
                $topTypeid = $typeid;
            }
            $results[0]['topTypeid'] = $topTypeid;
			$param = array(
				"service"     => "article",
				"template"    => "list",
				"typeid"      => $typeid,
			);
			$results[0]["url"] = getUrlPath($param);
			return $results;
		}
	}


	/**
     * 新闻列表
     * @return array
     */
	public function alist(){
		global $dsql;
		global $userLogin;
        global $HN_memory;
        global $_G;
		$pageinfo = $list = array();
		$typeid = $notid = $title = $flag = $thumb = $orderby = $u = $uid = $state = $group_img = $page = $pageSize = $where = $where1 = "";

        if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
                $mold     = $this->param['mold'];
				$typeid   = $this->param['typeid'];
				$notid    = $this->param['notid'];
				$title    = $this->param['title'];
				$flag     = $this->param['flag'];
                $thumb    = $this->param['thumb'];
                $litpic   = $this->param['litpic'];
				$orderby  = $this->param['orderby'];
				$u        = $this->param['u'];
				$uid      = $this->param['uid'];
				$state    = $this->param['state'];
				$group_img = $this->param['group_img'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
                $isAjax   = $this->param['isAjax'];
                $media    = $this->param['media'];
                $get_zan  = $this->param['get_zan'];
                $zhuanti  = $this->param['zhuanti'];
                $buMonth  = $this->param['buMonth'];
                $media_arctype  = (int)$this->param['media_arctype'];
			}
		}
		$cityid = getCityId($this->param['cityid']);
		if($cityid && $u != 1 && !$uid){
			$where .= " AND lt.`cityid` = ".$cityid;
		}else{
				$where .= " AND lt.`cityid` !=0 ";
		}
		//是否输出当前登录会员的信息
		if($u != 1){
			$where .= " AND lt.`arcrank` = 1 AND lt.`waitpay` = 0 AND lt.`media_state` = 1";
			//取指定会员的信息
			if($uid){
				$where .= " AND lt.`admin` = $uid";
			}
		}else{
			$uid = $userLogin->getMemberID();
			$where .= " AND lt.`admin` = ".$uid." AND lt.`cityid` != 0";
			if($state != ""){
				$where1 = " AND lt.`arcrank` = ".$state;
			}
		}
        if($media){
            if($media == 'is'){
                $where .= " AND lt.`media` <> 0";
            }else{
                $where .= " AND lt.`media` = $media";
            }
        }
        if($zhuanti){
            $zhuanti = (int)$zhuanti;
            if($zhuanti){
                $arr = $dsql->getTypeList($zhuanti, "article_zhuanti");
                if($arr){
                    global $arr_data;
                    $arr_data = array();
                    $lower = arr_foreach($arr);
                    $lower = $zhuanti.",".join(',',$lower);
                }else{
                    $lower = $zhuanti;
                }
                $where .= " AND lt.`zhuanti` in ($lower)";
            }else{
                $where .= " AND lt.`zhuanti` > 0";
            }
        }
        if($mold != ''){
            if(strstr($mold, ",")){
                $mold_ = explode(",", $mold);
                $where2 = array();
                foreach ($mold_ as $k => $v) {
                    $where2[$k] = "lt.`mold` = $v";
                }
                $where .= " AND (" . join(" || ", $where2) . ")";
            }else{
                $where .= " AND lt.`mold` = $mold";
            }
        }
        if($media_arctype){
            $where .= " AND lt.`media_arctype` = $media_arctype";
        }

        //不能包含哪些新闻
        if(!empty($notid)){
            $where .= " AND lt.`id` not in ($notid)";
        }

		//模糊查询关键字
		if(!empty($title)){
			//搜索记录
			if(!empty($_POST['keywords']) || !empty($_GET['keywords']) || !empty($_POST['title']) || !empty($_GET['title'])){
				siteSearchLog("article", $title);
			}
			$title = explode(" ", $title);
            $w = array();
			foreach ($title as $k => $v) {
				if(!empty($v)){
					$w[] = "`title` like '%".$v."%' OR `keywords` like '%".$v."%'";
				}
			}
            $where .= " AND (".join(" OR ", $w).")";
        }

		//匹配自定义属性
		if(!empty($flag)){
			$flag = explode(",", $flag);
            $flags_ = $flag;
            $flag_h = in_array('h', $flags_) ? 'h' : '';
            $flag_r = in_array('r', $flags_) ? 'r' : '';
            $flag_b = in_array('b', $flags_) ? 'b' : '';
            $flag_t = in_array('t', $flags_) ? 't' : '';
            $flag_p = in_array('p', $flags_) ? 'p' : '';
            $flag_where = '';
            if($flag_h){
                $flag_where .= " lt.`flag_h` = 1 AND ";
            }
            if($flag_r){
                $flag_where .= " lt.`flag_r` = 1 AND ";
            }
            if($flag_b){
                $flag_where .= " lt.`flag_b` = 1 AND ";
            }
            if($flag_t){
                $flag_where .= " lt.`flag_t` = 1 AND ";
            }
            if($flag_p){
                $flag_where .= " lt.`flag_p` = 1 AND ";
            }
            $flag_where = substr($flag_where, 0, -4);
			$where .= " AND " . $flag_where;
			// AND `flag` like '%h%' AND `flag` like '%b%'
		}
		//缩略图
        $thumb = $thumb ? $thumb : $litpic;
		if($thumb === "0"){
			$where .= " AND lt.`flag_p` = 0 ";
		}elseif($thumb === "1"){
			$where .= " AND lt.`flag_p` = 1 ";
		}
        //遍历分类
        if(!empty($typeid)){
            $typeArr = $dsql->getTypeList($typeid, "articletype");
            if($typeArr){
                global $arr_data;
                $arr_data = array();
                $lower = arr_foreach($typeArr);
                $lower = $typeid.",".join(',',$lower);
            }else{
                $lower = $typeid;
            }
            $where .= " AND lt.`typeid` in ($lower)";
        }
		$order = " ORDER BY lt.`weight` DESC, lt.`id` DESC";
		//发布时间
		if($orderby == "1"){
			$order = " ORDER BY lt.`pubdate` DESC, lt.`weight` DESC, lt.`id` DESC";
		//浏览量
		}elseif($orderby == "2"){
			$order = " ORDER BY lt.`click` DESC, lt.`weight` DESC, lt.`id` DESC";
		//今日浏览量
		}elseif($orderby == "2.1"){
			$order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = curdate() ORDER BY lt.`click` DESC, lt.`weight` DESC, lt.`id` DESC";
		//昨日浏览量
		}elseif($orderby == "2.2"){
			$order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY) ORDER BY lt.`click` DESC, lt.`weight` DESC, lt.`id` DESC";
		//本周浏览量
		}elseif($orderby == "2.3"){
			$order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') >= DATE_SUB(curdate(), INTERVAL 7 DAY) ORDER BY lt.`click` DESC, lt.`weight` DESC, lt.`id` DESC";
		//本月浏览量
		}elseif($orderby == "2.4"){
			$order = " AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m') = DATE_FORMAT(curdate(), '%Y-%m') ORDER BY lt.`click` DESC, lt.`weight` DESC, lt.`id` DESC";
		//随机
		}elseif($orderby == "3"){
			$order = " ORDER BY rand()";
		}
        $table_all = '#@__articlelist_all';
		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;
        $totalCount = 0;

        /**
         * 1.new SubTable(服务, 原始表 , where条件, 主表别名)
         * 2.getSelectSubTable($page, $pageSize) 获取要查询的分表
         * return array('tables'=>分表, 'code' => 状态, 'msg'=>code描述)
         */
        $subTableObj = new SubTable('article','#@__articlelist', $where,'l');
        // $tables_res = $subTableObj->getSelectSubTable($page, $pageSize);
        // $tables_break = $tables_res['tables'];
        // if($tables_res['code'] == 10000){
        //     return array("state" => 200, "info" => '暂无数据！');
        // }
        $archives = "";
        // $union = " UNION ALL ";
		if(strstr($orderby, "4")){
            //评论排行
            //今日评论
			if($orderby == "4.1"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(lt.`pubdate`), '%Y-%m-%d') = curdate()";
			//昨日评论
			}elseif($orderby == "4.2"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(lt.`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY)";
			//本周评论
			}elseif($orderby == "4.3"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(lt.`pubdate`), '%Y-%m-%d') >= DATE_SUB(curdate(), INTERVAL 7 DAY)";
			//本月评论
			}elseif($orderby == "4.4"){
				$where .= " AND DATE_FORMAT(FROM_UNIXTIME(lt.`pubdate`), '%Y-%m') = DATE_FORMAT(curdate(), '%Y-%m')";
			}

			$order = " ORDER BY total DESC";

            //分页计算分表
            if(!$isAjax){
                if(!strstr($where,"admin" )){

                }else{
                    //其他请求在原来的表查
                    // $sql_count1 = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__articlelist` l WHERE `del` = 0");
                    // $res = $dsql->dsqlOper($sql_count1 . $where, "results");
                    // $totalCount = $res ? $res[0]['total'] : 0;
                }

            }

            // if(empty($tables_break)){
            //     $archives .= $dsql->SetQuery("SELECT l.`id`, l.`title`, l.`admin`, l.`subtitle`, l.`flag`, l.`weight`, l.`keywords`, l.`description`, l.`redirecturl`, l.`litpic`, l.`color`, l.`click`, l.`arcrank`, l.`pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`admin`, l.`mold`, l.`zan`, l.`writer`, l.`videotime` FROM `". $subTableObj->getLastTable() ."` l WHERE l.`del` = 0".$where);
            // }else{
            //     foreach ($tables_break as $table_b){
            //         $archives .= $dsql->SetQuery("SELECT l.`id`, l.`title`, l.`admin`, l.`subtitle`, l.`flag`, l.`weight`, l.`keywords`, l.`description`, l.`redirecturl`, l.`litpic`, l.`color`, l.`click`, l.`arcrank`, l.`pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`admin`, l.`mold`, l.`zan`, l.`writer`, l.`videotime` FROM `".$table_b."` l WHERE l.`del` = 0".$where . $union);
            //     }
            //     $archives = substr($archives, 0, -(strlen($union)));
            // }

            // $archives .= $dsql->SetQuery("SELECT l.`id`, l.`title`, l.`admin`, l.`subtitle`, l.`flag`, l.`weight`, l.`keywords`, l.`description`, l.`redirecturl`, l.`litpic`, l.`color`, l.`click`, l.`arcrank`, l.`pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`admin`, l.`mold`, l.`zan`, l.`writer`, l.`videotime`, l.`videotype`, l.`videourl` FROM `". $table_all ."` l WHERE l.`del` = 0".$where);

            // 这里是要查询的字段
            $archives = "SELECT l.`id`, l.`title`, l.`admin`, l.`subtitle`, l.`flag`, l.`weight`, l.`keywords`, l.`description`, l.`redirecturl`, l.`litpic`, l.`color`, l.`click`, l.`arcrank`, l.`pubdate`, (SELECT COUNT(`id`)  FROM `#@__public_comment` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `pid` = 0 AND `type` = 'article-detail') AS total, l.`waitpay`, l.`admin`, l.`mold`, l.`zan`, l.`writer`, l.`videotime`, l.`videotype`, l.`videourl`";

		}else{

            if(!$isAjax){
                if(!strstr($where,"admin" )){
                    //只有前台新闻列表使用缓存

                }else{
                    //其他请求在原来的表查
                    // $sql_count1 = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__articlelist` l WHERE `del` = 0");
                    // $res = $dsql->dsqlOper($sql_count1 . $where, "results");
                    // $totalCount = $res ? $res[0]['total'] : 0;
                }

            }

       //      if(empty($tables_break)){
       //          //若不需要查询分表，直接从最后一张表查
       //          $archives = "SELECT `id`, `title`, `admin`, `subtitle`, `typeid`, `flag`, `weight`, `keywords`, `description`, `source`, `redirecturl`, `litpic`, `color`, `click`, l.`arcrank`, `pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`admin`, l.`mold`, l.`zan`, l.`writer`, l.`videotime` FROM `". $subTableObj->getLastTable() ."` l WHERE `del` = 0".$where;
       //      }else{
			    // foreach ($tables_break as $table_b){
       //              $archives .= "SELECT `id`, `title`, `admin`, `subtitle`, `typeid`, `flag`, `weight`, `keywords`, `description`, `source`, `redirecturl`, `litpic`, `color`, `click`, l.`arcrank`, `pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`admin`, l.`mold`, l.`zan`, l.`writer`, l.`videotime` FROM `".$table_b."` l WHERE `del` = 0".$where . $union;
       //          }
       //          $archives = substr($archives, 0, -(strlen($union)));
       //      }

            // $archives = "SELECT l.`id`, l.`title`, l.`admin`, l.`subtitle`, l.`typeid`, l.`flag`, l.`weight`, l.`keywords`, l.`description`, l.`source`, l.`redirecturl`, l.`litpic`, l.`color`, l.`click`, l.`arcrank`, l.`pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`admin`, l.`mold`, l.`zan`, l.`writer`, l.`videotime`, l.`videotype`, l.`videourl` FROM `". $table_all ."` l WHERE `del` = 0".$where;

            // 这里是要查询的数据
            $archives = "SELECT l.`id`, l.`title`, l.`admin`, l.`subtitle`, l.`typeid`, l.`flag`, l.`weight`, l.`keywords`, l.`description`, l.`source`, l.`redirecturl`, l.`litpic`, l.`color`, l.`click`, l.`arcrank`, l.`pubdate`, l.`typeset`, l.`videotype`, l.`videourl`, (SELECT COUNT(`id`)  FROM `#@__public_comment` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `pid` = 0 AND `type` = 'article-detail') AS total, l.`waitpay`, l.`admin`, l.`mold`, l.`zan`, l.`writer`, l.`videotime`, l.`videotype`, l.`videourl`";

		}
		//总分页数
        if(!$isAjax){

            $sql = $dsql->SetQuery("SELECT COUNT(*) total FROM `#@__articlelist_all` lt WHERE `del` = 0".$where);
            $totalCount = $subTableObj->getReqTotalCount_v2($sql, $u ? 5 : 86400);
            $totalPage = ceil($totalCount/$pageSize);
            if($totalCount == 0) return array("state" => 200, "info" => '暂无数据！');
            $pageinfo = array(
                "page" => $page,
                "pageSize" => $pageSize,
                "totalPage" => $totalPage,
                "totalCount" => $totalCount,
            );
        }

		//会员列表需要统计信息状态
		if($u == 1 && $userLogin->getMemberID() > -1){

            if($buMonth){
                $where .= " AND DATE_FORMAT(FROM_UNIXTIME(lt.`pubdate`), '%Y-%m') = '$buMonth'";

                $sql = $dsql->SetQuery("SELECT COUNT(*) total FROM `". $table_all ."` lt WHERE lt.`mold` = 0 AND lt.`del` = 0".$where);
                $res = $dsql->dsqlOper($sql, "results");
                $total0  = $res[0]['total'];

                $sql = $dsql->SetQuery("SELECT COUNT(*) total FROM `". $table_all ."` lt WHERE lt.`mold` = 1 AND lt.`del` = 0".$where);
                $res = $dsql->dsqlOper($sql, "results");
                $total1  = $res[0]['total'];

                $sql = $dsql->SetQuery("SELECT COUNT(*) total FROM `". $table_all ."` lt WHERE lt.`mold` = 2 AND lt.`del` = 0".$where);
                $res = $dsql->dsqlOper($sql, "results");
                $total2  = $res[0]['total'];

                $sql = $dsql->SetQuery("SELECT COUNT(*) total FROM `". $table_all ."` lt WHERE lt.`mold` = 3 AND lt.`del` = 0".$where);
                $res = $dsql->dsqlOper($sql, "results");
                $total3  = $res[0]['total'];

                return array("total0" => $total0, "total1" => $total1, "total2" => $total2, "total3" => $total3);
            }

            $sql = $dsql->SetQuery("SELECT COUNT(*) total FROM `". $table_all ."` lt WHERE `del` = 0").$where." AND lt.`arcrank` = 0";
            $res = $dsql->dsqlOper($sql, "results");
            $totalGray = $res[0]['total'];
            $sql = $dsql->SetQuery("SELECT COUNT(*) total FROM `". $table_all ."` lt WHERE `del` = 0").$where." AND lt.`arcrank` = 1";
            $res = $dsql->dsqlOper($sql, "results");
            $totalAudit = $res[0]['total'];
            $sql = $dsql->SetQuery("SELECT COUNT(*) total FROM `". $table_all ."` lt WHERE `del` = 0").$where." AND lt.`arcrank` = 2";
            $res = $dsql->dsqlOper($sql, "results");
            $totalRefuse = $res[0]['total'];

            // $totalGray   = $dsql->dsqlOper($dsql->SetQuery("SELECT lt.`id`, lt.`title`, lt.`admin`, lt.`subtitle`, lt.`typeid`, lt.`flag`, lt.`weight`, lt.`keywords`, lt.`description`, lt.`source`, lt.`redirecturl`, lt.`litpic`, lt.`color`, lt.`click`, lt.`arcrank`, lt.`pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = lt.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, lt.`waitpay`, lt.`mold`, lt.`zan` FROM `". $table_all ."` lt WHERE `del` = 0").$where." AND lt.`arcrank` = 0", "totalCount");
            // $totalAudit  = $dsql->dsqlOper($dsql->SetQuery("SELECT lt.`id`, lt.`title`, lt.`admin`, lt.`subtitle`, lt.`typeid`, lt.`flag`, lt.`weight`, lt.`keywords`, lt.`description`, lt.`source`, lt.`redirecturl`, lt.`litpic`, lt.`color`, lt.`click`, lt.`arcrank`, lt.`pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = lt.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, lt.`waitpay`, lt.`mold`, lt.`zan` FROM `". $table_all ."` lt WHERE `del` = 0").$where." AND lt.`arcrank` = 1", "totalCount");
            // $totalRefuse = $dsql->dsqlOper($dsql->SetQuery("SELECT lt.`id`, lt.`title`, lt.`admin`, lt.`subtitle`, lt.`typeid`, lt.`flag`, lt.`weight`, lt.`keywords`, lt.`description`, lt.`source`, lt.`redirecturl`, lt.`litpic`, lt.`color`, lt.`click`, lt.`arcrank`, lt.`pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = lt.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, lt.`waitpay`, lt.`mold`, lt.`zan` FROM `". $table_all ."` lt WHERE `del` = 0").$where." AND lt.`arcrank` = 2", "totalCount");

			// if(empty($tables_break)){
			// 	$totalGray   = $dsql->dsqlOper($dsql->SetQuery("SELECT `id`, `title`, `admin`, `subtitle`, `typeid`, `flag`, `weight`, `keywords`, `description`, `source`, `redirecturl`, `litpic`, `color`, `click`, l.`arcrank`, `pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`mold`, l.`zan` FROM `". $subTableObj->getLastTable() ."` l WHERE `del` = 0").$where." AND `arcrank` = 0", "totalCount");
			// 	$totalAudit  = $dsql->dsqlOper($dsql->SetQuery("SELECT `id`, `title`, `admin`, `subtitle`, `typeid`, `flag`, `weight`, `keywords`, `description`, `source`, `redirecturl`, `litpic`, `color`, `click`, l.`arcrank`, `pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`mold`, l.`zan` FROM `". $subTableObj->getLastTable() ."` l WHERE `del` = 0").$where." AND `arcrank` = 1", "totalCount");
			// 	$totalRefuse = $dsql->dsqlOper($dsql->SetQuery("SELECT `id`, `title`, `admin`, `subtitle`, `typeid`, `flag`, `weight`, `keywords`, `description`, `source`, `redirecturl`, `litpic`, `color`, `click`, l.`arcrank`, `pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`mold`, l.`zan` FROM `". $subTableObj->getLastTable() ."` l WHERE `del` = 0").$where." AND `arcrank` = 2", "totalCount");
			// }else{
			// 	foreach ($tables_break as $table_b){
			// 		$archives1 .= "SELECT `id`, `del`, `title`, `admin`, `subtitle`, `typeid`, `flag`, `weight`, `keywords`, `description`, `source`, `redirecturl`, `litpic`, `color`, `click`, l.`arcrank`, `pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`mold`, l.`zan` FROM `".$table_b."` l WHERE `del` = 0".$where." AND `arcrank` = 0" . $union;
			// 	}
			// 	$archives1  = substr($archives1, 0, -(strlen($union)));
			// 	$totalGray = $dsql->dsqlOper($dsql->SetQuery($archives1), "totalCount");

			// 	foreach ($tables_break as $table_b){
			// 		$archives2 .= "SELECT `id`, `del`, `title`, `admin`, `subtitle`, `typeid`, `flag`, `weight`, `keywords`, `description`, `source`, `redirecturl`, `litpic`, `color`, `click`, l.`arcrank`, `pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`mold`, l.`zan` FROM `".$table_b."` l WHERE `del` = 0".$where." AND `arcrank` = 1" . $union;
			// 	}
			// 	$archives2 = substr($archives2, 0, -(strlen($union)));
			// 	$totalAudit = $dsql->dsqlOper($dsql->SetQuery($archives2), "totalCount");

			// 	foreach ($tables_break as $table_b){
			// 		$archives3 .= "SELECT `id`, `del`, `title`, `admin`, `subtitle`, `typeid`, `flag`, `weight`, `keywords`, `description`, `source`, `redirecturl`, `litpic`, `color`, `click`, l.`arcrank`, `pubdate`, (SELECT COUNT(`id`)  FROM `#@__articlecommon` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `floor` = 0) AS total, l.`waitpay`, l.`mold`, l.`zan` FROM `".$table_b."` l WHERE `del` = 0".$where." AND `arcrank` = 2" . $union;
			// 	}
			// 	$archives3 = substr($archives3, 0, -(strlen($union)));
			// 	$totalRefuse = $dsql->dsqlOper($dsql->SetQuery($archives3), "totalCount");
			// }
			//待审核
			$pageinfo['gray'] = $totalGray;
			//已审核
			$pageinfo['audit'] = $totalAudit;
			//拒绝审核
			$pageinfo['refuse'] = $totalRefuse;
		}

        if($u && $buMonth){
            return $pageinfo;
        }
        $atpage = $pageSize*($page-1);
        $where_limit = " LIMIT $atpage, $pageSize";

        $archives .= " FROM `".$table_all."` l";

        /* 默认方式 */
        // $archives_ = $archives . " WHERE `del` = 0";
        // $sql = $dsql->SetQuery($archives_.$where.$where1.$order.$where_limit);

        /* INNER JOIN s */
        // $where = str_replace("l.", "", $where);
        // echo $keyStr;
        // echo "<br>";
        // echo "<br>";
        // $keyStr = "";
        $archives .= " INNER JOIN (SELECT lt.`id` FROM `".$table_all."` lt $keyStr WHERE lt.`del` = 0".$where.$where1.$order.$where_limit.") AS tmp ON tmp.`id` = l.`id`";
        $sql = $dsql->SetQuery($archives);
        /* INNER JOIN e */

        // echo $sql;die;

        // $sql = $dsql->SetQuery($archives.$where1.$order.$where_limit);
        // $s = microtime(true);
        // echo $sql."==";die;
        // $results = $dsql->dsqlOper($sql, "results");
        // echo number_format((microtime(true) - $s), 6);
        // echo $sql;die;
        $results = getCache("article_list", $sql, 300, array("disabled" => $u));

        if($this->param['test']){
            // echo $sql;
            // print_r($results);die;
        }

		if(is_array($results) && !empty($results)){
			global $cfg_clihost;
            $RenrenCrypt = new RenrenCrypt();
            $isWxMiniprogram = GetCookie('isWxMiniprogram');
            $is_android_app = isAndroidApp();
			foreach($results as $key => $val){
				$flag = explode(",", $val['flag']);
				$list[$key]['id']          = $val['id'];
				$list[$key]['title']       = in_array("b", $flag) ? '<strong>'.$val['title'].'</strong>' : $val['title'];
				$list[$key]['subtitle']    = $val['subtitle'];
                $list[$key]['typeid']      = $val['typeid'];
                $list[$key]['admin']       = $val['admin'];

				global $data;
				$data = "";
				$typeArr_ = getParentArr("articletype", $val['typeid']);
				$typeIdArr = array_reverse(parent_foreach($typeArr_, "id"));
                $data = "";
                $typeArr = array_reverse(parent_foreach($typeArr_, "typename"));
				$list[$key]['typeName']    = $typeArr;

                // 一级分类url
                $par = array(
                    "service"     => "article",
                    "template"    => "list",
                    "typeid"      => $typeIdArr[0]
                );
                $url = getUrlPath($par);
                $list[$key]['typeUrl'] = array($url);

                $list[$key]['flag']        = $val['flag'];
                $keywords                  = str_replace(",", " ", $val['keywords']);
                $keywords                  = str_replace("，", " ", $keywords);
                $list[$key]['keywords']    = str_replace("，", " ", $keywords);
                $list[$key]['keywordsArr'] = $keywords ? explode(" ", $keywords) : array();
                $list[$key]['description'] = $val['description'];
                $list[$key]['source']      = $val['source'];
                $list[$key]['writer']      = $val['writer'];
                $list[$key]['redirecturl'] = $val['redirecturl'];
                $list[$key]['litpic']      = !empty($val['litpic']) ? getFilePath($val['litpic']) : "";
                $list[$key]['color']       = $val['color'];
                $list[$key]['click']       = $val['click'];
                $list[$key]['click_']      = $val['click'] >= 10000 ? sprintf("%.1f", $val['click']/10000)."万" : $val['click'];
                $list[$key]['mold']        = $val['mold'];

				//会员中心显示信息状态和支付状态
				if($u == 1 && $userLogin->getMemberID() > -1){
					$list[$key]['arcrank'] = $val['arcrank'];
					$list[$key]['waitpay'] = $val['waitpay'];
				}

				$list[$key]['pubdate']     = $val['pubdate'];

				// $archives = $dsql->SetQuery("SELECT count(`id`) FROM `#@__articlecommon` WHERE `aid` = ".$val['id']." AND `ischeck` = 1");
				// $totalCount = $dsql->dsqlOper($archives, "results", "NUM");
				// $list[$key]['common']     = (int)$totalCount[0][0];
				$list[$key]['common'] = $val['total'];

				$param = array(
					"service"     => "article",
					"template"    => "detail",
					"id"          => $val['id'],
					"flag"        => $val['flag'],
                    "redirecturl" => $val['redirecturl'],
                    "typeid"      => $val['typeid']
					// "param"      => 'typeid='.$val['typeid'],
				);
				$list[$key]['url'] = getUrlPath($param);

				//图表信息
				if($group_img || $val['mold'] == 1){
					$archives = $dsql->SetQuery("SELECT `picPath`, `picInfo` FROM `#@__articlepic` WHERE `aid` = ".$val['id']." ORDER BY `id` ASC LIMIT 0, 6");
					$results = $dsql->dsqlOper($archives, "results");
                    $imgcount = 0;
					if(!empty($results)){
						$imglist = array();
						foreach($results as $k => $value){
							$imglist[$k]["path"] = getFilePath($value["picPath"]);
							$imglist[$k]["info"] = $value["picInfo"];
						}
						$list[$key]['group_img'] = $imglist;

                        $archives = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__articlepic` WHERE `aid` = ".$val['id']);
                        $res = $dsql->dsqlOper($archives, "results");
                        $imgcount = (int)$res[0]['total'];

					}elseif($val['mold'] == 1){
                        $list[$key]['group_img'] = array();
                    }
                    $list[$key]['group_imgnum'] = $imgcount;
				}

                if($val['mold'] == 2 || $val['mold'] == 3){
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
                    $list[$key]['videotime'] = $val['videotime'];
                    $videotime_ = '00:00';
                    if($val['videotime']){
                        $theTime = $val['videotime'];// 秒
                        $theTime1 = 0;// 分
                        $theTime2 = 0;// 小时
                        if($theTime > 60) {
                            $theTime1 = (int)($theTime/60);
                            $theTime = (int)($theTime%60);
                            if($theTime1 > 60) {
                                $theTime2 = (int)($theTime1/60);
                                $theTime1 = (int)($theTime1%60);
                            }
                        }
                        $videotime_ = $theTime;
                        if($theTime1 > 0) {
                            if($theTime2 > 0) {
                                $videotime_ = "".$theTime2.":".$theTime1.":".$videotime_;
                            }else{
                                $videotime_ = "".$theTime1.":".$videotime_;
                            }
                        }else{
                            $videotime_ = "00:".$videotime_;
                        }
                    }

                    $videotype = $val['videotype'];
                    if ($videotype) {
                        $videourl = getRealVideoUrl($val['videourl']);
                    }else{
                        $videourl = getFilePath($val['videourl']);
                    }

                    if($videourl && ($isWxMiniprogram || $is_android_app)){
                        $videotype = 0;
                    }
                    $list[$key]["videourl"] = $videourl;
                    $list[$key]["videotype"] = (int)$videotype;
                }
                $list[$key]['videotime_'] = $videotime_;


				// 打赏
                //读缓存
                $memberCache = $HN_memory->get('article_reward_' . $val["id"]);
                if($memberCache){
                    $data = $memberCache;
                }else{
    				//总条数
                    $archives = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__member_reward` WHERE `aid` = ".$val["id"]." AND `module` = 'article' AND `state` = 1");
    				$res = $dsql->dsqlOper($archives, "results");
                    $totalCount = $res ? $res[0]['total'] : 0;
    				if($totalCount){
    					$archives = $dsql->SetQuery("SELECT SUM(`amount`) totalAmount FROM `#@__member_reward` WHERE `module` = 'article' AND `aid` = ".$val["id"]." AND `state` = 1");
    					$ret = $dsql->dsqlOper($archives, "results");
    					$totalAmount = $ret[0]['totalAmount'];
    				}else{
    					$totalAmount = 0;
    				}
                    $data = array("count" => $totalCount, "amount" => $totalAmount);

                    //写入缓存
                    $HN_memory->set('article_reward_' . $val["id"], $data, 600);
                }

				$list[$key]['reward'] = $data;


                $media = "";
                $mediaid = $val['media'];
                if($mediaid){
                    if(isset($_G['article']['media'][$mediaid])){
                        $media = $_G['article']['media'][$mediaid];
                    }else{
                        $media = getCache("article_mediadetail", function() use($mediaid){
                            return $this->selfmediaDetail(0, $mediaid);
                        }, 600, $mediaid);
                        $_G['article']['media'][$mediaid] = $media;
                    }
                }
                $list[$key]['media'] = $media ? $media : null;

                if(empty($list[$key]['writer'])){
                    if($media){
                        $list[$key]['writer'] = $media['ac_name'];
                    }else{
                        $list[$key]['writer'] = '';
                    }
                }

                if($get_zan){
                    //验证是否已经点赞
                    $zanparams = array(
                        "module" => "article",
                        "temp"   => "detail",
                        "id"     => $val['id'],
                        "check"  => 1
                    );
                    $zan = checkIsZan($zanparams);
                    $list[$key]['zan'] = $zan == "has" ? 1 : 0;

                }
                $list[$key]['zannum'] = (int)$val['zan'];
                $list[$key]['typeset'] = (int)$val['typeset'];

			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 新闻信息详细
     * @return array
     */
	public function detail(){
		global $dsql;
		global $userLogin;
		$articleDetail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
		if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

		//判断是否管理员已经登录
		//功能点：管理员和信息的发布者可以查看所有状态的信息
		$where = "";
		if($userLogin->getUserID() == -1){

			$where = " AND `arcrank` = 1 AND `del` = 0 AND `waitpay` = 0";

			//如果没有登录再验证会员是否已经登录
			if($userLogin->getMemberID() == -1){
				$where = " AND `arcrank` = 1 AND `del` = 0 AND `waitpay` = 0";
			}else{
				$where = " AND (`arcrank` = 1 AND `del` = 0 AND `waitpay` = 0 OR `admin` = ".$userLogin->getMemberID().")";
			}
		}
        $where .= " AND `media_state` = 1";

		$userid = $userLogin->getMemberID();

        // $sub = new SubTable('article', '#@__articlelist');
        // $break_table = $sub->getSubTableById($id);
        $break_table = '#@__articlelist_all';
		$archives = $dsql->SetQuery("SELECT a.*, m.`nickname` FROM `".$break_table."` a LEFT JOIN `#@__member` m ON m.`id` = a.`admin` WHERE a.`id` = ".$id.$where." ORDER BY a.`id` DESC");
		// $results  = $dsql->dsqlOper($archives, "results");
        $results = getCache("article_detail", $archives, 0, $id);
		if($results){
			global $cfg_clihost;
			$articleDetail["id"]          = $results[0]['id'];
			$articleDetail["title"]       = htmlentities($results[0]['title'], ENT_QUOTES, "utf-8");
			$articleDetail["cityid"]      = $results[0]['cityid'];
			$articleDetail["subtitle"]    = htmlentities($results[0]['subtitle'], ENT_QUOTES, "utf-8");
			$articleDetail["flag"]        = $results[0]['flag'];
			$articleDetail["redirecturl"] = htmlentities($results[0]['redirecturl'], ENT_QUOTES, "utf-8");
			$articleDetail["litpic"]      = !empty($results[0]['litpic']) ? getFilePath($results[0]['litpic']) : "";
			$articleDetail["litpicSource"] = !empty($results[0]['litpic']) ? $results[0]['litpic'] : "";
			$articleDetail["source"]      = htmlentities($results[0]['source'], ENT_QUOTES, "utf-8");
			$articleDetail["sourceurl"]   = htmlentities($results[0]['sourceurl'], ENT_QUOTES, "utf-8");
			$articleDetail["writer"]      = htmlentities($results[0]['writer'], ENT_QUOTES, "utf-8");
            $articleDetail["typeid"]      = $results[0]['typeid'];
            $articleDetail["admin"]       = $results[0]['admin'];
            $articleDetail["admin_nickname"]       = $results[0]['nickname'];


            $mold = $results[0]['mold'];
            $articleDetail["mold"] = $mold;

			global $data;
			$data = "";
			$typeArr = getParentArr("articletype", $results[0]['typeid']);
			$typeArr = array_reverse(parent_foreach($typeArr, "typename"));
			$articleDetail['typeName']    = join(" > ", $typeArr);

			$articleDetail["keywords"]    = str_replace(",", " ", htmlentities($results[0]['keywords'], ENT_QUOTES, "utf-8"));
			$articleDetail["keywordsList"] = explode(" ", str_replace(",", " ", htmlentities($results[0]['keywords'], ENT_QUOTES, "utf-8")));
			$articleDetail["description"] = str_replace(array("\r\n", "\r", "\n"), '', strip_tags(htmlentities($results[0]['description'], ENT_QUOTES, "utf-8")));
			$articleDetail["notpost"]     = $results[0]['notpost'];
			$articleDetail["click"]       = $results[0]['click'];
			$articleDetail["color"]       = $results[0]['color'];
			$articleDetail["arcrank"]     = $results[0]['arcrank'];
			$articleDetail["pubdate"]     = $results[0]['pubdate'];
			$articleDetail["reward_switch"] = $results[0]['reward_switch'];

            global $cfg_secureAccess;
            global $cfg_basehost;

            $isWxMiniprogram = GetCookie('isWxMiniprogram');
            $is_android_app = isAndroidApp();

            if($mold == 0 || $mold == 1){
    			$body = "";
    			$archives = $dsql->SetQuery("SELECT `body` FROM `#@__article` WHERE `aid` = ".$id);
    			$results1  = $dsql->dsqlOper($archives, "results");
    			if($results1){
    				$body = $results1[0]['body'];
    			}

    			$mbody = $results[0]['mbody'];

    			$u = str_replace('//', '\/\/', $cfg_secureAccess) . $cfg_basehost . '\/include\/attachment.php';
    			$body = preg_replace('/'.$u.'/', '/include/attachment.php', $body);
    			$body = preg_replace('/\/include\/attachment.php/', $cfg_secureAccess . $cfg_basehost . '/include/attachment.php', $body);

                $u = str_replace('//', '\/\/', $cfg_secureAccess) . $cfg_basehost . '\/include\/attachment.php';
                $mbody = preg_replace('/'.$u.'/', '/include/attachment.php', $mbody);
    			$mbody = preg_replace('/\/include\/attachment.php/', $cfg_secureAccess . $cfg_basehost . '/include/attachment.php', $mbody);

    			$articleDetail["body"]       = $body;
    			$articleDetail["mbody"]      = empty($mbody) ? $body : $mbody;

            }

            if($mold == 1){
    			//图表信息
    			$archives = $dsql->SetQuery("SELECT * FROM `#@__articlepic` WHERE `aid` = ".$id." ORDER BY `id` ASC");
    			$results2 = $dsql->dsqlOper($archives, "results");

    			if(!empty($results2)){
    				$imglist = array();
    				foreach($results2 as $key => $value){
    					$imglist[$key]["pathSource"] = $value["picPath"];
    					$imglist[$key]["path"] = getFilePath($value["picPath"]);
    					$imglist[$key]["info"] = $value["picInfo"];
    				}
    			}else{
    				$imglist = array();
    			}

    			$articleDetail["imglist"]     = $imglist;

            }else if($mold == 2){
                $videotype = (int)$results[0]['videotype'];
                $articleDetail["videoSource"] = $results[0]['videourl'];
                //$articleDetail["video"] = $results[0]['videotype'] == 0 ? getFilePath($results[0]['videourl']) : $results[0]['videourl'];

                if ($videotype) {
                    $videourl = $results[0]['videourl'];
                    $articleDetail["realVideoUrl"] = getRealVideoUrl($videourl);
                }else{
                    $videourl = getFilePath($results[0]['videourl']);
                }

                if($articleDetail["realVideoUrl"] && ($isWxMiniprogram || $is_android_app)){
                    $videotype = 0;
                    $videourl = $articleDetail["realVideoUrl"];
                }
                $articleDetail["video"] = $videourl;
                $articleDetail["videotype"] = $videotype;
            }else if($mold == 3){
                $articleDetail["videoSource"] = $results[0]['videourl'];
                $articleDetail["video"] = getFilePath($results[0]['videourl']);
            }

            // $archives = $dsql->SetQuery("SELECT `id` FROM `#@__articlecommon` WHERE `aid` = ".$results[0]['id']." AND `ischeck` = 1 AND `floor` = 0");
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'article-detail' AND `aid` = '$id' AND `pid` = 0");
			$totalCount = $dsql->dsqlOper($archives, "totalCount");
            $articleDetail['common'] = $totalCount;

            // 打赏
            $archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_reward` WHERE `aid` = ".$results[0]["id"]." AND `state` = 1");
            //总条数
            $totalCount = $dsql->dsqlOper($archives, "totalCount");
            $articleDetail['rewardcount'] = $totalCount;

			//发布者会员信息
			$member = $userLogin->getMemberInfo($results[0]['admin']);
			$articleDetail['member'] = is_array($member) ? $member : array();

            //发布者媒体信息
            $media = $this->selfmediaDetail($results[0]['admin']);
            $articleDetail['media'] = $media ? $media : null;
			$articleDetail["mold"] = $results[0]['mold'];

            if(empty($articleDetail['writer'])){
                if($media){
                    $articleDetail['writer'] = $media['ac_name'];
                }else{
                    $articleDetail['writer'] = '';
                }
            }

			//是否相互关注
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $userid AND `fid` = " . $val['admin']);
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$articleDetail['isfollow'] = 1;//关注
			}elseif($userid == $val['ruid']){
				$articleDetail['isfollow'] = 2;//自己
			}else{
				$articleDetail['isfollow'] = 0;//未关注
			}

			//验证是否已经收藏
			$params = array(
				"module" => "article",
				"temp"   => "detail",
				"type"   => "add",
				"id"     => $id,
				"check"  => 1
			);
			$collect = checkIsCollect($params);
			$articleDetail['collect'] = $collect == "has" ? 1 : 0;

			//验证是否已经点赞
			$zanparams = array(
				"module" => "article",
				"temp"   => "detail",
				"id"     => $id,
				"check"  => 1
			);
			$zan = checkIsZan($zanparams);
			$articleDetail['zan'] = $zan == "has" ? 1 : 0;
            $articleDetail['zannum'] = $results[0]['zan'];

            $media_arctype = $results[0]['media_arctype'];
            $media_arctypeName = "";
            if($media_arctype){
                $sql = $dsql->SetQuery("SELECT `typename` FROM `#@__article_selfmedia_arctype` WHERE `id` = ".$media_arctype);
                $res = $dsql->dsqlOper($sql, "results");
                if($res){
                    $media_arctypeName = $res[0]['typename'];
                }else{
                    $media_arctype = 0;
                }
            }
            $articleDetail['typeset'] = $results[0]['typeset'];
            $articleDetail['media_arctype'] = $media_arctype;
            $articleDetail['media_arctypeName'] = $media_arctypeName;

		}
		return $articleDetail;
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

		$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__articlecommon` WHERE `aid` = ".$newsid." AND `ischeck` = 1 AND `floor` = 0".$oby);
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

				//$list[$key]['lower']   = $this->getCommonList($val['id']);
			}
		}
		return array("pageInfo" => $pageinfo, "list" => $list);
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

        if(empty($fid)) return array("state" => 200, "info" => "参数错误");

        $pageSize = empty($pageSize) ? 99999 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        if($fid){
            $where = " AND `floor` = '$fid'";
        }

        $where .= " AND `ischeck` = 1";

        $archives = $dsql->SetQuery("SELECT `id` FROM `#@__articlecommon` WHERE 1 = 1".$where);
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

        $archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__articlecommon` WHERE 1 = 1".$where);

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

                //$list[$key]['lower']   = $this->getCommonList($val['id']);


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

		$id = $this->param['id'];
		if(empty($id)) return "请传递评论ID！";
		$memberID = $userLogin->getMemberID();
		if($memberID == -1 || empty($memberID)) return "请先登录！";

		$archives = $dsql->SetQuery("SELECT `duser` FROM `#@__articlecommon` WHERE `id` = ".$id);
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

			$archives = $dsql->SetQuery("UPDATE `#@__articlecommon` SET `good` = `good` + 1 WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($archives, "update");

			$archives = $dsql->SetQuery("UPDATE `#@__articlecommon` SET `duser` = '$nuser' WHERE `id` = ".$id);
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

        $userid = $userLogin->getMemberID();

		$archives = $dsql->SetQuery("INSERT INTO `#@__articlecommon` (`aid`, `floor`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `ischeck`, `duser`) VALUES ('$aid', '$id', '".$userid."', '$content', ".GetMkTime(time()).", '".GetIP()."', '".getIpAddr(GetIP())."', 0, 0, '$ischeck', '')");
		$lid  = $dsql->dsqlOper($archives, "lastid");
		if($lid){

            // 消息通知
            if($ischeck){
                $sub = new SubTable('article', '#@__articlelist');
                $break_table = $sub->getSubTableById($aid);
                $archives = $dsql->SetQuery("SELECT a.`admin`, a.`title`, m.`mtype`, m. `username` FROM `".$break_table."` a LEFT JOIN `#@__member` m ON m.`id` = a.`admin` WHERE a.`id` = ".$aid);
                $results  = $dsql->dsqlOper($archives, "results");

                if($results){

                    $info = $results[0];
                    $param = array(
                        "service" => "article",
                        "template" => "detail",
                        "id" => $aid
                    );

					//自定义配置
	        		$data = array(
	        			"username" => $info['username'],
	        			"title" => $info['title'],
	        			"date" => date("Y-m-d H:i:s", time()),
	        			"fields" => array(
	        				'keyword1' => '信息标题',
	        				'keyword2' => '发布时间',
	        				'keyword3' => '进展状态'
	        			)
	        		);

                    // 通知作者
                    if($id == 0){

                        if($info['admin'] != $userid && ($info['mtype'] == 1 || $info['mtype'] == 2)){
                            updateMemberNotice($info['admin'], "会员-新评论通知", $param, $data);
                        }

                    // 通知被评论人
                    }else{
                        // $sql = $dsql->SetQuery("SELECT c.`userid`, m.`username` FROM `#@__articlecommon` c LEFT JOIN `#@__member` m ON m.`id` = c.`userid` WHERE c.`id` = $id AND c.`userid` != $userid");
                        // $res = $dsql->dsqlOper($sql, "results");
                        // if($res){
                        //     $data['username'] = $res[0]['username'];
                        //     updateMemberNotice($res[0]['userid'], "会员-新评论通知", $param, $data);
                        // }
                    }
                }
            }

			$archives = $dsql->SetQuery("SELECT `id`, `userid`, `content`, `dtime`, `ip`, `ipaddr`, `good`, `bad`, `duser` FROM `#@__articlecommon` WHERE `id` = ".$lid);
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


	/**
		* 发布信息
		* @return array
		*/
	public function put(){
        global $dsql;
        global $userLogin;

        $param = $this->param;


        $cityid      =  $param['cityid'];
        $title       = filterSensitiveWords(addslashes($param['title']));
        $typeid      = $param['typeid'];
        $litpic      = $param['litpic'];
        $body        = filterSensitiveWords($param['body'], false);
        $imglist     = $param['imglist'];
        $writer      = filterSensitiveWords(addslashes($param['writer']));
        $source      = filterSensitiveWords(addslashes($param['source']));
        $sourceurl   = filterSensitiveWords(addslashes($param['sourceurl']));
        $keywords    = filterSensitiveWords(addslashes($param['keywords']));
        $description = filterSensitiveWords(addslashes($param['description']));
        $mold        = (int)$param['mold'];
        $videotype   = (int)$param['videotype'];
        $videourl    = $param['videourl'];
        $video       = $param['video'];
        $media_arctype = (int)$param['media_arctype'];
        $typeset     = (int)$param['typeset'];
        $zhuanti     = (int)$param['zhuanti'];
        $media       = (int)$param['media'];

        global $dellink, $autolitpic;
        include HUONIAOINC."/config/article.inc.php";
        $dellink     = (int)$customDelLink;
        $autolitpic  = (int)$customAutoLitpic;
        $arcrank     = (int)$customFabuCheck;
        $auditSwidth = (int)$custom_auditSwitch;

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array("state" => 200, "info" => '登录超时，请重新登录！');
        }

        //用户信息
        $userinfo = $userLogin->getMemberInfo();

        $verify = $this->selfmedia_verify($uid, $userinfo, "check", $mediaDetail);
        if($verify != "ok") return $verify;

        // 需要支付费用
        $amount = 0;

        // 是否独立支付 普通会员或者付费会员超出限制
        $alonepay = 0;

        $alreadyFabu = 0; // 付费会员当天已免费发布数量

        //企业会员或已经升级为收费会员的状态才可以发布 --> 普通会员也可发布
        //增加媒体入驻之后取消收费
        if(false && $userinfo['userType'] == 1){

            $toMax = false;

            if($userinfo['level']){

                $memberLevelAuth = getMemberLevelAuth($userinfo['level']);
                $articleCount = (int)$memberLevelAuth['article'];

                //统计用户当天已发布数量 @
                $today = GetMkTime(date("Y-m-d", time()));
                $tomorrow = GetMkTime(date("Y-m-d", strtotime("+1 day")));
                $sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__articlelist_all` WHERE `admin` = $uid AND `pubdate` >= $today AND `pubdate` < $tomorrow AND `alonepay` = 0 AND `waitpay` = 0");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $alreadyFabu = $ret[0]['total'];
                    if($alreadyFabu >= $articleCount){
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
                    $amount = $fabuAmount["article"];
                }else{
                    $amount = 0;
                }

            }

        }

        $body = AnalyseHtmlBodyLinkLitpic($body, $litpic);

        $flag = $litpic ? "p" : "";

        if(empty($cityid)) return array("state" => 200, "info" => '请选择城市');
        if(empty($title)) return array("state" => 200, "info" => '请输入标题');
        if(empty($typeid)) return array("state" => 200, "info" => '请选择投稿分类');
        if($mold == 0 && empty($body)) return array("state" => 200, "info" => '请输入投稿内容');
        // if(empty($writer)) return array("state" => 200, "info" => '请输入作者');
        if(empty($source)) return array("state" => 200, "info" => '请输入投稿来源');

        if($mold == 1){
            if(empty($litpic)){
                return array("state" => 200, "info" => '请上传缩略图');
            }
            if(empty($imglist)){
                return array("state" => 200, "info" => '请上传图集');
            }
        }
        if($mold == 2){
            if(!$videotype){

                if(empty($video)){
                    return array("state" => 200, "info" => '请上传视频');
                }

                $videourl = $video;

            }else{

                if(empty($videourl)){
                    return array("state" => 200, "info" => '请填写视频地址');
                }
                if(stripos($videourl,'<iframe') !== false){
                    $r = preg_match("/\bsrc=(.*?)[\s|>]/i", $videourl, $res);
                    if($r){
                        $videourl = trim($res[1], "'");
                        $videourl = trim($videourl, '"');
                    }else{
                        return array("state" => 200, "info" => '视频地址获取失败');
                    }
                }
                $videourl = stripslashes($videourl);

                if(empty($litpic)){
                    return array("state" => 200, "info" => '请上传缩略图');
                }
            }
        }
        if($mold == 3){
            $videourl = $video;

            if(!isApp()){
                // return array("state" => 200, "info" => '短视频类型仅支持在APP端上传并发布');
            }

        }

        $title  = cn_substrR($title, 50);
        $writer = cn_substrR($writer, 10);
        $source = cn_substrR($source, 20);
        $sourceurl = cn_substrR($sourceurl, 150);


        //保存到主表
        $waitpay = $amount > 0 ? 1 : 0;

        // 审核流程
        $audit_log = $audit_state = "";
        if($auditSwidth){
            $arcrank = 0;
            $audit_log = $audit_state = array();
            // 获取分类管理员
            $sql = $dsql->SetQuery("SELECT `admin` FROM `#@__articletype` WHERE `id` = $typeid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $admin = '';
                if($ret[0]['admin']){
                    $admin = $ret[0]['admin'];
                // 查找父级是否设置管理员
                }else{
                    global $data;
                    $data = "";
                    $typeArr = getParentArr("articletype", $typeid);
                    $typeArr = parent_foreach($typeArr, "id");
                    foreach ($typeArr as $key => $value) {
                        $sql = $dsql->SetQuery("SELECT `admin` FROM `#@__articletype` WHERE `id` = $value AND `admin` != ''");
                        $ret = $dsql->dsqlOper($sql, "results");
                        if($ret){
                            $admin = $ret[0]['admin'];
                            break;
                        }

                    }
                }
                if($admin){
                    $audit_state[] = "OR";
                    $adminids = explode(",", $admin);
                    foreach ($adminids as $k => $v) {
                        $audit_log[$v] = array(
                            "id" => $v,
                            "admin" => $v,
                            "or" => 1,
                            "state" => 0,
                            "note" => '',
                            "pubdate" => '',
                            "log" => array()
                        );
                        $audit_state[] = $v.":0";
                    }
                }

            }
            // print_r($audit_log);
            // echo $audit_state;
            $audit_log = serialize($audit_log);
            $audit_state = join("|", $audit_state);
        }

        $sub = new SubTable('article', '#@__articlelist');
        $insert_table_name = $sub->getLastTable();

        $mediaId = $mediaDetail['id'];

        $archives = $dsql->SetQuery("INSERT INTO `".$insert_table_name."` (`cityid`, `title`, `flag`, `litpic`, `source`, `sourceurl`, `writer`, `typeid`, `keywords`, `description`, `mbody`, `arcrank`, `pubdate`, `admin`, `waitpay`, `alonepay`, `audit_state`, `audit_log`, `audit_edit`, `mold`, `videotype`, `videourl`, `media_arctype`, `typeset`, `zhuanti`, `media`) VALUES ('$cityid', '$title', '$flag', '$litpic', '$source', '$sourceurl', '$writer', '$typeid', '$keywords', '$description', '', '$arcrank', ".GetMkTime(time()).", '$uid', '$waitpay', '$alonepay', '$audit_state', '$audit_log', '', $mold, $videotype, '$videourl', $media_arctype, $typeset, $zhuanti, $mediaId)");
        $aid = $dsql->dsqlOper($archives, "lastid");

        if(is_numeric($aid)){
            //保存图集表
            if($imglist != ""){
                $picList = explode(",",$imglist);
                foreach($picList as $k => $v){
                    $picInfo = explode("|", $v);
                    $pics = $dsql->SetQuery("INSERT INTO `#@__articlepic` (`aid`, `picPath`, `picInfo`) VALUES ('$aid', '$picInfo[0]', '".filterSensitiveWords($picInfo[1])."')");
                    $dsql->dsqlOper($pics, "update");
                }
            }

            //保存内容表
            $art = $dsql->SetQuery("INSERT INTO `#@__article` (`aid`, `body`) VALUES ('$aid', '$body')");
            $dsql->dsqlOper($art, "update");

            if($zhuanti){
                $sql = $dsql->SetQuery("INSERT INTO `#@__article_zhuantilist`(`aid`, `typeid`) VALUES ($aid, $zhuanti)");
                $dsql->dsqlOper($sql, "lastid");
            }

            //后台消息通知
            if(!$arcrank){
                updateAdminNotice("article", "detail");
            }

            if($userinfo['level']){
                $auth = array("level" => $userinfo['level'], "levelname" => $userinfo['levelName'], "alreadycount" => $alreadyFabu, "maxcount" => $articleCount);
            }else{
                $auth = array("level" => 0, "levelname" => "普通会员", "maxcount" => 0);
            }

            if($arcrank){
                updateCache("article_list", 300);
            }

            return array("auth" => $auth, "aid" => $aid, "amount" => $amount);
            // return $aid;

        }else{

            return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');

        }

    }


    /**
        * 修改信息
        * @return array
        */
    public function edit(){
        global $dsql;
        global $userLogin;

        $param = $this->param;

        $id       = $param['id'];

        if(empty($id)) return array("state" => 200, "info" => '数据传递失败！');

        $title    = filterSensitiveWords(addslashes($param['title']));
        $cityid   = $param['cityid'];
        $typeid   = $param['typeid'];
        $litpic   = $param['litpic'];
        $body     = filterSensitiveWords($param['body'], false);
        $imglist  = $param['imglist'];
        $writer   = filterSensitiveWords(addslashes($param['writer']));
        $source   = filterSensitiveWords(addslashes($param['source']));
        $sourceurl = filterSensitiveWords(addslashes($param['sourceurl']));
        $keywords = filterSensitiveWords(addslashes($param['keywords']));
        $description = filterSensitiveWords(addslashes($param['description']));
        $mold        = (int)$param['mold'];
        $videotype   = (int)$param['videotype'];
        $videourl    = $param['videourl'];
        $video       = $param['video'];
        $media_arctype = (int)$param['media_arctype'];
        $typeset     = (int)$param['typeset'];
        $zhuanti     = (int)$param['zhuanti'];
        $media       = (int)$param['media'];

        global $dellink, $autolitpic;
        include HUONIAOINC."/config/article.inc.php";
        $dellink    = (int)$customDelLink;
        $autolitpic = (int)$customAutoLitpic;
        $arcrank    = (int)$customFabuCheck;

        $body = AnalyseHtmlBodyLinkLitpic($body, $litpic);

        //获取用户ID
        $uid = $userLogin->getMemberID();
        if($uid == -1){
            return array("state" => 200, "info" => '登录超时，请重新登录！');
        }

        //用户信息
        $userinfo = $userLogin->getMemberInfo();

        $verify = $this->selfmedia_verify($uid, $userinfo, "check", $mediaDetail);
        if($verify != "ok") return $verify;

        $sub = new SubTable('article', '#@__articlelist');
        $break_table = $sub->getSubTableById($id);

        $archives = $dsql->SetQuery("SELECT `id`, `mold` FROM `".$break_table."` WHERE `id` = ".$id." AND `admin` = ".$uid);
        $results  = $dsql->dsqlOper($archives, "results");
        if(!$results){
            return array("state" => 200, "info" => '权限不足，修改失败！');
        }
        $mold_ = $results[0]['mold'];

        // if($mold_ == 3 && $mold != 3) return array("state" => 200, "info" => '短视频类型不支持修改类型');

        if(empty($cityid)) return array("state" => 200, "info" => '请选择城市');
        if(empty($title)) return array("state" => 200, "info" => '请输入标题');
        if(empty($typeid)) return array("state" => 200, "info" => '请选择投稿分类');
        if($mold == 0 && empty($body)) return array("state" => 200, "info" => '请输入投稿内容');
        // if(empty($writer)) return array("state" => 200, "info" => '请输入作者');
        if(empty($source)) return array("state" => 200, "info" => '请输入投稿来源');

        if($mold == 1){
            if(empty($litpic)){
                return array("state" => 200, "info" => '请上传缩略图');
            }
            if(empty($imglist)){
                return array("state" => 200, "info" => '请上传图集');
            }
        }
        if($mold == 2){
            if(!$videotype){

                if(empty($video)){
                    return array("state" => 200, "info" => '请上传视频');
                }

                $videourl = $video;

            }else{

                if(empty($videourl)){
                    return array("state" => 200, "info" => '请填写视频地址');
                }


                if(stripos($videourl,'<iframe') !== false){
                    $r = preg_match("/\bsrc=(.*?)[\s|>]/i", $videourl, $res);
                    if($r){
                        $videourl = trim($res[1], "'");
                        $videourl = trim($videourl, '"');
                    }else{
                        return array("state" => 200, "info" => '视频地址获取失败');
                    }
                }
                $videourl = stripslashes($videourl);
            }
        }
        if($mold == 3){
            $videourl = $video;

            if(!isApp()){
                // return array("state" => 200, "info" => '短视频类型仅支持在APP端上传并发布');
            }

            if(empty($videourl)){
                return array("state" => 200, "info" => '请上传缩视频');
            }
        }

        $title  = cn_substrR($title, 50);
        $writer = cn_substrR($writer, 10);
        $source = cn_substrR($source, 20);
        $sourceurl = cn_substrR($sourceurl, 150);

        $mediaId = $mediaDetail['id'];

        $archives = $dsql->SetQuery("UPDATE `".$break_table."` SET `cityid` = '$cityid', `title` = '$title', `litpic` = '$litpic', `source` = '$source', `sourceurl` = '$sourceurl', `writer` = '$writer', `typeid` = '$typeid', `keywords` = '$keywords', `description` = '$description', `arcrank` = '$arcrank', `mold` = $mold, `videotype` = $videotype, `videourl` = '$videourl', `media_arctype` = $media_arctype, `typeset` = $typeset, `zhuanti` = $zhuanti, `media` = $mediaId WHERE `id` = ".$id);
        $results = $dsql->dsqlOper($archives, "update");

        if($results != "ok"){
            return array("state" => 200, "info" => '保存到数据时发生错误，请检查字段内容！');
        }

        //先删除文档所属图集
        $archives = $dsql->SetQuery("DELETE FROM `#@__articlepic` WHERE `aid` = ".$id);
        $results = $dsql->dsqlOper($archives, "update");

        //保存图集表
        if($imglist != ""){
            $picList = explode(",",$imglist);
            foreach($picList as $k => $v){
                $picInfo = explode("|", $v);
                $pics = $dsql->SetQuery("INSERT INTO `#@__articlepic` (`aid`, `picPath`, `picInfo`) VALUES ('$id', '$picInfo[0]', '".filterSensitiveWords($picInfo[1])."')");
                $dsql->dsqlOper($pics, "update");
            }
        }

        //保存内容表
        $art = $dsql->SetQuery("UPDATE `#@__article` SET `body` = '$body' WHERE `aid` = ".$id);
        $results = $dsql->dsqlOper($art, "update");

        //后台消息通知
        if(!$arcrank){
            updateAdminNotice("article", "detail");
        }

        // 清除缓存
        clearCache("article_detail", $id);

        return "修改成功！";

    }


	/**
		* 删除信息
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

		$archives = $dsql->SetQuery("SELECT * FROM `#@__articlelist_all` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['admin'] == $uid){
				$archives = $dsql->SetQuery("UPDATE `#@__articlelist_all` SET `del` = 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");

                // 清除缓存
                clearCache("article_detail", $id);
                checkCache("article_list", $id);
				return array("state" => 100, "info" => '删除成功！');
			}else{
				return array("state" => 101, "info" => '权限不足，请确认帐户信息后再进行操作！');
			}
		}else{
			return array("state" => 101, "info" => '信息不存在，或已经删除！');
		}

	}


	/**
		* 验证文章状态是否可以打赏
		* @return array
		*/
	public function checkRewardState(){
		global $dsql;
		global $userLogin;

		$aid = $this->param['aid'];

		if(!is_numeric($aid)) return array("state" => 200, "info" => '格式错误！');

		//打赏总开关
		include HUONIAOINC."/config/article.inc.php";
		$rewardSwitch = (int)$customRewardSwtich;
		if($rewardSwitch){
			return array("state" => 200, "info" => $customChannelName.'已关闭打赏！');
		}

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 100, "info" => 'true');
		}

		$archives = $dsql->SetQuery("SELECT `admin`, `reward_switch` FROM `#@__articlelist_all` WHERE `id` = ".$aid);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			if($results[0]['admin'] == $uid){
				return array("state" => 200, "info" => '自己不可以给自己打赏！');
			}else{
				if($results[0]['reward_switch']){
					return array("state" => 200, "info" => '该文章已关闭打赏！');
				}
				return array("state" => 100, "info" => 'true');
			}
		}else{
			return array("state" => 200, "info" => '信息不存在，或已经删除，不可以打赏，请确认后重试！');
		}

	}


	/**
	 * 打赏记录
	 * @param $fid int 评论ID
	 * @return array
	 */
	function rewardList(){
		global $dsql;

		$param   = $this->param;
		$aid     = $param['aid']; //信息ID

		$archives = $dsql->SetQuery("SELECT m.`id`, m.`nickname`, m.`photo`, r.`amount`, r.`date` FROM `#@__member_reward` r LEFT JOIN `#@__member` m ON m.`id` = r.`uid` WHERE r.`aid` = ".$aid." AND r.`state` = 1 ORDER BY r.`id` ASC");
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		$list = array();
		if($totalCount > 0){
            global $cfg_secureAccess;
            global $cfg_basehost;
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach($results as $key => $val){
					$list[$key]['id']       = $val['id'];
					$list[$key]['username'] = $val['nickname'];
					$list[$key]['photo']    = !empty($val['photo']) ? getFilePath($val['photo']) : "";
					$list[$key]['amount']   = $val['amount'];
                    $list[$key]['date']     = $val['date'];
					$list[$key]['url']      = $cfg_secureAccess.$cfg_basehost."/user/".$val['id'];
				}
			}
		}
		return array("pageInfo" => array("totalCount" => $totalCount), "list" => $list);
	}



	/**
	 * 打赏
	 * @return array
	 */
	public function reward(){
		global $dsql;
		global $userLogin;

		$param   = $this->param;
		$aid     = $param['aid'];      //信息ID
		$amount  = $param['amount'];   //打赏金额
		$paytype = $param['paytype'];  //支付方式

		$uid = $userLogin->getMemberID();  //当前登录用户

        $isMobile = isMobile();

		//信息url
		$param = array(
			"service"     => "article",
			"template"    => "detail",
			"id"          => $aid
		);
		$url = getUrlPath($param);

		//验证金额
		if($amount <= 0 || !is_numeric($aid) || (empty($paytype) && !isMobile()) ){
			header("location:".$url);
			die;
		}

		//打赏总开关
		include HUONIAOINC."/config/article.inc.php";
		$rewardSwitch = (int)$customRewardSwtich;
		if($rewardSwitch){
			header("location:".$url);
			die;
		}

		//查询信息发布人
		$sql = $dsql->SetQuery("SELECT `admin`, `reward_switch` FROM `#@__articlelist_all` WHERE `id` = ".$aid);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			//信息不存在
			header("location:".$url);
			die;
		}elseif($ret[0]['reward_switch']){
			//未开启打赏
			header("location:".$url);
			die;
		}


		$admin = $ret[0]['admin'];

		//自己不可以给自己打赏
		if($admin == $uid){
			//信息不存在
			header("location:".$url);
			die;
		}

		//订单号
		$ordernum = create_ordernum();

		$archives = $dsql->SetQuery("INSERT INTO `#@__member_reward` (`ordernum`, `module`, `uid`, `to`, `aid`, `amount`, `state`, `date`) VALUES ('$ordernum', 'article', '$uid', '$admin', '$aid', '$amount', 0, ".GetMkTime(time()).")");
		$return = $dsql->dsqlOper($archives, "update");
		if($return != "ok"){
			die("提交失败，请稍候重试！");
		}

        if($isMobile){
            $param = array(
                "service" => "article",
                "template" => "pay",
                "param" => "ordernum=".$ordernum
            );
            header("location:".getUrlPath($param));
            die;
        }

		//跳转至第三方支付页面
		createPayForm("article", $ordernum, $amount, $paytype, "打赏文章");

	}


	/**
	 * 支付成功
	 * 此处进行支付成功后的操作，例如发送短信等服务
	 *
	 */
	public function paySuccess(){
		global $cfg_secureAccess;
		global $cfg_basehost;
		$param = $this->param;
		if(!empty($param)){
			global $dsql;

			$paytype  = $param['paytype'];
			$ordernum = $param['ordernum'];
			$date     = GetMkTime(time());

			//查询订单信息
			$sql = $dsql->SetQuery("SELECT * FROM `#@__member_reward` WHERE `ordernum` = '$ordernum'");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){

				$rid    = $ret[0]['id'];
				$uid    = $ret[0]['uid'];
				$to     = $ret[0]['to'];
				$aid    = $ret[0]['aid'];
				$amount = $ret[0]['amount'];

				//文章信息
				$sql = $dsql->SetQuery("SELECT `title` FROM `#@__articlelist_all` WHERE `id` = $aid");
				$ret = $dsql->dsqlOper($sql, "results");
				$title = $ret[0]['title'];

				$title_ = '<a href="'.$cfg_secureAccess.$cfg_basehost.'/index.php?service=article&template=detail&id='.$aid.'" target="_blank">'.$title.'</a>';

				//更新订单状态
				$sql = $dsql->SetQuery("UPDATE `#@__member_reward` SET `state` = 1 WHERE `id` = ".$rid);
				$dsql->dsqlOper($sql, "update");

				//如果是会员打赏，保存操作日志
				if($uid != -1){
					$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '0', '$amount', '文章打赏：$title_', '$date')");
					$dsql->dsqlOper($archives, "update");
				}

				//扣除佣金
				global $cfg_rewardFee;
				$fee = $amount * $cfg_rewardFee / 100;
		    $fee = $fee < 0.01 ? 0 : $fee;
		    $amount_ = sprintf('%.2f', $amount - $fee);

				//将费用打给文章作者
				$archives = $dsql->SetQuery("UPDATE `#@__member` SET `money` = `money` + '$amount_' WHERE `id` = '$to'");
				$dsql->dsqlOper($archives, "update");

				//保存操作日志
				$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$to', '1', '$amount_', '文章打赏：$title_', '$date')");
				$dsql->dsqlOper($archives, "update");


				//会员通知
				$param = array(
					"service"  => "article",
					"template" => "detail",
					"id"   => $aid
				);

				//获取会员名
				$username = "";
				$sql = $dsql->SetQuery("SELECT `username`, `nickname` FROM `#@__member` WHERE `id` = $to");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$username = $ret[0]['nickname'] ? $ret[0]['nickname'] : $ret[0]['username'];
				}

				//自定义配置
				$config = array(
					"username" => $username,
					"title" => $title,
					"amount" => $amount,
					"date" => date("Y-m-d H:i:s", $date),
					"fields" => array(
						'keyword1' => '打赏目标',
						'keyword2' => '打赏金额',
						'keyword3' => '时间'
					)
				);
				updateMemberNotice($to, "会员-打赏通知", $param, $config);

			}

		}
	}


    //插入测试数据
	public function insert()
    {
        global $dsql;
        return;

        set_time_limit(0);
        ini_set("memory_limit","1024M");

        for ($i = 0; $i<1000; $i++){

            $sql_choose = $dsql->SetQuery("SELECT * FROM `#@__article_breakup_table`");
            $breakup_table_res = $dsql->dsqlOper($sql_choose, "results");
            if(!$breakup_table_res){
                $insert_table_name = 'huoniao_articlelist';
            }else{
                $insert_table_name = $breakup_table_res[count($breakup_table_res)-1]['table_name'];
            }
            $typeid = rand(2,26);
            $sql = <<<EE
INSERT INTO `$insert_table_name` (`cityid`, `title`, `subtitle`, `flag`, `redirecturl`, `weight`, `litpic`, `source`, `sourceurl`, `writer`, `typeid`, `keywords`, `description`, `mbody`, `notpost`, `click`, `color`, `arcrank`, `pubdate`, `admin`, `reward_switch`  , `flag_h`, `flag_r`, `flag_b`, `flag_t`, `flag_p`) VALUES ('166', '阪位列三甲之列。前五名还有卡尔加里（加拿大）和上榜', '外媒盘点', 'h,t', '', '1', '', ' 参考消息网(北京)', 'http://m.ckxx.net/zhongguo/p/116445.html', '程钢_NN7377', $typeid, '上海,北京,广州,首', '外媒盘点全球140个最宜居城市:北京上海广州等上榜 ,上海 北京 广州 首都 墨尔本', '', '0', '96', '', '1', '1534322330', '29', '0' ,'h', '', '', 't', '')
EE;
            $aid = $dsql->dsqlOper($sql, "lastid");
            $coun = $dsql->dsqlOper("select `id` from $insert_table_name", "totalCount");
            if($coun >= ARTICLE_TABLE_SIZE){
                $new_table = createArticleTable($aid); //创建分表
                saveBreakUpTable($new_table, $aid); //保存分表名称以及开始id
            }
            $sql2 = <<<AA
INSERT INTO `huoniao_article` (`aid`, `body`) VALUES ($aid, '（原标题：外媒盘点全球140个最宜居城市：北京上海广州等上榜）
参考消息网8月15日报道 外媒称，英国《经济学人》杂志公布全球140个最宜居城市榜单。10个中国城市上榜。
据俄罗斯卫星网8月14日报道，第一名为奥地利首都维也纳。第二名为澳大利亚墨尔本。日本大阪位列三甲之列。前五名还有卡尔加里（加拿大）和悉尼（澳大利亚）。此外，前十名中还包括温哥华（加拿大）、东京（日本）、多伦多（加拿大）、哥本哈根（丹麦）和阿德莱德（澳大利亚）。
报道称，中国以下城市入围最宜居城市之列：香港、台北、苏州、北京、天津、上海、深圳、大连、广州、青岛。
​')
AA;
            $dsql->dsqlOper($sql2, "lastid");
        }
        die;
    }

    /**
     *
     */
    public function checkPayAmount(){
        return "ok";
    }

    /**
     * 支付
     * @return [type] [description]
     */
    public function pay(){
        global $dsql;

        $param = $this->param;
        $paytype = $param['paytype'];
        $ordernum = $param['ordernum'];

        if($ordernum && $paytype){
            $sql = $dsql->SetQuery("SELECT `amount` FROM `#@__member_reward` WHERE `ordernum` = '$ordernum' AND `module` = 'article' AND `state` = 0");
            $res = $dsql->dsqlOper($sql, "results");
            if($res){
                $amount = $res[0]['amount'];
                //跳转至第三方支付页面
                createPayForm("article", $ordernum, $amount, $paytype, "打赏文章");
                return;
            }
        }
        header("location:/404.html");
        die;
    }

    /**
     * 自媒体状态验证
     * back 目前只有 put、edit 中设为 check
     * vdata 当验证用户为子管理员时，返回自媒体号信息
     */
    public function selfmedia_verify($userid, $userinfo, $back = "", &$vdata = []){
        global $dsql;
        global $userLogin;

        if($back == ""){
            $userid = $userLogin->getMemberID();
            if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");
        }

        $sql = $dsql->SetQuery("SELECT `id`, `ac_name`, `ac_photo`, `state`, `editstate`, `editlog`, `type`, `ac_profile` FROM `#@__article_selfmedia` WHERE `userid` = $userid");
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            $res = array("id" => $res[0]['id'], "ac_name" => $res[0]['ac_name'], "ac_photo" => $res[0]['ac_photo'], "ac_profile" => $res[0]['ac_profile'], "state" => $res[0]['state'], "editstate" => $res[0]['editstate'], "editlog" => $res[0]['editlog'], "type" => $res[0]['type']);
        }else{
            $res = array("state" => -1);
        }
        if($back == "check"){
            // 没有入驻自媒体时验证是否是其它自媒体号的管理员
            if($res['state'] == -1){
                $sql = $dsql->SetQuery("SELECT s.`id`, s.`ac_name`, `ac_photo`, `ac_profile`, s.`state`, `type` FROM `#@__article_selfmedia_manager` m LEFT JOIN `#@__article_selfmedia` s ON s.`id` = m.`aid` WHERE m.`userid` = $userid");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $vdata = $ret[0];
                    return "ok";
                }else{
                    return array("state" => 200, "info" => "您还没有入驻自媒体");
                }
            }
            if($res['state'] == -1) return array("state" => 200, "info" => "您还没有入驻自媒体");
            if($res['state'] == 0) return array("state" => 200, "info" => "您的入驻自媒体申请正在审核中，请耐心等待");
            if($res['state'] == 2) return array("state" => 200, "info" => "您的入驻自媒体申请已被拒绝，请修改资料后重新提交");

            $vdata = $res;
            unset($vdata['editlog']);
            return "ok";
        }
        return $res;
    }

    /**
     *
     */
    public function get_selfmedia_err($name = "mb_name", $type = 2){
        $arr = array(
            "mb_name" => array(
                2 => "请填写媒体名称",
                3 => "请填写企业名称",
                4 => "请填写机构名称"
            ),
            "mb_license" => array(
                0 => "请上传营业执照或事业单位法人证书副本扫描件",
                4 => "请上传统一社会信用代码证书或事业单位法人证书扫描件",
            )
        );
        if(!isset($arr[$name])) return "请填写完整";
        if(!isset($arr[$name][$type])) return $arr[$name][0];
        return $arr[$name][$type];
    }

    /**
     *
     */
    public function gettypename($fun, $id){
        $list = $this->$fun();
        return $list[array_search($id, array_column($list, "id"))]['typename'];
    }

    /**
     *
     * 自媒体类型
     */
    public function selfmedia_type($byid = false)
    {
        $typeList = array(
            0 => array('id' => 1, 'typename' => '个人/自媒体', 'lower' => array()),
            1 => array('id' => 2, 'typename' => '媒体', 'lower' => array()),
            2 => array('id' => 3, 'typename' => '企业', 'lower' => array()),
            3 => array('id' => 4, 'typename' => '政府', 'lower' => array()),
            4 => array('id' => 5, 'typename' => '其他组织', 'lower' => array()),
        );
        if($byid){
            $list = array();
            foreach ($typeList as $key => $value) {
                $list[$value['id']] = $value['typename'];
            }
            return $list;
        }
        return $typeList;
    }

    /**
     *
     * 新闻媒体类型
     */
    public function selfmedia_type2()
    {
        $typeList = array(
            0 => array('id' => 1, 'typename' => '机构媒体', 'lower' => array(), "des" => "适合报刊杂志、电视台、电台、新闻网站等有国家新闻出版广电总局认可资质的媒体机构申请"),
            1 => array('id' => 2, 'typename' => '群媒体', 'lower' => array(), "des" => "适合以内容生产为主要产出的公司、创作团队申请"),
        );
        return $typeList;
    }

    /**
     *
     * 政府机构类型
     */
    public function selfmedia_type4()
    {
        $typeList = array(
            0 => array('id' => 1, 'typename' => '政府机关', 'lower' => array()),
            1 => array('id' => 2, 'typename' => '事业单位', 'lower' => array()),
        );
        return $typeList;
    }

    /**
     *
     * 政府机构级别
     */
    public function selfmedia_type42()
    {
        $typeList = array(
            0 => array('id' => 1, 'typename' => '国家级', 'lower' => array()),
            1 => array('id' => 2, 'typename' => '省部级', 'lower' => array()),
            2 => array('id' => 3, 'typename' => '厅局（地市）级', 'lower' => array()),
            3 => array('id' => 4, 'typename' => '县处级', 'lower' => array()),
        );
        return $typeList;
    }

    /**
     *
     * 自媒体领域
     */
    public function selfmedia_field()
    {
        global $dsql;
        $param = $this->param;
        $type = is_array($param) ? (int)$param['type'] : 0;
        $list = array();

        $where = $type ? " WHERE `type` = $type" : "";
        $archives = $dsql->SetQuery("SELECT * FROM `#@__article_selfmedia_field` $where ORDER BY `weight` ASC");
        $results = $dsql->dsqlOper($archives, "results");
        if ($results) {
            foreach ($results as $key => $value) {
                $list[$key]['id'] = $value['id'];
                $list[$key]['typename'] = $value['typename'];
                $list[$key]['lower'] = array();
            }
        }
        return $list;
    }

    /**
     * 自媒体可修改字段名
     */
    public function selfmedia_dict(){
        $typeList = array(
          'ac_name' => '自媒体名称',
          'ac_profile' => '自媒体介绍',
          'ac_photo' => '自媒体头像',
        );

        return $typeList;
    }

    /**
     *
     * 媒体专业资质
     */
    public function selfmedia_type2_license()
    {
        $typeList = array();
        $typeList[] = array('id' => 1, 'typename' => '《广播电视播出机构许可证》', 'lower' => array());
        $typeList[] = array('id' => 2, 'typename' => '《广播电视频道许可证》', 'lower' => array());
        $typeList[] = array('id' => 3, 'typename' => '《中华人民共和国报纸出版许可证》', 'lower' => array());
        $typeList[] = array('id' => 4, 'typename' => '《中华人民共和国期刊出版许可证》', 'lower' => array());
        $typeList[] = array('id' => 5, 'typename' => '《互联网新闻信息服务许可证》', 'lower' => array());
        $typeList[] = array('id' => 6, 'typename' => '《信息网络传播视听节目许可证》', 'lower' => array());
        $typeList[] = array('id' => 7, 'typename' => '《网络文化经营许可证》', 'lower' => array());
        $typeList[] = array('id' => 8, 'typename' => '《电子出版物出版许可证》', 'lower' => array());
        $typeList[] = array('id' => 9, 'typename' => '《中华人民共和国出版物经营许可证》', 'lower' => array());
        $typeList[] = array('id' => 10, 'typename' => '《中华人民共和国互联网出版许可证》', 'lower' => array());

        return $typeList;
    }
    /**
     * 自媒体入驻
     */
    public function selfmediaConfig(){
        global $dsql;
        global $userLogin;
        $userid = $userLogin->getMemberID();
        $userinfo = $userLogin->getMemberInfo();

        if($userid == -1) return array("state" => 200, "info" => "登陆超时，请重新登陆");

        $id = 0;
        $detail = array();

        $sql = $dsql->SetQuery("SELECT * FROM `#@__article_selfmedia` WHERE `userid` = $userid");
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            $detail = $res[0];
            $id = $detail['id'];
            if($detail['state'] == 0){
                return array("state" => 200, "info" => "您已经入驻自媒体，正在审核中，请耐心等待");
            }
        }

        $param                  = $this->param;
        $cityid                 = (int)$param['cityid'];
        $type                   = (int)$param['type'];
        $ac_name                = $param['ac_name'];
        $ac_profile             = $param['ac_profile'];
        $ac_field               = (int)$param['ac_field'];
        $ac_addrid              = (int)$param['ac_addrid'];
        $ac_photo               = $param['ac_photo'];
        $mb_type                = (int)$param['mb_type'];
        $mb_name                = $param['mb_name'];
        $mb_code                = $param['mb_code'];
        $mb_level               = (int)$param['mb_level'];
        $mb_license             = $param['mb_license'];
        $op_name                = $param['op_name'];
        $op_idcard              = $param['op_idcard'];
        $op_idcardfront         = $param['op_idcardfront'];
        $op_phone               = $param['op_phone'];
        $op_phone_verify        = $param['op_phone_verify'];
        $op_email               = $param['op_email'];
        $op_authorize           = $param['op_authorize'];
        $org_major_license_type = (int)$param['org_major_license_type'];
        $org_major_license      = $param['org_major_license'];
        $outer                  = $param['outer'];
        $prove                  = $param['prove'];
        $areaCode               = $param['areaCode'] ? $param['areaCode'] : "86";



        if(empty($type) || !in_array($type, array(1,2,3,4,5))) return array("state" => 200, "info" => "入驻类型错误");

        if(empty($ac_name)) return array("state" => 200, "info" => "请填写自媒体名称");
        if(empty($ac_profile)) return array("state" => 200, "info" => "请填写自媒体介绍");
        if(empty($ac_field)) return array("state" => 200, "info" => "请选择自媒体领域");
        if(empty($ac_addrid)) return array("state" => 200, "info" => "请选择自媒体所在地");
        if(empty($ac_photo)) return array("state" => 200, "info" => "请上传自媒体头像");

        if(empty($cityid)){
            $cityInfoArr = getPublicParentInfo(array('tab' => 'site_area', 'id' => $ac_addrid));
            $cityInfoArr = explode(',', $cityInfoArr);
            $cityid      = $cityInfoArr[0];
        }

        if($type != 1 && $type != 5){

            if(empty($mb_name)) return array("state" => 200, "info" => $this->get_selfmedia_err("mb_name", $type));
            // 媒体
            if($type == 2){
                if(empty($mb_type)) return array("state" => 200, "info" => "请选择媒体类型");
            }
            // 政府机构
            if($type == 4){
                if(empty($mb_level)) return array("state" => 200, "info" => "请选择机构级别");
                if(empty($mb_type)) return array("state" => 200, "info" => "请选择机构类型");
            }
            if(empty($mb_code)) return array("state" => 200, "info" => "请填写统一社会信用代码");
            if(empty($mb_license)) return array("state" => 200, "info" => $this->get_selfmedia_err("mb_license", $type));

        }
        if($type != 1){
            if(empty($op_name)) return array("state" => 200, "info" => "请填写运营者姓名");
            if(empty($op_idcard)) return array("state" => 200, "info" => "请填写运营者身份证号码");
            if(empty($op_idcardfront)) return array("state" => 200, "info" => "请上传运营者手持身份证照片");
        }

        if(empty($op_phone)) return array("state" => 200, "info" => "请填写运营者联系手机");
        // if($userinfo['phone'] != $op_phone || !$userinfo['phoneCheck']){
        //     if(empty($op_phone_verify)) return array("state" => 200, "info" => "请填写手机验证码");

        //     $ip = GetIP();

        //     //国际版需要验证区域码
        //     $cphone_ = $op_phone;
        //     $archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
        //     $results = $dsql->dsqlOper($archives, "results");
        //     if($results){
        //         $international = $results[0]['international'];
        //         if($international){
        //             $cphone_ = $areaCode.$op_phone;
        //         }
        //     }

        //     $sql_code = $dsql->SetQuery("SELECT * FROM `#@__site_messagelog` WHERE `type` = 'op_phone' AND `lei` = 'auth' AND `ip` = '$ip' AND `user` = '$cphone_' ORDER BY `id` DESC LIMIT 1");
        //     $res_code = $dsql->dsqlOper($sql_code, "results");
        //     if($res_code){
        //         $code = $res_code[0]['code'];
        //         if(strtolower($op_phone_verify) != $code){
        //             return array('state' =>200, 'info' => '验证码输入错误，请重试！');
        //         }
        //         $msgId = $res_code[0]['id'];
        //     }else{
        //         return array('state' =>200, 'info' => '验证码输入错误，请重试！');
        //     }

        // }
        if(empty($op_email)) return array("state" => 200, "info" => "请填写运营者联系邮箱");
        preg_match('/\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/', $op_email, $matchEmail);
        if(!$matchEmail) return array("state" => 200, "info" => "请填写正确的邮箱");

        if($type != 1){
            if(empty($op_authorize)) return array("state" => 200, "info" => "请上传机构授权书扫描件");
        }

        // 媒体
        if($type == 2){
            if(empty($org_major_license_type)) return array("state" => 200, "info" => "请选择资质类型");
            if(empty($org_major_license)) return array("state" => 200, "info" => "请上传资质证明扫描件");
        }


        $pubdate = time();

        if($id == 0){
            // 写入自媒体列表
            $state = 0; // 入驻
            $editstate = 1;
            $editlog = serialize(array());

            $sql = $dsql->SetQuery("INSERT INTO `#@__article_selfmedia`
                (
					`cityid`, `userid`, `type`, `ac_name`, `ac_profile`,
					`ac_field`, `ac_addrid`, `ac_photo`, `mb_name`, `mb_code`,
					`mb_level`, `mb_type`, `mb_license`, `op_name`, `op_idcard`,
					`op_idcardfront`, `op_phone`, `op_email`, `op_authorize`,
					`org_major_license_type`, `org_major_license`, `outer`, `prove`,
					`state`, `editstate`, `pubdate`, `editlog`
				)
                 VALUES
                (
					$cityid, '$userid', '$type', '$ac_name', '$ac_profile',
					'$ac_field', '$ac_addrid', '$ac_photo', '$mb_name', '$mb_code',
					'$mb_level', '$mb_type', '$mb_license', '$op_name', '$op_idcard',
					'$op_idcardfront', '$op_phone', '$op_email', '$op_authorize',
					'$org_major_license_type', '$org_major_license', '$outer', '$prove',
					'$state', '$editstate', '$pubdate', '$editlog'
				)
			");
            $aid = $dsql->dsqlOper($sql, "lastid");
            if(is_numeric($aid)){
                if(isset($msgId)){
                    $sql = $dsql->SetQuery("DELETE FROM `#@__site_messagelog` WHERE `id` = $msgId");
                    $dsql->dsqlOper($sql, "update");

                    if($userinfo['phoneCheck'] != 1 && $userinfo['phone'] == $op_phone){
                        $sql = $dsql->SetQuery("UPDATE `#@__member` SET `phone` = '$op_phone', `phoneCheck` = 1 WHERE `id` = $userid");
                        $dsql->dsqlOper($sql, "update");
                    }
                }
                return "入驻申请已提交，请耐心等待审核";
            }else{
                return array("state" => 200, "info" => "入驻失败，请稍后重试");
            }

        }else{

            if($detail['editstate'] == 0){
                return array("state" => 200, "info" => "您的资料修改申请审核中，请耐心等待");
            }

            $field = array('cityid', 'ac_name', 'ac_profile', 'ac_field', 'ac_addrid', 'ac_photo', 'mb_name', 'mb_code', 'mb_level', 'mb_type', 'mb_license', 'op_name', 'op_idcard', 'op_idcardfront', 'op_phone', 'op_email', 'op_authorize', 'org_major_license_type', 'org_major_license', 'outer', 'prove');

            $haschange = false;
            $data_change = array();
            foreach ($field as $key => $value) {
                if($detail[$value] != ${$value}){
                    $data_change[$value] = ${$value};
                    $haschange = true;
                }
            }

            if(!$haschange){
                return array("state" => 200, "info" => "信息没有变化");
            }

            if($detail['state'] != 1){

                $state = 0; // 入驻
                $editstate = 1;
                $editlog = serialize(array());

                $sql = $dsql->SetQuery("UPDATE `#@__article_selfmedia` SET `cityid` = $cityid, `ac_name` = '$ac_name', `ac_profile` = '$ac_profile', `ac_field` = $ac_field, `ac_addrid` = $ac_addrid, `ac_photo` = '$ac_photo', `mb_name` = '$mb_name', `mb_code` = '$mb_code', `mb_level` = $mb_level, `mb_type` = '$mb_type', `mb_license` = '$mb_license', `op_name` = '$op_name', `op_idcard` = '$op_idcard', `op_idcardfront` = '$op_idcardfront', `op_phone` = '$op_phone', `op_email` = '$op_email', `op_authorize` = '$op_authorize', `org_major_license_type` = $org_major_license_type, `org_major_license` = '$org_major_license', `outer` = '$outer', `prove` = '$prove', `state` = $state, `editstate` = $editstate, `editlog` = '$editlog' WHERE `id` = $id");

            // 已审核的不更新账号信息
            }else{
                $editstate = 0;
                $editlog = array(
                    'state' => 0,
                    'time' => $pubdate,
                    'data' => $data_change
                );
                $editlog = serialize($editlog);
                $sql = $dsql->SetQuery("UPDATE `#@__article_selfmedia` SET `editstate` = $editstate, `editlog` = '$editlog' WHERE `id` = $id");
            }

            $res = $dsql->dsqlOper($sql, "update");
            if($res == "ok"){
                return "提交成功";
            }else{
                echo $sql;die;
                return array("state" => 200, "info" => "提交失败");
            }

        }
    }

    /**
     * 更新自媒体信息
     */
    public function selfmedia_update(){
        global $dsql;
        global $userLogin;
        $userid = $userLogin->getMemberID();
        $userinfo = $userLogin->getMemberInfo();

        $verify = $this->selfmedia_verify($userid, $userinfo);
        if($verify['state'] == 200) return $verify;

        if($verify['state'] == -1) return array("state" => 200, "info" => "您还没有入驻自媒体");
        if($verify['state'] == 0) return array("state" => 200, "info" => "您的入驻申请正在审核中，请耐心等待");

        $param                  = $this->param;
        $group                  = $param['group'];

        if(empty($group)) $group = "ac";

        $editlog = $verify['editlog'] ? unserialize($verify['editlog']) : array();

        $config = $this->config();

        $up = "";
        $time = time();

        if($group == "ac"){
            if($verify['ac_state'] == 0) return array("state" => 200, "info" => "您的账号信息正在审核中，请在审核完成后再进行修改操作");

            if($editlog && $config['selfmediaEditLimit']){
                if(isset($editlog[$group])){
                    $lastInfo = $editlog[$group]['data'];
                    $cfgInfo = $config['selfmediaEditLimit'][$group];

                    // 设置了修改周期
                    if($cfgInfo['cycle']){
                        $waittime = $time - $lastInfo['lasttime'];
                        // 距离上次修改的数据在一个修改周期内
                        if($waittime < $cfgInfo['cycle']*3600*24){
                            $day = ceil($waittime / (3600*24));
                            if($lastInfo['refusecount'] >= $cfgInfo['max'] && $cfgInfo['max']){
                                return array("state" => 200, "info" => "抱歉，您在{$cfgInfo['cycle']}内被拒绝次数已达上限，请于{$day}天后再进行修改操作");
                            }
                        }
                    }

                }
            }

            $cityid                 = $param['cityid'];
            $ac_name                = $param['ac_name'];
            $ac_profile             = $param['ac_profile'];
            $ac_field               = (int)$param['ac_field'];
            $ac_addrid              = (int)$param['ac_addrid'];
            $ac_photo               = $param['ac_photo'];

            if(empty($ac_name)) return array("state" => 200, "info" => "请填写自媒体名称");
            if(empty($ac_profile)) return array("state" => 200, "info" => "请填写自媒体介绍");
            if(empty($ac_field)) return array("state" => 200, "info" => "请选择自媒体领域");
            if(empty($ac_addrid)) return array("state" => 200, "info" => "请选择自媒体所在地");
            if(empty($ac_photo)) return array("state" => 200, "info" => "请上传自媒体头像");
            if(empty($cityid)){
                $cityInfoArr = getPublicParentInfo(array('tab' => 'site_area', 'id' => $ac_addrid));
                $cityInfoArr = explode(',', $cityInfoArr);
                $cityid      = $cityInfoArr[0];
            }

            if($verify['state'] != 1){
                $up = ", `cityid` = $ac_addrid, `state` = 0, `ac_name` = '$ac_name', `ac_profile` = '$ac_profile', `ac_field` = '$ac_field', `ac_addrid` = '$ac_addrid', `ac_photo` = '$ac_photo'";
            }else{
                $up = ", `ac_state` = 0";
            }
            $log = array(
                "time" => $time,
                "value" => array(
                    "ac_name" => $ac_name,
                    "ac_profile" => $ac_profile,
                    "ac_field" => $ac_field,
                    "ac_addrid" => $ac_addrid,
                    "ac_photo" => $ac_photo,
                ),
                "res" => array(
                    "state" => 0,
                    "note" => ""
                )
            );

        }elseif($group == "op"){
            if($verify['ac_state'] == 0) return array("state" => 200, "info" => "您的运营者信息正在审核中，请在审核完成后再进行修改操作");

            $op_name                = $param['op_name'];
            $op_idcard              = $param['op_idcard'];
            $op_idcardfront         = $param['op_idcardfront'];
            $op_phone               = $param['op_phone'];
            $op_phone_verify        = $param['op_phone_verify'];
            $op_email               = $param['op_email'];
            $op_authorize           = $param['op_authorize'];

            if($type != 1){
                if(empty($op_name)) return array("state" => 200, "info" => "请填写运营者姓名");
                if(empty($op_idcard)) return array("state" => 200, "info" => "请填写运营者身份证号码");
                if(empty($op_idcardfront)) return array("state" => 200, "info" => "运营者手持身份证照片");
            }

            if(empty($op_phone)) return array("state" => 200, "info" => "请填写联系手机");
            if($userinfo['phone'] != $op_phone || !$userinfo['phoneCheck']){
                if(empty($op_phone_verify)) return array("state" => 200, "info" => "请填写手机验证码");

                $ip = GetIP();

                //国际版需要验证区域码
                $cphone_ = $op_phone;
                $archives = $dsql->SetQuery("SELECT `international` FROM `#@__sitesms` WHERE `state` = 1");
                $results = $dsql->dsqlOper($archives, "results");
                if($results){
                    $international = $results[0]['international'];
                    if($international){
                        $cphone_ = $areaCode.$op_phone;
                    }
                }

                $sql_code = $dsql->SetQuery("SELECT * FROM `#@__site_messagelog` WHERE `type` = 'op_phone' AND `lei` = 'auth' AND `ip` = '$ip' AND `user` = '$cphone_' ORDER BY `id` DESC LIMIT 1");
                $res_code = $dsql->dsqlOper($sql_code, "results");
                if($res_code){
                    $code = $res_code[0]['code'];
                    if(strtolower($op_phone_verify) != $code){
                        return array('state' =>200, 'info' => '验证码输入错误，请重试！');
                    }
                    $msgId = $res_code[0]['id'];
                }else{
                    return array('state' =>200, 'info' => '验证码输入错误，请重试！');
                }

            }
            if(empty($op_email)) return array("state" => 200, "info" => "请填写联系邮箱");
            preg_match('/\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/', $op_email, $matchEmail);
            if(!$matchEmail) return array("state" => 200, "info" => "请填写正确的邮箱");

            if($type != 1){
                if(empty($op_authorize)) return array("state" => 200, "info" => "请上传机构授权书扫描件");
            }

            if($verify['state'] != 1){
                $up = ", `state` = 0, `op_name` = '$op_name', `op_idcard` = '$op_idcard', `op_idcardfront` = '$op_idcardfront', `op_phone` = '$op_phone', `op_email` = '$op_email', `op_authorize` = '$op_authorize'";
            }else{
                $up = ", `op_state` = 0";
            }
            $log = array(
                "time" => $time,
                "value" => array(
                    "op_name" => $op_name,
                    "op_idcard" => $op_idcard,
                    "op_idcardfront" => $op_idcardfront,
                    "op_phone" => $op_phone,
                    "op_email" => $op_email,
                    "op_authorize" => $op_authorize,
                ),
                "res" => array(
                    "state" => 0,
                    "note" => ""
                )
            );

        }else{
            return array("state" => 200, "info" => "参数错误");
        }

        if(empty($editlog)){
            $editlog[$group] = array(
                'data' => array(
                    'lasttime' => $time,
                    'refusecount' => 0,
                ),
                'list' => array()
            );
        }
        array_unshift($editlog[$group]['list'], $log);
        // print_r($editlog);
        $editlog = serialize($editlog);

        $sql = $dsql->SetQuery("UPDATE `#@__article_selfmedia` SET `editlog` = '$editlog' {$up} WHERE `id` = {$verify['id']}");
        $res = $dsql->dsqlOper($sql, "update");
        if($res == "ok"){
            if($verify['state'] == 1 && $group == "ac"){
                return "更新成功，审核通过前将继续使用当前账号信息";
            }
            return "更新成功";
        }else{
            return array("state" => 200, "info" => "更新失败，请稍后重试！");
        }

    }

    /**
     * 自媒体列表
     */
    public function selfmedia(){
        global $dsql;
        global $userLogin;

        $param    = $this->param;
        $type     = (int)$param['type'];
        $title    = $param['title'];
        $ac_field = (int)$param['ac_field'];
        $page     = (int)$param['page'];
        $pageSize = (int)$param['pageSize'];
        $orderby  = $param['orderby'];
        $isAjax   = (int)$param['isAjax'];

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;
        $totalCount = 0;

        $where = $order = "";
        $list = array();

        $loginUid = $this->param['from'] ? $this->param['from'] : $userLogin->getMemberID();

        if($type){
            $where .= " AND `type` = $type";
        }
        if($ac_field){
            $where .= " AND `ac_field` = $ac_field";
        }
        if($title){
            $where .= " AND (`ac_name` like '%$title%' or `ac_profile` like '%$title%' or `ac_field` like '%$title%')";
        }
        $archives = $dsql->SetQuery("SELECT COUNT(`id`) total FROM `#@__article_selfmedia` WHERE `state` = 1".$where);
        $res = $dsql->dsqlOper($archives, "results");
        $totalCount = $res[0]['total'];

        if($totalCount == 0) return array("state" => 200, "info" => "暂无数据");

        $totalPage = ceil($totalCount / $pageSize);

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $order = " ORDER BY `weight` DESC, `id` DESC";
        if($orderby == "time"){
            $order = " ORDER BY `pubdate` DESC";
        }

        $archives = $dsql->SetQuery("SELECT `id`, `userid`, `type`, `ac_name`, `ac_profile`, `ac_field`, `ac_photo`, `pubdate`, `click` FROM `#@__article_selfmedia` WHERE `state` = 1".$where);
        $atpage = $pageSize*($page-1);
        $where_limit = " LIMIT $atpage, $pageSize";
        $sql = $dsql->SetQuery($archives.$order.$where_limit);
        // $results = $dsql->dsqlOper($dsql->SetQuery($archives.$order.$where_limit), "results");

        $results = getCache("article_media_list", $sql, 3600);

        if($results){
            $typeList = $this->selfmedia_type(true);

            foreach ($results as $key => $value) {
                $list[$key]['id']        = $value['id'];
                // $list[$key]['userid'] = $value['userid'];
                $list[$key]['typeid']    = $value['type'];
                $list[$key]['type']      = $typeList[$value['type']];
                $list[$key]['name']      = $value['ac_name'];
                $list[$key]['profile']   = $value['ac_profile'];
                $list[$key]['field']     = $value['ac_field'];
                $list[$key]['photo']     = $value['ac_photo'] ? getFilePath($value['ac_photo']) : "";
                $list[$key]['pubdate']   = $value['pubdate'];
                $list[$key]['click']     = $value['click'];


                // 统计文章
                $sql = $dsql->SetQuery("SELECT COUNT(`id`) c FROM `#@__articlelist` WHERE `admin` = ".$value['userid']." AND `arcrank` = 1 AND `waitpay` = 0 AND `del` = 0");
                // $res = $dsql->dsqlOper($sql, "results");
                // $list[$key]['total_article'] = $res[0]['c'];
                $list[$key]['total_article'] = getCache("article_media_arc", $sql, 3600, array("sign" => $value['id'], "name" => "c"));

                // 统计粉丝
                $sql = $dsql->SetQuery("SELECT COUNT(`id`) c FROM `#@__member_follow` WHERE `fid` = ".$value['id']." AND `for` = 'media'");
                // $res = $dsql->dsqlOper($sql, "results");
                // $list[$key]['total_fans'] = $res[0]['c'];
                $list[$key]['total_fans'] = getCache("article_media_fans", $sql, 3600, array("sign" => $value['id'], "name" => "c"));


                $list[$key]['url'] = getUrlPath(array("service" => "article", "template" => "mddetail", "id" => $value['id']));

                //是否相互关注
                if($loginUid > -1){
                    if($loginUid == $value['userid']){
                        $list[$key]['isfollow'] = 2;//自己
                    }else{
                        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `for` = 'media' AND `tid` = $loginUid AND `fid` = " . $value['id']);
                        $ret = $dsql->dsqlOper($sql, "results");
                        if($ret){
                            $list[$key]['isfollow'] = 1;//关注
                        }else{
                            $list[$key]['isfollow'] = 0;//未关注
                        }
                    }
                }else{
                    $list[$key]['isfollow'] = 0;//未关注
                }
            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);
    }

    /**
     * 新闻类型
     */
    public function get_article_mold(){
        $typeList = array(
            0 => array("id" => 0, "typename" => "头条", "lower" => array()),
            1 => array("id" => 1, "typename" => "图集", "lower" => array()),
            2 => array("id" => 2, "typename" => "视频", "lower" => array()),
            3 => array("id" => 3, "typename" => "短视频", "lower" => array())
        );
        return $typeList;
    }

    /**
     * 自媒体详情
     */
    public function selfmediaDetail($uid = 0, $aid = 0){
        global $dsql;
        global $userLogin;
        global $service;
        $param = $this->param;

        $loginUid = $userLogin->getMemberID();

        $uid = $uid ? $uid : (is_array($param) ? (int)$param['uid'] : 0);
        $aid = $aid ? $aid : (is_array($param) ? (int)$param['aid'] : 0);



        $data = array();

        $where = "";
        if(empty($uid) && $aid){
            $where = " `id` = $aid";
        }elseif($uid){
            $check = $this->selfmedia_verify($uid, "", "check", $mediaDetail);
			if($check != 'ok'){
				return $data;
			}
            $aid = $mediaDetail['id'];
        }else{
            return $data;
        }
        $where = " `id` = $aid";
        $sign = "id_".$aid;

        $sql = $dsql->SetQuery("SELECT * FROM `#@__article_selfmedia` WHERE".$where);
        $res = $dsql->dsqlOper($sql, "results");
        if($res){

            $d = $res[0];
            if($loginUid == $res[0]['userid']){
                if($res[0]['state'] == 1 && $res[0]['editstate'] == 0 && $res[0]['editlog']){
                    $editdata = unserialize($res[0]['editlog']);
                    $editdata = $editdata['data'];
                    $res[0] = array_merge($res[0], $editdata);
                }
            }

            $data['id']      = $res[0]['id'];
            $data['userid']  = $res[0]['userid'];
            $data['type']    = $res[0]['type'];
            $data['cityid']  = $res[0]['cityid'];
            $data['ac_name']    = $res[0]['ac_name'];
            $data['ac_profile'] = $res[0]['ac_profile'];
            $data['ac_photo']   = $res[0]['ac_photo'] ? getFilePath($res[0]['ac_photo']) : "";
            $data['ac_addrid'] = $res[0]['ac_addrid'];
            $data['ac_field'] = $res[0]['ac_field'];
            $data['click']    = (int)$res[0]['click'];
            $data['ac_fieldname'] = $this->gettypename("selfmedia_field", $res[0]['ac_field']);
            $data['url']     = getUrlPath(array("service" => "article", "template" => "mddetail", "id" => $res[0]['id']));

            $mb_type = $res[0]['mb_type'];
            $mb_level = 0;
            $mb_levelname = "";
            if($res[0]['type'] == 2){
                $mb_typename = $this->gettypename("selfmedia_type2", $res[0]['mb_type']);
            }elseif($res[0]['type'] == 4){
                $mb_level = $res[0]['mb_level'];
                $mb_typename = $this->gettypename("selfmedia_type4", $res[0]['mb_type']);
                $mb_levelname = $this->gettypename("selfmedia_type42", $res[0]['mb_type']);
            }else{
                $mb_type = 0;
                $mb_typename = "";
            }
            $data['mb_type'] = $mb_type;
            $data['mb_typename'] = $mb_typename;
            $data['mb_level'] = $mb_level;
			$data['mb_levelname'] = $mb_levelname;

            if(!empty($res[0]['ac_banner'])){
              $imglist = array();
              $ac_banner = explode(',', $res[0]['ac_banner']);
              foreach($ac_banner as $key => $value){
                $info = explode("|", $value);
                $imglist[$key]["path"] = getFilePath($info[0]);
                $imglist[$key]["pathSource"] = $info[0];
                $imglist[$key]["info"] = $info[1];
              }
              // $imglist = json_encode($imglist);
            }else{
              $imglist = array();
            }
            $data['ac_banner'] = $res[0]['ac_banner'];
            $data['ac_bannerList'] = $imglist;

			//是否相互关注
            if($loginUid > -1){
                if($loginUid == $res[0]['userid']){
                    $data['isfollow'] = 2;//自己
                }else{
        			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `for` = 'media' AND `tid` = $loginUid AND `fid` = " . $res[0]['id']);
        			$ret = $dsql->dsqlOper($sql, "results");
        			if($ret){
        				$data['isfollow'] = 1;//关注
        			}else{
        				$data['isfollow'] = 0;//未关注
        			}
                }
            }else{
                $data['isfollow'] = 0;//未关注
            }

            // 统计文章
            $sql = $dsql->SetQuery("SELECT COUNT(`id`) c FROM `#@__articlelist_all` WHERE `admin` = ".$d['userid']." AND `arcrank` = 1 AND `waitpay` = 0 AND `del` = 0 AND 1 = 1");
            // $ret = $dsql->dsqlOper($sql, "results");
            // $data['total_article'] = $ret[0]['c'];
            $data['total_article'] = getCache("article_media_arc", $sql, 3600, array("sign" => $d['userid'], "name" => "c"));

            // 统计粉丝
            $sql = $dsql->SetQuery("SELECT COUNT(`id`) c FROM `#@__member_follow` WHERE `fid` = ".$d['id']." AND `for` = 'media'");
            // $ret = $dsql->dsqlOper($sql, "results");
            // $data['total_fans'] = $ret[0]['c'];
            $data['total_fans'] = getCache("article_media_fans", $sql, 3600, array("sign" => $d['userid'], "name" => "c"));

			$data['state'] = $res[0]['state'];

            // 会员中心
            if($loginUid == $res[0]['userid']){

                $data['state']                      = $res[0]['state'];
                $data['ac_photoSource']             = $res[0]['ac_photo'];
                $data['mb_name']                    = $res[0]['mb_name'];
                $data['mb_code']                    = $res[0]['mb_code'];
                $data['mb_license']                 = $res[0]['mb_license'] ? getFilePath($res[0]['mb_license']) : "";
                $data['mb_licenseSource']           = $res[0]['mb_license'];
                $data['op_name']                    = $res[0]['op_name'];
                $data['op_phone']                   = $res[0]['op_phone'];
                $data['op_email']                   = $res[0]['op_email'];
                $data['op_idcard']                  = $res[0]['op_idcard'];
                $data['op_idcardfront']             = $res[0]['op_idcardfront'] ? getFilePath($res[0]['op_idcardfront']) : "";
                $data['op_idcardfrontSource']       = $res[0]['op_idcardfront'];
                $data['org_major_license_type']     = $res[0]['org_major_license_type'];
                $data['org_major_license_typename'] = $this->gettypename("selfmedia_type2_license", $res[0]['org_major_license_type']);
                $data['org_major_license']          = $res[0]['org_major_license'] ? getFilePath($res[0]['org_major_license']) : "";
                $data['org_major_licenseSource']    = $res[0]['org_major_license'];
                $data['outer']                      = $res[0]['outer'];
                $data['editstate']                  = $res[0]['editstate'];

                if(!empty($res[0]['prove'])){
                  $imglist = array();
                  $prove = explode(',', $res[0]['prove']);
                  foreach($prove as $key => $value){
                    $info = explode("|", $value);
                    $imglist[$key]["path"] = getFilePath($info[0]);
                    $imglist[$key]["pathSource"] = $info[0];
                    $imglist[$key]["info"] = $info[1];
                  }
                  // $imglist = json_encode($imglist);
                }else{
                  $imglist = array();
                }
                $data['prove'] = $res[0]['prove'];
                $data['proveList'] = $imglist;
                $data['op_authorize'] = getFilePath($res[0]['op_authorize']);
                $data['op_authorizeSource'] = $res[0]['op_authorize'];
                // if(!empty($res[0]['op_authorize'])){
                //   $imglist = array();
                //   $op_authorize = explode(',', $res[0]['op_authorize']);
                //   foreach($op_authorize as $key => $value){
                //     $info = explode("|", $value);
                //     $imglist[$key]["path"] = getFilePath($info[0]);
                //     $imglist[$key]["pathSource"] = $info[0];
                //     $imglist[$key]["info"] = $info[1];
                //   }
                //   // $imglist = json_encode($imglist);
                // }else{
                //   $imglist = array();
                // }
                // $data['op_authorizeList'] = $imglist;
            }
        }
        // print_r($data);
        return $data;
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

        $sql = $dsql->SetQuery("SELECT * FROM `#@__articlecommon` WHERE `id` = $id AND `isCheck` = 1 ");//print_R($sql);exit;
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
                        $sql = $dsql->SetQuery("SELECT `content`, `userid` FROM `#@__articlecommon` WHERE `id` = '$value' AND `isCheck` = 1 ");
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
	 * 获取下一条信息内容
     * @return array
	 */
	public function nextData(){
		global $dsql;
		global $userLogin;
		$articleDetail = array();
		$id = $this->param['id'];
        if(!is_numeric($id)) return array("state" => 200, "info" => '格式错误！');

        $sub = new SubTable('article', '#@__articlelist');

        $breakup_table_res = $sub->getSubTable();

        $break_table = $sub->getSubTableById($id);

        $where = "";
        $cityid = getCityId();
        if($cityid){
            $where = " AND `cityid` = $cityid";
        }

		$sql = $dsql->SetQuery("select `id` from `".$break_table."` where `id` = (select max(`id`) from `".$break_table."` where `arcrank` = 1 AND `media_state` = 1 AND `mold` = 1 AND `waitpay` = 0 AND `del` = 0 $where AND `id` < $id)");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$this->param = $ret[0]['id'];
			$data = $this->detail();
			return $data;
		}

	}

    /**
     * 自媒体账号管理员添加子管理员
     */
    public function opearMediaChildManager(){
        global $dsql;
        global $userLogin;
        $param = $this->param;
        $uid  = (int)$param['uid'];
        $mid  = (int)$param['mid'];
        $type = $param['type'];

        if(empty($uid) || empty($uid) || empty($type)) return array("state" => 200, "info" => "参数错误");

        $userid = $userLogin->getMemberID();
        if($userid <= 0) return array("state" => 200, "info" => "登陆超时，请重新登陆");

        $sql = $dsql->SetQuery("SELECT `id`, `state` FROM `#@__article_selfmedia` WHERE `userid` = $userid AND `id` = $mid");
        $res = $dsql->dsqlOper($sql, "results");
        if(!$res) return array("state" => 200, "info" => "权限错误");
        if($res[0]['state'] != 1) return array("state" => 200, "info" => "您的自媒体账号状态异常，无法进行此操作");

        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__article_selfmedia_manager` WHERE `aid` = $mid AND `userid` = $uid");
        $check = $dsql->dsqlOper($sql, "results");

        if($type == "add"){
            if($check) return array("state" => 200, "info" => "该用户已经是管理员");
            $now = time();
            $sql = $dsql->SetQuery("INSERT INTO `#@__article_selfmedia_manager` (`aid`, `userid`, `pubdate`) VALUES ($mid, $uid, $now)");
        }elseif($type == "del"){
            if(!$check) return array("state" => 200, "info" => "管理员不存在");
            $sql = $dsql->SetQuery("DELETE FROM `#@__article_selfmedia_manager` WHERE `aid` = $mid AND `userid` = $uid");
        }
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            return "操作成功";
        }else{
            return array("state" => 200, "info" => "操作失败");
        }

    }

    /**
     * 媒体号栏目
     */
    public function mediaArcType(){
        global $dsql;
        $id = (int)$this->param['id'];
        if(empty($id)) return array("state" => 200, "info" => "参数错误");

        $sql = $dsql->SetQuery("SELECT * FROM `#@__article_selfmedia_arctype` WHERE `aid` = $id ORDER BY `weight`, `id`");
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            foreach ($res as $key => $value) {
                $res[$key]['iconSource'] = $value['icon'];
                $res[$key]['icon'] = getFilePath($value['icon']);
            }
        }

        return $res;
    }

    /**
     * 专题列表
     */
    public function zhuantiList(){
        global $dsql;
        $param    = $this->param;
        $r        = $param['r'];
        $h        = $param['h'];
        $get_news = (int)$param['get_news'];
        $get_lower = (int)$param['get_lower'];
        $thumb    = (int)$param['thumb'];
        $typeid    = (int)$param['typeid'];
        $orderby  = $param['orderby'];
        $page     = (int)$param['page'];
        $pageSize = (int)$param['pageSize'];

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $where = "";

        if($typeid){
            $where .= " AND `typeid` = $typeid";
        }
        if($r){
            $where .= (int)$r ? " AND `flag_r` = $r" : " AND `flag_r` > 0";
        }
        if($h){
            $where .= (int)$h ? " AND `flag_h` = $h" : " AND `flag_h` > 0";
        }

        $sql = $dsql->SetQuery("SELECT COUNT(`id`) c FROM `#@__article_zhuanti` WHERE `state` = 1 AND `parentid` = 0".$where);
        $res = $dsql->dsqlOper($sql, "results");
        $totalCount = $res[0]['c'];

        if($totalCount == 0) return array("state" => 200, "info" => "暂无数据");
        $totalPage = ceil($totalCount / $pageSize);
        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $order = " ORDER BY `weight` DESC, `id` DESC";

        if($orderby == "time"){
            $order = " ORDER BY `pubdate` DESC, `id` DESC";
        }elseif($orderby == "click"){
            $order = " ORDER BY `click` DESC, `pubdate` DESC";
        }
        $atpage = ($page - 1) * $pageSize;
        $limit = " LIMIT $atpage, $pageSize";
        $list = array();
        $archives = $dsql->SetQuery("SELECT * FROM `#@__article_zhuanti` WHERE `state` = 1 AND `parentid` = 0".$where.$order.$limit);
        $results = $dsql->dsqlOper($archives, "results");
        if($results){
            foreach ($results as $key => $value) {
                $list[$key] = array(
                    'id' => $value['id'],
                    'typename' => $value['typename'],
                    'description' => $value['description'],
                    'pubdate' => $value['pubdate'],
                    'click' => $value['click'] >= 10000 ? sprintf("%.1f", $value['click']/10000)."万" : $value['click'],
                    'flag_r' => (int)$value['flag_r'],
                    'flag_h' => (int)$value['flag_h'],
                    'litpic' => getFilePath($value['litpic']),
                    'banner_large' => getFilePath($value['banner_large']),
                    'banner_small' => getFilePath($value['banner_small']),
                    'url' => getUrlPath(array('service' => 'article', 'template' => 'zt_detail', 'id' => $value['id'])),
                );
                if($get_news){
                    $this->param = array('zhuanti' => $value['id'], "thumb" => $thumb);
                    $news = $this->zhuantiNews();
                    if($news['state'] == 200){
                        $news = array('list' => array());
                    }
                    $list[$key]['list'] = $news;
                }
                if($get_lower){
                    $sql = $dsql->SetQuery("SELECT `id`, `typename` FROM `#@__article_zhuanti` WHERE `parentid` = ".$value['id']);
                    $res = $dsql->dsqlOper($sql, "results");
                    $list[$key]['lower'] = $res;
                }
            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);
    }

    /**
     * 专题详情及子分类
     */
    public function zhuantiDetail(){
        global $dsql;
        $param    = $this->param;
        $id     = (int)$param['id'];
        if(empty($id)) return array("state" => 200, "info" => "参数错误");

        $archives = $dsql->SetQuery("SELECT * FROM `#@__article_zhuanti` WHERE `id` = $id AND `state` = 1 AND `parentid` = 0");
        $results = $dsql->dsqlOper($archives, "results");
        if($results){
            $detail = array();
            foreach ($results as $key => $value) {
                // 子分类
                $sql = $dsql->SetQuery("SELECT * FROM `#@__article_zhuanti` WHERE `parentid` = $id AND `state` = 1");
                $type = $dsql->dsqlOper($sql, "results");
                $detail = array(
                    'id' => $value['id'],
                    'typename' => $value['typename'],
                    'description' => $value['description'],
                    'pubdate' => $value['pubdate'],
                    'litpic' => getFilePath($value['litpic']),
                    'banner_large' => getFilePath($value['banner_large']),
                    'banner_small' => getFilePath($value['banner_small']),
                    'url' => getUrlPath(array('service' => 'article', 'template' => 'zt_detail', 'id' => $id)),
                    'lower' => $type
                );
            }
            return $detail;
        }
    }

    /**
     * 专题文章列表
     */
    public function zhuantiNews(){
        global $dsql;
        $param    = $this->param;
        $thumb    = (int)$param['thumb'];
        $typeid   = (int)$param['typeid'];
        $zhuanti  = (int)$param['zhuanti'];
        $orderby  = (int)$param['orderby'];
        $page     = (int)$param['page'];
        $pageSize = (int)$param['pageSize'];

        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page     = empty($page) ? 1 : $page;

        $where = " z.`state` = 1 AND a.`arcrank` = 1 AND a.`media_state` = 1 AND a.`del` = 0";

        $zhuanti = $zhuanti ? $zhuanti : $typeid;
        if($zhuanti){
            $arr = $dsql->getTypeList($zhuanti, "article_zhuanti");
            if($arr){
                global $arr_data;
                $arr_data = array();
                $lower = arr_foreach($arr);
                $lower = $zhuanti.",".join(',',$lower);
            }else{
                $lower = $zhuanti;
            }
            $where .= " AND l.`typeid` in ($lower)";
        }

        if($thumb){
            $where .= " AND a.`litpic` <> ''";
        }

        $sql = $dsql->SetQuery("SELECT COUNT(l.`id`) c FROM `#@__article_zhuantilist` l LEFT JOIN `#@__article_zhuanti` z ON z.`id` = l.`typeid` LEFT JOIN `#@__articlelist_all` a ON a.`id` = l.`aid` WHERE ".$where);
        $res = $dsql->dsqlOper($sql, "results");
        $totalCount = $res[0]['c'];
        if($totalCount == 0) return array("state" => 200, "info" => "暂无数据");
        $totalPage = ceil($totalCount / $pageSize);

        $pageinfo = array(
            "page" => $page,
            "pageSize" => $pageSize,
            "totalPage" => $totalPage,
            "totalCount" => $totalCount
        );

        $order = " ORDER BY a.`weight` DESC, a.`id` DESC";
        $atpage = $pageSize*($page-1);
        $limit = " LIMIT $atpage, $pageSize";

        $list = array();
        $archives = $dsql->SetQuery("SELECT l.*, z.`typename`, a.`id` aid, a.`title`, a.`litpic`, a.`flag`, a.`redirecturl`, a.`typeid` atypeid, a.`source` FROM `#@__article_zhuantilist` l LEFT JOIN `#@__article_zhuanti` z ON z.`id` = l.`typeid` LEFT JOIN `#@__articlelist_all` a ON a.`id` = l.`aid` WHERE ".$where.$order.$limit);
        $results = $dsql->dsqlOper($archives, "results");
        if($results){
            foreach ($results as $key => $value) {

                $param = array(
                    "service"     => "article",
                    "template"    => "detail",
                    "id"          => $value['aid'],
                    "flag"        => $value['flag'],
                    "redirecturl" => $value['redirecturl'],
                    "typeid"      => $value['atypeid']
                );
                $url = getUrlPath($param);
                $list[$key] = array(
                    'id' => $value['aid'],
                    'typename' => $value['typename'],
                    'title' => $value['title'],
                    'source' => $value['source'],
                    'litpic' => getFilePath($value['litpic']),
                    'url' => $url
                );
            }
        }
        return array("pageInfo" => $pageinfo, "list" => $list);
    }

    /**
     * 专题分类
     */
    public function zhuantiType(){
        global $dsql;
        $sql = $dsql->SetQuery("SELECT * FROM `#@__article_zhuantipar` ORDER BY `weight`");
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
            $url = getUrlPath(array("service" => "article", "template" => "zt_list", "param" => "typeid=url"));
            foreach ($res as $key => $value) {
                $res[$key]['url'] = str_replace("url", $value['id'], $url);
            }
        }
        return $res;
    }

}
