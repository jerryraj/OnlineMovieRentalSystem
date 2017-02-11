<?php
require_once('library.php');
require_once('user.php');
require_once('rating.php');

//session_start();
 
if(isset($_GET['function'])){
	$function = $_REQUEST['function'];
       
  //echo "here";die;      
if($function === 'rows'){
    
		Library::countRows();
		return;
	}
        if($function === 'countNewUsers'){
            $input1 = $_GET['input1'];
            $input2 = $_GET['input2'];
		Library::countNewUsers($input1,$input2);
		return;
	}
        if($function === 'checkRevenueDT'){
            $input1 = $_GET['input1'];
            $input2 = $_GET['input2'];
		Library::checkRevenueDT($input1,$input2);
		return;
	}
        
        if($function === 'popularGenre'){
            $input1 = $_GET['input1'];
            $input2 = $_GET['input2'];
		Library::popularGenre($input1,$input2);
		return;
	}
        if($function === 'revenueAge'){
            $input1 = $_GET['input1'];
            $input2 = $_GET['input2'];
		Library::revenueAge($input1,$input2);
		return;
	}
        if($function === 'popularLang'){
		Library::popularLang();
		return;
	}
        if($function === 'popularRegion'){
		Library::popularRegion();
		return;
	}
        if($function === 'popularMovGenre'){
		Library::popularMovGenre();
		return;
	}
        if($function === 'inactiveUsers'){
		Library::inactiveUsers();
		return;
	}
        if($function === 'ranking'){
		Library::ranking();
		return;
	}
        if($function === 'mostRented'){
		Library::mostRented();
		return;
	}
        
        
	if($function === 'showLib'){
            $userName = $_GET['userID'];
		Library::showLib($userName);
		return;
	}
	if($function === 'showSearch') {
		if($_GET['type'] === "Title") {
			Library::searchMoviesByTitle($_GET['input']);
		} else if($_GET['type'] === "Genre") {
                        Library::searchMoviesByGenre($_GET['input']);
		} 
                else if($_GET['type'] === "Language") {
                        Library::searchMoviesByLanguage($_GET['input']);
		} 
                else if($_GET['type'] === "Region") {
                        Library::searchMoviesByRegion($_GET['input']);
		} else if($_GET['type'] === "Actor") {
                        Library::searchMoviesByActor($_GET['input']);
		}
                else if($_GET['type'] === "Director") {
                        Library::searchMoviesByDirector($_GET['input']);
		}
                elseif($_GET['type'] === "Era") {
                        Library::searchMoviesByEra($_GET['input']);                
                }
                
                
		return;
	}
	if($function === 'addBook'){
		Library::addBook($_GET['title'], $_GET['author'], $_GET['qty']);
		return;
	}
	if($function === 'removeBook'){
		Library::deleteCopy($_GET['copyID']);
		return;
	}
	if($function == 'getBookInfo'){
		$book = Library::getBook($_GET['copyID']);
		$rating = new rating();
		$data["avg"] = $rating->getRatings($book->getCopyID());
		$ret =  "<h1 style='color:#333333'>".$book->getTitle()."</h1>".
		     "<img src='".$book->getPoster()."' alt='".$book->getTitle()."' border=10 style='width:500;'/>".
			// "<BR><BR>Director:\t".$book->getAuthor().
			// "<BR>Movie ID:\t".$book->getID().
			 "<BR>Copy ID:\t".$book->getCopyID().
			 "<div id=\"r2\" class=\"rate_widget\">";
		if($data["avg"] >= 1){
			$ret = $ret."<div class=\"star_1 ratings_stars ratings_over\"></div>";
		}else{
			$ret = $ret."<div class=\"star_1 ratings_stars\"></div>";
		}
		if($data["avg"] >= 2){
			$ret = $ret."<div class=\"star_2 ratings_stars ratings_over\"></div>";
		}else{
			$ret = $ret."<div class=\"star_2 ratings_stars\"></div>";
		}
		if($data["avg"] >= 3){
			$ret = $ret."<div class=\"star_3 ratings_stars ratings_over\"></div>";
		}else{
			$ret = $ret."<div class=\"star_3 ratings_stars\"></div>";
		}
		if($data["avg"] >= 4){
			$ret = $ret."<div class=\"star_4 ratings_stars ratings_over\"></div>";
		}else{
			$ret = $ret."<div class=\"star_4 ratings_stars\"></div>";
		}
		if($data["avg"] >= 5){
			$ret = $ret."<div class=\"star_5 ratings_stars ratings_over\"></div>";
		}else{
			$ret = $ret."<div class=\"star_5 ratings_stars\"></div>";
		}
		//$ret = $ret."<div>Number of ratings: ".$data["numRatings"]."</div><BR>";
		$ret = $ret."</div><BR>";
		echo $ret;
	 	return;
	}
	if($function == 'checkoutBook'){
		User::checkoutBook($_GET['userID'], $_GET['copyID']);
		return;
	}

	if($function == 'returnBook'){
		$book = Book::getBookInfoByCopyID($_GET['copyID']);
		$bookTitle = str_replace("_", " ", $book["Booktitle"]);
		$ret = "<div>Would you please rate " . $bookTitle . "?</div>";
		$ret = $ret."<div id=\"".$book["Bookid"]."\" class=\"rate_widget\">";
		$ret = $ret."<div class=\"star_1 ratings_stars\"></div>";
		$ret = $ret."<div class=\"star_2 ratings_stars\"></div>";
		$ret = $ret."<div class=\"star_3 ratings_stars\"></div>";
		$ret = $ret."<div class=\"star_4 ratings_stars\"></div>";
		$ret = $ret."<div class=\"star_5 ratings_stars\"></div>";
		$ret = $ret."</div>";
		echo $ret;
		User::returnBook($_GET['userID'], $_GET['copyID']);
		return;
	}
	if($function == 'viewLoans'){
		$userName = $_GET['user'];
		$exact = $_GET['exact'];
		User::viewLoanHistory($userName, $exact);
		return;
	}
	if($function == 'viewCheckOut'){
            //echo "here";die;
		$userName = $_GET['userID'];
		User::viewCheckedOutBook($userName);
		return;
	}

        
 
	if($function == 'validate'){
		$bookName = $_GET['bookName'];
		$author   = $_GET['author'];
		$qty      = $_GET['qty'];
		if(!ctype_digit($qty)){
			echo "Invalid qty: " .$qty;
			return;
		}
		echo "PASSED";
		return;
	}
	if($function == 'email'){
		if(!isset($_SESSION['notified'])){
			$userEmail = $_GET['userEmail'];
			if(mail($userEmail,
					'[Unified Rental Service] Upcoming rental deadline',
					'One of your rentals is due today, make sure you bring that back to us!'))
			{
				$_SESSION['notified'] = true;
				echo "You have a rental due today!\nAn email reminder has been sent to:\n".$userEmail;
				return;
			}
			echo "Unable to send an email reminder to your email address at\n".$userEmail;
			return;
		} else {
			return;
		}
	}
	if($function == 'checkDueToday'){
		$userName = $_GET['userID'];
		User::hasRentalDueToday($userName);
		return;
	}	
	if($function == 'getRatings'){
		$bookID = $_GET['BookID'];
		$rating = new rating();
		$rating->getRatings($bookID);
	}
	if($function == 'vote'){
		$bookID = $_GET['BookID'];
		$score = $_GET['Score'];
		$rating = new rating();
		$rating->updateRating($bookID, $score);
	}
	if($function == 'requestNotification') {
		$bookId = Book::getBookId(str_replace(" ", "_", $_GET['bookTitle']));
		if(!isset($bookId)) {
			echo "ERROR"; 
			return;
		}
		return Library::addUserToPendingNotification($_GET['bookTitle'], $_GET['userID']);
	}
	if($function === 'showSearch') {
		Library::searchMoviesByTitle($_GET['title']);
		return;
	}
}
?>