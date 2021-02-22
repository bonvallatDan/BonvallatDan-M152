<?php
require_once "php/function.php";





function affichePost()
{
    $type = "image";
    $msg = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);
    $destination = "./local/stockage";
    addPost($msg);
    //On envoie le message dans la BD

    $id = retournId();
    //On stock l'id du message dans une variable

    $arr = $_FILES["lienImg"]["name"];
    for ($i = 0; $i < sizeof($arr); $i++) {
        $pos = strpos($_FILES["lienImg"]["type"][$i], $type);
        if ($pos === false)
        {
            break;
        }
        else
        {
            addMedia($_FILES["lienImg"]["type"][$i], $_FILES["lienImg"]["name"][$i], $id[0]["idPost"]);
            //Pour chaque images, on envoie les données dans la base de donnée

            move_uploaded_file($_FILES["lienImg"]["tmp_name"][$i], $destination . genererChaineAleatoire());
            //Les images selectionnez sont envoyées dans un dossier local ou un fichier unique est crée
        }
    }
}

function genererChaineAleatoire($longueur = 5)
{
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $longueurMax = strlen($caracteres);
    $chaineAleatoire = '';
    for ($i = 0; $i < $longueur; $i++) {
        $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
    }
    return $chaineAleatoire;
}



if (isset($_POST['envoie'])) {
    affichePost();
    //Lorsqu'on appuie sur le bouton "envoyer"
    //On applique la fonction affichePost
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post</title>
</head>

<body>
    <p>
        <a href="index.php">Page d'acueille</a>
    </p>
    <form method="POST" enctype="multipart/form-data" action="#">
        <textarea placeholder="Ajouter un commentaire" style="resize:none;" rows="5" cols="55" name="commentaire"></textarea><br>
        <label>Choisir une image</label>
        <input type="file" multiple id="takeImage" accept="image/*" name="lienImg[]"><br>
        <input type="submit" value="Envoyer" name="envoie">
    </form>

</body>

</html>