<?php
    $projectConfigs = parse_ini_file(__DIR__ . DIRECTORY_SEPARATOR . "env.ini", true);
    $websiteInfo = isset($projectConfigs['website_info']) ? $projectConfigs['website_info'] : '';
    $generalSettings = isset($projectConfigs['settings']) ? $projectConfigs['settings'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=(!empty($websiteInfo['title']) ? $websiteInfo['title'] : 'Currency Website')?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/currency-flags.min.css">
    <link rel="stylesheet" href="./assets/css/bootstrap-toaster.min.css">
</head>
<body>

<div class="w3-container w3-green">
    <h1><?=(isset($websiteInfo['website_name']) ? $websiteInfo['website_name'] : 'Currency Website')?></h1>
</div>

<div class="topnav">
    <a class="active reload" href="javascript:void(0)">Refresh &#x21bb;</a>
    <a class="display-timer" href="javascript:void(0)">Cache Expiry Count Down: <span>-- : --</span></a>
</div>

<div class="w3-row-padding">
    <table id="loading-overlay">
        <thead>
            <tr>
                <th colspan="2" class="table-head">Loading...</th>
            </tr>
        </thead>
        <tbody id="data-container"></tbody>
    </table>
</div>
<script>
    const BASE_URL = "<?=isset($generalSettings['base_url']) ? $generalSettings['base_url'] : ""?>";
</script>
<script src="./assets/js/jquery.min.js"></script>
<script src="./assets/js/bootstrap-toaster.min.js"></script>
<script src="./assets/js/loadingoverlay.min.js"></script>
<script src="./assets/js/script.js"></script>
</body>
</html>

