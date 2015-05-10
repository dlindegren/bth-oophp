<?php 

/**
* Class CCarousel creates a Carousel based on Bootstrap's Carousel.
*/
class CCarousel {

	private $CMovieSort;
	
	/**
	* @param object $CMovieSort
	*/
	public function __construct($CMovieSort) {
		$this->CMovieSort = $CMovieSort;
	}


	/**
	* Creating the HTML Markup for the carousel
	* @return string $HTML
	*/
	public function carouselHTML() {
		//Declare $res based on three latest updated items.
		$res = $this->CMovieSort->searchStatement();
		$items = "";
		$firstItem = true;
		$amountOfObject = 2;

		$HTML = '
			<div id="myCarousel" class="carousel slide" data-ride="carousel">
	     		<div class="carousel-inner" role="listbox">   
		';

		//Adding database entries.
		foreach($res AS $key => $val) {
			//ext for picture
			$ext 	= pathinfo($val->image, PATHINFO_EXTENSION);
			$image 	= substr($val->image, 4);

			//If first item then class item active...
			if($firstItem == true) {
				$firstRow = '<div class="item active">';
			} else {
				$firstRow = '<div class="item">';
			}

			//If amountobobject modulus = 0... set title.
			if($amountOfObject %2 == 0) {
				$title = "<h2>Senast hyrda filmen: </h2>";
			} else {
				$title = "<h2>Mest populära filmen: </h2>";
			}

  			$items .= 
  				$firstRow . "
	  				<div class='container'>
	  					<div class='col-sm-6'>
			        			<img src='img.php?src={$image}&save-as={$ext}&width=200&height=200&crop-to-fit' alt='{$val->title}' />
			        	</div>
			          	<div class='col-sm-6 text-center'>
			          		{$title}
			          		<h3>
			          			{$val->title}
			          		</h3>
			          		<p>
			          			Genre: {$val->genre}
			          		</p>
			        	</div>
	  				</div>
	  			</div>
  			";
  			$firstItem = false;
  			$amountOfObject--;

  			//If no more of objects, break.
  			if($amountOfObject == 0) {break;}
		}

		//Finish HTML-markup
		$HTML .= $items . '
				</div>
			    <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
			    	<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
			    </a>
			    <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
			    	<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
			    </a>
		    </div>
	    ';

		return $HTML; 
	}
}