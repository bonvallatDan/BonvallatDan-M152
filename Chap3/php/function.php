<?php
require_once "database.php";

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
  $type = "image";
  $msg = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);
  $destination = "./local/stockage";
  addPost($msg);
  //On envoie le message dans la BD

  $idPost = retournId();
  //On stock l'id du message dans une variable

  $arr = $_FILES["lienImg"]["name"];
  for ($i = 0; $i < sizeof($arr); $i++) {
    if (verifType($i)) {
      //on verifie le type de chaque image
      if (verifSize($i, $destination, $idPost)) {
        break;
      } else {
        $reucpNomImg = recupNomImg($idPost);
        header('Location: index.php');
      }
    } else {
      break;
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




function verifType($i)
{
  $type = "image";
  $pos = strpos($_FILES["lienImg"]["type"][$i], $type);
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
    move_uploaded_file($_FILES["lienImg"]["tmp_name"][$i], $destination . genererChaineAleatoire() . $extension["dirname"] . $extension["extension"]);
    //Les images selectionnez sont envoyées dans un dossier local ou un fichier unique est crée

    addMedia($_FILES["lienImg"]["type"][$i], genererChaineAleatoire() . $extension["dirname"] . $extension["extension"], $id["idPost"]);
    //Pour chaque images, on envoie les données dans la base de donnée
  }
}


function displayPost()
{
  
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