<?php 
	session_start();

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	// require the autoload file
	require_once('vendor/autoload.php');

	// create an instance of the Base class
	$f3 = Base::instance();

	 // the main route. Automatically reroutes to the grade report
	$f3->route('GET /', function($f3) {
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
		if(!isset($_SESSION['gradeJSON-'.$index])) {
			$key = $user->getAccessKey();

			// get all the courses
			$courses = getCourses($key);

			// for every course, get the id and the name
			foreach($courses as $course) {
				array_push($courseIds, $course->id);
				array_push($courseNames, $course->name);
			}

			$enrollments = getEnrollments($key, $courseIds[$index]);

			// for every enrollment, get the userid, name, and grade
			foreach($enrollments as $enrollment) {
                $json = array("id" => $enrollment->user_id, 
                                "name" => $enrollment->user->name, 
                                "grade" => $enrollment->grades->final_score);

                array_push($data, $json);
				
			}

			$_SESSION['courseNames'] = $courseNames;
			$_SESSION['gradeJSON-'.$index] = $data;
		}
		
		// session json already exisits, so use it
		else {
			$courseNames = $_SESSION['courseNames'];
			$data = $_SESSION['gradeJSON-'.$index];
		}

		if(isset($_POST['refresh'])) {
			unset($_SESSION['courseNames']);
			unset($_SESSION['gradeJSON-'.$index]);
			unset($_POST['refresh']);
			
			$f3->reroute('/reports/grades/'.$index);
		}

		$f3->set('data', $data);
		$f3->set('courseName', $courseNames[$index]);
		$f3->set('courseNameList', $courseNames);
		$f3->set('index', $index);
		$f3->set('user', $user);

		echo Template::instance()->render('view/grades.html');
	});

	// assignment report route
	$f3->route('GET|POST /reports/assignments/@id', function($f3, $param) {
        
        if(!isset($_SESSION['user']))
            $f3->reroute('/login');
            
        include("model/apiRequests.php");

        $user = unserialize($_SESSION['user']);
        $key = $user->getAccessKey();

		$index = $param['id'];

        // check if json data already exist, if not, go through
        // the process of getting the data from the api
        if(!isset($_SESSION["assignmentReport-$index"])) {
            $courses = getCourses($key);

            $json = array();
            $data = array();

            $courseIds = array();
			$courseNames = array();

            // get the course id and course name for each course
            foreach($courses as $course) {
                array_push($courseIds, $course->id);
                array_push($courseNames, $course->name);
            }
            
            // get all the assignments for the current course
			$assignments = getAssignments($key, $courseIds[$index]);

			if(!empty($assignments)) {
				foreach($assignments as $assignment) {   
					// get the assignment stats for the current student
					if(!empty($assignment)) {
						$json = array('missing' => $assignment->tardiness_breakdown->missing,
									'on_time' => $assignment->tardiness_breakdown->on_time,
									'late' => $assignment->tardiness_breakdown->late,
									'floating' => $assignment->tardiness_breakdown->floating,
									'id' => $assignment->id,
									'name' => getStudentName($key, $assignment->id));
					}

					array_push($data, $json);
				}
			}
			
            // add the json data to the session
            $_SESSION["assignmentReport-$index"] = $data;
            $_SESSION['courseNames'] = $courseNames;
        }
        // otherwide just get the json data from the session
        else {
            $data = $_SESSION["assignmentReport-$index"];
            $courseNames = $_SESSION['courseNames'];
        }

        // check if the refresh button is pressed, if so, delete
        // the session variables for this json and refresh the page
        if(isset($_POST['refresh'])) {
			unset($_SESSION['courseNames']);
			unset($_SESSION["assignmentReport-$index"]);
			unset($_POST['refresh']);
			
			$f3->reroute('/reports/assignments/'.$index);
		}

		echo $user->getUsername();

        $f3->set('data', $data);
		$f3->set('courseNameList', $courseNames);
		$f3->set('index', $index);
		$f3->set('user', $user);

		echo Template::instance()->render('view/assignments.html');
	});

	// engagement report route
	$f3->route('GET|POST /reports/engagement/@id', function($f3, $params) {
		
		if(!isset($_SESSION['user']))
			$f3->reroute('/login');

		include("model/apiRequests.php");
		include("model/arrays.php");
		include("model/email-script.php");

		$user = unserialize($_SESSION['user']);
		$key = $user->getAccessKey();

		$index = $params['id'];

		// check if the user hit the submit button
		if(isset($_POST['submit'])) {
			$errors = array();
			$email = $emailMessage = "";

			if(!validEmail($_POST['email']))
				$errors['email'] = 'Invalid email format!';
			
			if(!validMessage($_POST['emailMessage']))
				$errors['message'] = 'Message should not be empty and be less than 300 characters!';
			
			// send the email if there were no errors
			if(empty($errors)) {	
				email($_POST['email'], $_POST['emailMessage'], 'This is a subject');
				$f3->reroute('/reports/engagement/'.$index);
			}
		}

		// check if the json file already exists in the server
		// if it doesn't, create it 
		if(!isset($_SESSION['engagement-'.$index])) {
			$courses = getCourses($key);

			$json = array();
			$data = array();
			$assignmentData = array();
			$courseIds = array();
			$courseNames = array();

			foreach($courses as $course) {
				array_push($courseIds, $course->id);
				array_push($courseNames, $course->name);
			}

			$enrollments = getEnrollments($key, $courseIds[$index]);
			$students = getAssignments($key, $courseIds[$index]);
            
            foreach($students as $student) {   
                // get the assignment stats for the current student
                if(!empty($student)) {
					$json = array('missing' => $student->tardiness_breakdown->missing,
								  'id' => $student->id);	
                }

                array_push($assignmentData, $json);
            }
			
			foreach($enrollments as $enrollment) {
				date_default_timezone_set('America/Los_Angeles');
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
                                'daysElapsed' => $daysElapsed->format('%a'),
                                'activityTime' => floor($enrollment->total_activity_time / 60),
								'email' => $enrollment->user->login_id);
				
                // add json data to array
				array_push($data, $json);
			}

			$data = merge_two_arrays($data, $assignmentData, "id");

			$_SESSION['engagement-'.$index] = $data;
			$_SESSION['courseNames'] = $courseNames;
		}

		else {
			$data = $_SESSION['engagement-'.$index];
			$courseNames = $_SESSION['courseNames'];
		}

		if(isset($_POST['refresh'])) {
			unset($_SESSION['engagement-'.$index]);
			unset($_SESSION['courseNames']);
			unset($_POST['refresh']);
			$f3->reroute('/reports/engagement/'.$index);
		}
	
		$f3->set('data', $data);
		$f3->set('courseName', $courseNames[$index]);
		$f3->set('courseNameList', $courseNames);
		$f3->set('index', $index);
		$f3->set('user', $user);
		
		echo Template::instance()->render('view/engagement.html');
	});

	$f3->route('GET|POST /admin', function($f3) {
		if(!isset($_SESSION['user']))
			$f3->reroute('/login');
		
		$user = unserialize($_SESSION['user']);

		
		if(!$user->isAdmin()) {
			$f3->reroute('/');
		}

		$f3->set('user', $user);

		echo Template::instance()->render('view/admin.html');
	});

	$f3->route('GET|POST /login', function($f3) {

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

	// route to log the user out. Unsets the user session
	$f3->route('GET|POST /logout', function($f3) {
		if(isset($_SESSION['user'])) {
			$_SESSION = array();
			session_destroy();
		}

		$f3->reroute('/login');
	});

	// run fat-free
	$f3->run();