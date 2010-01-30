<?php

function c($s) { return trim($s,"\n\r\t \'\""); }
function SCRIPTS($s,$l=0) { if(!$l) $GLOBALS['_SCRIPT'][]=$s; else $GLOBALS['_SCRIPT'][$s]=$l; }
function STYLES($s,$l=0) { if(!$l) $GLOBALS['_STYLE'][]=$s; else $GLOBALS['_STYLE'][$s]=$l; }
function SCRIPT_ADD($s) { $GLOBALS['_SCRIPT_ADD'][$s]=$s; }
function STYLE_ADD($s) { $GLOBALS['_STYLE_ADD'][$s]=$s; }

// ==============================================================================================
// повызывать все процедуры в цикле

function modules($s) { $s_old=''; $stop=100; while($s!=$s_old && --$stop) {
        $s_old=$s; $s=preg_replace_callback("/\{_(.*?)_\}/s","module",$s);
        }
        return $s;
}

function module($t) { $s=$t[1]; // подцепить модули

        if(strstr($s,':')) { // подключаемый модуль

//		$s=str_replace(array('{#_','_#}'),array('{_','_}'),$s); // разэкранировать аргументы

                list($mod,$arg)=explode(':',$s,2); $mod=c($mod);

                if(!function_exists($mod)) {
                        $mod=str_replace('..','',$mod); // так просто

                        $modfile=$GLOBALS['site_mod'].$mod.".php";
                        $modfile2=$GLOBALS['site_module'].$mod.".php";

                        if(file_exists($modfile)) include_once($modfile);
                        elseif(file_exists($modfile2)) include_once($modfile2);
			else idie("Module error: ".h($modfile));
		
//                        if(!file_exists($modfile)) idie("Module error: ".htmlspecialchars($modfile));
//                        include_once($modfile);


                        if(!function_exists($mod)) idie("Нет такой функции: ".h($mod)
.($GLOBALS['admin']&&isset($GLOBALS['Date'])?"
<p><a href=".$GLOBALS['httphost']."editor/?Date=".$GLOBALS['Date'].">редактировать</a>
<p><div class=l onclick=\"majax('editor.php',{a:'editform',num:'".$GLOBALS['article']['num']."'})\">редактировать в окне</div>
":'')
);
                }
                return call_user_func($mod,modules(str_replace(array('{=','=}'),array('{_','_}'),c($arg))));
        }

        // иначе - просто вынуть из базы
        $p=ms("SELECT `id`,`text`,`type` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($s)."'","_1",$ttl);
        $o=$p['text'];

        if($p['type']=='news') { // для новостей - своя текстовая обработка
                $o=str_replace(array("\n\n","\n"),array("<p>","<br>"),"\n\n".$o);
                $o=preg_replace_callback("/(>[^<]+<)/si","kawa",$o);
                $o=preg_replace("/([\s>]+)\-([\s<]+)/si","$1".chr(151)."$2",$o); // длинное тире
                $o="<div id='".$p['id']."'>".$o."</div>";
        }

        if(preg_replace("/\{_(SCRIPT\:|STYLE\:|SCRIPT_ADD\:|STYLE_ADD\:).*?_\}/si",'',c($o))=='') return '';
        return "<!--".$p['id']."-->".$o."<!--/".$p['id']."-->";
}

function SCRIPT($s) { list($n,$s)=explode(':',$s,2); $GLOBALS['_SCRIPT'][c($n)]=addm(c($s)); return ''; }
function STYLE($s) { list($n,$s)=explode(':',$s,2); $GLOBALS['_STYLE'][c($n)]=addm(c($s)); return ''; }
function addm($e) { return (strstr($e,"\n")?$e:ms("SELECT `text` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($e)."'","_1",$ttl)); }

?>
