<?php session_start(); ?>
<?php
   include $_SERVER['DOCUMENT_ROOT'].'/Admin/authAdmin.php';
   include_once $_SERVER['DOCUMENT_ROOT'].'/connection.php';
   include $_SERVER['DOCUMENT_ROOT'].'/design.php';
?>

<html>
<head>
   <!--Load the AJAX API-->
   <script type="text/javascript" src="https://www.google.com/jsapi"></script>
</head>

<body>
   <?php
      $year = $_GET['year'];
      $crop = escapehtml($_GET['crop']);
      $array = array();
      $array[0] = array("fieldID", "Yield");
      echo '<h4>Total Yield for each field of '.$crop.' in '.$year.'</h4><br clear="all">';   
      $sql = "select fieldID, sum(yield) from harvested where crop='".$crop."' and year(hardate)=".$year." group by fieldID";
      $sqldata = mysql_query($sql);
      $count=0;
      while($row = mysql_fetch_array($sqldata)){
         $array[$count+1] = array(escapeescapehtml($row['fieldID']), 
             intval($row['sum(yield)']));
         $count++;
      }
      $sql = mysql_query("select distinct unit, sum(yield) from harvested where crop='".$crop."' and year(hardate)=".$year);
      $row = mysql_fetch_array($sql);
      echo"<input type='hidden' id='unit' value='".$row['unit']."'/>";
      echo"<input type='hidden' id='total' value='".$row['sum(yield)']."'/>";
      $json = json_encode($array);
   ?>
   <script type="text/javascript">
   // Load the visualization API and the chart package
   google.load('visualization', '1.0', {'packages':['corechart']});
   // Set a callback to run when the google visualization API is loaded.
   google.setOnLoadCallback(drawChart);
   // callback that creates and populates a data table, instantiates the chart, passes in the data and draws it.
   function drawChart() {
      // Create the data table.
      //var colors = ["blue", "red", "orange", "green" , "yellow", "purple", "gold", "grey", "aero"];
      var count = 0;
      var data2 = eval(<?php echo $json;?>);
      var data = new google.visualization.arrayToDataTable(data2);
      // Set chart options
      var view  = new google.visualization.DataView(data);
      view.setColumns([0,1,{calc:"stringify", sourceColumn: 1, type :"string", role: "annotation"}]);
      var unit = document.getElementById('unit').value;
      var options = {'title':'Total of Yield: '+document.getElementById('total').value+ ' '+ unit,
                     'hAxis':{title:'FIELD ID', titleTextStyle:{color: 'red'}},
                     //'legend': 'none',
                     'vAxis':{title: unit},
                     'width':800,
                     'height':600
                     };
   
      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
      chart.draw(view, options);
   }
 
   </script>
   <!--Div that will hold the pie chart-->
   <div id="chart_div"></div>
</body>
</html>