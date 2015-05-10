<?php

/**
* Class CBlogPage displays a news item.
*/
class CBlogPage {

	private $db;
	private $filter;
	private $user;

	/**
	* @param object $db, object $filter, object $user
	*/
	public function __construct($db, $filter, $user) {
		$this->db 		= $db;
		$this->filter 	= $filter;
		$this->user 	= $user;
	}

	/**
	* getContent gets the content of a news item from a given slug.
	* @param string $slugRef
	* @return string $out
	*/
	public function getContent($slugRef) {
		$slugSql = $slugRef ? 'slug = ?' : '1';
		$sql = "
			SELECT *
			FROM content
			WHERE
		  		$slugSql AND
		  		published <= NOW()
			ORDER BY updated DESC
			;
		";

		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($slugRef));
		$out = "";

		if(isset($res[0])) {
			$out = $this->createHTMLMarkup($res);
		} else if($slugRef) {
			$out .= "Det finns inte en sådan bloggpost.";
		} else {
			$out .= "Det finns inga bloggposter.";
		}
		return $out;
	}

	/**
	* createHTMLMarkup displays a news item.
	* @param array $res 
	* @return string $out
	*/
	public function createHTMLMarkup($res) {
		$out = "<section>";
		foreach ($res as $c) {
			if($this->user->statusCheck() == true) {
				$editLink = "<a href='editnews.php?id={$c->id}'>Redigera</a> -
				<a href='deletenews.php?id={$c->id}'>Ta bort</a>";
			} else {
				$editLink = "";
			}

			//if the filter is not empty then add the filter to the string.
			if($c->FILTER != '') {
				$data = $this->filter->doFilter(htmlentities($c->DATA, null, 'UTF-8'), $c->FILTER);
			} else {
				$data = $c->DATA;
			}

			$out .= "
				<article class='article-bottom-border'>
					<header>
					 	<h1><a href='newsitem.php?slug={$c->slug}'>{$c->title}</a></h1>
					 	<p>	
					 		<h4>$editLink</h4>
					 		<a href='news.php'>Tillbaks till alla nyheter</a>
					 	</p>
					 	<h2>{$c->published}</h2>
					</header>
					{$data}
				</article>
			";
		}
		$out .= "</section>";
		$out .= "
			<p class='text-back-properties'>
				
			</p>
		";
		return $out;
	}
}