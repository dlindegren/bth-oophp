<?php

/**
* Class CNavigation creates a navigation for the webpage.
*/
class CNavigation {

	/**
	* Generating the menu
	* @param array $items
	* @return $html
	*/
	public static function GenerateMenu($items) {
	    $html = '
	    	<nav class="navbar navbar-default navbar-fixed-top">
	    		<div class="container-fluid">
	    			<div class="navbar-header">
	    			 	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
	    			 		<span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
				        </button>
				    </div>

				    <div id="navbar" class="navbar-collapse collapse">
				    	<ul class="nav navbar-nav">
	    ';

	    foreach($items as $key => $item) {
	      $html .= "<li><a href='{$item['url']}'>{$item['glyphicon']}{$item['text']}</a></li>";
	    }

	    $html .= "
	    				</ul>
	    			</div><!-- collapse -->
	    		</div><!-- container -->
	    	</nav>
	    ";
	    return $html;
  	}
};