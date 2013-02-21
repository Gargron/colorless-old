<?php
    class Banned {
		function check() {
	    	$ref = (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : NULL);

			if(empty($ref)) {

			} elseif($_SERVER["REQUEST_METHOD"] == "POST") {
			    $ref_url = parse_url($ref);
			    $host = $ref_url["host"];
			    preg_match('/([^\.]+)\.([a-z\.]+)/i', $host, $result);
			    $domain = $result[0];
			    if($domain !== "thecolorless.net" && $domain !== "yolde.thecolorless.net") {
				    die("Only allowed from thecolorless.net and yolde.thecolorless.net. Detected: ".$domain);
			    }
			}
		}
    }