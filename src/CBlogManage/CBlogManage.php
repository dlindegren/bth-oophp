<?php

/**
* Class CBlogManage is the administrative part of the system - for news items.
*/
class CBlogManage {
	
	private $db;

	/**
	* @param object $db
	*/
	public function __construct($db) {
		$this->db = $db;
	}

	/**
 	* Create a slug of a string, to be used as url.
	*
	* @param string $str the string to format as slug.
	* @returns str the formatted slug. 
	*/
	function slugify($str) {
	  	$str = mb_strtolower(trim($str));
	  	$str = str_replace(array('å','ä','ö'), array('a','a','o'), $str);
	  	$str = preg_replace('/[^a-z0-9-]/', '-', $str);
	  	$str = trim(preg_replace('/-+/', '-', $str), '-');
	  	return $str;
	}

	/**
	* getAllContent selecting everything from table content.
	*/
	public function getAllContent() {
		// Select from database
        $sql = "SELECT * FROM content";

		$param = array();
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $param);

		if(isset($res)) {
			return $res;
		} else {
			die("Något gick fel!");
		}
	}

	/**
	* getContentOneID selecting content from a specific given id.
	* @param string $idRef
	* @return array $resArray
	*/
	public function getContentOneID($idRef) {
		//Select from database
        $sql = "
			SELECT c1.*, c2.*
			FROM content c1, category c2, content2category m2g
			WHERE c1.id = m2g.idContent AND m2g.idCategory = c2.id
			AND c1.id = ?;
		";

        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($idRef));


        //If the ID was found and $res is not null...
        if(isset($res[0])) {
        	//Get genres and trim
			$categories = "";
			foreach($res as $val) {
				$categories .= $val->name . ', ';
			}
			
			$categories = rtrim($categories, ", ");
            $value	= $res[0];
            $title  = htmlentities($value->title, null, 'UTF-8');

            //Put content in array
			$resArray 	= array (
				"title" 	=> "{$title}",
				"text" 		=> "{$value->DATA}",
				"slug"		=> "{$value->slug}",
				"category" 	=> "{$categories}",
				"type" 		=> "{$value->TYPE}",
				"filter" 	=> "{$value->FILTER}",
				"published" => "{$value->published}",
				"created" 	=> "{$value->created}",
			);
        } else {
            die('Misslyckades: det finns inget innehåll med sådant id.');
        }
        return $resArray;
	}

	/**
	* createInformation makes it possible to create a news item.
	* @return string $output
	*/
	public function createInformation() {

		//call function $this->checkValid() and put boolean in $validUpdate.
		$validUpdate 	= $this->checkValid();
		
		//If $validUpdate is true...
		if($validUpdate == true) {

			//Post-variables
			$title 		= strip_tags($_POST['title']);
	        $text       = strip_tags($_POST['text']);
	        $slug     	= strip_tags($_POST['slug']);
	        $slug 		= $this->slugify($slug); 
	        $filter 	= "";
	        $setDisplay = "";

	        //Publish or not.
	        if($_POST['published']	=='yes') {
	        	$published = date("Y-m-d H:i:s");
	        	$display = "yes"; //published
	        } else {
	        	$published = 'null';
	        	$display = "no";
	        }

	        //Filters...
			if($_POST['bbcode']		=='yes') {$filter .= 'bbcode,';} 		//bbcode
			if($_POST['link']		=='yes') {$filter .= 'link,';} 			//link
			if($_POST['shortcode']	=='yes') {$filter .= 'shortcode,';} 	//shortcode
			if($_POST['nl2br']		=='yes') {$filter .= 'nl2br,';} 		//nl2br
			if($_POST['markdown']	=='yes') {$filter .= 'markdown';} 		//markdown 
			$filter = rtrim($filter, ',');

			//SQL
			$sql = "
	        	INSERT INTO content
	        	(title, data, slug, filter, display, published)
	        	VALUES(?, ?, ?, ?, ?, ?)
	        ";

	        $paramArray = array($title, $text, $slug, $filter, $display, $published);
	        $res = $this->db->ExecuteQuery($sql, $paramArray);

	        //If $res is not null then insert data.
	        if($res) {
	        	//get the last inserted id.
	        	$id = $this->db->LastInsertId();

	        	//Fill $categoryArray and call categorySQLInsert() to insert chosen categories.
		        if(isset($_POST['rmstore'])) 	{$category = $_POST['rmstore']; 	$categoryArray[] = $category;}
		        if(isset($_POST['newmovies'])) 	{$category = $_POST['newmovies']; 	$categoryArray[] = $category;}
		        if(isset($_POST['oldmovies'])) 	{$category = $_POST['oldmovies']; 	$categoryArray[] = $category;}
		        if(isset($_POST['movieworld'])) {$category = $_POST['movieworld']; 	$categoryArray[] = $category;}

		        foreach($categoryArray as $val) {
		        	$this->categorySQLInsert($id, $val);
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
	* updateInformation makes it possible to update a news item.
	* @return string $output
	*/
	public function updateInformation() {
		//define $id by the $_POST['id'] and call function $this->checkValid() and put boolean in $validUpdate.
		$id         	= $_POST['id'];
		$validUpdate 	= $this->checkValid($id);

		//If $validUpdate is true...
		if($validUpdate == true) {

			//call function to delete categories...
			$this->categorySQLDelete($id);
			$categoryArray = array();

			//Fill $categoryArray and call categorySQLInsert() to insert chosen categories.
	        if(isset($_POST['rmstore'])) 	{$category = $_POST['rmstore']; 	$categoryArray[] = $category;}
	        if(isset($_POST['newmovies'])) 	{$category = $_POST['newmovies']; 	$categoryArray[] = $category;}
	        if(isset($_POST['oldmovies'])) 	{$category = $_POST['oldmovies']; 	$categoryArray[] = $category;}
	        if(isset($_POST['movieworld'])) {$category = $_POST['movieworld']; 	$categoryArray[] = $category;}

	        foreach($categoryArray as $val) {
	        	$this->categorySQLInsert($id, $val);
	        }

			//Post-variables
			$title 		= strip_tags($_POST['title']);
	        $text       = strip_tags($_POST['text']);
	        $slug     	= strip_tags($_POST['slug']);
	        $slug 		= $this->slugify($slug); 
	        $filter 	= "";
	        $setDisplay = "";
	        $where = " WHERE content.id = ?";

	        //Publish or not.
	        if($_POST['published']	=='yes') {
	        	$published = date("Y-m-d H:i:s");
	        	$setDisplay = "content.display = 'yes'"; //published
	        } else {
	        	$published = 'null';
	        	$setDisplay = "content.display = 'no'";
	        }

	        //Filters...
			if($_POST['bbcode']		=='yes') {$filter .= 'bbcode,';} 		//bbcode
			if($_POST['link']		=='yes') {$filter .= 'link,';} 			//link
			if($_POST['shortcode']	=='yes') {$filter .= 'shortcode,';} 	//shortcode
			if($_POST['nl2br']		=='yes') {$filter .= 'nl2br,';} 		//nl2br
			if($_POST['markdown']	=='yes') {$filter .= 'markdown';} 		//markdown 
			$filter = rtrim($filter, ',');

			//SQL
	        $sqlOrig = '
	            UPDATE content SET
	            content.title = ?,
	            content.DATA = ?,
	            content.slug = ?,
	            content.filter = ?,
	            content.published = ?,
	        ';

	        $sql = $sqlOrig . $setDisplay . $where;

	        $paramArray = array($title, $text, $slug, $filter, $published, $id);
	        $res = $this->db->ExecuteQuery($sql, $paramArray);

	        //If $res is not null...
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
	* deleteInformation makes it possible to delete a news item.
	*/
	public function deleteInformation() {
		//Define ID and query
		$id         = strip_tags($_POST['id']);
		$sql = '
		DELETE FROM content2category 
		WHERE idContent = ' . $id . ';

		DELETE FROM content 
		WHERE id = ' . $id . ';		
		'; 

        $res = $this->db->ExecuteQuery($sql, array($id));
        header("Location: news.php");
        die();
	}


	/**
	* checkValid will return a boolean with a true or false-state depending on what $_POST[]'s that has been filled out.
	* @return bool $returnBool
	*/
	private function checkValid() {
		if (
	        !empty($_POST['title'])			&& !empty($_POST['text']) 		&& !empty($_POST['slug']) && 
	        !empty($_POST['published']) 	&& !empty($_POST['shortcode']) 	&& !empty($_POST['bbcode'])  &&
	        !empty($_POST['link'])			&& !empty($_POST['markdown'])	&& !empty($_POST['nl2br'])
	    ) {
			if (
	        	isset($_POST['rmstore'])	|| isset($_POST['newmovies']) 	|| isset($_POST['oldmovies']) 	|| 
	        	isset($_POST['movieworld']) 
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
	* categorySQLDelete will delete all categories with a specific given ID.
	* @param string $id
	*/
	private function categorySQLDelete($id) {
		//Ta bort från DB först för att undvika dubletter.
		$sql = "DELETE FROM content2category WHERE idContent=?";
		$paramArray = array($id);
        $res = $this->db->ExecuteQuery($sql, $paramArray);    
	}

	/**
	* categorySQLInsert will insert the given categories to a news item.
	* @param string $id, strinng $category
	*/
	private function categorySQLInsert($id, $category) {
		$sql = "INSERT INTO content2category(idContent, idCategory) VALUES(?, ?)"; 
        $paramArray = array($id, $category);
        $res = $this->db->ExecuteQuery($sql, $paramArray);
	}


	/**
	* createNewsUserPanel will create the HTML markup for a overview of available news item.
	* @param array $movieObject
	* @return string $out
	*/
	public function createNewsUserPanel($movieObject) {
		$out = '<div class="blog-controller"><h2>Nyhetshantering</h2>
			<p><b>Är biblioteket lite klent?</b> - <a href="newnewsitem.php">Lägg till nyhet</a></p>
		';

		foreach($movieObject AS $key => $val) {
			$out .= '
				<p>' 
					. $val->title . ' - 
					<a href="newsitem.php?slug='. $val->slug .'">Visa</a> - 
					<a href="editnews.php?id='. $val->id.'">Redigera</a> -
					<a href="deletenews.php?id='. $val->id.'">Ta bort</a>
				</p>'
			;
		}
		$out .= '</div>';
		return $out;
	}


	/**
	* createForm will create the HTML markup for creating a news item.
	* @return string $out
	*/
	public function createForm() {
		$out = "
			<form method='post' class='edit-form-properties'>
  				
  				<fieldset>
					<legend>Skapa nyhet</legend>
					
					<p>Titel:<br/>		<input class='movie-search-full' type='text' 		name='title'/></p>		
					<p>Text:<br/>		<textarea style='max-width: 100%;width:100%;'		name='text'></textarea></p>
					<p>Slug:<br/>			<input class='movie-search-full' type='text' 	name='slug'/></p>
					
					<p><b>Kategori</b></p>
					<p>
						RM Store <input type='checkbox' 		name='rmstore' value='1' /> &nbsp;
						New Movies <input type='checkbox' 		name='newmovies' value='2' /> &nbsp;
						Old Movies <input type='checkbox' 		name='oldmovies' value='3' /> &nbsp;
						Movieworld <input type='checkbox' 		name='movieworld' value='4' /> &nbsp;
					</p>

					<div class='row'>
						<div class='col-xs-2'>
							<p><b>bbcode2html</b></p>
							<p>
								Yes <input type='radio' name='bbcode' value='yes'> 
		    					No <input type='radio' name='bbcode' value='no'> 
		    				</p>
						</div>

						<div class='col-xs-2'>
							<p><b>makeClickable</b></p>
							<p>
								Yes <input type='radio' name='link' value='yes'> 
		    					No <input type='radio' name='link' value='no'> 
		    				</p>
						</div>

						<div class='col-xs-2'>
							<p><b>Markdown</b></p>
							<p>
								Yes <input type='radio' name='markdown' value='yes'> 
		    					No <input type='radio' name='markdown' value='no'> 
		    				</p>
						</div>

						<div class='col-xs-2'>
							<p><b>nl2br</b></p>
							<p>
								Yes <input type='radio' name='nl2br' value='yes'> 
		    					No <input type='radio' name='nl2br' value='no'> 
		    				</p>
						</div>

						<div class='col-xs-2'>
							<p><b>Shortcode</b></p>
							<p>
								Yes <input type='radio' name='shortcode' value='yes'> 
		    					No <input type='radio' name='shortcode' value='no'> 
		    				</p>
						</div>
					</div>

					<p><b>Publicera din nyhet?</b></p>
					<p>
						Ja <input type='radio' name='published' value='yes' /> &nbsp;
						Nej <input type='radio' name='published' value='no' /> &nbsp;
					</p>

					<p><input type='submit' name='save' value='Spara'/></p>
				</fieldset>

			</form>
			<p class='text-back-properties'>
				<a href='news.php'>Tillbaks till nyheterna</a>
			</p>
		";
		return $out;
	}

	/**
	* createEditForm will create the HTML markup for editing a news item.
	* @return string $out
	* @param string $id, array $editArray
	*/
	public function createEditForm($id, $editArray) {

		//Define variables for the form. 
		$title 		= htmlentities($editArray['title'], null, 'UTF-8');
		$text 		= htmlentities($editArray['text'], null, 'UTF-8');
		$genres 	= htmlentities($editArray['category'], null, 'UTF-8');
		$type 		= htmlentities($editArray['type'], null, 'UTF-8');
		$filter 	= htmlentities($editArray['filter'], null, 'UTF-8');
		$published 	= htmlentities($editArray['published'], null, 'UTF-8');
		$created 	= htmlentities($editArray['created'], null, 'UTF-8');
		$slug 		= htmlentities($editArray['slug'], null, 'UTF-8');

		$out = "
			<form method='post' class='edit-form-properties'>
  				
  				<fieldset>
					<legend>Uppdatera innehåll</legend>

					<input type='hidden' name='id' value='{$id}'/>
					
					<p>Titel:<br/>		<input class='movie-search-full' type='text' 		name='title' value='{$title}'/></p>		
					<p>Text:<br/>		<textarea style='max-width: 100%;width:100%;'		name='text'>{$text}</textarea></p>
					<p>Slug:<br/>			<input class='movie-search-full' type='text' 	name='slug' value='{$slug}'/></p>
					
					<p><b>Kategori</b></p>
					<p>
						RM Store <input type='checkbox' 		name='rmstore' value='1' /> &nbsp;
						New Movies <input type='checkbox' 		name='newmovies' value='2' /> &nbsp;
						Old Movies <input type='checkbox' 		name='oldmovies' value='3' /> &nbsp;
						Movieworld <input type='checkbox' 		name='movieworld' value='4' /> &nbsp;
					</p>

					<div class='row'>
						<div class='col-xs-2'>
							<p><b>bbcode2html</b></p>
							<p>
								Yes <input type='radio' name='bbcode' value='yes'> 
		    					No <input type='radio' name='bbcode' value='no'> 
		    				</p>
						</div>

						<div class='col-xs-2'>
							<p><b>makeClickable</b></p>
							<p>
								Yes <input type='radio' name='link' value='yes'> 
		    					No <input type='radio' name='link' value='no'> 
		    				</p>
						</div>

						<div class='col-xs-2'>
							<p><b>Markdown</b></p>
							<p>
								Yes <input type='radio' name='markdown' value='yes'> 
		    					No <input type='radio' name='markdown' value='no'> 
		    				</p>
						</div>

						<div class='col-xs-2'>
							<p><b>nl2br</b></p>
							<p>
								Yes <input type='radio' name='nl2br' value='yes'> 
		    					No <input type='radio' name='nl2br' value='no'> 
		    				</p>
						</div>

						<div class='col-xs-2'>
							<p><b>Shortcode</b></p>
							<p>
								Yes <input type='radio' name='shortcode' value='yes'> 
		    					No <input type='radio' name='shortcode' value='no'> 
		    				</p>
						</div>
					</div>

					<p><b>Publicera din nyhet?</b></p>
					<p>
						Ja <input type='radio' name='published' value='yes' /> &nbsp;
						Nej <input type='radio' name='published' value='no' /> &nbsp;
					</p>

					<p><input type='submit' name='save' value='Spara'/></p>
				</fieldset>

			</form>
			<p class='text-back-properties'>
				<a href='newsitem.php?slug={$slug}'>Tillbaks till filmen</a>
			</p>
		";
		return $out;
	}

	/**
	* createDeleteForm will create the HTML markup for deleting a news item.
	* @return string $out
	* @param string $id, array $deleteArray
	*/
	public function createDeleteForm($id, $deleteArray) {

		//Define the title of the news item.
		$title 		= htmlentities($deleteArray['title'], null, 'UTF-8');

		$out = "
			<form method='post' class='delete-form-properties'>
  				<fieldset>
					<legend>Ta bort</legend>
					<input type='hidden' name='id' value='{$id}'/>
					<input type='hidden' name='title' value='{$title}'/>
					<p>
						<label><b>Titel:</b> {$title}</label>
					</p>

					<p>
						<input type='submit' name='delete' value='Ta Bort'/>
					</p>		
				</fieldset>
			</form>
			<p class='text-back-properties'>
				<a href='news.php?id={$id}'>Tillbaks till nyheten</a>
			</p>
		";
		return $out;
	}	
}