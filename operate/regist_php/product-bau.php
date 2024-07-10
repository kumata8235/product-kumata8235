<?php
    // 商品情報を更新するスクリプト

    // 更新フィールドと値の配列を初期化します
    $update_item = array();
    $where_item = array();

    // 対象条件の設定
    // 更新対象の商品ID設定
    if (isset($_POST['product_id'])) {
        // 更新対象を指定するためにWhere句を設定
        $where_item[] = "product_id=:product_id";
        $sql_params[':product_id'] = $_POST['product_id'];
        $product_id = $_POST['product_id'];
    }

    // 更新フィールドの設定
    // ポストされている場合のみ処理する-すべて
    // 商品名
    if (isset($_POST['name'])) {
        $update_item[] = "name=:name"; // 更新項目の追加
        $sql_params[':name'] = $_POST['name']; // パラメーターの追加
    }

    // プレイ形式
    if (isset($_POST['play_type'])) {
        $update_item[] = "play_type=:play_type"; // 更新項目の追加
        $sql_params[':play_type'] = $_POST['play_type']; // パラメーターの追加
    }

    // 会場
    if (isset($_POST['play_venue'])) {
        $update_item[] = "play_venue=:play_venue"; // 更新項目の追加
        $sql_params[':play_venue'] = $_POST['play_venue']; // パラメーターの追加
    }

    // 開催日
    if (isset($_POST['play_days'])) {
        $update_item[] = "play_days=:play_days"; // 更新項目の追加
        $sql_params[':play_days'] = $_POST['play_days']; // パラメーターの追加
    }

    // ストーリー
    if (isset($_POST['play_story'])) {
        $update_item[] = "play_story=:play_story"; // 更新項目の追加
        $sql_params[':play_story'] = $_POST['play_story']; // パラメーターの追加
    }

    // リリース日
    if (isset($_POST['release_date'])) {
        $update_item[] = "release_date=:release_date"; // 更新項目の追加
        if ($_POST['release_date'] === '0000-00-00' || $_POST['release_date'] === '') {
            // リリース日が設定されていなかったり、0000-00-00の場合はNULLを設定
            $sql_params[':release_date'] = null; // パラメーターの追加
        } else {
            $sql_params[':release_date'] = $_POST['release_date']; // パラメーターの追加
        }
    }

    // リリース終了日
    if (isset($_POST['release_end'])) {
        $update_item[] = "release_end=:release_end"; // 更新項目の追加
        if ($_POST['release_end'] === '0000-00-00' || $_POST['release_end'] === '') {
            // リリース終了日が設定されていなかったり、0000-00-00の場合はNULLを設定
            $sql_params[':release_end'] = null; // パラメーターの追加
        } else {
            $sql_params[':release_end'] = $_POST['release_end']; // パラメーターの追加
        }
    }

    // 金額
    if (isset($_POST['price'])) {
        $update_item[] = "price=:price"; // 更新項目の追加
        $sql_params[':price'] = $_POST['price']; // パラメーターの追加
    }

    // deltdeAt
    if (isset($_POST['deletedAt'])) {
        $update_item[] = "deletedAt=NOW()"; // 更新項目の追加
    }

    // 商品画像
    if (isset($_FILES['UploadFile']['name']) && $_FILES['UploadFile']['error'] == 0) {
        // 新しいファイルがアップロードされている場合の処理
        // product_idからファイル名生成
        $product_file = "ninja-" . $product_id;
        // アップロードされたファイルの拡張子を取得
        $extension = pathinfo($_FILES['UploadFile']['name'], PATHINFO_EXTENSION);
        $newFileName = $product_file . "." . $extension; // ファイル名+拡張子でファイル名生成
        $target_dir = dirname(dirname(__dir__)) . "/ninja/img_course/"; // アップロードされたファイルの移動先指定
        $target_file = $target_dir . $newFileName; // ファイルパスを生成

        // アップロードされたファイルと同名のファイル有無のチェック
        $files = glob($target_dir . DIRECTORY_SEPARATOR . basename($product_file) . '.*');

        // 既存のファイルをすべて削除
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // 新しいファイルを移動
        if (move_uploaded_file($_FILES['UploadFile']['tmp_name'], $target_file)) {
            // ファイル移動時に、新しいファイル名を設定
            $picture = $newFileName;
            // ファイル名をDB登録する
            $update_item[] = "picture=:picture"; // 更新項目の追加
            $sql_params[':picture'] = $picture; // パラメーターの追加
        } else {
            // アップロード失敗時のエラー処理
            return "error:F001";
        }
    }

    // 更新項目と対象条件があるかチェック
    if (!empty($update_item) && !empty($where_item)) {
        // SQLクエリを構築
        $sql_query = "UPDATE product SET " . implode(', ', $update_item) . " WHERE " . implode(' AND ', $where_item);
        
        // SQL実行
        $sql_result = require(dirname(__DIR__) . '/db_php/sqlexec.php');

        // 失敗した場合はエラーを返す
        if (!$sql_result) {
            return "error:P001";
        }
    } else {
        // $update_itemが空の場合の処理
        return "error:更新する項目がありません。";
    }

    return true; // 更新成功を示す
?>
