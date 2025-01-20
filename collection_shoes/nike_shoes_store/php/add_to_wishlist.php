<?php
// Include database connection file
include '../../includes/config/db_config.php';

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php?id=" . $_GET['id']);  // Redirect to sign in page if not logged in
    exit;
}

// Get the product ID and user ID from the session
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// If no valid product ID, redirect to the homepage
if ($product_id <= 0) {
    header("Location: index.php");
    exit;
}

// Check if the product is already in the wishlist
$sql_check = "SELECT * FROM wishlists WHERE user_id = ? AND product_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $product_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    // Add the product to the wishlist
    $sql_add = "INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)";
    $stmt_add = $conn->prepare($sql_add);
    $stmt_add->bind_param("ii", $user_id, $product_id);
    $stmt_add->execute();

    echo "<p>Product added to wishlist successfully!</p>";
} else {
    echo "<p>This product is already in your wishlist.</p>";
}

$stmt_check->close();
$stmt_add->close();
$conn->close();
?>