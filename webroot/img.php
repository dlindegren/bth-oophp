<?php

include(__DIR__.'/../src/CImage/CImage.php'); 

error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly

//Paths
$imagePath = __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
$cachePath = __DIR__ . '/cache/';

$CImage = new CImage($imagePath, $cachePath);
$CImage->buildImage();