<?php /* ����� ������ ����� ����� ��������� � � ���������

��������� ������������� ����� ����� �� ����� (� ����� � ������ � http://). ��������������, ��� ��� ������ ���������� ������, ������� ��� �� ���� ����� pre/, ��� ����� ��� ���� ����� ����������� ���������.
����� ������ ����� ������� ������ ������� ����� ����� (��� ����������� �� ����� �����).
��� ����� ������������� �������, ������ ����� ������� ������ �������, ������ ��� ������ �� ������� � �� �����, ������ ������� �� ������� �� ���� ��� ���������. �� ��������� ������ 210 (�.�. ��� ��������� ������� 200), �� ����� ������� �������� "WIDTH nnn".

�� ��������� �������� mode=album, �� ���� ������� ������, �� ����� ��������� �� ��������, � ������������� � ���������������.
��� ���� text-valign=up, ��� ��������, ��� ����� � ����� ������������� ���� �����. ����� - ��� ������.

{_FOTOS: WIDTH=150
http://lleo.aha.ru/blog/2010/05/26-2098.jpg �����-�� ������
http://lleo.aha.ru/blog/2010/05/30-2114.jpg ���� �������� Grassy, ������� � ����� ����� ������������ �����
http://lleo.aha.ru/blog/2010/05/30-2112.jpg ���� �������� Grassy, ������� � �����
http://lleo.aha.ru/blog/2010/05/LLeo_Vysotsky.jpg ����� ���������� ������� � ������� ��������
http://lleo.aha.ru/blog/2010/05/Screenshot0022.jpg � ��� �������� � ���� �������
_}
*/

if(!isset($GLOBALS['bigfoto'])) $GLOBALS['bigfoto']=0;
if(!isset($GLOBALS['bigfotopart'])) $GLOBALS['bigfotopart']=0;

function FOTOS($e) {
	$e=str_replace('WIDTH ','WIDTH=',$e); // ��� ������������� �� ������ ������
	$conf=array_merge(array(
'WIDTH'=>210,
'mode'=>'album',
'text-valign'=>'up'
),parse_e_conf($e));

	$pp=explode("\n",$conf['body']);
	$s=''; foreach($pp as $p) { $p=c($p); if($p=='') continue;
		list($img,$txt)=explode(" ",$p,2); $img=c($img); $txt=c($txt);

		if(!strstr($img,'/')) {
			list($y,$m,)=explode('/',$GLOBALS['article']['Date'],3); if($y*$m) $img=$GLOBALS['wwwhost'].$y.'/'.$m.'/'.$img;
		}

	if($conf['mode']!='album') {
		$fm="{_FOTOM: ".$img." _}"; if($conf['text-valign']=='up') $s.=$txt.$fm; else $s.=$fm.$txt;
		$s.="<p>"; continue;
	}

		if(!strstr($img,',')) $epre=preg_replace("/^(.*?)\/([^\/]+)$/si","$1/pre/$2",$img);
		else list($img,$epre)=explode(',',$img);

		$s.="\n\n<a id='bigfot".($GLOBALS['bigfotopart'])."_".($GLOBALS['bigfoto'])."' href='".h($img)."' onclick='return bigfoto(".$GLOBALS['bigfoto'].",".$GLOBALS['bigfotopart'].")'><img src='".h($epre)."' border=0></a>"
		."<div class=r id='bigfott".($GLOBALS['bigfotopart'])."_".($GLOBALS['bigfoto']++)."'>".($txt!=''?$txt:'')."</div>";

	}


	if($conf['mode']!='album') return $s;

	if($GLOBALS['admin']) $s.="<div style='display:none' id='bigfotnum".($GLOBALS['bigfotopart'])."'>".$GLOBALS['article']['num']."</div>";
	$GLOBALS['bigfotopart']++;
	return "{_BLOKI: WIDTH=".$conf['WIDTH']."\n\n".$s."_}";
}

?>