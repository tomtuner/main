function setSearchValue()
{
	document.getElementById("cclSearchBox").value = "Search CCL Site";
}

function resetSearchValue()
{
	document.getElementById("cclSearchBox").value = "";
}

//SAU Reservation begins here..
function domConfig()
{
	//Everything in here are global variables	
	name = document.getElementById('name');
	phone = document.getElementById('phone');
	email = document.getElementById('email');
	department = document.getElementById('department');
	
	totalRequests = document.getElementById('totalRequests');
	eventName = new Array( totalRequests );
	eventDate = new Array( totalRequests );
	roomChoiceOne = new Array( totalRequests );
	roomChoiceTwo = new Array( totalRequests );
	startTime = new Array( totalRequests );
	endTime = new Array( totalRequests );
	peopleExpected = new Array( totalRequests );
	
	for( var i=1; i<=totalRequests.value; i++ )
	{
		eventName[i] = document.getElementById( "eventName" + i );
		eventDate[i] = document.getElementById( "eventDate" + i );
		roomChoiceOne[i] = document.getElementById( "roomChoiceOne" + i );
		roomChoiceTwo[i] = document.getElementById( "roomChoiceTwo" + i );
		startTime[i] = document.getElementById( "startTime" + i );
		endTime[i] = document.getElementById( "endTime" + i );
		peopleExpected[i] = document.getElementById( "peopleExpected" + i );
		if( i == totalRequests.value )
		{
			//Undefined values couldn't be sent to the server side
			dumpEmptyValues();
		}
	}
}

function dumpEmptyValues()
{
	for( var i=Number(totalRequests.value)+1; i<=4; i++ )
	{
		eventName[i] = "";
		eventDate[i] = "";
		roomChoiceOne[i] = "";
		roomChoiceTwo[i] = "";
		startTime[i] = "";
		endTime[i] = "";
		peopleExpected[i] = "";
	}
}

function showRequests( totalRequests )
{
	//Global holder
	holder = document.getElementById('requestsHolder');
	var requestsContainer = "";
	holder.style.display = "block";
	
	/*
	//Generic way of doing.. but pain in the butt..
	for( var i=1; i<=totalRequests; i++ )
	{
		var singleHolder = document.createElement("div");
		singleHolder.setAttribute('class', 'request');
		var titleStyle = document.createElement("strong");
		var titleText = document.createTextNode("Request Form No. " + i);
		var newLine = document.createElement("br");
		
		//Display stuff comes here..
		titleStyle.appendChild( titleText );
		singleHolder.appendChild( titleStyle );
		holder.appendChild( singleHolder );
	}
	*/
	
	//Messy innerHTML but makes your life much easier
	for( var i=1; i<=totalRequests; i++ )
	{
		requestsContainer += "<div class='request'>" +
		"<strong>Request Form No. " + i + "</strong><br/><br/>" +
		"Event Name:<br/><input type='text' size='25' id='eventName" + i + "' name='eventName" + i + "' /><br/><br/>" +
		"Date(s) of Event:<br/><input type='text' size='25' id='eventDate" + i +"' name='eventDate" + i +"' /><br/><br/>" +
		"Room Requested - 1st Choice<br/>" +
		"<select id='roomChoiceOne" + i + "' name='roomChoiceOne" + i + "'>" +
		"<option value=''>-- Please choose a Room --</option>" +
		"<option value='1829 Room'>(4)1829 Room</option>"	+
		"<option value='Alumni Conference Room'>(4)Alumni Conference Room</option>" +
		"<option value='Ingle Auditorium'>(4)Ingle Auditorium</option>" +
		"<option value='One SAU Lobby Table'>(4)One SAU Table</option>"	+
		"<option value='SAU Lobby'>(4)Entire SAU Lobby</option>"	+
		"<option value='(3)A640'>(3)A640</option>"	+
		"<option value='(3)A650'>(3)A650</option>"	+
		"<option value='(3)A640/A650'>(3)A640/A650</option>"	+
		"<option value='(3)1000'>(3)1000</option>"	+
		"<option value='(3)1010'>(3)1010</option>"	+
		"<option value='(3)1015'>(3)1015</option>"	+
		"<option value='(3)1010/1015'>(3)1010/1015</option>"	+
		"<option value='(3)2610 (Bamboo Room 1)'>(3)2610 (Bamboo Room 1)</option>"	+
		"<option value='(3)2650 (Bamboo Room 2)'>(3)2650 (Bamboo Room 2)</option>"	+
		"<option value='(3)2610/2650 (Both Bamboo Rooms)'>(3)2610/2650 (Both Bamboo Rooms)</option>"	+
		"<option value='(3)2540'>(3)2640</option>"+
		
		"</select>" +
		"<br/><br/>" +

		"Room Requested - 2nd Choice<br/>" +
		"<select id='roomChoiceTwo" + i + "' name='roomChoiceTwo" + i + "'>" +
		"<option value=''>-- Please select a second choice(Optional) --</option>" +
		"<option value='1829 Room'>(4)1829 Room</option>"	+
		"<option value='Alumni Conference Room'>(4)Alumni Conference Room</option>" +
		"<option value='Ingle Auditorium'>(4)Ingle Auditorium</option>" +
		"<option value='One SAU Lobby Table'>(4)One SAU Table</option>"	+
		"<option value='SAU Lobby'>(4)Entire SAU Lobby</option>"	+
		"<option value='(3)A640'>(3)A640</option>"	+
		"<option value='(3)A650'>(3)A650</option>"	+
		"<option value='(3)A640/A650'>(3)A640/A650</option>"	+
		"<option value='(3)1000'>(3)1000</option>"	+
		"<option value='(3)1010'>(3)1010</option>"	+
		"<option value='(3)1015'>(3)1015</option>"	+
		"<option value='(3)1010/1015'>(3)1010/1015</option>"	+
		"<option value='(3)2610 (Bamboo Room 1)'>(3)2610 (Bamboo Room 1)</option>"	+
		"<option value='(3)2650 (Bamboo Room 2)'>(3)2650 (Bamboo Room 2)</option>"	+
		"<option value='(3)2610/2650 (Both Bamboo Rooms)'>(3)2610/2650 (Both Bamboo Rooms)</option>"	+
		"<option value='(3)2540'>(3)2640</option>"+
		"</select>" +
		"<br/><br/>" +

		"Start Time:	<br/><input type='text' size='20' id='startTime" + i + "' name='startTime" + i + "'/> <br/><br/>End Time: <br/><input type='text' size='20' id='endTime" + i +"' name='endTime" + i +"'/><br/><br/>" +
		"People Expected: <br/><input type='text' size='10' id='peopleExpected" + i + "' name='peopleExpected" + i + "'/>" +
		"</div><br/><br/>";
	}

	requestsContainer += "<input type='reset' value='Reset all values' /> <input type='submit' value='Submit Reservation Request' onclick='return validateRequests()'/>"
	holder.innerHTML = requestsContainer;
}

function validateRequests()
{
	domConfig();
	
	var alertMessage = "Please enter the following: \n\n";
	var isValidated = true;
	
	if( name.value == '' )
	{
		alertMessage += "Your Name\n";
		isValidated = false;
	}	
	if( phone.value == '' )
	{
		alertMessage += "Your Phone\n";
		isValidated = false;
	}	
	if( email.value == '' )
	{
		alertMessage += "Your E.Mail\n";
		isValidated = false;
	}	
	if( department.value == '' )
	{
		alertMessage += "Your Department\n";
		isValidated = false;
	}	
	
	alertMessage += "\n";

	if( totalRequests.value == '' )
	{
		alertMessage += "Total requests\n\n";
		isValidated = false;
	}	

	
	for( var i=1; i <= totalRequests.value; i++ )
	{
		if( eventName[i].value == '' )
		{
			alertMessage += "Event Name" + " (Request " + i + ")\n";
			isValidated = false;
		}	
		if( eventDate[i].value == '' )
		{
			alertMessage += "Date(s) of Event" + " (Request " + i + ")\n";
			isValidated = false;
		}	
		if( roomChoiceOne[i].value == '' )
		{
			alertMessage += "Room Requested - 1st Choice" + " (Request " + i + ")\n";
			isValidated = false;
		}	
		//if( roomChoiceTwo[i].value == '' )
		//{
		//	alertMessage += "Room Requested - 2nd Choice" + " (Request " + i + ")\n";
		//	isValidated = false;
		//}	
		if( startTime[i].value == '' )
		{
			alertMessage += "Start Time" + " (Request " + i + ")\n";
			isValidated = false;
		}	
		if( endTime[i].value == '' )
		{
			alertMessage += "End Time" + " (Request " + i + ")\n";
			isValidated = false;
		}	
		if( peopleExpected[i].value == '' )
		{
			alertMessage += "People Expected" + " (Request " + i + ")\n";
			isValidated = false;
		}	
		alertMessage += "\n";
	}
		
	//Form is ready to  submit
	if( isValidated == true)
	{
		submitRequests();
	}
	//Alert the missed fields
	else
	{
		alert( alertMessage );
	}
	return false;
}

function submitRequests()
{
	new Ajax.Request('sau/mailReservation', {
	  method: 'post',
	  parameters: {name: name.value, phone: phone.value, email: email.value, department: department.value, totalRequests: totalRequests.value, eventName1: eventName[1].value, eventName2: eventName[2].value, eventName3: eventName[3].value, eventName4: eventName[4].value, eventDate1: eventDate[1].value, eventDate2: eventDate[2].value, eventDate3: eventDate[3].value, eventDate4: eventDate[4].value, roomChoiceOne1: roomChoiceOne[1].value, roomChoiceOne2: roomChoiceOne[2].value, roomChoiceOne3: roomChoiceOne[3].value, roomChoiceOne4: roomChoiceOne[4].value, roomChoiceTwo1: roomChoiceTwo[1].value, roomChoiceTwo2: roomChoiceTwo[2].value, roomChoiceTwo3: roomChoiceTwo[3].value, roomChoiceTwo4: roomChoiceTwo[4].value, startTime1: startTime[1].value, startTime2: startTime[2].value, startTime3: startTime[3].value, startTime4: startTime[4].value, endTime1: endTime[1].value, endTime2: endTime[2].value, endTime3: endTime[3].value, endTime4: endTime[4].value, peopleExpected1: peopleExpected[1].value, peopleExpected2: peopleExpected[2].value, peopleExpected3: peopleExpected[3].value, peopleExpected4: peopleExpected[4].value, limit: 50}
	  });
	  
	holder.style.display = "block";
	holder.innerHTML = "<div id='message'>Your reservation request has been submitted. Thanks !</div>";
}
// SAU Reservation ends here..