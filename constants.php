<?php
define('TABLE_USERS', 'rr2_users');
define('TABLE_QUESTIONS', 'rr2_questions');
define('TABLE_VOTES', 'rr2_votes');
define('TABLE_ANSWERS', 'rr2_answers');
define('TABLE_FILES', 'rr2_files');
define('TABLE_SESSIONS', 'rr2_sessions');
define('TABLE_SESSION_DATA', 'rr2_session_data');
define('TABLE_EXCEPTIONS', 'rr2_exceptions');
define('TABLE_LOGIN_HISTORY', 'rr2_login_history');
define('TABLE_LOG', 'rr2_log');

define('LOG_GENERIC', 0);
define('LOG_LOGIN', 1);
define('LOG_MANAGE_USERS', 2);
define('LOG_ADMINISTRATION', 3);
define('LOG_SESSION', 4);

define('PRIV_LOGIN', (1 << 0));
define('PRIV_ADD_QUESTION', (1 << 1));
define('PRIV_MANAGE_ACCOUNTS', (1 << 2));
define('PRIV_SITE_CONFIG', (1 << 3));
define('PRIV_4', (1 << 4));
define('PRIV_5', (1 << 5));
define('PRIV_6', (1 << 6));
define('PRIV_7', (1 << 7));
define('PRIV_8', (1 << 8));
define('PRIV_9', (1 << 9));
define('PRIV_10', (1 << 10));
define('PRIV_11', (1 << 11));
define('PRIV_12', (1 << 12));
define('PRIV_13', (1 << 13));
define('PRIV_14', (1 << 14));
define('PRIV_15', (1 << 15));
define('PRIV_16', (1 << 16));
define('PRIV_17', (1 << 17));
define('PRIV_18', (1 << 18));
define('PRIV_19', (1 << 19));
define('PRIV_20', (1 << 20));
define('PRIV_21', (1 << 21));
define('PRIV_22', (1 << 22));
define('PRIV_23', (1 << 23));
define('PRIV_24', (1 << 24));
define('PRIV_25', (1 << 25));
define('PRIV_26', (1 << 26));
define('PRIV_27', (1 << 27));
define('PRIV_28', (1 << 28));
define('PRIV_29', (1 << 29));
define('PRIV_30', (1 << 30));
define('PRIV_ADMIN', (1 << 31));

$priv_name[0] = 'Możliwość logowania';
$priv_name[1] = 'Zadawanie pytań';
$priv_name[2] = 'Zarządzanie kontami';
$priv_name[3] = 'Dane diagnostyczne strony';
$priv_name[31] = 'Administrator';
?>