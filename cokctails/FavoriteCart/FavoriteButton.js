/**
 * ajoute ou retire une recette de la liste des favoris a travers ajax/jQuery
 * @param bouton le bouton qui appelle la fonction
 * @param recette la recette a ajouter/retirer
 * @param ajouter true pour ajouter, false pour retirer
 */
function manageFavorites(bouton, recette, ajouter) {

    // formatage des parametres a envoyer au fichier php
    const param = new URLSearchParams({
        recette: recette,
        ajouter: ajouter,
    });

    // envoie des parametres au fichier php
    $.ajax({
        url : '../FavoriteCart/FavoriteButton.php',
        type : 'POST',
        data : param.toString(),
        dataType : 'html',

        // si l'appel au fichier php réussi, on met a jour le bouton qui a appelé la fonction
        success : function(code_html, statut){

            // mise a jour de l'aparence du bouton
            if(ajouter) {
                bouton.innerHTML = "Remove recipe to favorite";
                bouton.className = "button";
            } else {
                bouton.innerHTML = "Add recipe to favorite";
                bouton.className = "button special";
            }

            // mise a jour de l'effet du bouton
            bouton.onclick = function () {
                // le nouvel effet est un appel a la meme fonction, mais en inversant la valeur de ajouter
                manageFavorites(bouton,recette,!ajouter);
            }
        },

        // si l'appel au fichier echoue, le texte du bouton l'indique a l'utilisateur
        error : function(resultat, statut, erreur){
            bouton.innerHTML = 'Erreur';
        }

    });
}