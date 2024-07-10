<?php
    // このスクリプトは、データベース接続を行い、選択されたモードに基づいて特定のファイルを実行し、処理結果をJSON形式で返します

    // 初期化
    $err_msg = "";

    // データベースに接続する処理を含むファイルを読み込む
    require(__DIR__ . '/db_php/function.php');
    // エラーハンドラを含むファイルを読み込む
    require_once(__DIR__ . '/db_php/error_handler.php');
    // データベースに接続する処理を含むファイルを読み込む
    $con_result = require_once(__DIR__ . '/db_php/dbconnect.php');
    
    // 接続に成功したかをチェック
    if (!$con_result) {
        // 接続失敗時にエラーメッセージを返す
        echo json_encode(array("error" => "d101"));
        exit();
    }
    
    // ファイルパスの設定
    $file_path = __DIR__ . '/regist_php/' . $_POST['selectMode'] . '.php';

    // 指定されたファイルが存在するか確認
    if (file_exists($file_path)) {
        // ファイルが存在する場合、ファイルを実行
        $con_result = require($file_path);
        // 実行結果にエラーメッセージが含まれているかチェック
        if (strpos($con_result, "error:") !== false) {
            // エラーがある場合、エラーメッセージを返す
            echo json_encode(array("error" => $con_result));
            exit();
        }
    } else {
        // ファイルが存在しない場合、エラーメッセージを返す
        echo json_encode(array("error" => "f101"));
        exit();
    }

    // データベース接続を終了する関数を登録
    register_shutdown_function('closeDatabaseConnection', $dbh);

    // 処理が成功したことを示すJSONを返す
    echo json_encode(array("success" => true));
?>