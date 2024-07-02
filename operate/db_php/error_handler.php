<?php
    // エラーハンドラを設定する
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        // データベース接続を閉じる処理を実行
        global $dbh;
        closeDatabaseConnection($dbh);

        // HTTPステータスコード500を返す
        http_response_code(500);

        // エラーが発生した旨を返す
        echo "An error occurred on the server.";
        return false;
    });
?>