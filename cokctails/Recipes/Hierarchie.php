<?php include_once '../General/LoginManager.php';
include_once('../Donnees.inc.php');
include_once('./Functions.php');


/**
 * Cette parcelle de code permet de dire que par défaut on se situe
 * à la base de l'arborescence.
 */
if(!isset($_GET['arborescence'])) {
    $_GET['arborescence'] = '|Aliment';
}



/**
 * Cette fonction permet de générer le code HTML du formulaire
 * qui va permettre d'explorer une sous-catégorie de la catégorie courante.
 */
function creationMenu()
{
    global $Hierarchie;

    /*
     * On récupère le nom de l'aliment courant, en l'extrayant de la chaine de
     * caractères représentant l'arborescence qui a été passée en GET
     * Exemple de chaine de caractères "Aliment,Fruit,Fruit Sec".
     * La chaine de caractères représente en fait une liste qu'on a concaténée
     * pour la passer en paramètres.
     */
    $dernier_aliment = strrchr($_GET['arborescence'],"|");
    // derniere occurence de '|'
    $dernier_aliment = substr($dernier_aliment,1);


    //$dernier_aliment = str_replace(' ','_',$dernier_aliment);

    /*
     * On vérifie d'abord qu'il existe bien une sous-catégorie à l'aliment courant.
     * Si ce n'est pas le cas, il n'y pas besoin de créer de formulaire pour s
     * électioner une sous-catégorie
     */
    if(isset($Hierarchie[$dernier_aliment]['sous-categorie'])) {

        /*
         * On génère le code HTML du formulaire pour sélectionner une sous-catégorie.
         * On utilise la méthode GET et on est renvoyé sur la même page "Search.php".
         * Le formulaire contient simplement un <select> par sous-catégorie.
         */
        echo '<form name=formulaire method=get action=Hierarchie.php>
                    <ul class=actions>
                        <li>
                            <select id=arborescence name=arborescence style=\"border-radius: 2.5em;height: 2.75em\">';

        /*
         * On créé une <option> par sous-catégorie de l'aliment courant.
         * Comme expliqué précédemment, la méthode GET permet de renvoyer la valeur
         * d' "arborescence", qui représente la liste d'aliments déjà explorés depuis la racine
         * sous la forme de mots séparés par une virgule.
         * Ainsi, pour générer la nouvelle valeur d'aborescence en fonction de l'aliment sélectionné,
         * on concatène l'ancienne valeur d'arborescence et on lui ajoute ",[nom de l'aliment]"
         */
        foreach ($Hierarchie[$dernier_aliment]['sous-categorie'] as $key_value=>$value)

            echo "              <option value =\"{$_GET['arborescence']}|{$value}\" style=\"color:black;\"'>{$value}</option>";


        /*
         * Fin du code du formuaire
         */
        echo'                </select>
                        </li>
                        <li>
                            <input type=submit value=Confirm />
                        </li>
                    </ul>
            </form>';
    }
}

/**
 * Cette fonction permet de générer le code HTML du chemin qui mène à l'aliment courant
 * "Aliment > Fruit > ..."
 */
function creationChemin(){
    /*
     * On génère un array en séparant la chaine arborescence, en prenant pour séparateur la virgule
     * La liste d'aliments récupérés permet de générer une string qui représente le chemin
     * Par exemple, ",Aliment,Fruit,Fruit sec" devient "Aliment > Fruit > Fruit Sec".
     * Chaque aliment est associé à une URL qui permet de "remonter le chemin". Par exemple
     * en cliquant sur "Fruit" on arrive sur la page d'arborescence ",Aliment,Fruit".
     */
    $liste_aliments = explode("|",$_GET['arborescence']);
    foreach($liste_aliments as $value){
        if(!empty($value))
        echo "<a href='".getUrlAliment($value)."'>".$value."</a> > ";
    }
}

/**
 * Cette méthode permet de générer l'URL qui permet de remonter le chemin d'exploration
 * Le fonctionnement est le suivant : on ne modifie pas "$_GET['arborescence']" directement,
 * mais on génère "manuellement" l'URL qui permet d'associer à arborescence la bonne valeur.
 * L'URL est ensuite mise en href du texte correspondant.
 * @param $nom_aliment : le nom de l'aliment
 * @return string l'URL qui permet de "remonter" le chemin jusqu'à l'aliment de catégorie
 * supérieure sélectionné.
 */
function getUrlAliment($nom_aliment){

    /**
     * On extrait le bout du chemin qui s'arrête avant la premire occurence de l'aliment sélectionné.
     */
    $debut_arborescence = substr($_GET['arborescence'],1,strpos($_GET['arborescence'],"|".$nom_aliment));
    /**
     * On ajoute l'aliment sélectionné au chemin (car substr n'inclut pas l'aliment sélectionné).
     */
    $arborescence = $debut_arborescence."|".$nom_aliment;

    /**
     * On encode la nouvelle valeur de l'arborescence au format url
     */
    $url  = urlencode($arborescence);

    /**
     * On renvoie l'extrait d'url qui permet de revenir sur la page avec la nouvelle valeur
     * de l'arborescence.
     */
    return "Hierarchie.php?arborescence=".$url;
}

/**
 * Cette fonction gère l'affichage des recettes comprenant l'aliment courant ou un aliment
 * d'une sous catégorie de l'aliment courant.
 */
function afficherRecette()
{
    global $Recettes;
    /**
     * On récupère le dernier aliment, qui correspond aux caractères situés derrière
     * la dernière virgule
     */
    $dernier_aliment = strrchr($_GET['arborescence'], "|");
    /**
     * On enlève la virgule de la chaine de caractères extraite
     */
    $dernier_aliment = substr($dernier_aliment, 1);

    /**
     * Initialisation de la liste des clés des recettes à afficher
     */
    $liste_recettes = array();

    /**
     * On itère sur toutes les recettes, puis sur chaque ingrédient de la recette
     */
    foreach ($Recettes as $key => $value) {
        foreach ($value['index'] as $key_index => $aliment_recette) {

            /*
             * Si la clé de la recette n'est pas déjà dans la liste, et que l'ingrédient
             * de la recette est le dernier aliment ou une de ses sous-catégories (directes
             * ou indirectes, sous-sous-catégorie par exemple...), alors on l'ajoute à la
             * liste des recettes et on génère le code HTML de sa fiche.
             */
            if (!in_array($key, $liste_recettes, true)) {
                if (estDansLesAliments($dernier_aliment, $aliment_recette)) {
                    $liste_recettes[] = $key;
                    echo genererFicheRecette($value,null);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Explorer les recettes | Cokctails</title>

    <?php include '../General/Head.html'?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="../FavoriteCart/FavoriteButton.js"></script>
</head>

<body>

<?php include '../General/Header.php' ?>

<!-- Cette première section contient le chemin à l'aliment courant ainsi que
     le formulaire qui permet de sélectionner une sous-catégorie à explorer-->
<section id="explorer" class="wrapper style2 special">
    <p>
        <?php creationChemin(); ?>
    </p>
    <?php creationMenu(); ?>
</section>

<!-- Cette seconde section contient la liste des fiches recettes correspondant
     à l'aliment courant. Ces fiches correspondent à des articles en balises HTML -->
<section id="resultats" class="wrapper style1">

    <!-- La classe inner permet de fournir le style "fiche" pour chacun
         des articles contenus-->
    <div class="inner">
    <?php afficherRecette(); ?>
    </div>
</section>

<?php include '../General/Footer.php' ?>

</body>

</html>