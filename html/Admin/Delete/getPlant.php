<?php
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';
$crop = escapehtml($_GET['crop']);
$sql = "select units, units_per_case, dh_units, active from plant where crop='".$crop."'";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);

$plantarray = array();
$plantarray[0] = $row['units'];
$plantarray[1] = $row['units_per_case'];
$plantarray[2] = $row['dh_units'];
$plantarray[3] = $row['active'];
echo json_encode($plantarray);
?>
