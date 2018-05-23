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
	$f3->route('GET|POST /reports/grades/@id', function($f3, $params) {
		include("model/apiRequests.php");

		if(!isset($_SESSION['user']))
			$f3->reroute('/login');


		//new UserDB();
		$user = unserialize($_SESSION['user']);

		$courseIds = array();
		$courseNames = array();
		
		$json = array();
		$data = array();

		// get the index from the url
		$index = $params['id'];

		// check if gradeJSON session exists for particular index
		// if it doesn't, call the api and parse the data
		// and set the json to the session
		if(!isset($_SESSION['gradeJSON-'.$index]))
		{
			$key = $user->getAccessKey();

			// get all the courses
			$courses = getCourses($key);

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

			$_SESSION['courseNames'] = $courseNames;
			$_SESSION['gradeJSON-'.$index] = $data;
		}
		
		// session json already exisits, so use it
		else {
			$courseNames = $_SESSION['courseNames'];
			$data = $_SESSION['gradeJSON-'.$index];
		}

		if(isset($_POST['refresh']))
		{
			unset($_SESSION['courseNames']);
			unset($_SESSION['gradeJSON-'.$index]);
			unset($_POST['refresh']);
			
			$f3->reroute('/reports/grades/0');
		}

		$f3->set('data', $data);
		$f3->set('courseName', $courseNames[$index]);
		$f3->set('courseNameList', $courseNames);

		echo Template::instance()->render('view/grades.html');
	});

	// assignment report route
	$f3->route('GET|POST /reports/assignments', function($f3){

		if(!isset($_SESSION['user']))
			$f3->reroute('/login');

		include("model/apiRequests.php");


		$user = unserialize($_SESSION['user']);

		echo Template::instance()->render('view/assignments.html');
	});

	$f3->route('GET|POST /reports/engagement/@id', function($f3, $params){
		
		if(!isset($_SESSION['user']))
			$f3->reroute('/login');

		include("model/apiRequests.php");

		$user = unserialize($_SESSION['user']);
		$key = $user->getAccessKey();

		$index = $params['id'];

		if(!isset($_SESSION['engagement-'.$index]))
		{
			$courses = getCourses($key);

			$json = array();
			$data = array();
			$courseIds = array();

			foreach($courses as $course)
			{
				array_push($courseIds, $course->id);
			}

			$enrollments = getEnrollments($key, $courseIds[$index]);
			date_default_timezone_set('America/Los_Angeles');
			
			foreach($enrollments as $enrollment)
			{
				if($enrollment->role != 'TeacherEnrollment')
				{
					// get the current date, and the last login date
					$currentDate = new DateTime(date('Y-m-d'));
					$dateLoggedIn = new DateTime(date('Y-m-d', strtotime($enrollment->last_activity_at)));
					
					// calculate the date difference between now and when they last logged in
					$daysElapsed = date_diff($currentDate, $dateLoggedIn);

					// get the time in which they logged in and format 
					// it in a way that is easily readable
					$time = $enrollment->last_activity_at;
					$time = date('h:i a', strtotime($time));

					// get remaining information from json
					$json = array('id' => $enrollment->user_id,
								  'name' => $enrollment->user->name,
								  'lastLogin' => explode('T', $enrollment->last_activity_at)[0],
								  'time' => $time,
								  'daysElapsed' => $daysElapsed->d,
								  'activityTime' => floor($enrollment->total_activity_time / 60),
								  'email' => $enrollment->user->login_id);

					// add json data to array
					array_push($data, $json);
				}
			}

			$_SESSION['engagement-'.$index] = $data;
		}

		else
		{
			$data = $_SESSION['engagement-'.$index];
		}

		$f3->set('data', $data);

		echo Template::instance()->render('view/engagement.html');
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
			$_SESSION = array();

		$f3->reroute('/login');
	});

	// run fat-free
	$f3->run();
