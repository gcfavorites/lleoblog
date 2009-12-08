<?php // Показать спрятанное под катом

if(!isset($GLOBALS['admin'])) die('<pre>'.htmlspecialchars(file_get_contents($_SERVER['SCRIPT_FILENAME'])).'</pre>');

function cut($e) {

	$tag="[показать&nbsp;спрятанное]";
	$tag0="[...]";
	$l=(strstr($e,"\n")||strstr($e,'<')?1:0);

	$e=str_replace(array("\\","'",'"',"\n","\r"),array("\\\\","\\'",'\\"',"\\n",""),$e);

	SCRIPTS("cut","function cut(e,s,l) {
		if(e.className=='cuts') { e.className='cut'; e.innerHTML=(l?'$tag':'$tag0'); }
		else { e.className='cuts'; e.innerHTML=s; }
	return false; }"); // к скриптам

	STYLES("cutstyle","
.cut,.cut0 { cursor: pointer; color: blue; text_decoration: underline; }
.cut { text-align: center;}
.cuts { border: 1px dashed #ccc; }
"); // к стилям

	if($l) return "<div class=cut onclick=\"cut(this,'$e',$l)\">$tag</div>";
	return "<span class=cut0 onclick=\"cut(this,'$e',$l)\">$tag0</span>";

}

?>
