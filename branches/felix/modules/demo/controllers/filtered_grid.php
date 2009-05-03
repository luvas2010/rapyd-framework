<?php


class filtered_grid_controller extends rpd {


	function index()
	{
		//filter
		$config = array	(
			'label'   => 'Article Filter',
			'fields'  => array(
					array(
						'type'  => 'input',
						'name'  => 'title',
						'label' => 'Title',
						'style' => 'width:170px',
					),
					array(
						'type'   => 'radiogroup',
						'name'   => 'public',
						'label'  => 'Public',
						'options'=> array("y"=>"Yes", "n"=>"No"),
					),

			),
			'buttons' => array('reset','search')
		);
		$filter = new datafilter_library($config);
		$filter->db->select("articles.*, authors.*");
		$filter->db->from("articles");
		$filter->db->join("authors","authors.author_id=articles.author_id","LEFT");
		$filter->build();

		//grid
		$config = array	(
			'label'     => 'Article List',
			'source'    => $filter,
			'per_page'  => 5,
			'buttons'   => array('add'=>array('url'=>$this->url('edit?create=1'))),
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
				array(
					'label'   => 'Author',
					'pattern' => '{firstname} {lastname}',
				),
			)
		);
		$grid = new datagrid_library($config);
		$grid->build();


		$data['head']			= $this->head();
		$data['title']		= 'DataGrid+DataFilter';
		$data['content']	= $filter.'<br />'.$grid.'<br />';
		$data['code']			= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}


}
