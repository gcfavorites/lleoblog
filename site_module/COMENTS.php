<?php // Отображение статьи с каментами - дата передана в $Date

function COMENTS($e) { global $article, $podzamok, $load_comments_MS, $enter_comentary_days, $N_maxkomm;

//===================================
// как быть с комментариями?
$premesage="";
$dopload="";

$comments_form=true; // выводить форму приема комментариев
$comments_knopka=false; // выводить кнопку подкачки комментариев
$comments_list=false; // грузить простыню комментариев
$comments_screen=true;

	get_counter($article); // установить значение счетчика, если не было

$comments_timed=(
		$article["counter"] > $N_maxkomm // Превышение количества посещений
		|| $article["DateTime"] < time()-86400*$enter_comentary_days // Слишком старая заметка
		?true:false); 

switch($article["Comment_view"]) { // Comment_view enum('on', 'off', 'rul', 'load', 'timeload')
	case 'on': $comments_knopka=false; $comments_list=true; break;
	case 'off': $comments_knopka=false; $comments_form=false; $comments_list=false; break;
	case 'rul': $comments_knopka=true; $comments_list=true; $load_comments_MS=" AND `rul`='1'"; $dopload=" остальные"; break;
	case 'load': $comments_knopka=true; $comments_list=false; break;
	case 'timeload': $comments_knopka=$comments_timed; $comments_list=!$comments_timed; break;
	}

switch($article["Comment_write"]) { // Comment_write enum('on', 'off', 'friends-only', 'login-only', 'timeoff', 'login-only-timeoff')
	case 'on': $comments_form=true; break;
	case 'off': $comments_form=false; $coments.="<p>Комментарии к этой заметке я отключил, надеюсь на понимание."; break;
	case 'friends-only': $comments_form=$podzamok; if($podzamok) $coments.="<p>К этой заметке оставить комментарий могут только друзья (например, ты)."; break;
	case 'login-only': $comments_form=($login?true:false); $coments.="<p>К этой заметке оставить коментарий могут только залогиненные.
Залогиниться можно <a href=".$wwwhost."login/?retpage=".urlencode($wwwhost.$Date.".html").">здесь</a>."; break;
	case 'timeoff': $comments_form=!$comments_timed; if(!$comments_form) $coments.="<p>Комментарии к этой заметке автоматически отключились, потому что прошло больше ".$enter_comentary_days." дней или число посещений превысило ".$N_maxkomm.". Но если что-то важное, вы всегда можете написать мне письмо: <a href=mailto:lleo@aha.ru>lleo@aha.ru</a>"; break;
	case 'login-only-timeoff': $comments_form=($login?!$comments_timed:false); if(!$comments_form) $coments.="<p>Комментарии к этой заметке были поначалу разрешены только залогиненным, но автоматически отключились и они, потому что прошло больше ".$enter_comentary_days." дней или число посещений превысило ".$N_maxkomm.". Но если что-то важное, вы всегда можете написать мне письмо: <a href=mailto:lleo@aha.ru>lleo@aha.ru</a>"; break;
	}

switch($article["Comment_screen"]) { // Comment_screen  enum('open', 'screen', 'friends-open')
	case 'open': $comments_screen=false; break;
	case 'screen': $comments_screen=true; if($comments_form) $coments.="<p>Комментарии к этой заметке скрываются - они будут видны только вам и мне."; break;
	case 'friends-open': $comments_screen=!$podzamok; if($comments_form && $podzamok) $coments.="<p>Комментарии к этой заметке скрываются, но у друзей (у тебя) они будут открыты."; break;
	}

if(strstr($_SERVER["HTTP_USER_AGENT"],'Yandex') || $GLOBALS['IP']=='78.110.50.100') { // роботу Яндекса
	$premesage.=''; $coments='';
	$comments_form=false; // принимать комментарии - не надо (зачем Яндексу оставлять комментарии?)
	$comments_knopka=false; // простыню комментариев - выдавать с заметкой (Яндекс не умеет нажимать кнопку, а хотел бы индексировать)
	$comments_list=true;
	}

//===================================

$s='';

if(
// $GLOBALS['admin'] or 
$comments_form) { // РАЗРЕШЕНО ОСТАВИТЬ КОММЕНТАРИЙ

/*
if($GLOBALS['admin']) {

//	include_once $include_sys."_antibot.php"; // антибота подгружаем

$s .= "<div id='oldcomform'><b>Оставить комментарий:</b>
<form method=post action='".$GLOBALS['wwwhost']."ajax/comment_loh_noJS.php'>
<input type=hidden name=num value=".$article['num'].">
<input type=hidden name=Date value=".$article['Date'].">
<table>
<tr><td>Имя:</td><td><input class=t type=text name='name' size=40 maxlength=128 value='".h($IS['USER'])."'></td></tr>
<tr><td>Mail для ответа (скрыт):</td><td><input class=t type=text name='mail' size=40 maxlength=128 value='".h($IS['MAIL'])."'></td></tr>
<table><tr><td colspan=2>
<textarea class=t onFocus='document.onkeydown='return true';' onBlur='document.onkeydown=NavigateThrough;' name='text' cols=60 rows=7>".h($_POST["text"])."</textarea>
</td></tr>
</table><br><input class=t type=submit value='Отправить'>
</form></div><script>document.getElementById('oldcomform').style.display='none';</script>";

}
*/

$s.= "<div id='commpresent' class=l style='font-weight: bold; margin: 20pt; font-size: 16px;' onclick=\"majax('comment.php',{a:'comform',id:0,lev:0,comnu:comnum,dat:".$article['num']."});\">Оставить комментарий</div>";

if ( $article["DateTime"] > time() ) $s .= "<blockquote style='border: 3px dotted rgb(255,0,0); padding: 2px;'><font size=2>Заметка датирована будущим числом, и это просто значит, что прошлые дни заняты, а материал хотелось разместить.</font></blockquote>";


}

// блять, надо чтоб число комментариев сидело в самой заметке, это ж ебануться их считать всякий раз!
$idzan = intval(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `DateID`='".e($article["num"])."'".($podzamok?'':" AND `scr`='0'"), '_l'));

if($idzan) { // если вообще есть комментарии

if($comments_list) { // грузить простыню изначально
	$template=$e; include_once $GLOBALS['include_sys']."_onecomm.php";
	$pro = load_comments($article); //,$article["comments_order"]);
} elseif($comments_knopka) // подгружать по кнопке
	$pro = "<input TYPE='BUTTON' VALUE=' читать комментарии".$dopload." (".($podzamok?"всего":"открытых")." ".$idzan." шт) ' onClick=\"majax('comment.php',{a:'loadcomments',dat:".$article['num']."})\">";
}

return $s."<div id=0>$pro<div></div></div>";

}

?>
