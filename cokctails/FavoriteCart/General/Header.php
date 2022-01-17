<!-- barre de menu commune a toute les pages -->
<header id="header" class="skel-layers-fixed">
    <!-- liens vers les différentes pages du site -->
    <h1><a href='../General/Accueil.php' style="font-family:'Monotype Corsiva',sans-serif;text-transform:none;font-size:30px">FavCocktail</a></h1>
    <a href='../../Recipes/Hierarchie.php'>Explore recipes</a>
    <a href='../../Recipes/Search.php'>Search a recipe</a>
    <a href='../../FavoriteCart/MyFavRecipes.php'>My favorites'</a>
    <?php
    /**
     * liens différents selon que l'utilisateur soit log-in ou non
     */
    if(isset($_SESSION['utilisateur'])) echo
        "<a href='../../User/Account.php'>My account</a>" .
        "<a href='?deconnexion=true'>Log out</a>";
    else echo
        "<a href='../../User/Signup.php'>Sign up</a>" .
        "<a href='../../User/Login.php'>Login</a>";
    ?>
</header>