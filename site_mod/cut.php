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

function cut($e) {

/*
$conf=array_merge(array(
'nskip'=>5,
'next'=>"<small><a href={nextpage}>&lt;&lt;&nbsp;предыдущие {n}</a></small>",
'prev'=>"<small><a href={prevpage}>следующие {n}</a>&nbsp;&gt;&gt;</small>",
'prevnext'=>"<table width=100%><tr><td align=left>{next}</td><td align=right>{prev}</td></tr></table><p>",
// 'comment'=>"<p align=right><a style='font-size:10pt;' href={link}#comments>Добавить комментарий</a> <small>(сейчас {ncomm} шт)</small></p
'comment'=>"comment = <div style='text-align: right; font-size:10pt; margin-right: 5px'><a href={link}#comments>комментариев {ncomm}</a> | <
'template'=>"<div style='text-align:justify;padding:0 15px;'><div class='header' id='Header_{num}' style='text-align:left'>{edit}<a href='{l
),parse_e_conf($e));

$LAST_skip = intval($conf['nskip']); // 5;

$skip=intval($_GET['skip']);
$pp=ms("SELECT `Date`,`Body`,`template`,`autokaw`,`autoformat`,`Header`,`DateUpdate`,`Access`,`num`,`Comment_view` FROM `dnevnik_zapisi` ".W

$n=sizeof($pp);
if($n>$LAST_skip){ unset($pp[$n-1]); $next=mper($conf['next'],array('nextpage'=>$mypage."?skip=".($skip+$LAST_skip).$catpn,'n'=>$LAST_skip))
$n=$skip-$LAST_skip;
if($n>=0) { $prev=mper($conf['prev'],array('prevpage'=>$mypage."?skip=".$n.$catpn,'n'=>$LAST_skip)); } else $prev='';
*/



if($GLOBALS['article']['autoformat']=='no') { $GLOBALS['cutar1']=array("&","\\","'",'"',"\n","\r"); $GLOBALS['cutar2']=array("&amp;","\\\\","\\'",'&quot;',"\\n",""); }
elseif($GLOBALS['article']['autoformat']=='p') { $GLOBALS['cutar1']=array("&","\\","'",'"',"\n ","\n\n","\n","\r"); $GLOBALS['cutar2']=array("&amp;","\\\\","\\'",'&quot;',"\n<p class=z>","<p>","<br>",""); }
else { $GLOBALS['cutar1']=array("&","\\","'",'"',"\n ","\n\n","\n","\r"); $GLOBALS['cutar2']=array("&amp;","\\\\","\\'",'&quot;',"\n<p class=z>","<p class=pd>","<p class=d>",""); }


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
