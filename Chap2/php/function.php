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






function addMedia($branche, $date, $note, $remarque, $coefficient)
{
  static $ps = null;
  $sql = "INSERT INTO `db_m152`.`postes` (`branche`, `date`, `note`, `remarque`, `coefficient`) ";
  $sql .= "VALUES (:BRANCHE, :DATE, round(:NOTE,2), :REMARQUE, :COEFFICIENT)";
  if ($ps == null) {
    $ps = db_m152()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':BRANCHE', $branche, PDO::PARAM_STR);
    $ps->bindParam(':DATE', $date, PDO::PARAM_STR);
    $ps->bindParam(':NOTE', $note, PDO::PARAM_STR);
    $ps->bindParam(':REMARQUE', $remarque, PDO::PARAM_STR);
    $ps->bindParam(':COEFFICIENT', $coefficient, PDO::PARAM_INT);

    $answer = $ps->execute();
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}