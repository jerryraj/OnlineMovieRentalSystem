<?php
require_once('user.php');
require_once('router.php');

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
                    <li><a href="home.php">Home</a></li>
	    	<li class="active"><a href="#">Admin</a></li>
		<li><h4 class="navbar-text"><b>Admin Analyze Sales</b></h4>	
		</ul>
	    <ul class="nav navbar-nav navbar-right">
                <li><button type="button" class="btn btn-default navbar-btn" onclick="logout()">Logout</button></li>
		<li><button type="button" class="btn btn-default navbar-btn" onclick="rows()">Rows</button></li>
			<li><a style="padding-right:10px"></a></li>
		</ul>
		</div>
	</nav>
	<div class="col-md-offset-1">
		<div >
			<h1 style="">Movie Rental System</h1>
			<div class="col-md-10 urs-container" style="padding-left:25px">
				
                            <table id="SalesSearch" class="table" cellspacing="20px">
                                <TR><TD>Date From<input id="DateFrom" type="text" class="form-control" placeholder="MM-DD-YYYY">                                
                                </TD>
                                <TD>Date To<input id="DateTo" type="text" class="form-control" placeholder="MM-DD-YYYY">
                                </TD>
                                </TR><TR><TD>       
                                        
                                 <button id="btn1" type="button" class="btn btn-success">Count New Users</button>
                                    </TD><TD>
                                 <input id="btn2" type="button" class="btn btn-success" value="Check Revenue">
                                 </TD></TR>
                                <TR class="spacer"><td></td></TR>
                                <TR><TD>Age Group From <input id="AgeGroupFrom" type="text" class="form-control" placeholder="Age From">                                
                                </TD>
                                <TD>Age Group To <input id="AgeGroupTo" type="text" class="form-control" placeholder="Age To">
                                </TD></TR><TR><TD>
                                 <input id="btn3" type="button" class="btn btn-success" value="Popular Genre">
                                    </TD><TD>
                                 <input id="btn4" type="button" class="btn btn-success" value="Check Sales">
                                 </TD></TR>
                                <TR class="spacer"><td></td></TR>
                                <TR><TD>
                                 <input id="btn5" type="button" class="btn btn-success" value="Revenue By Language">
                                    </TD><TD>
                                 <input id="btn6" type="button" class="btn btn-success" value="Popular By Region">
                                 </TD><TD>
                                 <input id="btnx" type="button" class="btn btn-success" value="Revenue By Genre">
                                 </TD></TR>
                                <TR class="spacer"><td></td></TR>
                                 
                                <TR> <TD>
                                 <input id="btn7" type="button" class="btn btn-success" value="Inactive Users">
                                    </TD><TD>
                                 <input id="btn8" type="button" class="btn btn-success" value="Customer Ranking ">
                                 </TD><TD>
                                 <input id="btn9" type="button" class="btn btn-success" value="Most Rated Movies">
                                    </TD></TR>	
                            </table>
                                
			</div>		  
                        
		</div>
	</div>
	<br>
        	<div class="col-md-offset-1">
		<div >
			
			<div class="col-md-10 urs-container" style="padding-left:25px">
                            <table id="checkOutTable" class="table">
				</table>
                                
			</div>
		</div>
	</div>
        <br>
	
	
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

function rows() {

        $.get( "router.php", { function: "rows" })
            .done(function( data ) {
                	$("#checkOutTable").html(data);
			
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
                $('#checkOutTable').html(data);
              //alert( "Data Loaded: " + data );
            });
}

//function requestNotification(bookTitle){
//	var username = "<?php echo $user->getUsername() ?>";
//	$.ajax({
//		type : "GET",
//		url  : "../ConnectionModel/router.php",
//		data : {"function":"requestNotification","userID":username, "bookTitle": bookTitle},
//		success : function(data){
//			
//		}
//	}).done(function(data) {
//		if(data === "ERROR") {
//			alert("Invalid Movie Title");
//		}
//		else {
//			alert("Request logged");
//		}
//	});
//}

$('#btn1').click(function() {
    //alert( "Success here" );
	var input1 = $('#DateFrom').val();
        var input2 = $('#DateTo').val();
        if(input1 === "" || input2 === ""){
		checkOutTable();
		return;
	}
	$.get( "router.php", { function: "countNewUsers", input1: input1, input2: input2 } )
            .done(function( data ) {
                $('#checkOutTable').html(data);
              //alert( "Data Loaded: " + data );
            });
	$('#DateFrom').val("");
        $('#DateTo').val("");
});

$('#btn2').click(function() {
	var input1 = $('#DateFrom').val();
        var input2 = $('#DateTo').val();
        if(input1 === "" || input2 === ""){
		checkOutTable();
		return;
	}
	$.get( "router.php", { function: "checkRevenueDT", input1: input1, input2: input2 } )
            .done(function( data ) {
                $('#checkOutTable').html(data);
              //alert( "Data Loaded: " + data );
            });
	$('#DateFrom').val("");
        $('#DateTo').val("");
});

$('#btn3').click(function() {
	var input1 = $('#AgeGroupFrom').val();
        var input2 = $('#AgeGroupTo').val();
        if(input1 === "" || input2 === ""){
		checkOutTable();
		return;
	}
	$.get( "router.php", { function: "popularGenre", input1: input1, input2: input2 } )
            .done(function( data ) {
                $('#checkOutTable').html(data);
              //alert( "Data Loaded: " + data );
            });
	$('#AgeGroupFrom').val("");
        $('#AgeGroupTo').val("");
});

$('#btn4').click(function() {
	var input1 = $('#AgeGroupFrom').val();
        var input2 = $('#AgeGroupTo').val();
        if(input1 === "" || input2 === ""){            
		checkOutTable();
		return;
	}
	$.get( "router.php", { function: "revenueAge", input1: input1, input2: input2 } )
            .done(function( data ) {
                $('#checkOutTable').html(data);
              //alert( "Data Loaded: " + data );
            });
	$('#AgeGroupFrom').val("");
        $('#AgeGroupTo').val("");
});

$('#btn5').click(function() {
	$.get( "router.php", { function: "popularLang" } )
            .done(function( data ) {
                $('#checkOutTable').html(data);
            });
});
$('#btn6').click(function() {
	$.get( "router.php", { function: "popularRegion" } )
            .done(function( data ) {
                $('#checkOutTable').html(data);
            });
});
$('#btnx').click(function() {
	$.get( "router.php", { function: "popularMovGenre" } )
            .done(function( data ) {
                $('#checkOutTable').html(data);
            });
});
$('#btn7').click(function() {
	$.get( "router.php", { function: "inactiveUsers" } )
            .done(function( data ) {
                $('#checkOutTable').html(data);
            });
});
$('#btn8').click(function() {
	$.get( "router.php", { function: "ranking" } )
            .done(function( data ) {
                $('#checkOutTable').html(data);
            });
});
$('#btn9').click(function() {
	$.get( "router.php", { function: "mostRented" } )
            .done(function( data ) {
                $('#checkOutTable').html(data);
            });
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
        //
});
</script>
</html>