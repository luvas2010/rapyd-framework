<?php


class edit_grid_controller extends rpd {


	public function article()
	{

		//article dataedit configuration
		$article_config = array	(
			'label'  => 'Manage Article',
			'source' => 'articles',
			'fields' => array (
					array (
						'type'  => 'input',
						'name'  => 'title',
						'label' => 'Title',
						'rule'  => 'trim|required',
					),
					array(
						'type'  => 'editor',
						'name'  => 'body',
						'label' => 'Description',
						'when'  => 'show|modify|update',
					),
					array(
						'type'  => 'container',
						'name'  => 'comments',
					),
					//etc...
			),
			'buttons'  => array('modify','save','undo'),
		);
		$article_edit = new dataedit_library($article_config);
		$article_edit->build();

		if ($this->qs->value('create1|show1|modify1|update1|insert1|do_delete1'))
		{
			$article_edit->nest('comments',$this->comment());
		} else {
			$article_edit->nest('comments',$this->comments());
		}
		$this->render($article_edit);
	}


	public function comment()
	{

		//comments dataedit configuration
		$comment_config = array	(
			'label'  => 'Manage Comment',
			'source' => 'comments',
			'back_url'=> $this->url('edit_grid/article?show='.$this->qs->value('show')),
			'back_save' => true,
			'back_undo' => true,
			'fields' => array (
					array(
						'type'  => 'hidden',
						'name'  => 'article_id',
						'insert_value' => $this->qs->value('show'),
					),
					array(
						'type'  => 'textarea',
						'name'  => 'comment',
						'label' => 'Comment',
					),
			),
			'buttons'  => array('modify','save','undo','back'),
		);
		$comment_edit = new dataedit_library($comment_config);
		$comment_edit->build();
		return $comment_edit;
	}


	public function comments()
	{
		//comments datagrid configuration
		$comments_config = array (
			'label'     => 'Comments List',
			'source'    => 'comments',
			'per_page'  => 20,
			'buttons'   => array('add'),
			'columns'   => array(
				array(
					'label'   => 'ID',
					'pattern' => 'comment_id',
					'orderby' => true,
					'url'     => $this->url('edit_grid/article?show={article_id}&modify1={comment_id}'),
					'img'     => 'detail.gif',
				),
				array(
					'label'   => 'Comment',
					'pattern' => 'comment',
				),
				array(
					'label'   => 'delete',
					'pattern' => 'delete',
					'url'     => $this->url('edit_grid/article?show={article_id}&do_delete1={comment_id}'),
					'img'     => '',
				),
			),
		);
		$comments_grid = new datagrid_library($comments_config);
		$comments_grid->db->where('article_id',$this->qs->value('create|show|modify|update'));
		$comments_grid->db->orderby('comment_id','desc');
		$comments_grid->build();

		return $comments_grid;

	}


	private function render($content)
	{
		$data['head']		= $this->head();
		$data['title'] 		= 'DataEdit + DataGrid + Dataedit (Master-Detail)';
		$data['content'] 	= '<em>complex crud in 100 lines of code</em><br />';
		$data['content'].= $content.'<br />';
		$data['code'] 		= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}


}
