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

// Initialize the keyword variable
$keyword = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['keyword'])) {
    // Get the search keyword from the form
    $keyword = $_GET['keyword'];

    // Search for exhibitions based on the keyword
    $searchStmt = $conn->prepare("SELECT * FROM exhibition WHERE Title LIKE ? OR Description LIKE ?");
    $searchStmt->bind_param("ss", $searchKeyword, $searchKeyword);
    $searchKeyword = "%$keyword%";
    $searchStmt->execute();
    $exhibitionResults = $searchStmt->get_result();

    // Search for artworks based on the keyword
    $searchStmt = $conn->prepare("SELECT * FROM artwork WHERE Title LIKE ? OR Description LIKE ?");
    $searchStmt->bind_param("ss", $searchKeyword, $searchKeyword);
    $searchStmt->execute();
    $artworkResults = $searchStmt->get_result();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="search2.css">
    <title>Quick Search Results</title>
</head>
<body>

    <h2>Search Results for: <?php echo htmlspecialchars($keyword); ?></h2>

    <!-- Quick Search Form -->
    <form action="quick_search_results.php" method="get">
        <label for="keyword">Keyword:</label>
        <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" required>
        <input type="submit" value="Search">
    </form>

    <h3>Exhibitions</h3>
    <ul>
        <?php 
        if (isset($exhibitionResults)) {
            while ($exhibition = $exhibitionResults->fetch_assoc()) {
                echo "<li>";
                echo "<strong>" . $exhibition['Title'] . "</strong><br>";
                echo $exhibition['Description'] . "<br>";
                // Add more details as needed
                echo "</li>";
            }
        } else {
            echo "<p>No exhibitions found.</p>";
        }
        ?>
    </ul>

    <h3>Artworks</h3>
    <ul>
        <?php 
        if (isset($artworkResults)) {
            while ($artwork = $artworkResults->fetch_assoc()) {
                echo "<li>";
                echo "<strong>" . $artwork['Title'] . "</strong><br>";
                echo $artwork['Description'] . "<br>";
                // Display the artwork image if available
                if (file_exists($artwork['FilePath'])) {
                    echo "<img src='" . $artwork['FilePath'] . "' alt='Artwork Image' style='max-width: 300px;'>";
                }
                echo "</li>";
            }
        } else {
            echo "<p>No artworks found.</p>";
        }
        ?>
    </ul>

    <?php
    // Determine the dashboard link based on user type
    $dashboardLink = ($_SESSION['user_type'] === 'client') ? 'client_dashboard.php' : 'artist_dashboard.php';
    ?>

    <a href="<?php echo $dashboardLink; ?>">Go Back to Dashboard</a>

</body>
</html>
