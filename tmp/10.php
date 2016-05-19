<?php
	  function build_order_no(){
        return date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    echo $a = build_order_no();
    echo '<br />';
    echo uniqid();
    echo '<br />';
    echo rand(0, 9);

?>