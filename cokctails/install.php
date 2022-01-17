<?php
/**
 * Création de la DB
 */


function query($link,$requete)
{
    $resultat=mysqli_query($link,$requete) or die("$requete : ".mysqli_error($link));
    return($resultat);
}


$mysqli=mysqli_connect('localhost', 'root', '') or die("Erreur de connexion");
$base="Utilisateurs";
$Sql="
		DROP DATABASE IF EXISTS $base;
		CREATE DATABASE $base;
		USE $base;
		CREATE TABLE Utilisateurs (
            login VARCHAR(30) PRIMARY KEY, 
            mdp VARCHAR(60) NOT NULL, 
            nom VARCHAR(30) ,
            prenom VARCHAR(30) , 
            sexe BOOL, 
            email VARCHAR(100), 
            naissance DATE, 
            adresse VARCHAR(100), 
            code_postal VARCHAR(5),
            ville VARCHAR(100) , 
            telephone VARCHAR(15) 
		);
		
		CREATE TABLE Panier (
		    login VARCHAR(30) NOT NULL, 
		    boisson VARCHAR(100) NOT NULL, 
		    FOREIGN KEY (login) REFERENCES Utilisateurs(login)
		)";

foreach(explode(';',$Sql) as $Requete) query($mysqli,$Requete);

mysqli_close($mysqli);