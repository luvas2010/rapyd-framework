<?php


class edit_controller extends rpd {


	function index()
	{

		//edit
		$config = array	(
			'label'  => 'Manage Article',
			'source' => 'articles',
			'back_url' => $this->url('filtered_grid'),
			'fields' => array (
					array (
						'type'  => 'input',
						'name'  => 'title',
						'label' => 'Title',
						'rule'  => 'trim|required',
					),

					array(
						'type'   => 'radiogroup',
						'name'   => 'public',
						'label'  => 'Public',
						'options'=> array("y"=>"Yes", "n"=>"No"),
					),
					array(
						'type'   => 'dropdown',
						'name'   => 'author_id',
						'label'  => 'Author',
						'option' => '',
						'options'=> 'SELECT author_id, firstname FROM authors',
						'rule'  => 'required',
					),
					array(
						'type'  => 'date',
						'name'  => 'datefield',
						'label' => 'Date',
						'attributes' => array('style'=>'width: 80px'),
					),
					array(
						'type'  => 'editor',
						'name'  => 'body',
						'label' => 'Description',
					),
			),
			'buttons'  => array('modify','save','undo','back'),
		);
		$edit = new dataedit_library($config);
		$edit->build();


		$data['head']			= $this->head();
		$data['title'] 		= 'DataEdit';
		$data['content']	= $edit.'<br />';
		$data['code'] 		= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}


}
