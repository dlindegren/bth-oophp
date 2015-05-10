<?php 
/**
 * This is a DLINDE pagecontroller.
 *
 */
// Include the essential config-file which also creates the $dlinde variable with its defaults.
include(__DIR__.'/config.php'); 

/**
 * Creating the directory.
 *
 */
$source = new CSource(array('secure_dir' => '..', 'base_dir' => '..'));



// Do it and store it all in variables in the DLINDE container.
$dlinde['title'] = "Källkod";


$dlinde['main'] = 
					"<h1>Källkod</h1>
					<p>Ta för er av mina problem:</p>" 
						. $source->View();

// Finally, leave it all to the rendering phase of DLINDE.
include(DLINDE_THEME_PATH);
