<?php /* ������ youtube.com

�������� ��������: ���� �������� ����� ���� ������� � �����, �� ���� �� ��������� ������ 50 ����, ��������� �������� ����� ��������� �� ������ ����������.

������� � ������� ������, ������� �� ��� ������ ������ ����� ������ � ������������ ��� ���� ��������������� ���� ������.

����� ����������� | � ������ ������ ����������� ��� ������ �� ����� (����� ���, ��� ����� ����������� � ������) � ��������.
<script>function youtube(i,url){ zabil('tube'+i,"<object width='560' height='340'><param name='movie' value='http://www.youtube.com/v/"+url+"&hl=ru&fs=1'></param><param name='allowFullScreen' value='true'></param><param name='allowscriptaccess' value='always'></param><embed src='http://www.youtube.com/v/"+url+"&hl=ru&fs=1' type='application/x-shockwave-flash' allowscriptaccess='always' allowfullscreen='true' width='560' height='340'></embed></object>"); }</script>
<style>.tube { border: 1px dotted white;} .tube:hover { border: 1px dotted red;}</style>
{_YOUTUBE:

6KmxzYgle2U | ���� 1, �������: ������ ��������� � ���� ������� - 1
UN1KtRl8lMQ | ���� 1, �������: ������ �������, ����� ������, ����� ����������
nC8wgVD3DoU | ������� � ��������� ���� ����������.

BHVP8RbKJkw | ���� 2, ������, �������: ���� Jukebox - 1
MRc87bKvcvI | ���� 3, �������, ���: ������ Zventa Sventana
GioieSHPzZY | ���� 3, �������, ���: ������ ��������
IBwa0mYM7OU | ���� 5, �����, �������: ����� ������
fLVu7v-wviA | ���� 7, �������: ����� ���������� � ���-���-���. ������������� ����������.

A4dAmmye7lo | ���� 7, �������: ��������� ����� ������-��������
_}
*/


function YOUTUBE($e) { global $youtube_n;

$youtube_n++;

$v1="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' wmode='transparent' width='560' height='340'><param name='movie' value='http://www.youtube.com/v/";
$v2="&hl=ru&fs=1' /><param name='wmode' value='transparent' /><param name='allowFullScreen' value='true' /><param name='allowscriptaccess' value='always' /><embed src='http://www.youtube.com/v/";
$v3="&hl=ru&fs=1' wmode='transparent' type='application/x-shockwave-flash' allowscriptaccess='always' allowfullscreen='true' width='560' height='340'></embed></object>";

SCRIPTS("YouTube procedure"," function youtube(i,url){ zabil('tube'+i,\"".$v1."\"+url+\"".$v2."\"+url+\"".$v3."\"); } ");
STYLES("YouTube style",".tube { border: 1px dotted white;} .tube:hover { border: 1px dotted red;}");

$ara=array();
$m=explode("\n",$e); foreach($m as $n=>$l) {
	if(c($l)=='') continue;
	list($url,$txt)=explode('|',$l,2);
	$ara[]=array('url'=>c($url),'txt'=>c($txt));
}


$s=''; foreach($ara as $p) { $s.="<a class=tube href=\"javascript:youtube('".$youtube_n."','".$p['url']."')\">".njs($p['txt'])."</a><br>"; }

return "<center><div id='tube".$youtube_n."'>".$v1.$ara[0]['url'].$v2.$ara[0]['url'].$v3."</div><p><table><td>$s</td></table></center>";

}

?>