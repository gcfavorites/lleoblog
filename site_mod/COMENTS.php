<?php // Отображение статьи с каментами - дата передана в $Date

/*
comm:disabled_login     <p>Комментарии к этой заметке были поначалу разрешены только залогиненным, но автоматически отключили
comm:screen             <p>Комментарии к этой заметке скрываются - они будут видны только вам и мне.
comm:screen_nofriend    <p>Комментарии к этой заметке скрываются, но у друзей (у тебя) они будут открыты.
comm:comment_this       <div id='commpresent' class='l' style='font-weight: bold; margin: 20pt; font-size: 16px;' {majax}>Ост
comm:future             <blockquote style='border: 3px dotted rgb(255,0,0); padding: 2px;'><font size=2>Заметка датирована бу

comm:page               <div style='margin: 50px;'>{0}</div>
comm:button             <input TYPE='BUTTON' VALUE=' читать комментарии{dopload} ({podzamok?всего|открытых} {idzan} шт) ' {ma

comm:s                  <div class=r style='margin: 50px;'>{s}</div>
comm:pro                <div id=0>{pro}<div></div></div>

comm:nocomments         <p class=z>комментариев нет или они все скрыты
comm:itogo              <center><p class=br>всего комментариев: {nmas}</p>{u?<p>показаны только открытые комментарии - <span


*/
function COMENTS($e) { global $article, $podzamok, $load_comments_MS, $enter_comentary_days, $N_maxkomm, $idzan;

//===================================
// как быть с комментариями?
$s='';

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
	case 'rul': $comments_knopka=true; $comments_list=true; $load_comments_MS=" AND `rul`='1'";
$dopload=LL('comm:ostalnye'); // " остальные";
break;
	case 'load': $comments_knopka=true; $comments_list=false; break;
	case 'timeload': $comments_knopka=$comments_timed; $comments_list=!$comments_timed; break;
	}

switch($article["Comment_write"]) { // Comment_write enum('on', 'off', 'friends-only', 'login-only', 'timeoff', 'login-only-timeoff')
	case 'on': $comments_form=true; break;
	case 'off': $comments_form=false;
$s.=LL('comm:off'); // "Комментарии отключены";
break;
	case 'friends-only': $comments_form=$podzamok; if($podzamok)
$s.=LL('comm:friends_only'); // "оставить комментарий могут друзья";
break;
	case 'login-only': $comments_form=((($GLOBALS['IS']['login']!=''&&$GLOBALS['IS']['password']!='')||$GLOBALS['IS']['openid']!='')?true:false);
$s.=LL('comm:login_only',array('majax'=>"onclick=\"ifhelpc('".$GLOBALS['httphost']."login','logz','Login')\""));
// "<p>К этой заметке оставить коментарий могут только залогиненные. Залогиниться можно здесь";
break;
	case 'timeoff': $comments_form=!$comments_timed; if(!$comments_form)
$s.=LL('comm:disabled',array('1'=>$enter_comentary_days,'2'=>$N_maxkomm,'mail'=>$GLOBALS['admin_mail']));
// "Комментарии отключились, потому что больше ".$enter_comentary_days." дней или посещений ".$N_maxkomm.". можете написать mailto";
break;
	case 'login-only-timeoff': $comments_form=($login?!$comments_timed:false); if(!$comments_form)
$s.=LL('comm:disabled_login',array('1'=>$enter_comentary_days,'2'=>$N_maxkomm,'mail'=>$GLOBALS['admin_mail']));
// "Комментарии были разрешены залогиненным, но отключились и они
break;
	}

switch($article["Comment_screen"]) { // Comment_screen  enum('open', 'screen', 'friends-open')
	case 'open': $comments_screen=false; break;
	case 'screen': $comments_screen=true; if($comments_form)
$s.=LL('comm:screen'); // "будут видны только вам и мне";
break;
	case 'friends-open': $comments_screen=!$podzamok; if($comments_form && $podzamok)
$s.=LL('comm:screen_nofriend'); // "у друзей (у тебя) они будут открыты.
break;
	}

if(strstr($_SERVER["HTTP_USER_AGENT"],'Yandex') || $GLOBALS['IP']=='78.110.50.100') { // роботу Яндекса
	$comments_form=false; // принимать комментарии - не надо (зачем Яндексу оставлять комментарии?)
	$comments_knopka=false; // простыню комментариев - выдавать с заметкой (Яндекс не умеет нажимать кнопку, а хотел бы индексировать)
	$comments_list=true;
	}

//===================================

if($comments_form) { // РАЗРЕШЕНО ОСТАВИТЬ КОММЕНТАРИЙ
	$s.= LL('comm:comment_this',array('majax'=>"onclick=\"majax('comment.php',{a:'comform',id:0,lev:0,comnu:comnum,dat:".$article['num']."});\""));
// Оставить комментарий
	if ( $article["DateTime"] > time() ) $s.=LL('comm:future'); // Заметка датирована будущим числом
}

	$idzan=get_idzan($article['num']);
//	$idzan1=get_idzan1($article['num']);

if($idzan) { // если вообще есть комментарии
	if($comments_list) { // грузить простыню изначально
		$template=$e; include_once $GLOBALS['include_sys']."_onecomm.php";
		$pro = load_comments($article);
		SCRIPTS("page_onstart.push(\"var c=gethash_c(); if(c){ if(idd(c)){kl(idd(c))} else majax('comment.php',{a:'loadpage_with_id',page:0,id:c,dat:".$article['num']."})}\")");
	} elseif($comments_knopka) { // подгружать по кнопке
		$pro=LL('comm:page', get_comm_button($article['num'],$dopload,$comments_knopka) );
		SCRIPTS("page_onstart.push(\"var c=gethash_c(); if(c) majax('comment.php',{a:'loadpage_with_id',page:0,id:c,dat:".$article['num']."})\");");
	}
} // {a:'loadcomments',dat:'".$article['num']."'}

return ($s!=''?LL('comm:s',$s):'').LL('comm:pro',$pro);

}
?>