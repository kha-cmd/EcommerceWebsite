<?php
include('../../includes/config/db_config.php');

$product_id = $_GET['id']; // Get product ID from URL

// Fetch product details from the database
$query = "SELECT p.product_id, p.name, p.price, p.description, p.category_id, p.brand, p.color, p.size, c.name AS category_name 
          FROM products p
          JOIN categories c ON p.category_id = c.category_id
          WHERE p.product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<div class='alert alert-danger'>Product not found.</div>";
    exit();
}

// Fetch associated images from the `product_images` table
$image_query = "SELECT image FROM product_images WHERE product_id = ?";
$image_stmt = $conn->prepare($image_query);
$image_stmt->bind_param("i", $product_id);
$image_stmt->execute();
$image_result = $image_stmt->get_result();
$images = $image_result->fetch_all(MYSQLI_ASSOC);

$image_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Product</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    .card-body {
        padding: 20px;
    }

    .card img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .btn {
        margin-right: 10px;
    }

    .container {
        max-width: 1200px;
    }

    .product-images {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .product-images img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .main-content {
        margin-left: 260px;
        padding: 30px;
        transition: margin-left 0.3s ease;
    }

    .dashboard-header {
        background: #fff;
        padding: 20px 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }

    .dashboard-header h1 {
        font-size: 26px;
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 0;
    }

    .dashboard-header p {
        font-size: 16px;
        color: #4a5568;
        font-weight: 500;
    }

    .logout-btn {
        background-color: #38a169;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        transition: background-color 0.3s, transform 0.3s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .logout-btn:hover {
        background-color: #2f855a;
        transform: translateY(-2px);
    }

    .logout-btn:active {
        transform: translateY(0);
    }
    </style>
</head>

<body>
    <?php include '../../includes/partials/sidebar.php'; ?>
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <h1>Manage Products</h1>
                <p>Welcome, Admin!</p>
            </div>
            <div style="display: flex; align-items: center;">
                <form action="logout.php" method="POST" style="display: inline;">
                    <button type="submit" class="logout-btn">Logout <i class="fas fa-sign-out-alt"></i></button>
                </form>
            </div>
        </div>
        <div class="container mt-4">
            <h2 class="text-center mb-4">Product Details</h2>

            <!-- Product Information Card -->
            <div class="card shadow-lg">
                <div class="row g-0">
                    <!-- Left Column: Images -->
                    <div class="col-md-4">
                        <div class="product-images">
                            <?php if (!empty($images)): ?>
                            <?php foreach ($images as $image): ?>
                            <img src="../../includes/images/nike_shoes/<?= $image['image']; ?>" alt="Product Image">
                            <?php endforeach; ?>
                            <?php else: ?>
                            <p>No images available for this product.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Right Column: Details -->
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><strong>Category:</strong>
                                <?= htmlspecialchars($product['category_name']); ?></p>
                            <p class="card-text"><strong>Brand:</strong> <?= htmlspecialchars($product['brand']); ?></p>
                            <p class="card-text"><strong>Color:</strong> <?= htmlspecialchars($product['color']); ?></p>
                            <p class="card-text"><strong>Size:</strong> <?= htmlspecialchars($product['size']); ?></p>
                            <p class="card-text"><strong>Description:</strong>
                                <?= htmlspecialchars($product['description']); ?></p>
                            <p class="card-text"><strong>Price:</strong> $<?= number_format($product['price'], 2); ?>
                            </p>
                            <a href="manage_products.php" class="btn btn-secondary me-2"><i
                                    class="fas fa-arrow-left"></i> Back</a>
                            <a href="edit_product.php?id=<?= $product['product_id']; ?>" class="btn btn-warning me-2"><i
                                    class="fas fa-edit"></i> Edit</a>
                            <a href="delete_product.php?id=<?= $product['product_id']; ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this product?');"><i
                                    class="fas fa-trash"></i> Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>