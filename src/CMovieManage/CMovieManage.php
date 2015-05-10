<?php

/**
* Class CMovieManage is the administrative part of the system - for movies.
*/
class CMovieManage {
	
	private $db;

	/**
	* @param object $db
	*/
	public function __construct($db) {
		$this->db = $db;
	}

	/**
	* get all from the table movie
	*/
	public function getAllContent() {
		// Select from database
        $sql = "SELECT * FROM movie";

		$param = array();
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $param);

		if(isset($res)) {
			return $res;
		} else {
			die("Något gick fel!");
		}
	}

	/**
	* Get content depending on param ID.
	* @param string $idRef
	* @return array $resArray
	*/
	public function getContentOneID($idRef) {
		// Select from database
        $sql = "
			SELECT m.*, g.*
			FROM movie m, genre g, movie2genre m2g
			WHERE m.id = m2g.idMovie AND m2g.idGenre = g.id
			AND m.id = ?;
		";

        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($idRef));

        if(isset($res[0])) {
        	//Get genres and trim.
			$genres = "";
			foreach($res as $val) {
				$genres .= $val->name . ', ';
			}
			
			$genres = rtrim($genres, ", ");
            $value	= $res[0];
            $title  = htmlentities($value->title, null, 'UTF-8');
			$text   = htmlentities($value->plot, null, 'UTF-8'); 

			//Fill resArray before returning
			$resArray 	= array (
				"title" 	=> "{$title}",
				"text" 		=> "{$text}",
				"genres" 	=> "{$genres}",
				"image" 	=> "{$value->image}",
				"price" 	=> "{$value->price}",
				"year" 		=> "{$value->YEAR}",
				"youtube" 	=> "{$value->youtube}",
				"imdb" 		=> "{$value->imdb}",
				"director" 	=> "{$value->director}",
				"length" 	=> "{$value->LENGTH}",
			);
        } else {
            die('Misslyckades: det finns inget innehåll med sådant id.');
        }
        return $resArray;
	}

	/**
	* Create a new movie
	* @return string $output
	*/
	public function createInformation() {

		//set $validUpdate to boolean (from called function checkValid)
		$validUpdate 	= $this->checkValid();
		
		if($validUpdate == true) {

			//Post-variables
			$title 		= strip_tags($_POST['title']);
	        $text       = strip_tags($_POST['text']);
	        $price     	= strip_tags($_POST['price']);
	        $year      	= strip_tags($_POST['year']);
	        $youtube    = strip_tags($_POST['youtube']);
	        $imdb       = strip_tags($_POST['imdb']);
	        $director   = strip_tags($_POST['director']);
	        $length 	= strip_tags($_POST['length']); 

	        $sql = "
	        	INSERT INTO movie
	        	(title, plot, price, YEAR, youtube, imdb, director, LENGTH, updated) 
	        	VALUES(?, ?, ?, ?, ?, ?, ?, ?, NOW())
	        "; 

			$paramArray = array($title, $text, $price, $year, $youtube, $imdb, $director, $length);
	        $res = $this->db->ExecuteQuery($sql, $paramArray);

			//Check valid update.
	        if($res) {
	        	$id = $this->db->LastInsertId();

	        	//Fill array
	       		if(isset($_POST['comedy'])) 	{$genre = $_POST['comedy']; 	$genreArray[] = $genre;}
	        	if(isset($_POST['romance'])) 	{$genre = $_POST['romance']; 	$genreArray[] = $genre;}
	        	if(isset($_POST['college'])) 	{$genre = $_POST['college']; 	$genreArray[] = $genre;}
	        	if(isset($_POST['crime'])) 		{$genre = $_POST['crime']; 		$genreArray[] = $genre;}
	        	if(isset($_POST['drama'])) 		{$genre = $_POST['drama']; 		$genreArray[] = $genre;}
	        	if(isset($_POST['thriller'])) 	{$genre = $_POST['thriller']; 	$genreArray[] = $genre;}
	        	if(isset($_POST['animation'])) 	{$genre = $_POST['animation']; 	$genreArray[] = $genre;}
	        	if(isset($_POST['adventure'])) 	{$genre = $_POST['adventure']; 	$genreArray[] = $genre;}
	        	if(isset($_POST['family'])) 	{$genre = $_POST['family']; 	$genreArray[] = $genre;}
	        	if(isset($_POST['svenskt'])) 	{$genre = $_POST['svenskt']; 	$genreArray[] = $genre;}
	        	if(isset($_POST['action'])) 	{$genre = $_POST['action']; 	$genreArray[] = $genre;}
	        	if(isset($_POST['horror'])) 	{$genre = $_POST['horror'];		$genreArray[] = $genre;}

	        	//Insert genres.
	        	foreach($genreArray as $val) {
	        		$this->genreSQLInsert($id, $val);
	        	}
	            $output = "<p style='color:green;'>Informationen sparades.</p>";
	        } else {
	         	$output = "<p style='color:red;'>Informationen sparades EJ.</p>";
	        }        
		} else {
			$output = "<p style='color:red;'>Vänligen fyll i alla rutor.</p>";
		}
		return $output;	
	}
	
	/**
	* Update movie
	* @return string $output
	*/
	public function updateInformation() {

		//Set ID from $_POST['id'] and set $validUpdate to boolean
		$id         	= $_POST['id'];
		$validUpdate 	= $this->checkValid($id);
		
		if($validUpdate == true) {

			//Call function to delete genres within a specific movie.
			$this->genreSQLDelete($id);
			$genreArray = array();

			//Fill out array
	        if(isset($_POST['comedy'])) 	{$genre = $_POST['comedy']; 	$genreArray[] = $genre;}
	        if(isset($_POST['romance'])) 	{$genre = $_POST['romance']; 	$genreArray[] = $genre;}
	        if(isset($_POST['college'])) 	{$genre = $_POST['college']; 	$genreArray[] = $genre;}
	        if(isset($_POST['crime'])) 		{$genre = $_POST['crime']; 		$genreArray[] = $genre;}
	        if(isset($_POST['drama'])) 		{$genre = $_POST['drama']; 		$genreArray[] = $genre;}
	        if(isset($_POST['thriller'])) 	{$genre = $_POST['thriller']; 	$genreArray[] = $genre;}
	        if(isset($_POST['animation'])) 	{$genre = $_POST['animation']; 	$genreArray[] = $genre;}
	        if(isset($_POST['adventure'])) 	{$genre = $_POST['adventure']; 	$genreArray[] = $genre;}
	        if(isset($_POST['family'])) 	{$genre = $_POST['family']; 	$genreArray[] = $genre;}
	        if(isset($_POST['svenskt'])) 	{$genre = $_POST['svenskt']; 	$genreArray[] = $genre;}
	        if(isset($_POST['action'])) 	{$genre = $_POST['action']; 	$genreArray[] = $genre;}
	        if(isset($_POST['horror'])) 	{$genre = $_POST['horror'];		$genreArray[] = $genre;}

	        //Insert genres.
	        foreach($genreArray as $val) {
	        	$this->genreSQLInsert($id, $val);
	        }

			//Post-variables
			$title 		= strip_tags($_POST['title']);
	        $text       = strip_tags($_POST['text']);
	        $price     	= strip_tags($_POST['price']);
	        $year      	= strip_tags($_POST['year']);
	        $youtube    = strip_tags($_POST['youtube']);
	        $imdb       = strip_tags($_POST['imdb']);
	        $director   = strip_tags($_POST['director']);
	        $length 	= strip_tags($_POST['length']);      

	        $sql = '
	            UPDATE movie SET
	            movie.title = ?,
	            movie.plot = ?,
	            movie.price = ?,
	            movie.YEAR = ?,
	            movie.youtube= ?,
	            movie.imdb = ?,
	            movie.director = ?,
	            movie.LENGTH = ?,
	            movie.updated = NOW()
	            WHERE 
	            movie.id = ?
	        ';

	        $paramArray = array($title, $text, $price, $year, $youtube, $imdb, $director, $length, $id);
	        $res = $this->db->ExecuteQuery($sql, $paramArray);

	        //Check valid update.
	        if($res) {
	            $output = "<p style='color:green;'>Informationen sparades.</p>";
	        } else {
	         	$output = "<p style='color:red;'>Informationen sparades EJ.</p>";
	        }  
		} else {
			$output = "<p style='color:red;'>Vänligen fyll i alla rutor.</p>";
		}
		return $output;	
	}

	/**
	* Delete a movie
	*/
	public function deleteInformation() {
		//Set $id from $_POST['id']
		$id         = strip_tags($_POST['id']);

		$sql = '
		DELETE FROM movie2genre 
		WHERE idMovie = ' . $id . ';

		DELETE FROM movie 
		WHERE id = ' . $id . ';		
		'; 

        $res = $this->db->ExecuteQuery($sql, array($id));

        header("Location: movies.php");
        die();
	}


	/**
	* Checking if the form was filled out valid with checking $_POSTs
	* @return bool $returnBool
	*/
	private function checkValid() {
		if (
	        !empty($_POST['price'])	&& !empty($_POST['year']) 		&& !empty($_POST['youtube']) && 
	        !empty($_POST['imdb']) 	&& !empty($_POST['director']) 	&& !empty($_POST['length'])  &&
	        !empty($_POST['text'])	&& !empty($_POST['title'])
	    ) {
			if (
	        	isset($_POST['comedy'])		|| isset($_POST['romance']) 	|| isset($_POST['college']) 	|| 
	        	isset($_POST['crime']) 		|| isset($_POST['drama']) 		|| isset($_POST['thriller']) 	||
	        	isset($_POST['animation']) 	|| isset($_POST['adventure']) 	|| isset($_POST['family']) 		||
	        	isset($_POST['svenskt']) 	|| isset($_POST['action']) 		|| isset($_POST['horror'])
	        ) {
				

	        	$returnBool = true;

	        } else {
	        	$returnBool = false;
	        }
	    } else {
	    	$returnBool = false;
	    }
        return $returnBool;
	}

	/**
	* Deleting Genres within a movie.
	* @param string $id
	*/
	private function genreSQLDelete($id) {
		$sql = "DELETE FROM movie2genre WHERE idMovie=?";
		$paramArray = array($id);
        $res = $this->db->ExecuteQuery($sql, $paramArray);    
	}

	/**
	* Inserting genres within a movie.
	* @param string $id, string $genre
	*/
	private function genreSQLInsert($id, $genre) {
		$sql = "INSERT INTO movie2genre(idMovie, idGenre) VALUES(?, ?)"; 
        $paramArray = array($id, $genre);
        $res = $this->db->ExecuteQuery($sql, $paramArray);
	}


	/**
	* Creating HTML-markup for a user panel.
	* @param array $movieObject
	* @return string $out
	*/
	public function createMovieUserPanel($movieObject) {

		$out = '<div class="movie-controller"><h2>Filmhantering</h2>
			<p><b>Är biblioteket lite klent?</b> - <a href="newmovie.php">Lägg till film</a></p>
		';

		foreach($movieObject AS $key => $val) {
			$out .= '
				<p>' 
					. $val->title . ' - 
					<a href="movie.php?id='. $val->id .'">Visa</a> - 
					<a href="editmovie.php?id='. $val->id.'">Redigera</a> -
					<a href="deletemovie.php?id='. $val->id.'">Ta bort</a>
				</p>'
			;
		}
		$out .= '</div>';
		return $out;
	}

	/**
	* Creating HTML-markup for creating a movie.
	* @return string $out
	*/
	public function createForm() {
		$out = "
			<form method='post' class='edit-form-properties'>
  				
  				<fieldset>
					<legend>Uppdatera innehåll</legend>
					
					<p>Titel:<br/>		<input class='movie-search-full' 	type='text' 	name='title'/></p>		
					<p>Text:<br/>		<textarea style='max-width: 100%;width:100%;'		name='text'></textarea></p>
					
					<p><b>Glöm inte välja en genre!</b></p>
					<p>
						Comedy <input type='checkbox' 		name='comedy' value='1' /> &nbsp;
						Romance <input type='checkbox' 		name='romance' value='2' /> &nbsp;
						College <input type='checkbox' 		name='college' value='3' /> &nbsp;
						Crime <input type='checkbox' 		name='crime' value='4' /> &nbsp;
						Drama <input type='checkbox' 		name='drama' value='5' /> &nbsp;
						Thriller <input type='checkbox' 	name='thriller' value='6' /> &nbsp;
						Animation <input type='checkbox' 	name='animation' value='7' /> &nbsp;
						Adventure <input type='checkbox' 	name='adventure' value='8' /> &nbsp;
						Family <input type='checkbox' 		name='family' value='9' /> &nbsp;
						Svenskt <input type='checkbox' 		name='svenskt' value='10' /> &nbsp;
						Action <input type='checkbox' 		name='action' value='11' /> &nbsp;
						Horror <input type='checkbox' 		name='horror' value='12' /> &nbsp;
					</p>

					<p>Pris:<br/>		<input class='movie-search-full' 	type='text' 	name='price'/></p>
					<p>År:<br/>			<input class='movie-search-full'	type='text' 	name='year'/></p>
					<p>Youtube:<br/>	<input class='movie-search-full'	type='text' 	name='youtube'/></p>
					<p>IMDB:<br/>		<input class='movie-search-full' 	type='text' 	name='imdb'/></p>
					<p>Regissör:<br/>	<input class='movie-search-full' 	type='text' 	name='director'/></p>
					<p>Längd:<br/>		<input class='movie-search-full' 	type='text' 	name='length'/></p>

					<p><input type='submit' name='save' value='Spara'/></p>
				</fieldset>

			</form>
			<p class='text-back-properties'>
				<a href='usercontroller.php>Tillbaks till användarpanelen</a>
			</p>
		";
		return $out;
	}

	/**
	* Creating HTML-markup for editing a movie.
	* @param string $id, array $editArray
	* @return string $out
	*/
	public function createEditForm($id, $editArray) {

		//Defiene variables
		$title 		= htmlentities($editArray['title'], null, 'UTF-8');
		$text 		= htmlentities($editArray['text'], null, 'UTF-8');
		$genres 	= htmlentities($editArray['genres'], null, 'UTF-8');
		$price 		= htmlentities($editArray['price'], null, 'UTF-8');
		$year 		= htmlentities($editArray['year'], null, 'UTF-8');
		$youtube 	= htmlentities($editArray['youtube'], null, 'UTF-8');
		$imdb 		= htmlentities($editArray['imdb'], null, 'UTF-8');
		$director 	= htmlentities($editArray['director'], null, 'UTF-8');
		$length 	= htmlentities($editArray['length'], null, 'UTF-8');

		$out = "
			<form method='post' class='edit-form-properties'>
  				
  				<fieldset>
					<legend>Uppdatera innehåll</legend>

					<input type='hidden' name='id' value='{$id}'/>
					
					<p>Titel:<br/>		<input class='movie-search-full' 	type='text' 	name='title' value='{$title}'/></p>		
					<p>Text:<br/>		<textarea style='max-width: 100%;width:100%;'		name='text'>{$text}</textarea></p>
					
					<p><b>Glöm inte välja en genre!</b></p>
					<p>
						Comedy <input type='checkbox' 		name='comedy' value='1' /> &nbsp;
						Romance <input type='checkbox' 		name='romance' value='2' /> &nbsp;
						College <input type='checkbox' 		name='college' value='3' /> &nbsp;
						Crime <input type='checkbox' 		name='crime' value='4' /> &nbsp;
						Drama <input type='checkbox' 		name='drama' value='5' /> &nbsp;
						Thriller <input type='checkbox' 	name='thriller' value='6' /> &nbsp;
						Animation <input type='checkbox' 	name='animation' value='7' /> &nbsp;
						Adventure <input type='checkbox' 	name='adventure' value='8' /> &nbsp;
						Family <input type='checkbox' 		name='family' value='9' /> &nbsp;
						Svenskt <input type='checkbox' 		name='svenskt' value='10' /> &nbsp;
						Action <input type='checkbox' 		name='action' value='11' /> &nbsp;
						Horror <input type='checkbox' 		name='horror' value='12' /> &nbsp;
					</p>

					<p>Pris:<br/>		<input class='movie-search-full' 	type='text' 	name='price' value='{$price}'/></p>
					<p>År:<br/>			<input class='movie-search-full'	type='text' 	name='year' value='{$year}'/></p>
					<p>Youtube:<br/>	<input class='movie-search-full'	type='text' 	name='youtube' value='{$youtube}'/></p>
					<p>IMDB:<br/>		<input class='movie-search-full' 	type='text' 	name='imdb' value='{$imdb}'/></p>
					<p>Regissör:<br/>	<input class='movie-search-full' 	type='text' 	name='director' value='{$director}'/></p>
					<p>Längd:<br/>		<input class='movie-search-full' 	type='text' 	name='length' value='{$length}'/></p>

					<p><input type='submit' name='save' value='Spara'/></p>
				</fieldset>

			</form>
			<p class='text-back-properties'>
				<a href='movie.php?id={$id}'>Tillbaks till filmen</a>
			</p>
		";
		return $out;
	}

	/**
	* Creating HTML-markup for deleting a movie.
	* @param string $id, array $deleteArray
	* @return string $out
	*/
	public function createDeleteForm($id, $deleteArray) {

		//Define title
		$title 		= htmlentities($deleteArray['title'], null, 'UTF-8');

		$out = "
			<form method='post' class='delete-form-properties'>
  				<fieldset>
					<legend>Ta bort</legend>
					<input type='hidden' name='id' value='{$id}'/>
					<input type='hidden' name='title' value='{$title}'/>
					<p>
						<label><b>Titel:</b>{$title}</label>
					</p>

					<p>
						<input type='submit' name='delete' value='Ta Bort'/>
					</p>		
				</fieldset>
			</form>
			<p class='text-back-properties'>
				<a href='movie.php?id={$id}'>Tillbaks till filmen</a>
			</p>
		";
		return $out;
	}	
}