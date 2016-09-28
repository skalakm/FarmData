<?php session_start(); ?>
<?php
   include $_SERVER['DOCUMENT_ROOT'].'/Admin/authAdmin.php';
   include_once $_SERVER['DOCUMENT_ROOT'].'/connection.php';
   include $_SERVER['DOCUMENT_ROOT'].'/design.php';
?>

<head>
   <!--Load the AJAX API-->
   <script type="text/javascript" src="https://www.google.com/jsapi"></script>
</head>

<body>
   <?php
      $year = $_GET['year'];
      $crop = escapehtml($_GET['crop']);
      echo '<center>';
      echo '<h2>Average yield per row foot for each field of '.$crop.' in '.$year.'</h2>';   
      $sql = "select sum(yield)/((select coalesce(sum(bedft*rowsBed), 0) from dir_planted where ".
        "dir_planted.crop='".$crop."' and year(plantdate) = '".$year.
         "')+ (select coalesce(sum(bedft*rowsBed), 0) from transferred_to where year(transdate)='".
        $year."' and transferred_to.crop='".$crop."')) as overalAvg from harvested where crop='".$crop.
        "' and year(hardate)=".$year;
      
      $res = $dbcon->query($sql);
      $row = $res->fetch(PDO::FETCH_ASSOC);;
      echo "<input type='hidden' id='overallAvg' value='".number_format($row[overalAvg],2,'.','')."'>";

      $sql = "select fieldID, sum(yield) from harvested where crop='".$crop."' and year(hardate)=".$year.
         " group by fieldID";
      $sqldata = $dbcon->query($sql);
      $count=0;
      while($row = $sqldata->fetch(PDO::FETCH_ASSOC)){
         $sql ="Select unit, sum(yield)/(Select sum(tft) from ((Select bedft * rowsBed as tft ".
            "from dir_planted where fieldID like '".$row['fieldID'].
            "' and year(plantdate)=".$year." and dir_planted.crop= '".$crop."') union all ".
            "(Select bedft * rowsBed as tft from transferred_to where year(transdate)=".$year.
            " and transferred_to.crop= '".$crop.
            "' and fieldID like '".$fieldID."')) as temp1) as yperft from harvested where year(hardate)=".
            $year." and harvested.crop = '" .$crop."' and harvested.fieldID like '".
            $row['fieldID']."' group by unit order by unit";
         $res2 = $dbcon->query($sql);
         $yieldPerRowft = $res2->fetch(PDO::FETCH_ASSOC);
         echo"<input type='hidden' id='fieldID".$count."' value='".$row['fieldID'].
            "'/> <input type='hidden' id='yield".$count."' value='".
            number_format($yieldPerRowft['yperft'], 2,'.','')."'/>";
         $count++;
      }
      echo "<input type='hidden' id='numfield' value='".$count."'/>";
      $sql = $dbcon->query("select distinct unit from harvested where crop='".$crop."'");
      $row = $sql->fetch(PDO::FETCH_ASSOC);
      echo"<input type='hidden' id='unit' value='".$row['unit']."'/>";
      
   ?>
   <script type="text/javascript">
   // Load the visualization API and the chart package
   google.load('visualization', '1.0', {'packages':['corechart']});
   // Set a callback to run when the google visualization API is loaded.
   google.setOnLoadCallback(drawChart);
   // callback that creates and populates a data table, instantiates the chart, passes in the data and draws it.
   function drawChart() {
      // Create the data table.
      var count = 0;
      var numYear = document.getElementById('numfield').value; 
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'fieldID');
      data.addColumn('number', 'Yield');
      for (count; count < numYear; count++){
      console.log(document.getElementById('yield'+count).value);
      data.addRow(
         [document.getElementById('fieldID'+count).value,Number(document.getElementById('yield'+count).value)]
      );
      }
        // create a view for the table
      var view = new google.visualization.DataView(data);
      view.setColumns([0,1, {calc:"stringify", sourceColumn:1, type: "string", role:"annotation"}]); 
      // Set chart options
      var options = {'title':'Overall average Yield per row foot: ' + document.getElementById('overallAvg').value+ ' ' + document.getElementById('unit').value+' per row foot',
                     'hAxis':{title:'FIELD ID', titleTextStyle:{color: 'red'}},
                     'vAxis':{title:document.getElementById('unit').value+' per row foot'},
                     'width':800,
                     'height':600};
   
      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
      chart.draw(view, options);
   }
 
   </script>
   <!--Div that will hold the pie chart-->
   <div id="chart_div"></div>
   </center>
</body>
</html>
