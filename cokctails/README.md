# Web2 : projet site-vitrine de cocktails

## Local Usage
lancer la stack LAMP pour les distributions linux, Wamp pour Windows 10 ou Mamp pour Mac OS
- ```sudo /opt/lampp/lampp start ```
cloner le projet en local sous le folder {your-path-to-xampp}/xampp/htdocs/
- exécuter le fichier de la DB (configuration & installation base de données)
- ```firefox http://localhost/cokctails/install.php```
- lancer la page d'accueil
- ```firefox http://localhost/cokctails/index.php``` or directly
```firefox http://localhost/cokctails/General/Accueil.php```

## Mise en ligne / free hosting 000webhost.com
available at :
https://romainduyc.000webhostapp.com
https://romainduyc.000webhostapp.com/General/Accueil.php

## Généralités
website referencing cocktail recipes, in which we can sign up, save favorites recipes, or search a specific one.



### Accès hiérarchique aux recettes à partir de la hiérarchie des aliments
Il doit être possible de naviguer dans la hiérarchie des aliments et de visualiser les recettes utilisant
un aliment en tant qu’ingrédient. La navigation consiste à sélectionner des éléments de plus en plus
précis (par exemple : fruit -> agrume-> orange).
Les recettes présentées seront celles utilisant l’aliment sélectionné (par exemple : recettes avec
fruit(s), recettes avec agrume(s), recettes avec orange(s)). Il est également demandé d’afficher le
chemin menant à l’aliment courant (depuis la rubrique de plus haut niveau en passant par les
rubriques intermédiaires).
L’affichage d’une recette devra être agrémenté de sa photo correspondante, si elle existe.
### Panier (de recettes) ou « Mes recettes préférées »
A l’instar des sites de commerces électroniques avec panier (de produits), l’utilisateur devra
pouvoir sélectionner les recettes qu’il apprécie pour les mettre dans un panier (de recettes !), qui
peut être vu comme l’ensemble des recettes préférées de l’utilisateur. Cet ensemble de recettes :
- est initialement vide quand l’utilisateur ne s’est pas encore identifié ;
- augmente quand l’utilisateur sélectionne des recettes (fonctionnalité : « ajouter cette
recette à mes recettes préférées »)
- diminue lorsque l’utilisateur supprime une recette de ses recettes préférées (fonctionnalité
« supprimer cette recette de mes recettes préférées ») ;
- est complété par les recettes préférées « déjà connues » de l’utilisateur quand celui-ci se
connecte.
L’ensemble des recettes préférées d’un utilisateur doit être stocké de façon durable si l’utilisateur
est identifié (pour qu’il puisse les consulter ultérieurement).
Un lien « Mes recettes préférés » dans l’interface doit permettre à tout utilisateur d’accéder à ses
recettes préférées.
### Identification et données utilisateur
Un utilisateur doit pouvoir se connecter à l’application à n’importe quel moment (pas forcément
avant la consultation/sélection des recettes) ; cette connexion n’est pas obligatoire.
Si l’utilisateur n’est pas connecté, les recettes sélectionnées ne seront pas stockées durablement.
La connexion nécessite la saisie des données personnelles suivantes : login, mot de passe, nom,
prénom, sexe (homme ou femme), adresse électronique, date de naissance, adresse postale
(décomposée en adresse, code postal et ville) et numéro de téléphone ; seuls le login et le mot de
passe sont obligatoires.
Une fois les données personnelles saisies, l’utilisateur pourra s’identifier par « login/mot de
passe » pour ré-accéder ultérieurement à l’application. Il devra également pouvoir modifier ses
données personnelles à tout moment. Le login sera obligatoirement unique.
### Interface de recherche de recettes
Une fonctionnalité devra permettre aux utilisateurs de rechercher des recettes à partir d’un
ensemble d’aliments qu’il souhaite utiliser (e.g. « jus de tomate » et « sel ») et d’un ensemble
d’aliments qu’il ne souhaite pas utiliser (e.g. « pas de whisky »).
Traiter ce type de recherche nécessitera :
- de mettre en place une interface la plus conviviale et la plus efficace possible. L’utilisation
de Javascript et/ou AJAX est souhaitée pour guider l’utilisateur dans la saisie des
ingrédients.
- Si l’utilisateur commence à saisir « Ju » , lui proposer en liste déroulante les complétions
Éléments à rendre
- de prendre en compte la hiérarchie des aliments : par exemple, une recherche sur « Jus de
fruit » devra retourner des recettes contenant du « Jus de pommes », du « Jus d’orange »,
etc.
- de traiter une recherche approximative (recettes qui ne satisfont pas toutes les contraintes
de l’utilisateur) et de classer/ordonner les recettes, en leur attribuant un score de
satisfaction (par exemple en fonction du maximum de critères satisfaits, du recouvrement
entre les aliments souhaités et ceux nécessaires à la préparation, etc.)
- de présenter les résultats de la façon la plus claire possible à l’utilisateur.



## used tools
- lamp (XAMPP for Linux 7.4.24-2)
- PHP 7 / HTML 5 / Js / CSS
- MySQL -phpmyadmin-
- Ajax JQuery for add-favorite button & rm button listFavoriteRecipes

Xampp and Lamp are the two technologies available that are used as open-source platforms that are used for providing the environment to perform coding and test the applications on the local machine

## arborescence folders
- cokctails :
  - assets :
    - css :
      - images :
        - ... 
      - font-awesome.min.css
      - ie9.css
      - main.css
    - fonts :
      - ...
  - Recipes : 
    - Functions.php
    - Search.php
    - SearchResults.php
    - Hierarchie.php
  - User : 
    - Signup.php
    - Login.php
    - Account.php
  - General : 
    - Head.html
    - Header.php
    - Accueil.php
    - Footer.php
    - LoginManager.php
  - FavoriteCart : 
    - General :
        - Head.html
        - Header.php
        - Accueil.php
        - Footer.php
        - LoginManager.php
    - MyFavRecipes.php
    - FavoriteButton.js
    - FavoriteButton.php
  - Images : 
    - Accueil.jpg
    - logo.png
  - Photos : 
    - ...
  - Donnees.inc.php
  - index.php
  - install.php