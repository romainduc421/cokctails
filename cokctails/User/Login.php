<?php include_once '../General/LoginManager.php';

/**
 * traitement des données recu si le formulaire a deja été validé
 */
if(isset($_POST['submit'])) {
    /**
     * recherche du compte demandé dans la base de donnée
     */
    $resultat = query($mysqli,"SELECT mdp FROM Utilisateurs WHERE login='".addslashes($_POST['login'])."'");

    if(1 == mysqli_num_rows($resultat)){ // le compte a été trouvé
        $hash = mysqli_fetch_assoc($resultat)['mdp'];
        if(password_verify($_POST['mdp'],$hash)) {
            /**
             * definition de la variable de session utilisée pour verifier la connexion
             */
            $_SESSION['utilisateur'] = addslashes($_POST['login']);

            /**
             * ajout des recettes marqués comme favorites avant la connexion dans la base de données
             */
            foreach ($_SESSION['favorites'] as $key => $recette) {
                /**
                 * verification pour éviter les redondances
                 */
                $resultat = query($mysqli,
                    "SELECT * FROM Panier WHERE login='" . $_SESSION['utilisateur'] .
                    "' AND boisson='" . $recette . "'");
                if (0 == mysqli_num_rows($resultat)) {
                    /**
                     * si elle ne fait pas deja partie des favorites, ajout de la recette
                     */
                    query($mysqli,
                        "INSERT INTO Panier VALUE ('" . $_SESSION['utilisateur'] . "','" . $recette . "')");
                }
            }

            // redirection vers la page des recettes favorites
            header('Location: ../FavoriteCart/MyFavRecipes.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Se connecter | Cokctails</title>

    <?php include "../General/Head.html"?>
</head>

<body>

<?php include '../General/Header.php' ?>

<section id="main" class="wrapper">
    <div class="container 75%">
        <h1>Bienvenue !</h1>
        <!-- formulaire de connection -->
        <form name="formulaire" method="post" action=# >
            <table class="alt">
                <tbody>
                <tr><td>Nom d'utilisateur* : </td><td> <input type="text" minlength="2" maxlength="30" required name="login" /> </td></tr>
                <tr><td>Mot de passe* : </td><td> <input type="password" minlength="4" maxlength="30" required name="mdp" /> </td></tr>
                </tbody>
                <tfoot>
                <tr><td></td>
                    <td><input type="submit" value="Valider" name="submit" /> &nbsp &nbsp
                        <a href="Signup.php">Pas encore inscrit ?</a></td></tr>
                <?php if(isset($_POST['submit'])) echo "<tr><td></td><td>Nom d'utilisateur ou mot de passe incorrect</td></tr>" ?>
                <tr><td></td><td>  </td></tr>
                </tfoot>
            </table>
        </form>
    </div>
</section>

<?php include '../General/Footer.php' ?>

</body>