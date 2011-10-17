<?php
class welcome_controller extends rpd {


	function index()
	{
		echo $this->view('welcome');
	}

	function test()
	{
		echo 'test';
	}

	function maintenance()
	{
		echo 'site under maintenance';
	}

	function rerouted($stringa)
	{
		var_dump($stringa);
		var_dump(rpd::$uri_string);
		var_dump($this->uri->value('product', false, 2));
	}


}
?>