<?php
function SCRIPT($s) { list($n,$s)=explode(':',$s,2); $GLOBALS['_SCRIPT'][c($n)]=addm(c($s)); return ''; }
function STYLE($s) { list($n,$s)=explode(':',$s,2); $GLOBALS['_STYLE'][c($n)]=addm(c($s)); return ''; }
function addm($e) { return (strstr($e,"\n")?$e:ms("SELECT `text` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($e)."'","_l")); }

function SCRIPTS($s,$l=0) { if(!$l) $GLOBALS['_SCRIPT'][]=$s; else $GLOBALS['_SCRIPT'][$s]=$l; }
function STYLES($s,$l=0) { if(!$l) $GLOBALS['_STYLE'][]=$s; else $GLOBALS['_STYLE'][$s]=$l; }
function SCRIPT_ADD($s) { $GLOBALS['_SCRIPT_ADD'][$s]=$s; }
function STYLE_ADD($e) { 
	$e="link href='".str_replace('{www_css}',$GLOBALS['www_css'],h($e))."' rel='stylesheet' type='text/css' charset='".$GLOBALS['wwwcharset']."'";
	$GLOBALS['_HEADD'][$e]=$e;
}

//if(isset($_GET['module'])) { // обращение к модулям через GET
//	$a=array("onload:'yes'"); foreach($_GET as $n=>$l) { if($n!='module') $a[]="'$n':'$l'"; }
//	SCRIPTS("var page_onstart=[\"majax('".h($_GET['module']).".php',{".implode(',',$a)."})\"]");
//}

function mpr($s,$ara) { $s=$ara[$s]; foreach($ara as $n=>$l) $s=str_replace('{'.$n.'}',$l,$s); return $s; }

function modul_n($t) { global $arap; if(isset($arap[$t[1]])) return $arap[$t[1]]; return $t[0]; }
function modul_hn($t) { global $arap; if(isset($arap[$t[1]])) return h($arap[$t[1]]); return $t[0]; }
function modul_s($t) { global $arap; $n=$t[1]; $s=$t[3];
	if($t[2]=='?') {
		list($n,$a,$b)=explode(strstr($s,"\n")?"\n":'|',$s,3);
		return trim(($arap[$n]?$a:$b),"\n\r\t ");
	} elseif($t[2]==':') {
		foreach(explode((strstr($s,"\n")?"\n":'|'),$s) as $l) {
			if(!strstr($l,':')) continue;
			list($x,$c)=explode(':',$l,2);
			if(c($x)==$arap[$n]) break;
	        } return trim($c,"\n\r\t ");
	} elseif($t[2]=='@'&&$n=='LL') {
		if(!strstr($s,'|')) return LL($s);
		$a=explode('|',$s); $e=$a[0]; unset($a[0]); return LL($e,$a);
	}
}
function mper($s,$ara) { global $arap; $arap=$ara;
	$s_old=''; $stop=1000; while($s!=$s_old && --$stop) { $s_old=$s;
                $s=preg_replace_callback("/\{([0-9a-z_\-]+)\}/si","modul_n",$s); // {name}
                $s=preg_replace_callback("/\{#([0-9a-z_\-]+)\}/si","modul_hn",$s); // h({name})
		$s=str_replace(array("{?","?}"),array("\001","\002"),$s);            
		$s=preg_replace_callback("/\001([0-9a-z_\-]+)([\?\:])([^\001\002]+)\002/s","modul_s",$s);
	}
        return $s;
}

/*
function mpars($t){ global $araper;
        foreach(explode("\n",$t[2]) as $e) {
                if(!strstr($e,':')) continue;
                list($x,$c)=explode(':',$e,2);
                if(trim($x,"\n\r\t ")==$araper[$t[1]]) break;
        }
        return trim($c,"\n\r\t");
}
*/

function parse_e_conf($e) { $a=array('body'=>''); $dalee=0; $p=explode("\n",$e);
	foreach($p as $l) {
		if($dalee) { $a['body'].=$l."\n"; continue; }
		if(c($l)=='' or !strstr($l,'=')) { $dalee=1; $a['body'].=$l."\n"; continue; }
		list($n,$v)=explode('=',$l,2); $n=c($n);
		if($n=='' or $n!=strtr($n,' <>-"\'','xxxxxx')) { $dalee=1; $a['body'].=$l."\n"; continue; }
		$a[$n]=c($v);
	} return $a;
}

// ==============================================================================================
// повызывать все процедуры в цикле

$GLOBALS['mainmod']=array('ADMINSET','ADMINPANEL','PRAVKA','PREVNEXT','TITLE','STATISTIC','TEXT','OEMBED','COUNTER','UNIC','ANOTHER_DATE','HEAD','HEAD_D','HEAD_N','HEAD_TXT','MAY9','HEADERS');
$GLOBALS['norepeat_modules']=array('CONTENTER','ADMIN','PRAVKA','UNIC','LAST','FIDO','INSTALL');
$GLOBALS['repeat_modules']=array();
/*
function modules($s) { // $o='';
        $s_old=''; $stop=1000; while($s!=$s_old && --$stop) {
//                $s=str_replace("{_","\001",str_replace("_}","\002",$s));
                $s_old=$s;
//                $s=preg_replace_callback("/\001([^\001\002]+)\002/s","module",$s);
                $s=preg_replace_callback("/\{_(?!\{_)(.*?)_\}/s",'module',$s);
//		$o.='<hr>'.h($s);
// $o=preg_replace("/\{#(?!.*?\{#)(.*?)#\}/si",'TTT',$o);
//              $s=preg_replace_callback("/\{_(.+?)_\}/s","module",$s);
        }
       return $s;
//        return str_replace(array("\001","\002"),'############################',$s);
}
*/

function parss($s,$r1='{_',$r2='_}'){ $l1=strlen($r1); $l2=strlen($r2); $d=0; $stop=100; while($stop--) {
        if(($i=strpos($s,$r1))===false || ($j=strpos($s,$r2))===false) return false;
        if(($k=strpos(substr($s,$l1),$r1))===false || $j<$k) return array($d+$i,$j+$l2);
        $s=substr($s,$k+$l1); $d+=$k+$l1;
} return false;
}



function modules($s) {
        $s_old=''; $stop=500; while($s!=$s_old && --$stop) {
                $s=str_replace("{_","\001",str_replace("_}","\002",$s));
                $s_old=$s;
                $s=preg_replace_callback("/\001([^\001\002]+)\002/s","module",$s);
        }
        return $s;
}


function module($t) { $s=$t[1]; // подцепить модули
        if(strstr($s,':')) { // подключаемый модуль
                list($mod,$arg)=explode(':',$s,2); $mod=rpath(c($mod));
	if(in_array($mod,$GLOBALS['norepeat_modules'])) { // некоторым модулям запрещено повторяться больше 1 раза
	    if($GLOBALS['norepeat_modules'][$mod]==1) return "<font color=red>MODULE \"$mod\" can not repeat!</font>";
	    $GLOBALS['norepeat_modules'][$mod]=1;
	}

                if(!function_exists($mod) && !in_array($mod,$GLOBALS['mainmod'])) {
                        $modfile=$GLOBALS['site_mod'].$mod.".php";
                        $modfile2=$GLOBALS['site_module'].$mod.".php";

                        if(file_exists($modfile)) include_once($modfile);
			elseif(file_exists($modfile2)) include_once($modfile2);
			else return "<font color=red>MODULE NOT FOUND: <b>".h($t[1])."</b></font>";
		
                        if(!function_exists($mod)) idie("Func not found: ".h($mod)
.($GLOBALS['admin']&&isset($GLOBALS['Date'])?"
<p><a href=".$GLOBALS['httphost']."editor/?Date=".$GLOBALS['Date'].">редактировать</a>
<p><div class=l onclick=\"majax('editor.php',{a:'editform',num:'".$GLOBALS['article']['num']."'})\">редактировать в окне</div>
":'')
);
                }
                return call_user_func($mod,c0($arg));
        }

        // иначе - просто вынуть из базы
        $o=ms("SELECT `text` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($s)."'","_l");
//        if(preg_replace("/\{_(SCRIPT\:|STYLE\:|SCRIPT_ADD\:|STYLE_ADD\:).*?_\}/si",'',c($o))=='') return '';
        return "<!--".$p['id']."-->".$o."<!--/".$p['id']."-->";
}

?>