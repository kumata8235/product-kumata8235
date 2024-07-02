//*************************************************//
//  regist_data_forDB - データ登録用関数
//*************************************************//
//  selectMode : 処理モード
//  ajax_data  : 登録データ(FormData)
//*************************************************//

function regist_data_forDB(selectMode, ajax_data) {
    return new Promise((resolve, reject) => {
        var setModeNumber = -1;
        let showOpeErrMsg;
        let showOpeSucMsg;

        // 処理対象確認
        for (var i = 0; i < iniOpeDataRegist.length; i++) {
            if (iniOpeDataRegist[i][0] === selectMode) {
                setModeNumber = i;
                showOpeSucMsg = iniOpeDataRegist[i][1];
                showOpeErrMsg = iniOpeDataRegist[i][2];
                break;
            }
        }

        // 発見できなかった
        if (setModeNumber === -1) {
            alert('正しい処理が選択されませんでした。');
            reject('正しい処理が選択されませんでした。');
            return;
        }

        // 処理モードの追加
        ajax_data.append('selectMode',selectMode);

        $.ajax({
            url: './../operate/regist-db_ajax.php',
            type: 'POST',
            data: ajax_data,
            processData: false,
            contentType: false,
            success: function(response) {
                // responseにerror:が含まれる？
                if (response.includes("error:")) {
                    // "error:" を区切り文字として、エラーコードを取得する
                    var errorCodes = response.split("error:").slice(1);
                    // エラーコードをスペースで結合して、指定されたフォーマットで表記する
                    var showErrCodes = "(" + errorCodes.join(" ") + ")";

                    switch (selectMode) {
                        case 'reservation-fai':
                            // 在庫数が足りなかった場合
                            if (errorCodes.includes('Z002')) {
                                // ユーザーに在庫不足の通知を表示
                                alert('大変申し訳ございません。ご希望の数量をご用意することができませんでした。');
                            } else {
                                alert(showOpeErrMsg + showErrCodes);
                            }
                            break;
                        default:
                            alert(showOpeErrMsg + showErrCodes);
                            break;
                    }
                    reject(showOpeErrMsg + showErrCodes);
                } else {
                    switch (selectMode) {
                        case 'reservation-fai': //何もしない
                            break;
                        default:
                            alert(showOpeSucMsg);
                            break;
                    }
                    resolve(true);
                }
            },
            error: function(xhr, status, error) {
                alert(showOpeErrMsg);
                reject(showOpeErrMsg);
            }
        });
    });
}
