var playsid=0;
playswf=function(a,silent){
var s=www_design+'mp3playerns.swf?autostart='+(silent?'no':'yes')+'&file='+a+'.mp3';
var id='plays'+(silent?playsid++:'');
mkdiv(id,"<div style='position:absolute;width:1px;height:1px;overflow:hidden;left:-40px;top:0;opacity:0'>\
<audio"+(silent?'':"autoplay='autoplay'")+">\
<source src='"+a+".ogg' type='audio/ogg; codecs=vorbis'>\
<source src='"+a+".mp3' type='audio/mpeg'>\
<object width='1' height='1' \
style='width:1px;height:1px;overflow:hidden;position:absolute;left:-400px;top:0;border:0;'>\
<param name='movie' value='"+s+"' />\
<embed src='"+s+"' width='1' height='1' loop='false' type='application/x-shockwave-flash'>\
</embed></object></audio></div>");
otkryl(id);
}

saytime=function(e){
	var d=new Date(); var t=d.valueOf();
	d.setSeconds(0); d.setMinutes(0); d.setHours(d.getHours()+1);
	var t=d.valueOf()-t; if(!t) t=1;
	// if(admin) t=8000; // http://w3pro.ru/article/radio-pleer-s-pomoshchyu-html5-audio
	var tp=Math.floor(t*3/4); // ���������� ������� �� 3/4 �����
	var pdz=www_design+'kukus/s'+(1+Math.floor(Math.random()*100)%10);
	var chas=www_design+'kukus/'+(1+new Date().getHours());
	if(tp && (t-tp)>3) { // ���� ������� ������ 3 ������ - ������� ��������������� ���������
	setTimeout("playswf(www_design+'kukus/s0',1); playswf('"+pdz+"',1); playswf('"+chas+"',1);",tp);
	}
	setTimeout("playswf(www_design+'kukus/s0')",t);
	setTimeout("playswf('"+pdz+"')",t+2000);
	setTimeout("playswf('"+chas+"'); saytime();",t+4000);
}

saytime();

setTimeout("playswf(www_design+'kladez/"+((Math.floor(Math.random()*100)+1)%27)+"')",120000+Math.floor(Math.random()*2000000));
