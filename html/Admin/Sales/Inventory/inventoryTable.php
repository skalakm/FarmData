<?php session_start();

include_once $_SERVER['DOCUMENT_ROOT'].'/connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/authentication.php';
include $_SERVER['DOCUMENT_ROOT'].'/design.php';
?>

<?php
if (isset($_GET['editto'])){
/*
   // echo $_POST[edit];
   $sqlInsert = "Insert into correct (correctDate, crop_product, grade, amount, unit) values('".date('Y-m-d')."', '".$_GET[crop]."', ".$_GET[gradeupdate].", ".$_GET[amount].", '".$_GET[unit]."')";
   echo $sqlInsert;
   mysql_query($sqlInsert) or die(mysql_error());
*/
}

   $crop_product = escapehtml($_GET['crop_product']);
   $grade = $_GET['grade'];
   $sql = "Select * from inventory where crop_product like '".$crop_product."'and grade like '".$grade."' group by crop_product, grade, unit";
   $result = mysql_query($sql);
   
   echo "<table>";
   $crpProd = $_GET['crop_product'];
   if ($crpProd === "%" || $crpProd === "%25") {
      $crpProd = "All Crops/Products";
   }
   $grd = $_GET['grade'];
   if ($grd === "%" || $grd==="%25") {
      $grd = "All";
   }

   echo "<caption>Inventory Report for ".$crpProd." of Grade: ".$grd."</caption>";
   echo "<caption>".$dat."</option>";

   echo "<tr>
      <th>Crop/Product</th>
      <th>Grade</th>
      <th>Amount</th>
      <th>Unit</th>
      <th>Update To</th>
      <th>Update</th>
      </tr>";
   while ($row = mysql_fetch_array($result)) {
      echo "<tr>";
      echo "<td>";
      echo $row['crop_product'];
      echo "</td><td>";
      echo $row['grade'];
      echo "</td><td>";
      $unit = $row['unit'];
      $amount = $row['amount'];
      $convsql = "SELECT conversion FROM units WHERE crop='".$row['crop_product']."' AND unit='POUND'";
      $convresult = mysql_query($convsql);
      if (mysql_num_rows($convresult) > 0) {
         $convrow = mysql_fetch_array($convresult);
         $conversion = $convrow[0];
         $amount = $amount * $conversion;
         $unit = 'POUND';
      }
      echo number_format((float) $amount, 2, '.', '');
      echo "</td><td>";
      echo $unit;
      echo "</td><td>";
      echo "<form method='POST' action=\"inventoryUpdate.php?tab=admin:admin_sales:inventory&crop_product=".
          encodeURIComponent($_GET['crop_product'])."&grade=".$grade."&crop=".
          encodeURIComponent($row[crop_product])."&gradeupdate=".$row[grade]."&amount=".$amount."&unit=".
          encodeURIComponent($unit)."\"><input type='text' class='textbox2' id='edit' name='edit'>";
      echo "</td><td>";
      echo "<input type='submit' class='submitbutton' value='Update' name='submit' id='submit'></form>";
      echo "</td>";
      echo "</tr>";
   }
   echo "</table>";
   echo "<br clear='all'>";
        echo "<form name='form' method='POST' action='/down.php'>";
        echo "<input type=\"hidden\" name=\"query\" value=\"".escapehtml($sql)."\">";
   echo "<input class='submitbutton' type='submit' name='submit' value='Download Report'>";
   echo "</form>";
   echo "<form method='POST' action='inventoryReport.php?tab=admin:admin_sales:inventory'>";
   echo "<input type='submit' class='submitbutton' value='Run Another Report'></form>";

?>