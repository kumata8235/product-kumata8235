<?php
    // 更新フィールドと値の配列を初期化
    $update_item = array();
    $where_item = array();

    // 対象条件の設定
    // product_id
    if (isset($_POST['product_id'])) {
        $where_item[] = "product_id=:product_id";
        $sql_params[':product_id'] = $_POST['product_id'];
        $product_id = $_POST['product_id'];
    }

    // 更新フィールドの設定
    // 商品名
    if (isset($_POST['name'])) {
        $update_item[] = "name=:name";
        $sql_params[':name'] = $_POST['name'];
    }

    // プレイ形式
    if (isset($_POST['play_type'])) {
        $update_item[] = "play_type=:play_type";
        $sql_params[':play_type'] = $_POST['play_type'];
    }

    // 会場
    if (isset($_POST['play_venue'])) {
        $update_item[] = "play_venue=:play_venue";
        $sql_params[':play_venue'] = $_POST['play_venue'];
    }

    // 開催日
    if (isset($_POST['play_days'])) {
        $update_item[] = "play_days=:play_days";
        $sql_params[':play_days'] = $_POST['play_days'];
    }

    // ストーリー
    if (isset($_POST['play_story'])) {
        $update_item[] = "play_story=:play_story";
        $sql_params[':play_story'] = $_POST['play_story'];
    }

    // リリース日
    if (isset($_POST['release_date'])) {
        $update_item[] = "release_date=:release_date";
        if ($_POST['release_date'] === '0000-00-00' || $_POST['release_date'] === '') {
            $sql_params[':release_date'] = null;
        } else {
            $sql_params[':release_date'] = $_POST['release_date'];
        }
    }

    // リリース終了日
    if (isset($_POST['release_end'])) {
        $update_item[] = "release_end=:release_end";
        if ($_POST['release_end'] === '0000-00-00' || $_POST['release_end'] === '') {
            $sql_params[':release_end'] = null;
        } else {
            $sql_params[':release_end'] = $_POST['release_end'];
        }
    }

    // 金額
    if (isset($_POST['price'])) {
        $update_item[] = "price=:price";
        $sql_params[':price'] = $_POST['price'];
    }

    //　deltedAt
    if (isset($_POST['deletedAt'])) {
        $update_item[] = "deletedAt=NOW()";
    }

    // 新しいファイルがアップロードされてる？
    if (isset($_FILES['UploadFile']['name']) && $_FILES['UploadFile']['error'] == 0) {
        $product_file = "ninja-" . $product_id;
        $extension = pathinfo($_FILES['UploadFile']['name'], PATHINFO_EXTENSION);
        $newFileName = $product_file . "." .$extension;
        $target_dir = dirname(dirname(__dir__)) . "/ninja/img_course/";
        $target_file = $target_dir . $newFileName; 

        // 同名のファイルがあるか？
        $files = glob($target_dir . DIRECTORY_SEPARATOR . basename($product_file) . '.*');

        // 各ファイルに対してunlink()を呼び出して削除
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }       

        // 新しいファイルをアップロード
        if (move_uploaded_file($_FILES['UploadFile']['tmp_name'], $target_file)) {
            // アップロードが成功したら新しいファイル名を設定
            $picture = $newFileName;
            $update_item[] = "picture=:picture";
            $sql_params[':picture'] = $picture;
        } else {
            // アップロードに失敗した場合はエラー処理を行う
            return "error:F001";
        }
    }

    // 更新項目と対象条件はある？
    if (!empty($update_item) && !empty($where_item)) {
        // SQLクエリを構築
        $sql_query = "UPDATE product SET " . implode(', ', $update_item) . " WHERE " . implode('AND ', $where_item);
        
        // SQL実行
        $sql_result = require(dirname(__DIR__) . '/db_php/sqlexec.php');

        // 失敗してたら終了
        if (!$sql_result) {return "error:P001";}
    } else {
        // $update_item が空の場合の処理
        return "error:更新する項目がありません。";
    }

    return true;
?>