<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions
{
	public function MY_Exceptions() {
	    parent::CI_Exceptions();
	}

	public function show_error($heading, $message, $template = '', $status_code = 500)

	{

		//$ci =& get_instance();

		//if (!$page = $ci->uri->uri_string()) {

		//	$page = '';

		//}

		$images404 = array(
				    'http://imgur.com/6KLyy.png',
				    'http://i.imgur.com/EmLPQ.jpg'
				   );

		$imagesUser = array(
				    "http://i.imgur.com/fNhvm.jpg"
				    );

		$heading = array($heading);
		if($heading[0] == "An Error Was Encountered")
		    $heading[0] = "Whoops";

		switch($status_code) {

			case 403:
			    $heading[0] = 'Access Forbidden';
			    break;
			case 404:
			    $heading[0] = 'Not Found';
			    $heading[1]= '<img src="'.$images404[rand(0, count($images404)-1)].'" />';
			break;
			case 503:
			    $heading[0] = 'Undergoing Maintenance';
			break;

		}

		if($message == 'That.. user... that user... doesn\'t exist! ;-;') { $heading[1] = '<img src="'.$imagesUser[rand(0, count($imagesUser)-1)].'" />';}

		//log_message('error', $status_code . ' ' . $heading . ' --> '. $page);

		return parent::show_error($heading, $message, 'error_general', $status_code);

	}

}
?>