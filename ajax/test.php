<?php // �������� �������

include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include $include_sys."_autorize.php"; // ������ JsHttpRequest, ����� autorize





//=================================================================================================
if(isset($_REQUEST['onload'])) otprav(''); // ��� ���������� ����� ����� ��������� ��� GET-�������
//=================================================================================================



$a=$_REQUEST['a'];

if($a=='chi') {

$N=$_REQUEST['N'];
if(!intval($N) and $N!='0') idie("� �� ������ �����!");
if( $N%2 ) otprav("zabil('chi_otvet','��������'); helps('chi_okno','��������!');");
otprav("zabil('chi_otvet','������'); helps('chi_okno','������!');");
}

// =============== ������������ ������� ��� SIM (http://lleo.aha.ru/dnevnik/2009/09/11.html) =====
if($a=='sim_password') { $p=trim($_REQUEST['p'],"\n\r\t $"); $t=0x45; $s='';
  $a=explode('$',$p); if(sizeof($a)>2) { foreach($a as $l){ $x=hexdec(substr($l,2)); $s.=chr($t^$x); $t=$x; }}
  else{ $a=str_split($p); foreach($a as $l){ $t=$t^ord($l); $x="".dechex($t); if(strlen($x)<2) $x='0'.$x; $s.="$43".$x; }}
  idie($s); }
// =============== ������������ ������� ��� SIM ==================================================

?>
