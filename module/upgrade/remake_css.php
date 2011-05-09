<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// Перекачать старую базу комментариев

function remake_css_proc($o) {
	$o=preg_replace("/url\([\'\"]*\/blog\/design\/(.*?)[\'\"]*\)/si",'url('.$GLOBALS['www_design']."$1)",$o);
	$o=preg_replace("/\@charset\s[\'\"]*windows-1251[\'\"]*/si",'@charset "'.$GLOBALS['wwwcharset'].'"',$o);
	$o=str_replace('{www_design}',$GLOBALS['www_design'],$o);
	return $o;
}

$action='remake_css';
if($PEST['action']==$action) { $admin_upgrade=true;
//==========================================================================================
foreach(glob($GLOBALS['file_css']."*.css") as $l) {
	$s.="<br><b>".$l."</b>";
	$o=file_get_contents($l); $old=$o;

	$o=remake_css_proc($o);

	if($o!=$old) { 
		if(file_put_contents($l,$o)===false) $s.="<font color=red> Error write file `$l` </font>";
		else $s.=" - <font color=green>changed</font>";
	} else { $s.=" - ok"; }
}
//==========================================================================================	
} else {
	$o=''; foreach(glob($GLOBALS['file_css']."*.css") as $l) $o.=file_get_contents($l);
	if($o!=remake_css_proc($o)) $s.=admin_kletka('action',"исправить файлы *.css под ваш хостинг",$action);
}

?>