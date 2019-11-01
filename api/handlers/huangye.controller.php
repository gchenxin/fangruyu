<?php

/**
 * huoniaoTag模板标签函数插件-黄页模块
 *
 * @param $params array 参数集
 * @return array
 */
function huangye($params, $content = "", &$smarty = array(), &$repeat = array()){
	$service = "huangye";
	extract ($params);
	if(empty($action)) return '';
	global $dsql;
	global $huoniaoTag;

	//获取指定分类详细信息
	if($action == "list"){

		$list_seotitle = "";

		$data = $_GET['data'];
		$typeid = $addrid = $business = 0;
		global $page;

		if(!empty($data)){
			$data = explode("-", $data);
			$typeid    = (int)$data[0];
			$addrid    = (int)$data[1];
			$business  = (int)$data[2];
			$page      = isset($data[3]) ? $data[3] : 1;

		}else{
			$typeid = $addrid = $business = 0;
			$page = 1;
		}

		$_typename = "";
		if($typeid){
			$sql = $dsql->SetQuery("SELECT * FROM `#@__business_type` WHERE `id` = $typeid");
			$ret = $dsql->dsqlOper($sql, "results");
			if($ret){
				foreach ($ret[0] as $key => $value) {
					$huoniaoTag->assign('list_'.$key, $value);
				}
				$_typename = $ret[0]['typename'];
			}
		}

		//面包屑
		global $data;
		$data = "";
		$typeArr = getParentArr("business_type", $typeid);
		$typeArr = array_reverse(parent_foreach($typeArr, "typename"));

		global $data;
		$data = "";
		$typeIds = getParentArr("business_type", $typeid);
		$typeIds = array_reverse(parent_foreach($typeIds, "id"));

		$crumbs = array();
		foreach ($typeArr as $key => $value) {
			$param = array(
				"service"     => $service,
				"template"    => "list",
				"id"          => $typeIds[$key]."-"
			);
			$url = getUrlPath($param);
			$crumbs[] = '<a href="'.$url.'">'.$value.'</a>';
		}
		$huoniaoTag->assign('list_crumbs', join("<s></s>", $crumbs));


		//分类
		$huoniaoTag->assign('typeArr', $typeArr);
		$huoniaoTag->assign('typeIds', $typeIds);
		$huoniaoTag->assign('typeid', $typeid);
		//区域
		$huoniaoTag->assign('addrid', $addrid);
		$huoniaoTag->assign('business', $business);

		$huoniaoTag->assign('page', $page);
		$huoniaoTag->assign('keywords', $keywords);

		if(!empty($addrid) || !empty($business)){
			global $data;
			$data = "";
			$addrArr = getParentArr("huangyeaddr", empty($business) ? $addrid : $business);
			$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
			$list_seotitle = join("", $addrArr);
		}

		$lj = empty($list_seotitle) ? "" : " - ";
		$list_seotitle .= empty($typeid) ? "" : $lj.$_typename;

		$huoniaoTag->assign('list_seotitle', $list_seotitle);

		return;

	//获取指定ID的详细信息
	}elseif($action == "detail"){
		$detailHandels = new handlers($service, $action);
		$detailConfig  = $detailHandels->getHandle($id);
		// print_r($detailConfig);
		// die;

		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){

				detailCheckCity("huangye", $detailConfig['id'], $detailConfig['cityid']);

				//获取分类信息
				$listHandels = new handlers($service, "typeDetail");
				$listConfig  = $listHandels->getHandle($detailConfig['typeid']);
				if(is_array($listConfig) && $listConfig['state'] == 100){
					$listConfig  = $listConfig['info'];
					if(is_array($listConfig)){
						foreach ($listConfig[0] as $key => $value) {
							$huoniaoTag->assign('list_'.$key, $value);
						}
					}
				}

				//面包屑
				global $data;
				$data = "";
				$typeArr = getParentArr("huangyetype", $detailConfig['typeid']);
				$typeArr = array_reverse(parent_foreach($typeArr, "typename"));

				global $data;
				$data = "";
				$typeIds = getParentArr("huangyetype", $detailConfig['typeid']);
				$typeIds = array_reverse(parent_foreach($typeIds, "id"));

				$crumbs = $crumbs_html = array();
				foreach ($typeArr as $key => $value) {
					$param = array(
						"service"     => $service,
						"template"    => "list",
						"id"          => $typeIds[$key]."-"
					);
					$url = getUrlPath($param);
					$crumbs[] = '<a href="'.$url.'" target="_blank">'.$value.'</a>';
					$crumbs_html[] = $value;
				}
				$huoniaoTag->assign('list_crumbs', join(" &raquo; ", $crumbs));
				$huoniaoTag->assign('list_crumbs_html', join(" ", $crumbs_html));


				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

				/*$body = $detailConfig['body'];
				$huoniaoTag->assign('detail_body', str_replace("</p>_huoniao_page_break_tag_<p>", "", $body));*/

				//更新阅读次数
				global $dsql;
				$sql = $dsql->SetQuery("UPDATE `#@__".$service."list` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "update");

			}
		}else{
			header("location:".$cfg_basehost."/404.html");
		}
		return;

	//首页
	}elseif($action == "index"){
		// $data = $_GET['data'];
		// $typeid = $addrid = $business = 0;
		// if(!empty($data)){

		// 	$data = explode("-", $data);

		// 	$typeid    = (int)$data[0];
		// 	$addrid    = (int)$data[1];
		// 	$business  = (int)$data[2];
		// 	$page      = (int)$data[3];

		// }
		//区域
		// $huoniaoTag->assign('typeid', $typeid);
		// $huoniaoTag->assign('addrid', $addrid);
		// $huoniaoTag->assign('business', $business);

		// if(!empty($addrid)){
		// 	global $data;
		// 	$data = "";
		// 	$addrArr = getParentArr("huangyeaddr", $addrid);
		// 	$addrArr = array_reverse(parent_foreach($addrArr, "typename"));
		// 	$sale_seotitle = join("", $addrArr);
		// }
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
		}

		$moduleReturn  = $moduleHandels->getHandle($param);

		//只返回数据统计信息
		if($pageData == 1){
			if(!is_array($moduleReturn) || $moduleReturn['state'] != 100){
				$pageInfo_ = array("totalCount" => 0, "gray" => 0, "audit" => 0, "refuse" => 0, "expire" => 0);
			}else{
				$moduleReturn  = $moduleReturn['info'];  //返回数据
				$pageInfo_ = $moduleReturn['pageInfo'];
			}
			$smarty->block_data[$dataindex] = array($pageInfo_);

		//正常返回
		}else{
			global $pageInfo;
			$pageInfo = array();
			if(!is_array($moduleReturn) || $moduleReturn['state'] != 100) return '';
			$moduleReturn  = $moduleReturn['info'];  //返回数据
			$pageInfo_ = $moduleReturn['pageInfo'];
			if($pageInfo_){
				//如果有分页数据则提取list键
				$moduleReturn  = $moduleReturn['list'];
				//把pageInfo定义为global变量
				$pageInfo = $pageInfo_;
			}
			$smarty->assign('pageInfo', $pageInfo);

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
