<?php

// language flag
define('LANGUAGE_NOTSET', -1);
define('LANGUAGE_EN', 0);
define('LANGUAGE_ES', 1);
define('LANGUAGE_FR', 2);
define('LANGUAGE_IT', 3);
define('LANGUAGE_DE', 4);
define('LANGUAGE_PT', 5);
define('LANGUAGE_RU', 6);
define('LANGUAGE_ZH', 7);
define('LANGUAGE_KO', 8);
define('LANGUAGE_ALL', 100);

// release flag options
define('RELEASEFLAG_NOTSET',    0);
define('RELEASEFLAG_PRIVATE',   10);
define('RELEASEFLAG_APPROVED',  20);
define('RELEASEFLAG_PAID',      50);
define('RELEASEFLAG_MEMBER',    80);
define('RELEASEFLAG_PUBLIC',    100);
define('RELEASEFLAG_DEFAULT',   RELEASEFLAG_PRIVATE);

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

// defintion types
define('DEFTYPE_NOTSET',        0);
define('DEFTYPE_SNIPPET',       1);
define('DEFTYPE_DICTIONARY',    10);
define('DEFTYPE_USER',          100);
define('DEFTYPE_OTHER',         200);

define('DEF_HASH_LENGTH',        50);
define('DEF_PERMALINK_WORDS',     6);

define('DEFINITIONS_SEARCH_NOTSET', 0);
define('DEFINITIONS_SEARCH_ALPHA', 1);
define('DEFINITIONS_SEARCH_REVERSE', 2);
define('DEFINITIONS_SEARCH_NEWEST', 3);
define('DEFINITIONS_SEARCH_RECENT', 4);
define('DEFINITIONS_SEARCH_MISSING_TRANSLATION', 5);
define('DEFINITIONS_SEARCH_MISSING_DEFINITION', 6);
define('DEFINITIONS_SEARCH_MISSING_CONJUGATION', 7);
define('DEFINITIONS_SEARCH_WIP_NOTFINISHED', 8);
define('DEFINITIONS_SEARCH_VERBS', 9);
define('DEFINITIONS_SEARCH_ALL', 10);
define('DEFINITIONS_SEARCH_NEWEST_VERBS', 11);
define('DEFINITIONS_SEARCH_RANDOM_VERBS', 12);
define('DEFINITIONS_SEARCH_RANDOM_WORDS', 13);
define('DEFINITIONS_SEARCH_RANKED', 14);
define('DEFINITIONS_SEARCH_RANKED_VERBS', 15);

// entries
define('ENTRY_TYPE_NOTSET', 	-1);
define('ENTRY_TYPE_NOTUSED', 	0);
define('ENTRY_TYPE_ENTRY', 		1);
define('ENTRY_TYPE_ARTICLE', 	2);
define('ENTRY_TYPE_BOOK',	 	3);
define('ENTRY_TYPE_LESSON',	 	4);
define('ENTRY_TYPE_OTHER',		99);

// query sorting
define('ORDERBY_APPROVED', 0);
define('ORDERBY_TITLE', 1);
define('ORDERBY_DATE', 2);
define('ORDERBY_VIEWS', 3);

// Tags
define('TAG_RECENT', 'recent');
define('TAG_BOOK', 'book');

// Tag types
define('TAG_TYPE_NOTSET',			   	0);
define('TAG_TYPE_SYSTEM',				1); // one for everybody, ex: recent article
//define('TAG_TYPE_RECENT_ARTICLE',	   	1); // old way
define('TAG_TYPE_BOOK',				   	2); //not implented yet: need one per book
define('TAG_TYPE_DEF_FAVORITE', 	    3); // one per user so we have empty favorites list
define('TAG_TYPE_OTHER',			   	99);
//define('TAG_TYPE_DEFAULT', TAG_TYPE_SYSTEM); // need this?

// history type
define('HISTORY_TYPE_NOTSET', 	-1);
define('HISTORY_TYPE_NOTUSED', 	0);
define('HISTORY_TYPE_LIST',	    1);
define('HISTORY_TYPE_ARTICLE', 	2);
define('HISTORY_TYPE_BOOK',	 	3);
define('HISTORY_TYPE_LESSON',	4);
define('HISTORY_TYPE_EXERCISE',	10);
define('HISTORY_TYPE_OTHER',	99);

// misc options
define('USER_ID_NOTSET', 0);
define('DESCRIPTION_LIMIT_LENGTH', 30);
define('MAX_DB_TEXT_COLUMN_LENGTH', 65535 - 2); // 2 byetes for db overhead
define('MS_YEAR', 525600); // milliseconds for one year
define('TIMED_SLIDES_DEFAULT_BREAK_SECONDS', 20);
define('TIMED_SLIDES_DEFAULT_SECONDS', 50);

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
		'alphanum' => 'a-zA-Z0-9- \r\n',
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
