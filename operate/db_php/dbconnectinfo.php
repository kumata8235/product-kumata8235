<?php
    //データベース接続情報管理
    $dbipadd = 'localhost';
    $dbroot = 'root';
    $dbpass = stristr(php_uname('s'), 'Windows') ? '' : 'root';
    $dbname = 'ninja-dbn';
?>