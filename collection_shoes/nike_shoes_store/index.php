<?php
// Include database connection file
include '../includes/config/db_config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Nike Shoes Store</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Main Banner */
        .main-banner {
            position: relative;
            text-align: center;
            color: white;
        }

        .banner-img {
            width: 100%;
            height: auto;
        }

        .banner-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 10px;
        }

        .banner-text h1 {
            font-size: 2.5rem;
            margin: 0;
        }

        .banner-text p {
            font-size: 1.2rem;
            margin: 10px 0;
        }

        .shop-now {
            display: inline-block;
            background-color: #ff5722;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .shop-now:hover {
            background-color: #ff5722;
        }

        /* Featured Products */
        .featured-products {
            padding: 20px;
            background-color: #f4f4f4;
        }

        .featured-products h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .product-container {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            /* 6 products in one row */
            gap: 20px;
        }

        .product {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            padding: 15px;
            transition: transform 0.3s ease;
        }

        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .product img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .product h3 {
            font-size: 1.1rem;
            margin: 10px 0;
            color: #333;
        }

        .product p {
            font-size: 1rem;
            color: #ff5722;
            font-weight: bold;
        }

        .product a {
            display: inline-block;
            margin-top: 10px;
            color: white;
            background-color: #ff5722;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }

        .product a:hover {
            background-color: #ff5722;
        }
    </style>
</head>

<body>
    <!-- Include Navigation Bar -->
    <?php include 'include/nav.php'; ?>

    <!-- Main Banner Section -->
    <header class="main-banner">
        <img src="images/hero_banner.jpg" alt="Welcome to Nike Shoes Store" class="banner-img">
        <div class="banner-text">
            <h1>Welcome to Nike Shoes Store</h1>
            <p>Discover the latest in Nike footwear. Elevate your game with style and performance.</p>
            <a href="shop.php" class="shop-now">Shop Now</a>
        </div>
    </header>

    <!-- Featured Products Section -->
    <section class="featured-products">
        <h2>Featured Products</h2>
        <div class="product-container">
            <?php
            // Query to get the first image for each product
            $sql = "
                SELECT p.product_id, p.name, p.price, pi.image
                FROM products p
                LEFT JOIN product_images pi ON p.product_id = pi.product_id
                WHERE pi.image IS NOT NULL
                GROUP BY p.product_id
                ORDER BY pi.image ASC
                LIMIT 6
            ";

            // Execute the query
            $result = $conn->query($sql);

            // Check if products exist
            if ($result && $result->num_rows > 0) {
                while ($product = $result->fetch_assoc()) {
                    // Construct the image path (taking the first image found)
                    $imagePath = '../includes/images/nike_shoes/' . $product['image'];
                    $imageSrc = file_exists($imagePath) ? $imagePath : 'images/placeholder.jpg';

                    // Display the product
                    echo "
                    <div class='product'>
                        <img src='" . htmlspecialchars($imageSrc) . "' alt='" . htmlspecialchars($product['name']) . "'>
                        <h3>" . htmlspecialchars($product['name']) . "</h3>
                        <p>$" . htmlspecialchars(number_format($product['price'], 2)) . "</p>
                        <a href='php/product_details.php?id=" . htmlspecialchars($product['product_id']) . "'>View Details</a>
                    </div>
                    ";
                }
            } else {
                echo "<p>No featured products available.</p>";
            }

            // Close the database connection
            $conn->close();
            ?>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include 'include/footer.php'; ?>
</body>

</html>