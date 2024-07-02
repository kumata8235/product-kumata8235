<?php
//*************************************************//
//  Ajax利用DB書込
//*************************************************//
//  @author : kumata
//  共有変数
//  $sql_query    : SQL文
//  $sql_params   : プリペアドステートメント設定用
//  $where_clause : WHERE句
//  $sort_clause  : ORDER句
//*************************************************//
    //初期化
    $err_msg = "";

    // データベースに接続する処理
    require(__DIR__ . '/db_php/function.php');
    // エラーハンドラ
    require_once(__DIR__ . '/db_php/error_handler.php');
    // データベースに接続する処理
    $con_result = require_once(__DIR__ . '/db_php/dbconnect.php');
    
    // 接続に成功したかをチェック
    if (!$con_result){
        echo 'error:d101'; // エラーメッセージをJSON形式で返す
        exit();
    }
    
    // ファイルパスの設定
    //$file_path = __DIR__ . '/regist_php/' . $_POST['selectMode'] . '.php';
    $file_path = __DIR__ . '/regist_php/' . 'product-bau.php';

    // ファイルが存在する？
    if (file_exists($file_path)) {
        $con_result = require($file_path);
        if (strpos($con_result, "error:") !== false) {
            echo $con_result;
            exit();
        }
    } else {
        // ファイルが存在しない場合の処理
        echo "error:f101";
        exit();
    }

    // DB接続終了
    register_shutdown_function('closeDatabaseConnection', $dbh);

    echo json_encode(array("success" => true)); // 成功したことを示すJSONを返す
?>