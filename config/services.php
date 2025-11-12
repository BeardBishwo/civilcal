<?php
return [
    'cors' => [
        'allowed_origins' => ['*'],
        'allowed_methods' => ['GET','POST','PUT','PATCH','DELETE','OPTIONS'],
        'allowed_headers' => ['Content-Type','X-Requested-With','X-CSRF-Token','Authorization'],
        'allow_credentials' => true,
        'max_age' => 86400
    ],
];
