<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����
// ������� ����� �� rgb � 16-������

print 'a 2 '.$GLOBALS['host_design']."
function1: ".function_exists('file_get_contents')."
function2: ".function_exists('file_put_contents')."
";

print '<p>test_get:'.h(file_get_contents('README.TXT'));

// ." ".file_get_contents($GLOBALS['host_design']."plain.html") );
/*
) { function
file_put_contents($url,$s) { $f=fopen($url,"w"); fputs($f,$s); fclose($f); } }
*/

$_PAGE = array( /* 'design'=>file_get_contents($GLOBALS['host_design']."plain.html"), */
'header'=>"������� ����� �� RGB � 16-������ �������������",
'title'=>"������� ����� �� RGB � 16-������ �������������",
'www_design'=>$www_design,
'admin_name'=>$admin_name,
'httphost'=>$httphost,
'wwwhost'=>$wwwhost,
'wwwcharset'=>$wwwcharset,
'signature'=>$signature
);



$txt=$_POST['txt'];

$o="<center>
<form action=".$mypage." method=post>
<p>RGB:<input size=80 name=txt class=t value='".htmlspecialchars($txt)."'> <input type='submit' name='go' value='���������'>
</form>";

if($txt=='') die($o."</center>");

$txt=preg_replace("/[^0-9]+/si"," ",$txt);

list($a,$b,$c)=explode(" ",$txt);

$o.= "<p>#"
.base_convert($a,10,16)
.base_convert($b,10,16)
.base_convert($c,10,16);

die($o."</center>");

?>
