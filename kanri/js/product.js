// 現表示フラグ
var show_dataFlgConte;
var show_euFlg;
var selectFileFlg;
var registChkFlg;

//バリデーションチェック
//背景色設定用
var flgOK_bgc = 'rgb(162, 215, 221)' //瓶覗
var flgNG_bgc = 'rgb(242, 160, 161)' //紅梅色
var flgNw_bgc = 'rgb(255, 255, 255)' //初期色
var flgup_bgc = 'rgb(233, 228, 212)' //初期編集色

// 各種設定
var setWhereData= [[''],[''],[''],[''],['']];
var setOrderData= [[''],[''],['']];
var setLimitData= ['',''];
var setWhereExData= [[''],['']];

//読み込み時
document.addEventListener("DOMContentLoaded", function() { 
    show_dataFlgConte = 0;   
    load_data_forProduct('def');
});

// ロード
function load_data_forProduct(readFlg) {

    // 読込内容分岐
    switch (readFlg) {
        case 'def':
        case 'del':
            // 基本データ
            if (readFlg === 'def') {
                setWhereData = [['p'],['deletedAt'],['IS NULL'],[''],['']];
            } else {
                setWhereData = [['p'],['deletedAt'],['IS NOT NULL'],[''],['']];
            }
            // 初期化
            setOrderData = [[''],[''],['']];
            setLimitData = ['',''];
            setWhereExData = [[''],['']];
            break;
        case 'reload':
            // 何もかえない
            break;
        default:
            retrun;
    }

    load_data_forDB('product-ba',setWhereData,setOrderData,setLimitData,setWhereExData);
}

// 削除済み一覧/商品一覧に戻るボタンがクリックされたときの処理
$(document).on("click", ".button__item", function(event) {
    // クリックされたボタンのIDを取得
    var setId = $(this).attr("id");

    // ID分岐
    switch (setId) {
        case "button_def":
            show_dataFlgConte = 1; 
            load_data_forProduct('del');

            // ID変更
            $(this).attr("id", "button_back");
            $(this).text("商品一覧に戻る");
            break;
        case "button_back":
            show_dataFlgConte = 0; 
            load_data_forProduct('def');

            // ID変更
            $(this).attr("id", "button_def");
            $(this).text("削除済み一覧");
            break;
        case "button_new":
            var imageSrc = "./../ninja/img_course/no_image.jpg";
            vm.displayImage(imageSrc);
            vm.openModal(0);            
            break;
        case "button_edit":
            // クリックされたボタンのdata-item属性を取得
            var itemCode = $(this).data("item");
            var row = $(this).closest('tr'); // 編集する行を取得

            // フォームにデータをセット
            $('#product_id').val(itemCode)
            $('#product_id').css('background-color', flgup_bgc);
            $('#product_name').val(row.find('td:eq(1)').text())
            $('#product_name').css('background-color', flgup_bgc);
            // テーブルから画像のsrc属性を取得する
            var imageSrc = row.find('td:eq(2) img').attr('src');
            if (imageSrc.slice(-1) === '/') {imageSrc += "no_image.jpg"};
            vm.displayImage(imageSrc);
            $('#product_type').val(row.find('td:eq(3)').text())
            $('#product_type').css('background-color', flgup_bgc);
            $('#product_venu').val(row.find('td:eq(4)').text())
            $('#product_venu').css('background-color', flgup_bgc);
            $('#product_days').val(row.find('td:eq(5)').text())
            $('#product_days').css('background-color', flgup_bgc);
            $('#product_price').val(addFormatStr(row.find('td:eq(7)').text(),'bmoney'));
            $('#product_price').css('background-color', flgup_bgc);
            var data = row.find('td:eq(8)').text(); // 例えば "rstart/rend"
            // '/'を区切り文字としてデータを分割
            var dataArray = data.split('/');
            $('#product_rstart').val(dataArray[0]); // rstart
            $('#product_rstart').css('background-color', flgup_bgc);
            $('#product_rend').val(dataArray[1]); // rend
            $('#product_rend').css('background-color', flgup_bgc);
            $('#product_story').val(row.find('td:eq(6)').text())
            $('#product_story').css('background-color', flgup_bgc);
            vm.openModal(1);
            break;
        case "button_search":
            vms.openModal();
            break;
        case "button_close_ne":
            vm.closeModal();
            event.preventDefault();
            break;
        case "button_close_s":
            vms.closeModal();
            event.preventDefault();
            break;
        case "button_submit_es":
            break;
        case "button_submit_s":
            Search_Data();
            break;
        case "button_del": 
            // クリックされたボタンのdata-item属性を取得
            var itemCode = $(this).data("item");

            // 確認ダイアログを表示
            var confirmDelete = confirm("商品ID:" + itemCode + "を本当に削除しますか？");

            if (confirmDelete) {
                var setData = new FormData();

                // データセット
                setData.append('product_id', itemCode);
                setData.append('deletedAt',"");

                registChkFlg = false;
                regist_data_forDB('product-bau', setData)
                .then(function(registChkFlg) {
                    if (registChkFlg === true) {
                        load_data_forProduct("reload");
                    }
                })
                .catch(function(error) {
                    console.error('エラーが発生しました:', error);
                });
            }
            break;
        case "button_clear":
            ClearSearch();
            break;
        default:
            // 規定外のIDだった場合
            break;
    }
});
   
function showtableforproduct(data) {
    var html_response = '';

    html_response += '<thead>';
    html_response += '<tr>';
    html_response += '<th class="th__proid">商品ID</th>';
    html_response += '<th class="th__pronam">商品名</th>';
    html_response += '<th class="th__proimg">商品イメージ</th>';
    html_response += '<th class="th__protyp">商品タイプ</th>';
    html_response += '<th class="th__proven">開催場所</th>';
    html_response += '<th class="th__proday">開催日</th>';
    html_response += '<th class="th__prostr">ストーリー</th>';
    html_response += '<th class="th__propri">価格</th>';
    html_response += '<th class="th__prorel">リリース日/終了日</th>';
    html_response += '<th class="th__prodat">登録日時</th>';

    // テーブル表示の変更
    if (show_dataFlgConte===1) {
        html_response += '<th class="th__prodat">削除日時</th>';
    } else {
        html_response += '<th class="th__prosubj">処&emsp;&emsp;理</th>';
    }
    html_response += '</tr>';
    html_response += '</thead>';

    html_response += '<tbody>';

    if (data.length === 0) {
        html_response += '<tr><td colspan="10" class="td__center" style="width:100%">表示する内容がありません</td></tr>';
    } else {
        $.each(data, function(key, value) {
            html_response += '<tr>';
            html_response += '<td class="td__center">' + value.product_id + '</td>';
            html_response += '<td>' + value.name + '</td>';
            html_response += '<td class="td__center"><img class="table_imgbox" src="./../ninja/img_course/' + value.picture + '?' + new Date().getTime() + '"></td>';
            html_response += '<td>' + value.play_type + '</td>';
            html_response += '<td>' + value.play_venue + '</td>';
            html_response += '<td>' + value.play_days + '</td>';
            html_response += '<td class="td__none">' + value.play_story + '</td>';
            html_response += '<td class="td__right">' + addFormatStr(value.price,'money') + '</td>';
            html_response += '<td class="td__right">' + (value.release_date ? value.release_date : '未定') + '/' + (value.release_end ? value.release_end : '未定') + '</td>';
            html_response += '<td class="td__center">' + addFormatStr(value.createdAt,'date') + '</td>';
            // テーブル表示の変更
            if (show_dataFlgConte===1) {
                html_response += '<td class="td__center">' + addFormatStr(value.deletedAt,'date') + '</td>';
            } else {
                html_response += '<td class="td__center"><div class="button__wrap-table">';
                html_response += '<button class="button__item" id="button_edit" data-item="'+ value.product_id +'">編&emsp;&emsp;集</button>';
                html_response += '<button class="button__item" id="button_del" data-item="'+ value.product_id +'">削&emsp;&emsp;徐</button></div></td>';
            }        
            html_response += '</tr>';
        });
    }

    html_response += '</tbody>';

    $('.showitem').html(html_response); //取得したHTMLを.resultに反映
}

function addFormatStr(strValue,switchFlg) {
    var strReturn = "";

    switch(switchFlg) {
        case 'date':
            // サンプルの日付文字列
            var dateString = strValue;
            // Dateオブジェクトに変換
            var dateObject = new Date(dateString);
            // 日付の取得
            var date = dateObject.toLocaleDateString();
            // 時刻の取得
            var time = dateObject.toLocaleTimeString();

            strReturn = date + '</br>' + time;
            break;
        case 'money':
            // 数字を文字列に変換してカンマを挿入
            let numMoney = strValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            // 円記号を追加して返す
            strReturn =  numMoney + "円";
            break;
        case 'bmoney':
            // 数字のみを抽出する
            var numericValue = parseFloat(strValue.replace(/[^\d.-]/g, ''));
            // NaNでない場合、数値を返す
            if (!isNaN(numericValue)) {
                strReturn += numericValue;
            }
            break;
        default:
            strReturn = "";
    }

    return strReturn;
}

// モーダルが閉じられたときの処理
$('#popup_edit').on('hidden.bs.modal', function (e) {
    // フォームをリセットする
    $('#registrationForm')[0].reset();
});


// フォームが送信されたときの処理
$('#registrationForm').submit(function(event) {
    event.preventDefault(); // デフォルトの送信をキャンセル

    // ここでフォームの内容を処理するか、必要に応じてサーバーに送信する
});
$('#serachForm').submit(function(event) {
    event.preventDefault(); // デフォルトの送信をキャンセル

    // ここでフォームの内容を処理するか、必要に応じてサーバーに送信する
});

Vue.component('adjustable-modal', {
    template: `
    <div class="adjustable-modal"
        v-show="isOpen"
        ref="elModal"
        :style="{
            width: \`\${width}px\`,
            height: \`\${height}px\`,
            transform: \`translate3d(\${pos.x}px, \${pos.y}px, 0)\`,
        }">
        <div class="adjustable-modal__header" @mousedown="onMoveDragStart">
            <div class="header">
                <div class="header__title">{{ title }}</div>
                <div class="header__close" @click="$emit('close')"></div>
            </div>
        </div>
        <div class="adjustable-modal__content">
            <slot></slot>
        </div>
    </div>
    `,
    props: {
        isOpen: { type: Boolean },
        title: { type: String },
        initialWidth: { type: Number },
        initialHeight: { type: Number },
    },
    data() {
        return {
            width: this.initialWidth,
            height: this.initialHeight,
            pos: {
                x: (window.innerWidth - this.initialWidth) / 2,
                y: (window.innerHeight - this.initialHeight) / 2,
            },
            isMoveDragging: false,
        };
    },
    mounted() {
        document.addEventListener('mousemove', this.onDrag);
        document.addEventListener('mouseup', this.onDragEnd);
    },
    beforeDestroy() {
        document.removeEventListener('mousemove', this.onDrag);
        document.removeEventListener('mouseup', this.onDragEnd);
    },
    methods: {
        onMoveDragStart(event) {
            event.preventDefault();
            event.stopPropagation();

            this.isMoveDragging = true;
            this.dragStartX = event.clientX;
            this.dragStartY = event.clientY;
            this.startClientRect = {
                x: this.pos.x,
                y: this.pos.y,
                width: this.width,
                height: this.height,
            };
        },
        onDrag(event) {
            if (this.dragStartX == null || this.dragStartY == null || this.startClientRect == null) {
                return;
            }

            if (this.isMoveDragging) {
                this.pos.x = _.clamp(
                    this.startClientRect.x + (event.clientX - this.dragStartX),
                    0,
                    window.innerWidth - this.width
                );
                this.pos.y = _.clamp(
                    this.startClientRect.y + (event.clientY - this.dragStartY),
                    0,
                    window.innerHeight - this.height
                );
            }
        },
        onDragEnd() {
            this.isMoveDragging = false;
            this.dragStartX = null;
            this.dragStartY = null;
            this.startClientRect = null;
        },
    },
});

var vm = new Vue({
    el: '#edit',
    data() {
      return {
        modalIsOpen: false, // モーダルが最初は非表示
        modalTitle: '商品管理',
      };
    },
    mounted() {
        window.addEventListener('resize', this.calculateModalSize);
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.calculateModalSize);
    },
    methods: {
      calculateModalSize() {
          const modalElement = document.querySelector('.adjustable-modal');
          if (modalElement) {
              const screenWidth = window.innerWidth;
              const screenHeight = window.innerHeight;
              const modalWidth = screenWidth * 0.8; // 画面幅の80%
              const modalHeight = screenHeight * 0.8; // 画面高さの80%
              modalElement.style.width = modalWidth + 'px';
              modalElement.style.height = modalHeight + 'px';
          }
      },
      openModal(enFlg) {
        switch(enFlg) {
            case 0: // 新規
                this.modalTitle = '商品管理 / 新規追加';
                show_euFlg = 0;
                break;
            case 1: // 編集
                this.modalTitle = '商品管理 / 編集';
                show_euFlg = 1;
                break;
            default:
                this.modalTitle = '商品管理';
                show_euFlg = -1;
                break;
        }

        selectFileFlg = 0;
        this.modalIsOpen = true; // モーダルを表示
        document.getElementById("overlay").style.display = "block";
        //this.$nextTick(this.calculateModalSize); // モーダルが表示された後にサイズを調整

      },
      submitForm() {
        // 送信チェック
        var submitCheck = submitInputcheck();
        var inputBoxData = document.querySelectorAll('.input_edit');
        var selectedValue;
        
        var formData = new FormData();

        // 送信チェックがOKでない場合、処理を終了
        if (!submitCheck) {
            return;
        }

        switch(show_euFlg) {
            case 0:
                // すべての内容を確認の上、発注しますか？の確認メッセージを表示
                var confirmed = confirm('すべての内容を確認の上、登録しますか？');
                selectedValue = "product-bai";
                break;
            case 1:
                // すべての内容を確認の上、発注しますか？の確認メッセージを表示
                var confirmed = confirm('すべての内容を確認の上、更新しますか？');
                selectedValue = "product-bau";
                break;
            default:
                alaert('不明な処理です');
                return;
        }

        if (!confirmed) {
            return; // 発注しない場合は処理を終了
        }
                
        // お客様情報の入力を取得してフォームデータに追加
        inputBoxData.forEach(function(userInputBox) {
            var inputName = userInputBox.getAttribute('name');
            var inputValue = userInputBox.value;
            var backgroundColor = window.getComputedStyle(userInputBox).getPropertyValue('background-color');

            switch(userInputBox.id) {
                case "product_id":
                    // 処理内容は更新？
                    if (show_euFlg===1) {
                        formData.append(inputName, inputValue);
                    }                    
                    break;                
                case "selectpic":
                    break; // 何もしない
                default:
                    // 新規または更新がある場合のみ追加
                    if ((show_euFlg === 1 && backgroundColor === flgOK_bgc) || show_euFlg === 0) {
                        formData.append(inputName, inputValue);
                    }
                    break;
            }
        });

        const fileInput = document.getElementById('selectpic');
        const files = fileInput.files;
        
        // ファイルが選ばれている？
        if (files.length > 0) {
            // ファイルが選択されている場合の処理
            const file = files[0];
            formData.append('UploadFile',file);
        }

        registChkFlg = false;
        regist_data_forDB(selectedValue, formData)
        .then(function(registChkFlg) {
            if (registChkFlg === true) {
                vm.closeModal();
                load_data_forProduct("reload");
            }
        })
        .catch(function(error) {
            console.error('エラーが発生しました:', error);
        });
      },
      closeModal() {
        this.modalIsOpen = false; // モーダルを閉じる
        document.getElementById("overlay").style.display = "none";

        // 各要素の値を初期化
        $('#product_id').val('');
        $('#product_id').css('background-color', flgNw_bgc);
        $('#product_name').val('');
        $('#product_name').css('background-color', flgNw_bgc);
        $('#product_type').val('');
        $('#product_type').css('background-color', flgNw_bgc);
        $('#product_venu').val('');
        $('#product_venu').css('background-color', flgNw_bgc);
        $('#product_days').val('');
        $('#product_days').css('background-color', flgNw_bgc);
        $('#product_price').val('');
        $('#product_price').css('background-color', flgNw_bgc);
        $('#product_rstart').val('');
        $('#product_rstart').css('background-color', flgNw_bgc);
        $('#product_rend').val('');
        $('#product_rend').css('background-color', flgNw_bgc);
        $('#product_story').val('');
        $('#product_story').css('background-color', flgNw_bgc);

        // イメージ要素の初期化
        $('#inputwindow__imgitem').html('');
        $('#selectpic').val('');
        this.selectedFile = null;
      },
      handleFileInputChange(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
              const imagePath = reader.result;
              this.displayImage(imagePath);
            };
            reader.readAsDataURL(file);

            selectFileFlg = 1;
        } else {

        }
      },
      displayImage(imagePath) {
        const img = new Image();
        img.src = imagePath;
  
        img.onload = () => {
          const imageContainer = document.getElementById('inputwindow__imgitem');
          imageContainer.innerHTML = '';
          imageContainer.appendChild(img);
  
          // 画像のプレビューサイズを更新する
          this.updateImagePreviewSize();
        };
      },
      updateImagePreviewSize() {
        const imageContainer = document.getElementById('inputwindow__imgitem');
        const img = imageContainer.querySelector('img');
        
        if (img) {
          const containerWidth = imageContainer.offsetWidth;
          const containerHeight = imageContainer.offsetHeight;
          const imageAspectRatio = img.width / img.height;
          const containerAspectRatio = containerWidth / containerHeight;
  
          if (imageAspectRatio > containerAspectRatio) {
            // 画像のアスペクト比がコンテナのアスペクト比より大きい場合
            img.style.width = containerWidth + 'px';
            img.style.height = 'auto';
          } else {
            // 画像のアスペクト比がコンテナのアスペクト比より小さい場合
            img.style.width = 'auto';
            img.style.height = containerHeight + 'px';
          }
        }
      },
      handleInputChange(event, inputName, inputType) {
        var inputValue = event.target.value.trim();
        var inputDataName = event.target.name;
        var inputBoxId = event.target.id;
        var inputErrId = inputBoxId.replace('box','err');
        var inputErrBox = document.getElementById(inputErrId);
        var errors = '';
  
        // 入力された値をチェック
        // 未入力チェック
        if (inputValue === '' && event.target.hasAttribute('required')) {
          // NG色設定
          event.target.style.backgroundColor = flgNG_bgc;
          // エラーメッセージの設定
          errors = inputDataName + 'が未入力です。';
        } else {
          switch (inputDataName) {
            case "price":
                // 価格のチェック
                switch (isValidNumber(inputValue,3)) {
                    case 0:
                    case 1:
                        event.target.style.backgroundColor = flgOK_bgc;
                        break;
                    case -99:
                        // バリデーションエラー時のスタイル適用
                        event.target.style.backgroundColor = flgNG_bgc;
                        alert(inputDataName + 'は数字のみで入力してください。');
                        break;
                    default:                                
                }
                break; 
            default:
              event.target.style.backgroundColor = flgOK_bgc;
          }
        }
  
        inputErrBox.textContent = errors;
      }
    }
});

var vms = new Vue({
    el: '#search',
    data() {
      return {
        modalIsOpen: false, // モーダルが最初は非表示
        modalTitle: '商品管理-絞り込み',
      };
    },
    methods: {
      openModal() {
        this.modalIsOpen = true; // モーダルを表示
        document.getElementById("overlay").style.display = "block";
      },
      closeModal() {
        this.modalIsOpen = false; // モーダルを閉じる
        document.getElementById("overlay").style.display = "none";
      }
    },
}); 

// 送信チェック
function submitInputcheck() {
    var errors = []; // エラーメッセージを格納する配列
    var inputBoxes = document.querySelectorAll('.input_edit');
    var okCount;

    okCount = 0;
    //未入力チェック
    inputBoxes.forEach(function(inputBox) {
        //データ名の取得
        var inputDataName = inputBox.getAttribute('data-name');            
        var backgroundColor = window.getComputedStyle(inputBox).getPropertyValue('background-color');
        var inputBoxId = inputBox.id;
        var inputErrId = inputBoxId.replace('box','err');
        var inputErrBox = document.getElementById(inputErrId);

        if (backgroundColor === flgOK_bgc){okCount+=1;};
           
        //背景がOK色じゃない場合
        if ((backgroundColor !== flgOK_bgc && backgroundColor !== flgup_bgc) && inputBox.hasAttribute('required')) {
            //NG色でもない
            if (backgroundColor !== flgNG_bgc) {
                // 未入力ってことなんでNG色に変更
                inputBox.style.backgroundColor = flgNG_bgc;
                // エラーメッセージを出力
                inputErrBox.textContent = inputDataName + 'が未入力です。'
            }
            errors.push (inputDataName+'を確認してください。');
        }
    });

    // エラーメッセージがある場合falseを返す。
    if (errors.length > 0) {
        alert(errors.join('\n')); // エラーメッセージを改行で連結して表示
        return false; // ページ遷移を中止
    }

    if (okCount===0 && selectFileFlg===0) {
        errors.push ('変更された項目がありません。');
        alert(errors.join('\n'));
        return false;
    }

    return true; // 全ての入力必須項目が満たされていればtrueを返す
}

// 電話番号と郵便番号の形式チェック関数
function isValidNumber(inputNumber,type) {
    var inputCode = inputNumber.replace(/-/g, ''); // ハイフンを除去

    // 返答
    // 1 : OK
    // 0 : 不明
    // -1: 桁数エラー
    // -99:数字以外エラー

    //数字で構成されているかチェック
    if (!/^\d+$/.test(inputCode)) {
        return -99;
    } else {
        //桁数チェック
        switch (type) {
            case 1: //電話番号 --9桁-11桁
                if (inputCode.length < 9 || inputCode.length > 11) {
                    return -1;
                }
                break;
            case 2: //郵便番号 --7桁
                if (inputCode.length !== 7) {
                    return -1;
                }
                break;
            default:
                return 0;
        }

        return 1;
    }
}

function Search_Data() {
    var inputBoxData = document.querySelectorAll('.input_search');
    var setnewWhereData;
    var WhereFlg;
    var WhereCounter;

    WhereFlg = false;
    WhereCounter = 0;
    // お客様情報の入力を取得してフォームデータに追加
    inputBoxData.forEach(function(userInputBox) {
        var inputName = userInputBox.getAttribute('name');
        var inputValue = userInputBox.value;

        if (inputValue && inputValue.trim() !== '') {
            // id属性を取得
            var inputId = userInputBox.getAttribute('id');
            // idの一部を変える
            var opefiedId = inputId.replace('search', 'ope');
            var conjfiedId = inputId.replace('search', 'conj');
            // 変更後のidを持つ要素を取得
            var selectOpe = document.getElementById(opefiedId);
            var selectConj = document.getElementById(conjfiedId);

            // 演算子は選ばれてる？
            if (selectOpe.value.trim() === '') {
                var labelElement = document.querySelector('label[for="' + inputId + '"]');
                if (labelElement) {
                    var labelText = labelElement.textContent;
                    WhereFlg = false;
                    WhereCounter = -1;
                    alert(labelText+'演算子の指定をしてください。');
                    return;
                }
            }

            switch(selectOpe.value){
                case 'like':
                    inputValue = '%' + inputValue + '%';
                    break;
                default:
                    break;
            }

            // 新データ作成
            var setNewLine = [['p'],[inputName],[selectOpe.value],[inputValue],[selectConj.value]];
            WhereFlg = true;
            
            // 新ラインは空？
            if (setnewWhereData === undefined || setnewWhereData === null) {
                setnewWhereData = setNewLine;
            } else {
                // setNewLine の要素を setnewWhereData の対応する要素に結合する
                for (var i = 0; i < setnewWhereData.length; i++) {
                    setnewWhereData[i] = setnewWhereData[i].concat(setNewLine[i]);
                }
            }
        } else {
            if (WhereCounter !== -1) { WhereCounter+= 1};
        }
    });

    if (WhereFlg) {
        // 初期化
        if (show_dataFlgConte === 0) {
            setWhereData = [['p'],['deletedAt'],['IS NULL'],[''],['']];
        } else {
            setWhereData = [['p'],['deletedAt'],['IS NOT NULL'],[''],['']];
        }

        // setnewWhereData の要素を setWhereData の対応する要素に結合する
        for (var i = 0; i < setWhereData.length; i++) {
            setWhereData[i] = setWhereData[i].concat(setnewWhereData[i]);
        }
    } else {
        // WhereでErrorがなければ
        if (WhereCounter !== -1) {
            // 初期化
            if (show_dataFlgConte === 0) {
                setWhereData = [['p'],['deletedAt'],['IS NULL'],[''],['']];
            } else {
                setWhereData = [['p'],['deletedAt'],['IS NOT NULL'],[''],['']];
            }
            WhereFlg = true;
        }
    }

    // WhereでErrorがなければ
    if (WhereCounter !== -1) {
            // order取得
        var ordersetValue = document.getElementById('orderset').value;
        var orderset_nValue = document.getElementById('orderset_n').value;

        // 取得した値を表示
        if (ordersetValue && ordersetValue.trim() !== '') {
            // セット
            setOrderData= [['p'],[ordersetValue],[orderset_nValue]];
        } else {
            // orderData取得
            var isEmpty = setOrderData.every(function(item) {
                return item.length === 1 && item[0] === '';
            });
            
            // 空じゃない？
            if (!isEmpty) {
                // 初期化
                setOrderData= [[''],[''],['']];
            }
        }
    }

    if (WhereFlg) {
        vms.closeModal();
        load_data_forProduct('reload');
    }
}

function ClearSearch() {
    // テキスト入力欄と選択ボックスの値を空にする
    const inputElements = document.querySelectorAll(".input_search");
    inputElements.forEach(function(element) {
        element.value = "";
    });

    // 選択ボックスの選択肢を初期値に戻す
    const selectElements = document.querySelectorAll("select");
    selectElements.forEach(function(element) {
        element.selectedIndex = 0;
    });
}