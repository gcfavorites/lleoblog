var bV=parseInt(navigator.appVersion);
var ns=(document.layers) ? 1 : 0;
var ie=((document.getElementById) && (bV >= 4)) ? 1 : 0;
var ff = ((navigator.appName == "Netscape") && (bV >= 5)) ? 1 : 0;
var flag = (ns || ie) ? 1 : 0;
var timerID;
var timerIDl;
var timerIDr;
var kurs = -0.5;
var nomer = 0;
var lasta = 8;
var egx = 0;
var egy = 0;
var egvx = 4;
var egvy = 2;
var tut = 200;

var vmin = 1;
var vmax = 10;
var vr = 5;


function startFlakes(){
	getWindowSize();
		egx = pageW * Math.random() + pageX;
	   	egy = pageH * Math.random() + pageY;
}

function getWindowSize() {
	if(ns || ff) {
		pageX=window.pageXOffset;
		pageW=window.innerWidth - 20 - 120;
		pageY=window.pageYOffset;
		pageH=window.innerHeight - 8 - 84;
		if (ff) pageH -= 4;
	} else {
		pageX=document.body.scrollLeft;
		pageW=document.body.offsetWidth - 20 - 120;
		pageY=document.body.scrollTop;
		pageH=document.body.offsetHeight - 8 - 84;
	} } 


function buzz() {
	hideLayer(flakes[ lasta ].name);
	getWindowSize();
	egx += egvx*kurs*6;
	egy += egvy*2;
	if(egy >= pageY + pageH) { egvy *= -1; egy = pageY + pageH; }
	if(egy <= pageY) { egvy = egvy *= -1; egy = pageY; }
	if (egx < pageX) { egx = pageX; }
	if (egx > pageX + pageW) { egx = pageX + pageW; }
		
	egvx += vr * (Math.random()-0.5);
	if(egvx > vmax)  { egvx = vmax*2 - egvx; }
	if(egvx < vmin) { egvx = vmin*2 - egvx; }
	egvy += (Math.random()-0.5);
	if((egvy > (vmax/2))||(egvy < (-vmax)/2))  { egvy = 0; }
	
if (	(Math.random() > 0.96) || (egx <= pageX) || (egx >= pageX + pageW)	) {
	  showLayer(flakes[ lasta ].name);
	  kurs *= -1;
	  timerID = setTimeout("buzz()", tut*2);
	  
	  if(kurs < 0) { buzr(); timerIDr = setTimeout("buzl()", tut); }
	  else { buzl(); timerIDr = setTimeout("buzr()", tut); }
} else {	
	nomer=nomer+2; if(nomer >= 8) nomer = nomer-8;
   	lasta=nomer+0.5+kurs;
		moveLayer(flakes[ lasta ].name, egx, egy);
		showLayer(flakes[ lasta ].name);	
		timerID=setTimeout("buzz()",tut);		
}
}


function buzl() {
   hideLayer(flakes[ lasta ].name);
	lasta = 8;
   moveLayer(flakes[ lasta ].name, egx, egy);
	showLayer(flakes[ lasta ].name);	
}

function buzr() {
   hideLayer(flakes[ lasta ].name);
   lasta = 9;
	moveLayer(flakes[ lasta ].name, egx, egy);
	showLayer(flakes[ lasta ].name);	
}
