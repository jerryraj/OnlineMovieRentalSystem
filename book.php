<?php
require_once('dbutil.php');
require_once('library.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of book
 *
 * @author shweta
 */
class book {
    	private $_title;
	private $_poster;
	private $_copyID;

	function __construct($copyID, $title, $poster){
		$this->_title  = $title;
		$this->_poster = $poster;
		$this->_copyID = $copyID;
	}
        
	public function getTitle(){
		return $this->_title;
	}
        
        public function getPoster(){
		return $this->_poster;
	}

	public function getCopyID(){
		return $this->_copyID;
	}


	public static function getBookFromDB($bookID){
		$conn = DB::getConnection();

		$result = mysqli_query($conn, 
			"SELECT * from books ".
			"INNER JOIN bookscopy ".
			"ON books.Bookid=bookscopy.Bookid ".
			"WHERE Groupnumber=10");

		$book = null;
		if($row = mysqli_fetch_array($result)){
			$title  = $row['Booktitle'];
			$author = $row['Author'];
			$copyID = $row['Copyid'];
			$bookID = $row['Bookid'];
			$book = new Book($title, $author, $copyID, $bookID);
		}
		else
			echo "COULDNT GET BOOK IN DB<BR>";

		return $book;
	}
}
?>
