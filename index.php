<?php
require_once __DIR__ . '/vendor/autoload.php';
$config = [
    'app_id'  => '1680943468006411',
    'secrets' => '41f630565a9052caf7996069394b613f9638f43d',
    'log'     => [
        'path' => 'ad.log',
        'format' => '
            {method} {uri} HTTP/{version}
            HEADERS: {req_headers}
            BODY: {req_body}
            RESPONSE: {code} - {res_body}',
    ],
];



$app  = new MySkeleton\Application($config);

$code = $_GET['auth_code'];

var_dump($app->creative->getCreative('1671991554024462'));
