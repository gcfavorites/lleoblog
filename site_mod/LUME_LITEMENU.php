<?php // �������� ����

function LUME_LITEMENU($e) { $p=explode("\n",$e);

/*
        <div class="litemenu_nav"><a href="/about.html">� ����������</a></div>
        <div class="litemenu_nav"><a href="/equipment.html">������������</a></div>
        <div class="litemenu_nav"><a href="/portfolio.html">���������</a></div>
        <div class="litemenu_nav"><a href="/price.html">������ � ����</a></div>
        <div class="litemenu_active"><a href="/news.html">�������</a></div>
*/

$o='';

	foreach($p as $l) if(c($l)!='') {
		list($link,$txt)=explode("|",$l); $txt=c($txt); $link=c($link);

		if(strstr($GLOBALS['mypage']."\001",$link."\001")) $o.="<div class='litemenu_active'>";
		else $o.="<div class='litemenu_nav'>";


		$o .= "<a href='".h($link)."'>".$txt."</a></div>";

		}

	return "<div class='litemenu'>;".$o."</div>";

}

?>