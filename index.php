<?php
// Include the connection file
require_once 'connection.php';

// Start a new session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user'])) {
	header("location:welcome.php");
}
// Check if the login_btn request variable is set
if (isset($_REQUEST['login_btn'])) {

	// Retrieve the values of the email and password request variables
	$email = htmlspecialchars(strtolower($_REQUEST['email']));

	$password = strip_tags($_REQUEST['password']);


	// Validate the input fields
	if (empty($email)) {
		$errorMsg[] = 'Please enter email';
	} else if (empty($password)) {

		$errorMsg[] = 'Please enter password';
	} else {
		// If the input is valid, try to log in the user
		try {
			// Check if the email address is registered
			$user = ORM::for_table('users')->where(array(
				'email' => $email,
			))->find_one();

			if (isset($user)) {
				if (password_verify($password, $user->password)) {

					// Generate random CSRF token.
					$csrf_token = bin2hex(random_bytes(32));
					$_SESSION['user']['firstName'] = $user->first_name;
					$_SESSION['user']['lastName'] = $user->last_name;
					$_SESSION['user']['email'] = $user->email;
					$_SESSION['user']['id'] = $user->id;
					$_SESSION['csrf_token'] = $csrf_token;

					header("location: welcome.php");
				} else {
					$errorMsg[] = 'Sorry wrong credentials';
				}
			} else {
				$errorMsg[] = 'Sorry wrong credentials';
			}
		} catch (\PDOException $e) { //Added slash
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
	<title>Login</title>
</head>

<body>
<div class="container container-table" style="margin-top:5%;">

<div class="row justify-content-center">
<div class="col-md-4 col-md-offset-4">
		<form action="index.php" method="post">
			<div class="mb-3">
				<label for="email" class="form-label">Email address</label>
				<input type="email" name="email" class="form-control" placeholder="">

			</div>
			<div class="mb-3">
				<label for="password" class="form-label">Password</label>
				<input type="password" name="password" class="form-control" placeholder="">
				<?php
				if (isset($errorMsg)) {
					foreach ($errorMsg as $loginErrors) {
						echo "<p class='alert alert-danger'>" . $loginErrors . "</p>";
					}
				}
				?>
			</div>
			<button type="submit" name="login_btn" class="btn btn-primary" style="float: right;">Login</button>
			<div class="text-center" style="margin-top:70px;">
		No Account? <a class="register" href="register.php">Register Instead</a></div>
		</form>
		<div>
		</div>
	</div></div></div></div>
</body>

</html>