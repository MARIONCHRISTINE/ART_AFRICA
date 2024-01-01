<?php
session_start();

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

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email']) || !isset($_SESSION['user_type'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit;
}

// Handle profile update logic here

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve updated profile information
    $newUsername = $_POST["newUsername"];
    $newEmail = $_POST["newEmail"];

    // Update the user's profile in the database
    $updateStmt = $conn->prepare("UPDATE user SET Username = ?, Email = ? WHERE UserID = ?");
    $updateStmt->bind_param("ssi", $newUsername, $newEmail, $_SESSION['user_id']);

    if ($updateStmt->execute()) {
        // Update session variables with new information
        $_SESSION['email'] = $newEmail;

        // Determine the dashboard to redirect based on user type
        $dashboardPage = ($_SESSION['user_type'] === 'Client') ? 'client_dashboard.php' : 'artist_dashboard.php';

        // Redirect back to the specific dashboard
        header("Location: $dashboardPage");
        exit;
    } else {
        echo "Error updating profile: " . $updateStmt->error;
    }

    $updateStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="update_profile.css">
    <title>Update Profile</title>
</head>
<body>

    <h2>Update Profile</h2>

    <form action="" method="POST">
        <label for="newUsername">New Username:</label>
        <input type="text" name="newUsername" required><br>

        <label for="newEmail">New Email:</label>
        <input type="email" name="newEmail" required><br>

        <input type="submit" value="Update Profile">
    </form>

    <a href="<?php echo $dashboardPage; ?>">Back to Dashboard</a>

</body>
</html>
