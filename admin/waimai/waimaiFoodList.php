<?php
/**
 * 店铺管理 商品列表
 *
 * @version        $Id: list_list.php 2017-4-25 上午10:16:21 $
 * @package        HuoNiao.Shop
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */
ini_set('max_execution_time','0');
define('HUONIAOADMIN', "../" );
require_once(dirname(__FILE__)."/../inc/config.inc.php");
$dsql = new dsql($dbo);
$userLogin = new userLogin($dbo);
$tpl = dirname(__FILE__)."/../templates/waimai";
$huoniaoTag->template_dir = $tpl; //设置后台模板目录
$templates = "waimaiFoodList.html";
$dbname = "waimai_list";


//库存状态
if($action == "updateStockStatus"){
    if(!empty($id)){

        $val = (int)$val;

        if($val == 1){
            $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `stockvalid` = $val, `stock` = 0 WHERE `id` = $id");
        }else{
            $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `stockvalid` = $val WHERE `id` = $id");
        }
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            echo '{"state": 100, "info": "更新成功！"}';
    		exit();
        }else{
            echo '{"state": 200, "info": "更新失败！"}';
    		exit();
        }

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
		exit();
    }
}


//商品状态
if($action == "updateStatus"){
    if(!empty($id)){

        $val = (int)$val;

        $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `status` = $val WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            echo '{"state": 100, "info": "更新成功！"}';
    		exit();
        }else{
            echo '{"state": 200, "info": "更新失败！"}';
    		exit();
        }

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
		exit();
    }
}


//自定义属性状态
if($action == "updateNatureStatus"){
    if(!empty($id)){

        $val = (int)$val;

        $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `is_nature` = $val WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            echo '{"state": 100, "info": "更新成功！"}';
    		exit();
        }else{
            echo '{"state": 200, "info": "更新失败！"}';
    		exit();
        }

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
		exit();
    }
}





//导入商品
if($action == "import"){

  $sid = $_POST['sid'];
  $file = $_POST['file'];

    if(empty($sid) || empty($file)){
        echo '{"state": 200, "info": "参数传递失败，请刷新页面重试！"}';
        exit();
    }

    $RenrenCrypt = new RenrenCrypt();
	$fid = $RenrenCrypt->php_decrypt(base64_decode($file));

    $archives = $dsql->SetQuery("SELECT `path` FROM `#@__attachment` WHERE `id` = '$fid'");
	$results = $dsql->dsqlOper($archives, "results");
	if($results){

        set_time_limit(600);

        $path = $results[0]['path'];

        //验证文件
        if(!file_exists(HUONIAOROOT . '/uploads' . $path)){
          /* 下载文件 */
        	$httpdown = new httpdown();
        	$httpdown->OpenUrl(getFilePath($file)); # 远程文件地址
        	$httpdown->SaveToBin(HUONIAOROOT . '/uploads' . $path); # 保存路径及文件名
        	$httpdown->Close(); # 释放资源
        }

        //利用php读取excel数据
        require HUONIAOINC.'/class/PHPExcel/PHPExcel/IOFactory.php';
        $objPHPExcelReader = PHPExcel_IOFactory::load(HUONIAOROOT.'/uploads'.$path);  //加载excel文件

        //循环读取sheet
        $dataQuery = array();
        foreach($objPHPExcelReader->getWorksheetIterator() as $sheet){
            //逐行处理

            foreach($sheet->getRowIterator() as $row){
                //确定从哪一行开始读取
                if($row->getRowIndex()<2){
                    continue;
                }

                //逐列读取
                $dataArr = array();
                foreach($row->getCellIterator() as $index => $cell){
                    $data = $cell->getValue(); //获取cell中数据

                    $data = explode("|", $data)[0];

                    //商品分类
                    if($index == "E"){
                        $sql = $dsql->SetQuery("SELECT `id` FROM `#@__waimai_list_type` WHERE `sid` = $sid AND `title` = '$data'");
                        $ret = $dsql->dsqlOper($sql, "results");
                        if($ret){
                            $data = $ret[0]['id'];
                        }else{
                            $data = 0;
                        }
                    }

                    //库存状态
                    if($index == "H"){
                        $data = $data == "关闭" ? 0 : 1;
                    }

                    //商品状态
                    if($index == "J"){
                        $data = $data == "正常" ? 1 : 0;
                    }

                    //是否开启商品属性
                    if($index == "K"){
                        $data = $data == "关闭" ? 0 : 1;
                    }

                    //商品属性
                    if($index == "L"){

                        $nature = array();

                        if(!empty($data)){
                            $dataArr_ = explode("|", $data);
                            foreach ($dataArr_ as $key => $value) {
                                $dataInfo = explode(":", $value);
                                $title = explode("-", $dataInfo[0]);
                                $info = explode(",", $dataInfo[1]);

                                $infoArr = array();
                                foreach ($info as $k => $v) {
                                    $d = explode("&", $v);
                                    array_push($infoArr, array(
                                        "value" => $d[0],
                                        "price" => $d[1]
                                    ));
                                }

                                array_push($nature, array(
                                    "name" => $title[0],
                                    "data" => $infoArr
                                ));
                            }
                        }

                        $data = serialize($nature);

                    }


                    //商品图片
                    if($index == "M"){

                        global $cfg_atlasSize;
                        global $cfg_atlasType;
                        $picArr = array();

                        if(!empty($data)){
                            /* 上传配置 */
            				$config = array(
            				    "savePath" => "../../uploads/waimai/atlas/large/".date( "Y" )."/".date( "m" )."/".date( "d" )."/",
            				    "maxSize" => $cfg_atlasSize,
            				    "allowFiles" => explode("|", $cfg_atlasType)
            				);

                            global $editor_uploadDir;
                            $editor_uploadDir = "/uploads";

                            $pics = "http://img.lewaimai.com".str_replace(";", ";http://img.lewaimai.com", $data);
                            $photoArr = getRemoteImage(explode(";", $pics), $config, "waimai", "../..", false);

            				if($photoArr){
            					$photoArr = json_decode($photoArr, true);
            					if(is_array($photoArr) && $photoArr['state'] == "SUCCESS"){
            						foreach($photoArr['list'] as $key => $val){
                                        if($val['state'] == "SUCCESS" && $val['fid']){
                                            array_push($picArr, $val['fid']);
                                        }
                                    }
            					}
            				}
                        }

                        $data = join(",", $picArr);

                    }

                    //是否开启原价
                    if($index == "O"){
                        $data = $data == "关闭" ? 0 : 1;
                    }

                    //是否开启打包费
                    if($index == "Q"){
                        $data = $data == "关闭" ? 0 : 1;
                    }


                    array_push($dataArr, $data);
                }

                array_push($dataQuery, $dataArr);
            }

        }

        $insertQuery = array();
        foreach ($dataQuery as $key => $value) {

            $sort = $value[0];
            $title = $value[1];
            $price = $value[3];
            $typeid = $value[4];
            $unit = $value[2];
            $label = $value[5];
            $is_dabao = $value[16];
            $dabao_money = $value[17];
            $status = $value[9];
            $stockvalid = $value[7];
            $stock = $value[8];
            $formerprice = $value[15];
            $body = addslashes($value[6]);
            $is_nature = $value[10];
            $nature = $value[11];
            $pics = $value[12];
            array_push($insertQuery, "('$sid', '$sort', '$title', '$price', '$typeid', '$unit', '$label', '$is_dabao', '$dabao_money', '$status', '$stockvalid', '$stock', '$formerprice', '$body', '$is_nature', '$nature', '$pics')");
        }

        delPicFile($file, "delFile", "waimai");
        unlink(HUONIAOROOT.'/uploads'.$path);


        $sql = $dsql->SetQuery("INSERT INTO `#@__waimai_list` (`sid`, `sort`, `title`, `price`, `typeid`, `unit`, `label`, `is_dabao`, `dabao_money`, `status`, `stockvalid`, `stock`, `formerprice`, `body`, `is_nature`, `nature`, `pics`) VALUES ".join(", ", $insertQuery));
        $ret = $dsql->dsqlOper($sql, "results");
        if($ret){
            echo '{"state": 100, "info": "导入成功！"}';
        }else{
            echo '{"state": 200, "info": "数据插入失败，请重试！"}';
        }

    }else{
        echo '{"state": 200, "info": "文件读取失败，请重试上传！"}';
        exit();
    }
    die;

}

// 快速编辑
if($action == "fastedit"){
    if(empty($type) || $type == "id" || empty($id) || $val == ""){
        echo '{"state": 200, "info": "参数错误！"}';
        die;
    }
    // if($type != "sort"){
    //     echo '{"state": 200, "info": "操作错误！"}';
    //     die;
    // }


  if($type == "title"){
    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__$dbname` WHERE `title` = '$val' AND `id` != '$id'");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
      die('{"state": 200, "info": "商品名称已经存在！"}');
    }
  }

  $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `$type` = '$val' WHERE `id` = $id");
  $ret = $dsql->dsqlOper($sql, "update");
  if($ret == "ok"){
    die('{"state": 100, "info": "修改成功！"}');
  }else{
    die('{"state": 200, "info": "修改失败！"}');
  }
}

//删除商品-移入回收站
if($action == "delete"){
    if(!empty($id)){


        if(strstr($id, ",")){
            $id_ = explode(",", $id)[0];
            $logtitle = '批量删除商品-移入回收站';
        }else{
            $id_ = $id;
            $logtitle = '删除商品-移入回收站';
        }
        $sql = $dsql->SetQuery("SELECT t.`title`, p.`title` typename, s.`shopname` FROM `#@__$dbname` t LEFT JOIN `#@__waimai_list_type` p ON p.`id` = t.`typeid` LEFT JOIN `#@__waimai_shop` s ON s.`id` = t.`sid` WHERE t.`id` = $id_");
        $ret = $dsql->dsqlOper($sql, "results");
        if(!$ret){
          echo '{"state": 100, "info": "商品不存在！"}';
          exit();
        }
        $foodname = $ret[0]['title'];
        $typename = $ret[0]['typename'];
        $shopname = $ret[0]['shopname'];

        $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `del` = 1 WHERE `id` in ($id)");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            echo '{"state": 100, "info": "删除成功！"}';

            adminLog($logtitle, $shopname.">".$typename.">".$id."-".$foodname);
            exit();
        }else{
            echo '{"state": 200, "info": "删除失败！"}';
            exit();
        }

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
        exit();
    }
}

if($action == "destory"){

    if(!empty($id)){

        if(strstr($id, ",")){
            $id_ = explode(",", $id)[0];
            $logtitle = '批量删除商品-彻底删除';
        }else{
            $id_ = $id;
            $logtitle = '删除商品-彻底删除';
        }
        $sql = $dsql->SetQuery("SELECT t.`title`, p.`title` typename, s.`shopname` FROM `#@__$dbname` t LEFT JOIN `#@__waimai_list_type` p ON p.`id` = t.`typeid` LEFT JOIN `#@__waimai_shop` s ON s.`id` = t.`sid` WHERE t.`id` = $id_");
        $ret = $dsql->dsqlOper($sql, "results");
        if(!$ret){
            echo '{"state": 100, "info": "商品不存在！"}';
            exit();
        }
        $foodname = $ret[0]['title'];
        $typename = $ret[0]['typename'];
        $shopname = $ret[0]['shopname'];

        $sql = $dsql->SetQuery("DELETE FROM `#@__$dbname` WHERE `id` in ($id)");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            echo '{"state": 100, "info": "删除成功！"}';

            adminLog($logtitle, $shopname.">".$typename.">".$id."-".$foodname);
            exit();
        }else{
            echo '{"state": 200, "info": "删除失败！"}';
            exit();
        }

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
        exit();
    }

}

//从回收站恢复分类
if($action == "recycleback"){
    if(!empty($id)){

        $sql = $dsql->SetQuery("SELECT t.`title`, p.`title` typename, s.`shopname` FROM `#@__$dbname` t LEFT JOIN `#@__waimai_list_type` p ON p.`id` = t.`typeid` LEFT JOIN `#@__waimai_shop` s ON s.`id` = t.`sid` WHERE t.`id` = $id");
        $ret = $dsql->dsqlOper($sql, "results");
        if(!$ret){
          echo '{"state": 100, "info": "商品不存在！"}';
          exit();
        }
        $foodname = $ret[0]['title'];
        $typename = $ret[0]['typename'];
        $shopname = $ret[0]['shopname'];

        $sql = $dsql->SetQuery("UPDATE `#@__$dbname` SET `del` = 0 WHERE `id` = $id");
        $ret = $dsql->dsqlOper($sql, "update");
        if($ret == "ok"){
            echo '{"state": 100, "info": "恢复成功！"}';

            adminLog("从回收站恢复商品", $shopname.">".$typename.">".$id."-".$foodname);
            exit();
        }else{
            echo '{"state": 200, "info": "恢复失败！"}';
            exit();
        }

    }else{
        echo '{"state": 200, "info": "信息ID传输失败！"}';
        exit();
    }
}



if(empty($sid)){
    header("location:list.php?v=1");
    die;
}

$sql = $dsql->SetQuery("SELECT `shopname` FROM `#@__waimai_shop` WHERE `id` = $sid");
$ret = $dsql->dsqlOper($sql, "results");
if(!$ret){
    header("location:/404.php");
    die;
}
$shop = $ret[0];

$shopname = $shop['shopname'];



//验证模板文件
if(file_exists($tpl."/".$templates)){


    //css
	$cssFile = array(
		'admin/jquery-ui.css',
		'admin/styles.css',
		'admin/chosen.min.css',
		'admin/ace-fonts.min.css',
		'admin/select.css',
		'admin/ace.min.css',
		'admin/animate.css',
		'admin/font-awesome.min.css',
		'admin/simple-line-icons.css',
		'admin/font.css',
		// 'admin/app.css'
	);
	$huoniaoTag->assign('cssFile', includeFile('css', $cssFile));

    //js
	$jsFile = array(
		'ui/bootstrap.min.js',
		'ui/jquery.ajaxFileUpload.js',
		'admin/waimai/waimaiFoodList.js'
	);
	$huoniaoTag->assign('jsFile', includeFile('js', $jsFile));



    $huoniaoTag->assign('sid', (int)$sid);
    $huoniaoTag->assign('shopname', $shopname);
    $huoniaoTag->assign("title", $title);
    $huoniaoTag->assign("sort", (int)$sort);
    $huoniaoTag->assign("unit", $unit);
    $huoniaoTag->assign("price", $price);
    $huoniaoTag->assign("typeid", $typeid);
    $huoniaoTag->assign("typename", $typename);
    $huoniaoTag->assign("label", $label);
    $huoniaoTag->assign("saleCount", (int)$saleCount);
    $huoniaoTag->assign("stock", (int)$stock);

    $typelist = array();
    $sql = $dsql->SetQuery("SELECT * FROM `#@__waimai_list_type` WHERE `del` = 0 AND `sid` = $sid ORDER BY `sort` DESC, `id` DESC");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        $typelist = $ret;
    }
    $huoniaoTag->assign('typelist', $typelist);


    $del = empty($del) ? 0 : 1;
    $huoniaoTag->assign('isdel', $del);

    $where = " AND s.`del` = $del AND s.`sid` = $sid AND (t.`del` = 0 OR ISNULL(t.`id`))";

    //商品名称
    if(!empty($title)){
      $where .= " AND s.`title` like ('%$title%')";
    }

    //编号
    if(!empty($sort)){
      $where .= " AND s.`sort` = '$sort'";
    }

    //单位
    if(!empty($unit)){
      $where .= " AND s.`unit` like ('%$unit%')";
    }

    //价格
    if(!empty($price)){
      $where .= " AND s.`price` = $price";
    }

    //分类id
    if(!empty($typeid)){
      $where .= " AND s.`typeid` = $typeid";
    }

    //分类
    if(!empty($typename)){
      $where .= " AND t.`title` like ('%$typename%')";
    }

    //标签
    if(!empty($label)){
      $where .= " AND s.`label` like ('%$label%')";
    }

    //库存
    if(!empty($stock)){
      $where .= " AND s.`stock` = $stock";
    }

    $pageSize = 15;

    $sql = $dsql->SetQuery("SELECT s.`id`, s.`sort`, s.`title`, s.`price`, s.`typeid`, s.`unit`, s.`label`, s.`status`, s.`stockvalid`, s.`stock`, s.`is_day_limitfood`, s.`is_nature`, t.`title` typename FROM `#@__$dbname` s LEFT JOIN `#@__waimai_list_type` t ON t.`id` = s.`typeid` WHERE 1 = 1".$where." ORDER BY s.`sort` DESC, `id` DESC");

    //总条数
    $totalCount = $dsql->dsqlOper($sql, "totalCount");
    //总分页数
    $totalPage = ceil($totalCount/$pageSize);

    $p = (int)$p == 0 ? 1 : (int)$p;
    $atpage = $pageSize * ($p - 1);
    $results = $dsql->dsqlOper($sql." LIMIT $atpage, $pageSize", "results");

    // 统计销量
    $foodSale = array();
    $sql = $dsql->SetQuery("SELECT `food` FROM `#@__waimai_order` WHERE `sid` = $sid AND `state` = 1");
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        foreach ($ret as $a => $val) {
            $food = $val['food'];
            $food = unserialize($food);
            foreach ($food as $k => $v) {
                $foodSale[$v['id']] = isset($foodSale[$v['id']]) ? ($foodSale[$v['id']] + $v['count']) : $v['count'];
            }
        }
    }

    $list = array();
    foreach ($results as $key => $value) {
      $list[$key]['id']               = $value['id'];
      $list[$key]['sort']             = $value['sort'];
      $list[$key]['title']            = $value['title'];
      $list[$key]['price']            = $value['price'];
      $list[$key]['typeid']           = $value['typeid'];
      $list[$key]['typename']         = $value['typename'];
      $list[$key]['unit']             = $value['unit'];
      $list[$key]['label']            = $value['label'];
      $list[$key]['status']           = $value['status'];
      $list[$key]['stockvalid']       = $value['stockvalid'];
      $list[$key]['stock']            = $value['stock'];
      $list[$key]['is_day_limitfood'] = $value['is_day_limitfood'];
      $list[$key]['is_nature']        = $value['is_nature'];

      $list[$key]['sale'] = isset($foodSale[$value['id']]) ? $foodSale[$value['id']] : 0;


    }
    $huoniaoTag->assign("list", $list);

    $pagelist = new pagelist(array(
      "list_rows"   => $pageSize,
      "total_pages" => $totalPage,
      "total_rows"  => $totalCount,
      "now_page"    => $p
    ));
    $huoniaoTag->assign("pagelist", $pagelist->show());



    $huoniaoTag->assign('HUONIAOADMIN', HUONIAOADMIN);
	$huoniaoTag->display($templates);
}else{
	echo $templates."模板文件未找到！";
}
