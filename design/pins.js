// ��������� pins

function insert_n(ctrl) {
var pp=GetCaretPosition(ctrl);
var es = ctrl.selectionEnd; // ���������� ���������� �������
var ss = ctrl.selectionStart;
var txt1 = ctrl.value.substring(0,ss); // ����� �����
var txt2 = ctrl.value.substring(es,ctrl.value.length); // ����� �����
var o=txt1.replace(/\s+$/,'') + "\n" + txt2.replace(/^\s+/,'');
ctrl.value = o;
setCaretPosition(ctrl, pp);
}

function ins(ctrl,i) {
var pp=GetCaretPosition(ctrl);
var es = ctrl.selectionEnd; // ���������� ���������� �������
var ss = ctrl.selectionStart;
var txt1 = ctrl.value.substring(0,ss); // ����� �����
var txt2 = ctrl.value.substring(es,ctrl.value.length); // ����� �����
ctrl.value = txt1 + i + txt2;
setCaretPosition(ctrl, pp);
}

function pins(ctrl,i,j) {
var pp=GetCaretPosition(ctrl);
var es = ctrl.selectionEnd; // ���������� ���������� �������
var ss = ctrl.selectionStart;
var txt1 = ctrl.value.substring(0,ss); // ����� �����
var txt2 = ctrl.value.substring(es,ctrl.value.length); // ����� �����
var txt3 = ctrl.value.substring(ss,es); // ����� �����
ctrl.value = txt1 + i + txt3 + j + txt2;
setCaretPosition(ctrl, pp);
}

function pins2(ctrl,i1,i2,j) {
var pp=GetCaretPosition(ctrl);
var es = ctrl.selectionEnd; // ���������� ���������� �������
var ss = ctrl.selectionStart;
var txt1 = ctrl.value.substring(0,ss); // ����� �����
var txt2 = ctrl.value.substring(es,ctrl.value.length); // ����� �����
var txt3 = ctrl.value.substring(ss,es); // ����� �����
ctrl.value = txt1 + i1 + txt3 + i2 + txt3 + j + txt2;
setCaretPosition(ctrl, pp);
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
