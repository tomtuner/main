/* Copyright 2009 Bret Taylor */

(function(){var u=window.innerWidth||(document.documentElement&&document.documentElement.clientWidth);if(!window.__duckMyLife&&u){window.__duckMyLife=true;var t=9;var k=4000;var l=62;var h="http://www.duckmylife.com/static/";var o="";var p="__duckMyLifePlayer";var a=null;var m=null;var b=null;var s=null;var v=null;function e(z,y,w,B){var A=document.createElement("div");var x=A.style;x.position="fixed";x.border="0";x.padding="0";x.left="0";x.top="0";x.lineHeight="0";x.width=y+"px";x.height=w+"px";x.background="url('"+h+z+o+"') no-repeat top left";x.zIndex=100000+(B||0);x.display="none";document.body.appendChild(A);return A}function j(){if(window.innerWidth){return{width:window.innerWidth,height:window.innerHeight}}return{width:document.documentElement.clientWidth,height:document.documentElement.clientHeight}}function c(){return(new Date()).getTime()}function g(){var y=j();v=Math.round(Math.random()*y.height/2);a.style.left=(y.width+l)+"px";a.style.top=v+"px";a.style.display="";if(s){window.clearInterval(s)}var z=c();var x=y.width+l;var w=y.width*9;s=window.setInterval(function(){var A=(c()-z)/w;if(A>=1){window.clearInterval(s);a.style.display="none";window.setTimeout(g,k);return}a.style.left=Math.round((x+l)*(1-A)-l)+"px"},10)}function n(w){window.clearInterval(s);var w=w||window.event;b.style.left=(w.clientX-20)+"px";b.style.top=(w.clientY-20)+"px";b.style.display="";d("gunshot");window.setTimeout(q,150);window.setTimeout(function(){d("quack")},500)}function q(y){a.style.display="none";m.style.left=a.style.left;m.style.top=a.style.top;m.style.display="";var x=c();var w=(j().height-v)*(t*0.4);window.setTimeout(i,1000);s=window.setInterval(function(){var z=(c()-x)/w;if(z>=1){window.clearInterval(s);m.style.display="none";window.setTimeout(g,k);return}m.style.top=Math.round(v+z*(j().height-v))+"px"},10)}function i(){var w=c();fadeInterval=window.setInterval(function(){var x=(c()-w)/500;if(x>=1){b.style.display="none";window.clearInterval(fadeInterval);b.style.opacity=1;b.style.filter="alpha(opacity=100)";return}b.style.opacity=1-x;b.style.filter="alpha(opacity="+Math.round(100*(1-x))+")"},10)}function f(){a=e("duck.png",l,97);m=e("duck-dead.png",l,97);b=e("gunshot.png",40,40,1);r(p,h+"DuckMyLife.swf"+o);a.style.cursor="crosshair";a.onclick=n;g()}function r(A,x){var z="1px";var w="1px";var y=document.createElement("div");y.style.width=z;y.style.height=w;document.getElementsByTagName("body")[0].appendChild(y);if(navigator.plugins&&navigator.mimeTypes&&navigator.mimeTypes.length){y.innerHTML='<embed type="application/x-shockwave-flash" src="'+x+'" width="'+z+'" height="'+w+'" id="'+A+'" name="'+A+'" wmode="transparent" quality="high" menu="false" allowScriptAccess="always"/>'}else{y.innerHTML='<object id="'+A+'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+z+'" height="'+w+'"><param name="movie" value="'+x+'"><param name="wmode" value="transparent"/><param name="quality" value="high"/><param name="menu" value="false"/><param name="allowScriptAccess" value="always"/></object>'}}function d(w){var x=document.getElementById(p);if(!x){return}try{x[w]()}catch(y){}}window.setTimeout(f,100)}})();