<?php

include('CaseSolitaire.php');

class TablierSolitaire
{
    const NORD = 0;
    const EST = 1;
    const SUD = 2;
    const WEST = 3;

    private $nbLignes;
    private  $nbColonnes;
    protected  $tablier = array();

    private function __construct($nbL = 5, $nbC = 5){
        $this->nbLignes = $nbL;
        $this->nbColonnes  =$nbC;
        for ($i=0; $i< $this->nbLignes; $i++)
        {
            array_push($this->tablier,array());
            for ($j=0; $j<$this->nbColonnes; $j++)
            {
                array_push($this->tablier[$i],array());
                $this->tablier[$i][$j]=new CaseSolitaire();

            }
        }

    }

    public function getNbLignes(){
        return $this->nbLignes;
    }
    public function getNbColonnes(){
        return $this->nbColonnes;
    }
    public function getTablier(){
        return $this->tablier;
    }
    public function getCase($ligne, $colonne){
            //verifier si les coordonnées sont good
        if (!($ligne < $this->nbLignes && $ligne>= 0 && $colonne < $this->nbColonnes && $colonne>= 0)) {
            throw new Exception('Out of bounds !');
        }
        return $this->tablier[$ligne][$colonne];
    } //en verifiant la validité des coordonnées à getCase() elle verifiera dans la suite des fonctions;



    public function videCase($ligne, $colonne){

        $this->getCase($ligne, $colonne)->setValeur(0);
    }
    public function remplitCase($ligne, $colonne){
        $this->getCase($ligne, $colonne)->setValeur(1);
    }
    public function neutraliseCase($ligne, $colonne){
        $this->getCase($ligne, $colonne)->setValeur(-1);
        //($this->tablier[$ligne][$colonne])->setValeur(-1);
    }


    public function estValideMvt($ld, $cd, $la, $ca){
        /* il faut que : 
         * la case de depart et d'arrivée soient dans le Tablier (1).
         * la destination soit vide;         (2)
         * la case d'arrivée soit dans un rayon de 2 cases en horizontal ou en vertical seulement. (pas en diagonal) (3)
         * il faut qu'il yest une bille entre la case de depart et d'arrivée (4)
        */
        if($ld < 0 || $ld >= $this->getNbLignes() || $cd < 0 || $cd >= $this->getNbColonnes() ||
            $la < 0 || $la >= $this->getNbLignes() || $ca < 0 || $ca >= $this->getNbColonnes() ) return false; //(1)
        $bool = ($this->getCase($la, $ca)->isCaseVide());      // (2)            ( l'autre condition est bien verifiée dans getCase() )
        $lm = $ld + ($la - $ld)/2; // coordonnées x d ela case au milieu
        $cm = $cd + ($ca - $cd)/2; // coordonnées y d ela case au milieu

        $bool &=  (($ld == $la && abs($cd - $ca) == 2)  || ($cd == $ca && abs($ld - $la) == 2)) ; //(3)
        $bool &= $this->getCase($lm, $cm)->isCaseBille();   //    ()
        return $bool;
    }
    public function estValideMvtDir($ld, $cd, $dir){
        $la = $ld;
        $ca = $cd;
        switch ($dir) {         // selectionner les coordonnées de la case d'arrivée en fct de la direction 
            case self::NORD:
                $la = $ld-2;
                break;
            case self::EST:
                $ca = $cd+2;
                break;
            case self::SUD:
                $la = $ld+2;
                break;
            case self::WEST:
                $ca = $cd-2;
                break;
        }

        return  $this->estValideMvt($ld, $cd, $la, $ca);  // ^_^
    }


    public function isBilleJouable($ld, $cd){
        // Verifier que la case contient une bille.
        // verifier si on peut le mouvemenet est possible dans l'une des 4 directions alors elle est jouable;
        return ($this->getCase($ld, $cd)->isCaseBille())&&
            ($this->estValideMvtDir($ld, $cd, self::NORD) || $this->estValideMvtDir($ld, $cd, self::SUD) ||
                $this->estValideMvtDir($ld, $cd, self::EST) || $this->estValideMvtDir($ld, $cd, self::WEST));
    }
  

    public function deplaceBille( $ligneDep, $colDep, $ligneArr, $colArr){
        //verifier si le mouvement est possible sortir sinon;
        if (! ($this->estValideMvt($ligneDep, $colDep, $ligneArr, $colArr) ) ) return;
        //coordonnées de la case au milieu
        $lm = $ligneDep + ($ligneArr - $ligneDep)/2 ;
        $cm = $colDep+ ($colArr - $colDep)/2;

        $this->remplitCase($ligneArr, $colArr);    //deplacer la bille dans la case d'arrivée
        $this->VideCase($ligneDep, $colDep);       //vider la case de départ
        $this->VideCase($lm, $cm);                 //vider la case au milieu

    }
    public function deplaceBilleDir( $ligneDep, $colDep, $dir){
        if (!$this->estValideMvtDir($ligneDep, $colDep, $dir)) return;
        //coordonnées de la case d'arrivée
        $ligneArr = $ligneDep;
        $colArr = $colDep;
        //coordonnées de la case au milieu
        $lm = $ligneDep;
        $ma = $colDep;

        switch ($dir) {         //selectionner les coordonnées de l'arrivée et du milieu en fonction de la direction ;
            case self::NORD:         //NORD
                $ligneArr = $ligneDep-2;
                $lm = $ligneDep-1;
                break;
            case self::EST :         //EST
                $colArr = $colDep+2;
                $cm = $colDep+1;
                break;
            case self::SUD :         //SUD
                $ligneArr = $ligneDep+2;
                $lm = $ligneDep+1;
                break;
            case self::WEST :         //WEST
                $colArr = $colDep - 2;
                $cm = $colDep-1;
            break;
        }

        $this->deplaceBille( $ligneDep, $colDep, $ligneArr, $colArr); // deplacer la bille  

    }


    public function __toString(){
        if($this->getNbColonnes() == 0 && $this->getNbLignes()==0)
        return "Tablier Vide !";
        
        $s = "<br>______________________<br>";
        for ($i=0; $i< $this->nbLignes; $i++)
        {
            for ($j=0; $j< $this->nbColonnes; $j++)
            {
                $s .= $this->tablier[$i][$j];
            }
            $s .= '|<br>';
        }
        $s .= "‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾<br>";
        return $s;
    }

    public function isFinPartie(){
        $fin = true;
        // verifier que toutes les billes ne sont pas jouables;
        for ($i=0; $i < $this->getNbLignes() ; $i++) { 
            for ($j=0; $j < $this->getNbColonnes() ; $j++) { 
                if ($this->getCase($i, $j)->isCaseBille() && $this->isBilleJouable($i, $j)) {
                    $fin = false;
                }
            }
        }
        return $fin;
    }

    public function isVictoire(){
        $c = 0;
        // verifier qu'il reste une seul Bille;
        for ($i=0; $i < $this->getNbLignes() ; $i++) { 
            for ($j=0; $j < $this->getNbColonnes() ; $j++) { 
                if ($this->getCase($i, $j)->isCaseBille()) {
                    $c++;
                }
            }
        }
        return $c <= 1;
    }

    public static function initTablierEuropeen(){
        
        $dimension = 7;             //la dimension utilisée par def
        // Créer un Tablier Solitaire

        $n = $dimension - ($dimension-1) /2;

        $t = new TablierSolitaire($dimension, $dimension);
    
        //Neutraliser les cases inaccessibles du jeu 
        $t->neutraliseCase(0, 0);
        $t->neutraliseCase(0, 1);
        $t->neutraliseCase(1, 0);

        $t->neutraliseCase(0, $dimension-1);
        $t->neutraliseCase(0, $dimension-2);
        $t->neutraliseCase(1, $dimension-1);

        $t->neutraliseCase($dimension-1, 0);
        $t->neutraliseCase($dimension - 2, 0);
        $t->neutraliseCase($dimension-1, 1);

        $t->neutraliseCase($dimension-1, $dimension-1);
        $t->neutraliseCase($dimension-2, $dimension-1);
        $t->neutraliseCase($dimension-1, $dimension-2);

        //Vider une seule case 
        $t->videCase(2, 3);
        

        return $t;
    }

    public static function initTablierAnglais(){
        $dimension = 7; //dimension par def
        // Créer un Tablier Solitaire

        $n = $dimension - ($dimension-1) /2;

        $t = new TablierSolitaire();
        $t->__construct($dimension, $dimension);

        //Neutraliser les cases inaccessibles du jeu 
        $t->neutraliseCase(0, 0);
        $t->neutraliseCase(0, 1);        
        $t->neutraliseCase(1, 0);
        $t->neutraliseCase(1, 1);

        $t->neutraliseCase(0, $dimension-1);
        $t->neutraliseCase(0, $dimension-2);
        $t->neutraliseCase(1, $dimension-1);
        $t->neutraliseCase(1, $dimension-2);

        $t->neutraliseCase($dimension-1, 0);
        $t->neutraliseCase($dimension - 2, 0);
        $t->neutraliseCase($dimension-1, 1);
        $t->neutraliseCase($dimension-2, 1);


        $t->neutraliseCase($dimension-1, $dimension-1);
        $t->neutraliseCase($dimension-2, $dimension-1);
        $t->neutraliseCase($dimension-1, $dimension-2);
        $t->neutraliseCase($dimension-2, $dimension-2);
        $t->videCase(3, 3);
        return $t;
    }

    public static function initTablierGagnant (){
        $dimension = 7;
        // Créer un Tablier Solitaire

        $t = new TablierSolitaire($dimension, $dimension);

        //Neutraliser les cases inaccessibles du jeu 
        $t->neutraliseCase(0, 0);
        $t->neutraliseCase(0, 1);        
        $t->neutraliseCase(1, 0);
        $t->neutraliseCase(1, 1);

        $t->neutraliseCase(0, $dimension-1);
        $t->neutraliseCase(0, $dimension-2);
        $t->neutraliseCase(1, $dimension-1);
        $t->neutraliseCase(1, $dimension-2);

        $t->neutraliseCase($dimension-1, 0);
        $t->neutraliseCase($dimension - 2, 0);
        $t->neutraliseCase($dimension-1, 1);
        $t->neutraliseCase($dimension-2, 1);


        $t->neutraliseCase($dimension-1, $dimension-1);
        $t->neutraliseCase($dimension-2, $dimension-1);
        $t->neutraliseCase($dimension-1, $dimension-2);
        $t->neutraliseCase($dimension-2, $dimension-2);

        
        // Vider tout le tableau ...
        for ($i=0; $i< $t->getNbLignes(); $i++){
            for ($j=0; $j< $t->getNbColonnes(); $j++){
               if ($t->getCase($i, $j)->isCaseBille()) {
                $t->videCase($i, $j);
               }
            }
        }

        //laisser une seule bille qu'on va poser aléatoirement sur le tablier
        $n = $dimension - ($dimension-1) /2;
        $x = 0; $y = 0;
        while(!$t->getCase($x, $y)->isCaseVide()){
            $x = random_int(0, $dimension - 1);
            $y = random_int(0, $dimension - 1);
        }
        $t->remplitCase($x, $y);

        return $t;

    }

    public static function initTablierPerdant (){

        $dimension = 7;
        // Créer un Tablier Solitaire
        $t = new TablierSolitaire($dimension, $dimension);

        //Neutraliser les cases inaccessibles du jeu 
        $t->neutraliseCase(0, 0);
        $t->neutraliseCase(0, 1);        
        $t->neutraliseCase(1, 0);
        $t->neutraliseCase(1, 1);

        $t->neutraliseCase(0, $dimension-1);
        $t->neutraliseCase(0, $dimension-2);
        $t->neutraliseCase(1, $dimension-1);
        $t->neutraliseCase(1, $dimension-2);

        $t->neutraliseCase($dimension-1, 0);
        $t->neutraliseCase($dimension - 2, 0);
        $t->neutraliseCase($dimension-1, 1);
        $t->neutraliseCase($dimension-2, 1);


        $t->neutraliseCase($dimension-1, $dimension-1);
        $t->neutraliseCase($dimension-2, $dimension-1);
        $t->neutraliseCase($dimension-1, $dimension-2);
        $t->neutraliseCase($dimension-2, $dimension-2);

        
        // Vider tout le tableau ...
        for ($i=0; $i< $t->getNbLignes(); $i++){
            for ($j=0; $j< $t->getNbColonnes(); $j++){
               if ($t->getCase($i, $j)->isCaseBille()) {
                $t->videCase($i, $j);
               }
            }
        }

        //laisser deux billes dans une position aléatoire sur le tablier
        //à condition qu'elles soient distantes (les deux ne seront pas jouables)
        $n = $dimension - ($dimension-1) /2;
        $x = 0; $y = 0;
        $w = 0; $z = 0;
        while(!$t->getCase($x, $y)->isCaseVide()){
            $x = random_int(0, $dimension - 1);
            $y = random_int(0, $dimension - 1);

            $w = random_int(0, $dimension - 1);
            $z = random_int(0, $dimension - 1);
        }
        $t->remplitCase($x, $y);

        
        while((!($t->getCase($w, $z)->isCaseVide())) || ($t->isBilleJouable($w, $z)) || ($t->isBilleJouable($x, $y)) || ($w ==$x && $y == $z) ) {
            $w = random_int(0, $dimension - 1);
            $z = random_int(0, $dimension - 1);
        }

        $t->remplitCase($w, $z);
        return $t;
    }

    public static function initTablierDefaut ($width = 3, $height = 3){
        //-------> throw exception if width>8 OR width<3 ....

        // Créer un Tablier Solitaire
        $t = new TablierSolitaire($width, $height);


        return $t;
    }

}


?>