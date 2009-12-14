<?php // Выпадающее меню

if(!isset($GLOBALS['admin_name'])) { die(highlight_file(__FILE__,1)); }



/********************************************************************/
$GLOBALS['calfrus']=array(
'а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','ё',
'А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','Ё');

$GLOBALS['calftransl']=array(
'a','b','v','g','d','e','zj','z','i','j','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sch','','y','','e','u','ya','yo',
'a','b','v','g','d','e','zj','z','i','j','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sch','','y','','e','u','ya','yo',
);

$GLOBALS['calfrusl']=implode("",$GLOBALS['calfrus'])."\\-\\_\\.";

function translite($l) {
return str_replace($GLOBALS['calfrus'],$GLOBALS['calftransl'],trim(preg_replace("/[^".$GLOBALS['calfrusl']."]+/s","_",$l),"_"));
}

//********************************************************************/



STYLE_ADD($GLOBALS['httpsite'].$GLOBALS['www_design']."menu.css"); // подгрузить css выпадающего меню


SCRIPTS("Скрипт выпадающего меню","
jsHover = function() {
	var hEls = document.getElementById('nav').getElementsByTagName('LI');
	for (var i=0, len=hEls.length; i<len; i++) {
		hEls[i].onmouseover=function() { this.className+=' jshover'; }
		hEls[i].onmouseout=function() { this.className=this.className.replace(' jshover', ''); }
	}
}
if (window.attachEvent && navigator.userAgent.indexOf('Opera')==-1) window.attachEvent('onload', jsHover);
");


function MENU($e) { $a=explode("\n",$e);

	$pos=0;
	$m=array(0=> rtrim( $GLOBALS['wwwhost'] ,'/') );

foreach($a as $l) { $l=c($l); if($l!='') {
	$l1=ltrim($l,"-".chr(151)); $n=strlen($l)-strlen($l1);
	list($l1,$link)=explode('|',$l1); $l1=c($l1); $link=c($link);
		if($n==$pos) { $do=$dodo; }
		elseif($n<$pos) { $do=$dodo; for($i=$pos-$n;$i>0;$i--) { unset($m[$pos--]); $do.=MENU_pro($pos+1)."</ul>".MENU_pro($pos)."</li>"; }}
		else { $do=''; for($i=$n-$pos;$i>0;$i--) { $m[++$pos]=translite($l1last); $do.=MENU_pro($pos)."<ul>"; } }
	if($link=='') $link=implode("/",$m).'/'.translite($l1);
	$o.=$do.MENU_pro($pos)."<li><a href='".$link."'>".$l1."</a>";
			$l1last=$l1;
	$dodo="</li>";
}}

	$o.=$dodo; unset($m[$pos--]);
	foreach($m as $pos=>$l) $o.=MENU_pro($pos+1)."</ul>".MENU_pro($pos)."</li>";
	return "<!-- выпадающее меню --><center><table cellspacing=0 cellpadding=0 border=0><td><ul id='nav'>".$o."</ul></td></table></center><!-- /выпадающее меню -->";
}

function MENU_pro($l) { return '';
// return "\n".str_repeat("\t",$l); 
}

?>
