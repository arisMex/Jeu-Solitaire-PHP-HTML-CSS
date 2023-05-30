<?php
    include('TablierSolitaire.php');
    class TablierSolitaireUI
    {
        private $ts;
        //construvteur
        public function __construct($ts = null){
            $this->ts = $ts; //initialiser aves $ts (Tablier Solitaire) avec null sinon (val par def)
        }
        
        public function getFormulaireOrigine(){
            //un formulaire qui envoi les coordonnées d'origine vers la page action.php
            $s='
                <form action="action.php" method="post">
                    <label for="x" name="xDep" >Coord X:</label><br>
                    <input type="number" id="x" name="xDep" required><br>
                    <label for="y">Coord Y</label><br>
                    <input type="number" id="yDep" name="yDep" required><br><br>
                    <input type="submit" value="Submit">
                </form>';
            return $s;
        }

        public function getFormulaireDestination($coord_depart){
            //un formulaire qui envoi les coordonnées d'origine et de destination vers la page action.php

            $xDep = intval(substr($coord_depart, -3, 1),  10);
            $yDep = intval(substr($coord_depart, -1, 1),  10);
            $s='
                <form action="action.php" method="post">
                    <input type="hidden" id="xdep" name="coord_depart" value='.$coord_depart.' ><br>
                    <label for="x">Coord X Destination:</label><br>
                    <input type="number" id="x" name="xDest" required><br>
                    <label for="y">Coord Y Destination</label><br>
                    <input type="number" id="y" name="yDest" required><br><br>
                </form>'
            ;
            return $s;
        }

        public function getPlateauFinal(){
            if (isset($_GET["xDep"]) && isset($_GET["yDep"])   ) {
                $dest = "action.php?xDep=".$_GET["xDep"]."&yDep=".$_GET["yDep"];
            }else{
                $dest = "action.php";
            }//function
            $s='<form action="'.$dest.'" method="post">';

            for ($i=0; $i < $this->ts->getNbLignes() ; $i++) { 
                for ($j=0; $j < $this->ts->getNbColonnes() ; $j++) {               
                    switch ((int)$this->ts->getCase($i, $j)->getValeur($i, $j)) {
                        case -1:
                            $class = "neutralise";
                            break;
                        case 0:
                            $class = "vide";
                            break;
                        case 1:
                            $class = "bille";
                        break;                                                
                    }
                    if (isset($_GET["xDep"]) && isset($_GET["yDep"])) { // si une bille est dèja selectionnée
                        //on désactive la case si :
                        $disabel = ($this->ts->getCase($i, $j)->isCaseNeutralise()     // case Neutralisée
                         ||($this->ts->getCase($i, $j)->isCaseBille() && ($i != $_GET["xDep"] || $j != $_GET["yDep"]) || //Bille jouable non selectionnée
                                ($this->ts->getCase($i, $j)->isCaseVide() &&!$this->ts->estValideMvt($_GET["xDep"],$_GET["yDep"], $i, $j)) )); //case vide oû la bille selectionnée ne peut pas y accéder
                    }else{//sinon
                        //on désactive la case si :
                        $disabel = $this->ts->getCase($i, $j)->isCaseNeutralise() || //case neutralisée
                         !$this->ts->isBilleJouable($i, $j)  ; // Bille non jouable
                    }
                    $s .= $this->getBoutonCaseSolitaire($class, $i, $j, $disabel );
                    
                }
                $s .= "<br>";
            }
            $s .= "</form>";
            return $s;

        }

        //Nouveau
        public function setPlateau(){
            
            $s='<form action="action.php" method="post">';
            $s .= '<input type="hidden"  name="next" value="'.($_GET["config"]).'">';

            if ($_GET["config"] == 0) { //Etape 0
                $s .= '<label for="lignes">Nombre de lignes     :</label>
                <input type="number" id="lignes" name="lignes" min="3" max="8" value="7" required></br>'; //nb Lignes min 3 max 8
                $s .= '<label for="colonnes">Nombre de colonnes :</label>
                <input type="number" id="colonnes" name="colonnes" min="3" max="8" value="7" required></br>';   //nb colonnes min 3 max 8             
            }else{
                //affichage des cases
                for ($i=0; $i < $this->ts->getNbLignes() && $_GET["config"] != 0; $i++) { 
                    for ($j=0; $j < $this->ts->getNbColonnes() ; $j++) {               
                        switch ((int)$this->ts->getCase($i, $j)->getValeur($i, $j)) {
                            case -1:
                                $class = "neutralise";
                            break;
                            case 0:
                                 $class = "vide";
                            break;
                            case 1:
                                $class = "bille";
                            break;                                                
                        }
                    
                        $s .= $this->getBoutonCaseSolitaire($class, $i, $j, false );
                    
                    }
                    $s .= "<br>";
                }
            }
            /********Boutton Next  *******/
            $s .= '</br><button name="next" id="next" value="'.($_GET["config"]+1).'">Next</button></form>';//qui envoie le num de l'etape suivante par post
            return $s;

        }

        private static function getBoutonCaseSolitaire($classe, $ligne, $colonne, $disabled){
            
            $s =  "<button class='$classe' name='coord' value='".$ligne."_"."$colonne'";
            if($disabled){
                $s .=  "disabled";
            }
            $s .= ">&nbsp;</button>";
            return $s;
        }

    }

    
?>