<?php
// ---- TiDB Cloud connection (values from Connect dialog) ----
// Best practice: set DB_PASS as an environment variable on your server instead of typing it here.
define('DB_HOST', getenv('DB_HOST') ?: 'gateway01.ap-northeast-1.prod.aws.tidbcloud.com');
define('DB_PORT', getenv('DB_PORT') ?: '4000');
define('DB_NAME', getenv('DB_NAME') ?: 'library_db');
define('DB_USER', getenv('DB_USER') ?: 'tTe5qp1nLR1yhdq.root');
define('DB_PASS', getenv('DB_PASS') ?: 'IA7WcwBERMvfHiqv');

// CA certificate. Leave '' on Linux/AWS (system CA bundle is auto-detected).
// On Windows set e.g. 'C:/php/isrgrootx1.pem'
define('DB_SSL_CA', getenv('DB_SSL_CA') ?: '');

// URL prefix. '' when the app is at the site root; '/library' if it sits in a subfolder
define('BASE_URL', getenv('BASE_URL') !== false ? getenv('BASE_URL') : '');

define('LOAN_DAYS', 14);       // default loan period
define('FINE_PER_DAY', 5);     // fine in rupees per overdue day
define('MAX_ACTIVE_LOANS', 3); // per member
