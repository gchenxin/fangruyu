<?php
//系统核心配置文件
require_once(dirname(__FILE__).'/common.inc.php');

//手机号码图片化
if($action == "phoneimage"){

	if(!empty($num)){

		//转码
		$RenrenCrypt = new RenrenCrypt();
		$num = $RenrenCrypt->php_decrypt(base64_decode($num));

		//生成图像
		Header("Content-type: image/PNG");
		$str2PNG = new str2PNG($num, $size);
		$str2PNG->createImage();
	}
	die;

//输出广告代码
}elseif($action == "adjs"){

	if(!empty($id) || !empty($title)){

		$handler = true;
		include_once(HUONIAOINC."/class/myad.class.php");

		if(!empty($id)){
			$param = array("id" => $id);
		}
		if(!empty($title)){
			$param = array("title" => $title);
			$param['model'] = $model;
		}
		if(!empty($type)){
			$param["type"] = $type;
		}

		$adhtml = getMyAd($param);

		$adhtml = str_replace("\n", "", $adhtml);
		$adhtml = str_replace("\r", "", $adhtml);
		$adhtml = str_replace("\r\n", "", $adhtml);

		$adhtml = addslashes($adhtml);
		echo 'document.write("'.$adhtml.'");';die;

	}

//网址快捷方式
}elseif($action == "internetShortcut"){

	$url = $cfg_secureAccess.$cfg_basehost;
	$name = iconv("UTF-8", "GBK", $cfg_webname);

	Header("Content-type:application/octet-stream ");
	Header("Accept-Ranges:bytes ");
	header("Content-Disposition:attachment;filename=$name.url");
	echo "[DEFAULT]\r\n";
	echo "BASEURL=$url\r\n";
	echo "[$name]\r\n";
	echo "Prop3=19,11\r\n";
	echo "[InternetShortcut]\r\n";
	echo "URL=$url\r\n";
	echo "IconFile=$url/favicon.ico";

//语言包
}elseif($action == "lang"){

    //APP端多语言
    if($type == 'app'){

        //读缓存
        $appLangData_cache = $HN_memory->get('appLangData_' . $region);
        if($appLangData_cache && !HUONIAOBUG){
            $appLangData = $appLangData_cache;
        }else {
            include_once(HUONIAOINC . "/lang/app/" . $region . ".php");
            $appLangData = $lang;

            //写入缓存
            $HN_memory->set('appLangData_' . $region, $appLangData);
        }
        echo json_encode($appLangData);

    }else {
        $content = 'var langData =  ' . json_encode($langData);
        header('Content-type: application/x-javascript');
        header('Accept-Ranges: bytes');
        header('Expires: ' . gmstrftime("%a,%d %b %Y %H:%M:%S GMT", $cfg_staticVersion + 365 * 86440));
        header('Content-Length: ' . strlen($content));
        echo $content;
    }

//根据区域ID返回相应父级
}elseif($action == "getPublicParentInfo"){
	$data = getPublicParentInfo(array(
		'tab' => $tab,
		'id'  => $id,
		'type' => $type,
		'split' => $split
	));

	if($callback){
		echo $callback."(".json_encode($data).")";
	}else{
		echo json_encode($data);
	}
}
