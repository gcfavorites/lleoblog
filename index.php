<?php


// die('REMONT');

include "config.php";
include $include_sys."_autorize.php";
$_SCRIPT=$_SCRIPT_ADD=$_STYLE=$_HEADD=array(); include $include_sys."_modules.php";
include_once $include_sys."blogpage.php"; // ����� �������!!!
mystart();

// if($admin) idie('#<pre>'.print_r($_SERVER,1));


//SCRIPTS("jog_kuki",$jog_scripts." function setIsReady() { c_rest('".$uc."'); c_rest('lju'); } ");
//SCRIPTS("jog_kuki","function setIsReady() { c_rest('".$uc."'); c_rest('lju'); }");

SCRIPT_ADD($GLOBALS['www_design']."JsHttpRequest.js"); // ���������� ����

$hashpage=rand(0,1000000); $hashpage=substr(broident($hashpage.$hashinput),0,6).'-'.$hashpage;


function ARTICLE_Date($Date) { global $article;

	$article=ms("SELECT * FROM `dnevnik_zapisi` ".WHERE("`Date`='".e($Date)."'"),"_1");
        if($article!==false) ARTICLE();

	$Date2=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0")." ORDER BY `DateDatetime` DESC LIMIT 1","_l");
	idie("�������, ������������ ������ ".h($Date)." �� ����������. ������ ����� �� ������� �� ����. �����, ��� ������� ��� �������.
��������� ������� �������� ��������� <a href='".$GLOBALS['wwwhost'].$Date2.".html'>�����</a>. ����� ����� ����������
<a href='".$GLOBALS['wwwhost']."contents/'>����������</a>","HTTP/1.1 404 Not Found");
}

function ARTICLE() { global $_PAGE,$article,$file_template,$wwwhost,$REF,$httpsite;

	$article=mkzopt($article);

	$REF=$_SERVER["HTTP_REFERER"]; if($REF!='' && substr($REF,0,strlen($httpsite))!=$httpsite) {
	        include_once $GLOBALS['include_sys']."_refferer.php"; $GLOBALS['linksearch']=refferer($REF,$article['num']);
	}

if(empty($article['template'])) $article['template']='blog';

$f=$file_template.$article['template'].'.html';
if(!is_file($f)) { $f=$file_template.$article['template'].'.htm'; if(!is_file($f)) idie('Template not found: '.$article['template']); }
$design=file_get_contents($f);

$_PAGE=array();
$_PAGE['prevlink']=$wwwhost;
$_PAGE['nextlink']=$wwwhost;
$_PAGE['uplink']=$wwwhost;
$_PAGE['downlink']=$wwwhost."contents/";
$_PAGE['www_design']=$GLOBALS['www_design'];
$_PAGE['admin_name']=$GLOBALS['admin_name'];
$_PAGE['httphost']=$GLOBALS['httphost'];
$_PAGE['wwwhost']=$wwwhost;
$_PAGE['signature']=$GLOBALS['signature'];
$_PAGE['wwwcharset']=$GLOBALS['wwwcharset'];
$_PAGE['hashpage']=$GLOBALS['hashpage'];
$_PAGE['foto_www_preview']=$GLOBALS['foto_www_preview'];
$_PAGE['foto_res_small']=$GLOBALS['foto_res_small'];
$_PAGE['design']=modules($design);
if($GLOBALS['admin']) {
	$l=file_get_contents($GLOBALS['file_template'].'adminpanel.htm');
	$l=str_replace('{num}',$article['num'],$l);
	$l=str_replace('{Date}',$article['Date'],$l);
	$_PAGE['design'].=$l;
}
exit;
}


list($path)=explode('?',$GLOBALS['MYPAGE']); $path=rtrim($path,'\/');
$pwwwhost=str_replace('/','\/',$wwwhost);

// ============== ������ ��������, ����� ������ ��������� ==============

// ������� �������
if(preg_match("/^".$pwwwhost."(\d\d\d\d\/\d\d\/\d\d.*)\.html/si", $path, $m)) ARTICLE_Date($m[1]);

// ������� ������
if(preg_match("/^".$pwwwhost."(\d\d\d\d\/\d\d)$/si", $path, $m)) ARTICLE_Date($m[1]); // �������

// ������ => ��������� ������� ???
if($path."/" == $wwwhost) {
 	// Yandex ������ ������������� ����� �����! �� �� �������� ��� �����! �����, ��� ���� robots.txt ��� �������?!
 	if($rootpage=='' && (strstr($BRO,'Yandex') || $IP=='78.110.50.100')) {
 	logi("yandex_nah.log","\n".date("Y/m/d H:i:s")." Yandex ����� �����");
 	redirect('http://natribu.org/?WWFuZGV4JSDy+yDt6PXz-yDt5SD36PLg5fj8IHJvYm90cy50eHQg6CDr5efl+Pwg6vPk4CDt5SDt4OTuLiDfIOTr-yDq7uPuIHJvYm90cy50eHQg7+jx4Os-JSDv8OXq8OD54Okg6O3k5erx6PDu4uDy-CDy6PLz6yDv5fDl4OTw5fHg9ujoIPLl7CDq7u3y5e3y7uwsIOru8u7w++kg7+4g7OXx8vMg7+Xw5eDk8OXx4Pbo6C4gx+Dl4eDrLCBZYW5kZXgsIPfl8fLt7uUg8evu4u4h');
 	}

	if(isset($_GET['module'])) { $article=array('template'=>'module','num'=>0,'Date'=>h($mod_name)); ARTICLE(); }

	if(!empty($rootpage)) {
		if(substr($rootpage,0,6)=='index.') { // index � ���� ��������
			$article=ms("SELECT * FROM `dnevnik_zapisi` ".WHERE("`Date`='".e($rootpage)."'")." LIMIT 1","_1");
			if($article!==false) ARTICLE();
		}
		redirect($wwwhost.$rootpage); // ���� � ������� ���������� ����� ������� �� ���������
	}

	$last=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0")." ORDER BY `Date` DESC LIMIT 1","_l",$ttl);
	if($last=='') {
	if(!msq_table('site') and !msq_table('dnevnik_zapisi')) redirect($wwwhost."admin"); // � �������, ���� �� ������� ����
	redirect($wwwhost."editor"); // � ��������, ���� ������� ���
	} redirect($wwwhost.$last.".html"); // �� ���������
}

// ������ ����� ����������
if(preg_match("/^".$pwwwhost."(\d\d\d\d)\-(\d\d)\-(\d\d)\.shtml/", $path, $m)) redirect($httphost.$m[1]."/".$m[2]."/".$m[3].".html");




// ===== ����������� ������� ������� �� ���������� /module/* ====
if(preg_match("/[^0-9a-z_\-\.\/]+/si",$mod_name)) idie("Error 404: wrong name \"<b>".h($mod_name)."</b>\"");
$mod_name=substr($path,strlen($wwwhost)); $mod_name=str_replace('..','.',$mod_name);

// ������ ���� � �������-��������� (����������, ���������� ������ - ��� ����� ����� ������������� ������)
//if(file_exists($file_template.$mod_name.".htm")) { $article=array('template'=>$mod_name,'num'=>0,'Date'=>h($mod_name)); ARTICLE(); }

// ����� ���� � �������
$mod=$host_module.$mod_name.".php"; if(file_exists($mod)) { include($mod); exit; }

// ����� � ���� site
$text=ms("SELECT `text` FROM `site` ".WHERE("`name`='".e($mod_name)."' AND `type`='page'"),"_l",$ttl);
if($text!='') { $name=$mod_name; include("site.php"); exit; }

// ����� � ���� ��������
$article=ms("SELECT * FROM `dnevnik_zapisi` ".WHERE("`Date`='".e($mod_name)."'
OR `Date`='".e($mod_name)."/index.htm'
OR `Date`='".e($mod_name)."/index.shtml'
OR `Date`='".e($mod_name)."/index.html'
")." LIMIT 1","_1"); if($article!==false && $article!='') {
	if(preg_match("/^\d\d\d\d\/\d\d\/\d\d[\_\d]*$/si",$mod_name)) idie("Wrong name.<p>Try: <a href='".get_link($mod_name)."'>".get_link($mod_name)."</a>");
	ARTICLE();
}


// � ���� ������ ������ �� �������
if(preg_match("/\.js/si",$mod_name)) die( ($admin?"alert('Admin $admin_name! Script not found:\\n".h($mypage)."')":"") ); // �������� .js

header("HTTP/1.1 404 Not Found");
header("Status: 404 Not Found");
$article=array('num'=>0,'Date'=>h($mod_name),'opt'=>ser(array('template'=>'error')));

ARTICLE();


//===============================================================================================================================
function SCRIPTS_mine() { global $BRO;

SCRIPTS("main","
var hashpage='".$GLOBALS['hashpage']."';
var wwwhost='".$GLOBALS['wwwhost']."';
var admin=".($GLOBALS['admin']?1:0).";
var mypage='".$GLOBALS['httpsite'].$GLOBALS['mypage']."';
var uc='".$GLOBALS['uc']."';
var www_js='".$GLOBALS['www_js']."';
var www_css='".$GLOBALS['www_css']."';
var wwwcharset='".$GLOBALS['wwwcharset']."';
var www_design='".$GLOBALS['www_design']."';
var www_ajax='".$GLOBALS['www_ajax']."';
var num='".$GLOBALS['article']['num']."';
var up='".$GLOBALS['up']."';
var realname=\"".$GLOBALS['imgicourl']."\";
var aharu='".$GLOBALS['aharu']."';
var page_onstart=[];
"); //if(aharu && admin) alert(up);

// ".($GLOBALS['admin']?"setTimeout(\"inject('counter.php?num=".$GLOBALS['article']['num']."&ask=1&old=0');\",5000);":'')."

SCRIPT_ADD($GLOBALS['www_js']."main.js");
SCRIPT_ADD($GLOBALS['www_js']."ipad.js");

}
//===============================================================================================================================

?>
