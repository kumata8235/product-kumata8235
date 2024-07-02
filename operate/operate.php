<?php
    $setrelativePath  = "";

    // operateフォルダの取得
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
        if ($setrelativePath !== '') {
            $setrelativePath .= '..';
        } else {
            $setrelativePath .= '.';
        }
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

        $setRootPath = $setrelativePath . $SearchRootFolder;
        $load_db = $setRootPath . "load-db_ajax.js";
        $regist_db = $setRootPath . "regist-db_ajax.js";

        $ini_array = parse_ini_file(__DIR__ . "/operate.ini", true);

        $iniOpeDataLoad = array();
        foreach ($ini_array['opeLoad'] as $key => $value) {
            $iniOpeDataLoad[] = $value;
        }

        $iniOpeDataLoadsmarty = array();
        foreach ($ini_array['opeLoadsmarty'] as $key => $value) {
            $iniOpeDataLoadsmarty[] = $value;
        }

        $iniOpeDataRegist = array();
        foreach ($ini_array['opeRegist'] as $key => $value) {
            $iniOpeDataRegist[] = $value;
        }
?>
        <script src="<?php echo $load_db; ?>"></script>
        <script src="<?php echo $regist_db; ?>"></script>
        <script>
            var iniOpeDataLoad = <?php echo json_encode($iniOpeDataLoad)?>;
            var iniOpeDataRegist = <?php echo json_encode($iniOpeDataRegist)?>;
            var iniOpeDataLoadsmarty = <?php echo json_encode($iniOpeDataLoadsmarty)?>;
        </script>
<?php
    } else {
        echo "operateフォルダが見つかりませんでした。";
    }
?>