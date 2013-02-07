<?php
class MainController extends Controller{
    /*
     * Esse exemplo de renderização de layout só é válido
     * caso o usuário queira carregar um layout sem que o mesmo
     * esteja na pasta layout
     */
    //public $layout = '..//layout/main';
    /*
     * Esse exemplo de renderização de layout só é válido quando
     * se é necessário carregar um template que esteja dentro da
     * pasta layout.
     */
    public $helpers = array('Html');
    
    public $layout = 'main';
    
    public function index(){
        
        echo 'Oi';
      //  echo Application::getParam('pageTitle');
        /*Precisa terminar depois!*/
        //$http = new HttpRequest;
        //echo $http->getBaseUrl();
       /* App::set('pageTitle', ' > Início');
        $this->set('pageTitle', '> Fim');*/
        //$this->set('pageTitle',' > Início');
        
        $this->render('index',array('model'=>'Aqui é a model'));

    }
    
    public function download(){
        $this->set('pageTitle',' > Download');
        $this->render('download');
    }
    
    public function contato(){
        
        $this->render('contato',array('model'=>'Aqui será passado uma model.'));
    } 
    
    
    
    /*
    public function contato($id){
        $this->render('main/contato');
    }*/
}
?>
