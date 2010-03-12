<?php // панель редактора заметок

$panel = strtr("
<div style='margin: 1px 0 1px 0;'>
<img class=knop onClick=\"pns('".$id."','".chr(160)."','')\" src=".$www_design."e2/nbsp.gif>
<img class=knop onClick=\"pns('".$id."','".chr(169)."','')\" src=".$www_design."e2/copy.gif>
<img class=knop onClick=\"pns('".$id."','".chr(151)."','')\" src=".$www_design."e2/mdash.gif>
<img class=knop onClick=\"pns('".$id."','".chr(171)."','".chr(187)."')\" src=".$www_design."e2/ltgt.gif>
<img class=knop onClick=\"pns('".$id."','[b]','[/b]')\" src=".$www_design."e2/bold.gif>
<img class=knop onClick=\"pns('".$id."','[i]','[/i]')\" src=".$www_design."e2/italic.gif>
<img class=knop onClick=\"pns('".$id."','[s]','[/s]')\" src=".$www_design."e2/strikethrough.gif>
<img class=knop onClick=\"pns('".$id."','[u]','[/u]')\" src=".$www_design."e2/underline.gif>
</div>
","\n","");

?>