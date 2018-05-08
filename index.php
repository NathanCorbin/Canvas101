<?php 
	session_start();

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	// require the autoload file
	require_once('vendor/autoload.php');

	// create an instance of the Base class
	$f3 = Base::instance();

	$f3->route('GET /', function($f3){
		if(!isset($_SESSION['user']))
			$f3->reroute('/login');

		$f3->reroute('/reports/grades/0');
	});

	// report routes. Takes a parameter to determine what time of report to display
	$f3->route('GET /reports/grades/@id', function($f3, $params) {
		include("model/apiRequests.php");

		if(!isset($_SESSION['user']))
			$f3->reroute('/login');

		//new UserDB();
		$user = unserialize($_SESSION['user']);

		$key = $user->getAccessKey();

		// get all the courses
		$courses = getCourses($key);

		$names = array();
		$grades = array();
		$userIds = array();
		$courseIds = array();
		$courseNames = array();
		
		$json = array();
		$data = array();

		$index = $params['id'];

		// for every course, get the id and the name
		foreach($courses as $course)
		{
			array_push($courseIds, $course->id);
			array_push($courseNames, $course->name);
		}

		$enrollments = getEnrollments($key, $courseIds[$index]);
		
		// for every enrollment, get the userid, name, and grade
		foreach($enrollments as $enrollment)
		{

			// make sure the enrollment role is not a teacher
			if($enrollment->role != 'TeacherEnrollment')
			{	
				$json = array("id" => $enrollment->user_id, 
							  "name" => $enrollment->user->name, 
							  "grade" => $enrollment->grades->final_score);

				array_push($data, $json);
			}
		}

		//$f3->set('user', $user);
		//$f3->set('grades', $grades);
		//$f3->set('names', $names);

		$f3->set('data', $data);
		$f3->set('courseName', $courseNames[$index]);
		$f3->set('courseNameList', $courseNames);

		echo Template::instance()->render('view/grades.html');
	});

	$f3->route('GET|POST /reports/assignments', function($f3){



		echo Template::instance()->render('view/assignments.html');
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
