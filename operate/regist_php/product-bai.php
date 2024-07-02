<?php
    try {
    //商品IDの新規発行
    //SQL定義
    $sql_query_uid = "UPDATE createdid SET product_id = LAST_INSERT_ID(product_id+1)";
    $sql_query_sid = "SELECT product_id FROM createdid";
    // SQL実行
    $sql_result = require(dirname(__DIR__) . '/db_php/sqlexec_for-id.php');
    
    // 失敗してたら終了
    if (!$sql_result) {return "error:C001";}

    //商品IDの設定
    $product_id = "pr-". sprintf("%07d", $result_id['product_id']);

    $product_file = "ninja-" . $product_id;

    // ファイルがアップロードされた？
    if(isset($_FILES['UploadFile']['name'])&&$_FILES['UploadFile']['error'] == 0) {
        // 拡張子の取得
        $extension = pathinfo($_FILES['UploadFile']['name'], PATHINFO_EXTENSION);
        $newFileName = $product_file . "." . $extension;
        $target_dir = dirname(dirname(__dir__)) . "/ninja/img_course/";
        $target_file = $target_dir . $newFileName;

        // 万が一存在していたら削除
        if (file_exists($target_file)) {
            unlink($target_file);
        }

        if (move_uploaded_file($_FILES['UploadFile']['tmp_name'], $target_file)) {
            // アップロードが成功したら新しいファイル名を設定
            $picture = $newFileName;
        } else {
            // アップロードに失敗した場合はエラー処理を行う
            return "error:F001";
        }
    } else {
        // デフォルト設定
        $picture = "no_image.jpg";
    }

    $rstart = "";
    // リリース日
    if (isset($_POST['release_date'])) {
        $update_item[] = "release_date=:release_date";
        if ($_POST['release_date'] === '0000-00-00' || $_POST['release_date'] === '') {
            $rstart = null;
        } else {
            $rstart = $_POST['release_date'];
        }
    }

    $rend = "";
    // リリース終了日
    if (isset($_POST['release_end'])) {
        $update_item[] = "release_end=:release_end";
        if ($_POST['release_end'] === '0000-00-00' || $_POST['release_end'] === '') {
            $rend = null;
        } else {
            $rend = $_POST['release_end'];
        }
    }

    //商品DB登録
    $sql_query = "INSERT INTO product (`product_id`, `name`, `play_type`, `play_venue`, `play_days`, `play_story`, `release_date`, `release_end`, `price`, `picture`, `createdAt`)
                VALUES (:product_id,:name,:play_type,:play_venue,:play_days,:play_story,:release_date,:release_end,:price,:picture,NOW())";
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
   
    return true;
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>