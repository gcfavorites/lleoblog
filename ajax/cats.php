<?php //Рубрикатор (c) Тема Павлов http://temapavloff.ru/blog/

include "../config.php"; include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

//Запрашиваем всплывающее окошко со всеми рубриками
if($_REQUEST["mode"]=='get_cats') { 
	$pp = ms("SELECT `name` FROM `dnevnik_cats` ORDER BY `name` ASC","_a");
	$s=''; foreach($pp as $p) { $s.="<a style=\"margin: 4px;\" href=".$httphost."blog?cat=".urlencode($p["name"]).">".$p["name"]."</a> "; }
	$man=($admin?"<p style=\'margin-top: 10px;\' align=\'right\'><span style=\'font-size: 8pt;\'  class=scr onclick=\"majax(\'cats.php\',{mode:\'manage\'})\">Управление</span></p>":'');
	$s="<fieldset><legend>Рубрикатор</legend>".$s.$man."</fieldset>";
	otprav("helps('cats', '".$s."');");
}

//Выполняем поиск по рубрикам (для редактора заметок)
if($_REQUEST["mode"]=='search_cats') {
	if($_REQUEST["text"]=='') otprav("idd('c_popup').innerHTML=''; idd('c_popup').style.visibility='hidden';");
	$pp = ms("SELECT `name` FROM `dnevnik_cats` ".WHERE("`name` LIKE '".e($_REQUEST["text"])."%'")." ORDER BY `name` DESC LIMIT 0,20","_a");
	$s=''; foreach($pp as $p) { $s.="<li onclick=\"idd('".$_REQUEST["idhelp"]."_cat').value = this.innerHTML;\">".$p["name"]."</li>"; }
	$s="<ul class=cat_its>$s</ul>";
	otprav("idd('c_popup').innerHTML='".e($s)."'; idd('c_popup').style.visibility = 'visible';");
}

//Управление рубриками
if($_REQUEST["mode"]=='manage') { if(!$admin) idie("Надо залогиниться админом!");
	otprav("helps('cats', '".manage()."');");
}

if($_REQUEST["mode"]=='del') { if(!$admin) idie("Надо залогиниться админом!");
	msq_update('dnevnik_zapisi',array('cat'=>''),"WHERE `cat`='".$_REQUEST["name"]."'");
	msq("DELETE FROM `dnevnik_cats` WHERE `name`='".$_REQUEST["name"]."'");
	otprav("helps('cats', '".manage()."');");
}

function manage() { global $www_design;
	$pp = ms("SELECT `name` FROM `dnevnik_cats` ORDER BY `name` ASC","_a");
	$s=''; foreach ($pp as $p) { $s.="<span style=\"margin: 4px;\">".$p["name"]."<img src=\"".$www_design."e3/remove.png\" style=\"cursor:pointer;\" onclick=\"if(confirm(\'Точно удалить?\')) {majax(\'cats.php\',{mode:\'del\',name:\'".$p["name"]."\'})}\"></span>"; }
	return "<fieldset><legend>Рубрикатор</legend>$s</fieldset>";
}
?> 
