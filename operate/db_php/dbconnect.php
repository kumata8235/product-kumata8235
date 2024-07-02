<?php
    // DB情報の取得
    require_once(__DIR__ . '/dbconnectinfo.php');

    try {
        //　接続
        $dbh = new PDO("mysql:host=$dbipadd;dbname=$dbname",$dbroot,$dbpass);
        
        // エラーが発生した場合に例外を投げるように設定
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 文字コードおよび日本時間に設定
        $dbh->exec("SET CHARACTER SET utf8"); // 文字エンコーディングをUTF-8に設定
        $dbh->exec("SET time_zone = '+09:00'"); // MySQLのタイムゾーンを日本時間に設定
    } catch (PDOException $e) {
        // エラーが発生した場合はエラーメッセージをスタイリッシュに表示してスクリプトを終了
        $err_msg = '<link rel="stylesheet" href="./../operate/func_php/css/style_tdb.css">';
        $err_msg .= '<div class="db_error-message">';
        $err_msg .= "<strong>データベースに接続できませんでした。</strong><br> {$e->getMessage()}";
        $err_msg .= '</div>';
        return false;
    }

    // 初期化
    $sql_query = NULL;
    $sql_stmt = NULL;
    $sql_params = array();
    $sql_query_sid = NULL;
    $sql_query_uid = NULL;

    // smartyの初期化
    //テンプレート利用準備
    require_once (__DIR__ .'/smarty/Smarty.class.php');

    $smarty = new Smarty();
    $smarty->template_dir = __DIR__ . '/smarty/templates/';
    $smarty->compile_dir = __DIR__ . '/smarty/templates_c/';

    return true;
?>
