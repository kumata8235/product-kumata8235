<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="robots" content="noindex" />
        <title>商品管理</title>
        <link rel="stylesheet" href="./css/reset.css">
        <link rel="stylesheet" href="./css/style_kumata.css">
        <!--モーダル用-->
        <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>

        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    </head>

    <body>
        <header class="header">
            <div class="header__wrap">
                <h1>商品管理情報一覧</h1>
            </div>
        </header>

        <main class="main">
            <div class="table__wrap">
                <div class="table__box-title">
                    <div class="button__wrap">
                        <button class="button__item" id="button_search">&emsp;検&emsp;&emsp;索&emsp;</button>
                        <button @click="toggleModal" class="button__item" id="button_new">新商品登録</button>
                        <button class="button__item" id="button_def">削除済み一覧</button>
                    </div>
                </div>

                <div class="table__box">
                    <table class="showitem header-fixed">
                        <!-- ここに表示される -->
                    </table>
                </div>
            </div>
        </main>

        <footer class="footer">
            <div class="footer__wrap"></div>
        </footer>

        <!-- 半透明の背景 -->
        <div class="overlay" id="overlay"></div>

        <div id="edit">
            <adjustable-modal
                :is-open= "modalIsOpen"
                :title="modalTitle"
                :initial-width="1200"
                :initial-height="920"
                @close="closeModal"
            >
                <!-- モーダルの内容 -->
                <form id="registrationForm" @submit.prevent="submitForm">
                    <div class="inputwindow__wrap">
                        <div class="inputwindow__conte">
                            <label for="product_id">商品ID:</label>
                            <input type="text" class="input_edit" id="product_id" data-name="商品ID" name="product_id" readonly>
                            <label for="product_name">商品名:</label>
                            <input type="text" class="input_edit" id="product_name" data-name="商品名" name="name" @input="handleInputChange" autocomplete="off" required>
                            <label for="product_type">商品タイプ:</label>
                            <input type="text" class="input_edit" id="product_type" data-name="商品タイプ" name="play_type" @input="handleInputChange" autocomplete="off" required>
                            <label for="product_venu">開催場所:</label>
                            <input type="text" class="input_edit" id="product_venu" data-name="開催場所" name="play_venue" @input="handleInputChange" autocomplete="off" required>
                            <label for="product_days">開催日:</label>
                            <input type="text" class="input_edit" id="product_days" data-name="開催日" name="play_days" @input="handleInputChange" autocomplete="off" required>
                            <label for="product_price">価格:</label>
                            <input type="text" class="input_edit str_right" id="product_price" data-name="価格" inputmode="numeric" name="price" @input="handleInputChange" autocomplete="off" required>
                            <label for="product_rstart">リリース開始日:</label>
                            <input type="date" class="input_edit" id="product_rstart" data-name="リリース開始日" inputmode="numeric" name="release_date" @input="handleInputChange">
                            <label for="product_rend">リリース終了日:</label>
                            <input type="date" class="input_edit" id="product_rend" data-name="リリース終了日" inputmode="numeric" name="release_end" @input="handleInputChange">
                        </div>
                        <div class="inputwindow__conte">
                            <div id="inputwindow__imgitem"></div>
                            <div class="inputwindow_imgsuj">
                                <label for="selectpic">商品イメージ:</label>
                                <!-- 画像を選択するための input 要素 -->
                                <input type="file" class="input_edit" id="selectpic" @change="handleFileInputChange" name="file" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="inputwindow__wrap">
                        <div class="inputwindow__conte_full">
                            <label for="product_story">商品説明:</label>
                            <textarea  class="input_edit" id="product_story" data-name="商品説明" name="play_story" @input="handleInputChange" autocomplete="off" required></textarea>
                        </div>
                    </div>
                    <div class="button__wrap__input">
                    <button class="button__item" id="button_submit_es">&emsp;登&emsp;&emsp;録&emsp;</button>
                    <button class="button__item" id="button_close_ne">&emsp;閉&nbsp;じ&nbsp;る&emsp;</button>
                    </div>
                </form>
            </adjustable-modal>
        </div>

        <!-- ポップアップ 検索-->
        <div id="search">
            <adjustable-modal
                :is-open= "modalIsOpen"
                :title="modalTitle"
                :initial-width="500"
                :initial-height="1080"
                @close="closeModal"
            >
                <!-- モーダルの内容 -->
                <div class="inputwindow__wrap">
                    <div class="inputwindow__conte_search">
                        <label for="search_id">商品ID:</label>
                        <div class="inputwindow__item__serach">
                            <select class="search_ope" id="ope_id">
                                <option value="">-選択-</option>
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<"><</option>
                                <option value=">=">>=</option>
                                <option value="<="><=</option>
                                <option value="like">あいまい検索</option>
                            </select>
                            <select class="search_conj" id="conj_id">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                        </div>
                        <input type="text" class="input_search" id="search_id" name="product_id">
                        
                        <label for="search_name">商品名:</label>
                        <div class="inputwindow__item__serach">
                            <select class="search_ope" id="ope_name">
                                <option value="">-選択-</option>
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<"><</option>
                                <option value=">=">>=</option>
                                <option value="<="><=</option>
                                <option value="like">あいまい検索</option>
                            </select>
                            <select class="search_conj" id="conj_name">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>                        
                        </div>
                        <input type="text" class="input_search" id="search_name" name="name">

                        <label for="search_type">商品タイプ:</label>
                        <div class="inputwindow__item__serach">                            
                            <select class="search_ope" id="ope_type">
                                <option value="">-選択-</option>
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<"><</option>
                                <option value=">=">>=</option>
                                <option value="<="><=</option>
                                <option value="like">あいまい検索</option>
                            </select>                 
                            <select class="search_conj" id="conj_type">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>       
                        </div>
                        <input type="text" class="input_search" id="search_type" name="play_type">

                        <label for="search_venu">開催場所:</label>
                        <div class="inputwindow__item__serach">                            
                            <select class="search_ope" id="ope_venu">
                                <option value="">-選択-</option>
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<"><</option>
                                <option value=">=">>=</option>
                                <option value="<="><=</option>
                                <option value="like">あいまい検索</option>
                            </select>               
                            <select class="search_conj" id="conj_venu">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>  
                        </div>       
                        <input type="text" class="input_search" id="search_venu" name="play_venue">

                        <label for="search_days">開催日:</label>
                        <div class="inputwindow__item__serach">                            
                            <select class="search_ope" id="ope_days">
                                <option value="">-選択-</option>
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<"><</option>
                                <option value=">=">>=</option>
                                <option value="<="><=</option>
                                <option value="like">あいまい検索</option>
                            </select>      
                            <select class="search_conj" id="conj_days">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>  
                        </div>                
                        <input type="text" class="input_search" id="search_days" name="play_days">

                        <label for="search_price">価格:</label>
                        <div class="inputwindow__item__serach">
                            <select class="search_ope" id="ope_price">
                                <option value="">-選択-</option>
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<"><</option>
                                <option value=">=">>=</option>
                                <option value="<="><=</option>
                                <option value="like">あいまい検索</option>
                            </select>   
                            <select class="search_conj" id="conj_price">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                        </div>
                        <input type="text" class="input_search" id="search_price" name="price">

                        <label for="search_rstart">リリース開始日:</label>
                        <div class="inputwindow__item__serach">                            
                            <select class="search_ope" id="ope_rstart">
                                <option value="">-選択-</option>
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<"><</option>
                                <option value=">=">>=</option>
                                <option value="<="><=</option>
                                <option value="like">あいまい検索</option>
                            </select>
                            <select class="search_conj" id="conj_rstart">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>  
                        </div> 
                        <input type="date" class="input_search" id="search_rstart" name="release_date">

                        <label for="search_rend">リリース終了日:</label>
                        <div class="inputwindow__item__serach">                            
                            <select class="search_ope" id="ope_rend">
                                <option value="">-選択-</option>
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<"><</option>
                                <option value=">=">>=</option>
                                <option value="<="><=</option>
                                <option value="like">あいまい検索</option>
                            </select>   
                            <select class="search_conj" id="conj_rend">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                        </div>
                        <input type="date" class="input_search" id="search_rend" name="release_end">

                        <label for="search_story">商品説明:</label>
                        <div class="inputwindow__item__serach">                            
                            <select class="search_ope" id="ope_story">
                                <option value="">-選択-</option>
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<"><</option>
                                <option value=">=">>=</option>
                                <option value="<="><=</option>
                                <option value="like">あいまい検索</option>
                            </select>   
                            <select class="search_conj" id="conj_story">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                        </div>
                        <input type="text" class="input_search" id="search_story" name="play_story">

                        <div class="inputwindow__item__serach">
                            <label for="orderset">並び替え:</label>
                            <select id="orderset">
                                <option value="">-選択-</option>
                                <option value="product_id">商品ID</option>
                                <option value="name">商品名</option>
                                <option value="play_type">商品タイプ</option>
                                <option value="play_venue">開催場所</option>
                                <option value="play_days">開催日</option>
                                <option value="price">価格</option>
                                <option value="release_date">リリース開始日</option>
                                <option value="release_end">リリース終了日</option>
                                <option value="play_story">商品説明</option>
                            </select>
                            <select id="orderset_n">
                                <option value="ASC">昇順</option>
                                <option value="DESC">降順</option>
                            </select> 
                        </div>
                    </div>
                </div>
                <div class="button__wrap__input">
                    <button class="button__item" id="button_submit_s">&emsp;適&emsp;&emsp;用&emsp;</button>
                    <button class="button__item" id="button_clear">&emsp;ク&nbsp;リ&nbsp;ア&emsp;</button>
                    <button class="button__item" id="button_close_s">&emsp;閉&nbsp;じ&nbsp;る&emsp;</button>
                </div>
            </adjustable-modal>
        </div>

        <?php include './../operate/operate.php'; ?>
        <script src="./js/product.js"></script>
    </body>
</html>