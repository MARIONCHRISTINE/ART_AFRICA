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

$userID = $_SESSION['user_id'];

// Retrieve transactions for the current user
$transactionsStmt = $conn->prepare("SELECT * FROM transaction WHERE BuyerID = ?");
$transactionsStmt->bind_param("i", $userID);
$transactionsStmt->execute();
$transactionsResult = $transactionsStmt->get_result();

// Handle query error (for debugging purposes)
if (!$transactionsResult) {
    die("Query error: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="transactions.css">
    <title>Transaction History</title>
</head>
<body>

    <h2>Transaction History</h2>

    <table border="1">
        <tr>
            <th>Transaction ID</th>
            <th>Artwork ID</th>
            <th>Transaction Date</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Payment Method</th>
        </tr>
        <?php while ($transaction = $transactionsResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $transaction['TransactionID']; ?></td>
                <td><?php echo $transaction['ArtworkID']; ?></td>
                <td><?php echo $transaction['TransactionDate']; ?></td>
                <td><?php echo $transaction['Amount']; ?></td>
                <td><?php echo $transaction['Status']; ?></td>
                <td><?php echo $transaction['PaymentMethod']; ?></td>
            </tr>
        <?php } ?>
    </table>

    <?php
    // Determine the dashboard link based on user type
    $dashboardLink = ($_SESSION['user_type'] === 'client') ? 'client_dashboard.php' : 'artist_dashboard.php';
    ?>

    <a href="<?php echo $dashboardLink; ?>">Go Back to Dashboard</a>

</body>
</html>
