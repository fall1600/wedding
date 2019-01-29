<?php
$apiBase = json_decode(file_get_contents(__DIR__.'/static/apibase.json'), true);

$host = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"];
$port = '';

if($_SERVER["REQUEST_SCHEME"] == 'http' && $_SERVER["SERVER_PORT"] != 80){
    $port = ':'.$_SERVER["SERVER_PORT"];
}

if($_SERVER["REQUEST_SCHEME"] == 'https' && $_SERVER["SERVER_PORT"] != 443){
    $port = ':'.$_SERVER["SERVER_PORT"];
}

$query = $_SERVER["REDIRECT_URL"].'#!'.$_GET["_escaped_fragment_"];

$requestUrl = urlencode($host.$port.$query);

$result = json_decode(file_get_contents($url = "{$apiBase['apibase']}/seo/?url=$requestUrl"), true);

if(!$result || !$result['status']){
    header("HTTP/1.0 404 Not Found");
    die;
}

echo $result['content'];

