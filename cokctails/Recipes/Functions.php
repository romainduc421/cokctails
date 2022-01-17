<?php include_once '../General/LoginManager.php';
include_once('../Donnees.inc.php');


/**
 * Cette fonction permet de charger l'image associée à une recette, dans une balise <span>
 * L'image est soit une image spécifique, si une photo de la recette est dans nos fichiers,
 * soit une image par défaut dans le cas inverse.
 * @param $nom_recette : le nom de la recette dont il faut chercher l'image associée.
 * @return string l'extrait de code HTML qui correspond à l'image associée à la recette.
 */
function genererImage($nom_recette) {

    /*
     * Cet enchainement de fonctions permet d'enlever les accents du nom de l'aliment,
     * en le passant d'abord en format UNICODE avant de traiter ce code.
     */
    $nom_recette = htmlentities( $nom_recette, ENT_NOQUOTES, 'utf-8' );

    /*
     * Les regexp proviennent en partie du code d'un utilisateur de Stackoverflow,
     * https://www.tutos.eu/6436
     * https://openclassrooms.com/forum/sujet/je-ne-comprends-pas-une-regex
     */
    $nom_recette = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '${1}', $nom_recette );
    $nom_recette = preg_replace( '#&([A-za-z]{2})lig;#', '${1}', $nom_recette );
    $nom_recette = preg_replace( '#&[^;]+;#', '', $nom_recette );

    /*
     * On enlève tous les caractères sauf les lettres et les espaces
     */
    $nom_recette = preg_replace( '/([^ A-Za-z])/', '', $nom_recette );

    /*
     * On remplace les espaces par des "_", et on met toutes les lettres en minuscule
     * sauf la première qui est mise en majuscule. On génère ensuite l'url en ajoutant
     * l'arborescence de fichiers et l'extension ".jpg".
     */
    $nom_recette = ucwords(strtolower(preg_replace("/ /", "_",$nom_recette)));
    $url= "../Photos/".$nom_recette.".jpg";

    /*
     * On tente d'ouvrir l'URL. Si on y arrive, l'image chargée a pour source l'URL générée.
     * Si on ne peut pas, alors c'est que le fichier n'existe pas et l'image chargée est une image par défaut.
     */
    $F=@fopen($url,"r");
    if($F) {
        fclose($F);
        return "<span class=\"image fit\"><img src=\"$url\" alt=\"Photo du cocktail\"></span>";
    }
    else
        return "<span class=\"image fit\"><img src=\"../Photos/cocktail.png\" alt=\"Photo par défaut\" style=\"background-color:lightgray;\" ></span>";

}

/**
 * Cette fonction récursive permet d'explorer la hiérarchie des aliments pour vérifier
 * si un ingrédient (ingredient_under) est égal à un autre ingrédient (ingredient_super)
 * ou est une sous-catégorie de celui ci. Par exemple, estDansLesAliments(Fruit,Fruit sec)
 * renverra true parce que Fruit sec est une sous-catégorie de fruit
 * @param $ingredient_super l'ingrédient dont on doit vérifier s'il est
 * l'ingredient_under ou une de ses super-catégories
 * @param $ingredient_under l'ingredient dont on doit verifier s'il est
 * l'ingredient_super ou une de ses sous-catégories.
 * @return bool true si l'ingrédient est l'ingrédient_super ou une sous-catégorie
 * de l'ingrédient super, false sinon.
 */
function estDansLesAliments($ingredient_super,$ingredient_under){

    global $Hierarchie;
    $res = false;
    if($ingredient_super==$ingredient_under){
        /*
         * Si les deux ingrédients sont égaux alors on renvoie true
         */
        $res = true;
    } else if(!isset($Hierarchie[$ingredient_under]['super-categorie'])) {
        /*
         * Si ingredient_under n'a plus de super-categorie et qu'il n'est pas égal à
         * ingredient_super, alors ingredient_under est égal à Aliment et ne
         * peut donc pas être une sous-catégorie de ingredient_super. Donc on renvoie false
         */
        $res = false;
    } else {
        /*
         * Appel récursif sur chaque super-catégorie d'ingredient_under
         */
        foreach($Hierarchie[$ingredient_under]['super-categorie'] as $key => $value){
            if (estDansLesAliments($ingredient_super,$value))
                return true;
        }

        $res = false;
    }
    /**
     * Si aucun appel recursif n'a renvoyé true, alors on renvoie false.
     */
    return $res;

}

/**
 * Cette fonction permet de génrer le code HTML de la fiche d'une recette dont le nom est fourni
 * en paramètes. Si une note supérieure à 0 est fournie avec, on l'affiche également
 * @param $recette : la recette à partir de laquelle il faut créer une fiche
 * @param $note : score sur 100 représentant la correspondance entre les attentes
 * de l'utilisateur et la recette, lorsqu'il utilise la fonction "Rechercher". Dans
 * la fonction "Explorer", on met "null" en paramètres, et la note n'est pas affichée.
 * @return string le code HTML de la fiche recette associée à la recette fournie en paramètes
 */
function genererFicheRecette($recette,$note){


    if(isset($recette['titre'])&&isset($recette['ingredients'])&&isset($recette['preparation'])){
        $affichage =
                "<article class=\"feature left\">".
                    genererImage($recette['titre']).
                    "<div class=\"content\">
                        <h3>" . $recette['titre']."</h3>
                        <h4>ingredients</h4>".
                        genererListe("|",$recette['ingredients'])."
                        <h4>method for preparing</h4>
                        <p>".$recette['preparation']."</p>
                        <ul class=\"actions\">";

        /**
         * Si la note est définie (fonction Rechercher), alors on l'affiche.
         */
        if(isset($note)){
            $affichage.= "    <li>
                                 <button class=\"button special\">Score : ".intval($note)."%</button>
                              </li>";
        }

        global $mysqli;
        $titre = addslashes($recette['titre']);
        if(isset($_SESSION['utilisateur'])) {
            $resultat = query($mysqli,"SELECT * FROM Panier WHERE login='".$_SESSION['utilisateur']."' AND boisson='".$titre."'");

            if(mysqli_num_rows($resultat) == 0){
                // l'utilisateur est connecté et la recette ne fait pas partie de ses favoris
                $cl = "button special";
                $ajouter = "true";
                $texte = "Add recipe to favorite";
            } else {
                // l'utilisateur est connecté et la recette fait partie de ses favoris
                $cl = "button";
                $ajouter = "false";
                $texte = "Remove recipe to favorite";
            }
        } else {
            if(in_array($titre,$_SESSION['favoris'])){
                // l'utilisateur n'est pas connecté et la recette fait partie de ses favoris
                $cl = "button";
                $ajouter = "false";
                $texte = "Remove recipe to favorite";
            } else {
                // l'utilisateur n'est pas connecté et la recette ne fait pas partie de ses favoris
                $cl = "button special";
                $ajouter = "true";
                $texte = "Add recipe to favorite";
            }
        }

        $affichage.=
            "<li><button class=\"".$cl."\" " .
            "onclick=\"manageFavorites(this,'".addslashes($titre)."',".$ajouter.")\">"
            .$texte."</button></li>";


        $affichage.= "
                        </ul>
                     </div>
                </article>";
        return $affichage;
    } else {
        /*
         * Si tout n'est pas bien définie on créé une fiche "Erreur"
         */
        return "<article class=\"feature left\">
                    <div class=\"content\">
                        <h1>La fiche demandée n'existe pas</h1>
                    </div>
                </article>";
    }
}

/**
 * Cette fonction permet de générer le code HTML d'une liste <ul> à partir d'une string
 * qui représente une liste, et qui prend la forme de mots séparés par un caractère spécial.
 * Par exemple "50cl de jus de cerises,20 grains de raisins" avec pour séparateur ",", contient
 * deux éléments.
 * @param $separateur : le separateur de la chaine de caractères.
 * @param $liste : une liste sous la forme d'une chaine de caractères, avec des mots
 * séparés par un caractère spécial (le séparateur).
 * @return string le code HTML d'une liste d'éléments correspondant à la liste fournie
 * en paramètre.
 */
function genererListe($separateur,$liste){
    $liste = explode($separateur,$liste);
    $affichage ="<ul>";
    foreach ($liste as $value)
        $affichage.="<li>".$value."</li>";
    return $affichage."</ul>";
}