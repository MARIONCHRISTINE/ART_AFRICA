<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "art_africa";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming you have the ExhibitionID and Artist's UserID available
$exhibitionID = 123; // Replace with the actual ExhibitionID
$artistUserID = 456; // Replace with the actual Artist's UserID

// Fetch exhibition details from the database
$exhibitionStmt = $conn->prepare("SELECT * FROM exhibition WHERE ExhibitionID = ?");
$exhibitionStmt->bind_param("i", $exhibitionID);
$exhibitionStmt->execute();
$exhibitionResult = $exhibitionStmt->get_result();

if ($exhibitionResult->num_rows > 0) {
    $exhibition = $exhibitionResult->fetch_assoc();

    // Display exhibition details
    echo "<h2>Exhibition Details</h2>";
    echo "<p>Title: " . $exhibition['Title'] . "</p>";
    echo "<p>Description: " . $exhibition['Description'] . "</p>";
    echo "<p>Start Date: " . $exhibition['StartDate'] . "</p>";
    echo "<p>End Date: " . $exhibition['EndDate'] . "</p>";

    // Add a link to give feedback
    echo '<a href="give_feedback.php?exhibitionID=' . $exhibitionID . '&artistUserID=' . $artistUserID . '">Give Feedback</a>';
} else {
    echo "Exhibition not found.";
}

$exhibitionStmt->close();
$conn->close();
?>
