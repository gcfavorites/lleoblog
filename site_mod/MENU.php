<?php // Выпадающее меню
if(!isset($GLOBALS['admin_name'])) die("Error 404"); // неправильно запрошенный скрипт - нахуй

function MENU($e) { $a=explode("\n",$e);

	$pos=0;
	$m=array(0=> rtrim( $GLOBALS['wwwhost'] ,'/') );

	foreach($a as $l) {
		$l=trim($l," \r\n\"\'"); if($l!='') {
		$l1=ltrim($l,"-"); $n=strlen($l)-strlen($l1);
		list($l1,$link)=explode('|',$l1); $l1=trim($l1," \r\n\"\'");
			if($n==$pos) { $do=$dodo; }
			elseif($n<$pos) { 
			$do=$dodo; for($i=$pos-$n;$i>0;$i--) { unset($m[$pos--]); $do.=MENU_pro($pos+1)."</ul>".MENU_pro($pos)."</li>"; }
			} else { $do=''; for($i=$n-$pos;$i>0;$i--) { $m[++$pos]=translit($l1last); $do.=MENU_pro($pos)."<ul>"; } }
		if($link=='') $link=implode("/",$m).'/'.translit($l1);
		$o.=$do.MENU_pro($pos)."<li><a href='".$link."'>".$l1."</a>";
				$l1last=$l1;
		$dodo="</li>";
	}}

	$o.=$dodo; unset($m[$pos--]);
	foreach($m as $pos=>$l) $o.=MENU_pro($pos+1)."</ul>".MENU_pro($pos)."</li>";
	return "\n\n<!-- выпадающее меню -->\n<ul id='nav'>".$o."\n</ul>\n<!-- /выпадающее меню -->\n\n";
}
function MENU_pro($l) { return "\n".str_repeat("\t",$l); }

?>
