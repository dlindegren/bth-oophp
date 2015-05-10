<?php

/**
* Class CBlogSort is the part of the system handling selecting the news items, categories as well as creating HTML markup for displaying categories. 
*/
class CBlogSort {

	//Defining variables...
	private $db;
	private $category;
	private $orderby;
	private $order;
	private $hits;
	private $allCategories;
	private $params;

	/**
	* @param object $db, stringL $category, string $hits, string $orderby, string $order
	*/
	public function __construct($db, $category, $hits, $orderby, $order) {

		$this->db 		= $db;
		$this->category = $category;
		$this->orderby 	= $orderby;
		$this->order 	= $order;
		$this->hits 	= $hits;
		
		//Calling getCategories to set this->allCategories.
		$this->getCategories($category);
	}

	/**
	* createCategoryHTML creating HTML markup to display all available categories
	* @return $HTML 
	*/
	public function createCategoryHTML() {
		$html = '<div class="category-holder"><h2>Kategori</h2><ul>';
		$html .= $this->allCategories;
		$html .= '</ul></div>';
		return $html;
	}

	/**
	* getCategories setting $this->allCategories
	* @param string $category
	*/
	private function getCategories($category) {

		$sql = '
			SELECT DISTINCT c.name
			FROM category AS c
			INNER JOIN content2category AS C2G
		    ON c.id = C2G.idCategory
		';

		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql);

		$category = "<li><a href='news.php'>Alla</a></li>";
		foreach($res as $val) {
  			if($val->name == $this->category) {
    			$category .= "<li>$val->name</li>";
  			} else {
    			$category .= "<li><a href='" . $this->db->getQueryString(array('category' => $val->name)) . "'>{$val->name}</a></li> ";
  			}
		}
		$this->allCategories = $category;
	}

	/**
	* blogStatement prepare the query based on class variables and set an array ($res variable)
	* @return array $res
	*/
	public function blogStatement() {
		$sqlOrig = '
	  		SELECT C1.*,
	    	GROUP_CONCAT(C2.name) AS category
	  		FROM content AS C1
	    	LEFT OUTER JOIN content2category AS C2C
	      	ON C1.id = C2C.idContent
	    	INNER JOIN category AS C2
	     	ON C2C.idCategory = C2.id
		';
	
		$where    	= null;
		$limit 		= null;
		$groupby 	= ' GROUP BY C1.id';
		$sort     	= " ORDER BY $this->orderby $this->order";
		$this->params   = array();

		// Select by category
		if($this->category) {
			$where .= ' AND C2.name = ?';
		  	$this->params[] = $this->category;
		} 

		//hits
		if($this->hits) {
		  	$limit = " LIMIT $this->hits";
		}

		// Complete the sql statement
		$where = $where ? " WHERE 1 {$where} AND C1.display = 'yes'" : " WHERE C1.display = 'yes'";
		$sql = $sqlOrig . $where . $groupby . $sort . $limit;
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $this->params);

		return $res;
	}
}