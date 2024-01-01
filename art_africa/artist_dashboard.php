<?php
session_start();

// Check if the user is logged in and has the artist user type
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Artist') {
    // Redirect to the login page if not logged in or not an artist
    header("Location: login.php");
    exit;
}

// Artist-specific dashboard content goes here

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="artist.css">
    <title>Artist Dashboard</title>
</head>
<body>

    <h2>Welcome, <?php echo $_SESSION['email']; ?> (Artist)</h2>
    <!-- Artist-specific links -->
    <ul>
        <li><a href="update_profile.php">Update Profile</a></li>
        <li><a href="upload_artwork.php">Upload Artwork</a></li>
        <li><a href="create_exhibition.php">Create Exhibition</a></li>
        <li><a href="manage_exhibitions.php">Manage Exhibitions</a></li>
        <li><a href="quick_search.php">Quick Search</a></li>
        <li><a href="transactions.php">Transactions</a></li>
        <li><a href="direct_messages.php">Inbox</a></li>
        <li><a href="allart.php">Artworks</a></li>
    </ul>

    <a href="logout.php">Logout</a>

</body>
</html>
