<?php


class form_controller extends rpd {


	function index()
	{

		//form
		$config = array	(
			'label'  => 'Simple Form',
			'fields' => array (
					array (
						//serialized way to assign 'type|name|label'
						'field' => 'input|name|Name',
						'rule'  => 'trim|required',
						'group' => 'personal data',
						//improvement, no more 'attributes' => array('styles'..
						'style' => 'width:100px',
					),
					array (
						//normal way type,name,label assigned one-by-one
						'type'  => 'input',
						'name'  => 'lastname',
						'label' => 'Lastname',
						'rule'  => 'trim|required',
						'group' => 'personal data',
						'in'    => 'name',
					),
					array (
						'type'  => 'input',
						'name'  => 'cod_fiscale',
						'label' => 'vat code',
						'rule'  => 'required|exact_length[16]',
						'mask'  => 'aaaaaa99a99a999a',
						'group' => 'fiscal data',
					),
			),
			'buttons'	=> array (
										'save'    =>'save|Next Step',
			),
		);
		$form = new dataform_library($config);
		$form->build();

		//flow control
		if ($form->on('show') OR $form->on('error'))
		{
			$output = $form->output;
		}

		if ($form->on('success'))
		{
			var_dump($_POST);
		}

		$data['head']			= $this->head();
		$data['title'] 		= 'DataForm';
		$data['content']	= $form.'<br />';
		$data['code'] 		= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}


}
