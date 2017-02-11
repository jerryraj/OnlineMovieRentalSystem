<?php
require_once('dbutil.php');
require_once('book.php');
//require_once('shelf.php');
//require_once('notification.php');

class Library
{
	const SHELF_COUNT = 10;

        public static function countRows(){
		$conn = DB::getConnection();
		
                $query = "select get_rows from dual" ;
                $stid1 = oci_parse($conn, $query);
                oci_execute($stid1);
                
		if($row = oci_fetch_array($stid1)){
                    //var_dump($row);die;
                    echo "<TR><TD><h1>Total no. of rows in DB:</h1></TD><TD><h1>".htmlentities($row['GET_ROWS'])."</h1></TD></TR>";
		}else{
                return null;}              
                               
                oci_free_statement($stid1);
                oci_close($conn);
	}
        
	public static function showLib($userName){
		$movies = self::getPopularMovies($userName);

	
		if(sizeof($movies) > 0) {
			//$rowStr = "<TR>";
                        $rowStr = "<TR class='info'><TH>Popular Movies </TH></TR>";
                        //$rowStr .= "<h1>Popular Movies </h1></TR>";
                        $rowStr .= "<TR>";
			foreach($movies as &$movie) {
				$rowStr .= "<TD class='book' styel='border-top:none'>";
                                $rowStr .= "<img id=\"checkedOut".$movie->getCopyId()."\"class='span1' src='". $movie->getPoster() ."' alt='". $movie->getTitle() ."' />"; // width=50% height=175
				$rowStr .= "<input type='hidden' value='". $movie->getCopyID() ."'>";
				$rowStr .= "</TD>";
			}

			$rowStr .= "</TR>";
                }
                        
                        $moviesa = self::getrecommendedMovies($userName);
	
		if(sizeof($moviesa) > 0) {
//			$rowStr .= "<TR>";
//                        $rowStr .= "<h1>Recommened Movies </h1></TR>";
                        $rowStr .= "<TR class='info'><TH>Recommended Movies </TH></TR>";
                        $rowStr .= "<TR>";
			foreach($moviesa as &$moviea) {
				$rowStr .= "<TD class='book' styel='border-top:none'>";
                                $rowStr .= "<img id=\"checkedOut".$moviea->getCopyId()."\"class='span1' src='". $moviea->getPoster() ."' alt='". $moviea->getTitle() ."' />"; // width=50% height=175
				$rowStr .= "<input type='hidden' value='". $moviea->getCopyID() ."'>";
				$rowStr .= "</TD>";
			}

			$rowStr .= "</TR>";
			
		}
                if(strpos($rowStr, "class='book'") == true) {
				echo $rowStr;
			}
	}

	public static function searchMoviesByTitle($title) {
				$conn = DB::getConnection();
                echo "<TR class='info'><TH>SEARCHED MOVIES </TH></TR>";
		echo "<TR class='info'><TH>MOVIE </TH><TH>DESCRIPTION </TH></TR>";
		if(!$title){
			return;
		}
                
                $query = "Select movieid, name, synopsis from movie where lower(name) like lower(:id_bv)";
                $stid = oci_parse($conn, $query);
                oci_bind_by_name($stid, ":id_bv", $title);
                oci_execute($stid);
                
		while($row = oci_fetch_array($stid)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['NAME'])."</TD><TD>".htmlentities($row['SYNOPSIS'])."</TD></TR>";
		}
                
                
                oci_free_statement($stid);
                oci_close($conn);
	
	}

        
        public static function getPopularMovies() {
		$conn = DB::getConnection();
		//$query = "SELECT movieid, name, poster FROM MOVIE WHERE rownum <= 20";
                $query = "Select m.movieid,m.name,m.poster, avg(r.score) as avgscore ,count(userid) as users  from movie m, rating r ".
                " where m.movieid = r.movieid group by m.movieid,m.name,m.poster order by count(userid) desc, avgscore desc";
                $stid = oci_parse($conn, $query);
                //oci_bind_by_name($stid, ":id_bv", $wisherID);
                oci_execute($stid);
                
                $matchingBooks = array();
                $i = 0;
		while(($row = oci_fetch_array($stid)) && ($i <= 10)) {
			//create an array of books 
                        
			array_push($matchingBooks, new Book($row['MOVIEID'], $row['NAME'], $row['POSTER']));
                        $i++;
		} 
                oci_free_statement($stid);
                oci_close($conn);
		return $matchingBooks;
	}
        
        public static function getrecommendedMovies($userName){
		$conn = DB::getConnection();
                //$query = 'select m.movieid as movieid,m.name as name,m.poster as poster, type, avg(r.score) as avgscore ,count(userid) as users from movie m, rating r, genre g where m.movieid = r.movieid and g.movieid = r.movieid and trim(g.type) = (select trim(type) from (select trim(type) as type, count(*) as c from sales,genre where userid = 1400 and sales.movieid = genre.movieid group by trim(type) ) where rownum < 2) group by m.movieid,m.name,type, poster order by count(userid) desc, avgscore desc';
		$query = "select m.movieid as movieid,m.name as name, m.poster as poster, type, avg(r.score) as avgscore ,".
                        "count(userid) as users from movie m, rating r, genre g where m.movieid = r.movieid and g.movieid = r.movieid ".
                        "and trim(g.type) = (select trim(type) from (select trim(type) as type, count(*) as c from sales, genre ".
                        "where userid = 1400 and sales.movieid = genre.movieid group by trim(type) ) where rownum < 2) ".
                        "group by m.movieid, m.name, type, poster order by count(userid) desc, avgscore desc";
                $stid = oci_parse($conn, $query);
                //TODO: oci_bind_by_name($stid, ":id_bv", $userName);
                //var_dump($stid);die;
                oci_execute($stid);
                $matchingBooks = array();
                $si = 0;
		while(($row = oci_fetch_array($stid))  && ($si <= 10)) {
			//create an array of books 
                        //var_dump($row);die;
			array_push($matchingBooks, new Book($row['MOVIEID'], $row['NAME'], $row['POSTER']));
                        $si++;
		} 
                oci_free_statement($stid);
                oci_close($conn);
		return $matchingBooks;
	}

	public static function getBook($copyID){
		$conn = DB::getConnection();
		$query = "Select movieid, name, poster from movie where movieid like lower(:id_bv)";
                $stid = oci_parse($conn, $query);
                oci_bind_by_name($stid, ":id_bv", $copyID);
                oci_execute($stid);
                
		if($row = oci_fetch_array($stid)){
                    //var_dump($row);die;
                    return new Book($row['MOVIEID'], $row['NAME'], $row['POSTER']);
		}else{
                return null;}
                
                
                oci_free_statement($stid);
                oci_close($conn);
	}
        public static function countNewUsers($input1,$input2){
		$conn = DB::getConnection();
		$query = "select count(*) as cnt from customer where registrationdate <= to_date(:id_to, 'MM-DD-YYYY') and registrationdate >= to_date(:id_from, 'MM-DD-YYYY')";
                //$query = "select count(*) as cnt from customer where registrationdate <= to_date('03-31-2016', 'MM-DD-YYYY') and registrationdate >= to_date('03-01-2016', 'MM-DD-YYYY')";
                
                $stid = oci_parse($conn, $query);
                oci_bind_by_name($stid, ":id_from", $input1);
                oci_bind_by_name($stid, ":id_to", $input2);
                oci_execute($stid);
                
		if($row = oci_fetch_array($stid)){
                    
                    echo "<TR><TD><h1>Newly Added Customers</h1></TD><TD><h1>".htmlentities($row['CNT'])."</h1></TD></TR>";
		}else{
                return null;}
                
                
                oci_free_statement($stid);
                oci_close($conn);
	}
        public static function checkRevenueDT($input1,$input2){
		$conn = DB::getConnection();
		
                $aquery="select sum(amountpaid) as sum from sales where buyingdate <= to_date(:id_to, 'MM-DD-YYYY') and buyingdate >= to_date(:id_from, 'MM-DD-YYYY')";
                $stid = oci_parse($conn, $aquery);
                oci_bind_by_name($stid, ":id_from", $input1);
                oci_bind_by_name($stid, ":id_to", $input2);
                oci_execute($stid);
		if($row = oci_fetch_array($stid)){
                    //var_dump($row);die;
                    echo "<TR><TD><h1>Total Revenue:</h1></TD><TD><h1>".htmlentities($row['SUM'])."</h1></TD></TR>";
		}else{
                return null;}
                
                $query = "select s.movieid, m.name, sum(amountpaid) as totalgross from sales s left join movie m ".
                "on s.movieid = m.movieid where s.buyingdate <= to_date(:id1_to, 'MM-DD-YYYY') and ". 
                "s.buyingdate >= to_date(:id1_from, 'MM-DD-YYYY') ".
                "group by s.movieid, m.name order by totalgross desc";
                $stid1 = oci_parse($conn, $query);
                oci_bind_by_name($stid1, ":id1_from", $input1);
                oci_bind_by_name($stid1, ":id1_to", $input2);
                oci_execute($stid1);
                echo "<TR class='info'><TH>MOVIE ID</TH><TH>MOVIE NAME</TH><TH>Revenue</TH></TR>";
		while($row = oci_fetch_array($stid1)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['MOVIEID'])."</TD><TD>".htmlentities($row['NAME'])."</TD><TD>".htmlentities($row['TOTALGROSS'])."</TD></TR>";
		}               
                               
                oci_free_statement($stid1);
                oci_free_statement($stid);
                oci_close($conn);
	}
        public static function popularGenre($input1,$input2){
		$conn = DB::getConnection();
		
                $query = "select trim(g.type) as cat  ,avg(score) as avg_score, count(r.userid) as tusers from rating r, genre g , users u ".
                "where r.movieid = g.movieid and r.userid = u.userid and (MONTHS_BETWEEN (SYSDATE, u.dob) / 12 ) >= :id1_from ". 
                "and (MONTHS_BETWEEN (SYSDATE, u.dob) / 12) <= :id1_to group by trim(g.type) order by tusers * avg_score desc" ;
                $stid1 = oci_parse($conn, $query);
                oci_bind_by_name($stid1, ":id1_from", $input1);
                oci_bind_by_name($stid1, ":id1_to", $input2);
                oci_execute($stid1);
                echo "<TR class='info'><TH>popular Genre</TH><TH>Avg Rating</TH><TH>No. of Ratings</TH></TR>";
		while($row = oci_fetch_array($stid1)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['CAT'])."</TD><TD>".htmlentities($row['AVG_SCORE'])."</TD><TD>".htmlentities($row['TUSERS'])."</TD></TR>";
		}               
                               
                oci_free_statement($stid1);
                oci_close($conn);
	}
        public static function revenueAge($input1,$input2){
		$conn = DB::getConnection();
		
                $aquery="select  sum(amountpaid) as revenuegenerared from sales s inner join users u on s.userid = u.userid ".
                "where (MONTHS_BETWEEN (SYSDATE, u.dob) / 12 ) >= :id_from and (MONTHS_BETWEEN (SYSDATE, u.dob) / 12) <= :id_to";
                $stid = oci_parse($conn, $aquery);
                oci_bind_by_name($stid, ":id_from", $input1);
                oci_bind_by_name($stid, ":id_to", $input2);
                oci_execute($stid);
		if($row = oci_fetch_array($stid)){                    
                    echo "<TR><TD><h1>Total Revenue:</h1></TD><TD><h1>".htmlentities($row['REVENUEGENERARED'])."</h1></TD></TR>";
		}else{
                return null;}
//TODO:                
//                $query = "select s.movieid, m.name, sum(amountpaid) as totalgross from sales s, movie m, users u ".
//                "where s.movieid = m.movieid and (MONTHS_BETWEEN (SYSDATE, u.dob) / 12 ) >= :id1_from and ".
//                "(MONTHS_BETWEEN (SYSDATE, u.dob) / 12) <= :id1_to group by s.movieid, m.name order by totalgross desc";
//                $stid1 = oci_parse($conn, $query);
//                oci_bind_by_name($stid1, ":id1_from", $input1);
//                oci_bind_by_name($stid1, ":id1_to", $input2);
//                oci_execute($stid1);
//                echo "<TR class='info'><TH>MOVIE ID</TH><TH>MOVIE NAME</TH><TH>Revenue</TH></TR>";
//		while($row = oci_fetch_array($stid1)){
//                    //var_dump($row);die;
//                    echo "<TR><TD>".htmlentities($row['MOVIEID'])."</TD><TD>".htmlentities($row['NAME'])."</TD><TD>".htmlentities($row['TOTALGROSS'])."</TD></TR>";
//		}               
                               
                //oci_free_statement($stid1);
                oci_free_statement($stid);
                oci_close($conn);
	}
        
        public static function inactiveUsers(){
		$conn = DB::getConnection();
		
                $query = "select ceil((select count(*) from customer where  MONTHS_BETWEEN (SYSDATE, lastlogindate) > 3)*100 ".
                "/ (select count(*) from customer)) as percentInactive from dual" ;
                $stid1 = oci_parse($conn, $query);
                oci_execute($stid1);
                
		if($row = oci_fetch_array($stid1)){
                    //var_dump($row);die;
                    echo "<TR><TD><h1>Percentage of inactive users from last 3 months:</h1></TD><TD><h1>".htmlentities($row['PERCENTINACTIVE'])."</h1></TD></TR>";
		}else{
                return null;}              
                               
                oci_free_statement($stid1);
                oci_close($conn);
	}
        
        public static function ranking(){
		$conn = DB::getConnection();
		
                $query = "select * from (select s.userid,Firstname, lastname, sum(amountpaid) as moneyspent from sales s inner join users u on s.userid = u.userid ".
                "group by s.userid, firstname, lastname order by moneyspent desc) where rownum < 100" ;
                $stid1 = oci_parse($conn, $query);
                oci_execute($stid1);
                echo "<TR class='info'><TH>User ID</TH><TH>First Name</TH><TH>Last Name</TH><TH>Money Spent</TH></TR>";
		while($row = oci_fetch_array($stid1)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['USERID'])."</TD><TD>".htmlentities($row['FIRSTNAME'])."</TD><TD>".htmlentities($row['LASTNAME'])."</TD><TD>".htmlentities($row['MONEYSPENT'])."</TD></TR>";
		}               
                               
                oci_free_statement($stid1);
                oci_close($conn);
	}
        public static function mostRented(){
		$conn = DB::getConnection();
		
                $query = "select m.movieid,m.name, avg(r.score) as avgscore ,count(userid) as users  from movie m, rating r where m.movieid = r.movieid ".
                "group by m.movieid,m.name order by count(userid) desc, avgscore desc " ;
                $stid1 = oci_parse($conn, $query);
                oci_execute($stid1);
                echo "<TR class='info'><TH>Name</TH><TH>Score</TH><TH>No. of Users</TH></TR>";
		while($row = oci_fetch_array($stid1)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['NAME'])."</TD><TD>".htmlentities($row['AVGSCORE'])."</TD><TD>".htmlentities($row['USERS'])."</TD></TR>";
		}               
                               
                oci_free_statement($stid1);
                oci_close($conn);
	}
        
        public static function popularLang(){
		$conn = DB::getConnection();
		
                $query = "select language, sum(amountpaid) as revenue from sales s INNER JOIN Movie m ".
                "ON S.movieid = m.movieid group by language order by revenue desc" ;
                $stid1 = oci_parse($conn, $query);
                oci_execute($stid1);
                echo "<TR class='info'><TH>Language</TH><TH>Revenue</TH></TR>";
		while($row = oci_fetch_array($stid1)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['LANGUAGE'])."</TD><TD>".htmlentities($row['REVENUE'])."</TD></TR>";
		}               
                               
                oci_free_statement($stid1);
                oci_close($conn);
	}
        
        public static function popularRegion(){
		$conn = DB::getConnection();
		
                $query = "select m.name, c.country as country , count(s.movieid) as timesrented, ROW_NUMBER() OVER(PARTITION BY c.country ".
                "ORDER BY count(s.movieid) DESC) rn from sales s LEFT JOIN movie m ON m.movieid = s.movieid LEFT JOIN CUSTOMER C ".
                "ON C.USERID = s.userid group by c.country ,m.name order by timesrented desc" ;
                $stid1 = oci_parse($conn, $query);
                oci_execute($stid1);
                echo "<TR class='info'><TH>NAME</TH><TH>REGION</TH><TH>TIMES RENTED<TH></TR>";
		while($row = oci_fetch_array($stid1)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['NAME'])."</TD><TD>".htmlentities($row['COUNTRY'])."</TD><TD>".htmlentities($row['TIMESRENTED'])."</TD></TR>";
		}               
                               
                oci_free_statement($stid1);
                oci_close($conn);
	}
        
        public static function popularMovGenre(){
		$conn = DB::getConnection();
		
                $query = "select trim(G.type) as category, sum(amountpaid) as revenue from sales s INNER JOIN GENRE G ".
                "ON S.movieid = G.movieid group by trim(G.type) order by revenue desc" ;
                $stid1 = oci_parse($conn, $query);
                oci_execute($stid1);
                echo "<TR class='info'><TH>Genre</TH><TH>Revenue</TH></TR>";
		while($row = oci_fetch_array($stid1)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['CATEGORY'])."</TD><TD>".htmlentities($row['REVENUE'])."</TD></TR>";
		}               
                               
                oci_free_statement($stid1);
                oci_close($conn);
	}
        
        
        
        //ByRegion
        public static function searchMoviesByRegion($title) {
				$conn = DB::getConnection();
                echo "<TR class='info'><TH>SEARCHED MOVIES </TH></TR>";
		echo "<TR class='info'><TH>MOVIE </TH><TH>DESCRIPTION </TH></TR>";
		if(!$title){
			return;
		}
                
                $query = "select  * from(select m.name, c.country as country , count(s.movieid) as timesrented,ROW_NUMBER() ".
                        "OVER(PARTITION BY c.country ORDER BY count(s.movieid) DESC) rn from sales s LEFT JOIN movie m ON m.movieid = s.movieid LEFT JOIN CUSTOMER".
                        "C ON C.USERID = s.userid group by c.country ,m.name order by timesrented desc )  WHERE  rn = 1(:id_bv)";
                $stid = oci_parse($conn, $query);
                oci_bind_by_name($stid, ":id_bv", $title);
                oci_execute($stid);
                
		while($row = oci_fetch_array($stid)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['NAME'])."</TD><TD>".htmlentities($row['SYNOPSIS'])."</TD></TR>";
		}
                
                
                oci_free_statement($stid);
                oci_close($conn);
        }
          //ByGenre
        public static function searchMoviesByGenre($title) {
				$conn = DB::getConnection();
                echo "<TR class='info'><TH>SEARCHED MOVIES </TH></TR>";
		echo "<TR class='info'><TH>MOVIE </TH><TH>DESCRIPTION </TH></TR>";
		if(!$title){
			return;
		}
                
                $query = "Select movie.movieid,movie.name from movie inner join genre on movie.movieid=genre.movieid ".
                        "where lower(genre.type) like lower('comedy')(:id_bv)";
                $stid = oci_parse($conn, $query);
                oci_bind_by_name($stid, ":id_bv", $title);
                oci_execute($stid);
                
		while($row = oci_fetch_array($stid)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['NAME'])."</TD><TD>".htmlentities($row['SYNOPSIS'])."</TD></TR>";
		}
                
   
                
                oci_free_statement($stid);
                oci_close($conn);
        }
             //ByDirector
                public static function searchMoviesByDirector($title) {
				$conn = DB::getConnection();
                echo "<TR class='info'><TH>SEARCHED MOVIES </TH></TR>";
		echo "<TR class='info'><TH>MOVIE </TH><TH>DESCRIPTION </TH></TR>";
		if(!$title){
			return;
		}
                
                $query = "Select movieid,name from movie where lower(Director) like lower('MaRtiN%')(:id_bv)";
                $stid = oci_parse($conn, $query);
                oci_bind_by_name($stid, ":id_bv", $title);
                oci_execute($stid);
                
		while($row = oci_fetch_array($stid)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['NAME'])."</TD><TD>".htmlentities($row['SYNOPSIS'])."</TD></TR>";
		}
                
                
                oci_free_statement($stid);
                oci_close($conn);
                }
          
                  //ByActor
                 public static function searchMoviesByActor($title) {
				$conn = DB::getConnection();
                echo "<TR class='info'><TH>SEARCHED MOVIES </TH></TR>";
		echo "<TR class='info'><TH>MOVIE </TH><TH>DESCRIPTION </TH></TR>";
		if(!$title){
			return;
		}
                
                $query = "elect movie.movieid,movie.name,cast.actorname from movie inner join cast on movie.movieid=cast.movieid where lower(actorname) like lower('r%')(:id_bv)";
                $stid = oci_parse($conn, $query);
                oci_bind_by_name($stid, ":id_bv", $title);
                oci_execute($stid);
                
		while($row = oci_fetch_array($stid)){
                    //var_dump($row);die;
                    echo "<TR><TD>".htmlentities($row['NAME'])."</TD><TD>".htmlentities($row['SYNOPSIS'])."</TD></TR>";
		}
                
                
                oci_free_statement($stid);
                oci_close($conn);
                 }
                 
                 
                //ByEra
                
        public static function searchMoviesByEra($title) {
				$conn = DB::getConnection();
                echo "<TR class='info'><TH>SEARCHED MOVIES </TH></TR>";
		echo "<TR class='info'><TH>MOVIE </TH><TH>DESCRIPTION </TH></TR>";
		if(!$title){
			return;
		}
                
                $query = "select movie.movieid,movie.name,releasedate from movie ".
                         "where releasedate >= to_date('01-01-2014','DD-MM-YYYY')".
                        "and releasedate < to_date('01-01-2015','DD-MM-YYYY')".
                        " order by releasedate desc(:id_bv)";
        }

}
?>