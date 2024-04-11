<?php
  $variables = [
      'APP_HTTPS_ON' => 'false',
	  'APP_DB_HOST' => '',
	  'APP_DB_USER' => '',
	  'APP_DB_PASSWORD' => '',
	  'THEO_DB_HOST' => '',
	  'THEO_DB_USER' => '',
	  'THEO_DB_PASSWORD' => '',
	  'APP_DB_DEBUG' => 'false'
  ];

  foreach ($variables as $key => $value) {
      putenv("$key=$value");
  }
?>