<?php
class tr_route{
    public static function serve($routes,$routesTmp=array())
    {
        tr_hook::fire('before_request', compact('routes'));
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

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

        $discovered_handler = null;
        $regex_matches = array();
        list($discovered_handler,$regex_matches)=self::match($routes,$path_info);

        $result = null;
        $handler_instance = null;

        //如果没有匹配
        if(!$discovered_handler){
            list($app,$apiVersion,$from,$secondFrom)=tr::getVersion();
            $path_infoNew = str_replace("/".$app."/".$from.$secondFrom.$apiVersion,"",$path_info);
            list($discovered_handler,$regex_matches)=self::match($routesTmp,$path_infoNew);
        }

        if ($discovered_handler) {
            if(stristr($discovered_handler,"@")){
                list($className,$method) = explode("@",$discovered_handler);
                $handler_instance = new $className();
                $request_method = $method;
            }else{
                if (is_string($discovered_handler)) {
                    $handler_instance = new $discovered_handler();
                } elseif (is_callable($discovered_handler)) {
                    $handler_instance = $discovered_handler();
                }
            }
        }

        if ($handler_instance) {
            unset($regex_matches[0]);

            if (method_exists($handler_instance, $request_method)) {
                tr_hook::fire('before_handler', compact('routes', 'discovered_handler', 'request_method', 'regex_matches'));
                $result = call_user_func_array(array($handler_instance, $request_method), $regex_matches);
                tr_hook::fire('after_handler', compact('routes', 'discovered_handler', 'request_method', 'regex_matches', 'result'));
            } else {
                tr_hook::fire('404', compact('routes', 'discovered_handler', 'request_method', 'regex_matches'));
            }
        } else {
            tr_hook::fire('404', compact('routes', 'discovered_handler', 'request_method', 'regex_matches'));
        }

        tr_hook::fire('after_request', compact('routes', 'discovered_handler', 'request_method', 'regex_matches', 'result'));
    }

    private static function match($routes,$path_info){
        $discovered_handler = null;
        $regex_matches = array();
//        print_r($routes);
//        print_r($path_info);
        if (isset($routes[$path_info])) {
            $discovered_handler = $routes[$path_info];
        } elseif ($routes) {
            $tokens = array(
                ':string' => '([a-zA-Z]+)',
                ':number' => '([0-9]+)',
                ':alpha'  => '([a-zA-Z0-9-_]+)'
            );
            foreach ($routes as $pattern => $handler_name) {
                $pattern = strtr($pattern, $tokens);
                if (preg_match('#^/?' . $pattern . '/?$#', $path_info, $matches)) {
                    $discovered_handler = $handler_name;
                    $regex_matches = $matches;
                    break;
                }
            }
        }
        return array($discovered_handler,$regex_matches);
    }

}