<?php /* ���� '� �� mary kay'

���� ������ ��� ������ �� ������� ������ �����. ��� ��������� ������ �� ������ �������� �������� � ��������.

������ ����� �����: ����� ����������� | � ������ ������ ���������:

������ ��� ��������, ������� � �����������, ���� ������ ��� (��������� ����) | ��������� | ���� | ��������� ��������

<script>function menukay(i,img,text) { idd('img'+i).src=img; zabil('text'+i,text); }</script>

{_MENUKAY:

http://lleo.aha.ru/blog/photo/1.jpg | ���� ������ | /popa/gnezdo/index.html | ���� ������ - ���������� ���������, ����������� ��� �� �������� �������. ������ �������� �� ������ � ������ � ����������� ��������������. ���� ���������� �������� �������� ������ ������� ����� ����� ������ �� ����� ��� ��������� ������.

http://lleo.aha.ru/blog/photo/2.jpg | ����� �� ������� ����� | /postel/podushka | ��������� ��������� ��������� ���������� ���. ��� ��� ����� �������, �� ��� �� ����� � �����? � ���, ��� ������� �� ������� �������, ��������� ��� ������ '���������� ����� ������ ������'.

http://lleo.aha.ru/blog/photo/3.jpg | ����� � ������������ | /medical/super/spina | ����� �����? ������� ����� ���������? �� ������������� ���������� ������, ����� 50% ����������� ������ �������� �� ������ � ������������. �������-������� �������� ���������� �� ������ ��� �������� �� ���� �������!

_}

*/


function MENUKAY($e) { global $menykay_n;

$menykay_n++;

SCRIPTS("MenyKay procedure"," function menukay(i,img,text) { 

if(img.indexOf('/')) idd('img'+i).src=img; else idd('img'+i).src='".$GLOBALS['foto_www_small']."'+img;

zabil('text'+i,text); } ");

$m=explode("\n",$e); foreach($m as $n=>$l) { if(c($l)=='') { unset($m[$n]); continue; }
	list($img,$txt,$link,$text)=explode('|',$l,4);
	$m[$n]=array('img'=>c($img),'txt'=>c($txt),'link'=>c($link),'text'=>c($text));
}


$s=''; foreach($m as $p) {
  $s.="<p><a onmouseover=\"menukay('".$menykay_n."','".$p['img']."','".njs($p['text'])."')\" href='".$p['link']."'>".$p['txt']."</a>";
}

return "<center><table><tr valign=center>"
."<td><img id='img".$menykay_n."' src='".(strstr($m[0]['img'],'/')?$m[0]['img']:$GLOBALS['foto_www_small'].$m[0]['img'])."' hspace=5 vspace=5 border=0></td>"
."<td>{_CENTER:".$s."_}</td></tr></table>"
."<div align=justify id='text".$menykay_n."'>".$m[0]['text']."</div></center>";

}



?>