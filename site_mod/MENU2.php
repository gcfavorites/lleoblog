<?php // Строчное меню

STYLES("Меню2","
.menu2 {
  text-align: center;
  font-size:10px;
  color: #814c52;
  background: #b8858e url({www_design}silkway/menu2.gif);
  width: 700px;
  height: 24px;
  line-height: 24px;
}

.menu2l a { color: #814c52; } /*  text-decoration:none; */
");

function MENU2($e) { $o=''; $p=explode("\n",$e);

	foreach($p as $l) if(c($l)!='') {
		list($txt,$link)=explode("|",$l); $txt=c($txt); $link=c($link);
		if($link=='') $o .= "|<span class=menu2a>".$txt."</span>";
		else $o .= "|<span class=menu2l><a href='".$link."'>".$txt."</a></span>";
		}
	$o=str_replace("|"," &nbsp; | &nbsp; ",trim($o,'|'));
	return "<div class=menu2>".$o."</div>";

}

?>
