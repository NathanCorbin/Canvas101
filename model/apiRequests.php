<?php 

	use \Curl\Curl;

	/**
	 * Gets a json string representation of all the courses
	 * Useful for getting the course id and the name
	 * 
	 * @param $access_key the access token to access the api
	 * @return json string the lists out all the courses
	 */
	function getCourses($access_key) {
		$url = "https://canvas.instructure.com/api/v1/courses?access_token=$access_key&per_page=100";

		$curl = new Curl();
		$curl->get($url);
		
		$json = json_encode($curl->response);
        $json = json_decode($json);

       	return $json;
	}

	/**
	 * Gets the enrollment information for a particular course
	 * Useful for getting a students name, their grade, their id, etc.
	 * 
	 * @param $access_key the access token to access the api
	 * @param $courseId the id of the course to get information about
	 * @return json string that shows all the current enrollments for a course
	 */
	function getEnrollments($access_key, $courseId) {
		$url = "https://canvas.instructure.com/api/v1/courses/$courseId/enrollments?access_token=$access_key&per_page=100&type[]=StudentEnrollment";

		$curl = new Curl();
		$curl->get($url);

		$json = json_encode($curl->response);
		$json = json_decode($json);
		
		return $json;
	}

	/**
	 * Returns a json string of all the assignments for a particular
	 * course
	 * 
	 * @param $access_key the access token to access the api
	 * @param $courseId the id of the course to get information about
	 * @return json string for all the assignments in a course
	 */
	function getAssignments($access_key, $courseId) {     	
     	$url = "https://canvas.instructure.com/api/v1/courses/$courseId/analytics/student_summaries?access_token=$access_key&per_page=100";

     	$curl = new Curl();
		$curl->get($url);

		if($curl->error)
		{
			return array();
		}

		$json = json_encode($curl->response);
		$json = json_decode($json);

      	return $json;
	}

	/**
	 * Returns the student name given their id
	 * 
	 * @param $access_key the access token to access the api
	 * @param $id the user id
	 * @return json string for a student
	 */
	function getStudentName($access_key, $id) {
		$url = "https://canvas.instructure.com/api/v1/search/recipients?type=user&user_id=$id&access_token=$access_key&per_page=100";

		$curl = new Curl();
		$curl->get($url);

		$json = json_encode($curl->response);
		$json = json_decode($json);

		if($json[0]->name != null)
			return $json[0]->name;

		return "";
	}