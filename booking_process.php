<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $guest_name = sanitize($_POST['guest_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $room_type = sanitize($_POST['room_type']);
    $check_in = sanitize($_POST['check_in']);
    $check_out = sanitize($_POST['check_out']);
    $adults = intval($_POST['adults']);
    $children = intval($_POST['children']);
    
    // Validate required fields
    if (empty($guest_name) || empty($email) || empty($phone) || empty($room_type) || 
        empty($check_in) || empty($check_out)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO bookings (guest_name, email, phone, room_type, check_in, check_out, adults, children, booking_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssssii", $guest_name, $email, $phone, $room_type, $check_in, $check_out, $adults, $children);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Booking successfully created']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error creating booking: ' . $conn->error]);
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>