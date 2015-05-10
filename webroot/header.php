<?php

$dlinde['header'] = <<<EOD
<div class="container">
	<div class="col-sm-6">
		<img class='sitelogo' src='img/logo.png' alt='Logo'/>
	</div>
	
	<div class="col-sm-6">
		<div class="col-sm-12">
			<div class="header-text">
				<h1 class="title">RM Rental Moviestore</h1>
				<h4 class="slogan"><i>Uthyrning av filmer över internet</i></h4>
			</div>
		</div>

		<div class="col-sm-12">
			<form action='movies.php'>
				<p>
					<input class='movie-search-half' placeholder='Titel (delsträng, använd % som *)' type='search' name='title''/>
					<input style='width:20%;' center-div-text' type='submit' name='submit' value='Sök'/>
				</p>
			</form>
		</div>
	</div>
</div>
EOD;
