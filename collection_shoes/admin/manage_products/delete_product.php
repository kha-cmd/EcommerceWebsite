<?php
include('../../includes/config/db_config.php');

// Get product ID from URL
$product_id = $_GET['id'];

// Delete the product from the database
$delete_query = "DELETE FROM products WHERE product_id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    echo "<div class='alert alert-success'>Product deleted successfully!</div>";
} else {
    echo "<div class='alert alert-danger'>Error deleting product: " . $stmt->error . "</div>";
}

$stmt->close();
$conn->close();

// Redirect back to the product management page
header("Location: manage_products.php");
exit();
?>