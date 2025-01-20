<?php
include('../../includes/config/db_config.php');

// Get product ID from URL
$product_id = $_GET['id'];

// Fetch product details from the database
$query = "SELECT p.product_id, p.name, p.price, p.description, p.category_id, p.brand, p.color, p.size, p.image, c.name AS category_name 
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

// Handle form submission for updating the product
if (isset($_POST['update_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $brand = $_POST['brand'];
    $color = $_POST['color'];
    $size = $_POST['size'];
    $image_name = $product['image']; // Keep the old image if no new image is selected

    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
        // Handle image upload
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = '../../includes/images/nike_shoes/' . $image_name;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            echo "<div class='alert alert-danger'>Error uploading image.</div>";
        }
    }

    // Update product in the database
    $update_query = "UPDATE products SET name = ?, price = ?, description = ?, category_id = ?, brand = ?, color = ?, size = ?, image = ? WHERE product_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sdisssssi", $name, $price, $description, $category_id, $brand, $color, $size, $image_name, $product_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Product updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Fetch categories for the dropdown
$category_query = "SELECT category_id, name FROM categories";
$category_result = $conn->query($category_query);

// Fetch all images for the product
$image_query = "SELECT image FROM product_images WHERE product_id = ?";
$image_stmt = $conn->prepare($image_query);
$image_stmt->bind_param("i", $product_id);
$image_stmt->execute();
$image_result = $image_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f7fc;
    }

    .sidebar {
        width: 260px;
        background: #1a202c;
        color: #fff;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 30px;
        transition: width 0.3s ease;
    }

    .sidebar a {
        color: #bfc7d5;
        text-decoration: none;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        font-size: 15px;
        font-weight: 500;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: background 0.3s, padding-left 0.3s;
    }

    .sidebar a:hover {
        background: #2d3748;
        padding-left: 32px;
        color: #fff;
    }

    .sidebar a i {
        margin-right: 12px;
        font-size: 18px;
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
        /* Green for logout button */
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

    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .stat-card i {
        font-size: 28px;
        color: #38a169;
        margin-bottom: 15px;
    }

    .stat-card h3 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stat-card p {
        font-size: 14px;
        color: #4a5568;
    }

    .quick-links {
        margin-top: 30px;
    }

    .quick-links h3 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2d3748;
    }

    .quick-links a {
        display: flex;
        align-items: center;
        padding: 15px;
        margin-bottom: 12px;
        background: #fff;
        border-radius: 12px;
        color: #2d3748;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .quick-links a:hover {
        background: #f7fafc;
        transform: translateY(-4px);
    }

    .quick-links a i {
        margin-right: 12px;
        color: #38a169;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
            padding-top: 20px;
        }

        .main-content {
            margin-left: 0;
        }
    }

    #add-product-form {
        display: none;
    }

    .main {
        margin-left: 260px;
        padding: 30px;
        transition: margin-left 0.3s ease;
    }

    /* .search-container { display: flex; justify-content: space-between; align-items: center; } */
    /* .search-bar { width: 100%; max-width: 400px; } */
    /* .search-btn { flex-grow: 1; } */
    .search-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 40%;
    }

    .search-bar {
        margin-right: 5px;
    }

    /* .search-btn {
        flex-grow: 1;
        } */
    .btn {
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .btn i {
        margin-right: 5px;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-outline-primary {
        border-color: #4a90e2;
        color: #4a90e2;
    }

    .btn-outline-primary:hover {
        background-color: #4a90e2;
        color: #fff;
    }

    .btn-outline-warning {
        border-color: #f5a623;
        color: #f5a623;
    }

    .btn-outline-warning:hover {
        background-color: #f5a623;
        color: #fff;
    }

    .btn-outline-danger {
        border-color: #d0021b;
        color: #d0021b;
    }

    .btn-outline-danger:hover {
        background-color: #d0021b;
        color: #fff;
    }

    .action-link {
        text-decoration: none;
        font-weight: bold;
        margin-right: 10px;
        transition: color 0.3s ease;
    }

    .action-link.view {
        color: #3498db;
        /* Bright blue */
    }

    .action-link.edit {
        color: #f1c40f;
        /* Golden yellow */
    }

    .action-link.delete {
        color: #e74c3c;
        /* Vivid red */
    }

    .action-link:hover {
        color: #2c3e50;
        /* Deep navy for hover effect */
    }

    .img-preview {
        width: 100px;
        height: 100px;
        margin: 5px;
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
            <form action="logout.php" method="POST" style="display: inline;">
                <button type="submit" class="logout-btn">Logout <i class="fas fa-sign-out-alt"></i></button>
            </form>
        </div>
        <div class="container mt-4">
            <a href="manage_products.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i>Back to
                Product List</a>
            <h2 class="text-center mb-4">Edit Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $product['name']; ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" class="form-control" id="price" name="price" value="<?= $product['price']; ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description"
                        required><?= $product['description']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <?php while ($category = $category_result->fetch_assoc()) { ?>
                        <option value="<?= $category['category_id']; ?>"
                            <?= $category['category_id'] == $product['category_id'] ? 'selected' : ''; ?>>
                            <?= $category['name']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="brand" class="form-label">Brand</label>
                    <input type="text" class="form-control" id="brand" name="brand" value="<?= $product['brand']; ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="color" class="form-label">Color</label>
                    <input type="text" class="form-control" id="color" name="color" value="<?= $product['color']; ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="size" class="form-label">Size</label>
                    <input type="text" class="form-control" id="size" name="size" value="<?= $product['size']; ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Select Image to Change</label>
                    <div class="d-flex flex-wrap">
                        <?php while ($image = $image_result->fetch_assoc()) { ?>
                        <div>
                            <img src="../../includes/images/nike_shoes/<?= $image['image']; ?>" class="img-preview"
                                alt="Product Image">
                            <input type="radio" name="image" value="<?= $image['image']; ?>" class="form-check-input"
                                <?= ($product['image'] == $image['image']) ? 'checked' : ''; ?>>
                        </div>
                        <?php } ?>
                    </div>
                    <small class="form-text text-muted">Select one image to update.</small>
                    <input type="file" class="form-control mt-3" id="image" name="image">
                    <small class="form-text text-muted">Or upload a new image if you want to change it.</small>
                </div>

                <div class="mb-3 text-center">
                    <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
<?php
    $conn->close();
?>