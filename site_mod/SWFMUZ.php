<?php // флэш-музыкальный ролик
// {swfmuz /dnevnik/img/2006/10/2006-10-04.swf [silent] [noloop] }
// silent - не запускать при загрузке
// noloop - не зацикливать

function SWFMUZ($e) { list($swf,$e)=explode(' ',$e,2);

if(strstr($e,'noloop')) $incmuz="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width=1 height=1 style='width:1px;height:1px;overflow:hidden;position:absolute;left:-400px;top:0;border:0;'>"
."<param name=movie value='".h($swf)."' />"
."<param name=loop value='false' />"
."<embed src='".h($swf)."' width=1 height=1 loop=false type='application/x-shockwave-flash'></embed></object>";

else $incmuz="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width=1 height=1 style='width:1px;height:1px;overflow:hidden;position:absolute;left:-400px;top:0;border:0;'>"
."<param name=movie value='".h($swf)."' />"
."<embed src='".h($swf)."' width=1 height=1 type='application/x-shockwave-flash'></embed></object>";

return "<script>
function sound_off() { idd('music').innerHTML = \"<img onclick='sound_on()' src='".$GLOBALS['www_design']."e2/sound_on.gif'>\"; }
function sound_on() { idd('music').innerHTML = \"<img onclick='sound_off()' src='".$GLOBALS['www_design']."e2/sound_off.gif'>".$incmuz."\"; }
</script>

".(strstr($e,'silent')?
"<table align=right><td align=right><div id='music'>
<img onclick='sound_on()' src='".$GLOBALS['www_design']."e2/sound_on.gif'>
<div class=br onclick='sound_on()'>включить звук</div>
</div></td></table>"
:
"<table align=right><td align=right><div id='music'>
<img onclick='sound_off()' src='".$GLOBALS['www_design']."e2/sound_off.gif'>
<div class=br onclick='sound_off()'>выключить звук</div>".$incmuz."
</div></td></table>"
);

}

?>