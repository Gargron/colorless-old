<?php

class Advertising extends Controller {
	function Advertising {
		parent::Controller();
	}

	function index() {
		redirect('about/advertising', 301);
	}
}

?>