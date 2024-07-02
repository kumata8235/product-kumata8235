//*************************************************//
//  load_data_forDB - データロード用関数
//*************************************************//
//  selectMode : 処理モード
//  whereData  : 条件設定 - 省略可
//   - where_dbn      : DB識別子
//   - where_name     : 対象カラム名
//   - where_operator : 演算子
//   - where_value    : 値
//   - where_conj     : 等位接続詞
//  sortData   : ソート設定 - 省略可
//   - sort_dbn       : DB識別子
//   - sort_name      : 対象カラム名
//   - sort_value     : ソート順
//*************************************************//

function load_data_forDB (selectMode,whereData,sortData,limitData,whereDataEx,targetId) {

    var setModeNumber = -1;
    let showOperation;

    // 処理対象確認
    for (var i = 0; i < iniOpeDataLoad.length; i++) {
        if (iniOpeDataLoad[i][0] === selectMode) {
            setModeNumber = i;
            showOperation = iniOpeDataLoad[i][1];

            // 関数が存在するかどうかをチェック
            if (typeof window[showOperation] === "function") {
                //console.log("myFunction exists!");
            } else {
                alert('正しい処理をするための条件がととのってません。')
                return;
            }
            break;
        }
    }

    // 発見できなかった
    if (setModeNumber===-1) {
        alert('正しい処理が選択されませんでした。');
        return;
    }

    // デフォルトのwhereData
    var def_whereData = [[''], [''], [''] , [''] , ['']];
    // デフォルトのsortData
    var def_sortData = [[''], [''], ['']];
    // デフォルトのlimitData
    var def_limitData = ['', '']
    // デフォルトのWhereDataEx
    var def_whereDataEx = [[''], ['']]

    // whereDataが省略された場合、デフォルトの値を使用する
    if (typeof whereData === 'undefined') {
        whereData = def_whereData;
    } else {
        // whereDataの要素数が5以外の場合はエラー
        if (whereData.length != 5) {
            alert("whereDataの要素は、\nwhere_dbn:DB識別子\nwhere_name:対象カラム名\nwhere_operator:演算子\nwhere_value:値\nwhere_conj:等位接続詞が必要です");
            return;
        }
    }

    // sortDataが省略された場合、デフォルトの値を使用する
    if (typeof sortData === 'undefined') {
        sortData = def_sortData;
    } else {
        // sortDataの要素数が3以外の場合はエラー
        if (sortData.length != 3) {
            alert("sortDataの要素は、\nsort_dbn:DB識別子\nsort_name:対象カラム名\nsort_value:ソート順が必要です");
            return;
        }
    }

    // limitDataが省略された場合、デフォルトの値を使用する
    if (typeof limitData === 'undefined') {
        limitData = def_limitData
    } else {
        // limitDataの要素数が2以外の場合はエラー
        if (limitData.length != 2) {
            alert("limitDataの要素は、\nlimit_line:行数\nlimit_offset:指定行が必要です");
            return;
        }
    }

    // whereDataExが省略された場合、デフォルトの値を使用する
    if (typeof whereDataEx === 'undefined') {
        whereDataEx = def_whereDataEx
    } else {
        // whereDataExの要素数が2以外の場合はエラー
        if (whereDataEx.length != 2) {
            alert("whereDataExの要素は\nwhereEx_str:内容\nwhereEx_Conj:等位接続詞が必要です");
            return;
        }
    }
    
    // 初期化
    var ajax_data = {};

    // データセット
    ajax_data = {
                    selectMode:selectMode,
                    where_dbn:whereData[0],
                    where_name:whereData[1],
                    where_operator:whereData[2],
                    where_value:whereData[3],
                    where_conj:whereData[4],
                    sort_dbn:sortData[0],
                    sort_name:sortData[1],
                    sort_value:sortData[2],
                    limit_line:limitData[0],
                    limit_offset:limitData[1],
                    whereEx_str:whereDataEx[0],
                    whereEx_Conj:whereDataEx[1],
                }
                
    $.ajax({
        url: './../operate/load-db_ajax.php',
        type: 'GET',
        cache: false,
        dataType: 'json',
        data : ajax_data,
    }).done(function(data){
        // 処理モードに対応した処理の呼び出し
        switch (selectMode) {
            case 'zaiko-fa':
                window[showOperation](data,targetId);
                break;
            default:
                window[showOperation](data);
                break;
        }
    }).fail(function(data){
        /* 通信失敗時 */
        alert('通信失敗！');               
    });
}