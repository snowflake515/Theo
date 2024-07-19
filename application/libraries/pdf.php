<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class pdf {

	function pdf()
	{
		$CI = & get_instance();
		log_message('Debug', 'mPDF class is loaded.');
	}

	function load()
	{
		include_once APPPATH.'/third_party/mpdf/mpdf.php';
		return new mPDF('c','A4','','',15,15,45,20,5,10);
	}
}