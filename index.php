<?php 

	// require the autoload file
	require_once('vendor/autoload.php');
	
	// create an instance of the Base class
	$f3 = Base::instance();

	// define a default route
	$f3->route('GET /', function($f3) {


		echo Template::instance()->render('view/index.html');
	});

	// run fat-free
	$f3->run();
