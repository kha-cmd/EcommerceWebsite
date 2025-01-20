<?php
// Include database connection
include('../../includes/config/db_config.php');

// Handle product insertion
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $brand = $_POST['brand'];
    $color = $_POST['color'];
    $size = $_POST['size'];

    // Insert the main product data into the `products` table
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, brand, color, size) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdisss", $name, $description, $price, $category_id, $brand, $color, $size);

    if ($stmt->execute()) {
        $product_id = $stmt->insert_id; // Get the ID of the inserted product

        // Handle multiple image uploads
        if (isset($_FILES['images']) && count($_FILES['images']['name']) > 0) {
            $image_folder = '../../includes/images/nike_shoes/';
            foreach ($_FILES['images']['name'] as $key => $image_name) {
                if ($_FILES['images']['error'][$key] === 0) {
                    $new_image_name = time() . '_' . basename($image_name);
                    $target_file = $image_folder . $new_image_name;

                    // Move uploaded file to target folder
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                        // Insert image path into `product_images` table
                        $img_stmt = $conn->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                        $img_stmt->bind_param("is", $product_id, $new_image_name);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }
        }

        echo "<div class='alert alert-success'>Product and images added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Fetch all products
$query = "SELECT p.product_id, p.name, p.price, p.description, p.category_id, p.brand, p.color, p.size, c.name AS category_name 
          FROM products p
          JOIN categories c ON p.category_id = c.category_id";
// $query = "SELECT 
//             p.product_id, 
//             p.name, 
//             p.price, 
//             p.description, 
//             p.category_id, 
//             p.brand, 
//             p.color, 
//             p.size, 
//             c.name AS category_name, 
//             pi.image
//           FROM products p
//           JOIN categories c ON p.category_id = c.category_id
//           LEFT JOIN product_images pi ON p.product_id = pi.product_id";
$result = $conn->query($query);

// Search functionality
// $search = "";
// if (isset($_POST['search'])) {
//     $search = $_POST['search'];
//     $query .= " WHERE p.name LIKE ? OR p.description LIKE ?";
//     $stmt = $conn->prepare($query);
//     $search_term = "%" . $search . "%";
//     $stmt->bind_param("ss", $search_term, $search_term);
//     $stmt->execute();
//     $result = $stmt->get_result();
// }
// Search functionality
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    // Modify the query to allow searching by name, price, category, and brand
    $query .= " WHERE p.name LIKE ? OR p.price LIKE ? OR c.name LIKE ? OR p.brand LIKE ?";
    $stmt = $conn->prepare($query);
    $search_term = "%" . $search . "%";
    $stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term); // Added 4 placeholders
    $stmt->execute();
    $result = $stmt->get_result();
}
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
    /* .search-btn { flex-grow: 1; }   */
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
    </style>
</head>

<body>

    <!-- <div class="sidebar">
    <div class="sidebar-header text-center">
        <h2>Admin Panel</h2>
    </div>
    <a href="index.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
    <a href="manage_products.php"><i class="fa fa-cogs"></i> Manage Products</a>
    <a href="view_orders.php"><i class="fa fa-box"></i> View Orders</a>
</div> -->
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
        <!-- Search & Add Product Section -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <button class="btn btn-primary" id="addProductButton">Add Product</button>
            <!-- Search Form -->
            <form method="POST" class="d-flex search-container">
                <input type="text" name="search" class="form-control search-bar" value="<?= $search; ?>"
                    placeholder="Search by name, price, category, or brand" />
                <button type="submit" class="btn btn-primary ml-2 search-btn">Search</button>
            </form>
        </div>

        <!-- Add Product Form (Toggled) -->
        <div class="form-container">
            <div id="add-product-form">
                <h3>Add Product</h3>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <!-- Populate dynamically from DB -->
                            <?php 
                        $category_query = "SELECT * FROM categories";
                        $categories = $conn->query($category_query);
                        while ($category = $categories->fetch_assoc()) {
                            echo "<option value='{$category['category_id']}'>{$category['name']}</option>";
                        }
                        ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="brand" class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" value="Nike" required>
                    </div>
                    <!-- <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" name="color" class="form-control" required>
                    </div> -->
                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <select name="color" class="form-select" required>
                            <option value="">Select Color</option>
                            <option value="black">Black</option>
                            <option value="white">White</option>
                            <option value="gray">Gray</option>
                            <option value="red">Red</option>
                            <option value="blue">Blue</option>
                            <option value="green">Green</option>
                            <option value="yellow">Yellow</option>
                            <option value="brown">Brown</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="size" class="form-label">Size</label>
                        <select name="size" class="form-select" required>
                            <option value="">Select Size</option>
                            <option value="35">35</option>
                            <option value="36">36</option>
                            <option value="37">37</option>
                            <option value="38">38</option>
                            <option value="39">39</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="images" class="form-label">Images</label>
                        <input type="file" name="images[]" class="form-control" multiple>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
                </form>
            </div>
        </div>

        <!-- Product Table -->
        <h2 class="text-center mb-4">Product List</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Images</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $product['product_id']; ?></td>
                    <td><?= $product['name']; ?></td>
                    <td>$<?= number_format($product['price'], 2); ?></td>
                    <td><?= $product['category_name']; ?></td>
                    <td><?= $product['brand']; ?></td>
                    <td>
                        <?php
                        $image_query = "SELECT image FROM product_images WHERE product_id = ?";
                        $img_stmt = $conn->prepare($image_query);
                        $img_stmt->bind_param("i", $product['product_id']);
                        $img_stmt->execute();
                        $img_result = $img_stmt->get_result();
                        while ($image = $img_result->fetch_assoc()) {
                            echo "<img src='../../includes/images/nike_shoes/" . $image['image'] . "' class='img-thumbnail' width='50' height='50'>";
                        }
                        ?>
                    </td>
                    <td>
                        <a href="view_product.php?id=<?= $product['product_id']; ?>" class="action-link view"
                            style="text-decoration: none; font-weight: 500;">View</a> |
                        <a href="edit_product.php?id=<?= $product['product_id']; ?>" class="action-link edit"
                            style="text-decoration: none; font-weight: 500;">Edit</a> |
                        <a href="delete_product.php?id=<?= $product['product_id']; ?>" class="action-link delete"
                            style="text-decoration: none; font-weight: 500;">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
    // Toggle Add Product Form Visibility
    document.getElementById('addProductButton').addEventListener('click', function() {
        const form = document.getElementById('add-product-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });
    </script>
</body>

</html>