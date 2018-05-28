<?php 

	use \Curl\Curl;

	/**
	 * Gets a json string representation of all the courses
	 * Useful for getting the course id and the name
	 * 
	 * @param $access_key the access token to access the api
	 */
	function getCourses($access_key)
	{
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
	 */
	function getEnrollments($access_key, $courseId)
	{
		$url = "https://canvas.instructure.com/api/v1/courses/$courseId/enrollments?access_token=$access_key&per_page=100&type[]=StudentEnrollment";

		$curl = new Curl();
		$curl->get($url);

		$json = json_encode($curl->response);
		$json = json_decode($json);
		
		return $json;
	}

	function getAssignments($access_key, $courseId)
	{     	
     	$url = "https://canvas.instructure.com/api/v1/courses/$courseId/analytics/student_summaries?access_token=$access_key&per_page=100";

     	$curl = new Curl();
		$curl->get($url);

		$json = json_encode($curl->response);
		$json = json_decode($json);

      	return $json;
	}

	function getStudentName($access_key, $id)
	{
		$url = "https://canvas.instructure.com/api/v1/search/recipients?type=user&user_id=$id&access_token=$access_key&per_page=100";

		$curl = new Curl();
		$curl->get($url);

		$json = json_encode($curl->response);
		$json = json_decode($json);

      	return $json[0]->name;
	}