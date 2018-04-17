<?php

require_once('/home/ncorbing/db.php');

class UserDB
{
	private $_username;
	private $_password;

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
}