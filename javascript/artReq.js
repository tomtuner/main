//Added by AK  on Feb 11 2009
var arrInput = new Array(0);
var arrInputValue = new Array(0);

function addMoreFiles() {
	arrInput.push(arrInput.length);
	arrInputValue.push("");
	display();
}

function display() {
	document.getElementById('AreaAddMore').innerHTML="";
	for (intI=0;intI<arrInput.length;intI++) {
		document.getElementById('AreaAddMore').innerHTML+=createInput(arrInput[intI], arrInputValue[intI]);
	}
}

function saveValue(intId,strValue) {
	arrInputValue[intId]=strValue;
}  

function createInput(id,value) {
	return "Choose a file to upload: <input type='file' name='sent_file" + id +"' onChange='javascript:saveValue("+ id +",this.value)' ><br>";
}