<?php

/**
 * huoniaoTag模板标签函数插件-系统模块
 *
 * @param $params array 参数集
 * @return array
 */
function siteConfig($params, $content = "", &$smarty = array(), &$repeat = array()){
	$service = "siteConfig";
	extract ($params);
	if(empty($action)) return '';

	global $cfg_secureAccess;
	global $cfg_basehost;
	global $huoniaoTag;
	global $dsql;
	global $langData;

	//关于
	if($template == "about"){

		if(empty($id)){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_singellist` WHERE `type` = 'singel' LIMIT 0, 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$id = $ret[0]['id'];
			}else{
				header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
				die;
			}
		}

		$detailHandels = new handlers($service, "singelDetail");
		$detailConfig  = $detailHandels->getHandle($id);

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				$huoniaoTag->assign('id', $id);
				$huoniaoTag->assign('title', $detailConfig['title']);
				$huoniaoTag->assign('body', $detailConfig['body']);

			}else{
				header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
				die;
			}
		}else{
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
			die;
		}

		return;

	//协议
	}elseif($template == "protocol"){

		if(empty($id)){
			$sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_singellist` WHERE `type` = 'agree' LIMIT 0, 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$id = $ret[0]['id'];
			}else{
				header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
				die;
			}
		}

		$detailHandels = new handlers($service, "agree");
		$detailConfig  = $detailHandels->getHandle($id);

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				$huoniaoTag->assign('id', $id);
				$huoniaoTag->assign('title', $detailConfig[0]['title']);
				$huoniaoTag->assign('body', $detailConfig[0]['body']);

			}else{
				header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
				die;
			}
		}else{
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
			die;
		}

		return;

	//帮助
	}elseif($template == "help"){

		$title    = $langData['siteConfig'][19][273];    //帮助中心
		$typeid   = 0;
		$parentid = 0;

		if(!empty($id)){
			$sql = $dsql->SetQuery("SELECT `id`, `parentid`, `typename` FROM `#@__site_helpstype` WHERE `id` = '$id' LIMIT 0, 1");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				$typeid   = $id;
				$parentid = $ret[0]['parentid'];
				$title    = $ret[0]['typename'];
			}else{
				header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
				die;
			}
		}

		$huoniaoTag->assign('parentid', $parentid);
		$huoniaoTag->assign('typeid', $typeid);
		$huoniaoTag->assign('title', $title);
		return;

	//帮助详细
	}elseif($template == "help-detail"){

		$parentid = 0;

		if(empty($id)){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
			die;
		}

		$detailHandels = new handlers($service, "helpsDetail");
		$detailConfig  = $detailHandels->getHandle($id);

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				$huoniaoTag->assign('detail_id', $id);

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

				$sql = $dsql->SetQuery("SELECT `id`, `parentid`, `typename` FROM `#@__site_helpstype` WHERE `id` = ".$detailConfig['typeid']." LIMIT 0, 1");
				$ret = $dsql->dsqlOper($sql, "results");
				if($ret){
					$parentid = $ret[0]['parentid'];
				}
				$huoniaoTag->assign('parentid', $parentid);

			}
		}else{
			header("location:".$cfg_basehost."/404.html");
		}
		return;

	//公告详细
	}elseif($template == "notice-detail"){

		if(empty($id)){
			header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
			die;
		}

		$detailHandels = new handlers($service, "noticeDetail");
		$detailConfig  = $detailHandels->getHandle($id);

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				//跳转
				if($detailConfig['redirecturl']){
					header('location:'.$detailConfig['redirecturl']);
					die;
				}

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

			}
		}else{
			header("location:".$cfg_basehost."/404.html");
		}
		return;

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

			if(!is_array($moduleReturn) || $moduleReturn['state'] != 100) return '';
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
			reset($smarty->block_data[$dataindex]);

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
