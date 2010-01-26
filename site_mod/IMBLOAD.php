<?php // Показать спрятанное под катом

// МАЛО ЛИ КАКОЕ ГОВНО НАМ ПОНАДОБИТСЯ?
include_once $GLOBALS['include_sys']."_foto.php"; // фотовывод



function IMBLOAD($e) {

	if(!preg_match("/^(.*?\/)(\d\d\d\d\/\d\d\/\d\d.*).html$/si",$e,$m)) return 'Error: IMBLOAD NOT FOUND "'.$e.'"';

//	return "url =".$m[1]." Date = ".$m[2];


$s=str_replace("\n","","
<div style=\"border: 3px dashed red; margin: 20px; padding: 20px;\">
<img src=".$m[1]."design/userpick.jpg align=left hspace=10>
<a href=".$m[1].">'+responseJS.Admin+'</a>
<p><a href=".$e."><b>".$m[2]." - '+responseJS.Header+'</b></a>
<p>'+responseJS.Body+'
</div>

");


	SCRIPT_ADD($www_design."JsHttpRequest.js"); // подгрузить аякс

	SCRIPTS('IMBLOAD',"
function imbload(id,Date) { e=document.getElementById(id); e.innerHTML = '<center>...идет загрузка...</center>';
JsHttpRequest.query('".$m[1]."ajax_imbload.php', { Date: '".$m[2]."' }, function(responseJS, responseText) { if(responseJS.status) { e.innerHTML = '".$s."'; }},true); }
");

$id='imbload_'.intval($GLOBALS['imbload_n']++);

return "<div id=$id><script>imbload('$id','".$m[2]."');</script></div>";

// блять, вот так будет лучше ;)

// onclick=\"imbload(this,'".$m[2]."')\"
//<center><font color=blue>нажми для загрузки</font></center>

}

?>
