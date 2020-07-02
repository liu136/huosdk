<?php

/**
 * sdk公用函数
 */
if (!defined('IN_SYS')) {
    exit('Access Denied');
}

/**
 * 检查字符串是否表示金额，此金额小数点后最多带2位
 * 
 * @param str 需要被检查的字符串
 * @return ： true－表示金额，false-不表示金额
 */
function checkAmount($amount) {
    if (empty($amount)) {
        return false;
    }
    $checkExpressions = "/^[0-9]+(.[0-9]{1,2})?$/";
    return preg_match($checkExpressions, $amount);
}

// 生成订单号
function setorderid($cid) {
    list($usec, $sec) = explode(" ", microtime());
    
    // 取微秒前3位+再两位随机数+渠道ID后四位
    $orderid = $sec . substr($usec, 2, 3) . rand(10, 99) . sprintf("%04d", $cid % 10000);
    return $orderid;
}

function auth_code($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 0;
    
    $key = md5($key ? $key : AUTHCODE);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(
            md5(microtime()), 
            -$ckey_length)) : '';
    
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf(
            '%010d', 
            $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    
    $result = '';
    $box = range(0, 255);
    //$box = 100;
    
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    
    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(
                md5(substr($result, 26) . $keyb), 
                0, 
                16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

// 获取IP
function get_client_ip() {
    $client_ip = "";
    if (getenv('HTTP_CLIENT_IP')) {
        $client_ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $client_ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR')) {
        $client_ip = getenv('REMOTE_ADDR');
    } else {
        $client_ip = $_SERVER['REMOTE_ADDR'];
    }
    return $client_ip;
}

/**
 * POST方式请求数据
 * 
 * @param $url 请求的地址 ;
 * @param $data_string 数据
 *
 * @return 加密字符串
 *
 */
function http_post_data($url, $data_string) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt(
            $ch, 
            CURLOPT_HTTPHEADER, 
            array(
                    'Content-Type: application/json; charset=utf-8', 
                    'Content-Length: ' . strlen($data_string) 
            ));
    ob_start();
    curl_exec($ch);
    $return_content = ob_get_contents();
    ob_end_clean();
    
    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $return_content;
}

/**
 * 函数的含义说明
 * @date: 2015年10月15日下午5:21:16
 * 
 * @param $clientid 渠道ID
 * @param $username 用户名
 * @return $string 添加头后的用户名
 * @since 1.0
 */
function addclheader($clientid, $username) {
    return $clientid . '_' . $username;
}

/**
 * 用户名去除头
 * 
 * @param $lusername 渠道用户名
 *
 * @return 添加头
 */
function declheader($lusername) {
    return substr($lusername, strpos($lusername, '_') + 1);
}

/**
 * 与服务器交互
 * 
 * @param $data 数据
 * @param $key 加密的key
 *
 * @return 返回加密数据
 */
function getLmdata($str, $cid, $clientkey, $func) {
    $param = $str . "&clientkey=" . $clientkey;
    
    $md5params = md5($param);
    $params = $str . "&sign=" . $md5params;
    $params = urlencode($params);
    $postdata = "c=" . $cid . "&a=$func&t=" . time() . "&d=" . auth_code($params, 'ENCODE', $clientkey);
    $postdata = json_encode(urlencode($postdata));
    $url = LMWEBSITE . "/index.php/AppInterface/Clientapi/clientapi";
    
    $cnt = 0;
    while (1) {
        $return_content = base64_decode(http_post_data($url, $postdata));
        // $return_content = base64_decode('cj0xJmE9TURBd01EQXdNREF3TURnek9EQTNZbU01WkdZellUVmlZakl4TWpNME5UWTNPRGt3');
        parse_str($return_content, $rdata);
        if (0 < $rdata['r'] || 3 == $cnt) {
            break;
        }
        $cnt++;
    }
    return $rdata;
}
function combineData($str, $cid, $clientkey, $func) {
    $param = $str . "&clientkey=" . $clientkey;
    $md5params = md5($param);
    $params = $str . "&sign=" . $md5params;
    $params = urlencode($params);
    $time = time();
    $d = auth_code($params, 'ENCODE', $clientkey);
    $postdata = "c={$cid}&a={$func}&t={$time}&d={$d}";
    $postdata = urlencode($postdata);
    return $postdata;
}

/**
 * 回调联盟数据
 * @date: 2015年10月15日下午7:40:20
 * 
 * @param $postdata post数据
 * @return array
 * @since 1.0
 */
function postLm($postdata) {
    $postdata = json_encode($postdata);
    $url = LMWEBSITE . "/index.php/AppInterface/Clientapi/clientapi";
    $cnt = 0;
    while (1) {
        $return_content = base64_decode(http_post_data($url, $postdata));
        parse_str($return_content, $rdata);
        if (0 < $rdata['r'] || 4 == $cnt) {
            break;
        }
        $cnt++;
    }
    return $rdata;
}

function dm_getDbname($cid){
    if(!is_numeric($cid) || $cid < 1){
        return NULL;
    }

    $sql = "select db_name from `".MNG_DB_NAME."`.`".LDB_PREFIX."client` where id=:cid order by id desc";
    $db->bind("cid", $cid);
    $dbname = $db->single($sql);    

    if (isset($dbname) && $dbname != ''){
        return 'db_sdk_' . $dbname;
    }
    return NULL;
}

?>