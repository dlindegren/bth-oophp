<?php 
/**
 * This is a DLINDE pagecontroller.
 *
 */
// Include the essential config-file which also creates the $dlinde variable with its defaults.
include(__DIR__.'/config.php'); 



// Do it and store it all in variables in the dlinde container.
$dlinde['title'] = "404";
$dlinde['header'] = "";
$dlinde['main'] = "This is a DLINDE 404. Document is not here.";
$dlinde['footer'] = "";

// Send the 404 header 
header("HTTP/1.0 404 Not Found");


// Finally, leave it all to the rendering phase of DLINDE.
include(DLINDE_THEME_PATH);
