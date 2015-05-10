<?php 

/**
* Class CMoviePage is displaying a movie and with the use of the CUser class adding links to edit/deleting.
*/
class CMoviePage {
	
	private $db;
	private $filter;
	private $user;
	private $userCheck;

	/**
	* Setting $this->UserCheck depending on statusCheck()
	* @param object $db, object $filter, object $user
	*/
	public function __construct($db, $filter, $user) {
		$this->db 		= $db;
		$this->filter 	= $filter;
		$this->user 	= $user;

		if($this->user->statusCheck() == true) {
			$this->userCheck = true;
		} else {
			$this->userCheck = false;
		}		
	}

	/**
	* Get the content of one movie.
	* @param string $urlRef
	* @return string $out
	*/
	public function getContent($urlRef) {
		
		$sql = "
			SELECT m.*, g.*
			FROM movie m, genre g, movie2genre m2g
			WHERE m.id = m2g.idMovie AND m2g.idGenre = g.id
			AND m.id = ?;
		";
	
		$res 		= $this->db->ExecuteSelectQueryAndFetchAll($sql, array($urlRef));

		//check if $res contains something.
		if($res) {
			
			//Get categories and trim
			$genres = "";
			foreach($res as $val) {
				$genres .= $val->name . ', ';
			}
			$genres = rtrim($genres, ", ");

			$value 	= $res[0];

			//set variables to use when calling createHTMLMarkup()
			$title  	= htmlentities($value->title, null, 'UTF-8');
			$text   	= $this->filter->doFilter(htmlentities($value->plot, null, 'UTF-8'), 'bbcode'); //filter
			$id 		= $urlRef;
			$image 		= $value->image;
			$price 		= $value->price;
			$year 		= $value->YEAR;
			$youtube 	= $value->youtube;
			$imdb 		= $value->imdb;
			$director 	= $value->director;
			$length 	= $value->LENGTH;

			$out 	= $this->createHTMLMarkup($title, $text, $image, $genres, $price, $year, $youtube, $imdb, $director, $length, $id);
		} else {
			$out = "Someting went wrong!";
		}
		return $out;
	}

	/**
	* Creating the HTML for one movie.
	* @param strings $title, $text, $image, $genres, $price, $year, $youtube, $imdb, $director, $length, $id
	* @return string $out
	*/
	public function createHTMLMarkup($title, $text, $image, $genres, $price, $year, $youtube, $imdb, $director, $length, $id) {

		//Om inloggad
		if($this->userCheck == true) {
			$editLink = "
				<a href='editmovie.php?id={$id}'>Redigera information</a> -
				<a href='deletemovie.php?id={$id}'>Ta bort</a>
			";
		} else {
			$editLink = "";
		}

		//If Youtube link would be missing in the table (database) we won't use it.
		if($youtube != null) {
			$youtube = $this->createIframe($youtube);
		}

		//Image extension & PATH (img.php)
		$ext 		= pathinfo($image, PATHINFO_EXTENSION);
		$image 		= substr($image, 4);

		$out = "
			<article>
				<header style='padding-left: 1em;'>
					<h1>{$title}</h1>
					<h4 class='pull-right'>$editLink</h4>
				</header>

				<div class='col-md-6'>		
					<img src='img.php?src={$image}&save-as={$ext}&width=350&height=350&crop-to-fit' alt='{$title}'/>
				</div>

				<!-- INFO --> 
				<div class='col-md-6'>
					<h4><b>Handling: </b></h4>
					<p>
						$text
					</p>
				
					<div class='row'>
						<div class='col-sm-4'>
							<h4><b>Genre: </b></h4>
							<p>
								$genres
							</p>
						</div>

						<div class='col-sm-4'>
							<h4><b>Pris: </b></h4>
							<p>
								$price kr
							</p>
						</div>

						<div class='col-sm-2'>
							<h4><b>År: </b></h4>
							<p>
								$year kr
							</p>
						</div>

						<div class='col-sm-2'>
							<h4><b>Längd: </b></h4>
							<p>
								$length min
							</p>
						</div>
						
					</div>

					<div class='row'>
						<div class='col-sm-4'>
							<h4><b>Regissör: </b></h4>
							<p>
								$director
							</p>
						</div>
						<div class='col-sm-8'>
							<h4><b>IMDB: </b></h4>
							<p>
								<a href='$imdb' class='imdb-link'>$imdb</a>
							</p>
						</div>	
					</div>

					<div class='row'>
						<button type='button' class='btn btn-primary' style='width: 100%;'>Hyr</button>
					</div>
				</div>
			</article>

			<div class='row'></div>

			<div class='movie-footer'>
				<div class='container-fluid'>
					<div class='col-md-12'>
						$youtube
					</div>
				</div>
			</div>
		";
		return $out;
	}

	/**
	* Creating an iframe of the data given in database
	* @param strings $youtube
	* @return string $youtube
	*/
	private function createIframe($youtube) {

		//If $youtube isn't a URL we will give the user an error...
		if (filter_var($youtube, FILTER_VALIDATE_URL) === FALSE) {
    		die(htmlentities('Något har gått fel här! En trailer saknas!', null, 'UTF-8'));
		} else {
			$youtube	= explode('v=', $youtube);
			$youtube 	= explode('&',$youtube[1]);
			$youtube 	= $youtube[0];

			$youtube = "<iframe width='100%' height='500px' src='http://www.youtube.com/embed/". $youtube."' frameborder='0'></iframe>";
			return $youtube;
		}	
	}

	
}