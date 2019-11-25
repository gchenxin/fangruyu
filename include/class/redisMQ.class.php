<?php
/**
* @file Redis.class.php
* @brief RedisMQ
* @author cgchenxin
* @version v1.0
* @date 2019-11-21
 */

class redisMQ{
	public static $connect;
    public static $config;
	public static function init($param = []){
		self::$config = [
			"host" => '127.0.0.1',
			"port" => 6379,
			"password" => '3e8d1af87f84fee9',
			'tube' => 'redismq_dev'
		];
		self::$config = array_merge(self::$config,$param);
		if(!self::$connect || (self::$connect->ping() != '+PONG')){
			self::$connect = new \Redis();
			self::$connect->pconnect(self::$config['host'], self::$config['port']);
			self::$connect->auth(self::$config['password']);
		}
	}
	public static function get($name){
		return self::$connect->get($name);
	}
	public static function set($key,$value,$expire){
		return self::$connect->set($key,$value,$expire);
	}
	public static function delete($name){
		return self::$connect->delete($name);
	}

	public static function pubUrl($url){
		if(!$url)	return;
		self::init();
		$data = [
			'url' => $url,
			'type' => 0
		];
		self::$connect->publish(self::$config['tube'], json_encode($data));
	}

	public static function asyncExec($service, $action, $param = []){
		if(!$url)	return;
		self::init();
		$data = [
			'service' => $service,
			'action' => $action,
			'param' => $param,
			'type' => 1
		];
		self::$connect->publish(self::$config['tube'], json_encode($data));
	}

	public static function test(){
		self::init();
		self::set('tt','test',10);
		echo self::get('tt');
	}

	public function worker(){
		while(true){
			try{
				self::init();
				self::$connect->subscribe([self::$config['tube']],function($instance, $channel, $msg){
					echo $channel ."=>" . $msg . PHP_EOL;
				});
			}catch(\RedisException $e){
				echo "断开一次！".PHP_EOL;
				self::$connect->close();
			}   
		}
		self::$connect->close();
	}
}
