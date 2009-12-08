<?php

$GLOBALS['_PAGE']['body'].="<a onclick='hide_foto()'><div class='bar1' onmouseover=\"this.className='bar2'\" onmouseout=\"this.className='bar1'\" id='winfoto'></div></a>";

STYLES("Всплывающее окно фотки","

.fotoa{ width:200; height:150; float: left; text-align: center;}
.fotoa a { color: #814c52; }
.fotot{ font-size: 10px; }

.ok { cursor: pointer; text-align: right; float: left; }
.ok:after { content: url(\"{www_design}e/cancel1.png\"); }

.bar1, .bar2 { position: absolute; z-index:9998; padding: 2px; visibility: hidden; background-color: #F0F0F0 }

.fotoc { margin: 0px 8px 8px 8px; }

.bar1 { border: 1px solid #ccc; }
.bar2 { border: 1px solid blue; }
");


SCRIPTS("Всплывающая фотка","

var imgy=".$GLOBALS['foto_res_small'].";
var imgx=(800/600)*imgy;

function foto(e) { d = document.body; o = document.getElementById('winfoto');
    o.style.top = (getWinH()-imgy)/2+getScrollW()+'px';
    o.style.left = (getWinW()-imgx)/2+getScrollH()+'px';
    o.style.visibility = 'visible';
    o.innerHTML = \"<div class=ok title='Ок' onclick='hide_foto()'></div><img class=fotoc src='\"+e+\"'>\";
    return false;
}

function hide_foto() { document.getElementById('winfoto').style.visibility='hidden'; }
");

SCRIPTS("getWH","
function getScrollW(){ return (document.documentElement.scrollTop || document.body.scrollTop); }
function getScrollH(){ return (document.documentElement.scrollLeft || document.body.scrollLeft); }
function getWinW(){ return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth; }
function getWinH(){ return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight; }
");

// dier($GLOBALS['_PAGE']);
// exit;


/*
        function zabil(id,text) { document.getElementById(id).innerHTML = text; }
        function vzyal(id) { return document.getElementById(id).innerHTML; }
        function zakryl(id) { document.getElementById(id).style.display='none'; }
        function otkryl(id) { document.getElementById(id).style.display='block'; }
*/

?>