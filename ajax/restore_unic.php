<?php // ��������������� unic

include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
if(isset($_REQUEST['onload'])) otprav(''); // ��� ���������� ����� ����� ��������� ��� GET-�������
$autorizatio=false;
include $include_sys."_autorize.php"; // ������ JsHttpRequest, ����� autorize

// $_COOKIE[$uc]=$_REQUEST["up"];
$up=$_REQUEST["up"];
$upo=$_REQUEST["upo"];
$num=intval($_REQUEST["num"]);
// $i=intval($_REQUEST["i"]);
$i=($_REQUEST["i"]==1?'fc':'f5');
/*
idie(nl2br("up: $up
upo: $upo
num: $num
i: $i"));
*/

$un=intval(substr($upo,0,strpos($upo,'-')));

if(!$un || !upcheck($upo) || getis_global($un)===false) { // ��������� ������������, �� ������ �������

logi('restore_unic-ERRPASS.txt',"\n $unic $num $i $up $upo");

otprav("
".$i."_save('up','');
unic_rest_flag=0;
salert(\"������ ��� ������� �������������� $i:<br>".h($upo)."\",1000);
");
}

// ����� ���������
if($num){ $e=$msqe; msq_add("dnevnik_posetil",array('unic'=>$unic,'url'=>$num)); $msqe=$e; }

logi('restore_unic.txt',"\n $unic $num $i $up $upo");

otprav("
up='$upo'; realname=\"".$imgicourl."\";
fc_save('up',up); f5_save('up',up);
clean('loginobr_unic11');
helps('work',\"<fieldset>������������ $imgicourl ($i)</fieldset>\"); posdiv('work',-1,-1);
setTimeout(\"c_save(uc,up);clean('work');zabil('myunic',realname);\", 1000);
");


//	otprav("f".($_REQUEST["i"]==1?'c':'5')."_save('up',''); unic_rest_flag=0; unic_rest(0); unic_rest(1);");

?>
