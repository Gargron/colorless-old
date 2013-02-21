<?php

class Node extends Model {

	function Node() {
		parent::Model();
	}

	function send($channel, $msg) {
		$node = fsockopen("tcp://127.0.0.1", 99, $errno, $errstr, 10);
		if($node) {

			$header = "POST {$channel} HTTP/1.1\r\n";
    		$header.= "Host: 127.0.0.1\r\n";
    		$header.= "Content-Type: text/json\r\n";
    		$header.= "Content-Length: ". strlen($msg) ."\r\n";
    		$header.= "Connection: Close\r\n\r\n";

			fputs($node, $header);
			fputs($node, $msg);
			fgets($node, 1024);
			fclose($node);

			return true;
		}
		
		return false;
	}

}