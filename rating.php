<?php 

/**
* 
*/
class rating
{

	function __construct()
	{
		
	}

//	public function getRatings($bookID){
//		if(isset($this->data[$bookID])){
//			return $this->data[$bookID];
//		}else{
//			$data['bookID'] = $bookID;
//			$data['numRatings'] = 0;
//			$data['points'] = 0;
//			$data['avg'] = 0;
//			return $data;
//		}
//	}
        
        public function getRatings($bookID){
            $conn = DB::getConnection();
		$query = "Select avg(score) as data from rating where movieid = :id_bv";
                $stid = oci_parse($conn, $query);
                oci_bind_by_name($stid, ":id_bv", $bookID);
                oci_execute($stid);
                
		if($row = oci_fetch_array($stid)){
                    //var_dump($row);die;
                    //return new Book($row['MOVIE'], $row['NAME'], $row['POSTER']);
                    $data = $row['DATA'];
                    return $data;
		}else{
                return null;}
                
                
                oci_free_statement($stid);
                oci_close($conn);
        }

	public function updateRating($bookID, $score){
		if($this->data[$bookID]){
			$this->data[$bookID]['numRatings'] += 1;
			$this->data[$bookID]['points'] += $score;
		} else {
			$this->data[$bookID]['numRatings'] = 1;
			$this->data[$bookID]['points'] = $score;
		}
		$this->data[$bookID]['avg'] = round( $this->data[$bookID]['points'] / $this->data[$bookID]['numRatings']);
		file_put_contents($this->file, serialize($this->data));
		return $this->getRatings($bookID);
	}
}

?>