<?php

class Wechat {
	private $appId;
	private $appSecret;

	public function __construct($appId, $appSecret){
		$this->appId = $appId;
		$this->appSecret = $appSecret;
	}

	public function getWxUser(){
		global $cfg_secureAccess;
		if(empty($_GET['code'])){
			//微信服务器回调url
			//$callback = $cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
			$callback = $cfg_secureAccess.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$this->getCode($callback);
		}else{
			$code = $_GET['code'];
			$data = $this->getAccessToken($code);//获取网页授权access_token和用户openid
			$data_all = $this->getUserInfo($data['access_token'],$data['openid']);
			return $data_all;
		}
	}

	public function getAccessToken($code){
		$appid = $this->appId;
		$appsecret = $this->appSecret;    
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
		$user = json_decode(file_get_contents($url));
		if (isset($user->errcode)) {
			echo 'error:' . $user->errcode.'<hr>msg  :' . $user->errmsg;exit;
		}
		$data = json_decode(json_encode($user),true);//返回的json数组转换成array数组
		return $data;
	}

	public function getCode($callback){
		$appid = $this->appId;
		$scope = 'snsapi_userinfo';
		$state = md5(uniqid(mt_rand(), TRUE));//唯一ID标识符绝对不会重复
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($callback) .  '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
		header("Location:$url");
	}

	public function getUserInfo($access_token, $openid){
		//查询是否有该用户的信息
		global $dsql;
		$sql = $dsql->SetQuery("select * from #@__user_wechat where openid='{$openid}'");
		$result = $dsql->dsqlOper($sql, 'results');
		if($result && empty($result['state'])){
			return $result[0];
		}
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . 'lang=zh_CN';
		$user = json_decode(file_get_contents($url));
		if (isset($user->errcode)) {
			echo 'error:' . $user->errcode.'<hr>msg  :' . $user->errmsg;exit;
		}
		$data = json_decode(json_encode($user),true);//返回的json数组转换成array数组
		$insertSql = $dsql->SetQuery("insert into #@__user_wechat(openid,unionid,nickname,headimg) values('{$data['openid']}','{$data['unionid']}','{$data['nickname']}','{$data['headimgurl']}')");
		$dsql->dsqlOper($insertSql,'update');
		return $data;
	}
}
