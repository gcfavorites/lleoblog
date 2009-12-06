<?php
/*
function text_scripts_ajax() { global $_PAGE;
$_PAGE["ajaxscript"]="<script type='text/javascript' language='JavaScript' src='".$GLOBALS["www_design"]."JsHttpRequest.js'></script>\n";
}
	text_scripts_ajax();
*/
// include_once("text_scripts.php"); $prostynka=text_scripts();

//function text_scripts($data='0000/00/00') { global $hashpage,$wwwhost,$admin,$www_design;

SCRIPT_ADD($www_design."pravka_blog.js"); // подгрузить внешний скрипт
SCRIPT_ADD($www_design."pins.js"); // подгрузить pins
SCRIPT_ADD($www_design."JsHttpRequest.js"); // подгрузить внешний скрипт

SCRIPTS("text-scripts","
var hashpage='".$hashpage."';
var ajax_pravka='".$wwwhost."ajax_pravka.php';
var wwwhost='".$wwwhost."';
var admin=".($admin?1:0).";
var www_design='".$www_design."';
var dnevnik_data='".$Date."';
var ctrloff=".($_COOKIE['ctrloff']=='off'?1:0).";

function comment(text,id,i,action) {
// document.getElementById(id).innerHTML = \"<center>жди, идет загрузка...</center>\";
JsHttpRequest.query('".$wwwhost."ajax_comments.php',
{ action: action, i: i, id: id, text: text },
function(responseJS, responseText) { if(responseJS.status) {
document.getElementById(id).innerHTML = responseJS.otvet;
}},true);
}

function del_comment(id)	{ if(confirm('Точно удалить?')) comment('',id,0,'del'); }
function info_comment(id,i)	{ comment('',id,i,'info'); }
function edit_comment(id,i)	{ comment('',id,i,'edit'); }
function ans_comment(id,i)	{ comment('',id,i,'ans'); }
function sec_comment(id,i,act)	{ comment('',id,i,act); }
function jjote(id,i)		{ comment('none',id,i,'rulit'); }
function sspam(id,i)		{ comment('none',id,i,'spamit'); }
function ljjote(id,i)		{ comment('none',id,i,'ans_rulit'); }
function lsspam(id,i)		{ comment('none',id,i,'ans_spamit'); }
");

//}
?>
