<?php   if(!defined('HUONIAOINC')) exit('Request Error!');

/**
 *  检验用户是否有权使用某功能
 *  CheckPurview函数只是对他回值的一个处理过程
 *
 * @access    public
 * @param     string  $n  功能名称
 * @return    mix  如果具有则返回TRUE
 */
function testPurview($n){
    $rs = FALSE;
    global $userLogin;
    $purview = $userLogin->getPurview();
    if(preg_match('/founder/i', $purview)){
        return TRUE;
    }
    if($n == ''){
        return TRUE;
    }
    $ns = explode(',', $n);
    foreach($ns as $n){
        //只要找到一个匹配的权限，即可认为用户有权访问此页面
        if($n == ''){
            continue;
        }
        if(in_array($n, explode(',',$purview))){
            $rs = TRUE;
            break;
        }
    }
    return $rs;
}

/**
 *  对权限检测后返回操作对话框
 *
 * @access    public
 * @param     string  $n  功能名称
 * @return    string
 */
function checkPurview($n){
    if(!testPurview($n)){
        ShowMsg("对不起，您无权使用此功能！", 'javascript:;');
        exit();
    }
}

/**
 * 管理员登陆类
 *
 * @version        $Id: userlogin.class.php 2013-7-7 上午10:33:36 $
 * @package        HuoNiao.class
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class userLogin extends db_connect{
    var $userName = '';
    var $userPwd = '';
    var $userID = '';
    var $userPASSWORD = '';
    var $userPurview = '';
    var $keepUserID = 'admin_auth';
    var $keepMemberID = 'login_user';
    var $keepUserPurview = 'keepuserpurview';

    private $_saltLength = 7;

    /**
     * 保存或生成一个DB对象，设定盐的长度
     *
     * @param object $db 数据库对象
     * @param int $saltLength 密码盐的长度
     */
    function __construct($db = NULL, $saltLength = NULL){
        global $admin_path;

        parent::__construct($db);

        /*
         * 若传入一个整数，则用它来设定saltLength的值
         */
        if(is_int($saltLength)){
            $this->_saltLength = $saltLength;
        }

        if(isset($_SESSION[$this->keepUserID])){
            $this->userID = $_SESSION[$this->keepUserID];
        }

        if(isset($_SESSION[$this->keepUserPurview])){
            $this->userPurview = $_SESSION[$this->keepUserPurview];
        }
    }

    function userLogin(){
        $this->__construct();
    }

    /**
     *  检验用户是否正确
     *
     * @access    public
     * @param     string    $username  用户名
     * @param     string    $userpwd  密码
     * @return    string
     */
    function checkUser($username, $userpwd, $admin = false){
        //只允许用户名和密码用0-9,a-z,A-Z,'@','_','.','-'这些字符
        // $this->userName = preg_replace("/[^0-9a-zA-Z_@!\.-]/", '', $username);
        // $this->userPwd = preg_replace("/[^0-9a-zA-Z_@!\.-]/", '', $userpwd);
        $this->userName = $username;
        $this->userPwd = $userpwd;

        global $cfg_errLoginCount;
        global $cfg_loginLock;

        $ip = GetIP();
        $archives = $this->SetQuery("SELECT * FROM `#@__failedlogin` WHERE `ip` = '$ip'");
        $results = $this->db->prepare($archives);
        $results->execute();
        $results = $results->fetchAll(PDO::FETCH_ASSOC);
        if($results){
            //验证错误次数，并且上次登录错误是在15分钟之内
            if($results[0]['count'] >= $cfg_errLoginCount){
                $timedifference = GetMkTime(time()) - $results[0]['date'];
                if($timedifference/60 < $cfg_loginLock){
                    return -1;
                }
            }
        }

        //mtype为0表示系统管理员，3为城市管理员
        $where = " AND member.`mtype` != 0";
        if($admin) $where = " AND (member.`mtype` = 0 OR member.`mtype` = 3)";

        $sql = $this->SetQuery("SELECT member.*,admin.purviews FROM `#@__member` member LEFT JOIN `#@__admingroup` admin ON admin.id = member.mgroupid WHERE member.username = '".$this->userName."' AND member.mgroupid != ''".$where." LIMIT 1");

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $param = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $user = array_shift($param);
            $stmt->closeCursor();
        }catch(Exception $e){
            die($e->getMessage());
        }
        //根据用户输入的密码生成散列后的密码
        $hash = $this->_getSaltedHash($this->userPwd, $user['password']);

        //若用户名在数据库中不存在则返回出错信息
        if(!isset($user)){
            return -1;
        }

        if($user['state'] == 1){
            return -3;
        }

        //检查散列后的密码是否与数据库中保存的密码一致
        if($user['password'] == $hash){
            $this->userID = $user['id'];
            $this->userPASSWORD = $user['password'];
            // $this->userPurview = $user['purviews'];
            $this->keepUser();
            return 1;
        }

        //如果密码不正确返回出错信息
        else{
            return -2;
        }
    }

    /**
     * 验证用户是否存在
     * @param  int  $udi  用户ID
     * @return  boolean
     *
     */
    function checkUserNull($uid){
        if($uid){
            if(!is_numeric($uid)){
                $RenrenCrypt = new RenrenCrypt();
                $uid = $RenrenCrypt->php_decrypt(base64_decode($uid));
                $uid = explode("&", $uid);
                $uid = $uid[0];
            }
            $sql = $this->SetQuery("SELECT `id` FROM `#@__member` WHERE (`state` = 1 OR `mtype` = 0 OR `mtype` = 3) AND `id` = ".(int)$uid);
            $res = $this->db->prepare($sql);
            $res->execute();
            $res = $res->fetchAll(PDO::FETCH_ASSOC);
            if($res[0]){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    /**
     *  检验用户是否正确
     *
     * @access    public
     * @param     string    $username  用户名
     * @param     string    $userpwd  密码
     * @return    string
     */
    function memberLogin($username, $userpwd){
        $this->userName = addslashes($username);
        $this->userPwd = addslashes($userpwd);

        global $cfg_errLoginCount;
        global $cfg_loginLock;

        $ip = GetIP();
        $archives = $this->SetQuery("SELECT * FROM `#@__failedlogin` WHERE `ip` = '$ip'");
        $results = $this->db->prepare($archives);
        $results->execute();
        $results = $results->fetchAll(PDO::FETCH_ASSOC);
        if($results){
            //验证错误次数，并且上次登录错误是在15分钟之内
            if($results[0]['count'] >= $cfg_errLoginCount){
                $timedifference = GetMkTime(time()) - $results[0]['date'];
                if($timedifference/60 < $cfg_loginLock){
                    return -1;
                }
            }
        }

        $sql = $this->SetQuery("SELECT * FROM `#@__member` WHERE (`username` = '".$this->userName."' OR `email` = '".$this->userName."' OR `phone` = '".$this->userName."') AND `mtype` != 0");

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $param = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // $user = array_shift($param);
            $user = $param;
            $stmt->closeCursor();
        }catch(Exception $e){
            die($e->getMessage());
        }

        //若用户名在数据库中不存在则返回出错信息
        if(!isset($user)){
            return -1;
        }
        // 查询结果不止1条并且填写的用户名是手机号
        if(count($user) == 2 && is_numeric($this->userName)){
            $r1 = $this->memberLoginCheck($user[0]);
            if($r1 == 1){
                $r2 = $this->memberLoginCheck($user[1]);
                if($r2 == 1){
                    $k = 0;
                    if($user[0]['phone'] == $this->userName){
                        $k = 0;
                    }elseif($user[1]['phone'] == $this->userName){
                        $k = 1;
                    }elseif($user[0]['username'] == $this->userName){
                        $k = 0;
                    }elseif($user[1]['username'] == $this->userName){
                        $k = 1;
                    }
                    if($k == 1){
                        return $r2;
                    }else{
                        return $this->memberLoginCheck($user[$k]);
                    }
                }else{
                    return $r1;
                }
            }else{
                return $this->memberLoginCheck($user[1]);
            }
        }else{
            return $this->memberLoginCheck($user[0]);
        }



    }

    /**
     * 短信验证码登录
     * @param $user
     * @return int
     */
    function memberLoginCheckForSmsCode($user){
        //根据用户输入的密码生成散列后的密码
        // $hash = $this->_getSaltedHash($this->userPwd, $user['password']);

        //会员状态
        if($user['state'] != 1){
            return -1;
        }

        $data = $user;
        $data['uPwd'] = $this->userPwd;

        //验证论坛是否可以登录
        global $cfg_bbsState;
        global $cfg_bbsType;

        if($cfg_bbsState == 1 && $cfg_bbsType != ""){

        }else{
            // 如果用户输入密码为空并且实际密码也为空，验证此时是否存为第三方登陆 用于第三方登陆绑定手机号后自动登陆
            $is_loginConnect = false;
            if(empty($this->userPwd) && empty($user['password'])){
                $this->keepUserID = $this->keepMemberID;
                $this->userID = $user['id'];
                $this->userPASSWORD = $user['password'];
                $this->keepUser();

                //登录成功，重置登录失败次数
                $ip = GetIP();
                $archives = $this->SetQuery("UPDATE `#@__failedlogin` SET `count` = 0 WHERE `ip` = '$ip'");
                $results = $this->db->prepare($archives);
                $results->execute();
                return 1;
            }
            if($user['password'] || $is_loginConnect){
                $this->keepUserID = $this->keepMemberID;
                $this->userID = $user['id'];
                $this->userPASSWORD = $user['password'];
                $this->keepUser();

                //登录成功，重置登录失败次数
                $ip = GetIP();
                $archives = $this->SetQuery("UPDATE `#@__failedlogin` SET `count` = 0 WHERE `ip` = '$ip'");
                $results = $this->db->prepare($archives);
                $results->execute();
                return 1;
            }
        }
    }
    /**
     * 登陆验证,分离出来是为了处理手机号作为一个账号的用户名的同时又被另一个账号绑定的情况
     * @param  [type] $user [description]
     * @return [type]       [description]
     */
    function memberLoginCheck($user){
        //根据用户输入的密码生成散列后的密码
        $hash = $this->_getSaltedHash($this->userPwd, $user['password']);

        //会员状态
        if($user['state'] != 1){
            return -1;
        }

        $data = $user;
        $data['uPwd'] = $this->userPwd;

        //验证论坛是否可以登录
        global $cfg_bbsState;
        global $cfg_bbsType;
        $bbsID = $this->bbsSync($data, "login");

        if($cfg_bbsState == 1 && $cfg_bbsType != ""){
            if($bbsID > 0){
                //登录成功，重置登录失败次数
                $ip = GetIP();
                $archives = $this->SetQuery("UPDATE `#@__failedlogin` SET `count` = 0 WHERE `ip` = '$ip'");
                $results = $this->db->prepare($archives);
                $results->execute();

                //如果是通过论坛验证的，则更新主站密码
                $npass = $this->_getSaltedHash($this->userPwd);
                $archives = $this->SetQuery("UPDATE `#@__member` SET `password` = '$npass' WHERE `id` = ".$user['id']);
                $results = $this->db->prepare($archives);
                $results->execute();

                //论坛同步操作
                $this->bbsSync($data, "synlogin");

                //如果验证通过，则返回成功
                $this->keepUserID = $this->keepMemberID;
                $this->userID = $user['id'];
                $this->userPASSWORD = $npass;
                $this->keepUser();
                return 1;

            }else{

                //如果论坛用户不存在或已删除，再与主站数据进行匹配
                if($user['password'] == $hash){
                    $this->keepUserID = $this->keepMemberID;
                    $this->userID = $user['id'];
                    $this->userPASSWORD = $user['password'];
                    $this->keepUser();

                    //登录成功，重置登录失败次数
                    $ip = GetIP();
                    $archives = $this->SetQuery("UPDATE `#@__failedlogin` SET `count` = 0 WHERE `ip` = '$ip'");
                    $results = $this->db->prepare($archives);
                    $results->execute();

                    //更新论坛密码
                    $update['username'] = $user['username'];
                    $update['newpw'] = $this->userPwd;
                    $update['email'] = $user['email'];
                    $this->bbsSync($update, "edit");

                    //论坛同步操作
                    $this->bbsSync($data, "synlogin");

                    return 1;

                    //如果密码不正确返回出错信息
                }else{
                    return -2;
                }
            }

            //验证本站数据
        }else{

            // 如果用户输入密码为空并且实际密码也为空，验证此时是否存为第三方登陆 用于第三方登陆绑定手机号后自动登陆
            $is_loginConnect = false;
            if(empty($this->userPwd) && empty($user['password'])){
                $uid = GetCookie("connect_uid");
                if($uid){
                    $RenrenCrypt = new RenrenCrypt();
                    $userid = $RenrenCrypt->php_decrypt(base64_decode($uid));
                    if($userid == $user['id']){
                        $is_loginConnect = true;
                        DropCookie("connect_uid");
                    }
                }
            }
            //检查散列后的密码是否与数据库中保存的密码一致

            if($user['password'] == $hash || $is_loginConnect){
                $this->keepUserID = $this->keepMemberID;
                $this->userID = $user['id'];
                $this->userPASSWORD = $user['password'];
                $this->keepUser();

                //登录成功，重置登录失败次数
                $ip = GetIP();
                $archives = $this->SetQuery("UPDATE `#@__failedlogin` SET `count` = 0 WHERE `ip` = '$ip'");
                $results = $this->db->prepare($archives);
                $results->execute();

                //更新论坛密码
                $update['username'] = $user['username'];
                $update['newpw'] = $this->userPwd;
                $update['email'] = $user['email'];
                $this->bbsSync($update, "edit");

                //论坛同步操作
                $this->bbsSync($data, "synlogin");

                return 1;

                //如果密码不正确返回出错信息
            }else{
                return -2;
            }

        }
    }


    /**
     * 获取登录用户信息
     * @return array
     */
    function getMemberInfo($uid = ''){
        $mid = empty($uid) ? $this->getMemberID() : $uid;
        $memberInfo = array();

        if($mid > -1){
            global $handler;
            $handler = true;
            $handels = new handlers("member", "detail");
            $memberInfo = $handels->getHandle(array("id" => $mid));
            $memberInfo = $memberInfo["info"];
        }
        return $memberInfo;
    }


    /**
     * 整合第三方登录
     * @param    string    $code      类型
     * @param    string    $key       唯一值
     * @param    string    $nickname  昵称
     * @param    string    $photo     头像
     * @return   array
     */
    function loginConnect($params){

        extract($params);
        global $cfg_secureAccess;
        global $cfg_basehost;
        $cfg_basehost = $cfg_secureAccess.$cfg_basehost;
        $loginRedirect = $_SESSION['loginRedirect'];
        $loginRedirect = $loginRedirect ? $loginRedirect : $cfg_basehost;

        global $cfg_bindMobile;  //第三方登录必须绑定手机号码
        global $cfg_wechatBindPhone;   //微信注册必须绑定手机

         //记录当前设备s
        $sourceArr = array(
            "title" => $deviceTitle,
            "type"  => $deviceType,
            "serial" => $deviceSerial,
            "pudate" => time()
        );
         //记录当前设备e

        $uid = $this->getMemberID();

        // PC端已登录，扫码时如果后台设置微信访问自动登陆，会创建新用户
        // 所以在PC端已登陆的情况下，使用PC端已登录uid
        // 只要验证微信号是否已被其他用户绑定
        if($state){
            $archives = $this->SetQuery("SELECT `loginUid` FROM `#@__site_wxlogin` WHERE `state` = '$state'");
            $results = $this->db->prepare($archives);
            $results->execute();
            $results = $results->fetchAll(PDO::FETCH_ASSOC);
            if($results){
                $pc_uid = $results[0]['loginUid'];
                if($pc_uid){
                    if($openid){
                        $archives = $this->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` != $pc_uid AND (`".$code."_conn` = '$key' OR `wechat_openid` = '$key' OR `".$code."_conn` = '$openid' OR `wechat_openid` = '$openid')");
                    }else{
                        $archives = $this->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` != $pc_uid AND (`".$code."_conn` = '$key' OR `wechat_openid` = '$key')");
                    }
                    $results = $this->db->prepare($archives);
                    $results->execute();
                    $results = $results->fetchAll(PDO::FETCH_ASSOC);
                    if($results){
                        $sql = $this->SetQuery("UPDATE `#@__site_wxlogin` SET `sameConn` = '".$results[0]['id']."&".$key."' WHERE `loginUid` = $pc_uid");
                        $res = $this->db->prepare($sql);
                        $res->execute();
                        die('<meta charset="UTF-8"><script type="text/javascript">alert("您的帐号已经绑定其他用户，请在电脑端进行重新绑定的操作！_1");window.close();top.location="'.$loginRedirect.'";</script>');
                    }
                    $uid = $pc_uid;
                }
            }
        }

        //判断是否为已经登录的用户，如果是则绑定此社交账号
        if($uid > -1){

            $archives = $this->SetQuery("SELECT `id`, `username`, `password` FROM `#@__member` WHERE `id` = '$uid'");
            $results = $this->db->prepare($archives);
            $results->execute();
            $results = $results->fetchAll(PDO::FETCH_ASSOC);

            if(!$results){
                if(!$noRedir){
                    die('要绑定社交账号的用户不存在！');
                }
            }else{

                $username = $results[0]['username'];

                //如果是扫码登录
                if($state){

                    $this->keepUserID = $this->keepMemberID;
                    $this->userID = $uid;
                    $this->userPASSWORD = $results[0]['password'];
                    $this->keepUser();

                    $archives_ = $this->SetQuery("UPDATE `#@__site_wxlogin` SET `uid` = '$uid' WHERE `state` = '$state'");
                    $results_ = $this->db->prepare($archives_);
                    $results_->execute();

                    //论坛同步
                    global $cfg_bbsState;
                    global $cfg_bbsType;
                    if($cfg_bbsState == 1 && $cfg_bbsType != ""){
                        $data['username'] = $username;
                        $data['uPwd']     = md5(uniqid(rand(), TRUE));
                        $this->bbsSync($data, "synlogin");
                    }

                    if($code == 'wechat'){
                        $archives = $this->SetQuery("UPDATE `#@__member` SET `".$code."_conn` = '$key', `wechat_openid` = '$openid' WHERE `id` = '$uid'");
                    }else{
                        $archives = $this->SetQuery("UPDATE `#@__member` SET `".$code."_conn` = '$key' WHERE `id` = '$uid'");
                    }
                    $results = $this->db->prepare($archives);
                    $results->execute();


                    if(isMobile()){
                        die('<meta charset="UTF-8"><script type="text/javascript">top.location="'.$loginRedirect.'";</script>');
                    }else{
                        die('<meta charset="UTF-8"><script type="text/javascript">window.close();top.location="'.$loginRedirect.'";</script>');
                    }


                }else{
                    if($openid){
                        $archives = $this->SetQuery("SELECT `id` FROM `#@__member` WHERE `".$code."_conn` = '$key' OR `wechat_openid` = '$key' OR `".$code."_conn` = '$openid' OR `wechat_openid` = '$openid'");
                    }else{
                        $archives = $this->SetQuery("SELECT `id` FROM `#@__member` WHERE `".$code."_conn` = '$key' OR `wechat_openid` = '$key'");
                    }
                    $results = $this->db->prepare($archives);
                    $results->execute();
                    $results = $results->fetchAll(PDO::FETCH_ASSOC);
                    if($results){
                        if(!$noRedir){
                            // 打开弹出层请用户确认是否修改绑定
                            $sameConn = $results[0]['id']."&".$key;
                            $RenrenCrypt = new RenrenCrypt();
                            $sameConn = base64_encode($RenrenCrypt->php_encrypt($sameConn));

                            // die('您的帐号已经绑定其他用户！');
                            // die('<meta charset="UTF-8"><script type="text/javascript">window.opener.hasBindOtherUser("'.$sameConn.'");window.close();</script>');
                            // die('<meta charset="UTF-8"><script type="text/javascript">if(hasBindOtherUser){hasBindOtherUser("'.$sameConn.'");}else{window.opener.hasBindOtherUser("'.$sameConn.'");window.close();}</script>');

                            if(isMobile()){
                                global $cfg_cookiePath;
                                PutCookie("sameConnData", $code.'#'.$sameConn, 15, $cfg_cookiePath);
                                $furl = GetCookie("furl");
                                if($furl){
                                    die('<meta charset="UTF-8"><script type="text/javascript">alert("您的帐号已经绑定其他用户，请在电脑端进行重新绑定的操作！_2");window.close();top.location="'.$furl.'";</script>');
                                }else{
                                    die('<meta charset="UTF-8"><script type="text/javascript">setTimeout(function(){history.go(-1);},500)</script>');
                                }

                            }else{
                                die('<meta charset="UTF-8"><script type="text/javascript">window.opener.hasBindOtherUser("'.$sameConn.'");window.close();</script>');
                            }
                        }
                    }else{
                        $archives = $this->SetQuery("UPDATE `#@__member` SET `".$code."_conn` = '$key', `wechat_openid` = '$openid' WHERE `id` = '$uid'");
                        $results = $this->db->prepare($archives);
                        $results->execute();
                        if(!$noRedir){
                            $furl = GetCookie("furl");
                            $loginRedirect = $furl ? $furl : $loginRedirect;
                            if(isMobile()){
                                die('<meta charset="UTF-8"><script type="text/javascript">top.location="'.$loginRedirect.'";</script>');
                            }else{
                                die('<meta charset="UTF-8"><script type="text/javascript">window.close();top.location="'.$loginRedirect.'";</script>');
                            }
                            // echo '<script type="text/javascript">setTimeout(function(){window.close();}, 500);</script>绑定成功！';
                        }
                    }
                }
            }

            return;
        }

        if(!$noRedir){
            if(isMobile()){
                if(empty($key)) die('<meta charset="UTF-8"><script type="text/javascript">setTimeout(function(){top.location="'.$loginRedirect.'";}, 5000);</script>登录失败！');
            }else{
                if(empty($key)) die('<meta charset="UTF-8"><script type="text/javascript">setTimeout(function(){window.close();top.location="'.$loginRedirect.'";}, 5000);</script>登录失败！');
            }
        }


        //生成用户名【昵称+随机】
        $chcode = strtolower(create_check_code(8));
        // $username = $chcode."@".(strlen($code) > 6 ? substr($code, 0, 5) : $code);
        $username = $chcode."@".$code;
        $nickname = $nickname ? filter_emoji($nickname) : $chcode;
        // $password = substr($key, 0, 20);
        $password = "";  //第三方快捷登录，不设置密码，主要为了在会员中心修改密码时不输原密码

        $ip   = GetIP();
        $ipaddr = getIpAddr($ip);
        $time = GetMkTime(time());

        //验证用户是否已存在
        if($openid){
            $archives = $this->SetQuery("SELECT `id`, `username`, `phoneCheck`, `state`, `password`, `sourceclient` FROM `#@__member` WHERE `".$code."_conn` = '$key' OR `wechat_openid` = '$key' OR `".$code."_conn` = '$openid' OR `wechat_openid` = '$openid'");
        }else{
            $archives = $this->SetQuery("SELECT `id`, `username`, `phoneCheck`, `state`, `password`, `sourceclient` FROM `#@__member` WHERE `".$code."_conn` = '$key' OR `wechat_openid` = '$key'");
        }
        // echo $archives;die;
        $results = $this->db->prepare($archives);
        $results->execute();
        $results = $results->fetchAll(PDO::FETCH_ASSOC);

        //如果已经存在，如果已绑定手机号，则自动登录
        if($results){
            $userid = $results[0]['id'];
             //记录当前设备s
            if($results[0]['sourceclient']){
                $sourceclientAll = unserialize($results[0]['sourceclient']);
            }
             //记录当前设备e

            //如果是微信扫码登录，需要更新临时登录日志
            if($state){
                $archives_ = $this->SetQuery("UPDATE `#@__site_wxlogin` SET `uid` = '$userid' WHERE `state` = '$state'");
                $results_ = $this->db->prepare($archives_);
                $results_->execute();
            }

            // 会员状态未审核，但设置了微信自动登陆
            if($results[0]['state'] == 0 && $code == "wechat" && $cfg_wechatBindPhone){
                global $cfg_cookiePath;
                $RenrenCrypt = new RenrenCrypt();
                $userid_ = base64_encode($RenrenCrypt->php_encrypt($userid));
                PutCookie("connect_uid", $userid_, 300, $cfg_cookiePath);
                PutCookie("connect_code", $code, 300, $cfg_cookiePath);
            }

            if($results[0]['state'] == 0 && $results[0]['phoneCheck'] == 0 && $cfg_bindMobile){

                //如果开启了微信注册必须绑定手机，则跳转到绑定手机页面
                // if(isMobile() && ($code != "wechat" || ($code == "wechat" && $cfg_wechatBindPhone))){
                if(($code != "wechat" || ($code == "wechat" && $cfg_wechatBindPhone))){
                    global $cfg_cookiePath;
                    $RenrenCrypt = new RenrenCrypt();
                    $userid = base64_encode($RenrenCrypt->php_encrypt($userid));
                    PutCookie("connect_uid", $userid, 300, $cfg_cookiePath);
                    PutCookie("connect_code", $code, 300, $cfg_cookiePath);

                    //APP端需要由脚本来做跳转
                    if(isApp()){
                        die('bindMobile');
                    }else {
                        echo '跳转中...';
                        header("location:/bindMobile.html?type=" . $code);
                    }
                    die;

                    //没有开启则更新会员状态为已审核，并自动登录
                }else{
                    $archives = $this->SetQuery("UPDATE `#@__member` SET `state` = '1' WHERE `id` = ".$userid);
                    $results_ = $this->db->prepare($archives);
                    $results_->execute();
                }
            }


            $this->keepUserID = $this->keepMemberID;
            $this->userID = $userid;
            $this->userPASSWORD = $results[0]['password'];
            $this->keepUser();

            $username = $results[0]['username'];

            if($code == 'wechat'){
                $archives = $this->SetQuery("UPDATE `#@__member` SET `".$code."_conn` = '$key', `wechat_openid` = '$openid' WHERE `id` = '$userid'");
            }else{
                $archives = $this->SetQuery("UPDATE `#@__member` SET `".$code."_conn` = '$key' WHERE `id` = '$userid'");
            }
            $results = $this->db->prepare($archives);
            $results->execute();

            //登录成功，重置登录失败次数
            $archives = $this->SetQuery("UPDATE `#@__failedlogin` SET `count` = 0 WHERE `ip` = '$ip'");
            $results = $this->db->prepare($archives);
            $results->execute();

            //记录当前设备s
            $sourceclients = array();
            if(!empty($deviceTitle) && !empty($deviceSerial) && !empty($deviceType)){
                if(!empty($sourceclientAll)){
                    $sourceclients = $sourceclientAll;
                    //$foundTitle  = array_search($deviceTitle, array_column($sourceclientAll, 'title'));
                    $foundSerial = array_search($deviceSerial, array_column($sourceclientAll, 'serial'));
                    //$foundType   = array_search($deviceType, array_column($sourceclientAll, 'type'));
                    if($foundSerial){
                        //如果已有，更新时间，以Serial为准
                        $sourceclients[$foundSerial]['pudate'] = time();
                    }else{
                        array_push($sourceclients, $sourceArr);
                    }
                }else{
                    $sourceclients[] = $sourceArr;
                }
                $sourceclients = serialize($sourceclients);

                $where_ = "`sourceclient` = '$sourceclients',";
            }
            //记录当前设备e

            $archives = $this->SetQuery("UPDATE `#@__member` SET $where_ `logincount` = `logincount` + 1, `lastlogintime` = '$time', `lastloginip` = '$ip', `lastloginipaddr` = '$ipaddr', `online` = '$time' WHERE `id` = ".$userid);
            $results = $this->db->prepare($archives);
            $results->execute();

            //保存到主表
            $archives = $this->SetQuery("INSERT INTO `#@__member_login` (`userid`, `logintime`, `loginip`, `ipaddr`) VALUES ('$userid', '$time', '$ip', '$ipaddr')");
            $results = $this->db->prepare($archives);
            $results->execute();

            //论坛同步
            global $cfg_bbsState;
            global $cfg_bbsType;
            if($cfg_bbsState == 1 && $cfg_bbsType != ""){
                $data['username'] = $username;
                $data['uPwd']     = $password;
                $this->bbsSync($data, "synlogin");
            }

            if(!$noRedir || $state){
                if($notclose){
                    die('<meta charset="UTF-8"><script type="text/javascript">top.location="'.$loginRedirect.'";</script>');
                }else{
                    if(isMobile()){
                        die('<meta charset="UTF-8"><script type="text/javascript">top.location="'.$loginRedirect.'";</script>');
                    }else{
                        die('<meta charset="UTF-8"><script type="text/javascript">window.close();top.location="'.$loginRedirect.'";</script>');
                    }
                }
            }
            // echo '<script type="text/javascript">setTimeout(function(){window.close();}, 500);</script>授权成功！';

            //如果不存在则新建用户
        }else{

            // $pwd = $this->_getSaltedHash($password);
            $pwd = '';
            $sex = $gender == "男" ? 1 : 0;

            //头像   直接使用远程地址，不需要下载到本地  by gz 20190823
            $photo = str_replace('http://', 'https://', $photo);
            // if(!empty($photo)){
            //
            //     require_once(HUONIAOINC."/config/siteConfig.inc.php");
            //
            //     global $cfg_attachment;
            //     global $cfg_uploadDir;
            //     global $cfg_photoSize;
            //     global $cfg_atlasType;
            //     global $editor_uploadDir;
            //     global $cfg_ftpType;
            //     global $editor_ftpType;
            //     global $cfg_ftpState;
            //     global $editor_ftpState;
            //     global $cfg_ftpDir;
            //     global $editor_ftpDir;
            //
            //     global $cfg_photoSmallWidth;
            //     global $cfg_photoSmallHeight;
            //     global $cfg_photoMiddleWidth;
            //     global $cfg_photoMiddleHeight;
            //     global $cfg_photoLargeWidth;
            //     global $cfg_photoLargeHeight;
            //     global $cfg_photoCutType;
            //     global $cfg_photoCutPostion;
            //     global $cfg_quality;
            //
            //     $editor_uploadDir = $cfg_uploadDir;
            //     $editor_ftpType = $cfg_ftpType;
            //     $editor_ftpState = $cfg_ftpState;
            //     $editor_ftpDir = $cfg_ftpDir;
            //
            //     /* 上传配置 */
            //     $config = array(
            //         "savePath" => ($noRedir ? "../" : "")."..".$cfg_uploadDir."/siteConfig/photo/large/".date( "Y" )."/".date( "m" )."/".date( "d" )."/",
            //         "maxSize" => $cfg_photoSize,
            //         "allowFiles" => explode("|", $cfg_atlasType)
            //     );
            //
            //     $photoList = array();
            //     array_push($photoList, $photo);
            //
            //     $pic = "";
            //     $photoArr = getRemoteImage($photoList, $config, "siteConfig", ($noRedir ? "../" : "")."..", true);
            //     if($photoArr){
            //         $photoArr = json_decode($photoArr, true);
            //         if(is_array($photoArr) && $photoArr['state'] == "SUCCESS"){
            //             $pic = $photoArr['list'][0]['fid'];
            //         }
            //     }
            // }

            //记录当前设备s
            if(!empty($deviceTitle) && !empty($deviceSerial) && !empty($deviceType)){
                $sourceclient[] = $sourceArr;
                $sourceclient   = serialize($sourceclient);
            }
            //记录当前设备e

            //保存到主表
            if($code == "wechat"){
                $state_ = $cfg_wechatBindPhone ? 0 : 1;
                $archives = $this->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `emailCheck`, `phoneCheck`, `sex`, `photo`, `regtime`, `logincount`, `lastlogintime`, `lastloginip`, `lastloginipaddr`, `regip`, `regipaddr`, `state`, `".$code."_conn`, `wechat_openid`, `purviews`, `sourceclient`) VALUES (1, '$username', '$pwd', '$nickname', '0', '0', '$sex', '$photo', '$time', '1', '$time', '$ip', '$ipaddr', '$ip', '$ipaddr', '$state_', '$key', '$openid', '', '$sourceclient')");
            }else{
                $state_ = $cfg_bindMobile ? 0 : 1;
                $archives = $this->SetQuery("INSERT INTO `#@__member` (`mtype`, `username`, `password`, `nickname`, `emailCheck`, `phoneCheck`, `sex`, `photo`, `regtime`, `logincount`, `lastlogintime`, `lastloginip`, `lastloginipaddr`, `regip`, `regipaddr`, `state`, `".$code."_conn`, `purviews`, `sourceclient`) VALUES (1, '$username', '$pwd', '$nickname', '0', '0', '$sex', '$photo', '$time', '1', '$time', '$ip', '$ipaddr', '$ip', '$ipaddr', '$state_', '$key', '', '$sourceclient')");
            }
            $results = $this->db->prepare($archives);
            $results->execute();
            $aid = $this->db->lastInsertId();

            if(is_numeric($aid)){

                //如果是微信扫码登录，需要更新临时登录日志
                if($state){
                    $archives = $this->SetQuery("UPDATE `#@__site_wxlogin` SET `uid` = '$aid' WHERE `state` = '$state'");
                    $results = $this->db->prepare($archives);
                    $results->execute();
                }

                $this->registGiving($aid);

                global $cfg_cookiePath;
                $RenrenCrypt = new RenrenCrypt();
                $userid = base64_encode($RenrenCrypt->php_encrypt($aid));

                // if(isMobile() && !$noRedir && $cfg_wechatBindPhone){
                if(isMobile() && $cfg_bindMobile && !$noRedir && ($code != "wechat" || ($code == "wechat" && $cfg_wechatBindPhone))){
                    PutCookie("connect_uid", $userid, 300, $cfg_cookiePath);
                    PutCookie("connect_code", $code, 300, $cfg_cookiePath);

                    //APP端需要由脚本来做跳转
                    if(isApp()){
                        die('bindMobile');
                    }else {
                        echo '跳转中...';
                        header("location:/bindMobile.html?type=" . $code);
                    }
                    die;
                }

                //如果是微信，并且微信注册必须绑定手机
                if(($code == "wechat" && $cfg_wechatBindPhone) || ($code != "wechat" && $cfg_bindMobile)){
                    PutCookie("connect_uid", $userid, 300, $cfg_cookiePath);
                    PutCookie("connect_code", $code, 300, $cfg_cookiePath);

                    if(isMobile()){
                        //APP端需要由脚本来做跳转
                        if(isApp()){
                            die('bindMobile');
                        }else {
                            echo '跳转中...';
                            header("location:/bindMobile.html?type=" . $code);
                        }
                    }
                    return;
                }

                // /////////////////////////////////////////////////

                //保存到主表
                $archives = $this->SetQuery("INSERT INTO `#@__member_login` (`userid`, `logintime`, `loginip`, `ipaddr`) VALUES ('$aid', '$time', '$ip', '$ipaddr')");
                $results = $this->db->prepare($archives);
                $results->execute();

                // return;

                //论坛同步
                global $cfg_bbsState;
                global $cfg_bbsType;
                if($cfg_bbsState == 1 && $cfg_bbsType != ""){
                    $data['username'] = $username;
                    $data['password'] = $password;
                    $data['email']    = $chcode."@qq.com";
                    $this->bbsSync($data, "register");
                }

                //站点登录
                $this->keepUserID = $this->keepMemberID;
                $this->userID = $aid;
                $this->userPASSWORD = $pwd;
                $this->keepUser();

                //论坛登录
                $data['username'] = $username;
                $data['uPwd']     = $password;
                $this->bbsSync($data, "synlogin");

                if(!$noRedir || $state){
                    if($notclose){
                        die('<meta charset="UTF-8"><script type="text/javascript">top.location="'.$loginRedirect.'";</script>');
                    }else{
                        if(isMobile()){
                            die('<meta charset="UTF-8"><script type="text/javascript">top.location="'.$loginRedirect.'";</script>');
                        }else{
                            die('<meta charset="UTF-8"><script type="text/javascript">window.close();top.location="'.$loginRedirect.'";</script>');
                        }
                        // echo '<script type="text/javascript">setTimeout(function(){window.close();}, 500);</script>授权成功！';
                    }
                }

            }else{

                if(!$noRedir || $state){
                    if($notclose){
                        die('<meta charset="UTF-8"><script type="text/javascript">top.location="'.$loginRedirect.'";</script>');
                    }else{
                        if(isMobile()){
                            die('<meta charset="UTF-8"><script type="text/javascript">setTimeout(function(){top.location="'.$loginRedirect.'";}, 5000);</script>登录失败！');
                        }else{
                            die('<meta charset="UTF-8"><script type="text/javascript">setTimeout(function(){window.close();top.location="'.$loginRedirect.'";}, 5000);</script>登录失败！');
                        }
                        // die("登录失败！");
                    }
                }
            }

        }

    }


    /**
     *  保持用户的会话状态
     *
     * @access    public
     * @return    int    成功返回 1 ，失败返回 -1
     */
    function keepUser(){
        $time = GetMkTime(time());
        if($this->userID != '' && $this->checkUserNull($this->userID)){
            global $cfg_cookiePath;
            global $cfg_onlinetime;
            $data = $this->userID.'&'.$this->userPASSWORD;
            $RenrenCrypt = new RenrenCrypt();
            $userid = base64_encode($RenrenCrypt->php_encrypt($data));
            PutCookie($this->keepUserID, $userid, $cfg_onlinetime * 60 * 60, $cfg_cookiePath);

            $archives = $this->SetQuery("UPDATE `#@__member` SET `online` = '$time' WHERE `id` = ".$this->userID);
            $results = $this->db->prepare($archives);
            $results->execute();

            $this->keepUserID = "admin_auth";


            return 1;
        }else{
            if(GetCookie($this->keepUserID) != '' && $this->checkUserNull(GetCookie($this->keepUserID))){
                global $cfg_cookiePath;
                global $cfg_onlinetime;

                $RenrenCrypt = new RenrenCrypt();
                $userid = $RenrenCrypt->php_decrypt(base64_decode(GetCookie($this->keepUserID)));
                $userinfo = explode('&', $userid);
                if(count($userinfo) != 2){
                    $this->exitUser();
                    $this->keepUserID = "admin_auth";
                    return -1;
                }
                $userid = $userinfo[0];

                $archives = $this->SetQuery("UPDATE `#@__member` SET `online` = '$time' WHERE `id` = ".$userid);
                $results = $this->db->prepare($archives);
                $results->execute();

                PutCookie($this->keepUserID, GetCookie($this->keepUserID), $cfg_onlinetime * 60 * 60, $cfg_cookiePath);

                $this->keepUserID = "admin_auth";
                return 1;
            }else{
                $this->keepUserID = "admin_auth";
                return -1;
            }
        }
    }

    /**
     *  结束用户的会话状态
     *
     * @access    public
     * @return    void
     */
    function exitUser(){
        unset($_SESSION[$this->keepUserID]);
        DropCookie($this->keepUserID);
        //$_SESSION = array();
    }

    /**
     *  结束用户的会话状态
     *
     * @access    public
     * @return    void
     */
    function exitMember(){
        unset($_SESSION[$this->keepMemberID]);

        $RenrenCrypt = new RenrenCrypt();
        $userid = $RenrenCrypt->php_decrypt(base64_decode(GetCookie($this->keepMemberID)));
        $userinfo = explode('&', $userid);
        $userid = (int)$userinfo[0];

        if($userid){
            $archives = $this->SetQuery("UPDATE `#@__member` SET `online` = 0 WHERE `id` = ".$userid);
            $results = $this->db->prepare($archives);
            $results->execute();
        }

        DropCookie($this->keepMemberID);

        global $HN_memory;
        $HN_memory->rm('member_' . $userid);

        global $cfg_bbsState;
        global $cfg_bbsType;
        if($cfg_bbsState == 1 && $cfg_bbsType != ""){
            $this->bbsSync($this->keepMemberID, "logout");
        }
        //$_SESSION = array();
    }

    /**
     *  获得用户的ID
     *
     * @access    public
     * @return    int
     */
    function getUserID(){
        if($this->userID != ''){
            return $this->userID;
        }else{
            if(GetCookie($this->keepUserID) != ''){
                $RenrenCrypt = new RenrenCrypt();
                $userid = $RenrenCrypt->php_decrypt(base64_decode(GetCookie($this->keepUserID)));
                $userinfo = explode('&', $userid);
                if(count($userinfo) != 2){
                    $this->exitUser();
                    return -1;
                }
                $userid = $userinfo[0];
                $password = $userinfo[1];
                $sql = $this->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` = ".$userid." AND `password` = '$password'");
                $res = $this->db->prepare($sql);
                $res->execute();
                $res = $res->fetchAll(PDO::FETCH_ASSOC);
                if(!$res[0]){
                    $this->exitUser();
                    return -1;
                }

                return $userid;
            }else{
                return -1;
            }
        }
    }

    /**
     *  获得用户的类型
     *
     * @access    public
     * @return    int
     */
    function getUserType(){
        global $dsql;
        $userid = $this->getUserID();
        if(is_numeric($userid)){
            $sql = $dsql->SetQuery("SELECT `mtype` FROM `#@__member` WHERE `id` = $userid");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                return $ret[0]['mtype'];
            }
        }
    }

    /**
     *  获得用户的ID
     *
     * @access    public
     * @return    int
     */
    function getMemberID(){
        // 小程序使用userkey确定id
        if(isset($_REQUEST['userkey']) && $_REQUEST['userkey']){
            $userkey = $_REQUEST['userkey'];
            $RenrenCrypt = new RenrenCrypt();
            $userinfo = $RenrenCrypt->php_decrypt(base64_decode($userkey));
            $userinfo = explode('@@@@', $userinfo);
            if(count($userinfo) == 2){
                $openid  = $userinfo[0];
                $session = $userinfo[1];
                $sql = $this->SetQuery("SELECT `id`, `wechat_mini_session` FROM `#@__member` WHERE `wechat_mini_openid` = '$openid'");
                $res = $this->db->prepare($sql);
                $res->execute();
                $res = $res->fetchAll(PDO::FETCH_ASSOC);
                if($res){
                    $now = GetMktime(time());
                    $session_ = $res[0]['wechat_mini_session'];
                    if($session_){
                        $session_arr = explode("#", $session_);
                        $session_val = $session_arr[0];
                        $session_time = $session_arr[1];

                        global $cfg_onlinetime;
                        if($session_val == $session && $session_time + $cfg_onlinetime * 3600 > $now){
                            return $res[0]['id'];
                        }
                    }
                    return -1;
                }
            }
        }
        if($this->userID != '' && $this->checkUserNull($this->userID)){
            return $this->userID;
        }else{
            if(GetCookie($this->keepMemberID) != '' && $this->checkUserNull(GetCookie($this->keepMemberID))){
                $RenrenCrypt = new RenrenCrypt();
                $userid = $RenrenCrypt->php_decrypt(base64_decode(GetCookie($this->keepMemberID)));
                $userinfo = explode('&', $userid);
                if(count($userinfo) != 2){
                    $this->exitUser();
                    return -1;
                }
                $userid = $userinfo[0];
                $password = $userinfo[1];
                $sql = $this->SetQuery("SELECT `id`, `online` FROM `#@__member` WHERE `id` = ".$userid." AND `password` = '$password'");
                $res = $this->db->prepare($sql);
                $res->execute();
                $res = $res->fetchAll(PDO::FETCH_ASSOC);
                if(!$res[0]){
                    $this->exitMember();
                    return -1;
                    // 已退出但cookie还存在:独立域名情况下会有这种情况
                }elseif($res[0]['online'] == 0){

                    //APP端偶尔会出现登录后，登录不成功的情况，此处先做注释，遇到此问题时再做处理。
                    //$this->exitMember();
                    //return -2;

                    // DropCookie($this->keepMemberID);
                    // die('<meta charset="UTF-8"><script type="text/javascript">location.reload();</script>');
                    // die;
                }

                return $userid;
            }else{
                unset($_SESSION[$this->keepMemberID]);
                //$this->exitMember();
                return -1;
            }
        }
    }

    /**
     *  获得用户的权限值
     *
     * @access    public
     * @return    int
     */
    function getPurview(){
        global $dsql;
        $mtype = $this->getUserType();
        if($this->userPurview != ''){
            return $this->userPurview;
        }else{
            $userid = $this->getUserID();
            if(is_numeric($userid)){
                $purview = "";

                //系统管理员
                if($mtype == 0){
                    $sql = $dsql->SetQuery("SELECT member.*,admin.purviews FROM `#@__member` member LEFT JOIN `#@__admingroup` admin ON admin.id = member.mgroupid WHERE member.id = '".$userid."' AND member.mgroupid != '' LIMIT 1");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $purview = $ret[0]['purviews'];
                    }

                    //城市管理员
                }elseif($mtype == 3){
                    $sql = $dsql->SetQuery("SELECT `mtype`, `purviews` FROM `#@__member` WHERE `id` = $userid");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        $mtype = $ret[0]['mtype'];
                        if($mtype == 3){
                            // $purview = $this->getAdminPermissions();
                            // $purview = join(",", $purview);
                            $purview = $ret[0]['purviews'];
                        }
                    }
                }
                return $purview;
            }else{
                $this->exitUser();
                header("location:".HUONIAOADMIN."/login.php");
                die;
            }
        }
    }

    /**
     * 为给定的字符串生成一个加“盐”的散列值
     *
     * @param string $string 即将被散列的字符串
     * @param string $salt 从这个串中提取“盐”
     * @return string 加“盐”之后的散列值
     */
    function _getSaltedHash($string, $salt=NULL){

        //如果没有传入“盐”，则生成一个“盐”
        if($salt == NULL){
            $salt = substr(md5(time()), 0, $this->_saltLength);

            //如果传入了salt，则从中提取真正的"盐"
        }else{
            $salt = substr($salt, 0, $this->_saltLength);
        }
        //将“盐”添加到散列之前并返回散列值
        return $salt.sha1($salt.$string);

    }


    /**
     * 判断会员是否已经登录，如果没有登录，则根据会员类型跳转到不同的登录页面
     *
     */
    function checkUserIsLogin($returnRet = ""){

        global $dirDomain;     //当前页面
        global $cfg_secureAccess;
        global $cfg_secureAccess;
        global $cfg_secureAccess;
        global $cfg_basehost;
        global $template;

        $param = array("service"  => "member");
        $busiDomain = getUrlPath($param);     //商家会员域名

        $basehost = $cfg_secureAccess.$cfg_basehost;  //网站首页域名

        $ischeck = explode($busiDomain, $dirDomain);

        $uid = $this->getMemberID();
        $url = "";
        $changeUser = 0;

        $param = array("service" => "member",	"type" => "user");
        $userDomain = getUrlPath($param);     //个人会员域名

        //如果没有登录，根据访问地址进入不同的登录页面
        if($uid == -1){

            // $url = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            $url = $basehost."/login.html?furl=".urlencode($dirDomain);
            // if(count($ischeck) > 1){
            // 	$basehost = $busiDomain;

            // 	//如果访问的是企业会员中心的域名，如果是独立域名
            // 	if($cfg_basehost != $_SERVER['HTTP_HOST']){
            // 		$url = $cfg_secureAccess.$cfg_basehost."/index.php?service=member&template=login&furl=".urlencode($dirDomain);
            // 		header("location:".$cfg_secureAccess.$cfg_basehost."/index.php?service=member&template=login&furl=".urlencode($dirDomain));
            // 		die;
            // 	}
            // }else{
            // 	$url = $basehost."/login.html?furl=".urlencode($dirDomain);
            // }
            // header("location:".$basehost."/login.html?furl=".urlencode($dirDomain));
            // die;

            //如果已退出
        }elseif($uid == -2){
            $changeUser = 1;
            $url = $basehost."/login.html?furl=".urlencode($dirDomain);
            //如果已经登录，判断用户类型是否符合进入当前页面
        }else{



            $userinfo = $this->getMemberInfo();  //当前登录会员信息

            //个人会员不得进入商家会员中心，如果在商家会员的页面，则自动跳转至开通页面
            if($userinfo['userType'] == 1){
                $ischeck = explode($userDomain, $dirDomain);
                if(count($ischeck) <= 1 && $template != "bindemail"){

                    $param = array("service" => "member", "type" => "user");

                    // 判断是否已经成功提交了入驻申请
                    $archives = $this->SetQuery("SELECT * FROM `#@__business_list` WHERE `uid` = '$uid' AND `state` != 4");
                    $results = $this->db->prepare($archives);
                    $results->execute();
                    $results = $results->fetchAll(PDO::FETCH_ASSOC);
                    if($results){
                        PutCookie("notice_enter_in_wait_audit", 1, 20);
                        $param = array("service" => "member", "type" => "user", "template" => "business-config");
                        $url = getUrlPath($param);
                        header("location:".$url);
                        die;
                        // die('<meta charset="UTF-8"><script type="text/javascript">alert("您的入驻申请已提交，请耐心等待审核");top.location="'.$url.'";</script>');
                    }else{
                        $param['template'] = 'enter';
                    }

                    $business = getUrlPath($param);
                    $url = $business;
                    if($param['template'] == 'enter') $url .= "#join";
                    // header("location:".$business);
                    // die;
                }

                //商家会员不得进入个人中心页面，否则自动跳转商家会员首页
            }elseif($userinfo['userType'] == 2){
                $ischeck = explode($busiDomain, $dirDomain);
                if(count($ischeck) <= 1){
                    //header("location:".$busiDomain);
                    //die;
                }

                //其它情况，跳转到网站个人登录页面
            }else{
                $url = urlencode($cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                $url = $basehost."/login.html?furl=".$url;
                header("location:".$basehost."/login.html?furl=".$url);
                die;
            }

        }
        if($returnRet){
            $data = array(
                "uid" => $uid,
                "url" => $url,
                "changeUser" => $changeUser
            );
            return $data;
        }elseif($url){
            header("location:".$url);
            die;
        }

    }


    /**
     * 论坛同步操作
     * @param array $data     要操作的会员数据
     * @param string $action  动作
     * @return null
     */
    function bbsSync($data, $action){
        global $cfg_bbsState;
        global $cfg_bbsType;
        if($cfg_bbsState == 1 && $cfg_bbsType != "" && $data){

            //discuz
            //if($cfg_bbsType == "discuz"){
            include_once(HUONIAOROOT."/api/bbs/".$cfg_bbsType."/config.inc.php");
            include_once(HUONIAOROOT."/api/bbs/discuz/uc_client/client.php");

            //判断登录
            if($action == "login"){
                $username = $data['username'];
                $password = $data['uPwd'];
                list($uid, $uname, $pword, $email_) = uc_user_login($username, $password);
                return $uid;

                //同步登录
            }elseif($action == "synlogin"){
                $username = $data['username'];
                $password = $data['uPwd'];
                list($uid, $uname, $pword, $email_) = uc_user_login($username, $password);
                if($uid > 0) {
                    $ucsynlogin = uc_user_synlogin($uid);
                    echo $ucsynlogin;
                }

                //同步退出
            }elseif($action == "logout"){
                $ucsynlogout = uc_user_synlogout();
                echo $ucsynlogout;

                //同步注册
            }elseif($action == "register"){
                $username = $data['username'];
                $password = $data['password'];
                $email    = $data['email'];
                $nickname = $data['nickname'];
                $phone    = $data['phone'];
                $qq       = $data['qq'];
                $sex      = $data['sex'];
                $birthday = $data['birthday'];
                $uid = uc_user_register($username, $password, $email, $nickname, $phone, $qq, $sex, $birthday);
                if($uid <= 0) {
                    if($uid == -1) {
                        return '用户名不合法';
                    } elseif($uid == -2) {
                        return '包含要允许注册的词语';
                    } elseif($uid == -3) {
                        return '用户名已经存在';
                    } elseif($uid == -4) {
                        return 'Email 格式有误';
                    } elseif($uid == -5) {
                        return 'Email 不允许注册';
                    } elseif($uid == -6) {
                        return '该 Email 已经被注册';
                    } else {
                        return '未定义';
                    }
                }else {
                    $username = $username;
                }
                if($username) {
                    return '同步成功';
                }

                //同步删除
            }elseif($action == "delete"){
                //根据用户名查询UCenter用户ID
                $info = uc_get_user($data);
                $ucsyndelete = uc_user_delete($info[0]);

                //同步修改
            }elseif($action == "edit"){
                $username = $data['username'];
                $newpw    = $data['newpw'];
                $email    = $data['email'];
                $ucresult = uc_user_edit($username, "", $newpw, $email, 1);

            }

            //phpwind
            //}elseif($cfg_bbsType == "phpwind"){

            //}

        }
    }


    /**
     * 注册送积分
     * @param array $userid   要操作的会员ID
     * @return null
     */
    function registGiving($userid, $givePoint = true){
        include HUONIAOINC."/config/pointsConfig.inc.php";
        if($cfg_pointRegGiving > 0 && $givePoint){
            $sql = $this->SetQuery("UPDATE `#@__member` SET `point` = `point` + $cfg_pointRegGiving WHERE `id` = $userid");
            $ret = $this->db->prepare($sql);
            $ret->execute();
            $ret->closeCursor();

            $date = GetMkTime(time());
            //保存操作日志
            $sql = $this->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$userid', '1', '$cfg_pointRegGiving', '注册获得积分', '$date')");
            $ret = $this->db->prepare($sql);
            $ret->execute();
            $ret->closeCursor();
        }

        // 推荐注册送积分
        $fromShare_ = GetCookie('fromShare');
        $fromShare = $fromShare_ ? (int)$fromShare_ : 0;
        if($fromShare && $fromShare != $userid){
            $archives = $this->SetQuery("SELECT `id` FROM `#@__member` WHERE `id` = '$fromShare' AND `state` = 1");
            $results = $this->db->prepare($archives);
            $results->execute();
            $results = $results->fetchAll(PDO::FETCH_ASSOC);

            if($cfg_pointRegGivingRec && $givePoint){
                $now = time();
                $point = $cfg_pointRegGivingRec;
                $archives = $this->SetQuery("UPDATE `#@__member` SET `point` = `point` + $point WHERE `id` = $fromShare");
                $ret = $this->db->prepare($archives);
                $ret->execute();
                $ret->closeCursor();

                $title = "推荐注册送积分，来自用户ID：".$userid;
                //保存操作日志
                $sql = $this->SetQuery("INSERT INTO `#@__member_point` (`userid`, `type`, `amount`, `info`, `date`) VALUES ('$fromShare', '1', '$point', '$title', '$now')");
                $ret = $this->db->prepare($sql);
                $ret->execute();
                $ret->closeCursor();
            }
            if(is_file(HUONIAOINC.'/config/fenxiaoConfig.inc.php')){
                include HUONIAOINC.'/config/fenxiaoConfig.inc.php'; //分销配置
                if($cfg_fenxiaoState && $cfg_fenxiaoState){
                    $sql = $this->SetQuery("UPDATE `#@__member` SET `from_uid` = $fromShare WHERE `id` = $userid AND `from_uid` = 0");
                    $ret = $this->db->prepare($sql);
                    $ret->execute();
                    $ret->closeCursor();
                }
            }
            DropCookie('fromShare');
        }
    }


    /**
     * 获取后台操作权限集合
     * by 20180116
     * @return array
     */
    function getAdminPermissions(){

        global $dsql;
        $menusArr = array();

        //此处参考了/admin/member/adminGroup.php，如果修改规则，请一并修改
        //载入全局目录（普通功能最多分五级、功能模块最多为六级）
        require(HUONIAODATA."/admin/config_permission.php");
        if(!empty($menuData)){
            //一级
            foreach($menuData as $key => $menu){
                $data = array();
                $menuId = $menu['menuId'];
                if($menu['subMenu']){
                    //二级
                    foreach($menu['subMenu'] as $s_key => $subMenu){
                        $subdata = array();
                        $menuId = $menuId ? $menuId : $subMenu['menuId'];
                        if($subMenu['subMenu']){
                            //三级
                            foreach($subMenu['subMenu'] as $c_key => $childMenu){
                                if($childMenu['subMenu']){
                                    //四级
                                    foreach($childMenu['subMenu'] as $t_key => $threeMenu){
                                        if($threeMenu['menuChild']){
                                            //五级
                                            foreach($threeMenu['menuChild'] as $f_key => $fourMenu){
                                                if($fourMenu['menuChild']){
                                                    //六级
                                                    foreach($fourMenu['menuChild'] as $five_key => $fiveMenu){
                                                        if($fiveMenu['city']){
                                                            array_push($data, array(
                                                                "title" => $fiveMenu['menuName'],
                                                                "mark" => $fiveMenu['menuMark']
                                                            ));
                                                            array_push($subdata, array(
                                                                "title" => $fiveMenu['menuName'],
                                                                "mark" => $fiveMenu['menuMark']
                                                            ));
                                                        }
                                                    }
                                                }
                                                if($fourMenu['city']){
                                                    array_push($data, array(
                                                        "title" => $fourMenu['menuName'],
                                                        "mark" => $fourMenu['menuMark']
                                                    ));
                                                    array_push($subdata, array(
                                                        "title" => $fourMenu['menuName'],
                                                        "mark" => $fourMenu['menuMark']
                                                    ));
                                                }
                                            }
                                        }

                                        $value = $threeMenu['menuUrl'];
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

                                        if($threeMenu['city']){
                                            array_push($data, array(
                                                "title" => $threeMenu['menuName'],
                                                "mark" => $value
                                            ));
                                            array_push($subdata, array(
                                                "title" => $threeMenu['menuName'],
                                                "mark" => $value
                                            ));
                                        }
                                    }
                                }else{
                                    if($childMenu['menuChild']){
                                        //四级
                                        foreach($childMenu['menuChild'] as $f_key => $fourMenu){
                                            if($fourMenu['menuChild']){
                                                //五级
                                                foreach($fourMenu['menuChild'] as $five_key => $fiveMenu){
                                                    if($fiveMenu['city']){
                                                        array_push($data, array(
                                                            "title" => $fiveMenu['menuName'],
                                                            "mark" => $fiveMenu['menuMark']
                                                        ));
                                                        array_push($subdata, array(
                                                            "title" => $fiveMenu['menuName'],
                                                            "mark" => $fiveMenu['menuMark']
                                                        ));
                                                    }
                                                }
                                            }
                                            if($fourMenu['city']){
                                                array_push($data, array(
                                                    "title" => $fourMenu['menuName'],
                                                    "mark" => $fourMenu['menuMark']
                                                ));
                                                array_push($subdata, array(
                                                    "title" => $fourMenu['menuName'],
                                                    "mark" => $fourMenu['menuMark']
                                                ));
                                            }
                                        }
                                    }

                                    $value = $childMenu['menuUrl'];
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

                                    if($childMenu['city']){
                                        array_push($data, array(
                                            "title" => $childMenu['menuName'],
                                            "mark" => $value
                                        ));
                                        array_push($subdata, array(
                                            "title" => $childMenu['menuName'],
                                            "mark" => $value
                                        ));
                                    }
                                }

                            }
                        }

                        if($menu['menuName'] == '模块'){
                            array_push($menusArr, array(
                                "name" => $subMenu['menuName'],
                                "list" => $subdata
                            ));
                        }
                    }
                }

                if($data && $menu['menuName'] != '模块'){
                    array_push($menusArr, array(
                        "name" => $menu['menuName'],
                        "list" => $data
                    ));
                }
            }

        }

        return $menusArr;

    }


    /**
     * 根据当前登录人获取可查看的管理员ID
     * by 20180117
     * @return array
     */
    function getAdminIds(){
        $adminIds = array();
        $adminList = $this->getAdminList();
        if($adminList){
            foreach ($adminList as $key => $value) {
                if($value['list']){
                    foreach ($value['list'] as $k => $v) {
                        array_push($adminIds, $v['id']);
                    }
                }
            }
        }
        return join(',', $adminIds);
    }


    /**
     * 根据当前登录人获取可查看的分站城市ID
     * by 20180117
     * @return array
     */
    function getAdminCityIds(){
        $cityIds = array();
        $adminCity = $this->getAdminCity();
        if($adminCity){
            foreach ($adminCity as $key => $value) {
                array_push($cityIds, $value['id']);
            }
        }
        return join(',', $cityIds);
    }


    /**
     * 根据当前登录人获取可管理的分站城市
     * by 20180117
     * @return array
     */
    function getAdminCity(){
        global $dsql;
        $userid = $this->getUserID();
        $mtype = $this->getUserType();
        $cityArr = array();

        if($mtype == 3){
            $sql = $dsql->SetQuery("SELECT c.*, a.`id` cid, a.`typename`, a.`pinyin` FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` LEFT JOIN `#@__member` m ON m.`mgroupid` = a.`id` WHERE m.`mtype` = 3 AND m.`id` = $userid ORDER BY c.`id`");
            $result = $dsql->dsqlOper($sql, "results");
            if($result){
                foreach ($result as $key => $value) {
                    array_push($cityArr, array(
                        "id" => $value['cid'],
                        "name" => $value['typename'],
                        "pinyin" => substr($value['pinyin'], 0, 1)
                    ));
                }
            }
        }else{
            $sql = $dsql->SetQuery("SELECT c.*, a.`id` cid, a.`typename`, a.`pinyin` FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` ORDER BY c.`id`");
            $result = $dsql->dsqlOper($sql, "results");
            if($result){
                foreach ($result as $key => $value) {
                    array_push($cityArr, array(
                        "id" => $value['cid'],
                        "name" => $value['typename'],
                        "pinyin" => substr($value['pinyin'], 0, 1)
                    ));
                }
            }
        }
        return $cityArr;
    }


    /**
     * 根据当前登录人获取可查看的管理员列表
     * by 20180117
     * @return array
     */
    function getAdminList(){
        global $dsql;
        $groupArr = array();
        $mtype = $this->getUserType();
        $userid = $this->getUserID();
        //判断是否有权限查看管理员列表
        if(testPurview('adminList')){
            //系统管理员可查看所有管理员
            if($mtype == 0){
                $sql = $dsql->SetQuery("SELECT `id`, `groupname` FROM `#@__admingroup`");
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    foreach ($ret as $k => $v) {
                        $groupid = $v['id'];
                        $groupname = $v['groupname'];

                        //管理组下的管理员
                        $memberArr = array();
                        $sql = $dsql->SetQuery("SELECT `id`, `username`, `nickname` FROM `#@__member` WHERE `mtype` = 0 AND `mgroupid` = $groupid");
                        $ret = $dsql->dsqlOper($sql, "results");
                        if($ret){
                            foreach ($ret as $key => $value) {
                                array_push($memberArr, array(
                                    "id" => $value['id'],
                                    "username" => $value['username'],
                                    "nickname" => $value['nickname']
                                ));
                            }
                        }
                        if($memberArr){
                            array_push($groupArr, array(
                                "id" => $groupid,
                                "name" => $groupname,
                                "list" => $memberArr
                            ));
                        }
                    }
                }

                //分站城市
                $cityArr = array();
                $sql = $dsql->SetQuery("SELECT c.*, a.`id` cid, a.`typename` FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` ORDER BY c.`id`");
                $result = $dsql->dsqlOper($sql, "results");
                if($result){
                    foreach ($result as $k => $v) {
                        $cityid = $v['cid'];
                        $cityname = $v['typename'];

                        if($cityid){

                            //分站城市下的管理员
                            $memberArr = array();
                            if($cityid){
                                $sql = $dsql->SetQuery("SELECT `id`, `username`, `nickname` FROM `#@__member` WHERE `mtype` = 3 AND `mgroupid` = $cityid");
                                $ret = $dsql->dsqlOper($sql, "results");
                                if($ret){
                                    foreach ($ret as $key => $value) {
                                        array_push($memberArr, array(
                                            "id" => $value['id'],
                                            "username" => $value['username'],
                                            "nickname" => $value['nickname']
                                        ));
                                    }
                                }
                            }

                        }

                        if($memberArr){
                            array_push($groupArr, array(
                                "id" => $cityid,
                                "name" => $cityname . '分站管理员',
                                "list" => $memberArr
                            ));
                        }
                    }
                }

                //城市管理员只可以查看自己和下属管理员
            }elseif($mtype == 3){

                //首先查看所属城市
                $cityid = 0;
                $cityname = "未知分站管理员";
                $sql = $dsql->SetQuery("SELECT a.`id`, a.`typename` FROM `#@__site_city` c LEFT JOIN `#@__site_area` a ON a.`id` = c.`cid` LEFT JOIN `#@__member` m ON m.`mgroupid` = a.`id` WHERE m.`id` = " . $userid);
                $ret = $dsql->dsqlOper($sql, "results");
                if($ret){
                    $cityid = $ret[0]['id'];
                    $cityname = $ret[0]['typename'];

                    //查看城市分站下的管理员
                    $memberArr = array();
                    $sql = $dsql->SetQuery("SELECT `id`, `username`, `nickname` FROM `#@__member` WHERE `mtype` = 3 AND `mgroupid` = $cityid");
                    $ret = $dsql->dsqlOper($sql, "results");
                    if($ret){
                        foreach ($ret as $key => $value) {
                            array_push($memberArr, array(
                                "id" => $value['id'],
                                "username" => $value['username'],
                                "nickname" => $value['nickname']
                            ));
                        }
                    }
                }

                if($memberArr){
                    array_push($groupArr, array(
                        "id" => $cityid,
                        "name" => $cityname . '分站管理员',
                        "list" => $memberArr
                    ));
                }

            }

        }

        return $groupArr;

    }

}
