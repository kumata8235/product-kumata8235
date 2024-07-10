<?php
    // 商品情報を新規登録するスクリプト

    try {
        //商品IDの新規発行
        //SQL定義
        $sql_query_uid = "UPDATE createdid SET product_id = LAST_INSERT_ID(product_id+1)"; // 商品ID(ID管理テーブル)を1追加
        $sql_query_sid = "SELECT product_id FROM createdid"; // 最新の商品ID(ID管理テーブル)を取得する
        // SQL実行
        // id取得用のSQLを実行
        $sql_result = require(dirname(__DIR__) . '/db_php/sqlexec_for-id.php');
        
        // 失敗してたら終了
        if (!$sql_result) {return "error:C001";}

        //商品IDの設定
        $product_id = "pr-". sprintf("%07d", $result_id['product_id']);

        // 商品画像用ファイル名を生成
        $product_file = "ninja-" . $product_id;

        // 商品画像
        if(isset($_FILES['UploadFile']['name'])&&$_FILES['UploadFile']['error'] == 0) {
            // ファイルがアップロードされている場合の処理
            // アップロードされたファイルの拡張子を取得
            $extension = pathinfo($_FILES['UploadFile']['name'], PATHINFO_EXTENSION);
            $newFileName = $product_file . "." . $extension; // ファイル名+拡張子でファイル名生成
            $target_dir = dirname(dirname(__dir__)) . "/ninja/img_course/"; // アップロードされたファイルの移動先指定
            $target_file = $target_dir . $newFileName; // ファイルパスを生成

            // 同名ファイルが存在していたら削除
            if (file_exists($target_file)) {
                unlink($target_file);
            }

            // ファイル移動
            if (move_uploaded_file($_FILES['UploadFile']['tmp_name'], $target_file)) {
                // ファイル移動時に、新しいファイル名を設定
                $picture = $newFileName;
            } else {
                // ファイル移動に失敗した場合はエラー処理を行う
                return "error:F001";
            }
        } else {
            // ファイルがアップロードされていない場合にはデフォルトの画像を使用
            $picture = "no_image.jpg";
        }

        $rstart = "";
        // リリース日
        if (isset($_POST['release_date'])) {
            // リリース日が設定されている場合の処理
            if ($_POST['release_date'] === '0000-00-00' || $_POST['release_date'] === '') {
                // リリース日が設定されていなかったり、0000-00-00の場合はNULLを設定
                $rstart = null; // リリース日設定
            } else {
                $rstart = $_POST['release_date']; // リリース日設定
            }
        }

        $rend = "";
        // リリース終了日
        if (isset($_POST['release_end'])) {
            // リリース終了日が設定されている場合の処理
            if ($_POST['release_end'] === '0000-00-00' || $_POST['release_end'] === '') {
                // リリース終了日が設定されていなかったり、0000-00-00の場合はNULLを設定
                $rend = null; // リリース終了日設定
            } else {
                $rend = $_POST['release_end']; // リリース終了日設定
            }
        }

        // 商品DB登録
        // 商品追加用SQLの発行
        $sql_query = "INSERT INTO product (`product_id`, `name`, `play_type`, `play_venue`, `play_days`, `play_story`, `release_date`, `release_end`, `price`, `picture`, `createdAt`)
                    VALUES (:product_id,:name,:play_type,:play_venue,:play_days,:play_story,:release_date,:release_end,:price,:picture,NOW())";
        // パラメーターに値をセット
        $sql_params = array(
                        ':product_id' => $product_id,
                        ':name' => $_POST['name'],
                        ':play_type' => $_POST['play_type'],
                        ':play_venue' => $_POST['play_venue'],
                        ':play_days' => $_POST['play_days'],
                        ':play_story' => $_POST['play_story'],
                        ':release_date' => $rstart,
                        ':release_end' => $rend,
                        ':price' => $_POST['price'],
                        ':picture' => $picture
                    );
        // SQL実行
        $sql_result = require(dirname(__DIR__) . '/db_php/sqlexec.php');

        // 失敗してたら終了
        if (!$sql_result) {return "error:P002";}
    
        return true; // 成功したらTrueを返す
    } catch (PDOException $e) {
        // エラーが発生した場合は、エラーメッセージをJSON形式で渡す
        echo json_encode(['error' => $e->getMessage()]);
    }
?>