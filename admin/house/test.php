<?php

function query_address($key_words){
	$header[] = 'Referer: http://lbs.qq.com/webservice_v1/guide-suggestion.html';
	$header[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36';
	$url ="http://apis.map.qq.com/ws/place/v1/suggestion/?&region=&key=OB4BZ-D4W3U-B7VVO-4PJWW-6TKDJ-WPB77&keyword=".$key_words; 

	$ch = curl_init();
	//设置选项，包括URL
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);

	//执行并获取HTML文档内容
	$output = curl_exec($ch);
	// print_r($output);die;
	//释放curl句柄
	curl_close($ch);
	// return $output;
	$result = json_decode($output,true);
	// print_r($result);
	// $res = $result['data'][0];
	return $result;
	//echo json_encode(['error_code'=>'SUCCESS','reason'=>'查询成功','result'=>$result);
}

$data = query_address('重庆市磁器口-地铁站')['data'];

var_dump($data);
