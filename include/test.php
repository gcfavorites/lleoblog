<?php // ����������� ������

// �������� ����������:
// $article['Header'] // ���������
// $article["Date"] // ���� (��������) ���� �������
// $article["DateTime"] // ���� ������� � ������� time
// $article["view_counter"] // ������� ���������
// $article["Prev"] // ���� ��������� �������
// $article["Next"] // ���� ���������� �������
// $article["DateUpdate"] // ����� ���������� ���������� �������
// $_PAGE["coments"] // �������� ������������ (������ �����������: $_PAGE, � �� $article)

// 1. ������� ������: ������� ����� ����� {DateUpdate} �� �������� ���� ����������
$d=date("Y-m-d H:i:s",$article["DateUpdate"]);
$article['Body'] = str_ireplace("{DateUpdate}",$d,$article['Body']); // � �������
$_PAGE["coments"] = str_ireplace("{DateUpdate}",$d,$_PAGE["coments"]); // � � ������������

// 2. �������� � ������ ������� ������� "{������ ... �� ...}" � �������� ��
preg_match("/(\{������ ([^\}\s]+) �� ([^\}\s]+)\})/si",$article['Body'],$m);
// $article['Body'] = str_replace($m[1],'',$article['Body']); // ������ �� ������ ���� ������� "{zameni ...}"
$article['Body'] = str_replace($m[2],$m[3],$article['Body']); // ������� ������ � �������
$_PAGE["coments"] = str_replace($m[2],$m[3],$_PAGE["coments"]); // � � ������������

// 3. ������ ������ � �����
// ���� ����� ������ 20:50, �� ������ 21:00, �� ��� ����� �������� �������� "��������� ���� ������", �������� � ����� ������ �����:

if( // $admin ||
date("Hi")>"2050" && date("Hi")<"2100" ) $article['Body'] .= '<p><center>
<object type="application/x-shockwave-flash" data="http://deti.kiho.in.ua/data/flvplayer.swf" height="596" width="704"><param name="bgcolor" value="#FFFFFF" /><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="movie" value="http://deti.kiho.in.ua/data/flvplayer.swf" /><param name="FlashVars" value="way=http://teramult.org.ua/f/flv8/1988_su_spokoinoi.nochi.malyshi/spokoinoi.nochi.malyshi.flv&amp;swf=http://deti.kiho.in.ua/data/flvplayer.swf&amp;w=704&amp;h=528&amp;pic=http://deti.kiho.in.ua/data/zaglushka.gif&amp;autoplay=0&amp;tools=2&amp;skin=grey&amp;volume=70&amp;q=&amp;comment=" /></object>
</center>';




/*
 PS: ������, ���� ��� ���� �������� ���-�� � ���� �������� ��� ������, ������ ����������� �������, ������ �� ����� � ���������:

SCRIPTS("
var x=0;

function xss {
	alert(0);
}
");


STYLES("p {border: 1ps solid red;}");
*/

?>