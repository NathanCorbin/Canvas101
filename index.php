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


		if(!isset($_SESSION['user']))
			$f3->reroute('/login');

		new UserDB();

		// get the user from the session
		$user = unserialize($_SESSION['user']);

		// get data using curl with user access key
		$url = "https://canvas.instructure.com/api/v1/courses/1310362/analytics/assignments?access_token=".$user->getAccessKey();

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$data = curl_exec($ch);

		curl_close($ch);
		
		//$data = getCourseID($user);

		$f3->set('user', $user);
		$f3->set('data', json_decode($data, true));


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
