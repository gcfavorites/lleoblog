<?php // ���� � ��� mary kay

function MENUKAY($e) { global $menykay_n;

$menykay_n++;

SCRIPTS("MenyKay procedure"," function menukay(i,img,text) { idd('img'+i).src='".$GLOBALS['foto_www_small']."'+img; zabil('text'+i,text); } ");

$m=explode("\n",$e); foreach($m as $n=>$l) { if(c($l)=='') { unset($m[$n]); continue; }
	list($img,$txt,$link,$text)=explode('|',$l,4);
	$m[$n]=array('img'=>c($img),'txt'=>c($txt),'link'=>c($link),'text'=>c($text));
}


$s=''; foreach($m as $p) {
  $s.="<p><a onmouseover=\"menukay('".$menykay_n."','".$p['img']."','".njs($p['text'])."')\" href='".$p['link']."'>".$p['txt']."</a>";
}

return "<center><table><tr valign=center>"
."<td><img id='img".$menykay_n."' src='".$GLOBALS['foto_www_small'].$m[0]['img']."' hspace=5 vspace=5 border=0></td>"
."<td>{_CENTER:".$s."_}</td></tr></table>"
."<div align=justify id='text".$menykay_n."'>".$m[0]['text']."</div></center>";

}


/*

{_MENUKAY:

1.jpg | ���� ������ | /popa/gnezdo/index.html | ���� ������ ? ���������� ���������, ����������� ��� �� �������� �������. ������ �������� �� ������ � ������ � ����������� ��������������. ���� ���������� �������� �������� ������ ������� ����� ����� ������ �� ����� ��� ��������� ������.

2.jpg | ����� �� ������� ����� | /postel/podushka | ��������� ��������� ��������� ���������� ���. ��� ��� ����� �������, �� ��� �� ����� � �����? � ���, ��� ������� �� ������� �������, ��������� ��� ������ ?���������� ����� ������ ������?.

3.jpg | ����� � ������������ | /medical/super/spina | ����� �����? ������� ����� ���������? �� ������������� ���������� ������, ����� 50% ����������� ������ �������� �� ������ � ������������. �������-������� �������� ���������� �� ������ ��� �������� �� ���� �������!

_}

*/

?>
