<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

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
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

define('READ_MORE_IND', "Baca Lagi");
define('READ_MORE_ENG', "READMORE");

define('SECRET_KEYS', '84903790874920874290823tg423947g29g34293g49234g293gh4923nb4238n4283n420');

define('ERRORS_STYLE_OPEN', '<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> ');
define('ERRORS_STYLE_END', '</div> ');


define('SERVER_APP', 'WellTrackONE');

define('DELIMITER_SQL', '[and]');


//dev s3
//define('URL_AWS_S3', 'https://s3.amazonaws.com/awv-care-plans/');

//prod s3
define('URL_AWS_S3', 'https://s3-us-west-2.amazonaws.com/awv-care-plans-dev/');

//dev theo
//define('THEO_LINK', 'http://well1-wt1theo1.abe01.viawesthosted.net:8081');

define('THEO_LINK', 'http://well1-wt1theo2.abe01.viawesthosted.net:8080');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
