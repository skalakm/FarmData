<?php
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';

$crop = escapehtml($_GET['crop']);
$fld = escapehtml($_GET['fieldID']);

$sql = "select gen from (SELECT gen from dir_planted 
where crop like '".$crop."' and year(plantdate) = '".$_GET['plantyear']."' and fieldID = '".$fld."' 
union 
SELECT gen from transferred_to
where crop = '".$crop."' and year(transdate) = '".$_GET['plantyear']."' and fieldID = '".$fld."' ) as tmp
order by gen";

$result = $dbcon->query($sql);
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
echo "<option value=\"".$row['gen']."\">".$row['gen']."</option>";
}

?>

