<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����

// =============================== cron =========================================
$name='cron'; $$name='RUN_NOW';

global $cronfile;
if($PEST[$name]==$$name) {
		$admin_upgrade=true; include_once("cron.php"); 
		$s .= admin_kletka($name,"<font color=green><i>��������� �������:</i></font>".$cronrez,$$name);
} else {
	$mt=(is_file($cronfile)?(time()-filemtime($cronfile)):9999999);
	if($mt > 60*60) { $s .= admin_kletka($name,"<font color=red>cron ��������� ��� ���������� ".floor($mt/60)." ����� �����!
������� crontab ��� ������� �������!</font>",$$name); }
	else if($mt > 60*30) $s .= admin_kletka($name,"��������� ��� ���������� ".floor($mt/60)." ����� �����",$$name);
}
// =============================== memcache =========================================

$s .= admin_kletka('memcache',($GLOBALS['memcache']?"��������":"�� ����������"));

// =============================== antibot =========================================
$name='antibot'; $$name='Clean_OLD';

if($PEST[$name]==$$name) {
		include_once $GLOBALS['include_sys']."_antibot.php";
		$cronrez = antibot_del();
		$s .= admin_kletka($name,"<font color=green>$cronrez</font>",$$name);
} else {
        $a=glob($GLOBALS['antibot_file']."*.jpg"); $abot=sizeof($a); unset($a); // ������� ����������� ��������?
        if($abot>5000) $s .= admin_kletka($name,"<font color=red>�������� � ���� ���������� ����������� �����: $abot!</font>",$$name);
	else if($abot) $s .= admin_kletka($name,"����� �������� � ���� $abot",$$name);
}


// =============================== get_cache =========================================
$name='get_cache'; $$name='Clean_cache';
$a=glob($GLOBALS['fileget_tmp']."*"); if($a!==false) {
	$abot=sizeof($a); // ������� ������ � ����?
	if($PEST[$name]==$$name) { foreach($a as $l) unlink($l); }
	if($abot) $s .= admin_kletka($name,"������ � ����: $abot",$$name);
}
unset($a);

?>