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

// Retrieve all artworks from the database
$artworksStmt = $conn->prepare("SELECT * FROM artwork");
$artworksStmt->execute();
$artworksResult = $artworksStmt->get_result();
$artworksStmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="allart.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Artworks</title>
</head>
<body>

    <h2>All Artworks</h2>

    <!-- Quick Search Form -->
    <form action="search_results.php" method="GET">
        <label for="search">Quick Search:</label>
        <input type="text" name="search" placeholder="Enter keyword">
        <button type="submit">Search</button>
    </form>

    <?php
    // Display all artworks
    while ($artwork = $artworksResult->fetch_assoc()) {
        echo "<div>";
        echo "<h3>" . $artwork['Title'] . "</h3>";
        echo "<p>Description: " . $artwork['Description'] . "</p>";
        echo "<p>Artist: " . $artwork['ArtistID'] . "</p>";
        // Add more details as needed

        // Example: Display the artwork image
        echo "<img src='" . $artwork['FilePath'] . "' alt='Artwork Image' style='max-width: 300px;'>";

        echo "</div>";
    }
    ?>

    <?php
    // Determine the dashboard link based on user type
    $dashboardLink = ($_SESSION['user_type'] === 'client') ? 'client_dashboard.php' : 'artist_dashboard.php';
    ?>

    <a href="<?php echo $dashboardLink; ?>">Go Back to Dashboard</a>

</body>
</html>
