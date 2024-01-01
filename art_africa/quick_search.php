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

// Check if the quick search form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['keyword'])) {
    // Get the search keyword from the form
    $keyword = $_GET['keyword'];

    // Search for exhibitions based on the keyword
    $searchStmt = $conn->prepare("SELECT * FROM exhibition WHERE Title LIKE ? OR Description LIKE ?");
    $searchStmt->bind_param("ss", $searchKeyword, $searchKeyword);
    $searchKeyword = "%$keyword%";
    if ($searchStmt->execute()) {
        $exhibitionResults = $searchStmt->get_result();
    } else {
        die("Query error: " . $searchStmt->error);
    }

    // Add more search queries for other entities if needed

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="search.css">
    <title>Quick Search Results</title>
</head>
<body>

    <h2>Search Results for: <?php echo isset($keyword) ? $keyword : ""; ?></h2>

    <h3>Exhibitions</h3>
    <ul>
        <?php
        if (isset($exhibitionResults) && $exhibitionResults !== null) {
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

    <!-- Add more sections for other entities if needed -->

    <!-- Quick Search Form -->
    <form action="quick_search_results.php" method="get">
        <label for="keyword">Keyword:</label>
        <input type="text" name="keyword" required>
        <input type="submit" value="Search">
    </form>

    <?php
    // Determine the dashboard link based on user type
    $dashboardLink = ($_SESSION['user_type'] === 'client') ? 'client_dashboard.php' : 'artist_dashboard.php';
    ?>

    <a href="<?php echo $dashboardLink; ?>">Go Back to Dashboard</a>

</body>
</html>
