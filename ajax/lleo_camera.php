<?php // тестовое решение

// include "../config.php";
//require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
require_once "../include_sys/JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
// include $include_sys."_autorize.php"; // сперва JsHttpRequest, затем autorize

// $a=$_REQUEST['a'];

//$s="alert('ssss')";
//$_RESULT["modo"]=$s;
//$_RESULT["status"]=true;
//exit;



$f=glob("/var/www/home/camera/*.swf");

$s='';

$last=0; $lastf=''; foreach($f as $l) {
	$t=filemtime($l);
	// $s.="$l:$t\n";
	if((time()-$t)>600) { unlink($l); } //$s.="delete file: $l\n";
	if($t>$last) { $last=$t; $lastf=$l; }
}

if($lastf!='') {
	$l="http://lleo.me/home/camera/".basename($lastf);
	$x=320; $y=240;
	$s="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' wmode='transparent' height='$y' width='$x'>"
."<param name='wmode' value='transparent'><param name='movie' value='$l'>"
."<embed type='application/x-shockwave-flash' src='$l' wmode='transparent' height='$y' width='$x'>"
."</object>";

$s="zabil('myswfplay',\"".$s."\"); setTimeout(\"majax('http://lleo.me/ajax/lleo_camera.php',{})\",10000);";
// salert('new',200);

}

$_RESULT["modo"]=$s;
$_RESULT["status"]=true;
exit;
// die("### $s");

// if(!intval($N) and $N!='0') idie("Я же просил число!");
// if( $N%2 ) otprav("zabil('chi_otvet','нечетное'); helps('chi_okno','нечетное!');");
// otprav("zabil('chi_otvet','четное'); helps('chi_okno','четное!');");

function njs($s) { return str_replace(array("\\","'",'"',"\n","\r"),array("\\\\","\\'",'\\"',"",""),$s); }

?>
