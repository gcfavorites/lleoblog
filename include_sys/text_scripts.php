<?php

SCRIPT_ADD($www_design."pravka_blog.js"); // ���������� ������� ������
SCRIPT_ADD($www_design."pins.js"); // ���������� pins
SCRIPT_ADD($www_design."JsHttpRequest.js"); // ���������� ����

// SCRIPTS_mine();

SCRIPTS("text_scripts","
var dnevnik_data='".$Date."';
var ctrloff=".($_COOKIE['ctrloff']=='off'?1:0).";
");

?>
