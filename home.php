<?php

//home.php

session_start();

if(!isset($_SESSION["user_id"]))
{
	header("location:login.php");
}

include('database_connection.php');
$connect = new PDO("mysql:host=localhost;dbname=testing", "root", "");

include('function.php');

$user_name = '';
$user_id = '';
$user_type = '';
$tech_name = '';
$user_email = '';

if(isset($_SESSION["user_name"], $_SESSION["user_id"]))
{
	$user_name = $_SESSION["user_name"];
	$user_id = $_SESSION["user_id"];
	$data = array(
		':user_id'	=>	$_SESSION["user_id"]
	);
	$query = "
			SELECT * FROM register_user 
			WHERE register_user_id = $user_id
			";
		$statement = $connect->prepare($query);

		$statement->execute($data);

		$result = $statement->fetchAll();

		foreach($result as $row)
		{

			$user_type = $row["user_type"];

			$user_email = $row["user_email"];

			$tech_name = $row["tech_name"];
			$user_country = $row["user_country"];
			$user_reg_date = $row["user_reg_date"];
		}
	
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Profile</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="http://code.jquery.com/jquery.js"></script>
    	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	</head>
	<body>
		<br />
		<div class="container">
			<h1 align="center">Welcome <?php echo $user_type; ?> </h1>
			<br />
			<br />
			<div class="row">
				<div class="col-md-9">

				<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-9">
						<h3 class="panel-title">Profile Details</h3>
					</div>
					
				</div>
			</div>
			<div class="panel-body">
				
    <div class="table-responsive">
        <table class="table">
    
            <tbody><tr>
                <td colspan="2" align="center" style="padding:16px 0">
							<?php
								Get_user_avatar($user_id, $connect);
								echo '<br /><br />';
								
							?>
                </td>
            </tr>
            
        <tr>
            <th>Company Name</th>
            <td><?php echo $user_name; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $user_email; ?></td>
        </tr>
		<?php
			if($user_type == 'displayer')
			echo '<tr><th>Technology</th><td>'.$tech_name . '</td></tr>';
		?>
        <tr>
            <th>Country</th>
            <td><?php echo $user_country; ?></td>
        </tr>
		<tr>
            <th>Registration Date</th>
            <td><?php echo $user_reg_date; ?></td>
        </tr>
        
        </tbody></table>
    </div>
    			</div>
		</div>
					<!-- <div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">User Timeline</h3>
						</div>
						<div class="panel-body">
							<h1 align="center">Welcome </h1>
						</div>
					</div> -->
				</div>
				<div class="col-md-3">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">User</h3>
						</div>
						<div class="panel-body">
							<div align="center">
								<?php
								Get_user_avatar($user_id, $connect);
								echo '<br /><br />';
								echo $user_name;
								?>
								<br />
								<br />
								<a href="logout.php" class="btn btn-default">Logout</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br />
		<br />
	</body>
</html>