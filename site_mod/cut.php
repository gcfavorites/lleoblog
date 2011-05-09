<?php /* Спрятать под кат

Если текст короткий (в нем нет переводов строки и тэгов) - вместо него по ходу строки появится [...], если длинный (абзацы с переводами строк) - появится фраза [показать&nbsp;спрятанное] по центру.
При нажатии вместо этого появится то, что было скрыто.

Фразу можно задать самостоятельно, указав ее в начале в квадратных скобках.

<script>function cut(e,d){e.style.display='none';e.nextSibling.style.display=d;}</script>
<style>.cut{cursor:pointer;color:blue;text-align:center;}.cut:hover{text-decoration:underline;}</style>

Вся в полосках антилопа, без полосок только {_cut:жопа_}.
Покупает тушь для глаз хитрый дядька {_cut:пидарас_}.
Непорочны и чисты завелись в кишках {_cut:глисты_}.
Словно речка воду льет, {_cut:[попробуйте угадать сами]пьяный под окном блюет_}.

*/

SCRIPTS("cut","function cut(e,d){e.style.display='none';e.nextSibling.style.display=d;}");
STYLES("cut",".cut,.cutnc{cursor:pointer;color:blue;}.cut{text-align:center}.cut:hover,.cutnc:hover{text-decoration:underline;}");

function cut($e) {

	if(stristr($e,'#nocenter#')) { $cut='cutnc'; $e=str_ireplace('#nocenter#','',$e); }
	else $cut='cut';

	if(preg_match("/^\s*\[(.*?)\]([^\]].*?)$/si",$e,$m)) { $e=c($m[2]); $text=$m[1]; }

	if(strstr($e,"\n")||stristr($e,'<p')) { if(!isset($text)) $text="[показать&nbsp;спрятанное]"; $tag="div"; $display="block"; }
	else { if(!isset($text)) $text="[...]"; $tag="span"; $display="inline";	}

	return "<$tag class=".$cut." onclick=\"cut(this,'$display')\">$text</$tag><$tag style='display:none'>$e</$tag>";
}

?>
