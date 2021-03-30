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
  $bool = false;
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
        header('Location: index.php');
        $bool = true;
      }
    } else {
      header('Location: index.php');
      $bool = false;
    }
  }
  return $bool;
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
  $tabType = array("image", "video", "audio");

  //Verifie si le type commence par image, video ou audio
  for ($i = 0; $i < count($tabType); $i++) {
    if (strpos($type[0], $tabType[$i]) === 0) {
      return true;
    } 
  }
  return false;
}

function verifSize($i, $destination, $id)
{
  $sizeImgMaxTot = 70000000;
  $sizeImgTot = 0;
  $sizeImgMax = 30000000;
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
  $tabType = ["image", "video", "audio"];
  $bool = false;
  $post = retournPost();
  foreach ($post as $onePost) {
    echo "<div class=row mb-2>
      <div class=col-md-6>
        <div class=row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative>
          <div class=col p-4 d-flex flex-column position-static>";
    $media = retournMedia($onePost["idPost"]);

    if ($media) {
      for ($i = 0; $i < count($media); $i++) {
        if (strpos($media[$i]["typeMedia"], $tabType[0]) === 0) 
        {
          echo "<img src=./local/stockage" . $media[$i]["nomMedia"] . " alt=ResponsiveImage class=img-thumbnail>";
        }
        else if (strpos($media[$i]["typeMedia"], $tabType[1]) === 0) 
        {
            echo "<video height=200 width=200 autoplay muted loop><source src=./local/stockage" . $media[$i]["nomMedia"] . "></video>";
        } 
        else if (strpos($media[$i]["typeMedia"], $tabType[2]) === 0) 
        {
            echo "<audio controls><source src=./local/stockage" . $media[$i]["nomMedia"] . "></audio>";
        } 
        else 
        {
          return $bool;
        }
      }
    }
    echo "<span>" . $onePost["comentaire"] . "</span>
          </div>
        </div>
      </div>
    </div>";
    $bool = true;
  }
  return $bool;
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


function startTansaction()
{
  db_m152()->beginTransaction();
}

function commit()
{
  db_m152()->commit();
}

function stopTransaction()
{
  db_m152()->rollBack();
}
