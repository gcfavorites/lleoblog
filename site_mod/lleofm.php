<?php /* произведения

{_LLEOT: текст _}

http://radio.vesti.ru/fm/doc.html?id=443703
http://www.vesti.ru/doc.html?id=443703
http://radio.vesti.ru/xml/get_audio.html?id=19042&a=1
http://www.vesti.ru/xml/get_audio.html?id=19042&a=1
wma: http://www.vesti.ru/xml/get_audio.html?id=19042


*/

function LLEOT($e) { list($page,$mp3)=explode(' ',$e);
	$conf=array_merge(array(
't'=>"<img src='/t' width='1' height='1'>",
'color'=>"red",
'background'=>"/fon1.jpg",
'W'=>5,
'H'=>0,
'Htmpl'=>"<div><img src='/t' width='1' height='{H}'></div>",
'size'=>1,
'template'=>"<table cellspacing='0' cellpadding='0' border='0'>"
."<tr><td>{t}</td><td colspan='3' bgcolor='{color}'>{t}</td><td>{t}</td></tr>"
."<tr><td width='{size}' BGCOLOR='{color}'>{t}</td><td width='{W}'>{t}</td>"
."<td background='{background}'>{HH}{body}{HH}</td>"
."<td width='{W}'>{t}</td><td width='{size}' bgcolor='{color}'>{t}</td></tr>"
."<tr><td>{t}</td><td colspan=3 bgcolor='{color}'>{t}</td><td>{t}</td></tr>"
."</table>"
	),parse_e_conf($e));

$conf['HH']=($conf['H']==0?'':mpr('Htmpl',$conf));

//dier($conf);

	return mpr('template',$conf);
}
?>
