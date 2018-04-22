<?php

	class User
	{
		private $_username;
		private $_password;

		public function __construct($username, $password)
		{
			$this->username = $username;
			$this->password = $password;
		}

		public function setAccessKey($accessKey)
		{
			$this->accessKey = $accessKey;
		}

		public function getAccessKey()
		{	
			new UserDB();
			return UserDB::getAccessKey($this->username);
		}

		public function getUsername()
		{
			return $this->username;
		}
	}