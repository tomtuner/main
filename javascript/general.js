	var menus = Array("about", "clubs", "form_club", "fratsorlife", "specialevents", "sau", "techcrew", "advisorinfo","thesource");
	var openMenu = null;

	function beginDelayedCollapse(menu) {
	
		delayTimeout = setTimeout("showMenu('" + menu + "');", 500);
	}

	function cancelDelayedCollapse(menu) {

		clearTimeout(delayTimeout);
	}
	
	function showMenu(menu) {
		if (openMenu != menu)  {
			if (openMenu != null) {
				// hide open menu
				var closeMe = document.getElementById(openMenu);
				closeMe.style.height = 0;
				closeMe.style.display= "none";
			}
			// we need to calculate a "grown" height.
			var elt = document.getElementById(menu);
			elt.style.display = "block";
			 //stupid IE7 + YUI hack.  For some reason after animation completed
			//internet explorer 7 would clip the menu text over to the left 30px;
			if (getInternetExplorerVersion() >= 6) {
				 YAHOO.util.Dom.setStyle(elt, 'padding-left', '30px'); 
				YAHOO.util.Dom.setStyle(elt, 'margin-left', '0'); 
			}
			// how many LI items under this menu?
			var count = elt.getElementsByTagName("li").length;
			var menuHeight = ( count * 21 ) + 5;
			if (getInternetExplorerVersion() >= 6) {
				menuHeight = menuHeight + 21;
				}
			
			var anim = new YAHOO.util.Anim(menu, { height: {to: menuHeight}, opacity: {to: 100} }, 1, YAHOO.util.Easing.easeOut);
			anim.animate();
			
			openMenu = menu;
		}
	}
			
	function setup() {

		showMenu(currentMenu);
		
	}		


	// Returns the version of Internet Explorer or a -1
				// (indicating the use of another browser).
	function getInternetExplorerVersion()
	{
	  var rv = -1; // Return value assumes failure
	  if (navigator.appName == 'Microsoft Internet Explorer')
	  {
		var ua = navigator.userAgent;
		var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
		if (re.exec(ua) != null)
		  rv = parseFloat( RegExp.$1 );
	  }
	  return rv;
	}
		
		/*-----------------------------------------------------------
		Toggles element's display value
		Input: any number of element id's
		Output: none 
		---------------------------------------------------------*/
	function toggleDisp() {
		for (var i=0;i<arguments.length;i++){
			var d = $(arguments[i]);
			if (d.style.display == 'none')
				d.style.display = 'block';
			else
				d.style.display = 'none';
		}
	}
	/*-----------------------------------------------------------
		Toggles tabs - Closes any open tabs, and then opens current tab
		Input:     1.The number of the current tab
						2.The number of tabs
						3.(optional)The number of the tab to leave open
						4.(optional)Pass in true or false whether or not to animate the open/close of the tabs
		Output: none 
		---------------------------------------------------------*/

	function toggleTab(num,numelems,opennum,animate) 
	{
		if ($('tabContent'+num).style.display == 'none'){
			for (var i=1;i<=numelems;i++){
				if ((opennum == null) || (opennum != i)){
					var temph = 'tabHeader'+i;
					var h = $(temph);
					if (!h){
						var h = $('tabHeaderActive');
						h.id = temph;
					}
					
					var tempc = 'tabContent'+i;
					var c = $(tempc);
					if(c.style.display != 'none'){
						if (animate || typeof animate == 'undefined')
							Effect.toggle(tempc,'blind',{duration:0.5, queue:{scope:'menus', limit: 3}});
						else
							toggleDisp(tempc);
					}
				}
				$('tabHeader'+i).className = "deActiveHeader";
			}
			var h = $('tabHeader'+num);
			if (h)
				h.id = 'tabHeaderActive';
			//h.blur();
			var c = $('tabContent'+num);
		   c.style.marginTop = '2px';
			if (animate || typeof animate == 'undefined'){
				Effect.toggle('tabContent'+num,'blind',{duration:0.5, queue:{scope:'menus', position:'end', limit: 3}});
			}else{
				toggleDisp('tabContent'+num);
			}
		}
		//Give a different color for the active header
		$("tabHeaderActive").className = "activeHeader";
	}


