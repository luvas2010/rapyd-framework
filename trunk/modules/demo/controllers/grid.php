<?php


class grid_controller extends rpd {

	function index()
	{
		//grid

		$grid = new datagrid_library();
		$grid->label = 'Article List';
		$grid->per_page = 5;

		$grid->source('articles');
		$grid->column('article_id','ID',true)->url('edit?show={article_id}','detail.gif');
		$grid->column('title','Title');
		$grid->column('body','Body')->callback('escape');

		$grid->build();

		$data['head']	= $this->head();
		$data['title']	= 'DataGrid';
		$data['content']= $grid.'<br />';
		$data['code']	= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);


	}


}

function escape($row)
{
	return htmlspecialchars(substr($row['body'],0,10));
}
