// процедуры pins

function insert_n(ctrl) {
var pp=GetCaretPosition(ctrl);
var es = ctrl.selectionEnd; // определяем координаты курсора
var ss = ctrl.selectionStart;
var txt1 = ctrl.value.substring(0,ss); // текст перед
var txt2 = ctrl.value.substring(es,ctrl.value.length); // текст после
var o=txt1.replace(/\s+$/,'') + "\n" + txt2.replace(/^\s+/,'');
ctrl.value = o;
setCaretPosition(ctrl, pp);
}

function pns(ctrl,i,j) { pins(document.getElementById(ctrl),i,j); }
function pns2(ctrl,i1,i2,j) { pins2(document.getElementById(ctrl),i1,i2,j); }

function ins(ctrl,i) {
var pp=GetCaretPosition(ctrl);
var es = ctrl.selectionEnd; // определяем координаты курсора
var ss = ctrl.selectionStart;
var txt1 = ctrl.value.substring(0,ss); // текст перед
var txt2 = ctrl.value.substring(es,ctrl.value.length); // текст после
ctrl.value = txt1 + i + txt2;
setCaretPosition(ctrl, pp);
}

function pins(ctrl,i,j) {
var pp=GetCaretPosition(ctrl);
var es = ctrl.selectionEnd; // определяем координаты курсора
var ss = ctrl.selectionStart;
var txt1 = ctrl.value.substring(0,ss); // текст перед
var txt2 = ctrl.value.substring(es,ctrl.value.length); // текст после
var txt3 = ctrl.value.substring(ss,es); // текст между
ctrl.value = txt1 + i + txt3 + j + txt2;
setCaretPosition(ctrl, ss+(i+j+txt3).length);
ctrl.selectionStart=ss+i.length;
ctrl.selectionEnd=ss+(i+txt3).length;

}

function pins2(ctrl,i1,i2,j) {
var pp=GetCaretPosition(ctrl);
var es = ctrl.selectionEnd; // определяем координаты курсора
var ss = ctrl.selectionStart;
var txt1 = ctrl.value.substring(0,ss); // текст перед
var txt2 = ctrl.value.substring(es,ctrl.value.length); // текст после
var txt3 = ctrl.value.substring(ss,es); // текст между
var val=txt1 + i1 + txt3 + i2 + txt3 + j + txt2;
ctrl.value = val;
setCaretPosition(ctrl, ss+val.length);
ctrl.selectionStart=ss;
ctrl.selectionEnd=ss+val.length;
}




var scrollTop = 0;

function GetCaretPosition(ctrl) { var CaretPos = 0; // IE Support
if(document.selection) { ctrl.focus (); var Sel = document.selection.createRange (); Sel.moveStart ('character', -ctrl.value.length);
CaretPos = Sel.text.length; } // Firefox support
else if(ctrl.selectionStart || ctrl.selectionStart == '0') { CaretPos = ctrl.selectionStart; } scrollTop = ctrl.scrollTop;
return (CaretPos);
}

function setCaretPosition(ctrl,pos) {
if(ctrl.setSelectionRange){ ctrl.focus(); ctrl.setSelectionRange(pos,pos); }
else if(ctrl.createTextRange){ var range = ctrl.createTextRange();
range.collapse(true); range.moveEnd('character',pos); range.moveStart('character',pos); range.select(); }
ctrl.scrollTop = scrollTop;
}
