<?php // �������� ���������� ��� �����

if(!isset($GLOBALS['admin_name'])) die(highlight_file(__FILE__,1));

function cut($e) {

	$tag="[��������&nbsp;����������]";
	$tag0="[...]";
	$l=(strstr($e,"\n")||strstr($e,'<')?1:0);

       	$e=str_replace(array("&","\\","'",'"',"\n","\r"),array("&amp;","\\\\","\\'",'\\"',"\\n",""),$e);

	SCRIPTS("cut","function cut(e,s,l) {
		if(e.className=='cuts') { e.className='cut'; e.innerHTML=(l?'$tag':'$tag0'); }
		else { e.className='cuts'; e.innerHTML=s; }
	return false; }"); // � ��������

	STYLES("cutstyle","
.cut,.cut0 { cursor: pointer; color: blue; } .cut:hover,.cut0:hover { text-decoration: underline; }
.cut { text-align: center;}
.cuts { border: 1px dashed #ccc; cursor: pointer; }
"); // � ������

	if($l) return "<div class=cut onclick=\"cut(this,'$e',$l)\">$tag</div>";
	return "<span class=cut0 onclick=\"cut(this,'$e',$l)\">$tag0</span>";

}

?>
