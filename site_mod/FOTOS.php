<?php /* ����� ������ ����� ����� ��������� � � ���������

��������� ������������� ����� ����� �� ����� (� ����� � ������ � http://). ��������������, ��� ��� ������ ���������� ������, ������� ��� �� ���� ����� pre/, ��� ����� ��� ���� ����� ����������� ���������.
����� ������ ����� ������� ������ ������� ����� ����� (��� ����������� �� ����� �����).
��� ����� ������������� �������, ������ ����� ������� ������ �������, ������ ��� ������ �� ������� � �� �����, ������ ������� �� ������� �� ���� ��� ���������. �� ��������� ������ 210 (�.�. ��� ��������� ������� 200), �� ����� ������� �������� "WIDTH nnn".

<script>var bigfoto_onload=1;function bigfoto_pos(){ajaxoff();e=idd('bigfotoimg');posdiv('bigfoto',-1,-1);var H=(getWinH()-20); if(e.height>H && H>480) { e.height=H; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}var W=(getWinW()-50); if(e.width>W && W>640) { e.width=W; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}}function bigfoto(e){ajaxon();bigfoto_onload=1;setTimeout(\"if(bigfoto_onload) bigfoto_pos();\", 2000);helps('bigfoto',\"<img id='bigfotoimg' onclick=\\\"clean('bigfoto')\\\" onload=\\\"bigfoto_onload=0;bigfoto_pos()\\\" src='\"+e.href+\"'>\",1);return false;}</script>

{_FOTOS: WIDTH 150
http://lleo.aha.ru/blog/2010/05/26-2098.jpg �����-�� ������
http://lleo.aha.ru/blog/2010/05/30-2114.jpg ���� �������� Grassy, ������� � ����� ����� ������������ �����
http://lleo.aha.ru/blog/2010/05/30-2112.jpg ���� �������� Grassy, ������� � �����
http://lleo.aha.ru/blog/2010/05/LLeo_Vysotsky.jpg ����� ���������� ������� � ������� ��������
http://lleo.aha.ru/blog/2010/05/Screenshot0022.jpg � ��� �������� � ���� �������
_}
*/

SCRIPTS("bigfoto","

var bigfoto_onload=1;

function bigfoto_pos(){
	ajaxoff();
	e=idd('bigfotoimg');
	posdiv('bigfoto',-1,-1);
	var H=(getWinH()-20); if(e.height>H) { e.height=H; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}
	var W=(getWinW()-50); if(e.width>W) { e.width=W; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}
}

function bigfoto(e) {
        ajaxon();
        bigfoto_onload=1;
        setTimeout(\"if(bigfoto_onload) bigfoto_pos();\", 2000);
        helps('bigfoto',\"<img id='bigfotoimg' onclick=\\\"clean('bigfoto')\\\" onload=\\\"bigfoto_onload=0;bigfoto_pos()\\\" src='\"+e.href+\"'>\",1);
        return false;
}


");

// .thmbns {margin: -3em 0 0 -2em; text-align:center;}

STYLES("����� design.ru","
.thmbns {margin: margin: -3em 0 0 -2em; text-align:center;}
.thmbn {text-decoration:none; display: -moz-inline-box; display:inline-block; vertical-align:top; text-align:left; margin:3em 0 0 2em;}
.thmbn .rth {float:left;}
"); // width:210px; 

function FOTOS($e) { // list($e,$s)=explode(':',$e,2); $e=c($e);
	$WW=210;
	$pp=explode("\n",$e);
	$s=''; foreach($pp as $p) { $p=c($p); if($p=='') continue;

		list($img,$txt)=explode(" ",$p,2); $img=c($img); $txt=c($txt);

			if($img=='WIDTH') {
				$WW=intval($txt);
				if(!$WW) return "<b>�������� �������� WIDTH � ������ FOTOS</b>";
				continue;
			}

		if(!strstr($img,'/')) {
			list($y,$m,)=explode('/',$GLOBALS['article']['Date'],3); if($y*$m) $img=$GLOBALS['wwwhost'].$y.'/'.$m.'/'.$img;
		}

		$epre=preg_replace("/^(.*?)\/([^\/]+)$/si","$1/pre/$2",$img);

		$s.="<ins class='thmbn' style='width: ".$WW."px'><div class='rth' style='width: ".$WW."px'>"
		."<a href='".h($img)."' onclick='return bigfoto(this)'><img src='".h($epre)."' border=0></a>"
		."<div class=r>".($txt!=''?$txt:'')."</div>"
		."</div></ins>";
	}
	return "<br class=q><div class=thmbns>$s</div>";
}

?>
