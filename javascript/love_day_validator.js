function lovedayValidator(theForm){
var loveday_errors="";

if(document.loveday_form.clubname.value=='')
	loveday_errors+="Please enter a Club Name.\n";

if(document.loveday_form.contactname.value=='')
	loveday_errors+="Please enter a Contact Name.\n";

if(document.loveday_form.contactemail.value=='')
	loveday_errors+="Please enter a Contact Email.\n";

if ((document.loveday_form.contactemail.value.indexOf ('@',0) == -1 || document.loveday_form.contactemail.value.indexOf ('.',0) == -1) &&
   document.loveday_form.contactemail.value != "")
    loveday_errors+= 'Please verify that your Contact Email is valid.\n';	

if(document.loveday_form.activity.value=='')
	loveday_errors+="Please enter a description of the activity.\n";

if (loveday_errors != "")
  {
    alert(loveday_errors);
    return (false);
  } else {
    return (true);
  } 
	
}