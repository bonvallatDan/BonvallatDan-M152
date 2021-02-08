<?php
require_once "database.php";

function addPost($commentaire)
{
  static $ps = null;
  $sql = "INSERT INTO `db_m152`.`postes` (`commentaire`) ";
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
      $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }

  return $answer;
}






function addMedia($typeMedia, $nomMedia, $idPost)
{
  static $ps = null;
  $sql = "INSERT INTO `db_m152`.`postes` (`typeMedia`, `nomMedia`, `idPost`) ";
  $sql .= "VALUES (:TYPE_MEDIA, :NOM_MEDIA, :ID_POST)";
  if ($ps == null) {
    $ps = db_m152()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':TYPE_MEDIA', $typeMedia, PDO::PARAM_STR);
    $ps->bindParam(':NOM_MEDIA', $nomMedia, PDO::PARAM_STR);
    $ps->bindParam(':ID_POST', $idPost, PDO::PARAM_STR);

    $answer = $ps->execute();
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}