<?php /* ������������

{_LLEOP: [R,F] [+]2010 arhive/text.html ������� ��� ���� _}
*/

STYLES("
.lltR,.lltF { font-size: 16px;}
.lltR0,.lltF0 { font-size: 12px;}
.llR,.llR0 { color: red; }
.llF,.llF0 { color: #800080; }
.llLN { text-decoration: none; }
.llLN:hover { text-decoration: underline; }
");

//border: 1px dotted transparent; border: 1px dotted gray; 
// $GLOBALS['llcolors']=array('R'=>'red','F'=>'#800080');

function LLEOPS($e) {
	$conf=array_merge(array(
		'style'=>"R",
		'new'=>"<img src='/new.gif'>&nbsp;",
		'template'=>"<div class='llt{style}'><span class='ll{style}'>{year}</span>&nbsp;{new}{link}{text}{linka}</div>"
	),parse_e_conf($e));
	$s=''; foreach(explode("\n",$conf['body']) as $p) { list($conf['year'],$lnk,$conf['text'])=explode(' ',$p,3);
		if(substr($conf['year'],0,1)=='+') { $conf['year']=substr($conf['year'],1); $n=$conf['new']; } else $n='';

		if($lnk=='.') { $conf['link']=$conf['linka']=''; }
		else { $conf['link']="<a class='llLN' href='$lnk'>"; $conf['linka']="</a>"; }

		$s.=mper($conf['template'],array_merge($conf,array('new'=>$n)));
	}
	return $s;
}
?>