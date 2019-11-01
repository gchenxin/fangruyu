<?php

/**
 * huoniaoTag模板标签函数插件-汽车模块
 *
 * @param $params array 参数集
 * @return array
 */
function car($params, $content = "", &$smarty = array(), &$repeat = array()){
	$service = "car";
	extract ($params);
	if(empty($action)) return '';

	global $template;
	global $dsql;
	global $huoniaoTag;
	global $userLogin;
	global $cfg_basehost;
	global $cfg_secureAccess;
	
	if($action == "storeDetail" || $action == "store-detail"){
		$detailHandels = new handlers($service, "storeDetail");
		$detailConfig  = $detailHandels->getHandle($id);
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];//print_R($detailConfig);exit;
			if(is_array($detailConfig)){

				global $template;
				if($template != 'config'){
					detailCheckCity("car", $detailConfig['id'], $detailConfig['cityid'], "store-detail");
				}

				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__car_store` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");//print_R($detailConfig);exit;

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
				$state = 1;

			}
			$huoniaoTag->assign('storeState', $state);
			$huoniaoTag->assign('storeId', $id);

		}else{
			if($action == "store-detail"){
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
			}
		}
	}elseif($action == "broker-detail"){
		$huoniaoTag->assign('tpl', $tpl);

		$detailHandels = new handlers($service, "adviserList");
		$detailConfig  = $detailHandels->getHandle(array("userid" => $id, "u" => $u));

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				global $template;
				if(stripos($action, 'config-car') !== false ){
					detailCheckCity($service, $detailConfig['list'][0]['id'], $detailConfig['list'][0]['cityid'], $action);
				}

				//输出详细信息
				foreach ($detailConfig['list'][0] as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

				$sql = $dsql->SetQuery("UPDATE `#@__car_adviser` SET `click` = `click` + 1 WHERE `id` = $id");
				$dsql->dsqlOper($sql, "update");

			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}
		return;
	}elseif ($action == "fabu") {
		$huoniaoTag->assign('dopost', $dopost);
		//发布成功
        if (!empty($id)) {
            $huoniaoTag->assign("id", $id);
        }
	}elseif($action == "detail" || $action == "configure"){
		$detailHandels = new handlers($service, "detail");
		$detailConfig  = $detailHandels->getHandle($id);//print_R($detailConfig);die;
		if (is_array($detailConfig) && $detailConfig['state'] == 100) {
			$detailConfig = $detailConfig['info'];
			if (is_array($detailConfig)) {
				//输出详细信息
                foreach ($detailConfig as $key => $value) {
                    $huoniaoTag->assign('detail_' . $key, $value);
                }
                //更新阅读次数
                $sql = $dsql->SetQuery("UPDATE `#@__car_list` SET `click` = `click` + 1 WHERE `state` = 1 AND `id` = " . $id);
                $dsql->dsqlOper($sql, "update");

			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}
		return;

	//资讯详情页面
	}elseif($action == "news-detail"){

		$detailHandels = new handlers($service, "newsDetail");
		$detailConfig  = $detailHandels->getHandle($id);

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				detailCheckCity("car", $detailConfig['id'], $detailConfig['cityid'], "news-detail");

                //更新阅读次数
                global $dsql;
                $sql = $dsql->SetQuery("UPDATE `#@__car_news` SET `click` = `click` + 1 WHERE `id` = ".$id);
                $dsql->dsqlOper($sql, "update");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html");
		}
		return;

	//列表
	}elseif($action == 'list'){
		$huoniaoTag->assign('store', $store);
		$huoniaoTag->assign('usertype', $usertype);
		$huoniaoTag->assign('keywords', $keywords);
		$prices = $_GET['prices'];
		$huoniaoTag->assign('prices', $prices);
		$huoniaoTag->assign('brand', $id);

		require(HUONIAOINC."/config/car.inc.php");
		$list_typename = $customSeoTitle;
		if(!empty($id)){
			$sql = $dsql->SetQuery("SELECT `typename` FROM `#@__car_brandtype` WHERE `id` = ".$id);
			$results = $dsql->dsqlOper($sql, "results");
			if($results){
				$list_typename = $results[0]['typename'];
			}
		}
		$huoniaoTag->assign('list_typename', $list_typename);
		
	}elseif($action == 'wtsell'){
    	$archives = $dsql->SetQuery("SELECT `id` FROM `#@__car_enturst` ");
		$totalCount = $dsql->dsqlOper($archives, "totalCount");
        $huoniaoTag->assign('totalCount', $totalCount);
    }

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

		//获取分类
		if($action == "type"){
			$param['type']     = $type;
			$param['page']     = $page;
			$param['pageSize'] = $pageSize;
			$param['son']      = '0';
		
		//数据列表
		}else{
			//如果是列表页面，则获取地址栏传过来的typeid
			if($template == "list" && !$typeid){
				global $typeid;
			}
			$param = array();
			$param['typeid']      = $typeid;

			$param['page']        = $page;
			$param['pageSize']    = $pageSize;

		}

		$moduleReturn  = $moduleHandels->getHandle($params);
		if(!is_array($moduleReturn) || $moduleReturn['state'] != 100) return '';

		//只返回数据统计信息
		if($pageData == 1){
			if(!is_array($moduleReturn) || $moduleReturn['state'] != 100){
				$pageInfo_ = array("totalCount" => 0, "gray" => 0, "audit" => 0, "refuse" => 0);
			}else{
				$moduleReturn  = $moduleReturn['info'];  //返回数据
				$pageInfo_ = $moduleReturn['pageInfo'];
			}
			$smarty->block_data[$dataindex] = array($pageInfo_);
		}else{
			$moduleReturn  = $moduleReturn['info'];  //返回数据
			$pageInfo_ = $moduleReturn['pageInfo'];
			if($pageInfo_){
				//如果有分页数据则提取list键
				$moduleReturn  = $moduleReturn['list'];
				//把pageInfo定义为global变量
				global $pageInfo;
				$pageInfo = $pageInfo_;
			}
			$smarty->block_data[$dataindex] = $moduleReturn;  //存储数据
		}
		
	}

	//果没有数据，直接返回null,不必再执行了
	if(!$smarty->block_data[$dataindex]) {
		$repeat = false;
		return '';
	}

	//一条数据出栈，并把它指派给$return，重复执行开关置位1
	if(list($key, $item) = each($smarty->block_data[$dataindex])){
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