<?php
    // このスクリプトは、現在のURLから operate フォルダへの相対パスを計算し、
    // 必要な JavaScript ファイルと INI ファイルのデータを読み込んで、HTML に埋め込みます。

    // 相対パスの初期化
    $setrelativePath  = "";

    // operateフォルダのパス
    $SearchRootFolder = "/operate/";

    // 現在のURLを取得
    $currentUrl = $_SERVER['REQUEST_URI'];

    // URLをパースしてパスを取得
    $urlParts = parse_url($currentUrl);
    $path = $urlParts['path'];

    // ドキュメントルートの絶対パスを取得
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];

    // パスを"/"で分割してディレクトリ配列を取得
    $directories = explode('/', $path);

    // 末尾の空の要素を削除（末尾がスラッシュで終わる場合）
    if (end($directories) === '') {
        array_pop($directories);
    }

    // operateフォルダが見つかるまでパスを上の階層に移動
    // ループ中断用のフラグ初期化
    $operateFolderFound = false;
    while (count($directories) > 0) {
        // パスを結合してディレクトリパスを作成
        $directoryPath = $documentRoot . '/' . implode('/', $directories);

        // operateフォルダが存在するか確認
        if (is_dir($directoryPath . $SearchRootFolder)) {
            // operateフォルダが見つかったらループを抜ける
            $operateFolderFound = true;
            break;
        }

        // 相対パスを上の階層に移動
        // 相対パスが空白でないことを確認
        if ($setrelativePath !== '') {
            // 空白でなければ階層を1つ上に上がるために'..'を追加
            $setrelativePath .= '..';
        } else {
            // 空白であれば現在の階層を表す'.'を追加
            $setrelativePath .= '.';
        }
        // 相対パスが空白でなければ'/'を追加
        if ($setrelativePath !== '') {$setrelativePath .= '/';}

        // ディレクトリ配列から最後の要素を削除して、上の階層に移動
        array_pop($directories);
    }

    // 絶対パスからHTMLファイルからの相対パスに変換する関数
    function convertToRelativePath($absolutePath, $htmlFolderPath) {
        $relativePath = str_replace($htmlFolderPath, '', $absolutePath);
        // 念のため先頭のスラッシュを削除
        return ltrim($relativePath, '/');
    }

    // operateフォルダが見つかった場合のみ処理を行う
    if ($operateFolderFound) {
        // 読込ファイルの設定
        $htmlFolderPath = implode('/', $directories);

        // JavaScriptファイルのパス設定
        $setRootPath = $setrelativePath . $SearhRootFolder;
        $load_db = $setRootPath . "load-db_ajax.js"; // 読込制御用のJSファイルへのパス設定
        $regist_db = $setRootPath . "regist-db_ajax.js"; // 書込制御用のJSファイルへのパス設定

        // INIファイル(処理モードごとの設定内容)の読込
        $ini_array = parse_ini_file(__DIR__ . "/operate.ini", true);

        // AJAXを使用しての読込用の処理内容を取得
        $iniOpeDataLoad = array();
        foreach ($ini_array['opeLoad'] as $key => $value) {
            $iniOpeDataLoad[] = $value;
        }

        // Smartyを使用しての読込用の処理内容を取得
        $iniOpeDataLoadsmarty = array();
        foreach ($ini_array['opeLoadsmarty'] as $key => $value) {
            $iniOpeDataLoadsmarty[] = $value;
        }

        // AJAXを使用しての書込用の処理内容を取得
        $iniOpeDataRegist = array();
        foreach ($ini_array['opeRegist'] as $key => $value) {
            $iniOpeDataRegist[] = $value;
        }
?>
         <!-- JavaScriptファイルを読み込み -->
        <script src="<?php echo $load_db; ?>"></script>
        <script src="<?php echo $regist_db; ?>"></script>
        <script>
            // PHPからJavaScriptへデータを移す
            var iniOpeDataLoad = <?php echo json_encode($iniOpeDataLoad)?>;
            var iniOpeDataRegist = <?php echo json_encode($iniOpeDataRegist)?>;
            var iniOpeDataLoadsmarty = <?php echo json_encode($iniOpeDataLoadsmarty)?>;
        </script>
<?php
    } else {
        // operateフォルダが見つからなかった場合のメッセージ
        echo "operateフォルダが見つかりませんでした。";
    }
?>