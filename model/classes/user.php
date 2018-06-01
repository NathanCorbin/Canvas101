<?php

	/**
	 * User class. Creates a new user with a username and password.
	 * Can set a new access_key, retrieve one, and get the username
	 * @author NathanCorbin
	 * @version 1.0
	 */
	class User
	{
		private $_username;
		private $_password;

		/**
		 * Constructor - Sets the username and password for the user
		 * @param $username the username for the user
		 * @param $password the users password
		 */
		public function __construct($username, $password)
		{
			$this->username = $username;
			$this->password = $password;
		}

		/**
		 * Sets the access key for this user
		 * TODO: update users accesskey in the DB
		 * @param $accessKey the api accesskey
		 */
		public function setAccessKey($accessKey)
		{
			$this->accessKey = $accessKey;
		}

		/**
		 * Gets the users api access key
		 * 
		 * @return apikey from the database
		 */
		public function getAccessKey()
		{	
			new UserDB();
			return UserDB::getAccessKey($this->username);
		}

		/**
		 * Returns the username
		 * @return the username for this user
		 */ 
		public function getUsername()
		{
			return $this->username;
		}

		/**
		 * Checks if the current user is an admin
		 * @return boolean true if the user is an admin, false if not
		 */
		public function isAdmin()
		{
			new UserDB();
			return UserDB::isAdmin($this->username);
		}
	}