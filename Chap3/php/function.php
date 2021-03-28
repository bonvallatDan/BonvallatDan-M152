<?php
require_once "database.php";
session_start();

function addPost($commentaire)
{
  static $ps = null;
  $sql = "INSERT INTO `db_m152`.`postes` (`comentaire`) ";
  $sql .= "VALUES (:COMMENTAIRE)";
  if ($ps == null) {
    $ps = db_m152()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':COMMENTAIRE', $commentaire, PDO::PARAM_STR);

    $answer = $ps->execute();
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}
//Le commentaire, le type de média, le nom du fichier ainsi que la date à laquelle il a été inséré (date automatique).




function retournId()
{
  static $ps = null;
  $sql = 'SELECT `idPost` FROM `db_m152`.`postes` ORDER BY `idPost` DESC LIMIT 1';

  if ($ps == null) {
    $ps = db_m152()->prepare($sql);
  }
  $answer = false;
  try {

    if ($ps->execute())
      $answer = $ps->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }

  return $answer;
}


function retournPost()
{
  static $ps = null;
  $sql = 'SELECT idPost, comentaire, creationDate, modificationDate FROM postes';

  if ($ps == null) {
    $ps = db_m152()->prepare($sql);
  }
  $answer = false;
  try {

    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }

  return $answer;
}

function retournMedia($idPost)
{
  static $ps = null;
  $sql = 'SELECT postes.idPost, medias.idMedia, medias.typeMedia, medias.nomMedia, medias.creationDate FROM postes INNER JOIN medias ON postes.idPost = medias.idPost WHERE postes.idPost = :IDPOST';

  if ($ps == null) {
    $ps = db_m152()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':IDPOST', $idPost, PDO::PARAM_INT);
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }

  return $answer;
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

function insertPost()
{
  $msg = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);
  $destination = "./local/stockage";
  addPost($msg);
  //On envoie le message dans la BD

  $idPost = retournId();
  //On stock l'id du message dans une variable

  $arr = $_FILES["lienImg"]["name"];
  for ($i = 0; $i < sizeof($arr); $i++) {
    $type = $_FILES["lienImg"]["type"];
    if (verifType($type)) {
      //on verifie le type de chaque image
      if (verifSize($i, $destination, $idPost)) {
        break;
      } else {
        $reucpNomImg = recupNomImg($idPost);
        header('Location: index.php');
      }
    } else {
      $reucpNomImg = recupNomImg($idPost);
      header('Location: index.php');
    }
  }
}


function recupNomImg($idPost)
{
  static $ps = null;
  $sql = 'SELECT nomMedia FROM db_m152.medias';
  $sql .= ' WHERE idPost = :IDPOST';

  if ($ps == null) {
    $ps = db_m152()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':IDPOST', $idPost, PDO::PARAM_INT);

    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }

  return $answer;
}

function addMedia($typeMedia, $nomMedia, $idPost)
{
  static $ps = null;
  $sql = "INSERT INTO `db_m152`.`medias` (`typeMedia`, `nomMedia`, `idPost`) ";
  $sql .= "VALUES (:TYPE_MEDIA, :NOM_MEDIA, :ID_POST)";
  if ($ps == null) {
    $ps = db_m152()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':TYPE_MEDIA', $typeMedia, PDO::PARAM_STR);
    $ps->bindParam(':NOM_MEDIA', $nomMedia, PDO::PARAM_STR);
    $ps->bindParam(':ID_POST', $idPost, PDO::PARAM_INT);

    $answer = $ps->execute();
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}




function verifType($type)
{
  $i = ["image", "video", "audio"];
  switch ($i) {
    case 'image':
      $pos = strpos($_FILES["lienImg"]["type"][$i], $i[0]);
      break;
    case "video":
      $pos = strpos($_FILES["lienImg"]["type"][$i], $i[1]);
      break;
    case "audio":
      $pos = strpos($_FILES["lienImg"]["type"][$i], $i[2]);
      break;
  }

  //Verifie si le type commence par image
  if ($pos === false) {
    return false;
  } else {
    return true;
  }
}

function verifSize($i, $destination, $id)
{
  $sizeImgMaxTot = 70000000;
  $sizeImgTot = 0;
  $sizeImgMax = 3000000;
  $extension = pathinfo($_FILES["lienImg"]["name"][$i]);

  if ($_FILES["lienImg"]["size"][$i] < $sizeImgMax) {
    $sizeImgTot += $_FILES["lienImg"]["size"][$i];
    //verifie que l'image est plus petite que 3'000'000
    //si image plus petite alors stock la taille dans variable
  } else {
    return;
  }
  if ($sizeImgTot > $sizeImgMaxTot) {
    return;
  } else {
    $image = genererChaineAleatoire() . $extension["dirname"] . $extension["extension"];
    if (move_uploaded_file($_FILES["lienImg"]["tmp_name"][$i], $destination . $image)) {
      addMedia($_FILES["lienImg"]["type"][$i], $image, $id["idPost"]);
    }


    //Les images selectionnez sont envoyées dans un dossier local ou un fichier unique est crée

    //Pour chaque images, on envoie les données dans la base de donnée
  }
}


function displayPost()
{
  $post = retournPost();
  foreach ($post as $onePost) {
    echo "<div class=row mb-2>
      <div class=col-md-6>
        <div class=row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative>
          <div class=col p-4 d-flex flex-column position-static>";
    $media = retournMedia($onePost["idPost"]);
    if ($media) {
      for ($i = 0; $i < count($media); $i++) {
      
        echo "<img src=./local/stockage" . $media[$i]["nomMedia"] . " alt=ResponsiveImage class=img-thumbnail>";
      }
    }
    echo "<span>" . $onePost["comentaire"] . "</span>
          </div>
        </div>
      </div>
    </div>";
  }

  /**<div class="row mb-2">
      <div class="col-md-6">
        <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
          <div class="col p-4 d-flex flex-column position-static">
            <img src="./local/stockage1VV8w.jpg" alt="bob" class="img-thumbnail">
            <span>Yo les tulipes c'est diabloX9</span>
          </div>
        </div>
      </div>
    </div>*/
}


function recupMedia($idPost)
{
  static $ps = null;
  $sql = 'SELECT idMedia, typeMedia, nomMedia, creationDate, idPost FROM db_m152.medias';
  $sql .= ' WHERE idPost = :IDPOST';

  if ($ps == null) {
    $ps = db_m152()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':IDPOST', $idPost['idPost'], PDO::PARAM_INT);

    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }

  return $answer;
}

function recupPost($idPost)
{
  static $ps = null;
  $sql = 'SELECT idPost, comentaire, creationDate, modificationDate FROM db_m152.postes';
  $sql .= ' WHERE idPost = :IDPOST';

  if ($ps == null) {
    $ps = db_m152()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':IDPOST', $idPost['idPost'], PDO::PARAM_INT);

    if ($ps->execute())
      $answer = $ps->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }

  return $answer;
}
