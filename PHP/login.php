<?php
/* login.php
 * Login form for QuizMatch
 * Users should be able to use either their email or username to sign in.
 * Creates a new session and redirects user to profile welcome page (user_welcome.php/html)
 *
 * Redirects to User_Welcome.html/php upon completion
 *
 * References to Database have been commented out, and replaced with searching a testing .txt file
 * as a temporary Database
 */
 
 // Checks to see if USER is already signed into an account
session_start();
 
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
	// Forces USER back into user welcome page if they are already signed into an account
	header("location: userprofile.php");
	exit;
}
 
// Beginning of True Login process
require_once "config.php";
 
$profile_err = $password_err = "";
$profile = $password = "";
if(isset($_COOKIE["remember_me"])) $profile = $_COOKIE["remember_me"];
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	// Verifies both fields are filled. Will output an error to USER if either are empty
	if(empty(trim($_POST["profile"])))
	{
		$profile_err = "Please enter your username or email.";
	}else{
		$profile = htmlspecialchars(trim($_POST["profile"]));
	}
	if(empty(trim($_POST["password"])))
	{
		$password_err = "Please enter your password.";
	}else{
		$password = htmlspecialchars(trim($_POST["password"]));
	}
	
	// Begins core login if and only if both fields have been filled out.
	if(empty($profile_err) && empty($password_err))
	{
		// Check to see if the profile field is an EMAIL. Assumes string is a USERNAME if not an EMAIL. Begins preparation to access Database
		if(filter_var($profile, FILTER_VALIDATE_EMAIL))
		{
			// Is an EMAIL
			$sql = "SELECT id, username, password FROM users WHERE email = ?";
		}else{
			// Is a USERNAME
			$sql = "SELECT id, username, password FROM users WHERE username = ?";
		}
		if($stmt = mysqli_prepare($link, $sql))
		{
			mysqli_stmt_bind_param($stmt, "s", $param_profile);
			$param_profile = htmlspecialchars($profile);
			// Access Database
			if(mysqli_stmt_execute($stmt))
			{
				// Access granted; execution successful
				mysqli_stmt_store_result($stmt);
				if(mysqli_stmt_num_rows($stmt) == 1)
				{
					// Profile Found
					mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
					// Beginning of Password Verification
					if(mysqli_stmt_fetch($stmt))
					{
						if(password_verify($password, $hashed_password))
						{
							// Password matches recorded
							session_start();
							$_SESSION["loggedin"] = true;
							$_SESSION["id"] = $id;
							$_SESSION["username"] = $username;
							if($_POST["remember_me"]){
								$year = time() + 31536000;
								setcookie("remember_me", $profile, $year);
							}else if(!$_POST["remember_me"]){
								if(isset($_COOKIE['remember_me'])) {
									$past = time() - 100;
									setcookie("remember_me", "", $past);
								}
							}
							header("location: userprofile.php");
						}else{
							// Password does not match recorded
							$password_err = "The password you entered was not valid.";
						}
					}
				}else{
					// Profile not found
					$profile_err = "No account was found with this username/email.";
				}
			}else{
				// Connection error
				echo "An Error has occured. Please try again later.";
			}
		}
		mysqli_stmt_close($stmt);
	}
	mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang = "en">
	<head>
		<meta charset="UTF-8"/>
		<title>QuizMatch: Login</title>
		<!--<link rel="stylesheet" href="stupid.css">
		<style type="text/css">
			body{ font: 14px sans-serif; }
			.wrapper{ width: 350px; padding: 20px; }
		</style>-->
		<link href= "stupid.css" type = "text/css" rel = "stylesheet"/>
		<style>
		body
		{
			font: 14px sans-serif;
		}
		div.inputBar
		{
			margin-top:5%;
			width: 350px;
			padding: 20px; 
		}
		</style>
	</head>
	<body>
		<center>
			<div class="inputBar">
				<h2>Login</h2>
				<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
					<div class="form-group <?php echo (!empty($profile_err)) ? 'has-error' : ''; ?>">
					<br>
						<label>Username or Email</label>
						<br><span class="help-block"><font color="red"><?php echo $profile_err;?></font></span>
						<input type="text" style = "font-family:Helvetica" name="profile" class="form-control" value="<?php echo $profile; ?>">
					</div>
					<div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
					<br>
						<label>Password</label>
						<br><span class="help-block"><font color="red"><?php echo $password_err;?></font></span>
						<input type="password" name="password" class="form-control" value="<?php echo $password; ?>"><br><br>
					</div>
					<div class="form-group">
						<input type="submit" class="btn pink rounded" value="Submit" style = "font-family: Helvetica";>
						<input type="checkbox" name="remember_me" value="1" <?php if(isset($_COOKIE['remember_me'])){echo 'checked="checked"';}else {echo '';}?>> Remember me.<br>
					</div>
					<br>
					Need an account? <a href="signup.php"><b>Sign Up</b></a><br>
					Forgot password? <a href="forgot.html"><b>Reset Password</b></a>
				</form>
			</div>
		</center>
	</body>
</html>