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
    <title>Shop - Nike Shoes Store</title>
    <style>
        /* General styles for the page */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            padding-top: 80px;
            /* Adjust based on your header's height */
        }

        /* Shop page container */
        .shop-products {
            padding: 20px;
            background-color: #f4f4f4;
            text-align: center;
            /* Center the content */
        }

        /* Center the "All Products" heading */
        .shop-products h2 {
            margin-bottom: 20px;
            font-size: 2rem;
            color: #333;
        }

        /* Category filter styling */
        .category-filters form {
            margin-bottom: 20px;
            display: inline-block;
            /* To center the form */
        }

        /* Center the category select box */
        .category-filters select {
            padding: 15px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 250px;
            /* Adjust the width of the select box */
            text-align: center;
            /* Center the text */
        }

        /* Product container with 6 columns in one row */
        .product-container {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            /* 6 cards per row */
            gap: 20px;
        }

        /* Product card styles */
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
            height: auto;
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
            background-color: #e64a19;
        }

        /* Ensure header doesn't overlap fixed-position elements */
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 999;
            background-color: #fff;
            /* Adjust if needed */
        }

        /* Padding adjustments for content */
        .shop-products {
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <!-- Include Navigation Bar -->
    <?php include 'include/nav.php'; ?>

    <!-- Shop Page Content -->
    <section class="shop-products">
        <h2>All Products</h2>
        <div class="category-filters">
            <form method="GET" action="shop.php">
                <select name="category" onchange="this.form.submit()">
                    <option value="">Select Category</option>
                    <option value="0"
                        <?php echo isset($_GET['category']) && $_GET['category'] == '0' ? 'selected' : ''; ?>>All
                        Categories</option>
                    <?php
                    // Fetch categories from the database
                    $categoryQuery = "SELECT category_id, name FROM categories";
                    $categoryResult = $conn->query($categoryQuery);

                    // Loop through categories and create the filter options
                    while ($category = $categoryResult->fetch_assoc()) {
                        $selected = isset($_GET['category']) && $_GET['category'] == $category['category_id'] ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($category['category_id']) . "' $selected>" . htmlspecialchars($category['name']) . "</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <div class="product-container">
            <?php
            // Set the category filter if it exists in the query
            $categoryFilter = isset($_GET['category']) && $_GET['category'] != '0' ? "AND p.category_id = " . (int)$_GET['category'] : "";

            // Query to get all products with the first image for each product
            $sql = "
                SELECT p.product_id, p.name, p.price, pi.image
                FROM products p
                LEFT JOIN product_images pi ON p.product_id = pi.product_id
                WHERE pi.image IS NOT NULL
                $categoryFilter
                GROUP BY p.product_id
                ORDER BY p.product_id ASC
            ";

            // Execute the query
            $result = $conn->query($sql);

            // Check if products exist
            if ($result && $result->num_rows > 0) {
                while ($product = $result->fetch_assoc()) {
                    // Construct the image path (taking the first image found)
                    $imagePath = '../includes/images/nike_shoes/' . $product['image'];
                    $imageSrc = file_exists($imagePath) ? $imagePath : 'images/placeholder.jpg';

                    // Display the product (name and price only)
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
                echo "<p>No products available.</p>";
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