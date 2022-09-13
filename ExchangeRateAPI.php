<?php

    $callApi = new RequestApi;

    // general settings
    $projectConfigs = parse_ini_file($callApi::PROJECT_PATH . "env.ini", true);

    // autoload classes
    function __autoload($className) {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . $className. '.php';
        if (file_exists($filePath)) {
            include $filePath;
        }else{
            die("The file {$className}.php could not be found!");
        }
    }

    // call currency exchange api

    if (!$callApi->readCache()){
        $liveData = $callApi->get("{$callApi->apiBaseURL}/latest", ["base" => "USD"]);
        $liveData = json_decode($liveData, true);

        // write into cache file
        $callApi->writeIntoJsonFile($liveData);
    }else{
        // data from cache
        $liveData = json_decode($callApi->readCache(), true);
    }

    // get remaining expiry time in seconds
    $liveData['cache_expiry_time'] = $callApi->cacheExpiryTime();

    // in case of server issue
    if (!array_key_exists('success', $liveData)){
        echo json_encode($liveData);
        exit();
    }

    // skip donation array object
    if (isset($liveData['motd'])) unset($liveData['motd']);

    // select top 10 records from the array
    $liveRates = array_slice($liveData['rates'], 0, 11);

    // Convert base date format
    $liveData['date'] = date("d F Y", strtotime($liveData['date']));

    // prepare render data
    $prepareRenderData = [];
    $index = 0;
    foreach ($liveRates as $k => $val){
        $countryCode = substr(strtolower($k), 0, 2);
        $prepareRenderData[$index] = [
            'code' => $k,
            'amount' => number_format($val, 3),
            'flag_class' => "flag-icon-{$countryCode}",
            'country_name' => $callApi->codeToCountry($countryCode),
        ];
        $index++;
    }

    // general setting
    $liveData['site_settings'] = $projectConfigs['settings'];

    // replace the rates key with the latest data
    $liveData['rates'] = $prepareRenderData;

    echo json_encode($liveData);
    exit();

?>
