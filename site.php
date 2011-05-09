<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// Отображение страниц сайта

//********************************************************************
/*	$alfrus=array(
'а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','ё',
'А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','Ё');

	$alftransl=array(
'a','b','v','g','d','e','zj','z','i','j','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sch','','y','','e','u','ya','yo',
'a','b','v','g','d','e','zj','z','i','j','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sch','','y','','e','u','ya','yo',
);

	$alfrusl=implode("",$alfrus)."\\-\\_\\.";

function translit($l) { global $alfrus,$alftransl,$alfrusl;
	return str_replace($alfrus,$alftransl,trim(preg_replace("/[^".$alfrusl."]+/s","_",$l),"_"));
}
*/
//********************************************************************

$_PAGE=array(
        'myscript'=>'',
        'mystyle'=>'',

        'prevlink'=>$wwwhost,
        'nextlink'=>$wwwhost,
        'uplink'=>$wwwhost,
        'downlink'=>$wwwhost."contents/",

        'www_design'=>$www_design,
        'admin_name'=>$admin_name,
        'httphost'=>$httphost,
        'wwwhost'=>$wwwhost,
        'signature'=>$signature,
        'wwwcharset'=>$wwwcharset,

        'mypage'=>$mypage,

        'admin'=>($admin?'1':'0'),

        'hashpage'=>$hashpage,
        'foto_www_preview'=>$foto_www_preview,
        'foto_res_small'=>$foto_res_small
);

	// взять главную страничку

$p=ms("SELECT `id`,`text` FROM `".$db_site."` ".WHERE("`name`='".$name."'"),"_1",$ttl); $_PAGE['page_id']=$p['id']; $s="{body}".$p['text'];

	// добавить в начало ссылку на редактор

if($admin) $_PAGE['body'].="<div style='position:absolute;left:4px;top:4px;z-index:999;border:1px dashed rgb(255,0,0);
padding: 6px; background-color: rgb(255,252,223); text-align:justify; font-size:9px'>
<a href='".$wwwhost."adminsite/?edit=".$p['id']."'>редактировать</a></div>";

	// повызывать все процедуры в цикле

$_PAGE['design']=modules($s); exit;
	
?>
