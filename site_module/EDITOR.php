<?php

function EDITOR($e) {

return (!$GLOBALS['admin']?'':"<p><input TYPE=\"BUTTON\" VALUE=\"EDITOR\" onClick=\"window.location.href='"
.$GLOBALS['wwwhost']."editor/?Date=".$GLOBALS['article']["Date"]."' \">");

}

?>
