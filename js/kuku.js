playswf=function(s){ var a=www_design+'kuku/'+s; s=a+'.swf';
	mkdiv('plays',"<div style='position:absolute;width:1px;height:1px;overflow:hidden;left:-40px;top:0;opacity:0'>\
<audio autoplay='autoplay'>\
<source src='"+a+".ogg' type='audio/ogg; codecs=vorbis'>\
<source src='"+a+".mp3' type='audio/mpeg'>\
<object width='1' height='1' style='width:1px;height:1px;overflow:hidden;position:absolute;left:-400px;top:0;border:0;'><param name='movie' value='"+s+"' /><param name='loop' value='false' /><embed src='"+s+"' width='1' height='1' loop='false' type='application/x-shockwave-flash'></embed></object>\
</audio>\
</div>");
	otkryl('plays');
}

saytime=function(e){
	var d=new Date(); var t=d.valueOf();
	d.setSeconds(0); d.setMinutes(0); d.setHours(d.getHours()+1);
	var t=d.valueOf()-t; if(!t) t=1;
//	if(admin) t=1000; // http://w3pro.ru/article/radio-pleer-s-pomoshchyu-html5-audio
	setTimeout("playswf('s0');",t);
	setTimeout("playswf('s"+((Math.floor(Math.random()*10)+1)%24)+"')",t+2000);
	setTimeout("playswf('"+(new Date().getHours()+1)+"'); saytime();",t+4000);
}

saytime();
