<?php session_start(); ?>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/Admin/authAdmin.php';
include $_SERVER['DOCUMENT_ROOT'].'/design.php';
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';
?>
<?php
	// delete from tSprayMaster, tSprayWater, tSprayField.
	if(isset($_GET['id'])){
		$sqlDel="Delete from tSprayWater where id=".$_GET['id'];
		mysql_query($sqlDel);
      echo mysql_error();
	
		$sqlDel="Delete from tSprayField where id=".$_GET['id'];
		mysql_query($sqlDel);
      echo mysql_error();

		$sqlDel="Delete from tSprayMaster where id=".$_GET['id'];
      mysql_query($sqlDel) or die (mysql_error());
      echo mysql_error();
	}
?>
<table >
<caption> Tractor Spraying Report </caption>
<tr>
	<th >Date</th>
	<th># Field</th>
	<th># Material</th>
	<th>CropGroup</th> 
	<th>Comments</th>
	<th>Edit</th>
	<th>Delete</th>	
</tr>


<?php
// get date Range
$fromDate=$_GET['year']."-".$_GET['month']."-".$_GET['day'];
$toDate=$_GET['tyear']."-".$_GET['tmonth']."-".$_GET['tday'];
$sql="select id,user, sprayDate, noField, noMaterial, cropGroup, comment from tSprayMaster where sprayDate between '$fromDate' and '$toDate' order by sprayDate";
echo "<input type = \"hidden\" name = \"query\" value = \"".$sql."\">";
$count=0;
$totalMaterial=0;
$resultM=mysql_query($sql);
// echo table rows
	while($rowM=mysql_fetch_array($resultM)){
	echo "<tr><td>".$rowM['sprayDate']."</td>";
	echo "<td>".$rowM['noField']."</td>";
	echo "<td>".$rowM['noMaterial']."</td>";
	echo "<td>".$rowM['cropGroup']."</td>";
	echo "<td>".$rowM['comment']."</td>";
	echo "<td><form method='POST' action='tSpray.php?user=".$rowM[user]."&date=".$rowM[sprayDate]."&crop=".$rowM[cropGroup]."&month=".$_GET[month]."&day=".$_GET[day]."&year=".$_GET[year]."&tmonth=".$_GET[tmonth]."&tyear=".$_GET[tyear]."&tday=".$_GET[tday]."&id=".$rowM['id']."&tab=admin:admin_delete:deletesoil:deletespray:tractorspray&submit=Submit'><input type='submit' class='editbutton' value='Edit'></form></td>";
	echo "<td><form method='POST' action='deleteTspray.php?month=".$_GET[month]."&day=".$_GET[day]."&year=".$_GET[year]."&tmonth=".$_GET[tmonth]."&tyear=".$_GET[tyear]."&tday=".$_GET[tday]."&id=".$rowM['id']."&tab=admin:admin_delete:deletesoil:deletespray:tractorspray&submit=Submit'><input type='submit' class='deletebutton' value='Delete'></form></td></tr>";

	}
echo '</table>';


echo '<br clear = "all"/>';
?>