<?php
// Include database connection file
include('../../includes/config/db_config.php');

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Manage Inventory</h1>

        <!-- Edit Inventory Modal -->
        <div class="modal fade <?php echo isset($inventory_id) ? 'show d-block' : ''; ?>" id="editInventoryModal"
            tabindex="-1" aria-labelledby="editInventoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editInventoryModalLabel">Edit Inventory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <input type="hidden" name="inventory_id" value="<?php echo $inventory['inventory_id']; ?>">

                            <div class="mb-3">
                                <label for="product_id" class="form-label">Product Name</label>
                                <select class="form-select" name="product_id" id="product_id" required>
                                    <option value="" disabled>Select a product</option>
                                    <?php while ($product = $result_products->fetch_assoc()) { ?>
                                    <option value="<?php echo $product['product_id']; ?>"
                                        <?php echo $inventory['product_id'] == $product['product_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" name="stock" id="stock"
                                    value="<?php echo htmlspecialchars($inventory['stock']); ?>" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" name="update_inventory" class="btn btn-primary">Update
                                    Inventory</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (isset($inventory_id)) { ?>
    <script>
    // Automatically show the modal when editing
    const editModal = new bootstrap.Modal(document.getElementById('editInventoryModal'));
    editModal.show();
    </script>
    <?php } ?>
</body>

</html>