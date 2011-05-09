<?php

//if(substr($_SERVER['QUERY_STRING'],0,2)!='l=') exit;
include "../config.php";
//include $include_sys."_autorize.php";

//$httphost='http://lleo.aha.ru/blog/';

$text=h(preg_replace("/[\n\r\t ]+/si"," ",uw($_GET['t'])));
$link=h($_GET['l']);
$date=intval($_GET['d']);

if(isset($_GET['m'])) {	$mode=h($_GET['m']);
//	header("Content-Type: image/png"); die(file_get_contents("../re.png"));

//if($mode=='')

$s="<script>
function lleoblogpanel_f5_sup(){ return ('localStorage' in window) && window['localStorage'] !== null; }
function lleoblogpanel_f5_save(n,v){ if(!lleoblogpanel_f5_sup()) return false; window['localStorage'][n]=v;return lleoblogpanel_f5_read(n)==v?true:false; }
function lleoblogpanel_f5_read(n){ if(!lleoblogpanel_f5_sup()) return false; var v=window['localStorage'][n]; return v==undefined ? '' : v; }

//if(lleoblogpanel_f5_read('clipboard_mode')!='') alert('mode='+lleoblogpanel_f5_read('clipboard_mode'));
//if(lleoblogpanel_f5_read('clipboard_link')!='') alert('link='+lleoblogpanel_f5_read('clipboard_link'));
//if(lleoblogpanel_f5_read('clipboard_text')!='') alert('text='+lleoblogpanel_f5_read('clipboard_text'));

lleoblogpanel_f5_save('clipboard_mode',\"".$mode."\");
lleoblogpanel_f5_save('clipboard_link',\"".$link."\");
lleoblogpanel_f5_save('clipboard_text',\"".$text."\");
</script>";

die("<html><body bgcolor=red>&nbsp;$s</body></html>");
}


//file_put_contents('m.txt',"\n1234");

$s="link: <a href='$link'>$link</a><br>$text";
$close="<div onclick='lleoblogpanel_clean(\\\"lleoblogpanel\\\")' style='cursor:pointer;color:blue;position:absolute;top:3px;right:3px;'>close</div>";
// onclick=\\\'alert(1)\\\'
//$s="EEEEEEe";

$s="<input id='lleoblogpanel_link' size=50 value='$link'> <input id='lleoblogpanel_date' size=3 value='3'>
<br><textarea id='lleoblogpanel_text' cols=50 rows=".max(4,page($text,50)).">$text</textarea>
<br><input type=button value='Copy link' onclick='lleoblogpanel_send(this)'>
 <input type=button value='Matom!' onclick='lleoblogpanel_send(this)'>
 <input type=button value='Readability' onclick='lleoblogpanel_send(this)'>
 <input type=button value='Black&White' onclick='lleoblogpanel_send(this)'>
 <input type=button value='href' onclick='lleoblogpanel_send(this)'>
";

// <input type=button value='mail' onclick='lleoblogpanel_send(this)'>
// <input type=button value='mail' onclick='lleoblogpanel_send(this)'>
//  <input type=button value='Load image' onclick='lleoblogpanel_send(this)'>
// <input type=button value='Recomenda' onclick='lleoblogpanel_send(this)'>


$s=str_replace("\n","",$s);

/*
	if(e.value=='mail') {
	//	var s='mailto:'+window.prompt('Send to email:')+'?subject='+encodeURIComponent(lleoblogpanel_idd('lleoblogpanel_link').value)+'&Body='+encodeURIComponent(lleoblogpanel_idd('lleoblogpanel_text').value);
		var s='mailto:lleo@aha.ru';

		lleoblogpanel_mkdiv('lleoblogpanel_mailto',s);
		lleoblogpanel_idd('lleoblogpanel_mailto').click();

//	document.body.innerHTML='<a id=lleoblogpanel_mailto href=\"'+s+'\">EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE&nbsp;</a>';

//	lleoblogpanel_clean('lleoblogpanel_mailto');
//	lleoblogpanel_closeiframe();
	return;
	}
*/

die("

function lleoblogpanel_send(e) {

	if(e.value=='Black&White') {
		var elems = document.getElementsByTagName('*');
		for(var i=0;i<elems.length;i++) {
			elems[i].style.backgroundColor = '#fff';
			elems[i].style.backgroundImage = '';
			elems[i].style.color = '#000';
		}
	lleoblogpanel_closeiframe(); return;
	}

	if(e.value=='href') {
		var s='';
		var pp=document.getElementsByTagName('*');
		for(var i=0;i<pp.length;i++) {
			var l=pp[i].href; if(l && l.replace(/javascript\:/g,'')==l) s=s+'\\n'+l;
		}
		lleoblogpanel_idd('lleoblogpanel_text').value=s;
		return;
	}

	if(e.value=='Matom!') {
		lleoblogpanel_loadScript('mat.user.js');
	lleoblogpanel_closeiframe(); return;
	}

	if(e.value=='Readability') { 
		readStyle='style-newspaper';readSize='size-medium';readMargin='margin-wide';_readability_script=document.createElement('script');_readability_script.type='text/javascript';_readability_script.src='http://lab.arc90.com/experiments/readability/js/readability.js?x='+(Math.random());document.documentElement.appendChild(_readability_script);_readability_css=document.createElement('link');_readability_css.rel='stylesheet';_readability_css.href='http://lab.arc90.com/experiments/readability/css/readability.css';_readability_css.type='text/css';_readability_css.media='all';document.documentElement.appendChild(_readability_css);_readability_print_css=document.createElement('link');_readability_print_css.rel='stylesheet';_readability_print_css.href='http://lab.arc90.com/experiments/readability/css/readability-print.css';_readability_print_css.media='print';_readability_print_css.type='text/css';document.getElementsByTagName('head')[0].appendChild(_readability_print_css);
	lleoblogpanel_closeiframe(); return;
	}

var link='".$httphost."ajax/m.php?m='+encodeURIComponent(e.value)+'&d='+encodeURIComponent(lleoblogpanel_idd('lleoblogpanel_date').value)+'&l='+encodeURIComponent(lleoblogpanel_idd('lleoblogpanel_link').value)+'&t='+encodeURIComponent(lleoblogpanel_idd('lleoblogpanel_text').value);
var q=lleoblogpanel_idd('lleoblogpanelc');
q.innerHTML=\"<iframe src='\"+link+\"' width='10' height='10' onload='lleoblogpanel_closeiframe()'></iframe>\"+q.innerHTML;
lleoblogpanel_closeiframe(); return;
}

function lleoblogpanel_loadScript(src){ src='".$httphost."js/'+src;
        var s = document.createElement('script');
        s.setAttribute('type', 'text/javascript');
        s.setAttribute('charset', '".$wwwcharset."');
        s.setAttribute('src', src);
        var head = document.getElementsByTagName('head').item(0);
        head.insertBefore(s, head.firstChild);
}


function lleoblogpanel_mkdiv(id,cont,cls,paren){ if(lleoblogpanel_idd(id)) { lleoblogpanel_idd(id).innerHTML=cont; lleoblogpanel_idd(id).className=cls; return; }
        var div=document.createElement('DIV'); div.className=cls; div.id=id; div.innerHTML=cont; div.style.display='none';
        if(paren==undefined) paren=document.body; paren.insertBefore(div,paren.lastChild);
}

function lleoblogpanel_closeiframe() { setTimeout('lleoblogpanel_zakryl(\"lleoblogpanel\")',500); }
function lleoblogpanel_idd(id) { return document.getElementById(id); }
function lleoblogpanel_zabil(id,text) { lleoblogpanel_idd(id).innerHTML = text; }
// function lleoblogpanel_vzyal(id) { return lleoblogpanel_idd(id).innerHTML; }
function lleoblogpanel_zakryl(id) { lleoblogpanel_idd(id).style.display='none'; }
function lleoblogpanel_otkryl(id) { lleoblogpanel_idd(id).style.display='block'; }
function lleoblogpanel_getScrollH(){ return (document.documentElement.scrollTop || document.body.scrollTop); }
function lleoblogpanel_getScrollW(){ return (document.documentElement.scrollLeft || document.body.scrollLeft); }
function lleoblogpanel_getWinW(){ return window.innerWidth?window.innerWidth : document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth; }
function lleoblogpanel_getWinH(){ return window.innerHeight?window.innerHeight : document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight; }
function lleoblogpanel_getDocH(){ return document.compatMode!='CSS1Compat' ? document.body.scrollHeight : document.documentElement.scrollHeight; }
function lleoblogpanel_clean(id){ if(lleoblogpanel_idd(id)){ lleoblogpanel_zakryl(id); 
// setTimeout(\"var s=lleoblogpanel_idd('\"+id+\"'); if(s) s.parentNode.removeChild(s);\",40); 
}}

function lleoblogpanel_posdiv(id){ lleoblogpanel_otkryl(id);
var e=lleoblogpanel_idd(id),W=lleoblogpanel_getWinW(),H=lleoblogpanel_getWinH(),w=e.clientWidth,h=e.clientHeight;
var x=(W-w)/2+lleoblogpanel_getScrollW(),y=(H-h)/2+lleoblogpanel_getScrollH();
var DH=W-10; if(w<DH && x+w>DH) x=DH-w; if(x<0) x=0;
    DH=lleoblogpanel_getDocH()-10; if(h<DH && y+h>DH) y=DH-h; if(y<0) y=0;
e.style.top=y+'px'; e.style.left=x+'px';
}

var s=\"$s\";

if(!lleoblogpanel_idd('lleoblogpanel'))
document.body.innerHTML+=\"<div id='lleoblogpanel' style='position:absolute;z-index:99999;border:20px solid black;padding: 20px; background-color: rgb(255,252,223); text-align:justify;'>$close<div id='lleoblogpanelc'>\"+s+\"</div></div>\";
else { lleoblogpanel_zabil('lleoblogpanelc',s); lleoblogpanel_otkryl('lleoblogpanel'); }
lleoblogpanel_posdiv('lleoblogpanel',-1,-1);
");

function uw($s) { return iconv("utf-8","windows-1251//IGNORE",$s); }
function h($s) { return htmlspecialchars($s); }
function page($l,$c=50) { $m=split("\n",$l); $i=0; foreach($m as $t) if(strlen($t)<$c) $i++; else $i=$i+1+(floor(strlen($t)/$c)); return($i); }

//if($link!='') {
//	if($text=='') $text=$link;
//	msq_add_update('rekomenda',array('link'=>e($link),'text'=>e($text)),'link');
//}
//header("Content-Type: image/png"); die(file_get_contents("re.png"));

?>
