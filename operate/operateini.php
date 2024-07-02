 <?php
    function getIniOpeDataLoad() {
        // iniファイルの読込
        $ini_array = parse_ini_file(__DIR__ . "/operate.ini", true);

        $iniOpeDataLoad = array();
        foreach ($ini_array['opeLoad'] as $key => $value) {
            $iniOpeDataLoad[] = $value;
        }

        return $iniOpeDataLoad /* 処理結果 */;
    }

    function getIniOpeDataLoadsmarty() {
        // iniファイルの読込
        $ini_array = parse_ini_file(__DIR__ . "/operate.ini", true);

        $iniOpeDataLoadsmarty = array();
        foreach ($ini_array['opeLoadsmarty'] as $key => $value) {
            $iniOpeDataLoadsmarty[] = $value;
        }

        return $iniOpeDataLoadsmarty /* 処理結果 */;
    }

    function getIniOpeDataRegist() {
        // iniファイルの読込
        $ini_array = parse_ini_file(__DIR__ . "/operate.ini", true);

        $iniOpeDataRegist = array();
        foreach ($ini_array['opeRegist'] as $key => $value) {
            $iniOpeDataRegist[] = $value;
        }

        return $iniOpeDataRegist /* 処理結果 */;
    }
?>