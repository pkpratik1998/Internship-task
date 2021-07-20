<?php

//index.php

//error_reporting(E_ALL);

session_start();

if(isset($_SESSION["user_id"]))
{
	header("location:home.php");
}

include('function.php');

$connect = new PDO("mysql:host=localhost; dbname=testing", "root", "");

$message = '';
$error_user_type = '';
$error_user_name = '';
$error_tech_name = '';
$error_user_country = '';
$error_user_reg_date = '';
$error_user_email = '';
$error_user_password = '';
$user_type = '';
$user_name = '';
$tech_name = '';
$user_country = '';
$user_reg_date = '';
$user_email = '';
$user_password = '';

if(isset($_POST["register"]))
{
	if(empty($_POST["user_type"]))
	{
		$error_user_type = "<label class='text-danger'>Select User Type</label>";
	}
	else
	{
		$user_type = trim($_POST["user_type"]);
		$user_type = htmlentities($user_type);
	}
	if(empty($_POST["user_name"]))
	{
		$error_user_name = "<label class='text-danger'>Enter Name</label>";
	}
	else
	{
		$user_name = trim($_POST["user_name"]);
		$user_name = htmlentities($user_name);
	}
	if(empty($_POST["tech_name"]))
	{
		$error_tech_name = "<label class='text-danger'>Enter Technology Name</label>";
	}
	else
	{
		$tech_name = trim($_POST["tech_name"]);
		$tech_name = htmlentities($tech_name);
	}
	if(empty($_POST["user_country"]))
	{
		$error_user_country = "<label class='text-danger'>Enter Country of Origin</label>";
	}
	else
	{
		$user_country = trim($_POST["user_country"]);
		$user_country = htmlentities($user_country);
	}
	if(empty($_POST["user_reg_date"]))
	{
		$error_user_reg_date = "<label class='text-danger'>Select Date of Registration</label>";
	}
	else
	{
		$user_reg_date = trim($_POST["user_reg_date"]);
		$user_reg_date = htmlentities($user_reg_date);
	}
	if(empty($_POST["user_email"]))
	{
		$error_user_email = '<label class="text-danger">Enter Email Address</label>';
	}
	else
	{
		$user_email = trim($_POST["user_email"]);
		if(!filter_var($user_email, FILTER_VALIDATE_EMAIL))
		{
			$error_user_email = '<label class="text-danger">Enter Valid Email Address</label>';
		}
	}

	if(empty($_POST["user_password"]))
	{
		$error_user_password = '<label class="text-danger">Enter Password</label>';
	}
	else
	{
		$user_password = trim($_POST["user_password"]);
		$user_password = password_hash($user_password, PASSWORD_DEFAULT);
	}

	if($error_user_name == '' && $error_user_type == '' && $error_user_country == '' && $error_user_country == '' && $error_user_email == '' && $error_user_password == '')
	{
		$user_activation_code = md5(rand());

		$user_otp = rand(100000, 999999);

		$data = array(
			':user_type'		=>	$user_type,
			':user_name'		=>	$user_name,
			':tech_name'		=>	$tech_name,
			':user_country'		=>	$user_country,
			':user_reg_date'	=>	$user_reg_date,
			':user_email'		=>	$user_email,
			':user_password'	=>	$user_password,
			':user_activation_code' => $user_activation_code,
			':user_email_status'=>	'not verified',
			':user_otp'			=>	$user_otp
		);

		$query = "
		INSERT INTO register_user 
		(user_name, user_email, user_password, user_activation_code, user_email_status, user_otp, tech_name, user_type, user_country, user_reg_date)
		SELECT * FROM (SELECT :user_name, :user_email, :user_password, :user_activation_code, :user_email_status, :user_otp, :tech_name, :user_type, :user_country, :user_reg_date) AS tmp
		WHERE NOT EXISTS (
		    SELECT user_email FROM register_user WHERE user_email = :user_email
		) LIMIT 1
		";

		$statement = $connect->prepare($query);

		$statement->execute($data);

		if($connect->lastInsertId() == 0)
		{
			$message = '<label class="text-danger">Email Already Register</label>';
		}	
		else
		{
			$user_avatar = make_avatar(strtoupper($user_name[0]));

			$query = "
			UPDATE register_user 
			SET user_avatar = '".$user_avatar."' 
			WHERE register_user_id = '".$connect->lastInsertId()."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();


			require 'class/class.phpmailer.php';
			// require 'class/class.smtp.php';
			$mail = new PHPMailer();
			$mail->SMTPDebug = 1;                                 // Enable verbose debug output
			$mail->IsSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = 'aparichit0202@gmail.com';                 // SMTP username
			$mail->Password = 'Lucifer@4748';                           // SMTP password
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, 'ssl' also accepted
			$mail->Port = 587;
			$mail->From = 'pkpratik1998@gmail.com';
			$mail->FromName = 'Pratik';
			$mail->AddAddress($user_email);
			$mail->WordWrap = 50;
			$mail->IsHTML(true);
			$mail->Subject = 'Verification code for Verify Your Email Address';

			$message_body = '
			<p>For verify your email address, enter this verification code when prompted: <b>'.$user_otp.'</b>.</p>
			';
			$mail->Body = $message_body;

			if($mail->Send())
			{
				echo '<script>alert("Please Check Your Email for Verification Code")</script>';

				header('location:email_verify.php?code='.$user_activation_code);
			}
			else
			{
				$message = $mail->ErrorInfo;
			}
		}

	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Registration</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="http://code.jquery.com/jquery.js"></script>
    	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	</head>
	<body>
		<br />
		<div class="container">
			<h3 align="center">User Registration</h3>
			<br />
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Registration</h3>
				</div>
				<div class="panel-body">
					<?php echo $message; ?>
					<form method="post">
						<div class="form-group">
							<label>User type</label>
							<select name="user_type" id="user_type" class="form-control" >
								<option value=""></option>
								<option value="displayer">Displayer</option>
								<option value="scouter">Scouter</option>
							</select>
						</div>
						<div class="form-group">
							<label>Company Name</label>
							<input type="text" name="user_name" class="form-control" />
							<?php echo $error_user_name; ?>
						</div>
						<div class="form-group" id="tech">
							<label>Technology Name</label>
							<input type="text" name="tech_name" class="form-control" />
							<?php echo $error_user_name; ?>
						</div>
						<div class="form-group">
							<label>Country of Origin</label>
							<input type="text" name="user_country" class="form-control" />
							<?php echo $error_user_country; ?>
						</div>
						<div class="form-group">
							<label>Date of Registration</label>
							<input type="date" name="user_reg_date" class="form-control" />
							<?php echo $error_user_reg_date; ?>
						</div>
						<div class="form-group">
							<label>Enter Email</label>
							<input type="text" name="user_email" class="form-control" />
							<?php echo $error_user_email; ?>
						</div>
						<div class="form-group">
							<label>Enter Your Password</label>
							<input type="password" name="user_password" class="form-control" />
							<?php echo $error_user_password; ?>
						</div>
						<div class="form-group">
							<input type="submit" name="register" class="btn btn-success" value="Click to Register" />&nbsp;&nbsp;&nbsp;
							
						</div>
					</form>
				</div>
			</div>
			<br /><br />
			<div align="center">
				<b><a href="login.php">Already User?, Login</a></b>
			</div>
			
		</div>
		<br />
		<br />

		<script>
			 $("#user_type").change(function () {
            var type = $( "#user_type option:selected" ).val();
            if(type=="displayer")
            {
               $("#tech").show();
            }
			else
			{
				$("#tech").hide();
			}
        });
		</script>
	</body>
</html>