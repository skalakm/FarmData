<input type="hidden" name="numCropRows" id="numCropRows" value="0">

<br clear="all"/>
<br clear="all"/>
<table id="cropTable">
<tr><th>Crops</th></tr>
</table>
<br clear="all"/>
<input type="button" id="addCrop" name="addCrop" class="genericbutton" onClick="addCropRow();"
value="Add Crop">
&nbsp;&nbsp;&nbsp;
<input type="button" id="removeCrop" name="removeCrop" class="genericbutton" onClick="removeCropRow();"
value="Remove Crop">
<br clear="all"/>

<script type="text/javascript">
   var numCropRows = 0;

   function addCropRow() {
      numCropRows++;
      var numCrops = document.getElementById("numCropRows");
      numCrops.value = numCropRows;
      var table = document.getElementById("cropTable");
      var row    = table.insertRow(numCropRows);
      row.id="cropRow" + numCropRows;
      var cell0 = row.insertCell(-1);
      var cropID = '<?php
         $result=mysql_query("Select crop from plant");
         while ($row1 =  mysql_fetch_array($result)){
             echo "<option value = \"".$row1['crop']."\">".$row1['crop']."</option>";
         }
       ?>';
      cell0.innerHTML = '<div class="styled-select" id="cropDiv'+numCropRows+
        '"> <select class="mobile-select" name ="crop' + 
        numCropRows +'" id="crop' + numCropRows + '" >' +
       '<option value = 0 selected disabled> Crop </option>' +  cropID + '</select></div>';
   }
   addCropRow();

   function removeCropRow(){
      if (numCropRows >0){
         var row = document.getElementById("cropRow" + numCropRows);
         row.innerHTML = "";
         numCropRows--;
         var numCrops = document.getElementById("numCropRows");
         numCrops.value = numCropRows;
      }
   }

</script>