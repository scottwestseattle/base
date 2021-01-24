<?php

// language codes
define('LANGUAGE_ALL', -1);
define('LANGUAGE_EN', 0);
define('LANGUAGE_ES', 1);
define('LANGUAGE_FR', 2);
define('LANGUAGE_IT', 3);
define('LANGUAGE_DE', 4);
define('LANGUAGE_PT', 5);
define('LANGUAGE_RU', 6);
define('LANGUAGE_ZH', 7);
define('LANGUAGE_KO', 8);

// release flag options
define('RELEASEFLAG_NOTSET',    0);
define('RELEASEFLAG_PRIVATE',   10);
define('RELEASEFLAG_APPROVED',  20);
define('RELEASEFLAG_PAID',      50);
define('RELEASEFLAG_MEMBER',    80);
define('RELEASEFLAG_PUBLIC',    100);

// Work in progress
define('WIP_NOTSET', 0);
define('WIP_INACTIVE', 10);
define('WIP_DEV', 20);
define('WIP_TEST', 30);
define('WIP_FINISHED', 100);
define('WIP_DEFAULT', WIP_DEV);

// word types
define('WORDTYPE_LESSONLIST',           1);
define('WORDTYPE_LESSONLIST_USERCOPY',  2);
define('WORDTYPE_USERLIST',             3);
define('WORDTYPE_VOCABLIST',            4);
define('WORDTYPE_SNIPPET',              5);
define('WORDTYPE_USERLIST_LIMIT',       20);

// misc options
define('USER_ID_NOTSET', 0);
define('DESCRIPTION_LIMIT_LENGTH', 30);

return [
    'characters' => [
		'accents' => 'áÁéÉíÍóÓúÚüÜñÑ',
		'safe_punctuation' => '!@.,()\-+=?!_',
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
