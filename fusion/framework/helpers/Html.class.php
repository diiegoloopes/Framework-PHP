<?php
/* Html
 * 
 * Auxilia a criação de elementos HTML.
 * 
 * @author Diego Lopes do Nascimento
 * @email diiego.lopes01@gmail.com
 * @version 0.1
 * @since 0.1
 */
class Html {
    /*
     * Armazenará a instância da class HTML para ser utilizada
     * dentro do pattern singleton.
     */
    private static $instance=null;
    /*
     * Armazenará o method do formulário.
     */
    public static $methodForm=null;
    /*
     * getRequest
     * 
     * @param string $name É o índice... {Complementar depois!}
     * @return string
     */
    private static function getRequest($name){
       if(self::$methodForm='post')
           return isset($_POST[$name])?$_POST[$name]:'';
       elseif(self::$methodForm='get')
           return isset($_GET[$name])?$_GET[$name]:'';
    }
    /*
     * setRequestMethod
     * 
     * @param string $method O tipo do método a ser enviado.
     * @return void
     */
    private static function setRequestMethod($method){
        self::$methodForm=$method;
    }
    
    /*
     * beginForm
     * 
     * @param mixed $url
     * @param method $method
     * @param array $attributes É o array que armazenará os atributos do elemento.
     * @return string
     */
    
    public static function beginForm($url='',$method='',$attributes=array()){
        self::setRequestMethod($method);
        $attributes['action']=$url;
        $attributes['method']=$method;
        return sprintf('<form %s>',self::arrayToAttr($attributes));
    }
    /*
     * endForm
     * 
     * Tag de fechamento do elemento FORM
     * 
     * @return string
     */
    public static function endForm(){
        return '</form>';
    }
    
    /*
     * inputText
     * 
     * Cria o elemento Input com o type text.   
     * 
     * @param string $name É o valor do atributo name.
     * @param string $value É o valor do atributo value.
     * @param array $attributes É o array que armazenará os atributos do elemento.
     */
    public static function inputText($name,$value='',$attributes=array()){
        $attributes['type']='text';
        $attributes['name']=$name;
        $attributes['value']=$value;
        return self::createSingleTagElement('input',$attributes);
    }
    /*
     * activeInputText
     * 
     * Usado em formulários com validação, pois quando o usuário realizar
     * alguma requisição, não perder o valor do campo que o usuário havia digitado,
     * caso não consiga ser bem sucedido.
     * 
     * @param string $name É o valor do atributo name.
     * @param array $attributes É o array que armazenará os atributos do elemento.
     */
    public static function activeInputText($name,$attributes=array()){
        return self::inputText($name,self::getRequest($name),$attributes);
    }
    /*
     * br
     * 
     * Cria o elemento BR
     * 
     * @param array $attributes É o array que armazenará os atributos do elemento.
     * @return string
     */
    public static function br($attributes=array()){
        return self::createSingleTagElement('br',$attributes);
    }
    /*
     * anchor
     * 
     * Cria o elemento A (anchor)
     * 
     * @param string $content É o conteúdo que será exibido entre as tags.
     * @oaram mixed $url 
     * @param array $attributes É o array que armazenará os atributos do elemento.
     * @return string
     */
    public static function anchor($content,$url='#',$attributes=array()){
        $attributes['href']=$url;
        return self::createElement('a',$content,$attributes);
    }
    /*
     * createElement
     * 
     * Cria elemento que possui duas tags.
     * <a href="#"></a>
     * 
     * @param string $name É o nome do elemento a ser criado.
     * @param string $content É o conteúdo que será exibido entre as tags.
     * @param array $attributes É o array que armazenará os atributos do elemento.
     * @return string
     */
    private static function createElement($name, $content=null, $attributes=array()){
        return '<'.$name.' '.self::arrayToAttr($attributes).'>'.$content.'</'.$name.'>';
    }
    /*
     * createSingleTagElement
     * 
     * Cria elemento que possui uma única tag.
     * <br />
     * 
     * @param string $name É o nome do elemento a ser criado.
     * @param string $content É o conteúdo que será exibido entre as tags.
     * @param array $attributes É o array que armazenará os atributos do elemento.
     * @return string
     */
    private static function createSingleTagElement($name, $attributes=array()){
        return sprintf('<%s %s />',$name, self::arrayToAttr($attributes));
    }
    /*
     * arrayToAttr
     * 
     * @param array $attributes É o array que armazenará os atributos do elemento.
     * return string 
     */
    private static function arrayToAttr($attributes=array()){
        $html=array();
        foreach($attributes as $attributes=>$value)
            $html[]=sprintf('%s = "%s"',trim($attributes),trim($value));
        return implode(' ',$html);
    }
    /*
     * getInstance
     * 
     * Método que utiliza o pattern Singleton, para que mantenha uma única instância
     * do objeto.
     * 
     * @return object da class HTML
     */
    public static function getInstance(){
        if(is_null(self::$instance))
            return new self;
    }
}

/*$html = Html::getInstance();
echo $html->anchor('Oi','http://www.google.ccom.br',array('id'=>10,'onclick'=>"return javascript void(0);"));
echo $html->br();
echo Html::anchor('Fechar[x]','http://google.com.br',array('id'=>10));*/
/*
<html>
    <head>
        
    </head>
    <body>
        <form action="" method="post">
            <label for="nome">Nome: </label> 
            <?php
            echo $html->beginForm('','post');
                 echo $html->activeInputText('nome',''); 
            echo $html->endForm();
            ?>
            <input type="submit" value="Enviar"/>
        </form>
    </body>
</html>*/