<?php
date_default_timezone_set('Asia/Shanghai');
include_once ROOT_PATH . "/function.php";
include_once ROOT_PATH . "/lib/tr/config.php";
$configApp = tr_config::config()->get("app");
$namespaces = $configApp['namespaces'];
$apps = $configApp['apps'];

class tr_init
{
    private static $_instance = null;
    public static $elapsedTime=0;

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        spl_autoload_register(array($this, 'loader'));
    }

    function create()
    {
        tr_init::$elapsedTime = getmicrotime();
        ini_set('display_errors',1);
        if(!$this->isdebug()){
            error_reporting(0);
        }else{
            error_reporting(E_ALL);
        }
        try {
            $this->hook();
            $this->loadDao();
            $this->initialize();
            $this->route();
        } catch (Exception $e) {
            $errors = $e->getMessage()."\r\n";
            $errors .= $e->getTraceAsString();
            if($this->isdebug()) {
                echo $errors;
            }
        }
    }

    function loadDao(){
        require_once ROOT_PATH . "/dao.php";
    }

    function initialize()
    {
        //输入过滤
        if (!get_magic_quotes_gpc()) {
            !empty($_POST) && add_s($_POST);
            !empty($_GET) && add_s($_GET);
            !empty($_COOKIE) && add_s($_COOKIE);
            !empty($_FILES) && add_s($_FILES);
        }
    }


    function hook()
    {
        include_once ROOT_PATH . "/app/hook.php";
    }

    function route()
    {
        tr_hook::add("404", function() {
            echo "Page Not found";
        });
        $routeConfigTmp = tr_config::config()->get("route");
        list($app,$version,$from,$secondFrom) = tr::getVersion();
        $routeConfig = $routeConfigTmp[$app];
        if(!$routeConfig){
            tr_hook::fire("404");
        }
        if($routeConfig){
            $newRouteConfig = array();
            $newRouteConfigTmp = array();
            arsort($routeConfig);
            foreach($routeConfig as $k=>$v){
                if($v){
                    if($version<$k) continue;
                    foreach($v as $kEnd=>$vEnd){
                        $routetmp = $app."/".$from.$secondFrom.$k."/".$kEnd;
                        $routetmp = str_replace("//",'/',$routetmp);
                        $newRouteConfig[$routetmp]=$vEnd;
                        $newRouteConfigTmp[$kEnd]=$vEnd;
                    }
                }
            }
//            print_r($newRouteConfig);
//            print_r($newRouteConfigTmp);
            tr_route::serve($newRouteConfig,$newRouteConfigTmp);
        }else{
            tr_hook::fire("404");
        }
    }


    function isdebug()
    {
        return tr::config()->get("app.debug");
    }

    function loader($className)
    {
        global $namespaces, $apps;
        $libpath = dirname(__FILE__) . "/../../lib";
        $apppath = dirname(__FILE__) . "/../../app";
        if (strstr($className, '_')) {
            $pathArr = explode('_', $className);
            if ($pathArr) {
                if (in_array($pathArr[0], $namespaces)) {
                    $path = "";
                    foreach ($pathArr as $v) {
                        $path .= $v . "/";
                    }
                    $path = trim($path, "/");
                    require_once $libpath . "/" . $path . ".php";
                }
                if (in_array($pathArr[0], $apps)) {
                    $path = "";
                    foreach ($pathArr as $v) {
                        $path .= $v . "/";
                    }
                    $path = trim($path, "/");
                    require_once $apppath . "/" . $path . ".php";
                }
            }
        } else {
            if (!in_array($className, $namespaces)) return true;
            require_once $libpath . "/" . $className . "/" . $className . ".php";
        }
    }


}