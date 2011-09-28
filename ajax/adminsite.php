<?php // Работа с переменными

include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include $include_sys."_autorize.php"; // сперва JsHttpRequest, затем autorize
if(!$admin) idie('not admin');
$a=$_REQUEST["a"];

//=================================== album ===================================================================
if($a=='load') {
	$sql=ms("SELECT `name` FROM `".$GLOBALS['db_site']."` ORDER BY `name`",'_a',0);

	$o="<div id='adminsitebl'>
<div id='adminsite0'>
<img class='knop' src='".$GLOBALS['www_design']."e3/filenew.png' title='New' onclick=\"majax('adminsite.php',{a:'new'})\">
</div>
";
	foreach($sql as $n=>$p) { $name=h($p['name']); if($name=='') $name="&lt;...&gt;";
		$o.="<div class='l' id=\"as_".$name."\" onclick=\"majax('adminsite.php',{n:'".$name."'})\">".$name."</div>";
	}
$o.="</div>";
	otprav("ohelpc('adminsite','AdminSite',\"".njs($o)."\");");
}
//========================
if($a=='del'){ $name=RE('name');
	msq_del('site',array('name'=>e($name)));
	otprav("clean('as_".h($name)."'); salert('Delete',500);");
}

if($a=='save'){ $name=RE('name');
	msq_add_update('site',array('text'=>e(str_replace("\r",'',RE('text'))),'name'=>e($name)),'name');
	otprav("var d=\"as_".h($name)."\"; if(idd(d)) salert('".LL('saved')."',500); else {
		mkdiv(d,\"".h($name)."\",'l',idd('adminsitebl'),idd('adminsite0'));
		otkryl(d);
		idd(d).onclick=function(){ majax('adminsite.php',{n:'".$name."'}); };
	}");
}

//===================================================
if($a=='new') { otprav("ohelpc('fotoset','new name',\"Name: <input type='text' maxlength='128' id='newnamei' size='80' value='' onchange='newgo()'>"
." <input type='button' value='Edit' onclick='newgo()'>\");
idd('newnamei').focus();
newgo=function(){majax('adminsite.php',{n:idd('newnamei').value})};
"); }

if($a==''&&RE('n')!='') { $name=RE('n');
	$p=ms("SELECT `text` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($name)."'",'_1',0);

otprav("
delit=function(){ if(confirm('Delete?')) { majax('adminsite.php',{a:'del',name:'".h($name)."'}); clean('fotoset'); } };
save_and_close=function(){save_no_close();clean('fotoset')};
save_no_close=function(){ if(idd('edit_text').value==idd('edit_text').defaultValue) return salert('".LL('save_not_need')."',500);
	majax('adminsite.php',{a:'save',name:'".h($name)."',text:idd('edit_text').value});
	idd('edit_text').defaultValue=idd('edit_text').value;
};
ohelpc('fotoset',\"Edit: ".h($name)."\",\"<table><tr><td>"
."<textarea style='width:\"+(getWinW()-100)+\"px;height:\"+(getWinH()-100)+\"px;' id='edit_text'>".h(njsn($p['text']))."</textarea>"
."<br><input title='".LL('ctrl+Enter')."' type='button' value='".LL('Save+exit')."' onclick='save_and_close()'>"
." <input title='".LL('shift+Enter')."' type='button' value='".LL('Save')."' onclick='save_no_close()'>"
." <img class='knop' src='".$GLOBALS['www_design']."e3/remove.png' title='Delete' onclick='delit()'>"
."</td></tr></table></fieldset>\");
idd('edit_text').focus();
setkey('esc','',function(e){ if(idd('edit_text').value==idd('edit_text').defaultValue || confirm('".LL('exit_no_save')."')) clean('fotoset'); },false);
setkey('enter','ctrl',save_and_close,false);
setkey('del','ctrl',delit,false);
setkey('enter','shift',save_no_close,false);
setkey('tab','shift',function(){ti('edit_text','\\t{select}')},false);
");
}

?>