<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define('UPLOAD_STOREAGE', 'asset/upload/');
define('UPLOAD_STOREAGE_BASEPATH', BASEPATH . '../asset/upload');

define('YES', 'Y');
define('NO', 'N');
define('CONST_STATUS', 'status');
define('CONST_ERROR', 'error');
define('CONST_ERRORS', 'errors');
define('CONST_SUCCESS', 'success');
define('ASCENDING', 'ASC');
define('DESCENDING', 'DESC');

define('CONST_CREATION_TIME', 'creation_time');
define('CONST_CREATED_BY', 'created_by');
define('CONST_MODIFICATION_TIME', 'modification_time');
define('CONST_MODIFIED_BY', 'modified_by');

define('CONST_AGENDA', 'agenda');
define('CONST_SETTING', 'setting');

define('CONST_DATE_MASK', 'yyyy-mm-dd');
define('CONST_DATE_MASK_1', 'YYYY-MM-DD');
define('CONST_DATE_MASK_2', 'Y-m-d');
define('CONST_DATETIME_MASK_1', 'YYYY-MM-DD HH:mm:ss');
define('CONST_DATETIME_MASK_2', 'Y-m-d H:i:s');

define('CONST_ID', 'id');
define('CONST_EVENT', 'event');
define('CONST_TIME', 'time');
define('CONST_TIME_END', 'time_end');
define('CONST_PLACE', 'place');
define('CONST_MATERIAL', 'material');
define('CONST_NOTES', 'notes');
define('CONST_START_ON', 'start_on');
define('CONST_DAY', 'day');
define('CONST_DATE', 'date');
define('CONST_WEEK', 'week');
define('CONST_MONTH', 'month');
define('CONST_YEAR', 'year');
define('CONST_PERIOD', 'period');
define('CONST_DATETIME', 'datetime');
define('CONST_IS_REPEAT', 'is_repeat');
define('CONST_FREQUENCY', 'frequency');
define('CONST_IRREGULAR_DATES', 'irregular_dates');

define('CONST_WEEKLY', 'WEEKLY');
define('CONST_MONTHLY', 'MONTHLY');
define('CONST_IRREGULAR', 'IRREGULAR');

define('CONST_LEVEL', 'level');
define('CONST_LEVEL_DAERAH', 'daerah');
define('CONST_LEVEL_DESA', 'desa');
define('CONST_LEVEL_KELOMPOK', 'kelompok');

define('CONST_SUNDAY', 'SUNDAY');
define('CONST_MONDAY', 'MONDAY');
define('CONST_TUESDAY', 'TUESDAY');
define('CONST_WEDNESDAY', 'WEDNESDAY');
define('CONST_THURSDAY', 'THURSDAY');
define('CONST_FRIDAY', 'FRIDAY');
define('CONST_SATURDAY', 'SATURDAY');