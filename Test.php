<?php

include('TablierSolitaire.php');
echo ' ஐ Membre Du Groupe :  <br> ';
echo ' ===== Meksaoui Aris   <br> ';
echo ' ===== id:20204380  <br> ';

echo '<br> ஐ ஐ ஐ ஐ ஐ        Some Tests       ஐ ஐ ஐ ஐ ஐ <br> ';

//Initialisation
$eur = TablierSolitaire::initTablierEuropeen();
$ang = TablierSolitaire::initTablierAnglais();
$win =TablierSolitaire::initTablierGagnant();
$lose = TablierSolitaire::initTablierPerdant();

//Affichage
echo "<br> ☩☩☩ Tablier Europeen ☩☩☩";
echo $eur;
echo "<br> ☩☩☩ Tablier Anglais ☩☩☩";
echo $ang;
echo "<br> ☩☩☩ Tablier Gagnant ☩☩☩";
echo $win;
echo "<br> ☩☩☩ Tablier Perdant ☩☩☩";
echo $lose;

echo "<br> ஐ Let's do some Tests <br><br>";
echo '  ☩ Les Getteurs (Tablier Européen) ';
echo " <br> |--  getNbLignes() : ".$eur->getNbLignes();
echo " <br> |-- getNbColonnes() : ".$eur->getNbColonnes();
echo " <br> |-- getCase(0,0)  : ".$eur->getCase(0,0)."| ça veut dire neutralisée.";
echo " <br> |-- isFinPartie() : "; if($eur->isFinPartie()) echo"OUI";else echo "NON";
echo " <br> |__ isVictoire() : "; if($eur->isVictoire()) echo"OUI";else echo "NON";

echo "<br><br>";
echo '  ☩ Les Getteurs (Tablier Perdant) ';
echo " <br> |-- isFinPartie() : "; if($lose->isFinPartie()) echo"OUI";else echo "NON";
echo " <br> |__ isVictoire() : "; if($lose->isVictoire()) echo"OUI";else echo "NON";

echo "<br><br>";
echo '  ☩ Les Getteurs (Tablier Gagnant) ';
echo " <br> |-- isFinPartie() : "; if($win->isFinPartie()) echo"OUI";else echo "NON";
echo " <br> |__ isVictoire() : "; if($win->isVictoire()) echo"OUI";else echo "NON";

echo "<br><br>";
echo '  ☩ possibilités de jeu (Tablier Gagnant) ';
echo " <br> |-- estValideMvt(2, 4, 2, 3) : "; if($eur->estValideMvt(2, 4, 2, 3)) echo"OUI";else echo "NON";
echo " <br> |--estValideMvt(2, 4, 2, 3) : "; if($eur->estValideMvt(2, 5, 2, 3)) echo"OUI";else echo "NON";
echo " <br> |-- estValideMvtDir(2, 4, 1) : "; if($eur->estValideMvtDir(2, 5, 1)) echo"OUI";else echo "NON";
echo " <br> |__ estValideMvtDir(2, 4, 3) : "; if($eur->estValideMvtDir(2, 5, 3)) echo"OUI";else echo "NON";
echo " <br><br>";
echo '  ☩ Jouer (Tablier Gagnant) <br>';
echo "  |--deplaceBille(2, 5, 2, 3) ";$eur->deplaceBille(2, 5, 2, 3);
echo $eur;
echo "  |--deplaceBilleDir(2, 2, 1) ";$eur->deplaceBilleDir(2, 2, 1);
echo $eur;
echo " <br><br>";
echo " ஐஐஐஐஐ  Wooaah çA MARCHE !!!   ஐஐஐஐஐ ";
echo " <br><br>";



?>