<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// Перекачать старую базу комментариев

$action='restore hashlogin';

$o=file_get_contents('config.php');
if(preg_match("/\n\s*".'\$'."hashlogin\s*=\s*[\"\']+(.*?)[\'\"]+/si",$o,$m)) $h1=$m[1];
if(preg_match("/\n\s*".'\$'."newhash_user\s*=\s*[\"\'](.*?)[\'\"]/si",$o,$m)) $h2=$m[1];
if(isset($h1)&&isset($h2)&&preg_match("/^[0-9a-f]{64}$/si",$h2)) {

if($PEST['action']==$action) { $admin_upgrade=true;
//==========================================================================================
	$s.="копируем вместо сгенерированного '$h2'<br>старое значение '$h1'";
	$o=preg_replace("/(\n\s*".'\$'."newhash_user\s*=\s*[\"\']+).*?([\'\"]+)/si","$1$h1$2",$o);
	file_put_contents('config.php',$o); chmod('config.php',0666);
//==========================================================================================	
} else $s .= admin_kletka('action',"исправить hashlogin",$action);

}

?>