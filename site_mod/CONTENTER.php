<?php
/*
������ CONTENTER ������ ��� ������ ���������� �� ������ ���������� �����.
���������: �������� = ��������, ��������� ����������� ��������� ������
1. namespace = -this | -date | %����%
��������� ��������� ����� �� �������� ����� �������� ������. ��������, ���� ���� ��� �������� ololo,
������ ����� �������� ������� �� ������� http://sitename.ru/ololo/
���������� ��� ����������� ��������: -date � -this. ������ ������� ����������� �������,
������ - ������� �� �������� �������. �� ��������� -this.
2. pager = yes | no
����������, ���������� �� ������ ��� �������������� �������, ���� ���������� ������� ������, ��� �������� nskip. �� ��������� yes.
3. nskip = �����
���������� ���������� ������� �� ��������. �� ��������� 10.
4. next = %������%
���������� ������� ��� ������ �� ��������� ��������. �� ��������� <small>&larr;&nbsp;<a href={nextpage}>���������� {n}</a></small>
5. prev = %������%
���������� ������� ��� ������ �� ���������� ��������. �� ��������� <small><a href={prevpage}>��������� {n}</a>&nbsp;&rarr;</small>
6. prevnext = %������%
������� ��� ���������� ��� ������ �� ����������/��������� ��������. �� ��������� <table width=100%><tr><td align=left>{next}</td><td align=right>{prev}</td></tr></table>
7. comment = %������%
������� ��� ������ �� �����������. �� ��������� <div style='text-align: right; font-size:10pt;'><a href={link}#comments>������������ {ncomm}</a></div>
8. template = %������%
���������� ������ ������. �� ��������� <div style='padding:15px;'><div class='header' id='Header_{num}' style='text-align:left'>{D}.{M}.{Y}&nbsp;&#151;&nbsp;<a href='{link}'>{Header}</a>{edit}<br></div>{zamok}<div id='Body_{num}'>{Body}</div>{comment}</div>
*/

include_once $GLOBALS['include_sys']."_onetext.php";

function CONTENTER($e) { global $httphost;

$conf=array_merge(array(
'namespace'=>'-this',
'pager'=>'yes',
'nskip'=>10,
'next'=>"<small>&larr;&nbsp;<a href={nextpage}>���������� {n}</a></small>",
'prev'=>"<small><a href={prevpage}>��������� {n}</a>&nbsp;&rarr;</small>",
'prevnext'=>"<table width=100%><tr><td align=left>{next}</td><td align=right>{prev}</td></tr></table>",
'comment'=>"<div style='text-align: right; font-size:10pt;'><a href={link}#comments>������������ {ncomm}</a></div>",
'template'=>"<div style='padding:15px;'><div class='header' id='Header_{num}' style='text-align:left'>{D}.{M}.{Y}&nbsp;&#151;&nbsp;<a href='{link}'>{Header}</a>{edit}<br></div>{zamok}<div id='Body_{num}'>{Body}</div>{comment}</div>"
),parse_e_conf($e));

$last_skip = intval($conf['nskip']);
$skip=intval($_GET['skip']);

//����� ������ �������� �� ����?
if($conf['namespace']=='-this') { //������ �������� �������
list($path) = explode('?',$GLOBALS['MYPAGE']);
$path = ltrim($path,$httphost);
$path = rtrim($path,'.html../');
$where = "`Date` LIKE '".$path."/%'";
} elseif($conf['namespace']=='-date') $where = "`DateDatetime`!='0'"; //����������� �������
else $where = "`Date` LIKE '".$conf['namespace']."/%'"; //�� ���������� �������

//���� � ������� ��� {Body}, ������ �� ���� ����� ����� �������
if(strstr($conf['template'],'{Body}')) { $select = "*"; $fullmode = 1; }
else $select = "`Date`,`Header`,`Access`,`num`";

$oldarticle=$GLOBALS['article'];
$pp=ms("SELECT ".$select." FROM `dnevnik_zapisi` ".WHERE($where)." ORDER BY `Date` DESC LIMIT ".$skip.",".($last_skip+1),"_a");

//���������� �� �������� �������?
if($conf['pager']=='yes') {
$n=sizeof($pp);
if($n>$last_skip){ unset($pp[$n-1]); $next=mper($conf['next'],array('nextpage'=>$mypage."?skip=".($skip+$last_skip),'n'=>$last_skip)); } else $next='';
$n=$skip-$last_skip;
if($n>=0) { $prev=mper($conf['prev'],array('prevpage'=>$mypage."?skip=".$n,'n'=>$last_skip)); } else $prev='';
$prevnext = mper($conf['prevnext'],array('next'=>$next,'prev'=>$prev));
} else $prevnext = '';
$s=$prevnext;



foreach($pp as $p) {
    $GLOBALS['article']=$p;
    $link=get_link_($p["Date"]); // �������� ������ �� ������
    list($Y,$M,$D) = explode('/', $p['Date'], 3); $D=substr($D,0,2);

if($p['Comment_view']!='off' && strstr($conf['template'],'{comment}')) {
   $idzan=intval(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `DateID`='".e($p["num"])."'",'_l'));
   $comment = mper($conf['comment'],array('link'=>$link,'ncomm'=>$idzan,'num'=>$p['num']));
} else $comment = '';

//������������ �� ����� �������?
if($fullmode) $body = onetext($p); else $body = '';

$s.=mper($conf['template'],array(
'Body'=>$body,
'Header'=>$p["Header"],
'link'=>$link,
'num'=>$p["num"],
'comment'=>$comment,
'Y'=>$Y,'M'=>$M,'D'=>$D,
'zamok'=>zamok($p['Access']),
'edit'=>($GLOBALS['admin']?"<img style='margin: 0 10px 0 10px;' class=knop onClick=\"majax('editor.php',{a:'editform',num:'".$p['num']."',comments:(idd('commpresent')?1:0)})\" src='".$GLOBALS['www_design']."e3/color_line.png' alt='editor'>":'')
));
}

$GLOBALS['article'] = $oldarticle;
return $s.$prevnext;
}

?>