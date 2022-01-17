<?php include_once '../General/LoginManager.php';

/**
 * traitement des données reçues si le formulaire a été validé
 */
$chk_login = true;
$chk_mdp = true;
if(isset($_POST['submit']) && $_POST['submit'] == "Enregistrer"){
    // verification que les données fournies sont acceptables
    $chk_login = ($_POST['login'] == $_SESSION['utilisateur']
        || 0 == mysqli_num_rows(query($mysqli, "SELECT login FROM Utilisateurs WHERE login='".$_POST['login']."'")));
    $chk_mdp = (isset($_POST['mdp']) && isset($_POST['mdp2']) && $_POST['mdp'] == $_POST['mdp2'])
        || !(isset($_POST['mdp']) || isset($_POST['mdp2']));

    if($chk_login && $chk_mdp){
        // modification des informations du compte dans la base de donnée
        query($mysqli, "UPDATE Utilisateurs SET "
            ."login='".addslashes($_POST['login'])."',"
            .(isset($_POST['mdp']) && $_POST['mdp'] != "" ? "mdp='".password_hash($_POST['mdp'],PASSWORD_BCRYPT)."'," : "")
            ."nom=".(isset($_POST['nom']) ? "'".addslashes($_POST['nom'])."'" : "null").","
            ."prenom=".(isset($_POST['prenom']) ? "'".addslashes($_POST['prenom'])."'" : "null").","
            ."sexe=".(isset($_POST['sexe']) ? $_POST['sexe'] : "null").","
            ."email=".(isset($_POST['email']) ? "'".$_POST['email']."'" : "null").","
            ."naissance=".((isset($_POST['naissance']) && $_POST['naissance'] != "") ? "'".$_POST['naissance']."'" : "null").","
            ."adresse=".(isset($_POST['adresse']) ? "'".addslashes($_POST['adresse'])."'" : "null").","
            ."code_postal=".(isset($_POST['code_postal']) ? "'".$_POST['code_postal']."'" : "null").","
            ."ville=".(isset($_POST['ville']) ? "'".addslashes($_POST['ville'])."'" : "null").","
            ."telephone=".(isset($_POST['telephone']) ? "'".$_POST['telephone']."'" : "null")." "
            ."WHERE login='".$_SESSION['utilisateur']."'");
        // mise a jour du nom de l'utilisateur dans le tableau de session
        $_SESSION['utilisateur'] = $_POST['login'];

        // rechargement de la page
        header('Location: Account.php');
        exit();
    }
}

if(isset($_SESSION['utilisateur'])) {
    $resultat = query($mysqli,"SELECT * FROM Utilisateurs WHERE login='".$_SESSION['utilisateur']."'");

    if(1 == mysqli_num_rows($resultat)){
        $infos = mysqli_fetch_assoc($resultat);
    } else { // cette section n'est jamais utilisé si le site est utilisé correctement
        // si le compte indiqué par la session ne correspond a aucune entrée de la base de donnée
        // supression de la valeur inchoérente et redirection vers la page de connexion
        unset($_SESSION['utilisateur']);
        header('Location: Login.php');
        exit();
    }
} else { // cette section n'est jamais utilisé si l'utilisateur suis les liens du site
    // si l'utilisateur n'est pas connecté, redirection vers la page de connexion
    header('Location: Login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <title>Mon compte | Cokctails</title>

    <?php include "../General/Head.html"?>

</head>

<body>

<?php include '../General/Header.php' ?>

<section id="main" class="wrapper">
    <div class="container 75%">
        <h1>Vos informations</h1>
        <!-- affichage des données de l'utilisateur dans un formuaire pour être modifiables -->
        <form name="formulaire" method="post" action=# >
            <table class="alt">
                <tbody>
                <tr><td>nom d'utilisateur : </td>
                    <td><input type="text" minlength="2" maxlength="30" required name="login"
                            <?php if(isset($_POST['login'])) echo "value=\"".$_POST['login']."\" "; // maintient de la valeur déjà fournie
                            else if(isset($infos['login'])) echo "value=\"".$infos['login']."\" ";?>
                        /> </td>
                    <?php if(!$chk_login) echo "<td>login déjà utilisé</td>" // message d'erreur si le nouveau nom d'utilisateur est indisponible ?></tr>
                <tr><td>modifier mot de passe : </td>
                    <td><input type="password" minlength="4" maxlength="30" name="mdp" /> </td></tr>
                <tr><td>confirmation : </td>
                    <td><input type="password" minlength="4" maxlength="30" name="mdp2" /> </td>
                    <?php if(!$chk_mdp) echo "<td>confirmation de mot de passe incorrecte</td>" // message d'erreur si le mot de passe est mal confirmé ?></tr>
                <tr><td>nom : </td>
                    <td><input type="text" maxlength="30" name="nom"
                            <?php if(isset($_POST['nom'])) echo "value=\"".$_POST['nom']."\" "; // maintient de la valeur déjà fournie
                            else if(isset($infos['nom'])) echo "value=\"".$infos['nom']."\" ";?>
                        /> </td></tr>
                <tr><td>prénom : </td>
                    <td><input type="text" maxlength="30" name="prenom"
                            <?php if(isset($_POST['prenom'])) echo "value=\"".$_POST['prenom']."\" "; // maintient de la valeur déjà fournie
                            else if(isset($infos['prenom'])) echo "value=\"".$infos['prenom']."\" ";  // ou affichage de la valeur enregistrée ?>
                        /> </td></tr>
                <tr><td>sexe : </td>
                    <td><input type="radio" name="sexe" value=0 id="h"
                            <?php if(isset($_POST['sexe']) && $_POST['sexe'] == 0) echo "checked "; // maintient de la valeur déjà fournie
                            else if(isset($infos['sexe']) && $infos['sexe'] == 0) echo "checked ";  // ou affichage de la valeur enregistrée ?>
                        />
                        <label for="h">Homme</label>
                        <input type="radio" name="sexe" value=1 id="f"
                            <?php if(isset($_POST['sexe']) && $_POST['sexe'] == 1) echo "checked "; // maintient de la valeur déjà fournie
                            else if(isset($infos['sexe']) && $infos['sexe'] == 1) echo "checked ";  // ou affichage de la valeur enregistrée ?>
                        />
                        <label for="f">Femme</label></td></tr>
                <tr><td>email : </td>
                    <td><input type="email" name="email"
                            <?php if(isset($_POST['email'])) echo "value=\"".$_POST['email']."\" "; // maintient de la valeur déjà fournie
                            else if(isset($infos['email'])) echo "value=\"".$infos['email']."\" ";  // ou affichage de la valeur enregistrée ?>
                        /> </td></tr>
                <tr><td>date de naissance : </td>
                    <td><input type="date" name="naissance"
                            <?php if(isset($_POST['naissance'])) echo "value=\"".$_POST['naissance']."\" "; // maintient de la valeur déjà fournie
                            else if(isset($infos['naissance'])) echo "value=\"".$infos['naissance']."\" ";  // ou affichage de la valeur enregistrée ?>
                        /> </td></tr>
                <tr><td>adresse : </td>
                    <td><input type="text" maxlength="100" name="adresse"
                            <?php if(isset($_POST['adresse'])) echo "value=\"".$_POST['adresse']."\" "; // maintient de la valeur déjà fournie
                            else if(isset($infos['adresse'])) echo "value=\"".$infos['adresse']."\" ";  // ou affichage de la valeur enregistrée ?>
                        /> </td></tr>
                <tr><td>code postal : </td>
                    <td><input type="text" pattern="[0-9]{5}" maxlength="5" name="code_postal"
                            <?php if(isset($_POST['code_postal'])) echo "value=\"".$_POST['code_postal']."\" "; // maintient de la valeur déjà fournie
                            else if(isset($infos['code_postal'])) echo "value=\"".$infos['code_postal']."\" ";  // ou affichage de la valeur enregistrée ?>
                        /> </td></tr>
                <tr><td>ville : </td>
                    <td><input type="text" maxlength="100" name="ville"
                            <?php if(isset($_POST['ville'])) echo "value=\"".$_POST['ville']."\" "; // maintient de la valeur déjà fournie
                            else if(isset($infos['ville'])) echo "value=\"".$infos['ville']."\" ";  // ou affichage de la valeur enregistrée ?>
                        /> </td></tr>
                <tr><td>telephone :</td>
                    <td><input type="text" pattern=" ^(\+33|0)[0-9]{9}$" name="telephone"
                            <?php if(isset($_POST['telephone'])) echo "value=\"".$_POST['telephone']."\" "; // maintient de la valeur déjà fournie
                            else if(isset($infos['telephone'])) echo "value=\"".$infos['telephone']."\" ";  // ou affichage de la valeur enregistrée ?>
                        /> </td></tr>
                </tbody>
                <tfoot>
                <tr><td></td><td>
                        <input type="submit" value="Enregistrer" name="submit" />
                        <!-- bouton de rechargement de la page servant a annuler les modifications non enregistrés -->
                        <input type="button" onclick="window.location.href = 'Account.php';" value="Annuler"/>
                    </td></tr>
                </tfoot>
            </table>
        </form>
    </div>
</section>

<?php include '../General/Footer.php' ?>

</body>

