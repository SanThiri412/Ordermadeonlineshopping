<?php
/**
 * Script to hash all plain text passwords in the member table
 * Run this once to convert all existing plain text passwords to hashed passwords
 */

require_once './helpers/DAO.php';

try {
    // Get database connection
    $dbh = DAO::get_db_connect();
    
    // Get all members with their current passwords
    $sql = "SELECT member_id, email, password FROM member";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Password Hashing Script</h2>";
    echo "<p>Starting to hash passwords...</p>";
    echo "<ul>";
    
    $updated_count = 0;
    
    foreach ($members as $member) {
        $member_id = $member['member_id'];
        $email = $member['email'];
        $plain_password = $member['password'];
        
        // Check if password is already hashed (starts with $2y$ or $2a$)
        if (substr($plain_password, 0, 4) === '$2y$' || substr($plain_password, 0, 4) === '$2a$') {
            echo "<li>✓ {$email} - Already hashed, skipping</li>";
            continue;
        }
        
        // Hash the plain text password
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
        
        // Update the database
        $update_sql = "UPDATE member SET password = :password WHERE member_id = :member_id";
        $update_stmt = $dbh->prepare($update_sql);
        $update_stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
        $update_stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $update_stmt->execute();
        
        echo "<li>✓ {$email} - Password hashed successfully (was: {$plain_password})</li>";
        $updated_count++;
    }
    
    echo "</ul>";
    echo "<p><strong>Done! Updated {$updated_count} password(s).</strong></p>";
    echo "<p>You can now login with your original passwords. They are now stored securely as hashes.</p>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
