<?php

//
// Generic Gloval Values
//
define('DEFAULT_LIST_LIMIT', 50);
define('DEFAULT_REVIEW_LIMIT', 20);
define('DEFAULT_BIG_NUMBER', 99999);
define('RETURN_CODE_ERROR', -1);
define('RETURN_CODE_SUCCESS', 1);
define('USER_ID_NOTSET', 0);
define('MAX_DB_TEXT_COLUMN_LENGTH', 65535 - 2); // 2 bytes for db overhead
define('COOKIE_HOUR', 60);          // Minutes per hour: 60
define('COOKIE_DAY',  1440);        // Minutes per day:  60 * 24
define('COOKIE_WEEK', 1440*7);      // Minutes per week: 60 * 24 * 7
define('COOKIE_YEAR', 1440*365);    // Minutes per year: 60 * 24 * 365
define('CASE_INSENSITIVE', 'COLLATE UTF8MB4_GENERAL_CI');   // MYSQL case insensitive
define('COLLATE_ACCENTS', 'COLLATE utf8mb4_unicode_ci');    // MYSQL ignore accent chars
define('DESCRIPTION_LIMIT_LENGTH', 30);
define('TIMED_SLIDES_DEFAULT_BREAK_SECONDS', 20);
define('TIMED_SLIDES_DEFAULT_SECONDS', 50);

// query sorting
define('ORDERBY_APPROVED', 0);
define('ORDERBY_TITLE', 1);
define('ORDERBY_DATE', 2);
define('ORDERBY_VIEWS', 3);

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

//
// word types
//
define('WORDTYPE_LESSONLIST',           1);
define('WORDTYPE_LESSONLIST_USERCOPY',  2);
define('WORDTYPE_USERLIST',             3);
define('WORDTYPE_VOCABLIST',            4);
define('WORDTYPE_SNIPPET',              5);
define('WORDTYPE_USERLIST_LIMIT',       20);

//
// definition types
//
define('DEFTYPE_NOTSET',        0);
define('DEFTYPE_SNIPPET',       1);
define('DEFTYPE_DICTIONARY',    10);
define('DEFTYPE_USER',          100);
define('DEFTYPE_OTHER',         200);

define('DEF_HASH_LENGTH',        50);
define('DEF_PERMALINK_WORDS',     6);

// Search targets
define('SEARCH_MIN_LENGTH', 2); // search min length to use as 'starts with'

define('SEARCHTYPE_DEFINITIONS',    1); // word definitions
define('SEARCHTYPE_SNIPPETS',       2); // snippets only
define('SEARCHTYPE_DICTIONARY',     3); // words and snippets
define('SEARCHTYPE_ENTRIES',        4); // books and articles

// Search options
define('DEFINITIONS_SEARCH_NOTSET',                 0);
define('DEFINITIONS_SEARCH_ALPHA',                  1);
define('DEFINITIONS_SEARCH_REVERSE',                2);
define('DEFINITIONS_SEARCH_NEWEST',                 3);
define('DEFINITIONS_SEARCH_OLDEST',                 31);
define('DEFINITIONS_SEARCH_RECENT',                 4);
define('DEFINITIONS_SEARCH_MISSING_TRANSLATION',    5);
define('DEFINITIONS_SEARCH_MISSING_DEFINITION',     6);
define('DEFINITIONS_SEARCH_MISSING_CONJUGATION',    7);
define('DEFINITIONS_SEARCH_WIP_NOTFINISHED',        8);
define('DEFINITIONS_SEARCH_VERBS',                  9);
define('DEFINITIONS_SEARCH_ALL',                    10);
define('DEFINITIONS_SEARCH_NEWEST_VERBS',           11);
define('DEFINITIONS_SEARCH_RANDOM_VERBS',           12);
define('DEFINITIONS_SEARCH_RANDOM_WORDS',           13);
define('DEFINITIONS_SEARCH_RANKED',                 14);
define('DEFINITIONS_SEARCH_RANKED_VERBS',           15);
define('DEFINITIONS_SEARCH_EXAMPLES',               16);

// Snippet Categories
define('SNIPPET_CATEGORY_NOTSET',                   0);
define('SNIPPET_CATEGORY_ESP_GENDER',               1);
define('SNIPPET_CATEGORY_ESP_PRETERITE',            2);
define('SNIPPET_CATEGORY_ESP_PHRASING',             3);
define('SNIPPET_CATEGORY_ESP_REFLEXIVE',            4);
define('SNIPPET_CATEGORY_ESP_SUBJUNCTIVE',          5);
define('SNIPPET_CATEGORY_ESP_OBJECT',               6);
define('SNIPPET_CATEGORY_ESP_PREPOSITION',          7);
define('SNIPPET_CATEGORY_ESP_GRAMMAR',              8);

//
// entries
//
define('ENTRY_TYPE_NOTSET', 	-1);
define('ENTRY_TYPE_NOTUSED', 	0);
define('ENTRY_TYPE_ENTRY', 		1);
define('ENTRY_TYPE_ARTICLE', 	2);
define('ENTRY_TYPE_BOOK',	 	3);
define('ENTRY_TYPE_LESSON',	 	4);
define('ENTRY_TYPE_OTHER',		99);

//
// Tags
//
define('TAG_RECENT', 'recent');
define('TAG_BOOK', 'book');

// Tag types
define('TAG_TYPE_NOTSET',			   	0);
define('TAG_TYPE_SYSTEM',				1); // one for everybody, ex: recent article
//define('TAG_TYPE_RECENT_ARTICLE',	   	1); // old way
define('TAG_TYPE_BOOK',				   	2); // one per book to hold the chapters (entries) together
define('TAG_TYPE_DEF_FAVORITE', 	    3); // one per user so we have empty favorites list
define('TAG_TYPE_DEF_CATEGORY', 	    4); // definition categories to show what a snippet is demonstrating
define('TAG_TYPE_OTHER',			   	99);

//
// History
//
define('HISTORY_URL', '/history/add-public?');

// History type
define('HISTORY_TYPE_NOTSET',       -1);
define('HISTORY_TYPE_NOTUSED',      0);
define('HISTORY_TYPE_FAVORITES',    10);
define('HISTORY_TYPE_ARTICLE',      20);
define('HISTORY_TYPE_BOOK',         30);
define('HISTORY_TYPE_LESSON',       40);
define('HISTORY_TYPE_EXERCISE',     50);
define('HISTORY_TYPE_DICTIONARY',   60);
define('HISTORY_TYPE_DICTIONARY_VERBS', 61);
define('HISTORY_TYPE_SNIPPETS',     70);
define('HISTORY_TYPE_OTHER',        100);

//
// Subtype is how exercises are grouped/ordered/accessed
//
// non-scheduled reads/quizes/flashcards of article/word/snippet/lesson exercise/fav lists
define('HISTORY_SUBTYPE_NOTSET',          -1);
define('HISTORY_SUBTYPE_NOTUSED',         0);
define('HISTORY_SUBTYPE_OTD',             10); // article/lesson exercise/fav list of the day
define('HISTORY_SUBTYPE_LEAST_USED',      20); // least practiced article/word/snippet/lesson exercise
define('HISTORY_SUBTYPE_RANDOM',          30); // random article/words/snippets/lesson exercise/fav lists
define('HISTORY_SUBTYPE_NEWEST',          40); // newest article/words/snippets/lesson exercise/fav lists
define('HISTORY_SUBTYPE_MOST_COMMON',     50); // most common dictionary words such as "Top 20 Most Used Words"
define('HISTORY_SUBTYPE_SPECIFIC',        60); // specific article, fav list, lesson exercise
// scheduled exercises
define('HISTORY_SUBTYPE_EXERCISE_OTD',          100); // scheduled exercise of the day
define('HISTORY_SUBTYPE_EXERCISE_LEAST_USED',   110); // scheduled exercise: least practiced article/word/snippet/lesson exercise
define('HISTORY_SUBTYPE_EXERCISE_RANDOM',       120); // scheduled exercise: random article/word/snippet/lesson exercise/fav lists
define('HISTORY_SUBTYPE_EXERCISE_NEWEST',       130); // scheduled exercise: newest article/word/snippet/lesson exercise/fav lists
define('HISTORY_SUBTYPE_EXERCISE_MOST_COMMON',  140); // scheduled exercise: most common dictionary words such as "Top 20 Most Used Words"
define('HISTORY_SUBTYPE_EXERCISE_SPECIFIC',     150); // scheduled exercise: specific article, fav list, lesson exercise

// Frequency
define('FREQUENCY_NOTSET',      -1);
define('FREQUENCY_NOTUSED',     0);
define('FREQUENCY_DAILY',       10);
define('FREQUENCY_WEEKLY',      20);
define('FREQUENCY_BIWEEKLY',    30);
define('FREQUENCY_MONTHLY',     40);

// Content Level
define('LEVEL_NOTSET',  -1);
define('LEVEL_NOTUSED', 0);
define('LEVEL_A1',      10);
define('LEVEL_A2',      20);
define('LEVEL_B1',      30);
define('LEVEL_B2',      40);
define('LEVEL_C1',      50);
define('LEVEL_C2',      60);

//
// Lesson/Content Type also used for History subtype_flag
//
define('LESSON_TYPE_NOTSET',                0);
define('LESSON_TYPE_TEXT',                  10);
define('LESSON_TYPE_VOCAB',                 20);
define('LESSON_TYPE_QUIZ_MC',               30);
define('LESSON_TYPE_QUIZ_FLASHCARDS',       31);
define('LESSON_TYPE_QUIZ_TRANSLATION',      32);
define('LESSON_TYPE_QUIZ_WHEELOFFORTUNE',   33);

define('LESSON_TYPE_QUIZ_MC1',       40);
define('LESSON_TYPE_QUIZ_MC2',       41);
define('LESSON_TYPE_QUIZ_MC3',       42);
define('LESSON_TYPE_QUIZ_MC4',       43);

define('LESSON_TYPE_TIMED_SLIDES',   50);
define('LESSON_TYPE_READER',         60);
define('LESSON_TYPE_FAVORITES',      70);
define('LESSON_TYPE_OTHER',          99);
define('LESSON_TYPE_DEFAULT',        LESSONTYPE_TEXT);

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
