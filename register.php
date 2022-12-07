<?php
// Include the connection file
require_once 'connection.php';

// Start a new session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user'])) {
	header("location: welcome.php");
}

// Check if the register_btn request variable is set
if (isset($_REQUEST['register_btn'])) {

	// Retrieve the values of the firstname, lastname, email, and password request variables
	$firstName = htmlspecialchars($_REQUEST['firstname']);

	$lastName = htmlspecialchars($_REQUEST['lastname']);

	$email = htmlspecialchars(strtolower($_REQUEST['email']));

	$password = strip_tags($_REQUEST['password']);

	$confirmPassword = strip_tags($_REQUEST['confirm_password']);



	// Validate the input fields
	if (empty($firstName)) {
		$errorMsg[0][] = "First name required";
	}
	if (empty($lastName)) {
		$errorMsg[1][] = "Last name required";
	}
	if (empty($email)) {
		$errorMsg[2][] = "Email required";
	}
	if (empty($password)) {
		$errorMsg[3][] = "Password required";
	}
	if (strlen($password) < 9) {
		$errorMsg[3][] = "Password must be at least 9 characters";
	}
	if ($password != $confirmPassword) {

		$errorMsg[4][] = "Passwords do not match";
	}
	// Check if there are any error messages
	if (empty($errorMsg)) {
		try {
			// Check if the email address is already registered
			$person = ORM::for_table('users')->where(array(
				'email' => $email
			))->find_one();
			// If the email address is already registered, add an error message
			if (isset($person->email) == $email) {
				$errorMsg[2][] = "Email address already exists, please choose anather or login instead";
			} else {
				// If the email address is not registered, create a new user record
				// Hash the password
				$hashed_password = password_hash($password, PASSWORD_DEFAULT);
				$created = new DateTime();
				$created = $created->format('Y-m-d H:i:s');
				$usersTable = ORM::for_table('users')->create();

				// Create a new user record
				if (isset($usersTable)) {
					$usersTable->email = $email;
					$usersTable->first_name = $firstName;
					$usersTable->last_name = $lastName;
					$usersTable->password = $hashed_password;
					$usersTable->created_at = $created;
					$usersTable->save();
					
					$user = ORM::for_table('users')->where(array(
						'email' => $email,
					))->find_one();
					$csrf_token = bin2hex(random_bytes(32));
					$_SESSION['user']['firstName'] = $user->first_name;
					$_SESSION['user']['lastName'] = $user->last_name;
					$_SESSION['user']['email'] = $user->email;
					$_SESSION['user']['id'] = $user->id;
					$_SESSION['csrf_token'] = $csrf_token;
					
					// Redirect the user to the index page
					header("location: welcome.php");
				}
			}
			// In case of any errors, print the error message to the screen
		} catch (\PDOException $e) {
			echo $e->getMessage();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}
?>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
	<title>Register</title>
</head>

<body>
<div class="container container-table" style="margin-top:5%;">

<div class="row justify-content-center">
<div class="col-md-4 col-md-offset-4">
		<form action="register.php" class="align-middle" method="post" >
			<div class="mb-3">
				<label for="firstname" class="form-label">First Name</label>
				<input type="text" name="firstname" class="form-control" placeholder="">
				<?php
				if (isset($errorMsg[0])) {
					foreach ($errorMsg[0] as $nameErrors) {
						echo "<p class='small text-danger'>" . $nameErrors . "</p>";
					}
				}
				?>
			</div>
			<div class="mb-3">
				<label for="lastname" class="form-label">Last Name</label>
				<input type="text" name="lastname" class="form-control" placeholder="">
				<?php
				if (isset($errorMsg[1])) {
					foreach ($errorMsg[1] as $nameErrors) {
						echo "<p class='small text-danger'>" . $nameErrors . "</p>";
					}
				}
				?>
			</div>
			<div class="mb-3">
				<label for="email" class="form-label">Email address</label>
				<input type="email" name="email" class="form-control" placeholder="">
				<?php
				if (isset($errorMsg[2])) {
					foreach ($errorMsg[2] as $emailErrors) {
						echo "<p class='small text-danger'>" . $emailErrors . "</p>";
					}
				}
				?>
			</div>
			<div class="mb-3">
				<label for="password" class="form-label">Password</label>
				<input type="password" name="password" class="form-control" placeholder="">
				<?php
				if (isset($errorMsg[3])) {
					foreach ($errorMsg[3] as $passwordErrors) {
						echo "<p class='small text-danger'>" . $passwordErrors . "</p>";
					}
				}
				?>
			</div>
			<div class="mb-3">
				<label for="confirm_password">Confirm Password</label>
				<input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
				<?php
				if (isset($errorMsg[4])) {
					foreach ($errorMsg[4] as $passwordErrors) {
						echo "<p class='small text-danger'>" . $passwordErrors . "</p>";
					}
				}
				?>
			</div>
			<button type="submit" name="register_btn" class="btn btn-primary" style="float: right;">Register Account</button>
			<div class="text-center" style="margin-top:70px;">
			Already Have an Account? <a class="register" href="index.php">Login Instead</a></div>
		</form>
		</div></div></div>
</body>

</html>