<?php 
/**
 * This is a DLINDE pagecontroller.
 *
 */
// Include the essential config-file which also creates the $dlinde variable with its defaults.
include(__DIR__.'/config.php'); 

// Do it and store it all in variables in the DLINDE container.
$dlinde['title'] = "Välkommen";

//Parametrar
$title    = isset($_GET['title']) ? $_GET['title'] : null;
$genre    = isset($_GET['genre']) ? $_GET['genre'] : null;
$hits     = isset($_GET['hits'])  ? $_GET['hits']  : 3;
$page     = isset($_GET['page'])  ? $_GET['page']  : 1;
$year1    = isset($_GET['year1']) && !empty($_GET['year1']) ? $_GET['year1'] : null;
$year2    = isset($_GET['year2']) && !empty($_GET['year2']) ? $_GET['year2'] : null;
$category = isset($_GET['category']) ? $_GET['category'] : null;

$orderbyMovie  	= isset($_GET['orderby']) ? strtolower($_GET['orderby']) : 'updated';
$orderMovie   	= isset($_GET['order'])   ? strtolower($_GET['order'])   : 'DESC';
$orderbyNews  	= isset($_GET['orderby']) ? strtolower($_GET['orderby']) : 'published';
$orderNews    	= isset($_GET['order'])   ? strtolower($_GET['order'])   : 'DESC';

//Databasanslutning & Objektdeklarering
$dbRef 			= new CDatabase($dlinde['database']);
$CMovieSort 	= new CMovieSort($dbRef, $title, $genre, $hits, $page, $year1, $year2, $orderbyMovie, $orderMovie);
$CHTMLTable 	= new CMovieHTML($dbRef, $CMovieSort);
$CBlogSort 		= new CBlogSort($dbRef, $category, $hits, $orderbyNews, $orderNews);
$CTextFilter 	= new CTextFilter();
$CBlogHTML 		= new CBlogHTML($dbRef, $CBlogSort, $CTextFilter);
$CCarousel 		= new CCarousel($CMovieSort); //karusell

//Mock-up
$genreHolder 	= $CMovieSort->getAllGenres();
$movies 		= $CHTMLTable->createIndexHTML();
$blogHolder 	= $CBlogHTML->createIndexHTML();
$carousel 		= $CCarousel->carouselHTML($CMovieSort);


$dlinde['main'] = "	
	<div class='page-content'>
		$carousel
	</div>

	<div class='container'>
		<div class='col-md-12'>
			<div class='container'>
				<div class='col-md-3 page-content'>
					<h2 class='text-center'>TÄVLING</h2>
					<a href='dice.php'>
						<img class='center-block' src='img.php?src=dices/dices.jpg&width=150&height=150' alt='dices'/>
					</a>
				</div><div class='col-md-1'></div>

				<div class='col-md-3 page-content'>
					<h2 class='text-center'>KALENDER</h2>
					<a href='calendar.php'>
						<img class='center-block' src='img.php?src=calendar/calendar.jpg&width=130&height=130' alt='calendar'/>
					</a>
				</div><div class='col-md-1'></div>

				<div class='col-md-3 page-content'>
				<h2 class='text-center'>NYHETSBREV</h2>
				</div><div class='col-md-1'></div>
			</div>
		</div>
	</div>

	<div class='container'>
		<div class='col-md-12 page-content'>
			<div class='row text-center genre-holder-index'>
				Vårt utbud av genrer: $genreHolder
			</div>
			
			<div class='container col-md-12'>
				$movies 
			</div>		
		</div>

		<div class='col-md-12 page-content'>
			<div class='container col-md-12'>
				$blogHolder
			</div>		
		</div>

	</div>
";



// Finally, leave it all to the rendering phase of DLINDE.
include(DLINDE_THEME_PATH);