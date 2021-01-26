<?php
$img = filter_input(INPUT_POST,'lienImg', FILTER_SANITIZE_STRING);
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
        <textarea placeholder="Ajouter un commentaire" style="resize:none;" rows="5" cols="55"></textarea><br>
        <label>Choisir une image</label>
        <input type="file" multiple id="takeImage" accept="image/*" name="lienImg"><br>
        <input type="submit" value="Envoyer" name="envoie">
    </form>

</body>
</html>