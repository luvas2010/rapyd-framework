<?php
class cache_controller extends rpd {


	function index()
	{
		echo $this->view('welcome');
	}

	function test()
	{
		echo 'test';
	}

	public function cached()
	{

		$i = rand(1,100);

		for ($x=1; $x < 50000; $x++)
		{
			//giusto per testare le performance senza cache
		}
		$i .= " {time} {memory} {uncached} ";//{rpd::/welcome/test}
 
		//output del contenuto con cache di 60 secondi
		//prima di mandare in output la cache viene eseguito l'eventuale metodo callback
	    echo $this->cache($i, 60, 'post_cache');
	}

 	public function post_cache($content)
	{
		return str_replace('{uncached}', date('i:s'), $content);
	}



	function h() {

echo highlight_string('
<?php
class articles_model extends rpd
{

    function  __construct()
    {
        $this->db = rpd::$db;
    }

    //using simple query
    function get_categories()
    {
        $sql = " SELECT c.category_id as id, COUNT(a.article_id) as tot, c.*
		FROM articles_categories c LEFT JOIN articles a USING (category_id)
		WHERE c.public = 1
		GROUP BY c.category_id
		ORDER BY c.priority ASC ";

        $this->db->query($sql);

        if ($this->db->num_rows() > 0)
	{
            return $this->db->result_array();
        } else {
            return array();
        }
    }

    //using active record
    function get_articles($limit=10, $category="", $rand=false)
    {
        $this->db->select("a.*");
        $this->db->from("articles a");
        $this->db->where("a.public","y");

        if (is_numeric($category))
        {
            $this->db->where("a.category_id", $category);
        }
        if ($rand)
            $this->db->orderby("RANDOM()");
        else
            $this->db->orderby("article_date", "desc");

        $this->db->get(null, $limit);

        if ($this->db->num_rows() > 0)
        {
            return $this->db->result_array();
        } else {
            return array();
        }

    }
}
', true);

	}


}
?>