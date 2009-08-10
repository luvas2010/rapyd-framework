<?php


class edit_grid_controller extends rpd {


	public function article()
	{

		//article dataedit

		$article_edit = new dataedit_library();
		$article_edit->label = 'Manage Articls';
		$article_edit->source('articles');
		$article_edit->field('input','title','Title')->rule('trim','required');
		$article_edit->field('editor','body','Description')->rule('required');
		$article_edit->field('container','comments','m');
		$article_edit->buttons('modify','save','undo');
		$article_edit->build();

		if ($this->qs->value('show'))
		{
			if ($this->qs->value('show1|modify1|update1|create1|insert1|do_delete1'))
			{
				$article_edit->nest('comments',$this->comment());
			} else {
				$article_edit->nest('comments',$this->comments());
			}
		}
		$this->render($article_edit);
	}


	public function comment()
	{

		//comments dataedit
		$comment_edit = new dataedit_library();
		$comment_edit->label = 'Manage Comment';
		$comment_edit->back_url = $this->url('edit_grid/article?show='.$this->qs->value('show'));
		$comment_edit->back_save = true;
		$comment_edit->back_delete = true;
		$comment_edit->back_cancel = true;
		$comment_edit->back_cancel_save = true;
		$comment_edit->source('comments');
		$comment_edit->field('hidden','article_id','')->insert_value($this->qs->value('show'));
		$comment_edit->field('textarea','comment','Comment');
		$comment_edit->buttons('modify','save','undo','back');
		$comment_edit->build();

		return $comment_edit->output;
	}


	public function comments()
	{
		//comments datagrid configuration

		$comments_grid = new datagrid_library();
		$comments_grid->source('comments');
		$comments_grid->db->where('article_id',$this->qs->value('create|show|modify|update'));
		$comments_grid->db->orderby('comment_id','desc');
		$comments_grid->column('comment_id', 'ID', true)
					->url('edit_grid/article?show={article_id}&modify1={comment_id}','detail.gif');
		$comments_grid->column('comment','Comment');
		$comments_grid->column('delete', 'delete')
					->url('edit_grid/article?show={article_id}&do_delete1={comment_id}');
		$comments_grid->buttons('add');
		$comments_grid->build();

		return $comments_grid->output;

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
