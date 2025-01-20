<?php
// Include database connection file
include('../../includes/config/db_config.php');

// Handle inventory insertion
if (isset($_POST['add_inventory'])) {
    $product_id = $_POST['product_id'];
    $stock = $_POST['stock'];

    // Prepare the SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO inventory (product_id, stock) VALUES (?, ?)");
    $stmt->bind_param("ii", $product_id, $stock);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Inventory added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close(); // Close statement after execution
}

// Handle inventory editing
// if (isset($_POST['edit_inventory'])) {
//     $inventory_id = $_POST['inventory_id'];
//     $product_id = $_POST['product_id'];
//     $stock = $_POST['stock'];

// Prepare the SQL statement to update data
//     $stmt = $conn->prepare("UPDATE inventory SET product_id = ?, stock = ? WHERE inventory_id = ?");
//     $stmt->bind_param("iii", $product_id, $stock, $inventory_id);

//     if ($stmt->execute()) {
//         echo "<div class='alert alert-success'>Inventory updated successfully!</div>";
//     } else {
//         echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
//     }

//     $stmt->close();
// }
// Fetch inventory data for editing
if (isset($_GET['inventory_id'])) {
    $inventory_id = $_GET['inventory_id'];

    // Query to fetch the specific inventory record
    $stmt = $conn->prepare("SELECT * FROM inventory WHERE inventory_id = ?");
    $stmt->bind_param("i", $inventory_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $inventory = $result->fetch_assoc();
    $stmt->close();
}

// Update inventory data
if (isset($_POST['update_inventory'])) {
    $inventory_id = $_POST['inventory_id'];
    $product_id = $_POST['product_id'];
    $stock = $_POST['stock'];

    // Update query
    $stmt = $conn->prepare("UPDATE inventory SET product_id = ?, stock = ? WHERE inventory_id = ?");
    $stmt->bind_param("iii", $product_id, $stock, $inventory_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Inventory updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
    // Redirect back to inventory page
    header("Location: manage_inventory.php");
    exit();
}

// Fetch all products for the dropdown
$sql_products = "SELECT product_id, name FROM products";
$result_products = $conn->query($sql_products);

// Handle inventory deletion
if (isset($_GET['delete'])) {
    $inventory_id = $_GET['delete'];

    // Prepare the SQL statement to delete data
    $stmt = $conn->prepare("DELETE FROM inventory WHERE inventory_id = ?");
    $stmt->bind_param("i", $inventory_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Inventory deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Pagination Setup
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get the current page number
$offset = ($page - 1) * $limit; // Correct calculation of offset

// Search, Sort & Order Setup
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'inventory_id'; // Default sorting by inventory_id
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // Default order is ascending

// SQL query to fetch inventory with search, sort, and pagination (show product names)
$sql = "SELECT inventory.inventory_id, products.name AS product_name, inventory.stock
FROM inventory
INNER JOIN products ON inventory.product_id = products.product_id
WHERE products.name LIKE ?
ORDER BY $sort $order
LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result(); // Execute and get the result

// Fetch total number of rows for pagination
$sql_total = "SELECT COUNT(*) as total FROM inventory
INNER JOIN products ON inventory.product_id = products.product_id
WHERE products.name LIKE ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("s", $searchTerm);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_rows = $total_result->fetch_assoc()['total']; // Get total number of rows
$total_pages = ceil($total_rows / $limit); // Calculate total number of pages

// Fetch products for the dropdown
$sql_products = "SELECT product_id, name FROM products";
$result_products = $conn->query($sql_products);

$stmt->close();
$stmt_total->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #333;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .action-btn i {
            margin-right: 8px;
            font-size: 18px;
        }

        .action-btn-edit {
            color: #007bff;
        }

        .action-btn-edit:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .action-btn-delete {
            color: #dc3545;
        }

        .action-btn-delete:hover {
            color: #c82333;
            text-decoration: underline;
        }

        /* Center the search bar */
        .search-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .search-container form {
            display: flex;
            width: 50%;
            justify-content: space-between;
        }

        .search-container input {
            width: 80%;
        }

        .btn-custom,
        .btn-back {
            border-radius: 5px;
            padding: 10px 20px;
        }

        .btn-custom:hover,
        .btn-back:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .main {
            flex-grow: 1;
            padding: 20px;
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

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
        }
    </style>
</head>

<body>
    <?php include '../../includes/partials/sidebar.php'; ?>
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <h1>Manage Inventory</h1>
                <p>Welcome, Admin</p>
            </div>
            <div style="display: flex; align-items: center;">
                <form action="logout.php" method="POST" style="display: inline;">
                    <button type="submit" class="logout-btn">Logout <i class="fas fa-sign-out-alt"></i></button>
                </form>
            </div>
        </div>

        <!-- Add Inventory Form -->
        <div class="mt-4">
            <!-- <h2>Add Inventory</h2> -->
            <form action="" method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="product_id" class="form-label">Products Name</label>
                    <select class="form-select" name="product_id" id="product_id" required>
                        <option value="" disabled selected>Select a product</option>
                        <?php while ($product = $result_products->fetch_assoc()) { ?>
                            <option value="<?php echo $product['product_id']; ?>"><?php echo $product['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control" name="stock" id="stock" required>
                </div>

                <button type="submit" name="add_inventory" class="btn btn-primary">Add Inventory</button>
            </form>
        </div>

        <div class="search-container">
            <form action="" method="GET">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search by product name" class="form-control">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th><a href="?sort=inventory_id&order=<?php echo $order == 'ASC' ? 'DESC' : 'ASC'; ?>">
                                Id</a></th>
                        <th><a href="?sort=product_name&order=<?php echo $order == 'ASC' ? 'DESC' : 'ASC'; ?>">Product
                                Name</a></th>
                        <th><a href="?sort=stock&order=<?php echo $order == 'ASC' ? 'DESC' : 'ASC'; ?>">Stock</a></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                        <td>{$row['inventory_id']}</td>
                                        <td>{$row['product_name']}</td>
                                        <td>{$row['stock']}</td>
                                        <td>
                                            <a href='#' class='action-btn action-btn-edit' data-bs-toggle='modal' data-bs-target='#editModal{$row['inventory_id']}'><i class='bi bi-pencil-square'></i> Edit</a>
                                            <a href='manage_inventory.php?delete={$row['inventory_id']}' class='action-btn action-btn-delete' onclick='return confirm(\"Are you sure you want to delete this inventory?\")'><i class='bi bi-trash'></i> Delete</a>
                                        </td>
                                    </tr>";

                            // Edit Modal for each category
                            echo "
                                <div class='modal fade' id='editModal{$row['inventory_id']}' tabindex='-1' aria-labelledby='editModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='editModalLabel'>Edit Inventory</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <form method='POST' action='manage_inventory.php'>
                                                    <input type='hidden' name='inventory_id' value='{$row['inventory_id']}'>
                                                    <div class='mb-3'>
                                                        <label for='product_name' class='form-label'>Product Name</label>
                                                        <input type='text' class='form-control' name='product_name' value='{$row['product_name']}' required>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label for='stock' class='form-label'>Stock</label>
                                                        <textarea class='form-control' name='stock'>{$row['stock']}</textarea>
                                                    </div>
                                                    <button type='submit' class='btn btn-primary' name='edit_inventory'>Update Inventory</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No inventories found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <li class="page-item"><a class="page-link"
                                href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </div>
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>