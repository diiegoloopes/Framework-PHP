<?php

return array(
        // Configuração do Framework
        'basePath' => realpath(dirname(__FILE__).'/../..'), 
        'pageTitle'=>'Título da Página',


        'components' => array(
            'db'=>array(
                // Charset default é utf-8
                'charset'=>'utf-8',
                //'hashSalt'=>'aSjWNm918D19', 
                'connectionString'=>'mysql:host=localhost;dbname=test',
                'username'=>'root',
                'password'=>'',

            ),
        ),
);
