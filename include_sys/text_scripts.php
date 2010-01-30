<?php

SCRIPT_ADD($GLOBALS['www_design']."pravka_blog.js"); // подгрузить внешний скрипт
SCRIPT_ADD($GLOBALS['www_design']."pins.js"); // подгрузить pins
SCRIPT_ADD($GLOBALS['www_design']."JsHttpRequest.js"); // подгрузить аякс

// SCRIPTS_mine();

SCRIPTS("text_scripts","
var dnevnik_data='".$Date."';
var ctrloff=".($_COOKIE['ctrloff']=='off'?1:0).";
");

?>
