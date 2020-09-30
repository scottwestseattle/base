<?php

return [
    'user_type' => [
        'unconfirmed' => 0,
        'confirmed' => 100,
        'member' => 200,
        'affiliate' => 300,
        'admin' => 1000,
        'super_admin' => 10000,
    ],
    'email' => [
        'support' => 'support@' . domainName(),
        'info' => 'info@' . domainName(),
    ],	
];