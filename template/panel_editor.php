<?php // панель редактора заметок

/*
$inc=glob($filehost."site_mod/*.php");
$ainc=array(); $ainc['']='- модули -'; foreach($inc as $l) {

$s=file_get_contents($l);
$l=preg_replace("/^.*?\/([^\/]+)\.php$/si","$1",$l); 
if(!preg_match("/\/\*(.*?)\*\//si",$s,$m)) $l="--".$l;

$ainc[$l]=$l; }
*/

$panel = strtr("

<img class=knop onClick=\"majax('foto.php',{a:'album'})\" src=".$www_design."e3/foto.png>
<img class=knop onclick=\"majax('foto.php',{a:'lostfoto',idhelp:'".$idhelp."'})\" src='".$www_design."e3/ledlightblue.png' alt='Find lost foto'>
&nbsp;&nbsp;
<img class=knop onclick=\"majax('editor.php',{a:'loadhelp'})\" src='".$www_design."e3/gnome-help.png' alt='Help!'>
&nbsp;&nbsp;
<img class=knop onClick=\"ti('$id','<p class=pd>{select}')\" src=".$www_design."e2/pd.gif>
<img class=knop onClick=\"ti('$id','<p class=d>{select}')\" src=".$www_design."e2/d.gif>
<img class=knop onClick=\"ti('$id','".chr(160)."{select}')\" src=".$www_design."e2/nbsp.gif>
<img class=knop onClick=\"ti('$id','".chr(169)."{select}')\" src=".$www_design."e2/copy.gif>
<img class=knop onClick=\"ti('$id','".chr(151)."{select}')\" src=".$www_design."e2/mdash.gif>
<img class=knop onClick=\"ti('$id','".chr(171)."{select}".chr(187)."')\" src=".$www_design."e2/ltgt.gif>
<img class=knop onClick=\"ti('$id','<b>{select}</b>')\" src=".$www_design."e2/bold.gif>
<img class=knop onClick=\"ti('$id','<i>{select}</i>')\" src=".$www_design."e2/italic.gif>
<img class=knop onClick=\"ti('$id','<s>{select}</s>')\" src=".$www_design."e2/strikethrough.gif>
<img class=knop onClick=\"ti('$id','<u>{select}</u>')\" src=".$www_design."e2/underline.gif>
<img class=knop onClick=\"ti('$id','<center>{select}</center>')\" src=".$www_design."e2/justifycenter.gif>
<img class=knop onClick=\"ti('$id','<img src=".$www_design."e2/lj.gif style=\'vertical-align: middle;\'><a href=http://{select}.livejournal.com>{select}</a>')\" src=".$www_design."e2/ljuser.gif>
<img class=knop onClick=\"ti('$id','<center><img src={select} border=1></center>')\" src=".$www_design."e2/image.gif>
<img class=knop onClick=\"ti('$id','<a href=\'{select}\'>{select}</a>')\" src=".$www_design."e2/link.gif>
<img class=knop onClick=\"ti('$id','<center><object width=320 height=240><param name=movie value=\'{select}.swf\'></param><param name=wmode value=transparent></param><embed src=\'{select}.swf\' type=\'application/x-shockwave-flash\' wmode=transparent width=320 height=240></embed></object></center>')\" src=".$www_design."e2/ljvideo.gif>
<img class=knop onClick=\"ti('$id','<blockquote style=\'border: 1px dashed rgb(255,0,0); padding: 20px; margin-left: 50px; margin-right: 50px; background-color: rgb(255,252,223);\'>{select}</blockquote>')\" src=".$www_design."e2/tableb_1.gif>
<img class=knop onClick=\"ti('$id','<table style=\'border-collapse: collapse; border: 1px solid red; margin: 20pt;\'\\nbgcolor=#fffff0 border=1 cellpadding=20><td><div align=justify>{select}</td></table>')\" src=".$www_design."e2/tableb_r.gif>
<img class=knop onClick=\"ti('$id','<table bgcolor=#fff0ff border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>{select}</td></table>')\" src=".$www_design."e2/tableb1.gif>
<img class=knop onClick=\"ti('$id','<table bgcolor=#f0ffff border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>{select}</td></table>')\" src=".$www_design."e2/tableb2.gif>
<img class=knop onClick=\"ti('$id','<table bgcolor=#fffff0 border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>{select}</td></table>')\" src=".$www_design."e2/tableb3.gif>
<img class=knop onClick=\"ti('$id','<pre style=\'border: 0.01mm solid rgb(0,0,0); padding: 4px; line-height: 100%; font-family: monospace; background-color: rgb(255,255,255);\'>{select}</pre>')\" src=".$www_design."e2/tableb_pre.gif>

","\n","");

// <img class=knop onClick=\"majax('editor.php',{a:'help',mod:idd('editor_mod').value})\" src=".$www_design."e3/gnome-help.png>
// <div>".selecto('editor_mod','',$ainc,"id")."</div>


?>