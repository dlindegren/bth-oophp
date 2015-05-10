<?php

/**
* Class CBlogHTML creates the blog system.
*/
class CBlogHTML {
		
	private $db;
	private $blogstate;
	private $textfilter;

	/**
	* @param object $db, object $blogstate, object $CTextFilter.
	*/
	public function __construct($db, $blogstate, $CTextFilter) {
		$this->db = $db;
		$this->blogstate = $blogstate;
		$this->textfilter = $CTextFilter;
	}

	/**
	* createIndexHTML creates the HTML-markup for the startpage
	* @return string $HTML.
	*/
	public function createIndexHTML() {
		//Declare $res variable from blogStatement
		$res = $this->blogstate->blogStatement();

		$HTML = "";
		//Adding the news items.
		foreach($res AS $key => $val) {
			$HTML .= 
				"<div class='col-md-4'>
					<h2><a href='newsitem.php?slug=". $val->slug ."'>{$val->title}</a></h2>
  					<p>{$this->handleString($val->DATA, $val->slug, $val->FILTER)}</p>
  				</div>
				";
		}
		return $HTML;
	}

	/**
	* createHTML creates the HTML-markup for presenting the selected news items. 
	* @return string $out.
	*/
	public function createHTML () {
		
		//Declare STD Object $res
		$res = $this->blogstate->blogStatement();

		$blogPost = "";

		//Adding news items contained in $res variable.
		foreach($res AS $key => $val) {
			$catLink = "";
			$categoryAnchors = explode(",",($val->category));
			
			foreach($categoryAnchors as $catVal) {
				$catLink .= "<a href='". $this->db->getQueryString(array('genre' => $catVal))  ."'>$catVal</a> &nbsp;";
			}

			//Calling the function handleString to handle the $val-data variable with the chosen filters.
  			$blogPost .= "
  				<div class='one-blog-post'>
  					<h2><a href='newsitem.php?slug=". $val->slug ."'>{$val->title}</a></h2>
  					<p>{$this->handleString($val->DATA, $val->slug, $val->FILTER)}</p>

  					<div class='container-fluid'>
  						<p class='pull-right'>Kategori: ". $catLink ."  &nbsp; Publicerad: {$val->published}</p>
  					</div>
  				</div>";
		}

		//Adding HTML markup to $out.
		$out = "
				<div class='container-fluid text-center show-all-movies'>
					Nyheter
				</div>
				
				<div class='blog-posts'>  	
					{$blogPost}
				</div>
		";
		return $out;
	}

	/**
	* handleString is called in createHTML to handle the data with the appropriate filter.
	* @return string $HTML.
	* @param string $string, string $slug, string $filter.
	*/
	private function handleString($string, $slug, $filter) {

		$incomingStringLength = strlen($string);
		$outgoingString = mb_strimwidth($string, 0, 100, "...");
		$outgoingStringLength = strlen($outgoingString);

		if($filter != '') {
			$string = $this->textfilter->doFilter(htmlentities($string, null, 'UTF-8'), $filter);
		} 

		if($incomingStringLength > $outgoingStringLength) {
			$outgoingString = mb_strimwidth($string, 0, 140, "...");
			$HTML = $outgoingString . '<a href="newsitem.php?slug='.$slug.'" class="pull-right">Läs hela...</a>';
		} else {
			$HTML = $string;
		}
		return $HTML;
	}
}