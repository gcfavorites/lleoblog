<?php // Работа с фотоальбомом
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

//idie('l');

$hid=intval($_REQUEST["hid"]);
$a=$_REQUEST["a"];

$lastphoto_file=$hosttmp."lastphoto.txt";
$fileset=$foto_file_small."_fotoset.dat";

//=================================== работа с нодами альбома ===================================================================

if($a=='saveset') {

	$X=$_REQUEST['X']; if(!intval($X) or $X<10 or $X>1600) idie('Не паясничай, выставь ширину человеческую.');
	$x=$_REQUEST['x']; if(!intval($x) or $x<5 or $x>200) idie('Не паясничай, выставь ширину превью человеческую.');

	$Q=$_REQUEST['Q']; $q=$_REQUEST['q'];
	if(!intval($q) or $q<50 or $q>95 or !intval($Q) or $Q<50 or $Q>95) idie('Качество имеет смысл делать в пределах 50-95%');
	$dir=$_REQUEST['dir'];
	$logo=$_REQUEST['logo'];

	if(file_put_contents($fileset,serialize(array('X'=>$X,'x'=>$x,'q'=>$q,'Q'=>$Q,'dir'=>$dir,'logo'=>$logo))) ===false)
	idie("Ошибка записи $fileset!");

	otprav("clean('fotoset')");

}

//=================================== работа с нодами альбома ===================================================================
if($a=='formfotoset') { $idhelp='fotoset';

$fotoset=get_fotoset();

//  <tr><td>закачать новую картинку:</td><td><div class=l onclick=\"majax('foto.php',{a:'uploadform',hid:hid})\">здесь</div></td></tr>

$s="<table>
<tr><td>ширина картинки:</td><td><input id='fotoset_X' size=4 type=text name='X' value='".h($fotoset['X'])."'>px</td></tr>
<tr><td>ширина превью:</td><td><input id='fotoset_Q'size=3 type=text name='Q' value='".h($fotoset['Q'])."'>%</td></tr>
<tr><td>качество картинки:</td><td><input id='fotoset_x' size=4 type=text name='x' value='".h($fotoset['x'])."'>px</td></tr>
<tr><td>качество превью:</td><td><input id='fotoset_q' size=3 type=text name='q' value='".h($fotoset['q'])."'>%</td></tr>
<tr><td>папка:</td><td><input id='fotoset_dir' size=15 type=text name='dir' value='".h($fotoset['dir'])."'></td></tr>
<tr><td>подпись:</td><td><input id='fotoset_logo' size=25 type=text name='logo' value='".h($fotoset['logo'])."'></td></tr>
</table>
<input type=submit value='Save' onclick=\"edit_savefotoset()\">";

$s="
edit_savefotoset=function(){
	var ara={a:'saveset'};
        var nara=['X','x','Q','q','dir','logo']; for(var l in nara) { l=nara[l]; ara[l]=idd('fotoset_'+l).value; }
        majax('foto.php',ara);
};

helps('fotoset',\"<fieldset><legend>Настройки фото</legend>".njsn($s)."</fieldset>\"); idd('fotosetx').focus();
";

otprav($s);
}
//=================================== работа с нодами альбома ===================================================================
if($a=='albumgo') { $id=$_REQUEST["id"]; $id=preg_replace("/\.+/s",'.',$id);

	$node = array();

	$urln=$foto_file_small.$id.'*'; $a=glob($urln);

	$sl=strlen($foto_file_small);

        foreach($a as $n=>$l) {
                $l2=substr(strrchr($l,'/'),1);
                $url=substr($l,$sl).'/';
                if(is_dir($l)) {
			if($url!='/pre/' and $url!='/mic/') $node[] = array('i'=>$url,'t'=>$l2,'f'=>1);
			unset($a[$n]);
		}
        }

	$s=''; foreach($a as $l) {
        	$l = substr($l,$sl+1);

/*
$foto_small="photo/";  // сюда будут писаться обработанные закачанные фотки
$foto_preview="photo/pre/"; // сюда будут писаться превьюшки закачанных фоток

$foto_file_small=$filehost.$foto_small;
$foto_www_small=$wwwhost.$foto_small;
$foto_res_small=600;
$foto_qality_small=85;

$foto_file_preview=$filehost.$foto_preview;
$foto_www_preview=$wwwhost.$foto_preview;
$foto_res_preview=100;
$foto_qality_preview=70;

*/





	        $s.="<img src=\"".$foto_www_preview."$l\" width=50 height=50 border=1 vspace=1> ";
        }
	$node[] = array('i'=>"",'t'=>$s,'f'=>0);

	$_RESULT["data"] = $node;
	$_RESULT["albumdir"] = $id;
	$_RESULT["status"] = true;
	exit;
}

//=================================== setdir ===================================================================
if($a=='setdir') { otprav("helps('foto_$hid',\"<fieldset><legend>выбираем папку</legend>??</fieldset>\");"); }

if($a=='savedir') { $dir=$_REQUEST["dir"]; $dir=h(preg_replace("/\.+/s",'.',$dir));
	file_put_contents($lastphoto_file,$dir);
	otprav("clean('foto'); majax('foto.php',{a:'uploadform'});");
}

//=================================== editpanel ===================================================================
if($a=='uploadform') {

if(is_file($lastphoto_file)) $kuda=trim(file_get_contents($lastphoto_file)); else $kuda='';
// if($kuda='') $kuda='/';

otprav("helps('foto_$hid',\"<fieldset><legend>закачиваем новое фото</legend>"
."<div class=ll style='font-size: 13px' onclick=\\\"clean('foto_$hid'); majax('foto.php',{a:'album'})\\\">".$kuda."</div> <a href=\\\"javascript:majax('foto.php',{a:'formfotoset'})\\\" class=br>настройки</a><br>"
."<form enctype='multipart/form-data'>"
."<input type=file id='fotou_$hid' onchange=\\\"majax('foto.php',{a:'upload',hid:'$hid',kuda:'$kuda',file:idd('fotou_$hid')})\\\"></form>"
."</fieldset>\");");

}
//=================================== editpanel ===================================================================
if($a=='album') {

// <script>onload = function() { tree("root") }</script>
// <p>My photo <span onclick='tree(\"root\")'>albums</span>:

$s="<ul class='Container' id='root'>
  <li class='Node IsRoot IsLast ExpandClosed' id='/'>
    <div class='Expand'></div>
    <div class='Content'>photo</div>
    <ul class='Container'>
    </ul>
  </li>
</ul>
";

otprav( "
        loadScript('tree.js');
        loadCSS('tree.css');

	albumdirclick=function(){ majax('foto.php',{a:'savedir',dir:idd('albumdir').innerHTML}); };

        helps('foto',\"<fieldset id='commentform'><legend>фотоальбом</legend><div style='width: 750px'><div id='albumdir' class=l onclick='albumdirclick()'></div>".njs($s)."</div></fieldset>\");
        tree('root');
");

}

//=================================== editpanel ===================================================================
if($a=='upload') {

if(count($_FILES)>0) foreach($_FILES as $FILE) if(is_uploaded_file($FILE["tmp_name"])) { $fname=h($FILE["name"]);

$fotoset=get_fotoset();

	if(!preg_match("/\.jpe*g$/si",$fname)) idie("Это разве фотка?");
	if(preg_match("^\./si",$fname)) idie("Имя с точки?");
	if(strstr($fname,'..')) idie("Ошибка. Хакерствуем, бля?");

	if(is_file($foto_file_small.$fname)) idie("Этот файл уже есть.");

//	idie("kuda=$kuda");

//	obrajpeg($FILE["tmp_name"],$foto_file_small.$fname,$foto_res_small,$foto_qality_small,$foto_logo);
//	obrajpeg($foto_file_small.$fname,$foto_file_preview.$fname,$foto_res_preview,$foto_qality_preview);

	obrajpeg($FILE["tmp_name"],$foto_file_small.$fname,$fotoset['X'],$fotoset['Q'],$fotoset['logo']);
	obrajpeg($foto_file_small.$fname,$foto_file_preview.$fname,$fotoset['x'],$fotoset['q']);

//	helps('winfoto',\"<img onclick=\\\"clean('winfoto')\\\" src='\"+f+\"'>\");

otprav("

foto=function(f){
	helps('bigfoto',\"<img onclick=\\\"clean('winfoto')\\\" src='\"+f+\"'>\");
	idd('bigfoto').style.top = mouse_y+'px'; //(getWinH()-imgy)/2+getScrollW()
	idd('bigfoto').style.left = (getWinW()-".$foto_res_small.")/2+getScrollH()+'px';
};

helps('foto_$hid',\"<img onclick=\\\"foto('".$foto_www_small.$fname."')\\\" src='".$foto_www_preview.$fname."'><div align=center class=br>".h($fname)."</div>\");");

//otprav("zabil('foto_$hid',\"<div class='fotoa'><div onclick=\\\"return foto('".$foto_www_small.$fname."')\\\">"
//."<img src='".$foto_www_preview.$fname."' hspace=5 vspace=5><div class='fotot'>".$fname."</div></div></div>\\\");");

	} else idie("Ошибка 2! ".nl2br(h(print_r($_FILES,1))));

}


//==================================================================================================

function obrajpeg($from,$to,$X=150,$q=80,$s,$r=10) { // set_time_limit(0);
        $img1=ImageCreateFromJpeg($from); $W=ImagesX($img1); $H=ImagesY($img1);
        if($X<$H) { $Y=$X*$H/$W;
                $img2=ImageCreateTrueColor($X,$Y);
                ImageCopyResampled($img2,$img1,0,0,0,0,$X,$Y,$W,$H);
        } else { $X=$W; $Y=$H; $img2=$img1; }

	if($s!='')  pic_podpis($img2,$X,$Y,$s,$r); 

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



function get_fotoset() { global $fileset;
	$fotoset=unserialize(file_get_contents($fileset)); if($fotoset===false) $fotoset=array();
	if(!intval($fotoset['X'])) $fotoset['X']=$foto_res_small;
	if(!intval($fotoset['Q'])) $fotoset['Q']=$foto_qality_small;
	if(!intval($fotoset['x'])) $fotoset['x']=$foto_res_preview;
	if(!intval($fotoset['q'])) $fotoset['q']=$foto_qality_preview;
	if(!isset($fotoset['dir'])) $fotoset['dir']='';
	if(!isset($fotoset['logo'])) $fotoset['logo']=$foto_logo;
	return $fotoset;
}

?>
