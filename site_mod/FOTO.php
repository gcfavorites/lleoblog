<?php /* ������� ����� ����� ���������

��������� ������������� ����� ����� �� �����. ��������������, ��� ��� ������ ���������� ������, ������� ��� �� ���� ����� pre/, ��� ����� ��� ���� ����� ����������� ���������.

<script>var bigfoto_onload=1;function bigfoto_pos(){ajaxoff();e=idd('bigfotoimg');posdiv('bigfoto',-1,-1);var H=(getWinH()-20); if(e.height>H && H>480) { e.height=H; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}var W=(getWinW()-50); if(e.width>W && W>640) { e.width=W; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}}function bigfoto(e){ajaxon();bigfoto_onload=1;setTimeout(\"if(bigfoto_onload) bigfoto_pos();\", 2000);helps('bigfoto',\"<img id='bigfotoimg' onclick=\\\"clean('bigfoto')\\\" onload=\\\"bigfoto_onload=0;bigfoto_pos()\\\" src='\"+e.href+\"'>\",1);return false;}</script>

{_FOTO: /blog/2010/05/LLeo_Vysotsky.jpg _}
*/

SCRIPTS("bigfoto","

var bigfoto_onload=1;

function bigfoto_pos(){
	ajaxoff();
	e=idd('bigfotoimg');
	posdiv('bigfoto',-1,-1);
	var H=(getWinH()-20); if(e.height>H && H>480) { e.height=H; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}
	var W=(getWinW()-50); if(e.width>W && W>640) { e.width=W; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}
}

function bigfoto(e) {
	ajaxon();
	bigfoto_onload=1;
	setTimeout(\"if(bigfoto_onload) bigfoto_pos();\", 2000);
	helps('bigfoto',\"<img id='bigfotoimg' onclick=\\\"clean('bigfoto')\\\" onload=\\\"bigfoto_onload=0;bigfoto_pos()\\\" src='\"+e.href+\"'>\",1);
	return false;
}
");

// posdiv('bigfoto',-1,-1);

function FOTO($e) { // list($e,$s)=explode(':',$e,2); $e=c($e);
	$epre=preg_replace("/^(.*?)\/([^\/]+)$/si","$1/pre/$2",$e);
	return "<a href='".$e."' onclick='return bigfoto(this)'><img src='".h($epre)."' border=0></a>";
}

?>
