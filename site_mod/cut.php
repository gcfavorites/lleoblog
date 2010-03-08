<?php /* Спрятать под кат

Если текст короткий (если в нем встречаются переводы строки или тэги) - вместо него по ходу строки появится для нажатия [...], если длинный (абзацы с переводами строки) - появится фраза [показать&nbsp;спрятанное], но с новой строки и по центру.

При нажатии появится скрытый текст. При повторном нажатии - скроется снова.

<script>function cut(e,s,l) { if(e.className=='cuts') { e.className='cut'; e.innerHTML='[...]'; } else { e.className='cuts'; e.innerHTML=s; } return false; } </script>
<style> .cut,.cut0 { cursor: pointer; color: blue; } .cut:hover,.cut0:hover { text-decoration: underline; } .cut { text-align: center;} .cuts { border: 1px dashed #ccc; cursor: pointer; } </style>

Вся в полосках антилопа, без полосок только {_cut:жопа_}.
Покупает тушь для глаз хитрый дядька {_cut:пидарас_}.
Непорочны и чисты завелись в кишках {_cut:глисты_}.

*/


// if(!isset($GLOBALS['admin_name'])) die(highlight_file(__FILE__,1));

if($GLOBALS['article']['autoformat']=='no') { $GLOBALS['cutar1']=array("&","\\","'",'"',"\n","\r"); $GLOBALS['cutar2']=array("&amp;","\\\\","\\'",'&quot;',"\\n",""); }
elseif($GLOBALS['article']['autoformat']=='p') { $GLOBALS['cutar1']=array("&","\\","'",'"',"\n ","\n\n","\n","\r"); $GLOBALS['cutar2']=array("&amp;","\\\\","\\'",'&quot;',"\n<p class=z>","<p>","<br>",""); }
else { $GLOBALS['cutar1']=array("&","\\","'",'"',"\n ","\n\n","\n","\r"); $GLOBALS['cutar2']=array("&amp;","\\\\","\\'",'&quot;',"\n<p class=z>","<p class=pd>","<p class=d>",""); }

function cut($e) {

	$tag="[показать&nbsp;спрятанное]";
	$tag0="[...]";
	$l=(strstr($e,"\n")||strstr($e,'<')?1:0);

       	$e=str_replace($GLOBALS['cutar1'],$GLOBALS['cutar2'],$e);

	SCRIPTS("cut","function cut(e,s,l) {
		if(e.className=='cuts') { e.className='cut'; e.innerHTML=(l?'$tag':'$tag0'); }
		else { e.className='cuts'; e.innerHTML=s; }
	return false; }"); // к скриптам

	STYLES("cutstyle","
.cut,.cut0 { cursor: pointer; color: blue; } .cut:hover,.cut0:hover { text-decoration: underline; }
.cut { text-align: center;}
.cuts { border: 1px dashed #ccc; cursor: pointer; }
"); // к стилям

	if($l) return "<div class=cut onclick=\"cut(this,'$e',$l)\">$tag</div>";
	return "<span class=cut0 onclick=\"cut(this,'$e',$l)\">$tag0</span>";

}

?>
