<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';

$fields_array = json_decode($_GET['fields_array']);
$values_array_all = json_decode($_GET['values_array']);
$tableSize = $_GET['tableSize'];

$columns = "";
for ($i = 0; $i < $tableSize; $i++) {
   $columns .= escapehtml($fields_array[$i]);
   if ($i+1 < $tableSize) {
      $columns .= ", ";
   }
}

for ($j = 0; $j < count($values_array_all); $j++) {
   $values_array = $values_array_all[$j];
   $values = "";
   for ($i = 0; $i < $tableSize; $i++) {
      $values .= "'".$values_array[$i]."'";
      if ($i+1 < $tableSize) {
         $values .= ", ";
      }
   }

   $sql = "INSERT INTO distribution (".$columns.") VALUES (".$values.")";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->execute();
   } catch (PDOException $p) {
      echo "<script>alert(\"Could not insert distribution record".$p->getMessage()."\");</script>";
      die();
   }
}

?>
