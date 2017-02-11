 <?php
require_once('user.php');
require_once('router.php');
//require_once('book.php');
//require_once('library.php');
session_start();
$user = unserialize($_SESSION['user']);
//echo "you are here";die;

/* mail("andy.guibert@gmail.com",
	'[Unified Rental Service] Upcoming rental deadline',
	'One of your rentals is due today, make sure you bring that back to us!');
	*/
?>

<html>
<head>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel='stylesheet' type='text/css'>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<title>Movie Rental System</title>

<body>
	<!-- nav bar, goes at the top of every page -->
	<nav class="navbar navbar-inverse" role="navigation">
		<div class="collapse navbar-collapse">
	    <ul class="nav navbar-nav">
	    	<li><h4 class="navbar-text"><b>Movie Rental System</b></h4>
	    	<li class="active"><a href="#">Home</a></li>
			<li><a href="manage.php">Admin</a></li>
			<li>
				<form class="navbar-form navbar-left" role="search">
  				<div class="form-group">
   				 	<input id="searchTxt" type="text" class="form-control" placeholder="Search...">
                                        
                                        <select id= "ctg" name="Categories" class="form-control" aria-label="...">
                                            <option>Title</option>
                                            <option>Genre</option>
                                            <option>Language</option>
                                            <option>Region</option>
                                            <option>Actor</option>
                                            <option>Director</option>
                                            <option>Era</option>
                                        </select>                                        
   				 	<div class="btn-group" role="group" aria-label="...">
					  <button id ="searchTitleBtn" type="button" class="btn btn-default">Search</button>
					 <!-- <button id ="searchGenreBtn" type="button" class="btn btn-default">ByGenre</button> -->
					</div>
 				 </div>
				</form>
			</li>
		</ul>
	    <ul class="nav navbar-nav navbar-right">
			<li><button type="button" class="btn btn-default navbar-btn" onclick="logout()">Logout <?php echo $user->getFirst() ?></button></li>
			<li><a style="padding-right:10px"></a></li>
		</ul>
		</div>
	</nav>
	<div class="col-md-offset-1">
		<div >
			<h1 style="">Movie Rental System</h1>
			<div class="col-md-10 urs-container" style="padding-left:25px">
				<table id="lib" class="table">
				</table>
                            <table id="checkOutTable" class="table">
				</table>
                                
			</div>
		</div>
	</div>
	<br>
	
	
	<!-- Modal for when a table cell is clicked -->
	<div id="mymodal" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <input id="modal-copyid" type="hidden" value="">
	      <div class="modal-body" align="center">
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	        <button id="deleteBookBtn" type="button" class="btn btn-danger teacher" style="display:none" data-dismiss="modal">Delete</button>
	        <button id="checkoutBookBtn" type="button" class="btn btn-primary student" style="display:none" data-dismiss="modal">Checkout</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</body>
<script>
function logout(){
	window.location.href = "index.php";
}

function sendMail(){
	var userEmail = "<?php echo $user->getEmail() ?>";
        	$.get( "router.php", { function:"email", userEmail: userEmail } )
                .done(function( result ) {
                if(result !== "")
		  alert(result);
                            
            });
}
function showModal(title, body, copyID){
    	// $('#mymodal .modal-title').html(title);
    	$('#mymodal .modal-body').html(body);
    	$('#modal-copyid').val(copyID);
    	var checkedOut = $("#checkedOut" + copyID).html();
    	console.log(checkedOut);
    	if(checkedOut != undefined){
    		$("#checkoutBookBtn").hide();
    	}else{
    		$("#checkoutBookBtn").show();
    	}
        $('#mymodal').modal('show');
}
function getBookInfo(copyID){

$.get( "router.php", { function: "getBookInfo", copyID: copyID } )
           .done(function( result ) {
               showModal("Information for Movie " + copyID, result, copyID);
            });

}

function showSearch(searchType) {

	var input = $("#searchTxt").val();
	//input = input.replace(" ","_");
	if(input == ""){
		updateLib()
		return;
	}

        $.get( "router.php", { function: "showSearch", type: searchType, input: input} )
            .done(function( data ) {
                	$("#lib").html(data);
			$('.book').click(function(){
				getBookInfo($(this).find("input").val());
			})
            });
};

function updateLib(){
 
var username = "<?php echo $user->getUsername() ?>";
        $.get( "router.php", { function: "showLib", userID: username} )
            .done(function( data ) {
                			$("#lib").html(data);
			$('.book').click(function(){
				getBookInfo($(this).find("input").val());
			})
            });
 
};


//function removeBook(){
//	var input = $("#modal-copyid").val();
//	$.ajax({
//		type : "GET",
//		url  : "router.php",
//		data : {"function":"removeBook","copyID":input.trim()},
//		success : function(result){
//			updateLib();
//		}
//	});
//}
function checkOutTable(){
	var username = "<?php echo $user->getUsername() ?>";
        $.get( "router.php", { function: "viewCheckOut", userID: username } )
            .done(function( data ) {
//                alert("success");
                $('#checkOutTable').html(data);
              //alert( "Data Loaded: " + data );
            });
}

function requestNotification(bookTitle){
	var username = "<?php echo $user->getUsername() ?>";
	$.ajax({
		type : "GET",
		url  : "../ConnectionModel/router.php",
		data : {"function":"requestNotification","userID":username, "bookTitle": bookTitle},
		success : function(result){
			
		}
	}).done(function(data) {
		if(data === "ERROR") {
			alert("Invalid Movie Title");
		}
		else {
			alert("Request logged");
		}
	});
}
$('#viewLoansBtn').click(function(){
	var input = $('#viewUserHistory').val();
	$.ajax({
		type : "GET",
		url	 : "../ConnectionModel/router.php",
		data : {"function" :"viewLoans", "user"	:input, "exact":"true"},
		success	: function(result){
			$('#historyTable').html(result);
		}
	});
	$('#viewUserHistory').val("");
});
$('#viewUserHistory').keyup(function() {
	var input = $('#viewUserHistory').val();
	$.ajax({
		type : "GET",
		url	 : "../ConnectionModel/router.php",
		data : {"function" :"viewLoans", "user"	:input, "exact":"false"},
		success	: function(result){
			$('#historyTable').html(result);
		}
	});
});
$('#addBookBtn').click(function(){
	var bookName = $("#addBookName").val();
	var author 	 = $("#addAuthor").val();
	var qty      = $("#addQty").val();
	var validated = false;
	$.ajax({
		type : "GET",
		url  : "../ConnectionModel/router.php",
		data : {"function":"validate","bookName":bookName,"author":author,"qty":qty},
		async:   false,
		success : function(result){
			if(result == "PASSED")
				validated = true;
			else
				alert(result);
		}
	})
	if(!validated)
		return;
	$.ajax({
		type : "GET",
		url  : "../ConnectionModel/router.php",
		data : {"function":"addBook","title":bookName,"author":author,"qty":qty},
		success : function(result){
			updateLib();
		}
	});
	$("#addBookName").val("");
	$("#addAuthor").val("");
	$("#addQty").val("");
});
$('#checkoutBookBtn').click(function(){
	var input = $("#modal-copyid").val();
	var username = "<?php echo $user->getUsername() ?>";
	$.ajax({
		type : "GET",
		url  : "../ConnectionModel/router.php",
		data : {"function":"checkoutBook","copyID":input.trim(),"userID":username},
		success : function(result){
			if(result == 'FAILED')
				alert("You have already checked out this movie " + input + " before.");
			updateLib();
			checkOutTable();
		}
	});
});


$('#searchTitleBtn').click(function() {
        var ctg = $("#ctg").val();
	showSearch(ctg);
});

$('#requestButton').click(function() {
	requestNotification($('#requestField').val());
	$('#requestField').val("");
});


$(document).ready(function(){
	updateLib();
        //alert("here");return;
	checkOutTable();
//	if(<?php echo $user->isLib() ?>)
//		$(".teacher").css("display","");
//	else
//		$(".student").css("display","");
	//$('#deleteBookBtn').click(removeBook);
});
</script>
</html>
