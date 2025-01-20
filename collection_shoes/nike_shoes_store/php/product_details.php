<?php
// Include database connection file
include '../../includes/config/db_config.php';

// Get the product ID from the URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If no valid product ID, redirect to the homepage
if ($product_id <= 0) {
    header("Location: index.php");
    exit;
}

// Query to get the product details along with all associated images
$sql = "
    SELECT p.product_id, p.name, p.description, p.price, p.brand, p.color, p.size, i.stock, pi.image
    FROM products p
    LEFT JOIN inventory i ON p.product_id = i.product_id
    LEFT JOIN product_images pi ON p.product_id = pi.product_id
    WHERE p.product_id = ?;
";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the product exists
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $images = [];

    // Fetch all images associated with the product
    while ($image = $result->fetch_assoc()) {
        $images[] = $image['image'];
    }
} else {
    echo "<p>Product not found.</p>";
    exit;
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title><?php echo htmlspecialchars($product['name']); ?> - Nike Shoes Store</title>
    <style>
    /* General Styles */
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
        color: #333;
    }

    /* Header Styles */
    header {
        background-color: #ff5722;
        padding: 20px 0;
        text-align: center;
        color: white;
        font-size: 32px;
        font-weight: 600;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    /* Product Details Section */
    .product-details {
        display: flex;
        justify-content: center;
        margin-top: 100px;
        gap: 30px;
        padding: 15px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 950px;
        margin: 100px auto 50px;
    }

    /* Image Gallery */
    .image-gallery {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .main-image {
        width: 100%;
        height: 490px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
    }

    .main-image:hover {
        transform: scale(1.05);
    }

    .thumbnail-gallery {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .thumbnail-gallery img {
        width: 18%;
        cursor: pointer;
        border-radius: 8px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .thumbnail-gallery img:hover {
        transform: scale(1.1);
    }

    /* Product Information Section */
    .product-info {
        flex: 1.5;
        display: flex;
        flex-direction: column;
        gap: 20px;
        font-size: 1rem;
        justify-content: space-between;
    }

    .product-info h1 {
        font-size: 2.2rem;
        font-weight: 700;
        color: #333;
    }

    .product-info .price {
        font-size: 1.8rem;
        font-weight: bold;
        color: #ff5722;
    }

    .product-info .description {
        color: #555;
        line-height: 1.5;
    }

    .product-info .details p {
        font-size: 1rem;
        color: #777;
        margin: 10px 0;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        justify-content: flex-end;
    }

    .action-buttons a {
        padding: 12px 20px;
        background-color: #ff5722;
        color: white;
        text-decoration: none;
        font-size: 1.1rem;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .action-buttons a:hover {
        background-color: #e64a19;
        transform: scale(1.05);
    }

    /* Color and Size Options */
    .color-options {
        display: flex;
        gap: 15px;
    }

    .color-option {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
    }

    .color-option:hover {
        opacity: 0.7;
    }

    .size-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .size-option {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .size-option:hover {
        background-color: #ff5722;
        color: white;
    }

    .out-of-stock {
        color: red;
        font-weight: bold;
    }

    /* Footer Styles */
    footer {
        background-color: #333;
        color: white;
        text-align: center;
        padding: 20px;
        width: 100%;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .product-details {
            flex-direction: column;
            gap: 20px;
        }

        .image-gallery {
            width: 100%;
            margin-bottom: 30px;
        }

        .thumbnail-gallery {
            flex-direction: column;
            gap: 10px;
        }

        .product-info {
            width: 100%;
        }

        .main-image {
            height: 300px;
        }
    }
    </style>
</head>

<body>

    <!-- Include Header -->
    <?php include '../include/nav.php'; ?>

    <!-- Product Details Section -->
    <section class="product-details">
        <!-- Image Gallery -->
        <div class="image-gallery">
            <!-- Use the second image as the main image -->
            <img id="mainImage"
                src="<?php echo '../../includes/images/nike_shoes/' . htmlspecialchars($images[1] ?? $images[0]); ?>"
                alt="Main Image" class="main-image">

            <div class="thumbnail-gallery">
                <?php foreach ($images as $image): ?>
                <img src="<?php echo '../../includes/images/nike_shoes/' . htmlspecialchars($image); ?>"
                    alt="Product Image" class="thumbnail"
                    onclick="changeImage('<?php echo '../../includes/images/nike_shoes/' . htmlspecialchars($image); ?>')" />
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Product Information -->
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="price">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
            <p class="description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <div class="details">
                <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand']); ?></p>

                <!-- Color Options -->
                <p><strong>Color:</strong></p>
                <div class="color-options">
                    <?php
                    $colors = ['Black', 'Grey', 'Red', 'Blue', 'Green', 'Yellow', 'Brown'];
                    foreach ($colors as $color) {
                        echo '<div class="color-option" style="background-color: ' . strtolower($color) . ';" title="' . $color . '"></div>';
                    }
                    ?>
                </div>

                <!-- Size Options -->
                <p><strong>Size:</strong></p>
                <div class="size-options">
                    <?php
                    $sizes = [35, 36, 37, 38, 39];
                    foreach ($sizes as $size) {
                        echo '<div class="size-option">' . $size . '</div>';
                    }
                    ?>
                </div>

                <!-- Stock Info -->
                <div class="stock-info">
                    <?php if ($product['stock'] > 0): ?>
                    <p>In Stock: <?php echo $product['stock']; ?> pairs available</p>
                    <?php else: ?>
                    <p class="out-of-stock">Out of Stock</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <!-- <div class="action-buttons">
                <a href="#">Add to Wishlist</a>
                <a href="#">Add to Cart</a>
            </div> -->
            <div class="action-buttons">
                <a href="add_to_wishlist.php?id=<?php echo $product['product_id']; ?>">Add to Wishlist</a>
                <a href="add_to_cart.php?id=<?php echo $product['product_id']; ?>">Add to Cart</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Nike Shoes Store. All Rights Reserved.</p>
    </footer>

    <script>
    // Function to change the main image
    function changeImage(imageSrc) {
        document.getElementById('mainImage').src = imageSrc;
    }
    </script>

</body>

</html>