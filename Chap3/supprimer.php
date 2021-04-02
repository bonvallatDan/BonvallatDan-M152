<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
    include_once('php/function.php');
        $idPost = $_GET["idPost"];

        startTansaction();
        if(deleteMedia($idPost))
        {
            if(deletePost($idPost))
            {
                commit();
                header("Location: index.php");
            }
            else
            {
                stopTransaction();
                header("Location: index.php");
            }
        }
        else
        {
            stopTransaction();
            header("Location: index.php");
        }
        if(deletePost($idPost))
        {
            commit();
            header("Location: index.php");
        }
        else
        {
            stopTransaction();
            header("Location: index.php");
        }
        
    ?>
</body>
</html>