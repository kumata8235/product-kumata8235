<?php
//*************************************************//
//  SQL実行
//*************************************************//
//  @author : kumata
//  共有変数
//  $sql_query     : SQL文
//  $sql_params    : プリペアドステートメント設定用
//  $sql_stmt      : 実行結果
//  $dbh           : DB接続
//*************************************************//

    // 初期化
    $sql_result = false; // defaultは失敗(False)
    $sql_stmt = NULL;

    // SQLがセットされているかチェック
    if (empty($sql_query)) {
        echo '<link rel="stylesheet" href="./css/style_tdb.css">';
        echo '<div class="db_error-message">';
        echo "<strong>SQLクエリが指定されていません。</strong>";
        echo '</div>';
        return false;
    }

    // プリペアドステートメントを設定
    $sql_stmt = $dbh->prepare($sql_query);

    // SQLクエリの実行処理
    try {
        // 実行結果
        if ($sql_params ? $sql_stmt->execute($sql_params) : $sql_stmt->execute()) {
            // 初期化
            $sql_query = "";
            $sql_params = array();

            return true;
        } else {
            // 失敗
            echo '<link rel="stylesheet" href="./css/style_tdb.css">';
            echo '<div class="db_error-message">';
            $error_message = setErrorMessage($sql_query);
            echo "<strong>{$error_message}</strong>";
            echo $sql_query;
            echo '</div>';
            return false;
        }
    } catch (PDOException $e) {
        // PDOExceptionが発生した場合の処理
        echo '<link rel="stylesheet" href="./css/style_tdb.css">';
        echo '<div class="db_error-message">';
        echo "<strong>データベースエラー: </strong><br>" . $e->getMessage(); // エラーメッセージを表示
        echo '</div>';
        return false;
    }
?>