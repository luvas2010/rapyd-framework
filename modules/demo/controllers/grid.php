<?php

class grid_controller extends rpd {


	function index()
	{
		//$this->load('component','datagrid');

		//grid
		$config = array	(
			'label'     => 'Article List',
			'source'    => 'articles',
			'per_page'  => 5,
			'columns'   => array(
					array(
					'label'   => 'ID',
					'pattern' => 'article_id',
					'orderby' => true,
					'url'     => $this->url('edit?show={article_id}'),
					'img'     => 'detail.gif',
					),
					array(
					'label'   => 'Title',
					'pattern' => 'title',
					'orderby' => true,
					),
					array(
					'label'   => 'Body',
					'pattern' => '<htmlspecialchars><substr>{body}|0|10</substr> </htmlspecialchars>',
					),
			)
		);
		$grid = new datagrid_library($config);
		$grid->build();

		$data['head']			= $this->head();
		$data['title']		= 'DataGrid';
		$data['content']	= $grid.'<br />';
		$data['code'] 		= highlight_string(file_get_contents(__FILE__), TRUE);



		//output
		echo $this->view('demo', $data);


	}


}
