<html>
    <head>
        <meta charset="utf-8"/>
        <title>{title}</title>
        <link rel="stylesheet" href="../assets/css/reset.css" type="text/css" media="all">
        <style type="text/css">
            @font-face {
           /* font-family: ‘NomeDaFont’;*/
            src: url(‘../assets/fonts/YanoneKaffeessatz-Bold.otf’);
            font-weight: normal;
            font-style: normal;
            }
            body {
                font-size: 13px;
                color: rgb(47,47,47);
                font-family: "Yanone Kaffeesatz-Regular",Arial;
                background: url('../assets/images/body.png') repeat-x rgb(238,238,238);
            }
            p {
                margin: 5px 0;
            }
            /* --- Cabecalho --- */
            div#pagina {width: 960px; margin: 0 auto; }
            div#pagina div#cabecalho {height: 205px;}
            div#cabecalho div#logo {height:125px}
            div#cabecalho ul#menu {overflow: auto; float: left; margin-top: 23px; }
            div#cabecalho ul#menu li{float: left; list-style: none}
            div#cabecalho ul#menu li a {display: block; border-left: 1px solid rgb(221,221,221); border-right: 1px solid rgb(255,255,255); font-size: 13px; padding: 15px 15px; text-align:center;  color: rgb(47,47,47); text-decoration:none;}
            div#cabecalho ul#menu li a:hover {background-color: rgb(247,247,247)}
            
            /* --- Conteudo --- */
            
            div#conteudo {float: left; min-height: 400px; width: 640px; padding: 20px 15px;}
            div#conteudo h1 {font-size: 22px;}
            
            /* --- Lateral --- */
            
            div#lateral {float: right; width: 270px;}
            div#lateral div.widget {padding: 15px 10px; min-height: 100px}
            div#lateral div.widget h2 {font-size: 22px; } 
            
            /* Rodapé */
            
            div#rodape {clear: both}
            
            /**/
            
            .shadow-box {
                border-radius: 0.4em;
                box-shadow: 5px 5px 5px rgb(205, 205, 205);
                background-color: #fff; 
            }
        </style>
    </head>
    <body>
        <div id="pagina">
            <div id="cabecalho">
                <div id="logo">Fusion Framework - O melhor framework PHP</div>
                <ul id="menu">
                    <li><?php echo Html::anchor('Início', '/') ?></li>
                    <li><?php echo Html::anchor('Downloads', 'index.php?a=download') ?></li>
                    <li><?php echo Html::anchor('Documentação') ?></li>
                    <li><?php echo Html::anchor('Tutoriais') ?></li>
                    <li><strike><?php echo Html::anchor('Comunidade') ?></strike></li>
                </ul>
            </div><!-- cabecalho -->
            <div id="conteudo" class="shadow-box">
                {content}
               <!-- <h1>Por quê usar o Fusion Framework?</h1>
                <p>
                    Conteudo.
                </p>-->
                <!--{content}-->
            </div>
            <div id="lateral">
                <div class="widget shadow-box">
                    <h2>Like us!</h2>
                    <p>Box do facebook aqui!</p>
                </div>
            </div>
            <div id="rodape">
                
            </div>
        </div>
    </body>
</html>