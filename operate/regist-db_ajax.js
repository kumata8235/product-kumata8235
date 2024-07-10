/**
 * regist_data_forDB - データ登録用関数
 * @param {string} selectMode - 処理モード
 * @param {FormData} ajax_data - 登録データ
 * @returns {Promise<boolean>} - データ登録の成功または失敗を示すPromise
 */
function regist_data_forDB(selectMode, ajax_data) {
    return new Promise((resolve, reject) => {
        // 初期値設定
        var setModeNumber = -1; // 処理モードの初期値(-1)設定
        let showOpeErrMsg = ""; // エラーメッセージの初期値("")設定
        let showOpeSucMsg = ""; // 成功メッセージの初期値("")設定

        // 処理モードの対象登録確認
        // operate.phpで取得したiniOpeDataRegistを使用
        for (var i = 0; i < iniOpeDataRegist.length; i++) {
            if (iniOpeDataRegist[i][0] === selectMode) {
                // 選択されたモードが見つかった場合、そのモード番号とメッセージを設定
                setModeNumber = i; // 処理モード番号設定
                showOpeSucMsg = iniOpeDataRegist[i][1]; // 成功メッセージ取得
                showOpeErrMsg = iniOpeDataRegist[i][2]; // エラーメッセージ取得
                break;
            }
        }

        // 対象の処理モードが発見できなかった場合の処理
        if (setModeNumber === -1) {
            // エラーメッセージをアラートで表示
            alert('正しい処理が選択されませんでした。');
            reject('正しい処理が選択されませんでした。');
            return;
        }

        // 処理モードをajaxに送るデータへ追加
        ajax_data.append('selectMode',selectMode);

        // AJAXリクエストの実行
        $.ajax({
            url: './../operate/regist-db_ajax.php', // リクエスト先のURL
            type: 'POST', // リクエストタイプ
            data: ajax_data, // 送信データ
            processData: false, // データの処理をjQueryに任せない
            contentType: false, // Content-Typeの設定をjQueryに任せない
            success: function(response) {
                // responseにerror:が含まれているか確認
                if (response.includes("error:")) {
                    // "error:" を区切り文字として、エラーコードを取得する
                    var errorCodes = response.split("error:").slice(1);
                    // エラーコードをスペースで結合して、指定されたフォーマットで表記する
                    var showErrCodes = "(" + errorCodes.join(" ") + ")";

                    // 処理モードに応じて分岐がある場合
                    switch (selectMode) {
                        default: // 通常
                            // エラーメッセージをアラートで表示
                            alert(showOpeErrMsg + showErrCodes);
                            break;
                    }
                    reject(showOpeErrMsg + showErrCodes); // Promiseを拒否
                } else {
                    switch (selectMode) {
                        default:
                            alert(showOpeSucMsg);
                            break;
                    }
                    resolve(true); // Promiseを解決
                }
            },
            error: function(xhr, status, error) {
                // エラーメッセージをアラートで表示
                alert(showOpeErrMsg);
                reject(showOpeErrMsg); // Promiseを拒否
            }
        });
    });
}
