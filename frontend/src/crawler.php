<?php
$apiBase = '__APIBASE__';

if(!preg_match('/^(\w+):\/\//i', $apiBase, $match)){
    header("HTTP/1.0 404 Not Found");
    die;
}

$host = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER["SERVER_NAME"]}";

$requestUrl = urlencode($host.$_SERVER['REQUEST_URI']);

$result = json_decode(file_get_contents($url = "{$apiBase}/seo/?url=$requestUrl&e=puppeteer"), true);

if(!$result || !$result['status']){
    header("HTTP/1.0 404 Not Found");
    die;
}
$html = $result['content'];
header("HTTP/1.0 {$result['statusCode']} {$result['reason']}");
echo preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
