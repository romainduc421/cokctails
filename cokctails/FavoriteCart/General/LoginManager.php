<?php
/**
 * fichier php inclus au debut de chaque page
 */

session_start();

// supression de la session si la déconnexion est demandée
if(isset($_GET['deconnexion']) && $_GET['deconnexion'] == true) session_unset();

// initialisation du tableau de favoris (mode déconnecté)
if(!isset($_SESSION['favoris'])) $_SESSION['favoris']=array();

/**
 * encapsulation de la fonction mysqli_query pour gerer les erreurs
 */
function query($link,$requete) {
    $resultat = mysqli_query($link,$requete) or die("$requete : ".mysqli_error($link));
    return $resultat;
}

/**
 * connection a la DB
 */
global $mysqli;
$mysqli=mysqli_connect('localhost', 'root', '') or die("Erreur de connexion");
$base="Utilisateurs";
query($mysqli,"USE $base");