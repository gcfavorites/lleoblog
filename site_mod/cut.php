<?php /* �������� ��� ���

���� ����� �������� (� ��� ��� ��������� ������ � �����) - ������ ���� �� ���� ������ �������� [...], ���� ������� (������ � ���������� �����) - �������� ����� [��������&nbsp;����������] �� ������.
��� ������� ������ ����� �������� ��, ��� ���� ������.

����� ����� ������ ��������������, ������ �� � ������ � ���������� �������.

<script>function cut(e,d){e.style.display='none';e.nextSibling.style.display=d;}</script>
<style>.cut{cursor:pointer;color:blue;text-align:center;}.cut:hover{text-decoration:underline;}</style>

��� � �������� ��������, ��� ������� ������ {_cut:����_}.
�������� ���� ��� ���� ������ ������ {_cut:�������_}.
��������� � ����� �������� � ������ {_cut:������_}.
������ ����� ���� ����, {_cut:[���������� ������� ����]������ ��� ����� �����_}.

*/

SCRIPTS("cut","function cut(e,d){e.style.display='none';e.nextSibling.style.display=d;}");
STYLES("cut",".cut,.cutnc{cursor:pointer;color:blue;}.cut{text-align:center}.cut:hover,.cutnc:hover{text-decoration:underline;}");

function cut($e) {

	if(stristr($e,'#nocenter#')) { $cut='cutnc'; $e=str_ireplace('#nocenter#','',$e); }
	else $cut='cut';

	if(preg_match("/^\s*\[(.*?)\]([^\]].*?)$/si",$e,$m)) { $e=c($m[2]); $text=$m[1]; }

	if(strstr($e,"\n")||stristr($e,'<p')) { if(!isset($text)) $text="[��������&nbsp;����������]"; $tag="div"; $display="block"; }
	else { if(!isset($text)) $text="[...]"; $tag="span"; $display="inline";	}

	return "<$tag class=".$cut." onclick=\"cut(this,'$display')\">$text</$tag><$tag style='display:none'>$e</$tag>";
}

?>
