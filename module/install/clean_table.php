<?php

function installmod_init(){ return "Clean old Tables"; }
function installmod_do() { global $o,$skip,$allwork,$delknopka,$lim,$msqe; $starttime=time(); $lim=100;

	$act=RE('act');

	if(empty($act)) otprav("
oknof1=function(){
	helpc('wait',\"<div style='padding:50px'>wait...</div>\");
	majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',allwork:0,time:0,skip:0"
.",days:idd('days').value"
.",act:'view'"
."});
};

oknof2=function(){
	helpc('wait',\"<div style='padding:50px'>wait...</div>\");
	majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',allwork:0,time:0,skip:0"
.",days:vzyal('noday')"
.",act:'delete'"
."});
};


helpc('okno',\"<fieldset><legend>Clean table `unic` from old anonymous</legend>"
."<table>"
."<tr><td>MySQL `dnevnik_posetil`:</td><td>".ms("SELECT COUNT(*) FROM `dnevnik_posetil`","_l",0)."</td></tr>"
."<tr><td>MySQL `dnevnik_comm`:</td><td>".ms("SELECT COUNT(*) FROM `dnevnik_comm`","_l",0)."</td></tr>"
."<tr><td>MySQL `dnevnik_plusiki`:</td><td>".ms("SELECT COUNT(*) FROM `dnevnik_plusiki`","_l",0)."</td></tr>"
."<tr><td>MySQL ".$GLOBALS['db_unic'].":</td><td id='nunic'>".ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic'],"_l",0)."</td></tr>"
."<tr><td>anonymous older <input type='text' id='days' value='365' size='5'> days:</td><td>"
."<span id='nodel'></span>"
."<input type='button' value='View' onclick='oknof1()'></td></tr>"
."</table></fieldset>\");");

//<b><span id='noday'></span></b>
	// далее аякс

	$days=c(RE('days'));

	if($act=='view') {

$a=ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic']." WHERE (`openid`='' AND `password`='')
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_posetil` WHERE `date`>".(time()-86400*$days).")
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_comm`)
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_plusiki`)
","_l",0);
		otprav("
clean('wait');
zabil('nunic','<b>".ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic'],"_l",0)."</b>');
zabil('nodel',\"<b>$a</b> <input type='button' value='DELETE' onclick='oknof2()'> &nbsp; \");
");
	}

	if($act=='delete') {

$a=ms("DELETE FROM ".$GLOBALS['db_unic']." WHERE (`openid`='' AND `password`='')
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_posetil` WHERE `date`>".(time()-86400*$days).")
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_comm`)
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_plusiki`)
","_l",0);

		otprav("
clean('wait');
zabil('nunic','<b>".ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic'],"_l",0)."</b>');
zabil('nodel','');
");

	}

}
?>