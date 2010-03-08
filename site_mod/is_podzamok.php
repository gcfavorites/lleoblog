<?php /* только дл€ подзамочных друзей

≈сли страницу открыл посетитель, с доступом 'podzamok' - выдаетс€ текст перед разделителем |, если обычный 'user' - то текст после |.

{_is_podzamok: »сходники тут: http://10.8.0.1/rrr.zip | »сходники решил пока не выкладывать. _}
{_is_podzamok:  ак много набежало идиотов! | _}

*/

function is_podzamok($e) { 
        list($a,$b)=explode('|',$e,2);
        return ($GLOBALS['podzamok'] ? c($a) : c($b) );


//return ($GLOBALS['podzamok']?$e:''); 
}

?>
