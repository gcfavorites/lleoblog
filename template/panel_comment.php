<?php // панель редактора заметок

$panel = strtr("
<div style='margin: 1px 0 1px 0;'>
<img class=knop onClick=\"ti('".$id."','".chr(160)."{select}')\" src=".$www_design."e2/nbsp.gif>
<img class=knop onClick=\"ti('".$id."','".chr(169)."{select}')\" src=".$www_design."e2/copy.gif>
<img class=knop onClick=\"ti('".$id."','".chr(151)."{select}')\" src=".$www_design."e2/mdash.gif>
<img class=knop onClick=\"ti('".$id."','".chr(171)."{select}".chr(187)."')\" src=".$www_design."e2/ltgt.gif>
<img class=knop onClick=\"ti('".$id."','[b]{select}[/b]')\" src=".$www_design."e2/bold.gif>
<img class=knop onClick=\"ti('".$id."','[i]{select}[/i]')\" src=".$www_design."e2/italic.gif>
<img class=knop onClick=\"ti('".$id."','[s]{select}[/s]')\" src=".$www_design."e2/strikethrough.gif>
<img class=knop onClick=\"ti('".$id."','[u]{select}[/u]')\" src=".$www_design."e2/underline.gif>
</div>
","\n","");

?>