<?php
require_once(dirname(__FILE__)."/../../include/common.inc.php");

//文本回复xml 结构
function _response_text($object, $content){
    $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>%d</FuncFlag>
                </xml>";
    $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
    return $resultStr;
}

//单图文回复xml 结构
function _response_news($object, $newsContent){
	$newsTplHead = "<xml>
				    <ToUserName><![CDATA[%s]]></ToUserName>
				    <FromUserName><![CDATA[%s]]></FromUserName>
				    <CreateTime>%s</CreateTime>
				    <MsgType><![CDATA[news]]></MsgType>
				    <ArticleCount>1</ArticleCount>
				    <Articles>";
	$newsTplBody = "<item>
				    <Title><![CDATA[%s]]></Title>
				    <Description><![CDATA[%s]]></Description>
				    <PicUrl><![CDATA[%s]]></PicUrl>
				    <Url><![CDATA[%s]]></Url>
				    </item>";
	$newsTplFoot = "</Articles>
					<FuncFlag>0</FuncFlag>
				    </xml>";

	$header = sprintf($newsTplHead, $object->FromUserName, $object->ToUserName, time());

	$title = $newsContent['title'];
	$desc = $newsContent['description'];
	$picUrl = $newsContent['picUrl'];
	$url = $newsContent['url'];
	$body = sprintf($newsTplBody, $title, $desc, $picUrl, $url);

	$FuncFlag = 0;
	$footer = sprintf($newsTplFoot, $FuncFlag);
	return $header.$body.$footer;
}

//多图文回复xml 结构
function _response_multiNews($object, $newsContent){
	$newsTplHead = "<xml>
				    <ToUserName><![CDATA[%s]]></ToUserName>
				    <FromUserName><![CDATA[%s]]></FromUserName>
				    <CreateTime>%s</CreateTime>
				    <MsgType><![CDATA[news]]></MsgType>
				    <ArticleCount>%s</ArticleCount>
				    <Articles>";
	$newsTplBody = "<item>
				    <Title><![CDATA[%s]]></Title>
				    <Description><![CDATA[%s]]></Description>
				    <PicUrl><![CDATA[%s]]></PicUrl>
				    <Url><![CDATA[%s]]></Url>
				    </item>";
	$newsTplFoot = "</Articles>
					<FuncFlag>0</FuncFlag>
				    </xml>";

	$bodyCount = count($newsContent);
	$bodyCount = $bodyCount < 10 ? $bodyCount : 10;

	$header = sprintf($newsTplHead, $object->FromUserName, $object->ToUserName, time(), $bodyCount);
	foreach($newsContent as $key => $value){
		$body .= sprintf($newsTplBody, $value['title'], $value['description'], $value['picUrl'], $value['url']);
	}

	$FuncFlag = 0;
	$footer = sprintf($newsTplFoot, $FuncFlag);
	return $header.$body.$footer;
}



define("APPID", $cfg_wechatAppid);
define("APPSECRET", $cfg_wechatAppsecret);
define("TOKEN", $cfg_wechatToken);
$wechatObj = new wechat();

//开发者验证
if($_GET["echostr"]){
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechat{

    //微信开发者验证
	public function valid(){
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    //Token验证
    private function checkSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];

		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);

		if($tmpStr == $signature){
			return true;
		}else{
			return false;
		}
	}

    //消息回复
    public function responseMsg(){
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    if(!$postStr){
      $postStr = file_get_contents("php://input");
    }
		if (!empty($postStr)){

      	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

        // require_once dirname(__FILE__)."/../payment/log.php";
        // $logHandler= new CLogFileHandler('wx.log');
        // $log = Log::Init($logHandler, 15);
        // Log::DEBUG("消息：" . json_encode($postObj));

				$RX_TYPE = trim($postObj->MsgType);
        $FromUserName = $postObj->FromUserName;

				switch($RX_TYPE){
					case "text":
						$resultStr = $this->handleText($postObj);
						break;
					case "event":
						$resultStr = $this->handleEvent($postObj);
						break;
					default:
						$resultStr = "Unknow msg type: ".$RX_TYPE;
						break;
				}

        //微信传图首次记录
        if($RX_TYPE == "event" && $postObj->Event == "SCAN" && strstr($postObj->EventKey, '微信传图_')){
          global $dsql;

          $ticket = str_replace('微信传图_', '', $postObj->EventKey);
          $time = $postObj->CreateTime;

          //查询记录是否已经存在
          $expTime = time() - 28800;  //8个小时以内有效
          $sql = $dsql->SetQuery("SELECT `id` FROM `#@__site_wxupimg` WHERE `FromUserName` = '$FromUserName' AND `ticket` = '$ticket' AND `time` > $expTime");
          $ret = $dsql->dsqlOper($sql, "results");
          if(!$ret){
            //插入新记录
            $sql = $dsql->SetQuery("INSERT INTO `#@__site_wxupimg` (`FromUserName`, `ticket`, `time`, `PicUrl`, `MediaId`) VALUES ('$FromUserName', '$ticket', '$time', '', '')");
            $ret = $dsql->dsqlOper($sql, 'update');
          }

          $resultStr = $this->_response($postObj, array('type' => 'text', 'body' => 'HI，您已开启微信传图模式，点击左下的小键盘图标，发送你需要上传的图片吧。'));
          echo $resultStr;
          die;
        }

        //微信传图日志
        if($RX_TYPE == "image"){
          global $dsql;

          $expTime = time() - 28800;  //8个小时以内有效

          //查询微信传图表是否有记录
          $sql = $dsql->SetQuery("SELECT `ticket` FROM `#@__site_wxupimg` WHERE `FromUserName` = '$FromUserName' AND `time` > $expTime ORDER BY `id` DESC");
          $ret = $dsql->dsqlOper($sql, "results");
          if($ret){
            $ticket = $ret[0]['ticket'];

            $time = $postObj->CreateTime;
            $PicUrl = $postObj->PicUrl;
            $MediaId = $postObj->MediaId;

            $fileID = '';
            global $cfg_atlasSize;
            global $cfg_atlasType;
            global $cfg_ftpType;
            global $editor_ftpType;
            global $editor_uploadDir;
            $editor_uploadDir = '/uploads';
            $editor_ftpType = $cfg_ftpType;

            $fileInfo = getRemoteImage(array($PicUrl), array(
                'savePath' => '../../uploads/siteConfig/wxupimg/large/'.date( 'Y' ).'/'.date( 'm' ).'/'.date( 'd' ).'/',
                'maxSize' => $cfg_atlasSize,
                'allowFiles' => explode("|", $cfg_atlasType)
            ), 'siteConfig', '../..', false);

            // Log::DEBUG("上传：" . json_encode($fileInfo));

            if($fileInfo){
    					$fileInfo = json_decode($fileInfo, true);
    					if(is_array($fileInfo) && $fileInfo['state'] == "SUCCESS"){
    						$fileID = $fileInfo['list'][0]['fid'];
    					}
    				}

            $sql = $dsql->SetQuery("INSERT INTO `#@__site_wxupimg` (`FromUserName`, `ticket`, `time`, `PicUrl`, `MediaId`, `fid`) VALUES ('$FromUserName', '$ticket', '$time', '$PicUrl', '$MediaId', '$fileID')");
            $dsql->dsqlOper($sql, 'update');

          }else{
            //已经过期的
            $resultStr = $this->_response($postObj, array('type' => 'text', 'body' => '微信传图已超时，请重新扫码上传！'));
            echo $resultStr;
            die;
          }

          die;  //目前暂无图片类型的交互
        }

				echo $resultStr;

        //注册
        if($RX_TYPE == "event" && $postObj->Event == "subscribe"){
            $this->bindSiteMember($postObj->FromUserName);
        }

        exit;

		}else{
			echo "";
			exit;
		}
	}


    //普通文本回复
	public function handleText($postObj){
        $keyword = trim($postObj->Content);
        if(!empty($keyword)){
            $contentStr = $this->getAutoreply($keyword);
            $resultStr = $this->_response($postObj, $contentStr);
			// $resultStr = _response_text($postObj, $contentStr);
            echo $resultStr;
        }else{
            echo "Input something...";
        }
    }

    //关注回复
    public function handleEvent($object){
        $contentStr = "";
        switch ($object->Event){
            case "subscribe":
                $contentStr = $this->getAutoreply("subscribe");
                break;
            default:
                $contentStr = $this->getAutoreply($object->EventKey);
                break;
        }
        $resultStr = $this->_response($object, $contentStr);
        // $resultStr = _response_text($object, $contentStr);

        return $resultStr;
    }


    //根据类型组合相应的XML
    public function _response($obj, $con){
      //引入配置文件
      $wechatConfig = HUONIAOINC."/config/wechatConfig.inc.php";
      if(!file_exists($wechatConfig)) return;
      require($wechatConfig);

      include_once(HUONIAOROOT."/include/class/WechatJSSDK.class.php");
      $jssdk = new WechatJSSDK($cfg_wechatAppid, $cfg_wechatAppsecret);
      $weixinAccessToken = $jssdk->getAccessToken();

        if($con){
            $type = $con['type'];
            $body = $con['body'];

            //普通文本
            if($type == "text"){
                return _response_text($obj, $body);
            }

            //单图文
            if($type == "news"){

              //先发客服接口而且原文链接不为空，并且后台开发了强制跳转
              if($body[0]['content_source_url'] && $cfg_wechatRedirect){
                $data = '{
                  "touser":"'.$obj->FromUserName.'",
                  "msgtype":"news",
                  "news":{
                    "articles": [{
                         "title":"'.$body[0]['title'].'",
                         "description":"'.$body[0]['digest'].'",
                         "url":"'.$body[0]['content_source_url'].'",
                         "picurl":"'.$body[0]['thumb_url'].'"
                    }]
                  }
                }';

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$weixinAccessToken);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $result = json_decode(curl_exec($curl), true);
                curl_close($curl);
                if($result['errcode'] == 0){
                  echo '';die;

                //如果客服接口发送失败，走普通消息接口
                }else{
                  $data = array(
                    'title' => $body[0]['title'],
                    'description' => $body[0]['digest'],
                    'picUrl' => $body[0]['thumb_url'],
                    'url' => $body[0]['url']
                  );
                  return _response_news($obj, $data);
                }

              //原文链接为空的走普通消息接口
              }else{
                $data = array(
                  'title' => $body[0]['title'],
                  'description' => $body[0]['digest'],
                  'picUrl' => $body[0]['thumb_url'],
                  'url' => $body[0]['url']
                );
                return _response_news($obj, $data);
              }

            }

            //多图文
            if($type == "multiNews"){

              //如果开启了强制跳转
              if($cfg_wechatRedirect){

                $articles = array();
                foreach ($body as $key => $value) {
                  $url = $value['content_source_url'] ? $value['content_source_url'] : $value['url'];
                  array_push($articles, '{
                       "title":"'.$value['title'].'",
                       "description":"'.$value['digest'].'",
                       "url":"'.$url.'",
                       "picurl":"'.$value['thumb_url'].'"
                  }');
                }

                $articles = join(',', $articles);

                $data = '{
                  "touser":"'.$obj->FromUserName.'",
                  "msgtype":"news",
                  "news":{
                    "articles": ['.$articles.']
                  }
                }';

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$weixinAccessToken);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $result = json_decode(curl_exec($curl), true);
                curl_close($curl);
                if($result['errcode'] == 0){
                  echo '';die;

                //如果客服接口发送失败，走普通消息接口
                }else{
                  $data = array();
                  foreach ($body as $key => $value) {
                      array_push($data, array(
                          'title' => $value['title'],
                          'description' => $value['digest'],
                          'picUrl' => $value['thumb_url'],
                          'url' => $value['url']
                      ));
                  }
                  return _response_multiNews($obj, $data);
                }

              }else{
                $data = array();
                foreach ($body as $key => $value) {
                    array_push($data, array(
                        'title' => $value['title'],
                        'description' => $value['digest'],
                        'picUrl' => $value['thumb_url'],
                        'url' => $value['url']
                    ));
                }
                return _response_multiNews($obj, $data);
              }

            }
        }
        return "";
    }


    //根据关键字获取系统响应内容
    public function getAutoreply($key){

        //关注回复
        if($key == "subscribe"){
            global $cfg_wechatSubscribeType;
            global $cfg_wechatSubscribe;
            global $cfg_wechatSubscribeMedia;

            //自定义
            if($cfg_wechatSubscribeType == 1){
                return array("type" => "text", "body" => stripslashes($cfg_wechatSubscribe));
            }

            //微信素材
            if($cfg_wechatSubscribeType == 2){
                $news_item = $this->getWechatResource($cfg_wechatSubscribeMedia);
                if(count($news_item) > 1){
                    return array("type" => "multiNews", "body" => $news_item);
                }else{
                    return array("type" => "news", "body" => $news_item);
                }
            }

        //其他回复
        }else{
            global $dsql;
            $sql = $dsql->SetQuery("SELECT `type`, `body`, `media` FROM `#@__site_wechat_autoreply` WHERE `key` = '$key' LIMIT 1");
            $ret = $dsql->dsqlOper($sql, "results");
            if($ret){
                $type  = $ret[0]['type'];
                $body  = $ret[0]['body'];
                $media = $ret[0]['media'];

                //普通文本
                if($type == 1){
                    return array("type" => "text", "body" => "$body");
                }

                //微信素材
                if($type == 2){

                    $news_item = $this->getWechatResource($media);
                    if(count($news_item) > 1){
                        return array("type" => "multiNews", "body" => $news_item);
                    }else{
                        return array("type" => "news", "body" => $news_item);
                    }

                }
            }
        }
        return "";
    }


    //获取微信永久素材
    public function getWechatResource($media){

      //引入配置文件
      $wechatConfig = HUONIAOINC."/config/wechatConfig.inc.php";
      if(!file_exists($wechatConfig)) return;
      require($wechatConfig);

      include_once(HUONIAOROOT."/include/class/WechatJSSDK.class.php");
      $jssdk = new WechatJSSDK($cfg_wechatAppid, $cfg_wechatAppsecret);
      $weixinAccessToken = $jssdk->getAccessToken();

    	// $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$cfg_wechatAppid&secret=$cfg_wechatAppsecret";
        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_HEADER, 0);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_POST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // $output = curl_exec($ch);
        // curl_close($ch);
        // if(empty($output)){
    	// 	return;
    	// }
        // $result = json_decode($output, true);
    	// if(isset($result['errcode'])) {
    	// 	return;
    	// }
        //
        // $token = $result['access_token'];

        //获取素材列表
    	$pageSize = 20;
      $url = "https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=$weixinAccessToken";
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, '{"media_id": "'.$media.'"}');
      curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $output = curl_exec($ch);
      curl_close($ch);
    	if(empty($output)){
    		return;
    	}
    	$result = json_decode($output, true);
    	if(isset($result['errcode'])) {
    		return;
    	}

    	return $result['news_item'];

    }


    //关注后绑定网站帐号
    public function bindSiteMember($openid){

      //引入配置文件
      $wechatConfig = HUONIAOINC."/config/wechatConfig.inc.php";
      if(!file_exists($wechatConfig)) return;
      require($wechatConfig);

      include_once(HUONIAOROOT."/include/class/WechatJSSDK.class.php");
      $jssdk = new WechatJSSDK($cfg_wechatAppid, $cfg_wechatAppsecret);
      $weixinAccessToken = $jssdk->getAccessToken();


        // $curl = curl_init();
        // curl_setopt($curl, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET);
        // curl_setopt($curl, CURLOPT_HEADER, 0);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        // $token = json_decode(curl_exec($curl), true);
        // curl_close($curl);

        // $access_token = $token['access_token'];
        if($access_token){

            //获取用户信息
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$weixinAccessToken."&openid=".$openid);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            $user_info = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if(is_array($user_info)){

                $key      = $user_info['unionid'];
                $key      = $key ? $key : $user_info['openid'];
                // $key      = $openid;
                $nickname = trim($user_info['nickname']);
                $photo    = trim($user_info['headimgurl']);
                $gender   = $user_info['sex'] == 1 ? '男' : '女';

                //登录验证
                $userLogin = new userLogin($dbo);

                $data = array(
                    "code"     => "wechat",
                    "key"      => $key,
                    "openid"   => $user_info['openid'],
                    "nickname" => $nickname,
                    "photo"    => $photo,
                    "gender"   => $gender,
                    "noRedir"  => "1"
                );

                $userLogin->loginConnect($data);

            }

        }

    }


}
