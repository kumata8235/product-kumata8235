<?php
    // このスクリプトは、ID管理から新規IDの発行を受けて対象のIDを取得します
    // SQLクエリは他ファイルで設定されます

    // 初期化
    $sql_result = false; // defaultは失敗(False)
    $sql_stmt = NULL;

    // SQLがセットされているかチェック
    if (empty($sql_query_uid) || empty($sql_query_sid)) {
        echo '<link rel="stylesheet" href="./css/style_tdb.css">';
        echo '<div class="db_error-message">';
        echo "<strong>SQLクエリが指定されていません。</strong>";
        echo '</div>';
        return false;
    }

    // SQLクエリの実行処理
    try {
        // トランザクションを開始
        $dbh->beginTransaction();

        //対象IDのアップデート
        //プリペアドステートメントを設定
        $sql_stmt = $dbh->prepare($sql_query_uid);
        // 実行
        $sql_stmt -> execute();

        //対象IDの取得
        //プリペアドステートメントを設定
        $sql_stmt = $dbh->prepare($sql_query_sid);
        // 実行
        $sql_stmt -> execute();

        // 実行結果を取得
        $result_id = $sql_stmt -> fetch(PDO::FETCH_ASSOC);

        // SQLクエリ初期化
        $sql_query_uid = NULL;
        $sql_query_sid = NULL;
        
        // トランザクションをコミット
        $dbh->commit();

        return true;
    } catch (PDOException $e) {
        // エラーが発生した場合、ロールバック
        $dbh->rollback();
        // PDOExceptionが発生した場合の処理
        echo '<link rel="stylesheet" href="./css/style_tdb.css">';
        echo '<div class="db_error-message">';
        echo "<strong>データベースエラー: </strong><br>" . $e->getMessage(); // エラーメッセージを表示
        echo '</div>';
        return false;
    }
?>