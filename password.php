<?php
require_once './helpers/DAO.php';

// Get the password from POST
$pass = $_POST['password'];

// Hash the password using password_hash
$hash = password_hash($pass, PASSWORD_DEFAULT);

// Get database connection
$pdo = DAO::get_db_connect();

// Prepare UPDATE statement (if updating existing user)
$stmt = $pdo->prepare("UPDATE member SET password = :password WHERE email = :email");
$stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
$stmt->bindParam(':password', $hash, PDO::PARAM_STR);
$stmt->execute();

echo "Password updated successfully!";