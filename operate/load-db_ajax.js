/**
 * データベースからデータをロードする関数です。
 * @param {string} selectMode 処理モード
 * @param {Array} whereData 条件設定 - 省略可
 *   - where_dbn      : DB識別子
 *   - where_name     : 対象カラム名
 *   - where_operator : 演算子
 *   - where_value    : 値
 *   - where_conj     : 等位接続詞
 * @param {Array} sortData ソート設定 - 省略可
 *   - sort_dbn       : DB識別子
 *   - sort_name      : 対象カラム名
 *   - sort_value     : ソート順
 * @param {Array} limitData ページング設定 - 省略可
 *   - limit_line     : 取得行数
 *   - limit_offset   : オフセット行
 * @param {Array} whereDataEx 追加条件設定 - 省略可
 *   - whereEx_str    : 追加条件内容
 *   - whereEx_Conj   : 追加条件等位接続詞
 * @param {string} targetId 処理対象のID（省略可）
 */
function load_data_forDB(selectMode, whereData, sortData, limitData, whereDataEx, targetId) {
    // 初期化
    var setModeNumber = -1; // モード番号の初期化(-1)
    let showOperation = ""; // テーブル表示する際のJavaScript関数名の初期化("")

    // 処理モードの対象登録確認
    // operate.phpで取得したiniOpeDataRegistを使用
    for (var i = 0; i < iniOpeDataLoad.length; i++) {
        if (iniOpeDataLoad[i][0] === selectMode) {
            // 選択されたモードが見つかった場合、そのモード番号とメッセージを設定
            setModeNumber = i; // 処理モード番号設定
            showOperation = iniOpeDataLoad[i][1]; // テーブル表示する際のJavaScript関数名取得

            // 関数が存在するかどうかをチェック
            if (typeof window[showOperation] === "function") {
                //console.log("myFunction exists!");
            } else {
                // 関数が存在しなかった場合にアラートでエラー表示
                alert('正しい処理をするための条件がととのってません。');
                return;
            }
            break;
        }
    }

    // 対象の処理設定を発見できなかった場合の処理
    if (setModeNumber === -1) {
        alert('正しい処理が選択されませんでした。');
        return;
    }

    // デフォルトのwhereData
    var def_whereData = [[''], [''], [''], [''], ['']];
    // デフォルトのsortData
    var def_sortData = [[''], [''], ['']];
    // デフォルトのlimitData
    var def_limitData = ['', ''];
    // デフォルトのWhereDataEx
    var def_whereDataEx = [[''], ['']];

    // whereDataが省略された場合の処理
    if (typeof whereData === 'undefined') {
        // デフォルトの値を使用する
        whereData = def_whereData;
    } else {
        // whereDataを構成する要素数が5以外の場合の処理
        if (whereData.length != 5) {
            // whereDataに必要な要素をアラート表示(エラーチェック用)
            alert("whereDataの要素は、\nwhere_dbn:DB識別子\nwhere_name:対象カラム名\nwhere_operator:演算子\nwhere_value:値\nwhere_conj:等位接続詞が必要です");
            return;
        }
    }

    // sortDataが省略された場合の処理
    if (typeof sortData === 'undefined') {
        // デフォルトの値を使用する
        sortData = def_sortData;
    } else {
        // sortDataを構成する要素数が3以外の場合の処理
        if (sortData.length != 3) {
            // sortDataに必要な要素をアラート表示(エラーチェック用)
            alert("sortDataの要素は、\nsort_dbn:DB識別子\nsort_name:対象カラム名\nsort_value:ソート順が必要です");
            return;
        }
    }

    // limitDataが省略された場合の処理
    if (typeof limitData === 'undefined') {
        // デフォルトの値を使用する
        limitData = def_limitData;
    } else {
        // limitDataを構成する要素数が2以外の場合の処理
        if (limitData.length != 2) {
            // limitDataに必要な要素をアラート表示(エラーチェック用)
            alert("limitDataの要素は、\nlimit_line:行数\nlimit_offset:指定行が必要です");
            return;
        }
    }

    // limitDataが省略された場合の処理
    if (typeof whereDataEx === 'undefined') {
        // デフォルトの値を使用する
        whereDataEx = def_whereDataEx;
    } else {
        // whereDataExを構成する要素数が2以外の場合の処理
        if (whereDataEx.length != 2) {
            // whereDataExに必要な要素をアラート表示(エラーチェック用)
            alert("whereDataExの要素は、\nwhereEx_str:内容\nwhereEx_Conj:等位接続詞が必要です");
            return;
        }
    }

    // 初期化
    var ajax_data = {};

    // データセット
    ajax_data = {
        selectMode: selectMode, // 処理モード
        where_dbn: whereData[0], // Where-DB名
        where_name: whereData[1], // Where-要素名
        where_operator: whereData[2], // Where-演算子
        where_value: whereData[3], // Where-値
        where_conj: whereData[4], // Where-論理演算子
        sort_dbn: sortData[0], // Order-DB名
        sort_name: sortData[1], // Order-要素名
        sort_value: sortData[2], // Order-並び順値
        limit_line: limitData[0], // Limit-行数
        limit_offset: limitData[1], // Limit-オフセット値
        whereEx_str: whereDataEx[0], // WhereEx-設定したい式
        whereEx_Conj: whereDataEx[1], // Where-論理演算子
    };

    // AJAXリクエスト送信
    $.ajax({
        url: './../operate/load-db_ajax.php', // リクエスト先のURL
        type: 'GET', // リクエストタイプ
        cache: false, // キャッシュを無効化
        dataType: 'json', // データタイプ
        data: ajax_data, // 送信データ
    }).done(function(data) {
        // 処理モードに対応した処理の呼び出し
        switch (selectMode) {
            case 'zaiko-fa': // 在庫管理(顧客側用)
                window[showOperation](data, targetId);
                break;
            default: // 通常
                window[showOperation](data);
                break;
        }
    }).fail(function(data) {
        /* 通信失敗時 */
        alert('通信失敗！');
    });
}
