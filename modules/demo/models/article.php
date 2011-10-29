<?php

class article_model extends rpd
{

    function  __construct()
    {
        $this->db = rpd::$db;
    }
	
    function get_articles()
    {
        $this->db->select("a.*, au.firstname, au.lastname");
        $this->db->from("articles a");
        $this->db->join("authors au", "au.author_id=a.author_id", "LEFT");
		$this->db->where('public', 'y');
		$this->db->orderby('article_id', 'DESC');
		$this->db->get();
        return $this->db->result_array();
    }
	
    function get_article($id)
    {
        $this->db->select("a.*, au.firstname, au.lastname");
        $this->db->from("articles");
        $this->db->join("authors au", "au.author_id=a.author_id", "LEFT");
		$this->db->where('article_id', (int)$id);
		$this->db->get();

        return $this->db->row_array();
    }
	
	function count_articles()
	{
		$this->db->query('select COUNT(*) as tot from articles');
		return $this->db->row_object()->tot;
	}
}

