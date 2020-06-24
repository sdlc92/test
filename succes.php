<?php

session_start();

$mysqli = mysqli_connect("", "baptiste", "", "test_police");
    

if(!$mysqli){ // Test connexion à la BD
    echo "Erreur de connexion à la base de données.";
}
else{
    // Si le formulaire est celui de connexion
    if($_POST["connexion"]){
        // les champs sont bien posté et pas vide, on sécurise les données entrées
        $username = htmlentities($_POST['username'], ENT_QUOTES, "UTF-8"); 
        $password = htmlentities($_POST['password'], ENT_QUOTES, "UTF-8");

        // on fait maintenant la requête dans la base de données pour rechercher si l'utilisateur existe
        $Requete = mysqli_query($mysqli,"SELECT * FROM UTILISATEUR WHERE username = '". $username ."'"); // Champs username unique

        // test username existe
        if(mysqli_num_rows($Requete) == 0) {
            echo('Aucun utilisateur ne possède cet email');
        } else {
            // On récupère le mot de passe hashé pour vérifier que le mdp entré est le bon.
            echo($Requete);
            $res = mysqli_result($Requete);
            echo($res);
            $PasswordBD = $res[2];

            // Si la codition est vrai alors les mdp sont identiques
            if (password_verify($password, $PasswordBD)) {
                // on ouvre la session avec $_SESSION:
                $_SESSION["username"] = $_POST["username"];
                echo($_SESSION["username"]);
            } else {
                echo('Les mots de passes ne correspondes pas');
                
            }        

        }
        
    }


    else{
        // le htmlentities() passera les guillemets en entités HTML, ce qui empêchera les injections SQL
        $username = htmlentities($_POST['username'], ENT_QUOTES, "UTF-8"); ;
        $email = htmlentities($_POST['email'], ENT_QUOTES, "UTF-8"); 
        $password = htmlentities($_POST['password'], ENT_QUOTES, "UTF-8");

        // On chiffre le mdp en bcrypt
        $password = password_hash($MotDePasse, PASSWORD_BCRYPT);

        // On verifie que le mail n'est pas déjà utilisé
        $Requete = mysqli_query($mysqli,"SELECT * FROM UTILISATEUR WHERE email = '". $email ."'");

        // Si la requete ne possede aucune row, il n'y a pas d'utilisateur qui possède cet email
        if(mysqli_num_rows($Requete) == 0) {

            // On verifie que le nom d'utilisateur est unique (il servira d'identifiant)
            $Requete = mysqli_query($mysqli,"SELECT * FROM UTILISATEUR WHERE AND username = '". $username ."'");

            // Si la requete ne possede aucune row, il n'y a pas d'utilisateur qui possède ce username, on peut donc créer l'utilisateur
            if(mysqli_num_rows($Requete) == 0) {
                // On créé la requete d'insertion dans la bdd
                $query = "INSERT INTO UTILISATEUR (email, username, password) values ('". $email ."' , '". $username ."' , '". $password ."')";
                // On lance la requete dans la bdd
                $mysqli->query($query);
                // Affichage du succès
                echo("L'utilisateur à bien été ajouté");
            }
        } 
        else {
            echo("Cet utilisateur existe déjà");
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Connexion</title>
</head>
<body>
    <header>
        <h1>Success</h1>
    </header>

    <form action="/succes.php" method="post">
        <input name="connexion" type="text" hidden value="connexion">
        <input id="username" name="username" type="text" required>
        <label for="username">Username</label>
        <input type="password" name="password" id="password" required>
        <label for="password">Password</label>
        <input type="submit">
    </form>
</body>
</html>