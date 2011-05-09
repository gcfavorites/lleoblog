<?php
include "config.php";
include $include_sys."_autorize.php"; if(!$admin) die('Error autorize');
// include $include_sys."_msq.php";
//require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

// if(move_uploaded_file($FILE["tmp_name"],$ffile)) { // print "успешно закачан!";
//			obrajpeg($ffile,$foto_file.$fname,768,85,$s);


if(count($_FILES)>0) foreach($_FILES as $FILE) if(is_uploaded_file($FILE["tmp_name"])) {

	$fname=htmlspecialchars($FILE["name"]);
		if(!preg_match("/\.jpe*g$/si",$fname)) die("не фотка!");

		if(preg_match("^\./si",$fname)) die("Имя с точки?");
		if(strstr($fname,'..')) die("Error: ..");

	if(is_file($foto_file_small.$fname)) die("present");
	obrajpeg($FILE["tmp_name"],$foto_file_small.$fname,$foto_res_small,$foto_qality_small,$foto_logo);
	obrajpeg($foto_file_small.$fname,$foto_file_preview.$fname,$foto_res_preview,$foto_qality_preview);


	msq_add($db_site,array(
                'name'=>e( ($_REQUEST["name"]==''?$fname:$_REQUEST["name"]) ),
                'text'=>e($fname),
                'type'=>'photo',
                'Access'=>e($_REQUEST["Access"])
        ));


//	die("<img src=".$foto_www_preview.$fname." hspace=5 vspace=5>");

die("<div class='fotoa'>
<a href='' onclick='return foto(\"".$foto_www_small.$fname."\")'>
<img src='".$foto_www_preview.$fname."' hspace=5 vspace=5>
<div class='fotot'>".$fname."</div></a></div>");









	
} else { print "Ошибка 2! ".print_r($_FILES,1); }



//==================================================================================================

function obrajpeg($from,$to,$h=150,$q=80,$s,$r=10) { // set_time_limit(0);
        $img1=ImageCreateFromJpeg($from); $W=ImagesX($img1); $H=ImagesY($img1);
        if($h<$H) { $w=$W/($H/$h);
                $img2=ImageCreateTrueColor($w,$h);
                ImageCopyResampled($img2,$img1,0,0,0,0,$w,$h,$W,$H);
        } else { $h=$H; $w=$W; $img2=$img1; }

	if($s!='')  pic_podpis($img2,$w,$h,$s,$r); 

        ImageJpeg($img2, $to, $q);
        ImageDestroy($img2);
        ImageDestroy($img1);
}


function pic_podpis($img,$w,$h,$s,$fs=20,$font) { 
	if($font=='') $font=$GLOBALS['foto_ttf'];

//	die("<p>font: ".$font." text:".$s);

$s=wu($s);
$rez=imagettfbbox($fs,0,$font,$s); $x=$w-$rez[4]-$fs/4; $y=$h-$rez[3]-$fs/4; // координаты текста
// каким цветом $black/$white ?
$c=(imagecolorat($img,$x,$y)>imagecolorallocate($img,127,127,127)?imagecolorallocate($img,0,0,0):imagecolorallocate($img,255,255,255));
imagettftext($img,$fs,0,$x,$y,$c,$font,$s);
}

?>
