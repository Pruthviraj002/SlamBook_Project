<?php
session_start();

// Check if the admin ID is present in the URL
if (!isset($_GET['user_id']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("location: error.php");
    exit;
}

// Add database connection and necessary queries here
include 'partials/dbcon.php';

// Fetch user data based on user ID
$userId = $_GET['user_id'];
$stmtDownload = $conn->prepare("
    SELECT user.*, basicinfo.*, fav.*, aspiration.*, questions.*, images.*
    FROM user
    LEFT JOIN basicinfo ON user.id = basicinfo.user_id
    LEFT JOIN fav ON basicinfo.id = fav.u_id
    LEFT JOIN aspiration ON basicinfo.id = aspiration.u_id
    LEFT JOIN questions ON aspiration.id = questions.u_id
    LEFT JOIN images ON questions.id = images.u_id
    WHERE user.id = ?
");

if (!$stmtDownload) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    exit;
}

$stmtDownload->bind_param('i', $userId);
$stmtDownload->execute();

$result = $stmtDownload->get_result();
$userDataDownload = $result->fetch_all(MYSQLI_ASSOC);

// Generate PDF using TCPDF
require_once('tcpdf/tcpdf.php');

// Create new PDF document
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('User Information');
$pdf->SetSubject('User Information PDF');
$pdf->SetKeywords('TCPDF, PDF, user, information');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add Slambook heading
$pdf->Cell(0, 10, 'Slambook', 'B', 1, 'C');

// Output user data in PDF format
foreach ($userDataDownload as $row) {
    // Add profile image if available
    if (!empty($row['Photo'])) {
        $pdf->Image('profile_uploads/' . $row['Photo'], 140, 34, 40, 40); // Positioned on the right side
    } else {
        $pdf->Cell(0, 10, 'No photo available.', 0, 1);
    }

    // Add Basic Information heading
    $pdf->Cell(0, 10, 'Basic Information', 'B', 1, 'C');

    $pdf->Cell(0, 10, 'Full Name: ' . $row['Full_Name'], 0, 1);
    $pdf->Cell(0, 10, 'Nick Name: ' . $row['Nick_Name'], 0, 1);
    $pdf->Cell(0, 10, 'Gender: ' . $row['Gender'], 0, 1);
    $pdf->Cell(0, 10, 'Relationship Status: ' . $row['Relationship_status'], 0, 1);
    $pdf->Cell(0, 10, 'Birthday: ' . $row['BirthDay'], 0, 1);
    $pdf->Cell(0, 10, 'Class/Job: ' . $row['Class_Job'], 0, 1);
    $pdf->Cell(0, 10, 'Email: ' . $row['Email'], 0, 1);
    $pdf->Cell(0, 10, 'Phone: ' . $row['Phone'], 0, 1);
    $pdf->Cell(0, 10, 'Address: ' . $row['Address'], 0, 1);

    // Add Favourites Information heading
    $pdf->Cell(0, 10, 'Favourites', 'B', 1, 'C');

    $pdf->Cell(0, 10, 'Food: ' . $row['Food'], 0, 1);
    $pdf->Cell(0, 10, 'Subject: ' . $row['Subject'], 0, 1);
    $pdf->Cell(0, 10, 'Movie: ' . $row['Movie'], 0, 1);
    $pdf->Cell(0, 10, 'Actor/Actress: ' . $row['Actor_Actress'], 0, 1);
    $pdf->Cell(0, 10, 'Color: ' . $row['Color'], 0, 1);
    $pdf->Cell(0, 10, 'Place: ' . $row['Place'], 0, 1);
    $pdf->Cell(0, 10, 'Singer: ' . $row['Singer'], 0, 1);
    $pdf->Cell(0, 10, 'Game: ' . $row['Game'], 0, 1);
    $pdf->Cell(0, 10, 'Hobbies: ' . $row['Hobbies'], 0, 1);


    // Add Aspiration heading
    $pdf->Cell(0, 10, 'Aspiration', 'B', 1, 'C');


    $pdf->Cell(0, 10, 'Future Goals: ' . $row['Future_goals'], 0, 1);
    $pdf->Cell(0, 10, 'Dream Job: ' . $row['Dream_job'], 0, 1);
    $pdf->Cell(0, 10, 'Bucket List: ' . $row['Bucket_list'], 0, 1);

    // Add Fun Questions heading
    $pdf->Cell(0, 10, 'Fun Questions', 'B', 1, 'C');

    $pdf->Cell(0, 10, 'If you got to choose your name, what would it be and why?: ' . $row['question1'], 0, 1);
    $pdf->Cell(0, 10, 'If you could be best friends with a character in any animated show, which would you choose?: ' . $row['question2'], 0, 1);
    $pdf->Cell(0, 10, 'If you could have one superpower,what would it be?: ' . $row['question3'], 0, 1);

    // Add Social Media Information heading
    $pdf->Cell(0, 10, 'Social Media', 'B', 1, 'C');

    $pdf->Cell(0, 10, 'Social Media: ' . $row['Social_media'], 0, 1);

    // Add a new page for the gallery
    $pdf->AddPage();

    // Output gallery images horizontally
    $stmtImages = $conn->prepare("SELECT * FROM images WHERE u_id = ?");
    $stmtImages->bind_param('i', $row['user_id']);
    $stmtImages->execute();
    $resultImages = $stmtImages->get_result();

    // Set image dimensions
    $imageWidth = 60;
    $imageHeight = 60;
    $pdf->Cell(0, 10, 'Gallery', 'B', 1, 'C');
    while ($image = $resultImages->fetch_assoc()) {
        $imagePath = 'uploads/' . $image['image_url'];
        list($width, $height, $type, $attr) = getimagesize($imagePath);

        // Check image format
        switch ($type) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_PNG:
            case IMAGETYPE_GIF:
                // Output image with 90 degrees clockwise rotation
                $pdf->Image($imagePath, $pdf->GetX(), $pdf->GetY(), $imageWidth, $imageHeight, '', '', '', false, 90);
                $pdf->Cell($imageWidth); // Move the cursor to the next position
                break;
            // Add more cases for other image formats if needed
        }
    }
    $stmtImages->close();
}

// Create a unique temporary file name
$pdfFilePath = tempnam(sys_get_temp_dir(), 'user_info_');

// Output the PDF file
$pdf->Output($pdfFilePath, 'F');

// Send headers to force file download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="user_info.pdf"');
header('Content-Length: ' . filesize($pdfFilePath));

// Output the PDF file
readfile($pdfFilePath);

// Close database connection
$conn->close();

// Delete the temporary file
unlink($pdfFilePath);
?>