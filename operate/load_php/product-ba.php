<?php
    // このスクリプトは商品データの読込を行います
    // Where句・Order句・Limit句が設定されている場合は追加します
    // 取得できるデータ ALL 要素名は設計書通り
    // 対象DB:Product 指定DB名:P

    //初期化
    $data_product = array();
    
    //SQL定義
    $sql_query = "SELECT * FROM product as p";

    //WHERE句があれば追加
    if (!empty($where_clause)) {
        $sql_query .= " WHERE $where_clause";
    }
    
    //ORDER句があれば追加
    if (!empty($sort_clause)) {
        $sql_query .= " ORDER BY $sort_clause";
    }

    //Limit句があれば追加
    if (!empty($limit_clause)) {
        $sql_query .= " LIMIT $limit_clause";
    }

    // SQL実行
    $sql_result = require(dirname(__DIR__) . '/db_php/sqlexec.php');
    
    // 失敗してたら終了
    if (!$sql_result) {return "error:P001";}

    // データ移行
    $data_product = $sql_stmt->fetchAll(PDO::FETCH_ASSOC);

    // json形式に変換
    echo json_encode($data_product);

    return true;
?>