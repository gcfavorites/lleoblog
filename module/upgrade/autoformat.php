<?

msq_add_pole("dnevnik_zapisi","autoformat","enum('no','p','pd') default 'no'","возможность управлять автоформатированием заметок");
msq_add_pole("dnevnik_zapisi","autokaw","enum('auto','no')","возможность отключить автообработку кавычеки тире");

msq_add_pole("dnevnik_zapisi","count_comments_open","int(10) unsigned default '0'","число открытых комментариев у заметки - чтоб не лазить за ними по базе всякий раз");

msq_del_pole("dnevnik_zapisi","include","поле include больше не нужно, мы перешли на систему модулей II поколения");

#msq_del_pole("dnevnik_zapisi","autoformat","возможность управлять автоформатированием заметок");
#msq_del_pole("dnevnik_zapisi","autokaw","возможность отключить автообработку кавычек и тире");

?>