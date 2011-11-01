<?php


class demo_controller extends rpd {


	function index()
	{

		$data['head']		= $this->head();
		$data['title'] 		= 'Repyd Demos';
		
		$data['content']	= nl2br($this->view('home_'.rpd::get_lang('locale')));
		$data['code'] 		= '';

		//output

		echo $this->view('demo', $data);
	}


}
