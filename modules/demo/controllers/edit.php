<?php


class edit_controller extends rpd {


	function index()
	{

		//edit
		$edit = new dataedit_library();
		$edit->back_url = $this->url('filtered_grid');

		$edit->set_label('Manage Article');
		$edit->set_source('articles');

		$edit->set_field('input','title','Title')->set_rule('trim','required');
		$edit->set_field('radiogroup','public','Public')->set_options(array("y"=>"Yes", "n"=>"No"));
		$edit->set_field('dropdown','author_id','Author')->set_options('SELECT author_id, firstname FROM authors')
			 ->set_rule('required');

		$edit->set_field('date','datefield','Date')->set_attributes(array('style'=>'width: 80px'));
		$edit->set_field('editor','body','Description')->set_rule('required');

		$edit->set_buttons('modify','save','undo','back');

		$edit->build();

		$data['head']			= $this->head();
		$data['title'] 		= 'DataEdit';
		$data['content']	= $edit.'<br />';
		$data['code'] 		= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}


}
