<?php
require_once('dbutil.php');

class User
{
	private $_username;
	private $_password;
	private $_email;
	private $_DOB;
	private $_bIsLib;
	private $_first;
	private $_last;
        private $_country;

	public function __construct($user, $pass, $email, $DOB, $country, $bIsLib, $first, $last){
		$this->_username = $user;
		$this->_password = $pass;
		$this->_email    = $email;
		$this->_DOB    = $DOB;
		$this->_bIsLib   = $bIsLib;
		$this->_first    = $first;
		$this->_last     = $last;
                $this->_country  = $country;
	}

	public function __sleep(){
		return array('_username', '_email', '_DOB', '_country', '_bIsLib', '_first', '_last');
	}

	public function __wakeup(){
	}

	public function getUsername(){
		return $this->_username;
	}
	public function isLib(){
		return $this->_bIsLib;
	}
	public function getFirst(){
		return $this->_first;
	}

	public function getEmail(){
		return $this->_email;
	}

//	public static function viewLoanHistory($userName, $exact){
//		$conn = DB::getConnection();
//		echo "<TR class='info'><TH>Copy ID</TH><TH>Username</TH><TH>Due Date</TH><TH>Date Returned</TH><TR>";
//		if(!$userName){
//			return;
//		}
//		$result;
//		if($exact == "true"){
//			$result = mysqli_query($conn, "SELECT * from loanHistory where Groupnumber=10 and Username='".$userName."'");
//		}else{
//			$result = mysqli_query($conn, "SELECT * from loanHistory where Groupnumber=10 and Username LIKE '".$userName."%'");
//		}
//		while($row = mysqli_fetch_array($result)){
//			echo "<TR><TD><B>".$row['Copyid']."<B></TD><TD>".$row['Username']."</TD><TD>".$row['Duedate']."</TD><TD>".$row['Returnedondate']."</TD></TR>";
//		}
//	}
//
//	public static function createOrUpdateRentalRecord($userid, $copyid){
//		$conn = DB::getConnection();
//		$query = "INSERT INTO loanHistory (Groupnumber, Username, Copyid, Duedate) ".
//		    "VALUES (10, '".$userid."', ".$copyid.", DATE_ADD(CURDATE(), INTERVAL 5 DAY))";
//		$result = mysqli_query($conn, $query);
//		if(!$result) {
//			$query = "UPDATE loanHistory SET Duedate= DATE_ADD(CURDATE(), INTERVAL 5 DAY), Returnedondate = NULL ".
//					" WHERE Groupnumber=10 and Username='".$userid."' and Copyid=".$copyid;
//			$result = mysqli_query($conn, $query);
//		}
//		return $result;
//	}

//	public static function checkoutBook($userid, $copyid){
//		$res = self::createOrUpdateRentalRecord($userid, $copyid);
//		if($res)
//			Library::deleteCopyFromShelf($copyid);
//		else
//			echo "FAILED";
//	}

	public static function viewCheckedOutBook($userName){
		$conn = DB::getConnection();
                echo "<TR class='info'><TH>Movies Purchased</TH></TR>";
		echo "<TR class='info'><TH>MOVIE ID</TH><TH>MOVIE NAME</TH></TR>";
		if(!$userName){
			return;
		}
                
                $query = "SELECT m.movieid as movieid, m.name as moviename FROM sales s, movie m where m.movieid = s.movieid and userid = 256";
                $stid = oci_parse($conn, $query);
                //TODO: oci_bind_by_name($stid, ":id_bv", $userName);
                oci_execute($stid);
                //var_dump($stid);die;
		while($row = oci_fetch_array($stid)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['MOVIEID'])."</TD><TD>".htmlentities($row['MOVIENAME'])."</TD></TR>";
		}
                
                
                oci_free_statement($stid);
                oci_close($conn);
	}
        
//        public static function recommendedMovies($userName){
//		$movies = self::getrecommendedMovies($userName);
//	
//		if(sizeof($movies) > 0) {
//			$rowStr = "<TR>";
//			foreach($movies as &$movie) {
//				$rowStr .= "<TD class='book' styel='border-top:none'>";
//                                $rowStr .= "<img id=\"checkedOut".$movie->getCopyId()."\"class='span1' src='". $movie->getPoster() ."' alt='". $movie->getTitle() ."' />"; // width=50% height=175
//				$rowStr .= "<input type='hidden' value='". $movie->getCopyID() ."'>";
//				$rowStr .= "</TD>";
//			}
//
//			$rowStr .= "</TR>";
//
//			if(strpos($rowStr, "class='book'") == true) {
//				echo $rowStr;
//			}
//		}
//	}
        

//	public static function returnBook($userid, $copyid){
//		$conn = DB::getConnection();
//
//		// Update the loanHistory table
//		$query = "UPDATE loanHistory SET Returnedondate=CURDATE() ".
//			"WHERE Groupnumber=10 and Username='".$userid."' and Copyid=".$copyid;
//		$updateCount = mysqli_query($conn, $query);
//		// TODO need to check for null result and avoid adding to shelf in that case
//		if($updateCount == false){
//			echo "Error: ". mysqli_error($conn);
//			return;
//		}
//		// Now put the book back on the shelf
//		Library::addCopyToShelf($copyid);
//	}

	public static function doesUserExist($email){
		$exists = false;
		$conn = DB::getConnection();

		$statement = oci_parse($conn, "SELECT * FROM users where LOGINID = :email");
                oci_bind_by_name($statement, ':email', $email);
                oci_execute($statement);

                if (($row = oci_fetch_object($statement))) 
                {
                    $exists = true;
                }
		return $exists;
	}
        
	public static function checkUserAndPass($email, $pass){
		$success = false;
		$conn = DB::getConnection();
                $statement = oci_parse($conn, "SELECT * FROM users where LOGINID = :email and password = :dbpass");
                oci_bind_by_name($statement, ':email', $email);
                oci_bind_by_name($statement, ':dbpass', md5($pass));
                oci_execute($statement);
		
		if (($row = oci_fetch_object($statement)))
			$success = true;

		return $success;
	}

	public static function isLibrarian($uname){
		$conn = DB::getConnection();
		$bLib;

		$result = mysqli_query($conn, "SELECT Librarian from users ".
			"where Groupnumber=10 and username='". $uname ."'");
		if($row = mysqli_fetch_array($result))
			$bLib = $row['Librarian'];

		return $bLib;
	}

	public static function createUser($uname,$passhash,$email,$DOB,$country,$bLib,$first,$last){
		if(self::doesUserExist($email)){
			echo "User " . $email . " already exists.<BR>";
			return;
		}
		$con = DB::getConnection();
		// Group#, Username, password, email, DOB, country, lib?, First, Last
		
                $query = "INSERT INTO users (userid, Password, LoginId, firstname, lastname, usertype, DOB) VALUES (210006, :pwd, :email, :fname, :lname, 'Customer', to_date(:DOB,'MM-DD-YYYY'))";
        $stid = oci_parse($con, $query);

        //oci_bind_by_name($stid, ':userid', 10000);
        oci_bind_by_name($stid, ':pwd', $passhash);
        oci_bind_by_name($stid, ':email', $email);
        oci_bind_by_name($stid, ':fname', $first);
        oci_bind_by_name($stid, ':lname', $last);
        oci_bind_by_name($stid, ':utype', $bLib);
        oci_bind_by_name($stid, ':DOB', $DOB);
        
        oci_execute($stid);
        
        
        
        $query1 = "INSERT INTO CUSTOMER (USERID, REGISTRATIONDATE, COUNTRY) values (210006, SYSDATE, :dbcountry)";
        $stid1 = oci_parse($con, $query1);

        oci_bind_by_name($stid1, ':dbcountry', $country);       
        oci_execute($stid1);
        //oci_commit($con);  
        
        if(!oci_error()){
            //oci_commit($con);     
        }
        else
        {
            echo ("Insert Failed: ");
            echo ("<br/>");
        }         
        
        oci_free_statement($stid);
        oci_free_statement($stid1);
        oci_close($con);
        return new User($uname, $passhash, $email, $DOB, $country, $bLib, $first, $last);
	}

	public static function getUser($email){
		$user = null;
		$conn = DB::getConnection();
                
                $statement = oci_parse($conn, "SELECT * FROM users where LOGINID = :email");
                oci_bind_by_name($statement, ':email', $email);
                oci_execute($statement);

		if($row = oci_fetch_array($statement, OCI_ASSOC)){
                    
			$userid   = $row["USERID"];
			$pass   = $row["PASSWORD"];
			$email  = $row["LOGINID"];
			$DOB  = $row["DOB"];
                        $country = "United States";
			$bIsLib = $row["USERTYPE"];
			$first  = $row["FIRSTNAME"];
			$last   = $row["LASTNAME"];
			$user = new User($userid, $pass, $email, $DOB, $country, $bIsLib, $first, $last); 
                        //var_dump($user);die;                        
                        
		}
		return $user;
	}

	public static function hasRentalDueToday($userName){
		$conn = DB::getConnection();
		if(!$userName){
			return;
		}
		$result = mysqli_query($conn, "SELECT * FROM loanHistory where Groupnumber=10 and Duedate=CURDATE() and Username='".$userName."' and Returnedondate is NULL");
		while($row = mysqli_fetch_array($result)){
			echo "PASSED";
			return;
		}
		echo "FAILED";
		return;
	}
}
?>