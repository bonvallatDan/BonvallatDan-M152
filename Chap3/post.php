<?php
require_once "php/function.php";


if (isset($_POST['envoie'])) {
    insertPost();
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
        <a href="index.php">Page d'accueil</a>
    </p>
    <form method="POST" enctype="multipart/form-data" action="#">
        <textarea placeholder="Ajouter un commentaire" style="resize:none;" rows="5" cols="55" name="commentaire" required></textarea><br>
        <label>Choisir une image</label>
        <input type="file" multiple id="takeImage" accept="image/*" name="lienImg[]"><br>
        <input type="submit" value="Envoyer" name="envoie">
    </form>

</body>

</html>