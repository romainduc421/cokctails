<?php
include_once '../General/LoginManager.php';
include_once '../Donnees.inc.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rechercher une recette | Cocktails</title>

    <?php include '../General/Head.html'?>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
            integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer">

    </script>
    <script type="text/javascript" src="../FavoriteCart/FavoriteButton.js"></script>
    <script type="text/javascript" >

        /*
         * On créé la liste des aliments, qui sert à vérifier si un aliment saisi existe ou non
         */
        liste_aliments = [];
        <?php
        /**
         * impression du code JavaScript pour ajouter à la liste_aliments
         * la totalité des aliments du tableau php Hierarchie (on fournit le nom des aliments)
         */
        global $Hierarchie;
        foreach($Hierarchie as $key=>$value) {
            echo "liste_aliments.splice(liste_aliments.length,0,\"".$key."\");";
        }
        ?>

        /*
         * On créé les deux listes de contraintes (éléments à ajouter, éléments à exclure)
         */
        aliments_a_inclure = [];
        aliments_a_exclure = [];

        /**
         * Fonction qui permet de changer la valeur de l'entrée texte en fonction du paramètre
         * @param valeur : string, nouvelle valeur valeur de l'entrée texte
         */
        function choix(valeur){
            document.getElementById("contrainte").value = unescape(valeur);
            document.getElementById("prop").innerHTML ="";
        }

        function afficherSuggestions(){

            /*
             * On récupére la valeur de l'entrée texte
             */
            let saisie = document.getElementById("contrainte").value;
            let nbCarac = saisie.length;

            /*
             * On réinitialise la liste de propositions d'auto-complétion et on la cache
             */
            document.getElementById("prop").style.display = "none";
            document.getElementById("prop").innerHTML ="";

            /*
             * Si l'entrée texte est vide on ne fait rien.
             */
            if(nbCarac>0){
                let match = false;

                /*
                 * On itère sur chaque aliment pour voir si le début correspond au premiers caractères de l'aliment
                 */
                for(let i=0; i<liste_aliments.length; i++){
                    if(liste_aliments[i].length>nbCarac-1){

                        /*
                         * On ne prend pas en compte la casse dans la vérification.
                         */
                        if(!saisie.toLowerCase().localeCompare(liste_aliments[i].substr(0,nbCarac).toLowerCase())){
                            match = true;
                            /*
                             * Ajoute un composant <div> avec le nom de l'aliment à la liste de propositions d'auto-complétion
                             * Si on appuie sur ce div, on change la valeur de l'entrée texte avec l'aliment sélectionné
                             */
                            document.getElementById("prop").innerHTML += "<div style=\"margin : 10px 10px 10px;\" onClick='choix(\""+escape(liste_aliments[i])+"\")'>"+liste_aliments[i]+"</div>";
                        }
                    }
                    /*
                     * S'il y a eu au moins une proposition d'auto-complétion on affiche le composant
                     * de la liste des suggestions d'auto-complétion
                     */
                    if(match){
                        document.getElementById("prop").style.display = "block";
                    }

                }
            }

        }


        /**
         * Permer d'ajouter un aliment à la liste des aliments à inclure
         */
        function inclure(){
            /*
             * On récupère la valeur contenue dans l'entrée texte
             */
            aliment = document.getElementById("contrainte").value;

            /*
             * On vérifie que l'aliment n'est pas déjà dans la liste, et qu'il existe bien dans le
             * la liste des aliments.
             */
            if(liste_aliments.includes(aliment)&&(!aliments_a_inclure.includes(aliment))){

                /*
                 * Ajout de l'élément à la liste des aliments à exclure.
                 */
                aliments_a_inclure.splice(aliments_a_inclure.length,0,aliment);

                /*
                 * Récupération de l'indice
                 */
                let i = aliments_a_inclure.indexOf(aliment);
                /*
                 * Ajout de l'élément graphique associé, qui prend la forme d'un boutton
                 * Lorsqu'on appuie sur le boutton, on annule la contrainte
                 * L'id de l'élément est généré grâce à l'indice
                 */
                document.getElementById("liste_inclusions").innerHTML+=
                    "<li id=\"element_"+i+"_inclus\">" +
                        "<button  style=\"background-color:limegreen;color:white\" onclick=\"annulerInclusion("+i+")\">"+
                            aliment+"   X"+
                        "</button>"+
                    "</li>";
            }
        }

        /**
         * Permer d'ajouter un aliment à la liste des aliments à exclure
         */
        function exclure(){
            /*
             * On récupère la valeur contenue dans l'entrée texte
             */
            aliment = document.getElementById("contrainte").value;

            /*
             * On vérifie que l'aliment n'est pas déjà dans la liste, et qu'il existe bien dans le
             * la liste des aliments.
             */
            if(liste_aliments.includes(aliment)&&(!aliments_a_exclure.includes(aliment))){

                /*
                 * Ajout de l'élément à la liste des aliments à exclure.
                 */
                aliments_a_exclure.splice(aliments_a_exclure.length,0,aliment);

                /*
                * Récupération de l'indice
                */
                let i = aliments_a_exclure.indexOf(aliment);

                /*
                 * Ajout de l'élément graphique associé, qui prend la forme d'un boutton
                 * Lorsqu'on appuie sur le boutton, on annule la contrainte.
                 * L'id de l'élément est généré grâce à l'indice
                 */
                document.getElementById("liste_exclusions").innerHTML+=
                    "<li id=\"element_"+i+"_exclu\">" +
                        "<button  style=\"background-color:red;color:white\" onclick=\"annulerExclusion("+i+")\">"+
                            aliment+"   X"+
                        "</button>"+
                    "</li>";
            }
        }

        /**
         * Permet de retirer un aliment de la liste des aliments à inclure.
         * @param i : int indice dans la liste de l'élément à retirer.
         */
        function annulerInclusion(i){

            /*
             * On retire l'élément graphique associé
             */
            let child = document.getElementById("element_"+i+"_inclus");
            document.getElementById('liste_inclusions').removeChild(child);


            aliments_a_inclure.splice(i,1);
        }

        /**
         * Permet de retirer un aliment de la liste des aliments à exclure.
         * @param i : int indice dans la liste de l'élément à retirer.
         */
        function annulerExclusion(i){
            /*
             * On retire l'élément graphique associé
             */
            let child = document.getElementById("element_"+i+"_exclu");
            document.getElementById('liste_exclusions').removeChild(child);

            aliments_a_exclure.splice(i,1);
        }

        /**
         * Cette fonction permet de générer une URL permettant de transmettre par méthode get
         * la liste des aliments à inclure, la liste des aliments à exclur et le
         * pourcentage minimal de correspondance toléré.
         * Elle recharge ensuite la page avec ces paramètres fournies en URL, pour que
         * la page rechargée puisse récupérer ces paramètres comme un champ de $_GET.
         */
        function rechercher(){


            /*
             * On vérifie d'abord si au moins une des deux listes n'est pas vide
             */
            if(aliments_a_exclure.length!==0||aliments_a_inclure.length!==0) {

                /*
                 * On transforme les listes en chaine de caractères, avec les aliments
                 * séparés par le caractere '|'.
                 */
                let aliments_a_inclure_str = aliments_a_inclure.join("|");
                let aliments_a_exclure_str = aliments_a_exclure.join("|");


                /*
                 * On génère l'extrait 'URL qu'il faut utiliser pour transmettre les paramètres
                 * par la méthode POST à ResultatsRecherche.php
                 */
                const param = new URLSearchParams({
                    aliments_inclus: aliments_a_inclure_str,
                    aliments_exclus: aliments_a_exclure_str,
                    seuil : document.getElementById("seuil").value,
                });


                /*
                 * Affichage du chargement
                 */
                $('#resultats').empty();
                $('#resultats').append("<article class=\"feature\" style=\"background-color:transparent\">"+
                                            "<div class=\"content\" style=\"background-color:;text-align:center\">"+
                                                "<button>Chargement en cours...</button>"+
                                            "</div>"+
                                        "</article>");

                /*
                 * Les données sont envoyées à un fichier de traitement grâce à une fonction Ajax.
                 * Le fichier de traitement se charge de calculer le score de chaque recette, de les trier
                 * par score puis de générer le code HTML des fiches recettes
                 */
                $.ajax({
                    url : 'SearchResults.php',
                    type : 'POST',
                    data : param.toString(),
                    dataType : 'html',

                    success : function(code_html, statut){

                        $('#resultats').empty();

                        /*
                         * Les composants HTML générés sont ajoutés à la balise <div> censée les contenir
                         */
                        $(code_html).appendTo("#resultats"); // On passe code_html à jQuery() qui va nous créer l'arbre DOM !
                    },

                    error : function(resultat, statut, erreur){


                        /*
                        * Affichage en cas d'échec de la requête
                        */
                        $('#resultats').append("<article class=\"feature\">"+
                                                    "<div class=\"content\" style=\"text-align:center\">"+
                                                        "<h1>Il y a eu une erreur lors du chargement</h1>"+
                                                    "</div>"+
                                                "</article>");
                    }

                });

            } else {
                /*
                 * Si les deux listes sont vides on créé une alerte pour signaler
                 * qu'il faut au moins une contrainte
                 */
                alert("Vous devez ajouter ou exclure un aliment avant d'effectuer votre recherche.");
            }
        }
    </script>
</head>

<body>

<?php include '../General/Header.php' ?>

<!-- Cette première section contient tous les composants relatifs
     au moteur de recherche. Elle contient donc le formulaire pour ajouter/enlever
     des aliments, le récapitulatif des choix, et les boutons pour
     paramétrer le pourcentage minimal de correspondance exigé et
     lancer la recherche-->
<section id="rechercher" class="wrapper style2 special">
    <div class="inner">
        <article class="feature left" >
            <!-- Formulaire pour ajouter des contraintes d'inclusion ou d'exclusion.
                 Il contient une barre de recherche avec auto-complétion, et deux boutons
                 pour choisir si on ajoute où on enlève -->
            <div class="container 75%">
                <form>
                    <fieldset>
                        <!-- Pour éviter que la personne appuie sur entrée et recharge la page en perdant les contraintes,
                             qui ont été stockées en Javascript, on ajoute au moteur de recherche une fonction
                             javascript qui bloque les événements "appuyer sur la touche entrée"-->
                        <input type="text" id="contrainte" name="contrainte" placeholder="Saisissez le nom d'un aliment..." style="margin-top:2em" onkeyup="afficherSuggestions()" onkeypress="return event.keyCode !== 13;"  />

                        <!-- Ajout des deux boutons pour ajouter une contrainte (ajout d'un aliment,
                             exclusion d'un aliment)-->
                        <div id="prop" style="background-color: #EEEEEE;text-align: left;color: #07090c;position:absolute;display: none;"></div>
                        <ul class="actions" >
                            <li><input type="button" value="Ajouter" id="inclusion" onClick="inclure()"/></li>
                            <li><input type="button" value="Enlever" id="exclusion" onClick="exclure()"/></li>
                        </ul>
                    </fieldset>
                </form>
            </div>
        </article>

        <!-- Cet <article> contient la liste des contraintes, permet de choisir le
             pourcentage minimal de correspondance toléré et de lancer la recherche-->
        <article class="feature" style="background-color:honeydew;text-align:left">
            <div class="content" >

                <!-- Affichage des aliments à inclure dans la recette-->
                <h4 style="color:black;">Liste d'éléments à inclure dans la recette</h4>
                <ul class="actions" id="liste_inclusions"></ul>

                <!-- Affichage des aliments à exclure de la recette-->
                <h4  style="color:black;">Liste d'éléments à ne pas inclure dans la recette</h4>
                <ul class="actions" id="liste_exclusions"></ul>

                <h4 style="color:black;">Saisissez le pourcentage minimum de correspondance toléré et lancez la recherche</h4>
                <ul class="actions" >
                    <!-- Saisie du seuil de tolérance du pourcentage de correspondance
                         100 signifie que vous ne voulez que les recettes qui respectent
                         strictement les contraintes,
                         50 signifie que vous tolérez si seule une contrainte sur 2 est respectée, etc-->
                    <li><input type="number" value="100" id="seuil" name="seuil" min="1" max="100" style="border-radius: 2.5em;height: 2.85em;color:black;line-height: 2.95em;"></li>

                    <!-- Bouton pour lancer la recherche. Ne fonctionne pas si aucune contrainte n'a été saisie
                         et affiche une alerte-->
                    <li><input class="button special" type="submit" value="Nouvelle recherche" id="recherche" name="recherche" onclick="rechercher()" /></li>
                </ul>
            </div>
        </article>
    </div>
</section>

<!-- Cette seconde section contient la liste des fiches recettes correspondant
        à l'aliment courant. Ces fiches correspondent à des articles en balises HTML -->
<section class="wrapper style1">

    <!-- La classe inner permet de fournir le style "fiche" pour chacun
         des articles contenus-->
    <div  id="resultats" class="inner">
    </div>
</section>

<?php include '../General/Footer.php' ?>

</body>
</html>
