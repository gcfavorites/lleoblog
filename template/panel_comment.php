<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// панель редактора заметок

//<div style='margin: 1px 0 1px 0;'>

$panel = strtr("
<img class=knop onClick=\"ti('$id','".chr(160)."{select}')\" src=".$www_design."e2/nbsp.gif>
<img class=knop onClick=\"ti('$id','".chr(169)."{select}')\" src=".$www_design."e2/copy.gif>
<img class=knop onClick=\"ti('$id','".chr(151)."{select}')\" src=".$www_design."e2/mdash.gif>
<img class=knop onClick=\"ti('$id','".chr(171)."{select}".chr(187)."')\" src=".$www_design."e2/ltgt.gif>
<img class=knop onClick=\"ti('$id','[b]{select}[/b]')\" src=".$www_design."e2/bold.gif>
<img class=knop onClick=\"ti('$id','[i]{select}[/i]')\" src=".$www_design."e2/italic.gif>
<img class=knop onClick=\"ti('$id','[s]{select}[/s]')\" src=".$www_design."e2/strikethrough.gif>
<img class=knop onClick=\"ti('$id','[u]{select}[/u]')\" src=".$www_design."e2/underline.gif>
<div class=br>".LL('Commentpanel:loadfoto')." <input name='foto' type='file' onchange=\"idd('$id').value=idd('$id').value.replace(/\\n*\[IMG\]\\n*/gi,'')+'[IMG]'\"></div>
<div class=br>".LL('Commentpanel:html')." <input name='html' type='checkbox'></div>

<div class='l br' title=\"".LL('Commentpanel:screen_title')."\" onClick=\"ti('$id','{screen:\\n{select}\\n}')\">".LL('Commentpanel:screen')."</div>
<div class='l br' title=\"".LL('Commentpanel:scr_title')."\" onClick=\"ti('$id','{scr:\\n{select}\\n}')\">".LL('Commentpanel:scr')."</div>

","\n","");
// 
//<div class=br>загрузить фотку: <input name='foto' type='file' onchange=\"ti('".$id."','{select}[IMG]')\"></div>
//<img id='".$id."loadfoto' class=knop onClick=\"majax('comment.php',{a:'loadfoto',id:'".$id."'})\" alt='Загрузить фотку со своего диска<br>прямо на сайт в этот комментарий<br><br>(эта опция пока не работает)' src=".$www_design."e3/foto.png>
// </div>

?>