<?php
    // このスクリプトは、指定された条件でデータを取得し、特定のPHPファイルを実行して結果を返します

    // データベースに接続する処理を含むファイルを読み込む
    require(__DIR__ . '/db_php/function.php');
    // エラーハンドラを含むファイルを読み込む
    require_once(__DIR__ . '/db_php/error_handler.php');
    // データベースに接続する処理を含むファイルを読み込む
    $con_result = require_once(__DIR__ . '/db_php/dbconnect.php');
    
    // 接続に成功したかをチェック
    if (!$con_result) {
        // 接続失敗時にエラーメッセージを返して処理を中止する
        echo "error:d101";
        return false;
    }

    // flgパラメータの値を取得する
    $selectMode = isset($_GET['selectMode']) ? $_GET['selectMode'] : null;

    // WHERE句の構築
    $where_dbn = isset($_GET['where_dbn']) ? $_GET['where_dbn'] : []; // DB名前
    $where_name = isset($_GET['where_name']) ? $_GET['where_name'] : []; // 要素名
    $where_value = isset($_GET['where_value']) ? $_GET['where_value'] : []; // 値
    $where_operator = isset($_GET['where_operator']) ? $_GET['where_operator'] : []; // 演算子
    $where_conj = isset($_GET['where_conj']) ? $_GET['where_conj'] : []; // 論理演算子
    $whereEx_str = isset($_GET['whereEx_str']) ? $_GET['whereEx_str'] : []; // その他設定したいWhere句
    $whereEx_Conj = isset($_GET['whereEx_Conj']) ? $_GET['whereEx_Conj'] : []; // その他設定したいWhere句用論理演算子

    // WHERE句を初期化
    $where_clause = "";
    // パラメーター初期化
    $sql_params = array();

    // WHERE句を構築する
    // 設定する要素名が空でない場合のみ処理する
    if (!empty($where_name)) {
        for ($i = 0; $i < count($where_name); $i++) {
            // 要素名が空白ではない場合のみ処理する
            if ($where_name[$i] !== "") {
                // Where句が空白でない場合のみ処理する
                if ($where_clause !== "") {
                    // 対応する論理演算子を追加
                    $where_clause .= " " . $where_conj[$i] . " ";
                }
                $param_name = ':' . $where_name[$i]; // パラメータ名を構築
                $where_clause .= "$where_dbn[$i].$where_name[$i] $where_operator[$i]"; // DB名.要素名.演算子を追加
                // 設定する値が空白でない場合のみ処理する
                if ($where_value[$i] !== "") {
                    // Where句にパラメータを追加
                    $where_clause .= " $param_name";
                    $sql_params[$param_name] = $where_value[$i]; // パラメータを追加
                }
            }
        }
    }

    // ExWhere句を構築する
    // その他設定したいWhere句が空でない場合のみ処理する
    if (!empty($whereEx_str)) {
        for ($i = 0; $i < count($whereEx_str); $i++) {
            // その他設定したいWhere句が空白でない場合のみ処理する
            if ($whereEx_str[$i] !== "") {
                if ($where_clause !== "") {
                    // 対応する論理演算子追加
                    $where_clause .= " " . $whereEx_Conj[$i] . " ";
                }
                // その他設定したいWhere句を追加
                $where_clause .= $whereEx_str[$i];
            }
        }
    }

    // ORDER句の構築
    $sort_dbn = isset($_GET['sort_dbn']) ? $_GET['sort_dbn'] : []; // DB名
    $sort_name = isset($_GET['sort_name']) ? $_GET['sort_name'] : []; // 要素名
    $sort_value = isset($_GET['sort_value']) ? $_GET['sort_value'] : []; // 並び順の値

    // ORDER句を初期化
    $sort_clause = "";

    // ORDER句を構築する
    // ORDER句に設定する要素名が空でない場合のみ処理する
    if (!empty($sort_name)) {
        for ($i = 0; $i < count($sort_name); $i++) {
            // ORDER句に設定する要素名が空白でない場合のみ処理する
            if ($sort_name[$i] !== "") {
                // DB名.要素名 並び順の値を追加
                $sort_clause .= "$sort_dbn[$i].$sort_name[$i] $sort_value[$i]";
                // ORDER句に追加される要素がまだある場合に処理する
                if ($i < count($sort_name) - 1) {
                    // カンマを追加
                    $sort_clause .= ", ";
                }
            }
        }
    }

    // Limit句の構築
    $limit_line = isset($_GET['limit_line']) ? $_GET['limit_line'] : []; // 行数
    $limit_offset = isset($_GET['limit_offset']) ? $_GET['limit_offset'] : []; // オフセット

    // Limit句を初期化
    $limit_clause = "";

    // Limit句を構築する
    // 行数が設定されている場合のみ処理する
    if ($limit_line !== "") {
        // 行数を設定
        $limit_clause .= "$limit_line";
        // オフセットが設定されている
        if ($limit_offset !== "") {
            // オフセットを設定
            $limit_clause .= " OFFSET $limit_offset";
        }
    }

    // ファイルパスの設定
    $file_path = __DIR__ . '/load_php/' . $selectMode . '.php';

    // 指定されたファイルが存在するか確認
    if (file_exists($file_path)) {
        // ファイルが存在する場合、ファイルを実行
        $con_result = require($file_path);
        // 実行結果にエラーメッセージが含まれているかチェック
        if (strpos($con_result, "error:") !== false) {
            // エラーがある場合、エラーメッセージを出力して処理を中止する
            echo $con_result;
            exit();
        }
    } else {
        // 指定されたファイルが存在しない場合の処理
        echo "error:f101";
        exit();
    }

    // DB接続を終了する関数を登録
    register_shutdown_function('closeDatabaseConnection', $dbh);
?>
