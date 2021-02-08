<?php
require_once "php/function.php";

function affichePost()
{
    $msg = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);
    $destination = "/local/stockage";
    addPost($msg);
    $id = retournId();
    $arr = $_FILES["lienImg"]["name"];
    for ($i=0; $i < sizeof($arr); $i++) { 
        addMedia($_FILES["lienImg"]["type"][$i], $_FILES["lienImg"]["name"][$i], $id[0]["idPost"]);
        move_uploaded_file($_FILES["lienImg"]["tmp_name"][$i], $destination.$i.".txt");
    }

}

if (isset($_POST['envoie']))
{
    affichePost();
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