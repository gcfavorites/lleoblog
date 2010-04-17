<?php

// dier(ms("SHOW INDEX FROM `dnevnik_tags`","_a",0));

// function msq_index($tb,$index) { 

// $s .= msq_add_index("dnevnik_tags","num","(`num`)","индекс нужен");
$s .= msq_del_index("dnevnik_tags","num","индекс не нужен, я его сгоряча прописал, illand меня поправил");

/*


Array
(
[0] => Array
(
[Table] => dnevnik_tags
[Non_unique] => 0
[Key_name] => PRIMARY
[Seq_in_index] => 1
[Column_name] => num
[Collation] => A
[Cardinality] =>
[Sub_part] =>
[Packed] =>
[Null] =>
[Index_type] => BTREE
[Comment] =>
)

[1] => Array
(
[Table] => dnevnik_tags
[Non_unique] => 0
[Key_name] => PRIMARY
[Seq_in_index] => 2
[Column_name] => tag
[Collation] => A
[Cardinality] => 10
[Sub_part] =>
[Packed] =>
[Null] =>
[Index_type] => BTREE
[Comment] =>
)

[2] => Array
(
[Table] => dnevnik_tags
[Non_unique] => 1
[Key_name] => tag
[Seq_in_index] => 1
[Column_name] => tag
[Collation] => A
[Cardinality] =>
[Sub_part] =>
[Packed] =>
[Null] =>
[Index_type] => BTREE
[Comment] =>
)

[3] => Array
(
[Table] => dnevnik_tags
[Non_unique] => 1
[Key_name] => num
[Seq_in_index] => 1
[Column_name] => num
[Collation] => A
[Cardinality] =>
[Sub_part] =>
[Packed] =>
[Null] =>
[Index_type] => BTREE
[Comment] =>
)

)




*/

?>