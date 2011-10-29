<?php


class sql_controller extends rpd {

	function simple_query()
	{
		$this->db->query('select * from articles');
		$articles =  $this->db->result_array();
		
		$data['title']	= 'Simple SQL';
		$data['content']= $this->view('article_list',array('articles'=>$articles));

		$data['code']	= highlight_string(file_get_contents(__FILE__), TRUE)."\n VIEW\n".
			highlight_string(file_get_contents(dirname(__FILE__).'/../views/article_list.php'), TRUE);

		//output
		echo $this->view('demo', $data);
	}
	
	
	function active_record()
	{
		$this->db->select('*')->from('articles')->where('article_id', 1)->get();
		
		$article_one =  $this->db->row_object();
		
		$data['title']	= 'Active Record';
		$data['content']= 'first article title is: '.$article_one->title;

		$data['code']	= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}
}


