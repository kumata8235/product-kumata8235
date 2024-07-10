<?php
    // このスクリプトはSQL文に応じたエラーメッセージを生成します

    /**
    * SQLクエリの種類を判別する関数.
    *
    * @param {string} $query SQLクエリ
    * @return {string} SQLクエリの種類 ("SELECT", "INSERT", "UPDATE", "DELETE", "DROP", "ALTER", "TRUNCATE", "CREATE", "RENAME", "UNKNOWN")
    */
    function detectQueryType($query) {
        // SQL文を小文字に変換してから判定する
        $lowercaseQuery = strtolower($query);

        // 判定ロジック
        if (strpos($lowercaseQuery, "select") !== false) {
            return "SELECT";
        } elseif (strpos($lowercaseQuery, "insert") !== false) {
            return "INSERT";
        } elseif (strpos($lowercaseQuery, "update") !== false) {
            return "UPDATE";
        } elseif (strpos($lowercaseQuery, "delete") !== false) {
            return "DELETE";
        } elseif (strpos($lowercaseQuery, "drop") !== false) {
            return "DROP";
        } elseif (strpos($lowercaseQuery, "alter") !== false) {
            return "ALTER";
        } elseif (strpos($lowercaseQuery, "truncate") !== false) {
            return "TRUNCATE";
        } elseif (strpos($lowercaseQuery, "create") !== false) {
            return "CREATE";
        } elseif (strpos($lowercaseQuery, "rename") !== false) {
            return "RENAME";
        } else {
            return "UNKNOWN"; // 未知のSQL文の場合
        }
    }

    /**
    * SQLクエリの種類に応じたエラーメッセージを設定する関数.
    *
    * @param {string} $query SQLクエリ
    * @return {void} エラーメッセージを出力
    */
    function setErrorMessage($query) {
        // エラーメッセージを設定するスイッチ文をここに記述
        switch (detectQueryType($query)) {
            case "SELECT":
                $errorMessage = "データの取得中にエラーが発生しました。";
                break;
            case "INSERT":
                $errorMessage = "データの挿入中にエラーが発生しました。";
                break;
            case "UPDATE":
                $errorMessage = "データの更新中にエラーが発生しました。";
                break;
            case "DELETE":
                $errorMessage = "データの削除中にエラーが発生しました。";
                break;
            case "DROP":
                $errorMessage = "データベースの削除中にエラーが発生しました。";
                break;
            case "ALTER":
                $errorMessage = "データベースの変更中にエラーが発生しました。";
                break;
            case "TRUNCATE":
                $errorMessage = "データベースの切り捨て中にエラーが発生しました。";
                break;
            case "CREATE":
                $errorMessage = "データベースの作成中にエラーが発生しました。";
                break;
            case "RENAME":
                $errorMessage = "データベースの名前変更中にエラーが発生しました。";
                break;
            default:
                $errorMessage = "未知のSQL文が検出されました。";
                break;
        }
    
        // エラーメッセージを出力
        echo $errorMessage;
    }

    /**
    * データベース接続を閉じる関数.
    *
    * @param {object|null} $dbh PDOオブジェクトまたはnull
    * @return {void}
    */
    function closeDatabaseConnection($dbh) {
        if ($dbh !== null) {
            $dbh = null; // データベース接続を閉じる
        }
    }
?>