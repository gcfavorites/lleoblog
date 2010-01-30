<?php // флэш-музыкальный ролик

function SWFMUZ($e) { // {swfmuz /dnevnik/img/2006/10/2006-10-04.swf}

$incmuz='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width=1 height=1 style="width:1px;height:1px;overflow:hidden;position:absolute;left:-400px;top:0;border:0;"><param name=movie value="'.$e.'" /><embed src="'.$e.'" width=1 height=1 type="application/x-shockwave-flash"></embed></object>';

return '
<script>
function sound_off() { idd("music").innerHTML = \'<img onclick="sound_on()" src=/dnevnik/img/sound_on.gif>\'; }
function sound_on() { idd("music").innerHTML = \'<img onclick="sound_off()" src=/dnevnik/img/sound_off.gif>'.$incmuz.'\'; }
</script>

<table align=right><td align=right><div id="music">
<img onclick="sound_off()" src=/dnevnik/img/sound_off.gif><div class=br onclick="sound_off()">выключить звук</div>'.$incmuz.'
</div></td></table>';

}

?>