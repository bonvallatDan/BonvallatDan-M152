<?php
require_once "php/function.php";


for ($i=30; $i < 60; $i++) { 
    deleteNote($i);
}


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
        verifType($i, $destination, $id);
        //on verifie le type de chaque image
    }
}



function verifType($i, $destination, $id)
{
    $type = "image";
    $pos = strpos($_FILES["lienImg"]["type"][$i], $type);
    //Verifie si le type commence par image
    if ($pos === false) {
        return;
    } else {
        verifSize($i, $destination, $id);
    }
}

function verifSize($i, $destination, $id)
{
    $sizeImgMaxTot = 70000000;
    $sizeImgTot = 0;
    $sizeImgMax = 3000000;

    for ($i = 0; $i < sizeof($_FILES["lienImg"]["name"]); $i++) {
        if ($_FILES["lienImg"]["size"][$i] < $sizeImgMax) {
            $sizeImgTot += $_FILES["lienImg"]["size"][$i];
            //verifie que l'image est plus petite que 3'000'000
            //si image plus petite alors stock la taille dans variable
        } else {
            break;
        }
    }
    if ($sizeImgTot > $sizeImgMaxTot) {
        return;
    } else {
        for ($i = 0; $i < sizeof($_FILES["lienImg"]["name"]); $i++) {
            move_uploaded_file($_FILES["lienImg"]["tmp_name"][$i], $destination . genererChaineAleatoire());
            //Les images selectionnez sont envoyées dans un dossier local ou un fichier unique est crée

            addMedia($_FILES["lienImg"]["type"][$i], genererChaineAleatoire(), $id[0]["idPost"]);
            //Pour chaque images, on envoie les données dans la base de donnée
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
        //On ajoute un caractère tiré aléatoirement dans le tableau au nom du fichier
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