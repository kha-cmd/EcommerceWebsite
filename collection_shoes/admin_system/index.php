<?php
// Include the database connection file
include '../includes/config/db_config.php';

// Handle form submission for admin registration
if (isset($_POST['register_admin'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role_id = $_POST['role_id'];
    $status = $_POST['status'];

    // Prepare the query to insert data into the admin table
    $stmt = $conn->prepare("INSERT INTO admin (username, email, password_hash, role_id, status) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $username, $email, $password, $role_id, $status);

    if ($stmt->execute()) {
        echo "<div class='success-message'>Admin registered successfully!</div>";
    } else {
        echo "<div class='error-message'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Handle form submission for role creation
if (isset($_POST['create_role'])) {
    $role_name = $_POST['role_name'];
    $description = $_POST['description'];

    // Prepare the query to insert a new role into the roles table
    $stmt = $conn->prepare("INSERT INTO roles (role_name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $role_name, $description);

    if ($stmt->execute()) {
        echo "<div class='success-message'>Role created successfully!</div>";
    } else {
        echo "<div class='error-message'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Handle form submission for permission creation
if (isset($_POST['create_permission'])) {
    $permission_name = $_POST['permission_name'];
    $description = $_POST['description'];

    // Prepare the query to insert a new permission into the permissions table
    $stmt = $conn->prepare("INSERT INTO permissions (permission_name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $permission_name, $description);

    if ($stmt->execute()) {
        echo "<div class='success-message'>Permission created successfully!</div>";
    } else {
        echo "<div class='error-message'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Handle form submission for assigning permissions to a role
if (isset($_POST['assign_permission'])) {
    $role_id = $_POST['role_id'];
    $permission_ids = $_POST['permission_ids'];

    // Insert permissions for the selected role into the role_permissions table
    foreach ($permission_ids as $permission_id) {
        $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $role_id, $permission_id);
        
        if (!$stmt->execute()) {
            echo "<div class='error-message'>Error assigning permission: " . $stmt->error . "</div>";
        }
    }

    echo "<div class='success-message'>Permissions assigned to the role successfully!</div>";
    $stmt->close();
}

// Fetch available roles for registration
$role_query = "SELECT * FROM roles";
$role_result = $conn->query($role_query);

// Fetch available permissions for registration
$permission_query = "SELECT * FROM permissions";
$permission_result = $conn->query($permission_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Link to your CSS file -->
    <style>
    /* Centered Admin Panel */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    /* Title Styles */
    .header-container h2 {
        margin: 0;
    }

    /* Rectangular Button Styles */
    .login-button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        /* Compact rectangular shape */
        font-size: 16px;
        /* Medium font size */
        border: none;
        cursor: pointer;
        border-radius: 5px;
        /* Slightly rounded corners */
        transition: background-color 0.3s ease;
        width: auto;
        /* Ensure the width fits content */
    }

    .login-button:hover {
        background-color: #45a049;
    }

    /* Admin Form Container */
    section {
        margin: 20px 0;
    }

    /* Form Styles */
    form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    input,
    select,
    textarea {
        padding: 10px;
        margin: 5px 0;
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #45a049;
    }

    /* Success/Error Message Styles */
    .success-message,
    .error-message {
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
    }

    .success-message {
        background-color: #4CAF50;
        color: white;
    }

    .error-message {
        background-color: #f44336;
        color: white;
    }

    /* Login Form Styles */
    .login-form {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .login-form form {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    .login-form input {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
    }

    .login-form button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
    }

    .login-form button:hover {
        background-color: #45a049;
    }

    /* Close Button */
    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 30px;
        cursor: pointer;
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section with Title and Login Button -->
        <div class="header-container">
            <h2>Admin Panel</h2>
            <a href="login.php">
                <button class="login-button">Sign In</button>
            </a>
        </div>

        <!-- Admin Registration Form -->
        <section>
            <h3>Admin Registration</h3>
            <form action="index.php" method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter your username">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">

                <label for="role_id">Role</label>
                <select id="role_id" name="role_id" required>
                    <option value="">Select Role</option>
                    <?php
                    while ($role = $role_result->fetch_assoc()) {
                        echo "<option value='" . $role['role_id'] . "'>" . $role['role_name'] . "</option>";
                    }
                    ?>
                </select>

                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>

                <button type="submit" name="register_admin">Register Admin</button>
            </form>
        </section>

        <!-- Role Creation Form -->
        <section>
            <h3>Create Role</h3>
            <form action="index.php" method="POST">
                <label for="role_name">Role Name</label>
                <input type="text" id="role_name" name="role_name" required placeholder="Enter role name">

                <label for="description">Description</label>
                <textarea id="description" name="description" required placeholder="Enter role description"></textarea>

                <button type="submit" name="create_role">Create Role</button>
            </form>
        </section>

        <!-- Permission Creation Form -->
        <section>
            <h3>Create Permission</h3>
            <form action="index.php" method="POST">
                <label for="permission_name">Permission Name</label>
                <input type="text" id="permission_name" name="permission_name" required
                    placeholder="Enter permission name">

                <label for="description">Description</label>
                <textarea id="description" name="description" required
                    placeholder="Enter permission description"></textarea>

                <button type="submit" name="create_permission">Create Permission</button>
            </form>
        </section>

        <!-- Assign Permissions to Role -->
        <section>
            <h3>Assign Permissions to Role</h3>
            <form action="index.php" method="POST">
                <label for="role_id">Role</label>
                <select id="role_id" name="role_id" required>
                    <option value="">Select Role</option>
                    <?php
                    // Fetch available roles
                    $role_query = "SELECT * FROM roles";
                    $role_result = $conn->query($role_query);

                    // Check for errors in query execution
                    if ($role_result === false) {
                        die("Error executing query: " . $conn->error);
                    }

                    // Check if roles exist and populate the select dropdown
                    if ($role_result->num_rows > 0) {
                        while ($role = $role_result->fetch_assoc()) {
                            echo "<option value='" . $role['role_id'] . "'>" . $role['role_name'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No roles available</option>";
                    }
                    ?>
                </select>

                <label for="permission_ids">Permissions</label>
                <select id="permission_ids" name="permission_ids[]" multiple required>
                    <?php
                    // Fetch available permissions
                    $permission_query = "SELECT * FROM permissions";
                    $permission_result = $conn->query($permission_query);

                    // Check for errors in query execution
                    if ($permission_result === false) {
                        die("Error executing query: " . $conn->error);
                    }

                    // Check if permissions exist and populate the select dropdown
                    while ($permission = $permission_result->fetch_assoc()) {
                        echo "<option value='" . $permission['permission_id'] . "'>" . $permission['permission_name'] . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="assign_permission">Assign Permissions</button>
            </form>
        </section>
    </div>
</body>

</html>