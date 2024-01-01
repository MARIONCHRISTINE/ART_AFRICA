<?php
// Ensure that the artwork_uploads directory exists
$uploadsDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'artwork_uploads';

if (!file_exists($uploadsDirectory) && !mkdir($uploadsDirectory, 0777, true)) {
    die("Failed to create artwork_uploads directory");
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $artistID = $_SESSION['user_id'];

    // File upload handling
    $targetDirectory = "artwork_uploads/";
    $targetFile = $targetDirectory . basename($_FILES["artwork"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["artwork"]["tmp_name"]);
    if ($check === false) {
        echo "Error: File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($targetFile)) {
        echo "Error: File already exists.";
        $uploadOk = 0;
    }

    // Check file size (limit it to 5MB for example)
    if ($_FILES["artwork"]["size"] > 5000000) {
        echo "Error: File is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedExtensions)) {
        echo "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Error: Your file was not uploaded.";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["artwork"]["tmp_name"], $targetFile)) {
            // Insert artwork details into the database
            $insertStmt = $conn->prepare("INSERT INTO artwork (Title, Description, ArtistID, FilePath) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("ssis", $title, $description, $artistID, $targetFile);

            if ($insertStmt->execute()) {
                // Redirect to allart.php after successful upload
                header("Location: allart.php");
                exit;
            } else {
                echo "Error: Failed to insert artwork details into the database. " . $insertStmt->error;
            }

            $insertStmt->close();
        } else {
            echo "Error: There was an error uploading your file.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="upload.css">
    <title>Upload Artwork</title>
</head>
<body>

    <h2>Artwork Upload</h2>
    <!-- Your artwork upload form -->
    <form action="upload_artwork.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br>

        <label for="artwork">Artwork File:</label>
        <input type="file" name="artwork" accept="image/*" required><br>

        <input type="submit" value="Upload Artwork">
    </form>

</body>
</html>
