<?php

function ADMINPANEL($e) { if(!$GLOBALS['admin']) return '';

return "<div style='position:absolute;left:4px;top:4px;z-index:999;border:1px dashed rgb(255,0,0); padding: 6px; background-color: rgb(255,252,223); font-size:9px'>
<div style='font-size: 9px'>
<FORM METHOD=get ACTION='{httphost}contents/'><INPUT style='font-size: 9px; border: 1px solid #ccc' TYPE='text' NAME='search' SIZE=6 MAXLENGTH=160>
<INPUT style='font-size: 9px' TYPE=SUBMIT VALUE='>'></FORM>

посещений: <a href=\"javascript:majax('statistic.php',{a:'ktoposetil',data:'".$GLOBALS['article']['num']."',mode:'short'})\">{_COUNTER:_}</a>
<br><a href=\"javascript:majax('editor.php',{a:'editform',num:'".$GLOBALS['article']['num']."',comments:(idd('commpresent')?1:0)})\">редактировать</a>
<br><a href=\"{wwwhost}contents\">содержание</a>
<br><a href=\"javascript:majax('statistic.php',{a:'loadstat',data:'".$GLOBALS['article']['num']."'})\">статистика</a>
<br><a href=\"javascript:majax('foto.php',{a:'uploadform',hid:hid,num:'".$GLOBALS['article']['num']."'})\">закачать картинку</a>
</div></div>";

}

?>