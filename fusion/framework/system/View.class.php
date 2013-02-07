<?php
/*
 * @class:View
 *
 * Class que contém os métodos necessários para carregar view de modo seguro com
 * o uso ou não de algum template, fazendo com que haja menos código e melhor performace.
 *
 * @author: Diego L. do Nascimento
 * @email: diiego.lopes01@gmail.com
 * @copyright: Diego L. do Nascimento
 * @version: 0.1.1
 * @since: 0.1
 * @date: 10-12-2012
 */
class View {
    /*
     * @var $template
     *
     * Armazena o caminho do arquivo do template que poderá ser usado pela class.
     */
    public $template = 'main';
    /*
     * @var dir
     *
     * Armazena o diretório dos arquivos da view e do template.
     */
    public $dir = 'application/views/';
    /*
     * @var hasTemplate
     *
     * Handler que verifica se a class usará ou não template, para evitar carregamentos
     * desnecessários.
     */
    private $hasTemplate = true;
    private $content;
   
    /*
     * Carrega view
     *
     * @param string $filename é o nome do arquivo que será carregado para a view
     * @param array $data é o conjunto de variáveis que a view terá ao ser carregada
     * @return void
     */
    public function load($filename, $data = null){
        // Verifica se o usuário quis chamar uma view com .php ou sem
        $filename = $this->hasDot($filename);
       /* // Se o usuário esquecer de colocar ou o arquivo não existir na chamada
        include($this->dir.$filename);*/
        if($this->parser($filename, $data)){
            if(is_array($data) || !is_null($data)){
                // Extrai os valores passados através do controller como array para as view
                extract($data, EXTR_OVERWRITE);
            }
            // Inicia a saída de buffering
            ob_start();
            include($this->dir . $filename);
            // Armazena no índice content todo o conteúdo obtido através do include
            $this->content = ob_get_contents();
            // Fecha o buffering
            ob_end_clean();
            if(is_array($data) || !is_null($data)){
                // Extrai os valores passados através do controller como array para as view
                extract($data, EXTR_OVERWRITE);
            }
        }
        $hasTemplate = !empty($this->template) ? true : false;
        // Caso haja algum template, mas se não houver template ele carrega a própria view
        if(is_bool($hasTemplate) && $hasTemplate == true && $this->hasTemplate==true){
            ob_start();
                include($this->dir . 'layout/'.$this->hasDot($this->template));
                $template = $this->replaceKeys(ob_get_contents());
            ob_end_clean();
           
            echo $template;
            $this->template = null;
        } else {
            // Exibe o conteúdo carregado no include
            echo $this->getContent();
        }
    }
    private function getContent(){
        return $this->content;
    }
    private function replaceKeys($content){
        $keys=array( '{title}'=>Application::get('pageTitle'),
                     '{content}'=>$this->getContent());
        return str_replace(array_keys($keys), array_values($keys), $content);
    }
    /*
     * Carrega parte da view
     *
     * @param string $filename é o nome do arquivo que será carregado para a view
     * @param array $data é o conjunto de variáveis que a view terá ao ser carregada
     * @return void
     *
     * Note: Esse método somente deverá ser utilizado pela View e não pelo controller
     */
    public function loadPartial($filename, $data = null){
        // Verifica se o usuário quis chamar uma view com .php ou sem
        $filename = $this->hasDot($filename);
        if(is_object($this) && is_a($this, __CLASS__)){
           if($this->parser($filename)){
                if(is_array($data) || !is_null($data)){
                    // Extrai os valores passados através da view como array para as view
                    extract($data, EXTR_OVERWRITE);
                }
                // Carrega o arquivo .php
                include($this->dir . $filename);
            }
        }  
    }
    /*
     * Analisa o arquivo através do nome, verificando se o usuário preencheu o nome
     * do arquivo e também a existência do arquivo dentro do diretório especificado pela
     * variável $dir da class
     *
     * @param string $filename é o nome do arquivo que será carregado para a view
     * @return void
     * @access private
     */
    private function parser($filename){
        if(!empty($filename)){
            
            $path = Application::get('basePath').'/'.$this->dir . $filename;
           
            if(file_exists($path)){
                return true;
            } else {
                throw new Exception('Mensagem: Arquivo não existe no local <b>'.$this->dir . $filename.'</b>');
            }
        } else {
            throw new Exception('A variável se encontra vazia.');
        }
    }
    /*
     * Analisa o nome do arquivo passado , verificando se o usuário passou nomeDaView.php ou
     * somente nomeDaView, tratando a string para o formato adequado do arquivo.
     *
     * @param string $filename é o nome do arquivo que será carregado para a view
     * @return string
     * @access private
     */
    private function hasDot($filename){
        $filename = substr($filename, -4) == '.php' ? $filename : $filename . '.php';
        return $filename;
    }

}
?>
