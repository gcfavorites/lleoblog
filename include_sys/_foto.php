<?php

$GLOBALS['_PAGE']['body'].="<a onclick='hide_foto()'><div class='bar1'
onmouseover=\"this.className='bar2'\" onmouseout=\"this.className='bar1'\" id='winfoto'></div></a>";

STYLES("����������� ���� �����","

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


SCRIPTS("����������� �����","

var imgy=".$GLOBALS['foto_res_small'].";
var imgx=(800/600)*imgy;

function foto(e) { o=idd('winfoto');
    o.style.top = (getWinH()-imgy)/2+getScrollW()+'px';
    o.style.left = (getWinW()-imgx)/2+getScrollH()+'px';
    o.style.visibility = 'visible';
    o.innerHTML = \"<div class=ok title='��' onclick=\\\"zakryl('winfoto')\\\"></div><img class=fotoc src='\"+e+\"'>\";
    return false;
}
");

?>