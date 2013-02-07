<?php
/* @class? Application
 * @author: Diego Lopes do Nascimento
 * @email? diiego.lopes01@gmail.com
 * @date: 10-12-12
 * @version: 0.1
 * @package: framework.system
 * @since: 0.1
 * 
 * @note É a class mais importante do framework, pois ela controlará todas as chamadas
 * e funcionamento entre as demais classes.
 * 
 */
class Application {
    public static $folder = 'framework';
    private static $params=array();
    public $instances = array();
    
    public function run(){
        // Carrega todas as classes.
        $this->autoload();
        // Inicializa o controller.
        $this->controller->run();
    }
    
    public static function set($index,$value,$overwrite=false){
        if(array_key_exists($index,self::$params)){
            if(!empty($value)){
                if(is_bool($overwrite)){
                    if($overwrite==false){
                        $oldvalue = self::$params[$index];
                        $value = implode('',array($oldvalue,$value));
                    }
                    //$value = ($overwrite==true ? $value : $value . $value);
                    self::$params[$index]=$value;
                }
            }
        } else {
            self::$params[$index] = $value;
        }
    }
    
    public function setParams($params){
        if(is_array($params))
            self::$params=$params;
        return $this;
    }
    
    public static function get($index){
        if(isset(self::$params[$index]))
            return self::$params[$index];
    }
    /*
     * autoload
     * 
     * Método que importará todas as classes necessárias para o funcionamento
     * do Framework 
     * 
     * @access private
     * @return void
     * 
     * Observação: Ainda em teste! Pois estou pensando em uma maneira de melhorar esse método.
     */
    private function autoload(){
        
        $classes = array('controller' => '/system/Controller.class.php',
                         'view' => '/system/View.class.php');
        
        foreach($classes as $class => $namespace){
            include_once(self::getRoot().$namespace);
            $this->$class = new $class();
        }   
    }
    
    public function setRoot($path){
        self::set('root', $path);
        return $this;
    }
    public static function getRoot(){
        return self::get('root');
    }
    /*
     * import
     * 
     * @param string $namespace É passado o namespace, caminho, da localização do arquivo.
     * @param string $extension É a extensão dos arquivos que serão inseridos. .php, .class.php, .tlp
     * @param bool $return Flag que identifica se o arquivo importado será retornado ou não.
     * @since 0.1
     * @return void;
     * 
     * Observação: Foi implementada o carregamento automatico do diretório. Ainda encontra-se em versão de testes.
     * 
     * Exemplo: application.controllers.*
     */
    public static function import($namespace,$extension='.class.php',$return = false){
        
        $readAll = false;
        $path = null;
        if(stripos($namespace,'*'))
            $readAll = true;
        
        
        if($readAll == false)
            $path = self::getPathFromAlias($namespace,$extension);
         elseif($readAll == true) {
            $namespace = str_replace(array('*','.'),array('',DS),$namespace);
            $dir = opendir($namespace);
            
            while(false !== ($file = readdir($dir))){
                if($file != '.' && $file != '..')
                    $path = $namespace.$file;
            }
        }
        
        if($path == '')
            echo 'Class não carregada, pois o caminho está incorreto.';
        else {
            if($return)
                return include_once($path);
            else
                include_once($path);
        }
    }
    
    /*
     * getPathFromAlias
     *
     * @param string $namespace É passado o namespace da localização do arquivo.
     * @param string $extension É a extensão dos arquivos que serão inseridos. .php, .class.php, .tlp
     * @return mixed $fullPath Retorna o caminho completo , se verdadeiro.;
     * 
     */
    protected static function getPathFromAlias($path,$extension = null){
        
        $path = self::get('basePath').'/'.str_replace('.','/',$path);  
        $fullPath = is_null($extension)?$path:$path.$extension;
        
        if(file_exists($fullPath))
            return $fullPath;
        else{
            return null;
        }
    }
    /*
     * Write
     * 
     * 
     * @param string $var é o nome da constant a ser declarada.
     * @param string $value é o valor que a constant terá.
     * @return void;
     */
    public static function write($var,$value){
        if(!defined($var))
            define($var,$value);
    } 
    
    /*
     * @param mixed $name
     */
    public static function helper($name){
        $path = self::getRoot().'/helpers/';
        
        if(is_array($name)){
            for($i = 0; $i <= count($name)-1; $i++)
                include_once($path.self::getHelper($name[$i]));
            
        } else
            include_once($path.self::getHelper($name));
    }
    /*
     * @return path.to.helper
     */
    private static function getHelper($index){
        $helpers = array('Html' => 'Html.class.php');
        
        foreach($helpers as $class => $file){
            if($class == $index)
                return $file;
        }
    }
}


/*
 * Funciona como um atalho para acessar a class Application e seus respectivos
 * métodos.
 */
class App extends Application {}

?>
