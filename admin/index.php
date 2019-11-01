<?php
/**
 * 管理后台首页
 *
 * @version        $Id: index.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.Administrator
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
define('HUONIAOADMIN', "." );
require_once(dirname(__FILE__)."/inc/config.inc.php");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/templates";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "index.html";

//域名检测 s
$httpHost  = $_SERVER['HTTP_HOST'];    //当前访问域名
$reqUri    = $_SERVER['REQUEST_URI'];  //当前访问目录

//判断是否为主域名，如果不是则跳转到主域名的后台目录
if($cfg_basehost != $httpHost && $cfg_basehost != str_replace("www.", "", $httpHost)){
	header("location:".$cfg_secureAccess.$cfg_basehost.$reqUri);
	die;
}


//验证模板文件
if(file_exists($tpl."/".$templates)){


	//预览所有模块链接
	if($dopost == "getModuleArr"){

		$handler = true;
		$moduleArr = array();
		$config_path = HUONIAOINC."/config/";

		//多城市不再需要此功能，这里直接结束输出！  20180223
		echo $callback."(".json_encode($moduleArr).")";die;

		$moduleArr[] = array(
			"name" => '网站首页',
			"url"  => $cfg_secureAccess.$cfg_basehost
		);

		$siteDomainInc = "<"."?php\r\n";


		//个人会员
		$userDomainInfo = getDomain('member', 'user');
		$userChannelDomain = $userDomainInfo['domain'];
		if($cfg_userSubDomain == 0){
			$userChannelDomain = $cfg_secureAccess.$userChannelDomain;
		}elseif($cfg_userSubDomain == 1){
			$userChannelDomain = $cfg_secureAccess.$userChannelDomain.".".str_replace("www.", "", $cfg_basehost);
		}elseif($cfg_userSubDomain == 2){
			$userChannelDomain = $cfg_secureAccess.$cfg_basehost."/".$userChannelDomain;
		}

		$siteDomainInc .= "\$userDomain = '".$userChannelDomain."';\r\n";

		//企业会员
		$busiDomainInfo = getDomain('member', 'busi');
		$busiChannelDomain = $busiDomainInfo['domain'];
		if($cfg_busiSubDomain == 0){
			$busiChannelDomain = $cfg_secureAccess.$busiChannelDomain;
		}elseif($cfg_busiSubDomain == 1){
			$busiChannelDomain = $cfg_secureAccess.$busiChannelDomain.".".str_replace("www.", "", $cfg_basehost);
		}elseif($cfg_busiSubDomain == 2){
			$busiChannelDomain = $cfg_secureAccess.$cfg_basehost."/".$busiChannelDomain;
		}

		$siteDomainInc .= "\$busiDomain = '".$busiChannelDomain."';\r\n";

		//商家
		$busiDomainInfo = getDomain('business', 'config');
		$busiChannelDomain = $busiDomainInfo['domain'];

		//引入配置文件
		$serviceInc = $config_path."business.inc.php";
		if(file_exists($serviceInc)){
			require($serviceInc);
		}

		if($customSubDomain == 0){
			$busiChannelDomain = $cfg_secureAccess.$busiChannelDomain;
		}elseif($customSubDomain == 1){
			$busiChannelDomain = $cfg_secureAccess.$busiChannelDomain.".".str_replace("www.", "", $cfg_basehost);
		}elseif($customSubDomain == 2){
			$busiChannelDomain = $cfg_secureAccess.$cfg_basehost."/".$busiChannelDomain;
		}

		$siteDomainInc .= "\$businessDomain = '".$busiChannelDomain."';\r\n";

		// $moduleArr[] = array(
		// 	"name" => "商家中心",
		// 	"url" => $busiChannelDomain
		// );


		function getDomainUrl($module, $customSubDomain){
			global $cfg_secureAccess;
			global $cfg_basehost;
			$domainInfo = getDomain($module, 'config');
			$domain = $domainInfo['domain'];
			if($customSubDomain == 0){
				$domain = $cfg_secureAccess.$domain;
			}elseif($customSubDomain == 1){
				$domain = $cfg_secureAccess.$domain.".".str_replace("www.", "", $cfg_basehost);
			}elseif($customSubDomain == 2){
				$domain = $cfg_secureAccess.$cfg_basehost."/".$domain;
			}
			return $domain;
		}



		$sql = $dsql->SetQuery("SELECT `title`, `subject`, `name` FROM `#@__site_module` WHERE `state` = 0 AND `type` = 0 ORDER BY `weight`, `id`");
		$result = $dsql->dsqlOper($sql, "results");
		if(!$result){
			foreach ($result as $key => $value) {
				if(!empty($value['name'])){
					$sName = $value['name'];

					//获取功能模块配置参数
					$configHandels = new handlers($sName, "config");
					$moduleConfig  = $configHandels->getHandle();

					if(is_array($moduleConfig) && $moduleConfig['state'] == 100){
						$moduleConfig  = $moduleConfig['info'];

						//引入配置文件
						$serviceInc = $config_path.$sName.".inc.php";
						if(file_exists($serviceInc)){
							require($serviceInc);
						}
						$channelDomain = getDomainUrl($sName, $customSubDomain);

						$moduleArr[] = array(
							"name" => $value['subject'] ? $value['subject'] : $value['title'],
							"url" => $channelDomain
						);

						$siteDomainInc .= "\$".$sName."Domain = '".$channelDomain."';\r\n";

						//新闻频道增加图片频道
						// if($sName == "article"){
						// 	$sName = "pic";
						//
						// 	//获取功能模块配置参数
						// 	$configHandels = new handlers($sName, "config");
						// 	$moduleConfig  = $configHandels->getHandle();
						//
						// 	if(is_array($moduleConfig) && $moduleConfig['state'] == 100){
						// 		$moduleConfig  = $moduleConfig['info'];
						//
						// 		//引入配置文件
						// 		$serviceInc = $config_path.$sName.".inc.php";
						// 		if(file_exists($serviceInc)){
						// 			require($serviceInc);
						// 		}
						// 		$channelDomain = getDomainUrl($sName, $customSubDomain);
						//
						// 		$moduleArr[] = array(
						// 			"name" => "图片",
						// 			"url" => $channelDomain
						// 		);
						//
						// 		$siteDomainInc .= "\$".$sName."Domain = '".$channelDomain."';\r\n";
						// 	}
						// }
					}
				}
			}
		}

		$siteDomainInc .= "?".">";
		// $customIncFile = HUONIAOINC."/siteModuleDomain.inc.php";
		// $fp = @fopen($customIncFile, "w");
		// @fwrite($fp, $siteDomainInc);
		// @fclose($fp);


		//更新规则文件
		// updateHtaccess();


		echo $callback."(".json_encode($moduleArr).")";die;


	//获取消息通知
	}elseif($dopost == "getAdminNotice"){

		$noticeArr = array();

		//提现
		if(testPurview('withdraw')){
			$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__member_withdraw` WHERE `state` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			$count = $ret[0]['c'];
			if(is_numeric($count) && $count > 0){
				array_push($noticeArr, array(
					"module" => "member",
					"name"   => "提现申请",
					"id"     => "withdrawphp",
					"url"    => "member/withdraw.php",
					"count"  => $count
				));
			}
		}

		//认证
		if(testPurview('memberEdit')){
			$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__member` WHERE `certifyState` = 3 OR `licenseState` = 3");
			$ret = $dsql->dsqlOper($sql, "results");
			$count = $ret[0]['c'];
			if(is_numeric($count) && $count > 0){
				array_push($noticeArr, array(
					"module" => "member",
					"name"   => "会员认证",
					"id"     => "memberListphp",
					"url"    => "member/memberList.php",
					"count"  => $count
				));
			}
		}

		//商家入驻
		if(testPurview('businessJoin')){
			$sql = $dsql->SetQuery("SELECT l.`id` FROM `#@__business_list` l LEFT JOIN `#@__business_order` o ON o.`bid` = l.`id` WHERE l.`state` = 3 AND o.`id` != '' AND l.`cityid` in ($adminCityIds) GROUP BY l.`id`");
			$ret = $dsql->dsqlOper($sql, "totalCount");
			$count = $ret;
			if(is_numeric($count) && $count > 0){
				array_push($noticeArr, array(
					"module" => "business",
					"name"   => "商家入驻",
					"id"     => "businessJoinphp",
					"url"    => "business/businessJoin.php",
					"count"  => $count
				));
			}
		}

		//商家店铺
		if(testPurview('businessList')){
			$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__business_list` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
			$ret = $dsql->dsqlOper($sql, "results");
			$count = $ret[0]['c'];
			if(is_numeric($count) && $count > 0){
				array_push($noticeArr, array(
					"module" => "business",
					"name"   => "商家店铺",
					"id"     => "businessListphp",
					"url"    => "business/businessList.php",
					"count"  => $count
				));
			}
		}

		//探店文章审核
		// if(testPurview('editdiscovery')){
		// 	$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__business_discoverylist` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
		// 	$ret = $dsql->dsqlOper($sql, "results");
		// 	$count = $ret[0]['c'];
		// 	if(is_numeric($count) && $count > 0){
		// 		array_push($noticeArr, array(
		// 			"module" => "business",
		// 			"name"   => "探店文章",
		// 			"id"     => "discoveryListphp",
		// 			"url"    => "business/discoveryList.php",
		// 			"count"  => $count
		// 		));
		// 	}
		// }



		//查询所有可用模块
		$sql = $dsql->SetQuery("SELECT `name` FROM `#@__site_module` WHERE `state` = 0 AND `type` = 0");
		$result = $dsql->dsqlOper($sql, "results");
		if($result){
			foreach ($result as $key => $value) {

				$name = $value['name'];

				//新闻资讯
				if($name == "article"){

					if(testPurview('editarticle')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__articlelist_all` WHERE `del` = 0 AND `arcrank` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => "article",
								"name"   => "新闻资讯",
								"id"     => "articleListphpactionarticle",
								"url"    => "article/articleList.php",
								"count"  => $count
							));
						}
					}

					if(testPurview('editselfmedia')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__article_selfmedia` WHERE (`state` = 0 || (`state` != 0 &&`editstate` = 0) ) AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => "article",
								"name"   => "自媒体",
								"id"     => "selfmediaListphpactionjoin",
								"url"    => "article/selfmediaList.php",
								"count"  => $count
							));
						}
					}

				//分类信息
				}elseif($name == "info" && testPurview('editInfo')){

					$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__infolist` WHERE `arcrank` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
					$ret = $dsql->dsqlOper($sql, "results");
					$count = $ret[0]['c'];
					if(is_numeric($count) && $count > 0){
						array_push($noticeArr, array(
							"module" => $name,
							"name"   => "分类信息",
							"id"     => "infoListphp",
							"url"    => "info/infoList.php",
							"count"  => $count
						));
					}

					//二手商铺
					$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__infoshop` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
					$ret = $dsql->dsqlOper($sql, "results");
					$count = $ret[0]['c'];
					if(is_numeric($count) && $count > 0){
						array_push($noticeArr, array(
							"module" => $name,
							"name"   => "信息商铺",
							"id"     => "shopListphp",
							"url"    => "info/shopList.php",
							"count"  => $count
						));
					}

				//团购秒杀
				}elseif($name == "tuan"){

					//商家审核
					if(testPurview('tuanStore')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__tuan_store` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "团购商家",
								"id"     => "tuanStorephp",
								"url"    => "tuan/tuanStore.php",
								"count"  => $count
							));
						}
					}

					//团购审核
					if(testPurview('editTuan')){
						$sql = $dsql->SetQuery("SELECT count(l.`id`) as c FROM `#@__tuanlist` l LEFT JOIN `#@__tuan_store` s ON s.`id` = l.`sid` WHERE s.`cityid` in ($adminCityIds) AND l.`arcrank` = 0");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "团购商品",
								"id"     => "tuanListphp",
								"url"    => "tuan/tuanList.php",
								"count"  => $count
							));
						}
					}

				//房产
				}elseif($name == "house"){

					//信息订阅
					$sql = $dsql->SetQuery("SELECT count(n.`id`) as c FROM `#@__house_notice` n LEFT JOIN `#@__house_loupan` l ON l.`id` = n.`aid` WHERE n.`state` = 0 AND l.`cityid` in ($adminCityIds)");
					$ret = $dsql->dsqlOper($sql, "results");
					$count = $ret[0]['c'];
					if(is_numeric($count) && $count > 0){
						array_push($noticeArr, array(
							"module" => $name,
							"name"   => "信息订阅",
							"id"     => "houseNoticephpactionloupan",
							"url"    => "house/houseNotice.php?action=loupan",
							"count"  => $count
						));
					}

					//楼盘团购
					$sql = $dsql->SetQuery("SELECT count(n.`id`) as c FROM `#@__house_loupantuan` n LEFT JOIN `#@__house_loupan` l ON l.`id` = n.`aid` WHERE n.`state` = 0 AND l.`cityid` in ($adminCityIds)");
					$ret = $dsql->dsqlOper($sql, "results");
					$count = $ret[0]['c'];
					if(is_numeric($count) && $count > 0){
						array_push($noticeArr, array(
							"module" => $name,
							"name"   => "楼盘团购",
							"id"     => "houseTuanphp",
							"url"    => "house/houseTuan.php",
							"count"  => $count
						));
					}

					//中介公司
					if(testPurview('zjComEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__house_zjcom` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "房产中介",
								"id"     => "zjComListphp",
								"url"    => "house/zjComList.php",
								"count"  => $count
							));
						}
					}

					// 经纪人
					if(testPurview('zjUserEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__house_zjuser` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "经纪人",
								"id"     => "zjUserListphp",
								"url"    => "house/zjUserList.php",
								"count"  => $count
							));
						}
					}

					//二手房
					if(testPurview('houseSaleEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__house_sale` WHERE `state` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "二手房",
								"id"     => "houseSalephp",
								"url"    => "house/houseSale.php",
								"count"  => $count
							));
						}
					}

					//出租房
					if(testPurview('houseZuEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__house_zu` WHERE `state` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "出租房",
								"id"     => "houseZuphp",
								"url"    => "house/houseZu.php",
								"count"  => $count
							));
						}
					}

					//写字楼
					if(testPurview('houseXzlEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__house_xzl` WHERE `state` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "写字楼",
								"id"     => "houseXzlphp",
								"url"    => "house/houseXzl.php",
								"count"  => $count
							));
						}
					}

					//商铺
					if(testPurview('houseSpEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__house_sp` WHERE `state` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "房产商铺",
								"id"     => "houseSpphp",
								"url"    => "house/houseSp.php",
								"count"  => $count
							));
						}
					}

					//厂房仓库
					if(testPurview('houseCfEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__house_cf` WHERE `state` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "厂房仓库",
								"id"     => "houseCfphp",
								"url"    => "house/houseCf.php",
								"count"  => $count
							));
						}
					}

                    //车位
                    if(testPurview('houseCwEdit')){
                        $sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__house_cw` WHERE `state` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
                        $ret = $dsql->dsqlOper($sql, "results");
                        $count = $ret[0]['c'];
                        if(is_numeric($count) && $count > 0){
                            array_push($noticeArr, array(
                                "module" => $name,
                                "name"   => "车位",
                                "id"     => "houseCwphp",
                                "url"    => "house/houseCw.php",
                                "count"  => $count
                            ));
                        }
                    }

				//商城
				}elseif($name == "shop"){

					//店铺审核
					if(testPurview('shopStoreEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__shop_store` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "商城店铺",
								"id"     => "shopStoreListphp",
								"url"    => "shop/shopStoreList.php",
								"count"  => $count
							));
						}
					}

					//商品审核
					if(testPurview('productEdit')){
						$sql = $dsql->SetQuery("SELECT count(l.`id`) as c FROM `#@__shop_product` l LEFT JOIN `#@__shop_store` s ON s.`id` = l.`store` WHERE s.`cityid` in ($adminCityIds) AND l.`state` = 0");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "商城商品",
								"id"     => "productListphp",
								"url"    => "shop/productList.php",
								"count"  => $count
							));
						}
					}

				//装修公司
				}elseif($name == "renovation" && testPurview('renovationStoreEdit')){

					$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__renovation_store` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
					$ret = $dsql->dsqlOper($sql, "results");
					$count = $ret[0]['c'];
					if(is_numeric($count) && $count > 0){
						array_push($noticeArr, array(
							"module" => $name,
							"name"   => "装修公司",
							"id"     => "renovationStorephp",
							"url"    => "renovation/renovationStore.php",
							"count"  => $count
						));
					}

				//招聘
				}elseif($name == "job"){

				    //公司
				    if(testPurview('jobCompanyEdit')) {
                        $sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__job_company` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
                        $ret = $dsql->dsqlOper($sql, "results");
                        $count = $ret[0]['c'];
                        if (is_numeric($count) && $count > 0) {
                            array_push($noticeArr, array(
                                "module" => $name,
                                "name" => "招聘企业",
                                "id" => "jobCompanyphp",
                                "url" => "job/jobCompany.php",
                                "count" => $count
                            ));
                        }
                    }

                    //一句话招聘
                    if(testPurview('jobSentencephptype0Edit')) {
                        $sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__job_sentence` WHERE `type` = 0 AND `state` = 0 AND `cityid` in ($adminCityIds)");
                        $ret = $dsql->dsqlOper($sql, "results");
                        $count = $ret[0]['c'];
                        if (is_numeric($count) && $count > 0) {
                            array_push($noticeArr, array(
                                "module" => $name,
                                "name" => "一句话招聘",
                                "id" => "jobSentencephptype0",
                                "url" => "job/jobSentence.php?type=0",
                                "count" => $count
                            ));
                        }
                    }

                    //一句话求职
                    if(testPurview('jobSentencephptype1Edit')) {
                        $sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__job_sentence` WHERE `type` = 1 AND `state` = 0 AND `cityid` in ($adminCityIds)");
                        $ret = $dsql->dsqlOper($sql, "results");
                        $count = $ret[0]['c'];
                        if (is_numeric($count) && $count > 0) {
                            array_push($noticeArr, array(
                                "module" => $name,
                                "name" => "一句话求职",
                                "id" => "jobSentencephptype1",
                                "url" => "job/jobSentence.php?type=1",
                                "count" => $count
                            ));
                        }
                    }

				//外卖餐厅
				}elseif($name == "waimai"){

					if(testPurview("waimaiOrder")){
						$sql = $dsql->SetQuery("SELECT count(o.`id`) as c FROM `#@__waimai_order` o LEFT JOIN `#@__waimai_shop` s ON s.`id` = o.`sid` WHERE s.`cityid` in ($adminCityIds) AND o.`state` = 2");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "外卖订单",
								"id"     => "waimaiOrderphp",
								"url"    => "waimai/waimaiOrder.php",
								"count"  => $count
							));
						}
					}

					if(testPurview("paotuiOrder")){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__paotui_order` WHERE `state` = 3");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "跑腿订单",
								"id"     => "paotuiOrderphp",
								"url"    => "waimai/paotuiOrder.php",
								"count"  => $count
							));
						}
					}

				//汽车商家
				}elseif($name == "car"){

					//经销商
					if(testPurview('storeEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__car_store` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "经销商",
								"id"     => "storeListphp",
								"url"    => "car/storeList.php",
								"count"  => $count
							));
						}
					}

					// 顾问
					if(testPurview('gwUserEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__car_adviser` WHERE `state` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "顾问",
								"id"     => "gwUserListphp",
								"url"    => "car/gwUserList.php",
								"count"  => $count
							));
						}
					}

					//二手车
					if(testPurview('carEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__car_list` WHERE `state` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "二手车",
								"id"     => "carListphp",
								"url"    => "car/carList.php",
								"count"  => $count
							));
						}
					}

				//自助建站
				}elseif($name == "website" && testPurview('websiteEdit')){

					$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__website` WHERE `state` = 0");
					$ret = $dsql->dsqlOper($sql, "results");
					$count = $ret[0]['c'];
					if(is_numeric($count) && $count > 0){
						array_push($noticeArr, array(
							"module" => $name,
							"name"   => "自助建站",
							"id"     => "websitephp",
							"url"    => "website/website.php",
							"count"  => $count
						));
					}

				//贴吧社区
				}elseif($name == "tieba" && testPurview('tiebaEdit')){

					$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__tieba_list` WHERE `state` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
					$ret = $dsql->dsqlOper($sql, "results");
					$count = $ret[0]['c'];
					if(is_numeric($count) && $count > 0){
						array_push($noticeArr, array(
							"module" => $name,
							"name"   => "贴吧社区",
							"id"     => "tiebaListphp",
							"url"    => "tieba/tiebaList.php",
							"count"  => $count
						));
					}

				//活动
				}elseif($name == "huodong" && testPurview('huodongEdit')){

					$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__huodong_list` WHERE `state` = 0 AND `waitpay` = 0 AND `cityid` in ($adminCityIds)");
					$ret = $dsql->dsqlOper($sql, "results");
					$count = $ret[0]['c'];
					if(is_numeric($count) && $count > 0){
						array_push($noticeArr, array(
							"module" => $name,
							"name"   => "同城活动",
							"id"     => "huodongListphp",
							"url"    => "huodong/huodongList.php",
							"count"  => $count
						));
					}

				//家政
				}elseif($name == "homemaking"){
					//家政服务
					if(testPurview('homemakingEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__homemaking_list` WHERE `state` = 0  AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "家政服务",
								"id"     => "homemakingListphp",
								"url"    => "homemaking/homemakingList.php",
								"count"  => $count
							));
						}
					}

					//家政公司
					if(testPurview('storeEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__homemaking_store` WHERE `state` = 0  AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "家政公司",
								"id"     => "storeListphp",
								"url"    => "homemaking/storeList.php",
								"count"  => $count
							));
						}
					}

					//服务人员
					if(testPurview('personalEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__homemaking_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__homemaking_personal` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "服务人员",
									"id"     => "personalListphp",
									"url"    => "homemaking/personalList.php",
									"count"  => $count
								));
							}
						}
					}

					//保姆/月嫂
					if(testPurview('nannyEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__homemaking_nanny` WHERE `state` = 0  AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "保姆/月嫂",
								"id"     => "nannyListphp",
								"url"    => "homemaking/nannyList.php",
								"count"  => $count
							));
						}
					}

				//婚嫁
				}elseif($name == "marry"){
					//婚嫁公司
					if(testPurview('storeEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_store` WHERE `state` = 0  AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "婚嫁公司",
								"id"     => "marrystoreListphp",
								"url"    => "marry/marrystoreList.php",
								"count"  => $count
							));
						}
					}

					//婚宴场地
					if(testPurview('marryhotelfieldEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_hotelfield` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "婚宴场地",
									"id"     => "marryhotelfieldListphp",
									"url"    => "marry/marryhotelfieldList.php",
									"count"  => $count
								));
							}
						}
					}

					//婚宴菜单
					if(testPurview('marryhotelmenuEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_hotelmenu` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "婚宴菜单",
									"id"     => "marryhotelmenuListphp",
									"url"    => "marry/marryhotelmenuList.php",
									"count"  => $count
								));
							}
						}
					}

					//主持人
					if(testPurview('marryhostEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_host` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "婚宴主持",
									"id"     => "marryhostListphp",
									"url"    => "marry/marryhostList.php",
									"count"  => $count
								));
							}
						}
					}

					//婚车
					if(testPurview('weddingcarEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_weddingcar` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "婚车管理",
									"id"     => "weddingcarListphp",
									"url"    => "marry/weddingcarList.php",
									"count"  => $count
								));
							}
						}
					}

					//案例
					if(testPurview('marryplancaseEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_plancase` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "商家案例",
									"id"     => "marryplancaseListphp",
									"url"    => "marry/marryplancaseList.php",
									"count"  => $count
								));
							}
						}
					}

					//商家套餐
					if(testPurview('marryplanmealEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_planmeal` WHERE `type` = 0  AND `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "商家套餐",
									"id"     => "marryplanmealListphp",
									"url"    => "marry/marryplanmealList.php",
									"count"  => $count
								));
							}
						}
					}

					//婚纱摄影套餐
					if(testPurview('marryplanmealEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_planmeal` WHERE `type` = 1  AND `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "婚纱摄影",
									"id"     => "marryplanmealListphp",
									"url"    => "marry/marryplanmealList.php?typeid=1",
									"count"  => $count
								));
							}
						}
					}

					//摄影跟拍套餐
					if(testPurview('marryplanmealEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_planmeal` WHERE `type` = 2  AND `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "摄影跟拍",
									"id"     => "marryplanmealListphp",
									"url"    => "marry/marryplanmealList.php?typeid=2",
									"count"  => $count
								));
							}
						}
					}

					//珠宝首饰套餐
					if(testPurview('marryplanmealEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_planmeal` WHERE `type` = 3  AND `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "珠宝首饰",
									"id"     => "marryplanmealListphp",
									"url"    => "marry/marryplanmealList.php?typeid=3",
									"count"  => $count
								));
							}
						}
					}

					//摄像跟拍套餐
					if(testPurview('marryplanmealEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_planmeal` WHERE `type` = 4  AND `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "摄像跟拍",
									"id"     => "marryplanmealListphp",
									"url"    => "marry/marryplanmealList.php?typeid=4",
									"count"  => $count
								));
							}
						}
					}

					//新娘跟妆套餐
					if(testPurview('marryplanmealEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_planmeal` WHERE `type` = 5  AND `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "新娘跟妆",
									"id"     => "marryplanmealListphp",
									"url"    => "marry/marryplanmealList.php?typeid=5",
									"count"  => $count
								));
							}
						}
					}

					//婚纱礼服套餐
					if(testPurview('marryplanmealEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__marry_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__marry_planmeal` WHERE `type` = 6  AND `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "婚纱礼服",
									"id"     => "marryplanmealListphp",
									"url"    => "marry/marryplanmealList.php?typeid=6",
									"count"  => $count
								));
							}
						}
					}

				//旅游
				}elseif($name == "travel"){
					//旅游公司
					if(testPurview('travelstoreEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__travel_store` WHERE `state` = 0  AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "旅游公司",
								"id"     => "travelstoreListphp",
								"url"    => "travel/travelstoreList.php",
								"count"  => $count
							));
						}
					}

					//旅游视频
					if(testPurview('travelvideoEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__travel_video` WHERE `state` = 0 ");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "旅游视频",
								"id"     => "travelvideoListphp",
								"url"    => "travel/travelvideoList.php",
								"count"  => $count
							));
						}
					}

					//旅游攻略
					if(testPurview('travelstrategyEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__travel_strategy` WHERE `state` = 0 ");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "旅游攻略",
								"id"     => "travelstrategyListphp",
								"url"    => "travel/travelstrategyList.php",
								"count"  => $count
							));
						}
					}

					//旅游租车
					if(testPurview('travelrentcarEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__travel_rentcar` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "旅游租车",
									"id"     => "travelrentcarListphp",
									"url"    => "travel/travelrentcarList.php",
									"count"  => $count
								));
							}
						}
					}

					//旅游酒店
					if(testPurview('travelhotelEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__travel_hotel` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "旅游酒店",
									"id"     => "travelhotelListphp",
									"url"    => "travel/travelhotelList.php",
									"count"  => $count
								));
							}
						}
					}

					//景点门票
					if(testPurview('travelticketEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__travel_ticket` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "景点门票",
									"id"     => "travelticketListphp",
									"url"    => "travel/travelticketList.php",
									"count"  => $count
								));
							}
						}
					}

					//旅游签证
					if(testPurview('travelvisaEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__travel_visa` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "旅游签证",
									"id"     => "travelvisaListphp",
									"url"    => "travel/travelvisaList.php",
									"count"  => $count
								));
							}
						}
					}

					//周边游
					if(testPurview('travelagencyEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__travel_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__travel_agency` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "周边游",
									"id"     => "travelagencyListphp",
									"url"    => "travel/travelagencyList.php",
									"count"  => $count
								));
							}
						}
					}


				}elseif($name == "education"){//教育
					//教育公司
					if(testPurview('educationstoreEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__education_store` WHERE `state` = 0  AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "教育公司",
								"id"     => "educationstoreListphp",
								"url"    => "education/educationstoreList.php",
								"count"  => $count
							));
						}
					}

					//教育家教
					if(testPurview('educationfamilyEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__education_tutor` WHERE `state` = 0  AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "教育公司",
								"id"     => "educationfamilyListphp",
								"url"    => "education/educationfamilyList.php",
								"count"  => $count
							));
						}
					}

					//教育留言
					if(testPurview('educationWord')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__education_word` WHERE `state` = 0  AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "教育留言",
								"id"     => "educationWordphp",
								"url"    => "education/educationWord.php",
								"count"  => $count
							));
						}
					}

					//教育课程
					if(testPurview('educationcoursesEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__education_courses` WHERE `state` = 0 ");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "教育课程",
								"id"     => "educationcoursesListphp",
								"url"    => "education/educationcoursesList.php",
								"count"  => $count
							));
						}
					}

					//教育教师
					if(testPurview('educationteacherEdit')){
						$sql = $dsql->SetQuery("SELECT `id` FROM `#@__education_store` WHERE  `cityid` in ($adminCityIds)");
						$res = $dsql->dsqlOper($sql, "results");
						$ids = '';
						if(!empty($res)){
							foreach($res as $row){
								$ids .= $row['id'] . ',';
							}
						}
						$ids = rtrim($ids, ',');
						if($ids){
							$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__education_teacher` WHERE `state` = 0  AND `company` in ($ids)");
							$ret = $dsql->dsqlOper($sql, "results");
							$count = $ret[0]['c'];
							if(is_numeric($count) && $count > 0){
								array_push($noticeArr, array(
									"module" => $name,
									"name"   => "教育教师",
									"id"     => "educationteacherListphp",
									"url"    => "education/educationteacherList.php",
									"count"  => $count
								));
							}
						}
					}

				}elseif($name == "pension"){//养老
					//养老公司
					if(testPurview('pensionstoreEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__pension_store` WHERE `state` = 0  AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "养老机构",
								"id"     => "pensionstoreListphp",
								"url"    => "pension/pensionstoreList.php",
								"count"  => $count
							));
						}
					}

					//老人信息
					if(testPurview('pensionelderlyEdit')){
						$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__pension_elderly` WHERE `state` = 0  AND `cityid` in ($adminCityIds)");
						$ret = $dsql->dsqlOper($sql, "results");
						$count = $ret[0]['c'];
						if(is_numeric($count) && $count > 0){
							array_push($noticeArr, array(
								"module" => $name,
								"name"   => "老人信息",
								"id"     => "pensionelderlyListphp",
								"url"    => "pension/pensionelderlyList.php",
								"count"  => $count
							));
						}
					}
					
				}

			}
		}



		//分销商
		if(testPurview('fenxiaoUser')){

			$sql = $dsql->SetQuery("SELECT count(`id`) as c FROM `#@__member_fenxiao_user` WHERE `state` = 0");
			$ret = $dsql->dsqlOper($sql, "results");
			$count = $ret[0]['c'];
			if(is_numeric($count) && $count > 0){
				array_push($noticeArr, array(
					"module" => $name,
					"name"   => "分销商",
					"id"     => "fenxiaoUser",
					"url"    => "member/fenxiaoUser.php",
					"count"  => $count
				));
			}
		}

		//查询消息通知
		$sql = $dsql->SetQuery("SELECT count(`id`) c FROM `#@__site_admin_notice`");
		$ret = $dsql->dsqlOper($sql, "results");
		$hasnew = $ret[0]['c'];

		echo $callback."({'data': ".json_encode($noticeArr).", 'hasnew': ".$hasnew."})";die;


	//清除消息通知
	}elseif($dopost == "clearAdminNotice"){
		$sql = $dsql->SetQuery("DELETE FROM `#@__site_admin_notice`");
		$dsql->dsqlOper($sql, "results");
		die;
	
	}elseif($dopost == "checkOnlineUserCount"){
		if(isMobile()){
			echo '{"state":200,"info":"cancel"}';
			die;
		}
		$r = checkOnlineUserCount($time, $max, $speed);
		if($r){
			echo '{"state":100,"info":"ok"}';
		}else{
			echo '{"state":200,"info":"cancel"}';
		}
		die;
	}

	require(HUONIAODATA."/admin/config_permission.php");

	//配置
	$menuId = $menuData[0]['menuId'];
	if(!empty($menuData[0]['subMenu'])){
		$html_ = array();
		$span  = array();
		$dataHtml = "";
		foreach($menuData[0]['subMenu'] as $key => $val){
			//循环终级菜单
			$html__ = array();
			foreach($val['subMenu'] as $f_key => $f_val){
				$value = $f_val['menuUrl'];
				if(strpos($value, "/") !== false){
					$value = explode("/", $value);
					$value = $value[1];
				}
				$value = preg_replace('/\.php(\?action\=)?/', '', $value);
				$value = preg_replace('/\.php(\?type\=)?/', '', $value);
				$value = preg_replace('/\?action\=/', '', $value);
				$value = preg_replace('/\?type\=/', '', $value);
				$value = preg_replace('/&/', '', $value);
				$value = preg_replace('/=1/', '', $value);
				//验证权限
				if(testPurview($value)){
					array_push($html__, '<a href="'.$menuId.'/'.$f_val['menuUrl'].'">'.$f_val['menuName'].'</a>');
				}
			}
			//如果终级菜单不为空，则拼接菜单分组以及菜单列表
			if($html__){
				array_push($html_, '<dd>'.join("", $html__).'</dd>');
				array_push($span, '<span>'.$val['menuName'].'</span>');
			}
		}

		//如果菜单分组不为空，则拼接最外层代码
		if($html_){
			$html = array();
			array_push($html, '<div class="sub-nav clearfix" id="'.$menuId.'"><dl class="clearfix">');
			array_push($html, '<dt>'.join("", $span).'</dt>'.join("", $html_).'</dl></div>');
			$dataHtml = '<li class="sub-li"><a href="javascript:;" class="sub-title">'.$menuData[0]['menuName'].'</a>'.join("", $html).'</li>';
		}
		$huoniaoTag->assign('configData', $dataHtml);
	}

	//用户
	$menuId = $menuData[1]['menuId'];
	if(!empty($menuData[1]['subMenu'])){
		$html_ = array();
		$span  = array();
		$dataHtml = "";
		foreach($menuData[1]['subMenu'] as $key => $val){
			//循环终级菜单
			$html__ = array();
			foreach($val['subMenu'] as $f_key => $f_val){
				$value = $f_val['menuUrl'];
				if(strpos($value, "/") !== false){
					$value = explode("/", $value);
					$value = $value[1];
				}
				$value = preg_replace('/\.php(\?action\=)?/', '', $value);
				$value = preg_replace('/\.php(\?type\=)?/', '', $value);
				$value = preg_replace('/\?action\=/', '', $value);
				$value = preg_replace('/\?type\=/', '', $value);
				$value = preg_replace('/&/', '', $value);
				$value = preg_replace('/=1/', '', $value);
				//验证权限
				if(testPurview($value)){
					array_push($html__, '<a href="'.$menuId.'/'.$f_val['menuUrl'].'">'.$f_val['menuName'].'</a>');
				}
			}
			//如果终级菜单不为空，则拼接菜单分组以及菜单列表
			if($html__){
				array_push($span, '<span>'.$val['menuName'].'</span>');
				array_push($html_, '<dd>'.join("", $html__).'</dd>');
			}
		}

		//如果菜单分组不为空，则拼接最外层代码
		if($html_){
			$html = array();
			array_push($html, '<div class="sub-nav clearfix" id="'.$menuId.'"><dl class="clearfix">');
			array_push($html, '<dt>'.join("", $span).'</dt>'.join("", $html_).'</dl></div>');
			$dataHtml = '<li class="sub-li"><a href="javascript:;" class="sub-title">'.$menuData[1]['menuName'].'</a>'.join("", $html).'</li>';
		}
		$huoniaoTag->assign('memberData', $dataHtml);
	}

	//模块
	$sql = $dsql->SetQuery("SELECT `id`, `parentid`, `icon`, `title`, `subject`, `name`, `subnav` FROM `#@__site_module` WHERE `state` = 0 AND `type` = 0 ORDER BY `weight`, `id`");
	$result = $dsql->dsqlOper($sql, "results");
	if($result){//如果有子类
		$html = array();
		$type = array();
		$list = array();
		$info = array();
		$dataHtml = "";
		$i = 0;

		foreach($result as $f_key => $f_val){
			$list_ = array();
			//拼接模块列表
			foreach($result as $s_key => $s_val){
				if($s_val['parentid'] == $f_val['id']){
					$navdata = json_decode($s_val['subnav'], true);
					$info_ = array();
					$info__ = array();
					//拼接最终链接
					foreach($navdata as $s_type){
						$info___ = array();
						foreach($s_type['subMenu'] as $s_list){
							$href = $s_list['menuUrl'];
							if(strpos($href, "/") === false){
								$href = $s_val['name']."/".$href;
							}

							$value = $s_list['menuUrl'];
							if(strpos($value, "/") !== false){
								$value = explode("/", $value);
								$value = $value[1];
							}
							$value = preg_replace('/\.php(\?action\=)?/', '', $value);
							$value = preg_replace('/\.php(\?type\=)?/', '', $value);
							$value = preg_replace('/\?action\=/', '', $value);
							$value = preg_replace('/\?type\=/', '', $value);
							$value = preg_replace('/&/', '', $value);
							$value = preg_replace('/=1/', '', $value);
							//验证权限
							if(testPurview($value)){
								array_push($info___, '<a href="'.$href.'">'.$s_list['menuName'].'</a>');
							}
						}

						if($info___){
							//链接分类
							array_push($info__, '<span>'.$s_type['menuName'].'</span>');
							//最终链接
							array_push($info_, '<dd class="hide">'.join("", $info___).'</dd>');
						}
					}

					//如果链接不为空，则拼接外层代码
					if($info_){

						global $cfg_staticVersion;
						$icon = strstr($s_val['icon'], '/') ? $s_val['icon'] : (strstr($s_val['icon'], '=') || !strstr($s_val['icon'], '.') ? getFilePath($s_val['icon']) : '/static/images/admin/nav/' . $s_val['icon']);

						array_push($info, '<div class="hide" id="'.$s_val['name'].'"><dl class="clearfix"><dt>'.join("", $info__).'</dt>'.join("", $info_)."</dl></div>");
						array_push($list_, '<li data-id="'.$s_val['name'].'"><a href="javascript:;"><s><img src="'.$icon.'?v='.$cfg_staticVersion.'" /></s>'.($s_val['subject'] ? $s_val['subject'] : $s_val['title']).'</a></li>');
					}

				}
			}

			if($f_val['parentid'] == 0 && $list_){
				//第一个分组和第一个模块为显示状态
				$cla = "";
				$cla_ = " hide";
				if($i == 0){
					$cla = " class='selected'";
					$cla_ = "";
				}

				//模块分组
				array_push($type, '<li'.$cla.'><a href="javascript:;">'.$f_val['title'].'</a></li>');

				//模块列表
				array_push($list, '<ul class="clearfix'.$cla_.'">'.join("", $list_).'</ul>');
				$i++;
			}
		}
		if($info){
			array_push($html, '<div class="sub-nav clearfix" id="module">');
			// array_push($html, '<div class="sub-top clearfix"><ul class="tab clearfix" id="tab">'.join("", $type).'</ul>');
			// array_push($html, '<ul class="tool-r clearfix"><li class="selected"><a href="javascript:;" id="editModelList">编辑模块</a></li></ul>');
			// array_push($html, '</div>');
			array_push($html, '<div class="model-list" id="modelList">'.join("", $list).'</div>');
			array_push($html, '<div class="model-info hide" id="modelInfo">'.join("", $info).'</div>');
			array_push($html, '</div>');
			$dataHtml = '<li class="sub-li"><a href="javascript:;" class="sub-title">'.$menuData[2]['menuName'].'</a>'.join("", $html).'</li>';
		}

		$huoniaoTag->assign('moduleData', $dataHtml);
	}

	//手机
	$menuId = $menuData[3]['menuId'];
	if(!empty($menuData[3]['subMenu'])){
		$html_ = array();
		$span  = array();
		$dataHtml = "";
		foreach($menuData[3]['subMenu'] as $key => $val){
			//循环终级菜单
			$html__ = array();
			foreach($val['subMenu'] as $f_key => $f_val){
				$value = $f_val['menuUrl'];
				if(strpos($value, "/") !== false){
					$value = explode("/", $value);
					$value = $value[1];
				}
				$value = preg_replace('/\.php(\?action\=)?/', '', $value);
				$value = preg_replace('/\.php(\?type\=)?/', '', $value);
				$value = preg_replace('/\?action\=/', '', $value);
				$value = preg_replace('/\?type\=/', '', $value);
				$value = preg_replace('/&/', '', $value);
				$value = preg_replace('/=1/', '', $value);
				//验证权限
				if(testPurview($value)){
					array_push($html__, '<a href="'.$menuId.'/'.$f_val['menuUrl'].'">'.$f_val['menuName'].'</a>');
				}
			}
			//如果终级菜单不为空，则拼接菜单分组以及菜单列表
			if($html__){
				array_push($span, '<span>'.$val['menuName'].'</span>');
				array_push($html_, '<dd>'.join("", $html__).'</dd>');
			}
		}

		//如果菜单分组不为空，则拼接最外层代码
		if($html_){
			$html = array();
			array_push($html, '<div class="sub-nav clearfix" id="'.$menuId.'"><dl class="clearfix">');
			array_push($html, '<dt>'.join("", $span).'</dt>'.join("", $html_).'</dl></div>');
			$dataHtml = '<li class="sub-li"><a href="javascript:;" class="sub-title">'.$menuData[3]['menuName'].'</a>'.join("", $html).'</li>';
		}
		$huoniaoTag->assign('mobileData', $dataHtml);
	}

	//微信
	$menuId = $menuData[4]['menuId'];
	if(!empty($menuData[4]['subMenu'])){
		$html_ = array();
		$span  = array();
		$dataHtml = "";
		foreach($menuData[4]['subMenu'] as $key => $val){
			//循环终级菜单
			$html__ = array();
			foreach($val['subMenu'] as $f_key => $f_val){
				$value = $f_val['menuUrl'];
				if(strpos($value, "/") !== false){
					$value = explode("/", $value);
					$value = $value[1];
				}
				$value = preg_replace('/\.php(\?action\=)?/', '', $value);
				$value = preg_replace('/\.php(\?type\=)?/', '', $value);
				$value = preg_replace('/\?action\=/', '', $value);
				$value = preg_replace('/\?type\=/', '', $value);
				$value = preg_replace('/&/', '', $value);
				$value = preg_replace('/=1/', '', $value);
				//验证权限
				if(testPurview($value)){
					array_push($html__, '<a href="'.$menuId.'/'.$f_val['menuUrl'].'">'.$f_val['menuName'].'</a>');
				}
			}
			//如果终级菜单不为空，则拼接菜单分组以及菜单列表
			if($html__){
				array_push($span, '<span>'.$val['menuName'].'</span>');
				array_push($html_, '<dd>'.join("", $html__).'</dd>');
			}
		}

		//如果菜单分组不为空，则拼接最外层代码
		if($html_){
			$html = array();
			array_push($html, '<div class="sub-nav clearfix" id="'.$menuId.'"><dl class="clearfix">');
			array_push($html, '<dt>'.join("", $span).'</dt>'.join("", $html_).'</dl></div>');
			$dataHtml = '<li class="sub-li"><a href="javascript:;" class="sub-title">'.$menuData[4]['menuName'].'</a>'.join("", $html).'</li>';
		}
		$huoniaoTag->assign('wechatData', $dataHtml);
	}

	//商家
	$menuId = $menuData[5]['menuId'];
	if(!empty($menuData[5]['subMenu'])){
		$html_ = array();
		$span  = array();
		$dataHtml = "";
		foreach($menuData[5]['subMenu'] as $key => $val){
			//循环终级菜单
			$html__ = array();
			foreach($val['subMenu'] as $f_key => $f_val){
				$value = $f_val['menuUrl'];
				if(strpos($value, "/") !== false){
					$value = explode("/", $value);
					$value = $value[1];
				}
				$value = preg_replace('/\.php(\?action\=)?/', '', $value);
				$value = preg_replace('/\.php(\?type\=)?/', '', $value);
				$value = preg_replace('/\?action\=/', '', $value);
				$value = preg_replace('/\?type\=/', '', $value);
				$value = preg_replace('/&/', '', $value);
				$value = preg_replace('/=1/', '', $value);
				//验证权限
				if(testPurview($value)){
					$href = $f_val['menuUrl'];
					if(strpos($href, "/") === false){
						$href = $menuId.'/'.$href;
					}

					array_push($html__, '<a href="'.$href.'">'.$f_val['menuName'].'</a>');
				}
			}
			//如果终级菜单不为空，则拼接菜单分组以及菜单列表
			if($html__){
				array_push($span, '<span>'.$val['menuName'].'</span>');
				array_push($html_, '<dd>'.join("", $html__).'</dd>');
			}
		}

		//如果菜单分组不为空，则拼接最外层代码
		if($html_){
			$html = array();
			array_push($html, '<div class="sub-nav clearfix" id="'.$menuId.'"><dl class="clearfix">');
			array_push($html, '<dt>'.join("", $span).'</dt>'.join("", $html_).'</dl></div>');
			$dataHtml = '<li class="sub-li"><a href="javascript:;" class="sub-title">'.$menuData[5]['menuName'].'</a>'.join("", $html).'</li>';
		}
		$huoniaoTag->assign('businessData', $dataHtml);
	}


	//APP
	$menuId = $menuData[6]['menuId'];
	if(!empty($menuData[6]['subMenu'])){
		$html_ = array();
		$span  = array();
		$dataHtml = "";
		foreach($menuData[6]['subMenu'] as $key => $val){
			//循环终级菜单
			$html__ = array();
			foreach($val['subMenu'] as $f_key => $f_val){
				$value = $f_val['menuUrl'];
				if(strpos($value, "/") !== false){
					$value = explode("/", $value);
					$value = $value[1];
				}
				$value = preg_replace('/\.php(\?action\=)?/', '', $value);
				$value = preg_replace('/\.php(\?type\=)?/', '', $value);
				$value = preg_replace('/\?action\=/', '', $value);
				$value = preg_replace('/\?type\=/', '', $value);
				$value = preg_replace('/&/', '', $value);
				$value = preg_replace('/=1/', '', $value);
				//验证权限
				if(testPurview($value)){
					$href = $f_val['menuUrl'];
					if(strpos($href, "/") === false){
						$href = $menuId.'/'.$href;
					}

					array_push($html__, '<a href="'.$href.'">'.$f_val['menuName'].'</a>');
				}
			}
			//如果终级菜单不为空，则拼接菜单分组以及菜单列表
			if($html__){
				array_push($span, '<span>'.$val['menuName'].'</span>');
				array_push($html_, '<dd>'.join("", $html__).'</dd>');
			}
		}

		//如果菜单分组不为空，则拼接最外层代码
		if($html_){
			$html = array();
			array_push($html, '<div class="sub-nav clearfix" id="'.$menuId.'"><dl class="clearfix">');
			array_push($html, '<dt>'.join("", $span).'</dt>'.join("", $html_).'</dl></div>');
			$dataHtml = '<li class="sub-li"><a href="javascript:;" class="sub-title">'.$menuData[6]['menuName'].'</a>'.join("", $html).'</li>';
		}
		$huoniaoTag->assign('appData', $dataHtml);
	}


	//插件
	if(testPurview("plugins")){
		$menuId = $menuData[7]['menuId'];
		$dataHtml = '<li><a href="siteConfig/plugins.php" data-id="plugins">'.$menuData[7]['menuName'].'</a></li>';
		$huoniaoTag->assign('pluginsData', $dataHtml);
	}


	//商店
	$storeData = "";
	if(testPurview("moduleList")){
		$storeData = '<li><a href="siteConfig/store.php" data-id="store">商店</a></li>';
	}else{
	}
	$huoniaoTag->assign('storeData', $storeData);


	$userid = $userLogin->getUserID();
	$archives = $dsql->SetQuery("SELECT `mtype`, `username`, `mgroupid` FROM `#@__member` WHERE `id` = ".$userid);
	$results = $dsql->dsqlOper($archives, "results");
	$huoniaoTag->assign('username', $results[0]['username']);

	if($results[0]['mtype'] == 3){
		$sql = $dsql->SetQuery("SELECT a.`typename` FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` WHERE a.`id` = " . $results[0]['mgroupid']);
		$ret = $dsql->dsqlOper($sql, "results");
		if($ret){
			$huoniaoTag->assign('groupname', $ret[0]['typename'] . '分站管理员');
		}else{
			$huoniaoTag->assign('groupname', '未知分站管理员');
		}
	}else{
		$archives = $dsql->SetQuery("SELECT `groupname` FROM `#@__admingroup` WHERE `id` = ".$results[0]['mgroupid']);
		$results = $dsql->dsqlOper($archives, "results");
		$huoniaoTag->assign('groupname', $results[0]['groupname']);
	}

	$archives = $dsql->SetQuery("SELECT * FROM `#@__adminlogin` WHERE `userid` = ".$userid." ORDER BY `id` DESC LIMIT 0, 2");
	$results = $dsql->dsqlOper($archives, "results");
	$huoniaoTag->assign('logintime', date("Y-m-d H:i:s", $results[1]['logintime']));
	$huoniaoTag->assign('loginip', $results[1]['loginip']);

	$huoniaoTag->assign('gotopage', $gotopage);

	$huoniaoTag->assign('hour', getNowHour());
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
