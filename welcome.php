<?php
// Include the connection file
require_once 'connection.php';

// Start a new session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
	// If the user is not logged in, redirect to the login page
	header('location:index.php');
} else {
	// If the user is logged in, retrieve their posts from the database
	$posts = ORM::for_table('posts')->where(array(
		'user_id' => $_SESSION['user']['id'],
	))
		->order_by_desc('id')
		->find_many();
}

?>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
	<title>Welcome</title>
</head>

<body>

	<div class="container text-right">
		<div class="row" >
			<div class="col-md-12 ">
				<a class="btn btn-danger" style="float: right;" href="logout.php">Logout </a>
			</div>
		</div>

		<?php
		echo "<h1> Welcome " . $_SESSION['user']['firstName']. ' '. $_SESSION['user']['lastName'] . "</h1>"

		?>



		<div class="container">
			<h1 style="margin-top: 5%; margin-bottom: 5%" class="text-center">
				Your Posts
			</h1>
			<div>
				<div class="row" style="min-height: 70vh">
					<div class="col-xs-6 col-sm-4">
						<form action="form.php" name="Form" method="post" enctype="multipart/form-data">
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
							<Input name="title" class="form-control" placeholder="Title" style="background-color: white; line-height: 28px" v-model="description" required maxlength="50" cols="2" rows="3">
							<textarea name="description" class="form-control" placeholder="Content" style="background-color: white; line-height: 28px" v-model="description" required maxlength="255" cols="2" rows="3"></textarea>
							<input type="file" name="image" id="image" accept="image/jpeg,image/png" required>
							<button class="btn btn-success" type="submit" name="submit" style="width: 100%; margin-top: 20px">
								Add
							</button>
						</form>
					</div>
					<div class="col-xs-6 col-sm-8">
						<div style="border: 1px solid #ddd; border-radius: 5px;padding: 20px; margin-bottom: 150px; background-color: white;">
							<table class="table align-middle text-left table-responsive">
								<thead>
									<tr>
										<th class="col-md">#</th>
										<th class="col-md">Title</th>
										<th class="col-md">Content</th>
										<th class="col-md">Image</th>
									</tr>
								</thead>


								<tbody>
									<?php
									foreach ($posts as $index => $post) {
										echo "<tr>";
										echo "<th class=\"col-md-1\">" . $index + 1 . "</th>";
										echo "<td class=\"col-md-3\"style=\" overflow-wrap: anywhere;\">" . $post->header . "</td>";
										echo "<td class=\"col-md-6\"style=\" overflow-wrap: anywhere;\">" . $post->content . "</td>";
										echo "<td class=\"col-md-1\"style=\" overflow-wrap: anywhere;\">";
										$image_name = $post->image;

										// Construct the path to the image on the server
										$image_path = "images/" . $image_name;
										if (file_exists($image_path)) {
											// Display the image
											echo "<img style=\"width:100px; height:100px; object-fit: cover;\" src='" . $image_path . "'>";
										} else {
											// Display an error message if the image does not exist
											echo "Error: Image not found on server.";
										}
										echo "</td>";
										echo "<tr>";
									}
									?>


								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<footer>
				<div class="col-xl-12">
					<div class="copyright text-center">
						Copyrigh &copy; 2022 All Rights Reserved.
					</div>
				</div>
			</footer>
		</div>



	</div>
</body>

</html>