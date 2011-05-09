<?php // Отображение фотки

function FOTOM($e) {
	list($y,$m,)=explode('/',$GLOBALS['article']['Date'],3);
	if(!preg_match("/\.(jpg|jpeg|gif|png)$/si",$e)) $e.='.jpg';
	if(!strstr($e,'/')) $e=$GLOBALS['wwwhost'].$y.'/'.$m.'/'.$e;
	return "<center><img src='".h($e)."' border=1></center>";
}

?>
