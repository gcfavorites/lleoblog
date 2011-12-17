<?php

$GLOBALS['LAST_started']=0;

function LAST($e) {

	$oldarticle=$GLOBALS['article'];

	if($GLOBALS['LAST_started']++) return "{_CENTER:<b>LAST<b>_}";

$conf=array_merge(array(
'redirect'=>false,
'nskip'=>5,
'next'=>"<small><a href={nextpage}>&lt;&lt;&nbsp;предыдущие {n}</a></small>",
'prev'=>"<small><a href={prevpage}>следующие {n}</a>&nbsp;&gt;&gt;</small>",
'prevnext'=>"<table width=100%><tr><td align=left>{next}</td><td align=right>{prev}</td></tr></table><p>",
// 'comment'=>"<p align=right><a style='font-size:10pt;' href={link}#comments>Добавить комментарий</a> <small>(сейчас {ncomm} шт)</small></p>",
'comment'=>"<div style='text-align: right; font-size:10pt; margin-right: 5px'><a href={link}#comments>комментариев {ncomm}</a> | <a href=\"javascript:majax('comment.php',{a:'comform',id:0,lev:0,comnu:comnum,dat:{num}});\">оставить комментарий</a></div>",
'template'=>"<div style='text-align:justify;padding:0 15px;'><div class='header' id='Header_{num}' style='text-align:left'>{edit}<a href='{link}'>{Y}-{M}-{D}: {Header}</a></div><div id='Body_{num}'>{Body}</div>{comment}</div><hr width=100% color=green>"
),parse_e_conf($e));

if($conf['redirect']!==false) {

// <br><a href="javascript:majax('editor.php',{a:'editform',num:'296',comments:(idd('commpresent')?1:0)})">редактировать</a> <a href="/blog/editor?Date=2011/04/21">#</a>

if(empty($conf['redirect'])) { // если не указан тэг
	$Date=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0")." ORDER BY `Date` DESC LIMIT 1","_l");
} else {
/*
	$Date=ms("SELECT d.`Date` FROM `dnevnik_tags` AS t JOIN `dnevnik_zapisi` AS d ON t.`num`=d.`num`
".WHERE("`DateDatetime`!=0 AND t.`tag`='".e($conf['redirect'])."'")." ORDER BY `Date` DESC LIMIT 1","_l");
*/
	$Date=ms("SELECT d.`Date` FROM `dnevnik_zapisi` AS d
INNER JOIN `dnevnik_tags` AS t
ON t.`num`=d.`num` AND t.`tag`='".e($conf['redirect'])."'
".WHERE("d.`DateDatetime`!=0")." ORDER BY d.`Date` DESC LIMIT 1","_l");

}

	if(!empty($GLOBALS['msqe'])) die($GLOBALS['msqe']);

	if($GLOBALS['article']['Date']==$Date) return "<font color=red> error: last-redirect </font>"; // защита от саморедиректа
	redirect($GLOBALS['httphost'].$Date.".html".($GLOBALS['admin']?"?redir=".$GLOBALS['article']['Date']:''),302); // на последнюю

// select d.date from d inner join t on t.num=d.num and t.tag='движок ' order by d.date desc limit 1

return '###'.$Date.' - '.$conf['redirect']." # ".$GLOBALS['msqe'];

/*

      if($last=='') {
      if(!msq_table('site') and !msq_table('dnevnik_zapisi')) redirect($httphost."admin",302); // в админку, если по первому
      redirect($httphost."editor",302); // в редактор, если записей нет
      } 
*/

return '###'.$conf['redirect'];

}


include_once $GLOBALS['include_sys']."_onetext.php";

$LAST_skip = intval($conf['nskip']); // 5;

$skip=intval($_GET['skip']);
$pp=ms("SELECT `opt`,`Date`,`Body`,`Header`,`DateUpdate`,`Access`,`num` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!='0'")." ORDER BY `Date` DESC LIMIT ".$skip.",".($LAST_skip+1),"_a");

$n=sizeof($pp);
if($n>$LAST_skip){ unset($pp[$n-1]); $next=mper($conf['next'],array('nextpage'=>$mypage."?skip=".($skip+$LAST_skip).$catpn,'n'=>$LAST_skip)); } else $next='';
$n=$skip-$LAST_skip;
if($n>=0) { $prev=mper($conf['prev'],array('prevpage'=>$mypage."?skip=".$n.$catpn,'n'=>$LAST_skip)); } else $prev='';

// idie($next.$prev."-".sizeof($pp));

$s=$prevnext=mper($conf['prevnext'],array('next'=>$next,'prev'=>$prev));

foreach($pp as $p) { $p=mkzopt($p);
	$GLOBALS['article']=$p;
	$link=get_link_($p["Date"]); // неполная ссылка на статью
	list($Y,$M,$D) = explode('/', $p['Date'], 3); $article["Day"]=substr($article["Day"],0,2);

if($p['Comment_view']!='off' && strstr($conf['template'],'{comment}')) {
   $idzan=intval(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `DateID`='".e($p["num"])."'",'_l'));
   $comment = mper($conf['comment'],array('link'=>$link,'ncomm'=>$idzan,'num'=>$p['num']));
} else $comment = '';



$s.=mper($conf['template'],array(
'Body'=>preg_replace("/(<img[^>]+src\=[\'\"]*)([^\/\:]{4,})/si","$1".$GLOBALS['wwwhost'].$Y."/".$M."/$2",onetext($p)), // обработать текст заметки как положено
'Header'=>$p["Header"],
'link'=>$link,
'num'=>$p["num"],
'comment'=>$comment,
'Y'=>$Y,'M'=>$M,'D'=>$D,
'edit'=>($GLOBALS['admin']?"<img style='margin: 0 10px 0 10px;' class=knop onClick=\"majax('editor.php',{a:'editform',num:'".$p['num']."',comments:(idd('commpresent')?1:0)})\" src='".$GLOBALS['www_design']."e3/color_line.png' alt='editor'>":'')
));

}

$GLOBALS['article'] = $oldarticle;

return $s.$prevnext;
}
?> 
