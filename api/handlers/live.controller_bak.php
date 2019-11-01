<?php

/**
 * huoniaoTag模板标签函数插件-直播模块
 *
 * @param $params array 参数集
 * @return array
 */
function live($params, $content = "", &$smarty = array(), &$repeat = array()){
	$service = "live";
	extract ($params);
	if(empty($action)) return '';
	global $huoniaoTag;
    //var_dump($params);die;
	//获取指定分类详细信息
	if($action == "livelist"){
		$huoniaoTag->assign('keywords', $keywords);
		$huoniaoTag->assign('typeid', $typeid);
		$huoniaoTag->assign('page', $page);
		$listHandels = new handlers($service, "typeDetail");
		$listConfig  = $listHandels->getHandle($typeid);
		//print_R($listConfig);exit;
		if(is_array($listConfig) && $listConfig['state'] == 100){
			$listConfig  = $listConfig['info'];
			if(is_array($listConfig)){
				foreach ($listConfig[0] as $key => $value) {
					$huoniaoTag->assign('live_'.$key, $value);
				}
			}
		}
		$param = array(
			"service"     => "member",
			"type"         => "user",
			"template"    => "fabu-live"
		);
		$url  = getUrlPath($param);
		$huoniaoTag->assign('liveurl', $url);
	//搜索
	}elseif($action == "search"){

		//关键字为空跳回首页
		if(empty($keywords)){
			header("location:index.html");
			die;
		}

		$huoniaoTag->assign('keywords', $keywords);
		$huoniaoTag->assign('page', (int)$page);
		return;

	//获取指定ID的详细信息
	}elseif($action == "detail" || $action == "h_detail"){
		global $userLogin;
		global $dsql;
		$detailHandels = new handlers($service, "detail");
		$detailConfig  = $detailHandels->getHandle($id);
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){
				//print_R($detailConfig);exit;
				foreach ($detailConfig[0] as $key => $value) {
					$huoniaoTag->assign('livedetail_'.$key, $value);
				}
			}
			//在线人数
			$detailHandels = new handlers($service, "describeLiveStreamOnlineUserNum");
			$onLineParm = array('Streamname'=>$detailConfig[0]['streamname']);
			$onLineConfig  = $detailHandels->getHandle($onLineParm);
			if($onLineConfig['state'] == 100){
				$huoniaoTag->assign('onLineNums', $onLineConfig['info']['TotalUserNumber']);
			}
			//判断当前登录会员是否已经关注过要访问的会员
			$userid = $userLogin->getMemberID();
			$fid = $detailConfig[0]['user'];
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__live_follow` WHERE `tid` = $userid AND `fid` = $fid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret && is_array($ret)){
				$huoniaoTag->assign('isfollow', 1);
			}
			$huoniaoTag->assign('fid', $fid);
			$huoniaoTag->assign('userid', $userid);
			//聊天室ID
			$chatRoomId='chatroom'.$id;
			$huoniaoTag->assign('detail_chatRoomId', $chatRoomId);
			//获取推流地址
			if($detailConfig[0]['state']==2){
				//查看录播的URL地址
				$streamNameHandels = new handlers($service, "describeLiveStreamRecordIndexFiles");
				$streamNameConfig  = $streamNameHandels->getHandle($detailConfig[0]['streamname']);
				if($streamNameConfig['state']==100&&is_array($streamNameConfig['info']['RecordIndexInfoList']['RecordIndexInfo'])){
	         $requestUrl=$streamNameConfig['info']['RecordIndexInfoList']['RecordIndexInfo'][0]['RecordUrl'];
					 $RecordIndexInfo = $streamNameConfig['info']['RecordIndexInfoList']['RecordIndexInfo'];
                  
					 $mp4File = $m3u8File = '';
					 // include_once(HUONIAOROOT."/api/live/alilive/alilive.class.php");
					 include HUONIAOINC."/config/live.inc.php";
					 // $aliLive = new Alilive() ;

					 foreach ($RecordIndexInfo as $key => $value) {
					 		if(strstr($value['OssObject'], 'm3u8')){
								// $m3u8File = $value['RecordUrl'];
								$m3u8File = $custom_server . $value['OssObject'];
								// $m3u8File = $custom_server . $aliLive->ossSignatureurl($requestUrl, $value['OssObject']);
							}
							if(strstr($value['OssObject'], 'mp4')){
								// $mp4File = $value['RecordUrl'];
								$mp4File = $custom_server . $value['OssObject'];
								// $mp4File = $custom_server . $aliLive->ossSignatureurl($requestUrl, $value['OssObject']);
							}
					 }
					 $huoniaoTag->assign('detail_mp4url', $mp4File);
					 $huoniaoTag->assign('detail_m3u8url', $m3u8File);
				}else{
					return array("state" => 200, "info" => '本次直播未录制！');
				}
			}else{
				$PulldetailHandels = new handlers($service, "getPullSteam");
				if(isMobile()){
					$param=array('id'=>$id,'type'=>'m3u8');
				}else{
					$param=array('id'=>$id);
				}
				$PulldetailConfig  = $PulldetailHandels->getHandle($param);
				$huoniaoTag->assign('detail_url', $PulldetailConfig['info']);
			}

			//更新浏览次数
			global $dsql;
			$sql = $dsql->SetQuery("UPDATE `#@__livelist` SET `click` = `click` + 1 WHERE `id` = ".$id);
			$dsql->dsqlOper($sql, "results");
		}else{
			header("location:".$cfg_basehost."/404.html");
		}
		return;
	}elseif($action == "anchor_index"){
		global $dsql;
		global $userLogin;
		//获取主播的信息
		if(!empty($userid)){
			$member = getMemberDetail($userid);
			if($member){
				$huoniaoTag->assign('type', $type);
				//访问量
				$vsql = $dsql->SetQuery("SELECT SUM(click) as views FROM `#@__livelist` WHERE user='$userid'");
				$viewret = $dsql->dsqlOper($vsql, "results");
				//$views = $viewret[0]['views']>=10000 ? round($viewret[0]['views']/10000,2) : $viewret[0]['views'];
				$huoniaoTag->assign('views', $viewret[0]['views']);

				//判断当前登录会员是否已经关注过要访问的会员
				$loginuserid = $userLogin->getMemberID();
				$fid = $userid;
				$sql = $dsql->SetQuery("SELECT `id` FROM `#@__live_follow` WHERE `tid` = $loginuserid AND `fid` = $fid");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret && is_array($ret)){
					$huoniaoTag->assign('isfollow', 1);
				}
				$huoniaoTag->assign('fid', $fid);
				$huoniaoTag->assign('userid', $loginuserid);

				$name  = !empty($member['username']) ? $member['username'] : $member['nickname'] ;
				$photo = !empty($member['photo']) ? $member['photo'] : '/static/images/noPhoto_40.jpg' ;
				$huoniaoTag->assign('anchor_name', $name);
				$huoniaoTag->assign('anchor_photo', $photo);
				$huoniaoTag->assign('anchor_id', $userid);

				$archives_count = $dsql->SetQuery("SELECT count(`id`) FROM `#@__live_follow` WHERE fid='$userid'");
				//总条数
				$ftotalResults = $dsql->dsqlOper($archives_count, "results", "NUM");
				$ftotalCount = (int)$ftotalResults[0][0];

				$archives_count = $dsql->SetQuery("SELECT count(`id`) FROM `#@__live_follow` WHERE tid='$userid'");
				//总条数
				$ttotalResults = $dsql->dsqlOper($archives_count, "results", "NUM");
				$ttotalCount = (int)$ttotalResults[0][0];

				$huoniaoTag->assign('ftotalCount', $ftotalCount);
				$huoniaoTag->assign('ttotalCount', $ttotalCount);
			}else{
				header("location:".$cfg_basehost."/404.html");
			}
		}else{
			header("location:".$cfg_basehost."/404.html");
		}

	}elseif($action=='index'){
		global $userLogin;
		global $dsql;

		$param = array(
			"service"     => "member",
			"type"         => "user",
			"template"    => "fabu-live"
		);
		$url  = getUrlPath($param);
		$huoniaoTag->assign('liveurl', $url);

	}elseif($action == 'fabu_live'){
		$param = array(
			"service"     => "live",
			"template"    => "live_details"
		);
		$url  = getUrlPath($param);
		$huoniaoTag->assign('url', $url);
	}elseif($action == 'live_details'){
		global $userLogin;
		global $dsql;

        $userid = $userLogin->getMemberID();
        if($userid==-1){
            header("location:".$cfg_basehost."/login.html");
        }
		//$sql = $dsql->SetQuery("SELECT `title`,`click`,`litpic`,`ftime`,`typeid`,`catid`,`flow`,`way`,`pushurl` FROM `huoniao_livelist` where id =(SELECT max(`id`) i FROM `#@__livelist` where user='$userid')");
		$sql = $dsql->SetQuery("SELECT `title`,`click`,`litpic`,`ftime`,`typeid`,`catid`,`flow`,`way`,`pushurl` FROM `huoniao_livelist` where id='$id'");
        $res = $dsql->dsqlOper($sql, "results");
        if($res){
        	//直播分类
			$archives  = $dsql->SetQuery("SELECT `typename` FROM `#@__livetype` WHERE `id` = ".$res[0]['typeid']);
			$result    = $dsql->dsqlOper($archives, "results");
			$typename  = !empty($result[0]['typename']) ? $result[0]['typename'] : '';
			//直播类型
			$catidtype = empty($res[0]['catid']) ? '公开' : ($res[0]['catid']==1 ? '加密' : '收费');
			$title     = empty($res[0]['title'])   ? '无标题' : $res[0]['title'];
			//流畅度
			$flowname  = $res[0]['flow'] == 1 ? '流畅' : ($res[0]['flow'] == 2  ? '普清' : '高清');
			//直播方式
			$wayname   = empty($res[0]['way'])   ? '横屏' : '竖屏';
			$click   = empty($res[0]['click'])   ? '0' : $res[0]['click'];
			$litpic    = !empty($res[0]['litpic']) ? (strpos($res[0]['litpic'],'images') ? $res[0]['litpic'] : getFilePath($res[0]['litpic'])) : '/static/images/404.jpg';
			$ftime = !empty($res[0]['ftime']) ? date("Y-m-d H:i:s",$res[0]['ftime']) : date("Y-m-d H:i:s",time());
        	$pushurl = !empty($res[0]['pushurl']) ? $res[0]['pushurl'] : '';
        	$huoniaoTag->assign('typename', $typename);
        	$huoniaoTag->assign('catidtype', $catidtype);
        	$huoniaoTag->assign('flowname', $flowname);
        	$huoniaoTag->assign('click', $click);
        	$huoniaoTag->assign('wayname', $wayname);
        	$huoniaoTag->assign('ftime', $ftime);
        	$huoniaoTag->assign('title', $title);
        	$huoniaoTag->assign('pushurl', $pushurl);
        	$huoniaoTag->assign('litpic', $litpic);
        }
	}

	global $template;
	if(empty($smarty)) return;

	if(!isset($return))
		$return = 'row'; //返回的变量数组名

	//注册一个block的索引，照顾smarty的版本
  if(method_exists($smarty, 'get_template_vars')){
      $_bindex = $smarty->get_template_vars('_bindex');
  }else{
      $_bindex = $smarty->getVariable('_bindex')->value;
  }

  if(!$_bindex){
      $_bindex = array();
  }

  if($return){
      if(!isset($_bindex[$return])){
          $_bindex[$return] = 1;
      }else{
          $_bindex[$return] ++;
      }
  }

  $smarty->assign('_bindex', $_bindex);

	//对象$smarty上注册一个数组以供block使用
	if(!isset($smarty->block_data)){
		$smarty->block_data = array();
	}

	//得一个本区块的专属数据存储空间
	$dataindex = md5(__FUNCTION__.md5(serialize($params)));
	$dataindex = substr($dataindex, 0, 16);

	//使用$smarty->block_data[$dataindex]来存储
	if(!$smarty->block_data[$dataindex]){
		//取得指定动作名
		$moduleHandels = new handlers($service, $action);
		$param = $params;
		//获取分类
		if($action == "type" || $action == "addr"){
			$param['son'] = $son ? $son : 0;

		//信息列表
		}elseif($action == "alist"){

			//如果是列表页面，则获取地址栏传过来的typeid
			if($template == "list" && !$typeid){
				global $typeid;
			}
			!empty($typeid) ? $param['typeid'] = $typeid : "";

		}


		$moduleReturn  = $moduleHandels->getHandle($param);

		//只返回数据统计信息
		if($pageData == 1){
			if(!is_array($moduleReturn) || $moduleReturn['state'] != 100){
				$pageInfo_ = array("totalCount" => 0, "gray" => 0, "audit" => 0, "refuse" => 0);
			}else{
				$moduleReturn  = $moduleReturn['info'];  //返回数据
				$pageInfo_ = $moduleReturn['pageInfo'];
			}
			$smarty->block_data[$dataindex] = array($pageInfo_);

		//正常返回
		}else{

			if(!is_array($moduleReturn) || $moduleReturn['state']!= 100) return '';
			$moduleReturn  = $moduleReturn['info'];  //返回数据
			$pageInfo_ = $moduleReturn['pageInfo'];
			if($pageInfo_){
				//如果有分页数据则提取list键
				$moduleReturn  = $moduleReturn['list'];
				//把pageInfo定义为global变量
				global $pageInfo;
				$pageInfo = $pageInfo_;
				$smarty->assign('pageInfo', $pageInfo);
			}

			$smarty->block_data[$dataindex] = $moduleReturn;  //存储数据

		}
	}

	//果没有数据，直接返回null,不必再执行了
	if(!$smarty->block_data[$dataindex]) {
		$repeat = false;
		return '';
	}

	if($action=="type"){
		//print_r($smarty->block_data[$dataindex]);die;
	}

	//一条数据出栈，并把它指派给$return，重复执行开关置位1
	if(list($key, $item) = each($smarty->block_data[$dataindex])){
		if($action == "type"){
			//print_r($item);die;
		}
		$smarty->assign($return, $item);
		$repeat = true;
	}

	//如果已经到达最后，重置数组指针，重复执行开关置位0
	if(!$item) {
		reset($smarty->block_data[$dataindex]);
		$repeat = false;
	}


	//打印内容
	print $content;
}
