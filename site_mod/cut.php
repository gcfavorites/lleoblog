<?php /* �������� ��� ���

���� ����� �������� (���� � ��� ����������� �������� ������ ��� ����) - ������ ���� �� ���� ������ �������� ��� ������� [...], ���� ������� (������ � ���������� ������) - �������� ����� [��������&nbsp;����������], �� � ����� ������ � �� ������.

��� ������� �������� ������� �����. ��� ��������� ������� - �������� �����.

<script>function cut(e,s,l) { if(e.className=='cuts') { e.className='cut'; e.innerHTML='[...]'; } else { e.className='cuts'; e.innerHTML=s; } return false; } </script>
<style> .cut,.cut0 { cursor: pointer; color: blue; } .cut:hover,.cut0:hover { text-decoration: underline; } .cut { text-align: center;} .cuts { border: 1px dashed #ccc; cursor: pointer; } </style>

��� � �������� ��������, ��� ������� ������ {_cut:����_}.
�������� ���� ��� ���� ������ ������ {_cut:�������_}.
��������� � ����� �������� � ������ {_cut:������_}.

*/


// if(!isset($GLOBALS['admin_name'])) die(highlight_file(__FILE__,1));

if($GLOBALS['article']['autoformat']=='no') { $GLOBALS['cutar1']=array("&","\\","'",'"',"\n","\r"); $GLOBALS['cutar2']=array("&amp;","\\\\","\\'",'&quot;',"\\n",""); }
elseif($GLOBALS['article']['autoformat']=='p') { $GLOBALS['cutar1']=array("&","\\","'",'"',"\n ","\n\n","\n","\r"); $GLOBALS['cutar2']=array("&amp;","\\\\","\\'",'&quot;',"\n<p class=z>","<p>","<br>",""); }
else { $GLOBALS['cutar1']=array("&","\\","'",'"',"\n ","\n\n","\n","\r"); $GLOBALS['cutar2']=array("&amp;","\\\\","\\'",'&quot;',"\n<p class=z>","<p class=pd>","<p class=d>",""); }

function cut($e) {

	$tag="[��������&nbsp;����������]";
	$tag0="[...]";
	$l=(strstr($e,"\n")||strstr($e,'<')?1:0);

       	$e=str_replace($GLOBALS['cutar1'],$GLOBALS['cutar2'],$e);

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
