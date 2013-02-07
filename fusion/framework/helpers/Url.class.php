<?php
class Url {
    private static $url;
    private static $args;
    private static $controller;
    private static $action;
    public function __construct(){
        if(is_null($this->url))
            self::$url = 'http://localhost:8080/';
    }
    
    public static function createUrl($url,$params=array()){
        if(is_array($url)){
            $url = explode('/',$url[0]);
            self::$controller = $url[0];
            self::$action = $url[1];
            
            self::$url = implode('/',array(self::$url,self::$controller,self::$action));
        }
        
        return count($params) > 0 ? implode('/', array(self::$url, self::joinParams($params))) : self::$url;
       // return implode('/',array(self::$url));
    }
    
    private static function joinParams($params=array()){
        $iParams = null;
        $contador = 0;
        foreach($params as $key=>$value){
            if($contador == 0){
                $iParams .= '?';
            
            if(count($params) > 1)
                $iParams .= $key.'='.$value.'';
            $contador++;
        }
        return $iParams;
    }
    
}
}

$url = Url::createUrl(array('site/index'), array('id'=>10, 'nome'=>'Diego'));
var_dump($url);
?>