<?php include_once '../General/LoginManager.php';

/*
 * traitement des données recu si le formulaire a deja été validé
 */
$chk_login = true;
$chk_mdp = true;
if(isset($_POST['submit'])){
    // verification que les données fournies sont acceptables
    $chk_login = 0 == mysqli_num_rows(query($mysqli, "SELECT login FROM Utilisateurs WHERE login='".$_POST['login']."'"));
    $chk_mdp = $_POST['mdp'] == $_POST['mdp2'];

    if($chk_login && $chk_mdp){
        // création du compte dans la base de donnée
        query($mysqli, "INSERT INTO Utilisateurs VALUES ('"
            .addslashes($_POST['login'])."','"
            .password_hash($_POST['mdp'],PASSWORD_BCRYPT)."',"
            .(isset($_POST['nom']) ? "'".addslashes($_POST['nom'])."'" : "null").","
            .(isset($_POST['prenom']) ? "'".addslashes($_POST['prenom'])."'" : "null").","
            .(isset($_POST['sexe']) ? $_POST['sexe'] : "null").","
            .(isset($_POST['email']) ? "'".$_POST['email']."'" : "null").","
            .((isset($_POST['naissance']) && $_POST['naissance'] != "") ? "'".$_POST['naissance']."'" : "null").","
            .(isset($_POST['adresse']) ? "'".addslashes($_POST['adresse'])."'" : "null").","
            .(isset($_POST['code_postal']) ? "'".$_POST['code_postal']."'" : "null").","
            .(isset($_POST['ville']) ? "'".addslashes($_POST['ville'])."'" : "null").","
            .(isset($_POST['telephone']) ? "'".$_POST['telephone']."'" : "null").")");
        $_SESSION['utilisateur'] = $_POST['login'];
        header('Location: ../Recipes/Hierarchie.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Créer un compte | Cokctails</title>

    <?php include '../General/Head.html'?>
</head>

<body>

<?php include '../General/Header.php' ?>
<section id="main" class="wrapper">
    <div class="container 75%">
        <h1>Veuillez saisir vos informations</h1>
        <!-- formulaire de création de compte -->
        <form name="formulaire" method="post" action=# >
            <table class="alt">
                <tbody>
                <tr><td>Nom d'utilisateur* : </td>
                    <td><input type="text" minlength="2" maxlength="30" required name="login"
                            <?php if(isset($_POST['login'])) echo "value=\"".$_POST['login']."\" "; // maintient de la valeur déjà fournie?>
                        /> </td>
                    <?php if(!$chk_login) echo "<td>login déjà utilisé</td>" // message d'erreur si le nom d'utilisateur est indisponible ?></tr>
                <tr><td>Mot de passe* : </td>
                    <td><input type="password" minlength="4" maxlength="30" required name="mdp" /> </td></tr>
                <tr><td>Confirmation du mot de passe* : </td>
                    <td><input type="password" minlength="4" maxlength="30" required name="mdp2" /> </td>
                    <?php if(!$chk_mdp) echo "<td>confirmation de mot de passe incorrecte</td>" // message d'erreur si le mot de passe est mal confirmé ?></tr>
                <tr><td>Nom : </td>
                    <td><input type="text" maxlength="30" name="nom"
                            <?php if(isset($_POST['nom'])) echo "value=\"".$_POST['nom']."\" "; // maintient de la valeur déjà fournie?>
                        /> </td></tr>
                <tr><td>Prénom : </td>
                    <td><input type="text" maxlength="30" name="prenom"
                            <?php if(isset($_POST['prenom'])) echo "value=\"".$_POST['prenom']."\" "; // maintient de la valeur déjà fournie?>
                        /> </td></tr>
                <tr><td>Sexe : </td>
                    <td><input type="radio" name="sexe" value=0 id="h"
                            <?php if(isset($_POST['sexe']) && $_POST['sexe'] == 0) echo "checked "; // maintient de la valeur déjà fournie?>
                        />
                        <label for="h">Homme</label>
                        <input type="radio" name="sexe" value=1 id="f"
                            <?php if(isset($_POST['sexe']) && $_POST['sexe'] == 1) echo "checked "; // maintient de la valeur déjà fournie?>
                        />
                        <label for="f">Femme</label></td></tr>
                <tr><td>E-mail : </td>
                    <td><input type="email" name="email"
                            <?php if(isset($_POST['email'])) echo "value=\"".$_POST['email']."\" "; // maintient de la valeur déjà fournie?>
                        /> </td></tr>
                <tr><td>Date de naissance : </td>
                    <td><input type="date" name="naissance"
                            <?php if(isset($_POST['naissance'])) echo "value=\"".$_POST['naissance']."\" "; // maintient de la valeur déjà fournie?>
                        /> </td></tr>
                <tr><td>Adresse : </td>
                    <td><input type="text" maxlength="100" name="adresse"
                            <?php if(isset($_POST['adresse'])) echo "value=\"".$_POST['adresse']."\" "; // maintient de la valeur déjà fournie?>
                        /> </td></tr>
                <tr><td>Code postal : </td>
                    <td><input type="text" pattern="[0-9]{5}" maxlength="5" name="code_postal"
                            <?php if(isset($_POST['code_postal'])) echo "value=\"".$_POST['code_postal']."\" "; // maintient de la valeur déjà fournie?>
                        /> </td></tr>
                <tr><td>Ville : </td>
                    <td><input type="text" maxlength="100" name="ville"
                            <?php if(isset($_POST['ville'])) echo "value=\"".$_POST['ville']."\" "; // maintient de la valeur déjà fournie?>
                        /> </td></tr>
                <tr><td>Téléphone : </td>
                    <td><input type="text" pattern="^(+33|0)[0-9]{9}$" name="telephone"
                            <?php if(isset($_POST['telephone'])) echo "value=\"".$_POST['telephone']."\" "; // maintient de la valeur déjà fournie?>
                        /> </td></tr>
                </tbody>
                <tfoot>
                <tr><td>*Champs obligatoires</td>
                    <td><input type="submit" value="Valider" name="submit" /> &nbsp &nbsp
                        <a href="Login.php">Déjà un compte ?</a></td></tr>
                </tfoot>
            </table>
        </form>
    </div>
</section>

<?php include '../General/Footer.php' ?>

</body>