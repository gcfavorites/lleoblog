<?php

function SCRIPTS($s,$l=0) { if(!$l) $GLOBALS['_SCRIPT'][]=$s; else $GLOBALS['_SCRIPT'][$s]=$l; }
function STYLES($s,$l=0) { if(!$l) $GLOBALS['_STYLE'][]=$s; else $GLOBALS['_STYLE'][$s]=$l; }
function SCRIPT_ADD($s) { $GLOBALS['_SCRIPT_ADD'][$s]=$s; }
function STYLE_ADD($e) { 
	$e="link href='".str_replace('{www_css}',$GLOBALS['www_css'],h($e))."' rel='stylesheet' type='text/css' charset='".$GLOBALS['wwwcharset']."'";
	$GLOBALS['_HEADD'][$e]=$e;
}

if(isset($_GET['module'])) { // обращение к модулям через GET
	$a=array("onload:'yes'"); foreach($_GET as $n=>$l) { if($n!='module') $a[]="'$n':'$l'"; }
	SCRIPTS("var page_onstart=[\"majax('".$_GET['module'].".php',{".implode(',',$a)."})\"]");
}

function mpr($s,$ara) { $s=$ara[$s]; foreach($ara as $n=>$l) $s=str_replace('{'.$n.'}',$l,$s); return $s; }
function mper($s,$ara) { foreach($ara as $n=>$l) $s=str_replace('{'.$n.'}',$l,$s); return $s; }

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

function modules($s) {
	$s_old=''; $stop=100; while($s!=$s_old && --$stop) {
		$s=str_replace("{_","\001",str_replace("_}","\002",$s));
		$s_old=$s;
		$s=preg_replace_callback("/\001([^\001\002]+)\002/s","module",$s);
	}
        return $s;
}

function module($t) { $s=$t[1]; // подцепить модули

        if(strstr($s,':')) { // подключаемый модуль

                list($mod,$arg)=explode(':',$s,2); $mod=c($mod);

	if(in_array($mod,$GLOBALS['norepeat_modules'])) { // некоторым модулям запрещено повторяться больше 1 раза
	    if($GLOBALS['norepeat_modules'][$mod]==1) return "<font color=red>MODULE \"$mod\" can not repeat!</font>";
	    $GLOBALS['norepeat_modules'][$mod]=1;
	}

                if(!function_exists($mod) && !in_array($mod,$GLOBALS['mainmod'])) {
                        $mod=str_replace('..','',$mod); // так просто

                        $modfile=$GLOBALS['site_mod'].$mod.".php";
                        $modfile2=$GLOBALS['site_module'].$mod.".php";

                        if(file_exists($modfile)) include_once($modfile);
			elseif(file_exists($modfile2)) include_once($modfile2);
			else return "<font color=red>MODULE NOT FOUND: <b>".$t[1]."</b></font>";
		
//                        if(!file_exists($modfile)) idie("Module error: ".htmlspecialchars($modfile));
//                        include_once($modfile);


                        if(!function_exists($mod)) idie("Нет такой функции: ".h($mod)
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
/*
        $o=$p['text'];

        if($p['type']=='news') { // для новостей - своя текстовая обработка
                $o=str_replace(array("\n\n","\n"),array("<p>","<br>"),"\n\n".$o);
                $o=preg_replace_callback("/(>[^<]+<)/si","kawa",$o);
                $o=preg_replace("/([\s>]+)\-([\s<]+)/si","$1".chr(151)."$2",$o); // длинное тире
                $o="<div id='".$p['id']."'>".$o."</div>";
        }
*/

        if(preg_replace("/\{_(SCRIPT\:|STYLE\:|SCRIPT_ADD\:|STYLE_ADD\:).*?_\}/si",'',c($o))=='') return '';
        return "<!--".$p['id']."-->".$o."<!--/".$p['id']."-->";
}

function SCRIPT($s) { list($n,$s)=explode(':',$s,2); $GLOBALS['_SCRIPT'][c($n)]=addm(c($s)); return ''; }
function STYLE($s) { list($n,$s)=explode(':',$s,2); $GLOBALS['_STYLE'][c($n)]=addm(c($s)); return ''; }
function addm($e) { return (strstr($e,"\n")?$e:ms("SELECT `text` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($e)."'","_l")); }

?>
