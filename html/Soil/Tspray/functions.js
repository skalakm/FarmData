	var numRows=0;
	var numRowsMat=0;
	function addRow(numRows){
		numRows++;
		var table = document.getElementById("fieldTable");
		var row 	= table.insertRow(numRows);
		var cell0 = row.insertCell(0);
		var fieldID = '<?php
								$result=mysql_query("Select fieldID from field_GH");
								while ($row1 =  mysql_fetch_array($result)){
									echo "<option value = \"".escapehtml($row1[fieldID])."\">".escapehtml($row1[fieldID])."</option>";
								}
							?>';
		cell0.innerHTML = '<center><div class="styled-select" id="fieldDiv'+numRows+'"> <class="mobile-select" select name ="field' + numRows +'" id="field' + numRows + '" onChange="addInput('+numRows+'); addAcre('+numRows+'); calculateTotalUpdate(); calculateWater();">' +'<option value = 0 selected disabled> FieldID</option>' +	fieldID + '</select></div></center>';
		var cell1 = row.insertCell(1);
		cell1.innerHTML = "<center><div id=\"maxBed"+numRows+"\" class='styled-select2'> <class=\"mobile-select\" select id=\"maxBed2"+numRows+"\" name=\"maxBed2"+numRows+"\"  onChange=\"addAcre("+numRows+"); calculateTotalUpdate(); calculateWater(); \">"+
								"<option> Beds </option> </select></div></center>";
		var cell2 = row.insertCell(2);
		cell2.innerHTML = "<center><div id=\"acreDiv"+numRows+"\"><input class='textbox4 mobile-input inside_table' type=\"text\" id=\"acre"+numRows+"\" value=0 readonly></div> </center>";
	}
	//addRow();
	function removeRow(numRows){
		if (numRows > 0){
			var field = document.getElementById('field'+numRows);
			field.parentNode.removeChild(field);
			var maxBed = document.getElementById('maxBed2'+numRows);
			maxBed.parentNode.removeChild(maxBed);
			var acre = document.getElementById('acre'+numRows);
                        acre.parentNode.removeChild(acre);
			var table = document.getElementById('fieldTable');
			table.deleteRow(numRows);
			numRows--;
		}
	}
	function addRowMat(numRowsMat){
		numRowsMat++;
		var table = document.getElementById("materialTable");
		var row = table.insertRow(numRowsMat);
		var materialSprayed = "<?php
			$sqlM="SELECT sprayMaterial FROM tSprayMaterials";
			$resultM=mysql_query($sqlM);
			//echo mysql_error();
			while($rowM=mysql_fetch_array($resultM)){
				echo "<option value='".$rowM[sprayMaterial]."'>".$rowM[sprayMaterial]."</option>";
			}?>";

		var cell0 = row.insertCell(0);
		cell0.innerHTML =  "<center><div id =\"material"+numRowsMat+"\" class='styled-select2'><class=\"mobile-select\" select id=\"material2"+numRowsMat+"\" name=\"material2"+numRowsMat+"\"  onChange=\"addInputRates("+numRowsMat+"); calculateSuggested("+numRowsMat+"); addUnit("+numRowsMat+");  \"\n>"+ "<option value=0> MaterialList</option>\n"+materialSprayed+"</select></div></center>";
		var cell1 = row.insertCell(1);
		cell1.innerHTML =  "<center><div id =\"rate"+numRowsMat+
   			"\" class='styled-select2'><class=\"mobile-select\" select id='rate2"+numRowsMat+
   			"' name='rate2"+numRowsMat+"'  onChange=\"calculateSuggested("+
   			numRowsMat+");\">"+"<option value=0 selected> Rates </option> </select></div></center>";
		var cell2 = row.insertCell(2);
 		cell2.innerHTML = "<div id=\"unitDiv"+numRowsMat+"\"><label style=\"font-size:12pt\" id='unit"+ numRowsMat+"'> Unit </label></div>";
		var cell3 = row.insertCell(3);
		cell3.innerHTML = "<center><div id=\"calculatedTotalDiv"+numRowsMat+"\"><input type=\"text\" id=\"calculatedTotal"+numRowsMat+"\" class='textbox4 mobile-input inside_table' value=0 readonly></div></center>";
		var cell4 = row.insertCell(4);
		cell4.innerHTML = "<center><div id=\"actualTotalDiv"+numRowsMat+"\"><input class='textbox4 mobile-input inside_table' type=\"text\" id=\"actuarialTotal"+numRowsMat+"\" name=\"actuarialTotal"+numRowsMat+"\" value=0></div></center>";
		var cell5 = row.insertCell(5);
	}
	//addRowMat();
	function removeRowMat(numRowsMat){
		if (numRowsMat >0){
			var matSpray = document.getElementById("material2"+numRowsMat);
			matSpray.parentNode.removeChild(matSpray);
			var rate = document.getElementById("rate2"+numRowsMat);
			rate.parentNode.removeChild(rate);
			var unit = document.getElementById("unit"+numRowsMat);
         		unit.parentNode.removeChild(unit); 
        		var calcTotal = document.getElementById("calculatedTotal"+numRowsMat);
			calcTotal.parentNode.removeChild(calcTotal);
			var actualTotal = document.getElementById("actuarialTotal"+numRowsMat);
			actualTotal.parentNode.removeChild(actualTotal);
			var table = document.getElementById("materialTable");
			table.deleteRow(numRowsMat);
			numRowsMat--;  
		}
	}
	function addInput(num){
		var e = document.getElementById('field'+num);
		var newdiv=document.getElementById('maxBed'+num);
		var strUser = e.options[e.selectedIndex].text;
		console.log(strUser);
		xmlhttp= new XMLHttpRequest();
		xmlhttp.open("GET", "tupdate.php?field="+strUser, false);
		xmlhttp.send();
		console.log('the response starts');
		console.log(xmlhttp.responseText);
		console.log('the response ends');

		newdiv.innerHTML="<div class='styled-select2' id=\"maxBed"+num+"\"><select class=\"mobile-select\" onchange=\"addAcre("+num+"); calculateTotalUpdate(); calculateWater();\" id= \"maxBed2"+num+"\" name= \"maxBed2"+num+"\">"+xmlhttp.responseText+"</select></div>";
	}


	function addInputRates(numM){
	
		var m = document.getElementById('material2'+numM);
		var newdivM=document.getElementById('rate'+numM);
		var strUserM = m.options[m.selectedIndex].text;
		xmlhttp= new XMLHttpRequest();
		xmlhttp.open("GET", "tRateUpdate.php?material="+strUserM, false);
		xmlhttp.send();
		newdivM.innerHTML="<div class=styled-select2 id='rate"+numM+"'> <select class=\"mobile-select\" onchange=\"calculateSuggested("+numM+");\" id='rate2"+numM+"' name= 'rate2"+numM+"'>"+xmlhttp.responseText+"</select></div>";
	}	

	function addUnit(numU){
		console.log("addUnit");
		console.log(numU);
		var mU = document.getElementById('material2'+numU);
		var newdivU=document.getElementById('unit'+numU);
		var strUserU = mU.options[mU.selectedIndex].text;
		console.log(strUserU);
		xmlhttp= new XMLHttpRequest();
		xmlhttp.open("GET", "tUnitUpdate.php?material="+strUserU, false);
		xmlhttp.send();
		console.log('the response starts');
		console.log(xmlhttp.responseText);
		console.log('the response ends');
        
		newdivU.innerHTML="<label style=\"font-size:12pt\"  id='unit"+numU+"'>"+ xmlhttp.responseText +" </label>  ";
	}

	function addAcre(numA){
		console.log(numA);
		var eA = document.getElementById('field'+numA);
		var bA = document.getElementById('maxBed2'+numA);
		console.log(bA);
		var newdiv=document.getElementById('acre'+numA);
		var strUser = eA.value;
		var strUser2= bA.value;
		console.log(strUser);
		console.log(strUser2);
		xmlhttp= new XMLHttpRequest();
		xmlhttp.open("GET", "tAcreUpdate.php?field="+strUser+"&beds="+strUser2, false);
		xmlhttp.send();
      //console.log('the response starts');
      //console.log(xmlhttp.responseText);
      //console.log('the response ends');
		newdiv.value=xmlhttp.responseText;
//        newdiv.innerHTML="<select id= 'maxBed<?php echo $numFieldInd  ?>' name= 'maxBed'>"+xmlhttp.responseText+"</select>";
	
	}

	function calculateTotal(){
		var ind=1;
		var totalFieldAcre=0;
		var maxField= numRows;
	
		while(ind<= maxField){
			var eachFieldAcre=document.getElementById('acre'+ind).value;
			totalFieldAcre=parseFloat(totalFieldAcre)+ parseFloat(eachFieldAcre);
			ind++;
		}	
		return totalFieldAcre;
	}
//        var formatTotalFieldAcre=totalFieldAcre.toFixed(2); 
	//console.log('the Acreresponse starts');
	//console.log(totalFieldAcre);
        //console.log('the Acreresponse ends');
	
//input -1 when just input water
	function calculateWater() {
		var w = document.getElementById('waterPerAcre');
		var newdivW=document.getElementById('totalWater');
		newdivW.value=(calculateTotal() * w.value).toFixed(2);
	}
	function calculateTotalUpdate() {
		var num = 1;
		while(num <= numRowsMat){
			calculateSuggested(num);
			num++;
		}
	} 
	function calculateSuggested(numS) {	
		var mC = document.getElementById('rate2'+numS);
		var strUser = mC.options[mC.selectedIndex].value;
		var newdivC=document.getElementById('calculatedTotal'+numS);
	//console.log('THE NUMBERS STARTS');
	//console.log(totalFieldAcre);
		var integer= parseFloat(strUser).toFixed(2);
	//console.log(strUser);
	//console.log("THE NUMBERS ENDS!!!");
	
		newdivC.value= (calculateTotal() * strUser).toFixed(2);
	}
	
	function checkIfFilled(){
		var fIndex=1;	
		var mIndex=1;
		while(fIndex<= numRows){
			var currentF=document.getElementById('field'+fIndex);
			if(currentF.value==0){
				//console.log('AAAAA undefined!!!');
				return false;
			}
		fIndex++;
		}	
	
		while(mIndex<= numRowsMat){
			var currentM=document.getElementById('material2'+mIndex);
			var currentAct=document.getElementById('actuarialTotal'+mIndex).value;
	//console.log("this is the value "+currentM.value);
			if(currentM.value==0 || isNaN(parseFloat(currentAct)) ){
				//console.log('BBBBB undefined!!!');
				return false;
			}
			mIndex++;
		}	
		var currentCG=document.getElementById("cropGroup2");
		if(currentCG.value==0){
	//console.log('DDDDD undefined');
			return false;
		}		

	//console.log("DDDDefined");
		return true;
	}

	function show_confirm(){
		if(checkIfFilled()){
			var numRow = document.getElementById("numField");
			numRow.value = numRows;
			var numMat = document.getElementById("numMaterial");
			numMat.value = numRowsMat;
			return confirm("Confirm submit?");
		}else{
			alert('Please enter all data!');
		return false;
		}
	}
