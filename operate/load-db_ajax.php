<?php
//*************************************************//
//  Ajax利用DB読込
//*************************************************//
//  @author : kumata
//  共有変数
//  $sql_query    : SQL文
//  $sql_params   : プリペアドステートメント設定用
//  $where_clause : WHERE句
//  $sort_clause  : ORDER句
//*************************************************//

    // データベースに接続する処理
    require(__DIR__ . '/db_php/function.php');
    // エラーハンドラ
    require_once(__DIR__ . '/db_php/error_handler.php');
    // データベースに接続する処理
    $con_result = require_once(__DIR__ . '/db_php/dbconnect.php');
    // 接続に成功したかをチェック
    if (!$con_result){
        echo "error:d101";
        return false;
    }

    // flgパラメータの値を取得する
    $selectMode = isset($_GET['selectMode']) ? $_GET['selectMode'] : null;

    //WHEREデータの取得
    $where_dbn = isset($_GET['where_dbn']) ? $_GET['where_dbn'] : [];
    $where_name = isset($_GET['where_name']) ? $_GET['where_name'] : [];
    $where_value = isset($_GET['where_value']) ? $_GET['where_value'] : [];
    $where_operator = isset($_GET['where_operator']) ? $_GET['where_operator'] : [];
    $where_conj = isset($_GET['where_conj']) ? $_GET['where_conj'] : [];
    $whereEx_str = isset($_GET['whereEx_str']) ? $_GET['whereEx_str'] : [];;
    $whereEx_conj = isset($_GET['whereEx_Conj']) ? $_GET['whereEx_Conj'] : [];;

    // WHERE句の構築
    // WHERE句を初期化
    $where_clause = "";
    $sql_params = array();

    // WHERE句を構築する
    if (!empty($where_name)) {
        for ($i = 0; $i < count($where_name); $i++) {
            if ($where_name[$i]!=="") {
                if ($where_clause!=="") {
                    $where_clause .= " " . $where_conj[$i] ." ";
                }
                $param_name = ':' . $where_name[$i]; // パラメータ名を構築
                $where_clause .= "$where_dbn[$i].$where_name[$i] $where_operator[$i]";
                if ($where_value[$i] !== "") {
                    $where_clause .= " $param_name";
                    $sql_params[$param_name] = $where_value[$i]; // パラメータを追加
                }
            }
        }
    }

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
    $sort_dbn = isset($_GET['sort_dbn']) ? $_GET['sort_dbn'] : [];
    $sort_name = isset($_GET['sort_name']) ? $_GET['sort_name'] : [];
    $sort_value = isset($_GET['sort_value']) ? $_GET['sort_value'] : [];

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
    $limit_line = isset($_GET['limit_line']) ? $_GET['limit_line'] : [];
    $limit_offset = isset($_GET['limit_offset']) ? $_GET['limit_offset'] : [];

    // Limit句の構築
    // Limit句を初期化
    $limit_clause = "";

    // Limit句を構築する
    if ($limit_line!=="") {
        $limit_clause .= "$limit_line";
        if ($limit_offset!=="") {$limit_clause .= " OFFSET $limit_offset";}
    }

    // ファイルパスの設定
    $file_path = __DIR__ . '/load_php/' . $selectMode . '.php';

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
