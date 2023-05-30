<?php
    session_start(); // start un session
    include('TablierSolitaireUI.php');

    //Restart Game
    if (isset($_POST["restart"])) {
       restartGame();
    }

    //selectionner un tablier
    if (isset($_POST["table"])) {
        $ts = selectTablier($_POST["table"]);
        $_SESSION["tab"] = serialize($ts);   //on le stock dans la session
        (new TablierSolitaireUI())->getFormulaireOrigine(1,2);
        header("Location: index.php");
    }

    //Selectionner l'origine
    if (isset($_POST["coord"]) && !billeSelected()) { // si on des coordonnées passées par POST et aucune bille n'st selectionnée
        
        $coord = $_POST["coord"];

        selectBille($coord);   

        //récupérer les coordonnées
        $xDep = intval(substr($_POST["coord"], -3, 1),  10);  // x_y rexupérer x
        $yDep = intval(substr($_POST["coord"], -1, 1),  10);  // x_y rexupérer y

        //redirection vers la page proncipale avec les coordonnées de la bille à deplacer        
        header("Location: index.php?xDep=$xDep&yDep=$yDep");           
    }
    

    //La destination
    if (isset($_POST["coord"]) && billeSelected()) {
        
        /* On deux cas : 
            * le cas ou la destination est la même que le depart
            * le cas ou la destination est diffèrente de l'origine
        */

    
        //récupérer les coordonnées du depart et destination
        $xDest = coordX($_POST["coord"]);  // x_y rexupérer x
        $yDest = coordY($_POST["coord"]);  // x_y rexupérer y
        $xDep =  $_GET["xDep"];
        $yDep = $_GET["yDep"];

        // on récupère le tablier stocké dans la session 
        $ts = unserialize($_SESSION["tab"]);
         

        // le cas ou la destination est la même que le depart alors on déselectionne en revenant à index
        if ($xDep == $xDest && $yDep == $yDest) {        
           header('Location: index.php');//on déselectionne(redirection à la page principale)
        }else { // si la destination est différente de l'origine
           // on effectue le mouvement 
            bougerBille($xDep, $yDep, $xDest, $yDest);
            $_SESSION["tab"] =serialize($ts);  //om met à jour le tablier dans la session
            header("Location: index.php"); //redirection*

        }
    } 

    //Configuration Tablier
    if (isset($_POST["next"]) ) {
        if (isset($_SESSION["tab"]) && isset($_POST["coord"])) { //une case a été selectionnée

            //récupérer la tablier
            $ts = unserialize($_SESSION["tab"]);

            //récupérer les coordonnées de la case
            $x = coordX($_POST["coord"]);
            $y = coordY($_POST["coord"]);

            //si on est dans la première etape de configuration (Neutralisation)
            if ($_POST["next"] == 1) {
                //Si la case est une case bille on change
                if ($ts->getCase($x, $y)->isCaseBille()) {//ça permet de neutraliser et remplir une case
                    $ts->neutraliseCase($x, $y);
                }else { 
                    $ts->remplitCase($x, $y);
                }
            }

            //si on est dans la deuxième etape de configuration (Vider les cases)
            if ($_POST["next"] == 2) {
                //Si la case est une case bille on change
                if ($ts->getCase($x, $y)->isCaseBille()) {//ça permet de vider et remplir une case
                    $ts->videCase($x, $y);
                }elseif ($ts->getCase($x, $y)->isCaseVide()) { 
                    $ts->remplitCase($x, $y);
                }
            }
            $_SESSION["tab"] = serialize($ts);
            header("Location: index.php?config=".$_POST["next"]); //passer à létape suivante

        }elseif (isset($_SESSION["tab"]) && !isset($_POST["coord"])) {
            if ($_POST["next"] >=3) { //etape finale
                $ts = unserialize($_SESSION["tab"]);

                //verifier que le jeu n'est pas bloqué (au moins un mouvement possible)
                if ($ts->isFinPartie()) {
                    //sinon revenir à l'étape 2 pour vider au moins une case
                    header("Location: index.php?config=2");
                }else {
                    header("Location: index.php");
                }
                              
            }else {
                header("Location: index.php?config=".$_POST["next"]);              
            }
            
        }else {// si mon tablier est vide
            //Etape 0 :
            //je crée un tablier avec le nombre de lignes et de colonnes envoyés par POST 
            $ts = TablierSolitaire::initTablierDefaut($_POST["lignes"], $_POST["colonnes"]);
            $_SESSION["tab"] = serialize($ts);
            header("Location: index.php?config=1"); //on commence la configuration par l'etape 1
        }
    }
    




/*       **      ** Functions  **        **        *    */


function selectBille($coord){
    $xDep = intval(substr($_POST["coord"], -3, 1),  10); // x_y rexupérer x
    $yDep = intval(substr($_POST["coord"], -1, 1),  10);  // x_y rexupérer y  
    //getFormulaireOrigine($xDep."_".$xDep);            
}

function restartGame(){
    //réouvrir une nouvelle session pour un nouveau jeu
    session_destroy();  
    session_start();
    header('Location: index.php');
}

function selectTablier($tablier){
    switch ($tablier) {
        
        case 'Tablier Europeen':
            $ts = TablierSolitaire::initTablierEuropeen();
            break;
        case 'Tablier Anglais':
            $ts = TablierSolitaire::initTablierAnglais();
            break;
        case 'Tablier Perdant':
            $ts = TablierSolitaire::initTablierPerdant();
            break;
        case 'Tablier Gagnant':
            $ts = TablierSolitaire::initTablierGagnant();

        break;
        /*case 'Tablier Par Defaut':
            $ts = TablierSolitaire::initTablierDefaut();
        break;*/
    }
    return $ts;
}

function bougerBille($xDep, $yDep, $xDest, $yDest){
    global $ts;
    $ts->deplaceBille($xDep, $yDep, $xDest, $yDest); // on effectue le mouvement 
}

function billeSelected(){
    return (isset($_GET["xDep"]) && isset($_GET["yDep"]));
}

function coordX($coor){
    return intval(substr($coor, -3, 1),  10);  // x_y rexupérer x
}
function coordY($coor){
    return  intval(substr($coor, -1, 1),  10);  // x_y rexupérer y
}

?>