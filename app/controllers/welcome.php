<?php

class Welcome extends Controller {	
	function get() {
		$this->view->display('welcome');
	}
}

?>