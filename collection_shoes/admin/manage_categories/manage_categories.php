<?php
// Include database connection file
include('../../includes/config/db_config.php');

// Handle category insertion
if (isset($_POST['add_category'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Prepare the SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Category added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close(); // Close statement after execution
}

// Handle category editing
if (isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Prepare the SQL statement to update data
    $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE category_id = ?");
    $stmt->bind_param("ssi", $name, $description, $category_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Category updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Handle category deletion
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];

    // Prepare the SQL statement to delete data
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Category deleted successfully!</div>";
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
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'category_id'; // Default sorting by category_id
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // Default order is ascending

// SQL query to fetch categories with search, sort, and pagination
$sql = "SELECT * FROM categories WHERE name LIKE ? OR description LIKE ? ORDER BY $sort $order LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result(); // Execute and get the result

// Fetch total number of rows for pagination
$sql_total = "SELECT COUNT(*) as total FROM categories WHERE name LIKE ? OR description LIKE ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("ss", $searchTerm, $searchTerm);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_rows = $total_result->fetch_assoc()['total']; // Get total number of rows
$total_pages = ceil($total_rows / $limit); // Calculate total number of pages

$stmt->close();
$stmt_total->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
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
        /* Blue for edit */
    }

    .action-btn-edit:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    .action-btn-delete {
        color: #dc3545;
        /* Red for delete */
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

    /* Custom button styling with slight rounded corners */
    .btn-custom,
    .btn-back {
        border-radius: 5px;
        /* Slight border-radius for rounded corners */
        padding: 10px 20px;
    }

    .btn-custom:hover,
    .btn-back:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Add hover effect for visual feedback */
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
                <h1>Manage Categories</h1>
                <p>Welcome, Admin!</p>
            </div>
            <div style="display: flex; align-items: center;">
                <form action="logout.php" method="POST" style="display: inline;">
                    <button type="submit" class="logout-btn">Logout <i class="fas fa-sign-out-alt"></i></button>
                </form>
            </div>
        </div>
        <!-- Centered Search Form -->
        <div class="search-container">
            <form class="d-flex" method="GET" action="manage_categories.php">
                <input class="form-control me-2" type="search" name="search"
                    value="<?php echo htmlspecialchars($search); ?>" placeholder="Search categories">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>

        <!-- Add Category Form -->
        <form method="POST" action="manage_categories.php" class="mb-5">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-custom" name="add_category">Add Category</button>
        </form>

        <!-- Category List Table -->
        <h2 class="mt-5">Existing Categories</h2>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th><a
                            href="?sort=category_id&order=<?php echo $order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo htmlspecialchars($search); ?>">Id</a>
                    </th>
                    <th><a
                            href="?sort=name&order=<?php echo $order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo htmlspecialchars($search); ?>">Name</a>
                    </th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['category_id']}</td>
                                        <td>{$row['name']}</td>
                                        <td>{$row['description']}</td>
                                        <td>
                                            <a href='#' class='action-btn action-btn-edit' data-bs-toggle='modal' data-bs-target='#editModal{$row['category_id']}'><i class='bi bi-pencil-square'></i> Edit</a>
                                            <a href='manage_categories.php?delete={$row['category_id']}' class='action-btn action-btn-delete' onclick='return confirm(\"Are you sure you want to delete this category?\")'><i class='bi bi-trash'></i> Delete</a>
                                        </td>
                                    </tr>";

                                // Edit Modal for each category
                                echo "
                                <div class='modal fade' id='editModal{$row['category_id']}' tabindex='-1' aria-labelledby='editModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='editModalLabel'>Edit Category</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <form method='POST' action='manage_categories.php'>
                                                    <input type='hidden' name='category_id' value='{$row['category_id']}'>
                                                    <div class='mb-3'>
                                                        <label for='name' class='form-label'>Category Name</label>
                                                        <input type='text' class='form-control' name='name' value='{$row['name']}' required>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label for='description' class='form-label'>Description</label>
                                                        <textarea class='form-control' name='description'>{$row['description']}</textarea>
                                                    </div>
                                                    <button type='submit' class='btn btn-primary' name='edit_category'>Update Category</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No categories found.</td></tr>";
                        }
                        ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>"><?php echo $i; ?></a>
                </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
    </div>
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>