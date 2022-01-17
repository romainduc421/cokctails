<?php include_once '../General/LoginManager.php';
include_once '../Recipes/Functions.php';
include_once('../Donnees.inc.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mes favoris | Cokctials</title>

    <?php include '../General/Head.html'?>

    <!-- plutot que de charger jquery sur le serveur, nous utilisons celui du site directement -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="FavoriteButton.js"></script>
</head>

<body>

<?php include '../General/Header.php' ?>

<section class="wrapper style1">

    <!-- affichage des fiche de recettes favoris -->
    <div id="resultats" class="inner">
        <?php
        global $Recettes;
        global $mysqli;
        // parcour de la liste des recettes et comparaison avec la liste des favoris
        if(isset($_SESSION['utilisateur'])) {
            foreach ($Recettes as $key => $recette) {
                // si l'utilisateur est connecté, recherche des recettes dans la base de donnée
                $resultat = query($mysqli,
                    "SELECT * FROM Panier WHERE login='".$_SESSION['utilisateur'].
                    "' AND boisson='".addslashes($recette['titre'])."'");
                if(1 == mysqli_num_rows($resultat)){
                    echo genererFicheRecette($recette,null);
                }
            }
        } else {
            foreach ($Recettes as $key => $recette) {
                /**
                 * si l'utilisateur n'est pas connecté, recherche des recettes dans le tableau de session
                */
                if(in_array(addslashes($recette['titre']), $_SESSION['favoris']))
                    echo genererFicheRecette($recette,null);
            }
        }
        ?>
    </div>
</section>

<?php include '../General/Footer.php' ?>

</body>

</html>