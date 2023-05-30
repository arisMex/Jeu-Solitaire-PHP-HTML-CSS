<?php
    session_start(); // start un session
    echo '<!DOCTYPE html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title> TablierSolitaire </title>
        <link rel="stylesheet" href="CSS/styles.css" />
    </head>
    
    <body>
    
        <h1>Tablier Solitaire</h1> 
        ';
        include('TablierSolitaireUI.php'); 
    
        configurationTableau(); //une fonction qui permet à l'utilisateur de configurer son Tablier
        
        if (!isset( $_SESSION["tab"] ) && !isset($_GET["config"])  ) { // verifier s'il existe un tablier solitaire dans la session (si une session existe)
            //****  Vous pouvez choisir un tablier   */
            afficherTabliers();
           
            if (isset( $_POST["table"] )) {
                $ts = selectTablier($_POST["table"]);
                $_SESSION["tab"] = serialize($ts);   //on le stoque dans la session
                header("Refresh:0");
            }

        }elseif (!isset($_GET["config"])) {
            

            $ts = unserialize($_SESSION["tab"]); // on récupère le tablier stoqué dans la session 
            $_SESSION["tab"] = serialize($ts);    

            restartGame(); // boutton restart

            $t = new TablierSolitaireUI($ts); //Créer le jeu à partir du tablier
            echo $t->getPlateauFinal(); // affichage du plateau 
            if ($ts->isFinPartie()) { //si la partie est finie 
                session_destroy();  //fermer la session
                finPartie($ts); //affichages fin de partie
            }
        }

    echo "</br></body>";


    function finPartie($ts){
        if ($ts->isFinPartie()) { //si la partie est finie 
            
            echo ('<div id="fin"> <p> Game Over ! </p><br>');
            if ($ts->isVictoire()) {// si gagné
                echo '<p id="gagn">Vous avez gagné !🏆</p><br>';
            }else{ //sinon
                echo'<p id="perd">Vous avez perdu !😓<p>';
            }
            echo"</div>";
        }
    }
    function restartGame(){
        echo '<form action="action.php" method="post">
        <input type="submit" value="Restart Game" 
        name="restart" id="restart" />
        </form>';
    }

    function afficherTabliers(){
        echo '<form id="choixTab" action="action.php" method="post" >
        <input type="submit" class="choix" value="Tablier Europeen" name="table"/>
        <input type="submit" class="choix" value="Tablier Anglais" name="table" />
        <input type="submit" class="choix" value="Tablier Gagnant" name="table" />
        <input type="submit" class="choix" value="Tablier Perdant" name="table" />
        </form></br>
        
        <form id="tabDef" method="get">
        <button name="config" value="0" class="choix" id="config">Créer Tablier</button>
        </form>';
    }

    function configurationTableau(){
        if (isset($_GET["config"])) {
                
            if ($_GET["config"]==0) {//debut de la configuration
                echo (new TablierSolitaireUI())->setPlateau(); //afin de pouvoir initialiser un tablier dans la session 

            }else {
                //un petit mot
                if ($_GET["config"]==1) {
                    echo '<p class="pConf"> Neutraliser les Cases</p>';
                }elseif ($_GET["config"]==2) {
                    echo '<p class="pConf"> Vider les Cases </br>(au MOINS une Case)</p>';
                }

                //récupérer le tablier et l'afficher afin de pouvoir le modifier encore;
                $ts = unserialize($_SESSION["tab"]);
                $t = new TablierSolitaireUI($ts);
                echo $t->setPlateau(); //paramétrer le tablier au fur et à mesure
            }

        }
    }

?>