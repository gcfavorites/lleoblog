<?php

function PREWORD($e) { global $IS,$REF,$article; $s='';

	$name=$IS['imgicourl']; if(substr($name,0,1)=='#') $name=false;
	$time_expirie=($article["DateTime"]<time()-86400*30);

if(!empty($GLOBALS['linksearch'])) {

	$u0=$GLOBALS['linksearch'][0];
	$u1=$GLOBALS['linksearch'][1];

if($u0!='') { // если пришел из поисковика
	$s.=LL('preword:poisk',array('name'=>$name,'site'=>h($u1),'string'=>h($u0),'time_expirie'=>$time_expirie));
	// $s.=($name?"Какая встреча, ".$name."!<p>":"")."Ищешь через ".h($u1)." ерунду типа \"<b><u>".h($u0)."</u></b>\"?
	if(stristr($u0,"качать")) $s.=LL('preword:download'); // Теперь насчет \"качать\". Здесь - домашняя страница, а не файлопомойка.

} elseif( !strstr($REF,$GLOBALS["httpsite"]) && !strstr($REF,"livejournal.com") ) { // или если пришел по ссылке
	$s.=LL('preword:poisk',array('name'=>$name,'site'=>h(urldecode($REF)),'time_expirie'=>$time_expirie));
	// По ссылке c <font color=green>".h($fromlink)."</font> вы открыли листок дневника за некое число.";
	}
} elseif($name!='') $s.=LL('preword:opoznan',$name); // Здравствуй, дружище ".$name."!

if($_GET['search']!='') $s.=LL('preword:search',array('search'=>h($_GET['search']),'normal'=>$GLOBALS['mypage']));
// Страница отображена с подсветкой слов \"<span class=search>".h($_GET['search'])."</span>\", <a href='".$GLOBALS['mypage']."'>переключиться в нормальный режим</a>";

return LL('preword',$s); // '"<div class='preword'>$s</div>";
}

?>