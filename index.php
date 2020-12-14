<?php
require_once __DIR__ . '/vendor/autoload.php';
$config = [
    'app_id'  => '*',
    'secrets' => '*',
    'log'     => [
        'path' => 'ad.log',
        'format' => '
            {method} {uri} HTTP/{version}
            HEADERS: {req_headers}
            BODY: {req_body}
            RESPONSE: {code} - {res_body}',
    ],
];



// $app  = new MySkeleton\Application($config);

// $code = $_GET['auth_code'];
