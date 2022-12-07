<?php

require_once 'connection.php';

session_start();

if ($_POST['csrf_token'] != $_SESSION['csrf_token']) {
	// CSRF attack detected
	exit;
}
//Set variables

$userId = $_SESSION['user']['id'];
$description = htmlspecialchars($_POST['description']);
$title = htmlspecialchars($_POST['title']);
$created = new DateTime();
$created = $created->format('Y-m-d H:i:s');

// Check if the form was submitted
if (isset($_POST['submit'])) {
	// Get the uploaded image
	$image = $_FILES['image']['tmp_name'];

	// Get the image type
	$image_type = exif_imagetype($image);

	// Check if the image type is supported
	if ($image_type === IMAGETYPE_JPEG || $image_type === IMAGETYPE_PNG) {
		// Generate a unique file name
		$file_name = uniqid() . '.' . ($image_type === IMAGETYPE_JPEG ? 'jpg' : 'png');

		// Set the target directory
		$target_dir = __DIR__ . '/images/';

		// Save the image to the target directory
		move_uploaded_file($image, $target_dir . $file_name);

		// Save 
		$post = ORM::for_table('posts')->create();
		$post->user_id = $userId;
		$post->content = $description;
		$post->header = $title;
		$post->image = $file_name;
		$post->created_at = $created;
		$post->save();


		// Redirect back to index
		header('location:index.php');
		exit;
	} else {

		// Display error
		echo "<h1>Sorry, This File Type Is Not Permitted for Security reasons </h1>";
	}
}
