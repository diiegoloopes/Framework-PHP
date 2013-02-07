<?php

ini_set('display_errors', 1);


$root = include(dirname(__FILE__).'/../fusion/framework/index.php');
$settings = dirname(__FILE__).'/application/settings/settings.php';

/*if(!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);


if(!defined('BASEPATH'))
    define('BASEPATH',  dirname(__FILE__));


if(!defined('DIR'))
    define('DIR', basename(dirname(__FILE__)));

if(!defined('BASEURL'))
    define('BASEURL', 'http://'. $_SERVER['SERVER_NAME'].'/'.DIR.'/'); */


/*
 * Importa a variável $params que contém alguns parâmetros de configuração
 * do framework, tais como: Título padrão da página, configuração de Banco de Dados e entre outros.
 */

$settings = include_once($settings);

/*
 * Importa a class Application
 */

require_once($root.'/system/Application.class.php');

/*
 * Instancia o método que inicializa a aplicação.
 */

$app = new App();


/*
 * Passa toda a configuração para o core da class.
 */

$app->setParams($settings)->setRoot($root)->run();

?>
