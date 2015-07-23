<?php
class tr{
    static function getParam($str = null,$default=null)
    {
        parse_str(file_get_contents('php://input'), $data);
        add_s($data);
        $data = $data? $data:array();
        $all = array_merge($_REQUEST, $data);
        if (!$str) {
            return $all;
        }
        return isset($all[$str]) ? $all[$str] : $default;
    }

    static function config()
    {
        return tr_config::config();
    }

    static function getPath(){
        $path_info = '/';
        if (! empty($_SERVER['PATH_INFO'])) {
            $path_info = $_SERVER['PATH_INFO'];
        } elseif (! empty($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] !== '/index.php') {
            $path_info = $_SERVER['ORIG_PATH_INFO'];
        } else {
            if (! empty($_SERVER['REQUEST_URI'])) {
                $path_info = (strpos($_SERVER['REQUEST_URI'], '?') > 0) ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];
            }
        }
        return $path_info;
    }

   static function getApp(){
       list($app) = self::getVersion();
       return $app;
    }

    static function parsePath(){
        $path_info = self::getPath();
        $path_info = trim($path_info,'/');
        $pathinfoArr = explode("/",$path_info);
        $app = array_shift($pathinfoArr);
        $version = array_shift($pathinfoArr);
        return array($app,$version);
    }

    static function getVersion(){
        list($app,$apiVersion) = self::parsePath();
        preg_match("/([a-zA-Z]+)/i",$apiVersion,$match);
        if(!isset($match[1])){
            tr_hook::fire('404');
            return ;
        }
        $from = substr($match[1],0,1);
        $secondFrom =  strlen($match[1])>1?substr($match[1],1):"";
        $apiVersion = str_replace($match[1],"",$apiVersion);
        $apiVersion = !strstr($apiVersion,".")?$apiVersion.".0":$apiVersion;
        if($apiVersion <0 ){
            tr_hook::fire('404');
            return ;
        }
        return array($app,$apiVersion,$from,$secondFrom);
    }
}