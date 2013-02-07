<?php
/*
 * @since 1.0
 * 
 * Serão armazenadas todas as constantes que serão necessárias
 * para o funcionamento do framework, como por exemplo: caminho base da aplicação, 
 * caminho base da view, models e etc.
 */
Application::write('APPLICATION',BASEPATH.DS.'application');
Application::write('VIEW',APPLICATION.DS.'views');

?>
