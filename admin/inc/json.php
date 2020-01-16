<?php
/**
 *  异步JSON
 */

define('HUONIAOADMIN', "..");
require_once(dirname(__FILE__) . "/../inc/config.inc.php");


$autoload = true;
function classLoaderQiniu($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = HUONIAOROOT . '/api/upload/' . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('classLoaderQiniu');
require(HUONIAOROOT . '/api/upload/Qiniu/functions.php');

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

$autoload = false;

$dsql = new dsql($dbo);

$key = trim($key);

//城市管理员，只能管理管辖城市的会员
$adminAreaIDs = '';
if($userType == 3){
    $sql = $dsql->SetQuery("SELECT `mgroupid` FROM `#@__member` WHERE `id` = " . $userLogin->getUserID());
    $ret = $dsql->dsqlOper($sql, "results");
    if($ret){
        $adminCityID = $ret[0]['mgroupid'];

        global $data;
        $data = '';
        $adminAreaData = $dsql->getTypeList($adminCityID, 'site_area');
        $adminAreaIDArr = parent_foreach($adminAreaData, 'id');
        $adminAreaIDs = join(',', $adminAreaIDArr);
    }
}

//检测FTP是否可连接
if ($action == "checkFtpConn") {

    if (empty($ftpUrl)) die('{"state":"101","info":' . json_encode("请输入远程附件地址！") . '}');
    if (empty($ftpServer)) die('{"state":"101","info":' . json_encode("请输入FTP服务器地址！") . '}');
    if (empty($ftpDir)) die('{"state":"101","info":' . json_encode("请输入FTP上传目录！") . '}');
    if (empty($ftpUser)) die('{"state":"101","info":' . json_encode("请输入FTP帐号！") . '}');
    if (empty($ftpPwd)) die('{"state":"101","info":' . json_encode("请输入FTP密码！") . '}');

    $ftpConfig = array(
        "on" => 1, //是否开启
        "host" => $ftpServer, //FTP服务器地址
        "port" => $ftpPort, //FTP服务器端口
        "username" => $ftpUser, //FTP帐号
        "password" => $ftpPwd,  //FTP密码
        "attachdir" => $ftpDir,  //FTP上传目录
        "attachurl" => $ftpUrl,  //远程附件地址
        "timeout" => $ftpTimeout,  //FTP超时
        "ssl" => $ftpSSL,  //启用SSL连接
        "pasv" => $ftpPasv  //被动模式连接
    );
    $huoniao_ftp = new ftp($ftpConfig);
    $huoniao_ftp->connect();
    if ($huoniao_ftp->connectid) {

        $floder = create_check_code(10);
        if ($huoniao_ftp->ftp_mkdir($floder)) {

            $huoniao_ftp->ftp_rmdir($floder);
            echo '{"state":"100","info":' . json_encode("可以连接！") . '}';

        } else {

            echo '{"state":"200","info":' . json_encode("远程FTP无写入权限，请修改服务器权限！") . '}';

        }


    } else {
        echo '{"state":"200","info":' . json_encode("连接失败，请检查配置参数！") . '}';
    }
    die;

//检测阿里云OSS是否可连接
} elseif ($action == "checkOssConn") {

    if (empty($OSSUrl)) die('{"state":"101","info":' . json_encode("请输入服务器地址！") . '}');
    if (empty($OSSBucket)) die('{"state":"101","info":' . json_encode("请输入Bucket名称！") . '}');
    if (empty($EndPoint)) die('{"state":"101","info":' . json_encode("请输入EndPoint！") . '}');
    if (empty($OSSKeyID)) die('{"state":"101","info":' . json_encode("请输入Access Key ID！") . '}');
    if (empty($OSSKeySecret)) die('{"state":"101","info":' . json_encode("请输入Access Key Secret！") . '}');

    $OSSConfig = array(
        "bucketName" => $OSSBucket,
        "bucketName" => $OSSBucket,
        "endpoint" => $EndPoint,
        "accessKey" => $OSSKeyID,
        "accessSecret" => $OSSKeySecret
    );

    $aliyunOSS = new aliyunOSS($OSSConfig);
    if ($aliyunOSS->checkConn() == "成功") {
        echo '{"state":"100","info":' . json_encode("可以连接！") . '}';
    } else {
        echo '{"state":"200","info":' . json_encode("连接失败，请检查配置参数！") . '}';
    }
    die;

//检测七牛云是否可连接
} elseif ($action == "checkQINIUConn") {

    if (empty($access_key)) die('{"state":"101","info":' . json_encode("请输入AccessKey！") . '}');
    if (empty($secret_key)) die('{"state":"101","info":' . json_encode("请输入SecretKey！") . '}');
    if (empty($bucket)) die('{"state":"101","info":' . json_encode("请输入存储空间（bucket）！") . '}');
    if (empty($domain)) die('{"state":"101","info":' . json_encode("请输入外链域名！") . '}');

    $accessKey = $access_key;
    $secretKey = $secret_key;

    $autoload = true;
    $auth = new Auth($access_key, $secret_key);
    $bucketmanager = new BucketManager($auth);
    $bucketlists = $bucketmanager->buckets();
    if($bucketlists[0]!=null){
        if (in_array($bucket,$bucketlists[0])) {
            echo '{"state":"100","info":' . json_encode("可以连接！") . '}';
        } else {
            echo '{"state":"200","info":' . json_encode("连接失败，请检查配置参数！") . '}';
        }
    }else{
        echo '{"state":"200","info":' . json_encode("连接失败，请检查配置参数！") . '}';
    }

    die;

//检测系统默认FTP链接是否正常
} elseif ($action == "checkSystemConn") {

    $isOk = true;

    if ($cfg_ftpType == 0) {
        $ftpConfig = array(
            "on" => 1, //是否开启
            "host" => $cfg_ftpServer, //FTP服务器地址
            "port" => $cfg_ftpPort, //FTP服务器端口
            "username" => $cfg_ftpUser, //FTP帐号
            "password" => $cfg_ftpPwd,  //FTP密码
            "attachdir" => $cfg_ftpDir,  //FTP上传目录
            "attachurl" => $cfg_ftpUrl,  //远程附件地址
            "timeout" => $cfg_ftpTimeout,  //FTP超时
            "ssl" => $cfg_ftpSSL,  //启用SSL连接
            "pasv" => $cfg_ftpPasv  //被动模式连接
        );
        $huoniao_ftp = new ftp($ftpConfig);
        $huoniao_ftp->connect();
        if (!$huoniao_ftp->connectid) {
            $isOk = false;
        }
    //阿里云
    } elseif ($cfg_ftpType == 1) {
        $OSSConfig = array(
            "bucketName" => $cfg_OSSBucket,
            "endpoint" => $cfg_EndPoint,
            "accessKey" => $cfg_OSSKeyID,
            "accessSecret" => $cfg_OSSKeySecret
        );
        $aliyunOSS = new aliyunOSS($OSSConfig);
        if ($aliyunOSS->checkConn() != "成功") {
            $isOk = false;
        }
    //七牛云
    } elseif ($cfg_ftpType == 2){

      $auth = new Auth($cfg_QINIUAccessKey, $cfg_QINIUSecretKey);
      $bucketmanager = new BucketManager($auth);
      $bucketlists = $bucketmanager->buckets();
      if($bucketlists[0]!=null){
          if (!in_array($cfg_QINIUbucket,$bucketlists[0])) {
              $isOk = false;
          }
      }else{
          $isOk = false;
      }
    }

    if ($isOk) {
        echo '{"state":"100","info":' . json_encode("可以连接！") . '}';
    } else {
        echo '{"state":"200","info":' . json_encode("连接失败，请检查配置参数！") . '}';
    }
    die;

//检测启用帐号是否可用
} elseif ($action == "checkMail") {
    if (!empty($mailUser)) {
        $return = sendmail($mailUser, "系统测试邮件", "<center><br /><br />这是一封测试邮件，证明发送邮件系统正常。</center>");
        if (!empty($return)) {
            echo '{"state":"200","info":' . json_encode("邮件发送失败，请检查邮箱帐号！") . '}';
        } else {
            echo '{"state":"100","info":' . json_encode("测试邮件已发送到指定邮箱，请注意查收！&nbsp;&nbsp;如果没有收到邮件证明此帐号配置不正确 或 不支持发送邮件！") . '}';
        }
    } else {
        echo '{"state":"200","info":' . json_encode("请输入测试帐号！") . '}';
    }
    die;

//检测短信帐号是否可用
} elseif ($action == "checkSMS") {
    if (!empty($mobile)) {

        preg_match('/0?(13|14|15|17|18)[0-9]{9}/', $mobile, $matchPhone);
        if (!$matchPhone) {
            // die('{"state":"101","info":'.json_encode("手机号码格式错误！").'}');
        }

        //发送短信
        $return = sendsms($mobile, 0, "123456");
        if ($return != "ok") {
            echo '{"state":"200","info":' . json_encode("发送失败，请检查帐号配置信息！") . '}';
        } else {
            echo '{"state":"100","info":' . json_encode("测试短信已发送到指定手机，请注意查收！&nbsp;&nbsp;如果没有收到短信证明此帐号配置不正确！") . '}';
        }
    } else {
        echo '{"state":"101","info":' . json_encode("请输入手机号码！") . '}';
    }
    die;

//一键导入系统地址库
} elseif ($action == "importAddr") {
    if (!empty($type) && !empty($id)) {

        $intable = "INSERT INTO `" . $DB_PREFIX . $type . "addr` VALUES(";

        //获取所选城市的所有信息
        $addrListArr = $dsql->getTypeList($id, "site_area");

        //根据城市数据生成SQL语句
        $i = 0;
        function getSqlStr($data, $id)
        {
            global $intable;
            global $i;
            $str = array();
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $i++;

                    global $type;
                    if ($type == "house") {
                        $str[] = $intable . "'" . $i . "','" . $id . "','" . $value['typename'] . "','" . $key . "','" . GetMkTime(time()) . "','0','0');\r\n";
                    } else {
                        $str[] = $intable . "'" . $i . "','" . $id . "','" . $value['typename'] . "','" . $key . "','" . GetMkTime(time()) . "');\r\n";
                    }
                    //$str[] = $intable . "'".$i."','".$id."','".$value['typename']."','".$key."','".GetMkTime(time())."'".$el.");\r\n";
                    if ($value['lower']) {
                        $str[] = getSqlStr($value['lower'], $i);
                    }
                }
            }
            return join("", $str);
        }

        $insertSql = getSqlStr($addrListArr, 0);
        $insertArr = explode("\r\n", $insertSql);

        if (!empty($insertArr)) {

            //先清空表
            $userSql = $dsql->SetQuery("DELETE FROM `#@__" . $type . "addr`");
            $dsql->dsqlOper($userSql, "update");

            //执行导入数据
            foreach ($insertArr as $q) {
                if (!empty($q)) {
                    $dsql->dsqlOper(trim($q), "update");
                }
            }

            adminLog("导入系统地址库", $type);
            echo '{"state":"100","info":' . json_encode("导入成功！") . '}';

        } else {
            echo '{"state":"200","info":' . json_encode("系统地址库为空，导入失败！") . '}';
        }

    }
    die;

//获取会员信息
}elseif($action == "getMemberInfo"){
	if(!empty($id)){
		$userSql = $dsql->SetQuery("SELECT m.`username`, m.`nickname`, m.`company`, m.`realname`, m.`addr`, m.`money`, m.`promotion`, m.`point`, m.`email`, m.`emailCheck`, m.`phone`, m.`phoneCheck`, m.`qq`, m.`photo`, m.`sex`, m.`birthday`, m.`regtime`, m.`regip`, l.`name` as level FROM `#@__member` m LEFT JOIN `#@__member_level` l ON l.`id` = m.`level` WHERE m.`id` = ".$id);
		$userResult = $dsql->dsqlOper($userSql, "results");
		if($userResult){
			$addrname = $userResult[0]['addr'];
			if($addrname){
				$addrname = getPublicParentInfo(array('tab' => 'site_area', 'id' => $addrname, 'type' => 'typename', 'split' => ' '));
			}
			$userResult[0]['addr'] = $addrname;
			echo json_encode($userResult);
		}
	}
	die;

//模糊匹配会员
} elseif ($action == "checkUser") {
    $key = $_POST['key'];
    if (!empty($key)) {
        if($userType == 0)
            $where = "";
        if($userType == 3)
            $where = " AND `addr` in ($adminAreaIDs)";


        if($mtype == 1){
            $where = " AND `mtype` = 1";
        }elseif($mtype == 2){
            $where = " AND `mtype` = 2";
        }elseif($mtype == '1,2'){
            $where = " AND `mtype` BETWEEN 1 AND 2";
        }

        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone`, `email` FROM `#@__member` WHERE (`username` like '%$key%' || `nickname` like '%$key%' || `phone` like '$key%')" .$where. " LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo json_encode($userResult);
        }
    }
    die;

//模糊匹配顾问
} elseif ($action == "checkGw") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND gw.`cityid` in ($adminCityIds)";
        $userSql = $dsql->SetQuery("SELECT user.username, gw.id FROM `#@__house_gw` gw LEFT JOIN `#@__member` user ON user.id = gw.userid WHERE user.username like '%$key%'".$where." LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo json_encode($userResult);
        }
    }
    die;

//检查顾问是否已经存在
} elseif ($action == "checkGw_") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND gw.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, gw.id FROM `#@__house_gw` gw LEFT JOIN `#@__member` user ON user.id = gw.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//模糊匹配小区
} elseif ($action == "checkCommunity") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND comm.`cityid` in ($adminCityIds)";
        $commSql = $dsql->SetQuery("SELECT comm.id, comm.title, addr.typename, comm.addr, comm.addrid FROM `#@__house_community` comm LEFT JOIN `#@__site_area` addr ON comm.addrid = addr.id WHERE comm.title like '%$key%'".$where." LIMIT 0, 10");
        $commResult = $dsql->dsqlOper($commSql, "results");
        if ($commResult) {
            foreach ($commResult as $key=>$value) {
                //地区
                $addrname = $value['addrid'];
                if($addrname){
                    $addrname = getPublicParentInfo(array('tab' => 'site_area', 'id' => $addrname, 'type' => 'typename', 'split' => ' '));
                }
                $commResult[$key]['typename']=$addrname;
            }
            echo json_encode($commResult);
        }
    }
    die;

//模糊匹配校区
} elseif ($action == "checkSchool") {
    $key = $_REQUEST['key'];
    if (!empty($key)) {
        $where = " and `cityid` in ($adminCityIds)";
        $schoolsql = $dsql->setquery("select id,title from `#@__house_school` where title like '%$key%'".$where." limit 0, 10");
        $schoolresult = $dsql->dsqloper($schoolsql, "results");
        if ($schoolresult) {
            echo json_encode($schoolresult);
        }
    }
    die;
	
//模糊匹配门店
} elseif ($action == "checkStore") {
    $key = $_REQUEST['key'];
	$zjcom = $_REQUEST['zjcom'];
    if (!empty($key)) {
        $storesql = $dsql->setquery("select id,name from `#@__house_store` where name like '%$key%'".$where." and zjcom={$zjcom} limit 0, 10");
        $storeresult = $dsql->dsqloper($storesql, "results");
        if ($storeresult) {
            echo json_encode($storeresult);
        }
    }
    die;

//模糊匹配经纪人
} elseif ($action == "checkZjUser") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND zj.`cityid` in ($adminCityIds)";
        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, zj.id FROM `#@__house_zjuser` zj LEFT JOIN `#@__member` user ON user.id = zj.userid WHERE (user.username like '%$key%' OR user.nickname like '%$key%')".$where." LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo json_encode($userResult);
        }
    }
    die;

//模糊匹配个人
} elseif($action == "checkPersonUser"){
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND zj.`cityid` in ($adminCityIds)";
        $zjSql = $dsql->SetQuery("SELECT user.id FROM `#@__house_zjuser` zj LEFT JOIN `#@__member` user ON user.id = zj.userid WHERE zj.state = 1 ".$where."");
        $zjResult = $dsql->dsqlOper($zjSql, "results");
        $idArr = '';
        if(!empty($zjResult)){
            array_walk($zjResult, function($value, $key) use (&$idArr ){
                if(!empty($value['id'])){
                    $idArr .= $value['id']. ',';
                }
            });
            if(!empty($idArr)){
	            $idArr = rtrim($idArr, ',');
	            $userSql = $dsql->SetQuery("SELECT username, phone, nickname, id FROM  `#@__member`  WHERE `state` = 1 and `id` not in ($idArr) and (username like '%$key%' OR nickname like '%$key%') LIMIT 0, 10");
	            $userResult = $dsql->dsqlOper($userSql, "results");
	            if ($userResult) {
	                echo json_encode($userResult);
	            }
            }else{
            	$userSql = $dsql->SetQuery("SELECT username, phone, nickname, id FROM  `#@__member`  WHERE `state` = 1 and (username like '%$key%' OR nickname like '%$key%') LIMIT 0, 10");
	            $userResult = $dsql->dsqlOper($userSql, "results");
	            if ($userResult) {
	                echo json_encode($userResult);
	            }
            }
        }else{
        	$userSql = $dsql->SetQuery("SELECT username, phone, nickname, id FROM  `#@__member`  WHERE `state` = 1 and (username like '%$key%' OR nickname like '%$key%') LIMIT 0, 10");
            $userResult = $dsql->dsqlOper($userSql, "results");
            if ($userResult) {
                echo json_encode($userResult);
            }
        }
    }
    die;

//检查经纪人是否已经存在（添加经纪人）
} elseif ($action == "checkZjUser_") {
    $result = "";
    $key = $_POST['key'];
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND zj.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, zj.id FROM `#@__house_zjuser` zj LEFT JOIN `#@__member` user ON user.id = zj.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查经纪人是否已经存在（添加中介公司）
} elseif ($action == "checkZjUser_1") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND zj.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, zj.userid FROM `#@__house_zjcom` zj LEFT JOIN `#@__member` user ON user.id = zj.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//模糊匹配中介
} elseif ($action == "checkZjCom") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND `cityid` in (0,$adminCityIds)";
        $commSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__house_zjcom` WHERE `title` like '%$key%'".$where." LIMIT 0, 10");
        $commResult = $dsql->dsqlOper($commSql, "results");
        if ($commResult) {
            echo json_encode($commResult);
        }
    }
    die;

//检查会员是否已经存在（添加商城店铺）
} elseif ($action == "checkStoreUser_1") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND store.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, store.id FROM `#@__shop_store` store LEFT JOIN `#@__member` user ON user.id = store.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经存在（添加建材公司）
} elseif ($action == "checkStoreUser_2") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND store.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, store.id FROM `#@__" . $type . "_store` store LEFT JOIN `#@__member` user ON user.id = store.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经存在（添加装修公司）
} elseif ($action == "checkStoreUser_3") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND store.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, store.id FROM `#@__" . $type . "_store` store LEFT JOIN `#@__member` user ON user.id = store.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经存在（添加装修团队）
} elseif ($action == "checkTeamUser") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND team.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, team.id FROM `#@__" . $type . "_team` team LEFT JOIN `#@__member` user ON user.id = team.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经存在（添加装修团队）
} elseif ($action == "checkRenovationCompany") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND `cityid` in (0,$adminCityIds)";
        $commSql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__renovation_store` WHERE `company` like '%$key%'".$where." LIMIT 0, 10");
        $commResult = $dsql->dsqlOper($commSql, "results");
        if ($commResult) {
            echo json_encode($commResult);
        }
    }
    die;

//检查会员是否已经存在（添加装修团队）
} elseif ($action == "checkTeamCompany") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__renovation_store` WHERE `company` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND store.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, store.id FROM `#@__" . $type . "_store` store LEFT JOIN `#@__member` user ON user.id = store.company WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//输入设计师（添加装修作品）
} elseif ($action == "checkDesigner") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where1 = " AND `cityid` in (0,$adminCityIds)";
        $storeSql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__renovation_store` WHERE 1=1".$where1);
        $storeResult = $dsql->dsqlOper($storeSql, "results");
        $houseid=array();
        foreach($storeResult as $ke => $loupan){
            array_push($houseid, $loupan['id']);
        }
        if($houseid){
            $where .= " AND `company` in (".join(",", $houseid).")";
        }else{
            $where .= " AND 1=1 ";
        }
        $teamSql = $dsql->SetQuery("SELECT `id`, `name`, `company` FROM `#@__renovation_team` WHERE `name` like '%$key%'".$where);
        $teamResult = $dsql->dsqlOper($teamSql, "results");
        $return = array();
        if ($teamResult) {
            foreach ($teamResult as $key => $val) {
                $storeSql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__renovation_store` WHERE `id` = " . $val['company']);
                $storeResult = $dsql->dsqlOper($storeSql, "results");
                if ($storeResult) {
                    $return[$key]['id'] = $val['id'];
                    $return[$key]['name'] = $val['name'];
                    $return[$key]['company'] = $storeResult[0]['company'];
                }
            }
        }
        echo json_encode($return);
    }
    die;

//检查设计师是否存在（添加装修作品）
} elseif ($action == "checkDesignerName") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $where1 = " AND `cityid` in (0,$adminCityIds)";
        $storeSql = $dsql->SetQuery("SELECT `id`, `company` FROM `#@__renovation_store` WHERE 1=1".$where1);
        $storeResult = $dsql->dsqlOper($storeSql, "results");
        $houseid=array();
        foreach($storeResult as $ke => $loupan){
            array_push($houseid, $loupan['id']);
        }
        $where .= " AND `company` in (".join(",", $houseid).")";
        $userSql = $dsql->SetQuery("SELECT `id`, `name` FROM `#@__renovation_team` WHERE `name` like '%$key%'".$where." LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";

        $userSql = $dsql->SetQuery("SELECT team.id, team.name, store.id FROM `#@__" . $type . "_team` team LEFT JOIN `#@__" . $type . "_store` store ON team.company = store.id WHERE team.name = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo $result;
        } else {
            echo 200;
        }
    }
    die;

//检查会员是否已经存在（添加招聘企业）
} elseif ($action == "checkCompanyUser_job") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__" . $type . "_company` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//模糊匹配招聘企业
} elseif ($action == "checkJobCompany") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND `cityid` in (0,$adminCityIds)";
        $commSql = $dsql->SetQuery("SELECT `id`, `title`, `contact`, `email` FROM `#@__job_company` WHERE `title` like '%$key%'".$where." LIMIT 0, 10");
        $commResult = $dsql->dsqlOper($commSql, "results");
        if ($commResult) {
            echo json_encode($commResult);
        }
    }
    die;

//检查会员是否已经存在（添加招聘简历）
} elseif ($action == "checkResumeUser_job") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone`, `email` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND resume.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, user.email, resume.id FROM `#@__" . $type . "_resume` resume LEFT JOIN `#@__member` user ON user.id = resume.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经添加过网站（自助建站）
} elseif ($action == "checkWebsiteUser") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND ws.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.nickname, ws.id FROM `#@__website` ws LEFT JOIN `#@__member` user ON user.id = ws.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经开通交友功能
} elseif ($action == "checkDating") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND dating.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, dating.id FROM `#@__dating_member` dating LEFT JOIN `#@__member` user ON user.id = dating.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经开通交友成功故事
} elseif ($action == "checkDatingStory") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND story.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, story.id FROM `#@__dating_story` story LEFT JOIN `#@__member` user ON (user.id = story.fid OR user.id = story.tid) WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经存在（添加婚嫁酒店）
} elseif ($action == "checkUser_marry") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__marry_" . $type . "` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经存在（添加顾问）
}elseif($action == "checkUser_adviser"){
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__car_adviser` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经存在（添加汽车经销商）
} elseif ($action == "checkUser_car") {

    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__car_store` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//模糊匹配经销商
} elseif ($action == "checkCarStore") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND `cityid` in (0,$adminCityIds)";
        $commSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__car_store` WHERE `title` like '%$key%'".$where." LIMIT 0, 10");
        $commResult = $dsql->dsqlOper($commSql, "results");
        if ($commResult) {
            echo json_encode($commResult);
        }
    }
    die;

//模糊匹配顾问
} elseif ($action == "checkCarAdviser") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND zj.`cityid` in ($adminCityIds)";
        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, zj.id FROM `#@__car_store` zj LEFT JOIN `#@__member` user ON user.id = zj.userid WHERE (user.username like '%$key%' OR user.nickname like '%$key%')".$where." LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo json_encode($userResult);
        }
    }
    die;

//模糊匹配汽车个人
} elseif($action == "checkCarPersonUser"){
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND zj.`cityid` in ($adminCityIds)";
        $zjSql = $dsql->SetQuery("SELECT user.id FROM `#@__car_store` zj LEFT JOIN `#@__member` user ON user.id = zj.userid WHERE zj.state = 1 ".$where."");
        $zjResult = $dsql->dsqlOper($zjSql, "results");
        $idArr = '';
        if(!empty($zjResult)){
            array_walk($zjResult, function($value, $key) use (&$idArr ){
                if(!empty($value['id'])){
                    $idArr .= $value['id']. ',';
                }
            });
            $idArr = rtrim($idArr, ',');
            $userSql = $dsql->SetQuery("SELECT username, phone, nickname, id FROM  `#@__member`  WHERE `state` = 1 and `id` not in ($idArr) and (username like '%$key%' OR nickname like '%$key%') LIMIT 0, 10");
            $userResult = $dsql->dsqlOper($userSql, "results");
            if ($userResult) {
                echo json_encode($userResult);
            }
        }else{
        	$userSql = $dsql->SetQuery("SELECT username, phone, nickname, id FROM  `#@__member`  WHERE `state` = 1 and (username like '%$key%' OR nickname like '%$key%') LIMIT 0, 10");
            $userResult = $dsql->dsqlOper($userSql, "results");
            if ($userResult) {
                echo json_encode($userResult);
            }
        }
    }
    die;

//检查会员是否已经存在（添加外卖餐厅）
} elseif ($action == "checkUser_waimai") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__waimai_store` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

// 检查楼盘
} elseif($action == "checkLoupan"){
    $key = $_POST['key'];
    $type = $_POST['type'];

    $sql = $dsql->SetQuery("SELECT `id` FROM `#@__houseitem` WHERE `parentid` = 1 AND `typename` = '$type'");
    $res = $dsql->dsqlOper($sql, "results");
    if($res){
        $tid = $res[0]['id'];

        $where = " AND l.`cityid` in ($adminCityIds)";

        $sql = $dsql->SetQuery("SELECT l.`id`, l.`title`, l.`addrid`, l.`addr`, addr.typename FROM `#@__house_loupan` l LEFT JOIN `#@__site_area` addr ON l.addrid = addr.id WHERE FIND_IN_SET($tid, `protype`) AND l.`title` LIKE '%$key%' LIMIT 0, 10");
        $res = $dsql->dsqlOper($sql, "results");
        if ($res) {
            foreach ($res as $key=>$value) {
                //地区
                $addrname = $value['addrid'];
                if($addrname){
                    $addrname = getPublicParentInfo(array('tab' => 'site_area', 'id' => $addrname, 'type' => 'typename', 'split' => ' '));
                }
                $res[$key]['typename']=$addrname;
            }
            echo json_encode($res);
            die;
        }
    }

//检查会员是否已经存在（添加家政店铺）
} elseif ($action == "checkUser_homemaking") {

    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__homemaking_store` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//检查会员是否已经存在（添加保姆/月嫂）
}elseif($action == "checkUser_nanny"){
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__homemaking_nanny` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;
//模糊匹配家政公司
} elseif ($action == "checkHomemakingStore") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND `cityid` in (0,$adminCityIds)";
        $commSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__homemaking_store` WHERE `title` like '%$key%'".$where." LIMIT 0, 10");
        $commResult = $dsql->dsqlOper($commSql, "results");
        if ($commResult) {
            echo json_encode($commResult);
        }
    }
    die;
//检查会员是否已经存在（添加服务人员）
}elseif($action == "checkUser_personal"){
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__homemaking_personal` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//模糊匹配个人会员
}elseif ($action == "checkPersonalUser") {
    $key = $_POST['key'];
    if (!empty($key)) {
        if($userType == 0)
            $where = "";
        if($userType == 3)
            $where = " AND `addr` in ($adminAreaIDs)";

        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone`, `email` FROM `#@__member` WHERE `mtype` = '1' and `username` like '%$key%'" .$where. "LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo json_encode($userResult);
        }
    }
    die;

//检查会员是否已经存在（添加婚嫁店铺）
} elseif ($action == "checkUser_marrystore") {

    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__marry_store` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//模糊匹配婚嫁公司
} elseif ($action == "checkMarryStore") {
    $key    = $_POST['key'];
    $filter = $_POST['filter'];
    if (!empty($key)) {
        $where   = " AND FIND_IN_SET('".$filter."', `bind_module`) AND `cityid` in (0,$adminCityIds)";
        $commSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__marry_store` WHERE `title` like '%$key%'".$where." LIMIT 0, 10");
        $commResult = $dsql->dsqlOper($commSql, "results");
        if ($commResult) {
            echo json_encode($commResult);
        }
    }
    die;

//检查会员是否已经存在（添加旅游店铺）
} elseif ($action == "checkUser_travelstore") {

    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__travel_store` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;

//模糊匹配旅游公司
} elseif ($action == "checkTravelStore") {
    $key    = $_POST['key'];
    $filter = $_POST['filter'];
    if (!empty($key)) {
        $where   = " AND FIND_IN_SET('".$filter."', `bind_module`) AND `cityid` in (0,$adminCityIds)";
        $commSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__travel_store` WHERE `title` like '%$key%'".$where." LIMIT 0, 10");
        $commResult = $dsql->dsqlOper($commSql, "results");
        if ($commResult) {
            echo json_encode($commResult);
        }
    }
    die;

//模糊匹配旅游个人
} elseif($action == "checkTravelPersonUser"){
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND zj.`cityid` in ($adminCityIds)";
        $zjSql = $dsql->SetQuery("SELECT user.id FROM `#@__travel_store` zj LEFT JOIN `#@__member` user ON user.id = zj.userid WHERE zj.state = 1 ".$where."");
        $zjResult = $dsql->dsqlOper($zjSql, "results");
        $idArr = '';
        if(!empty($zjResult)){
            array_walk($zjResult, function($value, $key) use (&$idArr ){
                if(!empty($value['id'])){
                    $idArr .= $value['id']. ',';
                }
            });
            $idArr = rtrim($idArr, ',');
            $userSql = $dsql->SetQuery("SELECT username, phone, nickname, id FROM  `#@__member`  WHERE `state` = 1 and `id` not in ($idArr) and (username like '%$key%' OR nickname like '%$key%') LIMIT 0, 10");
            $userResult = $dsql->dsqlOper($userSql, "results");
            if ($userResult) {
                echo json_encode($userResult);
            }
        }else{
        	$userSql = $dsql->SetQuery("SELECT username, phone, nickname, id FROM  `#@__member`  WHERE `state` = 1 and (username like '%$key%' OR nickname like '%$key%') LIMIT 0, 10");
            $userResult = $dsql->dsqlOper($userSql, "results");
            if ($userResult) {
                echo json_encode($userResult);
            }
        }
    }
    die;

//模糊旅游商家
} elseif ($action == "checkTravelUser") {
    $key    = $_POST['key'];
    $filter = $_POST['filter'];
    if (!empty($key)) {
        $where = "  AND FIND_IN_SET('".$filter."', zj.`bind_module`) AND zj.`cityid` in ($adminCityIds)";
        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, zj.id FROM `#@__travel_store` zj LEFT JOIN `#@__member` user ON user.id = zj.userid WHERE (user.username like '%$key%' OR user.nickname like '%$key%' OR zj.title like '%$key%')".$where." LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo json_encode($userResult);
        }
    }
    die;

//检查会员是否已经存在（添加教育店铺）
} elseif ($action == "checkUser_educationstore") {

    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__education_store` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;
//模糊匹配家政公司
} elseif ($action == "checkEducationStore") {
    $key = $_POST['key'];
    if (!empty($key)) {
        $where = " AND `cityid` in (0,$adminCityIds)";
        $commSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__education_store` WHERE `title` like '%$key%'".$where." LIMIT 0, 10");
        $commResult = $dsql->dsqlOper($commSql, "results");
        if ($commResult) {
            echo json_encode($commResult);
        }
    }
    die;
//检查会员是否已经存在（添加教育店铺）
} elseif ($action == "checkUser_educationtutor") {

    $key = $_POST['key'];
    $id  = $_POST['id'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.userid = " . $id;
        }
        //商家
        $commSql = $dsql->SetQuery("SELECT `id`, `title` FROM `#@__education_store` WHERE `userid` = '$id'");
        $commResult = $dsql->dsqlOper($commSql, "results");
        if ($commResult) {
            $where = " AND company.userid = " . $commResult[0]['id'];
        }

        $userSql = $dsql->SetQuery("SELECT company.id FROM `#@__education_tutor` company WHERE 1=1" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;
//检查会员是否已经存在（添加养老店铺）
} elseif ($action == "checkUser_pensionstore") {
    
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__pension_store` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;
   
//检查会员是否已经存在老人信息
}elseif ($action == "checkUser_pensionelderly") {
    $key = $_POST['key'];
    $result = "";
    if (!empty($key)) {
        $userSql = $dsql->SetQuery("SELECT `id`, `username`, `nickname`, `phone` FROM `#@__member` WHERE `username` like '%$key%' LIMIT 0, 10");
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            $result = json_encode($userResult);
        }

        $where = "";
        if (!empty($id)) {
            $where = " AND company.id != " . $id;
        }

        $userSql = $dsql->SetQuery("SELECT user.username, user.phone, user.nickname, company.id FROM `#@__pension_elderly` company LEFT JOIN `#@__member` user ON user.id = company.userid WHERE user.username = '$key'" . $where);
        $userResult = $dsql->dsqlOper($userSql, "results");
        if ($userResult) {
            echo 200;
        } else {
            echo $result;
        }
    }
    die;
}
