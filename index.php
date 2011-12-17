<?php

// die('REMONT');

include "config.php";
$_SCRIPT=array(0=>"var page_onstart=[];");
$_SCRIPT_ADD=$_STYLE=$_HEADD=array();
include $include_sys."_autorize.php";
include $include_sys."_modules.php";
include_once $include_sys."blogpage.php"; // потом удалить!!!

// заплатки для инсталла:
if(!function_exists('acc_link')){function acc_link($s){};}

//if(!$admin) die('REMONT');

//====================================
// определим блог и прогоним нахуй www.
$MYHOST=substr($httpsite,7);

if($_SERVER["HTTP_HOST"]!=$MYHOST) { list($acc,$l,$s)=explode('.',$_SERVER["HTTP_HOST"],3);
  if($acc=='www') {
     if(isset($redirect_www)) redirect((substr($_SERVER["HTTP_HOST"],4)==$MYHOST?$httpsite:str_replace('//','//'.$l.'.',$httpsite)).$_SERVER["REQUEST_URI"]);
     else $acc=$l;
  }
        if(($p=ms("SELECT `acn`,`unic` FROM `jur` WHERE `acc`='".e($acc)
."' ORDER BY (`unic`='$unic') DESC LIMIT 1","_1"))===false) { $acn=-1; $ADM=0; }
        else { $acn=$p['acn']; $ADM=($unic==$p['unic']?1:0); if($ADM) $ttl=0; }
} else { $acc=0; $acn=0; $ADM=0; }
//====================================

mystart();

// if($admin) idie('#<pre>'.print_r($_SERVER,1));


//SCRIPTS("jog_kuki",$jog_scripts." function setIsReady() { c_rest('".$uc."'); c_rest('lju'); } ");
//SCRIPTS("jog_kuki","function setIsReady() { c_rest('".$uc."'); c_rest('lju'); }");

SCRIPT_ADD($GLOBALS['www_design']."JsHttpRequest.js"); // подгрузить аякс

$hashpage=rand(0,1000000); $hashpage=substr(broident($hashpage.$hashinput),0,6).'-'.$hashpage;


function ARTICLE_Date($Date) { global $article;

	$article=ms("SELECT * FROM `dnevnik_zapisi` ".WHERE("`Date`='".e($Date)."'"),"_1");
        if($article!==false) ARTICLE();

	$Date2=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0")." ORDER BY `DateDatetime` DESC LIMIT 1","_l");
	idie("Заметка, датированная числом ".h($Date)." не существует. Скорее всего ее никогда не было. Может, она удалена или закрыта.
Последняя заметка дневника находится <a href='".$GLOBALS['wwwhost'].$Date2.".html'>здесь</a>. Также можно посмотреть
<a href='".$GLOBALS['wwwhost']."contents/'>оглавление</a>","HTTP/1.1 404 Not Found");
}

function ARTICLE() { global $_PAGE,$article,$file_template,$wwwhost,$REF,$httpsite;

        if($GLOBALS['acn'] && !empty($GLOBALS['mnogouser']) && !$GLOBALS['mnogouser_html']) {
                $article['Body']=h($article['Body']); // экранировать
                $article['Header']=h($article['Header']);
        }

	$article=mkzopt($article);

	$REF=$_SERVER["HTTP_REFERER"]; if($REF!='' && substr($REF,0,strlen($httpsite))!=$httpsite) {
	        include_once $GLOBALS['include_sys']."_refferer.php"; $GLOBALS['linksearch']=refferer($REF,$article['num']);
	}

if(empty($article['template'])) $article['template']='blog';

$f=$file_template.$article['template'].'.html';
if(!is_file($f)) { $f=$file_template.$article['template'].'.htm'; if(!is_file($f)) idie('Template not found: '.h($article['template'])); }
$design=file_get_contents($f);

$_PAGE=array();
$_PAGE['acc']=h($GLOBALS['acc']);
$_PAGE['acc_link']=acc_link($GLOBALS['acc']);
$_PAGE['num']=$article['num'];
$_PAGE['Date']=h($article['Date']);
$_PAGE['prevlink']=$wwwhost;
$_PAGE['nextlink']=$wwwhost;
$_PAGE['uplink']=$wwwhost;
$_PAGE['downlink']=$wwwhost."contents/";
$_PAGE['www_design']=$GLOBALS['www_design'];
$_PAGE['admin_name']=h($GLOBALS['admin_name']);
$_PAGE['httphost']=$GLOBALS['httphost'];
$_PAGE['wwwhost']=$wwwhost;
$_PAGE['signature']=$GLOBALS['signature'];
$_PAGE['wwwcharset']=h($GLOBALS['wwwcharset']);
$_PAGE['hashpage']=$GLOBALS['hashpage'];
// $_PAGE['foto_www_preview']=$GLOBALS['foto_www_preview'];
// $_PAGE['foto_res_small']=$GLOBALS['foto_res_small'];
$_PAGE['design']=modules($design);
if($GLOBALS['ADM']||$GLOBALS['admin']) $_PAGE['design'].=file_get_contents($GLOBALS['file_template'].'adminpanel.htm');
exit;
}


list($path)=explode('?',$GLOBALS['MYPAGE']); $path=rtrim(rpath($path),'\/');
$pwwwhost=str_replace('/','\/',$wwwhost);

// ============== начали выяснять, какой модуль подцепить ==============

// рядовая заметка
if(preg_match("/^".$pwwwhost."(\d\d\d\d\/\d\d\/\d\d.*)\.html/si", $path, $m)) ARTICLE_Date($m[1]);

// заметка месяца
if(preg_match("/^".$pwwwhost."(\d\d\d\d\/\d\d)$/si", $path, $m)) ARTICLE_Date($m[1]); // Заметка




// Корень => Последняя заметка ???
if($path."/"==$wwwhost && empty($_SERVER['QUERY_STRING'])) {
 	// Yandex заебал индексировать титул блога! Он же меняется все время! Блять, для кого robots.txt был написан?!
 	if(($rootpage=='' || strstr($rootpage,'last')) && (strstr($BRO,'Yandex') || $IP=='78.110.50.100')) {
 	logi("yandex_nah.log","\n".date("Y/m/d H:i:s")." Yandex пошел нахуй");
 	redirect('http://natribu.org/?WWFuZGV4JSDy+yDt6PXz-yDt5SD36PLg5fj8IHJvYm90cy50eHQg6CDr5efl+Pwg6vPk4CDt5SDt4OTuLiDfIOTr-yDq7uPuIHJvYm90cy50eHQg7+jx4Os-JSDv8OXq8OD54Okg6O3k5erx6PDu4uDy-CDy6PLz6yDv5fDl4OTw5fHg9ujoIPLl7CDq7u3y5e3y7uwsIOru8u7w++kg7+4g7OXx8vMg7+Xw5eDk8OXx4Pbo6C4gx+Dl4eDrLCBZYW5kZXgsIPfl8fLt7uUg8evu4u4h');
 	}

/*
	if(isset($_GET['module'])) {
		if(preg_match("/[^0-9a-z_\-\.\/]+/si",$mod_name)) idie("Error 404: wrong name \"<b>".h($mod_name)."</b>\"");
		$mod_name=substr($path,strlen($wwwhost)); $mod_name=str_replace('..','.',$mod_name);
		$article=array('template'=>'module','num'=>0,'Date'=>h($mod_name)); ARTICLE(); }
*/

	if(!empty($rootpage)) {
		if(substr($rootpage,0,6)=='index.') { // index в базе дневника
			$article=ms("SELECT * FROM `dnevnik_zapisi` ".WHERE("`Date`='".e($rootpage)."'")." LIMIT 1","_1");
			if($article!==false) ARTICLE();
			if(!empty($acc)) {
			$article=array('num'=>0,'Date'=>h($rootpage),'opt'=>ser(array('template'=>'error'))); ARTICLE();
			}
		}
		redirect($httphost.$rootpage); // если в конфиге установлен адрес заметки по умолчанию
	}

//	$admin=0; $podzamok=1;

	$last=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0")." ORDER BY `Date` DESC LIMIT 1","_l");

/*
if($admin) die('tony #4'."<p>
<br>admin: `$admin`
<br>podzamok: `$podzamok`
<br>acn: `$acn`
<br>ADM: `$ADM`
<p>".h("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0")." ORDER BY `Date` DESC LIMIT 1"));
*/

	if($last=='') {
	if(/*!msq_table('site') and */!msq_table('dnevnik_zapisi')) redirect($httphost."install",302); // в админку, если по первому разу
	redirect($httphost."editor",302); // в редактор, если записей нет
	} redirect($httphost.$last.".html",302); // на последнюю
	/*
	300 Multiple Choices (Множество выборов).
	301 Moved Permanently (Перемещено окончательно).
	302 Found (Найдено).
	303 See Other (Смотреть другое).
	304 Not Modified (Не изменялось).
	305 Use Proxy (Использовать прокси).
	306 (зарезервировано).
	307 Temporary Redirect (Временное перенаправление).
	*/

}



// ===== подключение внешних модулей из директории /module/* ====
if(preg_match("/[^0-9a-z_\-\.\/]+/si",$mod_name)) idie("Error 404: wrong name \"<b>".h($mod_name)."</b>\"");
$mod_name=substr($path,strlen($wwwhost)); $mod_name=rpath($mod_name);

// сперва ищем в модулях-страницах (темплайтах, вызывающих модуль - это более новый прогрессивный формат)
//if(file_exists($file_template.$mod_name.".htm")) { $article=array('template'=>$mod_name,'num'=>0,'Date'=>h($mod_name)); ARTICLE(); }

// затем ищем в модулях
$mod=$host_module.$mod_name.".php"; if(file_exists($mod)) { include($mod); exit; }

// затем в базе site
//$text=ms("SELECT `text` FROM `site` ".WHERE("`name`='".e($mod_name)."' AND `type`='page'"),"_l",$ttl);
//if($text!='') { $name=$mod_name; include("site.php"); exit; }

// затем в базе дневника
$article=ms("SELECT * FROM `dnevnik_zapisi` ".WHERE("(`Date`='".e($mod_name)."'
OR `Date`='".e($mod_name)."/index.htm'
OR `Date`='".e($mod_name)."/index.shtml'
OR `Date`='".e($mod_name)."/index.html'
)")." LIMIT 1","_1"); if($article!==false && $article!='') {
	if(preg_match("/^\d\d\d\d\/\d\d\/\d\d[\_\d]*$/si",$mod_name)) idie("Wrong name.<p>Try: <a href='".get_link($mod_name)."'>".get_link($mod_name)."</a>");
	ARTICLE();
}

// или в таблице редиректов, пример:
// ?p=171 2011/04/21.html
// ?page_jopa=666 2011/08/16.html

if(($p=ms("SELECT `text` FROM `site` WHERE `name`='redirect'","_l",$ttl*10))!==false) {
        if($mod_name=='') $mod_name='?'.$_SERVER['QUERY_STRING'];
        $e=explode("\n",$p);
        foreach($e as $p) { list($a,$b)=explode(' ',$p,2); $b=trim($b);
                if(strstr($a,'/') && preg_match($a,$mod_name) || $a==$mod_name) redirect($httphost.($b=='/'?'':$b));
        }
}

// и если совсем ничего не нашлось

// то еще ищем в папке страниц: $site_module = $filehost."site_module/";

$modp=strtoupper($mod_name); $mod=$site_module.$modp.".php"; if(file_exists($mod)) {
	$article=array(
		'Date'=>$modp,'Header'=>$modp,'Body'=>'{_'.$modp.':_}',
		'Access'=>'all','DateUpdate'=>0,'num'=>0,'DateDatetime'=>0,'DateDate'=>0,
		'opt'=>'a:3:{s:8:"template";s:5:"blank";s:10:"autoformat";s:2:"no";s:7:"autokaw";s:2:"no";}',
		'view_counter'=>0
        );
//	SCRIPTS("alert(7);page_onstart.push('hotkey_reset=function(){}; hotkey=[];');");
	ARTICLE();
}

if(preg_match("/\.js/si",$mod_name)) die( ($admin?"alert('Admin $admin_name! Script not found:\\n".h($mypage)."')":"") ); // запрошен .js

header("HTTP/1.1 404 Not Found");
header("Status: 404 Not Found");

$article=array('num'=>0,'Date'=>h($mod_name),'opt'=>ser(array('template'=>'error')));

ARTICLE();


//===============================================================================================================================
function SCRIPTS_mine() { global $BRO;

//  /*httpsite'].$GLOBALS['mypage']."';

SCRIPTS("main","
var acn='".$GLOBALS['acn']."';
var MYHOST='".$GLOBALS['MYHOST']."';
var hashpage='".$GLOBALS['hashpage']."';
var wwwhost='".$GLOBALS['wwwhost']."';
var admin=".($GLOBALS['admin']?1:0).";
var mypage='".acc_link($GLOBALS['acc'],$GLOBALS['mypage'])."';
var uc='".$GLOBALS['uc']."';
var www_js='".$GLOBALS['www_js']."';
var www_css='".$GLOBALS['www_css']."';
var wwwcharset='".$GLOBALS['wwwcharset']."';
var www_design='".$GLOBALS['www_design']."';
var www_ajax='".$GLOBALS['www_ajax']."';
var num='".$GLOBALS['article']['num']."';
var up='".$GLOBALS['up']."';
var realname=\"".njsn($GLOBALS['imgicourl'])."\";
var aharu='".$GLOBALS['aharu']."';
"); //if(aharu && admin) alert(up);
//var page_onstart=[];
// ".($GLOBALS['admin']?"setTimeout(\"inject('counter.php?num=".$GLOBALS['article']['num']."&ask=1&old=0');\",5000);":'')."

//if($GLOBALS['admin']) {
	$file=$GLOBALS['filehost']."js/main.js";
	$i=md5_file($file); $n="main-".substr($i,4,12).".js";
	$name=$GLOBALS['filehost']."js/".$n;
	if(!is_file($name)) copy($file,$name);
	SCRIPT_ADD($GLOBALS['www_js'].$n);
//}
//SCRIPT_ADD($GLOBALS['www_js']."main.js");

SCRIPT_ADD($GLOBALS['www_js']."ipad.js");

}
//===============================================================================================================================

?>