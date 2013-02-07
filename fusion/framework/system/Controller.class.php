<?php   
/*
 * @class: Controller
 * 
 * Esta class manipula o acesso dos controllers e actions através
 * da URL.
 * 
 * @author: Diego Lopes do Nascimento
 * @since: 0.1
 * @package: framework.system
 * @date: 10-12-2012
 * 
 */

class Controller extends Application {
    /*
     * Define o controller padrão a ser chamado, caso não seja passado nenhum
     * controller.
     * @access public
     */
    private $defaultController = 'MainController';
    /*
     * Define o action padrão a ser chamado, caso não seja passado nenhum
     * action.
     * @access public
     */
    private $defaultAction = 'index';
    /*
     * Armazena os parâmetros.
     * @access public
     */
    private $params = array();
    /*
     * Armazena o controller atual.
     */
    protected $controller = '';
    /*
     * Armazena a action atual.
     */
    protected $action = '';
    /*
     * Armazena o objeto instanciado.
     */
    private $obj;
    
    public $helpers = null;
    
    public function __construct(){
        if(isset($_GET['c']))
            $this->controller = ucfirst($_GET['c']).'Controller';
        
        if(isset($_GET['a']))
            $this->action = $_GET['a'];
        
        if(!is_null($this->helpers))
            Application::helper($this->helpers);
    }
    
    /*
     * getController
     * 
     * @return
     */
    public function getController(){
        if($this->controller==null)
            return $this->defaultController;
        else
            return $this->controller;
    }
    /*
     * getAction
     * 
     * @return
     */
    public function getAction(){
        if($this->action=='')
            return $this->defaultAction;
        else
            return $this->action;
    }
    /*
     * getObject
     * 
     * @return
     */
    public function getObject(){
        return $this->obj;
    }
    /*
     * getParams
     * 
     * Método responsável por organizar e ordenar os parâmetros que
     * são passados via URL.
     * 
     * @access private
     * @return array Array com os parâmetros array('id'=>10) é equivalente a ?p=id/10
     */
    private function getParams(){
        $this->params=explode('/',$_GET['p']);
        //return $this->params;
        $params = array();
        $auxiliar='';
        $contador=0;
        foreach($this->params as $param){
            if($contador%2==0)
                $auxiliar=$param;
            
            if($contador%2==1)
                $params[$auxiliar]=$param;
            
            $contador++;
        }
        return $params;
    }
    /*
     * hasParams
     * 
     * Verifica se está sendo passado algum parâmetro pela URL.
     * 
     * @return bool
     */
    private function hasParams(){
        if(isset($_GET['p']))
            return true;
    }
    
    public function loadController($controller){
        $namespace='application.controllers.'.$controller;
        $path = Application::getPathFromAlias($namespace,'.php');
    
        if($path!=false){
            Application::import($namespace,'.php');
            return new $controller();
        }
    }
    
    public function run(){
        $this->controller = $this->getController();
        $this->action = $this->getAction();
        
        $this->obj=$this->loadController($this->controller);
       
        if(!$this->hasParams())
            $this->runNoParams();
        else
            $this->runWithParams();
    }
    private function runNoParams(){
        $obj = $this->getObject();
        $action = $this->getAction();
        $obj->$action();
    }
    private function runWithParams(){
        $object = $this->getObject();
        
        $class = new ReflectionClass($this->getController());
        $method = $class->getMethod($this->getAction());
        
        $internalParams = $method->getParameters();
        $externalParams = $this->getParams();
        
        if($this->compareParams($internalParams,$externalParams))
            call_user_func_array(array(new $object, $this->getAction()), $this->getParams());
        else
            $this->errorHandler();
    }
    
    private function compareParams($internalParams,$externalParams){
        $i=0;
        $qtdInternal = count($internalParams);
        $qtdExternal = count($externalParams);
        if($qtdInternal != $qtdExternal)
            return false;
        
        foreach($externalParams as $key=>$value){
            if($key!=$internalParams[$i]->name || empty($value))
                return false;           
            $i++;
        }
        return true;
    }
    /*
     * render
     * 
     * @param string $path É o caminho do arquivo que deverá ser incluso.
     * @param array $data São os dados que serão passados para a view.
     * @param bool $return Flag que delimita se o layout vai ser renderizado ou passado 
     * como string.
     * @return void
     */
    public function render($path,$data=array(),$return=false){
        $view = new View();
        $prepath = explode('/',$path);
        $controller = str_replace('Controller','',$this->getController());
        $path = count($prepath) > 1 ? $path : strtolower($controller).'/'.$path;
        @$view->template = !is_null($this->layout)?$this->layout:$view->template;
        
        $view->load($path ,$data);
        
        //$this->view->load($path,$data);
    }
    public function errorHandler(){
        echo 'Error ):';
    }
    
    
}
?>
