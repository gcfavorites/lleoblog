<?php // hashdata v2

include $GLOBALS['include_sys']."_hashdata2.php";

function hashme($e) { //return $e;
//	if(substr($e,0,1)=='#') 
//return hashflash($e);
	return hashdata($e," u".$GLOBALS['unic']." t".time()." ");
}

//$metka="Vot takoi tekstik ja zakodiroval. IP=10.8.0.100"; // Задать водяную метку
//$text=file_get_contents("santa.html"); // Взять текст
//print hashflash($text); // Показать все символы для замены в этом тексте
//$text2 = hashdata($text,$metka); // Закодировать метку в текст
//print datahash($text2); // Вынуть метку из текста

?>
