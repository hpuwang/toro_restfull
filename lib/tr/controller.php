<?php
class tr_controller{
    public static $twig=null;
    private  static $_variable = array();

    static function getParam($str = null,$default=null)
    {
        return tr::getParam($str,$default);
    }

    function display($path=array(),$param=array()){
        if(!$path){
            $class = get_called_class();
            $method = get_called_method($class);
            if($class){
                $path = implode("/",explode("_",$class))."/".$method.".html";
            }
        }
        $param = array_merge(self::$_variable,$param);
        $this->tpl()->display($path, $param);
    }

    function render($path=array(),$param=array()){
        return $this->tpl()->render($path, $param);
    }

    function tpl(){
        if(self::$twig) return self::$twig;
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem(ROOT_PATH.'/view');
        $twig = new Twig_Environment($loader, array(
            'cache' =>ROOT_PATH. '/cache/templates_c',
            'auto_reload' => true,
            'autoescape'=>"html",
        ));
        self::$twig = $twig;
        return $twig;
    }

    function errorReturn($info=null){
        return tr_error::returnError($info);
    }

    function __set($key,$value){
        self::$_variable[$key]=$value;
    }

    function response($bodyData=null,$errorCode=null,$errorDescr=null){
        $this->cross_domain();
        @header("Content-type: application/json; charset=utf-8");
        $request = $this->getParam();
        $callback = isset($request['callback']) ? $request['callback'] : '';
        $runTime = getmicrotime()-tr_init::$elapsedTime;
        $data = array();
        $data['elapsedTime'] = number_format($runTime,4);
        $data['errorCode'] = $errorCode?$errorCode:0;
        $data['errorDesc'] = $errorDescr;
        $data['body'] =$bodyData? $bodyData : (object)null;
        $json = json_encode($data);
        $json = str_replace(':null', ':""', $json);
        if ($callback)
        {
            echo $callback . '(' . $json . ');';
        }
        else
        {
            echo $json;
        }
    }

     function cross_domain()
    {
        if (empty($_SERVER['HTTP_ORIGIN']))
        {
            return;
        }

        //允许的根域名CROSS正则
        $pattren = '/(feiniu|feiniugo)\.com$/i';
        if (preg_match($pattren, $_SERVER['HTTP_ORIGIN']) <= 0)
        {
            return;
        }

        $wrap_header['origin'] = 'Access-Control-Allow-Origin:'.$_SERVER['HTTP_ORIGIN'];
        $wrap_header['cred'] = 'Access-Control-Allow-Credentials:true';
        $wrap_header['allow_methods'] = 'Access-Control-Allow-Methods: POST, GET, OPTIONS';
        $wrap_header['allow_header'] = 'Access-Control-Allow-Headers: accept, origin, withcredentials, content-type,urlEncodeCharset, Accept-Charset, sid, th5_sid';

        if($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
        {
            $wrap_header['cache'] = "Access-Control-Max-Age:86400";
        }

        foreach ($wrap_header as $key => $header_line)
        {
            @header($header_line);
        }

        if($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
        {
            exit;
        }
    }
}