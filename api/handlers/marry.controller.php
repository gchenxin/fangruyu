<?php

/**
 * huoniaoTag模板标签函数插件-婚嫁模块
 *
 * @param $params array 参数集
 * @return array
 */
function marry($params, $content = "", &$smarty = array(), &$repeat = array()){
	$service = "marry";
	extract ($params);
	if(empty($action)) return '';

	global $template;
	global $huoniaoTag;
	global $dsql;
	global $userLogin;
	global $cfg_basehost;
	global $cfg_secureAccess;

	$userid = $userLogin->getMemberID();

	if($action == "detail" || $action == "storeDetail" || $action == "store-detail" || $action == "plan-detail" || $action == "hotelmeallist" || $action == "plancaselist" || $action == "hotelmenu-detail" || $action == "planmeallist"){
		$detailHandels = new handlers($service, "storeDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id, "istype"=>$istype, "typeid"=>$typeid));
		$state = 0;
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;

				global $template;
				if($template != 'config'){
					detailCheckCity("marry", $detailConfig['id'], $detailConfig['cityid'], "store-detail");
				}

				if($action == "store-detail" || $action == "detail"){
					//更新浏览次数
					$sql = $dsql->SetQuery("UPDATE `#@__marry_store` SET `click` = `click` + 1 WHERE `id` = ".$id);
					$dsql->dsqlOper($sql, "results");
				}

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
				$state = 1;

			}

			global $langData;
			if($typeid == 4){
				$typename = $langData['marry'][2][6];
				$type = 1;
			}elseif($typeid == 5){
				$typename = $langData['marry'][2][7];
				$type = 2;
			}elseif($typeid == 7){
				$typename = $langData['marry'][2][8];
				$type = 3;
			}elseif($typeid == 8){
				$typename = $langData['marry'][2][9];
				$type = 4;
			}elseif($typeid == 9){
				$typename = $langData['marry'][2][10];
				$type = 5;
			}elseif($typeid == 10){
				$typename = $langData['marry'][2][11];
				$type = 6;
			}
			$huoniaoTag->assign('type', $type);//套餐类型
			$huoniaoTag->assign('storeState', $state);
			$huoniaoTag->assign('storeId', $id);
			$huoniaoTag->assign('typeid', $typeid ? $typeid : 0);//商家
			$huoniaoTag->assign('istype', $istype ? $istype : 1);//1：婚宴酒店;2、婚礼策划;3、婚宴套餐;
			$huoniaoTag->assign('businessid', $businessid ? $businessid : 1);//商家

		}else{
			if($action == "store-detail"){
				header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
			}
		}
	}elseif($action == "fabu"){

		if($type == "field"){//婚宴场地
			$act = "hotelfieldDetail";
		}elseif($type == "menu"){//婚宴菜单
			$act = "hotelmenuDetail";
		}elseif($type == "host"){//主持人
			$act = "hostDetail";
		}elseif($type == "rental"){//婚车
			$act = "rentalDetail";
		}elseif($type == "case"){//案例
			$act = "plancaseDetail";
		}elseif($type == "meal"){//套餐
			$act = "planmealDetail";
		}

		if($id){
			$detailHandels = new handlers($service, $act);
			$detailConfig  = $detailHandels->getHandle($id);
			if(is_array($detailConfig) && $detailConfig['state'] == 100){
				$detailConfig  = $detailConfig['info'];
				if(is_array($detailConfig)){
					foreach ($detailConfig as $key => $value) {
						$huoniaoTag->assign('detail_'.$key, $value);
					}
				}
			}else{
				header('location:'.$cfg_secureAccess.$cfg_basehost.'/404.html');
				die;
			}
		}
	}elseif($action == "comment"){
		$type = $type ? (int)$type : 0;

		if(empty($type)){
			$act = 'storeDetail';
		}elseif($type == 1){
			$act = 'rentalDetail';
		}

		$detailHandels = new handlers($service, $act);
		$detailConfig  = $detailHandels->getHandle($id);
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;
				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}
			}
		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}

		$huoniaoTag->assign('type', $type);

	}elseif($action == "comdetail"){
		$id   = (int)$id;
		$type = $type ? (int)$type : 0;
		$huoniaoTag->assign('type', $type);

        $detailHandels = new handlers("member", "commentDetail");
        $detail  = $detailHandels->getHandle(array("id" => $id));
        if(is_array($detail) && $detail['state'] == 100){
            $detail  = $detail['info'];
            foreach ($detail as $key => $value) {
                $huoniaoTag->assign('detail_'.$key, $value);
            }
        }else{
            $param = array(
				"service" => "marry",
			);
			header("location:".getUrlPath($param));
			die;
        }
    }elseif($action == "hotelfield-detail"){//场地详情
		$detailHandels = new handlers($service, "hotelfieldDetail");
		$detailConfig  = $detailHandels->getHandle($id);
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;

				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__marry_hotelfield` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

			}
			$huoniaoTag->assign('hotelfieldId', $id);

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "host-detail"){//主持人详情
		$detailHandels = new handlers($service, "hostDetail");
		$detailConfig  = $detailHandels->getHandle($id);
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;

				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__marry_host` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

			}
			$huoniaoTag->assign('hostId', $id);

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "rental-detail"){//婚车详情
		$detailHandels = new handlers($service, "rentalDetail");
		$detailConfig  = $detailHandels->getHandle($id);
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;

				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__marry_weddingcar` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

			}
			$huoniaoTag->assign('rentalId', $id);

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "plancase-detail"){//商家案例详情
		$detailHandels = new handlers($service, "plancaseDetail");
		$detailConfig  = $detailHandels->getHandle($id);
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;

				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__marry_plancase` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

			}
			$huoniaoTag->assign('plancaseId', $id);

		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "planmeal-detail"){//套餐详情
		$detailHandels = new handlers($service, "planmealDetail");
		$detailConfig  = $detailHandels->getHandle(array("id" => $id, "istype"=>$istype, "typeid"=>$typeid, "businessid"=>$businessid));
		if(is_array($detailConfig) && $detailConfig['state'] == 100){
			$detailConfig  = $detailConfig['info'];
			if(is_array($detailConfig)){//print_R($detailConfig);exit;

				//更新浏览次数
				$sql = $dsql->SetQuery("UPDATE `#@__marry_planmeal` SET `click` = `click` + 1 WHERE `id` = ".$id);
				$dsql->dsqlOper($sql, "results");

				//输出详细信息
				foreach ($detailConfig as $key => $value) {
					$huoniaoTag->assign('detail_'.$key, $value);
				}

			}
			$huoniaoTag->assign('plancaseId', $id);
			$huoniaoTag->assign('typeid', $typeid);

			$huoniaoTag->assign('istype', $istype);
			$huoniaoTag->assign('businessid', $businessid);


		}else{
			header("location:".$cfg_secureAccess.$cfg_basehost."/404.html?v=1");
		}
	}elseif($action == "list"){

		$huoniaoTag->assign('typeid', $typeid);

		global $langData;
		if($typeid == 4){
			$typename = $langData['marry'][2][6];
			$type = 1;
		}elseif($typeid == 5){
			$typename = $langData['marry'][2][7];
			$type = 2;
		}elseif($typeid == 7){
			$typename = $langData['marry'][2][8];
			$type = 3;
		}elseif($typeid == 8){
			$typename = $langData['marry'][2][9];
			$type = 4;
		}elseif($typeid == 9){
			$typename = $langData['marry'][2][10];
			$type = 5;
		}elseif($typeid == 10){
			$typename = $langData['marry'][2][11];
			$type = 6;
		}

		$huoniaoTag->assign('typename', $typename);
		$huoniaoTag->assign('type', $type);
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

		//指定数据
		}elseif(!empty($get)){
			$retArr = $moduleReturn['state'] == 100 ? $moduleReturn['info'][$get] : "";
			$retArr = is_array($retArr) ? $retArr : array();
			$smarty->block_data[$dataindex] = $retArr;

		//正常返回
		}else{

			global $pageInfo;
			if(!is_array($moduleReturn) || $moduleReturn['state'] != 100) {
				$pageInfo = array();
				$smarty->assign('pageInfo', $pageInfo);
				return '';
			}
			$moduleReturn  = $moduleReturn['info'];  //返回数据
			$pageInfo_ = $moduleReturn['pageInfo'];
			if($pageInfo_){
				//如果有分页数据则提取list键
				$moduleReturn  = $moduleReturn['list'];
				$pageInfo = $pageInfo_;
			}else{
				$pageInfo = array();
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
