<?php
class SendRequest
{
    private $AccessKeyID;
    private $appSecret;

    const   SERVERAPIURL = 'https://api.kumanyun.com';    //IM服务地址

    /**
     * 参数初始化
     * @param $AccessKeyID
     * @param $appSecret
     * @param string $format
     */
    public function __construct($AccessKeyID,$appSecret){
        $this->AccessKeyID = $AccessKeyID;
        $this->appSecret = $appSecret;
    }

    /**
     * 创建http header参数
     * @param array $data
     * @return bool
     */
    private function createHttpHeader() {
        $nonce = mt_rand();
        $timeStamp = time();
        $sign = sha1($this->appSecret.$nonce.$timeStamp);
        return array(
                'KM-AccessKeyID:'.$this->AccessKeyID,
                'KM-Nonce:'.$nonce,
                'KM-Timestamp:'.$timeStamp,
                'KM-Signature:'.$sign,
        );
    }

    /**
     * 重写实现 http_build_query 提交实现(同名key)key=val1&key=val2
     * @param array $formData 数据数组
     * @param string $numericPrefix 数字索引时附加的Key前缀
     * @param string $argSeparator 参数分隔符(默认为&)
     * @param string $prefixKey Key 数组参数，实现同名方式调用接口
     * @return string
     */
    private function build_query($formData, $numericPrefix = '', $argSeparator = '&', $prefixKey = '') {
        $str = '';
        foreach ($formData as $key => $val) {
            if (!is_array($val)) {
                $str .= $argSeparator;
                if ($prefixKey === '') {
                    if (is_int($key)) {
                        $str .= $numericPrefix;
                    }
                    $str .= urlencode($key) . '=' . urlencode($val);
                } else {
                    $str .= urlencode($prefixKey) . '=' . urlencode($val);
                }
            } else {
                if ($prefixKey == '') {
                    $prefixKey .= $key;
                }
                if (isset($val[0]) && is_array($val[0])) {
                    $arr = array();
                    $arr[$key] = $val[0];
                    $str .= $argSeparator . http_build_query($arr);
                } else {
                    $str .= $argSeparator . $this->build_query($val, $numericPrefix, $argSeparator, $prefixKey);
                }
                $prefixKey = '';
            }
        }
        return substr($str, strlen($argSeparator));
    }

    /**
     * 发起 server 请求
     * @param $action
     * @param $params
     * @param $httpHeader
     * @return mixed
     */
    public function curl($action, $params, $contentType = 'urlencoded', $httpMethod = 'POST') {

        $action = self::SERVERAPIURL.$action;
        $httpHeader = $this->createHttpHeader();
        $ch = curl_init();
        if ($httpMethod=='POST' && $contentType=='urlencoded') {
            $httpHeader[] = 'Content-Type:application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->build_query($params));
        }
        if ($httpMethod=='POST' && $contentType=='json') {
            $httpHeader[] = 'Content-Type:Application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params) );
        }
        if ($httpMethod=='GET' && $contentType=='urlencoded') {
            $action .= strpos($action, '?') === false?'?':'&';
            $action .= $this->build_query($params);
        }
        curl_setopt($ch, CURLOPT_URL, $action);
        curl_setopt($ch, CURLOPT_POST, $httpMethod=='POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        //curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
        if (false === $ret) {
            $ret =  curl_errno($ch);
        }
        curl_close($ch);
        return $ret;
    }
}