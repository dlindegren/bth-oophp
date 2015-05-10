<?php

/**
* Class CMovieHTML create HTML markup for displaying movies from object $movieSearch
*/
class CMovieHTML {
		
	private $db;
	private $movieSearch;

	/**
	* @param object $db, object $movieSearch
	*/
	public function __construct($db, $movieSearch) {
		$this->db = $db;
		$this->movieSearch = $movieSearch;
	}

	/**
	* Creating HTML-markup for the startpage. 
	* @return string $HTML
	*/
	public function createIndexHTML() {
		//$res is set through the searchStatement that says three movies should be collected depending on latest updated.
		$res = $this->movieSearch->searchStatement();
		
		$HTML = "";
		foreach($res AS $key => $val) {
			//ext for picture
			$ext 	= pathinfo($val->image, PATHINFO_EXTENSION);
			$image 	= substr($val->image, 4);

			$HTML .= "
				<div class='col-sm-4 text-center container'>
					<a href='movie.php?id={$val->id}'><img class='center-block' src='img.php?src={$image}&save-as={$ext}&width=200&height=200&crop-to-fit' alt='{$val->title}' /></a>
					<h2><a href='movie.php?id={$val->id}'>{$val->title}</a></h2>
					<p>Genre: {$val->genre}</p>
				</div>
			";
		}
		return $HTML;
	}

	/**
	* Creating HTML-markup (a table) 
	* @return string $out
	*/
	public function createTable () {
		
		//Declare $res variable
		$res = $this->movieSearch->searchStatement();

		//Declare variables for pagination and hits per page.
		$rows 				= $this->movieSearch->returnRows();
		$hits 				= $this->movieSearch->returnHits();
		$max 				= $this->movieSearch->returnMax();
		$page 				= $this->movieSearch->returnPage();
		$hitsPerPage 		= $this->getHitsPerPage(array(2, 4, 8), $hits);
		$getPageNavigation 	= $this->getPageNavigation($hits, $page, $max);
		
		//Creates a tr-tag with necessary variables. Calling the function orderby();
		$tr = 	"<tr>
			  		<th>Bild</th><th>Titel " . $this->orderby('title') . "</th>
			  		<th>År " . $this->orderby('year') . "</th>
			  		<th>Genre</th>
			  		<th>Pris" . $this->orderby('price') . "</th>
			  	</tr>";	


		//Adding of database entries.
		foreach($res AS $key => $val) {

			//ext for picture
			$ext 	= pathinfo($val->image, PATHINFO_EXTENSION);
			$image 	= substr($val->image, 4);

  			$tr .= "<tr>
  						<td>
  							<img src='img.php?src={$image}&save-as={$ext}&width=60&height=40&crop-to-fit' alt='{$val->title}' />
  						</td>
  						<td><a href='movie.php?id={$val->id}'>{$val->title}</a></td>
  						<td>{$val->YEAR}</td>
  						<td>{$val->genre}</td>
  						<td>{$val->price} kr</td>
  					</tr>";
		}

		//Add HTML markup 
		$out = "
				<div class='container-fluid text-center show-all-movies'>
					<div class='col-sm-3'>
						<div class='rows'>Totalt {$rows} träffar.</div>
					</div>
					<div class='col-sm-9'>
						{$hitsPerPage}
						<a href='?hits={$rows}'>Visa alla filmer</a>
					</div>
				</div>
				
				<div class='dbtable'>
				  	<table class='movie-table'>
				  		{$tr}
				  	</table>

				<div class='container-fluid text-center'>";

				if($hits != $rows) {
					$out .= "<div class='pages'>{$getPageNavigation}</div>";
				} else {
					//Om antalet hits är max rows skriver vi inte ut pagination;
					$out .= "<div class='pages'></div>";	
				}

		$out .= "</div></div>";
		return $out;
	}



	/**
	* Create links for hits per page.
	 *
	 * @param array $hits a list of hits-options to display.
	 * @param array $current value.
	 * @return string as a link to this page.
 	*/
	private function getHitsPerPage($hits, $current=null) {
  		$nav = "Antalet visningar per sida: ";
  		
  		foreach($hits AS $val) {
   			if($current == $val) {
      			$nav .= "<a class='active-anchor' href=''>$val</a>";
    		}
    		else {
      			$nav .= "<a href='" . $this->db->getQueryString(array('hits' => $val)) . "'>$val</a> ";
    		}
  		}  
  	return $nav;
	}

	/**
 	* Create navigation among pages.
	 *
	 * @param integer $hits per page.
	 * @param integer $page current page.
	 * @param integer $max number of pages. 
	 * @param integer $min is the first page number, usually 0 or 1. 
	 * @return string as a link to this page.
 	*/
	private function getPageNavigation($hits, $page, $max, $min=1) {
		$nav = '<ul class="pagination">';
  		$nav .= ($page != $min) ? "<li><a href='" .$this->db->getQueryString(array('page' => $min)) . "'>&lt;&lt;</a></li> " : '<li><a>&lt;&lt;</a></li>';
  		$nav .= ($page > $min) ? "<li><a href='" . $this->db->getQueryString(array('page' => ($page > $min ? $page - 1 : $min) )) . "'>&lt;</a></li> " : '<li><a>&lt;</a></li>';

  		for($i=$min; $i<=$max; $i++) {
    		if($page == $i) {
     			$nav .= "<li><a>$i</a></li>";
    		} else {
      			$nav .= "<li><a href='" . $this->db->getQueryString(array('page' => $i)) . "'>$i</a></li>";
    		}
  		}

  		$nav .= ($page < $max) ? "<li><a href='" . $this->db->getQueryString(array('page' => ($page < $max ? $page + 1 : $max) )) . "'>&gt;</a></li>" : '<li><a>&gt;</a></li> ';
  		$nav .= ($page != $max) ? "<li><a href='" . $this->db->getQueryString(array('page' => $max)) . "'>&gt;&gt;</a></li> " : '<li><a>&gt;&gt;</a></li>';
  		$nav .= '</ul>';
  		return $nav;
	}

	/**
	* Function to create links for sorting
	 *
	 * @param string $column the name of the database column to sort by
	 * @return string with links to order by column.
 	*/
	private function orderby($column) {
  		$nav  = "<a href='" . $this->db->getQueryString(array('orderby'=>$column, 'order'=>'asc')) . "'>&darr;</a>";
  		$nav .= "<a href='" . $this->db->getQueryString(array('orderby'=>$column, 'order'=>'desc')) . "'>&uarr;</a>";
  		return "<span class='orderby'>" . $nav . "</span>";
	}
}