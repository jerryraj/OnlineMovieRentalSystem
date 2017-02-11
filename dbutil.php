<?php

// Constants for storing database credentials
$dbUsername = "amitabh";
$dbPassword = "ashu1995";
$dbServer = "oracle.cise.ufl.edu"; 
$dbName   = "orcl";
$connString = "oracle.cise.ufl.edu/orcl";

class DB
{
	static $conn = null;

	//connection to the database
	function getConnection(){
		if(is_resource(self::$conn))
			return self::$conn;

		global $dbUsername, $dbPassword, $connString, $dbServer, $dbName;
		self::$conn = oci_connect($dbUsername, $dbPassword, $connString);
		/*if(mysqli_connect_errno()){
			die("Failed to connect to MYSQL: " . mysqli_connect_errno());
			return;
		}*/
                if (!self::$conn) {
                     $m = oci_error();
                     exit('Failed to connect to Oracle: ' . $m['message']);
                }
		return self::$conn;
	}
}