<?php // ������ - �� ����, �� ��

if(!isset($admin_name)) die("Error 404"); // ����������� ����������� ������ - �����

$_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."plain.html"),
'header'=>"�������� ��� ������",
'title'=>"�������� ��� ������",

'www_design'=>$www_design,
'admin_name'=>$admin_name,
'httphost'=>$httphost,
'wwwhost'=>$wwwhost,
'wwwcharset'=>$wwwcharset,
'signature'=>$signature
);

include_once $include_sys."_podsveti.php"; // ��������� ������ ������ � ����� �������

$txt1=$_POST['txt1'];
$txt2=$_POST['txt2'];


$o="<form action=".$mypage." method=post><center>
<p>�����1:<br><textarea name=txt1 cols=80 class=t>".htmlspecialchars($txt1)."</textarea>
<br>�����2:<br><textarea name=txt2 cols=80 class=t>".htmlspecialchars($txt2)."</textarea>
<p><input type='submit' name='go' value='��������'>
</form>";

if($txt1==''||$txt2=='') die($o."</center>");

$o.= "

<style>
.p1 { color: #3F3F3F; text-decoration: line-through; background: #DFDFDF; } /* ����������� */
.p2 { background: #FFD0C0; } /* ����������� */
</style>

<p>���������:
<table border=1 cellspacing=0 cellpadding=10 width=90%><td>".podsveti(htmlspecialchars($txt2),htmlspecialchars($txt1))."</td></table>
<p>&nbsp;";


die($o."</center>");

?>
