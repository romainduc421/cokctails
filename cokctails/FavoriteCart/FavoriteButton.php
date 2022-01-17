<?php include_once './General/LoginManager.php';
/*
 * fichier appelé par la fonction ajax gererListeFavoris
 * il ajoute ou retire une recette de la liste des favoris
 */

if(isset($_SESSION['utilisateur'])) {
    // cas d'un utilisateur connecté
    if($_POST['ajouter'] == "true") {
        // ajout de la recette favorite dans la base de donnée
        $mysqli=null;
        query($mysqli,
            "INSERT INTO Panier VALUE ('".$_SESSION['utilisateur']."','".$_POST['recette']."')");
    } else {
        // retrait de la recette de la base de donnée
        $mysqli =null;
        query($mysqli,
            "DELETE FROM Panier ".
            "WHERE login='".$_SESSION['utilisateur']."' AND boisson='".$_POST['recette']."'");
    }
} else {
    // cas d'un utilisateur non connecté
    if($_POST['ajouter'] == "true") {
        // ajout de la recette favorite au tableau de session
        $_SESSION['favoris'][] = $_POST['recette'];
    } else {
        // retrait de la recette du tableau de session
        $key = array_search($_POST['recette'],$_SESSION['favoris']);
        unset($_SESSION['favoris'][$key]);
    }
}