// Events ajax loader
function getAjaxPage(url,parent) {	
	xmlHttp = GetXmlHttpObject();
	if (xmlHttp==null) {
		alert ('Browser does not support HTTP Request');
		return;
	}
	
	parent = document.getElementById(parent);
	
	var loading = document.createElement("li");
	parent.appendChild(loading);
	
	xmlHttp.onreadystatechange = function() {
		switch (xmlHttp.readyState) {
			case 0: loading.innerHTML = "Initializing..."; break;
			case 1: loading.innerHTML = "Downloading events..."; break;
			case 2: loading.innerHTMl = "Download finished. Prepearing to load events..."; break;
			case 3: loading.innerHTML = "Loading..."; break;
			case 4:
				parent.removeChild(loading); // Remove loading li
				var list = xmlHttp.responseText.split("\n"); // create an array  based on the response string
			
				for (var i=0;i<list.length;i++) {
					var li_event = document.createElement("li");
					li_event.innerHTML = list[i];
					parent.appendChild(li_event);
				}
				
				break;
		} 
	}
	
	xmlHttp.open('GET',url,true);
	xmlHttp.send(null);
}

function putAjax(url,id) {
	xmlHttp = GetXmlHttpObject();
	if (xmlHttp==null) {
		alert ('Browser does not support HTTP Request');
		return;
	}
	
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			document.getElementById(id).innerHTML = xmlHttp.responseText;
		}
	}
	
	xmlHttp.open('GET',url,true);
	xmlHttp.send(null);
}

function GetXmlHttpObject() { 
	var objXMLHttp = null;
	if (window.XMLHttpRequest) {
		objXMLHttp=new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	return objXMLHttp;
}