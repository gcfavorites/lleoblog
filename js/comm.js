loadCSS('commentstyle.css');

function kus(u) { if(u) majax('login.php',{action:'getinfo',unic:u}); }// ������ �������� ������
function kd(e) { if(confirm('����� �������?')) majax('comment.php',{a:'del',id:ecom(e).id}); } // ������� �����������
function ked(e) { majax('comment.php',{a:'edit',id:ecom(e).id}); } // ������������� �����������
function ksc(e) { majax('comment.php',{a:'scr',id:ecom(e).id}); } // ������-��������
function rul(e) { majax('comment.php',{a:'rul',id:ecom(e).id}); } // rul-�� rul
function ka(e) { e=ecom(e); majax('comment.php',{a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum}); } // ��������

function kpl(e) { majax('comment.php',{a:'plus',id:ecom(e).id}); } // ������
function kmi(e) { majax('comment.php',{a:'minus',id:ecom(e).id}); } // minus

function opc(e) { e=ecom(e); majax('comment.php',{a:'pokazat',oid:e.id,lev:e.style.marginLeft,comnu:comnum}); } // ��������

function ecom(e) {
        while( ( e.id == '' || e.id == undefined ) && e.parentNode != undefined) e=e.parentNode;
        if(e.id == undefined) return 0; return e;
}

// ============================  ��������� ������ ������� ������ ���� ������ �����: ========================
var src='comm.js'; ajaxoff(); var r=JSload[src]; JSload[src]='load'; if(r && r!='load') eval(r);