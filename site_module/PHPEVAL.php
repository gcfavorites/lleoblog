<?php

function PHPEVAL($e) { if(!$GLOBALS['admin']) return " SYSTEM ERROR! ";
	$o='';
	eval($e);
	return $o;
}
?> 
