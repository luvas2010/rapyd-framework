<?php


class demo_controller extends rpd {


	function index()
	{

		$data['head']		= $this->head();
		$data['title'] 		= 'Repyd Demos';
		$data['content']	= nl2br($this->view('home'));
		$data['code'] 		= nl2br($this->view('sql'));

		//output

		echo $this->view('demo', $data);
	}


}
