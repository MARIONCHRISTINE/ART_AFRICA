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

// Get the user's ID
$userID = $_SESSION['user_id'];

// Handle message composition and submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['receiverID'])) {
        // Compose and send a new message
        $receiverID = $_POST['receiverID'];
        $messageText = isset($_POST['messageText']) ? $_POST['messageText'] : '';

        // Insert the new message into the database
        if (!empty($messageText)) {
            $insertStmt = $conn->prepare("INSERT INTO messages (SenderID, ReceiverID, MessageText, Status) VALUES (?, ?, ?, 'unread')");
            $insertStmt->bind_param("iis", $userID, $receiverID, $messageText);
            $insertStmt->execute();
        }
    } elseif (isset($_POST['replyText']) && isset($_POST['messageID'])) {
        // Reply to an existing message
        $replyText = $_POST['replyText'];
        $messageID = $_POST['messageID'];

        // Insert the reply into the database
        if (!empty($replyText)) {
            $replyStmt = $conn->prepare("INSERT INTO messages (SenderID, ReceiverID, MessageText, Status) VALUES (?, ?, ?, 'unread')");
            $replyStmt->bind_param("iis", $userID, $messageID, $replyText);
            $replyStmt->execute();

            // Update the original message's status to 'read'
            $updateStatusStmt = $conn->prepare("UPDATE messages SET Status = 'read' WHERE MessageID = ?");
            $updateStatusStmt->bind_param("i", $messageID);
            $updateStatusStmt->execute();
        }
    }
}

// Retrieve the user's received messages grouped by sender
$messagesStmt = $conn->prepare("SELECT DISTINCT SenderID FROM messages WHERE ReceiverID = ?");
$messagesStmt->bind_param("i", $userID);
$messagesStmt->execute();
$sendersResult = $messagesStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="messages.css">
    <style>
        small {
            font-size: 0.8em;
        }
    </style>
    <title>Direct Messages</title>
</head>
<body>

    <h2>Messages</h2>

    <!-- Message Composition Form -->
    <form action="direct_messages.php" method="post">
        <label for="receiverID">Recipient ID:</label>
        <input type="text" name="receiverID" required>
        <label for="messageText">Message:</label>
        <textarea name="messageText" required></textarea>
        <input type="submit" value="Send Message">
    </form>

    <h3>Your Conversations</h3>

    <!-- Display Conversations -->
    <ul>
        <?php while ($sender = $sendersResult->fetch_assoc()) {
            $senderID = $sender['SenderID'];
            $conversationStmt = $conn->prepare("SELECT * FROM messages WHERE (SenderID = ? AND ReceiverID = ?) OR (SenderID = ? AND ReceiverID = ?) ORDER BY Timestamp");
            $conversationStmt->bind_param("iiii", $userID, $senderID, $senderID, $userID);
            $conversationStmt->execute();
            $conversationResult = $conversationStmt->get_result();
        ?>
            <li>
                <strong>Conversation with User ID <?php echo $senderID; ?></strong>
                <ul>
                    <?php while ($message = $conversationResult->fetch_assoc()) {
                        // Check if the message text is not empty before displaying
                        if (!empty($message['MessageText'])) { ?>
                            <li>
                                <?php
                                    // Check if the message is from the sender or receiver
                                    $messageFrom = ($message['SenderID'] == $userID) ? 'You' : 'User ID ' . $senderID;
                                    echo $messageFrom . ': ' . $message['MessageText'];
                                ?>
                                <small>Status: <?php echo $message['Status']; ?></small>
                                <small>Timestamp: <?php echo $message['Timestamp']; ?></small>
                            </li>
                        <?php }
                    } ?>
                </ul>

                <!-- Reply Form -->
                <form action="direct_messages.php" method="post">
                    <input type="hidden" name="receiverID" value="<?php echo $senderID; ?>">
                    <label for="replyText">Reply:</label>
                    <textarea name="replyText" required></textarea>
                    <!-- Use the correct messageID for the reply -->
                    <input type="hidden" name="messageID" value="<?php echo $message['MessageID']; ?>">
                    <input type="submit" value="Reply">
                </form>
            </li>
        <?php } ?>
    </ul>

    <!-- Back to Dashboard Link -->
    <?php
    $dashboardLink = ($_SESSION['user_type'] === 'client') ? 'client_dashboard.php' : 'artist_dashboard.php';
    ?>
    <a href="<?php echo $dashboardLink; ?>">Go Back to Dashboard</a>

</body>
</html>
