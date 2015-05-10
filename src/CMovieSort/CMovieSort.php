<?php

/**
* Class CMovieSort manages the movie search function and setting a $res-variables which contains movies.
*/
class CMovieSort {

	//Declare variables
	private $db;
	private $title;
	private $genre;
	private $hits;
	private $page;
	private $year1;
	private $year2;
	private $orderby;
	private $order;
	private $allGenres;
	private $params;
	private $rows;
	private $max;

	/**
	* @param object $db, strings $title, $genre, $hits, $page, $year1, $year2, $orderby, $order
	*/
	public function __construct($db, $title, $genre, $hits, $page, $year1, $year2, $orderby, $order) {

		$this->db = $db;
		$this->title = $title;
		$this->genre = $genre;
		$this->hits = $hits;
		$this->page = $page;
		$this->year1 = $year1;
		$this->year2 = $year2;
		$this->orderby = $orderby;
		$this->order = $order;

		//Call function getGenres to set $this->allGenres
		$this->getGenres();
	}

	/**
	* Creating the search form.
	* @return string $out
	*/
	public function createForm() {
		$out = "
			<form>
				<fieldset>
					<input type=hidden name=genre value='{$this->genre}'/>
					<input type=hidden name=hits value='{$this->hits}'/>
					<input type=hidden name=page value='{$this->page}'/>
				
					<p>
						<input class='movie-search-full' placeholder='Titel (delsträng, använd % som *)' type='search' name='title' value='{$this->title}'/>
					</p>
 		    
				   	<input class='movie-search-half' placeholder='Producerad från år:' type='text' name='year1' value='{$this->year1}' placeholder='Från...'/>
				    <input class='movie-search-half' placeholder='Producerad till år:' type='text' name='year2' value='{$this->year2}' placeholder='Till...'/>
				    
				    </br></br>
				  	<p>
				  		<input class='movie-search-full center-div-text' type='submit' name='submit' value='Sök'/>
				  	</p>
				</fieldset>
			</form>
		";
		return $out;
	}

	/**
	* @return string $this->allGenres
	*/
	public function getAllGenres() {
		return $this->allGenres;
	}

	/**
	* @return string $this->rows
	*/
	public function returnRows() {
		return $this->rows;
	}

	/**
	* @return string $this->hits
	*/
	public function returnHits() {
		return $this->hits;
	}

	/**
	* @return string $this->max
	*/
	public function returnMax() {
		return $this->max;
	}

	/**
	* @return string $this->page
	*/
	public function returnPage() {
		return $this->page;
	}

	
	/**
	* get all gengres that exists.
	*/
	private function getGenres() {

		$sql = '
			SELECT DISTINCT G.name
			FROM genre AS G
			INNER JOIN movie2genre AS M2G
		    ON G.id = M2G.idGenre
		';

		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql);

		$genres = null;
		foreach($res as $val) {
  			if($val->name == $this->genre) {
    			$genres .= "<li>$val->name</li>";
  			} else {
    			$genres .= "<li><a href='movies.php" . $this->db->getQueryString(array('genre' => $val->name)) . "'>{$val->name}</a></li> ";
  			}
		}
		$this->allGenres = $genres;
	}

	/**
	* Prepare a query and getting a $res variable based on variables.
	* @return object $res
	*/
	public function searchStatement() {
		$sqlOrig = '
	  		SELECT 
	    	M.*,
	    	GROUP_CONCAT(G.name) AS genre
	  		FROM movie AS M
	    	LEFT OUTER JOIN movie2genre AS M2G
	      	ON M.id = M2G.idMovie
	    	INNER JOIN genre AS G
	     	ON M2G.idGenre = G.id
		';
		
		$where    = null;
		$groupby  = ' GROUP BY M.id';
		$limit    = null;
		$sort     = " ORDER BY $this->orderby $this->order";
		$this->params   = array();

		// Select by title
		if($this->title) {
			$where .= ' AND title LIKE ?';
	  		$this->params[] = $this->title;
		} 

		// Select by year
		if($this->year1) {
	  		$where .= ' AND year >= ?';
	  		$this->params[] = $this->year1;
		} 
	
		if($this->year2) {
	  		$where .= ' AND year <= ?';
	  		$this->params[] = $this->year2;
		} 

		// Select by genre
		if($this->genre) {
			$where .= ' AND G.name = ?';
		  	$this->params[] = $this->genre;
		} 

		// Pagination
		if($this->hits && $this->page) {
		  	$limit = " LIMIT $this->hits OFFSET " . (($this->page - 1) * $this->hits);
		}

		// Complete the sql statement
		$where = $where ? " WHERE 1 {$where}" : null;
		$sql = $sqlOrig . $where . $groupby . $sort . $limit;
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $this->params);

		$sql = " 
       		SELECT 
       		COUNT(id) AS rows 
       		FROM  
       		( 
         		$sqlOrig $where $groupby 
       		) AS movie 
    	";

    	//Rader
    	$res2 = $this->db->ExecuteSelectQueryAndFetchAll($sql, $this->params);
		$this->rows = $res2[0]->rows;
		
		//Roof
    	$this->max = ceil($this->rows/$this->hits); 
		return $res;
	}
}