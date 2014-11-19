<?php session_start();?>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/design.php';
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/stopSubmit.php';
$currentCrop=$_GET['crop'];
$currentDate=$_GET['date'];
$dateArray = explode("-", $currentDate);
$farm = $_SESSION['db'];
if (isset($_POST['day']) && isset($_POST['month']) && isset($_POST['year'])) {
   $dDay = $_POST['day'];
   $dMonth = $_POST['month'];
   $dYear = $_POST['year'];
} else if (isset($_GET['day']) && isset($_GET['month']) && isset($_GET['year'])) {
   $dDay = $_GET['day'];
   $dMonth = $_GET['month'];
   $dYear = $_GET['year'];
} 
?>

<script type="text/javascript">
function show_confirm() {
   var cb = document.getElementById("cropButton");
   var crp = cb.value;
   if(checkEmpty(crp) || crp == "Crop") {
      alert("Please Select a Crop");
      return false;
   }
   var con="Crop: "+ crp + "\n";
   var m = document.getElementById("month").value;
   con=con+"Harvest Date: "+m+"-";
   var d = document.getElementById("day").value;
   con=con+d+"-";
   var y = document.getElementById("year").value;
   con=con+y+"\n\n";

   var numRows = document.getElementById("numRows").value;
   if (numRows < 1) {
      alert("Add at Least One Field!");
      return false;
   }
   for (j = 1; j <= numRows; j++) {
      var fld = document.getElementById("fieldID"+j).value;
      if(checkEmpty(fld)) {
         alert("Please Select a FieldID in row " + j);
         return false;
      }
      con=con+"FieldID: "+ fld+ "\n";
      var yld = document.getElementById("yield"+j).value;
      if (checkEmpty(yld) || yld<=0 || !isFinite(yld) ) {
        alert("Enter a valid yield in row " + j);
        return false;
      }
      con=con+"Yield: "+ yld+ "\n";
      var unit = document.getElementById("unit"+j).value;
      if(checkEmpty(unit)) {
         alert("Please Select a Unit in row " + j);
         return false;
      }
      con=con+"Unit: "+ unit + "\n";
   <?php
   if ($_SESSION['labor']) {
      echo 'var numW = document.getElementById("numW"+j).value;
      if (checkEmpty(numW) || numW<=0 || !isFinite(numW)) {
         alert("Enter a valid number of workers in row " + j);
         return false;
      }
     con=con+"Number of workers: "+ numW+ "\n";

     var tme = document.getElementById("time"+j).value;
     var unit = document.getElementById("timeUnit").value;
      if (checkEmpty(tme) || tme<=0 || !isFinite(tme)) {
         alert("Enter a valid number of " + unit + " in row "+j);
         return false;
      }
      con = con+"Number of " + unit + ": " + tme + "\n\n";';
   } else {
      echo 'con +="\n";';
   }
   ?>
   }

   var ret = confirm("Confirm Entry:"+"\n"+con);
   if (ret) cb.disabled=false;
   return ret;
}
</script>

<form name='form' id='test'  method='POST' action="<?php echo $_SERVER['PHP_SELF'].'?year='.$dYear.
  '&month='.$dMonth.'&day='.$dDay.'&crop='.$currentCrop.'&currentID='.$_GET['currentID'].
  '&tab=harvest:harvestInput&date='.$currentDate;?>" >

<h3>Harvest Input Form</h3>
<br clear="all">
<label for="crop"><b>Date Crop Harvested:</b></label>
<?php
if (!$_SESSION['mobile']) echo "<br clear='all'>";
include $_SERVER['DOCUMENT_ROOT'].'/date.php';
?>
<br clear="all"/>

<label for='cropButton'><b>Crop:&nbsp;</b></label>
<div class='styled-select'>
<select name='cropButton' id='cropButton' class='mobile-select'>
<option value=0 selected>Crop</option>
<?php
$sql = "SELECT crop FROM plant WHERE active=1";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
   echo "<option value='".$row['crop']."'>".$row['crop']."</option>";
}
?>
</select></div>

<br clear="all"/>

<?php
if($_SESSION['mobile']){
echo "<br clear=\"all\">";
}
?>
<br clear="all"/>
<table id='harvestTable' name='harvestTable'>
<tr><th>FieldID</th><th>Yield</th><th>&nbsp;&nbsp;&nbsp;&nbsp;Unit&nbsp;&nbsp;&nbsp;&nbsp;</th>
<?php
if ($_SESSION['labor']) {
  echo '
<th>Workers</th><th>
<div class="styled-select">
<select name="timeUnit" id="timeUnit" class="mobile-select">
   <option value="minutes">Minutes</option>
   <option value="hours">Hours</option>
</select>
</div>
</th>';
}
?>
</tr>
</table>
<br clear="all"/>
<input type="button" id="addField" name="addField" class="genericbutton" onClick="addRow();"
value="Add Field">
<input type="button" id="removeField" name="removeField" class="genericbutton" onClick="removeRow();"
value="Remove Field">
<br clear="all"/>
<br clear="all"/>
<input type="hidden" name="numRows" id="numRows" value=0>
<script type="text/javascript">
var numRows = 0;
function addRow() {
   var cb = document.getElementById("cropButton");
   if (cb.value=="0") {
      alert("Error: choose crop first!");
   } else {
      cb.disabled=true;
      numRows++;
      var table = document.getElementById("harvestTable");
      var row = table.insertRow(numRows);
      row.id = "row"+numRows;
      row.name = "row"+numRows;
      var cell0 = row.insertCell(0);
      var year = document.getElementById("year").value;
      var crop = encodeURIComponent(cb.value);
      xmlhttp= new XMLHttpRequest();
      xmlhttp.open("GET", "update_field.php?crop="+crop+"&plantyear="+year, false);
      xmlhttp.send();
      if(xmlhttp.responseText=="\n") {
        cb.value="";
      }
      cell0.innerHTML="<div class='styled-select' id ='fieldID2" + numRows + "''>  <select name= 'fieldID" +
        numRows + "' id= 'fieldID" + numRows + "' class='mobile-select' style='width:100%'>"+xmlhttp.responseText+"</select> </div>";
      var cell1 = row.insertCell(1);
      cell1.innerHTML="<input onkeypress= 'stopSubmitOnEnter(event);' type = 'text' name='yield"+numRows+
         "' id='yield"+numRows+"' class='textbox mobile-input inside_table' style='width:100%'>";
      <?php
      if ($farm == 'wahlst_spiralpath') {
        echo 'xmlhttp.open("GET", "hupdatesp.php?crop="+crop, false);';
      } else {
        echo 'xmlhttp.open("GET", "hupdate.php?crop="+crop, false);';
      }
      ?>
      xmlhttp.send();
      var cell2 = row.insertCell(2);
      cell2.innerHTML="<div class = 'styled-select'> <select name='unit"+numRows+"' id='unit" + numRows +
        "' class='mobile-select' style='width:100%'>"+xmlhttp.responseText+" </select> </div>";
<?php
if ($_SESSION['labor']) {
echo '
      var cell3 = row.insertCell(3);
      cell3.innerHTML="<input onkeypress= \'stopSubmitOnEnter(event);\' type=\'text\' name=\'numW"+numRows+
         "\' id=\'numW" + numRows + "\' class=\'textbox mobile-input inside_table\' style=\'width:100%\'>";
      var cell4 = row.insertCell(4);
      cell4.innerHTML="<input onkeypress=\'stopSubmitOnEnter(event);\' type=\'text\' name=\'time"+numRows+
         "\' id=\'time"+numRows+"\' class=\'textbox mobile-input inside_table\' style=\'width:100%\'>";';
}
?>
   }
   var nr = document.getElementById("numRows");
   nr.value=numRows;
}
   
function removeRow() {
   if (numRows > 0) {
      var field=document.getElementById('fieldID2' + numRows);
      field.parentNode.removeChild(field);
      var yield=document.getElementById('yield' + numRows);
      yield.parentNode.removeChild(yield);
      var unit=document.getElementById('unit' + numRows);
      unit.parentNode.removeChild(unit);
<?php
if ($_SESSION['labor']) {
  echo '    var time=document.getElementById(\'time\' + numRows);
      time.parentNode.removeChild(time);';
  echo '    var work=document.getElementById(\'numW\' + numRows);
      work.parentNode.removeChild(work);';
}
?>
      var table = document.getElementById("harvestTable");
      table.deleteRow(numRows);
      numRows--;
   }
   if (numRows == 0) {
      var cb = document.getElementById("cropButton");
      cb.disabled=false;
   }
   var nr = document.getElementById("numRows");
   nr.value=numRows;
}
</script>

<?php
if($currentDate){
  echo "<script type=\"text/javascript\">";
  echo "var eb = document.getElementById(\"cropButton\");";
  echo "eb.value = \"".html_entity_decode($currentCrop, ENT_QUOTES)."\";";
  echo "addRow();";
  echo "</script>";
}
?>


<br clear="all"/>
<div>
<label for="comments">Comments:</label>
<br clear="all"/>
<textarea  name="comments"rows="20" cols="30">
</textarea>
</div>

<br clear="all"/>

   
<input  class="submitbutton"  type="submit" name="submit" value="Submit" onclick= "return show_confirm();">

</form>
<?php
   if(!empty($_GET['currentID'])){
   echo "<form method='POST' action='harvestList.php?year=".$_GET['year']."&month=".$_GET['month'].
         "&day=".$_GET['day']."&currentID=".$_GET['currentID'].
         "&tab=harvest:harvestList&detail=0'>";
       echo "<input type='submit' class='submitbutton' value ='View Harvest List (".$currentDate.")'></form> ";
}
?>
<!--
<br clear="all"/>
-->

<?php
//echo "</form>";
echo '<form method="POST" action = "harvestReport.php?tab=harvest:harvestReport"><input type="submit" class="submitbutton" value = "View Table"></form>';
if(isset($_POST['submit'])){
   $crop = escapehtml($_POST['cropButton']);
   $year = $_POST['year'];
   $month = $_POST['month'];
   $day = $_POST['day'];
   $numRows = $_POST['numRows'];
   $comments =escapehtml( $_POST['comments']);
   $unitSQL = "select units from plant where crop = '".$crop."'";
   $unitdata = mysql_query($unitSQL) or die(mysql_error());
   $row = mysql_fetch_array($unitdata);
   $insertUnit = $row['units'];
   for ($j = 1; $j <= $numRows; $j++) {
      $fieldID = escapehtml($_POST['fieldID'.$j]);
      $yield = escapehtml($_POST['yield'.$j]);
      $unit = escapehtml($_POST['unit'.$j]);

      if ($_SESSION['labor']) {
         // Check if given time is in minutes or hours
         $time = escapehtml($_POST['time'.$j]);
         if ($_POST['timeUnit'] == "minutes") {
            $hours = $time/60;
         } else if ($_POST['timeUnit'] == "hours") {
            $hours = $time;
         }
         // Check if num workers is filled in
         $numW = escapehtml($_POST['numW'.$j]);
         if ($numW != "") {
            $totalHours = $hours * $numW;
         } else {
            $totalHours = $hours;
         }
      } else {
         $hours=0;
         $totalHours=0;
      }

      if ($farm == 'wahlst_spiralpath') {
        $sql = "INSERT INTO harvested(username,hardate,crop,fieldID,yield,hours, comments, unit) VALUES('".
           $_SESSION['username']."','".$year.'-'.$month.'-'.$day."','".$crop."','".$fieldID.
           "',$yield,$hours,'$comments','$unit')";
      } else {
         $sql = "INSERT INTO harvested(username,hardate,crop,fieldID,yield,hours,comments, unit) VALUES('"
            .$_SESSION['username']."','".$year.'-'.$month.'-'.$day."','".$crop."','".$fieldID.
            "',$yield/(Select conversion from units where crop= '".$crop."' and unit= '".$unit.
            "'),$totalHours,'$comments', '$insertUnit')";
      }
   # $sql = "INSERT INTO harvested(username,hardate,crop,fieldID,yield,hours,comments, unit) VALUES('".$_SESSION['username']."','".$year.'-'.$month.'-'.$day."','".$crop."','".$fieldID."',$yield*(Select conversion from (Select 1 as conversion from units where crop= '".$_POST['crop']."' and default_unit ='".$_POST['unit']."' union select conversion from  units where crop= '".$_POST['crop']."' and unit= '".$_POST['unit']."') as conver) ,$hours,'$comments', '$unit')";
   # START - put conversion back in when available
#   $sql = "INSERT INTO harvested(username,hardate,crop,fieldID,yield,hours, comments, unit) VALUES('".$_SESSION['username']."','".$year.'-'.$month.'-'.$day."','".$crop."','".$fieldID."',$yield,$hours,'$comments','$unit')";
      $value = mysql_query($sql) or die(mysql_error());
      echo mysql_error();
      if($value){
         echo "<script>showAlert(\"Entered data successfully!\");</script> \n";
      } else {
          echo "<script>alert(\"Could not enter data: Please try again!\\n".mysql_error()."\");</script>\n";
      }
  }
}
?>