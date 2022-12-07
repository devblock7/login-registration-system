<?php
require_once 'connection.php';

// Start a new session
session_start();

// Destroy the session
session_destroy();

// Redirect to index.php
header('location:index.php');

