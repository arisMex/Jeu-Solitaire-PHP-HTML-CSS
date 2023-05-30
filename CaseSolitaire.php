<?php

class CaseSolitaire
{
    const BILLE = 1;
    const VIDE = 0;
    const NEUTURALISE = -1;

    protected $valeur;

    public FUNCTION __construct($val=self::BILLE){  //Valeure BILLE=1 par def 
        $this->valeur = $val;
    }

    public function __toString(){
        $S = "";
        switch ($this->valeur) {
            case self::BILLE:
                $S = "| ◉ ";
                break;
            case self::VIDE:
                $S = "| ✲ ";
                break;
            case self::NEUTURALISE:
                $S = "| ▨ ";//☩ ஐ
                break;

        }
        return $S;
    }

    public FUNCTION getValeur($x){
        
        return $this->valeur;
    }
    public FUNCTION setValeur($x){
        if (!($x <=1 && $x>=-1)) {     //Checker la valeur entrée
            throw new Exception('setValue must be in values {-1, 0, 1}');
        }
        $this->valeur = $x;
    }

    public FUNCTION  isCaseVide(){
        return $this->valeur == self::VIDE;
    }
    public FUNCTION  isCaseBille(){
        return $this->valeur == self::BILLE;
    }

    public FUNCTION  isCaseNeutralise(){
        return $this->valeur == self::NEUTURALISE;
    }



}

?>