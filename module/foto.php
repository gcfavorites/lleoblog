<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// Правки

if(!$admin) redirect($wwwhost."login/"); // посторонних - нахуй

blogpage();

$_PAGE["header"]=$_PAGE["title"]="Фотоальбом";

/*
STYLES("Всплывающее окно фотки","

.fotoa{ width:200px; height:150px; float: left; text-align: center; border: 1px solid black; }
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
*/

SCRIPTS("Всплывающая фотка","

function foto(e) {
        helps('bigfoto',\"<img onclick=\\\"clean('winfoto')\\\" src='\"+e+\"'>\");
	o=idd('bigfoto');
	var imgx=o.style.width;
	var imgy=o.style.height;
    o.style.top = (getWinH()-imgy)/2+getScrollW()+'px';
    o.style.left = (getWinW()-imgx)/2+getScrollH()+'px';
    o.style.visibility = 'visible';
}
");


$fotoset=unserialize(file_get_contents($foto_file_small."_fotoset.dat")); if($fotoset===false) $fotoset=array();
if(!intval($fotoset['X'])) $fotoset['X']=$foto_res_small;

// <p>размер фотки: <a href=\"javascript:majax('foto.php',{a:'formfotoset'})\">".$fotoset["X"]."</a>

$s = "<center><p><a href=\"javascript:majax('foto.php',{a:'uploadform',hid:hid})\">закачать новую картинку</a></center>";

$p=glob($foto_file_small."*.jpg");

foreach($p as $l) {
	$l=preg_replace("/^.*\/([^\/]+)$/si","$1",$l);
	$s.="
<div class='ll' onclick=\"foto('".$foto_www_small.$l."')\"><img src='".$foto_www_preview.$l."' hspace=5 vspace=5>
<div class=br>".$l."</div></div>";
}

die($s);


?>
