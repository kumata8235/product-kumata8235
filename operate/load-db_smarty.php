<?php
//*************************************************//
//  smarty利用DB読込
//*************************************************//
//  @author : kumata
//  共有変数
//  $loadMode     : 読込モード指定
//  $sql_params   : プリペアドステートメント設定用
//  $where_clause : WHERE句
//  $sort_clause  : ORDER句
//*************************************************//

    // 処理モード読込
    require (__DIR__ . '/operateini.php');

    $iniLoadDatasmarty = getIniOpeDataLoadsmarty();

    $setModeNumber = -1;
    // 処理対象確認
    for ($i = 0; $i < count($iniLoadDatasmarty); $i++) {
        if ($iniLoadDatasmarty[$i][0] === $loadMode) {
            $setModeNumber = $i;
            break;
        }
    }

    // 発見できなかった
    if ($setModeNumber===-1) {
        echo 'error:正しい処理が選択されませんでした。';
        exit();
    }

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

    //WHEREデータの取得
    $where_dbn = $loadWhere[0];
    $where_name = $loadWhere[1];
    $where_operator = $loadWhere[2];
    $where_value = $loadWhere[3];
    $where_conj = $loadWhere[4];

    // WHERE句の構築
    // WHERE句を初期化
    $where_clause = "";
    $sql_params = array();

    // WHERE句を構築する
    if (!empty($where_name)) {
        for ($i = 0; $i < count($where_name); $i++) {
            if ($where_name[$i]!=="") {
                $param_name = ':' . $where_name[$i]; // パラメータ名を構築
                $where_clause .= "$where_dbn[$i].$where_name[$i] $where_operator[$i]";
                if ($where_value[$i] !== "") {
                    $where_clause .= " $param_name";
                    $sql_params[$param_name] = $where_value[$i]; // パラメータを追加
                }
                if ($i < count($where_name) - 1) {
                    $where_clause .= " AND ";
                }
            }
        }
    }

    // ExWhereデータの取得
    $whereEx_str = $loadWhereEx[0];
    $whereEx_conj = $loadWhereEx[1];

    // ExWhere句を構築
    if (!empty($whereEx_str)) {
        for ($i = 0; $i < count($whereEx_str); $i++) {
            if ($whereEx_str[$i]!=="") {
                if ($where_clause!=="") {
                    $where_clause .= " " . $whereEx_Conj[$i] ." ";
                }
                $where_clause .= $whereEx_str[$i];
            }
        }
    }

    //ORDERデータの取得
    $sort_dbn = $loadSort[0];
    $sort_name = $loadSort[1];
    $sort_value = $loadSort[2];

    // ORDER句の構築
    // ORDER句を初期化
    $sort_clause = "";

    // ORDER句を構築する
    if (!empty($sort_name)) {
        for ($i = 0; $i < count($sort_name); $i++) {
            if ($sort_name[$i]!=="") {
                $sort_clause .= "$sort_dbn[$i].$sort_name[$i] $sort_value[$i]";
                if ($i < count($sort_name) - 1) {
                    $sort_clause .= ", ";
                }
            }
        }
    }

    //Limitデータの取得
    $limit_line = $loadLimit[0];
    $limit_offset = $loadLimit[1];

    // Limit句の構築
    // Limit句を初期化
    $limit_clause = "";

    // Limit句を構築する
    if ($limit_line!=="") {
        $limit_clause .= "$limit_line";
        if ($limit_offset!=="") {$limit_clause .= "OFFSET $limit_offset";}
    }

    // ファイルパスの設定
    $file_path = __DIR__ . '/load_php/' . $loadMode . '.php';

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
?>