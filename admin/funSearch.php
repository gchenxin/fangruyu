<?php
/**
 * 目录导航
 *
 * @version        $Id: funSearch.php 2014-1-2 下午15:53:05 $
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
$templates = "funSearch.html";

//查找字符
function _strpos($string) {
	global $keyword;
	if(empty($keyword)) return true;
	if(function_exists('stripos'))  return stripos($string, $keyword);
	return strpos($string, $keyword);
}

/**
 *  加亮关键词
 *
 * @access    public
 * @param     string  $text  关键词
 * @return    string
 */
function redColorKeyword($text){
	global $keyword;
	if(empty($keyword)) return $text;
	$text = str_replace($keyword, '<font color="red">'.$keyword.'</font>', $text);
	return $text;
}

//验证模板文件
if(file_exists($tpl."/".$templates)){

	//js
	$jsFile = array(
		'admin/funSearch.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));

	require_once(HUONIAODATA."/admin/config_permission.php");
	if(is_array($menuData)){
		$html = array();
		$c = 0;
		foreach($menuData as $key_1 => $val_1){

			$menuId = $val_1['menuId'];

			//二级
			if(is_array($val_1['subMenu'])){
				foreach($val_1['subMenu'] as $key_2 => $val_2){

					if($val_2['menuId'] != null){
						$menuId = $val_2['menuId'];
					}

					$html_ = array();
					//三级
					if(is_array($val_2['subMenu'])){
						foreach($val_2['subMenu'] as $key_3 => $val_3){

							//四级
							if(is_array($val_3['subMenu'])){
								$html__ = array();
								//模块
								foreach($val_3['subMenu'] as $key_4 => $val_4){

									$value = $val_4['menuUrl'];
									if(strpos($value, "/") !== false){
										$value = explode("/", $value);
										$value = $value[1];
									}
									$value = preg_replace('/\.php(\?action\=)?/', '', $value);

									//验证权限
									if(testPurview($value)){
										//查找字段串
										if(_strpos($val_4['menuName']) !== false || _strpos($val_4['menuInfo']) !== false){
											array_push($html__, '<dl>');

											$href = $val_4['menuUrl'];
											if(strpos($href, "/") === false){
												$href = $menuId."/".$href;
											}

											array_push($html__, '<dt><a href="'.$href.'">'.redColorKeyword($val_4['menuName']).'</a></dt>');
											array_push($html__, '<dd>'.redColorKeyword($val_4['menuInfo']).'</dd>');
											array_push($html__, '</dl>');
											$c++;
										}
									}

								}
								if(!empty($html__)){
									array_push($html_, '<dl>');
									array_push($html_, '<dt>'.redColorKeyword($val_3['menuName']).'</dt>');
									array_push($html_, '<dd>');
									array_push($html_, join("", $html__));
									array_push($html_, '</dd></dl>');
								}

							}else{

								$value = $val_3['menuUrl'];
								if(strpos($value, "/") !== false){
									$value = explode("/", $value);
									$value = $value[1];
								}
								$value = preg_replace('/\.php(\?action\=)?/', '', $value);

								//验证权限
								if(testPurview($value)){

									//查找字段串
									if(_strpos($val_3['menuName']) !== false || _strpos($val_3['menuInfo']) !== false){
										//普通
										array_push($html_, '<dl>');
										array_push($html_, '<dt><a href="'.$menuId.'/'.$val_3['menuUrl'].'">'.redColorKeyword($val_3['menuName']).'</a></dt>');
										array_push($html_, '<dd>'.redColorKeyword($val_3['menuInfo']).'</dd>');
										array_push($html_, '</dl>');
										$c++;
									}
								}

							}

						}
					}

					if(!empty($html_)){
						array_push($html, '<div class="fun-title">&nbsp;&nbsp;'.redColorKeyword($val_1['menuName']).' => '.redColorKeyword($val_2['menuName']).'</div>');
						array_push($html, '<div class="fun-list">');
						array_push($html, join("", $html_));
						array_push($html, '</div>');
					}

				}
			}

		}
	}

	$huoniaoTag->assign('keyword', $keyword);
	$huoniaoTag->assign('count', $c);
	$huoniaoTag->assign('funSearch', !empty($html) ? join("", $html) : "<center><p style='padding-top:50px; font-size:16px; color:#f00;'>没有找到相关操作！</p></center>");
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
