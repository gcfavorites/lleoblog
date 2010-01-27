<?php // Работа с фотоальбомом
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

//idie('l');

$hid=intval($_REQUEST["hid"]);
$a=$_REQUEST["a"];

$lastphoto_file=$hosttmp."lastphoto.txt";

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
."<span class=l style='font-size: 13px' onclick=\\\"clean('foto_$hid'); majax('foto.php',{a:'album'})\\\">".$kuda."</span><br>"
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

	if(!preg_match("/\.jpe*g$/si",$fname)) idie("Это разве фотка?");
	if(preg_match("^\./si",$fname)) idie("Имя с точки?");
	if(strstr($fname,'..')) idie("Ошибка. Хакерствуем, бля?");

	if(is_file($foto_file_small.$fname)) idie("Этот файл уже есть.");

	idie("kuda=$kuda");

	obrajpeg($FILE["tmp_name"],$foto_file_small.$fname,$foto_res_small,$foto_qality_small,$foto_logo);
	obrajpeg($foto_file_small.$fname,$foto_file_preview.$fname,$foto_res_preview,$foto_qality_preview);
//	obrajpeg($foto_file_micro.$fname,$foto_file_preview.$fname,$foto_res_preview,$foto_qality_preview);

//	msq_add($db_site,array(
//               'name'=>e( ($_REQUEST["name"]==''?$fname:$_REQUEST["name"]) ),
//                'text'=>e($fname),
//                'type'=>'photo',
//                'Access'=>e($_REQUEST["Access"])
//        ));

// dier($GLOBALS['_PAGE']);
// exit;

/*
        function zabil(id,text) { document.getElementById(id).innerHTML = text; }
        function vzyal(id) { return document.getElementById(id).innerHTML; }
        function zakryl(id) { document.getElementById(id).style.display='none'; }
        function otkryl(id) { document.getElementById(id).style.display='block'; }


$GLOBALS['_PAGE']['body'].="<a onclick='hide_foto()'><div class='bar1'
onmouseover=\"this.className='bar2'\" onmouseout=\"this.className='bar1'\" id='winfoto'></div></a>";

STYLES("Всплывающее окно фотки","

.fotoa{ width:200; height:150; float: left; text-align: center; border: 1px solid black; }
.fotoa:hover { border: 1px solid blue; }
.fotoa a { color: #814c52; }
.fotot{ font-size: 10px; }

.ok { cursor: pointer; text-align: right; float: left; }
.ok:after { content: url(\"{www_design}e/cancel1.png\"); }

.fotoc { margin: 0px 8px 8px 8px; }

.bar1, .bar2 { position: absolute; z-index:9996; padding: 2px; visibility: hidden; background-color: #F0F0F0 }
.bar1 { border: 1px solid #ccc; }
.bar2 { border: 1px solid blue; }
");


SCRIPTS("Всплывающая фотка","

var imgy=".$GLOBALS['foto_res_small'].";
var imgx=(800/600)*imgy;

function foto(e) { o=idd('winfoto');
    o.style.top = (getWinH()-imgy)/2+getScrollW()+'px';
    o.style.left = (getWinW()-imgx)/2+getScrollH()+'px';
    o.style.visibility = 'visible';
    o.innerHTML = \"<div class=ok title='Ок' onclick=\\\"zakryl('winfoto')\\\"></div><img class=fotoc src='\"+e+\"'>\";
    return false;
}
");

SCRIPTS("getWH","
function getScrollW(){ return (document.documentElement.scrollTop || document.body.scrollTop); }
function getScrollH(){ return (document.documentElement.scrollLeft || document.body.scrollLeft); }
function getWinW(){ return document.compatMode=='CSS1Compat' &&
!window.opera?document.documentElement.clientWidth:document.body.clientWidth; }
function getWinH(){ return document.compatMode=='CSS1Compat' &&
!window.opera?document.documentElement.clientHeight:document.body.clientHeight; }
");

*/


otprav("

foto=function(f){ 
	helps('winfoto',\"<img onclick=\\\"clean('winfoto')\\\" src='\"+f+\"'>\"); o=idd('winfoto');
	o.style.top = mouse_y+'px'; //(getWinH()-imgy)/2+getScrollW()
	o.style.left = (getWinW()-".$foto_res_small.")/2+getScrollH()+'px';
};

helps('foto_$hid',\"<img onclick=\\\"foto('".$foto_www_small.$fname."')\\\" src='".$foto_www_preview.$fname."'>\");");

//otprav("zabil('foto_$hid',\"<div class='fotoa'><div onclick=\\\"return foto('".$foto_www_small.$fname."')\\\">"
//."<img src='".$foto_www_preview.$fname."' hspace=5 vspace=5><div class='fotot'>".$fname."</div></div></div>\\\");");

	} else idie("Ошибка 2! ".nl2br(h(print_r($_FILES,1))));

}


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
