<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();  // Start the session

$servername = "localhost";
$username = "root";
$password = "";
$database = "art_africa";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $userType = $_POST["userType"];

    // Example: Hashing the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Example: Check if the user already exists in the database using prepared statement
    $checkStmt = $conn->prepare("SELECT UserID FROM user WHERE Username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "Error: User already exists";
        $checkStmt->close();
        $conn->close();
        exit;
    }

    $checkStmt->close();

    // Example: Inserting data into the user table with hashed password using prepared statement
    $insertStmt = $conn->prepare("INSERT INTO user (Username, Email, Password, UserType) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("ssss", $username, $email, $hashedPassword, $userType);

    try {
        $insertStmt->execute();

        // Check if the user was inserted successfully
        if ($insertStmt->affected_rows > 0) {
            // Store user information in the session for future use
            $_SESSION['user_id'] = $insertStmt->insert_id;
            $_SESSION['email'] = $email;
            $_SESSION['user_type'] = $userType;

            // Redirect to the appropriate dashboard
            if ($userType === 'Artist') {
                header("Location: artist_dashboard.php");
            } elseif ($userType === 'Client') {
                header("Location: client_dashboard.php");
            } else {
                // Handle other user types if needed
                header("Location: home.php");
            }
            exit;
        } else {
            echo "Error: User registration failed";
        }
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }

    $insertStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>User Registration</title>
</head>
<body>

    <h2>User Registration</h2>
    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <label for="userType">User Type:</label>
        <select name="userType" required>
            <option value="Artist">Artist</option>
            <option value="Client">Client</option>
        </select><br>

        <input type="submit" value="Register">
    </form>

</body>
</html>
