<?php session_start();?>
<link rel="stylesheet" href="/pure-release-0.5.0/pure-min.css">
<script type="text/javascript">
function getUnit(){
   var newdiv = document.getElementById('unitDiv');
   var crp = encodeURIComponent(document.getElementById("crop").value);
   xmlhttp= new XMLHttpRequest();
   xmlhttp.open("GET", "getDefUnit.php?crop="+crp, false);
   xmlhttp.send();
   newdiv.innerHTML = '<label>Unit:</label> ' +
       '<input type="text" readonly name="unit" id="unit" value="' +
       xmlhttp.responseText + '">';
/*
  $unit.'"></div>';
 '<div id="unitDiv"> <input type="text" class="textbox25" readonly name="unit" ' + 
    ' id="unit" value="' + xmlhttp.responseText + '"></div>';
*/
}
</script>

<?php
include $_SERVER['DOCUMENT_ROOT'].'/Admin/authAdmin.php';
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/design.php';

$id=$_GET['id'];
$origYear   = $_GET['year'];
$origMonth    = $_GET['month'];
$origDay    = $_GET['day'];
$origCrop    = $_GET['crop_product'];
$origTarget = $_GET['target'];
$origGrade   = $_GET['grade'];
$origBack   = $_GET['bringback'];
$tcurYear    = $_GET['tyear'];
$tcurMonth    = $_GET['tmonth'];
$tcurDay    = $_GET['tday'];

$sqlget = "SELECT id,year(packDate) as yr, month(packDate) as mth, day(packDate) as dy, crop_product, ".
              "grade, amount,".  "unit, comments, bringBack, Target FROM pack where id = ".$id;
$sqldata = $dbcon->query($sqlget);
$row = $sqldata->fetch(PDO::FETCH_ASSOC);
$unit = $row['unit'];
$grade = $row['grade'];
$amount = $row['amount'];
$bringBack = $row['bringBack'];
$curYear = $row['yr'];
$curMonth = $row['mth'];
$curDay = $row['dy'];
$curCrop = $row['crop_product'];
$comments = $row['comments'];
$target = $row['Target'];
$dMonth = $curMonth;
$dYear  = $curYear;
$dDay     = $curDay;
echo "<form class='pure-form pure-form-aligned' name='form' method='post' action=\"".$_SERVER['PHP_SELF'].
   "?tab=admin:admin_sales:packing:packing_report&year=".$origYear.
   "&month=".$origMonth."&day=".$origDay.
   "&tyear=".$tcurYear."&tmonth=".$tcurMonth."&tday=".$tcurDay.
   "&crop_product=".encodeURIComponent($origCrop).
   "&target=".encodeURIComponent($origTarget).
   "&bringback=".encodeURIComponent($origBack).
   "&grade=".encodeURIComponent($origGrade)."&id=".$id."\">";
echo '<div class="pure-controls">';
echo "<h2> Edit Packing Record </h2>";
echo '</div>';
echo '<br clear="all"/>';
echo "<div class='pure-control-group'>";
echo "<label for='from'>Date:</label>";
include $_SERVER['DOCUMENT_ROOT'].'/date.php';
echo"</div>";

echo '<div class="pure-control-group">';
echo '<label>Crop/Product:</label> ';
echo '<select name="crop" id="crop" onchange="getUnit();">';
echo '<option value="'.$curCrop.'" selected>'.$curCrop.' </option>';
$sql = 'select crop from (select crop from plant where active=1 union '.
         'select product as crop from product where active=1) tmp order by crop';
$sqldata = $dbcon->query($sql);
while ($row = $sqldata->fetch(PDO::FETCH_ASSOC)) {
   echo '<option value="'.$row['crop'].'">'.$row['crop'].' </option>';
}
echo '</select></div>';

echo '<div class="pure-control-group">';
echo '<label>Grade:</label> ';
echo '<select name="grade" id="grade" class="mobile-select">';
echo '<option value="'.$grade.'" selected>'.$grade.' </option>';
echo '<option value="1">1</option>';
echo '<option value="2">2</option>';
echo '<option value="3">3</option>';
echo '<option value="4">4</option>';
echo '</select></div>';

echo '<div class="pure-control-group">';
echo '<label>Amount:</label> ';
echo '<input type="text" class="textbox2" name="amount" id="amount" value="'.$amount.'"></div>';

echo '<div class="pure-control-group" id="unitDiv">';
echo '<label>Unit:</label> ';
echo '<input type="text" readonly name="unit" id="unit" value="'.$unit.'"></div>';

echo '<div class="pure-control-group">';
echo '<label>Bring Back:</label> ';
echo '<select name="bringback" id="bringback">';
$thisBringBack = "Yes";
$other         = "No";
if ($bringBack == 0){
   $thisBringBack = "No";
   $other      = "Yes";
}
$otherOption = ($bringBack == 0 ? 1 : 0);
echo '<option value='.$bringBack.' selected>'.$thisBringBack.' </option>';
echo '<option value='.$otherOption.'>'.$other.'</option>';
echo '</select></div>';

echo '<div class="pure-control-group">';
echo '<label>Target:</label> ';
echo '<select name="target" id="target">';
echo '<option value="'.$target.'" selected>'.$target.' </option>';
$sql = "select distinct targetName from targets where targetName <> '".$target."'";
$sqldata = $dbcon->query($sql);
while ($row = $sqldata->fetch(PDO::FETCH_ASSOC)) {
   echo '<option value="'.$row['targetName'].'">'.$row['targetName'].' </option>';
}
echo '</select></div>';

echo '<div class="pure-control-group">';
echo '<label>Comments:</label> ';
echo "<textarea rows=\"10\" cols=\"30\" name = \"comments\" id = \"comments\">";
echo $comments;
echo "</textarea></div>";
echo '<div class="pure-control-group">';
echo "<input type='submit' name='submit' value='Update Record' class = 'submitbutton pure-button wide'></div>";
echo "</form>";

if ($_POST['submit']) {
   $comSanitized=escapehtml($_POST['comments']);
   $amount = escapehtml($_POST['amount']);
   $bringback = escapehtml($_POST['bringback']);
   $updateGrade = escapehtml($_POST['grade']);
   $crop = escapehtml($_POST['crop']);

   $target = escapehtml($_POST['target']);
   $year = escapehtml($_POST['year']);
   $month = escapehtml($_POST['month']);
   $day = escapehtml($_POST['day']);
   $unit = escapehtml($_POST['unit']);
   $sql = "update pack set unit='".$unit."', grade=".$updateGrade.", packDate='".$year."-".
     $month."-".$day."', amount=".$amount.",bringBack=".$bringback.",target='".$target."', comments='".
     $comSanitized."',crop_product='".$crop."' where id=".$id;
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->execute();
   } catch (PDOException $p) {
      echo "<script>alert(\"Could not update packing record".$p->getMessage()."\");</script>";
      die();
   }
   echo "<script>showAlert(\"Entered data successfully!\");</script> \n";
   echo "<meta http-equiv=\"refresh\" content=\"0;URL=packingTable.php?year=".$origYear.
     "&month=".$origMonth."&day=".$origDay."&tyear=".$tcurYear.
     "&tmonth=".$tcurMonth."&tday=".$tcurDay.
     "&target=".encodeURIComponent($origTarget).
     "&bringback=".encodeURIComponent($origBack).
     "&grade=".encodeURIComponent($origGrade).
     "&tab=admin:admin_sales:packing:packing_report".
     "&crop_product=".encodeURIComponent($origCrop)."\">";
}
?>
