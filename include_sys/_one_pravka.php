<?php // Правки

function one_pravka($p,$answer='') { return "<div id=".$p['id']." class=po>"._one_pravka($p,$answer)."</div>"; }

function _one_pravka($p,$answer='') { global $admin;

$id=$p['id']; $prostynka = ''; $metka=$p['metka'];

if($answer=='') $answer=$p['Answer'];
if($answer!='') $answer="<div class=pct>$answer</div>";

        $knopki .= "\n<a id=k class=pkr href=\"javascript:pd($id)\">да, конечно!</a>";
        $knopki .= "\n<a id=k class=pkr href=\"javascript:pu($id)\">уговорили</a>";
        $knopki .= "\n<a id=k class=pkr href=\"javascript:pdi($id)\">да и</a>";

        $knopki .= "\n<a id=k class=pkl href=\"javascript:pe($id)\">EDIT</a>";
        $knopki .= "\n<a id=k class=pkl href=\"javascript:pc($id)\">edit_c</a>";
        $knopki .= "\n<a id=k class=pkl href=\"?a=ego&sc=".htmlspecialchars($p['sc'])."\">его правки</a>";
        $knopki .= "\n<a id=k class=pkl href=\"javascript:px($id)\">del</a>";
	$knopki .= "\n<a id=k class=pkl href=\"javascript:pp($id)\">подробнее</a>";

        $knopki .= "\n<a id=k class=pkg href=\"javascript:pz($id)\">так надо</a>";
        $knopki .= "\n<a id=k class=pkg href=\"javascript:pg($id)\">грамотей</a>";
        $knopki .= "\n<a id=k class=pkg href=\"javascript:pl($id)\">лень</a>";
        $knopki .= "\n<a id=k class=pkg href=\"javascript:ps($id)\">спам</a>";
        $knopki .= "\n<a id=k class=pkg href=\"javascript:pni($id)\">нет и</a>";

	$Name=htmlspecialchars($p['login']);
	if($Name=='') $Name=htmlspecialchars($p['Name']);
	if($Name=='') $Name=htmlspecialchars($p['lju']);
	if($Name=='') $Name=htmlspecialchars(substr($p['sc'],0,3));

//	$page_author='LLeo';

	if($metka=='new') $modescr='pc'; elseif($metka=='submit') $modescr='pcy'; else $modescr='pcn';

if($p['stdprav']=='no value') $p['stdprav']="no value:\n".$p['text']."\n".$p['textnew'];

return "
<div class=pkk>".$knopki."</div>
<div class=$modescr>
	<div class=ptime>".$p['DateTime']."</div>
	<span class=pch>".$Name."</span>
	<div class=pcc>".str_replace("\n",'<br>',$p['stdprav'])."</div>
</div>
".$answer;

}

?>
