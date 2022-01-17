<?php

/**
 * Il s'agit d'une fonction de comparaison qui compare deux recettes en fonction de leur
 * score de correspondance aux attentes de l'utilisateur. A score égal, on utilise l'ordre
 * alphabétique.
 * Cette fonction est utile pour créer une fonctionnalité de tri personnalisée, pour gérer
 * l'ordre d'affichage des recettes.
 * @param $recette_score1 : un premier tableau composé d'une recette et d'un score associé
 * @param $recette_score2 : un deuxième tableau composé d'une recette et d'un score associé
 * @return int -1 si le score de la premiere recette est strictement supérieur à celui
 * de la seconde recette ou qu'il est égal et que la première recette précède la seconde
 * dans l'ordre lexicoggraphique. 1 sinon.
 */
function customSort($recette_score1,$recette_score2){
    if(isset($recette_score1['score'])&&isset($recette_score2['score'])) {
        if (intval($recette_score1['score']) > intval($recette_score2['score'])) {
            return -1;
        } else if ($recette_score1['score']==$recette_score2['score']) {
            return strcmp($recette_score1['recette']['titre'], $recette_score2['recette']['titre']);
        } else {
            return 1;
        }
    } else {
        return 1;
    }
}


/**
 * Cette fonction renvoie le nombre total de contraintes, ce qui consiste à compter
 * le nombre d'éléments non vides des deux listes de contraintes
 * @param $liste_inclusions : la liste des ingrédients à inclure dans la recette
 * @param $liste_exclusions : la liste des ingrédients à exclure de la recette
 * @return int le nombre total de contraintes
 */
function totalPoints($liste_inclusions,$liste_exclusions){
    $total_points = count($liste_inclusions)+count($liste_exclusions);

    foreach($liste_inclusions as $value){
        if($value==""){
            $total_points--;
        }
    }
    foreach($liste_exclusions as $value){
        if($value==""){
            $total_points--;
        }
    }
    return $total_points;
}

/*
 * Debut du code principal
 */
include_once('../Donnees.inc.php');
include_once('./Functions.php');

if(isset($_POST['aliments_inclus'])&&isset($_POST['aliments_exclus'])) {

    /*
     * Récupération des listes sous forme de string et explode avec "|"
     * pour obtenir des array()
     */
    $liste_inclusions = $_POST['aliments_inclus'];
    $liste_inclusions = explode("|",$liste_inclusions);
    $liste_exclusions = $_POST['aliments_exclus'];
    $liste_exclusions = explode("|",$liste_exclusions);

    /*
     * Récupération du nombre total de points, qui servira de dénominateur
     * pour calculer la note
     */

    $total_points = totalPoints($liste_inclusions,$liste_exclusions);
    if($total_points!=0) {

        /*
         * On va itérer sur chaque recette
         */
        $liste_recettes_score = array();
        foreach($Recettes as $key=>$recette) {
            $score = 0;

            /*
             * Pour chaque ingrédient de la liste des ingrédients à inclure
             * Si l'ingrédient ou un sous-ingrédient de cet ingrédient est dans la recette,
             * on incrémente le score.
             */
            foreach ($liste_inclusions as $aliment_a_ajouter) {
                if ($aliment_a_ajouter != "") {
                    $est_dedans = false;
                    foreach ($recette['index'] as $key_index => $aliment_recette) {
                        if (estDansLesAliments($aliment_a_ajouter, $aliment_recette)) {
                            $est_dedans = true;
                        }
                    }
                    if ($est_dedans) {
                        $score++;
                    }
                }
            }

            /*
             * Pour chaque ingrédient de la liste des ingrédients à exclure
             * Si ni l'ingrédient ni aucun sous-ingrédient de cet ingrédient n'est dans la recette,
             * on incrémente le score
             */
            foreach ($liste_exclusions as $aliment_a_retirer) {
                if ($aliment_a_retirer != "") {
                    $est_dedans = false;
                    foreach ($recette['index'] as $key_index => $aliment_recette) {
                        if (estDansLesAliments($aliment_a_retirer, $aliment_recette)) {
                            $est_dedans = true;
                        }
                    }
                    if (!$est_dedans) {
                        $score++;
                    }
                }
            }

            /*
             * On créé une liste de recettes auxquelles on associe le score calculé
             */
            $recette_score = array('recette' => $recette, 'score' => $score);
            array_push($liste_recettes_score, $recette_score);
        }

        /*
         * On trie la liste par ordre décroissant (plus haut score en premiers)
         * On se sert de la fonction de comparaison définie précédemment.
         */
        uasort($liste_recettes_score, "customSort");
        if(isset($_POST['seuil'])){
            $seuil = $_POST['seuil'];
        } else {
            $seuil = 100;
        }

        /*
         * Une fois la liste triée, on l'explore, on calcule la note (pourcentage
         * de correspondance), et on créé une fiche si le seuil de tolérance est dépassé
         */
        foreach($liste_recettes_score as $key=>$recette_score) {
            $note = (($recette_score['score'] * 100) / $total_points);
            if($note>=intval($seuil)) {
                echo genererFicheRecette($recette_score['recette'],$note);
            }
        }
    }
}