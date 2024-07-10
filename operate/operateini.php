<?php
    // このスクリプトは、Smartyを使用した処理を行う場合に、設定ファイルを読込関数集です

    /**
     * INIファイルからopeLoadセクションのデータを取得する関数
     *
     * @return array iniファイルから読み取ったopeLoadデータの配列
     */
    function getIniOpeDataLoad() {
        // iniファイルの読込
        $ini_array = parse_ini_file(__DIR__ . "/operate.ini", true);

        // opeLoadセクションのデータを配列に格納
        $iniOpeDataLoad = array();
        foreach ($ini_array['opeLoad'] as $key => $value) {
            $iniOpeDataLoad[] = $value;
        }

        // 処理結果を返す
        return $iniOpeDataLoad;
    }

    /**
     * INIファイルからopeLoadsmartyセクションのデータを取得する関数
     *
     * @return array iniファイルから読み取ったopeLoadsmartyデータの配列
     */
    function getIniOpeDataLoadsmarty() {
        // iniファイルの読込
        $ini_array = parse_ini_file(__DIR__ . "/operate.ini", true);

        // opeLoadsmartyセクションのデータを配列に格納
        $iniOpeDataLoadsmarty = array();
        foreach ($ini_array['opeLoadsmarty'] as $key => $value) {
            $iniOpeDataLoadsmarty[] = $value;
        }

        // 処理結果を返す
        return $iniOpeDataLoadsmarty;
    }

    /**
     * INIファイルからopeRegistセクションのデータを取得する関数
     *
     * @return array iniファイルから読み取ったopeRegistデータの配列
     */
    function getIniOpeDataRegist() {
        // iniファイルの読込
        $ini_array = parse_ini_file(__DIR__ . "/operate.ini", true);

        // opeRegistセクションのデータを配列に格納
        $iniOpeDataRegist = array();
        foreach ($ini_array['opeRegist'] as $key => $value) {
            $iniOpeDataRegist[] = $value;
        }

        // 処理結果を返す
        return $iniOpeDataRegist;
    }
?>
