<?php /* �������� ������������� � FLV-�������

����� ������ ����������� ������� X,Y.

{_FLV: http://lleo.aha.ru/dnevnik/2010/09/philips.swf 720 384 _}

*/

function FLV($e) { list($file,$x,$y)=explode(' ',$e,3); $x=intval($x); $y=intval($y);
	if($x*$y==0) return "<font color=red>Module FLV error: x or y = null!";
	if(!strstr($file,'/')) $file=$GLOBALS['httphost'].substr($GLOBALS['article']['Date'],0,8).$file;

//http://lleo.aha.ru/dnevnik/design/flvplayer.swf?file=http://lleo.aha.ru/dnevnik/img/2009/01/idea.flv" 

	$play=$GLOBALS['www_design']."flvplayer.swf?file=".urlencode($file);
//.urlencode($file);

return "<object type='application/x-shockwave-flash' width='$x' height='$y' wmode='transparent' data=\"$play\">"
."<param name='movie' value=\"$play\">"
."<param name='wmode' value='transparent'>"
."<embed src=\"$play\" type='application/x-shockwave-flash' wmode='transparent' width='$x' height='$y'></embed>"
."</object>";

/*

<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='".intval($x)."' height='".intval($y)."'>"
."<param name='movie' value='".h($swf)."' />"
.($loop=='noloop'?"<param name='loop' value='false' />":'')
."<embed src='".h($swf)."' width='".intval($x)."' height='".intval($y)."' "
.($loop=='noloop'?"loop='false'":'')
."type='application/x-shockwave-flash'></embed></object>";
*/

}

?>