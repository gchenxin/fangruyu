<?php
/**
 * 人人网 API client for PHP
 *
 * @author PiscDong (http://www.piscdong.com/)
 */
class renrenPHP
{
	public $api_url='https://api.renren.com/v2/';

	public function __construct($client_id, $client_secret, $access_token=NULL){
		$this->client_id=$client_id;
		$this->client_secret=$client_secret;
		$this->access_token=$access_token;
	}

	//生成授权网址
	public function login_url($callback_url, $scope=''){
		$params=array(
			'response_type'=>'code',
			'client_id'=>$this->client_id,
			'redirect_uri'=>$callback_url,
			'scope'=>$scope
		);
		return 'https://graph.renren.com/oauth/authorize?'.http_build_query($params);
	}

	//获取access token
	public function access_token($callback_url, $code){
		$params=array(
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'client_id'=>$this->client_id,
			'client_secret'=>$this->client_secret,
			'redirect_uri'=>$callback_url
		);
		$url='https://graph.renren.com/oauth/token';
		return $this->http($url, http_build_query($params), 'POST');
	}

	//使用refresh token获取新的access token
	public function access_token_refresh($refresh_token){
		$params=array(
			'grant_type'=>'refresh_token',
			'refresh_token'=>$refresh_token,
			'client_id'=>$this->client_id,
			'client_secret'=>$this->client_secret
		);
		$url='https://graph.renren.com/oauth/token';
		return $this->http($url, http_build_query($params), 'POST');
	}

	//获取登录用户信息
	public function me(){
		$params=array();
		return $this->api('user/login/get', $params, 'GET');
	}

	//更新状态
	public function setStatus($status){
		$params=array(
			'content'=>$status
		);
		return $this->api('status/put', $params, 'POST');
	}

	//获取用户的状态列表
	public function getStatus($uid, $count=10, $page=1){
		$params=array(
			'ownerId'=>$uid,
			'pageSize'=>$page,
			'pageNumber'=>$count
		);
		return $this->api('status/list', $params, 'GET');
	}

	//调用接口
	/**
	//示例：获取登录用户信息
	$result=$renren->api('user/login/get', array(), 'GET');
	**/
	public function api($url, $params=array(), $method='GET'){
		$url=$this->api_url.$url;
		$headers[]='Authorization: Bearer '.$this->access_token;
		if($method=='GET'){
			$result=$this->http($url.'?'.http_build_query($params), '', 'GET', $headers);
		}else{
			$result=$this->http($url, http_build_query($params), 'POST', $headers);
		}
		return $result;
	}

	//提交请求
	private function http($url, $postfields='', $method='GET', $headers=array()){
		$ci=curl_init();
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		if($method=='POST'){
			curl_setopt($ci, CURLOPT_POST, TRUE);
			if($postfields!='')curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
		}
		$headers[]='User-Agent: Renren.PHP(piscdong.com)';
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response=curl_exec($ci);
		curl_close($ci);
		$json_r=array();
		if($response!='')$json_r=json_decode($response, true);
		return $json_r;
	}
}
