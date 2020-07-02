<?php
    
	require_once(dirname (dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'services/Core.php');
	require_once(dirname (dirname ( __FILE__ )).DIRECTORY_SEPARATOR.'services/Net.php');
    
    if (function_exists("date_default_timezone_set")) {
        date_default_timezone_set(Config::$timezone);
    }
    
    class Services{
        public static function trade(Array $params) {
            return Core::createLinkString($params, false, true);
        }
        public static function query(Array $params,Array &$resp) {
            $req_str=self::buildReq($params);
            $resp_str=Net::sendMessage($req_str, Config::QUERY_URL);
            return self::verifyResponse($resp_str, $resp);
        }
        
        public static function buildSignature(Array $params,$secure_key) {
            $filteredReq=Core::paraFilter($params);
            return Core::buildSignature($filteredReq,$secure_key);
        }
        
        private static function buildReq(Array $params) {
            return Core::createLinkString($params, false, true);
        }
        
        public static function verifySignature($para,$secure_key){
            $respSignature=$para[Config::SIGNATURE_KEY];
            $filteredReq=Core::paraFilter($para);
            $signature=Core::buildSignature($filteredReq,$secure_key);
            if ($respSignature!=""&&$respSignature==$signature) {
                return TRUE;
            }else {
                return FALSE;
            }
        }
        
        public static function verifyResponse($resp_str,&$resp){
            if ($resp_str!="") {
                parse_str($resp_str,$para);
        
                $signIsValid=self::verifySignature($para);
                $resp=$para;
                if ($signIsValid) {
                    return TRUE;
                }else{
                    return FALSE;
                }
            }
        }
    }