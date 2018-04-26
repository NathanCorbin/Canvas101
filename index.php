<?php 
	session_start();

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	// require the autoload file
	require_once('vendor/autoload.php');

	
	// create an instance of the Base class
	$f3 = Base::instance();

	// define a default route
	$f3->route('GET /', function($f3) {
		include("model/apiRequests.php");

		if(!isset($_SESSION['user']))
			$f3->reroute('/login');

		new UserDB();

		$user = unserialize($_SESSION['user']);

		$data = getData($user->getAccessKey());

		$grades = array();
		$names = array();

		foreach($data as $value)
		{
			if(is_numeric($value))
				array_push($grades, $value);
			else
				array_push($names, $value);
		}

		$f3->set('user', $user);
		$f3->set('data', $data);
		$f3->set('grades', $grades);
		$f3->set('names', array_unique($names));

		echo Template::instance()->render('view/index.html');
	});

	$f3->route('GET|POST /login', function($f3){

		if(isset($_POST['submit'])) {
			$errors = array();

			$username = $_POST['username'];
			$password = $_POST['password'];

			new UserDB();

			if(!UserDB::login($username, $password))
				$errors["invalid-login"] = 'Username or password is incorrect';

			$f3->set('username', $_POST['username']);
			$f3->set('errors', $errors);

			// no errors, create a user object and it to session
			if(empty($errors)) {
				$_SESSION['user'] = serialize(new User($username, $password));

				$f3->reroute('/');
			}
		}

		echo Template::instance()->render('view/login.html');
	});

	$f3->route('GET|POST /logout', function($f3){
		if(isset($_SESSION['user']))
			unset($_SESSION['user']);

		$f3->reroute('/login');
	});

	// run fat-free
	$f3->run();
