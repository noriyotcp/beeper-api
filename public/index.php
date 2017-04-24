<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods : HEAD, POST, GET, OPTIONS, PUT, PATCH, DELETE');
header('Access-Control-Allow-Headers : content-type,x-requested-with,x-api-key,X-ACCOUNT-API-KEY,X-USER-API-KEY,account_api_key,user_api_key, authorization');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	return 204;
}

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
        return false;    // serve the requested resource as-is.
    }
}

require __DIR__ . '/../src/Bootstrap.php';
