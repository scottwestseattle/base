<?php

// release flag options
define('RELEASEFLAG_NOTSET',    0);
define('RELEASEFLAG_PRIVATE',   10);
define('RELEASEFLAG_APPROVED',  20);
define('RELEASEFLAG_PAID',      50);
define('RELEASEFLAG_MEMBER',    80);
define('RELEASEFLAG_PUBLIC',    100);

// misc options
define('DESCRIPTION_LIMIT_LENGTH', 30);

return [
    'characters' => [
		'accents' => 'áÁéÉíÍóÓúÚüÜñÑ',
		'safe_punctuation' => '!@.,()-+=?!_',
    ],
    'email' => [
        'support' => 'support@' . domainName(),
        'info' => 'info@' . domainName(),
    ],
    'regex' => [
		'alpha' => 'a-zA-Z ',
		'alphanum' => 'a-zA-Z0-9 \r\n',
    ],
    'release_flag' => [
        'notset' => 0,
        'private' => 10,
        'approved' => 20,
        'paid' => 50,
        'member' => 80,
        'public' => 100,
    ],
    'time' => [
		'link_expiration_minutes' => 30,
    ],
    'user_type' => [
        'unconfirmed' => 0,
        'confirmed' => 100,
        'member' => 200,
        'affiliate' => 300,
        'admin' => 1000,
        'super_admin' => 10000,
    ],
    'wip_flag' => [
        'notset' => 0,
        'inactive' => 10,
        'dev' => 20,
        'test' => 30,
        'finished' => 100,
    ],
];
