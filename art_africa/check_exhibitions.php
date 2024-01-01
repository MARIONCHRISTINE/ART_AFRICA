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

// Check if the user is logged in as a client
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Client') {
    // Redirect to the login page if not logged in or not a client
    header("Location: login.php");
    exit;
}

// Example: Retrieve exhibitions from the database with artist information
$exhibitionsStmt = $conn->prepare("SELECT e.ExhibitionID, e.Title, e.Description, e.StartDate, e.EndDate, u.Username AS ArtistUsername
                                    FROM exhibition AS e
                                    JOIN user AS u ON e.ArtistID = u.UserID");
$exhibitionsStmt->execute();
$exhibitionsResult = $exhibitionsStmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="check.css">
    <title>Check Exhibitions</title>
</head>
<body>

    <h2>Check Exhibitions</h2>

    <!-- Display the list of exhibitions -->
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Artist</th>
            <th>Action</th>
        </tr>
        <?php while ($exhibition = $exhibitionsResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $exhibition['Title']; ?></td>
                <td><?php echo $exhibition['Description']; ?></td>
                <td><?php echo $exhibition['StartDate']; ?></td>
                <td><?php echo $exhibition['EndDate']; ?></td>
                <td><?php echo $exhibition['ArtistUsername']; ?></td>
                <td><a href="exhibition.php?exhibitionID=<?php echo $exhibition['ExhibitionID']; ?>">View Details</a></td>
            </tr>
        <?php } ?>
    </table>

    <a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
