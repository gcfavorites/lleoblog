<?php

function installmod_init(){ return "Clean old Tables"; }


function installmod_do() { global $o,$skip,$allwork,$delknopka,$lim,$msqe; $starttime=time(); $lim=100;

	$GLOBALS['starttime']+=600; // 10 мин

	$act=RE('act');

	if(empty($act)) otprav("

iwait=function(){ helpc('wait',\"<div style='padding:50px'>wait...</div>\"); };

oknof1=function(){ iwait();
majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',allwork:0,time:0,skip:0"
.",days:idd('days').value,act:'view'});
};

oknof2=function(){ iwait();
majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',allwork:0,time:0,skip:0"
.",days:vzyal('noday'),act:'delete'});
};

oknof3=function(){ iwait();
majax('module.php',{mod:'INSTALL',a:'do',module:'".RE('module')."',allwork:0,time:0,skip:0"
.",days2:idd('days2').value,act:'clean_posetil'});
};

helpc('okno',\"<fieldset><legend>Clean table `unic` from old anonymous</legend>"
."<table>"
."<tr><td>MySQL `dnevnik_posetil`:</td><td id='npos'>".ms("SELECT COUNT(*) FROM `dnevnik_posetil`","_l",0)."</td></tr>"
."<tr><td>older <input type='text' id='days2' value='365' size='5'> days:</td><td>"
."<input type='button' value='Clean' onclick='oknof3()'></td></tr>"

."<tr><td colspan=2>&nbsp;</td></tr>"
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

//$a=777;
		otprav("
clean('wait');
zabil('nunic','<b>".ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic'],"_l",0)."</b>');
zabil('nodel',\"<b>$a</b> <input type='button' value='DELETE' onclick='oknof2()'> &nbsp; \");
");
	}

	if($act=='delete') {

$a=ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic']." WHERE (`openid`='' AND `password`='')
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_posetil` WHERE `date`>".(time()-86400*$days).")
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_comm`)
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_plusiki`)
","_l",0);

$a1=ms("DELETE FROM ".$GLOBALS['db_unic']." WHERE (`openid`='' AND `password`='')
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_posetil` WHERE `date`>".(time()-86400*$days).")
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_comm`)
AND `id` NOT IN (SELECT `unic` FROM `dnevnik_plusiki`)
","_l",0);

		otprav("
clean('wait');
salert('Deleted: $a',3000);
zabil('nunic','<b>".ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic'],"_l",0)."</b>');
zabil('nodel','');
");
	}


	if($act=='clean_posetil') {
		$days2=c(RE('days2'));
$a=ms("SELECT COUNT(*) FROM `dnevnik_posetil` WHERE `date`<".(time()-86400*$days2),"_l",0);
$a1=ms("DELETE FROM `dnevnik_posetil` WHERE `date`<".(time()-86400*$days2),"_l",0);

		otprav("
clean('wait');
salert('Deleted: $a',3000);
zabil('npos','<b>".ms("SELECT COUNT(*) FROM `dnevnik_posetil`","_l",0)."</b>');
");
	}

}

?>