<?

// $s .= msq_del_pole("dnevnik_zapisi","wwwwwww","это тестовая хуйня, я ее создал просто так");
// $s .= msq_add_pole("dnevnik_zapisi","wwwwwww","enum('no','p','pd') default 'no'","это тестовая хуйня ее нужно создать!");

$s .= msq_add_pole("dnevnik_zapisi","autoformat","enum('no','p','pd') default 'no'","заработает автоформатирование заметок");
$s .= msq_add_pole("dnevnik_zapisi","autokaw","enum('auto','no')","тогда можно будет отключать автообработку кавычек и тире");
$s .= msq_add_pole("dnevnik_zapisi","count_comments_open","int(10) unsigned default '0'","это связано с оптимизацией движка");
$s .= msq_del_pole("dnevnik_zapisi","include","поле include больше не нужно, мы перешли на систему модулей II поколения");

$s .= msq_add_pole("dnevnik_zapisi","template","varchar(32) NOT NULL default 'blog'","темплайт дизайна задается в каждой заметке");


/*
$action='go';
$timesec=10;
$id='tiktime';

SCRIPTS("var tiktimen=".$timesec.";
function tiktime(id) { document.getElementById(id).innerHTML = tiktimen--;
setTimeout(\"tiktime('\" + id + \"')\", 1000);
}");


if($_GET['action']==$action) {
	$GLOBALS['admin_upgrade']=true;
	$s .= admin_kletka('action',"<font color=red>Ничего не трогать! Страницы обновляются сами:
<span id='".$id."'><script>tiktime('".$id."')</script></span>! Этап: <blink>$skip</blink></font>");
	$path = $mypage."?skip=".($skip+1)."&action=".$action;
	$s .= "<noscript><meta http-equiv=refresh content=\"".$timesec.";url=\"".$path."\"></noscript><script> setTimeout(\"location.replace('".$path."')\", ".($timesec*1000)."); </script>";
} else {
	$s .= admin_kletka('action',"нужно начать ремонт базы",'go');
}

*/


?>