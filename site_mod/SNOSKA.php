<?php /* сноска в виде открывающегос€ окна

Ёот модуль позвол€ет вставл€ть сноски в виде распахивающегос€ окна.

¬ерстка очередного фердипюкса{_SNOSKA: это слово употребл€ют, чтобы не произносить "творчество", ибо глупо_} потребовала модул€ сносок.

*/

/*
SCRIPTS("snoska","

function snoska(e,n){
	helps('snoska','<div style=br>сноска <b>'+n+'</b>:</div><div style=\"margin: 20px;\">'+e+'</div>');
//        posdiv('snoska',-1,0);
	addEvent(idd('snoska'),'click', function(){clean('snoska');});
	return false;
}

");
*/

$GLOBALS['snoska_n']=0;

function SNOSKA($s) {
	$s=str_replace(array("'",'"',"\n","\r"),array('&quot;','&quot;',"<br>",""),$s);
//	return "<span class=l title=\"".$s."\" onclick=\"snoska(this.title,'".$n."')\"><sup>".$n."</sup></span>"; }
	return "<span style='text-decoration:blink; vertical-align:text-top; font-weight:bold; font-size:60%; color:blue; cursor:pointer;' title=\"".$s."\">".(++$GLOBALS['snoska_n'])."</span>"; }
//	return "<span style='text-decoration:blink; vertical-align:text-top; font-weight:bold; font-size:60%; color:blue; cursor:pointer;' title=\"".$s."\">".(++$GLOBALS['snoska_n'])."</span>"; }
//helpс('snoska',\"<fieldset id='commentform'><legend>комментарий</legend>".njsn($s)."</fieldset>\");
?>
