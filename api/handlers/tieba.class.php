<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 贴吧API接口
 *
 * @version        $Id: tieba.class.php 2016-11-17 上午11:16:22 $
 * @package        HuoNiao.Handlers
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class tieba {
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

		require(HUONIAOINC."/config/tieba.inc.php");

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

		// $domainInfo = getDomain('tieba', 'config');
		// $customChannelDomain = $domainInfo['domain'];
		// if($customSubDomain == 0){
		// 	$customChannelDomain = "http://".$customChannelDomain;
		// }elseif($customSubDomain == 1){
		// 	$customChannelDomain = "http://".$customChannelDomain.".".$cfg_basehost;
		// }elseif($customSubDomain == 2){
		// 	$customChannelDomain = "http://".$cfg_basehost."/".$customChannelDomain;
		// }

		// include HUONIAOINC.'/siteModuleDomain.inc.php';
		$customChannelDomain = getDomainFullUrl('tieba', $customSubDomain);

        //分站自定义配置
        $ser = 'tieba';
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
     * 贴吧分类
     * @return array
     */
	public function type(){
		global $dsql;
		$type = $page = $pageSize = $where = "";

		$cityid = getCityId();

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
		// $results = $dsql->getTypeList($type, "tieba_type", $son, $page, $pageSize);
        $results = getCache("tieba_type_" . $cityid, function() use($dsql, $type, $son, $page, $pageSize){
            return $dsql->getTypeList($type, "tieba_type", $son, $page, $pageSize);
        }, 0, array("sign" => $type."_".(int)$son, "savekey" => 1));
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
     * 帖子列表
     * @return array
     */
	public function tlist(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$typeid = $keywords = $orderby = $u = $uid = $state = $page = $pageSize = $where = $where1 = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$typeid   = $this->param['typeid'];
				$keywords = $this->param['keywords'];
				$name     = $this->param['username'];
				$orderby  = $this->param['orderby'];
				$u        = $this->param['u'];
				$uid      = $this->param['uid'];
				$state    = $this->param['state'];
				$ispic    = $this->param['ispic'];
				$istop    = $this->param['istop'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$cityid = getCityId($this->param['cityid']);
		if($cityid && $u != 1){
			$where .= " AND `cityid` = ".$cityid;
		}else{
			$where .= " AND `cityid` != 0";
		}

		if(!empty($istop)){
			$where .=" AND `top` = 1";
		}

		$userid = $userLogin->getMemberID();

		//是否输出当前登录会员的信息
		if($u != 1){
			$where .= " AND l.`state` = 1 AND l.`waitpay` = 0";

			//取指定会员的信息
			if($uid){
				$where .= " AND l.`uid` = $uid";
			}
		}else{
			$where .= " AND l.`uid` = ".$userid;
			if($state != ""){
				$where1 = " AND l.`state` = ".$state;
			}
		}

		//遍历分类
		if(!empty($typeid)){
			if($dsql->getTypeList($typeid, "tieba_type")){
				global $arr_data;
				$arr_data = array();
				$lower = arr_foreach($dsql->getTypeList($typeid, "tieba_type"));
				$lower = $typeid.",".join(',',$lower);
			}else{
				$lower = $typeid;
			}
			$where .= " AND `typeid` in ($lower)";
		}


		//模糊查询关键字
		if(!empty($keywords)){

			//搜索记录
			siteSearchLog("tieba", $keywords);

			$keywords = explode(" ", $keywords);
			$w = array();
			foreach ($keywords as $k => $v) {
				if(!empty($v)){
					$w[] = "`title` like '%".$v."%'";
				}
			}
			$where .= " AND (".join(" OR ", $w).")";
		}

		if(!empty($name)){
			//搜索记录
			siteSearchLog("tieba", $name);
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member` WHERE `username` like '%$name%' or `nickname` like '%$name%' or `company` like '%$name%'");
			$retname = $dsql->dsqlOper($sql, "results");
			if(!empty($retname) && is_array($retname)){
				$list_name = array();
				foreach ($retname as $key => $value) {
					$list_name[] = $value["id"];
				}
				$idList = join(",", $list_name);
				$where .= " AND  l.`uid` in ($idList) ";
			}
		}

		//1、视频 2、图片 3、音频
		if($ispic == 1){
			$where .= " AND l.content like '%img%'";
		}elseif($ispic == 2){
			$where .= " AND l.content like '%video%'";
		}elseif($ispic == 3){
			$where .= " AND l.content like '%audio%' ";
		}

		$order = " ORDER BY `top` DESC, `jinghua` DESC, `weight` DESC, `id` DESC";

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		//评论排行
		if($orderby == "reply"){
			$order = " ORDER BY reply DESC, `top` DESC, `jinghua` DESC, `weight` DESC, `id` DESC";
		}elseif($orderby == "pubdate"){
			$order = " ORDER BY pubdate DESC, `top` DESC, `jinghua` DESC, `weight` DESC, `id` DESC";
		}elseif($orderby == "click"){
			$order = " ORDER BY click DESC, `top` DESC, `jinghua` DESC, `weight` DESC, `id` DESC";
		}elseif($orderby == "active"){//发帖最多的用户
			$order = " GROUP BY uid order by count(id) desc";
		}elseif($orderby == "lastreply"){//最新回复  去除重复的tid
			// $sql = $dsql->SetQuery("SELECT max(id) as mid, `tid`, `pubdate` FROM `#@__tieba_reply` WHERE `state` = 1  GROUP BY tid ORDER BY  mid DESC, pubdate DESC");
			$sql = $dsql->SetQuery("SELECT max(id) as mid, `aid`, `dtime` FROM `#@__public_comment` WHERE `ischeck` = 1  AND `pid` = 0 AND `type` = 'tieba-detail' GROUP BY aid ORDER BY  mid DESC, dtime DESC");
			$retReply = $dsql->dsqlOper($sql, "results");
			if($retReply){
				foreach ($retReply as $key => $value) {
					$replyArr[] = $value['aid'];
				}
				$replyArr = join(',',$replyArr);
				$where .= " AND `id` in ($replyArr)";
				$order = " order by field (`id`,$replyArr)";
			}
		}

		$archives = $dsql->SetQuery("SELECT l.`up`, l.`id`, l.`typeid`, l.`uid`, l.`title`, l.`pubdate`, l.`color`, l.`click`, l.`bold`, l.`jinghua`, l.`top`, l.`content`, l.`state`, l.`ip`, l.`ipaddr`, (SELECT COUNT(`id`)  FROM `#@__public_comment` WHERE `aid` = l.`id` AND `ischeck` = 1 AND `pid` = 0 AND `type` = 'tieba-detail') AS reply, l.`waitpay` FROM `#@__tieba_list` l WHERE 1 = 1".$where);
		$archives_count = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__tieba_list` l WHERE 1 = 1".$where);

		//总条数
		// $totalResults = $dsql->dsqlOper($archives_count, "results", "NUM");
		// $totalCount = (int)$totalResults[0][0];
		$totalCount = getCache("tieba_total", $archives_count, 300, array("name" => "total", "savekey" => 1, "disabled" => $u));

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
		// $results = $dsql->dsqlOper($archives.$where1.$order.$where, "results");
		$results = getCache("tieba_list", $archives.$where1.$order.$where, 300, array("disabled" => $u));

		if($results){
			foreach($results as $key => $val){
				$list[$key]['id']     = $val['id'];
				$list[$key]['typeid'] = $val['typeid'];
				$list[$key]['uid']    = $val['uid'];

				$username = $photo = "";
				$sql = $dsql->SetQuery("SELECT `nickname`, `photo` FROM `#@__member` WHERE `id` = ".$val['uid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$username = $ret[0]['nickname'];
					$photo    = getFilePath($ret[0]['photo']);
				}
				$list[$key]['username'] = $username;
				$list[$key]['photo'] = $photo;

				$list[$key]['title']  = $val['title'];
				$list[$key]['color']  = $val['color'];
				$list[$key]['click']  = $val['click'];
				$list[$key]['bold']    = $val['bold'];
				$list[$key]['jinghua'] = $val['jinghua'];
				$list[$key]['top']     = $val['top'];
				$list[$key]['ip']     = $val['ip'];
				$list[$key]["ipAddress"] = $val['ipaddr'];

				$archives   = $dsql->SetQuery("SELECT `id` FROM `#@__public_up` WHERE `module` = 'tieba' AND `action` = 'detail' AND `type` = '0' AND `tid` = {$val['id']}");
				$totalCount = $dsql->dsqlOper($archives, "totalCount");
				$list[$key]["up"]      = $totalCount;

				$content = $val['content'];
				if(strpos($content,'video')){
					$list[$key]['isvideo'] = 1;
				}
				$list[$key]['content'] = !empty($content) ? cn_substrR(strip_tags($content), 100) : "";

				global $data;
				$data = "";
				$typeArr = getParentArr("tieba_type", $val['typeid']);
				$typeArr = array_reverse(parent_foreach($typeArr, "typename"));
				$list[$key]['typename'] = $typeArr;

				$list[$key]['pubdate']    = $val['pubdate'];
				$list[$key]['pubdate1']   = floor((GetMkTime(time()) - $val['pubdate'] / 86400) % 30) > 30 ? date("Y-m-d", $val['pubdate']) : FloorTime(GetMkTime(time()) - $val['pubdate']);

				//会员中心显示信息状态
				if($u == 1 && $userLogin->getMemberID() > -1){
					$list[$key]['state'] = $val['state'];
					$list[$key]['waitpay'] = $val['waitpay'];
				}

				$list[$key]['reply'] = $val['reply'];

				$param = array(
					"service"     => "tieba",
					"template"    => "detail",
					"id"          => $val['id']
				);
				$list[$key]['url'] = getUrlPath($param);


				$imgGroup = array();
				$video = '';
				global $cfg_attachment;
				global $cfg_basehost;

				$attachment = str_replace("http://".$cfg_basehost, "", $cfg_attachment);
				$attachment = str_replace("https://".$cfg_basehost, "", $attachment);

				$attachment = str_replace("/", "\/", $attachment);
				$attachment = str_replace(".", "\.", $attachment);
				$attachment = str_replace("?", "\?", $attachment);
				$attachment = str_replace("=", "\=", $attachment);

				preg_match_all("/$attachment(.*)[\"|'| ]/isU", $content, $picList);
				$picList = array_unique($picList[1]);

				if(empty($picList)){
					preg_match_all("/\/tieba\/(.*)[\"|'| ]/isU", $content, $picList);
					$picList = array_unique($picList[1]);

					$newPicList = array();
					if($picList){
						foreach ($picList as $k => $v) {
							array_push($newPicList, '/tieba/' . $v);
						}
					}
					$picList = $newPicList;
				}

				//内容图片  如果后台开启隐藏附件路径功能，这里就不获取不到图片了
				if(!empty($picList)){
					foreach($picList as $v_){
						$filePath = getRealFilePath($v_);
						$fileType = explode(".", $filePath);
						$fileType = strtolower($fileType[count($fileType) - 1]);
						$ftype = array("jpg", "jpge", "gif", "jpeg", "png", "bmp");
						if(in_array($fileType, $ftype)){
							$imgGroup[] = $filePath;
						}elseif($fileType == 'mp4'){
							$video = $filePath;
						}
					}
				}
				$list[$key]['imgGroup'] = $imgGroup;
				$list[$key]['video'] = $video;

				//最新评论
				$lastReply = array();
				// $sql = $dsql->SetQuery("SELECT `uid`, `content`, `pubdate` FROM `#@__tieba_reply` WHERE `state` = 1 AND `tid` = ".$val['id']);
				$sql = $dsql->SetQuery("SELECT `userid` uid, `content`, `dtime` pubdate FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'tieba-detail' AND `aid` = '".$val['id']."' AND `pid` = 0");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){

					$username = "";
					$sql = $dsql->SetQuery("SELECT `nickname` FROM `#@__member` WHERE `id` = ".$ret[0]['uid']);
					$ret_ = $dsql->dsqlOper($sql, "results");
					if($ret_){
						$username = $ret_[0]['nickname'];
					}

					$lastReply = array(
						"uid" => $ret[0]['uid'],
						"username" => $username,
						"content" => !empty($ret[0]['content']) ? cn_substrR(strip_tags($ret[0]['content']), 100) : "",
						"pubdate" => $ret[0]['pubdate'],
					);
				}

				$list[$key]['lastReply'] = $lastReply;

				// 打赏
				$archives = $dsql->SetQuery("SELECT `id` FROM `#@__member_reward` WHERE `aid` = ".$val["id"]." AND `state` = 1");
				//总条数
				$totalCount = $dsql->dsqlOper($archives, "totalCount");
				if($totalCount){
					$archives = $dsql->SetQuery("SELECT SUM(`amount`) totalAmount FROM `#@__member_reward` WHERE `module` = 'tieba' AND `aid` = ".$val["id"]." AND `state` = 1");
					$ret = $dsql->dsqlOper($archives, "results");
					$totalAmount = $ret[0]['totalAmount'];
				}else{
					$totalAmount = 0;
				}
				$list[$key]['reward'] = array("count" => $totalCount, "amount" => $totalAmount);

				if($orderby=='active'){
					//是否相互关注
					$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $userid AND `fid` = " . $val['uid']);
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$list[$key]['isfollow'] = 1;//关注
					}elseif($userid == $val['ruid']){
						$list[$key]['isfollow'] = 2;//自己
					}else{
						$list[$key]['isfollow'] = 0;//未关注
					}

					//帖子总数
					$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `uid` = " . $val['uid']);
					$ret = $dsql->dsqlOper($sql, "results");
					$list[$key]['tiziTotal'] = $ret[0]['t'];
					//粉丝人数
					$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__member_follow` WHERE `fid` = " . $val['uid']);
					$fansret = $dsql->dsqlOper($sql, "results");
					$list[$key]['totalFans'] = $fansret[0]['t'];
				}

			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
     * 帖子详细
     * @return array
     */
	public function detail(){
		global $dsql;
		global $userLogin;
		$detail = array();
		$id = $this->param;
		$id = is_numeric($id) ? $id : $id['id'];
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

		$archives = $dsql->SetQuery("SELECT * FROM `#@__tieba_list` WHERE `id` = ".$id.$where);
		// $results  = $dsql->dsqlOper($archives, "results");
		$results = getCache("tieba_detail", $archives, 0, $id);
		if($results){
			$detail["id"]       = $results[0]['id'];
			$detail["typeid"]   = $results[0]['typeid'];

			$typename = "";
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__tieba_type` WHERE `id` = " . $results[0]['typeid']);
			$typename = getCache("tieba_type", $sql, 0, array("name" => "typename", "sign" => $results[0]['typeid']));
			$detail['typename'] = $typename;

			$detail["uid"]      = $results[0]['uid'];
			$detail["title"]    = $results[0]['title'];
			$detail["cityid"]   = $results[0]['cityid'];
			$detail["content"]  = str_replace(array("\r\n","\n","\r"), '<br />', $results[0]['content']);
			$detail["pubdate"]  = $results[0]['pubdate'];
			$detail["ip"]       = $results[0]['ip'];
			$detail["ipAddress"] = $results[0]['ipaddr'];
			$detail["color"]    = $results[0]['color'];
			$detail["click"]    = $results[0]['click'];
			$detail["bold"]     = $results[0]['bold'];
			$detail["isreply"]  = $results[0]['isreply'];
			$detail["jinghua"]  = $results[0]['jinghua'];
			$detail["top"]      = $results[0]['top'];

			//评论数量
			// $archives = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_reply` WHERE `tid` = ".$results[0]['id']." AND `state` = 1");
			$archives = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__public_comment` WHERE `ischeck` = 1 AND `type` = 'tieba-detail' AND `aid` = '".$results[0]['id']."' AND `pid` = 0");
			$totalCount = $dsql->dsqlOper($archives, "results");
			$detail['reply'] = $totalCount[0]['t'];

			//楼主信息
			$louzu = array();
			if($results[0]['uid']){
				//帖子总数
				$$tizi_louzuTotal = 0;
				$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `uid` = " . $results[0]['uid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$tizi_louzuTotal = $ret[0]['t'];
				}
				//精华总数
				$tizi_louzuJinghuaTotal = 0;
				$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `jinghua` = 1 AND `uid` = " . $results[0]['uid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$tizi_louzuJinghuaTotal = $ret[0]['t'];
				}

				$louzuInfo = $userLogin->getMemberInfo($results[0]['uid']);
				if($louzuInfo && is_array($louzuInfo)){
					$louzu = array(
						"uid" => $results[0]['uid'],
						"photo" => $louzuInfo['photo'],
						"nickname" => $louzuInfo['nickname'],
						"regtime" => $louzuInfo['regtime'],
						"tizi_louzuTotal" => $tizi_louzuTotal,
						"tizi_louzuJinghuaTotal" => $tizi_louzuJinghuaTotal
					);
				}
			}
			$detail["louzu"] = $louzu;


            $imgGroup = array();
            global $cfg_attachment;
            global $cfg_basehost;

            $attachment = str_replace("http://".$cfg_basehost, "", $cfg_attachment);
            $attachment = str_replace("https://".$cfg_basehost, "", $attachment);

            $attachment = str_replace("/", "\/", $attachment);
            $attachment = str_replace(".", "\.", $attachment);
            $attachment = str_replace("?", "\?", $attachment);
            $attachment = str_replace("=", "\=", $attachment);

            preg_match_all("/$attachment(.*)[\"|'| ]/isU", $results[0]['content'], $picList);
            $picList = array_unique($picList[1]);

            if(empty($picList)){
                preg_match_all("/\/tieba\/(.*)[\"|'| ]/isU", $results[0]['content'], $picList);
                $picList = array_unique($picList[1]);

                $newPicList = array();
                if($picList){
                    foreach ($picList as $k => $v) {
                        array_push($newPicList, '/tieba/' . $v);
                    }
                }
                $picList = $newPicList;
            }

            //内容图片  如果后台开启隐藏附件路径功能，这里就不获取不到图片了
            if(!empty($picList)){
                foreach($picList as $v_){
                    $filePath = getRealFilePath($v_);
                    $fileType = explode(".", $filePath);
                    $fileType = strtolower($fileType[count($fileType) - 1]);
                    $ftype = array("jpg", "jpge", "gif", "jpeg", "png", "bmp");
                    if(in_array($fileType, $ftype)){
                        $imgGroup[] = $filePath;
                    }
                }
            }
            $detail['imgGroup'] = $imgGroup;

		}
		return $detail;
	}


	/**
     * 评论列表
     * @return array
     */
	public function reply(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$tid = $uid = $page = $pageSize = $where = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$tid      = $this->param['tid'];
				$rid      = $this->param['rid'];
				$uid      = $this->param['uid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		if(empty($tid)) return array("state" => 200, "info" => '格式错误！');

		$where = " `state` = 1 AND `tid` = ".$tid;

		//指定会员ID
		if(!empty($uid)){
			$where .= " AND `uid` = ".$uid;
		}

		//指定评论回复
		if(!empty($rid)){
			$where .= " AND `rid` = ".$rid;
		}else{
			$where .= " AND `rid` = 0";
		}

		$order    = " ORDER BY `id` ASC";
		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$archives = $dsql->SetQuery("SELECT `id`, `uid`, `content`, `pubdate`, `zan`, `zan_user` FROM `#@__tieba_reply` WHERE ".$where);
		$archives_count = $dsql->SetQuery("SELECT count(`id`) FROM `#@__tieba_reply` l WHERE ".$where);

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
				$list[$key]['zan']    = $val['zan'];

				$list[$key]['content']  = preg_replace('/src="\/include\/attachment\.php/', 'class="r-pic" src="/include/attachment.php', $val['content']);
				$list[$key]['pubdate']  = $val['pubdate'];

				//是否点赞过
				if($val['zan_user']){
					$userArr               = explode(",", $val['zan_user']);
					$list[$key]['already'] = in_array($userLogin->getMemberID(), $userArr) ? 1 : 0;
				}

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
					//帖子总数
					$$tizi_memberTotal = 0;
					$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `uid` = $memberID");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$tizi_memberTotal = $ret[0]['t'];
					}
					//精华总数
					$tizi_memberJinghuaTotal = 0;
					$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `jinghua` = 1 AND `uid` = $memberID");
					$ret = $dsql->dsqlOper($sql, "results");
					if($ret){
						$tizi_memberJinghuaTotal = $ret[0]['t'];
					}

					$memberInfo = $userLogin->getMemberInfo($memberID);
					if(is_array($memberInfo)){
						$member = array(
							"id" => $memberID,
							"photo" => $memberInfo['photo'],
							"nickname" => $memberInfo['nickname'],
							"regtime" => $memberInfo['regtime'],
							"tizi_memberTotal" => $tizi_memberTotal,
							"tizi_memberJinghuaTotal" => $tizi_memberJinghuaTotal
						);
					}
				}
				$list[$key]['member'] = $member;

			}
		}//print_R($list);exit;

		return array("pageInfo" => $pageinfo, "list" => $list);
	}


	/**
	 * 发表帖子
	 * @return array
	 */
	public function sendPublish(){
		global $dsql;
		global $userLogin;

		$param = $this->param;
		$app   = (int)$param['app'];

		$ip = GetIp();
		$ipaddr = getIpAddr($ip);
		$pubdate = GetMkTime(time());

		include HUONIAOINC."/config/tieba.inc.php";
		$arcrank = (int)$customFabuCheck;

		//APP发贴
		if($app){

			$data = file_get_contents('php://input');
			if(empty($data)){
				return array("state" => 200, "info" => '要发表的内容为空！');
			}

			$data = json_decode($data, true);
			if(!is_array($data)){
				return array("state" => 200, "info" => '数据格式错误！');
			}

			$uid     = (int)$data['userid'];

		}else{
			//获取用户ID
			$uid = $userLogin->getMemberID();
			if($uid == -1){
				return array("state" => 200, "info" => '登录超时，请重新登录！');
			}
		}


		//用户信息
		$userinfo = $userLogin->getMemberInfo($uid);

		global $cfg_memberVerified;
		global $cfg_memberVerifiedInfo;
		if($cfg_memberVerified && $userinfo['userType'] == 1 && !$userinfo['certifyState']){
			return array("state" => 200, "info" => $cfg_memberVerifiedInfo);
		}
		// 手机认证
		global $cfg_memberBindPhone;
		global $cfg_memberBindPhoneInfo;
		if($cfg_memberBindPhone && (!$userinfo['phone'] || !$userinfo['phoneCheck'])){
			return array("state" => 200, "info" => $cfg_memberBindPhoneInfo);
		}

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
				$tiebaCount = (int)$memberLevelAuth['tieba'];

				//统计用户当天已发布数量 @
				$today = GetMkTime(date("Y-m-d", time()));
				$tomorrow = GetMkTime(date("Y-m-d", strtotime("+1 day")));
				$sql = $dsql->SetQuery("SELECT count(`id`) total FROM `#@__tieba_list` WHERE `uid` = $uid AND `pubdate` >= $today AND `pubdate` < $tomorrow AND `alonepay` = 0 AND `waitpay` = 0");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$alreadyFabu = $ret[0]['total'];
					if($alreadyFabu >= $tiebaCount){
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
					$amount = $fabuAmount["tieba"];
				}else{
					$amount = 0;
				}

			}

		}

		$waitpay = $amount > 0 ? 1 : 0;

		if($userinfo['level']){
			$auth = array("level" => $userinfo['level'], "levelname" => $userinfo['levelName'], "alreadycount" => $alreadyFabu, "maxcount" => $tiebaCount);
		}else{
			$auth = array("level" => 0, "levelname" => "普通会员", "maxcount" => 0);
		}

		//APP发贴
		if($app){

			$data = file_get_contents('php://input');
			if(empty($data)){
				return array("state" => 200, "info" => '要发表的内容为空！');
			}

			$data = json_decode($data, true);
			if(!is_array($data)){
				return array("state" => 200, "info" => '数据格式错误！');
			}

			$uid     = (int)$data['userid'];
			$title   = filterSensitiveWords($data['title']);
			$typeid  = $data['typeid'];
			$cityid  = $data['cityid'];
			$body    = $data['body'];
			$address = $data['address'];
			$lng     = $data['lng'];
			$lat     = $data['lat'];

			if(empty($uid) || $uid == -1) return array("state" => 200, "info" => '登录超时，请重新登录！');
			if(empty($title)) return array("state" => 200, "info" => '请填写标题！');
            if(empty($typeid)) return array("state" => 200, "info" => '请选择分类！');
			if(empty($cityid)) return array("state" => 200, "info" => '请选择城市！');
			if(!is_array($body)){
				return array("state" => 200, "info" => '内容格式错误！');
			}

			//组合内容
			$content = array();
			foreach ($body as $key => $value) {
				$k_ = array_keys($value);
				$v_ = array_values($value);
				$k = $k_[0];
				$v = $v_[0];
				if($k == "text"){
					array_push($content, '<div class="c-paragraph-text">' . preg_replace('/\/static\/images\/ui\/emot\/baidu\/(.*?)\.png/im','<img class="c-emot" src="/static/images/ui/emot/baidu/$1.png" />', str_replace(array("\r\n","\n","\r"), '<br />', $v)) . '</div>');
				}

				if($k == "image"){
					array_push($content, '<div class="c-paragraph-image"><img src="/include/attachment.php?f='.$v.'" /></div>');
				}

				if($k == "audio"){
					array_push($content, '<div class="c-paragraph-audio"><audio preload="auto"><source src="/include/attachment.php?f='.$v.'"></audio></div>');
				}

				if($k == "video"){
					array_push($content, '<div class="c-paragraph-video"><video class="video-js vjs-fluid" controls preload="auto" data-setup="{}"><source src="/include/attachment.php?f='.$v.'" type="video/mp4"></video></div>');
				}

				if($k == "iframe"){
					array_push($content, '<div class="c-paragraph-iframe"><iframe src="'.$v.'" frameborder=0 "allowfullscreen"></iframe></div>');
				}
			}

			// return array("state" => 200, "info" => json_encode($content)."<br />".json_encode($data));

			if(empty($content)){
				return array("state" => 200, "info" => '内容为空，发表失败！');
			}

			$content = filterSensitiveWords(join("", $content), false);

			//保存到主表
			$archives = $dsql->SetQuery("INSERT INTO `#@__tieba_list` (`cityid`, `typeid`, `uid`, `title`, `content`, `pubdate`, `ip`, `ipaddr`, `state`, `isreply`, `address`, `lng`, `lat`, `waitpay`, `alonepay`) VALUES ('$cityid', '$typeid', '$uid', '$title', '$content', '$pubdate', '$ip', '$ipaddr', '$arcrank', '1', '$address', '$lng', '$lat', '$waitpay', '$alonepay')");
			$aid = $dsql->dsqlOper($archives, "lastid");

			if(is_numeric($aid)){

				$type = $amount > 0 ? "pay" : "free";

				$param = array(
					"service"     => "member",
					"type"        => "user",
					"action"      => "manage",
					"template"    => "tieba"
				);
				$url = getUrlPath($param);

				return array("url" => $url, "status" => $type);

				// return $amount > 0 ? "pay" : "free";
				// return array("auth" => $auth, "aid" => $aid, "amount" => $amount);

			}else{
				return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');
			}
		}


		$typeid  = (int)$param['typeid'];
		$cityid  = (int)$param['cityid'];
		$title   = filterSensitiveWords($param['title']);
		$content = filterSensitiveWords($param['content'], false);
		$vdimgck = filterSensitiveWords($param['vdimgck']);

        if(empty($cityid)) return array("state" => 200, "info" => '请选择城市！');
		if(empty($typeid)) return array("state" => 200, "info" => '请选择分类！');
		if(empty($title)) return array("state" => 200, "info" => '请填写标题！');
		if(empty($content)) return array("state" => 200, "info" => '请填写内容！');
		if(empty($vdimgck) && !isMobile()) return array("state" => 200, "info" => '请填写验证码！');

		$vdimgck = strtolower($vdimgck);
		if($vdimgck != $_SESSION['huoniao_vdimg_value'] && !isMobile()) return array("state" => 200, "info" => '验证码输入错误');

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__tieba_list` (`cityid`, `typeid`, `uid`, `title`, `content`, `pubdate`, `ip`, `ipaddr`, `state`, `isreply`, `waitpay`, `alonepay`) VALUES ('$cityid', '$typeid', '$uid', '$title', '$content', '$pubdate', '$ip', '$ipaddr', '$arcrank', '1', '$waitpay', '$alonepay')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){
			return array("auth" => $auth, "aid" => $aid, "amount" => $amount);

		}else{
			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');
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

		$tid   = $param['tid'];
		$rid   = $param['rid'];
		$content = filterSensitiveWords($param['content'], false);
//		$content = cn_substrR($content, 200);
		$vdimgck = filterSensitiveWords($param['vdimgck']);

		if(empty($content)){
			return array("state" => 200, "info" => '请输入回复内容！');
		}

		if(empty($rid) && !isMobile()){
			$vdimgck = strtolower($vdimgck);
			if($vdimgck != $_SESSION['huoniao_vdimg_value']) return array("state" => 200, "info" => '验证码输入错误');
		}

		$ip = GetIp();
		$pubdate = GetMkTime(time());

		include HUONIAOINC."/config/tieba.inc.php";
		$state = (int)$customCommentCheck;

		//保存到主表
		$archives = $dsql->SetQuery("INSERT INTO `#@__tieba_reply` (`tid`, `rid`, `uid`, `content`, `pubdate`, `ip`, `state`, `zan_user`) VALUES ('$tid', '$rid', '$uid', '$content', '$pubdate', '$ip', '$state', '')");
		$aid = $dsql->dsqlOper($archives, "lastid");

		if(is_numeric($aid)){

			$info = array();
			$memberInfo = $userLogin->getMemberInfo($uid);
			if(is_array($memberInfo)){
				$info = array(
					"id" => $uid,
					"photo" => $memberInfo['photo'],
					"nickname" => $memberInfo['nickname'],
					"regtime" => $memberInfo['regtime'],
					"content" => $content,
					"pubdate" => $pubdate,
					"state"   => $state
				);
			}
			return $info;

		}else{
			return array("state" => 101, "info" => '发布到数据时发生错误，请检查字段内容！');
		}

	}


	/**
		* 删除帖子
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

		$archives = $dsql->SetQuery("SELECT * FROM `#@__tieba_list` WHERE `id` = ".$id);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			$results = $results[0];
			if($results['uid'] == $uid){

				$body = $results[0]['content'];
				if(!empty($body)){
					delEditorPic($body, "tieba");
				}

				//删除评论
				$archives = $dsql->SetQuery("DELETE FROM `#@__tieba_reply` WHERE `tid` = ".$id);
				$results = $dsql->dsqlOper($archives, "update");

				//删除表
				$archives = $dsql->SetQuery("DELETE FROM `#@__tieba_list` WHERE `id` = ".$id);
				$dsql->dsqlOper($archives, "update");

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

		//获取用户ID
		$uid = $userLogin->getMemberID();
		if($uid == -1){
			return array("state" => 100, "info" => 'true');
		}

		$archives = $dsql->SetQuery("SELECT `uid` FROM `#@__tieba_list` WHERE `id` = ".$aid);
		$results  = $dsql->dsqlOper($archives, "results");
		if($results){
			if($results[0]['uid'] == $uid){
				return array("state" => 200, "info" => '自己不可以给自己打赏！');
			}else{
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

		$archives = $dsql->SetQuery("SELECT m.`username`, m.`photo`, r.`amount`, r.`date` FROM `#@__member_reward` r LEFT JOIN `#@__member` m ON m.`id` = r.`uid` WHERE r.`aid` = ".$aid." AND r.`state` = 1 ORDER BY r.`id` ASC");
		//总条数
		$totalCount = $dsql->dsqlOper($archives, "totalCount");

		$list = array();
		if($totalCount > 0){
			$results = $dsql->dsqlOper($archives, "results");
			if($results){
				foreach($results as $key => $val){
					$list[$key]['username'] = $val['username'] ? $val['username'] : '游客';
					$list[$key]['photo']    = !empty($val['photo']) ? getFilePath($val['photo']) : "";
					$list[$key]['amount']   = $val['amount'];
					$list[$key]['date']   	= date("Y-m-d H:i:s", $val['date']);
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
			"service"     => "tieba",
			"template"    => "detail",
			"id"          => $aid
		);
		$url = getUrlPath($param);

		//验证金额
		if($amount <= 0 || !is_numeric($aid) || (empty($paytype) && !isMobile()) ){
			header("location:".$url);
			die;
		}

		//查询信息发布人
		$sql = $dsql->SetQuery("SELECT `uid` FROM `#@__tieba_list` WHERE `id` = ".$aid);
		$ret = $dsql->dsqlOper($sql, "results");
		if(!$ret){
			//信息不存在
			header("location:".$url);
			die;
		}
		$admin = $ret[0]['uid'];

		//自己不可以给自己打赏
		if($admin == $uid){
			//信息不存在
			header("location:".$url);
			die;
		}

		//订单号
		$ordernum = create_ordernum();

		$archives = $dsql->SetQuery("INSERT INTO `#@__member_reward` (`ordernum`, `module`, `uid`, `to`, `aid`, `amount`, `state`, `date`) VALUES ('$ordernum', 'tieba', '$uid', '$admin', '$aid', '$amount', 0, ".GetMkTime(time()).")");
		$return = $dsql->dsqlOper($archives, "update");
		if($return != "ok"){
			die("提交失败，请稍候重试！");
		}

		if($isMobile){
            $param = array(
                "service" => "tieba",
                "template" => "pay",
                "param" => "ordernum=".$ordernum
            );
            header("location:".getUrlPath($param));
            die;
        }

		//跳转至第三方支付页面
		createPayForm("tieba", $ordernum, $amount, $paytype, "打赏帖子");

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
				$sql = $dsql->SetQuery("SELECT `title` FROM `#@__tieba_list` WHERE `id` = $aid");
				$ret = $dsql->dsqlOper($sql, "results");
				$title = $ret[0]['title'];

				$title_ = '<a href="'.$cfg_secureAccess.$cfg_basehost.'/index.php?service=tieba&template=detail&id='.$aid.'" target="_blank">'.$title.'</a>';

				//更新订单状态
				$sql = $dsql->SetQuery("UPDATE `#@__member_reward` SET `state` = 1 WHERE `id` = ".$rid);
				$dsql->dsqlOper($sql, "update");

				//如果是会员打赏，保存操作日志
				if($uid != -1){
					$archives = $dsql->SetQuery("INSERT INTO `#@__member_money` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$uid', '0', '$amount', '贴吧打赏：$title_', '$date')");
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
					"service"  => "tieba",
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

	/**
	 * 帖子数据量、会员数调取
	 */
	public function getFormat(){
		global $dsql;

        $cityid = getCityId();

		//统计帖子数量
		$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `waitpay` = 0 AND `cityid` = $cityid");
		$Tiret = $dsql->dsqlOper($sql, "results");
		$tiziTotal = $Tiret[0]['t'];

		//今日
		$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `waitpay` = 0 AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = curdate() AND `cityid` = $cityid");
		$Tret = $dsql->dsqlOper($sql, "results");
		$tiziTodayTotal = $Tret[0]['t'];

		//今日浏览量
		$sql = $dsql->SetQuery("SELECT sum(`click`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `waitpay` = 0 AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY) AND `cityid` = $cityid");
		$Tret = $dsql->dsqlOper($sql, "results");
		$tiziTodayClickTotal = (int)$Tret[0]['t'];

		//昨日
		$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `waitpay` = 0 AND DATE_FORMAT(FROM_UNIXTIME(`pubdate`), '%Y-%m-%d') = DATE_SUB(curdate(), INTERVAL 1 DAY) AND `cityid` = $cityid");
		$Yret = $dsql->dsqlOper($sql, "results");
		$tiziYestodayTotal = $Yret[0]['t'];

		//统计会员数量及在线人数
		$memberStatistics = array();
		$sql = $dsql->SetQuery("SELECT count(`id`) total, (SELECT count(`id`) FROM `#@__member` WHERE `state` = 1 AND (`mtype` = 1 OR `mtype` = 2) AND `online` > 0) online FROM `#@__member` WHERE `state` = 1 AND (`mtype` = 1 OR `mtype` = 2)");
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$memberStatistics['total'] = $ret[0]['total'];
			$memberStatistics['online'] = $ret[0]['online'];
		}

		//今日签到多少人
		if($cityid){
			global $data;
			$data = '';
			$cityAreaData = $dsql->getTypeList($cityid, 'site_area');
			$cityAreaIDArr = parent_foreach($cityAreaData, 'id');
			$cityAreaIDs = join(',', $cityAreaIDArr);
			if($cityAreaIDs){
				$whereQ .= " AND b.`addr` in ($cityAreaIDs)";
			}else{
				$whereQ .= " AND 3 = 4";
			}
		}

		$sql = $dsql->SetQuery("select count(a.`id`) from `#@__member_qiandao` a LEFT JOIN `#@__member` b ON a.uid = b.id where 1=1 $whereQ AND DATE_FORMAT(FROM_UNIXTIME(a.`date`), '%Y-%m-%d') = curdate() ORDER BY a.`date` DESC");
		$qiandaoCount = $dsql->dsqlOper($sql, "results", "NUM");
		if($qiandaoCount){
			$qiandaoTotal = $qiandaoCount[0][0];
		}

		return array("qiandaoTotal" => $qiandaoTotal, "memberOnline" => $memberStatistics['online'],"memberTotal" => $memberStatistics['total'], "tiziTotal" => $tiziTotal, "tiziTodayTotal" => $tiziTodayTotal, "tiziYestodayTotal" => $tiziYestodayTotal, "tiziTodayClickTotal" => $tiziTodayClickTotal);
	}

	/**
	 * 点赞
	 */
	 public function getUp(){
		global $dsql;
		global $userLogin;
		$param = $this->param;

		$id       = $param['id'];
		$uid      = $param['uid'];

		$userid      = $userLogin->getMemberID();
		if($userid == -1){
			return array("state" => 200, "info" => '登录超时，请重新登录！');
		}

		if(empty($id)) return array("state" => 200, "info" => '数据传递失败！');

		$puctime = time();

		$sql = $dsql->SetQuery("SELECT `id` FROM `#@__tieba_up`  WHERE `tid` = '$id' and `ruid` = '$userid'");
		$res = $dsql->dsqlOper($sql, "results");
		if(!empty($res)){
			$archives = $dsql->SetQuery("UPDATE `#@__tieba_list` SET  `up` = up - 1 WHERE `id` = '$id'");
			$results = $dsql->dsqlOper($archives, "update");
			if($results == 'ok'){
				$archives = $dsql->SetQuery("DELETE FROM `#@__tieba_up` WHERE `tid` = '$id' and `ruid` = '$userid'");
				$dsql->dsqlOper($archives, "update");
				return 'ok';
			}else{
				return array("state" => 200, "info" => '数据出错！');
			}
		}else{
			//保存到主表
			$archives = $dsql->SetQuery("UPDATE `#@__tieba_list` SET  `up` = up + 1 WHERE `id` = '$id'");
			$results = $dsql->dsqlOper($archives, "update");
			if($results != "ok"){
				return array("state" => 200, "info" => '数据出错！');
			}else{
				//插入点赞人信息
				$archives = $dsql->SetQuery("INSERT INTO `#@__tieba_up` (`uid`, `tid`, `ruid`, `puctime`) VALUES ('$uid', '$id', '$userid', '$puctime')");
				$dsql->dsqlOper($archives, "update");
				return 'ok';
			}
		}
	 }

	 /**
	  * 点赞人列表
	  */
	 public function upList(){
		global $dsql;
		global $userLogin;
		$pageinfo = $list = array();
		$orderby = $page = $pageSize = $where = $where1 = "";

		if(!empty($this->param)){
			if(!is_array($this->param)){
				return array("state" => 200, "info" => '格式错误！');
			}else{
				$tid      = $this->param['tid'];
				$page     = $this->param['page'];
				$pageSize = $this->param['pageSize'];
			}
		}

		$userid = $userLogin->getMemberID();

		if(!empty($tid)){
			$where .=" and tid='$tid'";
		}

		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$page     = empty($page) ? 1 : $page;

		$order = " ORDER BY `puctime` DESC, `id` DESC";

		$archives_count = $dsql->SetQuery("SELECT count(`id`) FROM `#@__tieba_up` l WHERE 1 = 1".$where);
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

		$archives = $dsql->SetQuery("SELECT `id`, `uid`, `tid`, `ruid`, `puctime` FROM `#@__tieba_up` l WHERE 1 = 1".$where);
		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$results = $dsql->dsqlOper($archives.$where1.$order.$where, "results");
		if($results){
			foreach($results as $key => $val){
				//楼主信息
				$upUsername = $upPhoto = "";
				$sql = $dsql->SetQuery("SELECT `nickname`, `photo` FROM `#@__member` WHERE `id` = ".$val['uid']);
				$upRet = $dsql->dsqlOper($sql, "results");
				if($upRet){
					$upUsername = $upRet[0]['nickname'];
					$upPhoto    = getFilePath($upRet[0]['photo']);
				}
				$list[$key]['upUsername'] = $upUsername;
				$list[$key]['upPhoto'] = $upPhoto;

				//点赞人信息
				$uid = $username = $photo = "";
				$sql = $dsql->SetQuery("SELECT `id`, `nickname`, `photo` FROM `#@__member` WHERE `id` = ".$val['ruid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$username = $ret[0]['nickname'];
					$uid	  = $ret[0]['id'];
					$photo    = getFilePath($ret[0]['photo']);
				}
				$list[$key]['uid'] = $uid;
				$list[$key]['username'] = $username;
				$list[$key]['photo'] = $photo;
				//帖子总数
				$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__tieba_list` WHERE `state` = 1 AND `uid` = " . $val['ruid']);
				$ret = $dsql->dsqlOper($sql, "results");
				$list[$key]['tiziTotal'] = $ret[0]['t'];
				//关注人数
				$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__member_follow` WHERE `tid` = " . $val['ruid']);
				$followret = $dsql->dsqlOper($sql, "results");
				$list[$key]['followTotal'] = $followret[0]['t'];
				//粉丝人数
				$sql = $dsql->SetQuery("SELECT count(`id`) t FROM `#@__member_follow` WHERE `fid` = " . $val['ruid']);
				$fansret = $dsql->dsqlOper($sql, "results");
				$list[$key]['totalFans'] = $fansret[0]['t'];

				//点赞人和楼主是否相互关注
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__member_follow` WHERE `tid` = $userid AND `fid` = " . $val['ruid']);
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$list[$key]['isfollow'] = 1;
				}elseif($userid == $val['ruid']){
					$list[$key]['isfollow'] = 2;
				}else{
					$list[$key]['isfollow'] = 0;
				}
			}
		}

		return array("pageInfo" => $pageinfo, "list" => $list);
	 }


	 /**
     * 评论点赞
     */
    public function dingComment(){
        global $dsql;
        global $userLogin;

        $userid = $userLogin->getMemberID();
        if($userid == -1){
            return array("state" => 200, "info" => '登录超时，请重新登录！');
        }

        $param   = $this->param;
        $id      = (int)$param['id'];
        $type    = $param['type'];

        if(empty($id)){
            return array("state" => 200, "info" => "参数错误");
        }

        // 评论信息
        $sql = $dsql->SetQuery("SELECT `uid`, `zan_user` FROM `#@__tieba_reply` WHERE `id` = $id AND `state` = 1");
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){

            if($userid == $ret[0]['uid']){
                return array("state" => 200, "info" => "自己不能给自己点赞哦~");
            }

            $zan_user = $ret[0]['zan_user'];

            $zan_user_arr = $zan_user ? explode(',', $zan_user) : array();

            $ip = GetIP();

            if($type == "add"){
                if(in_array($userid, $zan_user_arr)){
                    return array("state" => 200, "info" => "您已经赞过");
                }
                $zan_user_arr[] = $userid;
            }else{
                $k = array_search($userid, $zan_user_arr);
                if($k === false) return "操作成功";
                unset($zan_user_arr[$k]);
            }

            $sql = $dsql->SetQuery("UPDATE `#@__tieba_reply` SET `zan` = ".count($zan_user_arr).", `zan_user` = '".join(",", $zan_user_arr)."' WHERE `id` = $id");
            $ret = $dsql->dsqlOper($sql, "update");
            if($ret == "ok"){
                return "操作成功";
            }else{
                return array("state" => 200, "info" => "操作失败，请重试！");
            }

        }else{
            return array("state" => 200, "info" => "评价不存在！");
        }

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
            $sql = $dsql->SetQuery("SELECT `amount` FROM `#@__member_reward` WHERE `ordernum` = '$ordernum' AND `module` = 'tieba' AND `state` = 0");
            $res = $dsql->dsqlOper($sql, "results");
            if($res){
                $amount = $res[0]['amount'];
                //跳转至第三方支付页面
                createPayForm("tieba", $ordernum, $amount, $paytype, "打赏帖子");
                return;
            }
        }
        header("location:/404.html");
        die;

    }

}
