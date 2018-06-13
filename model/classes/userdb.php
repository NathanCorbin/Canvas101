<?php
require_once('/home/ncorbing/db.php');
//require_once('/home/siqbalgr/database.php');

/**
 * Class for the user database
 * Gets a connection to the database, and allows us to
 * pull out the api access_key for a user
 * @author NathanCorbin
 * @version 1.0
 */
class UserDB
{
	private $_username;
	private $_password;
	private $_accessKey;

	/**
	 * Constructor, initializes the username and password and sets 
	 * up a connection to the database
	 * @return a new PDO object
	 */
	function __construct()
	{
		$_username = $_password = "";

		try {
			global $dbh;
		    $dbh = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
		    return $dbh;
		}
		catch(PDOException $e) {
        	echo $e->getMessage();
        	return;
    	}
	}

	/**
	 * Gets the access from the database given a username
	 * @param $username the username of the person with the key
	 * @return the api access_key
	 */
	public static function getAccessKey($username)
	{
		global $dbh;

		$query = "SELECT access_key FROM users WHERE username = :username";

		$statement = $dbh->prepare($query);
		$statement->execute(['username' => $username]);

		$result = $statement->fetch();

		return $result[0];
	}

	/**
	 * Logs a user in given the username and password. Checks 
	 * the username and password are valid
	 * 
	 * @param $username the user's username
	 * @param $password the user's password
	 * @return boolean true if the credentials were correct, false otherwise
	 */
	public static function login($username, $password)
	{
		global $dbh;

		$query = "SELECT * from users WHERE username = :username";
		$params = array(':username' => array($username => PDO::PARAM_STR));

		$statement = $dbh->prepare($query);

		foreach($params as $param => $value) {
			foreach($value as $variable => $type) {
				$statement->bindValue($param, $variable, $type);
			}
		}

		$success = $statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);

		$result = $result[0];

		return $result['password'] == sha1($password);
	}

	/**
	 * Returns the server's internal email address
	 * 
	 * @return string the email of the server
	 */
	public static function getEmailAddress()
	{
		global $dbh;

		$query = "SELECT email FROM admin";

		$statement = $dbh->prepare($query);
		$statement->execute();

		$result = $statement->fetch();

		return $statement->fetchObject()->email;
	}

	/**
	 * Returns the server's email host name
	 * 
	 * @return string the server's email host
	 */
	public static function getEmailHost()
	{
		global $dbh;

		$query = "SELECT `host` FROM `admin`";

		$statement = $dbh->prepare($query);
		$statement->execute();

		$result = $statement->fetch();

		return $statement->fetchObject()->host;
	}

	/**
	 * Returns the server's email password
	 * 
	 * @return string the password for the server email
	 */
	public static function getEmailPassword()
	{
		global $dbh;

		$query = "SELECT `password` FROM `admin`";

		$statement = $dbh->prepare($query);
		$statement->execute();

		$result = $statement->fetch();

		return $statement->fetchObject()->password;
	}

	/**
	 * Checks if the selected user is an admin or not
	 * 
	 * @param string $username the username to check if is an admin
	 * @return boolean true if admin, false if not
	 */
	public static function isAdmin($username)
	{
		global $dbh;

		$query = "SELECT * FROM users WHERE username = :username AND admin_status = 1";

		$statement = $dbh->prepare($query);
		$statement->execute(['username' => $username]);

		$result = $statement->fetch();

		return !empty($result);
	}

	public static function getAllUsers()
	{
		global $dbh;

		$query = "SELECT username, access_key FROM users";

		$statement = $dbh->prepare($query);
		$statement->execute();

		$result = $statement->fetchAll();

		return $result;
	}

	public static function addUser($username, $password, $access_key = "")
	{
		global $dbh;

		$query = "INSERT INTO users(username, password, access_key, admin_status) VALUES (:username, sha1(:password), :access_key, 0)";
		$statement = $dbh->prepare($query);

		return $statement->execute(array('username' => $username, 'password' => $password, 'access_key' => $access_key));
	}

	public static function deleteUser($username) 
	{
		global $dbh;

		$query = "DELETE FROM users where username = :username";
		$statement = $dbh->prepare($query);

		return $statement->execute(array('username' => $username));
	}

	public static function updateApiKey($username, $access_key) 
	{
		global $dbh;

		$query = "UPDATE users SET access_key = :access_key WHERE username = :username";
		$statement = $dbh->prepare($query);

		return $statement->execute(array('username' => $username, 'access_key' => $access_key));
	}
}