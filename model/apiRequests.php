<?php 

	use \Curl\Curl;

	function getCourseIDs($access_key)
	{
		$url = "https://canvas.instructure.com/api/v1/courses?access_token=".$access_key;

		$curl = new Curl();
		$curl->get($url);

		$json = json_encode($curl->response);
		$data = json_decode($json);

		$courseIds = array();

		foreach($data as $key => $jsons) {
			foreach($jsons as $key => $value) {
				if($key == "id") {
					array_push($courseIds, $value);
				}
			}
		}

		return $courseIds;
	}

	function getData($access_key)
	{
		$courseIds = getCourseIDs($access_key);
		$userIds = array();

		foreach($courseIds as $courseId) 
		{
			$url = "https://canvas.instructure.com/api/v1/courses/".$courseId."/enrollments?access_token=".$access_key;

			$curl = new Curl();
			$curl->get($url);

			$json = json_encode($curl->response);
			$data = json_decode($json);

			foreach($data as $key => $jsons) 
			{
				foreach($jsons as $key => $value) 
				{
					if($key == "user")
					{
						array_push($userIds, $value->name);
					}
					
					if($key == "grades")
					{
						array_push($userIds, $value->unposted_current_score);
					}
				}
			}
		}

		return $userIds;
	}

	function getAssignments($access_key)
	{
		
	}